@props([
    'name',
    'label' => null,
    'options' => [], // Expects array of ['id' => ..., 'name' => ...]
    'selected' => [], // array of selected IDs
    'placeholder' => 'Select options...',
    'required' => false,
    'id' => null,
    'multiple' => true,
])

@php
    // Sanitize ID to avoid issues with brackets in selectors
    $safeName = str_replace(['[]', '[', ']'], ['_', '', ''], $name);
    $componentId = $id ?? $safeName . '-' . Str::random(5);
    $selectedIds = collect(old(str_replace('[]', '', $name), $selected))
        ->map(fn($v) => (string) $v)
        ->toArray();
@endphp

<div class="searchable-tags-wrapper w-100" id="{{ $componentId }}-wrapper" data-name="{{ $name }}">
    @if ($label)
        <label class="il-gray fs-14 fw-500 mb-10 d-block">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="tag-input-container d-flex flex-wrap align-items-center gap-1" data-id="{{ $componentId }}"
        id="{{ $componentId }}-container">
        <div class="tags-display d-flex flex-wrap gap-1" id="{{ $componentId }}-tags-display">
            @foreach ($options as $option)
                @if (in_array((string) $option['id'], $selectedIds))
                    <span class="tag-badge d-inline-flex align-items-center badge-primary text-white rounded px-2 py-1"
                        data-id="{{ $option['id'] }}" style="font-size: 13px;">
                        {{ $option['name'] }}
                        <span class="tag-remove ms-2 cursor-pointer" style="line-height: 1;"
                            onclick="event.stopPropagation(); window.searchableTags.removeTag('{{ $componentId }}', '{{ $option['id'] }}')">&times;</span>
                        <input type="hidden" name="{{ $name }}" value="{{ $option['id'] }}">
                    </span>
                @endif
            @endforeach
        </div>

        <input type="text" class="tag-input flex-grow-1 border-0 outline-0 bg-transparent p-1"
            id="{{ $componentId }}-input" placeholder="{{ count($selectedIds) > 0 ? '' : $placeholder }}"
            autocomplete="new-password" style="min-width: 100px; font-size: 14px;">

        <div class="dropdown-chevron ms-auto pe-2 text-muted cursor-pointer">
            <i class="uil uil-angle-down fs-18"></i>
        </div>

        <div class="tag-dropdown shadow border rounded mt-1 position-absolute start-0 end-0 bg-white overflow-auto"
            id="{{ $componentId }}-dropdown" style="display: none; top: 100%; z-index: 1060; max-height: 250px;">
            @foreach ($options as $option)
                @php $isSelected = in_array((string)$option['id'], $selectedIds); @endphp
                <div class="tag-option p-2 cursor-pointer {{ $isSelected ? 'selected' : '' }}"
                    data-id="{{ $option['id'] }}" data-name="{{ addslashes($option['name']) }}"
                    style="{{ $isSelected ? 'display: none;' : '' }}"
                    onclick="event.stopPropagation(); window.searchableTags.addTag('{{ $componentId }}', '{{ $option['id'] }}', '{{ addslashes($option['name']) }}', '{{ $name }}', {{ $multiple ? 'true' : 'false' }})">
                    {{ $option['name'] }}
                </div>
            @endforeach
        </div>
    </div>
    @error(str_replace('[]', '', $name))
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <div class="dynamic-error-container"></div>
</div>

@once
    @push('styles')
        <style>
            .tag-input-container {
                position: relative;
                border: 1px solid #e3e6ef;
                border-radius: 4px;
                padding: 6px 10px;
                min-height: 45px;
                background: #fff;
                cursor: text;
                transition: border-color 0.2s, box-shadow 0.2s;
            }

            .tag-input-container:focus-within {
                border-color: #0056B7 !important;
                box-shadow: 0 0 0 0.15rem rgba(0, 86, 183, 0.1) !important;
            }

            .tag-option:hover {
                background-color: #f4f7fb;
            }

            .tag-option.selected {
                color: #0056B7;
                font-weight: 500;
            }

            .tag-input-container.is-invalid {
                border-color: #dc3545 !important;
            }

            .tag-remove:hover {
                color: #ff4d4d;
            }

            .cursor-pointer {
                cursor: pointer;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            window.searchableTags = {
                init: function() {
                    const self = this;

                    $(document).on('focus click', '.tag-input', function(e) {
                        e.stopPropagation();
                        const id = $(this).closest('.tag-input-container').data('id');
                        $('.tag-dropdown').not(`#${id}-dropdown`).hide();
                        $(`#${id}-dropdown`).show();
                        self.filterOptions(id, $(this).val());
                    });

                    $(document).on('click', '.dropdown-chevron', function(e) {
                        e.stopPropagation();
                        const id = $(this).closest('.tag-input-container').data('id');
                        const dropdown = $(`#${id}-dropdown`);
                        if (dropdown.is(':visible')) {
                            dropdown.hide();
                        } else {
                            $('.tag-dropdown').hide();
                            dropdown.show();
                            $(`#${id}-input`).focus();
                        }
                    });

                    $(document).on('input', '.tag-input', function() {
                        const id = $(this).closest('.tag-input-container').data('id');
                        self.filterOptions(id, $(this).val());
                    });

                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('.tag-input-container').length) {
                            $('.tag-dropdown').hide();
                        }
                    });

                    $(document).on('click', '.tag-input-container', function(e) {
                        if (!$(e.target).hasClass('tag-remove')) {
                            $(this).find('.tag-input').focus();
                        }
                    });
                },

                filterOptions: function(id, searchTerm) {
                    searchTerm = (searchTerm || '').toLowerCase();
                    const dropdown = $(`#${id}-dropdown`);
                    dropdown.find('.tag-option').each(function() {
                        const text = $(this).data('name').toString().toLowerCase();
                        const isSelected = $(this).hasClass('selected');
                        if (!isSelected && (searchTerm === '' || text.includes(searchTerm))) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                },

                addTag: function(id, val, name, inputName, multiple) {
                    const display = $(`#${id}-tags-display`);
                    const input = $(`#${id}-input`);
                    const dropdown = $(`#${id}-dropdown`);

                    if (!multiple) {
                        // Reset all options for single select
                        dropdown.find('.tag-option').removeClass('selected').show();
                        display.empty();
                    }

                    if (display.find(`.tag-badge[data-id="${val}"]`).length > 0) return;

                    const tagHtml = `
                        <span class="tag-badge d-inline-flex align-items-center badge-primary text-white rounded px-2 py-1" data-id="${val}" style="font-size: 13px;">
                            ${name}
                            <span class="tag-remove ms-2 cursor-pointer" onclick="event.stopPropagation(); window.searchableTags.removeTag('${id}', '${val}')">&times;</span>
                            <input type="hidden" name="${inputName}" value="${val}">
                        </span>
                    `;

                    display.append(tagHtml);
                    input.val('');

                    if (!multiple) {
                        dropdown.hide();
                        input.attr('placeholder', '');
                    } else {
                        input.focus();
                        input.attr('placeholder', '');
                    }

                    dropdown.find(`.tag-option[data-id="${val}"]`).addClass('selected').hide();
                },

                removeTag: function(id, val) {
                    const display = $(`#${id}-tags-display`);
                    const dropdown = $(`#${id}-dropdown`);
                    const input = $(`#${id}-input`);

                    display.find(`.tag-badge[data-id="${val}"]`).remove();
                    dropdown.find(`.tag-option[data-id="${val}"]`).removeClass('selected').show();

                    if (display.children().length === 0) {
                        // Restore placeholder if needed (optional)
                    }
                }
            };

            $(document).ready(function() {
                if (!window.searchableTagsInitialized) {
                    window.searchableTags.init();
                    window.searchableTagsInitialized = true;
                }
            });
        </script>
    @endpush
@endonce
