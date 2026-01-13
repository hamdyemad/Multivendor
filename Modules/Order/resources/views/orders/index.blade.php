@extends('layout.app')
@section('title')
    {{ trans('order::order.order_management') }} | Bnaia
@endsection
@push('styles')
    <style>
        /* Fix select heights to match inputs (38px) */
        .form-select.ih-medium,
        .form-control.ih-medium {
            height: 38px !important;
            min-height: 38px !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        
        /* Ensure multi-select matches other inputs */
        .multi-select-display {
            height: 38px !important;
            min-height: 38px !important;
        }
        
        .vendor-logos {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vendor-logo-wrapper {
            position: relative;
            display: inline-block;
            margin-left: -10px;
            /* Overlap amount */
        }

        .vendor-logo-wrapper:first-child {
            margin-left: 0;
        }

        .vendor-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #fff;
            background-color: #f0f0f0;
            transition: transform 0.2s ease-in-out;
        }

        .vendor-logo-wrapper:hover .vendor-logo {
            transform: translateY(-5px);
        }

        .vendor-logo-wrapper .vendor-name-tooltip {
            visibility: hidden;
            width: max-content;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            /* Position the tooltip above the logo */
            left: 50%;
            margin-left: -50%;
            /* Center the tooltip */
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            transform: translateY(10px);
        }

        .vendor-logo-wrapper:hover .vendor-name-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@endpush

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
                    ['title' => trans('order::order.order_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('order::order.order_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.orders.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('order::order.create_order') }}
                            </a>
                        </div>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    {{-- Search --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="{{ __('common.search') }}..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Stage --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="stage" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('order::order.stage') }}
                                            </label>
                                            <select class="form-control form-select ih-medium ip-gray radius-xs b-light" id="stage">
                                                <option value="">{{ trans('order::order.all_stages') }}</option>
                                                @foreach ($orderStages as $stage)
                                                    <option value="{{ $stage['id'] }}">{{ $stage['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Order Type --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="payment_type" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-credit-card me-1"></i>
                                                {{ trans('order::order.order_type') }}
                                            </label>
                                            <select class="form-control form-select ih-medium ip-gray radius-xs b-light" id="payment_type">
                                                <option value="">{{ trans('order::order.all_types') }}</option>
                                                <option value="online">{{ trans('order::order.online') }}</option>
                                                <option value="cash_on_delivery">{{ trans('order::order.cod') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Payment Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="payment_visa_status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-money-bill me-1"></i>
                                                {{ trans('order::order.payment_status') }}
                                            </label>
                                            <select class="form-control form-select ih-medium ip-gray radius-xs b-light" id="payment_visa_status">
                                                <option value="">{{ trans('order::order.all_payment_statuses') }}</option>
                                                <option value="success">{{ trans('order::order.paid') }}</option>
                                                <option value="pending">{{ trans('order::order.payment_pending') }}</option>
                                                <option value="unpaid">{{ trans('order::order.unpaid') }}</option>
                                                <option value="fail">{{ trans('order::order.payment_failed') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    @if(isAdmin())
                                        {{-- Vendor --}}
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-multi-select 
                                                    name="vendor[]" 
                                                    id="vendor"
                                                    :label="trans('order::order.vendor')"
                                                    icon="uil uil-store"
                                                    :options="$vendors->map(fn($v) => ['id' => $v->id, 'name' => $v->name])->toArray()"
                                                    :selected="[]"
                                                    :placeholder="trans('order::order.all_vendors')"
                                                />
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Created From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('order::order.created_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_from_filter">
                                        </div>
                                    </div>

                                    {{-- Created Until --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_until_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('order::order.created_until') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_until_filter">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-setting me-1"></i>
                                                {{ __('common.actions') }}
                                            </label>
                                            <div class="d-flex align-items-center">
                                                <button type="button" id="searchBtn"
                                                    class="btn btn-success btn-default btn-squared me-1"
                                                    title="{{ __('common.search') }}">
                                                    <i class="uil uil-search me-1"></i>
                                                    {{ __('common.search') }}
                                                </button>
                                                <button type="button" id="resetFilters"
                                                    class="btn btn-warning btn-default btn-squared me-1"
                                                    title="{{ __('common.reset') }}">
                                                    <i class="uil uil-redo me-1"></i>
                                                    {{ __('common.reset') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('common.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('common.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="ordersDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('order::order.order_information') }}</span>
                                    </th>
                                    @if(isAdmin())
                                    <th><span class="userDatatable-title">{{ trans('order::order.vendor') }}</span></th>
                                    @endif
                                    <th><span class="userDatatable-title">{{ trans('order::order.total_price') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ trans('order::order.stage') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('order::order.created_at') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Include Change Stage Modal Component --}}
    <x-order::change-stage-modal :order-id="null" :order-stages="$orderStages" />
    
    {{-- Vendor Quick Stage Change Confirmation Modal --}}
    @if(!isAdmin())
    <div class="modal fade" id="vendorQuickStageModal" tabindex="-1" aria-labelledby="vendorQuickStageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vendorQuickStageModalLabel">
                        <i class="uil uil-exclamation-triangle me-2"></i>{{ trans('common.confirm_action') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="quickStageOrderId">
                    <input type="hidden" id="quickStageTargetType">
                    <p id="quickStageMessage" class="mb-0 fs-16"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmQuickStageBtn">{{ __('common.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Vendor Change Stage Modal --}}
    <div class="modal fade" id="vendorChangeStageModal" tabindex="-1" aria-labelledby="vendorChangeStageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vendorChangeStageModalLabel">
                        <i class="uil uil-exchange me-2"></i>{{ trans('order::order.change_stage') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vendorStageOrderId">
                    <input type="hidden" id="vendorCurrentStageId">
                    <input type="hidden" id="vendorCurrentStageType">
                    <div class="form-group">
                        <label for="vendorNewStage" class="form-label fw-bold">{{ trans('order::order.select_new_stage') }}</label>
                        <select id="vendorNewStage" class="form-select" required>
                            <option value="">{{ trans('order::order.select_stage') }}</option>
                        </select>
                        <div id="vendorStageWarning" class="alert alert-warning mt-2" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmVendorStageBtn">{{ trans('order::order.update_stage') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Admin Change Stage Modal (for all vendors) --}}
    @if(isAdmin())
    <div class="modal fade" id="adminChangeStageModal" tabindex="-1" aria-labelledby="adminChangeStageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminChangeStageModalLabel">
                        <i class="uil uil-exchange me-2"></i>{{ trans('order::order.change_stage_all_vendors') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="adminStageOrderId">
                    <input type="hidden" id="adminCurrentStageType">
                    <input type="hidden" id="adminCurrentStageId">
                    
                    <div class="alert alert-info mb-3">
                        <i class="uil uil-info-circle me-2"></i>
                        {{ trans('order::order.admin_stage_change_warning') }}
                    </div>
                    <div class="form-group">
                        <label for="adminNewStage" class="form-label fw-bold">{{ trans('order::order.select_new_stage') }}</label>
                        <select id="adminNewStage" class="form-select" required>
                            <option value="">{{ trans('order::order.select_stage') }}</option>
                        </select>
                        <div id="adminStageWarning" class="alert alert-warning mt-2" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmAdminStageBtn">{{ trans('order::order.update_stage') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Admin Quick Stage Change Confirmation Modal --}}
    @if(isAdmin())
    <div class="modal fade" id="adminQuickStageModal" tabindex="-1" aria-labelledby="adminQuickStageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminQuickStageModalLabel">
                        <i class="uil uil-exclamation-triangle me-2"></i>{{ trans('common.confirm_action') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="adminQuickStageOrderId">
                    <input type="hidden" id="adminQuickStageTargetType">
                    <div class="alert alert-info mb-3">
                        <i class="uil uil-info-circle me-2"></i>
                        {{ trans('order::order.admin_stage_change_warning') }}
                    </div>
                    <p id="adminQuickStageMessage" class="mb-0 fs-16"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmAdminQuickStageBtn">{{ __('common.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;
            let table;

            // Initialize multi-select component
            if (document.getElementById('vendor')) {
                MultiSelect.init('vendor');
                
                // Listen for changes on the multi-select
                document.getElementById('vendor').addEventListener('change', function() {
                    table.ajax.reload();
                    updateUrlParams();
                });
            }

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('stage')) $('#stage').val(urlParams.get('stage'));
            if (urlParams.has('payment_type')) $('#payment_type').val(urlParams.get('payment_type'));
            if (urlParams.has('payment_visa_status')) $('#payment_visa_status').val(urlParams.get('payment_visa_status'));
            if (urlParams.has('vendor') && document.getElementById('vendor')) {
                const vendorValues = urlParams.get('vendor').split(',');
                MultiSelect.setValues('vendor', vendorValues);
            }
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));
            
            // Server-side processing with pagination
            const isVendorUser = {{ !isAdmin() ? 'true' : 'false' }};
            
            // Define columns based on user type
            let tableColumns = [
                {
                    data: 'index',
                    name: 'index',
                    orderable: false,
                    searchable: false,
                    className: 'text-center fw-bold'
                },
                {
                    data: null,
                    name: 'order_customer',
                    orderable: false,
                    searchable: true,
                    render: function(data, type, row) {
                        const orderNumber = data.order_number || '-';
                        const customerName = data.customer_name || '-';
                        const customerEmail = data.customer_email || '-';
                        const customerPhone = data.customer_phone || '-';
                        const hasQuotation = data.has_quotation || false;
                        const quotationNumber = data.quotation_number || '';
                        const quotationStatus = data.quotation_status || '';

                        // Status labels and colors
                        const statusLabels = {
                            'pending': '{{ __('order::request-quotation.status_pending') }}',
                            'sent_offer': '{{ __('order::request-quotation.status_sent_offer') }}',
                            'accepted_offer': '{{ __('order::request-quotation.status_accepted_offer') }}',
                            'rejected_offer': '{{ __('order::request-quotation.status_rejected_offer') }}',
                            'order_created': '{{ __('order::request-quotation.status_order_created') }}'
                        };
                        
                        const statusColors = {
                            'pending': '#ffc107',
                            'sent_offer': '#17a2b8',
                            'accepted_offer': '#28a745',
                            'rejected_offer': '#dc3545',
                            'order_created': '#007bff'
                        };

                        let html = `
                            <div class="customer-info">
                                <div class="fw-bold mb-1">
                                    <i class="uil uil-receipt me-1"></i><strong>${orderNumber}</strong>
                                </div>
                                <div class="small">
                                    <div class="mb-1">
                                        <i class="uil uil-user me-1"></i> <strong>${customerName}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <i class="uil uil-envelope me-1"></i> <a href="mailto:${customerEmail}">${customerEmail}</a>
                                    </div>
                                    <div class="mb-1">
                                        <i class="uil uil-phone me-1"></i> ${customerPhone}
                                    </div>`;
                        
                        if (hasQuotation) {
                            const statusLabel = statusLabels[quotationStatus] || quotationStatus;
                            const statusColor = statusColors[quotationStatus] || '#6c757d';
                            
                            html += `
                                    <div>
                                        <span class="badge badge-round" style="background-color: ${statusColor}; font-size: 10px;">
                                            <i class="uil uil-file-question-alt me-1"></i>{{ __('order::request-quotation.from_quotation') }}: ${quotationNumber}
                                        </span>
                                        <br>
                                        <span class="badge badge-round mt-1" style="background-color: ${statusColor}; font-size: 9px;">
                                            ${statusLabel}
                                        </span>
                                    </div>`;
                        }
                        
                        html += `
                                </div>
                            </div>
                        `;
                        
                        return html;
                    }
                }
            ];
            
            // Add vendor column only for admin
            if (!isVendorUser) {
                tableColumns.push({
                    data: 'vendor',
                    name: 'vendor',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (!data || data.length === 0) {
                            return '-';
                        }

                        let logosHtml = '<div class="vendor-logos">';
                        data.forEach(vendor => {
                            logosHtml += `
                                <div class="vendor-logo-wrapper">
                                    <img src="${vendor.logo_url}" alt="${vendor.name}" class="vendor-logo">
                                    <span class="vendor-name-tooltip">${vendor.name}</span>
                                </div>
                            `;
                        });
                        logosHtml += '</div>';

                        return logosHtml;
                    }
                });
            }
            
            // Add remaining columns
            tableColumns.push(
                {
                    data: 'total_price',
                    name: 'total_price',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (data !== null && data !== undefined && data !== '') {
                            return parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' {{ currency() }}';
                        }
                        return '-';
                    }
                },
                {
                    data: 'product_stages',
                    name: 'product_stages',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let html = '';
                        
                        // For admin: show vendors with their stages
                        if (!isVendorUser && row.vendors_with_stages && row.vendors_with_stages.length > 0) {
                            row.vendors_with_stages.forEach(function(vendor) {
                                const stageName = vendor.stage?.name || '-';
                                const stageColor = vendor.stage?.color || '#6c757d';
                                html += `<div class="mb-1 d-flex align-items-center">
                                    <img src="${vendor.logo_url}" alt="${vendor.name}" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 6px;">
                                    <span class="me-1" style="font-size: 12px; font-weight: 500;">${vendor.name}:</span>
                                    <span class="badge badge-round" style="background-color: ${stageColor}; color: white; font-size: 11px;">${stageName}</span>
                                </div>`;
                            });
                        } 
                        // For vendor: show their own stage
                        else if (isVendorUser && row.vendor_stage) {
                            const stageName = row.vendor_stage.name || '-';
                            const stageColor = row.vendor_stage.color || '#6c757d';
                            html += `<div class="mb-1"><span class="badge badge-round badge-lg" style="background-color: ${stageColor}; color: white;">${stageName}</span></div>`;
                        }
                        // Fallback to product stages
                        else if (data && data.length > 0) {
                            // Group stages by stage name
                            const stageGroups = {};
                            data.forEach(function(productStage) {
                                const stageName = productStage?.name || '-';
                                const stageColor = productStage?.color || '#6c757d';
                                const stageKey = stageName + '_' + stageColor;
                                
                                if (!stageGroups[stageKey]) {
                                    stageGroups[stageKey] = {
                                        name: stageName,
                                        color: stageColor,
                                        count: 0
                                    };
                                }
                                stageGroups[stageKey].count++;
                            });
                            
                            // Display each unique stage with count
                            Object.values(stageGroups).forEach(function(stage) {
                                html += `<div class="mb-1"><span class="badge badge-round badge-lg" style="background-color: ${stage.color}; color: white;">${stage.name} (${stage.count})</span></div>`;
                            });
                        } else {
                            html += `<div class="mb-1"><span class="badge badge-round badge-lg" style="background-color: #6c757d; color: white;">-</span></div>`;
                        }
                        
                        // Payment Type (Online / COD)
                        const paymentType = row.payment_type || 'cash_on_delivery';
                        const paymentTypeLabel = paymentType === 'online' ? '{{ trans("order::order.online") }}' : '{{ trans("order::order.cod") }}';
                        const paymentTypeColor = paymentType === 'online' ? '#17a2b8' : '#6c757d';
                        html += `<div class="mb-1"><span class="badge badge-round" style="background-color: ${paymentTypeColor}; color: white; font-size: 10px;">{{ trans("order::order.order_type") }}: ${paymentTypeLabel}</span></div>`;
                        
                        // Payment Status (only for online payments)
                        if (paymentType === 'online' && row.payment_visa_status) {
                            let paymentStatusLabel = '';
                            let paymentStatusColor = '';
                            switch(row.payment_visa_status) {
                                case 'success':
                                    paymentStatusLabel = '{{ trans("order::order.payment_success") }}';
                                    paymentStatusColor = '#28a745';
                                    break;
                                case 'pending':
                                    paymentStatusLabel = '{{ trans("order::order.payment_pending") }}';
                                    paymentStatusColor = '#ffc107';
                                    break;
                                case 'fail':
                                case 'failed':
                                    paymentStatusLabel = '{{ trans("order::order.payment_failed") }}';
                                    paymentStatusColor = '#dc3545';
                                    break;
                                default:
                                    paymentStatusLabel = row.payment_visa_status;
                                    paymentStatusColor = '#6c757d';
                            }
                            html += `<div><span class="badge badge-round" style="background-color: ${paymentStatusColor}; color: white; font-size: 10px;">{{ trans("order::order.payment_status") }}: ${paymentStatusLabel}</span></div>`;
                        }
                        
                        return html;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let showUrl =
                            "{{ route('admin.orders.show', ':id') }}"
                            .replace(':id', row.id);
                        let editUrl =
                            "{{ route('admin.orders.edit', ':id') }}"
                            .replace(':id', row.id);
                        let paymentsUrl =
                            "{{ route('admin.orders.payments', ':id') }}"
                            .replace(':id', row.id);
                        // Check if ALL product stages are in final stages (deliver, cancel, refund)
                        const finalStages = ['deliver', 'cancel', 'refund'];
                        const isFinalStage = row.product_stages && row.product_stages.length > 0 
                            ? row.product_stages.every(ps => ps.slug && finalStages.includes(ps.slug))
                            : false;
                        
                        // Check if any product is in progress
                        const hasInProgressProduct = row.product_stages && row.product_stages.length > 0
                            ? row.product_stages.some(ps => ps.type === 'in_progress')
                            : false;
                        
                        // For vendors: check if order belongs exclusively to them
                        const canEditDelete = isVendorUser ? row.is_exclusive_to_vendor : true;
                        
                        // Check if order has online payment
                        const hasOnlinePayment = row.payment_type === 'online';
                        
                        // Vendor stage info
                        const vendorStage = row.vendor_stage || null;
                        const vendorStageType = vendorStage ? vendorStage.type : null;
                        const vendorStageId = vendorStage ? vendorStage.id : null;
                        const isVendorFinalStage = vendorStageType && ['deliver', 'cancel', 'refund'].includes(vendorStageType);

                        let actionsHtml = `<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">`;
                        
                        // View button - always shown
                        actionsHtml += `
                            @can('orders.show')
                                <a href="${showUrl}"
                                class="view btn btn-primary table_action_father"
                                title="{{ trans('order::order.view_order') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                            @endcan
                        `;
                        
                        // For vendors: show stage change button
                        if (isVendorUser && !isVendorFinalStage) {
                            actionsHtml += `
                                <button type="button"
                                    class="btn btn-warning table_action_father btn-change-vendor-stage"
                                    data-order-id="${row.id}"
                                    data-stage-id="${vendorStageId || ''}"
                                    data-stage-type="${vendorStageType || ''}"
                                    title="{{ trans('order::order.change_stage') }}">
                                    <i class="uil uil-exchange table_action_icon"></i>
                                </button>
                            `;
                        }
                        
                        // For admin: show stage change button for all vendors
                        if (!isVendorUser && !isFinalStage) {
                            actionsHtml += `
                                <button type="button"
                                    class="btn btn-warning table_action_father btn-admin-change-stage"
                                    data-order-id="${row.id}"
                                    title="{{ trans('order::order.change_stage_all_vendors') }}">
                                    <i class="uil uil-exchange table_action_icon"></i>
                                </button>
                            `;
                        }
                        
                        // Admin quick actions: In Progress, Deliver, Cancel
                        if (!isVendorUser && row.vendors_with_stages && row.vendors_with_stages.length > 0) {
                            // Check if all vendors are in 'new' stage
                            const allNew = row.vendors_with_stages.every(v => v.stage?.type === 'new');
                            // Check if all vendors are in 'in_progress' stage
                            const allInProgress = row.vendors_with_stages.every(v => v.stage?.type === 'in_progress');
                            // Check if any vendor is not in final stage
                            const hasNonFinal = row.vendors_with_stages.some(v => !['deliver', 'cancel', 'refund'].includes(v.stage?.type));
                            
                            // In Progress button (only if all vendors are in 'new')
                            if (allNew) {
                                actionsHtml += `
                                    <button type="button"
                                        class="btn btn-secondary table_action_father btn-admin-quick-stage"
                                        data-order-id="${row.id}"
                                        data-target-type="in_progress"
                                        title="{{ trans('order::order.mark_in_progress') }}">
                                        <i class="uil uil-process table_action_icon"></i>
                                    </button>
                                `;
                            }
                            
                            // Deliver button (only if all vendors are in 'in_progress')
                            if (allInProgress) {
                                actionsHtml += `
                                    <button type="button"
                                        class="btn btn-success table_action_father btn-admin-quick-stage"
                                        data-order-id="${row.id}"
                                        data-target-type="deliver"
                                        title="{{ trans('order::order.mark_delivered') }}">
                                        <i class="uil uil-check-circle table_action_icon"></i>
                                    </button>
                                `;
                            }
                            
                            // Cancel button (if any vendor is not in final stage)
                            if (hasNonFinal) {
                                actionsHtml += `
                                    <button type="button"
                                        class="btn btn-danger table_action_father btn-admin-quick-stage"
                                        data-order-id="${row.id}"
                                        data-target-type="cancel"
                                        title="{{ trans('order::order.cancel_order') }}">
                                        <i class="uil uil-times-circle table_action_icon"></i>
                                    </button>
                                `;
                            }
                        }
                        
                        // Edit button for vendors (if not final stage)
                        if (isVendorUser && !isVendorFinalStage && canEditDelete) {
                            actionsHtml += `
                                @can('orders.update')
                                    <a href="${editUrl}"
                                    class="btn btn-info table_action_father"
                                    title="{{ trans('order::order.edit_order') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                @endcan
                            `;
                        }
                        
                        // Vendor quick actions: In Progress, Deliver, Cancel
                        if (isVendorUser && vendorStage) {
                            // In Progress button (only if current stage is 'new')
                            if (vendorStageType === 'new') {
                                actionsHtml += `
                                    <button type="button"
                                        class="btn btn-secondary table_action_father btn-vendor-quick-stage"
                                        data-order-id="${row.id}"
                                        data-target-type="in_progress"
                                        title="{{ trans('order::order.mark_in_progress') }}">
                                        <i class="uil uil-process table_action_icon"></i>
                                    </button>
                                `;
                            }
                            
                            // Deliver button (only if current stage is 'in_progress')
                            if (vendorStageType === 'in_progress') {
                                actionsHtml += `
                                    <button type="button"
                                        class="btn btn-success table_action_father btn-vendor-quick-stage"
                                        data-order-id="${row.id}"
                                        data-target-type="deliver"
                                        title="{{ trans('order::order.mark_delivered') }}">
                                        <i class="uil uil-check-circle table_action_icon"></i>
                                    </button>
                                `;
                            }
                            
                            // Cancel button (if not already final)
                            if (!isVendorFinalStage) {
                                actionsHtml += `
                                    <button type="button"
                                        class="btn btn-danger table_action_father btn-vendor-quick-stage"
                                        data-order-id="${row.id}"
                                        data-target-type="cancel"
                                        title="{{ trans('order::order.cancel_order') }}">
                                        <i class="uil uil-times-circle table_action_icon"></i>
                                    </button>
                                `;
                            }
                        }
                        
                        // Payments button
                        if (hasOnlinePayment) {
                            actionsHtml += `
                                <a href="${paymentsUrl}"
                                class="btn btn-success table_action_father"
                                title="{{ trans('order::order.view_payments') }}">
                                    <i class="uil uil-credit-card table_action_icon"></i>
                                </a>
                            `;
                        }
                        
                        // Allocate button - for admin when any vendor is in_progress
                        if (!isVendorUser && row.vendors_with_stages && row.vendors_with_stages.length > 0) {
                            const hasInProgress = row.vendors_with_stages.some(v => v.stage?.type === 'in_progress');
                            if (hasInProgress) {
                                actionsHtml += `
                                    <a href="${'{{ route('admin.order-fulfillments.allocate', ':id') }}'.replace(':id', row.id)}"
                                    class="btn btn-info table_action_father"
                                    title="{{ trans('order::order.allocate') }}">
                                        <i class="uil uil-box table_action_icon"></i>
                                    </a>
                                `;
                            }
                        }
                        
                        // Allocate button - for vendors only when in_progress and not fully allocated
                        if (isVendorUser && vendorStage && vendorStageType === 'in_progress' && !row.is_fully_allocated) {
                            actionsHtml += `
                                <a href="${'{{ route('admin.order-fulfillments.allocate', ':id') }}'.replace(':id', row.id)}"
                                class="btn btn-info table_action_father"
                                title="{{ trans('order::order.allocate') }}">
                                    <i class="uil uil-box table_action_icon"></i>
                                </a>
                            `;
                        }
                        
                        actionsHtml += `</div>`;
                        return actionsHtml;
                    }
                }
            );
            
            table = $('#ordersDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.orders.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.stage = $('#stage').val();
                        d.payment_type = $('#payment_type').val();
                        // Handle multiple vendor selection from multi-select component
                        if (document.getElementById('vendor')) {
                            const vendorValues = MultiSelect.getValues('vendor');
                            d.vendor = vendorValues.length > 0 ? vendorValues.join(',') : '';
                        }
                        d.created_date_from = $('#created_from_filter').val();
                        d.created_date_to = $('#created_until_filter').val();
                        d.payment_visa_status = $('#payment_visa_status').val();
                        return d;
                    }
                },
                columns: tableColumns,
                pageLength: per_page,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "{{ __('common.search') }}:",
                }
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Live search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                    updateUrlParams();
                }, 500);
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Filter change handlers for select and date inputs
            $('#stage, #payment_type, #payment_visa_status, #created_from_filter, #created_until_filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#stage').val('');
                $('#payment_type').val('');
                $('#payment_visa_status').val('');
                if (document.getElementById('vendor')) {
                    MultiSelect.clear('vendor');
                }
                $('#created_from_filter').val('');
                $('#created_until_filter').val('');
                
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Update URL parameters function
            function updateUrlParams() {
                const params = new URLSearchParams();
                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#stage').val()) params.set('stage', $('#stage').val());
                if ($('#payment_type').val()) params.set('payment_type', $('#payment_type').val());
                if ($('#payment_visa_status').val()) params.set('payment_visa_status', $('#payment_visa_status').val());
                // Handle multiple vendor selection from multi-select component
                if (document.getElementById('vendor')) {
                    const vendorValues = MultiSelect.getValues('vendor');
                    if (vendorValues.length > 0) params.set('vendor', vendorValues.join(','));
                }
                if ($('#created_from_filter').val()) params.set('created_from', $('#created_from_filter').val());
                if ($('#created_until_filter').val()) params.set('created_until', $('#created_until_filter').val());

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window
                    .location.pathname;
                window.history.replaceState({}, '', newUrl);
            }
            
            @if(!isAdmin())
            // Vendor stage management
            const orderStages = @json($orderStages);
            const vendorId = {{ auth()->user()->vendor?->id ?? 'null' }};
            
            // Stage step mapping
            const STAGE_STEPS = {
                'new': 1,
                'in_progress': 2,
                'deliver': 3,
                'cancel': 3,
                'refund': 4
            };
            
            const FINAL_STAGES = ['deliver', 'cancel'];
            
            function getStageStep(type) {
                if (!type) return 0;
                return STAGE_STEPS[type] || 0;
            }
            
            function isFinalStage(type) {
                if (!type) return false;
                return FINAL_STAGES.includes(type);
            }
            
            function canTransitionTo(currentType, newType, currentId, newId) {
                if (currentId == newId) return false;
                if (isFinalStage(currentType)) return false;
                if (!currentType) {
                    const newStep = getStageStep(newType);
                    return newStep <= 2;
                }
                if (!newType) return true;
                
                const currentStep = getStageStep(currentType);
                const newStep = getStageStep(newType);
                
                if (newStep < currentStep) return false;
                if (newType === 'cancel') return true;
                if (newStep > currentStep + 1) return false;
                if (newType === 'refund' && currentType !== 'deliver') return false;
                
                return true;
            }
            
            function getStageByType(targetType) {
                return orderStages.find(s => s.type === targetType);
            }
            
            // Quick stage change button click
            $(document).on('click', '.btn-vendor-quick-stage', function() {
                const orderId = $(this).data('order-id');
                const targetType = $(this).data('target-type');
                const targetStage = getStageByType(targetType);
                
                if (!targetStage) {
                    toastr.error('{{ trans("order::order.stage_not_found") }}');
                    return;
                }
                
                let message = '';
                switch(targetType) {
                    case 'in_progress':
                        message = '{{ trans("order::order.confirm_in_progress") }}';
                        break;
                    case 'deliver':
                        message = '{{ trans("order::order.confirm_deliver") }}';
                        break;
                    case 'cancel':
                        message = '{{ trans("order::order.confirm_cancel") }}';
                        break;
                    default:
                        message = '{{ trans("order::order.confirm_stage_change") }}';
                }
                
                $('#quickStageOrderId').val(orderId);
                $('#quickStageTargetType').val(targetType);
                $('#quickStageMessage').text(message);
                
                const modal = new bootstrap.Modal(document.getElementById('vendorQuickStageModal'));
                modal.show();
            });
            
            // Confirm quick stage change
            $('#confirmQuickStageBtn').on('click', function() {
                const orderId = $('#quickStageOrderId').val();
                const targetType = $('#quickStageTargetType').val();
                const targetStage = getStageByType(targetType);
                
                if (!targetStage || !vendorId) {
                    toastr.error('{{ trans("common.error") }}');
                    return;
                }
                
                const $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({ text: '{{ trans("order::order.updating_stage") }}' });
                }
                
                $.ajax({
                    url: '{{ route("admin.orders.vendor-stage.change", ["orderId" => "__ORDER__", "vendorId" => "__VENDOR__"]) }}'
                        .replace('__ORDER__', orderId)
                        .replace('__VENDOR__', vendorId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stage_id: targetStage.id
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                        bootstrap.Modal.getInstance(document.getElementById('vendorQuickStageModal')).hide();
                        toastr.success(response.message || '{{ trans("order::order.stage_updated_successfully") }}');
                         
                        // Redirect to allocate page if stage is in_progress
                        if (targetType === 'in_progress') {
                            setTimeout(function() {
                                window.location.href = '{{ route("admin.order-fulfillments.allocate", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId);
                            }, 500);
                        } else {
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                        let errorMessage = '{{ trans("order::order.error_updating_stage") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('{{ __("common.confirm") }}');
                    }
                });
            });
            
            // Change stage modal button click
            $(document).on('click', '.btn-change-vendor-stage', function() {
                const orderId = $(this).data('order-id');
                const stageId = $(this).data('stage-id');
                const stageType = $(this).data('stage-type');
                
                $('#vendorStageOrderId').val(orderId);
                $('#vendorCurrentStageId').val(stageId);
                $('#vendorCurrentStageType').val(stageType);
                
                // Populate stage select
                const $select = $('#vendorNewStage');
                const $warning = $('#vendorStageWarning');
                $select.find('option:not(:first)').remove();
                $warning.hide();
                
                if (isFinalStage(stageType)) {
                    $warning.text('{{ trans("order::order.cannot_change_final_stage") }}').show();
                } else {
                    let hasOptions = false;
                    orderStages.forEach(stage => {
                        if (canTransitionTo(stageType, stage.type, stageId, stage.id)) {
                            $select.append(`<option value="${stage.id}">${stage.name}</option>`);
                            hasOptions = true;
                        }
                    });
                    
                    if (!hasOptions) {
                        $warning.text('{{ trans("order::order.no_available_stages") }}').show();
                    }
                }
                
                const modal = new bootstrap.Modal(document.getElementById('vendorChangeStageModal'));
                modal.show();
            });
            
            // Confirm vendor stage change
            $('#confirmVendorStageBtn').on('click', function() {
                const orderId = $('#vendorStageOrderId').val();
                const stageId = $('#vendorNewStage').val();
                
                if (!stageId) {
                    toastr.warning('{{ trans("order::order.select_stage") }}');
                    return;
                }
                
                if (!vendorId) {
                    toastr.error('{{ trans("common.error") }}');
                    return;
                }
                
                const $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({ text: '{{ trans("order::order.updating_stage") }}' });
                }
                
                $.ajax({
                    url: '{{ route("admin.orders.vendor-stage.change", ["orderId" => "__ORDER__", "vendorId" => "__VENDOR__"]) }}'
                        .replace('__ORDER__', orderId)
                        .replace('__VENDOR__', vendorId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stage_id: stageId
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                        bootstrap.Modal.getInstance(document.getElementById('vendorChangeStageModal')).hide();
                        toastr.success(response.message || '{{ trans("order::order.stage_updated_successfully") }}');
                        
                        // Check if the selected stage is in_progress and redirect to allocate page
                        const selectedStage = orderStages.find(s => s.id == stageId);
                        if (selectedStage && (selectedStage.type === 'in_progress' || selectedStage.slug === 'in-progress')) {
                            setTimeout(function() {
                                window.location.href = '{{ route("admin.order-fulfillments.allocate", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId);
                            }, 500);
                        } else {
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                        let errorMessage = '{{ trans("order::order.error_updating_stage") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('{{ trans("order::order.update_stage") }}');
                    }
                });
            });
            @endif
            
            @if(isAdmin())
            // Admin stage management
            const orderStages = @json($orderStages);
            
            // Stage step mapping for validation
            const STAGE_STEPS = {
                'new': 1,
                'in_progress': 2,
                'deliver': 3,
                'cancel': 3,
                'refund': 4
            };
            
            const FINAL_STAGES = ['deliver', 'cancel'];
            
            function getStageStep(type) {
                if (!type) return 0;
                return STAGE_STEPS[type] || 0;
            }
            
            function isFinalStage(type) {
                if (!type) return false;
                return FINAL_STAGES.includes(type);
            }
            
            function canTransitionTo(currentType, newType, currentId, newId) {
                if (currentId == newId) return false;
                if (isFinalStage(currentType)) return false;
                if (!currentType) {
                    const newStep = getStageStep(newType);
                    return newStep <= 2;
                }
                if (!newType) return true;
                
                const currentStep = getStageStep(currentType);
                const newStep = getStageStep(newType);
                
                if (newStep < currentStep) return false;
                if (newType === 'cancel') return true;
                if (newStep > currentStep + 1) return false;
                if (newType === 'refund' && currentType !== 'deliver') return false;
                
                return true;
            }
            
            // Admin change stage modal button click
            $(document).on('click', '.btn-admin-change-stage', function() {
                const orderId = $(this).data('order-id');
                $('#adminStageOrderId').val(orderId);
                $('#adminNewStage').val('');
                $('#adminStageWarning').hide();
                
                // Find the row data to get current vendor stages
                const rowData = table.rows().data().toArray().find(r => r.id == orderId);
                
                // Clear and populate select
                const $select = $('#adminNewStage');
                $select.find('option:not(:first)').remove();
                
                if (rowData && rowData.vendors_with_stages && rowData.vendors_with_stages.length > 0) {
                    // Find the "lowest" stage among all vendors (the one that's earliest in workflow)
                    let lowestStep = 999;
                    let lowestStageType = null;
                    let lowestStageId = null;
                    let lowestStageName = '';
                    let lowestStageColor = '#6c757d';
                    let currentStageTypes = [];
                    
                    rowData.vendors_with_stages.forEach(v => {
                        const stageType = v.stage?.type;
                        const stageId = v.stage?.id;
                        if (stageType) {
                            currentStageTypes.push(stageType);
                            const step = getStageStep(stageType);
                            if (step < lowestStep) {
                                lowestStep = step;
                                lowestStageType = stageType;
                                lowestStageId = stageId;
                                lowestStageName = v.stage?.name || '';
                                lowestStageColor = v.stage?.color || '#6c757d';
                            }
                        }
                    });
                    
                    // Show current stage
                    $('#adminCurrentStageName').text(lowestStageName).css('background-color', lowestStageColor);
                    $('#adminCurrentStageType').val(lowestStageType);
                    $('#adminCurrentStageId').val(lowestStageId);
                    
                    // Check if all vendors are in final stage
                    const allFinal = currentStageTypes.length > 0 && currentStageTypes.every(t => isFinalStage(t));
                    
                    if (allFinal) {
                        $('#adminStageWarning').text('{{ trans("order::order.cannot_change_final_stage") }}').show();
                    } else {
                        // Populate select with available stages
                        let hasOptions = false;
                        orderStages.forEach(stage => {
                            if (canTransitionTo(lowestStageType, stage.type, lowestStageId, stage.id)) {
                                $select.append(`<option value="${stage.id}">${stage.name}</option>`);
                                hasOptions = true;
                            }
                        });
                        
                        if (!hasOptions) {
                            $('#adminStageWarning').text('{{ trans("order::order.no_available_stages") }}').show();
                        }
                    }
                } else {
                    // No vendor stages found, show all stages
                    $('#adminCurrentStageName').text('-').css('background-color', '#6c757d');
                    orderStages.forEach(stage => {
                        $select.append(`<option value="${stage.id}">${stage.name}</option>`);
                    });
                }
                
                const modal = new bootstrap.Modal(document.getElementById('adminChangeStageModal'));
                modal.show();
            });
            
            // Confirm admin stage change for all vendors
            $('#confirmAdminStageBtn').on('click', function() {
                const orderId = $('#adminStageOrderId').val();
                const stageId = $('#adminNewStage').val();
                
                if (!stageId) {
                    toastr.warning('{{ trans("order::order.select_stage") }}');
                    return;
                }
                
                const $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({ text: '{{ trans("order::order.updating_stage") }}' });
                }
                
                // Find the selected stage to check if it's in_progress
                const selectedStage = orderStages.find(s => s.id == stageId);
                
                $.ajax({
                    url: '{{ route("admin.orders.change-all-vendor-stages", ["orderId" => "__ORDER__"]) }}'
                        .replace('__ORDER__', orderId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stage_id: stageId
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                        bootstrap.Modal.getInstance(document.getElementById('adminChangeStageModal')).hide();
                        toastr.success(response.message || '{{ trans("order::order.stage_updated_successfully") }}');
                        
                        // Redirect to allocate page if stage is in_progress
                        if (selectedStage && (selectedStage.type === 'in_progress' || selectedStage.slug === 'in-progress')) {
                            setTimeout(function() {
                                window.location.href = '{{ route("admin.order-fulfillments.allocate", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId);
                            }, 500);
                        } else {
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                        let errorMessage = '{{ trans("order::order.error_updating_stage") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('{{ trans("order::order.update_stage") }}');
                    }
                });
            });
            
            // Admin quick stage change button click
            $(document).on('click', '.btn-admin-quick-stage', function() {
                const orderId = $(this).data('order-id');
                const targetType = $(this).data('target-type');
                const targetStage = orderStages.find(s => s.type === targetType);
                
                if (!targetStage) {
                    toastr.error('{{ trans("order::order.stage_not_found") }}');
                    return;
                }
                
                let message = '';
                switch(targetType) {
                    case 'in_progress':
                        message = '{{ trans("order::order.confirm_in_progress_all_vendors") }}';
                        break;
                    case 'deliver':
                        message = '{{ trans("order::order.confirm_deliver_all_vendors") }}';
                        break;
                    case 'cancel':
                        message = '{{ trans("order::order.confirm_cancel_all_vendors") }}';
                        break;
                    default:
                        message = '{{ trans("order::order.confirm_stage_change_all_vendors") }}';
                }
                
                $('#adminQuickStageOrderId').val(orderId);
                $('#adminQuickStageTargetType').val(targetType);
                $('#adminQuickStageMessage').text(message);
                
                const modal = new bootstrap.Modal(document.getElementById('adminQuickStageModal'));
                modal.show();
            });
            
            // Confirm admin quick stage change
            $('#confirmAdminQuickStageBtn').on('click', function() {
                const orderId = $('#adminQuickStageOrderId').val();
                const targetType = $('#adminQuickStageTargetType').val();
                const targetStage = orderStages.find(s => s.type === targetType);
                
                if (!targetStage) {
                    toastr.error('{{ trans("common.error") }}');
                    return;
                }
                
                const $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({ text: '{{ trans("order::order.updating_stage") }}' });
                }
                
                $.ajax({
                    url: '{{ route("admin.orders.change-all-vendor-stages", ["orderId" => "__ORDER__"]) }}'
                        .replace('__ORDER__', orderId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stage_id: targetStage.id
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                        bootstrap.Modal.getInstance(document.getElementById('adminQuickStageModal')).hide();
                        toastr.success(response.message || '{{ trans("order::order.stage_updated_successfully") }}');
                        
                        // Redirect to allocate page if stage is in_progress
                        if (targetType === 'in_progress') {
                            setTimeout(function() {
                                window.location.href = '{{ route("admin.order-fulfillments.allocate", ["orderId" => "__ORDER__"]) }}'.replace('__ORDER__', orderId);
                            }, 500);
                        } else {
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') LoadingOverlay.hide();
                        let errorMessage = '{{ trans("order::order.error_updating_stage") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('{{ __("common.confirm") }}');
                    }
                });
            });
            @endif

        });
    </script>
@endpush
