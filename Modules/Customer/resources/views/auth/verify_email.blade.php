@extends('auth.layout')

@section('content')
    <style>
        body {
            background-color: #0098ff;
            font-family: 'Poppins', sans-serif;
        }

        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 30px;
        }

        .auth-card {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .auth-image {
            flex: 1;
            background: url('https://services2.bnaia.com/_next/image?url=%2Fassets%2Fservices%2Felectrical.png&w=1080&q=85') center center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px;
            color: #fff;
            position: relative;
        }

        .auth-image::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
        }

        .auth-image-content {
            position: relative;
            z-index: 2;
        }

        .auth-image h2 {
            font-size: 28px;
            font-weight: 700;
        }

        .auth-image p {
            font-size: 15px;
            opacity: 0.9;
            margin-top: 10px;
        }

        .auth-form {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-form h3 {
            font-weight: 700;
            color: #0056b7;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .auth-form p {
            color: #555;
            margin-bottom: 30px;
        }

        .verify-icon {
            font-size: 60px;
            color: #0056b7;
            text-align: center;
            margin-bottom: 20px;
        }

        .verify-btn {
            width: 100%;
            background: #0056b7;
            border: none;
            border-radius: 10px;
            height: 50px;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            transition: 0.3s;
            cursor: pointer;
        }

        .verify-btn:hover {
            background: #0056b3;
        }

        .verify-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #0056b7;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0056b7;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .auth-card {
                flex-direction: column;
            }

            .auth-image {
                height: 250px;
            }

            .auth-form {
                padding: 40px 25px;
            }
        }
    </style>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-image">
                <div class="auth-image-content">
                    <h2 style="color: #fff;">Verify Your Email</h2>
                    <p>Complete your registration by verifying your email address. Click the button below to confirm.</p>
                </div>
            </div>
            <div class="auth-form">
                <div style="text-align: center; margin-bottom: 30px;">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="svg" style="width: 100px;">
                </div>

                <h3>Email Verification</h3>
                <p>Verify your email address to complete registration</p>

                @if (session('success'))
                    <div class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="verify-icon">
                    <i class="uil uil-envelope-check"></i>
                </div>

                @if ($token)
                    <form action="{{ route('verify-email.store') }}" method="POST" id="verifyForm">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <p style="text-align: center; color: #666; margin-bottom: 30px;">
                            Click the button below to verify your email address and complete your registration.
                        </p>

                        <button type="submit" class="verify-btn" id="verifyBtn">
                            <span id="btnText">Verify Email</span>
                            <span id="btnSpinner" style="display: none;">
                                <i class="uil uil-spinner-alt" style="animation: spin 1s linear infinite;"></i>
                            </span>
                        </button>
                    </form>
                @else
                    <div class="alert alert-danger text-center">
                        No verification token provided. Please use the link from your email.
                    </div>
                @endif

                <div class="back-link">
                    <a href="{{ route('login') }}">Back to Login</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('verifyForm');
            const verifyBtn = document.getElementById('verifyBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            if (form) {
                form.addEventListener('submit', function() {
                    verifyBtn.disabled = true;
                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline';
                });
            }
        });
    </script>
@endsection
