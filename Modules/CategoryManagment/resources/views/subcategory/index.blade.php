@extends('layout.app')
@section('title', trans('categorymanagment::subcategory.subcategories_management'))

@push('styles')
<style>
    /* Drag and Drop Styles */
    #subcategoriesDataTable tbody tr {
        cursor: default;
    }
    #subcategoriesDataTable tbody tr.ui-sortable-helper {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        cursor: grabbing;
    }
    #subcategoriesDataTable tbody tr.ui-sortable-placeholder {
        border: 2px dashed #2196f3 !important;
        visibility: visible !important;
        height: 50px;
    }
    .drag-handle {
        cursor: grab;
        color: #6c757d;
        padding: 10px 15px;
        font-size: 18px;
        display: block;
        width: 100%;
        height: 100%;
    }
    .drag-handle:hover {
        color: #495057;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .reorder-info {
        border: 1px solid #ffc107;
        border-radius: 5px;
        padding: 10px 15px;
        margin-bottom: 15px;
        display: none;
    }
    .reorder-info.show {
        display: block;
    }
</style>
<!-- jQuery UI for Sortable -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
                    ['title' => trans('categorymanagment::subcategory.subcategories_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('categorymanagment::subcategory.subcategories_management') }}</h4>
                        @can('sub-categories.create')
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.category-management.subcategories.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ trans('categorymanagment::subcategory.add_subcategory') }}
                                </a>
                            </div>
                        @endcan
                    </div>

                    {{-- Info Alert --}}
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
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('categorymanagment::subcategory.search_by_name') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Category Filter --}}
                                    <div class="col-md-3">
                                        <x-searchable-tags
                                            name="category_filter"
                                            :label="__('categorymanagment::subcategory.category')"
                                            :options="$categories"
                                            :selected="[]"
                                            :placeholder="__('categorymanagment::subcategory.select_category')"
                                            :multiple="false"
                                            id="category_filter"
                                        />
                                    </div>

                                    {{-- Activation Filter --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('categorymanagment::subcategory.activation') }}
                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="active">
                                                <option value="">{{ __('categorymanagment::subcategory.all') }}
                                                </option>
                                                <option value="1">{{ __('categorymanagment::subcategory.active') }}
                                                </option>
                                                <option value="0">{{ __('categorymanagment::subcategory.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- View Status Filter --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="view_status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-eye me-1"></i>
                                                {{ __('categorymanagment::subcategory.view_status') }}
                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="view_status">
                                                <option value="">{{ __('categorymanagment::subcategory.all') }}
                                                </option>
                                                <option value="1">{{ __('common.visible') }}
                                                </option>
                                                <option value="0">{{ __('common.hidden') }}
                                                </option>
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

                                    {{-- Sort Column --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sort_column" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-sort me-1"></i>
                                                {{ __('common.sort_by') ?? 'Sort By' }}
                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="sort_column">
                                                <option value="sort_number" selected>{{ __('common.sort_number') ?? 'Sort Number' }}</option>
                                                <option value="created_at">{{ __('common.created_at') ?? 'Created At' }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Sort Direction --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sort_direction" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-sort-amount-down me-1"></i>
                                                {{ __('common.sort_direction') ?? 'Sort Direction' }}
                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="sort_direction">
                                                <option value="asc" selected>{{ __('common.ascending') ?? 'Ascending' }}</option>
                                                <option value="desc">{{ __('common.descending') ?? 'Descending' }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-center">
                                        <div class="form-group">
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
                        <div class="reorder-info" id="reorderInfo">
                            <i class="uil uil-info-circle me-2"></i>
                            {{ __('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.' }}
                        </div>
                        <table id="subcategoriesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th style="width: 40px;"><span class="userDatatable-title"><i class="uil uil-sort"></i></span></th>
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('categorymanagment::subcategory.subcategory_information') }}</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ __('categorymanagment::subcategory.view_status') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('categorymanagment::subcategory.activation') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('categorymanagment::subcategory.created_at') }}</span>
                                    </th>
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
    {{-- Delete Confirmation Modal with Loading Component --}}
    <x-delete-with-loading modalId="modal-delete-subcategory" tableId="subcategoriesDataTable"
        deleteButtonClass="delete-subcategory" :title="__('main.confirm delete')" :message="__('main.are you sure you want to delete this')" itemNameId="delete-subcategory-name"
        confirmBtnId="confirmDeleteSubCategoryBtn" :cancelText="__('main.cancel')" :deleteText="__('main.delete')" :loadingDeleting="trans('main.deleting') ?? 'Deleting...'" :loadingPleaseWait="trans('main.please wait') ?? 'Please wait...'"
        :loadingDeletedSuccessfully="trans('main.deleted success') ?? 'Deleted Successfully!'" :loadingRefreshing="trans('main.refreshing') ?? 'Refreshing...'" :errorDeleting="__('main.error on delete')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush


@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;
            let dragDropEnabled = true; // Will be controlled by sort filters

            // Server-side processing with pagination
            var table = $('#subcategoriesDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '{{ route('admin.category-management.subcategories.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;

                        // Add search parameter from custom input
                        d.search = $('#search').val();

                        // Add filter parameters
                        d.category_id = $('#category_filter-single-display').find('input[name="category_filter"]').val() || '';
                        d.active = $('#active').val();
                        d.view_status = $('#view_status').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        // Add custom sorting parameters
                        d.sort_column = $('#sort_column').val();
                        d.sort_direction = $('#sort_direction').val();

                        console.log('📤 Sending to server:', {
                            search: d.search,
                            category_id: d.category_id,
                            active: d.active,
                            created_date_from: d.created_date_from,
                            created_date_to: d.created_date_to,
                            sort_column: d.sort_column,
                            sort_direction: d.sort_direction
                        });

                        return d;
                    },
                    dataSrc: function(json) {
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
                columns: [{
                        data: null,
                        name: 'drag',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<span class="drag-handle" title="{{ __('common.drag_to_reorder') ?? 'Drag to reorder' }}"><i class="uil uil-draggabledots"></i></span>`;
                        }
                    },
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        className: 'text-center fw-bold'
                    }, // #
                    {
                        data: 'translations',
                        name: 'subcategory_information',
                        orderable: false,
                        render: function(data, type, row) {
                            let html = '<div class="subcategory-info-container">';
                            
                            // Subcategory Names with language badges
                            @foreach ($languages as $language)
                                if (data && data['{{ $language->code }}'] && data['{{ $language->code }}'].name && data['{{ $language->code }}'].name !== '-') {
                                    let name = $('<div/>').text(data['{{ $language->code }}'].name).html();
                                    @if ($language->rtl)
                                        html += `<div class="name-item mb-2">
                                            <span class="language-badge badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">{{ strtoupper($language->code) }}</span>
                                            <span class="item-name text-dark fw-semibold" dir="rtl" style="font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">${name}</span>
                                        </div>`;
                                    @else
                                        html += `<div class="name-item mb-2">
                                            <span class="language-badge badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">{{ strtoupper($language->code) }}</span>
                                            <span class="item-name text-dark fw-semibold">${name}</span>
                                        </div>`;
                                    @endif
                                }
                            @endforeach

                            // Category and Sort Number
                            html += '<div class="subcategory-meta-info">';
                            
                            // Category
                            if (row.category && row.category.name) {
                                html += `<div class="mb-1">
                                    <small class="text-muted">{{ trans('categorymanagment::subcategory.category') }}:</small>
                                    <span class="badge badge-round badge-primary badge-lg ms-1" data-category-id="${row.category.id}">${row.category.name}</span>
                                </div>`;
                            }
                            
                            // Sort Number
                            html += `<div class="mb-1">
                                <small class="text-muted">{{ trans('categorymanagment::subcategory.sort_number') }}:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${row.sort_number ?? 0}</span>
                            </div>`;
                            html += '</div>';

                            html += '</div>';
                            return html;
                        },
                        className: 'text-start'
                    },
                    {
                        data: 'view_status',
                        name: 'view_status',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            @can('sub-categories.edit')
                                const isChecked = data ? 'checked' : '';
                                const switchId = 'view-status-switch-' + row.id;

                                return `<div class="form-switch">
                                <input class="form-check-input view-status-switcher"
                                       type="checkbox"
                                       id="${switchId}"
                                       data-subcategory-id="${row.id}"
                                       ${isChecked}
                                       style="cursor: pointer;">
                                <label class="form-check-label" for="${switchId}"></label>
                            </div>`;
                            @else
                                return data ?
                                    `<span class="badge badge-success badge-round badge-lg"><i class="uil uil-eye"></i></span>` :
                                    `<span class="badge badge-secondary badge-round badge-lg"><i class="uil uil-eye-slash"></i></span>`;
                            @endcan
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            // For display, return formatted HTML with switcher (for users with change-status permission)
                            @can('sub-categories.change-status')
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            const subcategoryName = row.translations && row.translations['en'] ? row.translations['en'].name : (row.translations && row.translations['ar'] ? row.translations['ar'].name : 'Subcategory #' + row.id);

                            return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input status-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-subcategory-id="${row.id}"
                                           data-subcategory-name="${$('<div>').text(subcategoryName).html()}"
                                           ${isChecked}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                            @else
                            if (data) {
                                return '<span class="badge badge-round badge-success badge-lg">{{ trans('categorymanagment::subcategory.active') }}</span>';
                            } else {
                                return '<span class="badge badge-round badge-danger badge-lg">{{ trans('categorymanagment::subcategory.inactive') }}</span>';
                            }
                            @endcan
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let showUrl = "{{ route('admin.category-management.subcategories.show', ':id') }}".replace(':id',row.id),
                                editUrl = "{{ route('admin.category-management.subcategories.edit', ':id') }}".replace(':id',row.id),
                                destroyUrl = "{{ route('admin.category-management.subcategories.destroy', ':id') }}".replace(':id',row.id);

                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>

                                    @can('sub-categories.edit')
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('sub-categories.delete')
                                    <a href="javascript:void(0);"
                                    class="remove delete-subcategory btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-subcategory"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.translations?.{{ app()->getLocale() }}?.name || 'Subcategory'}"
                                    data-url="${destroyUrl}"
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
                order: [],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ trans('categorymanagment::subcategory.subcategories_management') }}'
                }],
                searching: true, // Enable built-in search
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ trans('categorymanagment::subcategory.no_subcategories_found') ?? 'No subcategories found' }}",
                    emptyTable: "{{ trans('categorymanagment::subcategory.no_subcategories_found') ?? 'No subcategories found' }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
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
                    },
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
                }
            });

            // Initialize Select2 for entries select if available
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
                    // Only apply to subcategories table
                    if (settings.nTable.id !== 'subcategoriesDataTable') {
                        return true;
                    }

                    var categoryFilter = $('#category_filter-single-display').find('input[name="category_filter"]').val() || '';
                    var activeFilter = $('#active').val();
                    var dateFrom = $('#created_date_from').val();
                    var dateTo = $('#created_date_to').val();

                    // Category filter (now in column 2 - subcategory information)
                    if (categoryFilter && categoryFilter !== '') {
                        var categoryColIndex = 2;

                        // Get the actual rendered cell content with the data-category-id attribute
                        var rowNode = table.row(dataIndex).node();
                        if (!rowNode) {
                            return true;
                        }

                        var cells = $(rowNode).find('td');
                        if (cells.length <= categoryColIndex) {
                            return true;
                        }

                        // Get the category ID from the data attribute in the badge
                        var categoryBadge = $(cells[categoryColIndex]).find('[data-category-id]');
                        if (categoryBadge.length > 0) {
                            var categoryId = categoryBadge.attr('data-category-id');
                            // Compare as strings to avoid type mismatch
                            if (String(categoryId) !== String(categoryFilter)) {
                                return false;
                            }
                        } else {
                            // If no category badge found (category is '-'), hide it when filter is active
                            return false;
                        }
                    }

                    // Active filter (column 4 - after removing category column)
                    if (activeFilter && activeFilter !== '') {
                        var colIndex = 4;

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

                    // Date filters (column 5 - after removing category column)
                    if (dateFrom || dateTo) {
                        var dateColumn = data[5];
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
            $('#active, #view_status, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
            });

            // Sort filter change handlers
            $('#sort_column, #sort_direction').on('change', function() {
                console.log('Sort changed:', $('#sort_column').val(), $('#sort_direction').val());
                table.ajax.reload();
                updateDragDropState();
            });

            // Function to update drag and drop state based on sort filters
            function updateDragDropState() {
                var sortColumn = $('#sort_column').val();
                var sortDirection = $('#sort_direction').val();
                dragDropEnabled = (sortColumn === 'sort_number' && sortDirection === 'asc');
                
                if (dragDropEnabled) {
                    $('#subcategoriesDataTable tbody').removeClass('drag-disabled');
                    $('.drag-handle').css('opacity', '1').css('cursor', 'grab');
                    $('#reorderInfo').removeClass('show').html('<i class="uil uil-info-circle me-2"></i>{{ __('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.' }}');
                } else {
                    $('#subcategoriesDataTable tbody').addClass('drag-disabled');
                    $('.drag-handle').css('opacity', '0.3').css('cursor', 'not-allowed');
                    $('#reorderInfo').addClass('show').html('<i class="uil uil-exclamation-triangle me-2"></i>{{ __('common.drag_drop_disabled_info') ?? 'Drag and drop is only available when sorting by Sort Number (Ascending).' }}');
                }
            }

            // Category filter change handler (searchable-tags single select)
            $('#category_filter-wrapper').on('searchable-tags:change', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search, #active, #view_status, #created_date_from, #created_date_to').val('');
                $('#sort_column').val('sort_number');
                $('#sort_direction').val('asc');
                
                // Reset category filter (single select)
                const catDisplay = $('#category_filter-single-display');
                const catDropdown = $('#category_filter-dropdown');
                catDisplay.html('<span class="placeholder-text text-muted">{{ __("categorymanagment::subcategory.select_category") }}</span>');
                catDropdown.find('.tag-option').removeClass('selected').show();
                
                table.ajax.reload();
                updateDragDropState();
            });

            // Status switcher handler
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const subcategoryId = switcher.data('subcategory-id');
                const subcategoryName = switcher.data('subcategory-name');
                const newStatus = switcher.is(':checked') ? 1 : 2; // 1=active, 2=inactive

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ __('categorymanagment::subcategory.change_status') }}',
                        subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                    });
                }

                // Make AJAX request
                $.ajax({
                    url: '{{ route('admin.category-management.subcategories.change-status', ':id') }}'.replace(':id', subcategoryId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('common.success') ?? 'Success' }}',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }

                            // Reload table to reflect changes
                            table.ajax.reload(null, false);
                        } else {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            // Revert switcher state
                            switcher.prop('checked', !switcher.is(':checked'));

                            // Show error message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') ?? 'Error' }}',
                                    text: response.message
                                });
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Hide loading overlay
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        // Revert switcher state
                        switcher.prop('checked', !switcher.is(':checked'));

                        let errorMessage = '{{ __('categorymanagment::subcategory.error_changing_status') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Show error message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('common.error') ?? 'Error' }}',
                                text: errorMessage
                            });
                        } else {
                            alert(errorMessage);
                        }
                    },
                    complete: function() {
                        // Re-enable switcher
                        switcher.prop('disabled', false);
                    }
                });
            });

            // View status switcher handler
            $(document).on('change', '.view-status-switcher', function() {
                const switcher = $(this);
                const subcategoryId = switcher.data('subcategory-id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Make AJAX request
                $.ajax({
                    url: '{{ route('admin.category-management.subcategories.change-view-status', ':id') }}'.replace(':id', subcategoryId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        view_status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('common.success') ?? 'Success' }}',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                            table.ajax.reload(null, false);
                        } else {
                            switcher.prop('checked', !switcher.is(':checked'));
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') ?? 'Error' }}',
                                    text: response.message
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        switcher.prop('checked', !switcher.is(':checked'));
                        let errorMessage = '{{ __('common.error') ?? 'Error' }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('common.error') ?? 'Error' }}',
                                text: errorMessage
                            });
                        }
                    },
                    complete: function() {
                        switcher.prop('disabled', false);
                    }
                });
            });

            // Delete functionality is now handled by the delete-with-loading component

            // Initialize drag and drop sortable
            @can('sub-categories.edit')
            // Load jQuery UI if not already loaded
            if (typeof $.ui === 'undefined' || typeof $.ui.sortable === 'undefined') {
                $.getScript('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', function() {
                    console.log('jQuery UI loaded');
                    initSortable();
                    updateDragDropState();
                });
            } else {
                initSortable();
                updateDragDropState();
            }

            function initSortable() {
                var $tbody = $('#subcategoriesDataTable tbody');
                
                // Destroy existing sortable if any
                if ($tbody.hasClass('ui-sortable')) {
                    $tbody.sortable('destroy');
                }
                
                $tbody.sortable({
                    handle: '.drag-handle',
                    axis: 'y',
                    cursor: 'grabbing',
                    opacity: 0.8,
                    disabled: !dragDropEnabled,
                    helper: function(e, tr) {
                        var $originals = tr.children();
                        var $helper = tr.clone();
                        $helper.children().each(function(index) {
                            $(this).width($originals.eq(index).outerWidth());
                        });
                        return $helper;
                    },
                    placeholder: 'ui-sortable-placeholder',
                    start: function(event, ui) {
                        if (!dragDropEnabled) {
                            return false;
                        }
                        ui.placeholder.height(ui.item.outerHeight());
                        var colCount = ui.item.children('td').length;
                        ui.placeholder.html('<td colspan="' + colCount + '" style="background-color: #e3f2fd; border: 2px dashed #2196f3;">&nbsp;</td>');
                    },
                    stop: function(event, ui) {
                        // Optional: hide info after drop
                    },
                    update: function(event, ui) {
                        if (!dragDropEnabled) {
                            return false;
                        }
                        var items = [];
                        $('#subcategoriesDataTable tbody tr').each(function(index) {
                            var rowData = table.row(this).data();
                            if (rowData && rowData.id) {
                                items.push({
                                    id: rowData.id,
                                    sort_number: index + 1
                                });
                            }
                        });

                        console.log('Reorder items:', items);

                        if (items.length > 0) {
                            // Show loading
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.show({
                                    text: '{{ __('common.saving') ?? 'Saving' }}...',
                                    subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                                });
                            }

                            $.ajax({
                                url: '{{ route('admin.category-management.subcategories.reorder') }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    items: items
                                },
                                success: function(response) {
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }
                                    
                                    if (response.success) {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: '{{ __('common.success') ?? 'Success' }}',
                                                text: response.message || '{{ __('common.reorder_success') ?? 'Order updated successfully' }}',
                                                timer: 2000,
                                                showConfirmButton: false,
                                                toast: true,
                                                position: 'top-end'
                                            });
                                        }
                                        // Reload table to get updated sort numbers
                                        table.ajax.reload(null, false);
                                    } else {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                title: '{{ __('common.error') ?? 'Error' }}',
                                                text: response.message || '{{ __('common.reorder_error') ?? 'Failed to update order' }}'
                                            });
                                        }
                                        table.ajax.reload(null, false);
                                    }
                                },
                                error: function(xhr) {
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }
                                    
                                    let errorMessage = '{{ __('common.reorder_error') ?? 'Failed to update order' }}';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: '{{ __('common.error') ?? 'Error' }}',
                                            text: errorMessage
                                        });
                                    }
                                    table.ajax.reload(null, false);
                                }
                            });
                        }
                    }
                });
                
                console.log('Sortable initialized for subcategories');
            }

            // Re-initialize sortable after table draw
            table.on('draw', function() {
                setTimeout(function() {
                    if (typeof $.ui !== 'undefined' && typeof $.ui.sortable !== 'undefined') {
                        initSortable();
                        updateDragDropState();
                    }
                }, 100);
            });
            @endcan
        });
    </script>
@endpush
