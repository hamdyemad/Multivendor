{{--
    Tags Input Component

    Usage:
    <x-tags-input
        name="keywords"
        :value="$existingTags"
        placeholder="Type keywords..."
        language="en"
        :allow-duplicates="true"
        :max-tags="10"
        theme="primary"
        size="md"
    />
--}}

@props([
    'name' => 'tags',
    'value' => '',
    'placeholder' => 'Type and press Enter...',
    'rtlPlaceholder' => 'اكتب واضغط Enter...',
    'language' => 'en',
    'allowDuplicates' => true,
    'maxTags' => null,
    'delimiter' => ',',
    'theme' => 'primary',
    'size' => 'md',
    'required' => false,
    'disabled' => false,
    'class' => '',
    'id' => null
])

@php
    $componentId = $id ?? 'tags-input-' . Str::random(8);
    $isRtl = $language === 'ar';
    $containerClasses = [
        'tags-input-wrapper',
        $class,
        $theme !== 'primary' ? 'theme-' . $theme : '',
        $size !== 'md' ? 'size-' . $size : '',
    ];
@endphp

<div class="{{ implode(' ', array_filter($containerClasses)) }}" id="{{ $componentId }}_wrapper">
    <div class="tags-input-container" data-language="{{ $language }}">
        <div class="tags-display d-none"></div>
        <input
            type="text"
            class="tags-input form-control"
            placeholder="{{ $isRtl ? $rtlPlaceholder : $placeholder }}"
            {{ $isRtl ? 'dir=rtl' : '' }}
            {{ $disabled ? 'disabled' : '' }}
        >
        <input
            type="hidden"
            name="{{ $name }}"
            id="{{ $componentId }}"
            value="{{ $value }}"
            {{ $required ? 'required' : '' }}
        >
    </div>

    @error($name)
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

@once
    @push('styles')
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
    @endpush

    @push('scripts')
        <script src="{{ asset('js/components/tags-input.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-initialize all tags input components
                $('.tags-input-wrapper').each(function() {
                    const wrapper = $(this);
                    const container = wrapper.find('.tags-input-container');
                    const hiddenInput = wrapper.find('input[type="hidden"]');

                    // Get options from data attributes or defaults
                    const options = {
                        placeholder: container.find('.tags-input').attr('placeholder'),
                        language: container.data('language') || 'en',
                        allowDuplicates: {{ $allowDuplicates ? 'true' : 'false' }},
                        maxTags: {{ $maxTags ? $maxTags : 'null' }},
                        delimiter: '{{ $delimiter }}'
                    };

                    // Initialize the tags input
                    new TagsInput(container[0], options);
                });
            });
        </script>
    @endpush
@endonce
