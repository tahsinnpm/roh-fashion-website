@extends('FrontEnd.master')
@section('title', 'Order Tracking - #' . $order->invoice_no)

@section('content')
@php
    $status = strtolower($order->delivery_status ?? 'pending');
    $statusMap = [
        'pending' => 1,
        'confirmed' => 2,
        'processing' => 3,
        'picked_up' => 4,
        'shipped' => 5,
        'delivered' => 6,
    ];
    $currentStep = $statusMap[$status] ?? 1;
    $isCancelled = ($status === 'cancelled');

    $steps = [
        [
            'num' => 1,
            'key' => 'pending',
            'title' => 'Order Placed',
            'desc' => 'Awaiting confirmation',
            'icon' => 'fa-solid fa-receipt',
        ],
        [
            'num' => 2,
            'key' => 'confirmed',
            'title' => 'Confirmed',
            'desc' => 'Order verified',
            'icon' => 'fa-solid fa-circle-check',
        ],
        [
            'num' => 3,
            'key' => 'processing',
            'title' => 'Processing',
            'desc' => 'Quality check & packaging',
            'icon' => 'fa-solid fa-box-open',
        ],
        [
            'num' => 4,
            'key' => 'picked_up',
            'title' => 'Picked Up',
            'desc' => 'Handed over to courier',
            'icon' => 'fa-solid fa-boxes-packing',
        ],
        [
            'num' => 5,
            'key' => 'shipped',
            'title' => 'In Transit',
            'desc' => 'Out for delivery',
            'icon' => 'fa-solid fa-truck-fast',
        ],
        [
            'num' => 6,
            'key' => 'delivered',
            'title' => 'Delivered',
            'desc' => 'Order completed',
            'icon' => 'fa-solid fa-house-circle-check',
        ],
    ];

    $progressPercentage = $isCancelled ? 0 : (($currentStep - 1) / (count($steps) - 1)) * 100;
@endphp

<style>
    :root {
        --rf-black: #09090b;
        --rf-charcoal: #18181b;
        --rf-zinc-700: #3f3f46;
        --rf-zinc-500: #71717a;
        --rf-zinc-400: #a1a1aa;
        --rf-zinc-200: #e4e4e7;
        --rf-zinc-100: #f4f4f5;
        --rf-zinc-50: #fafafa;
        --rf-white: #ffffff;
    }

    .rf-track-page {
        background-color: var(--rf-zinc-50);
        min-height: 100vh;
        padding: 40px 0 80px 0;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--rf-black);
    }

    .rf-container {
        max-width: 1160px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Top Action Bar */
    .rf-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .rf-btn-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--rf-black);
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 9999px;
        border: 1px solid var(--rf-zinc-200);
        background: var(--rf-white);
        transition: all 0.2s ease;
    }

    .rf-btn-link:hover {
        background: var(--rf-black);
        color: var(--rf-white);
        border-color: var(--rf-black);
    }

    .rf-action-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rf-btn-print {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--rf-black);
        color: var(--rf-white);
        border: 1px solid var(--rf-black);
        padding: 8px 18px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .rf-btn-print:hover {
        background: var(--rf-charcoal);
        transform: translateY(-1px);
    }

    /* Main Overview Card */
    .rf-card {
        background: var(--rf-white);
        border: 1px solid var(--rf-zinc-200);
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .rf-card-body {
        padding: 32px;
    }

    /* Header Banner */
    .rf-order-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        padding-bottom: 28px;
        border-bottom: 1px solid var(--rf-zinc-200);
    }

    .rf-order-meta-label {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--rf-zinc-500);
        margin-bottom: 4px;
    }

    .rf-order-invoice {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--rf-black);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rf-btn-copy {
        background: var(--rf-zinc-100);
        border: 1px solid var(--rf-zinc-200);
        color: var(--rf-black);
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .rf-btn-copy:hover {
        background: var(--rf-black);
        color: var(--rf-white);
        border-color: var(--rf-black);
    }

    .rf-order-date {
        font-size: 13.5px;
        color: var(--rf-zinc-500);
        margin-top: 4px;
    }

    .rf-header-badges {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Monochrome Status Badges */
    .rf-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .rf-badge-status-solid {
        background: var(--rf-black);
        color: var(--rf-white);
        border: 1px solid var(--rf-black);
    }

    .rf-badge-status-outline {
        background: var(--rf-white);
        color: var(--rf-black);
        border: 1.5px solid var(--rf-black);
    }

    .rf-badge-status-muted {
        background: var(--rf-zinc-100);
        color: var(--rf-zinc-700);
        border: 1px solid var(--rf-zinc-200);
    }

    .rf-badge-cancelled {
        background: #18181b;
        color: #ffffff;
        border: 1px solid #27272a;
    }

    /* Live Stepper Section */
    .rf-stepper-wrapper {
        padding: 36px 10px 10px 10px;
    }

    .rf-stepper-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--rf-zinc-500);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rf-stepper {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .rf-stepper-track-bg {
        position: absolute;
        top: 22px;
        left: 24px;
        right: 24px;
        height: 2px;
        background: var(--rf-zinc-200);
        z-index: 1;
    }

    .rf-stepper-track-fill {
        position: absolute;
        top: 22px;
        left: 24px;
        height: 2px;
        background: var(--rf-black);
        z-index: 2;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .rf-step {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 110px;
    }

    .rf-step-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--rf-white);
        border: 2px solid var(--rf-zinc-200);
        color: var(--rf-zinc-400);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 0 0 4px var(--rf-white);
    }

    .rf-step.completed .rf-step-circle {
        background: var(--rf-black);
        border-color: var(--rf-black);
        color: var(--rf-white);
    }

    .rf-step.current .rf-step-circle {
        background: var(--rf-black);
        border-color: var(--rf-black);
        color: var(--rf-white);
        box-shadow: 0 0 0 4px var(--rf-white), 0 0 0 7px rgba(9, 9, 11, 0.15);
    }

    .rf-step-label {
        margin-top: 12px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--rf-zinc-400);
        line-height: 1.3;
        transition: color 0.2s ease;
    }

    .rf-step.completed .rf-step-label,
    .rf-step.current .rf-step-label {
        color: var(--rf-black);
    }

    .rf-step-caption {
        font-size: 11px;
        color: var(--rf-zinc-500);
        margin-top: 2px;
        display: block;
    }

    .rf-step-pulse-pill {
        display: inline-block;
        background: var(--rf-black);
        color: var(--rf-white);
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.5px;
        padding: 2px 6px;
        border-radius: 4px;
        margin-top: 4px;
        text-transform: uppercase;
    }

    /* Cancelled Alert Box */
    .rf-cancelled-banner {
        background: #18181b;
        color: #ffffff;
        border-radius: 12px;
        padding: 20px 24px;
        margin-top: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .rf-cancelled-icon {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    /* Two Columns Grid */
    .rf-details-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .rf-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--rf-zinc-200);
        margin-bottom: 20px;
    }

    .rf-section-title {
        font-size: 15px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--rf-black);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Product Item Cards */
    .rf-items-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .rf-item-card {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 16px;
        background: var(--rf-zinc-50);
        border: 1px solid var(--rf-zinc-200);
        border-radius: 12px;
        transition: border-color 0.2s ease;
    }

    .rf-item-card:hover {
        border-color: var(--rf-black);
    }

    .rf-item-image {
        width: 72px;
        height: 72px;
        border-radius: 8px;
        object-fit: cover;
        background: #ffffff;
        border: 1px solid var(--rf-zinc-200);
        flex-shrink: 0;
    }

    .rf-item-info {
        flex-grow: 1;
        min-width: 0;
    }

    .rf-item-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--rf-black);
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 4px;
    }

    .rf-item-name:hover {
        color: var(--rf-zinc-700);
    }

    .rf-variant-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--rf-white);
        border: 1px solid var(--rf-zinc-200);
        color: var(--rf-zinc-700);
        font-size: 11.5px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 6px;
        margin-bottom: 6px;
    }

    .rf-item-math {
        font-size: 13px;
        color: var(--rf-zinc-500);
    }

    .rf-item-price-total {
        font-size: 15px;
        font-weight: 800;
        color: var(--rf-black);
        text-align: right;
        flex-shrink: 0;
        padding-left: 12px;
    }

    /* Receipt Summary */
    .rf-receipt-list {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
    }

    .rf-receipt-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        font-size: 13.5px;
        border-bottom: 1px dashed var(--rf-zinc-200);
    }

    .rf-receipt-row:last-child {
        border-bottom: none;
    }

    .rf-receipt-label {
        color: var(--rf-zinc-500);
        font-weight: 500;
    }

    .rf-receipt-value {
        font-weight: 700;
        color: var(--rf-black);
    }

    .rf-grand-total-card {
        background: var(--rf-black);
        color: var(--rf-white);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
    }

    .rf-grand-total-label {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--rf-zinc-400);
    }

    .rf-grand-total-amount {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .rf-payment-status-row {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid var(--rf-zinc-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
    }

    /* Address Grid */
    .rf-address-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .rf-address-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rf-address-info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 9px 0;
        font-size: 13.5px;
        line-height: 1.5;
        border-bottom: 1px solid var(--rf-zinc-100);
    }

    .rf-address-info-item:last-child {
        border-bottom: none;
    }

    .rf-address-icon {
        width: 22px;
        color: var(--rf-black);
        font-size: 14px;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .rf-address-text strong {
        color: var(--rf-black);
        font-weight: 700;
    }

    .rf-address-text {
        color: var(--rf-zinc-700);
    }

    /* Need Assistance Banner */
    .rf-help-banner {
        background: var(--rf-white);
        border: 1px solid var(--rf-zinc-200);
        border-radius: 16px;
        padding: 24px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 18px;
    }

    .rf-help-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .rf-help-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--rf-zinc-100);
        border: 1px solid var(--rf-zinc-200);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: var(--rf-black);
        flex-shrink: 0;
    }

    .rf-help-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--rf-black);
        margin-bottom: 3px;
    }

    .rf-help-desc {
        font-size: 13px;
        color: var(--rf-zinc-500);
        margin-bottom: 0;
    }

    .rf-help-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Print Styles */
    @media print {
        header, footer, .rf-action-bar, .rf-help-banner, .rf-btn-copy, .rf-btn-link, .rf-btn-print {
            display: none !important;
        }

        body, .rf-track-page {
            background: #ffffff !important;
            padding: 0 !important;
        }

        .rf-card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            page-break-inside: avoid;
        }

        .rf-grand-total-card {
            background: #ffffff !important;
            color: #000000 !important;
            border: 2px solid #000 !important;
        }

        .rf-grand-total-label, .rf-grand-total-amount {
            color: #000000 !important;
        }
    }

    /* Responsive */
    @media (max-width: 992px) {
        .rf-details-grid {
            grid-template-columns: 1fr;
        }
        .rf-address-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .rf-card-body {
            padding: 22px 16px;
        }

        .rf-order-invoice {
            font-size: 20px;
        }

        .rf-stepper {
            overflow-x: auto;
            padding-bottom: 16px;
            justify-content: flex-start;
            gap: 16px;
        }

        .rf-stepper-track-bg, .rf-stepper-track-fill {
            display: none;
        }

        .rf-step {
            min-width: 90px;
        }

        .rf-help-banner {
            flex-direction: column;
            align-items: flex-start;
        }

        .rf-help-actions {
            width: 100%;
        }

        .rf-help-actions .rf-btn-link {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<main class="rf-track-page">
    <div class="rf-container">
        <!-- Top Action Navigation -->
        <div class="rf-action-bar">
            <a href="{{ route('order.tracking') }}" class="rf-btn-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Track Another Order</span>
            </a>

            <div class="rf-action-group">
                <button type="button" onclick="window.print()" class="rf-btn-print">
                    <i class="fa-solid fa-print"></i>
                    <span>Print Receipt</span>
                </button>
            </div>
        </div>

        <!-- Order Overview Card -->
        <div class="rf-card">
            <div class="rf-card-body">
                <!-- Header Info -->
                <div class="rf-order-header">
                    <div>
                        <div class="rf-order-meta-label">Invoice Number</div>
                        <div class="rf-order-invoice">
                            <span id="invoiceText">#{{ $order->invoice_no }}</span>
                            <button type="button" class="rf-btn-copy" onclick="copyInvoice('{{ $order->invoice_no }}')" title="Copy Invoice ID">
                                <i class="fa-regular fa-copy" id="copyIcon"></i> <span id="copyLabel">Copy</span>
                            </button>
                        </div>
                        <div class="rf-order-date">
                            <i class="fa-regular fa-calendar me-1"></i> Order Placed: {{ $order->created_at ? $order->created_at->format('M d, Y • h:i A') : 'N/A' }}
                        </div>
                    </div>

                    <div class="rf-header-badges">
                        @if($isCancelled)
                            <span class="rf-badge rf-badge-cancelled">
                                <i class="fa-solid fa-circle-xmark"></i> Cancelled
                            </span>
                        @elseif($status === 'delivered')
                            <span class="rf-badge rf-badge-status-solid">
                                <i class="fa-solid fa-circle-check"></i> Delivered
                            </span>
                        @elseif(in_array($status, ['shipped', 'picked_up']))
                            <span class="rf-badge rf-badge-status-solid">
                                <i class="fa-solid fa-truck-fast"></i> {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        @elseif($status === 'processing')
                            <span class="rf-badge rf-badge-status-outline">
                                <i class="fa-solid fa-gear fa-spin"></i> Processing
                            </span>
                        @elseif($status === 'confirmed')
                            <span class="rf-badge rf-badge-status-outline">
                                <i class="fa-solid fa-check"></i> Confirmed
                            </span>
                        @else
                            <span class="rf-badge rf-badge-status-muted">
                                <i class="fa-solid fa-clock"></i> Pending Confirmation
                            </span>
                        @endif

                        <span class="rf-badge {{ $order->payment_status == 1 ? 'rf-badge-status-solid' : 'rf-badge-status-outline' }}">
                            <i class="fa-solid {{ $order->payment_status == 1 ? 'fa-circle-check' : 'fa-hourglass-half' }}"></i>
                            {{ $order->payment_status == 1 ? 'Paid' : 'Payment Pending' }}
                        </span>
                    </div>
                </div>

                <!-- Stepper Progress Tracker -->
                @if($isCancelled)
                    <div class="rf-cancelled-banner">
                        <div class="rf-cancelled-icon">
                            <i class="fa-solid fa-ban"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 16px; font-weight: 800; margin-bottom: 4px; color: #ffffff;">Order Cancelled</h4>
                            <p style="font-size: 13.5px; color: #a1a1aa; margin-bottom: 0;">
                                This order has been cancelled. If you believe this is an error or need assistance, please contact our support desk.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="rf-stepper-wrapper">
                        <div class="rf-stepper-title">
                            <i class="fa-solid fa-route"></i> Live Order Journey
                        </div>

                        <div class="rf-stepper">
                            <div class="rf-stepper-track-bg"></div>
                            <div class="rf-stepper-track-fill" style="width: {{ $progressPercentage }}%;"></div>

                            @foreach($steps as $s)
                                @php
                                    $isCompleted = ($currentStep > $s['num']);
                                    $isCurrent = ($currentStep === $s['num']);
                                    $stepState = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'upcoming');
                                @endphp
                                <div class="rf-step {{ $stepState }}">
                                    <div class="rf-step-circle">
                                        @if($isCompleted)
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            <i class="{{ $s['icon'] }}"></i>
                                        @endif
                                    </div>
                                    <div class="rf-step-label">{{ $s['title'] }}</div>
                                    <span class="rf-step-caption">{{ $s['desc'] }}</span>
                                    @if($isCurrent)
                                        <span class="rf-step-pulse-pill">Active</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2 Columns: Items & Financial Breakdown -->
        <div class="rf-details-grid">
            <!-- Left: Ordered Products -->
            <div class="rf-card">
                <div class="rf-card-body">
                    <div class="rf-section-header">
                        <div class="rf-section-title">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Items in Order ({{ $order->order_details->count() }})</span>
                        </div>
                    </div>

                    <div class="rf-items-list">
                        @foreach($order->order_details as $orderDetail)
                            @php
                                $product = $orderDetail->product;
                                $productName = $product->name_en ?? $orderDetail->product_name ?? 'Fashion Item';
                                $img = (!empty($product->product_thumbnail)) ? asset($product->product_thumbnail) : asset('FrontEnd/assect/img/product/default.jpg');
                                
                                // Decode variation JSON or string safely
                                $variations = [];
                                if (!empty($orderDetail->variation)) {
                                    if (is_array($orderDetail->variation)) {
                                        $variations = $orderDetail->variation;
                                    } else {
                                        $decoded = json_decode($orderDetail->variation, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                            $variations = $decoded;
                                        } else {
                                            $variations = [['attribute_value' => $orderDetail->variation]];
                                        }
                                    }
                                }
                            @endphp
                            <div class="rf-item-card">
                                <img src="{{ $img }}" alt="{{ $productName }}" class="rf-item-image" onerror="this.src='https://placehold.co/100x100/f4f4f5/09090b?text=Item'">
                                <div class="rf-item-info">
                                    @if($product && !empty($product->slug))
                                        <a href="{{ route('product.details', $product->slug) }}" class="rf-item-name" target="_blank">{{ $productName }}</a>
                                    @else
                                        <div class="rf-item-name">{{ $productName }}</div>
                                    @endif

                                    @if(!empty($variations))
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin: 4px 0;">
                                            @foreach($variations as $var)
                                                @php
                                                    $attrName = $var['attribute_name'] ?? '';
                                                    $attrVal = $var['attribute_value'] ?? (is_string($var) ? $var : '');
                                                @endphp
                                                @if(!empty($attrVal))
                                                    <span class="rf-variant-pill">
                                                        @if(!empty($attrName) && strtolower($attrName) !== 'variant')
                                                            <strong style="color: var(--rf-black);">{{ $attrName }}:</strong>
                                                        @endif
                                                        {{ $attrVal }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="rf-item-math">
                                        ৳ {{ number_format($orderDetail->price, 2) }} × {{ $orderDetail->qty }}
                                    </div>
                                </div>
                                <div class="rf-item-price-total">
                                    ৳ {{ number_format($orderDetail->price * $orderDetail->qty, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary / Receipt -->
            <div class="rf-card">
                <div class="rf-card-body">
                    <div class="rf-section-header">
                        <div class="rf-section-title">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Order Summary</span>
                        </div>
                    </div>

                    <ul class="rf-receipt-list">
                        <li class="rf-receipt-row">
                            <span class="rf-receipt-label">Subtotal</span>
                            <span class="rf-receipt-value">৳ {{ number_format($order->sub_total ?? 0, 2) }}</span>
                        </li>
                        <li class="rf-receipt-row">
                            <span class="rf-receipt-label">Delivery Charge ({{ $order->shipping_name ?? 'Standard' }})</span>
                            <span class="rf-receipt-value">৳ {{ number_format($order->shipping_charge ?? 0, 2) }}</span>
                        </li>
                        @if(!empty($order->coupon) && $order->coupon > 0)
                            <li class="rf-receipt-row">
                                <span class="rf-receipt-label">Coupon Discount</span>
                                <span class="rf-receipt-value">- ৳ {{ number_format($order->coupon, 2) }}</span>
                            </li>
                        @endif
                    </ul>

                    <div class="rf-grand-total-card">
                        <div>
                            <div class="rf-grand-total-label">Total Payable</div>
                            <div style="font-size: 11px; color: var(--rf-zinc-400);">Inclusive of all taxes</div>
                        </div>
                        <div class="rf-grand-total-amount">
                            ৳ {{ number_format($order->grand_total, 2) }}
                        </div>
                    </div>

                    <div class="rf-payment-status-row">
                        <span style="color: var(--rf-zinc-500); font-weight: 600;">Payment Method</span>
                        <span style="font-weight: 700; color: var(--rf-black); text-transform: uppercase;">
                            @if(strtolower($order->payment_method ?? '') === 'cod')
                                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Cash On Delivery
                            @else
                                <i class="fa-regular fa-credit-card me-1"></i> {{ $order->payment_method ?? 'Cash On Delivery' }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping and Merchant Details Grid -->
        <div class="rf-address-grid">
            <!-- Shipping Card -->
            <div class="rf-card">
                <div class="rf-card-body">
                    <div class="rf-section-header">
                        <div class="rf-section-title">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Delivery Destination</span>
                        </div>
                    </div>

                    <ul class="rf-address-info-list">
                        <li class="rf-address-info-item">
                            <i class="fa-solid fa-user rf-address-icon"></i>
                            <div class="rf-address-text">
                                <strong>Recipient:</strong> {{ $order->name ?? 'Customer' }}
                            </div>
                        </li>
                        <li class="rf-address-info-item">
                            <i class="fa-solid fa-phone rf-address-icon"></i>
                            <div class="rf-address-text">
                                <strong>Contact Phone:</strong> {{ $order->phone ?? 'N/A' }}
                            </div>
                        </li>
                        <li class="rf-address-info-item">
                            <i class="fa-solid fa-map-pin rf-address-icon"></i>
                            <div class="rf-address-text">
                                <strong>Delivery Address:</strong> {{ $order->address ?? 'N/A' }}
                            </div>
                        </li>
                        @if(!empty($order->comment))
                            <li class="rf-address-info-item">
                                <i class="fa-solid fa-message rf-address-icon"></i>
                                <div class="rf-address-text">
                                    <strong>Special Instruction:</strong> {{ $order->comment }}
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Merchant Card -->
            <div class="rf-card">
                <div class="rf-card-body">
                    <div class="rf-section-header">
                        <div class="rf-section-title">
                            <i class="fa-solid fa-store"></i>
                            <span>Dispatched By</span>
                        </div>
                    </div>

                    <ul class="rf-address-info-list">
                        <li class="rf-address-info-item">
                            <i class="fa-solid fa-building rf-address-icon"></i>
                            <div class="rf-address-text">
                                <strong>Store:</strong> {{ get_setting('business_name')->value ?? 'Shinduk Fashion' }}
                            </div>
                        </li>
                        <li class="rf-address-info-item">
                            <i class="fa-solid fa-headset rf-address-icon"></i>
                            <div class="rf-address-text">
                                <strong>Helpline:</strong> {{ get_setting('phone')->value ?? '+880 1838-389494' }}
                            </div>
                        </li>
                        <li class="rf-address-info-item">
                            <i class="fa-solid fa-envelope rf-address-icon"></i>
                            <div class="rf-address-text">
                                <strong>Support Email:</strong> {{ get_setting('email')->value ?? 'support@rohfashion.com' }}
                            </div>
                        </li>
                        <li class="rf-address-info-item">
                            <i class="fa-solid fa-shield-halved rf-address-icon"></i>
                            <div class="rf-address-text">
                                <strong>Authenticity:</strong> 100% Genuine Designer Apparel
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Help / Assistance Bar -->
        <div class="rf-help-banner">
            <div class="rf-help-info">
                <div class="rf-help-icon">
                    <i class="fa-solid fa-circle-question"></i>
                </div>
                <div>
                    <h5 class="rf-help-title">Have inquiries about your parcel?</h5>
                    <p class="rf-help-desc">
                        Our customer service team is on standby to assist you with order delivery status and updates.
                    </p>
                </div>
            </div>

            <div class="rf-help-actions">
                @if(get_setting('phone') && get_setting('phone')->value)
                    <a href="tel:{{ get_setting('phone')->value }}" class="rf-btn-link">
                        <i class="fa-solid fa-phone"></i>
                        <span>Call Support</span>
                    </a>
                @endif
                <a href="{{ url('/') }}" class="rf-btn-link" style="background: var(--rf-black); color: var(--rf-white); border-color: var(--rf-black);">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span>Continue Shopping</span>
                </a>
            </div>
        </div>
    </div>
</main>
@endsection

@push('js')
<script>
    function copyInvoice(text) {
        if (!navigator.clipboard) {
            const temp = document.createElement('input');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            showCopyFeedback();
            return;
        }

        navigator.clipboard.writeText(text).then(function() {
            showCopyFeedback();
        }).catch(function(err) {
            console.error('Copy failed', err);
        });
    }

    function showCopyFeedback() {
        const icon = document.getElementById('copyIcon');
        const label = document.getElementById('copyLabel');
        if (icon && label) {
            icon.className = 'fa-solid fa-check';
            label.textContent = 'Copied!';
            setTimeout(function() {
                icon.className = 'fa-regular fa-copy';
                label.textContent = 'Copy';
            }, 2000);
        }
    }
</script>
@endpush