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
        .btn-primary:hover, .btn-primary:focus {
            color: var(--color-white);
            background-color: var(--bg-primary-hover) !important;
            border-color: var(--bg-primary-hover);
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
        @if(session('success'))
            toastr.success("{{ session('success') }}", 'Success')
        @elseif(session('error'))
            toastr.error("{{ session('error') }}", 'Error')
        @elseif(session('info'))
            toastr.info("{{ session('info') }}", 'Info')
        @endif
    </script>
</body>
</html>
