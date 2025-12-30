

<?php $__env->startSection('title'); ?>
    <?php echo e(isset($department) ? trans('categorymanagment::department.edit_department') : trans('categorymanagment::department.create_department')); ?>

<?php $__env->stopSection(); ?>


<?php $__env->startPush('styles'); ?>
    <style>
        /* Ensure validation messages are always visible */
        .invalid-feedback {
            display: block !important;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* RTL support for Arabic validation messages */
        input[data-lang="ar"]+.invalid-feedback,
        textarea[data-lang="ar"]+.invalid-feedback {
            direction: rtl;
            text-align: right;
        }

        /* Highlight invalid fields with red border */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        /* Select2 invalid state styling */
        .select2.is-invalid+.select2-container .select2-selection,
        .select2-container.is-invalid .select2-selection {
            border-color: #dc3545 !important;
        }

        /* Smooth transition for error states */
        .form-control {
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
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
                        'title' => trans('categorymanagment::department.departments_management'),
                        'url' => route('admin.category-management.departments.index'),
                    ],
                    [
                        'title' => isset($department)
                            ? trans('categorymanagment::department.edit_department')
                            : trans('categorymanagment::department.create_department'),
                    ],
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
                        'title' => trans('categorymanagment::department.departments_management'),
                        'url' => route('admin.category-management.departments.index'),
                    ],
                    [
                        'title' => isset($department)
                            ? trans('categorymanagment::department.edit_department')
                            : trans('categorymanagment::department.create_department'),
                    ],
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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            <?php echo e(isset($department) ? trans('categorymanagment::department.edit_department') : trans('categorymanagment::department.create_department')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="departmentForm"
                            action="<?php echo e(isset($department) ? route('admin.category-management.departments.update', $department->id) : route('admin.category-management.departments.store')); ?>"
                            method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($department)): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="row">
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="translation_<?php echo e($language->id); ?>_name"
                                                class="il-gray fs-14 fw-500 mb-10"
                                                <?php if($language->rtl): ?> dir="rtl" style="text-align: right; display: block;" <?php endif; ?>>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($language->code == 'ar'): ?>
                                                    الاسم (<?php echo e($language->name); ?>) <span class="text-danger">*</span>
                                                <?php else: ?>
                                                    <?php echo e(trans('categorymanagment::department.name_english')); ?>

                                                    (<?php echo e($language->name); ?>)
                                                    <span class="text-danger">*</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 <?php $__errorArgs = ['translations.' . $language->id . '.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="translation_<?php echo e($language->id); ?>_name"
                                                name="translations[<?php echo e($language->id); ?>][name]"
                                                value="<?php echo e(isset($department) ? $department->getTranslation('name', $language->code) ?? '' : old('translations.' . $language->id . '.name')); ?>"
                                                placeholder="<?php if($language->code == 'ar'): ?> أدخل اسم القسم بالعربية<?php else: ?><?php echo e(trans('categorymanagment::department.enter_department_name_english')); ?> <?php endif; ?>"
                                                <?php if($language->rtl): ?> dir="rtl" <?php endif; ?>
                                                data-lang="<?php echo e($language->code); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['translations.' . $language->id . '.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback d-block" style="display: block !important;">
                                                    <?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6 mb-25">
                                        <div class="form-group">
                                            <label for="translation_<?php echo e($language->id); ?>_description"
                                                class="il-gray fs-14 fw-500 mb-10"
                                                <?php if($language->rtl): ?> dir="rtl" style="text-align: right; display: block;" <?php endif; ?>>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($language->code == 'ar'): ?>
                                                    الوصف (<?php echo e($language->name); ?>)
                                                <?php else: ?>
                                                    <?php echo e(trans('categorymanagment::department.description')); ?>

                                                    (<?php echo e($language->name); ?>)
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </label>
                                            <textarea
                                                class="form-control ip-gray radius-xs b-light px-15 <?php $__errorArgs = ['translations.' . $language->id . '.description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                id="translation_<?php echo e($language->id); ?>_description" name="translations[<?php echo e($language->id); ?>][description]"
                                                rows="4"
                                                placeholder="<?php if($language->code == 'ar'): ?> أدخل وصف القسم بالعربية<?php else: ?><?php echo e(trans('categorymanagment::department.enter_department_name_english')); ?> <?php endif; ?>"
                                                <?php if($language->rtl): ?> dir="rtl" <?php endif; ?> data-lang="<?php echo e($language->code); ?>"><?php echo e(isset($department) ? $department->getTranslation('description', $language->code) ?? '' : old('translations.' . $language->id . '.description')); ?></textarea>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['translations.' . $language->id . '.description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback d-block" style="display: block !important;">
                                                    <?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <div class="col-md-6 mb-25">
                                    <?php if (isset($component)) { $__componentOriginaldbebdfa49a0907927fe266159631a348 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbebdfa49a0907927fe266159631a348 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-upload','data' => ['id' => 'department_image','name' => 'image','label' => trans('categorymanagment::department.image'),'placeholder' => trans('categorymanagment::department.click_to_upload_image'),'recommendedSize' => trans('categorymanagment::department.recommended_size'),'existingImage' => isset($department) && $department->image ? $department->image : null,'aspectRatio' => 'square']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'department_image','name' => 'image','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('categorymanagment::department.image')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('categorymanagment::department.click_to_upload_image')),'recommendedSize' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('categorymanagment::department.recommended_size')),'existingImage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($department) && $department->image ? $department->image : null),'aspectRatio' => 'square']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldbebdfa49a0907927fe266159631a348)): ?>
<?php $attributes = $__attributesOriginaldbebdfa49a0907927fe266159631a348; ?>
<?php unset($__attributesOriginaldbebdfa49a0907927fe266159631a348); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldbebdfa49a0907927fe266159631a348)): ?>
<?php $component = $__componentOriginaldbebdfa49a0907927fe266159631a348; ?>
<?php unset($__componentOriginaldbebdfa49a0907927fe266159631a348); ?>
<?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <?php if (isset($component)) { $__componentOriginaldbebdfa49a0907927fe266159631a348 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbebdfa49a0907927fe266159631a348 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-upload','data' => ['id' => 'department_icon','name' => 'icon','label' => trans('categorymanagment::department.icon'),'placeholder' => trans('categorymanagment::department.click_to_upload_icon'),'recommendedSize' => trans('categorymanagment::department.recommended_size_for_icon'),'existingImage' => isset($department) && $department->icon ? $department->icon : null,'aspectRatio' => 'square']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'department_icon','name' => 'icon','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('categorymanagment::department.icon')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('categorymanagment::department.click_to_upload_icon')),'recommendedSize' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('categorymanagment::department.recommended_size_for_icon')),'existingImage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($department) && $department->icon ? $department->icon : null),'aspectRatio' => 'square']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldbebdfa49a0907927fe266159631a348)): ?>
<?php $attributes = $__attributesOriginaldbebdfa49a0907927fe266159631a348; ?>
<?php unset($__attributesOriginaldbebdfa49a0907927fe266159631a348); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldbebdfa49a0907927fe266159631a348)): ?>
<?php $component = $__componentOriginaldbebdfa49a0907927fe266159631a348; ?>
<?php unset($__componentOriginaldbebdfa49a0907927fe266159631a348); ?>
<?php endif; ?>
                                </div>


                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('categorymanagment::department.activation')); ?>

                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox" class="form-check-input" id="active"
                                                    name="active" value="1"
                                                    <?php echo e(old('active', $department->active ?? 1) == 1 ? 'checked' : ''); ?>>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger fs-12 mt-1"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('categorymanagment::department.view_status') ?? 'View Status'); ?>

                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="view_status" value="0">
                                                <input type="checkbox" class="form-check-input" id="view_status"
                                                    name="view_status" value="1"
                                                    <?php echo e(old('view_status', $department->view_status ?? 1) == 1 ? 'checked' : ''); ?>>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['view_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger fs-12 mt-1"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="sort_number" class="il-gray fs-14 fw-500 mb-10">
                                            <?php echo e(trans('categorymanagment::department.sort_number') ?? 'Sort Number'); ?>

                                        </label>
                                        <input type="number"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 <?php $__errorArgs = ['sort_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="sort_number" name="sort_number"
                                            value="<?php echo e(old('sort_number', $department->sort_number ?? 0)); ?>" min="0">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sort_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback d-block" style="display: block !important;">
                                                <?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="commission" class="il-gray fs-14 fw-500 mb-10">
                                            <?php echo e(trans('categorymanagment::department.commission')); ?><span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                            class="form-control ih-medium ip-gray radius-xs b-light px-15 <?php $__errorArgs = ['commission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="commission" name="commission"
                                            value="<?php echo e(old('commission', $department->commission ?? 0)); ?>" min="0"
                                            max="100" step="0.01">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['commission'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback d-block" style="display: block !important;">
                                                <?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>


                            </div>

                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="<?php echo e(route('admin.category-management.departments.index')); ?>"
                                    class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> <?php echo e(trans('categorymanagment::department.cancel')); ?>

                                </a>
                                <button type="submit" id="submitBtn"
                                    class="btn btn-primary btn-default btn-squared text-capitalize"
                                    style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span><?php echo e(isset($department) ? trans('categorymanagment::department.update_department') : trans('categorymanagment::department.add_department')); ?></span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            if ($.fn.select2) {
                $('.select2-ajax').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '<?php echo e(trans('categorymanagment::department.select_activities')); ?>',
                    allowClear: true,
                    ajax: {
                        url: function() {
                            return $(this).data('ajax-url');
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0
                });
            }
            document.addEventListener('DOMContentLoaded', function() {
                // AJAX Form Submission
                const departmentForm = document.getElementById('departmentForm');
                const submitBtn = document.getElementById('submitBtn');
                const alertContainer = document.getElementById('alertContainer');

                // Clear validation errors on input
                departmentForm.querySelectorAll('input, textarea').forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.classList.contains('is-invalid')) {
                            this.classList.remove('is-invalid');
                            const feedback = this.parentNode.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.remove();
                            }
                        }
                    });
                });

                // Clear validation errors on select2 change
                $('.select2').on('change', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.remove();
                        }
                        // Also remove invalid class from Select2 container
                        const select2Container = this.parentNode.querySelector('.select2-container');
                        if (select2Container) {
                            select2Container.classList.remove('is-invalid');
                        }
                    }
                });

                departmentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Disable submit button and show loading
                    submitBtn.disabled = true;
                    const btnIcon = submitBtn.querySelector('i');
                    const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                    if (btnIcon) btnIcon.classList.add('d-none');
                    if (btnText) btnText.classList.add('d-none');
                    submitBtn.querySelector('.spinner-border').classList.remove('d-none');

                    // Update loading text dynamically
                    const loadingText = <?php echo json_encode(isset($department) ? trans('loading.updating') : trans('loading.creating'), 15, 512) ?>;
                    const loadingSubtext = '<?php echo e(trans('loading.please_wait')); ?>';
                    const overlay = document.getElementById('loadingOverlay');
                    if (overlay) {
                        overlay.querySelector('.loading-text').textContent = loadingText;
                        overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
                    }

                    // Show loading overlay
                    LoadingOverlay.show();

                    // Clear previous alerts
                    alertContainer.innerHTML = '';

                    // Remove previous validation errors
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                    // Start progress bar animation
                    LoadingOverlay.animateProgressBar(30, 300).then(() => {
                            // Prepare form data
                            const formData = new FormData(departmentForm);

                            // Send AJAX request
                            return fetch(departmentForm.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            });
                        })
                        .then(response => {
                            // Progress to 60%
                            LoadingOverlay.animateProgressBar(60, 200);

                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw data;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Progress to 90%
                            return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
                        })
                        .then(data => {
                            // Complete progress bar
                            return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                                // Show success animation with dynamic message
                                const successMessage = <?php echo json_encode(isset($department) ? trans('loading.updated_successfully') : trans('loading.created_successfully'), 15, 512) ?>;
                                LoadingOverlay.showSuccess(
                                    successMessage,
                                    '<?php echo e(trans('loading.redirecting')); ?>'
                                );

                                // Show success alert
                                showAlert('success', data.message || successMessage);

                                // Redirect after 1.5 seconds
                                setTimeout(() => {
                                    window.location.href = data.redirect ||
                                        '<?php echo e(route('admin.category-management.departments.index')); ?>';
                                }, 1500);
                            });
                        })
                        .catch(error => {
                            // Hide loading overlay and reset progress bar
                            LoadingOverlay.hide();

                            // Handle validation errors
                            if (error.errors) {
                                let errorCount = 0;
                                Object.keys(error.errors).forEach(key => {
                                    // Handle both dot notation and bracket notation for nested fields
                                    const inputName = key.replace(/\./g, '][').replace(/^/, '')
                                        .replace(/\]$/, '');
                                    let input = document.querySelector(`[name="${key}"]`);

                                    // Try alternative selectors for nested fields
                                    if (!input) {
                                        const bracketKey = key.replace(/\./g, '][');
                                        input = document.querySelector(`[name="${bracketKey}"]`);
                                    }
                                    if (!input) {
                                        const parts = key.split('.');
                                        if (parts.length === 3) {
                                            // translations.1.name -> translations[1][name]
                                            input = document.querySelector(
                                                `[name="${parts[0]}[${parts[1]}][${parts[2]}]"]`
                                            );
                                        }
                                    }
                                    // Handle activities array errors (activities.0, activities.1, etc.)
                                    if (!input && key.startsWith('activities')) {
                                        input = document.querySelector('[name="activities[]"]');
                                    }

                                    if (input) {
                                        errorCount++;
                                        input.classList.add('is-invalid');

                                        // Remove existing feedback to avoid duplicates
                                        const existingFeedback = input.parentNode.querySelector(
                                            '.invalid-feedback');
                                        if (existingFeedback) {
                                            existingFeedback.remove();
                                        }

                                        const feedback = document.createElement('div');
                                        feedback.className = 'invalid-feedback d-block';
                                        feedback.style.display = 'block';
                                        feedback.textContent = error.errors[key][0];
                                        input.parentNode.appendChild(feedback);

                                        // For Select2, also add invalid class to the Select2 container
                                        if (input.classList.contains('select2')) {
                                            const select2Container = input.parentNode.querySelector(
                                                '.select2-container');
                                            if (select2Container) {
                                                select2Container.classList.add('is-invalid');
                                            }
                                        }

                                        // Scroll to first error
                                        if (errorCount === 1) {
                                            input.scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'center'
                                            });
                                        }
                                    }
                                });

                                const errorMessage = error.message ||
                                    '<?php echo e(__('Please check the form for errors')); ?>';
                                showAlert('danger', errorMessage +
                                    ` (${errorCount} ${errorCount === 1 ? 'error' : 'errors'})`);
                            } else {
                                showAlert('danger', error.message || '<?php echo e(__('An error occurred')); ?>');
                            }

                            // Re-enable submit button
                            submitBtn.disabled = false;
                            const btnIcon = submitBtn.querySelector('i');
                            const btnText = submitBtn.querySelector('span:not(.spinner-border)');
                            if (btnIcon) btnIcon.classList.remove('d-none');
                            if (btnText) btnText.classList.remove('d-none');
                            submitBtn.querySelector('.spinner-border').classList.add('d-none');
                        });
                });

                // Show alert function
                function showAlert(type, message) {
                    const alert = document.createElement('div');
                    alert.className = `alert alert-${type} alert-dismissible fade show mb-20`;
                    alert.innerHTML = `
                    <i class="uil uil-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                    alertContainer.appendChild(alert);

                    // Scroll to top to show alert
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('after-body'); ?>
    <?php if (isset($component)) { $__componentOriginal115e82920da0ed7c897ee494af74b9d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal115e82920da0ed7c897ee494af74b9d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loading-overlay','data' => ['loadingText' => trans('loading.processing'),'loadingSubtext' => trans('loading.please_wait')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('loading-overlay'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['loadingText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('loading.processing')),'loadingSubtext' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('loading.please_wait'))]); ?>
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

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CategoryManagment\resources/views/department/form.blade.php ENDPATH**/ ?>