@extends('layout.app')
@section('title', trans('catalogmanagement::variantkey.view_variant_key'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::variantkey.variant_configuration_keys'), 'url' => route('admin.variant-keys.index')],
                    ['title' => trans('catalogmanagement::variantkey.view_variant_key')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::variantkey.variant_key_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.variant-keys.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.variant-keys.edit', $variantKey->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
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
                                                                {{ trans('catalogmanagement::variantkey.name') }}
                                                            @else
                                                                {{ trans('catalogmanagement::variantkey.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $variantKey->getTranslation('name', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            {{-- Parent Key --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantkey.parent_key') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantKey->parent)
                                                            <span class="badge badge-primary badge-round badge-lg">{{ $variantKey->parent->getTranslation('name', app()->getLocale()) }}</span>
                                                        @else
                                                            <span class="text-muted">{{ trans('catalogmanagement::variantkey.no_parent') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Children Count --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantkey.children_count') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">{{ $variantKey->childrenKeys->count() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Children Keys Section --}}
                                @if($variantKey->childrenKeys && $variantKey->childrenKeys->count() > 0)
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i class="uil uil-sitemap me-1"></i>{{ trans('catalogmanagement::variantkey.children_keys') ?? 'Children Keys' }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="view-item">
                                                        <p class="fs-15 color-dark">
                                                            @foreach ($variantKey->childrenKeys as $child)
                                                                <a href="{{ route('admin.variant-keys.show', $child->id) }}" class="badge badge-primary badge-round badge-lg me-2 mb-2">
                                                                    {{ $child->getTranslation('name', app()->getLocale()) }}
                                                                </a>
                                                            @endforeach
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="card mt-3">
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
                                                    <p class="fs-15 color-dark">{{ $variantKey->created_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $variantKey->updated_at->format('d M, Y h:i A') }}</p>
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
        </div>
    </div>
@endsection
