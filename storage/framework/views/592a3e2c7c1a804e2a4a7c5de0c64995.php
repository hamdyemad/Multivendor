

<?php $__env->startSection('title', __('systemsetting::push-notification.notification_details')); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('systemsetting::push-notification.all_notifications'), 'url' => route('admin.system-settings.push-notifications.index')],
                    ['title' => __('systemsetting::push-notification.notification_details')]
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('systemsetting::push-notification.all_notifications'), 'url' => route('admin.system-settings.push-notifications.index')],
                    ['title' => __('systemsetting::push-notification.notification_details')]
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
                        <h5 class="mb-0 fw-500"><?php echo e(__('systemsetting::push-notification.notification_details')); ?></h5>
                        <div class="d-flex gap-10">
                            <a href="<?php echo e(route('admin.system-settings.push-notifications.index')); ?>" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i><?php echo e(__('common.back_to_list')); ?>

                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3><i class="uil uil-info-circle me-1"></i><?php echo e(__('common.basic_information')); ?></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php if (isset($component)) { $__componentOriginale5a3093cad4b0bccb881a74044179ded = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5a3093cad4b0bccb881a74044179ded = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('systemsetting::push-notification.title'),'model' => $notification,'fieldName' => 'title','languages' => $languages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.title')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notification),'fieldName' => 'title','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages)]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.translation-display','data' => ['label' => __('systemsetting::push-notification.description'),'model' => $notification,'fieldName' => 'description','languages' => $languages,'type' => 'html']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('translation-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('systemsetting::push-notification.description')),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notification),'fieldName' => 'description','languages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($languages),'type' => 'html']); ?>
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
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('common.type')); ?></label>
                                                    <p class="fs-15 color-dark">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($notification->type):
                                                            case ('all'): ?>
                                                                <span class="badge badge-info badge-round badge-lg"><?php echo e(__('systemsetting::push-notification.type_all')); ?></span>
                                                                <?php break; ?>
                                                            <?php case ('specific'): ?>
                                                                <span class="badge badge-primary badge-round badge-lg"><?php echo e(__('systemsetting::push-notification.type_specific')); ?></span>
                                                                <?php break; ?>
                                                            <?php case ('all_vendors'): ?>
                                                                <span class="badge badge-success badge-round badge-lg"><?php echo e(__('systemsetting::push-notification.type_all_vendors')); ?></span>
                                                                <?php break; ?>
                                                            <?php case ('specific_vendors'): ?>
                                                                <span class="badge badge-warning badge-round badge-lg"><?php echo e(__('systemsetting::push-notification.type_specific_vendors')); ?></span>
                                                                <?php break; ?>
                                                        <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('systemsetting::push-notification.created_by')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($notification->createdBy?->name ?? '-'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customersCount > 0): ?>
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3><i class="uil uil-users-alt me-1"></i><?php echo e(__('systemsetting::push-notification.customers')); ?> (<?php echo e($customersCount); ?>)</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table id="customersDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><?php echo e(__('common.name')); ?></th>
                                                        <th><?php echo e(__('common.email')); ?></th>
                                                        <th><?php echo e(__('common.phone')); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendorsCount > 0): ?>
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3><i class="uil uil-store me-1"></i><?php echo e(__('systemsetting::push-notification.vendors')); ?> (<?php echo e($vendorsCount); ?>)</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table id="vendorsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><?php echo e(__('common.name')); ?></th>
                                                        <th><?php echo e(__('common.email')); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3><i class="uil uil-eye me-1"></i><?php echo e(__('systemsetting::push-notification.views')); ?> (<?php echo e($viewsCount); ?>)</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table id="viewsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><?php echo e(__('common.name')); ?></th>
                                                        <th><?php echo e(__('common.email')); ?></th>
                                                        <th><?php echo e(__('systemsetting::push-notification.viewed_at')); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3><i class="uil uil-clock me-1"></i><?php echo e(__('common.timestamps')); ?></h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('common.created_at')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($notification->created_at); ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('common.updated_at')); ?></label>
                                                    <p class="fs-15 color-dark"><?php echo e($notification->updated_at); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 order-1 order-md-2">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3><i class="uil uil-image me-1"></i><?php echo e(__('systemsetting::push-notification.image')); ?></h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->image): ?>
                                            <div class="image-wrapper">
                                                <img src="<?php echo e(formatImage($notification->image)); ?>" alt="<?php echo e(__('systemsetting::push-notification.image')); ?>" class="img-fluid rounded">
                                            </div>
                                        <?php else: ?>
                                            <p class="fs-15 color-light fst-italic"><?php echo e(__('common.no_image') ?? 'No image uploaded'); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
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

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            var dtLanguage = {
                emptyTable: "<?php echo e(__('datatable.empty_table')); ?>",
                processing: "<?php echo e(__('datatable.processing')); ?>",
                info: "<?php echo e(__('datatable.info')); ?>",
                infoEmpty: "<?php echo e(__('datatable.info_empty')); ?>",
                infoFiltered: "<?php echo e(__('datatable.info_filtered')); ?>",
                lengthMenu: "<?php echo e(__('datatable.length_menu')); ?>",
                zeroRecords: "<?php echo e(__('datatable.zero_records')); ?>",
                paginate: {
                    first: "<?php echo e(__('datatable.first')); ?>",
                    last: "<?php echo e(__('datatable.last')); ?>",
                    next: "<?php echo e(__('datatable.next')); ?>",
                    previous: "<?php echo e(__('datatable.previous')); ?>"
                }
            };

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customersCount > 0): ?>
            $('#customersDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '<?php echo e(route('admin.system-settings.push-notifications.customers-datatable', ['id' => $notification->id])); ?>',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'full_name', name: 'full_name', orderable: false, searchable: false },
                    { data: 'email', name: 'email', orderable: false, searchable: false },
                    { data: 'phone', name: 'phone', orderable: false, searchable: false }
                ],
                language: dtLanguage,
                dom: 'lrtip',
                pageLength: 10
            });
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vendorsCount > 0): ?>
            $('#vendorsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '<?php echo e(route('admin.system-settings.push-notifications.vendors-datatable', ['id' => $notification->id])); ?>',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name', orderable: false, searchable: false },
                    { data: 'email', name: 'email', orderable: false, searchable: false }
                ],
                language: dtLanguage,
                dom: 'lrtip',
                pageLength: 10
            });
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            $('#viewsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '<?php echo e(route('admin.system-settings.push-notifications.views-datatable', ['id' => $notification->id])); ?>',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name', orderable: false, searchable: false },
                    { data: 'email', name: 'email', orderable: false, searchable: false },
                    { data: 'viewed_at', name: 'viewed_at', orderable: false, searchable: false }
                ],
                language: dtLanguage,
                dom: 'lrtip',
                pageLength: 10
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/SystemSetting\resources/views/push-notifications/show.blade.php ENDPATH**/ ?>