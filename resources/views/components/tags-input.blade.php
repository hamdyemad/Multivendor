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
        <div class="tags-display"></div>
        <input 
            type="text" 
            class="tags-input" 
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
        <link rel="stylesheet" href="{{ asset('css/components/tags-input.css') }}">
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
