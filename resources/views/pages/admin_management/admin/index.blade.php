@extends('layout.app')
@section('title', trans('admin.admins_management'))

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
                    ['title' => __('admin.admins_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('admin.admins_management') }}</h4>
                        <div class="d-flex gap-2">
                            @can('admins.create')
                                <a href="{{ route('admin.admin-management.admins.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ __('admin.add_admin') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    {{-- Alert --}}
                    <div class="alert alert-info glowing-alert" role="alert">
                        {{ __('common.live_search_info') }}
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    {{-- Search --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ trans('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="{{ trans('admin.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('admin.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ trans('admin.all_status') }}</option>
                                                <option value="1">{{ trans('admin.active') }}</option>
                                                <option value="0">{{ trans('admin.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('common.created_date_from') }}
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
                                                {{ trans('common.created_date_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-align-center-alt me-1"></i>
                                                {{ trans('common.actions') }}
                                            </label>
                                            <button type="button" id="resetFilters"
                                                class="btn btn-warning btn-default btn-squared"
                                                title="{{ __('common.reset') }}">
                                                <i class="uil uil-redo me-1"></i> {{ __('common.reset_filters') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ trans('common.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ trans('common.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="adminsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ trans('admin.information') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('admin.email') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('admin.role') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('admin.active') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('admin.block') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('admin.created_at') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('common.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Delete Confirmation Modal Component --}}
    <x-delete-modal modalId="modal-delete-admin" :title="__('admin.confirm_delete')" :message="__('admin.delete_confirmation')" itemNameId="delete-admin-name"
        confirmBtnId="confirmDeleteAdminBtn" :deleteRoute="route('admin.admin-management.admins.index')" :cancelText="__('admin.cancel')" :deleteText="__('admin.delete_admin')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Admins page loaded, initializing DataTable...');

            let per_page = 10;

            // Server-side processing with pagination
            let table = $('#adminsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.admin-management.admins.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        if (d.order && d.order.length > 0) {
                            d.order_column = d.order[0].column;
                            d.order_dir = d.order[0].dir;
                        }
                        return d;
                    },
                    dataSrc: function(json) {
                        // Use the correct total from backend
                        json.recordsTotal = json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || 0;

                        if (json.error) {
                            console.error('❌ Server returned error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ DataTables AJAX Error:', {
                            xhr: xhr,
                            error: error,
                            code: code
                        });
                        alert('Error loading data. Status: ' + xhr.status);
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                    },
                    {
                        data: 'image',
                        name: 'information',
                        orderable: false,
                        render: function(data, type, row) {
                            let img;
                            if (data) {
                                img =
                                    `<img src="{{ asset('storage') }}/${data}" class="rounded-circle" style="width: 40px; height: 40px;">`;
                            } else {
                                img =
                                    `<div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="uil uil-user text-muted"></i></div>`;
                            }

                            let names = '';
                            Object.values(row.names).forEach(name => {
                                const badgeClass = name.code === 'ar' ? 'bg-info' :
                                    'bg-primary';
                                names += `
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge ${badgeClass} text-white px-1 py-0 me-1" style="font-size: 10px; text-transform: uppercase;">${name.code}</span>
                                        <div class="userDatatable-content" ${name.rtl ? 'dir="rtl"' : ''} style="font-size: 13px; line-height: 1.2;">${name.value || '-'}</div>
                                    </div>`;
                            });

                            return `
                                <div class="d-flex align-items-center gap-10">
                                    ${img}
                                    <div>${names}</div>
                                </div>`;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content text-lowercase">' + data +
                                '</div>';
                        }
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        render: function(data, type, row) {
                            const checked = data ? 'checked' : '';
                            @can('admins.change-status')
                            return `
                                <div class="userDatatable-content">
                                    <div class="form-check form-switch form-switch-primary">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                            data-id="${row.id}" data-type="active" ${checked}>
                                    </div>
                                </div>`;
                            @else
                            return data 
                                ? '<span class="badge badge-round bg-success">{{ trans("admin.active") }}</span>'
                                : '<span class="badge badge-round bg-danger">{{ trans("admin.inactive") }}</span>';
                            @endcan
                        }
                    },
                    {
                        data: 'block',
                        name: 'block',
                        orderable: false,
                        render: function(data, type, row) {
                            const checked = data ? 'checked' : '';
                            @can('admins.change-status')
                            return `
                                <div class="userDatatable-content">
                                    <div class="form-check form-switch form-switch-danger">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                            data-id="${row.id}" data-type="block" ${checked}>
                                    </div>
                                </div>`;
                            @else
                            return data 
                                ? '<span class="badge badge-round bg-danger">{{ trans("admin.blocked") }}</span>'
                                : '<span class="badge badge-round bg-success">{{ trans("admin.not_blocked") }}</span>';
                            @endcan
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
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
                                    @can('admins.show')
                                    <a href="{{ route('admin.admin-management.admins.index') }}/${row.id}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('admins.edit')
                                    <a href="{{ route('admin.admin-management.admins.index') }}/${row.id}/edit"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('admins.delete')
                                    <a href="javascript:void(0);"
                                    class="remove delete-admin btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-admin"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.display_name}"
                                    title="{{ trans('common.delete') }}">
                                        <i class="uil uil-trash-alt table_action_icon"></i>
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
                order: [
                    [0, 'desc']
                ],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ __('admin.admins_management') }}'
                }],
                searching: true,
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('admin.no_admins_found') ?? 'No admins found' }}",
                    emptyTable: "{{ __('admin.no_admins_found') ?? 'No admins found' }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
                    paginate: {
                        first: '{{ __('common.first') ?? 'First' }}',
                        last: '{{ __('common.last') ?? 'Last' }}',
                        next: '{{ __('common.next') ?? 'Next' }}',
                        previous: '{{ __('common.previous') ?? 'Previous' }}'
                    }
                }
            });

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();
                searchTimer = setTimeout(function() {
                    table.search(searchValue).draw();
                }, 500);
            });

            // Server-side filter event listeners
            $('#active, #created_date_from, #created_date_to').on('change', function() {
                table.ajax.reload();
            });

            // Handle Status Toggle
            $(document).on('change', '.status-toggle', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                const status = $(this).prop('checked') ? 1 : 0;
                const checkbox = $(this);

                // Disable during request
                checkbox.prop('disabled', true);

                $.ajax({
                    url: `{{ route('admin.admin-management.admins.index') }}/${id}/change-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                        type: type
                    },
                    success: function(response) {
                        checkbox.prop('disabled', false);
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            checkbox.prop('checked', !status); // Revert
                        }
                    },
                    error: function(xhr) {
                        checkbox.prop('disabled', false);
                        checkbox.prop('checked', !status); // Revert
                        const message = xhr.responseJSON ? xhr.responseJSON.message :
                            '{{ __('admin.error_occurred') }}';
                        toastr.error(message);
                    }
                });
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                table.search('').ajax.reload();
            });
        });
    </script>
@endpush
