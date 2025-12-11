@extends('layout.app')
@section('title', trans('withdraw::withdraw.send_money_request'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('withdraw::withdraw.send_money_request'), 'url' => route('admin.sendMoneyRequest')],
                    ['title' => trans('withdraw::withdraw.send_money_request')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ trans('withdraw::withdraw.send_money') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        <form id="sendMoneyForm" action="{{ route('admin.sendMoneyRequestAction') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @if (isset($category))
                                @method('PUT')
                            @endif

                            <div class="row">

                                <div class="col-md-12 mb-20">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body fw-bold" style="background-color: #0056b7; color: #fff">
                                                {{ trans('withdraw::withdraw.vendor_general_orders_data') }}
                                            </div>
                                        </div>
                                        <div class="col-12" style="background-color: rgb(201, 201, 201); padding: 10px">
                                            <div class="row">
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="total_orders">{{ $general_info['orders_price'] }}</span>
                                                                        {{ currency() }}</h1>
                                                                    <p>{{ trans('withdraw::withdraw.total_transactions', ['vendor' => $vendor_name]) }}</p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div class="svg-icon order-bg-opacity-info color-info">
                                                                        <i class="uil uil-wallet"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="bnaia_balance">{{ $general_info['bnaia_balance'] }}</span>
                                                                        {{ currency() }}</h1>
                                                                    <p style="font-size: 11px !important;">{{ trans('withdraw::withdraw.bnaia_commission_from_transactions') }}
                                                                    </p>
                                                                </div>
                                                                {{-- <div class="ap-po-details__icon-area">
                                                                    <div class="svg-icon order-bg-opacity-primary color-primary"
                                                                        style="width: 50px; height: 50px;">
                                                                        <i class="uil uil-export"></i>
                                                                    </div>
                                                                </div> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="vendor_balance_money">{{ $general_info['total_vendor_balance'] }}</span>
                                                                        {{ currency() }}</h1>
                                                                    <p>{{ trans('withdraw::withdraw.total_credit', ['vendor' => $vendor_name]) }}</p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div
                                                                        class="svg-icon order-bg-opacity-secondary color-secondary">
                                                                        <i class="uil uil-money-bill-stack"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-12">
                                        <div class="card" style="background-color: #0056b7; color: #fff">
                                            <div class="card-body fw-bold">
                                                {{ trans('dashboard.vendors_withdraw_transactions') }}
                                            </div>
                                        </div>
                                        <div class="col-12" style="background-color: rgb(201, 201, 201); padding: 10px">
                                            <div class="row">
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="vendor_balance_after_sent_money">{{ $general_info['total_vendor_balance'] }}</span>
                                                                        {{ currency() }}</h1>
                                                                    <p>{{ trans('withdraw::withdraw.total_balance_needed') }}</p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div class="svg-icon order-bg-opacity-info color-info">
                                                                        <i class="uil uil-wallet"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="total_sent_money">{{ $general_info['total_sent_money'] }}</span>
                                                                        {{ currency() }}</h1>
                                                                    <p>{{ trans('withdraw::withdraw.total_received_money') }}</p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div
                                                                        class="svg-icon order-bg-opacity-primary color-primary">
                                                                        <i class="uil uil-export"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div
                                                        class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                                                        <div class="overview-content w-100">
                                                            <div
                                                                class="ap-po-details-content d-flex flex-wrap justify-content-between">
                                                                <div class="ap-po-details__titlebar">
                                                                    <h1 style="font-size: 20px;"><span
                                                                            id="remaining_after_sent_money">{{ $general_info['remaining'] }}</span>
                                                                        {{ currency() }}
                                                                    </h1>
                                                                    <p>{{ trans('withdraw::withdraw.total_remaining') }}</p>
                                                                </div>
                                                                <div class="ap-po-details__icon-area">
                                                                    <div
                                                                        class="svg-icon order-bg-opacity-secondary color-secondary">
                                                                        <i class="uil uil-money-bill-stack"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-md-12 mb-10">
                                    <div class="form-group">
                                        <label class="mb-3">
                                            {{ trans('withdraw::withdraw.enter_amount') }} <span class="text-danger">*</span> <span
                                                class="badge text-bg-secondary"
                                                style="background-color: #0056b7; border-radius: 5px"><span
                                                    id="amount_max_which_will_be_sent">{{ $general_info['remaining'] }}</span>
                                                <span style="margin: 0px 4px">{{ currency() }}</span></span>

                                            <span class="badge text-bg-secondary"
                                                style="background-color: #fa0000; border-radius: 5px"> <span
                                                    style="margin: 0px 3px">{{ trans('withdraw::withdraw.waiting_approve') }}:</span> <span
                                                    id="amount_max_which_will_be_sent">{{ $general_info['waiting_approve_requests'] }}</span>
                                                <span style="margin: 0px 4px">{{ currency() }}</span></span>
                                        </label>
                                        <input type="text" class="form-control" placeholder="{{ trans('withdraw::withdraw.amount_placeholder') }}"
                                            name="sent_amount" id="sent_amount" value="{{ old('sent_amount') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-15 mt-30">
                                <a href="{{ route('admin.category-management.categories.index') }}"
                                    class="btn btn-light btn-default btn-squared fw-400 text-capitalize">
                                    <i class="uil uil-angle-left"></i> {{ __('common.cancel') }}
                                </a>
                                <button type="submit" id="submitBtn"
                                    class="btn btn-primary btn-default btn-squared text-capitalize"
                                    style="display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="uil uil-check"></i>
                                    <span>{{ trans('withdraw::withdraw.send_money_request') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" style="background-color: #0056b7; color: #fff">
                    <h5 class="modal-title d-flex align-items-center" id="confirmSubmitLabel" style="color: #fff">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ trans('withdraw::withdraw.confirm_submission') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-0" style="font-size: 16px;">{{ trans('withdraw::withdraw.confirm_send_money') }}
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" id="confirmSubmitBtn" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i> {{ trans('withdraw::withdraw.yes_send') }}
                    </button>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')
        <script>
            let maxVal = {{ $final_remaining }};

            $('#sent_amount').on('input', function() {
                // إزالة أي formatting
                let val = parseFloat(this.value.replace(/,/g, ''));

                if (!isNaN(val) && val > maxVal) {
                    alert('{{ trans("withdraw::withdraw.amount_exceeds_maximum") }}' + ' (' + maxVal + ')');
                    this.value = maxVal.toLocaleString(); // optional formatting
                }
            });
        </script>
        <script>
            function getVendorBalance(vendor_id) {
                let vendor_id = $("#vendor_id").value();
                let url = "{{ route('admin.getVendorBalance', ':vendor_id') }}";
                url = url.replace(':vendor_id', vendor_id);

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(response) {
                        $("#total_orders").html(response.orders_price);
                        $("#bnaia_balance").html(response.bnaia_balance);
                        $("#vendor_commission_percentage").html(response.vendor_commission + "%");
                        $("#vendor_balance_money").html(response.total_vendor_balance);

                        $("#vendor_balance_after_sent_money").html(response.total_vendor_balance);
                        $("#total_sent_money").html(response.total_sent_money);
                        $("#remaining_after_sent_money").html(response.remaining);

                        $("#amount_max_which_will_be_sent").html(response.remaining);

                        // تحويل الرقم اللي فيه commas لصافي رقم
                        let maxAmount = Number(response.remaining.replace(/,/g, ''));

                        // تعيين Max للـ input
                        $("#sent_amount").attr("max", maxAmount);
                    },
                    error: function(xhr, status, error) {
                        console.log("Error:", error);
                    }
                });
            }
        </script>

        <script>
            document.getElementById('imageInput').addEventListener('change', function(event) {
                let file = event.target.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        let img = document.getElementById('imagePreview');
                        img.src = e.target.result;
                        img.style.display = 'block';
                    };

                    reader.readAsDataURL(file);
                }
            });
        </script>

        <script>
            const input = document.getElementById('sent_amount');

            input.addEventListener('input', function(e) {
                let value = this.value.replace(/,/g, ''); // إزالة الفواصل

                // السماح بالأرقام العشرية فقط
                if (!isNaN(value) && value !== '') {
                    // تحويل الرقم لعدد عشري والحفاظ على decimals
                    let parts = value.split('.');
                    parts[0] = Number(parts[0]).toLocaleString(); // جزء الألف
                    this.value = parts.join('.');
                } else {
                    this.value = '';
                }
            });

            // إزالة الفواصل قبل إرسال الفورم
            input.form?.addEventListener('submit', function() {
                input.value = input.value.replace(/,/g, '');
            });
        </script>

        <script>
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');

            // لما المستخدم يغير الصورة
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });

            // لما المستخدم يضغط على الصورة، يفتح اختيار الصورة
            imagePreview.addEventListener('click', function() {
                imageInput.click();
            });
        </script>

        <script>
            const form = document.getElementById('sendMoneyForm');
            const sentAmountInput = document.getElementById('sent_amount');

            form.addEventListener('submit', function(e) {
                // الرقم اللي فيه commas نحوله لصافي رقم
                let maxAmount = Number(sentAmountInput.attr('max') || 0);
                let enteredAmount = Number(sentAmountInput.value.replace(/,/g, ''));

                if (enteredAmount > maxAmount) {
                    e.preventDefault(); // منع الفورم من الsubmit
                    alert("{{ trans('withdraw::withdraw.amount_cannot_exceed_maximum') }}: " + maxAmount.toLocaleString());
                    return false;
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('sendMoneyForm');
                const submitBtn = document.getElementById('submitBtn');
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
                const confirmBtn = document.getElementById('confirmSubmitBtn');

                // منع الفورم من الsubmit مباشرة
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    confirmModal.show();
                });

                // لما يضغط على Yes, Send
                confirmBtn.addEventListener('click', function() {
                    confirmModal.hide();
                    form.submit(); // الفورم يتبعت
                });
            });
        </script>
    @endpush
@endsection
