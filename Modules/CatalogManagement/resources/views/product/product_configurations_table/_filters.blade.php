{{-- Product Filters --}}
<div class="mb-25">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                {{-- Search --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="search" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                        </label>
                        <input type="text"
                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="search"
                            placeholder="{{ __('common.search') }}"
                            autocomplete="off">
                    </div>
                </div>

                {{-- Vendor Filter (Admin Only) --}}
                @if($isAdmin)
                <div class="col-md-3">
                    <x-custom-select
                        name="vendor_filter"
                        id="vendor_filter"
                        :label="__('catalogmanagement::product.vendor')"
                        icon="uil uil-store"
                        :options="$vendors"
                        :selected="request('vendor_id')"
                        :placeholder="__('common.all')"
                    />
                </div>
                @endif

                {{-- Brand Filter --}}
                <div class="col-md-3">
                    <x-custom-select
                        name="brand_filter"
                        id="brand_filter"
                        :label="__('catalogmanagement::product.brand')"
                        icon="uil uil-tag-alt"
                        :options="$brands"
                        :selected="request('brand_id')"
                        :placeholder="__('common.all')"
                    />
                </div>

                {{-- Department Filter --}}
                <div class="col-md-3">
                    <x-custom-select
                        name="department_filter"
                        id="department_filter"
                        :label="__('catalogmanagement::product.department')"
                        icon="uil uil-tag-alt"
                        :options="$departments"
                        :selected="request('department_id')"
                        :placeholder="__('common.all')"
                    />
                </div>

                {{-- Category Filter --}}
                <div class="col-md-3">
                    <x-custom-select
                        name="category_filter"
                        id="category_filter"
                        :label="__('catalogmanagement::product.category')"
                        icon="uil uil-folder"
                        :options="[]"
                        :selected="request('category_id')"
                        :placeholder="__('common.all')"
                    />
                </div>

                {{-- Product Type Filter --}}
                <div class="col-md-3">
                    <x-custom-select
                        name="product_type"
                        id="product_type"
                        :label="__('catalogmanagement::product.product_type')"
                        icon="uil uil-layers"
                        :options="$filterOptions['productType']"
                        :selected="request('product_type')"
                        :placeholder="__('common.all')"
                    />
                </div>

                {{-- Configuration Filter --}}
                <div class="col-md-3">
                    <x-custom-select
                        name="configuration_filter"
                        id="configuration_filter"
                        :label="__('catalogmanagement::product.configuration') ?? 'Configuration'"
                        icon="uil uil-package"
                        :options="$filterOptions['configuration']"
                        :selected="request('configuration')"
                        :placeholder="__('common.all')"
                    />
                </div>

                {{-- Active Status Filter --}}
                <div class="col-md-3">
                    <x-custom-select
                        name="active"
                        id="active"
                        :label="__('common.active_status')"
                        icon="uil uil-check-circle"
                        :options="$filterOptions['activeStatus']"
                        :selected="request('active')"
                        :placeholder="__('common.all')"
                    />
                </div>

                {{-- Stock Filter --}}
                <div class="col-md-3">
                    <x-custom-select
                        name="stock_filter"
                        id="stock_filter"
                        :label="__('catalogmanagement::product.stock_status') ?? 'Stock Status'"
                        icon="uil uil-box"
                        :options="$filterOptions['stockStatus']"
                        :selected="request('stock')"
                        :placeholder="__('common.all')"
                    />
                </div>

                {{-- Status Filter (if not filtered by status) --}}
                @if(!isset($statusFilter))
                <div class="col-md-3">
                    <x-custom-select
                        name="status"
                        id="status"
                        :label="__('catalogmanagement::product.approval_status')"
                        icon="uil uil-file-check"
                        :options="$filterOptions['approvalStatus']"
                        :selected="request('status')"
                        :placeholder="__('common.all')"
                    />
                </div>
                @endif

                {{-- Date Filters --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-calendar-alt me-1"></i>
                            {{ __('common.created_date_from') }}
                        </label>
                        <input type="date"
                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="created_date_from">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-calendar-alt me-1"></i>
                            {{ __('common.created_date_to') }}
                        </label>
                        <input type="date"
                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="created_date_to">
                    </div>
                </div>

                @if(isAdmin())
                {{-- Sort By Filter --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sort_column" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-sort me-1"></i>
                            {{ __('common.sort_by') ?? 'Sort By' }}
                        </label>
                        <select class="form-control form-select ih-medium ip-gray radius-xs b-light px-15" id="sort_column">
                            <option value="sort_number" selected>{{ __('common.sort_number') ?? 'Sort Number' }}</option>
                            <option value="created_at">{{ __('common.created_at') ?? 'Created At' }}</option>
                        </select>
                    </div>
                </div>

                {{-- Sort Direction Filter --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sort_direction" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-sort-amount-up me-1"></i>
                            {{ __('common.sort_direction') ?? 'Sort Direction' }}
                        </label>
                        <select class="form-control form-select ih-medium ip-gray radius-xs b-light px-15" id="sort_direction">
                            <option value="asc" selected>{{ __('common.ascending') ?? 'Ascending' }}</option>
                            <option value="desc">{{ __('common.descending') ?? 'Descending' }}</option>
                        </select>
                    </div>
                </div>
                @endif

                {{-- Action Buttons --}}
                <div class="col-md-12 d-flex align-items-center gap-2">
                    {{-- Per Page Selector --}}
                    <div class="d-flex align-items-center">
                        <label class="me-1 mb-0 text-muted" style="font-size: 13px;">{{ __('common.show') }}:</label>
                        <div style="width: 70px;">
                            <x-custom-select
                                name="per_page_filter"
                                id="per_page_filter"
                                class="form-select-sm"
                                :options="$filterOptions['perPage']"
                                :selected="'10'"
                                :placeholder="''"
                            />
                        </div>
                        <span class="ms-1 mb-0 text-muted" style="font-size: 13px;">{{ __('common.entries') }}</span>
                    </div>
                    
                    <div class="d-flex gap-1">
                        <button type="button" id="searchBtn"
                            class="btn btn-success btn-default btn-squared"
                            title="{{ __('common.search') }}">
                            <i class="uil uil-search me-1"></i>
                            {{ __('common.search') }}
                        </button>
                        <button type="button" id="resetFilters"
                            class="btn btn-warning btn-default btn-squared"
                            title="{{ __('common.reset') }}">
                            <i class="uil uil-redo me-1"></i>
                            {{ __('common.reset_filters') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
