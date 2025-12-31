
<?php $__env->startSection('title'); ?>
    <?php echo e(trans('shipping.shipping_management')); ?> | Bnaia
<?php $__env->stopSection(); ?>

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
                    ['title' => trans('shipping.shipping_management')],
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
                    ['title' => trans('shipping.shipping_management')],
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
                        <h4 class="mb-0 fw-500 fw-bold"><?php echo e(trans('shipping.shipping_management')); ?></h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.create')): ?>
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('admin.shippings.create')); ?>"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> <?php echo e(trans('shipping.create_shipping')); ?>

                            </a>
                        </div>
                        <?php endif; ?>
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
                                                placeholder="<?php echo e(__('common.search')); ?>..."
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(trans('shipping.status')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value=""><?php echo e(trans('common.all')); ?></option>
                                                <option value="1"><?php echo e(trans('shipping.active')); ?></option>
                                                <option value="0"><?php echo e(trans('shipping.inactive')); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_from_filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('shipping.created_from')); ?>

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
                                                <?php echo e(trans('shipping.created_until')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_until_filter">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
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
                        <table id="shippingsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.name')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.cost')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.status')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('shipping.created_at')); ?></span></th>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['modalId' => 'modal-delete-shipping','title' => trans('shipping.confirm_delete'),'message' => trans('shipping.delete_confirmation'),'itemNameId' => 'delete-shipping-name','confirmBtnId' => 'confirmDeleteShippingBtn','deleteRoute' => ''.e(rtrim(route('admin.shippings.index'), '/')).'','cancelText' => trans('main.cancel'),'deleteText' => trans('shipping.delete')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-shipping','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('shipping.confirm_delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('shipping.delete_confirmation')),'itemNameId' => 'delete-shipping-name','confirmBtnId' => 'confirmDeleteShippingBtn','deleteRoute' => ''.e(rtrim(route('admin.shippings.index'), '/')).'','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('main.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trans('shipping.delete'))]); ?>
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

    
    <div class="modal fade" id="modal-confirm-setting-change" tabindex="-1" aria-labelledby="settingChangeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle border-0">
                    <h5 class="modal-title" id="settingChangeModalLabel">
                        <i class="uil uil-exclamation-triangle text-warning me-2"></i>
                        <?php echo e(trans('shipping.confirm_setting_change')); ?>

                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="uil uil-info-circle text-warning" style="font-size: 48px;"></i>
                        </div>
                        <p class="mb-0 text-muted"><?php echo e(trans('shipping.setting_change_warning')); ?></p>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-light btn-default btn-squared" data-bs-dismiss="modal">
                        <?php echo e(trans('main.cancel')); ?>

                    </button>
                    <button type="button" class="btn btn-warning btn-default btn-squared" id="confirmSettingChangeBtn">
                        <i class="uil uil-check me-1"></i>
                        <?php echo e(trans('shipping.confirm_change')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            let per_page = 10;
            let pendingSwitcher = null;
            let pendingIsChecked = false;

            // Shipping Settings Switch Handler - Show confirmation first
            $('.shipping-setting-switch').on('change', function(e) {
                e.preventDefault();
                const switcher = $(this);
                const isChecked = switcher.is(':checked');
                
                // Revert the switch temporarily until confirmed
                switcher.prop('checked', !isChecked);
                
                // Store pending state
                pendingSwitcher = switcher;
                pendingIsChecked = isChecked;
                
                // Show confirmation modal
                $('#modal-confirm-setting-change').modal('show');
            });

            // Confirm setting change
            $('#confirmSettingChangeBtn').on('click', function() {
                if (!pendingSwitcher) return;
                
                // Apply the change
                pendingSwitcher.prop('checked', pendingIsChecked);
                
                // If turning on, turn off the others
                if (pendingIsChecked) {
                    $('.shipping-setting-switch').not(pendingSwitcher).prop('checked', false);
                }

                // Save settings via AJAX
                $.ajax({
                    url: '<?php echo e(route("admin.shippings.update-settings")); ?>',
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        shipping_allow_departments: $('#shipping_allow_departments').is(':checked') ? 1 : 0,
                        shipping_allow_categories: $('#shipping_allow_categories').is(':checked') ? 1 : 0,
                        shipping_allow_sub_categories: $('#shipping_allow_sub_categories').is(':checked') ? 1 : 0
                    },
                    success: function(response) {
                        $('#modal-confirm-setting-change').modal('hide');
                        if (response.success) {
                            toastr.success(response.message);
                            // Reload the datatable to reflect deleted shippings
                            table.ajax.reload();
                        } else {
                            toastr.error(response.message);
                            // Revert on error
                            pendingSwitcher.prop('checked', !pendingIsChecked);
                        }
                        pendingSwitcher = null;
                    },
                    error: function() {
                        $('#modal-confirm-setting-change').modal('hide');
                        toastr.error('<?php echo e(trans("shipping.error_saving_settings")); ?>');
                        // Revert on error
                        pendingSwitcher.prop('checked', !pendingIsChecked);
                        pendingSwitcher = null;
                    }
                });
            });

            // Cancel setting change
            $('#modal-confirm-setting-change').on('hidden.bs.modal', function() {
                pendingSwitcher = null;
                pendingIsChecked = false;
            });

            // Populate filters from URL parameters on page load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('created_from')) $('#created_from_filter').val(urlParams.get('created_from'));
            if (urlParams.has('created_until')) $('#created_until_filter').val(urlParams.get('created_until'));

            // Server-side processing with pagination
            let table = $('#shippingsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.shippings.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.active = $('#active').val() !== '' ? $('#active').val() : null;
                        d.created_date_from = $('#created_from_filter').val();
                        d.created_date_to = $('#created_until_filter').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'cost',
                        name: 'cost',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<?php echo e(currency()); ?> ' + parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.change-status')): ?>
                            const isChecked = data === 1 || data === true ? 'checked' : '';
                            const switchId = 'status-switch-' + row.id;
                            return `
                                <div class="form-check form-switch  form-switch-primary form-switch-sm d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input status-switcher" 
                                        id="${switchId}" data-id="${row.id}" ${isChecked}>
                                    <label class="form-check-label" for="${switchId}"></label>
                                </div>
                            `;
                            <?php else: ?>
                            if (data === 1 || data === true) {
                                return '<span class="badge badge-success badge-round badge-lg"><?php echo e(trans('shipping.active')); ?></span>';
                            } else {
                                return '<span class="badge badge-danger badge-round badge-lg"><?php echo e(trans('shipping.inactive')); ?></span>';
                            }
                            <?php endif; ?>
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let editUrl = "<?php echo e(route('admin.shippings.edit', ':id')); ?>".replace(':id', row.id);
                            let showUrl = "<?php echo e(route('admin.shippings.show', ':id')); ?>".replace(':id', row.id);
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.show')): ?>
                                    <a href="${showUrl}"
                                    class="view btn btn-primary table_action_father"
                                    title="<?php echo e(trans('common.view')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.edit')): ?>
                                    <a href="${editUrl}"
                                    class="edit btn btn-warning table_action_father"
                                    title="<?php echo e(trans('shipping.edit')); ?>">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('shippings.delete')): ?>
                                    <button type="button"
                                    class="btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-shipping"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.name}"
                                    title="<?php echo e(trans('shipping.delete')); ?>">
                                        <i class="uil uil-trash table_action_icon"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "<?php echo e(__('common.search')); ?>:",
                }
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Live search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                    updateUrlParams();
                }, 500);
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Filter change handlers
            $('#active, #created_from_filter, #created_until_filter').on('change', function() {
                table.ajax.reload();
                updateUrlParams();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#created_from_filter').val('');
                $('#created_until_filter').val('');
                table.ajax.reload();
                // Clear URL parameters
                window.history.replaceState({}, '', window.location.pathname);
            });

            // Update URL parameters function
            function updateUrlParams() {
                const params = new URLSearchParams();
                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#created_from_filter').val()) params.set('created_from', $('#created_from_filter').val());
                if ($('#created_until_filter').val()) params.set('created_until', $('#created_until_filter').val());

                const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }

            // Change shipping status
            $(document).on('change', '.status-switcher', function() {
                const switcher = $(this);
                const id = switcher.data('id');
                const newStatus = switcher.is(':checked') ? 1 : 0;

                switcher.prop('disabled', true);

                $.ajax({
                    url: "<?php echo e(route('admin.shippings.change-status', ':id')); ?>".replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        status: newStatus
                    },
                    success: function(response) {
                        switcher.prop('disabled', false);
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            switcher.prop('checked', !switcher.is(':checked'));
                        }
                    },
                    error: function() {
                        switcher.prop('disabled', false);
                        switcher.prop('checked', !switcher.is(':checked'));
                        toastr.error('<?php echo e(trans('shipping.error_changing_status')); ?>');
                    }
                });
            });

            // Delete shipping
            $(document).on('click', '#confirmDeleteShippingBtn', function() {
                let id = $(this).data('item-id');
                $.ajax({
                    url: "<?php echo e(route('admin.shippings.destroy', ':id')); ?>".replace(':id', id),
                    type: 'DELETE',
                    data: { _token: '<?php echo e(csrf_token()); ?>' },
                    success: function(response) {
                        $('#modal-delete-shipping').modal('hide');
                        toastr.success('<?php echo e(trans('shipping.deleted_successfully')); ?>');
                        table.ajax.reload();
                    },
                    error: function() {
                        $('#modal-delete-shipping').modal('hide');
                        toastr.error('<?php echo e(trans('shipping.error_deleting')); ?>');
                    }
                });
            });

            // Set item data when modal is shown
            $('#modal-delete-shipping').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const itemId = button.data('item-id');
                const itemName = button.data('item-name');
                
                $('#delete-shipping-name').text(itemName);
                $('#confirmDeleteShippingBtn').data('item-id', itemId);
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Order\resources/views/shippings/index.blade.php ENDPATH**/ ?>