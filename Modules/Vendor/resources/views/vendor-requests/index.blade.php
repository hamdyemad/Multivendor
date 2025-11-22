@extends('layout.app')

@section('title')
    Vendor Requests | Bnaia
@endsection

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
                    ['title' => 'Vendor Requests'],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">Vendor Requests Management</h4>
                    </div>

                    <div class="alert alert-info glowing-alert" role="alert">
                        {{ __('common.live_search_info') }}
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                                <small class="text-muted">({{ __('common.real_time') ?? 'Real-time' }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="Search by email or company name"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                Status
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status">
                                                <option value="">All</option>
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
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

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="vendorRequestsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">Company Information</span></th>
                                    <th><span class="userDatatable-title">Contact</span></th>
                                    <th><span class="userDatatable-title">Activities</span></th>
                                    <th><span class="userDatatable-title">Status</span></th>
                                    <th><span class="userDatatable-title">Date</span></th>
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
            let per_page = 10;

            let table = $('#vendorRequestsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.vendor-requests.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.status = $('#status').val();
                        if (d.order && d.order.length > 0) {
                            d.orderColumnIndex = d.order[0].column;
                            d.orderDirection = d.order[0].dir;
                        }
                        return d;
                    },
                    dataSrc: function(json) {
                        json.recordsTotal = json.total || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;
                        return json.data || [];
                    }
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row) {
                            return row.row_number;
                        }
                    },
                    {
                        data: null,
                        name: 'company_info',
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="vendor-card p-2 bg-light-subtle rounded-3">
                                    <div class="fw-semibold text-dark">${row.company_name}</div>
                                    <small class="text-muted">${row.email}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<span>${row.phone}</span>`;
                        }
                    },
                    {
                        data: 'activities',
                        name: 'activities',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!row.activities || row.activities.length === 0) {
                                return '<span class="badge bg-secondary">None</span>';
                            }
                            return row.activities.map(a => `<span class="badge bg-info">${a.name}</span>`).join(' ');
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        render: function(data, type, row) {
                            const statusColors = {
                                'pending': 'warning',
                                'approved': 'success',
                                'rejected': 'danger'
                            };
                            const color = statusColors[row.status] || 'secondary';
                            return `<span class="badge bg-${color} text-capitalize">${row.status}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        render: function(data, type, row) {
                            return row.created_at;
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        render: function(data, type, row) {
                            let actions = `
                                <div class="btn-group" role="group">
                                    <a href="#" class="btn btn-sm btn-info" title="View">
                                        <i class="uil uil-eye"></i>
                                    </a>
                            `;

                            if (row.status === 'pending') {
                                actions += `
                                    <a href="#" class="btn btn-sm btn-success approve-btn" data-id="${row.id}" title="Approve">
                                        <i class="uil uil-check"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger reject-btn" data-id="${row.id}" title="Reject">
                                        <i class="uil uil-times"></i>
                                    </a>
                                `;
                            }

                            actions += `
                                    <a href="#" class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" title="Delete">
                                        <i class="uil uil-trash-alt"></i>
                                    </a>
                                </div>
                            `;
                            return actions;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[5, 'desc']]
            });

            // Filter triggers
            $('#search, #status').on('keyup change', function() {
                table.draw();
            });

            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#status').val('');
                table.draw();
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });
        });
    </script>
@endpush
