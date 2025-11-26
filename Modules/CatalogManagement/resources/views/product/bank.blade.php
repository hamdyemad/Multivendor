@extends('layout.app')
@section('title', trans('catalogmanagement::product.bank_products_management'))

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
                    ['title' => trans('catalogmanagement::product.bank_products_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-database me-2"></i>
                            {{ trans('catalogmanagement::product.bank_products_management') }}
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products.bank.stock-management') }}"
                                class="btn btn-success btn-squared shadow-sm px-4">
                                <i class="uil uil-import"></i> {{ trans('catalogmanagement::product.import_product_from_bank') }}
                            </a>
                            <a href="{{ route('admin.products.index') }}"
                                class="btn btn-secondary btn-squared shadow-sm px-4">
                                <i class="uil uil-arrow-left"></i> {{ trans('common.back') ?? 'Back' }}
                            </a>
                        </div>
                    </div>
                    {{-- Search & Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="{{ __('common.search') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="brand_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-tag-alt me-1"></i>
                                                {{ __('catalogmanagement::product.brand') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="brand_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="category_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-folder me-1"></i>
                                                {{ __('catalogmanagement::product.category') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="category_filter">
                                                <option value="">{{ __('common.all') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                {{ __('common.active_status') }}
                                            </label>
                                            <select
                                                class="select2 form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value="">{{ __('common.all') }}</option>
                                                <option value="1">{{ __('common.active') }}</option>
                                                <option value="2">{{ __('common.inactive') }}</option>
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

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="bankProductsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.product_information') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.brand') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.category') }}</span></th>
                                    @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                                    <th><span class="userDatatable-title">{{ __('common.activation') }}</span></th>
                                    @endif
                                    <th><span class="userDatatable-title">{{ __('common.created_at') }}</span></th>
                                    @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                                    <th><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
                                    @endif
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
            const translations = {
                active: '{{ __('common.active') }}',
                inactive: '{{ __('common.inactive') }}'
            };

            // Check if user is admin
            const isAdmin = {{ in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()) ? 'true' : 'false' }};

            let table = $('#bankProductsDataTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '{{ route('admin.products.bank.datatable') }}',
                    data: function(d) {
                        d.search = $('#search').val();
                        d.brand_id = $('#brand_filter').val();
                        d.category_id = $('#category_filter').val();
                        @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                        d.active = $('#active').val();
                        @endif
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.per_page = $('#entriesSelect').val() || 10;
                    }
                },
                columns: (() => {
                    let columns = [
                        {
                            data: 'index',
                            name: 'index',
                            orderable: false,
                            searchable: false,
                            className: 'text-center fw-bold'
                        },
                        {
                            data: 'product_information',
                            name: 'product_information',
                            orderable: false,
                            searchable: true,
                            render: function(data, type, row) {
                                if (!data) return '<span class="text-muted">—</span>';
                                let html = '<div class="product-info-container">';

                                if (data.name_en && data.name_en !== '-') {
                                    html += `<div class="product-name-item mb-2">
                                        <span class="language-badge badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">EN</span>
                                        <span class="product-name text-dark fw-semibold">${$('<div/>').text(data.name_en).html()}</span>
                                    </div>`;
                                }

                                if (data.name_ar && data.name_ar !== '-') {
                                    html += `<div class="product-name-item">
                                        <span class="language-badge badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">AR</span>
                                        <span class="product-name text-dark fw-semibold" dir="rtl">${$('<div/>').text(data.name_ar).html()}</span>
                                    </div>`;
                                }

                                html += '</div>';
                                return html;
                            },
                            className: 'text-start'
                        },
                        {
                            data: 'brand',
                            name: 'brand',
                            searchable: false,
                            orderable: false,
                            render: function(data) {
                                if (!data?.name) return '<span class="text-muted">—</span>';
                                return `<span class="badge badge-info badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                            }
                        },
                        {
                            data: 'category',
                            name: 'category',
                            searchable: false,
                            orderable: false,
                            render: function(data) {
                                if (!data?.name) return '<span class="text-muted">—</span>';
                                return `<span class="badge badge-secondary badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                            }
                        }
                    ];

                    // Add activation column only for admin users
                    if (isAdmin) {
                        columns.push({
                            data: 'active',
                            name: 'active',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                if (type === 'sort' || type === 'type') {
                                    return data ? 1 : 0;
                                }

                                const isChecked = data ? 'checked' : '';
                                const switchId = 'activation-switch-' + row.id;
                                const productName = row.product_information?.name_en || row.product_information?.name_ar || 'Product #' + row.id;

                                return `<div class="userDatatable-content">
                                    <div class="form-switch">
                                        <input class="form-check-input activation-switcher"
                                               type="checkbox"
                                               id="${switchId}"
                                               data-product-id="${row.id}"
                                               data-product-name="${$('<div>').text(productName).html()}"
                                               ${isChecked}
                                               style="cursor: pointer;">
                                        <label class="form-check-label" for="${switchId}"></label>
                                    </div>
                                </div>`;
                            }
                        });
                    }

                    // Add created_at column
                    columns.push({
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                        orderable: false,
                        render: function(data) {
                            return data;
                        }
                    });

                    // Add actions column only for admin users
                    if (isAdmin) {
                        columns.push({
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                const bankViewUrl = "{{ route('admin.products.bank.view', ':id') }}".replace(':id', data.id);
                                const editUrl = "{{ route('admin.products.edit', ':id') }}".replace(':id', data.id);

                                return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    <a href="${bankViewUrl}" class="view btn btn-info table_action_father" title="{{ trans('catalogmanagement::product.view_bank_product') }}">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <a href="${editUrl}" class="edit btn btn-warning table_action_father" title="{{ trans('common.edit') }}">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                </div>`;
                            }
                        });
                    }

                    return columns;
                })(),
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[5, 'desc']],
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('catalogmanagement::product.no_products_found') ?? 'No products found' }}",
                    emptyTable: "{{ __('catalogmanagement::product.no_products_found') ?? 'No products found' }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    paginate: {
                        first: '{{ __('common.first') ?? 'First' }}',
                        last: '{{ __('common.last') ?? 'Last' }}',
                        next: '{{ __('common.next') ?? 'Next' }}',
                        previous: '{{ __('common.previous') ?? 'Previous' }}'
                    }
                },
                dom: '<"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Live search
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => table.ajax.reload(), 600);
            });

            // Filters
            @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
            $('#brand_filter, #category_filter, #active').on('select2:select select2:clear change', function() {
                table.ajax.reload();
            });
            @else
            $('#brand_filter, #category_filter').on('select2:select select2:clear change', function() {
                table.ajax.reload();
            });
            @endif

            // Date filters
            $('#created_date_from, #created_date_to').on('change', function() {
                table.ajax.reload();
            });

            // Reset
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                $('#brand_filter, #category_filter, #active').val(null).trigger('change');
                @else
                $('#brand_filter, #category_filter').val(null).trigger('change');
                @endif
                $('#created_date_from, #created_date_to').val('');
                table.ajax.reload();
            });

            // Activation switcher handler (admin only)
            @if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
            $(document).on('change', '.activation-switcher', function() {
                const switcher = $(this);
                const productId = switcher.data('product-id');
                const productName = switcher.data('product-name');
                const newStatus = switcher.is(':checked') ? 1 : 2;

                switcher.prop('disabled', true);

                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ __('catalogmanagement::product.change_activation') }}',
                        subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                    });
                }

                $.ajax({
                    url: '{{ route('admin.products.change-bank-activation', ':id') }}'.replace(':id', productId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('common.success') ?? 'Success' }}',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            }
                            table.ajax.reload(null, false);
                        } else {
                            switcher.prop('checked', !switcher.is(':checked'));
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') ?? 'Error' }}',
                                    text: response.message
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        switcher.prop('checked', !switcher.is(':checked'));
                        let errorMessage = '{{ __('catalogmanagement::product.error_changing_activation') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('common.error') ?? 'Error' }}',
                                text: errorMessage
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        }
                    },
                    complete: function() {
                        switcher.prop('disabled', false);
                    }
                });
            });
            @endif
        });
    </script>
@endpush
