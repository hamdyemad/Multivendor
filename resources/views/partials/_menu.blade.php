@php
    $user_type_id = auth()->user()->user_type_id;
    $user_type = auth()->user()->user_type->name;
    $vendor = auth()->user()->vendor;
    $currentLocale = LaravelLocalization::getCurrentLocale();
    $currentRoute = Request::route()->getName();
    $currentUrl = Request::url();

    // Helper function to check if menu item is active
    if (!function_exists('isMenuActive')) {
        function isMenuActive($routes, $currentRoute = null, $urlPatterns = [])
        {
            global $currentLocale;

            if ($currentRoute && is_array($routes)) {
                if (in_array($currentRoute, $routes)) {
                    return true;
                }
            } elseif ($currentRoute && is_string($routes)) {
                if ($currentRoute === $routes) {
                    return true;
                }
            }

            // Check URL patterns
            foreach ($urlPatterns as $pattern) {
                if (Request::is($currentLocale . '/' . $pattern)) {
                    return true;
                }
            }

            return false;
        }
    }

    // Helper function to check if parent menu should be open
    if (!function_exists('isParentMenuOpen')) {
        function isParentMenuOpen($childRoutes, $urlPatterns = [])
        {
            global $currentRoute;
            return isMenuActive($childRoutes, $currentRoute, $urlPatterns);
        }
    }

    $new_transactions = 0;
    $accepted_transactions = 0;
    $rejected_transactions = 0;

    if ($vendor) {
        $new_transactions = Modules\Withdraw\app\Models\Withdraw::where('reciever_id', $vendor->id)
            ->where('status', 'new')
            ->count();
        $accepted_transactions = Modules\Withdraw\app\Models\Withdraw::where('reciever_id', $vendor->id)
            ->where('status', 'accepted')
            ->count();
        $rejected_transactions = Modules\Withdraw\app\Models\Withdraw::where('reciever_id', $vendor->id)
            ->where('status', 'rejected')
            ->count();
    } else {
        $new_transactions = Modules\Withdraw\app\Models\Withdraw::where('status', 'new')->count();
        $accepted_transactions = Modules\Withdraw\app\Models\Withdraw::where('status', 'accepted')->count();
        $rejected_transactions = Modules\Withdraw\app\Models\Withdraw::where('status', 'rejected')->count();
    }

    $all_transactions = Modules\Withdraw\app\Models\Withdraw::count();
@endphp
<div class="sidebar__menu-group">
    <ul class="sidebar_nav">
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/dashboard') ? 'active' : '' }}">
                <span class="nav-icon uil uil-create-dashboard"></span>
                <span class="menu-text">{{ trans('menu.dashboard.title') }}</span>
            </a>
        </li>


        <li class="menu-title mt-30">
            <span>{{ trans('menu.sections.withdraw module') }}</span>
        </li>
        <li
            class="has-child {{ isParentMenuOpen(['admin.sendMoney', 'admin.allTransactions', 'admin.allVendorsTransactions', 'admin.sendMoneyRequest', 'admin.transactionsRequests'], ['admin/send-money*', 'admin/transactions*', 'admin/withdraw*', 'admin/vendors-transactions*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.sendMoney', 'admin.allTransactions', 'admin.allVendorsTransactions', 'admin.sendMoneyRequest', 'admin.transactionsRequests'], ['admin/send-money*', 'admin/transactions*', 'admin/withdraw*', 'admin/vendors-transactions*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-sitemap"></span>
                <span class="menu-text">{{ trans('menu.withdraw module.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.sendMoney', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.sendMoney') }}">
                            {{ trans('menu.withdraw module.send money') }}
                        </a>
                    </li>

                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.allVendorsTransactions', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.allVendorsTransactions') }}">
                            {{ trans('dashboard.vendors_transactions_overview') }}
                        </a>
                    </li>

                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.allTransactions', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.allTransactions') }}">
                            {{ trans('menu.withdraw module.all transactions') }}
                            <span class="badge badge-round badge-primary ms-1">{{ $all_transactions }}</span>
                        </a>
                    </li>
                @endif

                @if (in_array($user_type_id, \App\Models\UserType::vendorIds()))
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.sendMoneyRequest', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.sendMoneyRequest') }}">
                            {{ trans('menu.withdraw module.send money request') }}
                        </a>
                    </li>
                @endif

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new' ? 'active' : '' }}"
                        href="{{ route('admin.transactionsRequests', 'new') }}">
                        {{ trans('menu.withdraw module.new transaction requests') }}
                        <span class="badge badge-round badge-primary ms-1">{{ $new_transactions }}</span>
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted' ? 'active' : '' }}"
                        href="{{ route('admin.transactionsRequests', 'accepted') }}">
                        {{ trans('menu.withdraw module.accepted transaction requests') }}
                        <span class="badge badge-round badge-primary ms-1">{{ $accepted_transactions }}</span>
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected' ? 'active' : '' }}"
                        href="{{ route('admin.transactionsRequests', 'rejected') }}">
                        {{ trans('menu.withdraw module.rejected transaction requests') }}
                        <span class="badge badge-round badge-primary ms-1">{{ $rejected_transactions }}</span>
                    </a>
                </li>


            </ul>
        </li>

        <li class="menu-title mt-30">
            <span>{{ trans('menu.sections.financials') }}</span>
        </li>
        <li
            class="has-child {{ isParentMenuOpen(['admin.accounting.overview', 'admin.accounting.balance', 'admin.accounting.expenses'], ['admin/accounting*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.accounting.overview', 'admin.accounting.balance', 'admin.accounting.expenses'], ['admin/accounting*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-invoice"></span>
                <span class="menu-text">{{ trans('menu.accounting module.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                <li><a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">{{ trans('menu.accounting module.overview') }}</a>
                </li>
                <li><a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">{{ trans('menu.accounting module.balance') }}</a>
                </li>
                <li><a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">{{ trans('menu.accounting module.expenses keys') }}</a>
                </li>
                <li><a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">{{ trans('menu.accounting module.expenses') }}</a>
                </li>
            </ul>
        </li>
        @if(in_array($user_type_id, \App\Models\UserType::adminIds()))
            @canany(['activities.index', 'departments.index', 'categories.index', 'sub_categories.index'])
                <li class="menu-title mt-30">
                    <span>{{ trans('menu.sections.catalog management') }}</span>
                </li>
                <li
                    class="has-child {{ isParentMenuOpen(['admin.category-management.activities.index', 'admin.category-management.departments.index', 'admin.category-management.categories.index', 'admin.category-management.subcategories.index'], ['admin/category-management*']) ? 'open' : '' }}">
                    <a href="#"
                        class="{{ isParentMenuOpen(['admin.category-management.activities.index', 'admin.category-management.departments.index', 'admin.category-management.categories.index', 'admin.category-management.subcategories.index'], ['admin/category-management*']) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-sitemap"></span>
                        <span class="menu-text">{{ trans('menu.category managment.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('activities.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.activities.index', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.category-management.activities.index') }}">
                                    {{ trans('menu.activities.title') }}
                                    <span class="badge badge-round badge-primary ms-1">
                                        {{ \Modules\CategoryManagment\app\Models\Activity::count() }}
                                    </span>
                                </a>
                            </li>
                        @endcan

                        @can('departments.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.departments.index', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.category-management.departments.index') }}">
                                    {{ trans('menu.category managment.department') }}
                                    <span class="badge badge-round badge-primary ms-1">
                                        {{ \Modules\CategoryManagment\app\Models\Department::count() }}
                                    </span>
                                </a>
                            </li>
                        @endcan

                        @can('categories.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.categories.index', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.category-management.categories.index') }}">
                                    {{ trans('menu.category managment.main category') }}
                                    <span class="badge badge-round badge-primary ms-1">
                                        {{ \Modules\CategoryManagment\app\Models\Category::count() }}
                                    </span>
                                </a>
                            </li>
                        @endcan

                        @can('sub_categories.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.subcategories.index', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.category-management.subcategories.index') }}">
                                    {{ trans('menu.category managment.sub category') }}
                                    <span class="badge badge-round badge-primary ms-1">
                                        {{ \Modules\CategoryManagment\app\Models\SubCategory::count() }}
                                    </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
        @endif

        <li
            class="has-child {{ isParentMenuOpen(['admin.products.index', 'admin.products.pending', 'admin.products.rejected', 'admin.products.accepted', 'admin.products.create', 'admin.products.show', 'admin.products.edit', 'admin.products.bank', 'admin.products.stock-setup', 'admin.variant-keys.index', 'admin.variants-configurations.index'], ['admin/products*', 'admin/variant*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.products.index', 'admin.products.pending', 'admin.products.rejected', 'admin.products.accepted', 'admin.products.create', 'admin.products.show', 'admin.products.edit', 'admin.products.bank', 'admin.products.stock-setup', 'admin.variant-keys.index', 'admin.variants-configurations.index'], ['admin/products*', 'admin/variant*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-box"></span>
                <span class="menu-text">{{ trans('menu.products.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                <li>
                    <a class="fw-bold {{ isMenuActive('admin.products.stock-setup', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.products.stock-setup') }}">
                        {{ trans('menu.products.stock_setup') }}
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.bank', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.products.bank') }}">
                        {{ trans('menu.products.bank_products') }}
                        <span class="badge badge-round badge-info ms-1">
                            {{ \Modules\CatalogManagement\app\Models\Product::where('type', 'bank')->count() }}
                        </span>
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.products.index', 'admin.products.create', 'admin.products.show', 'admin.products.edit'], $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.products.index') }}">
                        {{ trans('menu.products.all_products') }}
                        <span class="badge badge-round badge-primary ms-1">
                            @if(in_array($user_type_id, \App\Models\UserType::adminIds()))
                                {{ \Modules\CatalogManagement\app\Models\Product::count() }}
                            @else
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                            @endif
                        </span>
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.pending', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.products.pending') }}">
                        {{ trans('menu.products.pending_products') }}
                        <span class="badge badge-round badge-warning ms-1">
                            @if(in_array($user_type_id, \App\Models\UserType::adminIds()))
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'pending')->count() }}
                            @else
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'pending')->where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                            @endif
                        </span>
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.rejected', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.products.rejected') }}">
                        {{ trans('menu.products.rejected_products') }}
                        <span class="badge badge-round badge-danger ms-1">
                            @if(in_array($user_type_id, \App\Models\UserType::adminIds()))
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'rejected')->count() }}
                            @else
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'rejected')->where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                            @endif
                        </span>
                    </a>
                </li>
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.accepted', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.products.accepted') }}">
                        {{ trans('menu.products.accepted_products') }}
                        <span class="badge badge-round badge-success ms-1">
                            @if(in_array($user_type_id, \App\Models\UserType::adminIds()))
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->count() }}
                            @else
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                            @endif
                        </span>
                    </a>
                </li>

                @if(in_array($user_type_id, \App\Models\UserType::adminIds()))
                    @canany(['variant-keys.view', 'variant-keys.create'])
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.variant-keys.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.variant-keys.index') }}">
                                {{ trans('menu.variant configurations.variant config keys') }}
                                <span class="badge badge-round badge-primary ms-1">{{ \Modules\CatalogManagement\app\Models\VariantConfigurationKey::count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.variants-configurations.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.variants-configurations.index') }}">
                                {{ trans('menu.variant configurations.variant config') }}
                                <span class="badge badge-round badge-primary ms-1">{{ \Modules\CatalogManagement\app\Models\VariantsConfiguration::count() }}</span>
                            </a>
                        </li>
                    @endcanany
                @endif
            </ul>
        </li>


        @can('reviews.view')
            <li class="has-child">
                <a href="#" class="">
                    <span class="nav-icon uil uil-star"></span>
                    <span class="menu-text">{{ trans('menu.product reviews.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.product reviews.all') }}
                            <span class="badge badge-round badge-primary ms-1">150</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.product reviews.accepted') }}
                            <span class="badge badge-round badge-primary ms-1">120</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.product reviews.rejected') }}
                            <span class="badge badge-round badge-primary ms-1">30</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
            @canany(['taxes.view', 'taxes.create'])
                <li
                    class="has-child {{ isParentMenuOpen(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], ['admin/taxes*']) ? 'open' : '' }}">
                    <a href="#"
                        class="{{ isParentMenuOpen(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], ['admin/taxes*']) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-percentage"></span>
                        <span class="menu-text">{{ trans('menu.taxes.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('taxes.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.taxes.index', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.taxes.index') }}">
                                    {{ trans('menu.taxes.all') }}
                                    <span class="badge badge-round badge-primary ms-1">12</span>
                                </a>
                            </li>
                        @endcan

                        @can('taxes.create')
                            <li><a href="{{ route('admin.taxes.create') }}"
                                    class="{{ isMenuActive('admin.taxes.create', $currentRoute) ? 'active' : '' }}">{{ trans('menu.taxes.create') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
        @endif

        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))

            @canany(['offers.view', 'offers.create'])
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-gift"></span>
                        <span class="menu-text">{{ trans('menu.offers.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('offers.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.dashboard') }}">
                                    {{ trans('menu.offers.all') }}
                                    <span class="badge badge-round badge-warning ms-1">8</span>
                                </a>
                            </li>
                        @endcan

                        @can('offers.create')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.dashboard') }}">
                                    {{ trans('menu.offers.create') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

        @endif

        @can('brands.index')
            <li>
                <a href="{{ route('admin.brands.index') }}"
                    class="{{ isMenuActive(['admin.brands.index', 'admin.brands.create', 'admin.brands.show', 'admin.brands.edit'], $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-ticket"></span>
                            <span class="menu-text">{{ trans('menu.brands.title') }}</span>
                        </span>
                        <span class="badge badge-round badge-success  ms-1">{{ \Modules\CatalogManagement\app\Models\Brand::count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
            @can('promocodes.view')
                <li>
                    <a href="{{ route('admin.promocodes.index') }}" class="{{ isMenuActive(['admin.promocodes.index', 'admin.promocodes.create', 'admin.promocodes.edit', 'admin.promocodes.show'], $currentRoute) ? 'active' : '' }}">
                        <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                            <span class="d-flex align-items-center">
                                <span class="nav-icon uil uil-ticket"></span>
                                <span class="menu-text">{{ trans('menu.promocodes.title') }}</span>
                            </span>
                            <span class="badge badge-round badge-success  ms-1">{{ \Modules\CatalogManagement\app\Models\Promocode::count() }}</span>
                        </span>
                    </a>
                </li>
            @endcan
        @endif


        <li class="menu-title mt-30">
            <span>{{ trans('menu.sections.user management') }}</span>
        </li>
        <li
            class="has-child {{ isParentMenuOpen(['admin.admin-management.roles.index', 'admin.admin-management.admins.index'], ['admin/admin-management*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.admin-management.roles.index', 'admin.admin-management.admins.index'], ['admin/admin-management*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-user-check"></span>
                <span class="menu-text">{{ trans('menu.admin managment.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                @can('roles.index')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.admin-management.roles.index', 'admin.admin-management.roles.create', 'admin.admin-management.roles.show', 'admin.admin-management.roles.edit'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.admin-management.roles.index') }}">
                            {{ trans('menu.admin managment.roles managment') }}
                        </a>
                    </li>
                @endcan

                @can('admins.index')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.admin-management.admins.index', 'admin.admin-management.admins.create', 'admin.admin-management.admins.show', 'admin.admin-management.admins.edit'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.admin-management.admins.index') }}">
                            {{ trans('menu.admin managment.admin managment') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>

        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
            @canany(['vendors.index', 'vendors.create'])
                <li
                    class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendors*') ? 'open' : '' }}">
                    <a href="#"
                        class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendors*') ? 'active' : '' }}">
                        <span class="nav-icon uil uil-users-alt"></span>
                        <span class="menu-text">{{ trans('menu.vendors.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('vendors.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.vendors.index', 'admin.vendors.show', 'admin.vendors.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.vendors.index') }}">
                                    {{ trans('menu.vendors.all') }}
                                    <span class="badge badge-round badge-primary  ms-1">
                                        {{ \Modules\Vendor\app\Models\Vendor::count() }}
                                    </span>
                                </a>
                            </li>
                        @endcan

                        @can('vendors.create')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendors.create', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.vendors.create') }}">
                                    {{ trans('menu.vendors.create') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany


            @can('vendor_requests.new')
                <li class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendor-requests*') ? 'open' : '' }}">
                    <a href="#" class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendor-requests*') ? 'active' : '' }}">
                        <span class="nav-icon uil uil-clipboard-notes"></span>
                        <span class="menu-text">{{ trans('menu.become a vendor requests.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status') ? 'active' : '' }}"
                                href="{{ route('admin.vendor-requests.index') }}">
                                {{ trans('menu.become a vendor requests.new') }}
                                <span class="badge badge-round badge-primary ms-1">{{ \Modules\Vendor\app\Models\VendorRequest::pending()->count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') === 'approved' ? 'active' : '' }}"
                                href="{{ route('admin.vendor-requests.index') }}?status=approved">
                                {{ trans('menu.become a vendor requests.accepted') }}
                                <span class="badge badge-round badge-primary ms-1">{{ \Modules\Vendor\app\Models\VendorRequest::approved()->count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') === 'rejected' ? 'active' : '' }}"
                                href="{{ route('admin.vendor-requests.index') }}?status=rejected">
                                {{ trans('menu.become a vendor requests.rejected') }}
                                <span class="badge badge-round badge-primary ms-1">{{ \Modules\Vendor\app\Models\VendorRequest::rejected()->count() }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('customers.index')
                <li class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/customers*') ? 'open' : '' }}">
                    <a href="#"
                        class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/customers*') ? 'active' : '' }}">
                        <span class="nav-icon uil uil-user-circle"></span>
                        <span class="menu-text">{{ trans('menu.customers.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.customers.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.customers.index') }}">
                                {{ trans('menu.customers.all') }}
                                <span class="badge badge-round badge-primary ms-1">{{ \Modules\Customer\app\Models\Customer::count() }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
        @endif

        <li class="menu-title mt-30">
            <span>{{ trans('menu.sections.order and fulfillment') }}</span>
        </li>
        <li
            class="has-child {{ isParentMenuOpen(['admin.orders.new', 'admin.orders.inprogress', 'admin.orders.delivered', 'admin.orders.canceled', 'admin.orders.refunded'], ['admin/orders*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.orders.new', 'admin.orders.inprogress', 'admin.orders.delivered', 'admin.orders.canceled', 'admin.orders.refunded'], ['admin/orders*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-shopping-cart"></span>
                <span class="menu-text">{{ trans('menu.orders.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">
                        {{ trans('menu.orders.new') }}
                        <span class="badge badge-round badge-primary  ms-1">50</span>
                    </a>
                </li>
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">
                        {{ trans('menu.orders.inprogress') }}
                        <span class="badge badge-round badge-primary  ms-1">50</span>
                    </a>
                </li>
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">
                        {{ trans('menu.orders.delivered') }}
                        <span class="badge badge-round badge-primary  ms-1">50</span>
                    </a>
                </li>
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">
                        {{ trans('menu.orders.canceled') }}
                        <span class="badge badge-round badge-primary  ms-1">50</span>
                    </a>
                </li>
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.dashboard') }}">
                        {{ trans('menu.orders.refunded') }}
                        <span class="badge badge-primary  badge-round ms-1">50</span>
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ isMenuActive('admin.order-stages.index', $currentRoute) ? 'active' : '' }}">
                <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-process"></span>
                        <span class="menu-text">{{ trans('menu.orders.order stages') }}</span>
                    </span>
                    <span class="badge badge-primary  badge-round ms-1">500</span>
                </span>
            </a>
        </li>

        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
            @can('shipping_methods.index')
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                            <span class="d-flex align-items-center">
                                <span class="nav-icon uil uil-truck"></span>
                                <span class="menu-text">{{ trans('menu.orders.shipping methods') }}</span>
                            </span>
                            <span class="badge badge-round badge-primary  ms-1">50</span>
                        </span>
                    </a>
                </li>
            @endcan
        @endif


        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
            @can('points.index')
                <li class="menu-title mt-30">
                    <span>{{ trans('menu.sections.points system') }}</span>
                </li>
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-coins"></span>
                        <span class="menu-text">{{ trans('menu.point managment.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.point managment.title') }}
                                {{-- <span class="badge badge-round badge-primary  ms-1">50</span> --}}
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.point managment.users points') }}
                                {{-- <span class="badge badge-round badge-primary  ms-1">50</span> --}}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan


            @can('advertisements.index')
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-trophy"></span>
                        <span class="menu-text">{{ trans('menu.advertisements.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.advertisements.title') }}
                                <span class="badge badge-round badge-primary  ms-1">50</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.advertisements.positions') }}
                                <span class="badge badge-round badge-primary  ms-1">50</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @canany(['notifications.send', 'notifications.view'])
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-bell"></span>
                        <span class="menu-text">{{ trans('menu.notifications.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('notifications.send')
                            <li><a
                                    href="{{ route('admin.dashboard') }}">{{ trans('menu.notifications.send notification') }}</a>
                            </li>
                        @endcan

                        @can('notifications.view')
                            <li><a
                                    href="{{ route('admin.dashboard') }}">{{ trans('menu.notifications.all notification') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany





            @can('blog.view')
                <li class="menu-title mt-30">
                    <span>{{ trans('menu.sections.content and engagement') }}</span>
                </li>
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-edit-alt"></span>
                        <span class="menu-text">{{ trans('menu.blog managment.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li><a href="{{ route('admin.dashboard') }}">{{ trans('menu.blog managment.categories') }}</a>
                        </li>
                        <li><a href="{{ route('admin.dashboard') }}">{{ trans('menu.blog managment.blogs') }}</a>
                        </li>
                    </ul>
                </li>
            @endcan


            @can('reports.view')
                <li class="menu-title mt-30">
                    <span>{{ trans('menu.sections.reports') }}</span>
                </li>
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-chart-line"></span>
                        <span class="menu-text">{{ trans('menu.reports.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li><a href="{{ route('admin.dashboard') }}">{{ trans('menu.reports.registerd users') }}</a>
                        </li>
                        <li><a href="{{ route('admin.dashboard') }}">{{ trans('menu.reports.area users') }}</a></li>
                        <li><a href="{{ route('admin.dashboard') }}">{{ trans('menu.reports.orders report') }}</a></li>
                        <li><a href="{{ route('admin.dashboard') }}">{{ trans('menu.reports.product report') }}</a>
                        </li>
                        <li><a href="{{ route('admin.dashboard') }}">{{ trans('menu.reports.points report') }}</a></li>
                    </ul>
                </li>
            @endcan

            @can('settings.view')
                <li class="menu-title mt-30">
                    <span>{{ trans('menu.sections.settings') }}</span>
                </li>
            @endcan

            @can('settings.logs.view')
                <li>
                    <a href="{{ route('admin.system-settings.activity-logs.index') }}"
                        class="{{ isMenuActive('admin.system-settings.activity-logs.index', $currentRoute) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-history"></span>
                        <span class="menu-text">{{ trans('menu.system log.title') }}</span>
                    </a>
                </li>
            @endcan

            @canany(['area.country.index', 'area.city.index', 'area.region.index', 'area.subregion.index'])
                <li
                    class="has-child {{ isParentMenuOpen(['admin.area-settings.countries.index', 'admin.area-settings.cities.index', 'admin.area-settings.regions.index', 'admin.area-settings.subregions.index'], ['admin/area-settings*']) ? 'open' : '' }}">
                    <a href="#"
                        class="{{ isParentMenuOpen(['admin.area-settings.countries.index', 'admin.area-settings.cities.index', 'admin.area-settings.regions.index', 'admin.area-settings.subregions.index'], ['admin/area-settings*']) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-map-marker"></span>
                        <span class="menu-text">{{ trans('menu.area settings.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('area.country.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.countries.index', 'admin.area-settings.countries.create', 'admin.area-settings.countries.show', 'admin.area-settings.countries.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.area-settings.countries.index') }}">
                                    {{ trans('menu.area settings.country') }}
                                    <span class="badge badge-round badge-primary  ms-1">15</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.city.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.cities.index', 'admin.area-settings.cities.create', 'admin.area-settings.cities.show', 'admin.area-settings.cities.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.area-settings.cities.index') }}">
                                    {{ trans('menu.area settings.city') }}
                                    <span class="badge badge-round badge-info ms-1">120</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.region.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.regions.index', 'admin.area-settings.regions.create', 'admin.area-settings.regions.show', 'admin.area-settings.regions.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.area-settings.regions.index') }}">
                                    {{ trans('menu.area settings.region') }}
                                    <span class="badge badge-round badge-warning ms-1">45</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.subregion.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.subregions.index', 'admin.area-settings.subregions.create', 'admin.area-settings.subregions.show', 'admin.area-settings.subregions.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.area-settings.subregions.index') }}">
                                    {{ trans('menu.area settings.subregion') }}
                                    <span class="badge badge-round badge-secondary ms-1">80</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['settings.terms.view', 'settings.privacy.view', 'settings.about.view', 'settings.contact.view',
                'settings.messages.view'])
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-setting"></span>
                        <span class="menu-text">{{ trans('menu.system settings.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('settings.terms.view')
                            <li><a href="{{ route('admin.dashboard') }}"><span
                                        class="nav-icon uil uil-file-contract-dollar"></span>
                                    {{ trans('menu.system settings.terms and conditions') }}</a></li>
                        @endcan

                        @can('settings.privacy.view')
                            <li><a href="{{ route('admin.dashboard') }}"><span class="nav-icon uil uil-shield-check"></span>
                                    {{ trans('menu.system settings.privacy policy') }}</a></li>
                        @endcan

                        @can('settings.about.view')
                            <li><a href="{{ route('admin.dashboard') }}"><span class="nav-icon uil uil-info-circle"></span>
                                    {{ trans('menu.system settings.about us') }}</a></li>
                        @endcan

                        @can('settings.contact.view')
                            <li><a href="{{ route('admin.dashboard') }}"><span class="nav-icon uil uil-phone"></span>
                                    {{ trans('menu.system settings.contact us') }}</a></li>
                        @endcan

                        @can('settings.messages.view')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.dashboard') }}">
                                    <span class="d-flex align-items-center">
                                        <span class="nav-icon uil uil-envelope"></span>
                                        <span>{{ trans('menu.system settings.messages') }}</span>
                                    </span>
                                    <span class="badge badge-round badge-primary ms-1">25</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('system.currency.index')
                <li>
                    <a href="{{ route('admin.system-settings.currencies.index') }}"
                        class="{{ isMenuActive(['admin.system-settings.currencies.index', 'admin.system-settings.currencies.create', 'admin.system-settings.currencies.show', 'admin.system-settings.currencies.edit'], $currentRoute) ? 'active' : '' }}">
                        <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                            <span class="d-flex align-items-center">
                                <span class="nav-icon uil uil-dollar-alt"></span>
                                <span class="menu-text">{{ trans('menu.currencies.title') }}</span>
                            </span>
                        </span>
                    </a>
                </li>
            @endcan

        @endif

    </ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to open parent menus
        function openParentMenus() {
            // Find all active menu items (both parent and child)
            const activeMenuItems = document.querySelectorAll('.sidebar_nav a.active');

            activeMenuItems.forEach(function(activeItem) {
                // Check if this is a child menu item (inside a ul that's inside a has-child li)
                const parentUl = activeItem.closest('ul');
                if (parentUl) {
                    const parentHasChild = parentUl.closest('li.has-child');
                    if (parentHasChild) {
                        // Add open class to parent menu
                        parentHasChild.classList.add('open');

                        // Make sure parent link is also active
                        const parentLink = parentHasChild.querySelector(':scope > a');
                        if (parentLink && !parentLink.classList.contains('active')) {
                            parentLink.classList.add('active');
                        }

                        // Show the submenu
                        const submenu = parentHasChild.querySelector(':scope > ul');
                        if (submenu) {
                            submenu.style.display = 'block';
                        }
                    }
                }
            });

            // Also handle menus that already have the 'open' class from PHP
            const openMenus = document.querySelectorAll('.sidebar_nav li.has-child.open');
            openMenus.forEach(function(menu) {
                const parentLink = menu.querySelector(':scope > a');
                if (parentLink && !parentLink.classList.contains('active')) {
                    parentLink.classList.add('active');
                }

                // Ensure submenu is visible
                const submenu = menu.querySelector(':scope > ul');
                if (submenu) {
                    submenu.style.display = 'block';
                }
            });
        }

        // Run the function
        openParentMenus();

        // Also run after a short delay to ensure all elements are rendered
        setTimeout(openParentMenus, 100);
    });
</script>
