@props([
    'name',
    'id' => null,
    'label' => null,
    'icon' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select...',
    'required' => false,
])

@php
    $componentId = $id ?? 'custom-select-' . Str::random(6);
    $selectedValue = (string) $selected;
@endphp

<div class="custom-select-wrapper form-group" id="{{ $componentId }}-wrapper">
    @if ($label)
        <label class="il-gray fs-14 fw-500 mb-10 d-block">
            @if ($icon)
                <i class="{{ $icon }} me-1"></i>
            @endif
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="custom-select-container" id="{{ $componentId }}" data-name="{{ $name }}">
        <div class="custom-select-display" id="{{ $componentId }}-display">
            <div class="custom-select-value" id="{{ $componentId }}-value">
                @if ($selectedValue)
                    @foreach ($options as $option)
                        @if ((string) $option['id'] === $selectedValue)
                            {{ $option['name'] }}
                        @endif
                    @endforeach
                @else
                    <span class="custom-select-placeholder">{{ $placeholder }}</span>
                @endif
            </div>
            <input type="text" class="custom-select-search" id="{{ $componentId }}-search" 
                   placeholder="{{ __('common.search') ?? 'Search...' }}" autocomplete="off" style="display: none;">
            <span class="custom-select-arrow">
                <i class="uil uil-angle-down"></i>
            </span>
        </div>
        
        <div class="custom-select-dropdown" id="{{ $componentId }}-dropdown">
            <div class="custom-select-search-wrapper">
                <input type="text" class="custom-select-search-input" id="{{ $componentId }}-search-input" 
                       placeholder="{{ __('common.search') ?? 'Search...' }}" autocomplete="off">
            </div>
            <div class="custom-select-options" id="{{ $componentId }}-options">
                <div class="custom-select-option {{ !$selectedValue ? 'selected' : '' }}" 
                     data-value="" data-text="{{ $placeholder }}">
                    {{ $placeholder }}
                </div>
                @foreach ($options as $option)
                    <div class="custom-select-option {{ (string) $option['id'] === $selectedValue ? 'selected' : '' }}" 
                         data-value="{{ $option['id'] }}" data-text="{{ $option['name'] }}">
                        {{ $option['name'] }}
                    </div>
                @endforeach
            </div>
            <div class="custom-select-no-results" style="display: none;">{{ __('common.no_results') ?? 'No results found' }}</div>
        </div>
        
        {{-- Hidden input for form submission --}}
        <input type="hidden" name="{{ $name }}" id="{{ $componentId }}-input" value="{{ $selectedValue }}">
    </div>
    
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
@push('styles')
<style>
.custom-select-container {
    position: relative;
    width: 100%;
}

.custom-select-display {
    display: flex;
    align-items: center;
    height: 48px;
    padding: 0 35px 0 15px;
    border: 1px solid #e3e6ef;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
    position: relative;
}

.custom-select-display:hover {
    border-color: #c6d0dc;
}

.custom-select-display:focus-within,
.custom-select-container.open .custom-select-display {
    border-color: #5F63F2;
    box-shadow: 0 0 0 2px rgba(95, 99, 242, 0.1);
}

.custom-select-value {
    flex: 1;
    font-size: 14px;
    color: #272b41;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.custom-select-placeholder {
    color: #9299b8;
}

.custom-select-arrow {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9299b8;
    transition: transform 0.2s;
}

.custom-select-container.open .custom-select-arrow {
    transform: translateY(-50%) rotate(180deg);
}

.custom-select-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #e3e6ef;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 1050;
    margin-top: 4px;
}

.custom-select-container.open .custom-select-dropdown {
    display: block;
}

.custom-select-search-wrapper {
    padding: 8px;
    border-bottom: 1px solid #e3e6ef;
}

.custom-select-search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e3e6ef;
    border-radius: 4px;
    font-size: 14px;
    outline: none;
}

.custom-select-search-input:focus {
    border-color: #5F63F2;
}

.custom-select-options {
    max-height: 200px;
    overflow-y: auto;
}

.custom-select-option {
    padding: 10px 15px;
    cursor: pointer;
    font-size: 14px;
    color: #272b41;
    transition: background 0.15s;
}

.custom-select-option:hover {
    background: #f8f9fb;
}

.custom-select-option.selected {
    background: #f0f1ff;
    color: #5F63F2;
    font-weight: 500;
}

.custom-select-option.hidden {
    display: none;
}

.custom-select-no-results {
    padding: 12px 15px;
    text-align: center;
    color: #9299b8;
    font-size: 14px;
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    if (window.CustomSelectInitialized) return;
    window.CustomSelectInitialized = true;

    window.CustomSelect = {
        init: function(containerId) {
            const container = document.getElementById(containerId);
            if (!container || container.dataset.initialized) return;
            container.dataset.initialized = 'true';

            const display = container.querySelector('.custom-select-display');
            const valueDisplay = container.querySelector('.custom-select-value');
            const dropdown = container.querySelector('.custom-select-dropdown');
            const searchInput = container.querySelector('.custom-select-search-input');
            const optionsContainer = container.querySelector('.custom-select-options');
            const options = container.querySelectorAll('.custom-select-option');
            const noResults = container.querySelector('.custom-select-no-results');
            const hiddenInput = container.querySelector('input[type="hidden"]');
            const placeholder = container.querySelector('.custom-select-placeholder')?.textContent || 'Select...';

            // Toggle dropdown
            display.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = container.classList.contains('open');
                
                // Close all other dropdowns
                document.querySelectorAll('.custom-select-container.open').forEach(function(el) {
                    if (el !== container) {
                        el.classList.remove('open');
                    }
                });
                
                container.classList.toggle('open');
                
                if (!isOpen) {
                    searchInput.value = '';
                    options.forEach(function(opt) {
                        opt.classList.remove('hidden');
                    });
                    noResults.style.display = 'none';
                    setTimeout(function() {
                        searchInput.focus();
                    }, 50);
                }
            });

            // Search filter
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                let hasVisible = false;
                
                options.forEach(function(opt) {
                    const text = opt.dataset.text.toLowerCase();
                    if (text.includes(term) || opt.dataset.value === '') {
                        opt.classList.remove('hidden');
                        hasVisible = true;
                    } else {
                        opt.classList.add('hidden');
                    }
                });
                
                noResults.style.display = hasVisible ? 'none' : 'block';
            });

            // Prevent search input click from closing dropdown
            searchInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Option click
            options.forEach(function(opt) {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = this.dataset.value;
                    const text = this.dataset.text;
                    
                    // Update selection
                    options.forEach(function(o) {
                        o.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    
                    // Update display
                    if (value === '') {
                        valueDisplay.innerHTML = '<span class="custom-select-placeholder">' + placeholder + '</span>';
                    } else {
                        valueDisplay.textContent = text;
                    }
                    
                    // Update hidden input
                    hiddenInput.value = value;
                    
                    // Close dropdown
                    container.classList.remove('open');
                    
                    // Trigger change event
                    const event = new CustomEvent('change', { 
                        detail: { value: value, text: text },
                        bubbles: true
                    });
                    container.dispatchEvent(event);
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!container.contains(e.target)) {
                    container.classList.remove('open');
                }
            });

            // Close on escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    container.classList.remove('open');
                }
            });
        },

        getValue: function(containerId) {
            const container = document.getElementById(containerId);
            const hiddenInput = container.querySelector('input[type="hidden"]');
            return hiddenInput.value;
        },

        setValue: function(containerId, value) {
            const container = document.getElementById(containerId);
            const valueDisplay = container.querySelector('.custom-select-value');
            const options = container.querySelectorAll('.custom-select-option');
            const hiddenInput = container.querySelector('input[type="hidden"]');
            const placeholder = container.querySelector('.custom-select-placeholder')?.textContent || 'Select...';

            hiddenInput.value = value;
            
            options.forEach(function(opt) {
                opt.classList.remove('selected');
                if (opt.dataset.value === String(value)) {
                    opt.classList.add('selected');
                    if (value === '' || value === null) {
                        valueDisplay.innerHTML = '<span class="custom-select-placeholder">' + placeholder + '</span>';
                    } else {
                        valueDisplay.textContent = opt.dataset.text;
                    }
                }
            });
        },

        setOptions: function(containerId, options, placeholder) {
            const container = document.getElementById(containerId);
            const optionsContainer = container.querySelector('.custom-select-options');
            const valueDisplay = container.querySelector('.custom-select-value');
            const hiddenInput = container.querySelector('input[type="hidden"]');
            placeholder = placeholder || 'Select...';

            // Clear current options
            optionsContainer.innerHTML = '';
            
            // Add placeholder option
            const placeholderOpt = document.createElement('div');
            placeholderOpt.className = 'custom-select-option selected';
            placeholderOpt.dataset.value = '';
            placeholderOpt.dataset.text = placeholder;
            placeholderOpt.textContent = placeholder;
            optionsContainer.appendChild(placeholderOpt);
            
            // Add new options
            options.forEach(function(opt) {
                const optEl = document.createElement('div');
                optEl.className = 'custom-select-option';
                optEl.dataset.value = opt.id;
                optEl.dataset.text = opt.name;
                optEl.textContent = opt.name;
                optionsContainer.appendChild(optEl);
            });
            
            // Reset value
            hiddenInput.value = '';
            valueDisplay.innerHTML = '<span class="custom-select-placeholder">' + placeholder + '</span>';
            
            // Re-bind click events
            optionsContainer.querySelectorAll('.custom-select-option').forEach(function(opt) {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = this.dataset.value;
                    const text = this.dataset.text;
                    
                    optionsContainer.querySelectorAll('.custom-select-option').forEach(function(o) {
                        o.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    
                    if (value === '') {
                        valueDisplay.innerHTML = '<span class="custom-select-placeholder">' + placeholder + '</span>';
                    } else {
                        valueDisplay.textContent = text;
                    }
                    
                    hiddenInput.value = value;
                    container.classList.remove('open');
                    
                    const event = new CustomEvent('change', { 
                        detail: { value: value, text: text },
                        bubbles: true
                    });
                    container.dispatchEvent(event);
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
        },

        clear: function(containerId) {
            this.setValue(containerId, '');
        }
    };

    // Auto-init on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.custom-select-container').forEach(function(el) {
            CustomSelect.init(el.id);
        });
    });
})();
</script>
@endpush
@endonce
