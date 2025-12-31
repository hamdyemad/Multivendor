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

        {{-- Shipping Settings Card --}}
        {{-- <div class="row mb-3">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-15">
                        <h6 class="mb-0 fw-500">
                            <i class="uil uil-setting me-2"></i>{{ trans('shipping.shipping_settings') }}
                        </h6>
                    </div>
                    <div class="card-body py-20">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-between p-3 rounded">
                                    <div>
                                        <span class="fw-500">{{ trans('shipping.allow_departments') }}</span>
                                        <small class="d-block text-muted">{{ trans('shipping.allow_departments_desc') }}</small>
                                    </div>
                                    <div class="form-check form-switch form-switch-primary form-switch-md">
                                        <input type="checkbox" class="form-check-input shipping-setting-switch" 
                                            id="shipping_allow_departments" 
                                            data-setting="shipping_allow_departments"
                                            {{ $shippingSettings->shipping_allow_departments ?? false ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-between p-3 rounded">
                                    <div>
                                        <span class="fw-500">{{ trans('shipping.allow_categories') }}</span>
                                        <small class="d-block text-muted">{{ trans('shipping.allow_categories_desc') }}</small>
                                    </div>
                                    <div class="form-check form-switch form-switch-primary form-switch-md">
                                        <input type="checkbox" class="form-check-input shipping-setting-switch" 
                                            id="shipping_allow_categories" 
                                            data-setting="shipping_allow_categories"
                                            {{ $shippingSettings->shipping_allow_categories ?? true ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-between p-3 rounded">
                                    <div>
                                        <span class="fw-500">{{ trans('shipping.allow_sub_categories') }}</span>
                                        <small class="d-block text-muted">{{ trans('shipping.allow_sub_categories_desc') }}</small>
                                    </div>
                                    <div class="form-check form-switch form-switch-primary form-switch-md">
                                        <input type="checkbox" class="form-check-input shipping-setting-switch" 
                                            id="shipping_allow_sub_categories" 
                                            data-setting="shipping_allow_sub_categories"
                                            {{ $shippingSettings->shipping_allow_sub_categories ?? false ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('shipping.shipping_management') }}</h4>
                        @can('shippings.create')
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.shippings.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('shipping.create_shipping') }}
                            </a>
                        </div>
                        @endcan
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

    {{-- Delete Confirmation Modal --}}
    <x-delete-modal 
        modalId="modal-delete-shipping" 
        :title="trans('shipping.confirm_delete')" 
        :message="trans('shipping.delete_confirmation')" 
        itemNameId="delete-shipping-name"
        confirmBtnId="confirmDeleteShippingBtn" 
        deleteRoute="{{ rtrim(route('admin.shippings.index'), '/') }}"
        :cancelText="trans('main.cancel')" 
        :deleteText="trans('shipping.delete')" />

    {{-- Setting Change Confirmation Modal --}}
    <div class="modal fade" id="modal-confirm-setting-change" tabindex="-1" aria-labelledby="settingChangeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle border-0">
                    <h5 class="modal-title" id="settingChangeModalLabel">
                        <i class="uil uil-exclamation-triangle text-warning me-2"></i>
                        {{ trans('shipping.confirm_setting_change') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="uil uil-info-circle text-warning" style="font-size: 48px;"></i>
                        </div>
                        <p class="mb-0 text-muted">{{ trans('shipping.setting_change_warning') }}</p>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-light btn-default btn-squared" data-bs-dismiss="modal">
                        {{ trans('main.cancel') }}
                    </button>
                    <button type="button" class="btn btn-warning btn-default btn-squared" id="confirmSettingChangeBtn">
                        <i class="uil uil-check me-1"></i>
                        {{ trans('shipping.confirm_change') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;
            let pendingSwitcher = null;
            let pendingIsChecked = false;

            // Shipping Settings Switch Handler - Show confirmation first
            $('.shipping-setting-switch').on('change', function(e) {
                e.preventDefault();
                const switcher = $(this);
                const isChecked = switcher.is(':checked');
                
                // Revert the switch temporarily until confirmed
                switcher.prop('checked', !isChecked);
                
                // Store pending state
                pendingSwitcher = switcher;
                pendingIsChecked = isChecked;
                
                // Show confirmation modal
                $('#modal-confirm-setting-change').modal('show');
            });

            // Confirm setting change
            $('#confirmSettingChangeBtn').on('click', function() {
                if (!pendingSwitcher) return;
                
                // Apply the change
                pendingSwitcher.prop('checked', pendingIsChecked);
                
                // If turning on, turn off the others
                if (pendingIsChecked) {
                    $('.shipping-setting-switch').not(pendingSwitcher).prop('checked', false);
                }

                // Save settings via AJAX
                $.ajax({
                    url: '{{ route("admin.shippings.update-settings") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        shipping_allow_departments: $('#shipping_allow_departments').is(':checked') ? 1 : 0,
                        shipping_allow_categories: $('#shipping_allow_categories').is(':checked') ? 1 : 0,
                        shipping_allow_sub_categories: $('#shipping_allow_sub_categories').is(':checked') ? 1 : 0
                    },
                    success: function(response) {
                        $('#modal-confirm-setting-change').modal('hide');
                        if (response.success) {
                            toastr.success(response.message);
                            // Reload the datatable to reflect deleted shippings
                            table.ajax.reload();
                        } else {
                            toastr.error(response.message);
                            // Revert on error
                            pendingSwitcher.prop('checked', !pendingIsChecked);
                        }
                        pendingSwitcher = null;
                    },
                    error: function() {
                        $('#modal-confirm-setting-change').modal('hide');
                        toastr.error('{{ trans("shipping.error_saving_settings") }}');
                        // Revert on error
                        pendingSwitcher.prop('checked', !pendingIsChecked);
                        pendingSwitcher = null;
                    }
                });
            });

            // Cancel setting change
            $('#modal-confirm-setting-change').on('hidden.bs.modal', function() {
                pendingSwitcher = null;
                pendingIsChecked = false;
            });

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
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            @can('shippings.change-status')
                            const isChecked = data === 1 || data === true ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            return `
                                <div class="form-check form-switch  form-switch-primary form-switch-sm d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input status-switcher" 
                                        id="${switchId}" data-id="${row.id}" ${isChecked}>
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            `;
                            @else
                            if (data === 1 || data === true) {
                                return '<span class="badge badge-success badge-round badge-lg">{{ trans('shipping.active') }}</span>';
                            } else {
                                return '<span class="badge badge-danger badge-round badge-lg">{{ trans('shipping.inactive') }}</span>';
                            }
                            @endcan
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
                                    @can('shippings.show')
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan
                                    @can('shippings.edit')
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('shipping.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan
                                    @can('shippings.delete')
                                    <button type="button"
                                    class="btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-shipping"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.name}"
                                    title="{{ trans('shipping.delete') }}">
                                        <i class="uil uil-trash table_action_icon"></i>
                                    </button>
                                    @endcan
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
            $(document).on('click', '#confirmDeleteShippingBtn', function() {
                let id = $(this).data('item-id');
                $.ajax({
                    url: "{{ route('admin.shippings.destroy', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        $('#modal-delete-shipping').modal('hide');
                        toastr.success('{{ trans('shipping.deleted_successfully') }}');
                        table.ajax.reload();
                    },
                    error: function() {
                        $('#modal-delete-shipping').modal('hide');
                        toastr.error('{{ trans('shipping.error_deleting') }}');
                    }
                });
            });

            // Set item data when modal is shown
            $('#modal-delete-shipping').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const itemId = button.data('item-id');
                const itemName = button.data('item-name');
                
                $('#delete-shipping-name').text(itemName);
                $('#confirmDeleteShippingBtn').data('item-id', itemId);
            });
        });
    </script>
@endpush
