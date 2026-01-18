@extends('layout.app')

@section('title', __('accounting.vendor_balance_overview'))

@push('styles')
@endpush

@section('content')
<div class="crm mb-25">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="uil uil-estate"></i>{{ __('accounting.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.accounting.summary') }}">{{ __('accounting.accounting') }}</a></li>
                                <li class="breadcrumb-item active">{{ __('accounting.vendor_balance_overview') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('accounting.vendor_balance_overview') }}</h4>
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
                                                <i class="uil uil-search me-1"></i> {{ __('accounting.search') }}
                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search" placeholder="{{ __('accounting.search_vendors') }}..." autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Date From --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date-from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> {{ __('accounting.date_from') }}
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date-from">
                                        </div>
                                    </div>

                                    {{-- Date To --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date-to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> {{ __('accounting.date_to') }}
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date-to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center gap-2">
                                        <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared me-1">
                                            <i class="uil uil-search me-1"></i> {{ __('accounting.search') }}
                                        </button>
                                        <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared">
                                            <i class="uil uil-redo me-1"></i> {{ __('accounting.reset') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('accounting.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('accounting.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="vendorBalancesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.vendor_information') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.total_earnings') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.commission_deducted') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.available_balance') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.total_withdrawn') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.actual_available_balance') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.last_updated') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
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
    <script>
        $(document).ready(function() {
            let per_page = 10;

            let table = $('#vendorBalancesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.accounting.vendor-balances.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.date_from = $('#date-from').val();
                        d.date_to = $('#date-to').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { 
                        data: 'vendor_name', 
                        name: 'vendor_name',
                        render: function(data, type, row) {
                            return `<div class="userDatatable-inline-title">
                                <h6 class="text-dark fw-500 mb-1">${row.vendor_name || '-'}</h6>
                                <p class="d-block mb-0 text-muted small">${row.vendor_email || '-'}</p>
                            </div>`;
                        }
                    },
                    { data: 'total_earnings', name: 'total_earnings' },
                    { data: 'commission_deducted', name: 'commission_deducted' },
                    { data: 'available_balance', name: 'available_balance' },
                    { data: 'total_withdrawn', name: 'total_withdrawn' },
                    { data: 'actual_available_balance', name: 'actual_available_balance' },
                    { data: 'updated_at', name: 'updated_at' }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[7, 'desc']],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                     '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#date-from, #date-to').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#date-from').val('');
                $('#date-to').val('');
                table.ajax.reload();
            });
        });
    </script>
@endpush
