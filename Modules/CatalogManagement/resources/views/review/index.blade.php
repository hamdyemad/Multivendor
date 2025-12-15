@extends('layout.app')

@section('title', __('catalogmanagement::review.product_reviews'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::review.product_reviews')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-star me-2"></i>
                            {{ __('catalogmanagement::review.product_reviews') }}
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
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="status_filter">
                                                <option value="">{{ __('common.all') }}</option>
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
                                                {{ __('catalogmanagement::review.rating') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
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
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::review.product') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::review.customer') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::review.rating') }}</span></th>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('catalogmanagement::review.review_details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewModalBody"></div>
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
                            <i class="uil uil-times-circle me-2"></i>{{ __('catalogmanagement::review.reject_review') }}
                        </h5>
                        <small class="text-muted d-block mt-1" id="rejectProductName"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="uil uil-exclamation-triangle me-2"></i>
                            {{ __('catalogmanagement::review.rejection_warning') ?? 'Please provide a reason for rejecting this review.' }}
                        </div>
                        <div class="form-group">
                            <label for="rejection_reason" class="form-label fw-500">
                                {{ __('catalogmanagement::review.rejection_reason') }} <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-lg nockeditor" id="rejection_reason" name="rejection_reason" rows="5" placeholder="{{ __('catalogmanagement::review.rejection_reason_placeholder') ?? 'Enter the reason for rejection...' }}" required></textarea>
                            <small class="text-muted d-block mt-2">
                                <i class="uil uil-info-circle me-1"></i>
                                {{ __('catalogmanagement::review.rejection_reason_help') ?? 'This reason will be visible to the customer.' }}
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
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const search = urlParams.get('search') || '';
            const status = urlParams.get('status') || '';
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
                    url: '{{ route("admin.reviews.datatable") }}',
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
                    { data: 'product_title', name: 'product_title', orderable: false, searchable: true },
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

            // Function to update URL with filter params
            function updateUrlParams() {
                const params = new URLSearchParams();
                const searchVal = $('#search').val();
                const statusVal = $('#status_filter').val();
                const starVal = $('#rating_filter').val();
                const fromVal = $('#created_date_from').val();
                const toVal = $('#created_date_to').val();

                if (searchVal) params.append('search', searchVal);
                if (statusVal) params.append('status', statusVal);
                if (starVal) params.append('star', starVal);
                if (fromVal) params.append('created_date_from', fromVal);
                if (toVal) params.append('created_date_to', toVal);

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            // Search button
            $('#searchBtn').on('click', function() {
                updateUrlParams();
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#status_filter').val('').trigger('change');
                $('#rating_filter').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                window.history.replaceState({}, '', window.location.pathname);
                table.ajax.reload();
            });

            // View Review Modal
            $(document).on('click', '.btn-view-review', function() {
                const review = $(this).data('review');

                let html = `
                    <div class="mb-3">
                        <label class="form-label fw-500">{{ __('catalogmanagement::review.product') }}</label>
                        <p>${review.product_title}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">{{ __('catalogmanagement::review.customer') }}</label>
                        <p>${review.customer_name || '-'}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">{{ __('catalogmanagement::review.rating') }}</label>
                        <p>
                            ${'⭐'.repeat(review.star)}<span class="ms-2">${review.star}/5</span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">{{ __('catalogmanagement::review.review') }}</label>
                        <p>${review.review}</p>
                    </div>
                `;

                if (review.status === 'rejected' && review.rejection_reason) {
                    html += `
                        <div class="mb-3">
                            <label class="form-label fw-500">{{ __('catalogmanagement::review.rejection_reason') }}</label>
                            <p class="text-danger">${review.rejection_reason}</p>
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
                let url = "{{ route('admin.reviews.reject', ':id') }}".replace(':id', reviewId);
                $('#rejectForm').attr('action', url);
                $('#rejectProductName').text('{{ __("catalogmanagement::review.product") }}: ' + (review.product_title || '-'));
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
                    toastr.warning('{{ __("catalogmanagement::review.rejection_reason_required") ?? "Please enter a rejection reason" }}');
                    return;
                }

                // Disable button and show loading
                submitBtn.prop('disabled', true);
                const originalBtnHtml = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") }}');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        rejection_reason: reason
                    },
                    success: function(response) {
                        const rejectModalInstance = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                        if (rejectModalInstance) {
                            rejectModalInstance.hide();
                        }

                        $('#rejection_reason').val('');
                        table.ajax.reload();

                        // Show success toastr
                        toastr.success('{{ __("catalogmanagement::review.review_rejected") }}', '{{ __("common.success") }}', {
                            timeOut: 3000,
                            progressBar: true
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = '{{ __("common.error_occurred") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg, '{{ __("common.error") }}', {
                            timeOut: 3000,
                            progressBar: true
                        });
                    },
                    complete: function() {
                        // Re-enable button
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

                // Disable button and show loading
                $btn.prop('disabled', true);
                const originalBtnHtml = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") }}');

                let url = "{{ route('admin.reviews.approve', ':id') }}".replace(':id', reviewId);
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table.ajax.reload();

                        // Show success toastr
                        toastr.success('{{ __("catalogmanagement::review.review_approved") }}', '{{ __("common.success") }}', {
                            timeOut: 3000,
                            progressBar: true
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = '{{ __("common.error_occurred") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg, '{{ __("common.error") }}', {
                            timeOut: 3000,
                            progressBar: true
                        });
                    },
                    complete: function() {
                        // Re-enable button
                        $btn.prop('disabled', false);
                        $btn.html(originalBtnHtml);
                    }
                });
            });
        });
    </script>
@endpush
