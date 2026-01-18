@extends('layout.app')

@section('title')
    {{ trans('menu.reports.area users') }}
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
                    ['title' => trans('menu.reports.area users')],
                ]" />
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-25">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl"
                    style="border-left: 4px solid {{ config('branding.colors.primary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.primary') }};">
                                <span id="record-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.customers_in_report') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl"
                    style="border-left: 4px solid {{ config('branding.colors.secondary') }};">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: {{ config('branding.colors.secondary') }};">
                                <span id="total-count">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.total_customers') }}</p>
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
                            <p class="ap-po-details__text">{{ trans('report.active_customers') }}</p>
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
                            <p class="ap-po-details__text">{{ trans('report.inactive_customers') }}</p>
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
                            {{ __('report.customers_by_city') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="cityChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">{{ __('report.city_distribution') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="statusChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Charts Row -->
        {{-- <div class="row mb-25">
            <div class="col-lg-12">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">
                            {{ __('Monthly Registration Trend') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="registrationChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Customers Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('menu.reports.area users') }}</h4>
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
                                                id="search"
                                                placeholder="{{ trans('report.search') }}..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- City/Area Filter --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="city-filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-map-pin me-1"></i> {{ trans('report.city') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="city-filter">
                                                <option value="">{{ trans('report.all_cities') }}</option>
                                            </select>
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
                                                <option value="active">{{ trans('report.active') }}</option>
                                                <option value="inactive">{{ trans('report.inactive') }}</option>
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
                        <table id="customers-table" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.email') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.phone') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.city') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.registered_date') }}</span></th>
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

        .dataTables_length select,
        .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .dataTables_filter input {
            margin-left: 0.5rem;
            min-width: 200px;
        }

        .dataTables_filter label {
            font-weight: 500;
            color: #495057;
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

        .dataTables_paginate .paginate_button i {
            vertical-align: middle;
        }

        /* Processing message */
        .dataTables_processing {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            width: auto;
            height: auto;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 8px;
            z-index: 1000;
        }

        .dataTables_processing.show {
            display: block;
        }

        /* Form label styling */
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
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
        let cityChart, statusChart;

        $(document).ready(function() {
            console.log('Customers by Area Report initialized');
            let per_page = 10;

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('city_id')) $('#city-filter').val(urlParams.get('city_id'));
            if (urlParams.has('from_date')) $('#from-date').val(urlParams.get('from_date'));
            if (urlParams.has('to_date')) $('#to-date').val(urlParams.get('to_date'));
            if (urlParams.has('status')) $('#status-filter').val(urlParams.get('status'));

            // Load cities for current country
            loadCities();

            // Initialize DataTable
            let table = $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.data.area-users') }}',
                    type: 'GET',
                    data: function(d) {
                        console.log('Sending AJAX data:', d);
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.city_id = $('#city-filter').val();
                        d.from_date = $('#from-date').val();
                        d.to_date = $('#to-date').val();
                        d.status = $('#status-filter').val();
                        return d;
                    },
                    dataFilter: function(data) {
                        console.log('Raw response:', data);
                        let json = JSON.parse(data);
                        console.log('Parsed response:', json);
                        
                        if (json.status && json.data) {
                            json.recordsTotal = json.data.total || json.data.count || 0;
                            json.recordsFiltered = json.data.statistics?.total_filtered || json.data.total || json.data.count || 0;
                            json.inactive = json.data.statistics?.inactive || 0;
                            json.active = json.data.statistics?.active || 0;
                            json.registration_trend = json.data.registration_trend || {};
                            json.city_distribution = json.data.city_distribution || {};
                            json.from = json.data.from;
                            json.to = json.data.to;
                            json.total = json.data.total;
                            json.data = json.data.data || [];
                            
                            console.log('Transformed response:', {
                                recordsTotal: json.recordsTotal,
                                recordsFiltered: json.recordsFiltered,
                                dataLength: json.data.length,
                                JSON: json
                            });
                        }
                        
                        return JSON.stringify(json);
                    },
                    dataSrc: function(json) {
                        console.log('Response received:', json);
                        if (!json.status) {
                            console.error('Ajax Error:', json);
                            return [];
                        }

                        // Update statistics
                        $('#record-count').text(json.recordsFiltered || 0);
                        $('#total-count').text(json.recordsTotal || 0);
                        $('#active-count').text(json.active || 0);
                        $('#inactive-count').text(json.inactive || 0);

                        // Log chart data for debugging
                        console.log('City Distribution from backend:', json.city_distribution);
                        console.log('Registration Trend from backend:', json.registration_trend);

                        // Use city distribution directly from backend (unique customers per city)
                        let cityData = json.city_distribution || {};
                        let registrationData = json.registration_trend || {};

                        // Update charts with backend data
                        if (Object.keys(cityData).length > 0) {
                            updateChartsWithData(cityData, registrationData);
                        } else {
                            console.warn('No city distribution data available - charts will be empty');
                        }

                        // Store pagination info
                        window.paginationInfo = {
                            from: json.from,
                            to: json.to,
                            total: json.total
                        };

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
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<strong>' + (data || '') + '</strong>';
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '--';
                        }
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '--';
                        }
                    },
                    {
                        data: 'city_name',
                        name: 'city_name',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge badge-round badge-lg bg-info text-white">' + (data || '--') + '</span>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ?
                                '<span class="badge badge-round badge-lg bg-success">{{ trans('report.active') }}</span>' :
                                '<span class="badge badge-round badge-lg bg-danger">{{ trans('report.inactive') }}</span>';
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
                    console.log('Initial data count:', this.api().rows().count());
                },
                drawCallback: function(settings) {
                    console.log('DataTable drawn - Rows:', this.api().rows().count());
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
            $('#city-filter, #from-date, #to-date, #status-filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#city-filter').val('');
                $('#from-date').val('');
                $('#to-date').val('');
                $('#status-filter').val('');
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, document.title, window.location.pathname);
            });

            // Initialize charts
            initializeCharts();
        });

        function loadCities() {
            $.ajax({
                url: '{{ route('admin.reports.get-cities') }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status && response.cities) {
                        let select = $('#city-filter');
                        response.cities.forEach(function(city) {
                            select.append('<option value="' + city.id + '">' + city.name + '</option>');
                        });
                        // Apply URL param after cities are loaded
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('city_id')) {
                            $('#city-filter').val(urlParams.get('city_id'));
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading cities:', error);
                }
            });
        }

        function initializeCharts() {
            const primaryColor = '{{ config('branding.colors.primary') }}';
            const secondaryColor = '{{ config('branding.colors.secondary') }}';

            // City Distribution Chart (Vertical Bar Chart)
            const cityCtx = document.getElementById('cityChart').getContext('2d');
            cityChart = new Chart(cityCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ trans('report.customers') }}',
                        data: [],
                        backgroundColor: [
                            primaryColor,
                            secondaryColor,
                            '#28a745',
                            '#ffc107',
                            '#17a2b8',
                            '#e83e8c',
                            '#fd7e14',
                            '#6f42c1',
                            '#20c997',
                            '#dc3545'
                        ],
                        borderColor: 'rgba(255, 255, 255, 0.8)',
                        borderWidth: 2,
                        borderRadius: 8,
                        barThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' {{ __('Customers') }}';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: 12 },
                                stepSize: 1
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 12 }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Status Distribution Chart (Pie Chart for Cities)
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            primaryColor,
                            secondaryColor,
                            '#28a745',
                            '#ffc107',
                            '#17a2b8',
                            '#e83e8c',
                            '#fd7e14',
                            '#6f42c1',
                            '#20c997',
                            '#dc3545'
                        ],
                        borderColor: '#fff',
                        borderWidth: 3,
                        hoverBorderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed + ' {{ __('Customers') }}';
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

            // Monthly Registration Trend Chart (Bar Chart with Gradient)
            // const regCtx = document.getElementById('registrationChart').getContext('2d');
            // const gradient = regCtx.createLinearGradient(0, 0, 0, 300);
            // gradient.addColorStop(0, primaryColor);
            // gradient.addColorStop(1, secondaryColor);
        }

        function updateChartsWithData(cityDistribution, registrationTrend) {
            // Update city bar chart (vertical bars - cities on X-axis, count on Y-axis)
            cityChart.data.labels = Object.keys(cityDistribution);
            cityChart.data.datasets[0].data = Object.values(cityDistribution);
            cityChart.update();

            // Update city pie chart (same data, different visualization)
            statusChart.data.labels = Object.keys(cityDistribution);
            statusChart.data.datasets[0].data = Object.values(cityDistribution);
            statusChart.update();

            // Update registration trend chart (monthly aggregation)
            const sortedDates = Object.keys(registrationTrend).sort();
            const monthlyData = {};
            
            sortedDates.forEach(date => {
                // Group by month (YYYY-MM)
                const month = date.substring(0, 7);
                monthlyData[month] = (monthlyData[month] || 0) + registrationTrend[date];
            });
            
            const sortedMonths = Object.keys(monthlyData).sort();
            const monthLabels = sortedMonths.map(month => {
                const [year, monthNum] = month.split('-');
                const monthName = new Date(year, monthNum - 1).toLocaleDateString('en-US', { 
                    month: 'short', 
                    year: 'numeric' 
                });
                return monthName;
            });
        }

        function updateUrlParams() {
            const params = new URLSearchParams();
            const search = $('#search').val();
            const cityId = $('#city-filter').val();
            const fromDate = $('#from-date').val();
            const toDate = $('#to-date').val();
            const status = $('#status-filter').val();

            if (search) params.set('search', search);
            if (cityId) params.set('city_id', cityId);
            if (fromDate) params.set('from_date', fromDate);
            if (toDate) params.set('to_date', toDate);
            if (status) params.set('status', status);

            const queryString = params.toString();
            const newUrl = queryString ? window.location.pathname + '?' + queryString : window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    </script>
@endpush