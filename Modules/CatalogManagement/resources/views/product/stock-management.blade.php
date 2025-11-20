@extends('layout.app')
@section('title', __('catalogmanagement::product.stock_management'))

@push('styles')
    @vite('Modules/CatalogManagement/resources/assets/scss/stock-management.scss')
@endpush

@section('content')
@endsection

@push('after-body')
    <x-loading-overlay />
@endpush
