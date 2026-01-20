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

    // Helper function to get badge style based on active state
    if (!function_exists('getBadgeStyle')) {
        function getBadgeStyle($isActive, $color = 'primary')
        {
            $brandingColor =
                $color === 'secondary' ? config('branding.colors.secondary') : config('branding.colors.primary');

            if ($isActive) {
                // Active: white background with branding color text
                return "background-color: white; color: {$brandingColor};";
            } else {
                // Inactive: branding color background with white text
                return "background-color: {$brandingColor}; color: white;";
            }
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

    // Get admin roles count (filtered by country + system roles)
    $admin_roles_count = 0;
    $admins_count = 0;
    try {
        // Get country code from route parameter (more reliable than session when switching countries)
        $countryCode = request()->route('countryCode') ?? session('country_code');
        $countryCode = strtoupper($countryCode);
        $currentCountryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');

        // Build query matching exactly what RoleAction does for admin roles
        $rolesQuery = \App\Models\Role::where('type', 'admin');

        // Apply user type scope based on current user
        $userTypeId = auth()->user()->user_type_id ?? null;
        switch ($userTypeId) {
            case \App\Models\UserType::SUPER_ADMIN_TYPE:
                $rolesQuery->superAdminShowRoles();
                break;
            case \App\Models\UserType::ADMIN_TYPE:
                $rolesQuery->adminShowRoles();
                break;
            case \App\Models\UserType::VENDOR_TYPE:
            case \App\Models\UserType::VENDOR_USER_TYPE:
                $rolesQuery->vendorShowRoles();
                break;
        }

        // Filter by country
        if ($currentCountryId) {
            $rolesQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)
                  ->orWhereNull('country_id');
            });
        }

        $admin_roles_count = $rolesQuery->count();

        // Get admins count (filtered by country + system admins)
        $adminsQuery = \App\Models\User::where('id', '!=', auth()->id());

        // Apply user type scope based on current user
        switch ($userTypeId) {
            case \App\Models\UserType::SUPER_ADMIN_TYPE:
                $adminsQuery->superAdminShow();
                break;
            case \App\Models\UserType::ADMIN_TYPE:
                $adminsQuery->adminShow();
                break;
            case \App\Models\UserType::VENDOR_TYPE:
                $adminsQuery->vendorShow();
                break;
            case \App\Models\UserType::VENDOR_USER_TYPE:
                $adminsQuery->otherShow();
                break;
        }

        // Filter by country
        if ($currentCountryId) {
            $adminsQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)
                  ->orWhereNull('country_id');
            });
        }

        $admins_count = $adminsQuery->count();

        // Get vendor user roles count (filtered by country + system roles)
        $vendorUserRolesQuery = \App\Models\Role::where('type', 'vendor_user');

        // Apply user type scope based on current user
        switch ($userTypeId) {
            case \App\Models\UserType::SUPER_ADMIN_TYPE:
                $vendorUserRolesQuery->superAdminShowRoles();
                break;
            case \App\Models\UserType::ADMIN_TYPE:
                $vendorUserRolesQuery->adminShowRoles();
                break;
            case \App\Models\UserType::VENDOR_TYPE:
            case \App\Models\UserType::VENDOR_USER_TYPE:
                $vendorUserRolesQuery->vendorShowRoles();
                // Filter by vendor_id for vendor users
                if (auth()->user()->isVendor()) {
                    $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                    if ($vendor) {
                        $vendorUserRolesQuery->where(function($q) use ($vendor) {
                            $q->where('vendor_id', $vendor->id)
                              ->orWhereNull('vendor_id');
                        });
                    }
                }
                break;
        }

        // Filter by country
        if ($currentCountryId) {
            $vendorUserRolesQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)
                  ->orWhereNull('country_id');
            });
        }

        $vendor_user_roles_count = $vendorUserRolesQuery->count();

        // Get vendor users count (filtered by country)
        $vendorUsersQuery = \App\Models\User::where('user_type_id', \App\Models\UserType::VENDOR_USER_TYPE)
            ->where('id', '!=', auth()->id());

        // If current user is vendor, filter by their vendor
        if (auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $vendorUsersQuery->where('vendor_id', $vendor->id);
            }
        }

        // Filter by country
        if ($currentCountryId) {
            $vendorUsersQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)
                  ->orWhereNull('country_id');
            });
        }

        $vendor_users_count = $vendorUsersQuery->count();
    } catch (\Exception $e) {
        $admin_roles_count = 0;
        $admins_count = 0;
        $vendor_user_roles_count = 0;
        $vendor_users_count = 0;
    }
@endphp
<div class="sidebar__menu-group">
    <ul class="sidebar_nav">
        @can('dashboard.view')
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ isMenuActive('admin.dashboard', $currentRoute) ? 'active' : '' }}">
                <span class="nav-icon uil uil-create-dashboard"></span>
                <span class="menu-text">{{ trans('menu.dashboard.title') }}</span>
            </a>
        </li>
        @endcan


        @canany(['withdraw.send_money.create', 'withdraw.transactions.view', 'withdraw.my_transactions.view', 'withdraw.request.create', 'withdraw.vendor_requests.new.view'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.withdraw module') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.sendMoney', 'admin.allTransactions', 'admin.allVendorsTransactions', 'admin.sendMoneyRequest', 'admin.transactionsRequests'], ['admin/send-money*', 'admin/transactions*', 'admin/withdraw*', 'admin/vendors-transactions*', 'admin/trasnactions-requests*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.sendMoney', 'admin.allTransactions', 'admin.allVendorsTransactions', 'admin.sendMoneyRequest', 'admin.transactionsRequests'], ['admin/send-money*', 'admin/transactions*', 'admin/withdraw*', 'admin/vendors-transactions*', 'admin/trasnactions-requests*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-sitemap"></span>
                    <span class="menu-text">{{ trans('menu.withdraw module.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('withdraw.send_money.create')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.sendMoney', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.sendMoney') }}">
                                {{ trans('menu.withdraw module.send money') }}
                            </a>
                        </li>
                    @endcan

                    @can('withdraw.transactions.view')
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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.allTransactions', $currentRoute)) }}">{{ $all_transactions }}</span>
                            </a>
                        </li>
                    @endcan

                    @if(isVendor())
                        @can('withdraw.request.create')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.sendMoneyRequest', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.sendMoneyRequest') }}">
                                    {{ trans('menu.withdraw module.send money request') }}
                                </a>
                            </li>
                        @endcan
                    @endif

                    {{-- Vendor view: my transactions (only for vendor users) --}}
                    @if(isVendor())
                        @can('withdraw.my_transactions.view')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new' ? 'active' : '' }}"
                                    href="{{ route('admin.transactionsRequests', ['status' => 'new']) }}">
                                    {{ trans('menu.withdraw module.my_new_requests') }}
                                    <span class="badge badge-round ms-1"
                                        style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new') }}">{{ $new_transactions }}</span>
                                </a>
                            </li>
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted' ? 'active' : '' }}"
                                    href="{{ route('admin.transactionsRequests', ['status' => 'accepted']) }}">
                                    {{ trans('menu.withdraw module.my_accepted_requests') }}
                                    <span class="badge badge-round ms-1"
                                        style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted') }}">{{ $accepted_transactions }}</span>
                                </a>
                            </li>
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected' ? 'active' : '' }}"
                                    href="{{ route('admin.transactionsRequests', ['status' => 'rejected']) }}">
                                    {{ trans('menu.withdraw module.my_rejected_requests') }}
                                    <span class="badge badge-round ms-1"
                                        style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected', 'secondary') }}">{{ $rejected_transactions }}</span>
                                </a>
                            </li>
                        @endcan
                    @endif

                    {{-- Admin view: vendor requests management --}}
                    @can('withdraw.vendor_requests.new.view')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new' ? 'active' : '' }}"
                                href="{{ route('admin.transactionsRequests', ['status' => 'new']) }}">
                                {{ trans('menu.withdraw module.new transaction requests') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new') }}">{{ $new_transactions }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('withdraw.vendor_requests.accepted.view')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted' ? 'active' : '' }}"
                                href="{{ route('admin.transactionsRequests', ['status' => 'accepted']) }}">
                                {{ trans('menu.withdraw module.accepted transaction requests') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted') }}">{{ $accepted_transactions }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('withdraw.vendor_requests.rejected.view')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected' ? 'active' : '' }}"
                                href="{{ route('admin.transactionsRequests', ['status' => 'rejected']) }}">
                                {{ trans('menu.withdraw module.rejected transaction requests') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected', 'secondary') }}">{{ $rejected_transactions }}</span>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcanany

        @can('request-quotations.index')
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.request_quotations') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.request-quotations.index', 'admin.request-quotations.archived'], ['admin/request-quotations*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.request-quotations.index', 'admin.request-quotations.archived'], ['admin/request-quotations*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-file-question-alt"></span>
                    <span class="menu-text">{{ trans('menu.request_quotations.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.request-quotations.index', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.request-quotations.index') }}">
                            {{ trans('menu.request_quotations.all_requests') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.request-quotations.index', $currentRoute)) }}">
                                {{ \Modules\Order\app\Models\RequestQuotation::notArchived()->count() }}
                            </span>
                        </a>
                    </li>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.request-quotations.archived', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.request-quotations.archived') }}">
                            {{ trans('menu.request_quotations.archived_requests') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.request-quotations.archived', $currentRoute)) }}">
                                {{ \Modules\Order\app\Models\RequestQuotation::archived()->count() }}
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @canany(['push-notifications.index', 'push-notifications.create'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.push_notifications') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.system-settings.push-notifications.index', 'admin.system-settings.push-notifications.create'], ['admin/system-settings/push-notifications*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.system-settings.push-notifications.index', 'admin.system-settings.push-notifications.create'], ['admin/system-settings/push-notifications*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-bell"></span>
                    <span class="menu-text">{{ trans('menu.push_notifications.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('push-notifications.create')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.push-notifications.create', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.system-settings.push-notifications.create') }}">
                            {{ trans('menu.push_notifications.send_notification') }}
                        </a>
                    </li>
                    @endcan
                    @can('push-notifications.index')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.push-notifications.index', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.system-settings.push-notifications.index') }}">
                            {{ trans('menu.push_notifications.all_notifications') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.system-settings.push-notifications.index', $currentRoute)) }}">
                                {{ \Modules\SystemSetting\app\Models\PushNotification::count() }}
                            </span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['accounting.summary.index', 'accounting.balances.index', 'accounting.expense-items.index', 'accounting.expenses.index', 'accounting.income.index'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.financials') }}</span>
            </li>
            <li
                class="has-child {{ isParentMenuOpen(['admin.accounting.summary', 'admin.accounting.income', 'admin.accounting.balances', 'admin.accounting.expenses', 'admin.accounting.expense-items'], ['admin/accounting*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.accounting.summary', 'admin.accounting.income', 'admin.accounting.balances', 'admin.accounting.expenses', 'admin.accounting.expense-items'], ['admin/accounting*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-invoice"></span>
                    <span class="menu-text">{{ trans('menu.accounting module.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('accounting.summary.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.summary', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.summary') }}">{{ trans('menu.accounting module.overview') }}</a>
                        </li>
                    @endcan
                    @can('accounting.balances.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.balances', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.balances') }}">{{ trans('menu.accounting module.vendor_balances') }}</a>
                        </li>
                    @endcan
                    @can('accounting.expense-items.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.expense-items', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.expense-items') }}">{{ trans('menu.accounting module.expense_categories') }}</a>
                        </li>
                    @endcan
                    @can('accounting.expenses.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.expenses', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.expenses') }}">{{ trans('menu.accounting module.expense_records') }}</a>
                        </li>
                    @endcan
                    @can('accounting.income.index')
                        <li><a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.accounting.income', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.accounting.income') }}">{{ trans('menu.accounting module.income_entries') }}</a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['departments.index', 'categories.index', 'sub-categories.index'])
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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.category-management.departments.index', $currentRoute)) }}">
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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.category-management.categories.index', $currentRoute)) }}">
                                    {{ \Modules\CategoryManagment\app\Models\Category::count() }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('sub-categories.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.category-management.subcategories.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.category-management.subcategories.index') }}">
                                {{ trans('menu.category managment.sub category') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.category-management.subcategories.index', $currentRoute)) }}">
                                    {{ \Modules\CategoryManagment\app\Models\SubCategory::count() }}
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany([
            'products.index', 'products.bank',
            'variant-keys.index', 'variant-keys.create',
            'variants-configurations.index', 'variants-configurations.create',
        ])
            <li
                class="has-child {{ isParentMenuOpen(['admin.products.index', 'admin.products.pending', 'admin.products.rejected', 'admin.products.accepted', 'admin.products.create', 'admin.products.show', 'admin.products.edit', 'admin.products.bank', 'admin.products.stock-setup', 'admin.variant-keys.index', 'admin.variants-configurations.index'], ['admin/products*', 'admin/variant*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.products.index', 'admin.products.pending', 'admin.products.rejected', 'admin.products.accepted', 'admin.products.create', 'admin.products.show', 'admin.products.edit', 'admin.products.bank', 'admin.products.stock-setup', 'admin.variant-keys.index', 'admin.variants-configurations.index'], ['admin/products*', 'admin/variant*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-box"></span>
                    <span class="menu-text">{{ trans('menu.products.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @if(isAdmin())
                        @can('products.stock-setup')
                            <li>
                                <a class="fw-bold {{ isMenuActive('admin.products.stock-setup', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.products.stock-setup') }}">
                                    {{ trans('menu.products.stock_setup') }}
                                </a>
                            </li>
                        @endcan
                    @endif
                    @can('products.bank')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.products.bank', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.products.bank') }}">
                                {{ trans('menu.products.bank_products') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.bank', $currentRoute)) }}">
                                    @php
                                        $bankProductsQuery = \Modules\CatalogManagement\app\Models\Product::where('type', 'bank');
                                        
                                        // Filter by vendor's departments if user is a vendor
                                        if (isVendor()) {
                                            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById ?? auth()->user()->vendor;
                                            if ($vendor) {
                                                $departmentIds = $vendor->departments()->pluck('departments.id')->toArray();
                                                if (!empty($departmentIds)) {
                                                    $bankProductsQuery->whereIn('department_id', $departmentIds);
                                                } else {
                                                    $bankProductsQuery->whereRaw('1 = 0');
                                                }
                                            }
                                        }
                                        
                                        $bankProductsCount = $bankProductsQuery->count();
                                    @endphp
                                    {{ $bankProductsCount }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('products.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.products.index', 'admin.products.create', 'admin.products.show', 'admin.products.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.products.index') }}">
                                {{ trans('menu.products.all_products') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.products.index', 'admin.products.create', 'admin.products.show', 'admin.products.edit'], $currentRoute)) }}">
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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.pending', $currentRoute)) }}">
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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.rejected', $currentRoute), 'secondary') }}">
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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.products.accepted', $currentRoute)) }}">
                                    @if (isAdmin())
                                        {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->count() }}
                                    @else
                                        {{ \Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                                    @endif
                                </span>
                            </a>
                        </li>

                    @endcan

                    @canany(['variant-keys.index', 'variant-keys.create'])
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.variant-keys.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.variant-keys.index') }}">
                                {{ trans('menu.variant configurations.variant config keys') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.variant-keys.index', $currentRoute)) }}">{{ \Modules\CatalogManagement\app\Models\VariantConfigurationKey::count() }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['variants-configurations.index', 'variants-configurations.create'])
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.variants-configurations.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.variants-configurations.index') }}">
                                {{ trans('menu.variant configurations.variant config') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.variants-configurations.index', $currentRoute)) }}">{{ \Modules\CatalogManagement\app\Models\VariantsConfiguration::count() }}</span>
                            </a>
                        </li>
                    @endcanany
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-catalog.index', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.system-catalog.index') }}">
                            {{ trans('menu.system_catalog.title') }}
                        </a>
                    </li>
                </ul>
            </li>
        @endcanany


        {{-- Bundles Menu --}}
        @canany(['bundle-categories.index', 'bundles.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit', 'admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], ['admin/bundle-categories*', 'admin/bundles*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit', 'admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], ['admin/bundle-categories*', 'admin/bundles*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-package"></span>
                    <span class="menu-text">{{ trans('menu.bundles.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('bundle-categories.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.bundle-categories.index') }}">
                                {{ trans('menu.bundles.bundle_categories') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit'], $currentRoute)) }}">
                                    {{ \Modules\CatalogManagement\app\Models\BundleCategory::count() }}
                                </span>
                            </a>
                        </li>
                    @endcan
                    @can('bundles.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.bundles.index') }}">
                                {{ trans('menu.bundles.all_bundles') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], $currentRoute)) }}">
                                    @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                        {{ \Modules\CatalogManagement\app\Models\Bundle::count() }}
                                    @else
                                        {{ \Modules\CatalogManagement\app\Models\Bundle::where('vendor_id', auth()->user()->vendor->id ?? 0)->count() }}
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        {{-- Taxes Menu --}}
        @can('taxes.index')
            <li
                class="{{ isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute) ? 'active' : '' }}">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.taxes.index') }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-bill"></span>
                        <span class="menu-text">{{ trans('menu.taxes.title') }}</span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute)) }}">
                        {{ \Modules\CatalogManagement\app\Models\Tax::count() }}
                    </span>
                </a>
            </li>
        @endcan

        {{-- Occasions Menu (Admin Only) --}}
        @if(isAdmin())
        @can('occasions.index')
            <li
                class="{{ isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute) ? 'active' : '' }}">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.occasions.index') }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-calendar-alt"></span>
                        <span class="menu-text">{{ trans('menu.occasions') }}</span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute)) }}">
                        @php
                            $occasions_count = \Modules\CatalogManagement\app\Models\Occasion::count();
                        @endphp
                        {{ $occasions_count }}
                    </span>
                </a>
            </li>
        @endcan
        @endif
        @can('product-reviews.index')
            @php
                try {
                    $reviews_all = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\CatalogManagement\app\Models\VendorProduct::class)->count();
                    $reviews_accepted = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\CatalogManagement\app\Models\VendorProduct::class)->where(
                        'status',
                        'approved',
                    )->count();
                    $reviews_rejected = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\CatalogManagement\app\Models\VendorProduct::class)->where(
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
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'all') }}">{{ $reviews_all }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'approved']) }}">
                            {{ trans('menu.product_reviews.accepted') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'approved') }}">{{ $reviews_accepted }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}">
                            {{ trans('menu.product_reviews.rejected') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'rejected', 'secondary') }}">{{ $reviews_rejected }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('brands.index')
            <li>
                <a href="{{ route('admin.brands.index') }}"
                    class="{{ isMenuActive(['admin.brands.index', 'admin.brands.create', 'admin.brands.show', 'admin.brands.edit'], $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-ticket"></span>
                            <span class="menu-text fw-bold">{{ trans('menu.brands.title') }}</span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive(['admin.brands.index', 'admin.brands.create', 'admin.brands.show', 'admin.brands.edit'], $currentRoute)) }}">{{ \Modules\CatalogManagement\app\Models\Brand::count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        @can('promocodes.index')
            <li>
                <a href="{{ route('admin.promocodes.index') }}"
                    class="{{ isMenuActive(['admin.promocodes.index', 'admin.promocodes.create', 'admin.promocodes.edit', 'admin.promocodes.show'], $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-ticket"></span>
                            <span class="menu-text">{{ trans('menu.promocodes.title') }}</span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive(['admin.promocodes.index', 'admin.promocodes.create', 'admin.promocodes.edit', 'admin.promocodes.show'], $currentRoute)) }}">{{ \Modules\CatalogManagement\app\Models\Promocode::count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        @canany(['points-settings.index', 'points-settings.user-points.index'])
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
                    @can('points-settings.index')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.points-settings.index'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.points-settings.index') }}">
                            {{ trans('menu.point managment.title') }}
                        </a>
                    </li>
                    @endcan
                    @can('points-settings.user-points.index')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.points-settings.user-points.index') }}">
                            {{ trans('menu.point managment.users points') }}
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['admin-roles.index', 'admins.index', 'vendor-user-roles.index', 'vendor-users.index'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.user management') }}</span>
            </li>
        @endcanany
        {{-- Admin Management --}}
        @canany(['admin-roles.index', 'admins.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.admin-management.roles.index', 'admin.admin-management.admins.index'], ['admin/admin-management*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.admin-management.roles.index', 'admin.admin-management.admins.index'], ['admin/admin-management*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-user-check"></span>
                    <span class="menu-text">{{ trans('menu.admin managment.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('admin-roles.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.admin-management.roles.index', 'admin.admin-management.roles.create', 'admin.admin-management.roles.show', 'admin.admin-management.roles.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.admin-management.roles.index') }}">
                                {{ trans('menu.admin managment.roles managment') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.admin-management.roles.index', 'admin.admin-management.roles.create', 'admin.admin-management.roles.show', 'admin.admin-management.roles.edit'], $currentRoute)) }}">
                                    {{ $admin_roles_count }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('admins.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.admin-management.admins.index', 'admin.admin-management.admins.create', 'admin.admin-management.admins.show', 'admin.admin-management.admins.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.admin-management.admins.index') }}">
                                {{ trans('menu.admin managment.admin managment') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.admin-management.admins.index', 'admin.admin-management.admins.create', 'admin.admin-management.admins.show', 'admin.admin-management.admins.edit'], $currentRoute)) }}">
                                    {{ $admins_count }}
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany
        {{-- Vendor Users Management --}}
        @canany(['vendor-user-roles.index', 'vendor-users.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.vendor-users.index'], ['admin/vendor-users-management*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.vendor-users.index'], ['admin/vendor-users-management*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-users-alt"></span>
                    <span class="menu-text">{{ trans('menu.admin managment.vendor users management') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('vendor-user-roles.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.roles.create', 'admin.vendor-users-management.roles.show', 'admin.vendor-users-management.roles.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.vendor-users-management.roles.index') }}">
                                {{ trans('menu.admin managment.vendor users roles management') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.roles.create', 'admin.vendor-users-management.roles.show', 'admin.vendor-users-management.roles.edit'], $currentRoute)) }}">
                                    {{ $vendor_user_roles_count }}
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('vendor-users.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.vendor-users-management.vendor-users.index', 'admin.vendor-users-management.vendor-users.create', 'admin.vendor-users-management.vendor-users.show', 'admin.vendor-users-management.vendor-users.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.vendor-users-management.vendor-users.index') }}">
                                {{ trans('menu.admin managment.vendor users management') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.vendor-users-management.vendor-users.index', 'admin.vendor-users-management.vendor-users.create', 'admin.vendor-users-management.vendor-users.show', 'admin.vendor-users-management.vendor-users.edit'], $currentRoute)) }}">
                                    {{ $vendor_users_count }}
                                </span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany


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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.vendors.index', 'admin.vendors.show', 'admin.vendors.edit'], $currentRoute)) }}">
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

        @can('vendor-reviews.index')
            @php
                try {
                    $vendor_reviews_all = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->count();
                    $vendor_reviews_accepted = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->where('status', 'approved')->count();
                    $vendor_reviews_rejected = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->where('status', 'rejected')->count();
                } catch (\Exception $e) {
                    $vendor_reviews_all = $vendor_reviews_accepted = $vendor_reviews_rejected = 0;
                }
            @endphp
            <li class="has-child {{ isParentMenuOpen(['admin.vendor-reviews.index'], ['admin/vendor-reviews*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.vendor-reviews.index'], ['admin/vendor-reviews*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-star"></span>
                    <span class="menu-text">{{ trans('menu.vendor_reviews.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'all' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-reviews.index', ['status' => 'all']) }}">
                            {{ trans('menu.vendor_reviews.all') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'all') }}">{{ $vendor_reviews_all }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-reviews.index', ['status' => 'approved']) }}">
                            {{ trans('menu.vendor_reviews.accepted') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'approved') }}">{{ $vendor_reviews_accepted }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-reviews.index', ['status' => 'rejected']) }}">
                            {{ trans('menu.vendor_reviews.rejected') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'rejected', 'secondary') }}">{{ $vendor_reviews_rejected }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan


        @can('vendor-requests.index')
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
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status')) }}">{{ \Modules\Vendor\app\Models\VendorRequest::count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status') ? 'active' : '' }}"
                            href="{{ route('admin.vendor-requests.index') }}">
                            {{ trans('menu.become a vendor requests.new') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status')) }}">{{ \Modules\Vendor\app\Models\VendorRequest::pending()->count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') === 'approved' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-requests.index') }}?status=approved">
                            {{ trans('menu.become a vendor requests.accepted') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(request()->get('status') === 'approved') }}">{{ \Modules\Vendor\app\Models\VendorRequest::approved()->count() }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') === 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.vendor-requests.index') }}?status=rejected">
                            {{ trans('menu.become a vendor requests.rejected') }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(request()->get('status') === 'rejected', 'secondary') }}">{{ \Modules\Vendor\app\Models\VendorRequest::rejected()->count() }}</span>
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
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.customers.index', $currentRoute)) }}">
                                @php
                                    $customerCount = 0;
                                    if (isAdmin()) {
                                        $customerCount = \Modules\Customer\app\Models\Customer::count();
                                    } else {
                                        $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                        if ($vendor) {
                                            // Vendors see: system customers (vendor_id = NULL) + their own customers (vendor_id = their vendor ID)
                                            $customerCount = \Modules\Customer\app\Models\Customer::where(function($q) use ($vendor) {
                                                $q->whereNull('vendor_id')
                                                  ->orWhere('vendor_id', $vendor->id);
                                            })->count();
                                        }
                                    }
                                @endphp
                                {{ $customerCount }}
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @canany(['orders.index', 'orders.create'])
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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.orders.index', $currentRoute) && !request()->has('stage')) }}">
                                    @php
                                        $allOrdersCount = 0;
                                        if (isAdmin()) {
                                            $allOrdersCount = \Modules\Order\app\Models\Order::count();
                                        } else {
                                            $orderVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                            if ($orderVendor) {
                                                $allOrdersCount = \Modules\Order\app\Models\Order::whereHas('products', function($q) use ($orderVendor) {
                                                    $q->where('vendor_id', $orderVendor->id);
                                                })->count();
                                            }
                                        }
                                    @endphp
                                    {{ $allOrdersCount }}
                                </span>
                            </a>
                        </li>
                        @can('orders.create')
                            <li class="l_sidebar">
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.orders.create', $currentRoute) ? 'active' : '' }}"
                                    href="{{ route('admin.orders.create') }}">
                                    {{ trans('menu.orders.create') }}
                                </a>
                            </li>
                        @endcan

                        {{-- Order Stages --}}
                        @php
                            $countryCode = session('country_code');
                            $countryId = $countryCode
                                ? \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id')
                                : null;

                            $currentLocale = app()->getLocale();
                            $currentLangId = \App\Models\Language::where('code', $currentLocale)->value('id');

                            $orderStages = \Modules\Order\app\Models\OrderStage::withoutCountryFilter()
                            ->with(['translations' => function($query) use ($currentLangId) {
                                $query->where('lang_id', $currentLangId);
                            }])
                            ->where(function ($q) use ($countryId) {
                                $q->where(function ($sub) {
                                    $sub->whereNull('country_id')->where('is_system', 1);
                                });

                                if ($countryId) {
                                    $q->orWhere('country_id', $countryId);
                                }
                            })
                                ->orderBy('sort_order')
                                ->get();

                            // Get vendor for stage counts
                            $stageVendor = null;
                            if (!isAdmin()) {
                                $stageVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                            }
                        @endphp
                        @foreach ($orderStages as $stage)
                            <li class="l_sidebar">
                                <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('stage') == $stage->id ? 'active' : '' }}"
                                    href="{{ route('admin.orders.index', ['stage' => $stage->id]) }}">
                                    {{ $stage->translations->where('lang_key', 'name')->first()?->lang_value ?? $stage->slug }}
                                    <span class="badge badge-round ms-1"
                                        style="{{ getBadgeStyle(request()->get('stage') == $stage->id) }}">
                                        @php
                                            $stageOrderCount = 0;
                                            if (isAdmin()) {
                                                $stageOrderCount = \Modules\Order\app\Models\Order::where('stage_id', $stage->id)->count();
                                            } elseif ($stageVendor) {
                                                $stageOrderCount = \Modules\Order\app\Models\Order::where('stage_id', $stage->id)
                                                    ->whereHas('products', function($q) use ($stageVendor) {
                                                        $q->where('vendor_id', $stageVendor->id);
                                                    })->count();
                                            }
                                        @endphp
                                        {{ $stageOrderCount }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
        @endcanany

        @can('order-stages.index')
            @php
                // Define orderStages if not already defined (when orders.index permission is not granted)
                if (!isset($orderStages)) {
                    $countryCode = session('country_code');
                    $countryId = $countryCode
                        ? \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id')
                        : null;

                    $currentLocale = app()->getLocale();
                    $currentLangId = \App\Models\Language::where('code', $currentLocale)->value('id');

                    $orderStages = \Modules\Order\app\Models\OrderStage::withoutCountryFilter()
                    ->with(['translations' => function($query) use ($currentLangId) {
                        $query->where('lang_id', $currentLangId);
                    }])
                    ->where(function ($q) use ($countryId) {
                        $q->where(function ($sub) {
                            $sub->whereNull('country_id')->where('is_system', 1);
                        });

                        if ($countryId) {
                            $q->orWhere('country_id', $countryId);
                        }
                    })
                        ->orderBy('sort_order')
                        ->get();
                }
            @endphp
            <li>
                <a href="{{ route('admin.order-stages.index') }}"
                    class="{{ isMenuActive('admin.order-stages.index', $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-process"></span>
                            <span class="menu-text">{{ trans('menu.orders.order stages') }}</span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.order-stages.index', $currentRoute)) }}">{{ $orderStages->count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        @can('shippings.index')
            <li>
                <a href="{{ route('admin.shippings.index') }}"
                    class="{{ isMenuActive('admin.shippings.index', $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-truck"></span>
                            <span class="menu-text">{{ trans('menu.orders.shipping methods') }}</span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.shippings.index', $currentRoute)) }}">{{ \Modules\Order\app\Models\Shipping::count() }}</span>
                    </span>
                </a>
            </li>
        @endcan

        <li
            class="has-child {{ isParentMenuOpen(['admin.refunds.index', 'admin.refunds.settings'], ['admin/refunds*']) ? 'open' : '' }}">
            <a href="#"
                class="{{ isParentMenuOpen(['admin.refunds.index', 'admin.refunds.settings'], ['admin/refunds*']) ? 'active' : '' }}">
                <span class="nav-icon uil uil-redo"></span>
                <span class="menu-text">{{ trans('menu.refunds.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                {{-- All Refunds --}}
                <li class="l_sidebar">
                    <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.refunds.index', $currentRoute) && !request()->has('status') ? 'active' : '' }}"
                        href="{{ route('admin.refunds.index') }}">
                        {{ trans('menu.refunds.all') }}
                        <span class="badge badge-round ms-1"
                            style="{{ getBadgeStyle(isMenuActive('admin.refunds.index', $currentRoute) && !request()->has('status')) }}">
                            @php
                                $allRefundsCount = 0;
                                try {
                                    if (isAdmin()) {
                                        $allRefundsCount = \Modules\Refund\app\Models\RefundRequest::count();
                                    } else {
                                        $refundVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                        if ($refundVendor) {
                                            $allRefundsCount = \Modules\Refund\app\Models\RefundRequest::where('vendor_id', $refundVendor->id)->count();
                                        }
                                    }
                                } catch (\Exception $e) {
                                    $allRefundsCount = 0;
                                }
                            @endphp
                            {{ $allRefundsCount }}
                        </span>
                    </a>
                </li>

                {{-- Dynamic Status Menu Items --}}
                @php
                    $refundStatuses = \Modules\Refund\app\Models\RefundRequest::STATUSES;
                @endphp

                @foreach($refundStatuses as $statusKey => $statusLabel)
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ request()->get('status') == $statusKey ? 'active' : '' }}"
                            href="{{ route('admin.refunds.index', ['status' => $statusKey]) }}">
                            {{ trans('refund::refund.statuses.' . $statusKey) }}
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(request()->get('status') == $statusKey, $statusKey == 'rejected' ? 'secondary' : 'primary') }}">
                                @php
                                    $statusCount = 0;
                                    try {
                                        if (isAdmin()) {
                                            $statusCount = \Modules\Refund\app\Models\RefundRequest::where('status', $statusKey)->count();
                                        } else {
                                            $refundVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                            if ($refundVendor) {
                                                $statusCount = \Modules\Refund\app\Models\RefundRequest::where('vendor_id', $refundVendor->id)->where('status', $statusKey)->count();
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        $statusCount = 0;
                                    }
                                @endphp
                                {{ $statusCount }}
                            </span>
                        </a>
                    </li>
                @endforeach

                {{-- Settings (Admin Only) --}}
                @if(isAdmin())
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.refunds.admin-settings.index', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.refunds.admin-settings.index') }}">
                            {{ trans('menu.refunds.admin_settings') }}
                        </a>
                    </li>
                @endif
                
                {{-- Vendor Settings --}}
                @if(!isAdmin())
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.refunds.settings', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.refunds.settings') }}">
                            {{ trans('menu.refunds.settings') }}
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        @canany(['blog-categories.index', 'blogs.index'])
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
                    @can('blog-categories.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.blog-categories.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.system-settings.blog-categories.index') }}">
                                {{ trans('menu.blog managment.categories') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.system-settings.blog-categories.index', $currentRoute)) }}">
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
                    @endcan
                    @can('blogs.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.system-settings.blogs.index', $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.system-settings.blogs.index') }}">
                                {{ trans('menu.blog managment.blogs') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive('admin.system-settings.blogs.index', $currentRoute)) }}">
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
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('messages.index')
            <li class="{{ isMenuActive('admin.messages.index', $currentRoute) ? 'active' : '' }}">
                <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.messages.index', $currentRoute) ? 'active' : '' }}"
                    href="{{ route('admin.messages.index') }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-envelope"></span>
                        <span class="menu-text">{{ __('systemsetting::messages.messages') }}</span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive('admin.messages.index', $currentRoute)) }}">
                        {{ \Modules\SystemSetting\app\Models\Message::count() }}
                    </span>
                </a>
            </li>
        @endcan

        @canany(['reports.registered_users.view', 'reports.area_users.view', 'reports.orders.view', 'reports.products.view', 'reports.points.view'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.reports') }}</span>
            </li>
            <li class="has-child {{ isParentMenuOpen(['admin.reports.index', 'admin.reports.registered-users', 'admin.reports.area-users', 'admin.reports.orders', 'admin.reports.products', 'admin.reports.points'], ['admin/reports*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.reports.index', 'admin.reports.registered-users', 'admin.reports.area-users', 'admin.reports.orders', 'admin.reports.products', 'admin.reports.points'], ['admin/reports*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-chart-line"></span>
                    <span class="menu-text">{{ trans('menu.reports.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('reports.registered_users.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.registered-users', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.registered-users') }}">
                            {{ trans('menu.reports.registerd users') }}
                        </a>
                    </li>
                    @endcan
                    @can('reports.area_users.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.area-users', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.area-users') }}">
                            {{ trans('menu.reports.area users') }}
                        </a>
                    </li>
                    @endcan
                    @can('reports.orders.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.orders', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.orders') }}">
                            {{ trans('menu.reports.orders report') }}
                        </a>
                    </li>
                    @endcan
                    @can('reports.products.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.products', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.products') }}">
                            {{ trans('menu.reports.product report') }}
                        </a>
                    </li>
                    @endcan
                    @can('reports.points.view')
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive('admin.reports.points', $currentRoute) ? 'active' : '' }}"
                            href="{{ route('admin.reports.points') }}">
                            {{ trans('menu.reports.points report') }}
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['ads.index', 'features.index', 'footer-content.index', 'faqs.index',
            'sliders.index', 'site-information.index', 'return-policy.index', 'service-terms.index',
            'privacy-policy.index', 'terms-conditions.index', 'area.country.index', 'system.currency.index',
            'system_log.view'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.settings') }}</span>
            </li>
        @endcanany

        @can('ads.index')
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
                            <span class="badge badge-round ms-1"
                                style="{{ getBadgeStyle(isMenuActive('admin.system-settings.ads.index', $currentRoute)) }}">
                                {{ \Modules\SystemSetting\app\Models\Ad::count() }}
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        {{-- Frontend Settings --}}
        @canany(['features.index', 'footer-content.index', 'faqs.index', 'sliders.index'])
            <li
                class="has-child {{ isParentMenuOpen(['admin.system-settings.features.index', 'admin.system-settings.footer-content.index', 'admin.system-settings.faqs.index', 'admin.system-settings.sliders.index', 'admin.system-settings.site-information.index'], ['*/system-settings/features*', '*/system-settings/footer-content*', '*/system-settings/faqs*', '*/system-settings/sliders*', '*/system-settings/site-information*']) ? 'open' : '' }}">
                <a href="#"
                    class="{{ isParentMenuOpen(['admin.system-settings.features.index', 'admin.system-settings.footer-content.index', 'admin.system-settings.faqs.index', 'admin.system-settings.sliders.index', 'admin.system-settings.site-information.index'], ['*/system-settings/features*', '*/system-settings/footer-content*', '*/system-settings/faqs*', '*/system-settings/sliders*', '*/system-settings/site-information*']) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-window-section"></span>
                    <span class="menu-text fw-bold">{{ trans('menu.frontend settings.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('features.index')
                        <li>
                            <a href="{{ route('admin.system-settings.features.index') }}"
                                class="{{ isMenuActive('admin.system-settings.features.index', $currentRoute) ? 'active' : '' }}">
                                <span class="nav-icon uil uil-star"></span>
                                <span class=" fw-bold">{{ trans('menu.frontend settings.our features') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('footer-content.index')
                        <li>
                            <a href="{{ route('admin.system-settings.footer-content.index') }}"
                                class="{{ isMenuActive('admin.system-settings.footer-content.index', $currentRoute) ? 'active' : '' }}">
                                <span class="nav-icon uil uil-align-center-alt"></span>
                                <span class=" fw-bold">{{ trans('menu.frontend settings.footer content') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('faqs.index')
                        <li>
                            <a href="{{ route('admin.system-settings.faqs.index') }}"
                                class="{{ isMenuActive(['admin.system-settings.faqs.index', 'admin.system-settings.faqs.create', 'admin.system-settings.faqs.edit', 'admin.system-settings.faqs.show'], $currentRoute) ? 'active' : '' }}">
                                <span class="nav-icon uil uil-question-circle"></span>
                                <span class=" fw-bold">{{ trans('menu.frontend settings.faq management') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('sliders.index')
                        <li>
                            <a href="{{ route('admin.system-settings.sliders.index') }}"
                                class="{{ isMenuActive(['admin.system-settings.sliders.index', 'admin.system-settings.sliders.create', 'admin.system-settings.sliders.edit', 'admin.system-settings.sliders.show'], $currentRoute) ? 'active' : '' }}">
                                <span class="nav-icon uil uil-image-v"></span>
                                <span class=" fw-bold">{{ trans('menu.frontend settings.sliders') }}</span>
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcanany

        {{-- Site Information --}}
        @canany(['site-information.index', 'about-us.index', 'return-policy.index', 'service-terms.index',
            'privacy-policy.index', 'terms-conditions.index'])
            <li class="has-child">
                <a href="#"
                    class="{{ isMenuActive(['admin.system-settings.site-information.index', 'admin.system-settings.about-us.website', 'admin.system-settings.about-us.mobile'], $currentRoute) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-info-circle"></span>
                    <span class="fw-bold">{{ trans('menu.frontend settings.site information') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('site-information.index')
                        <li>
                            <a href="{{ route('admin.system-settings.site-information.index') }}"
                                class="{{ isMenuActive('admin.system-settings.site-information.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.contact us') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('about-us.index')
                        <li>
                            <a href="{{ route('admin.system-settings.about-us.website') }}"
                                class="{{ isMenuActive('admin.system-settings.about-us.website', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.about us website') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.system-settings.about-us.mobile') }}"
                                class="{{ isMenuActive('admin.system-settings.about-us.mobile', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.about us mobile') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('return-policy.index')
                        <li>
                            <a href="{{ route('admin.system-settings.return-policy.index') }}"
                                class="{{ isMenuActive('admin.system-settings.return-policy.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.return policy') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('service-terms.index')
                        <li>
                            <a href="{{ route('admin.system-settings.service-terms.index') }}"
                                class="{{ isMenuActive('admin.system-settings.service-terms.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.service terms') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('privacy-policy.index')
                        <li>
                            <a href="{{ route('admin.system-settings.privacy-policy.index') }}"
                                class="{{ isMenuActive('admin.system-settings.privacy-policy.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.privacy policy') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('terms-conditions.index')
                        <li>
                            <a href="{{ route('admin.system-settings.terms-conditions.index') }}"
                                class="{{ isMenuActive('admin.system-settings.terms-conditions.index', $currentRoute) ? 'active' : '' }}">
                                <span class="fw-bold">{{ trans('menu.frontend settings.terms conditions') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

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
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.area-settings.countries.index', 'admin.area-settings.countries.create', 'admin.area-settings.countries.show', 'admin.area-settings.countries.edit'], $currentRoute)) }}">{{ \Modules\AreaSettings\app\Models\Country::count() }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('area.city.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.cities.index', 'admin.area-settings.cities.create', 'admin.area-settings.cities.show', 'admin.area-settings.cities.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.area-settings.cities.index') }}">
                                {{ trans('menu.area settings.city') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.area-settings.cities.index', 'admin.area-settings.cities.create', 'admin.area-settings.cities.show', 'admin.area-settings.cities.edit'], $currentRoute)) }}">{{ \Modules\AreaSettings\app\Models\City::count() }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('area.region.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.regions.index', 'admin.area-settings.regions.create', 'admin.area-settings.regions.show', 'admin.area-settings.regions.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.area-settings.regions.index') }}">
                                {{ trans('menu.area settings.region') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.area-settings.regions.index', 'admin.area-settings.regions.create', 'admin.area-settings.regions.show', 'admin.area-settings.regions.edit'], $currentRoute)) }}">{{ \Modules\AreaSettings\app\Models\Region::count() }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('area.subregion.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.area-settings.subregions.index', 'admin.area-settings.subregions.create', 'admin.area-settings.subregions.show', 'admin.area-settings.subregions.edit'], $currentRoute) ? 'active' : '' }}"
                                href="{{ route('admin.area-settings.subregions.index') }}">
                                {{ trans('menu.area settings.subregion') }}
                                <span class="badge badge-round ms-1"
                                    style="{{ getBadgeStyle(isMenuActive(['admin.area-settings.subregions.index', 'admin.area-settings.subregions.create', 'admin.area-settings.subregions.show', 'admin.area-settings.subregions.edit'], $currentRoute)) }}">{{ \Modules\AreaSettings\app\Models\SubRegion::count() }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('system.currency.index')
            <li>
                <a href="{{ route('admin.system-settings.currencies.index') }}"
                    class="d-flex align-items-center justify-content-between fw-bold {{ isMenuActive(['admin.system-settings.currencies.index', 'admin.system-settings.currencies.create', 'admin.system-settings.currencies.show', 'admin.system-settings.currencies.edit'], $currentRoute) ? 'active' : '' }}">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-dollar-alt"></span>
                        <span class="menu-text">{{ trans('menu.currencies.title') }}</span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="{{ getBadgeStyle(isMenuActive(['admin.system-settings.currencies.index', 'admin.system-settings.currencies.create', 'admin.system-settings.currencies.show', 'admin.system-settings.currencies.edit'], $currentRoute)) }}">
                        {{ \Modules\SystemSetting\app\Models\Currency::count() }}
                    </span>
                </a>
            </li>
        @endcan

        @can('system_log.view')
            <li>
                <a href="{{ route('admin.system-settings.activity-logs.index') }}"
                    class="{{ isMenuActive('admin.system-settings.activity-logs.index', $currentRoute) ? 'active' : '' }}">
                    <span class="nav-icon uil uil-history"></span>
                    <span class="menu-text">{{ trans('menu.system log.title') }}</span>
                </a>
            </li>
        @endcan

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
