@php
    $currentLocale = LaravelLocalization::getCurrentLocale();
    try {
        $currentRoute = Request::route() ? Request::route()->getName() : null;
    } catch (\Exception $e) {
        $currentRoute = null;
    }
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
    $all_transactions = 0;

    try {
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
    } catch (\Exception $e) {
        // Silently fail - keep all at 0
        $new_transactions = 0;
        $accepted_transactions = 0;
        $rejected_transactions = 0;
        $all_transactions = 0;
    }
@endphp
<div class="sidebar__menu-group">
    <ul class="sidebar_nav">
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ isMenuActive('admin.dashboard', $currentRoute) ? 'active' : '' }}">
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
                            <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ $all_transactions }}</span>
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
                        href="{{ route('admin.transactionsRequests', ['status' => 'new']) }}">
                        {{ trans('menu.withdraw module.new transaction requests') }}
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ $new_transactions }}</span>
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted' ? 'active' : '' }}"
                        href="{{ route('admin.transactionsRequests', ['status' => 'accepted']) }}">
                        {{ trans('menu.withdraw module.accepted transaction requests') }}
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ $accepted_transactions }}</span>
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected' ? 'active' : '' }}"
                        href="{{ route('admin.transactionsRequests', ['status' => 'rejected']) }}">
                        {{ trans('menu.withdraw module.rejected transaction requests') }}
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.secondary') }}; color: white;">{{ $rejected_transactions }}</span>
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
        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
            @canany(['departments.index', 'categories.index', 'sub_categories.index'])
                <li class="menu-title mt-30">
                    <span>{{ trans('menu.sections.catalog management') }}</span>
                </li>
                <li
                    class="has-child {{ isParentMenuOpen(['admin.category-management.departments.index', 'admin.category-management.categories.index', 'admin.category-management.subcategories.index'], ['admin/category-management*']) ? 'open' : '' }}">
                    <a href="#"
                        class="{{ isParentMenuOpen(['admin.category-management.departments.index', 'admin.category-management.categories.index', 'admin.category-management.subcategories.index'], ['admin/category-management*']) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-sitemap"></span>
                        <span class="menu-text">{{ trans('menu.category managment.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">

                        @can('departments.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.departments.index', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.category-management.departments.index') }}">
                                    {{ trans('menu.category managment.department') }}
                                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
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
                                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
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
                                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
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
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
                            {{ \Modules\CatalogManagement\app\Models\Product::where('type', 'bank')->count() }}
                        </span>
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.products.index', 'admin.products.create', 'admin.products.show', 'admin.products.edit'], $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.products.index') }}">
                        {{ trans('menu.products.all_products') }}
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
                            @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
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
                        <span class="badge badge-round ms-1" style="background-color: #ffc107; color: white;">
                            @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
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
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.secondary') }}; color: white;">
                            @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
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
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
                            @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->count() }}
                            @else
                                {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                            @endif
                        </span>
                    </a>
                </li>

                @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                    @canany(['variant-keys.view', 'variant-keys.create'])
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.variant-keys.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.variant-keys.index') }}">
                                {{ trans('menu.variant configurations.variant config keys') }}
                                <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\CatalogManagement\app\Models\VariantConfigurationKey::count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.variants-configurations.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.variants-configurations.index') }}">
                                {{ trans('menu.variant configurations.variant config') }}
                                <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\CatalogManagement\app\Models\VariantsConfiguration::count() }}</span>
                            </a>
                        </li>
                    @endcanany
                @endif
            </ul>
        </li>

        {{-- Bundles Menu --}}
        <li
            class="has-child {{ isParentMenuOpen(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit', 'admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], ['admin/bundle-categories*', 'admin/bundles*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit', 'admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], ['admin/bundle-categories*', 'admin/bundles*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-package"></span>
                <span class="menu-text">{{ trans('menu.bundles.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.bundle-categories.index') }}">
                            {{ trans('menu.bundles.bundle_categories') }}
                            <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
                                {{ \Modules\CatalogManagement\app\Models\BundleCategory::count() }}
                            </span>
                        </a>
                    </li>
                @endif
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.bundles.index') }}">
                        {{ trans('menu.bundles.all_bundles') }}
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
                            @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                {{ \Modules\CatalogManagement\app\Models\Bundle::count() }}
                            @else
                                {{ \Modules\CatalogManagement\app\Models\Bundle::where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                            @endif
                        </span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Occasions Menu --}}
        <li
            class="{{ isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute) ? 'active' : '' }}">
            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute) ? 'active' : '' }}"
                href="{{ route('admin.occasions.index') }}">
                <span class="d-flex align-items-center">
                    <span class="nav-icon uil uil-calendar-alt"></span>
                    <span class="menu-text">{{ trans('menu.occasions') }}</span>
                </span>
                <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">
                    @php
                        $occasions_count = \Modules\CatalogManagement\app\Models\Occasion::count();
                        if (in_array($user_type_id, \App\Models\UserType::vendorIds())) {
                            $occasions_count = \Modules\CatalogManagement\app\Models\Occasion::where(
                                'vendor_id',
                                $vendor->id,
                            )->count();
                        }
                    @endphp
                    {{ $occasions_count }}
                </span>
            </a>
        </li>
        @can('reviews.view')
            @php
                try {
                    $reviews_all = Modules\CatalogManagement\app\Models\Review::count();
                    $reviews_accepted = Modules\CatalogManagement\app\Models\Review::where(
                        'status',
                        'approved',
                    )->count();
                    $reviews_rejected = Modules\CatalogManagement\app\Models\Review::where(
                        'status',
                        'rejected',
                    )->count();
                } catch (\Exception $e) {
                    $reviews_all = $reviews_accepted = $reviews_rejected = 0;
                }
            @endphp
            <li class="has-child {{ isParentMenuOpen(['admin.reviews.index'], ['admin/reviews*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.reviews.index'], ['admin/reviews*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-star"></span>
                    <span class="menu-text">{{ trans('menu.product_reviews.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'all' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'all']) }}">
                            {{ trans('menu.product_reviews.all') }}
                            <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ $reviews_all }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'approved']) }}">
                            {{ trans('menu.product_reviews.accepted') }}
                            <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ $reviews_accepted }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}">
                            {{ trans('menu.product_reviews.rejected') }}
                            <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.secondary') }}; color: white;">{{ $reviews_rejected }}</span>
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
                        <span class="menu-text fw-bold">{{ trans('menu.taxes.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('taxes.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.taxes.index', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.taxes.index') }}">
                                    {{ trans('menu.taxes.all') }}
                                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\CatalogManagement\app\Models\Tax::count() }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('taxes.create')
                            <li><a href="{{ route('admin.taxes.create') }}"
                                    class=" fw-bold {{ isMenuActive('admin.taxes.create', $currentRoute) ? 'active' : '' }}">{{ trans('menu.taxes.create') }}</a>
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
                            <span class="menu-text fw-bold">{{ trans('menu.brands.title') }}</span>
                        </span>
                        <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\CatalogManagement\app\Models\Brand::count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
            @can('promocodes.view')
                <li>
                    <a href="{{ route('admin.promocodes.index') }}"
                        class="{{ isMenuActive(['admin.promocodes.index', 'admin.promocodes.create', 'admin.promocodes.edit', 'admin.promocodes.show'], $currentRoute) ? 'active' : '' }}">
                        <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                            <span class="d-flex align-items-center">
                                <span class="nav-icon uil uil-ticket"></span>
                                <span class="menu-text">{{ trans('menu.promocodes.title') }}</span>
                            </span>
                            <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\CatalogManagement\app\Models\Promocode::count() }}</span>
                        </span>
                    </a>
                </li>
            @endcan
        @endif

        @can('points.index')
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.points system') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.points-settings.index', 'admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], ['admin/points-settings*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.points-settings.index', 'admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], ['admin/points-settings*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-coins"></span>
                    <span class="menu-text">{{ trans('menu.point managment.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.points-settings.index'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.points-settings.index') }}">
                            {{ trans('menu.point managment.title') }}
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.points-settings.user-points.index') }}">
                            {{ trans('menu.point managment.users points') }}
                        </a>
                    </li>
                </ul>
            </li>
        @endcan


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
                <li
                    class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendor-requests*') ? 'open' : '' }}">
                    <a href="#"
                        class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendor-requests*') ? 'active' : '' }}">
                        <span class="nav-icon uil uil-clipboard-notes"></span>
                        <span class="menu-text">{{ trans('menu.become a vendor requests.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status') ? 'active' : '' }}"
                                href="{{ route('admin.vendor-requests.index') }}">
                                {{ trans('common.all') }}
                                <span
                                    class="badge badge-round badge-info ms-1">{{ \Modules\Vendor\app\Models\VendorRequest::count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status') ? 'active' : '' }}"
                                href="{{ route('admin.vendor-requests.index') }}">
                                {{ trans('menu.become a vendor requests.new') }}
                                <span
                                    class="badge badge-round badge-primary ms-1">{{ \Modules\Vendor\app\Models\VendorRequest::pending()->count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') === 'approved' ? 'active' : '' }}"
                                href="{{ route('admin.vendor-requests.index') }}?status=approved">
                                {{ trans('menu.become a vendor requests.accepted') }}
                                <span
                                    class="badge badge-round badge-primary ms-1">{{ \Modules\Vendor\app\Models\VendorRequest::approved()->count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') === 'rejected' ? 'active' : '' }}"
                                href="{{ route('admin.vendor-requests.index') }}?status=rejected">
                                {{ trans('menu.become a vendor requests.rejected') }}
                                <span
                                    class="badge badge-round badge-primary ms-1">{{ \Modules\Vendor\app\Models\VendorRequest::rejected()->count() }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('customers.index')
                <li
                    class="has-child {{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/customers*') ? 'open' : '' }}">
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
                                <span
                                    class="badge badge-round badge-primary ms-1">{{ \Modules\Customer\app\Models\Customer::count() }}</span>
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
            class="has-child {{ isParentMenuOpen(['admin.orders.index', 'admin.orders.create'], ['admin/orders*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.orders.index', 'admin.orders.create'], ['admin/orders*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-shopping-cart"></span>
                <span class="menu-text">{{ trans('menu.orders.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.orders.index', $currentRoute) && !request()->has('stage') ? 'active' : '' }}"
                        href="{{ route('admin.orders.index') }}">
                        {{ trans('menu.orders.all') }}
                        <span
                            class="badge badge-round badge-primary ms-1">{{ \Modules\Order\app\Models\Order::count() }}</span>
                    </a>
                </li>
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.orders.create', $currentRoute) ? 'active' : '' }}"
                        href="{{ route('admin.orders.create') }}">
                        {{ trans('menu.orders.create') }}
                    </a>
                </li>

                {{-- Order Stages --}}
                @php
                    $countryCode = session('country_code');
                    $countryId = $countryCode
                        ? \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id')
                        : null;

                    $orderStages = \Modules\Order\app\Models\OrderStage::where(function ($q) use ($countryId) {
                            $q->where(function ($sub) {
                                $sub->whereNull('country_id')->where('is_system', 1);
                            });

                            if ($countryId) {
                                $q->orWhere('country_id', $countryId);
                            }
                        })
                        ->orderBy('sort_order')
                        ->get();
                @endphp
                @foreach ($orderStages as $stage)
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('stage') == $stage->id ? 'active' : '' }}"
                            href="{{ route('admin.orders.index', ['stage' => $stage->id]) }}">
                            {{ $stage->translations->where('lang_key', 'name')->first()?->lang_value ?? $stage->slug }}
                            <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\Order\app\Models\Order::where('stage_id', $stage->id)->count() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>

        @can('order-stages.index')
            <li>
                <a href="{{ route('admin.order-stages.index') }}"
                    class="{{ isMenuActive('admin.order-stages.index', $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-process"></span>
                            <span class="menu-text">{{ trans('menu.orders.order stages') }}</span>
                        </span>
                    </span>
                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ $orderStages->count() }}</span>
                </a>
            </li>
        @endcan

        @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
            @can('shipping_methods.index')
                <li>
                    <a href="{{ route('admin.shippings.index') }}"
                        class="{{ isMenuActive('admin.shippings.index', $currentRoute) ? 'active' : '' }}">
                        <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                            <span class="d-flex align-items-center">
                                <span class="nav-icon uil uil-truck"></span>
                                <span class="menu-text">{{ trans('menu.orders.shipping methods') }}</span>
                            </span>
                            <span class="badge badge-round badge-primary  ms-1">{{ \Modules\Order\app\Models\Shipping::count() }}</span>
                        </span>
                    </a>
                </li>
            @endcan
        @endif


        @if (isAdmin())
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
                <li
                    class="has-child {{ isParentMenuOpen(['admin.system-settings.blog-categories.index', 'admin.system-settings.blogs.index'], ['admin/system-settings/blog-categories*', 'admin/system-settings/blogs*']) ? 'open' : '' }}">
                    <a href="#"
                        class="{{ isParentMenuOpen(['admin.system-settings.blog-categories.index', 'admin.system-settings.blogs.index'], ['admin/system-settings/blog-categories*', 'admin/system-settings/blogs*']) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-edit-alt"></span>
                        <span class="menu-text">{{ trans('menu.blog managment.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.blog-categories.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.system-settings.blog-categories.index') }}">
                                {{ trans('menu.blog managment.categories') }}
                                <span class="badge badge-round badge-info ms-1">
                                    @php
                                        try {
                                            $blog_categories_count = \Modules\SystemSetting\app\Models\BlogCategory::count();
                                        } catch (\Exception $e) {
                                            $blog_categories_count = 0;
                                        }
                                    @endphp
                                    {{ $blog_categories_count }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.blogs.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.system-settings.blogs.index') }}">
                                {{ trans('menu.blog managment.blogs') }}
                                <span class="badge badge-round badge-primary ms-1">
                                    @php
                                        try {
                                            $blogs_count = \Modules\SystemSetting\app\Models\Blog::count();
                                        } catch (\Exception $e) {
                                            $blogs_count = 0;
                                        }
                                    @endphp
                                    {{ $blogs_count }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            <li class="{{ isMenuActive('admin.messages.index', $currentRoute) ? 'active' : '' }}">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.messages.index', $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.messages.index') }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-envelope"></span>
                        <span class="menu-text">{{ __('systemsetting::messages.messages') }}</span>
                    </span>
                    <span class="badge badge-round badge-primary ms-1">
                        {{ \Modules\SystemSetting\app\Models\Message::count() }}
                    </span>
                </a>
            </li>

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


            @can('advertisements.index')
                <li
                    class="has-child {{ isParentMenuOpen(['admin.system-settings.ads.index'], ['*/system-settings/ads*']) ? 'open' : '' }}">
                    <a href="#"
                        class="{{ isParentMenuOpen(['admin.system-settings.ads.index'], ['*/system-settings/ads*']) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-trophy"></span>
                        <span class="menu-text">{{ trans('menu.advertisements.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.ads.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.system-settings.ads.index') }}">
                                {{ trans('menu.advertisements.title') }}
                                <span class="badge badge-round badge-primary  ms-1">
                                    {{ \Modules\SystemSetting\app\Models\Ad::count() }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            {{-- Frontend Settings --}}
            <li
                class="has-child {{ isParentMenuOpen(['admin.system-settings.features.index', 'admin.system-settings.footer-content.index', 'admin.system-settings.faqs.index', 'admin.system-settings.sliders.index', 'admin.system-settings.site-information.index'], ['*/system-settings/features*', '*/system-settings/footer-content*', '*/system-settings/faqs*', '*/system-settings/sliders*', '*/system-settings/site-information*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.system-settings.features.index', 'admin.system-settings.footer-content.index', 'admin.system-settings.faqs.index', 'admin.system-settings.sliders.index', 'admin.system-settings.site-information.index'], ['*/system-settings/features*', '*/system-settings/footer-content*', '*/system-settings/faqs*', '*/system-settings/sliders*', '*/system-settings/site-information*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-browser"></span>
                    <span class="menu-text fw-bold">{{ trans('menu.frontend settings.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a href="{{ route('admin.system-settings.features.index') }}"
                            class="{{ isMenuActive('admin.system-settings.features.index', $currentRoute) ? 'active' : '' }}">
                            <span class="nav-icon uil uil-star"></span>
                            <span class=" fw-bold">{{ trans('menu.frontend settings.our features') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system-settings.footer-content.index') }}"
                            class="{{ isMenuActive('admin.system-settings.footer-content.index', $currentRoute) ? 'active' : '' }}">
                            <span class="nav-icon uil uil-align-center-alt"></span>
                            <span class=" fw-bold">{{ trans('menu.frontend settings.footer content') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system-settings.faqs.index') }}"
                            class="{{ isMenuActive(['admin.system-settings.faqs.index', 'admin.system-settings.faqs.create', 'admin.system-settings.faqs.edit', 'admin.system-settings.faqs.show'], $currentRoute) ? 'active' : '' }}">
                            <span class="nav-icon uil uil-question-circle"></span>
                            <span class=" fw-bold">{{ trans('menu.frontend settings.faq management') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system-settings.sliders.index') }}"
                            class="{{ isMenuActive(['admin.system-settings.sliders.index', 'admin.system-settings.sliders.create', 'admin.system-settings.sliders.edit', 'admin.system-settings.sliders.show'], $currentRoute) ? 'active' : '' }}">
                            <span class="nav-icon uil uil-image-v"></span>
                            <span class=" fw-bold">{{ trans('menu.frontend settings.sliders') }}</span>
                        </a>
                    </li>

                </ul>
            </li>
            {{-- Site Information --}}
            <li class="has-child">
                <a href="#"
                    class="{{ isMenuActive(['admin.system-settings.site-information.index'], $currentRoute) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-info-circle"></span>
                    <span class="fw-bold">{{ trans('menu.frontend settings.site information') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a href="{{ route('admin.system-settings.site-information.index') }}"
                            class="{{ isMenuActive('admin.system-settings.site-information.index', $currentRoute) ? 'active' : '' }}">
                            <span class="fw-bold">{{ trans('menu.frontend settings.contact us') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system-settings.return-policy.index') }}"
                            class="{{ isMenuActive('admin.system-settings.return-policy.index', $currentRoute) ? 'active' : '' }}">
                            <span class="fw-bold">{{ trans('menu.frontend settings.return policy') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system-settings.service-terms.index') }}"
                            class="{{ isMenuActive('admin.system-settings.service-terms.index', $currentRoute) ? 'active' : '' }}">
                            <span class="fw-bold">{{ trans('menu.frontend settings.service terms') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system-settings.privacy-policy.index') }}"
                            class="{{ isMenuActive('admin.system-settings.privacy-policy.index', $currentRoute) ? 'active' : '' }}">
                            <span class="fw-bold">{{ trans('menu.frontend settings.privacy policy') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.system-settings.terms-conditions.index') }}"
                            class="{{ isMenuActive('admin.system-settings.terms-conditions.index', $currentRoute) ? 'active' : '' }}">
                            <span class="fw-bold">{{ trans('menu.frontend settings.terms conditions') }}</span>
                        </a>
                    </li>
                </ul>
            </li>



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
                                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\AreaSettings\app\Models\Country::count() }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.city.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.cities.index', 'admin.area-settings.cities.create', 'admin.area-settings.cities.show', 'admin.area-settings.cities.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.area-settings.cities.index') }}">
                                    {{ trans('menu.area settings.city') }}
                                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\AreaSettings\app\Models\City::count() }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.region.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.regions.index', 'admin.area-settings.regions.create', 'admin.area-settings.regions.show', 'admin.area-settings.regions.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.area-settings.regions.index') }}">
                                    {{ trans('menu.area settings.region') }}
                                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\AreaSettings\app\Models\Region::count() }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.subregion.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.subregions.index', 'admin.area-settings.subregions.create', 'admin.area-settings.subregions.show', 'admin.area-settings.subregions.edit'], $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.area-settings.subregions.index') }}">
                                    {{ trans('menu.area settings.subregion') }}
                                    <span class="badge badge-round ms-1" style="background-color: {{ config('branding.colors.primary') }}; color: white;">{{ \Modules\AreaSettings\app\Models\SubRegion::count() }}</span>
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

            @can('settings.logs.view')
                <li>
                    <a href="{{ route('admin.system-settings.activity-logs.index') }}"
                        class="{{ isMenuActive('admin.system-settings.activity-logs.index', $currentRoute) ? 'active' : '' }}">
                        <span class="nav-icon uil uil-history"></span>
                        <span class="menu-text">{{ trans('menu.system log.title') }}</span>
                    </a>
                </li>
            @endcan

        @endif

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
