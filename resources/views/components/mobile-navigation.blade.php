@php($mobileLabel = fn($en, $bn) => session('language') === 'bangla' ? $bn : $en)
<nav class="roh-mobile-nav" aria-label="{{ $mobileLabel('Mobile navigation', 'মোবাইল নেভিগেশন') }}">
    <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif><i class="fa-solid fa-house" aria-hidden="true"></i><span>{{ $mobileLabel('Home', 'হোম') }}</span></a>
    <button type="button" data-category-open aria-controls="roh-category-drawer" aria-expanded="false"><i class="fa-solid fa-bars" aria-hidden="true"></i><span>{{ $mobileLabel('Menu', 'মেনু') }}</span></button>
    <a href="{{ route('product.show') }}" @if(request()->routeIs('product.show')) aria-current="page" @endif><i class="fa-solid fa-store" aria-hidden="true"></i><span>{{ $mobileLabel('Shop', 'শপ') }}</span></a>
    <a href="{{ route('cart.show') }}" data-cart-open aria-controls="rf-cart-sidebar" aria-expanded="false"><span class="roh-mobile-nav__cart"><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i><span class="cartQty">{{ Cart::count() }}</span></span><span>{{ $mobileLabel('Cart', 'কার্ট') }}</span></a>
    <a href="{{ route(auth()->check() ? 'dashboard' : 'login') }}"><i class="fa-regular fa-user" aria-hidden="true"></i><span>{{ $mobileLabel('Account', 'অ্যাকাউন্ট') }}</span></a>
</nav>
<dialog id="roh-category-drawer" class="roh-category-drawer" aria-labelledby="roh-category-drawer-title">
    <header><div><span>ROH FASHION</span><h2 id="roh-category-drawer-title">{{ $mobileLabel('All categories', 'সকল ক্যাটাগরি') }}</h2></div><button type="button" data-category-close aria-label="{{ $mobileLabel('Close menu', 'মেনু বন্ধ করুন') }}">×</button></header>
    <nav class="roh-category-drawer__body" aria-label="{{ $mobileLabel('Product categories', 'পণ্যের ক্যাটাগরি') }}">
        <a class="roh-category-drawer__all" href="{{ route('product.show') }}">{{ $mobileLabel('Shop all products', 'সব পণ্য দেখুন') }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        @include('FrontEnd.partials.mobile-category-tree', ['branches' => $mobileCategories->get(0, collect())->merge($mobileCategories->get('', collect())), 'ancestors' => []])
    </nav>
</dialog>
