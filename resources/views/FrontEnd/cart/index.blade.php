@extends('FrontEnd.master')
@section('title')
Shopping Cart
@endsection
@section('content')
<!-- Cart Header Start -->
<div class="" style="">
    <div class="custom_container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="mt-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}" style="color: #921A40; text-decoration: none; font-weight: 500;">
                                @if(session()->get('language') == 'bangla')
                                হোম
                                @else
                                Home
                                @endif
                            </a>
                        </li>
                        <li class="breadcrumb-item active" style="color: #921A40; font-weight: 600;">
                            @if(session()->get('language') == 'bangla')
                            শপিং কার্ট
                            @else
                            Shopping Cart
                            @endif
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Cart Header End -->

<!-- Cart Content Start -->
<section class="custom_container my-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="cart-container" style="background: #fff; border-radius: 16px; padding: 0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); overflow: hidden; margin-bottom: 20px;">
                <div class="cart-header" style="background: linear-gradient(135deg, #921A40 0%, #921A40 100%); padding: 20px 25px; color: #fff;">
                    <h4 class="mb-0" style="font-weight: 600; letter-spacing: 0.5px; color: #fafafa;">
                        @if(session()->get('language') == 'bangla')
                        আপনার কার্ট আইটেম
                        @else
                        Your Cart Items
                        @endif
                    </h4>
                </div>
                <div class="p-4">
                    <div id="cartPage">
                        <!-- Cart items will be loaded dynamically here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="summary-card" style="background: #fff; border-radius: 16px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">
                <div class="summary-header" style="background: linear-gradient(135deg, #921A40 0%, #921A40 100%); padding: 20px 25px; color: #fff;">
                    <h4 class="fw-semibold mb-0" style="letter-spacing: 0.5px; color: #fafafa;">
                        @if(session()->get('language') == 'bangla')
                        কার্ট সারাংশ
                        @else
                        Cart Summary
                        @endif
                    </h4>
                </div>
                <div class="summary-body" style="padding: 25px;">
                    <div class="summary-row d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold" style="color: #2D3142; font-size: 1.05rem;">
                            @if(session()->get('language') == 'bangla')
                            সাবটোটাল
                            @else
                            Subtotal
                            @endif
                        </span>
                        <span class="fw-semibold" style="color: #921A40; font-size: 1.15rem;">৳<span id="cartSubTotal"></span></span>
                    </div>
                    <div class="summary-total pt-3 border-top" style="border-top: 2px dashed #e9ecef !important;">
                        <div class="d-flex justify-content-between align-items-center my-2">
                            <span class="fw-bold" style="color: #2D3142; font-size: 1.25rem;">
                                @if(session()->get('language') == 'bangla')
                                মোট
                                @else
                                Total
                                @endif
                            </span>
                            <span class="fw-bold" style="color: #921A40; font-size: 1.3rem;">৳<span id="cartTotal"></span></span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        @auth
                        <a href="{{ route('checkout') }}" class="btn w-100 fw-bold py-3" style="background: linear-gradient(135deg, #921A40 0%, #921A40 100%); color: #fff; border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(146, 26, 64, 0.3); transition: all 0.3s; font-size: 1.1rem;">
                            @if(session()->get('language') == 'bangla')
                            চেকআউটে যান
                            @else
                            Proceed To Checkout
                            @endif
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        @endauth
                        @guest
                        <a href="{{ route('login') }}" class="btn w-100 fw-bold py-3" style="background: linear-gradient(135deg, #921A40 0%, #921A40 100%); color: #fff; border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(146, 26, 64, 0.3); transition: all 0.3s; font-size: 1.1rem;">
                            @if(session()->get('language') == 'bangla')
                            অর্ডার করুন
                            @else
                            Place Order
                            @endif
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        @endguest
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('home') }}" class="text-decoration-none" style="color: #921A40; font-weight: 500;">
                            <i class="fas fa-arrow-left me-2"></i>
                            @if(session()->get('language') == 'bangla')
                            শপিং চালিয়ে যান
                            @else
                            Continue Shopping
                            @endif
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="security-badge text-center p-3" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                <div class="d-flex justify-content-center align-items-center mb-2">
                    <i class="fas fa-shield-alt me-2" style="color: #28a745; font-size: 1.2rem;"></i>
                    <span style="font-weight: 600; color: #2D3142;">
                        @if(session()->get('language') == 'bangla')
                        নিরাপদ চেকআউট
                        @else
                        Secure Checkout
                        @endif
                    </span>
                </div>
                <p class="small mb-0" style="color: #6c757d;">
                    @if(session()->get('language') == 'bangla')
                    আপনার তথ্য নিরাপদ এবং এনক্রিপ্ট করা
                    @else
                    Your information is safe and encrypted
                    @endif
                </p>
            </div>
        </div>
    </div>
</section>
<!-- Cart Content End -->
@endsection

@push('js')
<script>
    function cart() {
        $.ajax({
            type: 'GET',
            url: '/get-cart-product',
            dataType: 'json',
            success: function(response) {
                var rows = "";
                $('#total_cart_qty').text(Object.keys(response.carts).length);

                if (Object.keys(response.carts).length > 0) {
                    var subtotal = 0;

                    $.each(response.carts, function(key, value) {
                        var slug = value.options.slug;
                        var base_url = window.location.origin;
                        subtotal += value.subtotal;

                        rows += `
                        <div class="product-card p-3 mb-3" style="border-radius: 12px; background: #f8f9fa; border: 1px solid #e9ecef; transition: all 0.3s;">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-4 mb-2 mb-md-0">
                                    <img src="/${value.options.image}" class="product-img img-fluid" style="border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08); height: 100px; object-fit: cover; width: 100%;" alt="${value.name}">
                                </div>
                                <div class="col-md-4 col-8 mb-2 mb-md-0">
                                    <h5 class="product-name mb-2" style="font-weight: 600; color: #2D3142;">
                                        <a href="${base_url}/product-details/${slug}" style="text-decoration: none; color: #2D3142; transition: color 0.3s; font-size: 1.1rem;">${value.name}</a>
                                    </h5>
                                    <div class="product-attributes">`;
                        $.each(value.options.attribute_names, function(index, val) {
                            rows += `<span class="attribute-tag" style="display: inline-block; padding: 4px 12px; background: rgba(184, 46, 24, 0.1); border-radius: 20px; font-size: 0.85rem; margin-right: 8px; margin-bottom: 8px; color: #921A40; border: 1px solid rgba(184, 46, 24, 0.2);">${val}: ${value.options.attribute_values[index]}</span>`;
                        });
                        rows += `
                                    </div>
                                </div>
                                <div class="col-md-2 col-4 text-md-center mb-2 mb-md-0">
                                    <h5 class="price mb-0" style="font-weight: 00; color: #921A40; font-size: 1.2rem;">৳${value.price}</h5>
                                </div>
                                <div class="col-md-2 col-4 text-md-center mb-2 mb-md-0">
                                    <div class="quantity-control mx-auto" style="display: flex; align-items: center; background: #fff; border-radius: 30px; padding: 5px; width: fit-content; border: 1px solid #e9ecef; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                        ${value.qty > 1
                                            ? `<button type="button" class="qty-btn qty-btn-minus" id="${value.rowId}" onclick="cartDecrement(this.id)" style="width: 36px; height: 36px; border-radius: 50%; border: none; background: rgba(146, 26, 64, 0.1); color: #921A40; font-weight: 600; transition: all 0.2s;">
                                                <i class="fas fa-minus"></i>
                                               </button>`
                                            : `<button type="button" class="qty-btn qty-btn-minus" disabled style="width: 36px; height: 36px; border-radius: 50%; border: none; background: #f5f5f5; color: #ccc;">
                                                <i class="fas fa-minus"></i>
                                               </button>`
                                        }
                                        <input type="text" value="${value.qty}" class="qty-input" style="width: 45px; border: none; background: transparent; text-align: center; font-weight: 600; color: #2D3142; font-size: 1.1rem;" disabled>
                                        <button type="button" class="qty-btn qty-btn-plus" id="${value.rowId}" onclick="cartIncrement(this.id)" style="width: 36px; height: 36px; border-radius: 50%; border: none; background: #921A40; color: #fff; font-weight: 600; transition: all 0.2s;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-1 col-2 text-md-center mb-2 mb-md-0">
                                    <h5 class="subtotal mb-0" style="font-weight: 600; color: #921A40; font-size: 1.2rem;">৳${value.subtotal}</h5>
                                </div>
                                <div class="col-md-1 col-2 text-center">
                                    <button type="button" id="${value.rowId}" onclick="cartRemove(this.id)" class="remove-btn ms-auto" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(220, 53, 69, 0.1); border: none; color: #dc3545; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    });

                    $('#cartPage').html(rows);
                    $('#cartSubTotal').text(subtotal);
                    $('#cartTotal').text(subtotal);

                } else {
                    rows = `
                    <div class="text-center py-5">
                        <div class="empty-cart-icon mb-4">
                            <i class="fas fa-shopping-cart" style="font-size: 5rem; color: #e9ecef;"></i>
                        </div>
                        <h4 style="color: #921A40; margin-bottom: 12px; font-weight: 600;">Your cart is empty!</h4>
                        <p class="text-muted mb-4" style="font-size: 1.1rem;">Looks like you haven't added anything to your cart yet.</p>
                        <a href="{{ route('home') }}" class="btn py-2 px-4" style="background: linear-gradient(135deg, #921A40 0%, #921A40 100%); color: #fff; border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(146, 26, 64, 0.3);">
                            Continue Shopping
                        </a>
                    </div>`;
                    $('#cartPage').html(rows);
                    $('#cartSubTotal').text(0);
                    $('#cartTotal').text(0);
                }
            }
        });
    }
    cart();

    /* ==================== Start cartIncrement ================== */
    function cartIncrement(rowId) {
        $.ajax({
            type: 'GET',
            url: "/cart-increment/" + rowId,
            dataType: 'json',
            success: function(data) {
                cart();
                miniCart();

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1200
                })

                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                        type: 'success',
                        title: data.success
                    })
                } else {
                    Toast.fire({
                        type: 'error',
                        icon: 'error',
                        title: data.error
                    })
                }
            }
        });
    }
    /* ==================== End cartIncrement ================== */

    /* ==================== Start Cart Decrement ================== */
    function cartDecrement(rowId) {
        $.ajax({
            type: 'GET',
            url: "/cart-decrement/" + rowId,
            dataType: 'json',
            success: function(data) {
                cart();
                miniCart();

                // Start Message
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                })
                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                        type: 'success',
                        icon: 'success',
                        title: data.success
                    })
                } else {
                    Toast.fire({
                        type: 'error',
                        icon: 'error',
                        title: data.error
                    })
                }
            }
        });
    }
    /* ==================== End Cart Decrement ================== */

    /* ================ Start My Cart Remove Product =========== */
    function cartRemove(id) {
        $.ajax({
            type: 'GET',
            url: '/cart-remove/' + id,
            dataType: 'json',
            success: function(data) {
                cart();
                miniCart();

                // Start Message
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                })
                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                        type: 'success',
                        icon: 'success',
                        title: data.success
                    })
                } else {
                    Toast.fire({
                        type: 'error',
                        icon: 'error',
                        title: data.error
                    })
                }
                // End Message
            }
        });
    }
    /* ==================== End Cart Remove ================== */
</script>
@endpush