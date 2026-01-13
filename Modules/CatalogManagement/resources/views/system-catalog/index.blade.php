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
    .nav-tabs .nav-link {
        color: var(--color-primary);
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        background-color: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }
    .pagination-info {
        font-size: 14px;
        color: #6c757d;
    }
    .loading-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
    }
    .loading-spinner .spinner-border {
        width: 3rem;
        height: 3rem;
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
                        <button class="nav-link active" id="departments-tab" data-bs-toggle="tab" data-bs-target="#departments" data-tab="departments" type="button" role="tab">
                            <i class="uil uil-layer-group me-1"></i> {{ __('catalogmanagement::system_catalog.departments') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" data-tab="categories" type="button" role="tab">
                            <i class="uil uil-apps me-1"></i> {{ __('catalogmanagement::system_catalog.categories_subcategories') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="variants-tab" data-bs-toggle="tab" data-bs-target="#variants" data-tab="variants" type="button" role="tab">
                            <i class="uil uil-sliders-v-alt me-1"></i> {{ __('catalogmanagement::system_catalog.variants') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="brands-tab" data-bs-toggle="tab" data-bs-target="#brands" data-tab="brands" type="button" role="tab">
                            <i class="uil uil-tag-alt me-1"></i> {{ __('catalogmanagement::system_catalog.brands') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="regions-tab" data-bs-toggle="tab" data-bs-target="#regions" data-tab="regions" type="button" role="tab">
                            <i class="uil uil-map-marker me-1"></i> {{ __('catalogmanagement::system_catalog.regions') }}
                        </button>
                    </li>
                    @if(isAdmin())
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="vendors-tab" data-bs-toggle="tab" data-bs-target="#vendors" data-tab="vendors" type="button" role="tab">
                            <i class="uil uil-store me-1"></i> {{ __('catalogmanagement::system_catalog.vendors') }}
                        </button>
                    </li>
                    @endif
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="catalogTabContent">
                    <!-- Departments Tab -->
                    <div class="tab-pane fade show active" id="departments" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="width: 100px;" class="text-center"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.id') }}</span></th>
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_english') }}</span></th>
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_arabic') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody id="departments-tbody"></tbody>
                            </table>
                        </div>
                        <div id="departments-pagination" class="d-flex justify-content-between align-items-center mt-3"></div>
                    </div>

                    <!-- Categories Tab -->
                    <div class="tab-pane fade" id="categories" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="width: 80px;" class="text-center"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.id') }}</span></th>
                                        <th style="width: 15%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.department') }}</span></th>
                                        <th style="width: 20%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_english') }}</span></th>
                                        <th style="width: 20%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_arabic') }}</span></th>
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.subcategories') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody id="categories-tbody"></tbody>
                            </table>
                        </div>
                        <div id="categories-pagination" class="d-flex justify-content-between align-items-center mt-3"></div>
                    </div>

                    <!-- Variants Tab -->
                    <div class="tab-pane fade" id="variants" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="width: 80px;" class="text-center"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.id') }}</span></th>
                                        <th style="width: 25%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_english') }}</span></th>
                                        <th style="width: 25%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_arabic') }}</span></th>
                                        <th style="width: 20%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.key_name') }}</span></th>
                                        <th style="width: 15%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.color') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody id="variants-tbody"></tbody>
                            </table>
                        </div>
                        <div id="variants-pagination" class="d-flex justify-content-between align-items-center mt-3"></div>
                    </div>

                    <!-- Brands Tab -->
                    <div class="tab-pane fade" id="brands" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="width: 100px;" class="text-center"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.id') }}</span></th>
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_english') }}</span></th>
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_arabic') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody id="brands-tbody"></tbody>
                            </table>
                        </div>
                        <div id="brands-pagination" class="d-flex justify-content-between align-items-center mt-3"></div>
                    </div>

                    <!-- Regions Tab -->
                    <div class="tab-pane fade" id="regions" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="width: 100px;" class="text-center"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.id') }}</span></th>
                                        <th style="width: 20%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_english') }}</span></th>
                                        <th style="width: 20%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_arabic') }}</span></th>
                                        <th style="width: 20%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.city') }}</span></th>
                                        <th style="width: 20%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.country') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody id="regions-tbody"></tbody>
                            </table>
                        </div>
                        <div id="regions-pagination" class="d-flex justify-content-between align-items-center mt-3"></div>
                    </div>

                    <!-- Vendors Tab (Admin Only) -->
                    @if(isAdmin())
                    <div class="tab-pane fade" id="vendors" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 table-borderless">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th style="width: 100px;" class="text-center"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.id') }}</span></th>
                                        <th style="width: 120px;" class="text-center"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.logo') }}</span></th>
                                        <th style="width: 25%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_english') }}</span></th>
                                        <th style="width: 25%;"><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.name_in_arabic') }}</span></th>
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.email') }}</span></th>
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::system_catalog.phone') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody id="vendors-tbody"></tbody>
                            </table>
                        </div>
                        <div id="vendors-pagination" class="d-flex justify-content-between align-items-center mt-3"></div>
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
    const routes = {
        departments: "{{ route('admin.system-catalog.departments') }}",
        categories: "{{ route('admin.system-catalog.categories') }}",
        variants: "{{ route('admin.system-catalog.variants') }}",
        brands: "{{ route('admin.system-catalog.brands') }}",
        regions: "{{ route('admin.system-catalog.regions') }}",
        @if(isAdmin())
        vendors: "{{ route('admin.system-catalog.vendors') }}"
        @endif
    };

    const translations = {
        noData: "{{ __('catalogmanagement::system_catalog.no_data_found') }}",
        noSubcategories: "{{ __('catalogmanagement::system_catalog.no_subcategories') }}",
        showing: "{{ __('common.showing') }}",
        of: "{{ __('common.of') }}",
        entries: "{{ __('common.entries') }}",
        previous: "{{ __('common.previous') }}",
        next: "{{ __('common.next') }}"
    };

    const state = {
        currentTab: 'departments',
        pages: { departments: 1, categories: 1, variants: 1, brands: 1, regions: 1, vendors: 1 },
        search: '',
        loaded: { departments: false, categories: false, variants: false, brands: false, regions: false, vendors: false }
    };

    let searchTimeout = null;

    // Load data for a tab
    function loadTabData(tab, page = 1) {
        const tbody = document.getElementById(`${tab}-tbody`);
        const pagination = document.getElementById(`${tab}-pagination`);
        
        tbody.innerHTML = `<tr><td colspan="10" class="text-center"><div class="loading-spinner"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div></td></tr>`;
        
        const url = new URL(routes[tab], window.location.origin);
        url.searchParams.set('page', page);
        if (state.search) url.searchParams.set('search', state.search);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                state.pages[tab] = data.current_page;
                state.loaded[tab] = true;
                renderTable(tab, data);
                renderPagination(tab, data, pagination);
            })
            .catch(error => {
                console.error('Error loading data:', error);
                tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger">Error loading data</td></tr>`;
            });
    }

    // Render table rows based on tab type
    function renderTable(tab, data) {
        const tbody = document.getElementById(`${tab}-tbody`);
        
        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="10" class="text-center"><div class="userDatatable-content">${translations.noData}</div></td></tr>`;
            return;
        }

        let html = '';
        data.data.forEach(item => {
            html += renderRow(tab, item);
        });
        tbody.innerHTML = html;
    }

    // Render a single row based on tab type
    function renderRow(tab, item) {
        switch(tab) {
            case 'departments':
                return `<tr>
                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">${item.id}</span></div></td>
                    <td><div class="userDatatable-content">${item.name_en || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.name_ar || '-'}</div></td>
                </tr>`;
            
            case 'categories':
                let subsHtml = '';
                if (item.subs && item.subs.length > 0) {
                    subsHtml = '<ul class="subcategory-list">';
                    item.subs.forEach(sub => {
                        subsHtml += `<li><span class="id-badge" style="font-size: 11px; padding: 2px 8px;">${sub.id}</span><span class="ms-2">${sub.name_en || '-'} / ${sub.name_ar || '-'}</span></li>`;
                    });
                    subsHtml += '</ul>';
                } else {
                    subsHtml = `<span class="text-muted">${translations.noSubcategories}</span>`;
                }
                return `<tr>
                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">${item.id}</span></div></td>
                    <td><div class="userDatatable-content">${item.department_id ? `<span class="id-badge" style="font-size: 11px; padding: 2px 8px;">${item.department_id}</span><span class="ms-2">${item.department_name || '-'}</span>` : '<span class="text-muted">-</span>'}</div></td>
                    <td><div class="userDatatable-content">${item.name_en || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.name_ar || '-'}</div></td>
                    <td><div class="userDatatable-content">${subsHtml}</div></td>
                </tr>`;
            
            case 'variants':
                let colorHtml = item.color 
                    ? `<div class="d-flex align-items-center"><div class="color-preview" style="background-color: ${item.color};"></div><span class="color-code">${item.color}</span></div>`
                    : '<span class="text-muted">-</span>';
                return `<tr>
                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">${item.id}</span></div></td>
                    <td><div class="userDatatable-content">${item.name_en || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.name_ar || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.key_name || '<span class="text-muted">-</span>'}</div></td>
                    <td><div class="userDatatable-content">${colorHtml}</div></td>
                </tr>`;
            
            case 'brands':
                return `<tr>
                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">${item.id}</span></div></td>
                    <td><div class="userDatatable-content">${item.name_en || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.name_ar || '-'}</div></td>
                </tr>`;
            
            case 'regions':
                return `<tr>
                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">${item.id}</span></div></td>
                    <td><div class="userDatatable-content">${item.name_en || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.name_ar || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.city_id ? `<span class="id-badge" style="font-size: 11px; padding: 2px 8px;">${item.city_id}</span><span class="ms-2">${item.city_name || '-'}</span>` : '<span class="text-muted">-</span>'}</div></td>
                    <td><div class="userDatatable-content">${item.country_id ? `<span class="id-badge" style="font-size: 11px; padding: 2px 8px;">${item.country_id}</span><span class="ms-2">${item.country_name || '-'}</span>` : '<span class="text-muted">-</span>'}</div></td>
                </tr>`;
            
            case 'vendors':
                let logoHtml = item.logo 
                    ? `<img src="${item.logo}" alt="" style="max-width: 80px; max-height: 60px; object-fit: contain; border-radius: 8px; border: 1px solid #e3e6f0; padding: 5px;">`
                    : `<div style="width: 80px; height: 60px; background-color: #f8f9fc; border: 1px solid #e3e6f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;"><i class="uil uil-store" style="font-size: 24px; color: #d1d3e2;"></i></div>`;
                return `<tr>
                    <td class="text-center"><div class="userDatatable-content"><span class="id-badge">${item.id}</span></div></td>
                    <td class="text-center"><div class="userDatatable-content">${logoHtml}</div></td>
                    <td><div class="userDatatable-content">${item.name_en || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.name_ar || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.email || '-'}</div></td>
                    <td><div class="userDatatable-content">${item.phone || '-'}</div></td>
                </tr>`;
            
            default:
                return '';
        }
    }

    // Render pagination
    function renderPagination(tab, data, container) {
        const { total, current_page, last_page } = data;
        const perPage = 20;
        const from = (current_page - 1) * perPage + 1;
        const to = Math.min(current_page * perPage, total);

        let html = `<div class="pagination-info">${translations.showing} ${from}-${to} ${translations.of} ${total} ${translations.entries}</div>`;
        
        if (last_page > 1) {
            html += '<nav><ul class="pagination pagination-sm mb-0">';
            
            // Previous button
            html += `<li class="page-item ${current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current_page - 1}">${translations.previous}</a>
            </li>`;
            
            // Page numbers
            let startPage = Math.max(1, current_page - 2);
            let endPage = Math.min(last_page, current_page + 2);
            
            if (startPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            
            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            }
            
            if (endPage < last_page) {
                if (endPage < last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${last_page}">${last_page}</a></li>`;
            }
            
            // Next button
            html += `<li class="page-item ${current_page === last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current_page + 1}">${translations.next}</a>
            </li>`;
            
            html += '</ul></nav>';
        }
        
        container.innerHTML = html;
        
        // Add click handlers for pagination
        container.querySelectorAll('.page-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page >= 1 && page <= last_page) {
                    loadTabData(tab, page);
                }
            });
        });
    }

    // Tab change handler
    document.querySelectorAll('#catalogTabs .nav-link').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const tabName = this.dataset.tab;
            state.currentTab = tabName;
            if (!state.loaded[tabName] || state.search) {
                loadTabData(tabName, state.pages[tabName]);
            }
        });
    });

    // Search handler with debounce
    document.getElementById('globalSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            state.search = this.value.trim();
            // Reset all loaded states and reload current tab
            Object.keys(state.loaded).forEach(key => state.loaded[key] = false);
            Object.keys(state.pages).forEach(key => state.pages[key] = 1);
            loadTabData(state.currentTab, 1);
        }, 300);
    });

    // Load initial tab
    loadTabData('departments');
});
</script>
@endpush
@endsection
