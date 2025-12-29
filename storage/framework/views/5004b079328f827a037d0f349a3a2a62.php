
<?php $__env->startSection('title', trans('categorymanagment::subcategory.subcategories_management')); ?>

<?php $__env->startPush('styles'); ?>
<style>

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
                    ['title' => trans('categorymanagment::subcategory.subcategories_management')],
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
                    ['title' => trans('categorymanagment::subcategory.subcategories_management')],
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
                        <h4 class="mb-0 fw-500"><?php echo e(trans('categorymanagment::subcategory.subcategories_management')); ?></h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sub-categories.create')): ?>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('admin.category-management.subcategories.create')); ?>"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> <?php echo e(trans('categorymanagment::subcategory.add_subcategory')); ?>

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
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> <?php echo e(__('common.search')); ?>

                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search"
                                                placeholder="<?php echo e(__('categorymanagment::subcategory.search_by_name')); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <?php if (isset($component)) { $__componentOriginal8b85cde560f35167074cbd8632d75a5d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8b85cde560f35167074cbd8632d75a5d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-tags','data' => ['name' => 'category_filter','label' => __('categorymanagment::subcategory.category'),'options' => $categories,'selected' => [],'placeholder' => __('categorymanagment::subcategory.select_category'),'multiple' => false,'id' => 'category_filter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-tags'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'category_filter','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('categorymanagment::subcategory.category')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([]),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('categorymanagment::subcategory.select_category')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'id' => 'category_filter']); ?>
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
                                                <?php echo e(__('categorymanagment::subcategory.activation')); ?>

                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="active">
                                                <option value=""><?php echo e(__('categorymanagment::subcategory.all')); ?>

                                                </option>
                                                <option value="1"><?php echo e(__('categorymanagment::subcategory.active')); ?>

                                                </option>
                                                <option value="0"><?php echo e(__('categorymanagment::subcategory.inactive')); ?>

                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="view_status" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-eye me-1"></i>
                                                <?php echo e(__('categorymanagment::subcategory.view_status')); ?>

                                            </label>
                                            <select
                                                class="form-control form-select ih-medium ip-gray radius-xs b-light"
                                                id="view_status">
                                                <option value=""><?php echo e(__('categorymanagment::subcategory.all')); ?>

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
                        <table id="subcategoriesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('categorymanagment::subcategory.subcategory_information')); ?></span></th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('categorymanagment::subcategory.category')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('categorymanagment::subcategory.view_status')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('categorymanagment::subcategory.activation')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('categorymanagment::subcategory.created_at')); ?></span>
                                    </th>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-with-loading','data' => ['modalId' => 'modal-delete-subcategory','tableId' => 'subcategoriesDataTable','deleteButtonClass' => 'delete-subcategory','title' => __('main.confirm delete'),'message' => __('main.are you sure you want to delete this'),'itemNameId' => 'delete-subcategory-name','confirmBtnId' => 'confirmDeleteSubCategoryBtn','cancelText' => __('main.cancel'),'deleteText' => __('main.delete'),'loadingDeleting' => trans('main.deleting') ?? 'Deleting...','loadingPleaseWait' => trans('main.please wait') ?? 'Please wait...','loadingDeletedSuccessfully' => trans('main.deleted success') ?? 'Deleted Successfully!','loadingRefreshing' => trans('main.refreshing') ?? 'Refreshing...','errorDeleting' => __('main.error on delete')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-with-loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-subcategory','tableId' => 'subcategoriesDataTable','deleteButtonClass' => 'delete-subcategory','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.confirm delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.are you sure you want to delete this')),'itemNameId' => 'delete-subcategory-name','confirmBtnId' => 'confirmDeleteSubCategoryBtn','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.delete')),'loadingDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleting') ?? 'Deleting...'),'loadingPleaseWait' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.please wait') ?? 'Please wait...'),'loadingDeletedSuccessfully' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleted success') ?? 'Deleted Successfully!'),'loadingRefreshing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.refreshing') ?? 'Refreshing...'),'errorDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('main.error on delete'))]); ?>
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

            // Server-side processing with pagination
            var table = $('#subcategoriesDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '<?php echo e(route('admin.category-management.subcategories.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;

                        // Add search parameter from custom input
                        d.search = $('#search').val();

                        // Add filter parameters
                        d.category_id = $('#category_filter-single-display').find('input[name="category_filter"]').val() || '';
                        d.active = $('#active').val();
                        d.view_status = $('#view_status').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();

                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            var columnIndex = d.order[0].column;
                            var sortType = '';

                            // Map column index to sort type
                            if (columnIndex == 0) {
                                sortType = 'id';
                            } else if (columnIndex == 1) {
                                sortType = 'name';
                            } else if (columnIndex == 2) {
                                sortType = 'category';
                            } else if (columnIndex == 3) {
                                sortType = 'sort_number';
                            } else if (columnIndex == 4) {
                                sortType = 'view_status';
                            } else if (columnIndex == 5) {
                                sortType = 'active';
                            } else if (columnIndex == 6) {
                                sortType = 'created_at';
                            }

                            d.sort_type = sortType;
                            d.sort_by = d.order[0].dir; // 'asc' or 'desc'
                        }

                        console.log('📤 Sending to server:', {
                            search: d.search,
                            category_id: d.category_id,
                            active: d.active,
                            created_date_from: d.created_date_from,
                            created_date_to: d.created_date_to,
                            sort_type: d.sort_type,
                            sort_by: d.sort_by
                        });

                        return d;
                    },
                    dataSrc: function(json) {
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
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        className: 'text-center fw-bold'
                    }, // #
                    {
                        data: 'translations',
                        name: 'subcategory_information',
                        orderable: false,
                        render: function(data, type, row) {
                            let html = '<div class="subcategory-info-container">';
                            
                            // Subcategory Names with language badges
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
                            html += '<div class="subcategory-meta-info">';
                            html += `<div class="mb-1">
                                <small class="text-muted"><?php echo e(trans('categorymanagment::subcategory.sort_number')); ?>:</small>
                                <span class="badge badge-secondary badge-round badge-lg ms-1">${row.sort_number ?? 0}</span>
                            </div>`;
                            html += '</div>';

                            html += '</div>';
                            return html;
                        },
                        className: 'text-start'
                    },
                    {
                        data: 'category',
                        name: 'category',
                        orderable: false,
                        render: function(data) {
                            if (data && data.name) {
                                return '<span class="badge badge-round badge-primary badge-lg" data-category-id="' +
                                    data.id + '">' + data.name + '</span>';
                            }
                            return '<span class="text-muted">—</span>';
                        }
                    },
                    {
                        data: 'view_status',
                        name: 'view_status',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sub-categories.edit')): ?>
                                const isChecked = data ? 'checked' : '';
                                const switchId = 'view-status-switch-' + row.id;

                                return `<div class="form-switch">
                                <input class="form-check-input view-status-switcher"
                                       type="checkbox"
                                       id="${switchId}"
                                       data-subcategory-id="${row.id}"
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
                            // For display, return formatted HTML with switcher (for users with change-status permission)
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sub-categories.change-status')): ?>
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            const subcategoryName = row.translations && row.translations['en'] ? row.translations['en'].name : (row.translations && row.translations['ar'] ? row.translations['ar'].name : 'Subcategory #' + row.id);

                            return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input status-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-subcategory-id="${row.id}"
                                           data-subcategory-name="${$('<div>').text(subcategoryName).html()}"
                                           ${isChecked}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                            <?php else: ?>
                            if (data) {
                                return '<span class="badge badge-round badge-success badge-lg"><?php echo e(trans('categorymanagment::subcategory.active')); ?></span>';
                            } else {
                                return '<span class="badge badge-round badge-danger badge-lg"><?php echo e(trans('categorymanagment::subcategory.inactive')); ?></span>';
                            }
                            <?php endif; ?>
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let showUrl = "<?php echo e(route('admin.category-management.subcategories.show', ':id')); ?>".replace(':id',row.id),
                                editUrl = "<?php echo e(route('admin.category-management.subcategories.edit', ':id')); ?>".replace(':id',row.id),
                                destroyUrl = "<?php echo e(route('admin.category-management.subcategories.destroy', ':id')); ?>".replace(':id',row.id);

                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="<?php echo e(trans('common.view')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sub-categories.edit')): ?>
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="<?php echo e(trans('common.edit')); ?>">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sub-categories.delete')): ?>
                                    <a href="javascript:void(0);"
                                    class="remove delete-subcategory btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-subcategory"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.translations?.<?php echo e(app()->getLocale()); ?>?.name || 'Subcategory'}"
                                    data-url="${destroyUrl}"
                                    title="<?php echo e(trans('common.delete')); ?>">
                                        <i class="uil uil-trash-alt table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            `;

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
                    title: '<?php echo e(trans('categorymanagment::subcategory.subcategories_management')); ?>'
                }],
                searching: true, // Enable built-in search
                language: {
                    lengthMenu: "<?php echo e(__('common.show') ?? 'Show'); ?> _MENU_",
                    info: "<?php echo e(__('common.showing') ?? 'Showing'); ?> _START_ <?php echo e(__('common.to') ?? 'to'); ?> _END_ <?php echo e(__('common.of') ?? 'of'); ?> _TOTAL_ <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoEmpty: "<?php echo e(__('common.showing') ?? 'Showing'); ?> 0 <?php echo e(__('common.to') ?? 'to'); ?> 0 <?php echo e(__('common.of') ?? 'of'); ?> 0 <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoFiltered: "(<?php echo e(__('common.filtered_from') ?? 'filtered from'); ?> _MAX_ <?php echo e(__('common.total_entries') ?? 'total entries'); ?>)",
                    zeroRecords: "<?php echo e(trans('categorymanagment::subcategory.no_subcategories_found') ?? 'No subcategories found'); ?>",
                    emptyTable: "<?php echo e(trans('categorymanagment::subcategory.no_subcategories_found') ?? 'No subcategories found'); ?>",
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

            // Initialize Select2 for entries select if available
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
                    // Only apply to subcategories table
                    if (settings.nTable.id !== 'subcategoriesDataTable') {
                        return true;
                    }

                    var categoryFilter = $('#category_filter-single-display').find('input[name="category_filter"]').val() || '';
                    var activeFilter = $('#active').val();
                    var dateFrom = $('#created_date_from').val();
                    var dateTo = $('#created_date_to').val();

                    // Category filter (column 2)
                    if (categoryFilter && categoryFilter !== '') {
                        var categoryColIndex = 2;

                        // Get the actual rendered cell content with the data-category-id attribute
                        var rowNode = table.row(dataIndex).node();
                        if (!rowNode) {
                            return true;
                        }

                        var cells = $(rowNode).find('td');
                        if (cells.length <= categoryColIndex) {
                            return true;
                        }

                        // Get the category ID from the data attribute in the badge
                        var categoryBadge = $(cells[categoryColIndex]).find('[data-category-id]');
                        if (categoryBadge.length > 0) {
                            var categoryId = categoryBadge.attr('data-category-id');
                            // Compare as strings to avoid type mismatch
                            if (String(categoryId) !== String(categoryFilter)) {
                                return false;
                            }
                        } else {
                            // If no category badge found (category is '-'), hide it when filter is active
                            return false;
                        }
                    }

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

                    // Date filters (column 6)
                    if (dateFrom || dateTo) {
                        var dateColumn = data[6];
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
            $('#active, #view_status, #created_date_from, #created_date_to').on('change', function() {
                console.log('Filter changed:', $(this).attr('id'), '=', $(this).val());
                table.ajax.reload();
            });

            // Category filter change handler (searchable-tags single select)
            $('#category_filter-wrapper').on('searchable-tags:change', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search, #active, #view_status, #created_date_from, #created_date_to').val('');
                
                // Reset category filter (single select)
                const catDisplay = $('#category_filter-single-display');
                const catDropdown = $('#category_filter-dropdown');
                catDisplay.html('<span class="placeholder-text text-muted"><?php echo e(__("categorymanagment::subcategory.select_category")); ?></span>');
                catDropdown.find('.tag-option').removeClass('selected').show();
                
                table.ajax.reload();
            });

            // Status switcher handler
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const subcategoryId = switcher.data('subcategory-id');
                const subcategoryName = switcher.data('subcategory-name');
                const newStatus = switcher.is(':checked') ? 1 : 2; // 1=active, 2=inactive

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Show loading overlay
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.show({
                        text: '<?php echo e(__('categorymanagment::subcategory.change_status')); ?>',
                        subtext: '<?php echo e(__('common.please_wait') ?? 'Please wait'); ?>...'
                    });
                }

                // Make AJAX request
                $.ajax({
                    url: '<?php echo e(route('admin.category-management.subcategories.change-status', ':id')); ?>'.replace(':id', subcategoryId),
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

                        let errorMessage = '<?php echo e(__('categorymanagment::subcategory.error_changing_status')); ?>';
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
                const subcategoryId = switcher.data('subcategory-id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                // Disable switcher during request
                switcher.prop('disabled', true);

                // Make AJAX request
                $.ajax({
                    url: '<?php echo e(route('admin.category-management.subcategories.change-view-status', ':id')); ?>'.replace(':id', subcategoryId),
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
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CategoryManagment\resources/views/subcategory/index.blade.php ENDPATH**/ ?>