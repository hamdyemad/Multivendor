<?php
    $user_type_id = auth()->user()->user_type_id;
    $user_type = auth()->user()->user_type->name;
    $vendor = auth()->user()->vendor;

    // Calculate withdraw statistics
    $totalNeeded = 0;
    $totalSentMoney = 0;
    $totalRemaining = 0;
    $ordersPrice = 0;
    $bnaiaBalance = 0;
    $totalVendorBalance = 0;

    if (isAdmin()) {
        // For admin: calculate totals for all vendors
        $allVendors = \Modules\Vendor\app\Models\Vendor::all();
        $allOrderProducts = \Modules\Order\app\Models\OrderProduct::all();
        
        // Calculate total orders price (total transactions)
        $ordersPrice = $allOrderProducts->sum(function($op) {
            return $op->price;
        });
        
        // Calculate bnaia commission (commission is stored as actual amount, not percentage)
        $bnaiaBalance = $allOrderProducts->sum('commission');
        
        // Total vendor balance (after commission)
        $totalVendorBalance = $ordersPrice - $bnaiaBalance;
        
        $totalNeeded = $allVendors->sum('total_balance');
        $totalSentMoney = \Modules\Withdraw\app\Models\Withdraw::where('status', 'accepted')->sum('sent_amount');
        $totalRemaining = $totalNeeded - $totalSentMoney;
    } else {
        // For vendor: calculate their own totals
        if (!$vendor) {
            $vendor = \Modules\Vendor\app\Models\Vendor::where('user_id', auth()->user()->vendor_id)->first();
        }
        
        if ($vendor) {
            // Get order products for this vendor
            $orderProducts = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendor->id)->get();
            
            // Calculate orders price (total transactions)
            $ordersPrice = $orderProducts->sum('price');
            
            // Calculate bnaia commission (commission is stored as actual amount, not percentage)
            $bnaiaBalance = $orderProducts->sum('commission');
            
            // Total vendor balance (after commission)
            $totalVendorBalance = $ordersPrice - $bnaiaBalance;
            
            $totalNeeded = $vendor->total_balance;
            $totalSentMoney = $vendor->total_sent;
            $totalRemaining = $vendor->total_remaining;
        }
    }
?>

<div class="col-12">
    <div class="col-12">
        
        <div class="card mb-2">
            <div class="card-body fw-bold">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
                    <?php echo e(trans('withdraw::withdraw.vendors_general_orders_data')); ?>

                <?php else: ?>
                    <?php echo e(trans('withdraw::withdraw.vendor_general_orders_data')); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e(number_format($ordersPrice, 2)); ?> <?php echo e(currency()); ?></h1>
                                <p><?php echo e(trans('withdraw::withdraw.total_transactions')); ?></p>
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
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e(number_format($bnaiaBalance, 2)); ?> <?php echo e(currency()); ?></h1>
                                <p><?php echo e(trans('withdraw::withdraw.bnaia_commission_from_transactions')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e(number_format($totalVendorBalance, 2)); ?> <?php echo e(currency()); ?></h1>
                                <p><?php echo e(trans('withdraw::withdraw.total_vendor_credit')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-secondary color-secondary">
                                    <i class="uil uil-money-bill-stack"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="card mb-2">
            <div class="card-body fw-bold">
                <?php echo e(trans('dashboard.vendors_withdraw_transactions')); ?>

            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e(number_format($totalVendorBalance, 2)); ?> <?php echo e(currency()); ?></h1>
                                <p><?php echo e(trans('withdraw::withdraw.total_balance_needed')); ?></p>
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
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e(number_format($totalSentMoney, 2)); ?> <?php echo e(currency()); ?></h1>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
                                    <p><?php echo e(trans('dashboard.Total Sent Money To Vendors')); ?></p>
                                <?php else: ?>
                                    <p><?php echo e(trans('dashboard.total_received_money')); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-export"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1><?php echo e(number_format($totalVendorBalance - $totalSentMoney, 2)); ?> <?php echo e(currency()); ?></h1>
                                <p><?php echo e(trans('dashboard.Total Vendor\'s Remaining')); ?></p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-secondary color-secondary">
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
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/withdraw-transactions.blade.php ENDPATH**/ ?>