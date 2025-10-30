@extends('layout.app')

@push('styles')
<!-- Select2 CSS loaded via Vite -->
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('menu.admin managment.roles managment')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('roles.roles_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.admin-management.roles.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('roles.create_role') }}
                            </a>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                {{ __('common.search') }} 
                                                <small class="text-muted">({{ __('common.real_time') ?? 'Real-time' }})</small>
                                            </label>
                                            <input type="text" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                   id="search" 
                                                   placeholder="{{ trans('roles.search_placeholder') }}"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_date_from') }}</label>
                                            <input type="date" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                   id="created_date_from">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_date_to') }}</label>
                                            <input type="date" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                   id="created_date_to">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="d-flex align-items-start align-items-start m-0">
                                                <button type="button" id="exportExcel" class="btn btn-primary btn-default btn-squared me-2" title="{{ __('common.excel') }}">
                                                    <i class="uil uil-file-download-alt m-0"></i>
                                                </button>
                                                <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared" title="{{ __('common.reset') ?? 'Reset' }}">
                                                    <i class="uil uil-redo m-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page Selector --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label class="d-inline-flex align-items-center mb-0">
                                {{ __('common.show') ?? 'Show' }}
                                <select id="entriesSelect" class="form-select form-select-sm mx-2" style="width: auto;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                {{ __('common.entries') ?? 'entries' }}
                            </label>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="rolesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    @foreach($languages as $language)
                                        <th>
                                            <span class="userDatatable-title" @if($language->rtl) dir="rtl" @endif>
                                                {{ trans('roles.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th>
                                        <span class="userDatatable-title">{{ trans('roles.permissions') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('roles.created_at') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('common.actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Delete Confirmation Modal Component --}}
    <x-delete-modal 
        modalId="modal-delete-role"
        :title="trans('roles.confirm_delete')"
        :message="trans('roles.delete_warning')"
        itemNameId="delete-role-name"
        confirmBtnId="confirmDeleteRoleBtn"
        :deleteRoute="route('admin.admin-management.roles.index')"
        :cancelText="trans('roles.cancel')"
        :deleteText="trans('roles.delete_role')"
    />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Roles page loaded, initializing DataTable...');
    
    let per_page = 10;

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
                console.error('❌ DataTables AJAX Error:', {xhr: xhr, error: error, code: code});
                console.error('Response Status:', xhr.status);
                console.error('Response Text:', xhr.responseText);
                alert('Error loading data. Status: ' + xhr.status + '. Check console for details.');
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
            @foreach($languages as $language)
            { 
                data: 'translations.{{ $language->code }}.name',
                name: 'name_{{ $language->code }}',
                orderable: true,
                render: function(data, type, row) {
                    // For sorting, return the raw text value
                    if (type === 'sort' || type === 'type') {
                        return row.translations && row.translations['{{ $language->code }}'] ? 
                               row.translations['{{ $language->code }}'].name : '-';
                    }
                    
                    // For display, return formatted HTML
                    if (row.translations && row.translations['{{ $language->code }}']) {
                        const translation = row.translations['{{ $language->code }}'];
                        const name = translation.name || '-';
                        if (translation.rtl) {
                            return '<div class="userDatatable-content" dir="rtl"><strong>' + $('<div>').text(name).html() + '</strong></div>';
                        }
                        return '<div class="userDatatable-content"><strong>' + $('<div>').text(name).html() + '</strong></div>';
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
                    return '<div class="userDatatable-content"><span class="badge badge-primary" style="border-radius: 6px; padding: 6px 12px;"><i class="uil uil-shield-check me-1"></i>' + data + ' {{ trans('roles.permissions') }}</span></div>';
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
                    return `
                        <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                            <li>
                                <a href="{{ url('admin/admin-management/roles') }}/${row.id}" class="view" title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('admin/admin-management/roles') }}/${row.id}/edit" class="edit" title="{{ trans('common.edit') }}">
                                    <i class="uil uil-edit"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" 
                                   class="remove" 
                                   title="{{ trans('common.delete') }}"
                                   data-bs-toggle="modal" 
                                   data-bs-target="#modal-delete-role"
                                   data-item-id="${row.id}"
                                   data-item-name="${$('<div>').text(row.name).html()}">
                                    <i class="uil uil-trash-alt"></i>
                                </a>
                            </li>
                        </ul>`;
                }
            }
        ],
        pageLength: per_page,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        pagingType: 'full_numbers',
        dom: '<"row"<"col-sm-12"tr>>' +
             '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                title: '{{ trans("roles.roles_management") }}'
            }
        ],
        searching: true, // Enable built-in search
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

    // Search on cached data with debounce
    let searchTimer;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimer);
        const searchValue = $(this).val();
        searchTimer = setTimeout(function() {
            table.search(searchValue).draw(); // Search on cached data
        }, 500);
    });
    
    $('#search').on('change', function() {
        clearTimeout(searchTimer);
        table.search($(this).val()).draw();
    });

    // Server-side filter event listeners - reload data when filters change
    $('#created_date_from, #created_date_to').on('change', function() {
        console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
        table.ajax.reload();
    });

    // Reset filters button
    $('#resetFilters').on('click', function() {
        console.log('Resetting all filters...');
        // Clear all filter inputs
        $('#search').val('');
        $('#created_date_from').val('');
        $('#created_date_to').val('');
        // Clear search and reload table
        table.search('').ajax.reload();
    });
});
</script>
@endpush
