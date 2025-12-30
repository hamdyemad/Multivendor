

<nav class="navbar navbar-light" style="box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);">
    <div class="navbar-left">
        <div class="logo-area">
            <a class="navbar-brand" href="<?php echo e(route('admin.dashboard')); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() == 'ar'): ?>
                    <img src="<?php echo e(asset('assets/img/logo_ar.png')); ?>" alt="svg">
                <?php else: ?>
                    <img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="svg">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            <a href="#" class="sidebar-toggle">
                <img class="svg" src="<?php echo e(asset('assets/img/svg/align-center-alt.svg')); ?>" alt="img"></a>
        </div>
    </div>
    <div class="navbar-right">
        <ul class="navbar-right__menu">
            
            <?php echo $__env->make('partials.top_nav._country_selector', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
            <?php echo $__env->make('partials.top_nav._vendors_withdraw_requests', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('partials.top_nav._become_vendor_requests', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php echo $__env->make('partials.top_nav._orders', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('partials.top_nav._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('partials.top_nav._notifications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('partials.top_nav._language_selector', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('partials.top_nav._user_profile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </ul>
        <div class="navbar-right__mobileAction d-md-none">
            <a href="#" class="btn-search">
                <img src="<?php echo e(asset('assets/img/svg/search.svg')); ?>" alt="search" class="svg feather-search">
                <img src="<?php echo e(asset('assets/img/svg/x.svg')); ?>" alt="x" class="svg feather-x">
            </a>
            <a href="#" class="btn-author-action">
                <img src="<?php echo e(asset('assets/img/svg/more-vertical.svg')); ?>" alt="more-vertical" class="svg"></a>
        </div>
    </div>
</nav>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_top_nav.blade.php ENDPATH**/ ?>