@once
    @php
        $checkoutLabel = fn($en, $bn) => session('language') === 'bangla' ? $bn : $en;
        $shippings = \App\Models\Shipping::where('status', 1)->orderBy('type', 'asc')->get();
    @endphp
    <dialog id="rf-checkout" class="rf-checkout" aria-labelledby="rf-checkout-title"
        data-url="{{ route('checkout.quick') }}" data-coupon-url="{{ route('checkout.quick.coupon') }}" data-capture-url="{{ route('abandoned-checkout.capture') }}" data-checkout-path="{{ route('checkout') }}"
        data-bangla="{{ session('language') === 'bangla' ? '1' : '0' }}">
        <header class="rf-checkout__header">
            <div style="width:100%; text-align: center;">
                <!-- <span class="rf-checkout__brand">Shinduk Fashion</span> -->
                <h2 id="rf-checkout-title">{{ $checkoutLabel('ক্যাশ অন ডেলিভারিতে অর্ডার করুন
                                ', 'আপনার অর্ডার সম্পন্ন করুন') }}</h2>
                <p style="font-size: 12px;">অর্ডার নিশ্চিত করতে আপনার তথ্য দিন

                </p>
            </div>
            <button type="button" class="rf-checkout__close" data-checkout-close
                aria-label="{{ $checkoutLabel('Close checkout', 'বন্ধ করুন') }}">
            <span>×</span>
            </button>
        </header>

        <p class="rf-checkout__status" data-checkout-status role="status" tabindex="-1"></p>
        <button class="rf-checkout__retry" type="button" data-checkout-retry
            hidden>{{ $checkoutLabel('Try again', 'আবার চেষ্টা করুন') }}</button>

        <form class="rf-checkout__layout" data-checkout-form hidden>
            {{-- Left Column: Delivery Form --}}
            <div class="rf-checkout__fields">
                <div class="rf-checkout__field">
                    <label for="checkout-name">{{ $checkoutLabel('আপনার নাম', 'আপনার নাম') }} <span
                            class="rf-req">*</span></label>
                    <input id="checkout-name" name="name" autocomplete="name" maxlength="150"
                        placeholder="{{ $checkoutLabel('আপনার পুরো নাম লিখুন', 'আপনার পুরো নাম লিখুন') }}" required>
                </div>

                <div class="rf-checkout__field">
                    <label for="checkout-phone">{{ $checkoutLabel('ফোন নম্বর', 'মোবাইল নম্বর') }} <span
                            class="rf-req">*</span></label>
                    <input id="checkout-phone" name="phone" type="tel" autocomplete="tel" placeholder="01XXXXXXXXX"
                        maxlength="20" required>
                </div>
                <div class="rf-checkout__field">
                    <label for="checkout-address">{{ $checkoutLabel('সম্পূর্ণ ঠিকানা', 'সম্পূর্ণ ঠিকানা') }} <span
                            class="rf-req">*</span></label>
                    <textarea id="checkout-address" name="address" autocomplete="street-address"
                        placeholder="{{ $checkoutLabel('আপনার সম্পূর্ণ ঠিকানা দিন', 'বাসা নং, রোড নং, এলাকা, থানা, জেলা') }}"
                        rows="2" maxlength="255" required></textarea>
                </div>

                <div class="rf-checkout__field">
                    <label>{{ $checkoutLabel('ডেলিভারি এলাকা নির্বাচন করুন', 'ডেলিভারি এলাকা') }} <span
                            class="rf-req">*</span></label>
                    <div class="co-options" data-checkout-shipping-options>
                        @foreach($shippings as $shipping)
                            @php
                                $shippingTitle = $shipping->type == 1
                                    ? $checkoutLabel('ঢাকার ভিতরে ডেলিভারি চার্জ', 'ঢাকার ভিতরে  ডেলিভারি চার্জ')
                                    : ($shipping->type == 2
                                        ? $checkoutLabel('ঢাকার বাহিরে ডেলিভারি চার্জ', 'ঢাকার বাহিরে  ডেলিভারি চার্জ')
                                        : ($shipping->title ?: $shipping->name));
                                $shippingPrice = (float) ($shipping->price ?? $shipping->shipping_charge ?? 0);
                            @endphp
                            <label class="co-option {{ $loop->first ? 'active' : '' }}">
                                <input type="radio" name="shipping_id" value="{{ $shipping->id }}"
                                    data-price="{{ $shippingPrice }}" {{ $loop->first ? 'checked' : '' }} required>
                                <span>{{ $shippingTitle }}</span>
                                <strong>৳{{ number_format($shippingPrice, 2) }}</strong>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="rf-checkout__field" style="margin-bottom: 10px;">
                    <label for="checkout-comment">{{ $checkoutLabel('অর্ডার নোট (ঐচ্ছিক)', 'অর্ডার নোট (ঐচ্ছিক)') }}</label>
                    <textarea id="checkout-comment" name="comment" rows="2"
                        placeholder="{{ $checkoutLabel('Any special instructions for delivery...', 'ডেলিভারি সম্পর্কে কোনো বিশেষ নির্দেশনা থাকলে লিখুন...') }}"
                        maxlength="1000"></textarea>
                </div>
            </div>


            {{-- Right Column: Cart Products & Pricing --}}
            <aside class="rf-checkout__summary">
                <div class="rf-checkout__summary-head">
                    <h3>{{ $checkoutLabel('অর্ডারের পণ্যসমূহ', 'অর্ডারের পণ্যসমূহ') }}</h3>
                </div>

                <div class="rf-checkout__items-container">
                    <ul class="rf-checkout__items" data-checkout-items></ul>
                </div>

                <div class="rf-checkout__coupon">
                    <label for="checkout-coupon">কুপন কোড</label>
                    <div class="rf-checkout__coupon-controls">
                        <input id="checkout-coupon" name="coupon_code" type="text" maxlength="50" autocomplete="off" placeholder="কুপন কোড লিখুন">
                        <button type="button" data-checkout-coupon-apply>Apply</button>
                    </div>
                    <div class="rf-checkout__coupon-result" data-checkout-coupon-status aria-live="polite"></div>
                    <button type="button" data-checkout-coupon-remove hidden>কুপন সরান</button>
                </div>

                <div class="rf-checkout__breakdown">
                    <div class="rf-checkout__line">
                        <span>{{ $checkoutLabel('Cart subtotal', 'সাবটোটাল') }}</span>
                        <strong data-checkout-subtotal>৳0.00</strong>
                    </div>
                    <div class="rf-checkout__line" data-checkout-discount-row hidden>
                        <span>{{ $checkoutLabel('ছাড়', 'ছাড়') }}</span>
                        <strong data-checkout-discount>৳0.00</strong>
                    </div>
                    <div class="rf-checkout__line">
                        <span>{{ $checkoutLabel('Delivery charge', 'ডেলিভারি চার্জ') }}</span>
                        <strong data-checkout-delivery>—</strong>
                    </div>
                    <div class="rf-checkout__line rf-checkout__total">
                        <span>{{ $checkoutLabel('Total', 'সর্বমোট বিল') }}</span>
                        <strong data-checkout-total>৳0.00</strong>
                    </div>
                </div>
                <button class="rf-checkout__submit" type="submit" data-checkout-submit>
                    <span>{{ $checkoutLabel('আপনার অর্ডার কনফার্ম করুন', 'আপনার অর্ডার কনফার্ম করুন') }}</span>
                </button>
            </aside>
        </form>
    </dialog>
@endonce
