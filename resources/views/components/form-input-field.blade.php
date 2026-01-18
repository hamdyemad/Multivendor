@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'helpText' => null,
    'icon' => null,
    'disabled' => false,
    'readonly' => false,
    'class' => '',
])

<div class="form-group mb-3">
    @if($label)
        <label for="{{ $id ?? $name }}" class="form-label">
            @if($icon)
                <i class="{{ $icon }} me-1"></i>
            @endif
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}" 
        class="form-control ih-medium ip-gray radius-xs b-light px-15 {{ $class }}" 
        id="{{ $id ?? $name }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
        @if($step !== null) step="{{ $step }}" @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        {{ $attributes }}
    >
    
    @if($helpText)
        <small class="text-muted d-block mt-1">{{ $helpText }}</small>
    @endif
    
    @error($name)
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
