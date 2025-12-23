@extends('layout.app')

@section('title', trans('vendor::vendor.vendor_details'))
@section('styles')
    <style>
        /* Modern Glassmorphism Document Cards */
        .modern-document-card {
            position: relative;
            height: 100%;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .document-glass-bg {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: inset 0 0 30px rgba(255, 255, 255, 0.5);
        }

        .document-card-content {
            position: relative;
            z-index: 1;
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            text-align: center;
        }

        .document-icon-wrapper {
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.25) 0%, rgba(142, 146, 247, 0.15) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 2px solid rgba(95, 99, 242, 0.4);
            border-radius: 16px;
            margin-bottom: 16px;
            box-shadow: 0 8px 25px rgba(95, 99, 242, 0.2), inset 0 0 20px rgba(255, 255, 255, 0.3);
            font-size: 48px;
            color: #5f63f2;
            transition: all 0.4s ease;
        }

        .modern-document-card:hover .document-icon-wrapper {
            transform: scale(1.15) translateY(-4px);
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.35) 0%, rgba(142, 146, 247, 0.25) 100%);
            border-color: rgba(95, 99, 242, 0.6);
            box-shadow: 0 12px 35px rgba(95, 99, 242, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.4);
        }

        .document-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
            margin-bottom: 16px;
        }

        .document-name {
            font-weight: 700;
            margin-bottom: 6px;
            word-break: break-word;
            line-height: 1.4;
            font-size: 15px;
        }

        .document-type {
            font-size: 12px;
            color: #8e92f7;
            font-weight: 500;
            margin: 0;
        }

        .document-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            width: 100%;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            background: rgba(95, 99, 242, 0.1);
            color: #5f63f2;
        }

        .action-btn:hover {
            background: rgba(95, 99, 242, 0.2);
            transform: translateY(-2px);
        }

        .download-btn {
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.15) 0%, rgba(142, 146, 247, 0.1) 100%);
            color: #5f63f2;
        }

        .download-btn:hover {
            background: linear-gradient(135deg, rgba(95, 99, 242, 0.25) 0%, rgba(142, 146, 247, 0.2) 100%);
            box-shadow: 0 4px 12px rgba(95, 99, 242, 0.2);
        }

        .delete-btn {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .delete-btn:hover {
            background: rgba(220, 53, 69, 0.2);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
        }

        .modern-document-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(95, 99, 242, 0.2);
        }

        .modern-document-card:hover .document-glass-bg {
            background: rgba(255, 255, 255, 0.65);
            border-color: rgba(95, 99, 242, 0.5);
            box-shadow: inset 0 0 30px rgba(255, 255, 255, 0.4), 0 0 30px rgba(95, 99, 242, 0.15);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .document-card-content {
                padding: 20px 16px;
            }

            .document-icon-wrapper {
                width: 60px;
                height: 60px;
                font-size: 28px;
            }

            .document-name {
                color: #545454;
                font-size: 14px;
            }

            .action-btn {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }

        /* Transaction Card Animations */
        .transaction-card {
            animation: slideInUp 0.6s ease-out;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        }

        .transaction-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(95, 99, 242, 0.2) !important;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Staggered Animation for Multiple Cards */
        .transaction-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .transaction-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .transaction-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        /* Icon Pulse Animation on Hover */
        .transaction-card:hover i {
            animation: iconPulse 0.6s ease-out;
        }

        @keyframes iconPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .document-glass-bg {
                border-color: rgba(95, 99, 242, 0.2);
            }


            .document-type {
                color: #a8acff;
            }

        }
    </style>
@endsection
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
                    ['title' => trans('vendor::vendor.vendors_management'), 'url' => route('admin.vendors.index')],
                    ['title' => trans('vendor::vendor.vendor_details')],
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
                                            <i
                                                class="uil uil-dollar-alt me-1"></i>{{ trans('vendor::vendor.money_transactions') }}
                                        </h3>
                                    </div>
                                    <div class="card-body p-20">
                                        <div class="row">
                                            <div class="col-md-4">
                                                {{-- Total Vendors Balance --}}
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #667eea;">
                                                            {{ trans('vendor::vendor.total_vendors_balance') }}</p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            {{ number_format($vendor->total_balance, 2) }}
                                                            {{ currency() }}</p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(102, 126, 234, 0.2);">
                                                        <i class="uil uil-wallet fs-20" style="color: #667eea;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                {{-- Total Sent Money --}}
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(40, 199, 111, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #28c76f;">
                                                            {{ trans('vendor::vendor.total_sent_money') }}</p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            {{ number_format($vendor->total_sent, 2) }}
                                                            {{ currency() }}</p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(40, 199, 111, 0.15) 0%, rgba(40, 199, 111, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(40, 199, 111, 0.2);">
                                                        <i class="uil uil-arrow-up-right fs-20" style="color: #28c76f;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                {{-- Total Remaining --}}
                                                <div class="d-flex align-items-center justify-content-between p-15 rounded mb-15 transaction-card"
                                                    style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.95); box-shadow: 0 4px 15px rgba(79, 172, 254, 0.15), inset 0 0 20px rgba(255, 255, 255, 0.4);">
                                                    <div>
                                                        <p class="mb-0 fs-13 fw-bold" style="color: #4facfe;">
                                                            {{ trans('vendor::vendor.total_remaining') }}</p>
                                                        <p class="mb-0 fs-20 fw-bold mt-5" style="color: #272b41;">
                                                            {{ number_format($vendor->total_remaining, 2) }}
                                                            {{ currency() }}</p>
                                                    </div>
                                                    <div class="p-12 rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background: linear-gradient(135deg, rgba(79, 172, 254, 0.15) 0%, rgba(0, 242, 254, 0.1) 100%); width: 45px; height: 45px; border: 1px solid rgba(79, 172, 254, 0.2);">
                                                        <i class="uil uil-calculator-alt fs-20" style="color: #4facfe;"></i>
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
                                            {{-- Vendor Name --}}
                                            <x-translation-display :label="trans('vendor::vendor.name')" :model="$vendor" fieldName="name"
                                                :languages="$languages" />

                                            {{-- Vendor Description --}}
                                            <x-translation-display :label="trans('vendor::vendor.description')" :model="$vendor"
                                                fieldName="description" :languages="$languages" />
                                            {{-- Country --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.country') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $vendor->country->name ?? '--' }}
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Vendor Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.vendor_type') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if ($vendor->type == 'product')
                                                            <span
                                                                class="badge badge-primary badge-round badge-lg">{{ trans('vendor::vendor.product') }}</span>
                                                        @elseif($vendor->type == 'booking')
                                                            <span
                                                                class="badge badge-info badge-round badge-lg">{{ trans('vendor::vendor.booking') }}</span>
                                                        @elseif($vendor->type == 'product_booking')
                                                            <span
                                                                class="badge badge-warning badge-round badge-lg">{{ trans('vendor::vendor.product_booking') }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Departments --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.departments') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if ($vendor->departments && $vendor->departments->count() > 0)
                                                            @foreach ($vendor->departments as $department)
                                                                <span
                                                                    class="badge badge-primary badge-round badge-lg">{{ $department->name }}</span>
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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.email') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $vendor->user ? $vendor->user->email : '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Phone --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ __('common.phone') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        {{ $vendor->phone ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Activation Status --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('vendor::vendor.activation') }}</label>
                                                    <p class="fs-15">
                                                        @if ($vendor->active)
                                                            <span
                                                                class="badge badge-success badge-round badge-lg">{{ trans('vendor::vendor.active') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-danger badge-round badge-lg">{{ trans('vendor::vendor.inactive') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            {{-- Slug --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('main.slug') }}</label>
                                                    <p class="fs-15">
                                                        <span
                                                            class="badge badge-success badge-round badge-lg">{{ $vendor->slug }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Documents Section --}}
                                @if ($vendor->documents && $vendor->documents->count() > 0)
                                    <div class="card card-holder mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i
                                                    class="uil uil-file me-1"></i>{{ trans('vendor::vendor.vendor_documents') }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($vendor->documents as $document)
                                                    <div class="col-md-4 mb-4">
                                                        <div class="modern-document-card">
                                                            <div class="document-glass-bg"></div>
                                                            <div class="document-card-content">
                                                                <div class="document-icon-wrapper">
                                                                    <i class="uil uil-file"></i>
                                                                </div>
                                                                <div class="document-info">
                                                                    <h6 class="document-name">
                                                                        {{ $document->getTranslation('name', app()->getLocale()) ?? trans('vendor::vendor.document') }}
                                                                    </h6>
                                                                    <p class="document-type">
                                                                        {{ trans('vendor::vendor.document') }}
                                                                    </p>
                                                                </div>
                                                                <div class="document-actions">
                                                                    <a href="{{ asset('storage/' . $document->path) }}"
                                                                        target="_blank" class="action-btn"
                                                                        title="{{ trans('common.show') }}">
                                                                        <i class="uil uil-eye"></i>
                                                                    </a>
                                                                    <button type="button"
                                                                        class="action-btn delete-btn delete-document-btn"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#modal-delete-document"
                                                                        data-document-id="{{ $document->id }}"
                                                                        data-document-name="{{ $document->getTranslation('name', app()->getLocale()) ?? trans('vendor::vendor.document') }}"
                                                                        title="{{ trans('common.delete') }}">
                                                                        <i class="uil uil-trash-alt"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- SEO Information Section --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i
                                                class="uil uil-search me-1"></i>{{ trans('vendor::vendor.seo_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- SEO Titles --}}
                                            <x-translation-display :label="trans('vendor::vendor.meta_title')" :model="$vendor"
                                                fieldName="meta_title" :languages="$languages" />

                                            {{-- SEO Descriptions --}}
                                            <x-translation-display :label="trans('vendor::vendor.meta_description')" :model="$vendor"
                                                fieldName="meta_description" :languages="$languages" />

                                            {{-- SEO Keywords --}}
                                            <x-translation-display :label="trans('vendor::vendor.meta_keywords')" :model="$vendor"
                                                fieldName="meta_keywords" :languages="$languages" type="keywords" />
                                        </div>
                                    </div>
                                </div>


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
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $vendor->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label
                                                        class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
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
                                        @if ($vendor->logo && $vendor->logo->path)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $vendor->logo->path) }}"
                                                    alt="{{ $vendor->getTranslation('name', app()->getLocale()) }}"
                                                    class="vendor-image img-fluid">
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic">
                                                {{ trans('vendor::vendor.no_logo_uploaded') }}</p>
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
                                        @if ($vendor->banner && $vendor->banner->path)
                                            <div class="image-wrapper">
                                                <img src="{{ asset('storage/' . $vendor->banner->path) }}"
                                                    alt="{{ $vendor->getTranslation('name', app()->getLocale()) }}"
                                                    class="vendor-image img-fluid">
                                            </div>
                                        @else
                                            <p class="fs-15 color-light fst-italic">
                                                {{ trans('vendor::vendor.no_banner_uploaded') }}</p>
                                        @endif
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{-- Order Statistics Section --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-chart-bar me-1"></i>{{ trans('vendor::vendor.order_statistics') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Total Order Products --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #5b69ff15 0%, #5b69ff05 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-shopping-cart fs-1" style="color: #5b69ff;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #5b69ff;">{{ $orderStats['total_order_products'] ?? 0 }}</h3>
                                                        <p class="mb-0 text-muted small">{{ trans('vendor::vendor.total_order_products') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Total Revenue --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #20c99715 0%, #20c99705 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-money-bill fs-1" style="color: #20c997;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #20c997;">{{ number_format($orderStats['total_revenue'] ?? 0, 2) }} {{ currency() }}</h3>
                                                        <p class="mb-0 text-muted small">{{ trans('vendor::vendor.total_revenue') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Total Quantity Sold --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #17a2b815 0%, #17a2b805 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-package fs-1" style="color: #17a2b8;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #17a2b8;">{{ $orderStats['total_quantity_sold'] ?? 0 }}</h3>
                                                        <p class="mb-0 text-muted small">{{ trans('vendor::vendor.total_quantity_sold') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Total Products --}}
                                            <div class="col-md-3 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6f42c115 0%, #6f42c105 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-box fs-1" style="color: #6f42c1;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #6f42c1;">{{ $orderStats['total_products'] ?? 0 }}</h3>
                                                        <p class="mb-0 text-muted small">{{ trans('vendor::vendor.total_products') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Delivered Order Products --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a74515 0%, #28a74505 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-check-circle fs-1" style="color: #28a745;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #28a745;">{{ $orderStats['delivered_order_products'] ?? 0 }}</h3>
                                                        <p class="mb-0 text-muted small">{{ trans('vendor::vendor.delivered_orders') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Pending Order Products --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc10715 0%, #ffc10705 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-clock fs-1" style="color: #ffc107;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #ffc107;">{{ $orderStats['pending_order_products'] ?? 0 }}</h3>
                                                        <p class="mb-0 text-muted small">{{ trans('vendor::vendor.pending_orders') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Cancelled Order Products --}}
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #dc354515 0%, #dc354505 100%);">
                                                    <div class="card-body text-center py-4">
                                                        <div class="mb-2">
                                                            <i class="uil uil-times-circle fs-1" style="color: #dc3545;"></i>
                                                        </div>
                                                        <h3 class="mb-1 fw-bold" style="color: #dc3545;">{{ $orderStats['cancelled_order_products'] ?? 0 }}</h3>
                                                        <p class="mb-0 text-muted small">{{ trans('vendor::vendor.cancelled_orders') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Order Products Table Section --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-shopping-bag me-1"></i>{{ trans('vendor::vendor.vendor_order_products') }}
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="userDatatable global-shadow border-light-0 bg-white w-100">
                                            <div class="table-responsive">
                                                <table class="table mb-0 table-bordered table-hover">
                                                    <thead>
                                                        <tr class="userDatatable-header">
                                                            <th><span class="userDatatable-title">#</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.order_number') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.product_image') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.product_name') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.sku') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.variant') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.quantity') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.unit_price') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.total_price') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('vendor::vendor.order_status') }}</span></th>
                                                            <th><span class="userDatatable-title">{{ trans('common.actions') }}</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($orderProducts ?? [] as $index => $orderProduct)
                                                            <tr>
                                                                <td><div class="userDatatable-content">{{ ($orderProducts->currentPage() - 1) * $orderProducts->perPage() + $index + 1 }}</div></td>
                                                                <td><div class="userDatatable-content fw-medium">#{{ $orderProduct->order->order_number ?? $orderProduct->order_id }}</div></td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        @if($orderProduct->vendorProduct && $orderProduct->vendorProduct->product && $orderProduct->vendorProduct->product->mainImage)
                                                                            <img src="{{ asset('storage/' . $orderProduct->vendorProduct->product->mainImage->path) }}" 
                                                                                alt="{{ $orderProduct->vendorProduct->product->getTranslation('name', app()->getLocale()) }}" 
                                                                                class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                                        @else
                                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                                                <i class="uil uil-image text-muted"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        @if($orderProduct->vendorProduct && $orderProduct->vendorProduct->product)
                                                                            {{ $orderProduct->vendorProduct->product->getTranslation('name', app()->getLocale()) }}
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        @if($orderProduct->vendorProductVariant)
                                                                            <code>{{ $orderProduct->vendorProductVariant->sku ?? '-' }}</code>
                                                                        @elseif($orderProduct->vendorProduct)
                                                                            <code>{{ $orderProduct->vendorProduct->sku ?? '-' }}</code>
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        @if($orderProduct->vendorProductVariant && $orderProduct->vendorProductVariant->variantConfiguration)
                                                                            <span class="badge badge-primary badge-round badge-lg">{{ $orderProduct->vendorProductVariant->variant_name }}</span>
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td><div class="userDatatable-content fw-medium">{{ $orderProduct->quantity }}</div></td>
                                                                <td><div class="userDatatable-content">{{ number_format($orderProduct->price ?? 0, 2) }} {{ currency() }}</div></td>
                                                                <td><div class="userDatatable-content fw-bold text-success">{{ number_format(($orderProduct->price ?? 0) * ($orderProduct->quantity ?? 1), 2) }} {{ currency() }}</div></td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        @if($orderProduct->order && $orderProduct->order->stage)
                                                                            <span class="badge badge-round badge-lg" style="background-color: {{ $orderProduct->order->stage->color ?? '#6c757d' }}">
                                                                                {{ $orderProduct->order->stage->getTranslation('name', app()->getLocale()) }}
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-secondary">-</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="userDatatable-content">
                                                                        <a href="{{ route('admin.orders.show', $orderProduct->order_id) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                            <i class="uil uil-eye m-0"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="11" class="text-center text-muted py-4">{{ trans('vendor::vendor.no_order_products_found') }}</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                                <div class="text-muted small">
                                                    {{ trans('common.showing') }} {{ $orderProducts->firstItem() ?? 0 }} - {{ $orderProducts->lastItem() ?? 0 }} {{ trans('common.of') }} {{ $orderProducts->total() }} {{ trans('common.items') }}
                                                </div>
                                                @if($orderProducts && $orderProducts->hasPages())
                                                    {{ $orderProducts->links('vendor.pagination.custom') }}
                                                @endif
                                            </div>
                                        </div>
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
        <x-delete-modal modalId="modal-delete-document" :title="__('common.confirm_delete')" :message="__('common.delete_confirmation')"
            itemNameId="delete-document-name" confirmBtnId="confirmDeleteDocumentBtn" :deleteRoute="'#'"
            :cancelText="__('common.cancel')" :deleteText="__('common.delete')" />
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
                    confirmBtn.html(
                    '<i class="uil uil-spinner-alt spin me-1"></i>{{ __('common.deleting') }}');

                    // Send AJAX request
                    $.ajax({
                        url: `{{ route('admin.vendors.documents.destroy', ['vendor' => $vendor->id, 'document' => '__DOCUMENT_ID__']) }}`
                            .replace('__DOCUMENT_ID__', documentId),
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
                                // Display success message
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(response.message);
                                } else {
                                    alert(response.message);
                                }

                                // Reload the page after a short delay
                                setTimeout(function() {
                                    location.reload();
                                }, 1000); // 1-second delay
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
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            .vendor-image {
                max-width: 100%;
                height: auto;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
