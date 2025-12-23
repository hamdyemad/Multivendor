<div class="col-12 mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-500">{{ trans('dashboard.orders_overview') }}</h6>
        </div>

        <div class="card-body">
            <div class="revenueSourceChart">
                <div class="parentContainer position-relative mb-4" style="height: 250px;">
                    <canvas id="ordersOverviewChart"></canvas>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle me-3" style="background-color: rgba(91, 105, 255, 0.15); width: 48px; height: 48px; min-width: 48px;">
                                    <i class="uil uil-shopping-bag fs-4" style="color: #5b69ff;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ trans('dashboard.new') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ $ordersOverview['new'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle me-3" style="background-color: rgba(255, 193, 7, 0.15); width: 48px; height: 48px; min-width: 48px;">
                                    <i class="uil uil-clock fs-4" style="color: #ffc107;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ trans('dashboard.in_progress') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ $ordersOverview['in_progress'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle me-3" style="background-color: rgba(32, 201, 151, 0.15); width: 48px; height: 48px; min-width: 48px;">
                                    <i class="uil uil-check-circle fs-4" style="color: #20c997;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ trans('dashboard.delivered') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ $ordersOverview['delivered'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle me-3" style="background-color: rgba(255, 76, 81, 0.15); width: 48px; height: 48px; min-width: 48px;">
                                    <i class="uil uil-times-circle fs-4" style="color: #ff4c51;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ trans('dashboard.cancelled') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ $ordersOverview['cancelled'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle me-3" style="background-color: rgba(133, 97, 197, 0.15); width: 48px; height: 48px; min-width: 48px;">
                                    <i class="uil uil-money-bill fs-4" style="color: #8561c5;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ trans('dashboard.refunded') }}</p>
                                    <h5 class="mb-0 fw-bold">{{ $ordersOverview['refunded'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
