@php
    $footerSetting = fn($key, $fallback = '') => optional(get_setting($key))->value ?? $fallback;
    $isBanglaFooter = session('language') === 'bangla';
    $copyrightText = trim((string) $footerSetting('copy_right'));
@endphp

<footer class="rf-site-footer mt-5" aria-labelledby="rf-site-footer-title">
    <div class="rf-site-footer__main">
        <div class="rf-site-footer__brand">
            <a href="{{ route('home') }}" aria-label="{{ $footerSetting('business_name', 'Shinduk Fashion') }} home">
                <img src="{{ asset($footerSetting('site_logo', 'FrontEnd/assect/img/logo.png')) }}" alt="{{ $footerSetting('business_name', 'Shinduk Fashion') }}">
            </a>
            @if($footerSetting('short_description'))
                <p class="rf-site-footer__description">{{ $footerSetting('short_description') }}</p>
            @endif
            @if($footerSetting('business_address'))<p><i class="fa-solid fa-location-dot"></i>{{ $footerSetting('business_address') }}</p>@endif
            @if($footerSetting('phone'))<p><i class="fa-solid fa-phone"></i><a href="tel:{{ $footerSetting('phone') }}">{{ $footerSetting('phone') }}</a></p>@endif
            @if($footerSetting('email'))<p><i class="fa-regular fa-envelope"></i><a href="mailto:{{ $footerSetting('email') }}">{{ $footerSetting('email') }}</a></p>@endif
        </div>

        <nav class="rf-site-footer__links" aria-label="{{ $isBanglaFooter ? 'দ্রুত লিংক' : 'Quick links' }}">
            <h2>{{ $isBanglaFooter ? 'দ্রুত লিংক' : 'Quick links' }}</h2>
            <a href="{{ Auth::user() && Auth::user()->role == 3 ? route('dashboard') : route('login') }}">{{ Auth::user() && Auth::user()->role == 3 ? ($isBanglaFooter ? 'আমার অ্যাকাউন্ট' : 'Your account') : ($isBanglaFooter ? 'লগ ইন' : 'Log in') }}</a>
            <a href="{{ route('seller.apply') }}">{{ $isBanglaFooter ? 'বিক্রেতা হিসেবে যোগ দিন' : 'Become a seller' }}</a>
            <a href="{{ route('page.about') }}">{{ $isBanglaFooter ? 'আমাদের সম্পর্কে' : 'About us' }}</a>
            <a href="{{ route('page.policy') }}">{{ $isBanglaFooter ? 'গোপনীয়তা নীতি' : 'Privacy policy' }}</a>
            <a href="{{ route('page.terms') }}">{{ $isBanglaFooter ? 'শর্তাবলি' : 'Terms & conditions' }}</a>
        </nav>

        <nav class="rf-site-footer__links" aria-label="{{ $isBanglaFooter ? 'সাহায্য' : 'Help' }}">
            <h2>{{ $isBanglaFooter ? 'সাহায্য' : 'Help' }}</h2>
            <a href="{{ route('order.tracking') }}">{{ $isBanglaFooter ? 'অর্ডার ট্র্যাক করুন' : 'Track an order' }}</a>
            <a href="{{ route('page.help') }}">{{ $isBanglaFooter ? 'সাহায্য কেন্দ্র' : 'Help center' }}</a>
            <a href="{{ route('page.faq') }}">{{ $isBanglaFooter ? 'সাধারণ প্রশ্ন' : 'FAQs' }}</a>
        </nav>

        <div class="rf-site-footer__social">
            <h2>{{ $isBanglaFooter ? 'আমাদের সঙ্গে যুক্ত থাকুন' : 'Follow us' }}</h2>
            <div class="rf-site-footer__social-list">
                @foreach(['facebook' => 'facebook-f', 'instagram' => 'instagram', 'twitter' => 'x-twitter', 'youtube' => 'youtube', 'linkedin' => 'linkedin-in'] as $setting => $icon)
                    @if($footerSetting($setting . '_url'))
                        <a href="{{ $footerSetting($setting . '_url') }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($setting) }}"><i class="fa-brands fa-{{ $icon }}"></i></a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="rf-site-footer__bottom">
        @if($copyrightText !== '')
            <p>{{ $copyrightText }}</p>
        @else
            <p>&copy; {{ now()->year }} <a href="{{ route('home') }}">{{ $footerSetting('business_name', 'Shinduk Fashion') }}</a>. {{ $isBanglaFooter ? 'সর্বস্বত্ব সংরক্ষিত।' : 'All rights reserved.' }}</p>
        @endif

    </div>
</footer>
