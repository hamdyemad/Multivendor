<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
        }
        .header {
            background: linear-gradient(135deg, #0056b7 0%, #cb1037 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .content h2 {
            color: #0056b7;
            font-size: 18px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .content p {
            margin: 10px 0;
            line-height: 1.8;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        .feature-list li {
            padding: 10px;
            background-color: #f9f9f9;
            margin-bottom: 10px;
            border-left: 4px solid #0056b7;
        }
        .feature-list strong {
            color: #0056b7;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #0056b7 0%, #cb1037 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/img/logo_en.png') }}" alt="{{ trans('customer.app_name') }}" style="max-width: 150px; height: auto; margin-bottom: 10px;">
            <h1>{{ __('customer.welcome_email.greeting', ['name' => $customer->first_name]) }}</h1>
        </div>

        <div class="content">
            <p>{{ __('customer.welcome_email.thank_you', ['app_name' => trans('customer.app_name')]) }} {{ __('customer.welcome_email.account_created') }}</p>

            <center>
                <a href="{{ env('FRONT_END_URL') }}" class="button">{{ __('customer.welcome_email.start_exploring') }}</a>
            </center>

            <h2>{{ __('customer.welcome_email.what_next') }}</h2>
            <ul class="feature-list">
                <li><strong>{{ __('customer.welcome_email.complete_profile') }}:</strong> {{ __('customer.welcome_email.add_address') }}</li>
                <li><strong>{{ __('customer.welcome_email.explore_products') }}:</strong> {{ __('customer.welcome_email.browse_products') }}</li>
                <li><strong>{{ __('customer.welcome_email.secure_transactions') }}:</strong> {{ __('customer.welcome_email.secure_payment') }}</li>
            </ul>

            <h2>{{ __('customer.welcome_email.need_help') }}</h2>
            <p>{{ __('customer.welcome_email.support_team') }}</p>

            <div class="footer">
                <p>{{ __('customer.welcome_email.security_notice') }}</p>
                <p>{{ __('customer.welcome_email.footer_copyright', ['year' => date('Y'), 'app_name' => trans('customer.app_name')]) }}</p>
            </div>
        </div>
    </div>
</body>
</html>
