@props(['steps', 'currentStep' => 1])

@php
    $isRtl = app()->getLocale() === 'ar' || (isset($currentLanguage) && $currentLanguage === 'ar');
@endphp

@once
    @push('styles')
        <style>
            /* Wizard Component Styles */

            /* Wizard Step Styling */
            .checkout-progress-indicator {
                padding: 30px 20px;
                margin-bottom: 30px;
                background: linear-gradient(to bottom, #ffffff 0%, #f8f9fc 100%);
                border-radius: 12px;
                overflow-x: auto;
                overflow-y: hidden;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                border: 1px solid #e8e9f0;

                /* Smooth horizontal scrolling */
                &::-webkit-scrollbar {
                    height: 8px;
                }

                &::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }

                &::-webkit-scrollbar-thumb {
                    background: #c1c1c1;
                    border-radius: 10px;
                }

                &::-webkit-scrollbar-thumb:hover {
                    background: #a8a8a8;
                }
            }

            .checkout-progress {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-wrap: nowrap;
                gap: 0;
                position: relative;
                min-width: fit-content;
            }

            .wizard-step-nav {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 20px 25px;
                border-radius: 10px;
                background: #f8f9fa;
                transition: all 0.3s ease;
                min-width: 140px;
                position: relative;
                border: 2px solid transparent;
            }

            .wizard-step-nav .step-number {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: #e0e0e0;
                color: #666;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 20px;
                margin-bottom: 10px;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .wizard-step-nav .step-label {
                font-size: 14px;
                color: #666;
                text-align: center;
                font-weight: 500;
                line-height: 1.4;
            }

            /* Current Step */
            .wizard-step-nav.current {
                background: linear-gradient(135deg, var(--color-primary) 0%, var(--second-primary) 100%);
                border-color: var(--color-primary);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }

            .wizard-step-nav.current .step-number {
                background: #fff;
                color: var(--color-primary);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                animation: pulse 2s infinite;
            }

            .wizard-step-nav.current .step-label {
                color: #fff;
                font-weight: 600;
            }

            /* Completed Step */
            .wizard-step-nav.completed {
                background: #d4edda;
                border-color: #28a745;
            }

            .wizard-step-nav.completed .step-number {
                background: #28a745;
                color: white;
                box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            }

            .wizard-step-nav.completed .step-label {
                color: #155724;
                font-weight: 600;
            }

            /* Hover Effect */
            .wizard-step-nav:hover:not(.completed) {
                background: #e8e9ff;
                transform: translateY(-3px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
                border-color: var(--color-primary);
            }

            .wizard-step-nav:hover:not(.completed) .step-number {
                background: var(--color-primary);
                color: white;
            }

            .wizard-step-nav:hover:not(.completed) .step-label {
                color: var(--color-primary);
            }

            /* Wizard Separator */
            .wizard-separator {
                display: flex;
                align-items: center;
                padding: 0 15px;
            }

            .wizard-separator svg {
                opacity: 0.5;
                transition: all 0.3s ease;
            }

            .wizard-step-nav.completed + .wizard-separator svg path {
                stroke: #28a745;
                opacity: 1;
            }

            /* Pulse Animation */
            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
            }

            /* RTL Support for Arabic */
            [dir="rtl"] .checkout-progress-indicator,
            .checkout-progress-indicator[dir="rtl"],
            html[lang="ar"] .checkout-progress-indicator {
                direction: rtl;
            }

            [dir="rtl"] .wizard-step-nav,
            .checkout-progress-indicator[dir="rtl"] .wizard-step-nav,
            html[lang="ar"] .wizard-step-nav {
                direction: rtl;
            }

            [dir="rtl"] .wizard-step-nav .step-label,
            .checkout-progress-indicator[dir="rtl"] .wizard-step-nav .step-label,
            html[lang="ar"] .wizard-step-nav .step-label {
                direction: rtl;
                text-align: center;
            }

            [dir="rtl"] .wizard-separator svg,
            .checkout-progress-indicator[dir="rtl"] .wizard-separator svg,
            html[lang="ar"] .wizard-separator svg {
                transform: scaleX(-1);
            }

            /* Responsive Design */
            @media (max-width: 992px) {
                .checkout-progress-indicator {
                    padding: 20px 15px;
                    overflow-x: auto;
                    overflow-y: hidden;
                    -webkit-overflow-scrolling: touch;
                }

                .checkout-progress {
                    min-width: 600px;
                    justify-content: flex-start;
                    padding: 0 10px;
                }

                .wizard-step-nav {
                    min-width: 120px;
                    padding: 15px 20px;
                    flex-shrink: 0;
                }

                .wizard-step-nav .step-number {
                    width: 45px;
                    height: 45px;
                    font-size: 18px;
                }

                .wizard-step-nav .step-label {
                    font-size: 12px;
                    max-width: 110px;
                    line-height: 1.3;
                }

                .wizard-separator {
                    padding: 0 10px;
                    flex-shrink: 0;
                }

                .wizard-separator svg {
                    width: 28px;
                    height: 18px;
                    transform: rotate(90deg)
                }
            }

            @media (max-width: 768px) {
                .checkout-progress-indicator {
                    padding: 15px 10px;
                    margin-bottom: 20px;
                    border-radius: 8px;
                }

                .checkout-progress {
                    min-width: 550px;
                    gap: 5px;
                }

                .wizard-step-nav {
                    min-width: 100px;
                    padding: 12px 15px;
                    flex-shrink: 0;
                }

                .wizard-step-nav .step-number {
                    width: 40px;
                    height: 40px;
                    font-size: 16px;
                    margin-bottom: 8px;
                }

                .wizard-step-nav .step-label {
                    font-size: 11px;
                    max-width: 90px;
                    line-height: 1.2;
                }

                .wizard-separator {
                    padding: 0 6px;
                    flex-shrink: 0;
                }

                .wizard-separator svg {
                    width: 25px;
                    height: 16px;
                }
            }

            @media (max-width: 576px) {
                .checkout-progress-indicator {
                    padding: 12px 8px;
                    margin-bottom: 15px;
                }

                .checkout-progress {
                    min-width: 480px;
                    gap: 3px;
                }

                .wizard-step-nav {
                    min-width: 85px;
                    padding: 10px 12px;
                }

                .wizard-step-nav .step-number {
                    width: 35px;
                    height: 35px;
                    font-size: 14px;
                    margin-bottom: 6px;
                }

                .wizard-step-nav .step-label {
                    font-size: 10px;
                    max-width: 75px;
                    line-height: 1.1;
                }

                .wizard-separator {
                    padding: 0 4px;
                }

                .wizard-separator svg {
                    width: 20px;
                    height: 14px;
                }
            }

            @media (max-width: 480px) {
                .checkout-progress-indicator {
                    padding: 10px 5px;
                    margin-bottom: 15px;
                }

                .checkout-progress {
                    min-width: 420px;
                    gap: 2px;
                }

                .wizard-step-nav {
                    min-width: 75px;
                    padding: 8px 10px;
                }

                .wizard-step-nav .step-number {
                    width: 32px;
                    height: 32px;
                    font-size: 13px;
                    margin-bottom: 5px;
                }

                .wizard-step-nav .step-label {
                    font-size: 9px;
                    max-width: 65px;
                    line-height: 1.0;
                }

                .wizard-separator {
                    padding: 0 3px;
                }

                .wizard-separator svg {
                    width: 18px;
                    height: 12px;
                }
            }
        </style>
    @endpush
@endonce

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
