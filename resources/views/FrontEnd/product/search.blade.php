
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
        color: #18395C;
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb a:hover {
        color: #18395C;
    }

    .breadcrumb span {
        color: #2c3e50;
        font-weight: 500;
    }

    .product-grid > .text-center {
        grid-column: 1 / -1;
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
                    <x-product-card :product="$product_trending" />
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
