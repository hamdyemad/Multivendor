@extends('layout.app')
@section('title', trans('systemsetting::points.user_points_management'))

@push('styles')
    <style>
        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f0f0f0;
        }

        .user-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }

        .user-email {
            font-size: 12px;
            color: #999;
        }

        .points-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            min-width: 80px;
        }

        .points-total {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .points-earned {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .points-redeemed {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .points-expired {
            background-color: #ffebee;
            color: #c62828;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .action-btn-points {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .action-btn-points:hover {
            background-color: #1976d2;
            color: white;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-input {
            flex: 1;
            min-width: 200px;
        }

        .filter-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-btn-search {
            background-color: var(--color-primary);
            color: white;
        }

        .filter-btn-search:hover {
            opacity: 0.9;
        }

        .filter-btn-reset {
            background-color: #e0e0e0;
            color: #333;
        }

        .filter-btn-reset:hover {
            background-color: #d0d0d0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('systemsetting::points.user_points_management'),
                    ],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('systemsetting::points.user_points_management') }}</h4>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    {{-- Search --}}
                                    <div class="col-md-4">
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

                                    <div class="col-md-12 d-flex align-items-center mt-3">
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

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table id="customerPointsTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th class="text-center"><span class="userDatatable-title">#</span></th>
                                        <th><span class="userDatatable-title">{{ trans('customer::customer.customer_information') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.earned_points') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.redeemed_points') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.adjusted_points') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.available_points') }}</span></th>
                                        <th class="text-center"><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            // Initialize DataTable
            let table = $('#customerPointsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.points-settings.user-points.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'customer_information',
                        name: 'customer_information',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data) return '<span class="text-muted">—</span>';

                            let html = '<div class="userDatatable-content">';
                            html += '<div style="margin-bottom: 4px;">';
                            html += '<span>' + $('<div/>').text(data.full_name).html() + '</span>';
                            html += '</div>';
                            html += '<div>';
                            html += '<div><strong>{{ trans('customer::customer.email') }}:</strong> <span style="text-transform: lowercase;">' + data.email + '</span></div>';
                            html += '<div><strong>{{ trans('customer::customer.phone') }}:</strong> ' + $('<div/>').text(data.phone).html() + '</div>';
                            html += '</div>';
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        data: 'earned_points',
                        name: 'earned_points',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
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
                        name: 'redeemed_points',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
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
                        data: 'adjusted_points',
                        name: 'adjusted_points',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
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
                        data: 'available_points',
                        name: 'available_points',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
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
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let viewUrl = "{{ route('admin.points-settings.user-points.transactions',':id') }}".replace(':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <a href="${viewUrl}"
                                       class="btn btn-info table_action_father"
                                       onclick="this.href = this.href.replace(':lang', '{{ app()->getLocale() }}').replace(':countryCode', '{{ session('country_code', 'EG') }}').replace(':id', ${row.id})"
                                       title="{{ trans('systemsetting::points.view_transactions') }}">
                                        <i class="uil uil-history table_action_icon"></i>
                                    </a>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'desc']],
                pagingType: 'full_numbers',
                language: {
                    search: '',
                    searchPlaceholder: "{{ __('common.search') }}...",
                    lengthMenu: '_MENU_',
                    info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.entries') }}",
                    infoEmpty: "{{ __('common.showing') }} 0 {{ __('common.to') }} 0 {{ __('common.of') }} 0 {{ __('common.entries') }}",
                    infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.total_entries') }})",
                    zeroRecords: "{{ __('common.no_matching_records_found') }}",
                    emptyTable: "{{ __('common.no_data_available') }}",
                    paginate: {
                        @if(app()->getLocale() == 'en')
                            first: '<i class="uil uil-angle-double-left"></i>',
                            last: '<i class="uil uil-angle-double-right"></i>',
                            next: '<i class="uil uil-angle-right"></i>',
                            previous: '<i class="uil uil-angle-left"></i>'
                        @else
                            first: '<i class="uil uil-angle-double-right"></i>',
                            last: '<i class="uil uil-angle-double-left"></i>',
                            next: '<i class="uil uil-angle-left"></i>',
                            previous: '<i class="uil uil-angle-right"></i>'
                        @endif
                    }
                },
                dom: '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-bordered');
                }
            });

            // Handle entries per page change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                table.draw();
            });

            // Reset filters button click
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                table.draw();
            });

            // View points details
            window.viewPointsDetails = function(id) {
                fetch('{{ route("admin.points-settings.user-points.show", ":id") }}'.replace(':id', id), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const html = `
                            <div class="mb-3">
                                <h6 class="fw-bold">${$('<div/>').text(data.data.user_name).html()}</h6>
                                <small class="text-muted">${$('<div/>').text(data.data.user_email).html()}</small>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-center">
                                        <div class="badge badge-round badge-lg bg-success w-100" style="padding: 10px; font-size: 14px;">${data.data.earned_points}</div>
                                        <small class="d-block mt-2">{{ trans('systemsetting::points.earned_points') }}</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-center">
                                        <div class="badge badge-round badge-lg bg-warning w-100" style="padding: 10px; font-size: 14px;">${data.data.redeemed_points}</div>
                                        <small class="d-block mt-2">{{ trans('systemsetting::points.redeemed_points') }}</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-center">
                                        <div class="badge badge-round badge-lg bg-info w-100" style="padding: 10px; font-size: 14px;">${data.data.adjusted_points}</div>
                                        <small class="d-block mt-2">{{ trans('systemsetting::points.adjusted_points') }}</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        <strong>{{ trans('systemsetting::points.available_points') }}:</strong> ${data.data.available_points}
                                    </div>
                                </div>
                            </div>
                        `;

                        document.getElementById('pointsDetailsContent').innerHTML = html;
                        new bootstrap.Modal(document.getElementById('pointsDetailsModal')).show();
                    } else {
                        toastr.error(data.message || '{{ trans("common.error_occurred") }}');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('{{ trans("common.error_occurred") }}');
                });
            };
        });
    </script>
@endpush
