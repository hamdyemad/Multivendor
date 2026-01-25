@props([
    'title' => '',
    'icon' => 'uil uil-list-ul',
    'createRoute' => null,
    'createText' => null,
    'showExport' => false,
    'exportText' => null,
    'tableId' => 'dataTable',
    'additionalButtons' => null,
    'ajaxUrl' => '',
    'columnsJson' => '[]',
    'headers' => [],
    'customSelectIds' => [],
    'order' => [[0, 'desc']],
    'pageLength' => 10,
    'customScript' => null, // New: Custom JavaScript to inject
    'additionalAjaxData' => null, // New: Additional ajax data function
])

<div class="row">
    <div class="col-lg-12">
        <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-25">
                <h4 class="mb-0 fw-600 text-primary">
                    <i class="{{ $icon }} me-2"></i>
                    {{ $title }}
                </h4>
                @if($createRoute || $showExport || $additionalButtons)
                <div class="d-flex gap-2">
                    @if($showExport)
                    <button type="button" id="exportBtn" class="btn btn-info btn-squared shadow-sm px-4">
                        <i class="uil uil-download-alt"></i> {{ $exportText ?? trans('common.export_excel') }}
                    </button>
                    @endif
                    
                    {{-- Additional Buttons Slot --}}
                    @if($additionalButtons)
                        {{ $additionalButtons }}
                    @endif
                    
                    @if($createRoute)
                    <a href="{{ $createRoute }}" class="btn btn-primary btn-squared shadow-sm px-4">
                        <i class="uil uil-plus"></i> {{ $createText ?? trans('common.add') }}
                    </a>
                    @endif
                </div>
                @endif
            </div>

            {{-- Filters Slot --}}
            @if(isset($filters))
                {{ $filters }}
            @endif

            {{-- DataTable --}}
            <div class="table-responsive">
                <table id="{{ $tableId }}" class="table mb-0 table-bordered table-hover" style="width:100%">
                    @if(!empty($headers))
                        {{-- Generate headers from array --}}
                        <thead>
                            <tr class="userDatatable-header">
                                @foreach($headers as $header)
                                    <th class="{{ $header['class'] ?? '' }}" @if(isset($header['style'])) style="{{ $header['style'] }}" @endif>
                                        @if(isset($header['raw']) && $header['raw'])
                                            {!! $header['label'] !!}
                                        @else
                                            <span class="userDatatable-title">{{ $header['label'] }}</span>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody></tbody>
                    @else
                        {{-- Use slot for custom headers --}}
                        {{ $slot }}
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

{{-- DataTable Script (if ajaxUrl is provided) --}}
@if($ajaxUrl)
@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize all Custom Select dropdowns
        const customSelectIds = @json($customSelectIds);
        customSelectIds.forEach(function(id) {
            if (document.getElementById(id) && typeof CustomSelect !== 'undefined') {
                CustomSelect.init(id);
            }
        });

        // Flag to prevent double reload during initialization
        let isInitializing = true;

        // Add change handlers for all custom selects
        customSelectIds.forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function(e) {
                    if (!isInitializing) {
                        table.ajax.reload();
                    }
                });
            }
        });

        // Set initializing to false after a short delay
        setTimeout(function() {
            isInitializing = false;
        }, 500);

        // Populate filters from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
        if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
        if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

        // Parse columns - check if columns are defined in custom script or passed as JSON
        let columns;
        if (typeof window.datatableColumns !== 'undefined') {
            // Columns defined in custom script
            columns = window.datatableColumns;
        } else if ('{{ $columnsJson }}' === 'COLUMNS_DEFINED_IN_SCRIPT') {
            // Columns will be defined in custom script below
            columns = [];
        } else {
            // Parse columns from JSON
            let columnsJson = `{!! $columnsJson !!}`;
            
            // Evaluate the JSON string to get the columns array
            columns = eval('(' + columnsJson + ')');
        }

        // Initialize DataTable
        let table = $('#{{ $tableId }}').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: '{{ $ajaxUrl }}',
                data: function(d) {
                    d.search = $('#search').val();
                    d.created_date_from = $('#created_date_from').val();
                    d.created_date_to = $('#created_date_to').val();
                    
                    // Get per_page from per_page_filter if exists, otherwise use default
                    if (typeof CustomSelect !== 'undefined' && document.getElementById('per_page_filter')) {
                        d.per_page = CustomSelect.getValue('per_page_filter') || {{ $pageLength }};
                    } else {
                        d.per_page = $('#entriesSelect').val() || {{ $pageLength }};
                    }

                    // Add custom select values
                    customSelectIds.forEach(function(id) {
                        if (typeof CustomSelect !== 'undefined' && document.getElementById(id)) {
                            d[id] = CustomSelect.getValue(id);
                        }
                    });

                    @if($additionalAjaxData)
                        // Additional ajax data from custom script
                        {!! $additionalAjaxData !!}
                    @endif
                }
            },
            columns: columns,
            pageLength: {{ $pageLength }},
            lengthMenu: [10, 25, 50, 100],
            order: @json($order),
            language: {
                lengthMenu: "{{ __('common.show') ?? 'Show' }} _MENU_",
                info: "{{ __('common.showing') ?? 'Showing' }} _START_ {{ __('common.to') ?? 'to' }} _END_ {{ __('common.of') ?? 'of' }} _TOTAL_ {{ __('common.entries') ?? 'entries' }}",
                infoEmpty: "{{ __('common.showing') ?? 'Showing' }} 0 {{ __('common.to') ?? 'to' }} 0 {{ __('common.of') ?? 'of' }} 0 {{ __('common.entries') ?? 'entries' }}",
                infoFiltered: "({{ __('common.filtered_from') ?? 'filtered from' }} _MAX_ {{ __('common.total_entries') ?? 'total entries' }})",
                zeroRecords: "{{ __('common.no_records_found') ?? 'No records found' }}",
                emptyTable: "{{ __('common.no_records_found') ?? 'No records found' }}",
                loadingRecords: "{{ __('common.loading') ?? 'Loading' }}...",
                processing: "{{ __('common.processing') ?? 'Processing' }}...",
                search: "{{ __('common.search') ?? 'Search' }}:",
                paginate: {
                    @if(app()->getLocale() == 'en')
                        first: '<i class="uil uil-angle-double-left"></i>',
                        last: '<i class="uil uil-angle-double-right"></i>',
                        next: '<i class="uil uil-angle-right"></i>',
                        previous: '<i class="uil uil-angle-left"></i>'
                    @else
                        first: '<i class="uil uil-angle-double-right"></i>',
                        last: '<i class="uil uil-angle-double-left"></i>',
                        next: '<i class="uil uil-angle-left"></i>',
                        previous: '<i class="uil uil-angle-right"></i>'
                    @endif
                },
                aria: {
                    sortAscending: ": {{ __('common.sort_ascending') ?? 'activate to sort column ascending' }}",
                    sortDescending: ": {{ __('common.sort_descending') ?? 'activate to sort column descending' }}"
                }
            },
            dom: '<"row"<"col-sm-12"tr>><"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        });

        // Per page filter change handler (if exists)
        const perPageEl = document.getElementById('per_page_filter');
        if (perPageEl) {
            perPageEl.addEventListener('change', function(e) {
                const perPage = e.detail ? e.detail.value : (typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('per_page_filter') : {{ $pageLength }});
                table.page.len(parseInt(perPage)).draw();
            });
        }

        // Search button click
        $('#searchBtn').on('click', function() {
            const params = new URLSearchParams();
            const search = $('#search').val();
            const dateFrom = $('#created_date_from').val();
            const dateTo = $('#created_date_to').val();

            if (search) params.set('search', search);
            if (dateFrom) params.set('created_date_from', dateFrom);
            if (dateTo) params.set('created_date_to', dateTo);

            // Add custom select values to URL
            customSelectIds.forEach(function(id) {
                if (typeof CustomSelect !== 'undefined' && document.getElementById(id)) {
                    const value = CustomSelect.getValue(id);
                    if (value) params.set(id, value);
                }
            });

            const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
            window.history.pushState({}, '', newUrl);

            table.ajax.reload();
        });

        // Live search
        let searchTimer;
        $('#search').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => table.ajax.reload(), 600);
        });

        // Date filters change handler
        $('#created_date_from, #created_date_to').on('change', () => table.ajax.reload());

        // Reset filters
        $('#resetFilters').on('click', function() {
            window.history.pushState({}, '', window.location.pathname);
            $('#search, #created_date_from, #created_date_to').val('');

            customSelectIds.forEach(function(id) {
                if (document.getElementById(id) && typeof CustomSelect !== 'undefined') {
                    CustomSelect.clear(id);
                }
            });

            // Reset per page to default (10)
            if (document.getElementById('per_page_filter') && typeof CustomSelect !== 'undefined') {
                CustomSelect.setValue('per_page_filter', '10');
            }

            table.ajax.reload();
        });

        // RTL Support
        if ($('html').attr('dir') === 'rtl') {
            $('.dataTables_wrapper').addClass('text-end');
        }

        @if($customScript)
            // Custom JavaScript from parent view
            {!! $customScript !!}
        @endif
    });
</script>

{{-- Loading Overlay --}}
<x-loading-overlay />

@endpush
@endif
