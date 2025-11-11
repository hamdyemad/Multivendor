@extends('layout.app')

@section('title', __('areasettings::subregion.subregions_management'))

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
                    ['title' => __('areasettings::subregion.subregions_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('areasettings::subregion.subregions_management') }}</h4>
                        @can('area.subregion.create')
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.area-settings.subregions.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ __('areasettings::subregion.add_subregion') }}
                                </a>
                            </div>
                        @endcan
                    </div>

                    {{-- Alert --}}
                    <div class="alert alert-info glowing-alert" role="alert">
                        {{ __('common.live_search_info') }}
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
                                                <i class="uil uil-search me-1"></i>
                                                {{ __('common.search') }}
                                                <small class="text-muted">
                                                    ({{ __('common.real_time') ?? 'Real-time' }})
                                                </small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('areasettings::subregion.search_placeholder') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Region --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="region_id" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-map-pin me-1"></i>
                                                {{ __('areasettings::subregion.region') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="region_id">
                                                <option value="">{{ __('areasettings::subregion.all_regions') }}
                                                </option>
                                                @foreach ($regions as $region)
                                                    <option value="{{ $region['id'] }}">{{ $region['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('areasettings::subregion.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('areasettings::subregion.all_status') }}
                                                </option>
                                                <option value="1">{{ __('areasettings::subregion.active') }}</option>
                                                <option value="0">{{ __('areasettings::subregion.inactive') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-6">
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

                                    {{-- Created Date To --}}
                                    <div class="col-md-6">
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

                                    {{-- Export Excel --}}
                                    <div class="col-md-6">
                                        <button type="button" id="exportExcel"
                                            class="btn btn-primary btn-default btn-squared w-100"
                                            title="{{ __('common.excel') }}">
                                            <i class="uil uil-file-download-alt me-1"></i>
                                            {{ __('common.excel') }}
                                        </button>
                                    </div>

                                    {{-- Reset Filters --}}
                                    <div class="col-md-6">
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared w-100"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('common.reset') }}
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
                        <table id="subregionsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    @foreach ($languages as $language)
                                        <th>
                                            <span class="userDatatable-title"
                                                @if ($language->rtl) dir="rtl" @endif>
                                                {{ __('areasettings::subregion.name') }} ({{ $language->name }})
                                            </span>
                                        </th>
                                    @endforeach
                                    <th><span class="userDatatable-title">{{ __('areasettings::subregion.region') }}</span>
                                    </th>
                                    <th><span class="userDatatable-title">{{ __('areasettings::subregion.status') }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('areasettings::subregion.created_at') }}</span>
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
    <x-delete-modal modalId="modal-delete-subregion" :title="__('areasettings::subregion.confirm_delete')" :message="__('areasettings::subregion.delete_confirmation')" itemNameId="delete-subregion-name"
        confirmBtnId="confirmDeleteSubregionBtn" :deleteRoute="route('admin.area-settings.subregions.index')" :cancelText="__('areasettings::subregion.cancel')" :deleteText="__('areasettings::subregion.delete_subregion')" />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Subregions page loaded, initializing DataTable...');

            let per_page = 10;

            // Server-side processing with pagination
            let table = $('#subregionsDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '{{ route('admin.area-settings.subregions.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;

                        // Add search parameter from custom input
                        d.search = $('#search').val();

                        // Add filter parameters
                        d.region_id = $('#region_id').val();
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.order_column = d.order[0].column;
                            d.order_dir = d.order[0].dir;
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
                        alert('Error loading data. Status: ' + xhr.status +
                            '. Check console for details.');
                    }
                },
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    @foreach ($languages as $language)
                        {
                            data: 'names.{{ $language->id }}.value',
                            name: 'name_{{ $language->code }}',
                            orderable: true,
                            render: function(data, type, row) {
                                const nameData = row.names[{{ $language->id }}];
                                const rtlAttr = nameData.rtl ? ' dir="rtl"' : '';
                                return '<div class="userDatatable-content"' + rtlAttr +
                                    '>' + (nameData.value || '-') + '</div>';
                            }
                        },
                    @endforeach {
                        data: 'region.name',
                        name: 'region',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: true,
                        render: function(data, type, row) {
                            if (data) {
                                return `<div class="userDatatable-content">
                                    <span class="badge badge-success" style="border-radius: 6px; padding: 6px 12px;">
                                        <i class="uil uil-check me-1"></i>{{ __('areasettings::subregion.active') }}
                                    </span>
                                </div>`;
                            } else {
                                return `<div class="userDatatable-content">
                                    <span class="badge badge-danger" style="border-radius: 6px; padding: 6px 12px;">
                                        <i class="uil uil-times me-1"></i>{{ __('areasettings::subregion.inactive') }}
                                    </span>
                                </div>`;
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    @can('area.subregion.show')
                                    <a href="{{ url('admin/area-settings/subregions') }}/${row.id}"
                                    class="view btn btn-warning table_action_father"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('area.subregion.edit')
                                    <a href="{{ url('admin/area-settings/subregions') }}/${row.id}/edit"
                                    class="edit btn btn-info table_action_father"
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    @endcan

                                    @can('area.subregion.delete')
                                    <a href="javascript:void(0);"
                                    class="remove delete-subregion btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-subregion"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.display_name}"
                                    title="{{ trans('common.delete') }}">
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
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ __('areasettings::subregion.subregions_management') }}'
                }],
                searching: true, // Enable built-in search
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('areasettings::subregion.no_subregions_found') ?? 'No sub-regions found' }}",
                    emptyTable: "{{ __('areasettings::subregion.no_subregions_found') ?? 'No sub-regions found' }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
                    paginate: {
                        first: '{{ __('common.first') ?? 'First' }}',
                        last: '{{ __('common.last') ?? 'Last' }}',
                        next: '{{ __('common.next') ?? 'Next' }}',
                        previous: '{{ __('common.previous') ?? 'Previous' }}'
                    },
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
                }
            });

            // Initialize Select2 on custom entries select
            if ($.fn.select2) {
                $('#entriesSelect').select2({
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

            // Search with server-side processing and debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    console.log('🔍 Search triggered:', $('#search').val());
                    table.ajax.reload(); // Reload data from server with new search value
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
                console.log('🔍 Search changed:', $(this).val());
                table.ajax.reload();
            });

            // Server-side filter event listeners - reload data when filters change
            $('#region_id, #active, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#region_id').val('');
                $('#active').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Reload table with cleared filters
                table.ajax.reload();
            });
        });
    </script>
@endpush
