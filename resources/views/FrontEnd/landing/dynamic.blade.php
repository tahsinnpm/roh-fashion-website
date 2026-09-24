<!doctype html>
<html lang="bn">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $landingPage->title ?? ($product->name_en ?? 'Product') }} - {{ get_setting('site_name')->value ?? 'ROH CLOTHS' }}</title>
  <meta name="description" content="{{ Str::limit(strip_tags($landingPage->sub_title ?? $product->description_en ?? ''), 160) }}">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      margin: 0;
      font-family: "Hind Siliguri", Arial, "Noto Sans Bengali", sans-serif;
      background: #fff;
      color: #222;
      padding-bottom: 76px;
    }

    img {
      display: block;
      width: 100%;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .wrap {
      width: min(1120px, 94%);
      margin: auto;
    }

    :root {
      --main: {{ $landingPage->theme_color ?: '#275b57' }};
      --accent: #d8c7a7;
      --line: #e8e8e8;
      --soft: #f7f8f7;
      --muted: #6f7473;
      --maroon: #521626;
      --maroon-dark: #3f0f1b;
      --border-warm: #eddcd2;
      --border-input: #ecdcd3;
      --bg-warm: #fbf7f4;
    }

    /* Header */
    header {
      border-bottom: 1px solid var(--line);
      background: #fff;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .nav {
      height: 62px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo {
      font-size: 22px;
      font-weight: 800;
      letter-spacing: 1px;
      color: var(--main);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .logo img {
      max-height: 38px;
      width: auto;
    }

    .nav a.nav-btn {
      background: var(--main);
      color: #fff;
      padding: 10px 18px;
      font-weight: 700;
      border-radius: 4px;
      transition: opacity 0.2s ease;
    }

    .nav a.nav-btn:hover {
      opacity: 0.9;
    }

    /* Hero Section */
    .hero {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 38px;
      padding: 34px 0 46px;
      align-items: start;
    }

    .hero-slider {
      position: relative;
      overflow: hidden;
      border: 1px solid var(--line);
      border-radius: 8px;
      height: 560px;
      background: #fbfbfb;
    }

    .hero-slides {
      display: flex;
      height: 100%;
      transition: transform .45s ease;
    }

    .hero-slides img {
      min-width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .slide-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 38px;
      height: 38px;
      border: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, .9);
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      font-size: 24px;
      line-height: 1;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 5;
    }

    .slide-btn.prev { left: 10px; }
    .slide-btn.next { right: 10px; }

    .dots {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 12px;
      display: flex;
      justify-content: center;
      gap: 7px;
      z-index: 5;
    }

    .dots button {
      width: 8px;
      height: 8px;
      border: 0;
      border-radius: 50%;
      background: #d9d9d9;
      padding: 0;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .dots button.active {
      background: var(--main);
      width: 20px;
      border-radius: 4px;
    }

    .tag {
      font-size: 12px;
      letter-spacing: 2px;
      color: var(--main);
      margin: 4px 0 10px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .details h1 {
      font-size: 38px;
      line-height: 1.2;
      margin: 0 0 14px;
      color: #1f3533;
    }

    .lead {
      color: var(--muted);
      margin: 0 0 20px;
      font-size: 16px;
      line-height: 1.6;
    }

    .pricebox {
      border: 1px solid #dde3e2;
      border-radius: 8px;
      padding: 18px;
      margin: 0 0 16px;
      background: #fafcfb;
    }

    .price {
      font-size: 34px;
      color: var(--main);
      font-weight: 800;
    }

    .old {
      margin-left: 10px;
      color: #999;
      text-decoration: line-through;
      font-size: 18px;
    }

    .discount {
      display: inline-block;
      margin-left: 10px;
      background: #e9f1ef;
      color: var(--main);
      padding: 4px 10px;
      border-radius: 4px;
      font-size: 13px;
      font-weight: 700;
    }

    .stock {
      font-size: 13.5px;
      color: #555;
      margin-top: 12px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .bar {
      height: 5px;
      background: #eee;
      border-radius: 3px;
      margin-top: 7px;
      overflow: hidden;
    }

    .bar i {
      display: block;
      width: 68%;
      height: 100%;
      background: var(--main);
      border-radius: 3px;
    }

    .orderbtn {
      display: block;
      text-align: center;
      background: var(--main);
      color: #fff;
      padding: 14px;
      font-size: 17px;
      font-weight: 800;
      border-radius: 6px;
      margin: 20px 0 14px;
      box-shadow: 0 4px 14px rgba(39, 91, 87, 0.28);
      transition: opacity 0.2s ease, transform 0.15s ease;
    }

    .orderbtn:hover {
      opacity: 0.95;
      transform: translateY(-1px);
    }

    .notes {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .notes span {
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 8px 12px;
      font-size: 13px;
      color: #444;
      background: #fdfdfd;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    /* Sections */
    .section {
      padding: 52px 0;
      border-top: 1px solid var(--line);
    }

    .section h2 {
      text-align: center;
      color: #1f3533;
      margin: 0 0 12px;
      font-size: 30px;
      font-weight: 800;
    }

    .section-sub {
      text-align: center;
      color: var(--muted);
      margin: 0 auto 30px;
      max-width: 720px;
      font-size: 15.5px;
      line-height: 1.5;
    }

    .overview {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 36px;
      align-items: center;
    }

    .overview img {
      height: 480px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid var(--line);
    }

    .overview-text h3 {
      font-size: 28px;
      color: #1f3533;
      margin: 0 0 14px;
      font-weight: 800;
    }

    .overview-text p {
      color: var(--muted);
      margin: 0 0 14px;
      font-size: 16px;
      line-height: 1.6;
    }

    .overview-list {
      margin: 18px 0 0;
      padding-left: 20px;
      color: #383f3e;
      font-size: 15px;
    }

    .overview-list li {
      margin: 10px 0;
      line-height: 1.5;
    }

    /* Video Section if available */
    .video-container {
      position: relative;
      padding-bottom: 56.25%;
      height: 0;
      overflow: hidden;
      border-radius: 8px;
      margin-top: 24px;
      border: 1px solid var(--line);
    }
    .video-container iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: 0;
    }

    /* Customer Reviews */
    .review-slider {
      position: relative;
      overflow: hidden;
      padding: 10px 0;
    }

    .review-track {
      display: flex;
      transition: transform .45s ease;
      gap: 16px;
    }

    .review {
      flex: 0 0 calc(33.333% - 11px);
      border: 1px solid var(--line);
      border-radius: 8px;
      padding: 22px;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .stars {
      color: #f59e0b;
      letter-spacing: 2px;
      margin-bottom: 12px;
      font-size: 15px;
    }

    .review p {
      margin: 0 0 14px;
      color: #4b5563;
      font-size: 14.5px;
      line-height: 1.6;
      font-style: italic;
    }

    .review strong {
      display: block;
      color: #1f2937;
      font-size: 14px;
    }

    .review small {
      color: #9ca3af;
      font-size: 12.5px;
    }

    .review-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 38px;
      height: 38px;
      border: 1px solid var(--line);
      border-radius: 50%;
      background: #fff;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      font-size: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 5;
    }

    .review-prev { left: 0; }
    .review-next { right: 0; }

    /* ========================================================
       ORDER SECTION: 2-COLUMN LAYOUT WITH STICKY SUMMARY
       ======================================================== */
    .order-area {
      background: #fdfbf9;
      border-top: 1px solid var(--border-warm, #eddcd2);
      border-bottom: 1px solid var(--border-warm, #eddcd2);
    }

    .order-grid {
      display: grid;
      grid-template-columns: 1.15fr 0.85fr;
      gap: 28px;
      align-items: start;
    }

    /* Left Card: Input Fields */
    .order-fields-card {
      background: #ffffff;
      border: 1px solid var(--border-warm, #eddcd2);
      border-radius: 12px;
      padding: 28px 24px;
      box-shadow: 0 2px 12px rgba(130, 80, 60, 0.03);
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group:last-child {
      margin-bottom: 0;
    }

    .form-group label {
      display: block;
      font-size: 14.5px;
      font-weight: 700;
      color: #38302e;
      margin-bottom: 8px;
      letter-spacing: -0.1px;
    }

    .form-group input[type="text"],
    .form-group input[type="tel"],
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 14px;
      font-size: 15px;
      font-family: inherit;
      color: #2b2321;
      background: #ffffff;
      border: 1px solid var(--border-input, #ecdcd3);
      border-radius: 6px;
      outline: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 85px;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--maroon, #521626);
      box-shadow: 0 0 0 3px rgba(82, 22, 38, 0.1);
    }

    /* Attribute Selection Pills */
    .attribute-group {
      margin-bottom: 18px;
    }

    .attribute-label {
      display: block;
      font-size: 14.5px;
      font-weight: 700;
      color: #38302e;
      margin-bottom: 8px;
    }

    .attribute-pills {
      display: flex;
      flex-wrap: wrap;
      gap: 9px;
    }

    .attribute-pill-label {
      cursor: pointer;
      margin: 0;
    }

    .attribute-pill-label input {
      display: none;
    }

    .attribute-pill-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 18px;
      border: 1.5px solid var(--border-input, #ecdcd3);
      border-radius: 6px;
      background: #fff;
      color: #38302e;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.2s ease;
      user-select: none;
    }

    .attribute-pill-label input:checked + .attribute-pill-btn {
      border-color: var(--maroon, #521626);
      background: var(--maroon, #521626);
      color: #fff;
      box-shadow: 0 2px 8px rgba(82, 22, 38, 0.25);
    }

    .attribute-pill-label:hover .attribute-pill-btn {
      border-color: var(--maroon, #521626);
    }

    /* Delivery Area Options */
    .delivery-options {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .delivery-option {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 14px;
      background: #ffffff;
      border: 1.5px solid var(--border-input, #ecdcd3);
      border-radius: 8px;
      cursor: pointer;
      transition: border-color 0.2s ease, background 0.2s ease;
      font-weight: 500;
      margin: 0;
    }

    .delivery-option:hover {
      border-color: var(--maroon, #521626);
    }

    .delivery-option:has(input:checked) {
      border-color: var(--maroon, #521626);
      background: rgba(82, 22, 38, 0.04);
    }

    .delivery-option input {
      width: 16px;
      height: 16px;
      margin: 0;
      accent-color: var(--maroon, #521626);
      cursor: pointer;
    }

    .delivery-option-text {
      font-size: 14.5px;
      color: #38302e;
      font-weight: 600;
    }

    /* Right Card: Sticky Order Summary */
    .order-summary-card {
      position: sticky;
      top: 80px;
      z-index: 10;
      align-self: start;
      background: #ffffff;
      border: 1px solid var(--border-warm, #eddcd2);
      border-radius: 12px;
      padding: 26px 24px;
      box-shadow: 0 2px 12px rgba(130, 80, 60, 0.03);
    }

    .summary-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .summary-header-icon {
      width: 22px;
      height: 22px;
      stroke: var(--maroon, #521626);
      flex-shrink: 0;
    }

    .summary-header h3 {
      margin: 0;
      font-size: 19px;
      font-weight: 700;
      color: var(--maroon, #521626);
      line-height: 1.2;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 14px;
      margin-bottom: 14px;
      font-size: 14.5px;
    }

    .summary-item-label {
      color: #55504e;
      font-weight: 500;
    }

    .summary-item-val {
      color: #2b2321;
      font-weight: 600;
      text-align: right;
    }

    .summary-divider {
      height: 1px;
      background: var(--border-warm, #eddcd2);
      margin: 18px 0 16px;
    }

    .summary-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 22px;
    }

    .summary-total-label {
      font-size: 16px;
      font-weight: 700;
      color: #38302e;
    }

    .summary-total-val {
      font-size: 26px;
      font-weight: 800;
      color: var(--maroon, #521626);
      letter-spacing: -0.5px;
    }

    .order-submit-btn {
      width: 100%;
      background: var(--maroon, #521626);
      color: #ffffff;
      border: none;
      border-radius: 50px;
      padding: 14px 20px;
      font-size: 16.5px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      box-shadow: 0 4px 14px rgba(82, 22, 38, 0.25);
      transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
      display: block;
    }

    .order-submit-btn:hover {
      background: var(--maroon-dark, #3e0f1b);
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(82, 22, 38, 0.32);
    }

    .order-submit-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .order-terms-note {
      margin: 12px 0 0;
      font-size: 12.5px;
      color: #706662;
      text-align: center;
      line-height: 1.4;
    }

    /* FAQ */
    .faq {
      max-width: 850px;
      margin: auto;
      border-top: 1px solid var(--line);
    }

    details {
      border-bottom: 1px solid var(--line);
      padding: 0;
    }

    summary {
      cursor: pointer;
      padding: 17px 4px;
      font-weight: 700;
      color: #243b39;
      list-style: none;
      font-size: 16px;
    }

    summary::-webkit-details-marker {
      display: none;
    }

    summary:after {
      content: "+";
      float: right;
      color: var(--main);
      font-size: 20px;
      font-weight: 700;
    }

    details[open] summary:after {
      content: "−";
    }

    .faq p {
      margin: 0;
      padding: 0 4px 17px;
      color: var(--muted);
      line-height: 1.6;
      font-size: 15px;
    }

    footer {
      text-align: center;
      border-top: 1px solid var(--line);
      padding: 24px;
      color: #777;
      font-size: 14px;
    }

    /* Bottom Navigation Bar */
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-top: 1px solid var(--line);
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
      z-index: 999;
      padding: 10px 0;
    }

    .bottom-nav-wrap {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .bottom-nav-left {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .bottom-nav-label {
      font-size: 11px;
      color: var(--muted);
      font-weight: 600;
      line-height: 1.2;
      margin-bottom: 2px;
      letter-spacing: 0.3px;
    }

    .bottom-nav-price-group {
      display: flex;
      align-items: baseline;
      gap: 6px;
    }

    .bottom-nav-price {
      font-size: 22px;
      font-weight: 800;
      color: var(--main);
      line-height: 1;
    }

    .bottom-nav-old {
      font-size: 13px;
      color: #999;
      text-decoration: line-through;
    }

    .bottom-nav-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: var(--main);
      color: #fff;
      padding: 12px 28px;
      font-size: 16px;
      font-weight: 700;
      border-radius: 4px;
      text-decoration: none;
      box-shadow: 0 4px 14px rgba(39, 91, 87, 0.28);
      transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
      white-space: nowrap;
    }

    .bottom-nav-btn:hover {
      opacity: 0.95;
      transform: translateY(-1px);
      color: #fff;
    }

    /* Responsive */
    @media(max-width:800px) {
      .hero,
      .overview,
      .order-grid {
        grid-template-columns: 1fr;
      }

      .order-summary-card {
        position: static;
      }

      .order-fields-card {
        padding: 22px 18px;
      }

      .delivery-options {
        grid-template-columns: 1fr;
      }

      .hero-slider {
        height: 480px;
      }

      .details h1 {
        font-size: 32px;
      }

      .review {
        flex: 0 0 calc(100% - 10px);
      }

      .overview img {
        height: 380px;
      }
    }

    @media(max-width:520px) {
      .hero-slider {
        height: 400px;
      }
      .details h1 {
        font-size: 28px;
      }
      .price {
        font-size: 28px;
      }
      .bottom-nav-price {
        font-size: 19px;
      }
      .bottom-nav-btn {
        padding: 10px 18px;
        font-size: 15px;
      }
    }
  </style>
</head>

<body>

  <!-- Header -->
  <header>
    <div class="wrap nav">
      <a href="{{ route('home') }}" class="logo">
        @php
          $siteLogo = get_setting('site_logo');
        @endphp
        @if($siteLogo != null && !empty($siteLogo->value))
          <img src="{{ asset($siteLogo->value) }}" alt="{{ get_setting('site_name')->value ?? 'ROH CLOTHS' }}">
        @else
          {{ get_setting('site_name')->value ?? 'ROH CLOTHS' }}
        @endif
      </a>
      <a href="#order" class="nav-btn">অর্ডার করুন</a>
    </div>
  </header>

  <!-- Hero Section -->
  <main class="wrap hero">
    <div class="hero-slider">
      <div class="hero-slides" id="heroSlides">
        @forelse($sliderImages as $sImg)
          <img src="{{ asset($sImg) }}" alt="{{ $landingPage->title }}" id="mainHeroSlideImg">
        @empty
          <img src="{{ asset($product->product_thumbnail ?? 'upload/no_image.jpg') }}" alt="{{ $landingPage->title }}">
        @endforelse
      </div>
      @if(count($sliderImages) > 1)
        <button class="slide-btn prev" type="button" onclick="moveHero(-1)">‹</button>
        <button class="slide-btn next" type="button" onclick="moveHero(1)">›</button>
        <div class="dots" id="heroDots">
          @foreach($sliderImages as $k => $sImg)
            <button class="{{ $k == 0 ? 'active' : '' }}" onclick="goHero({{ $k }})"></button>
          @endforeach
        </div>
      @endif
    </div>

    <div class="details">
      <div class="tag">{{ $landingPage->hero_tag ?: 'PREMIUM COLLECTION' }}</div>
      <h1>{{ $landingPage->title }}</h1>
      <p class="lead">{{ $landingPage->sub_title ?: ($product->name_bn ?? $product->name_en) }}</p>

      <div class="pricebox">
        <span class="price" id="heroPrice">৳ {{ number_format($landingPage->sale_price, 0) }}</span>
        @if($landingPage->regular_price && $landingPage->regular_price > $landingPage->sale_price)
          <span class="old" id="heroOldPrice">৳ {{ number_format($landingPage->regular_price, 0) }}</span>
        @endif
        @if(!empty($landingPage->discount_text))
          <span class="discount" id="heroDiscount">{{ $landingPage->discount_text }}</span>
        @endif
        <div class="stock">
          <i class="fa fa-box"></i>
          <span id="stockStatusText">{{ $landingPage->stock_text ?: 'বর্তমান স্টক: সীমিত' }}</span>
        </div>
        <div class="bar"><i></i></div>
      </div>

      <a class="orderbtn" href="#order">
        <i class="fa fa-shopping-bag me-1"></i> অর্ডার করুন — <span id="heroOrderBtnPrice">৳ {{ number_format($landingPage->sale_price, 0) }}</span>
      </a>

      @if(!empty($features) && count($features) > 0)
        <div class="notes">
          @foreach($features as $feat)
            <span><i class="fa fa-check-circle text-success"></i> {{ $feat }}</span>
          @endforeach
        </div>
      @endif
    </div>
  </main>

  <!-- Product Full Overview Section -->
  <section class="section">
    <div class="wrap">
      <h2>{{ $landingPage->overview_title ?: 'Product Full Overview' }}</h2>
      <p class="section-sub">{{ $landingPage->overview_sub_title ?: 'প্রোডাক্ট সম্পর্কে দরকারি তথ্য এক জায়গায়।' }}</p>

      <div class="overview">
        <img src="{{ asset($overviewImage) }}" alt="{{ $landingPage->title }}">
        <div class="overview-text">
          <h3>{{ $landingPage->title }}</h3>
          <p>{!! nl2br(e($landingPage->overview_text ?: ($product->description_en ?? $product->description_bn))) !!}</p>
          
          @if(!empty($overviewPoints) && count($overviewPoints) > 0)
            <ul class="overview-list">
              @foreach($overviewPoints as $point)
                <li>{{ $point }}</li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>

      @if(!empty($landingPage->video_url))
        <div class="video-container">
          <iframe src="{{ $landingPage->video_url }}" allowfullscreen></iframe>
        </div>
      @endif
    </div>
  </section>

  <!-- Customer Reviews Section -->
  @if(!empty($reviews) && count($reviews) > 0)
  <section class="section">
    <div class="wrap">
      <h2>Customer Reviews</h2>
      <p class="section-sub">আমাদের সন্তুষ্ট কাস্টমারদের মতামত।</p>

      <div class="review-slider">
        <div class="review-track" id="reviewTrack">
          @foreach($reviews as $rev)
            <div class="review">
              <div class="stars">
                @for($s = 0; $s < ($rev['stars'] ?? 5); $s++)★@endfor
              </div>
              <p>“{{ $rev['comment'] ?? '' }}”</p>
              <strong>{{ $rev['name'] ?? 'সম্মানিত কাস্টমার' }}</strong>
              <small>{{ $rev['location'] ?? 'ঢাকা' }}</small>
            </div>
          @endforeach
        </div>
        @if(count($reviews) > 1)
          <button class="review-btn review-prev" type="button" onclick="moveReview(-1)">‹</button>
          <button class="review-btn review-next" type="button" onclick="moveReview(1)">›</button>
        @endif
      </div>
    </div>
  </section>
  @endif

  <!-- ========================================================
       ORDER SECTION: DYNAMIC ORDER FORM & STICKY SUMMARY
       ======================================================== -->
  <section class="section order-area" id="order">
    <div class="wrap">
      <form class="order-grid" id="landingOrderForm" action="{{ route('landing.order.store') }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" id="orderQty" value="1">
        <input type="hidden" name="varient" id="finalVariant" value="">
        <input type="hidden" name="shipping_type" id="shippingTypeInput" value="1">
        
        <!-- Left Side: Order Fields -->
        <div class="order-fields-card">
          
          <!-- Multiple Attribute Selectors -->
          @if(!empty($structuredAttributes) && count($structuredAttributes) > 0)
            @foreach($structuredAttributes as $attr)
              <div class="attribute-group" data-attr-id="{{ $attr['id'] }}">
                <label class="attribute-label">{{ $attr['name_bn'] ?? $attr['name'] }} নির্বাচন করুন <span style="color:var(--maroon)">*</span></label>
                <div class="attribute-pills">
                  @foreach($attr['values'] as $vIndex => $val)
                    <label class="attribute-pill-label">
                      <input type="radio" 
                             name="attr_{{ $attr['id'] }}" 
                             value="{{ $val }}" 
                             class="attribute-radio" 
                             data-attr-name="{{ $attr['name'] }}"
                             {{ $vIndex == 0 ? 'checked' : '' }} 
                             onchange="onVariantChange()">
                      <span class="attribute-pill-btn">{{ $val }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            @endforeach
          @endif

          <!-- Customer Name -->
          <div class="form-group">
            <label for="customerName">আপনার নাম <span style="color:var(--maroon)">*</span></label>
            <input type="text" id="customerName" name="name" required placeholder="পুরো নাম লিখুন">
          </div>

          <!-- Customer Phone -->
          <div class="form-group">
            <label for="customerPhone">মোবাইল নম্বর <span style="color:var(--maroon)">*</span></label>
            <input type="tel" id="customerPhone" name="phone" required placeholder="01XXXXXXXXX" pattern="^(\+?88)?01[3-9]\d{8}$">
          </div>

          <!-- Customer Address -->
          <div class="form-group">
            <label for="customerAddress">সম্পূর্ণ ঠিকানা <span style="color:var(--maroon)">*</span></label>
            <textarea id="customerAddress" name="address" required placeholder="বাসা/হোল্ডিং নং, রোড, থানা, জেলা"></textarea>
          </div>

          <!-- Delivery Area -->
          <div class="form-group">
            <label>ডেলিভারি এলাকা <span style="color:var(--maroon)">*</span></label>
            <div class="delivery-options">
              <label class="delivery-option">
                <input type="radio" name="delivery_area" value="{{ $delivery_inside }}" data-type="1" checked onchange="updateDelivery(this)">
                <span class="delivery-option-text">ঢাকার ভিতরে — ৳{{ number_format($delivery_inside, 0) }}</span>
              </label>
              <label class="delivery-option">
                <input type="radio" name="delivery_area" value="{{ $delivery_outside }}" data-type="2" onchange="updateDelivery(this)">
                <span class="delivery-option-text">ঢাকার বাইরে — ৳{{ number_format($delivery_outside, 0) }}</span>
              </label>
            </div>
          </div>

          <!-- Special Note / Comment -->
          <div class="form-group">
            <label for="orderComment">বিশেষ কোনো নির্দেশনা (ঐচ্ছিক)</label>
            <input type="text" id="orderComment" name="comment" placeholder="যেমন: ডেলিভারির সময় বা বিশেষ নোট">
          </div>
        </div>

        <!-- Right Side: Sticky Order Summary -->
        <div class="order-summary-card">
          <div class="summary-header">
            <svg class="summary-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
              <line x1="8" y1="8" x2="16" y2="8"></line>
              <line x1="8" y1="12" x2="16" y2="12"></line>
              <line x1="8" y1="16" x2="13" y2="16"></line>
            </svg>
            <h3>অর্ডার সারাংশ</h3>
          </div>

          <div class="summary-body">
            <div class="summary-item">
              <span class="summary-item-label">প্যাকেজ</span>
              <span class="summary-item-val" id="summaryProductName">{{ $landingPage->title }} × ১</span>
            </div>

            <div class="summary-item" id="summaryVariantRow" style="display: none;">
              <span class="summary-item-label">ভ্যারিয়েন্ট</span>
              <span class="summary-item-val" id="summaryVariantText" style="color: var(--maroon);"></span>
            </div>

            <div class="summary-item">
              <span class="summary-item-label">পণ্যের মূল্য</span>
              <span class="summary-item-val" id="summaryProductPrice">৳ {{ number_format($landingPage->sale_price, 0) }}</span>
            </div>

            <div class="summary-item">
              <span class="summary-item-label">ডেলিভারি চার্জ</span>
              <span class="summary-item-val" id="summaryDeliveryCharge">৳ {{ number_format($delivery_inside, 0) }}</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-total-row">
              <span class="summary-total-label">সর্বমোট (COD)</span>
              <span class="summary-total-val" id="summaryGrandTotal">৳ {{ number_format($landingPage->sale_price + $delivery_inside, 0) }}</span>
            </div>

            <button type="submit" class="order-submit-btn" id="confirmOrderBtn">
              <i class="fa fa-lock me-1"></i> অর্ডার কনফার্ম করুন
            </button>
            <p class="order-terms-note">অর্ডার করে আমাদের শর্তাবলীতে সম্মত হচ্ছেন। ক্যাশ অন ডেলিভারি প্রযোজ্য।</p>
          </div>
        </div>

      </form>
    </div>
  </section>

  <!-- FAQs Section -->
  @if(!empty($faqs) && count($faqs) > 0)
  <section class="section">
    <div class="wrap">
      <h2>Frequently Asked Questions</h2>
      <p class="section-sub">অর্ডারের আগে যেসব সাধারণ প্রশ্ন কাস্টমাররা জানতে চান।</p>

      <div class="faq">
        @foreach($faqs as $faq)
          <details>
            <summary>{{ $faq['question'] ?? '' }}</summary>
            <p>{{ $faq['answer'] ?? '' }}</p>
          </details>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  <!-- Footer -->
  <footer>
    <div class="wrap">
      © {{ date('Y') }} {{ get_setting('site_name')->value ?? 'ROH CLOTHS' }}. All rights reserved.
    </div>
  </footer>

  <!-- Sticky Bottom Navigation -->
  <div class="bottom-nav" id="bottomNav">
    <div class="wrap bottom-nav-wrap">
      <div class="bottom-nav-left">
        <span class="bottom-nav-label">সর্বমোট মূল্য (ডেলিভারিসহ)</span>
        <div class="bottom-nav-price-group">
          <span class="bottom-nav-price" id="bottomTotal">৳ {{ number_format($landingPage->sale_price + $delivery_inside, 0) }}</span>
          @if($landingPage->regular_price && $landingPage->regular_price > $landingPage->sale_price)
            <span class="bottom-nav-old" id="bottomOldPrice">৳ {{ number_format($landingPage->regular_price, 0) }}</span>
          @endif
        </div>
      </div>
      <div class="bottom-nav-right">
        <a href="#order" class="bottom-nav-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
          </svg>
          <span>অর্ডার করুন</span>
        </a>
      </div>
    </div>
  </div>

  <!-- JavaScript Logic -->
  <script>
    // 1. Data from Backend
    const baseProductPrice = {{ (float)($landingPage->sale_price ?? $product->regular_price) }};
    const structuredAttributes = @json($structuredAttributes);
    const productStocks = @json($productStocks);
    let currentDeliveryCharge = {{ (float)$delivery_inside }};
    let currentUnitProductPrice = baseProductPrice;

    // Bengali numeral helper
    const bnMap = { '0': '০', '1': '১', '2': '২', '3': '৩', '4': '৪', '5': '৫', '6': '৬', '7': '৭', '8': '৮', '9': '৯' };
    function toBn(num) {
      return String(Math.round(num)).replace(/\B(?=(\d{3})+(?!\d))/g, ',').replace(/[0-9]/g, d => bnMap[d] || d);
    }

    // 2. Hero Slider
    let heroIndex = 0;
    const heroSlides = document.getElementById('heroSlides');
    const heroDots = [...document.querySelectorAll('#heroDots button')];
    const totalSlides = heroDots.length;

    function showHero() {
      if (!heroSlides || totalSlides <= 1) return;
      heroSlides.style.transform = `translateX(-${heroIndex * 100}%)`;
      heroDots.forEach((d, i) => d.classList.toggle('active', i === heroIndex));
    }

    function moveHero(n) {
      if (totalSlides <= 1) return;
      heroIndex = (heroIndex + n + totalSlides) % totalSlides;
      showHero();
    }

    function goHero(i) {
      if (totalSlides <= 1) return;
      heroIndex = i;
      showHero();
    }

    if (totalSlides > 1) {
      setInterval(() => moveHero(1), 4000);
    }

    // 3. Customer Reviews Slider
    let reviewIndex = 0;
    const reviewTrack = document.getElementById('reviewTrack');
    const allReviews = document.querySelectorAll('.review');
    function visibleReviews() { return window.innerWidth <= 800 ? 1 : 3; }
    function maxReview() { return Math.max(0, allReviews.length - visibleReviews()); }

    function showReview() {
      if (!reviewTrack || allReviews.length <= 1) return;
      const step = 100 / visibleReviews();
      reviewIndex = Math.min(reviewIndex, maxReview());
      reviewTrack.style.transform = `translateX(-${reviewIndex * step}%)`;
    }

    function moveReview(n) {
      if (!reviewTrack || allReviews.length <= 1) return;
      reviewIndex += n;
      if (reviewIndex > maxReview()) reviewIndex = 0;
      if (reviewIndex < 0) reviewIndex = maxReview();
      showReview();
    }

    if (allReviews.length > 3) {
      setInterval(() => moveReview(1), 5000);
    }
    window.addEventListener('resize', showReview);

    // 4. Multi-Attribute & Variant Resolution
    function onVariantChange() {
      if (!structuredAttributes || structuredAttributes.length === 0) {
        return;
      }

      // Collect selected value for each attribute
      const selectedParts = [];
      const displayParts = [];

      structuredAttributes.forEach(attr => {
        const checkedRadio = document.querySelector(`input[name="attr_${attr.id}"]:checked`);
        if (checkedRadio) {
          selectedParts.push(checkedRadio.value.trim());
          displayParts.push(`${attr.name_bn || attr.name}: ${checkedRadio.value.trim()}`);
        }
      });

      // Combined variant identifier (e.g. "38-Stitched")
      const combinedVariant = selectedParts.join('-');
      document.getElementById('finalVariant').value = combinedVariant;

      // Update Summary Variant Display
      const summaryVariantRow = document.getElementById('summaryVariantRow');
      const summaryVariantText = document.getElementById('summaryVariantText');
      if (displayParts.length > 0) {
        summaryVariantRow.style.display = 'flex';
        summaryVariantText.innerText = displayParts.join(' | ');
      }

      // Check against productStocks
      let matchedStock = null;
      if (productStocks && productStocks.length > 0) {
        matchedStock = productStocks.find(s => {
          return s.varient && s.varient.toLowerCase().trim() === combinedVariant.toLowerCase().trim();
        });
      }

      if (matchedStock) {
        // Update Price if stock has custom price
        if (matchedStock.price && parseFloat(matchedStock.price) > 0) {
          currentUnitProductPrice = parseFloat(matchedStock.price);
        } else {
          currentUnitProductPrice = baseProductPrice;
        }

        // Update Stock text
        const stockStatusText = document.getElementById('stockStatusText');
        if (stockStatusText) {
          if (matchedStock.qty > 0) {
            stockStatusText.innerText = `ইন স্টক: ${toBn(matchedStock.qty)} টি উপলব্ধ`;
          } else {
            stockStatusText.innerText = 'স্টক শেষ';
          }
        }

        // If stock has specific image, swap main slide image
        if (matchedStock.image) {
          const firstImg = document.getElementById('mainHeroSlideImg');
          if (firstImg) {
            firstImg.src = '{{ asset("") }}' + matchedStock.image;
          }
        }
      } else {
        currentUnitProductPrice = baseProductPrice;
      }

      recalculateTotals();
    }

    // 5. Delivery Change
    function updateDelivery(radio) {
      currentDeliveryCharge = parseFloat(radio.value);
      document.getElementById('shippingTypeInput').value = radio.getAttribute('data-type') || '1';
      document.getElementById('summaryDeliveryCharge').innerText = '৳ ' + toBn(currentDeliveryCharge);
      recalculateTotals();
    }

    // 6. Recalculate Totals
    function recalculateTotals() {
      const grandTotal = currentUnitProductPrice + currentDeliveryCharge;

      // Hero price elements
      document.getElementById('heroPrice').innerText = '৳ ' + toBn(currentUnitProductPrice);
      const heroOrderBtnPrice = document.getElementById('heroOrderBtnPrice');
      if (heroOrderBtnPrice) {
        heroOrderBtnPrice.innerText = '৳ ' + toBn(currentUnitProductPrice);
      }

      // Summary Card
      document.getElementById('summaryProductPrice').innerText = '৳ ' + toBn(currentUnitProductPrice);
      document.getElementById('summaryGrandTotal').innerText = '৳ ' + toBn(grandTotal);

      // Bottom Bar
      const bottomTotal = document.getElementById('bottomTotal');
      if (bottomTotal) {
        bottomTotal.innerText = '৳ ' + toBn(grandTotal);
      }
    }

    // Initial variant trigger on page load
    document.addEventListener('DOMContentLoaded', () => {
      onVariantChange();

      // Smooth AJAX Order Form Submission
      const form = document.getElementById('landingOrderForm');
      const submitBtn = document.getElementById('confirmOrderBtn');

      if (form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();

          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }

          submitBtn.disabled = true;
          const origHtml = submitBtn.innerHTML;
          submitBtn.innerHTML = '<i class="fa fa-circle-notch fa-spin"></i> অর্ডার প্রক্রিয়া করা হচ্ছে...';

          const formData = new FormData(form);

          fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
            }
          })
          .then(async response => {
            const data = await response.json();
            if (response.ok && data.success && data.redirect_url) {
              window.location.href = data.redirect_url;
            } else {
              alert(data.message || 'অর্ডার প্রক্রিয়া করার সময় ত্রুটি হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।');
              submitBtn.disabled = false;
              submitBtn.innerHTML = origHtml;
            }
          })
          .catch(err => {
            console.error('Order submission error:', err);
            // Fallback to standard form submission
            form.submit();
          });
        });
      }
    });
  </script>

</body>

</html>