@extends('FrontEnd.master')
@section('title')
Home
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('FrontEnd/assect/css/home.css') }}?v={{ filemtime(public_path('FrontEnd/assect/css/home.css')) }}">
<link rel="stylesheet" href="{{ asset('FrontEnd/assect/css/hero.css') }}?v={{ filemtime(public_path('FrontEnd/assect/css/hero.css')) }}">
@endpush

@section('content')

<section class="rf-hero-section">
    <div class="custom_container">
        <div class="rf-hero-section-grid">
            <div class="rf-categories-panel">
                <div class="rf-categories-header">
                    <i class="fas fa-bars"></i>
                    <span>
                        @if(session()->get('language') == 'bangla')
                        সকল ক্যাটাগরি
                        @else
                        All Categories
                        @endif
                    </span>
                </div>

                <ul class="rf-category-menu">
                    @foreach($categories as $category)
                    @if($category->type == 1)
                    <li class="rf-category-item">
                        <a href="{{route('product.category', $category->slug)}}" class="rf-category-link">
                            @if($category->icon)
                            <img loading="lazy" decoding="async" src="{{ asset($category->icon) }}" class="rf-site-category-icon" alt="">
                            @else
                            <i class="fas fa-th-large rf-category-placeholder" aria-hidden="true"></i>
                            @endif
                            <span>
                                @if(session()->get('language') == 'bangla')
                                {{$category->name_bn}}
                                @else
                                {{$category->name_en}}
                                @endif
                            </span>
                            @php $subcategories = $category->categories @endphp
                            @if(count($subcategories) > 0)
                            <i class="fas fa-chevron-right rf-category-arrow"></i>
                            @endif
                        </a>

                        @if(count($subcategories) > 0)
                        <div class="rf-subcategories">
                            @foreach($subcategories as $subcategory)
                            <div class="rf-subcategory-item">
                                <a href="{{route('product.category', $subcategory->slug)}}" class="rf-subcategory-link">
                                    @if(session()->get('language') == 'bangla')
                                    {{$subcategory->name_bn}}
                                    @else
                                    {{$subcategory->name_en}}
                                    @endif
                                </a>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>
            <div class="rf-main-slider">
                <div id="mainCarousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        @forelse ($sliders as $index => $slider)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <img loading="lazy" decoding="async" src="{{ asset($slider->slider_img) }}" alt="{{ session('language') == 'bangla' ? 'রোহ ফ্যাশন কালেকশন' : 'Roh Fashion collection' }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                        </div>
                        @empty
                        <div class="rf-slider-empty"><span>ROH FASHION</span>
                            <h2>{{ session('language') == 'bangla' ? 'খুঁজে নিন আপনার পছন্দের স্টাইল' : 'Find your everyday favourite.' }}</h2><a href="{{ route('category_list.index') }}">{{ session('language') == 'bangla' ? 'কালেকশন দেখুন' : 'Discover the collection' }}</a>
                        </div>
                        @endforelse
                    </div>
                    @if($sliders->count() > 1)
                    <button aria-label="Previous slide" class="carousel-control-prev" type="button" data-bs-target="#mainCarousel"
                        data-bs-slide="prev">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </button>
                    <button aria-label="Next slide" class="carousel-control-next" type="button" data-bs-target="#mainCarousel"
                        data-bs-slide="next">
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </button>
                    @endif
                </div>
            </div>

            <div class="rf-side-banners">
                @foreach ($home_banners->take(2) as $index => $banner)
                <a class="rf-promo-banner" href="{{ $banner->url ?: route('category_list.index') }}" aria-label="{{ session('language') == 'bangla' ? 'কালেকশন দেখুন' : 'Explore collection' }}">
                    <img loading="lazy" decoding="async" class="rf-promo-image" src="{{ asset($banner->banner_img) }}" alt="{{ session('language') == 'bangla' ? 'রোহ ফ্যাশন অফার' : 'Roh Fashion offer' }}" loading="lazy">
                    <span class="rf-promo-arrow" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

@if($campaign && $start_diff2->invert == 0 && $end_diff2->invert == 0)
<section class="custom_container rf-flash-section" aria-labelledby="flash-sale-title">

    <header class="rf-flash-heading">
        <div class="rf-flash-intro">
            <span class="rf-flash-eyebrow"><i class="fa-solid fa-bolt" aria-hidden="true"></i> {{ session('language') === 'bangla' ? 'সীমিত সময়ের অফার' : 'LIMITED-TIME OFFERS' }}</span>
            <h2 id="flash-sale-title">{{ session('language') === 'bangla' ? 'ফ্ল্যাশ সেল' : 'Flash Sale' }}<span aria-hidden="true">.</span></h2>
            <p>{{ session('language') === 'bangla' ? 'পছন্দের পণ্য, আরও দারুণ দামে।' : 'Your favourites. Even better prices.' }}</p>
        </div>
        <div class="rf-flash-countdown">
            <span class="rf-flash-timer-label" style="white-space: nowrap;">{{ session('language') === 'bangla' ? 'অফার শেষ হতে বাকি' : 'OFFER ENDS IN' }}</span>
            <div class="rf-flash-clock" data-countdown-start="{{ $campaignStart }}" data-countdown-end="{{ $campaignEnd }}" role="timer" aria-label="{{ session('language') === 'bangla' ? 'অফারের বাকি সময়' : 'Time remaining for this offer' }}">
                @foreach(['days' => ['Days', 'দিন'], 'hours' => ['Hrs', 'ঘণ্টা'], 'minutes' => ['Min', 'মিনিট'], 'seconds' => ['Sec', 'সেকেন্ড']] as $unit => $labels)
                <span class="rf-flash-time"><strong data-time="{{ $unit }}">00</strong><small>{{ session('language') === 'bangla' ? $labels[1] : $labels[0] }}</small></span>
                @endforeach
            </div>
        </div>
        <div class="rf-flash-navigation">
            <a href="{{ route('campaign.product') }}" class="rf-flash-link">{{ session('language') === 'bangla' ? 'সব অফার দেখুন' : 'Explore all offers' }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            <div data-flash-controls></div>
        </div>
    </header>
    <div class="flash-sale rf-home-rail rf-products-rail" data-home-rail>
        @foreach($campaign->campaing_products->take(6) as $campaignProduct)
        @if($campaignProduct->product && $campaignProduct->product->status == 1)
        <div class="rf-flash-slide">
            <x-product-card :product="$campaignProduct->product" />
        </div>
        @endif
        @endforeach
    </div>
</section>
@endif

@if(isset($product_featured) && $product_featured->count() > 0)
<section class="custom_container">
    <div class="section-header">
        <h2 class="section-title">
            @if(session()->get('language') == 'bangla')
            ফিচার্ড প্রোডাক্ট
            @else
            Featured Products
            @endif
        </h2>
        <a href="{{ route('product.show') }}" class="view-all-btn">
            @if(session()->get('language') == 'bangla')
            সব দেখুন
            @else
            View All
            @endif
        </a>
    </div>

    <div class="featured-slider rf-home-rail rf-products-rail" data-home-rail>
        @foreach($product_featured->take(8) as $product)
        <div class="rf-slide">
            <x-product-card :product="$product" />
        </div>
        @endforeach
    </div>
</section>
@endif

<section class="custom_container pt-4 roh-category-section">
    <div class="section-header">
        <h2 class="section-title">
            @if(session()->get('language') == 'bangla')
            ক্যাটাগোরি
            @else
            Categories
            @endif
        </h2>
        <a href="{{route('category_list.index')}}" class="view-all-btn">
            @if(session()->get('language') == 'bangla')
            সব দেখুন
            @else
            View All
            @endif
        </a>
    </div>

    <div class="roh-category-carousel">
    <div class="category-slider rf-home-rail" data-home-rail aria-label="Product categories">
        @foreach($featured_category as $fc)
        <div class="category-slide">
            <a href="{{ route('product.category', $fc->slug) }}" class="category-card">
                <span class="category-label">{{ session('language') === 'bangla' ? ($fc->name_bn ?: $fc->name_en) : $fc->name_en }}</span>
                <div class="category-icon">
                    <img loading="lazy" decoding="async" src="{{ asset($fc->image ?: 'upload/product-default.jpg') }}" alt="" width="100" height="100" onerror="this.onerror=null;this.src='{{ asset('upload/product-default.jpg') }}'">
                </div>
                <div class="category-content">
                    <h3 class="category-name">
                        @if(session()->get('language') == 'bangla')
                        {{ $fc->name_bn ?: $fc->name_en }}
                        @else
                        {{ $fc->name_en }}
                        @endif
                    </h3>
                    <span class="category-shop">{{ session('language') === 'bangla' ? 'কেনাকাটা করুন' : 'Shop Now' }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    </div>
</section>





<section class="custom_container mb-5">
    <div class="section-header">
        <h2 class="section-title">
            @if(session()->get('language') == 'bangla')
            সাম্প্রতিক পণ্যসমূহ
            @else
            Latest Products
            @endif
        </h2>

    </div>

    <div class="product-grid" id="latest-products-container">
        @foreach ($product_trendings->take(8) as $product_trending)

        <div class="">
            <x-product-card :product="$product_trending" />
        </div>
        @endforeach
    </div>

    <div class="text-center mt-4">
        <button type="button" id="load-more-btn" data-url="{{ url('load-more-products') }}" data-offset="{{ $product_trendings->count() }}" data-bangla="{{ session('language') === 'bangla' ? '1' : '0' }}" class="view-all-btn">
            @if(session()->get('language') == 'bangla')
            আরো দেখুন
            @else
            View More
            @endif
        </button>
    </div>
</section>

<section class="custom_container mb-5" aria-labelledby="roh-about-title">
    <div class="roh-about">
        <div class="roh-about__intro">
            <span class="roh-about__eyebrow">ROH FASHION</span>
            <h2 id="roh-about-title">{{ session('language') === 'bangla' ? 'প্রতিদিনের পছন্দে, নিজের ছোঁয়া।' : 'Everyday favourites. A touch of you.' }}</h2>
        </div>
        <div class="roh-about__content">
            <p>{{ session('language') === 'bangla' ? 'নিজের জন্য হোক কিংবা প্রিয় কারও জন্য—রোহ ফ্যাশনে খুঁজে নিন পছন্দের বিউটি ও লাইফস্টাইল পণ্য। সময় নিয়ে দেখুন, তুলনা করুন, আর বেছে নিন আপনার ভালো লাগার জিনিসটি।' : 'For yourself or someone you love, discover beauty and lifestyle finds at Roh Fashion. Take a look around, compare your favourites, and choose what feels right for you.' }}</p>
            <div class="roh-about__links">
                <a class="roh-about__shop" href="{{ route('product.show') }}">{{ session('language') === 'bangla' ? 'কেনাকাটা শুরু করুন' : 'Find your favourites' }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a class="roh-about__browse" href="{{ route('category_list.index') }}">{{ session('language') === 'bangla' ? 'ক্যাটাগরি দেখুন' : 'Browse categories' }}</a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('js')
<script src="{{ asset('FrontEnd/assect/js/home.js') }}?v={{ filemtime(public_path('FrontEnd/assect/js/home.js')) }}" defer></script>
@endpush
