@props([
    'occasion' => null,
    'products' => [],
    'showDragHandle' => true,
    'showActions' => true,
])

<div class="card card-holder mt-3 mb-3">
    <div class="card-header">
        <h3>
            <i class="uil uil-box me-1"></i>{{ trans('catalogmanagement::occasion.product_variants') }}
        </h3>
    </div>
    <div class="card-body">
        @if(($occasion && $occasion->occasionProducts->count() > 0) || count($products) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="occasionProductsTable">
                    <thead>
                        <tr>
                            @if($showDragHandle)
                                <th style="width: 50px;"><i class="uil uil-arrows-move" title="{{ __('common.drag_to_reorder') }}"></i></th>
                            @endif
                            <th>#</th>
                            <th>{{ trans('catalogmanagement::occasion.product_information') }}</th>
                            <th>{{ trans('catalogmanagement::occasion.original_price') }}</th>
                            <th>{{ trans('catalogmanagement::occasion.special_price') }}</th>
                            <th>{{ trans('catalogmanagement::occasion.position') }}</th>
                            @if($showActions)
                                <th>{{ __('common.actions') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="occasionProductsBody" class="sortable-tbody">
                        @foreach(($occasion ? $occasion->occasionProducts : $products) as $index => $product)
                            @php
                                $vpv = $product->vendorProductVariant;
                                $vendorProduct = $vpv?->vendorProduct;
                                $productModel = $vendorProduct?->product;
                                $vendor = $vendorProduct?->vendor;
                                $variantConfig = $vpv?->variantConfiguration;
                                $variantKey = $variantConfig?->key?->name;
                                $variantValue = $variantConfig?->name;
                                $remainingStock = $vpv?->remaining_stock ?? 0;
                            @endphp
                            <tr class="draggable-row" data-product-id="{{ $product->id }}" data-occasion-id="{{ $occasion?->id }}" data-position="{{ $product->position }}" draggable="{{ $showDragHandle ? 'true' : 'false' }}">
                                @if($showDragHandle)
                                    <td class="drag-handle text-center" style="cursor: move; user-select: none;">
                                        <i class="uil uil-arrows-move" style="color: #5f63f2; font-size: 18px;"></i>
                                    </td>
                                @endif
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        {{-- Product Image --}}
                                        @if($productModel?->mainImage)
                                            <img src="{{ formatImage($productModel->mainImage) }}" alt="{{ $productModel->name ?? '' }}" class="rounded" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                                                <i class="uil uil-image text-muted fs-4"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            {{-- Product Name --}}
                                            <strong class="d-block mb-1">{{ $productModel->name ?? '-' }}</strong>
                                            {{-- Variant --}}
                                            @if($variantKey)
                                                <small class="d-block text-primary mb-1"><strong>{{ $variantKey }}:</strong> {{ $variantValue ?? 'Default' }}</small>
                                            @elseif($variantValue)
                                                <small class="d-block text-muted mb-1">{{ $variantValue }}</small>
                                            @endif
                                            {{-- SKU --}}
                                            <small class="d-block text-muted mb-1">
                                                <i class="uil uil-tag-alt me-1"></i>{{ trans('catalogmanagement::occasion.sku') }}: <code>{{ $vpv->sku ?? '-' }}</code>
                                            </small>
                                            {{-- Remaining Stock --}}
                                            <small class="d-block mb-1">
                                                <i class="uil uil-box me-1"></i>{{ trans('catalogmanagement::occasion.remaining_stock') }}:
                                                <span class="badge badge-sm badge-round {{ $remainingStock > 0 ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $remainingStock }}@if($remainingStock <= 0) ({{ trans('catalogmanagement::occasion.out_of_stock') }})@endif
                                                </span>
                                            </small>
                                            {{-- Vendor --}}
                                            @if($vendor)
                                                <div class="d-flex align-items-center justify-content-center gap-2 mt-2 pt-2 border-top">
                                                    @if($vendor->logo)
                                                        <img src="{{ formatImage($vendor->logo) }}" alt="{{ $vendor->name }}" class="rounded-circle" style="width: 24px; height: 24px; ">
                                                    @else
                                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                            <i class="uil uil-store text-muted" style="font-size: 12px;"></i>
                                                        </div>
                                                    @endif
                                                    <small class="text-primary fw-500">{{ $vendor->name }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-lg badge-round badge-info">{{ number_format($vpv->price ?? 0, 2) }} {{ currency() }}</span>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm" style="max-width: 150px;">
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               class="form-control special-price-edit"
                                               data-product-id="{{ $product->id }}"
                                               data-occasion-id="{{ $occasion?->id }}"
                                               value="{{ $product->special_price ?? '' }}"
                                               placeholder="0.00">
                                        <span class="input-group-text">{{ currency() }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-lg badge-round badge-primary">{{ $product->position }}</span>
                                </td>
                                @if($showActions)
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button"
                                                class="btn btn-sm btn-danger delete-occasion-product"
                                                data-product-id="{{ $product->id }}"
                                                data-occasion-id="{{ $occasion?->id }}"
                                                title="{{ __('common.delete') }}">
                                                <i class="uil uil-trash-alt m-0"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <i class="uil uil-info-circle me-2"></i>{{ trans('catalogmanagement::occasion.no_variants') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let draggedElement = null;
            let draggedOverElement = null;

            // Drag and Drop functionality
            $(document).on('dragstart', '.draggable-row', function(e) {
                draggedElement = this;
                $(this).addClass('dragging').css('opacity', '0.5');
                e.originalEvent.dataTransfer.effectAllowed = 'move';
            });

            $(document).on('dragend', '.draggable-row', function(e) {
                $(this).removeClass('dragging').css('opacity', '1');
                $('.draggable-row').removeClass('drag-over');
                draggedElement = null;
                draggedOverElement = null;
            });

            $(document).on('dragover', '.draggable-row', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';

                if (this !== draggedElement) {
                    $(this).addClass('drag-over');
                    draggedOverElement = this;
                }
            });

            $(document).on('dragleave', '.draggable-row', function(e) {
                $(this).removeClass('drag-over');
            });

            $(document).on('drop', '.draggable-row', function(e) {
                e.preventDefault();

                if (this !== draggedElement) {
                    // Swap rows
                    $(draggedElement).insertBefore($(this));
                    updatePositions();
                }
            });

            // Update positions after drag and drop
            function updatePositions() {
                const positions = [];

                $('#occasionProductsBody .draggable-row').each(function(index) {
                    const productId = $(this).data('product-id');
                    positions.push({
                        product_id: productId,
                        position: index
                    });
                });

                // Get occasion ID from first row
                const occasionId = $('#occasionProductsBody .draggable-row').first().data('occasion-id');

                if (!occasionId) {
                    console.error('Occasion ID not found');
                    toastr.error('{{ __("common.error_updating_order") }}');
                    return;
                }

                console.log('Updating positions for occasion:', occasionId, 'Positions:', positions);

                // Send update to server
                let route = "{{ route('admin.occasions.update-positions', ':id') }}".replace(':id', occasionId)
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        positions: positions
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message || '{{ __("common.order_updated_successfully") }}');
                        } else {
                            toastr.error(response.message || '{{ __("common.error_updating_order") }}');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('{{ __("common.error_updating_order") }}');
                    }
                });
            }

            // Store product data when delete button is clicked
            $(document).on('click', '.delete-occasion-product', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const productId = $btn.data('product-id');
                const occasionId = $btn.data('occasion-id');
                const productName = $btn.closest('tr').find('td:nth-child(3)').text().trim();

                // Update modal content with product name
                $('#delete-occasion-product-name').text(productName);

                // Store IDs in data attributes for use in confirm handler
                $('#confirmDeleteOccasionProductBtn').data('product-id', productId).data('occasion-id', occasionId);

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('modal-delete-occasion-product'));
                modal.show();
            });

            // Handle confirm delete from modal
            $(document).on('click', '#confirmDeleteOccasionProductBtn', function(e) {
                e.preventDefault();

                const productId = $(this).data('product-id');
                const occasionId = $(this).data('occasion-id');

                if (!productId || !occasionId) {
                    console.error('Product ID or Occasion ID not found');
                    toastr.error('{{ trans("catalogmanagement::occasion.error_deleting_product") }}');
                    return;
                }

                // Show loading
                LoadingOverlay.show({
                    text: '{{ __("main.deleting") }}',
                    subtext: '{{ __("main.please wait") }}'
                });

                // Send delete request
                let route = "{{ route('admin.occasions.products.destroy', ['occasion' => ':occasion', 'product' => ':product']) }}"
                    .replace(':occasion', occasionId)
                    .replace(':product', productId);
                $.ajax({
                    url: route,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        LoadingOverlay.hide();
                        if (response.status) {
                            toastr.success(response.message || '{{ trans("catalogmanagement::occasion.product_deleted_successfully") }}');
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modal-delete-occasion-product'));
                            if (modal) {
                                modal.hide();
                            }
                            // Reload page after 1 second
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message || '{{ trans("catalogmanagement::occasion.error_deleting_product") }}');
                        }
                    },
                    error: function(xhr) {
                        LoadingOverlay.hide();
                        const message = xhr.responseJSON?.message || '{{ trans("catalogmanagement::occasion.error_deleting_product") }}';
                        toastr.error(message);
                    }
                });
            });

            // Handle special price input change
            $(document).on('change', '.special-price-edit', function() {
                const $input = $(this);
                const productId = $input.data('product-id');
                const occasionId = $input.data('occasion-id');
                const specialPrice = $input.val();

                if (!productId || !occasionId) {
                    console.error('Product ID or Occasion ID not found');
                    return;
                }

                // Show loading indicator
                $input.prop('disabled', true);
                const originalValue = $input.val();

                // Send update request
                let route = "{{ route('admin.occasions.products.update-special-price', ['occasion' => ':occasion', 'product' => ':product']) }}"
                    .replace(':occasion', occasionId)
                    .replace(':product', productId);

                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        special_price: specialPrice
                    },
                    success: function(response) {
                        $input.prop('disabled', false);
                        if (response.status) {
                            toastr.success(response.message || '{{ trans("catalogmanagement::occasion.special_price") }} {{ trans("common.updated") }}');
                            $input.css('border-color', '#28a745').css('background-color', '#f0fff4');
                            setTimeout(() => {
                                $input.css('border-color', '').css('background-color', '');
                            }, 2000);
                        } else {
                            toastr.error(response.message || '{{ trans("common.error") }}');
                            $input.val(originalValue);
                        }
                    },
                    error: function(xhr) {
                        $input.prop('disabled', false);
                        const message = xhr.responseJSON?.message || '{{ trans("common.error") }}';
                        toastr.error(message);
                        $input.val(originalValue);
                    }
                });
            });
        });
    </script>

    <style>
        .draggable-row {
            transition: all 0.2s ease;
        }

        .draggable-row.dragging {
            background-color: #f0f0f0 !important;
            opacity: 0.5;
        }

        .draggable-row.drag-over {
            border-top: 3px solid #5f63f2 !important;
            background-color: #f8f9ff !important;
        }
    </style>
@endpush

{{-- Delete Product Modal --}}
<div class="modal fade" id="modal-delete-occasion-product" tabindex="-1" role="dialog" aria-labelledby="modal-delete-occasion-productLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-delete-occasion-productLabel">{{ trans('main.confirm delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-info-body d-flex">
                    <div class="modal-info-icon warning">
                        <img src="{{ asset('assets/img/svg/alert-circle.svg') }}" alt="alert-circle" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <p id="delete-occasion-product-name" class="fw-500">{{ trans('main.confirm delete') }}</p>
                        <p class="text-muted fs-13">{{ trans('main.are you sure you want to delete this') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> {{ trans('main.cancel') }}
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteOccasionProductBtn">
                    <i class="uil uil-trash-alt"></i> {{ trans('main.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
