@extends('layout.app')

@section('title')
    {{ __('vendor::vendor.vendors') }}
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
                            @can('vendors.create')
                            <a href="{{ route('admin.vendors.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('vendor::vendor.add_vendor') }}
                            </a>
                            @endcan
                        </div>
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
                                                id="search" placeholder="{{ __('common.search') }}" autocomplete="off">
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
                        <table id="vendorsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ __('vendor::vendor.vendor_information') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('vendor::vendor.departments') ?? 'Departments' }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('vendor::vendor.products_count') ?? 'Products' }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('vendor::vendor.active_status') }}</span>
                                    </th>
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

            // Get filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Populate filters from URL parameters on page load
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
            if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

            // Function to update URL with current filters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();

                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#created_date_from').val()) params.set('created_date_from', $('#created_date_from').val());
                if ($('#created_date_to').val()) params.set('created_date_to', $('#created_date_to').val());

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }

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
                    // Hex Number column
                    {
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row) {
                            return row.row_number
                        }
                    },
                    // Vendor Information column (combined)
                    {
                        data: null,
                        name: 'vendor_info',
                        orderable: false,
                        render: function(data, type, row) {
                            console.log('DDDDDDDDDDDDDDDDDDDDDDDDDDDDDd', row);

                            // English Name only
                            const nameEn = row.translations && row.translations['en'] ?
                                row.translations['en'].name :
                                '-';

                            // Vendor Email
                            const email = row.email || '-';

                            // Vendor Logo
                            let logoHtml = '';
                            if (row.logo_url) {
                                logoHtml = `<img src="${row.logo_url}" alt="${$('<div>').text(nameEn).html()}" class="rounded-circle me-3" style="width: 50px; height: 50px;">`;
                            } else {
                                logoHtml = `<div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">${nameEn.charAt(0).toUpperCase()}</div>`;
                            }

                            let html = `
                                <div class="vendor-card p-2 bg-light-subtle rounded-3">
                                    <div class="d-flex align-items-center">
                                        ${logoHtml}
                                        <div class="d-flex flex-column">
                                            <div class="fw-semibold text-dark text-capitalize mb-1">
                                                ${$('<div>').text(nameEn).html()}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="uil uil-envelope me-1"></i>${$('<div>').text(email).html()}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            return html;
                        }
                    },

                    // Departments column
                    {
                        data: 'departments',
                        name: 'departments',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data || !Array.isArray(data) || data.length === 0) {
                                return '<span class="text-muted">-</span>';
                            }

                            const displayLimit = 2;
                            let visibleHtml = '';
                            let hiddenHtml = '';
                            const uniqueId = `depts-${row.id}`;

                            data.forEach((d, index) => {
                                const badge =
                                    `<span class="badge badge-round badge-lg badge-primary mb-1 me-1">${d.name || '-'}</span>`;
                                if (index < displayLimit) {
                                    visibleHtml += badge;
                                } else {
                                    hiddenHtml += badge;
                                }
                            });

                            if (data.length > displayLimit) {
                                const remainingCount = data.length - displayLimit;
                                visibleHtml +=
                                    `<div id="hidden-${uniqueId}" style="display: none; margin-top: 5px;">${hiddenHtml}</div>`;
                                visibleHtml +=
                                    `<a href="javascript:void(0);" class="show-more-depts badge badge-round badge-lg badge-success" data-target="#hidden-${uniqueId}">+${remainingCount} more</a>`;
                            }

                            return `<div class="department-list">${visibleHtml}</div>`;
                        }
                    },

                    // Products Count column
                    {
                        data: 'products_count',
                        name: 'products_count',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<div class="text-center">
                                <span class="badge badge-round badge-lg badge-info">
                                    <i class="uil uil-box me-1"></i>${data || 0}
                                </span>
                            </div>`;
                        }
                    },

                    // Active Status column
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        render: function(data, type, row) {
                            let checked = data ? 'checked' : '';
                            @can('vendors.change-status')
                            return `<div class="userDatatable-content">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input status-switch" type="checkbox"
                                        data-id="${row.id}" ${checked} style="cursor: pointer; width: 40px; height: 20px;">
                                </div>
                            </div>`;
                            @else
                            return data 
                                ? '<span class="badge badge-round bg-success">{{ trans("vendor::vendor.active") }}</span>'
                                : '<span class="badge badge-round bg-danger">{{ trans("vendor::vendor.inactive") }}</span>';
                            @endcan
                        }
                    },
                    // Actions column
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let viewUrl = "{{ route('admin.vendors.show', ':id') }}".replace(':id',
                                row.id);
                            let editUrl = "{{ route('admin.vendors.edit', ':id') }}".replace(':id',
                                row.id);
                            return `
                            <div class="orderDatatable_actions d-inline-flex gap-1">
                                @can('vendors.show')
                                <a href="${viewUrl}"
                                class="view btn btn-primary table_action_father"
                                title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                @endcan

                                @can('vendors.edit')
                                <a href="${editUrl}"
                                class="edit btn btn-warning table_action_father"
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
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
                }
            });

            // Initialize Select2 on filter dropdowns
            if ($.fn.select2) {
                $('#entriesSelect, #active').select2({
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
            $('#active').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Date filter change handlers
            $('#created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#active').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Update URL and reload table
                updateUrlWithFilters();
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
                        $('#modal-delete-vendor').modal('hide');
                        
                        // Get error message - check both message and error fields
                        let errorMessage = 'Error deleting vendor';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        }
                        
                        // Use toastr if available, otherwise use showNotification
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage, '{{ __('common.error') }}', {
                                timeOut: 8000,
                                closeButton: true,
                                progressBar: true
                            });
                        } else {
                            showNotification('error', errorMessage);
                        }
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

            // Status switch handler
            // Handle "Show more" for departments
            $('#vendorsDataTable tbody').on('click', '.show-more-depts', function(e) {
                e.preventDefault();
                const $this = $(this);
                const targetSelector = $this.data('target');
                const $target = $(targetSelector);

                $target.slideToggle(200); // A bit of animation

                if ($this.text().includes('more')) {
                    $this.text('Show less');
                } else {
                    const remainingCount = $target.children().length;
                    $this.text(`+${remainingCount} more`);
                }
            });

            // Status switch handler
            $(document).on('change', '.status-switch', function() {
                const $switch = $(this);
                const vendorId = $switch.data('id');
                const originalState = !$switch.is(':checked');

                $.ajax({
                    url: '{{ route('admin.vendors.change-status', '__id__') }}'.replace('__id__',
                        vendorId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            }
                        } else {
                            $switch.prop('checked', originalState);
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        $switch.prop('checked', originalState);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('{{ __('vendor::vendor.error_changing_status') }}');
                        }
                    }
                });
            });
        });

        function showNotification(type, message) {
            // Use the global showMessage function from app.blade.php
            if (typeof showMessage === 'function') {
                const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
                showMessage(type, message, icon);
            } else {
                // Fallback to alert if showMessage is not available
                alert(message);
            }
        }
    </script>
@endpush
