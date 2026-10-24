@extends('admin.admin_master')
@section('admin')

    <style>
        .order-detail-page {
            --od-bg: #f8fafc;
            --od-card-bg: #ffffff;
            --od-border: #e2e8f0;
            --od-text-dark: #0f172a;
            --od-text-muted: #64748b;
            --od-primary: #2563eb;
            --od-primary-light: #eff6ff;
            --od-success: #10b981;
            --od-success-light: #ecfdf5;
            --od-warning: #f59e0b;
            --od-warning-light: #fffbeb;
            --od-danger: #ef4444;
            --od-danger-light: #fef2f2;
            color: var(--od-text-dark);
            font-family: inherit;
        }

        /* Override the global .card-header { background-color: #365486 !important; } from custom.css */
        .order-detail-page .card-header,
        .order-detail-page .order-detail-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid var(--od-border) !important;
            padding: 1.25rem 1.75rem !important;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        .order-detail-page .order-main-card {
            background: #ffffff;
            border: 1px solid var(--od-border);
            border-radius: 14px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        /* Top Breadcrumb & Actions Bar */
        .order-detail-page .page-nav-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-detail-page .page-nav-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .order-detail-page .page-nav-title h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--od-text-dark);
            margin: 0;
        }

        .order-detail-page .order-tag {
            background: var(--od-primary-light);
            color: var(--od-primary);
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 6px;
            border: 1px solid rgba(37, 99, 235, 0.15);
        }

        /* Header Date and Meta */
        .order-detail-page .order-header-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .order-detail-page .order-header-date {
            color: var(--od-text-dark);
            font-size: 1.05rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-detail-page .order-header-invoice {
            color: var(--od-text-muted);
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .order-detail-page .btn-copy-code {
            cursor: pointer;
            color: var(--od-primary);
            font-size: 0.85rem;
            border: none;
            background: transparent;
            padding: 0 0.25rem;
            transition: color 0.15s;
        }

        .order-detail-page .btn-copy-code:hover {
            color: #1d4ed8;
        }

        /* Header Action Controls */
        .order-detail-page .header-controls {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .order-detail-page .status-control-box {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: #f8fafc;
            border: 1px solid var(--od-border);
            padding: 0.25rem 0.5rem 0.25rem 0.65rem;
            border-radius: 8px;
        }

        .order-detail-page .status-control-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--od-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin: 0;
            white-space: nowrap;
        }

        .order-detail-page .status-select {
            font-size: 0.88rem;
            font-weight: 600;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.35rem 1.8rem 0.35rem 0.6rem;
            min-height: 36px;
            background-color: #ffffff;
            cursor: pointer;
        }

        .order-detail-page .status-select:focus {
            border-color: var(--od-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .order-detail-page .btn-action-invoice {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #f1f5f9;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            transition: all 0.2s;
            min-height: 38px;
            text-decoration: none;
        }

        .order-detail-page .btn-action-invoice:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .order-detail-page .btn-action-delete {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            transition: all 0.2s;
            min-height: 38px;
            cursor: pointer;
        }

        .order-detail-page .btn-action-delete:hover {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #f87171;
        }

        /* 3 Summary Cards */
        .order-detail-page .summary-card {
            background: #ffffff;
            border: 1px solid var(--od-border);
            border-radius: 12px;
            padding: 1.25rem;
            height: 100%;
            transition: box-shadow 0.2s, transform 0.2s;
            display: flex;
            flex-direction: column;
        }

        .order-detail-page .summary-card:hover {
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        }

        .order-detail-page .summary-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .order-detail-page .summary-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .order-detail-page .summary-icon-customer {
            background: #eff6ff;
            color: #2563eb;
        }

        .order-detail-page .summary-icon-order {
            background: #ecfdf5;
            color: #059669;
        }

        .order-detail-page .summary-icon-shipping {
            background: #fffbeb;
            color: #d97706;
        }

        .order-detail-page .summary-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--od-text-dark);
            margin: 0;
        }

        .order-detail-page .summary-card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .order-detail-page .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.88rem;
            line-height: 1.45;
        }

        .order-detail-page .summary-row-label {
            color: var(--od-text-muted);
            font-weight: 500;
            white-space: nowrap;
        }

        .order-detail-page .summary-row-value {
            color: var(--od-text-dark);
            font-weight: 600;
            text-align: right;
            word-break: break-word;
        }

        .order-detail-page .address-display-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 0.85rem;
            margin-bottom: 0.5rem;
        }

        .order-detail-page .address-text {
            font-size: 0.92rem;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.5;
            word-break: break-word;
            margin: 0 0 0.35rem 0;
        }

        .order-detail-page .address-location-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: #e2e8f0;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }

        /* Status Pills */
        .order-detail-page .od-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            line-height: 1.3;
        }

        .order-detail-page .od-pill-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .order-detail-page .od-pill-confirmed { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .order-detail-page .od-pill-shipped { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .order-detail-page .od-pill-picked_up { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
        .order-detail-page .od-pill-on_the_way { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
        .order-detail-page .od-pill-delivered { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .order-detail-page .od-pill-cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        .order-detail-page .od-pill-paid { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .order-detail-page .od-pill-unpaid { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }

        /* Content Section Cards */
        .order-detail-page .section-box {
            background: #ffffff;
            border: 1px solid var(--od-border);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .order-detail-page .section-box-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--od-border);
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .order-detail-page .section-box-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--od-text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .order-detail-page .section-box-title {
     
            color: black !important;
        }

        .order-detail-page .section-box-body {
            padding: 1.25rem;
        }

        /* Products Table */
        .order-detail-page .products-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .order-detail-page .products-table th {
            background: #f8fafc;
            border-bottom: 1px solid var(--od-border);
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 0.85rem 1rem;
        }

        .order-detail-page .products-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: var(--od-text-dark);
            font-size: 0.9rem;
        }

        .order-detail-page .products-table tr:last-child td {
            border-bottom: 0;
        }

        .order-detail-page .product-cell {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .order-detail-page .product-thumb {
            width: 52px;
            height: 52px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .order-detail-page .product-title {
            font-weight: 600;
            color: var(--od-text-dark);
            font-size: 0.92rem;
            line-height: 1.35;
            margin-bottom: 0.25rem;
        }

        .order-detail-page .product-variation-tag {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.76rem;
            font-weight: 500;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            margin-top: 0.2rem;
            margin-right: 0.35rem;
            border: 1px solid #e2e8f0;
        }

        /* Modern Financial Totals Card */
        .order-detail-page .totals-box {
            background: #f8fafc;
            border: 1px solid var(--od-border);
            border-radius: 10px;
            padding: 1.25rem;
            max-width: 380px;
            margin-left: auto;
        }

        .order-detail-page .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            font-size: 0.9rem;
            color: var(--od-text-muted);
        }

        .order-detail-page .totals-row-value {
            font-weight: 600;
            color: var(--od-text-dark);
        }

        .order-detail-page .totals-row.grand-total {
            border-top: 1px solid #cbd5e1;
            margin-top: 0.5rem;
            padding-top: 0.75rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--od-text-dark);
        }

        .order-detail-page .totals-row.grand-total .totals-row-value {
            color: var(--od-primary);
            font-size: 1.25rem;
        }

        /* Modern Form Inputs & Layout */
        .order-detail-page .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .order-detail-page .form-control,
        .order-detail-page .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.55rem 0.85rem;
            font-size: 0.9rem;
            color: #0f172a;
            min-height: 42px;
            background-color: #ffffff;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .order-detail-page .form-control:focus,
        .order-detail-page .form-select:focus {
            border-color: var(--od-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .order-detail-page .input-group-text {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-weight: 600;
            border-radius: 8px 0 0 8px;
        }

        .order-detail-page .input-group .form-control {
            border-radius: 0 8px 8px 0;
        }

        .order-detail-page .btn-save-order {
            background: var(--od-primary);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.65rem 1.75rem;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s, box-shadow 0.2s;
        }

        .order-detail-page .btn-save-order:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            color: #ffffff;
        }

        @media (max-width: 991.98px) {
            .order-detail-page .header-controls {
                justify-content: flex-start;
                margin-top: 1rem;
            }
            .order-detail-page .totals-box {
                max-width: 100%;
                margin-top: 1rem;
            }
        }
    </style>

    <section class="content-main order-detail-page">
        <!-- Top Navigation -->
        <div class="page-nav-bar">
            <div class="page-nav-title">
                <a href="{{ route('all_orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Back to orders">
                    <i class="fa fa-arrow-left me-1"></i> Orders
                </a>
                <h2>Order Details</h2>
                <span class="order-tag">#{{ $order->invoice_no ?? '' }}</span>
            </div>
            <div>
                <a href="{{ route('invoice.download', $order->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                    <i class="fa fa-print me-1"></i> Print Invoice
                </a>
            </div>
        </div>

        @php
            $payment_status = (string) $order->payment_status;
            $delivery_status = $order->delivery_status ?: 'pending';
        @endphp

        <!-- Main Order Header Card -->
        <div class="order-main-card">
            <header class="card-header order-detail-header">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-md-12 mb-lg-0 mb-3">
                        <div class="order-header-info">
                            <span class="order-header-date">
                                <i class="fa-regular fa-calendar text-primary"></i>
                                {{ optional($order->created_at)->format('d M Y, h:i A') }}
                            </span>
                            <span class="order-header-invoice">
                                <span>Invoice: <strong>#{{ $order->invoice_no ?? '' }}</strong></span>
                                <button type="button" class="btn-copy-code" title="Copy invoice number" onclick="copyToClipboard('{{ $order->invoice_no }}', this)">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12">
                        <div class="header-controls">
                            <!-- Payment Status Select -->
                            <div class="status-control-box">
                                <label class="status-control-label" for="update_payment_status">Payment</label>
                                <select class="form-select status-select" id="update_payment_status" aria-label="Update payment status">
                                    <option value="0" @if ($payment_status === '0') selected @endif>Unpaid</option>
                                    <option value="1" @if ($payment_status === '1') selected @endif>Paid</option>
                                </select>
                            </div>

                            <!-- Delivery Status Select -->
                            <div class="status-control-box">
                                <label class="status-control-label" for="update_delivery_status">Delivery</label>
                                @if($delivery_status != 'cancelled')
                                    <select class="form-select status-select" id="update_delivery_status" aria-label="Update delivery status">
                                        <option value="pending" @if ($delivery_status == 'pending') selected @endif>Pending</option>
                                        <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>Confirmed</option>
                                        <option value="shipped" @if ($delivery_status == 'shipped') selected @endif>Shipped</option>
                                        <option value="picked_up" @if ($delivery_status == 'picked_up') selected @endif>Picked Up</option>
                                        <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>On The Way</option>
                                        <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>Delivered</option>
                                        <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>Cancel</option>
                                    </select>
                                @else
                                    <span class="od-pill od-pill-cancelled">Cancelled</span>
                                @endif
                            </div>

                            <!-- Download Invoice Button -->
                            <a class="btn-action-invoice" href="{{ route('invoice.download', $order->id) }}" title="Download Invoice">
                                <i class="fa-solid fa-file-invoice"></i>
                                <span>Invoice</span>
                            </a>

                            <!-- Delete Order Button -->
                            <form id="delete-order-form-{{ $order->id }}" action="{{ route('delete.orders', $order->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action-delete" onclick="confirmOrderDelete({{ $order->id }}, '{{ $order->invoice_no }}')" title="Move order to trash">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="card-body p-4">
                <!-- 3 Top Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Customer Card -->
                    <div class="col-lg-4 col-md-6">
                        <div class="summary-card">
                            <div class="summary-card-header">
                                <div class="summary-icon-box summary-icon-customer">
                                    <i class="fa-regular fa-user"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="summary-card-title">Customer Info</h6>
                                    <small class="text-muted">Personal & Contact</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#staticBackdrop1{{ $order->id }}" title="Edit Customer Details">
                                    <i class="fa fa-pen me-1"></i> Edit
                                </button>
                            </div>
                            <div class="summary-card-body">
                                <div class="summary-row">
                                    <span class="summary-row-label">Name:</span>
                                    <span class="summary-row-value text-primary">{{ $order->name ?: 'N/A' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-row-label">Phone:</span>
                                    <span class="summary-row-value">
                                        @if($order->phone)
                                            <a href="tel:{{ $order->phone }}" class="text-dark text-decoration-none">
                                                <i class="fa fa-phone text-muted me-1"></i>{{ $order->phone }}
                                            </a>
                                            <button type="button" class="btn-copy-code" title="Copy phone" onclick="copyToClipboard('{{ $order->phone }}', this)">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-row-label">Email:</span>
                                    <span class="summary-row-value">
                                        @if($order->email)
                                            <a href="mailto:{{ $order->email }}" class="text-muted text-decoration-none">{{ $order->email }}</a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Card -->
                    <div class="col-lg-4 col-md-6">
                        <div class="summary-card">
                            <div class="summary-card-header">
                                <div class="summary-icon-box summary-icon-order">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div>
                                    <h6 class="summary-card-title">Order Status</h6>
                                    <small class="text-muted">Payment & Shipping</small>
                                </div>
                            </div>
                            <div class="summary-card-body">
                                <div class="summary-row">
                                    <span class="summary-row-label">Shipping Method:</span>
                                    <span class="summary-row-value">{{ $order->shipping_name ?: 'Standard' }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-row-label">Payment Method:</span>
                                    <span class="summary-row-value">
                                        @if($order->payment_method == 'cod')
                                            Cash on delivery
                                        @elseif($order->payment_method == 'bikash')
                                            bKash
                                        @elseif($order->payment_method == 'nagad')
                                            Nagad
                                        @else
                                            {{ ucfirst($order->payment_method ?: 'N/A') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-row-label">Payment Status:</span>
                                    <span class="summary-row-value">
                                        @if($payment_status === '1')
                                            <span class="od-pill od-pill-paid"><i class="fa fa-circle-check"></i> Paid</span>
                                        @else
                                            <span class="od-pill od-pill-unpaid"><i class="fa fa-clock"></i> Unpaid</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-row-label">Delivery Status:</span>
                                    <span class="summary-row-value">
                                        <span class="od-pill od-pill-{{ $delivery_status }}">
                                            {{ ucfirst(str_replace('_', ' ', $delivery_status)) }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Address Card -->
                    <div class="col-lg-4 col-md-12">
                        <div class="summary-card">
                            <div class="summary-card-header">
                                <div class="summary-icon-box summary-icon-shipping">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="summary-card-title">Delivery Address</h6>
                                    <small class="text-muted">Destination Details</small>
                                </div>
                                @if($order->address)
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill" title="Copy full address" onclick="copyFullAddress(this)">
                                        <i class="fa-regular fa-copy me-1"></i> Copy
                                    </button>
                                @endif
                            </div>
                            <div class="summary-card-body">
                                <div class="address-display-box" id="full-delivery-address-container">
                                    <p class="address-text" id="target-delivery-address">{{ $order->address ?: 'No address specified' }}</p>
                                    @if($order->upazilla || $order->district || $order->division)
                                        <div class="address-location-badge">
                                            <i class="fa-solid fa-location-dot"></i>
                                            <span>
                                                {{ ucwords($order->upazilla->name_en ?? '') }}{{ $order->district ? ', ' . ucwords($order->district->district_name_en) : '' }}{{ $order->division ? ', ' . ucwords($order->division->division_name_en) : '' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="section-box">
                    <div class="section-box-header">
                        <h5 class="section-box-title">
                            <i style="color:black !important" class="fa-solid fa-box-open text-primary"></i>
                            Ordered Products ({{ count($order->order_details) }})
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">Product</th>
                                    <th style="width: 18%;">Unit Price</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 22%;" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->order_details as $detail)
                                    <tr>
                                        <td>
                                            <div class="product-cell">
                                                <img src="{{ asset($detail->product->product_thumbnail ?? 'upload/no_image.jpg') }}" class="product-thumb" alt="Product" onerror="this.src='{{ asset('upload/no_image.jpg') }}'" />
                                                <div>
                                                    <div class="product-title">{{ $detail->product->name_en ?? ($detail->product_name ?? 'Product') }}</div>
                                                    @if($order->order_type == 1 && $detail->is_varient)
                                                        @php
                                                            $variations = json_decode($detail->variation, true) ?: [];
                                                        @endphp
                                                        @if(is_array($variations) && count($variations) > 0)
                                                            <div>
                                                                @foreach($variations as $var)
                                                                    <span class="product-variation-tag">
                                                                        {{ $var['attribute_name'] ?? 'Variant' }}: <strong>{{ $var['attribute_value'] ?? '' }}</strong>
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @elseif($detail->variation)
                                                            <span class="product-variation-tag">{{ $detail->variation }}</span>
                                                        @endif
                                                    @elseif($detail->variation)
                                                        <span class="product-variation-tag">{{ $detail->variation }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>৳ {{ number_format((float) ($detail->price ?? 0), 2) }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1">x {{ $detail->qty ?? 1 }}</span>
                                        </td>
                                        <td class="text-end fw-bold">৳ {{ number_format((float) (($detail->price ?? 0) * ($detail->qty ?? 1)), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Summary Breakdown -->
                    <div class="p-3 border-top bg-light">
                        <div class="totals-box">
                            <div class="totals-row">
                                <span>Subtotal</span>
                                <span class="totals-row-value">৳ {{ number_format((float) ($order->sub_total ?? 0), 2) }}</span>
                            </div>
                            <div class="totals-row">
                                <span>Shipping Charge</span>
                                <span class="totals-row-value">+ ৳ {{ number_format((float) ($order->shipping_charge ?? 0), 2) }}</span>
                            </div>
                            <div class="totals-row">
                                <span>Discount</span>
                                <span class="totals-row-value text-danger">- ৳ {{ number_format((float) ($order->discount ?? 0), 2) }}</span>
                            </div>
                            <div class="totals-row grand-total">
                                <span>Grand Total</span>
                                <span class="totals-row-value">৳ {{ number_format((float) ($order->grand_total ?? 0), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Order Details Form Section -->
                <div class="section-box">
                    <div class="section-box-header">
                        <h5 class="section-box-title">
                            <i style="color:black !important" class="fa-solid fa-pen-to-square text-primary"></i>
                            Edit Order & Delivery Information
                        </h5>
                        <small class="text-muted">Modify delivery address, locations, or financial charges</small>
                    </div>
                    <div class="section-box-body">
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="post" id="order-update-form">
                            @csrf
                            <div class="row g-3">
                                <!-- Delivery Address -->
                                <div class="col-12">
                                    <label class="form-label" for="order_address">Delivery Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" id="order_address" rows="3" placeholder="সম্পূর্ণ ডেলিভারি ঠিকানা">{{ $order->address }}</textarea>
                                </div>

                                <!-- Division / District / Upazilla (Optional) -->
                                <div class="col-md-4">
                                    <label for="division_id" class="form-label">Division</label>
                                    <select class="form-select" name="division_id" id="division_id">
                                        <option value="">Select Division</option>
                                        @foreach(get_divisions($order->division_id) as $division)
                                            <option value="{{ $division->id }}" {{ $division->id == $order->division_id ? 'selected': '' }}>
                                                {{ ucwords($division->division_name_en) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="district_id" class="form-label">District</label>
                                    <select class="form-select" name="district_id" id="district_id">
                                        <option value="">Select District</option>
                                        @foreach(get_district_by_division_id($order->division_id) as $district)
                                            <option value="{{ $district->id }}" {{ $district->id == $order->district_id ? 'selected': '' }}>
                                                {{ ucwords($district->district_name_en) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="upazilla_id" class="form-label">Upazilla</label>
                                    <select class="form-select" name="upazilla_id" id="upazilla_id">
                                        <option value="">Select Upazilla</option>
                                        @foreach(get_upazilla_by_district_id($order->district_id) as $upazilla)
                                            <option value="{{ $upazilla->id }}" {{ $upazilla->id == $order->upazilla_id ? 'selected': '' }}>
                                                {{ ucwords($upazilla->name_en) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12"><hr class="my-2 text-muted opacity-25"></div>

                                <!-- Payment Method & Email -->
                                <div class="col-md-4">
                                    <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-select" name="payment_method" id="payment_method" required>
                                        <option value="cod" @if($order->payment_method == 'cod') selected @endif>Cash on delivery</option>
                                        <option value="bikash" @if($order->payment_method == 'bikash') selected @endif>bKash</option>
                                        <option value="nagad" @if($order->payment_method == 'nagad') selected @endif>Nagad</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="email" class="form-label">Customer Email</label>
                                    <input type="email" class="form-control" name="email" id="email" value="{{ $order->email }}" placeholder="customer@example.com">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Payment Date</label>
                                    <input type="text" class="form-control bg-light" value="{{ optional($order->created_at)->format('Y/m/d h:i A') }}" disabled>
                                </div>

                                <!-- Shipping Charge & Discount -->
                                <div class="col-md-4">
                                    <label for="shipping_charge_input" class="form-label">Shipping Charge (৳)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="any" class="form-control calc-trigger" id="shipping_charge_input" name="shipping_charge" value="{{ (float) $order->shipping_charge }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="discount_input" class="form-label">Discount (৳)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="any" class="form-control calc-trigger" id="discount_input" name="discount" value="{{ (float) $order->discount }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Calculated Total (৳)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white">৳</span>
                                        <input type="text" style="color:black !important" class="form-control fw-bold text-primary bg-light" id="preview_grand_total" value="{{ number_format((float) $order->grand_total, 2) }}" readonly>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn-save-order">
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        <span>Update Order</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Edit Modal -->
    <div class="modal fade" id="staticBackdrop1{{ $order->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="staticBackdropLabel">
                        <i class="fa-solid fa-user-pen text-primary me-2"></i> Edit Customer Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('admin.user.update', $order->id) }}">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label"><span class="text-danger">*</span> Customer Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Customer full name" value="{{ $order->name }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label"><span class="text-danger">*</span> Phone Number</label>
                                <input type="text" class="form-control" name="phone" placeholder="Phone number" value="{{ $order->phone }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" placeholder="Email address" value="{{ $order->email }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('footer-script')
    <script type="text/javascript">
        // Live Grand Total Calculation
        const subTotalAmount = {{ (float) ($order->sub_total ?? 0) }};

        function recalculateLiveTotal() {
            const shipping = parseFloat($('#shipping_charge_input').val()) || 0;
            const discount = parseFloat($('#discount_input').val()) || 0;
            const total = Math.max(0, (subTotalAmount - discount) + shipping);
            $('#preview_grand_total').val(total.toFixed(2));
        }

        $('.calc-trigger').on('input change', recalculateLiveTotal);

        // Copy to clipboard helper
        function copyToClipboard(text, btnElement) {
            if (!text) return;
            navigator.clipboard.writeText(text).then(function() {
                const $btn = $(btnElement);
                const originalHtml = $btn.html();
                $btn.html('<i class="fa fa-check text-success"></i>');
                setTimeout(function() {
                    $btn.html(originalHtml);
                }, 1500);
            }).catch(function() {
                const tempInput = document.createElement("input");
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                const $btn = $(btnElement);
                const originalHtml = $btn.html();
                $btn.html('<i class="fa fa-check text-success"></i>');
                setTimeout(function() {
                    $btn.html(originalHtml);
                }, 1500);
            });
        }

        // Copy full address with customer details
        function copyFullAddress(btnElement) {
            const customerName = '{{ addslashes($order->name ?? '') }}';
            const customerPhone = '{{ addslashes($order->phone ?? '') }}';
            const address = document.getElementById('target-delivery-address').innerText.trim();
            const locationText = '{{ addslashes(trim(ucwords($order->upazilla->name_en ?? '') . ($order->district ? ', ' . ucwords($order->district->district_name_en) : '') . ($order->division ? ', ' . ucwords($order->division->division_name_en) : ''))) }}';

            let fullText = '';
            if (customerName) fullText += 'Name: ' + customerName + '\n';
            if (customerPhone) fullText += 'Phone: ' + customerPhone + '\n';
            fullText += 'Address: ' + address;
            if (locationText) fullText += ' (' + locationText + ')';

            copyToClipboard(fullText, btnElement);
        }

        // SweetAlert2 Confirmation for Order Delete
        function confirmOrderDelete(orderId, invoiceNo) {
            Swal.fire({
                title: 'Delete this order?',
                text: 'Order #' + invoiceNo + ' will be moved to trash and inventory stock will be returned.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, move to trash',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-order-form-' + orderId).submit();
                }
            });
        }

        /* ============ Update Payment Status =========== */
        $('#update_payment_status').on('change', function(){
            const order_id = {{ $order->id }};
            const status = $(this).val();

            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1200
                });

                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                        icon: 'success',
                        title: data.success || 'Payment status updated'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.error
                    });
                }
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: (xhr.responseJSON && xhr.responseJSON.error) || 'Failed to update payment status.'
                });
            });
        });

        /* ============ Update Delivery Status =========== */
        $('#update_delivery_status').on('change', function(){
            const order_id = {{ $order->id }};
            const status = $(this).val();

            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1200
                });

                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                        icon: 'success',
                        title: data.success || 'Delivery status updated'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.error
                    });
                }
            }).fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: (xhr.responseJSON && xhr.responseJSON.error) || 'Failed to update delivery status.'
                });
            });
        });

        /* ============ Location Ajax Dropdowns =========== */
        function capitalizeFirstLetter(string) {
            if (!string) return '';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        $('select[name="division_id"]').on('change', function(){
            const division_id = $(this).val();
            if(division_id) {
                $.ajax({
                    url: "{{ url('/division-district/ajax') }}/" + division_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('select[name="district_id"]').html('<option value="" selected="" disabled="">Select District</option>');
                        $.each(data, function(key, value){
                            $('select[name="district_id"]').append('<option value="'+ value.id +'">' + capitalizeFirstLetter(value.district_name_en) + '</option>');
                        });
                        $('select[name="upazilla_id"]').html('<option value="" selected="" disabled="">Select Upazilla</option>');
                    }
                });
            } else {
                $('select[name="district_id"]').html('<option value="">Select District</option>');
                $('select[name="upazilla_id"]').html('<option value="">Select Upazilla</option>');
            }
        });

        $('select[name="district_id"]').on('change', function(){
            const district_id = $(this).val();
            if(district_id) {
                $.ajax({
                    url: "{{ url('/district-upazilla/ajax') }}/" + district_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('select[name="upazilla_id"]').empty().append('<option value="" selected="" disabled="">Select Upazilla</option>');
                        $.each(data, function(key, value){
                            $('select[name="upazilla_id"]').append('<option value="'+ value.id +'">' + value.name_en + '</option>');
                        });
                    }
                });
            } else {
                $('select[name="upazilla_id"]').html('<option value="">Select Upazilla</option>');
            }
        });
    </script>
@endpush
