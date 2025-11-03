<li class="nav-author">
    <div class="dropdown-custom">
        <a href="javascript:;" class="nav-item-toggle"><img src="{{ asset('assets/img/author-nav.jpg') }}" alt="" class="rounded-circle">
            @if(Auth::check())
                <span class="nav-item__title"><i class="las la-angle-down nav-item__arrow"></i></span>
            @endif
        </a>
        <div class="dropdown-wrapper">
            <div class="nav-author__info">
                <div class="author-img">
                    <img src="{{ asset('assets/img/author-nav.jpg') }}" alt="" class="rounded-circle">
                </div>
                <div>
                    @if(Auth::check())
                        <h6 class="text-lowercase">
                            {{ auth()->user()->email }}
                        </h6>
                    @endif
                    @if(count(auth()->user()->roles) > 0)
                        <span>{{ auth()->user()->roles()->first()->getTranslation('name', app()->getLocale()) }}</span>
                    @endif
                </div>
            </div>
            <div class="nav-author__options">
                <ul>
                    <li>
                        <a href="">
                            <img src="{{ asset('assets/img/svg/user.svg') }}" alt="user" class="svg"> Profile</a>
                    </li>
                    <li>
                        <a href="">
                            <img src="{{ asset('assets/img/svg/settings.svg') }}" alt="settings" class="svg"> Settings</a>
                    </li>
                    <li>
                        <a href="">
                            <img src="{{ asset('assets/img/svg/key.svg') }}" alt="key" class="svg"> Billing</a>
                    </li>
                    <li>
                        <a href="">
                            <img src="{{ asset('assets/img/svg/users.svg') }}" alt="users" class="svg"> Activity</a>
                    </li>
                    <li>
                        <a href="">
                            <i class="uil uil-file-contract-dollar"></i> Terms and Conditions</a>
                    </li>
                    <li>
                        <a href="">
                            <img src="{{ asset('assets/img/svg/bell.svg') }}" alt="bell" class="svg"> Help</a>
                    </li>
                </ul>
                <a href="" class="nav-author__signout" onclick="event.preventDefault();document.getElementById('logout').submit();">
                    <img src="{{ asset('assets/img/svg/log-out.svg') }}" alt="log-out" class="svg">
                     Sign Out</a>
                    <form style="display:none;" id="logout" action="{{ route('logout') }}" method="POST">
                        @csrf
                        @method('post')
                    </form>
            </div>
        </div>
    </div>
</li>
