<ul class="roh-category-tree">
    @foreach($branches as $category)
        @continue(in_array($category->id, $ancestors))
        @php
            $categoryName = session('language') === 'bangla' ? ($category->name_bn ?: $category->name_en) : $category->name_en;
            $children = $mobileCategories->get($category->id, collect());
        @endphp
        <li>
            @if($children->isNotEmpty())
                <details>
                    <summary>{{ $categoryName }}<i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary>
                    <a class="roh-category-tree__all" href="{{ route('product.category', $category->slug) }}">{{ $mobileLabel('View all', 'সব দেখুন') }} — {{ $categoryName }}</a>
                    @include('FrontEnd.partials.mobile-category-tree', ['branches' => $children, 'ancestors' => array_merge($ancestors, [$category->id])])
                </details>
            @else
                <a href="{{ route('product.category', $category->slug) }}">{{ $categoryName }}<i class="fa-solid fa-angle-right" aria-hidden="true"></i></a>
            @endif
        </li>
    @endforeach
</ul>
