@extends('FrontEnd.master')
@section('title')
    Flash Sale
@endsection
@section('content')
<!-- Flash Sale Header Section -->
<section class="flash-sale-header bg-white rounded shadow-sm mb-3">
    <div class="container">
        <div class="row align-items-center py-3">
            <div class="col-12 text-center">
                <h2 class="sale-title fw-bold mb-2" style="color: #D83F26;">{{session()->get('language') == 'bangla' ? 'ফ্ল্যাশ সেল':'Flash Sale'}}</h2>
                <p class="text-muted mb-0">{{count($campaign->campaing_products)}} {{session()->get('language') == 'bangla' ? 'টি পণ্য পাওয়া গেছে':'Products Found'}}</p>
            </div>
        </div>
    </div>
</section>

<!-- Countdown Timer Section -->
<section class="countdown-section bg-white rounded shadow-sm mb-4">
    <div class="container">
        <div class="row align-items-center py-3">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-center">
                    <span class="me-2 text-muted">{{session()->get('language') == 'bangla' ? 'শেষ হচ্ছে':'Ending In'}}</span>
                    <div class="countdown-timer" id="demo"></div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $flash_start = date_create($campaign->flash_start);
    $flash_end = date_create($campaign->flash_end);

    $start_diff = $flash_start->getTimestamp() - time();
    $end_diff = $flash_end->getTimestamp() - time();

    $start_diff2=date_diff(date_create($campaign->flash_start), date_create(date('d-m-Y H:i:s')));
    $end_diff2=date_diff(date_create(date('d-m-Y H:i:s')), date_create($campaign->flash_end));
@endphp
<input type="hidden" id="language_status" value="{{session()->get('language')}}">

<!-- Products Section -->
<section class="flash-sale-products">
    <div class="container">
        @if ($start_diff2->invert == 0 && $end_diff2->invert == 0)
            @if(count($campaign->campaing_products) > 0)
                <div class="latest-products-grid">
                    @foreach ($campaign->campaing_products as $product)
                        @php
                            if ($product->discount_type == 1) {
                                $discount_price = $product->product->regular_price - $product->discount_price;
                                $discount_label = '৳'.$product->discount_price;
                                $discount_percent = round(($product->discount_price / $product->product->regular_price) * 100) . '%';
                            } else {
                                $discount_price = $product->product->regular_price - $product->discount_price*$product->product->regular_price/100;
                                $discount_label = $product->discount_price.'%';
                                $discount_percent = $product->discount_price . '%';
                            }
                        @endphp
                        <div class="product-card">
                            @if($discount_price && $product->product->regular_price != $discount_price)
                                <div class="discount-badge">
                                    Save {{ $discount_percent }}
                                </div>
                            @endif
                            <div class="product-image">
                                <a href="{{ route('product.details', $product->product->slug) }}">
                                    <img src="{{ asset($product->product->product_thumbnail) }}" alt="Product Image">
                                </a>
                            </div>
                            <div class="product-details">
                                <a href="{{ route('product.details', $product->product->slug) }}" class="product-link">
                                    <h3 class="product-title">
                                        @if(session()->get('language') == 'bangla')
                                            {{ $product->product->name_bn }}
                                        @else
                                            {{ $product->product->name_en }}
                                        @endif
                                    </h3>
                                </a>
                                <div class="price-section">
                                    <h4 class="current-price">{{ $discount_price }} TK</h4>
                                    @if($product->product->regular_price != $discount_price)
                                        <span class="original-price">{{ $product->product->regular_price }} TK</span>
                                    @endif
                                </div>
                                <div class="rating-section">
                                    <div class="stars">
                                        @for($i = 0; $i < 4; $i++)
                                            <svg class="star filled" viewBox="0 0 24 24">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                            </svg>
                                        @endfor
                                        <svg class="star empty" viewBox="0 0 24 24">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                        </svg>
                                    </div>
                                    <span class="rating-text">(4.0)</span>
                                </div>
                                <div class="action-buttons">
                                    @if($product->product->stock_qty > 0)
                                        @if($product->product->is_varient == 1)
                                            <button onclick="productView({{ $product->product->id }})" data-bs-toggle="modal" data-bs-target="#quickViewModal"  class="btn-buy-now">
                                                {{ session()->get('language') == 'bangla' ? 'কিনুন' : 'Buy Now' }}
                                            </button>
                                            <button onclick="productView({{ $product->product->id }})" data-bs-toggle="modal" data-bs-target="#quickViewModal" class="btn-add-cart">
                                                {{ session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন' : 'Add to Cart' }}
                                            </button>
                                        @else
                                            <button onclick="buyNow({{ $product->product->id }})"  class="btn-buy-now">
                                                {{ session()->get('language') == 'bangla' ? 'কিনুন' : 'Buy Now' }}
                                            </button>
                                            <input type="hidden" id="pfrom" value="direct">
                                            <input type="hidden" id="product_product_id" value="{{ $product->product->id }}" min="1">
                                            <input type="hidden" id="{{ $product->product->id }}-product_pname" value="{{ $product->product->name_en }}">
                                            <button onclick="addToCartDirect({{ $product->product->id }})" class="btn-add-cart">
                                                {{ session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন' : 'Add to Cart' }}
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
                    @endforeach
                </div>
            @else
                <div class="no-products-found text-center py-5">
                    <div class="no-products-icon mb-3">
                        <i class="fas fa-shopping-basket fa-3x text-muted"></i>
                    </div>
                    <h5 class="mb-2">{{session()->get('language') == 'bangla' ? 'কোন পণ্য পাওয়া যায়নি': 'No Products Found'}}</h5>
                    <p class="text-muted">{{session()->get('language') == 'bangla' ? 'দুঃখিত, এই মুহূর্তে কোন ফ্ল্যাশ সেল পণ্য নেই': 'Sorry, there are no flash sale products at the moment'}}</p>
                </div>
            @endif
        @else
            <div class="sale-not-active text-center py-5">
                <div class="alert alert-warning d-inline-block">
                    <i class="fas fa-clock me-2"></i>
                    {{session()->get('language') == 'bangla' ? 'ফ্ল্যাশ সেল এখনও শুরু হয়নি বা ইতিমধ্যে শেষ হয়ে গেছে।': 'The Flash Sale has not started yet or has already ended.'}}
                </div>
            </div>
        @endif
    </div>
</section>

<style>
    /* Flash Sale Styles */
    .sale-title {
        font-size: 1.75rem;
        color: #333;
    }
    @media (max-width: 576px) {
        .sale-title {
            font-size: 1.5rem;
        }
    }
    /* Countdown Timer Styles */
    .countdown-timer {
        display: flex;
        align-items: center;
    }
    .countdown-box {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px 8px;
        margin: 0 2px;
        min-width: 40px;
        text-align: center;
    }
    .countdown-separator {
        font-weight: bold;
        margin: 0 2px;
    }

    /* --- Begin: Responsive grid and card styles from index2.blade.php --- */
    .latest-products-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 18px;
        margin-top: 20px;
    }
    @media (max-width: 1400px) {
        .latest-products-grid {
            grid-template-columns: repeat(6, 1fr);
        }
    }
    @media (max-width: 1200px) {
        .latest-products-grid {
            grid-template-columns: repeat(6, 1fr);
        }
    }
    @media (max-width: 992px) {
        .latest-products-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    @media (max-width: 768px) {
        .latest-products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 576px) {
        .latest-products-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 8px !important;
        }
    }
    .product-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    .discount-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 3px 6px;
        border-radius: 8px;
        font-size: 9px;
        font-weight: 700;
        z-index: 3;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 8px rgba(238, 90, 36, 0.3);
    }
    .product-image {
        position: relative;
        height: 140px !important;
        overflow: hidden;
        object-fit: cover;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
    .product-image a {
        display: block;
        height: 100%;
        position: relative;
    }
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .product-image:hover img {
        transform: scale(1.05);
    }
    .product-details {
        padding: 10px;
        position: relative;
    }
    .product-link {
        text-decoration: none;
        color: inherit;
    }
    .product-title {
        font-size: 13px;
        font-weight: 500 !important;
        color: #2c3e50;
        margin-bottom: 8px;
        line-height: 1.3;
        transition: color 0.3s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 33px;
    }
    .product-link:hover .product-title {
        color: #313131;
    }
    .price-section {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
        height: 30px;
    }
    .current-price {
        font-size: 15px;
        font-weight: 600;
        color: #D83F26;
        margin: 0;
    }
    .original-price {
        font-size: 11px;
        color: #95a5a6;
        text-decoration: line-through;
        font-weight: 400;
    }
    .rating-section {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 5px;
        line-height: 1.2;
    }
    .stars {
        display: flex;
        gap: 1px;
    }
    .star {
        width: 10px;
        height: 10px;
    }
    .star.filled {
        fill: #ffc107;
    }
    .star.empty {
        fill: #e0e0e0;
    }
    .rating-text {
        font-size: 10px;
        color: #7f8c8d;
        font-weight: 500;
    }
    .action-buttons {
        display: flex;
        justify-content: space-between;
        gap: 5px;
        margin-bottom: 0px;
    }
    .btn-buy-now, .btn-add-cart {
        border: none;
        padding: 0px 3px;
        border-radius: 5px;
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 1;
        text-align: center;
    }

    .btn-buy-now {
        background: #D83F26;
        color: white;
        box-shadow: 0 2px 8px rgba(216, 63, 38, 0.3);
    }

    .btn-buy-now:hover {
        background: #c2362a;
        transform: translateY(-1px);
    }

    .btn-add-cart {
        background: white;
        color: #D83F26;
        border: 1px solid #D83F26;
    }

    .btn-add-cart:hover {
        background: #D83F26;
        color: white;
        transform: translateY(-1px);
    }

    @media (max-width: 575.98px) {
         .btn-add-cart {
            font-size: 12px;
        }
        .btn-buy-now 
        {
            display: none;
        }
    }
    @media (min-width: 576px) and (max-width: 767.98px) {
        .btn-buy-now, .btn-add-cart {
            font-size: 12px;
        }
    }
    @media (min-width: 768px) and (max-width: 991.98px) {
        .btn-buy-now, .btn-add-cart {
            font-size: 12px;
        }
    }
    @media (min-width: 992px) and (max-width: 1199.98px) {
        .btn-buy-now, .btn-add-cart {
            font-size: 12px;
        }
    }
    @media (min-width: 1200px) {
        .btn-buy-now, .btn-add-cart {
            font-size: 12px;
        }
    }
    .out-of-stock {
        width: 100%;
        background: linear-gradient(45deg, #f2f2f2, #e6e6e6);
        color: #999;
        text-align: center;
        border-radius: 8px;
        padding: 8px 0;
        font-weight: 600;
        letter-spacing: 0.5px;
        font-size: 11px;
    }
    /* Responsive for product card */
    @media (max-width: 575.98px) {
        .product-card {
            max-width: 200px;
            margin: 0 5px;
        }
        .product-details {
            padding: 8px;
        }
        .product-title {
            font-size: 12px;
            height: 30px;
        }
        .current-price {
            font-size: 13px;
        }
        .original-price {
            font-size: 10px;
        }
        .btn-buy-now, .btn-add-cart {
            font-size: 12px;
        }
        .discount-badge {
            font-size: 8px;
            padding: 2px 5px;
        }
    }
    @media (min-width: 576px) and (max-width: 767.98px) {
        .product-card {
            max-width: 220px;
        }
    }
    @media (min-width: 768px) and (max-width: 991.98px) {
        .product-card {
            max-width: 240px;
        }
        .product-image {
            height: 170px;
        }
    }
    @media (min-width: 992px) and (max-width: 1199.98px) {
        .product-card {
            max-width: 260px;
        }
    }
    @media (min-width: 1200px) {
        .product-card {
            max-width: 280px;
        }
    }
    /* --- End: Responsive grid and card styles from index2.blade.php --- */
</style>
@endsection

@push('js')
<script>
    function miniCartRemove(rowId) {
        $.ajax({
            type: 'GET',
            url: '/minicart/product-remove/' + rowId,
            dataType: 'json',
            success: function (data) {
                miniCart();
                cart();

                // Start Message
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000
                })
                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                        type: 'success',
                        title: data.success
                    })
                } else {
                    Toast.fire({
                        type: 'error',
                        title: data.error
                    })
                }
                // End Message
            }
        });
    }
</script>
<script>
    // Convert PHP date differences to JavaScript format
    var startDiff = <?php echo $start_diff * 1000; ?>;
    var endDiff = <?php echo $end_diff * 1000; ?>;

    // Set the date we're counting down to based on PHP date differences
    var countDownDateStart = new Date(Date.now() + startDiff);
    var countDownDateEnd = new Date(Date.now() + endDiff);

    // Update the count down every 1 second
    var x = setInterval(function() {
        // Get today's date and time
        var now = new Date().getTime();

        // Choose between start and end dates based on your requirement
        var countDownDate = (now < countDownDateStart.getTime()) ? countDownDateStart : countDownDateEnd;

        // Calculate the remaining time
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Format numbers to always have two digits
        days = days < 10 ? "0" + days : days;
        hours = hours < 10 ? "0" + hours : hours;
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        // Output the result in an element with id="demo"
        if($('#language_status').val() == 'bangla') {
            var html = `
                <div class="countdown-box">
                    <span>${days}</span>
                    <small>দিন</small>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-box">
                    <span>${hours}</span>
                    <small>ঘন্টা</small>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-box">
                    <span>${minutes}</span>
                    <small>মিনিট</small>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-box">
                    <span>${seconds}</span>
                    <small>সেকেন্ড</small>
                </div>
            `;
        } else {
            var html = `
                <div class="countdown-box">
                    <span>${days}</span>
                    <small>D</small>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-box">
                    <span>${hours}</span>
                    <small>H</small>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-box">
                    <span>${minutes}</span>
                    <small>M</small>
                </div>
                <span class="countdown-separator">:</span>
                <div class="countdown-box">
                    <span>${seconds}</span>
                    <small>S</small>
                </div>
            `;
        }

        document.getElementById("demo").innerHTML = html;

        // If the count down is over, write some text
        if (distance < 0) {
            clearInterval(x);
            location.reload();
        }
    }, 1000);
</script>
@endpush