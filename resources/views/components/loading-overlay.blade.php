{{-- Loading Progress Bar --}}
<div id="loading-progress-bar"></div>

{{-- Loading Overlay --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <div class="loading-text">{{ $loadingText ?? trans('loading.processing') }}</div>
        <div class="loading-subtext">{{ $loadingSubtext ?? trans('loading.please_wait') }}</div>
    </div>
</div>

@once
    @push('scripts')
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
    @endpush
@endonce
