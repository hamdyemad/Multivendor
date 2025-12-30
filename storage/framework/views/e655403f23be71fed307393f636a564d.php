<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'modalId' => 'deleteModal',
    'tableId' => 'dataTable',
    'deleteButtonClass' => 'delete-btn',
    'title' => 'Confirm Delete',
    'message' => 'Are you sure you want to delete this item?',
    'itemNameId' => 'delete-item-name',
    'confirmBtnId' => 'confirmDeleteBtn',
    'cancelText' => 'Cancel',
    'deleteText' => 'Delete',
    'loadingDeleting' => 'Deleting...',
    'loadingPleaseWait' => 'Please wait...',
    'loadingDeletedSuccessfully' => 'Deleted Successfully!',
    'loadingRefreshing' => 'Refreshing...',
    'errorDeleting' => 'Error deleting item'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'modalId' => 'deleteModal',
    'tableId' => 'dataTable',
    'deleteButtonClass' => 'delete-btn',
    'title' => 'Confirm Delete',
    'message' => 'Are you sure you want to delete this item?',
    'itemNameId' => 'delete-item-name',
    'confirmBtnId' => 'confirmDeleteBtn',
    'cancelText' => 'Cancel',
    'deleteText' => 'Delete',
    'loadingDeleting' => 'Deleting...',
    'loadingPleaseWait' => 'Please wait...',
    'loadingDeletedSuccessfully' => 'Deleted Successfully!',
    'loadingRefreshing' => 'Refreshing...',
    'errorDeleting' => 'Error deleting item'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div class="modal fade" id="<?php echo e($modalId); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo e($modalId); ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-info" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="<?php echo e($modalId); ?>Label"><?php echo e($title); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-info-body d-flex">
                    <div class="modal-info-icon warning">
                        <img src="<?php echo e(asset('assets/img/svg/alert-circle.svg')); ?>" alt="alert-circle" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <p id="<?php echo e($itemNameId); ?>" class="fw-500"><?php echo e($title); ?></p>
                        <p class="text-muted fs-13"><?php echo e($message); ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> <?php echo e($cancelText); ?>

                </button>
                <button type="button" class="btn btn-danger btn-sm" id="<?php echo e($confirmBtnId); ?>">
                    <i class="uil uil-trash-alt"></i> <?php echo e($deleteText); ?>

                </button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    const modalId = '<?php echo e($modalId); ?>';
    const tableId = '<?php echo e($tableId); ?>';
    const deleteButtonClass = '<?php echo e($deleteButtonClass); ?>';
    const confirmBtnId = '<?php echo e($confirmBtnId); ?>';
    const itemNameId = '<?php echo e($itemNameId); ?>';
    
    // Handle delete button clicks
    $(document).on('click', '.' + deleteButtonClass, function() {
        const itemId = $(this).data('id');
        const itemName = $(this).data('name');
        const deleteUrl = $(this).data('url');
        
        $('#' + itemNameId).text(itemName);
        
        // Set up the delete confirmation
        $('#' + confirmBtnId).off('click').on('click', function() {
            const deleteModal = $('#' + modalId);
            
            // Hide delete modal and wait for it to fully close
            deleteModal.modal('hide');
            
            // Wait for modal to fully hide before showing loading overlay
            deleteModal.on('hidden.bs.modal', function(e) {
                // Remove the event listener to prevent multiple bindings
                $(this).off('hidden.bs.modal');
                
                // Update loading text dynamically
                const loadingText = '<?php echo e($loadingDeleting); ?>';
                const loadingSubtext = '<?php echo e($loadingPleaseWait); ?>';
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.querySelector('.loading-text').textContent = loadingText;
                    overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
                }
                
                // Show loading overlay using the LoadingOverlay object
                LoadingOverlay.show();
                
                // Start progress bar animation
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                    // Perform delete request
                    return $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        }
                    });
                })
                .then(response => {
                    // Progress to 60%
                    return LoadingOverlay.animateProgressBar(60, 200).then(() => response);
                })
                .then(response => {
                    // Progress to 90%
                    return LoadingOverlay.animateProgressBar(90, 200).then(() => response);
                })
                .then(response => {
                    // Complete progress bar
                    return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                        // Show success animation
                        LoadingOverlay.showSuccess(
                            '<?php echo e($loadingDeletedSuccessfully); ?>',
                            '<?php echo e($loadingRefreshing); ?>'
                        );
                        
                        // Reload table and hide overlay after delay
                        setTimeout(() => {
                            LoadingOverlay.hide();
                            
                            // Reload DataTable if exists
                            const table = $('#' + tableId).DataTable();
                            if (table) {
                                table.ajax.reload();
                            }
                            
                            // Trigger custom event for additional handling
                            $(document).trigger('itemDeleted', [response]);
                        }, 1500);
                    });
                })
                .catch(xhr => {
                    // Hide loading overlay
                    LoadingOverlay.hide();
                    alert('<?php echo e($errorDeleting); ?>');
                    console.error('Delete error:', xhr);
                });
            });
        });
        
        $('#' + modalId).modal('show');
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/delete-with-loading.blade.php ENDPATH**/ ?>