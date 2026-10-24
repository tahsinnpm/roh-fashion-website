<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Receipt - {{ $order->invoice_no }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace, 'Segoe UI', Tahoma, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .no-print-bar {
            width: 80mm;
            max-width: 100%;
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .btn {
            flex: 1;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-print {
            background: #2563eb;
            color: #fff;
        }

        .btn-back {
            background: #e2e8f0;
            color: #1e293b;
        }

        .receipt {
            width: 80mm;
            max-width: 100%;
            background: #ffffff;
            padding: 16px 14px;
            color: #000000;
            font-size: 12px;
            line-height: 1.4;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            border-radius: 4px;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }

        .store-name {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .store-info {
            font-size: 11px;
            color: #222;
        }

        .receipt-title {
            margin-top: 6px;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .receipt-meta {
            font-size: 11px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-bottom: 10px;
        }

        .receipt-table th {
            text-align: left;
            padding: 4px 0;
            border-bottom: 1px solid #000;
            font-size: 11px;
        }

        .receipt-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .receipt-table .text-right {
            text-align: right;
        }

        .receipt-table .text-center {
            text-align: center;
        }

        .item-variant {
            font-size: 10px;
            color: #444;
            display: block;
        }

        .receipt-summary {
            border-top: 1px dashed #000;
            padding-top: 8px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .summary-row.total-row {
            font-size: 15px;
            font-weight: 900;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 6px 0;
            margin-top: 6px;
        }

        .badge-status {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 2px;
            text-transform: uppercase;
        }

        .receipt-footer {
            text-align: center;
            font-size: 11px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .receipt {
                width: 100% !important;
                box-shadow: none;
                padding: 4px;
                border-radius: 0;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button class="btn btn-print" onclick="window.print()">🖨️ Print Receipt</button>
        <a class="btn btn-back" href="{{ route('pos.index') }}">← Back to POS</a>
    </div>

    <div class="receipt">
        <div class="receipt-header">
            <div class="store-name">{{ get_setting('site_name')->value ?? 'Shinduk Fashion' }}</div>
            <div class="store-info">{{ get_setting('business_address')->value ?? 'Dhaka, Bangladesh' }}</div>
            <div class="store-info">Phone: {{ get_setting('phone')->value ?? '' }}</div>
            <div class="receipt-title">** POS RECEIPT **</div>
        </div>

        <div class="receipt-meta">
            <div class="meta-row">
                <span>Invoice No:</span>
                <strong>{{ $order->invoice_no }}</strong>
            </div>
            <div class="meta-row">
                <span>Date:</span>
                <span>{{ date('d-m-Y h:i A', strtotime($order->created_at)) }}</span>
            </div>
            @if($order->name)
            <div class="meta-row">
                <span>Customer:</span>
                <strong>{{ $order->name }}</strong>
            </div>
            @endif
            @if($order->phone && $order->phone !== '00000000000')
            <div class="meta-row">
                <span>Phone:</span>
                <span>{{ $order->phone }}</span>
            </div>
            @endif
            @if($order->address && $order->address !== 'Store Pickup')
            <div class="meta-row">
                <span>Address:</span>
                <span>{{ $order->address }}</span>
            </div>
            @endif
            <div class="meta-row">
                <span>Status:</span>
                <span class="badge-status">CONFIRMED ({{ $order->payment_status ? 'PAID' : 'PAYMENT DUE' }})</span>
            </div>
        </div>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Item</th>
                    <th class="text-center" style="width: 15%;">Qty</th>
                    <th class="text-right" style="width: 15%;">Price</th>
                    <th class="text-right" style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->order_details as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->variation)
                            <span class="item-variant">({{ $item->variation }})</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->price * $item->qty, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="receipt-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>৳ {{ number_format($order->sub_total, 2) }}</span>
            </div>
            @if((float) $order->shipping_charge > 0)
            <div class="summary-row">
                <span>{{ $order->shipping_name ?: 'Shipping' }}:</span>
                <span>৳ {{ number_format($order->shipping_charge, 2) }}</span>
            </div>
            @endif
            @if($order->discount > 0)
            <div class="summary-row" style="font-weight: bold;">
                <span>Discount:</span>
                <span>- ৳ {{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            <div class="summary-row total-row">
                <span>GRAND TOTAL:</span>
                <span>৳ {{ number_format($order->grand_total, 2) }}</span>
            </div>
            <div class="summary-row" style="margin-top: 6px;">
                <span>Payment Method:</span>
                <strong>{{ strtolower($order->payment_method ?? '') === 'cod' ? 'Cash on Delivery' : strtoupper($order->payment_method ?: 'Cash') }}</strong>
            </div>
            <div class="summary-row">
                <span>Due on Delivery:</span>
                <span>৳ {{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>

        <div class="receipt-footer">
            <p>Thank you for shopping with us!</p>
            <p>Please visit us again.</p>
            <p style="font-size: 9px; margin-top: 4px; color: #555;">Developed by Classic IT & Sky Mart Ltd.</p>
        </div>
    </div>

    <script>
        // Auto trigger print when loaded
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
