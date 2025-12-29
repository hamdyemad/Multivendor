<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'model', 'fieldName', 'languages', 'type' => 'text']));

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

foreach (array_filter((['label', 'model', 'fieldName', 'languages', 'type' => 'text']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="col-md-12">
    <div class="view-item box-items-translations">
        <label class="il-gray fs-14 fw-500 mb-10"><?php echo e($label); ?></label>
        <div class="row">
            <?php
                $current = app()->getLocale();
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages->sortBy(function($lang) use ($current) {
                    return $current == 'ar'
                        ? ($lang->code == 'ar' ? 0 : 1)
                        : ($lang->code == 'en' ? 0 : 1);
                }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $translation = $model->getTranslation($fieldName, $lang->code);
                ?>

                <div class="col-md-6 mb-3">
                    <div style="
                        padding: 12px;
                        background: #f8f9fa;
                        border-radius: 6px;
                        <?php echo e($lang->code == 'ar' ? 'border-right: 3px solid #5f63f2;' : 'border-left: 3px solid #5f63f2;'); ?>

                    ">

                        <small class="text-muted d-block mb-2"
                            style="<?php echo e($lang->code == 'ar' ? 'direction: rtl; text-align: right;' : 'direction: ltr; text-align: left;'); ?>">
                            <span class="badge
                                <?php echo e($lang->code == 'en' ? 'bg-primary' : 'bg-success'); ?>

                                text-white px-2 py-1 round-pill fw-bold"
                                style="font-size: 10px;">
                                <?php echo e(strtoupper($lang->code)); ?>

                            </span>
                        </small>

                        <div class="fs-15 color-dark mb-0 fw-500"
                            style="
                                <?php echo e($lang->code == 'ar'
                                    ? 'direction: rtl; text-align: right; font-family: Cairo, Segoe UI, Tahoma, Geneva, Verdana, sans-serif;'
                                    : 'direction: ltr; text-align: left;'); ?>

                            ">
                            <?php if($type === 'keywords'): ?>
                                <?php
                                    $keywords = [];
                                    if ($translation) {
                                        $decoded = json_decode($translation, true);
                                        if (is_array($decoded)) {
                                            $keywords = $decoded;
                                        } else {
                                            $keywords = array_map('trim', explode(',', $translation));
                                            $keywords = array_filter($keywords);
                                        }
                                    }
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($keywords) > 0): ?>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $keywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keyword): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge badge-lg badge-round bg-info text-white" style="font-size: 12px; padding: 6px 10px; <?php if($lang->code == 'ar'): ?> font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; <?php endif; ?>">
                                                <?php echo e(trim($keyword)); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'html'): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($translation): ?>
                                        <?php echo $translation; ?>

                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($translation): ?>
                                        <?php echo e($translation); ?>

                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/translation-display.blade.php ENDPATH**/ ?>