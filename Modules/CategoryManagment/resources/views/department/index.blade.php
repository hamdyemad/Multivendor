@extends('layout.app')

@section('title')
Departments | Bnaia
@endsection
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
                    ['title' => trans('categorymanagment::department.departments_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('categorymanagment::department.departments_management') }}</h4>
                        @can('departments.create')
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.category-management.departments.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ trans('categorymanagment::department.add_department') }}
                                </a>
                            </div>
                        @endcan
                    </div>
                    <div class="alert alert-info glowing-alert" role="alert">
                        {{ __('common.live_search_info') }}
                    </div>

                    {{-- Search and Filter --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search"
                                                class="il-gray fs-14 fw-500 mb-10">{{ trans('common.search') }}</label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ trans('categorymanagment::department.search_by_name_or_code') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active"
                                                class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::department.activation') }}</label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ trans('categorymanagment::department.all') }}
                                                </option>
                                                <option value="1">{{ trans('categorymanagment::department.active') }}
                                                </option>
                                                <option value="0">{{ trans('categorymanagment::department.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from"
                                                class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_date_from') }}</label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to"
                                                class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_date_to') }}</label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>
                                    <div class="col-md-12 d-flex">
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared me-1"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i> {{ __('common.export_excel') }}
                                        </button>
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

                    {{-- Entries Per Page Selector --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('common.show') ?? 'Show' }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('common.entries') ?? 'entries' }}</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="departmentsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    @foreach ($languages as $language)
                                        <th>
                                            <span class="userDatatable-title"
                                                @if ($language->rtl) dir="rtl" @endif>
                                                {{ trans('categorymanagment::department.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th>
                                        <span
                                            class="userDatatable-title">{{ trans('categorymanagment::department.activation') }}</span>
                                    </th>
                                    <th>
                                        <span
                                            class="userDatatable-title">{{ trans('categorymanagment::department.created_at') }}</span>
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
    {{-- Delete Confirmation Modal with Loading Component --}}
    <x-delete-with-loading modalId="modal-delete-department" tableId="departmentsDataTable"
        deleteButtonClass="delete-department" :title="__('main.confirm delete')" :message="__('main.are you sure you want to delete this')" itemNameId="delete-department-name"
        confirmBtnId="confirmDeleteDepartmentBtn" :cancelText="__('main.cancel')" :deleteText="__('main.delete')" :loadingDeleting="trans('main.deleting') ?? 'Deleting...'" :loadingPleaseWait="trans('main.please wait') ?? 'Please wait...'"
        :loadingDeletedSuccessfully="trans('main.deleted success') ?? 'Deleted Successfully!'" :loadingRefreshing="trans('main.refreshing') ?? 'Refreshing...'" :errorDeleting="__('main.error on delete')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            // Server-side processing with pagination
            var table = $('#departmentsDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '{{ route('admin.category-management.departments.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;

                        // Add search parameter from custom input
                        d.search = $('#search').val();

                        // Add filter parameters
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.orderColumnIndex = d.order[0].column;
                            d.orderDirection = d.order[0].dir;
                        }

                        console.log('📤 Sending to server:', {
                            search: d.search,
                            active: d.active,
                            created_date_from: d.created_date_from,
                            created_date_to: d.created_date_to,
                            orderColumnIndex: d.orderColumnIndex,
                            orderDirection: d.orderDirection
                        });

                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('📦 Data received from server:', json);
                        // Map backend response to DataTables format
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.log('DataTables Error:', xhr, error, code);
                        alert('Error loading data. Please check console for details.');
                    }
                },
                columns: [
                    // ID column
                    {
                        data: 'id',
                        name: 'id',
                        render: function(data) {
                            return data;
                        }
                    },
                    // Name columns for each language
                    @foreach ($languages as $language)
                        {
                            data: 'translations.{{ $language->code }}.name',
                            name: 'name_{{ $language->code }}',
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
                                        return '<span dir="rtl">' + $('<div>').text(name).html() +
                                            '</span>';
                                    }
                                    return $('<div>').text(name).html();
                                }
                                return '-';
                            }
                        },
                    @endforeach
                    // Active Status column
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data) {
                            if (data == 1) {
                                return '<span class="badge badge-success badge-round badge-lg">{{ trans('categorymanagment::department.active') }}</span>';
                            } else {
                                return '<span class="badge badge-danger badge-round badge-lg">{{ trans('categorymanagment::department.inactive') }}</span>';
                            }
                        }
                    },
                    // Created At column
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data;
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
                            <ul class="mb-0 d-flex flex-wrap justify-content-start">
                                @can('departments.view')
                                <li>
                                    <a href="{{ url('admin/category-management/departments') }}/${row.department_id}"
                                    class="btn btn-primary table_action_father me-1"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                </li>
                                @endcan
                                @can('departments.edit')
                                <li>
                                    <a href="{{ url('admin/category-management/departments') }}/${row.department_id}/edit"
                                    class="btn btn-warning table_action_father me-1"
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                </li>
                                @endcan
                                @can('departments.delete')
                                <li>
                                    <a href="javascript:void(0);"
                                    class="btn btn-danger delete-department table_action_father"
                                    title="{{ trans('common.delete') }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-department"
                                    data-item-id="${row.department_id}"
                                    data-item-name="${$('<div>').text(row.first_name).html()}"
                                    data-url="{{ url('admin/category-management/departments') }}/${row.department_id}">
                                        <i class="uil uil-trash-alt table_action_icon"></i>
                                    </a>
                                </li>
                                @endcan
                            </ul>`;
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
                    title: '{{ trans('categorymanagment::department.departments_management') }}'
                }],
                searching: true, // Enable built-in search
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ trans('categorymanagment::department.no_departments_found') ?? 'No departments found' }}",
                    emptyTable: "{{ trans('categorymanagment::department.no_departments_found') ?? 'No departments found' }}",
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
                    width: 'auto'
                });
            }

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Search with server-side processing and debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    console.log('🔍 Search triggered:', $('#search').val());
                    table.ajax.reload(); // Reload data from server with new search value
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
                console.log('🔍 Search changed:', $(this).val());
                table.ajax.reload();
            });

            // Custom filter function for active status and dates on cached data
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Only apply to departments table
                    if (settings.nTable.id !== 'departmentsDataTable') {
                        return true;
                    }

                    var activeFilter = $('#active').val();
                    var dateFrom = $('#created_date_from').val();
                    var dateTo = $('#created_date_to').val();

                    // Active filter (column {{ count($languages) + 1 }})
                    if (activeFilter && activeFilter !== '') {
                        var colIndex = {{ count($languages) + 1 }};

                        // Get the actual rendered cell content (with HTML)
                        var rowNode = table.row(dataIndex).node();
                        if (!rowNode) {
                            return true;
                        }

                        var cells = $(rowNode).find('td');
                        if (cells.length <= colIndex) {
                            return true;
                        }

                        // Get the HTML content of the cell
                        var cellHtml = $(cells[colIndex]).html();

                        if (!cellHtml) {
                            return true;
                        }

                        // Check if the cell contains the success badge (active) or danger badge (inactive)
                        var isActiveRow = cellHtml.indexOf('badge-success') > -1;
                        var isInactiveRow = cellHtml.indexOf('badge-danger') > -1;

                        // Filter logic
                        if (activeFilter === '1') {
                            // Show only active rows (must have badge-success)
                            return isActiveRow;
                        } else if (activeFilter === '0') {
                            // Show only inactive rows (must have badge-danger)
                            return isInactiveRow;
                        }
                    }

                    // Date filters (column {{ count($languages) + 2 }})
                    if (dateFrom || dateTo) {
                        var dateColumn = data[{{ count($languages) + 2 }}];
                        if (dateColumn) {
                            var rowDate = dateColumn.replace(/<[^>]*>/g, '').trim().split(' ')[
                                0]; // Extract YYYY-MM-DD
                            if (dateFrom && rowDate < dateFrom) return false;
                            if (dateTo && rowDate > dateTo) return false;
                        }
                    }

                    return true;
                }
            );

            // Server-side filter event listeners - reload data when filters change
            $('#active, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#active').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Reload table with cleared filters
                table.ajax.reload();
            });

            // Delete functionality is now handled by the delete-with-loading component
        });
    </script>
@endpush
