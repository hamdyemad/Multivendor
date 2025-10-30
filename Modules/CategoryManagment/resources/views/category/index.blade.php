@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('categorymanagment::category.categories_management')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('categorymanagment::category.categories_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.category-management.categories.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('categorymanagment::category.add_category') }}
                            </a>
                        </div>
                    </div>

                    {{-- Search and Filter --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="search" class="il-gray fs-14 fw-500 mb-10">{{ trans('common.search') }}</label>
                                                <input type="text" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="search" 
                                                       placeholder="{{ trans('categorymanagment::category.search_by_name') }}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="active" class="il-gray fs-14 fw-500 mb-10">{{ trans('categorymanagment::category.activation') }}</label>
                                                <select class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                        id="active">
                                                    <option value="">{{ trans('categorymanagment::category.all') }}</option>
                                                    <option value="1">{{ trans('categorymanagment::category.active') }}</option>
                                                    <option value="0">{{ trans('categorymanagment::category.inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_date_from') }}</label>
                                                <input type="date" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="created_date_from">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_date_to') }}</label>
                                                <input type="date" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="created_date_to">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <div class="form-group me-2">
                                                <label class="il-gray fs-14 fw-500 mb-10">&nbsp;</label>
                                                <button type="button" id="exportExcel" class="btn btn-primary btn-default btn-squared" title="{{ __('common.excel') }}">
                                                    <i class="uil uil-file-download-alt m-0"></i>
                                                </button>
                                            </div>
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10">&nbsp;</label>
                                                <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared" title="{{ __('common.reset') ?? 'Reset' }}">
                                                    <i class="uil uil-redo m-0"></i>
                                                </button>
                                            </div>
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
                        <table id="categoriesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    @foreach($languages as $language)
                                        <th>
                                            <span class="userDatatable-title" @if($language->rtl) dir="rtl" @endif>
                                                {{ trans('categorymanagment::category.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th>
                                        <span class="userDatatable-title">{{ trans('categorymanagment::category.department') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('categorymanagment::category.activation') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('categorymanagment::category.created_at') }}</span>
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
    <x-delete-with-loading
        modalId="modal-delete-category"
        tableId="categoriesDataTable"
        deleteButtonClass="delete-category"
        :title="__('main.confirm delete')"
        :message="__('main.are you sure you want to delete this')"
        itemNameId="delete-category-name"
        confirmBtnId="confirmDeleteCategoryBtn"
        :cancelText="__('main.cancel')"
        :deleteText="__('main.delete')"
        :loadingDeleting="trans('main.deleting') ?? 'Deleting...'"
        :loadingPleaseWait="trans('main.please wait') ?? 'Please wait...'"
        :loadingDeletedSuccessfully="trans('main.deleted success') ?? 'Deleted Successfully!'"
        :loadingRefreshing="trans('main.refreshing') ?? 'Refreshing...'"
        :errorDeleting="__('main.error on delete')"
    />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        let per_page = 10;

        // Server-side processing with pagination
        var table = $('#categoriesDataTable').DataTable({
            processing: true,
            serverSide: true, // Server-side processing
            ajax: {
                url: '{{ route('admin.category-management.categories.datatable') }}',
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
                    
                    console.log('📤 Sending to server:', {
                        search: d.search,
                        active: d.active,
                        created_date_from: d.created_date_from,
                        created_date_to: d.created_date_to
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
                { data: 'id', name: 'id' }, // #
                @foreach($languages as $language)
                {
                    data: 'translations.{{ $language->code }}.name',
                    name: 'name_{{ $language->code }}',
                    render: function(data, type, row) {
                        if (!data) return '-';
                        @if($language->rtl)
                        return '<span dir="rtl">' + data + '</span>';
                        @else
                        return data;
                        @endif
                    }
                },
                @endforeach
                {
                    data: 'department',
                    name: 'department',
                    render: function(data) {
                        if (data && data.name) {
                            return '<span class="badge badge-info badge-round badge-lg">' + data.name + '</span>';
                        }
                        return '-';
                    }
                },
                {
                    data: 'active',
                    name: 'active',
                    render: function(data) {
                        if (data) {
                            return '<span class="badge badge-success badge-round badge-lg">{{ trans('categorymanagment::category.active') }}</span>';
                        } else {
                            return '<span class="badge badge-danger badge-round badge-lg">{{ trans('categorymanagment::category.inactive') }}</span>';
                        }
                    }
                },
                { data: 'created_at', name: 'created_at' },
                {
                    data: null,
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const showUrl = '{{ url("admin/category-management/categories") }}/' + data.id;
                        const editUrl = '{{ url("admin/category-management/categories") }}/' + data.id + '/edit';
                        const destroyUrl = '{{ url("admin/category-management/categories") }}/' + data.id;
                        
                        return `
                            <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                <li>
                                    <a href="${showUrl}" 
                                       class="view" 
                                       title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="${editUrl}" 
                                       class="edit" 
                                       title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" 
                                       class="remove delete-category" 
                                       title="{{ trans('common.delete') }}"
                                       data-bs-toggle="modal" 
                                       data-bs-target="#modal-delete-category"
                                       data-item-id="${row.id}"
                                       data-item-name="${row.first_name || ''}"
                                       data-url="${destroyUrl}">
                                        <i class="uil uil-trash-alt"></i>
                                    </a>
                                </li>
                            </ul>
                        `;
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
                    title: '{{ trans('categorymanagment::category.categories_management') }}'
                }
            ],
            searching: true, // Enable built-in search
            language: {
                lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                zeroRecords: "{{ trans('categorymanagment::category.no_categories_found') ?? 'No categories found' }}",
                emptyTable: "{{ trans('categorymanagment::category.no_categories_found') ?? 'No categories found' }}",
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
                // Only apply to categories table
                if (settings.nTable.id !== 'categoriesDataTable') {
                    return true;
                }
                
                var activeFilter = $('#active').val();
                var dateFrom = $('#created_date_from').val();
                var dateTo = $('#created_date_to').val();
                
                // Active filter (column {{ count($languages) + 2 }})
                if (activeFilter && activeFilter !== '') {
                    var colIndex = {{ count($languages) + 2 }};
                    
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
                
                // Date filters (column {{ count($languages) + 3 }})
                if (dateFrom || dateTo) {
                    var dateColumn = data[{{ count($languages) + 3 }}];
                    if (dateColumn) {
                        var rowDate = dateColumn.replace(/<[^>]*>/g, '').trim().split(' ')[0]; // Extract YYYY-MM-DD
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
            // Clear search and reload table
            table.search('').ajax.reload();
        });
        
        // Delete functionality is now handled by the delete-with-loading component
    });
</script>
@endpush
