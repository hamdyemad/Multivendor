@extends('layout.app')

@section('title', $isArchived ? __('order::request-quotation.archived_requests') : __('order::request-quotation.all_requests'))

@push('styles')
    <style>
        .detail-group {
            margin-bottom: 20px;
        }
        .detail-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #868eae;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .detail-value {
            color: #272b41;
            font-size: 15px;
            font-weight: 500;
        }
    </style>
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
                    ['title' => $isArchived ? __('order::request-quotation.archived_requests') : __('order::request-quotation.all_requests')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-file-question-alt me-2"></i>
                            {{ $isArchived ? __('order::request-quotation.archived_requests') : __('order::request-quotation.all_requests') }}
                        </h4>
                    </div>

                    {{-- Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search_input" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i>
                                                {{ __('common.search') }}
                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search_input" placeholder="{{ __('order::request-quotation.search_placeholder') }}">
                                        </div>
                                    </div>

                                    @if(!$isArchived)
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('common.status') }}
                                            </label>
                                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="status_filter">
                                                <option value="all">{{ __('common.all') }}</option>
                                                <option value="pending">{{ __('order::request-quotation.status_pending') }}</option>
                                                <option value="sent_offer">{{ __('order::request-quotation.status_sent_offer') }}</option>
                                                <option value="accepted_offer">{{ __('order::request-quotation.status_accepted_offer') }}</option>
                                                <option value="rejected_offer">{{ __('order::request-quotation.status_rejected_offer') }}</option>
                                                <option value="order_created">{{ __('order::request-quotation.status_order_created') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_from') }}
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="created_date_from">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                {{ __('common.created_date_to') }}
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-center">
                                        <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared me-1" title="{{ __('common.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('common.search') }}
                                        </button>
                                        <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared" title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('common.reset') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="quotationsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('order::request-quotation.customer_info') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('order::request-quotation.order_number') }}</span></th>
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

    {{-- View Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title  text-white">
                        <i class="uil uil-file-info-alt me-2"></i>{{ __('order::request-quotation.request_details') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="viewModalBody"></div>
            </div>
        </div>
    </div>

    {{-- Send Offer Modal --}}
    <div class="modal fade" id="sendOfferModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white">
                        <i class="uil uil-envelope-send me-2"></i>{{ __('order::request-quotation.send_offer') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="sendOfferForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="offer_quotation_id">
                        <div class="form-group mb-3">
                            <label for="offer_price" class="il-gray fs-14 fw-500 mb-10">
                                <i class="uil uil-dollar-sign me-1"></i>{{ __('order::request-quotation.offer_price') }} <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="offer_price" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="offer_notes" class="il-gray fs-14 fw-500 mb-10">
                                <i class="uil uil-notes me-1"></i>{{ __('order::request-quotation.offer_notes') }}
                            </label>
                            <input type="text" class="form-control ip-gray radius-xs b-light px-15"  id="offer_notes"placeholder="{{ __('order::request-quotation.offer_notes_placeholder') }}">
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-info" id="confirmSendOfferBtn">
                            <i class="uil uil-envelope-send me-1"></i>{{ __('order::request-quotation.send_offer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Archive Confirmation Modal --}}
    <div class="modal fade" id="archiveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark">
                        <i class="uil uil-archive me-2"></i>{{ __('order::request-quotation.archive') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="uil uil-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                        <p class="mt-3 mb-0 fs-16">{{ __('order::request-quotation.confirm_archive') }}</p>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-warning" id="confirmArchiveBtn">
                        <i class="uil uil-archive me-1"></i>{{ __('order::request-quotation.archive') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const isArchived = {{ $isArchived ? 'true' : 'false' }};

            let table = $('#quotationsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.request-quotations.datatable') }}',
                    data: function(d) {
                        d.is_archived = isArchived;
                        d.status = $('#status_filter').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.search_text = $('#search_input').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'customer_info', name: 'customer_info', orderable: false, searchable: true },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                    { data: 'order_number', name: 'order_number', orderable: false, searchable: false },
                    { data: 'created_date', name: 'created_at', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[4, 'desc']],
                language: {
                    lengthMenu: "{{ __('common.show') }} _MENU_",
                    info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.entries') }}",
                    infoEmpty: "{{ __('common.showing') }} 0 {{ __('common.to') }} 0 {{ __('common.of') }} 0 {{ __('common.entries') }}",
                    infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.total_entries') }})",
                    loadingRecords: "{{ __('common.loading') }}",
                    processing: "{{ __('common.processing') }}",
                    emptyTable: "{{ __('common.no_data_available') }}",
                    paginate: {
                        first: "{{ __('common.first') }}",
                        last: "{{ __('common.last') }}",
                        next: "{{ __('common.next') }}",
                        previous: "{{ __('common.previous') }}"
                    }
                },
                dom: 'lrtip',
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pageLength: 10
            });

            // Handle URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            const statusParam = urlParams.get('status');
            
            if (searchParam) {
                $('#search_input').val(searchParam);
            }
            if (statusParam) {
                $('#status_filter').val(statusParam);
            }
            
            // Reload table if URL params exist
            if (searchParam || statusParam) {
                table.ajax.reload();
            }

            // Search button
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search_input').val('');
                $('#status_filter').val('all');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                table.ajax.reload();
            });

            // Search on Enter key
            $('#search_input').on('keypress', function(e) {
                if (e.which === 13) {
                    table.ajax.reload();
                }
            });

            // View Modal
            $(document).on('click', '.btn-view', function() {
                const data = $(this).data('quotation');
                
                const statusLabels = {
                    'pending': '{{ __('order::request-quotation.status_pending') }}',
                    'sent_offer': '{{ __('order::request-quotation.status_sent_offer') }}',
                    'accepted_offer': '{{ __('order::request-quotation.status_accepted_offer') }}',
                    'rejected_offer': '{{ __('order::request-quotation.status_rejected_offer') }}',
                    'order_created': '{{ __('order::request-quotation.status_order_created') }}',
                    'archived': '{{ __('order::request-quotation.status_archived') }}'
                };

                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-user"></i> {{ __('order::request-quotation.name') }}</label>
                                <div class="detail-value">${data.customer_name || '-'}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-envelope"></i> {{ __('order::request-quotation.email') }}</label>
                                <div class="detail-value">${data.customer_email || '-'}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-phone"></i> {{ __('order::request-quotation.phone') }}</label>
                                <div class="detail-value">${data.customer_phone || '-'}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-check-circle"></i> {{ __('common.status') }}</label>
                                <div class="detail-value">${statusLabels[data.status] || data.status}</div>
                            </div>
                        </div>
                    </div>
                `;

                // Address details section
                if (data.address_title || data.city || data.region || data.full_address) {
                    html += `<hr class="my-3">
                        <h6 class="text-primary mb-3"><i class="uil uil-map-marker me-1"></i> {{ __('order::request-quotation.address_info') }}</h6>`;
                    
                    if (data.address_title) {
                        html += `<div class="row">
                            <div class="col-md-12">
                                <div class="detail-group">
                                    <label class="detail-label"><i class="uil uil-home"></i> {{ __('customer::customer.address_title') }}</label>
                                    <div class="detail-value">${data.address_title}</div>
                                </div>
                            </div>
                        </div>`;
                    }
                    
                    html += `<div class="row">`;
                    if (data.country) {
                        html += `<div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-globe"></i> {{ __('customer::customer.country') }}</label>
                                <div class="detail-value">${data.country}</div>
                            </div>
                        </div>`;
                    }
                    if (data.city) {
                        html += `<div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-building"></i> {{ __('customer::customer.city') }}</label>
                                <div class="detail-value">${data.city}</div>
                            </div>
                        </div>`;
                    }
                    html += `</div>`;
                    
                    html += `<div class="row">`;
                    if (data.region) {
                        html += `<div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-map"></i> {{ __('customer::customer.region') }}</label>
                                <div class="detail-value">${data.region}</div>
                            </div>
                        </div>`;
                    }
                    if (data.subregion) {
                        html += `<div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-location-point"></i> {{ __('customer::customer.sub_region') }}</label>
                                <div class="detail-value">${data.subregion}</div>
                            </div>
                        </div>`;
                    }
                    html += `</div>`;
                    
                    if (data.full_address) {
                        html += `<div class="detail-group">
                            <label class="detail-label"><i class="uil uil-map-marker"></i> {{ __('order::request-quotation.address') }}</label>
                            <div class="detail-value">${data.full_address}</div>
                        </div>`;
                    }
                }

                html += `
                    <div class="detail-group">
                        <label class="detail-label"><i class="uil uil-notes"></i> {{ __('order::request-quotation.notes') }}</label>
                        <div class="detail-value">${data.notes || '-'}</div>
                    </div>
                `;

                // Show offer details if available
                if (data.offer_price) {
                    html += `
                        <hr class="my-3">
                        <h6 class="text-info mb-3"><i class="uil uil-envelope-send me-1"></i> {{ __('order::request-quotation.offer_details') }}</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label"><i class="uil uil-dollar-sign"></i> {{ __('order::request-quotation.offer_price') }}</label>
                                    <div class="detail-value text-success fw-bold">${parseFloat(data.offer_price).toFixed(2)} {{ currency() }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label"><i class="uil uil-clock"></i> {{ __('order::request-quotation.offer_sent_at') }}</label>
                                    <div class="detail-value">${data.offer_sent_at || '-'}</div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    if (data.offer_notes) {
                        html += `
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-notes"></i> {{ __('order::request-quotation.offer_notes') }}</label>
                                <div class="detail-value">${data.offer_notes}</div>
                            </div>
                        `;
                    }

                    if (data.offer_responded_at) {
                        html += `
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-clock"></i> {{ __('order::request-quotation.offer_responded_at') }}</label>
                                <div class="detail-value">${data.offer_responded_at}</div>
                            </div>
                        `;
                    }
                }

                html += `
                    <hr class="my-3">
                    <div class="detail-group mb-0">
                        <label class="detail-label"><i class="uil uil-calendar-alt"></i> {{ __('common.created_at') }}</label>
                        <div class="detail-value">${data.created_at}</div>
                    </div>
                `;

                $('#viewModalBody').html(html);
                new bootstrap.Modal(document.getElementById('viewModal')).show();
            });

            // Send Offer - show modal
            let sendOfferId = null;
            $(document).on('click', '.btn-send-offer', function() {
                sendOfferId = $(this).data('id');
                $('#offer_quotation_id').val(sendOfferId);
                $('#offer_price').val('');
                $('#offer_notes').val('');
                new bootstrap.Modal(document.getElementById('sendOfferModal')).show();
            });

            // Confirm Send Offer
            $('#sendOfferForm').on('submit', function(e) {
                e.preventDefault();
                
                const quotationId = $('#offer_quotation_id').val();
                if (!quotationId) return;

                const $btn = $('#confirmSendOfferBtn');
                $btn.prop('disabled', true);
                const originalHtml = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span>{{ __('common.processing') }}');

                $.ajax({
                    url: '{{ route('admin.request-quotations.send-offer', ':id') }}'.replace(':id', quotationId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        offer_price: $('#offer_price').val(),
                        offer_notes: $('#offer_notes').val()
                    },
                    success: function(response) {
                        bootstrap.Modal.getInstance(document.getElementById('sendOfferModal')).hide();
                        table.ajax.reload();
                        toastr.success(response.message, '{{ __('common.success') }}');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __('common.error_occurred') }}', '{{ __('common.error') }}');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.html(originalHtml);
                    }
                });
            });

            // Archive - show modal
            let archiveId = null;
            $(document).on('click', '.btn-archive', function() {
                archiveId = $(this).data('id');
                new bootstrap.Modal(document.getElementById('archiveModal')).show();
            });

            // Confirm Archive
            $('#confirmArchiveBtn').on('click', function() {
                if (!archiveId) return;

                const $btn = $(this);
                $btn.prop('disabled', true);
                const originalHtml = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span>{{ __('common.processing') }}');

                $.ajax({
                    url: '{{ route('admin.request-quotations.archive', ':id') }}'.replace(':id', archiveId),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        bootstrap.Modal.getInstance(document.getElementById('archiveModal')).hide();
                        table.ajax.reload();
                        toastr.success(response.message, '{{ __('common.success') }}');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __('common.error_occurred') }}', '{{ __('common.error') }}');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.html(originalHtml);
                        archiveId = null;
                    }
                });
            });
        });
    </script>
@endpush
