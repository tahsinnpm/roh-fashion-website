<ul class="roh-category-tree">
    @foreach($branches as $category)
        @continue(in_array($category->id, $ancestors))
        @php
            $categoryName = session('language') === 'bangla' ? ($category->name_bn ?: $category->name_en) : $category->name_en;
            $children = $mobileCategories->get($category->id, collect());
        @endphp
        <li class="roh-category-item">
            @if($children->isNotEmpty())
                <details class="roh-category-details">
                    <summary class="roh-category-summary">
                        <span class="roh-category-name">{{ $categoryName }}</span>
                        <i class="fa-solid fa-chevron-right roh-category-arrow" aria-hidden="true"></i>
                    </summary>
                    <div class="roh-category-children">
                        <a class="roh-category-tree__all" href="{{ route('product.category', $category->slug) }}">
                            <span>{{ $mobileLabel('View all', 'সব দেখুন') }} — {{ $categoryName }}</span>
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                        @include('FrontEnd.partials.mobile-category-tree', [
                            'branches' => $children, 
                            'ancestors' => array_merge($ancestors, [$category->id])
                        ])
                    </div>
                </details>
            @else
                <a class="roh-category-link" href="{{ route('product.category', $category->slug) }}">
                    <span class="roh-category-name">{{ $categoryName }}</span>
                    <i class="fa-solid fa-angle-right roh-category-arrow" aria-hidden="true"></i>
                </a>
            @endif
        </li>
    @endforeach
</ul>
