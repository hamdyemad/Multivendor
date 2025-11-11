<!doctype html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ (LaravelLocalization::getCurrentLocale()=='ar' ? 'rtl' : 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="@yield('description')"/>
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.min.css') }}">

    @if(\LaravelLocalization::getCurrentLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/css/plugin.rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/variables.rtl.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.rtl.min.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    @endif
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-bnaia.png') }}">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v3.0.0/css/line.css">
    <link rel="stylesheet" href="{{ asset('css/plugins/toastr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    @vite(['resources/scss/progress-bar.scss'])
    @vite(['resources/scss/rtl-validation.scss'])
    @vite('resources/scss/app.scss')
    @yield('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        /* Fix scrollbar for minimized sidebar submenus */
        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul {
            max-height: 80vh !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        /* Custom scrollbar styling */
        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .sidebar-collapse .sidebar__menu-group .has-child:hover > ul::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>



</head>
