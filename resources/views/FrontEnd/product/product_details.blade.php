@extends('FrontEnd.master')
@section('title')
{{$product->name_en}} Details
@endsection
@push('css')
<style>
    .app-figure {
        width: 100% !important;
        margin: 0px auto;
        border: 0px solid red;
        padding: 20px;
        position: relative;
        text-align: center;
    }

    .MagicZoom {
        display: none;
    }

    .MagicZoom.Active {
        display: block;
    }

    .selectors {
        margin-top: 10px;
    }

    .selectors .mz-thumb img {
        max-width: 56px;
    }

    .detail-qty {
        background: #f8f9fa;
        border-radius: 5px;
        height: 40px;
        min-width: 120px;
        border: 1px solid #dee2e6;
    }

    .detail-qty .btn {
        padding: 0;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        color: #6c757d;
        transition: all 0.2s;
    }

    .detail-qty .btn:hover {
        color: #000;
        background: #e9ecef;
        border-radius: 4px;
    }

    .detail-qty .btn:disabled {
        color: #adb5bd;
        cursor: not-allowed;
    }

    .detail-qty input {
        background: transparent;
        height: 100%;
        font-size: 16px;
        font-weight: 500;
    }

    .detail-qty input:focus {
        outline: none;
    }

    #qty_stock_alert {
        font-size: 0.875rem;
    }

    .quantity-label {
        font-weight: 500;
        color: #212529;
    }

    /* Responsive Related Products */
    @media (max-width: 991.98px) {
        .just-for-you {
            border-left: none !important;
            border-top: 1px solid #eee !important;
            margin-top: 2rem !important;
        }
    }

    @media (max-width: 767.98px) {
        .just-for-you {
            margin-top: 1rem !important;
        }

        .product_card {
            min-width: 100% !important;
            max-width: 100% !important;
        }
    }

    @media (max-width: 575.98px) {
        .just-for-you .row {
            row-gap: 1rem !important;
        }

        .product_card {
            min-width: 100% !important;
            max-width: 100% !important;
        }
    }

    @media screen and (max-width: 1023px) {
        .app-figure {
            width: 99% !important;
            margin: 20px auto;
            padding: 0;
        }
    }
</style>
<link rel="stylesheet" href="{{asset('frontend/magiczoomplus/magiczoomplus.css')}}" />
@endpush
@section('content')
{{-- @php dd($product->product_type) @endphp--}}
<!-- Product Information Start -->
<section class="custom_container  my-3  " style="border-radius: 10px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom  mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" style="color: #D83F26; text-decoration: none; transition: all 0.3s;">
                    @if(session()->get('language') == 'bangla')
                    হোম
                    @else
                    Home
                    @endif
                </a>
            </li>
            <li class="breadcrumb-item active" style=" font-weight: 600;">
                @if(session()->get('language') == 'bangla')
                পণ্যের বিবরণ
                @else
                Product Details
                @endif
            </li>
        </ol>
    </nav>
    <div class="row g-5 m-0">
        <?php $discount = calculateDiscount($product->id) ?>
        <input type="hidden" id="product_id" value="{{ $product->id }}" min="1">

        <input type="hidden" id="pname" value="{{ $product->name_en }}">

        <input type="hidden" id="product_price" value="{{ $discount['discount'] }}">

        <input type="hidden" id="minimum_buy_qty" value="{{ $product->minimum_buy_qty }}">
        <input type="hidden" id="stock_qty" value="{{ $product->stock_qty }}">

        <input type="hidden" id="pvarient" value="">

        <input type="hidden" id="buyNowCheck" value="0">
        <input type="hidden" name="" id="discount_amount" value="{{$product->regular_price - $discount['discount']}}">
        <div class="col-md-9 bg-white p-3 " style="border-radius: 10px;">
            <div class="row">

                <div class="col-md-4 ">
                    <!-- default start -->
                    <section id="default" class="padding-top0">
                        <div class="">
                            <div class="">
                                <div class="xzoom-container">
                                    <img class="xzoom w-100" style="border-radius: 8px" id="xzoom-default" src="{{asset($product->product_thumbnail)}}"
                                        xoriginal="{{asset($product->product_thumbnail)}}" width="300px" height="300px" />
                                    <div class="xzoom-thumbs mt-4 m-auto">
                                        <a href="{{asset($product->product_thumbnail)}}"><img class="xzoom-gallery"
                                                width="40" src="{{asset($product->product_thumbnail)}}"
                                                xpreview="{{asset($product->product_thumbnail)}}"></a>
                                        @foreach($multiImg as $image)
                                        <a href="{{asset($image->photo_name)}}"><img class="xzoom-gallery"
                                                width="40" src="{{asset($image->photo_name)}}"></a>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- default end -->
                </div>

                <div class="col-md-8" style="position: relative;">
                    <h1 class="product-title" style="color:#4a4a4a !important">
                        @if(session()->get('language') == 'bangla')
                        {{$product->name_bn}}
                        @else
                        {{$product->name_en}}
                        @endif
                    </h1>
                    <div class="{{$discount['discount'] == $product->regular_price ? 'd-none':''}}" style="background-color: #D83F26 ; color: #f8f9fa; border-radius: 5px; width: 70px; position: absolute; top: 0; left: -330px; padding: 5px 10px; font-size: 12px; text-align: center; z-index: 50;">
                        <small class="m-1 text-center">{{$discount['text']}}</small>
                    </div>
                    {{-- <span class="stock-status out-stock"> ৳{{  $discount }} Off </span>--}}

                    <div>
                        <div style="background-color: rgba(247,147,41,0.1); border-radius: 30px; padding: 5px 15px; margin: 10px 0; color: #ff00c3; width: 50%;">
                            <small style="color: #f9A11E;">
                                <i class="fa fa-star" style="margin-right: 5px"></i>
                                {{$product->points}}
                                @if(session()->get('language') == 'bangla')
                                স্টার পয়েন্ট
                                @else
                                Start Points
                                @endif

                            </small>
                        </div>
                        <h4 class="price">
                            @if(session()->get('language') == 'bangla')
                            মূল্য:
                            @else
                            Price:
                            @endif
                            <span class="product_price current-price">{{$discount['discount']}}TK</span>
                            @if($discount['discount'] != $product->regular_price)
                            <del class="old-price {{$discount['discount'] == 0 ? 'd-none':''}}" style="color: grey"> {{$product->regular_price}} TK</del>
                            @endif


                        </h4>
                        <p class="">
                            <small>
                                @if(session()->get('language') == 'bangla')
                                ক্যাটাগোরি: {{$product->category->name_bn??''}}
                                @else
                                Product Category: {{$product->category->name_en??''}}
                                @endif

                            </small><br>
                            @if($product->product_type == 2 && count($group_products) >0)
                            <strong>
                                @if(session()->get('language') == 'bangla')
                                প্যাকেজের পণ্য সমূহ
                                @else
                                Package Items
                                @endif
                                :</strong>
                            @foreach($group_products as $item)
                        <div class="row mb-1">
                            <div class="col-md-1">
                                <a href="{{route('product.details', $item->product->slug)}}">
                                    <img src="{{asset($item->product->product_thumbnail)}}" alt="" height="30px" width="30px">
                                </a>
                            </div>
                            <div class="col-md-11">
                                <a href="{{route('product.details', $item->product->slug)}}">
                                    <small>
                                        @if(session()->get('language') == 'bangla')
                                        {{$item->product->name_bn}}
                                        @else
                                        {{$item->product->name_en}}
                                        @endif
                                    </small>
                                </a>
                            </div>
                        </div>
                        @endforeach
                        <br>
                        @else
                        <small>
                            @if(session()->get('language') == 'bangla')
                            ব্র্যান্ড: {{$product->brand->name_bn ?? ''}}
                            @else
                            Brand:
                            {{$product->brand->name_en ?? 'N/A'}}
                            @endif


                        </small><br>
                        @endif
                        <small>
                            @if(session()->get('language') == 'bangla')
                            স্টক:
                            @else
                            Stock:
                            @endif
                            <span class="{{$product->stock_qty > 0 ? 'text-success': 'text-danger'}}">
                                @if(session()->get('language') == 'bangla')
                                {{$product->stock_qty > 0 ? 'স্টকে আছে':'স্টক আউট'}}
                                @else
                                {{$product->stock_qty > 0 ? 'In Stock':'Out of Stock'}}
                                @endif

                            </span>
                            <span id="stock_qty">{{$product->stock_qty != 0 ? '('.$product->stock_qty.')':''}}</span></small><br>


                        </p>
                    </div>
                    <form id="choice_form">
                        <div class="row " id="choice_attributes">
                            @if($product->is_varient)
                            {{-- @php dd($product->attribute_values->attribute_id)  @endphp--}}
                            @php $i=0; @endphp
                            @foreach(json_decode($product->attribute_values) as $attribute)
                            @php
                            $attr = get_attribute_by_id($attribute->attribute_id);
                            $i++;
                            // dd($attribute->attribute_id, $attr->name, $attribute->values[0], $product->id, 1)
                            @endphp
                            <input type="hidden" name="" onload="selectAttribute('{{$attribute->attribute_id}}', '{{$attr->name}}', '{{$attribute->values[0]}}', '{{$product->id}}', '1')">
                            <div class="attr-detail attr-size mb-3">
                                <strong class="mr-10">{{ $attr->name }}: </strong>
                                <input type="hidden" name="attribute_ids[]" id="attribute_id_{{ $i }}" value="{{ $attribute->attribute_id }}">
                                <input type="hidden" name="attribute_names[]" id="attribute_name_{{ $i }}" value="{{ $attr->name }}">
                                <input type="hidden" id="attribute_check_{{ $i }}" value="0">
                                <input type="hidden" id="attribute_check_attr_{{ $i }}" value="0">
                                <ul class="list-filter size-filter font-small">
                                    @foreach($attribute->values as $key=> $value)

                                    <label class="radio-inline">
                                        <input type="radio" class="m-2"
                                            onclick="selectAttribute('{{ $attribute->attribute_id }}{{ $attr->name }}', '{{ $value }}', '{{ $product->id }}', '{{ $i }}')"
                                            name="option_{{$i}}">{{$value}}
                                    </label>
                                    @php $key++; @endphp
                                    @endforeach
                                    <input type="hidden" name="attribute_options[]" id="{{ $attribute->attribute_id }}{{ $attr->name }}" class="attr_value_{{ $i }}">
                                </ul>
                            </div>
                            @endforeach
                            <input type="hidden" id="total_attributes" value="{{ count(json_decode($product->attribute_values)) }}">
                            @endif
                        </div>
                    </form>

                    <!-- <div class="row" id="attribute_alert">

                    </div> -->
                    @if($product->stock_qty > 0)
                    <div class="detail-extralink mb-3 align-items-baseline d-flex">
                        <div class="mr-10">
                            <span class="quantity-label">
                                @if(session()->get('language') == 'bangla')
                                পরিমাণ:
                                @else
                                Quantity:
                                @endif
                            </span>
                        </div>

                        <div class="detail-qty  radius mx-2 py-1 border px-1 d-flex align-items-center" style="border-radius: 6px;">
                            <button type="button" class="qty-down btn btn-sm border"><i class="fa fa-minus"></i></button>
                            <input type="text"
                                name="quantity"
                                class="qty-val mx-2"
                                value="{{$product->minimum_buy_qty}}"
                                min="{{$product->minimum_buy_qty}}"
                                max="{{$product->stock_qty}}"
                                id="qty"
                                style="border: none; width: 40px; text-align: center"
                                readonly>
                            <button type="button" class="qty-up btn btn-sm border"><i class="fa fa-plus"></i></button>
                        </div>

                        <div id="qty_stock_alert" class="text-danger ms-2"></div>
                    </div>

                    <div class="d-flex">
                        <input type="hidden" id="pfrom" value="direct">
                        <input type="hidden" id="product_id" value="{{ $product->id }}" min="1">
                        <input type="hidden" id="{{ $product->id }}-product_pname"
                            value="{{ $product->name_en }}">
                        <button class="buy_now" id="{{$product->is_varient == 1 ? '':'buy_now'}}" type="button" onclick="{{$product->is_varient == 1 ? 'buyProduct()':''}}" style="width:120px; font-size: 15px; color: white !important;">
                            @if(session()->get('language') == 'bangla')
                            এখুনি কিনুন
                            @else
                            Buy Now
                            @endif
                        </button>
                        {{-- <button class="like btn" style="margin-left: 5px" type="button" onclick="addToCartDirect({{$product->id}})">Add to cart</button> --}}
                        <button class="add_to_cart" style="width:120px; margin-left: 5px; margin-bottom: px; background-color: white; font-size: 15px; " type="button" onclick="test()">
                            @if(session()->get('language') == 'bangla')
                            কার্টে যোগ করুন
                            @else
                            Add to cart
                            @endif
                        </button>
                    </div>


                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-3">

            <div class="ml-3 h-100 bg-white p-3 " style="border-radius: 10px;">

                <div>
                    <div class="d-flex justify-content-between">
                        <p><i class="fa-solid fa-truck-fast"></i>
                            @if(session()->get('language') == 'bangla')
                            স্ট্যান্ডার্ড ডেলিভারি
                            @else
                            Standard Delivery
                            @endif
                        </p>
                        <p>৳{{$shipping_charge->shipping_charge}}</p>
                    </div>
                    <small>
                        @if(session()->get('language') == 'bangla')
                        ৩ - ৭ দিন
                        @else
                        3 - 7 day(s)
                        @endif

                    </small>
                    <p><i class="fa-regular fa-handshake"></i>
                        @if(session()->get('language') == 'bangla')
                        ক্যাশ অন ডেলিভারি পাওয়া যাচ্ছে
                        @else
                        Cash on Delivery Available
                        @endif
                    </p>
                </div>
                <hr class="">
                <div class="">
                    @if($product->is_replaceable == 1)
                    <small>{{session()->get('language') == 'bangla' ? 'পণ্য প্রতিস্থাপন'. get_setting('order_return_duration')->value. 'দিনের আগে প্রযোজ্য':'Replacement Applicable Before '.get_setting('order_return_duration')->value.' Days'}}</small>
                    @else
                    <small>{{session()->get('language') == 'bangla' ? 'পণ্য প্রতিস্থাপন প্রযোজ্য নয়':'Product Replacement Not Applicable'}}</small>
                    @endif

                    <p>
                        <i class="fa-solid fa-gears"></i>
                        {{session()->get('language') == 'bangla' ? 'ওয়ারেন্টি পাওয়া যাবে না':' Warranty Not Available'}}
                    </p>
                </div>
                <hr>
                <div>
                    <small>
                        @if(session()->get('language') == 'bangla')
                        বিক্রেতা
                        @else
                        Sold By
                        @endif

                    </small>
                    <div class="d-flex justify-content-between">
                        <p><i class="fa-solid fa-shop"></i> {{$product->vendor_id != 0 ? $product->vendor->shop_name:get_setting('business_name')->value}}</p>
                        {{-- <a href="#"><i class="fa-regular fa-message"></i> CHAT</a>--}}
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- Product Information End -->
<!-- Description Part Start -->
<section class="custom_container mb-5">
    <div class="row g-3">
        <div class="col-md-8 bg-white">
            <div class="p-4">
                <h4>
                    @if(session()->get('language') == 'bangla')
                    পণ্যের বিবরণ
                    @else
                    About this item
                    @endif

                </h4>
                <hr>
                <h6 class="mb-2">Product details</h6>
                @if(session()->get('language') == 'bangla')
                {!! $product->description_bn !!}
                @else
                {!! $product->description_en !!}
                @endif


                <h5 class="font-weight-bold mb-4 mt-3">Ratings & Reviews of {{ $product->name_en }}</h5>

                <div class="row">
                    <!-- Left: Average Rating -->
                    <div class="col-md-4 d-flex flex-column align-items-start">
                        <div class="d-flex align-items-center mb-2">
                            <div class="display-4 font-weight-bold mr-2">{{ number_format($average_rating, 1) }}</div>
                            <div>/5</div>
                        </div>
                        <div class="mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star{{ $i <= round($average_rating) ? '' : '-o' }} text-warning"></i>
                                @endfor
                        </div>
                        <div class="text-muted">{{ $total_ratings }} Ratings</div>
                    </div>

                    <!-- Right: Star Breakdown -->
                    <div class="col-md-8">
                        @foreach($formattedCounts as $star => $count)
                        <div class="d-flex align-items-center mb-2">
                            <div class="mr-2" style="width: 110px;">
                                {{-- Show full stars --}}
                                @for($i = 0; $i < $star; $i++)
                                    <i class="fa fa-star text-warning"></i>
                                    @endfor

                                    {{-- Show empty stars for the remainder --}}
                                    @for($i = $star; $i < 5; $i++)
                                        <i class="fa fa-star-o text-muted"></i> {{-- fa-star-o is empty star --}}
                                        @endfor
                            </div>
                            <div class="progress flex-grow-1 mx-2" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $total_ratings > 0 ? ($count / $total_ratings) * 100 : 0 }}%;"
                                    aria-valuenow="{{ $count }}" aria-valuemin="0" aria-valuemax="{{ $total_ratings }}"></div>
                            </div>
                            <div style="width: 10px;">{{ $count }}</div>
                        </div>
                        @endforeach
                    </div>

                </div>

                <!-- Divider -->
                <hr class="my-4">

                <!-- Product Reviews Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Product Reviews</h6>
                    <div class="d-flex gap-3 align-items-center">

                        <div>
                            Sort:
                            <select id="sortReviews" class="form-select form-select-sm">
                                <option value="relevance" selected>Relevance</option>
                                <option value="recent">Recent</option>
                                <option value="rating_desc">Rating: High to Low</option>
                                <option value="rating_asc">Rating: Low to High</option>
                            </select>
                        </div>

                        <div>
                            Filter:
                            <select id="filterReviews" class="form-select form-select-sm">
                                <option value="all" selected>All Stars</option>
                                @for ($star = 1; $star <= 5; $star++)
                                    <option value="{{ $star }}">{{ $star }} Star{{ $star > 1 ? 's' : '' }}</option>
                                    @endfor
                            </select>
                        </div>

                    </div>
                </div>


                <!-- Dynamic Reviews List -->
                <!-- Dynamic Reviews List -->
                <div id="reviewsContainer">
                    @include('FrontEnd.product.partials.reviews_list', ['reviews' => $reviews])
                </div>

            </div>


        </div>
        <div class="just-for-you col-md-4 mt-1">
            <div class="">
                <h5 class="my-2 ">
                    @if(session()->get('language') == 'bangla')
                    সংশ্লিষ্ট পণ্য
                    @else
                    Related Products
                    @endif
                </h5>
                <!-- Responsive grid: 2 columns on xs/sm, 3 on md, 2 on lg+ -->
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-2 g-4">
                    @if(count($relatedProduct) >0)
                    @foreach ($relatedProduct as $product_trending )
                    <?php $data = calculateDiscount($product_trending->id) ?>
                    <div class="col d-flex">
                        <div class="product_card flex-fill" data-index="{{ $product_trending->id }}" style="
                                min-width: 0;
                                max-width: 100%;
                                background: rgba(255, 255, 255, 0.95);
                                border-radius: 15px;
                                overflow: hidden;
                                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                                position: relative;
                                border: 1px solid rgba(255, 255, 255, 0.3);
                            " 
                            onmouseover="this.style.transform='translateY(-8px) scale(1.02)';this.style.boxShadow='0 20px 40px rgba(0, 0, 0, 0.15)';" 
                            onmouseout="this.style.transform='translateY(0) scale(1)';this.style.boxShadow='0 10px 30px rgba(0, 0, 0, 0.08)';">
                            @if($data['discount'] && $product_trending->regular_price != $data['discount'])
                            <div style="
                                        position: absolute;
                                        top: 10px;
                                        left: 10px;
                                        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
                                        color: white;
                                        padding: 4px 8px;
                                        border-radius: 12px;
                                        font-size: 10px;
                                        font-weight: 700;
                                        z-index: 3;
                                        text-transform: uppercase;
                                        letter-spacing: 0.3px;
                                        box-shadow: 0 3px 10px rgba(238, 90, 36, 0.3);
                                    ">
                                Save {{ $data['text'] }}
                            </div>
                            @endif
    
                            <div style="
                                    position: relative;
                                    height: 150px;
                                    overflow: hidden;
                                    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                                ">
                                <a href="{{ route('product.details', $product_trending->slug) }}" style="
                                        display: block;
                                        height: 100%;
                                        position: relative; 
                                    ">
                                    <img src="{{ asset($product_trending->product_thumbnail) }}" alt="Product Image" style="
                                            width: 100%;
                                            height: 100%;
                                            object-fit: cover;
                                            transition: transform 0.4s ease;
                                        " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                </a>
                            </div>
    
                            <div style="padding: 15px; position: relative;">
                                <a href="{{ route('product.details', $product_trending->slug) }}" style="
                                        text-decoration: none;
                                        color: inherit;
                                    ">
                                    <h3 style="
                                            font-size: 14px;
                                            font-weight: 600;
                                            color: #2c3e50;
                                            margin-top: 5px;
                                            line-height: 1.3;
                                            transition: color 0.3s ease;
                                            display: -webkit-box;
                                            -webkit-line-clamp: 2;
                                            -webkit-box-orient: vertical;
                                            overflow: hidden;
                                            height: 36px;
                                        " onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#2c3e50'">
                                        @if(session()->get('language') == 'bangla')
                                        {{ $product_trending->name_bn }}
                                        @else
                                        {{ $product_trending->name_en }}
                                        @endif
                                    </h3>
                                </a>
    
                                <div style="
                                        display: flex;
                                        align-items: center;
                                        gap: 8px;
                                        margin-bottom: 10px;
                                    ">
                                    <h4 style="
                                            font-size: 16px;
                                            font-weight: 700;
                                            color: #D83F26;
                                            margin: 0;
                                        ">{{ $data['discount'] }} TK</h4>
                                    @if($product_trending->regular_price != $data['discount'])
                                    <span style="
                                                font-size: 12px;
                                                color: #95a5a6;
                                                text-decoration: line-through;
                                                font-weight: 400;
                                            ">{{ $product_trending->regular_price }} TK</span>
                                    @endif
                                </div>
    
                                <div style="
                                        display: flex;
                                        align-items: center;
                                        gap: 5px;
                                        margin-bottom: 12px;
                                    ">
                                    <div style="display: flex; gap: 1px;">
                                        @for($i = 0; $i < 4; $i++)
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#ffc107">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                            </svg>
                                            @endfor
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#e0e0e0">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                            </svg>
                                    </div>
                                    <span style="
                                            font-size: 11px;
                                            color: #7f8c8d;
                                            font-weight: 500;
                                        ">(4.0)</span>
                                </div>
    
                                <div style="display: flex; justify-content: space-around; gap: 6px;">
                                    @if($product_trending->stock_qty > 0)
                                    @if($product_trending->is_varient == 1)
                                    <button onclick="productView({{ $product_trending->id }})" data-bs-toggle="modal" data-bs-target="#quickViewModal" style="
                                                    background: #D83F26;
                                                    color: white;
                                                    border: none;
                                                    padding: 2px 12px;
                                                    border-radius: 6px;
                                                    font-weight: 600;
                                                    font-size: 11px;
                                                    cursor: pointer;
                                                    transition: all 0.3s ease;
                                                    box-shadow: 0 3px 10px rgba(216, 63, 38, 0.3);
                                                " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 5px 15px rgba(216, 63, 38, 0.4)'"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(216, 63, 38, 0.3)'">
                                        @if(session()->get('language') == 'bangla') এখনি কিনুন @else Buy Now @endif
                                    </button>
                                    <button onclick="productView({{ $product_trending->id }})" data-bs-toggle="modal" data-bs-target="#quickViewModal" style="
                                                    background: white;
                                                    color: #D83F26;
                                                    border: 2px solid #D83F26;
                                                    padding: 2px 4px;
                                                    border-radius: 6px;
                                                    font-weight: 600;
                                                    font-size: 11px;
                                                    cursor: pointer;
                                                    transition: all 0.3s ease;
                                                " onmouseover="this.style.background='#D83F26'; this.style.color='white'; this.style.transform='translateY(-1px)'"
                                        onmouseout="this.style.background='white'; this.style.color='#D83F26'; this.style.transform='translateY(0)'">
                                        @if(session()->get('language') == 'bangla') কার্টে যোগ করুন @else Add to Cart @endif
                                    </button>
                                    @else
                                    <button onclick="buyNow({{ $product_trending->id }})" style="
                                                    background: #D83F26;
                                                    color: white;
                                                    border: none;
                                                    padding: 2px 12px;
                                                    border-radius: 6px;
                                                    font-weight: 600;
                                                    font-size: 11px;
                                                    cursor: pointer;
                                                    transition: all 0.3s ease;
                                                    box-shadow: 0 3px 10px rgba(216, 63, 38, 0.3);
                                                " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 5px 15px rgba(216, 63, 38, 0.4)'"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(216, 63, 38, 0.3)'">
                                        @if(session()->get('language') == 'bangla') এখনি কিনুন @else Buy Now @endif
                                    </button>
    
                                    <input type="hidden" id="pfrom" value="direct">
                                    <input type="hidden" id="product_product_id" value="{{ $product_trending->id }}" min="1">
                                    <input type="hidden" id="{{ $product_trending->id }}-product_pname" value="{{ $product_trending->name_en }}">
    
                                    <button onclick="addToCartDirect({{ $product_trending->id }})" style="
                                                    background: white;
                                                    color: #D83F26;
                                                    border: 2px solid #D83F26;
                                                    padding: 2px 4px;
                                                    border-radius: 6px;
                                                    font-weight: 600;
                                                    font-size: 11px;
                                                    cursor: pointer;
                                                    transition: all 0.3s ease;
                                                " onmouseover="this.style.background='#D83F26'; this.style.color='white'; this.style.transform='translateY(-1px)'"
                                        onmouseout="this.style.background='white'; this.style.color='#D83F26'; this.style.transform='translateY(0)'">
                                        @if(session()->get('language') == 'bangla') কার্টে যোগ করুন @else Add to Cart @endif
                                    </button>
                                    @endif
                                    @else
                                    <div style="
                                                width: 100%;
                                                background: linear-gradient(45deg, #f2f2f2, #e6e6e6);
                                                color: #999;
                                                text-align: center;
                                                border-radius: 10px;
                                                padding: 12px 0;
                                                font-weight: 600;
                                                letter-spacing: 0.5px;
                                            ">
                                        @if(session()->get('language') == 'bangla') স্টক আউট @else Out of Stock @endif
                                    </div>
                                    @endif
                                </div>
    
                                @if($product_trending->stock_qty > 0)
                                <div class="stock-indicator" style="
                                            position: absolute;
                                            top: -5px;
                                            left: 12px;
                                            display: flex;
                                            align-items: center;
                                            gap: 5px;
                                        ">
                                    <span style="
                                                width: 8px;
                                                height: 8px;
                                                border-radius: 50%;
                                                background-color: #4caf50;
                                                display: inline-block;
                                            "></span>
                                    <span style="font-size: 12px; color: #666;">In Stock</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p>No Products Found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</section>

@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sortReviews');
        const filterSelect = document.getElementById('filterReviews');
        const reviewsContainer = document.getElementById('reviewsContainer');
        const productSlug = "{{ $product->slug }}";

        function fetchReviews() {
            const sort = sortSelect.value;
            const filter = filterSelect.value;

            fetch(`/product/${productSlug}/reviews?sort=${sort}&filter=${filter}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    reviewsContainer.innerHTML = data.html;
                })
                .catch(error => {
                    console.error('Error fetching reviews:', error);
                });
        }

        sortSelect.addEventListener('change', fetchReviews);
        filterSelect.addEventListener('change', fetchReviews);
    });
</script>

<script>
    //Qty Up-Down
    $('.detail-qty').each(function() {
        var qtyval = parseInt($(this).find(".qty-val").val(), 10);

        $('.qty-up').on('click', function(event) {
            event.preventDefault();
            qtyval = qtyval + 1;
            $(this).prev().val(qtyval);
        });

        $(".qty-down").on("click", function(event) {
            event.preventDefault();
            qtyval = qtyval - 1;
            if (qtyval > 1) {
                $(this).next().val(qtyval);
            } else {
                qtyval = 1;
                $(this).next().val(qtyval);
            }
        });
    });

    function addCart(id) {
        var qty = $('.qty-val').val();
        addToCartDirect(id, false, qty);
    }


    {
        {
            --$('#buy_now').on('click', function() {
                    --
                }
            } {
                {
                    --
                    var qty = $('.qty-val').val();
                    --
                }
            } {
                {
                    --
                    var id = {
                        {
                            $product - > id
                        }
                    };
                    --
                }
            } {
                {
                    --buyNow(id, qty);
                    --
                }
            }

            {
                {
                    --
                });
            --
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get DOM elements
        const qtyContainer = document.querySelector('.detail-qty');
        const qtyInput = qtyContainer.querySelector('.qty-val');
        const qtyDownBtn = qtyContainer.querySelector('.qty-down');
        const qtyUpBtn = qtyContainer.querySelector('.qty-up');
        const alertDiv = document.getElementById('qty_stock_alert');

        // Get quantity constraints
        const minQty = parseInt(qtyInput.getAttribute('min'));
        const maxQty = parseInt(qtyInput.getAttribute('max'));
        let currentQty = parseInt(qtyInput.value);

        // Update quantity display and controls
        function updateQuantity(qty) {
            currentQty = qty;
            qtyInput.value = qty;

            // Update button states
            qtyDownBtn.disabled = qty <= minQty;
            qtyUpBtn.disabled = qty >= maxQty;

            // Show appropriate alerts
            if (qty >= maxQty) {
                alertDiv.textContent = '';
            } else if (qty <= minQty) {
                alertDiv.textContent = '';
            } else {
                alertDiv.textContent = '';
            }
        }

        // Initialize
        updateQuantity(currentQty);

        // Event Listeners
        qtyUpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentQty < maxQty) {
                updateQuantity(currentQty + 1);
            }
        });

        qtyDownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentQty > minQty) {
                updateQuantity(currentQty - 1);
            }
        });

        // Update cart/buy functions to use quantity
        window.addCart = function(id) {
            const qty = parseInt(qtyInput.value);
            addToCartDirect(id, false, qty);
        }

        window.buyNow = function(id) {
            const qty = parseInt(qtyInput.value);
            buyProduct(id, qty);
        }
    });
</script>
<script src="{{asset('FrontEnd')}}/assect/js/xzoom.js"></script>
<script src="{{asset('FrontEnd')}}/assect/js/magnific-popup.js"></script>
<script src="{{asset('FrontEnd')}}/assect/js/setup.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
@endpush