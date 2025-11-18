/**
 * Product Form JavaScript
 * Contains all JavaScript logic for product creation/editing wizard
 */

// Global variables for wizard state
let currentStep = 1;
const totalSteps = 4;
let validationErrors = {};
let variantIndex = 0; // Counter for variant boxes
let cachedRegions = null; // Cache for regions data

// Immediate initialization to hide steps
document.addEventListener("DOMContentLoaded", function () {
    const allSteps = document.querySelectorAll(".wizard-step-content");
    allSteps.forEach(function (step, index) {
        if (index === 0) {
            step.classList.add("active");
        } else {
            step.classList.remove("active");
        }
    });
});

// Use jQuery document ready to ensure DOM and jQuery are loaded
jQuery(document).ready(function () {
    console.log("🚀 Product form script loaded");

    // Ensure productFormConfig is available
    ensureProductFormConfig();

    setTimeout(function() {
        $('#brand_id, #vendor_id, #department_id, #category_id, #sub_category_id, #tax_id, #configuration_type').each(function() {
            // Skip if this is a hidden input
            if ($(this).attr('type') === 'hidden') {
                console.log('⏭️ Skipping Select2 init for hidden input:', $(this).attr('id'));
                return;
            }

            var emptyOptionText = $(this).find('option[value=""]').text().trim();
            console.log('📋 Select2 Init - ID:', $(this).attr('id'), 'Placeholder:', emptyOptionText);

            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: false,
                placeholder: emptyOptionText || 'Select An Option'
            });
        });
    }, 100);

    // Helper function to filter departments by vendor activities
    function filterDepartmentsByVendor(vendorId) {
        if (!vendorId) {
            console.log('⚠️ No vendor ID provided');
            return;
        }

        console.log('🔄 Fetching departments for vendor:', vendorId);

        // Fetch departments from API with vendor_id parameter
        const departmentsRoute = window.productFormConfig?.departmentsRoute || '/api/departments';
        const url = `${departmentsRoute}?vendor_id=${vendorId}`;

        console.log('🌐 Fetching from:', url);

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            console.log('📥 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Departments fetched:', data);

            const departmentSelect = $('#department_id');

            // Reset with empty option
            departmentSelect.empty().append('<option value="">Select Department</option>');

            // Handle API response
            if (data && Array.isArray(data) && data.length > 0) {
                // If data is array of departments
                data.forEach(dept => {
                    departmentSelect.append(
                        `<option value="${dept.id}">${dept.name}</option>`
                    );
                });
                console.log(`✅ Loaded ${data.length} departments`);
            } else if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
                // If data is wrapped in {data: [...]}
                data.data.forEach(dept => {
                    departmentSelect.append(
                        `<option value="${dept.id}">${dept.name}</option>`
                    );
                });
                console.log(`✅ Loaded ${data.data.length} departments`);
            } else {
                console.log('⚠️ No departments found for vendor:', vendorId);
                departmentSelect.append('<option value="">No departments available</option>');
            }

            // Refresh Select2
            if (departmentSelect.data('select2')) {
                departmentSelect.select2('destroy');
            }
            departmentSelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: false
            });
        })
        .catch(error => {
            console.error('❌ Error fetching departments:', error);
            const departmentSelect = $('#department_id');
            departmentSelect.empty().append('<option value="">Error loading departments</option>');

            // Refresh Select2
            if (departmentSelect.data('select2')) {
                departmentSelect.select2('destroy');
            }
            departmentSelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: false
            });
        });
    }

    // Handle vendor change to filter departments (only for select elements, not hidden inputs)
    $('#vendor_id').on('change', function() {
        // Skip if this is a hidden input
        if ($(this).attr('type') === 'hidden') {
            console.log('⏭️ Skipping vendor change handler for hidden input');
            return;
        }

        const vendorId = $(this).val();
        if (vendorId) {
            filterDepartmentsByVendor(vendorId);
        }
    });

    // Load departments on page load if vendor_id is set (for vendor users)
    setTimeout(function() {
        const vendorIdInput = $('#vendor_id');
        const vendorId = vendorIdInput.val();

        // Only process if vendor_id has a value and it's a hidden input (vendor user)
        if (vendorId && vendorIdInput.attr('type') === 'hidden') {
            console.log('📦 Loading departments for vendor:', vendorId);
            filterDepartmentsByVendor(vendorId);
        }
    }, 200);

    // Ensure all form fields have error containers
    setTimeout(function() {
        ensureErrorContainers();
    }, 500);

    // Add test function for error containers
    window.testErrorContainers = function() {
        console.log('🧪 Testing error containers...');

        // Test translation title fields
        const languages = [1, 2]; // Common language IDs
        languages.forEach(langId => {
            const containerId = `error-translations-${langId}-title`;
            const container = $(`#${containerId}`);
            console.log(`📝 Container #${containerId}: exists=${container.length > 0}`);

            if (container.length > 0) {
                // Test showing error
                container.html('<i class="uil uil-exclamation-triangle"></i> Test error message').show();
                console.log(`✅ Test error displayed in ${containerId}`);

                // Hide after 3 seconds
                setTimeout(() => {
                    container.hide().empty();
                    console.log(`🧹 Cleared test error from ${containerId}`);
                }, 3000);
            }
        });

        console.log('💡 Check the form to see test error messages appear and disappear');
    };

    // Add specific test for Title (English) field
    window.testTitleError = function() {
        console.log('🧪 Testing Title (English) error specifically...');

        const titleInput = $('input[name="translations[1][title]"]');
        const errorContainer = $('#error-translations-1-title');

        console.log('📝 Title input found:', titleInput.length > 0);
        console.log('📝 Title input element:', titleInput[0]);
        console.log('📝 Error container found:', errorContainer.length > 0);
        console.log('📝 Error container element:', errorContainer[0]);

        if (errorContainer.length > 0) {
            console.log('📝 Container current display:', errorContainer.css('display'));
            console.log('📝 Container current visibility:', errorContainer.is(':visible'));
            console.log('📝 Container classes:', errorContainer.attr('class'));
            console.log('📝 Container style attribute:', errorContainer.attr('style'));

            // Force show with test message
            errorContainer.html('<i class="uil uil-exclamation-triangle"></i> TEST: Title is required for English');
            errorContainer.show();
            errorContainer.css('display', 'block');
            errorContainer.css('visibility', 'visible');
            errorContainer.removeClass('d-none').addClass('d-block');
            errorContainer.attr('style', 'display: block !important;');

            console.log('📝 After force show - display:', errorContainer.css('display'));
            console.log('📝 After force show - visible:', errorContainer.is(':visible'));
            console.log('📝 After force show - style:', errorContainer.attr('style'));

            // Also add red border to input
            titleInput.addClass('is-invalid');

            console.log('✅ Test error should now be visible under Title (English) field');
        } else {
            console.log('❌ Error container not found for Title (English)');
        }
    };

    // Add direct test for error container
    window.testErrorContainer = function() {
        console.log('🧪 Direct test of error-translations-1-title container...');

        const container = $('#error-translations-1-title');
        console.log('📝 Container found:', container.length > 0);
        console.log('📝 Container element:', container[0]);

        if (container.length > 0) {
            console.log('📝 Current display:', container.css('display'));
            console.log('📝 Current visibility:', container.css('visibility'));
            console.log('📝 Is visible:', container.is(':visible'));
            console.log('📝 Current content:', container.html());
            console.log('📝 Current classes:', container.attr('class'));
            console.log('📝 Current style:', container.attr('style'));

            // Force show test
            container.html('<i class="uil uil-exclamation-triangle"></i> DIRECT TEST MESSAGE');
            container.show();
            container.css('display', 'block');
            container.css('visibility', 'visible');
            container.removeClass('d-none').addClass('d-block');
            container.attr('style', 'display: block !important; visibility: visible !important;');

            console.log('📝 After force show:');
            console.log('📝 Display:', container.css('display'));
            console.log('📝 Visibility:', container.css('visibility'));
            console.log('📝 Is visible:', container.is(':visible'));
            console.log('📝 Style attr:', container.attr('style'));

            console.log('✅ Direct test completed - check if message appears');
        } else {
            console.log('❌ Container #error-translations-1-title not found');
        }
    };

    // Function to attach event handlers
    function attachEventHandlers() {
        console.log("🔧 Attaching event handlers to Select2 dropdowns...");

        // Initialize: Hide all departments except empty option until vendor is selected
        const vendorSelect = $("#vendor_id");
        const departmentSelect = $("#department_id");
        const vendorActivitiesMap = window.productFormConfig.vendorActivitiesMap || {};

        // Check if this is Admin/Super Admin (has vendorActivitiesMap with multiple vendors)
        const isAdminUser = Object.keys(vendorActivitiesMap).length > 0;

        if (isAdminUser) {
            console.log("👤 Admin/Super Admin user detected - hiding departments until vendor selected");
            // Hide all department options except empty option
            departmentSelect.find("option[value!=''][data-activities]").hide();
        }

        // Vendor change handler - Filter departments based on vendor activities
        $(document)
            .off("change.productForm", "#vendor_id")
            .on("change.productForm", "#vendor_id", function (e) {
                console.log("🎯 Vendor changed");
                const vendorId = $(this).val();

                if (vendorId) {
                    // Use the optimized local filtering function
                    filterDepartmentsByVendor(vendorId);
                }
            });

        // Department change handler - Use namespaced events and listen for select2:select
        // Use event delegation on the body to ensure it survives re-initialization
        $(document)
            .off("change.productForm", "#department_id")
            .on("change.productForm", "#department_id", function (e) {
                console.log("🎯 Department event triggered:", e.type);
                const departmentId = $(this).val();
                console.log("🔄 Department changed:", departmentId);

                const categorySelect = $("#category_id");
                const subCategorySelect = $("#sub_category_id");

                // Reset category and subcategory
                categorySelect
                    .empty()
                    .append('<option value="">Loading categories...</option>')
                    .prop("disabled", true)
                    .trigger("change");
                subCategorySelect
                    .empty()
                    .append('<option value="">Select Sub Category</option>')
                    .val("")
                    .trigger("change");

                if (departmentId) {
                    // Load categories for selected department
                    const url = `${window.productFormConfig.categoriesRoute}?department_id=${departmentId}&select2=1`;
                    console.log("🌐 Fetching categories from:", url);

                    fetch(url, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            lang: document.documentElement.lang || "en", // Custom header for app locale
                        },
                    })
                        .then((response) => {
                            console.log(
                                "📥 Categories response status:",
                                response.status
                            );
                            if (!response.ok) {
                                throw new Error(
                                    `HTTP error! status: ${response.status}`
                                );
                            }
                            return response.json();
                        })
                        .then((response) => {
                            console.log(
                                "✅ Categories API response:",
                                response
                            );

                            // Reset with empty option
                            categorySelect
                                .empty()
                                .append(
                                    '<option value="">Select Category</option>'
                                )
                                .prop("disabled", false);

                            // Handle API response format: {status, message, data, errors, code}
                            if (
                                response.status &&
                                response.data &&
                                response.data.length > 0
                            ) {
                                response.data.forEach((category) => {
                                    categorySelect.append(
                                        `<option value="${category.id}">${category.name}</option>`
                                    );
                                });
                                console.log(
                                    `✅ Loaded ${response.data.length} categories`
                                );
                            } else {
                                console.log(
                                    "⚠️ No categories found for department:",
                                    departmentId
                                );
                                categorySelect.append(
                                    '<option value="">No categories available</option>'
                                );
                            }
                            // Refresh Select2 dropdown
                            categorySelect.trigger("change");
                        })
                        .catch((error) => {
                            console.error(
                                "❌ Error loading categories:",
                                error
                            );
                            categorySelect
                                .empty()
                                .append(
                                    '<option value="">Error loading categories</option>'
                                )
                                .prop("disabled", false)
                                .trigger("change");
                        });
                } else {
                    categorySelect
                        .empty()
                        .append('<option value="">Select Category</option>')
                        .prop("disabled", false)
                        .trigger("change");
                }
            });

        console.log("✅ Department handler attached");

        // Remove any existing handlers for category to prevent duplicates
        $("#category_id").off("change.productForm select2:select.productForm");

        // Category change handler - Use event delegation to survive re-initialization
        $(document)
            .off("change.productForm", "#category_id")
            .on("change.productForm", "#category_id", function (e) {
                console.log("🎯 Category event triggered:", e.type);
                const categoryId = $(this).val();
                console.log("🔄 Category changed:", categoryId);

                const subCategorySelect = $("#sub_category_id");

                // Reset subcategory
                subCategorySelect
                    .empty()
                    .append(
                        '<option value="">Loading subcategories...</option>'
                    )
                    .prop("disabled", true)
                    .trigger("change");

                if (categoryId) {
                    // Load subcategories for selected category
                    const url = `${window.productFormConfig.subCategoriesRoute}?category_id=${categoryId}`;
                    console.log("🌐 Fetching subcategories from:", url);

                    fetch(url, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                    })
                        .then((response) => {
                            console.log(
                                "📥 SubCategories response status:",
                                response.status
                            );
                            if (!response.ok) {
                                throw new Error(
                                    `HTTP error! status: ${response.status}`
                                );
                            }
                            return response.json();
                        })
                        .then((response) => {
                            console.log(
                                "✅ SubCategories API response:",
                                response
                            );

                            // Reset with empty option
                            subCategorySelect
                                .empty()
                                .append(
                                    '<option value="">Select Sub Category</option>'
                                )
                                .prop("disabled", false);

                            // Handle API response format: {status, message, data, errors, code}
                            if (
                                response.status &&
                                response.data &&
                                response.data.length > 0
                            ) {
                                response.data.forEach((subcategory) => {
                                    subCategorySelect.append(
                                        `<option value="${subcategory.id}">${subcategory.name}</option>`
                                    );
                                });
                                console.log(
                                    `✅ Loaded ${response.data.length} subcategories`
                                );
                            } else {
                                console.log(
                                    "⚠️ No subcategories found for category:",
                                    categoryId
                                );
                                subCategorySelect.append(
                                    '<option value="">No subcategories available</option>'
                                );
                            }
                            // Refresh Select2 dropdown
                            subCategorySelect.trigger("change");
                        })
                        .catch((error) => {
                            console.error(
                                "❌ Error loading subcategories:",
                                error
                            );
                            subCategorySelect
                                .empty()
                                .append(
                                    '<option value="">Error loading subcategories</option>'
                                )
                                .prop("disabled", false)
                                .trigger("change");
                        });
                } else {
                    subCategorySelect
                        .empty()
                        .append('<option value="">Select Sub Category</option>')
                        .prop("disabled", false)
                        .trigger("change");
                }
            });

        console.log("✅ Category handler attached");
        console.log("✅ All handlers ready!");
    }

    // Wait for Select2 to be fully initialized by the global layout
    // Check if Select2 is already initialized, if not wait
    function waitForSelect2AndAttach() {
        const deptElement = $("#department_id");
        if (
            deptElement.length &&
            deptElement.hasClass("select2-hidden-accessible")
        ) {
            // Select2 is initialized, attach handlers
            attachEventHandlers();
        } else {
            // Wait and try again
            setTimeout(waitForSelect2AndAttach, 100);
        }
    }

    // Start checking for Select2 initialization after a short delay
    setTimeout(waitForSelect2AndAttach, 200);

    // Initialize wizard on page load

    // Add form submission handler
    $('#productForm').on('submit', handleFormSubmission);

    // Next button (using event delegation)
    $(document).on("click", "#nextBtn", function () {
        console.log("🔄 Next button clicked, current step:", currentStep);

        // Validate current step before proceeding
        const errors = {};
        const isValid = validateStep(currentStep, errors);

        if (!isValid) {
            console.log("❌ Validation failed for step", currentStep);
            displayValidationErrors(errors);
            return;
        }

        console.log("✅ Validation passed for step", currentStep);
        currentStep++;
        if (currentStep > 4) currentStep = 4;
        showStep(currentStep);
    });

    // Previous button
    $(document).on("click", "#prevBtn", function () {
        console.log("🔄 Previous button clicked, current step:", currentStep);
        currentStep--;
        if (currentStep < 1) currentStep = 1;
        showStep(currentStep);
    });

    // Debug: Check if elements exist
    console.log("🔍 Debugging element detection:");
    console.log("- Next button (#nextBtn):", $("#nextBtn").length);
    console.log("- Previous button (#prevBtn):", $("#prevBtn").length);
    console.log("- Wizard step nav (.wizard-step-nav):", $(".wizard-step-nav").length);
    console.log("- Wizard step content (.wizard-step-content):", $(".wizard-step-content").length);

    // List all elements that might be step navigation
    console.log("🔍 All elements with 'step' in class or data:");
    $("[class*='step'], [data-step]").each(function() {
        console.log("Element:", this.tagName, "Class:", $(this).attr('class'), "Data-step:", $(this).data('step'));
    });

    // Click on wizard step navigation
    $(document).on("click", ".wizard-step-nav", function (e) {
        console.log("🖱️ Wizard step clicked!");
        e.preventDefault();

        const targetStep = parseInt($(this).data("step"));
        console.log("Clicked step:", targetStep, "Current step:", currentStep);

        if (isNaN(targetStep)) {
            console.error("Invalid target step:", $(this).data("step"));
            return;
        }

        // Allow navigation to any step without validation
        console.log(`🔍 Navigating from step ${currentStep} to step ${targetStep}`);

        // Hide validation alert when clicking on steps
        $('#validation-alerts-container').hide().empty();

        // Don't clear errors when navigating steps - keep them visible
        // clearAllErrors();

        currentStep = targetStep;
        console.log("About to call showStep with:", currentStep);
        showStep(currentStep);
        console.log("showStep completed");

        // No review step - step 4 is now the final step
    });

    // Edit button in review page
    $(document).on("click", ".edit-step", function () {
        const targetStep = parseInt($(this).data("step"));

        currentStep = targetStep;
        showStep(currentStep);

        // Scroll to top of form
        $("html, body").animate(
            {
                scrollTop: $(".card").offset().top - 100,
            },
            300
        );
    });

    // Form submission handler
    $('#productForm').on('submit', handleFormSubmission);

    // Clear errors when user starts typing in any input
    $('#productForm').on('input keyup change', 'input, textarea, select', function() {
        const $field = $(this);
        const fieldName = $field.attr('name');

        if (fieldName) {
            // Remove is-invalid class
            $field.removeClass('is-invalid');

            // Try to find and clear existing error container
            let errorContainer = null;

            // For translation fields, try the specific pattern
            if (fieldName.includes('translations[')) {
                // Extract language ID and field type from name like "translations[1][title]"
                const matches = fieldName.match(/translations\[(\d+)\]\[([^\]]+)\]/);
                if (matches) {
                    const langId = matches[1];
                    const fieldType = matches[2];
                    errorContainer = $(`#error-translations-${langId}-${fieldType}`);
                }
            }

            // Try other common patterns
            if (!errorContainer || !errorContainer.length) {
                const selectors = [
                    `#error-${fieldName}`,
                    `#error-${fieldName.replace(/\[|\]/g, '-').replace(/--/g, '-').replace(/-$/, '')}`,
                    `[data-error-for="${fieldName}"]`
                ];

                for (const selector of selectors) {
                    errorContainer = $(selector);
                    if (errorContainer.length > 0) {
                        break;
                    }
                }
            }

            // Clear existing error container
            if (errorContainer && errorContainer.length) {
                errorContainer.hide().empty();
            }

            // Also remove any dynamically created error messages
            $field.closest('.form-group').find('.error-message:not([id])').remove();
            $field.siblings('.error-message:not([id])').remove();

            // For Select2, also clear errors from Select2 container
            if ($field.hasClass('select2') || $field.data('select2')) {
                $field.next('.select2-container').siblings('.error-message:not([id])').remove();
            }

            console.log(`🧹 Cleared error for field: ${fieldName}`);
        }
    });

    // Configuration Type Toggle
    $("#configuration_type").on("change", function () {
        const selectedType = $(this).val();

        if (selectedType === "simple") {
            $("#simple-product-section").show();
            $("#variants-section").hide();

            // Generate simple product boxes
            generateSimpleProductBoxes();
        } else if (selectedType === "variants") {
            $("#simple-product-section").hide();
            $("#variants-section").show();

            // Clear simple product container
            $("#simple-product-details-container").empty();
        } else {
            // No selection - hide both sections
            $("#simple-product-section").hide();
            $("#variants-section").hide();

            // Clear simple product container
            $("#simple-product-details-container").empty();
        }
    });

    // Discount Checkbox Toggle (for simple products)
    $(document).on("change", ".simple-discount-toggle", function () {
        const discountFields = $(this).closest('.card').find('.simple-discount-fields');
        if ($(this).is(":checked")) {
            discountFields.slideDown();
        } else {
            discountFields.slideUp();
            discountFields.find('input').val("");
        }
    });

    // Discount Checkbox Toggle (for variants - existing)
    $(document).on("change", ".variant-discount-toggle", function () {
        const discountFields = $(this).closest('.card').find('.variant-discount-fields');
        if ($(this).is(":checked")) {
            discountFields.slideDown();
        } else {
            discountFields.slideUp();
            discountFields.find('input').val("");
        }
    });

    // Stock Row Index
    let stockRowIndex = 0;

    // Add Stock Row (Event Delegation for dynamically generated buttons)
    $(document).on("click", ".add-stock-row", function () {
        addStockRow();
    });

    // Variant Key Selection Change Handler
    $(document).on("change", ".variant-key-select", function () {
        const variantBox = $(this).closest(".variant-box");
        const variantIndex = variantBox.data("variant-index");
        const selectedKeyId = $(this).val();
        const selectedKeyText = $(this).find("option:selected").text();

        console.log("Variant key changed:", selectedKeyId, selectedKeyText);

        if (selectedKeyId) {
            // Update variant title
            const variantTitle = variantBox.find(".variant-title");
            variantTitle.html(`<i class="uil uil-cube"></i> ${selectedKeyText}`);

            // Load variant values for this key
            loadVariantValues(variantIndex, selectedKeyId);
        } else {
            // Reset title to default
            const config = window.productFormConfig || {};
            const variantTitle = variantBox.find(".variant-title");
            variantTitle.html(`<i class="uil uil-cube"></i> ${config.variantNumber || 'Variant'} ${variantIndex}`);

            // Clear variant values
            const valueSelect = variantBox.find(".variant-value-select");
            valueSelect.html('<option value="">Select Variant Key First</option>');
        }
    });

    // Variant Value Selection Change Handler
    $(document).on("change", ".variant-value-select", function () {
        const variantBox = $(this).closest(".variant-box");
        const variantIndex = variantBox.data("variant-index");
        const selectedValueId = $(this).val();
        const selectedValueText = $(this).find("option:selected").text();
        const keySelect = variantBox.find(".variant-key-select");
        const selectedKeyText = keySelect.find("option:selected").text();

        console.log("Variant value changed:", selectedValueId, selectedValueText);

        if (selectedValueId && selectedKeyText && selectedKeyText !== "Select Variant Key") {
            // Update variant title with key and value
            const variantTitle = variantBox.find(".variant-title");
            variantTitle.html(`<i class="uil uil-cube"></i> ${selectedKeyText}: ${selectedValueText}`);

            // Show variant details path
            const variantDetailsPath = variantBox.find(".variant-details-path");
            const variantPathText = variantBox.find(".variant-path-text");
            variantPathText.text(`${selectedKeyText}: ${selectedValueText}`);
            variantDetailsPath.show();
        } else {
            // Reset to key name only if value is cleared
            if (selectedKeyText && selectedKeyText !== "Select Variant Key") {
                const variantTitle = variantBox.find(".variant-title");
                variantTitle.html(`<i class="uil uil-cube"></i> ${selectedKeyText}`);
            }

            // Hide variant details path
            const variantDetailsPath = variantBox.find(".variant-details-path");
            variantDetailsPath.hide();
        }
    });

    // Remove Stock Row (Event Delegation)
    $(document).on("click", ".remove-stock-row", function () {
        $(this).closest("tr").remove();
        calculateTotalStock();
        toggleStockTableVisibility();
        reindexStockRows();
    });

    // Calculate Total Stock on Input Change
    $(document).on("input", ".stock-quantity", function () {
        calculateTotalStock();
    });

    // Variant Management
    let variantIndex = 0;

    // Add Variant Button
    $("#add-variant-btn").on("click", function () {
        addVariantBox();
    });

    // Remove Variant (Event Delegation)
    $(document).on("click", ".remove-variant-btn", function () {
        $(this).closest(".variant-box").remove();
        toggleVariantsVisibility();
        reindexVariants();
    });

    // Variant Key Change (Event Delegation)
    $(document).on("change", ".variant-key-select", function () {
        const variantBox = $(this).closest(".variant-box");
        const selectedKeyId = $(this).val();

        if (selectedKeyId) {
            // Show the tree container
            variantBox.find(".variant-tree-container").show();

            // Load nested variants for the selected key
            loadNestedVariants(variantBox, selectedKeyId);
        } else {
            // Hide the tree container and clear selection
            variantBox.find(".variant-tree-container").hide();
            variantBox.find(".final-variant-id").val("");
        }
    });

    // Variant Level Change (Event Delegation)
    $(document).on("change", ".variant-level-select", function () {
        const variantBox = $(this).closest(".variant-box");
        const level = parseInt($(this).data("level"));
        const selectedId = $(this).val();
        const hasChildren = $(this)
            .find("option:selected")
            .data("has-children");

        // Clear final selection when changing any level
        variantBox.find(".final-variant-id").val("");

        if (selectedId) {
            handleVariantLevelChange(
                variantBox,
                level,
                selectedId,
                hasChildren
            );
        } else {
            // Clear all levels after this one
            const nestedContainer = variantBox.find(".nested-variant-levels");
            nestedContainer.find(`[data-level]`).each(function () {
                if (parseInt($(this).data("level")) > level) {
                    $(this).remove();
                }
            });
            updateVariantSelectionInfo(variantBox);
        }
    });

    // Add Stock Row for Variant (Event Delegation)
    $(document).on("click", ".add-stock-row-variant", function () {
        const variantIndex = $(this).data("variant-index");
        addVariantStockRow(variantIndex);
    });

    // Remove Stock Row for Variant (Event Delegation)
    $(document).on("click", ".remove-variant-stock-row", function () {
        const row = $(this).closest("tr");
        const variantIndex = $(this).data("variant-index");
        const stockRowsContainer = $(
            `.variant-stock-rows[data-variant-index="${variantIndex}"]`
        );

        row.remove();

        // Re-index remaining rows
        stockRowsContainer.find("tr").each(function (index) {
            $(this)
                .find("td:first")
                .text(index + 1);
        });

        // Hide table if no rows left
        if (stockRowsContainer.find("tr").length === 0) {
            $(
                `.variant-stock-table-container[data-variant-index="${variantIndex}"]`
            ).hide();
            $(
                `.variant-stock-empty-state[data-variant-index="${variantIndex}"]`
            ).show();
        }

        updateVariantStockTotals(variantIndex);
    });

    // Update stock totals when quantity changes (Event Delegation)
    $(document).on("input", ".variant-stock-quantity", function () {
        const row = $(this).closest("tr");
        const variantIndex = row.data("variant-index");
        updateVariantStockTotals(variantIndex);
    });

    // Load regions data on page load
    loadRegionsData();

    // Ensure LoadingOverlay is initialized
    if (typeof LoadingOverlay !== "undefined" && LoadingOverlay.init) {
        console.log("🔄 Initializing LoadingOverlay...");
        LoadingOverlay.init();
        console.log("✅ LoadingOverlay initialized");
    } else {
        console.warn("⚠️ LoadingOverlay not available");
    }

    console.log("✅ Product form navigation initialized");
});

/**
 * Load regions data once on page load
 */
function loadRegionsData() {
    console.log("🌍 Loading regions data...");
    let url = "/api/area/regions?select2=1";
    $.ajax({
        url: url,
        method: "GET",
        dataType: "json",
        success: function (response) {
            cachedRegions = response.data;
            console.log("✅ Regions loaded successfully:", cachedRegions);
        },
        error: function (xhr, status, error) {
            console.log("❌ API error, using fallback regions");
            // Set fallback regions on error
            cachedRegions = [
                { id: 1, text: "Cairo", name: "Cairo" },
                { id: 2, text: "Alexandria", name: "Alexandria" },
                { id: 3, text: "Giza", name: "Giza" },
                { id: 4, text: "Luxor", name: "Luxor" },
                { id: 5, text: "Aswan", name: "Aswan" }
            ];
            console.log("✅ Fallback regions set:", cachedRegions);
        }
    });
}

/**
 * Show/Hide wizard steps
 */
function showStep(step) {
    console.log("🔄 showStep called with step:", step);

    // Hide all steps
    $(".wizard-step-content").each(function () {
        $(this).removeClass("active").css("display", "none");
    });
    console.log("✅ All steps hidden");

    // Show target step
    const targetStep = $(`.wizard-step-content[data-step="${step}"]`);
    console.log("🎯 Target step element found:", targetStep.length > 0, "Selector:", `.wizard-step-content[data-step="${step}"]`);

    if (targetStep.length) {
        targetStep.addClass("active").css("display", "block");
        console.log("✅ Target step shown");
    } else {
        console.error("❌ Target step not found for step:", step);
        // List all available steps for debugging
        $(".wizard-step-content").each(function() {
            console.log("Available step:", $(this).data("step"));
        });
    }

    // Reapply validation errors if they exist for this step
    if (Object.keys(validationErrors).length > 0 && step !== 4) {
        for (let field in validationErrors) {
            const bracketField = convertDotToBracket(field);
            const fieldElement = targetStep
                .find(
                    `[name="${bracketField}"], [name="${bracketField}[]"], [name="${field}"], [name="${field}[]"]`
                )
                .first();

            if (fieldElement.length) {
                fieldElement.addClass("is-invalid");

                // Try to use existing error container first (same logic as displayValidationErrors)
                let errorContainer = null;

                // For translation fields, try the specific pattern
                if (field.includes('translations.')) {
                    const parts = field.split('.');
                    if (parts.length === 3 && parts[0] === 'translations') {
                        const langId = parts[1];
                        const fieldType = parts[2];
                        const containerId = `error-translations-${langId}-${fieldType}`;
                        errorContainer = $(`#${containerId}`);
                    }
                }

                // Try other common patterns if translation pattern didn't work
                if (!errorContainer || !errorContainer.length) {
                    const fieldName = fieldElement.attr('name');
                    const selectors = [
                        `#error-${field}`,
                        `#error-${fieldName}`,
                        `#error-${field.replace(/\./g, '-')}`,
                        `#error-${fieldName.replace(/\[|\]/g, '-').replace(/--/g, '-').replace(/-$/, '')}`
                    ];

                    for (const selector of selectors) {
                        errorContainer = $(selector);
                        if (errorContainer.length > 0) {
                            break;
                        }
                    }
                }

                if (errorContainer && errorContainer.length) {
                    // Use existing error container
                    const errorMessage = `<i class="uil uil-exclamation-triangle"></i> ${validationErrors[field][0]}`;
                    errorContainer.html(errorMessage).show().css('display', 'block').removeClass('d-none').addClass('d-block');
                } else {
                    // Fallback: create new error message
                    fieldElement.closest(".form-group").find(".error-message:not([id])").remove();

                    const errorMsg = `<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${validationErrors[field][0]}</div>`;

                    if (fieldElement.hasClass("select2") || fieldElement.data("select2")) {
                        const select2Container = fieldElement.next(".select2-container");
                        if (select2Container.length) {
                            select2Container.after(errorMsg);
                        } else {
                            fieldElement.after(errorMsg);
                        }
                    } else {
                        fieldElement.after(errorMsg);
                    }
                }
            }
        }
    }

    // Update wizard navigation
    $(".wizard-step-nav").removeClass("current");
    $(`.wizard-step-nav[data-step="${step}"]`).addClass("current");

    // Mark completed steps
    $(".wizard-step-nav").each(function () {
        const stepNum = parseInt($(this).data("step"));
        if (stepNum < step) {
            $(this).addClass("completed");
        } else {
            $(this).removeClass("completed");
        }
    });

    // No review step - step 4 is now the final step

    // Update buttons
    if (step === 1) {
        $("#prevBtn").hide();
    } else {
        $("#prevBtn").show();
    }

    if (step === totalSteps) {
        $("#nextBtn").hide();
        $("#submitBtn").show();
    } else {
        $("#nextBtn").show();
        $("#submitBtn").hide();
    }

    // Scroll to top
    $("html, body").animate(
        {
            scrollTop: $(".card-body").offset().top - 100,
        },
        300
    );
}


/**
 * Helper function to convert dot notation to bracket notation
 * e.g., "translations.1.title" -> "translations[1][title]"
 */
function convertDotToBracket(field) {
    const parts = field.split(".");
    if (parts.length === 1) return field;

    let result = parts[0];
    for (let i = 1; i < parts.length; i++) {
        result += `[${parts[i]}]`;
    }
    return result;
}

/**
 * Ensure all form fields have error containers
 */
function ensureErrorContainers() {
    console.log('🔧 Ensuring all form fields have error containers...');

    // Find all input, select, and textarea elements
    $('#productForm').find('input, select, textarea').each(function() {
        const $field = $(this);
        const fieldName = $field.attr('name');

        if (!fieldName) return; // Skip fields without names

        // Check if error container already exists
        const errorContainerId = `error-${fieldName.replace(/\[|\]/g, '-').replace(/--/g, '-').replace(/-$/, '')}`;

        if ($(`#${errorContainerId}`).length === 0) {
            // Create error container if it doesn't exist
            const errorContainer = `<div class="error-message text-danger" id="${errorContainerId}" style="display: none;"></div>`;

            if ($field.hasClass('select2') || $field.data('select2')) {
                // For Select2, add after the Select2 container
                const select2Container = $field.next('.select2-container');
                if (select2Container.length) {
                    select2Container.after(errorContainer);
                } else {
                    $field.after(errorContainer);
                }
            } else {
                // For regular fields, add after the field
                $field.after(errorContainer);
            }

            console.log(`✅ Created error container for: ${fieldName}`);
        }
    });
}

/**
 * Display validation errors inline with form fields
 */
function displayValidationErrors(errors) {
    validationErrors = errors;

    // Ensure all fields have error containers
    ensureErrorContainers();

    let errorListHtml = '<ul class="mb-0">';

    for (let field in errors) {
        const errorMessages = errors[field];

        errorMessages.forEach(msg => {
            errorListHtml += `<li class="mb-2">${msg}</li>`;
        });

        const bracketField = convertDotToBracket(field);
        const fieldElement = $(
            `[name="${bracketField}"], [name="${bracketField}[]"], [name="${field}"], [name="${field}[]"]`
        ).first();

        console.log(`🔍 Looking for field: ${field} -> ${bracketField}, found: ${fieldElement.length > 0}`);

        // Special debugging for translation fields
        if (field.includes('translations.') && field.includes('.title')) {
            console.log(`📝 Translation title field detected: ${field}`);
            const expectedId = `error-translations-${field.split('.')[1]}-title`;
            console.log(`📝 Expected error container ID: ${expectedId}`);
            console.log(`📝 Container exists:`, $(`#${expectedId}`).length > 0);
            console.log(`📝 Container element:`, $(`#${expectedId}`)[0]);
        }

        if (fieldElement.length) {
            fieldElement.addClass('is-invalid');

            // First try to use existing error container
            let errorContainer = null;

            // For translation fields, try the specific pattern
            if (field.includes('translations.')) {
                const parts = field.split('.');
                if (parts.length === 3 && parts[0] === 'translations') {
                    const langId = parts[1];
                    const fieldType = parts[2];
                    const containerId = `error-translations-${langId}-${fieldType}`;
                    errorContainer = $(`#${containerId}`);
                    console.log(`🔍 Looking for translation container: #${containerId}, found: ${errorContainer.length > 0}`);
                    if (errorContainer.length > 0) {
                        console.log(`📝 Container element:`, errorContainer[0]);
                    }
                }
            }

            // Try other common patterns if translation pattern didn't work
            if (!errorContainer || !errorContainer.length) {
                const fieldName = fieldElement.attr('name');
                const selectors = [
                    `#error-${field}`,
                    `#error-${fieldName}`,
                    `#error-${field.replace(/\./g, '-')}`,
                    `#error-${fieldName.replace(/\[|\]/g, '-').replace(/--/g, '-').replace(/-$/, '')}`
                ];

                console.log(`🔍 Trying fallback selectors:`, selectors);
                for (const selector of selectors) {
                    errorContainer = $(selector);
                    if (errorContainer.length > 0) {
                        console.log(`✅ Found with selector: ${selector}`);
                        break;
                    }
                }
            }

            if (errorContainer && errorContainer.length) {
                // Use existing error container
                const errorMessage = `<i class="uil uil-exclamation-triangle"></i> ${errorMessages[0]}`;
                console.log(`✅ Using existing container for ${field}, setting content: ${errorMessage}`);

                // Force show with multiple methods
                errorContainer.html(errorMessage);
                errorContainer.show();
                errorContainer.css('display', 'block');
                errorContainer.css('visibility', 'visible');
                errorContainer.removeClass('d-none').addClass('d-block');
                errorContainer.attr('style', 'display: block !important;');

                console.log(`✅ Container after update - visible: ${errorContainer.is(':visible')}, display: ${errorContainer.css('display')}`);
            } else {
                // Fallback: create new error message like vendor form
                const errorMsg = `<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${errorMessages[0]}</div>`;
                fieldElement.closest('.form-group').find('.error-message:not([id])').remove();

                if (fieldElement.hasClass('select2') || fieldElement.data('select2')) {
                    const select2Container = fieldElement.next('.select2-container');
                    if (select2Container.length) {
                        select2Container.after(errorMsg);
                    } else {
                        fieldElement.after(errorMsg);
                    }
                } else {
                    fieldElement.after(errorMsg);
                }
                console.log(`✅ Created new error message for field: ${field}`);
            }
        } else {
            console.log(`❌ Field element not found for: ${field} (${bracketField})`);
        }
    }

    errorListHtml += '</ul>';

    // Display validation errors in an alert at the top of the form
    if (Object.keys(errors).length > 0) {
        const isRtl = document.documentElement.dir === 'rtl' ||
                      document.documentElement.lang === 'ar' ||
                      $('html').attr('lang') === 'ar';

        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show validation-errors-alert" role="alert">
                <div class="d-flex align-items-start">
                    <i class="uil uil-exclamation-triangle me-2" style="font-size: 18px; margin-top: 2px;"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-2">${isRtl ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:'}</h6>
                        ${errorListHtml}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Remove any existing validation error alerts
        $('.validation-errors-alert').remove();

        // Add the alert to the dedicated validation alerts container
        const alertsContainer = $('#validation-alerts-container');
        if (alertsContainer.length) {
            console.log('✅ Adding alert to validation-alerts-container');
            alertsContainer.html(alertHtml).show();
        } else {
            console.log('⚠️ validation-alerts-container not found, using fallback');
            // Fallback: add at the top of card-body
            $('.card-body').prepend(alertHtml);
        }

        // Ensure the alert is visible and scroll to it
        setTimeout(() => {
            const alertElement = $('.validation-errors-alert');
            if (alertElement.length) {
                alertElement.show();
                console.log('✅ Alert should now be visible');

                // Scroll to the alert
                $('html, body').animate({
                    scrollTop: alertElement.offset().top - 100
                }, 300);
            } else {
                console.log('❌ Alert element not found after creation');
            }
        }, 100);
    }
}

/**
 * Disable required attributes on hidden form fields to prevent HTML5 validation errors
 */
function disableRequiredOnHiddenFields() {
    // Find all hidden wizard steps
    $('.wizard-step-content:not(.active)').each(function() {
        // Temporarily disable required attributes on fields in hidden steps
        $(this).find('[required]').each(function() {
            $(this).attr('data-was-required', 'true').removeAttr('required');
        });
    });

    // Also handle hidden sections within the current step
    $('#simple-product-section:hidden [required]').each(function() {
        $(this).attr('data-was-required', 'true').removeAttr('required');
    });

    $('#variants-section:hidden [required]').each(function() {
        $(this).attr('data-was-required', 'true').removeAttr('required');
    });
}

/**
 * Re-enable required attributes that were temporarily disabled
 */
function restoreRequiredAttributes() {
    $('[data-was-required="true"]').each(function() {
        $(this).attr('required', 'required').removeAttr('data-was-required');
    });
}

/**
 * Ensure productFormConfig is available
 */
function ensureProductFormConfig() {
    if (!window.productFormConfig) {
        console.warn('productFormConfig is not defined, creating fallback configuration');
        window.productFormConfig = {
            productCreated: 'Product created successfully!',
            productUpdated: 'Product updated successfully!',
            creatingProduct: 'Creating product...',
            updatingProduct: 'Updating product...',
            pleaseWait: 'Please wait...',
            redirecting: 'Redirecting...',
            errorOccurred: 'An error occurred. Please try again.',
            validationError: 'Validation Error',
            errorLabel: 'Error',
            indexRoute: '/admin/products',
            productDetails: 'Product Details',
            sku: 'SKU',
            price: 'Price',
            enableDiscountOffer: 'Enable Discount Offer',
            priceBeforeDiscount: 'Price Before Discount',
            offerEndDate: 'Offer End Date',
            stockPerRegion: 'Stock per Region',
            addNewRegion: 'Add New Region',
            region: 'Region',
            stockQuantity: 'Stock Quantity',
            totalStock: 'Total Stock',
            actionsLabel: 'Actions',
            noRegionsAddedYet: 'No regions added yet. Click "Add New Region" to start.',
            variantNumber: 'Variant',
            variantSku: 'Variant SKU',
            variantDetails: 'Variant Details',
            selectVariantKey: 'Select Variant Key',
            loadingVariantKeys: 'Loading variant keys...',
            selectVariantKeyHelper: 'Choose a variant key to configure this variant',
            remove: 'Remove',
            rootVariantsLabel: 'Root Variants',
            selectLevel: 'Level',
            selectRootVariants: 'Select Root Variants',
            selectedColon: 'Selected:',
            pleaseSelectVariant: 'Please select a variant',
            // API Routes
            categoriesRoute: '/api/categories',
            subCategoriesRoute: '/api/sub-categories',
            departmentsRoute: '/api/departments',
            indexRoute: '/admin/products'
        };
    }
}

/**
 * Handle form submission
 */
function handleFormSubmission(e) {
    console.log('Form submission started');
    e.preventDefault();

    // Ensure config is available
    ensureProductFormConfig();
    const config = window.productFormConfig;

    // Temporarily disable required attributes on hidden fields to prevent HTML5 validation errors
    disableRequiredOnHiddenFields();

    // Ensure LoadingOverlay is initialized
    if (typeof LoadingOverlay !== "undefined") {
        // Initialize if not already done
        if (!LoadingOverlay.overlay) {
            console.log('Initializing LoadingOverlay...');
            LoadingOverlay.init();
        }
    } else {
        console.error('LoadingOverlay is not defined');
        return;
    }

    const formData = new FormData(this);
    const url = $(this).attr("action");

    // Check if this is an edit operation
    const isEdit = $('input[name="_method"][value="PUT"]').length > 0;
    const loadingMessage = isEdit ?
        (config.updatingProduct || 'Updating product...') :
        (config.creatingProduct || 'Creating product...');

    // Update loading overlay text
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.querySelector('.loading-text').textContent = loadingMessage;
        overlay.querySelector('.loading-subtext').textContent = config.pleaseWait || 'Please wait...';
    }

    // Show loading overlay
    LoadingOverlay.show();

    // Animate progress bar and send request
    LoadingOverlay.animateProgressBar(30, 300).then(() => {
        return fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
    })
    .then(response => {
        // Progress to 60%
        LoadingOverlay.animateProgressBar(60, 200);

        if (!response.ok) {
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        // Progress to 90%
        return LoadingOverlay.animateProgressBar(90, 200).then(() => data);
    })
    .then(data => {
        // Complete progress bar
        return LoadingOverlay.animateProgressBar(100, 200).then(() => {
            // Restore required attributes
            restoreRequiredAttributes();

            // Show success animation with dynamic message
            const isEdit = $('input[name="_method"][value="PUT"]').length > 0;
            const successMessage = data.message ||
                (isEdit ? config.productUpdated : config.productCreated) ||
                'Product saved successfully!';

            LoadingOverlay.showSuccess(
                successMessage,
                config.redirecting || 'Redirecting...'
            );

            // Redirect after 1.5 seconds
            setTimeout(() => {
                window.location.href = data.redirect || config.indexRoute || '/admin/products';
            }, 1500);
        });
    })
    .catch(error => {
        // Hide loading overlay and reset progress bar
        LoadingOverlay.hide();

        // Restore required attributes in case of error
        restoreRequiredAttributes();

        console.log('Error:', error);

        // Handle validation errors
        if (error.errors) {
            console.log('Validation errors:', error.errors);
            displayValidationErrors(error.errors);
        } else {
            const errorMessage = error.message || 'An error occurred. Please try again.';
            console.error('Error message:', errorMessage);
            alert(errorMessage);
        }
    });
}

/**
 * Add Stock Row to Table
 */
function addStockRow() {
    // Check if regions are loaded
    if (cachedRegions && cachedRegions.length > 0) {
        addStockRowWithRegions(cachedRegions);
    } else {
        console.log("⚠️ Regions not loaded yet, waiting...");
        // Wait a bit and try again
        setTimeout(function() {
            if (cachedRegions && cachedRegions.length > 0) {
                addStockRowWithRegions(cachedRegions);
            } else {
                console.log("⚠️ Using fallback regions");
                // Use fallback regions
                const fallbackRegions = [
                    { id: 1, name: "Cairo" },
                    { id: 2, name: "Alexandria" },
                    { id: 3, name: "Giza" }
                ];
                addStockRowWithRegions(fallbackRegions);
            }
        }, 500);
    }
}

/**
 * Add Stock Row with provided regions data
 */
function addStockRowWithRegions(regions) {
    const rowIndex = $(".stock-row").length;

    let regionOptions = '<option value="">Select Region</option>';
    regions.forEach((region) => {
        // Handle both API formats: {id, text} and {id, name}
        const regionName = region.text || region.name;
        regionOptions += `<option value="${region.id}">${regionName}</option>`;
    });

    const rowNumber = rowIndex + 1;
    const rowHtml = `
        <tr class="stock-row">
            <td>${rowNumber}</td>
            <td>
                <select name="stocks[${rowIndex}][region_id]" class="form-control select2-stock" required>
                    ${regionOptions}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${rowIndex}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;

    // Try to find the correct tbody - check both simple and variant contexts
    let tbody = $("#stock-table-body");
    if (tbody.length === 0) {
        tbody = $(".stock-table-body");
    }

    if (tbody.length > 0) {
        tbody.append(rowHtml);
    } else {
        console.error("Could not find stock table body to append row");
        return;
    }

    // Show table and hide empty state
    toggleStockTableVisibility();

    // Initialize Select2 for the new row
    $(".select2-stock").select2({
        theme: "bootstrap-5",
        width: "100%",
    });

    calculateTotalStock();
    reindexStockRows();
}

/**
 * Reindex Stock Row Numbers
 */
function reindexStockRows() {
    $(".stock-row").each(function (index) {
        $(this)
            .find("td:first")
            .text(index + 1);
    });
}

/**
 * Toggle Stock Table Visibility
 */
function toggleStockTableVisibility() {
    const rowCount = $(".stock-row").length;

    if (rowCount > 0) {
        // Show table and hide empty state
        $(".stock-table-container, .variant-stock-table-container").show();
        $(".stock-empty-state, .variant-stock-empty-state").hide();
        $("#stock-table").show();
    } else {
        // Show empty state and hide table
        $(".stock-table-container, .variant-stock-table-container").hide();
        $(".stock-empty-state, .variant-stock-empty-state").show();
        $("#stock-table").show();
    }
}

/**
 * Calculate Total Stock
 */
function calculateTotalStock() {
    let total = 0;

    $(".stock-quantity").each(function () {
        const value = parseInt($(this).val()) || 0;
        total += value;
    });

    // Update both simple and variant total displays
    $("#total-stock-display, .total-stock-display, .variant-total-stock").text(total);
}

/**
 * Generate Product Details Box (reusable for both simple and variant products)
 */
function generateProductDetailsBox(type, index = null) {
    const config = window.productFormConfig || {};
    const isVariant = type === 'variant';
    const namePrefix = isVariant ? `variants[${index}]` : '';
    const idPrefix = isVariant ? `variant_${index}_` : '';
    const boxTitle = isVariant ? `${config.variantNumber || 'Variant'} ${index}` : (config.productDetails || 'Product Details');

    // No additional fields for simple products
    const additionalFields = '';

    return `
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4">
                    <i class="uil uil-receipt"></i>
                    ${boxTitle}
                </h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="form-label">${isVariant ? (config.variantSku || 'Variant SKU') : (config.sku || 'SKU')} <span class="text-danger">*</span></label>
                            <input type="text" name="${namePrefix}${isVariant ? '[sku]' : 'sku'}" id="${idPrefix}sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345" required>
                            <div class="error-message text-danger" id="error-${idPrefix}sku" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="form-label">${config.price || 'Price'} <span class="text-danger">*</span></label>
                            <input type="number" name="${namePrefix}${isVariant ? '[price]' : 'price'}" id="${idPrefix}price" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                            <div class="error-message text-danger" id="error-${idPrefix}price" style="display: none;"></div>
                        </div>
                    </div>

                    ${additionalFields}

                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <label class="form-label d-block">${config.enableDiscountOffer || 'Enable Discount Offer'}</label>
                            <div class="form-check form-switch form-switch-lg">
                                <input class="form-check-input ${isVariant ? 'variant-discount-toggle' : 'simple-discount-toggle'}" type="checkbox" role="switch" name="${namePrefix}${isVariant ? '[has_discount]' : 'has_discount'}" id="${idPrefix}has_discount" value="1">
                            </div>
                        </div>
                    </div>

                    <!-- Discount Fields (shown when discount is checked) -->
                    <div class="${isVariant ? 'variant-discount-fields' : 'simple-discount-fields'}" id="${idPrefix}discount_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label">${config.priceBeforeDiscount || 'Price Before Discount'}</label>
                                    <input type="number" name="${namePrefix}${isVariant ? '[price_before_discount]' : 'price_before_discount'}" id="${idPrefix}price_before_discount" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label">${config.offerEndDate || 'Offer End Date'}</label>
                                    <input type="date" name="${namePrefix}${isVariant ? '[offer_end_date]' : 'offer_end_date'}" id="${idPrefix}offer_end_date" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Generate Stock Management Box (reusable for both simple and variant products)
 */
function generateStockManagementBox(type, index = null) {
    const config = window.productFormConfig || {};
    const isVariant = type === 'variant';
    const dataAttr = isVariant ? `data-variant-index="${index}"` : '';
    const tableClass = isVariant ? 'variant-stock-table' : 'stock-table';
    const tbodyClass = isVariant ? 'variant-stock-rows' : 'stock-table-body';
    const emptyStateClass = isVariant ? 'variant-stock-empty-state' : 'stock-empty-state';
    const containerClass = isVariant ? 'variant-stock-table-container' : 'stock-table-container';
    const addButtonClass = isVariant ? 'add-stock-row-variant' : 'add-stock-row';
    const totalStockClass = isVariant ? 'variant-total-stock' : 'total-stock-display';

    return `
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="uil uil-package"></i>
                        ${config.stockPerRegion || 'Stock per Region'}
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ${addButtonClass}" ${dataAttr}>
                        <i class="uil uil-plus"></i> ${config.addNewRegion || 'Add New Region'}
                    </button>
                </h5>

                <!-- Empty state message -->
                <div class="${emptyStateClass} text-center py-4" ${dataAttr}>
                    <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mb-0">${config.noRegionsAddedYet || 'No regions added yet. Click "Add New Region" to start.'}</p>
                </div>

                <!-- Stock table (hidden initially) -->
                <div class="${containerClass}" ${dataAttr} style="display: none;">
                    <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                        <div class="table-responsive">
                            <table class="table mb-0 table-bordered table-hover ${tableClass}" ${dataAttr} style="width:100%">
                                <thead>
                                    <tr class="userDatatable-header">
                                        <th><span class="userDatatable-title">#</span></th>
                                        <th><span class="userDatatable-title">${config.region || 'Region'}</span></th>
                                        <th><span class="userDatatable-title">${config.stockQuantity || 'Stock Quantity'}</span></th>
                                        <th><span class="userDatatable-title">${config.actionsLabel || 'Actions'}</span></th>
                                    </tr>
                                </thead>
                                <tbody class="${tbodyClass}" ${dataAttr}>
                                    <!-- Stock rows will be added here -->
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="2" class="text-center fw-bold">${config.totalStock || 'Total Stock'}:</td>
                                        <td class="fw-bold text-primary">
                                            <span class="${totalStockClass}" ${dataAttr}>0</span>
                                        </td>
                                        <td>-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Generate Simple Product Boxes
 */
function generateSimpleProductBoxes() {
    const container = $("#simple-product-details-container");
    container.empty();

    // Generate product details box
    const productDetailsHtml = generateProductDetailsBox('simple');
    container.append(productDetailsHtml);

    // Generate stock management box
    const stockManagementHtml = generateStockManagementBox('simple');
    container.append(stockManagementHtml);

    console.log("✅ Simple product boxes generated");
}

/**
 * Populate Tax Dropdown
 */
function populateTaxDropdown() {
    const taxSelect = $('#tax_id');
    const taxes = window.productFormConfig.taxes || [];

    taxSelect.empty();
    taxSelect.append('<option value="">Select Tax</option>');

    taxes.forEach(function(tax) {
        taxSelect.append(`<option value="${tax.id}">${tax.name} (${tax.percentage}%)</option>`);
    });
}

/**
 * Add Variant Box
 */
function addVariantBox() {
    variantIndex++;
    const config = window.productFormConfig;

    const variantHtml = `
        <div class="variant-box card mb-3" data-variant-index="${variantIndex}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-primary variant-title">
                            <i class="uil uil-cube"></i>
                            ${config.variantNumber} ${variantIndex}
                        </h6>
                        <small class="text-muted variant-details-path" style="display: none;">
                            <strong>${config.variantDetails}:</strong> <span class="variant-path-text"></span>
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                        <i class="uil uil-trash-alt m-0"></i> ${config.remove}
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">${config.selectVariantKey} <span class="text-danger">*</span></label>
                        <select name="variants[${variantIndex}][key_id]" class="form-control variant-key-select" required>
                            <option value="">${config.loadingVariantKeys}</option>
                        </select>
                        <small class="text-muted">${config.selectVariantKeyHelper}</small>
                    </div>
                </div>

                <div class="variant-tree-container" style="display: none;">
                    <div class="nested-variant-levels">
                        <!-- Dynamic variant levels will be added here -->
                    </div>

                    <!-- Hidden input to store the final selected variant ID -->
                    <input type="hidden" name="variants[${variantIndex}][value_id]" class="final-variant-id">

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info variant-selection-info" style="display: none;">
                                <i class="uil uil-info-circle"></i>
                                <span class="selection-text">No variant selected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Full Product Details Section (shown when final variant is selected) -->
                <div class="variant-product-details mt-3" style="display: none;">

                    <!-- Basic Product Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-4">
                                <i class="uil uil-receipt"></i>
                                ${config.productDetails}
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${config.variantSku} <span class="text-danger">*</span></label>
                                        <input type="text" name="variants[${variantIndex}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${config.price} <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[${variantIndex}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">${config.enableDiscountOffer}</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input variant-discount-toggle" type="checkbox" role="switch" name="variants[${variantIndex}][has_discount]" value="1">
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Fields (shown when discount is checked) -->
                                <div class="variant-discount-fields" style="display: none;" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">${config.priceBeforeDiscount}</label>
                                                <input type="number" name="variants[${variantIndex}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">${config.offerEndDate}</label>
                                                <input type="date" name="variants[${variantIndex}][offer_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <!-- Stock Management (reusing existing style) -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="uil uil-package"></i>
                                    ${config.stockPerRegion}
                                </div>
                                <button type="button" class="btn btn-primary btn-sm add-stock-row-variant" data-variant-index="${variantIndex}">
                                    <i class="uil uil-plus"></i> ${config.addNewRegion}
                                </button>
                            </h5>

                            <!-- Empty state message -->
                            <div class="variant-stock-empty-state text-center py-4" data-variant-index="${variantIndex}">
                                <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mb-0">${config.noRegionsAddedYet}</p>
                            </div>

                            <!-- Stock table (hidden initially) -->
                            <div class="variant-stock-table-container" data-variant-index="${variantIndex}" style="display: none;">
                                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-bordered table-hover variant-stock-table" data-variant-index="${variantIndex}" style="width:100%">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th><span class="userDatatable-title">#</span></th>
                                                    <th><span class="userDatatable-title">${config.region}</span></th>
                                                    <th><span class="userDatatable-title">${config.stockQuantity}</span></th>
                                                    <th><span class="userDatatable-title">${config.actionsLabel}</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="variant-stock-rows" data-variant-index="${variantIndex}">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="text-center fw-bold">${config.totalStock}:</td>
                                                    <td class="fw-bold text-primary">
                                                        <span class="variant-total-stock" data-variant-index="${variantIndex}">0</span>
                                                    </td>
                                                    <td>-</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    $("#variants-container").append(variantHtml);
    toggleVariantsVisibility();

    // Load variant keys for the new box
    loadVariantKeys(variantIndex);

    // Initialize Select2 for new selects
    initializeVariantSelects(variantIndex);
}

/**
 * Load Variant Keys from API
 */
function loadVariantKeys(variantIndex) {
    const variantBox = $(`.variant-box[data-variant-index="${variantIndex}"]`);
    const keySelect = variantBox.find(".variant-key-select");

    $.ajax({
        url: "/admin/api/variant-keys",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            Accept: "application/json",
        },
        success: function (response) {
            if (response.success && response.data) {
                let options = '<option value="">Select Variant Key</option>';

                response.data.forEach((key) => {
                    options += `<option value="${key.id}">${key.name}</option>`;
                });

                keySelect.html(options);
            } else {
                keySelect.html('<option value="">Error loading keys</option>');
            }
        },
        error: function () {
            console.error("Failed to load variant keys");
            keySelect.html('<option value="">Error loading keys</option>');
        },
    });
}

/**
 * Load Nested Variants based on selected key
 */
function loadNestedVariants(variantBox, keyId) {
    const nestedContainer = variantBox.find(".nested-variant-levels");
    const selectionInfo = variantBox.find(".variant-selection-info");

    if (!keyId) {
        nestedContainer.empty();
        selectionInfo.hide();
        return;
    }

    // Clear existing levels
    nestedContainer.empty();
    selectionInfo.show().find(".selection-text").text("Loading variants...");

    // Load root level variants
    loadVariantLevel(variantBox, keyId, null, 0);
}

/**
 * Load a specific level of variants
 */
function loadVariantLevel(variantBox, keyId, parentId, level) {
    const nestedContainer = variantBox.find(".nested-variant-levels");
    const config = window.productFormConfig;

    $.ajax({
        url: "/admin/api/variants-by-key",
        method: "GET",
        data: {
            key_id: keyId,
            parent_id: parentId || "root",
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            Accept: "application/json",
        },
        success: function (response) {
            if (response.success && response.data && response.data.length > 0) {
                // Create select for this level
                const levelId = `level-${level}`;
                const levelLabel =
                    level === 0 ? (config.rootVariantsLabel || 'Root Variants') : `${config.selectLevel || 'Level'} ${level + 1}`;

                const levelHtml = `
                    <div class="variant-level mb-3" data-level="${level}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">${levelLabel} <span class="text-danger">*</span></label>
                                <select class="form-control variant-level-select" data-level="${level}">
                                    <option value="">Select ${levelLabel}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;

                nestedContainer.append(levelHtml);

                const levelSelect = nestedContainer
                    .find(`[data-level="${level}"]`)
                    .find(".variant-level-select");

                // Populate options
                let options = '<option value="">Select variant</option>';
                response.data.forEach((variant) => {
                    const hasChildrenIcon = variant.has_children ? " 🌳" : "";
                    options += `<option value="${variant.id}" data-has-children="${variant.has_children}">${variant.name}${hasChildrenIcon}</option>`;
                });

                levelSelect.html(options);

                // Initialize Select2
                levelSelect.select2({
                    theme: "bootstrap-5",
                    width: "100%",
                });

                // If there's only one option and it has no children, auto-select it
                if (
                    response.data.length === 1 &&
                    !response.data[0].has_children
                ) {
                    levelSelect.val(response.data[0].id).trigger("change");
                }

                updateVariantSelectionInfo(variantBox);
            } else {
                // No variants at this level
                if (level === 0) {
                    variantBox
                        .find(".variant-selection-info")
                        .show()
                        .find(".selection-text")
                        .text("No variants available for this key");
                }
            }
        },
        error: function () {
            console.error("Error loading variant level", level);
        },
    });
}

/**
 * Handle variant level selection change
 */
function handleVariantLevelChange(variantBox, level, selectedId, hasChildren) {
    const nestedContainer = variantBox.find(".nested-variant-levels");
    const keyId = variantBox.find(".variant-key-select").val();

    // Remove all levels after this one
    nestedContainer.find(`[data-level]`).each(function () {
        if (parseInt($(this).data("level")) > level) {
            $(this).remove();
        }
    });

    if (selectedId && hasChildren) {
        // Load next level
        loadVariantLevel(variantBox, keyId, selectedId, level + 1);
    } else if (selectedId) {
        // This is the final selection (leaf node)
        setFinalVariantSelection(variantBox, selectedId);
    }

    updateVariantSelectionInfo(variantBox);
}

/**
 * Set the final variant selection
 */
function setFinalVariantSelection(variantBox, variantId) {
    variantBox.find(".final-variant-id").val(variantId);

    // Show full product details for the final variant
    showVariantProductDetails(variantBox, variantId);
}

/**
 * Update the selection info display
 */
function updateVariantSelectionInfo(variantBox) {
    const config = window.productFormConfig;
    const selectionInfo = variantBox.find(".variant-selection-info");
    const selectionText = selectionInfo.find(".selection-text");
    const finalVariantId = variantBox.find(".final-variant-id").val();
    const productDetails = variantBox.find(".variant-product-details");
    const variantDetailsPath = variantBox.find(".variant-details-path");
    const variantPathText = variantBox.find(".variant-path-text");

    if (finalVariantId) {
        // Get the path of selected variants
        const path = [];
        variantBox.find(".variant-level-select").each(function () {
            const selectedOption = $(this).find("option:selected");
            if (selectedOption.val()) {
                path.push(selectedOption.text().replace(" 🌳", ""));
            }
        });

        if (path.length > 0) {
            const pathString = path.join(" - ");

            // Update selection info
            selectionText.html(
                `<strong>${config.selectedColon}</strong> ${path.join(" → ")}`
            );
            selectionInfo
                .removeClass("alert-info")
                .addClass("alert-success")
                .show();

            // Update variant title with details path
            variantPathText.text(pathString);
            variantDetailsPath.show();

            // Product details are shown by setFinalVariantSelection function
        }
    } else {
        selectionText.text(config.pleaseSelectVariant);
        selectionInfo
            .removeClass("alert-success")
            .addClass("alert-info")
            .show();

        // Hide variant details path
        variantDetailsPath.hide();

        // Hide product details when no final variant is selected
        productDetails.hide();
    }
}

/**
 * Load Variant Values for a specific key
 */
function loadVariantValues(variantIndex, keyId) {
    const variantBox = $(`.variant-box[data-variant-index="${variantIndex}"]`);
    const valueSelect = variantBox.find(".variant-value-select");

    // Show loading state
    valueSelect.html('<option value="">Loading values...</option>');

    $.ajax({
        url: "/admin/api/variants-by-key",
        method: "GET",
        data: {
            key_id: keyId
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            Accept: "application/json",
        },
        success: function (response) {
            if (response.success && response.data) {
                let options = '<option value="">Select Variant Value</option>';

                response.data.forEach((value) => {
                    options += `<option value="${value.id}">${value.name}</option>`;
                });

                valueSelect.html(options);
            } else {
                valueSelect.html('<option value="">Error loading values</option>');
            }
        },
        error: function () {
            console.error("Failed to load variant values for key:", keyId);
            valueSelect.html('<option value="">Error loading values</option>');
        },
    });
}

/**
 * Initialize Variant Selects2 for variant selects
 */
function initializeVariantSelects(variantIndex) {
    const variantBox = $(`.variant-box[data-variant-index="${variantIndex}"]`);

    // Initialize Select2 for the key select
    variantBox.find(".variant-key-select").select2({
        theme: "bootstrap-5",
        width: "100%",
    });

    // Level selects will be initialized dynamically when created
}

/**
 * Show variant product details when final variant is selected
 */
function showVariantProductDetails(variantBox, variantId) {
    const productDetails = variantBox.find(".variant-product-details");

    // Show the product details section
    productDetails.show();

    // Initialize discount toggle functionality
    const discountToggle = variantBox.find(".variant-discount-toggle");
    const discountFields = variantBox.find(".variant-discount-fields");

    discountToggle.on("change", function () {
        if ($(this).is(":checked")) {
            discountFields.show();
        } else {
            discountFields.hide();
            // Clear discount fields when disabled
            discountFields.find("input").val("");
        }
    });
}

/**
 * Add stock row for variant (using cached regions)
 */
function addVariantStockRow(variantIndex) {
    // Use cached regions data
    if (cachedRegions && cachedRegions.length > 0) {
        addVariantStockRowWithRegions(variantIndex, cachedRegions);
    } else {
        // If regions not loaded yet, wait a bit and try again
        console.log("⏳ Regions not loaded yet for variant, waiting...");
        setTimeout(function() {
            if (cachedRegions && cachedRegions.length > 0) {
                addVariantStockRowWithRegions(variantIndex, cachedRegions);
            } else {
                console.log("⚠️ Using fallback regions for variant");
                addVariantStockRowWithFallback(variantIndex);
            }
        }, 500);
    }
}

/**
 * Add variant stock row with regions data (reusing existing logic)
 */
function addVariantStockRowWithRegions(variantIndex, regions) {
    const stockRowsContainer = $(
        `.variant-stock-rows[data-variant-index="${variantIndex}"]`
    );
    const rowIndex = stockRowsContainer.find("tr").length;

    let regionOptions = '<option value="">Select Region</option>';
    regions.forEach(function (region) {
        // Handle both API formats: {id, text} and {id, name}
        const regionName = region.text || region.name;
        regionOptions += `<option value="${region.id}">${regionName}</option>`;
    });

    const stockRowHtml = `
        <tr class="variant-stock-row" data-variant-index="${variantIndex}" data-row-index="${rowIndex}">
            <td class="text-center">${rowIndex + 1}</td>
            <td>
                <select name="variants[${variantIndex}][stock][${rowIndex}][region_id]" class="form-control region-select" required>
                    ${regionOptions}
                </select>
            </td>
            <td>
                <input type="number" name="variants[${variantIndex}][stock][${rowIndex}][quantity]"
                       class="form-control variant-stock-quantity" min="0" value="0" required>
            </td>
            <td class="actions">
                <button type="button" class="btn btn-sm btn-danger remove-variant-stock-row m-0" data-variant-index="${variantIndex}">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;

    stockRowsContainer.append(stockRowHtml);

    // Initialize Select2 for the new region select
    const newRow = stockRowsContainer.find("tr").last();
    newRow.find(".region-select").select2({
        theme: "bootstrap-5",
        width: "100%",
    });

    // Show table and hide empty state
    $(
        `.variant-stock-table-container[data-variant-index="${variantIndex}"]`
    ).show();
    $(
        `.variant-stock-empty-state[data-variant-index="${variantIndex}"]`
    ).hide();

    // Update totals
    updateVariantStockTotals(variantIndex);
}

/**
 * Fallback function with hardcoded regions for variants
 */
function addVariantStockRowWithFallback(variantIndex) {
    const regions = [
        { id: 1, text: "Cairo" },
        { id: 2, text: "Alexandria" },
        { id: 3, text: "Giza" },
        { id: 4, text: "Luxor" },
        { id: 5, text: "Aswan" },
    ];

    addVariantStockRowWithRegions(variantIndex, regions);
}

/**
 * Update variant stock totals (simplified version)
 */
function updateVariantStockTotals(variantIndex) {
    const stockRowsContainer = $(
        `.variant-stock-rows[data-variant-index="${variantIndex}"]`
    );
    const totalStockElement = $(
        `.variant-total-stock[data-variant-index="${variantIndex}"]`
    );

    let totalStock = 0;

    stockRowsContainer.find(".variant-stock-quantity").each(function () {
        const quantity = parseInt($(this).val()) || 0;
        totalStock += quantity;
    });

    totalStockElement.text(totalStock);
}

/**
 * Toggle Variants Visibility
 */
function toggleVariantsVisibility() {
    const variantCount = $(".variant-box").length;

    if (variantCount > 0) {
        $("#variants-empty-state").hide();
        $("#variants-container").show();
    } else {
        $("#variants-empty-state").show();
        $("#variants-container").hide();
    }
}

/**
 * Reindex Variants
 */
function reindexVariants() {
    $(".variant-box").each(function (index) {
        const newIndex = index + 1;
        $(this)
            .find("h6")
            .html(`<i class="uil uil-cube"></i> Variant ${newIndex}`);
    });
}

/**
 * Add Additional Image
 */
function addAdditionalImage() {
    console.log('🖼️ Adding new additional image...');

    const config = window.productFormConfig;
    const container = $('#additional-images-container');

    console.log('📦 Container found:', container.length > 0);
    console.log('📦 Container display:', container.css('display'));

    const imageCount = container.find('.additional-image-item').length + 1;
    const uniqueId = 'new_image_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

    console.log('🆔 Unique ID:', uniqueId);
    console.log('📊 Image count:', imageCount);

    const imageHtml = `
        <div class="col-md-6 col-lg-4 mb-3 additional-image-item" data-index="${imageCount}">
            <div class="form-group position-relative">
                <div class="d-flex justify-content-between align-items-center mb-2" style="display: flex !important; justify-content: space-between !important; align-items: center !important; width: 100%;">
                    <label class="il-gray fs-14 fw-500 mb-0" style="margin-bottom: 0 !important; flex: 1;">
                        ${config.additionalImage} ${imageCount}
                    </label>
                    <button type="button" class="btn btn-sm btn-danger remove-image-box-btn" title="Remove this image box" style="flex-shrink: 0; margin-left: 8px; padding: 6px 10px; font-size: 12px; min-width: auto; height: auto; line-height: 1; white-space: nowrap;">
                        <i class="uil uil-trash-alt" style="margin: 0; margin-right: 3px;"></i> Remove
                    </button>
                </div>
                <div class="image-upload-wrapper">
                    <div class="image-preview-container" id="${uniqueId}-preview-container" data-target="${uniqueId}">
                        <div class="image-placeholder" id="${uniqueId}-placeholder">
                            <i class="uil uil-image-plus"></i>
                            <p>${config.clickToUploadImage}</p>
                            <small>${config.recommendedSize}</small>
                        </div>
                        <div class="image-overlay">
                            <button type="button" class="btn-change-image" data-target="${uniqueId}">
                                <i class="uil uil-camera"></i> ${config.change}
                            </button>
                            <button type="button" class="btn-remove-image remove-additional-image-btn" data-target="${uniqueId}" style="display: none;">
                                <i class="uil uil-trash-alt"></i> ${config.remove}
                            </button>
                        </div>
                    </div>
                    <input type="file"
                           class="d-none image-file-input"
                           id="${uniqueId}"
                           name="additional_images[]"
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           data-preview="${uniqueId}">
                </div>
            </div>
        </div>
    `;

    console.log('📝 Appending image HTML to container...');
    container.append(imageHtml);

    console.log('✅ Image HTML appended');
    console.log('📦 Container now has', container.find('.additional-image-item').length, 'items');

    // Force container to display
    container.css('display', 'flex');

    // Initialize image upload handlers for the new image
    initializeImageUploadHandler(uniqueId);

    // Show container and hide empty state
    toggleAdditionalImagesVisibility();

    console.log('✅ Additional image added successfully');

    // Scroll to the new image
    setTimeout(function() {
        const newImage = container.find('.additional-image-item').last();
        if (newImage.length) {
            newImage[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, 100);
}

/**
 * Initialize Image Upload Handler for a specific image
 */
function initializeImageUploadHandler(uniqueId) {
    const input = $(`#${uniqueId}`);
    const container = $(`#${uniqueId}-preview-container`);
    const placeholder = $(`#${uniqueId}-placeholder`);
    const changeBtn = container.find('.btn-change-image');
    const removeBtn = container.find('.btn-remove-image');

    // Click on container to select file
    container.on('click', function(e) {
        if (!$(e.target).closest('.btn-change-image') && !$(e.target).closest('.btn-remove-image')) {
            input.click();
        }
    });

    // Change button click
    changeBtn.on('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        input.click();
    });

    // File selection
    input.on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                let previewImg = $(`#${uniqueId}-preview-img`);

                if (previewImg.length === 0) {
                    const img = $(`<img id="${uniqueId}-preview-img" class="preview-image" src="${event.target.result}">`);
                    container.prepend(img);
                } else {
                    previewImg.attr('src', event.target.result);
                }

                placeholder.hide();
                removeBtn.show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove button click
    removeBtn.on('click', function(e) {
        e.stopPropagation();
        input.val('');

        const previewImg = $(`#${uniqueId}-preview-img`);
        if (previewImg.length > 0) {
            previewImg.remove();
        }

        placeholder.show();
        removeBtn.hide();
    });
}

/**
 * Toggle Additional Images Visibility
 */
function toggleAdditionalImagesVisibility() {
    const container = $('#additional-images-container');
    const emptyState = $('#additional-images-empty-state');
    const imageCount = container.find('.additional-image-item').length;

    if (imageCount > 0) {
        container.show();
        emptyState.hide();
    } else {
        container.hide();
        emptyState.show();
    }
}

/**
 * Validate a specific step
 */
function validateStep(step) {
    console.log(`🔍 Validating step ${step}...`);

    let isValid = true;
    const errors = {};

    switch(step) {
        case 1:
            isValid = validateStep1(errors);
            break;
        case 2:
            isValid = validateStep2(errors);
            break;
        case 3:
            isValid = validateStep3(errors);
            break;
        case 4:
            isValid = validateStep4(errors);
            break;
    }

    if (!isValid) {
        console.log(`❌ Step ${step} validation failed:`, errors);
        displayValidationErrors(errors);
        return false;
    }

    console.log(`✅ Step ${step} validation passed`);
    return true;
}

/**
 * Validate Step 1: Basic Information
 * Required: Product titles (EN & AR), SKU, Brand, Vendor, Department, Category, Sub-category, Tax
 */
function validateStep1(errors) {
    let isValid = true;

    // Get all language IDs from the form
    const languages = [];
    $('input[name^="translations["][name$="][title]"]').each(function() {
        const match = $(this).attr('name').match(/translations\[(\d+)\]/);
        if (match && !languages.includes(match[1])) {
            languages.push(match[1]);
        }
    });

    // Validate titles for each language
    languages.forEach(langId => {
        const titleInput = $(`input[name="translations[${langId}][title]"]`);
        const titleValue = titleInput.val().trim();

        if (!titleValue) {
            const langName = titleInput.closest('.col-md-6').find('label').text();
            errors[`translations.${langId}.title`] = [`${langName} is required`];
            isValid = false;
        }
    });

    // Validate SKU
    const skuInput = $('input[name="sku"]');
    if (skuInput.length && !skuInput.val().trim()) {
        errors['sku'] = ['SKU is required'];
        isValid = false;
    }

    // Validate Brand
    const brandSelect = $('#brand_id');
    if (brandSelect.length && !brandSelect.val()) {
        errors['brand_id'] = ['Brand is required'];
        isValid = false;
    }

    // Validate Vendor
    const vendorSelect = $('#vendor_id');
    if (vendorSelect.length && !vendorSelect.val()) {
        errors['vendor_id'] = ['Vendor is required'];
        isValid = false;
    }

    // Validate Department
    const departmentSelect = $('#department_id');
    if (departmentSelect.length && !departmentSelect.val()) {
        errors['department_id'] = ['Department is required'];
        isValid = false;
    }

    // Validate Category
    const categorySelect = $('#category_id');
    if (categorySelect.length && !categorySelect.val()) {
        errors['category_id'] = ['Category is required'];
        isValid = false;
    }

    // Sub-category is optional - no validation needed

    // Validate Tax
    const taxSelect = $('#tax_id');
    if (taxSelect.length && !taxSelect.val()) {
        errors['tax_id'] = ['Tax is required'];
        isValid = false;
    }

    // Validate Max Per Order
    const maxPerOrder = $('input[name="max_per_order"]');
    if (maxPerOrder.length && !maxPerOrder.val()) {
        errors['max_per_order'] = ['Max per order is required'];
        isValid = false;
    } else if (maxPerOrder.length && parseInt(maxPerOrder.val()) <= 0) {
        errors['max_per_order'] = ['Max per order must be greater than 0'];
        isValid = false;
    }

    // Validate Points
    const points = $('input[name="points"]');
    if (points.length && points.val() === '') {
        errors['points'] = ['Points is required'];
        isValid = false;
    } else if (points.length && parseInt(points.val()) < 0) {
        errors['points'] = ['Points must be a valid number'];
        isValid = false;
    }

    return isValid;
}

/**
 * Validate Step 2: Details
 * Required: Main image, Description (EN & AR)
 */
function validateStep2(errors) {
    let isValid = true;

    // Validate Main Image
    const mainImageInput = $('input[name="main_image"]');
    const mainImagePreview = $('#main_image-preview-img');

    // Check if main image is uploaded (either new file or existing image)
    if (mainImageInput.length) {
        const hasNewImage = mainImageInput.val() !== '';
        const hasExistingImage = mainImagePreview.length > 0 && mainImagePreview.attr('src');

        if (!hasNewImage && !hasExistingImage) {
            errors['main_image'] = ['Main image is required'];
            isValid = false;
        }
    }

    // Details field is optional, so no validation needed for it
    // Step 2 only requires main image which is already validated above

    return isValid;
}

/**
 * Validate Step 3: Pricing & Inventory
 * Required: Configuration type, and based on type:
 *   - Simple: SKU, Price, Stock (at least one region)
 *   - Variants: At least one variant with all required fields
 */
function validateStep3(errors) {
    let isValid = true;

    // Validate Configuration Type
    const configType = $('#configuration_type').val();
    if (!configType) {
        errors['configuration_type'] = ['Product type is required'];
        isValid = false;
    }

    if (configType === 'simple') {
        // Validate Simple Product
        isValid = validateSimpleProduct(errors) && isValid;
    } else if (configType === 'variants') {
        // Validate Variants
        isValid = validateVariants(errors) && isValid;
    }

    return isValid;
}

/**
 * Validate Simple Product
 */
function validateSimpleProduct(errors) {
    let isValid = true;

    // Validate SKU
    const sku = $('input[name="sku"]');
    if (sku.length && !sku.val().trim()) {
        errors['sku'] = ['SKU is required'];
        isValid = false;
    }

    // Validate Price
    const price = $('input[name="price"]');
    if (price.length && !price.val()) {
        errors['price'] = ['Price is required'];
        isValid = false;
    } else if (price.length && parseFloat(price.val()) <= 0) {
        errors['price'] = ['Price must be greater than 0'];
        isValid = false;
    }

    // Validate Stock (at least one region)
    const stockRows = $('.stock-row').length;
    if (stockRows === 0) {
        errors['stocks'] = ['At least one region stock is required'];
        isValid = false;
    } else {
        // Validate each stock row
        $('.stock-row').each(function(index) {
            const regionSelect = $(this).find('select[name*="region_id"]');
            const quantityInput = $(this).find('input[name*="quantity"]');

            if (!regionSelect.val()) {
                errors[`stocks.${index}.region_id`] = ['Region is required'];
                isValid = false;
            }

            if (!quantityInput.val() || parseInt(quantityInput.val()) < 0) {
                errors[`stocks.${index}.quantity`] = ['Quantity must be a valid number'];
                isValid = false;
            }
        });
    }

    return isValid;
}

/**
 * Validate Variants
 */
function validateVariants(errors) {
    let isValid = true;

    const variantBoxes = $('.variant-box');

    if (variantBoxes.length === 0) {
        errors['variants'] = ['At least one variant is required'];
        return false;
    }

    variantBoxes.each(function(index) {
        const variantBox = $(this);
        const variantIndex = variantBox.data('variant-index');

        // Validate variant key is selected
        const keySelect = variantBox.find('.variant-key-select');
        if (!keySelect.val()) {
            errors[`variants.${variantIndex}.key_id`] = ['Variant key is required'];
            isValid = false;
        }

        // Validate final variant is selected
        const finalVariantId = variantBox.find('.final-variant-id').val();
        if (!finalVariantId) {
            errors[`variants.${variantIndex}.value_id`] = ['Variant value is required'];
            isValid = false;
        }

        // Validate product details if variant is selected
        if (finalVariantId) {
            // Validate SKU
            const skuInput = variantBox.find(`input[name="variants[${variantIndex}][sku]"]`);
            if (!skuInput.val().trim()) {
                errors[`variants.${variantIndex}.sku`] = ['Variant SKU is required'];
                isValid = false;
            }

            // Validate Price
            const priceInput = variantBox.find(`input[name="variants[${variantIndex}][price]"]`);
            if (!priceInput.val()) {
                errors[`variants.${variantIndex}.price`] = ['Variant price is required'];
                isValid = false;
            } else if (parseFloat(priceInput.val()) <= 0) {
                errors[`variants.${variantIndex}.price`] = ['Variant price must be greater than 0'];
                isValid = false;
            }

            // Validate Stock (at least one region)
            const stockRows = variantBox.find(`.variant-stock-rows[data-variant-index="${variantIndex}"] tr`).length;
            if (stockRows === 0) {
                errors[`variants.${variantIndex}.stock`] = ['At least one region stock is required for this variant'];
                isValid = false;
            } else {
                // Validate each stock row
                variantBox.find(`.variant-stock-rows[data-variant-index="${variantIndex}"] tr`).each(function(rowIndex) {
                    const regionSelect = $(this).find('select[name*="region_id"]');
                    const quantityInput = $(this).find('input[name*="quantity"]');

                    if (!regionSelect.val()) {
                        errors[`variants.${variantIndex}.stock.${rowIndex}.region_id`] = ['Region is required'];
                        isValid = false;
                    }

                    if (!quantityInput.val() || parseInt(quantityInput.val()) < 0) {
                        errors[`variants.${variantIndex}.stock.${rowIndex}.quantity`] = ['Quantity must be a valid number'];
                        isValid = false;
                    }
                });
            }
        }
    });

    return isValid;
}

/**
 * Validate Step 4: SEO & Images
 * Required: Meta title and description for each language
 */
function validateStep4(errors) {
    let isValid = true;

    // Get all language IDs from the form
    const languages = [];
    $('input[name^="translations["][name$="][meta_title]"]').each(function() {
        const match = $(this).attr('name').match(/translations\[(\d+)\]/);
        if (match && !languages.includes(match[1])) {
            languages.push(match[1]);
        }
    });

    // Validate meta titles and descriptions for each language
    languages.forEach(langId => {
        const metaTitleInput = $(`input[name="translations[${langId}][meta_title]"]`);
        const metaDescInput = $(`textarea[name="translations[${langId}][meta_description]"]`);

        const metaTitleValue = metaTitleInput.val().trim();
        const metaDescValue = metaDescInput.val().trim();

        if (!metaTitleValue) {
            const langName = metaTitleInput.closest('.col-md-6').find('label').text();
            errors[`translations.${langId}.meta_title`] = [`${langName} is required`];
            isValid = false;
        }

        if (!metaDescValue) {
            const langName = metaDescInput.closest('.col-md-6').find('label').text();
            errors[`translations.${langId}.meta_description`] = [`${langName} is required`];
            isValid = false;
        }
    });

    return isValid;
}

