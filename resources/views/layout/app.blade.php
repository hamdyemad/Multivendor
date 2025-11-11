@include('partials._header')

<link rel="stylesheet" href="{{ asset('assets/css/my_custom_style.css') }}">
<body class="layout-light side-menu">
    {{-- <div class="mobile-search">
        <form action="/" class="search-form">
            <img src="{{ asset('assets/img/svg/search.svg') }}" alt="search" class="svg">
            <input class="form-control me-sm-2 box-shadow-none" type="search" placeholder="Search..." aria-label="Search">
        </form>
    </div> --}}
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
            mapClockIcon: "{{ asset('assets/img/svg/clock-ticket1.sv') }}g"
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDduF2tLXicDEPDMAtC6-NLOekX0A5vlnY"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="{{ asset('assets/js/plugins.min.js') }}"></script>

    {{-- Add stub functions for missing plugins to prevent console errors --}}
    <script>
        // Prevent errors for optional plugins that script.min.js might try to initialize
        if (typeof jQuery !== 'undefined') {
            // Add stub for sortable if not loaded
            if (!jQuery.fn.sortable) {
                jQuery.fn.sortable = function() { return this; };
            }
            // Add stub for footable if not loaded
            if (!jQuery.fn.footable) {
                jQuery.fn.footable = function() { return this; };
            }
        }
    </script>

    <script src="{{ asset('assets/js/script.min.js') }}"></script>
    <script src="{{ asset('js/app.min.js') }}"></script>
    @vite('resources/js/app.js')

    <script>
        // Wait for everything to load, then dynamically load select2
        window.addEventListener('load', function() {
            console.log('Page fully loaded');
            console.log('jQuery available:', typeof $ !== 'undefined');
            
            // Dynamically load select2
            var select2Script = document.createElement('script');
            select2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            select2Script.onload = function() {
                console.log('Select2 script loaded');
                console.log('Select2 loaded:', typeof $.fn.select2 !== 'undefined');
                console.log('Select2 elements found:', $('.select2').length);
                
                // Initialize all select2 elements
                if (typeof $.fn.select2 !== 'undefined') {
                    $('.select2').each(function() {
                        $(this).select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: $(this).data('placeholder') || 'Select an option',
                            allowClear: true
                        });
                    });
                    console.log('Select2 initialized successfully');
                } else {
                    console.error('Select2 is not loaded!');
                }
            };
            select2Script.onerror = function() {
                console.error('Failed to load Select2 script');
            };
            document.head.appendChild(select2Script);
        });

        // Custom message function using utilities message system
        function showMessage(type, message, icon = 'check-circle', duration = 3000) {
            const messageWrapper = document.querySelector('.message-wrapper');
            if (!messageWrapper) return;

            const messageId = 'msg-' + Date.now();
            const iconClass = icon;

            const messageHTML = `
                <div id="${messageId}" class="alert-message alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease;">
                    <div class="alert-content d-flex align-items-center">
                        <i class="uil uil-${iconClass} me-2" style="font-size: 20px;"></i>
                        <p class="mb-0">${message}</p>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            `;

            messageWrapper.insertAdjacentHTML('beforeend', messageHTML);

            // Auto remove after duration
            setTimeout(() => {
                const msgElement = document.getElementById(messageId);
                if (msgElement) {
                    msgElement.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => msgElement.remove(), 300);
                }
            }, duration);
        }

        // Handle session messages
        @if(session('success'))
            showMessage('success', "{{ session('success') }}", 'check-circle');
        @elseif(session('error'))
            showMessage('danger', "{{ session('error') }}", 'times-circle');
        @elseif(session('info'))
            showMessage('info', "{{ session('info') }}", 'info-circle');
        @elseif(session('warning'))
            showMessage('warning', "{{ session('warning') }}", 'exclamation-triangle');
        @endif
    </script>

    <style>
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        [dir="rtl"] .alert-message {
            right: auto !important;
            left: 20px !important;
        }

        [dir="rtl"] .alert-message {
            animation: slideInLeft 0.3s ease !important;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>

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

</body>
</html>
