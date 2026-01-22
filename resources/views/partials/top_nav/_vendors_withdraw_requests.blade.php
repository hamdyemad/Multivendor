@php
    // Determine notification type based on user role
    if (isVendor()) {
        $withdrawType = 'withdraw_status';
        // Safely get vendor ID
        try {
            $vendorId = auth()->user()->vendor?->id;
        } catch (\Exception $e) {
            $vendorId = null;
        }
    } else {
        $withdrawType = 'withdraw_request';
        $vendorId = null;
    }
    
    // Get unread withdraw notifications count
    $withdrawCountQuery = \App\Models\AdminNotification::notViewedBy(auth()->id())
        ->where('type', $withdrawType);
    
    if ($vendorId) {
        $withdrawCountQuery->where('vendor_id', $vendorId);
    } else {
        $withdrawCountQuery->whereNull('vendor_id');
    }
    
    $withdrawCount = $withdrawCountQuery->count();
@endphp

<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
            <span class="withdraw-badge-custom" style="position: absolute !important; top: -8px !important; right: -8px !important; background: #fa8b0c !important; color: #ffffff !important; border-radius: 50% !important; min-width: 16px !important; height: 16px !important; display: {{ $withdrawCount > 0 ? 'flex' : 'none' }} !important; align-items: center !important; justify-content: center !important; font-size: 10px !important; font-weight: 600 !important; line-height: 16px !important; z-index: 9999 !important; padding: 0 4px !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; border: 1.5px solid #fff !important;" dir="ltr">{{ $withdrawCount }}</span>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">
                {{ trans('menu.withdraw module.vendors_withdraw_requests') }} 
                <span class="badge-circle badge-warning ms-1 withdraw-badge-count">{{ $withdrawCount }}</span>
            </h2>
            <div class="withdraw-list-container" id="withdraw-scroll-container" style="max-height: 400px; overflow-y: scroll; overflow-x: hidden; -webkit-overflow-scrolling: touch;">
                <ul id="withdraw-list" style="list-style: none; padding: 0; margin: 0;">
                    <!-- Withdraw requests will be loaded via AJAX -->
                </ul>
                <div id="withdraw-loading" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="withdraw-empty" class="text-center py-4" style="display: none;">
                    <p class="text-muted">{{ trans('menu.withdraw module.no_requests') }}</p>
                </div>
                <div id="withdraw-load-more-container" class="text-center py-3 border-top" style="display: none;">
                    <button id="withdraw-load-more-btn" class="btn btn-sm btn-light-primary" type="button" style="width: 90%; border-radius: 6px; font-weight: 500; padding: 8px 16px;">
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
    
    let withdrawCurrentPage = 1;
    let withdrawIsLoading = false;
    let withdrawHasMorePages = true;
    const withdrawType = '{{ $withdrawType }}';

    const withdrawList = document.getElementById('withdraw-list');
    const withdrawLoadingIndicator = document.getElementById('withdraw-loading');
    const withdrawEmptyMessage = document.getElementById('withdraw-empty');
    const withdrawLoadMoreContainer = document.getElementById('withdraw-load-more-container');
    const withdrawLoadMoreBtn = document.getElementById('withdraw-load-more-btn');
    const withdrawCounterBadge = document.querySelector('.withdraw-badge-custom');
    const withdrawBadgeCount = document.querySelector('.withdraw-badge-count');

    function loadWithdrawRequests(page) {
        if (withdrawIsLoading || (!withdrawHasMorePages && page > 1)) return;

        withdrawIsLoading = true;
        if (withdrawLoadingIndicator) withdrawLoadingIndicator.style.display = 'block';
        if (withdrawLoadMoreContainer) withdrawLoadMoreContainer.style.display = 'none';

        const url = '{{ route("admin.notifications.index", ["lang" => app()->getLocale(), "countryCode" => strtolower(session("country_code", "eg"))]) }}?page=' + page + '&type=' + withdrawType;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (page === 1 && withdrawList) withdrawList.innerHTML = '';

            if (data.notifications.length === 0 && page === 1) {
                if (withdrawEmptyMessage) withdrawEmptyMessage.style.display = 'block';
                if (withdrawList) withdrawList.style.display = 'none';
            } else {
                if (withdrawEmptyMessage) withdrawEmptyMessage.style.display = 'none';
                if (withdrawList) withdrawList.style.display = 'block';

                data.notifications.forEach(notification => {
                    const li = document.createElement('li');
                    li.className = 'nav-notification__single d-flex flex-wrap';
                    li.innerHTML = `
                        <div class="nav-notification__type nav-notification__type--${notification.color}">
                            <i class="${notification.icon}"></i>
                        </div>
                        <div class="nav-notification__details">
                            <p>
                                <a href="${notification.url}" class="subject stretched-link text-truncate withdraw-link" data-id="${notification.id}" style="max-width: 180px;">${notification.title}</a>
                            </p>
                            <p>
                                <span class="time-posted">${notification.description}</span>
                            </p>
                            <p>
                                <span class="time-posted text-muted">${notification.created_at}</span>
                            </p>
                        </div>
                    `;
                    if (withdrawList) withdrawList.appendChild(li);
                });
            }

            withdrawHasMorePages = data.has_more;
            withdrawCurrentPage = data.current_page;
            
            if (withdrawLoadMoreContainer && withdrawHasMorePages && data.notifications.length > 0) {
                withdrawLoadMoreContainer.style.display = 'block';
            }

            attachWithdrawClickHandlers();
            withdrawIsLoading = false;
            if (withdrawLoadingIndicator) withdrawLoadingIndicator.style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading withdraw requests:', error);
            withdrawIsLoading = false;
            if (withdrawLoadingIndicator) withdrawLoadingIndicator.style.display = 'none';
        });
    }

    function attachWithdrawClickHandlers() {
        document.querySelectorAll('.withdraw-link').forEach(link => {
            const newLink = link.cloneNode(true);
            if (link.parentNode) link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function() {
                const currentCount = parseInt(withdrawCounterBadge ? withdrawCounterBadge.textContent : '0') || 0;
                if (currentCount > 0) {
                    const newCount = currentCount - 1;
                    if (withdrawCounterBadge) {
                        withdrawCounterBadge.textContent = newCount;
                        withdrawCounterBadge.style.display = newCount > 0 ? 'flex' : 'none';
                    }
                    if (withdrawBadgeCount) withdrawBadgeCount.textContent = newCount;
                }

                const listItem = this.closest('li');
                if (listItem) listItem.remove();

                if (withdrawList && withdrawList.children.length === 0) {
                    if (withdrawEmptyMessage) withdrawEmptyMessage.style.display = 'block';
                    if (withdrawList) withdrawList.style.display = 'none';
                    if (withdrawLoadMoreContainer) withdrawLoadMoreContainer.style.display = 'none';
                }
            });
        });
    }

    if (withdrawLoadMoreBtn) {
        withdrawLoadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadWithdrawRequests(withdrawCurrentPage + 1);
        });
    }

    setTimeout(function() {
        loadWithdrawRequests(1);
    }, 500);
})();
</script>
