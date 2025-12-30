
<?php $__env->startSection('title'); ?>
    <?php echo e(__('systemsetting::activity_log.activity_logs')); ?> | E-RAMO
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
                    ['title' => __('systemsetting::activity_log.activity_logs_management')],
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
                    ['title' => __('systemsetting::activity_log.activity_logs_management')],
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
                        <h4 class="mb-0 fw-500"><?php echo e(__('systemsetting::activity_log.activity_logs_management')); ?></h4>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info glowing-alert" role="alert">
                        <i class="uil uil-lightbulb-alt me-1"></i>
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
                                                placeholder="<?php echo e(__('systemsetting::activity_log.search_placeholder')); ?>"
                                                autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="action" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-bolt me-1"></i>
                                                <?php echo e(__('systemsetting::activity_log.action')); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="action">
                                                <option value=""><?php echo e(__('systemsetting::activity_log.all_actions')); ?></option>
                                                <option value="created"><?php echo e(__('systemsetting::activity_log.actions.created')); ?></option>
                                                <option value="updated"><?php echo e(__('systemsetting::activity_log.actions.updated')); ?></option>
                                                <option value="deleted"><?php echo e(__('systemsetting::activity_log.actions.deleted')); ?></option>
                                                <option value="login"><?php echo e(__('systemsetting::activity_log.actions.login')); ?></option>
                                                <option value="logout"><?php echo e(__('systemsetting::activity_log.actions.logout')); ?></option>
                                                <option value="restored"><?php echo e(__('systemsetting::activity_log.actions.restored')); ?></option>
                                                <option value="force_deleted"><?php echo e(__('systemsetting::activity_log.actions.force_deleted')); ?></option>
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

                                    <div class="col-md-12 d-flex align-items-center">                                        <button type="button" id="resetFilters"
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
                        <table id="activityLogsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center" style="width: 50px;"><span class="userDatatable-title">#</span></th>
                                    <th style="width: 40%;"><span class="userDatatable-title"><?php echo e(__('systemsetting::activity_log.details')); ?></span></th>
                                    <th style="width: 120px;"><span class="userDatatable-title"><?php echo e(__('systemsetting::activity_log.ip_address')); ?></span></th>
                                    <th style="width: 150px;"><span class="userDatatable-title"><?php echo e(__('common.created_at')); ?></span></th>
                                    <th style="width: 80px;"><span class="userDatatable-title"><?php echo e(__('common.actions')); ?></span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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
    <script>
        $(document).ready(function() {
            console.log('Activity logs page loaded, initializing DataTable...');

            let per_page = 10;

            // Server-side processing with pagination
            let table = $('#activityLogsDataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [[3, 'desc']], // Default order by created_at column (index 3) descending
                ajax: {
                    url: '<?php echo e(route('admin.system-settings.activity-logs.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        // Add filter parameters
                        d.action = $('#action').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.order_column = d.order[0].column;
                            d.order_dir = d.order[0].dir;
                        }
                        console.log('📤 Sending request:', d);
                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('📦 Data received from server:', json);
                        console.log('Total records:', json.total);
                        console.log('Filtered records:', json.recordsFiltered);
                        console.log('Current page:', json.current_page);

                        // Map backend response to DataTables format
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;

                        if (json.error) {
                            console.error('❌ Server returned error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        if (!json.data || json.data.length === 0) {
                            console.warn('⚠️ No data returned from server');
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ DataTables AJAX Error:', {
                            xhr: xhr,
                            error: error,
                            code: code
                        });
                        console.error('Response Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);
                        alert('Error loading data. Status: ' + xhr.status +
                            '. Check console for details.');
                    }
                },
                columns: [{
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return '<div class="userDatatable-content text-center">' + (meta.row + 1) + '</div>';
                        }
                    },
                    {
                        data: null,
                        name: 'details',
                        orderable: false,
                        render: function(data, type, row) {
                            const colors = {
                                'Created': 'success',
                                'Updated': 'warning',
                                'Deleted': 'danger',
                                'Login': 'info',
                                'Logout': 'secondary'
                            };
                            const actionColor = colors[row.action] || 'secondary';
                            let html = '<div class="userDatatable-content">';
                            html += '<div class="mb-1"><strong>' + (row.user_name || '-') + '</strong></div>';
                            html += '<div class="mb-1"><span class="badge badge-' + actionColor + ' badge-sm">' + row.action + '</span>';
                            html += ' <span class="text-muted">' + (row.model || '-') + '</span></div>';
                            if (row.description) {
                                html += '<div class="text-muted small" title="' + row.description + '">' + (row.description.length > 80 ? row.description.substring(0, 80) + '...' : row.description) + '</div>';
                            }
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        data: 'ip_address',
                        name: 'ip_address',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content"><code class="small">' + (data || '-') + '</code></div>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        render: function(data, type, row) {
                            return '<div class="userDatatable-content"><small>' + data + '</small></div>';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="orderDatatable_actions d-inline-flex gap-1">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('settings.logs.view')): ?>
                                    <a href="<?php echo e(url('admin/system-settings/activity-logs')); ?>/${row.id}"
                                    class="view btn btn-primary btn-sm table_action_father"
                                    title="<?php echo e(trans('common.view')); ?>">
                                        <i class="uil uil-eye table_action_icon"></i>
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
                dom: 'rt<"bottom"<"row"<"col-sm-6"i><"col-sm-6"p>>>',
                language: {
                    "lengthMenu": "<?php echo e(trans('common.show')); ?> _MENU_ <?php echo e(trans('common.entries')); ?>",
                    "info": "<?php echo e(trans('common.showing')); ?> _START_ <?php echo e(trans('common.to')); ?> _END_ <?php echo e(trans('common.of')); ?> _TOTAL_",
                    "search": "<?php echo e(trans('common.search')); ?>:",
                    "emptyTable": "<?php echo e(trans('systemsetting::activity_log.no_logs_found')); ?>",
                    "zeroRecords": "<?php echo e(trans('systemsetting::activity_log.no_logs_found')); ?>"
                }
            });

            // Filter events
            $('#search, #action, #created_date_from, #created_date_to').on('change keyup', function() {
                table.draw();
            });

            // Reset filters
            $('#resetFilters').click(function() {
                $('#search').val('');
                $('#action').val('');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                table.draw();
            });

            // Entries per page
            $('#entriesSelect').on('change', function() {
                per_page = $(this).val();
                table.page.len(per_page).draw();
            });

            // Export Excel
            $('#exportExcel').click(function() {
                alert('<?php echo e(trans('common.export_coming_soon')); ?>');
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/SystemSetting\resources/views/activity-log/index.blade.php ENDPATH**/ ?>