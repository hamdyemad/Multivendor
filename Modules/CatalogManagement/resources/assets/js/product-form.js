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
jQuery(document).ready(function ($) {
    console.log("✅ Product form jQuery ready");

    // Function to attach event handlers
    function attachEventHandlers() {
        console.log("🔧 Attaching event handlers to Select2 dropdowns...");

        // Check department select status
        const deptElement = $("#department_id");
        console.log("📍 Department element found:", deptElement.length > 0);
        console.log(
            "📍 Department has Select2:",
            deptElement.hasClass("select2-hidden-accessible")
        );
        console.log("📍 Department value:", deptElement.val());

        // Remove any existing handlers to prevent duplicates
        $("#department_id").off(
            "change.productForm select2:select.productForm"
        );

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
    showStep(currentStep);

    // Next button
    $("#nextBtn").on("click", function () {
        console.log("📍 Next button clicked. Current step:", currentStep);

        // Clear previous errors
        clearAllErrors();

        // Proceed to next step
        currentStep++;
        if (currentStep > totalSteps) currentStep = totalSteps;
        showStep(currentStep);

        // No review step - step 4 is now the final step
    });

    // Previous button
    $("#prevBtn").on("click", function () {
        currentStep--;
        if (currentStep < 1) currentStep = 1;
        showStep(currentStep);
    });

    // Click on wizard step navigation
    $(".wizard-step-nav").on("click", function () {
        console.log("🖱️ Wizard step clicked!");
        const targetStep = parseInt($(this).data("step"));
        console.log("Clicked step:", targetStep);

        // Clear errors when navigating
        clearAllErrors();

        currentStep = targetStep;
        showStep(currentStep);

        // No review step - step 4 is now the final step
    });

    // Edit button in review page
    $(document).on("click", ".edit-step", function () {
        const targetStep = parseInt($(this).data("step"));

        // Clear any existing errors when editing
        clearAllErrors();

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
    $("#productForm").on("submit", handleFormSubmission);

    // Configuration Type Toggle
    $("#configuration_type").on("change", function () {
        const selectedType = $(this).val();

        if (selectedType === "simple") {
            $("#simple-product-section").show();
            $("#variants-section").hide();
        } else if (selectedType === "variants") {
            $("#simple-product-section").hide();
            $("#variants-section").show();
        } else {
            // No selection - hide both sections
            $("#simple-product-section").hide();
            $("#variants-section").hide();
        }
    });

    // Discount Checkbox Toggle
    $("#has_discount").on("change", function () {
        if ($(this).is(":checked")) {
            $("#discount-fields").slideDown();
        } else {
            $("#discount-fields").slideUp();
            $("#price_before_discount").val("");
            $("#offer_end_date").val("");
        }
    });

    // Stock Row Index
    let stockRowIndex = 0;

    // Add Stock Row
    $("#add-stock-row").on("click", function () {
        addStockRow();
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

    // Additional images functionality
    initializeAdditionalImages();

    console.log("✅ Product form navigation initialized");
});

/**
 * Load regions data once on page load
 */
function loadRegionsData() {
    console.log("🌍 Loading regions data...");

    $.ajax({
        url: "/api/regions?select2=1",
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.results && response.results.length > 0) {
                cachedRegions = response.results;
                console.log(`✅ Cached ${cachedRegions.length} regions`);
            } else if (response.data && response.data.items && response.data.items.length > 0) {
                // Handle different API response format
                cachedRegions = response.data.items.map(item => ({
                    id: item.id,
                    text: item.name
                }));
                console.log(`✅ Cached ${cachedRegions.length} regions (alternative format)`);
            } else {
                console.log("⚠️ No regions from API, using fallback");
                cachedRegions = [
                    { id: 1, text: "Cairo" },
                    { id: 2, text: "Alexandria" },
                    { id: 3, text: "Giza" },
                    { id: 4, text: "Luxor" },
                    { id: 5, text: "Aswan" },
                    { id: 6, text: "Beheira" },
                    { id: 7, text: "Fayoum" },
                    { id: 8, text: "Gharbia" },
                    { id: 9, text: "Ismailia" },
                    { id: 10, text: "Menofia" }
                ];
            }
        },
        error: function (xhr, status, error) {
            console.log("❌ API error, using fallback regions");
            cachedRegions = [
                { id: 1, text: "Cairo" },
                { id: 2, text: "Alexandria" },
                { id: 3, text: "Giza" },
                { id: 4, text: "Luxor" },
                { id: 5, text: "Aswan" },
                { id: 6, text: "Beheira" },
                { id: 7, text: "Fayoum" },
                { id: 8, text: "Gharbia" },
                { id: 9, text: "Ismailia" },
                { id: 10, text: "Menofia" }
            ];
        }
    });
}

/**
 * Show/Hide wizard steps
 */
function showStep(step) {
    // Hide all steps
    $(".wizard-step-content").each(function () {
        $(this).removeClass("active").css("display", "none");
    });

    // Show target step
    const targetStep = $(`.wizard-step-content[data-step="${step}"]`);

    if (targetStep.length) {
        targetStep.addClass("active").css("display", "block");
    } else {
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
                fieldElement
                    .closest(".form-group")
                    .find(".error-message")
                    .remove();

                const errorMsg = `<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${validationErrors[field][0]}</div>`;

                if (
                    fieldElement.hasClass("select2") ||
                    fieldElement.data("select2")
                ) {
                    const select2Container =
                        fieldElement.next(".select2-container");
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
 * Clear all validation errors
 */
function clearAllErrors() {
    $(".error-message").remove();
    $(".is-invalid").removeClass("is-invalid");
    validationErrors = {};
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
 * Display validation errors inline with form fields
 */
function displayValidationErrors(errors) {
    validationErrors = errors;

    for (let field in errors) {
        const errorMessages = errors[field];

        const bracketField = convertDotToBracket(field);
        const fieldElement = $(
            `[name="${bracketField}"], [name="${bracketField}[]"], [name="${field}"], [name="${field}[]"]`
        ).first();

        if (fieldElement.length) {
            fieldElement.addClass("is-invalid");

            const errorMsg = `<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${errorMessages[0]}</div>`;
            fieldElement.closest(".form-group").find(".error-message").remove();

            if (
                fieldElement.hasClass("select2") ||
                fieldElement.data("select2")
            ) {
                const select2Container =
                    fieldElement.next(".select2-container");
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
 * Handle form submission
 */
function handleFormSubmission(e) {
    e.preventDefault();

    const config = window.productFormConfig;
    if (!config) {
        return;
    }

    // Clear previous errors
    clearAllErrors();

    // Temporarily disable required attributes on hidden fields to prevent HTML5 validation errors
    disableRequiredOnHiddenFields();

    // Show loading overlay
    if (typeof LoadingOverlay !== "undefined") {
        LoadingOverlay.show();
        LoadingOverlay.progressSequence([30, 60, 90]);
    }

    const formData = new FormData(this);
    const url = $(this).attr("action");

    $.ajax({
        url: url,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (typeof LoadingOverlay !== "undefined") {
                LoadingOverlay.animateProgressBar(100);
            }

            if (response.success) {
                if (typeof LoadingOverlay !== "undefined") {
                    LoadingOverlay.showSuccess(
                        response.message || "Product created successfully!",
                        "Redirecting..."
                    );
                }

                setTimeout(function () {
                    window.location.href =
                        config.indexRoute || "/admin/products";
                }, 1500);
            }
        },
        error: function (xhr) {
            if (typeof LoadingOverlay !== "undefined") {
                LoadingOverlay.hide();
            }

            // Restore required attributes in case of error
            restoreRequiredAttributes();

            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                // Display validation errors inline
                displayValidationErrors(errors);
            } else {
                alert("An error occurred. Please try again.");
            }
        },
    });
}

/**
 * Add Stock Row to Table
 */
function addStockRow() {
    // Use cached regions data
    if (cachedRegions && cachedRegions.length > 0) {
        addStockRowWithRegions(cachedRegions);
    } else {
        // If regions not loaded yet, wait a bit and try again
        console.log("⏳ Regions not loaded yet, waiting...");
        setTimeout(function() {
            if (cachedRegions && cachedRegions.length > 0) {
                addStockRowWithRegions(cachedRegions);
            } else {
                console.log("⚠️ Using fallback regions");
                addStockRowWithFallback();
            }
        }, 500);
    }
}

/**
 * Fallback function with hardcoded regions
 */
function addStockRowWithFallback() {
    const regions = [
        { id: 1, name: "Cairo" },
        { id: 2, name: "Alexandria" },
        { id: 3, name: "Giza" },
        { id: 4, name: "Dakahlia" },
        { id: 5, name: "Red Sea" },
        { id: 6, name: "Beheira" },
        { id: 7, name: "Fayoum" },
        { id: 8, name: "Gharbia" },
        { id: 9, name: "Ismailia" },
        { id: 10, name: "Menofia" },
    ];

    const rowIndex = $(".stock-row").length;

    let regionOptions = '<option value="">Select Region</option>';
    regions.forEach((region) => {
        regionOptions += `<option value="${region.id}">${region.name}</option>`;
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

    $("#stock-rows").append(rowHtml);

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

    $("#stock-rows").append(rowHtml);

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
        // Show table, hide empty state
        $("#stock-table-container").show();
        $("#stock-empty-state").hide();
    } else {
        // Hide table, show empty state
        $("#stock-table-container").hide();
        $("#stock-empty-state").show();
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

    $("#total-stock").text(total);
}

/**
 * Add Variant Box
 */
function addVariantBox() {
    variantIndex++;

    const variantHtml = `
        <div class="variant-box card mb-3" data-variant-index="${variantIndex}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-primary variant-title">
                            <i class="uil uil-cube"></i>
                            Variant ${variantIndex}
                        </h6>
                        <small class="text-muted variant-details-path" style="display: none;">
                            <strong>Variant Details:</strong> <span class="variant-path-text"></span>
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                        <i class="uil uil-trash-alt m-0"></i> Remove
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Variant Configuration Key <span class="text-danger">*</span></label>
                        <select name="variants[${variantIndex}][key_id]" class="form-control variant-key-select" required>
                            <option value="">Loading variant keys...</option>
                        </select>
                        <small class="text-muted">Select the type of variant (e.g., Color, Size, Material)</small>
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
                                Product Details
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Variant SKU <span class="text-danger">*</span></label>
                                        <input type="text" name="variants[${variantIndex}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Price <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[${variantIndex}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">Enable Discount Offer</label>
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
                                                <label class="form-label">Price Before Discount</label>
                                                <input type="number" name="variants[${variantIndex}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Offer End Date</label>
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
                                    Stock per Region
                                </div>
                                <button type="button" class="btn btn-primary btn-sm add-stock-row-variant" data-variant-index="${variantIndex}">
                                    <i class="uil uil-plus"></i> Add New Region
                                </button>
                            </h5>

                            <!-- Empty state message -->
                            <div class="variant-stock-empty-state text-center py-4" data-variant-index="${variantIndex}">
                                <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mb-0">No regions added yet. Click "Add New Region" to start.</p>
                            </div>

                            <!-- Stock table (hidden initially) -->
                            <div class="variant-stock-table-container" data-variant-index="${variantIndex}" style="display: none;">
                                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-bordered table-hover variant-stock-table" data-variant-index="${variantIndex}" style="width:100%">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th><span class="userDatatable-title">#</span></th>
                                                    <th><span class="userDatatable-title">Region</span></th>
                                                    <th><span class="userDatatable-title">Stock Quantity</span></th>
                                                    <th><span class="userDatatable-title">Actions</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="variant-stock-rows" data-variant-index="${variantIndex}">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="text-center fw-bold">Total Stock:</td>
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
                    level === 0 ? "Root Variants" : `Level ${level + 1}`;

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
                `<strong>Selected:</strong> ${path.join(" → ")}`
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
        selectionText.text("Please select a variant");
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
 * Initialize Select2 for variant selects
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
        regionOptions += `<option value="${region.id}">${region.text}</option>`;
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
 * Initialize additional images functionality using x-image-upload component pattern
 */
function initializeAdditionalImages() {
    // Add image button
    $('#add-additional-image-btn').on('click', function() {
        addAdditionalImageUpload();
    });
    
    // Remove additional image (event delegation)
    $(document).on('click', '.remove-additional-image', function() {
        $(this).closest('.additional-image-item').remove();
        toggleAdditionalImagesVisibility();
        reindexAdditionalImages();
    });
    
    function addAdditionalImageUpload() {
        // Get the current count of existing images to determine the next index
        const currentCount = $('.additional-image-item').length;
        const nextIndex = currentCount + 1;
        const uniqueId = 'additional_image_' + Date.now(); // Use timestamp for unique ID
        
        const imageHtml = `
            <div class="col-md-4 mb-3 additional-image-item" data-index="${nextIndex}">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Additional Image ${nextIndex}</small>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-additional-image">
                                <i class="uil uil-trash-alt m-0"></i>
                            </button>
                        </div>
                        <p class="text-muted mb-2" style="font-size: 11px;">
                            <i class="uil uil-info-circle me-1"></i>
                            Recommended: 800x800px
                        </p>
                        <div class="form-group">
                            <div class="image-upload-wrapper">
                                <div class="image-preview-container" id="${uniqueId}-preview-container" data-target="${uniqueId}">
                                    <div class="image-placeholder" id="${uniqueId}-placeholder">
                                        <i class="uil uil-image-plus"></i>
                                        <p>Click to upload image</p>
                                        <small>Recommended: 800x800px</small>
                                    </div>
                                    <div class="image-overlay">
                                        <button type="button" class="btn-change-image" data-target="${uniqueId}">
                                            <i class="uil uil-camera"></i> Change
                                        </button>
                                        <button type="button" class="btn-remove-image" data-target="${uniqueId}" style="display: none;">
                                            <i class="uil uil-trash-alt"></i> Remove
                                        </button>
                                    </div>
                                </div>
                                <input type="file" class="d-none image-file-input" id="${uniqueId}" name="additional_images[]" accept="image/jpeg,image/png,image/jpg,image/webp" data-preview="${uniqueId}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#additional-images-container').append(imageHtml);
        
        // Initialize the image upload functionality for this new component
        initializeImageUploadComponent(uniqueId);
        
        toggleAdditionalImagesVisibility();
    }
    
    function initializeImageUploadComponent(uniqueId) {
        const input = document.getElementById(uniqueId);
        const container = document.getElementById(uniqueId + '-preview-container');
        const placeholder = document.getElementById(uniqueId + '-placeholder');
        const changeBtn = container.querySelector('.btn-change-image');
        const removeBtn = container.querySelector('.btn-remove-image');
        
        // Click on container to select file
        container.addEventListener('click', (e) => {
            if (!e.target.closest('.btn-change-image') && !e.target.closest('.btn-remove-image')) {
                input.click();
            }
        });
        
        if (changeBtn) {
            changeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                input.click();
            });
        }
        
        // Handle file selection
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    let previewImg = document.getElementById(uniqueId + '-preview-img');
                    
                    if (!previewImg) {
                        const img = document.createElement('img');
                        img.id = uniqueId + '-preview-img';
                        img.className = 'preview-image';
                        img.src = event.target.result;
                        container.insertBefore(img, placeholder);
                    } else {
                        previewImg.src = event.target.result;
                    }
                    
                    if (placeholder) placeholder.style.display = 'none';
                    if (removeBtn) removeBtn.style.display = 'inline-flex';
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Remove image
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                input.value = '';
                
                const currentPreviewImg = document.getElementById(uniqueId + '-preview-img');
                if (currentPreviewImg) {
                    currentPreviewImg.remove();
                }
                
                if (placeholder) placeholder.style.display = 'flex';
                removeBtn.style.display = 'none';
            });
        }
    }
    
    function toggleAdditionalImagesVisibility() {
        const imageCount = $('.additional-image-item').length;
        
        if (imageCount > 0) {
            $('#additional-images-empty-state').hide();
            $('#additional-images-container').show();
        } else {
            $('#additional-images-empty-state').show();
            $('#additional-images-container').hide();
        }
    }
    
    function reindexAdditionalImages() {
        $('.additional-image-item').each(function(index) {
            const newIndex = index + 1;
            $(this).attr('data-index', newIndex);
            $(this).find('small').text(`Additional Image ${newIndex}`);
        });
    }
}
