@php
    $isBangla = session('language') === 'bangla';
    $user = Auth::user();
    $logoUrl = asset(get_setting('site_logo')->value ?: 'upload/setting/logo/1790046067Screenshot_1.png');
    $accountLabel = $user ? Str::before($user->name, ' ') : ($isBangla ? 'লগইন' : 'Sign in');
    $navLinks = [
        ['home', 'Home', 'হোম'],
        ['category_list.index', 'Collections', 'কালেকশন'],
        ['page.about', 'Our story', 'আমাদের গল্প'],
        ['page.contact', 'Contact', 'যোগাযোগ'],
    ];
@endphp

<header class="roh-header" data-roh-header>
    <div class="roh-header__ribbon">
        <span>ROH FASHION</span>
        <span class="roh-header__ribbon-message">{{ $isBangla ? 'আপনার সাজে, আপনার গল্প' : 'A little elegance. Every day.' }}</span>
        <span>STYLE, YOUR WAY</span>
    </div>

    <div class="roh-header__masthead">
        <div class="roh-header__discovery">
            <button class="roh-header__menu-toggle roh-header__icon" type="button" aria-controls="roh-navigation" aria-expanded="false" aria-label="{{ $isBangla ? 'মেনু' : 'Menu' }}" data-roh-menu>
                <i class="fa-solid fa-bars-staggered" aria-hidden="true"></i>
            </button>
            <details class="roh-header__search" data-roh-search>
                <summary aria-label="{{ $isBangla ? 'পণ্য খুঁজুন' : 'Search products' }}">
                    <span class="roh-header__icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                    <span class="roh-header__search-copy"><small>{{ $isBangla ? 'খুঁজে নিন' : 'FIND YOUR FAVOURITE' }}</small>{{ $isBangla ? 'আপনার পছন্দের স্টাইল' : 'What’s your style?' }}</span>
                </summary>
                <div class="roh-header__search-panel">
                    <form action="{{ route('product.search') }}" method="post" role="search">
                        @csrf
                        <label for="roh-search-input">{{ $isBangla ? 'আপনার পছন্দের পণ্য খুঁজুন' : 'Find something you’ll love' }}</label>
                        <div class="roh-header__search-field">
                            <input id="roh-search-input" type="search" name="search" required placeholder="{{ $isBangla ? 'পণ্য খুঁজুন…' : 'Search products…' }}">
                            <button type="submit" aria-label="{{ $isBangla ? 'অনুসন্ধান' : 'Search' }}"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
            </details>
        </div>

        <a class="roh-header__brand" href="{{ route('home') }}" aria-label="Roh Fashion — {{ $isBangla ? 'হোম' : 'Home' }}">
            <img src="{{ $logoUrl }}" alt="Roh Fashion" width="190" height="94" fetchpriority="high">
        </a>

        <div class="roh-header__actions">
            <a class="roh-header__account" href="{{ route($user ? 'dashboard' : 'login') }}" aria-label="{{ $isBangla ? 'অ্যাকাউন্ট' : 'Account' }}">
                <span class="roh-header__icon">
                    @if($user && $user->profile_image)
                        <img src="{{ asset($user->profile_image) }}" alt="" width="24" height="24">
                    @else
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                    @endif
                </span>
                <span class="roh-header__account-label">{{ $accountLabel }}</span>
            </a>
            <span class="roh-header__action-divider" aria-hidden="true"></span>
            <a class="roh-header__bag" href="{{ route('cart.show') }}" aria-label="{{ $isBangla ? 'শপিং ব্যাগ' : 'Shopping bag' }}">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                <span class="roh-header__bag-label">{{ $isBangla ? 'ব্যাগ' : 'My bag' }}</span>
                <span class="roh-header__count cartQty">{{ Cart::count() }}</span>
            </a>
        </div>
    </div>

    <nav class="roh-header__navigation" id="roh-navigation" aria-label="{{ $isBangla ? 'প্রধান নেভিগেশন' : 'Main navigation' }}">
        <a class="roh-header__tracking" href="{{ route('order.tracking') }}"><i class="fa-solid fa-truck-fast" aria-hidden="true"></i>{{ $isBangla ? 'অর্ডার ট্র্যাক' : 'Track your order' }}</a>
        <ul class="roh-header__links">
            @foreach($navLinks as [$navRoute, $english, $bangla])
                <li><a href="{{ route($navRoute) }}" @if(request()->routeIs($navRoute)) aria-current="page" @endif>{{ $isBangla ? $bangla : $english }}</a></li>
            @endforeach
        </ul>
        <a class="roh-header__offers" href="{{ route('campaign.product') }}" @if(request()->routeIs('campaign.product')) aria-current="page" @endif><i class="fa-solid fa-tag" aria-hidden="true"></i>{{ $isBangla ? 'বিশেষ অফার' : 'The offer edit' }}<i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
    </nav>
</header>
