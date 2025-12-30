<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'image',
    'name' => 'image',
    'label' => 'Image',
    'required' => false,
    'existingImage' => null,
    'placeholder' => 'Click to upload image',
    'recommendedSize' => 'Recommended size: 800x600',
    'accept' => 'image/*',
    'containerClass' => '',
    'imageClass' => 'preview-image',
    'aspectRatio' => 'square' // square, wide, logo
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
    'id' => 'image',
    'name' => 'image',
    'label' => 'Image',
    'required' => false,
    'existingImage' => null,
    'placeholder' => 'Click to upload image',
    'recommendedSize' => 'Recommended size: 800x600',
    'accept' => 'image/*',
    'containerClass' => '',
    'imageClass' => 'preview-image',
    'aspectRatio' => 'square' // square, wide, logo
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $uniqueId = $id . '-' . uniqid();
    $containerClasses = match($aspectRatio) {
        'wide' => 'image-preview-container banner-preview',
        'logo' => 'logo-preview-container',
        default => 'image-preview-container'
    };
?>
<style>
    .image-upload-wrapper {
        position: relative;
    }

    .image-preview-container, .logo-preview-container {
        position: relative;
        width: 100%;
        height: 200px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .banner-preview {
        height: 180px;
    }

    .logo-preview-container {
        height: 180px;
    }

    .image-preview-container:hover,
    .logo-preview-container:hover {
        border-color: #0056B7;
        background: #e7f3ff;
    }

    .preview-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: white;
    }

    .image-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #8c90a4;
    }

    .image-placeholder i {
        font-size: 48px;
        margin-bottom: 10px;
        color: #c4c4c4;
    }

    .image-placeholder p {
        margin: 0;
        font-size: 14px;
        font-weight: 500;
    }

    .image-placeholder small {
        color: #adb5bd;
        font-size: 12px;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-preview-container:hover .image-overlay,
    .logo-preview-container:hover .image-overlay {
        opacity: 1;
    }

    .btn-change-image, .btn-remove-image {
        background: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-change-image:hover {
        background: #0056B7;
        color: white;
    }

    .btn-remove-image {
        background: #ff4757;
        color: white;
    }

    .btn-remove-image:hover {
        background: #e84118;
    }
</style>
<div class="form-group <?php echo e($containerClass); ?>">
    <label class="il-gray fs-14 fw-500 mb-10">
        <?php echo e($label); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?><span class="text-danger">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </label>
    <div class="image-upload-wrapper">
        <div class="<?php echo e($containerClasses); ?>" id="<?php echo e($uniqueId); ?>-preview-container" data-target="<?php echo e($id); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($existingImage): ?>
                <img src="<?php echo e(asset('storage/' . $existingImage)); ?>"
                     alt="<?php echo e($label); ?>"
                     class="<?php echo e($imageClass); ?>"
                     id="<?php echo e($uniqueId); ?>-preview-img">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="image-placeholder"
                 id="<?php echo e($uniqueId); ?>-placeholder"
                 style="<?php echo e($existingImage ? 'display: none;' : ''); ?>">
                <i class="uil uil-image-plus"></i>
                <p><?php echo e($placeholder); ?></p>
                <small><?php echo e($recommendedSize); ?></small>
            </div>
            <div class="image-overlay">
                <button type="button" class="btn-change-image" data-target="<?php echo e($id); ?>">
                    <i class="uil uil-camera"></i> <?php echo e(trans('common.change') ?? 'Change'); ?>

                </button>
                <button type="button"
                        class="btn-remove-image"
                        data-target="<?php echo e($id); ?>"
                        style="<?php echo e($existingImage ? 'display: inline-flex;' : 'display: none;'); ?>">
                    <i class="uil uil-trash-alt"></i> <?php echo e(trans('common.remove') ?? 'Remove'); ?>

                </button>
            </div>
        </div>
        <input type="file"
               class="d-none image-file-input"
               id="<?php echo e($id); ?>"
               name="<?php echo e($name); ?>"
               accept="<?php echo e($accept); ?>"
               data-preview="<?php echo e($uniqueId); ?>">
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$name];
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

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image upload functionality - prevent duplicate initialization
    const imageInputs = document.querySelectorAll('.image-file-input:not([data-initialized])');

    imageInputs.forEach(input => {
        // Mark as initialized to prevent duplicate handlers
        input.setAttribute('data-initialized', 'true');

        const previewId = input.dataset.preview;
        const container = document.getElementById(previewId + '-preview-container');
        const placeholder = document.getElementById(previewId + '-placeholder');
        const changeBtn = container.querySelector('.btn-change-image');
        const removeBtn = container.querySelector('.btn-remove-image');

        // Click on container to select file (but not on buttons)
        container.addEventListener('click', (e) => {
            // Only open file dialog if clicking directly on container or placeholder
            if (!e.target.closest('.btn-change-image') && !e.target.closest('.btn-remove-image')) {
                input.click();
            }
        });

        if (changeBtn) {
            changeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                input.click();
            });
        }

        // Handle file selection
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Get the current preview image (it might have been created dynamically)
                    let previewImg = document.getElementById(previewId + '-preview-img');

                    if (!previewImg) {
                        // Create new image element
                        const img = document.createElement('img');
                        img.id = previewId + '-preview-img';
                        img.className = '<?php echo e($imageClass); ?>';
                        img.src = event.target.result;
                        container.insertBefore(img, placeholder);
                    } else {
                        // Update existing image
                        previewImg.src = event.target.result;
                    }

                    if (placeholder) placeholder.style.display = 'none';
                    if (removeBtn) removeBtn.style.display = 'inline-flex';
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove image
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                input.value = '';

                // Get the current preview image
                const currentPreviewImg = document.getElementById(previewId + '-preview-img');
                if (currentPreviewImg) {
                    currentPreviewImg.remove();
                }

                if (placeholder) placeholder.style.display = 'flex';
                removeBtn.style.display = 'none';
            });
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/image-upload.blade.php ENDPATH**/ ?>