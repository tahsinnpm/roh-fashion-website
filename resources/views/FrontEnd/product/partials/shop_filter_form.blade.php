@php
    $formIsBangla = session('language') === 'bangla';
    $selectedCategories = (array) request('category', []);
    $selectedBrandsList = (array) request('brand', []);
@endphp

<div class="rf-filter-card {{ isset($isMobile) && $isMobile ? 'border-0 p-0' : '' }}">
    {{-- Collections & Section Shortcuts (Moved into Sidebar) --}}
    <div class="rf-filter-group">
        <h4 class="rf-filter-group__title">{{ $formIsBangla ? 'কালেকশন ও ফিচার' : 'Collections' }}</h4>
        <div class="rf-collection-links">
            <a href="{{ route('product.show') }}" class="rf-collection-btn {{ !request('section') && !request('featured') && !request('filter') && !request('sort_by') && !request('search') && empty($selectedCategories) && empty($selectedBrandsList) ? 'is-active' : '' }}">
                <i class="fa-solid fa-border-all"></i>
                <span>{{ $formIsBangla ? 'সকল পণ্য' : 'All Products' }}</span>
            </a>
            <a href="{{ route('product.show', ['filter' => 'new_arrivals']) }}" class="rf-collection-btn {{ request('filter') === 'new_arrivals' || request('sort_by') === 'newest' ? 'is-active' : '' }}">
                <i class="fa-solid fa-bolt"></i>
                <span>{{ $formIsBangla ? 'নতুন এসেছে' : 'New Arrivals' }}</span>
            </a>
            <a href="{{ route('product.show', ['filter' => 'best_sellers']) }}" class="rf-collection-btn {{ request('filter') === 'best_sellers' || request('sort_by') === 'best_sellers' || request('sort') === 'best_sellers' ? 'is-active' : '' }}">
                <i class="fa-solid fa-fire"></i>
                <span>{{ $formIsBangla ? 'সেরা বিক্রিত' : 'Best Sellers' }}</span>
            </a>
            <a href="{{ route('product.show', ['featured' => 1]) }}" class="rf-collection-btn {{ request('featured') ? 'is-active' : '' }}">
                <i class="fa-solid fa-star"></i>
                <span>{{ $formIsBangla ? 'ফিচার্ড পণ্য' : 'Featured' }}</span>
            </a>
            @if(isset($dynamicSections) && $dynamicSections->count() > 0)
                @foreach($dynamicSections as $sec)
                    <a href="{{ route('product.show', ['section' => $sec->slug]) }}" class="rf-collection-btn {{ request('section') == $sec->slug ? 'is-active' : '' }}">
                        <i class="fa-solid fa-tag"></i>
                        <span>{{ $formIsBangla ? ($sec->title_bn ?: $sec->title) : $sec->title }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <form action="{{ route('product.show') }}" method="GET" id="{{ isset($isMobile) && $isMobile ? 'rf-mobile-filter-form' : 'rf-desktop-filter-form' }}">
        {{-- Preserve active filters when submitting search/price/category form --}}
        @if(request('section'))
            <input type="hidden" name="section" value="{{ request('section') }}">
        @endif
        @if(request('featured'))
            <input type="hidden" name="featured" value="{{ request('featured') }}">
        @endif
        @if(request('filter'))
            <input type="hidden" name="filter" value="{{ request('filter') }}">
        @endif
        @if(request('sort_by'))
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
        @endif

        {{-- Search in Shop --}}
        <div class="rf-filter-group">
            <h4 class="rf-filter-group__title">{{ $formIsBangla ? 'পণ্য খুঁজুন' : 'Search' }}</h4>
            <div class="rf-filter-search-box">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ $formIsBangla ? 'নাম দিয়ে খুঁজুন...' : 'Search in shop...' }}">
                <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>

        {{-- Categories --}}
        @if($categories && $categories->count() > 0)
        <div class="rf-filter-group">
            <h4 class="rf-filter-group__title">{{ $formIsBangla ? 'ক্যাটাগরি' : 'Categories' }}</h4>
            <ul class="rf-filter-list">
                @foreach($categories as $category)
                    @php
                        $isCatChecked = in_array($category->slug, $selectedCategories) || in_array($category->name_en, $selectedCategories) || in_array((string)$category->id, $selectedCategories);
                    @endphp
                    <li class="rf-filter-item">
                        <label for="{{ (isset($isMobile) && $isMobile ? 'm_' : '') }}cat_{{ $category->id }}">
                            <input type="checkbox" 
                                   id="{{ (isset($isMobile) && $isMobile ? 'm_' : '') }}cat_{{ $category->id }}" 
                                   name="category[]" 
                                   value="{{ $category->slug }}" 
                                   {{ $isCatChecked ? 'checked' : '' }}>
                            <span>{{ $formIsBangla ? ($category->name_bn ?: $category->name_en) : $category->name_en }}</span>
                        </label>
                        <span class="rf-filter-badge">({{ $category->products_count }})</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Price Filter --}}
        <div class="rf-filter-group">
            <h4 class="rf-filter-group__title">{{ $formIsBangla ? 'মূল্য সীমা (৳)' : 'Price Range (Tk)' }}</h4>
            <div class="rf-price-inputs">
                <input type="number" name="min_price" class="rf-price-input" placeholder="Min" min="0" value="{{ request('min_price') }}">
                <span>-</span>
                <input type="number" name="max_price" class="rf-price-input" placeholder="Max" min="0" value="{{ request('max_price') }}">
            </div>
        </div>

        {{-- Stock Availability --}}
        <div class="rf-filter-group">
            <h4 class="rf-filter-group__title">{{ $formIsBangla ? 'স্টক' : 'Availability' }}</h4>
            <div class="rf-filter-item">
                <label for="{{ (isset($isMobile) && $isMobile ? 'm_' : '') }}stock_check">
                    <input type="checkbox" 
                           id="{{ (isset($isMobile) && $isMobile ? 'm_' : '') }}stock_check" 
                           name="stock" 
                           value="1" 
                           {{ request('stock') ? 'checked' : '' }}>
                    <span>{{ $formIsBangla ? 'শুধুমাত্র স্টকে আছে' : 'In Stock Only' }}</span>
                </label>
                <span class="rf-filter-badge">({{ $inStockCount }})</span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="rf-filter-actions">
            <button type="submit" class="rf-btn-apply">{{ $formIsBangla ? 'ফিল্টার প্রয়োগ করুন' : 'Apply Filters' }}</button>
            <a href="{{ route('product.show') }}" class="rf-btn-reset">{{ $formIsBangla ? 'রিসেট করুন' : 'Reset All' }}</a>
        </div>
    </form>
</div>
