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
</style>

<section class="content-main">
    <div class="content-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="content-title card-title">Edit Landing Page: {{ $landingPage->title }}</h2>
            <p class="text-muted">Update landing page configuration, pricing, or media</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('landing.dynamic', $landingPage->slug) }}" target="_blank" class="btn btn-outline-info">
                <i class="fa fa-external-link-alt me-1"></i> Preview Live
            </a>
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

    <form method="POST" action="{{ route('landing.page.update', $landingPage->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Left 8 Columns -->
            <div class="col-lg-8">

                <!-- 1. Product Selection -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-box-open text-primary"></i> 1. Linked Product
                    </div>
                    <div class="section-card-body">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Target Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-control select-active form-select" required>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ $landingPage->product_id == $p->id ? 'selected' : '' }}>
                                        #{{ $p->id }} — {{ $p->name_en }} (৳{{ $p->discount_price > 0 ? $p->discount_price : $p->regular_price }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 2. Page & Hero Section -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-desktop text-primary"></i> 2. Page & Hero Section
                    </div>
                    <div class="section-card-body">
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label fw-bold">Landing Page Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="page_title" class="form-control" value="{{ old('title', $landingPage->title) }}" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold">URL Slug <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text small bg-light text-muted">/landing/</span>
                                    <input type="text" name="slug" id="page_slug" class="form-control" value="{{ old('slug', $landingPage->slug) }}" required>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Hero Sub-title / Tagline</label>
                                <textarea name="sub_title" class="form-control" rows="2">{{ old('sub_title', $landingPage->sub_title) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Hero Tag (Top Badge)</label>
                                <input type="text" name="hero_tag" class="form-control" value="{{ old('hero_tag', $landingPage->hero_tag) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Theme Primary Color</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="theme_color" id="theme_color" class="form-control form-control-color" value="{{ old('theme_color', $landingPage->theme_color ?? '#275b57') }}">
                                    <input type="text" id="theme_color_text" class="form-control font-monospace" value="{{ old('theme_color', $landingPage->theme_color ?? '#275b57') }}" readonly style="width: 120px;">
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Video Embed URL (Optional)</label>
                                <input type="url" name="video_url" class="form-control" value="{{ old('video_url', $landingPage->video_url) }}">
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
                                <input type="text" name="overview_title" class="form-control" value="{{ old('overview_title', $landingPage->overview_title) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Overview Sub-title</label>
                                <input type="text" name="overview_sub_title" class="form-control" value="{{ old('overview_sub_title', $landingPage->overview_sub_title) }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Overview Description Text</label>
                                <textarea name="overview_text" class="form-control" rows="3">{{ old('overview_text', $landingPage->overview_text) }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Overview Bullet Points (1 point per line)</label>
                                @php
                                    $pointsStr = is_array($landingPage->overview_points) ? implode("\n", $landingPage->overview_points) : $landingPage->overview_points;
                                @endphp
                                <textarea name="overview_points" class="form-control" rows="5">{{ old('overview_points', $pointsStr) }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Feature Highlights (Under Order Button, 1 per line)</label>
                                @php
                                    $featuresStr = is_array($landingPage->features) ? implode("\n", $landingPage->features) : $landingPage->features;
                                @endphp
                                <textarea name="features" class="form-control" rows="3">{{ old('features', $featuresStr) }}</textarea>
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
                            @php
                                $reviews = is_array($landingPage->reviews) ? $landingPage->reviews : [];
                            @endphp
                            @forelse($reviews as $rIndex => $rev)
                                <div class="repeater-item">
                                    <button type="button" class="btn btn-sm btn-danger remove-btn remove-review"><i class="fa fa-times"></i></button>
                                    <div class="row">
                                        <div class="col-md-5 mb-2">
                                            <label class="form-label small fw-bold">Customer Name</label>
                                            <input type="text" name="reviews[{{ $rIndex }}][name]" class="form-control form-control-sm" value="{{ $rev['name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small fw-bold">City / Location</label>
                                            <input type="text" name="reviews[{{ $rIndex }}][location]" class="form-control form-control-sm" value="{{ $rev['location'] ?? '' }}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label small fw-bold">Rating (Stars)</label>
                                            <select name="reviews[{{ $rIndex }}][stars]" class="form-select form-select-sm">
                                                <option value="5" {{ ($rev['stars'] ?? 5) == 5 ? 'selected' : '' }}>5 Stars (★★★★★)</option>
                                                <option value="4" {{ ($rev['stars'] ?? 5) == 4 ? 'selected' : '' }}>4 Stars (★★★★)</option>
                                                <option value="3" {{ ($rev['stars'] ?? 5) == 3 ? 'selected' : '' }}>3 Stars (★★★)</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Review Comment</label>
                                            <input type="text" name="reviews[{{ $rIndex }}][comment]" class="form-control form-control-sm" value="{{ $rev['comment'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small">No reviews added yet. Click "+ Add Review" above.</p>
                            @endforelse
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
                            @php
                                $faqs = is_array($landingPage->faqs) ? $landingPage->faqs : [];
                            @endphp
                            @forelse($faqs as $fIndex => $faq)
                                <div class="repeater-item">
                                    <button type="button" class="btn btn-sm btn-danger remove-btn remove-faq"><i class="fa fa-times"></i></button>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Question</label>
                                        <input type="text" name="faqs[{{ $fIndex }}][question]" class="form-control form-control-sm" value="{{ $faq['question'] ?? '' }}">
                                    </div>
                                    <div>
                                        <label class="form-label small fw-bold">Answer</label>
                                        <textarea name="faqs[{{ $fIndex }}][answer]" class="form-control form-control-sm" rows="2">{{ $faq['answer'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small">No FAQs added yet. Click "+ Add FAQ" above.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 4 Columns -->
            <div class="col-lg-4">

                <!-- Publish Card -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-save text-success"></i> Save & Update
                    </div>
                    <div class="section-card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ $landingPage->status == 1 ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="statusSwitch">Page Status (Active)</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold">
                            <i class="fa fa-save me-1"></i> Update Landing Page
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
                            <input type="number" step="0.01" name="regular_price" class="form-control" value="{{ old('regular_price', $landingPage->regular_price) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sale / Offer Price (৳) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price', $landingPage->sale_price) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Discount Text Badge</label>
                            <input type="text" name="discount_text" class="form-control" value="{{ old('discount_text', $landingPage->discount_text) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Stock Status Text</label>
                            <input type="text" name="stock_text" class="form-control" value="{{ old('stock_text', $landingPage->stock_text) }}">
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
                            <input type="number" step="0.01" name="delivery_charge_dhaka" class="form-control" value="{{ old('delivery_charge_dhaka', $landingPage->delivery_charge_dhaka) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Outside Dhaka (৳)</label>
                            <input type="number" step="0.01" name="delivery_charge_outside" class="form-control" value="{{ old('delivery_charge_outside', $landingPage->delivery_charge_outside) }}" required>
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
                            <label class="form-label fw-bold">Hero Slider Images</label>
                            @if(!empty($landingPage->slider_images) && is_array($landingPage->slider_images))
                                <div class="d-flex gap-2 flex-wrap mb-2">
                                    @foreach($landingPage->slider_images as $sImg)
                                        <img src="{{ asset($sImg) }}" style="width: 55px; height: 55px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;">
                                    @endforeach
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="keep_existing_slider" value="1" id="keepSlider" checked>
                                    <label class="form-check-label small" for="keepSlider">Keep existing slider images when uploading new ones</label>
                                </div>
                            @endif
                            <input type="file" name="slider_images[]" class="form-control" multiple accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Overview Big Image</label>
                            @if($landingPage->overview_image)
                                <div class="mb-2">
                                    <img src="{{ asset($landingPage->overview_image) }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                                </div>
                            @endif
                            <input type="file" name="overview_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</section>

<script>
$(document).ready(function() {
    $('#theme_color').on('input', function() {
        $('#theme_color_text').val($(this).val());
    });

    var reviewIndex = {{ count($reviews ?? []) + 1 }};
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

    var faqIndex = {{ count($faqs ?? []) + 1 }};
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
});
</script>
@endsection
