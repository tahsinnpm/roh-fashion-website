@extends('FrontEnd.master')
@section('title', $product->name_en . ' Details')

@php
    $isBangla = session('language') === 'bangla';
    $productName = $isBangla ? ($product->name_bn ?: $product->name_en) : $product->name_en;
    $youtubeUrl = $product->youtube_video_url;
    $youtubeVideoId = null;
    if ($youtubeUrl) {
        $youtubeParts = parse_url($youtubeUrl);
        $youtubeHost = strtolower($youtubeParts['host'] ?? '');
        if (str_contains($youtubeHost, 'youtu.be')) {
            $youtubeVideoId = trim($youtubeParts['path'] ?? '', '/');
        } elseif (str_contains($youtubeHost, 'youtube.com')) {
            parse_str($youtubeParts['query'] ?? '', $youtubeQuery);
            $youtubePath = trim($youtubeParts['path'] ?? '', '/');
            $youtubeVideoId = $youtubeQuery['v'] ?? (preg_match('~^(?:embed|shorts)/([^/]+)~', $youtubePath, $videoMatch) ? $videoMatch[1] : null);
        }
        $youtubeVideoId = is_string($youtubeVideoId) && preg_match('/^[A-Za-z0-9_-]{6,20}$/', $youtubeVideoId) ? $youtubeVideoId : null;
    }
    $regularPrice = (float) $product->regular_price;
    $discountType = (int) $product->discount_type;
    $discountValue = (float) $product->discount_price;
    $minimumQuantity = max(1, (int) $product->minimum_buy_qty);

    $productStocks = $product->stocks;
    $hasVariants = ($product->is_varient == 1 && $productStocks && $productStocks->count() > 0);

    if ($hasVariants) {
        $sortedStocks = $productStocks->sortBy('price');
        $firstStock = $sortedStocks->firstWhere('qty', '>', 0) ?: $sortedStocks->first();
        $selectedVariantName = $firstStock ? $firstStock->varient : '';
        $currentPrice = $firstStock && $firstStock->price > 0 ? (float) $firstStock->price : (float) $product->storefrontPrice();
        $availableStock = $firstStock ? (int) ($firstStock->qty ?? 0) : 0;
        $variantRegularPrice = $regularPrice;
    } else {
        $selectedVariantName = '';
        $currentPrice = (float) $product->storefrontPrice();
        $availableStock = (int) $product->stock_qty;
        $variantRegularPrice = $regularPrice;
    }

    $discountPercent = $variantRegularPrice > $currentPrice && $variantRegularPrice > 0
        ? round(($variantRegularPrice - $currentPrice) / $variantRegularPrice * 100)
        : 0;

    $productAttributes = json_decode($product->attribute_values ?: '[]') ?: [];

    // Variants Map for JavaScript (matching landing page dynamic.blade.php)
    $variantsMap = [];
    if ($hasVariants) {
        foreach ($productStocks as $stk) {
            $stkPrice = $stk->price > 0 ? (float) $stk->price : (float) $product->storefrontPrice();
            $variantsMap[$stk->varient] = [
                'id' => $stk->id,
                'varient' => $stk->varient,
                'price' => $stkPrice,
                'regular_price' => $regularPrice,
                'qty' => (int) ($stk->qty ?? 0),
                'image' => $stk->image ? asset(ltrim($stk->image, '/')) : null,
            ];
        }
    }

    // Contact & Social channels
    $fbUrl = get_setting('facebook_url')->value ?? 'https://www.facebook.com';
    $fbChatUrl = $fbUrl;
    if (preg_match('/id=(\d+)/', $fbUrl, $matches)) {
        $fbChatUrl = 'https://m.me/' . $matches[1];
    } elseif (preg_match('/facebook\.com\/([a-zA-Z0-9\.\_]+)/', $fbUrl, $matches) && !in_array($matches[1], ['profile.php', 'sharer'])) {
        $fbChatUrl = 'https://m.me/' . $matches[1];
    }
    $phoneSetting = get_setting('phone')->value ?? '01838389494';
    $cleanPhoneForWa = preg_replace('/[^0-9]/', '', $phoneSetting);
    if (!str_starts_with($cleanPhoneForWa, '88') && str_starts_with($cleanPhoneForWa, '01')) {
        $cleanPhoneForWa = '88' . $cleanPhoneForWa;
    }
    $waText = urlencode('Hello, I want to know more about: ' . $productName . ' (' . url()->current() . ')');
    $waUrl = 'https://api.whatsapp.com/send?phone=' . $cleanPhoneForWa . '&text=' . $waText;
    $relatedProducts = $relatedProducts ?? \App\Models\Product::with(['stocks', 'category'])->where('status', 1)->where('id', '!=', $product->id)->where('category_id', $product->category_id)->latest('id')->limit(8)->get();
@endphp

@push('styles')
    <style>
        .pdp-page {
            max-width: 1360px;
            margin: 0 auto;
            padding: 24px 24px 56px;
            color: #171717;
        }

        .pdp-breadcrumb {
            margin: 0 0 18px;
            color: #777;
            font-size: 13px;
        }

        .pdp-breadcrumb a {
            color: inherit;
            text-decoration: none;
        }

        .pdp-layout {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 3fr);
            gap: clamp(24px, 3.5vw, 48px);
            align-items: start;
        }

        .pdp-gallery {
            min-width: 0;
        }

        .pdp-image-frame { position: relative; }

        .pdp-slide-arrow {
            position: absolute;
            z-index: 2;
            top: 50%;
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 50%;
            background: rgba(255,255,255,.92);
            color: #111;
            font-size: 20px;
            cursor: pointer;
            transform: translateY(-50%);
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
        }

        .pdp-slide-arrow[hidden] { display: none; }
        .pdp-slide-arrow--prev { left: 12px; }
        .pdp-slide-arrow--next { right: 12px; }

        .pdp-video-poster {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            border: 0;
            background: #161616;
            color: white;
            font-size: 58px;
            cursor: pointer;
        }
        .pdp-video-poster i { filter: drop-shadow(0 2px 8px #000); }
        .pdp-video-thumb { display:grid; place-items:center; height:100%; background:#181818; color:#fff; font-size:25px; }

        .pdp-video-modal {
            width: min(900px, calc(100vw - 28px));
            max-width: none;
            padding: 0;
            border: 0;
            background: #000;
        }
        .pdp-video-modal::backdrop { background: rgba(0,0,0,.82); }
        .pdp-video-modal__close { position:absolute; z-index:2; top:8px; right:8px; width:40px; height:40px; border:0; border-radius:50%; background:#fff; font-size:24px; cursor:pointer; }
        .pdp-video-modal__frame { display:block; width:100%; aspect-ratio:16/9; border:0; }

        .pdp-image-frame {
            position: relative;
            width: 100%;
            background: #f7f7f7;
        }

        .pdp-image-frame.is-video-slide {
            aspect-ratio: 4 / 5;
            overflow: hidden;
        }

        .pdp-image-frame img {
            display: block;
            width: 100%;
            height: auto;
        }

        .pdp-thumbnails {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            overflow-x: auto;
        }

        .pdp-thumbnail {
            flex: 0 0 68px;
            width: 68px;
            height: 82px;
            padding: 0;
            overflow: hidden;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
        }

        .pdp-thumbnail[aria-current="true"] {
            border: 2px solid #111;
        }

        .pdp-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pdp-info {
            min-width: 0;
        }

        .pdp-title {
            margin: 0 0 10px;
            color: #171717;
            font-size: clamp(28px, 3vw, 40px);
            font-weight: 400;
            line-height: 1.15;
        }

        .pdp-price {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 10px;
            margin: 0 0 8px;
            font-size: 17px;
            letter-spacing: .04em;
        }

        .pdp-price strong {
            font-weight: 500;
        }

        .pdp-price del {
            color: #777;
            font-size: 14px;
        }

        .pdp-price__save {
            color: #555;
            font-size: 13px;
        }

        .pdp-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            margin: 0 0 14px;
            color: #666;
            font-size: 13px;
        }

        .pdp-meta strong {
            color: #222;
            font-weight: 500;
        }

        .pdp-variant-block {
            margin: 0 0 14px;
        }

        .pdp-variant-lbl {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .pdp-variant-lbl strong {
            color: #000000;
            font-weight: 700;
        }

        .pdp-variant-select {
            width: 100%;
            min-height: 48px;
            padding: 10px 40px 10px 14px;
            border: 1px solid #888888ff;
            border-radius: 6px;
            background: #fff;
            color: #171717;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23880e4f' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 18px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .pdp-variant-select:focus {
            outline: none;
            border-color: #000000;
            box-shadow: 0 0 0 3px rgba(136, 14, 79, .15);
        }

        /* Quantity Selector matching image */
        .rf-pdp-qty-wrapper {
            margin: 0 0 14px;
        }

        .rf-pdp-qty-box {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid #e4e4e7;
            border-radius: 8px;
            background: #ffffff;
            overflow: hidden;
            height: 46px;
        }

        .rf-pdp-qty-btn {
            width: 46px;
            height: 46px;
            border: 0;
            background: #ffffff;
            color: #111111;
            font-size: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s ease;
            padding: 0;
        }

        .rf-pdp-qty-btn:hover {
            background: #f4f4f5;
        }

        .rf-pdp-qty-input {
            width: 54px;
            height: 46px;
            border-top: 0;
            border-bottom: 0;
            border-left: 1.5px solid #e4e4e7;
            border-right: 1.5px solid #e4e4e7;
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            color: #111111;
            background: #ffffff;
            outline: none;
            -moz-appearance: textfield;
        }

        .rf-pdp-qty-input::-webkit-outer-spin-button,
        .rf-pdp-qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Actions Stack */
        .rf-pdp-actions-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 14px;
        }

        /* Add To Cart - Black & White Outline */
        .rf-pdp-btn-cart {
            width: 100%;
            min-height: 48px;
            border: 1px solid #888888ff;
            background: #ffffff;
            color: #09090b;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            text-transform: none;
        }

        .rf-pdp-btn-cart:hover {
            background: #09090b;
            color: #ffffff;
        }

        /* Buy Now - Solid Black with continuous, non-pausing periodic shake */
        .rf-pdp-btn-buy {
            width: 100%;
            min-height: 50px;
            border: 1px solid #09090b;
            background: #09090b;
            color: #ffffff;
            border-radius: 8px;
            font-size: 16.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            text-transform: none;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            will-change: transform;
            animation: rf-buy-shake-2s 2.2s infinite ease-in-out;
            -webkit-animation: rf-buy-shake-2s 2.2s infinite ease-in-out;
        }

        .rf-pdp-btn-buy:hover {
            background: #27272a;
            border-color: #27272a;
        }

        @keyframes rf-buy-shake-2s {
            0%, 52%, 100% {
                transform: translate3d(0, 0, 0);
            }
            8% {
                transform: translate3d(-5px, 0, 0) rotate(-1.5deg);
            }
            16% {
                transform: translate3d(5px, 0, 0) rotate(1.5deg);
            }
            24% {
                transform: translate3d(-4px, 0, 0) rotate(-1deg);
            }
            32% {
                transform: translate3d(4px, 0, 0) rotate(1deg);
            }
            38% {
                transform: translate3d(-2px, 0, 0) rotate(-0.5deg);
            }
            44% {
                transform: translate3d(2px, 0, 0);
            }
            48% {
                transform: translate3d(0, 0, 0);
            }
        }

        @-webkit-keyframes rf-buy-shake-2s {
            0%, 52%, 100% {
                -webkit-transform: translate3d(0, 0, 0);
            }
            8% {
                -webkit-transform: translate3d(-5px, 0, 0) rotate(-1.5deg);
            }
            16% {
                -webkit-transform: translate3d(5px, 0, 0) rotate(1.5deg);
            }
            24% {
                -webkit-transform: translate3d(-4px, 0, 0) rotate(-1deg);
            }
            32% {
                -webkit-transform: translate3d(4px, 0, 0) rotate(1deg);
            }
            38% {
                -webkit-transform: translate3d(-2px, 0, 0) rotate(-0.5deg);
            }
            44% {
                -webkit-transform: translate3d(2px, 0, 0);
            }
            48% {
                -webkit-transform: translate3d(0, 0, 0);
            }
        }

        /* Three Social Buttons Row */
        .rf-pdp-social-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .rf-pdp-social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 46px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            color: #ffffff !important;
            transition: opacity 0.2s ease, transform 0.15s ease;
            border: none;
        }

        .rf-pdp-social-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .rf-btn-chat {
            background-color: #1877F2;
        }

        .rf-btn-whatsapp {
            background-color: #1E984D;
        }

        .rf-btn-call {
            background-color: #231F20;
        }

        /* Categories Box */
        .rf-pdp-categories-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 14.5px;
            color: #4b5563;
        }

        .rf-pdp-cat-link {
            color: #000000;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.15s;
        }

        .rf-pdp-cat-link:hover {
            color: #09090b;
            text-decoration: underline;
        }

        .pdp-stock-alert,
        .pdp-attribute-alert {
            font-size: 13px;
            color: #dc2626;
            font-weight: 500;
        }

        .pdp-stock-alert:empty,
        .pdp-attribute-alert:empty {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
            min-height: 0 !important;
            height: 0 !important;
        }

        .pdp-stock-alert:not(:empty),
        .pdp-attribute-alert:not(:empty) {
            display: block;
            margin-top: 6px;
        }

        .pdp-shipping {
            display: grid;
            gap: 12px;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #ddd;
            color: #555;
            font-size: 13px;
        }

        .pdp-shipping p {
            margin: 0;
        }

        .pdp-description {
            margin-top: 38px;
            padding-top: 24px;
            border-top: 1px solid #ddd;
        }

        .pdp-description h2 {
            margin: 0 0 14px;
            color: #171717;
            font-size: 22px;
            font-weight: 500;
        }

        .pdp-description__body {
            color: #444;
            line-height: 1.8;
        }

        .pdp-description__body img {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 767px) {
            .pdp-page {
                padding: 14px 14px 36px;
            }

            .rf-pdp-social-grid {
                grid-template-columns: repeat(1, 1fr);
            }

            .pdp-layout {
                grid-template-columns: minmax(0, 1fr);
                gap: 22px;
            }

            .pdp-image-frame {
                min-height: 0;
            }

            .pdp-title {
                font-size: 29px;
            }

            .pdp-description {
                margin-top: 28px;
            }
        }

        /* Related Products Section */
        .rf-related-section {
            margin-top: 56px;
            padding-top: 42px;
            border-top: 1px solid #e5e7eb;
        }

        .rf-related-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .rf-related-subtitle {
            font-size: 11.5px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #71717a;
            font-weight: 700;
            display: block;
            margin-bottom: 6px;
        }

        .rf-related-title {
            font-size: clamp(22px, 2.5vw, 30px);
            font-weight: 600;
            color: #09090b;
            margin: 0;
            letter-spacing: -0.01em;
        }

        /* Product page visual refresh */
        .pdp-page {
            max-width: 1440px;
            padding: 22px 28px 64px;
            color: #302727;
        }

        .pdp-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #8c817d;
            font-size: 12px;
        }

        .pdp-breadcrumb a:hover { color: #292524; }

        .pdp-layout {
            grid-template-columns: minmax(0, 1.02fr) minmax(0, .98fr);
            gap: clamp(28px, 5vw, 68px);
        }

        .pdp-gallery { position: sticky; top: 112px; }

        .pdp-image-frame {
            aspect-ratio: 4 / 5;
            overflow: hidden;
            border-radius: 10px;
            background: #f7f3f0;
        }

        .pdp-image-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .pdp-thumbnail {
            flex-basis: 72px;
            width: 72px;
            height: 86px;
            border-color: #e9dfda;
            border-radius: 5px;
        }

        .pdp-thumbnail[aria-current="true"] { border: 2px solid #292524; }

        .pdp-info {
            padding: clamp(22px, 3vw, 38px);
            border: 1px solid #eee6e1;
            border-radius: 10px;
            background: #fffdfb;
            box-shadow: 0 12px 36px rgba(53, 31, 28, .045);
        }

        .pdp-title {
            margin-bottom: 14px;
            color: #261f1e;
            font-size: clamp(27px, 3vw, 38px);
            font-weight: 600;
            letter-spacing: -.025em;
        }

        .pdp-price {
            gap: 8px 12px;
            margin-bottom: 12px;
            color: #292524;
            font-size: 18px;
            letter-spacing: 0;
        }

        .pdp-price strong { font-size: 26px; font-weight: 700; }
        .pdp-price del { color: #9b918d; }
        .pdp-price__save { color: #292524; font-weight: 600; }

        .pdp-meta {
            gap: 8px;
            margin: 0 0 22px;
            padding-bottom: 18px;
            border-bottom: 1px solid #eee6e1;
            color: #796e6a;
        }

        .pdp-meta strong { color: #514441; }
        .pdp-meta .in-stock { color: #27734a; }
        .pdp-meta .out-of-stock { color: #b42337; }

        .pdp-variant-block { margin-bottom: 18px; }
        .pdp-variant-lbl { color: #514441; }
        .pdp-variant-lbl strong { color: #292524; }
        .pdp-variant-select {
            border-color: #e6dcd7;
            border-radius: 6px;
            font-weight: 500;
        }
        .pdp-variant-select:focus {
            border-color: #292524;
            box-shadow: 0 0 0 3px rgba(41, 37, 36, .1);
        }

        .rf-pdp-qty-wrapper { margin: 0 0 16px; }
        .rf-pdp-qty-box { border-color: #e6dcd7; border-radius: 6px; }
        .rf-pdp-qty-btn { color: #514441; }
        .rf-pdp-qty-btn:hover { background: #f8f2ef; color: #292524; }
        .rf-pdp-qty-input { color: #302727; }

        .rf-pdp-actions-stack { gap: 9px; margin-bottom: 18px; }
        .rf-pdp-btn-cart,
        .rf-pdp-btn-buy {
            min-height: 50px;
            border-radius: 5px;
            font-size: 15px;
            animation: none;
            -webkit-animation: none;
            box-shadow: none;
        }
        .rf-pdp-btn-cart {
            border-color: #292524;
            color: #292524;
        }
        .rf-pdp-btn-cart:hover { background: #f4f1ef; color: #111111; }
        .rf-pdp-btn-buy { border-color: #292524; background: #292524; }
        .rf-pdp-btn-buy:hover { border-color: #111111; background: #111111; }

        .rf-pdp-social-grid { gap: 8px; margin-bottom: 16px; }
        .rf-pdp-social-btn { height: 42px; border-radius: 5px; font-size: 12px; }
        .rf-pdp-categories-box {
            border-color: #eee6e1;
            border-radius: 6px;
            background: #fff;
            color: #796e6a;
        }
        .rf-pdp-cat-link { color: #292524; }
        .rf-pdp-cat-link:hover { color: #111111; }

        .pdp-description {
            margin-top: 26px;
            padding-top: 22px;
            border-color: #eee6e1;
        }
        .pdp-description h2 { color: #302727; font-weight: 600; }
        .pdp-description__body { color: #655a56; }

        .rf-related-section { border-color: #eee6e1; }
        .rf-related-header { text-align: left; }
        .rf-related-subtitle { color: #292524; letter-spacing: .16em; }
        .rf-related-title { color: #302727; }

        @media (max-width: 767px) {
            .pdp-page { padding: 12px 14px 38px; }
            .pdp-layout {
                grid-template-columns: minmax(0, 1fr);
                gap: 20px;
            }
            .pdp-gallery { position: static; }
            .pdp-info { padding: 20px 16px; }
            .pdp-title { font-size: 27px; }
            .rf-pdp-social-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .rf-pdp-social-btn { gap: 4px; height: 40px; font-size: 11px; }
            .rf-pdp-social-btn i { font-size: 14px; }
            .pdp-description { margin-top: 22px; }
            .rf-related-section { margin-top: 38px; padding-top: 28px; }
        }
    </style>
@endpush

@section('content')
    <main class="pdp-page">
        <nav class="pdp-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true"> / </span><span>{{ $productName }}</span>
        </nav>

        <section class="pdp-layout" aria-label="Product details">
            <div class="pdp-gallery">
                @php
                    $gallerySlides = [['type' => 'image', 'src' => asset($product->product_thumbnail ?: 'upload/product-default.jpg')]];
                    foreach ($multiImg as $galleryImage) $gallerySlides[] = ['type' => 'image', 'src' => asset($galleryImage->photo_name)];
                    if ($youtubeVideoId) $gallerySlides[] = ['type' => 'video'];
                @endphp
                <div class="pdp-image-frame" data-product-gallery>
                    <img id="product_zoom_img"
                        src="{{ asset($product->product_thumbnail ?: 'upload/product-default.jpg') }}"
                        alt="{{ $productName }}" fetchpriority="high">
                    @if($youtubeVideoId)
                        <button type="button" class="pdp-video-poster" data-gallery-video aria-label="Play product video" hidden><i class="fa-brands fa-youtube" aria-hidden="true"></i></button>
                    @endif
                    <button type="button" class="pdp-slide-arrow pdp-slide-arrow--prev" data-gallery-prev aria-label="Previous slide" hidden><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                    <button type="button" class="pdp-slide-arrow pdp-slide-arrow--next" data-gallery-next aria-label="Next slide" hidden><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                </div>
                @if(count($gallerySlides) > 1)
                    <div class="pdp-thumbnails" aria-label="Product images">
                        @foreach($gallerySlides as $slide)
                            <button type="button" class="pdp-thumbnail" aria-current="false"
                                data-slide-index="{{ $loop->index }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" @if($slide['type'] === 'image') data-image="{{ $slide['src'] }}" @else data-video-slide="true" @endif>
                                @if($slide['type'] === 'image')
                                    <img src="{{ $slide['src'] }}" alt="{{ $productName }} image {{ $loop->iteration }}">
                                @else
                                    <span class="pdp-video-thumb"><i class="fa-brands fa-youtube" aria-hidden="true"></i></span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pdp-info">
                <h1 class="pdp-title">{{ $productName }}</h1>
                <div class="pdp-price" aria-live="polite">
                    <strong class="product_price current-price">Tk {{ number_format($currentPrice, 2) }} BDT</strong>
                    <del class="old-price" @if($discountPercent === 0) hidden @endif>Tk
                        {{ number_format($variantRegularPrice, 2) }}</del>
                    <span class="pdp-price__save" @if($discountPercent === 0) hidden @endif>Save
                        {{ $discountPercent }}%</span>
                </div>

                <div class="pdp-meta">
                    @if($product->category)
                        <span>Category:
                            <strong>{{ $isBangla ? ($product->category->name_bn ?: $product->category->name_en) : $product->category->name_en }}</strong></span>
                    @endif
                    @if($product->brand)
                        <span>Brand:
                            <strong>{{ $isBangla ? ($product->brand->name_bn ?: $product->brand->name_en) : $product->brand->name_en }}</strong></span>
                    @endif
                    <span>Stock: <strong
                            class="{{ $availableStock > 0 ? 'in-stock' : 'out-of-stock' }}">{{ $availableStock > 0 ? ($isBangla ? 'স্টকে আছে' : 'In stock') : ($isBangla ? 'স্টক শেষ' : 'Out of stock') }}</strong></span>
                </div>

                <input type="hidden" id="product_id" value="{{ $product->id }}">
                <input type="hidden" id="pname" value="{{ $product->name_en }}">
                <input type="hidden" id="product_price" value="{{ $currentPrice }}">
                <input type="hidden" id="minimum_buy_qty" value="{{ $minimumQuantity }}">
                <input type="hidden" id="stock_qty" value="{{ $availableStock }}">
                <input type="hidden" id="pvarient" value="{{ $selectedVariantName }}">
                <input type="hidden" id="buyNowCheck" value="0">
                <input type="hidden" id="discount_amount" value="{{ $discountValue }}">
                <input type="hidden" id="product_discount_type" value="{{ $discountType }}">
                <input type="hidden" id="product_discount_value" value="{{ $discountValue }}">
                <input type="hidden" id="{{ $product->id }}-product_pname" value="{{ $product->name_en }}">

                {{-- Unified Variant Selector matching Landing Page --}}
                @if($hasVariants)
                    <div class="pdp-variant-block">
                        <label for="variantSelect" class="pdp-variant-lbl">
                            <span>{{ $isBangla ? 'সাইজ নির্বাচন করুন:' : 'সাইজ নির্বাচন করুন' }}</span>
                            <!-- <strong id="curVarLabel">{{ $selectedVariantName }}</strong> -->
                        </label>
                        <select id="variantSelect" class="pdp-variant-select"
                            aria-label="{{ $isBangla ? 'ড্রেসের সাইজ সিলেক্ট করুন' : 'Select Variant' }}">
                            @foreach($productStocks->sortBy('price') as $stk)
                                @php
                                    $stkPrice = $stk->price > 0 ? (float) $stk->price : (float) $product->storefrontPrice();
                                    $isOutOfStock = ($stk->qty !== null && $stk->qty <= 0);
                                @endphp
                                <option value="{{ $stk->varient }}" {{ $stk->varient === $selectedVariantName ? 'selected' : '' }} {{ $isOutOfStock ? 'disabled' : '' }}>
                                    {{ $stk->varient }} @if($stkPrice > 0) — ৳{{ number_format($stkPrice) }} @endif
                                    @if($isOutOfStock) ({{ $isBangla ? 'স্টক শেষ' : 'Out of stock' }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Form for Cart / Checkout data serialization --}}
                <form id="choice_form" class="pdp-choice-form" style="display:none;">
                    @if($hasVariants && count($productAttributes) > 0)
                        @php
                            $variantParts = !empty($selectedVariantName) ? explode('-', $selectedVariantName) : [];
                        @endphp
                        @foreach($productAttributes as $index => $attributeDefinition)
                            @php
                                $position = $index + 1;
                                $attribute = get_attribute_by_id($attributeDefinition->attribute_id);
                                $val = $variantParts[$index] ?? ($index === 0 ? $selectedVariantName : '');
                            @endphp
                            @if($attribute)
                                <input type="hidden" name="attribute_ids[]" value="{{ $attributeDefinition->attribute_id }}">
                                <input type="hidden" name="attribute_names[]" id="attribute_name_{{ $position }}"
                                    value="{{ $attribute->name }}">
                                <input type="hidden" id="attribute_check_{{ $position }}"
                                    value="{{ !empty($selectedVariantName) ? 1 : 0 }}">
                                <input type="hidden" id="attribute_check_attr_{{ $position }}" value="{{ $val }}">
                                <input type="hidden" name="attribute_options[]" class="attr_value_{{ $position }}"
                                    data-variant-option="{{ $position }}" value="{{ $val }}">
                            @endif
                        @endforeach
                        <input type="hidden" id="total_attributes" value="{{ count($productAttributes) }}">
                    @else
                        <input type="hidden" id="total_attributes" value="0">
                    @endif
                </form>
                <div id="attribute_alert" class="pdp-attribute-alert" role="status"></div>

                {{-- Quantity Selector matching screenshot --}}
                <div class="rf-pdp-qty-wrapper">
                    <div class="rf-pdp-qty-box">
                        <button type="button" class="rf-pdp-qty-btn qty-down"
                            aria-label="Decrease quantity">&#8722;</button>
                        <input type="number" id="qty" class="rf-pdp-qty-input qty-val" value="{{ $minimumQuantity }}"
                            min="{{ $minimumQuantity }}" max="{{ $availableStock }}" readonly>
                        <button type="button" class="rf-pdp-qty-btn qty-up" aria-label="Increase quantity">+</button>
                    </div>
                    <div id="qty_stock_alert" class="pdp-stock-alert" role="status"></div>
                </div>

                {{-- Action Buttons: Add To Cart & 2s Periodic Shake Buy Now --}}
                <div class="rf-pdp-actions-stack">
                    <button type="button" class="rf-pdp-btn-cart pdp-action" onclick="$('#buyNowCheck').val(0); test();">
                        Add To Cart
                    </button>
                    <button type="button" class="rf-pdp-btn-buy pdp-action pdp-action--primary" onclick="buyProduct()">
                        অর্ডার করতে এই বাটনে ক্লিক করুন
                    </button>
                </div>

                {{-- Social & Call Actions matching image --}}
                <div class="rf-pdp-social-grid">
                    <a href="{{ $fbChatUrl }}" target="_blank" rel="noopener noreferrer"
                        class="rf-pdp-social-btn rf-btn-chat">
                        <i class="fa-brands fa-facebook-messenger"></i>
                        <span>Chat with Us</span>
                    </a>
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                        class="rf-pdp-social-btn rf-btn-whatsapp">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>WhatsApp Us</span>
                    </a>
                    <a href="tel:{{ $phoneSetting }}" class="rf-pdp-social-btn rf-btn-call">
                        <i class="fa-solid fa-phone"></i>
                        <span>Call Us</span>
                    </a>
                </div>

                {{-- Categories Box matching image --}}
                @if($product->category)
                    <div class="rf-pdp-categories-box">
                        <span>Categories: </span>
                        <a href="{{ route('product.category', $product->category->slug) }}" class="rf-pdp-cat-link">
                            {{ $isBangla ? ($product->category->name_bn ?: $product->category->name_en) : $product->category->name_en }}
                        </a>
                        @if($product->sub_category)
                            <span style="color: #9ca3af; margin: 0 4px;">/</span>
                            <a href="{{ route('product.category', $product->sub_category->slug) }}" class="rf-pdp-cat-link">
                                {{ $isBangla ? ($product->sub_category->name_bn ?: $product->sub_category->name_en) : $product->sub_category->name_en }}
                            </a>
                        @endif
                    </div>
                @endif

                @if($product->description_en || $product->description_bn)
                    <section class="pdp-description" aria-labelledby="pdp-description-title">
                        <h2 id="pdp-description-title">Product details</h2>
                        <div class="pdp-description__body">
                            {!! $isBangla ? ($product->description_bn ?: $product->description_en) : ($product->description_en ?: $product->description_bn) !!}
                        </div>
                    </section>
                @endif
            </div>
        </section>

        {{-- Related Products Section --}}
        @if($relatedProducts && $relatedProducts->isNotEmpty())
            <section class="rf-related-section" aria-labelledby="related-products-heading">
                <div class="rf-related-header">
                    <span class="rf-related-subtitle">{{ $isBangla ? 'আপনার পছন্দের আরও পণ্য' : 'SIMILAR PRODUCTS' }}</span>
                    <h2 id="related-products-heading" class="rf-related-title">
                        {{ $isBangla ? 'সম্পর্কিত পণ্যসমূহ' : 'Related Products' }}
                    </h2>
                </div>
                <div class="product-grid rf-related-grid">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif

    </main>
    @if($youtubeVideoId)
        <dialog class="pdp-video-modal" data-video-modal aria-label="Product video">
            <button type="button" class="pdp-video-modal__close" data-video-close aria-label="Close video">&times;</button>
            <iframe class="pdp-video-modal__frame" data-video-frame title="{{ $productName }} video" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
        </dialog>
    @endif
@endsection

@push('js')
    <script>
        (() => {
            const mainImage = document.querySelector('#product_zoom_img');
            const slides = @json($gallerySlides);
            const videoId = @json($youtubeVideoId);
            const gallery = document.querySelector('[data-product-gallery]');
            const videoPoster = gallery?.querySelector('[data-gallery-video]');
            const prevSlide = gallery?.querySelector('[data-gallery-prev]');
            const nextSlide = gallery?.querySelector('[data-gallery-next]');
            const thumbnails = [...document.querySelectorAll('.pdp-thumbnail')];
            const videoModal = document.querySelector('[data-video-modal]');
            const videoFrame = videoModal?.querySelector('[data-video-frame]');
            let activeSlide = 0;

            function showSlide(index) {
                if (!slides.length) return;
                activeSlide = (index + slides.length) % slides.length;
                const slide = slides[activeSlide];
                const isVideo = slide.type === 'video';
                gallery.classList.toggle('is-video-slide', isVideo);
                mainImage.hidden = isVideo;
                if (!isVideo) mainImage.src = slide.src;
                if (videoPoster) videoPoster.hidden = !isVideo;
                if (prevSlide) prevSlide.hidden = slides.length < 2;
                if (nextSlide) nextSlide.hidden = slides.length < 2;
                thumbnails.forEach((thumbnail, itemIndex) => thumbnail.setAttribute('aria-current', itemIndex === activeSlide ? 'true' : 'false'));
            }

            thumbnails.forEach((thumbnail) => thumbnail.addEventListener('click', () => showSlide(Number(thumbnail.dataset.slideIndex))));
            prevSlide?.addEventListener('click', () => showSlide(activeSlide - 1));
            nextSlide?.addEventListener('click', () => showSlide(activeSlide + 1));
            videoPoster?.addEventListener('click', () => {
                videoFrame.src = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;
                videoModal.showModal();
            });
            const closeVideo = () => { if (videoModal?.open) videoModal.close(); };
            videoModal?.querySelector('[data-video-close]')?.addEventListener('click', closeVideo);
            videoModal?.addEventListener('click', (event) => { if (event.target === videoModal) closeVideo(); });
            videoModal?.addEventListener('close', () => { videoFrame.src = ''; });
            showSlide(0);

            const quantity = document.querySelector('#qty');
            if (quantity) {
                const min = Number(quantity.min);
                document.querySelector('.qty-down').addEventListener('click', () => {
                    quantity.value = Math.max(min, Number(quantity.value) - 1);
                });
                document.querySelector('.qty-up').addEventListener('click', () => {
                    const max = Number(quantity.max);
                    quantity.value = Math.min(max, Number(quantity.value) + 1);
                });
            }

            const variantsMap = @json($variantsMap);
            const hasVariants = {{ $hasVariants ? 'true' : 'false' }};
            const baseRegularPrice = {{ $regularPrice }};
            const isBangla = {{ $isBangla ? 'true' : 'false' }};
            const minQty = {{ $minimumQuantity }};

            function selectVariant(vName) {
                if (!hasVariants || !variantsMap || !variantsMap[vName]) return;
                const vData = variantsMap[vName];
                const price = Number(vData.price);
                const regPrice = Number(vData.regular_price || baseRegularPrice);
                const stock = Number(vData.qty);

                // Update hidden inputs
                const pvarientInput = document.querySelector('#pvarient');
                if (pvarientInput) pvarientInput.value = vName;

                const priceInput = document.querySelector('#product_price');
                if (priceInput) priceInput.value = price;

                const stockInput = document.querySelector('#stock_qty');
                if (stockInput) stockInput.value = stock;

                // Update UI label
                const curVarLbl = document.querySelector('#curVarLabel');
                if (curVarLbl) curVarLbl.textContent = vName;

                // Update price UI
                const curPriceEl = document.querySelector('.current-price');
                if (curPriceEl) curPriceEl.textContent = `Tk ${price.toFixed(2)} BDT`;

                const oldPriceEl = document.querySelector('.old-price');
                const saveEl = document.querySelector('.pdp-price__save');
                if (oldPriceEl && saveEl) {
                    if (regPrice > price) {
                        oldPriceEl.textContent = `Tk ${regPrice.toFixed(2)}`;
                        oldPriceEl.hidden = false;
                        const pct = Math.round(((regPrice - price) / regPrice) * 100);
                        saveEl.textContent = `Save ${pct}%`;
                        saveEl.hidden = false;
                    } else {
                        oldPriceEl.hidden = true;
                        saveEl.hidden = true;
                    }
                }

                // Update stock UI & quantity limits
                const stockStatus = document.querySelector('.pdp-meta strong.in-stock, .pdp-meta strong.out-of-stock');
                if (stockStatus) {
                    stockStatus.textContent = stock > 0 ? (isBangla ? 'স্টকে আছে' : 'In stock') : (isBangla ? 'স্টক শেষ' : 'Out of stock');
                    stockStatus.className = stock > 0 ? 'in-stock' : 'out-of-stock';
                }

                const stockAlert = document.querySelector('#qty_stock_alert');
                const qtyInput = document.querySelector('#qty');
                const actionButtons = document.querySelectorAll('.pdp-action');

                if (stock <= 0) {
                    if (stockAlert) stockAlert.textContent = isBangla ? 'এই ভ্যারিয়েন্টটির স্টক শেষ।' : 'This variant is out of stock.';
                    actionButtons.forEach(btn => {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                        btn.style.cursor = 'not-allowed';
                    });
                } else {
                    if (stockAlert) stockAlert.textContent = '';
                    actionButtons.forEach(btn => {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.style.cursor = 'pointer';
                    });
                }

                if (qtyInput) {
                    qtyInput.max = stock;
                    if (Number(qtyInput.value) > stock) {
                        qtyInput.value = Math.max(minQty, stock);
                    }
                }

                // Update hidden choice_form inputs for CartController and test()
                const parts = vName.split('-');
                const totalAttrs = parseInt(document.querySelector('#total_attributes')?.value || 0);
                for (let i = 1; i <= Math.max(totalAttrs, parts.length); i++) {
                    const checkInput = document.querySelector(`#attribute_check_${i}`);
                    if (checkInput) checkInput.value = '1';

                    const optInput = document.querySelector(`.attr_value_${i}`);
                    if (optInput) {
                        const partVal = parts[i - 1];
                        optInput.value = (partVal !== undefined) ? partVal : (i === 1 ? vName : '');
                    }

                    const checkAttr = document.querySelector(`#attribute_check_attr_${i}`);
                    if (checkAttr) {
                        const partVal = parts[i - 1];
                        checkAttr.value = (partVal !== undefined) ? partVal : (i === 1 ? vName : '');
                    }
                }

                // Update main image if variant has image
                if (vData.image && mainImage) {
                    mainImage.src = vData.image;
                    document.querySelectorAll('.pdp-thumbnail').forEach((thumb) => {
                        if (thumb.dataset.image === vData.image) {
                            thumb.setAttribute('aria-current', 'true');
                        } else {
                            thumb.setAttribute('aria-current', 'false');
                        }
                    });
                }
            }

            const variantSelect = document.querySelector('#variantSelect');
            if (variantSelect) {
                variantSelect.addEventListener('change', function () {
                    selectVariant(this.value);
                });
                // Synchronize with initial selected variant
                if (variantSelect.value) {
                    selectVariant(variantSelect.value);
                }
            }
        })();
    </script>
@endpush
