<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} - Bnaia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v3.0.0/css/line.css">
    <link rel="stylesheet" href="{{ asset('css/plugins/toastr.min.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-bnaia.png') }}">

    <style>
        :root {
            --color-primary: #0056B7;
            --bg-primary-hover: #003f87;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            color: var(--color-white);
            background-color: var(--bg-primary-hover) !important;
            border-color: var(--bg-primary-hover);
        }

        /* Custom Toastr styling to match main app messages */
        .toast-top-right {
            top: 20px !important;
            right: 20px !important;
        }

        .toast {
            min-width: 300px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
        }

        .toast-success {
            background-color: #28a745 !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
        }

        .toast-info {
            background-color: #17a2b8 !important;
        }

        .toast-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .toast-message {
            font-size: 14px !important;
            line-height: 1.4 !important;
        }

        .toast-title {
            font-weight: 600 !important;
            margin-bottom: 4px !important;
        }
    </style>
</head>

<body>
    <main class="main-content">
        <div class="admin" style="background-image:url({{ asset('assets/img/admin-bg-light.png') }});">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </main>
    <div id="overlayer">
        <div class="loader-overlay">
            <div class="dm-spin-dots spin-lg">
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
            </div>
        </div>
    </div>
    {{-- <div class="enable-dark-mode dark-trigger">
        <ul>
            <li>
                <a href="#">
                    <i class="uil uil-moon"></i>
                </a>
            </li>
        </ul>
    </div> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/plugins/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.min.js') }}"></script>
    <script>
        // Configure Toastr to match main app message positioning
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

        @if (session('success'))
            toastr.success("{{ session('success') }}", 'Success');
        @elseif (session('error'))
            toastr.error("{{ session('error') }}", 'Error');
        @elseif (session('info'))
            toastr.info("{{ session('info') }}", 'Info');
        @elseif (session('warning'))
            toastr.warning("{{ session('warning') }}", 'Warning');
        @endif

        // Global Password Visibility Toggle
        document.addEventListener('DOMContentLoaded', function() {
            initPasswordToggle();
        });

        function initPasswordToggle() {
            const passwordInputs = document.querySelectorAll('input[type="password"]:not(.pw-toggle-init)');
            passwordInputs.forEach(input => {
                if (input.type === 'hidden') return;
                input.classList.add('pw-toggle-init');

                // Check if already has a toggle icon in its vicinity
                const parent = input.parentElement;
                if (parent.querySelector('.toggle-password') || parent.querySelector('.password-toggle-icon')) {
                    return;
                }

                // Create a wrapper to ensure the icon is centered RELATIVE TO THE INPUT ONLY
                const wrapper = document.createElement('div');
                wrapper.className = 'password-toggle-container position-relative w-100';

                // Transfer input's margin-bottom to wrapper to maintain layout spacing
                const inputStyle = window.getComputedStyle(input);
                if (inputStyle.marginBottom !== '0px') {
                    wrapper.style.marginBottom = inputStyle.marginBottom;
                    input.style.marginBottom = '0';
                }

                // Insert wrapper before input, then move input inside it
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);

                const icon = document.createElement('span');
                icon.className = 'uil uil-eye-slash password-toggle-icon';
                icon.style.cssText =
                    'position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #aaa; font-size: 18px; z-index: 10;';

                icon.addEventListener('click', function(e) {
                    e.preventDefault();
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('uil-eye');
                    this.classList.toggle('uil-eye-slash');
                });
                wrapper.appendChild(icon);
            });
        }
    </script>
</body>

</html>
