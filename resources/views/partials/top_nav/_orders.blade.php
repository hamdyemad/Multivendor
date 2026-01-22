@php
    // Get unread orders count only
    $orderNotificationsCountQuery = \App\Models\AdminNotification::notViewedBy(auth()->id())
        ->where('type', 'new_order');
    
    // Filter by vendor if not admin
    if (isAdmin()) {
        $orderNotificationsCountQuery->whereNull('vendor_id');
    } else {
        $vendorId = auth()->user()->vendor->id;
        $orderNotificationsCountQuery->where(function($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
              ->orWhereNull('vendor_id');
        });
    }
    
    $ordersCount = $orderNotificationsCountQuery->count();
@endphp

<li class="nav-order">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <span class="orders-badge-custom" style="position: absolute !important; top: -8px !important; right: -8px !important; background: #5f63f2 !important; color: #ffffff !important; border-radius: 50% !important; min-width: 16px !important; height: 16px !important; display: {{ $ordersCount > 0 ? 'flex' : 'none' }} !important; align-items: center !important; justify-content: center !important; font-size: 10px !important; font-weight: 600 !important; line-height: 16px !important; z-index: 9999 !important; padding: 0 4px !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; border: 1.5px solid #fff !important;">{{ $ordersCount }}</span>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">
                {{ trans('menu.latest_orders') }} 
                <span class="badge-circle badge-primary ms-1 orders-badge-count">{{ $ordersCount }}</span>
            </h2>
            <div class="orders-list-container" id="orders-scroll-container" style="max-height: 400px; overflow-y: scroll; overflow-x: hidden; -webkit-overflow-scrolling: touch;">
                <ul id="orders-list" style="list-style: none; padding: 0; margin: 0;">
                    <!-- Orders will be loaded via AJAX -->
                </ul>
                <div id="orders-loading" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="orders-empty" class="text-center py-4" style="display: none;">
                    <p class="text-muted">{{ trans('menu.no_orders') }}</p>
                </div>
                <div id="orders-load-more-container" class="text-center py-3 border-top" style="display: none;">
                    <button id="orders-load-more-btn" class="btn btn-sm btn-light-primary" type="button" style="width: 90%; border-radius: 6px; font-weight: 500; padding: 8px 16px;">
                        <i class="uil uil-angle-down me-1"></i> {{ trans('common.load_more') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</li>

<script>
(function() {
    'use strict';
    
    let ordersCurrentPage = 1;
    let ordersIsLoading = false;
    let ordersHasMorePages = true;

    const ordersList = document.getElementById('orders-list');
    const ordersLoadingIndicator = document.getElementById('orders-loading');
    const ordersEmptyMessage = document.getElementById('orders-empty');
    const ordersLoadMoreContainer = document.getElementById('orders-load-more-container');
    const ordersLoadMoreBtn = document.getElementById('orders-load-more-btn');
    const ordersCounterBadge = document.querySelector('.orders-badge-custom');
    const ordersBadgeCount = document.querySelector('.orders-badge-count');

    function loadOrders(page) {
        if (ordersIsLoading || (!ordersHasMorePages && page > 1)) return;

        ordersIsLoading = true;
        if (ordersLoadingIndicator) ordersLoadingIndicator.style.display = 'block';
        if (ordersLoadMoreContainer) ordersLoadMoreContainer.style.display = 'none';

        const url = '{{ route("admin.notifications.index", ["lang" => app()->getLocale(), "countryCode" => strtolower(session("country_code", "eg"))]) }}?page=' + page + '&type=new_order';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (page === 1 && ordersList) ordersList.innerHTML = '';

            if (data.notifications.length === 0 && page === 1) {
                if (ordersEmptyMessage) ordersEmptyMessage.style.display = 'block';
                if (ordersList) ordersList.style.display = 'none';
            } else {
                if (ordersEmptyMessage) ordersEmptyMessage.style.display = 'none';
                if (ordersList) ordersList.style.display = 'block';

                data.notifications.forEach(notification => {
                    const li = document.createElement('li');
                    li.className = 'nav-notification__single d-flex flex-wrap';
                    li.innerHTML = `
                        <div class="nav-notification__type nav-notification__type--${notification.color}">
                            <i class="${notification.icon}"></i>
                        </div>
                        <div class="nav-notification__details">
                            <p>
                                <a href="${notification.url}" class="subject stretched-link text-truncate orders-link" data-id="${notification.id}" style="max-width: 180px;">${notification.title}</a>
                                <span>${notification.description}</span>
                            </p>
                            <p>
                                <span class="time-posted">${notification.created_at}</span>
                            </p>
                        </div>
                    `;
                    if (ordersList) ordersList.appendChild(li);
                });
            }

            ordersHasMorePages = data.has_more;
            ordersCurrentPage = data.current_page;
            
            if (ordersLoadMoreContainer && ordersHasMorePages && data.notifications.length > 0) {
                ordersLoadMoreContainer.style.display = 'block';
            }

            attachOrdersClickHandlers();
            ordersIsLoading = false;
            if (ordersLoadingIndicator) ordersLoadingIndicator.style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            ordersIsLoading = false;
            if (ordersLoadingIndicator) ordersLoadingIndicator.style.display = 'none';
        });
    }

    function attachOrdersClickHandlers() {
        document.querySelectorAll('.orders-link').forEach(link => {
            const newLink = link.cloneNode(true);
            if (link.parentNode) link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function() {
                const currentCount = parseInt(ordersCounterBadge ? ordersCounterBadge.textContent : '0') || 0;
                if (currentCount > 0) {
                    const newCount = currentCount - 1;
                    if (ordersCounterBadge) {
                        ordersCounterBadge.textContent = newCount;
                        ordersCounterBadge.style.display = newCount > 0 ? 'flex' : 'none';
                    }
                    if (ordersBadgeCount) ordersBadgeCount.textContent = newCount;
                }

                const listItem = this.closest('li');
                if (listItem) listItem.remove();

                if (ordersList && ordersList.children.length === 0) {
                    if (ordersEmptyMessage) ordersEmptyMessage.style.display = 'block';
                    if (ordersList) ordersList.style.display = 'none';
                    if (ordersLoadMoreContainer) ordersLoadMoreContainer.style.display = 'none';
                }
            });
        });
    }

    if (ordersLoadMoreBtn) {
        ordersLoadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadOrders(ordersCurrentPage + 1);
        });
    }

    setTimeout(function() {
        loadOrders(1);
    }, 500);
})();
</script>
