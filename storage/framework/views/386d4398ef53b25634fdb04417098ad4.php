<style>
    .ap-po-details__titlebar h1 {
        font-weight: bold;
        color: var(--color-primary);
    }

    .ap-po-details__titlebar p {
        font-weight: bold !important;
    }

    .stat-card-link {
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-decoration: none;
        display: block;
    }

    .stat-card-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card-link:hover .ap-po-details {
        border-color: var(--color-primary);
    }
</style>
<div class="col-12">
    <div class="row">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.admin-management.admins.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_admins'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_admins')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-user-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.vendor-users-management.vendor-users.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['vendor_users'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.vendor_users')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-users-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.vendors.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_vendors'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_vendors')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-store"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.vendor-requests.index', ['status' => 'pending'])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['become_vendor_requests'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.become_vendor_requests')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-file-question-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.vendors.index', ['active' => 1])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['accepted_vendors'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.accepted_vendors')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.vendors.index', ['active' => 0])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['rejected_vendors'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.rejected_vendors')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-times-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.vendors.index', ['date_from' => now()->subDays(30)->format('Y-m-d')])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['new_vendors'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.new_vendors')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-plus-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.customers.index', ['gender' => 'male'])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_male_users'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_male_customers')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-mars"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.customers.index', ['gender' => 'female'])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_female_users'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_female_customers')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-venus"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.customers.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_customers'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_customers')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-shopping-bag"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.admin-management.roles.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['admins_total_roles'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.admins_total_roles')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-shield-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.vendor-users-management.roles.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['vendor_users_total_roles'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.vendor_users_total_roles')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-shield"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.products.index', ['stock' => 'instock'])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['instock'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.instock')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-check-square"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.products.index', ['stock' => 'outofstock'])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['out_of_stock'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.out_of_stock')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-times-square"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.orders.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_orders'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_orders')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.taxes.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_taxes'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_taxes')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-receipt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.messages.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_messages'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_messages')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-envelope"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.promocodes.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['promocodes'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.promocodes')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-tag-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.area-settings.countries.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['country'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.country')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-globe"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.area-settings.cities.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['city'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.city')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-building"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.area-settings.regions.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['region'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.region')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-map"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.reviews.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['all_products_reviews'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.all_products_reviews')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.reviews.index', ['status' => 'approved'])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['accept_products_reviews'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.accept_products_reviews')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-thumbs-up"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.reviews.index', ['status' => 'rejected'])); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['reject_products_reviews'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.reject_products_reviews')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-thumbs-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.order-stages.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_order_stages'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_order_stages')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-process"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
        
        <div class="col-12 col-md-4 mb-25">
            <a href="<?php echo e(route('admin.system-settings.ads.index')); ?>" class="stat-card-link" target="_blank">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e($stats['total_advertisments'] ?? 0); ?></h1>
                                <p><?php echo e(trans('dashboard.total_advertisments')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-megaphone"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/stats-cards.blade.php ENDPATH**/ ?>