@extends('FrontEnd.master')
@section('title')
    Home
@endsection

@push('styles')
    <link rel="stylesheet"
        href="{{ asset('FrontEnd/assect/css/home.css') }}?v={{ filemtime(public_path('FrontEnd/assect/css/home.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('FrontEnd/assect/css/hero.css') }}?v={{ filemtime(public_path('FrontEnd/assect/css/hero.css')) }}">
@endpush

@section('content')

<section class="rf-hero-section">
    <div class="rf-hero-section-grid">
        <div class="rf-main-slider">
            @if($sliders->isNotEmpty())
                <div class="rf-hero-track" data-hero-track>
                    @foreach($sliders as $sliderIndex => $heroBanner)
                        <div class="rf-hero-slide {{ $loop->first ? 'is-active' : '' }}" data-hero-slide
                            aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                            <picture>
                                <source media="(max-width: 767px)" srcset="{{ asset($heroBanner->slider_img_mobile ?: $heroBanner->slider_img) }}">
                                <img src="{{ asset($heroBanner->slider_img) }}"
                                    alt="Shinduk Fashion collection banner {{ $sliderIndex + 1 }}"
                                    fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                                    {{ $loop->first ? '' : 'loading=lazy' }}>
                            </picture>
                        </div>
                    @endforeach
                </div>
                @if($sliders->count() > 1)
                    <button type="button" class="rf-hero-arrow rf-hero-arrow--prev" data-hero-prev
                        aria-label="{{ session('language') === 'bangla' ? 'আগের স্লাইড' : 'Previous slide' }}">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="rf-hero-arrow rf-hero-arrow--next" data-hero-next
                        aria-label="{{ session('language') === 'bangla' ? 'পরের স্লাইড' : 'Next slide' }}">
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </button>
                    <div class="rf-hero-dots" role="group"
                        aria-label="{{ session('language') === 'bangla' ? 'ব্যানার স্লাইড নির্বাচন করুন' : 'Choose banner slide' }}">
                        @foreach($sliders as $sliderIndex => $slider)
                            <button type="button" class="rf-hero-dot {{ $loop->first ? 'is-active' : '' }}"
                                data-hero-dot="{{ $sliderIndex }}"
                                aria-label="{{ session('language') === 'bangla' ? 'স্লাইড' : 'Slide' }} {{ $sliderIndex + 1 }}"
                                aria-pressed="{{ $loop->first ? 'true' : 'false' }}"></button>
                        @endforeach
                    </div>
                @endif
            @else
            <div class="rf-slider-empty">
                <span>Shinduk Fashion</span>
                <h2>Find your everyday favourite.</h2>
                <a href="{{ route('category_list.index') }}">Discover the collection</a>
            </div>
            @endif
        </div>
    </div>
</section>
{{-- Flash Sale temporarily hidden.
@if($campaign && $start_diff2->invert == 0 && $end_diff2->invert == 0)
<section class="custom_container rf-flash-section" aria-labelledby="flash-sale-title">

    <header class="rf-flash-heading">
        <div class="rf-flash-intro">
            <span class="rf-flash-eyebrow"><i class="fa-solid fa-bolt" aria-hidden="true"></i> {{ session('language')
                === 'bangla' ? 'সীমিত সময়ের অফার' : 'LIMITED-TIME OFFERS' }}</span>
            <h2 id="flash-sale-title">{{ session('language') === 'bangla' ? 'ফ্ল্যাশ সেল' : 'Flash Sale' }}<span
                    aria-hidden="true">.</span></h2>
            <p>{{ session('language') === 'bangla' ? 'পছন্দের পণ্য, আরও দারুণ দামে।' : 'Your favourites. Even better
                prices.' }}</p>
        </div>
        <div class="rf-flash-countdown">
            <span class="rf-flash-timer-label" style="white-space: nowrap;">{{ session('language') === 'bangla' ? 'অফার
                শেষ হতে বাকি' : 'OFFER ENDS IN' }}</span>
            <div class="rf-flash-clock" data-countdown-start="{{ $campaignStart }}"
                data-countdown-end="{{ $campaignEnd }}" role="timer"
                aria-label="{{ session('language') === 'bangla' ? 'অফারের বাকি সময়' : 'Time remaining for this offer' }}">
                @foreach(['days' => ['Days', 'দিন'], 'hours' => ['Hrs', 'ঘণ্টা'], 'minutes' => ['Min', 'মিনিট'],
                'seconds' => ['Sec', 'সেকেন্ড']] as $unit => $labels)
                <span class="rf-flash-time"><strong data-time="{{ $unit }}">00</strong><small>{{ session('language') ===
                        'bangla' ? $labels[1] : $labels[0] }}</small></span>
                @endforeach
            </div>
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
    <div class="rf-home-section-footer">
        <a href="{{ route('campaign.product') }}" class="rf-home-view-all">{{ session('language') === 'bangla' ? 'সব
            অফার দেখুন' : 'View All' }}</a>
        <div data-flash-controls></div>
    </div>
</section>
@endif
--}}

<section class="custom_container pt-4 roh-category-section rf-home-product-section">
    <div class="section-header">
        <h2 class="section-title">
            @if(session()->get('language') == 'bangla')
                ক্যাটাগোরি
            @else
                Popular Categories
            @endif
        </h2>
        <div class="rf-home-section-footer"><a href="{{ route('category_list.index') }}"
                class="rf-home-view-all">{{ session('language') === 'bangla' ? 'সব দেখুন' : 'View All' }}</a></div>
    </div>

    <div class="roh-category-carousel">
        <div class="category-slider rf-home-rail" data-home-rail aria-label="Product categories">
            @foreach($featured_category as $fc)
                <div class="category-slide">
                    <a href="{{ route('product.category', $fc->slug) }}" class="category-card">
                        <img class="category-card-image" loading="lazy" decoding="async"
                            src="{{ asset($fc->image ?: 'upload/product-default.jpg') }}" alt=""
                            onerror="this.onerror=null;this.src='{{ asset('upload/product-default.jpg') }}'">
                        <div class="category-content">
                            <h3 class="category-name">
                                @if(session()->get('language') == 'bangla')
                                    {{ $fc->name_bn ?: $fc->name_en }}
                                @else
                                    {{ $fc->name_en }}
                                @endif
                            </h3>
                            <span class="category-shop">Discover</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>


</section>


@if(isset($product_featured) && $product_featured->count() > 0)
    <section class="custom_container rf-home-product-section">
        <div class="section-header">
            <h2 class="section-title">
                @if(session()->get('language') == 'bangla')
                    ফিচার্ড প্রোডাক্ট
                @else
                    Featured Products
                @endif
            </h2>

            <div class="rf-home-section-footer"><a href="{{ route('product.show', ['featured' => 1]) }}"
                    class="rf-home-view-all">{{ session('language') === 'bangla' ? 'সব দেখুন' : 'View All' }}</a></div>
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


{{-- Dynamic Sections (Configured from Admin with Custom Position & Product Ordering) --}}
@if(isset($dynamic_sections) && $dynamic_sections->count() > 0)
    @foreach($dynamic_sections as $section)
        @if($section->products && $section->products->count() > 0)
            <section class="custom_container rf-home-product-section dynamic-section mb-4" id="section-{{ $section->slug }}">
                <div class="section-header">
                    <h2 class="section-title">
                        @if(session()->get('language') == 'bangla')
                            {{ $section->title_bn ?: $section->title }}
                        @else
                            {{ $section->title }}
                        @endif
                    </h2>
                    <div class="rf-home-section-footer">
                        <a href="{{ route('product.show', ['section' => $section->slug]) }}" class="rf-home-view-all">
                            {{ session('language') === 'bangla' ? 'সব দেখুন' : 'View All' }}
                        </a>
                    </div>
                </div>

                <div class="rf-home-rail rf-products-rail" data-home-rail>
                    @foreach($section->products as $product)
                        <div class="rf-slide">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>

            </section>
        @endif
    @endforeach
@endif
<section class="custom_container mb-5 rf-home-product-section">
    <div class="section-header">
        <h2 class="section-title">
            @if(session()->get('language') == 'bangla')
                সাম্প্রতিক পণ্যসমূহ
            @else
                Latest Products
            @endif
        </h2>
        <div class="rf-home-section-footer">
            <a href="{{ route('product.show', ['sort_by' => 'newest']) }}"
                class="view-all-btn rf-home-view-all text-decoration-none">{{ session('language') === 'bangla' ? 'আরো দেখুন' : 'View More' }}</a>
        </div>
    </div>

    <div class="product-grid" id="latest-products-container">
        @foreach ($product_trendings->take(8) as $product_trending)
            <div>
                <x-product-card :product="$product_trending" />
            </div>
        @endforeach
    </div>
</section>

<section class="custom_container mb-5 " aria-labelledby="roh-about-title">
    <div class="roh-about">
        <div class="roh-about__intro">
            <span class="roh-about__eyebrow">Shinduk Fashion</span>
            <h2 id="roh-about-title">
                {{ session('language') === 'bangla' ? 'প্রতিদিনের পছন্দে, নিজের ছোঁয়া।' : 'Everyday favourites. A touch of you.' }}
            </h2>
        </div>
        <div class="roh-about__content">
            <p>{{ session('language') === 'bangla' ? 'নিজের জন্য হোক কিংবা প্রিয় কারও জন্য—রোহ ফ্যাশনে খুঁজে নিন পছন্দের বিউটি ও লাইফস্টাইল পণ্য। সময় নিয়ে দেখুন, তুলনা করুন, আর বেছে নিন আপনার ভালো লাগার জিনিসটি।' : 'For yourself or someone you love, discover beauty and lifestyle finds at Roh Fashion. Take a look around, compare your favourites, and choose what feels right for you.' }}
            </p>
            <div class="roh-about__links">
                <a class="roh-about__shop"
                    href="{{ route('product.show') }}">{{ session('language') === 'bangla' ? 'কেনাকাটা শুরু করুন' : 'Find your favourites' }}
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a class="roh-about__browse"
                    href="{{ route('category_list.index') }}">{{ session('language') === 'bangla' ? 'ক্যাটাগরি দেখুন' : 'Browse categories' }}</a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('js')
    <script src="{{ asset('FrontEnd/assect/js/home.js') }}?v={{ filemtime(public_path('FrontEnd/assect/js/home.js')) }}"
        defer></script>
@endpush
