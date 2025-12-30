<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500"><?php echo e(trans('dashboard.latest_orders')); ?></h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.order_number')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.customer')); ?></span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.total')); ?></span></th>
                        <th><span class="userDatatable-title">Status</span></th>
                        <th><span class="userDatatable-title"><?php echo e(trans('dashboard.actions')); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $latestOrders ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><span class="fw-medium">#<?php echo e($order->order_number ?? $order->id); ?></span></td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->customer): ?>
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="me-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->customer->image): ?>
                                            <img src="<?php echo e(asset($order->customer->image)); ?>" alt="<?php echo e($order->customer->full_name); ?>" class="rounded-circle" style="width: 35px; height: 35px;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px; font-size: 14px;">
                                                <?php echo e(strtoupper(substr($order->customer->first_name ?? 'C', 0, 1))); ?>

                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <span><?php echo e($order->customer->full_name ?? $order->customer->email); ?></span>
                                </div>
                            <?php elseif($order->customer_name): ?>
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="me-2">
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px; font-size: 14px;">
                                            <?php echo e(strtoupper(substr($order->customer_name, 0, 1))); ?>

                                        </div>
                                    </div>
                                    <span><?php echo e($order->customer_name); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="fw-bold text-success"><?php echo e(number_format($order->vendor_product_total ?? $order->total_price ?? 0, 2)); ?> <?php echo e(currency()); ?></td>
                        <td class="text-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->stage): ?>
                                <span class="badge badge-round badge-lg" style="background-color: <?php echo e($order->stage->color ?? '#6c757d'); ?>; color: #fff;">
                                    <?php echo e($order->stage->name ?? $order->stage->getTranslation('name', app()->getLocale()) ?? '-'); ?>

                                </span>
                            <?php elseif($order->stage_id): ?>
                                <span class="badge bg-secondary badge-round badge-lg">ID: <?php echo e($order->stage_id); ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary badge-round badge-lg">-</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" target="_blank" class="btn btn-sm btn-primary">
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
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/latest-orders.blade.php ENDPATH**/ ?>