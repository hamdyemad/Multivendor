@extends('layout.app')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('systemsetting::currency.currencies_management'), 'url' => route('admin.system-settings.currencies.index')],
                    ['title' => isset($currency) ? __('systemsetting::currency.edit_currency') : __('systemsetting::currency.create_currency')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            {{ isset($currency) ? __('systemsetting::currency.edit_currency') : __('systemsetting::currency.create_currency') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alertContainer"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ __('systemsetting::currency.validation_errors') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="currencyForm" method="POST" action="{{ isset($currency) ? route('admin.system-settings.currencies.update', $currency->id) : route('admin.system-settings.currencies.store') }}">
                            @csrf
                            @if(isset($currency))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <!-- Translation Fields -->
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name_{{ $language->id }}" class="form-label w-100" @if($language->code == 'ar') dir="rtl" @endif>{{ __('systemsetting::currency.name_' . ($language->code == 'ar' ? 'arabic' : 'english')) }} <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                   id="name_{{ $language->id }}"
                                                   name="translations[{{ $language->id }}][name]"
                                                   value="{{ old('translations.' . $language->id . '.name', isset($currency) ? $currency->translations->where('lang_id', $language->id)->first()->lang_value ?? '' : '') }}"
                                                   @if($language->rtl) dir="rtl" @endif
                                                   placeholder="{{ $language->code == 'ar' ? 'أدخل اسم العملة' : 'e.g., US Dollar, Saudi Riyal' }}">
                                            @error('translations.' . $language->id . '.name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="code" class="form-label">{{ __('systemsetting::currency.currency_code') }} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15 text-uppercase"
                                               id="code"
                                               name="code"
                                               value="{{ old('code', isset($currency) ? $currency->code : '') }}"
                                               maxlength="3"
                                               placeholder="e.g., USD, SAR, EUR">
                                        @error('code')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="symbol" class="form-label">{{ __('systemsetting::currency.currency_symbol') }} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                               id="symbol"
                                               name="symbol"
                                               value="{{ old('symbol', isset($currency) ? $currency->symbol : '') }}"
                                               maxlength="10"
                                               placeholder="e.g., $, ﷼, €">
                                        @error('symbol')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Active Status Switcher --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                            {{ __('systemsetting::currency.active') }}
                                        </label>
                                        <div class="dm-switch-wrap d-flex align-items-center">
                                            <div class="form-check form-switch form-switch-primary form-switch-md">
                                                <input type="hidden" name="active" value="0">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       id="active"
                                                       name="active"
                                                       value="1"
                                                       {{ old('active', isset($currency) ? $currency->active : 1) == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active"></label>
                                            </div>
                                        </div>
                                        @error('active')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mt-4 d-flex align-items-center justify-content-end">
                                        <a href="{{ route('admin.system-settings.currencies.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                            <i class="uil uil-arrow-left"></i> {{ __('systemsetting::currency.back_to_list') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize ms-2">
                                            <i class="uil uil-check"></i> {{ isset($currency) ? __('systemsetting::currency.update_currency') : __('systemsetting::currency.create_currency') }}
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
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currencyForm = document.getElementById('currencyForm');
    const submitBtn = currencyForm.querySelector('button[type="submit"]');
    const alertContainer = document.getElementById('alertContainer');
    let originalBtnHtml = '';

    currencyForm.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}';

        const loadingText = @json(isset($currency) ? trans('loading.updating') : trans('loading.creating'));
        const loadingSubtext = '{{ trans("loading.please_wait") }}';
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.querySelector('.loading-text').textContent = loadingText;
            overlay.querySelector('.loading-subtext').textContent = loadingSubtext;
        }

        LoadingOverlay.show();
        alertContainer.innerHTML = '';

        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        LoadingOverlay.animateProgressBar(30, 300).then(() => {
            const formData = new FormData(currencyForm);

            return fetch(currencyForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
        })
        .then(response => {
            LoadingOverlay.animateProgressBar(60, 200);

            if (!response.ok) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
        })
        .then(data => {
            return LoadingOverlay.animateProgressBar(100, 200).then(() => {
                const successMessage = @json(isset($currency) ? trans('loading.updated_successfully') : trans('loading.created_successfully'));
                LoadingOverlay.showSuccess(
                    successMessage,
                    '{{ trans("loading.redirecting") }}'
                );

                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("admin.system-settings.currencies.index") }}';
                }, 1500);
            });
        })
        .catch(error => {
            LoadingOverlay.hide();

            if (error.errors) {
                Object.keys(error.errors).forEach(key => {
                    let input = null;

                    input = document.querySelector(`[name="${key}"]`);

                    if (!input && key.includes('.')) {
                        const bracketKey = key.replace(/^([^.]+)\.(\d+)\.([^.]+)$/, '$1[$2][$3]');
                        input = document.querySelector(`[name="${bracketKey}"]`);
                    }

                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'text-danger small mt-1';
                        feedback.textContent = error.errors[key][0];
                        input.parentNode.appendChild(feedback);
                    }
                });

                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            const errorMessage = error.message || '{{ __("common.error_occurred") ?? "An error occurred" }}';
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>${errorMessage}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });
});
</script>
@endpush
