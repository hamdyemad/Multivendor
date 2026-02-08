@extends('layout.app')

@section('title', __('order::request-quotation.my_quotations'))

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
                    ['title' => __('order::request-quotation.my_quotations')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-file-question-alt me-2"></i>
                            {{ __('order::request-quotation.my_quotations') }}
                        </h4>
                    </div>

                    {{-- Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search_input" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i>
                                                {{ __('common.search') }}
                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search_input" placeholder="{{ __('order::request-quotation.search_placeholder') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('common.status') }}
                                            </label>
                                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="status_filter">
                                                <option value="all">{{ __('common.all') }}</option>
                                                <option value="pending">{{ __('order::request-quotation.vendor_status_pending') }}</option>
                                                <option value="offer_sent">{{ __('order::request-quotation.vendor_status_offer_sent') }}</option>
                                                <option value="offer_accepted">{{ __('order::request-quotation.vendor_status_offer_accepted') }}</option>
                                                <option value="offer_rejected">{{ __('order::request-quotation.vendor_status_offer_rejected') }}</option>
                                                <option value="order_created">{{ __('order::request-quotation.vendor_status_order_created') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_from') }}
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="created_date_from">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_to') }}
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-1 d-flex align-items-center">
                                        <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared me-1" title="{{ __('common.search') }}">
                                            <i class="uil uil-search"></i>
                                        </button>
                                        <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared" title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="quotationsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('order::request-quotation.quotation_number') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('order::request-quotation.customer_info') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('order::request-quotation.order_number') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.created_at') }}</span></th>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#quotationsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.vendor.request-quotations.datatable', ['lang' => app()->getLocale(), 'countryCode' => request()->route('countryCode') ?? 'eg']) }}',
                    data: function(d) {
                        d.status = $('#status_filter').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.search_text = $('#search_input').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'quotation_number', name: 'quotation_number', orderable: false, searchable: true },
                    { data: 'customer_info', name: 'customer_info', orderable: false, searchable: true },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                    { data: 'order_number', name: 'order_number', orderable: false, searchable: false },
                    { data: 'created_date', name: 'created_at', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']],
                language: {
                    lengthMenu: "{{ __('common.show') }} _MENU_",
                    info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.entries') }}",
                    infoEmpty: "{{ __('common.showing') }} 0 {{ __('common.to') }} 0 {{ __('common.of') }} 0 {{ __('common.entries') }}",
                    infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.total_entries') }})",
                    loadingRecords: "{{ __('common.loading') }}",
                    processing: "{{ __('common.processing') }}",
                    emptyTable: "{{ __('common.no_data_available') }}",
                    paginate: {
                        first: "{{ __('common.first') }}",
                        last: "{{ __('common.last') }}",
                        next: "{{ __('common.next') }}",
                        previous: "{{ __('common.previous') }}"
                    }
                },
                dom: 'lrtip',
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pageLength: 10
            });

            // Search button
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search_input').val('');
                $('#status_filter').val('all');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                table.ajax.reload();
            });

            // Search on Enter key
            $('#search_input').on('keypress', function(e) {
                if (e.which === 13) {
                    table.ajax.reload();
                }
            });
        });
    </script>
@endpush
