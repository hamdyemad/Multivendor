@props(['steps', 'currentStep' => 1])

@php
    $isRtl = app()->getLocale() === 'ar' || (isset($currentLanguage) && $currentLanguage === 'ar');
@endphp

<div class="checkout-progress-indicator content-center" @if($isRtl) dir="rtl" @endif>
    <div class="checkout-progress">
        @foreach($steps as $index => $step)
            @if($index > 0)
                <div class="wizard-separator">
                    <svg width="30" height="20" viewBox="0 0 30 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 10H25M25 10L18 3M25 10L18 17" stroke="#ddd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            @endif

            <div class="wizard-step-nav {{ $index + 1 <= $currentStep ? 'current' : '' }} {{ $index + 1 < $currentStep ? 'completed' : '' }}" data-step="{{ $index + 1 }}" style="cursor: pointer;">
                <span class="step-number">
                    @if($index + 1 < $currentStep)
                        <i class="uil uil-check"></i>
                    @else
                        {{ $index + 1 }}
                    @endif
                </span>
                <span class="step-label">{{ $step }}</span>
            </div>
        @endforeach
    </div>
</div>
