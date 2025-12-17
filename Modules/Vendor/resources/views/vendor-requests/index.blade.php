@extends('layout.app')

@section('title')
    {{ __('vendor::vendor.vendor_requests_management') }}
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
                    ['title' => __('vendor::vendor.vendor_requests_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-clipboard-notes me-2"></i>
                            {{ __('vendor::vendor.vendor_requests_management') }}
                        </h4>
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
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('vendor::vendor.search_by_email_or_company') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('common.status') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="pending">{{ __('common.pending') }}</option>
                                                <option value="approved">{{ __('common.approved') }}</option>
                                                <option value="rejected">{{ __('common.rejected') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
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
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('common.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('common.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
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
                        <table id="vendorRequestsDataTable" class="table mb-0 table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('vendor::vendor.company_information') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ trans('common.email') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('common.phone') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('common.status') }}</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('vendor::vendor.rejection_reason') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ trans('common.date') }}</span></th>
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

    {{-- Rejection Reason Modal --}}
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="confirmActionForm" method="POST">
                    @csrf
                    <div class="modal-header bg-danger bg-opacity-10 border-bottom">
                        <h5 class="modal-title" id="confirmActionLabel">
                            <i
                                class="uil uil-exclamation-triangle text-danger me-2"></i>{{ trans('vendor::vendor.reject_vendor_request') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="confirmMessage" class="text-muted mb-3">
                            {{ trans('vendor::vendor.confirm_reject_message') }}</p>
                        <div id="rejectReasonDiv">
                            <label for="rejectReason" class="form-label fw-600 mb-2">
                                <i class="uil uil-message-circle me-1"></i>{{ trans('vendor::vendor.rejection_reason') }}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control nockeditor" id="rejectReason" name="reason"
                                placeholder="{{ trans('vendor::vendor.rejection_reason_placeholder') }}" rows="4" required></textarea>
                            <small
                                class="text-muted d-block mt-2">{{ trans('vendor::vendor.rejection_reason_visible') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="uil uil-times me-1"></i>{{ trans('common.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger" id="confirmActionBtn">
                            <i class="uil uil-trash-alt me-1"></i>{{ trans('vendor::vendor.reject_request') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Vendor Request Details Modal --}}
    <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-opacity-10 border-bottom">
                    <h5 class="modal-title" id="viewRequestLabel">
                        <i
                            class="uil uil-info-circle text-primary me-2"></i>{{ trans('vendor::vendor.vendor_request_details') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Company Logo --}}
                        <div class="col-md-12 mb-3 text-center">
                            <div class="view-item">
                                <label
                                    class="il-gray fs-14 fw-500 mb-10 d-block">{{ trans('common.company_logo') ?? 'Company Logo' }}</label>
                                <img id="modalCompanyLogo" src="" alt="Company Logo"
                                    style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px; display: none;">
                                <div id="modalCompanyLogoPlaceholder"
                                    style="width: 200px; height: 200px; background-color: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i class="uil uil-image" style="font-size: 48px; color: #999;"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Company Information --}}
                        <div class="col-md-6 mb-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.company_name') }}</label>
                                <p class="fs-15 color-dark" id="modalCompanyName">-</p>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6 mb-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.email') }}</label>
                                <p class="fs-15 color-dark" id="modalEmail">-</p>
                            </div>
                        </div>

                        {{-- Phone (under Email) --}}
                        <div class="col-md-6 mb-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.phone') }}</label>
                                <p class="fs-15 color-dark" id="modalPhone">-</p>
                            </div>
                        </div>

                        {{-- Manager Name --}}
                        <div class="col-md-6 mb-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.manager_name') }}</label>
                                <p class="fs-15 color-dark" id="modalManagerName">-</p>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.status') }}</label>
                                <p class="fs-15" id="modalStatus">-</p>
                            </div>
                        </div>

                        {{-- Rejection Reason (if rejected) --}}
                        <div class="col-md-12 mb-3" id="rejectionReasonDiv" style="display: none;">
                            <div class="view-item">
                                <label
                                    class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.rejection_reason') }}</label>
                                <p class="fs-15 color-dark" id="modalRejectionReason">-</p>
                            </div>
                        </div>

                        {{-- Created At --}}
                        <div class="col-md-6 mb-3">
                            <div class="view-item">
                                <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                <p class="fs-15 color-dark" id="modalCreatedAt">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="uil uil-times me-1"></i>{{ trans('common.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Vendor Requests page loaded, initializing DataTable...');

            let per_page = 10;

            // Get filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Populate filters from URL parameters on page load
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('status')) $('#status').val(urlParams.get('status'));
            if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
            if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

            // Function to update URL with current filters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();

                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#status').val()) params.set('status', $('#status').val());
                if ($('#created_date_from').val()) params.set('created_date_from', $('#created_date_from').val());
                if ($('#created_date_to').val()) params.set('created_date_to', $('#created_date_to').val());

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }

            // Server-side processing with pagination
            let table = $('#vendorRequestsDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '{{ route('admin.vendor-requests.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        // Add filter parameters
                        d.search = $('#search').val();
                        d.status = $('#status').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.orderColumnIndex = d.order[0].column;
                            d.orderDirection = d.order[0].dir;
                        }
                        console.log('📤 Sending request:', d);
                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('📦 Data received from server:', json);
                        console.log('Total records:', json.total);
                        console.log('Filtered records:', json.recordsFiltered);
                        console.log('Current page:', json.current_page);

                        // Map backend response to DataTables format
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;

                        if (json.error) {
                            console.error('❌ Server returned error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        if (!json.data || json.data.length === 0) {
                            console.warn('⚠️ No data returned from server');
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ DataTables AJAX Error:', {
                            xhr: xhr,
                            error: error,
                            code: code
                        });
                        console.error('Response Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);

                        // Try to parse JSON error
                        let errorMsg = 'Error loading data. Status: ' + xhr.status;
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                console.error('❌ Server Error:', response.error);
                                errorMsg += '\n\nError: ' + response.error;
                                if (response.trace) {
                                    console.error('Stack Trace:', response.trace);
                                }
                            }
                        } catch (e) {
                            console.error('Could not parse error response');
                        }

                        alert(errorMsg + '\n\nCheck console for full details.');
                    }
                },
                columns: [
                    // Row number column
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return row.row_number
                        }
                    },
                    // Company Information column
                    {
                        data: null,
                        name: 'company_info',
                        orderable: false,
                        render: function(data, type, row) {
                            let logoHtml = '';
                            if (row.company_logo) {
                                logoHtml =
                                    `<img src="${row.company_logo}" alt="Company Logo" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 12px;">`;
                            } else {
                                logoHtml =
                                    `<div style="width: 60px; height: 60px; background-color: #e9ecef; border-radius: 8px; margin-right: 12px; display: flex; align-items: center; justify-content: center;"><i class="uil uil-image" style="font-size: 24px; color: #999;"></i></div>`;
                            }

                            let html = `
                                <div class="vendor-card p-2 bg-light-subtle rounded-3">
                                    <div class="d-flex align-items-center">
                                        ${logoHtml}
                                        <div style="width: 100%;">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">{{ trans('common.company_name') }}:</small>
                                                <div class="fw-semibold text-dark text-capitalize">
                                                    ${$('<div>').text(row.company_name).html()}
                                                </div>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">{{ trans('common.manager_name') }}:</small>
                                                <small class="text-dark">${row.manager_name ? $('<div>').text(row.manager_name).html() : '-'}</small>
                                            </div>
                                        </div>
                                    </div>
                            `;
                            html += `</div></div>`;
                            return html;
                        }
                    },
                    // Email column
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<span class="text-dark">${$('<div>').text(row.email).html()}</span>`;
                        }
                    },
                    // Phone column
                    {
                        data: 'phone',
                        name: 'phone',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<span class="text-dark">${row.phone}</span>`;
                        }
                    },
                    // Status column
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        render: function(data, type, row) {
                            const statusColors = {
                                'pending': 'warning',
                                'approved': 'success',
                                'rejected': 'danger'
                            };
                            const color = statusColors[row.status] || 'secondary';
                            return `<span class="badge bg-${color} badge-lg badge-round text-capitalize">${row.status}</span>`;
                        }
                    },

                    // Rejection Reason column
                    {
                        data: 'rejection_reason',
                        name: 'rejection_reason',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!data || data.trim() === '') {
                                return '<span class="text-muted">-</span>';
                            }
                            return `<span title="${$('<div>').text(data).html()}">${$('<div>').text(data).html()}</span>`;
                        }
                    },

                    // Date column
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        render: function(data, type, row) {
                            return row.created_at;
                        }
                    },

                    // Actions column
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let actions =
                                `<div class="orderDatatable_actions d-inline-flex gap-1 text-center justify-content-center">`;

                            // View button (always show)
                            actions += `
                            <a href="javascript:void(0);"
                            class="view btn btn-info table_action_father view-request-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#viewRequestModal"
                            data-request-id="${row.id}"
                            data-company-name="${row.company_name}"
                            data-email="${row.email}"
                            data-phone="${row.phone}"
                            data-manager-name="${row.manager_name || ''}"
                            data-company-logo="${row.company_logo || ''}"
                            data-status="${row.status}"
                            data-created-at="${row.created_at}"
                            data-rejection-reason="${row.rejection_reason || ''}"
                            title="View Details">
                                <i class="uil uil-eye table_action_icon"></i>
                            </a>
                            `;

                            // Pending: Show Create Vendor and Reject buttons
                            if (row.status === 'pending') {
                                // Build query params for vendor creation
                                const params = new URLSearchParams({
                                    vendor_request_id: row.id
                                });

                                actions += `
                                <a href="{{ route('admin.vendors.create') }}?${params.toString()}"
                                class="create-vendor btn btn-primary table_action_father"
                                title="Create Vendor from Request">
                                    <i class="uil uil-plus table_action_icon"></i>
                                </a>
                                <a href="javascript:void(0);"
                                class="reject btn btn-danger table_action_father reject-btn"
                                data-id="${row.id}"
                                title="Reject">
                                    <i class="uil uil-times table_action_icon"></i>
                                </a>
                                `;
                            }
                            // Approved: Show Reject and Archive buttons
                            else if (row.status === 'approved') {
                                actions += ``;
                            }
                            // Rejected: Show Archive button only
                            else if (row.status === 'rejected') {
                                actions += ``;
                            }

                            actions += `</div>`;
                            return actions;
                        }
                    }
                ],
                pageLength: per_page,
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
                searching: false, // Disable built-in search (using custom)
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "No vendor requests found",
                    emptyTable: "No vendor requests found",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
                }
            });

            // Initialize Select2 on filter dropdowns
            if ($.fn.select2) {
                $('#entriesSelect, #status').select2({
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

            // Real-time search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    updateUrlWithFilters();
                    table.ajax.reload();
                }, 500);
            });

            // Search button click handler
            $('#searchBtn').on('click', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Filter change handlers - real-time filtering
            $('#status').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Date filter change handlers
            $('#created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Export Excel button
            $('#exportExcel').on('click', function() {
                alert('{{ __('common.export_excel') }} feature coming soon');
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#status').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Update URL and reload table
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Handle view request button click
            $(document).on('click', '.view-request-btn', function(e) {
                e.preventDefault();

                // Get data from button attributes
                const companyName = $(this).data('company-name');
                const email = $(this).data('email');
                const phone = $(this).data('phone');
                const managerName = $(this).data('manager-name');
                const companyLogo = $(this).data('company-logo');
                const status = $(this).data('status');
                const createdAt = $(this).data('created-at');
                const rejectionReason = $(this).data('rejection-reason');

                // Populate modal fields
                $('#modalCompanyName').text(companyName || '-');
                $('#modalEmail').text(email || '-');
                $('#modalPhone').text(phone || '-');
                $('#modalManagerName').text(managerName || '-');
                $('#modalCreatedAt').text(createdAt || '-');

                // Handle company logo
                if (companyLogo) {
                    $('#modalCompanyLogo').attr('src', companyLogo).show();
                    $('#modalCompanyLogoPlaceholder').hide();
                } else {
                    $('#modalCompanyLogo').hide();
                    $('#modalCompanyLogoPlaceholder').show();
                }

                // Set status badge
                let statusBadge = '-';
                let statusText = '-';
                if (status === 'pending') {
                    statusText = '{{ trans('common.pending') }}';
                    statusBadge = '<span class="badge badge-warning badge-round badge-lg">' + statusText +
                        '</span>';
                } else if (status === 'approved') {
                    statusText = '{{ trans('common.approved') }}';
                    statusBadge = '<span class="badge badge-success badge-round badge-lg">' + statusText +
                        '</span>';
                } else if (status === 'rejected') {
                    statusText = '{{ trans('common.rejected') }}';
                    statusBadge = '<span class="badge badge-danger badge-round badge-lg">' + statusText +
                        '</span>';
                }
                $('#modalStatus').html(statusBadge);

                // Show rejection reason only if status is rejected
                if (status === 'rejected' && rejectionReason) {
                    $('#rejectionReasonDiv').show();
                    $('#modalRejectionReason').text(rejectionReason);
                } else {
                    $('#rejectionReasonDiv').hide();
                }
            });

            // Handle reject button click
            $(document).on('click', '.reject-btn', function(e) {
                e.preventDefault();
                const vendorRequestId = $(this).data('id');

                // Reset form
                $('#confirmActionForm')[0].reset();
                $('#rejectReason').val('');

                // Set form action - construct URL dynamically
                const baseUrl = '{{ route('admin.vendor-requests.index') }}';
                const actionUrl = baseUrl.replace('vendor-requests', 'vendor-requests/' + vendorRequestId +
                    '/reject');
                $('#confirmActionForm').attr('action', actionUrl);

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
                modal.show();
            });

            // Handle form submission
            $('#confirmActionForm').on('submit', function(e) {
                e.preventDefault();

                const reason = $('#rejectReason').val().trim();
                const actionUrl = $(this).attr('action');

                // Validate reason
                if (!reason || reason.length === 0) {
                    toastr.warning('Please provide a rejection reason', 'Validation Error');
                    return false;
                }

                // Disable submit button during request
                const submitBtn = $('#confirmActionBtn');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="uil uil-spinner-alt me-1"></i>Processing...');

                // Submit form via AJAX
                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: {
                        _token: '{{ csrf_token() }}',
                        reason: reason
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('✅ Rejection successful:', response);

                        // Close modal
                        const modalElement = document.getElementById('confirmActionModal');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }

                        // Show success message
                        toastr.success(response.message ||
                            'Vendor request rejected successfully', 'Success');

                        // Reload table
                        setTimeout(function() {
                            table.ajax.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Rejection failed:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });

                        let errorMsg = 'Error rejecting vendor request';
                        try {
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                errorMsg = Object.values(xhr.responseJSON.errors).flat().join(
                                    ', ');
                            }
                        } catch (e) {
                            if (xhr.status === 404) {
                                errorMsg = 'Vendor request not found';
                            } else if (xhr.status === 500) {
                                errorMsg = 'Server error. Please try again later.';
                            } else if (xhr.status === 422) {
                                errorMsg = 'Validation error. Please provide a valid reason.';
                            }
                        }
                        toastr.error(errorMsg, 'Error');
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

        });
    </script>
@endpush
