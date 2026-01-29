@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data from backend - dynamic orders overview
        const ordersOverviewData = {!! json_encode($ordersOverview) !!};
        const ordersOverviewLabels = ordersOverviewData.map(stage => stage.name);
        const ordersOverviewCounts = ordersOverviewData.map(stage => stage.count);
        const ordersOverviewColors = ordersOverviewData.map(stage => stage.color + 'cc'); // Add transparency

        // Total Sales chart data (all orders)
        const salesChartLabels = {!! json_encode($salesChart['labels'] ?? []) !!};
        const salesChartData = {!! json_encode($salesChart['data'] ?? []) !!};
        const salesChartHourly = {!! json_encode($salesChart['hourly'] ?? [0,0,0,0,0,0,0,0]) !!};
        const salesChartWeekly = {!! json_encode($salesChart['weekly'] ?? [0,0,0,0,0,0,0]) !!};
        const salesChartDaily = {!! json_encode($salesChart['daily'] ?? []) !!};
        const salesChartMonthly = {!! json_encode($salesChart['monthly'] ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!};
        const salesChartYearlyLabels = {!! json_encode($salesChart['yearly_labels'] ?? []) !!};
        const salesChartYearlyData = {!! json_encode($salesChart['yearly_data'] ?? []) !!};

        // Earnings chart data (delivered orders only)
        const earningsChartLabels = {!! json_encode($earningsChart['labels'] ?? []) !!};
        const earningsChartData = {!! json_encode($earningsChart['data'] ?? []) !!};
        const earningsChartHourly = {!! json_encode($earningsChart['hourly'] ?? [0,0,0,0,0,0,0,0]) !!};
        const earningsChartWeekly = {!! json_encode($earningsChart['weekly'] ?? [0,0,0,0,0,0,0]) !!};
        const earningsChartDaily = {!! json_encode($earningsChart['daily'] ?? []) !!};
        const earningsChartMonthly = {!! json_encode($earningsChart['monthly'] ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!};
        const earningsChartYearlyLabels = {!! json_encode($earningsChart['yearly_labels'] ?? []) !!};
        const earningsChartYearlyData = {!! json_encode($earningsChart['yearly_data'] ?? []) !!};

        const incomeExpenseMonthDaily = {!! json_encode($incomeExpense['month']['daily_data'] ?? []) !!};
        const incomeExpenseYearMonthly = {!! json_encode($incomeExpense['year']['monthly_data'] ?? []) !!};
        
        // Net Sales chart data
        const netSalesHourly = {!! json_encode($netSalesChart['hourly'] ?? []) !!};
        const netSalesWeekly = {!! json_encode($netSalesChart['weekly'] ?? []) !!};
        const netSalesDaily = {!! json_encode($netSalesChart['daily'] ?? []) !!};
        const netSalesMonthly = {!! json_encode($netSalesChart['monthly'] ?? []) !!};
        const netSalesYearlyLabels = {!! json_encode($netSalesChart['yearly_labels'] ?? []) !!};
        const netSalesYearlyData = {!! json_encode($netSalesChart['yearly_data'] ?? []) !!};

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
                    labels: Array.from({length: 24}, (_, i) => i + ':00'),
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
                    labels: ['{{ trans("dashboard.mon") }}', '{{ trans("dashboard.tue") }}', '{{ trans("dashboard.wed") }}', '{{ trans("dashboard.thu") }}', '{{ trans("dashboard.fri") }}', '{{ trans("dashboard.sat") }}', '{{ trans("dashboard.sun") }}'],
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
                        label: '{{ trans("dashboard.total_sales") }}',
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
                    labels: ['{{ trans("dashboard.jan") }}', '{{ trans("dashboard.feb") }}', '{{ trans("dashboard.mar") }}', '{{ trans("dashboard.apr") }}', '{{ trans("dashboard.may_short") }}', '{{ trans("dashboard.jun") }}', '{{ trans("dashboard.jul") }}', '{{ trans("dashboard.aug") }}', '{{ trans("dashboard.sep") }}', '{{ trans("dashboard.oct") }}', '{{ trans("dashboard.nov") }}', '{{ trans("dashboard.dec") }}'],
                    datasets: [{
                        label: '{{ trans("dashboard.total_sales") }}',
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
                    labels: Array.from({length: 24}, (_, i) => i + ':00'),
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
                    labels: ['{{ trans("dashboard.mon") }}', '{{ trans("dashboard.tue") }}', '{{ trans("dashboard.wed") }}', '{{ trans("dashboard.thu") }}', '{{ trans("dashboard.fri") }}', '{{ trans("dashboard.sat") }}', '{{ trans("dashboard.sun") }}'],
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
                    labels: ['{{ trans("dashboard.jan") }}', '{{ trans("dashboard.feb") }}', '{{ trans("dashboard.mar") }}', '{{ trans("dashboard.apr") }}', '{{ trans("dashboard.may_short") }}', '{{ trans("dashboard.jun") }}', '{{ trans("dashboard.jul") }}', '{{ trans("dashboard.aug") }}', '{{ trans("dashboard.sep") }}', '{{ trans("dashboard.oct") }}', '{{ trans("dashboard.nov") }}', '{{ trans("dashboard.dec") }}'],
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
                    labels: earningsChartYearlyLabels.length ? earningsChartYearlyLabels : [{{ date('Y')-4 }}, {{ date('Y')-3 }}, {{ date('Y')-2 }}, {{ date('Y')-1 }}, {{ date('Y') }}],
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
            const dailyCommission = incomeExpenseMonthDaily.map(d => d.commission || 0);
            
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
                    }, {
                        label: '{{ trans("dashboard.commission") }}',
                        data: dailyCommission.length ? dailyCommission : Array(31).fill(0),
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top' } },
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
                }
            });
        }

        // Yearly Accounting Chart
        const yearlyAccountingCtx = document.getElementById('yearlyAccountingChart');
        if (yearlyAccountingCtx) {
            const monthLabels = ['{{ trans("dashboard.jan") }}', '{{ trans("dashboard.feb") }}', '{{ trans("dashboard.mar") }}', '{{ trans("dashboard.apr") }}', '{{ trans("dashboard.may_short") }}', '{{ trans("dashboard.jun") }}', '{{ trans("dashboard.jul") }}', '{{ trans("dashboard.aug") }}', '{{ trans("dashboard.sep") }}', '{{ trans("dashboard.oct") }}', '{{ trans("dashboard.nov") }}', '{{ trans("dashboard.dec") }}'];
            const monthlyIncome = incomeExpenseYearMonthly.map(d => d.income);
            const monthlyExpenses = incomeExpenseYearMonthly.map(d => d.expenses);
            const monthlyCommission = incomeExpenseYearMonthly.map(d => d.commission || 0);
            
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
                    }, {
                        label: '{{ trans("dashboard.commission") }}',
                        data: monthlyCommission.length ? monthlyCommission : Array(12).fill(0),
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top' } },
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
                }
            });
        }
        
        // Refunds Overview Data
        @if(isset($stats['refunds']))
        const refundsMonthDaily = @json($stats['refunds']['month']['daily_data'] ?? []);
        const refundsYearMonthly = @json($stats['refunds']['year']['monthly_data'] ?? []);
        const refundsHourly = @json($stats['refunds']['hourly_data'] ?? []);
        const refundsWeekly = @json($stats['refunds']['weekly_data'] ?? []);
        @else
        const refundsMonthDaily = [];
        const refundsYearMonthly = [];
        const refundsHourly = Array.from({length: 24}, () => ({amount: 0, count: 0}));
        const refundsWeekly = Array.from({length: 7}, () => ({amount: 0, count: 0}));
        @endif

        // Monthly Refunds Chart
        const monthlyRefundsCtx = document.getElementById('monthlyRefundsChart');
        if (monthlyRefundsCtx) {
            new Chart(monthlyRefundsCtx, {
                type: 'bar',
                data: {
                    labels: refundsMonthDaily.map(d => d.day),
                    datasets: [{
                        label: '{{ trans("dashboard.refunded_amount") }}',
                        data: refundsMonthDaily.map(d => d.amount),
                        backgroundColor: 'rgba(220, 53, 69, 0.6)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    }, {
                        label: '{{ trans("dashboard.refunds_count") }}',
                        data: refundsMonthDaily.map(d => d.count),
                        backgroundColor: 'rgba(108, 117, 125, 0.6)',
                        borderColor: 'rgba(108, 117, 125, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: '{{ trans("dashboard.amount") }}'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: '{{ trans("dashboard.count") }}'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        }

        // Yearly Refunds Chart
        const yearlyRefundsCtx = document.getElementById('yearlyRefundsChart');
        if (yearlyRefundsCtx) {
            new Chart(yearlyRefundsCtx, {
                type: 'bar',
                data: {
                    labels: ['{{ trans("dashboard.january") }}', '{{ trans("dashboard.february") }}', '{{ trans("dashboard.march") }}', 
                             '{{ trans("dashboard.april") }}', '{{ trans("dashboard.may") }}', '{{ trans("dashboard.june") }}',
                             '{{ trans("dashboard.july") }}', '{{ trans("dashboard.august") }}', '{{ trans("dashboard.september") }}',
                             '{{ trans("dashboard.october") }}', '{{ trans("dashboard.november") }}', '{{ trans("dashboard.december") }}'],
                    datasets: [{
                        label: '{{ trans("dashboard.refunded_amount") }}',
                        data: refundsYearMonthly.map(d => d.amount),
                        backgroundColor: 'rgba(220, 53, 69, 0.6)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    }, {
                        label: '{{ trans("dashboard.refunds_count") }}',
                        data: refundsYearMonthly.map(d => d.count),
                        backgroundColor: 'rgba(108, 117, 125, 0.6)',
                        borderColor: 'rgba(108, 117, 125, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: '{{ trans("dashboard.amount") }}'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: '{{ trans("dashboard.count") }}'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        }
        
        // Net Sales Chart
        const netSalesTodayCtx = document.getElementById('netSalesToday');
        const netSalesWeekCtx = document.getElementById('netSalesWeek');
        const netSalesMonthCtx = document.getElementById('netSalesMonth');
        const netSalesYearCtx = document.getElementById('netSalesYear');
        
        const netSalesChartConfig = {
            type: 'line',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 6
                        }
                    }
                }
            }
        };
        
        // Net Sales Today Chart
        if (netSalesTodayCtx) {
            new Chart(netSalesTodayCtx, {
                ...netSalesChartConfig,
                data: {
                    labels: Array.from({length: 24}, (_, i) => i + ':00'),
                    datasets: [{
                        label: '{{ trans("dashboard.earnings") }}',
                        data: netSalesHourly.map(d => d.earnings),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.refunds") }}',
                        data: netSalesHourly.map(d => d.refunds),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.net_earnings") }}',
                        data: netSalesHourly.map(d => d.net_earnings),
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Net Sales Week Chart
        if (netSalesWeekCtx) {
            new Chart(netSalesWeekCtx, {
                ...netSalesChartConfig,
                data: {
                    labels: ['{{ trans("dashboard.sunday") }}', '{{ trans("dashboard.monday") }}', '{{ trans("dashboard.tuesday") }}', 
                             '{{ trans("dashboard.wednesday") }}', '{{ trans("dashboard.thursday") }}', '{{ trans("dashboard.friday") }}', '{{ trans("dashboard.saturday") }}'],
                    datasets: [{
                        label: '{{ trans("dashboard.earnings") }}',
                        data: netSalesWeekly.map(d => d.earnings),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.refunds") }}',
                        data: netSalesWeekly.map(d => d.refunds),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.net_earnings") }}',
                        data: netSalesWeekly.map(d => d.net_earnings),
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Net Sales Month Chart
        if (netSalesMonthCtx) {
            new Chart(netSalesMonthCtx, {
                ...netSalesChartConfig,
                data: {
                    labels: Array.from({length: netSalesDaily.length}, (_, i) => i + 1),
                    datasets: [{
                        label: '{{ trans("dashboard.earnings") }}',
                        data: netSalesDaily.map(d => d.earnings),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.refunds") }}',
                        data: netSalesDaily.map(d => d.refunds),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.net_earnings") }}',
                        data: netSalesDaily.map(d => d.net_earnings),
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Net Sales Year Chart
        if (netSalesYearCtx) {
            new Chart(netSalesYearCtx, {
                ...netSalesChartConfig,
                data: {
                    labels: ['{{ trans("dashboard.january") }}', '{{ trans("dashboard.february") }}', '{{ trans("dashboard.march") }}', 
                             '{{ trans("dashboard.april") }}', '{{ trans("dashboard.may") }}', '{{ trans("dashboard.june") }}',
                             '{{ trans("dashboard.july") }}', '{{ trans("dashboard.august") }}', '{{ trans("dashboard.september") }}',
                             '{{ trans("dashboard.october") }}', '{{ trans("dashboard.november") }}', '{{ trans("dashboard.december") }}'],
                    datasets: [{
                        label: '{{ trans("dashboard.earnings") }}',
                        data: netSalesMonthly.map(d => d.earnings),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.refunds") }}',
                        data: netSalesMonthly.map(d => d.refunds),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.net_earnings") }}',
                        data: netSalesMonthly.map(d => d.net_earnings),
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Refunds Charts
        const refundsTodayCtx = document.getElementById('refundsToday');
        const refundsWeekCtx = document.getElementById('refundsWeek');
        const refundsMonthCtx = document.getElementById('refundsMonth');
        const refundsYearCtx = document.getElementById('refundsYear');
        
        const refundsChartConfig = {
            type: 'line',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 6
                        }
                    }
                }
            }
        };
        
        // Refunds Today Chart
        if (refundsTodayCtx) {
            new Chart(refundsTodayCtx, {
                ...refundsChartConfig,
                data: {
                    labels: Array.from({length: 24}, (_, i) => i + ':00'),
                    datasets: [{
                        label: '{{ trans("dashboard.refunded_amount") }}',
                        data: refundsHourly.map(d => d.amount),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Refunds Week Chart
        if (refundsWeekCtx) {
            new Chart(refundsWeekCtx, {
                ...refundsChartConfig,
                data: {
                    labels: ['{{ trans("dashboard.sunday") }}', '{{ trans("dashboard.monday") }}', '{{ trans("dashboard.tuesday") }}', 
                             '{{ trans("dashboard.wednesday") }}', '{{ trans("dashboard.thursday") }}', '{{ trans("dashboard.friday") }}', '{{ trans("dashboard.saturday") }}'],
                    datasets: [{
                        label: '{{ trans("dashboard.refunded_amount") }}',
                        data: refundsWeekly.map(d => d.amount),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Refunds Month Chart
        if (refundsMonthCtx) {
            new Chart(refundsMonthCtx, {
                ...refundsChartConfig,
                data: {
                    labels: Array.from({length: refundsMonthDaily.length}, (_, i) => i + 1),
                    datasets: [{
                        label: '{{ trans("dashboard.refunded_amount") }}',
                        data: refundsMonthDaily.map(d => d.amount),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Refunds Year Chart
        if (refundsYearCtx) {
            new Chart(refundsYearCtx, {
                ...refundsChartConfig,
                data: {
                    labels: ['{{ trans("dashboard.january") }}', '{{ trans("dashboard.february") }}', '{{ trans("dashboard.march") }}', 
                             '{{ trans("dashboard.april") }}', '{{ trans("dashboard.may") }}', '{{ trans("dashboard.june") }}',
                             '{{ trans("dashboard.july") }}', '{{ trans("dashboard.august") }}', '{{ trans("dashboard.september") }}',
                             '{{ trans("dashboard.october") }}', '{{ trans("dashboard.november") }}', '{{ trans("dashboard.december") }}'],
                    datasets: [{
                        label: '{{ trans("dashboard.refunded_amount") }}',
                        data: refundsYearMonthly.map(d => d.amount),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Refunds 5 Years Chart
        const refunds5YearsCtx = document.getElementById('refunds5Years');
        @if(isset($stats['refunds']))
        const refundsYearlyLabels = @json($stats['refunds']['yearly_labels'] ?? []);
        const refundsYearlyData = @json($stats['refunds']['yearly_data'] ?? []);
        @else
        const refundsYearlyLabels = [];
        const refundsYearlyData = [];
        @endif
        
        if (refunds5YearsCtx) {
            new Chart(refunds5YearsCtx, {
                ...refundsChartConfig,
                data: {
                    labels: refundsYearlyLabels.length ? refundsYearlyLabels : [{{ date('Y')-4 }}, {{ date('Y')-3 }}, {{ date('Y')-2 }}, {{ date('Y')-1 }}, {{ date('Y') }}],
                    datasets: [{
                        label: '{{ trans("dashboard.refunded_amount") }}',
                        data: refundsYearlyData.map(d => d.amount),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
        
        // Net Sales 5 Years Chart
        const netSales5YearsCtx = document.getElementById('netSales5Years');
        if (netSales5YearsCtx) {
            new Chart(netSales5YearsCtx, {
                ...netSalesChartConfig,
                data: {
                    labels: netSalesYearlyLabels.length ? netSalesYearlyLabels : [{{ date('Y')-4 }}, {{ date('Y')-3 }}, {{ date('Y')-2 }}, {{ date('Y')-1 }}, {{ date('Y') }}],
                    datasets: [{
                        label: '{{ trans("dashboard.earnings") }}',
                        data: netSalesYearlyData.map(d => d.earnings),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.refunds") }}',
                        data: netSalesYearlyData.map(d => d.refunds),
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: '{{ trans("dashboard.net_earnings") }}',
                        data: netSalesYearlyData.map(d => d.net_earnings),
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        }
    });
</script>
@endpush