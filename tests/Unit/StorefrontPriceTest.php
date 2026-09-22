<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class StorefrontPriceTest extends TestCase
{
    public function test_regular_fixed_and_percentage_prices_support_variants()
    {
        $product = (new Product())->setRawAttributes(['regular_price' => 1000, 'discount_type' => 0, 'discount_price' => 0]);
        $this->assertEquals(1000, $product->storefrontPrice());
        $product->discount_type = 1;
        $product->discount_price = 150;
        $this->assertEquals(850, $product->storefrontPrice());
        $this->assertEquals(1050, $product->storefrontPrice(1200));
        $product->discount_type = 2;
        $product->discount_price = 20;
        $this->assertEquals(800, $product->storefrontPrice());
        $this->assertEquals(960, $product->storefrontPrice(1200));
    }

    public function test_discounts_never_produce_negative_prices()
    {
        $product = (new Product())->setRawAttributes(['regular_price' => 100, 'discount_type' => 1, 'discount_price' => 200]);
        $this->assertEquals(0, $product->storefrontPrice());
        $product->discount_type = 2;
        $this->assertEquals(0, $product->storefrontPrice());
    }
}
