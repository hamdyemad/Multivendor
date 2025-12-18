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
            background: url("{{ asset('assets/img/electrical.avif') }}") center center/cover no-repeat;
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

        .form-control {
            height: 50px;
            border-radius: 10px;
            border: 1px solid #ddd;
            padding-left: 45px;
            font-size: 15px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #0056b7;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 18px;
            z-index: 20;
        }

        .position-relative {
            position: relative;
        }

        .login-btn {
            width: 100%;
            background: #0056b7;
            border: none;
            border-radius: 10px;
            height: 50px;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: #0056b3;
        }

        .social-login {
            text-align: center;
            margin-top: 20px;
        }

        .social-login button {
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
            height: 45px;
            width: 45px;
            margin: 0 6px;
            font-size: 18px;
            color: #555;
            transition: 0.3s;
        }

        .social-login button:hover {
            background: #f0f0f0;
        }

        .forgot-pass {
            text-align: right;
            font-size: 14px;
        }

        .forgot-pass a {
            color: #0056b7;
            text-decoration: none;
        }

        .forgot-pass a:hover {
            text-decoration: underline;
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
                    <h2 style="color: #fff;">Welcome to Bnaia</h2>
                    <p>Are you unsure about pricing the order you need for your home finishing? Send us your order as an
                        image, Excel file, or PDF, and we will price it within an hour.</p>
                </div>
            </div>
            <div class="auth-form">


                <div style="text-align: center; margin-bottom: 30px;">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="svg" style="width: 100px;">
                </div>

                <h3>Welcome Back </h3>
                <p>Login to continue to your account</p>

                @if (session('message'))
                    <div class="alert alert-danger text-center mb-3">
                        {{ session('message') }}
                    </div>
                @endif

                <form action="{{ route('authenticate') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <div class="position-relative">
                            <i class="uil uil-envelope input-icon"></i>
                            <input type="text" class="form-control" name="email" value="{{ old('email') }}"
                                placeholder="Email Address">
                        </div>
                        @error('email')
                            <p class="text-danger small mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="position-relative">
                            <i class="uil uil-lock-alt input-icon"></i>
                            <input type="password" class="form-control" id="password-field" name="password"
                                placeholder="Password" style="padding-left: 45px !important;">
                        </div>
                        @error('password')
                            <p class="text-danger small mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="forgot-pass mb-3">
                        <a href="{{ route('forgetPassword.index') }}">Forgot password?</a>
                    </div>

                    <button type="submit" class="login-btn">Login</button>
                </form>
            </div>
        </div>
    </div>
@endsection
