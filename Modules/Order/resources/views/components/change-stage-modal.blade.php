@props(['orderId' => null, 'currentStageId' => null, 'orderStages' => []])

<div class="modal fade" id="changeStageModal" tabindex="-1" aria-labelledby="changeStageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStageModalLabel">{{ trans('order::order.change_order_stage') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changeStageForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="orderId" name="order_id" value="{{ $orderId }}">
                    <input type="hidden" id="currentStageId" value="{{ $currentStageId }}">
                    <div class="form-group">
                        <label for="newStage" class="form-label">{{ trans('order::order.select_new_stage') }}</label>
                        <select id="newStage" name="stage_id" class="form-select" required>
                            <option value="">{{ trans('order::order.select_stage') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('order::order.update_stage') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let orderStages = @json($orderStages);

        // Populate stage select dropdown
        function populateStageSelect() {
            const newStageSelect = $('#newStage');
            const currentStageId = $('#currentStageId').val();
            newStageSelect.find('option:not(:first)').remove();

            orderStages.forEach(stage => {
                const stageName = stage.name;
                const isSelected = currentStageId && stage.id == currentStageId ? 'selected' : '';
                newStageSelect.append(`<option value="${stage.id}" ${isSelected}>${stageName}</option>`);
            });

            // Set the current stage as selected if available
            if (currentStageId) {
                newStageSelect.val(currentStageId);
            }
        }

        // Handle form submission
        $('#changeStageForm').on('submit', function(e) {
            e.preventDefault();
            const stageId = $('#newStage').val();
            const orderId = $('#orderId').val();
            const selectedStage = orderStages.find(s => s.id == stageId);

            if (!stageId) {
                toastr.warning('{{ trans('order.select_stage') }}');
                return;
            }

            // Check if selected stage requires fulfillment (in-progress)
            if (selectedStage && selectedStage.slug === 'in-progress') {
                // Redirect to fulfillment page instead of changing stage directly
                window.location.href = "{{ route('admin.order-fulfillments.show', '__id__') }}".replace('__id__', orderId);
                return;
            }

            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ trans('common.updating') }}...');

            // Show loading overlay
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.show({
                    text: '{{ trans('order::order.updating_stage') }}',
                    subtext: '{{ __('common.please_wait') }}...'
                });
            }

            $.ajax({
                url: '{{ route("admin.orders.change-stage", "__id__") }}'.replace('__id__', orderId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    stage_id: stageId
                },
                success: function(response) {
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('changeStageModal'));
                    if (modal) modal.hide();

                    // Show success message
                    toastr.success(response.message || '{{ trans('order::order.stage_updated_successfully') }}');

                    // Reload page or table based on context
                    setTimeout(() => {
                        // Check if we're on the index page with DataTable
                        if (typeof table !== 'undefined' && table.ajax) {
                            table.ajax.reload();
                        } else {
                            // Otherwise reload the page (for show page)
                            location.reload();
                        }
                    }, 1500);
                },
                error: function(xhr) {
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }

                    let errorMessage = '{{ trans('order::order.error_updating_stage') }}';
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

        // Load stages when modal is shown
        $('#changeStageModal').on('shown.bs.modal', function() {
            populateStageSelect();
        });
    });
</script>
@endpush
