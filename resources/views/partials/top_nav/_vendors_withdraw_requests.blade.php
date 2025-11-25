@php
    $vendor = auth()->user()->vendor;

    $all_transactions = [];

    if ($vendor) {
        $all_transactions = Modules\Withdraw\app\Models\Withdraw::with([
            'vendor' => function ($vendor) {
                $vendor->with('translations')->first();
            },
        ])
            ->whereIn('status', ['accepted', 'rejected'])
            ->where('reciever_id', $vendor->id)
            ->latest()
            ->limit(10)
            ->get();
    } else {
        $all_transactions = Modules\Withdraw\app\Models\Withdraw::with([
            'vendor' => function ($vendor) {
                $vendor->with('translations')->first();
            },
        ])
            ->whereIn('status', ['new'])
            ->latest()
            ->limit(10)
            ->get();
    }
@endphp
<li class="nav-notification">
    <div class="dropdown-custom" style="position: relative;">
        <a href="javascript:;" class="nav-item-toggle icon-active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg">
                <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
            <span class="nav-item__badge"
                style="position: absolute; top: -8px; background-color: #fa8b0c; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; line-height: 1; z-index: 10;"
                dir="ltr">
                <style>[dir="rtl"] .nav-item__badge { left: -8px !important; right: auto !important; } [dir="ltr"]
                .nav-item__badge { right: -8px !important; left: auto !important; }</style>
                {{ count($all_transactions) }}
            </span>
        </a>
        <div class="dropdown-wrapper">
            <h2 class="dropdown-wrapper__title">Vendors Withdraw Requests <span
                    class="badge-circle badge-warning ms-1">{{ count($all_transactions) }}</span></h2>
            <ul>
                @foreach ($all_transactions as $item)
                    <li class="nav-notification__single d-flex flex-wrap">
                        <div class="nav-notification__type nav-notification__type--warning">
                            <i class="uil uil-wallet"></i>
                        </div>
                        <div class="nav-notification__details">
                            @if ($vendor)
                                <p>
                                    <a href="{{ $item->status == "accepted" ? route('admin.transactionsRequests', 'accepted') : route('admin.transactionsRequests', 'rejected') }}" class="subject stretched-link text-truncate"
                                        style="max-width: 180px;">Bnaia is {{ $item->status == "accepted" ? "Sent money to you" : "Reject your request"  }}</a>
                                </p>
                                <p>
                                    <span class="time-posted">Request Value : {{ $item->sent_amount }} {{ __('common.egp') }}</span>
                                </p>
                            @else
                                <p>
                                    <a href="{{ route('admin.transactionsRequests', 'new') }}" class="subject stretched-link text-truncate"
                                        style="max-width: 180px;">{{ $item->vendor->translations->first()->lang_value }} is sent new withdraw request</a>
                                </p>
                                <p>
                                    <span class="time-posted">Request Value : {{ $item->sent_amount }} {{ __('common.egp') }}</span>
                                </p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
            <a href="" class="dropdown-wrapper__more">See All Requests</a>
        </div>
    </div>
</li>
