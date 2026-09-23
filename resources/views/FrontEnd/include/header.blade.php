@php
    $isBangla = session('language') === 'bangla';
    $user = Auth::user();
    $logoUrl = asset(get_setting('site_logo')->value ?: 'upload/setting/logo/1790046067Screenshot_1.png');
    $phone = trim(get_setting('phone')->value ?? '');
    $email = trim(get_setting('email')->value ?? '');
    $phoneLink = preg_replace('/[^0-9+]/', '', $phone);
@endphp

<header class="roh-header" data-roh-header>
    <div class="roh-topbar">
        <div class="roh-topbar__inner">
            <div class="roh-topbar__contact">
                @if($phone && strtolower($phone) !== 'null')
                    <a href="tel:{{ $phoneLink }}"><i class="fa-solid fa-phone" aria-hidden="true"></i><span>{{ $phone }}</span></a>
                @endif
                @if($email && strtolower($email) !== 'null')
                    <a class="roh-topbar__email" href="mailto:{{ $email }}"><i class="fa-regular fa-envelope" aria-hidden="true"></i><span>{{ $email }}</span></a>
                @endif
            </div>
            <a href="{{ route('page.contact') }}">{{ $isBangla ? 'সাহায্য ও যোগাযোগ' : 'Help & contact' }}<i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="roh-header__masthead">

        <a class="roh-header__brand" href="{{ route('home') }}" aria-label="Roh Fashion — {{ $isBangla ? 'হোম' : 'Home' }}">
            <img src="{{ $logoUrl }}" alt="Roh Fashion" width="124" height="62" fetchpriority="high">
        </a>

        <form class="roh-header__search" action="{{ route('product.search') }}" method="post" role="search">
            @csrf
            <label class="visually-hidden" for="roh-search-input">{{ $isBangla ? 'আপনার পছন্দের পণ্য খুঁজুন' : 'Find something you’ll love' }}</label>
            <div class="roh-header__search-field">
                <input id="roh-search-input" type="search" name="search" required placeholder="{{ $isBangla ? 'পণ্য খুঁজুন…' : 'Search products…' }}">
                <button type="submit" aria-label="{{ $isBangla ? 'অনুসন্ধান' : 'Search' }}"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
            </div>
        </form>

        <div class="roh-header__actions">
            <a class="roh-header__tracking" aria-label="{{ $isBangla ? 'অর্ডার ট্র্যাক' : 'Track order' }}" title="{{ $isBangla ? 'অর্ডার ট্র্যাক' : 'Track order' }}" href="{{ route('order.tracking') }}">
                <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                <span>{{ $isBangla ? 'অর্ডার ট্র্যাক' : 'Track order' }}</span>
            </a>
            <a class="roh-header__account" href="{{ route($user ? 'dashboard' : 'login') }}" aria-label="{{ $isBangla ? 'অ্যাকাউন্ট' : 'Account' }}">
                <span class="roh-header__icon">
                    @if($user && $user->profile_image)
                        <img src="{{ asset($user->profile_image) }}" alt="" width="24" height="24">
                    @else
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                    @endif
                </span>
            </a>
            <a class="roh-header__bag" href="{{ route('cart.show') }}" data-cart-open aria-controls="rf-cart-sidebar" aria-expanded="false" aria-label="{{ $isBangla ? 'শপিং ব্যাগ' : 'Shopping bag' }}">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                <span class="roh-header__count cartQty">{{ Cart::count() }}</span>
            </a>
        </div>
    </div>
</header>
