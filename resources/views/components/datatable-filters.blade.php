@props([
    'searchPlaceholder' => 'Search...',
    'showActiveFilter' => true,
    'activeOptions' => [],
    'additionalFilters' => null,
    'exportTitle' => 'Export Excel',
    'resetTitle' => 'Reset Filters',
])

<div class="alert alert-info glowing-alert" role="alert">
    {{ __('common.live_search_info') }}
</div>

{{-- Search and Filter --}}
<div class="mb-25">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                {{-- Search Input --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="search" class="il-gray fs-14 fw-500 mb-10">
                            {{ __('common.search') }}
                        </label>
                        <input type="text"
                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                               id="search"
                               placeholder="{{ $searchPlaceholder }}"
                               autocomplete="off">
                    </div>
                </div>

                {{-- Active Filter --}}
                @if($showActiveFilter)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                {{ $activeOptions['label'] ?? __('common.status') }}
                            </label>
                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                    id="active">
                                <option value="">{{ $activeOptions['all'] ?? __('common.all') }}</option>
                                <option value="1">{{ $activeOptions['active'] ?? __('common.active') }}</option>
                                <option value="0">{{ $activeOptions['inactive'] ?? __('common.inactive') }}</option>
                            </select>
                        </div>
                    </div>
                @endif

                {{-- Additional Filters Slot --}}
                @if($additionalFilters)
                    {{ $additionalFilters }}
                @endif

                {{-- Date From --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                            {{ __('common.created_date_from') }}
                        </label>
                        <input type="date"
                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                               id="created_date_from">
                    </div>
                </div>

                {{-- Date To --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                            {{ __('common.created_date_to') }}
                        </label>
                        <input type="date"
                               class="form-control ih-medium ip-gray radius-xs b-light px-15"
                               id="created_date_to">
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="col-md-12 d-flex align-items-center">
                    <button type="button" id="exportExcel"
                            class="btn btn-primary btn-default btn-squared me-1"
                            title="{{ __('common.excel') }}">
                        <i class="uil uil-file-download-alt me-1"></i> {{ __('common.export_excel') }}
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

{{-- Entries Per Page Selector --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <label class="me-2 mb-0">{{ __('common.show') }}</label>
        <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <label class="ms-2 mb-0">{{ __('common.entries') }}</label>
    </div>
</div>
