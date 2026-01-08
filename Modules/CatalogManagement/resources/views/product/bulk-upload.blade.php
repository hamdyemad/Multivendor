admin.@extends('layout.app')
@section('title', __('catalogmanagement::product.bulk_upload'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('catalogmanagement::product.products_management'), 'url' => route('admin.products.index')],
                ['title' => __('catalogmanagement::product.bulk_upload')],
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                <div class="d-flex justify-content-between align-items-center mb-25">
                    <h4 class="mb-0 fw-600 text-primary">
                        <i class="uil uil-upload me-2"></i>{{ __('catalogmanagement::product.import_products_from_excel') }}
                    </h4>
                    <a href="{{ route('admin.products.download-demo') }}" 
                        class="btn btn-success btn-squared shadow-sm px-4">
                        <i class="uil uil-download-alt"></i> {{ __('catalogmanagement::product.download_demo_excel') }}
                    </a>
                </div>

                {{-- Alert Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="uil uil-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-times-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="uil uil-times-circle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Import Errors --}}
                @if(session('import_errors'))
                    {{-- Alert Header --}}
                    <div class="alert alert-danger border-0 shadow-sm mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="alert-icon me-3">
                                <i class="uil uil-exclamation-triangle fs-3 text-danger"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1 fw-bold">{{ __('catalogmanagement::product.import_errors') }}</h5>
                                <p class="mb-0 text-muted small">{{ __('catalogmanagement::product.import_errors_description') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Errors Table --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-hover table-bordered mb-0">
                                    <thead class="table-light sticky-top" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <tr>
                                            <th style="width: 120px;" class="text-center">{{ __('catalogmanagement::product.sheet') }}</th>
                                            <th style="width: 80px;" class="text-center">{{ __('catalogmanagement::product.row') }}</th>
                                            <th style="width: 180px;">{{ __('catalogmanagement::product.sku') }}</th>
                                            <th>{{ __('catalogmanagement::product.error') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('import_errors') as $error)
                                            @php
                                                $sheetName = $error['sheet'] ?? 'products';
                                            @endphp
                                            <tr>
                                                <td class="text-center align-middle">
                                                    @if($sheetName === 'products')
                                                        <span class="badge badge-round badge-lg bg-primary text-white">{{ $sheetName }}</span>
                                                    @elseif($sheetName === 'variants')
                                                        <span class="badge badge-round badge-lg bg-info text-white">{{ $sheetName }}</span>
                                                    @elseif($sheetName === 'variant_stock')
                                                        <span class="badge badge-round badge-lg bg-warning text-dark">{{ $sheetName }}</span>
                                                    @elseif($sheetName === 'images')
                                                        <span class="badge badge-round badge-lg bg-success text-white">{{ $sheetName }}</span>
                                                    @elseif($sheetName === 'occasions')
                                                        <span class="badge badge-round badge-lg bg-purple text-white">{{ $sheetName }}</span>
                                                    @elseif($sheetName === 'occasion_products')
                                                        <span class="badge badge-round badge-lg bg-danger text-white">{{ $sheetName }}</span>
                                                    @else
                                                        <span class="badge badge-round badge-lg bg-secondary text-white">{{ $sheetName }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-light text-dark border">{{ $error['row'] }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <code class="text-danger fw-bold">{{ $error['sku'] ?? $error['id'] ?? $error['variant_id'] ?? $error['product_id'] ?? $error['occasion_id'] ?? '-' }}</code>
                                                </td>
                                                <td class="align-middle">
                                                    @if(is_array($error['errors']))
                                                        <ul class="mb-0 ps-3 text-dark">
                                                            @foreach($error['errors'] as $err)
                                                                <li class="mb-1">{{ $err }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-dark">{{ $error['errors'] }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Upload Form --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.products.bulk-upload.store') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="file" class="il-gray fs-14 fw-500 mb-10">
                                        <i class="uil uil-file-upload me-1"></i> {{ __('catalogmanagement::product.choose_excel_file') }}
                                    </label>
                                    <input type="file" 
                                        class="form-control @error('file') is-invalid @enderror" 
                                        id="file" 
                                        name="file" 
                                        accept=".xlsx,.xls"
                                        required>
                                    <small class="text-muted">{{ __('catalogmanagement::product.accepted_formats') }}: .xlsx, .xls ({{ __('catalogmanagement::product.max_file_size') }})</small>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="fileName" class="mt-2 text-primary"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 d-flex gap-3">
                                    <button type="submit" class="btn btn-primary btn-squared px-5" id="importBtn">
                                        <i class="uil uil-import"></i> {{ __('catalogmanagement::product.import') }}
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-squared px-5">
                                        <i class="uil uil-times"></i> {{ __('common.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header ">
                        <h6 class="mb-0"><i class="uil uil-info-circle me-2"></i>{{ __('catalogmanagement::product.import_instructions') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="instructionsAccordion">
                            {{-- General Instructions --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingGeneral">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeneral" aria-expanded="true" aria-controls="collapseGeneral">
                                        <i class="uil uil-file-check-alt me-2"></i> {{ __('catalogmanagement::product.general_instructions') }}
                                    </button>
                                </h2>
                                <div id="collapseGeneral" class="accordion-collapse collapse show" aria-labelledby="headingGeneral" data-bs-parent="#instructionsAccordion">
                                    <div class="accordion-body">
                                        <ol class="mb-0">
                                            <li>{{ __('catalogmanagement::product.instruction_1') }}</li>
                                            <li>{{ __('catalogmanagement::product.instruction_2') }}</li>
                                            <li>{{ __('catalogmanagement::product.instruction_3') }}</li>
                                            <li>{{ __('catalogmanagement::product.instruction_4') }}</li>
                                            <li>{{ __('catalogmanagement::product.instruction_5') }}</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            {{-- Products Sheet --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingProducts">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProducts" aria-expanded="false" aria-controls="collapseProducts">
                                        <span class="badge badge-round badge-lg bg-primary me-2">products</span> {{ __('catalogmanagement::product.products_sheet_columns') }}
                                    </button>
                                </h2>
                                <div id="collapseProducts" class="accordion-collapse collapse" aria-labelledby="headingProducts" data-bs-parent="#instructionsAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 150px;">{{ __('catalogmanagement::product.column_name') }}</th>
                                                        <th>{{ __('catalogmanagement::product.description') }}</th>
                                                        <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><code>id</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_id_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_id_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>sku</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_sku_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_sku_source') }}</td>
                                                    </tr>
                                                    @if(isAdmin())
                                                    <tr>
                                                        <td><code>vendor_id</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_vendor_id_desc') }}</td>
                                                        <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <td><code>title_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_title_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>title_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_title_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>description_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_description_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_description_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>description_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_description_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_description_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>summary_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_summary_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_summary_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>summary_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_summary_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_summary_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>features_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_features_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_features_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>features_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_features_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_features_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>instructions_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_instructions_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_instructions_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>instructions_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_instructions_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_instructions_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>extra_description_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_extra_description_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_extra_description_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>extra_description_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_extra_description_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_extra_description_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>material_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_material_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_material_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>material_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_material_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_material_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_title_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_title_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_title_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_title_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_description_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_description_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_description_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_description_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_description_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_description_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_keywords_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_keywords_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_keywords_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_keywords_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_keywords_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_meta_keywords_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>department</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_department_desc') }}</td>
                                                        <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>main_category</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_category_desc') }}</td>
                                                        <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>sub_category</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_sub_category_desc') }}</td>
                                                        <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>brand</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_brand_desc') }}</td>
                                                        <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>have_varient</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_have_variant_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_have_variant_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>status</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_status_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_status_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>featured_product</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_featured_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_featured_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>max_per_order</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_max_per_order_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_max_per_order_source') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Variants Sheet --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingVariants">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVariants" aria-expanded="false" aria-controls="collapseVariants">
                                        <span class="badge badge-round badge-lg bg-info me-2">variants</span> {{ __('catalogmanagement::product.variants_sheet_columns') }}
                                    </button>
                                </h2>
                                <div id="collapseVariants" class="accordion-collapse collapse" aria-labelledby="headingVariants" data-bs-parent="#instructionsAccordion">
                                    <div class="accordion-body">
                                        <div class="alert alert-light border-info mb-3" style="background-color: #e7f3ff; border-left: 4px solid #0d6efd;">
                                            <i class="uil uil-info-circle me-2 text-info"></i>
                                            <strong>{{ __('catalogmanagement::product.note') }}:</strong> {{ __('catalogmanagement::product.variants_sheet_note') }}
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 200px;">{{ __('catalogmanagement::product.column_name') }}</th>
                                                        <th>{{ __('catalogmanagement::product.description') }}</th>
                                                        <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><code>product_id</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_product_id_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_product_id_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>sku</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_variant_sku_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_variant_sku_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>variant_configuration_id</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_variant_config_desc') }}</td>
                                                        <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>price</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_price_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_price_source') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Variant Stock Sheet --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingStock">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStock" aria-expanded="false" aria-controls="collapseStock">
                                        <span class="badge badge-round badge-lg bg-warning me-2">variant_stock</span> {{ __('catalogmanagement::product.stock_sheet_columns') }}
                                    </button>
                                </h2>
                                <div id="collapseStock" class="accordion-collapse collapse" aria-labelledby="headingStock" data-bs-parent="#instructionsAccordion">
                                    <div class="accordion-body">
                                        <div class="alert alert-light border-info mb-3" style="background-color: #e7f3ff; border-left: 4px solid #0d6efd;">
                                            <i class="uil uil-info-circle me-2 text-info"></i>
                                            <strong>{{ __('catalogmanagement::product.note') }}:</strong> {{ __('catalogmanagement::product.stock_sheet_note') }}
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 150px;">{{ __('catalogmanagement::product.column_name') }}</th>
                                                        <th>{{ __('catalogmanagement::product.description') }}</th>
                                                        <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><code>variant_sku</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_stock_variant_sku_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_stock_variant_sku_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>region_id</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_region_id_desc') }}</td>
                                                        <td><a href="{{ route('admin.system-catalog.index') }}" target="_blank">{{ __('catalogmanagement::product.system_catalog') }}</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>stock</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_stock_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_stock_source') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Images Sheet --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingImages">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImages" aria-expanded="false" aria-controls="collapseImages">
                                        <span class="badge badge-round badge-lg bg-success me-2">images</span> {{ __('catalogmanagement::product.images_sheet_columns') }}
                                    </button>
                                </h2>
                                <div id="collapseImages" class="accordion-collapse collapse" aria-labelledby="headingImages" data-bs-parent="#instructionsAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 150px;">{{ __('catalogmanagement::product.column_name') }}</th>
                                                        <th>{{ __('catalogmanagement::product.description') }}</th>
                                                        <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><code>product_id</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_image_product_id_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_image_product_id_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>image</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_image_url_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_image_url_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>is_main</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_is_main_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_is_main_source') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(isAdmin())
                            {{-- Occasions Sheet --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOccasions">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOccasions" aria-expanded="false" aria-controls="collapseOccasions">
                                        <span class="badge badge-round badge-lg bg-purple me-2">occasions</span> {{ __('catalogmanagement::product.occasions_sheet_columns') }}
                                    </button>
                                </h2>
                                <div id="collapseOccasions" class="accordion-collapse collapse" aria-labelledby="headingOccasions" data-bs-parent="#instructionsAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 150px;">{{ __('catalogmanagement::product.column_name') }}</th>
                                                        <th>{{ __('catalogmanagement::product.description') }}</th>
                                                        <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><code>id</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_id_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_id_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>name_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_name_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_name_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>name_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_name_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_name_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>title_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_title_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>title_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_title_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>sub_title_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_sub_title_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_sub_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>sub_title_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_sub_title_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_sub_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_title_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_title_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_title_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_title_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_title_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_description_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_description_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_description_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_description_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_description_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_description_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_keywords_en</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_keywords_en_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_keywords_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>meta_keywords_ar</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_keywords_ar_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_meta_keywords_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>start_date</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_start_date_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_start_date_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>end_date</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_end_date_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_end_date_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>image</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_image_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_image_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>is_active</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_is_active_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occasion_is_active_source') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Occasion Products Sheet --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOccasionProducts">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOccasionProducts" aria-expanded="false" aria-controls="collapseOccasionProducts">
                                        <span class="badge badge-round badge-lg bg-danger me-2">occasion_products</span> {{ __('catalogmanagement::product.occasion_products_sheet_columns') }}
                                    </button>
                                </h2>
                                <div id="collapseOccasionProducts" class="accordion-collapse collapse" aria-labelledby="headingOccasionProducts" data-bs-parent="#instructionsAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 150px;">{{ __('catalogmanagement::product.column_name') }}</th>
                                                        <th>{{ __('catalogmanagement::product.description') }}</th>
                                                        <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><code>occasion_id</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occ_prod_occasion_id_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occ_prod_occasion_id_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>variant_sku</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occ_prod_variant_sku_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occ_prod_variant_sku_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>special_price</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occ_prod_special_price_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occ_prod_special_price_source') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>position</code></td>
                                                        <td>{{ __('catalogmanagement::product.col_occ_prod_position_desc') }}</td>
                                                        <td>{{ __('catalogmanagement::product.col_occ_prod_position_source') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Move accordion arrow to the end */
.accordion-button {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.accordion-button::after {
    margin-left: auto;
    margin-right: 0;
}

/* RTL support */
[dir="rtl"] .accordion-button::after {
    margin-left: 0;
    margin-right: auto;
}

/* Ensure content stays on the left/right */
.accordion-button > * {
    flex-shrink: 0;
}

/* Purple badge color for occasions */
.bg-purple {
    background-color: #6f42c1 !important;
    color: #fff !important;
}

.badge.bg-purple {
    background-color: #6f42c1 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('file').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        document.getElementById('fileName').textContent = '{{ __("catalogmanagement::product.selected_file") }}: ' + fileName;
    }
});

document.getElementById('bulkUploadForm').addEventListener('submit', function() {
    const btn = document.getElementById('importBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("catalogmanagement::product.importing") }}...';
});
</script>
@endpush
@endsection
