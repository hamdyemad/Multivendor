@extends('layout.app')
@section('title', trans('categorymanagment::subcategory.subcategories_management'))

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
                        @can('sub_categories.create')
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
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="category_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-layers me-1"></i>
                                                {{ __('categorymanagment::subcategory.category') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="category_filter">
                                                <option value="">{{ __('categorymanagment::subcategory.all') }}
                                                </option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->getTranslation('name', app()->getLocale()) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Activation Filter --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('categorymanagment::subcategory.activation') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
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

                                    {{-- Created Date From --}}
                                    <div class="col-md-6">
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
                                    <div class="col-md-6">
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
                        <table id="subcategoriesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    @foreach ($languages as $language)
                                        <th>
                                            <span class="userDatatable-title"
                                                @if ($language->rtl) dir="rtl" @endif>
                                                {{ __('categorymanagment::subcategory.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th><span
                                            class="userDatatable-title">{{ __('categorymanagment::subcategory.category') }}</span>
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
                        d.category_id = $('#category_filter').val();
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            var columnIndex = d.order[0].column;
                            var sortType = '';

                            // Map column index to sort type
                            if (columnIndex == 0) {
                                sortType = 'id';
                            }
                            @foreach ($languages as $index => $language)
                                else if (columnIndex == {{ $index + 1 }}) {
                                    sortType = 'name_{{ $language->code }}';
                                }
                            @endforeach
                            else if (columnIndex == {{ count($languages) + 1 }}) {
                                sortType = 'category';
                            } else if (columnIndex == {{ count($languages) + 2 }}) {
                                sortType = 'active';
                            } else if (columnIndex == {{ count($languages) + 3 }}) {
                                sortType = 'created_at';
                            }

                            d.sort_type = sortType;
                            d.sort_by = d.order[0].dir; // 'asc' or 'desc'
                        }

                        console.log('📤 Sending to server:', {
                            search: d.search,
                            category_id: d.category_id,
                            active: d.active,
                            created_date_from: d.created_date_from,
                            created_date_to: d.created_date_to,
                            sort_type: d.sort_type,
                            sort_by: d.sort_by
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
                        data: 'index',
                        name: 'index'
                    }, // #
                    @foreach ($languages as $language)
                        {
                            data: 'translations.{{ $language->code }}.name',
                            name: 'name_{{ $language->code }}',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                @if ($language->rtl)
                                    return '<span dir="rtl">' + data + '</span>';
                                @else
                                    return data;
                                @endif
                            }
                        },
                    @endforeach {
                        data: 'category',
                        name: 'category',
                        render: function(data) {
                            if (data && data.name) {
                                return '<span class="badge badge-round badge-primary badge-lg" data-category-id="' +
                                    data.id + '">' + data.name + '</span>';
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data, type, row) {
                            // For sorting, return numeric value
                            if (type === 'sort' || type === 'type') {
                                return data ? 1 : 0;
                            }

                            // For display, return formatted HTML with switcher (for users with edit permission)
                            @can('sub_categories.edit')
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
                        name: 'created_at'
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let showUrl = "{{ route('admin.category-management.subcategories.show', ':id') }}".replace(':id',row.id),
                                editUrl = "{{ route('admin.category-management.subcategories.edit', ':id') }}".replace(':id',row.id),
                                destroyUrl = "{{ route('admin.category-management.subcategories.destroy', ':id') }}".replace(':id',row.id);

                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    @can('sub_categories.show')
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('sub_categories.edit')
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('sub_categories.delete')
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

                    var categoryFilter = $('#category_filter').val();
                    var activeFilter = $('#active').val();
                    var dateFrom = $('#created_date_from').val();
                    var dateTo = $('#created_date_to').val();

                    // Category filter (column {{ count($languages) + 1 }})
                    if (categoryFilter && categoryFilter !== '') {
                        var categoryColIndex = {{ count($languages) + 1 }};

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
            $('#category_filter, #active, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
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

            // Delete functionality is now handled by the delete-with-loading component
        });
    </script>
@endpush
