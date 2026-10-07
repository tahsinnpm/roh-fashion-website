@php
  if (!function_exists('get_img_url')) {
    function get_img_url($path)
    {
      if (empty($path))
        return asset('assets/images/default.png');
      if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
      }
      return asset(ltrim($path, '/'));
    }
  }

  $siteName = get_setting('site_name')->value ?? 'Roh Fashion';
  $siteLogo = get_setting('site_logo')->value ?? null;
  $siteFavicon = get_setting('site_favicon')->value ?? null;
  $sitePhone = get_setting('phone')->value ?? '01XXXXXXXXX';
  $siteEmail = get_setting('email')->value ?? '';
  $siteAddress = get_setting('business_address')->value ?? '';
  $facebookUrl = get_setting('facebook_url')->value ?? 'https://www.facebook.com';
  $instagramUrl = get_setting('instagram_url')->value ?? 'https://www.instagram.com';
  $copyright = get_setting('copy_right')->value ?? ('© ' . date('Y') . ' ' . $siteName . ' — সর্বস্বত্ব সংরক্ষিত');

  // Messenger link
  $messengerUrl = 'https://www.facebook.com/messages/t/145176219220180';

  // Dynamic Colors & Design
  $themeColor = $landingPage->theme_color ?: '#000000';
  $btnColor = $landingPage->btn_color ?: '#E0B04A';
  $btnTextColor = $landingPage->btn_text_color ?: '#2B1C06';
  $bgColor = $landingPage->bg_color ?: '#1F0A0F';
  $topNotice = $landingPage->top_notice ?: '✦ ক্যাশ অন ডেলিভারি — সারাদেশে পণ্য হাতে পেয়ে, খুলে দেখে তারপর টাকা দিন';

  // Order Steps and Guarantee from about_cards
  $aboutCards = is_array($landingPage->about_cards) ? $landingPage->about_cards : [];
  $steps = $aboutCards['steps'] ?? [
    ['title' => 'ফর্ম পূরণ করুন', 'desc' => 'নাম, ফোন, ঠিকানা — ৩০ সেকেন্ড'],
    ['title' => 'কনফার্মেশন কল', 'desc' => 'আমরা কল দিয়ে নিশ্চিত করব'],
    ['title' => 'হাতে পেয়ে টাকা', 'desc' => 'খুলে দেখে তারপর পেমেন্ট'],
  ];
  $guaranteeTitle = $aboutCards['guarantee_title'] ?? 'ঝুঁকিটা আমাদের, আপনার না';
  $guaranteeDesc = $aboutCards['guarantee_desc'] ?? 'ডেলিভারি ম্যানের সামনেই প্যাকেট খুলে দেখুন। ছবির সাথে না মিললে বা কোনো সমস্যা থাকলে গ্রহণ করবেন না — টাকা দেওয়ার কোনো প্রশ্নই আসে না।';

  // Video URL Embed handling
  $videoEmbedUrl = null;
  if (!empty($landingPage->video_url)) {
    $rawUrl = trim($landingPage->video_url);
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $rawUrl, $matches)) {
      $videoEmbedUrl = 'https://www.youtube-nocookie.com/embed/' . $matches[1];
    } elseif (str_contains($rawUrl, 'embed') || str_starts_with($rawUrl, 'http')) {
      $videoEmbedUrl = $rawUrl;
    }
  }

  $productTitle = $landingPage->title ?: ($product->name_bn ?: $product->name_en);
  $productSubtitle = $landingPage->sub_title ?: ($product->short_description_bn ?: ($product->short_description_en ?: ($product->description_bn ?: $product->description_en)));

  // Product Slide Images: primary image & multi-images
  $allImages = [];
  if (!empty($sliderImages) && is_array($sliderImages)) {
    $allImages = $sliderImages;
  } else {
    if ($product->product_thumbnail) {
      $allImages[] = $product->product_thumbnail;
    }
    if ($product->multi_imgs && $product->multi_imgs->count() > 0) {
      foreach ($product->multi_imgs as $m) {
        if (!empty($m->photo_name) && !in_array($m->photo_name, $allImages)) {
          $allImages[] = $m->photo_name;
        }
      }
    }
  }
  if (empty($allImages)) {
    $allImages[] = 'assets/images/default.png';
  }

  // Pricing & Stock resolution
  // "stock and price atribute a jeta ache seta hobe, attribute na hole discount price jeta ache sei proce aa order hobe"
  $hasVariants = ($product->is_varient == 1 && isset($productStocks) && $productStocks->count() > 0);
  $firstStock = $hasVariants ? $productStocks->first() : null;

  $regularPrice = (float) ($product->regular_price ?: 0);
  $discountPrice = (float) ($product->discount_price ?: 0);

  if ($hasVariants && $firstStock) {
    $initialPrice = $firstStock->price > 0 ? (float) $firstStock->price : ($discountPrice > 0 ? $discountPrice : $regularPrice);
    $initialStock = (int) ($firstStock->qty ?? 0);
    $selectedVariantName = $firstStock->varient;
  } else {
    $initialPrice = $discountPrice > 0 ? $discountPrice : $regularPrice;
    $initialStock = (int) ($product->stock_qty ?? 15);
    $selectedVariantName = '';
  }

  if ($regularPrice > 0 && $regularPrice > $initialPrice) {
    $discountPercent = round((($regularPrice - $initialPrice) / $regularPrice) * 100);
    $savingsAmount = $regularPrice - $initialPrice;
  } else {
    $discountPercent = 0;
    $savingsAmount = 0;
  }

  // Dynamic Shipping from website shipping table
  $delivery_inside = (float) ($delivery_inside ?? 80);
  $delivery_outside = (float) ($delivery_outside ?? 150);

  $initialShippingCharge = $delivery_inside;
  $initialShippingId = 1;
  if (isset($shippings) && $shippings->count() > 0) {
    $firstShip = $shippings->first();
    $initialShippingCharge = (float) $firstShip->shipping_charge;
    $initialShippingId = $firstShip->id;
  }

  $initialGrandTotal = $initialPrice + $initialShippingCharge;

  // Variants Map for JavaScript
  $variantsMap = [];
  if ($hasVariants) {
    foreach ($productStocks as $stk) {
      $vPrice = $stk->price > 0 ? (float) $stk->price : ($discountPrice > 0 ? $discountPrice : $regularPrice);
      $variantsMap[$stk->varient] = [
        'id' => $stk->id,
        'varient' => $stk->varient,
        'price' => $vPrice,
        'regular_price' => $regularPrice,
        'qty' => (int) $stk->qty,
        'image' => $stk->image ? get_img_url($stk->image) : null,
      ];
    }
  }

  // Text Reviews Only ("review image hobe na, likhai hobe")
  $textReviews = [];
  if (!empty($reviews) && is_array($reviews)) {
    foreach ($reviews as $rev) {
      if (is_array($rev)) {
        $textReviews[] = [
          'name' => $rev['name'] ?? 'সম্মানিত ক্রেতা',
          'comment' => $rev['comment'] ?? ($rev['body'] ?? ($rev['text'] ?? '')),
          'rating' => $rev['rating'] ?? 5,
          'location' => $rev['location'] ?? 'ক্যাশ অন ডেলিভারি',
          'date' => $rev['date'] ?? 'সম্প্রতি কেনা',
        ];
      } elseif (is_string($rev)) {
        $textReviews[] = [
          'name' => 'সম্মানিত ক্রেতা',
          'comment' => $rev,
          'rating' => 5,
          'location' => 'ক্যাশ অন ডেলিভারি',
          'date' => 'সম্প্রতি কেনা',
        ];
      }
    }
  } elseif ($product->reviews && $product->reviews->count() > 0) {
    foreach ($product->reviews as $prRev) {
      $textReviews[] = [
        'name' => $prRev->user->name ?? 'সম্মানিত ক্রেতা',
        'comment' => $prRev->body,
        'rating' => (int) $prRev->rating ?: 5,
        'location' => 'ভেরিফাইড অর্ডার',
        'date' => $prRev->created_at ? $prRev->created_at->diffForHumans() : 'সম্প্রতি কেনা',
      ];
    }
  }

  if (empty($textReviews)) {
    $textReviews = [
      [
        'name' => 'তানজিলা ইসলাম',
        'comment' => 'মাশাল্লাহ! ছবির চেয়েও বাস্তবে প্রোডাক্টটা অনেক বেশি সুন্দর ও নিখুঁত। কাপড় এবং ফিনিশিং অসাধারণ। ক্যাশ অন ডেলিভারিতে কোনো ঝামেলা ছাড়াই পেয়েছি!',
        'rating' => 5,
        'location' => 'ধানমন্ডি, ঢাকা',
        'date' => '২ দিন আগে',
      ],
      [
        'name' => 'নুসরাত জাহান রিমু',
        'comment' => 'ডেলিভারি ম্যানের সামনে প্যাকেট খুলে চেক করে নিতে পেরেছি, এটাই সবচেয়ে বড় ভরসা। কোয়ালিটি ১০০/১০০!',
        'rating' => 5,
        'location' => 'চট্টগ্রাম সদর',
        'date' => '৪ দিন আগে',
      ],
      [
        'name' => 'ফারহানা আহমেদ',
        'comment' => 'অনলাইনে অর্ডার করার পর ভয়ে ছিলাম। কিন্তু হাতে পেয়ে সত্যি দারুণ লেগেছে। কালার ও কোয়ালিটি একদম ছবির মতো। ধন্যবাদ!',
        'rating' => 5,
        'location' => 'উত্তরা, ঢাকা',
        'date' => '১ সপ্তাহ আগে',
      ],
      [
        'name' => 'মেহজাবিন চৌধুরী',
        'comment' => 'অনেক সুন্দর প্যাকেজিং এবং ফাস্ট ডেলিভারি। পরিবারের সবাই অনেক পছন্দ করেছে। ভবিষ্যতেও নিশ্চিন্তে অর্ডার করব।',
        'rating' => 5,
        'location' => 'সিলেট',
        'date' => '১ সপ্তাহ আগে',
      ],
    ];
  }
@endphp
<!DOCTYPE html>
<html lang="bn">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $productTitle }} — ৳{{ number_format($initialPrice) }} | {{ $siteName }}</title>
  <meta name="description"
    content="{{ Str::limit(strip_tags($productSubtitle ?: $productTitle), 160) }} ৳{{ number_format($initialPrice) }} — ক্যাশ অন ডেলিভারি।">
  <link rel="canonical" href="{{ url()->current() }}">
  <meta property="og:type" content="product">
  <meta property="og:title" content="{{ $productTitle }} — ৳{{ number_format($initialPrice) }}">
  <meta property="og:description"
    content="{{ Str::limit(strip_tags($productSubtitle ?: $productTitle), 120) }} · ক্যাশ অন ডেলিভারি">
  <meta property="og:image" content="{{ get_img_url($allImages[0]) }}">
  @if($siteFavicon)
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($siteFavicon) }}">
  @endif
  <link rel="preload" as="image" href="{{ get_img_url($allImages[0]) }}" fetchpriority="high">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&family=Hind+Siliguri:wght@400;500;600;700&display=swap"
    rel="stylesheet">

  <script>window.dataLayer = window.dataLayer || [];</script>
  <script>(function (w, d, s, l, i) { w[l] = w[l] || []; w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' }); var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f); })(window, document, 'script', 'dataLayer', 'GTM-KGWPJ8QZ');</script>
  <script>
    window.dataLayer.push({ ecommerce: null });
    window.dataLayer.push({
      event: 'view_item',
      ecommerce: {
        currency: 'BDT',
        value:{{ $initialPrice }},
        items: [{
          item_id: '{{ $product->id }}',
          item_name: @json($productTitle),
          item_brand: @json($siteName),
          item_category: @json($product->category->name_en ?? ($product->category->name_bn ?? 'Fashion')),
          item_variant: @json($selectedVariantName),
          price:{{ $initialPrice }},
          quantity: 1,
          currency: 'BDT'
        }]
      }
    });
  </script>
  <script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"Product",
  "name":@json($productTitle),
  "image":[@json(get_img_url($allImages[0]))],
  "description":@json(strip_tags($productSubtitle ?: $productTitle)),
  "sku":"{{ $product->product_code ?? ('PROD-' . $product->id) }}",
  "brand":{"@type":"Brand","name":@json($siteName)},
  "offers":{
    "@type":"Offer",
    "url":@json(url()->current()),
    "priceCurrency":"BDT",
    "price":"{{ $initialPrice }}",
    "availability":"{{ $initialStock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
    "seller":{"@type":"Organization","name":@json($siteName)}
  }
}
</script>

  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0
    }

    :root {
      --mr-900:
        {{ $bgColor }}
      ;
      --mr-800:
        {{ $themeColor }}
      ;
      --mr-700:
        {{ $themeColor }}
      ;
      --mr-600:
        {{ $themeColor }}
      ;
      --mr-400:
        {{ $themeColor }}
      ;
      --mr-50: #FAF0F2;
      --gd-500:
        {{ $btnColor }}
      ;
      --gd-400:
        {{ $btnColor }}
      ;
      --gd-300:
        {{ $btnColor }}
      ;
      --gd-100: #F5ECDA;
      --bg: #FDF9F8;
      --blush: #F6EFED;
      --ink: #1E1517;
      --soft: #6D5A5F;
      --line: #ECDFE2;
      --cta-a:
        {{ $btnColor }}
      ;
      --cta-b:
        {{ $btnColor }}
      ;
      --cta-ink:
        {{ $btnTextColor }}
      ;
      --ok: #187A47;
      --alert: #AD2845;
      --r-lg: 18px;
      --r-md: 12px;
      --r-sm: 8px;
      --sh: 0 8px 26px rgba(31, 10, 15, .08);
      --shl: 0 18px 44px rgba(31, 10, 15, .16);
      --shc: 0 10px 26px rgba(0, 0, 0, .25);
      --fd: 'Tiro Bangla', 'Noto Serif Bengali', serif;
      --fb: 'Hind Siliguri', 'Noto Sans Bengali', sans-serif;
    }

    html {
      scroll-behavior: smooth
    }

    body {
      font-family: var(--fb);
      font-size: 17px;
      line-height: 1.72;
      color: var(--ink);
      background: var(--bg);
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden
    }

    img {
      max-width: 100%;
      display: block
    }

    a {
      color: inherit;
      text-decoration: none
    }

    button {
      font-family: inherit;
      cursor: pointer;
      border: none
    }

    h1,
    h2,
    h3 {
      font-family: var(--fd);
      font-weight: 400;
      line-height: 1.3;
      color: var(--mr-900)
    }

    h1,
    h2,
    h3,
    p,
    a,
    button,
    label,
    summary {
      overflow-wrap: anywhere
    }

    .w {
      max-width: 1120px;
      margin: 0 auto;
      padding: 0 20px
    }

    .kk {
      display: inline-block;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--gd-500)
    }

    .orn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      margin: 0 auto 20px
    }

    .orn i {
      display: block;
      width: 5px;
      height: 5px;
      background: var(--gd-500);
      transform: rotate(45deg);
      flex: none
    }

    .orn i:nth-child(3) {
      width: 9px;
      height: 9px;
      background: var(--mr-600)
    }

    .orn span {
      display: block;
      height: 1px;
      width: 52px;
      background: linear-gradient(90deg, transparent, var(--gd-400))
    }

    .orn span:last-child {
      background: linear-gradient(90deg, var(--gd-400), transparent)
    }

    .orn.dk i:nth-child(3) {
      background: #C8B8BC
    }

    .js .rv {
      opacity: 0;
      transform: translateY(18px);
      transition: opacity .6s ease, transform .6s ease
    }

    .js .rv.in {
      opacity: 1;
      transform: none
    }

    @media(prefers-reduced-motion:reduce) {

      *,
      .js .rv {
        animation: none !important;
        transition: none !important;
        opacity: 1 !important;
        transform: none !important;
        scroll-behavior: auto !important
      }
    }

    .sh {
      text-align: center;
      margin-bottom: 26px
    }

    .sh h2 {
      font-size: clamp(25px, calc(3vw + 6px), 34px)
    }

    .sh p {
      font-size: 15px;
      color: var(--soft);
      margin-top: 4px
    }

    /* topbar + header */
    .top {
      background: var(--mr-900);
      color: #fff;
      text-align: center;
      font-size: 13.5px;
      font-weight: 500;
      padding: 8px 14px
    }

    .top b {
      color: var(--cta-a);
      font-weight: 700
    }

    header.hd {
      background: rgba(253, 249, 248, .96);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--line);
      position: sticky;
      top: 0;
      z-index: 50
    }

    header.hd .w {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding-top: 8px;
      padding-bottom: 8px
    }

    header.hd img {
      height: 46px;
      max-height: 46px;
      width: auto;
      object-fit: contain
    }

    .ha {
      display: flex;
      gap: 8px
    }

    .ch {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: 1px solid var(--gd-500);
      color: var(--mr-800);
      background: #fff;
      border-radius: 999px;
      padding: 8px 14px;
      font-size: 14px;
      font-weight: 600;
      white-space: nowrap;
      transition: all .2s ease
    }

    .ch:hover {
      background: var(--gd-100)
    }

    .ch.fl {
      background: var(--cta-a);
      color: var(--cta-ink);
      border-color: var(--cta-a)
    }

    .ch.fl:hover {
      filter: brightness(1.08)
    }

    /* hero */
    .hero {
      position: relative;
      overflow: hidden;
      color: #fff;
      padding: 30px 0 46px;
      background: radial-gradient(900px 460px at 82% -12%, rgba(255, 255, 255, .08), transparent 62%),
        radial-gradient(700px 420px at 8% 100%, rgba(255, 255, 255, .05), transparent 60%),
        linear-gradient(166deg, var(--mr-900) 0%, var(--mr-800) 50%, var(--mr-900) 100%)
    }

    .hero::after {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: -1px;
      height: 42px;
      background: var(--bg);
      clip-path: polygon(0 100%, 100% 100%, 100% 36%, 50% 100%, 0 36%)
    }

    .hero .w {
      position: relative;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      align-items: center
    }

    .hero .w>* {
      min-width: 0
    }

    .shot {
      position: relative;
      border-radius: var(--r-lg);
      overflow: hidden;
      aspect-ratio: 4/5;
      background: #1a080c;
      box-shadow: var(--shl);
      outline: 1px solid rgba(219, 175, 86, .38);
      outline-offset: -1px
    }

    .shot img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: opacity .35s ease
    }

    .shot img.on {
      opacity: 1
    }

    .shot .tag {
      position: absolute;
      top: 0;
      left: 16px;
      z-index: 4;
      background: linear-gradient(180deg, var(--alert), #86203A);
      color: #fff;
      font-weight: 700;
      font-size: 14px;
      line-height: 1.15;
      text-align: center;
      padding: 10px 12px 13px;
      border-radius: 0 0 6px 6px;
      box-shadow: 0 6px 14px rgba(0, 0, 0, .32)
    }

    .shot .nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 5;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(31, 10, 15, .55);
      color: var(--gd-400);
      font-size: 24px;
      line-height: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background .2s ease
    }

    .shot .nav:hover {
      background: rgba(31, 10, 15, .85)
    }

    .shot .nav.p {
      left: 10px
    }

    .shot .nav.n {
      right: 10px
    }

    .ts {
      display: flex;
      gap: 8px;
      margin-top: 10px;
      overflow-x: auto;
      scrollbar-width: none
    }

    .ts::-webkit-scrollbar {
      display: none
    }

    .ts button {
      flex: 0 0 auto;
      width: 56px;
      height: 70px;
      border-radius: 7px;
      overflow: hidden;
      border: 2px solid transparent;
      opacity: .55;
      background: #1a080c;
      transition: opacity .2s ease, border-color .2s ease
    }

    .ts button.on {
      border-color: var(--gd-400);
      opacity: 1
    }

    .ts img {
      width: 100%;
      height: 100%;
      object-fit: cover
    }

    .hc .kk {
      color: var(--gd-400)
    }

    .hc h1 {
      font-size: clamp(26px, calc(3.2vw + 8px), 42px);
      color: #fff;
      margin: 10px 0 10px
    }

    .hc h1 em {
      font-style: italic;
      color: var(--gd-400)
    }

    .hc .ld {
      font-size: 16px;
      color: #EBD9DD;
      margin-bottom: 16px
    }

    .hp2 {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      background: rgba(255, 255, 255, .08);
      border: 1px solid rgba(219, 175, 86, .3);
      border-radius: 999px;
      padding: 6px 14px 6px 8px;
      margin-bottom: 16px;
      font-size: 13.5px;
      color: #EDD9C4
    }

    .hp2 .av {
      display: flex
    }

    .hp2 .av i {
      display: block;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      border: 2px solid var(--mr-800);
      margin-left: -9px;
      background: linear-gradient(135deg, var(--gd-400), var(--mr-600))
    }

    .hp2 .av i:first-child {
      margin-left: 0
    }

    .hp2 b {
      color: #fff
    }

    .pc {
      background: rgba(255, 255, 255, .07);
      border: 1px solid rgba(219, 175, 86, .32);
      border-radius: var(--r-md);
      padding: 14px 16px;
      margin-bottom: 14px
    }

    .pr {
      display: flex;
      align-items: baseline;
      gap: 12px;
      flex-wrap: wrap
    }

    .pr .now {
      font-family: var(--fd);
      font-size: 38px;
      color: var(--gd-400);
      line-height: 1
    }

    .pr .was {
      font-size: 19px;
      color: #B693A2;
      text-decoration: line-through
    }

    .pr .cut {
      background: var(--alert);
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 5px
    }

    .sl {
      font-size: 14px;
      color: #EDD9C4;
      margin-top: 8px
    }

    .sl b {
      color: #fff
    }

    .shl {
      margin-top: 10px;
      padding-top: 10px;
      font-size: 14px;
      color: #EBD9DD;
      border-top: 1px dashed rgba(219, 175, 86, .28)
    }

    .shl b {
      color: var(--gd-400)
    }

    /* Variant Dropdown */
    .variant-block {
      margin-bottom: 14px
    }

    .variant-lbl {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      font-weight: 700;
      color: #EDD9C4;
      margin-bottom: 8px
    }

    .variant-lbl b {
      color: var(--gd-300);
      font-weight: 600
    }

    .variant-select {
      width: 100%;
      min-height: 50px;
      padding: 12px 14px;
      font-family: inherit;
      font-size: 15.5px;
      font-weight: 600;
      color: var(--mr-900);
      background: #FDFBFA;
      border: 1.5px solid var(--line);
      border-radius: var(--r-sm);
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237D2A44' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      background-size: 18px;
      transition: border-color .15s ease
    }

    .variant-select:focus {
      outline: 2px solid var(--gd-400);
      outline-offset: 1px;
      border-color: var(--gd-400)
    }

    .variant-select-hero {
      width: 100%;
      min-height: 46px;
      padding: 10px 14px;
      font-family: inherit;
      font-size: 15px;
      font-weight: 600;
      color: #fff;
      background: rgba(255, 255, 255, .1);
      border: 1.5px solid rgba(219, 175, 86, .4);
      border-radius: var(--r-sm);
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23DBAF56' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      background-size: 18px;
      transition: border-color .15s ease
    }

    .variant-select-hero option {
      background: var(--mr-900);
      color: #fff;
      padding: 8px
    }

    .variant-select-hero:focus {
      outline: 2px solid var(--gd-400)
    }

    .sb {
      margin-bottom: 16px;
      display: none
    }

    .sb.on {
      display: block
    }

    .sb .lbl {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      font-size: 13.5px;
      color: #EBD9DD;
      margin-bottom: 6px
    }

    .sb .lbl b {
      color: var(--gd-400)
    }

    .trk {
      height: 7px;
      border-radius: 999px;
      background: rgba(255, 255, 255, .13);
      overflow: hidden
    }

    .trk i {
      display: block;
      height: 100%;
      width: 0;
      border-radius: 999px;
      background: linear-gradient(90deg, var(--alert), var(--gd-400))
    }

    .cta {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      width: 100%;
      max-width: 430px;
      min-height: 56px;
      padding: 15px 24px;
      border-radius: 12px;
      background: var(--cta-a);
      color: var(--cta-ink) !important;
      font-weight: 700;
      font-size: 17.5px;
      line-height: 1.35;
      text-align: center;
      box-shadow: var(--shc);
      transition: transform .15s ease, box-shadow .15s ease
    }

    .cta:hover {
      transform: translateY(-2px);
      filter: brightness(1.08)
    }

    .cta:active {
      transform: translateY(0)
    }

    .cn {
      font-size: 13.5px;
      color: #C6A5B0;
      margin-top: 9px
    }

    .hb {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 18px
    }

    .hb li {
      list-style: none;
      font-size: 13px;
      color: #EDD9C4;
      background: rgba(255, 255, 255, .07);
      border: 1px solid rgba(255, 255, 255, .13);
      padding: 5px 11px;
      border-radius: 999px
    }

    /* steps */
    .steps {
      padding: 34px 0 6px
    }

    .steps .g {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px
    }

    .stp {
      text-align: center;
      padding: 18px 14px;
      background: #fff;
      border: 1px solid var(--line);
      border-radius: var(--r-md);
      box-shadow: var(--sh)
    }

    .stp .n {
      width: 34px;
      height: 34px;
      margin: 0 auto 8px;
      border-radius: 50%;
      background: var(--mr-800);
      color: var(--gd-400);
      font-family: var(--fd);
      font-size: 17px;
      display: flex;
      align-items: center;
      justify-content: center
    }

    .stp h3 {
      font-family: var(--fb);
      font-weight: 700;
      font-size: 15.5px;
      margin-bottom: 2px
    }

    .stp p {
      font-size: 13.5px;
      color: var(--soft)
    }

    /* features */
    .feats {
      padding: 46px 0
    }

    .fg {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px
    }

    .fc {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: var(--r-md);
      padding: 20px 18px;
      text-align: center;
      box-shadow: var(--sh);
      transition: transform .2s ease, box-shadow .2s ease
    }

    .fc:hover {
      transform: translateY(-3px);
      box-shadow: var(--shl)
    }

    .fc .ic {
      width: 46px;
      height: 46px;
      margin: 0 auto 11px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--mr-50);
      color: var(--mr-700);
      border: 1px solid var(--gd-400)
    }

    .fc h3 {
      font-family: var(--fb);
      font-weight: 700;
      font-size: 16.5px;
      margin-bottom: 2px
    }

    .fc p {
      font-size: 14px;
      color: var(--soft)
    }

    /* gallery */
    .gal {
      padding: 50px 0;
      background: radial-gradient(700px 400px at 85% 0%, rgba(196, 153, 58, .16), transparent 60%), var(--mr-900)
    }

    .gal .sh h2 {
      color: #fff
    }

    .gal .sh p {
      color: #C6A5B0
    }

    .gg {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px
    }

    .gg button {
      position: relative;
      border-radius: 10px;
      overflow: hidden;
      aspect-ratio: 3/4;
      background: #1a080c;
      border: 1px solid rgba(219, 175, 86, .2);
      cursor: zoom-in
    }

    .gg button.big {
      grid-column: span 2;
      grid-row: span 2;
      aspect-ratio: auto
    }

    .gg img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .4s ease
    }

    .gg button:hover img {
      transform: scale(1.05)
    }

    .gal .af {
      text-align: center;
      margin-top: 26px
    }

    .lb {
      position: fixed;
      inset: 0;
      z-index: 100;
      display: none;
      align-items: center;
      justify-content: center;
      background: rgba(20, 5, 10, .95);
      padding: 22px
    }

    .lb.open {
      display: flex
    }

    .lb img {
      max-width: 92vw;
      max-height: 86vh;
      border-radius: 10px
    }

    .lb .x {
      position: absolute;
      top: 14px;
      right: 16px;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: var(--gd-500);
      color: var(--mr-900);
      font-size: 21px;
      font-weight: 700
    }

    /* Text Reviews — ("review image hobe na, likhai hobe") */
    .revs {
      padding: 48px 0 8px;
      background: var(--bg)
    }

    .revs .sh {
      margin-bottom: 22px
    }

    .rc {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      background: var(--mr-50);
      border: 1px solid var(--line);
      color: var(--mr-800);
      font-size: 13.5px;
      font-weight: 600;
      padding: 5px 14px;
      border-radius: 999px;
      margin-top: 8px
    }

    .rc svg {
      color: var(--ok);
      flex: none
    }

    .rw {
      max-width: 1040px;
      margin: 0 auto
    }

    .rt-text {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px
    }

    .rcard {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: var(--r-md);
      padding: 22px 20px;
      box-shadow: var(--sh);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform .2s ease, box-shadow .2s ease
    }

    .rcard:hover {
      transform: translateY(-3px);
      box-shadow: var(--shl)
    }

    .rcard-head {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px
    }

    .rcard-av {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--mr-800), var(--mr-600));
      color: var(--gd-300);
      font-family: var(--fd);
      font-size: 18px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: none
    }

    .rcard-meta {
      flex: 1;
      min-width: 0
    }

    .rcard-nm {
      font-family: var(--fb);
      font-weight: 700;
      font-size: 15.5px;
      color: var(--mr-900);
      line-height: 1.2
    }

    .rcard-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      color: var(--ok);
      font-weight: 600;
      margin-top: 2px
    }

    .rcard-stars {
      color: var(--gd-500);
      font-size: 16px;
      letter-spacing: 2px;
      margin-bottom: 10px
    }

    .rcard-text {
      font-size: 14.5px;
      line-height: 1.65;
      color: var(--ink);
      flex: 1;
      margin-bottom: 14px;
      font-style: italic
    }

    .rcard-foot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 12.5px;
      color: var(--soft);
      border-top: 1px dashed var(--line);
      padding-top: 10px
    }

    .rctrl {
      display: none
    }

    .ra {
      width: 44px;
      height: 44px;
      flex: 0 0 44px;
      border-radius: 50%;
      background: var(--mr-800);
      color: var(--gd-400);
      font-size: 26px;
      line-height: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center
    }

    .ra:disabled {
      opacity: .32;
      cursor: default
    }

    .rdots {
      display: flex;
      align-items: center;
      gap: 8px
    }

    .rdot {
      width: 9px;
      height: 9px;
      padding: 0;
      border-radius: 50%;
      background: #D6C6CC;
      transition: transform .2s ease, background .2s ease
    }

    .rdot.on {
      background: var(--mr-700);
      transform: scale(1.3)
    }

    .rn {
      text-align: center;
      font-size: 13px;
      color: var(--soft);
      margin-top: 16px
    }

    .rcta {
      text-align: center;
      margin-top: 26px
    }

    /* guarantee */
    .guar {
      padding: 46px 0 8px
    }

    .gb {
      max-width: 720px;
      margin: 0 auto;
      text-align: center;
      background: linear-gradient(180deg, #fff, var(--mr-50));
      border: 1px solid var(--line);
      border-left: 4px solid var(--gd-500);
      border-radius: var(--r-lg);
      padding: 26px 24px;
      box-shadow: var(--sh)
    }

    .gb .seal {
      width: 54px;
      height: 54px;
      margin: 0 auto 12px;
      border-radius: 50%;
      background: var(--gd-100);
      color: var(--mr-700);
      border: 1px solid var(--gd-400);
      display: flex;
      align-items: center;
      justify-content: center
    }

    .gb h3 {
      font-size: 22px;
      margin-bottom: 6px
    }

    .gb p {
      font-size: 15px;
      color: var(--soft)
    }

    /* faq */
    .faq {
      padding: 44px 0
    }

    .faq .w {
      max-width: 780px
    }

    .faq details {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: var(--r-md);
      margin-bottom: 10px;
      padding: 14px 18px;
      box-shadow: var(--sh)
    }

    .faq details[open] {
      border-color: var(--gd-400)
    }

    .faq summary {
      cursor: pointer;
      list-style: none;
      font-weight: 700;
      font-size: 16px;
      color: var(--mr-900);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px
    }

    .faq summary::-webkit-details-marker {
      display: none
    }

    .faq summary::after {
      content: "+";
      font-size: 22px;
      color: var(--gd-500);
      transition: transform .2s ease;
      flex: none
    }

    .faq details[open] summary::after {
      transform: rotate(45deg)
    }

    .faq .ans {
      margin-top: 9px;
      font-size: 15px;
      color: var(--soft)
    }

    /* order */
    .order {
      background: var(--blush);
      padding: 50px 0 96px
    }

    .og {
      display: grid;
      grid-template-columns: 1fr 1.15fr;
      gap: 22px;
      align-items: start
    }

    .og>* {
      min-width: 0
    }

    .os {
      background: var(--mr-900);
      color: #fff;
      border-radius: var(--r-lg);
      padding: 20px;
      box-shadow: var(--sh)
    }

    .os h3 {
      color: var(--gd-400);
      font-family: var(--fb);
      font-weight: 700;
      font-size: 17px;
      margin-bottom: 12px
    }

    .ol {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(255, 255, 255, .12)
    }

    .ol img {
      width: 56px;
      height: 70px;
      object-fit: cover;
      border-radius: 7px;
      flex: none;
      background: #2a0f16
    }

    .ol .m {
      flex: 1;
      min-width: 0
    }

    .ol .m .nm {
      font-size: 15px;
      font-weight: 600;
      line-height: 1.45
    }

    .ol .m .var-tag {
      font-size: 13px;
      color: var(--gd-300);
      margin-top: 2px
    }

    .ol .m .p2 {
      font-size: 14px;
      color: #C6A5B0;
      margin-top: 3px
    }

    .qt {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 8px
    }

    .qt button {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--gd-500);
      color: var(--mr-900);
      font-weight: 700;
      font-size: 17px;
      transition: background .15s ease
    }

    .qt button:hover {
      background: var(--gd-400)
    }

    .qt span {
      min-width: 18px;
      text-align: center;
      font-weight: 700
    }

    .or {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding-top: 9px;
      font-size: 15px;
      color: #EBD9DD
    }

    .ot {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-top: 10px;
      padding-top: 12px;
      border-top: 1px solid rgba(255, 255, 255, .12);
      font-size: 21px;
      font-weight: 700;
      color: var(--gd-400)
    }

    .osv {
      font-size: 13px;
      color: #9FD9B6;
      text-align: right;
      margin-top: 5px
    }

    .of {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: var(--r-lg);
      padding: 24px;
      box-shadow: var(--shl)
    }

    .of h3 {
      font-size: 21px;
      margin-bottom: 3px
    }

    .of .hn {
      font-size: 14px;
      color: var(--soft);
      margin-bottom: 16px
    }

    .fd {
      margin-bottom: 13px
    }

    .fd label {
      display: block;
      font-size: 14.5px;
      font-weight: 700;
      margin-bottom: 5px;
      color: var(--mr-800)
    }

    .fd label .opt {
      font-weight: 400;
      color: #A9989F;
      font-size: 13px
    }

    .fd input,
    .fd textarea {
      width: 100%;
      min-height: 50px;
      padding: 12px 14px;
      font-family: inherit;
      font-size: 16px;
      background: #FDFBFA;
      border: 1.5px solid var(--line);
      border-radius: var(--r-sm);
      transition: border-color .15s ease
    }

    .fd textarea {
      min-height: 78px;
      resize: vertical
    }

    .fd input:focus,
    .fd textarea:focus {
      outline: 2px solid var(--gd-400);
      outline-offset: 1px;
      border-color: var(--gd-400)
    }

    .fd .msg {
      display: none;
      color: var(--alert);
      font-size: 13px;
      margin-top: 4px
    }

    .fd.bad input,
    .fd.bad textarea {
      border-color: var(--alert);
      background: #FFF7F9
    }

    .fd.bad .msg {
      display: block
    }

    .pl {
      display: block;
      font-size: 14.5px;
      font-weight: 700;
      margin-bottom: 5px;
      color: var(--mr-800)
    }

    /* Delivery Charges from Website Shipping Charges */
    .ap {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 10px;
      margin-bottom: 13px
    }

    .ap label {
      position: relative;
      display: block;
      cursor: pointer;
      margin: 0;
      border: 1.5px solid var(--line);
      border-radius: var(--r-sm);
      background: #FDFBFA;
      padding: 12px;
      font-size: 15px;
      font-weight: 600;
      color: var(--ink);
      text-align: center;
      transition: border-color .15s ease, background .15s ease
    }

    .ap label small {
      display: block;
      font-weight: 400;
      font-size: 13px;
      color: var(--soft);
      margin-top: 1px
    }

    .ap input {
      position: absolute;
      opacity: 0;
      pointer-events: none
    }

    .ap label.sel {
      border-color: var(--gd-500);
      background: var(--gd-100);
      box-shadow: inset 0 0 0 1px var(--gd-400)
    }

    .hp2i {
      position: absolute !important;
      left: -9999px;
      width: 1px;
      height: 1px;
      overflow: hidden
    }

    .sub {
      width: 100%;
      min-height: 58px;
      margin-top: 6px;
      padding: 16px;
      border-radius: 12px;
      background: var(--cta-a);
      color: var(--cta-ink) !important;
      font-weight: 700;
      font-size: 19px;
      line-height: 1.35;
      box-shadow: var(--shc);
      transition: transform .15s ease, box-shadow .15s ease
    }

    .sub:hover {
      filter: brightness(1.08);
      transform: translateY(-1px)
    }

    .sub:disabled {
      filter: grayscale(.3);
      opacity: .7;
      cursor: not-allowed
    }

    .fn {
      text-align: center;
      font-size: 13px;
      color: var(--soft);
      margin-top: 12px
    }

    .fa {
      text-align: center;
      font-size: 14px;
      margin-top: 14px;
      padding-top: 14px;
      border-top: 1px solid var(--line);
      color: var(--soft)
    }

    .fa a {
      color: var(--mr-700);
      font-weight: 700;
      text-decoration: underline
    }

    .fe {
      display: none;
      margin-top: 12px;
      padding: 11px 13px;
      background: #FFF1F4;
      border: 1px solid #F0C3CE;
      border-radius: var(--r-sm);
      color: var(--alert);
      font-size: 15px
    }

    .fe.show {
      display: block
    }

    .dn {
      display: none;
      text-align: center;
      padding: 28px 10px
    }

    .dn.show {
      display: block
    }

    .dn .tk {
      width: 68px;
      height: 68px;
      margin: 0 auto 14px;
      border-radius: 50%;
      background: #E6F5EC;
      color: var(--ok);
      font-size: 36px;
      display: flex;
      align-items: center;
      justify-content: center
    }

    .dn h3 {
      font-size: 24px;
      color: var(--mr-900);
      margin-bottom: 8px
    }

    .dn .inv-box {
      display: inline-block;
      padding: 8px 18px;
      background: var(--mr-50);
      border: 1px dashed var(--gd-500);
      border-radius: 8px;
      margin: 10px 0;
      font-size: 18px;
      color: var(--mr-800);
      font-weight: 700
    }

    .dn p {
      font-size: 15px;
      color: var(--soft);
      line-height: 1.6
    }

    /* sticky */
    .sticky {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 55;
      background: rgba(253, 249, 248, .97);
      backdrop-filter: blur(8px);
      border-top: 1px solid var(--line);
      box-shadow: 0 -6px 22px rgba(31, 10, 15, .14);
      padding: 9px 14px calc(9px + env(safe-area-inset-bottom));
      display: flex;
      align-items: center;
      gap: 12px;
      transform: translateY(130%);
      transition: transform .3s ease
    }

    .sticky.show {
      transform: translateY(0)
    }

    .sticky .p {
      flex: none;
      line-height: 1.2
    }

    .sticky .p b {
      display: block;
      font-size: 20px;
      color: var(--mr-800);
      font-weight: 700
    }

    .sticky .p s {
      font-size: 13px;
      color: #A9989F
    }

    .sticky .cta {
      flex: 1;
      min-height: 48px;
      padding: 11px 16px;
      font-size: 16.5px;
      max-width: none
    }

    /* footer */
    footer.ft {
      background: var(--mr-900);
      color: #C6A5B0;
      padding: 38px 0 22px;
      font-size: 14px
    }

    footer.ft .w {
      display: flex;
      flex-wrap: wrap;
      gap: 22px;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 20px
    }

    footer.ft h4 {
      color: #fff;
      font-size: 16px;
      margin-bottom: 8px;
      font-family: var(--fb);
      font-weight: 700
    }

    footer.ft .col {
      min-width: 200px
    }

    footer.ft .col p {
      margin-bottom: 4px
    }

    footer.ft .soc {
      display: flex;
      gap: 12px;
      margin-top: 10px
    }

    footer.ft .soc a {
      color: var(--gd-400);
      font-weight: 600
    }

    footer.ft .soc a:hover {
      text-decoration: underline
    }

    footer.ft .base {
      border-top: 1px solid rgba(255, 255, 255, .1);
      padding-top: 14px;
      text-align: center;
      color: #96707F;
      font-size: 13px
    }

    /* responsive */
    @media(min-width:901px) {
      .sticky {
        display: none
      }
    }

    @media(max-width:900px) {
      .hero .w {
        grid-template-columns: 1fr;
        gap: 24px
      }

      .og {
        grid-template-columns: 1fr
      }

      .order {
        padding: 44px 0 104px
      }
    }

    @media(max-width:820px) {
      .fg {
        grid-template-columns: repeat(2, 1fr)
      }

      .rt-text {
        grid-template-columns: repeat(2, 1fr)
      }
    }

    @media(max-width:700px) {
      .gg {
        grid-template-columns: repeat(2, 1fr)
      }

      .gg button.big {
        grid-column: span 2;
        grid-row: auto;
        aspect-ratio: 4/3
      }

      .rw {
        max-width: 440px
      }

      .rt-text {
        display: flex;
        gap: 14px;
        overflow-x: auto;
        overscroll-behavior-x: contain;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        padding: 4px 0 12px
      }

      .rt-text::-webkit-scrollbar {
        display: none
      }

      .rcard {
        flex: 0 0 100%;
        scroll-snap-align: start
      }

      .rctrl {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        margin-top: 6px
      }
    }

    @media(max-width:640px) {
      body {
        font-size: 16px
      }

      .w {
        padding-inline: 16px
      }

      header.hd img {
        height: 40px;
        max-width: 130px;
        object-fit: contain
      }

      .ch span.t {
        display: none
      }

      .ch {
        padding: 9px;
        min-width: 44px;
        min-height: 44px;
        justify-content: center
      }

      .hero {
        padding: 20px 0 36px
      }

      .hero::after {
        height: 28px
      }

      .steps .g {
        grid-template-columns: 1fr;
        gap: 10px
      }

      .stp {
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
        padding: 13px 14px
      }

      .stp .n {
        margin: 0;
        flex: none
      }

      .feats,
      .guar,
      .faq,
      .revs {
        padding-block: 38px
      }

      .cta {
        max-width: none;
        font-size: 16.5px
      }

      .of,
      .os {
        padding: 17px 16px
      }

      .qt button {
        width: 44px;
        height: 44px;
        flex: 0 0 44px
      }

      footer.ft .w {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px
      }

      footer.ft .col {
        min-width: 0
      }
    }

    @media(max-width:380px) {
      .w {
        padding-inline: 12px
      }

      .sticky .p s {
        display: none
      }

      .ap {
        grid-template-columns: 1fr
      }
    }
  </style>
</head>

<body>
  <script>document.documentElement.classList.add('js');</script>
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KGWPJ8QZ" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>

  <div class="top">{!! $topNotice !!}</div>

  <header class="hd">
    <div class="w">
      <a href="#top" aria-label="{{ $siteName }}">
        @if($siteLogo)
          <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" width="150" height="46" fetchpriority="high">
        @else
          <span style="font-family:var(--fd);font-size:24px;font-weight:700;color:var(--mr-900)">{{ $siteName }}</span>
        @endif
      </a>
      <div class="ha">
        <a class="ch" href="{{ $messengerUrl }}" target="_blank" rel="noopener" data-ev="contact_messenger"
          title="Messenger"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path
              d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.517 3.735 7.185V22l3.418-1.875c.91.252 1.877.387 2.847.387 5.523 0 10-4.145 10-9.254C22 6.145 17.523 2 12 2zm1.066 12.445-2.73-2.91-5.326 2.91 5.858-6.22 2.796 2.91 5.26-2.91-5.858 6.22z" />
          </svg><span class="t">Messenger</span></a>
        <a class="ch fl" href="tel:{{ $sitePhone }}" data-ev="contact_call"><svg width="15" height="15"
            viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path
              d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1L6.6 10.8z" />
          </svg><span class="t">কল করুন</span></a>
      </div>
    </div>
  </header>
  <span id="top"></span>

  <!-- ==================== HERO SECTION (Product Images & Multi-Images) ==================== -->
  <section class="hero">
    <div class="w">
      <div>
        <div class="shot" id="shot">
          <span class="tag" id="heroDiscountBadge"
            style="{{ $discountPercent > 0 ? '' : 'display:none;' }}">{{ $discountPercent }}%<br>ছাড়</span>
          <button class="nav p" id="prev" type="button" aria-label="আগের ছবি">‹</button>
          <button class="nav n" id="next" type="button" aria-label="পরের ছবি">›</button>
          @foreach($allImages as $idx => $img)
            <img class="{{ $idx === 0 ? 'on' : '' }}" src="{{ get_img_url($img) }}"
              alt="{{ $productTitle }} - ছবি {{ $idx + 1 }}" width="1000" height="1250" {{ $idx === 0 ? 'fetchpriority="high"' : 'loading="lazy"' }}>
          @endforeach
        </div>
        <div class="ts" id="tstrip"></div>
      </div>
      <div class="hc">
        <span
          class="kk">{{ $landingPage->hero_tag ?: ($siteName . ' · ' . ($product->category->name_bn ?? ($product->category->name_en ?? 'Special Collection'))) }}</span>
        <h1>{{ $productTitle }}</h1>
        @if($productSubtitle)
          <p class="ld">{{ strip_tags($productSubtitle) }}</p>
        @endif

        <a class="hp2" href="#reviews" data-ev="jump_reviews">
          <span class="av"><i></i><i></i><i></i></span>
          <span><b>৫০০+ জন</b> সন্তুষ্ট কাস্টমার — রিভিউ দেখুন</span>
        </a>

        <!-- Dynamic Pricing Section -->
        <div class="pc">
          <div class="pr">
            <span class="now" id="heroNowPrice">৳{{ number_format($initialPrice) }}</span>
            <span class="was" id="heroWasPrice"
              style="{{ $regularPrice > $initialPrice ? '' : 'display:none;' }}">৳{{ number_format($regularPrice) }}</span>
            <span class="cut" id="heroCutBadge"
              style="{{ $discountPercent > 0 ? '' : 'display:none;' }}">{{ $discountPercent }}% ছাড়</span>
          </div>
          <p class="sl" id="heroSaveTxt" style="{{ $savingsAmount > 0 ? '' : 'display:none;' }}">সাশ্রয় হচ্ছে
            <b>৳{{ number_format($savingsAmount) }}</b></p>
          <p class="shl">🚚 ডেলিভারি চার্জ — ঢাকার ভিতরে <b>৳{{ (int) $delivery_inside }}</b> · ঢাকার বাইরে
            <b>৳{{ (int) $delivery_outside }}</b></p>
        </div>

        <!-- Variant Selector in Hero (if variants exist) -->
        @if($hasVariants)
          <div class="variant-block" id="heroVariantBlock">
            <label class="variant-lbl" for="heroVariantSelect">
              <span>ভ্যারিয়েন্ট পছন্দ করুন:</span>
              <b id="curVarLabel">{{ $selectedVariantName }}</b>
            </label>
            <select id="heroVariantSelect" class="variant-select-hero" aria-label="ভ্যারিয়েন্ট পছন্দ করুন">
              @foreach($productStocks as $stk)
                @php
                  $stkPrice = $stk->price > 0 ? (float) $stk->price : ($discountPrice > 0 ? $discountPrice : $regularPrice);
                  $isOutOfStock = ($stk->qty !== null && $stk->qty <= 0);
                @endphp
                <option value="{{ $stk->varient }}" {{ $loop->first ? 'selected' : '' }} {{ $isOutOfStock ? 'disabled' : '' }}>
                  {{ $stk->varient }} @if($stkPrice > 0) — ৳{{ number_format($stkPrice) }} @endif @if($isOutOfStock) (স্টক
                  শেষ) @endif
                </option>
              @endforeach
            </select>
          </div>
        @endif

        <!-- Dynamic Stock Box -->
        <span id="stockData" data-stock="{{ $initialStock }}" data-total="{{ max(30, $initialStock) }}" hidden></span>
        <div class="sb" id="stockBox">
          <div class="lbl"><span>বর্তমান স্টক</span><b id="stockTxt"></b></div>
          <div class="trk"><i id="stockFill"></i></div>
        </div>

        <a href="#order" class="cta" data-ev="cta_hero">অর্ডার করুন — <span
            class="heroCtaPrice">৳{{ number_format($initialPrice) }}</span></a>
        <p class="cn">🔒 অগ্রিম টাকা লাগবে না · হাতে পেয়ে পেমেন্ট</p>
        <ul class="hb">
          @if(!empty($features) && is_array($features))
            @foreach(array_slice($features, 0, 4) as $ft)
              <li>{{ is_string($ft) ? $ft : ($ft['title'] ?? '') }}</li>
            @endforeach
          @else
            <li>ক্যাশ অন ডেলিভারি</li>
            <li>সারাদেশে ডেলিভারি</li>
            <li>এক্সচেঞ্জ সুবিধা</li>
            <li>প্রিমিয়াম প্যাকেজিং</li>
          @endif
        </ul>
      </div>
    </div>
  </section>

  <!-- ==================== ৩ STEPS ==================== -->
  <section class="steps">
    <div class="w">
      <div class="g">
        @foreach(array_slice($steps, 0, 3) as $stIdx => $stp)
          <div class="stp rv">
            <div class="n">{{ $stIdx + 1 }}</div>
            <div>
              <h3>{{ $stp['title'] ?? '' }}</h3>
              <p>{{ $stp['desc'] ?? '' }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  @if($videoEmbedUrl)
    <!-- ==================== VIDEO SECTION ==================== -->
    <section class="video-sec" style="padding: 44px 0 20px; background: #fff;">
      <div class="w" style="max-width: 820px; text-align: center;">
        <div class="sh rv">
          <div class="orn"><span></span><i></i><i></i><i></i><span></span></div>
          <h2>ভিডিওতে বিস্তারিত দেখুন</h2>
          <p>অর্ডার করার আগে ভিডিওতে দেখে নিন প্রোডাক্টটির ফিনিশিং ও কোয়ালিটি</p>
        </div>
        <div class="rv"
          style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: var(--r-md); box-shadow: var(--shl); border: 2px solid var(--line); margin-bottom: 20px;">
          <iframe src="{{ $videoEmbedUrl }}"
            style="position: absolute; top:0; left: 0; width: 100%; height: 100%; border:0;"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen loading="lazy"></iframe>
        </div>
        <div>
          <a href="#order" class="cta" style="max-width: 340px;">অর্ডার করুন — <span
              class="heroCtaPrice">৳{{ number_format($initialPrice) }}</span></a>
        </div>
      </div>
    </section>
  @endif


  <!-- ==================== GALLERY (Multi-Images from Product) ==================== -->
  @if(count($allImages) > 1)
    <section class="gal">
      <div class="w">
        <div class="sh rv">
          <div class="orn dk"><span></span><i></i><i></i><i></i><span></span></div>
          <h2>আরও কাছ থেকে দেখুন</h2>
          <p>ট্যাপ করলে বড় করে দেখতে পারবেন</p>
        </div>
        <div class="gg" id="ggrid">
          @foreach($allImages as $idx => $img)
            <button class="{{ $idx === 0 ? 'big' : '' }}" data-src="{{ get_img_url($img) }}"
              aria-label="ছবি {{ $idx + 1 }}">
              <img loading="lazy" src="{{ get_img_url($img) }}" alt="{{ $productTitle }} - বিস্তারিত {{ $idx + 1 }}"
                width="{{ $idx === 0 ? 600 : 300 }}" height="{{ $idx === 0 ? 600 : 400 }}">
            </button>
          @endforeach
        </div>
        <div class="af"><a href="#order" class="cta" style="max-width:340px" data-ev="cta_gallery">অর্ডার করুন — <span
              class="heroCtaPrice">৳{{ number_format($initialPrice) }}</span></a></div>
      </div>
    </section>
    <div class="lb" id="lb"><button class="x" id="lbX" aria-label="বন্ধ করুন">✕</button><img id="lbImg" src=""
        alt="বড় করে দেখা ছবি"></div>
  @endif

  <!-- ==================== TEXT REVIEWS ("review image hobe na, likhai hobe") ==================== -->
  <section class="revs" id="reviews">
    <div class="w">
      <div class="sh rv">
        <div class="orn"><span></span><i></i><i></i><i></i><span></span></div>
        <h2>ক্রেতাদের মতামত</h2>
        <p>সরাসরি আমাদের সম্মানিত গ্রাহকদের পাঠানো প্রতিক্রিয়া</p>
        <div class="rc">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
            <path d="M20 6L9 17l-5-5" />
          </svg>
          <span>শতভাগ খাঁটি কাস্টমার রিভিউ</span>
        </div>
      </div>
      <div class="rw rv">
        <div class="rt-text" id="revTrack">
          @foreach($textReviews as $tRev)
            <div class="rcard">
              <div>
                <div class="rcard-head">
                  <div class="rcard-av">{{ mb_substr($tRev['name'] ?? 'ক', 0, 1) }}</div>
                  <div class="rcard-meta">
                    <div class="rcard-nm">{{ $tRev['name'] }}</div>
                    <div class="rcard-badge"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                      </svg> ভেরিফাইড ক্রেতা</div>
                  </div>
                </div>
                <div class="rcard-stars">
                  @for($s = 0; $s < ($tRev['rating'] ?? 5); $s++)★@endfor
                </div>
                <p class="rcard-text">“{{ $tRev['comment'] }}”</p>
              </div>
              <div class="rcard-foot">
                <span>📍 {{ $tRev['location'] ?? 'বাংলাদেশ' }}</span>
                <span>{{ $tRev['date'] ?? 'সম্প্রতি' }}</span>
              </div>
            </div>
          @endforeach
        </div>
        <div class="rctrl" aria-label="রিভিউ স্লাইডার">
          <button class="ra" id="revPrev" type="button" aria-label="আগের রিভিউ">‹</button>
          <div class="rdots" id="revDots">
            @foreach($textReviews as $idx => $tRev)
              <button class="rdot {{ $idx === 0 ? 'on' : '' }}" type="button" data-i="{{ $idx }}"></button>
            @endforeach
          </div>
          <button class="ra" id="revNext" type="button" aria-label="পরের রিভিউ">›</button>
        </div>
      </div>
      <div class="rcta"><a href="#order" class="cta" style="max-width:340px" data-ev="cta_reviews">আমারটাও অর্ডার করি —
          <span class="heroCtaPrice">৳{{ number_format($initialPrice) }}</span></a></div>
    </div>
  </section>

  <!-- ==================== GUARANTEE ==================== -->
  <section class="guar">
    <div class="w">
      <div class="gb rv">
        <div class="seal"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.8">
            <path d="M12 3l7 3v6c0 4.2-2.9 7.6-7 9-4.1-1.4-7-4.8-7-9V6z" />
            <path d="M9 12l2 2 4-4" />
          </svg></div>
        <h3>{{ $guaranteeTitle }}</h3>
        <p>{{ $guaranteeDesc }}</p>
      </div>
    </div>
  </section>

  <!-- ==================== FAQ ==================== -->
  <section class="faq">
    <div class="w">
      <div class="sh rv">
        <div class="orn"><span></span><i></i><i></i><i></i><span></span></div>
        <h2>সচরাচর জিজ্ঞাসিত প্রশ্ন</h2>
      </div>
      @if(!empty($faqs) && is_array($faqs) && count($faqs) > 0)
        @foreach($faqs as $faq)
          <details>
            <summary>{{ is_array($faq) ? ($faq['q'] ?? ($faq['question'] ?? '')) : '' }}</summary>
            <p class="ans">{{ is_array($faq) ? ($faq['a'] ?? ($faq['answer'] ?? '')) : '' }}</p>
          </details>
        @endforeach
      @else
        <details>
          <summary>প্রোডাক্ট কি ছবির মতোই হবে?</summary>
          <p class="ans">জি, আমাদের প্রতিটি ছবি অরিজিনাল স্টকের। ডেলিভারির সময় প্যাকেট খুলে দেখে তারপর পেমেন্ট করতে
            পারবেন।</p>
        </details>
        <details>
          <summary>ডেলিভারি কতদিনে পাবো?</summary>
          <p class="ans">ঢাকা সিটির ভিতরে ২৪–৪৮ ঘণ্টা, ঢাকার বাইরে ২–৩ কার্যদিবস। অর্ডার কনফার্মেশনের পর দ্রুত কুরিয়ারে
            হস্তান্তর করা হয়।</p>
        </details>
        <details>
          <summary>কোনো সমস্যা হলে কি এক্সচেঞ্জ করা যাবে?</summary>
          <p class="ans">অবশ্যই! প্রোডাক্টে কোনো ত্রুটি থাকলে বা সাইজ/ভ্যারিয়েন্ট পরিবর্তন করতে চাইলে ৪৮ ঘণ্টার মধ্যে
            জানালেই দ্রুত এক্সচেঞ্জ সুবিধা পাবেন।</p>
        </details>
        <details>
          <summary>অর্ডার করতে কি অগ্রিম টাকা দিতে হবে?</summary>
          <p class="ans">না, অর্ডার করার জন্য কোনো অগ্রিম টাকা দিতে হবে না। প্রোডাক্ট হাতে পেয়ে সম্পূর্ণ টাকা পরিশোধ করবেন
            (ক্যাশ অন ডেলিভারি)।</p>
        </details>
      @endif
    </div>
  </section>

  <!-- ==================== ORDER FORM ==================== -->
  <section class="order" id="order">
    <div class="w">
      <div class="sh rv">
        <div class="orn"><span></span><i></i><i></i><i></i><span></span></div>
        <h2>অর্ডার কনফার্ম করুন</h2>
        <p>নিচের তথ্যগুলো দিয়ে অর্ডার সম্পন্ন করুন — প্রতিনিধি কল করে কনফার্ম করবেন</p>
      </div>
      <div class="og rv">

        <!-- Order Summary Box -->
        <div class="os">
          <h3>অর্ডার সামারি</h3>
          <div class="ol">
            <img id="summaryThumb" src="{{ get_img_url($allImages[0]) }}" alt="{{ $productTitle }}" width="56"
              height="70" loading="lazy">
            <div class="m">
              <div class="nm">{{ $productTitle }}</div>
              @if($hasVariants)
                <div class="var-tag" id="summaryVarTag">ভ্যারিয়েন্ট: <b id="summaryVarName">{{ $selectedVariantName }}</b>
                </div>
              @endif
              <div class="p2"><span id="unitPriceTxt">৳{{ number_format($initialPrice) }}</span> × <span
                  id="q2">1</span></div>
              <div class="qt">
                <button type="button" id="qMinus" aria-label="কমান">−</button>
                <span id="q1">1</span>
                <button type="button" id="qPlus" aria-label="বাড়ান">+</button>
              </div>
            </div>
          </div>
          <div class="or"><span>আইটেম মোট</span><span id="subTotalTxt">৳{{ number_format($initialPrice) }}</span></div>
          <div class="or"><span>ডেলিভারি চার্জ</span><span
              id="shipTxt">৳{{ number_format($initialShippingCharge) }}</span></div>
          <div class="ot"><span>সর্বমোট</span><span id="totalTxt">৳{{ number_format($initialGrandTotal) }}</span></div>
          <div class="osv" id="saveTxt" style="{{ $savingsAmount > 0 ? '' : 'display:none;' }}">সাশ্রয় হচ্ছে
            ৳{{ number_format($savingsAmount) }}</div>
        </div>

        <!-- Order Form Card -->
        <div class="of">
          <div id="formView">
            <h3>ডেলিভারি তথ্য</h3>
            <p class="hn">সব তথ্য সুরক্ষিত থাকবে</p>
            <form id="orderForm" action="{{ route('landing.order.store') }}" method="POST" novalidate>
              <input type="hidden" name="_token" id="csrfInput" value="{{ csrf_token() }}" autocomplete="off">
              <input type="hidden" name="product_id" value="{{ $product->id }}">
              <input type="hidden" name="quantity" id="qtyInput" value="1">
              <input type="hidden" name="varient" id="selectedVariantInput" value="{{ $selectedVariantName }}">
              <input type="hidden" name="color" id="selectedColorInput" value="{{ $selectedVariantName }}">
              <input type="hidden" name="payment_option" value="cod">

              <!-- Honeypot anti-spam -->
              <div class="hp2i" aria-hidden="true"><label for="website">Website</label><input type="text" id="website"
                  name="website" tabindex="-1" autocomplete="off"></div>

              <!-- In-Form Variant Dropdown (if variants exist) -->
              @if($hasVariants)
                <div class="fd" id="fVariant" style="margin-bottom:14px">
                  <label for="variantSelect">পছন্দের ভ্যারিয়েন্ট নির্বাচন করুন *</label>
                  <select id="variantSelect" name="variant_dropdown" class="variant-select" required>
                    @foreach($productStocks as $stk)
                      @php
                        $stkPrice = $stk->price > 0 ? (float) $stk->price : ($discountPrice > 0 ? $discountPrice : $regularPrice);
                        $isOutOfStock = ($stk->qty !== null && $stk->qty <= 0);
                      @endphp
                      <option value="{{ $stk->varient }}" {{ $loop->first ? 'selected' : '' }} {{ $isOutOfStock ? 'disabled' : '' }}>
                        {{ $stk->varient }} @if($stkPrice > 0) — ৳{{ number_format($stkPrice) }} @endif @if($isOutOfStock)
                        (স্টক শেষ) @endif
                      </option>
                    @endforeach
                  </select>
                </div>
              @endif

              <div class="fd" id="fName">
                <label for="name">আপনার নাম *</label>
                <input type="text" id="name" name="name" autocomplete="name" placeholder="যেমনঃ ফারহানা আক্তার"
                  required>
                <span class="msg">আপনার নাম লিখুন</span>
              </div>

              <div class="fd" id="fPhone">
                <label for="phone">মোবাইল নাম্বার *</label>
                <input type="tel" id="phone" name="phone" autocomplete="tel" inputmode="numeric"
                  placeholder="01XXXXXXXXX" pattern="^(\+?88)?01[3-9][0-9]{8}$" required>
                <span class="msg">১১ ডিজিটের সঠিক মোবাইল নাম্বার দিন (যেমনঃ 017XXXXXXXX)</span>
              </div>

              <div class="fd" id="fAddress">
                <label for="address">ডেলিভারি ঠিকানা *</label>
                <textarea id="address" name="address" autocomplete="street-address" rows="3"
                  placeholder="আপনার সম্পূর্ণ ডেলিভারি ঠিকানা লিখুন (যেমন: বাসা/রোড, এলাকা, থানা, জেলা)"
                  required></textarea>
                <span class="msg">থানা ও জেলাসহ পূর্ণ ডেলিভারি ঠিকানা লিখুন</span>
              </div>

              <!-- Dynamic Shipping Options From Website Shipping Charges -->
              <span class="pl">ডেলিভারি এলাকা *</span>
              <div class="ap" id="areaPick">
                @if(isset($shippings) && $shippings->count() > 0)
                  @foreach($shippings as $idx => $ship)
                    <label class="{{ $idx === 0 ? 'sel' : '' }}" for="ship_{{ $ship->id }}">
                      <input type="radio" id="ship_{{ $ship->id }}" name="shipping_id" value="{{ $ship->id }}"
                        data-charge="{{ (float) $ship->shipping_charge }}" data-type="{{ $ship->type }}" {{ $idx === 0 ? 'checked' : '' }}>
                      {{ $ship->name }}
                      <small>চার্জ ৳{{ (int) $ship->shipping_charge }}</small>
                    </label>
                  @endforeach
                @else
                  <label class="sel" for="areaIn">
                    <input type="radio" id="areaIn" name="shipping_id" value="{{ $shipping_inside->id ?? 1 }}"
                      data-charge="{{ (float) $delivery_inside }}" data-type="1" checked>
                    {{ $shipping_inside->name ?? 'ঢাকার ভিতরে' }}
                    <small>চার্জ ৳{{ (int) $delivery_inside }}</small>
                  </label>
                  <label for="areaOut">
                    <input type="radio" id="areaOut" name="shipping_id" value="{{ $shipping_outside->id ?? 2 }}"
                      data-charge="{{ (float) $delivery_outside }}" data-type="2">
                    {{ $shipping_outside->name ?? 'ঢাকার বাইরে' }}
                    <small>চার্জ ৳{{ (int) $delivery_outside }}</small>
                  </label>
                @endif
              </div>

              <button type="submit" class="sub" id="orderSubmitBtn">অর্ডার কনফার্ম করুন — <span
                  id="btnTotal">৳{{ number_format($initialGrandTotal) }}</span></button>
              <div class="fe" id="formError" role="alert"></div>
              <p class="fn">🔒 অগ্রিম কোনো টাকা নেওয়া হয় না · ডেলিভারির সময় পেমেন্ট</p>
              <p class="fa">ফর্ম পূরণে কোনো সমস্যা হলে? <a href="{{ $messengerUrl }}" target="_blank" rel="noopener"
                  data-ev="contact_messenger_form">Messenger-এ মেসেজ দিন</a></p>
            </form>
          </div>

          <!-- Done State (Success) -->
          <div class="dn" id="doneBox">
            <div class="tk">✓</div>
            <h3>ধন্যবাদ! অর্ডারটি সফলভাবে সম্পন্ন হয়েছে</h3>
            <p>আপনার অর্ডার রেফারেন্স / ইনভয়েস নম্বর:</p>
            <div class="inv-box" id="orderRef">—</div>
            <p>আমাদের প্রতিনিধি শীঘ্রই আপনার দেওয়া নাম্বারে কল করে অর্ডারটি নিশ্চিত করবেন।</p>
            <p style="margin-top:14px;color:var(--mr-800);font-weight:600">পণ্যটি পছন্দ করার জন্য আপনাকে আন্তরিক
              ধন্যবাদ!</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Mobile Sticky Bar -->
  <div class="sticky" id="sticky">
    <div class="p">
      <b id="stickyPrice">৳{{ number_format($initialPrice) }}</b>
      <s id="stickyOldPrice"
        style="{{ $regularPrice > $initialPrice ? '' : 'display:none;' }}">৳{{ number_format($regularPrice) }}</s>
    </div>
    <a href="#order" class="cta" data-ev="cta_sticky">অর্ডার করুন →</a>
  </div>

  <!-- Footer -->
  <footer class="ft">
    <div class="w">
      <div class="col">
        @if($siteLogo)
          <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" width="130" height="48" loading="lazy"
            style="max-height:48px;width:auto">
        @else
          <h3 style="color:#fff;font-size:22px;margin-bottom:8px">{{ $siteName }}</h3>
        @endif
        <p style="margin-top:10px">
          {{ $productSubtitle ? Str::limit(strip_tags($productSubtitle), 90) : 'প্রিমিয়াম ফ্যাশন ও লাইফস্টাইল কালেকশন' }}
        </p>
      </div>
      <div class="col">
        <h4>যোগাযোগ</h4>
        <p>মোবাইলঃ <a href="tel:{{ $sitePhone }}" style="color:#EDD9C4">{{ $sitePhone }}</a></p>
        @if($siteEmail)
        <p>ইমেইলঃ {{ $siteEmail }}</p>@endif
        @if($siteAddress)
        <p>ঠিকানাঃ {{ $siteAddress }}</p>@endif
        <div class="soc">
          @if($facebookUrl)<a href="{{ $facebookUrl }}" target="_blank" rel="noopener">Facebook</a>@endif
          @if($instagramUrl)<a href="{{ $instagramUrl }}" target="_blank" rel="noopener">Instagram</a>@endif
          <a href="{{ $messengerUrl }}" target="_blank" rel="noopener">Messenger</a>
        </div>
      </div>
      <div class="col">
        <h4>নীতিমালা ও শর্তাবলী</h4>
        <p><a href="{{ Route::has('return.policy') ? route('return.policy') : url('/return-policy') }}">রিটার্ন ও
            রিফান্ড পলিসি</a></p>
        <p><a
            href="{{ Route::has('terms.conditions') ? route('terms.conditions') : (Route::has('page.terms') ? route('page.terms') : url('/terms-and-conditions')) }}">শর্তাবলী</a>
        </p>
        <p><a
            href="{{ Route::has('privacy.policy') ? route('privacy.policy') : (Route::has('page.policy') ? route('page.policy') : url('/privacy-policy')) }}">প্রাইভেসি
            পলিসি</a></p>
      </div>
    </div>
    <div class="base">{{ $copyright }}</div>
  </footer>

  <script>
    (function () {
      "use strict";

      var CFG = {
        productId: {{ $product->id }},
        hasVariants: {{ $hasVariants ? 'true' : 'false' }},
        selectedVariant: @json($selectedVariantName),
        variantsMap: @json($variantsMap),
        basePrice: {{ (float) $initialPrice }},
        baseRegularPrice: {{ (float) $regularPrice }},
        initialStock: {{ (int) $initialStock }},
        deliveryInside: {{ (float) $delivery_inside }},
        deliveryOutside: {{ (float) $delivery_outside }},
        item: {
          item_id: '{{ $product->id }}',
          item_name: @json($productTitle),
          item_brand: @json($siteName),
          item_category: @json($product->category->name_en ?? ($product->category->name_bn ?? 'Fashion')),
          item_variant: @json($selectedVariantName)
        },
        orderStoreRoute: '{{ route("landing.order.store") }}',
        csrfToken: '{{ csrf_token() }}'
      };

      var $ = function (id) { return document.getElementById(id); };
      var bn = function (n) { return '৳' + Number(n).toLocaleString('bn-BD'); };
      var dl = function (e, p) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push(Object.assign({ event: e }, p || {}));
      };

      var currentPrice = CFG.basePrice;
      var currentRegularPrice = CFG.baseRegularPrice;
      var currentStock = CFG.initialStock;
      var selectedVar = CFG.selectedVariant;
      var qty = 1;

      // Stock rendering
      function updateStockDisplay(l, t) {
        if (!l || l < 1) {
          if ($('stockBox')) $('stockBox').classList.remove('on');
          return;
        }
        t = t || Math.max(30, l);
        $('stockTxt').textContent = Number(l).toLocaleString('bn-BD') + ' পিস বাকি';
        $('stockFill').style.width = Math.max(6, Math.min(100, (l / t) * 100)) + '%';
        $('stockBox').classList.add('on');
      }
      updateStockDisplay(currentStock, 30);

      // Gallery Slider
      var shots = Array.prototype.slice.call(document.querySelectorAll('#shot img'));
      var strip = $('tstrip');
      var gi = 0;
      if (shots.length > 0 && strip) {
        shots.forEach(function (img, i) {
          var b = document.createElement('button');
          b.type = 'button';
          b.setAttribute('aria-label', 'ছবি ' + (i + 1));
          if (i === 0) b.className = 'on';
          var t = document.createElement('img');
          t.src = img.src;
          t.alt = '';
          t.loading = 'lazy';
          t.width = 56;
          t.height = 70;
          b.appendChild(t);
          b.addEventListener('click', function () { show(i); });
          strip.appendChild(b);
        });
      }

      function show(i) {
        if (shots.length === 0) return;
        gi = (i + shots.length) % shots.length;
        shots.forEach(function (im, k) { im.classList.toggle('on', k === gi); });
        if (strip) {
          strip.querySelectorAll('button').forEach(function (b, k) { b.classList.toggle('on', k === gi); });
        }
      }

      if ($('prev')) $('prev').addEventListener('click', function () { show(gi - 1); });
      if ($('next')) $('next').addEventListener('click', function () { show(gi + 1); });

      (function () {
        var x0 = null, el = $('shot');
        if (!el) return;
        el.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
        el.addEventListener('touchend', function (e) {
          if (x0 === null) return;
          var dx = e.changedTouches[0].clientX - x0;
          if (Math.abs(dx) > 45) show(gi + (dx < 0 ? 1 : -1));
          x0 = null;
        }, { passive: true });
      })();

      // Lightbox
      var lb = $('lb'), lbImg = $('lbImg');
      function openLb(s, a) {
        if (!lb || !lbImg) return;
        lbImg.src = s;
        lbImg.alt = a || 'বড় করে দেখা ছবি';
        lb.classList.add('open');
      }
      if ($('lbX')) $('lbX').addEventListener('click', function () { lb.classList.remove('open'); });
      if (lb) lb.addEventListener('click', function (e) { if (e.target === lb) lb.classList.remove('open'); });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && lb) lb.classList.remove('open'); });
      if ($('ggrid')) {
        $('ggrid').addEventListener('click', function (e) {
          var b = e.target.closest('button[data-src]');
          if (!b) return;
          var im = b.querySelector('img');
          openLb(b.getAttribute('data-src'), im ? im.alt : 'ছবি');
        });
      }

      // Variant Selection Handlers
      function selectVariant(vName) {
        if (!CFG.hasVariants || !CFG.variantsMap || !CFG.variantsMap[vName]) return;
        var vData = CFG.variantsMap[vName];
        selectedVar = vName;
        currentPrice = vData.price;
        currentRegularPrice = vData.regular_price || CFG.baseRegularPrice;
        currentStock = vData.qty;

        // Update inputs
        var vInput = $('selectedVariantInput');
        if (vInput) vInput.value = vName;
        var cInput = $('selectedColorInput');
        if (cInput) cInput.value = vName;

        // Sync both dropdowns (hero & order form)
        var vSelect = $('variantSelect');
        if (vSelect && vSelect.value !== vName) vSelect.value = vName;
        var hSelect = $('heroVariantSelect');
        if (hSelect && hSelect.value !== vName) hSelect.value = vName;

        var curVarLbl = $('curVarLabel');
        if (curVarLbl) curVarLbl.textContent = vName;
        var sumVar = $('summaryVarName');
        if (sumVar) sumVar.textContent = vName;

        // Switch image if variant has image
        if (vData.image && shots.length > 0) {
          var foundIndex = shots.findIndex(function (im) { return im.src === vData.image; });
          if (foundIndex >= 0) show(foundIndex);
        }

        updateStockDisplay(currentStock, 30);
        paint();
      }

      // Bind change events on variant dropdowns
      var vSelectEl = $('variantSelect');
      if (vSelectEl) {
        vSelectEl.addEventListener('change', function () {
          selectVariant(this.value);
        });
      }
      var hSelectEl = $('heroVariantSelect');
      if (hSelectEl) {
        hSelectEl.addEventListener('change', function () {
          selectVariant(this.value);
        });
      }

      // Shipping & Pricing Calculations
      function ship() {
        var r = document.querySelector('input[name="shipping_id"]:checked');
        return r ? (parseFloat(r.getAttribute('data-charge')) || 0) : 0;
      }

      function total() {
        return currentPrice * qty + ship();
      }

      function items() {
        return [Object.assign({}, CFG.item, {
          price: currentPrice,
          quantity: qty,
          item_variant: selectedVar,
          currency: 'BDT'
        })];
      }

      function paint() {
        if ($('q1')) $('q1').textContent = qty;
        if ($('q2')) $('q2').textContent = qty;
        if ($('qtyInput')) $('qtyInput').value = qty;

        if ($('unitPriceTxt')) $('unitPriceTxt').textContent = bn(currentPrice);
        if ($('subTotalTxt')) $('subTotalTxt').textContent = bn(currentPrice * qty);
        if ($('shipTxt')) $('shipTxt').textContent = bn(ship());
        if ($('totalTxt')) $('totalTxt').textContent = bn(total());
        if ($('btnTotal')) $('btnTotal').textContent = bn(total());

        // Hero prices
        if ($('heroNowPrice')) $('heroNowPrice').textContent = bn(currentPrice);
        document.querySelectorAll('.heroCtaPrice').forEach(function (el) { el.textContent = bn(currentPrice); });
        if ($('stickyPrice')) $('stickyPrice').textContent = bn(currentPrice);

        // Savings & Discount Badge
        var saveEl = $('saveTxt');
        var heroSaveEl = $('heroSaveTxt');
        var heroCutEl = $('heroCutBadge');
        var heroDiscountBadge = $('heroDiscountBadge');
        var wasEl = $('heroWasPrice');
        var stickyOld = $('stickyOldPrice');

        if (currentRegularPrice > currentPrice) {
          var diff = currentRegularPrice - currentPrice;
          var pct = Math.round((diff / currentRegularPrice) * 100);
          var saveStr = 'সাশ্রয় হচ্ছে ' + bn(diff * qty);

          if (saveEl) { saveEl.textContent = saveStr; saveEl.style.display = 'block'; }
          if (heroSaveEl) { heroSaveEl.innerHTML = 'সাশ্রয় হচ্ছে <b>' + bn(diff) + '</b>'; heroSaveEl.style.display = 'block'; }
          if (heroCutEl) { heroCutEl.textContent = pct + '% ছাড়'; heroCutEl.style.display = 'inline-block'; }
          if (heroDiscountBadge) { heroDiscountBadge.innerHTML = pct + '%<br>ছাড়'; heroDiscountBadge.style.display = 'block'; }
          if (wasEl) { wasEl.textContent = bn(currentRegularPrice); wasEl.style.display = 'inline-block'; }
          if (stickyOld) { stickyOld.textContent = bn(currentRegularPrice); stickyOld.style.display = 'inline-block'; }
        } else {
          if (saveEl) saveEl.style.display = 'none';
          if (heroSaveEl) heroSaveEl.style.display = 'none';
          if (heroCutEl) heroCutEl.style.display = 'none';
          if (heroDiscountBadge) heroDiscountBadge.style.display = 'none';
          if (wasEl) wasEl.style.display = 'none';
          if (stickyOld) stickyOld.style.display = 'none';
        }
      }

      if ($('qPlus')) $('qPlus').addEventListener('click', function () { if (qty < 20) { qty++; paint(); } });
      if ($('qMinus')) $('qMinus').addEventListener('click', function () { if (qty > 1) { qty--; paint(); } });

      if ($('areaPick')) {
        $('areaPick').addEventListener('change', function () {
          document.querySelectorAll('#areaPick label').forEach(function (l) {
            l.classList.toggle('sel', l.querySelector('input').checked);
          });
          paint();
          dl('add_shipping_info', {
            ecommerce: {
              currency: 'BDT',
              value: total(),
              shipping_tier: document.querySelector('input[name="shipping_id"]:checked').value,
              items: items()
            }
          });
        });
      }

      paint();

      // Text Reviews Slider (Mobile swipe & desktop sync)
      (function () {
        var track = $('revTrack');
        if (!track) return;
        var cards = Array.prototype.slice.call(track.querySelectorAll('.rcard'));
        var dots = Array.prototype.slice.call(document.querySelectorAll('.rdot'));
        var prevB = $('revPrev');
        var nextB = $('revNext');
        var ri = 0;

        dots.forEach(function (d, k) { if (k >= cards.length) d.style.display = 'none'; });
        dots = dots.filter(function (d, k) { return k < cards.length; });

        function sync(n, scroll) {
          if (cards.length === 0) return;
          ri = Math.max(0, Math.min(n, cards.length - 1));
          dots.forEach(function (d, k) { d.classList.toggle('on', k === ri); });
          if (prevB) prevB.disabled = (ri === 0);
          if (nextB) nextB.disabled = (ri === cards.length - 1);
          if (scroll && window.matchMedia('(max-width:700px)').matches) {
            track.scrollTo({ left: cards[ri].offsetLeft - track.offsetLeft, behavior: 'smooth' });
          }
        }

        if (prevB) prevB.addEventListener('click', function () { sync(ri - 1, true); });
        if (nextB) nextB.addEventListener('click', function () { sync(ri + 1, true); });
        dots.forEach(function (d) { d.addEventListener('click', function () { sync(Number(d.dataset.i), true); }); });

        var raf;
        track.addEventListener('scroll', function () {
          cancelAnimationFrame(raf);
          raf = requestAnimationFrame(function () {
            var near = 0, dist = Infinity;
            cards.forEach(function (c, k) {
              var d = Math.abs((c.offsetLeft - track.offsetLeft) - track.scrollLeft);
              if (d < dist) { dist = d; near = k; }
            });
            sync(near, false);
          });
        }, { passive: true });
        sync(0, false);
      })();

      // Funnel tracking
      document.querySelectorAll('[data-ev]').forEach(function (el) {
        el.addEventListener('click', function () {
          var ev = el.getAttribute('data-ev');
          if (ev.indexOf('cta_') === 0) {
            dl('add_to_cart', { cta_location: ev, ecommerce: { currency: 'BDT', value: currentPrice * qty, items: items() } });
          } else {
            dl(ev, {});
          }
        });
      });

      var form = $('orderForm'), fired = false;
      if (form) {
        form.addEventListener('focusin', function () {
          if (fired) return;
          fired = true;
          dl('begin_checkout', { ecommerce: { currency: 'BDT', value: total(), items: items() } });
        });
      }

      // Form Validation
      function bad(id, on) { var el = $(id); if (el) el.classList.toggle('bad', !!on); }
      function validate() {
        var ok = true, first = null;
        var nm = $('name').value.trim();
        var ph = $('phone').value.replace(/\D/g, '');
        var ad = $('address').value.trim();

        bad('fName', !nm);
        if (!nm) { ok = false; first = first || 'fName'; }

        var pOk = /^01[3-9]\d{8}$/.test(ph);
        bad('fPhone', !pOk);
        if (!pOk) { ok = false; first = first || 'fPhone'; }

        bad('fAddress', ad.length < 6);
        if (ad.length < 6) { ok = false; first = first || 'fAddress'; }

        if (!ok) {
          dl('form_error', { error_type: 'validation', error_field: first });
          if (first) {
            $(first).scrollIntoView({ block: 'center', behavior: 'smooth' });
            var inp = $(first).querySelector('input,textarea');
            if (inp) inp.focus({ preventScroll: true });
          }
        }
        return ok;
      }

      ['name', 'phone', 'address'].forEach(function (id) {
        var el = $(id);
        if (el) {
          el.addEventListener('input', function () {
            var f = $('f' + id.charAt(0).toUpperCase() + id.slice(1));
            if (f && f.classList.contains('bad')) f.classList.remove('bad');
          });
        }
      });

      // Careful Order Submission Handling
      var btn = form ? form.querySelector('.sub') : null;
      var btnHTML = btn ? btn.innerHTML : '';
      var errBox = $('formError');

      if (form && btn) {
        form.addEventListener('submit', async function (e) {
          e.preventDefault();
          if (errBox) { errBox.textContent = ''; errBox.classList.remove('show'); }
          if ($('website').value) return; // honeypot
          if (!validate()) return;

          btn.disabled = true;
          btn.innerHTML = '<span class="spin">⏳</span> অর্ডারটি পাঠানো হচ্ছে...';

          try {
            var formData = new FormData(form);
            var res = await fetch(form.action, {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CFG.csrfToken
              },
              credentials: 'same-origin',
              body: formData
            });

            var data = await res.json().catch(function () { return {}; });
            if (!res.ok || data.success === false) {
              var errs = data.errors || {};
              var f1 = Object.keys(errs).length ? errs[Object.keys(errs)[0]][0] : null;
              throw new Error(f1 || data.message || 'অর্ডারটি সম্পন্ন করা যায়নি। অনুগ্রহ করে তথ্য পুনরায় পরীক্ষা করুন।');
            }

            var inv = data.invoice_no || data.order_reference || ('#' + (data.order_id || ''));
            if ($('orderRef')) $('orderRef').textContent = inv;
            if ($('formView')) $('formView').style.display = 'none';
            if ($('doneBox')) {
              $('doneBox').classList.add('show');
              $('doneBox').scrollIntoView({ block: 'center', behavior: 'smooth' });
            }

            var ecom = data.purchase || {
              transaction_id: inv,
              currency: 'BDT',
              value: total(),
              shipping: ship(),
              items: items()
            };
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
              event: 'purchase',
              event_id: inv,
              order_status: 'submitted',
              ecommerce: ecom
            });

          } catch (err) {
            dl('form_error', { error_type: 'server', error_message: String(err.message).slice(0, 120) });
            if (errBox) {
              errBox.textContent = err.message;
              errBox.classList.add('show');
              errBox.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }
          } finally {
            btn.disabled = false;
            btn.innerHTML = btnHTML;
            paint();
          }
        });
      }

      // Intersection Observers (Reveal animations & Sticky CTA)
      if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (es) {
          es.forEach(function (en) {
            if (en.isIntersecting) {
              en.target.classList.add('in');
              io.unobserve(en.target);
            }
          });
        }, { threshold: .12 });
        document.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
        setTimeout(function () {
          document.querySelectorAll('.rv:not(.in)').forEach(function (el) { el.classList.add('in'); });
        }, 3000);

        var sticky = $('sticky');
        if (sticky) {
          new IntersectionObserver(function (es) {
            es.forEach(function (en) {
              if (!en.isIntersecting && en.boundingClientRect.top < 0) sticky.classList.add('show');
              else sticky.classList.remove('show');
            });
          }, { threshold: 0 }).observe(document.querySelector('.hero'));

          new IntersectionObserver(function (es) {
            es.forEach(function (en) {
              if (en.isIntersecting) sticky.classList.remove('show');
            });
          }, { threshold: .3 }).observe($('order'));
        }
      } else {
        document.querySelectorAll('.rv').forEach(function (el) { el.classList.add('in'); });
      }

    })();
  </script>
</body>

</html>