@extends('layout.app')
@section('title', __('activity.activities_management'))

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
                    ['title' => __('activity.activities_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    {{-- Header with Add Button --}}
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('activity.activities_management') }}</h4>
                        @can('activities.create')
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.category-management.activities.create') }}"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> {{ __('activity.add_activity') }}
                                </a>
                            </div>
                        @endcan
                    </div>

                    {{-- Filters Component --}}
                    <x-datatable-filters 
                        :searchPlaceholder="__('activity.search_by_name')"
                        :showActiveFilter="true"
                        :activeOptions="[
                            'label' => __('activity.activation'),
                            'all' => __('activity.all'),
                            'active' => __('activity.active'),
                            'inactive' => __('activity.inactive')
                        ]"
                    />

                    {{-- Table Component --}}
                    <x-datatable-table 
                        tableId="activitiesDataTable"
                        :languages="$languages"
                        :columns="[
                            'nameLabel' => __('activity.name'),
                            'additional' => [
                                __('activity.activation'),
                                __('activity.created_at')
                            ]
                        ]"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal with Loading Component --}}
    <x-delete-with-loading 
        modalId="modal-delete-activity" 
        tableId="activitiesDataTable" 
        deleteButtonClass="delete-activity"
        :title="__('main.confirm delete')" 
        :message="__('main.are you sure you want to delete this')" 
        itemNameId="delete-activity-name" 
        confirmBtnId="confirmDeleteBtn"
        :cancelText="__('main.cancel')" 
        :deleteText="__('main.delete')" 
        :loadingDeleting="trans('main.deleting') ?? 'Deleting...'" 
        :loadingPleaseWait="trans('main.please wait') ?? 'Please wait...'" 
        :loadingDeletedSuccessfully="trans('main.deleted success') ?? 'Deleted Successfully!'" 
        :loadingRefreshing="trans('main.refreshing') ?? 'Refreshing...'"
        :errorDeleting="__('main.error on delete')" 
    />
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            // Server-side processing with pagination
            var table = $('#activitiesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.category-management.activities.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        if (d.order && d.order.length > 0) {
                            d.orderColumnIndex = d.order[0].column;
                            d.orderDirection = d.order[0].dir;
                        }

                        console.log('📤 Sending to server:', {
                            search: d.search,
                            active: d.active,
                            created_date_from: d.created_date_from,
                            created_date_to: d.created_date_to,
                            orderColumnIndex: d.orderColumnIndex,
                            orderDirection: d.orderDirection
                        });

                        return d;
                    },
                    dataSrc: function(json) {
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.log('DataTables Error:', xhr, error, code);
                        alert('Error loading data. Please check console for details.');
                    }
                },
                columns: [
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    @foreach ($languages as $language)
                        {
                            data: 'translations.{{ $language->code }}.name',
                            name: 'name_{{ $language->code }}',
                            render: function(data, type, row) {
                                if (type === 'sort' || type === 'type') {
                                    return row.translations && row.translations['{{ $language->code }}'] ?
                                        row.translations['{{ $language->code }}'].name : '-';
                                }

                                if (row.translations && row.translations['{{ $language->code }}']) {
                                    const translation = row.translations['{{ $language->code }}'];
                                    const name = translation.name || '-';
                                    if (translation.rtl) {
                                        return '<span dir="rtl">' + $('<div>').text(name).html() + '</span>';
                                    }
                                    return $('<div>').text(name).html();
                                }
                                return '-';
                            }
                        },
                    @endforeach
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data, type, row) {
                            if (type === 'sort' || type === 'type') {
                                return data ? 1 : 0;
                            }

                            if (data == 1) {
                                return '<span class="badge badge-success badge-round badge-lg">{{ __('activity.active') }}</span>';
                            } else {
                                return '<span class="badge badge-danger badge-round badge-lg">{{ __('activity.inactive') }}</span>';
                            }
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
                            let viewRoute = '{{ route('admin.category-management.activities.show', ':id') }}',
                                editRoute = '{{ route('admin.category-management.activities.edit', ':id') }}',
                                deleteRoute = '{{ route('admin.category-management.activities.destroy', ':id') }}';
                            return `
                            <ul class="mb-0 d-flex flex-wrap justify-content-start">
                                @can('activities.show')
                                <li>
                                    <a href="${viewRoute.replace(':id', row.id)}"
                                    class="btn btn-primary table_action_father me-1"
                                    title="{{ trans('common.view') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                </li>
                                @endcan
                                @can('activities.edit')
                                <li>
                                    <a href="${editRoute.replace(':id', row.id)}"
                                    class="btn btn-warning table_action_father me-1"
                                    title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                </li>
                                @endcan
                                @can('activities.delete')
                                <li>
                                    <a href="javascript:void(0);" style=""
                                    class="btn btn-danger delete-activity table_action_father"
                                    title="{{ trans('common.delete') }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-activity"
                                    data-item-id="${row.id}"
                                    data-item-name="${$('<div>').text(row.first_name).html()}"
                                    data-url="${deleteRoute.replace(':id', row.id)}">
                                        <i class="uil uil-trash-alt table_action_icon" ></i>
                                    </a>
                                </li>
                                @endcan
                            </ul>`;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'desc']],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ __('activity.activities_management') }}'
                }],
                searching: true,
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('activity.no_activities_found') ?? 'No activities found' }}",
                    emptyTable: "{{ __('activity.no_activities_found') ?? 'No activities found' }}",
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
                    width: 'auto'
                });
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
                    table.ajax.reload();
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
                console.log('🔍 Search changed:', $(this).val());
                table.ajax.reload();
            });

            // Custom filter function for active status and dates on cached data
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    if (settings.nTable.id !== 'activitiesDataTable') {
                        return true;
                    }

                    var activeFilter = $('#active').val();
                    var dateFrom = $('#created_date_from').val();
                    var dateTo = $('#created_date_to').val();

                    if (activeFilter && activeFilter !== '') {
                        var colIndex = {{ count($languages) + 1 }};
                        var rowNode = table.row(dataIndex).node();
                        if (!rowNode) {
                            return true;
                        }

                        var cells = $(rowNode).find('td');
                        if (cells.length <= colIndex) {
                            return true;
                        }

                        var cellHtml = $(cells[colIndex]).html();
                        if (!cellHtml) {
                            return true;
                        }

                        var isActiveRow = cellHtml.indexOf('badge-success') > -1;
                        var isInactiveRow = cellHtml.indexOf('badge-danger') > -1;

                        if (activeFilter === '1') {
                            return isActiveRow;
                        } else if (activeFilter === '0') {
                            return isInactiveRow;
                        }
                    }

                    if (dateFrom || dateTo) {
                        var dateColumn = data[{{ count($languages) + 2 }}];
                        if (dateColumn) {
                            var rowDate = dateColumn.replace(/<[^>]*>/g, '').trim().split(' ')[0];
                            if (dateFrom && rowDate < dateFrom) return false;
                            if (dateTo && rowDate > dateTo) return false;
                        }
                    }

                    return true;
                }
            );

            // Server-side filter event listeners
            $('#active, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                $('#search').val('');
                $('#active').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                table.search('').ajax.reload();
            });
        });
    </script>
@endpush
