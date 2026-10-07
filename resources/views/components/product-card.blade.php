@props(['product'])
@php
    $bangla = session('language') === 'bangla';
    $name = $bangla ? ($product->name_bn ?: $product->name_en) : $product->name_en;
    $price = $product->storefrontPrice();
    $regular = (float) $product->regular_price;
    $discountPercent = $regular > $price && $regular > 0 ? round(($regular - $price) / $regular * 100) : 0;

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
            <img src="{{ asset($thumb) }}" alt="{{ $name }}" loading="lazy" decoding="async" width="280" height="420" onerror="this.onerror=null;this.src='{{ asset('upload/no_image.jpg') }}'">
        </a>
        @if($discountPercent > 0)
            <span class="rf-product__badge">Sale</span>
        @endif
        <button type="button" class="rf-product__preview" data-product-preview="{{ $product->id }}" aria-label="Quick view: {{ $name }}">
            <i class="fa-regular fa-eye" aria-hidden="true"></i>
        </button>
    </div>
    <div class="rf-product__body">
        <h3 title="{{ $name }}"><a href="{{ route('product.details', $product->slug) }}">{{ $name }}</a></h3>
        <div class="rf-product__price">
            @if($discountPercent > 0)
                <del>Tk {{ number_format($regular, 2) }}</del>
            @endif
            <strong>Tk {{ number_format($price, 2) }}</strong>
            @if($discountPercent > 0)
                <span class="rf-product__saving">Save {{ $discountPercent }}%</span>
            @endif
        </div>
    </div>
</article>
