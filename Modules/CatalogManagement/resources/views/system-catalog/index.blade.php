@extends('layout.app')
@section('title')
    {{ __('catalogmanagement::system_catalog.system_catalog') }}
@endsection

@push('styles')
<style>
    .id-badge {
        display: inline-block;
        background-color: var(--color-primary);
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 13px;
        min-width: 40px;
        text-align: center;
    }
    .color-preview {
        width: 35px;
        height: 35px;
        border: 2px solid #ddd;
        border-radius: 6px;
        display: inline-block;
        vertical-align: middle;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .color-code {
        display: inline-block;
        margin-left: 10px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: #5a5c69;
    }
    [dir="rtl"] .color-code {
        margin-left: 0;
        margin-right: 10px;
    }
    .subcategory-list {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }
    .subcategory-list li {
        padding: 6px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .subcategory-list li:last-child {
        border-bottom: none;
    }
    .variant-tree {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }
    .variant-tree li {
        padding: 4px 0;
        position: relative;
    }
    .variant-tree ul {
        list-style: none;
        padding-left: 25px;
        margin-top: 4px;
        border-left: 2px solid #e3e6f0;
    }
    [dir="rtl"] .variant-tree ul {
        padding-left: 0;
        padding-right: 25px;
        border-left: none;
        border-right: 2px solid #e3e6f0;
    }
    .variant-tree ul li {
        position: relative;
    }
    .variant-tree ul li:before {
        content: '';
        position: absolute;
        left: -25px;
        top: 15px;
        width: 20px;
        height: 2px;
        background: #e3e6f0;
    }
    [dir="rtl"] .variant-tree ul li:before {
        left: auto;
        right: -25px;
    }
    .variant-name {
        font-weight: 500;
        color: #2c3e50;
    }
    .nav-tabs .nav-link {
        color: var(--color-primary);
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        background-color: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }
    
    /* RTL Support */
    [dir="rtl"] .ms-2 {
        margin-right: 0.5rem !important;
        margin-left: 0 !important;
    }
    [dir="rtl"] .me-1 {
        margin-left: 0.25rem !important;
        margin-right: 0 !important;
    }
    [dir="rtl"] .me-2 {
        margin-left: 0.5rem !important;
        margin-right: 0 !important;
    }
    
    /* Collapsible Tree Styles */
    .collapsible-tree .tree-node {
        display: flex;
        align-items: center;
        padding: 8px 0;
    }
    .collapsible-tree .tree-toggle {
        cursor: pointer;
        color: var(--color-primary);
        font-size: 18px;
        width: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }
    .collapsible-tree .tree-toggle:hover {
        color: #0056b3;
    }
    .collapsible-tree .tree-toggle-placeholder {
        width: 24px;
        display: inline-block;
    }
    .collapsible-tree .tree-children {
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    .collapsible-tree .tree-children.collapsed {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => __('catalogmanagement::system_catalog.system_catalog')],
            ]" />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                <div class="d-flex justify-content-between align-items-center mb-25">
                    <h4 class="mb-0 fw-600 text-primary">
                        <i class="uil uil-database me-2"></i>{{ __('catalogmanagement::system_catalog.system_catalog') }}
                    </h4>
                </div>

                <!-- Global Search -->
                <div class="mb-25">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="globalSearch" class="il-gray fs-14 fw-500 mb-10">
                                            <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                                        </label>
                                        <input type="text" id="globalSearch" class="form-control" 
                                            placeholder="{{ __('catalogmanagement::system_catalog.search_placeholder') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="catalogTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="category-tree-tab" data-bs-toggle="tab" data-bs-target="#category-tree" type="button" role="tab">
                            <i class="uil uil-sitemap me-1"></i> {{ __('catalogmanagement::system_catalog.category_tree') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="departments-tab" data-bs-toggle="tab" data-bs-target="#departments" type="button" role="tab">
                            <i class="uil uil-layer-group me-1"></i> {{ __('catalogmanagement::system_catalog.departments') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button" role="tab">
                            <i class="uil uil-apps me-1"></i> {{ __('catalogmanagement::system_catalog.categories_subcategories') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="variants-tab" data-bs-toggle="tab" data-bs-target="#variants" type="button" role="tab">
                            <i class="uil uil-sliders-v-alt me-1"></i> {{ __('catalogmanagement::system_catalog.variants') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="brands-tab" data-bs-toggle="tab" data-bs-target="#brands" type="button" role="tab">
                            <i class="uil uil-tag-alt me-1"></i> {{ __('catalogmanagement::system_catalog.brands') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="regions-tab" data-bs-toggle="tab" data-bs-target="#regions" type="button" role="tab">
                            <i class="uil uil-map-marker me-1"></i> {{ __('catalogmanagement::system_catalog.regions') }}
                        </button>
                    </li>
                    @if(isset($vendors))
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="vendors-tab" data-bs-toggle="tab" data-bs-target="#vendors" type="button" role="tab">
                            <i class="uil uil-store me-1"></i> {{ __('catalogmanagement::system_catalog.vendors') }}
                        </button>
                    </li>
                    @endif
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="catalogTabContent">
                    <!-- Category Tree Tab -->
                    <div class="tab-pane fade show active" id="category-tree" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" id="expandAllTree">
                                <i class="uil uil-angle-double-down me-1"></i>{{ __('catalogmanagement::system_catalog.expand_all') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAllTree">
                                <i class="uil uil-angle-double-up me-1"></i>{{ __('catalogmanagement::system_catalog.collapse_all') }}
                            </button>
                        </div>
                        <div class="category-tree-container">
                            @forelse($departments as $department)
                                <ul class="variant-tree collapsible-tree">
                                    <li>
                                        <div class="tree-node">
                                            @if($department->categories && $department->categories->count() > 0)
                                                <span class="tree-toggle" data-expanded="true"><i class="uil uil-minus-circle"></i></span>
                                            @else
                                                <span class="tree-toggle-placeholder"></span>
                                            @endif
                                            <span class="id-badge">{{ $department->id }}</span>
                                            <span class="variant-name ms-2">{{ $department->getTranslation('name', 'en') }} / {{ $department->getTranslation('name', 'ar') }}</span>
                                            <i class="uil uil-layer-group ms-2 text-primary"></i>
                                        </div>
                                        
                                        @if($department->categories && $department->categories->count() > 0)
                                            <ul class="tree-children">
                                                @foreach($department->categories as $category)
                                                    <li>
                                                        <div class="tree-node">
                                                            @if($category->subs && $category->subs->count() > 0)
                                                                <span class="tree-toggle" data-expanded="true"><i class="uil uil-minus-circle"></i></span>
                                                            @else
                                                                <span class="tree-toggle-placeholder"></span>
                                                            @endif
                                                            <span class="id-badge" style="font-size: 11px; padding: 2px 8px; background-color: #17a2b8;">{{ $category->id }}</span>
                                                            <span class="variant-name ms-2">{{ $category->getTranslation('name', 'en') }} / {{ $category->getTranslation('name', 'ar') }}</span>
                                                            <i class="uil uil-apps ms-2 text-info"></i>
                                                        </div>
                                                        
                                                        @if($category->subs && $category->subs->count() > 0)
                                                            <ul class="tree-children">
                                                                @foreach($category->subs as $sub)
                                                                    <li>
                                                                        <div class="tree-node">
                                                                            <span class="tree-toggle-placeholder"></span>
                                                                            <span class="id-badge" style="font-size: 11px; padding: 2px 8px; background-color: #6c757d;">{{ $sub->id }}</span>
                                                                            <span class="variant-name ms-2">{{ $sub->getTranslation('name', 'en') }} / {{ $sub->getTranslation('name', 'ar') }}</span>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                </ul>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="uil uil-folder-open fs-1 d-block mb-3"></i>
                                    {{ __('catalogmanagement::system_catalog.no_departments_found') }}
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Departments Tab -->
                    <div class="tab-pane fade" id="departments" role="tabpanel">
                        <x-catalog-table 
                            :headers="[
                                ['label' => __('catalogmanagement::system_catalog.id'), 'style' => 'width: 100px;', 'class' => 'text-center'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_english')],
                                ['label' => __('catalogmanagement::system_catalog.name_in_arabic')]
                            ]"
                            :data="$departments"
                            :emptyMessage="__('catalogmanagement::system_catalog.no_departments_found')">
                            @foreach($departments as $department)
                                <tr>
                                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">{{ $department->id }}</span></div></td>
                                    <td><div class="userDatatable-content">{{ $department->getTranslation('name', 'en') }}</div></td>
                                    <td><div class="userDatatable-content">{{ $department->getTranslation('name', 'ar') }}</div></td>
                                </tr>
                            @endforeach
                        </x-catalog-table>
                    </div>

                    <!-- Categories Tab -->
                    <div class="tab-pane fade" id="categories" role="tabpanel">
                        <x-catalog-table 
                            :headers="[
                                ['label' => __('catalogmanagement::system_catalog.id'), 'style' => 'width: 100px;', 'class' => 'text-center'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_english'), 'style' => 'width: 25%;'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_arabic'), 'style' => 'width: 25%;'],
                                ['label' => __('catalogmanagement::system_catalog.subcategories')]
                            ]"
                            :data="$categories"
                            :emptyMessage="__('catalogmanagement::system_catalog.no_categories_found')">
                            @foreach($categories as $category)
                                <tr>
                                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">{{ $category->id }}</span></div></td>
                                    <td><div class="userDatatable-content">{{ $category->getTranslation('name', 'en') }}</div></td>
                                    <td><div class="userDatatable-content">{{ $category->getTranslation('name', 'ar') }}</div></td>
                                    <td>
                                        <div class="userDatatable-content">
                                            @if($category->subs->count() > 0)
                                                <ul class="subcategory-list">
                                                    @foreach($category->subs as $sub)
                                                        <li>
                                                            <span class="id-badge" style="font-size: 11px; padding: 2px 8px;">{{ $sub->id }}</span>
                                                            <span class="ms-2">{{ $sub->getTranslation('name', 'en') }} / {{ $sub->getTranslation('name', 'ar') }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">{{ __('catalogmanagement::system_catalog.no_subcategories') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-catalog-table>
                    </div>

                    <!-- Variants Tab -->
                    <div class="tab-pane fade" id="variants" role="tabpanel">
                        <x-catalog-table 
                            :headers="[
                                ['label' => __('catalogmanagement::system_catalog.id'), 'style' => 'width: 80px;', 'class' => 'text-center'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_english'), 'style' => 'width: 15%;'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_arabic'), 'style' => 'width: 15%;'],
                                ['label' => __('catalogmanagement::system_catalog.key_name'), 'style' => 'width: 12%;'],
                                ['label' => __('catalogmanagement::system_catalog.color'), 'style' => 'width: 12%;'],
                                ['label' => __('catalogmanagement::system_catalog.variant_tree')]
                            ]"
                            :data="$variants"
                            :emptyMessage="__('catalogmanagement::system_catalog.no_variants_found')">
                            @foreach($variants as $variant)
                                <tr>
                                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">{{ $variant->id }}</span></div></td>
                                    <td><div class="userDatatable-content">{{ $variant->getTranslation('name', 'en') }}</div></td>
                                    <td><div class="userDatatable-content">{{ $variant->getTranslation('name', 'ar') }}</div></td>
                                    <td>
                                        <div class="userDatatable-content">
                                            @if($variant->key)
                                                {{ $variant->key->getTranslation('name', 'en') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            @if($variant->color)
                                                <div class="d-flex align-items-center">
                                                    <div class="color-preview" style="background-color: {{ $variant->color }};"></div>
                                                    <span class="color-code">{{ $variant->color }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            @if($variant->childrenRecursive && $variant->childrenRecursive->count() > 0)
                                                <ul class="variant-tree">
                                                    @foreach($variant->childrenRecursive as $child)
                                                        @include('catalogmanagement::system-catalog.partials.variant-tree-item', ['variant' => $child])
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">{{ __('catalogmanagement::system_catalog.no_children') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-catalog-table>
                    </div>

                    <!-- Brands Tab -->
                    <div class="tab-pane fade" id="brands" role="tabpanel">
                        <x-catalog-table 
                            :headers="[
                                ['label' => __('catalogmanagement::system_catalog.id'), 'style' => 'width: 100px;', 'class' => 'text-center'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_english')],
                                ['label' => __('catalogmanagement::system_catalog.name_in_arabic')]
                            ]"
                            :data="$brands"
                            :emptyMessage="__('catalogmanagement::system_catalog.no_brands_found')">
                            @foreach($brands as $brand)
                                <tr>
                                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">{{ $brand->id }}</span></div></td>
                                    <td><div class="userDatatable-content">{{ $brand->getTranslation('name', 'en') }}</div></td>
                                    <td><div class="userDatatable-content">{{ $brand->getTranslation('name', 'ar') }}</div></td>
                                </tr>
                            @endforeach
                        </x-catalog-table>
                    </div>

                    <!-- Regions Tab -->
                    <div class="tab-pane fade" id="regions" role="tabpanel">
                        <x-catalog-table 
                            :headers="[
                                ['label' => __('catalogmanagement::system_catalog.id'), 'style' => 'width: 100px;', 'class' => 'text-center'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_english'), 'style' => 'width: 20%;'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_arabic'), 'style' => 'width: 20%;'],
                                ['label' => __('catalogmanagement::system_catalog.city'), 'style' => 'width: 20%;'],
                                ['label' => __('catalogmanagement::system_catalog.country'), 'style' => 'width: 20%;']
                            ]"
                            :data="$regions"
                            :emptyMessage="__('catalogmanagement::system_catalog.no_regions_found')">
                            @foreach($regions as $region)
                                <tr>
                                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">{{ $region->id }}</span></div></td>
                                    <td><div class="userDatatable-content">{{ $region->getTranslation('name', 'en') }}</div></td>
                                    <td><div class="userDatatable-content">{{ $region->getTranslation('name', 'ar') }}</div></td>
                                    <td>
                                        <div class="userDatatable-content">
                                            @if($region->city)
                                                <span class="id-badge" style="font-size: 11px; padding: 2px 8px;">{{ $region->city->id }}</span>
                                                <span class="ms-2">{{ $region->city->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('catalogmanagement::system_catalog.no_city') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="userDatatable-content">
                                            @if($region->city && $region->city->country)
                                                <span class="id-badge" style="font-size: 11px; padding: 2px 8px;">{{ $region->city->country->id }}</span>
                                                <span class="ms-2">{{ $region->city->country->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('catalogmanagement::system_catalog.no_country') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-catalog-table>
                    </div>

                    <!-- Vendors Tab (Admin Only) -->
                    @if(isset($vendors))
                    <div class="tab-pane fade" id="vendors" role="tabpanel">
                        <x-catalog-table 
                            :headers="[
                                ['label' => __('catalogmanagement::system_catalog.id'), 'style' => 'width: 100px;', 'class' => 'text-center'],
                                ['label' => __('catalogmanagement::system_catalog.logo'), 'style' => 'width: 120px;', 'class' => 'text-center'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_english'), 'style' => 'width: 25%;'],
                                ['label' => __('catalogmanagement::system_catalog.name_in_arabic'), 'style' => 'width: 25%;'],
                                ['label' => __('catalogmanagement::system_catalog.email')],
                                ['label' => __('catalogmanagement::system_catalog.phone')]
                            ]"
                            :data="$vendors"
                            :emptyMessage="__('catalogmanagement::system_catalog.no_vendors_found')">
                            @foreach($vendors as $vendor)
                                <tr>
                                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">{{ $vendor->id }}</span></div></td>
                                    <td class="text-center">
                                        <div class="userDatatable-content">
                                            @if($vendor->logo && $vendor->logo->path)
                                                <img src="{{ asset($vendor->logo->path) }}" 
                                                     alt="{{ $vendor->getTranslation('name', app()->getLocale()) }}" 
                                                     style="max-width: 80px; max-height: 60px; object-fit: contain; border-radius: 8px; border: 1px solid #e3e6f0; padding: 5px;">
                                            @else
                                                <div style="width: 80px; height: 60px; background-color: #f8f9fc; border: 1px solid #e3e6f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                    <i class="uil uil-store" style="font-size: 24px; color: #d1d3e2;"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td><div class="userDatatable-content">{{ $vendor->getTranslation('name', 'en') }}</div></td>
                                    <td><div class="userDatatable-content">{{ $vendor->getTranslation('name', 'ar') }}</div></td>
                                    <td><div class="userDatatable-content">{{ $vendor->email ?? '-' }}</div></td>
                                    <td><div class="userDatatable-content">{{ $vendor->phone ?? '-' }}</div></td>
                                </tr>
                            @endforeach
                        </x-catalog-table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('globalSearch');
    
    // Collapsible Tree Functionality
    const treeToggles = document.querySelectorAll('.collapsible-tree .tree-toggle');
    
    treeToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('data-expanded') === 'true';
            const parentLi = this.closest('li');
            const childrenUl = parentLi.querySelector(':scope > .tree-children');
            
            if (childrenUl) {
                if (isExpanded) {
                    childrenUl.classList.add('collapsed');
                    this.setAttribute('data-expanded', 'false');
                    this.innerHTML = '<i class="uil uil-plus-circle"></i>';
                } else {
                    childrenUl.classList.remove('collapsed');
                    this.setAttribute('data-expanded', 'true');
                    this.innerHTML = '<i class="uil uil-minus-circle"></i>';
                }
            }
        });
    });
    
    // Expand All Button
    document.getElementById('expandAllTree')?.addEventListener('click', function() {
        document.querySelectorAll('.collapsible-tree .tree-toggle').forEach(toggle => {
            const parentLi = toggle.closest('li');
            const childrenUl = parentLi.querySelector(':scope > .tree-children');
            if (childrenUl) {
                childrenUl.classList.remove('collapsed');
                toggle.setAttribute('data-expanded', 'true');
                toggle.innerHTML = '<i class="uil uil-minus-circle"></i>';
            }
        });
    });
    
    // Collapse All Button
    document.getElementById('collapseAllTree')?.addEventListener('click', function() {
        document.querySelectorAll('.collapsible-tree .tree-toggle').forEach(toggle => {
            const parentLi = toggle.closest('li');
            const childrenUl = parentLi.querySelector(':scope > .tree-children');
            if (childrenUl) {
                childrenUl.classList.add('collapsed');
                toggle.setAttribute('data-expanded', 'false');
                toggle.innerHTML = '<i class="uil uil-plus-circle"></i>';
            }
        });
    });
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        // Search in Category Tree
        const treeContainer = document.querySelector('.category-tree-container');
        if (treeContainer) {
            const allTreeItems = treeContainer.querySelectorAll('.collapsible-tree > li');
            
            allTreeItems.forEach(departmentLi => {
                let departmentMatch = false;
                const departmentNode = departmentLi.querySelector(':scope > .tree-node');
                const departmentText = departmentNode ? departmentNode.textContent.toLowerCase() : '';
                
                if (searchTerm === '' || departmentText.includes(searchTerm)) {
                    departmentMatch = true;
                }
                
                // Check categories
                const categoryItems = departmentLi.querySelectorAll(':scope > .tree-children > li');
                categoryItems.forEach(categoryLi => {
                    let categoryMatch = false;
                    const categoryNode = categoryLi.querySelector(':scope > .tree-node');
                    const categoryText = categoryNode ? categoryNode.textContent.toLowerCase() : '';
                    
                    if (searchTerm === '' || categoryText.includes(searchTerm)) {
                        categoryMatch = true;
                    }
                    
                    // Check subcategories
                    const subItems = categoryLi.querySelectorAll(':scope > .tree-children > li');
                    subItems.forEach(subLi => {
                        const subNode = subLi.querySelector(':scope > .tree-node');
                        const subText = subNode ? subNode.textContent.toLowerCase() : '';
                        
                        if (searchTerm === '' || subText.includes(searchTerm)) {
                            subLi.style.display = '';
                            categoryMatch = true;
                            departmentMatch = true;
                        } else {
                            subLi.style.display = 'none';
                        }
                    });
                    
                    // Show/hide category
                    if (searchTerm === '' || categoryMatch) {
                        categoryLi.style.display = '';
                        // Expand parent if searching
                        if (searchTerm !== '' && categoryMatch) {
                            const childrenUl = categoryLi.querySelector(':scope > .tree-children');
                            const toggle = categoryLi.querySelector(':scope > .tree-node .tree-toggle');
                            if (childrenUl && toggle) {
                                childrenUl.classList.remove('collapsed');
                                toggle.setAttribute('data-expanded', 'true');
                                toggle.innerHTML = '<i class="uil uil-minus-circle"></i>';
                            }
                        }
                    } else {
                        categoryLi.style.display = 'none';
                    }
                });
                
                // Show/hide department
                const departmentUl = departmentLi.closest('.collapsible-tree');
                if (searchTerm === '' || departmentMatch) {
                    departmentUl.style.display = '';
                    // Expand department if searching
                    if (searchTerm !== '' && departmentMatch) {
                        const childrenUl = departmentLi.querySelector(':scope > .tree-children');
                        const toggle = departmentLi.querySelector(':scope > .tree-node .tree-toggle');
                        if (childrenUl && toggle) {
                            childrenUl.classList.remove('collapsed');
                            toggle.setAttribute('data-expanded', 'true');
                            toggle.innerHTML = '<i class="uil uil-minus-circle"></i>';
                        }
                    }
                } else {
                    departmentUl.style.display = 'none';
                }
            });
        }
        
        // Search in all tables
        const tables = document.querySelectorAll('.table tbody');
        
        tables.forEach(table => {
            const rows = table.querySelectorAll('tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                // Skip empty state rows
                if (row.querySelector('td[colspan]')) {
                    return;
                }
                
                const text = row.textContent.toLowerCase();
                
                if (searchTerm === '' || text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide empty state message
            const emptyRow = table.querySelector('tr td[colspan]');
            if (emptyRow) {
                const parentRow = emptyRow.parentElement;
                if (visibleCount === 0 && searchTerm !== '') {
                    parentRow.style.display = '';
                    emptyRow.querySelector('.userDatatable-content').textContent = '{{ __('catalogmanagement::system_catalog.no_results_found') }}';
                } else if (visibleCount > 0) {
                    parentRow.style.display = 'none';
                }
            }
        });
    });
});
</script>
@endpush
@endsection
