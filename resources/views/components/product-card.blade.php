@props(['product'])
@php
    $bangla = session('language') === 'bangla';
    $name = $bangla ? ($product->name_bn ?: $product->name_en) : $product->name_en;
    $price = $product->storefrontPrice();
    $regular = (float) $product->regular_price;
    $discountPercent = $regular > $price && $regular > 0 ? round(($regular - $price) / $regular * 100) : 0;
    $hasVariants = (int) $product->is_varient === 1;
    $isAvailable = $hasVariants
        ? $product->stocks->contains(fn($stock) => (int) $stock->qty > 0)
        : (int) $product->stock_qty > 0;

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
            <span class="rf-product__badge">
                <span class="rf-product__badge-sale">Sale</span>
                <span class="rf-product__badge-saving">Save {{ $discountPercent }}%</span>
            </span>
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
        <div class="rf-product__actions">
            <button type="button" class="rf-product__buy" data-product-buy="{{ $product->id }}"
                data-variant="{{ $hasVariants ? '1' : '0' }}" data-quantity="1" {{ $isAvailable ? '' : 'disabled' }}>
                {{ $bangla ? 'এখনই কিনুন' : 'Order Now' }}
            </button>
            <button type="button" class="rf-product__add" data-product-add="{{ $product->id }}"
                data-variant="{{ $hasVariants ? '1' : '0' }}" data-quantity="1"
                aria-label="{{ $bangla ? 'কার্টে যোগ করুন' : 'Add to Cart' }}" title="{{ $bangla ? 'কার্টে যোগ করুন' : 'Add to Cart' }}"
                {{ $isAvailable ? '' : 'disabled' }}>
                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                <span class="rf-product__add-label">{{ $bangla ? 'কার্টে যোগ করুন' : 'Add to Cart' }}</span>
            </button>
        </div>
    </div>
</article>
