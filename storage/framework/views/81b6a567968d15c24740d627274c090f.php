<?php
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
?>
<div class="sidebar__menu-group">
    <ul class="sidebar_nav">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.view')): ?>
        <li>
            <a href="<?php echo e(route('admin.dashboard')); ?>"
                class="<?php echo e(isMenuActive('admin.dashboard', $currentRoute) ? 'active' : ''); ?>">
                <span class="nav-icon uil uil-create-dashboard"></span>
                <span class="menu-text"><?php echo e(trans('menu.dashboard.title')); ?></span>
            </a>
        </li>
        <?php endif; ?>


        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['withdraw.send_money.create', 'withdraw.transactions.view', 'withdraw.my_transactions.view', 'withdraw.request.create', 'withdraw.vendor_requests.new.view'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.withdraw module')); ?></span>
            </li>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.sendMoney', 'admin.allTransactions', 'admin.allVendorsTransactions', 'admin.sendMoneyRequest', 'admin.transactionsRequests'], ['admin/send-money*', 'admin/transactions*', 'admin/withdraw*', 'admin/vendors-transactions*', 'admin/trasnactions-requests*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.sendMoney', 'admin.allTransactions', 'admin.allVendorsTransactions', 'admin.sendMoneyRequest', 'admin.transactionsRequests'], ['admin/send-money*', 'admin/transactions*', 'admin/withdraw*', 'admin/vendors-transactions*', 'admin/trasnactions-requests*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-sitemap"></span>
                    <span class="menu-text"><?php echo e(trans('menu.withdraw module.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('withdraw.send_money.create')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.sendMoney', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.sendMoney')); ?>">
                                <?php echo e(trans('menu.withdraw module.send money')); ?>

                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('withdraw.transactions.view')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.allVendorsTransactions', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.allVendorsTransactions')); ?>">
                                <?php echo e(trans('dashboard.vendors_transactions_overview')); ?>

                            </a>
                        </li>

                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.allTransactions', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.allTransactions')); ?>">
                                <?php echo e(trans('menu.withdraw module.all transactions')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.allTransactions', $currentRoute))); ?>"><?php echo e($all_transactions); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isVendor()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('withdraw.request.create')): ?>
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.sendMoneyRequest', $currentRoute) ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.sendMoneyRequest')); ?>">
                                    <?php echo e(trans('menu.withdraw module.send money request')); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isVendor()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('withdraw.my_transactions.view')): ?>
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.transactionsRequests', ['status' => 'new'])); ?>">
                                    <?php echo e(trans('menu.withdraw module.my_new_requests')); ?>

                                    <span class="badge badge-round ms-1"
                                        style="<?php echo e(getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new')); ?>"><?php echo e($new_transactions); ?></span>
                                </a>
                            </li>
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.transactionsRequests', ['status' => 'accepted'])); ?>">
                                    <?php echo e(trans('menu.withdraw module.my_accepted_requests')); ?>

                                    <span class="badge badge-round ms-1"
                                        style="<?php echo e(getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted')); ?>"><?php echo e($accepted_transactions); ?></span>
                                </a>
                            </li>
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected' ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.transactionsRequests', ['status' => 'rejected'])); ?>">
                                    <?php echo e(trans('menu.withdraw module.my_rejected_requests')); ?>

                                    <span class="badge badge-round ms-1"
                                        style="<?php echo e(getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected', 'secondary')); ?>"><?php echo e($rejected_transactions); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('withdraw.vendor_requests.new.view')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new' ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.transactionsRequests', ['status' => 'new'])); ?>">
                                <?php echo e(trans('menu.withdraw module.new transaction requests')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'new')); ?>"><?php echo e($new_transactions); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('withdraw.vendor_requests.accepted.view')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted' ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.transactionsRequests', ['status' => 'accepted'])); ?>">
                                <?php echo e(trans('menu.withdraw module.accepted transaction requests')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'accepted')); ?>"><?php echo e($accepted_transactions); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('withdraw.vendor_requests.rejected.view')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected' ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.transactionsRequests', ['status' => 'rejected'])); ?>">
                                <?php echo e(trans('menu.withdraw module.rejected transaction requests')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.transactionsRequests', $currentRoute) && request()->route('status') === 'rejected', 'secondary')); ?>"><?php echo e($rejected_transactions); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('request-quotations.index')): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.request_quotations')); ?></span>
            </li>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.request-quotations.index', 'admin.request-quotations.archived'], ['admin/request-quotations*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.request-quotations.index', 'admin.request-quotations.archived'], ['admin/request-quotations*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-file-question-alt"></span>
                    <span class="menu-text"><?php echo e(trans('menu.request_quotations.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.request-quotations.index', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.request-quotations.index')); ?>">
                            <?php echo e(trans('menu.request_quotations.all_requests')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.request-quotations.index', $currentRoute))); ?>">
                                <?php echo e(\Modules\Order\app\Models\RequestQuotation::notArchived()->count()); ?>

                            </span>
                        </a>
                    </li>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.request-quotations.archived', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.request-quotations.archived')); ?>">
                            <?php echo e(trans('menu.request_quotations.archived_requests')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.request-quotations.archived', $currentRoute))); ?>">
                                <?php echo e(\Modules\Order\app\Models\RequestQuotation::archived()->count()); ?>

                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['push-notifications.index', 'push-notifications.create'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.push_notifications')); ?></span>
            </li>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.system-settings.push-notifications.index', 'admin.system-settings.push-notifications.create'], ['admin/system-settings/push-notifications*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.system-settings.push-notifications.index', 'admin.system-settings.push-notifications.create'], ['admin/system-settings/push-notifications*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-bell"></span>
                    <span class="menu-text"><?php echo e(trans('menu.push_notifications.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('push-notifications.create')): ?>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.system-settings.push-notifications.create', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.system-settings.push-notifications.create')); ?>">
                            <?php echo e(trans('menu.push_notifications.send_notification')); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('push-notifications.index')): ?>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.system-settings.push-notifications.index', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.system-settings.push-notifications.index')); ?>">
                            <?php echo e(trans('menu.push_notifications.all_notifications')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.system-settings.push-notifications.index', $currentRoute))); ?>">
                                <?php echo e(\Modules\SystemSetting\app\Models\PushNotification::count()); ?>

                            </span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['accounting.overview.view', 'accounting.balance.view', 'accounting.expenses_keys.view', 'accounting.expenses.view'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.financials')); ?></span>
            </li>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.accounting.overview', 'admin.accounting.balance', 'admin.accounting.expenses'], ['admin/accounting*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.accounting.overview', 'admin.accounting.balance', 'admin.accounting.expenses'], ['admin/accounting*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-invoice"></span>
                    <span class="menu-text"><?php echo e(trans('menu.accounting module.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('accounting.overview.view')): ?>
                        <li><a class="d-flex align-items-center justify-content-between fw-bold"
                                href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(trans('menu.accounting module.overview')); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('accounting.balance.view')): ?>
                        <li><a class="d-flex align-items-center justify-content-between fw-bold"
                                href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(trans('menu.accounting module.balance')); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('accounting.expenses_keys.view')): ?>
                        <li><a class="d-flex align-items-center justify-content-between fw-bold"
                                href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(trans('menu.accounting module.expenses keys')); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('accounting.expenses.view')): ?>
                        <li><a class="d-flex align-items-center justify-content-between fw-bold"
                                href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(trans('menu.accounting module.expenses')); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['departments.index', 'categories.index', 'sub-categories.index'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.catalog management')); ?></span>
            </li>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.category-management.departments.index', 'admin.category-management.categories.index', 'admin.category-management.subcategories.index'], ['admin/category-management*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.category-management.departments.index', 'admin.category-management.categories.index', 'admin.category-management.subcategories.index'], ['admin/category-management*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-sitemap"></span>
                    <span class="menu-text"><?php echo e(trans('menu.category managment.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.category-management.departments.index', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.category-management.departments.index')); ?>">
                                <?php echo e(trans('menu.category managment.department')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.category-management.departments.index', $currentRoute))); ?>">
                                    <?php echo e(\Modules\CategoryManagment\app\Models\Department::count()); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categories.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.category-management.categories.index', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.category-management.categories.index')); ?>">
                                <?php echo e(trans('menu.category managment.main category')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.category-management.categories.index', $currentRoute))); ?>">
                                    <?php echo e(\Modules\CategoryManagment\app\Models\Category::count()); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sub-categories.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.category-management.subcategories.index', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.category-management.subcategories.index')); ?>">
                                <?php echo e(trans('menu.category managment.sub category')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.category-management.subcategories.index', $currentRoute))); ?>">
                                    <?php echo e(\Modules\CategoryManagment\app\Models\SubCategory::count()); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
            'products.index', 'products.bank',
            'variant-keys.index', 'variant-keys.create',
            'variants-configurations.index', 'variants-configurations.create',
        ])): ?>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.products.index', 'admin.products.pending', 'admin.products.rejected', 'admin.products.accepted', 'admin.products.create', 'admin.products.show', 'admin.products.edit', 'admin.products.bank', 'admin.products.stock-setup', 'admin.variant-keys.index', 'admin.variants-configurations.index'], ['admin/products*', 'admin/variant*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.products.index', 'admin.products.pending', 'admin.products.rejected', 'admin.products.accepted', 'admin.products.create', 'admin.products.show', 'admin.products.edit', 'admin.products.bank', 'admin.products.stock-setup', 'admin.variant-keys.index', 'admin.variants-configurations.index'], ['admin/products*', 'admin/variant*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-box"></span>
                    <span class="menu-text"><?php echo e(trans('menu.products.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.stock-setup')): ?>
                            <li>
                                <a class="fw-bold <?php echo e(isMenuActive('admin.products.stock-setup', $currentRoute) ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.products.stock-setup')); ?>">
                                    <?php echo e(trans('menu.products.stock_setup')); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.bank')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.products.bank', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.products.bank')); ?>">
                                <?php echo e(trans('menu.products.bank_products')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.products.bank', $currentRoute))); ?>">
                                    <?php echo e(\Modules\CatalogManagement\app\Models\Product::where('type', 'bank')->count()); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.products.index', 'admin.products.create', 'admin.products.show', 'admin.products.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.products.index')); ?>">
                                <?php echo e(trans('menu.products.all_products')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.products.index', 'admin.products.create', 'admin.products.show', 'admin.products.edit'], $currentRoute))); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($user_type_id, \App\Models\UserType::adminIds())): ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\Product::count()); ?>

                                    <?php else: ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\VendorProduct::where('vendor_id', auth()->user()->vendor->id ?? 0)->count()); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.products.pending', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.products.pending')); ?>">
                                <?php echo e(trans('menu.products.pending_products')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.products.pending', $currentRoute))); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($user_type_id, \App\Models\UserType::adminIds())): ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'pending')->count()); ?>

                                    <?php else: ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'pending')->where('vendor_id', auth()->user()->vendor->id ?? 0)->count()); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.products.rejected', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.products.rejected')); ?>">
                                <?php echo e(trans('menu.products.rejected_products')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.products.rejected', $currentRoute), 'secondary')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($user_type_id, \App\Models\UserType::adminIds())): ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'rejected')->count()); ?>

                                    <?php else: ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'rejected')->where('vendor_id', auth()->user()->vendor->id ?? 0)->count()); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.products.accepted', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.products.accepted')); ?>">
                                <?php echo e(trans('menu.products.accepted_products')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.products.accepted', $currentRoute))); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($user_type_id, \App\Models\UserType::adminIds())): ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->count()); ?>

                                    <?php else: ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\VendorProduct::where('status', 'approved')->where('vendor_id', auth()->user()->vendor->id ?? 0)->count()); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['variant-keys.index', 'variant-keys.create'])): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.variant-keys.index', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.variant-keys.index')); ?>">
                                <?php echo e(trans('menu.variant configurations.variant config keys')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.variant-keys.index', $currentRoute))); ?>"><?php echo e(\Modules\CatalogManagement\app\Models\VariantConfigurationKey::count()); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['variants-configurations.index', 'variants-configurations.create'])): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.variants-configurations.index', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.variants-configurations.index')); ?>">
                                <?php echo e(trans('menu.variant configurations.variant config')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.variants-configurations.index', $currentRoute))); ?>"><?php echo e(\Modules\CatalogManagement\app\Models\VariantsConfiguration::count()); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['bundle-categories.index', 'bundles.index'])): ?>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit', 'admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], ['admin/bundle-categories*', 'admin/bundles*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit', 'admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], ['admin/bundle-categories*', 'admin/bundles*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-package"></span>
                    <span class="menu-text"><?php echo e(trans('menu.bundles.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bundle-categories.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.bundle-categories.index')); ?>">
                                <?php echo e(trans('menu.bundles.bundle_categories')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.bundle-categories.index', 'admin.bundle-categories.create', 'admin.bundle-categories.show', 'admin.bundle-categories.edit'], $currentRoute))); ?>">
                                    <?php echo e(\Modules\CatalogManagement\app\Models\BundleCategory::count()); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bundles.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.bundles.index')); ?>">
                                <?php echo e(trans('menu.bundles.all_bundles')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.bundles.index', 'admin.bundles.create', 'admin.bundles.show', 'admin.bundles.edit'], $currentRoute))); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($user_type_id, \App\Models\UserType::adminIds())): ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\Bundle::count()); ?>

                                    <?php else: ?>
                                        <?php echo e(\Modules\CatalogManagement\app\Models\Bundle::where('vendor_id', auth()->user()->vendor->id ?? 0)->count()); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('taxes.index')): ?>
            <li
                class="<?php echo e(isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute) ? 'active' : ''); ?>">
                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute) ? 'active' : ''); ?>"
                    href="<?php echo e(route('admin.taxes.index')); ?>">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-bill"></span>
                        <span class="menu-text"><?php echo e(trans('menu.taxes.title')); ?></span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="<?php echo e(getBadgeStyle(isMenuActive(['admin.taxes.index', 'admin.taxes.create', 'admin.taxes.show', 'admin.taxes.edit'], $currentRoute))); ?>">
                        <?php echo e(\Modules\CatalogManagement\app\Models\Tax::count()); ?>

                    </span>
                </a>
            </li>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('occasions.index')): ?>
            <li
                class="<?php echo e(isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute) ? 'active' : ''); ?>">
                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute) ? 'active' : ''); ?>"
                    href="<?php echo e(route('admin.occasions.index')); ?>">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-calendar-alt"></span>
                        <span class="menu-text"><?php echo e(trans('menu.occasions')); ?></span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="<?php echo e(getBadgeStyle(isMenuActive(['admin.occasions.index', 'admin.occasions.create', 'admin.occasions.show', 'admin.occasions.edit'], $currentRoute))); ?>">
                        <?php
                            $occasions_count = 0;
                            if (isAdmin()) {
                                $occasions_count = \Modules\CatalogManagement\app\Models\Occasion::count();
                            } else {
                                $occasionVendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                if ($occasionVendor) {
                                    $occasions_count = \Modules\CatalogManagement\app\Models\Occasion::where('vendor_id', $occasionVendor->id)->count();
                                }
                            }
                        ?>
                        <?php echo e($occasions_count); ?>

                    </span>
                </a>
            </li>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product-reviews.index')): ?>
            <?php
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
            ?>
            <li class="has-child <?php echo e(isParentMenuOpen(['admin.reviews.index'], ['admin/reviews*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.reviews.index'], ['admin/reviews*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-star"></span>
                    <span class="menu-text"><?php echo e(trans('menu.product_reviews.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'all' ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.reviews.index', ['status' => 'all'])); ?>">
                            <?php echo e(trans('menu.product_reviews.all')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'all')); ?>"><?php echo e($reviews_all); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'approved' ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.reviews.index', ['status' => 'approved'])); ?>">
                            <?php echo e(trans('menu.product_reviews.accepted')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'approved')); ?>"><?php echo e($reviews_accepted); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'rejected' ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.reviews.index', ['status' => 'rejected'])); ?>">
                            <?php echo e(trans('menu.product_reviews.rejected')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.reviews.index', $currentRoute) && request()->query('status') === 'rejected', 'secondary')); ?>"><?php echo e($reviews_rejected); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('brands.index')): ?>
            <li>
                <a href="<?php echo e(route('admin.brands.index')); ?>"
                    class="<?php echo e(isMenuActive(['admin.brands.index', 'admin.brands.create', 'admin.brands.show', 'admin.brands.edit'], $currentRoute) ? 'active' : ''); ?>">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-ticket"></span>
                            <span class="menu-text fw-bold"><?php echo e(trans('menu.brands.title')); ?></span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="<?php echo e(getBadgeStyle(isMenuActive(['admin.brands.index', 'admin.brands.create', 'admin.brands.show', 'admin.brands.edit'], $currentRoute))); ?>"><?php echo e(\Modules\CatalogManagement\app\Models\Brand::count()); ?></span>
                    </span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('promocodes.index')): ?>
            <li>
                <a href="<?php echo e(route('admin.promocodes.index')); ?>"
                    class="<?php echo e(isMenuActive(['admin.promocodes.index', 'admin.promocodes.create', 'admin.promocodes.edit', 'admin.promocodes.show'], $currentRoute) ? 'active' : ''); ?>">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-ticket"></span>
                            <span class="menu-text"><?php echo e(trans('menu.promocodes.title')); ?></span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="<?php echo e(getBadgeStyle(isMenuActive(['admin.promocodes.index', 'admin.promocodes.create', 'admin.promocodes.edit', 'admin.promocodes.show'], $currentRoute))); ?>"><?php echo e(\Modules\CatalogManagement\app\Models\Promocode::count()); ?></span>
                    </span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['points-settings.index', 'points-settings.user-points.index'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.points system')); ?></span>
            </li>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.points-settings.index', 'admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], ['admin/points-settings*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.points-settings.index', 'admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], ['admin/points-settings*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-coins"></span>
                    <span class="menu-text"><?php echo e(trans('menu.point managment.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('points-settings.index')): ?>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.points-settings.index'], $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.points-settings.index')); ?>">
                            <?php echo e(trans('menu.point managment.title')); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('points-settings.user-points.index')): ?>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.points-settings.user-points.index', 'admin.points-settings.user-points.transactions'], $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.points-settings.user-points.index')); ?>">
                            <?php echo e(trans('menu.point managment.users points')); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['admin-roles.index', 'admins.index', 'vendor-user-roles.index', 'vendor-users.index'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.user management')); ?></span>
            </li>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['admin-roles.index', 'admins.index'])): ?>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.admin-management.roles.index', 'admin.admin-management.admins.index'], ['admin/admin-management*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.admin-management.roles.index', 'admin.admin-management.admins.index'], ['admin/admin-management*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-user-check"></span>
                    <span class="menu-text"><?php echo e(trans('menu.admin managment.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin-roles.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.admin-management.roles.index', 'admin.admin-management.roles.create', 'admin.admin-management.roles.show', 'admin.admin-management.roles.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.admin-management.roles.index')); ?>">
                                <?php echo e(trans('menu.admin managment.roles managment')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.admin-management.roles.index', 'admin.admin-management.roles.create', 'admin.admin-management.roles.show', 'admin.admin-management.roles.edit'], $currentRoute))); ?>">
                                    <?php echo e($admin_roles_count); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admins.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.admin-management.admins.index', 'admin.admin-management.admins.create', 'admin.admin-management.admins.show', 'admin.admin-management.admins.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.admin-management.admins.index')); ?>">
                                <?php echo e(trans('menu.admin managment.admin managment')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.admin-management.admins.index', 'admin.admin-management.admins.create', 'admin.admin-management.admins.show', 'admin.admin-management.admins.edit'], $currentRoute))); ?>">
                                    <?php echo e($admins_count); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['vendor-user-roles.index', 'vendor-users.index'])): ?>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.vendor-users.index'], ['admin/vendor-users-management*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.vendor-users.index'], ['admin/vendor-users-management*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-users-alt"></span>
                    <span class="menu-text"><?php echo e(trans('menu.admin managment.vendor users management')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-user-roles.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.roles.create', 'admin.vendor-users-management.roles.show', 'admin.vendor-users-management.roles.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.vendor-users-management.roles.index')); ?>">
                                <?php echo e(trans('menu.admin managment.vendor users roles management')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.vendor-users-management.roles.index', 'admin.vendor-users-management.roles.create', 'admin.vendor-users-management.roles.show', 'admin.vendor-users-management.roles.edit'], $currentRoute))); ?>">
                                    <?php echo e($vendor_user_roles_count); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-users.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.vendor-users-management.vendor-users.index', 'admin.vendor-users-management.vendor-users.create', 'admin.vendor-users-management.vendor-users.show', 'admin.vendor-users-management.vendor-users.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.vendor-users-management.vendor-users.index')); ?>">
                                <?php echo e(trans('menu.admin managment.vendor users management')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.vendor-users-management.vendor-users.index', 'admin.vendor-users-management.vendor-users.create', 'admin.vendor-users-management.vendor-users.show', 'admin.vendor-users-management.vendor-users.edit'], $currentRoute))); ?>">
                                    <?php echo e($vendor_users_count); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>


        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['vendors.index', 'vendors.create'])): ?>
            <li
                class="has-child <?php echo e(Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendors*') ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendors*') ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-users-alt"></span>
                    <span class="menu-text"><?php echo e(trans('menu.vendors.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendors.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.vendors.index', 'admin.vendors.show', 'admin.vendors.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.vendors.index')); ?>">
                                <?php echo e(trans('menu.vendors.all')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.vendors.index', 'admin.vendors.show', 'admin.vendors.edit'], $currentRoute))); ?>">
                                    <?php echo e(\Modules\Vendor\app\Models\Vendor::count()); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendors.create')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.vendors.create', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.vendors.create')); ?>">
                                <?php echo e(trans('menu.vendors.create')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-reviews.index')): ?>
            <?php
                try {
                    $vendor_reviews_all = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->count();
                    $vendor_reviews_accepted = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->where('status', 'approved')->count();
                    $vendor_reviews_rejected = Modules\CatalogManagement\app\Models\Review::where('reviewable_type', Modules\Vendor\app\Models\Vendor::class)->where('status', 'rejected')->count();
                } catch (\Exception $e) {
                    $vendor_reviews_all = $vendor_reviews_accepted = $vendor_reviews_rejected = 0;
                }
            ?>
            <li class="has-child <?php echo e(isParentMenuOpen(['admin.vendor-reviews.index'], ['admin/vendor-reviews*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.vendor-reviews.index'], ['admin/vendor-reviews*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-star"></span>
                    <span class="menu-text"><?php echo e(trans('menu.vendor_reviews.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'all' ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.vendor-reviews.index', ['status' => 'all'])); ?>">
                            <?php echo e(trans('menu.vendor_reviews.all')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'all')); ?>"><?php echo e($vendor_reviews_all); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'approved' ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.vendor-reviews.index', ['status' => 'approved'])); ?>">
                            <?php echo e(trans('menu.vendor_reviews.accepted')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'approved')); ?>"><?php echo e($vendor_reviews_accepted); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'rejected' ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.vendor-reviews.index', ['status' => 'rejected'])); ?>">
                            <?php echo e(trans('menu.vendor_reviews.rejected')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.vendor-reviews.index', $currentRoute) && request()->query('status') === 'rejected', 'secondary')); ?>"><?php echo e($vendor_reviews_rejected); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>


        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-requests.index')): ?>
            <li
                class="has-child <?php echo e(Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendor-requests*') ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(Request::is(LaravelLocalization::getCurrentLocale() . '/admin/vendor-requests*') ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-clipboard-notes"></span>
                    <span class="menu-text"><?php echo e(trans('menu.become a vendor requests.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.vendor-requests.index')); ?>">
                            <?php echo e(trans('common.all')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status'))); ?>"><?php echo e(\Modules\Vendor\app\Models\VendorRequest::count()); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.vendor-requests.index')); ?>">
                            <?php echo e(trans('menu.become a vendor requests.new')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.vendor-requests.index', $currentRoute) && !request()->has('status'))); ?>"><?php echo e(\Modules\Vendor\app\Models\VendorRequest::pending()->count()); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(request()->get('status') === 'approved' ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.vendor-requests.index')); ?>?status=approved">
                            <?php echo e(trans('menu.become a vendor requests.accepted')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(request()->get('status') === 'approved')); ?>"><?php echo e(\Modules\Vendor\app\Models\VendorRequest::approved()->count()); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(request()->get('status') === 'rejected' ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.vendor-requests.index')); ?>?status=rejected">
                            <?php echo e(trans('menu.become a vendor requests.rejected')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(request()->get('status') === 'rejected', 'secondary')); ?>"><?php echo e(\Modules\Vendor\app\Models\VendorRequest::rejected()->count()); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customers.index')): ?>
            <li
                class="has-child <?php echo e(Request::is(LaravelLocalization::getCurrentLocale() . '/admin/customers*') ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(Request::is(LaravelLocalization::getCurrentLocale() . '/admin/customers*') ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-user-circle"></span>
                    <span class="menu-text"><?php echo e(trans('menu.customers.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.customers.index', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.customers.index')); ?>">
                            <?php echo e(trans('menu.customers.all')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.customers.index', $currentRoute))); ?>">
                                <?php
                                    $customerCount = 0;
                                    if (isAdmin()) {
                                        $customerCount = \Modules\Customer\app\Models\Customer::count();
                                    } else {
                                        $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                                        if ($vendor) {
                                            $customerCount = \Modules\Customer\app\Models\Customer::where('vendor_id', $vendor->id)->count();
                                        }
                                    }
                                ?>
                                <?php echo e($customerCount); ?>

                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['orders.index', 'orders.create'])): ?>
                <li class="menu-title mt-30">
                    <span><?php echo e(trans('menu.sections.order and fulfillment')); ?></span>
                </li>
                <li
                    class="has-child <?php echo e(isParentMenuOpen(['admin.orders.index', 'admin.orders.create'], ['admin/orders*']) ? 'open' : ''); ?>">
                    <a href="#"
                        class="<?php echo e(isParentMenuOpen(['admin.orders.index', 'admin.orders.create'], ['admin/orders*']) ? 'active' : ''); ?>">
                        <span class="nav-icon uil uil-shopping-cart"></span>
                        <span class="menu-text"><?php echo e(trans('menu.orders.title')); ?></span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li class="l_sidebar">
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.orders.index', $currentRoute) && !request()->has('stage') ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.orders.index')); ?>">
                                <?php echo e(trans('menu.orders.all')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.orders.index', $currentRoute) && !request()->has('stage'))); ?>">
                                    <?php
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
                                    ?>
                                    <?php echo e($allOrdersCount); ?>

                                </span>
                            </a>
                        </li>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('orders.create')): ?>
                            <li class="l_sidebar">
                                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.orders.create', $currentRoute) ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.orders.create')); ?>">
                                    <?php echo e(trans('menu.orders.create')); ?>

                                </a>
                            </li>
                        <?php endif; ?>

                        
                        <?php
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
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $orderStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="l_sidebar">
                                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(request()->get('stage') == $stage->id ? 'active' : ''); ?>"
                                    href="<?php echo e(route('admin.orders.index', ['stage' => $stage->id])); ?>">
                                    <?php echo e($stage->translations->where('lang_key', 'name')->first()?->lang_value ?? $stage->slug); ?>

                                    <span class="badge badge-round ms-1"
                                        style="<?php echo e(getBadgeStyle(request()->get('stage') == $stage->id)); ?>">
                                        <?php
                                            $stageOrderCount = 0;
                                            if (isAdmin()) {
                                                $stageOrderCount = \Modules\Order\app\Models\Order::where('stage_id', $stage->id)->count();
                                            } elseif ($stageVendor) {
                                                $stageOrderCount = \Modules\Order\app\Models\Order::where('stage_id', $stage->id)
                                                    ->whereHas('products', function($q) use ($stageVendor) { 
                                                        $q->where('vendor_id', $stageVendor->id); 
                                                    })->count();
                                            }
                                        ?>
                                        <?php echo e($stageOrderCount); ?>

                                    </span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('order-stages.index')): ?>
            <?php
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
            ?>
            <li>
                <a href="<?php echo e(route('admin.order-stages.index')); ?>"
                    class="<?php echo e(isMenuActive('admin.order-stages.index', $currentRoute) ? 'active' : ''); ?>">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-process"></span>
                            <span class="menu-text"><?php echo e(trans('menu.orders.order stages')); ?></span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="<?php echo e(getBadgeStyle(isMenuActive('admin.order-stages.index', $currentRoute))); ?>"><?php echo e($orderStages->count()); ?></span>
                    </span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.index')): ?>
            <li>
                <a href="<?php echo e(route('admin.shippings.index')); ?>"
                    class="<?php echo e(isMenuActive('admin.shippings.index', $currentRoute) ? 'active' : ''); ?>">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-truck"></span>
                            <span class="menu-text"><?php echo e(trans('menu.orders.shipping methods')); ?></span>
                        </span>
                        <span class="badge badge-round ms-1"
                            style="<?php echo e(getBadgeStyle(isMenuActive('admin.shippings.index', $currentRoute))); ?>"><?php echo e(\Modules\Order\app\Models\Shipping::count()); ?></span>
                    </span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['blog-categories.index', 'blogs.index'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.content and engagement')); ?></span>
            </li>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.system-settings.blog-categories.index', 'admin.system-settings.blogs.index'], ['admin/system-settings/blog-categories*', 'admin/system-settings/blogs*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.system-settings.blog-categories.index', 'admin.system-settings.blogs.index'], ['admin/system-settings/blog-categories*', 'admin/system-settings/blogs*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-edit-alt"></span>
                    <span class="menu-text"><?php echo e(trans('menu.blog managment.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('blog-categories.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.system-settings.blog-categories.index', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.system-settings.blog-categories.index')); ?>">
                                <?php echo e(trans('menu.blog managment.categories')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.system-settings.blog-categories.index', $currentRoute))); ?>">
                                    <?php
                                        try {
                                            $blog_categories_count = \Modules\SystemSetting\app\Models\BlogCategory::count();
                                        } catch (\Exception $e) {
                                            $blog_categories_count = 0;
                                        }
                                    ?>
                                    <?php echo e($blog_categories_count); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('blogs.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.system-settings.blogs.index', $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.system-settings.blogs.index')); ?>">
                                <?php echo e(trans('menu.blog managment.blogs')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive('admin.system-settings.blogs.index', $currentRoute))); ?>">
                                    <?php
                                        try {
                                            $blogs_count = \Modules\SystemSetting\app\Models\Blog::count();
                                        } catch (\Exception $e) {
                                            $blogs_count = 0;
                                        }
                                    ?>
                                    <?php echo e($blogs_count); ?>

                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('messages.index')): ?>
            <li class="<?php echo e(isMenuActive('admin.messages.index', $currentRoute) ? 'active' : ''); ?>">
                <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.messages.index', $currentRoute) ? 'active' : ''); ?>"
                    href="<?php echo e(route('admin.messages.index')); ?>">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-envelope"></span>
                        <span class="menu-text"><?php echo e(__('systemsetting::messages.messages')); ?></span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="<?php echo e(getBadgeStyle(isMenuActive('admin.messages.index', $currentRoute))); ?>">
                        <?php echo e(\Modules\SystemSetting\app\Models\Message::count()); ?>

                    </span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['reports.registered_users.view', 'reports.area_users.view', 'reports.orders.view', 'reports.products.view', 'reports.points.view'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.reports')); ?></span>
            </li>
            <li class="has-child <?php echo e(isParentMenuOpen(['admin.reports.index', 'admin.reports.registered-users', 'admin.reports.area-users', 'admin.reports.orders', 'admin.reports.products', 'admin.reports.points'], ['admin/reports*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.reports.index', 'admin.reports.registered-users', 'admin.reports.area-users', 'admin.reports.orders', 'admin.reports.products', 'admin.reports.points'], ['admin/reports*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-chart-line"></span>
                    <span class="menu-text"><?php echo e(trans('menu.reports.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.registered_users.view')): ?>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.reports.registered-users', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.reports.registered-users')); ?>">
                            <?php echo e(trans('menu.reports.registerd users')); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.area_users.view')): ?>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.reports.area-users', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.reports.area-users')); ?>">
                            <?php echo e(trans('menu.reports.area users')); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.orders.view')): ?>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.reports.orders', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.reports.orders')); ?>">
                            <?php echo e(trans('menu.reports.orders report')); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.products.view')): ?>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.reports.products', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.reports.products')); ?>">
                            <?php echo e(trans('menu.reports.product report')); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reports.points.view')): ?>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.reports.points', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.reports.points')); ?>">
                            <?php echo e(trans('menu.reports.points report')); ?>

                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['ads.index', 'features.index', 'footer-content.index', 'faqs.index',
            'sliders.index', 'site-information.index', 'return-policy.index', 'service-terms.index',
            'privacy-policy.index', 'terms-conditions.index', 'area.country.index', 'system.currency.index',
            'system_log.view'])): ?>
            <li class="menu-title mt-30">
                <span><?php echo e(trans('menu.sections.settings')); ?></span>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ads.index')): ?>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.system-settings.ads.index'], ['*/system-settings/ads*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.system-settings.ads.index'], ['*/system-settings/ads*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-trophy"></span>
                    <span class="menu-text"><?php echo e(trans('menu.advertisements.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive('admin.system-settings.ads.index', $currentRoute) ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.system-settings.ads.index')); ?>">
                            <?php echo e(trans('menu.advertisements.title')); ?>

                            <span class="badge badge-round ms-1"
                                style="<?php echo e(getBadgeStyle(isMenuActive('admin.system-settings.ads.index', $currentRoute))); ?>">
                                <?php echo e(\Modules\SystemSetting\app\Models\Ad::count()); ?>

                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['features.index', 'footer-content.index', 'faqs.index', 'sliders.index'])): ?>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.system-settings.features.index', 'admin.system-settings.footer-content.index', 'admin.system-settings.faqs.index', 'admin.system-settings.sliders.index', 'admin.system-settings.site-information.index'], ['*/system-settings/features*', '*/system-settings/footer-content*', '*/system-settings/faqs*', '*/system-settings/sliders*', '*/system-settings/site-information*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.system-settings.features.index', 'admin.system-settings.footer-content.index', 'admin.system-settings.faqs.index', 'admin.system-settings.sliders.index', 'admin.system-settings.site-information.index'], ['*/system-settings/features*', '*/system-settings/footer-content*', '*/system-settings/faqs*', '*/system-settings/sliders*', '*/system-settings/site-information*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-browser"></span>
                    <span class="menu-text fw-bold"><?php echo e(trans('menu.frontend settings.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('features.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.features.index')); ?>"
                                class="<?php echo e(isMenuActive('admin.system-settings.features.index', $currentRoute) ? 'active' : ''); ?>">
                                <span class="nav-icon uil uil-star"></span>
                                <span class=" fw-bold"><?php echo e(trans('menu.frontend settings.our features')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('footer-content.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.footer-content.index')); ?>"
                                class="<?php echo e(isMenuActive('admin.system-settings.footer-content.index', $currentRoute) ? 'active' : ''); ?>">
                                <span class="nav-icon uil uil-align-center-alt"></span>
                                <span class=" fw-bold"><?php echo e(trans('menu.frontend settings.footer content')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('faqs.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.faqs.index')); ?>"
                                class="<?php echo e(isMenuActive(['admin.system-settings.faqs.index', 'admin.system-settings.faqs.create', 'admin.system-settings.faqs.edit', 'admin.system-settings.faqs.show'], $currentRoute) ? 'active' : ''); ?>">
                                <span class="nav-icon uil uil-question-circle"></span>
                                <span class=" fw-bold"><?php echo e(trans('menu.frontend settings.faq management')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sliders.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.sliders.index')); ?>"
                                class="<?php echo e(isMenuActive(['admin.system-settings.sliders.index', 'admin.system-settings.sliders.create', 'admin.system-settings.sliders.edit', 'admin.system-settings.sliders.show'], $currentRoute) ? 'active' : ''); ?>">
                                <span class="nav-icon uil uil-image-v"></span>
                                <span class=" fw-bold"><?php echo e(trans('menu.frontend settings.sliders')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </li>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['site-information.index', 'return-policy.index', 'service-terms.index',
            'privacy-policy.index', 'terms-conditions.index'])): ?>
            <li class="has-child">
                <a href="#"
                    class="<?php echo e(isMenuActive(['admin.system-settings.site-information.index'], $currentRoute) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-info-circle"></span>
                    <span class="fw-bold"><?php echo e(trans('menu.frontend settings.site information')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('site-information.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.site-information.index')); ?>"
                                class="<?php echo e(isMenuActive('admin.system-settings.site-information.index', $currentRoute) ? 'active' : ''); ?>">
                                <span class="fw-bold"><?php echo e(trans('menu.frontend settings.contact us')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('return-policy.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.return-policy.index')); ?>"
                                class="<?php echo e(isMenuActive('admin.system-settings.return-policy.index', $currentRoute) ? 'active' : ''); ?>">
                                <span class="fw-bold"><?php echo e(trans('menu.frontend settings.return policy')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('service-terms.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.service-terms.index')); ?>"
                                class="<?php echo e(isMenuActive('admin.system-settings.service-terms.index', $currentRoute) ? 'active' : ''); ?>">
                                <span class="fw-bold"><?php echo e(trans('menu.frontend settings.service terms')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('privacy-policy.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.privacy-policy.index')); ?>"
                                class="<?php echo e(isMenuActive('admin.system-settings.privacy-policy.index', $currentRoute) ? 'active' : ''); ?>">
                                <span class="fw-bold"><?php echo e(trans('menu.frontend settings.privacy policy')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('terms-conditions.index')): ?>
                        <li>
                            <a href="<?php echo e(route('admin.system-settings.terms-conditions.index')); ?>"
                                class="<?php echo e(isMenuActive('admin.system-settings.terms-conditions.index', $currentRoute) ? 'active' : ''); ?>">
                                <span class="fw-bold"><?php echo e(trans('menu.frontend settings.terms conditions')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['area.country.index', 'area.city.index', 'area.region.index', 'area.subregion.index'])): ?>
            <li
                class="has-child <?php echo e(isParentMenuOpen(['admin.area-settings.countries.index', 'admin.area-settings.cities.index', 'admin.area-settings.regions.index', 'admin.area-settings.subregions.index'], ['admin/area-settings*']) ? 'open' : ''); ?>">
                <a href="#"
                    class="<?php echo e(isParentMenuOpen(['admin.area-settings.countries.index', 'admin.area-settings.cities.index', 'admin.area-settings.regions.index', 'admin.area-settings.subregions.index'], ['admin/area-settings*']) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-map-marker"></span>
                    <span class="menu-text"><?php echo e(trans('menu.area settings.title')); ?></span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('area.country.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.area-settings.countries.index', 'admin.area-settings.countries.create', 'admin.area-settings.countries.show', 'admin.area-settings.countries.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.area-settings.countries.index')); ?>">
                                <?php echo e(trans('menu.area settings.country')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.area-settings.countries.index', 'admin.area-settings.countries.create', 'admin.area-settings.countries.show', 'admin.area-settings.countries.edit'], $currentRoute))); ?>"><?php echo e(\Modules\AreaSettings\app\Models\Country::count()); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('area.city.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.area-settings.cities.index', 'admin.area-settings.cities.create', 'admin.area-settings.cities.show', 'admin.area-settings.cities.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.area-settings.cities.index')); ?>">
                                <?php echo e(trans('menu.area settings.city')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.area-settings.cities.index', 'admin.area-settings.cities.create', 'admin.area-settings.cities.show', 'admin.area-settings.cities.edit'], $currentRoute))); ?>"><?php echo e(\Modules\AreaSettings\app\Models\City::count()); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('area.region.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.area-settings.regions.index', 'admin.area-settings.regions.create', 'admin.area-settings.regions.show', 'admin.area-settings.regions.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.area-settings.regions.index')); ?>">
                                <?php echo e(trans('menu.area settings.region')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.area-settings.regions.index', 'admin.area-settings.regions.create', 'admin.area-settings.regions.show', 'admin.area-settings.regions.edit'], $currentRoute))); ?>"><?php echo e(\Modules\AreaSettings\app\Models\Region::count()); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('area.subregion.index')): ?>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.area-settings.subregions.index', 'admin.area-settings.subregions.create', 'admin.area-settings.subregions.show', 'admin.area-settings.subregions.edit'], $currentRoute) ? 'active' : ''); ?>"
                                href="<?php echo e(route('admin.area-settings.subregions.index')); ?>">
                                <?php echo e(trans('menu.area settings.subregion')); ?>

                                <span class="badge badge-round ms-1"
                                    style="<?php echo e(getBadgeStyle(isMenuActive(['admin.area-settings.subregions.index', 'admin.area-settings.subregions.create', 'admin.area-settings.subregions.show', 'admin.area-settings.subregions.edit'], $currentRoute))); ?>"><?php echo e(\Modules\AreaSettings\app\Models\SubRegion::count()); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('system.currency.index')): ?>
            <li>
                <a href="<?php echo e(route('admin.system-settings.currencies.index')); ?>"
                    class="d-flex align-items-center justify-content-between fw-bold <?php echo e(isMenuActive(['admin.system-settings.currencies.index', 'admin.system-settings.currencies.create', 'admin.system-settings.currencies.show', 'admin.system-settings.currencies.edit'], $currentRoute) ? 'active' : ''); ?>">
                    <span class="d-flex align-items-center">
                        <span class="nav-icon uil uil-dollar-alt"></span>
                        <span class="menu-text"><?php echo e(trans('menu.currencies.title')); ?></span>
                    </span>
                    <span class="badge badge-round ms-1"
                        style="<?php echo e(getBadgeStyle(isMenuActive(['admin.system-settings.currencies.index', 'admin.system-settings.currencies.create', 'admin.system-settings.currencies.show', 'admin.system-settings.currencies.edit'], $currentRoute))); ?>">
                        <?php echo e(\Modules\SystemSetting\app\Models\Currency::count()); ?>

                    </span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('system_log.view')): ?>
            <li>
                <a href="<?php echo e(route('admin.system-settings.activity-logs.index')); ?>"
                    class="<?php echo e(isMenuActive('admin.system-settings.activity-logs.index', $currentRoute) ? 'active' : ''); ?>">
                    <span class="nav-icon uil uil-history"></span>
                    <span class="menu-text"><?php echo e(trans('menu.system log.title')); ?></span>
                </a>
            </li>
        <?php endif; ?>

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
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/_menu.blade.php ENDPATH**/ ?>