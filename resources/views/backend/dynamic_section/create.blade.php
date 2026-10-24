@extends('admin.admin_master')
@section('admin')
<style>
    .cursor-grab {
        cursor: grab;
    }
    .cursor-grab:active {
        cursor: grabbing;
    }
    .product-drag-handle {
        padding: 6px 10px;
        display: inline-block;
        color: #94a3b8;
        transition: color 0.2s;
    }
    .product-drag-handle:hover {
        color: #38bdf8;
    }
    .sortable-ghost {
        background-color: rgba(56, 189, 248, 0.15) !important;
        opacity: 0.7;
    }
    .product-thumb-sm {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .selected-products-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
</style>

<section class="content-main">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="content-header">
                <div>
                    <h2 class="content-title">Add Dynamic Section</h2>
                    <p class="text-muted">Create a new product section for your homepage and assign products with drag & drop ordering.</p>
                </div>
                <div>
                    <a href="{{ route('dynamic-section.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Sections
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <form action="{{ route('dynamic-section.store') }}" method="POST" id="dynamicSectionForm">
                @csrf
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-dark"><i class="fa-solid fa-sliders me-2 text-primary"></i> Section Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label fw-bold">Section Title (English) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Trendy Summer Outfits" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="title_bn" class="form-label fw-bold">Section Title (Bangla)</label>
                                <input type="text" class="form-control @error('title_bn') is-invalid @enderror" id="title_bn" name="title_bn" value="{{ old('title_bn') }}" placeholder="e.g. সামার কালেকশন">
                                @error('title_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="slug" class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="e.g. trendy-summer-outfits" required>
                                <small class="text-muted">Unique URL-friendly identifier for this section.</small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="position" class="form-label fw-bold">Section Position / Order</label>
                                <input type="number" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $next_position) }}" min="1">
                                <small class="text-muted">Display sequence among home sections.</small>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3 d-flex flex-column justify-content-center">
                                <label class="form-label fw-bold">Status</label>
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="status">Active (Show on Home)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Product Selection & Drag and Drop Reordering Card --}}
                <div class="card mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 text-dark"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i> Select & Position Products</h5>
                            <small class="text-muted">Select products to add, then drag & drop the rows to set their exact sequence in this section.</small>
                        </div>
                        <span class="badge bg-primary fs-6" id="selected-count-badge">0 Products Added</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="product_category_picker" class="form-label fw-bold">Choose a Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="product_category_picker" name="category_id">
                                    <option value="">-- Choose a category (optional) --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name_en }}{{ $category->name_bn ? ' / ' . $category->name_bn : '' }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Selecting a category automatically adds all active products in it.</small>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-10">
                                <label for="product_picker" class="form-label fw-bold">Search & Select Products:</label>
                                <select class="form-control select-active form-select" id="product_picker" data-placeholder="Choose products to add to this section">
                                    <option value="">-- Search & Choose a Product --</option>
                                    @foreach($products as $prod)
                                        @php
                                            $thumb = $prod->product_thumbnail;
                                            if ($thumb) {
                                                $thumb = preg_replace('/^(\/?public\/)+/', '', $thumb);
                                                $thumb = ltrim($thumb, '/');
                                            }
                                            if (!$thumb || !file_exists(public_path($thumb))) {
                                                $thumb = 'upload/product-default.jpg';
                                            }
                                        @endphp
                                        <option value="{{ $prod->id }}" 
                                                data-name-en="{{ $prod->name_en }}" 
                                                data-name-bn="{{ $prod->name_bn }}"
                                                data-category-id="{{ $prod->category_id }}"
                                                data-thumb="{{ asset($thumb) }}"
                                                data-price="{{ $prod->regular_price }}">
                                            {{ $prod->name_en }} (Tk {{ number_format($prod->regular_price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary w-100" id="btn-add-product">
                                    <i class="fa-solid fa-plus me-1"></i> Add
                                </button>
                            </div>
                        </div>

                        {{-- Selected Products Table with Drag & Drop --}}
                        <div class="selected-products-wrapper">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="selected-products-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="8%" class="text-center">Move</th>
                                            <th width="10%" class="text-center">Pos #</th>
                                            <th width="12%" class="text-center">Photo</th>
                                            <th width="42%">Product Name</th>
                                            <th width="16%">Price</th>
                                            <th width="12%" class="text-center">Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-products-tbody">
                                        {{-- Rows added dynamically --}}
                                    </tbody>
                                </table>
                            </div>
                            <div id="no-products-placeholder" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-basket-shopping fa-3x mb-2 text-secondary d-block"></i>
                                <p class="mb-0">No products added yet. Select products from the dropdown above to add them to this section.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ route('dynamic-section.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fa-solid fa-check me-1"></i> Save Section
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@push('footer-script')
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        $('#product_category_picker').on('change', function() {
            var categoryId = $(this).val();
            if (!categoryId) return;

            var tbody = $('#sortable-products-tbody').empty();
            $('#product_picker option').filter(function() {
                return String($(this).data('category-id')) === String(categoryId);
            }).each(function() {
                var option = $(this);
                appendProductRow({
                    id: option.val(),
                    name_en: option.data('name-en'),
                    name_bn: option.data('name-bn'),
                    thumb: option.data('thumb'),
                    price: option.data('price')
                }, true);
            });
            $('#product_picker').val('').trigger('change');
            refreshProductPositions();
        });

        // Auto-generate slug from English Title
        $('#title').on('input', function() {
            var title = $(this).val();
            var slug = title.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            $('#slug').val(slug);
        });

        // Initialize Sortable for Products table
        var prodTbody = document.getElementById('sortable-products-tbody');
        if (prodTbody) {
            Sortable.create(prodTbody, {
                handle: '.product-drag-handle',
                animation: 200,
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    refreshProductPositions();
                }
            });
        }

        // Add Product to list
        $('#btn-add-product').on('click', function() {
            addProductFromSelect();
        });

        $('#product_picker').on('change', function() {
            if ($(this).val()) {
                addProductFromSelect();
            }
        });

        function addProductFromSelect() {
            var select = $('#product_picker');
            var prodId = select.val();
            if (!prodId) return;

            $('#product_category_picker').val('');

            // Check if product is already added
            if ($('#sortable-products-tbody tr[data-product-id="' + prodId + '"]').length > 0) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning('This product is already added to this section!');
                }
                select.val('').trigger('change');
                return;
            }

            var selectedOption = select.find('option:selected');
            var nameEn = selectedOption.data('name-en');
            var nameBn = selectedOption.data('name-bn');
            var thumb = selectedOption.data('thumb');
            var price = selectedOption.data('price');

            appendProductRow({
                id: prodId,
                name_en: nameEn,
                name_bn: nameBn,
                thumb: thumb,
                price: price
            });

            select.val('').trigger('change');
        }

        function appendProductRow(prod, skipRefresh) {
            $('#no-products-placeholder').hide();

            var rowCount = $('#sortable-products-tbody tr').length + 1;

            var tr = `
                <tr data-product-id="${prod.id}">
                    <td class="text-center">
                        <span class="product-drag-handle cursor-grab" title="Drag to reorder position">
                            <i class="fa-solid fa-grip-vertical fs-5"></i>
                        </span>
                        <input type="hidden" name="products[]" value="${prod.id}">
                    </td>
                    <td class="text-center">
                        <input type="number" 
                               name="product_positions[${prod.id}]" 
                               class="form-control form-control-sm text-center prod-position-input d-inline-block" 
                               value="${rowCount}" 
                               min="1" 
                               style="width: 65px; font-weight: 600;">
                    </td>
                    <td class="text-center">
                        <img src="${prod.thumb}" alt="${prod.name_en}" class="product-thumb-sm">
                    </td>
                    <td>
                        <strong class="text-dark d-block">${prod.name_en}</strong>
                        ${prod.name_bn ? '<small class="text-muted">' + prod.name_bn + '</small>' : ''}
                    </td>
                    <td>
                        <span class="text-success fw-bold">Tk ${parseFloat(prod.price || 0).toFixed(2)}</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-product" title="Remove product">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#sortable-products-tbody').append(tr);
            if (!skipRefresh) refreshProductPositions();
        }

        // Remove Product
        $(document).on('click', '.btn-remove-product', function() {
            $('#product_category_picker').val('');
            $(this).closest('tr').remove();
            refreshProductPositions();
        });

        // When numeric position input is manually changed
        $(document).on('change', '.prod-position-input', function() {
            var rows = $('#sortable-products-tbody tr').get();
            rows.sort(function(a, b) {
                var posA = parseInt($(a).find('.prod-position-input').val()) || 9999;
                var posB = parseInt($(b).find('.prod-position-input').val()) || 9999;
                return posA - posB;
            });
            $.each(rows, function(idx, item) {
                $('#sortable-products-tbody').append(item);
            });
            refreshProductPositions();
        });

        function refreshProductPositions() {
            var rows = $('#sortable-products-tbody tr');
            var count = rows.length;

            if (count === 0) {
                $('#no-products-placeholder').show();
                $('#selected-count-badge').text('0 Products Added');
            } else {
                $('#no-products-placeholder').hide();
                $('#selected-count-badge').text(count + ' Products Added');
            }

            rows.each(function(index) {
                var pos = index + 1;
                $(this).find('.prod-position-input').val(pos);
            });
        }
    });
</script>
@endpush
@endsection
