<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Support\Facades\URL;
use App\Services\QuickCheckout;
use App\Services\AbandonedCheckoutCapture;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QuickCheckoutController extends Controller
{
    public function show(Request $request, QuickCheckout $checkout)
    {
        $quote = $checkout->quote();
        if (!$request->session()->has('quick_checkout.token') || $request->session()->has('quick_checkout.completed')) {
            $request->session()->put('quick_checkout.token', (string) Str::uuid());
            $request->session()->forget('quick_checkout.completed');
        }
        return response()->json([
            'token' => session('quick_checkout.token'), 'subtotal' => $quote['subtotal'], 'discount' => $quote['discount'],
            'coupon' => $quote['coupon'] ? ['code' => $quote['coupon']->coupon_code] : null,
            'items' => collect($quote['items'])->map(function ($item) {
                return [
                    'rowId' => $item['row']->rowId,
                    'id' => $item['product']->id,
                    'name' => $item['product']->name_en,
                    'quantity' => (int) $item['row']->qty,
                    'price' => (float) $item['price'],
                    'image' => asset($item['product']->product_thumbnail ?: 'upload/product-default.jpg'),
                    'variant' => $item['row']->options->varient ?: '',
                    'total' => (float) $item['total'],
                ];
            }),
            'areas' => Shipping::where('status', 1)->get(['id', 'name', 'type', 'shipping_charge']),
            'customer' => $request->user() && (int) $request->user()->role === 3 ? $request->user()->only('name', 'phone', 'address', 'email') : null,
        ]);
    }

    public function applyCoupon(Request $request, QuickCheckout $checkout)
    {
        if ($request->boolean('remove')) {
            $request->session()->forget(['couponCode', 'couponId', 'discountType', 'amount']);
            return response()->json(['success' => true, 'message' => 'Coupon removed.']);
        }

        $data = $request->validate(['coupon_code' => 'required|string|max:50']);
        $quote = $checkout->quote();
        $code = strtoupper(trim($data['coupon_code']));
        $coupon = \App\Models\Coupon::whereRaw('UPPER(coupon_code) = ?', [$code])
            ->where('status', 1)
            ->where('total_use_limit', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expire_date')->orWhereDate('expire_date', '>=', today());
            })
            ->first();

        if (!$coupon || $quote['subtotal'] <= 0) {
            return response()->json(['message' => 'This coupon is invalid, expired, or unavailable.'], 422);
        }

        $discount = $coupon->discount_type == 0
            ? $quote['subtotal'] * ((float) $coupon->discount / 100)
            : (float) $coupon->discount;
        $discount = min($quote['subtotal'], max(0, round($discount, 2)));

        $request->session()->put([
            'couponCode' => $coupon->coupon_code,
            'couponId' => $coupon->id,
            'discountType' => $coupon->discount_type,
            'amount' => $discount,
        ]);

        return response()->json([
            'success' => true,
            'code' => $coupon->coupon_code,
            'discount' => $discount,
            'message' => 'Coupon applied successfully.',
        ]);
    }

    public function store(Request $request, QuickCheckout $checkout, AbandonedCheckoutCapture $abandonedCheckoutCapture)
    {
        $phone = preg_replace('/[\s()-]+/', '', (string) $request->input('phone'));
        $request->merge(['phone' => preg_replace('/^(?:\+88|88)(01)/', '$1', $phone)]);
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => ['required', 'regex:/^01[3-9][0-9]{8}$/'],
            'address' => ['required', 'string', 'max:255', 'regex:/\\S+(?:\\s+\\S+){1,}/u'],
            'email' => 'nullable|email|max:191',
            'comment' => 'nullable|string|max:1000',
            'shipping_id' => 'required|integer',
            'token' => 'required|uuid',
        ], [
            'address.required' => 'Please enter your full address, including your house or road, area, thana, and district.',
            'address.regex' => 'Please enter at least two parts of your address, separated by a space.',
        ]);
        if (!hash_equals((string) session('quick_checkout.token'), $data['token'])) {
            throw ValidationException::withMessages(['cart' => 'Please reopen checkout and try again.']);
        }
        $result = $checkout->place($data, $data['token'], $request->user());
        try {
            $abandonedCheckoutCapture->markConverted($data['phone'], $result['order']->id);
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::warning('Unable to mark checkout lead as converted.', ['exception' => $exception->getMessage()]);
        }
        Cart::destroy();
        $request->session()->forget(['couponCode', 'couponId', 'discountType', 'amount', 'affiliate_ref']);
        $request->session()->put('quick_checkout.completed', true);
        return response()->json([
            'invoice' => $result['order']->invoice_no, 'total' => (float) $result['order']->grand_total,
            'account_created' => $result['account_created'], 'cartQty' => 0,
            'invoice_url' => URL::signedRoute('checkout.invoice', ['order' => $result['order']->id]),
        ]);
    }

    public function updateItem(Request $request, $rowId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $row = Cart::get($rowId);
        if (!$row) {
            return response()->json(['error' => 'Item not found in cart.'], 404);
        }
        $product = \App\Models\Product::findOrFail($row->id);
        if ($product->is_varient) {
            $stock = \App\Models\ProductStock::where('product_id', $product->id)->where('varient', $row->options->varient)->first();
            $available = $stock ? (int) $stock->qty : 0;
        } else {
            $available = (int) $product->stock_qty;
        }
        if ($request->quantity > $available) {
            return response()->json(['error' => 'Not enough stock available.'], 422);
        }
        Cart::update($rowId, (int) $request->quantity);
        return response()->json(['success' => 'Cart updated', 'cartQty' => Cart::count()]);
    }

    public function removeItem($rowId)
    {
        Cart::remove($rowId);
        return response()->json(['success' => 'Item removed', 'cartQty' => Cart::count()]);
    }

    public function invoice(Order $order)
    {
        $order->load('order_details');
        return response()->view('FrontEnd.order.quick-invoice', compact('order'))
            ->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
