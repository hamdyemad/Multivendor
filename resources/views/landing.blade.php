<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bnaia - Welcome</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            color: #0056b7;
            padding: 40px 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        header p {
            font-size: 18px;
            opacity: 0.95;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .content {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .content h2 {
            color: #0056b7;
            margin-bottom: 20px;
            font-size: 28px;
        }

        .content p {
            margin-bottom: 15px;
            font-size: 16px;
            line-height: 1.8;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .feature {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #0056b7;
        }

        .feature h3 {
            color: #0056b7;
            margin-bottom: 10px;
        }

        .feature p {
            color: #666;
            font-size: 14px;
        }

        .cta-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0056b7 0%, #cb1037 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 86, 183, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #0056b7;
            border: 2px solid #0056b7;
        }

        .btn-secondary:hover {
            background: #0056b7;
            color: white;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 24px;
            }

            header p {
                font-size: 16px;
            }

            .content {
                padding: 20px;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <img src="{{ asset('assets/img/logo_en.png') }}" alt="Bnaia" style="max-width: 200px; height: auto; margin-bottom: 15px;">
            <h1>Bnaia</h1>
            <p>{{ __('Welcome to our platform') }}</p>
        </header>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                ✗ {{ session('error') }}
            </div>
        @endif

        <!-- Main Content -->
        <div class="content">
            <h2>{{ __('Welcome to') }} Bnaia</h2>
            <p>
                {{ __('Thank you for joining our community. Your email has been successfully verified!') }}
            </p>
            <p>
                {{ __('You can now log in to your account and start exploring our amazing platform with access to exclusive products and services.') }}
            </p>

            <!-- Features -->
            <div class="features">
                <div class="feature">
                    <h3>🛍️ {{ __('Browse Products') }}</h3>
                    <p>{{ __('Explore our wide range of products from trusted vendors') }}</p>
                </div>
                <div class="feature">
                    <h3>🔒 {{ __('Secure Shopping') }}</h3>
                    <p>{{ __('All transactions are protected with our secure payment gateway') }}</p>
                </div>
                <div class="feature">
                    <h3>📦 {{ __('Fast Delivery') }}</h3>
                    <p>{{ __('Track your orders in real-time and get updates on delivery') }}</p>
                </div>
                <div class="feature">
                    <h3>💬 {{ __('24/7 Support') }}</h3>
                    <p>{{ __('Our support team is here to help you anytime') }}</p>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="cta-buttons">
                <a href="{{ env('FRONT_END_URL') }}/ar/auth/sign-in" class="btn btn-primary">
                    {{ __('Log In to Your Account') }}
                </a>
                <a href="{{ env('FRONT_END_URL') }}" class="btn btn-secondary">
                    {{ __('Continue Browsing') }}
                </a>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; {{ date('Y') }} Bnaia. {{ __('All rights reserved.') }}</p>
        </footer>
    </div>
</body>
</html>
