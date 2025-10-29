@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('brands::brand.brands_management')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('brands::brand.brands_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('brands::brand.add_brand') }}
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
                                                       placeholder="{{ trans('brands::brand.search_by_name') }}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="active" class="il-gray fs-14 fw-500 mb-10">{{ trans('brands::brand.activation') }}</label>
                                                <select class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                        id="active">
                                                    <option value="">{{ trans('brands::brand.all') }}</option>
                                                    <option value="1">{{ trans('brands::brand.active') }}</option>
                                                    <option value="0">{{ trans('brands::brand.inactive') }}</option>
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

                    <div class="table-responsive">
                        <table id="brandsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('brands::brand.logo') }}</span>
                                    </th>
                                    @foreach($languages as $language)
                                        <th>
                                            <span class="userDatatable-title" @if($language->rtl) dir="rtl" @endif>
                                                {{ trans('brands::brand.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th>
                                        <span class="userDatatable-title">{{ trans('brands::brand.activation') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('brands::brand.created_at') }}</span>
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
        modalId="modal-delete-brand"
        tableId="brandsDataTable"
        deleteButtonClass="delete-brand"
        :title="__('main.confirm delete')"
        :message="__('main.are you sure you want to delete this')"
        itemNameId="delete-brand-name"
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

        // Server-side processing with pagination
        var table = $('#brandsDataTable').DataTable({
            processing: true,
            serverSide: true, // Server-side processing
            ajax: {
                url: '{{ route('admin.brands.datatable') }}',
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
                { data: 0, name: 'id' }, // #
                { data: 1, name: 'logo', orderable: false, searchable: false }, // Logo
                @foreach($languages as $language)
                { data: {{ $loop->index + 2 }}, name: 'name_{{ $language->code }}', render: function(data) { return data; } },
                @endforeach
                { data: {{ count($languages) + 2 }}, name: 'active', render: function(data) { return data; } }, // Active Status
                { data: {{ count($languages) + 3 }}, name: 'created_at', render: function(data) { return data; } }, // Created At
                { data: {{ count($languages) + 4 }}, name: 'actions', orderable: false, searchable: false, render: function(data) { return data; } } // Actions
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
                    title: '{{ trans('brands::brand.brands_management') }}'
                }
            ],
            searching: true, // Enable built-in search
            language: {
                lengthMenu: "{{ trans('common.show') }} _MENU_",
                info: "{{ trans('common.showing') }} _START_ {{ trans('common.to') }} _END_ {{ trans('common.of') }} _TOTAL_ {{ trans('common.entries') }}",
                infoEmpty: "{{ trans('common.showing') }} 0 {{ trans('common.to') }} 0 {{ trans('common.of') }} 0 {{ trans('common.entries') }}",
                infoFiltered: "({{ trans('common.filtered_from') }} _MAX_ {{ trans('common.total_entries') }})",
                zeroRecords: "{{ trans('brands::brand.no_brands_found') }}",
                emptyTable: "{{ trans('brands::brand.no_brands_found') }}",
                loadingRecords: "{{ trans('common.loading') }}...",
                processing: "{{ trans('common.processing') }}...",
                search: "{{ trans('common.search') }}:",
                paginate: {
                    first: '{{ trans('common.first') }}',
                    last: '{{ trans('common.last') }}',
                    next: '{{ trans('common.next') }}',
                    previous: '{{ trans('common.previous') }}'
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
