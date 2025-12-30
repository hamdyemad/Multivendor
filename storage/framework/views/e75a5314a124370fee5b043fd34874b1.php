

<?php $__env->startSection('title'); ?>
    <?php echo e(__('vendor::vendor.vendors')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
    <!-- Select2 CSS loaded via Vite -->
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => __('vendor::vendor.vendors_management')],
                ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => __('vendor::vendor.vendors_management')],
                ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">

                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500"><?php echo e(__('vendor::vendor.vendors_management')); ?></h4>
                        <div class="d-flex gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendors.create')): ?>
                            <a href="<?php echo e(route('admin.vendors.create')); ?>"
                                class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> <?php echo e(__('vendor::vendor.add_vendor')); ?>

                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="mb-25">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="search" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-search me-1"></i> <?php echo e(__('common.search')); ?>

                                                <small
                                                    class="text-muted">(<?php echo e(__('common.real_time') ?? 'Real-time'); ?>)</small>
                                            </label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="search" placeholder="<?php echo e(__('common.search')); ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="active" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-check-circle me-1"></i>
                                                <?php echo e(__('vendor::vendor.status') ?? 'Status'); ?>

                                            </label>
                                            <select
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 form-select"
                                                id="active">
                                                <option value=""><?php echo e(__('vendor::vendor.all') ?? 'All'); ?></option>
                                                <option value="1"><?php echo e(__('vendor::vendor.active') ?? 'Active'); ?>

                                                </option>
                                                <option value="0"><?php echo e(__('vendor::vendor.inactive') ?? 'Inactive'); ?>

                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                                                <i class="uil uil-calendar-alt me-1"></i>
                                                <?php echo e(__('common.created_date_from')); ?>

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
                                                <?php echo e(__('common.created_date_to')); ?>

                                            </label>
                                            <input type="date"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                id="created_date_to">
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex align-items-center">
                                        <button type="button" id="searchBtn"
                                            class="btn btn-success btn-default btn-squared me-1"
                                            title="<?php echo e(__('common.search')); ?>">
                                            <i class="uil uil-search me-1"></i>
                                            <?php echo e(__('common.search')); ?>

                                        </button>
                                        <button type="button" id="resetFilters"
                                            class="btn btn-warning btn-default btn-squared me-1"
                                            title="<?php echo e(__('common.reset')); ?>">
                                            <i class="uil uil-redo me-1"></i>
                                            <?php echo e(__('common.reset_filters')); ?>

                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <label class="me-2 mb-0"><?php echo e(__('common.show') ?? 'Show'); ?></label>
                            <select id="entriesSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="ms-2 mb-0"><?php echo e(__('common.entries') ?? 'entries'); ?></label>
                        </div>
                    </div>

                    
                    <div class="table-responsive">
                        <table id="vendorsDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="userDatatable-title">#</span></th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('vendor::vendor.vendor_information')); ?></span>
                                    </th>
                                    <th><span
                                            class="userDatatable-title"><?php echo e(__('vendor::vendor.departments') ?? 'Departments'); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(__('vendor::vendor.active_status')); ?></span>
                                    </th>
                                    <th><span class="userDatatable-title"><?php echo e(__('common.actions')); ?></span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <?php if (isset($component)) { $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.delete-modal','data' => ['modalId' => 'modal-delete-vendor','title' => __('vendor::vendor.confirm_delete'),'message' => __('vendor::vendor.delete_confirmation'),'itemNameId' => 'delete-vendor-name','confirmBtnId' => 'confirmDeleteVendorBtn','deleteRoute' => route('admin.vendors.index'),'cancelText' => __('common.cancel') ?? 'Cancel','deleteText' => __('vendor::vendor.delete_vendor')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['modalId' => 'modal-delete-vendor','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('vendor::vendor.confirm_delete')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('vendor::vendor.delete_confirmation')),'itemNameId' => 'delete-vendor-name','confirmBtnId' => 'confirmDeleteVendorBtn','deleteRoute' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.vendors.index')),'cancelText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('common.cancel') ?? 'Cancel'),'deleteText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('vendor::vendor.delete_vendor'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $attributes = $__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__attributesOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd)): ?>
<?php $component = $__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd; ?>
<?php unset($__componentOriginalb7eac87efb73c0c2c26fe03ec80faafd); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-body'); ?>
    <?php if (isset($component)) { $__componentOriginal115e82920da0ed7c897ee494af74b9d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal115e82920da0ed7c897ee494af74b9d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loading-overlay','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('loading-overlay'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal115e82920da0ed7c897ee494af74b9d8)): ?>
<?php $attributes = $__attributesOriginal115e82920da0ed7c897ee494af74b9d8; ?>
<?php unset($__attributesOriginal115e82920da0ed7c897ee494af74b9d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal115e82920da0ed7c897ee494af74b9d8)): ?>
<?php $component = $__componentOriginal115e82920da0ed7c897ee494af74b9d8; ?>
<?php unset($__componentOriginal115e82920da0ed7c897ee494af74b9d8); ?>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            console.log('Vendors page loaded, initializing DataTable...');

            let per_page = 10;

            // Get filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Populate filters from URL parameters on page load
            if (urlParams.has('search')) $('#search').val(urlParams.get('search'));
            if (urlParams.has('active')) $('#active').val(urlParams.get('active'));
            if (urlParams.has('created_date_from')) $('#created_date_from').val(urlParams.get('created_date_from'));
            if (urlParams.has('created_date_to')) $('#created_date_to').val(urlParams.get('created_date_to'));

            // Function to update URL with current filters
            function updateUrlWithFilters() {
                const params = new URLSearchParams();

                if ($('#search').val()) params.set('search', $('#search').val());
                if ($('#active').val()) params.set('active', $('#active').val());
                if ($('#created_date_from').val()) params.set('created_date_from', $('#created_date_from').val());
                if ($('#created_date_to').val()) params.set('created_date_to', $('#created_date_to').val());

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }

            // Server-side processing with pagination
            let table = $('#vendorsDataTable').DataTable({
                processing: true,
                serverSide: true, // Server-side processing
                ajax: {
                    url: '<?php echo e(route('admin.vendors.datatable')); ?>',
                    type: 'GET',
                    data: function(d) {
                        // Map DataTables parameters to backend parameters
                        d.per_page = d.length;
                        d.page = (d.start / d.length) + 1;
                        // Add filter parameters
                        d.search = $('#search').val();
                        d.active = $('#active').val();
                        d.created_date_from = $('#created_date_from').val();
                        d.created_date_to = $('#created_date_to').val();
                        // Add sorting parameters
                        if (d.order && d.order.length > 0) {
                            d.orderColumnIndex = d.order[0].column;
                            d.orderDirection = d.order[0].dir;
                        }
                        console.log('📤 Sending request:', d);
                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('📦 Data received from server:', json);
                        console.log('Total records:', json.total);
                        console.log('Filtered records:', json.recordsFiltered);
                        console.log('Current page:', json.current_page);

                        // Map backend response to DataTables format
                        json.recordsTotal = json.total || json.recordsTotal || 0;
                        json.recordsFiltered = json.recordsFiltered || json.total || 0;

                        if (json.error) {
                            console.error('❌ Server returned error:', json.error);
                            alert('Error: ' + json.error);
                            return [];
                        }
                        if (!json.data || json.data.length === 0) {
                            console.warn('⚠️ No data returned from server');
                        }
                        return json.data || [];
                    },
                    error: function(xhr, error, code) {
                        console.error('❌ DataTables AJAX Error:', {
                            xhr: xhr,
                            error: error,
                            code: code
                        });
                        console.error('Response Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);

                        // Try to parse JSON error
                        let errorMsg = 'Error loading data. Status: ' + xhr.status;
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                console.error('❌ Server Error:', response.error);
                                errorMsg += '\n\nError: ' + response.error;
                                if (response.trace) {
                                    console.error('Stack Trace:', response.trace);
                                }
                            }
                        } catch (e) {
                            console.error('Could not parse error response');
                        }

                        alert(errorMsg + '\n\nCheck console for full details.');
                    }
                },
                columns: [
                    // Hex Number column
                    {
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row) {
                            return row.row_number
                        }
                    },
                    // Vendor Information column (combined)
                    {
                        data: null,
                        name: 'vendor_info',
                        orderable: false,
                        render: function(data, type, row) {
                            console.log('DDDDDDDDDDDDDDDDDDDDDDDDDDDDDd', row);

                            // English Name only
                            const nameEn = row.translations && row.translations['en'] ?
                                row.translations['en'].name :
                                '-';

                            // Vendor Email
                            const email = row.email || '-';

                            // Vendor Logo
                            let logoHtml = '';
                            if (row.logo_url) {
                                logoHtml = `<img src="${row.logo_url}" alt="${$('<div>').text(nameEn).html()}" class="rounded-circle me-3" style="width: 50px; height: 50px;">`;
                            } else {
                                logoHtml = `<div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">${nameEn.charAt(0).toUpperCase()}</div>`;
                            }

                            let html = `
                                <div class="vendor-card p-2 bg-light-subtle rounded-3">
                                    <div class="d-flex align-items-center">
                                        ${logoHtml}
                                        <div class="d-flex flex-column">
                                            <div class="fw-semibold text-dark text-capitalize mb-1">
                                                ${$('<div>').text(nameEn).html()}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="uil uil-envelope me-1"></i>${$('<div>').text(email).html()}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            return html;
                        }
                    },

                    // Departments column
                    {
                        data: 'departments',
                        name: 'departments',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data || !Array.isArray(data) || data.length === 0) {
                                return '<span class="text-muted">-</span>';
                            }

                            const displayLimit = 2;
                            let visibleHtml = '';
                            let hiddenHtml = '';
                            const uniqueId = `depts-${row.id}`;

                            data.forEach((d, index) => {
                                const badge =
                                    `<span class="badge badge-round badge-lg badge-primary mb-1 me-1">${d.name || '-'}</span>`;
                                if (index < displayLimit) {
                                    visibleHtml += badge;
                                } else {
                                    hiddenHtml += badge;
                                }
                            });

                            if (data.length > displayLimit) {
                                const remainingCount = data.length - displayLimit;
                                visibleHtml +=
                                    `<div id="hidden-${uniqueId}" style="display: none; margin-top: 5px;">${hiddenHtml}</div>`;
                                visibleHtml +=
                                    `<a href="javascript:void(0);" class="show-more-depts badge badge-round badge-lg badge-success" data-target="#hidden-${uniqueId}">+${remainingCount} more</a>`;
                            }

                            return `<div class="department-list">${visibleHtml}</div>`;
                        }
                    },

                    // Active Status column
                    {
                        data: 'active',
                        name: 'active',
                        orderable: false,
                        render: function(data, type, row) {
                            let checked = data ? 'checked' : '';
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendors.change-status')): ?>
                            return `<div class="userDatatable-content">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input status-switch" type="checkbox"
                                        data-id="${row.id}" ${checked} style="cursor: pointer; width: 40px; height: 20px;">
                                </div>
                            </div>`;
                            <?php else: ?>
                            return data 
                                ? '<span class="badge badge-round bg-success"><?php echo e(trans("vendor::vendor.active")); ?></span>'
                                : '<span class="badge badge-round bg-danger"><?php echo e(trans("vendor::vendor.inactive")); ?></span>';
                            <?php endif; ?>
                        }
                    },
                    // Actions column
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let viewUrl = "<?php echo e(route('admin.vendors.show', ':id')); ?>".replace(':id',
                                row.id);
                            let editUrl = "<?php echo e(route('admin.vendors.edit', ':id')); ?>".replace(':id',
                                row.id);
                            return `
                            <div class="orderDatatable_actions d-inline-flex gap-1">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendors.show')): ?>
                                <a href="${viewUrl}"
                                class="view btn btn-primary table_action_father"
                                title="<?php echo e(trans('common.view')); ?>">
                                    <i class="uil uil-eye table_action_icon"></i>
                                </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendors.edit')): ?>
                                <a href="${editUrl}"
                                class="edit btn btn-warning table_action_father"
                                title="<?php echo e(trans('common.edit')); ?>">
                                    <i class="uil uil-edit table_action_icon"></i>
                                </a>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vendors.delete')): ?>
                                <a href="javascript:void(0);"
                                class="remove delete-vendor btn btn-danger table_action_father"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-delete-vendor"
                                data-item-id="${row.id}"
                                data-item-name="${$('<div>').text(row.first_name).html()}"
                                title="<?php echo e(trans('common.delete')); ?>">
                                    <i class="uil uil-trash-alt table_action_icon"></i>
                                </a>
                                <?php endif; ?>
                            </div>`;

                        }
                    }
                ],
                pageLength: per_page,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [
                    [0, 'desc']
                ],
                pagingType: 'full_numbers',
                dom: '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    title: '<?php echo e(__('vendor::vendor.vendors_management')); ?>'
                }],
                searching: false, // Disable built-in search (using custom)
                language: {
                    lengthMenu: "<?php echo e(__('common.show') ?? 'Show'); ?> _MENU_",
                    info: "<?php echo e(__('common.showing') ?? 'Showing'); ?> _START_ <?php echo e(__('common.to') ?? 'to'); ?> _END_ <?php echo e(__('common.of') ?? 'of'); ?> _TOTAL_ <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoEmpty: "<?php echo e(__('common.showing') ?? 'Showing'); ?> 0 <?php echo e(__('common.to') ?? 'to'); ?> 0 <?php echo e(__('common.of') ?? 'of'); ?> 0 <?php echo e(__('common.entries') ?? 'entries'); ?>",
                    infoFiltered: "(<?php echo e(__('common.filtered_from') ?? 'filtered from'); ?> _MAX_ <?php echo e(__('common.total_entries') ?? 'total entries'); ?>)",
                    zeroRecords: "<?php echo e(__('vendor::vendor.no_vendors_found') ?? 'No vendors found'); ?>",
                    emptyTable: "<?php echo e(__('vendor::vendor.no_vendors_found') ?? 'No vendors found'); ?>",
                    loadingRecords: "<?php echo e(__('common.loading') ?? 'Loading'); ?>...",
                    processing: "<?php echo e(__('common.processing') ?? 'Processing'); ?>...",
                    search: "<?php echo e(__('common.search') ?? 'Search'); ?>:",
                    aria: {
                        sortAscending: ": <?php echo e(__('common.sort_ascending') ?? 'activate to sort column ascending'); ?>",
                        sortDescending: ": <?php echo e(__('common.sort_descending') ?? 'activate to sort column descending'); ?>"
                    }
                }
            });

            // Initialize Select2 on filter dropdowns
            if ($.fn.select2) {
                $('#entriesSelect, #active').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            } else {
                console.error('Select2 is not loaded');
            }

            // Handle entries select change
            $('#entriesSelect').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Handle Excel export button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Real-time search with debounce
            let searchTimer;
            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    updateUrlWithFilters();
                    table.ajax.reload();
                }, 500);
            });

            // Search button click handler
            $('#searchBtn').on('click', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Filter change handlers - real-time filtering
            $('#active').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Date filter change handlers
            $('#created_date_from, #created_date_to').on('change', function() {
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Reset filters button
            $('#resetFilters').on('click', function() {
                console.log('Resetting all filters...');
                // Clear all filter inputs
                $('#search').val('');
                $('#active').val('').trigger('change');
                $('#created_date_from').val('');
                $('#created_date_to').val('');
                // Update URL and reload table
                updateUrlWithFilters();
                table.ajax.reload();
            });

            // Delete vendor
            $('#confirmDeleteVendorBtn').on('click', function() {
                const vendorId = $(this).data('item-id');
                const deleteUrl = '<?php echo e(route('admin.vendors.index')); ?>/' + vendorId;

                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        $('#modal-delete-vendor').modal('hide');
                        table.ajax.reload();
                        showNotification('success', response.message ||
                            'Vendor deleted successfully');
                    },
                    error: function(xhr) {
                        showNotification('error', xhr.responseJSON?.message ||
                            'Error deleting vendor');
                    }
                });
            });

            // Set delete modal data
            $(document).on('click', '.remove', function() {
                const itemId = $(this).data('item-id');
                const itemName = $(this).data('item-name');
                $('#delete-vendor-name').text(itemName);
                $('#confirmDeleteVendorBtn').data('item-id', itemId);
            });

            // Status switch handler
            // Handle "Show more" for departments
            $('#vendorsDataTable tbody').on('click', '.show-more-depts', function(e) {
                e.preventDefault();
                const $this = $(this);
                const targetSelector = $this.data('target');
                const $target = $(targetSelector);

                $target.slideToggle(200); // A bit of animation

                if ($this.text().includes('more')) {
                    $this.text('Show less');
                } else {
                    const remainingCount = $target.children().length;
                    $this.text(`+${remainingCount} more`);
                }
            });

            // Status switch handler
            $(document).on('change', '.status-switch', function() {
                const $switch = $(this);
                const vendorId = $switch.data('id');
                const originalState = !$switch.is(':checked');

                $.ajax({
                    url: '<?php echo e(route('admin.vendors.change-status', '__id__')); ?>'.replace('__id__',
                        vendorId),
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            }
                        } else {
                            $switch.prop('checked', originalState);
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        $switch.prop('checked', originalState);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('<?php echo e(__('vendor::vendor.error_changing_status')); ?>');
                        }
                    }
                });
            });
        });

        function showNotification(type, message) {
            // Use the global showMessage function from app.blade.php
            if (typeof showMessage === 'function') {
                const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
                showMessage(type, message, icon);
            } else {
                // Fallback to alert if showMessage is not available
                alert(message);
            }
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\eramo-multi-vendor\Modules/Vendor\resources/views/vendors/index.blade.php ENDPATH**/ ?>