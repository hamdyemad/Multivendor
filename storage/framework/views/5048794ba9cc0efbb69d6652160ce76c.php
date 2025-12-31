
<?php $__env->startSection('title', __('withdraw::withdraw.send_money')); ?>
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
                    ['title' => __('withdraw::withdraw.send_money'), 'url' => route('admin.sendMoney')],
                    ['title' => __('withdraw::withdraw.send_money')],
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
                    ['title' => __('withdraw::withdraw.send_money'), 'url' => route('admin.sendMoney')],
                    ['title' => __('withdraw::withdraw.send_money')],
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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            <?php echo e(__('withdraw::withdraw.send_money')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="sendMoneyForm" action="<?php echo e(route('admin.sendMoneyToVendorAction')); ?>" method="POST"
                            enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($category)): ?>
                                <?php echo method_field('PUT'); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="row">
                                
                                <div class="col-md-12 mb-25">
                                    <div class="form-group">
                                        <label class="mb-2">
                                            <?php echo e(__('withdraw::withdraw.select_vendor')); ?> <span class="text-danger">*</span>
                                        </label>
                                        <select required name="vendor_id" class="form-control form-select select2"
                                            id="vendor_select">
                                            <option value=""><?php echo e(__('withdraw::withdraw.select_vendor')); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($vendor->id); ?>">
                                                    <?php echo e($vendor->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-20">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body fw-bold" style="background-color: #0056b7; color: #fff">
                                                <?php echo e(__('withdraw::withdraw.vendor_general_orders_data')); ?>

                                            </div>
                                        </div>
                                        <div class="col-12" style="background-color: rgb(201, 201, 201); padding: 10px">
                                            <div class="row">
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="total_orders" class="protected-value">0.00</span> <?php echo e(currency()); ?></h1>
                                                                    <p style="font-size:11px"><?php echo e(__('withdraw::withdraw.total_vendor_transactions')); ?></p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div class="svg-icon order-bg-opacity-info color-info">
                                                                        <i class="uil uil-wallet"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="bnaia_balance" class="protected-value">0.00</span> <?php echo e(currency()); ?></h1>
                                                                    <p style="font-size:11px"><?php echo e(__('withdraw::withdraw.bnaia_commission_from_transactions')); ?>

                                                                    </p>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="vendor_balance_money" class="protected-value">0.00</span> <?php echo e(currency()); ?></h1>
                                                                    <p style="font-size:11px"><?php echo e(__('withdraw::withdraw.total_vendor_credit')); ?></p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div
                                                                        class="svg-icon order-bg-opacity-secondary color-secondary">
                                                                        <i class="uil uil-money-bill-stack"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-12">
                                        <div class="card" style="background-color: #0056b7; color: #fff; border-radius: 1px !important">
                                            <div class="card-body fw-bold">
                                                <?php echo e(__('withdraw::withdraw.vendors_withdraw_transactions')); ?>

                                            </div>
                                        </div>
                                        <div class="col-12" style="background-color: rgb(201, 201, 201); padding: 10px">
                                            <div class="row">
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="vendor_balance_after_sent_money" class="protected-value">0.00</span>
                                                                        <?php echo e(currency()); ?></h1>
                                                                    <p><?php echo e(__('withdraw::withdraw.total_balance_needed')); ?></p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div class="svg-icon order-bg-opacity-info color-info">
                                                                        <i class="uil uil-wallet"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="total_sent_money" class="protected-value">0.00</span> <?php echo e(currency()); ?></h1>
                                                                    <p><?php echo e(__('withdraw::withdraw.total_sent_money')); ?></p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div
                                                                        class="svg-icon order-bg-opacity-primary color-primary">
                                                                        <i class="uil uil-export"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="remaining_after_sent_money" class="protected-value">0.00</span> <?php echo e(currency()); ?>

                                                                    </h1>
                                                                    <p><?php echo e(__('withdraw::withdraw.total_remaining')); ?></p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div
                                                                        class="svg-icon order-bg-opacity-secondary color-secondary">
                                                                        <i class="uil uil-money-bill-stack"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-md-12 mb-10">
                                    <div class="form-group">
                                        <label class="mb-3">
                                            <?php echo e(__('withdraw::withdraw.enter_amount')); ?> <span class="text-danger">*</span> <span
                                                class="badge text-bg-secondary"
                                                style="background-color: #0056b7; border-radius: 5px"><span
                                                    id="amount_max_which_will_be_sent">0.00</span> <span
                                                    style="margin: 0px 4px"><?php echo e(currency()); ?></span></span>

                                            <span class="badge text-bg-secondary"
                                                style="background-color: #fa0000; border-radius: 5px"> <span
                                                    style="margin: 0px 3px"><?php echo e(__('withdraw::withdraw.waiting_approve')); ?> :</span> <span
                                                    id="waiting_approve_requests">0.000</span>
                                                <span style="margin: 0px 4px"><?php echo e(currency()); ?></span></span>
                                        </label>
                                        <input required type="text" class="form-control"
                                            placeholder="<?php echo e(__('withdraw::withdraw.example_amount')); ?>" name="sent_amount" id="sent_amount"
                                            value="<?php echo e(old('sent_amount')); ?>">
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <div class="form-group">
                                        <label class="mb-2">
                                            <?php echo e(__('withdraw::withdraw.upload_invoice')); ?> <span class="text-danger">*</span>
                                        </label><br>

                                        <img id="imagePreview" src="<?php echo e(asset('assets/img/empty_image.jpg')); ?>"
                                            alt="Preview"
                                            style="margin-top:10px; max-width:200px; border:1px solid #ddd; padding:5px; cursor: pointer;">

                                        <input required type="file" name="invoice" class="form-control"
                                            id="imageInput" accept="image/*">
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="<?php echo e(route('admin.category-management.categories.index')); ?>"
                                    class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> <?php echo e(__('withdraw::withdraw.cancel')); ?>

                                </a>
                                <button type="submit" id="submitBtn"
                                    class="btn btn-primary btn-default btn-squared text-capitalize"
                                    style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span><?php echo e(__('withdraw::withdraw.send_money_button')); ?></span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #0056b7; color: #fff">
                    <h5 class="modal-title d-flex align-items-center" id="confirmSubmitLabel" style="color: #fff">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo e(__('withdraw::withdraw.confirm_submission')); ?>

                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-0" style="font-size: 16px;"><?php echo e(__('withdraw::withdraw.are_you_sure_send_money')); ?>

                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal"><?php echo e(__('withdraw::withdraw.cancel')); ?></button>
                    <button type="button" id="confirmSubmitBtn" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i> <?php echo e(__('withdraw::withdraw.yes_send')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Amount Exceeded Alert Modal -->
    <div class="modal fade" id="amountExceededModal" tabindex="-1" aria-labelledby="amountExceededLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #dc3545;">
                    <h5 class="modal-title d-flex align-items-center" id="amountExceededLabel" style="color: #fff">
                        <i class="uil uil-exclamation-triangle me-2"></i> <?php echo e(__('withdraw::withdraw.amount_exceeded_title')); ?>

                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="uil uil-times-circle text-danger" style="font-size: 48px;"></i>
                    </div>
                    <p class="mb-2" style="font-size: 16px;"><?php echo e(__('withdraw::withdraw.amount_exceeds_maximum')); ?></p>
                    <p class="mb-0 fw-bold text-danger" style="font-size: 18px;">
                        <?php echo e(__('withdraw::withdraw.max_amount')); ?>: <span id="maxAmountDisplay">0</span> <?php echo e(currency()); ?>

                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                        <i class="uil uil-check me-1"></i> <?php echo e(__('common.ok')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // Global function to get max amount from the display - defined outside DOMContentLoaded
            function getMaxAmount() {
                var element = document.getElementById('amount_max_which_will_be_sent');
                if (!element) {
                    console.warn('amount_max_which_will_be_sent element not found');
                    return 0;
                }
                var maxAmountText = element.innerText || element.textContent || '0';
                return Number(maxAmountText.replace(/,/g, '')) || 0;
            }

            // Global variable to store modals
            var confirmModal = null;
            var amountExceededModal = null;

            // Function to show amount exceeded modal
            function showAmountExceededModal(maxAmount) {
                var maxDisplay = document.getElementById('maxAmountDisplay');
                if (maxDisplay) {
                    maxDisplay.innerText = maxAmount.toLocaleString();
                }
                if (amountExceededModal) {
                    amountExceededModal.show();
                }
            }

                var form = document.getElementById('sendMoneyForm');
                var submitBtn = document.getElementById('submitBtn');
                var confirmBtn = document.getElementById('confirmSubmitBtn');
                var sentAmountInput = document.getElementById('sent_amount');
                var imageInput = document.getElementById('imageInput');
                var imagePreview = document.getElementById('imagePreview');

                // Initialize modals
                var confirmModalEl = document.getElementById('confirmSubmitModal');
                var amountExceededModalEl = document.getElementById('amountExceededModal');
                
                if (confirmModalEl) {
                    confirmModal = new bootstrap.Modal(confirmModalEl);
                }
                if (amountExceededModalEl) {
                    amountExceededModal = new bootstrap.Modal(amountExceededModalEl);
                }

                // Format number input with commas and validate max amount
                if (sentAmountInput) {
                    sentAmountInput.addEventListener('input', function() {
                        var value = this.value.replace(/,/g, '');
                        if (value === '') return;

                        // Allow only numbers and decimal point
                        if (!/^\d*\.?\d*$/.test(value)) {
                            this.value = this.value.slice(0, -1);
                            return;
                        }

                        var numVal = parseFloat(value);
                        if (!isNaN(numVal)) {
                            // Check if exceeds max amount
                            var maxAmount = getMaxAmount();
                            if (maxAmount > 0 && numVal > maxAmount) {
                                showAmountExceededModal(maxAmount);
                                this.value = maxAmount.toLocaleString('en-US');
                                return;
                            }
                            
                            var parts = numVal.toString().split('.');
                            parts[0] = Number(parts[0]).toLocaleString('en-US');
                            this.value = parts.join('.');
                        }
                    });
                }

                // Image preview on change
                if (imageInput) {
                    imageInput.addEventListener('change', function(event) {
                        var file = event.target.files[0];
                        if (file) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                if (imagePreview) {
                                    imagePreview.src = e.target.result;
                                    imagePreview.style.display = 'block';
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }

                // Click on image to open file selector
                if (imagePreview) {
                    imagePreview.addEventListener('click', function() {
                        if (imageInput) {
                            imageInput.click();
                        }
                    });
                }

                // Validate amount before showing confirm modal
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        var enteredAmount = sentAmountInput ? Number(sentAmountInput.value.replace(/,/g, '')) || 0 : 0;
                        var maxAmount = getMaxAmount();
                        
                        // Only validate if max amount is set (vendor selected)
                        if (maxAmount > 0 && enteredAmount > maxAmount) {
                            showAmountExceededModal(maxAmount);
                            return;
                        }
                        
                        if (confirmModal) {
                            confirmModal.show();
                        } else {
                            // Fallback: submit directly if modal not available
                            form.submit();
                        }
                    });
                }

                // When user clicks Yes, Send
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function() {
                        // Remove commas before submitting
                        if (sentAmountInput) {
                            sentAmountInput.value = sentAmountInput.value.replace(/,/g, '');
                        }
                        if (confirmModal) {
                            confirmModal.hide();
                        }
                        if (form) {
                            form.submit();
                        }
                    });
                }

            // Get vendor balance function
            function getVendorBalance(vendor_id) {
                if (!vendor_id) return;
                
                var url = "<?php echo e(route('admin.getVendorBalance', ':vendor_id')); ?>";
                url = url.replace(':vendor_id', vendor_id);
                
                console.log('Fetching vendor balance from:', url);
                
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log('Vendor balance response:', response);
                        
                        $("#total_orders").html(response.orders_price || '0.00');
                        $("#bnaia_balance").html(response.bnaia_balance || '0.00');
                        $("#vendor_commission_percentage").html((response.vendor_commission || 0) + "%");
                        $("#vendor_balance_money").html(response.total_vendor_balance || '0.00');

                        $("#vendor_balance_after_sent_money").html(response.total_vendor_balance || '0.00');
                        $("#total_sent_money").html(response.total_sent_money || '0.00');
                        $("#remaining_after_sent_money").html(response.remaining || '0.00');

                        $("#amount_max_which_will_be_sent").html(response.remaining || '0.00');
                        $("#waiting_approve_requests").html(response.waiting_approve_requests || '0.00');
                        
                        // Store the new values in protected badges
                        if (window.storeProtectedValue) {
                            window.storeProtectedValue('total_orders', response.orders_price);
                            window.storeProtectedValue('bnaia_balance', response.bnaia_balance);
                            window.storeProtectedValue('vendor_balance_money', response.total_vendor_balance);
                            window.storeProtectedValue('vendor_balance_after_sent_money', response.total_vendor_balance);
                            window.storeProtectedValue('total_sent_money', response.total_sent_money);
                            window.storeProtectedValue('remaining_after_sent_money', response.remaining);
                            window.storeProtectedValue('amount_max_which_will_be_sent', response.remaining);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching vendor balance:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);
                    }
                });
            }

            $('#vendor_select').on('change', function() {
                getVendorBalance($(this).val());
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Withdraw\resources/views/send_money.blade.php ENDPATH**/ ?>