@props([
    'name' => '',
    'id' => null,
    'label' => '',
    'checked' => false,
    'value' => '1',
    'helpText' => null,
    'switchColor' => 'primary', // primary, success, danger, warning, info
    'disabled' => false,
])

<div class="form-group mb-25">
    @if($label)
        <label class="il-gray fs-14 fw-500 mb-10 d-block">
            {{ $label }}
        </label>
    @endif
    
    <div class="dm-switch-wrap d-flex align-items-center">
        <div class="form-check form-switch form-switch-{{ $switchColor }} form-switch-md">
            <input type="hidden" name="{{ $name }}" value="0">
            <input 
                type="checkbox"
                class="form-check-input"
                id="{{ $id ?? $name }}"
                name="{{ $name }}"
                value="{{ $value }}"
                {{ old($name, $checked) == $value ? 'checked' : '' }}
                @if($disabled) disabled @endif
                {{ $attributes }}
            >
            <label class="form-check-label" for="{{ $id ?? $name }}"></label>
        </div>
    </div>
    
    @if($helpText)
        <small class="text-muted d-block mt-2">{{ $helpText }}</small>
    @endif
    
    @error($name)
        <div class="text-danger fs-12 mt-1">{{ $message }}</div>
    @enderror
</div>
