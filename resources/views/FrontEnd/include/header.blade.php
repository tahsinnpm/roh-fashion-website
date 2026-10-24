@php
    $isBangla = session('language') === 'bangla';
    $user = Auth::user();
    $logoUrl = asset(get_setting('site_logo')->value ?: 'upload/setting/logo/1790046067Screenshot_1.png');
    $topbarSettings = \App\Models\Setting::whereIn('name', ['topbar_marquee_text', 'topbar_background_color', 'topbar_text_color'])->pluck('value', 'name');
    $topbarText = trim($topbarSettings['topbar_marquee_text'] ?? '');
    $topbarMessages = $topbarText === '' ? [] : array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $topbarText))));
    $topbarBgColor = $topbarSettings['topbar_background_color'] ?? '#C20000';
    $topbarTextColor = $topbarSettings['topbar_text_color'] ?? '#FFFFFF';
    $topbarBgColor = preg_match('/^#[0-9a-fA-F]{6}$/', $topbarBgColor) ? $topbarBgColor : '#C20000';
    $topbarTextColor = preg_match('/^#[0-9a-fA-F]{6}$/', $topbarTextColor) ? $topbarTextColor : '#FFFFFF';
@endphp

<header class="roh-header" data-roh-header>
    @if(count($topbarMessages))
        <div class="roh-topbar" role="region" aria-label="Announcements" data-topbar-slider style="--roh-topbar-background: {{ $topbarBgColor }}; --roh-topbar-text: {{ $topbarTextColor }};">
            <div class="roh-topbar__slider" aria-live="polite">
                @foreach($topbarMessages as $message)
                    <span class="roh-topbar__slide{{ $loop->first ? ' is-active' : '' }}">{{ trim($message) }}</span>
                @endforeach
            </div>
        </div>
    @endif
    <div class="roh-header__masthead">
        {{-- Mobile Menu (Hamburger) Button - Left on mobile --}}
         <button type="button" class="roh-header__menu-btn" data-category-open aria-controls="roh-category-drawer" aria-expanded="false" aria-label="{{ $isBangla ? 'মেনু' : 'Menu' }}">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        {{-- Brand Logo - Middle on mobile --}}
        <a class="roh-header__brand" href="{{ route('home') }}"
            aria-label="Shinduk Fashion — {{ $isBangla ? 'হোম' : 'Home' }}">
            <img src="{{ $logoUrl }}" alt="Shinduk Fashion" width="124" height="62" fetchpriority="high">
        </a>

        {{-- Search Form - toggled on mobile, left on desktop --}}
        <form class="roh-header__search" action="{{ route('product.search') }}" method="post" role="search"
            data-suggestions-url="{{ route('ajax.product.search') }}">
            @csrf
            <label class="visually-hidden"
                for="roh-search-input">{{ $isBangla ? 'আপনার পছন্দের পণ্য খুঁজুন' : 'Find something you’ll love' }}</label>
            <div class="roh-header__search-field">
                <input id="roh-search-input" type="search" name="search" required autocomplete="off"
                    aria-autocomplete="list" aria-controls="roh-search-suggestions" aria-expanded="false"
                    placeholder="{{ $isBangla ? 'পণ্য খুঁজুন…' : 'Search products…' }}">
                <button type="submit" aria-label="{{ $isBangla ? 'অনুসন্ধান' : 'Search' }}"><i
                        class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
            </div>
            <div class="roh-search-suggestions" id="roh-search-suggestions" role="listbox" hidden></div>
        </form>

        {{-- Actions - Right on mobile --}}
        <div class="roh-header__actions">
            {{-- Mobile Search Toggle Button --}}
            <button type="button" class="roh-header__search-toggle" data-search-toggle
                aria-label="{{ $isBangla ? 'অনুসন্ধান' : 'Search' }}">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            </button>

            <a class="roh-header__tracking" aria-label="{{ $isBangla ? 'অর্ডার ট্র্যাক' : 'Track order' }}"
                title="{{ $isBangla ? 'অর্ডার ট্র্যাক' : 'Track order' }}" href="{{ route('order.tracking') }}">
                <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                <span>{{ $isBangla ? 'অর্ডার ট্র্যাক' : 'Track order' }}</span>
            </a>
            <a class="roh-header__account" href="{{ route($user ? 'dashboard' : 'login') }}"
                aria-label="{{ $isBangla ? 'অ্যাকাউন্ট' : 'Account' }}">
                <span class="roh-header__icon">
                    @if($user && $user->profile_image)
                        <img src="{{ asset($user->profile_image) }}" alt="" width="24" height="24">
                    @else
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                    @endif
                </span>
            </a>
            <a class="roh-header__bag" href="{{ route('cart.show') }}" data-cart-open aria-controls="rf-cart-sidebar"
                aria-expanded="false" aria-label="{{ $isBangla ? 'শপিং ব্যাগ' : 'Shopping bag' }}">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                <span class="roh-header__count cartQty">{{ Cart::count() }}</span>
            </a>
        </div>
    </div>
</header>

@if(count($topbarMessages) > 1)
    @push('js')
        <script>
            (() => {
                const slider = document.querySelector('[data-topbar-slider]');
                if (!slider) return;
                const slides = [...slider.querySelectorAll('.roh-topbar__slide')];
                if (slides.length < 2) return;
                let active = 0;
                window.setInterval(() => {
                    slides[active].classList.remove('is-active');
                    active = (active + 1) % slides.length;
                    slides[active].classList.add('is-active');
                }, 2000);
            })();
        </script>
    @endpush
@endif

<nav class="roh-category-nav" aria-label="Main categories">
    <div class="roh-category-nav__inner">
        <a href="{{ route('home') }}"
           class="roh-category-nav__item roh-category-nav__home {{ request()->routeIs('home') ? 'is-active' : '' }}"
           aria-label="{{ $isBangla ? 'হোম' : 'Home' }}">
            <i class="fa-solid fa-house" aria-hidden="true"></i>
            <span>{{ $isBangla ? 'হোম' : 'Home' }}</span>
        </a>
  
        @foreach($categories as $category)
            <a href="{{ route('product.category', $category->slug) }}"
               class="roh-category-nav__item {{ (request()->routeIs('product.category') && request()->route('slug') === $category->slug) ? 'is-active' : '' }}">
                <span>{{ $isBangla && $category->name_bn ? $category->name_bn : $category->name_en }}</span>
            </a>
        @endforeach

              <a href="{{ route('product.show', ['filter' => 'new_arrivals']) }}"
           class="roh-category-nav__item {{ (request()->routeIs('product.show') && request('filter') === 'new_arrivals') ? 'is-active' : '' }}">
            <span>{{ $isBangla ? 'নতুন এসেছে' : 'New Arrivals' }}</span>
        </a>
        <a href="{{ route('product.show', ['filter' => 'best_sellers']) }}"
           class="roh-category-nav__item {{ (request()->routeIs('product.show') && request('filter') === 'best_sellers') ? 'is-active' : '' }}">
            <span>{{ $isBangla ? 'সেরা বিক্রিত' : 'Best Sellers' }}</span>
        </a>
    </div>
</nav>

<script>
    (() => {
        const header = document.querySelector('[data-roh-header]');
        const categoryNav = document.querySelector('.roh-category-nav');
        if (!header || !categoryNav) return;

        const syncCategoryNavOffset = () => {
            categoryNav.style.setProperty('--roh-header-height', `${header.getBoundingClientRect().height}px`);
        };

        syncCategoryNavOffset();
        if ('ResizeObserver' in window) {
            new ResizeObserver(syncCategoryNavOffset).observe(header);
        } else {
            window.addEventListener('resize', syncCategoryNavOffset, { passive: true });
        }
    })();
</script>

@push('js')
    <script>
        (() => {
            const form = document.querySelector('.roh-header__search');
            const input = document.getElementById('roh-search-input');
            const suggestions = document.getElementById('roh-search-suggestions');
            if (!form || !input || !suggestions) return;

            let debounceTimer;
            let activeRequest;
            const closeSuggestions = () => {
                suggestions.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            };

            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                if (activeRequest) activeRequest.abort();

                const query = input.value.trim();
                if (query.length < 2) {
                    suggestions.replaceChildren();
                    closeSuggestions();
                    return;
                }

                debounceTimer = setTimeout(async () => {
                    activeRequest = new AbortController();
                    const url = new URL(form.dataset.suggestionsUrl, window.location.origin);
                    url.searchParams.set('search', query);

                    try {
                        const response = await fetch(url, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            signal: activeRequest.signal
                        });
                        if (!response.ok) throw new Error('Search request failed');
                        const result = await response.json();
                        if (input.value.trim() !== query) return;
                        suggestions.innerHTML = result.html || '';
                        suggestions.hidden = !suggestions.childElementCount;
                        input.setAttribute('aria-expanded', String(!suggestions.hidden));
                    } catch (error) {
                        if (error.name !== 'AbortError') closeSuggestions();
                    }
                }, 220);
            });

            document.addEventListener('click', event => {
                if (!form.contains(event.target)) closeSuggestions();
            });
            input.addEventListener('keydown', event => {
                if (event.key === 'Escape') closeSuggestions();
            });
        })();
    </script>
@endpush
