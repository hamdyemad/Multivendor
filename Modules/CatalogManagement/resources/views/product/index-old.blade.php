@extends('layout.app')
@section('title',
    (isset($statusFilter) && $statusFilter === 'pending') ? trans('menu.products.pending_products') :
    ((isset($statusFilter) && $statusFilter === 'rejected') ? trans('menu.products.rejected_products') :
    ((isset($statusFilter) && $statusFilter === 'approved') ? trans('menu.products.accepted_products') :
    trans('catalogmanagement::product.products_management')))
)

@push('styles')
<style>
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .rotating {
        animation: rotate 1s linear infinite;
        display: inline-block;
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
                    ['title' =>
                        $statusFilter === 'pending' ? trans('menu.products.pending_products') :
                        ($statusFilter === 'rejected' ? trans('menu.products.rejected_products') :
                        ($statusFilter === 'approved' ? trans('menu.products.accepted_products') :
                        trans('catalogmanagement::product.products_management')))
                    ],
                ]" />
            </div>
        </div>

        {{-- Bank Products Card (Admin Only) --}}
        @can('products.bank')
        <div class="row mb-4">
            <div class="col-lg-12">
                <a href="{{ route('admin.products.bank') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm bg-gradient-primary text-white" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--second-primary) 100%);">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="uil uil-database" style="font-size: 2.5rem; opacity: 0.9;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 text-white fw-bold">{{ __('catalogmanagement::product.bank_products') }}</h5>
                                        <small class="text-white-50">{{ __('catalogmanagement::product.bank_products_description') ?? 'Manage shared products available for all vendors' }}</small>
                                    </div>
                                </div>
                                <div>
                                    <i class="uil uil-arrow-right" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endcan

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-box me-2"></i>
                            @if($statusFilter === 'pending')
                                {{ trans('menu.products.pending_products') }}
                            @elseif($statusFilter === 'rejected')
                                {{ trans('menu.products.rejected_products') }}
                            @elseif($statusFilter === 'approved')
                                {{ trans('menu.products.accepted_products') }}
                            @else
                                {{ trans('catalogmanagement::product.products_management') }}
                            @endif
                        </h4>
                        @can('products.create')
                        <div class="d-flex gap-2">
                            <button type="button" id="exportBtn" class="btn btn-info btn-squared shadow-sm px-4">
                                <i class="uil uil-download-alt"></i> {{ trans('common.export_excel') }}
                            </button>
                            <a href="{{ route('admin.products.bulk-upload') }}"
                                class="btn btn-success btn-squared shadow-sm px-4">
                                <i class="uil uil-upload"></i> {{ trans('catalogmanagement::product.bulk_upload') }}
                            </a>
                            <a href="{{ route('admin.products.create') }}"
                                class="btn btn-primary btn-squared shadow-sm px-4">
                                <i class="uil uil-plus"></i> {{ trans('catalogmanagement::product.add_product') }}
                            </a>
                        </div>
                        @endcan
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

                                    @if(isAdmin())
                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="vendor_filter"
                                            id="vendor_filter"
                                            :label="__('catalogmanagement::product.vendor')"
                                            icon="uil uil-store"
                                            :options="$vendors"
                                            :selected="request('vendor_id')"
                                            :placeholder="__('common.all')"
                                        />
                                    </div>
                                    @endif
                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="brand_filter"
                                            id="brand_filter"
                                            :label="__('catalogmanagement::product.brand')"
                                            icon="uil uil-tag-alt"
                                            :options="$brands"
                                            :selected="request('brand_id')"
                                            :placeholder="__('common.all')"
                                        />
                                    </div>

                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="department_filter"
                                            id="department_filter"
                                            :label="__('catalogmanagement::product.department')"
                                            icon="uil uil-tag-alt"
                                            :options="$departments"
                                            :selected="request('department_id')"
                                            :placeholder="__('common.all')"
                                        />
                                    </div>

                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="category_filter"
                                            id="category_filter"
                                            :label="__('catalogmanagement::product.category')"
                                            icon="uil uil-folder"
                                            :options="[]"
                                            :selected="request('category_id')"
                                            :placeholder="__('common.all')"
                                        />
                                    </div>

                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="product_type"
                                            id="product_type"
                                            :label="__('catalogmanagement::product.product_type')"
                                            icon="uil uil-layers"
                                            :options="[
                                                ['id' => 'bank', 'name' => __('catalogmanagement::product.bank')],
                                                ['id' => 'product', 'name' => __('catalogmanagement::product.product')]
                                            ]"
                                            :selected="request('product_type')"
                                            :placeholder="__('common.all')"
                                        />
                                    </div>

                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="configuration_filter"
                                            id="configuration_filter"
                                            :label="__('catalogmanagement::product.configuration') ?? 'Configuration'"
                                            icon="uil uil-package"
                                            :options="[
                                                ['id' => 'simple', 'name' => __('catalogmanagement::product.simple_product') ?? 'Simple Product'],
                                                ['id' => 'variants', 'name' => __('catalogmanagement::product.variant_product') ?? 'Variant Product']
                                            ]"
                                            :selected="request('configuration')"
                                            :placeholder="__('common.all')"
                                        />
                                    </div>

                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="active"
                                            id="active"
                                            :label="__('common.active_status')"
                                            icon="uil uil-check-circle"
                                            :options="[
                                                ['id' => '1', 'name' => __('common.active')],
                                                ['id' => '2', 'name' => __('common.inactive')]
                                            ]"
                                            :selected="request('active')"
                                            :placeholder="__('common.all')"
                                        />
                                    </div>

                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="stock_filter"
                                            id="stock_filter"
                                            :label="__('catalogmanagement::product.stock_status') ?? 'Stock Status'"
                                            icon="uil uil-box"
                                            :options="[
                                                ['id' => 'instock', 'name' => __('dashboard.instock')],
                                                ['id' => 'outofstock', 'name' => __('dashboard.out_of_stock')]
                                            ]"
                                            :selected="request('stock')"
                                            :placeholder="__('common.all')"
                                        />
                                    </div>

                                    @if(!isset($statusFilter))
                                    <div class="col-md-3">
                                        <x-custom-select
                                            name="status"
                                            id="status"
                                            :label="__('catalogmanagement::product.approval_status')"
                                            icon="uil uil-file-check"
                                            :options="collect(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses())->map(fn($label, $value) => ['id' => $value, 'name' => $label])->values()->toArray()"
                                            :selected="request('status')"
                                            :placeholder="__('common.all')"
                                        />
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

                                    <div class="col-md-12 d-flex align-items-center gap-2">
                                        {{-- Per Page Selector --}}
                                        <div class="d-flex align-items-center">
                                            <label class="me-1 mb-0 text-muted" style="font-size: 13px;">{{ __('common.show') }}:</label>
                                            <div style="width: 70px;">
                                                <x-custom-select
                                                    name="per_page_filter"
                                                    id="per_page_filter"
                                                    class="form-select-sm"
                                                    :options="[
                                                        ['id' => '10', 'name' => '10'],
                                                        ['id' => '25', 'name' => '25'],
                                                        ['id' => '50', 'name' => '50'],
                                                        ['id' => '100', 'name' => '100']
                                                    ]"
                                                    :selected="'10'"
                                                    :placeholder="''"
                                                />
                                            </div>
                                            <span class="ms-1 mb-0 text-muted" style="font-size: 13px;">{{ __('common.entries') }}</span>
                                        </div>
                                        
                                        <div class="d-flex gap-1">
                                            <button type="button" id="searchBtn"
                                                class="btn btn-success btn-default btn-squared"
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
                    </div>

                    {{-- DataTable --}}
                    <div class="table-responsive">
                        <table id="productsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center" style="width: 40px;">
                                        <input type="checkbox" id="selectAllProducts" class="form-check-input" style="cursor: pointer;">
                                    </th>
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.product_information') }}</span></th>
                                    @if(auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::product.vendor') }}</span></th>
                                    @endif
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::product.approval_status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.activation') }}</span></th>
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

    {{-- Delete Modal --}}
    <x-delete-with-loading modalId="modal-delete-product" tableId="productsDataTable" deleteButtonClass="delete-product"
        :title="trans('main.confirm delete')" :message="trans('main.are you sure you want to delete this')" itemNameId="delete-product-name" confirmBtnId="confirmDeleteProductBtn"
        :cancelText="trans('main.cancel')" :deleteText="trans('main.delete')" :loadingDeleting="trans('main.deleting')" :loadingPleaseWait="trans('main.please wait')" :loadingDeletedSuccessfully="trans('main.deleted success')" :loadingRefreshing="trans('main.refreshing')"
        :errorDeleting="trans('main.error on delete')" />

    {{-- Change Status Modal --}}
    <div class="modal fade" id="modal-change-status" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-info" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusModalLabel">{{ __('catalogmanagement::product.change_product_status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-info-body d-flex mb-3">
                        <div class="modal-info-icon primary">
                            <img src="{{ asset('assets/img/svg/info.svg') }}" alt="info" class="svg">
                        </div>
                        <div class="modal-info-text">
                            <p class="fw-500" id="status-product-name"></p>
                            <p class="text-muted fs-13">{{ __('catalogmanagement::product.select_new_status_for_product') }}</p>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="product-status" class="form-label il-gray fs-14 fw-500 align-center">
                            {{ __('catalogmanagement::product.approval_status') }} <span class="text-danger">*</span>
                        </label>
                        <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="product-status" required>
                            <option value="">{{ __('common.select_option') }}</option>
                            @foreach(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses() as $statusValue => $statusLabel)
                                <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="rejection-reason-group" style="display: none;">
                        <label for="rejection-reason" class="form-label il-gray fs-14 fw-500 align-center">
                            {{ __('catalogmanagement::product.rejection_reason') }}
                        </label>
                        <textarea class="form-control ih-medium ip-gray radius-xs b-light px-15" id="rejection-reason" rows="3" placeholder="{{ __('catalogmanagement::product.enter_rejection_reason') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                        <i class="uil uil-times"></i> {{ __('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="confirmChangeStatusBtn">
                        <i class="uil uil-check"></i> {{ __('common.confirm') }}
                    </button>
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

            // Initialize all Select2 dropdowns (check if select2 is available)
            if ($.fn.select2) {
                $('.select2').select2({
                    allowClear: true,
                    width: '100%'
                });
            }

            // Initialize all custom selects
            const customSelectIds = [
                'vendor_filter', 'brand_filter', 'department_filter', 'category_filter',
                'product_type', 'configuration_filter', 'active', 'stock_filter', 'status', 'per_page_filter'
            ];
            
            customSelectIds.forEach(function(id) {
                if (document.getElementById(id) && typeof CustomSelect !== 'undefined') {
                    CustomSelect.init(id);
                }
            });

            // Flag to prevent double reload during initialization
            let isInitializing = true;

            // Department change handler - fetch categories
            document.getElementById('department_filter').addEventListener('change', function(e) {
                const departmentId = e.detail ? e.detail.value : (typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('department_filter') : '');
                const currentCategoryId = "{{ request('category_id') }}";
                
                if (!departmentId) {
                    // Clear categories and set empty options
                    if (typeof CustomSelect !== 'undefined') {
                        CustomSelect.setOptions('category_filter', [], '{{ __('common.all') }}');
                    }
                    if (!isInitializing) {
                        table.ajax.reload();
                    }
                    return;
                }
                
                // Fetch categories for selected department
                $.ajax({
                    url: '/api/v1/categories',
                    type: 'GET',
                    data: {
                        department_id: departmentId,
                        select2: 1
                    },
                    headers: {
                        'lang': '{{ app()->getLocale() }}',
                        'X-Country-Code': $("meta[name='currency_country_code']").attr('content')
                    },
                    success: function(response) {
                        const data = response.data || response;
                        if (data && data.length > 0) {
                            // Format options for CustomSelect
                            const options = data.map(function(category) {
                                return { id: category.id, name: category.name };
                            });
                            if (typeof CustomSelect !== 'undefined') {
                                CustomSelect.setOptions('category_filter', options, '{{ __('common.all') }}');
                                
                                // If there's a current category selected, set it
                                if (currentCategoryId) {
                                    CustomSelect.setValue('category_filter', currentCategoryId);
                                }
                            }
                        } else {
                            if (typeof CustomSelect !== 'undefined') {
                                CustomSelect.setOptions('category_filter', [], '{{ __('common.all') }}');
                            }
                        }
                        if (!isInitializing) {
                            table.ajax.reload();
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching categories:', error);
                        if (typeof CustomSelect !== 'undefined') {
                            CustomSelect.setOptions('category_filter', [], '{{ __('common.all') }}');
                        }
                        if (!isInitializing) {
                            table.ajax.reload();
                        }
                    }
                });
            });

            // Add change handlers for all other custom selects (except department which has special handling)
            ['vendor_filter', 'brand_filter', 'category_filter', 'product_type', 'configuration_filter', 'active', 'stock_filter', 'status'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', function(e) {
                        if (!isInitializing) {
                            table.ajax.reload();
                        }
                    });
                }
            });

            // Per page filter change handler
            const perPageEl = document.getElementById('per_page_filter');
            if (perPageEl) {
                perPageEl.addEventListener('change', function(e) {
                    const perPage = e.detail ? e.detail.value : (typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('per_page_filter') : 10);
                    table.page.len(parseInt(perPage)).draw();
                });
            }

            // Trigger department change on page load if a department is selected
            const initialDepartmentId = typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('department_filter') : '';
            if (initialDepartmentId) {
                // Trigger change event to load categories
                document.getElementById('department_filter').dispatchEvent(new CustomEvent('change', { detail: { value: initialDepartmentId } }));
            }
            
            // Set initializing to false after a short delay to allow initial load
            setTimeout(function() {
                isInitializing = false;
            }, 500);

            // Populate other filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('vendor_id')) $('#vendor_filter').val(urlParams.get('vendor_id'));
            if (urlParams.has('brand_id')) $('#brand_filter').val(urlParams.get('brand_id'));
            if (urlParams.has('product_type')) $('#product_type').val(urlParams.get('product_type'));
            if (urlParams.has('configuration') && $('#configuration_filter').length) {
                $('#configuration_filter').val(urlParams.get('configuration'));
            }
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('status')) $('#status').val(urlParams.get('status'));
            if (urlParams.has('stock')) $('#stock_filter').val(urlParams.get('stock'));
            if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
            if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

            let table = $('#productsDataTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '{{ route('admin.products.datatable') }}',
                    data: function(d) {
                        d.search = $('#search').val();
                        d.department_id = typeof CustomSelect !== 'undefined' && document.getElementById('department_filter') ? CustomSelect.getValue('department_filter') : '';
                        d.vendor_id = typeof CustomSelect !== 'undefined' && document.getElementById('vendor_filter') ? CustomSelect.getValue('vendor_filter') : '';
                        d.brand_id = typeof CustomSelect !== 'undefined' && document.getElementById('brand_filter') ? CustomSelect.getValue('brand_filter') : '';
                        d.category_id = typeof CustomSelect !== 'undefined' && document.getElementById('category_filter') ? CustomSelect.getValue('category_filter') : '';
                        d.product_type = typeof CustomSelect !== 'undefined' && document.getElementById('product_type') ? CustomSelect.getValue('product_type') : '';
                        d.configuration = typeof CustomSelect !== 'undefined' && document.getElementById('configuration_filter') ? CustomSelect.getValue('configuration_filter') : '';
                        d.active = typeof CustomSelect !== 'undefined' && document.getElementById('active') ? CustomSelect.getValue('active') : '';
                        d.status = typeof CustomSelect !== 'undefined' && document.getElementById('status') ? CustomSelect.getValue('status') : '';
                        d.stock = typeof CustomSelect !== 'undefined' && document.getElementById('stock_filter') ? CustomSelect.getValue('stock_filter') : '';
                        @if(isset($statusFilter) && $statusFilter)
                        d.status = '{{ $statusFilter }}';
                        @endif
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.per_page = typeof CustomSelect !== 'undefined' && document.getElementById('per_page_filter') ? CustomSelect.getValue('per_page_filter') : 10;
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '40px',
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="form-check-input product-checkbox" data-product-id="${row.vendor_product_id}" style="cursor: pointer;">`;
                        }
                    },
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

                            // Product Names
                            if (data.name_en && data.name_en !== '-') {
                                html += `<div class="product-name-item mb-2">
                                    <span class="language-badge badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">EN</span>
                                    <span class="product-name text-dark fw-semibold">${$('<div/>').text(data.name_en).html()}</span>
                                </div>`;
                            }

                            if (data.name_ar && data.name_ar !== '-') {
                                html += `<div class="product-name-item mb-2">
                                    <span class="language-badge badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">AR</span>
                                    <span class="product-name text-dark fw-semibold" dir="rtl" style="font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">${$('<div/>').text(data.name_ar).html()}</span>
                                </div>`;
                            }

                            // Product Type
                            const productType = row.product_type === 'bank' ? '{{ __("catalogmanagement::product.bank_product") }}' : '{{ __("catalogmanagement::product.regular_product") }}';
                            const typeClass = row.product_type === 'bank' ? 'bg-info' : 'bg-secondary';
                            html += `<div class="mb-2">
                                <span class="badge ${typeClass} text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 10px;">
                                    <i class="uil ${row.product_type === 'bank' ? 'uil-database' : 'uil-box'} me-1"></i>
                                    ${productType}
                                </span>
                            </div>`;

                            // Configuration Type (Simple or Variant)
                            const configurationType = row.configuration_type || 'simple';
                            const configClass = configurationType === 'variants' ? 'bg-warning' : 'bg-success';
                            const configLabel = configurationType === 'variants' ? '{{ __("catalogmanagement::product.variant_product") }}' : '{{ __("catalogmanagement::product.simple_product") }}';
                            const configIcon = configurationType === 'variants' ? 'uil-layers' : 'uil-package';
                            html += `<div class="mb-2">
                                <span class="badge badge-round badge-lg ${configClass} text-white px-2 py-1 rounded-pill fw-bold" style="font-size: 10px;">
                                    <i class="uil ${configIcon} me-1"></i>
                                    ${configLabel}
                                </span>
                            </div>`;

                            // Brand and Category
                            html += '<div class="product-meta-info">';
                            if (row.department && row.department.name) {
                                html += `<div class="mb-1">
                                    <small class="text-muted">{{ __('catalogmanagement::product.department') }}:</small>
                                    <span class="badge badge-secondary badge-round badge-lg ms-1">${$('<div/>').text(row.department.name).html()}</span>
                                </div>`;
                            }
                            if (row.category && row.category.name) {
                                html += `<div class="mb-1">
                                    <small class="text-muted">{{ __('catalogmanagement::product.category') }}:</small>
                                    <span class="badge badge-secondary badge-round badge-lg ms-1">${$('<div/>').text(row.category.name).html()}</span>
                                </div>`;
                            }
                            if (row.brand && row.brand.name) {
                                html += `<div class="mb-1">
                                    <small class="text-muted">{{ __('catalogmanagement::product.brand') }}:</small>
                                    <span class="badge badge-secondary badge-round badge-lg ms-1">${$('<div/>').text(row.brand.name).html()}</span>
                                </div>`;
                            }
                            // SKU
                            if (row.sku && row.sku !== '-') {
                                html += `<div class="mb-1">
                                    <small class="text-muted">{{ __('catalogmanagement::product.sku') }}:</small>
                                    <code class="ms-1">${$('<div/>').text(row.sku).html()}</code>
                                </div>`;
                            }
                            // Stock Information (Total Stock & Remaining Stock)
                            const totalStock = row.total_stock || 0;
                            const remainingStock = row.remaining_stock || 0;
                            const stockBadgeClass = remainingStock > 0 ? 'badge-success' : 'badge-danger';
                            html += `<div class="mb-1">
                                <small class="text-muted">{{ __('catalogmanagement::product.total_stock') }}:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${totalStock.toLocaleString()}</span>
                            </div>`;
                            html += `<div class="mb-1">
                                <small class="text-muted">{{ __('catalogmanagement::product.remaining_stock') }}:</small>
                                <span class="badge ${stockBadgeClass} badge-round badge-lg ms-1">${remainingStock > 0 ? remainingStock.toLocaleString() : '{{ __('dashboard.out_of_stock') }}'}</span>
                            </div>`;
                            // Created At
                            if (row.created_at) {
                                html += `<div class="mb-1">
                                    <small class="text-muted"><i class="uil uil-calendar-alt"></i> {{ __('common.created_at') }}:</small>
                                    <span class="text-dark ms-1">${row.created_at}</span>
                                </div>`;
                            }
                            html += '</div>';

                            html += '</div>';
                            return html;
                        },
                        className: 'text-start'
                    },
                    @if(isAdmin())
                    {
                        data: 'vendor',
                        name: 'vendor',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            if (!data || !data.name) {
                                return '<span class="text-muted">—</span>';
                            }
                            return `<span class="badge badge-primary badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                        }
                    },
                    @endif
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (!data) {
                                return `<span class="badge badge-secondary badge-round badge-lg"><i class="uil uil-minus"></i> {{ __('common.none') }}</span>`;
                            }
                            if (data === 'approved') {
                                return `<span class="badge badge-success badge-round badge-lg"><i class="uil uil-check-circle"></i> {{ __('common.approved') }}</span>`;
                            } else if (data === 'rejected') {
                                return `<span class="badge badge-danger badge-round badge-lg"><i class="uil uil-times-circle"></i> {{ __('common.rejected') }}</span>`;
                            } else if (data === 'pending') {
                                return `<span class="badge badge-warning badge-round badge-lg"><i class="uil uil-clock"></i> {{ __('common.pending') }}</span>`;
                            }
                            return `<span class="badge badge-secondary badge-round badge-lg">${data}</span>`;
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            // For sorting, return numeric value
                            if (type === 'sort' || type === 'type') {
                                return data ? 1 : 0;
                            }

                            // For display, return formatted HTML with switcher
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'activation-switch-' + row.id;
                            const productName = row.product_information?.name_en || row.product_information?.name_ar || 'Product #' + row.id;
                            const isDisabled = @can('products.change-activation') '' @else 'disabled' @endcan;

                            return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input activation-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-product-id="${row.vendor_product_id}"
                                           data-product-name="${$('<div>').text(productName).html()}"
                                           ${isChecked}
                                           ${isDisabled}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            const showUrl = "{{ route('admin.products.show', ':id') }}".replace(':id', data.vendor_product_id);
                            const editUrl = "{{ route('admin.products.edit', ':id') }}".replace(':id', data.vendor_product_id);
                            const destroyUrl = "{{ route('admin.products.destroy', ':id') }}".replace(':id', data.vendor_product_id);
                            const stockPricingUrl = "{{ route('admin.products.stock-management', ':id') }}".replace(':id', data.vendor_product_id);

                            let actions = `
                            <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                @can('products.show')
                                <a href="${showUrl}" class="view btn btn-primary table_action_father" title="{{ trans('common.view') }}">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                @endcan`;

                            @can('products.edit')
                            actions += `
                                <a href="${editUrl}" class="edit btn btn-warning table_action_father" title="{{ trans('common.edit') }}">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>`;
                            @endcan
                            @can('products.stock-management')
                                actions += `<a href="${stockPricingUrl}" class="stock-management btn btn-info table_action_father" title="{{ trans('catalogmanagement::product.stock_management') }}">
                                        <i class="uil uil-box table_action_icon"></i>
                                    </a>`;
                            @endcan

                            // Add approve/reject button for admin users
                            @if(isAdmin())
                                @can('products.change-status')
                                    actions += `
                                        <a href="javascript:void(0);" class="change-status btn btn-success table_action_father"
                                        data-bs-toggle="modal" data-bs-target="#modal-change-status"
                                        data-item-id="${data.vendor_product_id}"
                                        data-item-status="${data.status || ''}"
                                        data-item-name="${data.product_information?.name_en || 'Product'}"
                                        data-item-type="${data.product_type || ''}"
                                        title="{{ trans('catalogmanagement::product.change_status') }}">
                                            <i class="uil uil-check-circle table_action_icon"></i>
                                        </a>`;
                                @endcan

                                // Move to bank button - only show for regular products (not already bank products)
                                @can('products.edit')
                                if (data.product_type !== 'bank') {
                                    actions += `
                                        <a href="javascript:void(0);" class="move-to-bank btn btn-secondary table_action_father"
                                        data-item-id="${data.vendor_product_id}"
                                        data-item-name="${data.product_information?.name_en || 'Product'}"
                                        title="{{ trans('catalogmanagement::product.move_to_bank') }}">
                                            <i class="uil uil-database table_action_icon"></i>
                                        </a>`;
                                }
                                @endcan
                            @endif

                            @can('products.delete')
                            actions += `
                                <a href="javascript:void(0);" class="remove delete-product btn btn-danger table_action_father"
                                   data-bs-toggle="modal" data-bs-target="#modal-delete-product"
                                   data-item-id="${data.vendor_product_id}"
                                   data-item-name="${data.product_information?.name_en || data.product_information?.name_ar || 'Product'}"
                                   data-url="${destroyUrl}"
                                   title="{{ trans('common.delete') }}">
                                    <i class="uil uil-trash-alt table_action_icon"></i>
                                </a>`;
                            @endcan
                            
                            actions += `</div>`;

                            return actions;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [1, 'desc'] // Index column (no default sorting, just show as is)
                ],
                language: {
                    lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                    info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                    infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                    infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                    zeroRecords: "{{ __('catalogmanagement::product.no_products_found') ?? 'No products found' }}",
                    emptyTable: "{{ __('catalogmanagement::product.no_products_found') ?? 'No products found' }}",
                    loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                    processing: "{{ __('common.processing') ?? 'Processing' }}...",
                    search: "{{ __('common.search') ?? 'Search' }}:",
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
                    },
                    aria: {
                        sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                        sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                    }
                },
                dom: '<"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="uil uil-file-download-alt"></i>',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '{{ trans('catalogmanagement::product.products_management') }}'
                }]
            });
            

            // Search button click
            $('#searchBtn').on('click', function() {
                // Update URL with filter parameters
                const params = new URLSearchParams();
                const search = $('#search').val();
                const vendor = typeof CustomSelect !== 'undefined' && document.getElementById('vendor_filter') ? CustomSelect.getValue('vendor_filter') : '';
                const brand = typeof CustomSelect !== 'undefined' && document.getElementById('brand_filter') ? CustomSelect.getValue('brand_filter') : '';
                const department = typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('department_filter') : '';
                const category = typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('category_filter') : '';
                const productType = typeof CustomSelect !== 'undefined' && document.getElementById('product_type') ? CustomSelect.getValue('product_type') : '';
                const configuration = typeof CustomSelect !== 'undefined' && document.getElementById('configuration_filter') ? CustomSelect.getValue('configuration_filter') : '';
                const active = typeof CustomSelect !== 'undefined' && document.getElementById('active') ? CustomSelect.getValue('active') : '';
                const status = typeof CustomSelect !== 'undefined' && document.getElementById('status') ? CustomSelect.getValue('status') : '';
                const stock = typeof CustomSelect !== 'undefined' && document.getElementById('stock_filter') ? CustomSelect.getValue('stock_filter') : '';
                const dateFrom = $('#created_date_from').val();
                const dateTo = $('#created_date_to').val();

                if (search) params.set('search', search);
                if (department) params.set('department_id', department);
                if (vendor) params.set('vendor_id', vendor);
                if (brand) params.set('brand_id', brand);
                if (category) params.set('category_id', category);
                if (productType) params.set('product_type', productType);
                if (configuration) params.set('configuration', configuration);
                if (active) params.set('active', active);
                if (status) params.set('status', status);
                if (stock) params.set('stock', stock);
                if (dateFrom) params.set('created_date_from', dateFrom);
                if (dateTo) params.set('created_date_to', dateTo);

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                window.history.pushState({}, '', newUrl);

                table.ajax.reload();
            });

            // Live search for product names only (EN/AR)
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => table.ajax.reload(), 600);
            });

            // Date filters change handler
            $('#created_date_from, #created_date_to').on('change', () => table.ajax.reload());

            // Export
            $('#exportExcel').on('click', () => table.button('.buttons-excel').trigger());

            // Reset
            $('#resetFilters').on('click', function() {
                // Clear URL parameters and reload page
                window.history.pushState({}, '', window.location.pathname);

                // Clear regular inputs
                $('#search, #created_date_from, #created_date_to').val('');

                // Clear all Custom Select dropdowns
                const customSelectIds = [
                    'vendor_filter', 'brand_filter', 'department_filter', 'category_filter',
                    'product_type', 'configuration_filter', 'active', 'stock_filter', 'status'
                ];
                
                customSelectIds.forEach(function(id) {
                    if (document.getElementById(id) && typeof CustomSelect !== 'undefined') {
                        CustomSelect.clear(id);
                    }
                });

                // Reset per page to default (10)
                if (document.getElementById('per_page_filter') && typeof CustomSelect !== 'undefined') {
                    CustomSelect.setValue('per_page_filter', '10');
                }

                // Reload table
                table.ajax.reload();
            });

            // RTL Support in DataTables
            if ($('html').attr('dir') === 'rtl') {
                $('.dataTables_wrapper').addClass('text-end');
            }

            // Change Status Modal Handler
            let currentProductId = null;

            let currentProductType = ''; // Store product type for later use

            $(document).on('click', '.change-status', function() {
                currentProductId = $(this).data('item-id');
                const productName = $(this).data('item-name');
                const currentStatus = $(this).data('item-status');
                currentProductType = $(this).data('item-type');

                $('#status-product-name').text(productName);
                $('#product-status').val(currentStatus);
                $('#rejection-reason').val('');

                // Show/hide rejection reason based on current status
                if (currentStatus === 'rejected') {
                    $('#rejection-reason-group').show();
                } else {
                    $('#rejection-reason-group').hide();
                }

                // Hide bank product group if product type is 'bank'
                if (currentProductType === 'bank') {
                    $('#bank-product-group').hide();
                } else {
                    $('#bank-product-group').show();
                }
            });



            // Show/hide rejection reason and bank product fields based on selected status
            $('#product-status').on('change', function() {
                const selectedStatus = $(this).val();

                if (selectedStatus === 'rejected') {
                    $('#rejection-reason-group').slideDown();
                } else if (selectedStatus === 'approved' && currentProductType !== 'bank') {
                    $('#rejection-reason-group').slideUp();

                    // Clear rejection reason - check if CKEditor is initialized
                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['rejection-reason']) {
                        CKEDITOR.instances['rejection-reason'].setData('');
                    } else {
                        $('#rejection-reason').val('');
                    }
                } else {
                    $('#rejection-reason-group').slideUp();

                    // Clear rejection reason - check if CKEditor is initialized
                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['rejection-reason']) {
                        CKEDITOR.instances['rejection-reason'].setData('');
                    } else {
                        $('#rejection-reason').val('');
                    }
                }
            });


            // Confirm Status Change
            $('#confirmChangeStatusBtn').on('click', function() {
                const newStatus = $('#product-status').val();

                // Get rejection reason - check if CKEditor is initialized
                let rejectionReason = '';
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['rejection-reason']) {
                    // Get value from CKEditor
                    rejectionReason = CKEDITOR.instances['rejection-reason'].getData();
                    // Strip HTML tags for validation
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = rejectionReason;
                    rejectionReason = tempDiv.textContent || tempDiv.innerText || '';
                } else {
                    // Get value from regular textarea
                    rejectionReason = $('#rejection-reason').val();
                }

                console.log('Status:', newStatus);
                console.log('Rejection Reason:', rejectionReason);

                if (!newStatus) {
                    toastr.error('{{ __("catalogmanagement::product.please_select_status") }}');
                    return;
                }

                if (newStatus === 'rejected' && !rejectionReason.trim()) {
                    toastr.error('{{ __("catalogmanagement::product.rejection_reason_required") }}');
                    return;
                }

                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="uil uil-spinner-alt spin-animation me-1"></i>{{ __("common.updating") }}...');

                const requestData = {
                    _token: '{{ csrf_token() }}',
                    status: newStatus,
                    rejection_reason: rejectionReason
                };

                $.ajax({
                    url: '{{ route("admin.products.change-status", ":id") }}'.replace(':id', currentProductId),
                    method: 'POST',
                    data: requestData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || '{{ __("catalogmanagement::product.status_updated_successfully") }}');
                            $('#modal-change-status').modal('hide');
                            // Reload the table data and redraw
                            table.ajax.reload(function() {
                                console.log('Table reloaded after status change');
                            }, false);
                        } else {
                            toastr.error(response.message || '{{ __("common.error_occurred") }}');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        toastr.error(xhr.responseJSON?.message || '{{ __("common.error_occurred") }}');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset modal when closed
            $('#modal-change-status').on('hidden.bs.modal', function() {
                $('#product-status').val('');

                // Clear rejection reason - check if CKEditor is initialized
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['rejection-reason']) {
                    CKEDITOR.instances['rejection-reason'].setData('');
                } else {
                    $('#rejection-reason').val('');
                }

                $('#rejection-reason-group').hide();
                currentProductId = null;
            });

            // Activation switcher handler
            $(document).on('change', '.activation-switcher', function() {
                const switcher = $(this);
                const productId = switcher.data('product-id');
                const productName = switcher.data('product-name');
                const newStatus = switcher.is(':checked') ? 1 : 2; // 1=active, 2=inactive

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '{{ __('catalogmanagement::product.change_activation') }}',
                        subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                    });
                }

                // Make AJAX request
                $.ajax({
                    url: '{{ route('admin.products.change-activation', ':id') }}'.replace(':id', productId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            // Show success message
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

                            // Reload table to reflect changes
                            table.ajax.reload(null, false);
                        } else {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            // Revert switcher state
                            switcher.prop('checked', !switcher.is(':checked'));

                            // Show error message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') ?? 'Error' }}',
                                    text: response.message
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Hide loading overlay
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        // Revert switcher state
                        switcher.prop('checked', !switcher.is(':checked'));

                        let errorMessage = '{{ __('catalogmanagement::product.error_changing_activation') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Show error message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('common.error') ?? 'Error' }}',
                                text: errorMessage
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    },
                    complete: function() {
                        // Re-enable switcher
                        switcher.prop('disabled', false);
                    }
                });
            });

            // Move to bank handler
            @if(isAdmin())
            $(document).on('click', '.move-to-bank', function() {
                const btn = $(this);
                const productId = btn.data('item-id');
                const productName = btn.data('item-name');

                // Show confirmation popup
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '<i class="uil uil-database text-primary"></i> {{ __('catalogmanagement::product.move_to_bank') }}',
                        html: `<div class="text-center py-3">
                                   <div class="mb-3">
                                       <span class="badge bg-primary badge-lg badge-round px-3 py-2 fs-6">${productName}</span>
                                   </div>
                                   <p class="mb-2">{{ __('catalogmanagement::product.confirm_move_to_bank') }}</p>
                                   <p class="text-muted small mb-0">{{ __('catalogmanagement::product.move_to_bank_description') }}</p>
                               </div>`,
                        icon: null,
                        showCancelButton: true,
                        confirmButtonColor: '#5F63F2',
                        cancelButtonColor: '#868e96',
                        confirmButtonText: '<i class="uil uil-check me-1"></i> {{ __('common.confirm') ?? 'Confirm' }}',
                        cancelButtonText: '<i class="uil uil-times me-1"></i> {{ __('common.cancel') ?? 'Cancel' }}',
                        customClass: {
                            popup: 'swal2-lg',
                            title: 'fs-5 fw-bold',
                            confirmButton: 'btn btn-success px-4 me-1',
                            cancelButton: 'btn btn-secondary px-4 me-1'
                        },
                        buttonsStyling: false,
                        showCloseButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.show({
                                    text: '{{ __('catalogmanagement::product.move_to_bank') }}',
                                    subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                                });
                            }

                            // Make AJAX request
                            $.ajax({
                                url: '{{ route('admin.products.move-to-bank', ':id') }}'.replace(':id', productId),
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    // Hide loading overlay
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }

                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: '{{ __('common.success') ?? 'Success' }}',
                                            text: response.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        });

                                        // Reload table to reflect changes
                                        table.ajax.reload(null, false);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: '{{ __('common.error') ?? 'Error' }}',
                                            text: response.message
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    // Hide loading overlay
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }

                                    let errorMessage = '{{ __('catalogmanagement::product.error_moving_to_bank') }}';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }

                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('common.error') ?? 'Error' }}',
                                        text: errorMessage
                                    });
                                }
                            });
                        }
                    });
                } else {
                    // Fallback without SweetAlert
                    if (confirm('{{ __('catalogmanagement::product.confirm_move_to_bank') }}')) {
                        $.ajax({
                            url: '{{ route('admin.products.move-to-bank', ':id') }}'.replace(':id', productId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message);
                                    table.ajax.reload(null, false);
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function(xhr) {
                                alert('{{ __('catalogmanagement::product.error_moving_to_bank') }}');
                            }
                        });
                    }
                }
            });
            @endif

            // Export button handler
            $('#exportBtn').on('click', function() {
                const btn = $(this);
                const originalHtml = btn.html();
                
                // Get selected product IDs
                const selectedIds = [];
                $('.product-checkbox:checked').each(function() {
                    selectedIds.push($(this).data('product-id'));
                });
                
                // Check if any products are selected
                if (selectedIds.length === 0) {
                    // Show info toast
                    toastr.info('{{ __('catalogmanagement::product.please_select_products_to_export') }}', '{{ __('common.info') }}');
                    return;
                }
                
                // Disable button and show loading
                btn.prop('disabled', true);
                btn.html('<i class="uil uil-spinner-alt rotating"></i> {{ __('common.processing') }}');
                
                // Get current filter values - check if elements exist first
                const filters = {
                    search: $('#search').val() || '',
                    vendor_id: (typeof CustomSelect !== 'undefined' && document.getElementById('vendor_filter')) ? CustomSelect.getValue('vendor_filter') : '',
                    department_id: (typeof CustomSelect !== 'undefined' && document.getElementById('department_filter')) ? CustomSelect.getValue('department_filter') : '',
                    category_id: (typeof CustomSelect !== 'undefined' && document.getElementById('category_filter')) ? CustomSelect.getValue('category_filter') : '',
                    brand_id: (typeof CustomSelect !== 'undefined' && document.getElementById('brand_filter')) ? CustomSelect.getValue('brand_filter') : '',
                    status: (typeof CustomSelect !== 'undefined' && document.getElementById('status')) ? CustomSelect.getValue('status') : '',
                    product_type: (typeof CustomSelect !== 'undefined' && document.getElementById('product_type')) ? CustomSelect.getValue('product_type') : '',
                    configuration_type: (typeof CustomSelect !== 'undefined' && document.getElementById('configuration_filter')) ? CustomSelect.getValue('configuration_filter') : '',
                    is_active: (typeof CustomSelect !== 'undefined' && document.getElementById('active_status')) ? CustomSelect.getValue('active_status') : '',
                    stock_status: (typeof CustomSelect !== 'undefined' && document.getElementById('stock_status')) ? CustomSelect.getValue('stock_status') : '',
                    created_from: $('#created_date_from').val() || '',
                    created_to: $('#created_date_to').val() || '',
                    product_ids: selectedIds.join(',') // Add selected product IDs
                };
                
                // Build query string
                const queryParams = new URLSearchParams();
                Object.keys(filters).forEach(key => {
                    if (filters[key]) {
                        queryParams.append(key, filters[key]);
                    }
                });
                
                // Create export URL
                const exportUrl = '{{ route('admin.products.export') }}' + (queryParams.toString() ? '?' + queryParams.toString() : '');
                
                // Trigger download
                window.location.href = exportUrl;
                
                // Re-enable button after a delay
                setTimeout(function() {
                    btn.prop('disabled', false);
                    btn.html(originalHtml);
                }, 2000);
            });
            
            // Select all checkbox handler
            $('#selectAllProducts').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.product-checkbox').prop('checked', isChecked);
                updateExportButtonState();
            });
            
            // Individual checkbox handler
            $(document).on('change', '.product-checkbox', function() {
                // Update select all checkbox state
                const totalCheckboxes = $('.product-checkbox').length;
                const checkedCheckboxes = $('.product-checkbox:checked').length;
                $('#selectAllProducts').prop('checked', totalCheckboxes === checkedCheckboxes);
                updateExportButtonState();
            });
            
            // Update export button state based on selection
            function updateExportButtonState() {
                const selectedCount = $('.product-checkbox:checked').length;
                const btn = $('#exportBtn');
                
                if (selectedCount > 0) {
                    btn.html(`<i class="uil uil-download-alt"></i> {{ __('common.export_excel') }} (${selectedCount})`);
                } else {
                    btn.html('<i class="uil uil-download-alt"></i> {{ __('common.export_excel') }}');
                }
            }
        });
    </script>
@endpush
