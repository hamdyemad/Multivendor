@extends('layout.app')

@section('title')
    {{ __('systemsetting::messages.messages') }}
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
                    ['title' => __('systemsetting::messages.messages')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('systemsetting::messages.messages') }}</h4>
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
                                    <div class="col-md-3">
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
                                                placeholder="{{ __('systemsetting::messages.search_placeholder') ?? 'Search by name, email, or title...' }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('systemsetting::messages.status') ?? 'Status' }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status">
                                                <option value="">
                                                    {{ __('systemsetting::messages.all_status') ?? 'All Status' }}
                                                </option>
                                                <option value="pending">{{ __('systemsetting::messages.pending') }}</option>
                                                <option value="read">{{ __('systemsetting::messages.read') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created Date From --}}
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
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

                                    <div class="col-md-12 d-flex">
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i> {{ __('common.reset_filters') }}
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
                        <table id="messagesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::messages.title') ?? 'Title' }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::messages.sender') ?? 'Sender' }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::messages.status') ?? 'Status' }}</span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title">{{ __('systemsetting::messages.created_at') ?? 'Date' }}</span>
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
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('Messages page loaded, initializing DataTable...');

            let per_page = 10;

            // Server-side processing with pagination
            let table = $('#messagesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.messages.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.status = $('#status').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        if (d.order && d.order.length > 0) {
                            d.order_column = d.order[0].column;
                            d.order_dir = d.order[0].dir;
                        }
                        return d;
                    },
                    dataSrc: function(json) {
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;

                        if (json.error) {
                            console.error('Server returned error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTables AJAX Error:', {
                            xhr: xhr,
                            error: error,
                            code: code
                        });
                        alert('Error loading data. Status: ' + xhr.status);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    {
                        data: 'title',
                        name: 'title',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content"><span>' + data +
                                '</span><br><small>' + (row.email || '') + '</small></div>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        render: function(data, type, row) {
                            const statusColors = {
                                'pending': 'warning',
                                'read': 'success',
                                'archived': 'secondary'
                            };
                            const statusLabels = {
                                'pending': '{{ __('systemsetting::messages.pending') }}',
                                'read': '{{ __('systemsetting::messages.read') }}',
                                'archived': '{{ __('systemsetting::messages.archived') }}'
                            };
                            const color = statusColors[data] || 'secondary';
                            const label = statusLabels[data] || data;
                            return '<div class="userDatatable-content"><span class="badge badge-' +
                                color + ' badge-lg badge-round text-capitalize">' + label +
                                '</span></div>';
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                        // Render function removed; relying on HTML from Controller
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
                    title: '{{ __('systemsetting::messages.messages') }}'
                }],
                searching: true,
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('systemsetting::messages.no_messages_found') ?? 'No messages found' }}",
                    emptyTable: "{{ __('systemsetting::messages.no_messages_found') ?? 'No messages found' }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:"
                }
            });

            // Initialize Select2
            if ($.fn.select2) {
                $('#entriesSelect').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
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

            // Search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();
                searchTimer = setTimeout(function() {
                    table.search(searchValue).draw();
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
                table.search($(this).val()).draw();
            });

            // Server-side filter event listeners
            $('#status, #created_date_from, #created_date_to').on('change', function() {
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#status').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                table.search('').ajax.reload();
            });

            // Mark as Read Logic
            $(document).on('click', '.mark-read-message', function(e) {
                e.preventDefault();
                const url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message ||
                            '{{ __('systemsetting::messages.mark_read_success') }}'
                        );
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON ? xhr.responseJSON.message :
                            '{{ __('systemsetting::messages.mark_read_error') }}';
                        toastr.error(msg);
                    }
                });
            });

            // Handle delete modal
            $(document).on('click', '.delete-message', function() {
                const messageId = $(this).data('item-id');
                const messageTitle = $(this).data('item-name');
                $('#delete-message-name').text(messageTitle);
                $('#confirmDeleteMessageBtn').data('item-id', messageId);
            });

            // Confirm delete button
            $(document).on('click', '#confirmDeleteMessageBtn', function() {
                const messageId = $(this).data('item-id');

                // Make DELETE request using fetch
                fetch(`{{ url('messages') }}/${messageId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message and reload
                            toastr.success(data.message, '{{ __('common.success') }}');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(data.message, '{{ __('common.error') }}');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('{{ __('systemsetting::messages.delete_error') }}',
                            '{{ __('common.error') }}');
                    });
            });
        });
    </script>
@endpush
