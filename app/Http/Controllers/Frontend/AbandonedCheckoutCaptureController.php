<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AbandonedCheckoutCapture;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AbandonedCheckoutCaptureController extends Controller
{
    public function store(Request $request, AbandonedCheckoutCapture $capture)
    {
        $data = $request->validate([
            'source' => 'required|in:quick_checkout,dynamic_landing',
            'phone' => ['required', 'string', 'max:20', 'regex:/^(?:\+?88)?01[3-9][0-9]{8}$/'],
            'name' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'address' => 'nullable|string|max:1000',
            'product_id' => 'required_if:source,dynamic_landing|nullable|integer|exists:products,id',
            'landing_slug' => 'nullable|string|max:191',
            'quantity' => 'nullable|integer|min:1|max:100',
            'variant' => 'nullable|string|max:100',
        ]);

        if ($data['source'] === 'quick_checkout') {
            $items = Cart::content()->map(fn ($row) => [
                'product_id' => (int) $row->id,
                'name' => (string) $row->name,
                'quantity' => (int) $row->qty,
                'variant' => (string) ($row->options->varient ?? ''),
            ])->values()->all();
        } else {
            $product = Product::findOrFail($data['product_id']);
            $items = [[
                'product_id' => (int) $product->id,
                'name' => (string) ($product->name_en ?: $product->name_bn),
                'quantity' => (int) ($data['quantity'] ?? 1),
                'variant' => (string) ($data['variant'] ?? ''),
            ]];
        }

        try {
            $capture->capture(array_merge($data, ['cart_snapshot' => $items]));
        } catch (\Throwable $exception) {
            Log::warning('Unable to capture abandoned checkout.', ['exception' => $exception->getMessage()]);
            return response()->json(['success' => false], 503);
        }

        return response()->json(['success' => true]);
    }
}
