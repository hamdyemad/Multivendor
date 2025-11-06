<div class="col-12 col-md-6 mb-25">
    <div class="card border-0 px-25">
        <div class="card-header px-0 border-0">
            <h6 style="font-weight: bold">Earnings</h6>
            <div class="card-extra">
                <ul class="card-tab-links nav-tabs nav" role="tablist">
                    <li>
                        <a href="#earnings-today" data-bs-toggle="tab" id="earnings-today-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.today') }}</a>
                    </li>
                    <li>
                        <a href="#earnings-week" data-bs-toggle="tab" id="earnings-week-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.week') }}</a>
                    </li>
                    <li>
                        <a class="active" href="#earnings-month" data-bs-toggle="tab" id="earnings-month-tab"
                            role="tab" aria-selected="true" style="font-size: 12px">{{ trans('dashboard.month') }}</a>
                    </li>
                    <li>
                        <a href="#earnings-year" data-bs-toggle="tab" id="earnings-year-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">Year</a>
                    </li>
                    <li>
                        <a href="#earnings-5years" data-bs-toggle="tab" id="earnings-5years-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">Latest 5 Years</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0 pb-25">
            <div class="tab-content">
                <div class="tab-pane" id="earnings-today" role="tabpanel"
                    aria-labelledby="earnings-today-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="earningsToday"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="earnings-week" role="tabpanel"
                    aria-labelledby="earnings-week-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="earningsWeek"></canvas>
                    </div>
                </div>
                <div class="tab-pane active show" id="earnings-month" role="tabpanel"
                    aria-labelledby="earnings-month-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="earningsMonth"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="earnings-year" role="tabpanel"
                    aria-labelledby="earnings-year-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="earningsYear"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="earnings-5years" role="tabpanel"
                    aria-labelledby="earnings-5years-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="earnings5Years"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
