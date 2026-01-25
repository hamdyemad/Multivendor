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
    'enableSorting' => false, // New: Enable drag & drop sorting
    'sortUpdateUrl' => null, // New: URL to update sort order
    'sortPermission' => null, // New: Permission required for sorting
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
                @if($enableSorting)
                <div class="reorder-info" id="reorderInfo-{{ $tableId }}">
                    <i class="uil uil-info-circle me-2"></i>
                    {{ __('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.' }}
                </div>
                @endif
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

{{-- Sorting Styles (if enabled) --}}
@if($enableSorting)
@push('styles')
<style>
    #{{ $tableId }} tbody tr {
        cursor: default;
    }
    #{{ $tableId }} tbody tr.ui-sortable-helper {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        cursor: grabbing;
    }
    #{{ $tableId }} tbody tr.ui-sortable-placeholder {
        border: 2px dashed #2196f3 !important;
        visibility: visible !important;
        height: 50px;
    }
    .drag-handle {
        cursor: grab;
        color: #6c757d;
        padding: 10px 15px;
        font-size: 18px;
        display: block;
        width: 100%;
        height: 100%;
    }
    .drag-handle:hover {
        color: #495057;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .drag-disabled .drag-handle {
        cursor: not-allowed !important;
        opacity: 0.3;
    }
    .reorder-info {
        border: 1px solid #ffc107;
        border-radius: 5px;
        padding: 10px 15px;
        margin-bottom: 15px;
        display: none;
    }
    .reorder-info.show {
        display: block;
    }
</style>
<!-- jQuery UI for Sortable -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush
@endif

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

        // Populate filters from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
        if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
        if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

        // Populate custom selects from URL parameters
        customSelectIds.forEach(function(id) {
            if (urlParams.has(id) && typeof CustomSelect !== 'undefined' && document.getElementById(id)) {
                const value = urlParams.get(id);
                if (value) {
                    CustomSelect.setValue(id, value);
                }
            }
        });

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
                    
                    console.log('AJAX Data - Dates:', {
                        created_date_from: d.created_date_from,
                        created_date_to: d.created_date_to
                    });
                    
                    // Get per_page from per_page_filter if exists, otherwise use default
                    if (typeof CustomSelect !== 'undefined' && document.getElementById('per_page_filter')) {
                        d.per_page = CustomSelect.getValue('per_page_filter') || {{ $pageLength }};
                    } else {
                        d.per_page = $('#entriesSelect').val() || {{ $pageLength }};
                    }

                    // Add sort parameters if sorting is enabled
                    @if($enableSorting)
                    d.sort_column = $('#sort_column').val() || 'sort_number';
                    d.sort_direction = $('#sort_direction').val() || 'asc';
                    @endif

                    // Add custom select values
                    customSelectIds.forEach(function(id) {
                        if (typeof CustomSelect !== 'undefined' && document.getElementById(id)) {
                            const value = CustomSelect.getValue(id);
                            // Map filter IDs to backend parameter names
                            const paramName = id.replace('_filter', '_id').replace('configuration_id', 'configuration').replace('stock_id', 'stock').replace('vendor_id', 'vendor_id');
                            d[paramName] = value;
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

        // Helper function to update URL with all current filter values and reload table
        function updateURLAndReload() {
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
        }

        // Add change handlers for all custom selects (except per_page_filter)
        customSelectIds.forEach(function(id) {
            if (id !== 'per_page_filter') {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', function(e) {
                        updateURLAndReload();
                    });
                }
            }
        });

        // Per page filter change handler (if exists)
        const perPageEl = document.getElementById('per_page_filter');
        if (perPageEl) {
            perPageEl.addEventListener('change', function(e) {
                const perPage = e.detail ? e.detail.value : (typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('per_page_filter') : {{ $pageLength }});
                table.page.len(parseInt(perPage)).draw();
                updateURLAndReload();
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
            searchTimer = setTimeout(() => updateURLAndReload(), 600);
        });

        // Date filters change handler
        $('#created_date_from, #created_date_to').on('change', function() {
            console.log('Date filter changed:', $(this).attr('id'), $(this).val());
            updateURLAndReload();
        });

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

        @if($enableSorting)
        // Drag & Drop Sorting Functionality
        let dragDropEnabled = true;

        // Sort filter change handlers
        $('#sort_column, #sort_direction').on('change', function() {
            updateURLAndReload();
            updateDragDropState();
        });

        // Function to update drag and drop state based on sort filters
        function updateDragDropState() {
            const sortColumn = $('#sort_column').val();
            const sortDirection = $('#sort_direction').val();
            dragDropEnabled = (sortColumn === 'sort_number' && sortDirection === 'asc');
            
            if (dragDropEnabled) {
                $('#{{ $tableId }} tbody').removeClass('drag-disabled');
                $('.drag-handle').css('opacity', '1').css('cursor', 'grab');
                $('#reorderInfo-{{ $tableId }}').removeClass('show').html('<i class="uil uil-info-circle me-2"></i>{{ __('common.drag_drop_info') ?? 'Drag and drop rows to reorder. Changes will be saved automatically.' }}');
            } else {
                $('#{{ $tableId }} tbody').addClass('drag-disabled');
                $('.drag-handle').css('opacity', '0.3').css('cursor', 'not-allowed');
                $('#reorderInfo-{{ $tableId }}').addClass('show').html('<i class="uil uil-exclamation-triangle me-2"></i>{{ __('common.drag_drop_disabled_info') ?? 'Drag and drop is only available when sorting by Sort Number (Ascending).' }}');
            }
        }

        // Initialize jQuery UI Sortable
        @if($sortPermission)
            @can($sortPermission)
                initSortable();
            @endcan
        @else
            initSortable();
        @endif

        function initSortable() {
            // Load jQuery UI if not already loaded
            if (typeof $.ui === 'undefined' || typeof $.ui.sortable === 'undefined') {
                $.getScript('https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', function() {
                    setupSortable();
                    updateDragDropState();
                });
            } else {
                setupSortable();
                updateDragDropState();
            }
        }

        function setupSortable() {
            const $tbody = $('#{{ $tableId }} tbody');
            
            // Destroy existing sortable if any
            if ($tbody.hasClass('ui-sortable')) {
                $tbody.sortable('destroy');
            }
            
            $tbody.sortable({
                handle: '.drag-handle',
                axis: 'y',
                cursor: 'grabbing',
                opacity: 0.8,
                disabled: !dragDropEnabled,
                helper: function(e, tr) {
                    const $originals = tr.children();
                    const $helper = tr.clone();
                    $helper.children().each(function(index) {
                        $(this).width($originals.eq(index).outerWidth());
                    });
                    return $helper;
                },
                placeholder: 'ui-sortable-placeholder',
                start: function(event, ui) {
                    if (!dragDropEnabled) {
                        return false;
                    }
                    ui.placeholder.height(ui.item.outerHeight());
                    const colCount = ui.item.children('td').length;
                    ui.placeholder.html('<td colspan="' + colCount + '" style="background-color: #e3f2fd; border: 2px dashed #2196f3;">&nbsp;</td>');
                },
                update: function(event, ui) {
                    if (!dragDropEnabled) {
                        return false;
                    }

                    // Get new order
                    const items = [];
                    $('#{{ $tableId }} tbody tr').each(function(index) {
                        const id = $(this).find('.drag-handle').data('id');
                        if (id) {
                            items.push({
                                id: id,
                                sort_number: index + 1
                            });
                        }
                    });

                    console.log('Reorder items:', items);

                    if (items.length > 0) {
                        // Show loading overlay
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.show({
                                text: '{{ __('common.saving') ?? 'Saving' }}...',
                                subtext: '{{ __('common.please_wait') ?? 'Please wait' }}...'
                            });
                        }

                        // Send AJAX request to update order
                        @if($sortUpdateUrl)
                        $.ajax({
                            url: '{{ $sortUpdateUrl }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                items: items
                            },
                            success: function(response) {
                                if (typeof LoadingOverlay !== 'undefined') {
                                    LoadingOverlay.hide();
                                }
                                
                                if (response.success) {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: '{{ __('common.success') ?? 'Success' }}',
                                            text: response.message || '{{ __('common.sort_updated') ?? 'Sort order updated successfully' }}',
                                            timer: 2000,
                                            showConfirmButton: false,
                                            toast: true,
                                            position: 'top-end'
                                        });
                                    }
                                    // Reload table to show updated sort numbers
                                    table.ajax.reload(null, false);
                                } else {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: '{{ __('common.error') ?? 'Error' }}',
                                            text: response.message || '{{ __('common.reorder_error') ?? 'Failed to update order' }}'
                                        });
                                    }
                                    table.ajax.reload(null, false);
                                }
                            },
                            error: function(xhr) {
                                if (typeof LoadingOverlay !== 'undefined') {
                                    LoadingOverlay.hide();
                                }
                                
                                let errorMessage = '{{ __('common.reorder_error') ?? 'Failed to update order' }}';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('common.error') ?? 'Error' }}',
                                        text: errorMessage
                                    });
                                }
                                table.ajax.reload(null, false);
                            }
                        });
                        @endif
                    }
                }
            });
            
            console.log('Sortable initialized');
        }

        // Re-initialize sortable after table draw
        table.on('draw', function() {
            setTimeout(function() {
                if (typeof $.ui !== 'undefined' && typeof $.ui.sortable !== 'undefined') {
                    setupSortable();
                    updateDragDropState();
                }
            }, 100);
        });
        @endif

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
