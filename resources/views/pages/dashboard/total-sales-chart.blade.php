<div class="col-12 col-md-6 mb-25">
    <div class="card border-0 px-25">
        <div class="card-header px-0 border-0">
            <h6 style="font-weight: bold">{{ trans('dashboard.total_sales') }}</h6>
            <div class="card-extra">
                <ul class="card-tab-links nav-tabs nav" role="tablist">
                    <li>
                        <a href="#totalsales-today" data-bs-toggle="tab" id="totalsales-today-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.today') }}</a>
                    </li>
                    <li>
                        <a href="#totalsales-week" data-bs-toggle="tab" id="totalsales-week-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">{{ trans('dashboard.week') }}</a>
                    </li>
                    <li>
                        <a class="active" href="#totalsales-month" data-bs-toggle="tab" id="totalsales-month-tab"
                            role="tab" aria-selected="true" style="font-size: 12px">{{ trans('dashboard.month') }}</a>
                    </li>
                    <li>
                        <a href="#totalsales-year" data-bs-toggle="tab" id="totalsales-year-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">Year</a>
                    </li>
                    <li>
                        <a href="#totalsales-5years" data-bs-toggle="tab" id="totalsales-5years-tab"
                            role="tab" aria-selected="false" style="font-size: 12px">Latest 5 Years</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0 pb-25">
            <div class="tab-content">
                <div class="tab-pane" id="totalsales-today" role="tabpanel"
                    aria-labelledby="totalsales-today-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="totalSalesToday"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="totalsales-week" role="tabpanel"
                    aria-labelledby="totalsales-week-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="totalSalesWeek"></canvas>
                    </div>
                </div>
                <div class="tab-pane active show" id="totalsales-month" role="tabpanel"
                    aria-labelledby="totalsales-month-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="totalSalesMonth"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="totalsales-year" role="tabpanel"
                    aria-labelledby="totalsales-year-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="totalSalesYear"></canvas>
                    </div>
                </div>
                <div class="tab-pane" id="totalsales-5years" role="tabpanel"
                    aria-labelledby="totalsales-5years-tab">
                    <div class="parentContainer" style="height: 180px;">
                        <canvas id="totalSales5Years"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
