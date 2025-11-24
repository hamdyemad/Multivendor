<div class="col-xxl-4 mb-25">
    <div class="card border-0 px-25">
        <div class="card-header px-0 border-0">
            <h6>Sales</h6>
            <div class="card-extra">
                <ul class="card-tab-links nav-tabs nav" role="tablist">
                    <li>
                        <a class="active" href="#sales-today" data-bs-toggle="tab" id="sales-today-tab"
                            role="tab" aria-selected="true">{{ trans('dashboard.today') }}</a>
                    </li>
                    <li>
                        <a href="#sales-week" data-bs-toggle="tab" id="sales-week-tab"
                            role="tab" aria-selected="false">{{ trans('dashboard.week') }}</a>
                    </li>
                    <li>
                        <a href="#sales-month" data-bs-toggle="tab" id="sales-month-tab"
                            role="tab" aria-selected="false">{{ trans('dashboard.month') }}</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0 pb-25">
            <div class="tab-content">
                <div class="tab-pane active show" id="sales-today" role="tabpanel"
                    aria-labelledby="sales-today-tab">
                    <div class="label-detailed label-detailed--two">
                        <div class="label-detailed__single"><strong
                                class="label-detailed__total">8,550 {{ __('common.egp') }}</strong> <span
                                class="label-detailed__status color-success"><img class="svg"
                                    src="img/svg/arrow-success-up.svg" alt="">
                                <strong>25%</strong></span> </div>
                        <div class="label-detailed__single"><strong
                                class="label-detailed__total">5,550 {{ __('common.egp') }}</strong> <span
                                class="label-detailed__status color-danger"><img class="svg"
                                    src="img/svg/arrow-danger-down.svg" alt="">
                                <strong>15%</strong></span> </div>
                    </div>
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="salesToday"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="sales-week" role="tabpanel"
                    aria-labelledby="sales-week-tab">
                    <div class="label-detailed label-detailed--two">
                        <div class="label-detailed__single"><strong
                                class="label-detailed__total">8,550 {{ __('common.egp') }}</strong> <span
                                class="label-detailed__status color-success"><img class="svg"
                                    src="img/svg/arrow-success-up.svg" alt="">
                                <strong>25%</strong></span> </div>
                        <div class="label-detailed__single"><strong
                                class="label-detailed__total">5,550 {{ __('common.egp') }}</strong> <span
                                class="label-detailed__status color-danger"><img class="svg"
                                    src="img/svg/arrow-danger-down.svg" alt="">
                                <strong>15%</strong></span> </div>
                    </div>
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="salesWeek"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="sales-month" role="tabpanel"
                    aria-labelledby="sales-month-tab">
                    <div class="label-detailed label-detailed--two">
                        <div class="label-detailed__single"><strong
                                class="label-detailed__total">8,550 {{ __('common.egp') }}</strong> <span
                                class="label-detailed__status color-success"><img class="svg"
                                    src="img/svg/arrow-success-up.svg" alt="">
                                <strong>25%</strong></span> </div>
                        <div class="label-detailed__single"><strong
                                class="label-detailed__total">5,550 {{ __('common.egp') }}</strong> <span
                                class="label-detailed__status color-danger"><img class="svg"
                                    src="img/svg/arrow-danger-down.svg" alt="">
                                <strong>15%</strong></span> </div>
                    </div>
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="salesMonth"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
