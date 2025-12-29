@extends('layout.app')

@section('title')
    {{ trans('menu.reports.product report') }}
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
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
                    ['title' => trans('menu.reports.product report')],
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
                            <p class="ap-po-details__text">{{ trans('report.products_in_report') }}</p>
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
                            <p class="ap-po-details__text">{{ trans('report.total_products') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl" style="border-left: 4px solid #28a745;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #28a745;">
                                <span id="active-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.active_products') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #ffc107;">
                                <span id="inactive-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.inactive_products') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-25">
            <div class="col-lg-8">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">
                            {{ trans('report.products_trend') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="productsChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">{{ trans('report.product_status_distribution') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="statusChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('menu.reports.product report') }}</h4>
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

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status-filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('report.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status-filter">
                                                <option value="">{{ trans('report.all_status') }}</option>
                                                <option value="approved">{{ trans('report.approved') }}</option>
                                                <option value="pending">{{ trans('report.pending') }}</option>
                                                <option value="rejected">{{ trans('report.rejected') }}</option>
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
                        <table id="products-table" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.product_name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.sku') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.category') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.vendor') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.product_status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.product_date') }}</span></th>
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

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let productsChart, statusChart;

        $(document).ready(function() {
            let per_page = 10;

            let table = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.data.products') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.from_date = $('#from-date').val();
                        d.to_date = $('#to-date').val();
                        d.status = $('#status-filter').val();
                        return d;
                    },
                    dataFilter: function(data) {
                        console.log('Raw response:', data);
                        let json = JSON.parse(data);
                        console.log('Parsed response:', json);
                        return JSON.stringify(json);
                    },
                    dataSrc: function(json) {
                        console.log('Response received:', json);

                        // Update statistics
                        $('#record-count').text(json.recordsFiltered || 0);
                        $('#total-count').text(json.recordsTotal || 0);

                        if (json.statistics) {
                            $('#active-count').text(json.statistics.active || 0);
                            $('#inactive-count').text(json.statistics.inactive || 0);

                            // Update charts
                            updateChartsWithData(json.statistics.status_distribution || {}, json.statistics.products_trend || {});
                        }

                        return json.data || [];
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax Error Details:', { xhr, status, error });
                        console.error('Response Text:', xhr.responseText);
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
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let img = row.product_image 
                                ? '<img src="' + row.product_image + '" class="rounded me-2" style="width: 40px; height: 40px;">' 
                                : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="uil uil-image text-muted"></i></div>';
                            return '<div class="d-flex align-items-center">' + img + '<strong>' + (data || '') + '</strong></div>';
                        }
                    },
                    {
                        data: 'sku',
                        name: 'sku',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold">' + (data || '--') + '</span>';
                        }
                    },
                    {
                        data: 'category',
                        name: 'category',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '--';
                        }
                    },
                    {
                        data: 'vendor',
                        name: 'vendor',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let img = row.vendor_image 
                                ? '<img src="' + row.vendor_image + '" class="rounded-circle me-2" style="width: 30px; height: 30px;">' 
                                : '<div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;"><i class="uil uil-store text-muted" style="font-size: 14px;"></i></div>';
                            return '<div class="d-flex align-items-center">' + img + '<span>' + (data || '--') + '</span></div>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            const statusColors = {
                                'approved': 'primary',
                                'pending': 'warning',
                                'rejected': 'danger'
                            };
                            const color = statusColors[data] || 'secondary';
                            const label = data ? data.charAt(0).toUpperCase() + data.slice(1) : '--';
                            return '<span class="badge bg-' + color + ' text-white px-3 py-2 rounded-pill fw-bold">' + label + '</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '--';
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' + '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#from-date, #to-date, #status-filter').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search, #from-date, #to-date, #status-filter').val('');
                table.ajax.reload();
            });

            initializeCharts();
        });

        function initializeCharts() {
            const primaryColor = '{{ config('branding.colors.primary') }}';
            const secondaryColor = '{{ config('branding.colors.secondary') }}';

            const ctxProducts = document.getElementById('productsChart').getContext('2d');
            productsChart = new Chart(ctxProducts, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Products',
                        data: [],
                        borderColor: primaryColor,
                        backgroundColor: primaryColor + '15',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: primaryColor,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, labels: { font: { size: 13, weight: 'bold' } } }
                    },
                    scales: {
                        y: { beginAtZero: true },
                        x: { ticks: { font: { size: 12 } } }
                    }
                }
            });

            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Rejected'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: [
                            '#0d6efd', // bg-primary - approved (blue)
                            '#ffc107', // bg-warning - pending (yellow)
                            '#dc3545'  // bg-danger - rejected (red)
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom', labels: { font: { size: 12, weight: 'bold' } } }
                    }
                }
            });
        }

        function updateChartsWithData(statusDistribution, productsTrend) {
            // Update daily products bar chart
            const sortedDates = Object.keys(productsTrend).sort();
            const formattedDates = sortedDates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });

            productsChart.data.labels = formattedDates;
            productsChart.data.datasets[0].data = sortedDates.map(date => productsTrend[date]);
            productsChart.update();

            // Update status distribution doughnut chart with dynamic colors
            const statusLabels = Object.keys(statusDistribution).map(key => {
                return key.charAt(0).toUpperCase() + key.slice(1);
            });
            const statusData = Object.values(statusDistribution);

            // Get CSS variable colors
            const rootStyles = getComputedStyle(document.documentElement);
            const primaryColor = rootStyles.getPropertyValue('--bg-primary') || '#0d6efd';
            const warningColor = rootStyles.getPropertyValue('--bg-warning') || '#ffc107';
            const dangerColor = '#dc3545';

            // Map colors based on actual status keys
            const statusColors = Object.keys(statusDistribution).map(status => {
                switch(status) {
                    case 'approved': return primaryColor;
                    case 'pending': return warningColor;
                    case 'rejected': return dangerColor;
                    default: return '#6c757d'; // secondary color for unknown status
                }
            });

            statusChart.data.labels = statusLabels;
            statusChart.data.datasets[0].data = statusData;
statusChart.data.datasets[0].backgroundColor = statusColors;
            statusChart.update();
        }
    </script>
@endpush
