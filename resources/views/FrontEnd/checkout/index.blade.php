@extends('FrontEnd.master')
@section('title')
    Checkout
@endsection
@section('content')
    <style>
        .payment-option {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option input[type="radio"] {
            display: none;
        }

        .payment-option.active {
            border: 2px solid #0d6efd;
            background-color: #eaf2ff;
        }
    </style>

    <!-- Checkout Section Start -->
    <section class="py-4 bg-light">
        <div class="custom_container">
            <!-- Checkout Header -->
            <div class=" mb-3">
                @if(session()->get('language') == 'bangla')
                    <h2 class="fw-semibold mb-2" style="color: #C5351D;">আপনার কেনাকাটা সম্পন্ন করুন</h2>
                @else
                    <h2 class="fw-semibold mb-2" style="color: #C5351D;">Complete Your Purchase</h2>
                @endif
            </div>

            <div class="row g-4">
                <!-- Left Column - Form -->
                <div class="col-lg-8">
                    <!-- Coupon Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-body p-0">
                            <div
                                class="p-3 bg-primary bg-opacity-10 border-start border-5 border-black d-flex align-items-center">
                                <!-- <div class="me-3">
                                                            <i class="bi bi-tag-fill text-primary fs-3"></i>
                                                        </div> -->
                                <div class="flex-grow-1">
                                    @if(session()->get('language') == 'bangla')
                                        <h5 class="fw-semibold mb-1 text-white">কুপন আছে?</h5>
                                    @else
                                        <h5 class="fw-semibold mb-1 text-white">Do you have a coupon?</h5>
                                    @endif
                                    @if(session()->get('language') == 'bangla')
                                        <p class="text-muted mb-0 small " style="color: #fafafa !important;">আপনার কেনাকাটায় ছাড়
                                            পেতে কুপন কোড প্রবেশ করুন।</p>
                                    @else
                                        <p class="text-muted mb-0 small " style="color: #fafafa !important;">Enter your coupon
                                            code to get a discount on your purchase.</p>
                                    @endif

                                </div>
                            </div>
                            <div class="p-4">
                                <form action="{{ route('apply-coupon') }}" method="post" class="d-flex">
                                    @csrf
                                    <input type="hidden" name="cart_value" value="{{ $cartTotal }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-lg border-end-0"
                                            name="apply_coupon" placeholder="Enter coupon code">
                                        <button type="submit" class="btn btn-primary px-4" style="color: #fff;">
                                            @if(session()->get('language') == 'bangla')
                                                <span class="d-none d-md-inline">কুপন প্রয়োগ করুন</span>
                                            @else
                                                <span class="d-none d-md-inline">Apply Coupon</span>
                                            @endif

                                            @if(session()->get('language') == 'bangla')
                                                <span class="d-inline d-md-none">কুপন </span>
                                            @else
                                                <span class="d-inline d-md-none">Apply</span>
                                            @endif
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Form Card -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white px-3 pt-3 border-0">
                            <h4 class="fw-semibold m-0" style="color: #C5351D;">Checkout Details</h4>
                        </div>

                        <div class="card-body p-4">
                            <form action="{{ route('checkout.payment') }}" method="post">
                                @csrf

                                <!-- Personal Information Section -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-1 me-2">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <h5 class="fw-medium mb-0">Personal Information</h5>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="name" name="name"
                                                    placeholder="Full Name" value="{{ Auth::user()->name ?? old('name') }}"
                                                    required>
                                                <label for="name">Full Name <span class="text-danger">*</span></label>
                                            </div>
                                            @error('name')
                                                <div class="text-danger small mt-1">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" id="email" name="email"
                                                    placeholder="Email" value="{{ Auth::user()->email ?? old('email') }}"
                                                    required>
                                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" id="phone" name="phone"
                                                    placeholder="Phone" value="{{ Auth::user()->phone ?? old('phone') }}"
                                                    required>
                                                <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Shipping Information Section -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-1 me-2">
                                            <i class="bi bi-geo-alt text-primary"></i>
                                        </div>
                                        <h5 class="fw-medium mb-0">Shipping Information</h5>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="address" id="address"
                                                    style="height: 100px" placeholder="Address"
                                                    required>{{ old('address') }}</textarea>
                                                <label for="address">Complete Address <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            @error('address')
                                                <div class="text-danger small mt-1">{{$message}}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select" name="division_id" id="division_id" required>
                                                    <option value="">Select Division</option>
                                                    @foreach(get_divisions() as $division)
                                                        <option value="{{ $division->id }}">
                                                            {{ ucwords($division->division_name_en) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <label for="division_id">Division <span class="text-danger">*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select" name="district_id" id="district_id" required>
                                                    <option value="">Select District</option>
                                                </select>
                                                <label for="district_id">District <span class="text-danger">*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select" name="upazilla_id" id="upazilla_id" required>
                                                    <option value="">Select Upazila</option>
                                                </select>
                                                <label for="upazilla_id">Upazila <span class="text-danger">*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <select class="form-select" name="shipping_id" id="shipping_id" required>
                                                    <option value="">Select Shipping Method</option>
                                                    @foreach ($shippings as $key => $shipping)
                                                        <option value="{{ $shipping->id }}">@if($shipping->type == 1) Inside
                                                        Dhaka @else Outside Dhaka @endif</option>
                                                    @endforeach
                                                </select>
                                                <label for="shipping_id">Shipping Method <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information Section -->
                                <div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-1 me-2">
                                            <i class="bi bi-pencil text-primary"></i>
                                        </div>
                                        <h5 class="fw-medium mb-0">Additional Notes</h5>
                                    </div>

                                    <div class="form-floating">
                                        <textarea class="form-control" name="comment" id="comment" style="height: 100px"
                                            placeholder="Additional Information"></textarea>
                                        <label for="comment">Special Delivery Instructions (Optional)</label>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="col-lg-4">
                    <!-- Order Summary Card -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden ">
                        <div class="card-header p-3 bg-primary text-white">
                            @if(session()->get('language') == 'bangla')
                                <h4 class="fw-semibold m-0" style="color: #fafafa !important;">অর্ডার সারাংশ</h4>
                            @else
                                <h4 class="fw-semibold m-0" style="color: #fafafa !important;">Order Summary</h4>
                            @endif

                        </div>

                        <div class="card-body p-0">
                            <!-- Products List -->
                            <div class="">
                                <!-- @if(session()->get('language') == 'bangla')
                                            <h6 class="fw-semibold border-bottom pb-2 mb-3">আপনার কার্টে আইটেমসমূহ</h6>
                                        @else

                                            <h6 class="fw-semibold border-bottom pb-2 mb-3">Items in Your Cart</h6>
                                        @endif -->

                                @foreach ($carts as $cart)
                                    <div class="card mb-3 border-1 border-bottom">
                                        <div class="card-body d-flex align-items-end px-3 py-2">
                                            <div class="flex-grow-1">
                                                <div class="fw-medium">{{ $cart->name }}</div>
                                                <div class="text-muted small">Quantity: <span
                                                        class="fw-medium">{{ $cart->qty }}</span></div>
                                            </div>
                                            <div class="ms-3 text-end">
                                                <span class="fw-medium fs-5">৳{{ $cart->subtotal }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Price Breakdown -->
                            <div class="p-4 bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-medium">৳<span id="cartSubTotal">{{ $cartTotal }}</span></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Shipping</span>
                                    <span class="fw-medium">৳<span id="ship_amount">0.00</span></span>
                                </div>

                                <!-- Hidden inputs -->
                                <input type="hidden" value="" name="shipping_charge" class="ship_amount" />
                                <input type="hidden" value="" name="shipping_type" class="shipping_type" />
                                <input type="hidden" value="" name="shipping_name" class="shipping_name" />
                                <input type="hidden" value="{{ $cartTotal }}" name="sub_total" id="cartSubTotalShi" />
                                <input type="hidden" value="" name="grand_total" id="grand_total" />

                                @if (Session::get('couponCode'))
                                    <input type="hidden" name="coupon" value="{{ $cartTotal }}">
                                @endif

                                <!-- Coupon display area -->
                                <div id="couponInformation" class="mb-0"></div>
                            </div>

                            <!-- Total -->
                            <div class="p-3 bg-primary bg-opacity-10 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-white fs-5">Total</span>
                                    <span class="fw-semibold text-white fs-5 ">৳<span
                                            id="grand_total_set">{{ $cartTotal }}</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Payment Method -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
                        <div class="card-header p-3 bg-primary text-white">
                            <h4 class="fw-semibold text-white m-0">Payment Method</h4>
                        </div>

                        <div class="card-body p-4">

                            <!-- COD + Wallet Row -->
                            <div class="row g-3 mb-4">
                                <!-- Cash On Delivery -->
                                <div class="col-6">
                                    <div class="form-check d-flex align-items-start p-3 border rounded payment-option active h-100"
                                        id="label-cod">
                                        <input class="form-check-input mt-1 me-3" style="transform: scale(1.2);"
                                            type="radio" name="payment_option" id="cod" value="cod" checked>
                                        <div>
                                            <label class="form-check-label fw-semibold" for="cod">
                                                Cash On Delivery
                                            </label>

                                        </div>
                                    </div>
                                </div>


                                <!-- Wallet -->
                                <div class="col-6" id="wallet-option-wrapper" style="display: none;">
                                    <label class="payment-option p-3 d-block border rounded h-100" id="label-wallet">
                                        <input type="radio" name="payment_option" value="wallet" id="wallet">
                                        <div class="fw-semibold text-center">Wallet</div>
                                    </label>
                                </div>
                            </div>

                            <!-- Digital Payments -->
                            <div class="row g-3">
                                <!-- bKash -->
                                <div class="col-6">
                                    <label class="payment-option p-3 d-block border rounded" id="label-bkash">
                                        <input type="radio" name="payment_option" value="bkash" id="bkash">
                                        <img src="{{ asset('frontend/assect/img/theme/bkash.png') }}" alt="bKash"
                                            class="img-fluid mb-2">

                                    </label>
                                </div>

                                <!-- Nagad -->
                                <div class="col-6">
                                    <label class="payment-option p-3 d-block border rounded" id="label-nagad">
                                        <input type="radio" name="payment_option" value="nagad" id="nagad">
                                        <img src="{{ asset('frontend/assect/img/theme/nagad.png') }}" alt="Nagad"
                                            class="img-fluid mb-2">

                                    </label>
                                </div>

                                <!-- Aamarpay -->
                                <!-- <div class="col-6">
                                    <label class="payment-option p-3 d-block border rounded" id="label-aamarpay">
                                        <input type="radio" name="payment_option" value="aamarpay" id="aamarpay">
                                        <img src="{{ asset('frontend/assect/img/theme/aamarpay.png') }}" alt="Aamarpay"
                                            class="img-fluid mb-2">

                                    </label>
                                </div> -->

                                <!-- SSLCommerz -->
                                <!-- <div class="col-6">
                                    <label class="payment-option p-3 d-block border rounded" id="label-sslcommerz">
                                        <input type="radio" name="payment_option" value="sslcommerz" id="sslcommerz">
                                        <img src="{{ asset('frontend/assect/img/theme/sslcommerz.png') }}" alt="SSLCommerz"
                                            class="img-fluid mb-2">

                                    </label>
                                </div> -->
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="btn btn-primary btn-lg w-100 py-3 mt-4 fw-bold d-flex justify-content-center align-items-center bg-white">
                                <i class="bi bi-bag-check-fill me-2"></i> Place Order
                            </button>
                        </div>
                    </div>



                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <!--  Division To District Show Ajax -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('select[name="division_id"]').on('change', function () {
                var division_id = $(this).val();
                if (division_id) {
                    $.ajax({
                        url: 'division-district/ajax/',
                        type: "GET",
                        data: { 'division_id': division_id },
                        dataType: "json",
                        success: function (data) {
                            // Reset district selection
                            $('select[name="district_id"]').html('<option value="" selected="" disabled="">Select District</option>');
                            // Populate district options
                            $.each(data, function (key, value) {
                                $('select[name="district_id"]').append('<option value="' + value.id + '">' + capitalizeFirstLetter(value.district_name_en) + '</option>');
                            });
                            $('select[name="upazilla_id"]').html('<option value="" selected="" disabled="">Select Upazila</option>');
                        },
                    });
                } else {
                    // Reset district selection if division is not selected
                    $('select[name="district_id"]').html('<option value="" selected="" disabled="">Select District</option>');
                    $('select[name="upazilla_id"]').html('<option value="" selected="" disabled="">Select Upazila</option>');
                }
            });

            // Function to capitalize first letter of a string
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            // Address Relationship Division/District/Upazilla Show Data Ajax
            $('select[name="address_id"]').on('change', function () {
                var address_id = $(this).val();
                $('.selected_address').removeClass('d-none');
                if (address_id) {
                    $.ajax({
                        url: "{{  url('/address/ajax') }}/" + address_id,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $('#dynamic_division').text(capitalizeFirstLetter(data.division_name_en));
                            $('#dynamic_division_input').val(data.division_id);
                            $("#dynamic_district").text(capitalizeFirstLetter(data.district_name_en));
                            $('#dynamic_district_input').val(data.district_id);
                            $("#dynamic_upazilla").text(capitalizeFirstLetter(data.upazilla_name_en));
                            $('#dynamic_upazilla_input').val(data.upazilla_id);
                            $("#dynamic_address").text(data.address);
                            $('#dynamic_address_input').val(data.address);
                        },
                    });
                } else {
                    alert('danger');
                }
            });
        });
    </script>

    <!--  District To Upazilla Show Ajax -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('select[name="district_id"]').on('change', function () {
                var district_id = $(this).val();
                if (district_id) {
                    $.ajax({
                        url: '/district-upazilla/ajax/',
                        type: "GET",
                        data: { 'district_id': district_id },
                        dataType: "json",
                        success: function (data) {
                            var d = $('select[name="upazilla_id"]').empty();
                            $.each(data, function (key, value) {
                                $('select[name="upazilla_id"]').append('<option value="' + value.id + '">' + value.name_en + '</option>');
                            });
                        },
                    });
                } else {
                    alert('danger');
                }
            });
        });
    </script>

    <!-- create address ajax -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('#addressStore').on('click', function () {
                var division_id = $('#division_id').val();
                var district_id = $('#district_id').val();
                var upazilla_id = $('#upazilla_id').val();
                var address = $('#address').val();
                var is_default = $('#is_default').val();
                var status = $('#status').val();

                $.ajax({
                    url: '{!! route("address.ajax.store") !!}',
                    type: "POST",
                    data: {
                        _token: $("#csrf").val(),
                        division_id: division_id,
                        district_id: district_id,
                        upazilla_id: upazilla_id,
                        address: address,
                        is_default: is_default,
                        status: status,
                    },
                    dataType: 'json',
                    success: function (data) {
                        // console.log(data);
                        $('#address').val(null);

                        $('select[name="address_id"]').html('<option value="" selected="" disabled="">Select Address</option>');
                        $.each(data, function (key, value) {
                            $('select[name="address_id"]').append('<option value="' + value.id + '">' + value.address + '</option>');
                        });
                        $('select[name="division_id"]').html('<option value="" selected="" disabled="">Select Division</option>');
                        $('select[name="district_id"]').html('<option value="" selected="" disabled="">Select District</option>');
                        $('select[name="upazilla_id"]').html('<option value="" selected="" disabled="">Select Upazila</option>');

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
                            Swal.fire({
                                type: 'error',
                                title: data.error
                            })
                        }

                        // End Message
                        $('#Close').click();
                    }
                });
            });
        });
    </script>
    <script>
        var couponApplied = false; // Declare couponApplied variable
        $(document).ready(function () {
            // Your existing AJAX code for applying the coupon
            $('form[action="{{ route('apply-coupon') }}"]').submit(function (event) {
                event.preventDefault();
                if (couponApplied) {
                    const errorToast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    errorToast.fire({
                        title: 'Coupon Already Used'
                    });
                    return;
                }
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if (data.discount) {
                            let couponDiscount = parseInt(data.discount);

                            // Set coupon discount as an attribute for later use
                            $('#grand_total_set').attr('data-coupon-discount', couponDiscount);

                            // Set couponApplied to true after applying the coupon
                            couponApplied = true;

                            // Update the displayed coupon amount
                            $('#coupon_amount').text('৳' + couponDiscount);

                            // Update the total price after applying the coupon
                            updateTotalPrice();
                            showCouponInformation(data);
                        }
                        const successToast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Create error Toast mixin
                        const errorToast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Check if there is an error or success message in the data
                        if ($.isEmptyObject(data.error)) {
                            // Display success Toast
                            successToast.fire({
                                title: data.success
                            });
                        } else {
                            // Display error Toast
                            errorToast.fire({
                                title: data.error
                            });
                        }

                        // End Message
                        $('#Close').click();
                        // Handle other messages or actions if needed
                    },
                    error: function (xhr, status, error) {
                        // Handle errors if necessary
                        const errorToast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Check if there is an error message in the response
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            // Display error Toast with the error message
                            errorToast.fire({
                                title: xhr.responseJSON.error
                            });
                        } else {
                            // Display a generic error Toast
                            errorToast.fire({
                                title: 'Invalid Coupon Code'
                            });
                        }
                    }
                });
            });
            function showCouponInformation(data) {
                // Assuming you have an element to display the coupon information
                // Update the element with the coupon details
                $('#couponInformation').html('<div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">' +
                    '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Coupon Applied</span>' +
                    '<span class="text-success fw-medium">-৳<span>' + data.discount + '</span></span>' +
                    '</div>' +
                    '<input type="hidden" value="" name="shipping_charge" class="ship_amount" />' +
                    '<input type="hidden" value="" name="shipping_type" class="shipping_type" />' +
                    '<input type="hidden" value="" name="shipping_name" class="shipping_name" />');
            }

            // Your existing AJAX code for updating shipping information
            $('select[name="shipping_id"]').on('change', function () {
                var shipping_id = $(this).val();

                if (shipping_id) {
                    $.ajax({
                        url: "{{ url('/checkout/shipping/ajax') }}/" + shipping_id,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $('#ship_amount').text(data.shipping_charge);
                            $('.ship_amount').val(data.shipping_charge);
                            $('.shipping_name').val(data.name);
                            $('.shipping_type').val(data.type);

                            updateTotalPrice(); // Update the total price after selecting shipping
                        },
                    });
                } else {
                    // Reset the elements if no shipping option is selected
                    $('#ship_amount').text('0');
                    $('.ship_amount').val('0');
                    $('.shipping_name').val('');
                    $('.shipping_type').val('');

                    updateTotalPrice(); // Update the total price after resetting shipping
                }
            });
            function updateTotalPrice() {
                let couponDiscount = couponApplied ? parseInt($('#grand_total_set').attr('data-coupon-discount')) : 0;
                let shipping_price = parseInt($('#ship_amount').text());
                let product_price = parseInt($('#cartSubTotalShi').val());
                let grand_total_price = product_price + shipping_price - couponDiscount;
                const userPoints = parseInt('{{ auth()->user()->points }}');
                console.log(userPoints + ' ' + grand_total_price);

                $('#grand_total_set').text(grand_total_price);
                $('#grand_total').val(grand_total_price);

                // Show or hide wallet option
                if (userPoints >= grand_total_price) {
                    $('#wallet-option-wrapper').show();
                } else {
                    $('#wallet-option-wrapper').hide();
                }
            }



        });

    </script>

    <script>
        document.querySelectorAll('input[name="payment_option"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
                if (radio.checked) {
                    radio.closest('.payment-option').classList.add('active');
                }
            });
        });
    </script>




@endpush