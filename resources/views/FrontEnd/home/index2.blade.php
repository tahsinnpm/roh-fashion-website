@extends('FrontEnd.master')
@section('title')
    Home
@endsection

@section('content')
    <style>
        /* Modern Design Variables */
        :root {
            --primary: #D83F26;
            --primary-dark: #B02C18;
            --secondary: #182b5c;
            --accent: #FF6B4A;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --gray: #6c757d;
            --success: #28a745;
            --border-radius: 12px;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        .custom_container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 12px;
        }

        .site-category-icon {
            width: 22px;
            height: 22px;
            margin-right: 12px;
            object-fit: contain;
            filter: grayscale(100%);
            transition: var(--transition);
        }

        .category-link:hover .site-category-icon {
            filter: grayscale(0%);
        }

        /* Hero Section - Modern Design */
        .hero-section {
            padding: 15px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 300px;
            background: linear-gradient(135deg, #921A40 0%, var(--accent) 100%);
            opacity: 0.03;
            z-index: 0;
        }

        .hero-section-grid {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 16px;
            position: relative;
            z-index: 1;
        }

        /* Modern Categories Panel */
        .categories-panel {
            background: #ffffff;
            border-radius: var(--border-radius);
            padding: 0;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);

            height: fit-content;
        }

        .categories-header {
            background: #921A40;
            padding: 10px 15px;
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .categories-header i {
            font-size: 1.1rem;
        }

        .category-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 400px;
        }

        .category-menu::-webkit-scrollbar {
            width: 6px;
        }

        .category-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .category-menu::-webkit-scrollbar-thumb {
            background: #921A40;
            border-radius: 3px;
        }

        .category-item {
            position: relative;
            border-bottom: 1px solid #f0f0f0;
            transition: var(--transition);
        }

        .category-item:last-child {
            border-bottom: none;
        }

        .category-item:hover {
            background: rgba(216, 63, 38, 0.05);
        }

        .category-link {
            color: var(--dark);
            display: flex;
            align-items: center;
            padding: 8px 16px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .category-link:hover {
            color: #921A40;
            transform: translateX(5px);
        }

        .category-icon {
            width: 22px;
            height: 22px;
            margin-right: 12px;
            object-fit: contain;
            filter: grayscale(100%);
            transition: var(--transition);
        }

        .category-link:hover .category-icon {
            filter: grayscale(0%);
        }

        .category-arrow {
            margin-left: auto;
            font-size: 12px;
            color: var(--gray);
            transition: var(--transition);
        }

        .category-item:hover .category-arrow {
            color: #921A40;
            transform: translateX(3px);
        }

        /* Subcategories Dropdown - Modern */
        .subcategories {
            position: absolute;
            left: 100%;
            top: 0;
            width: 280px;
            background: #ffffff;
            border-radius: 0 var(--border-radius) var(--border-radius) var(--border-radius);
            box-shadow: 5px 5px 25px rgba(0, 0, 0, 0.1);
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 100;
            border: 1px solid #f0f0f0;
            padding: 10px 0;
        }

        .category-item:hover .subcategories {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .subcategory-item {
            padding: 10px 20px;
            transition: var(--transition);
        }

        .subcategory-item:hover {
            background: rgba(216, 63, 38, 0.05);
        }

        .subcategory-link {
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: var(--transition);
            font-weight: 500;
        }

        .subcategory-link:hover {
            color: #921A40;
            transform: translateX(5px);
        }

        /* Modern Main Slider */
        .main-slider {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            position: relative;
        }

        .main-slider img {
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .main-slider .carousel-item:hover img {
            transform: scale(1.02);
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: var(--transition);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .carousel-control-prev {
            left: 20px;
        }

        .carousel-control-next {
            right: 20px;
        }

        .main-slider:hover .carousel-control-prev,
        .main-slider:hover .carousel-control-next {
            opacity: 1;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(25%) sepia(90%) saturate(4000%) hue-rotate(350deg) brightness(90%) contrast(95%);
        }

        /* Side Banners - Modern */
        .side-banners {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .promo-banner {
            position: relative;
            background-color: var(--secondary);
            border-radius: var(--border-radius);
            height: calc(50% - 10px);
            overflow: hidden;
            transition: var(--transition);
        }

        .promo-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.4));
            z-index: 1;
        }

        .promo-banner:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .promo-banner:nth-child(2) {
            background: linear-gradient(135deg, #921A40 0%, var(--accent) 100%);
        }

        .promo-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }



        .promo-banner::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .promo-title {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .promo-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .promo-btn {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: #fff;
            padding: 2px 20px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            transition: var(--transition);
            align-self: flex-start;
            text-decoration: none;
            display: inline-block;
        }

        .promo-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
            transform: translateY(-2px);
        }



        /* Section Headers */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            position: relative;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, #921A40 0%, var(--accent) 100%);
        }

        .section-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .view-all-btn {
            background: #921A40;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .view-all-btn:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(216, 63, 38, 0.3);
        }

        /* Modern Product Cards */
        .product-card {
            background: #fff;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            border: 1px solid #f0f0f0;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #921A40;
        }

        .product-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #921A40;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            line-height: 1;
            font-weight: 600;
            z-index: 2;
        }

        .product-details {
            padding: 12px;
        }

        .product-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
            line-height: 1;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-title a {
            color: inherit;
            text-decoration: none;
        }

        .product-title a:hover {
            color: #921A40;
        }

        .price-section {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .current-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: #921A40;
        }

        .original-price {
            font-size: 0.9rem;
            color: var(--gray);
            text-decoration: line-through;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-buy-now,
        .btn-add-cart {
            border: none;
            padding: 0px 10px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            flex: 1;
            text-align: center;
        }

        .btn-buy-now {
            background: #921A40;
            color: white;
        }

        .btn-buy-now:hover {
            background: var(--primary-dark);
        }

        .btn-add-cart {
            background: transparent;
            color: #921A40;
            border: 1px solid #921A40;
        }

        .btn-add-cart:hover {
            background: #921A40;
            color: white;
        }

        .out-of-stock {
            background: #f8f9fa;
            color: var(--gray);
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Category Grid */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .category-item-grid {
            background: #fff;
            border-radius: var(--border-radius);
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            border: 1px solid #f0f0f0;
        }

        .category-item-grid:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: #921A40;
        }

        .category-item-grid img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 10px;
            transition: var(--transition);
        }

        .category-item-grid:hover img {
            transform: scale(1.1);
        }

        .category-title {
            font-weight: 600;
            color: var(--dark);
            margin: 0;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .hero-section-grid {
                grid-template-columns: 250px 1fr 280px;
            }

            .main-slider img {
                height: 400px;
            }
        }

        @media (max-width: 991px) {
            .hero-section-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .categories-panel {
                display: none;
            }

            .main-slider img {
                height: 350px;
            }

            .side-banners {
                flex-direction: row;
            }

            .promo-banner {
                height: 200px;
            }

            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 20px 0;
            }

            .main-slider img {
                height: 250px;
            }

            .side-banners {
                flex-direction: column;
            }

            .promo-banner {
                height: 150px;
            }

            .section-title {
                font-size: 1.6rem;
            }

            .category-grid {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
                gap: 10px;
            }

            .verified-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .main-slider img {
                height: 200px;
            }

            .carousel-control-prev,
            .carousel-control-next {
                width: 40px;
                height: 40px;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .view-all-btn {
                align-self: flex-start;
            }

            .category-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Verified Section - New Styles */
        .verified-section {
            padding: 20px 0;
            background: #fff;
        }

        .verified-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .verified-item {
            padding: 14px;
            transition: all 0.3s ease;
            border-radius: 10px;
            background: #fff;
        }

        .verified-item {
            transform: translateY(-5px);
        }

        .icon-box {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background: #F5F6F8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .verified-item .icon-circle {
            background: #7D1A39;
        }

        .icon-circle img {
            width: 30px;
            height: 30px;
            transition: all 0.3s ease;
        }

        .verified-item .icon-circle img {
            filter: brightness(0) invert(1);
        }

        .icon-text {
            flex: 1;
        }

        .icon-text h4 {
            font-size: 16px;
            font-weight: 600;
            color: #182B5C;
            margin: 0 0 5px;
            transition: all 0.3s ease;
        }

        .verified-item .icon-text h4 {
            color: #7D1A39;
        }

        .icon-text p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        @media (max-width: 1200px) {
            .verified-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .verified-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .verified-grid {
                grid-template-columns: 1fr;
            }

            .verified-item {
                padding: 15px;
            }

            .icon-circle {
                width: 50px;
                height: 50px;
            }

            .icon-circle img {
                width: 25px;
                height: 25px;
            }

            .icon-text h4 {
                font-size: 14px;
            }

            .icon-text p {
                font-size: 12px;
            }
        }

        /* Modern Category Slider Styles */
        /* Category Slider - Modern Design 2023 */
        .category-slider {
            padding: 30px 0;
        }

        .category-slide {
            padding: 10px;
        }

        .category-card {
            display: block;
            text-decoration: none;
            background: #fff;
            padding: 25px 15px;
            position: relative;
            transition: all 0.4s ease;
            border-radius: 16px;
            border: 1px solid rgba(146, 26, 64, 0.1);
        }

        .category-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            background: linear-gradient(145deg, #921A40 0%, #C1255A 100%);
            opacity: 0;
            z-index: 0;
            transition: opacity 0.4s ease;
        }

        .category-card:hover::after {
            opacity: 1;
        }

        .category-icon {
            position: relative;
            z-index: 1;
            width: 85px;
            height: 85px;
            margin: 0 auto 15px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(146, 26, 64, 0.1);
            transition: all 0.4s ease;
        }

        .category-card:hover .category-icon {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(146, 26, 64, 0.2);
        }

        .category-icon img {
            width: 45px;
            height: 45px;
            object-fit: contain;
            transition: all 0.4s ease;
            filter: grayscale(100%);
        }

        .category-card:hover .category-icon img {
            filter: none;
            transform: scale(1.1);
        }

        .category-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .category-name {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin: 0;
            transition: all 0.4s ease;
            position: relative;
            padding-bottom: 10px;
        }

        .category-name::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #fff;
            transition: all 0.4s ease;
        }

        .category-card:hover .category-name {
            color: #fff;
        }

        .category-card:hover .category-name::after {
            width: 40px;
        }

        /* Custom Navigation Arrows */
        .category-slider .slick-prev,
        .category-slider .slick-next {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 3px 12px rgba(146, 26, 64, 0.15);
            transition: all 0.3s ease;
            z-index: 1;
        }

        .category-slider .slick-prev {
            left: 0px;
        }

        .category-slider .slick-next {
            right: 0px;
        }

        .category-slider .slick-prev:hover,
        .category-slider .slick-next:hover {
            background: #921A40;
        }

        .category-slider .slick-prev:before,
        .category-slider .slick-next:before {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 18px;
            color: #921A40;
            transition: all 0.3s ease;
        }

        .category-slider .slick-prev:hover:before,
        .category-slider .slick-next:hover:before {
            color: #fff;
        }

        .category-slider .slick-prev:before {
            content: "\f104";
        }

        .category-slider .slick-next:before {
            content: "\f105";
        }

        /* Dots Styling */
        .category-slider .slick-dots {
            bottom: -35px;
        }

        .category-slider .slick-dots li button {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(146, 26, 64, 0.2);
            transition: all 0.3s ease;
        }

        .category-slider .slick-dots li.slick-active button {
            background: #921A40;
            transform: scale(1.2);
        }

        .category-slider .slick-dots li button:before {
            display: none;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .category-card {
                padding: 15px 10px;
            }

            .category-icon {
                width: 65px;
                height: 65px;
            }

            .category-icon img {
                width: 35px;
                height: 35px;
            }

            .category-name {
                font-size: 13px;
            }

            .category-slider .slick-prev {
                left: 0px;
            }

            .category-slider .slick-next {
                right: 0px;
            }
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
        }
    </style>

    <!-- Modern Hero Section -->
    <section class="hero-section">
        <div class="custom_container">
            <div class="hero-section-grid">
                <!-- Modern Categories Panel -->
                <div class="categories-panel">
                    <div class="categories-header">
                        <i class="fas fa-bars"></i>
                        <span>
                            @if(session()->get('language') == 'bangla')
                                সকল ক্যাটাগরি
                            @else
                                All Categories
                            @endif
                        </span>
                    </div>

                    <ul class="category-menu" style="height:100%">
                        @foreach($categories as $category)
                            @if($category->type == 1)
                                <li class="category-item" style="height:100%">
                                    <a href="{{route('product.category', $category->slug)}}" class="category-link"
                                        style="height:100%">
                                        <img src="{{asset($category->icon)}}" class="site-category-icon" alt="" style="height:100%">
                                        <span>
                                            @if(session()->get('language') == 'bangla')
                                                {{$category->name_bn}}
                                            @else
                                                {{$category->name_en}}
                                            @endif
                                        </span>
                                        @php $subcategories = findChildCategory($category->id) @endphp
                                        @if(count($subcategories) > 0)
                                            <i class="fas fa-chevron-right category-arrow"></i>
                                        @endif
                                    </a>

                                    @if(count($subcategories) > 0)
                                        <div class="subcategories">
                                            @foreach($subcategories as $subcategory)
                                                <div class="subcategory-item">
                                                    <a href="{{route('product.category', $subcategory->slug)}}" class="subcategory-link">
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

                <!-- Modern Main Slider -->
                <div class="main-slider">
                    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel" style="height:100%">
                        <div class="carousel-inner" style="height:100%">
                            @foreach ($sliders as $index => $slider)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" style="height:100%">
                                    <img src="{{ asset($slider->slider_img) }}" alt="Slider Image" style="height:100%">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>


                <div class="side-banners">
                    @foreach ($home_banners->take(2) as $index => $banner)
                        <div class="promo-banner"
                            style="background-image: url('{{ asset($banner->banner_img) }}'); background-size: cover; background-position: center;">
                            <div class="promo-content"
                                style="position: relative; z-index: 2; background: rgba(0,0,0,0.4); padding: 20px; border-radius: var(--border-radius);">
                                <h4 class="promo-title">Special Offer {{ $index + 1 }}</h4>
                                <p class="promo-text">Discover amazing deals on our premium products</p>
                                <a href="{{ $banner->url ?? '#' }}" class="promo-btn">Shop Now</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Verified Section -->
    <section class="verified-section">
        <div class="custom_container">
            <div class="verified-grid">
                <div class="verified-item">
                    <div class="icon-box">
                        <div class="icon-circle">
                            <img src="{{asset('FrontEnd')}}/assect/img/icon/safe.png" alt="Safe Payments">
                        </div>
                        <div class="icon-text">
                            <h4>
                                @if(session()->get('language') == 'bangla')
                                    নিরাপদ পেমেন্ট
                                @else
                                    Safe & Secure
                                @endif
                            </h4>
                            <p>Payment Protection</p>
                        </div>
                    </div>
                </div>
                <div class="verified-item">
                    <div class="icon-box">
                        <div class="icon-circle">
                            <img src="{{asset('FrontEnd')}}/assect/img/icon/car.png" alt="Delivery">
                        </div>
                        <div class="icon-text">
                            <h4>
                                @if(session()->get('language') == 'bangla')
                                    দেশব্যাপী ডেলিভারি
                                @else
                                    Fast Delivery
                                @endif
                            </h4>
                            <p>Nationwide Service</p>
                        </div>
                    </div>
                </div>

                <div class="verified-item">
                    <div class="icon-box">
                        <div class="icon-circle">
                            <img src="{{asset('FrontEnd')}}/assect/img/icon/best.png" alt="Price">
                        </div>
                        <div class="icon-text">
                            <h4>
                                @if(session()->get('language') == 'bangla')
                                    বেস্ট প্রাইস
                                @else
                                    Best Price
                                @endif
                            </h4>
                            <p>Guaranteed Savings</p>
                        </div>
                    </div>
                </div>
                <div class="verified-item">
                    <div class="icon-box">
                        <div class="icon-circle">
                            <img src="{{asset('FrontEnd')}}/assect/img/icon/right.png" alt="Authentic">
                        </div>
                        <div class="icon-text">
                            <h4>
                                @if(session()->get('language') == 'bangla')
                                    অথেনটিক পণ্য
                                @else
                                    100% Authentic
                                @endif
                            </h4>
                            <p>Genuine Products</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Flash Sale Section -->
    @php
        $campaign = \App\Models\Campaing::where('status', 1)->where('is_featured', 1)->orderBy('id', 'desc')->first();
        if ($campaign) {
            $campaing_products = $campaign->campaing_products;

            $flash_start = date_create($campaign->flash_start);
            $flash_end = date_create($campaign->flash_end);

            $start_diff = $flash_start->getTimestamp() - time();
            $end_diff = $flash_end->getTimestamp() - time();

            $start_diff2 = date_diff(date_create($campaign->flash_start), date_create(date('d-m-Y H:i:s')));
            $end_diff2 = date_diff(date_create(date('d-m-Y H:i:s')), date_create($campaign->flash_end));
        }
    @endphp

    @if($campaign && $start_diff2->invert == 0 && $end_diff2->invert == 0)
        <section class="custom_container pt-4">
          <style>
            /* Add this CSS */
.flash-sale-header {
    background: linear-gradient(135deg, #921A40 0%, #921A40 100%);
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.flash-sale-header::before {
    content: '🔥';
    position: absolute;
    right: -20px;
    top: -30px;
    font-size: 120px;
    opacity: 0.1;
    transform: rotate(15deg);
}

.flash-sale-header::after {
    content: '';
    position: absolute;
    bottom: -50%;
    left: -10%;
    width: 120%;
    height: 120%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    animation: pulse 2s infinite;
}

.flash-sale-content {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 30px;
    position: relative;
    z-index: 1;
}

.flash-sale-title {
    font-size: 32px;
    font-weight: 800;
    color: white;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.fire-icon {
    font-size: 40px;
    animation: flame 0.8s ease-in-out infinite alternate;
}

.timer-section {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 15px 25px;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-align: center;
}

.timer-label {
    color: rgba(255, 255, 255, 0.9);
    font-size: 14px;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.countdown-timer {
    font-family: 'Arial', sans-serif;
    font-size: 32px;
    font-weight: 700;
    color: white;
    line-height: 1;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    letter-spacing: 2px;
}

.flash-sale-cta {
    background: white;
    color: #921A40;
    padding: 12px 30px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    margin-left: auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.flash-sale-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    background: rgba(255,255,255,0.9);
    color: #921A40;
}

.flash-sale-cta i {
    transition: transform 0.3s ease;
}

.flash-sale-cta:hover i {
    transform: translateX(5px);
}

@keyframes flame {
    0% { transform: scale(1) rotate(-5deg); }
    100% { transform: scale(1.1) rotate(5deg); }
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.02); opacity: 0.3; }
    100% { transform: scale(1); opacity: 0.5; }
}

/* Responsive Design */
@media (max-width: 992px) {
    .flash-sale-content {
        grid-template-columns: 1fr 1fr;
    }
    
    .flash-sale-cta {
        grid-column: 1/-1;
        justify-self: center;
        margin: 0;
    }
}

@media (max-width: 768px) {
    .flash-sale-header {
        padding: 20px;
    }

    .flash-sale-content {
        grid-template-columns: 1fr;
        gap: 20px;
        text-align: center;
    }

    .flash-sale-title {
        justify-content: center;
        font-size: 28px;
    }

    .timer-section {
        padding: 12px 20px;
    }

    .countdown-timer {
        font-size: 28px;
    }
}

@media (max-width: 576px) {
    .flash-sale-title {
        font-size: 24px;
    }
    
    .fire-icon {
        font-size: 32px;
    }
    
    .countdown-timer {
        font-size: 24px;
    }
    
    .flash-sale-cta {
        width: 100%;
        justify-content: center;
    }
}
          </style>
        <!-- Replace the existing flash sale header with this updated HTML -->
<div class="flash-sale-header">
    <div class="flash-sale-content">
        <h2 class="flash-sale-title">
            <span class="fire-icon">🔥</span>
            @if(session()->get('language') == 'bangla')
                ফ্ল্যাশ সেল
            @else
                Flash Sale
            @endif
        </h2>
        
        <div class="timer-section">
            <div class="timer-label">
                @if(session()->get('language') == 'bangla')
                    শেষ হচ্ছে
                @else
                    Ending In
                @endif
            </div>
            <div class="countdown-timer" id="demo">
                00:00:00
            </div>
        </div>
        
        <a href="{{ route('campaign.product') }}" class="flash-sale-cta">
            @if(session()->get('language') == 'bangla')
                আরো দেখুন
            @else
                View All
            @endif
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
            <div class="flash-sale owl-carousel">
                @foreach($campaign->campaing_products->take(6) as $product)
                    @php $data = calculateDiscount($product->product->id); @endphp
                    <div>
                        <div class="product-card">
                            @if($data['text'])
                                <div class="discount-badge">
                                    Save {{$data['text']}}
                                </div>
                            @endif

                            <div class="product-image">
                                <a href="{{route('product.details', $product->product->slug)}}">
                                    <img src="{{asset($product->product->product_thumbnail)}}" alt="Product Image">
                                </a>
                            </div>

                            <div class="product-details">
                                <h3 class="product-title">
                                    <a href="{{route('product.details', $product->product->slug)}}">
                                        @if(session()->get('language') == 'bangla')
                                            {!! Str::substr($product->product->name_bn, 0, 25) !!}
                                        @else
                                            {!! Str::substr($product->product->name_en, 0, 25) !!}
                                        @endif
                                    </a>
                                </h3>

                                <div class="price-section">
                                    <span class="current-price">{{$data['discount']}} TK</span>
                                    <span class="original-price">{{$product->product->regular_price}} TK</span>
                                </div>

                                <div class="action-buttons">
                                    @if($product->product->stock_qty > 0)
                                        @if($product->product->is_varient == 1)
                                            <button onclick="productView({{ $product->product->id }})" data-bs-toggle="modal"
                                                data-bs-target="#quickViewModal" class="btn-buy-now">
                                                {{ session()->get('language') == 'bangla' ? ' কিনুন' : 'Buy Now' }}
                                            </button>
                                            <button onclick="productView({{ $product->product->id }})" data-bs-toggle="modal"
                                                data-bs-target="#quickViewModal" class="btn-add-cart">
                                                {{ session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন' : 'Add Cart' }}
                                            </button>
                                        @else
                                            <button onclick="buyNow({{ $product->product->id }})" class="btn-buy-now">
                                                {{ session()->get('language') == 'bangla' ? ' কিনুন' : 'Buy Now' }}
                                            </button>

                                            <input type="hidden" id="pfrom" value="direct">
                                            <input type="hidden" id="product_product_id" value="{{ $product->product->id }}" min="1">
                                            <input type="hidden" id="{{ $product->product->id }}-product_pname"
                                                value="{{ $product->product->name_en }}">

                                            <button onclick="addToCartDirect({{ $product->product->id }})" class="btn-add-cart">
                                                {{ session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন' : 'Add  Cart' }}
                                            </button>
                                        @endif
                                    @else
                                        <div class="out-of-stock">
                                            {{ session()->get('language') == 'bangla' ? 'স্টক আউট' : ' Stock Out' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Categories Section -->
    <section class="custom_container pt-4">
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

        <div class="category-slider">
            @foreach($featured_category as $fc)
                <div class="category-slide">
                    <a href="{{ route('product.category', $fc->slug) }}" class="category-card">
                        <div class="category-icon">
                            <img src="{{ asset($fc->image) }}" alt="{{ $fc->name_en }}">
                        </div>
                        <div class="category-content">
                            <h3 class="category-name">
                                @if(session()->get('language') == 'bangla')
                                    {{ $fc->name_bn }}
                                @else
                                    {{ $fc->name_en }}
                                @endif
                            </h3>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    <!-- Featured Products Section -->
    <section class="custom_container mb-5">
        <div class="section-header">
            <h2 class="section-title">Featured Products</h2>
            <a href="{{ route('product.show') }}" class="view-all-btn">View All</a>
                    </div>

    <div class="featured-slider owl-carousel">
        @foreach($product_featured->take(8) as $product)
            @php $data = calculateDiscount($product->id); @endphp
            <div class="">
                <div class="product-card">
                    @if($data['text'])
                        <div class="discount-badge">
                            Save {{ $data['text'] }}
                        </div>
                    @endif

                    <div class="product-image">
                        <a href="{{ route('product.details', $product->slug) }}">
                            <img src="{{ asset($product->product_thumbnail) }}" alt="Product Image">
                        </a>
                    </div>

                    <div class="product-details">
                        <h3 class="product-title">
                            <a href="{{ route('product.details', $product->slug) }}">
                                @if(session()->get('language') == 'bangla')
                                    {!! Str::substr($product->name_bn, 0, 25) !!}
                                @else
                                    {!! Str::substr($product->name_en, 0, 25) !!}
                                @endif
                            </a>
                        </h3>

                        <div class="price-section">
                            <span class="current-price">{{ $data['discount'] }} TK</span>
                            @if($product->regular_price != $data['discount'])
                                <span class="original-price">{{ $product->regular_price }} TK</span>
                            @endif
                        </div>

                        <div class="action-buttons">
                            @if($product->stock_qty > 0)
                                @if($product->is_varient == 1)
                                    <button onclick="productView({{ $product->id }})" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#quickViewModal" 
                                            class="btn-buy-now">
                                        {{ session()->get('language') == 'bangla' ? 'এখনি কিনুন' : 'Buy Now' }}
                                    </button>
                                    <button onclick="productView({{ $product->id }})" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#quickViewModal" 
                                            class="btn-add-cart">
                                        {{ session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন' : 'Add Cart' }}
                                    </button>
                                @else
                                    <button onclick="buyNow({{ $product->id }})" class="btn-buy-now">
                                        {{ session()->get('language') == 'bangla' ? 'এখনি কিনুন' : 'Buy Now' }}
                                    </button>

                                    <input type="hidden" id="pfrom" value="direct">
                                    <input type="hidden" id="product_product_id" value="{{ $product->id }}" min="1">
                                    <input type="hidden" id="{{ $product->id }}-product_pname" value="{{ $product->name_en }}">

                                    <button onclick="addToCartDirect({{ $product->id }})" class="btn-add-cart">
                                        {{ session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন' : 'Add Cart' }}
                                    </button>
                                @endif
                            @else
                                <div class="out-of-stock">
                                    {{ session()->get('language') == 'bangla' ? 'স্টক আউট' : 'Out of Stock' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

                </section>
                <style>
                    /* Popular Brands Slider Styles */
    .brands-slider {
        padding: 10px 0;
    }

    .brand-slide {
        padding: 5px;
    }

    .brand-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 25px 20px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .brand-card:hover {
        transform: translateY(-5px);
        border-color: #921A40;
        box-shadow: 0 10px 20px rgba(146, 26, 64, 0.08);
    }

    .brand-image {
        width: 120px;
        height: 80px;
        margin-bottom: 15px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .brand-card:hover .brand-image img {
        filter: grayscale(0%);
        opacity: 1;
    }

    .brand-title {
        font-size: 14px;
        color: #333;
        margin: 0;
        text-align: center;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .brand-card:hover .brand-title {
        color: #921A40;
    }

    /* Custom Navigation */
    .brands-slider .slick-prev,
    .brands-slider .slick-next {
        width: 40px;
        height: 40px;
        background: #fff;
        border: 2px solid #eee;
        border-radius: 8px;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .brands-slider .slick-prev {
        left: -5px;
    }

    .brands-slider .slick-next {
        right: -5px;
    }

    .brands-slider .slick-prev:hover,
    .brands-slider .slick-next:hover {
        background: #921A40;
        border-color: #921A40;
    }

    .brands-slider .slick-prev:before,
    .brands-slider .slick-next:before {
        font-family: "Font Awesome 5 Free";
        font-weight: 800;
        font-size: 16px;
        color: #921A40;
        transition: all 0.3s ease;
    }

    .brands-slider .slick-prev:hover:before,
    .brands-slider .slick-next:hover:before {
        color: #fff;
    }

    .brands-slider .slick-prev:before {
        content: "\f104";
    }

    .brands-slider .slick-next:before {
        content: "\f105";
    }

                </style>

               <!-- Popular Brands Section -->
    <section class="custom_container mb-5">
        <div class="section-header">
            <h2 class="section-title">Popular Brands</h2>
            <a href="{{ route('product.show') }}" class="view-all-btn">View All</a>
        </div>

        <div class="brands-slider category-slider">
            @foreach($brands as $brand)
                @if($brand->name_en != 'Non Brand')
                    <div class="brand-slide">
                        <a href="#" class="brand-card">
                            <div class="brand-image">
                                <img src="{{asset($brand->brand_image)}}" alt="{{ $brand->name_en }}">
                            </div>
                            <p class="brand-title">
                                {{session()->get('language') == 'bangla' ? $brand->name_bn : $brand->name_en}}
                            </p>
                        </a>
                    </div>
                @endif
            @endforeach
</div>
    </section>

                <!-- Latest Products Section -->
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
                            @php 
                                                                                                    $data = calculateDiscount($product_trending->id);
                                $discountPercentage = $data['text'];
                            @endphp

                            <div class="">
                                <div class="product-card">
                                    @if($data['discount'] && $product_trending->regular_price != $data['discount'])
                                        <div class="discount-badge">
                                            Save {{ $discountPercentage }}
                                        </div>
                                    @endif

                                    <div class="product-image">
                                        <a href="{{ route('product.details', $product_trending->slug) }}">
                                            <img src="{{ asset($product_trending->product_thumbnail) }}" alt="Product Image">
                                        </a>
                                    </div>

                                    <div class="product-details">
                                        <h3 class="product-title">
                                            <a href="{{ route('product.details', $product_trending->slug) }}">
                                                @if(session()->get('language') == 'bangla')
                                                    {{ $product_trending->name_bn }}
                                                @else
                                                    {{ $product_trending->name_en }}
                                                @endif
                                            </a>
                                        </h3>

                                        <div class="price-section">
                                            <span class="current-price">{{ $data['discount'] }} TK</span>
                                            @if($product_trending->regular_price != $data['discount'])
                                                <span class="original-price">{{ $product_trending->regular_price }} TK</span>
                                            @endif
                                        </div>

                                        <div class="action-buttons">
                                            @if($product_trending->stock_qty > 0)
                                                @if($product_trending->is_varient == 1)
                                                    <button onclick="productView({{ $product_trending->id }})" data-bs-toggle="modal" data-bs-target="#quickViewModal" class="btn-buy-now">
                                                        {{ session()->get('language') == 'bangla' ? 'কিনুন' : 'Buy Now' }}
                                                    </button>
                                                    <button onclick="productView({{ $product_trending->id }})" data-bs-toggle="modal" data-bs-target="#quickViewModal" class="btn-add-cart">
                                                        {{ session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন' : 'Add Cart' }}
                                                    </button>
                                                @else
                                                    <button onclick="buyNow({{ $product_trending->id }})" class="btn-buy-now">
                                                        {{ session()->get('language') == 'bangla' ? 'কিনুন' : 'Buy Now' }}
                                                    </button>
                                                    <input type="hidden" id="pfrom" value="direct">
                                                    <input type="hidden" id="product_product_id" value="{{ $product_trending->id }}" min="1">
                                                    <input type="hidden" id="{{ $product_trending->id }}-product_pname" value="{{ $product_trending->name_en }}">
                                                    <button onclick="addToCartDirect({{ $product_trending->id }})" class="btn-add-cart">
                                                        {{ session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন' : 'Add Cart' }}
                                                    </button>
                                                @endif
                                            @else
                                                <div class="out-of-stock">
                                                    {{ session()->get('language') == 'bangla' ? 'স্টক আউট' : 'Out of Stock' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                          <button type="button" id="load-more-btn" class="view-all-btn">
                            @if(session()->get('language') == 'bangla')
                                আরো দেখুন
                            @else
                                View More
                            @endif
                        </button></style></style></style></style></style></style></style></style></style>
                    </div>
                </section>
                <style>
                    /* Modern About Section Styles */
    .about-section {
        background: linear-gradient(120deg, #fff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 50px 30px;
        position: relative;
        overflow: hidden;
    }

    .about-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #921A40, #ff6b6b);
    }

    .about-content {
        position: relative;
        z-index: 2;
    }

    .about-title {
        font-size: 2.5rem;
        font-weight: 600;
        color: #2D3748;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
    }

    .about-description {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #4A5568;
        margin-bottom: 10px;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-top: 10px;
    }

    .feature-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(146, 26, 64, 0.15);
    }

    .feature-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, transparent 50%, rgba(146, 26, 64, 0.05) 50%);
        border-radius: 0 0 0 100%;
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        background: #921A40;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transform: rotate(10deg);
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: rotate(0deg);
    }

    .feature-icon i {
        font-size: 24px;
        color: white;
    }

    .feature-title {
        font-size: 18px;
        font-weight: 600;
        color: #2D3748;
        margin-bottom: 8px;
    }

    .feature-text {
        font-size: 14px;
        color: #718096;
        margin: 0;
    }

    .about-cta {
        background: #921A40;
        padding: 40px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
    }

    .about-cta::before {
        content: '';
        position: absolute;
        right: -20px;
        bottom: -20px;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .cta-title {
        font-size: 24px;
        font-weight: 700;
        color: white;
        margin-bottom: 15px;
    }

    .cta-text {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 25px;
        font-size: 15px;
        line-height: 1.6;
    }

    .cta-button {
        display: inline-flex;
        align-items: center;
        padding: 12px 25px;
        background: white;
        color: #921A40;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        color: #921A40;
    }

    @media (max-width: 992px) {
        .features-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .about-section {
            padding: 30px 20px;
        }

        .about-title {
            font-size: 2rem;
        }

        .features-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .about-cta {
            padding: 30px;
            margin-top: 30px;
        }
    }
                </style>

                <!-- About Section -->
               <section class="custom_container mb-5">
        <div class="about-section">
            <div class="row">
                <div class="col-lg-8">
                    <div class="about-content">
                        <h2 class="about-title">{{ get_setting('site_name')->value ?? '' }}</h2>
                        <p class="about-description">
                            Your trusted partner for hassle-free online shopping. We bring you the latest in technology, innovation, and authentic products designed to enhance your comfort.
                        </p>

                        <div class="features-grid">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <h4 class="feature-title">0% EMI Available</h4>
                                <p class="feature-text">Flexible payment options with easy installments</p>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h4 class="feature-title">Product Warranty</h4>
                                <p class="feature-text">Guaranteed brand authenticity and protection</p>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-undo-alt"></i>
                                </div>
                                <h4 class="feature-title">Easy Returns</h4>
                                <p class="feature-text">Hassle-free 3-day return policy</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="about-cta">
                        <h3 class="cta-title">Why Choose Us?</h3>
                        <p class="cta-text">We're committed to providing you with the best shopping experience through quality products and excellent service.</p>
                        <a href="#" class="cta-button">
                            Learn More 
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
        <script>
            // Flash Sale Countdown Timer
            var startDiff = <?php echo isset($start_diff) ? $start_diff * 1000 : 0; ?>;
            var endDiff = <?php echo isset($end_diff) ? $end_diff * 1000 : 0; ?>;

            if (startDiff > 0 || endDiff > 0) {
                var countDownDateStart = new Date(Date.now() + startDiff);
                var countDownDateEnd = new Date(Date.now() + endDiff);

                var x = setInterval(function() {
                    var now = new Date().getTime();
                    var countDownDate = (now < countDownDateStart.getTime()) ? countDownDateStart : countDownDateEnd;
                    var distance = countDownDate - now;

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    var html = `<span>${days}d ${hours}h ${minutes}m ${seconds}s</span>`;

                    document.getElementById("demo").innerHTML = html;

                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("demo").innerHTML = "EXPIRED";
                    }
                }, 1000);
            }

            // Load More Products
            var offset = 8;
            $('#load-more-btn').click(function() {
                var $btn = $(this);
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                $btn.prop('disabled', true);

                $.ajax({
                    url: '/load-more-products',
                    method: 'GET',
                    data: {
                        offset: offset,
                        count: 8
                    },
                    success: function(response) {
                        var products = response.products;

                        if (products.length > 0) {
                            var html = '';
                            products.forEach(function(product) {
                                var discountPercentage = Math.round(((product.regular_price - product.discount_price) / product.regular_price) * 100);

                                html += `
                                    <div class="">
                                        <div class="product-card">
                                            ${(product.discount_price && product.regular_price != product.discount_price) ? `
                                                <div class="discount-badge">
                                                    Save ${discountPercentage}%
                                                </div>
                                            ` : ''}

                                            <div class="product-image">
                                                <a href="/product/${product.slug}">
                                                    <img src="${product.product_thumbnail}" alt="Product Image">
                                                </a>
                                            </div>

                                            <div class="product-details">
                                                <h3 class="product-title">
                                                    <a href="/product/${product.slug}">
                                                        ${$("#language_status").val() == 'bangla' ? product.name_bn : product.name_en}
                                                    </a>
                                                </h3>

                                                <div class="price-section">
                                                    <span class="current-price">${product.discount_price} TK</span>
                                                    ${(product.regular_price != product.discount_price) ? `
                                                        <span class="original-price">${product.regular_price} TK</span>
                                                    ` : ''}
                                                </div>

                                                <div class="action-buttons">
                                                    ${product.stock_qty > 0 ? (
                                                        product.is_varient == 1 ? `
                                                            <button onclick="productView(${product.id})" data-bs-toggle="modal" data-bs-target="#quickViewModal" class="btn-buy-now">
                                                                ${$("#language_status").val() == 'bangla' ? 'কিনুন' : 'Buy Now'}
                                                            </button>
                                                            <button onclick="productView(${product.id})" data-bs-toggle="modal" data-bs-target="#quickViewModal" class="btn-add-cart">
                                                                ${$("#language_status").val() == 'bangla' ? 'কার্টে যোগ করুন' : 'Add Cart'}
                                                            </button>
                                                        ` : `
                                                            <button onclick="buyNow(${product.id})" class="btn-buy-now">
                                                                ${$("#language_status").val() == 'bangla' ? 'কিনুন' : 'Buy Now'}
                                                            </button>
                                                            <input type="hidden" id="pfrom" value="direct">
                                                            <input type="hidden" id="product_product_id" value="${product.id}" min="1">
                                                            <input type="hidden" id="${product.id}-product_pname" value="${product.name_en}">
                                                            <button onclick="addToCartDirect(${product.id})" class="btn-add-cart">
                                                                ${$("#language_status").val() == 'bangla' ? 'কার্টে যোগ করুন' : 'Add Cart'}
                                                            </button>
                                                        `
                                                    ) : `
                                                        <div class="out-of-stock">
                                                            ${$("#language_status").val() == 'bangla' ? 'স্টক আউট' : 'Out of Stock'}
                                                        </div>
                                                    `}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            $('#latest-products-container').append(html);
                            offset += products.length;

                            $btn.html($("#language_status").val() == 'bangla' ? 'আরো দেখুন' : 'View More');
                            $btn.prop('disabled', false);
                        } else {
                            $btn.html('No More Products');
                            $btn.prop('disabled', true);
                        }
                    },
                    error: function(error) {
                        console.error(error);
                        $btn.html($("#language_status").val() == 'bangla' ? 'আরো দেখুন' : 'View More');
                        $btn.prop('disabled', false);
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function(){
        $('.category-slider').slick({
            dots: true,
            infinite: true,
            speed: 500,
            slidesToShow: 6,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 5
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        });
    });
        </script>
@endpush