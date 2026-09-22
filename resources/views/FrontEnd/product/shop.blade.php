@extends('FrontEnd.master')
@section('title')
    All Products
@endsection
@section('content')
<style>
.shop-container {
    background: #f8f9fa;
    min-height: 100vh;
}

.hero-section {
    
    padding-top: 1rem;
    color: #921A40;
    margin-bottom: 1rem;
}

.filter-sidebar {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    padding: 1.5rem;
}

.filter-section {
    padding: 1rem 0;
}

.filter-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #fafafa;
    margin-bottom: 1rem;
}

.price-inputs {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.price-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.5rem;
}

.filter-btn {
    background: #fafafa;
    color: #921A40;
    border: none !important;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    width: 100%;
    transition: all 0.3s;
}

.filter-btn:hover {
    background: #6d1608;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    position: relative;
    padding-top: 100%;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-details {
    padding: 10px;
}

.product-name {
    font-size: 1rem;
    font-weight: 500;
    color: #1F2937;
    margin-bottom: 0.5rem;
}

.product-price {
    border: none;
    padding: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #921A40;
}

.price-original {
    color: #6B7280;
    text-decoration: line-through;
    font-size: 0.875rem;
}

.product-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn-buy, .btn-cart {
    flex: 1;
    padding: 0.2rem;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-buy {
    background: #921A40;
    color: white;
}

.btn-buy:hover {
    background: #6d1608;
}

.btn-cart {
    background: #E5E7EB;
    color: #921A40;
}

.btn-cart:hover {
    background: #D1D5DB;
}
.form-check-label{
    color: #fafafa;
}

.stock-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #10B981;
    color: white;
    padding: 0rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    line-height: 20px;
}

.pagination svg{
        width: 15px !important;
    }
    .pagination nav{
        display: flex;
    justify-content: space-between;
    width: 100%;
    align-items: end;
    margin-bottom: 20px;
    }

@media (max-width: 768px) {
    .filter-sidebar {
        margin-bottom: 2rem;
    }
    
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}
</style>

@php
    $min_price = 0;
    $max_price = 1000000000;
    if(isset($_GET['min_price']) && $_GET['min_price']>0){
        $min_price = $_GET['min_price'];
    }
    if(isset($_GET['max_price']) && $_GET['max_price']>0){
        $max_price = $_GET['max_price'];
    }
@endphp

<div class="shop-container">
    <div class="hero-section">
        <div class="container">
            <a href="">
                Home
            </a>
            <span>
                >
            </span>
            <a class="">
                {{session()->get('language') == 'bangla' ? 'সমস্ত পণ্য':'All Products'}}
</a>
          
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3">
                <div class="filter-sidebar " style="background-color: #921A40;">
                    <form action="">
                        <!-- Price Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                {{session()->get('language') == 'bangla' ? 'মূল্য দ্বারা ফিল্টার':'Filter by price'}}
                            </h3>
                            <div class="price-inputs">
                                <input type="number" name="min_price" class="price-input" 
                                    placeholder="Min" value="@isset($_GET['min_price']){{$_GET['min_price']}}@endisset">
                                <span>-</span>
                                <input type="number" name="max_price" class="price-input" 
                                    placeholder="Max" value="@isset($_GET['max_price']){{$_GET['max_price']}}@endisset">
                            </div>
                        </div>

                        <!-- Stock Filter -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                {{session()->get('language') == 'bangla' ? 'স্টক উপলব্ধতা':'Stock Availability'}}
                            </h3>
                            <div class="form-check">
                                <input type="checkbox" name="stock" value="1" class="form-check-input" 
                                    @isset($_GET['stock']){{$_GET['stock'] > 0 ? 'checked':''}}@endisset>
                                <label class="form-check-label">
                                    {{session()->get('language') == 'bangla' ? 'স্টক আছে':'In Stock'}}
                                    <span class="badge bg-secondary">{{count(getInstockPorducts())}}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                {{session()->get('language') == 'bangla' ? 'ক্যাটাগরি':'Categories'}}
                            </h3>
                            @foreach(get_categories() as $category)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="category[]" 
                                    value="{{$category->name_en}}"
                                    @isset($_GET['category']){{in_array($category->name_en, $_GET['category']) ? 'checked':''}}@endisset>
                                <label class="form-check-label">
                                    {{session()->get('language') == 'bangla' ? $category->name_bn : $category->name_en}}
                                    <span class="badge bg-secondary">{{count(get_category_products($category->slug))}}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <!-- Brands -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                {{session()->get('language') == 'bangla' ? 'ব্র্যান্ড':'Brands'}}
                            </h3>
                            @foreach(get_brands() as $brand)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="brand[]" 
                                    value="{{$brand->name_en}}"
                                    @isset($_GET['brand']){{in_array($brand->name_en, $_GET['brand']) ? 'checked':''}}@endisset>
                                <label class="form-check-label">
                                    {{session()->get('language') == 'bangla' ? $brand->name_bn : $brand->name_en}}
                                    <span class="badge bg-secondary">{{count(get_brand_products($brand->id))}}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <button type="submit" class="filter-btn">
                            {{session()->get('language') == 'bangla' ? 'ফিল্টার':'Apply Filters'}}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <div class="product-grid">
                    @foreach ($products as $product)
                        @php $data = calculateDiscount($product->id) @endphp
                        @if($data['discount'] >= $min_price && $data['discount'] <= $max_price)
                        <div class="product-card">
                            <div class="product-image">
                                <img src="{{ asset($product->product_thumbnail) }}" alt="{{ $product->name_en }}">
                                @if($product->stock_qty > 0)
                                    <span class="stock-badge">
                                        {{session()->get('language') == 'bangla' ? 'স্টকে আছে':'In Stock'}}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="product-details">
                                <h3 class="product-name">
                                    @if(session()->get('language') == 'bangla')
                                        {!! Str::limit($product->name_bn, 20) !!}
                                    @else
                                        {!! Str::limit($product->name_en, 20) !!}
                                    @endif
                                </h3>
                                
                                <div class="product-price">
                                    ৳{{ $data['discount'] }}
                                    <span class="price-original">৳{{ $product->regular_price }}</span>
                                </div>

                                @if($product->stock_qty > 0)
                                    <div class="product-actions">
                                        @if($product->is_varient == 1)
                                            <button type="button" class="btn-buy" 
                                                onclick="productView({{ $product->id }})" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#quickViewModal">
                                                {{session()->get('language') == 'bangla' ? 'এখুনি কিনুন':'Buy Now'}}
                                            </button>
                                            <button type="button" class="btn-cart"
                                                onclick="productView({{ $product->id }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#quickViewModal">
                                                {{session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন':'Add Cart'}}
                                            </button>
                                        @else
                                            <button type="button" class="btn-buy" 
                                                onclick="buyNow({{ $product->id }})">
                                                {{session()->get('language') == 'bangla' ? 'এখুনি কিনুন':'Buy Now'}}
                                            </button>
                                            <button type="button" class="btn-cart"
                                                onclick="addToCartDirect({{ $product->id }})">
                                                {{session()->get('language') == 'bangla' ? 'কার্টে যোগ করুন':'Add Cart'}}
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center mt-3">
                                        <span class="badge bg-danger">
                                            {{session()->get('language') == 'bangla' ? 'স্টক আউট':'Out of Stock'}}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                <div class="pagination">
                    {{ $products->onEachSide(5)->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
        function addToCart() {
            var total_attributes = parseInt($('#total_attributes').val());
            //alert(total_attributes);
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
                //$('#attribute_alert').html(checkAlertHtml);
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
            // console.log(min_qty);
            var p_qty = parseInt($('#stock_qty').val());
            // if(quantity > p_qty){
            //     $('#stock_alert').html(`<div class="attr-detail mb-5">
            // 								<div class="alert alert-danger d-flex align-items-center" role="alert">
            // 									<div>
            // 										<i class="fa fa-warning mr-10"></i> <span> Not enough stock.</span>
            // 									</div>
            // 								</div>
            // 							</div>`);
            //     return false;
            // }


            // alert(varient);

            var options = $('#choice_form').serializeArray();
            var jsonString = JSON.stringify(options);
            //console.log(options);

            // Start Message
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
                    // console.log(data);
                    miniCart();
                    $('#closeModel').click();

                    // Start Sweertaleart Message
                    if ($.isEmptyObject(data.error)) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1200
                        })

                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })

                        $('#qty').val(min_qty);
                        $('#pvarient').val('');

                        for (var i = 1; i <= total_attributes; i++) {
                            $('#attribute_check_' + i).val(0);
                        }

                    } else {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 1200
                        })

                        Toast.fire({
                            type: 'error',
                            title: data.error
                        })

                        $('#qty').val(min_qty);
                        $('#pvarient').val('');

                        for (var i = 1; i <= total_attributes; i++) {
                            $('#attribute_check_' + i).val(0);
                        }
                    }
                    // Start Sweertaleart Message
                    var buyNowCheck = $('#buyNowCheck').val();
                    //alert(buyNowCheck);
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
{{--    <script>--}}
{{--        $(document).ready(function () {--}}
{{--           var $rangeInput = $('#slider-range');--}}
{{--           var $lower_limit = $('#lower_limit');--}}
{{--           var $upper_limit = $('#upper_limit');--}}

{{--           $lower_limit.val($rangeInput.attr('min'));--}}
{{--           $upper_limit.val($rangeInput.attr('max'));--}}

{{--           $('#slider-range').on('input', function () {--}}
{{--               $lower_limit.val($rangeInput.val());--}}
{{--           });--}}

{{--           $upper_limit.on('input', function () {--}}
{{--               var $upperLimit = parseInt($upper_limit.val());--}}
{{--               var max_range = parseInt($rangeInput.attr('max'));--}}
{{--               if($upperLimit <= max_range){--}}
{{--                   $rangeInput.val($upperLimit);--}}
{{--               }--}}
{{--               else{--}}
{{--                   $rangeInput.val(max_range);--}}
{{--                   $upper_limit.val(max_range);--}}
{{--               }--}}
{{--           });--}}
{{--        });--}}
{{--    </script>--}}
@endpush