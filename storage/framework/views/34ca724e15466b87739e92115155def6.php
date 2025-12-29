<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => null,
    'options' => [], // Expects array of ['id' => ..., 'name' => ...]
    'selected' => [], // array of selected IDs
    'placeholder' => 'Select options...',
    'required' => false,
    'id' => null,
    'multiple' => true,
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
    'name',
    'label' => null,
    'options' => [], // Expects array of ['id' => ..., 'name' => ...]
    'selected' => [], // array of selected IDs
    'placeholder' => 'Select options...',
    'required' => false,
    'id' => null,
    'multiple' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Sanitize ID to avoid issues with brackets in selectors
    $safeName = str_replace(['[]', '[', ']'], ['_', '', ''], $name);
    $componentId = $id ?? $safeName . '-' . Str::random(5);
    $selectedIds = collect(old(str_replace('[]', '', $name), $selected))
        ->map(fn($v) => (string) $v)
        ->toArray();
?>

<div class="searchable-tags-wrapper w-100" id="<?php echo e($componentId); ?>-wrapper" data-name="<?php echo e($name); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
        <label class="il-gray fs-14 fw-500 mb-10 d-block">
            <?php echo e($label); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?>
                <span class="text-danger">*</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="tag-input-container d-flex flex-wrap align-items-center gap-1" data-id="<?php echo e($componentId); ?>"
        id="<?php echo e($componentId); ?>-container">
        <div class="tags-display d-flex flex-wrap gap-1" id="<?php echo e($componentId); ?>-tags-display">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array((string) $option['id'], $selectedIds)): ?>
                    <span class="tag-badge d-inline-flex align-items-center badge-primary text-white rounded px-2 py-1"
                        data-id="<?php echo e($option['id']); ?>" style="font-size: 13px;">
                        <?php echo e($option['name']); ?>

                        <span class="tag-remove ms-2 cursor-pointer" style="line-height: 1;"
                            onclick="event.stopPropagation(); window.searchableTags.removeTag('<?php echo e($componentId); ?>', '<?php echo e($option['id']); ?>')">&times;</span>
                        <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($option['id']); ?>">
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <input type="text" class="tag-input flex-grow-1 border-0 outline-0 bg-transparent p-1"
            id="<?php echo e($componentId); ?>-input" placeholder="<?php echo e(count($selectedIds) > 0 ? '' : $placeholder); ?>"
            autocomplete="new-password" style="min-width: 100px; font-size: 14px;">

        <div class="dropdown-chevron ms-auto pe-2 text-muted cursor-pointer">
            <i class="uil uil-angle-down fs-18"></i>
        </div>

        <div class="tag-dropdown shadow border rounded mt-1 position-absolute start-0 end-0 bg-white overflow-auto"
            id="<?php echo e($componentId); ?>-dropdown" style="display: none; top: 100%; z-index: 1060; max-height: 250px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $isSelected = in_array((string)$option['id'], $selectedIds); ?>
                <div class="tag-option p-2 cursor-pointer <?php echo e($isSelected ? 'selected' : ''); ?>"
                    data-id="<?php echo e($option['id']); ?>" data-name="<?php echo e(addslashes($option['name'])); ?>"
                    style="<?php echo e($isSelected ? 'display: none;' : ''); ?>"
                    onclick="event.stopPropagation(); window.searchableTags.addTag('<?php echo e($componentId); ?>', '<?php echo e($option['id']); ?>', '<?php echo e(addslashes($option['name'])); ?>', '<?php echo e($name); ?>', <?php echo e($multiple ? 'true' : 'false'); ?>)">
                    <?php echo e($option['name']); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [str_replace('[]', '', $name)];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="dynamic-error-container"></div>
</div>

<?php if (! $__env->hasRenderedOnce('acdfc458-b57a-4db0-b1de-568c6261cd42')): $__env->markAsRenderedOnce('acdfc458-b57a-4db0-b1de-568c6261cd42'); ?>
    <?php $__env->startPush('styles'); ?>
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
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
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
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/searchable-tags.blade.php ENDPATH**/ ?>