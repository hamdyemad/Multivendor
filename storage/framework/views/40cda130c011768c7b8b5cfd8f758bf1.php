<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => null,
    'labelAr' => null,
    'placeholder' => null,
    'placeholderAr' => null,
    'type' => 'text',
    'required' => false,
    'value' => null,
    'rows' => null,
    'languages' => [],
    'model' => null,
    'oldPrefix' => 'translations',
    'tags' => false,
    'cols' => 6,
    'inputClass' => null,
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
    'labelAr' => null,
    'placeholder' => null,
    'placeholderAr' => null,
    'type' => 'text',
    'required' => false,
    'value' => null,
    'rows' => null,
    'languages' => [],
    'model' => null,
    'oldPrefix' => 'translations',
    'tags' => false,
    'cols' => 6,
    'inputClass' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="row">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div
            class="col-md-<?php echo e($cols); ?> mb-25 <?php if(app()->getLocale() == 'ar'): ?> <?php echo e($language->code == 'ar' ? 'order-1' : 'order-2'); ?> <?php else: ?> <?php echo e($language->code == 'en' ? 'order-1' : 'order-2'); ?> <?php endif; ?>">
            <div class="form-group" data-lang="<?php echo e($language->code); ?>">
                <label for="translation_<?php echo e($language->id); ?>_<?php echo e($name); ?>"
                    class="il-gray fs-14 fw-500 mb-10 d-block"
                    <?php if($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')): ?> dir="rtl"
                    <?php else: ?>
                        dir="ltr" <?php endif; ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($language->code == 'ar'): ?>
                        <?php echo e($labelAr ?? $label); ?> (<?php echo e($language->name); ?>) <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?>
                            <span class="text-danger">*</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <?php echo e($label); ?> (<?php echo e($language->name); ?>) <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?>
                            <span class="text-danger">*</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tags): ?>
                    <div <?php if($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')): ?> dir="rtl" <?php else: ?> dir="ltr" <?php endif; ?>>
                        <?php if (isset($component)) { $__componentOriginala1c00a7045666cdbfc6419ab5f748667 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala1c00a7045666cdbfc6419ab5f748667 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.tags-input','data' => ['name' => ''.e($oldPrefix).'['.e($language->id).']['.e($name).']','value' => isset($model)
                                ? $model->getTranslation($name, $language->code) ?? ''
                                : old($oldPrefix . '.' . $language->id . '.' . $name, ''),'placeholder' => ''.e($language->code == 'ar' ? $placeholderAr ?? $placeholder : $placeholder).'','rtl' => $language->code == 'ar','dir' => ''.e($language->code == 'ar' ? 'rtl' : 'ltr').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('tags-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($oldPrefix).'['.e($language->id).']['.e($name).']','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($model)
                                ? $model->getTranslation($name, $language->code) ?? ''
                                : old($oldPrefix . '.' . $language->id . '.' . $name, '')),'placeholder' => ''.e($language->code == 'ar' ? $placeholderAr ?? $placeholder : $placeholder).'','rtl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($language->code == 'ar'),'dir' => ''.e($language->code == 'ar' ? 'rtl' : 'ltr').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala1c00a7045666cdbfc6419ab5f748667)): ?>
<?php $attributes = $__attributesOriginala1c00a7045666cdbfc6419ab5f748667; ?>
<?php unset($__attributesOriginala1c00a7045666cdbfc6419ab5f748667); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala1c00a7045666cdbfc6419ab5f748667)): ?>
<?php $component = $__componentOriginala1c00a7045666cdbfc6419ab5f748667; ?>
<?php unset($__componentOriginala1c00a7045666cdbfc6419ab5f748667); ?>
<?php endif; ?>
                    </div>
                <?php elseif($type === 'textarea'): ?>
                    <textarea
                        class="<?php echo e($inputClass); ?> form-control ip-gray radius-xs b-light px-15 <?php $__errorArgs = [$oldPrefix . '.' . $language->id . '.' . $name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="translation_<?php echo e($language->id); ?>_<?php echo e($name); ?>"
                        name="<?php echo e($oldPrefix); ?>[<?php echo e($language->id); ?>][<?php echo e($name); ?>]"
                        <?php if($rows): ?> rows="<?php echo e($rows); ?>" <?php endif; ?>
                        placeholder="<?php echo e($language->code == 'ar' ? $placeholderAr ?? $placeholder : $placeholder); ?>"
                        <?php if($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')): ?> dir="rtl"
                        <?php else: ?>
                            dir="ltr" <?php endif; ?>
                        data-lang="<?php echo e($language->code); ?>"><?php echo e(isset($model) ? $model->getTranslation($name, $language->code) ?? '' : old($oldPrefix . '.' . $language->id . '.' . $name)); ?></textarea>
                <?php else: ?>
                    <input type="<?php echo e($type); ?>"
                        class="form-control ih-medium ip-gray radius-xs b-light px-15 <?php $__errorArgs = [$oldPrefix . '.' . $language->id . '.' . $name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="translation_<?php echo e($language->id); ?>_<?php echo e($name); ?>"
                        name="<?php echo e($oldPrefix); ?>[<?php echo e($language->id); ?>][<?php echo e($name); ?>]"
                        value="<?php echo e(isset($model) ? $model->getTranslation($name, $language->code) ?? '' : old($oldPrefix . '.' . $language->id . '.' . $name)); ?>"
                        placeholder="<?php echo e($language->code == 'ar' ? $placeholderAr ?? $placeholder : $placeholder); ?>"
                        <?php if($language->code == 'ar' && (app()->getLocale() == 'en' || app()->getLocale() == 'ar')): ?> dir="rtl"
                        <?php else: ?>
                            dir="ltr" <?php endif; ?>
                        data-lang="<?php echo e($language->code); ?>">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$oldPrefix . '.' . $language->id . '.' . $name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback d-block" style="display: block !important;"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/multilingual-input.blade.php ENDPATH**/ ?>