<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500"><?php echo e(trans('dashboard.top_selling_products')); ?></h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.product_name')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.vendor_name')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.sold_count')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.total')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.actions')); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topSellingProducts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->vendorProduct && $item->vendorProduct->product): ?>
                                <?php $product = $item->vendorProduct->product; ?>
                                <a href="<?php echo e(route('admin.products.show', $product->id)); ?>" target="_blank" class="d-flex align-items-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->mainImage): ?>
                                        <img class="rounded-circle"
                                            src="<?php echo e(asset('storage/' . $product->mainImage->path)); ?>"
                                            alt="product" style="width: 40px; height: 40px;">
                                    <?php else: ?>
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="uil uil-box text-muted"></i>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="ms-3"><?php echo e($product->title); ?></span>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->vendorData): ?>
                                <a href="<?php echo e(route('admin.vendors.show', $item->vendor_id)); ?>" target="_blank">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->vendorData->logo): ?>
                                        <img class="rounded-circle"
                                            src="<?php echo e(asset('storage/' . $item->vendorData->logo->path)); ?>"
                                            alt="vendor" style="width: 40px; height: 40px;">
                                    <?php else: ?>
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="uil uil-store text-muted"></i>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="ms-3"><?php echo e($item->vendorData->getTranslation('name', app()->getLocale())); ?></span>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td><?php echo e($item->total_sold ?? 0); ?></td>
                        <td class="fw-bold text-success"><?php echo e(number_format($item->total_revenue ?? 0, 2)); ?> <?php echo e(currency()); ?></td>
                        <td class="actions">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->vendorProduct && $item->vendorProduct->product): ?>
                            <a href="<?php echo e(route('admin.products.show', $item->vendorProduct->product->id)); ?>" target="_blank" class="btn btn-sm btn-primary">
                                <i class="uil uil-eye m-0"></i>
                            </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted"><?php echo e(trans('common.no_data_available')); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/top-selling-products.blade.php ENDPATH**/ ?>