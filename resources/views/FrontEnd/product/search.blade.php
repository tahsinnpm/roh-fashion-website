
@extends('FrontEnd.master')
@section('title')
    Searched Products
@endsection
@section('content')
<style>
    .search-container {
        background: linear-gradient(to bottom, #f9f9f9, #ffffff);
        min-height: 100vh;
        font-family: 'Roboto', sans-serif;
    }

    .breadcrumb {
        background: transparent;
        padding: 1rem 0;
        font-size: 1rem;
        color: #333;
    }

    .breadcrumb a {
        color: #921A40;
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb a:hover {
        color: #7a1634;
    }

    .breadcrumb span {
        color: #2c3e50;
        font-weight: 500;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 2rem;
    }

    .product-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    .product-image {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
    }

    .product-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.05);
    }

    .product-details {
        padding: 10px;
        text-align: start;
    }

    .product-name {
        font-size: 1rem;
        font-weight: 600;
        text-align: start;

        color: #2c3e50;
        margin-bottom: 0.4rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price {
        font-size: 1.2rem;
        font-weight: 700;
        padding: 0;
        text-align: start;
        color: #921A40;
        border: none;
        margin-bottom: 0.5rem;
    }

    .discount-percent {
        font-size: 0.9rem;
        color: #28a745;
        margin-bottom: 0.5rem;
    }

    .discount-price {
        color: #95a5a6;
        text-decoration: line-through;
        margin-right: 0.5rem;
    }

    .product-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
    }

    .btn-buy, .btn-cart {
        flex: 1;
        padding: 0 0.4rem;
        border-radius: 10px;
        border: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .btn-buy {
        background: #921A40;
        color: white;
    }

    .btn-buy:hover {
        background: #7a1634;
        transform: translateY(-2px);
    }

    .btn-cart {
        background: white;
        color: #921A40;
        border: 2px solid #921A40;
    }

    .btn-cart:hover {
        background: #921A40;
        color: white;
        transform: translateY(-2px);
    }

    .out-of-stock {
        background: #dc3545;
        color: white;
        text-align: center;
        padding: 0.75rem;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
    }

    @media (max-width: 576px) {
        .product-grid {
            grid-template-columns: 1fr;
        }

        .product-name {
            font-size: 1rem;
        }

        .product-price {
            font-size: 1.2rem;
        }
    }
</style>

<div class="search-container">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">{{session()->get('language') == 'bangla' ? 'হোম ':'Home '}}</a> /
            <span>{{session()->get('language') == 'bangla' ? 'অনুসন্ধান ফলাফল ':'Search Results '}}</span>
        </nav>

        <!-- Product Start -->
        <section class="product-grid">
            @if(count($products)>0)
                @foreach ($products as $product_trending )
                    <?php $data = calculateDiscount($product_trending->id)?>
                    <div class="product-card">
                        <div class="product-image">
                            <a href="{{route('product.details', $product_trending->slug)}}">
                                <img src="{{ asset($product_trending->product_thumbnail) }}" alt="{{ $product_trending->name_en }}">
                            </a>
                        </div>
                        <div class="product-details">
                            <a href="{{route('product.details', $product_trending->slug)}}" class="text-decoration-none">
                                <h3 class="product-name">
                                    @if(session()->get('language') == 'bangla')
                                        {!! Str::limit($product_trending->name_bn, 30) !!}
                                    @else
                                        {!! Str::limit($product_trending->name_en, 30) !!}
                                    @endif
                                </h3>
                            </a>
                            <div class="product-price">৳{{ $data['discount'] }}</div>
                            @if($data['discount'] < $product_trending->regular_price)
                            <p class="discount-percent">
                                <span class="discount-price">৳{{ $product_trending->regular_price }}</span>
                                {{ $data['text'] }}
                            </p>
                            @endif
                            @if($product_trending->stock_qty > 0)
                                <div class="product-actions">
                                    @if($product_trending->is_varient == 1)
                                        <button type="button" class="btn-buy" onclick="productView({{ $product_trending->id }})" data-bs-toggle="modal" data-bs-target="#quickViewModal">
                                            {{session()->get('language') == 'bangla' ? 'এখুনি কিনুন':'Buy Now'}}
                                        </button>
                                        <button type="button" class="btn-cart" onclick="productView({{ $product_trending->id }})" data-bs-toggle="modal" data-bs-target="#quickViewModal">
                                            {{session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন':'Add Cart'}}
                                        </button>
                                    @else
                                        <button type="button" class="btn-buy" onclick="buyNow({{ $product_trending->id }})">
                                            {{session()->get('language') == 'bangla' ? 'এখুনি কিনুন':'Buy Now'}}
                                        </button>
                                        <button type="button" class="btn-cart" onclick="addToCartDirect({{ $product_trending->id }})">
                                            {{session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন':'Add Cart'}}
                                        </button>
                                        <input type="hidden" id="pfrom" value="direct">
                                        <input type="hidden" id="product_product_id" value="{{ $product_trending->id }}" min="1">
                                        <input type="hidden" id="{{ $product_trending->id }}-product_pname" value="{{ $product_trending->name_en }}">
                                    @endif
                                </div>
                            @else
                                <div class="out-of-stock">
                                    {{session()->get('language') == 'bangla' ? 'স্টক আউট':'Out of Stock'}}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <p class="text-muted fs-5">{{session()->get('language') == 'bangla' ? 'কোনো পণ্য পাওয়া যায়নি':'No products found'}}</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
@push('js')
<script>
    function addToCart() {
        var total_attributes = parseInt($('#total_attributes').val());
        var checkNotSelected = 0;
        var checkAlertHtml = '';
        for (var i = 1; i <= total_attributes; i++) {
            var checkSelected = parseInt($('#attribute_check_' + i).val());
            if (checkSelected == 0) {
                checkNotSelected = 1;
                checkAlertHtml += `<div class="attr-detail mb-5">
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <div>
                            <i class="fa fa-warning mr-10"></i> <span> Select ` + $('#attribute_name_' + i).val() + `</span>
                        </div>
                    </div>
                </div>`;
            }
        }
        if (checkNotSelected == 1) {
            $('#qty_alert').html('');
            $('#attribute_alert').html(`<div class="attr-detail mb-5">
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                        <i class="fa fa-warning mr-10"></i> <span> Select all attributes</span>
                    </div>
                </div>
            </div>`);
            return false;
        }
        $('.size-filter li').removeClass("active");
        var product_name = $('#pname').val();
        var id = $('#product_id').val();
        var price = $('#product_price').val();
        var color = $('#color option:selected').val();
        var size = $('#size option:selected').val();
        var quantity = $('#qty').val();
        var varient = $('#pvarient').val();
        var min_qty = parseInt($('#minimum_buy_qty').val());
        if (quantity < min_qty) {
            $('#attribute_alert').html('');
            $('#qty_alert').html(`<div class="attr-detail mb-5">
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                        <i class="fa fa-warning mr-10"></i> <span> Minimum quantity ` + min_qty + ` required.</span>
                    </div>
                </div>
            </div>`);
            return false;
        }
        var options = $('#choice_form').serializeArray();
        var jsonString = JSON.stringify(options);
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            icon: 'success',
            showConfirmButton: false,
            timer: 1200
        });
        $.ajax({
            type: 'POST',
            url: '/cart/data/store/' + id,
            dataType: 'json',
            data: {
                color: color,
                size: size,
                quantity: quantity,
                product_name: product_name,
                product_price: price,
                product_varient: varient,
                options: jsonString,
            },
            success: function (data) {
                miniCart();
                $('#closeModel').click();
                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                        type: 'success',
                        title: data.success
                    });
                    $('#qty').val(min_qty);
                    $('#pvarient').val('');
                    for (var i = 1; i <= total_attributes; i++) {
                        $('#attribute_check_' + i).val(0);
                    }
                } else {
                    Toast.fire({
                        type: 'error',
                        title: data.error
                    });
                    $('#qty').val(min_qty);
                    $('#pvarient').val('');
                    for (var i = 1; i <= total_attributes; i++) {
                        $('#attribute_check_' + i).val(0);
                    }
                }
                var buyNowCheck = $('#buyNowCheck').val();
                if (buyNowCheck && buyNowCheck == 1) {
                    $('#buyNowCheck').val(0);
                    window.location = '/checkout';
                }
            }
        });
    }

    function miniCartRemove(rowId) {
        $.ajax({
            type: 'GET',
            url: '/minicart/product-remove/' + rowId,
            dataType: 'json',
            success: function (data) {
                miniCart();
                cart();
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000
                });
                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                        type: 'success',
                        title: data.success
                    });
                } else {
                    Toast.fire({
                        type: 'error',
                        title: data.error
                    });
                }
            }
        });
    }
</script>
@endpush
