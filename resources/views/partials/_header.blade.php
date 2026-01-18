@php
    $countryCode = session('country_code') ?? 'eg';
   $country = Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->first();
@endphp
<!doctype html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ (LaravelLocalization::getCurrentLocale()=='ar' ? 'rtl' : 'ltr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @if ($country)
        <meta name="currency_country_code" content="{{ $country->code }}">
        <meta name="current_country_id" content="{{ $country->id }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description')"/>
    <title>{{ __('common.project_name') . ' |' }} @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.min.css') }}">

    @if(\LaravelLocalization::getCurrentLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/css/plugin.rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.rtl.min.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    @endif
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-bnaia.png') }}">
    <link rel="stylesheet" href="{{ asset('css/unicons/line.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    @vite(['resources/scss/progress-bar.scss'])
    @vite(['resources/scss/rtl-validation.scss'])
    @vite('resources/scss/app.scss')
    @stack('styles')
    @yield('styles')
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>

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
