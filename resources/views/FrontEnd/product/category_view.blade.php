@extends('FrontEnd.master')
@section('title')
    {{ session('language') === 'bangla' ? ($category->name_bn ?: $category->name_en) : $category->name_en }}
@endsection

@section('content')
<div class="category-page-wrapper">
    <div class="category-breadcrumb-wrap py-3 border-bottom mb-4">
        <div class="container d-flex align-items-center flex-wrap gap-2 text-muted small">
            <a href="{{ route('home') }}" class="text-decoration-none text-secondary">{{ session('language') === 'bangla' ? 'হোম' : 'Home' }}</a>
            <span>/</span>
            <a href="{{ route('category_list.index') }}" class="text-decoration-none text-secondary">{{ session('language') === 'bangla' ? 'সব ক্যাটাগরি' : 'All Categories' }}</a>
            <span>/</span>
            <span class="text-dark fw-semibold">{{ session('language') === 'bangla' ? ($category->name_bn ?: $category->name_en) : $category->name_en }}</span>
        </div>
    </div>

    @if($subcategories && $subcategories->count() > 0)
    <section class="container mb-4">
        <div class="owl-carousel owl-theme category-subcat-carousel">
            @foreach($subcategories as $subcategory)
                <div class="item">
                    <a class="d-flex align-items-center p-2 text-decoration-none text-dark border bg-white rounded shadow-sm" href="{{ route('product.category', $subcategory->slug) }}">
                        @if($subcategory->image)
                            <img src="{{ asset($subcategory->image) }}" class="me-2 rounded" alt="{{ $subcategory->name_en }}" style="width: 44px; height: 44px; object-fit: cover;">
                        @endif
                        <span class="my-auto fw-medium small">
                            {{ session('language') === 'bangla' ? ($subcategory->name_bn ?: $subcategory->name_en) : $subcategory->name_en }}
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <section class="container mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
            <div>
                <h1 class="h4 fw-bold text-dark mb-1">
                    {{ session('language') === 'bangla' ? ($category->name_bn ?: $category->name_en) : $category->name_en }}
                </h1>
                <p class="text-muted small mb-0">
                    {{ session('language') === 'bangla' ? 'মোট '.$products->total().' টি পণ্য পাওয়া গেছে' : $products->total().' products found' }}
                </p>
            </div>
        </div>

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
            <div class="text-center py-5 my-5">
                <i class="fa-solid fa-box-open fa-3x text-muted mb-3 d-block"></i>
                <h4 class="text-secondary fw-normal">
                    {{ session('language') === 'bangla' ? 'এই ক্যাটাগরিতে কোনো পণ্য পাওয়া যায়নি।' : 'No products found in this category.' }}
                </h4>
                <a href="{{ route('home') }}" class="btn btn-dark mt-3 px-4 py-2">
                    {{ session('language') === 'bangla' ? 'কেনাকাটা চালিয়ে যান' : 'Continue Shopping' }}
                </a>
            </div>
        @endif
    </section>
</div>
@endsection

@push('js')
@if($subcategories && $subcategories->count() > 0)
<script>
    $(document).ready(function() {
        if (typeof $.fn.owlCarousel === 'function') {
            $('.category-subcat-carousel').owlCarousel({
                loop: false,
                margin: 12,
                nav: false,
                dots: false,
                responsive: {
                    0: { items: 2 },
                    600: { items: 3 },
                    1000: { items: 5 }
                }
            });
        }
    });
</script>
@endif
@endpush
