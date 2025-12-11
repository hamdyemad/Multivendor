@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Today
        const salesTodayCtx = document.getElementById('salesToday');
        if (salesTodayCtx) {
            new Chart(salesTodayCtx, {
                type: 'bar',
                data: {
                    labels: ['12am', '3am', '6am', '9am', '12pm', '3pm', '6pm', '9pm'],
                    datasets: [{
                        label: 'Sales',
                        data: [500, 800, 600, 1200, 1000, 1500, 1300, 1800],
                        backgroundColor: 'rgba(91, 105, 255, 0.2)',
                        borderColor: 'rgba(91, 105, 255, 1)',
                        borderWidth: 2
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

        // Sales Week
        const salesWeekCtx = document.getElementById('salesWeek');
        if (salesWeekCtx) {
            new Chart(salesWeekCtx, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Sales',
                        data: [5000, 7500, 6000, 9000, 8000, 10000, 9500],
                        backgroundColor: 'rgba(91, 105, 255, 0.2)',
                        borderColor: 'rgba(91, 105, 255, 1)',
                        borderWidth: 2
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

        // Sales Month
        const salesMonthCtx = document.getElementById('salesMonth');
        if (salesMonthCtx) {
            new Chart(salesMonthCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Sales',
                        data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 32000, 27000, 35000, 40000, 38000],
                        backgroundColor: 'rgba(91, 105, 255, 0.2)',
                        borderColor: 'rgba(91, 105, 255, 1)',
                        borderWidth: 2
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

        // Orders Overview Pie Chart
        const ordersOverviewCtx = document.getElementById('ordersOverviewChart');
        if (ordersOverviewCtx) {
            new Chart(ordersOverviewCtx, {
                type: 'doughnut',
                data: {
                    labels: ['New', 'In Progress', 'Delivered', 'Cancelled', 'Want to Return', 'Return in Progress', 'Refunded'],
                    datasets: [{
                        data: [2, 2, 2, 3, 0, 0, 1],
                        backgroundColor: [
                            'rgba(91, 105, 255, 0.8)', 'rgba(255, 193, 7, 0.8)', 'rgba(32, 201, 151, 0.8)',
                            'rgba(255, 76, 81, 0.8)', 'rgba(255, 152, 0, 0.8)', 'rgba(156, 39, 176, 0.8)', 'rgba(103, 58, 183, 0.8)'
                        ],
                        borderColor: [
                            'rgba(91, 105, 255, 1)', 'rgba(255, 193, 7, 1)', 'rgba(32, 201, 151, 1)',
                            'rgba(255, 76, 81, 1)', 'rgba(255, 152, 0, 1)', 'rgba(156, 39, 176, 1)', 'rgba(103, 58, 183, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return (context.label || '') + ': ' + context.parsed + ' orders';
                                }
                            }
                        }
                    }
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
                        label: 'Total Sales',
                        data: [600, 900, 700, 1400, 1200, 1700, 1500, 2000],
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
                        label: 'Total Sales',
                        data: [6000, 8500, 7000, 10000, 9000, 11000, 10500],
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
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Total Sales',
                        data: [15000, 22000, 18000, 28000, 25000, 35000, 32000, 38000, 33000, 42000, 48000, 45000],
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
                        data: [600, 900, 700, 1400, 1200, 1700, 1500, 2000],
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
                        data: [6000, 8500, 7000, 10000, 9000, 11000, 10500],
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
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Earnings',
                        data: [15000, 22000, 18000, 28000, 25000, 35000, 32000, 38000, 33000, 42000, 48000, 45000],
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

        // Total Sales Year
        const totalSalesYearCtx = document.getElementById('totalSalesYear');
        if (totalSalesYearCtx) {
            new Chart(totalSalesYearCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Total Sales',
                        data: [18000, 25000, 22000, 32000, 28000, 38000, 35000, 42000, 38000, 48000, 52000, 50000],
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
                    labels: ['2021', '2022', '2023', '2024', '2025'],
                    datasets: [{
                        label: 'Total Sales',
                        data: [180000, 220000, 280000, 350000, 420000],
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

        // Earnings Year
        const earningsYearCtx = document.getElementById('earningsYear');
        if (earningsYearCtx) {
            new Chart(earningsYearCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Earnings',
                        data: [8000, 12000, 10000, 15000, 13000, 18000, 16000, 20000, 18000, 22000, 25000, 23000],
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
                    labels: ['2021', '2022', '2023', '2024', '2025'],
                    datasets: [{
                        label: 'Earnings',
                        data: [85000, 110000, 145000, 180000, 220000],
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

        // Monthly Accounting Chart (Income & Expenses by Days)
        const monthlyAccountingCtx = document.getElementById('monthlyAccountingChart');
        if (monthlyAccountingCtx) {
            new Chart(monthlyAccountingCtx, {
                type: 'line',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'],
                    datasets: [{
                        label: 'Income',
                        data: [800, 1200, 950, 1100, 1300, 1050, 900, 1400, 1250, 1150, 1350, 1200, 1100, 1450, 1300, 1250, 1400, 1500, 1350, 1200, 1450, 1550, 1400, 1300, 1500, 1600, 1450, 1550, 1650, 1700, 1800],
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1
                    }, {
                        label: 'Expenses',
                        data: [600, 750, 650, 800, 900, 700, 650, 950, 850, 800, 900, 850, 750, 1000, 900, 850, 950, 1050, 900, 800, 950, 1100, 950, 850, 1000, 1150, 950, 1050, 1100, 1200, 1250],
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: 'rgba(220, 53, 69, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + '';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return (value / 1000) + '';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Yearly Accounting Chart (Income & Expenses by Year)
        const yearlyAccountingCtx = document.getElementById('yearlyAccountingChart');
        if (yearlyAccountingCtx) {
            new Chart(yearlyAccountingCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Income',
                        data: [15000, 18000, 22000, 25000, 28000, 30000, 32000, 35000, 38000, 40000, 42000, 45000],
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Expenses',
                        data: [12000, 14000, 18000, 20000, 22000, 24000, 26000, 28000, 30000, 32000, 34000, 36000],
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(220, 53, 69, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + '';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return (value / 1000) + '';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
