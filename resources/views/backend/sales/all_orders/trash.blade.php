@extends('admin.admin_master')

@section('admin')
<section class="content-main">
    <div class="content-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="content-title card-title">Order Trash</h2>
            <p class="text-muted mb-0">Orders are permanently removed 30 days after being moved here.</p>
        </div>
        <a href="{{ route('all_orders.index') }}" class="btn btn-light">Back to orders</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Deleted at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->invoice_no }}</td>
                            <td>{{ $order->name }}<br><small>{{ $order->phone }}</small></td>
                            <td>{{ $order->order_details->sum('qty') }}</td>
                            <td>{{ number_format((float) $order->grand_total, 2) }}</td>
                            <td>{{ $order->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="text-nowrap">
                                <form action="{{ route('orders.restore', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success" type="submit">Restore</button>
                                </form>
                                <form action="{{ route('orders.force-delete', $order->id) }}" method="POST" class="d-inline" data-order-force-delete-form>
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Delete permanently</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Trash is empty.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>
    </div>
</section>
@endsection
