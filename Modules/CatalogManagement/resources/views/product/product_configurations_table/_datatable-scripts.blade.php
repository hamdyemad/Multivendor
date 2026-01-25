{{-- Product DataTable Custom Scripts --}}
<script>
// Custom render functions for product datatable columns

// Render product information column
window.renderProductInformation = function(data, type, row) {
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
    
    @if(isAdmin())
    // Sort Number (Admin only)
    const sortNumber = row.sort_number || 0;
    html += `<div class="mb-1">
        <small class="text-muted"><i class="uil uil-sort-amount-up"></i> {{ __('common.sort_number') ?? 'Sort Number' }}:</small>
        <span class="badge badge-info badge-round badge-lg ms-1">${sortNumber}</span>
    </div>`;
    @endif
    
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
};

// Render vendor column
window.renderVendor = function(data, type, row) {
    if (!data || !data.name) {
        return '<span class="text-muted">—</span>';
    }
    return `<span class="badge badge-primary badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
};

// Render status column
window.renderStatus = function(data, type, row) {
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
};

// Render activation column
window.renderActivation = function(data, type, row) {
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
};

// Render actions column
window.renderActions = function(data, type, row) {
    const showUrl = "{{ route('admin.products.show', ':id') }}".replace(':id', row.vendor_product_id);
    const editUrl = "{{ route('admin.products.edit', ':id') }}".replace(':id', row.vendor_product_id);
    const destroyUrl = "{{ route('admin.products.destroy', ':id') }}".replace(':id', row.vendor_product_id);
    const stockPricingUrl = "{{ route('admin.products.stock-management', ':id') }}".replace(':id', row.vendor_product_id);

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
                data-item-id="${row.vendor_product_id}"
                data-item-status="${row.status || ''}"
                data-item-name="${row.product_information?.name_en || 'Product'}"
                data-item-type="${row.product_type || ''}"
                title="{{ trans('catalogmanagement::product.change_status') }}">
                    <i class="uil uil-check-circle table_action_icon"></i>
                </a>`;
        @endcan

        // Move to bank button - only show for regular products (not already bank products)
        @can('products.edit')
        if (row.product_type !== 'bank') {
            actions += `
                <a href="javascript:void(0);" class="move-to-bank btn btn-secondary table_action_father"
                data-item-id="${row.vendor_product_id}"
                data-item-name="${row.product_information?.name_en || 'Product'}"
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
           data-item-id="${row.vendor_product_id}"
           data-item-name="${row.product_information?.name_en || row.product_information?.name_ar || 'Product'}"
           data-url="${destroyUrl}"
           title="{{ trans('common.delete') }}">
            <i class="uil uil-trash-alt table_action_icon"></i>
        </a>`;
    @endcan
    
    actions += `</div>`;

    return actions;
};
</script>
