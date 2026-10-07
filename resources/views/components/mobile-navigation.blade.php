@php
    $mobileLabel = fn($en, $bn) => session('language') === 'bangla' ? $bn : $en;
    $user = Auth::user();
    $logoUrl = asset(get_setting('site_logo')->value ?: 'upload/setting/logo/1790046067Screenshot_1.png');
@endphp

<dialog id="roh-category-drawer" class="roh-category-drawer" aria-labelledby="roh-category-drawer-title">
    <header class="roh-drawer-header">
        <a href="{{ route('home') }}" class="roh-drawer-logo" aria-label="Roh Fashion">
            <img src="{{ $logoUrl }}" alt="Roh Fashion" width="104" height="52">
        </a>
        <button type="button" class="roh-drawer-close" data-category-close aria-label="{{ $mobileLabel('Close menu', 'মেনু বন্ধ করুন') }}">&#215;</button>
    </header>

    <div class="roh-drawer-body">
        {{-- Primary Navigation (Home, Shop, About Us) --}}
        <div class="roh-drawer-group roh-drawer-group--primary">
            <ul class="roh-drawer-nav-list">
                <li>
                    <a href="{{ route('home') }}" class="roh-drawer-link {{ request()->routeIs('home') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-house" aria-hidden="true"></i>
                        <span>{{ $mobileLabel('Home', 'হোম') }}</span>
                        <i class="fa-solid fa-angle-right roh-drawer-link__arrow" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('product.show') }}" class="roh-drawer-link {{ request()->routeIs('product.show') && !request('filter') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-store" aria-hidden="true"></i>
                        <span>{{ $mobileLabel('Shop', 'শপ') }}</span>
                        <i class="fa-solid fa-angle-right roh-drawer-link__arrow" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('page.about') }}" class="roh-drawer-link {{ request()->routeIs('page.about') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <span>{{ $mobileLabel('About Us', 'আমাদের সম্পর্কে') }}</span>
                        <i class="fa-solid fa-angle-right roh-drawer-link__arrow" aria-hidden="true"></i>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Collections & Categories (Vertical List) --}}
        <div class="roh-drawer-group">
            <span class="roh-drawer-heading">{{ $mobileLabel('COLLECTIONS & CATEGORIES', 'কালেকশন ও ক্যাটাগরি সমূহ') }}</span>
            <ul class="roh-drawer-nav-list roh-drawer-collection-list">
                <li>
                    <a href="{{ route('product.show', ['filter' => 'new_arrivals']) }}" class="roh-drawer-link roh-drawer-link--badge {{ request('filter') === 'new_arrivals' ? 'is-active' : '' }}">
                        <span class="roh-drawer-emoji">🔥</span>
                        <span>{{ $mobileLabel('New Arrivals', 'New Arrivals') }}</span>
                        <span class="roh-drawer-badge">{{ $mobileLabel('New', 'নতুন') }}</span>
                        <i class="fa-solid fa-angle-right roh-drawer-link__arrow" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('product.show', ['filter' => 'best_sellers']) }}" class="roh-drawer-link roh-drawer-link--badge {{ request('filter') === 'best_sellers' ? 'is-active' : '' }}">
                        <span class="roh-drawer-emoji">⭐</span>
                        <span>{{ $mobileLabel('Best Sellers', 'Best Sellers') }}</span>
                        <span class="roh-drawer-badge roh-drawer-badge--hot">{{ $mobileLabel('Hot', 'হট') }}</span>
                        <i class="fa-solid fa-angle-right roh-drawer-link__arrow" aria-hidden="true"></i>
                    </a>
                </li>
            </ul>

            {{-- Categories Section Header --}}
            <div class="roh-drawer-subheading">
                <span class="roh-drawer-emoji">👗</span>
                <span>{{ $mobileLabel('All Categories', 'সকল ক্যাটাগরি ও সাব-ক্যাটাগরি') }}</span>
            </div>

            {{-- Categories & Sub-categories Tree --}}
            <div class="roh-drawer-categories">
                @include('FrontEnd.partials.mobile-category-tree', [
                    'branches' => $mobileCategories->get(0, collect())->merge($mobileCategories->get('', collect())), 
                    'ancestors' => []
                ])
            </div>
        </div>

        {{-- Account & Support --}}
        <div class="roh-drawer-group roh-drawer-group--footer">
            <span class="roh-drawer-heading">{{ $mobileLabel('ACCOUNT & SUPPORT', 'অ্যাকাউন্ট ও সেবা') }}</span>
            <ul class="roh-drawer-nav-list">
                <li>
                    <a href="{{ route('order.tracking') }}" class="roh-drawer-link">
                        <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                        <span>{{ $mobileLabel('Track Order', 'অর্ডার ট্র্যাক করুন') }}</span>
                        <i class="fa-solid fa-angle-right roh-drawer-link__arrow" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route($user ? 'dashboard' : 'login') }}" class="roh-drawer-link">
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                        <span>{{ $user ? $mobileLabel('My Account (' . $user->name . ')', 'আমার অ্যাকাউন্ট (' . $user->name . ')') : $mobileLabel('Login / Register', 'লগইন / রেজিস্টার') }}</span>
                        <i class="fa-solid fa-angle-right roh-drawer-link__arrow" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('page.contact') }}" class="roh-drawer-link">
                        <i class="fa-solid fa-headset" aria-hidden="true"></i>
                        <span>{{ $mobileLabel('Help & Contact', 'সাহায্য ও যোগাযোগ') }}</span>
                        <i class="fa-solid fa-angle-right roh-drawer-link__arrow" aria-hidden="true"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</dialog>
