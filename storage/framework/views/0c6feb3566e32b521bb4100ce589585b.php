<?php
    $pendingRequests = \Modules\Vendor\app\Models\VendorRequest::where('status', 'pending')
        ->latest()
        ->take(5)
        ->get();
    $pendingCount = \Modules\Vendor\app\Models\VendorRequest::where('status', 'pending')->count();
?>

<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingCount > 0): ?>
                <span class="nav-item__badge" style="position: absolute; top: -8px; background-color: #01b8ff; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;"><?php echo e($pendingCount); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title"><?php echo e(trans('menu.become a vendor requests.pending')); ?> <span class="badge-circle badge-info ms-1"><?php echo e($pendingCount); ?></span></h2>
            <ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__type nav-notification__type--info">
                            <i class="uil uil-user"></i>
                        </div>
                        <div class="nav-notification__details">
                            <p>
                                <a href="<?php echo e(route('admin.vendor-requests.index')); ?>" class="subject stretched-link text-truncate" style="max-width: 180px;"><?php echo e($request->company_name); ?></a>
                                <span><?php echo e(trans('menu.become a vendor requests.wants_to_become')); ?></span>
                            </p>
                            <p>
                                <span class="time-posted"><?php echo e($request->created_at); ?></span>
                            </p>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__details">
                            <p class="text-muted"><?php echo e(trans('menu.become a vendor requests.no_pending')); ?></p>
                        </div>
                    </li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>
            <a href="<?php echo e(route('admin.vendor-requests.index')); ?>" class="dropdown-wrapper__more"><?php echo e(trans('menu.become a vendor requests.see_all')); ?></a>
        </div>
    </div>
</li>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_become_vendor_requests.blade.php ENDPATH**/ ?>