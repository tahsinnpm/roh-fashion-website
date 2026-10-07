@php
    $isBangla = session('language') === 'bangla';
    $user = Auth::user();
    $logoUrl = asset(get_setting('site_logo')->value ?: 'upload/setting/logo/1790046067Screenshot_1.png');
    $marqueeText = trim(get_setting('topbar_marquee_text')->value ?? '');
@endphp

<header class="roh-header" data-roh-header>
    @if($marqueeText)
        <div class="roh-topbar" role="region" aria-label="Announcement">
            <div class="roh-topbar__marquee">
                <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" ontouchstart="this.stop();" ontouchend="this.start();">
                    {{ $marqueeText }}
                </marquee>
            </div>
        </div>
    @endif
    <div class="roh-header__masthead">
        {{-- Mobile Menu (Hamburger) Button - Left on mobile --}}
        <button type="button" class="roh-header__menu-btn" data-category-open aria-controls="roh-category-drawer" aria-expanded="false" aria-label="{{ $isBangla ? 'মেনু' : 'Menu' }}">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        {{-- Brand Logo - Middle on mobile --}}
        <a class="roh-header__brand" href="{{ route('home') }}" aria-label="Roh Fashion — {{ $isBangla ? 'হোম' : 'Home' }}">
            <img src="{{ $logoUrl }}" alt="Roh Fashion" width="124" height="62" fetchpriority="high">
        </a>

        {{-- Search Form - toggled on mobile, left on desktop --}}
        <form class="roh-header__search" action="{{ route('product.search') }}" method="post" role="search">
            @csrf
            <label class="visually-hidden" for="roh-search-input">{{ $isBangla ? 'আপনার পছন্দের পণ্য খুঁজুন' : 'Find something you’ll love' }}</label>
            <div class="roh-header__search-field">
                <input id="roh-search-input" type="search" name="search" required placeholder="{{ $isBangla ? 'পণ্য খুঁজুন…' : 'Search products…' }}">
                <button type="submit" aria-label="{{ $isBangla ? 'অনুসন্ধান' : 'Search' }}"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
            </div>
        </form>

        {{-- Actions - Right on mobile --}}
        <div class="roh-header__actions">
            {{-- Mobile Search Toggle Button --}}
            <button type="button" class="roh-header__search-toggle" data-search-toggle aria-label="{{ $isBangla ? 'অনুসন্ধান' : 'Search' }}">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            </button>

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

    <nav class="roh-category-nav" aria-label="Main categories">
        <div class="roh-category-nav__inner">
            <a href="{{ route('product.show', ['filter' => 'new_arrivals']) }}">{{ $isBangla ? 'নতুন এসেছে' : 'New Arrivals' }}</a>
            <a href="{{ route('product.show', ['filter' => 'best_sellers']) }}">{{ $isBangla ? 'সেরা বিক্রিত' : 'Best Sellers' }}</a>
            @foreach($categories as $category)
                <a href="{{ route('product.category', $category->slug) }}">{{ $isBangla && $category->name_bn ? $category->name_bn : $category->name_en }}</a>
            @endforeach
        </div>
    </nav>
</header>
