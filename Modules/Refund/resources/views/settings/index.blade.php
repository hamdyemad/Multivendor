@extends('layout.app')

@section('title', trans('menu.refunds.settings'))

@section('content')
<div class="container-fluid mb-3">
    {{-- Breadcrumb Component --}}
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => trans('menu.refunds.title'), 'url' => route('admin.refunds.index')],
                ['title' => trans('menu.refunds.settings')]
            ]" />
        </div>
    </div>

    {{-- Form Card Handler Component --}}
    <x-form-card-handler
        formId="refundSettingsForm"
        :formAction="route('admin.refunds.settings.update')"
        formMethod="PUT"
        :title="trans('refund::refund.titles.refund_settings')"
        icon="uil uil-setting"
        :backUrl="route('admin.refunds.index')"
        :successMessage="trans('refund::refund.messages.settings_updated')"
        :showSuccessAlert="true">
        
        {{-- Default Refund Days - Using Form Input Component --}}
        <div class="col-md-6">
            <x-form-input-field
                type="number"
                name="refund_processing_days"
                :label="trans('refund::refund.fields.refund_processing_days')"
                :value="$settings->refund_processing_days ?? 7"
                placeholder="7"
                :min="1"
                :max="365"
                :required="true"
                :helpText="trans('refund::refund.help.refund_processing_days')"
            />
        </div>

        {{-- Customer Pays Return Shipping - Using Form Switcher Component --}}
        <div class="col-md-6">
            <x-form-switcher
                name="customer_pays_return_shipping"
                :label="trans('refund::refund.fields.customer_pays_return_shipping')"
                :checked="$settings->customer_pays_return_shipping ?? 0"
                switchColor="primary"
                :helpText="trans('refund::refund.help.customer_pays_return_shipping')"
            />
        </div>
    </x-form-card-handler>
</div>
@endsection
