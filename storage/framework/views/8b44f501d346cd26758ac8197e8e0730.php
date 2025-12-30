

<?php $__env->startSection('title', trans('categorymanagment::subcategory.view_subcategory')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('categorymanagment::subcategory.subcategories_management'), 'url' => route('admin.category-management.subcategories.index')],
                    ['title' => trans('categorymanagment::subcategory.view_subcategory')]
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('categorymanagment::subcategory.subcategories_management'), 'url' => route('admin.category-management.subcategories.index')],
                    ['title' => trans('categorymanagment::subcategory.view_subcategory')]
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
                        <h5 class="mb-0 fw-500"><?php echo e(trans('categorymanagment::subcategory.subcategory_details')); ?></h5>
                        <div class="d-flex gap-10">
                            <a href="<?php echo e(route('admin.category-management.subcategories.index')); ?>" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i><?php echo e(trans('common.back_to_list')); ?>

                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sub-categories.edit')): ?>
                                <a href="<?php echo e(route('admin.category-management.subcategories.edit', $subCategory->id)); ?>" class="btn btn-primary btn-sm">
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
                                            <i class="uil uil-info-circle me-1"></i><?php echo e(trans('common.basic_information')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => trans('categorymanagment::subcategory.name'),'model' => $subCategory,'fieldName' => 'name','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('categorymanagment::subcategory.name')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subCategory),'fieldName' => 'name','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5a3093cad4b0bccb881a74044179ded)): ?>
<?php $attributes = $__attributesOriginale5a3093cad4b0bccb881a74044179ded; ?>
<?php unset($__attributesOriginale5a3093cad4b0bccb881a74044179ded); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5a3093cad4b0bccb881a74044179ded)): ?>
<?php $component = $__componentOriginale5a3093cad4b0bccb881a74044179ded; ?>
<?php unset($__componentOriginale5a3093cad4b0bccb881a74044179ded); ?>
<?php endif; ?>
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => trans('categorymanagment::subcategory.description'),'model' => $subCategory,'fieldName' => 'description','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('categorymanagment::subcategory.description')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subCategory),'fieldName' => 'description','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5a3093cad4b0bccb881a74044179ded)): ?>
<?php $attributes = $__attributesOriginale5a3093cad4b0bccb881a74044179ded; ?>
<?php unset($__attributesOriginale5a3093cad4b0bccb881a74044179ded); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5a3093cad4b0bccb881a74044179ded)): ?>
<?php $component = $__componentOriginale5a3093cad4b0bccb881a74044179ded; ?>
<?php unset($__componentOriginale5a3093cad4b0bccb881a74044179ded); ?>
<?php endif; ?>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('categorymanagment::subcategory.category')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subCategory->category): ?>
                                                            <span class="badge badge-info badge-round badge-lg"><?php echo e($subCategory->category->getTranslation('name', app()->getLocale())); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('categorymanagment::subcategory.activation')); ?></label>
                                                    <p class="fs-15">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subCategory->active): ?>
                                                            <span class="badge badge-success badge-round badge-lg"><?php echo e(trans('categorymanagment::subcategory.active')); ?></span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger badge-round badge-lg"><?php echo e(trans('categorymanagment::subcategory.inactive')); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i><?php echo e(trans('common.timestamps')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.created_at')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($subCategory->created_at); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.updated_at')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($subCategory->updated_at); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i><?php echo e(trans('categorymanagment::subcategory.image')); ?>

                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subCategory->image): ?>
                                            <div class="image-wrapper">
                                                <img src="<?php echo e(asset('storage/' . $subCategory->image)); ?>"
                                                alt="<?php echo e($subCategory->getTranslation('name', app()->getLocale())); ?>"
                                                class="category-image img-fluid">
                                            </div>
                                        <?php else: ?>
                                            <div class="image-wrapper">
                                                <img src="<?php echo e(asset('assets/img/default.png')); ?>"
                                                alt="<?php echo e($subCategory->getTranslation('name', app()->getLocale())); ?>"
                                                class="category-image img-fluid">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal428f5f1760e699cb50a829dfa3984f87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal428f5f1760e699cb50a829dfa3984f87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal428f5f1760e699cb50a829dfa3984f87)): ?>
<?php $attributes = $__attributesOriginal428f5f1760e699cb50a829dfa3984f87; ?>
<?php unset($__attributesOriginal428f5f1760e699cb50a829dfa3984f87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal428f5f1760e699cb50a829dfa3984f87)): ?>
<?php $component = $__componentOriginal428f5f1760e699cb50a829dfa3984f87; ?>
<?php unset($__componentOriginal428f5f1760e699cb50a829dfa3984f87); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CategoryManagment\resources/views/subcategory/view.blade.php ENDPATH**/ ?>