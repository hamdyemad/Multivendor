<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
<li class="nav-message">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <img class="svg" src="<?php echo e(asset('assets/img/svg/message.svg')); ?>" alt="img">
            <?php
                $unreadMessagesCount = \Modules\SystemSetting\app\Models\Message::where('status', 'pending')->count();
                $latestMessages = \Modules\SystemSetting\app\Models\Message::where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadMessagesCount > 0): ?>
                <span class="nav-item__badge" style="position: absolute; top: -8px; background-color: #20c997; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;"><?php echo e($unreadMessagesCount); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title"><?php echo e(trans('menu.messages')); ?> <span class="badge-circle badge-success ms-1"><?php echo e($unreadMessagesCount); ?></span></h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadMessagesCount > 0): ?>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $latestMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="author-online has-new-message">
                            <div class="user-avater">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #5f63f2; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                                    <?php echo e(strtoupper(substr($message->name, 0, 1))); ?>

                                </div>
                            </div>
                            <div class="user-message">
                                <p>
                                    <a href="<?php echo e(route('admin.messages.show', $message->id)); ?>" class="subject stretched-link text-truncate" style="max-width: 180px;"><?php echo e($message->name); ?></a>
                                    <span class="time-posted"><?php echo e($message->created_at); ?></span>
                                </p>
                                <p>
                                    <span class="desc text-truncate" style="max-width: 215px;"><?php echo e($message->title); ?></span>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted"><?php echo e(trans('menu.no_messages')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="<?php echo e(route('admin.messages.index')); ?>" class="dropdown-wrapper__more"><?php echo e(trans('menu.see_all_messages')); ?></a>
        </div>
    </div>
</li>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_messages.blade.php ENDPATH**/ ?>