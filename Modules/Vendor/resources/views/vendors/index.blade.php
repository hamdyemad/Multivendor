@extends('layout.app')

@section('title')
    Vendors | Bnaia
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
                    ['title' => __('vendor::vendor.vendors_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('vendor::vendor.vendors_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.vendors.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('vendor::vendor.add_vendor') }}
                            </a>
                        </div>
                    </div>

                    <div class="alert alert-info glowing-alert" role="alert">
                        As soon as you type anything, the search will be performed instantly (live search).
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
                                                <small
                                                    class="text-muted">({{ __('common.real_time') ?? 'Real-time' }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="{{ __('vendor::vendor.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('vendor::vendor.status') ?? 'Status' }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('vendor::vendor.all') ?? 'All' }}</option>
                                                <option value="1">{{ __('vendor::vendor.active') ?? 'Active' }}
                                                </option>
                                                <option value="0">{{ __('vendor::vendor.inactive') ?? 'Inactive' }}
                                                </option>
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

                                    <div class="col-md-6">
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared w-100"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt m-0"></i> Export Excel Sheet
                                        </button>
                                    </div>

                                    <div class="col-md-6">
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared w-100"
                                            title="{{ __('common.reset') ?? 'Reset' }}">
                                            <i class="uil uil-redo m-0"></i> Reset Search Form
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
                        <table id="vendorsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span
                                            class="userDatatable-title">{{ __('vendor::vendor.vendor_information') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('vendor::vendor.commission') }}</span></th>
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
    {{-- Delete Confirmation Modal Component --}}
    <x-delete-modal modalId="modal-delete-vendor" :title="__('vendor::vendor.confirm_delete')" :message="__('vendor::vendor.delete_confirmation')" itemNameId="delete-vendor-name"
        confirmBtnId="confirmDeleteVendorBtn" :deleteRoute="route('admin.vendors.index')" :cancelText="__('common.cancel') ?? 'Cancel'" :deleteText="__('vendor::vendor.delete_vendor')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Vendors page loaded, initializing DataTable...');

            let per_page = 10;

            // Server-side processing with pagination
            let table = $('#vendorsDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '{{ route('admin.vendors.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        // Add filter parameters
                        d.search = $('#search').val();
                        d.active = $('#active').val();
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
                    // Vendor Information column (combined)
                    {
                        data: null,
                        name: 'vendor_info',
                        orderable: false,
                        render: function(data, type, row) {
                            let html = `
                                <div class="vendor-card p-2 bg-light-subtle">
                                    <div class="fw-bold text-capitalize mb-1 text-dark">

                            `;

                            // English Name only
                            const nameEn = row.translations && row.translations['en'] ? row
                                .translations['en'].name : '-';
                            html += `
                                <div class="d-flex align-items-center small" dir="ltr">
                                    <span class="fw-semibold text-dark"><i class="uil uil-user me-1 text-primary"></i>${$('<div>').text(nameEn).html()}</span>
                                    <small class="text-muted ms-1">(English)</small>
                                </div>
                            `;

                            html += `
                                    </div>
                                    <div class="vendor-details mt-2">
                                        <div class="d-flex align-items-center mb-1 text-secondary">
                                            <i class="uil uil-envelope-alt me-1 text-info"></i>
                                            <span class="small">${$('<div>').text(row.email || '-').html()}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-1 text-secondary">
                                            <i class="uil uil-map-marker me-1 text-danger"></i>
                                            <span class="small">${$('<div>').text(row.country_name || '-').html()}</span>
                                        </div>
                            `;

                            // Status Badge
                            if (row.active == 1) {
                                html += `
                                    <div class="mt-2">
                                        <span class="badge bg-success-subtle text-success border border-success px-3 py-1 rounded-pill">
                                            <i class="uil uil-check-circle me-1"></i> {{ trans('vendor::vendor.active') }}
                                        </span>
                                    </div>
                                `;
                            } else {
                                html += `
                                    <div class="mt-2">
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-1 rounded-pill">
                                            <i class="uil uil-times-circle me-1"></i> {{ trans('vendor::vendor.inactive') }}
                                        </span>
                                    </div>
                                `;
                            }

                            html += `</div></div>`;
                            return html;
                        }
                    },
                    // Commission column
                    {
                        data: 'commission',
                        name: 'commission',
                        orderable: true,
                        render: function(data) {
                            return '<div class="userDatatable-content text-center"><span class="badge badge-success badge-round badge-lg">' +
                                data + '%</span></div>';
                        }
                    },
                    // Actions column
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <div class="orderDatatable_actions d-inline-flex gap-1">
                                @can('vendors.show')
                                <a href="{{ url('admin/vendors') }}/${row.id}"
                                class="view btn btn-warning table_action_father"
                                title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                @endcan

                                @can('vendors.edit')
                                <a href="{{ url('admin/vendors') }}/${row.id}/edit"
                                class="edit btn btn-info table_action_father"
                                title="{{ trans('common.edit') }}">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>
                                @endcan

                                @can('vendors.delete')
                                <a href="javascript:void(0);"
                                class="remove delete-vendor btn btn-danger table_action_father"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-delete-vendor"
                                data-item-id="${row.id}"
                                data-item-name="${$('<div>').text(row.first_name).html()}"
                                title="{{ trans('common.delete') }}">
                                    <i class="uil uil-trash-alt table_action_icon"></i>
                                </a>
                                @endcan
                            </div>`;

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
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ __('vendor::vendor.vendors_management') }}'
                }],
                searching: false, // Disable built-in search (using custom)
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('vendor::vendor.no_vendors_found') ?? 'No vendors found' }}",
                    emptyTable: "{{ __('vendor::vendor.no_vendors_found') ?? 'No vendors found' }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
                    paginate: {
                        first: '{{ __('common.first') ?? 'First' }}',
                        last: '{{ __('common.last') ?? 'Last' }}',
                        next: '{{ __('common.next') ?? 'Next' }}',
                        previous: '{{ __('common.previous') ?? 'Previous' }}'
                    },
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
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

            // Search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
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
                // Reload table
                table.ajax.reload();
            });

            // Delete vendor
            $('#confirmDeleteVendorBtn').on('click', function() {
                const vendorId = $(this).data('item-id');
                const deleteUrl = '{{ route('admin.vendors.index') }}/' + vendorId;

                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#modal-delete-vendor').modal('hide');
                        table.ajax.reload();
                        showNotification('success', response.message ||
                            'Vendor deleted successfully');
                    },
                    error: function(xhr) {
                        showNotification('error', xhr.responseJSON?.message ||
                            'Error deleting vendor');
                    }
                });
            });

            // Set delete modal data
            $(document).on('click', '.remove', function() {
                const itemId = $(this).data('item-id');
                const itemName = $(this).data('item-name');
                $('#delete-vendor-name').text(itemName);
                $('#confirmDeleteVendorBtn').data('item-id', itemId);
            });
        });

        function showNotification(type, message) {
            // Implement your notification system here
            alert(message);
        }
    </script>
@endpush
