<dialog id="rf-product-dialog" class="rf-quick" aria-labelledby="rf-quick-title" data-base-url="{{ url('/') }}" data-bangla="{{ session('language') === 'bangla' ? '1' : '0' }}">
    <button type="button" class="rf-quick__close" data-quick-close aria-label="Close">&#215;</button>
    <p class="rf-quick__status" data-quick-status role="status"></p>
    <div class="rf-quick__layout" data-quick-content hidden>
        <div class="rf-quick__visual">
            <img data-quick-image alt="" loading="eager">
            <div class="rf-quick__gallery" data-quick-gallery></div>
        </div>
        <form class="rf-quick__form" data-quick-form>
            <span class="rf-quick__eyebrow">ROH FASHION</span>
            <h2 id="rf-quick-title" data-quick-title>{{ session('language') === 'bangla' ? 'পণ্যের তথ্য' : 'Product details' }}</h2>
            <div class="rf-quick__price" data-quick-price></div>
            <p class="rf-quick__stock" data-quick-stock></p>
            <p class="rf-quick__description" data-quick-description></p>
            <div class="rf-quick__attributes" data-quick-attributes></div>
            
            <div class="rf-quick-qty-wrapper">
                <span class="rf-quick-qty-label">{{ session('language') === 'bangla' ? 'পরিমাণ:' : 'Quantity:' }}</span>
                <div class="rf-quick-qty-box">
                    <button type="button" class="rf-quick-qty-btn" data-quick-qty-down aria-label="Decrease quantity">&#8722;</button>
                    <input id="rf-quick-quantity" class="rf-quick-qty-input" type="number" min="1" step="1" value="1" required data-quick-quantity readonly>
                    <button type="button" class="rf-quick-qty-btn" data-quick-qty-up aria-label="Increase quantity">+</button>
                </div>
            </div>

            <p class="rf-quick__error" data-quick-error role="alert"></p>
            <button class="rf-product__add" type="submit" data-quick-submit>{{ session('language') === 'bangla' ? 'কার্টে যোগ করুন' : 'Add to cart' }}</button>
            <button class="rf-product__buy" type="submit" data-quick-buy>{{ session('language') === 'bangla' ? 'অর্ডার করতে এই বাটনে ক্লিক করুন' : 'Order Now' }}</button>
            <a class="rf-quick__details" data-quick-details>{{ session('language') === 'bangla' ? 'সম্পূর্ণ বিস্তারিত দেখুন' : 'View full product details' }} &#8594;</a>
        </form>
    </div>
</dialog>
<div class="rf-cart-notice" data-cart-notice role="status" hidden></div>
