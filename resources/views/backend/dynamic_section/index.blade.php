@extends('admin.admin_master')
@section('admin')
<style>
    .cursor-grab {
        cursor: grab;
    }
    .cursor-grab:active {
        cursor: grabbing;
    }
    .drag-handle {
        padding: 8px 12px;
        display: inline-block;
        color: #94a3b8;
        transition: color 0.2s;
    }
    .drag-handle:hover {
        color: #38bdf8;
    }
    .sortable-ghost {
        background-color: rgba(56, 189, 248, 0.15) !important;
        opacity: 0.8;
    }
    .position-badge {
        font-weight: 600;
        min-width: 32px;
    }
</style>

<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Dynamic Sections</h2>
            <p class="text-muted">Manage homepage dynamic product sections. Drag & drop rows to reorder section positions on the homepage.</p>
        </div>
        <div>
            <a href="{{ route('dynamic-section.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Add New Section
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <header class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <strong class="text-dark">Total Sections: {{ count($sections) }}</strong>
                    <span class="text-muted ms-2"><i class="fa-solid fa-info-circle me-1"></i> You can drag the handles <i class="fa-solid fa-grip-vertical text-muted"></i> to rearrange the section display order.</span>
                </div>
            </div>
        </header>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered" id="dynamic-sections-table">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">Move</th>
                            <th width="8%" class="text-center">Position</th>
                            <th width="30%">Title</th>
                            <th width="20%">Slug</th>
                            <th width="12%" class="text-center">Products</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-sections-tbody">
                        @forelse($sections as $index => $section)
                        <tr data-id="{{ $section->id }}">
                            <td class="text-center">
                                <span class="drag-handle cursor-grab" title="Drag to reorder position">
                                    <i class="fa-solid fa-grip-vertical fs-5"></i>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary position-badge" id="pos-badge-{{ $section->id }}">
                                    #{{ $section->position }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <strong class="text-dark">{{ $section->title }}</strong>
                                    @if($section->title_bn)
                                        <br><small class="text-muted">{{ $section->title_bn }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code class="text-primary">{{ $section->slug }}</code>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark px-3 py-2">
                                    <i class="fa-solid fa-boxes-stacked me-1"></i> {{ $section->products_count }} Products
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm btn-status-toggle border-0 p-0" 
                                        data-id="{{ $section->id }}"
                                        title="Click to toggle status">
                                    @if($section->status == 1)
                                        <span class="badge rounded-pill bg-success status-indicator" id="status-badge-{{ $section->id }}">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger status-indicator" id="status-badge-{{ $section->id }}">Disabled</span>
                                    @endif
                                </button>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('dynamic-section.edit', $section->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Section & Products">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="{{ route('dynamic-section.destroy', $section->id) }}" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this dynamic section?');"
                                       title="Delete Section">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-layer-group fa-3x mb-3 text-secondary d-block"></i>
                                <h5>No Dynamic Sections Found</h5>
                                <p>Create your first dynamic product section to showcase on your homepage.</p>
                                <a href="{{ route('dynamic-section.create') }}" class="btn btn-primary mt-2">
                                    <i class="fa-solid fa-plus me-1"></i> Add Section Now
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@push('footer-script')
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Sortable for Sections
        var tbody = document.getElementById('sortable-sections-tbody');
        if (tbody) {
            Sortable.create(tbody, {
                handle: '.drag-handle',
                animation: 200,
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    var order = [];
                    $('#sortable-sections-tbody tr').each(function(index) {
                        var sectionId = $(this).data('id');
                        if (sectionId) {
                            order.push(sectionId);
                            $(this).find('.position-badge').text('#' + (index + 1));
                        }
                    });

                    if (order.length > 0) {
                        $.ajax({
                            url: "{{ route('dynamic-section.update_positions') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                order: order
                            },
                            success: function(res) {
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(res.message || "Section order updated!");
                                }
                            },
                            error: function() {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error("Failed to update section order.");
                                }
                            }
                        });
                    }
                }
            });
        }

        // Status Toggle AJAX
        $(document).on('click', '.btn-status-toggle', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var badge = $('#status-badge-' + id);

            $.ajax({
                url: "{{ url('/admin/dynamic-sections/change-status') }}/" + id,
                method: "GET",
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    if (res.status == 1) {
                        badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                    } else {
                        badge.removeClass('bg-success').addClass('bg-danger').text('Disabled');
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message || "Status changed successfully!");
                    }
                },
                error: function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error("Failed to change status.");
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection
