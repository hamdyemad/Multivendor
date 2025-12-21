@extends('layout.app')
@section('title', trans('catalogmanagement::tax.taxes_management'))

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
                    ['title' => trans('catalogmanagement::tax.taxes_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('catalogmanagement::tax.taxes_management') }}</h4>
                        <div class="d-flex gap-2">
                            @can('taxes.index')
                                <button type="button" id="exportExcel"
                                    class="btn btn-secondary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-file-download"></i> {{ trans('common.export_excel') }}
                                </button>
                            @endcan
                            @can('taxes.create')
                                <a href="{{ route('admin.taxes.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ trans('catalogmanagement::tax.add_tax') }}
                                </a>
                            @endcan
                        </div>
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
                                                placeholder="{{ __('catalogmanagement::tax.search_by_name') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Activation --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('catalogmanagement::tax.activation') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('catalogmanagement::tax.all') }}</option>
                                                <option value="1">{{ __('catalogmanagement::tax.active') }}</option>
                                                <option value="0">{{ __('catalogmanagement::tax.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created From --}}
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

                                    {{-- Created To --}}
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
                        <table id="taxesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    @foreach ($languages as $language)
                                        <th>
                                            <span class="userDatatable-title"
                                                @if ($language->rtl) dir="rtl" @endif>
                                                {{ __('catalogmanagement::tax.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::tax.tax_rate') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::tax.activation') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::tax.created_at') }}</span>
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
    <x-delete-with-loading modalId="modal-delete-tax" tableId="taxesDataTable" deleteButtonClass="delete-tax"
        :title="__('main.confirm delete')" :message="__('main.are you sure you want to delete this')" itemNameId="delete-tax-name" confirmBtnId="confirmDeleteBtn" :cancelText="__('main.cancel')"
        :deleteText="__('main.delete')" :loadingDeleting="trans('main.deleting') ?? 'Deleting...'" :loadingPleaseWait="trans('main.please wait') ?? 'Please wait...'" :loadingDeletedSuccessfully="trans('main.deleted success') ?? 'Deleted Successfully!'" :loadingRefreshing="trans('main.refreshing') ?? 'Refreshing...'" :errorDeleting="__('main.error on delete')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            // Server-side processing with pagination
            var table = $('#taxesDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '{{ route('admin.taxes.datatable') }}',
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
                    // Tax Rate column
                    {
                        data: 'tax_rate',
                        name: 'tax_rate',
                        render: function(data) {
                            return parseFloat(data).toFixed(2) + '%';
                        }
                    },
                    // Active Status column
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
                            let indexUrl = "{{ route('admin.taxes.index') }}";
                            let viewUrl = "{{ route('admin.taxes.show', ':id') }}".replace(':id',
                                row.id);
                            let editUrl = "{{ route('admin.taxes.edit', ':id') }}".replace(':id',
                                row.id);
                            return `
                            <div class="orderDatatable_actions d-inline-flex gap-1">
                                @can('taxes.show')
                                <a href="${viewUrl}"
                                class="view btn btn-primary table_action_father"
                                title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                @endcan

                                @can('taxes.edit')
                                <a href="${editUrl}"
                                class="edit btn btn-warning table_action_father"
                                title="{{ trans('common.edit') }}">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>
                                @endcan

                                @can('taxes.delete')
                                <a href="javascript:void(0);"
                                class="remove delete-tax btn btn-danger table_action_father"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-delete-tax"
                                data-item-id="${row.id}"
                                data-item-name="${$('<div>').text(row.first_name).html()}"
                                data-url="${indexUrl}/${row.id}"
                                title="{{ trans('common.delete') }}">
                                    <i class="uil uil-trash-alt table_action_icon"></i>
                                </a>
                                @endcan
                            </div>`;

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
                    title: '{{ trans('catalogmanagement::tax.taxes_management') }}'
                }],
                searching: true, // Enable built-in search
                language: {
                    lengthMenu: "{{ trans('common.show') }} _MENU_",
                    info: "{{ trans('common.showing') }} _START_ {{ trans('common.to') }} _END_ {{ trans('common.of') }} _TOTAL_ {{ trans('common.entries') }}",
                    infoEmpty: "{{ trans('common.showing') }} 0 {{ trans('common.to') }} 0 {{ trans('common.of') }} 0 {{ trans('common.entries') }}",
                    infoFiltered: "({{ trans('common.filtered_from') }} _MAX_ {{ trans('common.total_entries') }})",
                    zeroRecords: "{{ trans('catalogmanagement::tax.no_taxes_found') }}",
                    emptyTable: "{{ trans('catalogmanagement::tax.no_taxes_found') }}",
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
                    },
                    aria: {
                        sortAscending: ": {{ trans('common.sort_ascending') }}",
                        sortDescending: ": {{ trans('common.sort_descending') }}"
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
