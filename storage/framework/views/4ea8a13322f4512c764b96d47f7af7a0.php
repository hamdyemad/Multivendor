
<div id="loading-progress-bar"></div>


<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <div class="loading-text"><?php echo e($loadingText ?? trans('loading.processing')); ?></div>
        <div class="loading-subtext"><?php echo e($loadingSubtext ?? trans('loading.please_wait')); ?></div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('385e4e18-c2e0-4448-acde-ad9f39093693')): $__env->markAsRenderedOnce('385e4e18-c2e0-4448-acde-ad9f39093693'); ?>
    <?php $__env->startPush('styles'); ?>
    <style>
        /* Vendor System Loading Overlay Styles - Exact Match */

        /* Progress Bar */
        #loading-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary) 0%, #8e92f7 50%, var(--color-primary) 100%);
            background-size: 200% 100%;
            z-index: 99999;
            transition: width 0.3s ease;
            animation: shimmer 1.5s infinite;
            box-shadow: 0 0 10px rgba(95, 99, 242, 0.5);
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 99998;
            backdrop-filter: blur(5px);
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            text-align: center;
            animation: fadeInUp 0.5s ease;
            background: #fff;
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            min-width: 300px;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--color-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading-text {
            font-size: 16px;
            font-weight: 500;
            color: #272b41;
            margin-bottom: 10px;
        }

        .loading-subtext {
            font-size: 14px;
            color: #5a5f7d;
        }

        /* Success Animation */
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
        }

        .success-checkmark .check-icon {
            width: 80px;
            height: 80px;
            position: relative;
            border-radius: 50%;
            box-sizing: content-box;
            border: 4px solid #28a745;
            background: transparent;
        }

        .success-checkmark .check-icon::before,
        .success-checkmark .check-icon::after {
            display: none;
        }

        .success-checkmark .check-icon .icon-line {
            height: 5px;
            background-color: #28a745;
            display: block;
            border-radius: 2px;
            position: absolute;
            z-index: 10;
        }

        .success-checkmark .check-icon .icon-line.line-tip {
            top: 46px;
            left: 14px;
            width: 25px;
            transform: rotate(45deg);
            animation: icon-line-tip 0.75s;
        }

        .success-checkmark .check-icon .icon-line.line-long {
            top: 38px;
            right: 8px;
            width: 47px;
            transform: rotate(-45deg);
            animation: icon-line-long 0.75s;
        }

        @keyframes icon-line-tip {
            0% {
                width: 0;
                left: 1px;
                top: 19px;
            }
            54% {
                width: 0;
                left: 1px;
                top: 19px;
            }
            70% {
                width: 50px;
                left: -8px;
                top: 37px;
            }
            84% {
                width: 17px;
                left: 21px;
                top: 48px;
            }
            100% {
                width: 25px;
                left: 14px;
                top: 46px;
            }
        }

        @keyframes icon-line-long {
            0% {
                width: 0;
                right: 46px;
                top: 54px;
            }
            65% {
                width: 0;
                right: 46px;
                top: 54px;
            }
            84% {
                width: 55px;
                right: 0px;
                top: 35px;
            }
            100% {
                width: 47px;
                right: 8px;
                top: 38px;
            }
        }

        /* RTL Support */
        [dir="rtl"] #loading-progress-bar {
            left: auto;
            right: 0;
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .loading-overlay {
                background: rgba(27, 30, 43, 0.95);
            }

            .loading-content {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                color: white;
            }

            .loading-text {
                color: white;
            }

            .loading-subtext {
                color: rgba(255, 255, 255, 0.8);
            }

            .loading-spinner {
                border-color: #444;
                border-top-color: #5f63f2;
            }
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .loading-content {
                padding: 30px 40px;
                margin: 0 20px;
                min-width: auto;
                max-width: 350px;
            }

            .loading-spinner {
                width: 50px;
                height: 50px;
                margin-bottom: 15px;
            }

            .loading-text {
                font-size: 15px;
            }

            .loading-subtext {
                font-size: 13px;
            }
        }

        /* High Contrast Mode */
        @media (prefers-contrast: high) {
            .loading-overlay {
                background: rgba(0, 0, 0, 0.9);
            }

            .loading-spinner {
                border-color: #fff;
                border-top-color: #000;
            }
        }

        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            .loading-spinner,
            #loading-progress-bar {
                animation: none;
            }

            .loading-spinner {
                border: 4px solid #5f63f2;
                border-radius: 0;
                width: 20px;
                height: 4px;
            }

            .loading-content {
                animation: none;
            }
        }

        /* Print Styles */
        @media print {
            .loading-overlay,
            #loading-progress-bar {
                display: none !important;
            }
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Global Loading Overlay Functions
        window.LoadingOverlay = {
            progressBar: null,
            overlay: null,

            init() {
                this.progressBar = document.getElementById('loading-progress-bar');
                this.overlay = document.getElementById('loadingOverlay');
            },

            show(config = {}) {
                if (!this.overlay) this.init();

                // Update text if provided
                if (config.text || config.subtext) {
                    const textElement = this.overlay?.querySelector('.loading-text');
                    const subtextElement = this.overlay?.querySelector('.loading-subtext');

                    if (config.text && textElement) {
                        textElement.textContent = config.text;
                    }
                    if (config.subtext && subtextElement) {
                        subtextElement.textContent = config.subtext;
                    }
                }

                this.overlay?.classList.add('active');
                this.resetProgressBar();
            },

            hide() {
                if (!this.overlay) this.init();
                this.overlay?.classList.remove('active');
                this.resetProgressBar();
            },

            animateProgressBar(targetWidth, duration = 300) {
                if (!this.progressBar) this.init();
                return new Promise((resolve) => {
                    if (this.progressBar) {
                        this.progressBar.style.width = targetWidth + '%';
                    }
                    setTimeout(resolve, duration);
                });
            },

            resetProgressBar() {
                if (!this.progressBar) this.init();
                if (this.progressBar) {
                    this.progressBar.style.width = '0';
                }
            },

            showSuccess(message = 'Success!', subtext = 'Redirecting...') {
                if (!this.overlay) this.init();
                const content = this.overlay?.querySelector('.loading-content');
                if (content) {
                    content.innerHTML = `
                        <div class="success-checkmark">
                            <div class="check-icon">
                                <span class="icon-line line-tip"></span>
                                <span class="icon-line line-long"></span>
                            </div>
                        </div>
                        <div class="loading-text" style="color: #4caf50;">${message}</div>
                        <div class="loading-subtext">${subtext}</div>
                    `;
                }
            },

            async progressSequence(stages = [30, 60, 90, 100], durations = [300, 200, 200, 200]) {
                for (let i = 0; i < stages.length; i++) {
                    await this.animateProgressBar(stages[i], durations[i]);
                }
            }
        };

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            window.LoadingOverlay.init();
        });
    </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/components/loading-overlay.blade.php ENDPATH**/ ?>