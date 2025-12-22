<li class="nav-author">
    <div class="dropdown-custom">
        <a href="javascript:;" class="nav-item-toggle">
            @php
                $userImage = Auth::user()->image
                    ? asset('storage/' . Auth::user()->image)
                    : asset('assets/img/author-nav.jpg');
            @endphp
            <img src="{{ $userImage }}" alt="" class="rounded-circle"
                style="width: 32px; height: 32px; object-fit: cover;">
            @if (Auth::check())
                <span class="nav-item__title"><i class="las la-angle-down nav-item__arrow"></i></span>
            @endif
        </a>
        <div class="dropdown-wrapper">
            <div class="nav-author__info">
                <div class="author-img">
                    <img src="{{ $userImage }}" alt="" class="rounded-circle"
                        style="width: 46px; height: 46px; object-fit: cover;">
                </div>
                <div>
                    @if (Auth::check())
                        <h6 class="text-lowercase">
                            {{ truncateString(auth()->user()->getTranslation('name', app()->getLocale()),10) }}
                        </h6>
                    @endif
                    @if (count(auth()->user()->roles) > 0)
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach (auth()->user()->roles as $role)
                                <span class="badge badge-round badge-info"
                                    style="font-size: 10px;">{{ $role->getTranslation('name', app()->getLocale()) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="nav-author__options">
                <ul>
                    <li>
                        <a href="{{ route('admin.profile.index') }}">
                            <img src="{{ asset('assets/img/svg/user.svg') }}" alt="user" class="svg">
                            {{ trans('admin.edit_profile') ?? 'Edit Profile' }}</a>
                    </li>
                </ul>
                <a style="background-color: red; color: #fff;" href="" class="nav-author__signout"
                    onclick="event.preventDefault();document.getElementById('logout').submit();">
                    <img src="{{ asset('assets/img/svg/log-out.svg') }}" alt="log-out" class="svg">
                    {{ __('admin.sign_out') ?? 'Sign Out' }}</a>
                <form style="display:none;" id="logout" action="{{ route('logout') }}" method="POST">
                    @csrf
                    @method('post')
                </form>
            </div>
        </div>
    </div>
</li>
