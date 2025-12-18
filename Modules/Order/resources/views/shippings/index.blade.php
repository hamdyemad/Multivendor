@extends('layout.app')
@section('title')
    {{ trans('shipping.shipping_management') }} | Bnaia
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
                    ['title' => trans('shipping.shipping_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('shipping.shipping_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.shippings.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('shipping.create_shipping') }}
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
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('common.search') }}..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('shipping.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ trans('common.all') }}</option>
                                                <option value="1">{{ trans('shipping.active') }}</option>
                                                <option value="0">{{ trans('shipping.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('shipping.created_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_from_filter">
                                        </div>
                                    </div>

                                    {{-- Created Until --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_until_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('shipping.created_until') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_until_filter">
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
                        <table id="shippingsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.cost') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.cities') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.categories') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('shipping.created_at') }}</span></th>
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

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));

            // Server-side processing with pagination
            let table = $('#shippingsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.shippings.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val() !== '' ? $('#active').val() : null;
                        d.created_date_from = $('#created_from_filter').val();
                        d.created_date_to = $('#created_until_filter').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'cost',
                        name: 'cost',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '{{ currency() }} ' + parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'cities',
                        name: 'cities',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.cities && row.cities.length > 0) {
                                let badges = row.cities.slice(0, 3).map(city => 
                                    `<span class="badge badge-sm" style="background-color: #0056B7; color: white; margin: 2px; padding: 4px 8px; border-radius: 4px; font-size: 11px;">${city.name}</span>`
                                ).join(' ');
                                
                                if (row.cities.length > 3) {
                                    let remainingCities = row.cities.slice(3).map(city => city.name).join(', ');
                                    badges += ` <span class="badge badge-sm" style="background-color: #6c757d; color: white; margin: 2px; padding: 4px 8px; border-radius: 4px; font-size: 11px;"  data-bs-toggle="tooltip" data-bs-placement="top" title="${remainingCities}">+${row.cities.length - 3}</span>`;
                                }
                                
                                return badges;
                            }
                            return '<span style="color: #999;">-</span>';
                        }
                    },
                    {
                        data: 'categories',
                        name: 'categories',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.categories && row.categories.length > 0) {
                                let badges = row.categories.slice(0, 3).map(category => 
                                    `<span class="badge badge-sm" style="background-color: #9C27B0; color: white; margin: 2px; padding: 4px 8px; border-radius: 4px; font-size: 11px;">${category.name}</span>`
                                ).join(' ');
                                
                                if (row.categories.length > 3) {
                                    let remainingCategories = row.categories.slice(3).map(category => category.name).join(', ');
                                    badges += ` <span class="badge badge-sm" style="background-color: #6c757d; color: white; margin: 2px; padding: 4px 8px; border-radius: 4px; font-size: 11px;" data-bs-toggle="tooltip" data-bs-placement="top" title="${remainingCategories}">+${row.categories.length - 3}</span>`;
                                }
                                
                                return badges;
                            }
                            return '<span style="color: #999;">-</span>';
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const isChecked = data === 1 || data === true ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            return `
                                <div class="form-check form-switch  form-switch-primary form-switch-sm">
                                    <input type="checkbox" class="form-check-input status-switcher" 
                                        id="${switchId}" data-id="${row.id}" ${isChecked}>
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let editUrl = "{{ route('admin.shippings.edit', ':id') }}".replace(':id', row.id);
                            let showUrl = "{{ route('admin.shippings.show', ':id') }}".replace(':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('shipping.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <button type="button"
                                    class="delete-btn btn btn-danger table_action_father"
                                    data-id="${row.id}"
                                    title="{{ trans('shipping.delete') }}">
                                        <i class="uil uil-trash table_action_icon"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "{{ __('common.search') }}:",
                }
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Live search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                    updateUrlParams();
                }, 500);
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Filter change handlers
            $('#active, #created_from_filter, #created_until_filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#created_from_filter').val('');
                $('#created_until_filter').val('');
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Update URL parameters function
            function updateUrlParams() {
                const params = new URLSearchParams();
                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#created_from_filter').val()) params.set('created_from', $('#created_from_filter').val());
                if ($('#created_until_filter').val()) params.set('created_until', $('#created_until_filter').val());

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            // Change shipping status
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.shippings.change-status', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        switcher.prop('disabled', false);
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function() {
                        switcher.prop('disabled', false);
                        switcher.prop('checked', !switcher.is(':checked'));
                        toastr.error('{{ trans('shipping.error_changing_status') }}');
                    }
                });
            });

            // Delete shipping
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                if (confirm('{{ trans('shipping.confirm_delete') }}')) {
                    $.ajax({
                        url: "{{ route('admin.shippings.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            toastr.success('{{ trans('shipping.deleted_successfully') }}');
                            table.ajax.reload();
                        },
                        error: function() {
                            toastr.error('{{ trans('shipping.error_deleting') }}');
                        }
                    });
                }
            });
        });
    </script>
@endpush
