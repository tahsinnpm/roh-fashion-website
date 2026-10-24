@extends('FrontEnd.master')

@section('title')
    ইনভয়েস #{{ $order->invoice_no }} · Shikhuk Fashion
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('FrontEnd/assect/css/invoice.css') }}?v={{ filemtime(public_path('FrontEnd/assect/css/invoice.css')) }}">
@endpush

@section('content')
@php
    $phoneSetting = get_setting('phone')->value ?? '01838389494';
    $statusMap = [
        'pending'    => 'পেন্ডিং (প্রসেসিং চলছে)',
        'processing' => 'প্রসেসিং চলছে',
        'completed'  => 'সম্পন্ন',
        'cancelled'  => 'বাতিল',
        'shipped'    => 'ডেলিভারিতে পাঠানো হয়েছে',
    ];
    $displayStatus = $statusMap[strtolower($order->delivery_status ?? '')] ?? ($order->delivery_status ?: 'প্রসেসিং চলছে');
@endphp

<div class="quick-invoice-page">
    <div class="quick-invoice-wrapper">

        {{-- Top Success Card (Matching User Screenshot) --}}
        <div class="order-success-hero">
            <div class="order-success-icon" aria-hidden="true">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 class="order-success-title">
                ধন্যবাদ! আপনার অর্ডার পেয়েছি
            </h1>
            <p class="order-success-subtitle">
                Shikhuk Fashion-এর পাশে থাকার জন্য কৃতজ্ঞতা। আপনার অর্ডারের তথ্য আমাদের কাছে পৌঁছে গেছে।
            </p>
            <div class="order-success-status">
                <span class="status-pulse-dot"></span>
                <span>বর্তমান অবস্থা: {{ $displayStatus }}</span>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="invoice-actions" aria-label="Invoice actions">
            <a href="{{ route('home') }}" class="invoice-back-btn">
                <i class="fa-solid fa-arrow-left"></i>
                <span>কেনাকাটা চালিয়ে যান</span>
            </a>
            <button type="button" class="invoice-print-btn" onclick="window.print()">
                <i class="fa-solid fa-print"></i>
                <span>প্রিন্ট / PDF সেভ করুন</span>
            </button>
        </div>

        {{-- Section 1: Order Information (আপনার অর্ডারের তথ্য) --}}
        <div class="invoice-section-card">
            <h2 class="invoice-section-title">
                আপনার অর্ডারের তথ্য
            </h2>

            <div class="order-quick-summary-table">
                <div class="summary-row">
                    <span class="summary-label">অর্ডার নং:</span>
                    <strong class="summary-value order-num">#{{ $order->invoice_no }}</strong>
                </div>
                <div class="summary-row">
                    <span class="summary-label">নাম:</span>
                    <span class="summary-value">{{ $order->name }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">ফোন নম্বর:</span>
                    <span class="summary-value">{{ $order->phone }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">আইটেম:</span>
                    <div class="summary-value summary-items-list">
                        @foreach($order->order_details as $item)
                            <div class="summary-item-entry">
                                <strong>{{ $item->product_name }}</strong>
                                <span class="summary-item-qty">× {{ $item->qty }}</span>
                                @php($variations = json_decode($item->variation ?: '[]', true) ?: [])
                                @if(count($variations))
                                    <span class="summary-item-vars">
                                        ({{ implode(', ', array_map(fn($v) => ($v['attribute_name'] ?? '') . ': ' . ($v['attribute_value'] ?? ''), $variations)) }})
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="summary-row">
                    <span class="summary-label">পেমেন্ট:</span>
                    <span class="summary-value badge-cod">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                        ক্যাশ অন ডেলিভারি
                    </span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">মোট:</span>
                    <strong class="summary-value total-price">৳{{ number_format($order->grand_total, 2) }}</strong>
                </div>
                <div class="summary-row">
                    <span class="summary-label">ডেলিভারি ঠিকানা:</span>
                    <span class="summary-value">{{ $order->address }} ({{ $order->shipping_type == 1 ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }} - {{ $order->shipping_name }})</span>
                </div>
            </div>
        </div>

        {{-- Section 2: What Happens Next? (এরপর কী হবে?) --}}
        <div class="invoice-section-card next-steps-section">
            <h2 class="invoice-section-title">
                এরপর কী হবে?
            </h2>

            <div class="next-steps-grid">
                <div class="step-card">
                    <div class="step-badge">১</div>
                    <div class="step-content">
                        <h3 class="step-title">ফোনে কনফার্মেশন</h3>
                        <p class="step-desc">
                            আমাদের প্রতিনিধি আপনার নম্বরে ফোন করে অর্ডার কনফার্ম করবেন।
                        </p>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-badge">২</div>
                    <div class="step-content">
                        <h3 class="step-title">ডেলিভারি</h3>
                        <p class="step-desc">
                            কনফার্মের পর পণ্যগুলো আপনার ঠিকানায় পাঠানো হবে।
                        </p>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-badge">৩</div>
                    <div class="step-content">
                        <h3 class="step-title">ক্যাশ অন ডেলিভারি</h3>
                        <p class="step-desc">
                            পণ্য হাতে পেয়ে টাকা পরিশোধ করুন।
                        </p>
                    </div>
                </div>
            </div>

            @if($phoneSetting)
                <div class="support-callout">
                    <i class="fa-solid fa-headset"></i>
                    <span>যেকোনো প্রয়োজনে আমাদের কল করুন:</span>
                    <a href="tel:{{ $phoneSetting }}">{{ $phoneSetting }}</a>
                </div>
            @endif
        </div>

        {{-- Section 3: Detailed Printable Invoice (ইনভয়েস বিবরণী) --}}
        <div class="invoice">
            <header class="invoice-header">
                <div>
                    <span class="brand">Shikhuk Fashion</span>
                    <h2 class="invoice-title">অর্ডার ইনভয়েস</h2>
                    <p class="invoice-sub">আপনার কেনাকাটার অফিসিয়াল ইনভয়েস কপি।</p>
                </div>
                <div class="invoice-meta">
                    <strong>#{{ $order->invoice_no }}</strong>
                    <p>{{ $order->created_at->format('d M Y, h:i A') }}</p>
                    <span class="status">{{ $displayStatus }}</span>
                </div>
            </header>

            <section class="invoice-address">
                <div>
                    <h3>ডেলিভারির ঠিকানা</h3>
                    <strong>{{ $order->name }}</strong>
                    <p>{{ $order->phone }}</p>
                    <p>{{ $order->address }}</p>
                </div>
                <div>
                    <h3>পেমেন্ট ও ডেলিভারি</h3>
                    <strong>ক্যাশ অন ডেলিভারি</strong>
                    <p>{{ $order->shipping_type == 1 ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }} · {{ $order->shipping_name }}</p>
                    <p class="text-muted">পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন</p>
                </div>
            </section>

            <div class="invoice-table">
                <table>
                    <thead>
                        <tr>
                            <th>পণ্য</th>
                            <th>পরিমাণ</th>
                            <th>দাম</th>
                            <th>মোট</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->order_details as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @foreach(json_decode($item->variation ?: '[]', true) ?: [] as $variation)
                                        <small>{{ $variation['attribute_name'] ?? '' }}: {{ $variation['attribute_value'] ?? '' }}</small>
                                    @endforeach
                                </td>
                                <td>{{ $item->qty }}</td>
                                <td>৳{{ number_format($item->price, 2) }}</td>
                                <td>৳{{ number_format($item->price * $item->qty, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <section class="invoice-totals" aria-label="Order totals">
                <p><span>সাবটোটাল</span><span>৳{{ number_format($order->sub_total, 2) }}</span></p>
                @if($order->discount > 0)
                    <p><span>ছাড়</span><span>−৳{{ number_format($order->discount, 2) }}</span></p>
                @endif
                <p><span>ডেলিভারি চার্জ</span><span>৳{{ number_format($order->shipping_charge, 2) }}</span></p>
                <p class="invoice-total"><strong>সর্বমোট</strong><strong>৳{{ number_format($order->grand_total, 2) }}</strong></p>
            </section>

            <footer class="invoice-footer">
                অর্ডার ট্র্যাক করার জন্য এই ইনভয়েস নম্বরটি সংরক্ষণ করুন।
            </footer>
        </div>

    </div>
</div>
@endsection
