class s{constructor(){this.config=window.productFormConfig||{},this.isInitializing=!1}init(){console.log("🚀 Initializing Product Form..."),this.ensureConfig(),this.initializeSelect2(),this.setupEventListeners(),this.config.isEditMode&&this.initializeEditMode(),console.log("✅ Product Form initialized")}ensureConfig(){window.productFormConfig||(console.warn("⚠️ productFormConfig not found, creating default"),window.productFormConfig={isEditMode:!1,selectedValues:{},existingVariants:[]}),this.config=window.productFormConfig}initializeSelect2(){const e="#brand_id, #vendor_id, #department_id, #category_id, #sub_category_id, #tax_id, #configuration_type",i=()=>{typeof $.fn.select2<"u"?(console.log("✅ Select2 is available, initializing..."),$(e).each(function(){if($(this).attr("type")==="hidden"){console.log("⏭️ Skipping Select2 init for hidden input:",$(this).attr("id"));return}$(this).hasClass("select2-hidden-accessible")||($(this).select2({theme:"bootstrap-5",width:"100%",allowClear:!1}),console.log("✅ Select2 initialized for:",$(this).attr("id")))})):(console.log("⏳ Waiting for Select2 to load..."),setTimeout(i,200))};setTimeout(i,100)}setupEventListeners(){$("#configuration_type").on("change",e=>{this.handleConfigurationTypeChange($(e.target).val())})}handleConfigurationTypeChange(e){if(console.log("🔄 Configuration type changed to:",e),e==="simple")if($("#simple-product-section").show(),$("#variants-section").hide(),window.productForm&&window.productForm.getModule){const i=window.productForm.getModule("variants");i&&i.generateSimpleProductBoxes?i.generateSimpleProductBoxes():(console.warn("⚠️ Variants module not available, generating simple boxes manually"),this.generateSimpleProductBoxesManually())}else console.warn("⚠️ ProductForm not initialized, generating simple boxes manually"),this.generateSimpleProductBoxesManually();else e==="variants"?($("#simple-product-section").hide(),$("#variants-section").show(),$("#simple-product-details-container").empty()):($("#simple-product-section").hide(),$("#variants-section").hide(),$("#simple-product-details-container").empty())}generateSimpleProductBoxesManually(){const e=$("#simple-product-details-container");e.empty();const i=`
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="uil uil-receipt"></i>
                        Product Details
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" id="sku" class="form-control" placeholder="PRD-12345" required>
                                <div class="error-message text-danger" id="error-sku" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" placeholder="Enter price" required>
                                <div class="error-message text-danger" id="error-price" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label d-block">Enable Discount Offer</label>
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input simple-discount-toggle" type="checkbox" role="switch" name="has_discount" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="simple-discount-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Price Before Discount</label>
                                        <input type="number" name="price_before_discount" class="form-control" min="0" step="0.01" placeholder="Enter original price">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Offer End Date</label>
                                        <input type="date" name="offer_end_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,t=`
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="uil uil-package"></i>
                            Stock per Region
                        </div>
                        <button type="button" class="btn btn-primary btn-sm add-stock-row">
                            <i class="uil uil-plus"></i> Add New Region
                        </button>
                    </h5>
                    <div class="stock-empty-state text-center py-4">
                        <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mb-0">No regions added yet. Click "Add New Region" to start.</p>
                    </div>
                    <div class="stock-rows-container">
                        <!-- Stock rows will be added here -->
                    </div>
                </div>
            </div>
        `;e.append(i),e.append(t),console.log("✅ Simple product boxes generated manually")}initializeEditMode(){console.log("🔧 Initializing edit mode...");const e=this.config.selectedValues;if(!e){console.warn("⚠️ No selectedValues found for edit mode");return}e.vendor_id&&setTimeout(()=>{var t;const i=(t=window.productForm)==null?void 0:t.getModule("edit");i&&i.loadDepartmentsForEdit&&i.loadDepartmentsForEdit(e.vendor_id,e.department_id)},100),e.department_id&&e.category_id&&setTimeout(()=>{var t;const i=(t=window.productForm)==null?void 0:t.getModule("edit");i&&i.loadCategoriesForEdit&&i.loadCategoriesForEdit(e.department_id,e.category_id)},300),e.category_id&&e.sub_category_id&&setTimeout(()=>{var t;const i=(t=window.productForm)==null?void 0:t.getModule("edit");i&&i.loadSubCategoriesForEdit&&i.loadSubCategoriesForEdit(e.category_id,e.sub_category_id)},600),e.configuration_type&&setTimeout(()=>{const i=$("#configuration_type");i.val(e.configuration_type),i.trigger("change"),console.log("🔧 Configuration type set to:",e.configuration_type),setTimeout(()=>{var o;const t=(o=window.productForm)==null?void 0:o.getModule("edit");t&&t.populateProductDetailsForEdit&&t.populateProductDetailsForEdit()},200)},800)}}window.ProductFormInit=s;
