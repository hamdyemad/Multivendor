<div class="col-xxl-8 mb-25">
    <div class="card border-0 px-25">
        <div class="card-header px-0 border-0">
            <h6>{{ trans('dashboard.best_customers') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="selling-table-wrap">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('dashboard.name') }}</th>
                                <th>{{ trans('dashboard.orders_count') }}</th>
                                <th>{{ trans('dashboard.revenue') }}</th>
                                <th>{{ trans('dashboard.joined_at') }}</th>
                                <th>{{ trans('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">John Smith</span>
                                </td>
                                <td>45</td>
                                <td class="fw-bold text-success">12,450.00 EGP</td>
                                <td>Jan 15, 2024</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-light">
                                        <i class="uil uil-eye"></i>
                                        <span>{{ trans('dashboard.show') }}</span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Sarah Johnson</span>
                                </td>
                                <td>38</td>
                                <td class="fw-bold text-success">10,890.00 EGP</td>
                                <td>Feb 22, 2024</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-light">
                                        <i class="uil uil-eye"></i>
                                        <span>{{ trans('dashboard.show') }}</span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Michael Brown</span>
                                </td>
                                <td>32</td>
                                <td class="fw-bold text-success">9,560.00 EGP</td>
                                <td>Mar 10, 2024</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-light">
                                        <i class="uil uil-eye"></i>
                                        <span>{{ trans('dashboard.show') }}</span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Emily Davis</span>
                                </td>
                                <td>28</td>
                                <td class="fw-bold text-success">8,340.00 EGP</td>
                                <td>Apr 05, 2024</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-light">
                                        <i class="uil uil-eye"></i>
                                        <span>{{ trans('dashboard.show') }}</span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">David Wilson</span>
                                </td>
                                <td>25</td>
                                <td class="fw-bold text-success">7,750.00 EGP</td>
                                <td>May 18, 2024</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-light">
                                        <i class="uil uil-eye"></i>
                                        <span>{{ trans('dashboard.show') }}</span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
