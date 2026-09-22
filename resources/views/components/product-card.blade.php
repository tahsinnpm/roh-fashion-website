@props(['product'])
@php
    $bangla = session('language') === 'bangla';
    $name = $bangla ? ($product->name_bn ?: $product->name_en) : $product->name_en;
    $price = $product->storefrontPrice();
    $regular = (float) $product->regular_price;
    $variant = (bool) $product->is_varient;
    $available = $variant ? $product->stocks->where('qty', '>=', max(1, $product->minimum_buy_qty))->isNotEmpty() : $product->stock_qty >= max(1, $product->minimum_buy_qty);
@endphp
<article class="rf-product">
    <div class="rf-product__visual">
        <a href="{{ route('product.details', $product->slug) }}" class="rf-product__image">
            <img src="{{ asset($product->product_thumbnail) }}" alt="{{ $name }}" loading="lazy" width="480" height="600">
        </a>
        @if($regular > $price && $regular > 0)
            <span class="rf-product__badge">−{{ round(($regular - $price) / $regular * 100) }}%</span>
        @endif
        <button type="button" class="rf-product__preview" data-product-preview="{{ $product->id }}" aria-label="{{ $bangla ? 'পণ্যের তথ্য দেখুন' : 'Quick view' }}: {{ $name }}"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
    </div>
    <div class="rf-product__body">
        <h3><a href="{{ route('product.details', $product->slug) }}">{{ $name }}</a></h3>
        <div class="rf-product__price"><strong>৳{{ number_format($price, 0) }}</strong>@if($regular > $price)<del>৳{{ number_format($regular, 0) }}</del>@endif</div>
        <div class="rf-product__actions">
        <button type="button" class="rf-product__add" data-product-add="{{ $product->id }}" data-variant="{{ $variant ? '1' : '0' }}" data-quantity="{{ max(1, $product->minimum_buy_qty) }}" @if(!$available) disabled @endif>
            <span>{{ !$available ? ($bangla ? 'স্টক শেষ' : 'Out of stock') : ($bangla ? 'কার্টে যোগ করুন' : 'Add to cart') }}</span><i class="fa-solid fa-plus" aria-hidden="true"></i>
        </button>
        <button type="button" class="rf-product__buy" data-product-buy="{{ $product->id }}" data-variant="{{ $variant ? '1' : '0' }}" data-quantity="{{ max(1, $product->minimum_buy_qty) }}" @if(!$available) disabled @endif>{{ $bangla ? 'এখনই কিনুন' : 'Buy now' }}</button>
        </div>
    </div>
</article>
