<?php
    try {
        $user = auth()->user();
        $user_type = auth()->user()->user_type?->name ?? 'Unknown';
        $user_type_id = $user->user_type_id ?? null;
        $vendor = auth()->user()->vendor ?? null;
    } catch (\Exception $e) {
        $user_type = 'Unknown';
        $vendor = null;
    }
?>
<?php echo $__env->make('partials._header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<link rel="stylesheet" href="<?php echo e(asset('assets/css/my_custom_style.css')); ?>">

<body class="layout-light side-menu">
    <div class="mobile-author-actions"></div>
    <header class="header-top">
        <?php echo $__env->make('partials.top_nav._top_nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </header>
    <main class="main-content">
        <div class="sidebar-wrapper">
            <aside class="sidebar sidebar-collapse" id="sidebar">
                <?php echo $__env->make('partials._menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </aside>
        </div>
        <div class="contents">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        <footer class="footer-wrapper">
            <?php echo $__env->make('partials._footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </footer>
    </main>

    
    <?php echo $__env->make('partials.whatsapp-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="message-wrapper"></div>

    
    <?php echo $__env->yieldPushContent('after-body'); ?>

    <div id="overlayer">
        <span class="loader-overlay">
            <div class="dm-spin-dots spin-lg">
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
            </div>
        </span>
    </div>
    <div class="overlay-dark-sidebar"></div>
    <div class="customizer-overlay"></div>

    <script>
        var env = {
            iconLoaderUrl: "<?php echo e(asset('assets/js/json/icons.json')); ?>",
            googleMarkerUrl: "<?php echo e(asset('assets/img/markar-icon.png')); ?>",
            editorIconUrl: "<?php echo e(asset('assets/img/ui/icons.svg')); ?>",
            mapClockIcon: "<?php echo e(asset('assets/img/svg/clock-ticket1.svg')); ?>"
        }
    </script>

    <script src="<?php echo e(asset('assets/js/plugins.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/select2.min.js')); ?>"></script>

    
    <script>
        // Prevent errors for optional plugins that script.min.js might try to initialize
        $(document).ready(function() {
            // Better stubs that return chainable objects
            if (!$.fn.sortable) {
                $.fn.sortable = function() {
                    console.warn('Sortable not loaded, using stub');
                    return this; // Return this for chaining
                };
            }
            if (!$.fn.footable) {
                $.fn.footable = function() {
                    console.warn('Footable not loaded, using stub');
                    return this;
                };
            }
            // Add disableSelection stub for sortable chaining
            if (!$.fn.disableSelection) {
                $.fn.disableSelection = function() {
                    console.warn('disableSelection not loaded, using stub');
                    return this;
                };
            }
        });
    </script>

    <script src="<?php echo e(asset('js/chart.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/script.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/app.min.js')); ?>"></script>
    <!-- Toastr JS -->
    <script src="<?php echo e(asset('js/plugins/toastr.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/ckeditor.js')); ?>"></script>
    <script>
        // Set CKEditor base path immediately after loading
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.basePath = '<?php echo e(asset("js/")); ?>/';
            // Version 4.16.2 doesn't require license key
        }
    </script>
    
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>

    <script src="<?php echo e(asset('assets/js/sweetalert2@11.js')); ?>"></script>
    <script>
        // Configure Toastr options to match login page
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Add custom CSS for Toastr to match main app styling
        const toastrStyle = document.createElement('style');
        toastrStyle.textContent = `
            #toast-container > .toast {
                position: fixed;
                top: 20px;
                right: 20px;
                min-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border-radius: 8px;
                font-weight: 600;
            }
            #toast-container > .toast-success {
                background-color: #28a745;
                color: white;
            }
            #toast-container > .toast-error {
                background-color: #dc3545;
                color: white;
            }
            #toast-container > .toast-info {
                background-color: #17a2b8;
                color: white;
            }
            #toast-container > .toast-warning {
                background-color: #ffc107;
                color: #212529;
            }
        `;
        document.head.appendChild(toastrStyle);

        // Handle session messages
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            toastr.success("<?php echo e(session('success')); ?>", 'Success');
        <?php elseif(session('error')): ?>
            toastr.error("<?php echo e(session('error')); ?>", 'Error');
        <?php elseif(session('info')): ?>
            toastr.info("<?php echo e(session('info')); ?>", 'Info');
        <?php elseif(session('warning')): ?>
            toastr.warning("<?php echo e(session('warning')); ?>", 'Warning');
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
        });
    </script>

    <!-- CKEditor Initialization -->
    <script>
        // Initialize CKEditor immediately when DOM is ready (no delay)
        (function() {
            function initCKEditor() {
                if (typeof CKEDITOR === 'undefined') {
                    console.warn('CKEditor not loaded');
                    return;
                }
                
                // Initialize CKEditor for ALL textareas
                const textareas = document.querySelectorAll('textarea:not(.nockeditor)');
                textareas.forEach(function(textarea) {
                    // Skip if no ID
                    if (!textarea.id) {
                        return;
                    }
                    
                    // Destroy existing instance if any
                    if (CKEDITOR.instances[textarea.id]) {
                        CKEDITOR.instances[textarea.id].destroy(true);
                    }
                    
                    const isRTL = textarea.getAttribute('dir') === 'rtl';
                    try {
                        CKEDITOR.replace(textarea.id, {
                            language: 'en',
                            contentsLangDirection: isRTL ? 'rtl' : 'ltr',
                            height: 200,
                            toolbar: [
                                { name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', 'Print' ] },
                                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                                { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
                                '/',
                                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                                { name: 'links', items: [ 'Link', 'Unlink' ] },
                                { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
                                '/',
                                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                                { name: 'tools', items: [ 'Maximize' ] }
                            ],
                            removePlugins: 'elementspath',
                            resize_enabled: false,
                            enterMode: CKEDITOR.ENTER_BR,
                            shiftEnterMode: CKEDITOR.ENTER_P,
                            on: {
                                instanceReady: function(evt) {
                                    if (isRTL) {
                                        evt.editor.document.getBody().setStyle('direction', 'rtl');
                                        evt.editor.document.getBody().setStyle('text-align', 'right');
                                    }
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing CKEditor for', textarea.id, ':', error);
                    }
                });
            }
            
            // Run immediately when DOM is ready (no setTimeout delay)
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCKEditor);
            } else {
                // DOM already loaded, run immediately
                initCKEditor();
            }
        })();
    </script>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to make date inputs clickable
            function makeDateInputsClickable() {
                const dateInputs = document.querySelectorAll('input[type="date"]');

                dateInputs.forEach(function(input) {
                    // Remove any existing click listeners to avoid duplicates
                    input.removeEventListener('click', openDatePicker);

                    // Add click event to open date picker
                    input.addEventListener('click', openDatePicker);

                    // Also handle focus event
                    input.addEventListener('focus', openDatePicker);
                });
            }

            // Function to open the date picker
            function openDatePicker(event) {
                const input = event.target;

                // Try modern showPicker() API first (supported in newer browsers)
                if (typeof input.showPicker === 'function') {
                    try {
                        input.showPicker();
                    } catch (error) {
                        // Fallback for browsers that don't support showPicker
                        console.log('showPicker not supported, using fallback');
                    }
                }
            }

            // Initialize on page load
            makeDateInputsClickable();

            // Re-initialize when new content is dynamically loaded
            // Watch for DOM changes (useful for AJAX-loaded content)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        makeDateInputsClickable();
                    }
                });
            });

            // Start observing the document body for changes
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    
    <script src="<?php echo e(asset('assets/js/menu-badges.js')); ?>"></script>

    
    <script>
        (function() {
            // Store protected badge values globally
            window.protectedBadgeValues = window.protectedBadgeValues || {};
            
            // Function to store a protected value
            window.storeProtectedValue = function(id, value) {
                if (id && value && value !== '0' && value !== '0.00' && value !== '0.000') {
                    window.protectedBadgeValues[id] = value;
                }
            };
            
            // Function to get stored protected value
            window.getProtectedValue = function(id) {
                return window.protectedBadgeValues[id] || null;
            };
            
            $(document).ready(function() {
                // Store initial values of protected elements
                function storeAllProtectedValues() {
                    $('.protected-value, [data-protected="true"]').each(function() {
                        var id = $(this).attr('id');
                        var value = $(this).text().trim();
                        if (id && value && value !== '0' && value !== '0.00' && value !== '0.000') {
                            window.protectedBadgeValues[id] = value;
                        }
                    });
                }
                
                // Restore protected values if they were reset to 0
                function restoreProtectedValues() {
                    $.each(window.protectedBadgeValues, function(id, value) {
                        var element = $('#' + id);
                        if (element.length) {
                            var currentValue = element.text().trim();
                            // Only restore if current value is 0 and we have a stored non-zero value
                            if ((currentValue === '0' || currentValue === '0.00' || currentValue === '0.000') && 
                                value && value !== '0' && value !== '0.00' && value !== '0.000') {
                                element.text(value);
                            }
                        }
                    });
                }
                
                // Initial store
                storeAllProtectedValues();
                
                // Watch for AJAX updates that might set new values
                $(document).ajaxComplete(function(event, xhr, settings) {
                    // After AJAX completes, store any new non-zero values
                    setTimeout(storeAllProtectedValues, 100);
                });
                
                // Periodically check and restore (fallback protection against global resets)
                setInterval(function() {
                    restoreProtectedValues();
                }, 1500);
            });
        })();
    </script>

    
    <script>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::check()): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->user_type_id == \App\Models\UserType::ADMIN_TYPE ||
                    Auth::user()->user_type_id == \App\Models\UserType::SUPER_ADMIN_TYPE): ?>
                $('body').addClass('admin-user');
            <?php elseif(Auth::user()->user_type_id == \App\Models\UserType::VENDOR_TYPE): ?>
                $('body').addClass('vendor-user');
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </script>


</body>

</html>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/layout/app.blade.php ENDPATH**/ ?>