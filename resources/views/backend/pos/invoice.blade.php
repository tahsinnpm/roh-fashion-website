@extends('admin.admin_master')
@section('admin')
<div class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">POS Invoice #{{ $order->invoice_no }}</h2>
            <p>Order Placed & Confirmed: {{ date('d M Y, h:i A', strtotime($order->created_at)) }}</p>
        </div>
        <div>
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to POS
            </a>
            <a href="{{ route('pos.invoicePrint', $order->id) }}" target="_blank" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> Thermal Print (80mm)
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <h4 class="fw-bold mb-1">{{ get_setting('site_name')->value ?? 'Shinduk Fashion' }}</h4>
                        <p class="text-muted small mb-0">{{ get_setting('business_address')->value ?? 'Dhaka, Bangladesh' }}</p>
                        <p class="text-muted small mb-0">Phone: {{ get_setting('phone')->value ?? '' }}</p>
                        <span class="badge bg-success mt-2">DELIVERY STATUS: CONFIRMED</span>
                        <span class="badge {{ $order->payment_status ? 'bg-primary' : 'bg-warning text-dark' }} mt-2">{{ $order->payment_status ? 'PAID' : 'PAYMENT DUE' }}</span>
                    </div>

                    <div class="row mb-3 small">
                        <div class="col-6">
                            <strong>Invoice:</strong> {{ $order->invoice_no }}<br>
                            <strong>Date:</strong> {{ date('d-m-Y h:i A', strtotime($order->created_at)) }}<br>
                            <strong>Payment:</strong> {{ strtolower($order->payment_method) === 'cod' ? 'Cash on Delivery' : strtoupper($order->payment_method) }}
                        </div>
                        <div class="col-6 text-end">
                            @if($order->name)
                                <strong>Customer:</strong> {{ $order->name }}<br>
                            @endif
                            @if($order->phone)
                                <strong>Phone:</strong> {{ $order->phone }}<br>
                            @endif
                            @if($order->address)
                                <strong>Address:</strong> {{ $order->address }}
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center" style="width: 70px;">Qty</th>
                                    <th class="text-end" style="width: 100px;">Price</th>
                                    <th class="text-end" style="width: 100px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->order_details as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->variation)
                                            <div class="text-muted small">({{ $item->variation }})</div>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">৳ {{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">৳ {{ number_format($item->price * $item->qty, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-7">
                            <div class="d-flex justify-content-between py-1">
                                <span>Sub Total:</span>
                                <strong>৳ {{ number_format($order->sub_total, 2) }}</strong>
                            </div>
                            @if((float) $order->shipping_charge > 0)
                            <div class="d-flex justify-content-between py-1">
                                <span>{{ $order->shipping_name ?: 'Shipping' }}:</span>
                                <strong>৳ {{ number_format($order->shipping_charge, 2) }}</strong>
                            </div>
                            @endif
                            @if($order->discount > 0)
                            <div class="d-flex justify-content-between py-1 text-danger">
                                <span>Discount:</span>
                                <strong>- ৳ {{ number_format($order->discount, 2) }}</strong>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between py-2 border-top border-bottom fs-5 fw-bold text-dark">
                                <span>Grand Total:</span>
                                <span>৳ {{ number_format($order->grand_total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 mt-1 text-muted small">
                                <span>Due on Delivery:</span>
                                <span>৳ {{ number_format($order->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top text-muted small">
                        <p class="mb-0">Thank you for shopping with Shinduk Fashion!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
