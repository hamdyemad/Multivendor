@extends('layout.app')
@section('title')
    {{ __('catalogmanagement::promocodes.title') }} | Bnaia
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
                    ['title' => __('catalogmanagement::promocodes.title')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('catalogmanagement::promocodes.title') }}</h4>
                        <div class="d-flex gap-2">
                            @can('promocodes.index')
                                <button type="button" id="exportExcel"
                                    class="btn btn-secondary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-file-download"></i> {{ trans('common.export_excel') }}
                                </button>
                            @endcan
                            @can('promocodes.create')
                                <a href="{{ route('admin.promocodes.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ __('catalogmanagement::promocodes.add_promocode') }}
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
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="{{ __('common.search') }}..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('catalogmanagement::promocodes.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('catalogmanagement::promocodes.all_status') }}
                                                </option>
                                                <option value="1">{{ __('catalogmanagement::promocodes.active') }}
                                                </option>
                                                <option value="0">{{ __('catalogmanagement::promocodes.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Valid From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="valid_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('catalogmanagement::promocodes.valid_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="valid_from_filter">
                                        </div>
                                    </div>

                                    {{-- Valid Until --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="valid_until_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('catalogmanagement::promocodes.valid_until') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="valid_until_filter">
                                        </div>
                                    </div>

                                    {{-- Type Filter --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-percentage me-1"></i>
                                                {{ __('catalogmanagement::promocodes.type') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="type_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="percent">
                                                    {{ __('catalogmanagement::promocodes.types.percent') }}</option>
                                                <option value="amount">
                                                    {{ __('catalogmanagement::promocodes.types.amount') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Dedicated To Filter --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="dedicated_to_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-users-alt me-1"></i>
                                                {{ __('catalogmanagement::promocodes.dedicated_to') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="dedicated_to_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="all">
                                                    {{ __('catalogmanagement::promocodes.dedicated_options.all') }}
                                                </option>
                                                <option value="male">
                                                    {{ __('catalogmanagement::promocodes.dedicated_options.male') }}
                                                </option>
                                                <option value="female">
                                                    {{ __('catalogmanagement::promocodes.dedicated_options.female') }}
                                                </option>
                                            </select>
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
                        <table id="promocodesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::promocodes.code') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::promocodes.type') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::promocodes.value') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::promocodes.valid_from') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::promocodes.valid_until') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::promocodes.dedicated_to') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('catalogmanagement::promocodes.status') }}</span>
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
    <x-delete-modal modalId="modal-delete-promocode" :title="__('catalogmanagement::promocodes.confirm_delete')" :message="__('catalogmanagement::promocodes.delete_confirmation')"
        itemNameId="delete-promocode-name" confirmBtnId="confirmDeletePromocodeBtn" :deleteRoute="route('admin.promocodes.index')" :cancelText="__('catalogmanagement::promocodes.cancel')"
        :deleteText="__('catalogmanagement::promocodes.delete_promocode')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('type')) $('#type_filter').val(urlParams.get('type'));
            if (urlParams.has('dedicated_to')) $('#dedicated_to_filter').val(urlParams.get('dedicated_to'));
            if (urlParams.has('valid_from')) $('#valid_from_filter').val(urlParams.get('valid_from'));
            if (urlParams.has('valid_until')) $('#valid_until_filter').val(urlParams.get('valid_until'));

            // Server-side processing with pagination
            let table = $('#promocodesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.promocodes.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val();
                        d.type = $('#type_filter').val();
                        d.dedicated_to = $('#dedicated_to_filter').val();
                        d.valid_from = $('#valid_from_filter').val();
                        d.valid_until = $('#valid_until_filter').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'type',
                        name: 'type',
                        render: function(data) {
                            const types = @json(__('catalogmanagement::promocodes.types'));
                            return types[data] || data;
                        }
                    },
                    {
                        data: 'value',
                        name: 'value'
                    },
                    {
                        data: 'valid_from',
                        name: 'valid_from'
                    },
                    {
                        data: 'valid_until',
                        name: 'valid_until'
                    },
                    {
                        data: 'dedicated_to',
                        name: 'dedicated_to',
                        render: function(data) {
                            const options = @json(__('catalogmanagement::promocodes.dedicated_options'));
                            return options[data] || data;
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: false,
                        render: function(data, type, row) {
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            const isDisabled =
                                @can('promocodes.change-status')
                                    ''
                                @else
                                    'disabled'
                                @endcan ;
                            return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input status-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-id="${row.id}"
                                           ${isChecked}
                                           ${isDisabled}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let viewUrl = "{{ route('admin.promocodes.show', ':id') }}".replace(
                                ':id', row.id)
                            let editUrl = "{{ route('admin.promocodes.edit', ':id') }}".replace(
                                ':id', row.id)
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    @can('promocodes.show')
                                    <a href="${viewUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ __('catalogmanagement::promocodes.view_promocode') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('promocodes.edit')
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ __('catalogmanagement::promocodes.edit_promocode') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('promocodes.delete')
                                    <a href="javascript:void(0);"
                                    class="remove delete-promocode btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-promocode"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.code}"
                                    title="{{ __('catalogmanagement::promocodes.delete_promocode') }}">
                                        <i class="uil uil-trash-alt table_action_icon"></i>
                                    </a>
                                    @endcan
                                </div>
                            `;
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
            $('#active, #type_filter, #dedicated_to_filter, #valid_from_filter, #valid_until_filter').on('change',
                function() {
                    table.ajax.reload();
                    updateUrlParams();
                });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#type_filter').val('');
                $('#dedicated_to_filter').val('');
                $('#valid_from_filter').val('');
                $('#valid_until_filter').val('');
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Update URL parameters function
            function updateUrlParams() {
                const params = new URLSearchParams();
                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#type_filter').val()) params.set('type', $('#type_filter').val());
                if ($('#dedicated_to_filter').val()) params.set('dedicated_to', $('#dedicated_to_filter').val());
                if ($('#valid_from_filter').val()) params.set('valid_from', $('#valid_from_filter').val());
                if ($('#valid_until_filter').val()) params.set('valid_until', $('#valid_until_filter').val());

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window
                    .location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                // Show loading overlay for status change
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ __('catalogmanagement::promocodes.messages.status_changed') }}',
                        subtext: '{{ __('common.please_wait') }}...'
                    });
                }
                let url = "{{ route('admin.promocodes.change-status', ':id') }}".replace(':id', id)
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        switcher.prop('disabled', false);
                        // Optional: Toast
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('common.success') ?? 'Success' }}',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        switcher.prop('disabled', false);
                        switcher.prop('checked', !switcher.is(':checked'));
                        alert('Error changing status');
                    }
                });
            });
        });
    </script>
@endpush
