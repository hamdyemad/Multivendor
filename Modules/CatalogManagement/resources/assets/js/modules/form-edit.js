/**
 * Product Form Edit Mode Module
 * Handles edit mode functionality, data population, and cascading selects
 */

class ProductFormEdit {
    constructor() {
        this.config = window.productFormConfig || {};
    }

    /**
     * Load departments for edit mode
     */
    loadDepartmentsForEdit(vendorId, selectedDepartmentId) {
        console.log("🔧 Loading departments for edit mode, vendor:", vendorId);

        const departmentsRoute = this.config.departmentsRoute || '/api/departments';
        const url = `${departmentsRoute}?vendor_id=${vendorId}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ Departments fetched:', data);
            this.populateDepartmentSelect(data, selectedDepartmentId);
        })
        .catch(error => {
            console.error('❌ Error fetching departments:', error);
            this.handleDepartmentError();
        });
    }

    /**
     * Populate department select with data
     */
    populateDepartmentSelect(data, selectedDepartmentId) {
        const departmentSelect = $('#department_id');
        departmentSelect.empty().append('<option value="">Select Department</option>');

        if (data && Array.isArray(data) && data.length > 0) {
            data.forEach(dept => {
                departmentSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
            });
            console.log(`✅ Loaded ${data.length} departments`);
        } else if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
            data.data.forEach(dept => {
                departmentSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
            });
            console.log(`✅ Loaded ${data.data.length} departments`);
        } else {
            console.log('⚠️ No departments found');
            departmentSelect.append('<option value="">No departments available</option>');
        }

        this.refreshSelect2(departmentSelect, selectedDepartmentId);
    }

    /**
     * Handle department loading error
     */
    handleDepartmentError() {
        const departmentSelect = $('#department_id');
        departmentSelect.empty().append('<option value="">Error loading departments</option>');
        this.refreshSelect2(departmentSelect);
    }

    /**
     * Refresh Select2 and set selected value
     */
    refreshSelect2(selectElement, selectedValue = null) {
        // Check if Select2 is available
        if (typeof $.fn.select2 === 'undefined') {
            console.warn('⚠️ Select2 not available, skipping refresh');
            return;
        }

        if (selectElement.data('select2')) {
            selectElement.select2('destroy');
        }

        selectElement.select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: false
        });

        if (selectedValue && this.config.isEditMode) {
            setTimeout(() => {
                selectElement.val(selectedValue);
                selectElement.trigger('change.select2');
                console.log('🔄 Set selected value:', selectedValue);
            }, 100);
        }
    }

    /**
     * Load categories for edit mode
     */
    loadCategoriesForEdit(departmentId, selectedCategoryId) {
        console.log("🔧 Loading categories for edit mode, department:", departmentId);

        const categoriesRoute = this.config.categoriesRoute || '/api/categories';
        const url = `${categoriesRoute}?department_id=${departmentId}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(response => {
            const categorySelect = $("#category_id");
            categorySelect.empty().append('<option value="">Select Category</option>');

            if (response.status && response.data && response.data.length > 0) {
                response.data.forEach(category => {
                    categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                });
                categorySelect.val(selectedCategoryId);
                console.log("🔧 Categories loaded and selected for edit mode");
            }
        })
        .catch(error => console.error("❌ Error loading categories for edit:", error));
    }

    /**
     * Load subcategories for edit mode
     */
    loadSubCategoriesForEdit(categoryId, selectedSubCategoryId) {
        console.log("🔧 Loading subcategories for edit mode, category:", categoryId);

        const subCategoriesRoute = this.config.subCategoriesRoute || '/api/sub-categories';
        const url = `${subCategoriesRoute}?category_id=${categoryId}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(response => {
            const subCategorySelect = $("#sub_category_id");
            subCategorySelect.empty().append('<option value="">Select Sub Category</option>');

            if (response.status && response.data && response.data.length > 0) {
                response.data.forEach(subcategory => {
                    subCategorySelect.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                });
                subCategorySelect.val(selectedSubCategoryId);
                console.log("🔧 Subcategories loaded and selected for edit mode");
            }
        })
        .catch(error => console.error("❌ Error loading subcategories for edit:", error));
    }

    /**
     * Populate product details for edit mode
     */
    populateProductDetailsForEdit() {
        const selectedValues = this.config.selectedValues;

        if (!selectedValues || !this.config.isEditMode) {
            return;
        }

        console.log('🔧 Populating product details for edit mode');

        if (selectedValues.configuration_type === 'simple') {
            this.populateSimpleProductFields();
        } else if (selectedValues.configuration_type === 'variants' && selectedValues.existingVariants?.length > 0) {
            this.populateExistingVariants();
        }
    }

    /**
     * Populate simple product fields
     */
    populateSimpleProductFields() {
        const selectedValues = this.config.selectedValues;

        console.log('🔧 Populating simple product fields');
        console.log('🔧 Product SKU from config:', selectedValues?.productSku);
        console.log('🔧 Product Price from config:', selectedValues?.productPrice);

        // Set SKU (for simple product in Step 3, not the basic SKU in Step 1)
        if (selectedValues?.productSku) {
            // Target the simple product SKU field (input[name="sku"] but not #sku from Step 1)
            const simpleSkuField = $('input[name="sku"]').not('#sku');
            if (simpleSkuField.length) {
                simpleSkuField.val(selectedValues.productSku);
                console.log('🔧 Set simple product SKU field:', selectedValues.productSku);
            } else {
                console.warn('⚠️ Simple product SKU field not found');
            }
        }

        // Set Price
        if (selectedValues?.productPrice) {
            const priceField = $('input[name="price"]');
            if (priceField.length) {
                priceField.val(selectedValues.productPrice);
                console.log('🔧 Set Price:', selectedValues.productPrice);
            } else {
                console.warn('⚠️ Price field not found');
            }
        }

        // Set Discount
        if (selectedValues?.productHasDiscount) {
            $('.simple-discount-toggle').prop('checked', true).trigger('change');

            setTimeout(() => {
                if (selectedValues?.productPriceBeforeDiscount) {
                    $('input[name="price_before_discount"]').val(selectedValues.productPriceBeforeDiscount);
                }

                if (selectedValues?.productOfferEndDate) {
                    $('input[name="offer_end_date"]').val(selectedValues.productOfferEndDate);
                }
            }, 100);

            console.log('🔧 Set Discount fields');
        }

        // Populate stock data
        this.populateSimpleProductStocks();
    }

    /**
     * Populate stock data for simple products
     */
    async populateSimpleProductStocks() {
        const existingVariants = this.config.selectedValues?.existingVariants || [];

        console.log('🔧 Populating simple product stocks');
        console.log('🔧 Existing variants:', existingVariants);

        if (existingVariants.length > 0 && existingVariants[0].stocks && existingVariants[0].stocks.length > 0) {
            const stocks = existingVariants[0].stocks;
            console.log('🔧 Found ' + stocks.length + ' stock(s) to populate:', stocks);

            // Log each stock for debugging
            stocks.forEach((stock, idx) => {
                console.log(`📦 Stock ${idx + 1}:`, {
                    region_id: stock.region_id,
                    region_name: stock.region_name,
                    quantity: stock.quantity
                });
            });

            // Add and populate stock rows sequentially using async/await
            for (let index = 0; index < stocks.length; index++) {
                const stock = stocks[index];
                console.log(`🔄 Adding and populating stock row ${index + 1}...`);
                await this.addAndPopulateStockRow(stock, index);
            }

            console.log('✅ All stock rows populated successfully');
        } else {
            console.log('⚠️ No stocks found for simple product');
            console.log('⚠️ Variants length:', existingVariants.length);
            if (existingVariants.length > 0) {
                console.log('⚠️ First variant:', existingVariants[0]);
                console.log('⚠️ Stocks:', existingVariants[0].stocks);
            }
        }
    }

    /**
     * Add and populate a stock row using Promise-based approach
     */
    async addAndPopulateStockRow(stock, index) {
        console.log(`🔄 Adding stock row ${index + 1} with data:`, stock);

        // Find the stock container
        const container = $('#simple-product-section').length ? $('#simple-product-section') : $('.card:has(.stock-rows-container)');

        if (!container.length) {
            console.warn('⚠️ Stock container not found');
            return;
        }

        // Get the variants module instance from the main productForm
        const variantsModule = window.productForm?.getModule('variants');
        if (!variantsModule) {
            console.warn('⚠️ ProductFormVariants module not available');
            return;
        }

        try {
            // Add stock row and get the row elements
            const rowData = await variantsModule.addStockRow(container, false, null);

            console.log('✅ Stock row created, received:', rowData);

            if (rowData && rowData.regionSelect && rowData.quantityInput) {
                const { regionSelect, quantityInput } = rowData;

                console.log(`📝 Setting region_id to: ${stock.region_id}`);
                console.log(`📝 Setting quantity to: ${stock.quantity}`);

                // Set values using Select2 API
                regionSelect.val(stock.region_id).trigger('change');
                quantityInput.val(stock.quantity).trigger('input');

                console.log('🔍 Region select value after set:', regionSelect.val());
                console.log('🔍 Quantity input value after set:', quantityInput.val());

                console.log(`✅ Populated stock row ${index + 1}: Region ${stock.region_id} (${stock.region_name}), Quantity ${stock.quantity}`);
            } else {
                console.warn('⚠️ Row elements not returned properly');
            }
        } catch (error) {
            console.error(`❌ Error adding stock row ${index + 1}:`, error);
        }
    }

    /**
     * Populate existing variants
     */
    populateExistingVariants() {
        const existingVariants = this.config.selectedValues?.existingVariants || [];

        console.log('🔧 Populating existing variants:', existingVariants.length);
        console.log('🔧 Existing variants data:', existingVariants);

        if (existingVariants.length === 0) {
            console.log('⚠️ No existing variants to populate');
            return;
        }

        // Get the variants module
        const variantsModule = window.productForm?.getModule('variants');
        if (!variantsModule) {
            console.warn('⚠️ ProductFormVariants module not available');
            return;
        }

        existingVariants.forEach((variant, index) => {
            console.log(`🔧 Processing variant ${index + 1}:`, variant);

            // Add variant box
            variantsModule.addVariantBox();

            setTimeout(() => {
                this.populateVariantFields(variant, index);
            }, 500 * (index + 1)); // Longer delay to ensure proper loading
        });
    }

    /**
     * Populate individual variant fields
     */
    populateVariantFields(variant, variantIndex) {
        console.log(`🔧 Populating variant ${variantIndex}:`, variant);

        const variantBox = $(`.variant-box`).eq(variantIndex);
        if (!variantBox.length) {
            console.warn(`⚠️ Variant box ${variantIndex} not found`);
            return;
        }

        // Set variant key and configuration if available
        if (variant.variant_config && variant.variant_config.key_id) {
            console.log(`🔧 Setting variant key: ${variant.variant_config.key_id}`);

            // First, set the variant key
            const variantKeySelect = variantBox.find('.variant-key-select');
            if (variantKeySelect.length) {
                // Wait for variant keys to load, then set the value
                setTimeout(() => {
                    variantKeySelect.val(variant.variant_config.key_id).trigger('change');
                    console.log(`✅ Set variant key to: ${variant.variant_config.key_id}`);

                    // After key is set, wait for tree to load and traverse the path
                    setTimeout(() => {
                        if (variant.variant_configuration_id) {
                            this.traverseVariantTreePath(variantBox, variant.variant_configuration_id);
                        }
                    }, 1000); // Wait longer for tree to load
                }, 500); // Wait for keys to load
            }
        }

        // Set SKU
        if (variant.sku) {
            const skuInput = variantBox.find(`input[name*="[sku]"]`);
            if (skuInput.length) {
                skuInput.val(variant.sku);
                console.log(`✅ Set SKU: ${variant.sku}`);
            }
        }

        // Set Price
        if (variant.price) {
            const priceInput = variantBox.find(`input[name*="[price]"]`);
            if (priceInput.length) {
                priceInput.val(variant.price);
                console.log(`✅ Set Price: ${variant.price}`);
            }
        }

        // Set Discount
        if (variant.has_discount) {
            const discountToggle = variantBox.find(`input[name*="[has_discount]"]`);
            if (discountToggle.length) {
                discountToggle.prop('checked', true).trigger('change');
                console.log(`✅ Set discount toggle: true`);

                setTimeout(() => {
                    if (variant.price_before_discount) {
                        const priceBeforeInput = variantBox.find(`input[name*="[price_before_discount]"]`);
                        if (priceBeforeInput.length) {
                            priceBeforeInput.val(variant.price_before_discount);
                            console.log(`✅ Set price before discount: ${variant.price_before_discount}`);
                        }
                    }

                    if (variant.offer_end_date) {
                        const offerEndInput = variantBox.find(`input[name*="[offer_end_date]"]`);
                        if (offerEndInput.length) {
                            offerEndInput.val(variant.offer_end_date);
                            console.log(`✅ Set offer end date: ${variant.offer_end_date}`);
                        }
                    }
                }, 300);
            }
        }

        // Populate stock data for this variant (wait for variant tree to fully load)
        if (variant.stocks && variant.stocks.length > 0) {
            console.log(`🔧 Populating ${variant.stocks.length} stock rows for variant ${variantIndex}`);

            // Wait for variant tree to be fully loaded and stock section to be visible
            setTimeout(() => {
                this.waitForStockSection(variantBox, () => {
                    variant.stocks.forEach((stock, stockIndex) => {
                        setTimeout(() => {
                            this.addVariantStockRow(variantIndex, stock, stockIndex, variantBox);
                        }, 400 * (stockIndex + 1));
                    });
                });
            }, 2500); // Wait even longer for variant tree to fully load
        }

        console.log(`✅ Populated variant ${variantIndex}:`, variant.sku);
    }

    /**
     * Traverse variant tree path to select the correct variant configuration
     */
    async traverseVariantTreePath(variantBox, targetVariantId) {
        console.log(`🌲 Traversing variant tree to find: ${targetVariantId}`);

        // Wait a bit more for the tree to be fully loaded
        setTimeout(() => {
            this.selectVariantInTree(variantBox, targetVariantId, 0);
        }, 500);
    }

    /**
     * Select variant in tree by checking each level
     */
    selectVariantInTree(variantBox, targetVariantId, currentLevel) {
        console.log(`🔄 Checking level ${currentLevel} for variant: ${targetVariantId}`);

        // Find the select for this level
        const levelSelect = variantBox.find(`.variant-level-select[data-level="${currentLevel}"]`);

        if (!levelSelect.length) {
            console.log(`⚠️ No more levels found. Trying direct selection.`);
            this.selectVariantDirectly(variantBox, targetVariantId);
            return;
        }

        // Check if our target variant is in this level
        const targetOption = levelSelect.find(`option[value="${targetVariantId}"]`);

        if (targetOption.length) {
            // Found it! Select it
            console.log(`✅ Found target variant at level ${currentLevel}`);
            levelSelect.val(targetVariantId).trigger('change');
            return;
        }

        // Not found at this level, check if any option has children
        const optionsWithChildren = levelSelect.find('option[data-has-children="true"]');

        if (optionsWithChildren.length > 0) {
            // Try the first option with children and continue searching
            const firstOptionWithChildren = optionsWithChildren.first();
            const optionValue = firstOptionWithChildren.val();

            if (optionValue) {
                console.log(`🔄 Selecting option with children: ${optionValue}`);
                levelSelect.val(optionValue).trigger('change');

                // Wait for next level to load, then continue
                setTimeout(() => {
                    this.selectVariantInTree(variantBox, targetVariantId, currentLevel + 1);
                }, 800);
            }
        } else {
            console.log(`⚠️ No options with children at level ${currentLevel}`);
            this.selectVariantDirectly(variantBox, targetVariantId);
        }
    }

    /**
     * Fallback: try to select variant directly (for simple cases)
     */
    selectVariantDirectly(variantBox, targetVariantId) {
        console.log(`🔄 Attempting direct selection of variant: ${targetVariantId}`);

        // Try to find any select that has this value
        const allSelects = variantBox.find('.variant-level-select');

        allSelects.each(function() {
            const select = $(this);
            const option = select.find(`option[value="${targetVariantId}"]`);

            if (option.length) {
                select.val(targetVariantId).trigger('change');
                console.log(`✅ Direct selection successful at level ${select.data('level')}`);
                return false; // Break the loop
            }
        });
    }

    /**
     * Wait for stock section to be visible before adding stock rows
     */
    waitForStockSection(variantBox, callback) {
        console.log('🔍 Waiting for stock section to be visible...');

        const checkStockSection = () => {
            // Check if stock section is visible
            const stockSection = variantBox.find('.variant-stock-section, .stock-management-section');
            const addStockButton = variantBox.find('.add-stock-row-variant');

            if (stockSection.length && stockSection.is(':visible') && addStockButton.length) {
                console.log('✅ Stock section is visible, proceeding with stock population');
                callback();
            } else {
                console.log('⏳ Stock section not ready yet, waiting...');
                setTimeout(checkStockSection, 500);
            }
        };

        checkStockSection();
    }

    /**
     * Add and populate variant stock row
     */
    addVariantStockRow(variantIndex, stock, stockIndex, variantBox) {
        console.log(`🔧 Adding stock row ${stockIndex} for variant ${variantIndex}:`, stock);

        // Get the variants module from the global product form
        let variantsModule = window.productForm?.modules?.variants;

        if (!variantsModule && window.ProductFormVariants) {
            // Create new instance if class is available
            variantsModule = new window.ProductFormVariants();
            console.log(`🔄 Created new ProductFormVariants instance`);
        }

        console.log(`🔍 Variants module available:`, !!variantsModule);
        console.log(`🔍 addStockRow method available:`, !!(variantsModule && typeof variantsModule.addStockRow === 'function'));

        if (variantsModule && typeof variantsModule.addStockRow === 'function') {
            console.log(`🔄 Using variants module to add stock row`);

            // Call the addStockRow method directly
            variantsModule.addStockRow(variantBox, true, variantIndex).then((result) => {
                console.log(`✅ Stock row added successfully:`, result);

                // Now populate the newly created row
                if (result && result.regionSelect && result.quantityInput) {
                    result.regionSelect.val(stock.region_id).trigger('change');
                    result.quantityInput.val(stock.quantity);

                    console.log(`✅ Successfully populated variant ${variantIndex} stock row ${stockIndex + 1}: Region ${stock.region_id} (${stock.region_name || 'Unknown'}), Quantity ${stock.quantity}`);
                } else {
                    console.warn(`⚠️ Stock row elements not found in result:`, result);
                }
            }).catch((error) => {
                console.error(`❌ Error adding stock row:`, error);
            });
        } else {
            console.warn(`⚠️ Variants module not available, falling back to button click`);

            // Fallback to button click method
            const addButton = variantBox.find('.add-stock-row-variant');

            if (addButton.length) {
                console.log(`🔄 Clicking add stock button for variant ${variantIndex}`);
                addButton.trigger('click');

                setTimeout(() => {
                    // Find the newly created stock row
                    const stockRows = variantBox.find('.stock-rows-container tr');
                    const currentRow = stockRows.eq(stockIndex);

                    if (currentRow.length) {
                        console.log(`🎯 Found stock row ${stockIndex}, populating...`);

                        const regionSelect = currentRow.find('select');
                        const quantityInput = currentRow.find('input[type="number"]');

                        if (regionSelect.length && quantityInput.length) {
                            regionSelect.val(stock.region_id).trigger('change');
                            quantityInput.val(stock.quantity);

                            console.log(`✅ Successfully populated variant ${variantIndex} stock row ${stockIndex + 1}: Region ${stock.region_id} (${stock.region_name || 'Unknown'}), Quantity ${stock.quantity}`);
                        } else {
                            console.warn(`⚠️ Stock row elements not found for variant ${variantIndex}, row ${stockIndex}`);
                        }
                    } else {
                        console.warn(`⚠️ Stock row ${stockIndex} not found for variant ${variantIndex}`);
                    }
                }, 500);
            } else {
                console.warn(`⚠️ Add stock button not found for variant ${variantIndex}`);
            }
        }
    }
}

// Export for global use
window.ProductFormEdit = ProductFormEdit;
