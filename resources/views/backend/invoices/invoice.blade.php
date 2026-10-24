<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Invoice {{ $order->invoice_no }}</title>
    <style>
        @page { margin: 12mm; }
        body { color: #243247; font-family: freeserif, sans-serif; font-size: 10pt; line-height: 1.5; }
        table { border-collapse: collapse; width: 100%; }
        .header { background: #f7f8fb; border-top: 4px solid #d82f70; padding: 18px 20px; }
        .header-grid td { vertical-align: top; }
        .brand-logo { max-height: 62px; max-width: 180px; }
        .brand-name { color: #d82f70; font-size: 17pt; font-weight: bold; }
        .muted { color: #68778d; }
        .invoice-title { color: #172b4d; font-size: 23pt; font-weight: bold; letter-spacing: 1px; text-align: right; }
        .invoice-number { color: #d82f70; font-size: 11pt; font-weight: bold; text-align: right; }
        .meta { color: #526176; font-size: 9pt; text-align: right; }
        .section { margin-top: 22px; }
        .section-title { color: #68778d; font-size: 8pt; font-weight: bold; letter-spacing: .7px; margin-bottom: 5px; text-transform: uppercase; }
        .customer-name { color: #172b4d; font-size: 12pt; font-weight: bold; }
        .customer-details { color: #526176; font-size: 9pt; }
        .items { margin-top: 20px; }
        .items th { background: #172b4d; color: #fff; font-size: 8.5pt; font-weight: bold; padding: 9px 8px; text-align: left; }
        .items td { border-bottom: 1px solid #e8edf3; padding: 9px 8px; vertical-align: top; }
        .items .number { text-align: right; white-space: nowrap; }
        .product-name { color: #172b4d; font-weight: bold; }
        .variant { color: #68778d; font-size: 8.5pt; }
        .totals-wrap { margin-top: 18px; }
        .totals { width: 48%; margin-left: auto; }
        .totals td { padding: 5px 8px; }
        .totals .label { color: #68778d; }
        .totals .grand-total td { border-top: 2px solid #d82f70; color: #172b4d; font-size: 12pt; font-weight: bold; padding-top: 9px; }
        .footer { border-top: 1px solid #e8edf3; color: #68778d; font-size: 8pt; margin-top: 30px; padding-top: 10px; text-align: center; }
    </style>
</head>
<body>
    @php
        $brandSetting = get_setting('site_name');
        $logoSetting = get_setting('site_favicon');
        $logoPath = $logoSetting && $logoSetting->value ? public_path($logoSetting->value) : null;
        $logoMime = $logoPath && is_file($logoPath) && function_exists('mime_content_type') ? mime_content_type($logoPath) : 'image/png';
        $invoiceDate = $order->date
            ? (is_numeric($order->date) ? date('d M Y', (int) $order->date) : \Illuminate\Support\Carbon::parse($order->date)->format('d M Y'))
            : optional($order->created_at)->format('d M Y');
    @endphp

    <div class="header">
        <table class="header-grid">
            <tr>
                <td width="54%">
                    @if($logoPath && is_file($logoPath))
                        <img class="brand-logo" src="data:{{ $logoMime }};base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="{{ $brandSetting->value ?? config('app.name') }}">
                    @else
                        <div class="brand-name">{{ $brandSetting->value ?? config('app.name') }}</div>
                    @endif
                </td>
                <td width="46%">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">{{ $order->invoice_no }}</div>
                    <div class="meta">Date: {{ $invoiceDate }}</div>
                    <div class="meta">Payment: {{ strtoupper($order->payment_method ?: 'N/A') }}</div>
                </td>
            </tr>
        </table>
        <div class="meta" style="text-align:left; margin-top:10px;">
            @if($order->email) Email: {{ $order->email }} @endif
            @if($order->phone) &nbsp;&nbsp; Phone: {{ $order->phone }} @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Bill to</div>
        <div class="customer-name">{{ $order->name }}</div>
        <div class="customer-details">
            @if($order->address)
                {{ $order->address }}<br>
            @elseif($order->order_type == 1 && ($order->upazilla || $order->district || $order->division))
                {{ ucwords($order->upazilla->name_en ?? '') }}, {{ ucwords($order->district->district_name_en ?? '') }}, {{ ucwords($order->division->division_name_en ?? '') }}<br>
            @endif
            @if($order->phone) Phone: {{ $order->phone }} @endif
            @if($order->email) &nbsp;&nbsp; {{ $order->email }} @endif
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th width="49%">Product</th>
                <th width="10%" class="number">Qty</th>
                <th width="19%" class="number">Unit price</th>
                <th width="22%" class="number">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->order_details as $orderDetail)
                @php
                    $productName = optional($orderDetail->product)->name_en ?: ($orderDetail->product_name ?: 'Product');
                    $variantText = '';
                    if ($order->order_type == 1 && $orderDetail->is_varient) {
                        $variantRows = json_decode($orderDetail->variation, true);
                        if (is_array($variantRows)) {
                            $variantText = collect($variantRows)->map(function ($variant) {
                                $name = trim((string) ($variant['attribute_name'] ?? ''));
                                $value = trim((string) ($variant['attribute_value'] ?? ''));
                                return $name !== '' ? $name . ': ' . $value : $value;
                            })->filter()->implode(' · ');
                        }
                    } elseif ($order->order_type != 1) {
                        $variantText = (string) $orderDetail->variation;
                    }
                @endphp
                <tr>
                    <td>
                        <div class="product-name">{{ $productName }}</div>
                        @if($variantText !== '') <div class="variant">{{ $variantText }}</div> @endif
                    </td>
                    <td class="number">{{ $orderDetail->qty }}</td>
                    <td class="number">{{ number_format((float) $orderDetail->price, 2) }}</td>
                    <td class="number">{{ number_format((float) $orderDetail->price * (int) $orderDetail->qty, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-wrap">
        <table class="totals">
            <tbody>
                <tr><td class="label">Subtotal</td><td class="number">{{ number_format((float) $order->sub_total, 2) }}</td></tr>
                <tr><td class="label">Shipping</td><td class="number">{{ number_format((float) $order->shipping_charge, 2) }}</td></tr>
                <tr><td class="label">Discount</td><td class="number">− {{ number_format((float) $order->discount, 2) }}</td></tr>
                <tr class="grand-total"><td>Grand total (BDT)</td><td class="number">{{ number_format((float) $order->grand_total, 2) }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="footer">Thank you for shopping with {{ $brandSetting->value ?? config('app.name') }}.</div>
</body>
</html>
