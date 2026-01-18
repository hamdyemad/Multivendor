@extends('layout.app')

@section('title')
    {{ trans('menu.reports.orders report') }}
@endsection

@push('styles')
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
                    [
                        'title' => trans('menu.reports.title'),
                        'url' => route('admin.reports.index'),
                    ],
                    ['title' => trans('menu.reports.orders report')],
                ]" />
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-25">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid {{ config('branding.colors.primary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.primary') }};">
                                <span id="record-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.orders_in_report') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl" style="border-left: 4px solid {{ config('branding.colors.secondary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.secondary') }};">
                                <span id="total-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.total_orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl" style="border-left: 4px solid #28a745;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #28a745;">
                                <span id="completed-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.completed_orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #ffc107;">
                                <span id="pending-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.pending_orders') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-25">
            <div class="col-lg-6">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">
                            {{ trans('report.daily_orders') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="ordersChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">{{ trans('report.order_status_distribution') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="statusChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('menu.reports.orders report') }}</h4>
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
                                                <i class="uil uil-search me-1"></i> {{ trans('report.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="{{ trans('report.search') }}..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- From Date --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="from-date" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('report.from_date') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="from-date">
                                        </div>
                                    </div>

                                    {{-- To Date --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="to-date" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('report.to_date') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="to-date">
                                        </div>
                                    </div>

                                    {{-- Stage --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="stage-filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('report.stage') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="stage-filter">
                                                <option value="">{{ trans('report.all_stages') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center gap-2">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ trans('report.search_button') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ trans('report.search_button') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ trans('report.reset_button') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ trans('report.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <label class="ms-2 mb-0">{{ trans('report.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="orders-table" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.order_number') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.customer_name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.stage') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.total') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.order_date') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Table styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(90deg, {{ config('branding.colors.primary') }} 0%, {{ config('branding.colors.secondary') }} 100%);
            border: none;
            padding: 1rem 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.6px;
            color: white;
        }

        .table tbody td {
            padding: 0.9rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.9rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            background-color: #fff;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff !important;
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tbody tr:nth-child(even):hover {
            background-color: #f8f9ff !important;
        }

        /* DataTables wrapper styling */
        .dataTables_wrapper {
            padding: 0;
        }

        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 1.5rem;
            padding: 0 25px;
            padding-top: 20px;
        }

        .dataTables_info {
            padding: 1rem 25px;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .dataTables_paginate {
            padding: 1rem 25px;
            text-align: right;
        }

        .dataTables_paginate .paginate_button {
            display: inline-block;
            padding: 0.5rem 0.8rem;
            margin: 0 0.25rem;
            border: 1px solid #dee2e6;
            background-color: #fff;
            color: #495057;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1;
        }

        .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background-color: {{ config('branding.colors.primary') }};
            border-color: {{ config('branding.colors.primary') }};
            color: white;
        }

        .dataTables_paginate .paginate_button.active {
            background-color: {{ config('branding.colors.primary') }} !important;
            border-color: {{ config('branding.colors.primary') }} !important;
            color: white !important;
        }

        .dataTables_paginate .paginate_button.disabled,
        .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
        }

        /* Form label styling */
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dataTables_wrapper {
                padding: 0;
            }

            .dataTables_length,
            .dataTables_filter {
                padding: 0 15px;
                padding-top: 15px;
            }

            .dataTables_info,
            .dataTables_paginate {
                padding: 1rem 15px;
            }

            .dataTables_paginate {
                text-align: center;
                margin-top: 1rem;
            }

            .dataTables_paginate .paginate_button {
                padding: 0.4rem 0.6rem;
                margin: 0.25rem 0.15rem;
                font-size: 0.8rem;
            }

            .table thead th {
                padding: 0.75rem 0.5rem;
                font-size: 0.7rem;
            }

            .table tbody td {
                padding: 0.65rem 0.5rem;
                font-size: 0.85rem;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        let ordersChart, statusChart;

        $(document).ready(function() {
            console.log('Orders Report initialized');
            let per_page = 10;

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('from_date')) $('#from-date').val(urlParams.get('from_date'));
            if (urlParams.has('to_date')) $('#to-date').val(urlParams.get('to_date'));
            if (urlParams.has('type')) $('#stage-filter').val(urlParams.get('type'));

            // Load order stages
            loadOrderStages();

            // Initialize DataTable
            let table = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.data.orders') }}',
                    type: 'GET',
                    data: function(d) {
                        console.log('Sending AJAX data:', d);
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.from_date = $('#from-date').val();
                        d.to_date = $('#to-date').val();
                        d.type = $('#stage-filter').val();
                        return d;
                    },
                    dataFilter: function(data) {
                        console.log('Raw response:', data);
                        let json = JSON.parse(data);
                        console.log('Parsed response:', json);

                        if (json.status && json.data) {
                            json.recordsTotal = json.data.total || 0;
                            json.recordsFiltered = json.data.statistics?.total_filtered || json.data.count || 0;
                            json.completed = json.statistics?.completed || 0;
                            json.pending = json.statistics?.pending || 0;
                            json.orders_trend = json.statistics?.orders_trend || {};
                            json.stage_distribution = json.statistics?.stage_distribution || {};
                            json.from = json.data.from;
                            json.to = json.data.to;
                            json.total = json.data.total;
                            json.data = json.data.data || [];

                            console.log('Transformed response:', {
                                json
                            });
                        }

                        return JSON.stringify(json);
                    },
                    dataSrc: function(json) {
                        console.log('Response received:', json);
                        
                        // Update statistics
                        $('#record-count').text(json.recordsFiltered || 0);
                        $('#total-count').text(json.recordsTotal || 0);
                        
                        if (json.statistics) {
                            $('#completed-count').text(json.statistics.completed || 0);
                            $('#pending-count').text(json.statistics.pending || 0);
                            
                            // Update charts
                            updateChartsWithData(json.statistics.stage_distribution || {}, json.statistics.orders_trend || {});
                        }

                        return json.data || [];
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax Error Details:', { xhr, status, error });
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
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold">#' + (data || '') + '</span>';
                        }
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<strong>' + (data || '--') + '</strong>';
                        }
                    },
                    {
                        data: 'stage',
                        name: 'stage',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const stageColors = {
                                'new': 'info',
                                'in_progress': 'warning',
                                'deliver': 'success',
                                'cancel': 'danger',
                                'want_to_return': 'secondary',
                                'in_progress_return': 'warning',
                                'refund': 'dark'
                            };
                            const color = stageColors[row.stage_type] || 'secondary';
                            return '<span class="badge bg-' + color + ' text-white px-3 py-2 rounded-pill fw-bold">' + (data || 'N/A') + '</span>';
                        }
                    },
                    {
                        data: 'total',
                        name: 'total',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? parseFloat(data).toFixed(2) : '0.00';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "{{ __('common.search') }}:",
                },
                initComplete: function(settings, json) {
                    console.log('DataTable initialized successfully');
                },
                drawCallback: function(settings) {
                    console.log('DataTable drawn', settings);
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

            // Filter change events
            $('#from-date, #to-date, #stage-filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#from-date').val('');
                $('#to-date').val('');
                $('#stage-filter').val('');
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, document.title, window.location.pathname);
            });

            // Initialize charts
            initializeCharts();
        });

        function loadOrderStages() {
            // Fetch available order stages from the system
            $.ajax({
                url: '{{ route('api.order-stages.index') }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response && response.data && Array.isArray(response.data)) {
                        let select = $('#stage-filter');
                        response.data.forEach(function(stage) {
                            select.append('<option value="' + stage.id + '">' + stage.name + '</option>');
                        });
                        // Apply URL param after stages are loaded
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('type')) {
                            $('#stage-filter').val(urlParams.get('type'));
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading order stages:', error);
                }
            });
        }

        function initializeCharts() {
            const primaryColor = '{{ config('branding.colors.primary') }}';
            const secondaryColor = '{{ config('branding.colors.secondary') }}';

            // Daily Orders Chart (Bar Chart)
            const ctxOrders = document.getElementById('ordersChart').getContext('2d');
            ordersChart = new Chart(ctxOrders, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __("Orders") }}',
                        data: [],
                        backgroundColor: primaryColor + '80',
                        borderColor: primaryColor,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                font: { size: 13, weight: 'bold' },
                                padding: 15,
                                usePointStyle: true
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { font: { size: 12 } },
                            grid: { color: '#f0f0f0' }
                        },
                        x: {
                            ticks: { font: { size: 12 } },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Stage Distribution Chart (Doughnut Chart)
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#17a2b8', // info - new
                            '#ffc107', // warning - in progress
                            '#28a745', // success - deliver
                            '#dc3545', // danger - cancel
                            '#6c757d', // secondary - want to return
                            '#fd7e14', // orange - in progress return
                            '#343a40'  // dark - refund
                        ],
                        borderColor: '#fff',
                        borderWidth: 3,
                        hoverBorderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                font: { size: 12, weight: 'bold' },
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    label += context.parsed + ' {{ __("Orders") }}';
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    label += ' (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateChartsWithData(stageDistribution, ordersTrend) {
            // Update daily orders bar chart
            const sortedDates = Object.keys(ordersTrend).sort();
            const formattedDates = sortedDates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });

            ordersChart.data.labels = formattedDates;
            ordersChart.data.datasets[0].data = sortedDates.map(date => ordersTrend[date]);
            ordersChart.update();

            // Update stage distribution doughnut chart with proper labels
            const stageLabels = {
                'new': 'New Orders',
                'in_progress': 'In Progress',
                'deliver': 'Delivered',
                'cancel': 'Cancelled',
                'want_to_return': 'Return Request',
                'in_progress_return': 'Processing Return',
                'refund': 'Refunded'
            };

            const chartLabels = Object.keys(stageDistribution).map(key => stageLabels[key] || key);
            const chartData = Object.values(stageDistribution);

            statusChart.data.labels = chartLabels;
            statusChart.data.datasets[0].data = chartData;
            statusChart.update();
        }

        function updateUrlParams() {
            const params = new URLSearchParams();
            const search = $('#search').val();
            const fromDate = $('#from-date').val();
            const toDate = $('#to-date').val();
            const stageId = $('#stage-filter').val();

            if (search) params.set('search', search);
            if (fromDate) params.set('from_date', fromDate);
            if (toDate) params.set('to_date', toDate);
            if (stageId) params.set('type', stageId);

            const queryString = params.toString();
            const newUrl = queryString ? window.location.pathname + '?' + queryString : window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    </script>
@endpush
