

<?php $__env->startSection('title'); ?>
    <?php echo e($title ?? (isset($product) ? __('catalogmanagement::product.edit_product') : __('catalogmanagement::product.create_product'))); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['Modules/CatalogManagement/resources/assets/scss/product-form.scss']); ?>
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
                        'title' => __('catalogmanagement::product.products_management'),
                        'url' => route('admin.products.index'),
                    ],
                    [
                        'title' => isset($product)
                            ? __('catalogmanagement::product.edit_product')
                            : __('catalogmanagement::product.create_product'),
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
                        'title' => __('catalogmanagement::product.products_management'),
                        'url' => route('admin.products.index'),
                    ],
                    [
                        'title' => isset($product)
                            ? __('catalogmanagement::product.edit_product')
                            : __('catalogmanagement::product.create_product'),
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
                <div class="card product-form">
                    <div class="card-header">
                        <h4 class="card-title">
                            <?php echo e(isset($product) ? __('catalogmanagement::product.edit_product') : __('catalogmanagement::product.create_product')); ?>

                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Wizard Navigation -->
                        <?php if (isset($component)) { $__componentOriginal3f30168eb99a06e730e11b595d6f2979 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3f30168eb99a06e730e11b595d6f2979 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.wizard','data' => ['steps' => [
                            __('common.basic_information'),
                            __('common.details'),
                            __('common.variant_configurations'),
                            __('common.seo'),
                        ],'currentStep' => 1]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('wizard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['steps' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                            __('common.basic_information'),
                            __('common.details'),
                            __('common.variant_configurations'),
                            __('common.seo'),
                        ]),'currentStep' => 1]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3f30168eb99a06e730e11b595d6f2979)): ?>
<?php $attributes = $__attributesOriginal3f30168eb99a06e730e11b595d6f2979; ?>
<?php unset($__attributesOriginal3f30168eb99a06e730e11b595d6f2979); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3f30168eb99a06e730e11b595d6f2979)): ?>
<?php $component = $__componentOriginal3f30168eb99a06e730e11b595d6f2979; ?>
<?php unset($__componentOriginal3f30168eb99a06e730e11b595d6f2979); ?>
<?php endif; ?>

                        <!-- Validation Alerts Container -->
                        <div id="validation-alerts-container" class="mb-3"></div>

                        <!-- Tax Information Alert -->
                        <?php
                            // Get current product taxes
                            $productTaxIds = [];
                            if (isset($product)) {
                                $vendorProduct = $product->product ? $product : $product;
                                if ($vendorProduct && method_exists($vendorProduct, 'taxes')) {
                                    $productTaxIds = $vendorProduct->taxes->pluck('id')->toArray();
                                }
                            }
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($taxes) && count($taxes) > 0): ?>
                        <div class="mb-4">
                            <div class="p-3 rounded" style="background: rgba(255, 193, 7, 0.1); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 193, 7, 0.3); box-shadow: 0 4px 15px rgba(255, 193, 7, 0.1);">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="p-2 rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(255, 193, 7, 0.2); min-width: 40px; height: 40px;">
                                        <i class="uil uil-exclamation-triangle fs-5" style="color: #ffc107;"></i>
                                    </div>
                                    <div class="w-100">
                                        <h6 class="mb-2 fw-bold" style="color: #856404;"><?php echo e(__('catalogmanagement::product.tax_notice')); ?></h6>
                                        
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($productTaxIds) > 0): ?>
                                        <p class="mb-2 small" style="color: #856404;"><?php echo e(__('catalogmanagement::product.current_product_taxes')); ?>:</p>
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $taxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($tax['id'], $productTaxIds)): ?>
                                                <span class="badge badge-round badge-lg px-3 py-2" style="background: rgba(40, 167, 69, 0.2); color: #155724; font-size: 13px;">
                                                    <i class="uil uil-check-circle me-1"></i><?php echo e($tax['name'] ?? __('catalogmanagement::product.tax')); ?> (<?php echo e($tax['percentage']); ?>%)
                                                </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        
                                        
                                        <p class="mb-2 small" style="color: #856404;"><?php echo e(__('catalogmanagement::product.tax_notice_description')); ?></p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $taxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge badge-round badge-lg px-3 py-2" style="background: rgba(255, 193, 7, 0.2); color: #856404; font-size: 13px;">
                                                    <?php echo e($tax['name'] ?? __('catalogmanagement::product.tax')); ?> (<?php echo e($tax['percentage']); ?>%)
                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <!-- Form -->
                        <form id="productForm" method="POST"
                            action="<?php echo e(isset($product) ? route('admin.products.update', $product->product ? $product->product->id : $product->id) : route('admin.products.store')); ?>"
                            enctype="multipart/form-data" novalidate onkeydown="return event.key != 'Enter';">
                            <?php echo csrf_field(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($product)): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <!-- Step 1: Product Information -->
                            <div class="wizard-step-content active" data-step="1">
                                <!-- Card 1: Product Information -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-info-circle"></i>
                                            <?php echo e(__('catalogmanagement::product.product_details')); ?>

                                        </h5>
                                        <div class="row">
                                            <?php
                                                $translationModel = isset($product)
                                                    ? $product->product ?? $product
                                                    : null;
                                            ?>

                                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'title','label' => 'Product Title','labelAr' => 'عنوان المنتج','placeholder' => 'Enter product title','placeholderAr' => 'أدخل عنوان المنتج','languages' => $languages,'model' => $translationModel,'required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'title','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Product Title'),'labelAr' => 'عنوان المنتج','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter product title'),'placeholderAr' => 'أدخل عنوان المنتج','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel),'required' => true]); ?>
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

                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label for="sku"
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.sku')); ?> <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="sku" id="sku"
                                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                        placeholder="<?php echo e(__('catalogmanagement::product.sku')); ?>"
                                                        value="<?php echo e(isset($product) ? $product->sku : ''); ?>">
                                                    <div class="error-message text-danger" id="error-sku" style="display: none;"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label d-block"><?php echo e(__('catalogmanagement::product.status')); ?></label>
                                                    <div class="form-check form-switch form-switch-lg">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="is_active" name="is_active" value="1"
                                                            <?php if(isset($product) && $product->is_active): ?> checked <?php endif; ?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label d-block"><?php echo e(__('catalogmanagement::product.featured')); ?></label>
                                                    <div class="form-check form-switch form-switch-lg">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="is_featured" name="is_featured" value="1"
                                                            <?php if(isset($product) && $product->is_featured): ?> checked <?php endif; ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Organization -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-sitemap"></i>
                                            <?php echo e(__('common.organization')); ?>

                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="brand_id"
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.brand')); ?>

                                                        <span class="text-danger">*</span></label>
                                                    <select name="brand_id" id="brand_id" class="form-control select2">
                                                        <option value=""><?php echo e(__('common.select_option')); ?></option>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($brand['id']); ?>"
                                                                <?php echo e(isset($product) && $product->product->brand_id == $brand['id'] ? 'selected' : ''); ?>>
                                                                <?php echo e($brand['name']); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(auth()->user()->user_type_id, \App\Models\UserType::vendorIds())): ?>
                                                <!-- Hidden input for vendor users -->
                                                <input class="form-control" type="hidden" name="vendor_id" id="vendor_id"
                                                    value="<?php echo e(auth()->user()->vendor->id ?? ''); ?>">
                                            <?php else: ?>
                                                <!-- Vendor select for admin users -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label for="vendor_id" class="form-label">
                                                            <?php echo e(__('catalogmanagement::product.vendor')); ?>

                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="vendor_id" id="vendor_id"
                                                            class="form-control select2">
                                                            <option value=""><?php echo e(__('common.select_option')); ?>

                                                            </option>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($vendor['id']); ?>"
                                                                    <?php echo e(isset($product) && $product->vendor_id == $vendor['id'] ? 'selected' : ''); ?>>
                                                                    <?php echo e($vendor['name']); ?>

                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </select>
                                                        <div class="error-message text-danger" id="error-vendor_id"
                                                            style="display: none;"></div>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="department_id"
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.department')); ?>

                                                        <span class="text-danger">*</span></label>
                                                    <select name="department_id" id="department_id"
                                                        class="form-control select2">
                                                        <option value=""><?php echo e(__('common.select_option')); ?></option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="category_id"
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.category')); ?>

                                                        <span class="text-danger">*</span></label>
                                                    <select name="category_id" id="category_id"
                                                        class="form-control select2">
                                                        <option value=""><?php echo e(__('common.select_option')); ?></option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="sub_category_id"
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.sub_category')); ?></label>
                                                    <select name="sub_category_id" id="sub_category_id"
                                                        class="form-control select2">
                                                        <option value=""><?php echo e(__('common.select_option')); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="max_per_order"
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.max_per_order')); ?>

                                                        <span class="text-danger">*</span></label>
                                                    <input type="number" name="max_per_order" id="max_per_order"
                                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                        min="1" placeholder="Enter max per order"
                                                        value="<?php echo e(isset($product) ? $product->max_per_order ?? (($product->product ? $product->product->max_per_order : null) ?? '') : ''); ?>"
                                                        required>
                                                    <div class="error-message text-danger" id="error-max_per_order"
                                                        style="display: none;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 4: Product Tags -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-tag-alt"></i>
                                            <?php echo e(__('common.tags')); ?>

                                        </h5>
                                        <div class="row">
                                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'tags','label' => __('common.tags'),'labelAr' => 'الوسوم','placeholder' => 'Type a tag and press Enter...','placeholderAr' => 'اكتب وسم واضغط انتر','languages' => $languages,'model' => $translationModel,'tags' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'tags','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.tags')),'labelAr' => 'الوسوم','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Type a tag and press Enter...'),'placeholderAr' => 'اكتب وسم واضغط انتر','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel),'tags' => true]); ?>
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
                            </div>

                            <!-- Step 2: Product Details -->
                            <div class="wizard-step-content" data-step="2" style="display: none;">

                                <!-- Main Product Image -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-image"></i>
                                            <?php echo e(__('catalogmanagement::product.main_image')); ?>

                                        </h5>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <?php if (isset($component)) { $__componentOriginaldbebdfa49a0907927fe266159631a348 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbebdfa49a0907927fe266159631a348 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-upload','data' => ['id' => 'main_image','name' => 'main_image','label' => ''.e(__('common.product_image')).'','required' => false,'existingImage' => isset($product) &&
                                                    $product->product &&
                                                    $product->product->mainImage
                                                        ? $product->product->mainImage->path
                                                        : null,'placeholder' => ''.e(__('common.click_to_upload')).'','recommendedSize' => ''.e(__('common.recommended_logo_size')).'','accept' => 'image/jpeg,image/png,image/jpg,image/webp','aspectRatio' => 'square']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'main_image','name' => 'main_image','label' => ''.e(__('common.product_image')).'','required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'existingImage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($product) &&
                                                    $product->product &&
                                                    $product->product->mainImage
                                                        ? $product->product->mainImage->path
                                                        : null),'placeholder' => ''.e(__('common.click_to_upload')).'','recommendedSize' => ''.e(__('common.recommended_logo_size')).'','accept' => 'image/jpeg,image/png,image/jpg,image/webp','aspectRatio' => 'square']); ?>
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
                                        </div>
                                    </div>
                                </div>
                                <!-- Additional Images -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-image"></i>
                                            <?php echo e(__('catalogmanagement::product.additional_images')); ?>

                                        </h5>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <input type="file" multiple class="form-control" accept="image/*"
                                                    name="additional_images[]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card 1: Main Descriptions -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-file-alt"></i>
                                            <?php echo e(__('common.description')); ?>

                                        </h5>
                                        <div class="row">
                                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'details','label' => __('common.details'),'labelAr' => 'تفاصيل المنتج','placeholder' => 'Enter product details','placeholderAr' => 'أدخل تفاصيل المنتج','type' => 'textarea','rows' => 6,'inputClass' => 'tinymce-editor','languages' => $languages,'model' => $translationModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'details','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.details')),'labelAr' => 'تفاصيل المنتج','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter product details'),'placeholderAr' => 'أدخل تفاصيل المنتج','type' => 'textarea','rows' => 6,'inputClass' => 'tinymce-editor','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel)]); ?>
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

                                <!-- Card 2: Additional Information -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-info-circle"></i>
                                            <?php echo e(__('common.additional_information')); ?>

                                        </h5>
                                        <div class="row">
                                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'summary','label' => __('common.summary'),'labelAr' => 'الملخص','placeholder' => 'Enter summary','placeholderAr' => 'أدخل الملخص','type' => 'textarea','rows' => 4,'inputClass' => 'tinymce-editor','languages' => $languages,'model' => $translationModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'summary','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.summary')),'labelAr' => 'الملخص','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter summary'),'placeholderAr' => 'أدخل الملخص','type' => 'textarea','rows' => 4,'inputClass' => 'tinymce-editor','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'features','label' => __('common.features'),'labelAr' => 'المميزات','placeholder' => 'Enter features','placeholderAr' => 'أدخل المميزات','type' => 'textarea','rows' => 4,'inputClass' => 'tinymce-editor','languages' => $languages,'model' => $translationModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'features','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.features')),'labelAr' => 'المميزات','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter features'),'placeholderAr' => 'أدخل المميزات','type' => 'textarea','rows' => 4,'inputClass' => 'tinymce-editor','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'instructions','label' => __('common.instructions'),'labelAr' => 'التعليمات','placeholder' => 'Enter instructions','placeholderAr' => 'أدخل التعليمات','type' => 'textarea','rows' => 4,'inputClass' => 'tinymce-editor','languages' => $languages,'model' => $translationModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'instructions','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.instructions')),'labelAr' => 'التعليمات','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter instructions'),'placeholderAr' => 'أدخل التعليمات','type' => 'textarea','rows' => 4,'inputClass' => 'tinymce-editor','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'extra_description','label' => __('catalogmanagement::product.extra_description'),'labelAr' => 'وصف إضافي','placeholder' => 'Enter extra description','placeholderAr' => 'أدخل وصف إضافي','type' => 'textarea','rows' => 4,'inputClass' => 'tinymce-editor','languages' => $languages,'model' => $translationModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'extra_description','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.extra_description')),'labelAr' => 'وصف إضافي','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter extra description'),'placeholderAr' => 'أدخل وصف إضافي','type' => 'textarea','rows' => 4,'inputClass' => 'tinymce-editor','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel)]); ?>
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

                                <!-- Card 3: Material & Media -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-play-circle"></i>
                                            <?php echo e(__('common.media')); ?>

                                        </h5>
                                        <div class="row">
                                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'material','label' => __('catalogmanagement::product.material'),'labelAr' => 'المواد','placeholder' => 'Enter material','placeholderAr' => 'أدخل المواد','type' => 'textarea','rows' => 3,'inputClass' => 'tinymce-editor','languages' => $languages,'model' => $translationModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'material','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.material')),'labelAr' => 'المواد','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter material'),'placeholderAr' => 'أدخل المواد','type' => 'textarea','rows' => 3,'inputClass' => 'tinymce-editor','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel)]); ?>
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

                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label for="video_link"
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.video_link')); ?></label>
                                                    <input type="url" name="video_link" id="video_link"
                                                        value="<?php echo e(isset($product) ? $product->video_link : ''); ?>"
                                                        class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                        placeholder="https://www.youtube.com/watch?v=...">
                                                    <small
                                                        class="text-muted"><?php echo e(__('common.enter_valid_video_url')); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Variant Configurations -->
                            <div class="wizard-step-content" data-step="3">
                                <!-- Configuration Type -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-setting"></i>
                                            <?php echo e(__('catalogmanagement::product.configuration_type')); ?>

                                        </h5>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label for="configuration_type"
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.product_type')); ?>

                                                        <span class="text-danger">*</span></label>
                                                    <select name="configuration_type" id="configuration_type"
                                                        class="form-control select2">
                                                        <option value="">
                                                            <?php echo e(__('catalogmanagement::product.select_product_type')); ?>

                                                        </option>
                                                        <option value="simple"
                                                            <?php echo e(isset($product) && ($product->configuration_type ?? ($product->product->configuration_type ?? '')) == 'simple' ? 'selected' : ''); ?>>
                                                            <?php echo e(__('catalogmanagement::product.simple_product')); ?></option>
                                                        <option value="variants"
                                                            <?php echo e(isset($product) && ($product->configuration_type ?? ($product->product->configuration_type ?? '')) == 'variants' ? 'selected' : ''); ?>>
                                                            <?php echo e(__('catalogmanagement::product.with_variants')); ?></option>
                                                    </select>
                                                    <div class="error-message text-danger" id="error-configuration_type"
                                                        style="display: none;"></div>
                                                </div>
                                            </div>
                                            <?php
                                                $configurationType =
                                                    $product->configuration_type ??
                                                    ($product->product->configuration_type ?? '');
                                                $firstVariant = $product->variants->first();
                                            ?>

                                            <!-- Simple Product Information (shown only for simple products) -->
                                            <?php if(isset($product) && $configurationType === 'simple' && $firstVariant): ?>
                                                <div class="card mt-4" id="simple-product-section">
                                                    <div class="card-header">
                                                        <h6 class="mb-0" style="font-weight: 600; font-size: 16px;">
                                                            <i class="uil uil-package me-2"></i>
                                                            Simple Product Configuration
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        
                                                        <div class="row mb-4">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold">Price <span
                                                                        class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="number" name="price"
                                                                        class="form-control"
                                                                        value="<?php echo e($firstVariant->price ?? 0); ?>"
                                                                        step="0.01" min="0" placeholder="0.00">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        
                                                        <div class="mb-4">
                                                            <div>
                                                                <label class="form-label fw-bold mb-0">Enable Discount
                                                                    Offer</label>
                                                                <div class="form-check form-switch form-switch-lg">
                                                                    
                                                                    <input type="hidden" name="has_discount"
                                                                        value="0">
                                                                    <input type="checkbox" name="has_discount"
                                                                        class="form-check-input" role="switch"
                                                                        id="simple_discount" value="1"
                                                                        <?php echo e($firstVariant && $firstVariant->has_discount ? 'checked' : ''); ?>>
                                                                    <label class="form-check-label"
                                                                        for="simple_discount"></label>
                                                                </div>
                                                            </div>

                                                            
                                                            <div id="simple_discount_fields" class="mt-3"
                                                                style="display: <?php echo e($firstVariant && $firstVariant->has_discount ? 'block' : 'none'); ?>;">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-bold">Price Before
                                                                            Discount <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="number" name="price_before_discount"
                                                                            class="form-control"
                                                                            value="<?php echo e($firstVariant->price_before_discount ?? ''); ?>"
                                                                            step="0.01" min="0"
                                                                            placeholder="0.00">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-bold">Discount End
                                                                            Date</label>
                                                                        <input type="date" name="discount_end_date"
                                                                            class="form-control"
                                                                            value="<?php echo e($firstVariant && $firstVariant->discount_end_date ? date('Y-m-d', strtotime($firstVariant->discount_end_date)) : ''); ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        
                                                        <div class="mb-4">
                                                            <label
                                                                class="form-label fw-bold"><?php echo e(__('catalogmanagement::product.stock_per_region')); ?>

                                                                <span class="text-danger">*</span></label>

                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr class="userDatatable-header">
                                                                            <th style="width: 40%; font-weight: 600;">
                                                                                <?php echo e(__('catalogmanagement::product.region')); ?>

                                                                            </th>
                                                                            <th style="width: 30%; font-weight: 600;">
                                                                                <?php echo e(__('catalogmanagement::product.quantity')); ?>

                                                                            </th>
                                                                            <th
                                                                                style="width: 15%; text-align: center; font-weight: 600;">
                                                                                <?php echo e(__('catalogmanagement::product.actions')); ?>

                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="simple-stock-rows">
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $firstVariant->stocks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockIndex => $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                            <tr class="stock-row">
                                                                                
                                                                                <input type="hidden"
                                                                                    name="stocks[<?php echo e($stockIndex); ?>][id]"
                                                                                    value="<?php echo e($stock->id); ?>">
                                                                                <input type="hidden"
                                                                                    name="stocks[<?php echo e($stockIndex); ?>][variant_id]"
                                                                                    value="<?php echo e($firstVariant->id); ?>">
                                                                                <td>
                                                                                    <select
                                                                                        name="stocks[<?php echo e($stockIndex); ?>][region_id]"
                                                                                        class="form-control select2 region-select"
                                                                                        required>
                                                                                        <option value="">
                                                                                            <?php echo e(__('catalogmanagement::product.select_region')); ?>

                                                                                        </option>
                                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($regions)): ?>
                                                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <option
                                                                                                    value="<?php echo e($region['id']); ?>"
                                                                                                    <?php echo e($stock->region_id == $region['id'] ? 'selected' : ''); ?>>
                                                                                                    <?php echo e($region['name']); ?>

                                                                                                </option>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number"
                                                                                        name="stocks[<?php echo e($stockIndex); ?>][quantity]"
                                                                                        class="form-control quantity-input"
                                                                                        value="<?php echo e($stock->quantity); ?>"
                                                                                        min="0" placeholder="0">
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <button type="button"
                                                                                        class="btn btn-danger btn-sm remove-stock-row">
                                                                                        <i
                                                                                            class="uil uil-trash-alt m-0"></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                            <tr class="stock-row">
                                                                                <td>
                                                                                    <select name="stocks[0][region_id]"
                                                                                        class="form-control region-select"
                                                                                        required>
                                                                                        <option value="">
                                                                                            <?php echo e(__('common.select')); ?>

                                                                                        </option>
                                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($regions)): ?>
                                                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <option
                                                                                                    value="<?php echo e($region['id']); ?>">
                                                                                                    <?php echo e($region['name']); ?>

                                                                                                </option>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number"
                                                                                        name="stocks[0][quantity]"
                                                                                        class="form-control quantity-input"
                                                                                        value="0" min="0"
                                                                                        placeholder="0">
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <button type="button"
                                                                                        class="btn btn-danger btn-sm remove-stock-row">
                                                                                        <i
                                                                                            class="uil uil-trash-alt m-0"></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr
                                                                            style="background-color: #f8f9fa; font-weight: 600;">
                                                                            <td class="text-end" style="padding: 12px;">
                                                                                <strong><?php echo e(__('catalogmanagement::product.total_stock') ?? 'Total Stock'); ?>:</strong>
                                                                            </td>
                                                                            <td style="padding: 12px;">
                                                                                <span
                                                                                    class="badge badge-primary badge-lg total-stock-display">
                                                                                    <?php echo e($firstVariant->stocks->sum('quantity') ?? 0); ?>

                                                                                </span>
                                                                                <span
                                                                                    class="ms-1"><?php echo e(__('common.quantity')); ?></span>
                                                                            </td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>

                                                            
                                                            <button type="button" class="btn btn-primary mt-3"
                                                                id="add-simple-stock-row">
                                                                <i class="uil uil-plus me-1"></i>
                                                                <?php echo e(__('catalogmanagement::product.add_region')); ?>

                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <!-- Dynamic Simple Product Section (shown when simple is selected from dropdown) -->
                                            <div id="dynamic-simple-product-section" style="display: none;">
                                                <div class="card mt-4">
                                                    <div class="card-header">
                                                        <h6 class="mb-0" style="font-weight: 600; font-size: 16px;">
                                                            <i class="uil uil-package me-2"></i>
                                                            <?php echo e(__('catalogmanagement::product.simple_product_configuration')); ?>

                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- Dynamic Simple Product Pricing & Stock Box -->
                                                        <div id="dynamic-simple-pricing-stock">
                                                            <!-- Pricing & Stock box will be inserted here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Variant Information (shown only for variant products) -->
                                            <div class="variant-configuration-section">
                                                <?php if(isset($product) && $configurationType === 'variants' && $product->variants->count() > 0): ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variantIndex => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="existing-variant-wrapper"
                                                            id="existing-variant-<?php echo e($variantIndex); ?>">
                                                            
                                                            <input type="hidden"
                                                                name="variants[<?php echo e($variantIndex); ?>][id]"
                                                                value="<?php echo e($variant->id); ?>">
                                                            <input type="hidden"
                                                                name="variants[<?php echo e($variantIndex); ?>][variant_configuration_id]"
                                                                value="<?php echo e($variant->variant_configuration_id); ?>">

                                                            <div class="card mt-4"
                                                                id="variant-<?php echo e($variantIndex); ?>-section">
                                                                <div
                                                                    class="card-header d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0"
                                                                        style="font-weight: 600; font-size: 16px;">
                                                                        <i class="uil uil-layer-group me-2"></i>
                                                                        <?php echo e(__('catalogmanagement::product.variant_configuration')); ?>:
                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->variantConfiguration): ?>
                                                                            <?php
                                                                                // Build the variant hierarchy by traversing up the parent chain
                                                                                $hierarchy = [];
                                                                                $current =
                                                                                    $variant->variantConfiguration;
                                                                                $visited = []; // Prevent infinite loops

                                                                                // Start with the current variant (could be leaf or parent node)
                                                                                while (
                                                                                    $current &&
                                                                                    !in_array($current->id, $visited)
                                                                                ) {
                                                                                    $visited[] = $current->id;

                                                                                    // Get the value name (current node)
                                                                                    $valueName =
                                                                                        $current->getTranslation(
                                                                                            'name',
                                                                                            app()->getLocale(),
                                                                                        ) ??
                                                                                        ($current->getTranslation(
                                                                                            'name',
                                                                                            'en',
                                                                                        ) ??
                                                                                            ($current->name ??
                                                                                                'Value'));

                                                                                    // If this has a parent, it means this is a value and parent is key
                                                                                    if ($current->parent_data) {
                                                                                        $keyName =
                                                                                            $current->parent_data->getTranslation(
                                                                                                'name',
                                                                                                app()->getLocale(),
                                                                                            ) ??
                                                                                            ($current->parent_data->getTranslation(
                                                                                                'name',
                                                                                                'en',
                                                                                            ) ??
                                                                                                ($current->parent_data
                                                                                                    ->name ??
                                                                                                    'Key'));

                                                                                        // Add to hierarchy (key -> value)
                                                                                        array_unshift(
                                                                                            $hierarchy,
                                                                                            $keyName .
                                                                                                ' → ' .
                                                                                                $valueName,
                                                                                        );

                                                                                        // Move to parent for next iteration
                                                                                        $current =
                                                                                            $current->parent_data;
                                                                                    } else {
                                                                                        // This is a root node (no parent) - still add it to hierarchy
                                                                                        $keyName = $current->key
                                                                                            ? $current->key->getTranslation(
                                                                                                    'name',
                                                                                                    app()->getLocale(),
                                                                                                ) ??
                                                                                                ($current->key->getTranslation(
                                                                                                    'name',
                                                                                                    'en',
                                                                                                ) ??
                                                                                                    ($current->key
                                                                                                        ->name ??
                                                                                                        'Key'))
                                                                                            : 'Key';

                                                                                        array_unshift(
                                                                                            $hierarchy,
                                                                                            $keyName .
                                                                                                ' → ' .
                                                                                                $valueName,
                                                                                        );
                                                                                        break;
                                                                                    }
                                                                                }

                                                                                // Join the hierarchy with arrows
                                                                                $hierarchyString = implode(
                                                                                    ' → ',
                                                                                    $hierarchy,
                                                                                );
                                                                            ?>
                                                                            <?php echo e($hierarchyString ?: 'Packaging - 10 kg'); ?>

                                                                        <?php else: ?>
                                                                            Packaging - 10 kg
                                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm remove-existing-variant-btn"
                                                                        data-variant-index="<?php echo e($variantIndex); ?>">
                                                                        <i class="uil uil-trash-alt m-0"></i>
                                                                        <?php echo e(__('common.remove')); ?>

                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    
                                                                    <div class="row mb-4">
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label fw-bold"><?php echo e(__('catalogmanagement::product.variant_sku')); ?>

                                                                                <span class="text-danger">*</span></label>
                                                                            <input type="text"
                                                                                name="variants[<?php echo e($variantIndex); ?>][sku]"
                                                                                class="form-control"
                                                                                value="<?php echo e($variant->sku); ?>"
                                                                                placeholder="<?php echo e(__('catalogmanagement::product.enter_sku')); ?>">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label fw-bold"><?php echo e(__('catalogmanagement::product.price')); ?>

                                                                                <span class="text-danger">*</span></label>
                                                                            <div class="input-group">
                                                                                <input type="number"
                                                                                    name="variants[<?php echo e($variantIndex); ?>][price]"
                                                                                    class="form-control"
                                                                                    value="<?php echo e($variant->price); ?>"
                                                                                    step="0.01" min="0"
                                                                                    placeholder="0.00">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    
                                                                    <div class="mb-4">
                                                                        <div>
                                                                            <label
                                                                                class="form-label fw-bold mb-0"><?php echo e(__('catalogmanagement::product.enable_discount_offer')); ?></label>
                                                                            <div
                                                                                class="form-check form-switch form-switch-lg">
                                                                                
                                                                                <input type="hidden"
                                                                                    name="variants[<?php echo e($variantIndex); ?>][has_discount]"
                                                                                    value="0">
                                                                                <input type="checkbox"
                                                                                    name="variants[<?php echo e($variantIndex); ?>][has_discount]"
                                                                                    class="form-check-input"
                                                                                    role="switch"
                                                                                    id="discount_<?php echo e($variantIndex); ?>"
                                                                                    value="1"
                                                                                    <?php echo e($variant->has_discount ? 'checked' : ''); ?>

                                                                                    onchange="toggleDiscountFields(<?php echo e($variantIndex); ?>)">
                                                                                <label class="form-check-label"
                                                                                    for="discount_<?php echo e($variantIndex); ?>"></label>
                                                                            </div>
                                                                        </div>

                                                                        
                                                                        <div id="discount_fields_<?php echo e($variantIndex); ?>"
                                                                            class="mt-3"
                                                                            style="display: <?php echo e($variant->has_discount ? 'block' : 'none'); ?>;">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label fw-bold"><?php echo e(__('catalogmanagement::product.price_before_discount')); ?>

                                                                                        <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <input type="number"
                                                                                        name="variants[<?php echo e($variantIndex); ?>][price_before_discount]"
                                                                                        class="form-control discount-field"
                                                                                        value="<?php echo e($variant->price_before_discount ?? ''); ?>"
                                                                                        step="0.01" min="0"
                                                                                        placeholder="0.00"
                                                                                        data-variant-index="<?php echo e($variantIndex); ?>">
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label fw-bold"><?php echo e(__('catalogmanagement::product.discount_end_date')); ?></label>
                                                                                    <input type="date"
                                                                                        name="variants[<?php echo e($variantIndex); ?>][discount_end_date]"
                                                                                        class="form-control discount-field"
                                                                                        value="<?php echo e($variant->discount_end_date ? date('Y-m-d', strtotime($variant->discount_end_date)) : ''); ?>"
                                                                                        data-variant-index="<?php echo e($variantIndex); ?>">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        
                                                                    </div>

                                                                    
                                                                    <div class="mb-4">
                                                                        <label
                                                                            class="form-label fw-bold"><?php echo e(__('catalogmanagement::product.stock_per_region')); ?>

                                                                            <span class="text-danger">*</span></label>

                                                                        <div class="table-responsive">
                                                                            <table class="table table-bordered">
                                                                                <thead>
                                                                                    <tr class="userDatatable-header">
                                                                                        <th
                                                                                            style="width: 40%; font-weight: 600;">
                                                                                            <?php echo e(__('catalogmanagement::product.region')); ?>

                                                                                        </th>
                                                                                        <th
                                                                                            style="width: 30%; font-weight: 600;">
                                                                                            <?php echo e(__('catalogmanagement::product.quantity')); ?>

                                                                                        </th>
                                                                                        <th
                                                                                            style="width: 15%; text-align: center; font-weight: 600;">
                                                                                            <?php echo e(__('catalogmanagement::product.actions')); ?>

                                                                                        </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody
                                                                                    id="variant-<?php echo e($variantIndex); ?>-stock-rows">
                                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $variant->stocks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockIndex => $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                                        
                                                                                        <input type="hidden"
                                                                                            name="variants[<?php echo e($variantIndex); ?>][stocks][<?php echo e($stockIndex); ?>][id]"
                                                                                            value="<?php echo e($stock->id); ?>">
                                                                                        <input type="hidden"
                                                                                            name="variants[<?php echo e($variantIndex); ?>][stocks][<?php echo e($stockIndex); ?>][variant_id]"
                                                                                            value="<?php echo e($variant->id); ?>">

                                                                                        <tr class="stock-row">
                                                                                            <td>
                                                                                                <select
                                                                                                    name="variants[<?php echo e($variantIndex); ?>][stocks][<?php echo e($stockIndex); ?>][region_id]"
                                                                                                    class="form-control select2 region-select"
                                                                                                    required>
                                                                                                    <option value="">
                                                                                                        <?php echo e(__('catalogmanagement::product.select_region')); ?>

                                                                                                    </option>
                                                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($regions)): ?>
                                                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                            <option
                                                                                                                value="<?php echo e($region['id']); ?>"
                                                                                                                <?php echo e($stock->region_id == $region['id'] ? 'selected' : ''); ?>>
                                                                                                                <?php echo e($region['name']); ?>

                                                                                                            </option>
                                                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                                </select>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="number"
                                                                                                    name="variants[<?php echo e($variantIndex); ?>][stocks][<?php echo e($stockIndex); ?>][quantity]"
                                                                                                    class="form-control quantity-input"
                                                                                                    value="<?php echo e($stock->quantity); ?>"
                                                                                                    min="0"
                                                                                                    placeholder="0">
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <button type="button"
                                                                                                    class="btn btn-danger btn-sm remove-stock-row">
                                                                                                    <i
                                                                                                        class="uil uil-trash-alt m-0"></i>
                                                                                                </button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                                        
                                                                                        <input type="hidden"
                                                                                            name="variants[<?php echo e($variantIndex); ?>][stocks][0][variant_id]"
                                                                                            value="<?php echo e($variant->id); ?>">

                                                                                        <tr class="stock-row">
                                                                                            <td>
                                                                                                <select
                                                                                                    name="variants[<?php echo e($variantIndex); ?>][stocks][0][region_id]"
                                                                                                    class="form-control region-select"
                                                                                                    required>
                                                                                                    <option value="">
                                                                                                        <?php echo e(__('catalogmanagement::product.select_region')); ?>

                                                                                                    </option>
                                                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($regions)): ?>
                                                                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                            <option
                                                                                                                value="<?php echo e($region['id']); ?>">
                                                                                                                <?php echo e($region['name']); ?>

                                                                                                            </option>
                                                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                                </select>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="number"
                                                                                                    name="variants[<?php echo e($variantIndex); ?>][stocks][0][quantity]"
                                                                                                    class="form-control quantity-input"
                                                                                                    value="0"
                                                                                                    min="0"
                                                                                                    placeholder="0">
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <button type="button"
                                                                                                    class="btn btn-danger btn-sm remove-stock-row">
                                                                                                    <i
                                                                                                        class="uil uil-trash-alt m-0"></i>
                                                                                                </button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr
                                                                                        style="background-color: #f8f9fa; font-weight: 600;">
                                                                                        <td colspan="1"
                                                                                            class="text-end"
                                                                                            style="padding: 12px;">
                                                                                            <strong><?php echo e(__('catalogmanagement::product.total_stock') ?? 'Total Stock'); ?>:</strong>
                                                                                        </td>
                                                                                        <td style="padding: 12px;">
                                                                                            <span
                                                                                                class="badge badge-primary badge-lg total-stock-display">
                                                                                                <?php echo e($variant->stocks->sum('quantity') ?? 0); ?>

                                                                                            </span>
                                                                                            <span
                                                                                                class="ms-1"><?php echo e(__('common.quantity')); ?></span>
                                                                                        </td>
                                                                                        <td></td>
                                                                                    </tr>
                                                                                </tfoot>
                                                                            </table>
                                                                        </div>

                                                                        
                                                                        <button type="button"
                                                                            class="btn btn-primary mt-3 add-variant-stock-row"
                                                                            data-variant-index="<?php echo e($variantIndex); ?>">
                                                                            <i class="uil uil-plus me-1"></i>
                                                                            <?php echo e(__('catalogmanagement::product.add_region')); ?>

                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                                                                <hr class="my-4">
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <!-- Add New Variants Section (shown/hidden dynamically based on configuration type) -->
                                                <div class="card mt-4" id="add-new-variants-section"
                                                    style="display: <?php echo e($configurationType === 'variants' ? 'block' : 'none'); ?>;">
                                                    <div class="card-body">
                                                        <h5 class="d-flex justify-content-between align-items-center mb-4">
                                                            <div>
                                                                <i class="uil uil-plus-circle"></i>
                                                                <?php echo e(__('catalogmanagement::product.add_new_variants')); ?>

                                                            </div>
                                                            <button type="button" id="add-variant-btn"
                                                                class="btn btn-white btn-sm">
                                                                <i class="uil uil-plus"></i>
                                                                <?php echo e(__('catalogmanagement::product.add_variant')); ?>

                                                            </button>
                                                        </h5>

                                                        <!-- Empty state message -->
                                                        <div id="variants-empty-state" class="text-center py-4">
                                                            <i class="uil uil-layer-group text-muted"
                                                                style="font-size: 48px;"></i>
                                                            <p class="text-muted mb-0">
                                                                <?php echo e(__('catalogmanagement::product.click_add_variant_to_create_new')); ?>

                                                            </p>
                                                        </div>

                                                        <!-- New Variants Container -->
                                                        <div id="variants-container">
                                                            <!-- New variant boxes will be added here dynamically -->
                                                        </div>
                                                    </div>
                                                </div>

                                                
                                                <template id="variant-box-template">
                                                    <div class="card mb-3 variant-box"
                                                        data-variant-index="__VARIANT_INDEX__"
                                                        id="variant-__VARIANT_INDEX__">
                                                        <div
                                                            class="card-header d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0">
                                                                <i class="uil uil-layer-group"></i>
                                                                <?php echo e(__('common.variant')); ?> #__VARIANT_NUMBER__
                                                            </h6>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-variant-btn">
                                                                <i class="uil uil-trash-alt m-0"></i>
                                                                <?php echo e(__('common.remove')); ?>

                                                            </button>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- Variant Key Selection -->
                                                            <div class="row mb-3">
                                                                <div class="col-md-12">
                                                                    <label
                                                                        class="form-label"><?php echo e(__('catalogmanagement::product.variant_key')); ?>

                                                                        <span class="text-danger">*</span></label>
                                                                    <select class="form-control select2 variant-key-select"
                                                                        required>
                                                                        <option value="">
                                                                            <?php echo e(__('catalogmanagement::product.select_variant_key')); ?>

                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Variant Tree Container -->
                                                            <div class="variant-tree-container" style="display: none;">
                                                                <label
                                                                    class="form-label"><?php echo e(__('catalogmanagement::product.variant_selection')); ?>

                                                                    <span class="text-danger">*</span></label>
                                                                <div class="variant-tree-levels">
                                                                    <!-- Dynamic variant levels will be added here -->
                                                                </div>
                                                                <input type="hidden"
                                                                    name="variants[__VARIANT_INDEX__][variant_configuration_id]"
                                                                    class="selected-variant-id">
                                                                <div class="alert alert-info mt-2 selected-variant-path"
                                                                    style="display: none;">
                                                                    <strong><?php echo e(__('catalogmanagement::product.selected_variant')); ?>:</strong>
                                                                    <span class="path-text"></span>
                                                                </div>
                                                            </div>

                                                            <!-- Pricing & Stock will be inserted here after variant selection -->
                                                            <div id="variant-__VARIANT_INDEX__-pricing-stock"
                                                                style="display: none;"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div> <!-- End variant-configuration-section -->
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Step 4: SEO & Images -->
                            <div class="wizard-step-content" data-step="4" style="display: none;">
                                <!-- SEO Information -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-search"></i>
                                            <?php echo e(__('common.seo')); ?>

                                        </h5>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($languages) && count($languages) > 0): ?>
                                            <div class="row">
                                                <div class="row">
                                                    <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'meta_title','label' => __('catalogmanagement::product.meta_title'),'labelAr' => 'العنوان الوصفي','placeholder' => 'Enter meta title','placeholderAr' => 'أدخل العنوان الوصفي','languages' => $languages,'model' => $translationModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'meta_title','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.meta_title')),'labelAr' => 'العنوان الوصفي','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter meta title'),'placeholderAr' => 'أدخل العنوان الوصفي','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'meta_description','label' => __('catalogmanagement::product.meta_description'),'labelAr' => 'الوصف الوصفي','placeholder' => 'Enter meta description','placeholderAr' => 'أدخل الوصف الوصفي','type' => 'textarea','rows' => 3,'languages' => $languages,'model' => $translationModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'meta_description','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.meta_description')),'labelAr' => 'الوصف الوصفي','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Enter meta description'),'placeholderAr' => 'أدخل الوصف الوصفي','type' => 'textarea','rows' => 3,'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'meta_keywords','label' => __('catalogmanagement::product.meta_keywords'),'labelAr' => 'كلمات مفتاحية','placeholder' => 'Type a keyword and press Enter...','placeholderAr' => 'اكتب كلمة مفتاحية واضغط انتر','languages' => $languages,'model' => $translationModel,'tags' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'meta_keywords','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::product.meta_keywords')),'labelAr' => 'كلمات مفتاحية','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Type a keyword and press Enter...'),'placeholderAr' => 'اكتب كلمة مفتاحية واضغط انتر','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($translationModel),'tags' => true]); ?>
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
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                <i class="uil uil-exclamation-triangle me-2"></i>
                                                <strong>No Languages Available</strong><br>
                                                Languages are required to display SEO fields. Please ensure languages are
                                                configured in the system.
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>


                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between wizard-navigation">
                                <button type="button" id="prevBtn" class="btn btn-light btn-squared"
                                    style="display: none;">
                                    <i class="uil uil-arrow-left"></i> <?php echo e(__('common.previous')); ?>

                                </button>
                                <div class="d-flex justify-content-end gap-2 w-100">
                                    <a href="#" class="btn btn-light btn-squared">
                                        <i class="uil uil-times"></i> <?php echo e(__('common.cancel')); ?>

                                    </a>
                                    <button type="button" id="nextBtn" class="btn btn-primary btn-squared">
                                        <?php echo e(__('common.next')); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() == 'ar'): ?>
                                            <i class="uil uil-arrow-left"></i>
                                        <?php else: ?>
                                            <i class="uil uil-arrow-right"></i>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </button>
                                    <button type="submit" id="submitBtn" class="btn btn-success btn-squared"
                                        style="display: none;">
                                        <i class="uil uil-check"></i>
                                        <?php echo e(isset($product) ? __('catalogmanagement::product.update_product') : __('catalogmanagement::product.create_product')); ?>

                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Delete Image Confirmation Modal -->
                        <div class="modal fade" id="deleteImageConfirmModal" tabindex="-1"
                            aria-labelledby="deleteImageConfirmLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteImageConfirmLabel">
                                            <i
                                                class="uil uil-exclamation-triangle me-2"></i><?php echo e(__('common.confirm_deletion') ?? 'Confirm Deletion'); ?>

                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-0">
                                            <?php echo e(__('common.are_you_sure_delete_image') ?? 'Are you sure you want to delete this image? This action cannot be undone.'); ?>

                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                            <i class="uil uil-times me-1"></i><?php echo e(__('common.cancel')); ?>

                                        </button>
                                        <button type="button" class="btn btn-danger" id="confirmDeleteImageBtn">
                                            <i class="uil uil-trash-alt me-1"></i><?php echo e(__('common.delete')); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <template id="pricing-stock-template">
                            <div class="pricing-stock-box" data-index="__INDEX__">
                                <!-- Pricing Card -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4">
                                            <i class="uil uil-dollar-sign"></i>
                                            <?php echo e(__('catalogmanagement::product.pricing')); ?>

                                        </h5>
                                        <div class="row">
                                            <!-- SKU Field (only for variants) -->
                                            <div class="col-md-6 mb-3 variant-sku-field" style="display: none;">
                                                <div class="form-group">
                                                    <label class="form-label"><?php echo e(__('catalogmanagement::product.sku')); ?>

                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" name="__NAME_PREFIX__[sku]"
                                                        class="form-control sku-input"
                                                        placeholder="<?php echo e(__('catalogmanagement::product.sku')); ?>"
                                                        required>
                                                    <div class="error-message text-danger" style="display: none;"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.price')); ?>

                                                        <span class="text-danger">*</span></label>
                                                    <input type="number" name="__NAME_PREFIX__[price]"
                                                        class="form-control price-input" step="0.01" min="0"
                                                        placeholder="0.00" required>
                                                    <div class="error-message text-danger" style="display: none;"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label d-block"><?php echo e(__('catalogmanagement::product.has_discount')); ?></label>
                                                    <div class="form-check form-switch form-switch-lg">
                                                        <input class="form-check-input has-discount-switch"
                                                            type="checkbox" name="__NAME_PREFIX__[has_discount]"
                                                            value="1">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3 discount-fields" style="display: none;">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.price_before_discount')); ?></label>
                                                    <input type="number" name="__NAME_PREFIX__[price_before_discount]"
                                                        class="form-control" step="0.01" min="0"
                                                        placeholder="0.00">
                                                    <div class="error-message text-danger" style="display: none;"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3 discount-fields" style="display: none;">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label"><?php echo e(__('catalogmanagement::product.discount_end_date')); ?></label>
                                                    <input type="date" name="__NAME_PREFIX__[discount_end_date]"
                                                        class="form-control">
                                                    <div class="error-message text-danger" style="display: none;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock Card -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <i class="uil uil-box"></i>
                                                <?php echo e(__('catalogmanagement::product.stock_management')); ?>

                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm add-stock-row">
                                                <i class="uil uil-plus"></i>
                                                <?php echo e(__('catalogmanagement::product.add_region')); ?>

                                            </button>
                                        </h5>

                                        <div class="table-responsive">
                                            <table class="table table-bordered stock-table">
                                                <thead>
                                                    <tr class="userDatatable-header">
                                                        <th width="50%"><?php echo e(__('catalogmanagement::product.region')); ?>

                                                        </th>
                                                        <th width="35%">
                                                            <?php echo e(__('catalogmanagement::product.quantity')); ?></th>
                                                        <th width="15%" class="text-center">
                                                            <?php echo e(__('catalogmanagement::product.actions')); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="stock-rows">
                                                    <!-- Stock rows will be added here -->
                                                </tbody>
                                                <tfoot>
                                                    <tr style="background-color: #f8f9fa; font-weight: 600;">
                                                        <td class="text-end" style="padding: 12px;">
                                                            <strong><?php echo e(__('catalogmanagement::product.total_stock') ?? 'Total Stock'); ?>:</strong>
                                                        </td>
                                                        <td style="padding: 12px;">
                                                            <span
                                                                class="badge badge-primary badge-lg total-stock-display">0</span>
                                                            <span class="ms-1"><?php echo e(__('common.quantity')); ?></span>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        
                        <template id="stock-row-template">
                            <tr class="stock-row">
                                <td>
                                    <select name="__NAME_PREFIX__[stocks][__STOCK_INDEX__][region_id]"
                                        class="form-control select2 region-select" required>
                                        <option value=""><?php echo e(__('catalogmanagement::product.select_region')); ?>

                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="__NAME_PREFIX__[stocks][__STOCK_INDEX__][quantity]"
                                        class="form-control quantity-input" min="0" placeholder="0" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-stock-row">
                                        <i class="uil uil-trash-alt m-0"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        
                        <template id="variant-box-template">
                            <div class="card mb-3 variant-box" data-variant-index="__VARIANT_INDEX__"
                                id="variant-__VARIANT_INDEX__">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="uil uil-layer-group"></i>
                                        <?php echo e(__('common.variant')); ?> #__VARIANT_NUMBER__
                                    </h6>
                                    <button type="button" class="btn btn-danger btn-sm remove-variant-btn">
                                        <i class="uil uil-trash-alt"></i> <?php echo e(__('common.remove')); ?>

                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Variant Key Selection -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label"><?php echo e(__('catalogmanagement::product.variant_key')); ?>

                                                <span class="text-danger">*</span></label>
                                            <select class="form-control select2 variant-key-select" required>
                                                <option value="">
                                                    <?php echo e(__('catalogmanagement::product.select_variant_key')); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Variant Tree Container -->
                                    <div class="variant-tree-container" style="display: none;">
                                        <label
                                            class="form-label"><?php echo e(__('catalogmanagement::product.variant_selection')); ?>

                                            <span class="text-danger">*</span></label>
                                        <div class="variant-tree-levels">
                                            <!-- Dynamic variant levels will be added here -->
                                        </div>
                                        <input type="hidden" name="variants[__VARIANT_INDEX__][variant_configuration_id]"
                                            class="selected-variant-id">
                                        <div class="alert alert-info mt-2 selected-variant-path" style="display: none;">
                                            <strong><?php echo e(__('catalogmanagement::product.selected_variant')); ?>:</strong>
                                            <span class="path-text"></span>
                                        </div>
                                    </div>

                                    <!-- Pricing & Stock will be inserted here after variant selection -->
                                    <div id="variant-__VARIANT_INDEX__-pricing-stock" style="display: none;"></div>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-body'); ?>
    <?php if (isset($component)) { $__componentOriginal115e82920da0ed7c897ee494af74b9d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal115e82920da0ed7c897ee494af74b9d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loading-overlay','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('loading-overlay'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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

<?php $__env->startPush('scripts'); ?>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function($) {
            'use strict';

            // ============================================
            // Configuration & Global Variables
            // ============================================
            const config = {
                currentStep: 1,
                totalSteps: 4,
                locale: '<?php echo e(app()->getLocale()); ?>',
                apiBaseUrl: '<?php echo e(url('/api')); ?>',
                translations: {
                    loading: '<?php echo e(__('common.loading')); ?>',
                    selectOption: '<?php echo e(__('common.select_option')); ?>',
                    error: '<?php echo e(__('common.error')); ?>',
                    success: '<?php echo e(__('common.success')); ?>'
                },
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($product)): ?>
                    selectedValues: {
                        department_id: <?php echo e($product->product->department_id ?? 'null'); ?>,
                        category_id: <?php echo e($product->product->category_id ?? 'null'); ?>,
                        sub_category_id: <?php echo e($product->product->sub_category_id ?? 'null'); ?>,
                        configuration_type: '<?php echo e($product->configuration_type ?? ($product->product->configuration_type ?? '')); ?>',
                        sku: '<?php echo e($product->sku ?? ''); ?>',
                        <?php
                            $configurationType = $product->configuration_type ?? ($product->product->configuration_type ?? '');
                            $firstVariant = $product->variants->first();
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($configurationType === 'simple'): ?>
                            
                            price: <?php echo e($firstVariant ? $firstVariant->price : 0); ?>,
                                has_discount:
                                <?php echo e($firstVariant && $firstVariant->has_discount ? 'true' : 'false'); ?>,
                                price_before_discount:
                                <?php echo e($firstVariant ? $firstVariant->price_before_discount : 0); ?>,
                                discount_end_date:
                                '<?php echo e($firstVariant && $firstVariant->discount_end_date ? $firstVariant->discount_end_date->format('Y-m-d') : ''); ?>',
                                stocks: <?php echo json_encode($firstVariant && $firstVariant->stocks ? $firstVariant->stocks : [], 15, 512) ?>,
                        <?php else: ?>
                            
                            <?php
                                $variantsData = $product->variants->map(function ($variant) {
                                    $variantConfig = null;
                                    if ($variant->variantConfiguration) {
                                        // The variant configuration structure:
                                        // - The variantConfiguration IS the selected value
                                        // - The parent_id points to the key (parent configuration)
                                        // - We need to find the root key by traversing up the hierarchy

                                        $config = $variant->variantConfiguration;
                                        $keyId = null;
                                        $valueId = $config->id;

                                        // Find the root key by traversing up the parent hierarchy
                                        $currentConfig = $config;
                                        while ($currentConfig && $currentConfig->parent_id) {
                                            if ($currentConfig->parent_data) {
                                                $currentConfig = $currentConfig->parent_data;
                                            } else {
                                                break;
                                            }
                                        }

                                        // The root configuration is the key
                                        if ($currentConfig && !$currentConfig->parent_id) {
                                            $keyId = $currentConfig->id;
                                        }

                                        $variantConfig = [
                                            'id' => $config->id,
                                            'variant_key_id' => $keyId,
                                            'variant_value_id' => $valueId,
                                            'parent_id' => $config->parent_id,
                                            'key_name' => $currentConfig ? $currentConfig->getTranslation('name', app()->getLocale()) ?? ($currentConfig->name ?? 'Unknown Key') : 'Unknown Key',
                                            'value_name' => $config->getTranslation('name', app()->getLocale()) ?? ($config->name ?? 'Unknown Value'),
                                            'debug_info' => [
                                                'config_id' => $config->id,
                                                'parent_id' => $config->parent_id,
                                                'found_key_id' => $keyId,
                                                'traversed_to_root' => !is_null($currentConfig),
                                            ],
                                        ];
                                    }

                                    return [
                                        'id' => $variant->id,
                                        'variant_configuration_id' => $variant->variant_configuration_id,
                                        'sku' => $variant->sku,
                                        'price' => $variant->price,
                                        'has_discount' => $variant->has_discount,
                                        'price_before_discount' => $variant->price_before_discount,
                                        'discount_end_date' => $variant->discount_end_date ? $variant->discount_end_date->format('Y-m-d') : null,
                                        'stocks' => $variant->stocks->map(function ($stock) {
                                            return [
                                                'id' => $stock->id,
                                                'region_id' => $stock->region_id,
                                                'stock' => $stock->stock,
                                                'quantity' => $stock->stock, // Alias for compatibility
                                                'region' => $stock->region,
                                            ];
                                        }),
                                        'variant_configuration' => $variantConfig,
                                    ];
                                });
                            ?>
                            variantsData: <?php echo json_encode($variantsData, 15, 512) ?>,
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        variants: <?php echo json_encode($product->variants ?: [], 15, 512) ?>,
                        // Debug: Output variant data for inspection
                        debugVariantsData: <?php echo json_encode($variantsData ?? [], 15, 512) ?>
                    }
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            };

            // ============================================
            // Step Navigation Functions
            // ============================================
            function showStep(stepNumber) {
                // Sync CKEditor data to textareas before changing steps
                if (typeof CKEDITOR !== 'undefined') {
                    for (let instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                }

                // Clear validation errors when changing steps
                $('#validation-alerts-container').empty();
                $('.error-message').hide().text('');
                $('.is-invalid').removeClass('is-invalid');
                $('.select2-selection').removeClass('is-invalid');

                // Hide all steps
                $('.wizard-step-content').each(function() {
                    $(this).hide().removeClass('active');
                });

                // Show current step
                const $currentStep = $(`.wizard-step-content[data-step="${stepNumber}"]`);
                $currentStep.show().addClass('active');

                // Update wizard navigation
                $('.wizard-step-nav').removeClass('current completed locked');
                $('.wizard-step-nav').each(function() {
                    const step = parseInt($(this).data('step'));
                    if (step < stepNumber) {
                        $(this).addClass('completed').css('cursor', 'pointer');
                    } else if (step === stepNumber) {
                        $(this).addClass('current').css('cursor', 'pointer');
                    } else if (step === stepNumber + 1) {
                        $(this).css('cursor', 'pointer'); // Next immediate step is clickable
                    } else if (step > stepNumber + 1) {
                        $(this).addClass('locked').css('cursor', 'not-allowed');
                    }
                });

                // Update buttons
                updateNavigationButtons(stepNumber);

                // Update current step
                config.currentStep = stepNumber;

                // Scroll to top
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            }

            function updateNavigationButtons(stepNumber) {
                const $prevBtn = $('#prevBtn');
                const $nextBtn = $('#nextBtn');
                const $submitBtn = $('#submitBtn');

                // Previous button
                if (stepNumber === 1) {
                    $prevBtn.hide();
                } else {
                    $prevBtn.show();
                }

                // Next/Submit buttons
                if (stepNumber === config.totalSteps) {
                    $nextBtn.hide();
                    $submitBtn.show();
                } else {
                    $nextBtn.show();
                    $submitBtn.hide();
                }
            }

            function validateStep(stepNumber) {
                let isValid = true;
                const errors = [];

                // Clear previous errors
                $('.error-message').hide().text('');
                $('.is-invalid').removeClass('is-invalid');
                $('.select2-selection').removeClass('is-invalid');

                switch (stepNumber) {
                    case 1:
                        // Validate basic information
                        // Title validation
                        let hasTitles = false;
                        $('[name^="translations"][name$="[title]"]').each(function() {
                            const $input = $(this);
                            if ($input.val().trim()) {
                                hasTitles = true;
                            } else {
                                // Add is-invalid class to empty title fields
                                $input.addClass('is-invalid');
                                $input.next('.error-message').text(
                                    '<?php echo e(__('catalogmanagement::product.title_required')); ?>').show();
                            }
                        });
                        if (!hasTitles) {
                            errors.push('<?php echo e(__('catalogmanagement::product.title_required')); ?>');
                            isValid = false;
                        }

                        // SKU validation
                        if (!$('#sku').val().trim()) {
                            $('#error-sku').text('<?php echo e(__('catalogmanagement::product.sku_required')); ?>').show();
                            $('#sku').addClass('is-invalid');
                            isValid = false;
                        }

                        // Brand validation
                        if (!$('#brand_id').val()) {
                            $('#error-brand_id').text('<?php echo e(__('catalogmanagement::product.brand_required')); ?>').show();
                            $('#brand_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                            isValid = false;
                        }

                        // Vendor validation (for admin users)
                        <?php if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds())): ?>
                            if (!$('#vendor_id').val()) {
                                $('#error-vendor_id').text('<?php echo e(__('catalogmanagement::product.vendor_required')); ?>')
                                    .show();
                                $('#vendor_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                                isValid = false;
                            }
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        // Department validation
                        if (!$('#department_id').val()) {
                            $('#error-department_id').text(
                                '<?php echo e(__('catalogmanagement::product.department_required')); ?>').show();
                            $('#department_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                            isValid = false;
                        }

                        // Category validation
                        if (!$('#category_id').val()) {
                            $('#error-category_id').text('<?php echo e(__('catalogmanagement::product.category_required')); ?>')
                                .show();
                            $('#category_id').next('.select2').find('.select2-selection').addClass('is-invalid');
                            isValid = false;
                        }

                        // Max per order validation
                        if (!$('#max_per_order').val() || $('#max_per_order').val() < 1) {
                            $('#error-max_per_order').text(
                                '<?php echo e(__('catalogmanagement::product.max_per_order_required')); ?>').show();
                            $('#max_per_order').addClass('is-invalid');
                            isValid = false;
                        }
                        break;

                    case 2:
                        // Main image validation (optional in edit mode)
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($product)): ?>
                            // Only required in create mode
                            const mainImageInput = $('#main_image')[0];
                            const hasExistingImage = $('#main_image').data('existing-image');

                            // Check if there's a new file selected or an existing image
                            if (!mainImageInput.files || mainImageInput.files.length === 0) {
                                if (!hasExistingImage) {
                                    $('#main_image').addClass('is-invalid');
                                    // Add is-invalid class to image preview container
                                    $('#main_image').closest('.image-upload-wrapper').find('.image-preview-container')
                                        .addClass('is-invalid');
                                    // Show error message under the image upload
                                    const $errorContainer = $('#main_image').closest('.image-upload-wrapper').find(
                                        '.error-message');
                                    if ($errorContainer.length === 0) {
                                        $('#main_image').closest('.image-upload-wrapper').append(
                                            '<div class="error-message text-danger mt-2" style="display: block;"><?php echo e(__('catalogmanagement::product.main_image_required')); ?></div>'
                                        );
                                    } else {
                                        $errorContainer.text(
                                            '<?php echo e(__('catalogmanagement::product.main_image_required')); ?>').show();
                                    }
                                    isValid = false;
                                }
                            }
                        <?php endif; ?>
                        break;

                    case 3:
                        // Configuration type validation
                        if (!$('#configuration_type').val()) {
                            $('#error-configuration_type').text(
                                '<?php echo e(__('catalogmanagement::product.configuration_type_required')); ?>').show();
                            $('#configuration_type').addClass('is-invalid');
                            isValid = false;
                        } else {
                            const configurationType = $('#configuration_type').val();

                            if (configurationType === 'simple') {
                                // Validate simple product pricing and stock
                                const $priceInput = $('#simple-product-pricing-stock .price-input');
                                if ($priceInput.length > 0) {
                                    if (!$priceInput.val() || parseFloat($priceInput.val()) < 0) {
                                        $priceInput.addClass('is-invalid');
                                        $priceInput.next('.error-message').text(
                                            '<?php echo e(__('catalogmanagement::product.price_required')); ?>').show();
                                        isValid = false;
                                    }

                                    // Validate at least one stock row
                                    const stockRows = $('#simple-product-pricing-stock .stock-row').length;
                                    if (stockRows === 0) {
                                        errors.push('<?php echo e(__('catalogmanagement::product.stock_required')); ?>');
                                        isValid = false;
                                    } else {
                                        // Validate each stock row
                                        $('#simple-product-pricing-stock .stock-row').each(function() {
                                            const $row = $(this);
                                            const regionId = $row.find('.region-select').val();
                                            const quantity = $row.find('.quantity-input').val();

                                            if (!regionId) {
                                                $row.find('.region-select').next('.select2').find(
                                                    '.select2-selection').addClass('is-invalid');
                                                isValid = false;
                                            }

                                            if (!quantity || parseInt(quantity) < 0) {
                                                $row.find('.quantity-input').addClass('is-invalid');
                                                isValid = false;
                                            }
                                        });
                                    }
                                }
                            } else if (configurationType === 'variants') {
                                // Validate that at least one variant exists (check both new variant boxes and existing variant sections)
                                const variantBoxes = $('.variant-box').length;
                                const existingVariantSections = $('[id$="-section"]').filter(function() {
                                    return $(this).attr('id').match(/^variant-\d+-section$/);
                                }).length;
                                const totalVariants = variantBoxes + existingVariantSections;

                                if (totalVariants === 0) {
                                    errors.push(
                                        '<?php echo e(__('catalogmanagement::product.at_least_one_variant_required') ?? 'At least one variant is required'); ?>'
                                    );
                                    isValid = false;
                                } else {
                                    // Validate each variant
                                    $('.variant-box').each(function() {
                                        const $variantBox = $(this);
                                        const variantIndex = $variantBox.data('variant-index');

                                        // Check if variant configuration is selected
                                        const variantConfigId = $variantBox.find('.selected-variant-id').val();
                                        const variantKeyId = $variantBox.find('.variant-key-select').val();

                                        if (!variantConfigId && !variantKeyId) {
                                            $variantBox.find('.variant-key-select').next('.select2').find(
                                                '.select2-selection').addClass('is-invalid');
                                            errors.push(
                                                `Variant ${variantIndex + 1}: <?php echo e(__('catalogmanagement::product.variant_configuration_required') ?? 'Please select a variant configuration'); ?>`
                                            );
                                            isValid = false;
                                        }

                                        // Validate pricing if pricing section is visible
                                        const $pricingSection = $(`#variant-${variantIndex}-pricing-stock`);
                                        if ($pricingSection.is(':visible')) {
                                            // Validate SKU
                                            const $skuInput = $pricingSection.find('.variant-sku-input');
                                            if ($skuInput.length > 0 && !$skuInput.val().trim()) {
                                                $skuInput.addClass('is-invalid');
                                                isValid = false;
                                            }

                                            // Validate price
                                            const $priceInput = $pricingSection.find('.variant-price-input');
                                            if ($priceInput.length > 0 && (!$priceInput.val() || parseFloat(
                                                    $priceInput.val()) < 0)) {
                                                $priceInput.addClass('is-invalid');
                                                isValid = false;
                                            }

                                            // Validate at least one stock row
                                            const stockRows = $pricingSection.find('.stock-row').length;
                                            if (stockRows === 0) {
                                                errors.push(
                                                    `Variant ${variantIndex + 1}: <?php echo e(__('catalogmanagement::product.stock_required') ?? 'At least one stock entry is required'); ?>`
                                                );
                                                isValid = false;
                                            } else {
                                                // Validate each stock row
                                                $pricingSection.find('.stock-row').each(function() {
                                                    const $row = $(this);
                                                    const regionId = $row.find('.region-select').val();
                                                    const quantity = $row.find('.quantity-input').val();

                                                    if (!regionId) {
                                                        $row.find('.region-select').next('.select2')
                                                            .find('.select2-selection').addClass(
                                                                'is-invalid');
                                                        isValid = false;
                                                    }

                                                    if (!quantity || parseInt(quantity) < 0) {
                                                        $row.find('.quantity-input').addClass(
                                                            'is-invalid');
                                                        isValid = false;
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        }
                        break;

                    case 4:
                        // SEO validation (optional)
                        break;
                }

                // Log errors but don't show alert (inline errors are enough)
                if (errors.length > 0) {
                    console.log('❌ Validation errors found:', errors);
                    // Don't show alert - inline errors are displayed under each field
                } else {
                    console.log('✅ Validation passed for step:', stepNumber);
                }

                return isValid;
            }

            function showValidationErrors(errors) {
                const $container = $('#validation-alerts-container');
                $container.empty();

                const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show d-block" role="alert">
                <strong><i class="uil uil-exclamation-triangle"></i> ${config.translations.error}:</strong>
                <ul class="mb-0 mt-2">
                    ${errors.map(error => `<li>${error}</li>`).join('')}
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

                $container.html(alertHtml);
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            }

            // ============================================
            // Cascading Dropdowns Functions
            // ============================================
            function loadDepartmentsByVendor(vendorId) {
                const $departmentSelect = $('#department_id');
                const $categorySelect = $('#category_id');
                const $subCategorySelect = $('#sub_category_id');

                // Reset dependent dropdowns
                resetSelect($departmentSelect);
                resetSelect($categorySelect);
                resetSelect($subCategorySelect);

                if (!vendorId) {
                    return;
                }

                console.log('📦 Loading departments for vendor:', vendorId);

                // Show loading
                $departmentSelect.prop('disabled', true);
                $departmentSelect.html(`<option value="">${config.translations.loading}...</option>`);

                // Make AJAX request to your existing API
                $.ajax({
                    url: `${config.apiBaseUrl}/departments`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        "X-Country-Code": $("meta[name='currency_country_code']").attr("content")
                    },
                    data: {
                        vendor_id: vendorId,
                        country_id: $("meta[name='current_country_id']").attr("content"),
                        select2: true,
                        params: true
                    },
                    success: function(response) {
                        console.log('✅ Departments loaded:', response);

                        // Destroy Select2 first
                        if ($departmentSelect.hasClass('select2-hidden-accessible')) {
                            $departmentSelect.select2('destroy');
                        }

                        $departmentSelect.prop('disabled', false);
                        $departmentSelect.html(
                            `<option value="">${config.translations.selectOption}</option>`);

                        // Handle response data structure
                        const departments = response.data || response;

                        if (departments && departments.length > 0) {
                            departments.forEach(function(dept) {
                                $departmentSelect.append(
                                    `<option value="${dept.id}">${dept.name || dept.text}</option>`
                                );
                            });
                        }

                        // Reinitialize Select2
                        $departmentSelect.select2({
                            placeholder: config.translations.selectOption,
                            width: '100%',
                            theme: 'bootstrap-5'
                        });

                        // Auto-select department if in edit mode
                        if (config.selectedValues && config.selectedValues.department_id) {
                            console.log('🎯 Auto-selecting department:', config.selectedValues
                                .department_id);
                            $departmentSelect.val(config.selectedValues.department_id).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading departments:', error);
                        $departmentSelect.prop('disabled', false);
                        $departmentSelect.html(`<option value="">${config.translations.error}</option>`);

                        showNotification('error', '<?php echo e(__('common.error_loading_departments')); ?>');
                    }
                });
            }

            function loadCategoriesByDepartment(departmentId) {
                const $categorySelect = $('#category_id');
                const $subCategorySelect = $('#sub_category_id');

                // Reset dependent dropdowns
                resetSelect($categorySelect);
                resetSelect($subCategorySelect);

                if (!departmentId) {
                    return;
                }

                console.log('🏢 Loading categories for department:', departmentId);

                // Show loading
                $categorySelect.prop('disabled', true);
                $categorySelect.html(`<option value="">${config.translations.loading}...</option>`);

                // Make AJAX request to your existing API
                $.ajax({
                    url: `${config.apiBaseUrl}/categories`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        'X-Country-Code': $("meta[name='currency_country_code']").attr('content'),
                    },
                    data: {
                        department_id: departmentId,
                        country_id: $("meta[name='current_country_id']").attr("content"),
                        select2: true,
                        params: true
                    },
                    success: function(response) {
                        console.log('✅ Categories loaded:', response);

                        // Destroy Select2 first
                        if ($categorySelect.hasClass('select2-hidden-accessible')) {
                            $categorySelect.select2('destroy');
                        }

                        $categorySelect.prop('disabled', false);
                        $categorySelect.html(
                            `<option value="">${config.translations.selectOption}</option>`);

                        // Handle response data structure
                        const categories = response.data || response;

                        if (categories && categories.length > 0) {
                            categories.forEach(function(cat) {
                                $categorySelect.append(
                                    `<option value="${cat.id}">${cat.name || cat.text}</option>`
                                );
                            });
                        }

                        // Reinitialize Select2
                        $categorySelect.select2({
                            placeholder: config.translations.selectOption,
                            width: '100%',
                            theme: 'bootstrap-5',
                        });

                        // Auto-select category if in edit mode
                        if (config.selectedValues && config.selectedValues.category_id) {
                            console.log('🎯 Auto-selecting category:', config.selectedValues.category_id);
                            $categorySelect.val(config.selectedValues.category_id).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading categories:', error);
                        $categorySelect.prop('disabled', false);
                        $categorySelect.html(`<option value="">${config.translations.error}</option>`);

                        showNotification('error', '<?php echo e(__('common.error_loading_categories')); ?>');
                    }
                });
            }

            function loadSubCategoriesByCategory(categoryId) {
                const $subCategorySelect = $('#sub_category_id');

                // Reset dropdown
                resetSelect($subCategorySelect);

                if (!categoryId) {
                    return;
                }

                console.log('📁 Loading subcategories for category:', categoryId);

                // Show loading
                $subCategorySelect.prop('disabled', true);
                $subCategorySelect.html(`<option value="">${config.translations.loading}...</option>`);

                // Make AJAX request to your existing API
                $.ajax({
                    url: `${config.apiBaseUrl}/subcategories`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        'X-Country-Code': $("meta[name='currency_country_code']").attr('content'),
                    },
                    data: {
                        category_id: categoryId,
                        country_id: $("meta[name='current_country_id']").attr("content"),
                        select2: true,
                        params: true
                    },
                    success: function(response) {
                        console.log('✅ Subcategories loaded:', response);

                        // Destroy Select2 first
                        if ($subCategorySelect.hasClass('select2-hidden-accessible')) {
                            $subCategorySelect.select2('destroy');
                        }

                        $subCategorySelect.prop('disabled', false);
                        $subCategorySelect.html(
                            `<option value="">${config.translations.selectOption}</option>`);

                        // Handle response data structure
                        const subcategories = response.data || response;

                        if (subcategories && subcategories.length > 0) {
                            subcategories.forEach(function(subCat) {
                                $subCategorySelect.append(
                                    `<option value="${subCat.id}">${subCat.name || subCat.text}</option>`
                                );
                            });
                        }

                        // Reinitialize Select2
                        $subCategorySelect.select2({
                            placeholder: config.translations.selectOption,
                            width: '100%',
                            theme: 'bootstrap-5',
                        });

                        // Auto-select sub-category if in edit mode
                        if (config.selectedValues && config.selectedValues.sub_category_id) {
                            console.log('🎯 Auto-selecting sub-category:', config.selectedValues
                                .sub_category_id);
                            $subCategorySelect.val(config.selectedValues.sub_category_id).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading subcategories:', error);
                        $subCategorySelect.prop('disabled', false);
                        $subCategorySelect.html(`<option value="">${config.translations.error}</option>`);

                        showNotification('error', '<?php echo e(__('common.error_loading_subcategories')); ?>');
                    }
                });
            }

            function resetSelect($select) {
                $select.val(null).trigger('change');
            }

            function showNotification(type, message) {
                console.log(`[${type.toUpperCase()}] ${message}`);

                // Optional: Show toast notification if available
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                }
            }

            // Configuration Type Handler removed

            // ============================================
            // Pricing & Stock Management Functions
            // ============================================
            let stockRowCounter = 0;
            let regionsData = []; // Store regions globally

            // Calculate and update total quantity
            function updateTotalQuantity($table) {
                let total = 0;
                $table.find('.quantity-input').each(function() {
                    const qty = parseInt($(this).val()) || 0;
                    total += qty;
                });
                $table.find('.total-quantity-display').text(total);
                console.log('📊 Total quantity updated:', total);
            }

            function createPricingStockBox(containerId, namePrefix, index = 0) {
                const template = $('#pricing-stock-template').html();

                if (!template) {
                    console.error('❌ Pricing stock template not found!');
                    return;
                }

                let html = template.replace(/__INDEX__/g, index);

                // Handle empty prefix (for simple products, fields should be at root level)
                if (namePrefix) {
                    // With prefix: variants[0][price]
                    html = html.replace(/__NAME_PREFIX__\[/g, `${namePrefix}[`);
                } else {
                    // Without prefix: price (remove __NAME_PREFIX__[ and the closing ])
                    html = html.replace(/__NAME_PREFIX__\[([^\]]+)\]/g, '$1');
                }

                $(`#${containerId}`).html(html);

                // For simple products, remove the SKU field from the template (it's in Step 1)
                if (!namePrefix) {
                    $(`#${containerId} .variant-sku-field`).remove();
                }

                // For variants, update the "<?php echo e(__('catalogmanagement::product.add_region')); ?>" button to use the correct class and data attribute
                if (namePrefix && namePrefix.includes('variants[')) {
                    const variantIndex = namePrefix.match(/variants\[(\d+)\]/)?.[1];
                    if (variantIndex) {
                        $(`#${containerId} .add-stock-row`)
                            .removeClass('add-stock-row')
                            .addClass('add-variant-stock-row')
                            .attr('data-variant-index', variantIndex);
                    }
                }

                // Initialize Select2 for region selects
                setTimeout(function() {
                    $(`#${containerId} .select2`).select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                    });
                }, 100);

                // Add first stock row
                addStockRow(containerId, namePrefix);

                console.log('✅ Pricing & Stock box created for:', namePrefix);
            }

            function addStockRow(containerId, namePrefix) {
                const $template = $('#stock-row-template');

                if ($template.length === 0) {
                    console.error('❌ Stock row template not found!');
                    return;
                }

                // Get template HTML content
                let template = $template.html();

                if (!template) {
                    console.error('❌ Stock row template content is empty!');
                    return;
                }

                let html = template.replace(/__STOCK_INDEX__/g, stockRowCounter);

                // Handle empty prefix (for simple products)
                if (namePrefix) {
                    // With prefix: variants[0][stocks][0][region_id]
                    html = html.replace(/__NAME_PREFIX__\[stocks\]/g, `${namePrefix}[stocks]`);
                } else {
                    // Without prefix: stocks[0][region_id] (remove __NAME_PREFIX__ completely)
                    html = html.replace(/__NAME_PREFIX__\[stocks\]/g, 'stocks');
                }

                $(`#${containerId} .stock-rows`).append(html);

                // Get current vendor_id and refresh regions if needed
                const vendorId = $('#vendor_id').val();
                if (vendorId) {
                    console.log('🔄 Refreshing regions for vendor:', vendorId);
                    loadRegions(vendorId);
                }

                // Populate region select with vendor-filtered data
                setTimeout(function() {
                    const $regionSelect = $(`#${containerId} .stock-rows tr:last .region-select`);

                    // Clear existing options except placeholder
                    $regionSelect.find('option:not(:first)').remove();

                    // Add vendor-filtered regions
                    regionsData.forEach(function(region) {
                        $regionSelect.append(`<option value="${region.id}">${region.name}</option>`);
                    });

                    // Initialize Select2
                    $regionSelect.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: '<?php echo e(__('common.select_region')); ?>'
                    });

                    console.log('✅ Region select populated with', regionsData.length,
                        'vendor-filtered regions');
                }, 200);

                stockRowCounter++;
                console.log('✅ Stock row added');
            }

            // Load regions based on vendor_id
            function loadRegions(vendorId = null) {
                console.log('🌍 Loading regions from API...', vendorId ? `for vendor: ${vendorId}` : 'all regions');

                // Get vendor_id from parameter or from the select/input field
                if (!vendorId) {
                    vendorId = $('#vendor_id').val();
                }

                const requestData = {
                    select2: true
                };

                // Add vendor_id to request if available
                if (vendorId) {
                    requestData.vendor_id = vendorId;
                    requestData.country_id = $("meta[name='current_country_id']").attr("content");
                    requestData.vendor_selected_regions = true;
                }

                $.ajax({
                    url: '/api/area/regions',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        'X-Country-Code': $("meta[name='currency_country_code']").attr('content'),
                    },
                    data: requestData,
                    success: function(response) {
                        console.log(response)
                        const data = response.data;
                        regionsData = data.map(function(region) {
                            return {
                                id: region.id,
                                name: region.name || region.text
                            };
                        });
                        console.log('✅ Regions loaded:', regionsData.length, 'regions');

                        // Update existing region selects with new data
                        updateRegionSelects();
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading regions:', error);
                        // Fallback to empty regions if error
                        regionsData = [];
                        updateRegionSelects();
                    }
                });
            }

            // Update all existing region selects with new data
            function updateRegionSelects() {
                $('.region-select').each(function() {
                    const $select = $(this);
                    const currentValue = $select.val();

                    // Clear existing options except the first placeholder
                    $select.find('option:not(:first)').remove();

                    // Add new regions
                    regionsData.forEach(function(region) {
                        $select.append(`<option value="${region.id}">${region.name}</option>`);
                    });

                    // Restore previous value if it still exists
                    if (currentValue && regionsData.find(r => r.id == currentValue)) {
                        $select.val(currentValue);
                    }

                    // Initialize Select2 if not already initialized
                    if (!$select.hasClass('select2-hidden-accessible')) {
                        $select.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: "<?php echo e(__('common.select')); ?>"
                        });
                    } else {
                        // Trigger Select2 update for already initialized selects
                        $select.trigger('change');
                    }
                });
            }

            function createVariantPricingStockBox(variantIndex, variantName) {
                const containerId = `variant-${variantIndex}-pricing-stock`;
                const namePrefix = `variants[${variantIndex}]`;

                // Show the pricing stock container
                $(`#${containerId}`).show();

                createPricingStockBox(containerId, namePrefix, variantIndex);

                // Show SKU field for variants
                $(`#${containerId} .variant-sku-field`).show();

                console.log('✅ Variant pricing & stock created for:', variantName);
            }

            // ============================================
            // Variant Management Functions
            // ============================================
            let variantCounter = 1000; // Start with high number to avoid conflicts with existing variants
            let variantKeysData = [];

            // Load variant keys from API
            function loadVariantKeys() {
                console.log('🔑 Loading variant keys from API...');

                const countryId = $("meta[name='current_country_id']").attr("content");

                $.ajax({
                    url: '<?php echo e(route('admin.api.variant-keys')); ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        country_id: countryId
                    },
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        'X-Country-Code': $("meta[name='currency_country_code']").attr('content'),
                    },
                    success: function(response) {
                        variantKeysData = response.data || response;
                        console.log('✅ Variant keys loaded:', variantKeysData.length, 'keys');
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading variant keys:', error);
                    }
                });
            }

            // Add new variant box
            function addVariantBox() {
                const template = $('#variant-box-template').html();
                const html = template
                    .replace(/__VARIANT_INDEX__/g, variantCounter)
                    .replace(/__VARIANT_NUMBER__/g, variantCounter + 1);

                $('#variants-container').append(html);
                $('#variants-empty-state').hide();

                // Populate variant keys
                const $keySelect = $(`#variant-${variantCounter} .variant-key-select`);
                variantKeysData.forEach(function(key) {
                    $keySelect.append(`<option value="${key.id}">${key.name}</option>`);
                });

                // Initialize Select2
                setTimeout(function() {
                    $keySelect.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                    });
                }, 100);

                variantCounter++;
                console.log('✅ Variant box added');

                // Update remove buttons visibility
                updateVariantRemoveButtons();
            }

            // Function to update visibility of remove buttons
            function updateVariantRemoveButtons() {
                const existingVariantsCount = $('.existing-variant-wrapper').length;
                const newVariantsCount = $('#variants-container .variant-box').length;
                const totalVariants = existingVariantsCount + newVariantsCount;

                console.log('🔄 Updating remove buttons. Total variants:', totalVariants);

                if (totalVariants <= 1) {
                    // Hide all remove buttons if only 1 variant exists
                    $('.remove-existing-variant-btn').hide();
                    $('.remove-variant-btn').hide();
                } else {
                    // Show all remove buttons if more than 1 variant exists
                    $('.remove-existing-variant-btn').show();
                    $('.remove-variant-btn').show();
                }
            }

            // Load variants by key (root level - no parent)
            function loadVariantsByKey(variantIndex, keyId) {
                console.log('🌳 Loading root variants for key:', keyId);

                const $container = $(`#variant-${variantIndex} .variant-tree-container`);
                const $levelsContainer = $(`#variant-${variantIndex} .variant-tree-levels`);

                // Clear previous tree and pricing/stock
                $levelsContainer.empty();
                $container.hide();
                $(`#variant-${variantIndex}-pricing-stock`).hide().empty();
                $(`#variant-${variantIndex} .selected-variant-path`).hide();

                // Store keyId in the variant box for later use
                $(`#variant-${variantIndex}`).data('current-key-id', keyId);

                const countryId = $("meta[name='current_country_id']").attr("content");

                $.ajax({
                    url: '<?php echo e(route('admin.api.variants-by-key')); ?>',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        'X-Country-Code': $("meta[name='currency_country_code']").attr('content'),
                    },
                    data: {
                        key_id: keyId,
                        country_id: countryId,
                    },
                    success: function(response) {
                        const variants = response.data || response;
                        console.log('✅ Root variants loaded:', variants.length);

                        if (variants.length > 0) {
                            $container.show();
                            addVariantLevel($levelsContainer, variants, variantIndex, 0, []);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading variants:', error);
                    }
                });
            }

            // Add a level to the variant tree
            function addVariantLevel($container, variants, variantIndex, level, selectedPath) {
                const levelDiv = $('<div>', {
                    class: 'variant-level mb-3',
                    'data-level': level
                });

                const select = $('<select>', {
                    class: 'form-control select2 variant-value-select',
                    'data-variant-index': variantIndex,
                    'data-level': level
                });

                select.append('<option value=""><?php echo e(__('common.select_option')); ?></option>');

                variants.forEach(function(variant) {
                    const hasChildren = variant.has_children || false;
                    const treeIcon = hasChildren ? ' 🌳' : '';
                    select.append(
                        `<option value="${variant.id}" data-has-children="${hasChildren}">${variant.name}${treeIcon}</option>`
                    );
                });

                levelDiv.append(select);
                $container.append(levelDiv);

                // Initialize Select2
                setTimeout(function() {
                    select.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                    });
                }, 100);
            }


            // Load child variants
            function loadChildVariants(variantIndex, parentId, level, selectedPath, keyId) {
                console.log('🌳 Loading child variants for parent:', parentId, 'at level:', level);

                const $levelsContainer = $(`#variant-${variantIndex} .variant-tree-levels`);

                // Remove all levels after current level
                $levelsContainer.find('.variant-level').each(function() {
                    if (parseInt($(this).data('level')) > level) {
                        $(this).remove();
                    }
                });

                const countryId = $("meta[name='current_country_id']").attr("content");

                $.ajax({
                    url: '<?php echo e(route('admin.api.variants-by-key')); ?>',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        'X-Country-Code': $("meta[name='currency_country_code']").attr('content'),
                    },
                    data: {
                        key_id: keyId,
                        parent_id: parentId,
                        country_id: countryId
                    },
                    success: function(response) {
                        const variants = response.data || response;
                        console.log('✅ Child variants loaded:', variants.length);

                        if (variants.length > 0) {
                            addVariantLevel($levelsContainer, variants, variantIndex, level + 1,
                                selectedPath);
                        } else {
                            // No more children - this is the final selection
                            finalizeVariantSelection(variantIndex, parentId, selectedPath);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading child variants:', error);
                    }
                });
            }

            // Finalize variant selection and show pricing/stock
            function finalizeVariantSelection(variantIndex, variantId, path) {
                console.log('✅ Final variant selected:', variantId, 'Path:', path);

                // Set hidden input
                $(`#variant-${variantIndex} .selected-variant-id`).val(variantId);

                // Show selected path
                const $pathAlert = $(`#variant-${variantIndex} .selected-variant-path`);
                $pathAlert.find('.path-text').text(path.join(' > '));
                $pathAlert.show();

                // Create pricing & stock box
                createVariantPricingStockBox(variantIndex, path[path.length - 1]);
            }

            // ============================================
            // Simple Product Functions
            // ============================================
            function toggleSimpleDiscountFields() {
                const isChecked = $('#simple_discount').is(':checked');
                const $discountFields = $('#simple_discount_fields');

                if (isChecked) {
                    $discountFields.show();
                } else {
                    $discountFields.hide();
                }

                console.log('🏷️ Simple discount toggled:', isChecked);
            }

            // Make function globally accessible for inline onchange attribute
            window.toggleSimpleDiscountFields = toggleSimpleDiscountFields;

            // Initialize existing variant discount fields on page load
            function initializeVariantDiscountFields() {
                console.log('🔧 Initializing variant discount fields...');

                // Find all variant discount checkboxes and initialize their states
                $('[id^="discount_"]').each(function() {
                    const $checkbox = $(this);
                    const variantIndex = $checkbox.attr('id').replace('discount_', '');

                    // Call the toggle function to properly initialize the fields
                    if (typeof window.toggleDiscountFields === 'function') {
                        window.toggleDiscountFields(variantIndex);
                        console.log(
                            `🔄 Initialized variant ${variantIndex} discount fields using toggleDiscountFields`
                        );
                    } else {
                        // Fallback if function not available yet
                        const isChecked = $checkbox.is(':checked');
                        const $discountFields = $(`#discount_fields_${variantIndex}`);

                        if (isChecked) {
                            $discountFields.show();
                            console.log(`✅ Variant ${variantIndex} discount fields shown (fallback)`);
                        } else {
                            $discountFields.hide();
                            console.log(`❌ Variant ${variantIndex} discount fields hidden (fallback)`);
                        }
                    }
                });

                console.log('✅ Variant discount fields initialized');
            }

            // Initialize existing variant stock selects with Select2
            function initializeVariantStockSelects() {
                console.log('🔧 Initializing variant stock selects...');

                // Find all variant stock region selects and initialize Select2
                $('select[name*="variants"][name*="stocks"][name*="region_id"]').each(function() {
                    const $select = $(this);
                    if (!$select.hasClass('select2-hidden-accessible')) {
                        $select.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: "<?php echo e(__('common.select')); ?>"
                        });
                        console.log('✅ Initialized Select2 for variant stock select');
                    }
                });

                console.log('✅ Variant stock selects initialized');
            }

            // Comprehensive variant initialization function
            function initializeExistingVariants() {
                console.log('🚀 Initializing all existing variant components...');

                // Initialize discount fields
                initializeVariantDiscountFields();

                // Initialize stock selects with a small delay
                setTimeout(function() {
                    initializeVariantStockSelects();
                }, 100);

                console.log('🎉 All variant components initialized');
            }

            // ============================================
            // Stock Total Calculation Function
            // ============================================
            // Function to update total stock display for a variant or simple product
            function updateVariantTotalStock($quantityInput) {
                // Try to find the parent table first (works for both existing and new variants)
                const $table = $quantityInput.closest('table');
                if ($table.length) {
                    const $tbody = $table.find('tbody');

                    // Calculate total stock from all quantity inputs in this table
                    let totalStock = 0;
                    $tbody.find('.quantity-input').each(function() {
                        const qty = parseInt($(this).val()) || 0;
                        totalStock += qty;
                    });

                    // Update the total stock display
                    const $totalStockDisplay = $table.find('.total-stock-display');
                    if ($totalStockDisplay.length) {
                        $totalStockDisplay.text(totalStock);
                        console.log('📦 Updated total stock:', totalStock);
                    }
                }
            }

            // Add stock row for simple product
            function addSimpleStockRow() {
                const $tbody = $('#simple-stock-rows');
                const rowCount = $tbody.find('tr').length;

                // Get current vendor_id
                const vendorId = $('#vendor_id').val();

                // Refresh regions data for current vendor before adding row
                if (vendorId) {
                    console.log('🔄 Refreshing regions for vendor:', vendorId);
                    loadRegions(vendorId);
                }

                const newRow = `
            <tr class="stock-row">
                <td>
                    <select name="stocks[${rowCount}][region_id]" class="form-control region-select select2" required>
                        <option value=""><?php echo e(__('catalogmanagement::product.select_region')); ?></option>
                    </select>
                </td>
                <td>
                    <input type="number" name="stocks[${rowCount}][quantity]" class="form-control quantity-input" value="0" min="0" placeholder="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-stock-row">
                        <i class="uil uil-trash-alt m-0"></i>
                    </button>
                </td>
            </tr>
        `;

                $tbody.append(newRow);

                // Populate the new row's region select with filtered regions and initialize Select2
                setTimeout(function() {
                    const $newRegionSelect = $tbody.find('tr:last .region-select');

                    // Clear existing options except placeholder
                    $newRegionSelect.find('option:not(:first)').remove();

                    // Add vendor-filtered regions
                    regionsData.forEach(function(region) {
                        $newRegionSelect.append(`<option value="${region.id}">${region.name}</option>`);
                    });

                    // Initialize Select2 for the new select
                    $newRegionSelect.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: "<?php echo e(__('common.select')); ?>"
                    });

                    console.log('✅ Populated new stock row with', regionsData.length,
                        'vendor-filtered regions and initialized Select2');
                }, 200);

                console.log('📦 Added simple stock row');
            }

            // Remove stock row
            function removeStockRow(button) {
                const $row = $(button).closest('tr');
                const $tbody = $row.closest('tbody');

                // Remove the row
                $row.remove();
                console.log('🗑️ Removed stock row');

                // Update total stock after removing row
                // Calculate new total from remaining rows
                let totalStock = 0;
                $tbody.find('.quantity-input').each(function() {
                    const qty = parseInt($(this).val()) || 0;
                    totalStock += qty;
                });

                // Find and update the total stock display in the same table
                const $table = $tbody.closest('table');
                const $totalStockDisplay = $table.find('.total-stock-display');
                if ($totalStockDisplay.length) {
                    $totalStockDisplay.text(totalStock);
                    console.log('📦 Updated total stock after removal:', totalStock);
                }
            }

            // Add stock row for variant product
            function addVariantStockRow(variantIndex) {
                // Try existing variant structure first, then new variant structure
                let $tbody = $(`#variant-${variantIndex}-stock-rows`);
                if ($tbody.length === 0) {
                    $tbody = $(`#variant-${variantIndex}-pricing-stock .stock-rows`);
                }

                const rowCount = $tbody.find('tr').length;

                console.log('🔍 Looking for tbody - trying existing variant structure first...');
                console.log('📊 Found tbody:', $tbody.length > 0 ? 'Yes' : 'No');
                console.log('📈 Current row count:', rowCount);

                if ($tbody.length === 0) {
                    console.error('❌ Stock table body not found for variant:', variantIndex);
                    return;
                }

                // Use existing regions data (already loaded for the vendor)
                console.log('📋 Using existing regions data:', regionsData.length, 'regions');

                const newRow = `
            <tr class="stock-row">
                <td>
                    <select name="variants[${variantIndex}][stocks][${rowCount}][region_id]" class="form-control select2 region-select" required>
                        <option value=""><?php echo e(__('catalogmanagement::product.select_region')); ?></option>
                    </select>
                </td>
                <td>
                    <input type="number" name="variants[${variantIndex}][stocks][${rowCount}][quantity]" class="form-control quantity-input" value="0" min="0" placeholder="0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-stock-row">
                        <i class="uil uil-trash-alt m-0"></i>
                    </button>
                </td>
            </tr>
        `;

                $tbody.append(newRow);

                // Populate the new row's region select with filtered regions and initialize Select2
                setTimeout(function() {
                    const $newRegionSelect = $tbody.find('tr:last .region-select');

                    // Clear existing options except placeholder
                    $newRegionSelect.find('option:not(:first)').remove();

                    // Add vendor-filtered regions
                    regionsData.forEach(function(region) {
                        $newRegionSelect.append(`<option value="${region.id}">${region.name}</option>`);
                    });

                    // Initialize Select2 for the new select
                    $newRegionSelect.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: '<?php echo e(__('catalogmanagement::product.select_region')); ?>'
                    });

                    console.log('✅ Populated new variant stock row with', regionsData.length,
                        'vendor-filtered regions and initialized Select2');

                    // Update total stock after adding row
                    const $newQuantityInput = $tbody.find('tr:last .quantity-input');
                    if ($newQuantityInput.length) {
                        updateVariantTotalStock($newQuantityInput);
                    }
                }, 200);

                console.log(`📦 Added variant ${variantIndex} stock row`);
            }

            // ============================================
            // New Variant Creation Functions
            // ============================================
            // Note: variantCounter and variantKeysData are already declared above
            // Note: addVariantBox function is defined earlier in the file

            // Load variants by key (root level - no parent)
            function loadVariantsByKey(variantIndex, keyId) {
                console.log('🔄 Loading variants for key:', keyId);

                const countryId = $("meta[name='current_country_id']").attr("content");

                $.ajax({
                    url: `/api/variant-configurations/key/${keyId}/tree`,
                    method: 'GET',
                    data: {
                        country_id: countryId
                    },
                    headers: {
                        'lang': "<?php echo e(app()->getLocale()); ?>",
                        'X-Country-Code': $("meta[name='currency_country_code']").attr("content")
                    },
                    success: function(response) {
                        console.log('✅ Variant tree response:', response);
                        if (response && response.children) {
                            buildVariantTree(variantIndex, response.children, 0, response.children);
                            $(`#variant-${variantIndex} .variant-tree-container`).show();
                        } else {
                            console.warn('⚠️ No children found in response');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Failed to load variants:', error);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }

            // Build variant tree levels
            function buildVariantTree(variantIndex, variants, level, fullTree = null) {
                const $container = $(`#variant-${variantIndex} .variant-tree-levels`);

                // Store full tree for navigation
                if (fullTree) {
                    $container.data('fullTree', fullTree);
                } else {
                    fullTree = $container.data('fullTree') || variants;
                }

                // Clear existing levels from this level onwards
                $container.find(`.variant-level[data-level="${level}"]`).nextAll().remove();
                $container.find(`.variant-level[data-level="${level}"]`).remove();

                if (!variants || variants.length === 0) return;

                const levelHtml = `
            <div class="variant-level mb-3" data-level="${level}">
                <label class="form-label">Select Option</label>
                <select class="form-control select2 variant-select" data-level="${level}">
                    <option value="">Select option</option>
                    ${variants.map(variant => {
                        const hasChildren = variant.children && variant.children.length > 0;
                        const treeIcon = hasChildren ? '🌳 ' : '';
                        return `<option value="${variant.id}" data-has-children="${hasChildren}">${treeIcon}${variant.name}</option>`;
                    }).join('')}
                </select>
            </div>
        `;

                $container.append(levelHtml);

                // Initialize Select2 for new select
                const $newSelect = $container.find(`.variant-select[data-level="${level}"]`);
                $newSelect.select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });

                // Handle selection change
                $newSelect.on('change', function() {
                    const selectedId = $(this).val();
                    const selectedVariant = variants.find(v => v.id == selectedId);

                    // Clear all subsequent levels and hide pricing/stock box
                    $container.find(`.variant-level[data-level="${level + 1}"]`).nextAll().remove();
                    $container.find(`.variant-level[data-level="${level + 1}"]`).remove();
                    $(`#variant-${variantIndex}-pricing-stock`).hide();
                    $(`#variant-${variantIndex} .selected-variant-path`).hide();
                    $(`#variant-${variantIndex} .selected-variant-id`).val('');

                    if (selectedId && selectedVariant) {
                        // Check if this variant has children (and they're not empty)
                        const hasChildren = selectedVariant.children &&
                            Array.isArray(selectedVariant.children) &&
                            selectedVariant.children.length > 0;

                        if (hasChildren) {
                            // Load children for next level
                            console.log('📂 Variant has children, loading next level...');
                            buildVariantTree(variantIndex, selectedVariant.children, level + 1, fullTree);
                        } else {
                            // This is a leaf node - final selection
                            console.log('✅ Leaf node reached, finalizing selection...');
                            setSelectedVariant(variantIndex, selectedId);
                        }
                    }
                });
            }

            // Note: loadVariantChildren function removed - using nested tree structure from single API call

            // Set selected variant and create pricing/stock box
            function setSelectedVariant(variantIndex, variantId) {
                $(`#variant-${variantIndex} .selected-variant-id`).val(variantId);

                // Build path display
                const path = [];
                $(`#variant-${variantIndex} .variant-select`).each(function() {
                    const selectedText = $(this).find('option:selected').text();
                    if (selectedText && selectedText !== 'Select option') {
                        path.push(selectedText);
                    }
                });

                $(`#variant-${variantIndex} .selected-variant-path .path-text`).text(path.join(' → '));
                $(`#variant-${variantIndex} .selected-variant-path`).show();

                // Create pricing and stock box
                createVariantPricingStockBox(variantIndex, variantId);
            }

            // Remove variant box
            function removeVariantBox(button) {
                $(button).closest('.variant-box').remove();

                // Show empty state if no variants left
                if ($('#variants-container .variant-box').length === 0) {
                    $('#variants-empty-state').show();
                }

                console.log('🗑️ Variant box removed');
            }


            // ============================================
            // Event Handlers
            // ============================================
            $(document).ready(function() {
                // Load variant keys once on page load
                loadVariantKeys();

                // Show first step
                showStep(config.currentStep);

                // Initial check for remove buttons visibility
                setTimeout(function() {
                    updateVariantRemoveButtons();
                }, 500);

                // Initialize Select2 for existing region selects
                setTimeout(function() {
                    $('.region-select').each(function() {
                        const $select = $(this);
                        if (!$select.hasClass('select2-hidden-accessible')) {
                            $select.select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: "<?php echo e(__('common.select')); ?>"
                            });
                        }
                    });
                    console.log('✅ Initialized Select2 for existing region selects');
                }, 500);

                // Initialize all existing variant components
                setTimeout(function() {
                    initializeExistingVariants();
                }, 600);

                // Debug: Log form data before submission
                $('#productForm').on('submit', function(e) {
                    console.log('📋 Form submission - checking variant discount values:');

                    // Check all variant discount fields
                    $('[id^="discount_"]').each(function() {
                        const $checkbox = $(this);
                        const variantIndex = $checkbox.attr('id').replace('discount_', '');
                        const isChecked = $checkbox.is(':checked');

                        const priceValue = $(
                            `input[name="variants[${variantIndex}][price_before_discount]"]`
                        ).val();
                        const dateValue = $(
                                `input[name="variants[${variantIndex}][discount_end_date]"]`)
                            .val();

                        console.log(`Variant ${variantIndex}:`);
                        console.log(`  - Has discount: ${isChecked}`);
                        console.log(`  - Price before discount: ${priceValue}`);
                        console.log(`  - Discount end date: ${dateValue}`);
                    });
                });

                // Simple product fields are populated directly in HTML, no JS population needed

                // Debug section for variants removed

                // Next button click
                $('#nextBtn').on('click', function(e) {
                    e.preventDefault();
                    console.log('⏭️ Next button clicked, current step:', config.currentStep);
                    if (validateStep(config.currentStep)) {
                        if (config.currentStep < config.totalSteps) {
                            showStep(config.currentStep + 1);
                        }
                    }
                });

                // Previous button click
                $('#prevBtn').on('click', function(e) {
                    e.preventDefault();
                    console.log('⏮️ Previous button clicked, current step:', config.currentStep);
                    if (config.currentStep > 1) {
                        showStep(config.currentStep - 1);
                    }
                });

                // Wizard step click
                $(document).on('click', '.wizard-step-nav', function(e) {
                    e.preventDefault();
                    const targetStep = parseInt($(this).data('step'));
                    console.log('🎯 Wizard step clicked:', targetStep, 'Current:', config.currentStep);

                    // If moving forward, validate current step first
                    if (targetStep > config.currentStep) {
                        console.log('⚠️ Moving forward, validating current step...');
                        if (!validateStep(config.currentStep)) {
                            console.log('❌ Validation failed, staying on current step');
                            return;
                        }
                    }

                    showStep(targetStep);
                });

                // Auto-load departments and regions on page load if vendor is already selected
                <?php if(isset($product)): ?>
                    // Edit mode: Load cascading dropdowns with product data
                    const productVendorId = <?php echo e($product->vendor_id ?? 'null'); ?>;
                    if (productVendorId) {
                        console.log('📦 Edit mode: Auto-loading departments and regions for vendor:',
                            productVendorId);
                        loadDepartmentsByVendor(productVendorId);
                        loadRegions(productVendorId);
                    } else {
                        // Load all regions if no vendor is selected initially
                        loadRegions();
                    }
                <?php else: ?>
                    // Create mode: Load departments and regions if vendor is selected
                    const initialVendorId = $('#vendor_id').val();
                    if (initialVendorId) {
                        console.log('📦 Create mode: Auto-loading departments and regions for vendor:',
                            initialVendorId);
                        loadDepartmentsByVendor(initialVendorId);
                        loadRegions(initialVendorId);
                    } else {
                        // Load all regions if no vendor is selected initially
                        loadRegions();
                    }
                <?php endif; ?>

                // Vendor change event - Load departments and regions based on vendor
                $('#vendor_id').on('change', function() {
                    const vendorId = $(this).val();
                    console.log('📦 Vendor changed:', vendorId);
                    loadDepartmentsByVendor(vendorId);
                    loadRegions(vendorId);
                });

                // Department change event - Load categories based on department
                $('#department_id').on('change', function() {
                    const departmentId = $(this).val();
                    console.log('🏢 Department changed:', departmentId);
                    loadCategoriesByDepartment(departmentId);
                });

                // Category change event - Load subcategories based on category
                $('#category_id').on('change', function() {
                    const categoryId = $(this).val();
                    console.log('📁 Category changed:', categoryId);
                    loadSubCategoriesByCategory(categoryId);
                });

                // Simple product event handlers
                $('#add-simple-stock-row').on('click', function() {
                    addSimpleStockRow();
                });

                $(document).on('click', '.remove-stock-row', function() {
                    removeStockRow(this);
                });

                // Simple product discount toggle handler
                $(document).on('change', '#simple_discount', function() {
                    toggleSimpleDiscountFields();
                });

                // Variant "<?php echo e(__('catalogmanagement::product.add_region')); ?>" button handler
                $(document).on('click', '.add-variant-stock-row', function() {
                    const variantIndex = $(this).data('variant-index');
                    addVariantStockRow(variantIndex);
                });

                // Add new variant button handler
                $('#add-variant-btn').on('click', function() {
                    addVariantBox();
                });

                // Remove variant button handler
                $(document).on('click', '.remove-variant-btn', function() {
                    const totalVariants = $('.existing-variant-wrapper').length + $(
                        '#variants-container .variant-box').length;
                    if (totalVariants > 1) {
                        removeVariantBox(this);
                        updateVariantRemoveButtons();
                    }
                });

                // Remove existing variant button handler
                $(document).on('click', '.remove-existing-variant-btn', function() {
                    const totalVariants = $('.existing-variant-wrapper').length + $(
                        '#variants-container .variant-box').length;

                    if (totalVariants > 1) {
                        const variantIndex = $(this).data('variant-index');
                        $(`#existing-variant-${variantIndex}`).remove();
                        console.log(`🗑️ Existing variant ${variantIndex} removed`);

                        // Show empty state if no variants left (new or existing)
                        if ($('#variants-container .variant-box').length === 0 && $(
                                '.existing-variant-wrapper').length === 0) {
                            $('#variants-empty-state').show();
                        }

                        updateVariantRemoveButtons();
                    }
                });

                // Variant key selection handler
                $(document).on('change', '.variant-key-select', function() {
                    const variantIndex = $(this).closest('.variant-box').data('variant-index');
                    const keyId = $(this).val();

                    // Clear everything when key changes
                    $(`#variant-${variantIndex} .variant-tree-levels`).empty();
                    $(`#variant-${variantIndex} .selected-variant-path`).hide();
                    $(`#variant-${variantIndex} .selected-variant-id`).val('');
                    $(`#variant-${variantIndex}-pricing-stock`).hide();

                    if (keyId) {
                        loadVariantsByKey(variantIndex, keyId);
                    } else {
                        $(`#variant-${variantIndex} .variant-tree-container`).hide();
                    }
                });

                // Discount switch handler for new variants (from template)
                $(document).on('change', '.has-discount-switch', function() {
                    const $switch = $(this);
                    const $container = $switch.closest('.pricing-stock-box');
                    const $discountFields = $container.find('.discount-fields');

                    if ($switch.is(':checked')) {
                        $discountFields.show();
                        console.log('✅ Discount enabled for new variant');
                    } else {
                        $discountFields.hide();
                        // Clear discount field values
                        $discountFields.find('input').val('');
                        console.log('❌ Discount disabled for new variant');
                    }
                });

                // Configuration type change handler - show/hide relevant sections
                $('#configuration_type').on('change', function() {
                    const selectedType = $(this).val();
                    console.log('🔄 Configuration type changed to:', selectedType);

                    // Hide all sections first
                    $('#simple-product-section').hide();
                    $('#dynamic-simple-product-section').hide();
                    $('.variant-configuration-section').hide();
                    $('#add-new-variants-section').hide();
                    $('#variants-container').empty();
                    $('#variants-empty-state').show();

                    if (selectedType === 'simple') {
                        // Remove name attribute from variant fields so they won't be submitted
                        $('.variant-configuration-section input, .variant-configuration-section select, .variant-configuration-section textarea')
                            .each(function() {
                                const $field = $(this);
                                if ($field.attr('name')) {
                                    $field.attr('data-original-name', $field.attr('name'));
                                    $field.removeAttr('name');
                                }
                            });

                        // Show existing simple product section if it exists, otherwise show dynamic section
                        if ($('#simple-product-section').length > 0) {
                            $('#simple-product-section').show();
                            // Restore name attributes for simple product fields
                            $('#simple-product-section input, #simple-product-section select, #simple-product-section textarea')
                                .each(function() {
                                    const $field = $(this);
                                    if ($field.attr('data-original-name')) {
                                        $field.attr('name', $field.attr('data-original-name'));
                                    }
                                });
                            console.log('✅ Showing existing simple product section');
                        } else {
                            // Create dynamic simple product pricing/stock box
                            $('#dynamic-simple-product-section').show();
                            createPricingStockBox('dynamic-simple-pricing-stock', '', 0);
                            console.log(
                                '✅ Showing dynamic simple product section with new pricing/stock box'
                            );
                        }
                    } else if (selectedType === 'variants') {
                        // Restore name attributes for variant fields
                        $('.variant-configuration-section input, .variant-configuration-section select, .variant-configuration-section textarea')
                            .each(function() {
                                const $field = $(this);
                                if ($field.attr('data-original-name')) {
                                    $field.attr('name', $field.attr('data-original-name'));
                                }
                            });

                        // Remove name attribute from simple product fields so they won't be submitted
                        $('#simple-product-section input, #simple-product-section select, #simple-product-section textarea')
                            .each(function() {
                                const $field = $(this);
                                if ($field.attr('name')) {
                                    $field.attr('data-original-name', $field.attr('name'));
                                    $field.removeAttr('name');
                                }
                            });

                        // Show variant sections
                        $('.variant-configuration-section').show();
                        $('#add-new-variants-section').show();
                        console.log('✅ Showing variant sections and add new variants section');
                    } else {
                        // No type selected - hide everything
                        console.log('❌ No product type selected - hiding all sections');
                    }
                });

                // ============================================
                // Clear validation errors on input change
                // ============================================

                // Clear error on title input
                $(document).on('input', '[name^="translations"][name$="[title]"]', function() {
                    const $input = $(this);
                    if ($input.val().trim()) {
                        $input.removeClass('is-invalid');
                        $input.next('.error-message').hide();
                    }
                });

                // Clear error on SKU input
                $('#sku').on('input', function() {
                    if ($(this).val().trim()) {
                        $(this).removeClass('is-invalid');
                        $('#error-sku').hide();
                    }
                });

                // Clear error on Max Per Order input
                $('#max_per_order').on('input', function() {
                    if ($(this).val() && $(this).val() >= 1) {
                        $(this).removeClass('is-invalid');
                        $('#error-max_per_order').hide();
                    }
                });

                // Clear error on select2 change
                $('#brand_id').on('change', function() {
                    if ($(this).val()) {
                        $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                        $('#error-brand_id').hide();
                    }
                });

                $('#vendor_id').on('change', function() {
                    if ($(this).val()) {
                        $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                        $('#error-vendor_id').hide();
                    }
                });

                $('#department_id').on('change', function() {
                    if ($(this).val()) {
                        $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                        $('#error-department_id').hide();
                    }
                });

                $('#category_id').on('change', function() {
                    if ($(this).val()) {
                        $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                        $('#error-category_id').hide();
                    }
                });

                // Clear error on main image upload
                $('#main_image').on('change', function() {
                    if (this.files && this.files.length > 0) {
                        $(this).removeClass('is-invalid');
                        // Remove is-invalid class from image preview container
                        $(this).closest('.image-upload-wrapper').find('.image-preview-container')
                            .removeClass('is-invalid');
                        // Hide error message
                        $(this).closest('.image-upload-wrapper').find('.error-message').hide();
                    }
                });

                // Clear error on price input (for both simple and variant products)
                $(document).on('input', '.price-input', function() {
                    const $input = $(this);
                    if ($input.val() && parseFloat($input.val()) >= 0) {
                        $input.removeClass('is-invalid');
                        $input.next('.error-message').hide();
                    }
                });

                // Clear error on SKU input (for variants)
                $(document).on('input', '.sku-input', function() {
                    const $input = $(this);
                    if ($input.val() && $input.val().trim()) {
                        $input.removeClass('is-invalid');
                        $input.next('.error-message').hide();
                    }
                });

                // Clear error on quantity input and update total stock
                $(document).on('input', '.quantity-input', function() {
                    const $input = $(this);
                    if ($input.val() && parseInt($input.val()) >= 0) {
                        $input.removeClass('is-invalid');
                    }

                    // Update total stock for the variant
                    updateVariantTotalStock($input);
                });

                // Clear error on region select
                $(document).on('change', '.region-select', function() {
                    const $select = $(this);
                    if ($select.val()) {
                        $select.next('.select2').find('.select2-selection').removeClass('is-invalid');
                    }
                });

                // Clear error on variant key select
                $(document).on('change', '.variant-key-select', function() {
                    const $select = $(this);
                    if ($select.val()) {
                        $select.next('.select2').find('.select2-selection').removeClass('is-invalid');
                    }
                });

                // Submit button click handler
                $('#submitBtn').on('click', function(e) {
                    e.preventDefault();
                    console.log('🔘 Submit button clicked');
                    $('#productForm').submit();
                });

                // Form submission
                $('#productForm').on('submit', function(e) {
                    e.preventDefault();
                    console.log('📝 Form submitted');

                    // Sync CKEditor data to textareas before validation/submission
                    if (typeof CKEDITOR !== 'undefined') {
                        for (let instance in CKEDITOR.instances) {
                            CKEDITOR.instances[instance].updateElement();
                            console.log('✅ CKEditor data synced for:', instance);
                        }
                    }

                    // Validate all steps
                    let allValid = true;
                    for (let i = 1; i <= config.totalSteps; i++) {
                        if (!validateStep(i)) {
                            allValid = false;
                            showStep(i);
                            break;
                        }
                    }

                    if (allValid) {
                        console.log('✅ Validation passed, showing loader');

                        // Disable submit button
                        const $submitBtn = $('#submitBtn');
                        const originalBtnHtml = $submitBtn.html();
                        $submitBtn.prop('disabled', true);
                        $submitBtn.html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><?php echo e(__('common.processing') ?? 'Processing...'); ?>'
                        );

                        // Update loading text dynamically
                        const loadingText = <?php echo json_encode(isset($product) ? trans('loading.updating') : trans('loading.creating'), 15, 512) ?>;
                        const loadingSubtext = '<?php echo e(trans('loading.please_wait')); ?>';
                        const overlay = document.getElementById('loadingOverlay');
                        if (overlay) {
                            overlay.querySelector('.loading-text').textContent = loadingText;
                            overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
                        }

                        // Show loading overlay
                        LoadingOverlay.show();

                        // Start progress bar animation
                        LoadingOverlay.animateProgressBar(30, 300).then(() => {
                                // Prepare form data
                                const formData = new FormData(document.getElementById(
                                    'productForm'));

                                // Send AJAX request
                                return fetch($('#productForm').attr('action'), {
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
                                    const successMessage = <?php echo json_encode(isset($product) ? trans('loading.updated_successfully') : trans('loading.created_successfully'), 15, 512) ?>;
                                    LoadingOverlay.showSuccess(
                                        successMessage,
                                        '<?php echo e(trans('loading.redirecting')); ?>'
                                    );

                                    // Redirect after 1.5 seconds
                                    setTimeout(() => {
                                        window.location.href = data.redirect ||
                                            '<?php echo e(route('admin.products.index')); ?>';
                                    }, 1500);
                                });
                            })
                            .catch(error => {
                                // Hide loading overlay
                                LoadingOverlay.hide();

                                // Remove previous validation errors
                                $('.is-invalid').removeClass('is-invalid');
                                $('.invalid-feedback').hide().text('');
                                $('.error-message').hide().text('');

                                // Handle validation errors
                                if (error.errors) {
                                    const errorMessages = [];

                                    Object.keys(error.errors).forEach(key => {
                                        const errorMessage = error.errors[key][0];
                                        errorMessages.push(errorMessage);

                                        // Show inline error
                                        const $errorElement = $(
                                            `#error-${key.replace(/\./g, '-')}`);
                                        if ($errorElement.length) {
                                            $errorElement.text(errorMessage).css('display', 'block');
                                        }

                                        // Add invalid class to input
                                        const $input = $(`[name="${key}"]`);
                                        $input.addClass('is-invalid');

                                        // Also show toastr notification
                                        if (typeof toastr !== 'undefined') {
                                            toastr.error(errorMessage);
                                        }
                                    });

                                    showValidationErrors(errorMessages);
                                } else if (error.message) {
                                    if (typeof toastr !== 'undefined') {
                                        toastr.error(error.message);
                                    }
                                    showValidationErrors([error.message]);
                                }

                                // Re-enable submit button
                                $submitBtn.prop('disabled', false);
                                $submitBtn.html(originalBtnHtml);
                            });
                    }
                });

                // Prevent Enter key from submitting form
                $('#productForm').on('keydown', function(e) {
                    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        return false;
                    }
                });
            });

            // ============================================
            // Discount Fields Toggle Function
            // ============================================
            window.toggleDiscountFields = function(variantIndex) {
                const checkbox = document.getElementById('discount_' + variantIndex);
                const discountFields = document.getElementById('discount_fields_' + variantIndex);

                // Check if elements exist (they might not exist for new variants)
                if (!checkbox || !discountFields) {
                    console.warn('⚠️ Discount elements not found for variant:', variantIndex);
                    return;
                }

                // Get discount fields
                const priceBeforeInput = document.querySelector(
                    `input[name="variants[${variantIndex}][price_before_discount]"]`);
                const endDateInput = document.querySelector(
                    `input[name="variants[${variantIndex}][discount_end_date]"]`);

                if (checkbox.checked) {
                    // Show discount fields
                    discountFields.style.display = 'block';
                    console.log('✅ Variant discount enabled - fields visible and editable');
                } else {
                    // Hide discount fields and clear their values
                    discountFields.style.display = 'none';

                    // Clear the values when discount is disabled
                    if (priceBeforeInput) priceBeforeInput.value = '0';
                    if (endDateInput) endDateInput.value = '';

                    console.log('❌ Variant discount disabled - fields hidden and cleared');
                }

                console.log('🔄 Toggled discount fields for variant', variantIndex, 'enabled:', checkbox.checked);
            };


        })(jQuery);
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/product/edit.blade.php ENDPATH**/ ?>