@props([
    'name',
    'label' => null,
    'type' => 'text',
    'required' => false,
    'value' => null,
    'placeholder' => null,
    'class' => 'form-control',
    'id' => null,
    'options' => [], // For select fields
    'multiple' => false,
    'accept' => null, // For file inputs
    'rows' => 3, // For textarea
    'dir' => null,
    'errorContainer' => true
])

@php
    $fieldId = $id ?? str_replace(['[', ']', '.'], ['_', '_', '_'], $name);
    $errorId = 'error-' . str_replace(['.', '[', ']'], ['-', '-', '-'], $name);
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $fieldId }}" class="form-label {{ $dir === 'rtl' ? 'text-end' : '' }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($type === 'select')
        <select 
            name="{{ $name }}" 
            id="{{ $fieldId }}" 
            class="{{ $class }}"
            @if($multiple) multiple @endif
            {{ $attributes }}
        >
            @if(!$multiple && !$required)
                <option value="">{{ $placeholder ?? 'Select an option' }}</option>
            @endif
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" 
                    @if($value == $optionValue || (is_array($value) && in_array($optionValue, $value))) selected @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea 
            name="{{ $name }}" 
            id="{{ $fieldId }}" 
            class="{{ $class }}"
            rows="{{ $rows }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($dir) dir="{{ $dir }}" @endif
            {{ $attributes }}
        >{{ $value }}</textarea>
    @elseif($type === 'checkbox')
        <div class="form-check">
            <input 
                type="checkbox" 
                name="{{ $name }}" 
                id="{{ $fieldId }}" 
                class="form-check-input"
                value="1"
                @if($value) checked @endif
                {{ $attributes }}
            >
            @if($label)
                <label class="form-check-label" for="{{ $fieldId }}">
                    {{ $label }}
                </label>
            @endif
        </div>
    @elseif($type === 'radio')
        @foreach($options as $optionValue => $optionLabel)
            <div class="form-check">
                <input 
                    type="radio" 
                    name="{{ $name }}" 
                    id="{{ $fieldId }}_{{ $optionValue }}" 
                    class="form-check-input"
                    value="{{ $optionValue }}"
                    @if($value == $optionValue) checked @endif
                    {{ $attributes }}
                >
                <label class="form-check-label" for="{{ $fieldId }}_{{ $optionValue }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
    @else
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $fieldId }}" 
            class="{{ $class }}"
            @if($value !== null) value="{{ $value }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($accept) accept="{{ $accept }}" @endif
            @if($dir) dir="{{ $dir }}" @endif
            {{ $attributes }}
        >
    @endif

    @if($errorContainer)
        <div class="error-message text-danger" id="{{ $errorId }}" style="display: none;"></div>
    @endif
</div>
