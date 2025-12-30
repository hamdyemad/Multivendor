<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['steps', 'currentStep' => 1]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['steps', 'currentStep' => 1]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isRtl = app()->getLocale() === 'ar' || (isset($currentLanguage) && $currentLanguage === 'ar');
?>

<?php if (! $__env->hasRenderedOnce('d502b554-dc25-4511-9800-dbf785d77b71')): $__env->markAsRenderedOnce('d502b554-dc25-4511-9800-dbf785d77b71'); ?>
    <?php $__env->startPush('styles'); ?>
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
            .wizard-step-nav:hover:not(.completed):not(.locked) {
                background: #e8e9ff;
                transform: translateY(-3px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
                border-color: var(--color-primary);
            }

            .wizard-step-nav:hover:not(.completed):not(.locked) .step-number {
                background: var(--color-primary);
                color: white;
            }

            .wizard-step-nav:hover:not(.completed):not(.locked) .step-label {
                color: var(--color-primary);
            }

            /* Locked Step */
            .wizard-step-nav.locked {
                opacity: 0.5;
                cursor: not-allowed !important;
                pointer-events: none;
                filter: grayscale(1);
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

            .wizard-step-nav.completed+.wizard-separator svg path {
                stroke: #28a745;
                opacity: 1;
            }

            /* Pulse Animation */
            @keyframes pulse {

                0%,
                100% {
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
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<div class="checkout-progress-indicator content-center" <?php if($isRtl): ?> dir="rtl" <?php endif; ?>>
    <div class="checkout-progress">
        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index > 0): ?>
                <div class="wizard-separator">
                    <svg width="30" height="20" viewBox="0 0 30 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 10H25M25 10L18 3M25 10L18 17" stroke="#ddd" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php
                $isCompleted = $index + 1 < $currentStep;
                $isCurrent = $index + 1 == $currentStep;
                $isNext = $index + 1 == $currentStep + 1;
                $isLocked = $index + 1 > $currentStep + 1;
            ?>
            <div class="wizard-step-nav <?php echo e($isCurrent ? 'current' : ''); ?> <?php echo e($isCompleted ? 'completed' : ''); ?> <?php echo e($isLocked ? 'locked' : ''); ?>"
                data-step="<?php echo e($index + 1); ?>" style="cursor: <?php echo e($isLocked ? 'not-allowed' : 'pointer'); ?>;">
                <span class="step-number">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCompleted): ?>
                        <i class="uil uil-check"></i>
                    <?php else: ?>
                        <?php echo e($index + 1); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <span class="step-label"><?php echo e($step); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/wizard.blade.php ENDPATH**/ ?>