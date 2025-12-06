@props(['orderId' => null, 'currentStageId' => null])

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
        let orderStages = [];

        // Fetch order stages for dropdown
        function loadOrderStages() {
            const orderId = $('#orderId').val();
            
            // Use order-specific endpoint if order ID is available
            const url = orderId ? `/api/orders/${orderId}/allowed-stages` : '/api/order-stages';
            
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data) {
                        orderStages = response.data;
                        populateStageSelect();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading order stages:', xhr);
                    toastr.error('{{ trans('common.error') }}');
                }
            });
        }

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
                window.location.href = '{{ url('admin/order-fulfillments') }}/' + orderId + '/allocate';
                return;
            }

            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ trans('common.updating') }}...');

            $.ajax({
                url: '{{ url('admin/orders') }}/' + orderId + '/change-stage',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    stage_id: stageId
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.error(response.message || '{{ trans('common.error_occurred') }}');
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || '{{ trans('common.error_occurred') }}';
                    toastr.error(errorMessage);
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Load stages when modal is shown
        $('#changeStageModal').on('shown.bs.modal', function() {
            if (orderStages.length === 0) {
                loadOrderStages();
            }
        });
    });
</script>
@endpush
