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
                return ['name' => $item['product']->name_en, 'quantity' => $item['row']->qty, 'variant' => $item['row']->options->varient ?: '', 'total' => $item['total']];
            }),
            'areas' => Shipping::where('status', 1)->get(['id', 'name', 'type', 'shipping_charge']),
            'customer' => $request->user() && (int) $request->user()->role === 3 ? $request->user()->only('name', 'phone', 'address') : null,
        ]);
    }

    public function store(Request $request, QuickCheckout $checkout)
    {
        $phone = preg_replace('/[\s()-]+/', '', (string) $request->input('phone'));
        $request->merge(['phone' => preg_replace('/^(?:\+88|88)(01)/', '$1', $phone)]);
        $data = $request->validate([
            'name' => 'required|string|max:150', 'phone' => ['required', 'regex:/^01[3-9][0-9]{8}$/'],
            'address' => 'required|string|max:255', 'shipping_id' => 'required|integer', 'token' => 'required|uuid',
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

    public function invoice(Order $order)
    {
        $order->load('order_details');
        return response()->view('FrontEnd.order.quick-invoice', compact('order'))
            ->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
