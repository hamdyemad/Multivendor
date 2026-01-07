@php
    $appName = config('app.name');
    $primaryColor = config('branding.colors.primary');
    $secondaryColor = config('branding.colors.secondary');
    $textColor = config('branding.colors.text');
    $lightGray = config('branding.colors.light_gray');
    $borderColor = config('branding.colors.border');
@endphp

<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('customer.welcome_email.subject', ['app_name' => $appName]) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: {{ $textColor }};
            background-color: #f9f9f9;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            font-size: 16px;
            opacity: 0.95;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 22px;
            font-weight: 600;
            color: {{ $primaryColor }};
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: {{ $primaryColor }};
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid {{ $borderColor }};
        }

        .section p {
            margin-bottom: 12px;
            line-height: 1.8;
            font-size: 15px;
        }

        .features {
            background-color: {{ $lightGray }};
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .feature-item:last-child {
            margin-bottom: 0;
        }

        .feature-icon {
            color: {{ $primaryColor }};
            font-weight: bold;
            margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 12px;
            font-size: 18px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
        }

        .highlight-box {
            background-color: {{ $lightGray }};
            border-left: 4px solid {{ $primaryColor }};
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .highlight-box.exclusive {
            border-left-color: {{ $secondaryColor }};
        }

        .footer {
            background-color: {{ $lightGray }};
            padding: 30px;
            text-align: center;
            border-top: 1px solid {{ $borderColor }};
            font-size: 13px;
            color: #666666;
        }

        .social-links {
            margin: 15px 0;
        }

        .social-links a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: {{ $primaryColor }};
            color: white;
            text-decoration: none;
            border-radius: 50%;
            line-height: 36px;
            margin: 0 5px;
            transition: background-color 0.2s;
        }

        .social-links a:hover {
            background-color: {{ $secondaryColor }};
        }

        .divider {
            height: 1px;
            background-color: {{ $borderColor }};
            margin: 20px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #999999;
            font-size: 12px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }

            .content {
                padding: 20px 15px;
            }

            .header {
                padding: 30px 15px;
            }

            .header h1 {
                font-size: 24px;
            }

            .greeting {
                font-size: 18px;
            }

            .section-title {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('assets/img/logo_en.png') }}" alt="{{ $appName }}" style="max-width: 180px; height: auto; margin-bottom: 15px;">
            <h1>{{ $appName }}</h1>
            <p>{{ __('customer.welcome_email.thank_you', ['app_name' => $appName]) }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                {{ __('customer.welcome_email.greeting', ['name' => $customer->first_name]) }}
            </div>

            <!-- Account Created Section -->
            <div class="section">
                <p>{{ __('customer.welcome_email.account_created') }}</p>
                <p>{{ __('customer.welcome_email.ready_to_explore') }}</p>
            </div>

            <div class="divider"></div>

            <!-- What's Next Section -->
            <div class="section">
                <div class="section-title">{{ __('customer.welcome_email.what_next') }}</div>
                <div class="features">
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>{{ __('customer.welcome_email.browse_products') }}</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>{{ __('customer.welcome_email.create_wishlist') }}</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>{{ __('customer.welcome_email.track_orders') }}</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">✓</span>
                        <span>{{ __('customer.welcome_email.manage_profile') }}</span>
                    </div>
                </div>
            </div>

            <!-- CTA Button -->
            {{-- <div class="text-center">
                <a href="{{ url('/') }}" class="cta-button">
                    {{ __('customer.welcome_email.contact_support') }}
                </a>
            </div> --}}

            <div class="divider"></div>

            <!-- Support Section -->
            <div class="section">
                <div class="section-title">{{ __('customer.welcome_email.need_help') }}</div>
                <p>{{ __('customer.welcome_email.support_team') }}</p>
            </div>

            <!-- Exclusive Offers Section -->
            <div class="highlight-box exclusive">
                <strong style="color: {{ $secondaryColor }};">{{ __('customer.welcome_email.exclusive_offers') }}</strong>
                <p style="margin-top: 8px; margin-bottom: 0;">{{ __('customer.welcome_email.stay_updated') }}</p>
            </div>

            <!-- Social Media -->
            {{-- <div class="text-center">
                <p style="margin-top: 20px; margin-bottom: 10px; font-size: 14px;">
                    {{ __('customer.welcome_email.follow_us') }}
                </p>
                <div class="social-links">
                    <a href="#" title="Facebook">f</a>
                    <a href="#" title="Twitter">𝕏</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="LinkedIn">in</a>
                </div>
            </div> --}}
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin-bottom: 10px;">
                <strong>{{ __('customer.welcome_email.best_regards') }}</strong><br>
                {{ __('customer.welcome_email.team', ['app_name' => $appName]) }}
            </p>
            <p class="text-muted" style="margin-bottom: 10px;">
                {{ __('customer.welcome_email.footer_copyright', ['year' => date('Y'), 'app_name' => $appName]) }}
            </p>
            <p class="text-muted">
                {{ __('customer.welcome_email.footer_note') }}
            </p>
        </div>
    </div>
</body>
</html>
