@extends('layout.app')

@section('title')
    {{ __('systemsetting::blog_categories.blog_categories_management') }}
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
                    ['title' => __('systemsetting::blog_categories.blog_categories_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('systemsetting::blog_categories.blog_categories_management') }}
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.system-settings.blog-categories.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('systemsetting::blog_categories.add_blog_category') }}
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
                                                <i class="uil uil-search me-1"></i>
                                                {{ __('systemsetting::blog_categories.search') }}
                                                <small
                                                    class="text-muted">({{ __('systemsetting::blog_categories.real_time') }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('systemsetting::blog_categories.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('systemsetting::blog_categories.status') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">
                                                    {{ __('systemsetting::blog_categories.all_statuses') }}</option>
                                                <option value="1">{{ __('systemsetting::blog_categories.active') }}
                                                </option>
                                                <option value="0">{{ __('systemsetting::blog_categories.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('systemsetting::blog_categories.created_from') }}
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
                                                {{ __('systemsetting::blog_categories.created_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('systemsetting::blog_categories.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('systemsetting::blog_categories.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('systemsetting::blog_categories.reset_filters') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('systemsetting::blog_categories.reset_filters') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('systemsetting::blog_categories.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('systemsetting::blog_categories.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="blogCategoriesDataTable" class="table mb-0 table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::blog_categories.title') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::blog_categories.image') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::blog_categories.status') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::blog_categories.created_at') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::blog_categories.action') }}</span>
                                    </th>
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
    <x-delete-modal modalId="modal-delete-blog-category" :title="__('systemsetting::blog_categories.confirm_delete')" :message="__('systemsetting::blog_categories.delete_confirmation')"
        itemNameId="delete-blog-category-name" confirmBtnId="confirmDeleteBlogCategoryBtn"
        deleteRoute="{{ rtrim(route('admin.system-settings.blog-categories.index'), '/') }}" :cancelText="__('systemsetting::blog_categories.cancel')"
        :deleteText="__('systemsetting::blog_categories.delete_blog_category')" />
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
            if (urlParams.has('active')) {
                $('#active').val(urlParams.get('active'));
            }
            if (urlParams.has('created_date_from')) {
                $('#created_date_from').val(urlParams.get('created_date_from'));
            }
            if (urlParams.has('created_date_to')) {
                $('#created_date_to').val(urlParams.get('created_date_to'));
            }

            let table = $('#blogCategoriesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.system-settings.blog-categories.datatable') }}',
                    type: 'GET',
                    data: function(d) {
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
                        data: 'title_display',
                        name: 'title_display',
                        orderable: false
                    },
                    {
                        data: 'image_preview',
                        name: 'image_preview',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status_badge',
                        orderable: false
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
                    lengthMenu: "{{ __('systemsetting::blog_categories.show') }} _MENU_",
                    info: "{{ __('systemsetting::blog_categories.showing_entries') }}",
                    infoEmpty: "{{ __('systemsetting::blog_categories.showing_empty') }}",
                    emptyTable: "{{ __('systemsetting::blog_categories.no_data_available') }}",
                    zeroRecords: "{{ __('systemsetting::blog_categories.no_blog_categories_found') }}",
                    loadingRecords: "{{ __('systemsetting::blog_categories.loading') }}",
                    processing: "{{ __('systemsetting::blog_categories.processing') }}",
                    search: "{{ __('systemsetting::blog_categories.search') }}:",
                }
            });

            // Initialize Select2
            if ($.fn.select2) {
                $('#entriesSelect, #active').select2({
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
                const active = $('#active').val();
                const createdDateFrom = $('#created_date_from').val();
                const createdDateTo = $('#created_date_to').val();

                if (search) params.set('search', search);
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
            $('#active, #created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.draw();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                $('#search').val('');
                $('#active').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                updateUrlWithFilters();
                table.draw();
            });

            // Status switcher
            $(document).on('change', '.status-switcher', function() {
                let switcher = $(this);
                let categoryId = switcher.data('id');
                let isActive = switcher.is(':checked') ? 1 : 0;

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show();
                }

                $.ajax({
                    url: '{{ route('admin.system-settings.blog-categories.toggle-status', ':id') }}'
                        .replace(':id', categoryId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        active: isActive
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        if (response.status) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            }
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        switcher.prop('checked', !switcher.is(':checked'));
                        if (typeof toastr !== 'undefined') {
                            toastr.error(
                                '{{ __('systemsetting::blog_categories.error_changing_status') }}'
                                );
                        }
                    }
                });
            });

            // Reload table after successful delete
            $('#modal-delete-blog-category').on('hidden.bs.modal', function() {
                // Check if delete was successful and reload table
                if (window.deleteSuccess) {
                    table.draw();
                    window.deleteSuccess = false;
                }
            });
        });
    </script>
@endpush
