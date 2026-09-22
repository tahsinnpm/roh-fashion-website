@extends('FrontEnd.master')

@section('content')
<section class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="row g-4 flex-lg-nowrap">
            <!-- Sidebar -->
            <nav class="col-12 col-lg-3 mb-3 mb-lg-0" aria-label="Affiliate sidebar">
                <div class="mobile-header d-lg-none mb-3">
                    <button id="sidebarToggleBtn" class="mobile-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#affiliateSidebar" aria-expanded="false" aria-controls="affiliateSidebar">
                        <i class="bi bi-grid-3x3"></i> Menu
                    </button>
                </div>
                <div class="collapse d-lg-block" id="affiliateSidebar">
                    <div class="sidebar">
                        <a href="{{ route('affiliate.dashboard') }}" class="sidebar-item active">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="#" class="sidebar-item">
                            <i class="bi bi-person-circle"></i>
                            <span>Profile</span>
                        </a>
                        <a href="#" class="sidebar-item">
                            <i class="bi bi-cart3"></i>
                            <span>Orders</span>
                        </a>
                        <a href="#" class="sidebar-item logout-item">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </nav>
            <style>
                /* Sidebar styles copied from update-profile.html */
                .sidebar {
                    background: #fff;
                    border-radius: 18px;
                    box-shadow: 0 2px 16px rgba(44, 62, 80, 0.06);
                    padding: 1.5rem 1rem;
                    display: flex;
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.5rem;
                }
                .sidebar-item {
                    display: flex;
                    align-items: center;
                    gap: 0.8rem;
                    padding: 0.8rem 1.2rem;
                    border-radius: 10px;
                    color: #6c757d;
                    background: none;
                    border: none;
                    font-weight: 500;
                    font-size: 1rem;
                    transition: background 0.2s, color 0.2s;
                    text-decoration: none;
                    width: 100%;
                }
                .sidebar-item.active, .sidebar-item:hover {
                    background: #ffe6e1; /* lighter shade for #DC452C */
                    color: #DC452C;
                }
                .sidebar-item i {
                    font-size: 1.2rem;
                }
                .logout-item {
                    color: #e74c3c !important;
                }
                .logout-item:hover {
                    background: #e74c3c !important;
                    color: #fff !important;
                }
                .mobile-header {
                    display: none;
                }
                .mobile-toggle {
                    background: #DC452C;
                    border: none;
                    color: #fff;
                    border-radius: 10px;
                    padding: 0.6rem 1.2rem;
                    font-weight: 500;
                    width: 100%;
                    transition: background 0.2s;
                }
                .mobile-toggle:hover, .mobile-toggle:focus {
                    background: #b33622;
                    color: #fff;
                }
                @media (max-width: 991.98px) {
                    .mobile-header {
                        display: block;
                        background: #fff;
                        border-radius: 12px;
                        padding: 0.7rem 1rem;
                        margin-bottom: 1rem;
                        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.06);
                    }
                    .sidebar {
                        flex-direction: row;
                        justify-content: space-around;
                        align-items: center;
                        gap: 0;
                        padding: 1rem 0.5rem;
                    }
                    .sidebar-item span {
                        display: none;
                    }
                    .sidebar-item i {
                        font-size: 1.5rem;
                    }
                }
            </style>

            <!-- Main Content -->
            <div class="col-12 col-lg-9">
                <div class="card border-0 shadow-sm p-4 mb-4 rounded-4 bg-white">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0">
                            <span class="avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.5rem;">
                                {{ strtoupper(substr(auth('affiliate')->user()->name,0,1)) }}
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-0">Welcome, <span class="fw-bold">{{ auth('affiliate')->user()->name }}</span>!</h4>
                            <small class="text-muted">Affiliate Dashboard</small>
                        </div>
                    </div>

                    <!-- Referral Link -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2">Your Referral Link</label>
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control bg-light border-0" id="referralLinkInput" readonly value="{{ url('/') }}?ref={{ auth('affiliate')->user()->referral_code }}">
                            <button class="btn btn-primary text-white" onclick="copyToClipboard(document.getElementById('referralLinkInput').value)">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>
                    </div>

                    <!-- Summary Statistics -->
                    <!-- <div class="row text-center g-3">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm py-4 rounded-3 bg-gradient bg-primary bg-opacity-10">
                                <div class="mb-2">
                                    <span class="badge bg-primary bg-opacity-75 fs-6 mb-2"><i class="bi bi-people"></i></span>
                                </div>
                                <h6 class="fw-semibold text-white">Total Referrals</h6>
                                <h3 class="fw-bold text-white mb-0">{{ $totalReferrals ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm py-4 rounded-3 bg-gradient bg-success bg-opacity-10">
                                <div class="mb-2">
                                    <span class="badge bg-success bg-opacity-75 fs-6 mb-2"><i class="bi bi-cash-coin"></i></span>
                                </div>
                                <h6 class="fw-semibold text-success">Total Commissions</h6>
                                <h3 class="fw-bold text-success mb-0">৳{{ number_format($totalCommissions ?? 0, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm py-4 rounded-3 bg-gradient bg-warning bg-opacity-10">
                                <div class="mb-2">
                                    <span class="badge bg-warning bg-opacity-75 fs-6 mb-2"><i class="bi bi-hourglass-split"></i></span>
                                </div>
                                <h6 class="fw-semibold text-warning">Pending Referrals</h6>
                                <h3 class="fw-bold text-warning mb-0">{{ $pendingReferrals ?? 0 }}</h3>
                            </div>
                        </div>
                    </div> -->
                </div>

                <!-- Affiliate Products Section -->
                <div class="card border-0 shadow p-3 p-md-4 rounded-4 bg-white">
                    <h5 class="mb-4 fw-bold">My Affiliate Products</h5>
                    <div class="d-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
                        @forelse($products as $product)
                        @php
                        $referralLink = url('/product-details/'.$product->slug.'?ref='.auth('affiliate')->user()->referral_code);
                        @endphp
                        <div>
                            <div class="card border-0 shadow-sm h-100 rounded-3 position-relative">
                                <img src="{{ asset($product->product_thumbnail) }}" alt="{{ $product->name_en }}" class="card-img-top rounded-top-3" style="height: 140px; object-fit: cover;">
                                @if($product->discount_price)
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">Sale</span>
                                @endif
                                <div class="card-body d-flex flex-column p-2">
                                    <h6 class="card-title mb-1" style="font-size:0.95rem;">{{ \Illuminate\Support\Str::limit($product->name_en, 38) }}</h6>
                                    <div class="mb-2">
                                        @if($product->discount_price)
                                        @php $data = calculateDiscount($product->id); @endphp
                                        <span class="fw-bold text-danger">৳{{ number_format($data['discount'], 2) }}</span>
                                        <small class="text-muted ms-1"><del>৳{{ number_format($product->regular_price, 2) }}</del></small>
                                        @else
                                        <span class="fw-bold">৳{{ number_format($product->regular_price, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="input-group input-group-sm mt-auto">
                                        <input type="text" class="form-control bg-light border-0" readonly value="{{ $referralLink }}">
                                        <button class="btn btn-outline-primary" onclick="copyToClipboard('{{ $referralLink }}')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div style="grid-column: 1 / -1;">
                            <div class="alert alert-info text-center mb-0 rounded-3">
                                No affiliate products available right now.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Clipboard Script -->
<script>
    function copyToClipboard(value) {
        const temp = document.createElement("input");
        temp.value = value;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand("copy");
        document.body.removeChild(temp);
        // Use a toast or a less intrusive notification in production
        alert("Referral link copied to clipboard!");
    }

    // Sidebar toggle icon/text change (match profile.html)
    document.addEventListener('DOMContentLoaded', function () {
        var sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        var sidebar = document.getElementById('affiliateSidebar');
        var bsCollapse = null;

        if (sidebarToggleBtn && sidebar) {
            sidebar.addEventListener('show.bs.collapse', function () {
                sidebarToggleBtn.innerHTML = '<i class="bi bi-x-lg"></i> Close';
            });
            sidebar.addEventListener('hide.bs.collapse', function () {
                sidebarToggleBtn.innerHTML = '<i class="bi bi-grid-3x3"></i> Menu';
            });
        }
    });
</script>

<!-- Bootstrap 5 JS for collapse functionality (if not already included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Optionally include Bootstrap Icons CDN for icons used above -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@endsection