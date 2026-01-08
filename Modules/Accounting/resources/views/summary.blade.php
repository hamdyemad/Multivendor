@extends('layout.app')

@section('title', __('accounting.accounting_summary'))

@push('styles')
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
@endpush

@section('content')
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
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                                class="uil uil-estate"></i>{{ __('accounting.dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('accounting.accounting_summary') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Main Stats Cards --}}
                <div class="col-12">
                    {{-- Filters --}}
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    {{-- From Date --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> {{ __('accounting.date_from') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="date_from" name="date_from" value="{{ request('date_from') }}">
                                        </div>
                                    </div>

                                    {{-- To Date --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_to" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i> {{ __('accounting.date_to') }}
                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="date_to" name="date_to" value="{{ request('date_to') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="filterBtn"
                                            class="btn btn-success btn-default btn-squared me-1">
                                            <i class="uil uil-filter me-1"></i> {{ __('accounting.filter') }}
                                        </button>
                                        <button type="button" id="resetBtn"
                                            class="btn btn-warning btn-default btn-squared">
                                            <i class="uil uil-redo me-1"></i> {{ __('accounting.reset') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Total Income --}}
                        <div class="col-12 col-md-3 mb-25">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1 id="total-income">{{ number_format($summary['total_income'], 2) }}
                                                {{ currency() }}</h1>
                                            <p>{{ __('accounting.total_income') }}</p>
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

                        {{-- Total Expenses --}}
                        <div class="col-12 col-md-3 mb-25">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1 id="total-expenses">{{ number_format($summary['total_expenses'], 2) }}
                                                {{ currency() }}</h1>
                                            <p>{{ __('accounting.total_expenses') }}</p>
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

                        {{-- Total Commissions --}}
                        <div class="col-12 col-md-3 mb-25">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1 id="total-commissions">
                                                {{ number_format($summary['total_commissions'], 2) }} {{ currency() }}
                                            </h1>
                                            <p>{{ __('accounting.total_commissions') }}</p>
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

                        {{-- Net Profit --}}
                        <div class="col-12 col-md-3 mb-25">
                            <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                <div class="overview-content w-100">
                                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                        <div class="ap-po-details__titlebar">
                                            <h1 id="net-profit">{{ number_format($summary['net_profit'], 2) }}
                                                {{ currency() }}</h1>
                                            <p>{{ __('accounting.net_profit') }}</p>
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

                {{-- Monthly Breakdown Chart - Last Section --}}
                <div class="col-lg-12">
                    <div class="chart-container">
                        <h5 class="mb-4">{{ __('accounting.monthly_breakdown') }}</h5>
                        <canvas id="monthlyChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>

                {{-- Detailed Analysis Tabs --}}
                <div class="col-lg-12">
                    <div class="chart-container">
                        <div class="custom-tab-headers">
                            <a class="custom-tab-header active" data-bs-toggle="tab" href="#income_expense">{{ __('accounting.income_expense') }}</a>
                            <a class="custom-tab-header" data-bs-toggle="tab" href="#cost_analysis">{{ __('accounting.cost_analysis') }}</a>
                            <a class="custom-tab-header" data-bs-toggle="tab" href="#cash_flow">{{ __('accounting.cash_flow') }}</a>
                            <a class="custom-tab-header" data-bs-toggle="tab" href="#orders">{{ __('accounting.orders') }}</a>
                        </div>

                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="income_expense">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-bordered table-hover" style="width:100%">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th><span class="userDatatable-title">{{ __('accounting.category') }}</span></th>
                                                @foreach($monthHeaders as $month)
                                                    <th class="text-center"><span class="userDatatable-title">{{ $month['name'] }}</span></th>
                                                @endforeach
                                                <th class="text-center"><span class="userDatatable-title">{{ __('accounting.total') }}</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">{{ __('accounting.income') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    <td class="text-center text-success">
                                                        {{ number_format($summary['monthly_data'][$month['key']]['income'] ?? 0, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center text-success fw-bold">
                                                    {{ number_format($summary['total_income'], 2) }} {{ currency() }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">{{ __('accounting.commissions') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    <td class="text-center text-info">
                                                        {{ number_format($summary['monthly_data'][$month['key']]['commissions'] ?? 0, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center text-info fw-bold">
                                                    {{ number_format($summary['total_commissions'], 2) }} {{ currency() }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">{{ __('accounting.expenses') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    <td class="text-center text-danger">
                                                        {{ number_format($summary['monthly_data'][$month['key']]['expenses'] ?? 0, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center text-danger fw-bold">
                                                    {{ number_format($summary['total_expenses'], 2) }} {{ currency() }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">{{ __('accounting.withdraws') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    <td class="text-center text-secondary">
                                                        {{ number_format($summary['monthly_data'][$month['key']]['withdraws'] ?? 0, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center text-secondary fw-bold">
                                                    {{ number_format($summary['total_withdraws'] ?? 0, 2) }} {{ currency() }}
                                                </td>
                                            </tr>
                                            <tr class="table-info">
                                                <td class="fw-bold">{{ __('accounting.net_profit') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    @php
                                                        // Net Profit = Platform Commission - Expenses
                                                        $monthProfit = ($summary['monthly_data'][$month['key']]['commissions'] ?? 0) - ($summary['monthly_data'][$month['key']]['expenses'] ?? 0);
                                                    @endphp
                                                    <td class="text-center fw-bold {{ $monthProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($monthProfit, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center fw-bold {{ $summary['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($summary['net_profit'], 2) }} {{ currency() }}
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
                                                <th><span class="userDatatable-title">{{ __('accounting.expense_category') }}</span></th>
                                                @foreach($monthHeaders as $month)
                                                    <th class="text-center"><span class="userDatatable-title">{{ $month['name'] }}</span></th>
                                                @endforeach
                                                <th class="text-center"><span class="userDatatable-title">{{ __('accounting.total') }}</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($summary['expense_categories']))
                                                @foreach($summary['expense_categories'] as $category)
                                                    <tr>
                                                        <td class="fw-bold">{{ $category['name'] }}</td>
                                                        @foreach($monthHeaders as $month)
                                                            <td class="text-center">
                                                                {{ number_format($category['monthly'][$month['key']] ?? 0, 2) }} {{ currency() }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-center fw-bold">
                                                            {{ number_format(array_sum($category['monthly']), 2) }} {{ currency() }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="{{ count($monthHeaders) + 2 }}" class="text-center">{{ __('accounting.no_expense_data') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="cash_flow">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-bordered table-hover" style="width:100%">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th><span class="userDatatable-title">{{ __('accounting.source') }}</span></th>
                                                @foreach($monthHeaders as $month)
                                                    <th class="text-center"><span class="userDatatable-title">{{ $month['name'] }}</span></th>
                                                @endforeach
                                                <th class="text-center"><span class="userDatatable-title">{{ __('accounting.total') }}</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-success">{{ __('accounting.sales_revenue') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    <td class="text-center text-success">
                                                        {{ number_format($summary['monthly_data'][$month['key']]['income'] ?? 0, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center text-success fw-bold">
                                                    {{ number_format($summary['total_income'], 2) }} {{ currency() }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-info">{{ __('accounting.commissions') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    <td class="text-center text-info">
                                                        -{{ number_format($summary['monthly_data'][$month['key']]['commissions'] ?? 0, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center text-info fw-bold">
                                                    -{{ number_format($summary['total_commissions'], 2) }} {{ currency() }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-danger">{{ __('accounting.operating_expenses') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    <td class="text-center text-danger">
                                                        -{{ number_format($summary['monthly_data'][$month['key']]['expenses'] ?? 0, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center text-danger fw-bold">
                                                    -{{ number_format($summary['total_expenses'], 2) }} {{ currency() }}
                                                </td>
                                            </tr>
                                            <tr class="table-info">
                                                <td class="fw-bold">{{ __('accounting.net_flow') }}</td>
                                                @foreach($monthHeaders as $month)
                                                    @php
                                                        // Net Flow = Income - Commissions - Expenses
                                                        $monthFlow = ($summary['monthly_data'][$month['key']]['income'] ?? 0) - ($summary['monthly_data'][$month['key']]['commissions'] ?? 0) - ($summary['monthly_data'][$month['key']]['expenses'] ?? 0);
                                                    @endphp
                                                    <td class="text-center fw-bold {{ $monthFlow >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($monthFlow, 2) }} {{ currency() }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center fw-bold {{ $summary['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($summary['net_profit'], 2) }} {{ currency() }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="orders">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <label class="me-2 mb-0">{{ __('common.show') }}</label>
                                        <select id="ordersEntriesSelect" class="form-select form-select-sm"
                                            style="width: auto;">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <label class="ms-2 mb-0">{{ __('common.entries') }}</label>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="ordersDataTable" class="table mb-0 table-bordered table-hover"
                                        style="width:100%">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th class="text-center"><span class="userDatatable-title">#</span></th>
                                                <th><span
                                                        class="userDatatable-title">{{ trans('order::order.order_information') }}</span>
                                                </th>
                                                @if (isAdmin())
                                                    <th><span
                                                            class="userDatatable-title">{{ trans('order::order.vendor') }}</span>
                                                    </th>
                                                @endif
                                                <th><span
                                                        class="userDatatable-title">{{ trans('order::order.total_price') }}</span>
                                                </th>
                                                <th><span
                                                        class="userDatatable-title">{{ trans('order::order.stage') }}</span>
                                                </th>
                                                <th><span
                                                        class="userDatatable-title">{{ trans('order::order.created_at') }}</span>
                                                </th>
                                                <th><span class="userDatatable-title">{{ __('common.actions') }}</span>
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

                {{-- Existing Detailed Metrics Section --}}
                <div class="col-lg-8">
                    <div class="chart-container">
                        <h5 class="mb-4">{{ __('accounting.financial_breakdown') }}</h5>

                        <div class="metric-item">
                            <span class="metric-label">{{ __('accounting.gross_revenue') }}</span>
                            <span class="metric-value positive"
                                id="gross-revenue">{{ number_format($summary['total_income'], 2) }}
                                {{ currency() }}</span>
                        </div>

                        <div class="metric-item">
                            <span class="metric-label">{{ __('accounting.operating_expenses') }}</span>
                            <span class="metric-value negative"
                                id="operating-expenses">{{ number_format($summary['total_expenses'], 2) }}
                                {{ currency() }}</span>
                        </div>

                        <div class="metric-item">
                            <span class="metric-label">{{ __('accounting.commission_earned') }}</span>
                            <span class="metric-value positive"
                                id="commission-earned">{{ number_format($summary['total_commissions'], 2) }}
                                {{ currency() }}</span>
                        </div>

                        <div class="metric-item">
                            <span class="metric-label">{{ __('accounting.vendor_payouts') }}</span>
                            <span class="metric-value neutral"
                                id="vendor-payouts">{{ number_format($summary['total_income'] - $summary['total_commissions'], 2) }}
                                {{ currency() }}</span>
                        </div>

                        <div class="metric-item">
                            <span class="metric-label">{{ __('accounting.profit_margin') }}</span>
                            <span
                                class="metric-value {{ $summary['total_income'] > 0 ? ($summary['net_profit'] > 0 ? 'positive' : 'negative') : 'neutral' }}"
                                id="profit-margin">
                                {{ $summary['total_income'] > 0 ? number_format(($summary['net_profit'] / $summary['total_income']) * 100, 1) : '0.0' }}%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="col-lg-4">
                    <div class="chart-container">
                        <h5 class="mb-4">{{ __('accounting.quick_insights') }}</h5>

                        <div class="text-center mb-4">
                            <div
                                class="display-6 fw-bold {{ $summary['total_refunds'] > 0 ? 'text-warning' : 'text-success' }}">
                                {{ number_format($summary['total_refunds'], 2) }} {{ currency() }}
                            </div>
                            <small class="text-muted">{{ __('accounting.total_refunds') }}</small>
                        </div>

                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="h4 mb-1 text-primary">
                                        {{ $summary['total_income'] > 0 ? number_format(($summary['total_commissions'] / $summary['total_income']) * 100, 1) : '0.0' }}%
                                    </div>
                                    <small class="text-muted">{{ __('accounting.avg_commission_rate') }}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h4 mb-1 text-info">
                                    {{ $summary['total_expenses'] > 0 ? number_format(($summary['total_expenses'] / ($summary['total_income'] ?: 1)) * 100, 1) : '0.0' }}%
                                </div>
                                <small class="text-muted">{{ __('accounting.expense_ratio') }}</small>
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
            const monthlyData = @json($summary['monthly_data'] ?? []);

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
                // Profit = Platform Commission - Expenses
                profitData.push((monthlyData[i]?.commissions || 0) - (monthlyData[i]?.expenses || 0));
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: '{{ __('accounting.income') }}',
                        data: incomeData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }, {
                        label: '{{ __('accounting.expenses') }}',
                        data: expenseData,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4
                    }, {
                        label: '{{ __('accounting.profit_loss') }}',
                        data: profitData,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4
                    }, {
                        label: '{{ __('accounting.commissions') }}',
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
                                    return value.toLocaleString() + ' {{ currency() }}';
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
            const deliverStageId = @json(\Modules\Order\app\Models\OrderStage::withoutGlobalScopes()->where('type', 'deliver')->value('id'));

            // Server-side processing with pagination
            const isVendorUser = {{ !isAdmin() ? 'true' : 'false' }};

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
                        return `<span class="fw-bold">${parseFloat(data).toFixed(2)} {{ currency() }}</span>`;
                    }
                }, {
                    data: 'product_stages',
                    name: 'product_stages',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let html = '';
                        
                        // For admin: show vendors with their stages
                        if (!isVendorUser && row.vendors_with_stages && row.vendors_with_stages.length > 0) {
                            row.vendors_with_stages.forEach(function(vendor) {
                                const stageName = vendor.stage?.name || '-';
                                const stageColor = vendor.stage?.color || '#6c757d';
                                html += `<div class="mb-1 d-flex align-items-center">
                                    <img src="${vendor.logo_url}" alt="${vendor.name}" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 6px;">
                                    <span class="me-1" style="font-size: 12px; font-weight: 500;">${vendor.name}:</span>
                                    <span class="badge badge-round" style="background-color: ${stageColor}; color: white; font-size: 11px;">${stageName}</span>
                                </div>`;
                            });
                        } 
                        // For vendor: show their own stage
                        else if (isVendorUser && row.vendor_stage) {
                            const stageName = row.vendor_stage.name || '-';
                            const stageColor = row.vendor_stage.color || '#6c757d';
                            html += `<div class="mb-1"><span class="badge badge-round badge-lg" style="background-color: ${stageColor}; color: white;">${stageName}</span></div>`;
                        }
                        // Fallback to product stages
                        else if (data && data.length > 0) {
                            // Group stages by stage name
                            const stageGroups = {};
                            data.forEach(function(productStage) {
                                const stageName = productStage?.name || '-';
                                const stageColor = productStage?.color || '#6c757d';
                                const stageKey = stageName + '_' + stageColor;
                                
                                if (!stageGroups[stageKey]) {
                                    stageGroups[stageKey] = {
                                        name: stageName,
                                        color: stageColor,
                                        count: 0
                                    };
                                }
                                stageGroups[stageKey].count++;
                            });
                            
                            // Display each unique stage with count
                            Object.values(stageGroups).forEach(function(stage) {
                                html += `<div class="mb-1"><span class="badge badge-round badge-lg" style="background-color: ${stage.color}; color: white;">${stage.name} (${stage.count})</span></div>`;
                            });
                        } else {
                            html += `<div class="mb-1"><span class="badge badge-round badge-lg" style="background-color: #6c757d; color: white;">-</span></div>`;
                        }
                        
                        // Payment Type (Online / COD)
                        const paymentType = row.payment_type || 'cash_on_delivery';
                        const paymentTypeLabel = paymentType === 'online' ? '{{ trans("order::order.online") }}' : '{{ trans("order::order.cod") }}';
                        const paymentTypeColor = paymentType === 'online' ? '#17a2b8' : '#6c757d';
                        html += `<div class="mb-1"><span class="badge badge-round" style="background-color: ${paymentTypeColor}; color: white; font-size: 10px;">{{ trans("order::order.order_type") }}: ${paymentTypeLabel}</span></div>`;
                        
                        // Payment Status (only for online payments)
                        if (paymentType === 'online' && row.payment_visa_status) {
                            let paymentStatusLabel = '';
                            let paymentStatusColor = '';
                            switch(row.payment_visa_status) {
                                case 'success':
                                    paymentStatusLabel = '{{ trans("order::order.payment_success") }}';
                                    paymentStatusColor = '#28a745';
                                    break;
                                case 'pending':
                                    paymentStatusLabel = '{{ trans("order::order.payment_pending") }}';
                                    paymentStatusColor = '#ffc107';
                                    break;
                                case 'fail':
                                case 'failed':
                                    paymentStatusLabel = '{{ trans("order::order.payment_failed") }}';
                                    paymentStatusColor = '#dc3545';
                                    break;
                                default:
                                    paymentStatusLabel = row.payment_visa_status;
                                    paymentStatusColor = '#6c757d';
                            }
                            html += `<div><span class="badge badge-round" style="background-color: ${paymentStatusColor}; color: white; font-size: 10px;">{{ trans("order::order.payment_status") }}: ${paymentStatusLabel}</span></div>`;
                        }
                        
                        return html;
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
                        return `<a href="{{ route('admin.orders.show', ':id') }}" class="btn btn-sm btn-outline-primary">{{ __('common.show') }}</a>`
                            .replace(':id', row.id);
                    }
                });

                // Initialize Orders DataTable
                ordersTable = $('#ordersDataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.orders.datatable') }}',
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
                        processing: "{{ __('common.processing') }}",
                        search: "{{ __('common.search') }}:",
                        lengthMenu: "{{ __('common.show') }} _MENU_ {{ __('common.entries') }}",
                        info: "{{ __('common.showing') }} _START_ {{ __('common.to') }} _END_ {{ __('common.of') }} _TOTAL_ {{ __('common.entries') }}",
                        infoEmpty: "{{ __('common.showing') }} 0 {{ __('common.to') }} 0 {{ __('common.of') }} 0 {{ __('common.entries') }}",
                        infoFiltered: "({{ __('common.filtered_from') }} _MAX_ {{ __('common.total_entries') }})",
                        paginate: {
                            first: "{{ __('common.first') }}",
                            last: "{{ __('common.last') }}",
                            next: "{{ __('common.next') }}",
                            previous: "{{ __('common.previous') }}"
                        },
                        emptyTable: "{{ __('accounting.no_delivered_orders') }}",
                        zeroRecords: "{{ __('accounting.no_delivered_orders') }}"
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
@endsection
