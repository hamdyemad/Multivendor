@php
    $user_type_id = auth()->user()->user_type_id;
    $user_type = auth()->user()->user_type->name;
    $vendor = auth()->user()->vendor;

    // Calculate withdraw statistics
    $totalNeeded = 0;
    $totalSentMoney = 0;
    $totalRemaining = 0;

    if (in_array($user_type_id, \App\Models\UserType::adminIds())) {
        // For admin: calculate totals for all vendors
        $allVendors = \Modules\Vendor\app\Models\Vendor::all();

        foreach ($allVendors as $v) {
            $orders = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $v->id)->get();
            if ($orders->count() > 0) {
                $commission = $orders->first()->commission ?? 0;
                $vendorBalance = $orders->sum('price') - ($orders->sum('price') * ($commission / 100));
                $totalNeeded += $vendorBalance;
            }

            $sentMoney = \Modules\Withdraw\app\Models\Withdraw::where('reciever_id', $v->id)
                ->where('status', 'accepted')
                ->sum('sent_amount');
            $totalSentMoney += $sentMoney;
        }

        $totalRemaining = $totalNeeded - $totalSentMoney;
    } else {
        // For vendor: calculate their own totals
        if ($vendor) {
            $totalNeeded = $vendor->total_balance;
            $totalSentMoney = $vendor->total_sent_money;
            $totalRemaining = $vendor->total_remaining;
        }
    }
@endphp

<div class="col-12">
    <div class="card mb-2">
        <div class="card-body fw-bold">
            @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                {{ trans('dashboard.vendors_withdraw_transactions') }}
            @else
                {{ trans('dashboard.withdraw_transactions') }} {{ $vendor->translations->first()->lang_value }}
            @endif

        </div>
    </div>
    <div class="col-12">
        <div class="row">
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1>{{ number_format($totalNeeded, 2) }} {{ __('common.egp') }}</h1>
                                <p>{{ trans('dashboard.Total Needed From Bnaia To Vendors') }}</p>
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
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between" style="flex-direction: row;">
                            <div class="ap-po-details__titlebar" style="order: 1;">
                                <h1>{{ number_format($totalSentMoney, 2) }} {{ __('common.egp') }}</h1>
                                @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                <p>{{ trans('dashboard.Total Sent Money To Vendors') }}</p>
                                @else
                                 <p>{{ trans('dashboard.total_received_money') }}</p>
                                @endif
                            </div>
                            <div class="ap-po-details__icon-area" style="order: 2;">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-export"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1>{{ number_format($totalRemaining, 2) }} {{ __('common.egp') }}</h1>
                                @if (in_array($user_type_id, \App\Models\UserType::adminIds()))
                                    <p>{{ trans('dashboard.Total Vendor\'s Remaining') }}</p>
                                @else
                                    <p>{{ $vendor->translations->first()->lang_value }}'s {{ trans('dashboard.credit_balance') }}</p>
                                @endif
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-secondary color-secondary">
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
