

<?php $__env->startSection('title'); ?>
    <?php echo e($title ?? 'Dashboard'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="crm mb-25">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <div class="breadcrumb-action justify-content-center flex-wrap">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#"><i
                                                class="uil uil-estate"></i><?php echo e(trans('dashboard.title')); ?></a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                
                <?php echo $__env->make('pages.dashboard.withdraw-transactions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('pages.dashboard.stats-overview', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('pages.dashboard.income-and-expenses', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('pages.dashboard.stats-cards', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                
                <?php echo $__env->make('pages.dashboard.orders-overview', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
                <div class="col-12">
                    <div class="row">
                        <?php echo $__env->make('pages.dashboard.total-sales-chart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('pages.dashboard.earnings-chart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>

                <?php echo $__env->make('pages.dashboard.top-selling-products', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('pages.dashboard.latest-orders', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('pages.dashboard.best-customers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds())): ?>
                    <?php echo $__env->make('pages.dashboard.top-vendors', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo $__env->make('pages.dashboard.recent-activities', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            </div>
            <!-- ends: .row -->
        </div>
    </div>

    
    <?php echo $__env->make('pages.dashboard.charts-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/dashboard.blade.php ENDPATH**/ ?>