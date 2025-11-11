@extends('layout.app')
@section('title', trans('catalogmanagement::variantsconfig.view_variants_config'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::variantsconfig.variants_configurations'), 'url' => route('admin.variants-configurations.index')],
                    ['title' => trans('catalogmanagement::variantsconfig.view_variants_config')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::variantsconfig.variants_config_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.variants-configurations.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            <a href="{{ route('admin.variants-configurations.edit', $variantsConfig->id) }}" class="btn btn-primary btn-sm">
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
                                                                {{ trans('catalogmanagement::variantsconfig.name') }}
                                                            @else
                                                                {{ trans('catalogmanagement::variantsconfig.name') }} ({{ $language->name }})
                                                            @endif
                                                        </label>
                                                        <p class="fs-15 color-dark fw-500" @if($language->rtl) dir="rtl" style="text-align: right;" @endif>
                                                            {{ $variantsConfig->getTranslation('name', $language->code) ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            {{-- Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.type') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantsConfig->type)
                                                            <span class="badge badge-{{ $variantsConfig->type == 'color' ? 'info' : 'secondary' }} badge-round badge-lg">
                                                                <i class="uil uil-{{ $variantsConfig->type == 'color' ? 'palette' : 'text' }}"></i>
                                                                {{ trans('catalogmanagement::variantsconfig.' . $variantsConfig->type) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Value --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.value') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantsConfig->value)
                                                            @if($variantsConfig->type == 'color')
                                                                <span class="d-inline-flex align-items-center gap-2">
                                                                    <span class="color-preview" style="background-color: {{ $variantsConfig->value }}; width: 30px; height: 30px; border-radius: 6px; border: 2px solid #dee2e6; display: inline-block;"></span>
                                                                    <strong>{{ $variantsConfig->value }}</strong>
                                                                </span>
                                                            @else
                                                                <strong>{{ $variantsConfig->value }}</strong>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Key --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.key') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantsConfig->key)
                                                            <span class="badge badge-primary badge-round badge-lg">
                                                                <i class="uil uil-key-skeleton-alt"></i>
                                                                {{ $variantsConfig->key->getTranslation('name', app()->getLocale()) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Parent --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.parent') ?? 'Parent' }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantsConfig->parent_data)
                                                            <a href="{{ route('admin.variants-configurations.show', $variantsConfig->parent_data->id) }}" class="badge badge-primary badge-round badge-lg">
                                                                {{ $variantsConfig->parent_data->getTranslation('name', app()->getLocale()) }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">{{ trans('catalogmanagement::variantsconfig.no_parent') ?? 'No Parent' }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Children Count --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.children_count') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">{{ $variantsConfig->children->count() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Children Section --}}
                                @if($variantsConfig->children && $variantsConfig->children->count() > 0)
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i class="uil uil-sitemap me-1"></i>{{ trans('catalogmanagement::variantsconfig.children') ?? 'Children' }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="view-item">
                                                        <p class="fs-15 color-dark">
                                                            @foreach ($variantsConfig->children as $child)
                                                                <a href="{{ route('admin.variants-configurations.show', $child->id) }}" class="badge badge-primary badge-round badge-lg me-2 mb-2">
                                                                    {{ $child->getTranslation('name', app()->getLocale()) }}
                                                                    @if($child->value)
                                                                        ({{ $child->value }})
                                                                    @endif
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
                                                    <p class="fs-15 color-dark">{{ $variantsConfig->created_at->format('d M, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $variantsConfig->updated_at->format('d M, Y h:i A') }}</p>
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
