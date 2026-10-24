@extends('FrontEnd.master')

@php
    $isBangla = session('language') === 'bangla';

    // Determine current page heading & active breadcrumb
    $pageTitle = $isBangla ? 'সমস্ত পণ্য' : 'All Products';
    if ($activeSection) {
        $pageTitle = $isBangla ? ($activeSection->title_bn ?: $activeSection->title) : $activeSection->title;
    } elseif ($activeCategory) {
        $pageTitle = $isBangla ? ($activeCategory->name_bn ?: $activeCategory->name_en) : $activeCategory->name_en;
    } elseif ($activeBrand) {
        $pageTitle = $isBangla ? ($activeBrand->name_bn ?: $activeBrand->name_en) : $activeBrand->name_en;
    } elseif (request('featured')) {
        $pageTitle = $isBangla ? 'ফিচার্ড পণ্যসমূহ' : 'Featured Products';
    } elseif (request('filter') === 'new_arrivals' || request('sort_by') === 'newest') {
        $pageTitle = $isBangla ? 'নতুন পণ্যসমূহ' : 'New Arrivals';
    } elseif (request('filter') === 'best_sellers' || request('sort_by') === 'best_sellers' || request('sort') === 'best_sellers') {
        $pageTitle = $isBangla ? 'সেরা বিক্রিত পণ্য' : 'Best Sellers';
    }

    $selectedCats = (array) request('category', []);
    $selectedBrands = (array) request('brand', []);
    $hasActiveFilters = request()->hasAny(['category', 'brand', 'section', 'min_price', 'max_price', 'stock', 'featured', 'search', 'q', 'filter']);
@endphp

@section('title')
    {{ $pageTitle }}
@endsection

@section('content')
    <style>
        /* ==========================================================================
       Shinduk Fashion — SHOP PAGE (LUXURY BLACK & WHITE THEME)
       ========================================================================== */
        .rf-shop-page {
            background-color: #fafafa;
            min-height: 100vh;
            padding-bottom: 60px;
            color: #111;
        }

        /* Breadcrumbs */
        .rf-shop-breadcrumb {
            background: #fff;
            border-bottom: 1px solid #ededed;
            padding: 12px 0;
            margin-bottom: 20px;
        }

        .rf-shop-breadcrumb__inner {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 13px;
            color: #666;
        }

        .rf-shop-breadcrumb a {
            color: #666;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .rf-shop-breadcrumb a:hover {
            color: #111;
        }

        .rf-shop-breadcrumb__sep {
            color: #bbb;
            font-size: 11px;
        }

        .rf-shop-breadcrumb__current {
            color: #111;
            font-weight: 600;
        }

        /* Simple Minimalist Top Header Bar */
        .rf-shop-topbar {
            background: transparent;
        }

        .rf-shop-topbar h1 {
            letter-spacing: -0.01em;
        }

        /* Sort & Filter Controls */
        .rf-shop-filter-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: #111;
            color: #fff;
            border: 0;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 4px;
            transition: opacity 0.2s ease;
        }

        .rf-shop-filter-toggle:hover {
            opacity: 0.9;
        }

        .rf-shop-sort-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .rf-shop-sort-select {
            padding: 7px 12px;
            border: 1px solid #e0e0e0;
            background: #fff;
            color: #111;
            font-size: 13px;
            font-weight: 500;
            outline: none;
            border-radius: 4px;
            cursor: pointer;
            transition: border-color 0.2s ease;
        }

        .rf-shop-sort-select:focus {
            border-color: #111;
        }

        /* Active Filter Badges */
        .rf-active-filters {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px 14px;
            background: #fff;
            border: 1px solid #ededed;
            border-radius: 4px;
        }

        .rf-active-filters__label {
            font-size: 11px;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-right: 4px;
        }

        .rf-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 9px;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            color: #18181b;
            font-size: 12px;
            border-radius: 3px;
            text-decoration: none;
            transition: background-color 0.15s ease;
        }

        .rf-filter-chip:hover {
            background: #e4e4e7;
            color: #000;
        }

        .rf-filter-chip i {
            font-size: 10px;
        }

        .rf-clear-all {
            color: #000000;
            font-size: 12px;
            font-weight: 600;
            text-decoration: underline;
            margin-left: auto;
        }

        .rf-clear-all:hover {
            color: #be123c;
        }

        /* Sidebar Collections & Filter Card */
        .rf-filter-card {
            background: #fff;
            border: 1px solid #ededed;
            padding: 20px;
            position: sticky;
            top: 20px;
            border-radius: 4px;
        }

        .rf-filter-group {
            padding-bottom: 18px;
            margin-bottom: 18px;
            border-bottom: 1px solid #f0f0f0;
        }

        .rf-filter-group:last-child {
            border-bottom: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .rf-filter-group__title {
            font-size: 12px;
            font-weight: 700;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 12px;
        }

        /* Sidebar Collection Buttons */
        .rf-collection-links {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .rf-collection-btn {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 11px;
            font-size: 12.5px;
            font-weight: 500;
            color: #333;
            background: #f9f9f9;
            border: 1px solid #eeeeee;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.18s ease;
        }

        .rf-collection-btn i {
            font-size: 11px;
            color: #777;
            width: 14px;
            text-align: center;
        }

        .rf-collection-btn:hover {
            background: #111;
            border-color: #111;
            color: #fff;
        }

        .rf-collection-btn:hover i {
            color: #fff;
        }

        .rf-collection-btn.is-active {
            background: #111;
            border-color: #111;
            color: #fff;
            font-weight: 600;
        }

        .rf-collection-btn.is-active i {
            color: #fff;
        }

        /* Search Box in Filter */
        .rf-filter-search-box {
            position: relative;
        }

        .rf-filter-search-box input {
            width: 100%;
            padding: 8px 36px 8px 10px;
            border: 1px solid #e0e0e0;
            font-size: 12.5px;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.2s;
        }

        .rf-filter-search-box input:focus {
            border-color: #111;
        }

        .rf-filter-search-box button {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 36px;
            border: 0;
            background: transparent;
            color: #555;
            cursor: pointer;
        }

        /* Filter List & Checkboxes */
        .rf-filter-list {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 240px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .rf-filter-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 12.5px;
            color: #333;
        }

        .rf-filter-item label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            flex: 1;
            user-select: none;
        }

        .rf-filter-item input[type="checkbox"] {
            accent-color: #111;
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        .rf-filter-badge {
            color: #888;
            font-size: 11px;
        }

        /* Price Inputs */
        .rf-price-inputs {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rf-price-input {
            width: 100%;
            padding: 7px 9px;
            border: 1px solid #e0e0e0;
            font-size: 12.5px;
            border-radius: 4px;
            outline: none;
        }

        .rf-price-input:focus {
            border-color: #111;
        }

        /* Filter Submit Buttons */
        .rf-filter-actions {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-top: 18px;
        }

        .rf-btn-apply {
            width: 100%;
            padding: 9px;
            background: #111;
            color: #fff;
            border: 1px solid #111;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .rf-btn-apply:hover {
            background: #333;
            border-color: #333;
        }

        .rf-btn-reset {
            width: 100%;
            padding: 8px;
            background: #fff;
            color: #666;
            border: 1px solid #e0e0e0;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .rf-btn-reset:hover {
            background: #f5f5f5;
            color: #111;
        }

        /* Empty State */
        .rf-empty-state {
            background: #fff;
            border: 1px solid #ededed;
            padding: 60px 20px;
            border-radius: 4px;
            text-align: center;
        }

        .rf-empty-state__icon {
            font-size: 44px;
            color: #bbb;
            margin-bottom: 14px;
        }

        .rf-empty-state__title {
            font-size: 17px;
            font-weight: 600;
            color: #111;
            margin-bottom: 6px;
        }

        .rf-empty-state__desc {
            color: #777;
            font-size: 13.5px;
            max-width: 420px;
            margin: 0 auto 20px;
        }
    </style>

    <div class="rf-shop-page">
        {{-- Breadcrumbs --}}
        <nav class="rf-shop-breadcrumb" aria-label="Breadcrumb">
            <div class="container rf-shop-breadcrumb__inner">
                <a href="{{ route('home') }}"><i class="fa-solid fa-house" style="font-size: 11px;"></i>
                    {{ $isBangla ? 'হোম' : 'Home' }}</a>
                <span class="rf-shop-breadcrumb__sep">/</span>
                <a href="{{ route('product.show') }}">{{ $isBangla ? 'শপ' : 'Shop' }}</a>
                @if($pageTitle !== ($isBangla ? 'সমস্ত পণ্য' : 'All Products'))
                    <span class="rf-shop-breadcrumb__sep">/</span>
                    <span class="rf-shop-breadcrumb__current">{{ $pageTitle }}</span>
                @endif
            </div>
        </nav>

        <div class="container">
            {{-- Simple Sleek Header Bar --}}
            <div
                class="rf-shop-topbar d-flex align-items-center justify-content-between flex-wrap gap-2 pb-3 mb-3 border-bottom">
                <div>
                    <h1 class="h4 fw-bold text-dark m-0 d-inline-block align-middle me-2">{{ $pageTitle }}</h1>
                    <span class="text-muted small align-middle">
                        @if($isBangla)
                            ({{ $products->total() }} টি পণ্য)
                        @else
                            ({{ $products->total() }} items)
                        @endif
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="rf-shop-filter-toggle d-lg-none" data-bs-toggle="offcanvas"
                        data-bs-target="#rfShopFilterDrawer" aria-controls="rfShopFilterDrawer">
                        <i class="fa-solid fa-sliders"></i>
                        <span>{{ $isBangla ? 'ফিল্টার' : 'Filters' }}</span>
                        @if($hasActiveFilters)
                            <span class="badge bg-danger rounded-pill ms-1">•</span>
                        @endif
                    </button>

                    <div class="rf-shop-sort-wrap">
                        <label for="shop-sort-select"
                            class="d-none d-sm-inline small text-muted">{{ $isBangla ? 'সাজান:' : 'Sort by:' }}</label>
                        <select id="shop-sort-select" class="rf-shop-sort-select" onchange="handleSortChange(this.value)">
                            <option value="newest" {{ $sort_by === 'newest' && request('filter') !== 'best_sellers' ? 'selected' : '' }}>{{ $isBangla ? 'নতুন পণ্য আগে' : 'New Arrivals' }}</option>
                            <option value="best_sellers" {{ $sort_by === 'best_sellers' || request('filter') === 'best_sellers' ? 'selected' : '' }}>{{ $isBangla ? 'সেরা বিক্রিত' : 'Best Sellers' }}</option>
                            <option value="price-asc" {{ $sort_by === 'price-asc' ? 'selected' : '' }}>
                                {{ $isBangla ? 'দাম: কম থেকে বেশি' : 'Price: Low to High' }}</option>
                            <option value="price-desc" {{ $sort_by === 'price-desc' ? 'selected' : '' }}>
                                {{ $isBangla ? 'দাম: বেশি থেকে কম' : 'Price: High to Low' }}</option>
                            <option value="name-asc" {{ $sort_by === 'name-asc' ? 'selected' : '' }}>
                                {{ $isBangla ? 'নাম: A থেকে Z' : 'Name: A to Z' }}</option>
                            <option value="oldest" {{ $sort_by === 'oldest' ? 'selected' : '' }}>
                                {{ $isBangla ? 'পুরাতন পণ্য আগে' : 'Oldest' }}</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Active Filters Chip Bar --}}
            @if($hasActiveFilters)
                <div class="rf-active-filters mb-3">
                    <span class="rf-active-filters__label">{{ $isBangla ? 'সক্রিয় ফিল্টার:' : 'Active Filters:' }}</span>

                    @if(request('section') && $activeSection)
                        <a href="{{ request()->fullUrlWithoutQuery(['section']) }}" class="rf-filter-chip">
                            <span>{{ $isBangla ? 'কালেকশন: ' : 'Section: ' }}
                                {{ $isBangla ? ($activeSection->title_bn ?: $activeSection->title) : $activeSection->title }}</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif

                    @if(request('filter') === 'new_arrivals')
                        <a href="{{ request()->fullUrlWithoutQuery(['filter']) }}" class="rf-filter-chip">
                            <span>{{ $isBangla ? 'নতুন এসেছে' : 'New Arrivals' }}</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @elseif(request('filter') === 'best_sellers')
                        <a href="{{ request()->fullUrlWithoutQuery(['filter']) }}" class="rf-filter-chip">
                            <span>{{ $isBangla ? 'সেরা বিক্রিত' : 'Best Sellers' }}</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif

                    @if(request('featured'))
                        <a href="{{ request()->fullUrlWithoutQuery(['featured']) }}" class="rf-filter-chip">
                            <span>{{ $isBangla ? 'ফিচার্ড' : 'Featured' }}</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif

                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithoutQuery(['search', 'q']) }}" class="rf-filter-chip">
                            <span>{{ $isBangla ? 'অনুসন্ধান: ' : 'Search: ' }} "{{ request('search') }}"</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif

                    @foreach($selectedCats as $catSlug)
                        @php $catObj = $categories->firstWhere('slug', $catSlug) ?: $categories->firstWhere('name_en', $catSlug); @endphp
                        <a href="{{ request()->fullUrlWithQuery(['category' => array_values(array_diff($selectedCats, [$catSlug]))]) }}"
                            class="rf-filter-chip">
                            <span>{{ $isBangla && $catObj ? ($catObj->name_bn ?: $catObj->name_en) : ($catObj ? $catObj->name_en : $catSlug) }}</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endforeach

                    @foreach($selectedBrands as $bSlug)
                        @php $bObj = $brands->firstWhere('slug', $bSlug) ?: $brands->firstWhere('name_en', $bSlug); @endphp
                        <a href="{{ request()->fullUrlWithQuery(['brand' => array_values(array_diff($selectedBrands, [$bSlug]))]) }}"
                            class="rf-filter-chip">
                            <span>{{ $bObj ? $bObj->name_en : $bSlug }}</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endforeach

                    @if(request('stock'))
                        <a href="{{ request()->fullUrlWithoutQuery(['stock']) }}" class="rf-filter-chip">
                            <span>{{ $isBangla ? 'স্টকে আছে' : 'In Stock' }}</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif

                    @if(request('min_price') || request('max_price'))
                        <a href="{{ request()->fullUrlWithoutQuery(['min_price', 'max_price']) }}" class="rf-filter-chip">
                            <span>Tk {{ request('min_price', 0) }} - Tk {{ request('max_price', $catalogMaxPrice) }}</span>
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif

                    <a href="{{ route('product.show') }}" class="rf-clear-all">{{ $isBangla ? 'সব মুছুন' : 'Clear All' }}</a>
                </div>
            @endif

            {{-- Main Content Grid & Sidebar --}}
            <div class="row">
                {{-- Desktop Sidebar --}}
                <aside class="col-lg-3 d-none d-lg-block">
                    @include('FrontEnd.product.partials.shop_filter_form')
                </aside>

                {{-- Products Grid Column --}}
                <main class="col-lg-9">
                    @if($products->count() > 0)
                        <div id="product-container" class="product-grid">
                            @foreach ($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>

                        <div class="rf-pagination-wrap">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="rf-empty-state">
                            <div class="rf-empty-state__icon"><i class="fa-solid fa-box-open"></i></div>
                            <h2 class="rf-empty-state__title">
                                {{ $isBangla ? 'কোনো পণ্য পাওয়া যায়নি' : 'No products found' }}
                            </h2>
                            <p class="rf-empty-state__desc">
                                {{ $isBangla ? 'আপনার নির্বাচিত ফিল্টারের সাথে মিলে এমন কোনো পণ্য নেই। ফিল্টার পরিবর্তন করে আবার চেষ্টা করুন।' : 'We couldn\'t find any products matching your selected criteria. Try adjusting your filters.' }}
                            </p>
                            <a href="{{ route('product.show') }}" class="btn btn-dark px-4 py-2">
                                {{ $isBangla ? 'সমস্ত পণ্য দেখুন' : 'View All Products' }}
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>

    {{-- Mobile Filter Drawer (Offcanvas) --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="rfShopFilterDrawer" aria-labelledby="rfShopFilterDrawerLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="rfShopFilterDrawerLabel">{{ $isBangla ? 'ফিল্টার' : 'Filters' }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('FrontEnd.product.partials.shop_filter_form', ['isMobile' => true])
        </div>
    </div>

@endsection

@push('js')
    <script>
        function handleSortChange(sortVal) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', sortVal);
            url.searchParams.delete('filter'); // Clear filter preset if manually changing sort
            url.searchParams.delete('page'); // Reset to page 1 on sort change
            window.location.href = url.toString();
        }
    </script>
@endpush