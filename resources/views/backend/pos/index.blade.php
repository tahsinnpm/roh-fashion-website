@extends('admin.admin_master')
@section('admin')
@push('css')
<style>
    .pos-wrapper {
        min-height: calc(100vh - 120px);
    }
    .pos-card-product {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
        background: #ffffff;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .pos-card-product:hover {
        border-color: #93b4f5;
        box-shadow: 0 12px 28px rgba(30, 64, 175, 0.13);
        transform: translateY(-3px);
    }
    .pos-product-img {
        width: 100%;
        height: 110px;
        object-fit: cover;
        background: #f8fafc;
    }
    .pos-product-body {
        padding: 8px 10px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .pos-product-title {
        font-size: 12px;
        font-weight: 600;
        line-height: 1.3;
        margin-bottom: 4px;
        color: #1e293b;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 31px;
    }
    .pos-product-price {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }
    .pos-product-stock {
        font-size: 10.5px;
    }
    .pos-cart-panel {
        position: sticky;
        top: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    }
    .pos-cart-table {
        max-height: 290px;
        overflow-y: auto;
    }
    .pos-cart-table table th {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background: #f8fafc;
    }
    .pos-cart-table table td {
        font-size: 12.5px;
        vertical-align: middle;
        padding: 6px 8px;
    }
    .qty-btn {
        width: 24px;
        height: 24px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #1e293b;
        cursor: pointer;
    }
    .qty-btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    .qty-input {
        width: 36px;
        height: 24px;
        text-align: center;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        margin: 0 2px;
        padding: 0;
    }
    .badge-variant {
        font-size: 10px;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        border-radius: 4px;
        padding: 1px 5px;
        display: inline-block;
    }
    .pos-variant-option { display: grid; grid-template-columns: minmax(0, 1fr) auto auto; align-items: center; gap: 14px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; }
    .pos-variant-option.is-unavailable { background: #f8fafc; opacity: .65; }
    .pos-variant-name { color: #172033; font-weight: 600; }
    .pos-variant-stock { color: #64748b; font-size: 12px; }
    .pos-variant-price { color: #1d4ed8; font-size: 16px; font-weight: 700; white-space: nowrap; }
    @media (max-width: 420px) { .pos-variant-option { grid-template-columns: minmax(0, 1fr) auto; gap: 8px; } .pos-variant-price { grid-column: 2; grid-row: 1; } .pos-variant-option button { grid-column: 1 / -1; width: 100%; } }
    .pos-wrapper { color: #172033; }
    .pos-card-product { min-height: 228px; border-radius: 12px; box-shadow: 0 2px 8px rgba(15, 23, 42, .04); }
    .pos-card-product:focus-visible { outline: 3px solid rgba(37, 99, 235, .35); outline-offset: 2px; }
    .pos-product-img { height: 148px; object-fit: contain; padding: 10px; background: linear-gradient(145deg, #f8faff, #f1f5fb); }
    .pos-product-body { min-height: 94px; padding: 11px 12px 12px; }
    .pos-product-title { font-size: 13px; line-height: 1.4; height: 36px; }
    .pos-product-price { color: #1647a6; font-size: 15px; letter-spacing: -.01em; }
    .pos-variant-count {
        display: inline-flex !important; align-items: center; gap: 5px; padding: 4px 8px !important;
        border: 1px solid #bfd2ff !important; border-radius: 999px !important;
        background: #eaf1ff !important; color: #174ea6 !important;
        font-size: 10px !important; font-weight: 700 !important; line-height: 1.2;
    }
    .pos-product-stock { display: inline-flex; align-items: center; border-radius: 999px; background: #eef8f1; padding: 4px 8px; font-weight: 600; }
    .pos-product-stock.text-danger { background: #fff0f0; }
    .pos-cart-panel { border-color: #dbe3ef; box-shadow: 0 8px 28px rgba(15, 23, 42, .08); }
    .pos-cart-table { max-height: min(38vh, 360px); }
    .pos-cart-table table { min-width: 100%; }
    .pos-cart-table table td { padding: 9px 8px; }
    .pos-wrapper .form-control, .pos-wrapper .form-select { min-height: 40px; border-color: #d8dfeb; }
    .pos-wrapper .card { border-radius: 12px; }
    @media (max-width: 991.98px) {
        .pos-cart-panel { position: static; }
        .pos-cart-table { max-height: 320px; }
    }
</style>
@endpush

<div class="content-main pos-wrapper">
    <div class="content-header mb-3">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div>
                <h3 class="content-title card-title mb-0">POS Terminal (পয়েন্ট অফ সেল)</h3>
                <p class="text-muted small mb-0">সরাসরি কাউন্টার সেল ও নিশ্চিত অর্ডার প্রক্রিয়া</p>
            </div>
            <div>
                <a href="{{ route('all_orders.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-list"></i> সকল অর্ডার
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- LEFT PANEL: PRODUCTS CATALOG & SEARCH --}}
        <div class="col-lg-7 col-xl-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                </span>
                                <input type="text" id="pos_search" class="form-control border-start-0" placeholder="প্রোডাক্ট নাম, SKU বা কোড লিখে খুঁজুন..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="pos_category" class="form-select">
                                <option value="">সকল ক্যাটাগরি</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" id="pos_reset_filter" class="btn btn-outline-secondary w-100" title="রিসেট">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="row g-2" id="pos_product_grid">
                        @foreach($products as $product)
                            @php
                                $finalPrice = $product->storefrontPrice();
                                $hasVariants = $product->is_varient == 1 && $product->stocks->count() > 0;
                            @endphp
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                <div class="pos-card-product" role="button" tabindex="0" onclick="handleProductClick({{ $product->id }})" onkeydown="handleProductCardKey(event, {{ $product->id }})">
                                    <img src="{{ $product->product_thumbnail ? asset($product->product_thumbnail) : asset('upload/no_image.jpg') }}" class="pos-product-img" alt="{{ $product->name_en }}" loading="lazy">
                                    <div class="pos-product-body">
                                        <div class="pos-product-title" title="{{ $product->name_en }}">
                                            {{ $product->name_en }}
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-baseline justify-content-between">
                                                <span class="pos-product-price">৳{{ number_format($finalPrice, 0) }}</span>
                                                @if($finalPrice < (float) $product->regular_price)
                                                    <span class="text-muted text-decoration-line-through small" style="font-size: 10px;">৳{{ number_format($product->regular_price, 0) }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-1">
                                                @if($hasVariants)
                                                    <span class="pos-variant-count"><i class="fa-solid fa-layer-group" aria-hidden="true"></i>{{ $product->stocks->count() }} ভ্যারিয়েন্ট</span>
                                                @else
                                                    <span class="pos-product-stock {{ $product->stock_qty > 0 ? 'text-success' : 'text-danger' }}">
                                                        স্টক: {{ $product->stock_qty }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="pos_empty_state" class="text-center py-5 d-none">
                        <i class="fa-solid fa-box-open text-muted fa-3x mb-2"></i>
                        <h6 class="text-muted">কোনো প্রোডাক্ট পাওয়া যায়নি</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: CART & CHECKOUT --}}
        <div class="col-lg-5 col-xl-4">
            <div class="pos-cart-panel p-3">
                {{-- Customer Info Section --}}
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0 fw-bold"><i class="fa-solid fa-user me-1 text-primary"></i> কাস্টমার তথ্য</h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle py-0 px-2" type="button" id="customerSearchDrop" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 11px;">
                                <i class="fa-solid fa-search"></i> সিলেক্ট কাস্টমার
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow p-2" style="min-width: 250px;">
                                <li>
                                    <input type="text" id="pos_customer_quick_search" class="form-control form-control-sm mb-2" placeholder="ফোন নম্বর বা নাম লিখে খুঁজুন...">
                                </li>
                                <div id="pos_customer_search_results" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($customers as $c)
                                        <li class="dropdown-item py-1 px-2 cursor-pointer small border-bottom" onclick="selectCustomer('{{ rawurlencode($c->name) }}', '{{ rawurlencode($c->phone ?? '') }}', '{{ rawurlencode($c->address ?? '') }}')">
                                            <strong>{{ $c->name }}</strong><br>
                                            <span class="text-muted" style="font-size: 10px;">{{ $c->phone }}</span>
                                        </li>
                                    @endforeach
                                </div>
                            </ul>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">কাস্টমার নাম (ঐচ্ছিক)</label>
                            <input type="text" id="pos_cust_name" class="form-control form-control-sm" placeholder="নাম (ঐচ্ছিক)" value="">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">ফোন নম্বর</label>
                            <input type="text" id="pos_cust_phone" class="form-control form-control-sm" placeholder="01XXXXXXXXX">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted mb-1">ঠিকানা</label>
                            <input type="text" id="pos_cust_address" class="form-control form-control-sm" placeholder="ঠিকানা (ঐচ্ছিক)" value="">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted mb-1" for="pos_shipping_id">ডেলিভারি এলাকা / শিপিং চার্জ</label>
                            <select id="pos_shipping_id" class="form-select form-select-sm">
                                <option value="" data-charge="0">পিকআপ / ডেলিভারি চার্জ নেই</option>
                                @foreach($shippings as $shipping)
                                    <option value="{{ $shipping->id }}" data-charge="{{ number_format((float) $shipping->shipping_charge, 2, '.', '') }}">
                                        {{ $shipping->type == 1 ? 'ঢাকার ভিতরে' : ($shipping->type == 2 ? 'ঢাকার বাইরে' : $shipping->name) }} — ৳{{ number_format((float) $shipping->shipping_charge, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Cart Items Table --}}
                <div class="pos-cart-table border rounded mb-3">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>আইটেম</th>
                                <th class="text-center" style="width: 105px;">পরিমাণ</th>
                                <th class="text-end" style="width: 70px;">মূল্য</th>
                                <th class="text-end" style="width: 30px;"></th>
                            </tr>
                        </thead>
                        <tbody id="pos_cart_items">
                            {{-- Dynamically filled via JS --}}
                        </tbody>
                    </table>
                    <div id="pos_cart_empty" class="text-center py-4 text-muted">
                        <i class="fa-solid fa-cart-shopping fa-2x mb-1 text-secondary opacity-50"></i>
                        <p class="small mb-0">কার্ট খালি! প্রোডাক্টে ক্লিক করে যোগ করুন।</p>
                    </div>
                </div>

                {{-- Calculation Summary --}}
                <div class="bg-light p-3 rounded mb-3">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>সাব টোটাল (Sub Total):</span>
                        <strong class="fs-6">৳ <span id="pos_subtotal">0.00</span></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span>শিপিং চার্জ:</span>
                        <strong>৳ <span id="pos_shipping_charge">0.00</span></strong>
                    </div>

                    {{-- Manual Discount Input (User Request) --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-danger">ম্যানুয়াল ডিসকাউন্ট (৳):</span>
                        <div class="input-group input-group-sm" style="width: 130px;">
                            <span class="input-group-text bg-white">৳</span>
                            <input type="number" id="pos_discount_input" class="form-control text-end fw-bold" min="0" step="any" value="0" placeholder="0">
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="fw-bold fs-6">সর্বমোট মূল্য (Total):</span>
                        <strong class="fs-4 text-primary">৳ <span id="pos_grand_total">0.00</span></strong>
                    </div>
                </div>

                {{-- Payment Options & Tender --}}
                <div class="mb-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label small text-muted mb-1">পেমেন্ট মেথড</label>
                            <select id="pos_payment_method" class="form-select form-select-sm" aria-label="Payment method">
                                <option value="cod" selected>Cash on Delivery</option>
                            </select>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">পণ্য ডেলিভারির সময় পেমেন্ট নেওয়া হবে।</p>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm w-30" onclick="clearCart()">
                        <i class="fa-solid fa-trash-can"></i> ক্লিয়ার
                    </button>
                    <button type="button" id="pos_submit_btn" class="btn btn-success btn-lg flex-grow-1 fw-bold" onclick="submitPosOrder()">
                        <i class="fa-solid fa-circle-check me-1"></i> অর্ডার কনফার্ম করুন
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Variant Selector --}}
<div class="modal fade" id="posVariantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold" id="posVariantModalTitle">ভ্যারিয়েন্ট নির্বাচন করুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 mb-3 pb-2 border-bottom">
                    <img id="posVariantModalImg" src="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;" alt="">
                    <div>
                        <h6 id="posVariantModalName" class="mb-1 fw-bold"></h6>
                        <div id="posVariantModalPrice" class="text-primary fw-bold"></div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted">উপলব্ধ ভ্যারিয়েন্ট তালিকা:</label>
                    <div id="posVariantList" class="d-flex flex-column gap-2">
                        {{-- Injected dynamically --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: order confirmation --}}
<div class="modal fade" id="posSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow text-center p-3">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-success mb-2">
                    <i class="fa-solid fa-circle-check fa-4x"></i>
                </div>
                <h5 class="fw-bold mb-1">অর্ডার সম্পন্ন হয়েছে!</h5>
                <p class="text-muted small mb-2">ইনভয়েস: <strong id="successInvoiceNo"></strong></p>
                <span class="badge bg-success">Confirmed · Cash on Delivery</span>
            </div>
        </div>
    </div>
</div>

@push('footer-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global Cart State
    let cart = [];
    const initialProducts = @json($products->items());

    function posPrice(basePrice, discount, discountType) {
        basePrice = Number(basePrice) || 0;
        discount = Number(discount) || 0;
        if (discount <= 0) return basePrice;
        if (Number(discountType) === 1) return discount < basePrice ? discount : basePrice;
        if (Number(discountType) === 2) return Math.max(0, Math.round(basePrice - (basePrice * discount / 100)));
        return basePrice;
    }

    // The first render comes from Eloquent models; AJAX search returns POS-ready objects.
    // Normalize both to the same shape so the first click has a real name, price and stock.
    let productsCache = initialProducts.map(product => {
        const stocks = product.stocks || [];
        const pricedStocks = stocks.filter(stock => Number(stock.price) > 0);
        const inStockPrices = pricedStocks.filter(stock => Number(stock.qty) > 0).map(stock => Number(stock.price));
        const allVariantPrices = pricedStocks.map(stock => Number(stock.price));
        const minimumVariantPrice = inStockPrices.length ? Math.min(...inStockPrices) : (allVariantPrices.length ? Math.min(...allVariantPrices) : null);
        const storefrontBasePrice = minimumVariantPrice || Number(product.regular_price) || 0;
        const storefrontPrice = Number(product.is_varient) === 1 && minimumVariantPrice ? minimumVariantPrice : posPrice(storefrontBasePrice, product.discount_price, product.discount_type);
        const variants = stocks.map(stock => {
            const regularPrice = Number(stock.price) || storefrontPrice;
            return {
                id: Number(stock.id),
                name: stock.varient || '',
                regular_price: Number(stock.price) || Number(product.regular_price) || 0,
                price: regularPrice,
                qty: Number(stock.qty) || 0
            };
        });
        const regularPrice = Number(product.regular_price) || 0;
        return {
            id: Number(product.id),
            name: product.name_en || product.name_bn || 'Product',
            code: product.product_code || '',
            thumbnail: product.product_thumbnail ? `{{ asset('') }}${product.product_thumbnail}` : `{{ asset('upload/no_image.jpg') }}`,
            regular_price: regularPrice,
            price: storefrontPrice,
            has_discount: storefrontPrice < regularPrice,
            stock_qty: Number(product.stock_qty) || 0,
            is_varient: Number(product.is_varient) || 0,
            variants: variants
        };
    });

    // Search and Filter Handlers
    let searchDebounceTimer;
    document.getElementById('pos_search').addEventListener('input', function() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(fetchFilteredProducts, 250);
    });
    document.getElementById('pos_category').addEventListener('change', fetchFilteredProducts);
    document.getElementById('pos_shipping_id').addEventListener('change', renderCart);
    document.getElementById('pos_reset_filter').addEventListener('click', function() {
        document.getElementById('pos_search').value = '';
        document.getElementById('pos_category').value = '';
        fetchFilteredProducts();
    });

    // Customer Search Autocomplete
    document.getElementById('pos_customer_quick_search').addEventListener('input', function() {
        const term = this.value.trim();
        if (term.length < 1) return;
        fetch(`{{ route('pos.searchCustomer') }}?term=${encodeURIComponent(term)}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('pos_customer_search_results');
                if (data.length === 0) {
                    container.innerHTML = '<li class="dropdown-item small text-muted text-center py-2">কোনো কাস্টমার পাওয়া যায়নি</li>';
                    return;
                }
                container.innerHTML = data.map(c => `
                    <li class="dropdown-item py-1 px-2 cursor-pointer small border-bottom" onclick="selectCustomer('${encodeURIComponent(c.name)}', '${encodeURIComponent(c.phone || '')}', '${encodeURIComponent(c.address || '')}')">
                        <strong>${escapeHtml(c.name)}</strong><br>
                        <span class="text-muted" style="font-size: 10px;">${escapeHtml(c.phone)}</span>
                    </li>
                `).join('');
            });
    });

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[character]);
    }

    function selectCustomer(name, phone, address) {
        name = decodeURIComponent(name);
        phone = decodeURIComponent(phone);
        address = decodeURIComponent(address);
        document.getElementById('pos_cust_name').value = name || '';
        document.getElementById('pos_cust_phone').value = phone || '';
        document.getElementById('pos_cust_address').value = address || '';
        const dropdownEl = document.getElementById('customerSearchDrop');
        const dropdownInstance = bootstrap.Dropdown.getInstance(dropdownEl);
        if (dropdownInstance) dropdownInstance.hide();
    }

    // Fetch Products via AJAX
    function fetchFilteredProducts() {
        const search = document.getElementById('pos_search').value.trim();
        const category_id = document.getElementById('pos_category').value;

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (category_id) params.append('category_id', category_id);

        fetch(`{{ route('pos.searchProducts') }}?${params.toString()}`)
            .then(res => res.json())
            .then(products => {
                productsCache = products;
                renderProductGrid(products);
            });
    }

    function renderProductGrid(products) {
        const grid = document.getElementById('pos_product_grid');
        const empty = document.getElementById('pos_empty_state');

        if (!products || products.length === 0) {
            grid.innerHTML = '';
            empty.classList.remove('d-none');
            return;
        }

        empty.classList.add('d-none');
        grid.innerHTML = products.map(p => {
            const hasVariants = p.is_varient == 1 && p.variants && p.variants.length > 0;
            return `
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="pos-card-product" role="button" tabindex="0" onclick="handleProductClick(${p.id})" onkeydown="handleProductCardKey(event, ${p.id})">
                        <img src="${escapeHtml(p.thumbnail)}" class="pos-product-img" alt="${escapeHtml(p.name)}" loading="lazy">
                        <div class="pos-product-body">
                            <div class="pos-product-title" title="${escapeHtml(p.name)}">
                                ${escapeHtml(p.name)}
                            </div>
                            <div>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <span class="pos-product-price">৳${Math.round(p.price)}</span>
                                    ${p.has_discount ? `<span class="text-muted text-decoration-line-through small" style="font-size: 10px;">৳${Math.round(p.regular_price)}</span>` : ''}
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    ${hasVariants ? 
                                        `<span class="pos-variant-count"><i class="fa-solid fa-layer-group" aria-hidden="true"></i>${p.variants.length} ভ্যারিয়েন্ট</span>` :
                                        `<span class="pos-product-stock ${p.stock_qty > 0 ? 'text-success' : 'text-danger'}">স্টক: ${p.stock_qty}</span>`
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Handle Product Card Click
    function handleProductCardKey(event, productId) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            handleProductClick(productId);
        }
    }

    function handleProductClick(productId) {
        const product = productsCache.find(p => p.id == productId);
        if (!product) return;

        // If product has variants, open modal
        if (product.is_varient == 1 && product.variants && product.variants.length > 0) {
            openVariantModal(product);
        } else {
            // Check stock
            if (product.stock_qty <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'স্টক শেষ!',
                    text: 'এই প্রোডাক্টটির কোনো স্টক অবশিষ্ট নেই।',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            addToCart({
                product_id: product.id,
                variant_id: null,
                name: product.name,
                variant_name: null,
                price: product.price,
                stock: product.stock_qty,
                qty: 1
            });
        }
    }

    // Open Variant Selection Modal
    function openVariantModal(product) {
        document.getElementById('posVariantModalImg').src = product.thumbnail;
        document.getElementById('posVariantModalName').textContent = product.name;
        document.getElementById('posVariantModalPrice').textContent = `৳${Math.round(product.price)}`;

        const container = document.getElementById('posVariantList');
        container.innerHTML = product.variants.map(v => {
            const inStock = v.qty > 0;
            const variantPrice = Number(v.price);
            return `
                <div class="pos-variant-option ${inStock ? '' : 'is-unavailable'}">
                    <div>
                        <div class="pos-variant-name">${escapeHtml(v.name || 'Default')}</div>
                        <div class="pos-variant-stock">স্টক: ${Number(v.qty) || 0} পিস</div>
                    </div>
                    <strong class="pos-variant-price">৳${Number.isFinite(variantPrice) ? variantPrice.toFixed(2) : '—'}</strong>
                    <button type="button" class="btn btn-sm ${inStock ? 'btn-primary' : 'btn-secondary'} px-3"
                        ${inStock && Number.isFinite(variantPrice) ? '' : 'disabled'}
                        onclick="selectVariant(${product.id}, ${v.id}, '${encodeURIComponent(v.name || 'Default')}', ${Number.isFinite(variantPrice) ? variantPrice : 0}, ${Number(v.qty) || 0})">
                        ${inStock ? '+ কার্টে যোগ' : 'স্টক শেষ'}
                    </button>
                </div>
            `;
        }).join('');

        const modal = new bootstrap.Modal(document.getElementById('posVariantModal'));
        modal.show();
    }

    function selectVariant(productId, variantId, variantName, price, stock) {
        const product = productsCache.find(p => p.id == productId);
        if (!product) return;
        variantName = decodeURIComponent(variantName);

        addToCart({
            product_id: product.id,
            variant_id: variantId,
            name: product.name,
            variant_name: variantName,
            price: price,
            stock: stock,
            qty: 1
        });

        const modalEl = document.getElementById('posVariantModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
    }

    // Add to Cart Logic
    function addToCart(item) {
        item.price = Number(item.price);
        item.qty = Number(item.qty);
        item.stock = Number(item.stock);
        if (!Number.isFinite(item.price) || !Number.isFinite(item.qty) || !Number.isFinite(item.stock)) {
            Swal.fire({ icon: 'error', title: 'Price unavailable', text: 'This product has incomplete price or stock information. Refresh the product list and try again.' });
            return;
        }
        // Check if item already exists in cart
        const existing = cart.find(c => c.product_id === item.product_id && c.variant_id === item.variant_id);
        if (existing) {
            if (existing.qty + 1 > item.stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'পর্যাপ্ত স্টক নেই!',
                    text: `সর্বোচ্চ স্টক পরিমাণ: ${item.stock}`,
                    timer: 1800,
                    showConfirmButton: false
                });
                return;
            }
            existing.qty += 1;
        } else {
            cart.push({ ...item });
        }

        renderCart();
    }

    function updateCartQty(index, newQty) {
        if (newQty <= 0) {
            removeCartItem(index);
            return;
        }
        if (newQty > cart[index].stock) {
            Swal.fire({
                icon: 'warning',
                title: 'পর্যাপ্ত স্টক নেই!',
                text: `সর্বোচ্চ স্টক পরিমাণ: ${cart[index].stock}`,
                timer: 1800,
                showConfirmButton: false
            });
            return;
        }
        cart[index].qty = newQty;
        renderCart();
    }

    function removeCartItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        if (cart.length === 0) return;
        cart = [];
        document.getElementById('pos_discount_input').value = 0;
        renderCart();
    }

    // Render Cart Table & Totals
    function renderCart() {
        const tbody = document.getElementById('pos_cart_items');
        const emptyState = document.getElementById('pos_cart_empty');
        const shippingSelect = document.getElementById('pos_shipping_id');
        const shippingCharge = Number(shippingSelect.selectedOptions[0].dataset.charge) || 0;
        document.getElementById('pos_shipping_charge').textContent = shippingCharge.toFixed(2);

        if (cart.length === 0) {
            tbody.innerHTML = '';
            emptyState.classList.remove('d-none');
            document.getElementById('pos_subtotal').textContent = '0.00';
            document.getElementById('pos_grand_total').textContent = '0.00';
            return;
        }

        emptyState.classList.add('d-none');
        let subtotal = 0;

        tbody.innerHTML = cart.map((item, index) => {
            const itemTotal = Number(item.price) * Number(item.qty);
            subtotal += itemTotal;
            return `
                <tr>
                    <td>
                        <strong class="d-block text-truncate" style="max-width: 140px;" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</strong>
                        ${item.variant_name ? `<span class="badge-variant">${escapeHtml(item.variant_name)}</span>` : ''}
                        <div class="text-muted small" style="font-size: 10.5px;">৳${Math.round(item.price)}</div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <button type="button" class="qty-btn" onclick="updateCartQty(${index}, ${item.qty - 1})">-</button>
                            <input type="text" class="qty-input" value="${item.qty}" readonly>
                            <button type="button" class="qty-btn" onclick="updateCartQty(${index}, ${item.qty + 1})">+</button>
                        </div>
                    </td>
                    <td class="text-end fw-bold">৳${Math.round(itemTotal)}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-link text-danger p-0" onclick="removeCartItem(${index})" title="মুছে ফেলুন">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        // Manual Discount Handling
        const discountInput = Math.max(0, Number(document.getElementById('pos_discount_input').value) || 0);
        const appliedDiscount = Math.min(subtotal, discountInput);
        const grandTotal = Math.max(0, subtotal - appliedDiscount + shippingCharge);

        document.getElementById('pos_subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('pos_grand_total').textContent = grandTotal.toFixed(2);

    }

    // Discount & Paid Inputs Listeners
    document.getElementById('pos_discount_input').addEventListener('input', function() {
        renderCart();
    });

    // Submit POS Order
    function submitPosOrder() {
        if (cart.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'কার্ট খালি!',
                text: 'দয়া করে অন্তত একটি প্রোডাক্ট কার্টে যোগ করুন।',
            });
            return;
        }

        const customer_name = document.getElementById('pos_cust_name').value.trim();
        const customer_phone = document.getElementById('pos_cust_phone').value.trim();
        const customer_address = document.getElementById('pos_cust_address').value.trim();
        const discount = parseFloat(document.getElementById('pos_discount_input').value) || 0;
        const payment_method = document.getElementById('pos_payment_method').value;
        const submitBtn = document.getElementById('pos_submit_btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> প্রসেসিং হচ্ছে...';

        const payload = {
            _token: '{{ csrf_token() }}',
            customer_name: customer_name,
            customer_phone: customer_phone,
            customer_address: customer_address,
            shipping_id: document.getElementById('pos_shipping_id').value || null,
            discount: discount,
            payment_method: payment_method,
            items: cart
        };

        fetch('{{ route('pos.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> অর্ডার কনফার্ম করুন';

            if (data.success) {
                // Show a simple confirmation; closing it prepares the terminal for the next sale.
                document.getElementById('successInvoiceNo').textContent = data.invoice_no;

                const successModal = new bootstrap.Modal(document.getElementById('posSuccessModal'));
                successModal.show();

                // Clear Cart
                cart = [];
                renderCart();
                document.getElementById('pos_discount_input').value = 0;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ব্যর্থ হয়েছে',
                    text: data.message || 'অর্ডার সম্পূর্ণ করা যায়নি।'
                });
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> অর্ডার কনফার্ম করুন';
            Swal.fire({
                icon: 'error',
                title: 'ত্রুটি',
                text: 'সার্ভারে সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।'
            });
        });
    }

    function resetPosForNewSale() {
        document.getElementById('pos_cust_name').value = '';
        document.getElementById('pos_cust_phone').value = '';
        document.getElementById('pos_cust_address').value = '';
        document.getElementById('pos_shipping_id').value = '';
        renderCart();
        fetchFilteredProducts();
    }

    document.getElementById('posSuccessModal').addEventListener('hidden.bs.modal', resetPosForNewSale);
</script>
@endpush
@endsection
