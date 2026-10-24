@extends('admin.admin_master')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <style>
        .section-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
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
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }
    </style>

    <section class="content-main">
        <div class="content-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="content-title card-title">Edit Landing Page: {{ $landingPage->title }}</h2>
                <p class="text-muted">Update landing page configuration, dynamic colors, or content</p>
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
                <h6 class="fw-bold mb-1"><i class="fa fa-exclamation-triangle me-1"></i> Please correct the following errors:
                </h6>
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
                                <select name="product_id" id="product_id" class="form-control select-active form-select"
                                    required>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ $landingPage->product_id == $p->id ? 'selected' : '' }}>
                                            #{{ $p->id }} — {{ $p->name_en }}
                                            (৳{{ $p->discount_price > 0 ? $p->discount_price : $p->regular_price }})
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
                                    <label class="form-label fw-bold">Landing Page Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" id="page_title" class="form-control"
                                        value="{{ old('title', $landingPage->title) }}" required>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label fw-bold">URL Slug <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text small bg-light text-muted">/landing/</span>
                                        <input type="text" name="slug" id="page_slug" class="form-control"
                                            value="{{ old('slug', $landingPage->slug) }}" required>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Top Announcement Bar Text</label>
                                    <input type="text" name="top_notice" class="form-control"
                                        value="{{ old('top_notice', $landingPage->top_notice ?: '✦ ক্যাশ অন ডেলিভারি — সারাদেশে পণ্য হাতে পেয়ে, খুলে দেখে তারপর টাকা দিন') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Hero Tag (Top Badge)</label>
                                    <input type="text" name="hero_tag" class="form-control"
                                        value="{{ old('hero_tag', $landingPage->hero_tag) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Video Embed URL (Optional)</label>
                                    <input type="url" name="video_url" class="form-control"
                                        value="{{ old('video_url', $landingPage->video_url) }}"
                                        placeholder="https://www.youtube.com/watch?v=...">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Messenger Link (Optional)</label>
                                    <input type="url" name="messenger_url" class="form-control @error('messenger_url') is-invalid @enderror"
                                        value="{{ old('messenger_url', $landingPage->messenger_url) }}"
                                        placeholder="https://m.me/your-page">
                                    <small class="text-muted">Used by the Messenger buttons on this landing page.</small>
                                    @error('messenger_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Hero Sub-title / Tagline</label>
                                    <textarea name="sub_title" class="form-control"
                                        rows="2">{{ old('sub_title', $landingPage->sub_title) }}</textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Feature Highlights (Under Order Button, 1 per
                                        line)</label>
                                    @php
                                        $featuresStr = is_array($landingPage->features) ? implode("\n", $landingPage->features) : $landingPage->features;
                                    @endphp
                                    <textarea name="features" class="form-control"
                                        rows="3">{{ old('features', $featuresStr) }}</textarea>
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
                                        <input type="color" name="btn_color" id="btn_color"
                                            class="form-control form-control-color"
                                            value="{{ old('btn_color', $landingPage->btn_color ?? '#E0B04A') }}">
                                        <input type="text" id="btn_color_text" class="form-control font-monospace"
                                            value="{{ old('btn_color', $landingPage->btn_color ?? '#E0B04A') }}" readonly
                                            style="width: 120px;">
                                    </div>
                                    <small class="text-muted">সমস্ত "অর্ডার করুন" ও সাবমিট বাটনের কালার পরিবর্তন
                                        হবে।</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">CTA Button Text Color (বাটন টেক্সট কালার)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="btn_text_color" id="btn_text_color"
                                            class="form-control form-control-color"
                                            value="{{ old('btn_text_color', $landingPage->btn_text_color ?? '#2B1C06') }}">
                                        <input type="text" id="btn_text_color_text" class="form-control font-monospace"
                                            value="{{ old('btn_text_color', $landingPage->btn_text_color ?? '#2B1C06') }}"
                                            readonly style="width: 120px;">
                                    </div>
                                    <small class="text-muted">বাটনের ভেতরের লেখার কালার।</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Section Dark Background Color (হিরো ও ডার্ক সেকশন
                                        ব্যাকগ্রাউন্ড)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="bg_color" id="bg_color"
                                            class="form-control form-control-color"
                                            value="{{ old('bg_color', $landingPage->bg_color ?? '#1F0A0F') }}">
                                        <input type="text" id="bg_color_text" class="form-control font-monospace"
                                            value="{{ old('bg_color', $landingPage->bg_color ?? '#1F0A0F') }}" readonly
                                            style="width: 120px;">
                                    </div>
                                    <small class="text-muted">Hero ব্যানার, গ্যালারি ও ফুটারের ব্যাকগ্রাউন্ড কালার।</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Theme Primary / Accent Color (থিম অ্যাকসেন্ট)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="theme_color" id="theme_color"
                                            class="form-control form-control-color"
                                            value="{{ old('theme_color', $landingPage->theme_color ?? '#000000') }}">
                                        <input type="text" id="theme_color_text" class="form-control font-monospace"
                                            value="{{ old('theme_color', $landingPage->theme_color ?? '#000000') }}"
                                            readonly style="width: 120px;">
                                    </div>
                                    <small class="text-muted">অ্যাকসেন্ট, হেডার বাটন এবং হাইলাইট কালার।</small>
                                </div>

                                <!-- Live Color Preview -->
                                <div class="col-12 mt-2">
                                    <label class="form-label fw-bold small text-muted">লাইভ কালার প্রিভিউ (Live
                                        Preview):</label>
                                    <div class="color-preview-box" id="colorPreviewBox"
                                        style="background: {{ $landingPage->bg_color ?? '#1F0A0F' }}; border: 1px solid #e2e8f0;">
                                        <span
                                            style="color: #fff; font-size: 13px; display: block; margin-bottom: 12px;">ডার্ক
                                            ব্যাকগ্রাউন্ডে বাটনের দৃশ্যমানতা:</span>
                                        <a href="javascript:void(0)" class="color-preview-btn" id="previewBtn"
                                            style="background: {{ $landingPage->btn_color ?? '#E0B04A' }}; color: {{ $landingPage->btn_text_color ?? '#2B1C06' }};">অর্ডার
                                            করুন — ৳১,৯৫০</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. ৩টি সহজ অর্ডার ধাপ ও গ্যারান্টি (Order Steps & Guarantee) -->
                    @php
                        $aboutCards = is_array($landingPage->about_cards) ? $landingPage->about_cards : [];
                        $steps = $aboutCards['steps'] ?? [
                            ['title' => 'ফর্ম পূরণ করুন', 'desc' => 'নাম, ফোন, ঠিকানা — ৩০ সেকেন্ড'],
                            ['title' => 'কনফার্মেশন কল', 'desc' => 'আমরা কল দিয়ে নিশ্চিত করব'],
                            ['title' => 'হাতে পেয়ে টাকা', 'desc' => 'খুলে দেখে তারপর পেমেন্ট'],
                        ];
                        $guaranteeTitle = $aboutCards['guarantee_title'] ?? 'ঝুঁকিটা আমাদের, আপনার না';
                        $guaranteeDesc = $aboutCards['guarantee_desc'] ?? 'ডেলিভারি ম্যানের সামনেই প্যাকেট খুলে দেখুন। ছবির সাথে না মিললে বা কোনো সমস্যা থাকলে গ্রহণ করবেন না — টাকা দেওয়ার কোনো প্রশ্নই আসে না।';
                        $whatYouGetTitle = $aboutCards['what_you_get_title'] ?? 'যা যা পাচ্ছেন';
                        $whatYouGetCards = $aboutCards['what_you_get_cards'] ?? [
                            ['icon' => '✦', 'title' => 'সিলভার সিরোস্কি স্টোন', 'subtitle' => 'পুরো শাড়ি জুড়ে ঝলমলে কাজ'],
                            ['icon' => '吕', 'title' => 'সিল্ক ব্লেন্ড শিফন', 'subtitle' => 'আল্ট্রা সফট ও লাইটওয়েট'],
                            ['icon' => '💎', 'title' => 'নেকলেস ডিজাইন ব্লাউজ', 'subtitle' => 'ম্যাচিং জর্জেট ব্লাউজ পিস'],
                        ];
                    @endphp

                    <!-- 4. যা যা পাচ্ছেন / ফিচার কার্ডসমূহ (What You Get Cards) -->
                    <div class="section-card">
                        <div class="section-card-header d-flex justify-content-between align-items-center">
                            <div><i class="fa fa-boxes-packing text-primary"></i> 4. যা যা পাচ্ছেন (What You Get Cards)</div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addWhatYouGetCardBtn">
                                <i class="fa fa-plus me-1"></i> কার্ড যোগ করুন
                            </button>
                        </div>
                        <div class="section-card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">সেকশন টাইটেল (Section Heading)</label>
                                <input type="text" name="what_you_get_title" class="form-control form-control-sm" value="{{ old('what_you_get_title', $whatYouGetTitle) }}" placeholder="যা যা পাচ্ছেন">
                            </div>
                            <div id="whatYouGetCardsContainer">
                                @forelse($whatYouGetCards as $cIdx => $card)
                                    <div class="repeater-item p-3 mb-3 border rounded bg-light position-relative">
                                        <button type="button" class="btn btn-sm btn-danger remove-btn remove-card-btn position-absolute top-0 end-0 m-2"><i class="fa fa-times"></i></button>
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">আইকন / চিহ্ন (Icon)</label>
                                                <input type="text" name="what_you_get_cards[{{ $cIdx }}][icon]" class="form-control form-control-sm" value="{{ $card['icon'] ?? '✦' }}" placeholder="যেমন: ✦, 吕, 💎, ⭐, 👑">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">কার্ড টাইটেল (Title)</label>
                                                <input type="text" name="what_you_get_cards[{{ $cIdx }}][title]" class="form-control form-control-sm" value="{{ $card['title'] ?? '' }}" placeholder="যেমন: সিলভার সিরোস্কি স্টোন" required>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small fw-bold">সাবটাইটেল / বিবরণ (Subtitle)</label>
                                                <input type="text" name="what_you_get_cards[{{ $cIdx }}][subtitle]" class="form-control form-control-sm" value="{{ $card['subtitle'] ?? '' }}" placeholder="যেমন: পুরো শাড়ি জুড়ে ঝলমলে কাজ">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted small">কোনো কার্ড যোগ করা হয়নি। উপরে "+ কার্ড যোগ করুন" বাটনে ক্লিক করুন।</p>
                                @endforelse
                            </div>
                            <small class="text-muted d-block mt-2">হিরো সেকশনের ঠিক নিচে এই কার্ডগুলো সুন্দরভাবে প্রদর্শিত হবে। আপনি যতগুলো ইচ্ছা কার্ড যোগ বা বাদ দিতে পারেন।</small>
                        </div>
                    </div>

                    <!-- 5. Customer Review Images -->
                    <div class="section-card">
                        <div class="section-card-header"><i class="fa fa-images text-warning"></i> 5. Customer Review Images</div>
                        <div class="section-card-body">
                            <label for="review_images" class="form-label fw-bold">Add review images</label>
                            <input type="file" name="review_images[]" id="review_images" multiple class="form-control @error('review_images.*') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">Upload multiple review images (JPG, PNG, or WebP; max 5 MB each). Existing images are kept unless removed below.</small>
                            @error('review_images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @php $currentReviewImages = $landingPage->review_images ?: array_filter([$landingPage->review_image]); @endphp
                            <div class="d-flex flex-wrap gap-3 mt-3">
                                @foreach($currentReviewImages as $reviewImage)
                                    <div style="width:180px">
                                        <img src="{{ asset($reviewImage) }}" alt="Customer review" style="display:block;width:100%;max-height:160px;object-fit:contain;border:1px solid #e2e8f0;border-radius:8px;padding:4px;">
                                        <label class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_review_images[]" value="{{ $reviewImage }}"> <span class="form-check-label">Remove this image</span></label>
                                    </div>
                                @endforeach
                            </div>
                            <div id="reviewImagePreview" class="d-flex flex-wrap gap-2 mt-3"></div>
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
                                @php
                                    $faqs = is_array($landingPage->faqs) ? $landingPage->faqs : [];
                                @endphp
                                @forelse($faqs as $fIndex => $faq)
                                    <div class="repeater-item">
                                        <button type="button" class="btn btn-sm btn-danger remove-btn remove-faq"><i
                                                class="fa fa-times"></i></button>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Question</label>
                                            <input type="text" name="faqs[{{ $fIndex }}][question]"
                                                class="form-control form-control-sm" value="{{ $faq['question'] ?? '' }}">
                                        </div>
                                        <div>
                                            <label class="form-label small fw-bold">Answer</label>
                                            <textarea name="faqs[{{ $fIndex }}][answer]" class="form-control form-control-sm"
                                                rows="2">{{ $faq['answer'] ?? '' }}</textarea>
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

                    <!-- Order Form Email Field Settings -->
                    <div class="section-card">
                        <div class="section-card-header">
                            <i class="fa fa-envelope text-primary"></i> অর্ডার ফর্মের ইমেইল ফিল্ড (Email Field)
                        </div>
                        <div class="section-card-body">
                            @php
                                $currentEmailMode = old('email_field', $landingPage->getEmailFieldMode());
                            @endphp
                            <label class="form-label fw-bold">ইমেইল ফিল্ডের স্ট্যাটাস</label>
                            <select name="email_field" class="form-select mb-2">
                                <option value="hidden" {{ $currentEmailMode === 'hidden' ? 'selected' : '' }}>লুকানো (Hidden / Hide)</option>
                                <option value="optional" {{ $currentEmailMode === 'optional' ? 'selected' : '' }}>ঐচ্ছিক (Optional / Unrequired)</option>
                                <option value="required" {{ $currentEmailMode === 'required' ? 'selected' : '' }}>আবশ্যক (Required / বাধ্যতামূলক)</option>
                            </select>
                          
                        </div>
                    </div>

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

                    <!-- Auto-Fetched Summary Notice -->
                    <div class="card border-0 shadow-sm mb-3"
                        style="background: linear-gradient(135deg, #f0fdf4 0%, #e6fcf5 100%); border: 1px solid #bbf7d0 !important; border-radius: 8px;">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-success mb-2 d-flex align-items-center gap-2">
                                <i class="fa fa-circle-check"></i> স্বয়ংক্রিয়ভাবে সংযুক্ত বিষয়সমূহ
                            </h6>
                            <ul class="list-unstyled mb-0 small text-secondary" style="line-height: 1.8;">
                                <li><i class="fa fa-check text-success me-1"></i> <strong>মূল্য ও স্টক:</strong> সিলেক্ট করা
                                    প্রোডাক্ট ও তার ভ্যারিয়েন্ট স্টক থেকে সরাসরি অটোমেটিক আসবে।</li>
                                <li><i class="fa fa-check text-success me-1"></i> <strong>ডেলিভারি চার্জ:</strong> ওয়েবসাইট
                                    শিপিং সেটিংস থেকে সরাসরি আসবে।</li>
                                <li><i class="fa fa-check text-success me-1"></i> <strong>স্লাইডার ও গ্যালারি ছবি:</strong>
                                    প্রোডাক্টের প্রধান ছবি এবং মাল্টি-ইমেজ থেকে সরাসরি তৈরি হবে।</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </section>

    <script>
        $(document).ready(function () {
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

            $('#review_images').on('change', function () {
                var preview = $('#reviewImagePreview').empty();
                Array.from(this.files || []).forEach(function (file) {
                    $('<img>').attr({ src: URL.createObjectURL(file), alt: 'Review preview' }).css({ maxWidth: '180px', maxHeight: '160px', objectFit: 'contain', border: '1px solid #e2e8f0', borderRadius: '8px', padding: '4px' }).appendTo(preview);
                });
            });

            var cardIndex = {{ count($whatYouGetCards ?? []) + 1 }};
            $('#addWhatYouGetCardBtn').on('click', function () {
                var html = `
                <div class="repeater-item p-3 mb-3 border rounded bg-light position-relative">
                    <button type="button" class="btn btn-sm btn-danger remove-btn remove-card-btn position-absolute top-0 end-0 m-2"><i class="fa fa-times"></i></button>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">আইকন / চিহ্ন (Icon)</label>
                            <input type="text" name="what_you_get_cards[` + cardIndex + `][icon]" class="form-control form-control-sm" value="✦" placeholder="যেমন: ✦, 吕, 💎, ⭐, 👑">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">কার্ড টাইটেল (Title)</label>
                            <input type="text" name="what_you_get_cards[` + cardIndex + `][title]" class="form-control form-control-sm" placeholder="কার্ডের শিরোনাম" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">সাবটাইটেল / বিবরণ (Subtitle)</label>
                            <input type="text" name="what_you_get_cards[` + cardIndex + `][subtitle]" class="form-control form-control-sm" placeholder="ছোট বিবরণ">
                        </div>
                    </div>
                </div>`;
                $('#whatYouGetCardsContainer').append(html);
                cardIndex++;
            });

            $(document).on('click', '.remove-card-btn', function () {
                $(this).closest('.repeater-item').remove();
            });

            var faqIndex = {{ count($faqs ?? []) + 1 }};
            $('#addFaqBtn').on('click', function () {
                var html = `
            <div class="repeater-item">
                <button type="button" class="btn btn-sm btn-danger remove-btn remove-faq"><i class="fa fa-times"></i></button>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Question</label>
                    <input type="text" name="faqs[` + faqIndex + `][question]" class="form-control form-control-sm" placeholder="প্রশ্ন">
                </div>
                <div>
                    <label class="form-label small fw-bold">Answer</label>
                    <textarea name="faqs[` + faqIndex + `][answer]" class="form-control form-control-sm" rows="2" placeholder="উত্তর"></textarea>
                </div>
            </div>`;
                $('#faqsContainer').append(html);
                faqIndex++;
            });

            $(document).on('click', '.remove-faq', function () {
                $(this).closest('.repeater-item').remove();
            });
        });
    </script>
@endsection
