<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'modalId' => 'modal-delete',
    'title' => '',
    'message' => '',
    'itemNameId' => 'delete-item-name',
    'confirmBtnId' => 'confirmDeleteBtn',
    'deleteRoute' => '',
    'cancelText' => '',
    'deleteText' => ''
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
    'modalId' => 'modal-delete',
    'title' => '',
    'message' => '',
    'itemNameId' => 'delete-item-name',
    'confirmBtnId' => 'confirmDeleteBtn',
    'deleteRoute' => '',
    'cancelText' => '',
    'deleteText' => ''
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!-- Delete Confirmation Modal -->
<div class="modal-info-delete modal fade" id="<?php echo e($modalId); ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-info" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-info-body d-flex">
                    <div class="modal-info-icon warning">
                        <img src="<?php echo e(asset('assets/img/svg/alert-circle.svg')); ?>" alt="alert-circle" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <h6><?php echo e($title); ?></h6>
                        <p id="<?php echo e($itemNameId); ?>" class="fw-500"></p>
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

<?php if (! $__env->hasRenderedOnce('fdafea67-3055-4751-9e2d-f99a3d022b77')): $__env->markAsRenderedOnce('fdafea67-3055-4751-9e2d-f99a3d022b77'); ?>
    <?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalId = '<?php echo e($modalId); ?>';
            const deleteModal = document.getElementById(modalId);
            const confirmDeleteBtn = document.getElementById('<?php echo e($confirmBtnId); ?>');
            const itemNameElement = document.getElementById('<?php echo e($itemNameId); ?>');
            let currentItemId = null;
            let deleteRouteBase = '<?php echo e($deleteRoute); ?>';

            if (deleteModal && confirmDeleteBtn) {
                // When modal is triggered, store item data
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    currentItemId = button.getAttribute('data-item-id') || button.getAttribute('data-role-id');
                    const itemName = button.getAttribute('data-item-name') || button.getAttribute('data-role-name');
                    
                    // Update modal content
                    if (itemNameElement && itemName) {
                        itemNameElement.textContent = itemName;
                    }
                });

                // Handle delete confirmation
                confirmDeleteBtn.addEventListener('click', function() {
                    if (currentItemId && deleteRouteBase) {
                        // Close the modal first
                        const modalInstance = bootstrap.Modal.getInstance(deleteModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        
                        // Show loading overlay with progress animation
                        if (window.LoadingOverlay) {
                            window.LoadingOverlay.show();
                            // Animate progress bar
                            window.LoadingOverlay.progressSequence([30, 60, 90], [200, 300, 400]);
                        }
                        
                        // Make AJAX request
                        fetch(`${deleteRouteBase}/${currentItemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Complete progress bar
                                if (window.LoadingOverlay) {
                                    window.LoadingOverlay.animateProgressBar(100, 200).then(() => {
                                        // Show success state
                                        window.LoadingOverlay.showSuccess(
                                            data.message || 'Deleted successfully!',
                                            'Redirecting...'
                                        );
                                        
                                        // Redirect after showing success
                                        setTimeout(() => {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                window.location.reload();
                                            }
                                        }, 1000);
                                    });
                                } else {
                                    // Fallback if no LoadingOverlay
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        window.location.reload();
                                    }
                                }
                            } else {
                                // Hide loading overlay on error
                                if (window.LoadingOverlay) {
                                    window.LoadingOverlay.hide();
                                }
                                
                                // Show error message
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message || 'An error occurred while deleting.'
                                    });
                                } else {
                                    alert(data.message || 'An error occurred while deleting.');
                                }
                            }
                        })
                        .catch(error => {
                            // Hide loading overlay
                            if (window.LoadingOverlay) {
                                window.LoadingOverlay.hide();
                            }
                            
                            console.error('Delete error:', error);
                            
                            // Show error message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while deleting.'
                                });
                            } else {
                                alert('An error occurred while deleting.');
                            }
                        });
                    }
                });
            }
        });
    </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/delete-modal.blade.php ENDPATH**/ ?>