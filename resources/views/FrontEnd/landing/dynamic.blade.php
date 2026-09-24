<!doctype html>
<html lang="bn">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Roh Cloths - Premium Saree</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box
    }

    html {
      scroll-behavior: smooth
    }

    body {
      margin: 0;
      font-family: "Hind Siliguri", Arial, "Noto Sans Bengali", sans-serif;
      background: #fff;
      color: #222
    }

    img {
      display: block;
      width: 100%
    }

    a {
      text-decoration: none;
      color: inherit
    }

    .wrap {
      width: min(1120px, 94%);
      margin: auto
    }

    :root {
      --main: #275b57;
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

    header {
      border-bottom: 1px solid var(--line);
      background: #fff
    }

    .nav {
      height: 62px;
      display: flex;
      align-items: center;
      justify-content: space-between
    }

    .logo {
      font-size: 22px;
      font-weight: 800;
      letter-spacing: 1px;
      color: var(--main)
    }

    .nav a {
      background: var(--main);
      color: #fff;
      padding: 10px 16px;
      font-weight: 700
    }

    .hero {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 38px;
      padding: 34px 0 46px;
      align-items: start
    }

    .hero-slider {
      position: relative;
      overflow: hidden;
      border: 1px solid var(--line);
      height: 560px
    }

    .hero-slides {
      display: flex;
      height: 100%;
      transition: transform .45s ease
    }

    .hero-slides img {
      min-width: 100%;
      height: 100%;
      object-fit: cover
    }

    .slide-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 38px;
      height: 38px;
      border: 0;
      background: rgba(255, 255, 255, .88);
      font-size: 26px;
      cursor: pointer
    }

    .slide-btn.prev {
      left: 10px
    }

    .slide-btn.next {
      right: 10px
    }

    .dots {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 12px;
      display: flex;
      justify-content: center;
      gap: 7px
    }

    .dots button {
      width: 8px;
      height: 8px;
      border: 0;
      border-radius: 50%;
      background: #d9d9d9;
      padding: 0
    }

    .dots button.active {
      background: var(--main)
    }

    .tag {
      font-size: 12px;
      letter-spacing: 2px;
      color: var(--main);
      margin: 4px 0 10px
    }

    .details h1 {
      font-size: 44px;
      line-height: 1.16;
      margin: 0 0 14px;
      color: #1f3533
    }

    .lead {
      color: var(--muted);
      margin: 0 0 20px
    }

    .pricebox {
      border: 1px solid #dde3e2;
      padding: 18px;
      margin: 0 0 16px
    }

    .price {
      font-size: 34px;
      color: var(--main);
      font-weight: 800
    }

    .old {
      margin-left: 10px;
      color: #999;
      text-decoration: line-through
    }

    .discount {
      display: inline-block;
      margin-left: 10px;
      background: #e9f1ef;
      color: var(--main);
      padding: 4px 8px;
      font-size: 12px
    }

    .stock {
      font-size: 13px;
      color: #666;
      margin-top: 12px
    }

    .bar {
      height: 5px;
      background: #eee;
      margin-top: 7px
    }

    .bar i {
      display: block;
      width: 56%;
      height: 100%;
      background: var(--main)
    }

    .size-label {
      display: block;
      font-weight: 700;
      margin: 14px 0 7px
    }

    .size-select {
      width: 100%;
      padding: 12px;
      border: 1px solid #d6dada;
      background: #fff;
      border-radius: 0
    }

    .orderbtn {
      display: block;
      text-align: center;
      background: var(--main);
      color: #fff;
      padding: 14px;
      font-weight: 800;
      margin: 16px 0 12px
    }

    .notes {
      display: flex;
      gap: 8px;
      flex-wrap: wrap
    }

    .notes span {
      border: 1px solid #ddd;
      padding: 8px 10px;
      font-size: 13px
    }

    .section {
      padding: 52px 0;
      border-top: 1px solid var(--line)
    }

    .section h2 {
      text-align: center;
      color: #1f3533;
      margin: 0 0 12px;
      font-size: 30px
    }

    .section-sub {
      text-align: center;
      color: var(--muted);
      margin: 0 auto 28px;
      max-width: 720px
    }

    .overview {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 28px;
      align-items: center
    }

    .overview img {
      height: 470px;
      object-fit: cover
    }

    .overview-text h3 {
      font-size: 28px;
      color: #1f3533;
      margin: 0 0 12px
    }

    .overview-text p {
      color: var(--muted);
      margin: 0 0 13px
    }

    .overview-list {
      margin: 15px 0 0;
      padding-left: 19px;
      color: #434a49
    }

    .overview-list li {
      margin: 8px 0
    }

    .about {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px
    }

    .about div {
      border: 1px solid var(--line);
      padding: 20px;
      text-align: center
    }

    .about h3 {
      margin: 0 0 6px;
      color: var(--main)
    }

    .about p {
      margin: 0;
      color: var(--muted);
      font-size: 14px
    }

    .review-slider {
      position: relative;
      overflow: hidden
    }

    .review-track {
      display: flex;
      transition: transform .45s ease
    }

    .review {
      min-width: 33.333%;
      border: 1px solid var(--line);
      padding: 20px;
      background: #fff
    }

    .stars {
      color: #b99b5b;
      letter-spacing: 2px;
      margin-bottom: 10px
    }

    .review p {
      color: #5f6765;
      margin: 0 0 14px
    }

    .review strong {
      color: #1f3533
    }

    .review small {
      display: block;
      color: #8a8f8e;
      margin-top: 2px
    }

    .review-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 36px;
      height: 36px;
      border: 1px solid var(--line);
      background: #fff;
      font-size: 24px;
      cursor: pointer
    }

    .review-prev {
      left: 8px
    }

    .review-next {
      right: 8px
    }

    .order-area {
      background: var(--bg-warm, #fbf7f4);
      padding: 56px 0;
      position: relative;
    }

    .order-grid {
      display: grid;
      grid-template-columns: 1.18fr 0.92fr;
      gap: 32px;
      align-items: start;
    }

    /* Left Card: Customer input fields */
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
      font-size: 15px;
      font-weight: 700;
      color: var(--maroon, #521626);
      margin: 0 0 8px 0;
      letter-spacing: 0.2px;
    }

    .form-group input[type="text"],
    .form-group input[type="tel"],
    .form-group select,
    .form-group textarea {
      width: 100%;
      background: #ffffff;
      border: 1.5px solid var(--border-input, #ecdcd3);
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 15px;
      color: #2b2321;
      font-family: inherit;
      outline: none;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      box-sizing: border-box;
    }

    .form-group textarea {
      height: 92px;
      resize: vertical;
      line-height: 1.5;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
      color: #a49994;
      font-weight: 400;
      opacity: 0.9;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--maroon, #521626);
      box-shadow: 0 0 0 3px rgba(82, 22, 38, 0.08);
      background: #ffffff;
    }

    .select-wrapper {
      position: relative;
    }

    .select-wrapper::after {
      content: "";
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      width: 10px;
      height: 6px;
      background-color: var(--maroon, #521626);
      clip-path: polygon(100% 0%, 0 0%, 50% 100%);
      pointer-events: none;
    }

    .form-group select {
      appearance: none;
      -webkit-appearance: none;
      padding-right: 36px;
      cursor: pointer;
    }

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
      border: 1.5px solid var(--border-input, #ecdcd3);
      border-radius: 8px;
      background: #ffffff;
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
      top: 24px;
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

    .order-submit-btn:active {
      transform: translateY(0);
    }

    .order-terms-note {
      margin: 12px 0 0;
      font-size: 12.5px;
      color: #706662;
      text-align: center;
      line-height: 1.4;
    }

    .faq {
      max-width: 850px;
      margin: auto;
      border-top: 1px solid var(--line)
    }

    details {
      border-bottom: 1px solid var(--line);
      padding: 0
    }

    summary {
      cursor: pointer;
      padding: 17px 4px;
      font-weight: 700;
      color: #243b39;
      list-style: none
    }

    summary::-webkit-details-marker {
      display: none
    }

    summary:after {
      content: "+";
      float: right;
      color: var(--main);
      font-size: 20px
    }

    details[open] summary:after {
      content: "−"
    }

    .faq p {
      margin: 0;
      padding: 0 4px 17px;
      color: var(--muted)
    }

    footer {
      text-align: center;
      border-top: 1px solid var(--line);
      padding: 24px;
      color: #777
    }

    /* Bottom Navigation Bar */
    body {
      padding-bottom: 76px;
    }

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

    .bottom-nav-right {
      display: flex;
      align-items: center;
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
      background: #1e4744;
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(39, 91, 87, 0.36);
      color: #fff;
    }

    .bottom-nav-btn:active {
      transform: translateY(0);
    }

    .bottom-nav-btn svg {
      width: 18px;
      height: 18px;
      display: inline-block;
      vertical-align: middle;
      flex-shrink: 0;
    }

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
        height: 500px
      }

      .details h1 {
        font-size: 36px
      }

      .about {
        grid-template-columns: 1fr
      }

      .review {
        min-width: 100%
      }

      .delivery {
        grid-template-columns: 1fr
      }

      .overview img {
        height: 390px
      }
    }

    @media(max-width:520px) {
      .hero-slider {
        height: 430px
      }

      .hero {
        padding-top: 20px
      }

      .details h1 {
        font-size: 31px
      }

      .price {
        font-size: 29px
      }

      .nav {
        height: 56px
      }

      body {
        padding-bottom: 72px;
      }

      .bottom-nav {
        padding: 8px 0;
      }

      .bottom-nav-price {
        font-size: 19px;
      }

      .bottom-nav-btn {
        padding: 10px 18px;
        font-size: 15px;
        gap: 6px;
      }

      .bottom-nav-btn svg {
        width: 16px;
        height: 16px;
      }
    }
  </style>
</head>

<body>

  <header>
    <div class="wrap nav">
      <div class="logo">ROH CLOTHS</div>
      <a href="#order">অর্ডার করুন</a>
    </div>
  </header>

  <main class="wrap hero">
    <div class="hero-slider">
      <div class="hero-slides" id="heroSlides">
        <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=85"
          alt="Premium saree">
        <img src="https://images.unsplash.com/photo-1583391733956-6c78276477e2?auto=format&fit=crop&w=1000&q=85"
          alt="Premium saree">
        <img src="https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?auto=format&fit=crop&w=1000&q=85"
          alt="Premium saree">
        <img src="https://images.unsplash.com/photo-1612336307429-8a898d10e223?auto=format&fit=crop&w=1000&q=85"
          alt="Premium saree">
      </div>
      <button class="slide-btn prev" type="button" onclick="moveHero(-1)">‹</button>
      <button class="slide-btn next" type="button" onclick="moveHero(1)">›</button>
      <div class="dots" id="heroDots">
        <button class="active" onclick="goHero(0)"></button>
        <button onclick="goHero(1)"></button>
        <button onclick="goHero(2)"></button>
        <button onclick="goHero(3)"></button>
      </div>
    </div>

    <div class="details">
      <div class="tag">ROH CLOTHS · PREMIUM COLLECTION</div>
      <h1>Premium Designer Saree</h1>
      <p class="lead">সিম্পল, এলিগ্যান্ট এবং প্রিমিয়াম লুক। পার্টি, ওয়েডিং ও বিশেষ দিনের জন্য বাছাই করা ডিজাইন।</p>

      <div class="pricebox">
        <span class="price">৳ ৩,০৫০</span>
        <span class="old">৳ ৩,৮৫০</span>
        <span class="discount">২১% ছাড়</span>
        <div class="stock">বর্তমান স্টক: সীমিত</div>
        <div class="bar"><i></i></div>
      </div>

      <label class="size-label" for="heroSize">সাইজ নির্বাচন করুন</label>
      <select class="size-select" id="heroSize">
        <option value="">— সাইজ নির্বাচন করুন —</option>
        <option>S</option>
        <option>M</option>
        <option>L</option>
        <option>XL</option>
        <option>XXL</option>
        <option>Free Size</option>
      </select>

      <a class="orderbtn" href="#order">অর্ডার করুন — ৳ ৩,০৫০</a>

      <div class="notes">
        <span>ক্যাশ অন ডেলিভারি</span>
        <span>সারাদেশে ডেলিভারি</span>
        <span>এক্সচেঞ্জ সুবিধা</span>
      </div>
    </div>
  </main>

  <section class="section">
    <div class="wrap">
      <h2>Product Full Overview</h2>
      <p class="section-sub">প্রোডাক্ট সম্পর্কে দরকারি তথ্য এক জায়গায়।</p>

      <div class="overview">
        <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=1000&q=85"
          alt="Product overview">
        <div class="overview-text">
          <h3>Premium Designer Saree</h3>
          <p>এই শাড়িটি এমনভাবে বাছাই করা হয়েছে যাতে দেখতে প্রিমিয়াম লাগে, পরতে আরামদায়ক হয় এবং পার্টি বা বিশেষ অনুষ্ঠানে
            সহজেই স্টাইল করা যায়।</p>
          <ul class="overview-list">
            <li>Soft & comfortable premium fabric</li>
            <li>Elegant party and wedding design</li>
            <li>Neat finishing and premium appearance</li>
            <li>Available in multiple sizes</li>
            <li>Suitable for gift and special occasions</li>
            <li>Cash on Delivery available</li>
          </ul>
        </div>
      </div>
    </div>
  </section>


  <section class="section">
    <div class="wrap">
      <h2>Customer Reviews</h2>
      <p class="section-sub">কাস্টমারদের কিছু মতামত।</p>

      <div class="review-slider">
        <div class="review-track" id="reviewTrack">
          <div class="review">
            <div class="stars">★★★★★</div>
            <p>“কাপড়ের কোয়ালিটি খুব ভালো। ছবির মতোই সুন্দর পেয়েছি।”</p>
            <strong>সাদিয়া রহমান</strong>
            <small>ঢাকা</small>
          </div>
          <div class="review">
            <div class="stars">★★★★★</div>
            <p>“ডেলিভারি দ্রুত ছিল এবং প্যাকেজিংও ভালো ছিল।”</p>
            <strong>তানজিনা আক্তার</strong>
            <small>চট্টগ্রাম</small>
          </div>
          <div class="review">
            <div class="stars">★★★★★</div>
            <p>“পার্টিতে পরার জন্য নিয়েছিলাম, ডিজাইনটা খুব এলিগ্যান্ট।”</p>
            <strong>নুসরাত জাহান</strong>
            <small>কুমিল্লা</small>
          </div>
          <div class="review">
            <div class="stars">★★★★★</div>
            <p>“ফ্যাব্রিক আর ফিনিশিং দুইটাই খুব সুন্দর। আবারও অর্ডার করবো।”</p>
            <strong>মারিয়া ইসলাম</strong>
            <small>নারায়ণগঞ্জ</small>
          </div>
        </div>
        <button class="review-btn review-prev" type="button" onclick="moveReview(-1)">‹</button>
        <button class="review-btn review-next" type="button" onclick="moveReview(1)">›</button>
      </div>
    </div>
  </section>

  <section class="section order-area" id="order">
    <div class="wrap">
      <form class="order-grid" onsubmit="event.preventDefault();alert('অর্ডার সফলভাবে জমা দেওয়া হয়েছে!');">
        
        <!-- Left Side: Order Fields (Serialized as original) -->
        <div class="order-fields-card">
          <div class="form-group">
            <label for="orderSize">সাইজ নির্বাচন করুন *</label>
            <div class="select-wrapper">
              <select id="orderSize" name="size" required>
                <option value="">— সাইজ নির্বাচন করুন —</option>
                <option>S</option>
                <option>M</option>
                <option>L</option>
                <option>XL</option>
                <option>XXL</option>
                <option>Free Size</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="customerName">আপনার নাম *</label>
            <input type="text" id="customerName" name="name" required placeholder="পুরো নাম লিখুন">
          </div>

          <div class="form-group">
            <label for="customerPhone">মোবাইল নম্বর *</label>
            <input type="tel" id="customerPhone" name="phone" required placeholder="01XXXXXXXXX" pattern="[0-9]{11}">
          </div>

          <div class="form-group">
            <label for="customerAddress">সম্পূর্ণ ঠিকানা *</label>
            <textarea id="customerAddress" name="address" required placeholder="বাসা/হোল্ডিং নং, রোড, থানা, জেলা"></textarea>
          </div>

          <div class="form-group">
            <label>ডেলিভারি এলাকা *</label>
            <div class="delivery-options">
              <label class="delivery-option">
                <input type="radio" name="area" value="70" checked>
                <span class="delivery-option-text">ঢাকার ভিতরে — ৳৭০</span>
              </label>
              <label class="delivery-option">
                <input type="radio" name="area" value="120">
                <span class="delivery-option-text">ঢাকার বাইরে — ৳১২০</span>
              </label>
            </div>
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
              <span class="summary-item-val">Premium Designer Saree × ১</span>
            </div>
            <div class="summary-item">
              <span class="summary-item-label">পণ্যের মূল্য</span>
              <span class="summary-item-val">৳ ৩,০৫০</span>
            </div>
            <div class="summary-item">
              <span class="summary-item-label">ডেলিভারি চার্জ</span>
              <span class="summary-item-val" id="delivery">৳ ৭০</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-total-row">
              <span class="summary-total-label">সর্বমোট (COD)</span>
              <span class="summary-total-val" id="total">৳ ৩,১২০</span>
            </div>

            <button type="submit" class="order-submit-btn">অর্ডার কনফার্ম করুন</button>
            <p class="order-terms-note">অর্ডার করে আমাদের শর্তাবলীতে সম্মত হচ্ছেন।</p>
          </div>
        </div>

      </form>
    </div>
  </section>

  <section class="section">
    <div class="wrap">
      <h2>Frequently Asked Questions</h2>
      <p class="section-sub">অর্ডারের আগে যেসব প্রশ্ন সাধারণত কাস্টমাররা জানতে চান।</p>

      <div class="faq">
        <details>
          <summary>ডেলিভারি করতে কতদিন সময় লাগে?</summary>
          <p>ঢাকার ভিতরে সাধারণত ১–২ কর্মদিবস এবং ঢাকার বাইরে ২–৪ কর্মদিবস সময় লাগতে পারে।</p>
        </details>
        <details>
          <summary>ক্যাশ অন ডেলিভারি আছে?</summary>
          <p>হ্যাঁ, সারা বাংলাদেশে ক্যাশ অন ডেলিভারি সুবিধা রয়েছে।</p>
        </details>
        <details>
          <summary>সাইজ কীভাবে নির্বাচন করবো?</summary>
          <p>অর্ডারের সময় S, M, L, XL, XXL অথবা Free Size থেকে প্রয়োজনীয় সাইজ নির্বাচন করতে পারবেন।</p>
        </details>
        <details>
          <summary>প্রোডাক্ট এক্সচেঞ্জ করা যাবে?</summary>
          <p>প্রোডাক্টে সমস্যা বা সাইজ সংক্রান্ত ক্ষেত্রে আমাদের এক্সচেঞ্জ পলিসি অনুযায়ী এক্সচেঞ্জ করা যাবে।</p>
        </details>
        <details>
          <summary>ছবির মতো একই প্রোডাক্ট পাবো?</summary>
          <p>আমরা প্রোডাক্টের আসল ডিজাইন ও কোয়ালিটি যতটা সম্ভব সঠিকভাবে দেখানোর চেষ্টা করি। স্ক্রিনের কারণে রঙে সামান্য
            পার্থক্য হতে পারে।</p>
        </details>
      </div>
    </div>
  </section>

  <footer>© 2026 ROH CLOTHS</footer>

  <!-- Sticky Bottom Navigation -->
  <div class="bottom-nav" id="bottomNav">
    <div class="wrap bottom-nav-wrap">
      <div class="bottom-nav-left">
        <span class="bottom-nav-label">সর্বমোট মূল্য</span>
        <div class="bottom-nav-price-group">
          <span class="bottom-nav-price" id="bottomTotal">৳ ৩,১২০</span>
          <span class="bottom-nav-old">৳ ৩,৮৫০</span>
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

  <script>
    let heroIndex = 0, reviewIndex = 0;
    const heroSlides = document.getElementById('heroSlides'), heroDots = [...document.querySelectorAll('#heroDots button')];
    function showHero() {
      heroSlides.style.transform = `translateX(-${heroIndex * 100}%)`;
      heroDots.forEach((d, i) => d.classList.toggle('active', i === heroIndex));
    }
    function moveHero(n) { heroIndex = (heroIndex + n + 4) % 4; showHero() }
    function goHero(i) { heroIndex = i; showHero() }
    setInterval(() => moveHero(1), 4000);

    function visibleReviews() { return innerWidth <= 800 ? 1 : 3 }
    function maxReview() { return Math.max(0, 4 - visibleReviews()) }
    function showReview() {
      const step = 100 / visibleReviews();
      reviewIndex = Math.min(reviewIndex, maxReview());
      document.getElementById('reviewTrack').style.transform = `translateX(-${reviewIndex * step}%)`;
    }
    function moveReview(n) {
      reviewIndex += n;
      if (reviewIndex > maxReview()) reviewIndex = 0;
      if (reviewIndex < 0) reviewIndex = maxReview();
      showReview();
    }
    setInterval(() => moveReview(1), 4500);
    addEventListener('resize', showReview);

    heroSize.onchange = () => orderSize.value = heroSize.value;

    const bnMap = { '0': '০', '1': '১', '2': '২', '3': '৩', '4': '৪', '5': '৫', '6': '৬', '7': '৭', '8': '৮', '9': '৯' };
    function toBn(num) {
      return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ',').replace(/[0-9]/g, d => bnMap[d] || d);
    }

    const bottomTotalEl = document.getElementById('bottomTotal');
    document.querySelectorAll('[name=area]').forEach(x => x.onchange = () => {
      let v = +x.value;
      delivery.innerText = '৳ ' + toBn(v);
      const formattedTotal = '৳ ' + toBn(3050 + v);
      total.innerText = formattedTotal;
      if (bottomTotalEl) {
        bottomTotalEl.innerText = formattedTotal;
      }
    });
  </script>

</body>

</html>