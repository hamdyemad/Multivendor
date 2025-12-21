@extends('layout.app')
@section('title', trans('roles.roles_management'))

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
                    ['title' => trans('menu.admin managment.roles managment')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('roles.roles_management') }}</h4>
                        @can('roles.create')
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.admin-management.roles.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ trans('roles.create_role') }}
                                </a>
                            </div>
                        @endcan
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

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ trans('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="{{ trans('roles.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
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

                                    <div class="col-md-4">
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
                        <table id="rolesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    @foreach ($languages as $language)
                                        <th>
                                            <span class="userDatatable-title"
                                                @if ($language->rtl) dir="rtl" @endif>
                                                {{ trans('roles.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th><span class="userDatatable-title">{{ trans('roles.permissions') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('roles.type') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('roles.created_at') }}</span></th>
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
    <x-delete-modal modalId="modal-delete-role" :title="trans('roles.confirm_delete')" :message="trans('roles.delete_warning')" itemNameId="delete-role-name"
        confirmBtnId="confirmDeleteRoleBtn" :deleteRoute="route('admin.admin-management.roles.index')" :cancelText="trans('roles.cancel')" :deleteText="trans('roles.delete_role')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Roles page loaded, initializing DataTable...');

            let per_page = 10;

            // Get filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Populate filters from URL parameters on page load
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
            if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

            // Function to update URL with current filters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();

                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#created_date_from').val()) params.set('created_date_from', $('#created_date_from').val());
                if ($('#created_date_to').val()) params.set('created_date_to', $('#created_date_to').val());

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }

            // Server-side processing with pagination
            let table = $('#rolesDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '{{ route('admin.admin-management.roles.data') }}',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        // Add filter parameters
                        d.search = $('#search').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.orderColumnIndex = d.order[0].column;
                            d.orderDirection = d.order[0].dir;
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
                columns: [
                    // Row Number column
                    {
                        data: 'row_number',
                        name: 'id',
                        orderable: true,
                        render: function(data) {
                            return data;
                        }
                    },
                    // Name columns for each language
                    @foreach ($languages as $language)
                        {
                            data: 'translations.{{ $language->code }}.name',
                            name: 'name_{{ $language->code }}',
                            orderable: true,
                            render: function(data, type, row) {
                                // For sorting, return the raw text value
                                if (type === 'sort' || type === 'type') {
                                    return row.translations && row.translations[
                                            '{{ $language->code }}'] ?
                                        row.translations['{{ $language->code }}'].name : '-';
                                }

                                // For display, return formatted HTML
                                if (row.translations && row.translations[
                                        '{{ $language->code }}']) {
                                    const translation = row.translations['{{ $language->code }}'];
                                    const name = translation.name || '-';
                                    if (translation.rtl) {
                                        return '<div class="userDatatable-content" dir="rtl">' +
                                            $('<div>').text(name).html() + '</div>';
                                    }
                                    return '<div class="userDatatable-content">' + $(
                                        '<div>').text(name).html() + '</div>';
                                }
                                return '-';
                            }
                        },
                    @endforeach
                    // Permissions column
                    {
                        data: 'permissions_count',
                        name: 'permissions',
                        orderable: false,
                        render: function(data) {
                            return '<div class="userDatatable-content"><span class="badge badge-primary" style="border-radius: 6px; padding: 6px 12px;"><i class="uil uil-shield-check me-1"></i>' +
                                data + ' {{ trans('roles.permissions') }}</span></div>';
                        }
                    },
                    // Type column
                    {
                        data: 'type',
                        name: 'type',
                        orderable: true,
                        render: function(data) {
                            const typeColors = {
                                'super_admin': 'danger',
                                'admin': 'primary',
                                'vendor': 'success',
                                'vendor_user': 'info',
                                'other': 'secondary'
                            };
                            const typeLabels = {
                                'super_admin': 'Super Admin',
                                'admin': 'Admin',
                                'vendor': 'Vendor',
                                'vendor_user': 'Vendor User',
                                'other': 'Other'
                            };
                            const badgeColor = typeColors[data] || 'secondary';
                            const label = typeLabels[data] || data;
                            return '<div class="userDatatable-content"><span class="badge badge-' +
                                badgeColor +
                                '" style="border-radius: 6px; padding: 6px 12px;"><i class="uil uil-tag-alt me-1"></i>' +
                                label + '</span></div>';
                        }
                    },
                    // Created At column
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        render: function(data) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    // Actions column
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let viewUrl = "{{ route('admin.admin-management.roles.show', ':id') }}"
                                .replace(':id', row.id);
                            let editUrl = "{{ route('admin.admin-management.roles.edit', ':id') }}"
                                .replace(':id', row.id);

                            let actions =
                                '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';

                            // View
                            @can('roles.show')
                                actions +=
                                    `<a href="${viewUrl}" class="view btn btn-primary table_action_father" title="{{ trans('common.view') }}"><i class="uil uil-eye table_action_icon"></i></a>`;
                            @endcan


                            // Delete - Only if NOT system protected
                            if (!row.is_system_protected && row.is_system_protected != 1) {
                                // Edit
                                @can('roles.edit')
                                    actions +=
                                        `<a href="${editUrl}" class="edit btn btn-warning table_action_father" title="{{ __('common.edit') }}"><i class="uil uil-edit table_action_icon"></i></a>`;
                                @endcan
                                @can('roles.delete')
                                    actions +=
                                        `<a href="javascript:void(0);" class="remove delete-role btn btn-danger table_action_father" data-bs-toggle="modal" data-bs-target="#modal-delete-role" data-item-id="${row.id}" data-item-name="${row.name}" title="{{ __('common.delete') }}"><i class="uil uil-trash-alt table_action_icon"></i></a>`;
                                @endcan
                            }

                            actions += '</div>';
                            return actions;
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
                    title: '{{ __('roles.roles_management') }}'
                }],
                searching: false, // Disable built-in search (using custom)
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ trans('roles.no_roles_found') }}",
                    emptyTable: "{{ trans('roles.no_roles_found') }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
                    paginate: {
                        first: '{{ __('common.first') ?? 'First' }}',
                        last: '{{ __('common.last') ?? 'Last' }}',
                        next: '{{ __('common.next') ?? 'Next' }}',
                        previous: '{{ __('common.previous') ?? 'Previous' }}'
                    },
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
                }
            });

            // Initialize Select2 on custom entries select
            if ($.fn.select2) {
                $('#entriesSelect').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            } else {
                console.error('Select2 is not loaded');
            }

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Real-time search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    updateUrlWithFilters();
                    table.ajax.reload();
                }, 500);
            });

            // Search button click handler
            $('#searchBtn').on('click', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Date filter change handlers
            $('#created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Update URL and reload table
                updateUrlWithFilters();
                table.ajax.reload();
            });
        });
    </script>
@endpush
