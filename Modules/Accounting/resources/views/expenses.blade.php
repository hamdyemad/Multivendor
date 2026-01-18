@extends('layout.app')

@section('title', __('accounting.expense_records'))

@push('styles')
@endpush

@section('content')
<div class="crm mb-25">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="uil uil-estate"></i>{{ __('accounting.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.accounting.summary') }}">{{ __('accounting.accounting') }}</a></li>
                                <li class="breadcrumb-item active">{{ __('accounting.expense_records') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('accounting.expense_records') }}</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-default btn-squared text-capitalize" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                <i class="uil uil-plus"></i> {{ __('accounting.add_expense') }}
                            </button>
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
                                                <i class="uil uil-search me-1"></i> {{ __('accounting.search') }}
                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search" placeholder="{{ __('accounting.search_expenses') }}..." autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Category --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="expense-item-filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-tag me-1"></i> {{ __('accounting.category') }}
                                            </label>
                                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="expense-item-filter">
                                                <option value="">{{ __('accounting.all_categories') }}</option>
                                                @foreach($expenseItems as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Date From --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date-from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> {{ __('accounting.date_from') }}
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date-from">
                                        </div>
                                    </div>

                                    {{-- Date To --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date-to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> {{ __('accounting.date_to') }}
                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date-to">
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex align-items-center gap-2">
                                        <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared me-1">
                                            <i class="uil uil-search me-1"></i> {{ __('accounting.search') }}
                                        </button>
                                        <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared">
                                            <i class="uil uil-redo me-1"></i> {{ __('accounting.reset') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Entries Per Page --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0">{{ __('accounting.show') }}</label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <label class="ms-2 mb-0">{{ __('accounting.entries') }}</label>
                        </div>
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="expensesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.category') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.amount') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.description') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.expense_date') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.receipt') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.created') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Add Expense Modal --}}
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseModalLabel">{{ __('accounting.add_expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('accounting.close') }}"></button>
            </div>
            <form method="POST" id="addExpenseForm" action="{{ route('admin.accounting.expenses.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert-container"></div>
                    <div class="mb-3">
                        <label for="expense_item_id" class="form-label">{{ __('accounting.category') }}</label>
                        <select class="form-control" id="expense_item_id" name="expense_item_id">
                            <option value="">{{ __('accounting.select_category') }}</option>
                            @foreach($expenseItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">{{ __('accounting.amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('accounting.description') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="expense_date" class="form-label">{{ __('accounting.expense_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="receipt_file" class="form-label">{{ __('accounting.receipt_file') }}</label>
                        <input type="file" class="form-control" id="receipt_file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                    <button type="submit" id="addExpenseBtn" class="btn btn-primary">
                        <i class="uil uil-check"></i>
                        <span>{{ __('accounting.create_expense') }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Expense Modal --}}
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseModalLabel">{{ __('accounting.edit_expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('accounting.close') }}"></button>
            </div>
            <form method="POST" id="editExpenseForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert-container"></div>
                    <div class="mb-3">
                        <label for="edit_expense_item_id" class="form-label">{{ __('accounting.category') }}</label>
                        <select class="form-control" id="edit_expense_item_id" name="expense_item_id">
                            <option value="">{{ __('accounting.select_category') }}</option>
                            @foreach($expenseItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">{{ __('accounting.amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">{{ __('accounting.description') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_expense_date" class="form-label">{{ __('accounting.expense_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_expense_date" name="expense_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_receipt_file" class="form-label">{{ __('accounting.receipt_file') }}</label>
                        <input type="file" class="form-control" id="edit_receipt_file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                    <button type="submit" id="editExpenseBtn" class="btn btn-primary">
                        <i class="uil uil-check"></i>
                        <span>{{ __('accounting.update_expense') }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal with Loading Component --}}
<x-delete-with-loading modalId="modal-delete-expense" tableId="expensesDataTable"
    deleteButtonClass="delete-expense" :title="__('accounting.confirm_delete')" :message="__('accounting.are_you_sure_delete_expense')" itemNameId="delete-expense-name"
    confirmBtnId="confirmDeleteExpenseBtn" :cancelText="__('accounting.cancel')" :deleteText="__('accounting.delete')" :loadingDeleting="__('accounting.deleting')" :loadingPleaseWait="__('accounting.please_wait')"
    :loadingDeletedSuccessfully="__('accounting.expense_deleted_successfully')" :loadingRefreshing="__('accounting.refreshing')" :errorDeleting="__('accounting.error_deleting_expense')" />

@endsection

@push('after-body')
    <x-loading-overlay />
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            let table = $('#expensesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.accounting.expenses.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.expense_item_id = $('#expense-item-filter').val();
                        d.date_from = $('#date-from').val();
                        d.date_to = $('#date-to').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'category', name: 'category' },
                    { data: 'amount', name: 'amount' },
                    { data: 'description', name: 'description' },
                    { data: 'expense_date', name: 'expense_date' },
                    { data: 'receipt', name: 'receipt', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[3, 'desc']],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                     '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function(settings) {
                    bindEditEvents();
                }
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#expense-item-filter, #date-from, #date-to').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#expense-item-filter').val('');
                $('#date-from').val('');
                $('#date-to').val('');
                table.ajax.reload();
            });

            // AJAX form handling for add expense
            $('#addExpenseForm').on('submit', function(e) {
                e.preventDefault();
                handleFormSubmission(this, '#addExpenseBtn', '#addExpenseModal');
            });

            // AJAX form handling for edit expense
            $('#editExpenseForm').on('submit', function(e) {
                e.preventDefault();
                handleFormSubmission(this, '#editExpenseBtn', '#editExpenseModal');
            });

            function handleFormSubmission(form, btnSelector, modalSelector) {
                const submitBtn = $(btnSelector);
                const modal = $(modalSelector);
                
                // Disable submit button and show loading
                submitBtn.prop('disabled', true);
                const btnIcon = submitBtn.find('i');
                const btnText = submitBtn.find('span:not(.spinner-border)');
                btnIcon.addClass('d-none');
                btnText.addClass('d-none');
                submitBtn.find('.spinner-border').removeClass('d-none');

                // Clear previous validation errors
                $(form).find('.is-invalid').removeClass('is-invalid');
                $(form).find('.invalid-feedback').remove();

                // Show loading overlay
                LoadingOverlay.show();

                // Start progress bar animation
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                    const formData = new FormData(form);
                    
                    return fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                })
                .then(response => {
                    LoadingOverlay.animateProgressBar(60, 200);
                    
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
                })
                .then(data => {
                    return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                        LoadingOverlay.showSuccess(
                            data.message,
                            '{{ __('accounting.refreshing') }}'
                        );

                        setTimeout(() => {
                            LoadingOverlay.hide();
                            modal.modal('hide');
                            table.ajax.reload();
                            form.reset();
                        }, 1500);
                    });
                })
                .catch(error => {
                    LoadingOverlay.hide();
                    
                    // Clear previous alerts
                    $(form).find('.alert-container').empty();
                    
                    // Handle validation errors
                    if (error.errors) {
                        Object.keys(error.errors).forEach(key => {
                            const input = $(form).find(`[name="${key}"]`);
                            if (input.length) {
                                input.addClass('is-invalid');
                                const feedback = $('<div class="invalid-feedback d-block"></div>').text(error.errors[key][0]);
                                input.parent().append(feedback);
                            }
                        });
                    } else {
                        // Show error alert at top of form
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <i class="uil uil-exclamation-triangle me-2"></i>${error.message || '{{ __('accounting.error_occurred') }}'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $(form).find('.alert-container').html(alertHtml);
                    }
                    
                    // Re-enable submit button
                    submitBtn.prop('disabled', false);
                    btnIcon.removeClass('d-none');
                    btnText.removeClass('d-none');
                    submitBtn.find('.spinner-border').addClass('d-none');
                });
            }

            function bindEditEvents() {
                $('.edit').off('click').on('click', function() {
                    const id = $(this).data('id');
                    const expenseItemId = $(this).data('expense-item-id');
                    const amount = $(this).data('amount');
                    const description = $(this).data('description');
                    const expenseDate = $(this).data('expense-date');

                    $('#edit_expense_item_id').val(expenseItemId);
                    $('#edit_amount').val(amount);
                    $('#edit_description').val(description);
                    $('#edit_expense_date').val(expenseDate);

                    const form = $('#editExpenseForm');
                    const updateUrl = "{{ route('admin.accounting.expenses.update', ':id') }}".replace(':id', id);
                    form.attr('action', updateUrl);
                });
            }
        });
    </script>
@endpush
