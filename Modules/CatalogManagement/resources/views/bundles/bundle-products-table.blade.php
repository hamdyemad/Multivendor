@props([
    'bundle' => null,
    'products' => [],
    'showDragHandle' => true,
    'showActions' => true,
])

<div class="card card-holder mt-3 mb-3">
    <div class="card-header">
        <h3>
            <i class="uil uil-box me-1"></i>{{ trans('catalogmanagement::bundle.bundle_products') }}
        </h3>
    </div>
    <div class="card-body">
        @if (($bundle && $bundle->bundleProducts->count() > 0) || count($products) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="bundleProductsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('catalogmanagement::bundle.product_variant') }}</th>
                            <th>{{ trans('catalogmanagement::bundle.price') }}</th>
                            <th>{{ trans('catalogmanagement::bundle.min_quantity') }}</th>
                            <th>{{ trans('catalogmanagement::bundle.limitation_quantity') }}</th>
                            @if ($showActions)
                                <th>{{ __('common.actions') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="bundleProductsBody" class="sortable-tbody">
                        @foreach ($bundle ? $bundle->bundleProducts : $products as $index => $product)
                            <tr class="draggable-row" data-product-id="{{ $product->id }}"
                                data-bundle-id="{{ $bundle?->id }}" data-position="{{ $product->position ?? $index }}"
                                draggable="{{ $showDragHandle ? 'true' : 'false' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $product->vendorProductVariant?->vendorProduct?->product?->name ?? 'N/A' }}</strong>
                                        @if ($product->vendorProductVariant?->variantConfiguration)
                                            @php
                                                $variant = $product->vendorProductVariant?->variantConfiguration;
                                                $path = [];
                                                $current = $variant;
                                                while ($current) {
                                                    array_unshift($path, $current);
                                                    $current = $current->parent_data;
                                                }
                                            @endphp
                                            <div class="text-muted small mt-1">
                                                @foreach ($path as $item)
                                                    <strong>{{ $item->key->name }}</strong>
                                                    ->
                                                    <strong>{{ $item->name }}</strong>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-lg badge-round badge-info">{{ number_format($product->price ?? 0, 2) }}
                                        {{ currency() }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-lg badge-round badge-primary">{{ $product->min_quantity }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-lg badge-round badge-secondary">{{ $product->limitation_quantity ?? trans('catalogmanagement::bundle.unlimited') }}</span>
                                </td>
                                @if ($showActions)
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-danger delete-bundle-product"
                                                data-product-id="{{ $product->id }}"
                                                data-bundle-id="{{ $bundle?->id }}"
                                                title="{{ __('common.delete') }}">
                                                <i class="uil uil-trash-alt m-0"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        @if (($bundle && $bundle->bundleProducts->count() > 0) || count($products) > 0)
                            <tr class="table-active fw-bold" style="background-color: #f8f9fa;">
                                <td colspan="{{ $showDragHandle ? 3 : 2 }}">
                                    <strong>{{ trans('catalogmanagement::bundle.totals') }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-lg badge-round badge-info">
                                        {{ number_format(($bundle ? $bundle->bundleProducts->sum('price') : collect($products)->sum('price')) ?? 0, 2) }}
                                        {{ currency() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-lg badge-round badge-primary">
                                        {{ ($bundle ? $bundle->bundleProducts->sum('min_quantity') : collect($products)->sum('min_quantity')) ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                </td>
                                @if ($showActions)
                                    <td class="text-center">
                                        
                                    </td>
                                @endif
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <i class="uil uil-info-circle me-2"></i>{{ trans('catalogmanagement::bundle.no_products_added') }}
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
                }
            });

            // Store product data when delete button is clicked
            $(document).on('click', '.delete-bundle-product', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const productId = $btn.data('product-id');
                const bundleId = $btn.data('bundle-id');
                const productName = $btn.closest('tr').find('td:nth-child(3)').text().trim();

                // Update modal content with product name
                $('#delete-bundle-product-name').text(productName);

                // Store IDs in data attributes for use in confirm handler
                $('#confirmDeleteBundleProductBtn').data('product-id', productId).data('bundle-id',
                    bundleId);

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('modal-delete-bundle-product'));
                modal.show();
            });

            // Handle confirm delete from modal
            $(document).on('click', '#confirmDeleteBundleProductBtn', function(e) {
                e.preventDefault();

                const productId = $(this).data('product-id');
                const bundleId = $(this).data('bundle-id');

                if (!productId || !bundleId) {
                    console.error('Product ID or Bundle ID not found');
                    toastr.error('{{ trans('catalogmanagement::bundle.error_deleting_product') }}');
                    return;
                }

                // Show loading
                LoadingOverlay.show({
                    text: '{{ __('main.deleting') }}',
                    subtext: '{{ __('main.please wait') }}'
                });

                // Send delete request
                let route =
                    "{{ route('admin.bundles.products.destroy', ['bundle' => ':bundle', 'product' => ':product']) }}"
                    .replace(':bundle', bundleId)
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
                            toastr.success(response.message ||
                                '{{ trans('catalogmanagement::bundle.product_deleted_successfully') }}'
                                );
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'modal-delete-bundle-product'));
                            if (modal) {
                                modal.hide();
                            }
                            // Reload page after 1 second
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message ||
                                '{{ trans('catalogmanagement::bundle.error_deleting_product') }}'
                                );
                        }
                    },
                    error: function(xhr) {
                        LoadingOverlay.hide();
                        const message = xhr.responseJSON?.message ||
                            '{{ trans('catalogmanagement::bundle.error_deleting_product') }}';
                        toastr.error(message);
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
<div class="modal fade" id="modal-delete-bundle-product" tabindex="-1" role="dialog"
    aria-labelledby="modal-delete-bundle-productLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-delete-bundle-productLabel">{{ trans('main.confirm delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-info-body d-flex">
                    <div class="modal-info-icon warning">
                        <img src="{{ asset('assets/img/svg/alert-circle.svg') }}" alt="alert-circle" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <p id="delete-bundle-product-name" class="fw-500">{{ trans('main.confirm delete') }}</p>
                        <p class="text-muted fs-13">{{ trans('main.are you sure you want to delete this') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> {{ trans('main.cancel') }}
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBundleProductBtn">
                    <i class="uil uil-trash-alt"></i> {{ trans('main.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
