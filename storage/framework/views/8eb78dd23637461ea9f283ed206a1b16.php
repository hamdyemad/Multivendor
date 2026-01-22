<?php
    // Get unread notifications count only
    $notificationsCountQuery = \App\Models\AdminNotification::notViewedBy(auth()->id());
    
    // Filter by vendor if not admin
    if (isAdmin()) {
        $notificationsCountQuery->where(function($q) {
            $q->whereNull('vendor_id')
              ->orWhereIn('type', ['new_refund_request', 'refund_status_changed']);
        });
    } else {
        $vendorId = auth()->user()->vendor->id;
        $notificationsCountQuery->where(function($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
              ->orWhereNull('vendor_id');
        })->whereNotIn('type', ['vendor_request', 'new_message']);
    }
    
    $notificationsCount = $notificationsCountQuery->count();
?>

<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <img class="svg" src="<?php echo e(asset('assets/img/svg/alarm.svg')); ?>" alt="img">
            <span class="notification-badge-custom" style="position: absolute !important; top: -8px !important; right: -8px !important; background: #fa8b0c !important; color: #ffffff !important; border-radius: 50% !important; min-width: 16px !important; height: 16px !important; display: <?php echo e($notificationsCount > 0 ? 'flex' : 'none'); ?> !important; align-items: center !important; justify-content: center !important; font-size: 10px !important; font-weight: 600 !important; line-height: 16px !important; z-index: 9999 !important; padding: 0 4px !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; border: 1.5px solid #fff !important;"><?php echo e($notificationsCount); ?></span>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">
                <?php echo e(trans('menu.notifications.title')); ?> 
                <span class="badge-circle badge-warning ms-1 notification-badge-count"><?php echo e($notificationsCount); ?></span>
            </h2>
            <div class="notification-list-container" id="notification-scroll-container" style="max-height: 400px; overflow-y: scroll; overflow-x: hidden; -webkit-overflow-scrolling: touch;">
                <ul id="notifications-list" style="list-style: none; padding: 0; margin: 0;">
                    <!-- Notifications will be loaded via AJAX -->
                </ul>
                <div id="notifications-loading" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="notifications-empty" class="text-center py-4" style="display: none;">
                    <p class="text-muted"><?php echo e(trans('menu.no_notifications')); ?></p>
                </div>
                <div id="load-more-container" class="text-center py-3 border-top" style="display: none;">
                    <button id="load-more-btn" class="btn btn-sm btn-light-primary" type="button" style="width: 90%; border-radius: 6px; font-weight: 500; padding: 8px 16px;">
                        <i class="uil uil-angle-down me-1"></i> <?php echo e(trans('common.load_more')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>
</li>

<script>
(function() {
    'use strict';
    
    let currentPage = 1;
    let isLoading = false;
    let hasMorePages = true;

    const notificationsList = document.getElementById('notifications-list');
    const loadingIndicator = document.getElementById('notifications-loading');
    const emptyMessage = document.getElementById('notifications-empty');
    const loadMoreContainer = document.getElementById('load-more-container');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const counterBadge = document.querySelector('.notification-badge-custom');
    const badgeCount = document.querySelector('.notification-badge-count');

    console.log('=== Notification System Initialized ===');

    // Load notifications
    function loadNotifications(page) {
        if (isLoading) {
            console.log('⏸️ Already loading, skipping...');
            return;
        }
        
        if (!hasMorePages && page > 1) {
            console.log('⏸️ No more pages available');
            return;
        }

        console.log('📥 Loading page:', page);
        isLoading = true;
        
        if (loadingIndicator) {
            loadingIndicator.style.display = 'block';
        }
        
        if (loadMoreContainer) {
            loadMoreContainer.style.display = 'none';
        }

        const url = '<?php echo e(route("admin.notifications.index", ["lang" => app()->getLocale(), "countryCode" => strtolower(session("country_code", "eg"))])); ?>?page=' + page;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Data received:', {
                page: data.current_page,
                total: data.total,
                count: data.notifications.length,
                hasMore: data.has_more
            });
            
            if (page === 1 && notificationsList) {
                notificationsList.innerHTML = '';
            }

            if (data.notifications.length === 0 && page === 1) {
                if (emptyMessage) emptyMessage.style.display = 'block';
                if (notificationsList) notificationsList.style.display = 'none';
            } else {
                if (emptyMessage) emptyMessage.style.display = 'none';
                if (notificationsList) notificationsList.style.display = 'block';

                data.notifications.forEach((notification, index) => {
                    const li = document.createElement('li');
                    li.className = 'nav-notification__single nav-notification__single--unread d-flex flex-wrap';
                    li.innerHTML = `
                        <div class="nav-notification__type nav-notification__type--${notification.color}">
                            <i class="${notification.icon}"></i>
                        </div>
                        <div class="nav-notification__details">
                            <p>
                                <a href="${notification.url}" class="subject stretched-link text-truncate notification-link" data-id="${notification.id}" style="max-width: 180px;">${notification.title}</a>
                                <span>${notification.description}</span>
                            </p>
                            <p>
                                <span class="time-posted">${notification.created_at}</span>
                            </p>
                        </div>
                    `;
                    if (notificationsList) {
                        notificationsList.appendChild(li);
                    }
                });
            }

            hasMorePages = data.has_more;
            currentPage = data.current_page;
            
            // Show/hide load more button
            if (loadMoreContainer) {
                if (hasMorePages && data.notifications.length > 0) {
                    loadMoreContainer.style.display = 'block';
                } else {
                    loadMoreContainer.style.display = 'none';
                }
            }
            
            console.log('📊 State updated:', { 
                currentPage, 
                hasMorePages,
                totalInList: notificationsList ? notificationsList.children.length : 0
            });

            attachNotificationClickHandlers();
            
            isLoading = false;
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('❌ Error loading notifications:', error);
            isLoading = false;
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
            if (loadMoreContainer && hasMorePages) {
                loadMoreContainer.style.display = 'block';
            }
        });
    }

    // Update notification counter
    function updateCounter() {
        const url = '<?php echo e(route("admin.notifications.count", ["lang" => app()->getLocale(), "countryCode" => strtolower(session("country_code", "eg"))])); ?>';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const count = data.count;
            if (counterBadge) {
                counterBadge.textContent = count;
                counterBadge.style.display = count > 0 ? 'flex' : 'none';
            }
            if (badgeCount) {
                badgeCount.textContent = count;
            }
        })
        .catch(error => {
            console.error('Error updating counter:', error);
        });
    }

    // Handle notification click
    function attachNotificationClickHandlers() {
        const links = document.querySelectorAll('.notification-link');
        links.forEach(link => {
            // Remove old handler if exists
            const newLink = link.cloneNode(true);
            if (link.parentNode) {
                link.parentNode.replaceChild(newLink, link);
            }
            
            newLink.addEventListener('click', function(e) {
                const notificationId = this.dataset.id;
                console.log('🔔 Notification clicked:', notificationId);
                
                // Update counter immediately
                const currentCount = parseInt(counterBadge ? counterBadge.textContent : '0') || 0;
                if (currentCount > 0) {
                    const newCount = currentCount - 1;
                    if (counterBadge) {
                        counterBadge.textContent = newCount;
                        counterBadge.style.display = newCount > 0 ? 'flex' : 'none';
                    }
                    if (badgeCount) {
                        badgeCount.textContent = newCount;
                    }
                }

                // Remove from list
                const listItem = this.closest('li');
                if (listItem) listItem.remove();

                // Check if empty
                if (notificationsList && notificationsList.children.length === 0) {
                    if (emptyMessage) emptyMessage.style.display = 'block';
                    if (notificationsList) notificationsList.style.display = 'none';
                    if (loadMoreContainer) loadMoreContainer.style.display = 'none';
                }
            });
        });
    }

    // Setup load more button
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔽 Load More clicked, loading page:', currentPage + 1);
            loadNotifications(currentPage + 1);
        });
        console.log('✅ Load More button attached');
    }

    // Initial load
    console.log('🚀 Starting initial load...');
    setTimeout(function() {
        loadNotifications(1);
    }, 500);

    // Update counter every 30 seconds
    setInterval(updateCounter, 30000);
    
    console.log('=== Setup Complete ===');
})();
</script>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_notifications.blade.php ENDPATH**/ ?>