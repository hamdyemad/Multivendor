<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'id' => null,
    'label' => null,
    'icon' => null,
    'options' => [],
    'selected' => [],
    'placeholder' => 'Select...',
    'required' => false,
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
    'id' => null,
    'label' => null,
    'icon' => null,
    'options' => [],
    'selected' => [],
    'placeholder' => 'Select...',
    'required' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $componentId = $id ?? 'multi-select-' . Str::random(6);
    $selectedIds = collect($selected)->map(fn($v) => (string) $v)->toArray();
?>

<div class="multi-select-wrapper" id="<?php echo e($componentId); ?>-wrapper">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
        <label class="il-gray fs-14 fw-500 mb-10 d-block">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon): ?>
                <i class="<?php echo e($icon); ?> me-1"></i>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php echo e($label); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?>
                <span class="text-danger">*</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </label>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="multi-select-container" id="<?php echo e($componentId); ?>" data-name="<?php echo e($name); ?>">
        <div class="multi-select-display" id="<?php echo e($componentId); ?>-display">
            <div class="multi-select-tags" id="<?php echo e($componentId); ?>-tags">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) > 1): ?>
                    <span class="multi-select-count"><?php echo e(count($selectedIds)); ?> selected</span>
                <?php elseif(count($selectedIds) === 1): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array((string) $option['id'], $selectedIds)): ?>
                            <span class="multi-select-tag" data-value="<?php echo e($option['id']); ?>">
                                <?php echo e($option['name']); ?>

                                <span class="multi-select-tag-remove" data-value="<?php echo e($option['id']); ?>">&times;</span>
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <input type="text" class="multi-select-search" id="<?php echo e($componentId); ?>-search" 
                   placeholder="<?php echo e(count($selectedIds) > 0 ? '' : $placeholder); ?>" data-placeholder="<?php echo e($placeholder); ?>" autocomplete="off">
            <span class="multi-select-arrow">
                <i class="uil uil-angle-down"></i>
            </span>
        </div>
        
        <div class="multi-select-dropdown" id="<?php echo e($componentId); ?>-dropdown">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="multi-select-option <?php echo e(in_array((string) $option['id'], $selectedIds) ? 'selected' : ''); ?>" 
                     data-value="<?php echo e($option['id']); ?>" data-text="<?php echo e($option['name']); ?>">
                    <span class="multi-select-checkbox">
                        <i class="uil <?php echo e(in_array((string) $option['id'], $selectedIds) ? 'uil-check' : ''); ?>"></i>
                    </span>
                    <?php echo e($option['name']); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="multi-select-no-results" style="display: none;">No results found</div>
        </div>
        
        
        <div class="multi-select-values" id="<?php echo e($componentId); ?>-values">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array((string) $option['id'], $selectedIds)): ?>
                    <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($option['id']); ?>">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
</div>

<?php if (! $__env->hasRenderedOnce('aecafb1d-79f6-4344-be9e-68896302b3bf')): $__env->markAsRenderedOnce('aecafb1d-79f6-4344-be9e-68896302b3bf'); ?>
<?php $__env->startPush('styles'); ?>
<style>
.multi-select-container {
    position: relative;
    width: 100%;
}

.multi-select-display {
    display: flex;
    align-items: center;
    height: 38px;
    min-height: 38px;
    max-height: 38px;
    padding: 0 30px 0 8px;
    border: 1px solid #e3e6ef;
    border-radius: 4px;
    background: #fff;
    cursor: text;
    position: relative;
    overflow: hidden;
}

.multi-select-display:focus-within {
    border-color: #5F63F2;
    box-shadow: 0 0 0 2px rgba(95, 99, 242, 0.1);
}

.multi-select-tags {
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
    flex-shrink: 0;
    max-width: calc(100% - 70px);
}

.multi-select-tag {
    display: inline-flex;
    align-items: center;
    background: #5F63F2;
    color: #fff;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    gap: 4px;
    white-space: nowrap;
    flex-shrink: 0;
}

.multi-select-tag-remove {
    cursor: pointer;
    font-size: 12px;
    line-height: 1;
    opacity: 0.8;
}

.multi-select-tag-remove:hover {
    opacity: 1;
}

.multi-select-count {
    display: inline-flex;
    align-items: center;
    background: #5F63F2;
    color: #fff;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 11px;
    white-space: nowrap;
    flex-shrink: 0;
}

.multi-select-search {
    flex: 1;
    min-width: 40px;
    border: none;
    outline: none;
    padding: 4px;
    font-size: 14px;
    background: transparent;
    height: 100%;
}

.multi-select-arrow {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #9299b8;
    pointer-events: none;
}

.multi-select-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #e3e6ef;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 1050;
    margin-top: 4px;
}

.multi-select-dropdown.show {
    display: block;
}

.multi-select-option {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
    gap: 8px;
}

.multi-select-option:hover {
    background: #f8f9fb;
}

.multi-select-option.selected {
    background: #f0f1ff;
}

.multi-select-option.hidden {
    display: none;
}

.multi-select-checkbox {
    width: 16px;
    height: 16px;
    border: 1px solid #d9d9d9;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #5F63F2;
}

.multi-select-option.selected .multi-select-checkbox {
    background: #5F63F2;
    border-color: #5F63F2;
    color: #fff;
}

.multi-select-no-results {
    padding: 12px;
    text-align: center;
    color: #9299b8;
    font-size: 14px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    if (window.MultiSelectInitialized) return;
    window.MultiSelectInitialized = true;

    window.MultiSelect = {
        init: function(containerId) {
            const container = document.getElementById(containerId);
            if (!container || container.dataset.initialized) return;
            container.dataset.initialized = 'true';

            const display = container.querySelector('.multi-select-display');
            const search = container.querySelector('.multi-select-search');
            const dropdown = container.querySelector('.multi-select-dropdown');
            const tagsContainer = container.querySelector('.multi-select-tags');
            const valuesContainer = container.querySelector('.multi-select-values');
            const options = container.querySelectorAll('.multi-select-option');
            const noResults = container.querySelector('.multi-select-no-results');
            const name = container.dataset.name;

            // Toggle dropdown
            display.addEventListener('click', function(e) {
                if (e.target.classList.contains('multi-select-tag-remove')) return;
                dropdown.classList.toggle('show');
                search.focus();
            });

            // Search filter
            search.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                let hasVisible = false;
                
                options.forEach(function(opt) {
                    const text = opt.dataset.text.toLowerCase();
                    if (text.includes(term)) {
                        opt.classList.remove('hidden');
                        hasVisible = true;
                    } else {
                        opt.classList.add('hidden');
                    }
                });
                
                noResults.style.display = hasVisible ? 'none' : 'block';
            });

            // Option click
            options.forEach(function(opt) {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = this.dataset.value;
                    const text = this.dataset.text;
                    
                    if (this.classList.contains('selected')) {
                        // Deselect
                        this.classList.remove('selected');
                        this.querySelector('.multi-select-checkbox i').className = 'uil';
                        
                        const tag = tagsContainer.querySelector('[data-value="' + value + '"]');
                        if (tag) tag.remove();
                        
                        const input = valuesContainer.querySelector('input[value="' + value + '"]');
                        if (input) input.remove();
                    } else {
                        // Select
                        this.classList.add('selected');
                        this.querySelector('.multi-select-checkbox i').className = 'uil uil-check';
                        
                        // Add hidden input
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = value;
                        valuesContainer.appendChild(input);
                    }
                    
                    // Update tags display
                    MultiSelect.updateTagsDisplay(containerId);
                    
                    search.value = '';
                    
                    // Trigger change event
                    container.dispatchEvent(new CustomEvent('change', { detail: { values: MultiSelect.getValues(containerId) }}));
                });
            });

            // Tag remove click
            tagsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('multi-select-tag-remove')) {
                    e.stopPropagation();
                    const value = e.target.dataset.value;
                    
                    // Remove hidden input
                    const input = valuesContainer.querySelector('input[value="' + value + '"]');
                    if (input) input.remove();
                    
                    // Deselect option
                    const opt = container.querySelector('.multi-select-option[data-value="' + value + '"]');
                    if (opt) {
                        opt.classList.remove('selected');
                        opt.querySelector('.multi-select-checkbox i').className = 'uil';
                    }
                    
                    // Update tags display
                    MultiSelect.updateTagsDisplay(containerId);
                    
                    // Trigger change event
                    container.dispatchEvent(new CustomEvent('change', { detail: { values: MultiSelect.getValues(containerId) }}));
                }
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!container.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        },

        getValues: function(containerId) {
            const container = document.getElementById(containerId);
            const inputs = container.querySelectorAll('.multi-select-values input');
            return Array.from(inputs).map(function(input) { return input.value; });
        },

        updateTagsDisplay: function(containerId) {
            const container = document.getElementById(containerId);
            const tagsContainer = container.querySelector('.multi-select-tags');
            const search = container.querySelector('.multi-select-search');
            const selectedOptions = container.querySelectorAll('.multi-select-option.selected');
            const count = selectedOptions.length;
            
            // Clear tags
            tagsContainer.innerHTML = '';
            
            if (count === 0) {
                search.placeholder = search.dataset.placeholder || 'Select...';
                return;
            }
            
            search.placeholder = '';
            
            if (count === 1) {
                // Show single tag
                const opt = selectedOptions[0];
                const tag = document.createElement('span');
                tag.className = 'multi-select-tag';
                tag.dataset.value = opt.dataset.value;
                tag.innerHTML = opt.dataset.text + '<span class="multi-select-tag-remove" data-value="' + opt.dataset.value + '">&times;</span>';
                tagsContainer.appendChild(tag);
            } else {
                // Show count badge for 2 or more
                const countBadge = document.createElement('span');
                countBadge.className = 'multi-select-count';
                countBadge.textContent = count + ' selected';
                tagsContainer.appendChild(countBadge);
            }
        },

        setValues: function(containerId, values) {
            const container = document.getElementById(containerId);
            const valuesContainer = container.querySelector('.multi-select-values');
            const options = container.querySelectorAll('.multi-select-option');
            const name = container.dataset.name;

            // Clear existing
            valuesContainer.innerHTML = '';
            options.forEach(function(opt) {
                opt.classList.remove('selected');
                opt.querySelector('.multi-select-checkbox i').className = 'uil';
            });

            // Set new values
            values.forEach(function(value) {
                const opt = container.querySelector('.multi-select-option[data-value="' + value + '"]');
                if (opt) {
                    opt.classList.add('selected');
                    opt.querySelector('.multi-select-checkbox i').className = 'uil uil-check';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    valuesContainer.appendChild(input);
                }
            });

            // Update tags display
            this.updateTagsDisplay(containerId);
        },

        clear: function(containerId) {
            this.setValues(containerId, []);
            const container = document.getElementById(containerId);
            const search = container.querySelector('.multi-select-search');
            search.placeholder = search.dataset.placeholder || 'Select...';
        }
    };

    // Auto-init on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.multi-select-container').forEach(function(el) {
            MultiSelect.init(el.id);
        });
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/multi-select.blade.php ENDPATH**/ ?>