@once
    <dialog id="rf-cart-sidebar" class="rf-cart" aria-labelledby="rf-cart-title" data-cart-url="{{ route('cart.sidebar') }}" data-bangla="{{ session('language') === 'bangla' ? '1' : '0' }}">
        <div class="rf-cart__header">
            <div><span class="rf-cart__eyebrow">Shinduk Fashion</span><h2 id="rf-cart-title">{{ session('language') === 'bangla' ? 'আপনার শপিং ব্যাগ' : 'Your shopping bag' }} <span data-cart-count>0</span></h2></div>
            <button type="button" class="rf-cart__close" data-cart-close aria-label="{{ session('language') === 'bangla' ? 'বন্ধ করুন' : 'Close cart' }}">×</button>
        </div>
        <div class="rf-cart__body">
            <p class="rf-cart__status" data-cart-status role="status"></p>
            <button type="button" class="rf-cart__retry" data-cart-retry hidden>{{ session('language') === 'bangla' ? 'আবার চেষ্টা করুন' : 'Try again' }}</button>
            <div class="rf-cart__empty" data-cart-empty hidden>
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                <h3>{{ session('language') === 'bangla' ? 'আপনার ব্যাগ এখন খালি' : 'Your bag is empty' }}</h3>
                <p>{{ session('language') === 'bangla' ? 'পছন্দের পণ্য বেছে নিন।' : 'Find something you’ll love and add it here.' }}</p>
                <button type="button" data-cart-close>{{ session('language') === 'bangla' ? 'কেনাকাটা চালিয়ে যান' : 'Continue shopping' }}</button>
            </div>
            <ul class="rf-cart__items" data-cart-items></ul>
        </div>
        <div class="rf-cart__footer" data-cart-footer hidden>
            <div class="rf-cart__subtotal"><span>{{ session('language') === 'bangla' ? 'সাবটোটাল' : 'Subtotal' }}</span><strong data-cart-subtotal></strong></div>
            <p>{{ session('language') === 'bangla' ? 'ডেলিভারি চার্জ checkout-এ হিসাব হবে।' : 'Delivery charges are calculated at checkout.' }}</p>
            <a class="rf-cart__checkout" href="{{ route('checkout') }}">{{ session('language') === 'bangla' ? 'চেকআউট করুন' : 'Proceed to checkout' }} →</a>
            <a class="rf-cart__view" href="{{ route('cart.show') }}">{{ session('language') === 'bangla' ? 'সম্পূর্ণ কার্ট দেখুন' : 'View full cart' }}</a>
        </div>
    </dialog>
@endonce
