@extends('layout.app')
@section('title')
    {{ trans('order::order.order_management') }} | Bnaia
@endsection
@push('styles')
    <!-- Select2 CSS loaded via Vite -->
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
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between h-100">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar">
                                <h1 class="ap-po-details__title" id="totalOrdersCount">0</h1>
                                <p class="ap-po-details__text text-nowrap">{{ trans('order::order.total_orders') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon ap-po-details__icon--balance d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <i class="uil uil-shopping-cart" style="font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl d-flex justify-content-between h-100">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar">
                                <h1 class="ap-po-details__title" id="totalProductPrice">0.00 {{ __('common.currency') }}</h1>
                                <p class="ap-po-details__text text-nowrap">{{ trans('order::order.total_product_price') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon ap-po-details__icon--sent d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                    <i class="uil uil-receipt" style="font-size: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl d-flex justify-content-between h-100">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content h-100">
                            <div class="ap-po-details__titlebar">
                                <h1 class="ap-po-details__title" id="totalIncome">0.00 {{ __('common.currency') }}</h1>
                                <p class="ap-po-details__text text-nowrap">{{ trans('order::order.income') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="ap-po-details__icon ap-po-details__icon--remaining d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                    <i class="uil uil-money-bill" style="font-size: 24px;"></i>
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('common.search') }}..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- stage --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="stage" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('order::order.stage') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="stage">
                                                <option value="">{{ trans('order::order.all_stages') }}</option>
                                            </select>
                                        </div>
                                    </div>

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
                                    <th><span class="userDatatable-title">{{ trans('order::order.order_id') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('order::order.customer_name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('order::order.customer_email') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('order::order.total_price') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('order::order.items_count') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('order::order.stage') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('order::order.created_at') }}</span></th>
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
    <x-order::change-stage-modal :order-id="null" />

@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;
            let orderStages = [];

            // Fetch order stages for filter dropdown
            function loadOrderStages() {
                $.ajax({
                    url: '{{ route('admin.order-stages.index') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.data) {
                            orderStages = response.data;
                            populateStageFilters();
                        }
                    }
                });
            }

            // Populate stage filter and modal select
            function populateStageFilters() {
                const stageSelect = $('#stage');
                const newStageSelect = $('#newStage');

                stageSelect.find('option:not(:first)').remove();
                newStageSelect.find('option:not(:first)').remove();

                orderStages.forEach(stage => {
                    const stageName = stage.translations && stage.translations.en
                        ? stage.translations.en.name
                        : stage.slug;

                    stageSelect.append(`<option value="${stage.id}">${stageName}</option>`);
                    newStageSelect.append(`<option value="${stage.id}">${stageName}</option>`);
                });
            }

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('stage')) $('#stage').val(urlParams.get('stage'));
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));

            // Load stages first
            loadOrderStages();

            // Server-side processing with pagination
            let table = $('#ordersDataTable').DataTable({
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
                        d.created_date_from = $('#created_from_filter').val();
                        d.created_date_to = $('#created_until_filter').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'order_number',
                        name: 'order_number',
                        orderable: false,
                        searchable: true,
                        render: function(data) {
                            return `${data}`;
                        }
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        orderable: false,
                        searchable: true,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'customer_email',
                        name: 'customer_email',
                        orderable: false,
                        searchable: true,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? ` ${parseFloat(data).toFixed(2)}` : '-';
                        }
                    },
                    {
                        data: 'items_count',
                        name: 'items_count',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '0';
                        }
                    },
                    {
                        data: 'stage',
                        name: 'stage',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data) return '-';
                            const stageName = data.translations && data.translations.en
                                ? data.translations.en.name
                                : data.slug;
                            const stageColor = data.color || '#6c757d';
                            return `<span class="badge" style="background-color: ${stageColor}; color: white;">${stageName}</span>`;
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
                            let showUrl = "{{ route('admin.orders.show', ':id') }}".replace(':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('order::order.view_order') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <button type="button"
                                    class="change-stage btn btn-info table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#changeStageModal"
                                    data-id="${row.id}"
                                    data-stage-id="${row.stage?.id || ''}"
                                    title="{{ trans('order::order.change_order_stage') }}">
                                        <i class="uil uil-exchange-alt table_action_icon"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "{{ __('common.search') }}:",
                },
                drawCallback: function(settings) {
                    updateStatistics(settings);
                }
            });

            // Function to update statistics
            function updateStatistics(settings) {
                const data = settings.json;

                if (data && data.data) {
                    let totalOrders = data.recordsTotal || 0;
                    let totalProductPrice = 0;
                    let totalIncome = 0;

                    // Calculate totals from all orders (not just current page)
                    $.ajax({
                        url: '{{ route('admin.orders.datatable') }}',
                        type: 'GET',
                        data: {
                            per_page: 10000, // Get all records
                            page: 1,
                            search: $('#search').val(),
                            stage: $('#stage').val(),
                            created_date_from: $('#created_from_filter').val(),
                            created_date_to: $('#created_until_filter').val(),
                        },
                        success: function(response) {
                            if (response.data) {
                                totalOrders = response.recordsFiltered || response.recordsTotal || 0;

                                response.data.forEach(order => {
                                    totalProductPrice += parseFloat(order.total_product_price || 0);
                                    
                                    // Only add to income if order is delivered
                                    if (order.stage_id === 3) {
                                        totalIncome += parseFloat(order.total_price || 0);
                                    }
                                });

                                // Update card displays
                                $('#totalOrdersCount').text(totalOrders);
                                $('#totalProductPrice').text(totalProductPrice.toFixed(2) + ' {{ __('common.currency') }}');
                                $('#totalIncome').text(totalIncome.toFixed(2) + ' {{ __('common.currency') }}');
                            }
                        }
                    });
                }
            }

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

            // Filter change handlers
            $('#stage, #created_from_filter, #created_until_filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#stage').val('');
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
                if ($('#created_from_filter').val()) params.set('created_from', $('#created_from_filter').val());
                if ($('#created_until_filter').val()) params.set('created_until', $('#created_until_filter').val());

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            // Handle change stage modal
            $(document).on('click', '.change-stage', function() {
                const orderId = $(this).data('id');
                $('#orderId').val(orderId);
            });

            // Handle change stage form submission
            $('#changeStageForm').on('submit', function(e) {
                e.preventDefault();

                const orderId = $('#orderId').val();
                const stageId = $('#newStage').val();

                if (!stageId) {
                    toastr.warning('{{ trans('order.please_select_stage') }}');
                    return;
                }

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ trans('order::order.updating_stage') }}',
                        subtext: '{{ __('common.please_wait') }}...'
                    });
                }

                $.ajax({
                    url: `{{ url('admin/orders') }}/${orderId}/change-stage`,
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
                        toastr.success(response.message || '{{ trans('order.stage_updated_successfully') }}');

                        // Reload table
                        setTimeout(() => table.ajax.reload(), 1500);
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        let errorMessage = '{{ trans('order.error_updating_stage') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        toastr.error(errorMessage);
                    }
                });
            });

            // Handle change stage modal - set order ID and stage ID from button data attributes
            $('#changeStageModal').on('show.bs.modal', function(e) {
                const button = $(e.relatedTarget);
                const orderId = button.data('id');
                const stageId = button.data('stage-id');

                $('#orderId').val(orderId);
                $('#currentStageId').val(stageId);
            });
        });
    </script>
@endpush
