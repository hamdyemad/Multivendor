<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data from backend - dynamic orders overview
        const ordersOverviewData = <?php echo json_encode($ordersOverview); ?>;
        const ordersOverviewLabels = ordersOverviewData.map(stage => stage.name);
        const ordersOverviewCounts = ordersOverviewData.map(stage => stage.count);
        const ordersOverviewColors = ordersOverviewData.map(stage => stage.color + 'cc'); // Add transparency

        // Total Sales chart data (all orders)
        const salesChartLabels = <?php echo json_encode($salesChart['labels'] ?? []); ?>;
        const salesChartData = <?php echo json_encode($salesChart['data'] ?? []); ?>;
        const salesChartHourly = <?php echo json_encode($salesChart['hourly'] ?? [0,0,0,0,0,0,0,0]); ?>;
        const salesChartWeekly = <?php echo json_encode($salesChart['weekly'] ?? [0,0,0,0,0,0,0]); ?>;
        const salesChartDaily = <?php echo json_encode($salesChart['daily'] ?? []); ?>;
        const salesChartMonthly = <?php echo json_encode($salesChart['monthly'] ?? [0,0,0,0,0,0,0,0,0,0,0,0]); ?>;
        const salesChartYearlyLabels = <?php echo json_encode($salesChart['yearly_labels'] ?? []); ?>;
        const salesChartYearlyData = <?php echo json_encode($salesChart['yearly_data'] ?? []); ?>;

        // Earnings chart data (delivered orders only)
        const earningsChartLabels = <?php echo json_encode($earningsChart['labels'] ?? []); ?>;
        const earningsChartData = <?php echo json_encode($earningsChart['data'] ?? []); ?>;
        const earningsChartHourly = <?php echo json_encode($earningsChart['hourly'] ?? [0,0,0,0,0,0,0,0]); ?>;
        const earningsChartWeekly = <?php echo json_encode($earningsChart['weekly'] ?? [0,0,0,0,0,0,0]); ?>;
        const earningsChartDaily = <?php echo json_encode($earningsChart['daily'] ?? []); ?>;
        const earningsChartMonthly = <?php echo json_encode($earningsChart['monthly'] ?? [0,0,0,0,0,0,0,0,0,0,0,0]); ?>;
        const earningsChartYearlyLabels = <?php echo json_encode($earningsChart['yearly_labels'] ?? []); ?>;
        const earningsChartYearlyData = <?php echo json_encode($earningsChart['yearly_data'] ?? []); ?>;

        const incomeExpenseMonthDaily = <?php echo json_encode($incomeExpense['month']['daily_data'] ?? []); ?>;
        const incomeExpenseYearMonthly = <?php echo json_encode($incomeExpense['year']['monthly_data'] ?? []); ?>;

        // Orders Overview Pie Chart
        const ordersOverviewCtx = document.getElementById('ordersOverviewChart');
        if (ordersOverviewCtx) {
            new Chart(ordersOverviewCtx, {
                type: 'doughnut',
                data: {
                    labels: ordersOverviewLabels,
                    datasets: [{
                        data: ordersOverviewCounts,
                        backgroundColor: ordersOverviewColors,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Total Sales Today
        const totalSalesTodayCtx = document.getElementById('totalSalesToday');
        if (totalSalesTodayCtx) {
            new Chart(totalSalesTodayCtx, {
                type: 'line',
                data: {
                    labels: ['12am', '1am', '2am', '3am', '4am', '5am', '6am', '7am', '8am', '9am', '10am', '11am'],
                    datasets: [{
                        label: '<?php echo e(trans("dashboard.total_sales")); ?>',
                        data: salesChartHourly,
                        backgroundColor: 'rgba(130, 49, 211, 0.1)',
                        borderColor: 'rgba(130, 49, 211, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Total Sales Week
        const totalSalesWeekCtx = document.getElementById('totalSalesWeek');
        if (totalSalesWeekCtx) {
            new Chart(totalSalesWeekCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: '<?php echo e(trans("dashboard.total_sales")); ?>',
                        data: salesChartWeekly,
                        backgroundColor: 'rgba(130, 49, 211, 0.1)',
                        borderColor: 'rgba(130, 49, 211, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Total Sales Month (daily breakdown)
        const totalSalesMonthCtx = document.getElementById('totalSalesMonth');
        if (totalSalesMonthCtx) {
            const daysInMonth = salesChartDaily.length || 31;
            const dayLabels = Array.from({length: daysInMonth}, (_, i) => i + 1);
            new Chart(totalSalesMonthCtx, {
                type: 'line',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: '<?php echo e(trans("dashboard.total_sales")); ?>',
                        data: salesChartDaily.length ? salesChartDaily : Array(daysInMonth).fill(0),
                        backgroundColor: 'rgba(130, 49, 211, 0.1)',
                        borderColor: 'rgba(130, 49, 211, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Total Sales Year (monthly breakdown)
        const totalSalesYearCtx = document.getElementById('totalSalesYear');
        if (totalSalesYearCtx) {
            new Chart(totalSalesYearCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: '<?php echo e(trans("dashboard.total_sales")); ?>',
                        data: salesChartMonthly.length ? salesChartMonthly : Array(12).fill(0),
                        backgroundColor: 'rgba(130, 49, 211, 0.1)',
                        borderColor: 'rgba(130, 49, 211, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Total Sales 5 Years
        const totalSales5YearsCtx = document.getElementById('totalSales5Years');
        if (totalSales5YearsCtx) {
            new Chart(totalSales5YearsCtx, {
                type: 'line',
                data: {
                    labels: salesChartYearlyLabels.length ? salesChartYearlyLabels : [<?php echo e(date('Y')-4); ?>, <?php echo e(date('Y')-3); ?>, <?php echo e(date('Y')-2); ?>, <?php echo e(date('Y')-1); ?>, <?php echo e(date('Y')); ?>],
                    datasets: [{
                        label: '<?php echo e(trans("dashboard.total_sales")); ?>',
                        data: salesChartYearlyData.length ? salesChartYearlyData : [0,0,0,0,0],
                        backgroundColor: 'rgba(130, 49, 211, 0.1)',
                        borderColor: 'rgba(130, 49, 211, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Earnings Today
        const earningsTodayCtx = document.getElementById('earningsToday');
        if (earningsTodayCtx) {
            new Chart(earningsTodayCtx, {
                type: 'line',
                data: {
                    labels: ['12am', '1am', '2am', '3am', '4am', '5am', '6am', '7am', '8am', '9am', '10am', '11am'],
                    datasets: [{
                        label: 'Earnings',
                        data: earningsChartHourly,
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderColor: 'rgba(32, 201, 151, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Earnings Week
        const earningsWeekCtx = document.getElementById('earningsWeek');
        if (earningsWeekCtx) {
            new Chart(earningsWeekCtx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Earnings',
                        data: earningsChartWeekly,
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderColor: 'rgba(32, 201, 151, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Earnings Month (daily breakdown)
        const earningsMonthCtx = document.getElementById('earningsMonth');
        if (earningsMonthCtx) {
            const daysInMonth = earningsChartDaily.length || 31;
            const dayLabels = Array.from({length: daysInMonth}, (_, i) => i + 1);
            new Chart(earningsMonthCtx, {
                type: 'line',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: 'Earnings',
                        data: earningsChartDaily.length ? earningsChartDaily : Array(daysInMonth).fill(0),
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderColor: 'rgba(32, 201, 151, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Earnings Year (monthly breakdown)
        const earningsYearCtx = document.getElementById('earningsYear');
        if (earningsYearCtx) {
            new Chart(earningsYearCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Earnings',
                        data: earningsChartMonthly.length ? earningsChartMonthly : Array(12).fill(0),
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderColor: 'rgba(32, 201, 151, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Earnings 5 Years
        const earnings5YearsCtx = document.getElementById('earnings5Years');
        if (earnings5YearsCtx) {
            new Chart(earnings5YearsCtx, {
                type: 'line',
                data: {
                    labels: earningsChartYearlyLabels.length ? earningsChartYearlyLabels : [<?php echo e(date('Y')-4); ?>, <?php echo e(date('Y')-3); ?>, <?php echo e(date('Y')-2); ?>, <?php echo e(date('Y')-1); ?>, <?php echo e(date('Y')); ?>],
                    datasets: [{
                        label: 'Earnings',
                        data: earningsChartYearlyData.length ? earningsChartYearlyData : [0,0,0,0,0],
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        borderColor: 'rgba(32, 201, 151, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Monthly Accounting Chart
        const monthlyAccountingCtx = document.getElementById('monthlyAccountingChart');
        if (monthlyAccountingCtx) {
            const dailyLabels = incomeExpenseMonthDaily.map(d => d.day);
            const dailyIncome = incomeExpenseMonthDaily.map(d => d.income);
            const dailyExpenses = incomeExpenseMonthDaily.map(d => d.expenses);
            
            new Chart(monthlyAccountingCtx, {
                type: 'line',
                data: {
                    labels: dailyLabels.length ? dailyLabels : Array.from({length: 31}, (_, i) => i + 1),
                    datasets: [{
                        label: '<?php echo e(trans("dashboard.income")); ?>',
                        data: dailyIncome.length ? dailyIncome : Array(31).fill(0),
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '<?php echo e(trans("dashboard.expenses")); ?>',
                        data: dailyExpenses.length ? dailyExpenses : Array(31).fill(0),
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
                }
            });
        }

        // Yearly Accounting Chart
        const yearlyAccountingCtx = document.getElementById('yearlyAccountingChart');
        if (yearlyAccountingCtx) {
            const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthlyIncome = incomeExpenseYearMonthly.map(d => d.income);
            const monthlyExpenses = incomeExpenseYearMonthly.map(d => d.expenses);
            
            new Chart(yearlyAccountingCtx, {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: '<?php echo e(trans("dashboard.income")); ?>',
                        data: monthlyIncome.length ? monthlyIncome : Array(12).fill(0),
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '<?php echo e(trans("dashboard.expenses")); ?>',
                        data: monthlyExpenses.length ? monthlyExpenses : Array(12).fill(0),
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/pages/dashboard/charts-scripts.blade.php ENDPATH**/ ?>