<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500"><?php echo e(trans('dashboard.best_customers')); ?></h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.name')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.orders_count')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.revenue')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.joined_at')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.actions')); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $bestCustomers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customer->image): ?>
                                    <img class="rounded-circle" src="<?php echo e(asset('storage/' . $customer->image)); ?>" alt="user" style="width: 40px; height: 40px;">
                                <?php else: ?>
                                    <div class="bg-<?php echo e(($customer->customer_type ?? 'registered') == 'external' ? 'secondary' : 'primary'); ?> rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                        <span style="font-size: 14px;"><?php echo e(strtoupper(substr($customer->first_name ?? 'C', 0, 1))); ?></span>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="ms-3">
                                    <span class="d-block"><?php echo e($customer->full_name ?? ($customer->first_name . ' ' . $customer->last_name)); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($customer->customer_type ?? 'registered') == 'external'): ?>
                                        <small class="text-muted">(<?php echo e(trans('common.guest')); ?>)</small>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-center"><?php echo e($customer->orders_count ?? 0); ?></td>
                        <td class="fw-bold text-success text-center"><?php echo e(number_format($customer->orders_sum_total_price ?? 0, 2)); ?> <?php echo e(currency()); ?></td>
                        <td class="text-center"><?php echo e($customer->created_at ? \Carbon\Carbon::parse($customer->created_at)->format('d M, Y, h:i A') : '-'); ?></td>
                        <td class="actions text-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customer->id): ?>
                                <a href="<?php echo e(route('admin.customers.show', $customer->id)); ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="uil uil-eye m-0"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
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
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/best-customers.blade.php ENDPATH**/ ?>