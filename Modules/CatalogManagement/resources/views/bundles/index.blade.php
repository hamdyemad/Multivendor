@extends('layout.app')
@section('title')
    {{ trans('catalogmanagement::bundle.bundles_management') }} | Bnaia
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
                    ['title' => trans('catalogmanagement::bundle.bundles_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('catalogmanagement::bundle.bundles_management') }}</h4>
                        <div class="d-flex gap-2">
                            @can('bundles.index')
                                <button type="button" id="exportExcel"
                                    class="btn btn-secondary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-file-download"></i> {{ trans('common.export_excel') }}
                                </button>
                            @endcan
                            @can('bundles.create')
                                <a href="{{ route('admin.bundles.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ trans('catalogmanagement::bundle.add_bundle') }}
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
                                    <div class="col-md-4">
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

                                    {{-- Vendor Filter --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vendor_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-store me-1"></i>
                                                {{ trans('catalogmanagement::bundle.vendor') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select select2"
                                                id="vendor_filter" style="width: 100%;">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor['id'] }}">{{ $vendor['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ trans('catalogmanagement::bundle.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ trans('catalogmanagement::bundle.all_status') }}
                                                </option>
                                                <option value="1">{{ trans('catalogmanagement::bundle.active') }}
                                                </option>
                                                <option value="0">{{ trans('catalogmanagement::bundle.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Approval Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="approval_status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-shield-check me-1"></i>
                                                {{ trans('catalogmanagement::bundle.approval_status') ?? 'Approval Status' }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="approval_status">
                                                <option value="">{{ trans('catalogmanagement::bundle.all_status') }}
                                                </option>
                                                <option value="1">{{ trans('catalogmanagement::bundle.approved') }}
                                                </option>
                                                <option value="0">
                                                    {{ trans('catalogmanagement::bundle.pending_approval') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('catalogmanagement::bundle.created_from') }}
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
                                                {{ trans('catalogmanagement::bundle.created_until') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_until_filter">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center mt-3">
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
                        <table id="bundlesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::bundle.bundle_information') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::bundle.sku') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::bundle.vendor') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::bundle.status') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::bundle.approval_status') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ trans('catalogmanagement::bundle.created_at') }}</span>
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

    @can('bundles.change-approval')
        {{-- Approval Modal --}}
        <div class="modal fade" id="modal-approve-bundle" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title" id="approveModalLabel">
                            <i class="uil uil-check-circle me-2"></i>{{ trans('catalogmanagement::bundle.approve_bundle') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="uil uil-info-circle me-2"></i>
                            <span id="approve-bundle-name"></span>
                        </div>

                        <div class="form-group mb-3">
                            <label for="approval_action"
                                class="form-label fw-500">{{ trans('catalogmanagement::bundle.action') }}</label>
                            <select id="approval_action" class="form-select">
                                <option value="">{{ trans('catalogmanagement::bundle.select_action') }}</option>
                                <option value="approve">{{ trans('catalogmanagement::bundle.approve') }}</option>
                                <option value="reject">{{ trans('catalogmanagement::bundle.reject') }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="approval_reason"
                                class="form-label fw-500">{{ trans('catalogmanagement::bundle.reason') }}
                                ({{ trans('catalogmanagement::bundle.optional') }})</label>
                            <textarea id="approval_reason" class="form-control" rows="3"
                                placeholder="{{ trans('catalogmanagement::bundle.enter_reason') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ trans('main.cancel') }}</button>
                        <button type="button" class="btn btn-primary" id="confirmApprovalBtn">
                            <span id="approvalBtnText">{{ trans('catalogmanagement::bundle.confirm') }}</span>
                            <span id="approvalSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"
                                aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- Delete Modal --}}
    <x-delete-with-loading modalId="modal-delete-bundle" tableId="bundlesDataTable" deleteButtonClass="delete-bundle"
        :title="trans('main.confirm delete')" :message="trans('main.are you sure you want to delete this')" itemNameId="delete-bundle-name" confirmBtnId="confirmDeleteBundleBtn"
        :cancelText="trans('main.cancel')" :deleteText="trans('main.delete')" :loadingDeleting="trans('main.deleting')" :loadingPleaseWait="trans('main.please wait')" :loadingDeletedSuccessfully="trans('main.deleted success')" :loadingRefreshing="trans('main.refreshing')"
        :errorDeleting="trans('main.error on delete')" />
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
            if (urlParams.has('vendor_id')) {
                $('#vendor_filter').val(urlParams.get('vendor_id')).trigger('change.select2');
            }
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('approval_status')) $('#approval_status').val(urlParams.get('approval_status'));
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));

            // Server-side processing with pagination
            let table = $('#bundlesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.bundles.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.vendor_id = $('#vendor_filter').val();
                        d.active = $('#active').val();
                        d.approval_status = $('#approval_status').val();
                        d.created_from = $('#created_from_filter').val();
                        d.created_until = $('#created_until_filter').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'bundle_information',
                        name: 'bundle_information',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data) return '<span class="text-muted">—</span>';

                            let html =
                                '<div class="bundle-info-container" style="display: flex; gap: 12px; align-items: flex-start;">';

                            // Image
                            if (data.image) {
                                html += `<div style="flex-shrink: 0;">
                                    <img src="${data.image}" alt="Bundle Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                </div>`;
                            } else {
                                html +=
                                    `<img src="{{ asset('assets/img/default.png') }}" alt="Bundle Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">`;
                            }

                            // Names
                            html += '<div style="flex: 1; min-width: 0;">';

                            // EN Name
                            if (data.name_en && data.name_en !== '-') {
                                html += `<div style="margin-bottom: 4px;">
                                    <span class="badge badge-round badge-lg bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">EN</span>
                                    <span class="text-dark fw-semibold" style="font-size: 14px;">${$('<div/>').text(data.name_en).html()}</span>
                                </div>`;
                            }

                            // AR Name
                            if (data.name_ar && data.name_ar !== '-') {
                                html += `<div style="margin-bottom: 4px;">
                                    <span class="badge badge-round badge-lg bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">AR</span>
                                    <span class="text-dark fw-semibold" dir="rtl" style="font-size: 14px; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">${$('<div/>').text(data.name_ar).html()}</span>
                                </div>`;
                            }

                            html += '</div></div>';
                            return html;
                        }
                    },
                    {
                        data: 'sku',
                        name: 'sku',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'vendor',
                        name: 'vendor',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            const isDisabled =
                                @can('bundles.toggle-status')
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
                        data: 'admin_approval',
                        name: 'admin_approval',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // 0 = pending, 1 = approved, 2 = rejected
                            if (data === 1) {
                                return '<span class="badge badge-round badge-lg bg-success"><i class="uil uil-check-circle me-1"></i>{{ trans('catalogmanagement::bundle.approved') }}</span>';
                            } else if (data === 0) {
                                return '<span class="badge badge-round badge-lg bg-warning"><i class="uil uil-clock me-1"></i>{{ trans('catalogmanagement::bundle.pending_approval') }}</span>';
                            } else if (data === 2) {
                                return '<span class="badge badge-round badge-lg bg-danger"><i class="uil uil-times-circle me-1"></i>{{ trans('catalogmanagement::bundle.rejected') }}</span>';
                            } else {
                                return '<span class="badge badge-round badge-lg bg-secondary">{{ trans('common.unknown') }}</span>';
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        orderable: false,
                        searchable: false,
                        name: 'created_at',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let showUrl = "{{ route('admin.bundles.show', ':id') }}".replace(':id',
                                row.id);
                            let editUrl = "{{ route('admin.bundles.edit', ':id') }}".replace(':id',
                                row.id);
                            let approvalBtn = '';

                            // Only show approval button if bundle is not approved
                            @can('bundles.change-approval')
                                approvalBtn = `<a href="javascript:void(0);"
                                    class="approve-bundle btn btn-info table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-approve-bundle"
                                    data-id="${row.id}"
                                    data-name="${row.bundle_information.name_en && row.bundle_information.name_en ? row.bundle_information.name_en : ''}"
                                    title="{{ trans('catalogmanagement::bundle.approve_bundle') }}">
                                        <i class="uil uil-check-circle table_action_icon"></i>
                                    </a>`;
                            @endcan

                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    @can('bundles.show')
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('catalogmanagement::bundle.view_bundle') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('bundles.edit')
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('catalogmanagement::bundle.edit_bundle') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan

                                    ${approvalBtn}

                                    @can('bundles.delete')
                                    <a href="javascript:void(0);"
                                    class="remove delete-bundle btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-bundle"
                                    data-id="${row.id}"
                                    data-name="${row.name && row.name.en ? row.name.en : 'Bundle'}"
                                    data-url="{{ route('admin.bundles.index') }}/${row.id}"
                                    title="{{ trans('catalogmanagement::bundle.delete_bundle') }}">
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
                language: {
                    search: '',
                    searchPlaceholder: "{{ __('common.search') }}...",
                    lengthMenu: '_MENU_',
                    info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.entries') }}",
                    infoEmpty: "{{ __('common.showing') }} 0 {{ __('common.to') }} 0 {{ __('common.of') }} 0 {{ __('common.entries') }}",
                    infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.total_entries') }})",
                    zeroRecords: "{{ __('common.no_matching_records_found') }}",
                    emptyTable: "{{ __('common.no_data_available') }}",
                    paginate: {
                        @if (app()->getLocale() == 'en')
                            first: '<i class="uil uil-angle-double-left"></i>',
                            last: '<i class="uil uil-angle-double-right"></i>',
                            next: '<i class="uil uil-angle-right"></i>',
                            previous: '<i class="uil uil-angle-left"></i>'
                        @else
                            first: '<i class="uil uil-angle-double-right"></i>',
                            last: '<i class="uil uil-angle-double-left"></i>',
                            next: '<i class="uil uil-angle-left"></i>',
                            previous: '<i class="uil uil-angle-right"></i>'
                        @endif
                    }
                },
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-bordered');
                }
            });

            // Search button
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#vendor_filter').val('').trigger('change');
                $('#active').val('');
                $('#approval_status').val('');
                $('#created_from_filter').val('');
                $('#created_until_filter').val('');
                table.ajax.reload();
                // Clear URL params
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Entries per page
            $('#entriesSelect').on('change', function() {
                per_page = $(this).val();
                table.page.len(per_page).draw();
            });

            // Function to update URL params
            function updateUrlParams() {
                const params = new URLSearchParams();

                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#vendor_filter').val()) params.set('vendor_id', $('#vendor_filter').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#approval_status').val()) params.set('approval_status', $('#approval_status').val());
                if ($('#created_from_filter').val()) params.set('created_from', $('#created_from_filter').val());
                if ($('#created_until_filter').val()) params.set('created_until', $('#created_until_filter').val());

                const newUrl = params.toString() ?
                    `${window.location.pathname}?${params.toString()}` :
                    window.location.pathname;

                window.history.replaceState({}, '', newUrl);
            }

            // Live search with debounce for all filters
            let searchTimer;

            // Text search - live search on keyup
            $('#search').on('keyup', function(e) {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                    updateUrlParams();
                }, 500);
            });

            // Vendor filter - Select2 change
            $('#vendor_filter').on('change.select2', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Select filters - live search on change
            $('#active').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Approval status filter - live search on change
            $('#approval_status').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Date filters - live search on change
            $('#created_from_filter, #created_until_filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Enter key to search immediately
            $('#search').on('keypress', function(e) {
                if (e.which === 13) {
                    clearTimeout(searchTimer);
                    table.ajax.reload();
                    updateUrlParams();
                }
            });

            // Status switcher
            $(document).on('change', '.status-switcher', function() {
                let switcher = $(this);
                let bundleId = switcher.data('id');
                let isActive = switcher.is(':checked') ? 1 : 0;

                // Show loading overlay
                LoadingOverlay.show();

                $.ajax({
                    url: '{{ route('admin.bundles.toggle-status', ':id') }}'.replace(':id',
                        bundleId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_active: isActive
                    },
                    success: function(response) {
                        LoadingOverlay.hide();
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function(xhr) {
                        LoadingOverlay.hide();
                        switcher.prop('checked', !switcher.is(':checked'));
                        toastr.error(
                            '{{ trans('catalogmanagement::bundle.error_changing_status') }}'
                        );
                    }
                });
            });

            // Approval modal handler
            $(document).on('click', '.approve-bundle', function() {
                let bundleId = $(this).data('id');
                let bundleName = $(this).data('name');

                $('#approve-bundle-name').text(bundleName);
                $('#approval_action').val('');
                $('#approval_reason').val('');

                // Store bundle ID for confirmation
                $('#confirmApprovalBtn').data('bundle-id', bundleId);
            });

            // Approval confirmation handler
            $('#confirmApprovalBtn').on('click', function() {
                let bundleId = $(this).data('bundle-id');
                let action = $('#approval_action').val();
                let reason = $('#approval_reason').val();

                if (!action) {
                    toastr.warning('Please select an action');
                    return;
                }

                // Sync CKEditor data if it exists for approval_reason
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['approval_reason']) {
                    reason = CKEDITOR.instances['approval_reason'].getData();
                }

                // Show loading state
                $('#approvalSpinner').removeClass('d-none');
                $('#approvalBtnText').text('Processing...');
                $(this).prop('disabled', true);

                $.ajax({
                    url: '{{ route('admin.bundles.change-approval', ':id') }}'.replace(':id',
                        bundleId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action,
                        reason: reason
                    },
                    success: function(response) {
                        $('#approvalSpinner').addClass('d-none');
                        $('#approvalBtnText').text(
                            '{{ trans('catalogmanagement::bundle.confirm') }}');
                        $('#confirmApprovalBtn').prop('disabled', false);

                        if (response.status) {
                            toastr.success(response.message);
                            $('#modal-approve-bundle').modal('hide');
                            table.ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        $('#approvalSpinner').addClass('d-none');
                        $('#approvalBtnText').text(
                            '{{ trans('catalogmanagement::bundle.confirm') }}');
                        $('#confirmApprovalBtn').prop('disabled', false);

                        let errorMsg = 'Error processing approval';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                    }
                });
            });
        });
    </script>
@endpush
