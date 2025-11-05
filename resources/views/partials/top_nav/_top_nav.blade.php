

<nav class="navbar navbar-light">
    <div class="navbar-left">
        <div class="logo-area">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('assets/img/logo.png') }}" alt="svg">
            </a>
            <a href="#" class="sidebar-toggle">
                <img class="svg" src="{{ asset('assets/img/svg/align-center-alt.svg') }}" alt="img"></a>
        </div>
    </div>
    <div class="navbar-right">
        <ul class="navbar-right__menu">
            @include('partials.top_nav._search')
            @include('partials.top_nav._vendors_withdraw_requests')
            @include('partials.top_nav._become_vendor_requests')
            @include('partials.top_nav._orders')
            @include('partials.top_nav._messages')
            @include('partials.top_nav._notifications')
            @include('partials.top_nav._language_selector')
            @include('partials.top_nav._user_profile')
        </ul>
        <div class="navbar-right__mobileAction d-md-none">
            <a href="#" class="btn-search">
                <img src="{{ asset('assets/img/svg/search.svg') }}" alt="search" class="svg feather-search">
                <img src="{{ asset('assets/img/svg/x.svg') }}" alt="x" class="svg feather-x">
            </a>
            <a href="#" class="btn-author-action">
                <img src="{{ asset('assets/img/svg/more-vertical.svg') }}" alt="more-vertical" class="svg"></a>
        </div>
    </div>
</nav>
