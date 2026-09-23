<?php

namespace App\Services;

use App\Models\{AccountHead, AccountLedger, Affiliate, Coupon, Order, OrderDetail, Product, ProductStock, Shipping, User};
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Validation\ValidationException;

class QuickCheckout
{
    public function quote($lock = false)
    {
        $cart = Cart::content();
        if ($cart->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your bag is empty. Please add a product first.']);
        }
        $query = Product::whereIn('id', $cart->pluck('id'))->orderBy('id');
        $products = ($lock ? $query->lockForUpdate() : $query)->get()->keyBy('id');
        $stockQuery = ProductStock::whereIn('product_id', $products->keys())->orderBy('id');
        $stocks = ($lock ? $stockQuery->lockForUpdate() : $stockQuery)->get();
        $used = [];
        $items = [];
        foreach ($cart as $row) {
            $product = $products->get($row->id);
            $variant = $product && $product->is_varient
                ? $stocks->where('product_id', $row->id)->firstWhere('varient', $row->options->varient) : null;
            $key = $row->id.':'.($variant ? $variant->id : 'simple');
            $used[$key] = ($used[$key] ?? 0) + (int) $row->qty;
            $available = $product && $product->is_varient ? (int) optional($variant)->qty : (int) optional($product)->stock_qty;
            if (!$product || !$product->status || ($product->is_varient && !$variant)
                || $row->qty < max(1, (int) $product->minimum_buy_qty) || $used[$key] > $available) {
                throw ValidationException::withMessages(['cart' => 'Please update your bag: '.$row->name.' is unavailable in the selected quantity.']);
            }
            $price = round($product->storefrontPrice($variant ? $variant->price : null), 2);
            $items[] = ['product' => $product, 'stock' => $variant, 'row' => $row, 'price' => $price, 'total' => round($price * $row->qty, 2)];
        }
        $subtotal = round(array_sum(array_column($items, 'total')), 2);
        $couponQuery = Coupon::where('id', session('couponId'))->where('status', 1)->where('total_use_limit', '>', 0)->where('expire_date', '>=', now());
        $coupon = ($lock ? $couponQuery->lockForUpdate() : $couponQuery)->first();
        $discount = $coupon ? min($subtotal, max(0, round($coupon->discount_type == 0 ? $subtotal * $coupon->discount / 100 : $coupon->discount, 2))) : 0;
        return compact('items', 'subtotal', 'coupon', 'discount');
    }

    public function place(array $data, string $token, $authenticatedUser = null)
    {
        return DB::transaction(function () use ($data, $token, $authenticatedUser) {
            // Serialize order ledger writes and repeated submissions before checking stock.
            AccountHead::whereKey(2)->lockForUpdate()->firstOrFail();
            $invoice = 'RF-'.strtoupper(substr(hash('sha256', $token), 0, 24));
            $existingOrder = Order::where('invoice_no', $invoice)->first();
            if ($existingOrder) return ['order' => $existingOrder, 'account_created' => false];

            $quote = $this->quote(true);
            $shipping = Shipping::where('status', 1)->lockForUpdate()->find($data['shipping_id']);
            if (!$shipping) throw ValidationException::withMessages(['shipping_id' => 'Select an available delivery area.']);
            $created = false;
            $user = $authenticatedUser && (int) $authenticatedUser->role === 3 ? $authenticatedUser : null;
            if (!$user) {
                $user = User::whereIn('phone', [$data['phone'], '88'.$data['phone'], '+88'.$data['phone']])->lockForUpdate()->first();
                if ($user && ((int) $user->role !== 3 || !$user->status)) {
                    throw ValidationException::withMessages(['phone' => 'Please use a customer phone number.']);
                }
                if (!$user) {
                    $user = User::create([
                        'name' => $data['name'], 'phone' => $data['phone'], 'address' => $data['address'],
                        'role' => 3, 'password' => Hash::make('12345678'),
                    ]);
                    $created = true;
                }
            }
            $charge = max(0, (float) $shipping->shipping_charge);
            $order = Order::create([
                'user_id' => $user->id, 'name' => $data['name'], 'phone' => $data['phone'], 'address' => $data['address'],
                'affiliate_id' => session('affiliate_ref') ? Affiliate::where('referral_code', session('affiliate_ref'))->value('id') : null,
                'invoice_no' => $invoice, 'sub_total' => $quote['subtotal'], 'discount' => $quote['discount'],
                'coupon' => $quote['coupon'] ? $quote['discount'] : null,
                'grand_total' => round($quote['subtotal'] - $quote['discount'] + $charge, 2),
                'shipping_charge' => $charge, 'shipping_name' => $shipping->name, 'shipping_type' => $shipping->type,
                'payment_method' => 'cod', 'payment_status' => 0, 'delivery_status' => 'pending',
                'type' => $authenticatedUser ? 1 : 2,
            ]);
            foreach ($quote['items'] as $item) {
                $product = $item['product'];
                $row = $item['row'];
                $variations = [];
                foreach (($row->options->attribute_names ?: []) as $index => $name) {
                    $variations[] = ['attribute_name' => $name, 'attribute_value' => $row->options->attribute_values[$index] ?? ''];
                }
                OrderDetail::create([
                    'order_id' => $order->id, 'invoice_no' => $invoice, 'product_id' => $product->id,
                    'vendor_id' => $product->vendor_id ?: 0, 'product_name' => $product->name_en,
                    'is_varient' => (int) $product->is_varient, 'variation' => $product->is_varient ? json_encode($variations, JSON_UNESCAPED_UNICODE) : null,
                    'qty' => $row->qty, 'price' => $item['price'], 'tax' => 0,
                ]);
                if ($item['stock']) $item['stock']->decrement('qty', $row->qty);
                $product->stock_qty = max(0, $product->stock_qty - $row->qty);
                $product->save();
            }
            if ($quote['coupon']) $quote['coupon']->decrement('total_use_limit');
            $balance = (float) AccountLedger::orderByDesc('id')->value('balance');
            AccountLedger::create([
                'account_head_id' => 2, 'particulars' => 'Order ID: '.$order->id, 'credit' => $order->grand_total,
                'order_id' => $order->id, 'balance' => $balance + $order->grand_total, 'type' => 2,
            ]);
            return ['order' => $order, 'account_created' => $created];
        }, 3);
    }
}
