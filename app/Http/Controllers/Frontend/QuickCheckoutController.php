<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Support\Facades\URL;
use App\Services\QuickCheckout;
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

    public function store(Request $request, QuickCheckout $checkout)
    {
        $phone = preg_replace('/[\s()-]+/', '', (string) $request->input('phone'));
        $request->merge(['phone' => preg_replace('/^(?:\+88|88)(01)/', '$1', $phone)]);
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => ['required', 'regex:/^01[3-9][0-9]{8}$/'],
            'address' => 'required|string|max:255',
            'email' => 'nullable|email|max:191',
            'comment' => 'nullable|string|max:1000',
            'shipping_id' => 'required|integer',
            'token' => 'required|uuid',
        ]);
        if (!hash_equals((string) session('quick_checkout.token'), $data['token'])) {
            throw ValidationException::withMessages(['cart' => 'Please reopen checkout and try again.']);
        }
        $result = $checkout->place($data, $data['token'], $request->user());
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
