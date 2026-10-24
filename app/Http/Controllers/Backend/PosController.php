<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use App\Models\Shipping;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\AccountHead;
use App\Models\AccountLedger;
use App\Models\Order;
use App\Models\OrderDetail;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PosController extends Controller
{
    /**
     * Display POS terminal interface.
     */
    public function index()
    {
        $products = Product::where('status', 1)
            ->with(['stocks' => function($q) {
                $q->where('qty', '>', 0);
            }, 'category'])
            ->latest()
            ->paginate(36);

        $categories = Category::where('status', 1)->orderBy('name_en')->get();
        $shippings = Shipping::where('status', 1)->orderBy('type')->get();
        $customers = User::whereIn('role', [3, 4])->where('phone', '!=', '00000000000')->latest()->take(30)->get();

        return view('backend.pos.index', compact('products', 'categories', 'shippings', 'customers'));
    }

    /**
     * AJAX Search Products by keyword, barcode, and category.
     */
    public function searchProducts(Request $request)
    {
        $query = Product::where('status', 1)->with('stocks');

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function($q) use ($term) {
                $q->where('name_en', 'like', "%{$term}%")
                  ->orWhere('name_bn', 'like', "%{$term}%")
                  ->orWhere('product_code', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->take(60)->get();

        $data = $products->map(function($product) {
            $finalPrice = $product->storefrontPrice();

            return [
                'id' => $product->id,
                'name' => $product->name_en,
                'name_bn' => $product->name_bn,
                'code' => $product->product_code,
                'thumbnail' => $product->product_thumbnail ? asset($product->product_thumbnail) : asset('upload/no_image.jpg'),
                'regular_price' => (float)$product->regular_price,
                'price' => (float)$finalPrice,
                'has_discount' => $finalPrice < (float) $product->regular_price,
                'discount_price' => (float)$product->discount_price,
                'stock_qty' => (int)$product->stock_qty,
                'is_varient' => (int)$product->is_varient,
                'variants' => $product->stocks->map(function($st) use ($product) {
                    $vPrice = $product->storefrontPrice($st->price > 0 ? $st->price : null);
                    return [
                        'id' => $st->id,
                        'name' => $st->varient,
                        'price' => (float)$vPrice,
                        'regular_price' => (float)($st->price ?: $product->regular_price),
                        'qty' => (int)$st->qty,
                    ];
                })
            ];
        });

        return response()->json($data);
    }

    /**
     * AJAX Search Customer by phone or name.
     */
    public function searchCustomer(Request $request)
    {
        $term = trim($request->get('term', ''));
        if (strlen($term) < 1) {
            return response()->json([]);
        }

        $customers = User::whereIn('role', [3, 4])
            ->where('phone', '!=', '00000000000')
            ->where(function($q) use ($term) {
                $q->where('phone', 'like', "%{$term}%")
                  ->orWhere('name', 'like', "%{$term}%");
            })
            ->take(10)
            ->get(['id', 'name', 'phone', 'address']);

        return response()->json($customers);
    }

    /**
     * Store POS Order with direct Confirmation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1|max:100',
            'items.*.product_id' => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|integer|min:1',
            'items.*.qty' => 'required|integer|min:1|max:10000',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cod',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'customer_address' => 'nullable|string|max:1000',
            'shipping_id' => 'nullable|integer|exists:shippings,id',
        ]);
        $items = $validated['items'];

        DB::beginTransaction();
        try {
            // 1. Process Customer (Name, Phone, Address)
            $customerName = trim($request->input('customer_name', ''));
            $orderCustomerName = $customerName;
            $customerPhone = trim($request->input('customer_phone', ''));
            $customerAddress = trim($request->input('customer_address', ''));
            $shipping = null;
            if (!empty($validated['shipping_id'])) {
                $shipping = Shipping::where('status', 1)->lockForUpdate()->find($validated['shipping_id']);
                if (!$shipping) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['shipping_id' => 'The selected delivery option is no longer available.']);
                }
            }

            if (empty($customerName)) {
                $customerName = 'Walking Customer';
            }

            if (!empty($customerPhone)) {
                $user = User::where('phone', $customerPhone)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $customerName,
                        'phone' => $customerPhone,
                        'address' => $customerAddress ?: null,
                        'role' => 3,
                        'status' => 1,
                        'password' => Hash::make('12345678'),
                    ]);
                } else {
                    if (!empty($customerAddress) && empty($user->address)) {
                        $user->address = $customerAddress;
                        $user->save();
                    }
                }
            } else {
                $user = User::firstOrCreate(
                    ['phone' => '00000000000'],
                    [
                        'name' => 'Walking Customer',
                        'address' => 'Store Pickup',
                        'role' => 3,
                        'status' => 1,
                        'password' => Hash::make('12345678'),
                    ]
                );
            }

            // 2. Calculate Subtotal & Manual Discount
            $subtotal = 0;
            $lines = [];
            $productQuantities = [];
            $variantQuantities = [];
            foreach ($items as $item) {
                $product = Product::where('status', 1)->lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['qty'];
                $stock = null;
                if (!empty($item['variant_id'])) {
                    if ((int) $product->is_varient !== 1) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['items' => 'Invalid product variant.']);
                    }
                    $stock = ProductStock::where('product_id', $product->id)->lockForUpdate()->findOrFail($item['variant_id']);
                    $requestedVariantQty = ($variantQuantities[$stock->id] ?? 0) + $qty;
                    if ((int) $stock->qty < $requestedVariantQty) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['items' => $product->name_en . ' has insufficient stock.']);
                    }
                    $variantQuantities[$stock->id] = $requestedVariantQty;
                    $basePrice = $stock->price > 0 ? (float) $stock->price : null;
                } else {
                    if ((int) $product->is_varient === 1) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['items' => 'Choose a variant for ' . $product->name_en . '.']);
                    }
                    $requestedQty = ($productQuantities[$product->id] ?? 0) + $qty;
                    if ((int) $product->stock_qty < $requestedQty) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['items' => $product->name_en . ' has insufficient stock.']);
                    }
                    $productQuantities[$product->id] = $requestedQty;
                    $basePrice = null;
                }
                $price = (float) $product->storefrontPrice($basePrice);
                $subtotal += $price * $qty;
                $lines[] = compact('product', 'stock', 'qty', 'price');
            }

            // Manual discount input
            $manualDiscount = min($subtotal, (float) ($validated['discount'] ?? 0));
            $shippingCharge = $shipping ? max(0, (float) $shipping->shipping_charge) : 0;
            $grandTotal = $subtotal - $manualDiscount + $shippingCharge;

            // 3. Generate Unique Invoice Number
            $invoice_no = 'POS-' . date('ymd') . '-' . strtoupper(Str::random(5));

            // 4. Create Confirmed POS Order (Order Confirmed Directly, NOT Pending)
            $order = Order::create([
                'order_type' => 2, // POS
                'type' => 3,       // POS
                'user_id' => $user->id,
                'name' => $orderCustomerName,
                'phone' => $customerPhone,
                'email' => $user->email ?: '',
                'address' => $customerAddress,
                'sub_total' => $subtotal,
                'discount' => $manualDiscount,
                'shipping_name' => $shipping ? $shipping->name : null,
                'shipping_type' => $shipping ? $shipping->type : null,
                'shipping_charge' => $shippingCharge,
                'grand_total' => $grandTotal,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 0,
                'delivery_status' => 'confirmed', // DIRECTLY CONFIRMED
                'confirmed_date' => Carbon::now(),
                'invoice_no' => $invoice_no,
                'created_by' => Auth::guard('admin')->id(),
            ]);

            // 5. Store Order Details & Decrement Stock
            foreach ($lines as $line) {
                $product = $line['product'];
                $qty = $line['qty'];
                $price = $line['price'];
                $productId = $product->id;
                $variation = $line['stock'] ? $line['stock']->varient : null;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'invoice_no' => $invoice_no,
                    'product_id' => $productId,
                    'product_name' => $product->name_en,
                    'is_varient' => $line['stock'] ? 1 : 0,
                    'variation' => $variation,
                    'qty' => $qty,
                    'price' => $price,
                    'delivery_status' => 'confirmed',
                    'payment_status' => 'unpaid',
                    'created_at' => Carbon::now(),
                ]);

                // Stock decrement
                if ($line['stock']) $line['stock']->decrement('qty', $qty);
                $product->decrement('stock_qty', $qty);
            }

            // 6. Account Ledger Entry
            $previousBalance = (float) (AccountLedger::orderByDesc('id')->value('balance') ?? 0);
            AccountLedger::create([
                'account_head_id' => 2, // Sales Revenue
                'particulars' => 'POS Order Invoice: ' . $order->invoice_no,
                'credit' => $order->grand_total,
                'balance' => $previousBalance + $order->grand_total,
                'order_id' => $order->id,
                'type' => 2,
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'POS অর্ডার সফলভাবে কনফার্ম করা হয়েছে!',
                    'order_id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'invoice_url' => route('pos.invoice', $order->id),
                    'invoice_print_url' => route('pos.invoicePrint', $order->id),
                ]);
            }

            Session::flash('success', 'POS অর্ডার কনফার্ম করা হয়েছে!');
            return redirect()->route('pos.invoice', $order->id);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first(),
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to create the POS order. Please try again.',
                ], 500);
            }
            Session::flash('error', 'Unable to create the POS order. Please try again.');
            return redirect()->back();
        }
    }

    /**
     * Thermal Receipt Printable Page (80mm standard).
     */
    public function invoicePrint($id)
    {
        $order = Order::with('order_details.product')->findOrFail($id);
        return view('backend.pos.invoice_print', compact('order'));
    }

    /**
     * Invoice View Modal/Page.
     */
    public function invoice($id)
    {
        $order = Order::with('order_details.product')->findOrFail($id);
        return view('backend.pos.invoice', compact('order'));
    }

    public function customerInsert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => ['required', 'unique:users,phone'],
            'address' => 'nullable',
        ]);

        $customer = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?? null,
            'address' => $request->address ?? 'Store Pickup',
            'role' => 3,
            'status' => 1,
            'password' => Hash::make('12345678'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'customer' => $customer
            ]);
        }

        Session::flash('success', 'Customer Added Successfully');
        return back();
    }
}
