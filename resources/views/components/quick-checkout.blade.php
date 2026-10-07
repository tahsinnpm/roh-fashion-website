@once
    @php($checkoutLabel = fn($en, $bn) => session('language') === 'bangla' ? $bn : $en)
    <dialog id="rf-checkout" class="rf-checkout" aria-labelledby="rf-checkout-title" data-url="{{ route('checkout.quick') }}" data-checkout-path="{{ route('checkout') }}" data-bangla="{{ session('language') === 'bangla' ? '1' : '0' }}">
        <header class="rf-checkout__header">
            <div>
                <span class="rf-checkout__brand">ROH FASHION</span>
                <h2 id="rf-checkout-title">{{ $checkoutLabel('Complete your order', 'আপনার অর্ডার সম্পন্ন করুন') }}</h2>
            </div>
            <button type="button" class="rf-checkout__close" data-checkout-close aria-label="{{ $checkoutLabel('Close checkout', 'বন্ধ করুন') }}">×</button>
        </header>

        <p class="rf-checkout__status" data-checkout-status role="status" tabindex="-1"></p>
        <button class="rf-checkout__retry" type="button" data-checkout-retry hidden>{{ $checkoutLabel('Try again', 'আবার চেষ্টা করুন') }}</button>

        <form class="rf-checkout__layout" data-checkout-form hidden>
            {{-- Left Column: Delivery Form --}}
            <div class="rf-checkout__fields">
                <p class="rf-checkout__intro">{{ $checkoutLabel('Please provide your delivery details below.', 'অর্ডারটি কনফার্ম করতে নিচের তথ্যগুলো পূরণ করুন।') }}</p>

                <div class="rf-checkout__field">
                    <label for="checkout-name">{{ $checkoutLabel('Full Name', 'আপনার নাম') }} <span class="rf-req">*</span></label>
                    <input id="checkout-name" name="name" autocomplete="name" maxlength="150" placeholder="{{ $checkoutLabel('e.g. Rahim Ahmed', 'যেমন: রহিম আহমেদ') }}" required>
                </div>

                <div class="rf-checkout__field">
                    <label for="checkout-phone">{{ $checkoutLabel('Phone Number', 'মোবাইল নম্বর') }} <span class="rf-req">*</span></label>
                    <input id="checkout-phone" name="phone" type="tel" autocomplete="tel" placeholder="01XXXXXXXXX" maxlength="20" required>
                </div>

                <div class="rf-checkout__field">
                    <label for="checkout-address">{{ $checkoutLabel('Delivery Address', 'সম্পূর্ণ ঠিকানা') }} <span class="rf-req">*</span></label>
                    <textarea id="checkout-address" name="address" autocomplete="street-address" placeholder="{{ $checkoutLabel('House, Road, Area, Thana, District', 'বাসা নং, রোড নং, এলাকা, থানা, জেলা') }}" rows="2" maxlength="255" required></textarea>
                </div>

                <div class="rf-checkout__field">
                    <label for="checkout-area">{{ $checkoutLabel('Delivery Area', 'ডেলিভারি এলাকা') }} <span class="rf-req">*</span></label>
                    <select id="checkout-area" name="shipping_id" required>
                        <option value="">{{ $checkoutLabel('Select delivery area', 'ডেলিভারি এলাকা বাছুন') }}</option>
                    </select>
                </div>

                <div class="rf-checkout__field">
                    <label for="checkout-email">{{ $checkoutLabel('Email (Optional)', 'ইমেইল (ঐচ্ছিক)') }}</label>
                    <input id="checkout-email" name="email" type="email" autocomplete="email" placeholder="example@email.com" maxlength="191">
                </div>

                <div class="rf-checkout__field">
                    <label for="checkout-comment">{{ $checkoutLabel('Order Note (Optional)', 'অর্ডার নোট (ঐচ্ছিক)') }}</label>
                    <textarea id="checkout-comment" name="comment" rows="2" placeholder="{{ $checkoutLabel('Any special instructions for delivery...', 'ডেলিভারি সম্পর্কে কোনো বিশেষ নির্দেশনা থাকলে লিখুন...') }}" maxlength="1000"></textarea>
                </div>

                <p class="rf-checkout__account">
                    <i class="fa-solid fa-shield-halved me-1"></i>
                    {{ $checkoutLabel('New customer? An account will be created automatically using your phone (Initial password: 12345678).', 'নতুন গ্রাহকদের ফোন নম্বর দিয়ে স্বয়ংক্রিয়ভাবে অ্যাকাউন্ট তৈরি হয়ে যাবে (ডিফল্ট পাসওয়ার্ড: 12345678)।') }}
                </p>
            </div>

            {{-- Right Column: Cart Products & Pricing --}}
            <aside class="rf-checkout__summary">
                <div class="rf-checkout__summary-head">
                    <h3>{{ $checkoutLabel('Your Order Items', 'অর্ডারের পণ্যসমূহ') }}</h3>
                </div>

                <div class="rf-checkout__items-container">
                    <ul class="rf-checkout__items" data-checkout-items></ul>
                </div>

                <div class="rf-checkout__breakdown">
                    <div class="rf-checkout__line">
                        <span>{{ $checkoutLabel('Subtotal', 'সাবটোটাল') }}</span>
                        <strong data-checkout-subtotal>৳0.00</strong>
                    </div>
                    <div class="rf-checkout__line" data-checkout-discount-row hidden>
                        <span>{{ $checkoutLabel('Discount', 'ছাড়') }}</span>
                        <strong data-checkout-discount>৳0.00</strong>
                    </div>
                    <div class="rf-checkout__line">
                        <span>{{ $checkoutLabel('Delivery Charge', 'ডেলিভারি চার্জ') }}</span>
                        <strong data-checkout-delivery>—</strong>
                    </div>
                    <div class="rf-checkout__line rf-checkout__total">
                        <span>{{ $checkoutLabel('Total Payable', 'সর্বমোট বিল') }}</span>
                        <strong data-checkout-total>৳0.00</strong>
                    </div>
                </div>

                <div class="rf-checkout__payment-badge">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>{{ $checkoutLabel('Cash on Delivery · Pay when parcel arrives', 'ক্যাশ অন ডেলিভারি · পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন') }}</span>
                </div>

                <button class="rf-checkout__submit" type="submit" data-checkout-submit>
                    <span>{{ $checkoutLabel('Confirm Order', 'অর্ডার নিশ্চিত করুন') }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </aside>
        </form>
    </dialog>
@endonce
