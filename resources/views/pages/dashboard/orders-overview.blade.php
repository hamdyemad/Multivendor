<div class="col-12 mb-4">
    <div class="card border-0 p-3 h-100">
        <div class="card-header border-0 pb-0">
            <h6>{{ trans('dashboard.orders_overview') }}</h6>
        </div>

        <style>
            .chart-content__details {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }

            .chart-content__single {
                background: #fafafa;
                border-radius: 10px;
                padding: 0.20rem;
            }

            @media (max-width: 576px) {
                .parentContainer {
                    height: 200px !important;
                }
            }
        </style>

        <div class="card-body p-0">
            <div class="revenueSourceChart">
                <div class="parentContainer position-relative" style="height: 200px;">
                    <canvas id="ordersOverviewChart"></canvas>
                </div>

                <div class="chart-content__details mt-3 d-flex flex-wrap gap-3">
                    <div class="chart-content__single d-flex align-items-center flex-grow-1 flex-sm-fill">
                        <span class="icon d-flex align-items-center justify-content-center rounded-circle me-2"
                            style="background-color: rgba(91, 105, 255, 0.2); width: 36px; height: 36px;">
                            <span class="uil uil-shopping-bag" style="color: #5b69ff;"></span>
                        </span>
                        <div>
                            <span class="label d-block small">{{ trans('dashboard.new') }}</span>
                            <span class="data fw-bold">2</span>
                        </div>
                    </div>

                    <div class="chart-content__single d-flex align-items-center flex-grow-1 flex-sm-fill">
                        <span class="icon d-flex align-items-center justify-content-center rounded-circle me-2"
                            style="background-color: rgba(255, 193, 7, 0.2); width: 36px; height: 36px;">
                            <span class="uil uil-clock" style="color: #ffc107;"></span>
                        </span>
                        <div>
                            <span class="label d-block small">{{ trans('dashboard.in_progress') }}</span>
                            <span class="data fw-bold">2</span>
                        </div>
                    </div>

                    <div class="chart-content__single d-flex align-items-center flex-grow-1 flex-sm-fill">
                        <span class="icon d-flex align-items-center justify-content-center rounded-circle me-2"
                            style="background-color: rgba(32, 201, 151, 0.2); width: 36px; height: 36px;">
                            <span class="uil uil-check-circle" style="color: #20c997;"></span>
                        </span>
                        <div>
                            <span class="label d-block small">{{ trans('dashboard.delivered') }}</span>
                            <span class="data fw-bold">2</span>
                        </div>
                    </div>

                    <div class="chart-content__single d-flex align-items-center flex-grow-1 flex-sm-fill">
                        <span class="icon d-flex align-items-center justify-content-center rounded-circle me-2"
                            style="background-color: rgba(255, 76, 81, 0.2); width: 36px; height: 36px;">
                            <span class="uil uil-times-circle" style="color: #ff4c51;"></span>
                        </span>
                        <div>
                            <span class="label d-block small">{{ trans('dashboard.cancelled') }}</span>
                            <span class="data fw-bold">3</span>
                        </div>
                    </div>

                    <!-- باقي العناصر -->
                </div>
            </div>
        </div>
    </div>
</div>
