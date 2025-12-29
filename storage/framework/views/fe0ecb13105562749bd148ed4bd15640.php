

<?php $__env->startSection('title', __('systemsetting::push-notification.send_notification')); ?>

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
                    ['title' => __('systemsetting::push-notification.all_notifications'), 'url' => route('admin.system-settings.push-notifications.index')],
                    ['title' => __('systemsetting::push-notification.send_notification')],
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
                    ['title' => __('systemsetting::push-notification.all_notifications'), 'url' => route('admin.system-settings.push-notifications.index')],
                    ['title' => __('systemsetting::push-notification.send_notification')],
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
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            <i class="uil uil-bell me-2"></i>
                            <?php echo e(__('systemsetting::push-notification.send_notification')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="alertContainer" class="mb-3"></div>

                        <form id="notificationForm" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            
                            <div class="mb-25">
                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                    <?php echo e(__('systemsetting::push-notification.notification_type')); ?>

                                    <span class="text-danger">*</span>
                                </label>
                                <div class="btn-group w-100 flex-wrap" role="group">
                                    <input type="radio" class="btn-check" name="type" id="type_all" value="all" checked>
                                    <label class="btn btn-outline-primary" for="type_all">
                                        <i class="uil uil-users-alt me-1"></i><?php echo e(__('systemsetting::push-notification.type_all')); ?>

                                    </label>

                                    <input type="radio" class="btn-check" name="type" id="type_specific" value="specific">
                                    <label class="btn btn-outline-primary" for="type_specific">
                                        <i class="uil uil-user-check me-1"></i><?php echo e(__('systemsetting::push-notification.type_specific')); ?>

                                    </label>

                                    <input type="radio" class="btn-check" name="type" id="type_all_vendors" value="all_vendors">
                                    <label class="btn btn-outline-success" for="type_all_vendors">
                                        <i class="uil uil-store me-1"></i><?php echo e(__('systemsetting::push-notification.type_all_vendors')); ?>

                                    </label>

                                    <input type="radio" class="btn-check" name="type" id="type_specific_vendors" value="specific_vendors">
                                    <label class="btn btn-outline-success" for="type_specific_vendors">
                                        <i class="uil uil-shop me-1"></i><?php echo e(__('systemsetting::push-notification.type_specific_vendors')); ?>

                                    </label>
                                </div>
                            </div>

                            
                            <div class="mb-25" id="customerSection" style="display: none;">
                                <?php if (isset($component)) { $__componentOriginal8b85cde560f35167074cbd8632d75a5d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b85cde560f35167074cbd8632d75a5d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-tags','data' => ['name' => 'customer_ids[]','label' => __('systemsetting::push-notification.select_customers'),'options' => $customers->toArray(),'selected' => [],'placeholder' => __('systemsetting::push-notification.search_customers'),'required' => false,'multiple' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-tags'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'customer_ids[]','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.select_customers')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($customers->toArray()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([]),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.search_customers')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'multiple' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8b85cde560f35167074cbd8632d75a5d)): ?>
<?php $attributes = $__attributesOriginal8b85cde560f35167074cbd8632d75a5d; ?>
<?php unset($__attributesOriginal8b85cde560f35167074cbd8632d75a5d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8b85cde560f35167074cbd8632d75a5d)): ?>
<?php $component = $__componentOriginal8b85cde560f35167074cbd8632d75a5d; ?>
<?php unset($__componentOriginal8b85cde560f35167074cbd8632d75a5d); ?>
<?php endif; ?>
                            </div>

                            
                            <div class="mb-25" id="vendorSection" style="display: none;">
                                <?php if (isset($component)) { $__componentOriginal8b85cde560f35167074cbd8632d75a5d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b85cde560f35167074cbd8632d75a5d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-tags','data' => ['name' => 'vendor_ids[]','label' => __('systemsetting::push-notification.select_vendors'),'options' => $vendors->toArray(),'selected' => [],'placeholder' => __('systemsetting::push-notification.search_vendors'),'required' => false,'multiple' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-tags'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'vendor_ids[]','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.select_vendors')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendors->toArray()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([]),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.search_vendors')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'multiple' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8b85cde560f35167074cbd8632d75a5d)): ?>
<?php $attributes = $__attributesOriginal8b85cde560f35167074cbd8632d75a5d; ?>
<?php unset($__attributesOriginal8b85cde560f35167074cbd8632d75a5d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8b85cde560f35167074cbd8632d75a5d)): ?>
<?php $component = $__componentOriginal8b85cde560f35167074cbd8632d75a5d; ?>
<?php unset($__componentOriginal8b85cde560f35167074cbd8632d75a5d); ?>
<?php endif; ?>
                            </div>

                            
                            <?php if (isset($component)) { $__componentOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal36fb2a0d5a2d77000f95bef7ddc5a9bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'title','label' => 'Title','labelAr' => 'العنوان','placeholder' => 'Title','placeholderAr' => 'العنوان','type' => 'text','required' => true,'languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'title','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Title'),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('العنوان'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Title'),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('العنوان'),'type' => 'text','required' => true,'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.multilingual-input','data' => ['name' => 'description','label' => 'Description','labelAr' => 'الوصف','placeholder' => 'Description','placeholderAr' => 'الوصف','type' => 'textarea','rows' => 4,'required' => true,'languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('multilingual-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'description','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Description'),'labelAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('الوصف'),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Description'),'placeholderAr' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('الوصف'),'type' => 'textarea','rows' => 4,'required' => true,'languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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

                            
                            <div class="mb-25">
                                <?php if (isset($component)) { $__componentOriginaldbebdfa49a0907927fe266159631a348 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbebdfa49a0907927fe266159631a348 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image-upload','data' => ['id' => 'notification_image','name' => 'image','label' => __('systemsetting::push-notification.image'),'required' => false,'placeholder' => __('systemsetting::push-notification.upload_image'),'recommendedSize' => __('systemsetting::push-notification.image_size'),'aspectRatio' => 'wide']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image-upload'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'notification_image','name' => 'image','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.image')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.upload_image')),'recommendedSize' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.image_size')),'aspectRatio' => 'wide']); ?>
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

                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('admin.system-settings.push-notifications.index')); ?>" class="btn btn-light btn-default btn-squared">
                                    <i class="uil uil-arrow-left me-1"></i>
                                    <?php echo e(__('common.cancel')); ?>

                                </a>
                                <button type="submit" class="btn btn-primary btn-squared" id="submitBtn">
                                    <i class="uil uil-message me-1"></i>
                                    <?php echo e(__('systemsetting::push-notification.send')); ?>

                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="position: sticky; top: 20px;">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            <i class="uil uil-mobile-android me-2"></i>
                            <?php echo e(__('systemsetting::push-notification.preview')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="notification-preview p-3 rounded">
                            <div class="d-flex align-items-start gap-3">
                                <div class="notification-icon bg-primary text-white rounded p-2">
                                    <i class="uil uil-bell" style="font-size: 24px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-600" id="previewTitle"><?php echo e(__('systemsetting::push-notification.notification_title')); ?></h6>
                                    <p class="mb-0 text-muted small" id="previewDescription"><?php echo e(__('systemsetting::push-notification.notification_description')); ?></p>
                                </div>
                            </div>
                            <div class="mt-3" id="previewImageContainer" style="display: none;">
                                <img id="previewImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 150px; width: 100%;">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="uil uil-info-circle me-1"></i>
                            <?php echo e(__('systemsetting::push-notification.preview_note')); ?>

                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            // Toggle customer/vendor selection based on type
            $('input[name="type"]').on('change', function() {
                const type = $(this).val();
                if (type === 'specific') {
                    $('#customerSection').slideDown();
                    $('#vendorSection').slideUp();
                } else if (type === 'specific_vendors') {
                    $('#customerSection').slideUp();
                    $('#vendorSection').slideDown();
                } else {
                    $('#customerSection').slideUp();
                    $('#vendorSection').slideUp();
                }
            });

            // Live preview - title
            $(document).on('input keyup', 'input[name*="[title]"]', function() {
                var lang = $(this).attr('data-lang') || $(this).closest('.form-group').attr('data-lang');
                if (lang === 'en') {
                    $('#previewTitle').text($(this).val() || '<?php echo e(__('systemsetting::push-notification.notification_title')); ?>');
                }
            });

            // Live preview - description (for CKEditor)
            // Wait for CKEditor instances to be ready
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.on('instanceReady', function(evt) {
                    var editor = evt.editor;
                    var element = editor.element.$;
                    var lang = $(element).attr('data-lang') || $(element).closest('.form-group').attr('data-lang');
                    
                    if (lang === 'en' && editor.name.includes('description')) {
                        editor.on('change', function() {
                            var text = editor.getData().replace(/<[^>]*>/g, '').trim();
                            $('#previewDescription').text(text || '<?php echo e(__('systemsetting::push-notification.notification_description')); ?>');
                        });
                        editor.on('key', function() {
                            setTimeout(function() {
                                var text = editor.getData().replace(/<[^>]*>/g, '').trim();
                                $('#previewDescription').text(text || '<?php echo e(__('systemsetting::push-notification.notification_description')); ?>');
                            }, 100);
                        });
                    }
                });
            }

            // Fallback for regular textarea (if CKEditor not used)
            $(document).on('input keyup', 'textarea[name*="[description]"]', function() {
                var lang = $(this).attr('data-lang') || $(this).closest('.form-group').attr('data-lang');
                if (lang === 'en') {
                    $('#previewDescription').text($(this).val() || '<?php echo e(__('systemsetting::push-notification.notification_description')); ?>');
                }
            });

            // Image preview
            $(document).on('change', '#notification_image, input[name="image"]', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#previewImage').attr('src', event.target.result);
                        $('#previewImageContainer').show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Form submission
            $('#notificationForm').on('submit', function(e) {
                e.preventDefault();

                // Sync CKEditor content to textareas before submit
                if (typeof CKEDITOR !== 'undefined') {
                    for (var instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                }

                const $btn = $('#submitBtn');
                $btn.prop('disabled', true);
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span><?php echo e(__('common.processing')); ?>');

                const formData = new FormData(this);

                $.ajax({
                    url: '<?php echo e(route('admin.system-settings.push-notifications.store')); ?>',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href = '<?php echo e(route('admin.system-settings.push-notifications.index')); ?>';
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                            $btn.prop('disabled', false);
                            $btn.html('<i class="uil uil-message me-1"></i><?php echo e(__('systemsetting::push-notification.send')); ?>');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '<?php echo e(__('common.error_occurred')); ?>';
                        if (xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul class="mb-0">';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            $('#alertContainer').html('<div class="alert alert-danger">' + errorHtml + '</div>');
                        } else {
                            toastr.error(xhr.responseJSON?.message || errorMessage);
                        }
                        $btn.prop('disabled', false);
                        $btn.html('<i class="uil uil-message me-1"></i><?php echo e(__('systemsetting::push-notification.send')); ?>');
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/SystemSetting\resources/views/push-notifications/create.blade.php ENDPATH**/ ?>