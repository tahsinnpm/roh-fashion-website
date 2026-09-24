@extends('admin.admin_master')
@section('admin')
<section class="content-main">
    <div class="content-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="content-title card-title">Landing Pages</h2>
            <p class="text-muted">Manage all custom product landing pages</p>
        </div>
        <div>
            <a href="{{ route('landing.page.create') }}" class="btn btn-primary">
                <i class="fa fa-plus me-1"></i> Create Landing Page
            </a>
        </div>
    </div>

    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 50px;">#</th>
                            <th scope="col" style="width: 80px;">Image</th>
                            <th scope="col">Page Title / Slug</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Sale Price</th>
                            <th scope="col">Delivery (In / Out)</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center" style="width: 170px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($landing_pages as $key => $page)
                        @php
                            $thumb = null;
                            if (!empty($page->slider_images) && is_array($page->slider_images) && count($page->slider_images) > 0) {
                                $thumb = $page->slider_images[0];
                            } elseif ($page->overview_image) {
                                $thumb = $page->overview_image;
                            } elseif ($page->product && $page->product->product_thumbnail) {
                                $thumb = $page->product->product_thumbnail;
                            }
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <img src="{{ $thumb ? asset($thumb) : asset('upload/no_image.jpg') }}" 
                                     alt="{{ $page->title }}" 
                                     class="img-thumbnail" 
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $page->title }}</div>
                                <div class="mt-1 d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-secondary border">/landing/{{ $page->slug }}</span>
                                    <a href="{{ route('landing.dynamic', $page->slug) }}" target="_blank" class="text-primary small" title="View Landing Page">
                                        <i class="fa fa-external-link-alt"></i> Open Link
                                    </a>
                                </div>
                            </td>
                            <td>
                                @if($page->product)
                                    <div class="fw-semibold text-secondary">{{ $page->product->name_en }}</div>
                                    <small class="text-muted">ID: #{{ $page->product->id }} | SKU: {{ $page->product->product_code ?? 'N/A' }}</small>
                                    @if($page->product->is_varient == 1)
                                        <span class="badge bg-info text-dark ms-1">Has Variants</span>
                                    @endif
                                @else
                                    <span class="text-danger">Product Deleted</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-6">৳{{ number_format($page->sale_price, 2) }}</span>
                                @if($page->regular_price && $page->regular_price > $page->sale_price)
                                    <br><small class="text-muted text-decoration-line-through">৳{{ number_format($page->regular_price, 2) }}</small>
                                    <span class="badge bg-danger-light text-danger">{{ $page->discount_text }}</span>
                                @endif
                            </td>
                            <td>
                                <small>Dhaka: <b>৳{{ number_format($page->delivery_charge_dhaka, 0) }}</b></small><br>
                                <small>Outside: <b>৳{{ number_format($page->delivery_charge_outside, 0) }}</b></small>
                            </td>
                            <td>
                                @if($page->status == 1)
                                    <a href="{{ route('landing.page.change_status', $page->id) }}" class="badge rounded-pill bg-success text-white text-decoration-none px-3 py-2" title="Click to Deactivate">
                                        <i class="fa fa-check-circle me-1"></i> Active
                                    </a>
                                @else
                                    <a href="{{ route('landing.page.change_status', $page->id) }}" class="badge rounded-pill bg-danger text-white text-decoration-none px-3 py-2" title="Click to Activate">
                                        <i class="fa fa-ban me-1"></i> Inactive
                                    </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('landing.dynamic', $page->slug) }}" target="_blank" class="btn btn-sm btn-info text-white" title="Preview Landing Page">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('landing.page.edit', $page->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a href="{{ route('landing.page.destroy', $page->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this landing page?');" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa fa-file-invoice fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h5>No Landing Pages Found</h5>
                                    <p class="mb-3">You haven't created any landing pages yet.</p>
                                    <a href="{{ route('landing.page.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus me-1"></i> Create First Landing Page
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
