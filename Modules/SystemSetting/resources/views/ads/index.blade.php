@extends('layout.app')

@section('title')
    {{ __('systemsetting::ads.ads_management') }}
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
                    ['title' => __('systemsetting::ads.ads_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('systemsetting::ads.ads_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.system-settings.ads.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('systemsetting::ads.add_ad') }}
                            </a>
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
                                                <i class="uil uil-search me-1"></i> {{ __('systemsetting::ads.search') }}
                                                <small class="text-muted">({{ __('systemsetting::ads.real_time') }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('systemsetting::ads.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Position --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="position" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-map-pin me-1"></i>
                                                {{ __('systemsetting::ads.position') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="position">
                                                <option value="">{{ __('systemsetting::ads.all_positions') }}</option>
                                                @foreach ($positions as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('systemsetting::ads.status') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('systemsetting::ads.all_status') }}</option>
                                                <option value="1">{{ __('systemsetting::ads.active') }}</option>
                                                <option value="0">{{ __('systemsetting::ads.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('systemsetting::ads.created_from') }}
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
                                                {{ __('systemsetting::ads.created_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('systemsetting::ads.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('systemsetting::ads.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('systemsetting::ads.reset_filters') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('systemsetting::ads.reset_filters') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('systemsetting::ads.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('systemsetting::ads.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="adsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::ads.title') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::ads.position') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::ads.ad_image') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::ads.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::ads.created_at') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::ads.action') }}</span></th>
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
    <x-delete-modal modalId="modal-delete-ad" :title="__('systemsetting::ads.confirm_delete')" :message="__('systemsetting::ads.delete_confirmation')" itemNameId="delete-ad-name"
        confirmBtnId="confirmDeleteAdBtn" deleteRoute="{{ rtrim(route('admin.system-settings.ads.index'), '/') }}"
        :cancelText="__('systemsetting::ads.cancel')" :deleteText="__('systemsetting::ads.delete_ad')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Get URL parameters and populate filters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) {
                $('#search').val(urlParams.get('search'));
            }
            if (urlParams.has('position')) {
                $('#position').val(urlParams.get('position'));
            }
            if (urlParams.has('active')) {
                $('#active').val(urlParams.get('active'));
            }
            if (urlParams.has('created_date_from')) {
                $('#created_date_from').val(urlParams.get('created_date_from'));
            }
            if (urlParams.has('created_date_to')) {
                $('#created_date_to').val(urlParams.get('created_date_to'));
            }

            let table = $('#adsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.system-settings.ads.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.position = $('#position').val();
                        d.active = $('#active').val();
                        d.search = $('#search').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title_subtitle',
                        name: 'title_subtitle',
                        orderable: false
                    },
                    {
                        data: 'position_badge',
                        name: 'position_badge',
                        orderable: false
                    },
                    {
                        data: 'image_preview',
                        name: 'image_preview',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        render: function(data, type, row) {
                            const isChecked = data === 1 || data === true ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            return `
                        <div class="form-check form-switch form-switch-primary form-switch-sm d-flex justify-content-center">
                            <input type="checkbox" class="form-check-input status-switcher" 
                                id="${switchId}" data-id="${row.id}" ${isChecked}>
                            <label class="form-check-label" for="${switchId}"></label>
                        </div>
                    `;
                        }
                    },
                    {
                        data: 'created_date',
                        name: 'created_date',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
                searching: false,
                language: {
                    lengthMenu: "{{ __('systemsetting::ads.show') }} _MENU_",
                    info: "{{ __('systemsetting::ads.showing_entries') }}",
                    infoEmpty: "{{ __('systemsetting::ads.showing_empty') }}",
                    emptyTable: "{{ __('systemsetting::ads.no_data_available') }}",
                    zeroRecords: "{{ __('systemsetting::ads.no_ads_found') }}",
                    loadingRecords: "{{ __('systemsetting::ads.loading') }}",
                    processing: "{{ __('systemsetting::ads.processing') }}",
                    search: "{{ __('systemsetting::ads.search') }}:",
                }
            });

            // Initialize Select2
            if ($.fn.select2) {
                $('#entriesSelect, #position, #active').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            }

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Function to update URL with filter parameters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();
                const search = $('#search').val();
                const position = $('#position').val();
                const active = $('#active').val();
                const createdDateFrom = $('#created_date_from').val();
                const createdDateTo = $('#created_date_to').val();

                if (search) params.set('search', search);
                if (position) params.set('position', position);
                if (active) params.set('active', active);
                if (createdDateFrom) params.set('created_date_from', createdDateFrom);
                if (createdDateTo) params.set('created_date_to', createdDateTo);

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.pushState({}, '', newUrl);
            }

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

            // Filter change handlers
            $('#position, #active, #created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.draw();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                $('#search').val('');
                $('#position').val('').trigger('change');
                $('#active').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                updateUrlWithFilters();
                table.draw();
            });

            // Toggle status
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.system-settings.ads.toggle-status', ':id') }}".replace(
                        ':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        switcher.prop('disabled', false);
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function(xhr) {
                        switcher.prop('disabled', false);
                        switcher.prop('checked', !switcher.is(':checked'));
                        const message = xhr.responseJSON ? xhr.responseJSON.message :
                            'Error changing status';
                        toastr.error(message);
                    }
                });
            });

            // Reload table after successful delete
            $('#modal-delete-ad').on('hidden.bs.modal', function() {
                // Check if delete was successful and reload table
                if (window.deleteSuccess) {
                    table.draw();
                    window.deleteSuccess = false;
                }
            });
        });
    </script>
@endpush
