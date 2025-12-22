@extends('layout.app')

@section('title', trans('admin.my_profile'))

@push('styles')
    <style>
        .fs-15.color-dark {
            line-height: 1.6;
        }

        .fs-15.color-dark strong {
            color: #2c3e50;
            font-weight: 600;
        }

        .card-holder .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .card-header-gradient {
            background: linear-gradient(90deg, #1e40af 0%, #db2777 100%) !important;
            color: white !important;
            border-radius: 10px 10px 0 0 !important;
        }

        .card-header-gradient h3,
        .card-header-gradient h5 {
            color: white !important;
        }

        .box-items-translations {
            padding: 15px;
            border: 1px solid #f1f2f6;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .field-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #adb5bd;
            z-index: 10;
        }

        [dir="rtl"] .field-icon {
            right: auto;
            left: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('admin.my_profile')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">{{ trans('admin.my_profile') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="card card-holder overflow-hidden border-0 shadow-sm mb-4">
                                        <div class="card-header card-header-gradient">
                                            <h3 class="mb-0">
                                                <i class="uil uil-user me-1"></i>{{ trans('admin.profile_information') }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                {{-- Profile Image Section --}}
                                                <div class="col-md-12 mb-30">
                                                    <div class="d-flex justify-content-center">
                                                        <div style="width: 200px;">
                                                            <x-image-upload :id="'profile_image_input'" :name="'image'"
                                                                :label="''" :existingImage="$user->image" :placeholder="trans('admin.click_to_upload_image')" />
                                                        </div>
                                                    </div>
                                                </div>

                                                @php
                                                    $userModel = new class ($user) {
                                                        private $user;
                                                        public function __construct($user)
                                                        {
                                                            $this->user = $user;
                                                        }
                                                        public function getTranslation($key, $langCode)
                                                        {
                                                            $language = \App\Models\Language::where(
                                                                'code',
                                                                $langCode,
                                                            )->first();
                                                            return $this->user->translations
                                                                ->where('lang_id', $language?->id)
                                                                ->where('lang_key', $key)
                                                                ->first()->lang_value ?? '';
                                                        }
                                                    };
                                                @endphp

                                                <x-multilingual-input name="name" :label="trans('admin.name')" :labelAr="trans('admin.name', [], 'ar')"
                                                    type="text" :placeholder="trans('admin.name')" :placeholderAr="trans('admin.name', [], 'ar')" :required="true"
                                                    :languages="$languages ?? \App\Models\Language::all()" :model="$userModel" oldPrefix="translations"
                                                    :cols="6" />

                                                <div class="col-md-6 mb-25">
                                                    <label for="email"
                                                        class="form-label fw-500">{{ trans('admin.email') }}</label>
                                                    <div class="position-relative">
                                                        <input type="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            id="email" name="email"
                                                            value="{{ old('email', $user->email) }}"
                                                            placeholder="{{ trans('admin.email') }}" required>
                                                    </div>
                                                    @error('email')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3 mb-25 text-muted">
                                                    <label
                                                        class="form-label fw-500 mb-1 d-block">{{ trans('admin.role') }}</label>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($user->roles as $role)
                                                            <span
                                                                class="d-inline-flex align-items-center px-2 py-1 rounded-pill bg- border text-secondary fs-12">
                                                                <i class="uil uil-shield-check me-1 text-primary"></i>
                                                                {{ $role->getNameAttribute() }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                @if ($user->vendor)
                                                    <div class="col-md-3 mb-25 text-muted">
                                                        <label
                                                            class="form-label fw-500 mb-1 d-block">{{ trans('admin.vendor') }}</label>
                                                        <p class="fs-14 color-dark mb-0">
                                                            <i class="uil uil-building me-1 text-primary"></i>
                                                            {{ $user->vendor->name }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="submit"
                                                    class="btn btn-primary btn-default btn-squared shadow-sm">
                                                    <i class="uil uil-check me-1"></i>{{ trans('admin.update_user') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                {{-- Change Password Form --}}
                                <form action="{{ route('admin.profile.password.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="card card-holder overflow-hidden border-0 shadow-sm">
                                        <div class="card-header card-header-gradient">
                                            <h3 class="mb-0">
                                                <i class="uil uil-lock me-1"></i>{{ trans('admin.change_password') }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-25">
                                                    <label for="current_password"
                                                        class="form-label fw-500">{{ trans('admin.current_password') }}</label>
                                                    <div class="position-relative">
                                                        <input type="password"
                                                            class="form-control @error('current_password') is-invalid @enderror"
                                                            id="current_password" name="current_password"
                                                            placeholder="{{ trans('admin.current_password') }}" required>
                                                        <span toggle="#current_password"
                                                            class="uil uil-eye-slash field-icon toggle-password"></span>
                                                    </div>
                                                    @error('current_password')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 mb-25">
                                                    <label for="password"
                                                        class="form-label fw-500">{{ trans('admin.new_password') }}</label>
                                                    <div class="position-relative">
                                                        <input type="password"
                                                            class="form-control @error('password') is-invalid @enderror"
                                                            id="password" name="password"
                                                            placeholder="{{ trans('admin.new_password') }}" required>
                                                        <span toggle="#password"
                                                            class="uil uil-eye-slash field-icon toggle-password"></span>
                                                    </div>
                                                    @error('password')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">{{ trans('admin.password_min_8') }}</small>
                                                </div>

                                                <div class="col-md-6 mb-25">
                                                    <label for="password_confirmation"
                                                        class="form-label fw-500">{{ trans('admin.confirm_password') }}</label>
                                                    <div class="position-relative">
                                                        <input type="password" class="form-control"
                                                            id="password_confirmation" name="password_confirmation"
                                                            placeholder="{{ trans('admin.confirm_password') }}" required>
                                                        <span toggle="#password_confirmation"
                                                            class="uil uil-eye-slash field-icon toggle-password"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="submit"
                                                    class="btn btn-warning btn-default btn-squared shadow-sm">
                                                    <i
                                                        class="uil uil-key-skeleton me-1"></i>{{ trans('admin.change_password') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.toggle-password', function() {
            $(this).toggleClass('uil-eye uil-eye-slash');
            let input = $($(this).attr('toggle'));
            if (input.attr('type') == 'password') {
                input.attr('type', 'text');
            } else {
                input.attr('type', 'password');
            }
        });
    </script>
@endpush
