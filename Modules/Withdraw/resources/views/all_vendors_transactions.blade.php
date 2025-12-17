@extends('layout.app')

@section('title')
    {{ __('withdraw::withdraw.all_vendors_transactions') }} | Bnaiadsection
@endsection

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .glowing-alert {
            animation: glow 2s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { box-shadow: 0 0 5px #007bff; }
            to { box-shadow: 0 0 20px #007bff; }
        }
    </style>
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
                    ['title' => __('withdraw::withdraw.all_vendors_transactions')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('withdraw::withdraw.all_vendors_transactions') }}</h4>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i>
                                                {{ __('withdraw::withdraw.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('withdraw::withdraw.search') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vendor_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-store me-1"></i>
                                                {{ __('withdraw::withdraw.vendor') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="vendor_filter">
                                                <small class="text-muted">({{ __('withdraw::withdraw.real_time') }})</small>
                                                <option value="">{{ __('withdraw::withdraw.all') }} {{ __('withdraw::withdraw.vendors') }}</option>
                                                @foreach($vendors as $vendor)
                                                    <option value="{{ $vendor['id'] }}" @if(request('vendor_id') == $vendor['id']) selected @endif>{{ $vendor['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('withdraw::withdraw.created_date_from') }}
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
                                                {{ __('withdraw::withdraw.created_date_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>


                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('common.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('withdraw::withdraw.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('withdraw::withdraw.reset_filters') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('withdraw::withdraw.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('withdraw::withdraw.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="citiesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.vendor') }}
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.before_sending_money') }}
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.sent_amount') }}
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.after_sending_amount') }}
                                        </span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.created_at') }}
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            console.log('Vendors transactions page loaded, initializing DataTable...');

            let per_page = 10;

            // Function to get URL parameters
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // Function to update URL with filter parameters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();

                const search = $('#search').val();
                const vendorFilter = $('#vendor_filter').val();
                const createdDateFrom = $('#created_date_from').val();
                const createdDateTo = $('#created_date_to').val();

                if (search) params.set('search', search);
                if (vendorFilter) params.set('vendor_id', vendorFilter);
                if (createdDateFrom) params.set('created_date_from', createdDateFrom);
                if (createdDateTo) params.set('created_date_to', createdDateTo);

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.pushState({}, '', newUrl);
            }

            // Initialize filters from URL parameters
            function initializeFiltersFromUrl() {
                $('#search').val(getUrlParameter('search'));
                $('#vendor_filter').val(getUrlParameter('vendor_filter'));
                $('#created_date_from').val(getUrlParameter('created_date_from'));
                $('#created_date_to').val(getUrlParameter('created_date_to'));
            }

            // Initialize filters from URL
            initializeFiltersFromUrl();

            // Initialize Select2 on all select elements
            if ($.fn.select2) {
                $('#entriesSelect, #vendor_filter').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            } else {
                console.error('Select2 is not loaded');
            }

            // Server-side processing with pagination
            let table = $('#citiesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.allVendorsTransactionsDatatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;

                        // Add filter parameters
                        d.search = $('#search').val();
                        d.vendor_filter = $('#vendor_filter').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        return d;
                    },
                    dataSrc: function(json) {
                        if (json.error) {
                            console.error('Server error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTables AJAX Error:', xhr.responseText);
                        alert('Error loading data. Status: ' + xhr.status);
                    }
                },
                columns: [{ // Index
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1 + (meta.settings._iDisplayStart || 0);
                        }
                    },
                    { // Vendor (Logo + Name)
                        data: 'vendor',
                        name: 'vendor',
                        render: function(data, type, row) {
                            if (typeof data !== 'object' || data === null) {
                                return '-';
                            }

                            const logo = data.logo ?
                                `<img class='me-1' src="${data.logo}" alt="Vendor Logo" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">` :
                                `<div class='me-1' style="width:40px; height:40px; border-radius:50%; background:#f0f0f0; display:flex; align-items:center; justify-content:center; font-size:12px; color:#666;">{{ __('withdraw::withdraw.no_logo') }}</div>`;

                            return `<div class="userDatatable-content d-flex align-items-center">
                                ${logo}
                                <span style="font-weight:500;">${data.name || '-'}</span>
                            </div>`;
                        }
                    },
                    { // Sent Amount
                        data: 'before_sending_money',
                        name: 'before_sending_money',
                        render: function(data, type, row) {
                            return `<div class="userDatatable-content">${row.before_sending_money || '-'}</div>`;
                        }
                    },
                    { // After Money
                        data: 'sent_amount',
                        name: 'sent_amount',
                        render: function(data, type, row) {
                            return `<div class="userDatatable-content">${row.sent_amount || '-'}</div>`;
                        }
                    },
                    { // After Money
                        data: 'after_sending_amount',
                        name: 'after_sending_amount',
                        render: function(data, type, row) {
                            return `<div class="userDatatable-content">${row.after_sending_amount || '-'}</div>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return `<div class="userDatatable-content">${row.created_at || '-'}</div>`;
                        }
                    },
                ],
                pageLength: 10,
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
                searching: true,
                language: {
                    lengthMenu: "{{ __('withdraw::withdraw.show') }} _MENU_",
                    info: "{{ __('withdraw::withdraw.showing_entries') }}",
                    infoEmpty: "{{ __('withdraw::withdraw.showing_empty') }}",
                    emptyTable: "{{ __('withdraw::withdraw.no_data_available') }}",
                    zeroRecords: "{{ __('withdraw::withdraw.no_transactions_found') }}",
                    loadingRecords: "{{ __('withdraw::withdraw.loading') }}",
                    processing: "{{ __('withdraw::withdraw.processing') }}",
                    search: "{{ __('withdraw::withdraw.search') }}:"
                }
            });

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Search functionality is handled below in the new search handler

            // Search button functionality
            $('#searchBtn').on('click', function() {
                console.log('Search button clicked, updating URL and reloading table...');
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Server-side filter event listeners - reload data when filters change
            $('#vendor_filter, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Search input with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();
                searchTimer = setTimeout(function() {
                    updateUrlWithFilters();
                    table.ajax.reload();
                }, 500);
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#vendor_filter').val('').trigger('change');
                $('#amount_range').val('').trigger('change');
                $('#transaction_status').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                $('#min_amount').val('');

                // Update URL to clear parameters
                window.history.pushState({}, '', window.location.pathname);

                // Clear search and reload table
                table.search('').ajax.reload();
            });
        });
    </script>
@endpush
