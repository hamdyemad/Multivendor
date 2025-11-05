<div class="col-xxl-6 mb-25">
    <div class="card border-0 px-25 h-100">
        <div class="card-header px-0 border-0">
            <h6>{{ trans('dashboard.orders_overview') }}</h6>
        </div>
        <div class="p-0 card-body">
            <div class="revenueSourceChart px-0">
                <div class="parentContainer position-relative" style="height: 300px;">
                    <canvas id="ordersOverviewChart"></canvas>
                </div>
                <div class="chart-content__details mt-3">
                    <div class="chart-content__single">
                        <span class="icon" style="background-color: rgba(91, 105, 255, 0.2);">
                            <span class="uil uil-shopping-bag" style="color: #5b69ff;"></span>
                        </span>
                        <span class="label">{{ trans('dashboard.new') }}</span>
                        <span class="data">2</span>
                    </div>
                    <div class="chart-content__single">
                        <span class="icon" style="background-color: rgba(255, 193, 7, 0.2);">
                            <span class="uil uil-clock" style="color: #ffc107;"></span>
                        </span>
                        <span class="label">{{ trans('dashboard.in_progress') }}</span>
                        <span class="data">2</span>
                    </div>
                    <div class="chart-content__single">
                        <span class="icon" style="background-color: rgba(32, 201, 151, 0.2);">
                            <span class="uil uil-check-circle" style="color: #20c997;"></span>
                        </span>
                        <span class="label">{{ trans('dashboard.delivered') }}</span>
                        <span class="data">2</span>
                    </div>
                    <div class="chart-content__single">
                        <span class="icon" style="background-color: rgba(255, 76, 81, 0.2);">
                            <span class="uil uil-times-circle" style="color: #ff4c51;"></span>
                        </span>
                        <span class="label">{{ trans('dashboard.cancelled') }}</span>
                        <span class="data">3</span>
                    </div>
                    <div class="chart-content__single">
                        <span class="icon" style="background-color: rgba(255, 152, 0, 0.2);">
                            <span class="uil uil-redo" style="color: #ff9800;"></span>
                        </span>
                        <span class="label">{{ trans('dashboard.want_to_return') }}</span>
                        <span class="data">0</span>
                    </div>
                    <div class="chart-content__single">
                        <span class="icon" style="background-color: rgba(156, 39, 176, 0.2);">
                            <span class="uil uil-sync" style="color: #9c27b0;"></span>
                        </span>
                        <span class="label">{{ trans('dashboard.return_in_progress') }}</span>
                        <span class="data">0</span>
                    </div>
                    <div class="chart-content__single">
                        <span class="icon" style="background-color: rgba(103, 58, 183, 0.2);">
                            <span class="uil uil-money-bill" style="color: #673ab7;"></span>
                        </span>
                        <span class="label">{{ trans('dashboard.refunded') }}</span>
                        <span class="data">1</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
