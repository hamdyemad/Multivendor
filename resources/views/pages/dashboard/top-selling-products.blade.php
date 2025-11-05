<div class="col-xxl-8 mb-25">
    <div class="card border-0 px-25">
        <div class="card-header px-0 border-0">
            <h6>{{ trans('dashboard.top_selling_products') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="selling-table-wrap">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('dashboard.product_name') }}</th>
                                <th>{{ trans('dashboard.sold_count') }}</th>
                                <th>{{ trans('dashboard.price') }}</th>
                                <th>{{ trans('dashboard.total') }}</th>
                                <th>{{ trans('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Wireless Headphones</span>
                                </td>
                                <td>156</td>
                                <td>89.99 EGP</td>
                                <td class="fw-bold text-success">14,038.44 EGP</td>
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
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Smart Watch Pro</span>
                                </td>
                                <td>234</td>
                                <td>299.00 EGP</td>
                                <td class="fw-bold text-success">69,966.00 EGP</td>
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
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Laptop Stand</span>
                                </td>
                                <td>189</td>
                                <td>45.50 EGP</td>
                                <td class="fw-bold text-success">8,599.50 EGP</td>
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
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">USB-C Cable Pack</span>
                                </td>
                                <td>445</td>
                                <td>19.99 EGP</td>
                                <td class="fw-bold text-success">8,895.55 EGP</td>
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
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Mechanical Keyboard</span>
                                </td>
                                <td>98</td>
                                <td>129.99 EGP</td>
                                <td class="fw-bold text-success">12,739.02 EGP</td>
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
