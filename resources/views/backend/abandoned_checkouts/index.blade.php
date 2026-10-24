@extends('admin.admin_master')

@section('admin')
<style>
    .abandoned-page { --ink:#172b4d; --muted:#68778d; --line:#e7edf4; color:var(--ink); }
    .abandoned-card { border:1px solid var(--line); border-radius:12px; box-shadow:0 4px 18px rgba(23,43,77,.05); overflow:hidden; }
    .abandoned-toolbar { padding:1rem 1.25rem; border-bottom:1px solid var(--line); }
    .abandoned-table { margin:0; }
    .abandoned-table th { background:#f6f8fb; color:#64748b; font-size:.73rem; letter-spacing:.04em; text-transform:uppercase; white-space:nowrap; }
    .abandoned-table th,.abandoned-table td { padding:.85rem 1rem; vertical-align:middle; border-color:#edf1f6; }
    .abandoned-muted { color:var(--muted); font-size:.82rem; }
    .abandoned-items { margin:0; padding-left:1.1rem; min-width:180px; }
    .abandoned-pill { display:inline-block; padding:.3rem .6rem; border-radius:999px; font-size:.75rem; font-weight:700; text-transform:capitalize; background:#fff5d6; color:#8a5b00; }
    .abandoned-pill.contacted { background:#eaf2ff; color:#2456a6; }
    .abandoned-pill.closed { background:#f1f3f6; color:#566477; }
    .abandoned-pill.converted { background:#e8f7ee; color:#207344; }
    @media(max-width:767px) { .abandoned-table th,.abandoned-table td { padding:.7rem; } }
</style>

<section class="content-main abandoned-page">
    <div class="content-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div><h2 class="content-title mb-1">Abandoned Checkouts</h2><p class="abandoned-muted mb-0">Phone numbers saved after customers started checkout without placing an order.</p></div>
    </div>

    <div class="card abandoned-card mt-3">
        <div class="abandoned-toolbar d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong>{{ number_format($checkouts->total()) }} checkout records</strong>
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Phone, name, landing page" aria-label="Search checkout records">
                <select class="form-select" name="status" aria-label="Filter by status">
                    <option value="">All statuses</option>
                    @foreach(['pending', 'contacted', 'closed', 'converted'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary" type="submit">Filter</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table abandoned-table">
                <thead><tr><th>Customer</th><th>Cart / product</th><th>Source</th><th>Last activity</th><th>Status</th><th>Follow-up</th></tr></thead>
                <tbody>
                @forelse($checkouts as $checkout)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $checkout->name ?: 'Name not provided' }}</div>
                            <a href="tel:{{ $checkout->phone }}">{{ $checkout->phone }}</a>
                            @if($checkout->email)<div class="abandoned-muted">{{ $checkout->email }}</div>@endif
                            @if($checkout->address)<div class="abandoned-muted">{{ $checkout->address }}</div>@endif
                        </td>
                        <td>
                            @if($checkout->cart_snapshot)
                                <ul class="abandoned-items">
                                    @foreach($checkout->cart_snapshot as $item)
                                        <li>{{ $item['name'] ?? 'Product' }} × {{ $item['quantity'] ?? 1 }}@if(!empty($item['variant'])) <span class="abandoned-muted">({{ $item['variant'] }})</span>@endif</li>
                                    @endforeach
                                </ul>
                            @else<span class="abandoned-muted">No cart details</span>@endif
                        </td>
                        <td>{{ $checkout->source === 'dynamic_landing' ? 'Dynamic landing page' : 'Quick checkout' }}@if($checkout->landing_slug)<div class="abandoned-muted">{{ $checkout->landing_slug }}</div>@endif</td>
                        <td>{{ optional($checkout->last_activity_at)->format('d M Y, h:i A') ?: $checkout->updated_at->format('d M Y, h:i A') }}</td>
                        <td><span class="abandoned-pill {{ $checkout->status }}">{{ $checkout->status }}</span>@if($checkout->order_id)<div class="abandoned-muted">Order #{{ $checkout->order_id }}</div>@endif</td>
                        <td>
                            @if($checkout->status !== 'converted')
                                <a class="btn btn-sm btn-outline-success mb-1" target="_blank" rel="noopener" href="https://wa.me/88{{ preg_replace('/\D/', '', $checkout->phone) }}">WhatsApp</a>
                                <form method="POST" action="{{ route('abandoned-checkouts.status', $checkout) }}" class="d-flex gap-1">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm" aria-label="Update follow-up status">
                                        <option value="contacted" @selected($checkout->status === 'contacted')>Contacted</option>
                                        <option value="closed" @selected($checkout->status === 'closed')>Close lead</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                </form>
                            @else<span class="abandoned-muted">Order placed</span>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No checkout records found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $checkouts->links() }}</div>
    </div>
</section>
@endsection
