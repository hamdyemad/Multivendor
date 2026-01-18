<?php
    $countryCode = session('country_code') ?? 'eg';
   $country = Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->first();
?>
<!doctype html>
<html lang="<?php echo e(LaravelLocalization::getCurrentLocale()); ?>" dir="<?php echo e((LaravelLocalization::getCurrentLocale()=='ar' ? 'rtl' : 'ltr')); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country): ?>
        <meta name="currency_country_code" content="<?php echo e($country->code); ?>">
        <meta name="current_country_id" content="<?php echo e($country->id); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="<?php echo $__env->yieldContent('description'); ?>"/>
    <title><?php echo e(__('common.project_name') . ' |'); ?> <?php echo $__env->yieldContent('title'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugin.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/variables.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.min.css')); ?>">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\LaravelLocalization::getCurrentLocale() == 'ar'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugin.rtl.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.rtl.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/app.rtl.min.css')); ?>">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.min.css')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('assets/img/favicon-bnaia.png')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/unicons/line.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/plugins/toastr.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/select2-bootstrap-5-theme.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/select2.min.css')); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/progress-bar.scss']); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/rtl-validation.scss']); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/scss/app.scss'); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo $__env->yieldContent('styles'); ?>
    <script src="<?php echo e(asset('js/chart.umd.min.js')); ?>"></script>

    <style>
        /* Fix scrollbar for minimized sidebar submenus */
        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul {
            max-height: 80vh !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        /* Custom scrollbar styling */
        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>



</head>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/_header.blade.php ENDPATH**/ ?>