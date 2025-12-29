<li class="nav-author">
    <div class="dropdown-custom">
        <a href="javascript:;" class="nav-item-toggle">
            <?php
                $userImage = Auth::user()->image
                    ? asset('storage/' . Auth::user()->image)
                    : asset('assets/img/author-nav.jpg');
            ?>
            <img src="<?php echo e($userImage); ?>" alt="" class="rounded-circle"
                style="width: 32px; height: 32px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::check()): ?>
                <span class="nav-item__title"><i class="las la-angle-down nav-item__arrow"></i></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>
        <div class="dropdown-wrapper">
            <div class="nav-author__info">
                <div class="author-img">
                    <img src="<?php echo e($userImage); ?>" alt="" class="rounded-circle"
                        style="width: 46px; height: 46px;">
                </div>
                <div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::check()): ?>
                        <h6 class="text-lowercase">
                            <?php echo e(truncateString(auth()->user()->getTranslation('name', app()->getLocale()),10)); ?>

                        </h6>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isAdmin() && auth()->user()->vendor): ?>
                            <span class="badge badge-round badge-success" style="font-size: 10px;">
                                <i class="uil uil-store me-1"></i><?php echo e(auth()->user()->vendor->name); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count(auth()->user()->roles) > 0): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = auth()->user()->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-round badge-info" style="font-size: 10px;">
                                    <i class="uil uil-shield-check me-1"></i><?php echo e($role->getTranslation('name', app()->getLocale())); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="nav-author__options">
                <ul>
                    <li>
                        <a href="<?php echo e(route('admin.profile.index')); ?>">
                            <img src="<?php echo e(asset('assets/img/svg/user.svg')); ?>" alt="user" class="svg">
                            <?php echo e(trans('admin.edit_profile') ?? 'Edit Profile'); ?></a>
                    </li>
                </ul>
                <a href="" class="nav-author__signout"
                    onclick="event.preventDefault();document.getElementById('logout').submit();"
                    style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%); color: #fff; border-radius: 8px; padding: 10px 20px; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(238, 90, 90, 0.3);"
                    onmouseover="this.style.background='linear-gradient(135deg, #ee5a5a 0%, #dc4747 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(238, 90, 90, 0.4)';"
                    onmouseout="this.style.background='linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(238, 90, 90, 0.3)';">
                    <i class="uil uil-sign-out-alt" style="font-size: 18px;"></i>
                    <?php echo e(__('admin.sign_out') ?? 'Sign Out'); ?></a>
                <form style="display:none;" id="logout" action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('post'); ?>
                </form>
            </div>
        </div>
    </div>
</li>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_user_profile.blade.php ENDPATH**/ ?>