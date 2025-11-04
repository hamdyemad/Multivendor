@extends('layout.app')

@section('title')
{{ $title }}
@endsection

@push('styles')
<!-- Select2 CSS loaded via Vite -->
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('systemsetting::currency.currencies_management')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('systemsetting::currency.currencies_management') }}</h4>
                        @can('system.currency.create')
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.system-settings.currencies.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('systemsetting::currency.add_currency') }}
                            </a>
                        </div>
                        @endcan
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
                                                   placeholder="{{ __('systemsetting::currency.search_placeholder') }}"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">{{ __('systemsetting::currency.status') }}</label>
                                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15" id="active">
                                                <option value="">{{ __('systemsetting::currency.all_status') }}</option>
                                                <option value="1">{{ __('systemsetting::currency.active') }}</option>
                                                <option value="0">{{ __('systemsetting::currency.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_date_from') }}</label>
                                            <input type="date" 
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                   id="created_date_from">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">{{ __('common.created_date_to') }}</label>
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

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="currenciesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    @foreach($languages as $language)
                                        <th>
                                            <span class="userDatatable-title" @if($language->rtl) dir="rtl" @endif>
                                                {{ __('systemsetting::currency.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th>
                                        <span class="userDatatable-title">{{ __('systemsetting::currency.currency_code') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ __('systemsetting::currency.currency_symbol') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ __('systemsetting::currency.active') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ __('systemsetting::currency.created_at') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ __('common.actions') }}</span>
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
        modalId="modal-delete-currency"
        :title="__('systemsetting::currency.confirm_delete')"
        :message="__('systemsetting::currency.delete_confirmation')"
        itemNameId="delete-currency-name"
        confirmBtnId="confirmDeleteCurrencyBtn"
        :deleteRoute="route('admin.system-settings.currencies.index')"
        :cancelText="__('systemsetting::currency.cancel')"
        :deleteText="__('systemsetting::currency.delete_currency')"
    />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Currencies page loaded, initializing DataTable...');
    
    let per_page = 10;

    // Server-side processing with pagination
    let table = $('#currenciesDataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.system-settings.currencies.datatable') }}',
            type: 'GET',
            data: function(d) {
                d.per_page = d.length;
                d.page = (d.start / d.length) + 1;
                d.active = $('#active').val();
                d.created_date_from = $('#created_date_from').val();
                d.created_date_to = $('#created_date_to').val();
                if (d.order && d.order.length > 0) {
                    d.order_column = d.order[0].column;
                    d.order_dir = d.order[0].dir;
                }
                return d;
            },
            dataSrc: function(json) {
                json.recordsTotal = json.total || json.recordsTotal || 0;
                json.recordsFiltered = json.recordsFiltered || json.total || 0;
                
                if (json.error) {
                    console.error('Server returned error:', json.error);
                    alert('Error: ' + json.error);
                    return [];
                }
                return json.data || [];
            },
            error: function(xhr, error, code) {
                console.error('DataTables AJAX Error:', {xhr: xhr, error: error, code: code});
                alert('Error loading data. Status: ' + xhr.status);
            }
        },
        columns: [
            { 
                data: 'index', 
                name: 'index', 
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<div class="userDatatable-content">' + data + '</div>';
                }
            },
            @foreach($languages as $language)
            { 
                data: 'names.{{ $language->id }}.value',
                name: 'name_{{ $language->code }}', 
                orderable: true,
                render: function(data, type, row) {
                    const nameData = row.names[{{ $language->id }}];
                    const rtlAttr = nameData.rtl ? ' dir="rtl"' : '';
                    return '<div class="userDatatable-content"' + rtlAttr + '>' + (nameData.value || '-') + '</div>';
                }
            },
            @endforeach
            { 
                data: 'code',
                name: 'code', 
                orderable: true,
                render: function(data, type, row) {
                    return '<div class="userDatatable-content">' + data + '</div>';
                }
            },
            { 
                data: 'symbol',
                name: 'symbol', 
                orderable: true,
                render: function(data, type, row) {
                    return '<div class="userDatatable-content">' + data + '</div>';
                }
            },
            { 
                data: 'active',
                name: 'active', 
                orderable: true,
                render: function(data, type, row) {
                    if (data) {
                        return '<div class="userDatatable-content"><span class="badge badge-success badge-lg badge-round">{{ __("systemsetting::currency.active") }}</span></div>';
                    } else {
                        return '<div class="userDatatable-content"><span class="badge badge-danger badge-lg badge-round">{{ __("systemsetting::currency.inactive") }}</span></div>';
                    }
                }
            },
            { 
                data: 'created_at',
                name: 'created_at', 
                orderable: true,
                render: function(data, type, row) {
                    return '<div class="userDatatable-content">' + data + '</div>';
                }
            },
            { 
                data: null,
                name: 'actions', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                            @can('system.currency.show')
                            <li>
                                <a href="{{ url('admin/system-settings/currencies') }}/${row.id}" class="view">
                                    <i class="uil uil-eye"></i>
                                </a>
                            </li>
                            @endcan
                            @can('system.currency.edit')
                            <li>
                                <a href="{{ url('admin/system-settings/currencies') }}/${row.id}/edit" class="edit">
                                    <i class="uil uil-edit"></i>
                                </a>
                            </li>
                            @endcan
                            @can('system.currency.delete')
                            <li>
                                <a href="javascript:void(0);" 
                                   class="remove" 
                                   data-bs-toggle="modal" 
                                   data-bs-target="#modal-delete-currency"
                                   data-item-id="${row.id}"
                                   data-item-name="${row.display_name}">
                                    <i class="uil uil-trash-alt"></i>
                                </a>
                            </li>
                            @endcan
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
                title: '{{ __("systemsetting::currency.currencies_management") }}'
            }
        ],
        searching: true,
        language: {
            lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
            info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
            infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
            infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
            zeroRecords: "{{ __("systemsetting::currency.no_currencies_found") ?? 'No currencies found' }}",
            emptyTable: "{{ __("systemsetting::currency.no_currencies_found") ?? 'No currencies found' }}",
            loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
            processing: "{{ __('common.processing') ?? 'Processing' }}...",
            search: "{{ __('common.search') ?? 'Search' }}:",
            paginate: {
                first: '{{ __('common.first') ?? 'First' }}',
                last: '{{ __('common.last') ?? 'Last' }}',
                next: '{{ __('common.next') ?? 'Next' }}',
                previous: '{{ __('common.previous') ?? 'Previous' }}'
            }
        }
    });

    // Initialize Select2
    if ($.fn.select2) {
        $('#entriesSelect').select2({
            theme: 'bootstrap-5',
            minimumResultsForSearch: Infinity,
            width: '100%'
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

    // Search with debounce
    let searchTimer;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimer);
        const searchValue = $(this).val();
        searchTimer = setTimeout(function() {
            table.search(searchValue).draw();
        }, 500);
    });
    
    $('#search').on('change', function() {
        clearTimeout(searchTimer);
        table.search($(this).val()).draw();
    });

    // Server-side filter event listeners
    $('#active, #created_date_from, #created_date_to').on('change', function() {
        table.ajax.reload();
    });

    // Reset filters button
    $('#resetFilters').on('click', function() {
        $('#search').val('');
        $('#active').val('');
        $('#created_date_from').val('');
        $('#created_date_to').val('');
        table.search('').ajax.reload();
    });
});
</script>
@endpush
