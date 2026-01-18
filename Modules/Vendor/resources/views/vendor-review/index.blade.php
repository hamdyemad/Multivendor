@extends('layout.app')

@section('title', __('vendor::vendor-review.vendor_reviews'))

@push('styles')
    <style>
        .review-modal-content {
            border-radius: 20px;
            overflow: hidden;
        }

        .review-header {
            background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
            padding: 25px;
            border: none;
        }

        .review-header .modal-title {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
        }

        .review-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .detail-group {
            margin-bottom: 25px;
            padding: 0 10px;
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
            margin-bottom: 10px;
        }

        .detail-value {
            color: #272b41;
            font-size: 16px;
            font-weight: 600;
        }

        .rating-glass-badge {
            background: #f8f9fb;
            border: 1px solid #edf0f5;
            padding: 12px 20px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }

        .review-text-bubble {
            background: #fdfdfd;
            border-left: 4px solid #a777e3;
            padding: 20px;
            border-radius: 0 12px 12px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            font-style: italic;
            color: #5a5f7d;
            line-height: 1.6;
        }

        .rejection-notice {
            background: #fff5f5;
            border: 1px dashed #feb2b2;
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
        }

        .rating-badge {
            background: #fff;
            border: 1px solid #f1f2f6;
            padding: 4px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .text-warning {
            color: #ffb300 !important;
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
                    ['title' => __('vendor::vendor-review.vendor_reviews')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-star me-2"></i>
                            {{ __('vendor::vendor-review.vendor_reviews') }}
                        </h4>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('common.status') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status_filter">
                                                <option value="all">{{ __('common.all') }}</option>
                                                <option value="pending">{{ __('common.pending') }}</option>
                                                <option value="approved">{{ __('common.approved') }}</option>
                                                <option value="rejected">{{ __('common.rejected') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="rating_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-star me-1"></i>
                                                {{ __('vendor::vendor-review.rating') }}
                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="rating_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="5">5 ⭐</option>
                                                <option value="4">4 ⭐</option>
                                                <option value="3">3 ⭐</option>
                                                <option value="2">2 ⭐</option>
                                                <option value="1">1 ⭐</option>
                                            </select>
                                        </div>
                                    </div>

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

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="{{ __('common.search') }}">
                                            <i class="uil uil-search me-1"></i>
                                            {{ __('common.search') }}
                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared"
                                            title="{{ __('common.reset') }}">
                                            <i class="uil uil-redo me-1"></i>
                                            {{ __('common.reset_filters') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reviews DataTable --}}
                    <div class="table-responsive">
                        <table id="reviewsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('vendor::vendor-review.vendor') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('vendor::vendor-review.customer') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('vendor::vendor-review.rating') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.status') }}</span></th>
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

    {{-- View Review Modal Template --}}
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content review-modal-content border-0 shadow-2xl">
                <div class="modal-header review-header">
                    <h5 class="modal-title">
                        <i class="uil uil-comment-alt-notes me-2"></i>{{ __('vendor::vendor-review.review_details') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-40" id="viewModalBody"></div>
            </div>
        </div>
    </div>

    {{-- Reject Review Modal Template --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-opacity-10 border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="uil uil-times-circle me-2"></i>{{ __('vendor::vendor-review.reject_review') }}
                        </h5>
                        <small class="text-muted d-block mt-1" id="rejectVendorName"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-2"></i>
                            {{ __('vendor::vendor-review.rejection_warning') }}
                        </div>
                        <div class="form-group">
                            <label for="rejection_reason" class="form-label fw-500">
                                {{ __('vendor::vendor-review.rejection_reason') }} <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-lg nockeditor" id="rejection_reason" name="rejection_reason"
                                rows="5"
                                placeholder="{{ __('vendor::vendor-review.rejection_reason_placeholder') }}"
                                required></textarea>
                            <small class="text-muted d-block mt-2">
                                <i class="uil uil-info-circle me-1"></i>
                                {{ __('vendor::vendor-review.rejection_reason_help') }}
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-danger" id="submitRejectBtn">
                            <i class="uil uil-check me-1"></i>{{ __('common.reject') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const search = urlParams.get('search') || '';
            const status = urlParams.get('status') || 'all';
            const star = urlParams.get('star') || '';
            const createdFrom = urlParams.get('created_date_from') || '';
            const createdTo = urlParams.get('created_date_to') || '';

            // Set filter values from URL
            if (search) $('#search').val(search);
            if (status) $('#status_filter').val(status).trigger('change');
            if (star) $('#rating_filter').val(star).trigger('change');
            if (createdFrom) $('#created_date_from').val(createdFrom);
            if (createdTo) $('#created_date_to').val(createdTo);

            let table = $('#reviewsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.vendor-reviews.datatable') }}',
                    data: function(d) {
                        d.status = $('#status_filter').val();
                        d.star = $('#rating_filter').val();
                        d.search = $('#search').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'vendor_info', name: 'vendor_info', orderable: false, searchable: false },
                    { data: 'customer_name', name: 'customer_name', orderable: false, searchable: true },
                    { data: 'stars', name: 'stars', orderable: false, searchable: false },
                    { data: 'status_info', name: 'status_info', orderable: false, searchable: false },
                    { data: 'created_date', name: 'created_at', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']],
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

            // Search button
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#status_filter').val('all').trigger('change');
                $('#rating_filter').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                window.history.replaceState({}, '', window.location.pathname);
                table.ajax.reload();
            });

            // View Review Modal
            $(document).on('click', '.btn-view-review', function() {
                const review = $(this).data('review');

                let starsHtml = '';
                for (let i = 0; i < 5; i++) {
                    starsHtml += `<i class="uil uil-star ${i < review.star ? 'text-warning' : 'text-muted'}" style="font-size: 24px;"></i>`;
                }

                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-store"></i> {{ __('vendor::vendor-review.vendor') }}</label>
                                <div class="detail-value">${review.vendor_name}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label"><i class="uil uil-user"></i> {{ __('vendor::vendor-review.customer') }}</label>
                                <div class="detail-value">${review.customer_name || '-'}</div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-group">
                        <label class="detail-label"><i class="uil uil-award"></i> {{ __('vendor::vendor-review.rating') }}</label>
                        <div class="rating-glass-badge">
                            <div class="d-flex gap-1">${starsHtml}</div>
                            <div class="h-100 border-start ps-3 ms-2">
                                <span class="fw-bold text-dark fs-20">${review.star}.0</span>
                                <small class="text-muted ms-1">/ 5.0</small>
                            </div>
                        </div>
                    </div>

                    <div class="detail-group">
                        <label class="detail-label"><i class="uil uil-align-left"></i> {{ __('vendor::vendor-review.review') }}</label>
                        <div class="review-text-bubble">"${review.review}"</div>
                    </div>
                `;

                if (review.status === 'rejected' && review.rejection_reason) {
                    html += `
                        <div class="detail-group mb-0">
                            <label class="detail-label text-danger"><i class="uil uil-info-circle"></i> {{ __('vendor::vendor-review.rejection_reason') }}</label>
                            <div class="rejection-notice">
                                <p class="m-0 fs-14 fw-500 text-danger">${review.rejection_reason}</p>
                            </div>
                        </div>
                    `;
                }

                $('#viewModalBody').html(html);
                new bootstrap.Modal(document.getElementById('viewModal')).show();
            });

            // Reject Review Modal
            $(document).on('click', '.btn-reject-review', function() {
                const reviewId = $(this).data('review-id');
                const review = $(this).data('review');
                let url = "{{ route('admin.vendor-reviews.reject', ':id') }}".replace(':id', reviewId);
                $('#rejectForm').attr('action', url);
                $('#rejectVendorName').text('{{ __('vendor::vendor-review.vendor') }}: ' + (review.vendor_name || '-'));
                $('#rejection_reason').val('');

                const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
                rejectModal.show();
            });

            // Reject Form Submit
            $('#rejectForm').on('submit', function(e) {
                e.preventDefault();
                const url = $(this).attr('action');
                const reason = $('#rejection_reason').val();
                const submitBtn = $('#submitRejectBtn');

                if (!reason.trim()) {
                    toastr.warning('{{ __('vendor::vendor-review.rejection_reason_required') }}');
                    return;
                }

                submitBtn.prop('disabled', true);
                const originalBtnHtml = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>{{ __('common.processing') }}');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', rejection_reason: reason },
                    success: function(response) {
                        bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
                        $('#rejection_reason').val('');
                        table.ajax.reload();
                        toastr.success('{{ __('vendor::vendor-review.review_rejected') }}', '{{ __('common.success') }}');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __('common.error_occurred') }}', '{{ __('common.error') }}');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalBtnHtml);
                    }
                });
            });

            // Approve Review
            $(document).on('click', '.btn-approve-review', function(e) {
                e.preventDefault();
                const reviewId = $(this).data('review-id');
                const $btn = $(this);

                $btn.prop('disabled', true);
                const originalBtnHtml = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm"></span>');

                let url = "{{ route('admin.vendor-reviews.approve', ':id') }}".replace(':id', reviewId);
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        table.ajax.reload();
                        toastr.success('{{ __('vendor::vendor-review.review_approved') }}', '{{ __('common.success') }}');
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __('common.error_occurred') }}', '{{ __('common.error') }}');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.html(originalBtnHtml);
                    }
                });
            });
        });
    </script>
@endpush
