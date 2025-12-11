@extends('layout.app')
@section('title', trans('systemsetting::points.transaction_history'))

@push('styles')
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.earned {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stat-card.redeemed {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card.expired {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .stat-card-icon {
            font-size: 32px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-card-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-card-label {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 500;
        }

        .user-info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .user-info-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info-label {
            font-weight: 600;
            color: #333;
        }

        .user-info-value {
            color: #666;
            font-size: 14px;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-input {
            flex: 1;
            min-width: 200px;
        }

        .filter-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-btn-search {
            background-color: var(--color-primary);
            color: white;
        }

        .filter-btn-search:hover {
            opacity: 0.9;
        }

        .filter-btn-reset {
            background-color: #e0e0e0;
            color: #333;
        }

        .filter-btn-reset:hover {
            background-color: #d0d0d0;
        }

        .type-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .type-earned {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .type-redeemed {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .type-expired {
            background-color: #ffebee;
            color: #c62828;
        }

        .type-adjusted {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .points-positive {
            color: #388e3c;
            font-weight: 600;
        }

        .points-negative {
            color: #c62828;
            font-weight: 600;
        }

        .status-active {
            color: #388e3c;
        }

        .status-expired {
            color: #c62828;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('systemsetting::points.user_points_management'),
                        'url' => route('admin.user-points.index'),
                    ],
                    [
                        'title' => trans('systemsetting::points.transaction_history'),
                    ],
                ]" />
            </div>
        </div>

        <!-- User Information Section -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="user-info-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <span class="user-info-label fw-bold">{{ trans('customer::customer.customer_name') }}:</span>
                                <span class="user-info-value fw-bold" id="customerName">{{ $customer->full_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="user-info-item">
                                <span class="user-info-label fw-bold">{{ trans('customer::customer.email') }}:</span>
                                <span class="user-info-value fw-bold" id="customerEmail">{{ $customer->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-card-icon">
                        <i class="uil uil-wallet"></i>
                    </div>
                    <div class="stat-card-value" id="availablePoints">{{ number_format($available_points ?? 0, 2) }}</div>
                    <div class="stat-card-label">{{ trans('systemsetting::points.available_points') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card earned">
                    <div class="stat-card-icon">
                        <i class="uil uil-arrow-up"></i>
                    </div>
                    <div class="stat-card-value" id="totalEarned">{{ number_format($earned_points ?? 0, 2) }}</div>
                    <div class="stat-card-label">{{ trans('systemsetting::points.earned_points') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card adjusted">
                    <div class="stat-card-icon">
                        <i class="uil uil-edit"></i>
                    </div>
                    <div class="stat-card-value" id="totalAdjusted">{{ number_format($adjusted_points ?? 0, 2) }}</div>
                    <div class="stat-card-label">{{ trans('systemsetting::points.adjusted_points') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card redeemed">
                    <div class="stat-card-icon">
                        <i class="uil uil-arrow-down"></i>
                    </div>
                    <div class="stat-card-value" id="totalRedeemed">{{ number_format($redeemed_points ?? 0, 2) }}</div>
                    <div class="stat-card-label">{{ trans('systemsetting::points.redeemed_points') }}</div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ trans('systemsetting::points.transaction_history') }}</h4>
                        <div class="d-flex">
                            <a href="{{ route('admin.user-points.index') }}" class="btn btn-primary btn-default btn-squared me-1">
                                <i class="uil uil-arrow-left me-1"></i>
                                {{ __('common.back') }}
                            </a>
                            <button type="button" class="btn btn-primary btn-default" data-bs-toggle="modal" data-bs-target="#adjustPointsModal">
                                <i class="uil uil-edit me-1"></i>
                                {{ trans('systemsetting::points.adjust_points') }}
                            </button>
                        </div>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    {{-- Type Filter --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="typeFilter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-filter me-1"></i> {{ trans('systemsetting::points.type') }}
                                            </label>
                                            <select id="typeFilter" class="form-select form-control ih-medium ip-gray radius-xs b-light px-15">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="earned">{{ trans('systemsetting::points.type_earned') }}</option>
                                                <option value="redeemed">{{ trans('systemsetting::points.type_redeemed') }}</option>
                                                <option value="expired">{{ trans('systemsetting::points.type_expired') }}</option>
                                                <option value="adjusted">{{ trans('systemsetting::points.type_adjusted') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Created From --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="createdFrom" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> {{ __('common.date_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="createdFrom"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Created To --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="createdTo" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> {{ __('common.date_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="createdTo"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-center gap-2">
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

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="transactionsTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ trans('systemsetting::points.date') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('systemsetting::points.type') }}</span></th>
                                    <th class="text-center"><span class="userDatatable-title">{{ trans('systemsetting::points.points') }}</span></th>
                                    <th><span class="userDatatable-title">{{ trans('systemsetting::points.description') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Adjust Points Modal -->
    <div class="modal fade" id="adjustPointsModal" tabindex="-1" aria-labelledby="adjustPointsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="adjustPointsModalLabel">
                        <i class="uil uil-edit me-2"></i>{{ trans('systemsetting::points.adjust_points') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Validation Errors Alert -->
                    <div id="validationErrorsAlert" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                        <strong class="d-block"><i class="uil uil-exclamation-triangle me-2"></i>{{ trans('common.validation_error') }}</strong>
                        <ul id="errorsList" class="mb-0 mt-2"></ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <form id="adjustPointsForm">
                        @csrf
                        <input type="hidden" id="adjustUserId" name="user_id">

                        <!-- Points Input -->
                        <div class="mb-3">
                            <label for="adjustPoints" class="form-label fw-bold">
                                <i class="uil uil-coins me-1"></i>{{ trans('systemsetting::points.points') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <select id="pointsSign" class="form-select border-0" style="width: auto;">
                                        <option value="positive">+</option>
                                        <option value="negative">-</option>
                                    </select>
                                </span>
                                <input type="number" class="form-control" id="adjustPoints" name="points"
                                    placeholder="0.00" step="0.01" min="0" required>
                            </div>
                            <small class="text-muted d-block mt-2">
                                {{ trans('systemsetting::points.adjust_points_help') }}
                            </small>
                            <small class="text-danger d-block mt-1" id="pointsError" style="display: none;"></small>
                        </div>

                        <x-multilingual-input
                            name="description"
                            :label="trans('systemsetting::points.description')"
                            :labelAr="'الوصف'"
                            :placeholder="trans('systemsetting::points.description')"
                            :placeholderAr="'أدخل الوصف'"
                            type="text"
                            rows="3"
                            :languages="$languages"
                            :model="null"
                            cols="12"
                        />
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="uil uil-times me-1"></i>{{ trans('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="submitAdjustPoints">
                        <i class="uil uil-check me-1"></i>{{ trans('common.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const userId = '{{ request()->route("userId") }}';
            let per_page = 10;

            // Initialize DataTable
            let transactionsTable = $('#transactionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.user-points.transactions.datatable", [":userId"]) }}'
                        .replace(':userId', userId),
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.type = $('#typeFilter').val();
                        d.created_from = $('#createdFrom').val();
                        d.created_to = $('#createdTo').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '50px',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'type_label',
                        name: 'type_label',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let badgeClass = 'type-adjusted';
                            if (row.type === 'earned') badgeClass = 'type-earned';
                            else if (row.type === 'redeemed') badgeClass = 'type-redeemed';
                            else if (row.type === 'expired') badgeClass = 'type-expired';

                            return `<span class="type-badge ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'points',
                        name: 'points',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let className = 'points-positive';
                            if (row.points < 0 && row.type == 'adjusted' || row.type == 'redeemed') className = 'points-negative';
                            return `<span class="${className}">${data}</span>`;
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[1, 'desc']],
                pagingType: 'full_numbers',
                language: {
                    search: '',
                    searchPlaceholder: "{{ __('common.search') }}...",
                    lengthMenu: '_MENU_',
                    info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.entries') }}",
                    infoEmpty: "{{ __('common.showing') }} 0 {{ __('common.to') }} 0 {{ __('common.of') }} 0 {{ __('common.entries') }}",
                    infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.total_entries') }})",
                    zeroRecords: "{{ __('common.no_matching_records_found') }}",
                    emptyTable: "{{ __('common.no_data_available') }}",
                    paginate: {
                        @if(app()->getLocale() == 'en')
                            first: '<i class="uil uil-angle-double-left"></i>',
                            last: '<i class="uil uil-angle-double-right"></i>',
                            next: '<i class="uil uil-angle-right"></i>',
                            previous: '<i class="uil uil-angle-left"></i>'
                        @else
                            first: '<i class="uil uil-angle-double-right"></i>',
                            last: '<i class="uil uil-angle-double-left"></i>',
                            next: '<i class="uil uil-angle-left"></i>',
                            previous: '<i class="uil uil-angle-right"></i>'
                        @endif
                    }
                },
                dom: '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-bordered');
                }
            });

            // Handle entries per page change
            $('#entriesSelect').on('change', function() {
                transactionsTable.page.len($(this).val()).draw();
            });

            // Type filter change
            $('#typeFilter').on('change', function() {
                transactionsTable.draw();
            });

            // Created From filter change
            $('#createdFrom').on('change', function() {
                transactionsTable.draw();
            });

            // Created To filter change
            $('#createdTo').on('change', function() {
                transactionsTable.draw();
            });

            // Reset filters button click
            $('#resetFilters').on('click', function() {
                $('#typeFilter').val('');
                $('#createdFrom').val('');
                $('#createdTo').val('');
                transactionsTable.draw();
            });

            // Set user ID when modal is shown
            $('#adjustPointsModal').on('show.bs.modal', function() {
                document.getElementById('adjustUserId').value = userId;
            });

            // Clear validation errors
            function clearValidationErrors() {
                document.getElementById('validationErrorsAlert').style.display = 'none';
                document.getElementById('errorsList').innerHTML = '';
                document.getElementById('adjustPoints').classList.remove('is-invalid');
            }

            // Display validation errors
            function displayValidationErrors(errors) {
                const errorsList = document.getElementById('errorsList');
                const alertDiv = document.getElementById('validationErrorsAlert');
                errorsList.innerHTML = '';

                let hasErrors = false;

                if (errors.points) {
                    const li = document.createElement('li');
                    li.textContent = errors.points[0];
                    errorsList.appendChild(li);
                    document.getElementById('adjustPoints').classList.add('is-invalid');
                    hasErrors = true;
                }

                if (errors.description_en) {
                    const li = document.createElement('li');
                    li.textContent = 'Description (English): ' + errors.description_en[0];
                    errorsList.appendChild(li);
                    hasErrors = true;
                }

                if (errors.description_ar) {
                    const li = document.createElement('li');
                    li.textContent = 'Description (Arabic): ' + errors.description_ar[0];
                    errorsList.appendChild(li);
                    hasErrors = true;
                }

                if (hasErrors) {
                    alertDiv.style.display = 'block';
                }
            }

            // Handle adjust points form submission
            $('#submitAdjustPoints').on('click', function() {
                const form = document.getElementById('adjustPointsForm');

                // Clear previous errors
                clearValidationErrors();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const points = parseFloat(document.getElementById('adjustPoints').value);
                const sign = document.getElementById('pointsSign').value;
                const finalPoints = sign === 'negative' ? -points : points;

                console.log('Points Debug:', {
                    'points_input': points,
                    'sign_selected': sign,
                    'final_points': finalPoints,
                    'is_negative': sign === 'negative'
                });

                // Get description values from multilingual input component
                // The component generates fields like: translations[{language_id}][description]
                let descriptionEn = '';
                let descriptionAr = '';

                // Find all input/textarea fields with name pattern translations[*][description]
                const inputs = document.querySelectorAll('input[name*="description"], textarea[name*="description"]');
                console.log('Found inputs:', inputs.length);

                inputs.forEach(input => {
                    const lang = input.getAttribute('data-lang');
                    const value = input.value.trim();
                    console.log('Input lang:', lang, 'value:', value);

                    if (lang === 'en') {
                        descriptionEn = value;
                    } else if (lang === 'ar') {
                        descriptionAr = value;
                    }
                });

                // Show loading state
                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ trans("common.loading") }}...';

                // Send request to adjust points
                const requestBody = {
                    points: finalPoints,
                    description_en: descriptionEn,
                    description_ar: descriptionAr,
                };

                console.log('Request Body:', requestBody);

                fetch('{{ route("admin.user-points.adjust", ":id") }}'.replace(':id', userId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(requestBody)
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;

                    if (data.success) {
                        toastr.success(data.message || '{{ trans("systemsetting::points.points_adjusted_successfully") }}');

                        // Reset form
                        form.reset();
                        document.getElementById('pointsSign').value = 'positive';
                        clearValidationErrors();

                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('adjustPointsModal')).hide();

                        // Reload page after 1 second
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        // Display validation errors if present
                        displayValidationErrors(data.errors);
                        toastr.error(data.message || '{{ trans("common.error_occurred") }}');
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    console.error('Error:', error);
                    toastr.error('{{ trans("common.error_occurred") }}');
                });
            });
        });
    </script>
@endpush
