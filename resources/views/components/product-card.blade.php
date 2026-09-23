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
            <img src="{{ asset($thumb) }}" alt="{{ $name }}" loading="lazy" decoding="async" width="280" height="280" onerror="this.onerror=null;this.src='{{ asset('upload/no_image.jpg') }}'">
        </a>
        @if(!$available)
            <span class="rf-product__badge">{{ $bangla ? 'স্টকে নেই' : 'Out of stock' }}</span>
        @elseif($regular > $price && $regular > 0)
            <span class="rf-product__badge">{{ round(($regular - $price) / $regular * 100) }}% {{ $bangla ? 'ছাড়' : 'OFF' }}</span>
        @endif
        <button type="button" class="rf-product__preview" data-product-preview="{{ $product->id }}" aria-label="{{ $bangla ? 'বিস্তারিত দেখুন' : 'Quick view' }}: {{ $name }}">
            <i class="fa-regular fa-eye" aria-hidden="true"></i>
        </button>
    </div>
    <div class="rf-product__body">
        <h3 title="{{ $name }}"><a href="{{ route('product.details', $product->slug) }}">{{ $name }}</a></h3>
        <div class="rf-product__price">
            <strong>৳{{ number_format($price, $price == floor($price) ? 0 : 2) }}</strong>
            @if($regular > $price)
                <del>৳{{ number_format($regular, $regular == floor($regular) ? 0 : 2) }}</del>
            @endif
        </div>
        <div class="rf-product__actions">
            <button type="button" class="rf-product__buy" data-product-buy="{{ $product->id }}" data-variant="{{ $variant ? '1' : '0' }}" data-quantity="{{ max(1, $product->minimum_buy_qty) }}" @if(!$available) disabled @endif>
                {{ $bangla ? 'এখনই কিনুন' : 'Buy now' }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </button>
            <button type="button" class="rf-product__add" aria-label="{{ $bangla ? 'কার্টে যোগ করুন' : 'Add to cart' }}: {{ $name }}" title="{{ $bangla ? 'কার্টে যোগ করুন' : 'Add to cart' }}" data-product-add="{{ $product->id }}" data-variant="{{ $variant ? '1' : '0' }}" data-quantity="{{ max(1, $product->minimum_buy_qty) }}" @if(!$available) disabled @endif>
                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</article>
