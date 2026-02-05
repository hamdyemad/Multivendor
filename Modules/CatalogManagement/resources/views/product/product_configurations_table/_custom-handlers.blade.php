{{-- Product DataTable Custom Event Handlers --}}
<script>
// Define columns for DataTable
window.datatableColumns = [
    @if(isset($showAdminColumns) ? $showAdminColumns : $isAdmin)
    {
        data: null,
        orderable: false,
        searchable: false,
        className: 'text-center',
        width: '40px',
        render: function(data, type, row) {
            return `<div class="drag-handle" data-id="${row.vendor_product_id}" data-sort-number="${row.sort_number || 0}" style="cursor: grab;">
                <i class="uil uil-draggabledots" style="font-size: 20px; color: #666;"></i>
            </div>`;
        }
    },
    @endif
    {
        data: null,
        orderable: false,
        searchable: false,
        className: 'text-center',
        width: '40px',
        render: function(data, type, row) {
            return `<input type="checkbox" class="form-check-input product-checkbox" data-product-id="${row.vendor_product_id}" style="cursor: pointer;">`;
        }
    },
    {
        data: 'index',
        name: 'index',
        orderable: false,
        searchable: false,
        className: 'text-center fw-bold'
    },
    {
        data: 'product_information',
        name: 'product_information',
        orderable: false,
        searchable: true,
        className: 'text-start',
        render: renderProductInformation
    },
    @if(isset($showAdminColumns) ? $showAdminColumns : $isAdmin)
    {
        data: 'vendor',
        name: 'vendor',
        searchable: false,
        orderable: false,
        render: renderVendor
    },
    @endif
    {
        data: 'status',
        name: 'status',
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: renderStatus
    },
    {
        data: 'active',
        name: 'active',
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: renderActivation
    },
    {
        data: null,
        orderable: false,
        searchable: false,
        className: 'text-center',
        render: renderActions
    }
];

$(document).ready(function() {
    // Department change handler - fetch categories dynamically
    const departmentEl = document.getElementById('department_filter');
    if (departmentEl) {
        departmentEl.addEventListener('change', function(e) {
            const departmentId = e.detail ? e.detail.value : (typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('department_filter') : '');
            
            // Get category from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const urlCategoryId = urlParams.get('category_filter');
            
            if (!departmentId) {
                // Clear categories and set empty options
                if (typeof CustomSelect !== 'undefined') {
                    CustomSelect.setOptions('category_filter', [], '{{ __('common.all') }}');
                }
                return;
            }
            
            // Fetch categories for selected department
            $.ajax({
                url: '/api/v1/categories',
                type: 'GET',
                data: {
                    department_id: departmentId,
                    select2: 1
                },
                headers: {
                    'lang': '{{ app()->getLocale() }}',
                    'X-Country-Code': $("meta[name='currency_country_code']").attr('content')
                },
                success: function(response) {
                    const data = response.data || response;
                    if (data && data.length > 0) {
                        // Format options for CustomSelect
                        const options = data.map(function(category) {
                            return { id: category.id, name: category.name };
                        });
                        if (typeof CustomSelect !== 'undefined') {
                            CustomSelect.setOptions('category_filter', options, '{{ __('common.all') }}');
                            
                            // If there's a category in URL, set it
                            if (urlCategoryId) {
                                CustomSelect.setValue('category_filter', urlCategoryId);
                            }
                        }
                    } else {
                        if (typeof CustomSelect !== 'undefined') {
                            CustomSelect.setOptions('category_filter', [], '{{ __('common.all') }}');
                        }
                    }
                },
                error: function(error) {
                    console.error('Error fetching categories:', error);
                    if (typeof CustomSelect !== 'undefined') {
                        CustomSelect.setOptions('category_filter', [], '{{ __('common.all') }}');
                    }
                }
            });
        });
    }

    // Trigger department change on page load if a department is selected (after a short delay to ensure URL params are loaded)
    setTimeout(function() {
        const initialDepartmentId = typeof CustomSelect !== 'undefined' ? CustomSelect.getValue('department_filter') : '';
        if (initialDepartmentId && departmentEl) {
            departmentEl.dispatchEvent(new CustomEvent('change', { detail: { value: initialDepartmentId } }));
        }
    }, 300);

    // Select all checkbox handler
    $('#selectAllProducts').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.product-checkbox').prop('checked', isChecked);
        updateExportButtonState();
    });
    
    // Individual checkbox handler
    $(document).on('change', '.product-checkbox', function() {
        // Update select all checkbox state
        const totalCheckboxes = $('.product-checkbox').length;
        const checkedCheckboxes = $('.product-checkbox:checked').length;
        $('#selectAllProducts').prop('checked', totalCheckboxes === checkedCheckboxes);
        updateExportButtonState();
    });
    
    // Update export button state based on selection
    function updateExportButtonState() {
        const selectedCount = $('.product-checkbox:checked').length;
        const btn = $('#exportBtn');
        
        if (selectedCount > 0) {
            btn.html(`<i class="uil uil-download-alt"></i> {{ __('common.export_excel') }} (${selectedCount})`);
        } else {
            btn.html('<i class="uil uil-download-alt"></i> {{ __('common.export_excel') }}');
        }
    }

    // Export button handler
    $('#exportBtn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        
        // Get selected product IDs
        const selectedIds = [];
        $('.product-checkbox:checked').each(function() {
            selectedIds.push($(this).data('product-id'));
        });
        
        // Show loading overlay
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.show({
                text: '{{ __('catalogmanagement::product.exporting_products') ?? 'Exporting Products' }}...',
                subtext: '{{ __('common.please_wait') }}...',
                progress: true
            });
            
            // Animate progress bar
            LoadingOverlay.progressSequence([20, 40, 60, 80], [300, 500, 700, 900]);
        }
        
        // Disable button
        btn.prop('disabled', true);
        
        // Get current filter values
        const filters = {
            search: $('#search').val() || '',
            vendor_id: (typeof CustomSelect !== 'undefined' && document.getElementById('vendor_filter')) ? CustomSelect.getValue('vendor_filter') : '',
            department_id: (typeof CustomSelect !== 'undefined' && document.getElementById('department_filter')) ? CustomSelect.getValue('department_filter') : '',
            category_id: (typeof CustomSelect !== 'undefined' && document.getElementById('category_filter')) ? CustomSelect.getValue('category_filter') : '',
            brand_id: (typeof CustomSelect !== 'undefined' && document.getElementById('brand_filter')) ? CustomSelect.getValue('brand_filter') : '',
            status: (typeof CustomSelect !== 'undefined' && document.getElementById('status')) ? CustomSelect.getValue('status') : '',
            product_type: (typeof CustomSelect !== 'undefined' && document.getElementById('product_type')) ? CustomSelect.getValue('product_type') : '',
            configuration_type: (typeof CustomSelect !== 'undefined' && document.getElementById('configuration_filter')) ? CustomSelect.getValue('configuration_filter') : '',
            is_active: (typeof CustomSelect !== 'undefined' && document.getElementById('active_status')) ? CustomSelect.getValue('active_status') : '',
            stock_status: (typeof CustomSelect !== 'undefined' && document.getElementById('stock_status')) ? CustomSelect.getValue('stock_status') : '',
            created_from: $('#created_date_from').val() || '',
            created_to: $('#created_date_to').val() || ''
        };
        
        // Only add product_ids if products are selected
        if (selectedIds.length > 0) {
            filters.product_ids = selectedIds.join(',');
        }
        
        // Build query string
        const queryParams = new URLSearchParams();
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                queryParams.append(key, filters[key]);
            }
        });
        
        // Check if this is vendor bank page - use special export route
        @if(isset($isVendorBankPage) && $isVendorBankPage)
        const exportUrl = '{{ route('admin.products.vendor-bank.export') }}' + (queryParams.toString() ? '?' + queryParams.toString() : '');
        @else
        const exportUrl = '{{ route('admin.products.export') }}' + (queryParams.toString() ? '?' + queryParams.toString() : '');
        @endif
        
        // Use XMLHttpRequest for blob download
        const xhr = new XMLHttpRequest();
        xhr.open('GET', exportUrl, true);
        xhr.responseType = 'blob';
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Create blob and download
                const blob = xhr.response;
                const contentDisposition = xhr.getResponseHeader('Content-Disposition');
                let fileName = 'products_export_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                
                if (contentDisposition) {
                    const fileNameMatch = contentDisposition.match(/filename="?(.+)"?/);
                    if (fileNameMatch && fileNameMatch.length === 2) {
                        fileName = fileNameMatch[1];
                    }
                }
                
                // Complete progress bar to 100%
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.animateProgressBar(100, 300);
                }
                
                // Create download link
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                
                // Show success and hide overlay after download starts
                setTimeout(function() {
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.showSuccess(
                            '{{ __('catalogmanagement::product.export_completed') ?? 'Export Completed Successfully' }}',
                            '{{ __('common.downloading') ?? 'Downloading' }}...'
                        );
                    }
                    
                    // Clean up and hide overlay
                    setTimeout(function() {
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        btn.prop('disabled', false);
                    }, 2000);
                }, 500);
            } else {
                // Error handling
                if (typeof LoadingOverlay !== 'undefined') {
                    LoadingOverlay.hide();
                }
                
                toastr.error('{{ __('catalogmanagement::product.export_failed') ?? 'Export failed. Please try again.' }}');
                btn.prop('disabled', false);
            }
        };
        
        xhr.onerror = function() {
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }
            
            toastr.error('{{ __('catalogmanagement::product.export_failed') ?? 'Export failed. Please try again.' }}');
            btn.prop('disabled', false);
        };
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status !== 200) {
                    // Handle error response
                    if (typeof LoadingOverlay !== 'undefined') {
                        LoadingOverlay.hide();
                    }
                    
                    let errorMessage = '{{ __('catalogmanagement::product.export_failed') ?? 'Export failed. Please try again.' }}';
                    
                    // Try to parse error response
                    if (xhr.responseType === 'blob' && xhr.response) {
                        const reader = new FileReader();
                        reader.onload = function() {
                            try {
                                const errorData = JSON.parse(reader.result);
                                if (errorData.message) {
                                    errorMessage = errorData.message;
                                }
                                if (errorData.error) {
                                    errorMessage += '\n' + errorData.error;
                                }
                            } catch (e) {
                                console.error('Error parsing error response:', e);
                            }
                            toastr.error(errorMessage);
                        };
                        reader.readAsText(xhr.response);
                    } else {
                        toastr.error(errorMessage);
                    }
                    
                    btn.prop('disabled', false);
                }
            }
        };
        
        xhr.send();
    });

    // Activation switcher handler
    $(document).on('change', '.activation-switcher', function() {
        const checkbox = $(this);
        const productId = checkbox.data('product-id');
        const productName = checkbox.data('product-name');
        const newStatus = checkbox.is(':checked') ? 1 : 2;
        
        // Disable checkbox during request
        checkbox.prop('disabled', true);
        
        $.ajax({
            url: '{{ route('admin.products.change-activation', ':id') }}'.replace(':id', productId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    // Revert checkbox state
                    checkbox.prop('checked', !checkbox.is(':checked'));
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                // Revert checkbox state
                checkbox.prop('checked', !checkbox.is(':checked'));
                toastr.error('{{ __('catalogmanagement::product.error_changing_activation') }}');
            },
            complete: function() {
                checkbox.prop('disabled', false);
            }
        });
    });

    // Change Status Modal Handler
    let currentProductId = null;
    let currentProductType = '';

    $(document).on('click', '.change-status', function() {
        currentProductId = $(this).data('item-id');
        const productName = $(this).data('item-name');
        const currentStatus = $(this).data('item-status');
        currentProductType = $(this).data('item-type');

        $('#status-product-name').text(productName);
        $('#product-status').val(currentStatus);
        $('#rejection-reason').val('');

        // Show/hide rejection reason based on current status
        if (currentStatus === 'rejected') {
            $('#rejection-reason-group').show();
        } else {
            $('#rejection-reason-group').hide();
        }
    });

    // Show/hide rejection reason based on selected status
    $('#product-status').on('change', function() {
        const selectedStatus = $(this).val();

        if (selectedStatus === 'rejected') {
            $('#rejection-reason-group').slideDown();
        } else {
            $('#rejection-reason-group').slideUp();
            $('#rejection-reason').val('');
        }
    });

    // Confirm Status Change
    $('#confirmChangeStatusBtn').on('click', function() {
        const newStatus = $('#product-status').val();
        const rejectionReason = $('#rejection-reason').val();

        if (!newStatus) {
            toastr.error('{{ __('catalogmanagement::product.please_select_status') }}');
            return;
        }

        if (newStatus === 'rejected' && !rejectionReason) {
            toastr.error('{{ __('catalogmanagement::product.rejection_reason_required') }}');
            return;
        }

        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="uil uil-spinner-alt rotating"></i> {{ __('common.processing') }}');

        $.ajax({
            url: '{{ route('admin.products.change-status', ':id') }}'.replace(':id', currentProductId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus,
                rejection_reason: rejectionReason
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#modal-change-status').modal('hide');
                    $('#productsDataTable').DataTable().ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('{{ __('catalogmanagement::product.error_changing_status') }}');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Move to Bank Button Click Handler
    let currentBankProductId = null;
    $(document).on('click', '.move-to-bank', function() {
        currentBankProductId = $(this).data('item-id');
        const productName = $(this).data('item-name');
        
        $('#bank-product-name').text(productName);
        $('#modal-move-to-bank').modal('show');
    });

    // Confirm Move to Bank
    $('#confirmMoveToBankBtn').on('click', function() {
        if (!currentBankProductId) {
            toastr.error('{{ __('common.error') }}');
            return;
        }

        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="uil uil-spinner-alt rotating"></i> {{ __('common.processing') }}');

        $.ajax({
            url: '{{ route('admin.products.move-to-bank', ':id') }}'.replace(':id', currentBankProductId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#modal-move-to-bank').modal('hide');
                    $('#productsDataTable').DataTable().ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || '{{ __('common.error') }}';
                toastr.error(errorMessage);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
                currentBankProductId = null;
            }
        });
    });
});
</script>
