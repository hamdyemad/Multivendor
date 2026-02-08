@extends('layout.app')

@section('title', __('order::request-quotation.quotation_details'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard', ['lang' => app()->getLocale(), 'countryCode' => request()->route('countryCode') ?? 'eg']),
                        'icon' => 'uil uil-estate',
                    ],
                    [
                        'title' => __('order::request-quotation.my_quotations'),
                        'url' => route('admin.vendor.request-quotations.index', ['lang' => app()->getLocale(), 'countryCode' => request()->route('countryCode') ?? 'eg']),
                    ],
                    ['title' => __('order::request-quotation.quotation_details')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h5 class="mb-0 fw-600">
                                <i class="uil uil-file-question-alt me-2"></i>
                                {{ __('order::request-quotation.quotation_details') }}
                            </h5>
                            <span class="badge badge-{{ $quotationVendor->status_badge_color }} badge-lg badge-round">
                                {{ $quotationVendor->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        {{-- Customer Information --}}
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="uil uil-user me-1"></i>
                                {{ __('order::request-quotation.customer_information') }}
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>{{ __('order::request-quotation.name') }}:</strong> {{ $quotationVendor->requestQuotation->customer_name }}</p>
                                    <p class="mb-2"><strong>{{ __('order::request-quotation.email') }}:</strong> {{ $quotationVendor->requestQuotation->customer_email }}</p>
                                    <p class="mb-0"><strong>{{ __('order::request-quotation.phone') }}:</strong> {{ $quotationVendor->requestQuotation->customer_phone }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>{{ __('common.address') }}:</strong> {{ $quotationVendor->requestQuotation->full_address }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Quotation Notes --}}
                        @if($quotationVendor->requestQuotation->notes)
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="uil uil-notes me-1"></i>
                                    {{ __('order::request-quotation.notes') }}
                                </h6>
                                <p class="mb-0">{{ $quotationVendor->requestQuotation->notes }}</p>
                            </div>
                        @endif

                        {{-- Attached File --}}
                        @if($quotationVendor->requestQuotation->file)
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="uil uil-file me-1"></i>
                                    {{ __('order::request-quotation.attached_file') }}
                                </h6>
                                <a href="{{ asset('storage/' . $quotationVendor->requestQuotation->file) }}" 
                                   class="btn btn-sm btn-info" download>
                                    <i class="uil uil-download-alt me-1"></i>
                                    {{ __('order::request-quotation.download_file') }}
                                </a>
                            </div>
                        @endif

                        {{-- Your Offer --}}
                        @if($quotationVendor->offer_price)
                            <div class="mb-4">
                                <h6 class="text-success mb-3">
                                    <i class="uil uil-envelope-send me-1"></i>
                                    {{ __('order::request-quotation.your_offer') }}
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>{{ __('order::request-quotation.offer_price') }}:</strong> 
                                            <span class="text-success fw-bold">{{ number_format($quotationVendor->offer_price, 2) }} {{ currency() }}</span>
                                        </p>
                                        <p class="mb-0"><strong>{{ __('order::request-quotation.offer_sent_at') }}:</strong> 
                                            {{ $quotationVendor->offer_sent_at ? $quotationVendor->offer_sent_at->format('Y-m-d H:i') : '-' }}
                                        </p>
                                    </div>
                                    @if($quotationVendor->offer_notes)
                                        <div class="col-md-6">
                                            <p class="mb-0"><strong>{{ __('order::request-quotation.offer_notes') }}:</strong> {{ $quotationVendor->offer_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Order Information --}}
                        @if($quotationVendor->order)
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="uil uil-file-alt me-1"></i>
                                    {{ __('order::request-quotation.order_information') }}
                                </h6>
                                <p class="mb-0">
                                    <strong>{{ __('order::request-quotation.order_number') }}:</strong>
                                    <a href="{{ route('admin.orders.show', ['lang' => app()->getLocale(), 'countryCode' => request()->route('countryCode') ?? 'eg', 'order' => $quotationVendor->order_id]) }}" class="text-primary fw-500">
                                        {{ $quotationVendor->order->order_number }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.vendor.request-quotations.index', ['lang' => app()->getLocale(), 'countryCode' => request()->route('countryCode') ?? 'eg']) }}" class="btn btn-light">
                                <i class="uil uil-arrow-left"></i> {{ __('common.back') }}
                            </a>
                            
                            @if(!$quotationVendor->order_id)
                                <a href="{{ route('admin.orders.create', ['lang' => app()->getLocale(), 'countryCode' => request()->route('countryCode') ?? 'eg', 'quotation_vendor_id' => $quotationVendor->id]) }}" class="btn btn-success">
                                    <i class="uil uil-file-plus"></i> {{ __('order::request-quotation.send_offer') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // No JavaScript needed - button now redirects to order creation page
    </script>
@endpush
