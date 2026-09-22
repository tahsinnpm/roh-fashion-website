<dialog id="rf-product-dialog" class="rf-quick" aria-labelledby="rf-quick-title" data-base-url="{{ url('/') }}" data-checkout-url="{{ route('checkout') }}" data-bangla="{{ session('language') === 'bangla' ? '1' : '0' }}">
    <button type="button" class="rf-quick__close" data-quick-close aria-label="Close">×</button>
    <p class="rf-quick__status" data-quick-status role="status"></p>
    <div class="rf-quick__layout" data-quick-content hidden>
        <div class="rf-quick__visual"><img data-quick-image alt=""><div class="rf-quick__gallery" data-quick-gallery></div></div>
        <form class="rf-quick__form" data-quick-form>
            <span class="rf-quick__eyebrow">ROH FASHION</span>
            <h2 id="rf-quick-title" data-quick-title>{{ session('language') === 'bangla' ? 'পণ্যের তথ্য' : 'Product details' }}</h2>
            <div class="rf-quick__price" data-quick-price></div>
            <p data-quick-stock></p>
            <p class="rf-quick__description" data-quick-description></p>
            <div class="rf-quick__attributes" data-quick-attributes></div>
            <label for="rf-quick-quantity">{{ session('language') === 'bangla' ? 'পরিমাণ' : 'Quantity' }}</label>
            <input id="rf-quick-quantity" type="number" min="1" step="1" value="1" required data-quick-quantity>
            <p class="rf-quick__error" data-quick-error role="alert"></p>
            <button class="rf-product__add" type="submit" data-quick-submit>{{ session('language') === 'bangla' ? 'কার্টে যোগ করুন' : 'Add to cart' }}</button>
            <button class="rf-product__buy" type="submit" data-quick-buy>{{ session('language') === 'bangla' ? 'এখনই কিনুন' : 'Buy now' }}</button>
            <a class="rf-quick__details" data-quick-details>{{ session('language') === 'bangla' ? 'সম্পূর্ণ বিস্তারিত দেখুন' : 'View full product details' }} →</a>
        </form>
    </div>
</dialog>
<div class="rf-cart-notice" data-cart-notice role="status" hidden></div>
