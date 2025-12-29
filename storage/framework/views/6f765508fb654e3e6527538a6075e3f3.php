<?php
    $countries = collect();
    $currentCountryCode = 'EG';
    $currentCountry = null;

    try {
        $countries = \Modules\AreaSettings\app\Models\Country::where('active', 1)->get();
        // Get country code from session or default to EG
        $currentCountryCode = strtoupper(session('country_code', 'EG'));
        $currentCountry = $countries->firstWhere('code', $currentCountryCode) ?? $countries->first();
    } catch (\Exception $e) {
        // Silently fail - use defaults
        $countries = collect();
        $currentCountryCode = 'EG';
        $currentCountry = null;
    }

?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds())): ?>
<li class="nav-flag-select nav-country-select">
    <div class="dropdown-custom">
        <a href="javascript:;" class="nav-item-toggle d-flex" title="<?php echo e(__('dashboard.select_country')); ?>">
           <div class="d-flex align-items-center" style="    background: #065ab9;
            padding: 2px 20px;
            border-radius: 5px;
            color: #fff;">
             <i class="uil uil-globe me-1" style="font-size: 20px;"></i>
                <div><?php echo e(__('dashboard.select_country')); ?></div>
            <div>
                <span class="nav-item__title ms-1 badge badge-round badge-lg badge-white" style="color:#065ab9">
                    <?php echo e($currentCountryCode); ?>

                </span>
            </div>
            </div>

        </a>
        <div class="dropdown-wrapper dropdown-wrapper--small">
            <div class="dropdown-header px-3 py-2 border-bottom">
                <small class="text-muted fw-bold"><?php echo e(__('dashboard.select_country')); ?></small>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    // Build the new URL with the selected country code
                    $currentUrl = request()->getPathInfo();
                    $segments = explode('/', trim($currentUrl, '/'));

                    // URL format: /{lang}/{country}/admin/...
                    if (count($segments) >= 3 && $segments[2] === 'admin') {
                        // Replace segment 1 (country code) with new country code
                        $segments[1] = strtolower($country->code);
                    }

                    $newUrl = '/' . implode('/', $segments);
                ?>
                <a href="<?php echo e($newUrl); ?>" class="country-item <?php echo e(strtoupper($country->code) == $currentCountryCode ? 'active' : ''); ?>">
                    <span class="country-code-badge me-2">
                        <?php echo e(strtoupper($country->code)); ?>

                    </span>
                    <span class="country-name">
                        <?php echo e($country->getTranslation('name', app()->getLocale()) ?? $country->getTranslation('name', 'en') ?? $country->code); ?>

                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strtoupper($country->code) == $currentCountryCode): ?>
                        <i class="uil uil-check ms-auto text-success"></i>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</li>

<style>
    .nav-country-select .dropdown-wrapper {
        min-width: 200px;
    }

    .nav-country-select .dropdown-header {
        background-color: #f8f9fa;
    }

    .nav-country-select .country-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        transition: all 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .nav-country-select .country-item:last-child {
        border-bottom: none;
    }

    .nav-country-select .country-item:hover {
        background-color: rgba(95, 99, 242, 0.08);
    }

    .nav-country-select .country-item.active {
        background-color: rgba(95, 99, 242, 0.12);
        color: #5f63f2;
    }

    .nav-country-select .country-code-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 22px;
        padding: 0 6px;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #5f63f2 0%, #8e92f7 100%);
        border-radius: 4px;
        letter-spacing: 0.5px;
    }

    .nav-country-select .country-item.active .country-code-badge {
        background: linear-gradient(135deg, #20c997 0%, #38d9a9 100%);
    }

    .nav-country-select .country-name {
        flex: 1;
        font-size: 13px;
    }

    .nav-country-select .nav-item-toggle {
        display: flex;
        align-items: center;
    }

    /* RTL Support */
    html[dir="rtl"] .nav-country-select .country-code-badge {
        margin-left: 8px;
        margin-right: 0;
    }

    html[dir="rtl"] .nav-country-select .country-item .uil-check {
        margin-right: auto;
        margin-left: 0;
    }
</style>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/top_nav/_country_selector.blade.php ENDPATH**/ ?>