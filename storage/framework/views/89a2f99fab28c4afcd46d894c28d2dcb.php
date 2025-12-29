

<?php $__env->startSection('title', __('systemsetting::push-notification.all_notifications')); ?>

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
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => __('systemsetting::push-notification.all_notifications')],
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
                    ['title' => __('systemsetting::push-notification.all_notifications')],
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
                            <i class="uil uil-bell me-2"></i>
                            <?php echo e(__('systemsetting::push-notification.all_notifications')); ?>

                        </h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('push-notifications.create')): ?>
                        <a href="<?php echo e(route('admin.system-settings.push-notifications.create')); ?>" class="btn btn-primary btn-default btn-squared">
                            <i class="uil uil-plus me-1"></i>
                            <?php echo e(__('systemsetting::push-notification.send_notification')); ?>

                        </a>
                        <?php endif; ?>
                    </div>

                    
                    <div class="mb-25">
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search_input" placeholder="<?php echo e(__('systemsetting::push-notification.search_placeholder')); ?>" style="max-width: 300px;">
                            <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared">
                                <i class="uil uil-search m-0"></i>
                            </button>
                            <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared">
                                <i class="uil uil-redo m-0"></i>
                            </button>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <table id="notificationsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::push-notification.title')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('common.type')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::push-notification.recipients')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('systemsetting::push-notification.created_by')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('common.created_at')); ?></span></th>
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

    
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white">
                    <h5 class="modal-title">
                        <i class="uil uil-trash me-2"></i><?php echo e(__('common.delete')); ?>

                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="uil uil-exclamation-triangle text-danger" style="font-size: 48px;"></i>
                        <p class="mt-3 mb-0 fs-16"><?php echo e(__('systemsetting::push-notification.confirm_delete')); ?></p>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo e(__('common.cancel')); ?></button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="uil uil-trash me-1"></i><?php echo e(__('common.delete')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = $('#notificationsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.system-settings.push-notifications.datatable')); ?>',
                    data: function(d) {
                        d.search_text = $('#search_input').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title_display', name: 'title', orderable: false, searchable: false },
                    { data: 'type_badge', name: 'type', orderable: false, searchable: false },
                    { data: 'recipients_count', name: 'recipients_count', orderable: false, searchable: false },
                    { data: 'created_by_name', name: 'created_by', orderable: false, searchable: false },
                    { data: 'created_date', name: 'created_at', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    lengthMenu: "<?php echo e(__('common.show') ?? 'Show'); ?> _MENU_",
                    info: "<?php echo e(__('common.showing') ?? 'Showing'); ?> _START_ <?php echo e(__('common.to') ?? 'to'); ?> _END_ <?php echo e(__('common.of') ?? 'of'); ?> _TOTAL_ <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoEmpty: "<?php echo e(__('common.showing') ?? 'Showing'); ?> 0 <?php echo e(__('common.to') ?? 'to'); ?> 0 <?php echo e(__('common.of') ?? 'of'); ?> 0 <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoFiltered: "(<?php echo e(__('common.filtered_from') ?? 'filtered from'); ?> _MAX_ <?php echo e(__('common.total_entries') ?? 'total entries'); ?>)",
                    zeroRecords: "<?php echo e(__('systemsetting::messages.no_messages_found') ?? 'No messages found'); ?>",
                    emptyTable: "<?php echo e(__('systemsetting::messages.no_messages_found') ?? 'No messages found'); ?>",
                    loadingRecords: "<?php echo e(__('common.loading') ?? 'Loading'); ?>...",
                    processing: "<?php echo e(__('common.processing') ?? 'Processing'); ?>...",
                    search: "<?php echo e(__('common.search') ?? 'Search'); ?>:"
                },
                dom: 'lrtip',
                pageLength: 10
            });

            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#search_input').on('keypress', function(e) {
                if (e.which === 13) table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search_input').val('');
                table.ajax.reload();
            });

            // Delete
            let deleteId = null;
            $(document).on('click', '.btn-delete', function() {
                deleteId = $(this).data('id');
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });

            $('#confirmDeleteBtn').on('click', function() {
                if (!deleteId) return;

                const $btn = $(this);
                $btn.prop('disabled', true);

                $.ajax({
                    url: '<?php echo e(route('admin.system-settings.push-notifications.destroy', ':id')); ?>'.replace(':id', deleteId),
                    method: 'DELETE',
                    data: { _token: '<?php echo e(csrf_token()); ?>' },
                    success: function(response) {
                        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                        table.ajax.reload();
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || '<?php echo e(__('common.error_occurred')); ?>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        deleteId = null;
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/SystemSetting\resources/views/push-notifications/index.blade.php ENDPATH**/ ?>