
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <img id="modalImage" src="" alt="">
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    /* Image Modal Styles */
    #imageModal {
        padding: 0 !important;
    }

    #imageModal .modal-dialog {
        max-width: 100vw !important;
        width: 100vw !important;
        height: 100vh !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #imageModal .modal-content {
        border-radius: 0;
        border: none;
        background: transparent;
        box-shadow: none;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100vh;
        position: relative;
        overflow: hidden;
    }

    #imageModal .modal-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1055;
        background: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        opacity: 0.9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    #imageModal .modal-close-btn:hover {
        opacity: 1;
        background: #f8f9fa;
    }

    #imageModal img {
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        max-width: 90vw !important;
        max-height: 90vh !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        display: block;
        border-radius: 10px !important;
    }

    body.modal-open {
        overflow: hidden !important;
    }

    #imageModal .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.8);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageModal = document.getElementById('imageModal');

        if (imageModal) {
            // Close modal when clicking on backdrop (outside the image)
            imageModal.addEventListener('click', function(e) {
                if (e.target === this || e.target.classList.contains('modal-dialog') || e.target.classList.contains('modal-content')) {
                    const modal = bootstrap.Modal.getInstance(imageModal);
                    if (modal) {
                        modal.hide();
                    }
                }
            });

            // Handle image wrapper click
            document.querySelectorAll('.image-wrapper').forEach(wrapper => {
                wrapper.addEventListener('click', function() {
                    const img = this.querySelector('img');
                    if (img) {
                        const modal = new bootstrap.Modal(imageModal);
                        const modalImage = document.getElementById('modalImage');

                        // Set the modal image source and alt
                        modalImage.src = img.src;
                        modalImage.alt = img.alt;

                        // Show the modal
                        modal.show();
                    }
                });
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/image-modal.blade.php ENDPATH**/ ?>