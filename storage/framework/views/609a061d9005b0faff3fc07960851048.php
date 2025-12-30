

<?php $__env->startSection('title', __('accounting.accounting_summary')); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .vendor-logos {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vendor-logo-wrapper {
            position: relative;
            display: inline-block;
            margin-left: -10px;
        }

        .vendor-logo-wrapper:first-child {
            margin-left: 0;
        }

        .vendor-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #fff;
            object-fit: cover;
            background-color: #f0f0f0;
            transition: transform 0.2s ease-in-out;
        }

        .vendor-logo-wrapper:hover .vendor-logo {
            transform: translateY(-5px);
        }

        .vendor-logo-wrapper .vendor-name-tooltip {
            visibility: hidden;
            width: max-content;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -50%;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            transform: translateY(10px);
        }

        .vendor-logo-wrapper:hover .vendor-name-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        /* Custom Tab Header Styling */
        .custom-tab-headers {
            display: flex;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 4px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .custom-tab-header {
            flex: 1;
            padding: 12px 20px;
            text-align: center;
            background: transparent;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .custom-tab-header:hover {
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
        }

        .custom-tab-header.active {
            background: #007bff;
            color: white;
            box-shadow: 0 2px 8px rgba(0,123,255,0.3);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <style>
        .ap-po-details__titlebar h1 {
            font-weight: bold;
            color: var(--color-primary);
        }

        .ap-po-details__titlebar p {
            font-weight: bold !important;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .metric-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .metric-item:last-child {
            border-bottom: none;
        }

        .metric-label {
            font-weight: 600;
            color: #666;
        }

        .metric-value {
            font-weight: bold;
            font-size: 1.1em;
        }

        .positive {
            color: #28a745;
        }

        .negative {
            color: #dc3545;
        }

        .neutral {
            color: #6c757d;
        }
    </style>

    <div class="crm mb-25">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <div class="breadcrumb-action justify-content-center flex-wrap">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><i
                                                class="uil uil-estate"></i><?php echo e(__('accounting.dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active"><?php echo e(__('accounting.accounting_summary')); ?></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                
                <div class="col-12">
                    
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> <?php echo e(__('accounting.date_from')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="date_from" name="date_from" value="<?php echo e(request('date_from')); ?>">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> <?php echo e(__('accounting.date_to')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="date_to" name="date_to" value="<?php echo e(request('date_to')); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="filterBtn"
                                            class="btn btn-success btn-default btn-squared me-1">
                                            <i class="uil uil-filter me-1"></i> <?php echo e(__('accounting.filter')); ?>

                                        </button>
                                        <button type="button" id="resetBtn"
                                            class="btn btn-warning btn-default btn-squared">
                                            <i class="uil uil-redo me-1"></i> <?php echo e(__('accounting.reset')); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="col-12 col-md-3 mb-25">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1 id="total-income"><?php echo e(number_format($summary['total_income'], 2)); ?>

                                                <?php echo e(currency()); ?></h1>
                                            <p><?php echo e(__('accounting.total_income')); ?></p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-primary color-primary">
                                                <i class="uil uil-money-dollar-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-12 col-md-3 mb-25">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1 id="total-expenses"><?php echo e(number_format($summary['total_expenses'], 2)); ?>

                                                <?php echo e(currency()); ?></h1>
                                            <p><?php echo e(__('accounting.total_expenses')); ?></p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-danger color-danger">
                                                <i class="uil uil-shopping-cart"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-12 col-md-3 mb-25">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1 id="total-commissions">
                                                <?php echo e(number_format($summary['total_commissions'], 2)); ?> <?php echo e(currency()); ?>

                                            </h1>
                                            <p><?php echo e(__('accounting.total_commissions')); ?></p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-info color-info">
                                                <i class="uil uil-percent"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-12 col-md-3 mb-25">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1 id="net-profit"><?php echo e(number_format($summary['net_profit'], 2)); ?>

                                                <?php echo e(currency()); ?></h1>
                                            <p><?php echo e(__('accounting.net_profit')); ?></p>
                                        </div>
                                        <div class="ap-po-details__icon-area">
                                            <div class="svg-icon order-bg-opacity-success color-success">
                                                <i class="uil uil-line-chart"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-12">
                    <div class="chart-container">
                        <h5 class="mb-4"><?php echo e(__('accounting.monthly_breakdown')); ?></h5>
                        <canvas id="monthlyChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>

                
                <div class="col-lg-12">
                    <div class="chart-container">
                        <div class="custom-tab-headers">
                            <a class="custom-tab-header active" data-bs-toggle="tab" href="#income_expense"><?php echo e(__('accounting.income_expense')); ?></a>
                            <a class="custom-tab-header" data-bs-toggle="tab" href="#cost_analysis"><?php echo e(__('accounting.cost_analysis')); ?></a>
                            <a class="custom-tab-header" data-bs-toggle="tab" href="#cash_flow"><?php echo e(__('accounting.cash_flow')); ?></a>
                            <a class="custom-tab-header" data-bs-toggle="tab" href="#orders"><?php echo e(__('accounting.orders')); ?></a>
                        </div>

                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="income_expense">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-bordered table-hover" style="width:100%">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th><span class="userDatatable-title"><?php echo e(__('accounting.category')); ?></span></th>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <th class="text-center"><span class="userDatatable-title"><?php echo e($month['name']); ?></span></th>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <th class="text-center"><span class="userDatatable-title"><?php echo e(__('accounting.total')); ?></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold"><?php echo e(__('accounting.income')); ?></td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center text-success">
                                                        <?php echo e(number_format($summary['monthly_data'][$month['key']]['income'] ?? 0, 2)); ?> <?php echo e(currency()); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <td class="text-center text-success fw-bold">
                                                    <?php echo e(number_format($summary['total_income'], 2)); ?> <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold"><?php echo e(__('accounting.commissions')); ?></td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center text-info">
                                                        <?php echo e(number_format($summary['monthly_data'][$month['key']]['commissions'] ?? 0, 2)); ?> <?php echo e(currency()); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <td class="text-center text-info fw-bold">
                                                    <?php echo e(number_format($summary['total_commissions'], 2)); ?> <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold"><?php echo e(__('accounting.expenses')); ?></td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center text-danger">
                                                        <?php echo e(number_format($summary['monthly_data'][$month['key']]['expenses'] ?? 0, 2)); ?> <?php echo e(currency()); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <td class="text-center text-danger fw-bold">
                                                    <?php echo e(number_format($summary['total_expenses'], 2)); ?> <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold"><?php echo e(__('accounting.withdraws')); ?></td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center text-warning">
                                                        <?php echo e(number_format($summary['monthly_data'][$month['key']]['withdraws'] ?? 0, 2)); ?> <?php echo e(currency()); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <td class="text-center text-warning fw-bold">
                                                    <?php echo e(number_format($summary['total_withdraws'] ?? 0, 2)); ?> <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                            <tr class="table-info">
                                                <td class="fw-bold"><?php echo e(__('accounting.net_profit')); ?></td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $monthProfit = ($summary['monthly_data'][$month['key']]['income'] ?? 0) + ($summary['monthly_data'][$month['key']]['commissions'] ?? 0) - ($summary['monthly_data'][$month['key']]['expenses'] ?? 0);
                                                    ?>
                                                    <td class="text-center fw-bold <?php echo e($monthProfit >= 0 ? 'text-success' : 'text-danger'); ?>">
                                                        <?php echo e(number_format($monthProfit, 2)); ?> <?php echo e(currency()); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <td class="text-center fw-bold <?php echo e(($summary['net_profit'] + $summary['total_commissions']) >= 0 ? 'text-success' : 'text-danger'); ?>">
                                                    <?php echo e(number_format($summary['net_profit'] + $summary['total_commissions'], 2)); ?> <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="cost_analysis">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-bordered table-hover" style="width:100%">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th><span class="userDatatable-title"><?php echo e(__('accounting.expense_category')); ?></span></th>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <th class="text-center"><span class="userDatatable-title"><?php echo e($month['name']); ?></span></th>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <th class="text-center"><span class="userDatatable-title"><?php echo e(__('accounting.total')); ?></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($summary['expense_categories'])): ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $summary['expense_categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="fw-bold"><?php echo e($category['name']); ?></td>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <td class="text-center">
                                                                <?php echo e(number_format($category['monthly'][$month['key']] ?? 0, 2)); ?> <?php echo e(currency()); ?>

                                                            </td>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <td class="text-center fw-bold">
                                                            <?php echo e(number_format(array_sum($category['monthly']), 2)); ?> <?php echo e(currency()); ?>

                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="<?php echo e(count($monthHeaders) + 2); ?>" class="text-center"><?php echo e(__('accounting.no_expense_data')); ?></td>
                                                </tr>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="cash_flow">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-bordered table-hover" style="width:100%">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th><span class="userDatatable-title"><?php echo e(__('accounting.source')); ?></span></th>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <th class="text-center"><span class="userDatatable-title"><?php echo e($month['name']); ?></span></th>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <th class="text-center"><span class="userDatatable-title"><?php echo e(__('accounting.total')); ?></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-success"><?php echo e(__('accounting.sales_revenue')); ?></td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center text-success">
                                                        <?php echo e(number_format($summary['monthly_data'][$month['key']]['income'] ?? 0, 2)); ?> <?php echo e(currency()); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <td class="text-center text-success fw-bold">
                                                    <?php echo e(number_format($summary['total_income'], 2)); ?> <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-danger"><?php echo e(__('accounting.operating_expenses')); ?></td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center text-danger">
                                                        -<?php echo e(number_format($summary['monthly_data'][$month['key']]['expenses'] ?? 0, 2)); ?> <?php echo e(currency()); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <td class="text-center text-danger fw-bold">
                                                    -<?php echo e(number_format($summary['total_expenses'], 2)); ?> <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                            <tr class="table-info">
                                                <td class="fw-bold"><?php echo e(__('accounting.net_flow')); ?></td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $monthFlow = ($summary['monthly_data'][$month['key']]['income'] ?? 0) - ($summary['monthly_data'][$month['key']]['expenses'] ?? 0);
                                                    ?>
                                                    <td class="text-center fw-bold <?php echo e($monthFlow >= 0 ? 'text-success' : 'text-danger'); ?>">
                                                        <?php echo e(number_format($monthFlow, 2)); ?> <?php echo e(currency()); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <td class="text-center fw-bold <?php echo e($summary['net_profit'] >= 0 ? 'text-success' : 'text-danger'); ?>">
                                                    <?php echo e(number_format($summary['net_profit'], 2)); ?> <?php echo e(currency()); ?>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="orders">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <label class="me-2 mb-0"><?php echo e(__('common.show')); ?></label>
                                        <select id="ordersEntriesSelect" class="form-select form-select-sm"
                                            style="width: auto;">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <label class="ms-2 mb-0"><?php echo e(__('common.entries')); ?></label>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="ordersDataTable" class="table mb-0 table-bordered table-hover"
                                        style="width:100%">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th class="text-center"><span class="userDatatable-title">#</span></th>
                                                <th><span
                                                        class="userDatatable-title"><?php echo e(trans('order::order.order_information')); ?></span>
                                                </th>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isAdmin()): ?>
                                                    <th><span
                                                            class="userDatatable-title"><?php echo e(trans('order::order.vendor')); ?></span>
                                                    </th>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <th><span
                                                        class="userDatatable-title"><?php echo e(trans('order::order.total_price')); ?></span>
                                                </th>
                                                <th><span
                                                        class="userDatatable-title"><?php echo e(trans('order::order.stage')); ?></span>
                                                </th>
                                                <th><span
                                                        class="userDatatable-title"><?php echo e(trans('order::order.created_at')); ?></span>
                                                </th>
                                                <th><span class="userDatatable-title"><?php echo e(__('common.actions')); ?></span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-8">
                    <div class="chart-container">
                        <h5 class="mb-4"><?php echo e(__('accounting.financial_breakdown')); ?></h5>

                        <div class="metric-item">
                            <span class="metric-label"><?php echo e(__('accounting.gross_revenue')); ?></span>
                            <span class="metric-value positive"
                                id="gross-revenue"><?php echo e(number_format($summary['total_income'], 2)); ?>

                                <?php echo e(currency()); ?></span>
                        </div>

                        <div class="metric-item">
                            <span class="metric-label"><?php echo e(__('accounting.operating_expenses')); ?></span>
                            <span class="metric-value negative"
                                id="operating-expenses"><?php echo e(number_format($summary['total_expenses'], 2)); ?>

                                <?php echo e(currency()); ?></span>
                        </div>

                        <div class="metric-item">
                            <span class="metric-label"><?php echo e(__('accounting.commission_earned')); ?></span>
                            <span class="metric-value positive"
                                id="commission-earned"><?php echo e(number_format($summary['total_commissions'], 2)); ?>

                                <?php echo e(currency()); ?></span>
                        </div>

                        <div class="metric-item">
                            <span class="metric-label"><?php echo e(__('accounting.vendor_payouts')); ?></span>
                            <span class="metric-value neutral"
                                id="vendor-payouts"><?php echo e(number_format($summary['total_income'] - $summary['total_commissions'], 2)); ?>

                                <?php echo e(currency()); ?></span>
                        </div>

                        <div class="metric-item">
                            <span class="metric-label"><?php echo e(__('accounting.profit_margin')); ?></span>
                            <span
                                class="metric-value <?php echo e($summary['total_income'] > 0 ? ($summary['net_profit'] > 0 ? 'positive' : 'negative') : 'neutral'); ?>"
                                id="profit-margin">
                                <?php echo e($summary['total_income'] > 0 ? number_format(($summary['net_profit'] / $summary['total_income']) * 100, 1) : '0.0'); ?>%
                            </span>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4">
                    <div class="chart-container">
                        <h5 class="mb-4"><?php echo e(__('accounting.quick_insights')); ?></h5>

                        <div class="text-center mb-4">
                            <div
                                class="display-6 fw-bold <?php echo e($summary['total_refunds'] > 0 ? 'text-warning' : 'text-success'); ?>">
                                <?php echo e(number_format($summary['total_refunds'], 2)); ?> <?php echo e(currency()); ?>

                            </div>
                            <small class="text-muted"><?php echo e(__('accounting.total_refunds')); ?></small>
                        </div>

                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="h4 mb-1 text-primary">
                                        <?php echo e($summary['total_income'] > 0 ? number_format(($summary['total_commissions'] / $summary['total_income']) * 100, 1) : '0.0'); ?>%
                                    </div>
                                    <small class="text-muted"><?php echo e(__('accounting.avg_commission_rate')); ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h4 mb-1 text-info">
                                    <?php echo e($summary['total_expenses'] > 0 ? number_format(($summary['total_expenses'] / ($summary['total_income'] ?: 1)) * 100, 1) : '0.0'); ?>%
                                </div>
                                <small class="text-muted"><?php echo e(__('accounting.expense_ratio')); ?></small>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
        document.getElementById('filterBtn').addEventListener('click', function() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;

            const url = new URL(window.location);
            if (dateFrom) url.searchParams.set('date_from', dateFrom);
            if (dateTo) url.searchParams.set('date_to', dateTo);

            window.location.href = url.toString();
        });

        document.getElementById('resetBtn').addEventListener('click', function() {
            const url = new URL(window.location);
            url.searchParams.delete('date_from');
            url.searchParams.delete('date_to');
            window.location.href = url.toString();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Custom tab header functionality
            $('.custom-tab-header').on('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all headers
                $('.custom-tab-header').removeClass('active');
                
                // Add active class to clicked header
                $(this).addClass('active');
                
                // Get target tab pane
                const target = $(this).attr('href');
                
                // Hide all tab panes
                $('.tab-pane').removeClass('show active');
                
                // Show target tab pane
                $(target).addClass('show active');
                
                // Initialize orders table if orders tab is clicked
                if (target === '#orders' && !ordersTableInitialized) {
                    initializeOrdersTable();
                    ordersTableInitialized = true;
                }
            });

            // Monthly Breakdown Chart
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyData = <?php echo json_encode($summary['monthly_data'] ?? [], 15, 512) ?>;

            const months = [];
            const incomeData = [];
            const expenseData = [];
            const profitData = [];
            const commissionData = [];

            for (let i = 1; i <= 12; i++) {
                months.push(new Date(0, i - 1).toLocaleString('default', {
                    month: 'short'
                }));
                incomeData.push(monthlyData[i]?.income || 0);
                expenseData.push(monthlyData[i]?.expenses || 0);
                commissionData.push(monthlyData[i]?.commissions || 0);
                profitData.push((monthlyData[i]?.income || 0) - (monthlyData[i]?.expenses || 0));
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: '<?php echo e(__('accounting.income')); ?>',
                        data: incomeData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }, {
                        label: '<?php echo e(__('accounting.expenses')); ?>',
                        data: expenseData,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4
                    }, {
                        label: '<?php echo e(__('accounting.profit_loss')); ?>',
                        data: profitData,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4
                    }, {
                        label: '<?php echo e(__('accounting.commissions')); ?>',
                        data: commissionData,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' <?php echo e(currency()); ?>';
                                }
                            }
                        }
                    }
                }
            });

            // Orders DataTable
            let ordersPerPage = 10;
            let ordersTable;
            let ordersTableInitialized = false;

            // Get deliver stage ID
            const deliverStageId = <?php echo json_encode(\Modules\Order\app\Models\OrderStage::withoutGlobalScopes()->where('type', 'deliver')->value('id'), 512) ?>;

            // Server-side processing with pagination
            const isVendorUser = <?php echo e(!isAdmin() ? 'true' : 'false'); ?>;

            function initializeOrdersTable() {
                // Define columns based on user type (same as orders index)
                let tableColumns = [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        className: 'text-center fw-bold'
                    },
                    {
                        data: null,
                        name: 'order_customer',
                        orderable: false,
                        searchable: true,
                        render: function(data, type, row) {
                            const orderNumber = data.order_number || '-';
                            const customerName = data.customer_name || '-';
                            const customerEmail = data.customer_email || '-';
                            const customerPhone = data.customer_phone || '-';

                            return `
                    <div class="customer-info">
                        <div class="fw-bold mb-1">
                            <i class="uil uil-receipt me-1"></i><strong>${orderNumber}</strong>
                        </div>
                        <div class="small">
                            <div class="mb-1">
                                <i class="uil uil-user me-1"></i> <strong>${customerName}</strong>
                            </div>
                            <div class="mb-1">
                                <i class="uil uil-envelope me-1"></i> <a href="mailto:${customerEmail}">${customerEmail}</a>
                            </div>
                            <div>
                                <i class="uil uil-phone me-1"></i> ${customerPhone}
                            </div>
                        </div>
                    </div>
                `;
                        }
                    }
                ];

                // Add vendor column only for admin users
                if (!isVendorUser) {
                    tableColumns.push({
                        data: 'vendor',
                        name: 'vendor',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data || data.length === 0) {
                                return '-';
                            }

                            let logosHtml = '<div class="vendor-logos">';
                            data.forEach(function(vendor, index) {
                                logosHtml += `
                        <div class="vendor-logo-wrapper">
                            <img src="${vendor.logo_url}" alt="${vendor.name}" class="vendor-logo">
                            <div class="vendor-name-tooltip">${vendor.name}</div>
                        </div>
                    `;
                            });
                            logosHtml += '</div>';
                            return logosHtml;
                        }
                    });
                }

                // Add remaining columns
                tableColumns.push({
                    data: 'total_price',
                    name: 'total_price',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<span class="fw-bold">${data} <?php echo e(currency()); ?></span>`;
                    }
                }, {
                    data: 'stage',
                    name: 'stage',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (!data || !data.name) return '-';
                        return `<span class="badge" style="background-color: ${data.color || '#6c757d'}; color: white;">${data.name}</span>`;
                    }
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        return new Date(data).toLocaleDateString();
                    }
                }, {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<a href="<?php echo e(route('admin.orders.show', ':id')); ?>" class="btn btn-sm btn-outline-primary"><?php echo e(__('common.show')); ?></a>`
                            .replace(':id', row.id);
                    }
                });

                // Initialize Orders DataTable
                ordersTable = $('#ordersDataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '<?php echo e(route('admin.orders.datatable')); ?>',
                        type: 'GET',
                        data: function(d) {
                            // Map DataTables search format to expected format
                            return {
                                stage: deliverStageId,
                                per_page: ordersPerPage,
                                start: d.start,
                                length: d.length,
                                draw: d.draw,
                                search: d.search ? d.search.value : '',
                                order: d.order
                            };
                        }
                    },
                    columns: tableColumns,
                    pageLength: ordersPerPage,
                    lengthChange: false,
                    searching: false,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    language: {
                        processing: "<?php echo e(__('common.processing')); ?>",
                        search: "<?php echo e(__('common.search')); ?>:",
                        lengthMenu: "<?php echo e(__('common.show')); ?> _MENU_ <?php echo e(__('common.entries')); ?>",
                        info: "<?php echo e(__('common.showing')); ?> _START_ <?php echo e(__('common.to')); ?> _END_ <?php echo e(__('common.of')); ?> _TOTAL_ <?php echo e(__('common.entries')); ?>",
                        infoEmpty: "<?php echo e(__('common.showing')); ?> 0 <?php echo e(__('common.to')); ?> 0 <?php echo e(__('common.of')); ?> 0 <?php echo e(__('common.entries')); ?>",
                        infoFiltered: "(<?php echo e(__('common.filtered_from')); ?> _MAX_ <?php echo e(__('common.total_entries')); ?>)",
                        paginate: {
                            first: "<?php echo e(__('common.first')); ?>",
                            last: "<?php echo e(__('common.last')); ?>",
                            next: "<?php echo e(__('common.next')); ?>",
                            previous: "<?php echo e(__('common.previous')); ?>"
                        },
                        emptyTable: "<?php echo e(__('accounting.no_delivered_orders')); ?>",
                        zeroRecords: "<?php echo e(__('accounting.no_delivered_orders')); ?>"
                    },
                    drawCallback: function(settings) {
                        // Re-initialize tooltips after table redraw
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                });

                // Handle entries per page change for orders
                $('#ordersEntriesSelect').on('change', function() {
                    ordersPerPage = parseInt($(this).val());
                    ordersTable.page.len(ordersPerPage).draw();
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Accounting\resources/views/summary.blade.php ENDPATH**/ ?>