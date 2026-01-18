@extends('layout.app')

@section('title')
    {{ trans('menu.reports.points report') }}
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
                    ['title' => trans('menu.reports.points report')],
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
                            <p class="ap-po-details__text">{{ trans('report.points_in_report') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--3 p-25 radius-xl" style="border-left: 4px solid #28a745;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #28a745;">
                                <span id="earned-points">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.total_earned_points') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--4 p-25 radius-xl" style="border-left: 4px solid #dc3545;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #dc3545;">
                                <span id="redeemed-points">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.total_redeemed_points') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl" style="border-left: 4px solid #ffc107;">
                    <div class="overview-content">
                        <div class="ap-po-details__titlebar">
                            <h1 class="ap-po-details__title" style="color: #ffc107;">
                                <span id="avg-points">0</span>
                            </h1>
                            <p class="ap-po-details__text">{{ trans('report.avg_points') }}</p>
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
                            {{ trans('report.points_trend') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="pointsChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card radius-xl p-25 h-100">
                    <div class="card__header pb-20" style="border-bottom: 2px solid #f0f0f0;">
                        <h5 style="color: {{ config('branding.colors.primary') }};">{{ trans('report.points_distribution') }}</h5>
                    </div>
                    <div class="card__body pt-20" style="min-height: 350px; display: flex; align-items: center;">
                        <canvas id="distributionChart" style="max-height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Points Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('menu.reports.points report') }}</h4>
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

                                    {{-- Min Points --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="min-points" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-award me-1"></i>
                                                {{ trans('report.min_points') }}
                                            </label>
                                            <input type="number"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="min-points" placeholder="0">
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
                        <table id="points-table" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('report.user_name') }}</span></th>
                                    <th class="text-center"><span class="userDatatable-title">{{ __('report.earned_points') }}</span></th>
                                    <th class="text-center"><span class="userDatatable-title">{{ __('report.redeemed_points') }}</span></th>
                                    <th class="text-center"><span class="userDatatable-title">{{ __('report.points_spent') }}</span></th>
                                    <th class="text-center"><span class="userDatatable-title">{{ __('report.remaining_points') }}</span></th>
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
    <script>
        let pointsChart, distributionChart;

        $(document).ready(function() {
            let per_page = 10;

            let table = $('#points-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.data.points') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.from_date = $('#from-date').val();
                        d.to_date = $('#to-date').val();
                        d.min_points = $('#min-points').val();
                        return d;
                    },
                    dataFilter: function(data) {
                        let json = JSON.parse(data);
                        console.log('Raw response:', json);
                        
                        // Update statistics if available
                        if (json.statistics) {
                            console.log('Updating statistics:', json.statistics);
                            $('#record-count').text(json.statistics.total_users || 0);
                            $('#earned-points').text(json.statistics.total_earned || 0);
                            $('#redeemed-points').text(json.statistics.total_redeemed || 0);
                            $('#avg-points').text(json.statistics.avg_points || 0);
                        }
                        
                        return JSON.stringify(json);
                    },
                    dataSrc: function(json) {
                        console.log('DataSrc data:', json.data);
                        
                        // Update charts with statistics data
                        if (json.statistics) {
                            updateChartsWithData(json.statistics.points_distribution || {}, json.statistics.points_trend || {});
                        }
                        
                        return json.data || [];
                    }
                },
                columns: [
                    {
                        data: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'user_name',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let html = '<div class="userDatatable-content">';
                            html += '<div style="margin-bottom: 4px;">';
                            html += '<span>' + $('<div/>').text(data).html() + '</span>';
                            html += '</div>';
                            html += '<div>';
                            html += '<div><strong>{{ trans('report.email') }}:</strong> <span style="text-transform: lowercase;">' + row.email + '</span></div>';
                            html += '</div>';
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        data: 'earned_points',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            let spanClass = 'success';
                            if(data == 0) {
                                spanClass = 'primary';
                            } else if(data < 0) {
                                spanClass = 'danger';
                            }
                            return `<span class="badge badge-round badge-lg badge-${spanClass}" style="padding: 6px 10px; font-size: 12px;">${data}</span>`;
                        }
                    },
                    {
                        data: 'redeemed_points',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            let spanClass = 'success';
                            if(data == 0) {
                                spanClass = 'primary';
                            } else if(data < 0) {
                                spanClass = 'danger';
                            }
                            return `<span class="badge badge-round badge-lg badge-${spanClass}" style="padding: 6px 10px; font-size: 12px;">${data}</span>`;
                        }
                    },
                    {
                        data: 'points_spent',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            let spanClass = 'success';
                            if(data == 0) {
                                spanClass = 'primary';
                            } else if(data < 0) {
                                spanClass = 'danger';
                            }
                            return `<span class="badge badge-round badge-lg badge-${spanClass}" style="padding: 6px 10px; font-size: 12px;">${data}</span>`;
                        }
                    },
                    {
                        data: 'remaining_points',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            let spanClass = 'success';
                            if(data == 0) {
                                spanClass = 'primary';
                            } else if(data < 0) {
                                spanClass = 'danger';
                            }
                            return `<span class="badge badge-round badge-lg badge-${spanClass}" style="padding: 6px 10px; font-size: 12px;">${data}</span>`;
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

            $('#from-date, #to-date, #min-points').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search, #from-date, #to-date, #min-points').val('');
                table.ajax.reload();
            });

            initializeCharts();
        });

        function initializeCharts() {
            const primaryColor = '{{ config('branding.colors.primary') }}';
            const secondaryColor = '{{ config('branding.colors.secondary') }}';

            const ctxPoints = document.getElementById('pointsChart').getContext('2d');
            pointsChart = new Chart(ctxPoints, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Points Earned',
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

            const ctxDist = document.getElementById('distributionChart').getContext('2d');
            distributionChart = new Chart(ctxDist, {
                type: 'bar',
                data: {
                    labels: ['Low (<100)', 'Medium (100-999)', 'High (1000+)'],
                    datasets: [{
                        label: 'Users',
                        data: [0, 0, 0],
                        backgroundColor: [primaryColor, secondaryColor, '#28a745'],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: true, labels: { font: { size: 12, weight: 'bold' } } }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });
        }

        function updateChartsWithData(pointsDistribution, pointsTrend) {
            // Update points trend chart
            const dates = Object.keys(pointsTrend).sort();
            pointsChart.data.labels = dates;
            pointsChart.data.datasets[0].data = dates.map(date => pointsTrend[date]);
            pointsChart.update();

            // Update distribution chart with correct keys
            distributionChart.data.datasets[0].data = [
                pointsDistribution.low || 0,
                pointsDistribution.medium || 0,
                pointsDistribution.high || 0
            ];
            distributionChart.update();
        }
    </script>
@endpush
