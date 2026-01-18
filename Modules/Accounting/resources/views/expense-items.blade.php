@extends('layout.app')

@section('title', __('accounting.expense_categories'))

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
                                <li class="breadcrumb-item active">{{ __('accounting.expense_items') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold">{{ __('accounting.expense_categories') }}</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-default btn-squared text-capitalize" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="uil uil-plus"></i> {{ __('accounting.add_category') }}
                            </button>
                        </div>
                    </div>

                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    {{-- Search --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('accounting.search') }}
                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search" placeholder="{{ __('accounting.search_categories') }}..." autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status-filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i> {{ __('accounting.status') }}
                                            </label>
                                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="status-filter">
                                                <option value="">{{ __('accounting.all_status') }}</option>
                                                <option value="1">{{ __('accounting.active') }}</option>
                                                <option value="0">{{ __('accounting.inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 d-flex align-items-center gap-2">
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
                        <table id="expenseItemsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.name') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('accounting.status') }}</span></th>
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

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">{{ __('accounting.add_expense_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('accounting.close') }}"></button>
            </div>
            <form method="POST" action="{{ route('admin.accounting.expense-items.store') }}" id="addCategoryForm">
                @csrf
                <div class="modal-body">
                    {{-- Validation Errors Alert --}}
                    <div class="alert alert-danger d-none" id="addCategoryErrors">
                        <ul class="mb-0" id="addCategoryErrorsList"></ul>
                    </div>
                    
                    <x-multilingual-input 
                        name="name" 
                        :label="__('accounting.category_name')"
                        :placeholder="__('accounting.enter_category_name')"
                        :labelAr="__('accounting.category_name')"
                        :placeholderAr="__('accounting.enter_category_name_ar')"
                        :languages="$languages"
                        :required="true"
                        cols="6"
                    />
                    <div class="mb-3">
                        <label for="active" class="form-label">{{ __('accounting.status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                            <label class="form-check-label" for="active">{{ __('accounting.active') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="addCategorySubmitBtn">
                        <span class="btn-text">{{ __('accounting.create_category') }}</span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            {{ __('accounting.please_wait') }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">{{ __('accounting.edit_expense_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Validation Errors Alert --}}
                    <div class="alert alert-danger d-none" id="editCategoryErrors">
                        <ul class="mb-0" id="editCategoryErrorsList"></ul>
                    </div>
                    
                    <div class="row">
                        @foreach ($languages as $language)
                            <div class="col-md-6 mb-25 @if (app()->getLocale() == 'ar') {{ $language->code == 'ar' ? 'order-1' : 'order-2' }} @else {{ $language->code == 'en' ? 'order-1' : 'order-2' }} @endif">
                                <div class="form-group">
                                    <label for="edit_translation_{{ $language->id }}_name" class="il-gray fs-14 fw-500 mb-10 d-block"
                                        @if ($language->code == 'ar') dir="rtl" @else dir="ltr" @endif>
                                        {{ __('accounting.category_name') }} ({{ $language->name }}) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                        id="edit_translation_{{ $language->id }}_name"
                                        name="translations[{{ $language->id }}][name]"
                                        data-lang="{{ $language->code }}"
                                        @if ($language->code == 'ar') dir="rtl" @else dir="ltr" @endif
                                        required>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <label for="edit_active" class="form-label">{{ __('accounting.status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_active" name="active" value="1">
                            <label class="form-check-label" for="edit_active">{{ __('accounting.active') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="editCategorySubmitBtn">
                        <span class="btn-text">{{ __('accounting.update_category') }}</span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            {{ __('accounting.please_wait') }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal with Loading Component --}}
<x-delete-with-loading modalId="modal-delete-expense-item" tableId="expenseItemsDataTable"
    deleteButtonClass="delete-expense-item" :title="__('accounting.confirm_delete')" :message="__('accounting.are_you_sure_delete')" itemNameId="delete-expense-item-name"
    confirmBtnId="confirmDeleteExpenseItemBtn" :cancelText="__('accounting.cancel')" :deleteText="__('accounting.delete')" :loadingDeleting="__('accounting.deleting')" :loadingPleaseWait="__('accounting.please_wait')"
    :loadingDeletedSuccessfully="__('accounting.category_deleted_successfully')" :loadingRefreshing="__('accounting.refreshing')" :errorDeleting="__('accounting.error_deleting_category')" />

<style>
    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: linear-gradient(90deg, #5f63f2 0%, #06d6a0 100%);
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.6px;
        color: white;
    }

    .table tbody td {
        padding: 0.9rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
        font-size: 0.9rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
        background-color: #fff;
    }

    .table tbody tr:hover {
        background-color: #f8f9ff !important;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.05);
    }

    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .table tbody tr:nth-child(even):hover {
        background-color: #f8f9ff !important;
    }

    .dataTables_wrapper {
        padding: 0;
    }

    .dataTables_info {
        padding: 1rem 25px;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .dataTables_paginate {
        padding: 1rem 25px;
        text-align: right;
    }

    .dataTables_paginate .paginate_button {
        display: inline-block;
        padding: 0.5rem 0.8rem;
        margin: 0 0.25rem;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #495057;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1;
    }

    .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background-color: #5f63f2;
        border-color: #5f63f2;
        color: white;
    }

    .dataTables_paginate .paginate_button.active {
        background-color: #5f63f2 !important;
        border-color: #5f63f2 !important;
        color: white !important;
    }

    .dataTables_paginate .paginate_button.disabled,
    .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f8f9fa;
        color: #6c757d;
        border-color: #dee2e6;
    }
</style>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let per_page = 10;

            let table = $('#expenseItemsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.accounting.expense-items.datatable') }}',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#status-filter').val();
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
                    { data: 'name', name: 'name' },
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data) {
                            return data ?
                                '<span class="badge badge-lg badge-round bg-success">{{ __('accounting.active') }}</span>' :
                                '<span class="badge badge-lg badge-round bg-danger">{{ __('accounting.inactive') }}</span>';
                        }
                    },
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

            $('#status-filter').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#status-filter').val('');
                table.ajax.reload();
            });

            function bindEditEvents() {
                $('.edit').off('click').on('click', function() {
                    const id = $(this).data('id');
                    const translations = $(this).data('translations');
                    const active = $(this).data('active') === 1;

                    // Clear previous errors
                    $('#editCategoryErrors').addClass('d-none');
                    $('#editCategoryErrorsList').empty();
                    $('#editCategoryForm').find('.is-invalid').removeClass('is-invalid');

                    // Set translations for each language
                    if (translations) {
                        Object.keys(translations).forEach(function(langId) {
                            const translation = translations[langId];
                            if (translation && translation.name) {
                                $(`#edit_translation_${langId}_name`).val(translation.name);
                            }
                        });
                    }

                    $('#edit_active').prop('checked', active);

                    const form = $('#editCategoryForm');
                    const updateUrl = "{{ route('admin.accounting.expense-items.update', ':id') }}".replace(':id', id);
                    form.attr('action', updateUrl);
                });
            }

            // Add Category Form AJAX Submit
            $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitBtn = $('#addCategorySubmitBtn');
                const errorsContainer = $('#addCategoryErrors');
                const errorsList = $('#addCategoryErrorsList');
                
                // Clear previous errors
                errorsContainer.addClass('d-none');
                errorsList.empty();
                form.find('.is-invalid').removeClass('is-invalid');
                
                // Show loading
                submitBtn.prop('disabled', true);
                submitBtn.find('.btn-text').addClass('d-none');
                submitBtn.find('.btn-loading').removeClass('d-none');
                
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close modal and reload table
                            $('#addCategoryModal').modal('hide');
                            form[0].reset();
                            table.ajax.reload();
                            
                            // Show success toast if available
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || '{{ __("accounting.record_created") }}');
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                errors[field].forEach(function(message) {
                                    errorsList.append('<li>' + message + '</li>');
                                });
                                
                                // Add is-invalid class to the field
                                const fieldName = field.replace(/\./g, '_');
                                form.find(`[name="${field}"], [name*="${field}"]`).addClass('is-invalid');
                            });
                            errorsContainer.removeClass('d-none');
                        } else {
                            errorsList.append('<li>{{ __("accounting.error_creating") }}</li>');
                            errorsContainer.removeClass('d-none');
                        }
                    },
                    complete: function() {
                        // Hide loading
                        submitBtn.prop('disabled', false);
                        submitBtn.find('.btn-text').removeClass('d-none');
                        submitBtn.find('.btn-loading').addClass('d-none');
                    }
                });
            });

            // Edit Category Form AJAX Submit
            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const submitBtn = $('#editCategorySubmitBtn');
                const errorsContainer = $('#editCategoryErrors');
                const errorsList = $('#editCategoryErrorsList');
                
                // Clear previous errors
                errorsContainer.addClass('d-none');
                errorsList.empty();
                form.find('.is-invalid').removeClass('is-invalid');
                
                // Show loading
                submitBtn.prop('disabled', true);
                submitBtn.find('.btn-text').addClass('d-none');
                submitBtn.find('.btn-loading').removeClass('d-none');
                
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close modal and reload table
                            $('#editCategoryModal').modal('hide');
                            table.ajax.reload();
                            
                            // Show success toast if available
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || '{{ __("accounting.record_updated") }}');
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                errors[field].forEach(function(message) {
                                    errorsList.append('<li>' + message + '</li>');
                                });
                                
                                // Add is-invalid class to the field
                                form.find(`[name="${field}"], [name*="${field}"]`).addClass('is-invalid');
                            });
                            errorsContainer.removeClass('d-none');
                        } else {
                            errorsList.append('<li>{{ __("accounting.error_updating") }}</li>');
                            errorsContainer.removeClass('d-none');
                        }
                    },
                    complete: function() {
                        // Hide loading
                        submitBtn.prop('disabled', false);
                        submitBtn.find('.btn-text').removeClass('d-none');
                        submitBtn.find('.btn-loading').addClass('d-none');
                    }
                });
            });

            // Clear errors when modal is closed
            $('#addCategoryModal').on('hidden.bs.modal', function() {
                $('#addCategoryErrors').addClass('d-none');
                $('#addCategoryErrorsList').empty();
                $('#addCategoryForm').find('.is-invalid').removeClass('is-invalid');
                $('#addCategoryForm')[0].reset();
            });

            $('#editCategoryModal').on('hidden.bs.modal', function() {
                $('#editCategoryErrors').addClass('d-none');
                $('#editCategoryErrorsList').empty();
                $('#editCategoryForm').find('.is-invalid').removeClass('is-invalid');
            });
        });
    </script>
@endpush
