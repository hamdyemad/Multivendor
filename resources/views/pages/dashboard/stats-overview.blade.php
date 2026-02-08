@push('styles')
    <style>
        .overview-content h1 {
            font-size: 18px;
        }
    </style>
@endpush

<div class="col-12">
    <div class="card mb-2">
        <div class="card-body fw-bold">
            {{ trans('dashboard.sales_overview') }}
        </div>
    </div>
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1 style="">{{ formatCurrency($salesOverview['total_expenses'] ?? 0) }} {{ currency() }}</h1>
                                <p>{{ trans('dashboard.Total Expenses') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-danger color-danger">
                                    <i class="uil uil-arrow-circle-up"></i>
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
                                <h1 style="">{{ formatCurrency($salesOverview['total_income'] ?? 0) }} {{ currency() }}</h1>
                                <p>{{ trans('dashboard.Total Income (After Delivery)') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-success color-success">
                                    <i class="uil uil-arrow-circle-down"></i>
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
                                <h1 style="">{{ formatCurrency($salesOverview['net_profit_ytd'] ?? 0) }} {{ currency() }}</h1>
                                <p>{{ trans('dashboard.Net Profit Y.T.D') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-secondary color-secondary">
                                    <i class="uil uil-chart-growth"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card mb-2">
        <div class="card-body fw-bold">
            {{ trans('dashboard.orders_and_revenue_statistics') }}
        </div>
    </div>
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1 style="">{{ formatCurrency($salesOverview['revenue_ytd'] ?? 0) }} {{ currency() }}</h1>
                                <p>{{ trans('dashboard.Revenue Y.T.D') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-info color-info">
                                    <i class="uil uil-dollar-sign"></i>
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
                                <h1 style="">{{ $salesOverview['new_orders_count'] ?? 0 }}</h1>
                                <p>{{ trans('dashboard.Total New Orders') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-shopping-cart-alt"></i>
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
                                <h1 style="">{{ $salesOverview['in_progress_orders_count'] ?? 0 }}</h1>
                                <p>{{ trans('dashboard.Total In Progress Orders') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-secondary color-secondary">
                                    <i class="uil uil-receipt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
