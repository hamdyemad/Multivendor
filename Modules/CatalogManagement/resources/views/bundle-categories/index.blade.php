@extends('layout.app')
@section('title')
    {{ trans('catalogmanagement::bundle_category.bundle_categories_management') }} | Bnaia
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
                    ['title' => trans('catalogmanagement::bundle_category.bundle_categories_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('catalogmanagement::bundle_category.bundle_categories_management') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.bundle-categories.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('catalogmanagement::bundle_category.add_bundle_category') }}
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
                                                {{ trans('catalogmanagement::bundle_category.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ trans('catalogmanagement::bundle_category.all_status') }}</option>
                                                <option value="1">{{ trans('catalogmanagement::bundle_category.active') }}</option>
                                                <option value="0">{{ trans('catalogmanagement::bundle_category.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ trans('catalogmanagement::bundle_category.created_from') }}
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
                                                {{ trans('catalogmanagement::bundle_category.created_until') }}
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
                        <table id="bundleCategoriesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    @foreach($languages as $language)
                                        <th><span class="userDatatable-title">{{ trans('catalogmanagement::bundle_category.name') }} ({{ $language->name }})</span></th>
                                    @endforeach
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::bundle_category.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('catalogmanagement::bundle_category.created_at') }}</span></th>
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

    {{-- Delete Modal --}}
    <x-delete-with-loading modalId="modal-delete-bundle-category" tableId="bundleCategoriesDataTable" deleteButtonClass="delete-bundle-category"
        :title="trans('main.confirm delete')" :message="trans('main.are you sure you want to delete this')" itemNameId="delete-bundle-category-name" confirmBtnId="confirmDeleteBundleCategoryBtn"
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
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));

            // Server-side processing with pagination
            let table = $('#bundleCategoriesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.bundle-categories.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val();
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
                    @foreach($languages as $language)
                    {
                        data: 'translations.{{ $language->code }}.name',
                        name: 'translations.{{ $language->code }}.name',
                        render: function(data, type, row) {
                            return data || '-';
                        }
                    },
                    @endforeach
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        render: function(data, type, row) {
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input status-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-id="${row.id}"
                                           ${isChecked}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let viewUrl = "{{ route('admin.bundle-categories.show', ':id') }}".replace(':id', row.id),
                                editUrl = "{{ route('admin.bundle-categories.edit', ':id') }}".replace(':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    <a href="${viewUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="{{ trans('catalogmanagement::bundle_category.view_bundle_category') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>

                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="{{ trans('catalogmanagement::bundle_category.edit_bundle_category') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>

                                    <a href="javascript:void(0);"
                                    class="remove delete-bundle-category btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-bundle-category"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.translations && row.translations.en ? row.translations.en.name : 'Bundle Category'}"
                                    title="{{ trans('catalogmanagement::bundle_category.delete_bundle_category') }}">
                                        <i class="uil uil-trash-alt table_action_icon"></i>
                                    </a>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'desc']],
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

            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                // Show loading overlay for status change
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ trans('catalogmanagement::bundle_category.status_changed_successfully') }}',
                        subtext: '{{ __('common.please_wait') }}...'
                    });
                }

                let route = "{{ route('admin.bundle-categories.toggle-status', ':id') }}".replace(':id', id)
                $.ajax({
                    url: route,
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

    {{-- Delete Modal Configuration --}}
    <script>
        // Configure delete functionality
        window.deleteConfig = {
            url: '{{ url("admin/bundle-categories") }}',
            token: '{{ csrf_token() }}'
        };
    </script>
@endpush
