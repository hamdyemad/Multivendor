<?php
    $langs = collect();
    try {
        $langs = \App\Models\Language::all();
    } catch (\Exception $e) {
        // Silently fail - use empty collection
        $langs = collect();
    }
?>
<li class="nav-flag-select">
    <div class="dropdown-custom">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch(app()->getLocale()):
            case ('en'): ?>
                <a href="javascript:;" class="nav-item-toggle"><img src="<?php echo e(asset('assets/img/uk.webp')); ?>" alt="" class="rounded-circle"></a>
                <?php break; ?>
            <?php case ('ar'): ?>
                <a href="javascript:;" class="nav-item-toggle"><img src="<?php echo e(asset('assets/img/eg.webp')); ?>" alt="" class="rounded-circle"></a>
                <?php break; ?>
            <?php default: ?>
                <a href="javascript:;" class="nav-item-toggle">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(LaravelLocalization::getCurrentLocale() == 'ar'): ?>
                        <img src="<?php echo e(asset('assets/img/eg.webp')); ?>" alt="" class="rounded-circle">
                    <?php else: ?>
                        <img src="<?php echo e(asset('assets/img/uk.webp')); ?>" alt="" class="rounded-circle">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
                <?php break; ?>
        <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="dropdown-wrapper dropdown-wrapper--small">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $langs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a hreflang="<?php echo e($lang->code); ?>"
                href="<?php echo e(LaravelLocalization::getLocalizedURL($lang->code, null, [], true)); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lang->code == 'ar'): ?>
                        <img src="<?php echo e(asset('assets/img/eg.webp')); ?>" alt="">
                    <?php else: ?>
                        <img src="<?php echo e(asset('assets/img/uk.webp')); ?>" alt="">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lang->code == 'ar'): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() == 'ar'): ?>
                            العربيه
                        <?php else: ?>
                            العربيه
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() == 'ar'): ?>
                            English
                        <?php else: ?>
                            English
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</li>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_language_selector.blade.php ENDPATH**/ ?>