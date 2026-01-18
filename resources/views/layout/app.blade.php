@php
    try {
        $user = auth()->user();
        $user_type = auth()->user()->user_type?->name ?? 'Unknown';
        $user_type_id = $user->user_type_id ?? null;
        $vendor = auth()->user()->vendor ?? null;
    } catch (\Exception $e) {
        $user_type = 'Unknown';
        $vendor = null;
    }
@endphp
@include('partials._header')

<link rel="stylesheet" href="{{ asset('assets/css/my_custom_style.css') }}">

<body class="layout-light side-menu">
    <div class="mobile-author-actions"></div>
    <header class="header-top">
        @include('partials.top_nav._top_nav')
    </header>
    <main class="main-content">
        <div class="sidebar-wrapper">
            <aside class="sidebar sidebar-collapse" id="sidebar">
                @include('partials._menu')
            </aside>
        </div>
        <div class="contents">
            @yield('content')
        </div>
        <footer class="footer-wrapper">
            @include('partials._footer')
        </footer>
    </main>

    {{-- WhatsApp Floating Button --}}
    @include('partials.whatsapp-button')

    {{-- Message Wrapper for Notifications --}}
    <div class="message-wrapper"></div>

    {{-- Loading Overlay Stack --}}
    @stack('after-body')

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
            iconLoaderUrl: "{{ asset('assets/js/json/icons.json') }}",
            googleMarkerUrl: "{{ asset('assets/img/markar-icon.png') }}",
            editorIconUrl: "{{ asset('assets/img/ui/icons.svg') }}",
            mapClockIcon: "{{ asset('assets/img/svg/clock-ticket1.svg') }}"
        }
    </script>

    <script src="{{ asset('assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    {{-- Add stub functions AFTER jQuery and Select2 load but BEFORE other scripts --}}
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

    <script src="{{ asset('js/chart.js') }}"></script>
    <script src="{{ asset('assets/js/script.min.js') }}"></script>
    <script src="{{ asset('js/app.min.js') }}"></script>
    <!-- Toastr JS -->
    <script src="{{ asset('js/plugins/toastr.min.js') }}"></script>
    <script src="{{ asset('js/ckeditor.js') }}"></script>
    <script>
        // Set CKEditor base path immediately after loading
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.basePath = '{{ asset("js/") }}/';
            // Version 4.16.2 doesn't require license key
        }
    </script>
    {{-- @vite('resources/plugins/ckeditor/ckeditor.js') --}}
    @vite('resources/js/app.js')

    <script src="{{ asset('assets/js/sweetalert2@11.js') }}"></script>
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
        @if(session('success'))
            toastr.success("{{ session('success') }}", 'Success');
        @elseif(session('error'))
            toastr.error("{{ session('error') }}", 'Error');
        @elseif(session('info'))
            toastr.info("{{ session('info') }}", 'Info');
        @elseif(session('warning'))
            toastr.warning("{{ session('warning') }}", 'Warning');
        @endif

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

    {{-- Make date inputs fully clickable --}}
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

    @stack('scripts')

    {{-- Menu Badge Counter Script --}}
    <script src="{{ asset('assets/js/menu-badges.js') }}"></script>

    {{-- Protect dynamic badge values from being reset by global scripts --}}
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

    {{-- Add user type classes to body for JavaScript detection --}}
    <script>
        @if (Auth::check())
            @if (Auth::user()->user_type_id == \App\Models\UserType::ADMIN_TYPE ||
                    Auth::user()->user_type_id == \App\Models\UserType::SUPER_ADMIN_TYPE)
                $('body').addClass('admin-user');
            @elseif (Auth::user()->user_type_id == \App\Models\UserType::VENDOR_TYPE)
                $('body').addClass('vendor-user');
            @endif
        @endif
    </script>


</body>

</html>
