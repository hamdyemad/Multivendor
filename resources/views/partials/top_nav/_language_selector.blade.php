@php
    $langs = \App\Models\Language::all();
@endphp
<li class="nav-flag-select">
    <div class="dropdown-custom">
        @switch(app()->getLocale())
            @case('en')
                <a href="javascript:;" class="nav-item-toggle"><img src="{{ asset('assets/img/eng.png') }}" alt="" class="rounded-circle"></a>
                @break
            @case('ar')
                <a href="javascript:;" class="nav-item-toggle"><img src="{{ asset('assets/img/iraq.png') }}" alt="" class="rounded-circle"></a>
                @break
            @case('gr')
                <a href="javascript:;" class="nav-item-toggle"><img src="{{ asset('assets/img/ger.png') }}" alt="" class="rounded-circle"></a>
                @break
            @default
                <a href="javascript:;" class="nav-item-toggle">
                    @if(LaravelLocalization::getCurrentLocale() == 'ar')
                        <img src="{{ asset('assets/img/iraq.png') }}" alt="" class="rounded-circle">
                    @else
                        <img src="{{ asset('assets/img/eng.png') }}" alt="" class="rounded-circle">
                    @endif
                </a>
                @break
        @endswitch
        <div class="dropdown-wrapper dropdown-wrapper--small">
            @foreach ($langs as $lang)
                <a hreflang="{{ $lang->code }}"
                href="{{ LaravelLocalization::getLocalizedURL($lang->code, null, [], true) }}">
                    @if($lang->code == 'ar')
                        <img src="{{ asset('assets/img/iraq.png') }}" alt="">
                    @else
                        <img src="{{ asset('assets/img/eng.png') }}" alt="">
                    @endif
                    
                    @if($lang->code == 'ar')
                        @if(app()->getLocale() == 'ar')
                            عربى
                        @else
                            Arabic
                        @endif
                    @else
                        @if(app()->getLocale() == 'ar')
                            انجليزي
                        @else
                            English
                        @endif
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</li>
