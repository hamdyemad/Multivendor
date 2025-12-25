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
            object-fit: cover;
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

        {{-- Statistics Cards --}}
        <div class="row mb-25">
            <div class="col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between h-100">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar">
                                <h1 class="ap-po-details__title" id="totalOrdersCount">{{ $orders_count }}</h1>
                                <p class="ap-po-details__text text-nowrap">{{ trans('order::order.total_orders') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon ap-po-details__icon--balance d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <i class="uil uil-shopping-cart" style="font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl d-flex justify-content-between h-100">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar">
                                <h1 class="ap-po-details__title" id="totalProductPrice">{{ $total_price }}
                                    {{ currency() }}</h1>
                                <p class="ap-po-details__text text-nowrap">{{ trans('order::order.total_product_price') }}
                                </p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon ap-po-details__icon--sent d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 60px; height: 60px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                    <i class="uil uil-receipt" style="font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    <div class="col-md-2">
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
                                    <div class="col-md-2">
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
                                    <div class="col-md-2">
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

                                    @if(isAdmin())
                                        {{-- Vendor --}}
                                        <div class="col-md-2">
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
                                    <div class="col-md-2">
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
                                    <div class="col-md-2">
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

                                    <div class="col-md-12 d-flex align-items-center">
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
                                            {{ __('common.reset_filters') }}
                                        </button>
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
                                    <th><span class="userDatatable-title">{{ trans('order::order.vendor') }}</span></th>
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

    {{-- Delete Confirmation Modal --}}
    <x-delete-modal 
        modalId="modal-delete-order" 
        :title="trans('order::order.delete_order')" 
        :message="trans('order::order.delete_order_confirm')" 
        itemNameId="delete-order-name"
        confirmBtnId="confirmDeleteOrderBtn" 
        deleteRoute="{{ rtrim(route('admin.orders.index'), '/') }}"
        :cancelText="trans('main.cancel')" 
        :deleteText="trans('order::order.delete_order')" />
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
            if (urlParams.has('vendor') && document.getElementById('vendor')) {
                const vendorValues = urlParams.get('vendor').split(',');
                MultiSelect.setValues('vendor', vendorValues);
            }
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));
            
            // Server-side processing with pagination
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
                        return d;
                    }
                },
                columns: [{
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

                            return `
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
                                        <div>
                                            <i class="uil uil-phone me-1"></i> ${customerPhone}
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
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
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? ` ${parseFloat(data).toFixed(2)} {{ currency() }}` :
                                '-';
                        }
                    },
                    {
                        data: 'stage',
                        name: 'stage',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let html = '';
                            
                            // Order Stage
                            const stageName = data?.name || '-';
                            const stageColor = data?.color || '#6c757d';
                            html += `<div class="mb-1"><span class="badge badge-round badge-lg" style="background-color: ${stageColor}; color: white;">{{ trans("order::order.stage") }}: ${stageName}</span></div>`;
                            
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
                            // Check if stage is delivered, cancelled, or refund
                            const finalStages = ['deliver', 'cancel', 'refund'];
                            const isFinalStage = row.stage && finalStages.includes(row.stage.slug);
                            
                            // For vendors: check if order belongs exclusively to them
                            const isVendor = {{ !isAdmin() ? 'true' : 'false' }};
                            const canEditDelete = isVendor ? row.is_exclusive_to_vendor : true;
                            
                            // Check if order has online payment
                            const hasOnlinePayment = row.payment_type === 'online';

                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    @can('orders.show')
                                        <a href="${showUrl}"
                                        class="view btn btn-primary table_action_father"
                                        title="{{ trans('order::order.view_order') }}">
                                            <i class="uil uil-eye table_action_icon"></i>
                                        </a>
                                    @endcan
                                    ${hasOnlinePayment ? `
                                        <a href="${paymentsUrl}"
                                        class="btn btn-success table_action_father"
                                        title="{{ trans('order::order.view_payments') }}">
                                            <i class="uil uil-credit-card table_action_icon"></i>
                                        </a>
                                    ` : ''}
                                    @can('orders.edit')
                                        ${!isFinalStage && canEditDelete ? `
                                        <a href="${editUrl}"
                                        class="edit btn btn-warning table_action_father"
                                        title="{{ trans('order::order.edit_order') }}">
                                            <i class="uil uil-edit table_action_icon"></i>
                                        </a>
                                        ` : ''}
                                    @endcan
                                    @if(isAdmin())
                                        @can('orders.change-stage')
                                            ${!isFinalStage ? `
                                            <button type="button"
                                            class="change-stage btn btn-info table_action_father"
                                            data-bs-toggle="modal"
                                            data-bs-target="#changeStageModal"
                                            data-id="${row.id}"
                                            data-stage-id="${row.stage?.id || ''}"
                                            title="{{ trans('order::order.change_order_stage') }}">
                                                <i class="uil uil-exchange-alt table_action_icon"></i>
                                            </button>
                                            ` : ''}
                                        @endcan
                                    @endif
                                    @can('orders.delete')
                                        ${!isFinalStage && canEditDelete ? `
                                        <button type="button"
                                        class="btn btn-danger table_action_father"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-delete-order"
                                        data-item-id="${row.id}"
                                        data-item-name="${row.order_number}"
                                        title="{{ trans('order::order.delete_order') }}">
                                            <i class="uil uil-trash-alt table_action_icon"></i>
                                        </button>
                                        ` : ''}
                                    @endcan
                                </div>
                            `;
                        }
                    }
                ],
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
            $('#stage, #payment_type, #created_from_filter, #created_until_filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#stage').val('');
                $('#payment_type').val('');
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

        });
    </script>
@endpush
