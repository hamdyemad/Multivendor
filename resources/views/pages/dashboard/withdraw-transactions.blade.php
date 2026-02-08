@php
    $user_type_id = auth()->user()->user_type_id;
    $user_type = auth()->user()->user_type->name;
    $vendor = auth()->user()->vendor;

    // Calculate withdraw statistics
    $totalNeeded = 0;
    $totalSentMoney = 0;
    $totalRemaining = 0;
    $ordersPrice = 0;
    $bnaiaBalance = 0;
    $totalVendorBalance = 0;

    if (isAdmin()) {
        // Get current country_id from session
        $countryCode = session('country_code', 'eg');
        $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
        
        // For admin: use optimized aggregate queries instead of loading all vendors
        $statistics = \Modules\Vendor\app\Models\Vendor::getVendorsStatistics($countryId);
        
        // Get total vendor balance (after commission)
        $totalVendorBalance = (float) str_replace(',', '', $statistics['total_balance']);
        
        // Get total commission from statistics
        $totalCommission = (float) str_replace(',', '', $statistics['total_commission'] ?? '0');
        
        // Get total transactions (this is the actual delivered orders value)
        $ordersPrice = (float) str_replace(',', '', $statistics['total_transactions'] ?? '0');
        
        // Bnaia commission is the same as total commission
        $bnaiaBalance = $totalCommission;
        
        $totalNeeded = $totalVendorBalance;
        $totalSentMoney = (float) str_replace(',', '', $statistics['total_sent']);
        $totalRemaining = (float) str_replace(',', '', $statistics['total_remaining']);
        
    } else {
        // For vendor: get their own totals from model
        if (!$vendor) {
            $vendor = \Modules\Vendor\app\Models\Vendor::where('user_id', auth()->user()->vendor_id)->first();
        }
        
        if ($vendor) {
            $ordersPrice = $vendor->orders_price;
            $bnaiaBalance = max(0, $vendor->bnaia_commission);
            $totalVendorBalance = $vendor->total_balance;
            
            $totalNeeded = $vendor->total_balance;
            $totalSentMoney = $vendor->total_sent;
            $totalRemaining = $vendor->total_remaining;
        }
    }
@endphp

<div class="col-12">
    <div class="col-12">
        {{-- Vendor General Orders Data --}}
        <div class="card mb-2">
            <div class="card-body fw-bold">
                @if (isAdmin())
                    {{ trans('withdraw::withdraw.vendors_general_orders_data') }}
                @else
                    {{ trans('withdraw::withdraw.vendor_general_orders_data') }}
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1>{{ formatCurrency($ordersPrice) }} {{ currency() }}</h1>
                                <p>{{ trans('withdraw::withdraw.total_transactions') }}</p>
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
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1>{{ formatCurrency($bnaiaBalance) }} {{ currency() }}</h1>
                                <p>{{ trans('withdraw::withdraw.bnaia_commission_from_transactions') }}</p>
                            </div>
                            <div class="ap-po-details__icon-area">
                                <div class="svg-icon order-bg-opacity-primary color-primary">
                                    <i class="uil uil-percentage"></i>
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
                                <h1>{{ formatCurrency($totalVendorBalance) }} {{ currency() }}</h1>
                                <p>{{ trans('withdraw::withdraw.total_vendor_credit') }}</p>
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
        
        {{-- Vendors Withdraw Transactions --}}
        <div class="card mb-2">
            <div class="card-body fw-bold">
                {{ trans('dashboard.vendors_withdraw_transactions') }}
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1>{{ formatCurrency($totalVendorBalance) }} {{ currency() }}</h1>
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
            <div class="col-12 col-md-4 mb-25">
                <div class="ap-po-details ap-po-details--2 p-25 radius-xl d-flex justify-content-between">
                    <div class="overview-content w-100">
                        <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                            <div class="ap-po-details__titlebar">
                                <h1>{{ formatCurrency($totalSentMoney) }} {{ currency() }}</h1>
                                @if (isAdmin())
                                    <p>{{ trans('dashboard.Total Sent Money To Vendors') }}</p>
                                @else
                                    <p>{{ trans('dashboard.total_received_money') }}</p>
                                @endif
                            </div>
                            <div class="ap-po-details__icon-area">
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
                                <h1>{{ formatCurrency($totalRemaining) }} {{ currency() }}</h1>
                                <p>{{ trans('dashboard.Total Vendor\'s Remaining') }}</p>
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
