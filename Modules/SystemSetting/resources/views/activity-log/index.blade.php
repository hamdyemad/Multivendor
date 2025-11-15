@extends('layout.app')
@section('title')
    {{ __('systemsetting::activity_log.activity_logs') }} | E-RAMO
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
                    ['title' => __('systemsetting::activity_log.activity_logs_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('systemsetting::activity_log.activity_logs_management') }}</h4>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info glowing-alert" role="alert">
                        <i class="uil uil-lightbulb-alt me-1"></i>
                        {{ __('common.live_search_info') }}
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
                                                placeholder="{{ __('systemsetting::activity_log.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Action Filter --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="action" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-bolt me-1"></i>
                                                {{ __('systemsetting::activity_log.action') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="action">
                                                <option value="">{{ __('systemsetting::activity_log.all_actions') }}</option>
                                                <option value="created">{{ __('activity_log.actions.created') }}</option>
                                                <option value="updated">{{ __('activity_log.actions.updated') }}</option>
                                                <option value="deleted">{{ __('activity_log.actions.deleted') }}</option>
                                                <option value="login">{{ __('activity_log.actions.login') }}</option>
                                                <option value="logout">{{ __('activity_log.actions.logout') }}</option>
                                                <option value="restored">{{ __('activity_log.actions.restored') }}</option>
                                                <option value="force_deleted">{{ __('activity_log.actions.force_deleted') }}</option>
                                                <option value="password_reset_requested">{{ __('activity_log.actions.password_reset_requested') }}</option>
                                                <option value="password_reset_success">{{ __('activity_log.actions.password_reset_success') }}</option>
                                                <option value="password_reset_failed">{{ __('activity_log.actions.password_reset_failed') }}</option>

                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    {{-- Created Date To --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared me-1"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i> {{ __('common.export_excel') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared"
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
                        <table id="activityLogsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::activity_log.user') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::activity_log.action') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::activity_log.model') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::activity_log.description') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::activity_log.ip_address') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.created_at') }}</span></th>
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
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Activity logs page loaded, initializing DataTable...');

            let per_page = 10;

            // Server-side processing with pagination
            let table = $('#activityLogsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.system-settings.activity-logs.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        // Add filter parameters
                        d.action = $('#action').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.order_column = d.order[0].column;
                            d.order_dir = d.order[0].dir;
                        }
                        console.log('📤 Sending request:', d);
                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('📦 Data received from server:', json);
                        console.log('Total records:', json.total);
                        console.log('Filtered records:', json.recordsFiltered);
                        console.log('Current page:', json.current_page);

                        // Map backend response to DataTables format
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;

                        if (json.error) {
                            console.error('❌ Server returned error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        if (!json.data || json.data.length === 0) {
                            console.warn('⚠️ No data returned from server');
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ DataTables AJAX Error:', {
                            xhr: xhr,
                            error: error,
                            code: code
                        });
                        console.error('Response Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);
                        alert('Error loading data. Status: ' + xhr.status +
                            '. Check console for details.');
                    }
                },
                columns: [{
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return '<div class="userDatatable-content">' + (meta.row + 1) + '</div>';
                        }
                    },
                    {
                        data: 'user_name',
                        name: 'user_name',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + (data || '-') + '</div>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        render: function(data, type, row) {
                            const colors = {
                                'Created': 'success',
                                'Updated': 'warning',
                                'Deleted': 'danger',
                                'Viewed': 'info'
                            };
                            const actionColor = colors[data] || 'secondary';
                            return '<div class="userDatatable-content"><span class="badge badge-' + actionColor + ' badge-lg badge-round">' + data + '</span></div>';
                        }
                    },
                    {
                        data: 'model',
                        name: 'model',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + (data || '-') + '</div>';
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content" title="' + (data || '-') + '">' + (data ? (data.length > 50 ? data.substring(0, 50) + '...' : data) : '-') + '</div>';
                        }
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content"><code>' + (data || '-') + '</code></div>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content"><small>' + data + '</small></div>';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    @can('settings.logs.view')
                                    <a href="{{ url('admin/system-settings/activity-logs') }}/${row.id}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
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
                dom: '<"top"<"row"<"col-sm-6"<"dataTables_info"i>><"col-sm-6"<"dataTables_paginate paging_simple_numbers"p>>>>rt<"bottom"<"row"<"col-sm-6"<"dataTables_info"i>><"col-sm-6"<"dataTables_paginate paging_simple_numbers"p>>>>',
                language: {
                    "lengthMenu": "{{ trans('common.show') }} _MENU_ {{ trans('common.entries') }}",
                    "info": "{{ trans('common.showing') }} _START_ {{ trans('common.to') }} _END_ {{ trans('common.of') }} _TOTAL_",
                    "search": "{{ trans('common.search') }}:",
                    "paginate": {
                        "first": "{{ trans('common.first') }}",
                        "last": "{{ trans('common.last') }}",
                        "next": "{{ trans('common.next') }}",
                        "previous": "{{ trans('common.previous') }}"
                    }
                }
            });

            // Filter events
            $('#search, #action, #created_date_from, #created_date_to').on('change keyup', function() {
                table.draw();
            });

            // Reset filters
            $('#resetFilters').click(function() {
                $('#search').val('');
                $('#action').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                table.draw();
            });

            // Entries per page
            $('#entriesSelect').on('change', function() {
                per_page = $(this).val();
                table.page.len(per_page).draw();
            });

            // Export Excel
            $('#exportExcel').click(function() {
                alert('{{ trans('common.export_coming_soon') }}');
            });
        });
    </script>
@endpush
