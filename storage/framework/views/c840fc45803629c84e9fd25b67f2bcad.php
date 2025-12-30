
<?php $__env->startSection('title', trans('categorymanagment::category.categories_management')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Drag and Drop Styles */
    #categoriesDataTable tbody tr {
        cursor: default;
    }
    #categoriesDataTable tbody tr.ui-sortable-helper {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        cursor: grabbing;
    }
    #categoriesDataTable tbody tr.ui-sortable-placeholder {
        border: 2px dashed #2196f3 !important;
        visibility: visible !important;
        height: 50px;
    }
    .drag-handle {
        cursor: grab;
        color: #6c757d;
        padding: 10px 15px;
        font-size: 18px;
        display: block;
        width: 100%;
        height: 100%;
    }
    .drag-handle:hover {
        color: #495057;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .reorder-info {
        border: 1px solid #ffc107;
        border-radius: 5px;
        padding: 10px 15px;
        margin-bottom: 15px;
        display: none;
    }
    .reorder-info.show {
        display: block;
    }
</style>
<!-- jQuery UI for Sortable -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
                    ['title' => trans('categorymanagment::category.categories_management')],
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
                    ['title' => trans('categorymanagment::category.categories_management')],
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
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-600 text-primary">
                            <i class="uil uil-folder-open me-2"></i>
                            <?php echo e(trans('categorymanagment::category.categories_management')); ?>

                        </h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categories.create')): ?>
                            <a href="<?php echo e(route('admin.category-management.categories.create')); ?>"
                                class="btn btn-primary btn-squared shadow-sm px-4">
                                <i class="uil uil-plus"></i> <?php echo e(trans('categorymanagment::category.add_category')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="alert alert-info glowing-alert" role="alert">
                        <?php echo e(__('common.live_search_info')); ?>

                    </div>

                    
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> <?php echo e(__('common.search')); ?>

                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="<?php echo e(__('categorymanagment::category.search_by_name')); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <?php if (isset($component)) { $__componentOriginal8b85cde560f35167074cbd8632d75a5d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b85cde560f35167074cbd8632d75a5d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-tags','data' => ['name' => 'department_filter','label' => __('categorymanagment::category.department'),'options' => $departments,'selected' => [],'placeholder' => __('categorymanagment::category.select_departments'),'multiple' => false,'id' => 'department_filter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-tags'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'department_filter','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('categorymanagment::category.department')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($departments),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([]),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('categorymanagment::category.select_departments')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'id' => 'department_filter']); ?>
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

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(__('categorymanagment::category.activation')); ?>

                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="active">
                                                <option value=""><?php echo e(__('categorymanagment::category.all')); ?></option>
                                                <option value="1"><?php echo e(__('categorymanagment::category.active')); ?>

                                                </option>
                                                <option value="0"><?php echo e(__('categorymanagment::category.inactive')); ?>

                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="view_status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-eye me-1"></i>
                                                <?php echo e(__('categorymanagment::category.view_status')); ?>

                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="view_status">
                                                <option value=""><?php echo e(__('categorymanagment::category.all')); ?></option>
                                                <option value="1"><?php echo e(__('common.visible')); ?></option>
                                                <option value="0"><?php echo e(__('common.hidden')); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(__('common.created_date_from')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(__('common.created_date_to')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sort_column" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-sort me-1"></i>
                                                <?php echo e(__('common.sort_by') ?? 'Sort By'); ?>

                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="sort_column">
                                                <option value="sort_number" selected><?php echo e(__('common.sort_number') ?? 'Sort Number'); ?></option>
                                                <option value="created_at"><?php echo e(__('common.created_at') ?? 'Created At'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sort_direction" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-sort-amount-down me-1"></i>
                                                <?php echo e(__('common.sort_direction') ?? 'Sort Direction'); ?>

                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="sort_direction">
                                                <option value="asc" selected><?php echo e(__('common.ascending') ?? 'Ascending'); ?></option>
                                                <option value="desc"><?php echo e(__('common.descending') ?? 'Descending'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex align-items-center">
                                        <div class="form-group">
                                            <button type="button" id="resetFilters"
                                                class="btn btn-warning btn-default btn-squared"
                                                title="<?php echo e(__('common.reset')); ?>">
                                                <i class="uil uil-redo me-1"></i>
                                                <?php echo e(__('common.reset_filters')); ?>

                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0"><?php echo e(__('common.show')); ?></label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0"><?php echo e(__('common.entries')); ?></label>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <div class="reorder-info" id="reorderInfo">
                            <i class="uil uil-info-circle me-2"></i>
                            <?php echo e(__('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.'); ?>

                        </div>
                        <table id="categoriesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th style="width: 40px;"><span class="userDatatable-title"><i class="uil uil-sort"></i></span></th>
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('categorymanagment::category.category_information')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('activity.department')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('categorymanagment::category.view_status')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('activity.activation')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('activity.created_at')); ?></span></th>
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

    
    <?php if (isset($component)) { $__componentOriginal4d4be0bcf29da35c820833c3b98d2b58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-with-loading','data' => ['modalId' => 'modal-delete-category','tableId' => 'categoriesDataTable','deleteButtonClass' => 'delete-category','title' => trans('main.confirm delete'),'message' => trans('main.are you sure you want to delete this'),'itemNameId' => 'delete-category-name','confirmBtnId' => 'confirmDeleteCategoryBtn','cancelText' => trans('main.cancel'),'deleteText' => trans('main.delete'),'loadingDeleting' => trans('main.deleting'),'loadingPleaseWait' => trans('main.please wait'),'loadingDeletedSuccessfully' => trans('main.deleted success'),'loadingRefreshing' => trans('main.refreshing'),'errorDeleting' => trans('main.error on delete')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-with-loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-category','tableId' => 'categoriesDataTable','deleteButtonClass' => 'delete-category','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.confirm delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.are you sure you want to delete this')),'itemNameId' => 'delete-category-name','confirmBtnId' => 'confirmDeleteCategoryBtn','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.delete')),'loadingDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleting')),'loadingPleaseWait' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.please wait')),'loadingDeletedSuccessfully' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleted success')),'loadingRefreshing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.refreshing')),'errorDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.error on delete'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58)): ?>
<?php $attributes = $__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58; ?>
<?php unset($__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4d4be0bcf29da35c820833c3b98d2b58)): ?>
<?php $component = $__componentOriginal4d4be0bcf29da35c820833c3b98d2b58; ?>
<?php unset($__componentOriginal4d4be0bcf29da35c820833c3b98d2b58); ?>
<?php endif; ?>
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
    <script>
        $(document).ready(function() {
            let dragDropEnabled = true; // Will be controlled by sort filters

            let table = $('#categoriesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.category-management.categories.datatable')); ?>',
                    data: function(d) {
                        d.search = $('#search').val();
                        d.department_id = $('#department_filter-single-display').find('input[name="department_filter"]').val() || '';
                        d.active = $('#active').val();
                        d.view_status = $('#view_status').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        d.per_page = $('#entriesSelect').val() || 10;
                        d.sort_column = $('#sort_column').val();
                        d.sort_direction = $('#sort_direction').val();
                    }
                },
                columns: [{
                        data: null,
                        name: 'drag',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<span class="drag-handle" title="<?php echo e(__('common.drag_to_reorder') ?? 'Drag to reorder'); ?>"><i class="uil uil-draggabledots"></i></span>`;
                        }
                    },
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'translations',
                        name: 'category_information',
                        orderable: false,
                        render: function(data, type, row) {
                            let html = '<div class="category-info-container">';
                            
                            // Category Names with language badges
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                if (data && data['<?php echo e($language->code); ?>'] && data['<?php echo e($language->code); ?>'].name && data['<?php echo e($language->code); ?>'].name !== '-') {
                                    let name = $('<div/>').text(data['<?php echo e($language->code); ?>'].name).html();
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($language->rtl): ?>
                                        html += `<div class="name-item mb-2">
                                            <span class="language-badge badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;"><?php echo e(strtoupper($language->code)); ?></span>
                                            <span class="item-name text-dark fw-semibold" dir="rtl" style="font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">${name}</span>
                                        </div>`;
                                    <?php else: ?>
                                        html += `<div class="name-item mb-2">
                                            <span class="language-badge badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;"><?php echo e(strtoupper($language->code)); ?></span>
                                            <span class="item-name text-dark fw-semibold">${name}</span>
                                        </div>`;
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                }
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            // Sort Number
                            html += '<div class="category-meta-info">';
                            html += `<div class="mb-1">
                                <small class="text-muted"><?php echo e(trans('categorymanagment::category.sort_number')); ?>:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${row.sort_number ?? 0}</span>
                            </div>`;
                            html += '</div>';

                            html += '</div>';
                            return html;
                        },
                        className: 'text-start'
                    },
                    {
                        data: 'department',
                        name: 'department',
                        orderable: false,
                        render: function(data) {
                            if (!data?.name) return '<span class="text-muted">—</span>';
                            return `<span class="badge badge-info badge-round badge-lg">${$('<div/>').text(data.name).html()}</span>`;
                        }
                    },
                    {
                        data: 'view_status',
                        name: 'view_status',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categories.edit')): ?>
                                const isChecked = data ? 'checked' : '';
                                const switchId = 'view-status-switch-' + row.id;

                                return `<div class="form-switch">
                                <input class="form-check-input view-status-switcher"
                                       type="checkbox"
                                       id="${switchId}"
                                       data-category-id="${row.id}"
                                       data-category-name="${$('<div>').text(row.name).html()}"
                                       ${isChecked}
                                       style="cursor: pointer;">
                                <label class="form-check-label" for="${switchId}"></label>
                            </div>`;
                            <?php else: ?>
                                return data ?
                                    `<span class="badge badge-success badge-round badge-lg"><i class="uil uil-eye"></i></span>` :
                                    `<span class="badge badge-secondary badge-round badge-lg"><i class="uil uil-eye-slash"></i></span>`;
                            <?php endif; ?>
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categories.edit')): ?>
                                const isChecked = data ? 'checked' : '';
                                const switchId = 'status-switch-' + row.id;

                                return `<div class="form-switch">
                                <input class="form-check-input status-switcher"
                                       type="checkbox"
                                       id="${switchId}"
                                       data-category-id="${row.id}"
                                       data-category-name="${$('<div>').text(row.name).html()}"
                                       ${isChecked}
                                       style="cursor: pointer;">
                                <label class="form-check-label" for="${switchId}"></label>
                            </div>`;
                            <?php else: ?>
                                return data ?
                                    `<span class="badge badge-success badge-round badge-lg"><i class="uil uil-check"></i> <?php echo e(trans('categorymanagment::category.active')); ?></span>` :
                                    `<span class="badge badge-danger badge-round badge-lg"><i class="uil uil-times"></i> <?php echo e(trans('categorymanagment::category.inactive')); ?></span>`;
                            <?php endif; ?>
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            let showUrl =
                                "<?php echo e(route('admin.category-management.categories.show', ':id')); ?>"
                                .replace(':id', data.id),
                                editUrl =
                                "<?php echo e(route('admin.category-management.categories.edit', ':id')); ?>"
                                .replace(':id', data.id),
                                deleteUrl =
                                "<?php echo e(route('admin.category-management.categories.destroy', ':id')); ?>"
                                .replace(':id', data.id);

                            return `
                            <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                <a href="${showUrl}" class="view btn btn-primary table_action_father" title="<?php echo e(trans('common.view')); ?>">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categories.edit')): ?>
                                <a href="${editUrl}" class="edit btn btn-warning table_action_father" title="<?php echo e(trans('common.edit')); ?>">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categories.delete')): ?>
                                <a href="javascript:void(0);" class="remove delete-category btn btn-danger table_action_father"
                                   data-bs-toggle="modal" data-bs-target="#modal-delete-category"
                                   data-item-id="${data.id}"
                                   data-item-name="${data.translations?.<?php echo e(app()->getLocale()); ?>?.name || 'Category'}"
                                   data-url="${deleteUrl}"
                                   title="<?php echo e(trans('common.delete')); ?>">
                                    <i class="uil uil-trash-alt table_action_icon"></i>
                                </a>
                                <?php endif; ?>
                            </div>`;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [],
                language: {
                    lengthMenu: "<?php echo e(__('common.show') ?? 'Show'); ?> _MENU_",
                    info: "<?php echo e(__('common.showing') ?? 'Showing'); ?> _START_ <?php echo e(__('common.to') ?? 'to'); ?> _END_ <?php echo e(__('common.of') ?? 'of'); ?> _TOTAL_ <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoEmpty: "<?php echo e(__('common.showing') ?? 'Showing'); ?> 0 <?php echo e(__('common.to') ?? 'to'); ?> 0 <?php echo e(__('common.of') ?? 'of'); ?> 0 <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoFiltered: "(<?php echo e(__('common.filtered_from') ?? 'filtered from'); ?> _MAX_ <?php echo e(__('common.total_entries') ?? 'total entries'); ?>)",
                    zeroRecords: "<?php echo e(__('activity.no_activities_found') ?? 'No activities found'); ?>",
                    emptyTable: "<?php echo e(__('activity.no_activities_found') ?? 'No activities found'); ?>",
                    loadingRecords: "<?php echo e(__('common.loading') ?? 'Loading'); ?>...",
                    processing: "<?php echo e(__('common.processing') ?? 'Processing'); ?>...",
                    search: "<?php echo e(__('common.search') ?? 'Search'); ?>:",
                    paginate: {
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() == 'en'): ?>
                            first: '<i class="uil uil-angle-double-left"></i>',
                            last: '<i class="uil uil-angle-double-right"></i>',
                            next: '<i class="uil uil-angle-right"></i>',
                            previous: '<i class="uil uil-angle-left"></i>'
                        <?php else: ?>
                            first: '<i class="uil uil-angle-double-right"></i>',
                            last: '<i class="uil uil-angle-double-left"></i>',
                            next: '<i class="uil uil-angle-left"></i>',
                            previous: '<i class="uil uil-angle-right"></i>'
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    },
                    aria: {
                        sortAscending: ": <?php echo e(__('common.sort_ascending') ?? 'activate to sort column ascending'); ?>",
                        sortDescending: ": <?php echo e(__('common.sort_descending') ?? 'activate to sort column descending'); ?>"
                    }
                },
                dom: '<"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="uil uil-file-download-alt"></i>',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '<?php echo e(trans('categorymanagment::category.categories_management')); ?>'
                }]
            });

            // Entries Selector
            $('#entriesSelect').html([10, 25, 50, 100].map(n => `<option value="${n}">${n}</option>`).join(''));
            $('#entriesSelect').val(10).on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Search Debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => table.ajax.reload(), 600);
            });

            // Filters
            $('#active, #view_status, #created_date_from, #created_date_to').on('change', () => table.ajax.reload());

            // Sort filter change handlers
            $('#sort_column, #sort_direction').on('change', function() {
                console.log('Sort changed:', $('#sort_column').val(), $('#sort_direction').val());
                table.ajax.reload();
                updateDragDropState();
            });

            // Function to update drag and drop state based on sort filters
            function updateDragDropState() {
                var sortColumn = $('#sort_column').val();
                var sortDirection = $('#sort_direction').val();
                dragDropEnabled = (sortColumn === 'sort_number' && sortDirection === 'asc');
                
                if (dragDropEnabled) {
                    $('#categoriesDataTable tbody').removeClass('drag-disabled');
                    $('.drag-handle').css('opacity', '1').css('cursor', 'grab');
                    $('#reorderInfo').removeClass('show').html('<i class="uil uil-info-circle me-2"></i><?php echo e(__('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.'); ?>');
                } else {
                    $('#categoriesDataTable tbody').addClass('drag-disabled');
                    $('.drag-handle').css('opacity', '0.3').css('cursor', 'not-allowed');
                    $('#reorderInfo').addClass('show').html('<i class="uil uil-exclamation-triangle me-2"></i><?php echo e(__('common.drag_drop_disabled_info') ?? 'Drag and drop is only available when sorting by Sort Number (Ascending).'); ?>');
                }
            }

            // Department filter change handler - listen for custom event
            $('#department_filter-wrapper').on('searchable-tags:change', function() {
                table.ajax.reload();
            });

            // Export
            $('#exportExcel').on('click', () => table.button('.buttons-excel').trigger());

            // Reset
            $('#resetFilters').on('click', function() {
                $('#search, #active, #view_status, #created_date_from, #created_date_to').val('');
                $('#sort_column').val('sort_number');
                $('#sort_direction').val('asc');
                
                // Reset department filter (single select)
                const deptDisplay = $('#department_filter-single-display');
                const deptDropdown = $('#department_filter-dropdown');
                deptDisplay.html('<span class="placeholder-text text-muted"><?php echo e(__("categorymanagment::category.select_departments")); ?></span>');
                deptDropdown.find('.tag-option').removeClass('selected').show();
                
                $('#entriesSelect').val(10);
                table.search('').page.len(10).ajax.reload();
                updateDragDropState();
            });

            // Status switcher handler
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const categoryId = switcher.data('category-id');
                const categoryName = switcher.data('category-name');
                const newStatus = switcher.is(':checked') ? 1 : 2; // 1=active, 2=inactive

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '<?php echo e(__('categorymanagment::category.change_status')); ?>',
                        subtext: '<?php echo e(__('common.please_wait') ?? 'Please wait'); ?>...'
                    });
                }

                // Make AJAX request
                $.ajax({
                    url: '<?php echo e(route('admin.category-management.categories.change-status', ':id')); ?>'
                        .replace(':id', categoryId),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '<?php echo e(__('common.success') ?? 'Success'); ?>',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }

                            // Reload table to reflect changes
                            table.ajax.reload(null, false);
                        } else {
                            // Hide loading overlay
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.hide();
                            }

                            // Revert switcher state
                            switcher.prop('checked', !switcher.is(':checked'));

                            // Show error message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '<?php echo e(__('common.error') ?? 'Error'); ?>',
                                    text: response.message
                                });
                            } else {
                                alert(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Hide loading overlay
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }

                        // Revert switcher state
                        switcher.prop('checked', !switcher.is(':checked'));

                        let errorMessage =
                            '<?php echo e(__('categorymanagment::category.error_changing_status')); ?>';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Show error message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '<?php echo e(__('common.error') ?? 'Error'); ?>',
                                text: errorMessage
                            });
                        } else {
                            alert(errorMessage);
                        }
                    },
                    complete: function() {
                        // Re-enable switcher
                        switcher.prop('disabled', false);
                    }
                });
            });

            // View status switcher handler
            $(document).on('change', '.view-status-switcher', function() {
                const switcher = $(this);
                const categoryId = switcher.data('category-id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Make AJAX request
                $.ajax({
                    url: '<?php echo e(route('admin.category-management.categories.change-view-status', ':id')); ?>'
                        .replace(':id', categoryId),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        view_status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '<?php echo e(__('common.success') ?? 'Success'); ?>',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    toast: true,
                                    position: 'top-end'
                                });
                            }
                            table.ajax.reload(null, false);
                        } else {
                            switcher.prop('checked', !switcher.is(':checked'));
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: '<?php echo e(__('common.error') ?? 'Error'); ?>',
                                    text: response.message
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        switcher.prop('checked', !switcher.is(':checked'));
                        let errorMessage = '<?php echo e(__('common.error') ?? 'Error'); ?>';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '<?php echo e(__('common.error') ?? 'Error'); ?>',
                                text: errorMessage
                            });
                        }
                    },
                    complete: function() {
                        switcher.prop('disabled', false);
                    }
                });
            });

            // RTL Support in DataTables
            if ($('html').attr('dir') === 'rtl') {
                $('.dataTables_wrapper').addClass('text-end');
            }

            // Initialize drag and drop sortable
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categories.edit')): ?>
            // Load jQuery UI if not already loaded
            if (typeof $.ui === 'undefined' || typeof $.ui.sortable === 'undefined') {
                $.getScript('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', function() {
                    console.log('jQuery UI loaded');
                    initSortable();
                    updateDragDropState();
                });
            } else {
                initSortable();
                updateDragDropState();
            }

            function initSortable() {
                var $tbody = $('#categoriesDataTable tbody');
                
                // Destroy existing sortable if any
                if ($tbody.hasClass('ui-sortable')) {
                    $tbody.sortable('destroy');
                }
                
                $tbody.sortable({
                    handle: '.drag-handle',
                    axis: 'y',
                    cursor: 'grabbing',
                    opacity: 0.8,
                    disabled: !dragDropEnabled,
                    helper: function(e, tr) {
                        var $originals = tr.children();
                        var $helper = tr.clone();
                        $helper.children().each(function(index) {
                            $(this).width($originals.eq(index).outerWidth());
                        });
                        return $helper;
                    },
                    placeholder: 'ui-sortable-placeholder',
                    start: function(event, ui) {
                        if (!dragDropEnabled) {
                            return false;
                        }
                        ui.placeholder.height(ui.item.outerHeight());
                        var colCount = ui.item.children('td').length;
                        ui.placeholder.html('<td colspan="' + colCount + '" style="background-color: #e3f2fd; border: 2px dashed #2196f3;">&nbsp;</td>');
                    },
                    stop: function(event, ui) {
                        // Optional: hide info after drop
                    },
                    update: function(event, ui) {
                        if (!dragDropEnabled) {
                            return false;
                        }
                        var items = [];
                        $('#categoriesDataTable tbody tr').each(function(index) {
                            var rowData = table.row(this).data();
                            if (rowData && rowData.id) {
                                items.push({
                                    id: rowData.id,
                                    sort_number: index + 1
                                });
                            }
                        });

                        console.log('Reorder items:', items);

                        if (items.length > 0) {
                            // Show loading
                            if (typeof LoadingOverlay !== 'undefined') {
                                LoadingOverlay.show({
                                    text: '<?php echo e(__('common.saving') ?? 'Saving'); ?>...',
                                    subtext: '<?php echo e(__('common.please_wait') ?? 'Please wait'); ?>...'
                                });
                            }

                            $.ajax({
                                url: '<?php echo e(route('admin.category-management.categories.reorder')); ?>',
                                type: 'POST',
                                data: {
                                    _token: '<?php echo e(csrf_token()); ?>',
                                    items: items
                                },
                                success: function(response) {
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }
                                    
                                    if (response.success) {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: '<?php echo e(__('common.success') ?? 'Success'); ?>',
                                                text: response.message || '<?php echo e(__('common.reorder_success') ?? 'Order updated successfully'); ?>',
                                                timer: 2000,
                                                showConfirmButton: false,
                                                toast: true,
                                                position: 'top-end'
                                            });
                                        }
                                        // Reload table to get updated sort numbers
                                        table.ajax.reload(null, false);
                                    } else {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                title: '<?php echo e(__('common.error') ?? 'Error'); ?>',
                                                text: response.message || '<?php echo e(__('common.reorder_error') ?? 'Failed to update order'); ?>'
                                            });
                                        }
                                        table.ajax.reload(null, false);
                                    }
                                },
                                error: function(xhr) {
                                    if (typeof LoadingOverlay !== 'undefined') {
                                        LoadingOverlay.hide();
                                    }
                                    
                                    let errorMessage = '<?php echo e(__('common.reorder_error') ?? 'Failed to update order'); ?>';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: '<?php echo e(__('common.error') ?? 'Error'); ?>',
                                            text: errorMessage
                                        });
                                    }
                                    table.ajax.reload(null, false);
                                }
                            });
                        }
                    }
                });
                
                console.log('Sortable initialized for categories');
            }

            // Re-initialize sortable after table draw
            table.on('draw', function() {
                setTimeout(function() {
                    if (typeof $.ui !== 'undefined' && typeof $.ui.sortable !== 'undefined') {
                        initSortable();
                        updateDragDropState();
                    }
                }, 100);
            });
            <?php endif; ?>
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CategoryManagment\resources/views/category/index.blade.php ENDPATH**/ ?>