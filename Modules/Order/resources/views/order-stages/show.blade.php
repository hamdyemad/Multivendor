@extends('layout.app')

@section('title', trans('order::order_stage.order_stage_details'))

@push('styles')
<style>
/* Custom styling for order stage show view */
.card-holder {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    border-bottom: 1px solid #e3e6f0;
    padding: 1rem 1.25rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
}

.view-item {
    margin-bottom: 1rem;
}

.view-item label {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.box-items-translations {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    border: 1px solid #e3e6f0;
}

.badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.badge-success {
    background-color: #1cc88a;
}

.badge-secondary {
    background-color: #858796;
}

/* Color display styling */
.color-display-wrapper {
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.color-display-wrapper:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
</style>
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('order::order_stage.order_stages_management'), 'url' => route('admin.order-stages.index')],
                    ['title' => trans('order::order_stage.order_stage_details')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('order::order_stage.order_stage_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.order-stages.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ __('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.order-stages.edit', $orderStage->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ __('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Basic Information --}}
                            <div class="col-md-8 order-2 order-md-1">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('order::order_stage.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <x-translation-display :label="trans('order::order_stage.name')" :model="$orderStage" fieldName="name" :languages="$languages" />

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('order::order_stage.status') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if($orderStage->active)
                                                            <span class="badge badge-success badge-lg badge-round">{{ trans('order::order_stage.active') }}</span>
                                                        @else
                                                            <span class="badge badge-secondary badge-lg badge-round">{{ trans('order::order_stage.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Slug --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('main.slug') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <code>{{ $orderStage->slug ?? '-' }}</code>
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Sort Order --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('order::order_stage.sort_order') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $orderStage->sort_order }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- System Stage --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('order::order_stage.system_stage') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        @if($orderStage->is_system)
                                                            <span class="badge badge-warning badge-lg badge-round">
                                                                <i class="uil uil-shield-check me-1"></i>
                                                                {{ trans('common.yes') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-light badge-lg badge-round">
                                                                {{ trans('common.no') }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Created Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('main.created_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $orderStage->created_at }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Updated Date --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ __('main.updated_at') }}</label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        {{ $orderStage->updated_at }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Order Stage Color --}}
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-palette me-1"></i>{{ trans('order::order_stage.color') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="color-display-wrapper" onclick="copyColorToClipboard('{{ $orderStage->color }}')" title="{{ __('main.click_to_copy') }}">
                                            <div class="d-flex flex-column align-items-center justify-content-center" style="height: 200px;">
                                                <div class="mb-3" style="width: 80px; height: 80px; background-color: {{ $orderStage->color }}; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.2);"></div>
                                                <h5 class="mb-2 fw-600" id="colorCode">{{ $orderStage->color }}</h5>
                                                <small class="text-muted">{{ trans('order::order_stage.color') }} - <span class="text-primary">{{ __('main.click_to_copy') }}</span></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Component -->
    <x-delete-with-loading
        modalId="deleteOrderStageModal"
        tableId=""
        deleteButtonClass="delete-order-stage-btn"
        :title="trans('order::order_stage.delete_order_stage')"
        :message="trans('order::order_stage.delete_confirmation')"
        itemNameId="orderStageName"
        confirmBtnId="confirmDeleteOrderStage"
        :loadingText="trans('main.deleting')"
        :successMessage="trans('order::order_stage.order_stage_deleted')"
        :errorMessage="trans('order::order_stage.error_deleting_order_stage')"
    />

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Delete handler
            $(document).on('click', '.delete-order-stage-btn', function() {
                const orderStageId = $(this).data('id');
                const orderStageName = $(this).data('name');
                const deleteUrl = '{{ route('admin.order-stages.destroy', ':id') }}'.replace(':id', orderStageId);

                $('#orderStageName').text(orderStageName);
                $('#confirmDeleteOrderStage').data('url', deleteUrl);
            });

            // Handle successful deletion
            $(document).on('click', '#confirmDeleteOrderStage', function() {
                const deleteUrl = $(this).data('url');

                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#deleteOrderStageModal').modal('hide');
                            window.location.href = '{{ route('admin.order-stages.index') }}';
                        }
                    }
                });
            });
        });

        /**
         * Copy color code to clipboard
         */
        function copyColorToClipboard(colorCode) {
            // Create a temporary textarea element
            const tempTextArea = document.createElement('textarea');
            tempTextArea.value = colorCode;
            document.body.appendChild(tempTextArea);

            // Select and copy the text
            tempTextArea.select();
            tempTextArea.setSelectionRange(0, 99999); // For mobile devices

            try {
                document.execCommand('copy');

                // Show success feedback
                showCopyFeedback(true, colorCode);
            } catch (err) {
                // Fallback for modern browsers
                navigator.clipboard.writeText(colorCode).then(function() {
                    showCopyFeedback(true, colorCode);
                }).catch(function() {
                    showCopyFeedback(false, colorCode);
                });
            }

            // Remove the temporary element
            document.body.removeChild(tempTextArea);
        }

        /**
         * Show copy feedback to user
         */
        function showCopyFeedback(success, colorCode) {
            const colorElement = document.getElementById('colorCode');
            const originalText = colorElement.textContent;

            if (success) {
                colorElement.textContent = '{{ __('main.copied') }}';
                colorElement.style.color = '#28a745';

                // Show toast notification if available
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('main.copied') }}',
                        text: '{{ __('main.color_code_copied', ['color' => '']) }}'.replace(':color', colorCode),
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }

                // Reset after 2 seconds
                setTimeout(function() {
                    colorElement.textContent = originalText;
                    colorElement.style.color = '';
                }, 2000);
            } else {
                colorElement.textContent = '{{ __('main.copy_failed') }}';
                colorElement.style.color = '#dc3545';

                setTimeout(function() {
                    colorElement.textContent = originalText;
                    colorElement.style.color = '';
                }, 2000);
            }
        }
    </script>
    @endpush
@endsection
