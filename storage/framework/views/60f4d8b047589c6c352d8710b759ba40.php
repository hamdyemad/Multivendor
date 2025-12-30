

<?php $__env->startSection('title', trans('catalogmanagement::bundle.view_bundle')); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Bundle View HTML Content Styling */
.fs-15.color-dark {
    line-height: 1.6;
}

.fs-15.color-dark table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.fs-15.color-dark table th,
.fs-15.color-dark table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e3e6f0;
}

.fs-15.color-dark table th {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 12px;
}

.fs-15.color-dark table tr:hover {
    background-color: #f8f9fa;
}

.fs-15.color-dark table tr:last-child td {
    border-bottom: none;
}

.fs-15.color-dark strong {
    color: #2c3e50;
    font-weight: 600;
}

.fs-15.color-dark em {
    color: #7f8c8d;
    font-style: italic;
}

.fs-15.color-dark ul,
.fs-15.color-dark ol {
    margin: 10px 0;
    padding-left: 20px;
}

.fs-15.color-dark li {
    margin-bottom: 5px;
    line-height: 1.5;
}

.fs-15.color-dark p {
    margin-bottom: 10px;
    line-height: 1.6;
}

.fs-15.color-dark h1,
.fs-15.color-dark h2,
.fs-15.color-dark h3,
.fs-15.color-dark h4,
.fs-15.color-dark h5,
.fs-15.color-dark h6 {
    margin: 15px 0 10px 0;
    color: #2c3e50;
    font-weight: 600;
}

.fs-15.color-dark blockquote {
    border-left: 4px solid #4e73df;
    padding-left: 15px;
    margin: 15px 0;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
}

.fs-15.color-dark a {
    color: #4e73df;
    text-decoration: none;
}

.fs-15.color-dark a:hover {
    color: #224abe;
    text-decoration: underline;
}

/* Arabic content styling */
.fs-15.color-dark[style*="direction: rtl"] {
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.fs-15.color-dark[style*="direction: rtl"] table th,
.fs-15.color-dark[style*="direction: rtl"] table td {
    text-align: right;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('catalogmanagement::bundle.bundles_management'),
                        'url' => route('admin.bundles.index'),
                    ],
                    ['title' => trans('catalogmanagement::bundle.view_bundle')],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => trans('catalogmanagement::bundle.bundles_management'),
                        'url' => route('admin.bundles.index'),
                    ],
                    ['title' => trans('catalogmanagement::bundle.view_bundle')],
                ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500"><?php echo e(trans('catalogmanagement::bundle.bundle_details')); ?></h5>
                        <div class="d-flex gap-10">
                            <a href="<?php echo e(route('admin.bundles.index')); ?>" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i><?php echo e(trans('common.back_to_list')); ?>

                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bundles.edit')): ?>
                                <a href="<?php echo e(route('admin.bundles.edit', $bundle->id)); ?>" class="btn btn-primary btn-sm">
                                    <i class="uil uil-edit me-2"></i><?php echo e(trans('common.edit')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i><?php echo e(trans('catalogmanagement::bundle.basic_information')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.name')); ?></label>
                                                    <div class="row">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $translation = $bundle->getTranslation('name', $lang->code);
                                                            ?>
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; <?php if($lang->code == 'ar'): ?> border-right: 3px solid #5f63f2; <?php else: ?> border-left: 3px solid #5f63f2; <?php endif; ?>">
                                                                    <small class="text-muted d-block mb-2" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; <?php endif; ?>">
                                                                        <span class="badge <?php if($lang->code == 'en'): ?> bg-primary <?php else: ?> bg-success <?php endif; ?> text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;"><?php echo e(strtoupper($lang->code)); ?></span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; <?php endif; ?>">
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($translation): ?>
                                                                            <?php echo e($translation); ?>

                                                                        <?php else: ?>
                                                                            <span class="text-muted">—</span>
                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-12">
                                                <div class="view-item box-items-translations">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.description')); ?></label>
                                                    <div class="row">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $translation = $bundle->getTranslation('description', $lang->code);
                                                            ?>
                                                            <div class="col-md-6 mb-3">
                                                                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; <?php if($lang->code == 'ar'): ?> border-right: 3px solid #5f63f2; <?php else: ?> border-left: 3px solid #5f63f2; <?php endif; ?>">
                                                                    <small class="text-muted d-block mb-2" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; <?php endif; ?>">
                                                                        <span class="badge <?php if($lang->code == 'en'): ?> bg-primary <?php else: ?> bg-success <?php endif; ?> text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;"><?php echo e(strtoupper($lang->code)); ?></span>
                                                                    </small>
                                                                    <div class="fs-15 color-dark mb-0 fw-500" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; <?php endif; ?>">
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($translation): ?>
                                                                            <?php echo nl2br(e($translation)); ?>

                                                                        <?php else: ?>
                                                                            <span class="text-muted">—</span>
                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.vendor')); ?></label>
                                                    <p class="fs-15">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bundle->vendor): ?>
                                                            <span class="badge badge-round badge-primary badge-lg">
                                                                <?php echo e($bundle->vendor->getTranslation('name', app()->getLocale()) ?? $bundle->vendor->getTranslation('name', 'en') ?? $bundle->vendor->getTranslation('name', 'ar') ?? '-'); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.category')); ?></label>
                                                    <p class="fs-15">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bundle->bundleCategory): ?>
                                                            <span class="badge badge-round badge-primary badge-lg">
                                                                <?php echo e($bundle->bundleCategory->getTranslation('name', app()->getLocale()) ?? $bundle->bundleCategory->getTranslation('name', 'en') ?? $bundle->bundleCategory->getTranslation('name', 'ar') ?? '-'); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.sku')); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <code><?php echo e($bundle->sku ?? '-'); ?></code>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.status')); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bundle->is_active): ?>
                                                            <span class="badge badge-round badge-success badge-lg">
                                                                <i class="uil uil-check-circle me-1"></i><?php echo e(trans('catalogmanagement::bundle.active')); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge badge-round badge-danger badge-lg">
                                                                <i class="uil uil-times-circle me-1"></i><?php echo e(trans('catalogmanagement::bundle.inactive')); ?>

                                                            </span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.approval_status') ?? 'Approval Status'); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bundle->admin_approval === 1): ?>
                                                            <span class="badge badge-round badge-success badge-lg">
                                                                <i class="uil uil-check-circle me-1"></i><?php echo e(trans('catalogmanagement::bundle.approved')); ?>

                                                            </span>
                                                        <?php elseif($bundle->admin_approval === 2): ?>
                                                            <span class="badge badge-round badge-danger badge-lg">
                                                                <i class="uil uil-times-circle me-1"></i><?php echo e(trans('catalogmanagement::bundle.rejected')); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge badge-round badge-warning badge-lg">
                                                                <i class="uil uil-clock me-1"></i><?php echo e(trans('catalogmanagement::bundle.pending_approval')); ?>

                                                            </span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bundle->admin_approval === 2 && $bundle->approval_reason): ?>
                                                <div class="col-md-12">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.rejection_reason') ?? 'Rejection Reason'); ?></label>
                                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                            <i class="uil uil-info-circle me-2"></i>
                                                            <p class="fs-15 color-dark fw-500 mb-0">
                                                                <?php echo e($bundle->approval_reason); ?>

                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.created_at')); ?></label>
                                                    <p class="fs-15 color-dark fw-500">
                                                        <i class="uil uil-calendar-alt me-1"></i><?php echo e($bundle->created_at ? $bundle->created_at : '-'); ?>

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 order-1 order-md-2">
                                
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i><?php echo e(trans('catalogmanagement::bundle.image')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php
                                            $imageAttachment = $bundle->main_image;
                                        ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageAttachment): ?>
                                            <img src="<?php echo e(asset('storage/' . $imageAttachment->path)); ?>" alt="Bundle Image" class="img-fluid round" style="max-width: 100%; max-height: 300px;">
                                        <?php else: ?>
                                            <div class="p-5 bg-light round">
                                            <img src="<?php echo e(asset('assets/img/default.png')); ?>" alt="Bundle Image" class="img-fluid round" style="max-width: 100%; max-height: 300px;">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="card card-holder mt-3">
                                <div class="card-header">
                                    <h3>
                                        <i class="uil uil-search me-1"></i><?php echo e(trans('catalogmanagement::bundle.seo_information')); ?>

                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.seo_title')); ?></label>
                                                <div class="row">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $translation = $bundle->getTranslation('seo_title', $lang->code);
                                                        ?>
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; <?php if($lang->code == 'ar'): ?> border-right: 3px solid #5f63f2; <?php else: ?> border-left: 3px solid #5f63f2; <?php endif; ?>">
                                                                <small class="text-muted d-block mb-2" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; <?php endif; ?>">
                                                                    <span class="badge <?php if($lang->code == 'en'): ?> bg-primary <?php else: ?> bg-success <?php endif; ?> text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;"><?php echo e(strtoupper($lang->code)); ?></span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; <?php endif; ?>">
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($translation): ?>
                                                                        <?php echo e($translation); ?>

                                                                    <?php else: ?>
                                                                        <span class="text-muted">—</span>
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.seo_description')); ?></label>
                                                <div class="row">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $translation = $bundle->getTranslation('seo_description', $lang->code);
                                                        ?>
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; <?php if($lang->code == 'ar'): ?> border-right: 3px solid #5f63f2; <?php else: ?> border-left: 3px solid #5f63f2; <?php endif; ?>">
                                                                <small class="text-muted d-block mb-2" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; <?php endif; ?>">
                                                                    <span class="badge <?php if($lang->code == 'en'): ?> bg-primary <?php else: ?> bg-success <?php endif; ?> text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;"><?php echo e(strtoupper($lang->code)); ?></span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0 fw-500" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; <?php endif; ?>">
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($translation): ?>
                                                                        <?php echo e($translation); ?>

                                                                    <?php else: ?>
                                                                        <span class="text-muted">—</span>
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-12">
                                            <div class="view-item box-items-translations">
                                                <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('catalogmanagement::bundle.seo_keywords')); ?></label>
                                                <div class="row">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $translation = $bundle->getTranslation('seo_keywords', $lang->code);
                                                            $keywords = [];
                                                            if ($translation) {
                                                                // Try to decode as JSON first (if stored as JSON array)
                                                                $decoded = json_decode($translation, true);
                                                                if (is_array($decoded)) {
                                                                    $keywords = $decoded;
                                                                } else {
                                                                    // Otherwise split by comma
                                                                    $keywords = array_map('trim', explode(',', $translation));
                                                                    $keywords = array_filter($keywords); // Remove empty values
                                                                }
                                                            }
                                                        ?>
                                                        <div class="col-md-6 mb-3">
                                                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; <?php if($lang->code == 'ar'): ?> border-right: 3px solid #5f63f2; <?php else: ?> border-left: 3px solid #5f63f2; <?php endif; ?>">
                                                                <small class="text-muted d-block mb-2" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; <?php endif; ?>">
                                                                    <span class="badge badge-lg badge-round <?php if($lang->code == 'en'): ?> bg-primary <?php else: ?> bg-success <?php endif; ?> text-white px-2 py-1 round-pill fw-bold" style="font-size: 10px;"><?php echo e(strtoupper($lang->code)); ?></span>
                                                                </small>
                                                                <div class="fs-15 color-dark mb-0" style="<?php if($lang->code == 'ar'): ?> direction: rtl; text-align: right; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; <?php endif; ?>">
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($keywords) > 0): ?>
                                                                        <div class="d-flex flex-wrap gap-2">
                                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $keywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keyword): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <span class="badge badge-lg badge-round bg-info text-white" style="font-size: 12px; padding: 6px 10px;">
                                                                                    <?php echo e(trim($keyword)); ?>

                                                                                </span>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">—</span>
                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <?php echo $__env->make('catalogmanagement::bundles.bundle-products-table', ['bundle' => $bundle, 'showDragHandle' => false, 'showActions' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-body'); ?>

<?php if (isset($component)) { $__componentOriginal115e82920da0ed7c897ee494af74b9d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal115e82920da0ed7c897ee494af74b9d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loading-overlay','data' => ['loadingText' => ''.e(trans('main.deleting')).'','loadingSubtext' => ''.e(trans('main.please wait')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('loading-overlay'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['loadingText' => ''.e(trans('main.deleting')).'','loadingSubtext' => ''.e(trans('main.please wait')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal115e82920da0ed7c897ee494af74b9d8)): ?>
<?php $attributes = $__attributesOriginal115e82920da0ed7c897ee494af74b9d8; ?>
<?php unset($__attributesOriginal115e82920da0ed7c897ee494af74b9d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal115e82920da0ed7c897ee494af74b9d8)): ?>
<?php $component = $__componentOriginal115e82920da0ed7c897ee494af74b9d8; ?>
<?php unset($__componentOriginal115e82920da0ed7c897ee494af74b9d8); ?>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/bundles/show.blade.php ENDPATH**/ ?>