@props([
    'filters' => [],
    'searchPlaceholder' => null,
    'showDateFilters' => true,
    'showSearchButton' => true,
    'showResetButton' => true,
    'showExportButton' => false,
    'additionalContent' => null,
])

<div class="mb-25">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                {{-- Search Input --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="search" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                        </label>
                        <input type="text"
                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="search"
                            placeholder="{{ $searchPlaceholder ?? __('common.search') }}"
                            autocomplete="off">
                    </div>
                </div>

                {{-- Dynamic Filters --}}
                @foreach($filters as $filter)
                    <div class="col-md-3">
                        <x-custom-select
                            :name="$filter['name']"
                            :id="$filter['id']"
                            :label="$filter['label']"
                            :icon="$filter['icon'] ?? 'uil uil-filter'"
                            :options="$filter['options'] ?? []"
                            :selected="$filter['selected'] ?? ''"
                            :placeholder="$filter['placeholder'] ?? __('common.all')"
                        />
                    </div>
                @endforeach

                {{-- Date Filters --}}
                @if($showDateFilters)
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
                @endif

                {{-- Additional Content Slot --}}
                @if($additionalContent)
                    {{ $additionalContent }}
                @endif

                {{-- Action Buttons --}}
                <div class="col-md-12 d-flex align-items-center">
                    @if($showSearchButton)
                    <button type="button" id="searchBtn"
                        class="btn btn-success btn-default btn-squared me-1"
                        title="{{ __('common.search') }}">
                        <i class="uil uil-search me-1"></i>
                        {{ __('common.search') }}
                    </button>
                    @endif
                    
                    @if($showResetButton)
                    <button type="button" id="resetFilters"
                        class="btn btn-warning btn-default btn-squared me-1"
                        title="{{ __('common.reset') }}">
                        <i class="uil uil-redo me-1"></i>
                        {{ __('common.reset_filters') }}
                    </button>
                    @endif
                    
                    @if($showExportButton)
                    <button type="button" id="exportExcel"
                        class="btn btn-info btn-default btn-squared"
                        title="{{ __('common.export_excel') }}">
                        <i class="uil uil-file-download-alt me-1"></i>
                        {{ __('common.export_excel') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
