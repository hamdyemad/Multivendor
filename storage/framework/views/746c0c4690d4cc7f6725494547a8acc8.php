<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'bundle' => null,
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
    'bundle' => null,
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
            <i class="uil uil-box me-1"></i><?php echo e(trans('catalogmanagement::bundle.bundle_products')); ?>

        </h3>
    </div>
    <div class="card-body">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($bundle && $bundle->bundleProducts->count() > 0) || count($products) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="bundleProductsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo e(trans('catalogmanagement::bundle.product_variant')); ?></th>
                            <th><?php echo e(trans('catalogmanagement::bundle.price')); ?></th>
                            <th><?php echo e(trans('catalogmanagement::bundle.min_quantity')); ?></th>
                            <th><?php echo e(trans('catalogmanagement::bundle.limitation_quantity')); ?></th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showActions): ?>
                                <th><?php echo e(__('common.actions')); ?></th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="bundleProductsBody" class="sortable-tbody">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $bundle ? $bundle->bundleProducts : $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="draggable-row" data-product-id="<?php echo e($product->id); ?>"
                                data-bundle-id="<?php echo e($bundle?->id); ?>" data-position="<?php echo e($product->position ?? $index); ?>"
                                draggable="<?php echo e($showDragHandle ? 'true' : 'false'); ?>">
                                <td><?php echo e($index + 1); ?></td>
                                <td>
                                    <div>
                                        <strong><?php echo e($product->vendorProductVariant?->vendorProduct?->product?->name ?? 'N/A'); ?></strong>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->vendorProductVariant?->variantConfiguration): ?>
                                            <?php
                                                $variant = $product->vendorProductVariant?->variantConfiguration;
                                                $path = [];
                                                $current = $variant;
                                                while ($current) {
                                                    array_unshift($path, $current);
                                                    $current = $current->parent_data;
                                                }
                                            ?>
                                            <div class="text-muted small mt-1">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $path; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <strong><?php echo e($item->key->name); ?></strong>
                                                    ->
                                                    <strong><?php echo e($item->name); ?></strong>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-lg badge-round badge-info"><?php echo e(number_format($product->price ?? 0, 2)); ?>

                                        <?php echo e(currency()); ?></span>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-lg badge-round badge-primary"><?php echo e($product->min_quantity); ?></span>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-lg badge-round badge-secondary"><?php echo e($product->limitation_quantity ?? trans('catalogmanagement::bundle.unlimited')); ?></span>
                                </td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showActions): ?>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-danger delete-bundle-product"
                                                data-product-id="<?php echo e($product->id); ?>"
                                                data-bundle-id="<?php echo e($bundle?->id); ?>"
                                                title="<?php echo e(__('common.delete')); ?>">
                                                <i class="uil uil-trash-alt m-0"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($bundle && $bundle->bundleProducts->count() > 0) || count($products) > 0): ?>
                            <tr class="table-active fw-bold" style="background-color: #f8f9fa;">
                                <td colspan="<?php echo e($showDragHandle ? 3 : 2); ?>">
                                    <strong><?php echo e(trans('catalogmanagement::bundle.totals')); ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-lg badge-round badge-info">
                                        <?php echo e(number_format(($bundle ? $bundle->bundleProducts->sum('price') : collect($products)->sum('price')) ?? 0, 2)); ?>

                                        <?php echo e(currency()); ?>

                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-lg badge-round badge-primary">
                                        <?php echo e(($bundle ? $bundle->bundleProducts->sum('min_quantity') : collect($products)->sum('min_quantity')) ?? 0); ?>

                                    </span>
                                </td>
                                <td class="text-center">
                                </td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showActions): ?>
                                    <td class="text-center">
                                        
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="uil uil-info-circle me-2"></i><?php echo e(trans('catalogmanagement::bundle.no_products_added')); ?>

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
                    toastr.error('<?php echo e(trans('catalogmanagement::bundle.error_deleting_product')); ?>');
                    return;
                }

                // Show loading
                LoadingOverlay.show({
                    text: '<?php echo e(__('main.deleting')); ?>',
                    subtext: '<?php echo e(__('main.please wait')); ?>'
                });

                // Send delete request
                let route =
                    "<?php echo e(route('admin.bundles.products.destroy', ['bundle' => ':bundle', 'product' => ':product'])); ?>"
                    .replace(':bundle', bundleId)
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
                            toastr.success(response.message ||
                                '<?php echo e(trans('catalogmanagement::bundle.product_deleted_successfully')); ?>'
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
                                '<?php echo e(trans('catalogmanagement::bundle.error_deleting_product')); ?>'
                                );
                        }
                    },
                    error: function(xhr) {
                        LoadingOverlay.hide();
                        const message = xhr.responseJSON?.message ||
                            '<?php echo e(trans('catalogmanagement::bundle.error_deleting_product')); ?>';
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
<?php $__env->stopPush(); ?>


<div class="modal fade" id="modal-delete-bundle-product" tabindex="-1" role="dialog"
    aria-labelledby="modal-delete-bundle-productLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-delete-bundle-productLabel"><?php echo e(trans('main.confirm delete')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-info-body d-flex">
                    <div class="modal-info-icon warning">
                        <img src="<?php echo e(asset('assets/img/svg/alert-circle.svg')); ?>" alt="alert-circle" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <p id="delete-bundle-product-name" class="fw-500"><?php echo e(trans('main.confirm delete')); ?></p>
                        <p class="text-muted fs-13"><?php echo e(trans('main.are you sure you want to delete this')); ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> <?php echo e(trans('main.cancel')); ?>

                </button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBundleProductBtn">
                    <i class="uil uil-trash-alt"></i> <?php echo e(trans('main.delete')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/bundles/bundle-products-table.blade.php ENDPATH**/ ?>