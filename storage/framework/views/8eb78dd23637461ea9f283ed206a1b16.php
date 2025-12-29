<?php
    // Collect all notifications from different sources
    $notifications = collect();
    
    // 1. Vendor Requests (pending vendors where active = 0)
    if (isAdmin()) {
        $vendorRequests = \Modules\Vendor\app\Models\Vendor::where('active', 0)
            ->with('user')
            ->get()
            ->map(function($vendor) {
                return [
                    'type' => 'vendor_request',
                    'icon' => 'uil-user-plus',
                    'color' => 'warning',
                    'title' => $vendor->name,
                    'description' => trans('menu.become a vendor requests.wants_to_become'),
                    'url' => route('admin.vendors.show', $vendor->id),
                    'created_at' => $vendor->created_at,
                ];
            });
        $notifications = $notifications->merge($vendorRequests);
    }
    
    // 2. Recent Orders
    $ordersQuery = \Modules\Order\app\Models\Order::with('customer');
    if (!isAdmin() && auth()->user()->vendor) {
        $ordersQuery->whereHas('products', function($q) {
            $q->where('vendor_id', auth()->user()->vendor->id);
        });
    }
    $recentOrders = $ordersQuery->orderBy('created_at', 'desc')
        ->limit(10)
        ->get()
        ->map(function($order) {
            return [
                'type' => 'order',
                'icon' => 'uil-shopping-bag',
                'color' => 'primary',
                'title' => trans('menu.order') . ' #' . $order->order_number,
                'description' => $order->customer_name ?? 'N/A',
                'url' => route('admin.orders.show', $order->id),
                'created_at' => $order->created_at,
            ];
        });
    $notifications = $notifications->merge($recentOrders);
    
    // 3. User Messages (pending messages)
    if (isAdmin()) {
        $messages = \Modules\SystemSetting\app\Models\Message::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($message) {
                return [
                    'type' => 'message',
                    'icon' => 'uil-envelope',
                    'color' => 'success',
                    'title' => $message->name,
                    'description' => $message->title,
                    'url' => route('admin.messages.show', $message->id),
                    'created_at' => $message->created_at,
                ];
            });
        $notifications = $notifications->merge($messages);
    }

    // 4. Push Notifications for Vendors (only unviewed)
    if (!isAdmin() && auth()->user()->vendor) {
        $vendorId = auth()->user()->vendor->id;
        $userId = auth()->id();
        $pushNotifications = \Modules\SystemSetting\app\Models\PushNotification::where(function($q) use ($vendorId) {
                // Notifications sent to all vendors
                $q->where('type', 'all_vendors')
                // Or notifications sent to this specific vendor
                ->orWhere(function($q2) use ($vendorId) {
                    $q2->where('type', 'specific_vendors')
                       ->whereHas('vendors', function($q3) use ($vendorId) {
                           $q3->where('vendors.id', $vendorId);
                       });
                });
            })
            // Exclude viewed notifications
            ->whereDoesntHave('views', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($notification) {
                $locale = app()->getLocale();
                return [
                    'type' => 'push_notification',
                    'icon' => 'uil-bell',
                    'color' => 'info',
                    'title' => $notification->getTranslation('title', $locale) ?? $notification->getTranslation('title', 'en'),
                    'description' => \Illuminate\Support\Str::limit(strip_tags($notification->getTranslation('description', $locale) ?? $notification->getTranslation('description', 'en')), 50),
                    'url' => route('admin.system-settings.push-notifications.view', ['id' => $notification->id]),
                    'created_at' => $notification->created_at,
                    'image' => $notification->image ? formatImage($notification->image) : null,
                ];
            });
        $notifications = $notifications->merge($pushNotifications);
    }
    
    // Sort by created_at and take latest 5
    $notifications = $notifications->sortByDesc('created_at')->take(5);
    $notificationsCount = $notifications->count();
?>

<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <img class="svg" src="<?php echo e(asset('assets/img/svg/alarm.svg')); ?>" alt="img">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notificationsCount > 0): ?>
                <span class="nav-item__badge" style="position: absolute; top: -8px; background-color: #fa8b0c; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;"><?php echo e($notificationsCount); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title"><?php echo e(trans('menu.notifications.title')); ?> <span class="badge-circle badge-warning ms-1"><?php echo e($notificationsCount); ?></span></h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notificationsCount > 0): ?>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-notification__single <?php echo e(empty($notification['is_viewed']) ? 'nav-notification__single--unread' : ''); ?> d-flex flex-wrap">
                            <div class="nav-notification__type nav-notification__type--<?php echo e($notification['color']); ?>">
                                <i class="<?php echo e($notification['icon']); ?>"></i>
                            </div>
                            <div class="nav-notification__details">
                                <p>
                                    <a href="<?php echo e($notification['url']); ?>" class="subject stretched-link text-truncate" style="max-width: 180px;"><?php echo e($notification['title']); ?></a>
                                    <span><?php echo e($notification['description']); ?></span>
                                </p>
                                <p>
                                    <span class="time-posted"><?php echo e($notification['created_at']); ?></span>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted"><?php echo e(trans('menu.no_notifications')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="dropdown-wrapper__more"><?php echo e(trans('menu.see_all_notifications')); ?></a>
        </div>
    </div>
</li>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_notifications.blade.php ENDPATH**/ ?>