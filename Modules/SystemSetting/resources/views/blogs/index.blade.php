@extends('layout.app')

@section('title')
    {{ __('systemsetting::blogs.blogs_management') }}
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
                    ['title' => __('systemsetting::blogs.blogs_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('systemsetting::blogs.blogs_management') }}
                        </h4>
                        <div class="d-flex gap-2">
                            @can('blogs.create')
                            <a href="{{ route('admin.system-settings.blogs.create') }}"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('systemsetting::blogs.add_blog') }}
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
                                                <i class="uil uil-search me-1"></i>
                                                {{ __('systemsetting::blogs.search') }}
                                                <small
                                                    class="text-muted">({{ __('systemsetting::blogs.real_time') }})</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('systemsetting::blogs.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Category Filter --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="blog_category_id" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-tag me-1"></i>
                                                {{ __('systemsetting::blogs.category') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="blog_category_id">
                                                <option value="">{{ __('systemsetting::blogs.all_categories') }}
                                                </option>
                                                @foreach ($blogCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('systemsetting::blogs.status') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">
                                                    {{ __('systemsetting::blogs.all_statuses') }}</option>
                                                <option value="1">{{ __('systemsetting::blogs.active') }}
                                                </option>
                                                <option value="0">{{ __('systemsetting::blogs.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('systemsetting::blogs.created_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    {{-- Created Date To --}}
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('systemsetting::blogs.created_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('systemsetting::blogs.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('systemsetting::blogs.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="{{ __('systemsetting::blogs.reset_filters') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('systemsetting::blogs.reset_filters') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('systemsetting::blogs.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('systemsetting::blogs.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="blogsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::blogs.blog_information') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::blogs.image') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::blogs.status') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::blogs.created_at') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::blogs.action') }}</span>
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
    <x-delete-modal modalId="modal-delete-blog" :title="__('systemsetting::blogs.confirm_delete')" :message="__('systemsetting::blogs.delete_confirmation')" itemNameId="delete-blog-name"
        confirmBtnId="confirmDeleteBlogBtn" deleteRoute="{{ rtrim(route('admin.system-settings.blogs.index'), '/') }}"
        :cancelText="__('systemsetting::blogs.cancel')" :deleteText="__('systemsetting::blogs.delete_blog')" />
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
            if (urlParams.has('blog_category_id')) {
                $('#blog_category_id').val(urlParams.get('blog_category_id'));
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

            let table = $('#blogsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.system-settings.blogs.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.active = $('#active').val();
                        d.blog_category_id = $('#blog_category_id').val();
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
                        data: 'blog_information',
                        name: 'blog_information',
                        orderable: false,
                        searchable: true
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
                    lengthMenu: "{{ __('systemsetting::blogs.show') }} _MENU_",
                    info: "{{ __('systemsetting::blogs.showing_entries') }}",
                    infoEmpty: "{{ __('systemsetting::blogs.showing_empty') }}",
                    emptyTable: "{{ __('systemsetting::blogs.no_data_available') }}",
                    zeroRecords: "{{ __('systemsetting::blogs.no_blogs_found') }}",
                    loadingRecords: "{{ __('systemsetting::blogs.loading') }}",
                    processing: "{{ __('systemsetting::blogs.processing') }}",
                    search: "{{ __('systemsetting::blogs.search') }}:",
                }
            });

            // Initialize Select2
            if ($.fn.select2) {
                $('#entriesSelect, #active, #blog_category_id').select2({
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
                const category = $('#blog_category_id').val();
                const active = $('#active').val();
                const createdDateFrom = $('#created_date_from').val();
                const createdDateTo = $('#created_date_to').val();

                if (search) params.set('search', search);
                if (category) params.set('blog_category_id', category);
                if (active) params.set('active', active);
                if (createdDateFrom) params.set('created_date_from', createdDateFrom);
                if (createdDateTo) params.set('created_date_to', createdDateTo);

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.pushState({}, '', newUrl);
            }

            // Search button functionality
            $('#searchBtn').on('click', function() {
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
            $('#active, #blog_category_id, #created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.draw();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#blog_category_id').val('').trigger('change');
                $('#active').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                updateUrlWithFilters();
                table.draw();
            });

            // Status switcher
            $(document).on('change', '.status-switcher', function() {
                let switcher = $(this);
                let blogId = switcher.data('id');
                let isActive = switcher.is(':checked') ? 1 : 0;

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show();
                }

                $.ajax({
                    url: '{{ route('admin.system-settings.blogs.toggle-status', ':id') }}'
                        .replace(':id', blogId),
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
                                '{{ __('systemsetting::blogs.error_changing_status') }}'
                            );
                        }
                    }
                });
            });

            // Reload table after successful delete
            $('#modal-delete-blog').on('hidden.bs.modal', function() {
                // Check if delete was successful and reload table
                if (window.deleteSuccess) {
                    table.draw();
                    window.deleteSuccess = false;
                }
            });
        });
    </script>
@endpush
