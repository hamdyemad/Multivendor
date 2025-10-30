@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => 'Products Management']
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Products</h4>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-squared">
                        <i class="uil uil-plus"></i> Create Product
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted">Product list will appear here...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
