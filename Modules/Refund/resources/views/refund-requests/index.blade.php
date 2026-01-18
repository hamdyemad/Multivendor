@extends('layout.app')

@section('title', trans('menu.refunds.title'))

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb Component --}}
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                [
                    'title' => trans('dashboard.title'),
                    'url' => route('admin.dashboard'),
                    'icon' => 'uil uil-estate',
                ],
                ['title' => trans('menu.refunds.title')],
            ]" />
        </div>
    </div>

    @php
    // Build table headers
    $headers = [
        ['label' => '#', 'class' => 'text-center'],
        ['label' => trans('refund::refund.fields.refund_number')],
        ['label' => trans('refund::refund.fields.order_number')],
        ['label' => trans('refund::refund.fields.customer')],
    ];

    if (isAdmin()) {
        $headers[] = ['label' => trans('refund::refund.fields.vendor')];
    }

    $headers = array_merge($headers, [
        ['label' => trans('refund::refund.fields.total_refund_amount')],
        ['label' => trans('refund::refund.fields.status')],
        ['label' => trans('common.created_at')],
        ['label' => trans('common.actions')],
    ]);

    // Build columns array
    $columns = [
        ['data' => 'index', 'name' => 'index', 'orderable' => false, 'searchable' => false, 'className' => 'text-center fw-bold'],
        ['data' => 'refund_number', 'name' => 'refund_number', 'orderable' => false, 'searchable' => true],
        ['data' => 'order_number', 'name' => 'order_number', 'orderable' => false, 'searchable' => false],
        ['data' => 'customer_name', 'name' => 'customer_name', 'orderable' => false, 'searchable' => false],
    ];

    if (isAdmin()) {
        $columns[] = ['data' => 'vendor_name', 'name' => 'vendor_name', 'orderable' => false, 'searchable' => false];
    }

    $columns = array_merge($columns, [
        ['data' => 'total_amount', 'name' => 'total_amount', 'orderable' => false, 'searchable' => false],
        ['data' => 'status', 'name' => 'status', 'orderable' => false, 'searchable' => false, 'className' => 'text-center'],
        ['data' => 'created_at', 'name' => 'created_at', 'orderable' => false, 'searchable' => false],
        ['data' => null, 'orderable' => false, 'searchable' => false, 'className' => 'text-center'],
    ]);

    // Convert to JSON and add render functions
    $columnsJson = json_encode($columns);
    
    // Add render function for total_amount
    $columnsJson = str_replace(
        '"data":"total_amount"',
        '"data":"total_amount","render":function(data){return data+" ' . (trans('common.currency') ?? 'SAR') . '";}',
        $columnsJson
    );
    
    // Add render function for actions
    $columnsJson = str_replace(
        '"className":"text-center","data":null',
        '"className":"text-center","data":null,"render":function(data){const showUrl="' . route('admin.refunds.show', ':id') . '".replace(":id",data.id);return `<div class=\"orderDatatable_actions d-inline-flex gap-1 justify-content-center\"><a href=\"${showUrl}\" class=\"view btn btn-primary table_action_father\" title=\"' . trans('common.view') . '\"><i class=\"uil uil-eye table_action_icon\"></i></a></div>`;}',
        $columnsJson
    );
    @endphp

    {{-- DataTable Wrapper Component with Built-in Script --}}
    <x-datatable-wrapper
        :title="trans('menu.refunds.all')"
        icon="uil uil-redo"
        :showExport="false"
        tableId="refundsDataTable"
        ajaxUrl="{{ route('admin.refunds.datatable') }}"
        :headers="$headers"
        :columnsJson="$columnsJson"
        :customSelectIds="['status_filter']"
        :order="[[isAdmin() ? 7 : 6, 'desc']]"
        :pageLength="10">
        
        {{-- Search & Filters Component --}}
        <x-slot name="filters">
            <x-datatable-filters-advanced
                :searchPlaceholder="trans('refund::refund.fields.refund_number')"
                :filters="[
                    [
                        'name' => 'status_filter',
                        'id' => 'status_filter',
                        'label' => trans('refund::refund.fields.status'),
                        'icon' => 'uil uil-check-circle',
                        'options' => collect(\Modules\Refund\app\Models\RefundRequest::STATUSES)->map(fn($label, $value) => ['id' => $value, 'name' => trans('refund::refund.statuses.' . $value)])->values()->toArray(),
                        'selected' => request('status'),
                        'placeholder' => __('common.all'),
                    ],
                ]"
                :showDateFilters="true"
            />
        </x-slot>
    </x-datatable-wrapper>
</div>
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush
