@extends('layout.app')

@section('title', __('systemsetting::push-notification.send_notification'))

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
                    ['title' => __('systemsetting::push-notification.all_notifications'), 'url' => route('admin.system-settings.push-notifications.index')],
                    ['title' => __('systemsetting::push-notification.send_notification')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            <i class="uil uil-bell me-2"></i>
                            {{ __('systemsetting::push-notification.send_notification') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="alertContainer" class="mb-3"></div>

                        <form id="notificationForm" enctype="multipart/form-data">
                            @csrf

                            {{-- Notification Type --}}
                            <div class="mb-25">
                                <label class="il-gray fs-14 fw-500 mb-10 d-block">
                                    {{ __('systemsetting::push-notification.notification_type') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="type" id="type_all" value="all" checked>
                                    <label class="btn btn-outline-primary" for="type_all">
                                        <i class="uil uil-users-alt me-1"></i>{{ __('systemsetting::push-notification.type_all') }}
                                    </label>

                                    <input type="radio" class="btn-check" name="type" id="type_specific" value="specific">
                                    <label class="btn btn-outline-primary" for="type_specific">
                                        <i class="uil uil-user-check me-1"></i>{{ __('systemsetting::push-notification.type_specific') }}
                                    </label>
                                </div>
                            </div>

                            {{-- Customer Selection (shown when specific is selected) --}}
                            <div class="mb-25" id="customerSection" style="display: none;">
                                <x-searchable-tags
                                    name="customer_ids[]"
                                    :label="__('systemsetting::push-notification.select_customers')"
                                    :options="$customers->toArray()"
                                    :selected="[]"
                                    :placeholder="__('systemsetting::push-notification.search_customers')"
                                    :required="false"
                                    :multiple="true"
                                />
                            </div>

                            {{-- Title (Multilingual) --}}
                            <x-multilingual-input
                                name="title"
                                :label="'Title'"
                                :labelAr="'العنوان'"
                                :placeholder="'Title'"
                                :placeholderAr="'العنوان'"
                                type="text"
                                :required="true"
                                :languages="$languages"
                            />

                            {{-- Description (Multilingual) --}}
                            <x-multilingual-input
                                name="description"
                                :label="'Description'"
                                :labelAr="'الوصف'"
                                :placeholder="'Description'"
                                :placeholderAr="'الوصف'"
                                type="textarea"
                                :rows="4"
                                :required="true"
                                :languages="$languages"
                            />

                            {{-- Image --}}
                            <div class="mb-25">
                                <x-image-upload
                                    id="notification_image"
                                    name="image"
                                    :label="__('systemsetting::push-notification.image')"
                                    :required="false"
                                    :placeholder="__('systemsetting::push-notification.upload_image')"
                                    :recommendedSize="__('systemsetting::push-notification.image_size')"
                                    aspectRatio="wide"
                                />
                            </div>

                            {{-- Submit --}}
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.system-settings.push-notifications.index') }}" class="btn btn-light btn-default btn-squared">
                                    <i class="uil uil-arrow-left me-1"></i>
                                    {{ __('common.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary btn-squared" id="submitBtn">
                                    <i class="uil uil-message me-1"></i>
                                    {{ __('systemsetting::push-notification.send') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Preview Card --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="position: sticky; top: 20px;">
                    <div class="card-header bg-white border-bottom py-20">
                        <h5 class="mb-0 fw-500">
                            <i class="uil uil-mobile-android me-2"></i>
                            {{ __('systemsetting::push-notification.preview') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="notification-preview p-3 rounded">
                            <div class="d-flex align-items-start gap-3">
                                <div class="notification-icon bg-primary text-white rounded p-2">
                                    <i class="uil uil-bell" style="font-size: 24px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-600" id="previewTitle">{{ __('systemsetting::push-notification.notification_title') }}</h6>
                                    <p class="mb-0 text-muted small" id="previewDescription">{{ __('systemsetting::push-notification.notification_description') }}</p>
                                </div>
                            </div>
                            <div class="mt-3" id="previewImageContainer" style="display: none;">
                                <img id="previewImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 150px; width: 100%;">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="uil uil-info-circle me-1"></i>
                            {{ __('systemsetting::push-notification.preview_note') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle customer selection based on type
            $('input[name="type"]').on('change', function() {
                if ($(this).val() === 'specific') {
                    $('#customerSection').slideDown();
                } else {
                    $('#customerSection').slideUp();
                }
            });

            // Live preview - title
            $(document).on('input keyup', 'input[name*="[title]"]', function() {
                var lang = $(this).attr('data-lang') || $(this).closest('.form-group').attr('data-lang');
                if (lang === 'en') {
                    $('#previewTitle').text($(this).val() || '{{ __('systemsetting::push-notification.notification_title') }}');
                }
            });

            // Live preview - description (for CKEditor)
            // Wait for CKEditor instances to be ready
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.on('instanceReady', function(evt) {
                    var editor = evt.editor;
                    var element = editor.element.$;
                    var lang = $(element).attr('data-lang') || $(element).closest('.form-group').attr('data-lang');
                    
                    if (lang === 'en' && editor.name.includes('description')) {
                        editor.on('change', function() {
                            var text = editor.getData().replace(/<[^>]*>/g, '').trim();
                            $('#previewDescription').text(text || '{{ __('systemsetting::push-notification.notification_description') }}');
                        });
                        editor.on('key', function() {
                            setTimeout(function() {
                                var text = editor.getData().replace(/<[^>]*>/g, '').trim();
                                $('#previewDescription').text(text || '{{ __('systemsetting::push-notification.notification_description') }}');
                            }, 100);
                        });
                    }
                });
            }

            // Fallback for regular textarea (if CKEditor not used)
            $(document).on('input keyup', 'textarea[name*="[description]"]', function() {
                var lang = $(this).attr('data-lang') || $(this).closest('.form-group').attr('data-lang');
                if (lang === 'en') {
                    $('#previewDescription').text($(this).val() || '{{ __('systemsetting::push-notification.notification_description') }}');
                }
            });

            // Image preview
            $(document).on('change', '#notification_image, input[name="image"]', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#previewImage').attr('src', event.target.result);
                        $('#previewImageContainer').show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Form submission
            $('#notificationForm').on('submit', function(e) {
                e.preventDefault();

                // Sync CKEditor content to textareas before submit
                if (typeof CKEDITOR !== 'undefined') {
                    for (var instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                }

                const $btn = $('#submitBtn');
                $btn.prop('disabled', true);
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span>{{ __('common.processing') }}');

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.system-settings.push-notifications.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href = '{{ route('admin.system-settings.push-notifications.index') }}';
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                            $btn.prop('disabled', false);
                            $btn.html('<i class="uil uil-message me-1"></i>{{ __('systemsetting::push-notification.send') }}');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ __('common.error_occurred') }}';
                        if (xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul class="mb-0">';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            $('#alertContainer').html('<div class="alert alert-danger">' + errorHtml + '</div>');
                        } else {
                            toastr.error(xhr.responseJSON?.message || errorMessage);
                        }
                        $btn.prop('disabled', false);
                        $btn.html('<i class="uil uil-message me-1"></i>{{ __('systemsetting::push-notification.send') }}');
                    }
                });
            });
        });
    </script>
@endpush
