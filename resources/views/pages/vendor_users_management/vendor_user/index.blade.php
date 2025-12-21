@extends('layout.app')
@section('title', trans('admin.vendor_users_management'))

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
                    ['title' => __('admin.vendor_users_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('admin.vendor_users_management') }}</h4>
                        <div class="d-flex gap-2">
                            @can('vendor-users.create')
                                <a href="{{ route('admin.admin-management.vendor-users.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ __('admin.add_vendor_user') }}
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
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
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

                                    {{-- Vendor Filter --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vendor_id" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-store me-1"></i>
                                                {{ trans('admin.vendor') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select select2"
                                                id="vendor_id">
                                                <option value="">{{ trans('admin.all_vendors') }}</option>
                                                @foreach (\Modules\Vendor\app\Models\Vendor::all() as $vendor)
                                                    <option value="{{ $vendor->id }}">
                                                        {{ $vendor->getTranslation('name', app()->getLocale()) }}</option>
                                                @endforeach
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

                                    <div class="col-md-12 d-flex">
                                        @can('vendor-users.index')
                                            <button type="button" id="exportExcel"
                                                class="btn btn-primary btn-default btn-squared me-1"
                                                title="{{ __('common.excel') }}">
                                                <i class="uil uil-file-download-alt me-1"></i> {{ __('common.export_excel') }}
                                            </button>
                                        @endcan
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
                        <table id="vendorUsersDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>

                                    @foreach ($languages as $language)
                                        <th>
                                            <span class="userDatatable-title"
                                                @if ($language->rtl) dir="rtl" @endif>
                                                {{ trans('admin.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach

                                    <th><span class="userDatatable-title">{{ trans('admin.email') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('admin.vendor') }}</span></th>
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
    <x-delete-modal modalId="modal-delete-vendor-user" :title="__('admin.confirm_delete')" :message="__('admin.delete_confirmation')"
        itemNameId="delete-vendor-user-name" confirmBtnId="confirmDeleteVendorUserBtn" :deleteRoute="route('admin.admin-management.vendor-users.index')" :cancelText="__('admin.cancel')"
        :deleteText="__('admin.delete_user')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            // Server-side processing with pagination
            let table = $('#vendorUsersDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.admin-management.vendor-users.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.active = $('#active').val();
                        d.vendor_id = $('#vendor_id').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        return d;
                    },
                    dataSrc: function(json) {
                        return json.data || [];
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: true
                    },
                    @foreach ($languages as $language)
                        {
                            data: 'names.{{ $language->id }}.value',
                            name: 'name_{{ $language->code }}',
                            orderable: true,
                            render: function(data, type, row) {
                                const nameData = row.names[{{ $language->id }}];
                                const rtlAttr = nameData.rtl ? ' dir="rtl"' : '';
                                return '<div class="userDatatable-content"' + rtlAttr + '>' + (
                                    nameData.value || '-') + '</div>';
                            }
                        },
                    @endforeach {
                        data: 'email',
                        name: 'email',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content text-lowercase">' + data +
                                '</div>';
                        }
                    },
                    {
                        data: 'vendor',
                        name: 'vendor',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
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
                        orderable: true,
                        render: function(data, type, row) {
                            const checked = data ? 'checked' : '';
                            const disabled =
                                @can('vendor-users.change-status')
                                    ''
                                @else
                                    'disabled'
                                @endcan ;
                            return `
                                <div class="userDatatable-content">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                            data-id="${row.id}" data-type="active" ${checked} ${disabled}>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'block',
                        name: 'block',
                        orderable: true,
                        render: function(data, type, row) {
                            const checked = data ? 'checked' : '';
                            const disabled =
                                @can('vendor-users.change-status')
                                    ''
                                @else
                                    'disabled'
                                @endcan ;
                            return `
                                <div class="userDatatable-content">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                            data-id="${row.id}" data-type="block" ${checked} ${disabled}>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
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
                                    @can('vendor-users.index')
                                    <a href="{{ route('admin.admin-management.vendor-users.index') }}/${row.id}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('vendor-users.edit')
                                    <a href="{{ route('admin.admin-management.vendor-users.index') }}/${row.id}/edit"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('vendor-users.delete')
                                    <a href="javascript:void(0);"
                                    class="remove delete-vendor-user btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-vendor-user"
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
                searching: true,
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('admin.no_users_found') ?? 'No users found' }}",
                    emptyTable: "{{ __('admin.no_users_found') ?? 'No users found' }}",
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

            // Status Toggle
            $(document).on('change', '.status-toggle', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                const status = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: `{{ route('admin.admin-management.vendor-users.index') }}/${id}/change-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                        type: type
                    },
                    success: function(response) {
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
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

            // Filters
            $('#active, #vendor_id').on('change', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#vendor_id').val('').trigger('change');
                $('#created_date_from').val('');
                table.search('').ajax.reload();
            });

            // Delete Modal
            let deleteItemId;
            $(document).on('click', '.delete-vendor-user', function() {
                deleteItemId = $(this).data('item-id');
                $('#delete-vendor-user-name').text($(this).data('item-name'));
            });

            $('#confirmDeleteVendorUserBtn').on('click', function() {
                $.ajax({
                    url: `{{ route('admin.admin-management.vendor-users.index') }}/${deleteItemId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#modal-delete-vendor-user').modal('hide');
                        toastr.success(response.message);
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endpush
