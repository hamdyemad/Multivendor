@props([
    'tableId' => 'dataTable',
    'ajaxUrl' => '',
    'columnsJson' => '[]',
    'order' => [[0, 'desc']],
    'customSelectIds' => [],
    'additionalData' => [],
    'pageLength' => 10,
])

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

        // Parse columns (will be evaluated as JavaScript)
        const columns = {!! $columnsJson !!};

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
                    d.per_page = $('#entriesSelect').val() || {{ $pageLength }};

                    // Add custom select values
                    customSelectIds.forEach(function(id) {
                        if (typeof CustomSelect !== 'undefined' && document.getElementById(id)) {
                            d[id] = CustomSelect.getValue(id);
                        }
                    });

                    // Add additional data
                    @foreach($additionalData as $key => $value)
                        d.{{ $key }} = '{{ $value }}';
                    @endforeach
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

            table.ajax.reload();
        });

        // RTL Support
        if ($('html').attr('dir') === 'rtl') {
            $('.dataTables_wrapper').addClass('text-end');
        }
    });
</script>
