@extends('layout.app')

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
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('catalogmanagement::variantsconfig.variants_config_details') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.variants-configurations.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-arrow-left"></i> {{ trans('common.back') }}
                            </a>
                            <a href="{{ route('admin.variants-configurations.edit', $variantsConfig->id) }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-edit"></i> {{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>

                    <!-- Basic Information Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ trans('common.basic_information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Variant Names --}}
                                @foreach($languages as $language)
                                    <div class="col-md-6">
                                        <div class="form-group mb-20">
                                            <label class="il-gray fs-14 fw-500 mb-10 w-100" @if($language->code == 'ar')  dir="rtl" @endif>
                                                @if($language->code == 'ar')
                                                    الاسم ({{ $language->name }})
                                                @else
                                                    {{ trans('catalogmanagement::variantsconfig.name') }} ({{ $language->name }})
                                                @endif
                                            </label>
                                            <div class="userDatatable-content" @if($language->code == 'ar')  dir="rtl" @endif>
                                                <strong class="fs-16">{{ $variantsConfig->getTranslation('name', $language->code) ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Type --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.type') }}</label>
                                        <div class="userDatatable-content">
                                            @if($variantsConfig->type)
                                                <span class="badge badge-{{ $variantsConfig->type == 'color' ? 'info' : 'secondary' }} badge-pill px-3 py-2">
                                                    <i class="uil uil-{{ $variantsConfig->type == 'color' ? 'palette' : 'text' }}"></i>
                                                    {{ trans('catalogmanagement::variantsconfig.' . $variantsConfig->type) }}
                                                </span>
                                            @else
                                                <strong>-</strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Value --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.value') }}</label>
                                        <div class="userDatatable-content">
                                            @if($variantsConfig->value)
                                                @if($variantsConfig->type == 'color')
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="color-preview-large" style="background-color: {{ $variantsConfig->value }}; width: 40px; height: 40px; border-radius: 6px; border: 2px solid #dee2e6; display: inline-block;"></span>
                                                        <strong class="fs-16">{{ $variantsConfig->value }}</strong>
                                                    </div>
                                                @else
                                                    <strong class="fs-16">{{ $variantsConfig->value }}</strong>
                                                @endif
                                            @else
                                                <strong>-</strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.key') }}</label>
                                        <div class="userDatatable-content">
                                            @if($variantsConfig->key)
                                                <span class="badge badge-primary badge-lg badge-round badge-pill px-3 py-2">
                                                    <i class="uil uil-key-skeleton-alt"></i> 
                                                    {{ $variantsConfig->key->getTranslation('name', app()->getLocale()) }}
                                                </span>
                                            @else
                                                <strong>-</strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Children Information Card -->
                    @if($variantsConfig->children && $variantsConfig->children->count() > 0)
                        <div class="card border-0 shadow-sm mb-25">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-500">
                                    {{ trans('catalogmanagement::variantsconfig.children_count') }}
                                    <span class="badge badge-primary ms-2">{{ $variantsConfig->children->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    @foreach($variantsConfig->children as $child)
                                        <a href="{{ route('admin.variants-configurations.show', $child->id) }}" 
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $child->value }}</strong>
                                                @if($child->key)
                                                    @php
                                                        $childKeyTranslation = $child->key->translations->where('lang_key', 'name')->first();
                                                    @endphp
                                                    <small class="text-muted ms-2">
                                                        <i class="uil uil-key-skeleton-alt"></i> {{ $childKeyTranslation ? $childKeyTranslation->lang_value : '-' }}
                                                    </small>
                                                @endif
                                            </div>
                                            <i class="uil uil-angle-right-b"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Metadata Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ trans('common.timestamps') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                        <div class="userDatatable-content">
                                            <strong>{{ $variantsConfig->created_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                        <div class="userDatatable-content">
                                            <strong>{{ $variantsConfig->updated_at->format('Y-m-d H:i:s') }}</strong>
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
