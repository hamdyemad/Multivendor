

<?php $__env->startSection('title'); ?>
    <?php echo e(trans('categorymanagment::department.departments_management')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Drag and Drop Styles */
    #departmentsDataTable tbody tr {
        cursor: default;
    }
    #departmentsDataTable tbody tr.ui-sortable-helper {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        cursor: grabbing;
    }
    #departmentsDataTable tbody tr.ui-sortable-placeholder {
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
                    ['title' => trans('categorymanagment::department.departments_management')],
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
                    ['title' => trans('categorymanagment::department.departments_management')],
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
                        <h4 class="mb-0 fw-500"><?php echo e(trans('categorymanagment::department.departments_management')); ?></h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.create')): ?>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('admin.category-management.departments.create')); ?>"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> <?php echo e(trans('categorymanagment::department.add_department')); ?>

                                </a>
                            </div>
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
                                            <label for="search"
                                                class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.search')); ?></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="<?php echo e(__('common.search')); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active"
                                                class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('categorymanagment::department.activation')); ?></label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="active">
                                                <option value=""><?php echo e(trans('categorymanagment::department.all')); ?>

                                                </option>
                                                <option value="1"><?php echo e(trans('categorymanagment::department.active')); ?>

                                                </option>
                                                <option value="0">
                                                    <?php echo e(trans('categorymanagment::department.inactive')); ?>

                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="view_status"
                                                class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('categorymanagment::department.view_status')); ?></label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="view_status">
                                                <option value=""><?php echo e(trans('categorymanagment::department.all')); ?>

                                                </option>
                                                <option value="1"><?php echo e(__('common.visible')); ?>

                                                </option>
                                                <option value="0"><?php echo e(__('common.hidden')); ?>

                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from"
                                                class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.created_date_from')); ?></label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_to"
                                                class="il-gray fs-14 fw-500 mb-10"><?php echo e(trans('common.created_date_to')); ?></label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sort_column"
                                                class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('common.sort_by') ?? 'Sort By'); ?></label>
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
                                            <label for="sort_direction"
                                                class="il-gray fs-14 fw-500 mb-10"><?php echo e(__('common.sort_direction') ?? 'Sort Direction'); ?></label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="sort_direction">
                                                <option value="asc" selected><?php echo e(__('common.ascending') ?? 'Ascending'); ?></option>
                                                <option value="desc"><?php echo e(__('common.descending') ?? 'Descending'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex">
                                        <div class="form-group">
                                            <button type="button" id="resetFilters"
                                                class="btn btn-warning btn-default btn-squared"
                                                title="<?php echo e(__('common.reset')); ?>">
                                                <i class="uil uil-redo me-1"></i> <?php echo e(__('common.reset_filters')); ?>

                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0"><?php echo e(__('common.show') ?? 'Show'); ?></label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0"><?php echo e(__('common.entries') ?? 'entries'); ?></label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div class="reorder-info" id="reorderInfo">
                            <i class="uil uil-info-circle me-2"></i>
                            <?php echo e(__('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.'); ?>

                        </div>
                        <table id="departmentsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th style="width: 40px;">
                                        <span class="userDatatable-title"><i class="uil uil-sort"></i></span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title"><?php echo e(trans('categorymanagment::department.department_information') ?? 'Department Information'); ?></span>
                                    </th>
                                    <th>
                                        <span
                                            class="userDatatable-title"><?php echo e(trans('categorymanagment::department.view_status') ?? 'View Status'); ?></span>
                                    </th>
                                    <th>
                                        <span
                                            class="userDatatable-title"><?php echo e(trans('categorymanagment::department.activation')); ?></span>
                                    </th>
                                    <th>
                                        <span
                                            class="userDatatable-title"><?php echo e(trans('categorymanagment::department.created_at')); ?></span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title"><?php echo e(trans('common.actions')); ?></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (isset($component)) { $__componentOriginal4d4be0bcf29da35c820833c3b98d2b58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-with-loading','data' => ['modalId' => 'modal-delete-department','tableId' => 'departmentsDataTable','deleteButtonClass' => 'delete-department','title' => __('main.confirm delete'),'message' => __('main.are you sure you want to delete this'),'itemNameId' => 'delete-department-name','confirmBtnId' => 'confirmDeleteDepartmentBtn','cancelText' => __('main.cancel'),'deleteText' => __('main.delete'),'loadingDeleting' => trans('main.deleting') ?? 'Deleting...','loadingPleaseWait' => trans('main.please wait') ?? 'Please wait...','loadingDeletedSuccessfully' => trans('main.deleted success') ?? 'Deleted Successfully!','loadingRefreshing' => trans('main.refreshing') ?? 'Refreshing...','errorDeleting' => __('main.error on delete')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-with-loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-department','tableId' => 'departmentsDataTable','deleteButtonClass' => 'delete-department','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.confirm delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.are you sure you want to delete this')),'itemNameId' => 'delete-department-name','confirmBtnId' => 'confirmDeleteDepartmentBtn','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.delete')),'loadingDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleting') ?? 'Deleting...'),'loadingPleaseWait' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.please wait') ?? 'Please wait...'),'loadingDeletedSuccessfully' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleted success') ?? 'Deleted Successfully!'),'loadingRefreshing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.refreshing') ?? 'Refreshing...'),'errorDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.error on delete'))]); ?>
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
            let per_page = 10;
            let dragDropEnabled = true; // Will be controlled by sort filters

            // Server-side processing with pagination
            var table = $('#departmentsDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '<?php echo e(route('admin.category-management.departments.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;

                        // Add search parameter from custom input
                        d.search = $('#search').val();

                        // Add filter parameters
                        d.active = $('#active').val();
                        d.view_status = $('#view_status').val();
                        d.commission_from = $('#commission_from').val();
                        d.commission_to = $('#commission_to').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        // Add custom sorting parameters
                        d.sort_column = $('#sort_column').val();
                        d.sort_direction = $('#sort_direction').val();

                        console.log('📤 Sending to server:', {
                            search: d.search,
                            active: d.active,
                            view_status: d.view_status,
                            commission_from: d.commission_from,
                            commission_to: d.commission_to,
                            created_date_from: d.created_date_from,
                            created_date_to: d.created_date_to,
                            sort_column: d.sort_column,
                            sort_direction: d.sort_direction
                        });

                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('📦 Data received from server:', json);
                        // Map backend response to DataTables format
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.log('DataTables Error:', xhr, error, code);
                        alert('Error loading data. Please check console for details.');
                    }
                },
                columns: [
                    // Drag handle column
                    {
                        data: null,
                        name: 'drag',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<span class="drag-handle" title="<?php echo e(__('common.drag_to_reorder') ?? 'Drag to reorder'); ?>"><i class="uil uil-draggabledots"></i></span>`;
                        }
                    },
                    // ID column
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        className: 'text-center fw-bold',
                        render: function(data) {
                            return data;
                        }
                    },
                    // Department Information column (merged: names, commission, sort_number)
                    {
                        data: 'translations',
                        name: 'department_information',
                        orderable: false,
                        render: function(data, type, row) {
                            let html = '<div class="department-info-container">';

                            // Department Names with language badges
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

                            // Commission and Sort Number
                            html += '<div class="department-meta-info">';
                            html += `<div class="mb-1">
                                <small class="text-muted"><?php echo e(trans('categorymanagment::department.commission')); ?>:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${row.commission ? row.commission + '%' : '0%'}</span>
                            </div>`;
                            html += `<div class="mb-1">
                                <small class="text-muted"><?php echo e(trans('categorymanagment::department.sort_number')); ?>:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${row.sort_number ?? 0}</span>
                            </div>`;
                            html += '</div>';

                            html += '</div>';
                            return html;
                        },
                        className: 'text-start'
                    },
                    // View Status column
                    {
                        data: 'view_status',
                        name: 'view_status',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.change-status')): ?>
                                const isChecked = data ? 'checked' : '';
                                const switchId = 'view-status-switch-' + row.department_id;
                                return `<div class="userDatatable-content">
                                    <div class="form-switch">
                                        <input class="form-check-input view-status-switcher"
                                               type="checkbox"
                                               id="${switchId}"
                                               data-department-id="${row.department_id}"
                                               ${isChecked}
                                               style="cursor: pointer;">
                                        <label class="form-check-label" for="${switchId}"></label>
                                    </div>
                                </div>`;
                            <?php else: ?>
                                if (data == 1) {
                                    return '<span class="badge badge-success badge-round badge-lg"><?php echo e(trans('common.visible') ?? 'Visible'); ?></span>';
                                } else {
                                    return '<span class="badge badge-danger badge-round badge-lg"><?php echo e(trans('common.hidden') ?? 'Hidden'); ?></span>';
                                }
                            <?php endif; ?>
                        }
                    },
                    // Active Status column
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            // For display, return formatted HTML with switcher (for users with change-status permission)
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.change-status')): ?>
                                const isChecked = data ? 'checked' : '';
                                const switchId = 'status-switch-' + row.department_id;
                                const departmentName = row.translations && row.translations['en'] ?
                                    row.translations['en'].name : (row.translations && row
                                        .translations['ar'] ? row.translations['ar'].name :
                                        'Department #' + row.department_id);

                                return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input status-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-department-id="${row.department_id}"
                                           data-department-name="${$('<div>').text(departmentName).html()}"
                                           ${isChecked}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                            <?php else: ?>
                                if (data == 1) {
                                    return '<span class="badge badge-success badge-round badge-lg"><?php echo e(trans('categorymanagment::department.active')); ?></span>';
                                } else {
                                    return '<span class="badge badge-danger badge-round badge-lg"><?php echo e(trans('categorymanagment::department.inactive')); ?></span>';
                                }
                            <?php endif; ?>
                        }
                    },
                    // Created At column
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    // Actions column
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let viewUrl =
                                "<?php echo e(route('admin.category-management.departments.show', ':id')); ?>"
                                .replace(':id', row.department_id);
                            let editUrl =
                                "<?php echo e(route('admin.category-management.departments.edit', ':id')); ?>"
                                .replace(':id', row.department_id);
                            return `
                            <ul class="mb-0 d-flex flex-wrap justify-content-center">
                                <li>
                                    <a href="${viewUrl}"
                                    class="btn btn-primary table_action_father me-1"
                                    title="<?php echo e(trans('common.view')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                </li>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.edit')): ?>
                                <li>
                                    <a href="${editUrl}"
                                    class="btn btn-warning table_action_father me-1"
                                    title="<?php echo e(trans('common.edit')); ?>">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.delete')): ?>
                                <li>
                                    <a href="javascript:void(0);"
                                    class="btn btn-danger delete-department table_action_father"
                                    title="<?php echo e(trans('common.delete')); ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-department"
                                    data-id="${row.department_id}"
                                    data-name="${$('<div>').text(row.translations && row.translations['en'] ? row.translations['en'].name : 'Department').html()}"
                                    data-url="${'<?php echo e(route('admin.category-management.departments.destroy', 'REPLACE_ID')); ?>'.replace('REPLACE_ID', row.department_id)}">
                                        <i class="uil uil-trash-alt table_action_icon"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>`;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '<?php echo e(trans('categorymanagment::department.departments_management')); ?>'
                }],
                searching: true, // Enable built-in search
                language: {
                    lengthMenu: "<?php echo e(__('common.show') ?? 'Show'); ?> _MENU_",
                    info: "<?php echo e(__('common.showing') ?? 'Showing'); ?> _START_ <?php echo e(__('common.to') ?? 'to'); ?> _END_ <?php echo e(__('common.of') ?? 'of'); ?> _TOTAL_ <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoEmpty: "<?php echo e(__('common.showing') ?? 'Showing'); ?> 0 <?php echo e(__('common.to') ?? 'to'); ?> 0 <?php echo e(__('common.of') ?? 'of'); ?> 0 <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoFiltered: "(<?php echo e(__('common.filtered_from') ?? 'filtered from'); ?> _MAX_ <?php echo e(__('common.total_entries') ?? 'total entries'); ?>)",
                    zeroRecords: "<?php echo e(trans('categorymanagment::department.no_departments_found') ?? 'No departments found'); ?>",
                    emptyTable: "<?php echo e(trans('categorymanagment::department.no_departments_found') ?? 'No departments found'); ?>",
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
                }
            });

            // Initialize Select2 on custom entries select
            if ($.fn.select2) {
                $('#entriesSelect').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: 'auto'
                });
            }

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Search with server-side processing and debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    console.log('🔍 Search triggered:', $('#search').val());
                    table.ajax.reload(); // Reload data from server with new search value
                }, 500);
            });

            $('#search').on('change', function() {
                clearTimeout(searchTimer);
                console.log('🔍 Search changed:', $(this).val());
                table.ajax.reload();
            });

            // Custom filter function for active status and dates on cached data
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Only apply to departments table
                    if (settings.nTable.id !== 'departmentsDataTable') {
                        return true;
                    }

                    var activeFilter = $('#active').val();
                    var dateFrom = $('#created_date_from').val();
                    var dateTo = $('#created_date_to').val();

                    // Active filter (column 5)
                    if (activeFilter && activeFilter !== '') {
                        var colIndex = 5;

                        // Get the actual rendered cell content (with HTML)
                        var rowNode = table.row(dataIndex).node();
                        if (!rowNode) {
                            return true;
                        }

                        var cells = $(rowNode).find('td');
                        if (cells.length <= colIndex) {
                            return true;
                        }

                        // Get the HTML content of the cell
                        var cellHtml = $(cells[colIndex]).html();

                        if (!cellHtml) {
                            return true;
                        }

                        // Check if the cell contains the success badge (active) or danger badge (inactive)
                        var isActiveRow = cellHtml.indexOf('badge-success') > -1;
                        var isInactiveRow = cellHtml.indexOf('badge-danger') > -1;

                        // Filter logic
                        if (activeFilter === '1') {
                            // Show only active rows (must have badge-success)
                            return isActiveRow;
                        } else if (activeFilter === '0') {
                            // Show only inactive rows (must have badge-danger)
                            return isInactiveRow;
                        }
                    }

                    // Date filters (column <?php echo e(count($languages) + 3); ?>)
                    if (dateFrom || dateTo) {
                        var dateColumn = data[<?php echo e(count($languages) + 3); ?>];
                        if (dateColumn) {
                            var rowDate = dateColumn.replace(/<[^>]*>/g, '').trim().split(' ')[
                                0]; // Extract YYYY-MM-DD
                            if (dateFrom && rowDate < dateFrom) return false;
                            if (dateTo && rowDate > dateTo) return false;
                        }
                    }

                    return true;
                }
            );

            // Server-side filter event listeners - reload data when filters change
            $('#active, #view_status, #commission_from, #commission_to, #created_date_from, #created_date_to').on('change',
                function() {
                    console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                    table.ajax.reload();
                });

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
                    $('#departmentsDataTable tbody').removeClass('drag-disabled');
                    $('.drag-handle').css('opacity', '1').css('cursor', 'grab');
                    $('#reorderInfo').removeClass('show').html('<i class="uil uil-info-circle me-2"></i><?php echo e(__('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.'); ?>');
                } else {
                    $('#departmentsDataTable tbody').addClass('drag-disabled');
                    $('.drag-handle').css('opacity', '0.3').css('cursor', 'not-allowed');
                    $('#reorderInfo').addClass('show').html('<i class="uil uil-exclamation-triangle me-2"></i><?php echo e(__('common.drag_drop_disabled_info') ?? 'Drag and drop is only available when sorting by Sort Number (Ascending).'); ?>');
                }
            }

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#active').val('');
                $('#view_status').val('');
                $('#commission_from').val('');
                $('#commission_to').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                $('#sort_column').val('sort_number');
                $('#sort_direction').val('asc');
                // Reload table with cleared filters
                table.ajax.reload();
                updateDragDropState();
            });

            // Status switcher handler
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const departmentId = switcher.data('department-id');
                const departmentName = switcher.data('department-name');
                const newStatus = switcher.is(':checked') ? 1 : 2; // 1=active, 2=inactive

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '<?php echo e(__('categorymanagment::department.change_status')); ?>',
                        subtext: '<?php echo e(__('common.please_wait') ?? 'Please wait'); ?>...'
                    });
                }

                // Make AJAX request
                $.ajax({
                    url: '<?php echo e(route('admin.category-management.departments.change-status', ':id')); ?>'
                        .replace(':id', departmentId),
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
                            '<?php echo e(__('categorymanagment::department.error_changing_status')); ?>';
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
                const departmentId = switcher.data('department-id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Make AJAX request
                $.ajax({
                    url: '<?php echo e(route('admin.category-management.departments.change-view-status', ':id')); ?>'
                        .replace(':id', departmentId),
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

            // Delete functionality is now handled by the delete-with-loading component

            // Initialize drag and drop sortable
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.edit')): ?>
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
                var $tbody = $('#departmentsDataTable tbody');
                
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
                        $('#departmentsDataTable tbody tr').each(function(index) {
                            var rowData = table.row(this).data();
                            if (rowData && rowData.department_id) {
                                items.push({
                                    id: rowData.department_id,
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
                                url: '<?php echo e(route('admin.category-management.departments.reorder')); ?>',
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
                
                console.log('Sortable initialized for departments');
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

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CategoryManagment\resources/views/department/index.blade.php ENDPATH**/ ?>