@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data from backend
        const ordersOverviewData = {
            new: {{ $ordersOverview['new'] ?? 0 }},
            in_progress: {{ $ordersOverview['in_progress'] ?? 0 }},
            delivered: {{ $ordersOverview['delivered'] ?? 0 }},
            cancelled: {{ $ordersOverview['cancelled'] ?? 0 }},
            want_to_return: {{ $ordersOverview['want_to_return'] ?? 0 }},
            return_in_progress: {{ $ordersOverview['return_in_progress'] ?? 0 }},
            refunded: {{ $ordersOverview['refunded'] ?? 0 }}
        };

        const salesChartLabels = {!! json_encode($salesChart['labels'] ?? []) !!};
        const salesChartData = {!! json_encode($salesChart['data'] ?? []) !!};
        const salesChartHourly = {!! json_encode($salesChart['hourly'] ?? [0,0,0,0,0,0,0,0]) !!};
        const salesChartWeekly = {!! json_encode($salesChart['weekly'] ?? [0,0,0,0,0,0,0]) !!};
        const salesChartYearlyLabels = {!! json_encode($salesChart['yearly_labels'] ?? []) !!};
        const salesChartYearlyData = {!! json_encode($salesChart['yearly_data'] ?? []) !!};

        const incomeExpenseMonthDaily = {!! json_encode($incomeExpense['month']['daily_data'] ?? []) !!};
        const incomeExpenseYearMonthly = {!! json_encode($incomeExpense['year']['monthly_data'] ?? []) !!};

        // Orders Overview Pie Chart
        const ordersOverviewCtx = document.getElementById('ordersOverviewChart');
        if (ordersOverviewCtx) {
            new Chart(ordersOverviewCtx, {
                type: 'doughnut',
                data: {
                    labels: ['{{ trans("dashboard.new") }}', '{{ trans("dashboard.in_progress") }}', '{{ trans("dashboard.delivered") }}', '{{ trans("dashboard.cancelled") }}', '{{ trans("dashboard.want_to_return") }}', '{{ trans("dashboard.return_in_progress") }}', '{{ trans("dashboard.refunded") }}'],
                    datasets: [{
                        data: [
                            ordersOverviewData.new,
                            ordersOverviewData.in_progress,
                            ordersOverviewData.delivered,
                            ordersOverviewData.cancelled,
                            ordersOverviewData.want_to_return,
                            ordersOverviewData.return_in_progress,
                            ordersOverviewData.refunded
                        ],
                        backgroundColor: [
                            'rgba(91, 105, 255, 0.8)', 'rgba(255, 193, 7, 0.8)', 'rgba(32, 201, 151, 0.8)',
                            'rgba(255, 76, 81, 0.8)', 'rgba(255, 152, 0, 0.8)', 'rgba(156, 39, 176, 0.8)', 'rgba(103, 58, 183, 0.8)'
                        ],
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
                    labels: ['12am', '3am', '6am', '9am', '12pm', '3pm', '6pm', '9pm'],
                    datasets: [{
                        label: '{{ trans("dashboard.total_sales") }}',
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
                        label: '{{ trans("dashboard.total_sales") }}',
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

        // Total Sales Month
        const totalSalesMonthCtx = document.getElementById('totalSalesMonth');
        if (totalSalesMonthCtx) {
            new Chart(totalSalesMonthCtx, {
                type: 'line',
                data: {
                    labels: salesChartLabels.length ? salesChartLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: '{{ trans("dashboard.total_sales") }}',
                        data: salesChartData.length ? salesChartData : Array(12).fill(0),
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

        // Total Sales Year
        const totalSalesYearCtx = document.getElementById('totalSalesYear');
        if (totalSalesYearCtx) {
            new Chart(totalSalesYearCtx, {
                type: 'line',
                data: {
                    labels: salesChartLabels.length ? salesChartLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: '{{ trans("dashboard.total_sales") }}',
                        data: salesChartData.length ? salesChartData : Array(12).fill(0),
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
                    labels: salesChartYearlyLabels.length ? salesChartYearlyLabels : [{{ date('Y')-4 }}, {{ date('Y')-3 }}, {{ date('Y')-2 }}, {{ date('Y')-1 }}, {{ date('Y') }}],
                    datasets: [{
                        label: '{{ trans("dashboard.total_sales") }}',
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
                    labels: ['12am', '3am', '6am', '9am', '12pm', '3pm', '6pm', '9pm'],
                    datasets: [{
                        label: 'Earnings',
                        data: salesChartHourly,
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
                        data: salesChartWeekly,
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

        // Earnings Month
        const earningsMonthCtx = document.getElementById('earningsMonth');
        if (earningsMonthCtx) {
            new Chart(earningsMonthCtx, {
                type: 'line',
                data: {
                    labels: salesChartLabels.length ? salesChartLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Earnings',
                        data: salesChartData.length ? salesChartData : Array(12).fill(0),
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

        // Earnings Year
        const earningsYearCtx = document.getElementById('earningsYear');
        if (earningsYearCtx) {
            new Chart(earningsYearCtx, {
                type: 'line',
                data: {
                    labels: salesChartLabels.length ? salesChartLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Earnings',
                        data: salesChartData.length ? salesChartData : Array(12).fill(0),
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
                    labels: salesChartYearlyLabels.length ? salesChartYearlyLabels : [{{ date('Y')-4 }}, {{ date('Y')-3 }}, {{ date('Y')-2 }}, {{ date('Y')-1 }}, {{ date('Y') }}],
                    datasets: [{
                        label: 'Earnings',
                        data: salesChartYearlyData.length ? salesChartYearlyData : [0,0,0,0,0],
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
                        label: '{{ trans("dashboard.income") }}',
                        data: dailyIncome.length ? dailyIncome : Array(31).fill(0),
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.expenses") }}',
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
                        label: '{{ trans("dashboard.income") }}',
                        data: monthlyIncome.length ? monthlyIncome : Array(12).fill(0),
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.expenses") }}',
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
@endpush
