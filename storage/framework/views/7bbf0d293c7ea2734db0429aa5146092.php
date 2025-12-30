
<?php $__env->startSection('title'); ?>
    <?php echo e(__('catalogmanagement::tax.taxes_management')); ?> | Bnaia
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::tax.taxes_management')],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::tax.taxes_management')],
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
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500"><?php echo e(__('catalogmanagement::tax.taxes_management')); ?></h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('taxes.create')): ?>
                            <a href="<?php echo e(route('admin.taxes.create')); ?>" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> <?php echo e(__('catalogmanagement::tax.add_tax')); ?>

                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="table-responsive">
                        <table id="taxesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><span class="userDatatable-title"><?php echo e(__('catalogmanagement::tax.name')); ?> (<?php echo e(strtoupper($language->code)); ?>)</span></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <th><span class="userDatatable-title"><?php echo e(__('catalogmanagement::tax.percentage')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('catalogmanagement::tax.status')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('common.actions')); ?></span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($component)) { $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['modalId' => 'modal-delete-tax','title' => __('catalogmanagement::tax.confirm_delete'),'message' => __('catalogmanagement::tax.delete_confirmation'),'itemNameId' => 'delete-tax-name','confirmBtnId' => 'confirmDeleteTaxBtn','deleteRoute' => route('admin.taxes.index'),'cancelText' => __('common.cancel'),'deleteText' => __('catalogmanagement::tax.delete_tax')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-tax','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::tax.confirm_delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::tax.delete_confirmation')),'itemNameId' => 'delete-tax-name','confirmBtnId' => 'confirmDeleteTaxBtn','deleteRoute' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.taxes.index')),'cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('catalogmanagement::tax.delete_tax'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $attributes = $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $component = $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let languages = <?php echo json_encode($languages, 15, 512) ?>;
    
    let columns = [
        {
            data: 'index',
            name: 'index',
            orderable: false,
            render: function(data) {
                return '<div class="userDatatable-content">' + data + '</div>';
            }
        }
    ];

    // Add name columns for each language
    languages.forEach(function(language) {
        columns.push({
            data: 'names',
            name: 'name_' + language.id,
            orderable: false,
            render: function(data, type, row) {
                let name = data[language.id] ? data[language.id].value : '-';
                let rtl = data[language.id] ? data[language.id].rtl : false;
                return '<div class="userDatatable-content" ' + (rtl ? 'dir="rtl"' : '') + '>' + name + '</div>';
            }
        });
    });

    columns.push(
        {
            data: 'percentage',
            name: 'percentage',
            render: function(data) {
                return '<div class="userDatatable-content"><span class="badge badge-info badge-lg badge-round">' + data + '%</span></div>';
            }
        },
        {
            data: 'is_active',
            name: 'is_active',
            orderable: false,
            render: function(data, type, row) {
                let checked = data ? 'checked' : '';
                return `
                    <div class="userDatatable-content d-flex justify-content-center">
                        <div class="form-check form-switch form-switch-primary form-switch-md">
                            <input type="checkbox" class="form-check-input toggle-status" 
                                data-id="${row.id}" 
                                ${checked}
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->denies('taxes.edit')): ?> disabled <?php endif; ?>>
                        </div>
                    </div>
                `;
            }
        },
        {
            data: null,
            name: 'actions',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                let viewUrl = "<?php echo e(route('admin.taxes.show', ':id')); ?>".replace(':id', row.id);
                let editUrl = "<?php echo e(route('admin.taxes.edit', ':id')); ?>".replace(':id', row.id);
                return `
                    <div class="orderDatatable_actions d-inline-flex gap-1">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('taxes.show')): ?>
                        <a href="${viewUrl}" class="view btn btn-primary table_action_father" title="<?php echo e(trans('common.view')); ?>">
                            <i class="uil uil-eye table_action_icon"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('taxes.edit')): ?>
                        <a href="${editUrl}" class="edit btn btn-warning table_action_father" title="<?php echo e(trans('common.edit')); ?>">
                            <i class="uil uil-edit table_action_icon"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('taxes.delete')): ?>
                        <a href="javascript:void(0);" class="remove delete-tax btn btn-danger table_action_father" data-bs-toggle="modal" data-bs-target="#modal-delete-tax" data-item-id="${row.id}" data-item-name="${row.display_name}" title="<?php echo e(trans('common.delete')); ?>">
                            <i class="uil uil-trash-alt table_action_icon"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                `;
            }
        }
    );

    $('#taxesDataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo e(route("admin.taxes.datatable")); ?>',
        columns: columns,
        order: [[0, 'desc']],
        language: {
            zeroRecords: "<?php echo e(__('catalogmanagement::tax.no_taxes_found')); ?>",
            emptyTable: "<?php echo e(__('catalogmanagement::tax.no_taxes_found')); ?>"
        }
    });

    // Handle toggle status
    $(document).on('change', '.toggle-status', function() {
        let checkbox = $(this);
        let taxId = checkbox.data('id');
        let isActive = checkbox.is(':checked') ? 1 : 0;

        $.ajax({
            url: "<?php echo e(route('admin.taxes.toggle-status', ':id')); ?>".replace(':id', taxId),
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                is_active: isActive
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                    checkbox.prop('checked', !isActive);
                }
            },
            error: function(xhr) {
                toastr.error('<?php echo e(__("common.error_occurred")); ?>');
                checkbox.prop('checked', !isActive);
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/tax/index.blade.php ENDPATH**/ ?>