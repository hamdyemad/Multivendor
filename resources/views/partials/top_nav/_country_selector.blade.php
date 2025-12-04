@php
    $countries = collect();
    $currentCountryCode = 'EG';
    $currentCountry = null;

    try {
        $countries = \Modules\AreaSettings\app\Models\Country::where('active', 1)->get();
        // Get country code from session or default to EG
        $currentCountryCode = strtoupper(session('country_code', 'EG'));
        $currentCountry = $countries->firstWhere('code', $currentCountryCode) ?? $countries->first();
    } catch (\Exception $e) {
        // Silently fail - use defaults
        $countries = collect();
        $currentCountryCode = 'EG';
        $currentCountry = null;
    }

@endphp

@if(in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds()))
<li class="nav-flag-select nav-country-select">
    <div class="dropdown-custom">
        <a href="javascript:;" class="nav-item-toggle" title="{{ __('dashboard.select_country') }}">
            <i class="uil uil-globe" style="font-size: 20px;"></i>
            <span class="nav-item__title ms-1" style="font-size: 12px; font-weight: 600;">
                {{ $currentCountryCode }}
            </span>
        </a>
        <div class="dropdown-wrapper dropdown-wrapper--small">
            <div class="dropdown-header px-3 py-2 border-bottom">
                <small class="text-muted fw-bold">{{ __('dashboard.select_country') }}</small>
            </div>
            @foreach ($countries as $country)
                @php
                    // Build the new URL with the selected country code
                    $currentUrl = request()->getPathInfo();
                    $segments = explode('/', trim($currentUrl, '/'));

                    // URL format: /{lang}/{country}/admin/...
                    if (count($segments) >= 3 && $segments[2] === 'admin') {
                        // Replace segment 1 (country code) with new country code
                        $segments[1] = strtolower($country->code);
                    }

                    $newUrl = '/' . implode('/', $segments);
                @endphp
                <a href="{{ $newUrl }}" class="country-item {{ strtoupper($country->code) == $currentCountryCode ? 'active' : '' }}">
                    <span class="country-code-badge me-2">
                        {{ strtoupper($country->code) }}
                    </span>
                    <span class="country-name">
                        {{ $country->getTranslation('name', app()->getLocale()) ?? $country->getTranslation('name', 'en') ?? $country->code }}
                    </span>
                    @if(strtoupper($country->code) == $currentCountryCode)
                        <i class="uil uil-check ms-auto text-success"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</li>

<style>
    .nav-country-select .dropdown-wrapper {
        min-width: 200px;
    }

    .nav-country-select .dropdown-header {
        background-color: #f8f9fa;
    }

    .nav-country-select .country-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        transition: all 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .nav-country-select .country-item:last-child {
        border-bottom: none;
    }

    .nav-country-select .country-item:hover {
        background-color: rgba(95, 99, 242, 0.08);
    }

    .nav-country-select .country-item.active {
        background-color: rgba(95, 99, 242, 0.12);
        color: #5f63f2;
    }

    .nav-country-select .country-code-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 22px;
        padding: 0 6px;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #5f63f2 0%, #8e92f7 100%);
        border-radius: 4px;
        letter-spacing: 0.5px;
    }

    .nav-country-select .country-item.active .country-code-badge {
        background: linear-gradient(135deg, #20c997 0%, #38d9a9 100%);
    }

    .nav-country-select .country-name {
        flex: 1;
        font-size: 13px;
    }

    .nav-country-select .nav-item-toggle {
        display: flex;
        align-items: center;
    }

    /* RTL Support */
    html[dir="rtl"] .nav-country-select .country-code-badge {
        margin-left: 8px;
        margin-right: 0;
    }

    html[dir="rtl"] .nav-country-select .country-item .uil-check {
        margin-right: auto;
        margin-left: 0;
    }
</style>

@endif
