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
            background: #004999;
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
            {{-- Left Side - Image --}}
            <div class="auth-image">
                <div class="auth-image-content">
                    <h2 style="color: #fff;">Reset Your Password</h2>
                    <p>Enter your reset code and new password to regain access to your account.</p>
                </div>
            </div>

            {{-- Right Side - Form --}}
            <div class="auth-form">
                <div style="text-align: center; margin-bottom: 30px;">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="logo" style="width: 100px;">
                </div>

                <h3>Reset Password</h3>
                <p>Please fill in the required details below</p>

                @if (session('message'))
                    <div class="alert alert-danger text-center mb-3">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success text-center mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger text-center mb-3">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('forgetPassword.reset-store', $user) }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <div class="position-relative">
                            <i class="uil uil-key-skeleton input-icon"></i>
                            <input type="text" class="form-control" id="reset_code" name="reset_code"
                                value="{{ old('reset_code') }}" placeholder="Reset Code" autocomplete="off">
                        </div>
                        @error('reset_code')
                            <p class="text-danger small mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="position-relative">
                            <i class="uil uil-lock-alt input-icon"></i>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="New Password" autocomplete="new-password">
                        </div>
                        @error('password')
                            <p class="text-danger small mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="position-relative">
                            <i class="uil uil-lock-alt input-icon"></i>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="Confirm Password" autocomplete="new-password">
                        </div>
                        @error('password_confirmation')
                            <p class="text-danger small mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="login-btn">Update Password</button>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" style="color: #0056b7; text-decoration: none;">
                            ← Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
