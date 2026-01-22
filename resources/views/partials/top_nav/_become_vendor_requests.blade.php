@php
    // Get unread vendor requests count only
    $vendorRequestsCountQuery = \App\Models\AdminNotification::notViewedBy(auth()->id())
        ->where('type', 'vendor_request')
        ->whereNull('vendor_id');
    
    $pendingCount = $vendorRequestsCountQuery->count();
@endphp

<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            <span class="vendor-requests-badge-custom" style="position: absolute !important; top: -8px !important; right: -8px !important; background: #01b8ff !important; color: #ffffff !important; border-radius: 50% !important; min-width: 16px !important; height: 16px !important; display: {{ $pendingCount > 0 ? 'flex' : 'none' }} !important; align-items: center !important; justify-content: center !important; font-size: 10px !important; font-weight: 600 !important; line-height: 16px !important; z-index: 9999 !important; padding: 0 4px !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; border: 1.5px solid #fff !important;">{{ $pendingCount }}</span>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">
                {{ trans('menu.become a vendor requests.pending') }} 
                <span class="badge-circle badge-info ms-1 vendor-requests-badge-count">{{ $pendingCount }}</span>
            </h2>
            <div class="vendor-requests-list-container" id="vendor-requests-scroll-container" style="max-height: 400px; overflow-y: scroll; overflow-x: hidden; -webkit-overflow-scrolling: touch;">
                <ul id="vendor-requests-list" style="list-style: none; padding: 0; margin: 0;">
                    <!-- Vendor requests will be loaded via AJAX -->
                </ul>
                <div id="vendor-requests-loading" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="vendor-requests-empty" class="text-center py-4" style="display: none;">
                    <p class="text-muted">{{ trans('menu.become a vendor requests.no_pending') }}</p>
                </div>
                <div id="vendor-requests-load-more-container" class="text-center py-3 border-top" style="display: none;">
                    <button id="vendor-requests-load-more-btn" class="btn btn-sm btn-light-primary" type="button" style="width: 90%; border-radius: 6px; font-weight: 500; padding: 8px 16px;">
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
    
    let vendorRequestsCurrentPage = 1;
    let vendorRequestsIsLoading = false;
    let vendorRequestsHasMorePages = true;

    const vendorRequestsList = document.getElementById('vendor-requests-list');
    const vendorRequestsLoadingIndicator = document.getElementById('vendor-requests-loading');
    const vendorRequestsEmptyMessage = document.getElementById('vendor-requests-empty');
    const vendorRequestsLoadMoreContainer = document.getElementById('vendor-requests-load-more-container');
    const vendorRequestsLoadMoreBtn = document.getElementById('vendor-requests-load-more-btn');
    const vendorRequestsCounterBadge = document.querySelector('.vendor-requests-badge-custom');
    const vendorRequestsBadgeCount = document.querySelector('.vendor-requests-badge-count');

    function loadVendorRequests(page) {
        if (vendorRequestsIsLoading || (!vendorRequestsHasMorePages && page > 1)) return;

        vendorRequestsIsLoading = true;
        if (vendorRequestsLoadingIndicator) vendorRequestsLoadingIndicator.style.display = 'block';
        if (vendorRequestsLoadMoreContainer) vendorRequestsLoadMoreContainer.style.display = 'none';

        const url = '{{ route("admin.notifications.index", ["lang" => app()->getLocale(), "countryCode" => strtolower(session("country_code", "eg"))]) }}?page=' + page + '&type=vendor_request';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (page === 1 && vendorRequestsList) vendorRequestsList.innerHTML = '';

            if (data.notifications.length === 0 && page === 1) {
                if (vendorRequestsEmptyMessage) vendorRequestsEmptyMessage.style.display = 'block';
                if (vendorRequestsList) vendorRequestsList.style.display = 'none';
            } else {
                if (vendorRequestsEmptyMessage) vendorRequestsEmptyMessage.style.display = 'none';
                if (vendorRequestsList) vendorRequestsList.style.display = 'block';

                data.notifications.forEach(notification => {
                    const li = document.createElement('li');
                    li.className = 'nav-notification__single d-flex flex-wrap';
                    li.innerHTML = `
                        <div class="nav-notification__type nav-notification__type--${notification.color}">
                            <i class="${notification.icon}"></i>
                        </div>
                        <div class="nav-notification__details">
                            <p>
                                <a href="${notification.url}" class="subject stretched-link text-truncate vendor-requests-link" data-id="${notification.id}" style="max-width: 180px;">${notification.title}</a>
                                <span>${notification.description}</span>
                            </p>
                            <p>
                                <span class="time-posted">${notification.created_at}</span>
                            </p>
                        </div>
                    `;
                    if (vendorRequestsList) vendorRequestsList.appendChild(li);
                });
            }

            vendorRequestsHasMorePages = data.has_more;
            vendorRequestsCurrentPage = data.current_page;
            
            if (vendorRequestsLoadMoreContainer && vendorRequestsHasMorePages && data.notifications.length > 0) {
                vendorRequestsLoadMoreContainer.style.display = 'block';
            }

            attachVendorRequestsClickHandlers();
            vendorRequestsIsLoading = false;
            if (vendorRequestsLoadingIndicator) vendorRequestsLoadingIndicator.style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading vendor requests:', error);
            vendorRequestsIsLoading = false;
            if (vendorRequestsLoadingIndicator) vendorRequestsLoadingIndicator.style.display = 'none';
        });
    }

    function attachVendorRequestsClickHandlers() {
        document.querySelectorAll('.vendor-requests-link').forEach(link => {
            const newLink = link.cloneNode(true);
            if (link.parentNode) link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function() {
                const currentCount = parseInt(vendorRequestsCounterBadge ? vendorRequestsCounterBadge.textContent : '0') || 0;
                if (currentCount > 0) {
                    const newCount = currentCount - 1;
                    if (vendorRequestsCounterBadge) {
                        vendorRequestsCounterBadge.textContent = newCount;
                        vendorRequestsCounterBadge.style.display = newCount > 0 ? 'flex' : 'none';
                    }
                    if (vendorRequestsBadgeCount) vendorRequestsBadgeCount.textContent = newCount;
                }

                const listItem = this.closest('li');
                if (listItem) listItem.remove();

                if (vendorRequestsList && vendorRequestsList.children.length === 0) {
                    if (vendorRequestsEmptyMessage) vendorRequestsEmptyMessage.style.display = 'block';
                    if (vendorRequestsList) vendorRequestsList.style.display = 'none';
                    if (vendorRequestsLoadMoreContainer) vendorRequestsLoadMoreContainer.style.display = 'none';
                }
            });
        });
    }

    if (vendorRequestsLoadMoreBtn) {
        vendorRequestsLoadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadVendorRequests(vendorRequestsCurrentPage + 1);
        });
    }

    setTimeout(function() {
        loadVendorRequests(1);
    }, 500);
})();
</script>
