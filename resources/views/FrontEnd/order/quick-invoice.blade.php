<!doctype html>
<html lang="{{ session('language') === 'bangla' ? 'bn' : 'en' }}">
@php($label = fn($en, $bn) => session('language') === 'bangla' ? $bn : $en)
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Invoice {{ $order->invoice_no }} · Roh Fashion</title>
    <link rel="stylesheet" href="{{ asset('FrontEnd/assect/css/invoice.css') }}">
</head>
<body>
    <nav class="invoice-actions" aria-label="Invoice actions">
        <a href="{{ route('home') }}">← {{ $label('Continue shopping', 'কেনাকাটা চালিয়ে যান') }}</a>
        <button type="button" onclick="window.print()">{{ $label('Print / Save PDF', 'প্রিন্ট / PDF সেভ করুন') }}</button>
    </nav>
    <main class="invoice">
        <header class="invoice-header">
            <div><span class="brand">ROH FASHION</span><h1>{{ $label('Order invoice', 'অর্ডার ইনভয়েস') }}</h1><p>{{ $label('Your order has been placed successfully.', 'আপনার অর্ডার সফলভাবে সম্পন্ন হয়েছে।') }}</p></div>
            <div class="invoice-meta"><strong>{{ $order->invoice_no }}</strong><p>{{ $order->created_at->format('d M Y, h:i A') }}</p><span class="status">{{ ucfirst($order->delivery_status) }}</span></div>
        </header>
        <section class="invoice-address">
            <div><h2>{{ $label('Deliver to', 'ডেলিভারির ঠিকানা') }}</h2><strong>{{ $order->name }}</strong><p>{{ $order->phone }}</p><p>{{ $order->address }}</p></div>
            <div><h2>{{ $label('Payment & delivery', 'পেমেন্ট ও ডেলিভারি') }}</h2><strong>{{ $label('Cash on delivery', 'ক্যাশ অন ডেলিভারি') }}</strong><p>{{ $order->shipping_type == 1 ? $label('Inside Dhaka', 'ঢাকার ভিতরে') : $label('Outside Dhaka', 'ঢাকার বাইরে') }} · {{ $order->shipping_name }}</p><p>{{ $label('Payment due on delivery', 'পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন') }}</p></div>
        </section>
        <div class="invoice-table">
            <table>
                <thead><tr><th>{{ $label('Product', 'পণ্য') }}</th><th>{{ $label('Qty', 'পরিমাণ') }}</th><th>{{ $label('Price', 'দাম') }}</th><th>{{ $label('Total', 'মোট') }}</th></tr></thead>
                <tbody>
                    @foreach($order->order_details as $item)
                        <tr><td>{{ $item->product_name }}
                            @foreach(json_decode($item->variation ?: '[]', true) ?: [] as $variation)
                                <small>{{ $variation['attribute_name'] ?? '' }}: {{ $variation['attribute_value'] ?? '' }}</small>
                            @endforeach
                        </td><td>{{ $item->qty }}</td><td>৳{{ number_format($item->price, 2) }}</td><td>৳{{ number_format($item->price * $item->qty, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <section class="invoice-totals" aria-label="Order totals">
            <p><span>{{ $label('Subtotal', 'সাবটোটাল') }}</span><span>৳{{ number_format($order->sub_total, 2) }}</span></p>
            @if($order->discount > 0)<p><span>{{ $label('Discount', 'ছাড়') }}</span><span>−৳{{ number_format($order->discount, 2) }}</span></p>@endif
            <p><span>{{ $label('Delivery charge', 'ডেলিভারি চার্জ') }}</span><span>৳{{ number_format($order->shipping_charge, 2) }}</span></p>
            <p class="invoice-total"><strong>{{ $label('Grand total', 'সর্বমোট') }}</strong><strong>৳{{ number_format($order->grand_total, 2) }}</strong></p>
        </section>
        <footer>{{ $label('Keep this invoice number for tracking your order.', 'অর্ডার ট্র্যাক করার জন্য ইনভয়েস নম্বরটি সংরক্ষণ করুন।') }}</footer>
    </main>
</body>
</html>
