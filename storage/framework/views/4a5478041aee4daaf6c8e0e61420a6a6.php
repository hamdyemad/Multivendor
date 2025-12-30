<div class="col-xl-7 col-lg-12 mb-30">
    <div class="card chart-card">
        <div class="card-body fw-bold">
            <h5 class="header-title pb-2 mt-0" style="font-weight: bold; font-size: 1.1rem; color: #495057; margin-bottom: 1rem;">
                <?php echo e(trans('dashboard.income_expense_this_month')); ?>

                ( <?php echo e($incomeExpense['month']['period'] ?? date('m-Y')); ?> )
            </h5>


            <div class="text-center mb-3" style="font-size: 0.75rem; color: #555;">
                <?php echo e(trans('dashboard.income')); ?>: <span style="color: #28a745; font-weight: 600;"><?php echo e(number_format($incomeExpense['month']['income'] ?? 0, 1)); ?></span>
                <?php echo e(currency()); ?> |
                <?php echo e(trans('dashboard.expenses')); ?>: <span style="color: #dc3545; font-weight: 600;"><?php echo e(number_format($incomeExpense['month']['expenses'] ?? 0, 1)); ?></span>
                <?php echo e(currency()); ?> |
                <?php echo e(trans('dashboard.commission')); ?>: <span style="color: #007bff; font-weight: 600;"><?php echo e(number_format($incomeExpense['month']['commission'] ?? 0, 1)); ?></span>
                <?php echo e(currency()); ?> |
                <?php echo e(trans('dashboard.profit')); ?>: <span style="color: <?php echo e(($incomeExpense['month']['profit'] ?? 0) >= 0 ? '#28a745' : '#dc3545'); ?>; font-weight: 600;"><?php echo e(number_format($incomeExpense['month']['profit'] ?? 0, 1)); ?></span>
                <?php echo e(currency()); ?>

            </div>

            <canvas id="monthlyAccountingChart"
                style="max-height: 300px; display: block; box-sizing: border-box; height: 300px;"></canvas>


            <div class="text-center mt-3" style="font-size: 0.9rem;">
                <span style="margin-right: 20px;">
                    <span
                        style="display: inline-block; width: 12px; height: 12px; background-color: #28a745; border-radius: 50%; margin-right: 5px;"></span>
                    <?php echo e(trans('dashboard.income')); ?>

                </span>
                <span>
                    <span
                        style="display: inline-block; width: 12px; height: 12px; background-color: #dc3545; border-radius: 50%; margin-right: 5px;"></span>
                    <?php echo e(trans('dashboard.expenses')); ?>

                </span>
            </div>
        </div>
    </div>
</div>
<div class="col-xl-5 col-lg-12 mb-30">
    <div class="card chart-card">
        <div class="card-body">
            <h5 class="header-title pb-2 mt-0" style="font-weight: bold; font-size: 1.1rem; color: #495057; margin-bottom: 1rem;">
                <?php echo e(trans('dashboard.income_expense_this_year')); ?> ( <?php echo e($incomeExpense['year']['period'] ?? date('Y')); ?> )
            </h5>


            <div class="text-center mb-3" style="font-size: 0.60rem; color: #555;">
                <?php echo e(trans('dashboard.income')); ?>: <span style="color: #28a745; font-weight: 600;"><?php echo e(number_format($incomeExpense['year']['income'] ?? 0, 1)); ?></span>
                <?php echo e(currency()); ?> |
                <?php echo e(trans('dashboard.expenses')); ?>: <span style="color: #dc3545; font-weight: 600;"><?php echo e(number_format($incomeExpense['year']['expenses'] ?? 0, 1)); ?></span>
                <?php echo e(currency()); ?> |
                <?php echo e(trans('dashboard.commission')); ?>: <span style="color: #007bff; font-weight: 600;"><?php echo e(number_format($incomeExpense['year']['commission'] ?? 0, 1)); ?></span>
                <?php echo e(currency()); ?> |
                <?php echo e(trans('dashboard.profit')); ?>: <span style="color: <?php echo e(($incomeExpense['year']['profit'] ?? 0) >= 0 ? '#28a745' : '#dc3545'); ?>; font-weight: 600;"><?php echo e(number_format($incomeExpense['year']['profit'] ?? 0, 1)); ?></span>
                <?php echo e(currency()); ?>

            </div>

            <canvas id="yearlyAccountingChart"
                style="max-height: 300px; display: block; box-sizing: border-box; height: 300px;"></canvas>


            <div class="text-center mt-3" style="font-size: 0.9rem;">
                <span style="margin-right: 20px;">
                    <span
                        style="display: inline-block; width: 12px; height: 12px; background-color: #28a745; border-radius: 50%; margin-right: 5px;"></span>
                    <?php echo e(trans('dashboard.income')); ?>

                </span>
                <span>
                    <span
                        style="display: inline-block; width: 12px; height: 12px; background-color: #dc3545; border-radius: 50%; margin-right: 5px;"></span>
                    <?php echo e(trans('dashboard.expenses')); ?>

                </span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/income-and-expenses.blade.php ENDPATH**/ ?>