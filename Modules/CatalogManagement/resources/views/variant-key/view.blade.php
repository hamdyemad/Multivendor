@extends('layout.app')

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
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('catalogmanagement::variantkey.variant_key_details') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.variant-keys.index') }}" class="btn btn-light btn-default btn-squared text-capitalize">
                                <i class="uil uil-arrow-left"></i> {{ trans('common.back') }}
                            </a>
                            <a href="{{ route('admin.variant-keys.edit', $variantKey->id) }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-edit"></i> {{ trans('common.edit') }}
                            </a>
                        </div>
                    </div>
                    <!-- Translations Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-500">{{ trans('common.translations') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($languages as $language)
                                    @php
                                        $name = $variantKey->translations->where('lang_id', $language->id)
                                            ->where('lang_key', 'name')
                                            ->first();
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="form-group mb-20">
                                            <label class="il-gray fs-14 fw-500 mb-10 w-100"  @if($language->rtl) dir="rtl" @endif>
                                                @if($language->code == 'ar')
                                                    الاسم ({{ $language->name }})
                                                @else
                                                    {{ trans('catalogmanagement::variantkey.name') }} ({{ $language->name }})
                                                @endif
                                            </label>
                                            <div class="userDatatable-content w-100" @if($language->rtl) dir="rtl" @endif>
                                                <strong>{{ $name ? $name->lang_value : '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- General Information Card -->
                    <div class="card border-0 shadow-sm mb-25">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantkey.parent_key') }}</label>
                                        <div class="userDatatable-content">
                                            <strong>
                                                @if($variantKey->parent)
                                                    {{ $variantKey->parent->getTranslation('name', app()->getLocale()) }}
                                                @else
                                                    {{ trans('catalogmanagement::variantkey.no_parent') }}
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

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
                                            <strong>{{ $variantKey->created_at->format('Y-m-d H:i:s') }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-20">
                                        <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                        <div class="userDatatable-content">
                                            <strong>{{ $variantKey->updated_at->format('Y-m-d H:i:s') }}</strong>
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
