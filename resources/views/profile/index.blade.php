@extends('layout.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title">{{ __('My Profile') }}</h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="uil uil-estate"></i>{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Profile') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header">
                    <h6>{{ __('Profile Information') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-25">
                                <label for="name" class="form-label">{{ __('Name') }}</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->getTranslation('name', app()->getLocale())) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-25">
                                <label for="email" class="form-label">{{ __('Email') }}</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_type" class="form-label">{{ __('User Type') }}</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $user->user_type ? $user->user_type->getTranslation('name', app()->getLocale()) : 'N/A' }}" 
                                           readonly 
                                           disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="roles" class="form-label">{{ __('Roles') }}</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $user->roles->pluck('name')->join(', ') }}" 
                                           readonly 
                                           disabled>
                                </div>
                            </div>
                        </div>

                        <div class="button-group d-flex pt-25">
                            <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize">
                                {{ __('Update Profile') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-default card-md mb-4">
                <div class="card-header">
                    <h6>{{ __('Change Password') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-25">
                                <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-25">
                                <label for="password" class="form-label">{{ __('New Password') }}</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('Password must be at least 8 characters') }}</small>
                            </div>

                            <div class="col-md-6 mb-25">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>

                        <div class="button-group d-flex pt-25">
                            <button type="submit" class="btn btn-warning btn-default btn-squared text-capitalize">
                                {{ __('Update Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
