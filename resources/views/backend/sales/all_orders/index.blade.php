@extends('admin.admin_master')

@section('admin')
<style>
    .orders-page { --orders-ink: #172b4d; --orders-muted: #68778d; --orders-line: #e7edf4; --orders-accent: #2463eb; }
    .orders-page .content-header { margin-bottom: 1.25rem; }
    .orders-page .content-title { color: var(--orders-ink); margin-bottom: .25rem; }
    .orders-page .orders-subtitle { color: var(--orders-muted); margin: 0; }
    .orders-page .orders-card { border: 1px solid var(--orders-line); border-radius: 12px; box-shadow: 0 4px 18px rgba(23, 43, 77, .05); overflow: hidden; }
    .orders-page .orders-toolbar { padding: 1.1rem 1.25rem; border-bottom: 1px solid var(--orders-line); background: #fff; }
    .orders-page .orders-toolbar-title { color: var(--orders-ink); font-size: 1rem; font-weight: 700; margin: 0; }
    .orders-page .orders-count { color: var(--orders-muted); font-size: .85rem; }
    .orders-page .form-label { color: #46566d; font-size: .8rem; font-weight: 600; margin-bottom: .35rem; }
    .orders-page .form-select, .orders-page .form-control { min-height: 42px; border-color: #dce4ee; color: var(--orders-ink); border-radius: 8px; }
    .orders-page .form-select:focus, .orders-page .form-control:focus { border-color: #82a9ff; box-shadow: 0 0 0 .2rem rgba(36, 99, 235, .12); }
    .orders-page .orders-table { margin: 0; color: var(--orders-ink); }
    .orders-page .orders-table thead th { background: #f6f8fb; border-bottom: 1px solid var(--orders-line); color: #64748b; font-size: .73rem; font-weight: 700; letter-spacing: .045em; padding: .85rem 1rem; text-transform: uppercase; white-space: nowrap; }
    .orders-page .orders-table tbody td { border-color: #edf1f6; padding: .9rem 1rem; vertical-align: middle; }
    .orders-page .orders-table tbody tr:hover { background: #f9fbfe; }
    .orders-page .order-code { color: var(--orders-accent); font-weight: 700; white-space: nowrap; }
    .orders-page .customer-name { color: var(--orders-ink); font-weight: 600; }
    .orders-page .customer-phone { color: var(--orders-muted); font-size: .82rem; margin-top: .2rem; }
    .orders-page .order-products { min-width: 220px; max-width: 360px; }
    .orders-page .order-product-line { color: var(--orders-ink); font-size: .84rem; line-height: 1.45; }
    .orders-page .order-product-line + .order-product-line { border-top: 1px solid #edf1f6; margin-top: .35rem; padding-top: .35rem; }
    .orders-page .order-product-variant { color: var(--orders-muted); white-space: normal; }
    .orders-page .order-amount { color: var(--orders-ink); font-weight: 700; white-space: nowrap; }
    .orders-page .status-pill { display: inline-flex; align-items: center; border-radius: 999px; font-size: .76rem; font-weight: 600; padding: .35rem .65rem; white-space: nowrap; }
    .orders-page .status-pending { background: #fff5d6; color: #8a5b00; }
    .orders-page .status-confirmed, .orders-page .status-picked_up { background: #eaf2ff; color: #2456a6; }
    .orders-page .status-shipped, .orders-page .status-on_the_way { background: #e9f5ff; color: #17618d; }
    .orders-page .status-delivered { background: #e8f7ee; color: #207344; }
    .orders-page .status-cancelled { background: #fff0f0; color: #a53232; }
    .orders-page .payment-paid { background: #e8f7ee; color: #207344; }
    .orders-page .payment-unpaid { background: #fff5d6; color: #8a5b00; }
    .orders-page .order-action { align-items: center; border: 1px solid #dce4ee; border-radius: 8px; color: #42546e; display: inline-flex; height: 36px; justify-content: center; margin-left: .25rem; text-decoration: none; width: 36px; }
    .orders-page .order-action:hover { background: #edf4ff; border-color: #c8d9ff; color: var(--orders-accent); }
    .orders-page .orders-empty { color: var(--orders-muted); padding: 3rem 1rem !important; text-align: center; }
    .orders-page .orders-pagination { padding: 1rem 1.25rem; }
    @media (max-width: 767.98px) { .orders-page .orders-toolbar { padding: 1rem; } .orders-page .orders-table thead th, .orders-page .orders-table tbody td { padding: .75rem; } }
</style>

<section class="content-main orders-page">
    <div class="content-header">
        <div>
            <h2 class="content-title">Orders</h2>
        </div>
    </div>

    <div class="card orders-card">
        <div class="orders-toolbar">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h3 class="orders-toolbar-title">Order list</h3>
                <span class="orders-count">{{ number_format($orders->total()) }} orders</span>
            </div>
            <form action="{{ route('all_orders.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label" for="delivery_status">Delivery status</label>
                    <select class="form-select" name="delivery_status" id="delivery_status">
                        <option value="">All delivery statuses</option>
                        @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'shipped' => 'Shipped', 'picked_up' => 'Picked up', 'on_the_way' => 'On the way', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $value => $label)
                            <option value="{{ $value }}" @if($delivery_status == $value) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label" for="payment_status">Payment status</label>
                    <select class="form-select" name="payment_status" id="payment_status">
                        <option value="">All payment statuses</option>
                        <option value="1" @if((string) $payment_status === '1') selected @endif>Paid</option>
                        <option value="0" @if((string) $payment_status === '0' && $payment_status !== '') selected @endif>Unpaid</option>
                    </select>
                </div>
                <div class="col-12 col-sm-8 col-lg-3">
                    <label class="form-label" for="reportrange">Order date</label>
                    <input type="text" id="reportrange" class="form-control" name="date" placeholder="Choose date range" value="{{ request('date') }}" autocomplete="off">
                </div>
                <div class="col-12 col-sm-4 col-lg-3 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1" type="submit"><i class="fa fa-filter me-1"></i> Filter</button>
                    <a class="btn btn-light border" href="{{ route('all_orders.index') }}">Clear</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table orders-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Products</th>
                        <th>Amount</th>
                        <th>Delivery</th>
                        <th>Payment</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $deliveryStatus = $order->delivery_status ?: 'pending';
                            $deliveryLabel = ucwords(str_replace('_', ' ', $deliveryStatus));
                        @endphp
                        <tr>
                            <td><span class="order-code">{{ $order->invoice_no }}</span></td>
                            <td>
                                <div class="customer-name">{{ $order->name }}</div>
                                @if($order->phone)
                                    <div class="customer-phone"><i class="fa fa-phone me-1"></i>{{ $order->phone }}</div>
                                @endif
                            </td>
                            <td class="order-products">
                                @forelse($order->order_details as $detail)
                                    @php
                                        $rawVariation = $detail->variation;
                                        $decodedVariation = is_string($rawVariation) ? json_decode($rawVariation, true) : null;
                                        $variantNames = [];
                                        if (is_array($decodedVariation)) {
                                            $variationItems = array_key_exists('attribute_value', $decodedVariation)
                                                ? [$decodedVariation]
                                                : $decodedVariation;
                                            foreach ($variationItems as $variant) {
                                                $variantName = is_array($variant)
                                                    ? ($variant['attribute_value'] ?? $variant['varient'] ?? $variant['value'] ?? '')
                                                    : $variant;
                                                if (is_scalar($variantName) && trim((string) $variantName) !== '') $variantNames[] = trim((string) $variantName);
                                            }
                                        } elseif (is_string($rawVariation) && trim($rawVariation) !== '') {
                                            $variantNames[] = trim($rawVariation);
                                        }
                                        $variantLabel = implode(', ', array_unique($variantNames));
                                    @endphp
                                    <div class="order-product-line">
                                        {{ $detail->product_name ?: optional($detail->product)->name_en ?: 'Product' }}@if($variantLabel) <span class="order-product-variant">({{ $variantLabel }})</span>@endif
                                    </div>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td class="order-amount">৳ {{ number_format((float) $order->grand_total, 2) }}</td>
                            <td><span class="status-pill status-{{ $deliveryStatus }}">{{ $deliveryLabel }}</span></td>
                            <td>
                                <span class="status-pill {{ (string) $order->payment_status === '1' ? 'payment-paid' : 'payment-unpaid' }}">
                                    {{ (string) $order->payment_status === '1' ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a class="order-action" href="{{ route('all_orders.show', $order->id) }}" title="View order" aria-label="View order">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a class="order-action" href="{{ route('invoice.download', $order->id) }}" title="Download invoice" aria-label="Download invoice">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="orders-empty">No orders found for these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="orders-pagination">{{ $orders->appends(request()->query())->links() }}</div>
        @endif
    </div>
</section>

@push('footer-script')
<script>
    $(function () {
        var $date = $('#reportrange');
        if (!$date.length || !$.fn.daterangepicker) return;

        $date.daterangepicker({
            autoUpdateInput: false,
            locale: { format: 'MM/DD/YYYY', cancelLabel: 'Clear' },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $date.on('apply.daterangepicker', function (event, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });
        $date.on('cancel.daterangepicker', function () { $(this).val(''); });
    });
</script>
@endpush
@endsection
