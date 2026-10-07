@extends('FrontEnd.master')
@section('title', 'Track Your Order')

@section('content')
<style>
    .rf-tracking-page {
        background-color: #fafafa;
        background-image: radial-gradient(#e4e4e7 1px, transparent 1px);
        background-size: 24px 24px;
        min-height: calc(100vh - 120px);
        padding: 60px 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    .rf-tracking-wrapper {
        width: 100%;
        max-width: 540px;
        margin: 0 auto;
    }

    .rf-tracking-card {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 20px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05), 0 0 1px rgba(0, 0, 0, 0.1);
        padding: 42px 36px;
        position: relative;
        overflow: hidden;
    }

    .rf-tracking-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #09090b;
    }

    .rf-badge-icon {
        width: 68px;
        height: 68px;
        background: #09090b;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin: 0 auto 20px auto;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        border: 4px solid #f4f4f5;
        transition: transform 0.3s ease;
    }

    .rf-tracking-card:hover .rf-badge-icon {
        transform: translateY(-2px);
    }

    .rf-tracking-title {
        color: #09090b;
        font-weight: 800;
        font-size: 26px;
        letter-spacing: -0.5px;
        text-align: center;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .rf-tracking-subtitle {
        color: #71717a;
        font-size: 14.5px;
        text-align: center;
        margin-bottom: 28px;
        line-height: 1.5;
    }

    .rf-alert-box {
        background: #09090b;
        color: #ffffff;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 13.5px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .rf-alert-box.alert-error {
        background: #18181b;
        border: 1px solid #27272a;
    }

    .rf-alert-box i {
        font-size: 16px;
        color: #f4f4f5;
    }

    .rf-form-group {
        margin-bottom: 22px;
    }

    .rf-form-label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: #18181b;
        margin-bottom: 8px;
    }

    .rf-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .rf-input-icon {
        position: absolute;
        left: 16px;
        color: #71717a;
        font-size: 16px;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .rf-input {
        width: 100%;
        background: #ffffff;
        border: 1.5px solid #e4e4e7;
        border-radius: 12px;
        padding: 13px 16px 13px 46px;
        font-size: 14.5px;
        color: #09090b;
        font-family: inherit;
        font-weight: 500;
        transition: all 0.2s ease;
        outline: none;
    }

    .rf-input::placeholder {
        color: #a1a1aa;
        font-weight: 400;
    }

    .rf-input:focus {
        border-color: #09090b;
        box-shadow: 0 0 0 3px rgba(9, 9, 11, 0.08);
    }

    .rf-input:focus + .rf-input-icon,
    .rf-input-wrapper:focus-within .rf-input-icon {
        color: #09090b;
    }

    .rf-field-error {
        color: #09090b;
        font-size: 12px;
        font-weight: 600;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .rf-btn-track {
        width: 100%;
        background: #09090b;
        color: #ffffff;
        border: 1px solid #09090b;
        border-radius: 12px;
        padding: 14px 24px;
        font-size: 14.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.25s ease;
        margin-top: 26px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .rf-btn-track:hover {
        background: #27272a;
        border-color: #27272a;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.16);
    }

    .rf-btn-track:active {
        transform: translateY(0);
    }

    .rf-tracking-footer {
        margin-top: 30px;
        padding-top: 22px;
        border-top: 1px solid #f4f4f5;
        text-align: center;
    }

    .rf-help-text {
        font-size: 13.5px;
        color: #71717a;
        margin-bottom: 0;
    }

    .rf-help-link {
        color: #09090b;
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 3px;
        transition: opacity 0.2s;
    }

    .rf-help-link:hover {
        color: #000000;
        opacity: 0.8;
    }

    /* Feature pills */
    .rf-features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 26px;
    }

    .rf-feature-pill {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 12px;
        padding: 14px 10px;
        text-align: center;
        transition: border-color 0.2s ease;
    }

    .rf-feature-pill:hover {
        border-color: #09090b;
    }

    .rf-feature-icon {
        font-size: 18px;
        color: #09090b;
        margin-bottom: 6px;
    }

    .rf-feature-text {
        font-size: 11.5px;
        font-weight: 600;
        color: #27272a;
        line-height: 1.3;
        margin-bottom: 0;
    }

    @media (max-width: 576px) {
        .rf-tracking-page {
            padding: 30px 15px;
        }

        .rf-tracking-card {
            padding: 30px 20px;
            border-radius: 16px;
        }

        .rf-tracking-title {
            font-size: 22px;
        }

        .rf-features-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .rf-feature-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            text-align: left;
        }

        .rf-feature-icon {
            margin-bottom: 0;
        }
    }
</style>

<main class="rf-tracking-page">
    <div class="rf-tracking-wrapper">
        <div class="rf-tracking-card">
            <div class="rf-badge-icon">
                <i class="fa-solid fa-truck-fast"></i>
            </div>

            <h1 class="rf-tracking-title">Track Your Order</h1>
            <p class="rf-tracking-subtitle">
                Enter your <strong>Invoice ID</strong> and registered <strong>Phone Number</strong> to check live parcel updates.
            </p>

            @if(session()->has('message'))
                <div class="rf-alert-box alert-error" id="session-alert">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ session()->get('message') }}</span>
                    </div>
                    <button type="button" onclick="document.getElementById('session-alert').remove()" style="background: transparent; border: none; color: #ffffff; cursor: pointer; padding: 0 4px; font-size: 14px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            <form method="GET" action="{{ route('order.track') }}" autocomplete="off" id="trackingForm">
                <div class="rf-form-group">
                    <label for="invoice_no" class="rf-form-label">
                        Invoice ID <span style="color: #71717a;">*</span>
                    </label>
                    <div class="rf-input-wrapper">
                        <i class="fa-solid fa-receipt rf-input-icon"></i>
                        <input 
                            type="text" 
                            name="invoice_no" 
                            id="invoice_no" 
                            class="rf-input" 
                            placeholder="e.g. RF-8B77D3ABFFC76092682861D9" 
                            value="{{ old('invoice_no', request('invoice_no')) }}" 
                            required
                        >
                    </div>
                    @if(isset($errors) && $errors->has('invoice_no'))
                        <div class="rf-field-error">
                            <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first('invoice_no') }}
                        </div>
                    @endif
                </div>

                <div class="rf-form-group">
                    <label for="phone" class="rf-form-label">
                        Phone Number <span style="color: #71717a;">*</span>
                    </label>
                    <div class="rf-input-wrapper">
                        <i class="fa-solid fa-phone rf-input-icon"></i>
                        <input 
                            type="tel" 
                            name="phone" 
                            id="phone" 
                            class="rf-input" 
                            placeholder="e.g. 017XXXXXXXX" 
                            value="{{ old('phone', request('phone')) }}" 
                            required
                        >
                    </div>
                    @if(isset($errors) && $errors->has('phone'))
                        <div class="rf-field-error">
                            <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first('phone') }}
                        </div>
                    @endif
                </div>

                <button type="submit" class="rf-btn-track" id="btnTrackSubmit">
                    <span>Track Order</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

                <div class="rf-tracking-footer">
                    <p class="rf-help-text">
                        Need assistance? 
                        @if(get_setting('phone') && get_setting('phone')->value)
                            <a href="tel:{{ get_setting('phone')->value }}" class="rf-help-link">Call Support ({{ get_setting('phone')->value }})</a>
                        @else
                            <a href="{{ url('/contact') }}" class="rf-help-link">Contact Support</a>
                        @endif
                    </p>
                </div>
            </form>
        </div>

        <!-- Quick Info Pills -->
        <div class="rf-features-grid">
            <div class="rf-feature-pill">
                <div class="rf-feature-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <p class="rf-feature-text">Live Status Tracking</p>
            </div>
            <div class="rf-feature-pill">
                <div class="rf-feature-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <p class="rf-feature-text">Verified Delivery</p>
            </div>
            <div class="rf-feature-pill">
                <div class="rf-feature-icon">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <p class="rf-feature-text">Dedicated Helpline</p>
            </div>
        </div>
    </div>
</main>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('trackingForm');
        const submitBtn = document.getElementById('btnTrackSubmit');

        if (form && submitBtn) {
            form.addEventListener('submit', function () {
                submitBtn.innerHTML = '<span>Searching...</span> <i class="fa-solid fa-spinner fa-spin"></i>';
                submitBtn.style.opacity = '0.85';
                submitBtn.style.pointerEvents = 'none';
            });
        }
    });
</script>
@endpush