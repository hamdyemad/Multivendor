

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'tags',
    'value' => '',
    'placeholder' => 'Type and press Enter...',
    'rtlPlaceholder' => 'اكتب واضغط Enter...',
    'helpText' => null,
    'language' => 'en',
    'allowDuplicates' => true,
    'maxTags' => null,
    'delimiter' => ',',
    'theme' => 'primary',
    'size' => 'md',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'id' => null,
    'dir' => null,
]));

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

foreach (array_filter(([
    'name' => 'tags',
    'value' => '',
    'placeholder' => 'Type and press Enter...',
    'rtlPlaceholder' => 'اكتب واضغط Enter...',
    'helpText' => null,
    'language' => 'en',
    'allowDuplicates' => true,
    'maxTags' => null,
    'delimiter' => ',',
    'theme' => 'primary',
    'size' => 'md',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'id' => null,
    'dir' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $componentId = $id ?? 'tags-input-' . Str::random(8);
    // Use explicit dir prop if provided, otherwise fallback to language-based detection
    $isRtl = $dir ? $dir === 'rtl' : $language === 'ar';
    $containerClasses = [
        'tags-input-wrapper',
        $class,
        $theme !== 'primary' ? 'theme-' . $theme : '',
        $size !== 'md' ? 'size-' . $size : '',
    ];
?>
<div class="<?php echo e(implode(' ', array_filter($containerClasses))); ?>" id="<?php echo e($componentId); ?>_wrapper"
    <?php echo e($isRtl ? 'dir=rtl' : 'dir=ltr'); ?>>
    <div class="tags-input-container" data-language="<?php echo e($language); ?>" <?php echo e($isRtl ? 'dir=rtl' : 'dir=ltr'); ?>>
        <input type="text" class="tags-input form-control" placeholder="<?php echo e($isRtl ? $rtlPlaceholder : $placeholder); ?>"
            <?php echo e($isRtl ? 'dir=rtl' : 'dir=ltr'); ?> <?php echo e($disabled ? 'disabled' : ''); ?>>
        <input type="hidden" name="<?php echo e($name); ?>" id="<?php echo e($componentId); ?>" value="<?php echo e($value); ?>"
            <?php echo e($required ? 'required' : ''); ?>>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($helpText): ?>
        <small class="text-muted w-100 d-block mt-1"
            <?php echo e($isRtl ? 'dir=rtl style=text-align:right;' : ''); ?>><?php echo e($helpText); ?></small>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="tags-display d-none mt-2" <?php echo e($isRtl ? 'dir=rtl' : 'dir=ltr'); ?>></div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="text-danger mt-1"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if (! $__env->hasRenderedOnce('931e0c7f-c145-4adc-9b2f-7e76942e0056')): $__env->markAsRenderedOnce('931e0c7f-c145-4adc-9b2f-7e76942e0056'); ?>
    <?php $__env->startPush('styles'); ?>
        <style>
            /**
                                                     * Tags Input Component Styles
                                                     */

            /* Tags Input Container */
            .tags-input-wrapper {
                position: relative;
                width: 100%;
            }

            .tags-input-container {
                position: relative;
                width: 100%;
            }

            /* Tags Display Area */
            .tags-display {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 8px;
                min-height: 20px;
                padding: 4px 0;
            }

            .tags-display.d-none {
                display: none !important;
                min-height: 0;
                margin-bottom: 0;
                padding: 0;
            }

            /* Ensure tags display works for all locales */
            .tags-input-wrapper .tags-display {
                display: flex !important;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 8px;
                width: 100%;
            }

            .tags-input-wrapper .tags-display.d-none {
                display: none !important;
            }

            /* Individual Tag Items */
            .tag-item {
                display: inline-flex;
                align-items: center;
                background: #007bff;
                color: white;
                padding: 4px 8px;
                border-radius: 16px;
                font-size: 12px;
                gap: 6px;
                transition: all 0.2s ease;
                user-select: none;
            }

            .tag-item:hover {
                background: #0056b3;
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
            }

            /* Tag Text */
            .tag-text {
                white-space: nowrap;
                font-weight: 500;
                line-height: 1.2;
            }

            /* Tag Remove Button */
            .tag-remove {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                font-size: 16px;
                line-height: 1;
                padding: 0;
                width: 16px;
                height: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: background-color 0.2s ease;
                font-weight: bold;
            }

            .tag-remove:hover {
                background-color: rgba(255, 255, 255, 0.2);
                transform: scale(1.1);
            }

            .tag-remove:active {
                transform: scale(0.95);
            }

            /* Tags Input Field */
            .tags-input {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                outline: none !important;
                transition: all 0.3s ease;
                width: 100%;
            }

            .tags-input:focus {
                border-color: #007bff !important;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
                outline: none !important;
            }

            .tags-input::placeholder {
                color: #6c757d;
                opacity: 1;
            }

            /* Different Themes */
            .tags-input-wrapper.theme-success .tag-item {
                background: #28a745;
            }

            .tags-input-wrapper.theme-success .tag-item:hover {
                background: #218838;
            }

            .tags-input-wrapper.theme-warning .tag-item {
                background: #ffc107;
                color: #212529;
            }

            .tags-input-wrapper.theme-warning .tag-item:hover {
                background: #e0a800;
            }

            .tags-input-wrapper.theme-danger .tag-item {
                background: #dc3545;
            }

            .tags-input-wrapper.theme-danger .tag-item:hover {
                background: #c82333;
            }

            .tags-input-wrapper.theme-info .tag-item {
                background: #17a2b8;
            }

            .tags-input-wrapper.theme-info .tag-item:hover {
                background: #138496;
            }

            .tags-input-wrapper.theme-dark .tag-item {
                background: #343a40;
            }

            .tags-input-wrapper.theme-dark .tag-item:hover {
                background: #23272b;
            }

            /* Size Variations */
            .tags-input-wrapper.size-sm .tag-item {
                padding: 2px 6px;
                font-size: 11px;
                border-radius: 12px;
            }

            .tags-input-wrapper.size-sm .tag-remove {
                width: 14px;
                height: 14px;
                font-size: 14px;
            }

            .tags-input-wrapper.size-lg .tag-item {
                padding: 6px 12px;
                font-size: 14px;
                border-radius: 20px;
            }

            .tags-input-wrapper.size-lg .tag-remove {
                width: 18px;
                height: 18px;
                font-size: 18px;
            }

            /* RTL Support for Arabic */
            .tags-input-wrapper[dir="rtl"],
            .tags-input-container[data-language="ar"] {
                direction: rtl;
            }

            .tags-input-wrapper[dir="rtl"] .tags-display,
            .tags-input-container[data-language="ar"] .tags-display {
                direction: rtl;
                justify-content: flex-start;
            }

            .tags-input-wrapper[dir="rtl"] .tag-item,
            .tags-input-container[data-language="ar"] .tag-item {
                direction: rtl;
                text-align: right;
            }

            .tags-input-wrapper[dir="rtl"] .tag-text,
            .tags-input-container[data-language="ar"] .tag-text {
                direction: rtl;
                text-align: right;
            }

            .tags-input-wrapper[dir="rtl"] .tags-input,
            .tags-input-container[data-language="ar"] .tags-input {
                direction: rtl;
                text-align: right;
            }

            /* RTL Support based on app locale */

            html[dir="rtl"] .tags-display <?php echo e($isRtl ? 'dir=rtl' : 'dir=ltr'); ?> {
                direction: rtl;
                justify-content: flex-start;
            }

            html[dir="rtl"] .tag-item <?php echo e($isRtl ? 'dir=rtl' : 'dir=ltr'); ?> {
                direction: rtl;
                text-align: right;
            }

            html[dir="rtl"] .tag-text <?php echo e($isRtl ? 'dir=rtl' : 'dir=ltr'); ?> {
                direction: rtl;
                text-align: right;
            }

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() == 'ar' && !$isRtl): ?>
                html[dir="rtl"] .tags-display {
                    direction: ltr;
                }

                .text-muted {
                    direction: ltr;
                }
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            /* Animation for tag creation */
            @keyframes tagFadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.8);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .tag-item {
                animation: tagFadeIn 0.2s ease-out;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .tags-display {
                    gap: 6px;
                }

                .tag-item {
                    font-size: 11px;
                    padding: 3px 6px;
                }

                .tag-remove {
                    width: 14px;
                    height: 14px;
                    font-size: 14px;
                }
            }

            /* Focus within container */
            .tags-input-container:focus-within {
                outline: none;
            }

            .tags-input-container:focus-within .tags-input {
                border-color: #007bff !important;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            }

            /* Empty state - only show height when not hidden */
            .tags-display:empty:not(.d-none)::before {
                content: '';
                display: block;
                height: 20px;
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/components/tags-input.js')); ?>"></script>
        <script>
            function initializeTagsInputs() {
                $('.tags-input-wrapper:not(.initialized)').each(function() {
                    const wrapper = $(this);
                    const container = wrapper.find('.tags-input-container');

                    if (container.length > 0 && !container[0].tagsInput) {
                        const options = {
                            placeholder: container.find('.tags-input').attr('placeholder'),
                            language: container.data('language') || 'en',
                            allowDuplicates: <?php echo e($allowDuplicates ? 'true' : 'false'); ?>,
                            maxTags: <?php echo e($maxTags ? $maxTags : 'null'); ?>,
                            delimiter: '<?php echo e($delimiter); ?>'
                        };

                        const instance = new TagsInput(container[0], options);
                        container.data('tags-input', instance);
                        wrapper.addClass('initialized');
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeTagsInputs);
            } else {
                initializeTagsInputs();
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/tags-input.blade.php ENDPATH**/ ?>