@once
    @php($checkoutLabel = fn($en, $bn) => session('language') === 'bangla' ? $bn : $en)
    <dialog id="rf-checkout" class="rf-checkout" aria-labelledby="rf-checkout-title" data-url="{{ route('checkout.quick') }}" data-checkout-path="{{ route('checkout') }}" data-bangla="{{ session('language') === 'bangla' ? '1' : '0' }}">
        <header class="rf-checkout__header">
            <div><span>ROH FASHION</span><h2 id="rf-checkout-title">{{ $checkoutLabel('Complete your order', 'আপনার অর্ডার সম্পন্ন করুন') }}</h2></div>
            <button type="button" data-checkout-close aria-label="{{ $checkoutLabel('Close checkout', 'বন্ধ করুন') }}">×</button>
        </header>
        <p class="rf-checkout__status" data-checkout-status role="status" tabindex="-1"></p>
        <button class="rf-checkout__retry" type="button" data-checkout-retry hidden>{{ $checkoutLabel('Try again', 'আবার চেষ্টা করুন') }}</button>
        <form class="rf-checkout__layout" data-checkout-form hidden>
            <div class="rf-checkout__fields">
                <p class="rf-checkout__intro">{{ $checkoutLabel('A few details, and it’s on its way.', 'অল্প কিছু তথ্য দিন, অর্ডার হয়ে যাবে।') }}</p>
                <label for="checkout-name">{{ $checkoutLabel('Full name', 'আপনার নাম') }}</label>
                <input id="checkout-name" name="name" autocomplete="name" maxlength="150" required>
                <label for="checkout-phone">{{ $checkoutLabel('Phone number', 'ফোন নম্বর') }}</label>
                <input id="checkout-phone" name="phone" type="tel" autocomplete="tel" placeholder="01XXXXXXXXX" maxlength="20" required>
                <label for="checkout-address">{{ $checkoutLabel('Delivery address', 'ডেলিভারির ঠিকানা') }}</label>
                <textarea id="checkout-address" name="address" autocomplete="street-address" placeholder="{{ $checkoutLabel('House, road, area, city', 'বাসা, রোড, এলাকা, শহর') }}" rows="3" maxlength="255" required></textarea>
                <label for="checkout-area">{{ $checkoutLabel('Delivery area', 'ডেলিভারি এলাকা') }}</label>
                <select id="checkout-area" name="shipping_id" required></select>
                <p class="rf-checkout__account">{{ $checkoutLabel('New customers: an account will be created using your phone number with initial password 12345678. You can change it after signing in.', 'নতুন গ্রাহকের ফোন নম্বর দিয়ে অ্যাকাউন্ট তৈরি হবে। প্রাথমিক পাসওয়ার্ড 12345678; লগইন করে পরিবর্তন করতে পারবেন।') }}</p>
            </div>
            <aside class="rf-checkout__summary">
                <h3>{{ $checkoutLabel('Your order', 'আপনার অর্ডার') }}</h3>
                <ul data-checkout-items></ul>
                <div class="rf-checkout__line"><span>{{ $checkoutLabel('Subtotal', 'সাবটোটাল') }}</span><strong data-checkout-subtotal></strong></div>
                <div class="rf-checkout__line" data-checkout-discount-row hidden><span>{{ $checkoutLabel('Discount', 'ছাড়') }}</span><strong data-checkout-discount></strong></div>
                <div class="rf-checkout__line"><span>{{ $checkoutLabel('Delivery', 'ডেলিভারি') }}</span><strong data-checkout-delivery>—</strong></div>
                <div class="rf-checkout__line rf-checkout__total"><span>{{ $checkoutLabel('Total', 'সর্বমোট') }}</span><strong data-checkout-total></strong></div>
                <p class="rf-checkout__payment">{{ $checkoutLabel('Cash on delivery · Pay when your order arrives', 'ক্যাশ অন ডেলিভারি · পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন') }}</p>
                <button class="rf-checkout__submit" type="submit" data-checkout-submit>{{ $checkoutLabel('Confirm order', 'অর্ডার নিশ্চিত করুন') }} →</button>
            </aside>
        </form>
    </dialog>
@endonce
