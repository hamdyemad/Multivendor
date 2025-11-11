<div class="col-xxl-12 mb-25">
    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h4 class="mb-0 fw-500">{{ trans('dashboard.recent_activities') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 table-bordered table-hover" style="width:100%">
                <thead>
                    <tr class="userDatatable-header">
                        <th><span class="userDatatable-title">#</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.employee') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.activity') }}</span></th>
                        <th><span class="userDatatable-title">{{ trans('dashboard.activity_date') }}</span></th>
                    </tr>
                </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="employee" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Ahmed Hassan</span>
                                </td>
                                <td><span class="text-success fw-medium">Added new product</span></td>
                                <td>Oct 21, 2025 10:30 AM</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="employee" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Fatima Ali</span>
                                </td>
                                <td><span class="text-primary fw-medium">Created order #2045</span></td>
                                <td>Oct 21, 2025 09:15 AM</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="employee" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Mohamed Ibrahim</span>
                                </td>
                                <td><span class="text-info fw-medium">Updated customer profile</span></td>
                                <td>Oct 21, 2025 08:45 AM</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="employee" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Sara Mahmoud</span>
                                </td>
                                <td><span class="text-warning fw-medium">Approved vendor request</span></td>
                                <td>Oct 21, 2025 07:20 AM</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>
                                    <img class="rounded-circle" src="{{ asset('/assets/img/author/robert-3.png') }}" alt="employee" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="ms-3">Youssef Khaled</span>
                                </td>
                                <td><span class="text-danger fw-medium">Deleted product</span></td>
                                <td>Oct 20, 2025 11:50 PM</td>
                            </tr>
                        </tbody>
            </table>
        </div>
    </div>
</div>
