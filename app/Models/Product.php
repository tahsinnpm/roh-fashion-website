<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * Get the minimum variant price for products with variants.
     */
    public function minVariantPrice()
    {
        if ((int)$this->is_varient === 1) {
            $stocks = $this->relationLoaded('stocks') ? $this->stocks : $this->stocks()->get();
            if ($stocks && $stocks->count() > 0) {
                $inStockMin = $stocks->where('qty', '>', 0)->where('price', '>', 0)->min('price');
                if ($inStockMin !== null && (float)$inStockMin > 0) {
                    return (float)$inStockMin;
                }
                $anyMin = $stocks->where('price', '>', 0)->min('price');
                if ($anyMin !== null && (float)$anyMin > 0) {
                    return (float)$anyMin;
                }
            }
        }
        return null;
    }

    /**
     * Get storefront selling price.
     * If $base is provided (e.g. variant stock price), that price is used.
     * If product has variants, returns the minimum variant price.
     */
    public function storefrontPrice($base = null)
    {
        if ($base !== null && (float)$base > 0) {
            return (float)$base;
        }

        if ((int)$this->is_varient === 1) {
            $minVariant = $this->minVariantPrice();
            if ($minVariant !== null && $minVariant > 0) {
                return (float)$minVariant;
            }
        }

        $regular = (float)$this->regular_price;
        $discount = (float)$this->discount_price;

        if ($discount > 0) {
            if ((int)$this->discount_type === 2) {
                return max(0, round($regular - ($regular * $discount / 100)));
            }
            if ((int)$this->discount_type === 1) {
                if ($discount < $regular) {
                    return $discount;
                }
            }
        }

        return $regular;
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }
    public function sub_category(){
        return $this->belongsTo(Category::class, 'sub_category_id');
    }
    public function brand(){
    	return $this->belongsTo(Brand::class,'brand_id','id');
    }
    public function unit(){
    	return $this->belongsTo(Unit::class,'unit_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }
    public function multi_imgs()
    {
        return $this->hasMany(MultiImg::class);
    }

    public function group_product()
    {
        return $this->hasMany(GroupProduct::class, 'id', 'product_id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function dynamic_sections()
    {
        return $this->belongsToMany(DynamicSection::class, 'dynamic_section_products', 'product_id', 'dynamic_section_id')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

}
