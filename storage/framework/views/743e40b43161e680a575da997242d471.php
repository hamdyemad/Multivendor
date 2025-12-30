
<?php $__env->startSection('title', trans('order.stock_allocation') . ' | Bnaia'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order.orders'), 'url' => route('admin.orders.index')],
                    [
                        'title' => trans('order.order') . ' #' . $order->order_number,
                        'url' => route('admin.orders.show', $order->id),
                    ],
                    ['title' => trans('order.stock_allocation')],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('order.orders'), 'url' => route('admin.orders.index')],
                    [
                        'title' => trans('order.order') . ' #' . $order->order_number,
                        'url' => route('admin.orders.show', $order->id),
                    ],
                    ['title' => trans('order.stock_allocation')],
                ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="bg-white p-40 radius-xl">
                    <div class="mb-40">
                        <h3 class="text-primary fw-bold mb-10"><?php echo e(trans('order.stock_allocation_for_order')); ?>

                            #<?php echo e($order->order_number); ?></h3>
                        <p class="text-muted"><?php echo e(trans('order.allocate_stock_from_regions')); ?></p>
                    </div>

                    <form id="allocationForm" method="POST"
                        action="<?php echo e(route('admin.order-fulfillments.allocate', $order->id)); ?>">
                        <?php echo csrf_field(); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $stockData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderProductId => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $orderProduct = $data['order_product'];
                                $vendorProduct = $orderProduct->vendorProduct;
                                $product = $vendorProduct?->product;
                                $vendor = $vendorProduct?->vendor;
                                $variant = $data['vendor_product_variant'];
                                $productImage = $product?->image ?? null;
                                $vendorName = $vendor?->getTranslation('name', app()->getLocale()) ?? 'N/A';
                                $sku = $variant?->sku ?? $vendorProduct?->sku ?? 'N/A';
                                
                                // Build variant path: Key -> Value
                                $variantConfig = $variant?->variantConfiguration;
                                $variantKey = $variantConfig?->key?->getTranslation('name', app()->getLocale()) ?? null;
                                $variantValue = $variantConfig?->getTranslation('name', app()->getLocale()) ?? null;
                                $variantPath = null;
                                if ($variantKey && $variantValue) {
                                    $variantPath = $variantKey . ' → ' . $variantValue;
                                } elseif ($variantValue) {
                                    $variantPath = $variantValue;
                                }
                            ?>
                            <div class="mb-30" data-ordered-qty="<?php echo e($data['order_product']->quantity); ?>">
                                <div class="p-20 mb-15" style="position: relative; z-index: 10;">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center gap-3">
                                                
                                                <div class="flex-shrink-0">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productImage): ?>
                                                        <img src="<?php echo e(asset('storage/' . $productImage)); ?>" 
                                                             alt="<?php echo e($data['order_product']->name ?? 'Product'); ?>"
                                                             class="rounded"
                                                             style="width: 60px; height: 60px; ">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                             style="width: 60px; height: 60px;">
                                                            <i class="uil uil-image text-muted" style="font-size: 24px;"></i>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                
                                                <div>
                                                    <h5 class="text-primary fw-bold">
                                                        <?php echo e($data['order_product']->name ?? 'Product'); ?>

                                                    </h5>
                                                    <div class="d-flex flex-wrap gap-3 text-muted" style="font-size: 0.9em;">
                                                        <span><strong><?php echo e(trans('order::order.sku')); ?>:</strong> <?php echo e($sku); ?></span>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variantPath): ?>
                                                            <span><strong><?php echo e(trans('order::order.variant')); ?>:</strong>
                                                                <?php echo e($variantPath); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <span><strong><?php echo e(trans('order::order.vendor')); ?>:</strong> <?php echo e($vendorName); ?></span>
                                                        <span><strong><?php echo e(trans('order::order.ordered_qty')); ?>:</strong> <span
                                                                class="text-primary fw-bold"><?php echo e($data['order_product']->quantity); ?></span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="custom-badge bg-primary p-20" style="font-size: 1em; border-radius: 0;">
                                                <?php echo e(trans('order.total_allocated')); ?>: <span
                                                    id="total-<?php echo e($orderProductId); ?>" class="fw-bold">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table mb-0" style="border-color: #dee2e6;">
                                        <thead style="background-color: #003d82; color: white;">
                                            <tr>
                                                <th class="text-white fw-bold"><?php echo e(trans('order.regions')); ?></th>
                                                <th class="text-white fw-bold text-center">
                                                    <?php echo e(trans('order.available_stocks')); ?></th>
                                                <th class="text-white fw-bold text-center">
                                                    <?php echo e(trans('order.allocated_quantity')); ?></th>
                                                <th class="text-white fw-bold text-center">
                                                    <?php echo e(trans('order.remaining_stock')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $availableRegions = collect($data['regions'])->filter(function (
                                                    $region,
                                                ) {
                                                    return $region['available_stock'] > 0;
                                                });
                                            ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableRegions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regionId => $regionData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $isInsufficient = $regionData['remaining_stock'] < 0;
                                                ?>
                                                <tr class="<?php echo e($isInsufficient ? 'table-danger' : ''); ?>">
                                                    <td class="fw-bold">
                                                        <?php echo e($regionData['region']->name); ?>

                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-success fw-bold"
                                                            style="font-size: 1.1em;"><?php echo e($regionData['available_stock']); ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                            $max = min(
                                                                $regionData['available_stock'],
                                                                $data['order_product']->quantity,
                                                            );
                                                        ?>
                                                        <input type="number"
                                                            name="allocations[<?php echo e($orderProductId); ?>_<?php echo e($regionId); ?>][quantity]"
                                                            value="<?php echo e($regionData['allocated_quantity']); ?>" min="0"
                                                            max="<?php echo e($max); ?>"
                                                            class="form-control allocation-input text-center"
                                                            style="width: 100px; margin: 0 auto;"
                                                            data-order-product="<?php echo e($orderProductId); ?>"
                                                            data-region="<?php echo e($regionId); ?>"
                                                            data-available="<?php echo e($regionData['available_stock']); ?>">
                                                        <input type="hidden"
                                                            name="allocations[<?php echo e($orderProductId); ?>_<?php echo e($regionId); ?>][order_product_id]"
                                                            value="<?php echo e($orderProductId); ?>">
                                                        <input type="hidden"
                                                            name="allocations[<?php echo e($orderProductId); ?>_<?php echo e($regionId); ?>][region_id]"
                                                            value="<?php echo e($regionId); ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="remaining-<?php echo e($orderProductId); ?>-<?php echo e($regionId); ?>">
                                                            <?php echo e($regionData['remaining_stock']); ?>

                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="error-<?php echo e($orderProductId); ?>" style="display: none; margin-top: 15px;">
                                    <div class="alert alert-danger mb-0" role="alert">
                                        <i class="uil uil-exclamation-triangle me-2"></i>
                                        <?php echo e(trans('order.total_allocated_must_equal_ordered', ['quantity' => $data['order_product']->quantity])); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mt-40">
                            <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-outline-secondary">
                                <i class="uil uil-arrow-left me-2"></i><?php echo e(trans('common.back')); ?>

                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="uil uil-check-circle me-2"></i><?php echo e(trans('order.save_allocation')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .custom-badge {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            background-color: #235ba8 !important;
            color: white !important;
            padding: 10px 20px !important;
            font-size: 1em !important;
            border-radius: 0 !important;
            white-space: nowrap;
        }

        .custom-badge span {
            display: inline !important;
            visibility: visible !important;
            opacity: 1 !important;
            color: white !important;
        }

        .custom-badge.text-danger {
            background-color: #dc3545 !important;
        }

        .custom-badge.text-danger span {
            color: white !important;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            // Store totals in memory to prevent loss
            const productTotals = {};

            // Function to get ordered quantity for a product
            function getOrderedQuantity(orderProductId) {
                const inputs = $(`.allocation-input[data-order-product="${orderProductId}"]`);
                if (inputs.length === 0) return 0;
                
                const productContainer = inputs.first().closest('.mb-30');
                return parseInt(productContainer.data('ordered-qty')) || 0;
            }

            // Function to calculate total allocated for a product
            function calculateTotalAllocated(orderProductId) {
                let total = 0;
                const inputs = $(`.allocation-input[data-order-product="${orderProductId}"]`);
                
                console.log(`Calculating total for product ${orderProductId}, found ${inputs.length} inputs`);
                
                inputs.each(function() {
                    const val = $(this).val();
                    const numVal = parseInt(val);
                    console.log(`  Input value: "${val}", parsed: ${numVal}`);
                    
                    if (!isNaN(numVal) && numVal > 0) {
                        total += numVal;
                    }
                });
                
                console.log(`  Total calculated: ${total}`);
                productTotals[orderProductId] = total; // Store in memory
                return total;
            }

            // Function to update max values for all inputs of a product
            function updateMaxValues(orderProductId) {
                const orderedQuantity = getOrderedQuantity(orderProductId);
                let totalAllocated = calculateTotalAllocated(orderProductId);

                $(`.allocation-input[data-order-product="${orderProductId}"]`).each(function() {
                    const availableStock = $(this).data('available');
                    const currentValue = parseInt($(this).val()) || 0;
                    const otherAllocations = totalAllocated - currentValue;
                    const remainingToAllocate = orderedQuantity - otherAllocations;

                    const maxAllowed = Math.min(availableStock, remainingToAllocate);
                    $(this).attr('max', Math.max(0, maxAllowed));

                    if (currentValue > maxAllowed) {
                        $(this).val(Math.max(0, maxAllowed));
                    }
                });
            }

            // Function to safely update total display
            function updateTotalDisplay(orderProductId) {
                const total = productTotals[orderProductId] || 0;
                const totalElement = $(`#total-${orderProductId}`);
                
                if (totalElement.length > 0) {
                    totalElement.text(total);
                }
            }

            // Function to update display for a product
            function updateProductDisplay(orderProductId) {
                const totalAllocated = calculateTotalAllocated(orderProductId);
                const orderedQuantity = getOrderedQuantity(orderProductId);
                
                updateTotalDisplay(orderProductId);
                
                // Show/hide error based on match
                if (totalAllocated > 0 && totalAllocated !== orderedQuantity) {
                    $(`#error-${orderProductId}`).show();
                } else {
                    $(`#error-${orderProductId}`).hide();
                }
            }

            // Continuously monitor and restore totals every 100ms
            setInterval(function() {
                Object.keys(productTotals).forEach(function(orderProductId) {
                    const totalElement = $(`#total-${orderProductId}`);
                    if (totalElement.length > 0) {
                        const currentText = totalElement.text().trim();
                        const expectedText = String(productTotals[orderProductId]);
                        
                        // If total is empty or wrong, restore it
                        if (currentText === '' || currentText !== expectedText) {
                            totalElement.text(expectedText);
                        }
                    }
                });
            }, 100);

            // Calculate totals and validate on input change
            $('.allocation-input').on('input', function() {
                const orderProductId = $(this).data('order-product');
                const regionId = $(this).data('region');
                const availableStock = $(this).data('available');
                let quantity = parseInt($(this).val());
                
                if (isNaN(quantity) || quantity < 0) {
                    quantity = 0;
                    $(this).val('');
                }

                if (quantity > availableStock) {
                    quantity = availableStock;
                    $(this).val(quantity);
                }

                updateMaxValues(orderProductId);

                const remaining = availableStock - quantity;
                $(`.remaining-${orderProductId}-${regionId}`).text(remaining);

                const row = $(this).closest('tr');
                if (remaining < 0) {
                    row.addClass('table-danger');
                } else {
                    row.removeClass('table-danger');
                }

                updateProductDisplay(orderProductId);
            });

            // Form submission validation
            $('#allocationForm').on('submit', function(e) {
                let hasErrors = false;

                const productIds = new Set();
                $('.allocation-input').each(function() {
                    productIds.add($(this).data('order-product'));
                });

                productIds.forEach(function(orderProductId) {
                    const totalAllocated = calculateTotalAllocated(orderProductId);
                    const orderedQuantity = getOrderedQuantity(orderProductId);

                    updateTotalDisplay(orderProductId);

                    if (totalAllocated > 0 && totalAllocated !== orderedQuantity) {
                        hasErrors = true;
                        $(`#error-${orderProductId}`).show();
                    } else if (totalAllocated === 0) {
                        hasErrors = true;
                        $(`#error-${orderProductId}`).show();
                    } else {
                        $(`#error-${orderProductId}`).hide();
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    
                    const firstError = $('.alert-danger:visible').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                    
                    toastr.error('<?php echo e(trans('order.total_allocated_must_equal_ordered_message')); ?>', '<?php echo e(trans('order.validation_error')); ?>');
                }
            });

            // Initialize on page load
            const processedProducts = new Set();
            $('.allocation-input').each(function() {
                const orderProductId = $(this).data('order-product');
                if (!processedProducts.has(orderProductId)) {
                    updateMaxValues(orderProductId);
                    updateProductDisplay(orderProductId);
                    processedProducts.add(orderProductId);
                }
            });

            // Update remaining stock for all inputs on load
            $('.allocation-input').each(function() {
                const orderProductId = $(this).data('order-product');
                const regionId = $(this).data('region');
                const availableStock = $(this).data('available');
                const quantity = parseInt($(this).val()) || 0;
                const remaining = availableStock - quantity;
                $(`.remaining-${orderProductId}-${regionId}`).text(remaining);
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Order\resources/views/fulfillments/allocate.blade.php ENDPATH**/ ?>