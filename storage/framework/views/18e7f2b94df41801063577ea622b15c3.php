
<?php $__env->startSection('title'); ?>
    <?php echo e(trans('catalogmanagement::occasion.occasions_management')); ?> | Bnaia
<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
    <!-- Select2 CSS loaded via Vite -->
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
                    ['title' => trans('catalogmanagement::occasion.occasions_management')],
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
                    ['title' => trans('catalogmanagement::occasion.occasions_management')],
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
                        <h4 class="mb-0 fw-500 fw-bold"><?php echo e(trans('catalogmanagement::occasion.occasions_management')); ?></h4>
                        <div class="d-flex gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('occasions.create')): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$occasionExists): ?>
                                <a href="<?php echo e(route('admin.occasions.create')); ?>"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> <?php echo e(trans('catalogmanagement::occasion.add_occasion')); ?>

                                </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?>
                        </div>
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
                                                id="search" placeholder="<?php echo e(__('common.search')); ?>..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(trans('catalogmanagement::occasion.status')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value=""><?php echo e(trans('catalogmanagement::occasion.all_status')); ?>

                                                </option>
                                                <option value="1"><?php echo e(trans('catalogmanagement::occasion.active')); ?>

                                                </option>
                                                <option value="0"><?php echo e(trans('catalogmanagement::occasion.inactive')); ?>

                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('catalogmanagement::occasion.created_from')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_from_filter">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_until_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('catalogmanagement::occasion.created_until')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_until_filter">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="start_date_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('catalogmanagement::occasion.start_date')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="start_date_filter">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="end_date_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('catalogmanagement::occasion.end_date')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="end_date_filter">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center mt-3">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="<?php echo e(__('common.search')); ?>">
                                            <i class="uil uil-search me-1"></i>
                                            <?php echo e(__('common.search')); ?>

                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="<?php echo e(__('common.reset')); ?>">
                                            <i class="uil uil-redo me-1"></i>
                                            <?php echo e(__('common.reset_filters')); ?>

                                        </button>
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
                        <table id="occasionsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(trans('catalogmanagement::occasion.occasion_information')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(trans('catalogmanagement::occasion.start_date')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(trans('catalogmanagement::occasion.end_date')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(trans('catalogmanagement::occasion.status')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(trans('catalogmanagement::occasion.created_at')); ?></span>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-with-loading','data' => ['modalId' => 'modal-delete-occasion','tableId' => 'occasionsDataTable','deleteButtonClass' => 'delete-occasion','title' => trans('main.confirm delete'),'message' => trans('main.are you sure you want to delete this'),'itemNameId' => 'delete-occasion-name','confirmBtnId' => 'confirmDeleteOccasionBtn','cancelText' => trans('main.cancel'),'deleteText' => trans('main.delete'),'loadingDeleting' => trans('main.deleting'),'loadingPleaseWait' => trans('main.please wait'),'loadingDeletedSuccessfully' => trans('main.deleted success'),'loadingRefreshing' => trans('main.refreshing'),'errorDeleting' => trans('main.error on delete')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-with-loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-occasion','tableId' => 'occasionsDataTable','deleteButtonClass' => 'delete-occasion','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.confirm delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.are you sure you want to delete this')),'itemNameId' => 'delete-occasion-name','confirmBtnId' => 'confirmDeleteOccasionBtn','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.delete')),'loadingDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleting')),'loadingPleaseWait' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.please wait')),'loadingDeletedSuccessfully' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.deleted success')),'loadingRefreshing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.refreshing')),'errorDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.error on delete'))]); ?>
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
            // Initialize Select2
            if ($.fn.select2) {
                $('.select2').select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: '<?php echo e(__("common.all")); ?>'
                });
            }

            let per_page = 10;

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));
            if (urlParams.has('start_date')) $('#start_date_filter').val(urlParams.get('start_date'));
            if (urlParams.has('end_date')) $('#end_date_filter').val(urlParams.get('end_date'));

            // Server-side processing with pagination
            let table = $('#occasionsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.occasions.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val();
                        d.created_from = $('#created_from_filter').val();
                        d.created_until = $('#created_until_filter').val();
                        d.start_date = $('#start_date_filter').val();
                        d.end_date = $('#end_date_filter').val();
                        return d;
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'occasion_information',
                        name: 'occasion_information',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data) return '<span class="text-muted">—</span>';

                            let html =
                                '<div class="occasion-info-container" style="display: flex; gap: 12px; align-items: flex-start;">';

                            // Image
                            if (data.image) {
                                html += `<div style="flex-shrink: 0;">
                                    <img src="${data.image}" alt="Occasion Image" style="width: 60px; height: 60px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                </div>`;
                            } else {
                                html +=
                                    `<img src="<?php echo e(asset('assets/img/default.png')); ?>" alt="Occasion Image" style="width: 60px; height: 60px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">`;
                            }

                            // Names
                            html += '<div style="flex: 1; min-width: 0;">';

                            // EN Name
                            if (data.name_en && data.name_en !== '-') {
                                html += `<div style="margin-bottom: 4px;">
                                    <span class="badge bg-primary text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">EN</span>
                                    <span class="text-dark fw-semibold" style="font-size: 14px;">${$('<div/>').text(data.name_en).html()}</span>
                                </div>`;
                            }

                            // AR Name
                            if (data.name_ar && data.name_ar !== '-') {
                                html += `<div style="margin-bottom: 4px;">
                                    <span class="badge bg-success text-white px-2 py-1 me-2 rounded-pill fw-bold" style="font-size: 10px;">AR</span>
                                    <span class="text-dark fw-semibold" dir="rtl" style="font-size: 14px; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">${$('<div/>').text(data.name_ar).html()}</span>
                                </div>`;
                            }

                            html += '</div></div>';
                            return html;
                        }
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const isChecked = data ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            const isDisabled =
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('occasions.toggle-status')): ?>
                                    ''
                                <?php else: ?>
                                    'disabled'
                                <?php endif; ?> ;
                            return `<div class="userDatatable-content">
                                <div class="form-switch">
                                    <input class="form-check-input status-switcher"
                                           type="checkbox"
                                           id="${switchId}"
                                           data-id="${row.id}"
                                           ${isChecked}
                                           ${isDisabled}
                                           style="cursor: pointer;">
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            </div>`;
                        }
                    },
                    {
                        data: 'created_at',
                        orderable: false,
                        searchable: false,
                        name: 'created_at',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let showUrl = "<?php echo e(route('admin.occasions.show', ':id')); ?>".replace(
                                ':id', row.id);
                            let editUrl = "<?php echo e(route('admin.occasions.edit', ':id')); ?>".replace(
                                ':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="<?php echo e(trans('catalogmanagement::occasion.view_occasion')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('occasions.edit')): ?>
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="<?php echo e(trans('catalogmanagement::occasion.edit_occasion')); ?>">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('occasions.delete')): ?>
                                    <a href="javascript:void(0);"
                                    class="remove delete-occasion btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-occasion"
                                    data-id="${row.id}"
                                    data-name="${row.name && row.name.en ? row.name.en : 'Occasion'}"
                                    data-url="<?php echo e(route('admin.occasions.index')); ?>/${row.id}"
                                    title="<?php echo e(trans('catalogmanagement::occasion.delete_occasion')); ?>">
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
                order: [
                    [0, 'desc']
                ],
                pagingType: 'full_numbers',
                language: {
                    search: '',
                    searchPlaceholder: "<?php echo e(__('common.search')); ?>...",
                    lengthMenu: '_MENU_',
                    info: "<?php echo e(__('common.showing')); ?> _START_ <?php echo e(__('common.to')); ?> _END_ <?php echo e(__('common.of')); ?> _TOTAL_ <?php echo e(__('common.entries')); ?>",
                    infoEmpty: "<?php echo e(__('common.showing')); ?> 0 <?php echo e(__('common.to')); ?> 0 <?php echo e(__('common.of')); ?> 0 <?php echo e(__('common.entries')); ?>",
                    infoFiltered: "(<?php echo e(__('common.filtered_from')); ?> _MAX_ <?php echo e(__('common.total_entries')); ?>)",
                    zeroRecords: "<?php echo e(__('common.no_matching_records_found')); ?>",
                    emptyTable: "<?php echo e(__('common.no_data_available')); ?>",
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
                    }
                },
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-bordered');
                }
            });

            // Search button
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val(null).trigger('change');
                $('#created_from_filter').val('');
                $('#created_until_filter').val('');
                $('#start_date_filter').val('');
                $('#end_date_filter').val('');
                table.ajax.reload();
                // Clear URL params
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Entries per page
            $('#entriesSelect').on('change', function() {
                per_page = $(this).val();
                table.page.len(per_page).draw();
            });

            // Function to update URL params
            function updateUrlParams() {
                const params = new URLSearchParams();

                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#created_from_filter').val()) params.set('created_from', $('#created_from_filter').val());
                if ($('#created_until_filter').val()) params.set('created_until', $('#created_until_filter').val());
                if ($('#start_date_filter').val()) params.set('start_date', $('#start_date_filter').val());
                if ($('#end_date_filter').val()) params.set('end_date', $('#end_date_filter').val());

                const newUrl = params.toString() ?
                    `${window.location.pathname}?${params.toString()}` :
                    window.location.pathname;

                window.history.replaceState({}, '', newUrl);
            }

            // Live search with debounce for all filters
            let searchTimer;

            // Text search - live search on keyup
            $('#search').on('keyup', function(e) {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                    updateUrlParams();
                }, 500);
            });

            // Select filters - live search on change
            $('#active').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Date filters - live search on change
            $('#created_from_filter, #created_until_filter, #start_date_filter, #end_date_filter').on('change',
                function() {
                    table.ajax.reload();
                    updateUrlParams();
                });

            // Enter key to search immediately
            $('#search').on('keypress', function(e) {
                if (e.which === 13) {
                    clearTimeout(searchTimer);
                    table.ajax.reload();
                    updateUrlParams();
                }
            });

            // Status switcher
            $(document).on('change', '.status-switcher', function() {
                let switcher = $(this);
                let occasionId = switcher.data('id');
                let isActive = switcher.is(':checked') ? 1 : 0;

                // Show loading overlay
                LoadingOverlay.show();

                $.ajax({
                    url: '<?php echo e(route('admin.occasions.toggle-status', ':id')); ?>'.replace(':id',
                        occasionId),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        is_active: isActive
                    },
                    success: function(response) {
                        LoadingOverlay.hide();
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function(xhr) {
                        LoadingOverlay.hide();
                        switcher.prop('checked', !switcher.is(':checked'));
                        toastr.error(
                            '<?php echo e(trans('catalogmanagement::occasion.error_changing_status')); ?>'
                            );
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/CatalogManagement\resources/views/occasions/index.blade.php ENDPATH**/ ?>