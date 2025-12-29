<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['orderId' => null, 'currentStageId' => null, 'orderStages' => []]));

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

foreach (array_filter((['orderId' => null, 'currentStageId' => null, 'orderStages' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="modal fade" id="changeStageModal" tabindex="-1" aria-labelledby="changeStageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStageModalLabel"><?php echo e(trans('order::order.change_order_stage')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changeStageForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" id="orderId" name="order_id" value="<?php echo e($orderId); ?>">
                    <input type="hidden" id="currentStageId" value="<?php echo e($currentStageId); ?>">
                    <input type="hidden" id="currentStageType" value="">
                    <div class="form-group">
                        <label for="newStage" class="form-label"><?php echo e(trans('order::order.select_new_stage')); ?></label>
                        <select id="newStage" name="stage_id" class="form-select" required>
                            <option value=""><?php echo e(trans('order::order.select_stage')); ?></option>
                        </select>
                        <div id="stageWarning" class="text-warning mt-2" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?php echo e(__('common.cancel')); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo e(trans('order::order.update_stage')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            let orderStages = <?php echo json_encode($orderStages, 15, 512) ?>;

            // Stage step mapping (must match OrderStage::STAGE_STEPS in PHP)
            const STAGE_STEPS = {
                'new': 1,
                'in_progress': 2,
                'deliver': 3,
                'cancel': 3,
                'refund': 4
            };

            // Final stages that cannot transition
            const FINAL_STAGES = ['deliver', 'cancel'];

            // Get step for a stage type (default to 0 if type is null/undefined)
            function getStageStep(type) {
                if (!type) return 0;
                return STAGE_STEPS[type] || 0;
            }

            // Check if stage is final
            function isFinalStage(type) {
                if (!type) return false;
                return FINAL_STAGES.includes(type);
            }

            // Check if transition is allowed
            // Rules:
            // - Cannot change from final stages
            // - Cannot go backwards
            // - Cannot skip steps (must go to next step only)
            // - Exception: can cancel from any stage
            function canTransitionTo(currentType, newType, currentId, newId) {
                // Cannot transition to same stage
                if (currentId == newId) {
                    return false;
                }

                // Cannot change from final stages
                if (isFinalStage(currentType)) {
                    return false;
                }

                // If current type is null/undefined, allow only to step 1 or 2
                if (!currentType) {
                    const newStep = getStageStep(newType);
                    return newStep <= 2; // Can go to new or in_progress
                }

                // If new type is null/undefined, allow transition
                if (!newType) {
                    return true;
                }

                const currentStep = getStageStep(currentType);
                const newStep = getStageStep(newType);

                // Cannot go backwards
                if (newStep < currentStep) {
                    return false;
                }

                // Can always cancel from any non-final stage
                if (newType === 'cancel') {
                    return true;
                }

                // Cannot skip steps - must go to next step only
                if (newStep > currentStep + 1) {
                    return false;
                }

                // Refund only after deliver
                if (newType === 'refund' && currentType !== 'deliver') {
                    return false;
                }

                return true;
            }

            // Populate stage select dropdown with step-based restrictions
            function populateStageSelect() {
                const newStageSelect = $('#newStage');
                const currentStageId = $('#currentStageId').val();
                const currentStageType = $('#currentStageType').val();
                const stageWarning = $('#stageWarning');
                
                newStageSelect.find('option:not(:first)').remove();
                stageWarning.hide();

                // Find current stage
                const currentStage = orderStages.find(s => s.id == currentStageId);
                const currentType = currentStageType || (currentStage ? currentStage.type : null);

                // Check if current stage is final
                if (isFinalStage(currentType)) {
                    stageWarning.text('<?php echo e(trans("order::order.cannot_change_final_stage")); ?>').show();
                    return;
                }

                let hasOptions = false;
                orderStages.forEach(stage => {
                    const stageName = stage.name;
                    const stageType = stage.type;
                    
                    // Check if transition is allowed
                    if (canTransitionTo(currentType, stageType, currentStageId, stage.id)) {
                        newStageSelect.append(
                            `<option value="${stage.id}">${stageName}</option>`
                        );
                        hasOptions = true;
                    }
                });

                if (!hasOptions) {
                    stageWarning.text('<?php echo e(trans("order::order.no_available_stages")); ?>').show();
                }
            }

            // Handle form submission
            $('#changeStageForm').on('submit', function(e) {
                e.preventDefault();
                const stageId = $('#newStage').val();
                const orderId = $('#orderId').val();
                const selectedStage = orderStages.find(s => s.id == stageId);

                if (!stageId) {
                    toastr.warning('<?php echo e(trans('order::order.select_stage')); ?>');
                    return;
                }

                if (!orderId) {
                    toastr.error('<?php echo e(trans('order::order.order_id_required')); ?>');
                    return;
                }

                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.html();
                $submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo e(trans('common.updating')); ?>...'
                );

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '<?php echo e(trans('order::order.updating_stage')); ?>',
                        subtext: '<?php echo e(__('common.please_wait')); ?>...'
                    });
                }

                $.ajax({
                    url: '<?php echo e(route('admin.orders.change-stage', ['id' => '__id__'])); ?>'.replace(
                        '__id__', orderId),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        stage_id: stageId
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'changeStageModal'));
                        if (modal) modal.hide();

                        // Show success message
                        toastr.success(response.message ||
                            '<?php echo e(trans('order::order.stage_updated_successfully')); ?>');

                        // If changed to in-progress, redirect to fulfillment/allocate page
                        if (selectedStage && (selectedStage.slug === 'in-progress' || selectedStage.type === 'in_progress')) {
                            setTimeout(() => {
                                window.location.href = "<?php echo e(route('admin.order-fulfillments.show', ['orderId' => '__id__'])); ?>".replace('__id__', orderId);
                            }, 500);
                            return;
                        }

                        // Reload table immediately if exists, otherwise reload page after delay
                        if (typeof table !== 'undefined' && table.ajax) {
                            table.ajax.reload(null, false);
                        } else {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        let errorMessage = '<?php echo e(trans('order::order.error_updating_stage')); ?>';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Load stages and set data when modal is shown
            $('#changeStageModal').on('show.bs.modal', function(e) {
                const button = $(e.relatedTarget);
                if (button.length && button.data('id')) {
                    const orderId = button.data('id');
                    const stageId = button.data('stage-id');
                    const stageType = button.data('stage-type');
                    $('#orderId').val(orderId);
                    $('#currentStageId').val(stageId);
                    $('#currentStageType').val(stageType);
                }
            });

            $('#changeStageModal').on('shown.bs.modal', function() {
                populateStageSelect();
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Order\resources/views/components/change-stage-modal.blade.php ENDPATH**/ ?>