@extends('layout.app')

@section('title', __('systemsetting::push-notification.all_notifications'))

@push('styles')
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
                    ['title' => __('systemsetting::push-notification.all_notifications')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-bell me-2"></i>
                            {{ __('systemsetting::push-notification.all_notifications') }}
                        </h4>
                        @can('push-notifications.create')
                        <a href="{{ route('admin.system-settings.push-notifications.create') }}" class="btn btn-primary btn-default btn-squared">
                            <i class="uil uil-plus me-1"></i>
                            {{ __('systemsetting::push-notification.send_notification') }}
                        </a>
                        @endcan
                    </div>

                    {{-- Search --}}
                    <div class="mb-25">
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search_input" placeholder="{{ __('systemsetting::push-notification.search_placeholder') }}" style="max-width: 300px;">
                            <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared">
                                <i class="uil uil-search m-0"></i>
                            </button>
                            <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared">
                                <i class="uil uil-redo m-0"></i>
                            </button>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="notificationsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::push-notification.title') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.type') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::push-notification.recipients') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('systemsetting::push-notification.created_by') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.created_at') }}</span></th>
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
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white">
                    <h5 class="modal-title">
                        <i class="uil uil-trash me-2"></i>{{ __('common.delete') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="uil uil-exclamation-triangle text-danger" style="font-size: 48px;"></i>
                        <p class="mt-3 mb-0 fs-16">{{ __('systemsetting::push-notification.confirm_delete') }}</p>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="uil uil-trash me-1"></i>{{ __('common.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#notificationsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.system-settings.push-notifications.datatable') }}',
                    data: function(d) {
                        d.search_text = $('#search_input').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title_display', name: 'title', orderable: false, searchable: false },
                    { data: 'type_badge', name: 'type', orderable: false, searchable: false },
                    { data: 'recipients_count', name: 'recipients_count', orderable: false, searchable: false },
                    { data: 'created_by_name', name: 'created_by', orderable: false, searchable: false },
                    { data: 'created_date', name: 'created_at', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
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
                },
                dom: 'lrtip',
                pageLength: 10
            });

            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#search_input').on('keypress', function(e) {
                if (e.which === 13) table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search_input').val('');
                table.ajax.reload();
            });

            // Delete
            let deleteId = null;
            $(document).on('click', '.btn-delete', function() {
                deleteId = $(this).data('id');
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (!deleteId) return;

                const $btn = $(this);
                $btn.prop('disabled', true);

                $.ajax({
                    url: '{{ route('admin.system-settings.push-notifications.destroy', ':id') }}'.replace(':id', deleteId),
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                        table.ajax.reload();
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __('common.error_occurred') }}');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        deleteId = null;
                    }
                });
            });
        });
    </script>
@endpush
