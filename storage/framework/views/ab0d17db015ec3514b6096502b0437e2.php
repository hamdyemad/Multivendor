<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
<?php
    // Get unread messages count only
    $messagesCountQuery = \App\Models\AdminNotification::notViewedBy(auth()->id())
        ->where('type', 'new_message')
        ->whereNull('vendor_id');
    
    $unreadMessagesCount = $messagesCountQuery->count();
?>

<li class="nav-message">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <img class="svg" src="<?php echo e(asset('assets/img/svg/message.svg')); ?>" alt="img">
            <span class="messages-badge-custom" style="position: absolute !important; top: -8px !important; right: -8px !important; background: #20c997 !important; color: #ffffff !important; border-radius: 50% !important; min-width: 16px !important; height: 16px !important; display: <?php echo e($unreadMessagesCount > 0 ? 'flex' : 'none'); ?> !important; align-items: center !important; justify-content: center !important; font-size: 10px !important; font-weight: 600 !important; line-height: 16px !important; z-index: 9999 !important; padding: 0 4px !important; box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important; border: 1.5px solid #fff !important;"><?php echo e($unreadMessagesCount); ?></span>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">
                <?php echo e(trans('menu.messages')); ?> 
                <span class="badge-circle badge-success ms-1 messages-badge-count"><?php echo e($unreadMessagesCount); ?></span>
            </h2>
            <div class="messages-list-container" id="messages-scroll-container" style="max-height: 400px; overflow-y: scroll; overflow-x: hidden; -webkit-overflow-scrolling: touch;">
                <ul id="messages-list" style="list-style: none; padding: 0; margin: 0;">
                    <!-- Messages will be loaded via AJAX -->
                </ul>
                <div id="messages-loading" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="messages-empty" class="text-center py-4" style="display: none;">
                    <p class="text-muted"><?php echo e(trans('menu.no_messages')); ?></p>
                </div>
                <div id="messages-load-more-container" class="text-center py-3 border-top" style="display: none;">
                    <button id="messages-load-more-btn" class="btn btn-sm btn-light-primary" type="button" style="width: 90%; border-radius: 6px; font-weight: 500; padding: 8px 16px;">
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
    
    let messagesCurrentPage = 1;
    let messagesIsLoading = false;
    let messagesHasMorePages = true;

    const messagesList = document.getElementById('messages-list');
    const messagesLoadingIndicator = document.getElementById('messages-loading');
    const messagesEmptyMessage = document.getElementById('messages-empty');
    const messagesLoadMoreContainer = document.getElementById('messages-load-more-container');
    const messagesLoadMoreBtn = document.getElementById('messages-load-more-btn');
    const messagesCounterBadge = document.querySelector('.messages-badge-custom');
    const messagesBadgeCount = document.querySelector('.messages-badge-count');

    function loadMessages(page) {
        if (messagesIsLoading || (!messagesHasMorePages && page > 1)) return;

        messagesIsLoading = true;
        if (messagesLoadingIndicator) messagesLoadingIndicator.style.display = 'block';
        if (messagesLoadMoreContainer) messagesLoadMoreContainer.style.display = 'none';

        const url = '<?php echo e(route("admin.notifications.index", ["lang" => app()->getLocale(), "countryCode" => strtolower(session("country_code", "eg"))])); ?>?page=' + page + '&type=new_message';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (page === 1 && messagesList) messagesList.innerHTML = '';

            if (data.notifications.length === 0 && page === 1) {
                if (messagesEmptyMessage) messagesEmptyMessage.style.display = 'block';
                if (messagesList) messagesList.style.display = 'none';
            } else {
                if (messagesEmptyMessage) messagesEmptyMessage.style.display = 'none';
                if (messagesList) messagesList.style.display = 'block';

                data.notifications.forEach(notification => {
                    const li = document.createElement('li');
                    li.className = 'author-online has-new-message';
                    li.innerHTML = `
                        <div class="user-avater">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #5f63f2; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                                ${notification.title.substring(0, 1).toUpperCase()}
                            </div>
                        </div>
                        <div class="user-message">
                            <p>
                                <a href="${notification.url}" class="subject stretched-link text-truncate messages-link" data-id="${notification.id}" style="max-width: 180px;">${notification.title}</a>
                            </p>
                            <p>
                                <span class="desc text-truncate" style="max-width: 215px;">${notification.description}</span>
                            </p>
                            <p>
                                <span class="time-posted">${notification.created_at}</span>
                            </p>
                        </div>
                    `;
                    if (messagesList) messagesList.appendChild(li);
                });
            }

            messagesHasMorePages = data.has_more;
            messagesCurrentPage = data.current_page;
            
            if (messagesLoadMoreContainer && messagesHasMorePages && data.notifications.length > 0) {
                messagesLoadMoreContainer.style.display = 'block';
            }

            attachMessagesClickHandlers();
            messagesIsLoading = false;
            if (messagesLoadingIndicator) messagesLoadingIndicator.style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            messagesIsLoading = false;
            if (messagesLoadingIndicator) messagesLoadingIndicator.style.display = 'none';
        });
    }

    function attachMessagesClickHandlers() {
        document.querySelectorAll('.messages-link').forEach(link => {
            const newLink = link.cloneNode(true);
            if (link.parentNode) link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function() {
                const currentCount = parseInt(messagesCounterBadge ? messagesCounterBadge.textContent : '0') || 0;
                if (currentCount > 0) {
                    const newCount = currentCount - 1;
                    if (messagesCounterBadge) {
                        messagesCounterBadge.textContent = newCount;
                        messagesCounterBadge.style.display = newCount > 0 ? 'flex' : 'none';
                    }
                    if (messagesBadgeCount) messagesBadgeCount.textContent = newCount;
                }

                const listItem = this.closest('li');
                if (listItem) listItem.remove();

                if (messagesList && messagesList.children.length === 0) {
                    if (messagesEmptyMessage) messagesEmptyMessage.style.display = 'block';
                    if (messagesList) messagesList.style.display = 'none';
                    if (messagesLoadMoreContainer) messagesLoadMoreContainer.style.display = 'none';
                }
            });
        });
    }

    if (messagesLoadMoreBtn) {
        messagesLoadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loadMessages(messagesCurrentPage + 1);
        });
    }

    setTimeout(function() {
        loadMessages(1);
    }, 500);
})();
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_messages.blade.php ENDPATH**/ ?>