@extends('layout.app')
@section('title')
    {{ __('customer::customer.customers_management') }}
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
                    ['title' => __('customer::customer.customers_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('customer::customer.customers_management') }}</h4>
                        <div class="d-flex gap-2">
                            @can('customers.create')
                            <a href="{{ route('admin.customers.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('customer::customer.add_customer') }}
                            </a>
                            @endcan
                        </div>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    {{-- Search --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('customer::customer.search') }}
                                                <small class="text-muted">({{ __('customer::customer.real_time') }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('customer::customer.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="active"
                                            id="active"
                                            :label="__('customer::customer.status')"
                                            icon="uil uil-check-circle"
                                            :placeholder="__('customer::customer.all_status')"
                                            :options="[
                                                ['id' => '1', 'name' => __('customer::customer.active')],
                                                ['id' => '0', 'name' => __('customer::customer.inactive')]
                                            ]"
                                        />
                                    </div>

                                    {{-- Email Verified --}}
                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="email_verified"
                                            id="email_verified"
                                            :label="__('customer::customer.email_verified')"
                                            icon="uil uil-envelope-check"
                                            :placeholder="__('customer::customer.all')"
                                            :options="[
                                                ['id' => '1', 'name' => __('customer::customer.verified')],
                                                ['id' => '0', 'name' => __('customer::customer.not_verified')]
                                            ]"
                                        />
                                    </div>

                                    @if(isAdmin())
                                    {{-- Vendor Filter --}}
                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="vendor_filter"
                                            id="vendor_filter"
                                            :label="__('customer::customer.created_by_vendor')"
                                            icon="uil uil-store"
                                            :placeholder="__('customer::customer.all_vendors')"
                                            :options="collect($vendors)->map(fn($v) => ['id' => $v['id'], 'name' => $v['name']])->toArray()"
                                        />
                                    </div>
                                    @endif

                                    {{-- City Filter --}}
                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="city_filter"
                                            id="city_filter"
                                            :label="__('customer::customer.city')"
                                            icon="uil uil-map-pin"
                                            :placeholder="__('main.choose')"
                                            :options="[]"
                                        />
                                    </div>

                                    {{-- Region Filter --}}
                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="region_filter"
                                            id="region_filter"
                                            :label="__('customer::customer.region')"
                                            icon="uil uil-map-marker"
                                            :placeholder="__('main.choose')"
                                            :options="[]"
                                        />
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('customer::customer.created_date_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    {{-- Created Date To --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('customer::customer.created_date_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('customer::customer.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('customer::customer.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('customer::customer.reset_filters') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('customer::customer.reset_filters') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('customer::customer.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('customer::customer.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="customersDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('customer::customer.customer_information') }}</span></th>
                                    @if(isAdmin())
                                    <th><span class="userDatatable-title">{{ __('customer::customer.created_by_vendor') }}</span></th>
                                    @endif
                                    <th><span class="userDatatable-title">{{ __('customer::customer.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('customer::customer.email_verified') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('customer::customer.created_at') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('customer::customer.action') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <x-delete-modal modalId="modal-delete-customer" :title="__('customer::customer.confirm_delete')" :message="__('customer::customer.delete_confirmation')" itemNameId="delete-customer-name"
        confirmBtnId="confirmDeleteCustomerBtn" deleteRoute="{{ rtrim(route('admin.customers.index'), '/') }}" :cancelText="__('customer::customer.cancel')" :deleteText="__('customer::customer.delete_customer')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        // Function to get URL parameter
        function getUrlParameter(name) {
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Load cities from session country on page load
        const sessionCountryId = $("meta[name='current_country_id']").attr('content');
        if (sessionCountryId) {
            fetch(`/api/area/countries/${sessionCountryId}/cities`, {
                method: 'GET',
                headers: {
                    'lang': "{{ app()->getLocale() }}"
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.data && Array.isArray(data.data)) {
                        const cityOptions = data.data.map(city => ({
                            id: city.id,
                            name: city.name
                        }));
                        CustomSelect.setOptions('city_filter', cityOptions, "{{ __('main.choose') }}");
                    }
                    // After cities are loaded, set city value from URL params
                    const cityIdFromUrl = getUrlParameter('city_id');
                    if (cityIdFromUrl) {
                        CustomSelect.setValue('city_filter', cityIdFromUrl);
                        // Trigger change to load regions
                        document.getElementById('city_filter').dispatchEvent(new CustomEvent('change', { 
                            detail: { value: cityIdFromUrl },
                            bubbles: true
                        }));
                    }
                })
                .catch(error => console.error('Error loading cities:', error));
        }

        // Handle city filter change to load regions
        document.getElementById('city_filter').addEventListener('change', function(e) {
            const cityId = e.detail ? e.detail.value : CustomSelect.getValue('city_filter');

            // Clear region select
            CustomSelect.setOptions('region_filter', [], "{{ __('main.choose') }}");

            if (cityId) {
                // Fetch regions for selected city
                fetch(`/api/area/cities/${cityId}/regions`, {
                    method: 'GET',
                    headers: {
                        'lang': "{{ app()->getLocale() }}"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.data && Array.isArray(data.data)) {
                            const regionOptions = data.data.map(region => ({
                                id: region.id,
                                name: region.name
                            }));
                            CustomSelect.setOptions('region_filter', regionOptions, "{{ __('main.choose') }}");
                        }
                        // After regions are loaded, set region value from URL params
                        const regionIdFromUrl = getUrlParameter('region_id');
                        if (regionIdFromUrl) {
                            CustomSelect.setValue('region_filter', regionIdFromUrl);
                        }
                    })
                    .catch(error => console.error('Error loading regions:', error));
            }
        });




        $(document).ready(function() {
            console.log('Customers page loaded, initializing DataTable...');

            // Permission flags
            const canChangeStatus = @json(auth()->user()->can('customers.change-status'));
            const canChangeVerification = @json(auth()->user()->can('customers.change-verification'));
            const canShow = @json(auth()->user()->can('customers.show'));
            const canEdit = @json(auth()->user()->can('customers.edit'));
            const canDelete = @json(auth()->user()->can('customers.delete'));

            let per_page = 10;
            let table = $('#customersDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.customers.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.active = CustomSelect.getValue('active');
                        d.email_verified = CustomSelect.getValue('email_verified');
                        d.search = $('#search').val();
                        d.city_id = CustomSelect.getValue('city_filter');
                        d.region_id = CustomSelect.getValue('region_filter');
                        d.vendor_id = document.getElementById('vendor_filter') ? CustomSelect.getValue('vendor_filter') : '';
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        if (d.order && d.order.length > 0) {
                            d.order_column = d.order[0].column;
                            d.order_dir = d.order[0].dir;
                        }
                        console.log('📤 Sending request:', d);
                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('📦 Data received from server:', json);
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
                        alert('Error loading data. Status: ' + xhr.status + '. Check console for details.');
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
                    {
                        data: 'customer_info',
                        name: 'customer_info',
                        orderable: false,
                        render: function(data, type, row) {
                            let info = '<div class="userDatatable-content">';
                            info += '<div class="mb-2"><strong>' + (row.full_name || '-') + '</strong></div>';
                            info += '<div class="mb-2" style="text-transform: lowercase;"><strong>{{ __("customer::customer.email") }}:</strong> ' + (row.email || '-') + '</div>';
                            if (row.phone) {
                                info += '<div class="mb-2"><strong>{{ __("customer::customer.phone") }}:</strong> ' + row.phone + '</div>';
                            }
                            if (row.city_name && row.city_name !== '-') {
                                info += '<div class="mb-2"><strong>{{ __("customer::customer.city") }}:</strong> ' + row.city_name + '</div>';
                            }
                            if (row.region_name && row.region_name !== '-') {
                                info += '<div class="mb-2"><strong>{{ __("customer::customer.region") }}:</strong> ' + row.region_name + '</div>';
                            }
                            info += '</div>';
                            return info;
                        }
                    },
                    @if(isAdmin())
                    {
                        data: 'vendor_name',
                        name: 'vendor_name',
                        orderable: false,
                        render: function(data, type, row) {
                            if (data) {
                                return '<div class="userDatatable-content"><span class="badge badge-primary badge-round">' + data + '</span></div>';
                            } else {
                                return '<div class="userDatatable-content"><span class="badge badge-light badge-round">{{ __("customer::customer.no_vendor") }}</span></div>';
                            }
                        }
                    },
                    @endif
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!row.can_manage || !canChangeStatus) {
                                // Show status badge only (no toggle) for customers vendor cannot manage or no permission
                                if (data) {
                                    return '<div class="userDatatable-content"><span class="badge badge-success badge-round">{{ __("customer::customer.active") }}</span></div>';
                                } else {
                                    return '<div class="userDatatable-content"><span class="badge badge-danger badge-round">{{ __("customer::customer.inactive") }}</span></div>';
                                }
                            }
                            let checked = data ? 'checked' : '';
                            return `<div class="userDatatable-content">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input status-switch" type="checkbox"
                                        data-id="${row.id}" ${checked} style="cursor: pointer; width: 40px; height: 20px;">
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: 'email_verified_at',
                        name: 'email_verified_at',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!row.can_manage || !canChangeVerification) {
                                // Show verification badge only (no toggle) for customers vendor cannot manage or no permission
                                if (data) {
                                    return '<div class="userDatatable-content"><span class="badge badge-success badge-round">{{ __("customer::customer.verified") }}</span></div>';
                                } else {
                                    return '<div class="userDatatable-content"><span class="badge badge-warning badge-round">{{ __("customer::customer.not_verified") }}</span></div>';
                                }
                            }
                            let checked = data ? 'checked' : '';
                            return `<div class="userDatatable-content">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input verification-switch" type="checkbox"
                                        data-id="${row.id}" ${checked} style="cursor: pointer; width: 40px; height: 20px;">
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + row.created_at + '</div>';
                        }
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let actions = '<div class="userDatatable-content">';
                            actions += '<div class="btn-group">';
                            
                            if (canShow) {
                                actions += '<a href="' + '{{ route("admin.customers.show", "__id__") }}'.replace('__id__', data) + '" class="btn btn-outline-info btn-sm" title="{{ __('customer::customer.view') }}">';
                                actions += '<i class="uil uil-eye m-0"></i>';
                                actions += '</a>';
                            }
                            
                            if (row.can_manage && canEdit) {
                                actions += '<a href="' + '{{ route("admin.customers.edit", "__id__") }}'.replace('__id__', data) + '" class="btn btn-outline-primary btn-sm" title="{{ __('customer::customer.edit') }}">';
                                actions += '<i class="uil uil-edit m-0"></i>';
                                actions += '</a>';
                            }
                            
                            if (row.can_manage && canDelete) {
                                actions += '<a href="javascript:void(0);" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delete-customer" data-item-id="' + data + '" data-item-name="' + row.full_name + '" title="{{ __('customer::customer.delete') }}">';
                                actions += '<i class="uil uil-trash m-0"></i>';
                                actions += '</a>';
                            }
                            
                            actions += '</div>';
                            actions += '</div>';
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
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ __('customer::customer.customers_management') }}'
                }],
                searching: false,
                language: {
                    lengthMenu: "{{ __('customer::customer.show') }} _MENU_",
                    info: "{{ __('customer::customer.showing_entries') }}",
                    infoEmpty: "{{ __('customer::customer.showing_empty') }}",
                    emptyTable: "{{ __('customer::customer.no_data_available') }}",
                    zeroRecords: "{{ __('customer::customer.no_customers_found') }}",
                    loadingRecords: "{{ __('customer::customer.loading') }}",
                    processing: "{{ __('customer::customer.processing') }}",
                    search: "{{ __('customer::customer.search') }}:",
                }
            });

            // Initialize Select2 on all select elements
            if ($.fn.select2) {
                $('#entriesSelect, #active, #email_verified, #city_filter, #region_filter, #vendor_filter').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            } else {
                console.error('Select2 is not loaded');
            }





            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });



            // Function to update URL with filter parameters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();

                const search = $('#search').val();
                const active = CustomSelect.getValue('active');
                const emailVerified = CustomSelect.getValue('email_verified');
                const cityId = CustomSelect.getValue('city_filter');
                const regionId = CustomSelect.getValue('region_filter');
                const vendorId = document.getElementById('vendor_filter') ? CustomSelect.getValue('vendor_filter') : '';
                const createdDateFrom = $('#created_date_from').val();
                const createdDateTo = $('#created_date_to').val();

                if (search) params.set('search', search);
                if (active) params.set('active', active);
                if (emailVerified) params.set('email_verified', emailVerified);
                if (cityId) params.set('city_id', cityId);
                if (regionId) params.set('region_id', regionId);
                if (vendorId) params.set('vendor_id', vendorId);
                if (createdDateFrom) params.set('created_date_from', createdDateFrom);
                if (createdDateTo) params.set('created_date_to', createdDateTo);

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.pushState({}, '', newUrl);
            }

            // Initialize filters from URL parameters
            function initializeFiltersFromUrl() {
                $('#search').val(getUrlParameter('search'));
                const activeVal = getUrlParameter('active');
                const emailVerifiedVal = getUrlParameter('email_verified');
                const vendorIdVal = getUrlParameter('vendor_id');
                
                if (activeVal) CustomSelect.setValue('active', activeVal);
                if (emailVerifiedVal) CustomSelect.setValue('email_verified', emailVerifiedVal);
                if (vendorIdVal && document.getElementById('vendor_filter')) {
                    CustomSelect.setValue('vendor_filter', vendorIdVal);
                }
                
                $('#created_date_from').val(getUrlParameter('created_date_from'));
                $('#created_date_to').val(getUrlParameter('created_date_to'));
                // Note: city_id and region_id are handled after cities are loaded via AJAX
            }

            // Initialize filters from URL
            initializeFiltersFromUrl();

            // Search button functionality
            $('#searchBtn').on('click', function() {
                console.log('Search button clicked, updating URL and reloading table...');
                updateUrlWithFilters();
                table.draw();
            });

            // Search input with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();
                searchTimer = setTimeout(function() {
                    updateUrlWithFilters();
                    table.draw();
                }, 500);
            });

            // Filter change handlers for custom selects
            ['active', 'email_verified', 'city_filter', 'region_filter', 'vendor_filter'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', function() {
                        updateUrlWithFilters();
                        table.draw();
                    });
                }
            });
            
            // Filter change handlers for date inputs
            $('#created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.draw();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                CustomSelect.clear('active');
                CustomSelect.clear('email_verified');
                if (document.getElementById('vendor_filter')) {
                    CustomSelect.clear('vendor_filter');
                }

                // Reset city and region
                CustomSelect.clear('city_filter');
                CustomSelect.clear('region_filter');

                // Reload cities
                if (sessionCountryId) {
                    fetch(`/api/area/countries/${sessionCountryId}/cities`, {
                        method: 'GET',
                        headers: {
                            'lang': "{{ app()->getLocale() }}"
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.data && Array.isArray(data.data)) {
                                const cityOptions = data.data.map(city => ({
                                    id: city.id,
                                    name: city.name
                                }));
                                CustomSelect.setOptions('city_filter', cityOptions, "{{ __('main.choose') }}");
                            }
                        })
                        .catch(error => console.error('Error loading cities:', error));
                }

                $('#created_date_from').val('');
                $('#created_date_to').val('');

                // Update URL and reload table
                updateUrlWithFilters();
                table.draw();
            });

            $('#exportExcel').on('click', function() {
                alert('{{ __('customer::customer.export_excel') }} feature coming soon');
            });

            // Status switch handler
            $(document).on('change', '.status-switch', function() {
                const $switch = $(this);
                const customerId = $switch.data('id');
                const originalState = !$switch.is(':checked');

                $.ajax({
                    url: '{{ route("admin.customers.change-status", "__id__") }}'.replace('__id__', customerId),
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
                            toastr.error('{{ __("customer::customer.error_changing_status") }}');
                        }
                    }
                });
            });

            // Verification switch handler
            $(document).on('change', '.verification-switch', function() {
                const $switch = $(this);
                const customerId = $switch.data('id');
                const originalState = !$switch.is(':checked');

                $.ajax({
                    url: '{{ route("admin.customers.change-verification", "__id__") }}'.replace('__id__', customerId),
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
                            toastr.error('{{ __("customer::customer.error_changing_verification") }}');
                        }
                    }
                });
            });
        });
    </script>
@endpush
