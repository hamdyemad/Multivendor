<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500"><?php echo e(trans('dashboard.top_vendors')); ?></h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.vendor_name')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.orders_count')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.total_selling')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.joined_at')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.actions')); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topVendors ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.vendors.show', $vendor->id)); ?>" target="_blank">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendor->media && $vendor->media->first()): ?>
                                    <img class="rounded-circle"
                                        src="<?php echo e(asset('storage/' . $vendor->media->first()->path)); ?>"
                                        alt="vendor" style="width: 40px; height: 40px;">
                                <?php else: ?>
                                    <img class="rounded-circle"
                                        src="<?php echo e(asset('assets/img/default.png')); ?>"
                                        alt="vendor" style="width: 40px; height: 40px;">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span class="ms-3"><?php echo e($vendor->getTranslation('name', app()->getLocale())); ?></span>
                            </a>
                        </td>
                        <td><?php echo e($vendor->total_orders_count ?? 0); ?></td>
                        <td class="fw-bold text-success"><?php echo e(number_format($vendor->total_orders_sum_price ?? 0, 2)); ?> <?php echo e(currency()); ?></td>
                        <td><?php echo e($vendor->created_at ? $vendor->created_at : '-'); ?></td>
                        <td class="actions">
                            <a href="<?php echo e(route('admin.vendors.show', $vendor->id)); ?>" target="_blank" class="btn btn-sm btn-primary">
                                <i class="uil uil-eye m-0"></i>
                            </a>
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
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/top-vendors.blade.php ENDPATH**/ ?>