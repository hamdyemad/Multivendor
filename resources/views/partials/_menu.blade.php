@php
    $user_type = auth()->user()->user_type->name;
    $vendor = auth()->user()->vendor;

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
@endphp
<div class="sidebar__menu-group">
    <ul class="sidebar_nav">
        @can('dashboard.view')
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ Request::is(LaravelLocalization::getCurrentLocale() . '/admin/dashboard') ? 'active' : '' }}">
                    <span class="nav-icon uil uil-create-dashboard"></span>
                    <span class="menu-text">{{ trans('menu.dashboard.title') }}</span>
                </a>
            </li>
        @endcan
































        <li class="menu-title mt-30">
            <span>{{ trans('menu.sections.withdraw module') }}</span>
        </li>
        <li class="has-child">
            <a href="#" class="">
                <span class="nav-icon uil uil-sitemap"></span>
                <span class="menu-text">{{ trans('menu.withdraw module.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                @if ($user_type == 'super_admin')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.sendMoney') }}">
                            {{ trans('menu.withdraw module.send money') }}
                        </a>
                    </li>

                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.allTransactions') }}">
                            {{ trans('menu.withdraw module.all transactions') }}
                        </a>
                    </li>
                @endif
                @if ($user_type == 'vendor')
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.sendMoneyRequest') }}">
                            {{ trans('menu.withdraw module.send money request') }}
                        </a>
                    </li>
                @endif

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.transactionsRequests', 'new') }}">
                        {{ trans('menu.withdraw module.new transaction requests') }}
                        <span class="badge badge-round badge-primary ms-1">{{ $new_transactions }}</span>
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.transactionsRequests', 'accepted') }}">
                        {{ trans('menu.withdraw module.accepted transaction requests') }}
                        <span class="badge badge-round badge-primary ms-1">{{ $accepted_transactions }}</span>
                    </a>
                </li>

                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.transactionsRequests', 'rejected') }}">
                        {{ trans('menu.withdraw module.rejected transaction requests') }}
                        <span class="badge badge-round badge-primary ms-1">{{ $rejected_transactions }}</span>
                    </a>
                </li>


            </ul>
        </li>

        @can('accounting.view')
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.financials') }}</span>
            </li>
            <li class="has-child">
                <a href="#" class="">
                    <span class="nav-icon uil uil-invoice"></span>
                    <span class="menu-text">{{ trans('menu.accounting module.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li><a class="d-flex align-items-center justify-content-between fw-bold" href="{{ route('admin.dashboard') }}">{{ trans('menu.accounting module.overview') }}</a>
                    </li>
                    <li><a class="d-flex align-items-center justify-content-between fw-bold" href="{{ route('admin.dashboard') }}">{{ trans('menu.accounting module.balance') }}</a>
                    </li>
                    <li><a class="d-flex align-items-center justify-content-between fw-bold" href="{{ route('admin.dashboard') }}">{{ trans('menu.accounting module.expenses keys') }}</a>
                    </li>
                    <li><a class="d-flex align-items-center justify-content-between fw-bold" href="{{ route('admin.dashboard') }}">{{ trans('menu.accounting module.expenses') }}</a>
                    </li>
                </ul>
            </li>
        @endcan


























        @canany(['activities.index', 'departments.index', 'categories.index', 'sub_categories.index'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.catalog management') }}</span>
            </li>
            @if ($user_type == 'super_admin')
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-sitemap"></span>
                        <span class="menu-text">{{ trans('menu.category managment.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('activities.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.category-management.activities.index') }}">
                                    {{ trans('menu.activities.title') }}
                                    <span class="badge badge-round badge-secondary ms-1">8</span>
                                </a>
                            </li>
                        @endcan

                        @can('departments.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.category-management.departments.index') }}">
                                    {{ trans('menu.category managment.department') }}
                                    <span class="badge badge-round badge-primary ms-1">8</span>
                                </a>
                            </li>
                        @endcan

                        @can('categories.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.category-management.categories.index') }}">
                                    {{ trans('menu.category managment.main category') }}
                                    <span class="badge badge-round badge-info ms-1">25</span>
                                </a>
                            </li>
                        @endcan

                        @can('sub_categories.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.category-management.subcategories.index') }}">
                                    {{ trans('menu.category managment.sub category') }}
                                    <span class="badge badge-round badge-success ms-1">45</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
        @endcanany

        <li class="has-child">
            <a href="#" class="">
                <span class="nav-icon uil uil-box"></span>
                <span class="menu-text">{{ trans('menu.products.title') }}</span>
                <span class="toggle-icon"></span>
            </a>
            <ul class="px-0">
                <li>
                    <a class="d-flex align-items-center justify-content-between fw-bold"
                        href="{{ route('admin.products.index') }}">
                        {{ trans('menu.products.all_products') }}
                        <span class="badge badge-round badge-primary ms-1">20</span>
                    </a>
                </li>
                @canany(['variant-keys.view', 'variant-keys.create'])
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.variant-keys.index') }}">
                            {{ trans('menu.variant configurations.variant config keys') }}
                            <span class="badge badge-round badge-info ms-1">20</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.variants-configurations.index') }}">
                            {{ trans('menu.variant configurations.variant config') }}
                            <span class="badge badge-round badge-success ms-1">10</span>
                        </a>
                    </li>
                @endcanany
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
                            <span class="badge badge-round badge-success ms-1">120</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.product reviews.rejected') }}
                            <span class="badge badge-round badge-danger ms-1">30</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @if ($user_type == 'super_admin')
            @canany(['taxes.view', 'taxes.create'])
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-percentage"></span>
                        <span class="menu-text">{{ trans('menu.taxes.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('taxes.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.taxes.index') }}">
                                    {{ trans('menu.taxes.all') }}
                                    <span class="badge badge-round badge-info ms-1">12</span>
                                </a>
                            </li>
                        @endcan

                        @can('taxes.create')
                            <li><a href="{{ route('admin.taxes.create') }}">{{ trans('menu.taxes.create') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany
        @endif

        @if ($user_type == 'super_admin')

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
                <a href="{{ route('admin.brands.index') }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-ticket"></span>
                            <span class="menu-text">{{ trans('menu.brands.title') }}</span>
                        </span>
                        <span class="badge badge-round badge-success ms-1">20</span>
                    </span>
                </a>
            </li>
        @endcan

        @if ($user_type == 'super_admin')
            @can('promocodes.view')
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                            <span class="d-flex align-items-center">
                                <span class="nav-icon uil uil-ticket"></span>
                                <span class="menu-text">{{ trans('menu.promocodes.title') }}</span>
                            </span>
                            <span class="badge badge-round badge-success ms-1">50</span>
                        </span>
                    </a>
                </li>
            @endcan
        @endif

        @canany(['roles.index', 'admins.index'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.user management') }}</span>
            </li>
            <li class="has-child">
                <a href="#" class="">
                    <span class="nav-icon uil uil-user-check"></span>
                    <span class="menu-text">{{ trans('menu.admin managment.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    @can('roles.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.admin-management.roles.index') }}">
                                {{ trans('menu.admin managment.roles managment') }}
                            </a>
                        </li>
                    @endcan

                    @can('admins.index')
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.admin-management.admins.index') }}">
                                {{ trans('menu.admin managment.admin managment') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany
        @if ($user_type == 'super_admin')
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
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.vendors.index') }}">
                                    {{ trans('menu.vendors.all') }}
                                    <span class="badge badge-round badge-success ms-1">50</span>
                                </a>
                            </li>
                        @endcan

                        @can('vendors.create')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.vendors.create') }}">
                                    {{ trans('menu.vendors.create') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('vendor_requests.new')
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-clipboard-notes"></span>
                        <span class="menu-text">{{ trans('menu.become a vendor requests.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.become a vendor requests.new') }}
                                <span class="badge badge-round badge-primary ms-1">50</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.become a vendor requests.accepted') }}
                                <span class="badge badge-round badge-success ms-1">50</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.become a vendor requests.rejected') }}
                                <span class="badge badge-round badge-danger ms-1">50</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

        @endif

        @canany(['orders.new', 'orders.inprogress', 'orders.delivered', 'orders.canceled', 'orders.refunded'])
            <li class="menu-title mt-30">
                <span>{{ trans('menu.sections.order and fulfillment') }}</span>
            </li>
            <li class="has-child">
                <a href="#" class="">
                    <span class="nav-icon uil uil-shopping-cart"></span>
                    <span class="menu-text">{{ trans('menu.orders.title') }}</span>
                    <span class="toggle-icon"></span>
                </a>
                <ul class="px-0">
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.orders.new') }}
                            <span class="badge badge-round badge-success ms-1">50</span>
                        </a>
                    </li>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.orders.inprogress') }}
                            <span class="badge badge-round badge-success ms-1">50</span>
                        </a>
                    </li>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.orders.delivered') }}
                            <span class="badge badge-round badge-success ms-1">50</span>
                        </a>
                    </li>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.orders.canceled') }}
                            <span class="badge badge-round badge-success ms-1">50</span>
                        </a>
                    </li>
                    <li class="l_sidebar">
                        <a class="d-flex align-items-center justify-content-between fw-bold"
                            href="{{ route('admin.dashboard') }}">
                            {{ trans('menu.orders.refunded') }}
                            <span class="badge badge-success badge-round ms-1">50</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcanany

        @can('order_stages.index')
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                        <span class="d-flex align-items-center">
                            <span class="nav-icon uil uil-process"></span>
                            <span class="menu-text">{{ trans('menu.orders.order stages') }}</span>
                        </span>
                        <span class="badge badge-success badge-round ms-1">500</span>
                    </span>
                </a>
            </li>
        @endcan

        @if ($user_type == 'super_admin')
            @can('shipping_methods.index')
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <span class="d-flex align-items-center justify-content-between fw-bold w-100">
                            <span class="d-flex align-items-center">
                                <span class="nav-icon uil uil-truck"></span>
                                <span class="menu-text">{{ trans('menu.orders.shipping methods') }}</span>
                            </span>
                            <span class="badge badge-round badge-success ms-1">50</span>
                        </span>
                    </a>
                </li>
            @endcan
        @endif


        @if ($user_type == 'super_admin')
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
                                {{-- <span class="badge badge-round badge-success ms-1">50</span> --}}
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.point managment.users points') }}
                                {{-- <span class="badge badge-round badge-success ms-1">50</span> --}}
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
                                <span class="badge badge-round badge-success ms-1">50</span>
                            </a>
                        </li>
                        <li>
                            <a class="d-flex align-items-center justify-content-between fw-bold"
                                href="{{ route('admin.dashboard') }}">
                                {{ trans('menu.advertisements.positions') }}
                                <span class="badge badge-round badge-success ms-1">50</span>
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
                    <a href="{{ route('admin.system-settings.activity-logs.index') }}">
                        <span class="nav-icon uil uil-history"></span>
                        <span class="menu-text">{{ trans('menu.system log.title') }}</span>
                    </a>
                </li>
            @endcan

            @canany(['area.country.index', 'area.city.index', 'area.region.index', 'area.subregion.index'])
                <li class="has-child">
                    <a href="#" class="">
                        <span class="nav-icon uil uil-map-marker"></span>
                        <span class="menu-text">{{ trans('menu.area settings.title') }}</span>
                        <span class="toggle-icon"></span>
                    </a>
                    <ul class="px-0">
                        @can('area.country.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.area-settings.countries.index') }}">
                                    {{ trans('menu.area settings.country') }}
                                    <span class="badge badge-round badge-success ms-1">15</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.city.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.area-settings.cities.index') }}">
                                    {{ trans('menu.area settings.city') }}
                                    <span class="badge badge-round badge-info ms-1">120</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.region.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
                                    href="{{ route('admin.area-settings.regions.index') }}">
                                    {{ trans('menu.area settings.region') }}
                                    <span class="badge badge-round badge-warning ms-1">45</span>
                                </a>
                            </li>
                        @endcan

                        @can('area.subregion.index')
                            <li>
                                <a class="d-flex align-items-center justify-content-between fw-bold"
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
                    <a href="{{ route('admin.system-settings.currencies.index') }}">
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
