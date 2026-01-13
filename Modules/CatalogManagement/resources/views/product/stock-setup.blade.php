@extends('layout.app')
@section('title', trans('catalogmanagement::product.stock_setup'))

@section('content')
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    [
                        'title' => trans('dashboard.title'),
                        'url' => route('admin.dashboard'),
                        'icon' => 'uil uil-estate',
                    ],
                    ['title' => trans('catalogmanagement::product.stock_setup')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-0 p-30 bg-white radius-xl w-100 mb-30">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-2 fw-600 text-primary">
                                <i class="uil uil-map-marker me-2"></i>
                                {{ trans('catalogmanagement::product.stock_setup') }}
                            </h4>
                            <p class="text-muted mb-0">
                                {{ trans('catalogmanagement::product.select_regions_for_stock_management') }}
                            </p>
                        </div>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-squared shadow-sm px-4">
                            <i class="uil uil-arrow-left me-1"></i> {{ trans('common.back') ?? 'Back' }}
                        </a>
                    </div>

                    <!-- Vendor Selection (Admin) or Vendor Info (Vendor) -->
                    @if($isAdmin)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="vendor_select" class="form-label fw-600">
                                            <i class="uil uil-store me-2"></i>
                                            {{ trans('catalogmanagement::product.select_vendor') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="vendor_select" class="form-select select2">
                                            <option value="">{{ trans('catalogmanagement::product.select_vendor') }}</option>
                                            @foreach($vendors as $vendorItem)
                                                <option value="{{ $vendorItem['id'] }}"
                                                    {{ $selectedVendorId == $vendorItem['id'] ? 'selected' : '' }}>
                                                    {{ $vendorItem['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @if($vendor)
                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                            <i class="uil uil-info-circle fs-4 me-3"></i>
                            <div>
                                <strong>{{ trans('catalogmanagement::product.vendor') }}:</strong>
                                {{ $vendor->getTranslation('name', app()->getLocale()) ?? $vendor->name }}
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Instructions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i class="uil uil-lightbulb-alt me-2 text-warning"></i>
                                {{ trans('catalogmanagement::product.instructions') }}
                            </h6>
                            <ul class="mb-0">
                                <li>{{ trans('catalogmanagement::product.click_region_to_select') }}</li>
                                <li>{{ trans('catalogmanagement::product.selected_regions_highlighted') }}</li>
                                <li>{{ trans('catalogmanagement::product.click_save_to_apply') }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Region Boxes -->
                    @if($vendor && !$vendor->country_id)
                        <!-- Vendor Country Not Set Warning -->
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="uil uil-exclamation-triangle fs-4 me-3"></i>
                            <div>
                                <strong>{{ trans('common.warning') }}:</strong>
                                {{ trans('catalogmanagement::product.vendor_country_not_set') }}
                                <br>
                                <small>{{ trans('catalogmanagement::product.please_set_vendor_country_first') }}</small>
                            </div>
                        </div>
                    @endif

                    <!-- Always render the card structure so JavaScript can target it -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="uil uil-map me-2"></i>
                                {{ trans('catalogmanagement::product.available_regions') }}
                                <span class="badge badge-primary badge-lg badge-round ms-2 regions-count-badge text-white">{{ count($regions) }}</span>
                                @if($vendor && $vendor->country_id)
                                    <small class="text-muted ms-2">({{ trans('catalogmanagement::product.country') }}: {{ $vendor->country?->getTranslation('name', app()->getLocale()) ?? $vendor->country?->name ?? '-' }})</small>
                                @endif
                            </h6>
                        </div>
                        <div class="card-body regions-card-body">
                            @if(!$vendor)
                                <!-- No Vendor Selected -->
                                <div class="text-center py-5">
                                    <i class="uil uil-store fs-1 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted mb-2">{{ trans('catalogmanagement::product.no_vendor_selected') }}</h5>
                                    <p class="text-muted">{{ trans('catalogmanagement::product.please_select_vendor_to_continue') }}</p>
                                </div>
                            @else
                                <!-- Loading state - will be replaced by JavaScript -->
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted">{{ trans('common.loading') }}...</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-body')
    <x-loading-overlay
        loadingText="{{ trans('catalogmanagement::product.saving_regions') }}"
        loadingSubtext="{{ trans('common.please_wait') }}" />
@endpush

@push('styles')
<style>
    .region-box {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e5e5e5;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .region-box:hover {
        border-color: #5f63f2;
        box-shadow: 0 4px 12px rgba(95, 99, 242, 0.15);
        transform: translateY(-2px);
    }

    .region-box.active {
        border-color: #5f63f2;
        background: linear-gradient(135deg, #5f63f2 0%, #8e92f7 100%);
    }

    .region-box-inner {
        padding: 20px;
        text-align: center;
        position: relative;
    }

    .region-icon {
        font-size: 36px;
        color: #5f63f2;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .region-box.active .region-icon {
        color: #fff;
    }

    .region-name {
        font-size: 14px;
        font-weight: 600;
        color: #272b41;
        transition: all 0.3s ease;
    }

    .region-box.active .region-name {
        color: #fff;
    }

    .region-box.active .region-name .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .region-check {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
    }

    .region-check i {
        color: #5f63f2;
        font-size: 16px;
        font-weight: bold;
    }

    .region-box.active .region-check {
        opacity: 1;
        transform: scale(1);
    }

    /* Scrollable regions container */
    #regions-scroll-container {
        max-height: 600px;
        overflow-y: auto;
        padding: 15px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    #regions-scroll-container::-webkit-scrollbar {
        width: 8px;
    }

    #regions-scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #regions-scroll-container::-webkit-scrollbar-thumb {
        background: #5f63f2;
        border-radius: 10px;
    }

    #regions-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #4a4dd8;
    }

    #regions-container {
        margin: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .region-box-inner {
            padding: 15px;
        }

        .region-icon {
            font-size: 28px;
        }

        .region-name {
            font-size: 13px;
        }

        #regions-scroll-container {
            max-height: 400px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Global variables
let selectedVendorId = {{ $selectedVendorId ?? 'null' }};
const isAdmin = {{ $isAdmin ? 'true' : 'false' }};

// Pagination variables
let currentPage = 1;
let totalPages = 1;
let isLoadingMore = false;
let allSelectedRegions = [];
let totalRegionsCount = 0;

$('#vendor_select').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: '{{ trans('catalogmanagement::product.select_vendor') }}'
});

// Initialize Select2 for vendor dropdown
if (isAdmin) {
    // Handle vendor selection change
    $('#vendor_select').on('change', function() {
        const vendorId = $(this).val();
        if (vendorId) {
            selectedVendorId = vendorId;
            currentPage = 1;
            totalPages = 1;
            isLoadingMore = false;
            loadVendorRegions(vendorId);
        } else {
            selectedVendorId = null;
            showNoVendorSelectedMessage();
        }
    });

} else {
    // For vendors (not admins), auto-load regions if vendor is selected
    if (selectedVendorId) {
        console.log('Auto-loading regions for vendor:', selectedVendorId);
        loadVendorRegions(selectedVendorId);
    }
}

// Function to load vendor regions via AJAX with pagination
function loadVendorRegions(vendorId) {
    console.log('Loading regions for vendor:', vendorId, 'Page:', currentPage);

    // Show loading state only on first page
    if (currentPage === 1) {
        const $regionsCard = $('.regions-card-body');
        $regionsCard.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted">{{ trans('common.loading') }}...</p>
            </div>
        `);
    }

    // Load regions via /api/area/regions endpoint with pagination
    $.ajax({
        url: '/api/v1/area/regions',
        type: 'GET',
        dataType: 'json',
        data: {
            vendor_id: vendorId,
            country_id: $("meta[name='current_country_id']").attr('content'),
            per_page: 12,
            page: currentPage,
            paginated: 'ok',
            active: 1
        },
        success: function(response) {
            console.log('=== REGIONS API RESPONSE ===');
            console.log('Full response:', response);
            console.log('Response keys:', Object.keys(response));

            // Parse regions from API response - handle multiple formats
            let regions = [];
            if (response.data && Array.isArray(response.data)) {
                regions = response.data;
                console.log('Using response.data format');
            } else if (Array.isArray(response)) {
                regions = response;
                console.log('Using array format');
            }

            console.log('Parsed regions count:', regions.length);
            console.log('First region:', regions[0]);

            // Get pagination info - try multiple field names
            let newTotalPages = 1;
            let newTotalCount = regions.length;

            // Try different pagination field names
            if (response.pagination && response.pagination.last_page) {
                newTotalPages = response.pagination.last_page;
                newTotalCount = response.pagination.total;
                console.log('Found pagination object - last_page:', newTotalPages, 'total:', newTotalCount);
            } else if (response.last_page) {
                newTotalPages = response.last_page;
                console.log('Found last_page:', newTotalPages);
            } else if (response.meta && response.meta.last_page) {
                newTotalPages = response.meta.last_page;
                console.log('Found meta.last_page:', newTotalPages);
            }

            if (!newTotalCount || newTotalCount === regions.length) {
                if (response.total) {
                    newTotalCount = response.total;
                    console.log('Found total:', newTotalCount);
                } else if (response.meta && response.meta.total) {
                    newTotalCount = response.meta.total;
                    console.log('Found meta.total:', newTotalCount);
                }
            }

            totalPages = newTotalPages;
            totalRegionsCount = newTotalCount;

            console.log('✅ Pagination info - Total pages:', totalPages, 'Total count:', totalRegionsCount, 'Current page:', currentPage, 'Has more pages:', currentPage < totalPages);

            if (regions.length > 0) {
                // Update header count with total
                $('.regions-count-badge').text(totalRegionsCount);

                // Now get selected regions for this vendor
                loadSelectedRegions(vendorId, regions, currentPage === 1);
            } else if (currentPage === 1) {
                console.warn('No regions found for this vendor');
                showNoRegionsMessage();
            }

            // Hide loading indicator
            $('#loading-more').hide();
            isLoadingMore = false;
        },
        error: function(xhr, status, error) {
            console.error('Error loading regions:', error);
            console.error('XHR:', xhr);
            console.error('Response Text:', xhr.responseText);

            if (currentPage === 1) {
                const $regionsCard = $('.regions-card-body');
                $regionsCard.html(`
                    <div class="alert alert-danger">
                        <i class="uil uil-exclamation-triangle me-2"></i>
                        {{ trans('catalogmanagement::product.error_loading_regions') }}: ${error}
                        <br><small>${xhr.responseText}</small>
                    </div>
                `);
            }

            // Hide loading indicator
            $('#loading-more').hide();
            isLoadingMore = false;
        }
    });
}

// Function to load selected regions for vendor
function loadSelectedRegions(vendorId, regions, isFirstPage) {
    console.log('Loading selected regions for vendor:', vendorId, 'First page:', isFirstPage);

    // Only load selected regions on first page
    if (!isFirstPage) {
        displayRegions(regions, isFirstPage);
        return;
    }

    // Load selected regions via AJAX from stock-setup controller
    $.ajax({
        url: '{{ route('admin.products.stock-setup') }}',
        type: 'GET',
        dataType: 'json',
        data: {
            vendor_id: vendorId,
            ajax: 1
        },
        success: function(response) {
            console.log('Selected regions response:', response);

            if (!response.success) {
                console.error('API returned success=false');
                allSelectedRegions = [];
                displayRegions(regions, isFirstPage);
                return;
            }

            allSelectedRegions = response.selectedRegions || [];
            console.log('Selected region IDs:', allSelectedRegions);

            // Mark regions as selected based on IDs
            regions.forEach(function(region) {
                region.selected_for_vendor = allSelectedRegions.includes(region.id);
            });

            console.log('Regions with selection flags:', regions);

            // Display all regions with selection flags
            displayRegions(regions, isFirstPage);
        },
        error: function(xhr, status, error) {
            console.error('Error loading selected regions:', error);
            console.warn('Displaying regions without selection data');

            allSelectedRegions = [];
            // Display regions without selection (all unselected)
            displayRegions(regions, isFirstPage);
        }
    });
}

// Function to display regions
function displayRegions(regions, isFirstPage) {
    console.log('displayRegions called with:', regions, 'First page:', isFirstPage);
    const $regionsCard = $('.regions-card-body');

    console.log('$regionsCard element found:', $regionsCard.length);

    if (!regions || regions.length === 0) {
        if (isFirstPage) {
            console.warn('No regions to display');
            showNoRegionsMessage();
        }
        return;
    }

    // Create structure on first page
    if (isFirstPage) {
        let html = `
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="uil uil-search"></i>
                            </span>
                            <input type="text"
                                   id="region-search"
                                   class="form-control"
                                   placeholder="{{ trans('catalogmanagement::product.search_regions') }}"
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                <i class="uil uil-times m-0"></i>
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <span id="filtered-count">${totalRegionsCount}</span> {{ trans('catalogmanagement::product.of') }} ${totalRegionsCount} {{ trans('catalogmanagement::product.regions') }}
                        </small>
                    </div>
                </div>
            </div>
        `;

        html += '<div id="regions-scroll-container"><div class="row g-3" id="regions-container"></div><div id="loading-more" class="text-center py-4" style="display: none;"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div></div>';
        html += `
            <div class="text-center mt-4 pt-4 border-top">
                <button type="button" id="save-regions" class="btn btn-primary btn-lg px-5">
                    <i class="uil uil-save me-2"></i>
                    {{ trans('common.save') ?? 'Save' }}
                </button>
            </div>
        `;

        $regionsCard.html(html);
        setupRegionSearch();
    }

    // Add regions to container
    const $container = $('#regions-container');

    regions.forEach(function(region) {
        console.log('Processing region:', region);

        // Handle region name
        let regionName = region.name || 'Region #' + region.id;

        // Use selected_for_vendor flag from API
        const isSelected = region.selected_for_vendor === true || region.selected_for_vendor === 1;
        console.log(`Region ${region.id} (${regionName}): selected_for_vendor = ${isSelected}`);

        // Build location info (City, Country)
        let locationInfo = '';
        if (region.city_name && region.country_name) {
            locationInfo = `<small class="text-muted d-block mt-1">${region.city_name}, ${region.country_name}</small>`;
        } else if (region.city_name) {
            locationInfo = `<small class="text-muted d-block mt-1">${region.city_name}</small>`;
        } else if (region.country_name) {
            locationInfo = `<small class="text-muted d-block mt-1">${region.country_name}</small>`;
        }

        const regionHtml = `
            <div class="col-md-4 col-lg-3 region-item">
                <div class="region-box ${isSelected ? 'active' : ''}" data-region-id="${region.id}">
                    <div class="region-box-inner">
                        <div class="region-icon">
                            <i class="uil uil-location-point"></i>
                        </div>
                        <div class="region-name">
                            ${regionName}
                            ${locationInfo}
                        </div>
                        <div class="region-check">
                            <i class="uil uil-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $container.append(regionHtml);
    });

    console.log('Regions displayed:', regions.length);

    // Setup infinite scroll on first page
    if (isFirstPage) {
        // Small delay to ensure DOM is ready
        setTimeout(function() {
            setupInfiniteScroll();
        }, 100);
    } else {
        // Re-attach scroll listener for subsequent pages
        setTimeout(function() {
            attachScrollListener();
        }, 100);
    }
}

// Function to attach scroll listener
function attachScrollListener() {
    const $scrollContainer = $('#regions-scroll-container');

    if ($scrollContainer.length === 0) {
        console.warn('Scroll container not found');
        return;
    }

    console.log('Attaching scroll listener to container');

    $scrollContainer.on('scroll.regions', function() {
        const $container = $(this);
        const scrollTop = $container.scrollTop();
        const scrollHeight = $container[0].scrollHeight;
        const containerHeight = $container.height();

        // Calculate how close to bottom we are
        const distanceFromBottom = scrollHeight - (scrollTop + containerHeight);

        // Only log every 10 scroll events to avoid spam
        if (scrollTop % 50 === 0) {
            console.log('Scroll event - ScrollTop:', scrollTop, 'ScrollHeight:', scrollHeight, 'ContainerHeight:', containerHeight, 'Distance from bottom:', distanceFromBottom, 'isLoadingMore:', isLoadingMore, 'currentPage:', currentPage, 'totalPages:', totalPages);
        }

        // Load more when user scrolls to within 100px of bottom
        if (distanceFromBottom < 100 && !isLoadingMore && currentPage < totalPages) {
            console.log('✅ TRIGGER: Loading more regions... Current page:', currentPage, 'Total pages:', totalPages);
            isLoadingMore = true;

            // Show loading indicator
            $('#loading-more').show();

            // Load next page
            currentPage++;
            loadVendorRegions(selectedVendorId);
        }
    });
}

// Function to setup infinite scroll
function setupInfiniteScroll() {
    console.log('Setting up infinite scroll...');

    // Remove previous scroll handler
    $('#regions-scroll-container').off('scroll.regions');

    // Attach the scroll listener
    attachScrollListener();
}

// Function to setup region search functionality
function setupRegionSearch() {
    // Search functionality
    $(document).on('keyup', '#region-search', function() {
        const searchTerm = $(this).val().toLowerCase();
        let visibleCount = 0;

        $('.region-item').each(function() {
            const regionName = $(this).find('.region-name').text().toLowerCase();
            if (regionName.includes(searchTerm)) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $('#filtered-count').text(visibleCount);
    });

    // Clear search
    $(document).on('click', '#clear-search', function() {
        $('#region-search').val('').trigger('keyup');
    });
}

// Function to show no vendor selected message
function showNoVendorSelectedMessage() {
    const $regionsCard = $('.regions-card-body');
    $regionsCard.html(`
        <div class="text-center py-5">
            <i class="uil uil-store fs-1 text-muted mb-3 d-block"></i>
            <h5 class="text-muted mb-2">{{ trans('catalogmanagement::product.no_vendor_selected') }}</h5>
            <p class="text-muted">{{ trans('catalogmanagement::product.please_select_vendor_to_continue') }}</p>
        </div>
    `);
}

// Function to show no regions message
function showNoRegionsMessage() {
    const $regionsCard = $('.regions-card-body');
    $regionsCard.html(`
        <div class="text-center py-5">
            <i class="uil uil-map-marker fs-1 text-muted mb-3 d-block"></i>
            <h6 class="text-muted mb-2">{{ trans('catalogmanagement::product.no_regions_available') }}</h6>
            <p class="text-muted small">
                <small class="text-danger">{{ trans('catalogmanagement::product.no_active_regions_in_country') }}</small>
            </p>
        </div>
    `);
}

// Toggle region selection (using event delegation for dynamic content)
$(document).on('click', '.region-box', function() {
    $(this).toggleClass('active');
});

// Save regions (using event delegation for dynamic content)
$(document).on('click', '#save-regions', function() {
    // Check if vendor is selected
    if (!selectedVendorId) {
        if (typeof toastr !== 'undefined') {
            toastr.error('{{ trans('catalogmanagement::product.please_select_vendor_first') }}');
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: '{{ trans('common.error') }}',
                text: '{{ trans('catalogmanagement::product.please_select_vendor_first') }}'
            });
        }
        return;
    }

    const selectedRegions = [];

    $('.region-box.active').each(function() {
        selectedRegions.push($(this).data('region-id'));
    });

    console.log('Selected regions:', selectedRegions);
    console.log('Vendor ID:', selectedVendorId);

    // Show loading
    if (typeof LoadingOverlay !== 'undefined') {
        LoadingOverlay.show({
            text: '{{ trans('catalogmanagement::product.saving_regions') }}',
            subtext: '{{ trans('common.please_wait') }}'
        });
    }

    // Disable save button
    $(this).prop('disabled', true);

    // Send AJAX request
    $.ajax({
        url: '{{ route('admin.products.stock-setup.save') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            vendor_id: selectedVendorId,
            regions: selectedRegions
        },
        success: function(response) {
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }

            if (response.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message);
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ trans('common.success') }}',
                        text: response.message,
                        timer: 2000
                    });
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message);
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ trans('common.error') }}',
                        text: response.message
                    });
                }
            }
        },
        error: function(xhr) {
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }

            let errorMessage = '{{ trans('catalogmanagement::product.error_saving_regions') }}';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            if (typeof toastr !== 'undefined') {
                toastr.error(errorMessage);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: '{{ trans('common.error') }}',
                    text: errorMessage
                });
            }
        },
        complete: function() {
            $('#save-regions').prop('disabled', false);
        }
    });
});
</script>
@endpush
