@extends('layout.app')

@section('title', __('order::request-quotation.select_vendors'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('menu.vendors.request_quotations.title'),
                        'url' => route('admin.request-quotations.index'),
                    ],
                    ['title' => __('order::request-quotation.select_vendors')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-users-alt me-2"></i>
                            {{ __('order::request-quotation.select_vendors') }}
                        </h4>
                    </div>

                    <!-- Quotation Info -->
                    <div class="quotation-info">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>{{ __('order::request-quotation.quotation_number') }}:</strong> {{ $quotation->quotation_number }}</p>
                                <p class="mb-2"><strong>{{ __('order::request-quotation.customer_name') }}:</strong> {{ $quotation->customer_name }}</p>
                                <p class="mb-2"><strong>{{ __('common.email') }}:</strong> {{ $quotation->customer_email }}</p>
                                <p class="mb-0"><strong>{{ __('common.phone') }}:</strong> {{ $quotation->customer_phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>{{ __('common.address') }}:</strong> {{ $quotation->full_address }}</p>
                                @if($quotation->notes)
                                    <p class="mb-0"><strong>{{ __('common.notes') }}:</strong> {{ $quotation->notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form id="selectVendorsForm">
                        @csrf
                        
                        <div class="mb-4">
                            @if($vendors->isEmpty())
                                <div class="alert alert-warning">
                                    <i class="uil uil-exclamation-triangle"></i>
                                    {{ __('order::request-quotation.no_vendors_available') }}
                                </div>
                            @else
                                @php
                                    $vendorOptions = $vendors->map(function($vendor) {
                                        return [
                                            'id' => $vendor->id,
                                            'name' => $vendor->name,
                                        ];
                                    })->toArray();
                                @endphp

                                <x-multi-select 
                                    name="vendor_ids[]"
                                    id="vendor_ids"
                                    :label="__('order::request-quotation.select_vendors_to_send')"
                                    icon="uil uil-users-alt"
                                    :options="$vendorOptions"
                                    :selected="[]"
                                    :placeholder="__('order::request-quotation.select_vendors_placeholder')"
                                    :required="true"
                                />
                                <small class="text-muted d-block mt-2">
                                    <i class="uil uil-info-circle"></i>
                                    {{ __('order::request-quotation.select_multiple_vendors_hint') }}
                                </small>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.request-quotations.index') }}" class="btn btn-light">
                                <i class="uil uil-arrow-left"></i> {{ __('common.back') }}
                            </a>
                            
                            @if(!$vendors->isEmpty())
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="uil uil-message"></i> {{ __('order::request-quotation.send_to_vendors') }}
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .quotation-info {
            background: #f8f9ff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Form submission
            $('#selectVendorsForm').submit(function(e) {
                e.preventDefault();

                const selectedVendors = MultiSelect.getValues('vendor_ids');
                
                if (!selectedVendors || selectedVendors.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __('common.warning') }}',
                        text: '{{ __('order::request-quotation.please_select_vendors') }}',
                    });
                    return;
                }

                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).html('<i class="uil uil-spinner-alt rotating"></i> {{ __('common.sending') }}...');

                $.ajax({
                    url: '{{ route('admin.request-quotations.send-to-vendors', ['lang' => app()->getLocale(), 'countryCode' => $quotation->country->code ?? 'eg', 'id' => $quotation->id]) }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('common.success') }}',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route('admin.request-quotations.index') }}';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('common.error') }}',
                                text: response.message,
                            });
                            submitBtn.prop('disabled', false).html('<i class="uil uil-message"></i> {{ __('order::request-quotation.send_to_vendors') }}');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ __('common.error_occurred') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('common.error') }}',
                            text: errorMessage,
                        });
                        submitBtn.prop('disabled', false).html('<i class="uil uil-message"></i> {{ __('order::request-quotation.send_to_vendors') }}');
                    }
                });
            });
        });
    </script>
@endpush
