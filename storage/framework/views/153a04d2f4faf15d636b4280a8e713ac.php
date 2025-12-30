
<?php $__env->startSection('title', trans('admin.vendor_users_management')); ?>

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
                    ['title' => __('admin.vendor_users_management')],
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
                    ['title' => __('admin.vendor_users_management')],
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
                        <h4 class="mb-0 fw-500"><?php echo e(__('admin.vendor_users_management')); ?></h4>
                        <div class="d-flex gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-users.create')): ?>
                                <a href="<?php echo e(route('admin.vendor-users-management.vendor-users.create')); ?>"
                                    class="btn btn-primary btn-default btn-squared text-capitalize">
                                    <i class="uil uil-plus"></i> <?php echo e(__('admin.add_vendor_user')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
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
                                                <i class="uil uil-search me-1"></i> <?php echo e(trans('common.search')); ?>

                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="<?php echo e(trans('admin.search_placeholder')); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(trans('admin.status')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value=""><?php echo e(trans('admin.all_status')); ?></option>
                                                <option value="1"><?php echo e(trans('admin.active')); ?></option>
                                                <option value="0"><?php echo e(trans('admin.inactive')); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vendor_id" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-store me-1"></i>
                                                <?php echo e(trans('admin.vendor')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select select2"
                                                id="vendor_id">
                                                <option value=""><?php echo e(trans('admin.all_vendors')); ?></option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \Modules\Vendor\app\Models\Vendor::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($vendor->id); ?>">
                                                        <?php echo e($vendor->getTranslation('name', app()->getLocale())); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(trans('common.created_date_from')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_from">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex">
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

                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0"><?php echo e(trans('common.show')); ?></label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0"><?php echo e(trans('common.entries')); ?></label>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <table id="vendorUsersDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('admin.information')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('admin.email')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('admin.vendor')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('admin.role')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('admin.active')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('admin.block')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('admin.created_at')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(trans('common.actions')); ?></span></th>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['modalId' => 'modal-delete-vendor-user','title' => __('admin.confirm_delete'),'message' => __('admin.delete_confirmation'),'itemNameId' => 'delete-vendor-user-name','confirmBtnId' => 'confirmDeleteVendorUserBtn','deleteRoute' => route('admin.vendor-users-management.vendor-users.index'),'cancelText' => __('admin.cancel'),'deleteText' => __('admin.delete_user')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-vendor-user','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.confirm_delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.delete_confirmation')),'itemNameId' => 'delete-vendor-user-name','confirmBtnId' => 'confirmDeleteVendorUserBtn','deleteRoute' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.vendor-users-management.vendor-users.index')),'cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('admin.delete_user'))]); ?>
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
            let table = $('#vendorUsersDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.vendor-users-management.vendor-users.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.active = $('#active').val();
                        d.vendor_id = $('#vendor_id').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        return d;
                    },
                    dataSrc: function(json) {
                        // Use the correct total from backend
                        json.recordsTotal = json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || 0;

                        if (json.error) {
                            console.error('❌ Server returned error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ DataTables AJAX Error:', {
                            xhr: xhr,
                            error: error,
                            code: code
                        });
                        alert('Error loading data. Status: ' + xhr.status);
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                    },
                    {
                        data: 'image',
                        name: 'information',
                        orderable: false,
                        render: function(data, type, row) {
                            let img;
                            if (data) {
                                img =
                                    `<img src="<?php echo e(asset('storage')); ?>/${data}" class="rounded-circle" style="width: 40px; height: 40px;">`;
                            } else {
                                img =
                                    `<img src="<?php echo e(asset('assets/img/default.png')); ?>" class="rounded-circle" style="width: 40px; height: 40px;">`;
                            }

                            let names = '';
                            Object.values(row.names).forEach(name => {
                                const badgeClass = name.code === 'ar' ? 'bg-info' :
                                    'bg-primary';
                                names += `
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge ${badgeClass} text-white px-1 py-0 me-1" style="font-size: 10px; text-transform: uppercase;">${name.code}</span>
                                        <div class="userDatatable-content" ${name.rtl ? 'dir="rtl"' : ''} style="font-size: 13px; line-height: 1.2;">${name.value || '-'}</div>
                                    </div>`;
                            });

                            return `
                                <div class="d-flex align-items-center gap-10">
                                    ${img}
                                    <div>${names}</div>
                                </div>`;
                        }
                    }, {
                        data: 'email',
                        name: 'email',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content text-lowercase">' + data +
                                '</div>';
                        }
                    },
                    {
                        data: 'vendor',
                        name: 'vendor',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        render: function(data, type, row) {
                            const checked = data ? 'checked' : '';
                            const field = `
                                    <div class="userDatatable-content">
                                        <div class="form-check form-switch form-switch-primary form-switch-md">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                data-id="${row.id}" data-type="active" ${checked}>
                                        </div>
                                    </div>`;
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-users.change-status')): ?>
                                return field
                            <?php else: ?>
                                const badge = data 
                                    ? '<span class="badge badge-round badge-lg badge-success"><?php echo e(__("admin.active")); ?></span>' 
                                    : '<span class="badge badge-round badge-lg badge-danger"><?php echo e(__("admin.inactive")); ?></span>';
                                return `<div class="userDatatable-content">${badge}</div>`;
                            <?php endif; ?>
                        }
                    },
                    {
                        data: 'block',
                        name: 'block',
                        orderable: false,
                        render: function(data, type, row) {
                            const checked = data ? 'checked' : '';
                            const field = `
                                    <div class="userDatatable-content">
                                        <div class="form-check form-switch form-switch-danger form-switch-md">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                data-id="${row.id}" data-type="block" ${checked}>
                                        </div>
                                    </div>`;
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-users.change-status')): ?>
                                return field
                            <?php else: ?>
                                const badge = data 
                                    ? '<span class="badge badge-round badge-lg badge-danger"><?php echo e(__("admin.blocked")); ?></span>' 
                                    : '<span class="badge badge-round badge-lg badge-success"><?php echo e(__("admin.not_blocked")); ?></span>';
                                return `<div class="userDatatable-content">${badge}</div>`;
                            <?php endif; ?>
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content">' + data + '</div>';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-users.show')): ?>
                                    <a href="<?php echo e(route('admin.vendor-users-management.vendor-users.index')); ?>/${row.id}"
                                    class="view btn btn-primary table_action_father"
                                    title="<?php echo e(trans('common.view')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-users.edit')): ?>
                                    <a href="<?php echo e(route('admin.vendor-users-management.vendor-users.index')); ?>/${row.id}/edit"
                                    class="edit btn btn-warning table_action_father"
                                    title="<?php echo e(trans('common.edit')); ?>">
                                        <i class="uil uil-edit table_action_icon"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendor-users.delete')): ?>
                                    <a href="javascript:void(0);"
                                    class="remove delete-vendor-user btn btn-danger table_action_father"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-delete-vendor-user"
                                    data-item-id="${row.id}"
                                    data-item-name="${row.display_name}"
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
                order: [
                    [0, 'desc']
                ],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                searching: true,
                language: {
                    lengthMenu: "<?php echo e(__('common.show') ?? 'Show'); ?> _MENU_",
                    info: "<?php echo e(__('common.showing') ?? 'Showing'); ?> _START_ <?php echo e(__('common.to') ?? 'to'); ?> _END_ <?php echo e(__('common.of') ?? 'of'); ?> _TOTAL_ <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoEmpty: "<?php echo e(__('common.showing') ?? 'Showing'); ?> 0 <?php echo e(__('common.to') ?? 'to'); ?> 0 <?php echo e(__('common.of') ?? 'of'); ?> 0 <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoFiltered: "(<?php echo e(__('common.filtered_from') ?? 'filtered from'); ?> _MAX_ <?php echo e(__('common.total_entries') ?? 'total entries'); ?>)",
                    zeroRecords: "<?php echo e(__('admin.no_users_found') ?? 'No users found'); ?>",
                    emptyTable: "<?php echo e(__('admin.no_users_found') ?? 'No users found'); ?>",
                    loadingRecords: "<?php echo e(__('common.loading') ?? 'Loading'); ?>...",
                    processing: "<?php echo e(__('common.processing') ?? 'Processing'); ?>...",
                    search: "<?php echo e(__('common.search') ?? 'Search'); ?>:",
                    paginate: {
                        first: '<?php echo e(__('common.first') ?? 'First'); ?>',
                        last: '<?php echo e(__('common.last') ?? 'Last'); ?>',
                        next: '<?php echo e(__('common.next') ?? 'Next'); ?>',
                        previous: '<?php echo e(__('common.previous') ?? 'Previous'); ?>'
                    }
                }
            });

            // Status Toggle
            $(document).on('change', '.status-toggle', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                const status = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: `<?php echo e(route('admin.vendor-users-management.vendor-users.index')); ?>/${id}/change-status`,
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        status: status,
                        type: type
                    },
                    success: function(response) {
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                const searchValue = $(this).val();
                searchTimer = setTimeout(function() {
                    table.search(searchValue).draw();
                }, 500);
            });

            // Filters
            $('#active, #vendor_id').on('change', function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#active').val('');
                $('#vendor_id').val('').trigger('change');
                $('#created_date_from').val('');
                table.search('').ajax.reload();
            });

            // Delete Modal
            let deleteItemId;
            $(document).on('click', '.delete-vendor-user', function() {
                deleteItemId = $(this).data('item-id');
                $('#delete-vendor-user-name').text($(this).data('item-name'));
            });

            $('#confirmDeleteVendorUserBtn').on('click', function() {
                $.ajax({
                    url: `<?php echo e(route('admin.vendor-users-management.vendor-users.index')); ?>/${deleteItemId}`,
                    type: 'DELETE',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        $('#modal-delete-vendor-user').modal('hide');
                        toastr.success(response.message);
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/vendor_users_management/vendor_user/index.blade.php ENDPATH**/ ?>