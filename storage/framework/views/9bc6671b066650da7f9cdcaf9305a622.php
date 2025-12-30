<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'occasion' => null,
    'products' => [],
    'showDragHandle' => true,
    'showActions' => true,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'occasion' => null,
    'products' => [],
    'showDragHandle' => true,
    'showActions' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="card card-holder mt-3 mb-3">
    <div class="card-header">
        <h3>
            <i class="uil uil-box me-1"></i><?php echo e(trans('catalogmanagement::occasion.product_variants')); ?>

        </h3>
    </div>
    <div class="card-body">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($occasion && $occasion->occasionProducts->count() > 0) || count($products) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="occasionProductsTable">
                    <thead>
                        <tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDragHandle): ?>
                                <th style="width: 50px;"><i class="uil uil-arrows-move" title="<?php echo e(__('common.drag_to_reorder')); ?>"></i></th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th>#</th>
                            <th><?php echo e(trans('catalogmanagement::occasion.product_information')); ?></th>
                            <th><?php echo e(trans('catalogmanagement::occasion.original_price')); ?></th>
                            <th><?php echo e(trans('catalogmanagement::occasion.special_price')); ?></th>
                            <th><?php echo e(trans('catalogmanagement::occasion.position')); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showActions): ?>
                                <th><?php echo e(__('common.actions')); ?></th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="occasionProductsBody" class="sortable-tbody">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($occasion ? $occasion->occasionProducts : $products); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $vpv = $product->vendorProductVariant;
                                $vendorProduct = $vpv?->vendorProduct;
                                $productModel = $vendorProduct?->product;
                                $vendor = $vendorProduct?->vendor;
                                $variantConfig = $vpv?->variantConfiguration;
                                $variantKey = $variantConfig?->key?->name;
                                $variantValue = $variantConfig?->name;
                                $remainingStock = $vpv?->remaining_stock ?? 0;
                            ?>
                            <tr class="draggable-row" data-product-id="<?php echo e($product->id); ?>" data-occasion-id="<?php echo e($occasion?->id); ?>" data-position="<?php echo e($product->position); ?>" draggable="<?php echo e($showDragHandle ? 'true' : 'false'); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDragHandle): ?>
                                    <td class="drag-handle text-center" style="cursor: move; user-select: none;">
                                        <i class="uil uil-arrows-move" style="color: #5f63f2; font-size: 18px;"></i>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <td><?php echo e($index + 1); ?></td>
                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productModel?->mainImage): ?>
                                            <img src="<?php echo e(formatImage($productModel->mainImage)); ?>" alt="<?php echo e($productModel->name ?? ''); ?>" class="rounded" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <?php else: ?>
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; flex-shrink: 0;">
                                                <i class="uil uil-image text-muted fs-4"></i>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="flex-grow-1">
                                            
                                            <strong class="d-block mb-1"><?php echo e($productModel->name ?? '-'); ?></strong>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantKey): ?>
                                                <small class="d-block text-primary mb-1"><strong><?php echo e($variantKey); ?>:</strong> <?php echo e($variantValue ?? 'Default'); ?></small>
                                            <?php elseif($variantValue): ?>
                                                <small class="d-block text-muted mb-1"><?php echo e($variantValue); ?></small>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            
                                            <small class="d-block text-muted mb-1">
                                                <i class="uil uil-tag-alt me-1"></i><?php echo e(trans('catalogmanagement::occasion.sku')); ?>: <code><?php echo e($vpv->sku ?? '-'); ?></code>
                                            </small>
                                            
                                            <small class="d-block mb-1">
                                                <i class="uil uil-box me-1"></i><?php echo e(trans('catalogmanagement::occasion.remaining_stock')); ?>:
                                                <span class="badge badge-sm badge-round <?php echo e($remainingStock > 0 ? 'badge-success' : 'badge-danger'); ?>">
                                                    <?php echo e($remainingStock); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($remainingStock <= 0): ?> (<?php echo e(trans('catalogmanagement::occasion.out_of_stock')); ?>)<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                            </small>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor): ?>
                                                <div class="d-flex align-items-center justify-content-center gap-2 mt-2 pt-2 border-top">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor->logo): ?>
                                                        <img src="<?php echo e(formatImage($vendor->logo)); ?>" alt="<?php echo e($vendor->name); ?>" class="rounded-circle" style="width: 24px; height: 24px; ">
                                                    <?php else: ?>
                                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                            <i class="uil uil-store text-muted" style="font-size: 12px;"></i>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <small class="text-primary fw-500"><?php echo e($vendor->name); ?></small>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-lg badge-round badge-info"><?php echo e(number_format($vpv->price ?? 0, 2)); ?> <?php echo e(currency()); ?></span>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm" style="max-width: 150px;">
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               class="form-control special-price-edit"
                                               data-product-id="<?php echo e($product->id); ?>"
                                               data-occasion-id="<?php echo e($occasion?->id); ?>"
                                               value="<?php echo e($product->special_price ?? ''); ?>"
                                               placeholder="0.00">
                                        <span class="input-group-text"><?php echo e(currency()); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-lg badge-round badge-primary"><?php echo e($product->position); ?></span>
                                </td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showActions): ?>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button"
                                                class="btn btn-sm btn-danger delete-occasion-product"
                                                data-product-id="<?php echo e($product->id); ?>"
                                                data-occasion-id="<?php echo e($occasion?->id); ?>"
                                                title="<?php echo e(__('common.delete')); ?>">
                                                <i class="uil uil-trash-alt m-0"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="uil uil-info-circle me-2"></i><?php echo e(trans('catalogmanagement::occasion.no_variants')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
                    toastr.error('<?php echo e(__("common.error_updating_order")); ?>');
                    return;
                }

                console.log('Updating positions for occasion:', occasionId, 'Positions:', positions);

                // Send update to server
                let route = "<?php echo e(route('admin.occasions.update-positions', ':id')); ?>".replace(':id', occasionId)
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        positions: positions
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message || '<?php echo e(__("common.order_updated_successfully")); ?>');
                        } else {
                            toastr.error(response.message || '<?php echo e(__("common.error_updating_order")); ?>');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('<?php echo e(__("common.error_updating_order")); ?>');
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
                    toastr.error('<?php echo e(trans("catalogmanagement::occasion.error_deleting_product")); ?>');
                    return;
                }

                // Show loading
                LoadingOverlay.show({
                    text: '<?php echo e(__("main.deleting")); ?>',
                    subtext: '<?php echo e(__("main.please wait")); ?>'
                });

                // Send delete request
                let route = "<?php echo e(route('admin.occasions.products.destroy', ['occasion' => ':occasion', 'product' => ':product'])); ?>"
                    .replace(':occasion', occasionId)
                    .replace(':product', productId);
                $.ajax({
                    url: route,
                    type: 'DELETE',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        LoadingOverlay.hide();
                        if (response.status) {
                            toastr.success(response.message || '<?php echo e(trans("catalogmanagement::occasion.product_deleted_successfully")); ?>');
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
                            toastr.error(response.message || '<?php echo e(trans("catalogmanagement::occasion.error_deleting_product")); ?>');
                        }
                    },
                    error: function(xhr) {
                        LoadingOverlay.hide();
                        const message = xhr.responseJSON?.message || '<?php echo e(trans("catalogmanagement::occasion.error_deleting_product")); ?>';
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
                let route = "<?php echo e(route('admin.occasions.products.update-special-price', ['occasion' => ':occasion', 'product' => ':product'])); ?>"
                    .replace(':occasion', occasionId)
                    .replace(':product', productId);

                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        special_price: specialPrice
                    },
                    success: function(response) {
                        $input.prop('disabled', false);
                        if (response.status) {
                            toastr.success(response.message || '<?php echo e(trans("catalogmanagement::occasion.special_price")); ?> <?php echo e(trans("common.updated")); ?>');
                            $input.css('border-color', '#28a745').css('background-color', '#f0fff4');
                            setTimeout(() => {
                                $input.css('border-color', '').css('background-color', '');
                            }, 2000);
                        } else {
                            toastr.error(response.message || '<?php echo e(trans("common.error")); ?>');
                            $input.val(originalValue);
                        }
                    },
                    error: function(xhr) {
                        $input.prop('disabled', false);
                        const message = xhr.responseJSON?.message || '<?php echo e(trans("common.error")); ?>';
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
<?php $__env->stopPush(); ?>


<div class="modal fade" id="modal-delete-occasion-product" tabindex="-1" role="dialog" aria-labelledby="modal-delete-occasion-productLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-delete-occasion-productLabel"><?php echo e(trans('main.confirm delete')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-info-body d-flex">
                    <div class="modal-info-icon warning">
                        <img src="<?php echo e(asset('assets/img/svg/alert-circle.svg')); ?>" alt="alert-circle" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <p id="delete-occasion-product-name" class="fw-500"><?php echo e(trans('main.confirm delete')); ?></p>
                        <p class="text-muted fs-13"><?php echo e(trans('main.are you sure you want to delete this')); ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> <?php echo e(trans('main.cancel')); ?>

                </button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteOccasionProductBtn">
                    <i class="uil uil-trash-alt"></i> <?php echo e(trans('main.delete')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/occasions/occasion-products-table.blade.php ENDPATH**/ ?>