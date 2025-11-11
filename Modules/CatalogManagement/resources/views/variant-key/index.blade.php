@extends('layout.app')
@section('title', trans('catalogmanagement::variantkey.variant_configuration_keys'))

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
                    ['title' => trans('catalogmanagement::variantkey.variant_configuration_keys')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">
                            {{ trans('catalogmanagement::variantkey.variant_configuration_keys_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.variant-keys.tree') }}"
                                class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-sitemap"></i> {{ trans('catalogmanagement::variantkey.tree_view') }}
                            </a>
                            <a href="{{ route('admin.variant-keys.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('catalogmanagement::variantkey.add_variant_key') }}
                            </a>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('catalogmanagement::variantkey.search_by_name') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
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
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared me-1"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i>
                                            {{ __('common.export_excel') }}
                                        </button>
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
                        <table id="variantKeysDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>

                                    @foreach ($languages as $language)
                                        <th>
                                            <span class="userDatatable-title"
                                                @if ($language->rtl) dir="rtl" @endif>
                                                @if ($language->code == 'ar')
                                                    الاسم ({{ $language->name }})
                                                @else
                                                    {{ trans('catalogmanagement::variantkey.name') }}
                                                    ({{ $language->name }})
                                                @endif
                                            </span>
                                        </th>
                                    @endforeach

                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::variantkey.parent_key') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ trans('common.created_at') }}</span></th>
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
    <x-delete-with-loading modalId="modal-delete-variant-key" tableId="variantKeysDataTable"
        deleteButtonClass="delete-variant-key" :title="__('main.confirm delete')" :message="__('main.are you sure you want to delete this')" itemNameId="delete-variant-key-name"
        confirmBtnId="confirmDeleteBtn" :cancelText="__('main.cancel')" :deleteText="__('main.delete')" :loadingDeleting="trans('main.deleting') ?? 'Deleting...'" :loadingPleaseWait="trans('main.please wait') ?? 'Please wait...'"
        :loadingDeletedSuccessfully="trans('main.deleted success') ?? 'Deleted Successfully!'" :loadingRefreshing="trans('main.refreshing') ?? 'Refreshing...'" :errorDeleting="__('main.error on delete')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            let viewRoute = '{{ route('admin.variant-keys.show', ':id') }}';
            let editRoute = '{{ route('admin.variant-keys.edit', ':id') }}';
            let deleteRoute = '{{ route('admin.variant-keys.destroy', ':id') }}';
            // Server-side processing with pagination
            var table = $('#variantKeysDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.variant-keys.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.parent_key_id = $('#parent_key_id').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.orderColumnIndex = d.order[0].column;
                            d.orderDirection = d.order[0].dir;
                        }

                        return d;
                    },
                    dataSrc: function(json) {
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
                        name: 'index',
                        searchable: false,
                        sortable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    @foreach ($languages as $language)
                        {
                            data: 'translations.{{ $language->code }}.name',
                            name: 'name_{{ $language->code }}',
                            render: function(data, type, row) {
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
                    @endforeach {
                        data: 'parent',
                        name: 'parent',
                        orderable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <div class="orderDatatable_actions d-inline-flex gap-1">
                                @can('variant_keys.show')
                                <a href="${viewRoute.replace(':id', row.id)}"
                                class="view btn btn-primary table_action_father"
                                title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                @endcan

                                @can('variant_keys.edit')
                                <a href="${editRoute.replace(':id', row.id)}"
                                class="edit btn btn-warning table_action_father"
                                title="{{ trans('common.edit') }}">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>
                                @endcan

                                @can('variant_keys.delete')
                                <a href="javascript:void(0);"
                                class="remove delete-variant-key btn btn-danger table_action_father"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-delete-variant-key"
                                data-item-id="${row.id}"
                                data-item-name="${$('<div>').text(row.first_name || '').html()}"
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
                lengthChange: false,
                searching: false,
                order: [
                    [0, 'desc']
                ]
            });

            // Custom search input
            $('#search').on('keyup', function() {
                table.ajax.reload();
            });

            // Filter by date range
            $('#created_date_from, #created_date_to').on('change', function() {
                table.ajax.reload();
            });

            // Entries per page selector
            $('#entriesSelect').on('change', function() {
                per_page = $(this).val();
                table.page.len(per_page).draw();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                table.ajax.reload();
            });

            // Excel export functionality - Export directly from DataTable
            $('#exportExcel').on('click', function() {
                // Get all data from current table view (filtered)
                const tableData = table.rows({
                    search: 'applied'
                }).data().toArray();

                if (tableData.length === 0) {
                    alert('{{ trans('common.no_data_to_export') ?? 'No data to export' }}');
                    return;
                }

                // Build CSV content
                let csvContent = "\uFEFF"; // UTF-8 BOM for Excel

                // Header row
                const headers = [
                    '#',
                    @foreach ($languages as $language)
                        '{{ $language->code == 'ar' ? 'الاسم' : trans('catalogmanagement::variantkey.name') }} ({{ $language->name }})',
                    @endforeach
                    '{{ trans('catalogmanagement::variantkey.parent_key') }}',
                    '{{ trans('common.created_at') }}'
                ];
                csvContent += headers.map(h => '"' + h + '"').join(',') + '\r\n';

                // Data rows
                tableData.forEach(function(row) {
                    const rowData = [
                        row.id,
                        @foreach ($languages as $language)
                            row.translations['{{ $language->code }}'] ? row.translations[
                                    '{{ $language->code }}'].name : '-',
                        @endforeach
                        row.parent ? row.parent : '-',
                        row.created_at
                    ];
                    csvContent += rowData.map(cell => '"' + (cell || '-').toString().replace(/"/g,
                        '""') + '"').join(',') + '\r\n';
                });

                // Create download link
                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                const filename = 'variant_keys_' + new Date().toISOString().slice(0, 19).replace(/:/g,
                    '-') + '.csv';

                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            // Delete functionality is now handled by the delete-with-loading component
        });
    </script>
@endpush
