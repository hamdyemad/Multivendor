

<?php $__env->startSection('title', __('accounting.expense_records')); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="crm mb-25">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="uil uil-estate"></i><?php echo e(__('accounting.dashboard')); ?></a></li>
                                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.accounting.summary')); ?>"><?php echo e(__('accounting.accounting')); ?></a></li>
                                <li class="breadcrumb-item active"><?php echo e(__('accounting.expense_records')); ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500 fw-bold"><?php echo e(__('accounting.expense_records')); ?></h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-default btn-squared text-capitalize" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                <i class="uil uil-plus"></i> <?php echo e(__('accounting.add_expense')); ?>

                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> <?php echo e(__('accounting.search')); ?>

                                            </label>
                                            <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="search" placeholder="<?php echo e(__('accounting.search_expenses')); ?>..." autocomplete="off">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="expense-item-filter" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-tag me-1"></i> <?php echo e(__('accounting.category')); ?>

                                            </label>
                                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select" id="expense-item-filter">
                                                <option value=""><?php echo e(__('accounting.all_categories')); ?></option>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $expenseItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date-from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> <?php echo e(__('accounting.date_from')); ?>

                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date-from">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date-to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> <?php echo e(__('accounting.date_to')); ?>

                                            </label>
                                            <input type="date" class="form-control ih-medium ip-gray radius-xs b-light px-15" id="date-to">
                                        </div>
                                    </div>

                                    <div class="col-md-2 d-flex align-items-center gap-2">
                                        <button type="button" id="searchBtn" class="btn btn-success btn-default btn-squared me-1">
                                            <i class="uil uil-search me-1"></i> <?php echo e(__('accounting.search')); ?>

                                        </button>
                                        <button type="button" id="resetFilters" class="btn btn-warning btn-default btn-squared">
                                            <i class="uil uil-redo me-1"></i> <?php echo e(__('accounting.reset')); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0"><?php echo e(__('accounting.show')); ?></label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <label class="ms-2 mb-0"><?php echo e(__('accounting.entries')); ?></label>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <table id="expensesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('accounting.category')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('accounting.amount')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('accounting.description')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('accounting.expense_date')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('accounting.receipt')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('accounting.created')); ?></span></th>
                                    <th><span class="userDatatable-title"><?php echo e(__('accounting.actions')); ?></span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseModalLabel"><?php echo e(__('accounting.add_expense')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo e(__('accounting.close')); ?>"></button>
            </div>
            <form method="POST" id="addExpenseForm" action="<?php echo e(route('admin.accounting.expenses.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="alert-container"></div>
                    <div class="mb-3">
                        <label for="expense_item_id" class="form-label"><?php echo e(__('accounting.category')); ?></label>
                        <select class="form-control" id="expense_item_id" name="expense_item_id">
                            <option value=""><?php echo e(__('accounting.select_category')); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $expenseItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label"><?php echo e(__('accounting.amount')); ?> <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label"><?php echo e(__('accounting.description')); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="expense_date" class="form-label"><?php echo e(__('accounting.expense_date')); ?> <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="receipt_file" class="form-label"><?php echo e(__('accounting.receipt_file')); ?></label>
                        <input type="file" class="form-control" id="receipt_file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('accounting.cancel')); ?></button>
                    <button type="submit" id="addExpenseBtn" class="btn btn-primary">
                        <i class="uil uil-check"></i>
                        <span><?php echo e(__('accounting.create_expense')); ?></span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseModalLabel"><?php echo e(__('accounting.edit_expense')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo e(__('accounting.close')); ?>"></button>
            </div>
            <form method="POST" id="editExpenseForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="alert-container"></div>
                    <div class="mb-3">
                        <label for="edit_expense_item_id" class="form-label"><?php echo e(__('accounting.category')); ?></label>
                        <select class="form-control" id="edit_expense_item_id" name="expense_item_id">
                            <option value=""><?php echo e(__('accounting.select_category')); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $expenseItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_amount" class="form-label"><?php echo e(__('accounting.amount')); ?> <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label"><?php echo e(__('accounting.description')); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_expense_date" class="form-label"><?php echo e(__('accounting.expense_date')); ?> <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_expense_date" name="expense_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_receipt_file" class="form-label"><?php echo e(__('accounting.receipt_file')); ?></label>
                        <input type="file" class="form-control" id="edit_receipt_file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('accounting.cancel')); ?></button>
                    <button type="submit" id="editExpenseBtn" class="btn btn-primary">
                        <i class="uil uil-check"></i>
                        <span><?php echo e(__('accounting.update_expense')); ?></span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if (isset($component)) { $__componentOriginal4d4be0bcf29da35c820833c3b98d2b58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4d4be0bcf29da35c820833c3b98d2b58 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-with-loading','data' => ['modalId' => 'modal-delete-expense','tableId' => 'expensesDataTable','deleteButtonClass' => 'delete-expense','title' => __('accounting.confirm_delete'),'message' => __('accounting.are_you_sure_delete_expense'),'itemNameId' => 'delete-expense-name','confirmBtnId' => 'confirmDeleteExpenseBtn','cancelText' => __('accounting.cancel'),'deleteText' => __('accounting.delete'),'loadingDeleting' => __('accounting.deleting'),'loadingPleaseWait' => __('accounting.please_wait'),'loadingDeletedSuccessfully' => __('accounting.expense_deleted_successfully'),'loadingRefreshing' => __('accounting.refreshing'),'errorDeleting' => __('accounting.error_deleting_expense')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-with-loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-expense','tableId' => 'expensesDataTable','deleteButtonClass' => 'delete-expense','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.confirm_delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.are_you_sure_delete_expense')),'itemNameId' => 'delete-expense-name','confirmBtnId' => 'confirmDeleteExpenseBtn','cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.cancel')),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.delete')),'loadingDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.deleting')),'loadingPleaseWait' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.please_wait')),'loadingDeletedSuccessfully' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.expense_deleted_successfully')),'loadingRefreshing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.refreshing')),'errorDeleting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('accounting.error_deleting_expense'))]); ?>
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
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            let per_page = 10;

            let table = $('#expensesDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('admin.accounting.expenses.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        d.search = $('#search').val();
                        d.expense_item_id = $('#expense-item-filter').val();
                        d.date_from = $('#date-from').val();
                        d.date_to = $('#date-to').val();
                        return d;
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'category', name: 'category' },
                    { data: 'amount', name: 'amount' },
                    { data: 'description', name: 'description' },
                    { data: 'expense_date', name: 'expense_date' },
                    { data: 'receipt', name: 'receipt', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                pageLength: per_page,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[3, 'desc']],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                     '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function(settings) {
                    bindEditEvents();
                }
            });

            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('#searchBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#expense-item-filter, #date-from, #date-to').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function() {
                $('#search').val('');
                $('#expense-item-filter').val('');
                $('#date-from').val('');
                $('#date-to').val('');
                table.ajax.reload();
            });

            // AJAX form handling for add expense
            $('#addExpenseForm').on('submit', function(e) {
                e.preventDefault();
                handleFormSubmission(this, '#addExpenseBtn', '#addExpenseModal');
            });

            // AJAX form handling for edit expense
            $('#editExpenseForm').on('submit', function(e) {
                e.preventDefault();
                handleFormSubmission(this, '#editExpenseBtn', '#editExpenseModal');
            });

            function handleFormSubmission(form, btnSelector, modalSelector) {
                const submitBtn = $(btnSelector);
                const modal = $(modalSelector);
                
                // Disable submit button and show loading
                submitBtn.prop('disabled', true);
                const btnIcon = submitBtn.find('i');
                const btnText = submitBtn.find('span:not(.spinner-border)');
                btnIcon.addClass('d-none');
                btnText.addClass('d-none');
                submitBtn.find('.spinner-border').removeClass('d-none');

                // Clear previous validation errors
                $(form).find('.is-invalid').removeClass('is-invalid');
                $(form).find('.invalid-feedback').remove();

                // Show loading overlay
                LoadingOverlay.show();

                // Start progress bar animation
                LoadingOverlay.animateProgressBar(30, 300).then(() => {
                    const formData = new FormData(form);
                    
                    return fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                })
                .then(response => {
                    LoadingOverlay.animateProgressBar(60, 200);
                    
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
                })
                .then(data => {
                    return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                        LoadingOverlay.showSuccess(
                            data.message,
                            '<?php echo e(__('accounting.refreshing')); ?>'
                        );

                        setTimeout(() => {
                            LoadingOverlay.hide();
                            modal.modal('hide');
                            table.ajax.reload();
                            form.reset();
                        }, 1500);
                    });
                })
                .catch(error => {
                    LoadingOverlay.hide();
                    
                    // Clear previous alerts
                    $(form).find('.alert-container').empty();
                    
                    // Handle validation errors
                    if (error.errors) {
                        Object.keys(error.errors).forEach(key => {
                            const input = $(form).find(`[name="${key}"]`);
                            if (input.length) {
                                input.addClass('is-invalid');
                                const feedback = $('<div class="invalid-feedback d-block"></div>').text(error.errors[key][0]);
                                input.parent().append(feedback);
                            }
                        });
                    } else {
                        // Show error alert at top of form
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <i class="uil uil-exclamation-triangle me-2"></i>${error.message || '<?php echo e(__('accounting.error_occurred')); ?>'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        $(form).find('.alert-container').html(alertHtml);
                    }
                    
                    // Re-enable submit button
                    submitBtn.prop('disabled', false);
                    btnIcon.removeClass('d-none');
                    btnText.removeClass('d-none');
                    submitBtn.find('.spinner-border').addClass('d-none');
                });
            }

            function bindEditEvents() {
                $('.edit').off('click').on('click', function() {
                    const id = $(this).data('id');
                    const expenseItemId = $(this).data('expense-item-id');
                    const amount = $(this).data('amount');
                    const description = $(this).data('description');
                    const expenseDate = $(this).data('expense-date');

                    $('#edit_expense_item_id').val(expenseItemId);
                    $('#edit_amount').val(amount);
                    $('#edit_description').val(description);
                    $('#edit_expense_date').val(expenseDate);

                    const form = $('#editExpenseForm');
                    const updateUrl = "<?php echo e(route('admin.accounting.expenses.update', ':id')); ?>".replace(':id', id);
                    form.attr('action', updateUrl);
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Accounting\resources/views/expenses.blade.php ENDPATH**/ ?>