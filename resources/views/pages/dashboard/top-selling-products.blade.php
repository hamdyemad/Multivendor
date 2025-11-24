<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500">{{ trans('dashboard.top_selling_products') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.product_name') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.vendor_name') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.sold_count') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.price') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.total') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.commission') }} {{ __('common.egp') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.actions') }}</span></th>
                    </tr>
                </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <a href="">
                                        <img class="rounded-circle"
                                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4lh5HPNDdbs0EeH50dYsH0envY10K99paRg&s"
                                            alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="ms-3">Product 1</span>
                                    </a>
                                </td>
                                <td>
                                    <a href="">
                                        <img class="rounded-circle"
                                            src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Fresh_Electric.jpg"
                                            alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="ms-3">Fresh</span>
                                    </a>
                                </td>
                                <td>156</td>
                                <td>89.99 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">14,038.44 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">1,403.844 {{ __('common.egp') }}</td>
                                <td class="actions">
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="uil uil-eye m-0"></i>

                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <img class="rounded-circle"
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRH4CbBiARYvpPP6dEGTT_cSind4K9Z2fk6pA&s"
                                        alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Product 2</span>
                                </td>
                                <td>
                                    <a href="">
                                        <img class="rounded-circle"
                                            src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Fresh_Electric.jpg"
                                            alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="ms-3">Fresh</span>
                                    </a>
                                </td>
                                <td>234</td>
                                <td>299.00 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">69,966.00 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">6,996.6 {{ __('common.egp') }}</td>
                                <td class="actions">
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="uil uil-eye m-0"></i>

                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <img class="rounded-circle"
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_hxP_PNuc7iNuHmvJsUyXg8HsPf7H-iFE4Q&s"
                                        alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Product 3</span>
                                </td>
                                <td>
                                    <a href="">
                                        <img class="rounded-circle"
                                            src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Fresh_Electric.jpg"
                                            alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="ms-3">Fresh</span>
                                    </a>
                                </td>
                                <td>189</td>
                                <td>45.50 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">8,599.50 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">859.95 {{ __('common.egp') }}</td>
                                <td class="actions">
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="uil uil-eye m-0"></i>

                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
                                    <img class="rounded-circle"
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_hxP_PNuc7iNuHmvJsUyXg8HsPf7H-iFE4Q&s"
                                        alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Product 4</span>
                                </td>
                                <td>
                                    <a href="">
                                        <img class="rounded-circle"
                                            src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Fresh_Electric.jpg"
                                            alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="ms-3">Fresh</span>
                                    </a>
                                </td>
                                <td>445</td>
                                <td>19.99 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">8,895.55 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">889.555 {{ __('common.egp') }}</td>
                                <td class="actions">
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="uil uil-eye m-0"></i>

                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>
                                    <img class="rounded-circle"
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_hxP_PNuc7iNuHmvJsUyXg8HsPf7H-iFE4Q&s"
                                        alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Product 5</span>
                                </td>
                                <td>
                                    <a href="">
                                        <img class="rounded-circle"
                                            src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Fresh_Electric.jpg"
                                            alt="product" style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="ms-3">Fresh</span>
                                    </a>
                                </td>
                                <td>98</td>
                                <td>129.99 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">12,739.02 {{ __('common.egp') }}</td>
                                <td class="fw-bold text-success">1,273.902 {{ __('common.egp') }}</td>
                                <td class="actions">
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="uil uil-eye m-0"></i>

                                    </a>
                                </td>
                            </tr>
                        </tbody>
            </table>
        </div>
    </div>
</div>
