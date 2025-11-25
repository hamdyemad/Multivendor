<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .content {
            color: #555;
            line-height: 1.6;
        }
        .content p {
            margin: 15px 0;
        }
        .otp-box {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        .otp-expiry {
            font-size: 12px;
            color: #999;
            margin-top: 10px;
        }
        .verify-button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }
        .verify-button:hover {
            background-color: #0056b3;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ trans('customer.app_name') }}</h1>
        </div>

        <div class="content">
            @if($type === 'email_verification')
                <p>{{ trans('customer.otp_email.email_verification_greeting') }}</p>
                <p>{{ trans('customer.otp_email.email_verification_thank_you', ['app_name' => trans('customer.app_name')]) }}</p>
                <p>{{ trans('customer.otp_email.email_verification_intro') }}</p>

                @if($verificationToken)
                    <div class="button-container">
                        <a href="{{ route('verify-email', $verificationToken) }}" class="verify-button">
                            {{ trans('customer.otp_email.verify_account_button') }}
                        </a>
                    </div>
                    <p style="text-align: center; color: #666; font-size: 14px;">
                        {{ trans('customer.otp_email.verify_button_expires', ['minutes' => $expiresInMinutes]) }}
                    </p>
                @else
                    <div class="otp-box">
                        <div class="otp-code">{{ $otp }}</div>
                        <div class="otp-expiry">{{ trans('customer.otp_email.expires_in', ['minutes' => $expiresInMinutes]) }}</div>
                    </div>
                @endif

                <p>{{ trans('customer.otp_email.email_verification_ignore') }}</p>

            @elseif($type === 'password_reset')
                <p>{{ trans('customer.otp_email.password_reset_greeting') }}</p>
                <p>{{ trans('customer.otp_email.password_reset_intro') }}</p>

                <div class="otp-box">
                    <div class="otp-code">{{ $otp }}</div>
                    <div class="otp-expiry">{{ trans('customer.otp_email.expires_in', ['minutes' => $expiresInMinutes]) }}</div>
                </div>

                <div class="warning">
                    <strong>{{ trans('customer.otp_email.password_reset_security_notice') }}</strong>
                </div>

            @else
                <p>{{ trans('customer.otp_email.default_greeting') }}</p>
                <p>{{ trans('customer.otp_email.default_intro') }}</p>

                <div class="otp-box">
                    <div class="otp-code">{{ $otp }}</div>
                    <div class="otp-expiry">{{ trans('customer.otp_email.expires_in', ['minutes' => $expiresInMinutes]) }}</div>
                </div>
            @endif

            <p>
                <strong>{{ trans('customer.otp_email.security_tips_title') }}</strong>
                <ul>
                    <li>{{ trans('customer.otp_email.security_tip_1') }}</li>
                    <li>{{ trans('customer.otp_email.security_tip_2', ['app_name' => trans('customer.app_name')]) }}</li>
                    <li>{{ trans('customer.otp_email.security_tip_3', ['minutes' => $expiresInMinutes]) }}</li>
                </ul>
            </p>

            <p>{{ trans('customer.otp_email.best_regards') }}<br>{{ trans('customer.otp_email.team', ['app_name' => trans('customer.app_name')]) }}</p>
        </div>

        <div class="footer">
            <p>{{ trans('customer.otp_email.footer_copyright', ['year' => date('Y'), 'app_name' => trans('customer.app_name')]) }}</p>
            <p>{{ trans('customer.otp_email.footer_note') }}</p>
        </div>
    </div>
</body>
</html>
