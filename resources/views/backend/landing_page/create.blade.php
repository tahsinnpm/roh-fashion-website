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
    .color-preview-box {
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .color-preview-btn {
        display: inline-block;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 15px;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(0,0,0,0.15);
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

                <!-- 2. Page & Hero Settings -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-desktop text-primary"></i> 2. Page & Hero Section
                    </div>
                    <div class="section-card-body">
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label fw-bold">Landing Page Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="page_title" class="form-control" placeholder="e.g. Premium Designer Dress" value="{{ old('title') }}" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold">URL Slug <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text small bg-light text-muted">/landing/</span>
                                    <input type="text" name="slug" id="page_slug" class="form-control" placeholder="designer-dress" value="{{ old('slug') }}">
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Top Announcement Bar Text</label>
                                <input type="text" name="top_notice" class="form-control" placeholder="✦ ক্যাশ অন ডেলিভারি — সারাদেশে পণ্য হাতে পেয়ে, খুলে দেখে তারপর টাকা দিন" value="{{ old('top_notice', '✦ ক্যাশ অন ডেলিভারি — সারাদেশে পণ্য হাতে পেয়ে, খুলে দেখে তারপর টাকা দিন') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Hero Tag (Top Badge)</label>
                                <input type="text" name="hero_tag" id="hero_tag" class="form-control" placeholder="e.g. ROH CLOTHS · PREMIUM COLLECTION" value="{{ old('hero_tag', 'ROH CLOTHS · PREMIUM COLLECTION') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Video Embed URL (Optional)</label>
                                <input type="url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('video_url') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Hero Sub-title / Tagline</label>
                                <textarea name="sub_title" id="sub_title" class="form-control" rows="2" placeholder="সিম্পল, এলিগ্যান্ট এবং প্রিমিয়াম লুক। পার্টি, ওয়েডিং ও বিশেষ দিনের জন্য বাছাই করা ডিজাইন।">{{ old('sub_title') }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Feature Highlights (Under Order Button, 1 per line)</label>
                                <textarea name="features" class="form-control" rows="3" placeholder="ক্যাশ অন ডেলিভারি&#10;সারাদেশে ডেলিভারি&#10;এক্সচেঞ্জ সুবিধা&#10;প্রিমিয়াম প্যাকেজিং">{{ old('features', "ক্যাশ অন ডেলিভারি\nসারাদেশে ডেলিভারি\nএক্সচেঞ্জ সুবিধা\nপ্রিমিয়াম প্যাকেজিং") }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Button & Background Colors (Dynamic Styling) -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-palette text-primary"></i> 3. Button & Background Colors (ডিজাইন কালার)
                    </div>
                    <div class="section-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">CTA Button Background Color (বাটন কালার)</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="btn_color" id="btn_color" class="form-control form-control-color" value="{{ old('btn_color', '#E0B04A') }}">
                                    <input type="text" id="btn_color_text" class="form-control font-monospace" value="{{ old('btn_color', '#E0B04A') }}" readonly style="width: 120px;">
                                </div>
                                <small class="text-muted">সমস্ত "অর্ডার করুন" ও সাবমিট বাটনের কালার পরিবর্তন হবে।</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">CTA Button Text Color (বাটন টেক্সট কালার)</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="btn_text_color" id="btn_text_color" class="form-control form-control-color" value="{{ old('btn_text_color', '#2B1C06') }}">
                                    <input type="text" id="btn_text_color_text" class="form-control font-monospace" value="{{ old('btn_text_color', '#2B1C06') }}" readonly style="width: 120px;">
                                </div>
                                <small class="text-muted">বাটনের ভেতরের লেখার কালার।</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Section Dark Background Color (হিরো ও ডার্ক সেকশন ব্যাকগ্রাউন্ড)</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="bg_color" id="bg_color" class="form-control form-control-color" value="{{ old('bg_color', '#1F0A0F') }}">
                                    <input type="text" id="bg_color_text" class="form-control font-monospace" value="{{ old('bg_color', '#1F0A0F') }}" readonly style="width: 120px;">
                                </div>
                                <small class="text-muted">Hero ব্যানার, গ্যালারি ও ফুটারের ব্যাকগ্রাউন্ড কালার।</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Theme Primary / Accent Color (থিম অ্যাকসেন্ট)</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="theme_color" id="theme_color" class="form-control form-control-color" value="{{ old('theme_color', '#880e4f') }}">
                                    <input type="text" id="theme_color_text" class="form-control font-monospace" value="{{ old('theme_color', '#880e4f') }}" readonly style="width: 120px;">
                                </div>
                                <small class="text-muted">অ্যাকসেন্ট, হেডার বাটন এবং হাইলাইট কালার।</small>
                            </div>

                            <!-- Live Color Preview -->
                            <div class="col-12 mt-2">
                                <label class="form-label fw-bold small text-muted">লাইভ কালার প্রিভিউ (Live Preview):</label>
                                <div class="color-preview-box" id="colorPreviewBox" style="background: #1F0A0F; border: 1px solid #e2e8f0;">
                                    <span style="color: #fff; font-size: 13px; display: block; margin-bottom: 12px;">ডার্ক ব্যাকগ্রাউন্ডে বাটনের দৃশ্যমানতা:</span>
                                    <a href="javascript:void(0)" class="color-preview-btn" id="previewBtn">অর্ডার করুন — ৳১,৯৫০</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. ৩টি সহজ অর্ডার ধাপ ও গ্যারান্টি (Order Steps & Guarantee) -->
                <div class="section-card">
                    <div class="section-card-header">
                        <i class="fa fa-list-ol text-primary"></i> 4. ৩টি সহজ অর্ডার ধাপ ও গ্যারান্টি
                    </div>
                    <div class="section-card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label class="form-label small fw-bold">ধাপ ১ (Step 1 Title)</label>
                                <input type="text" name="steps[0][title]" class="form-control form-control-sm" value="{{ old('steps.0.title', 'ফর্ম পূরণ করুন') }}">
                                <input type="text" name="steps[0][desc]" class="form-control form-control-sm mt-1" value="{{ old('steps.0.desc', 'নাম, ফোন, ঠিকানা — ৩০ সেকেন্ড') }}" placeholder="সংক্ষিপ্ত বিবরণ">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small fw-bold">ধাপ ২ (Step 2 Title)</label>
                                <input type="text" name="steps[1][title]" class="form-control form-control-sm" value="{{ old('steps.1.title', 'কনফার্মেশন কল') }}">
                                <input type="text" name="steps[1][desc]" class="form-control form-control-sm mt-1" value="{{ old('steps.1.desc', 'আমরা কল দিয়ে নিশ্চিত করব') }}" placeholder="সংক্ষিপ্ত বিবরণ">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small fw-bold">ধাপ ৩ (Step 3 Title)</label>
                                <input type="text" name="steps[2][title]" class="form-control form-control-sm" value="{{ old('steps.2.title', 'হাতে পেয়ে টাকা') }}">
                                <input type="text" name="steps[2][desc]" class="form-control form-control-sm mt-1" value="{{ old('steps.2.desc', 'খুলে দেখে তারপর পেমেন্ট') }}" placeholder="সংক্ষিপ্ত বিবরণ">
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">গ্যারান্টি সেকশন শিরোনাম</label>
                                <input type="text" name="guarantee_title" class="form-control form-control-sm" value="{{ old('guarantee_title', 'ঝুঁকিটা আমাদের, আপনার না') }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold">গ্যারান্টি সেকশন বিবরণ</label>
                                <input type="text" name="guarantee_desc" class="form-control form-control-sm" value="{{ old('guarantee_desc', 'ডেলিভারি ম্যানের সামনেই প্যাকেট খুলে দেখুন। ছবির সাথে না মিললে বা কোনো সমস্যা থাকলে গ্রহণ করবেন না — টাকা দেওয়ার কোনো প্রশ্নই আসে না।') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Customer Reviews -->
                <div class="section-card">
                    <div class="section-card-header d-flex justify-content-between align-items-center">
                        <div><i class="fa fa-star text-warning"></i> 5. Customer Reviews (ক্রেতাদের রিভিউ)</div>
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
                                        <input type="text" name="reviews[0][name]" class="form-control form-control-sm" value="তানজিলা ইসলাম">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small fw-bold">City / Location</label>
                                        <input type="text" name="reviews[0][location]" class="form-control form-control-sm" value="ধানমন্ডি, ঢাকা">
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
                                        <input type="text" name="reviews[0][comment]" class="form-control form-control-sm" value="মাশাল্লাহ! ছবির চেয়েও বাস্তবে প্রোডাক্টটা অনেক বেশি সুন্দর ও নিখুঁত। কাপড় এবং ফিনিশিং অসাধারণ।">
                                    </div>
                                </div>
                            </div>

                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger remove-btn remove-review"><i class="fa fa-times"></i></button>
                                <div class="row">
                                    <div class="col-md-5 mb-2">
                                        <label class="form-label small fw-bold">Customer Name</label>
                                        <input type="text" name="reviews[1][name]" class="form-control form-control-sm" value="নুসরাত জাহান রিমু">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small fw-bold">City / Location</label>
                                        <input type="text" name="reviews[1][location]" class="form-control form-control-sm" value="চট্টগ্রাম সদর">
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
                                        <input type="text" name="reviews[1][comment]" class="form-control form-control-sm" value="ডেলিভারি ম্যানের সামনে প্যাকেট খুলে চেক করে নিতে পেরেছি, এটাই সবচেয়ে বড় ভরসা। কোয়ালিটি ১০০/১০০!">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Frequently Asked Questions (FAQs) -->
                <div class="section-card">
                    <div class="section-card-header d-flex justify-content-between align-items-center">
                        <div><i class="fa fa-circle-question text-info"></i> 6. FAQs (সচরাচর জিজ্ঞাসা)</div>
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
                                    <input type="text" name="faqs[0][question]" class="form-control form-control-sm" value="প্রোডাক্ট কি ছবির মতোই হবে?">
                                </div>
                                <div>
                                    <label class="form-label small fw-bold">Answer</label>
                                    <textarea name="faqs[0][answer]" class="form-control form-control-sm" rows="2">জি, আমাদের প্রতিটি ছবি অরিজিনাল স্টকের। ডেলিভারির সময় প্যাকেট খুলে দেখে তারপর পেমেন্ট করতে পারবেন।</textarea>
                                </div>
                            </div>

                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger remove-btn remove-faq"><i class="fa fa-times"></i></button>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Question</label>
                                    <input type="text" name="faqs[1][question]" class="form-control form-control-sm" value="ডেলিভারি কতদিনে পাবো?">
                                </div>
                                <div>
                                    <label class="form-label small fw-bold">Answer</label>
                                    <textarea name="faqs[1][answer]" class="form-control form-control-sm" rows="2">ঢাকা সিটির ভিতরে সাধারণত ২৪–৪৮ ঘণ্টা এবং ঢাকার বাইরে ২–৩ কার্যদিবস সময় লাগে।</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 4 Columns: Publish & Auto-Sync Summary -->
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

                <!-- Auto-Fetched Summary Notice -->
                <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #e6fcf5 100%); border: 1px solid #bbf7d0 !important; border-radius: 8px;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-success mb-2 d-flex align-items-center gap-2">
                            <i class="fa fa-circle-check"></i> স্বয়ংক্রিয়ভাবে সংযুক্ত বিষয়সমূহ
                        </h6>
                        <ul class="list-unstyled mb-0 small text-secondary" style="line-height: 1.8;">
                            <li><i class="fa fa-check text-success me-1"></i> <strong>মূল্য ও স্টক:</strong> সিলেক্ট করা প্রোডাক্ট ও তার ভ্যারিয়েন্ট স্টক থেকে সরাসরি অটোমেটিক আসবে।</li>
                            <li><i class="fa fa-check text-success me-1"></i> <strong>ডেলিভারি চার্জ:</strong> ওয়েবসাইট শিপিং সেটিংস থেকে সরাসরি আসবে।</li>
                            <li><i class="fa fa-check text-success me-1"></i> <strong>স্লাইডার ও গ্যালারি ছবি:</strong> প্রোডাক্টের প্রধান ছবি এবং মাল্টি-ইমেজ থেকে সরাসরি তৈরি হবে।</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </form>
</section>

<script>
$(document).ready(function() {
    // Sync color pickers with text inputs & live preview
    function updateColorPreview() {
        var btnColor = $('#btn_color').val();
        var btnTextColor = $('#btn_text_color').val();
        var bgColor = $('#bg_color').val();

        $('#btn_color_text').val(btnColor);
        $('#btn_text_color_text').val(btnTextColor);
        $('#bg_color_text').val(bgColor);
        $('#theme_color_text').val($('#theme_color').val());

        $('#colorPreviewBox').css('background', bgColor);
        $('#previewBtn').css({
            'background': btnColor,
            'color': btnTextColor
        });
    }

    $('#btn_color, #btn_text_color, #bg_color, #theme_color').on('input change', updateColorPreview);
    updateColorPreview();

    // Auto title & slug from product
    $('#product_id').on('change', function() {
        var productId = $(this).val();
        if (!productId) {
            $('#productPreviewBox').hide();
            return;
        }

        $.ajax({
            url: "{{ url('admin/landing-page/ajax-product') }}/" + productId,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    $('#productPreviewBox').show();
                    $('#previewTitle').text(data.name_en + (data.name_bn ? ' (' + data.name_bn + ')' : ''));
                    if (data.thumbnail) {
                        $('#previewThumb').attr('src', data.thumbnail).show();
                    } else {
                        $('#previewThumb').hide();
                    }
                    $('#previewRegPrice').text('৳' + (data.regular_price || 0));
                    $('#previewSalePrice').text('৳' + (data.discount_price > 0 ? data.discount_price : data.regular_price));
                    $('#previewStock').text(data.stock_qty || 0);

                    // Auto-fill title & slug if empty
                    if (!$('#page_title').val()) {
                        $('#page_title').val(data.name_en);
                        var slug = data.name_en.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                        $('#page_slug').val(slug);
                    }
                    if (!$('#sub_title').val() && (data.description_en || data.description_bn)) {
                        $('#sub_title').val(data.description_en || data.description_bn);
                    }
                }
            }
        });
    });

    // Auto slug on typing title
    $('#page_title').on('keyup', function() {
        var title = $(this).val();
        var slug = title.toLowerCase().replace(/[^a-z0-9\u0980-\u09FF]+/g, '-').replace(/(^-|-$)+/g, '');
        $('#page_slug').val(slug);
    });

    // Add Review Repeater
    var reviewIndex = 2;
    $('#addReviewBtn').on('click', function() {
        var html = `
        <div class="repeater-item">
            <button type="button" class="btn btn-sm btn-danger remove-btn remove-review"><i class="fa fa-times"></i></button>
            <div class="row">
                <div class="col-md-5 mb-2">
                    <label class="form-label small fw-bold">Customer Name</label>
                    <input type="text" name="reviews[` + reviewIndex + `][name]" class="form-control form-control-sm" placeholder="গ্রাহকের নাম">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label small fw-bold">City / Location</label>
                    <input type="text" name="reviews[` + reviewIndex + `][location]" class="form-control form-control-sm" placeholder="শহর / জেলা">
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
                    <input type="text" name="reviews[` + reviewIndex + `][comment]" class="form-control form-control-sm" placeholder="রিভিউ মন্তব্য">
                </div>
            </div>
        </div>`;
        $('#reviewsContainer').append(html);
        reviewIndex++;
    });

    $(document).on('click', '.remove-review', function() {
        $(this).closest('.repeater-item').remove();
    });

    // Add FAQ Repeater
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
});
</script>
@endsection
