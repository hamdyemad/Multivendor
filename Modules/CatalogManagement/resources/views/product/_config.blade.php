{{-- Product DataTable Configuration --}}
@php
    // Determine if user is admin
    $isAdmin = isAdmin();
    
    // Build table headers
    $tableHeaders = [
        ['label' => '<input type="checkbox" id="selectAllProducts" class="form-check-input" style="cursor: pointer;">', 'class' => 'text-center', 'style' => 'width: 40px;', 'raw' => true],
        ['label' => '#', 'class' => 'text-center'],
        ['label' => trans('catalogmanagement::product.product_information')],
    ];
    
    if ($isAdmin) {
        $tableHeaders[] = ['label' => trans('catalogmanagement::product.vendor')];
    }
    
    $tableHeaders[] = ['label' => trans('catalogmanagement::product.approval_status'), 'class' => 'text-center'];
    $tableHeaders[] = ['label' => trans('common.activation'), 'class' => 'text-center'];
    $tableHeaders[] = ['label' => trans('common.actions'), 'class' => 'text-center'];

    // Custom select IDs for filters
    $customSelectIds = [
        'per_page_filter',
        'brand_filter',
        'department_filter',
        'category_filter',
        'product_type',
        'configuration_filter',
        'active',
        'stock_filter'
    ];
    
    if ($isAdmin) {
        $customSelectIds[] = 'vendor_filter';
    }
    
    if (!isset($statusFilter)) {
        $customSelectIds[] = 'status';
    }

    // DataTable configuration
    $datatableConfig = [
        'tableId' => 'productsDataTable',
        'ajaxUrl' => route('admin.products.datatable'),
        'headers' => $tableHeaders,
        'customSelectIds' => $customSelectIds,
        'order' => [[1, 'desc']],
        'pageLength' => 10,
        'columnsJson' => 'COLUMNS_DEFINED_IN_SCRIPT',
        'additionalAjaxData' => isset($statusFilter) && $statusFilter ? "d.status = '{$statusFilter}';" : null,
    ];

    // Page title based on status filter
    $pageTitle = match($statusFilter ?? null) {
        'pending' => trans('menu.products.pending_products'),
        'rejected' => trans('menu.products.rejected_products'),
        'approved' => trans('menu.products.accepted_products'),
        default => trans('catalogmanagement::product.products_management'),
    };

    // Breadcrumb items
    $breadcrumbItems = [
        [
            'title' => trans('dashboard.title'),
            'url' => route('admin.dashboard'),
            'icon' => 'uil uil-estate',
        ],
        ['title' => $pageTitle],
    ];

    // Filter options
    $filterOptions = [
        'perPage' => [
            ['id' => '10', 'name' => '10'],
            ['id' => '25', 'name' => '25'],
            ['id' => '50', 'name' => '50'],
            ['id' => '100', 'name' => '100']
        ],
        'productType' => [
            ['id' => 'bank', 'name' => __('catalogmanagement::product.bank')],
            ['id' => 'product', 'name' => __('catalogmanagement::product.product')]
        ],
        'configuration' => [
            ['id' => 'simple', 'name' => __('catalogmanagement::product.simple_product') ?? 'Simple Product'],
            ['id' => 'variants', 'name' => __('catalogmanagement::product.variant_product') ?? 'Variant Product']
        ],
        'activeStatus' => [
            ['id' => '1', 'name' => __('common.active')],
            ['id' => '2', 'name' => __('common.inactive')]
        ],
        'stockStatus' => [
            ['id' => 'instock', 'name' => __('dashboard.instock')],
            ['id' => 'outofstock', 'name' => __('dashboard.out_of_stock')]
        ],
        'approvalStatus' => collect(\Modules\CatalogManagement\app\Models\VendorProduct::getStatuses())
            ->map(fn($label, $value) => ['id' => $value, 'name' => $label])
            ->values()
            ->toArray()
    ];
@endphp
