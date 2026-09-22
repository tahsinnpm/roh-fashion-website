@props(['product'])
@php
    $bangla = session('language') === 'bangla';
    $name = $bangla ? ($product->name_bn ?: $product->name_en) : $product->name_en;
    $price = $product->storefrontPrice();
    $regular = (float) $product->regular_price;
    $variant = (bool) $product->is_varient;
    $available = $variant ? $product->stocks->where('qty', '>=', max(1, $product->minimum_buy_qty))->isNotEmpty() : $product->stock_qty >= max(1, $product->minimum_buy_qty);

    $thumb = $product->product_thumbnail;
    if ($thumb) {
        $thumb = preg_replace('/^(\/?public\/)+/', '', $thumb);
        $thumb = ltrim($thumb, '/');
    }
    if (!$thumb || !file_exists(public_path($thumb))) {
        $thumb = 'upload/product-default.jpg';
    }
@endphp
<article class="rf-product">
    <div class="rf-product__visual">
        <a href="{{ route('product.details', $product->slug) }}" class="rf-product__image">
            <img src="{{ asset($thumb) }}" alt="{{ $name }}" loading="lazy" width="280" height="340" onerror="this.onerror=null;this.src='{{ asset('upload/no_image.jpg') }}'">
        </a>
        @if($regular > $price && $regular > 0)
            <span class="rf-product__badge">−{{ round(($regular - $price) / $regular * 100) }}%</span>
        @endif
        <div class="rf-product__tools">
            <button type="button" class="rf-product__preview" data-product-preview="{{ $product->id }}" aria-label="{{ $bangla ? 'পণ্যের তথ্য দেখুন' : 'Quick view' }}: {{ $name }}" title="{{ $bangla ? 'কুইক ভিউ' : 'Quick view' }}"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
            <button type="button" class="rf-product__wishlist" data-product-wishlist="{{ $product->id }}" aria-label="{{ $bangla ? 'পছন্দের তালিকায় যুক্ত করুন' : 'Add to wishlist' }}" title="{{ $bangla ? 'উইশলিস্ট' : 'Wishlist' }}"><i class="fa-regular fa-heart" aria-hidden="true"></i></button>
        </div>
        <button type="button" class="rf-product__add" data-product-add="{{ $product->id }}" data-variant="{{ $variant ? '1' : '0' }}" data-quantity="{{ max(1, $product->minimum_buy_qty) }}" @if(!$available) disabled @endif title="{{ $bangla ? 'কার্টে যোগ করুন' : 'Add to cart' }}">
            <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
            <span class="rf-btn-text">{{ !$available ? ($bangla ? 'স্টক নেই' : 'Out of stock') : ($bangla ? 'Cart+' : 'Cart+') }}</span>
        </button>
    </div>
    <div class="rf-product__body">
        <h3 title="{{ $name }}"><a href="{{ route('product.details', $product->slug) }}">{{ $name }}</a></h3>
        <div class="rf-product__footer">
            <div class="rf-product__price">
                <strong>৳{{ number_format($price, 2) }}</strong>
                @if($regular > $price)
                    <del>৳{{ number_format($regular, 2) }}</del>
                @endif
            </div>
            <button type="button" class="rf-product__buy" data-product-buy="{{ $product->id }}" data-variant="{{ $variant ? '1' : '0' }}" data-quantity="{{ max(1, $product->minimum_buy_qty) }}" @if(!$available) disabled @endif title="{{ $bangla ? 'এখনই কিনুন' : 'Buy now' }}">
                <i class="fa-solid fa-bolt" aria-hidden="true"></i>
                <span class="rf-btn-text">{{ $bangla ? 'Buy Now' : 'Buy Now' }}</span>
            </button>
        </div>
    </div>
</article>


