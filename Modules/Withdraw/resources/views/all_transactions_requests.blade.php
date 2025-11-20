@extends('layout.app')

@section('title')
   {{ $status }} Withdraw Transactions | Bnaia
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
                    ['title' =>  $status . ' Withdraw Transactions | Bnaia'],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500"><span style="text-transform: capitalize">{{ $status }}</span> Withdraw Transactions</h4>
                    </div>

                    {{-- Alert --}}
                    {{-- <div class="alert alert-info glowing-alert" role="alert">
                        {{ __('common.live_search_info') }}
                    </div> --}}

                    {{-- Search & Filters --}}
                    {{-- <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i>
                                                {{ __('common.search') }}
                                                <small
                                                    class="text-muted">({{ __('common.real_time') ?? 'Real-time' }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('areasettings::city.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="country_id" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-globe me-1"></i>
                                                {{ __('areasettings::city.country') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="country_id">
                                                <option value="">{{ __('areasettings::city.all_countries') }}</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">
                                                        {{ $country->getTranslation('name', app()->getLocale()) ?? $country->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('areasettings::city.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('areasettings::city.all_status') }}</option>
                                                <option value="1">{{ __('areasettings::city.active') }}</option>
                                                <option value="0">{{ __('areasettings::city.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

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
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared me-1"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i> {{ __('common.export_excel') }}
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
                    </div> --}}

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
                        <table id="citiesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th>
                                        <span class="userDatatable-title">
                                            Vendor
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            Balance Before Send Money
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            Total Sent Money
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            Balance After Send Money
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            Status
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            Invoice
                                        </span>
                                    </th>

                                    <th>
                                        <span class="userDatatable-title">
                                            Action
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
                        <h5 class="modal-title">Upload Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Invoice Image</label>
                        <input type="file" required class="form-control" id="invoice_file" name="invoice" accept="image/*">

                        <div class="mt-3">
                            <img id="invoice_preview" src="{{ asset('assets/img/empty_image.jpg') }}"
                                style="margin-top:10px; max-width:200px; border:1px solid #ddd; padding:5px; cursor: pointer;">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Approve Now</button>
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
                        <h5 class="modal-title">Confirm Reject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-danger fw-bold" style="font-size: 25px;">Are you sure you want to reject this request?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Reject</button>
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
                                return '<p class="text-success" style="text-transform: capitalize; font-weight: bold;">' + data + '</p>';
                            } else if( data == "rejected" ) {
                                return '<p class="text-danger" style="text-transform: capitalize; font-weight: bold;">'+ data +'</p>';
                            } else if( data == "new" ) {
                                return '<p class="text-primary" style="text-transform: capitalize; font-weight: bold;">'+ data +'</p>';
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
                                    <i class="uil uil-download-alt"></i> Download
                                </a>`;
                            }
                            return '-';
                        }
                    }, { // Actions
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
                                                <i class="uil uil-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger reject-withdraw" data-id="${row.id}">
                                                <i class="uil uil-times"></i> Reject
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
                    lengthMenu: "Show _MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    zeroRecords: "No transactions found",
                    loadingRecords: "Loading...",
                    processing: "Processing...",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                }
            });



            // Initialize Select2 on custom entries select
            if ($.fn.select2) {
                $('#entriesSelect').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            } else {
                console.error('Select2 is not loaded');
            }

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Search on cached data with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();
                searchTimer = setTimeout(function() {
                    table.search(searchValue).draw(); // Search on cached data
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
                table.search($(this).val()).draw();
            });

            // Server-side filter event listeners - reload data when filters change
            $('#country_id, #active, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#country_id').val('');
                $('#active').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Clear search and reload table
                table.search('').ajax.reload();
            });
        });
    </script>
@endpush
