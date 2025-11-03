@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::variantsconfig.variants_configurations')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('catalogmanagement::variantsconfig.variants_configurations_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.variants-configurations.tree') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-sitemap"></i> {{ trans('catalogmanagement::variantsconfig.tree_view') }}
                            </a>
                            <a href="{{ route('admin.variants-configurations.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('catalogmanagement::variantsconfig.add_variants_config') }}
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
                                                       placeholder="{{ trans('common.search') }}"
                                                       autocomplete="off">
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
                                                <button type="button" id="exportExcel" class="btn btn-primary btn-default btn-squared" title="{{ trans('common.excel') }}">
                                                    <i class="uil uil-file-download-alt m-0"></i>
                                                </button>
                                            </div>
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10">&nbsp;</label>
                                                <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared" title="{{ trans('common.reset') }}">
                                                    <i class="uil uil-redo m-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                </div>
                                {{-- Entries Per Page Selector --}}
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
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="variantsConfigsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('catalogmanagement::variantsconfig.name') }} (EN)</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('catalogmanagement::variantsconfig.name') }} (AR)</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('catalogmanagement::variantsconfig.type') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('catalogmanagement::variantsconfig.value') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('catalogmanagement::variantsconfig.key') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('common.created_at') }}</span>
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
        modalId="modal-delete-variants-config"
        tableId="variantsConfigsDataTable"
        deleteButtonClass="delete-variants-config"
        :title="__('main.confirm delete')"
        :message="__('main.are you sure you want to delete this')"
        itemNameId="delete-variants-config-name"
        confirmBtnId="confirmDeleteBtn"
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

        let viewRoute = '{{ route('admin.variants-configurations.show', ':id') }}';
        let editRoute = '{{ route('admin.variants-configurations.edit', ':id') }}';
        let deleteRoute = '{{ route('admin.variants-configurations.destroy', ':id') }}';
        
        // Server-side processing with pagination
        var table = $('#variantsConfigsDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.variants-configurations.datatable') }}',
                type: 'GET',
                data: function(d) {
                    d.per_page = d.length;
                    d.page = (d.start / d.length) + 1;
                    d.search = $('#search').val();
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
            columns: [
                { 
                    data: 'index', 
                    name: 'index',
                    searchable: false,
                    sortable: false,
                    render: function(data) {
                        return data;
                    }
                },
                { 
                    data: 'name_en',
                    name: 'name_en',
                    render: function(data) {
                        return data || '-';
                    }
                },
                { 
                    data: 'name_ar',
                    name: 'name_ar',
                    render: function(data) {
                        return data ? '<span dir="rtl">' + data + '</span>' : '-';
                    }
                },
                { 
                    data: 'type',
                    name: 'type',
                    render: function(data) {
                        if (data === 'color') {
                            return '<span class="badge badge-success badge-lg badge-round"> {{ trans("catalogmanagement::variantsconfig.color") }}</span>';
                        } else if (data === 'text') {
                            return '<span class="badge badge-info badge-lg badge-round"> {{ trans("catalogmanagement::variantsconfig.text") }}</span>';
                        }
                        return data || '-';
                    }
                },
                { 
                    data: 'value',
                    name: 'value',
                    render: function(data, type, row) {
                        if (row.type === 'color') {
                            return '<div class="d-flex align-items-center gap-2"><span class="color-preview" style="display: inline-block; width: 30px; height: 30px; border-radius: 4px; border: 1px solid #ddd; background-color: ' + data + ';"></span><span>' + data + '</span></div>';
                        }
                        return data;
                    }
                },
                { 
                    data: 'key_name',
                    name: 'key_name',
                    orderable: false
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
                            <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                <li>
                                    <a href="${viewRoute.replace(':id', row.id)}" 
                                    class="view" 
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="${editRoute.replace(':id', row.id)}" 
                                    class="edit" 
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" 
                                    class="remove delete-variants-config" 
                                    title="{{ trans('common.delete') }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modal-delete-variants-config"
                                    data-item-id="${row.id}"
                                    data-item-name="${$('<div>').text(row.value).html()}"
                                    data-url="${deleteRoute.replace(':id', row.id)}">
                                        <i class="uil uil-trash-alt"></i>
                                    </a>
                                </li>
                            </ul>`;
                    }
                }
            ],
            pageLength: per_page,
            lengthChange: false,
            searching: false,
            order: [[6, 'desc']]  // Order by created_at column (index 6)
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
            const tableData = table.rows({ search: 'applied' }).data().toArray();
            
            if (tableData.length === 0) {
                alert('{{ trans("common.no_data_to_export") ?? "No data to export" }}');
                return;
            }
            
            // Build CSV content
            let csvContent = "\uFEFF"; // UTF-8 BOM for Excel
            
            // Header row
            const headers = [
                '#',
                '{{ trans("catalogmanagement::variantsconfig.name") }} (EN)',
                '{{ trans("catalogmanagement::variantsconfig.name") }} (AR)',
                '{{ trans("catalogmanagement::variantsconfig.type") }}',
                '{{ trans("catalogmanagement::variantsconfig.value") }}',
                '{{ trans("catalogmanagement::variantsconfig.key") }}',
                '{{ trans("catalogmanagement::variantsconfig.parent") }}',
                '{{ trans("catalogmanagement::variantsconfig.children_count") }}',
                '{{ trans("common.created_at") }}'
            ];
            csvContent += headers.map(h => '"' + h + '"').join(',') + '\r\n';
            
            // Data rows
            tableData.forEach(function(row) {
                const rowData = [
                    row.id,
                    row.name_en || '-',
                    row.name_ar || '-',
                    row.type || '-',
                    row.value,
                    row.key_name,
                    row.parent,
                    row.children_count,
                    row.created_at
                ];
                csvContent += rowData.map(cell => '"' + (cell || '-').toString().replace(/"/g, '""') + '"').join(',') + '\r\n';
            });
            
            // Create download link
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            const filename = 'variants_configurations_' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') + '.csv';
            
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
