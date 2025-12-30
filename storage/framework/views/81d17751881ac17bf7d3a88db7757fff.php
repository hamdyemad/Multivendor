
<?php $__env->startSection('title', isset($occasion) ? trans('catalogmanagement::occasion.edit_occasion') :
    trans('catalogmanagement::occasion.add_occasion')); ?>

    <?php $__env->startPush('styles'); ?>
        <style>
            /* Search Results Dropdown */
            .variant-search-container {
                position: relative;
            }

            .variant-search-results {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #fff;
                border: 1px solid #e0e0e0;
                border-top: none;
                border-radius: 0 0 8px 8px;
                max-height: 350px;
                overflow-y: auto;
                z-index: 1000;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .variant-search-item {
                display: flex;
                align-items: center;
                padding: 12px 15px;
                border-bottom: 1px solid #f0f0f0;
                cursor: pointer;
                transition: background 0.2s ease;
            }

            .variant-search-item:hover {
                background: #f8f9fa;
            }

            .variant-search-item:last-child {
                border-bottom: none;
            }

            .variant-search-item .variant-image {
                width: 50px;
                height: 50px;
                border-radius: 6px;
                margin-right: 12px;
                flex-shrink: 0;
            }

            .variant-search-item .variant-info {
                flex: 1;
                min-width: 0;
            }

            .variant-search-item .variant-title {
                font-weight: 600;
                font-size: 14px;
                color: #333;
                margin-bottom: 4px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .variant-search-item .variant-details {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                font-size: 12px;
            }

            .variant-search-item .variant-badge {
                display: inline-flex;
                align-items: center;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: 500;
            }

            .variant-search-item .variant-badge.variant-name {
                background: #e3f2fd;
                color: #1565c0;
            }

            .variant-search-item .variant-badge.price {
                background: #e8f5e9;
                color: #2e7d32;
            }

            .variant-search-item .variant-badge.price-discount {
                background: #fff3e0;
                color: #e65100;
            }

            .variant-search-item .variant-badge.stock {
                background: #f3e5f5;
                color: #7b1fa2;
            }

            /* Selected Products Cards */
            .product-card {
                border: 2px solid #e0e0e0;
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 15px;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #ffffff;
            }

            .product-card:hover {
                border-color: var(--color-primary);
                box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            }

            .product-card.selected {
                border-color: #28a745;
                background-color: #f8fff8;
            }

            .product-card .product-image {
                width: 60px;
                height: 60px;
                border-radius: 8px;
            }

            .product-card .product-checkbox {
                position: absolute;
                top: 10px;
                right: 10px;
            }

            .product-card .product-info h6 {
                margin-bottom: 5px;
                font-weight: 600;
            }

            .product-card .product-info .product-meta {
                font-size: 0.875rem;
                color: #6c757d;
            }

            /* No results message */
            .no-results-message {
                padding: 20px;
                text-align: center;
                color: #6c757d;
            }

            /* Selected Variants Container */
            .selected-variants-container {
                max-height: 400px;
                overflow-y: auto;
            }

            /* Products Container */
            #products-container {
                max-height: 500px;
                overflow-y: auto;
            }

            #products-container::-webkit-scrollbar {
                width: 6px;
            }

            #products-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            #products-container::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }

            #products-container::-webkit-scrollbar-thumb:hover {
                background: #a1a1a1;
            }

            .selected-variant-item {
                transition: background 0.2s ease;
            }

            .selected-variant-item:hover {
                background: #f8f9fa;
            }

            /* Gradient backgrounds */
            .bg-gradient-success {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            }

            /* Soft badges */
            .badge-soft-primary {
                background-color: rgba(13, 110, 253, 0.1);
                color: #0d6efd;
            }

            .badge-soft-success {
                background-color: rgba(25, 135, 84, 0.1);
                color: #198754;
            }

            .badge-soft-danger {
                background-color: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }

            .badge-soft-warning {
                background-color: rgba(255, 193, 7, 0.15);
                color: #cc9a06;
            }

            .badge-soft-info {
                background-color: rgba(13, 202, 240, 0.1);
                color: #0dcaf0;
            }

            /* Remove button hover */
            .remove-variant-btn:hover {
                background-color: #dc3545 !important;
                border-color: #dc3545 !important;
                color: #fff !important;
            }

            /* Custom price input styling */
            .custom-price-input {
                border-radius: 6px 0 0 6px !important;
                border-color: #e0e0e0;
            }

            .custom-price-input:focus {
                border-color: var(--color-primary);
                box-shadow: 0 0 0 0.2rem rgba(var(--color-primary-rgb), 0.15);
            }

            .custom-price-input::placeholder {
                color: #adb5bd;
                font-style: italic;
            }

            /* Validation states */
            .custom-price-input.is-invalid {
                border-color: #dc3545;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            }

            .alert {
                border: none;
                border-radius: 8px;
            }

            .alert-warning {
                background-color: rgba(255, 193, 7, 0.1);
                color: #856404;
                border-left: 4px solid #ffc107;
            }

            .alert-danger {
                background-color: rgba(220, 53, 69, 0.1);
                color: #721c24;
                border-left: 4px solid #dc3545;
            }

            /* Error feedback styling */
            .error-feedback {
                font-size: 0.875rem;
                color: #dc3545;
            }

            .is-invalid {
                border-color: #dc3545 !important;
            }

            .image-upload-container.is-invalid {
                border-color: #dc3545 !important;
            }

            /* Scrollbar styling for selected variants */
            .selected-variants-container::-webkit-scrollbar {
                width: 6px;
            }

            .selected-variants-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            .selected-variants-container::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }

            .selected-variants-container::-webkit-scrollbar-thumb:hover {
                background: #a1a1a1;
            }
        </style>
    <?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid mb-4">
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
                        'title' => trans('catalogmanagement::occasion.occasions_management'),
                        'url' => route('admin.occasions.index'),
                    ],
                    [
                        'title' => isset($occasion)
                            ? trans('catalogmanagement::occasion.edit_occasion')
                            : trans('catalogmanagement::occasion.add_occasion'),
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
                        'title' => trans('catalogmanagement::occasion.occasions_management'),
                        'url' => route('admin.occasions.index'),
                    ],
                    [
                        'title' => isset($occasion)
                            ? trans('catalogmanagement::occasion.edit_occasion')
                            : trans('catalogmanagement::occasion.add_occasion'),
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
                            <?php echo e(isset($occasion) ? trans('catalogmanagement::occasion.edit_occasion') : trans('catalogmanagement::occasion.add_occasion')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer" class="mb-2"></div>

                        <form id="occasionForm"
                            action="<?php echo e(isset($occasion) ? route('admin.occasions.update', $occasion->id) : route('admin.occasions.store')); ?>"
                            method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($occasion)): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'name','label' => trans('catalogmanagement::occasion.name'),'labelAr' => 'اسم العرض','placeholder' => trans('catalogmanagement::occasion.enter_occasion_name'),'placeholderAr' => 'اسم العرض','languages' => $languages,'model' => $occasion ?? null,'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.name')),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('اسم العرض'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.enter_occasion_name')),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('اسم العرض'),'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($occasion ?? null),'required' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $attributes = $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $component = $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>

                            
                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'title','label' => trans('catalogmanagement::occasion.title'),'labelAr' => 'العنوان','placeholder' => trans('catalogmanagement::occasion.enter_occasion_title'),'placeholderAr' => 'العنوان','languages' => $languages,'model' => $occasion ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'title','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.title')),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('العنوان'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.enter_occasion_title')),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('العنوان'),'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($occasion ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $attributes = $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $component = $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>

                            
                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'sub_title','label' => trans('catalogmanagement::occasion.sub_title'),'labelAr' => 'العنوان الفرعى','placeholder' => trans('catalogmanagement::occasion.enter_occasion_sub_title'),'placeholderAr' => 'العنوان الفرعى','languages' => $languages,'model' => $occasion ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sub_title','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.sub_title')),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('العنوان الفرعى'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.enter_occasion_sub_title')),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('العنوان الفرعى'),'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($occasion ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $attributes = $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $component = $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="start_date" class="il-gray fs-14 fw-500 mb-10">
                                            <?php echo e(trans('catalogmanagement::occasion.start_date')); ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15 <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="start_date" name="start_date" required
                                            value="<?php echo e(old('start_date', isset($occasion) ? $occasion->start_date?->format('Y-m-d') : '')); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label for="end_date" class="il-gray fs-14 fw-500 mb-10">
                                            <?php echo e(trans('catalogmanagement::occasion.end_date')); ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15 <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="end_date" name="end_date" required
                                            value="<?php echo e(old('end_date', isset($occasion) ? $occasion->end_date?->format('Y-m-d') : '')); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <?php if (isset($component)) { $__componentOriginaldbebdfa49a0907927fe266159631a348 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbebdfa49a0907927fe266159631a348 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-upload','data' => ['id' => 'occasion_image','name' => 'image','placeholder' => trans('catalogmanagement::occasion.image'),'recommendedSize' => trans('catalogmanagement::occasion.recommended_size'),'existingImage' => isset($occasion) && $occasion->image
                                                ? asset('storage/' . $occasion->image)
                                                : null,'aspectRatio' => '16:9','required' => true,'hasError' => $errors->has('image')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'occasion_image','name' => 'image','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.image')),'recommendedSize' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.recommended_size')),'existingImage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($occasion) && $occasion->image
                                                ? asset('storage/' . $occasion->image)
                                                : null),'aspectRatio' => '16:9','required' => true,'hasError' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->has('image'))]); ?>
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
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-25">
                                    <div class="form-group">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            <?php echo e(trans('catalogmanagement::occasion.activation')); ?>

                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" class="form-check-input <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="is_active"
                                                    name="is_active" value="1"
                                                    <?php echo e(old('is_active', $occasion->is_active ?? 1) == 1 ? 'checked' : ''); ?>>
                                            </div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row" id="variantsSection">
                                <div class="col-12">
                                    <h6 class="mb-20 fw-500"><?php echo e(trans('catalogmanagement::occasion.product_variants')); ?>

                                    </h6>
                                </div>
                                
                                <div class="col-12 mb-25">
                                    <div class="form-group">
                                        <label for="product_search" class="il-gray fs-14 fw-500 mb-10">
                                            <?php echo e(trans('catalogmanagement::occasion.search_products')); ?>

                                        </label>
                                        <input type="text" id="product_search" class="form-control"
                                               placeholder="<?php echo e(trans('catalogmanagement::occasion.type_to_search_products')); ?>"
                                               style="width: 100%;">
                                        <small class="text-muted"><?php echo e(trans('catalogmanagement::occasion.search_products_help')); ?></small>
                                    </div>
                                </div>

                                
                                <div class="col-12 mb-25">
                                    <div id="products-grid" class="row" style="max-height: 500px; overflow-y: auto; border: 1px solid #e9ecef; border-radius: 0.375rem; padding: 15px;">
                                        <div class="col-12 text-center text-muted py-5">
                                            <i class="uil uil-search fs-1 mb-2"></i>
                                            <p><?php echo e(trans('catalogmanagement::occasion.search_products_help')); ?></p>
                                        </div>
                                    </div>

                                    
                                    <div id="products-loader" style="display: none; text-align: center; padding: 20px;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden"><?php echo e(__('common.loading')); ?></span>
                                        </div>
                                        <p class="text-muted mt-2"><?php echo e(trans('catalogmanagement::occasion.loading_products')); ?></p>
                                    </div>
                                </div>


                                
                                <div class="col-12 mb-25">
                                    <h6 class="mb-3 fw-500"><?php echo e(trans('catalogmanagement::occasion.selected_products')); ?></h6>
                                    <div id="selected-products" class="row" style="min-height: 100px;">
                                        <div class="col-12 text-center text-muted py-3">
                                            <p><?php echo e(trans('catalogmanagement::occasion.no_products_selected')); ?></p>
                                        </div>
                                    </div>
                                </div>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has('variants') || $errors->has('variants.*')): ?>
                                    <div class="col-12">
                                        <div class="invalid-feedback d-block">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['variants'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <?php echo e($message); ?>

                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->get('variants.*'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variantErrors): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $variantErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo e($error); ?><br>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </div>

                            
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="p-0 fw-500 fw-bold">
                                        <?php echo e(trans('catalogmanagement::occasion.seo_information')); ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        
                                        <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'seo_title','label' => trans('catalogmanagement::occasion.seo_title'),'labelAr' => 'عنوان ال SEO','placeholder' => trans('catalogmanagement::occasion.enter_seo_title'),'placeholderAr' => 'عنوان ال SEO','languages' => $languages,'model' => $occasion ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'seo_title','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.seo_title')),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('عنوان ال SEO'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.enter_seo_title')),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('عنوان ال SEO'),'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($occasion ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $attributes = $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $component = $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>

                                        
                                        <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'seo_description','label' => trans('catalogmanagement::occasion.seo_description'),'labelAr' => 'وصف SEO','placeholder' => trans('catalogmanagement::occasion.enter_seo_description'),'placeholderAr' => 'وصف SEO','type' => 'textarea','rows' => '3','languages' => $languages,'model' => $occasion ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'seo_description','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.seo_description')),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('وصف SEO'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.enter_seo_description')),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('وصف SEO'),'type' => 'textarea','rows' => '3','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($occasion ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $attributes = $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $component = $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>

                                        
                                        <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'seo_keywords','label' => trans('catalogmanagement::occasion.seo_keywords'),'labelAr' => 'الكلمات المفتاحية','placeholder' => trans('catalogmanagement::occasion.type_keyword_press_enter'),'placeholderAr' => 'الكلمات المفتاحية','tags' => true,'languages' => $languages,'model' => $occasion ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'seo_keywords','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.seo_keywords')),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('الكلمات المفتاحية'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('catalogmanagement::occasion.type_keyword_press_enter')),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('الكلمات المفتاحية'),'tags' => true,'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($occasion ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $attributes = $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb)): ?>
<?php $component = $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb; ?>
<?php unset($__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb); ?>
<?php endif; ?>
                                    </div>
                                </div>
                            </div>


                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="<?php echo e(route('admin.occasions.index')); ?>"
                                    class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> <?php echo e(trans('common.cancel')); ?>

                                </a>
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-default btn-squared text-capitalize"
                                    style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span><?php echo e(isset($occasion) ? trans('common.update') : trans('common.save')); ?></span>
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
            // Global variables for product selection
            let selectedProducts = [];
            let selectedProductsDetails = {};
            let allProducts = [];

            // Search products function
            function searchProducts(searchTerm = '', page = 1) {
                // Show loader
                $('#products-loader').show();
                $('#products-grid').hide();

                $.ajax({
                    url: '/api/products',
                    type: 'GET',
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        'X-Country-Code': $('meta[name="currency_country_code"]').attr("content"),
                    },
                    data: {
                        search: searchTerm,
                        country_id: $("meta[name='current_country_id']").attr("content"),
                        page: page,
                        per_page: 15,
                        paginated: 'ok',
                    },
                    success: function(response) {
                        console.log('Products response:', response);

                        // Hide loader
                        $('#products-loader').hide();
                        $('#products-grid').show();

                        if (response.status && response.data && response.data.length > 0) {
                            allProducts = [];
                            let productsHtml = '';
                            const currencySymbol = '<?php echo e(currency()); ?>';

                            response.data.forEach(function(vendorProduct) {
                                const productImage = vendorProduct.image || '<?php echo e(asset('assets/img/logo.png')); ?>';
                                const productName = vendorProduct.name || 'N/A';
                                const variants = vendorProduct.variants || [];
                                
                                // Get vendor info
                                const vendor = vendorProduct.vendor || {};
                                const vendorName = vendor.name || 'N/A';
                                const vendorLogo = vendor.logo || "<?php echo e(asset('assets/img/default-vendor.png')); ?>";

                                if (variants.length > 0) {
                                    variants.forEach(function(variant) {
                                        const variantId = variant.id;
                                        const variantName = variant.variant_name || variant.name || '';
                                        const variantKey = variant.variant_key || '';
                                        const variantValue = variant.variant_value || variantName || '';
                                        const variantSku = variant.sku || 'N/A';
                                        // Use remaining_stock instead of stock
                                        const remainingStock = variant.remaining_stock ?? variant.stock ?? 0;
                                        const price = variant.real_price;
                                        const priceBeforeDiscount = variant.fake_price;
                                        const isSelected = selectedProducts.includes(variantId);
                                        const isOutOfStock = remainingStock <= 0;

                                        allProducts.push({
                                            id: variantId,
                                            productName: productName,
                                            variantName: variantName,
                                            variantKey: variantKey,
                                            variantValue: variantValue,
                                            sku: variantSku,
                                            stock: remainingStock,
                                            price: price,
                                            priceBeforeDiscount: priceBeforeDiscount,
                                            image: productImage,
                                            vendorName: vendorName,
                                            vendorLogo: vendorLogo
                                        });

                                        const disabledClass = isOutOfStock ? 'opacity-50' : '';
                                        const stockClass = remainingStock > 0 ? 'text-success' : 'text-danger fw-bold';
                                        const stockText = isOutOfStock ? `${remainingStock} (<?php echo e(trans('catalogmanagement::occasion.out_of_stock')); ?>)` : remainingStock;

                                        productsHtml += `
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100 product-card ${disabledClass}" data-product-id="${variantId}">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex gap-2 mb-2">
                                                            <img src="${productImage}" alt="${productName}"
                                                                 class="rounded" style="width: 50px; height: 50px;  flex-shrink: 0;">
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1 fw-semibold text-truncate">${productName}</h6>
                                                                ${variantKey ? `<small class="text-primary d-block"><strong>${variantKey}:</strong> ${variantValue}</small>` : ''}
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column gap-1 mb-2">
                                                            <small class="text-muted"><strong><?php echo e(trans('catalogmanagement::occasion.sku')); ?>:</strong> ${variantSku}</small>
                                                            <small><strong><?php echo e(trans('catalogmanagement::occasion.remaining_stock')); ?>:</strong> <span class="${stockClass}">${stockText}</span></small>
                                                            <small class="text-muted"><strong><?php echo e(trans('catalogmanagement::occasion.original_price')); ?>:</strong> ${price} ${currencySymbol}</small>
                                                            ${priceBeforeDiscount ? `<small class="text-muted"><strong><?php echo e(trans('common.before_discount')); ?>:</strong> ${priceBeforeDiscount} ${currencySymbol}</small>` : ''}
                                                        </div>
                                                        <div class="d-flex align-items-center mb-2 pt-2 border-top">
                                                            <img src="${vendorLogo}" alt="${vendorName}" class="rounded-circle me-2" style="width: 20px; height: 20px;">
                                                            <small class="text-primary fw-500">${vendorName}</small>
                                                        </div>
                                                        <button type="button" class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-primary'} w-100 add-product-btn"
                                                                data-product-id="${variantId}" ${isSelected || isOutOfStock ? 'disabled' : ''}>
                                                            <i class="uil ${isSelected ? 'uil-check' : 'uil-plus'} me-1"></i>${isSelected ? '<?php echo e(trans('common.added')); ?>' : (isOutOfStock ? '<?php echo e(trans('catalogmanagement::occasion.out_of_stock')); ?>' : '<?php echo e(trans('common.add')); ?>')}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });
                                }
                            });

                            $('#products-grid').html(productsHtml);
                        } else {
                            $('#products-loader').hide();
                            $('#products-grid').show();
                            $('#products-grid').html(`
                                <div class="col-12 text-center text-muted py-5">
                                    <i class="uil uil-inbox fs-1 mb-2"></i>
                                    <p><?php echo e(trans('catalogmanagement::occasion.no_products_found')); ?></p>
                                </div>
                            `);
                        }
                    },
                    error: function(error) {
                        console.error('Error loading products:', error);
                        $('#products-loader').hide();
                        $('#products-grid').show();
                        $('#products-grid').html(`
                            <div class="col-12 text-center text-danger py-5">
                                <i class="uil uil-exclamation-triangle fs-1 mb-2"></i>
                                <p><?php echo e(trans('catalogmanagement::occasion.error_loading_data')); ?></p>
                            </div>
                        `);
                    }
                });
            }

            // Search input handler
            $('#product_search').on('keyup', function() {
                const searchTerm = $(this).val();
                searchProducts(searchTerm, 1);
            });

            // Add product button handler
            $(document).on('click', '.add-product-btn', function(e) {
                e.preventDefault();
                const productId = parseInt($(this).data('product-id'));
                const product = allProducts.find(p => p.id === productId);

                if (product && !selectedProducts.includes(productId)) {
                    selectedProducts.push(productId);
                    selectedProductsDetails[productId] = product;
                    updateSelectedProductsDisplay();

                    // Update button state
                    $(this).addClass('btn-success').removeClass('btn-primary').prop('disabled', true);
                    $(this).html('<i class="uil uil-check me-1"></i>Added');
                }
            });

            // Update selected products display
            function updateSelectedProductsDisplay() {
                const count = selectedProducts.length;
                const currencySymbol = '<?php echo e(currency()); ?>';

                if (count > 0) {
                    let selectedHtml = '';
                    selectedProducts.forEach(function(variantId, index) {
                        const variant = selectedProductsDetails[variantId];
                        if (variant) {
                            const vendorLogo = variant.vendorLogo || '<?php echo e(asset('assets/img/default-vendor.png')); ?>';
                            const vendorName = variant.vendorName || 'N/A';
                            const stockClass = variant.stock > 0 ? 'text-success' : 'text-danger';
                            
                            selectedHtml += `
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-0 shadow-sm h-100 product-card" data-product-id="${variantId}">
                                        <div class="card-body p-3">
                                            <div class="d-flex gap-2 mb-2">
                                                <img src="${variant.image}" alt="${variant.productName}"
                                                     class="rounded" style="width: 50px; height: 50px;  flex-shrink: 0;">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold text-truncate">${variant.productName}</h6>
                                                    ${variant.variantKey ? `<small class="text-primary d-block"><strong>${variant.variantKey}:</strong> ${variant.variantValue || variant.variantName}</small>` : (variant.variantName ? `<small class="text-primary d-block">${variant.variantName}</small>` : '')}
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="${vendorLogo}" alt="${vendorName}" class="rounded-circle me-2" style="width: 20px; height: 20px; ">
                                                <small class="text-primary fw-500">${vendorName}</small>
                                            </div>
                                            <div class="d-flex flex-column gap-1 mb-2">
                                                <small class="text-muted"><strong><?php echo e(trans('catalogmanagement::occasion.sku')); ?>:</strong> ${variant.sku}</small>
                                                <small><strong><?php echo e(trans('catalogmanagement::occasion.remaining_stock')); ?>:</strong> <span class="${stockClass}">${variant.stock}</span></small>
                                                <small class="text-muted"><strong><?php echo e(trans('catalogmanagement::occasion.original_price')); ?>:</strong> ${variant.price} ${currencySymbol}</small>
                                                ${variant.priceBeforeDiscount ? `<small class="text-muted"><strong><?php echo e(trans('common.before_discount')); ?>:</strong> ${variant.priceBeforeDiscount} ${currencySymbol}</small>` : ''}
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fs-13 fw-500"><?php echo e(trans('catalogmanagement::occasion.special_price')); ?></label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm special-price-input"
                                                       placeholder="<?php echo e(trans('catalogmanagement::occasion.special_price')); ?>" data-product-id="${variantId}"
                                                       value="${variant.specialPrice || ''}">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger w-100 remove-selected-btn"
                                                    data-product-id="${variantId}">
                                                <i class="uil uil-trash-alt me-1"></i><?php echo e(trans('common.remove')); ?>

                                            </button>
                                            <input type="hidden" name="variants[${index}][vendor_product_variant_id]" value="${variantId}">
                                            <input type="hidden" name="variants[${index}][special_price]" class="special-price-hidden-${variantId}" value="">
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });

                    $('#selected-products').html(selectedHtml);
                } else {
                    $('#selected-products').html(`
                        <div class="col-12 text-center text-muted py-3">
                            <p><?php echo e(trans('catalogmanagement::occasion.no_products_selected')); ?></p>
                        </div>
                    `);
                }
            }

            // Remove product from selected list
            $(document).on('click', '.remove-selected-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const productId = parseInt($(this).data('product-id'));
                console.log('Removing product:', productId);

                // Remove from selected products array
                selectedProducts = selectedProducts.filter(id => id !== productId);
                delete selectedProductsDetails[productId];

                // Update button state in products grid
                $(`.add-product-btn[data-product-id="${productId}"]`)
                    .removeClass('btn-success').addClass('btn-primary')
                    .prop('disabled', false)
                    .html('<i class="uil uil-plus me-1"></i>Add');

                updateSelectedProductsDisplay();
                console.log('Updated selected products:', selectedProducts);
            });

            // Handle special price input change
            $(document).on('change keyup', '.special-price-input', function() {
                const productId = parseInt($(this).data('product-id'));
                const specialPrice = $(this).val();

                // Update the hidden input
                $(`.special-price-hidden-${productId}`).val(specialPrice);

                // Update the stored product details
                if (selectedProductsDetails[productId]) {
                    selectedProductsDetails[productId].specialPrice = specialPrice;
                }

                console.log('Updated special price for product', productId, ':', specialPrice);
            });



                // Trigger change on page load if vendor is already selected
                if ($('#vendor_id').val()) {
                    $('#variantsSection').show();
                }

                <?php if(isset($occasion) && $occasion->occasionProducts->count() > 0): ?>
                    // Load existing occasion products for edit mode
                    $(document).ready(function() {
                        <?php
                            $existingVariantsData = $occasion->occasionProducts->map(function ($item) {
                                $vpv = $item->vendorProductVariant;
                                $vendorProduct = $vpv?->vendorProduct;
                                $product = $vendorProduct?->product;
                                $vendor = $vendorProduct?->vendor;
                                $variantConfig = $vpv?->variantConfiguration;
                                
                                return [
                                    'id' => $item->vendor_product_variant_id,
                                    'special_price' => $item->special_price,
                                    'position' => $item->position,
                                    'variant' => $vpv
                                        ? [
                                            'id' => $vpv->id,
                                            'sku' => $vpv->sku,
                                            'variant_name' => $vpv->variant_name,
                                            'variant_key' => $variantConfig?->key?->name,
                                            'variant_value' => $variantConfig?->name,
                                            'stock' => $vpv->remaining_stock ?? 0,
                                            'real_price' => $vpv->price,
                                            'fake_price' => $vpv->price_before_discount,
                                        ]
                                        : null,
                                    'product' => $product
                                        ? [
                                            'name' => $product->title,
                                            'image' => $product->mainImage ? formatImage($product->mainImage) : null,
                                        ]
                                        : null,
                                    'vendor' => $vendor
                                        ? [
                                            'name' => $vendor->name,
                                            'logo' => formatImage($vendor->logo),
                                        ]
                                        : null,
                                ];
                            });
                        ?>
                        const existingVariants = <?php echo json_encode($existingVariantsData, 15, 512) ?>;
                        const defaultImage = '<?php echo e(asset('assets/img/default.png')); ?>';
                        const defaultVendorLogo = '<?php echo e(asset('assets/img/default-vendor.png')); ?>';

                        // Add existing variants to selected products
                        existingVariants.forEach(function(item) {
                            if (item.variant && item.product) {
                                const variantId = item.variant.id;

                                // Add to selected products array
                                if (!selectedProducts.includes(variantId)) {
                                    selectedProducts.push(variantId);
                                }

                                // Store variant details
                                selectedProductsDetails[variantId] = {
                                    productName: item.product.name,
                                    variantName: item.variant.variant_name || '',
                                    variantKey: item.variant.variant_key || '',
                                    variantValue: item.variant.variant_value || '',
                                    sku: item.variant.sku,
                                    stock: item.variant.stock || 0,
                                    price: item.variant.real_price || '0.00',
                                    priceBeforeDiscount: item.variant.fake_price || null,
                                    image: item.product.image || defaultImage,
                                    vendorName: item.vendor?.name || 'N/A',
                                    vendorLogo: item.vendor?.logo || defaultVendorLogo,
                                    specialPrice: item.special_price
                                };
                            }
                        });

                        // Update the display with existing variants
                        updateSelectedProductsDisplay();

                        // Set the special price values for existing products
                        existingVariants.forEach(function(item) {
                            const variantId = item.variant.id;
                            const specialPrice = item.special_price;
                            if (specialPrice) {
                                $(`.special-price-input[data-product-id="${variantId}"]`).val(specialPrice);
                                $(`.special-price-hidden-${variantId}`).val(specialPrice);
                            }
                        });
                    });
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                $("#submitBtn").on("click", function(e) {
                    e.preventDefault();
                    const form = $("#occasionForm")[0];
                    const submitBtn = $("#submitBtn");
                    const alertContainer = $("#alertContainer");
                    const spinner = submitBtn.find(".spinner-border");
                    spinner.removeClass("d-none");
                    submitBtn.prop("disabled", true);

                    // Sync CKEditor data before form submission
                    if (typeof CKEDITOR !== 'undefined') {
                        for (const instanceName in CKEDITOR.instances) {
                            const instance = CKEDITOR.instances[instanceName];
                            const textarea = document.getElementById(instanceName);
                            if (textarea) {
                                textarea.value = instance.getData();
                            }
                        }
                    }

                    // Use FormData to include file uploads
                    const formData = new FormData(form);

                    $.ajax({
                        url: $(form).attr("action"),
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            spinner.addClass("d-none");
                            submitBtn.prop("disabled", false);

                            // Show success message with toastr
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message, 'Success');
                            } else {
                                alertContainer.html('<div class="alert alert-success">' + response.message + '</div>');
                            }

                            // Redirect after 2 seconds
                            setTimeout(function() {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                            }, 2000);
                        },
                        error: function(xhr, status, error) {
                            spinner.addClass("d-none");
                            submitBtn.prop("disabled", false);

                            const response = xhr.responseJSON;
                            const errors = response.errors || {};

                            // Show error message with toastr
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'An error occurred', 'Error');
                            }

                            // Clear previous errors
                            $('.is-invalid').removeClass('is-invalid');
                            $('.invalid-feedback').remove();
                            $('.error-feedback').remove();

                            // Show errors under each input
                            for (const key in errors) {
                                const errorMessages = errors[key];
                                const errorText = Array.isArray(errorMessages) ? errorMessages[0] : errorMessages;
                                
                                let inputElement = null;
                                let errorContainer = null;

                                // Handle translation fields (translations.1.name, translations.2.name)
                                if (key.startsWith('translations.')) {
                                    const parts = key.split('.');
                                    const langId = parts[1];
                                    const fieldName = parts[2];
                                    inputElement = $(`[name="translations[${langId}][${fieldName}]"]`);
                                }
                                // Handle variants errors
                                else if (key === 'variants' || key.startsWith('variants.')) {
                                    // Show error under selected products section
                                    const variantsError = `<div class="error-feedback text-danger mt-2">${errorText}</div>`;
                                    if ($('#selected-products .error-feedback').length === 0) {
                                        $('#selected-products').after(variantsError);
                                    }
                                    continue;
                                }
                                // Handle image field
                                else if (key === 'image') {
                                    const imageContainer = $('#occasion_image').closest('.form-group');
                                    if (imageContainer.length) {
                                        imageContainer.find('.image-upload-container').addClass('is-invalid');
                                        if (imageContainer.find('.error-feedback').length === 0) {
                                            imageContainer.append(`<div class="error-feedback text-danger mt-2">${errorText}</div>`);
                                        }
                                    }
                                    continue;
                                }
                                // Handle regular fields
                                else {
                                    inputElement = $(`[name="${key}"]`);
                                }

                                if (inputElement && inputElement.length) {
                                    inputElement.addClass('is-invalid');
                                    // Add error message after input
                                    if (inputElement.next('.error-feedback').length === 0) {
                                        inputElement.after(`<div class="error-feedback invalid-feedback d-block">${errorText}</div>`);
                                    }
                                }
                            }

                            window.scrollTo(0, 0);
                        }
                    });


                });

                // Clear error on input focus
                $(document).on('focus', 'input, select, textarea', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-feedback').remove();
                    $(this).next('.invalid-feedback').remove();
                });

                // Clear image error on click
                $(document).on('click', '.image-upload-container', function() {
                    $(this).removeClass('is-invalid');
                    $(this).closest('.form-group').find('.error-feedback').remove();
                });

                // Clear variants error when adding a product
                $(document).on('click', '.add-product-btn', function() {
                    $('#selected-products').next('.error-feedback').remove();
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

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/occasions/form.blade.php ENDPATH**/ ?>