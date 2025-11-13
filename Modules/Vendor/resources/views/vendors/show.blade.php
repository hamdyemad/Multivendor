@extends('layout.app')

@section('title', trans('vendor::vendor.vendor_details'))
@section('styles')
<style>
    .view-item.docs-box {
        border: 3px dashed var(--color-primary);
        padding: 10px;
        border-radius: 5px;
    }
</style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('vendor::vendor.vendors_management'), 'url' => route('admin.vendors.index')],
                    ['title' => trans('vendor::vendor.vendor_details')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('vendor::vendor.vendor_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.vendors.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 order-2 order-md-1">
                                {{-- Money Transactions --}}
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-dollar-alt me-1"></i>{{ trans('vendor::vendor.money_transactions') }}
                                        </h3>
                                    </div>
                                    <div class="card-body p-20">
                                        <div class="row">
                                            <div class="col-md-6">
                                                {{-- Total Vendors Balance --}}
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: rgba(255,255,255,0.9);">{{ trans('vendor::vendor.total_vendors_balance') }}</p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: white;">{{ number_format($vendor->total_balance ?? 0, 2) }} {{ trans('common.egp') }}</p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(255,255,255,0.2); width: 45px; height: 45px;">
                                                        <i class="uil uil-wallet fs-20" style="color: white;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                {{-- Total Sent Money --}}
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: rgba(255,255,255,0.9);">{{ trans('vendor::vendor.total_sent_money') }}</p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: white;">{{ number_format($vendor->total_sent ?? 0, 2) }} {{ trans('common.egp') }}</p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(255,255,255,0.2); width: 45px; height: 45px;">
                                                        <i class="uil uil-arrow-up-right fs-20" style="color: white;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                {{-- Total Remaining --}}
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: rgba(255,255,255,0.9);">{{ trans('vendor::vendor.total_remaining') }}</p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: white;">{{ number_format($vendor->total_remaining ?? 0, 2) }} {{ trans('common.egp') }}</p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(255,255,255,0.2); width: 45px; height: 45px;">
                                                        <i class="uil uil-calculator-alt fs-20" style="color: white;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                {{-- Commission --}}
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); box-shadow: 0 4px 15px rgba(250, 112, 154, 0.3);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: rgba(255,255,255,0.9);">{{ trans('vendor::vendor.commission') }}</p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: white;">{{ $vendor->commission ? $vendor->commission->commission . '%' : '0%' }}</p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(255,255,255,0.2); width: 45px; height: 45px;">
                                                        <i class="uil uil-percentage fs-20" style="color: white;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Dynamic Language Translations for Name --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                الاسم بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ trans('vendor::vendor.name') }} ({{ $language->name }})
                                                            @else
                                                                {{ trans('vendor::vendor.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $vendor->getTranslation('name', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            {{-- Dynamic Language Translations for Description --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                الوصف بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ trans('vendor::vendor.description') }} ({{ $language->name }})
                                                            @else
                                                                {{ trans('vendor::vendor.description') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $vendor->getTranslation('description', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            {{-- Country --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.country') }}</label>
                                                </div>
                                            </div>
                                            {{-- Vendor Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.vendor_type') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($vendor->type == 'product')
                                                            <span class="badge badge-primary badge-round badge-lg">{{ trans('vendor::vendor.product') }}</span>
                                                        @elseif($vendor->type == 'booking')
                                                            <span class="badge badge-info badge-round badge-lg">{{ trans('vendor::vendor.booking') }}</span>
                                                        @elseif($vendor->type == 'product_booking')
                                                            <span class="badge badge-warning badge-round badge-lg">{{ trans('vendor::vendor.product_booking') }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Vendor Activities --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.activities') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($vendor->activities && $vendor->activities->count() > 0)
                                                            @foreach ($vendor->activities as $activity)
                                                                <span class="badge badge-primary badge-round badge-lg">{{ $activity->getTranslation('name', app()->getLocale()) }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Email --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.email') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $vendor->user ? $vendor->user->email : '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Activation Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if($vendor->active)
                                                            <span class="badge badge-success badge-round badge-lg">{{ trans('vendor::vendor.active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger badge-round badge-lg">{{ trans('vendor::vendor.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SEO Information Section --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-search me-1"></i>{{ trans('vendor::vendor.seo_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Meta Title for each language --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                عنوان SEO بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ trans('vendor::vendor.meta_title') }} ({{ $language->name }})
                                                            @else
                                                                {{ trans('vendor::vendor.meta_title') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $vendor->getTranslation('meta_title', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            {{-- Meta Description for each language --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                وصف SEO بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ trans('vendor::vendor.meta_description') }} ({{ $language->name }})
                                                            @else
                                                                {{ trans('vendor::vendor.meta_description') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $vendor->getTranslation('meta_description', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            {{-- Meta Keywords for each language --}}
                                            @foreach($languages as $language)
                                                <div class="col-md-6">
                                                    <div class="view-item">
                                                        <label class="il-gray fs-14 fw-500 mb-10" @if($language->rtl) dir="rtl" style="text-align: right; display: block;" @endif>
                                                            @if($language->code == 'ar')
                                                                كلمات SEO المفتاحية بالعربية
                                                            @elseif($language->code == 'en')
                                                                {{ trans('vendor::vendor.meta_keywords') }} ({{ $language->name }})
                                                            @else
                                                                {{ trans('vendor::vendor.meta_keywords') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            @php
                                                                $keywords = $vendor->getMetaKeywordsArray($language->code);
                                                            @endphp
                                                            @if(!empty($keywords))
                                                                @foreach($keywords as $keyword)
                                                                    <span class="badge badge-primary badge-round me-1 mb-1">{{ $keyword }}</span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Documents Section --}}
                                @if($vendor->documents && $vendor->documents->count() > 0)
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-file-alt me-1"></i>{{ trans('vendor::vendor.vendor_documents') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($vendor->documents as $document)
                                                <div class="col-md-6 mb-3">
                                                    <div class="view-item docs-box">
                                                        <label class="il-gray fs-14 fw-500 mb-10">
                                                            {{ $document->getTranslation('name', app()->getLocale()) ?? trans('vendor::vendor.document') }}
                                                        </label>
                                                        <p class="fs-15 color-dark">
                                                            <div class="d-flex gap-2">
                                                                <a href="{{ asset('storage/' . $document->path) }}" target="_blank" class="btn btn-sm btn-light">
                                                                    <i class="uil uil-file-download me-1"></i>{{ trans('common.download') ?? 'Download' }}
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-danger delete-document-btn"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#modal-delete-document"
                                                                        data-document-id="{{ $document->id }}"
                                                                        data-document-name="{{ $document->getTranslation('name', app()->getLocale()) ?? trans('vendor::vendor.document') }}">
                                                                    <i class="uil uil-trash-alt me-1"></i>{{ trans('common.delete') ?? 'Delete' }}
                                                                </button>
                                                            </div>
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ trans('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $vendor->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $vendor->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{-- Vendor Branding (Logo & Banner) --}}
                            <div class="col-md-4 order-1 order-md-2">
                                {{-- Logo --}}
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image me-1"></i>{{ trans('vendor::vendor.logo') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($vendor->logo && $vendor->logo->path)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $vendor->logo->path) }}"
                                                alt="{{ $vendor->getTranslation('name', app()->getLocale()) }}"
                                                class="vendor-image img-fluid">
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic">{{ trans('vendor::vendor.no_logo_uploaded') }}</p>
                                        @endif
                                    </div>
                                </div>
                                {{-- Banner --}}
                                <div class="card card-holder mb-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-image-v me-1"></i>{{ trans('vendor::vendor.banner') }}
                                        </h3>
                                    </div>
                                    <div class="card-body text-center">
                                        @if($vendor->banner && $vendor->banner->path)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $vendor->banner->path) }}"
                                                alt="{{ $vendor->getTranslation('name', app()->getLocale()) }}"
                                                class="vendor-image img-fluid">
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic">{{ trans('vendor::vendor.no_banner_uploaded') }}</p>
                                        @endif
                                    </div>
                                </div>


                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Modal Component --}}
    <x-image-modal />

    {{-- Delete Document Confirmation Modal --}}
    <x-delete-modal modalId="modal-delete-document" 
                    :title="__('common.confirm_delete')" 
                    :message="__('common.delete_confirmation')" 
                    itemNameId="delete-document-name"
                    confirmBtnId="confirmDeleteDocumentBtn" 
                    :deleteRoute="'#'" 
                    :cancelText="__('common.cancel')" 
                    :deleteText="__('common.delete')" />
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
    // Set delete modal data when delete button is clicked
    $(document).on('click', '.delete-document-btn', function() {
        const documentId = $(this).data('document-id');
        const documentName = $(this).data('document-name');
        
        // Set the document name in the modal
        $('#delete-document-name').text(documentName);
        
        // Store document ID and name in the confirm button
        $('#confirmDeleteDocumentBtn').data('document-id', documentId);
        $('#confirmDeleteDocumentBtn').data('document-name', documentName);
    });

    // Handle confirm delete button click
    $('#confirmDeleteDocumentBtn').on('click', function() {
        const documentId = $(this).data('document-id');
        const documentName = $(this).data('document-name');
        const confirmBtn = $(this);
        
        // Debug logging
        console.log('Deleting document:', documentId, documentName);
        
        // Disable button and show loading
        confirmBtn.prop('disabled', true);
        confirmBtn.html('<i class="uil uil-spinner-alt spin me-1"></i>{{ __('common.deleting') }}');
        
        // Send AJAX request
        $.ajax({
            url: `{{ route('admin.vendors.documents.destroy', ['vendor' => $vendor->id, 'document' => '__DOCUMENT_ID__']) }}`.replace('__DOCUMENT_ID__', documentId),
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Delete response:', response);
                
                // Hide the modal
                $('#modal-delete-document').modal('hide');
                
                if (response.success) {
                    console.log('Document deleted successfully, removing from DOM');
                    // Find and remove the document row with animation
                    $(`.delete-document-btn[data-document-id="${documentId}"]`).closest('.col-md-6').fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if no documents left
                        if ($('.delete-document-btn').length === 0) {
                            console.log('No documents left, reloading page');
                            location.reload(); // Reload to hide the documents section
                        }
                    });
                } else {
                    console.error('Delete failed:', response);
                }
                
                // Reset button
                confirmBtn.prop('disabled', false);
                confirmBtn.html('{{ __('common.delete') }}');
            },
            error: function(xhr) {
                console.error('AJAX Error deleting document:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                // Hide the modal
                $('#modal-delete-document').modal('hide');
                
                // Reset button
                confirmBtn.prop('disabled', false);
                confirmBtn.html('{{ __('common.delete') }}');
            }
        });
    });
});
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.vendor-image {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.image-wrapper {
    cursor: pointer;
    transition: transform 0.2s ease;
}

.image-wrapper:hover {
    transform: scale(1.02);
}
</style>
@endpush

