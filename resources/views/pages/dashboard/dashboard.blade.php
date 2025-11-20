@php
    $user_type = auth()->user()->user_type->name;
    $vendor = auth()->user()->vendor;
@endphp

@extends('layout.app')

@section('title')
    {{ $title }}
@endsection
@section('content')
    <div class="crm mb-25">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <div class="breadcrumb-action justify-content-center flex-wrap">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#"><i
                                                class="uil uil-estate"></i>{{ trans('dashboard.title') }}</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Sales Overview Header --}}
                @include('pages.dashboard.withdraw-transactions')
                @include('pages.dashboard.vendors-withdraw-transactions')
                @include('pages.dashboard.stats-overview')
                @include('pages.dashboard.income-and-expenses')



                {{-- Statistics Cards --}}
                @include('pages.dashboard.stats-cards')

                {{-- Orders Overview & Top Selling Products --}}
                <div class="col-12">
                    <div class="row">
                        @include('pages.dashboard.orders-overview')
                    </div>
                </div>
                {{-- Charts Row: Sales, Earnings, Total Sales --}}
                <div class="col-12">
                    <div class="row">
                        @include('pages.dashboard.total-sales-chart')
                        @include('pages.dashboard.earnings-chart')
                    </div>
                </div>

                @include('pages.dashboard.top-selling-products')
                @include('pages.dashboard.latest-orders')
                @include('pages.dashboard.best-customers')
                @if ($user_type == 'super_admin')
                    @include('pages.dashboard.top-vendors')
                @endif

                @include('pages.dashboard.recent-activities')

            </div>
            <!-- ends: .row -->
        </div>
    </div>

    {{-- Chart Scripts --}}
    @include('pages.dashboard.charts-scripts')
@endsection
