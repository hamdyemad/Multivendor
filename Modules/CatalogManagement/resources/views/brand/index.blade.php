@extends('layout.app')
@section('title', trans('catalogmanagement::brand.brands_management'))

@push('styles')
<style>
    /* Drag and Drop Styles */
    #brandsDataTable tbody tr {
        cursor: default;
    }
    #brandsDataTable tbody tr.ui-sortable-helper {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        cursor: grabbing;
    }
    #brandsDataTable tbody tr.ui-sortable-placeholder {
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
                    ['title' => trans('catalogmanagement::brand.brands_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('catalogmanagement::brand.brands_management') }}</h4>
                        <div class="d-flex gap-2">
                            @can('brands.create')
                                <a href="{{ route('admin.brands.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ trans('catalogmanagement::brand.add_brand') }}
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

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ trans('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ trans('catalogmanagement::brand.search_by_name') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('catalogmanagement::brand.activation') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ trans('catalogmanagement::brand.all') }}</option>
                                                <option value="1">{{ trans('catalogmanagement::brand.active') }}
                                                </option>
                                                <option value="0">{{ trans('catalogmanagement::brand.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

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
                        <div class="reorder-info" id="reorderInfo">
                            <i class="uil uil-info-circle me-2"></i>
                            {{ __('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.' }}
                        </div>
                        <table id="brandsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th style="width: 40px;"><span class="userDatatable-title"><i class="uil uil-sort"></i></span></th>
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::brand.logo') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::brand.brand_information') }}</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::brand.activation') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::brand.created_at') }}</span>
                                    </th>
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
    {{-- Delete Confirmation Modal with Loading Component --}}
    <x-delete-with-loading modalId="modal-delete-brand" tableId="brandsDataTable" deleteButtonClass="delete-brand"
        :title="__('main.confirm delete')" :message="__('main.are you sure you want to delete this')" itemNameId="delete-brand-name" confirmBtnId="confirmDeleteBtn" :cancelText="__('main.cancel')"
        :deleteText="__('main.delete')" :loadingDeleting="trans('main.deleting') ?? 'Deleting...'" :loadingPleaseWait="trans('main.please wait') ?? 'Please wait...'" :loadingDeletedSuccessfully="trans('main.deleted success') ?? 'Deleted Successfully!'" :loadingRefreshing="trans('main.refreshing') ?? 'Refreshing...'" :errorDeleting="__('main.error on delete')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let dragDropEnabled = true;
            let per_page = 10;

            let viewRoute = '{{ route('admin.brands.show', ':id') }}';
            let editRoute = '{{ route('admin.brands.edit', ':id') }}';
            let deleteRoute = '{{ route('admin.brands.destroy', ':id') }}';
            
            // Server-side processing with pagination
            var table = $('#brandsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.brands.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.sort_column = $('#sort_column').val();
                        d.sort_direction = $('#sort_direction').val();
                        return d;
                    },
                    dataSrc: function(json) {
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;
                        return json.data || [];
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'drag',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<span class="drag-handle" data-id="${row.id}" data-sort-number="${row.sort_number || 0}" title="{{ __('common.drag_to_reorder') ?? 'Drag to reorder' }}"><i class="uil uil-draggabledots"></i></span>`;
                        }
                    },
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'logo_path',
                        name: 'logo',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (data) {
                                return '<img src="{{ asset('storage/') }}/' + data +
                                    '" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;" />';
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'translations',
                        name: 'brand_information',
                        orderable: false,
                        render: function(data, type, row) {
                            let html = '<div class="brand-info-container">';
                            
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

                            html += '<div class="brand-meta-info">';
                            html += `<div class="mb-1">
                                <small class="text-muted">{{ trans('common.sort_number') }}:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${row.sort_number ?? 0}</span>
                            </div>`;
                            html += '</div>';
                            html += '</div>';
                            return html;
                        },
                        className: 'text-start'
                    },
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data) {
                            if (data == 1) {
                                return '<span class="badge badge-success badge-lg badge-round">{{ __('common.active') }}</span>';
                            } else {
                                return '<span class="badge badge-danger badge-lg badge-round">{{ __('common.inactive') }}</span>';
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data;
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
                                    @can('brands.show')
                                    <a href="${viewRoute.replace(':id', row.id)}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('brands.edit')
                                    <a href="${editRoute.replace(':id', row.id)}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('brands.delete')
                                    <a href="javascript:void(0);"
                                    class="remove delete-brand btn btn-danger table_action_father"
                                    data-id="${row.id}"
                                    data-name="${row.translations?.{{ app()->getLocale() }}?.name || 'Brand'}"
                                    data-url="${deleteRoute.replace(':id', row.id)}"
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
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    lengthMenu: "{{ trans('common.show') }} _MENU_",
                    info: "{{ trans('common.showing') }} _START_ {{ trans('common.to') }} _END_ {{ trans('common.of') }} _TOTAL_ {{ trans('common.entries') }}",
                    infoEmpty: "{{ trans('common.showing') }} 0 {{ trans('common.to') }} 0 {{ trans('common.of') }} 0 {{ trans('common.entries') }}",
                    infoFiltered: "({{ trans('common.filtered_from') }} _MAX_ {{ trans('common.total_entries') }})",
                    zeroRecords: "{{ trans('catalogmanagement::brand.no_brands_found') }}",
                    emptyTable: "{{ trans('catalogmanagement::brand.no_brands_found') }}",
                    loadingRecords: "{{ trans('common.loading') }}...",
                    processing: "{{ trans('common.processing') }}...",
                    search: "{{ trans('common.search') }}:",
                    paginate: {
                        @if (app()->getLocale() == 'en')
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
                }
            });

            // Entries Selector
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Search Debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            // Filters
            $('#active, #created_date_from, #created_date_to').on('change', () => table.ajax.reload());

            // Sort filter change handlers
            $('#sort_column, #sort_direction').on('change', function() {
                table.ajax.reload();
                updateDragDropState();
            });

            // Function to update drag and drop state
            function updateDragDropState() {
                var sortColumn = $('#sort_column').val();
                var sortDirection = $('#sort_direction').val();
                dragDropEnabled = (sortColumn === 'sort_number' && sortDirection === 'asc');
                
                if (dragDropEnabled) {
                    $('#brandsDataTable tbody').removeClass('drag-disabled');
                    $('.drag-handle').css('opacity', '1').css('cursor', 'grab');
                    $('#reorderInfo').removeClass('show').html('<i class="uil uil-info-circle me-2"></i>{{ __('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.' }}');
                } else {
                    $('#brandsDataTable tbody').addClass('drag-disabled');
                    $('.drag-handle').css('opacity', '0.3').css('cursor', 'not-allowed');
                    $('#reorderInfo').addClass('show').html('<i class="uil uil-exclamation-triangle me-2"></i>{{ __('common.drag_drop_disabled_info') ?? 'Drag and drop is only available when sorting by Sort Number (Ascending).' }}');
                }
            }

            // Reset
            $('#resetFilters').on('click', function() {
                $('#search, #active, #created_date_from, #created_date_to').val('');
                $('#sort_column').val('sort_number');
                $('#sort_direction').val('asc');
                $('#entriesSelect').val(10);
                table.search('').page.len(10).ajax.reload();
                updateDragDropState();
            });

            // Initialize drag and drop sortable
            @can('brands.edit')
            if (typeof $.ui === 'undefined' || typeof $.ui.sortable === 'undefined') {
                $.getScript('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', function() {
                    initSortable();
                    updateDragDropState();
                });
            } else {
                initSortable();
                updateDragDropState();
            }

            function initSortable() {
                var $tbody = $('#brandsDataTable tbody');
                
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
                    update: function(event, ui) {
                        if (!dragDropEnabled) {
                            return false;
                        }
                        
                        const draggedRow = ui.item;
                        const $dragHandle = draggedRow.find('.drag-handle');
                        const draggedId = $dragHandle.data('id');
                        const draggedOldSortNumber = $dragHandle.data('sort-number');
                        
                        let targetSortNumber = null;
                        
                        const $nextRow = draggedRow.next('tr');
                        if ($nextRow.length > 0) {
                            const nextSortNumber = $nextRow.find('.drag-handle').data('sort-number');
                            if (nextSortNumber !== undefined) {
                                targetSortNumber = nextSortNumber;
                            }
                        }
                        
                        if (targetSortNumber === null) {
                            const $prevRow = draggedRow.prev('tr');
                            if ($prevRow.length > 0) {
                                const prevSortNumber = $prevRow.find('.drag-handle').data('sort-number');
                                if (prevSortNumber !== undefined) {
                                    targetSortNumber = prevSortNumber;
                                }
                            }
                        }
                        
                        if (targetSortNumber === null) {
                            targetSortNumber = draggedOldSortNumber;
                        }

                        const items = [{
                            id: draggedId,
                            sort_number: targetSortNumber
                        }];

                        if (items.length > 0) {
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.show({
                                    text: '{{ __('common.saving') ?? 'Saving' }}...',
                                    subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                                });
                            }

                            $.ajax({
                                url: '{{ route('admin.brands.reorder') }}',
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
            }

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
