@extends('layout.app')

@section('title')
   {{ __('withdraw::withdraw.' . strtolower($status)) }} {{ __('withdraw::withdraw.withdraw_transactions') }} | Bnaia
@endsection

@push('styles')
    <!-- Select2 CSS loaded via Vite -->
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
                    ['title' => __('withdraw::withdraw.' . strtolower($status)) . ' ' . __('withdraw::withdraw.withdraw_transactions')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('withdraw::withdraw.' . strtolower($status)) }} {{ __('withdraw::withdraw.withdraw_transactions') }}</h4>
                    </div>

                    {{-- Alert --}}
                    {{-- <div class="alert alert-info glowing-alert" role="alert">
                        {{ __('common.live_search_info') }}
                    </div> --}}

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i>
                                                {{ __('withdraw::withdraw.search') }}
                                                <small class="text-muted">({{ __('withdraw::withdraw.real_time') }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('common.search') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="vendor_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-shop me-1"></i>
                                                {{ __('withdraw::withdraw.vendor') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="vendor_filter">
                                                <option value="">{{ __('withdraw::withdraw.all') }} {{ __('withdraw::withdraw.vendors') }}</option>
                                                @if(isset($vendors))
                                                    @foreach($vendors as $vendor)
                                                        <option value="{{ $vendor['id'] }}" @if(request('vendor_id') == $vendor['id']) selected @endif>{{ $vendor['name'] }}</option>
                                                    @endforeach
                                                @endif
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
                                            title="{{ __('withdraw::withdraw.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('withdraw::withdraw.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('withdraw::withdraw.reset_filters') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('withdraw::withdraw.reset_filters') }}
                                        </button>
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared"
                                            title="{{ __('withdraw::withdraw.export_excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i> {{ __('withdraw::withdraw.export_excel') }}
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
                                            {{ __('withdraw::withdraw.balance_before_send_money') }}
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.total_sent_money') }}
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.balance_after_send_money') }}
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.status') }}
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.invoice') }}
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.created_at') }}
                                        </span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">
                                            {{ __('withdraw::withdraw.action') }}
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

    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('admin.changeTransactionRequestsStatus') }}" enctype="multipart/form-data">
                @csrf
                <input type="text" name="request_id" hidden id="approve_id">
                <input type="text" name="status" hidden id="approve_status_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('withdraw::withdraw.upload_invoice') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">{{ __('withdraw::withdraw.invoice_image') }}</label>
                        <input type="file" required class="form-control" id="invoice_file" name="invoice" accept="image/*">

                        <div class="mt-3">
                            <img id="invoice_preview" src="{{ asset('assets/img/empty_image.jpg') }}"
                                style="margin-top:10px; max-width:200px; border:1px solid #ddd; padding:5px; cursor: pointer;">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('withdraw::withdraw.approve_now') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="rejectForm" method="POST" action="{{ route('admin.changeTransactionRequestsStatus') }}">
                @csrf
                <input type="text" name="request_id" hidden id="reject_id">
                <input type="text" name="status" hidden id="reject_status_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('withdraw::withdraw.confirm_reject') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-danger fw-bold" style="font-size: 25px;">{{ __('withdraw::withdraw.are_you_sure_reject_request') }}</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('withdraw::withdraw.cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('withdraw::withdraw.yes_reject') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        // === OPEN APPROVE MODAL ===
        $(document).on('click', '.approve-withdraw', function() {
            let id = $(this).data('id');
            $('#approve_id').val(id);
            $('#approve_status_id').val("accepted");
            $('#invoice_file').val('');
            $('#approveModal').modal('show');
        });


        // === PREVIEW IMAGE ===
        $('#invoice_file').on('change', function() {
            let file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#invoice_preview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(file);
            }
        });

        // === OPEN REJECT MODAL ===
        $(document).on('click', '.reject-withdraw', function() {
            let id = $(this).data('id');
            $('#reject_id').val(id);
            $('#reject_status_id').val("rejected");
            $('#rejectModal').modal('show');
        });
    </script>
    <script>
        let vendorExists = @json($vendor ? true : false);
    </script>

    <script>
        $(document).ready(function() {
            console.log('Cities page loaded, initializing DataTable...');

            let per_page = 10;

            // Server-side processing with pagination
            let table = $('#citiesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.transactionsRequestsDatatable', ['status' => $status]) }}',
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
                    { // Vendor
                        data: 'vendor',
                        name: 'vendor',
                        render: function(data, type, row) {
                            const logo = row.vendor_logo ?
                                `<img src="${row.vendor_logo}" alt="${data}" style="width:30px; height:30px; border-radius:50%; margin-right:8px;">` :
                                '';
                            return `<div class="userDatatable-content d-flex align-items-center">
                    ${logo}
                            <span>${data || '-'}</span>
                        </div>`;
                        }
                    },
                    { // Before Money
                        data: 'before_sending_money',
                        name: 'before_sending_money',
                        render: function(data, type, row) {
                            console.log(row);
                            return `<div class="userDatatable-content">${row.before_sending_money || '-'}</div>`;
                        }
                    },
                    { // Sent Amount
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
                    { // Status
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data == "accepted") {
                                return '<p class="text-success" style="text-transform: capitalize; font-weight: bold;">{{ __('withdraw::withdraw.accepted') }}</p>';
                            } else if( data == "rejected" ) {
                                return '<p class="text-danger" style="text-transform: capitalize; font-weight: bold;">{{ __('withdraw::withdraw.rejected') }}</p>';
                            } else if( data == "new" ) {
                                return '<p class="text-primary" style="text-transform: capitalize; font-weight: bold;">{{ __('withdraw::withdraw.new') }}</p>';
                            }
                        }
                    },
                    { // Invoice
                        data: 'invoice',
                        name: 'invoice',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (data) {
                                return `<a href="${data}" class="btn btn-sm btn-primary" target="_blank" download>
                                    <i class="uil uil-download-alt"></i> {{ __('withdraw::withdraw.download') }}
                                </a>`;
                            }
                            return '-';
                        }
                    },
                    { // Created at
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return `<div class="userDatatable-content">${row.created_at || '-'}</div>`;
                        }
                    },
                    { // Actions
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.status === 'new') {
                                if (!vendorExists) {
                                    return `
                                        <div class="d-inline-flex gap-1">
                                            <button class="btn btn-success approve-withdraw" data-id="${row.id}">
                                                <i class="uil uil-check"></i> {{ __('withdraw::withdraw.approve') }}
                                            </button>
                                            <button class="btn btn-danger reject-withdraw" data-id="${row.id}">
                                                <i class="uil uil-times"></i> {{ __('withdraw::withdraw.reject') }}
                                            </button>
                                        </div>`;
                                } else {
                                    return '-';
                                }
                            } else {
                                return '-';
                            }
                        }

                    }
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
                    search: "{{ __('withdraw::withdraw.search') }}:",
                    paginate: {
                        first: "{{ __('withdraw::withdraw.first') }}",
                        last: "{{ __('withdraw::withdraw.last') }}",
                        next: "{{ __('withdraw::withdraw.next') }}",
                        previous: "{{ __('withdraw::withdraw.previous') }}"
                    },
                }
            });



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

            // Function to get URL parameter
            function getUrlParameter(name) {
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

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

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
                $('#created_date_from').val('');
                $('#created_date_to').val('');

                // Update URL and reload table
                updateUrlWithFilters();
                table.ajax.reload();
            });
        });
    </script>
@endpush
