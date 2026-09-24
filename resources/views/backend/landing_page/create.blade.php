@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<style>
    .section-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .section-card-header {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-card-body {
        padding: 20px;
    }
    .repeater-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 14px;
        margin-bottom: 12px;
        position: relative;
    }
    .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .product-preview-card {
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 16px;
        display: none;
    }
</style>

<section class="content-main">
    <div class="content-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="content-title card-title">Create Landing Page</h2>
            <p class="text-muted">Build a high-converting, fully customized landing page for any product</p>
        </div>
        <div>
            <a href="{{ route('landing.page.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa fa-exclamation-triangle me-1"></i> Please correct the following errors:</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('landing.page.store') }}" enctype="multipart/form-data" id="landingForm">
        @csrf

        <div class="row">
            <!-- Left 8 Columns: Main Content -->
            <div class="col-lg-8">

                <!-- 1. Product Selection -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-box-open text-primary"></i> 1. Select Product
                    </div>
                    <div class="section-card-body">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Target Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-control select-active form-select" required>
                                <option value="">— Select a product to link this landing page —</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                        #{{ $p->id }} — {{ $p->name_en }} (৳{{ $p->discount_price > 0 ? $p->discount_price : $p->regular_price }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Selecting a product automatically auto-populates title, prices, images, and description below.</small>
                        </div>

                        <!-- Product Preview Box -->
                        <div id="productPreviewBox" class="product-preview-card mt-3">
                            <div class="d-flex align-items-center gap-3">
                                <img id="previewThumb" src="" alt="Thumbnail" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <div class="flex-grow-1">
                                    <h6 id="previewTitle" class="mb-1 fw-bold text-dark"></h6>
                                    <div class="d-flex gap-3 text-secondary small">
                                        <span>Regular: <b id="previewRegPrice" class="text-dark"></b></span>
                                        <span>Sale: <b id="previewSalePrice" class="text-success"></b></span>
                                        <span>Stock: <b id="previewStock" class="text-primary"></b></span>
                                    </div>
                                    <div id="previewVarBadge" class="mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Hero & Basic Settings -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-desktop text-primary"></i> 2. Page & Hero Section
                    </div>
                    <div class="section-card-body">
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label fw-bold">Landing Page Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="page_title" class="form-control" placeholder="e.g. Premium Designer Saree" value="{{ old('title') }}" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold">URL Slug <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text small bg-light text-muted">/landing/</span>
                                    <input type="text" name="slug" id="page_slug" class="form-control" placeholder="designer-saree" value="{{ old('slug') }}">
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Hero Sub-title / Tagline</label>
                                <textarea name="sub_title" id="sub_title" class="form-control" rows="2" placeholder="সিম্পল, এলিগ্যান্ট এবং প্রিমিয়াম লুক। পার্টি, ওয়েডিং ও বিশেষ দিনের জন্য বাছাই করা ডিজাইন।">{{ old('sub_title') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Hero Tag (Top Badge)</label>
                                <input type="text" name="hero_tag" id="hero_tag" class="form-control" placeholder="e.g. ROH CLOTHS · PREMIUM COLLECTION" value="{{ old('hero_tag', 'ROH CLOTHS · PREMIUM COLLECTION') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Theme Primary Color</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="theme_color" id="theme_color" class="form-control form-control-color" value="{{ old('theme_color', '#275b57') }}" title="Choose theme color">
                                    <input type="text" id="theme_color_text" class="form-control font-monospace" value="{{ old('theme_color', '#275b57') }}" readonly style="width: 120px;">
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Video Embed URL (Optional)</label>
                                <input type="url" name="video_url" class="form-control" placeholder="https://www.youtube.com/embed/..." value="{{ old('video_url') }}">
                                <small class="text-muted">Provide a YouTube embed or video link if you want to feature a product video.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Overview Section -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-align-left text-primary"></i> 3. Product Full Overview
                    </div>
                    <div class="section-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Overview Section Title</label>
                                <input type="text" name="overview_title" class="form-control" value="{{ old('overview_title', 'Product Full Overview') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Overview Sub-title</label>
                                <input type="text" name="overview_sub_title" class="form-control" value="{{ old('overview_sub_title', 'প্রোডাক্ট সম্পর্কে দরকারি তথ্য এক জায়গায়।') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Overview Description Text</label>
                                <textarea name="overview_text" id="overview_text" class="form-control" rows="3" placeholder="প্রোডাক্টের আকর্ষণীয় বর্ণনা লিখুন...">{{ old('overview_text') }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Overview Bullet Points (1 point per line)</label>
                                <textarea name="overview_points" id="overview_points" class="form-control" rows="5" placeholder="Soft & comfortable premium fabric&#10;Elegant party and wedding design&#10;Neat finishing and premium appearance&#10;Available in multiple sizes&#10;Cash on Delivery available">{{ old('overview_points', "Soft & comfortable premium fabric\nElegant party and wedding design\nNeat finishing and premium appearance\nAvailable in multiple sizes\nCash on Delivery available") }}</textarea>
                                <small class="text-muted">Enter each bullet feature on a new line.</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Feature Highlights (Under Order Button, 1 per line)</label>
                                <textarea name="features" class="form-control" rows="3" placeholder="ক্যাশ অন ডেলিভারি&#10;সারাদেশে ডেলিভারি&#10;এক্সচেঞ্জ সুবিধা">{{ old('features', "ক্যাশ অন ডেলিভারি\nসারাদেশে ডেলিভারি\nএক্সচেঞ্জ সুবিধা") }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Customer Reviews -->
                <div class="section-card">
                    <div class="section-card-header d-flex justify-content-between align-items-center">
                        <div><i class="fa fa-star text-warning"></i> 4. Customer Reviews</div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addReviewBtn">
                            <i class="fa fa-plus me-1"></i> Add Review
                        </button>
                    </div>
                    <div class="section-card-body">
                        <div id="reviewsContainer">
                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger remove-btn remove-review"><i class="fa fa-times"></i></button>
                                <div class="row">
                                    <div class="col-md-5 mb-2">
                                        <label class="form-label small fw-bold">Customer Name</label>
                                        <input type="text" name="reviews[0][name]" class="form-control form-control-sm" value="সাদিয়া রহমান">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small fw-bold">City / Location</label>
                                        <input type="text" name="reviews[0][location]" class="form-control form-control-sm" value="ঢাকা">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label small fw-bold">Rating (Stars)</label>
                                        <select name="reviews[0][stars]" class="form-select form-select-sm">
                                            <option value="5" selected>5 Stars (★★★★★)</option>
                                            <option value="4">4 Stars (★★★★)</option>
                                            <option value="3">3 Stars (★★★)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Review Comment</label>
                                        <input type="text" name="reviews[0][comment]" class="form-control form-control-sm" value="কাপড়ের কোয়ালিটি খুব ভালো। ছবির মতোই সুন্দর পেয়েছি।">
                                    </div>
                                </div>
                            </div>

                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger remove-btn remove-review"><i class="fa fa-times"></i></button>
                                <div class="row">
                                    <div class="col-md-5 mb-2">
                                        <label class="form-label small fw-bold">Customer Name</label>
                                        <input type="text" name="reviews[1][name]" class="form-control form-control-sm" value="তানজিনা আক্তার">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small fw-bold">City / Location</label>
                                        <input type="text" name="reviews[1][location]" class="form-control form-control-sm" value="চট্টগ্রাম">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label small fw-bold">Rating (Stars)</label>
                                        <select name="reviews[1][stars]" class="form-select form-select-sm">
                                            <option value="5" selected>5 Stars (★★★★★)</option>
                                            <option value="4">4 Stars (★★★★)</option>
                                            <option value="3">3 Stars (★★★)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Review Comment</label>
                                        <input type="text" name="reviews[1][comment]" class="form-control form-control-sm" value="ডেলিভারি দ্রুত ছিল এবং প্যাকেজিংও ভালো ছিল।">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Frequently Asked Questions (FAQs) -->
                <div class="section-card">
                    <div class="section-card-header d-flex justify-content-between align-items-center">
                        <div><i class="fa fa-circle-question text-info"></i> 5. FAQs (Accordion)</div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addFaqBtn">
                            <i class="fa fa-plus me-1"></i> Add FAQ
                        </button>
                    </div>
                    <div class="section-card-body">
                        <div id="faqsContainer">
                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger remove-btn remove-faq"><i class="fa fa-times"></i></button>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Question</label>
                                    <input type="text" name="faqs[0][question]" class="form-control form-control-sm" value="ডেলিভারি করতে কতদিন সময় লাগে?">
                                </div>
                                <div>
                                    <label class="form-label small fw-bold">Answer</label>
                                    <textarea name="faqs[0][answer]" class="form-control form-control-sm" rows="2">ঢাকার ভিতরে সাধারণত ১–২ কর্মদিবস এবং ঢাকার বাইরে ২–৪ কর্মদিবস সময় লাগতে পারে।</textarea>
                                </div>
                            </div>

                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger remove-btn remove-faq"><i class="fa fa-times"></i></button>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Question</label>
                                    <input type="text" name="faqs[1][question]" class="form-control form-control-sm" value="ক্যাশ অন ডেলিভারি আছে?">
                                </div>
                                <div>
                                    <label class="form-label small fw-bold">Answer</label>
                                    <textarea name="faqs[1][answer]" class="form-control form-control-sm" rows="2">হ্যাঁ, সারা বাংলাদেশে ক্যাশ অন ডেলিভারি সুবিধা রয়েছে।</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 4 Columns: Pricing, Media, Delivery, Publish -->
            <div class="col-lg-4">

                <!-- Publish Card -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-save text-success"></i> Save & Publish
                    </div>
                    <div class="section-card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" checked>
                            <label class="form-check-label fw-bold" for="statusSwitch">Page Status (Active)</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold">
                            <i class="fa fa-check me-1"></i> Create Landing Page
                        </button>
                    </div>
                </div>

                <!-- Pricing & Stock Card -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-tag text-primary"></i> Pricing & Stock
                    </div>
                    <div class="section-card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Regular Price (৳)</label>
                            <input type="number" step="0.01" name="regular_price" id="regular_price" class="form-control" placeholder="3850.00" value="{{ old('regular_price') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sale / Offer Price (৳) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control" placeholder="3050.00" value="{{ old('sale_price') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Discount Text Badge</label>
                            <input type="text" name="discount_text" id="discount_text" class="form-control" placeholder="e.g. ২১% ছাড়" value="{{ old('discount_text') }}">
                            <small class="text-muted">Leave empty to auto-calculate discount %.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Stock Status Text</label>
                            <input type="text" name="stock_text" class="form-control" placeholder="বর্তমান স্টক: সীমিত" value="{{ old('stock_text', 'বর্তমান স্টক: সীমিত') }}">
                        </div>
                    </div>
                </div>

                <!-- Delivery Charges Card -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-truck text-primary"></i> Delivery Charges
                    </div>
                    <div class="section-card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Inside Dhaka (৳)</label>
                            <input type="number" step="0.01" name="delivery_charge_dhaka" class="form-control" value="{{ old('delivery_charge_dhaka', '70.00') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Outside Dhaka (৳)</label>
                            <input type="number" step="0.01" name="delivery_charge_outside" class="form-control" value="{{ old('delivery_charge_outside', '120.00') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Media Uploads Card -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-images text-primary"></i> Slider & Overview Images
                    </div>
                    <div class="section-card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hero Slider Images (Multiple)</label>
                            <input type="file" name="slider_images[]" id="slider_images" class="form-control" multiple accept="image/*">
                            <small class="text-muted">If none selected, product images are automatically used.</small>
                            <div id="sliderPreviewContainer" class="d-flex gap-2 flex-wrap mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Overview Big Image</label>
                            <input type="file" name="overview_image" id="overview_image" class="form-control" accept="image/*">
                            <small class="text-muted">Defaults to product thumbnail if left empty.</small>
                            <div id="overviewPreviewContainer" class="mt-2"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</section>

<script>
$(document).ready(function() {
    // Sync color input with text input
    $('#theme_color').on('input', function() {
        $('#theme_color_text').val($(this).val());
    });

    // Auto-generate slug from title
    $('#page_title').on('keyup change', function() {
        if (!$('#page_slug').data('customized')) {
            var slug = $(this).val().toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            $('#page_slug').val(slug);
        }
    });

    $('#page_slug').on('input', function() {
        $(this).data('customized', true);
    });

    // Handle Product AJAX load on change
    $('#product_id').on('change', function() {
        var productId = $(this).val();
        if (!productId) {
            $('#productPreviewBox').hide();
            return;
        }

        $.ajax({
            url: "{{ url('admin/landing-page/ajax/product-details') }}/" + productId,
            type: "GET",
            dataType: "json",
            beforeSend: function() {
                $('#productPreviewBox').show();
                $('#previewTitle').text('Loading product details...');
            },
            success: function(data) {
                if (data.success) {
                    $('#productPreviewBox').show();
                    $('#previewTitle').text(data.name_en);
                    $('#previewThumb').attr('src', data.thumbnail || '{{ asset("upload/no_image.jpg") }}');
                    $('#previewRegPrice').text('৳' + (data.regular_price || 0));
                    $('#previewSalePrice').text('৳' + (data.discount_price > 0 ? data.discount_price : data.regular_price));
                    $('#previewStock').text(data.stock_qty + ' in stock');

                    if (data.is_varient == 1 && data.attributes && data.attributes.length > 0) {
                        var attrHtml = '<span class="badge bg-primary">Variants: ';
                        var attrNames = data.attributes.map(function(a) { return a.name; }).join(', ');
                        attrHtml += attrNames + '</span>';
                        $('#previewVarBadge').html(attrHtml);
                    } else {
                        $('#previewVarBadge').html('<span class="badge bg-secondary">Single Product (No Variants)</span>');
                    }

                    // Auto-fill form fields if they are currently blank
                    if (!$('#page_title').val()) {
                        $('#page_title').val(data.name_en).trigger('change');
                    }
                    if (!$('#sub_title').val()) {
                        $('#sub_title').val(data.name_bn || data.name_en);
                    }
                    if (!$('#regular_price').val()) {
                        $('#regular_price').val(data.regular_price);
                    }
                    if (!$('#sale_price').val()) {
                        $('#sale_price').val(data.discount_price > 0 ? data.discount_price : data.regular_price);
                    }
                    if (!$('#overview_text').val() && data.description_en) {
                        $('#overview_text').val(data.description_en.substring(0, 300));
                    }

                    // Display product gallery in preview container
                    if (data.gallery && data.gallery.length > 0) {
                        var galHtml = '<div class="w-100 small text-muted mb-1">Product Images (used as fallback):</div>';
                        data.gallery.forEach(function(imgUrl) {
                            galHtml += '<img src="' + imgUrl + '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">';
                        });
                        $('#sliderPreviewContainer').html(galHtml);
                    }
                }
            },
            error: function() {
                $('#productPreviewBox').hide();
            }
        });
    });

    // Auto trigger if old value exists
    if ($('#product_id').val()) {
        $('#product_id').trigger('change');
    }

    // Dynamic Review Adder
    var reviewIndex = 2;
    $('#addReviewBtn').on('click', function() {
        var html = `
        <div class="repeater-item">
            <button type="button" class="btn btn-sm btn-danger remove-btn remove-review"><i class="fa fa-times"></i></button>
            <div class="row">
                <div class="col-md-5 mb-2">
                    <label class="form-label small fw-bold">Customer Name</label>
                    <input type="text" name="reviews[` + reviewIndex + `][name]" class="form-control form-control-sm" placeholder="নাম">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label small fw-bold">City / Location</label>
                    <input type="text" name="reviews[` + reviewIndex + `][location]" class="form-control form-control-sm" placeholder="লোকেশন">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label small fw-bold">Rating (Stars)</label>
                    <select name="reviews[` + reviewIndex + `][stars]" class="form-select form-select-sm">
                        <option value="5" selected>5 Stars (★★★★★)</option>
                        <option value="4">4 Stars (★★★★)</option>
                        <option value="3">3 Stars (★★★)</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Review Comment</label>
                    <input type="text" name="reviews[` + reviewIndex + `][comment]" class="form-control form-control-sm" placeholder="মন্তব্য">
                </div>
            </div>
        </div>`;
        $('#reviewsContainer').append(html);
        reviewIndex++;
    });

    $(document).on('click', '.remove-review', function() {
        $(this).closest('.repeater-item').remove();
    });

    // Dynamic FAQ Adder
    var faqIndex = 2;
    $('#addFaqBtn').on('click', function() {
        var html = `
        <div class="repeater-item">
            <button type="button" class="btn btn-sm btn-danger remove-btn remove-faq"><i class="fa fa-times"></i></button>
            <div class="mb-2">
                <label class="form-label small fw-bold">Question</label>
                <input type="text" name="faqs[` + faqIndex + `][question]" class="form-control form-control-sm" placeholder="প্রশ্ন লিখুন">
            </div>
            <div>
                <label class="form-label small fw-bold">Answer</label>
                <textarea name="faqs[` + faqIndex + `][answer]" class="form-control form-control-sm" rows="2" placeholder="উত্তর লিখুন"></textarea>
            </div>
        </div>`;
        $('#faqsContainer').append(html);
        faqIndex++;
    });

    $(document).on('click', '.remove-faq', function() {
        $(this).closest('.repeater-item').remove();
    });

    // Image preview for slider
    $('#slider_images').on('change', function() {
        $('#sliderPreviewContainer').empty();
        if (this.files) {
            Array.from(this.files).forEach(function(file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#sliderPreviewContainer').append('<img src="' + e.target.result + '" style="width: 55px; height: 55px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;">');
                }
                reader.readAsDataURL(file);
            });
        }
    });

    // Image preview for overview
    $('#overview_image').on('change', function() {
        $('#overviewPreviewContainer').empty();
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#overviewPreviewContainer').html('<img src="' + e.target.result + '" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
@endsection
