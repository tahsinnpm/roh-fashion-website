<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use RuntimeException;

class OrderInventory
{
    public function restore(Order $order): void
    {
        $this->change($order, true);
    }

    public function deduct(Order $order): void
    {
        $this->change($order, false);
    }

    private function change(Order $order, bool $restore): void
    {
        foreach ($order->order_details()->get() as $detail) {
            $quantity = max(0, (int) $detail->qty);
            if ($quantity === 0) continue;

            $product = Product::whereKey($detail->product_id)->lockForUpdate()->first();
            if (!$product) {
                if ($restore) {
                    continue;
                }
                throw new RuntimeException("Product for order item {$detail->product_name} no longer exists.");
            }
            if (!$restore && (int) $product->stock_qty < $quantity) {
                // If product stock is lower than required, clamp to 0 instead of hard crash
                $product->stock_qty = max(0, (int) $product->stock_qty);
            }

            if ((int) $detail->is_varient === 1) {
                $variants = json_decode($detail->variation, true) ?: [];
                $variant = implode('-', array_map(static fn ($item) => trim((string) ($item['attribute_value'] ?? '')), $variants));
                if ($variant === '') {
                    $product->stock_qty = max(0, (int) $product->stock_qty + ($restore ? $quantity : -$quantity));
                    $product->save();
                    continue;
                }

                $stocks = ProductStock::where('product_id', $product->id)
                    ->lockForUpdate()->get();
                $normalizeVariant = static function ($value): string {
                    return preg_replace('/\s+/u', ' ', trim((string) $value));
                };
                $variantParts = array_map($normalizeVariant, explode('-', $variant));
                sort($variantParts);
                $sortedKey = implode('|||', $variantParts);

                $stock = $stocks->first(function ($candidate) use ($variant, $normalizeVariant, $sortedKey) {
                    if ($normalizeVariant($candidate->varient) === $normalizeVariant($variant)) {
                        return true;
                    }
                    $candidateParts = array_map($normalizeVariant, explode('-', (string) $candidate->varient));
                    sort($candidateParts);
                    return implode('|||', $candidateParts) === $sortedKey;
                });
                if (!$stock) {
                    // If variant stock entry is missing, create or restore safely
                    $stock = new ProductStock([
                        'product_id' => $product->id,
                        'varient' => $variant,
                        'qty' => 0,
                        'price' => $product->regular_price ?? 0,
                    ]);
                }

                $newStockQty = (int) $stock->qty + ($restore ? $quantity : -$quantity);
                $stock->qty = max(0, $newStockQty);
                $stock->save();
            }

            $product->stock_qty = max(0, (int) $product->stock_qty + ($restore ? $quantity : -$quantity));
            $product->save();
        }
    }
}
