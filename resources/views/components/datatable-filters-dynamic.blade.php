@props([
    'filters' => [],
    'showDateFilters' => true,
    'showButtons' => true,
    'showEntriesSelector' => true,
    'customLayout' => false,
])

{{-- Info Alert --}}
<div class="alert alert-info glowing-alert" role="alert">
    {{ __('common.live_search_info') }}
</div>

{{-- Search and Filter --}}
<div class="mb-25">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                
                {{-- Dynamic Filters --}}
                @foreach($filters as $filter)
                    @if($filter['type'] === 'search')
                        {{-- Search Input --}}
                        <div class="{{ $filter['col'] ?? 'col-md-3' }}">
                            <div class="form-group">
                                <label for="{{ $filter['id'] }}" class="il-gray fs-14 fw-500 mb-10">
                                    @if(isset($filter['icon']))<i class="{{ $filter['icon'] }} me-1"></i>@endif
                                    {{ $filter['label'] }}
                                </label>
                                <input type="text"
                                       class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                       id="{{ $filter['id'] }}"
                                       placeholder="{{ $filter['placeholder'] ?? '' }}"
                                       autocomplete="off">
                            </div>
                        </div>

                    @elseif($filter['type'] === 'select')
                        {{-- Select Dropdown --}}
                        <div class="{{ $filter['col'] ?? 'col-md-3' }}">
                            <div class="form-group">
                                <label for="{{ $filter['id'] }}" class="il-gray fs-14 fw-500 mb-10">
                                    @if(isset($filter['icon']))<i class="{{ $filter['icon'] }} me-1"></i>@endif
                                    {{ $filter['label'] }}
                                </label>
                                <select class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                        id="{{ $filter['id'] }}">
                                    <option value="">{{ $filter['allOption'] ?? __('common.all') }}</option>
                                    @if(isset($filter['options']))
                                        @foreach($filter['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                    @elseif($filter['type'] === 'date')
                        {{-- Date Input --}}
                        <div class="{{ $filter['col'] ?? 'col-md-3' }}">
                            <div class="form-group">
                                <label for="{{ $filter['id'] }}" class="il-gray fs-14 fw-500 mb-10">
                                    @if(isset($filter['icon']))<i class="{{ $filter['icon'] }} me-1"></i>@endif
                                    {{ $filter['label'] }}
                                </label>
                                <input type="date"
                                       class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                       id="{{ $filter['id'] }}">
                            </div>
                        </div>

                    @elseif($filter['type'] === 'custom')
                        {{-- Custom HTML --}}
                        <div class="{{ $filter['col'] ?? 'col-md-3' }}">
                            {!! $filter['html'] !!}
                        </div>
                    @endif
                @endforeach

                {{-- Default Date Range Filters --}}
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

                {{-- Action Buttons --}}
                @if($showButtons)
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
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Entries Per Page Selector --}}
@if($showEntriesSelector)
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
@endif
