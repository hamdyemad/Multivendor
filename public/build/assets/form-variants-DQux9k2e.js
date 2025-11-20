class c{constructor(){this.config=window.productFormConfig||{},this.variantIndex=0}init(){this.setupEventListeners()}setupEventListeners(){$(document).on("click","#add-variant-btn",()=>{this.addVariantBox()}),$(document).on("click",".remove-variant-btn",e=>{this.removeVariantBox($(e.target).closest(".variant-box"))}),$(document).on("change",".simple-discount-toggle",e=>{this.toggleDiscountFields($(e.target),".simple-discount-fields")}),$(document).on("change",".variant-discount-toggle",e=>{this.toggleDiscountFields($(e.target),".variant-discount-fields")}),$(document).on("change",".variant-key-select",e=>{const a=$(e.target).closest(".variant-box"),t=a.data("variant-index"),i=$(e.target).val();console.log(`🔄 Variant key changed for variant ${t}, key: ${i}`),i?this.loadVariantTree(a,t,i):(a.find(".variant-tree-container").hide(),a.find(".nested-variant-levels").empty())}),$(document).on("change",".variant-level-select",e=>{const a=$(e.target).closest(".variant-box"),t=a.data("variant-index"),i=$(e.target).val(),s=$(e.target).data("level");console.log(`🔄 Variant level ${s} changed, value: ${i}`),i&&this.handleVariantLevelChange(a,t,i,s)})}generateSimpleProductBoxes(){const e=$("#simple-product-details-container");e.empty();const a=this.generateProductDetailsBox("simple");e.append(a);const t=this.generateStockManagementBox("simple");e.append(t),console.log("✅ Simple product boxes generated")}generateProductDetailsBox(e,a=null){const t=e==="variant",i=t?`variants[${a}]`:"",s=t?`variant_${a}_`:"";return`
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="uil uil-receipt"></i>
                        ${t?`${this.config.variantNumber||"Variant"} ${a}`:this.config.productDetails||"Product Details"}
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">${t?this.config.variantSku||"Variant SKU":this.config.sku||"SKU"} <span class="text-danger">*</span></label>
                                <input type="text" name="${i}${t?"[sku]":"sku"}" id="${s}sku" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345" required>
                                <div class="error-message text-danger" id="error-${s}sku" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">${this.config.price||"Price"} <span class="text-danger">*</span></label>
                                <input type="number" name="${i}${t?"[price]":"price"}" id="${s}price" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                <div class="error-message text-danger" id="error-${s}price" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label d-block">${this.config.enableDiscountOffer||"Enable Discount Offer"}</label>
                                <div class="form-check form-switch form-switch-lg">
                                    <input class="form-check-input ${t?"variant":"simple"}-discount-toggle" type="checkbox" role="switch" name="${i}${t?"[has_discount]":"has_discount"}" value="1">
                                </div>
                            </div>
                        </div>

                        <!-- Discount Fields -->
                        <div class="${t?"variant":"simple"}-discount-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${this.config.priceBeforeDiscount||"Price Before Discount"}</label>
                                        <input type="number" name="${i}${t?"[price_before_discount]":"price_before_discount"}" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${this.config.offerEndDate||"Offer End Date"}</label>
                                        <input type="date" name="${i}${t?"[offer_end_date]":"offer_end_date"}" class="form-control ih-medium ip-gray radius-xs b-light px-15">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `}generateStockManagementBox(e,a=null){const t=e==="variant",i=t?`data-variant-index="${a}"`:"",s=t?"variant-stock-empty-state":"stock-empty-state",o=t?"add-stock-row-variant":"add-stock-row";return`
            <div class="card mb-4 ${t?"variant-stock-section":""}" ${i}>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">
                            <i class="uil uil-package"></i>
                            ${this.config.stockPerRegion||"Stock per Region"}
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm ${o}" ${i}>
                            <i class="uil uil-plus"></i> ${this.config.addNewRegion||"Add New Region"}
                        </button>
                    </div>

                    <!-- Empty state message -->
                    <div class="${s} text-center py-4" ${i}>
                        <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mb-0">${this.config.noRegionsAddedYet||'No regions added yet. Click "Add New Region" to start.'}</p>
                    </div>

                    <!-- Stock table -->
                    <div class="stock-table-container table-responsive" style="display: none;">
                        <table class="table mb-0 table-bordered table-hover dataTable" style="width: 100%;">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="dt-orderable-none" data-dt-column="0">
                                        <div class="dt-column-header">
                                            <span class="dt-column-title">
                                                <span class="userDatatable-title">${this.config.region||"Region"}</span>
                                            </span>
                                        </div>
                                    </th>
                                    <th class="dt-orderable-none" data-dt-column="1">
                                        <div class="dt-column-header">
                                            <span class="dt-column-title">
                                                <span class="userDatatable-title">${this.config.stockQuantity||"Stock Quantity"}</span>
                                            </span>
                                        </div>
                                    </th>
                                    <th class="text-center dt-orderable-none" data-dt-column="2">
                                        <div class="dt-column-header">
                                            <span class="dt-column-title">
                                                <span class="userDatatable-title">${this.config.actionsLabel||"Actions"}</span>
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="stock-rows-container" ${i}>
                                <!-- Stock rows will be added here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Total stock display -->
                    <div class="total-stock-display mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <strong>${this.config.totalStock||"Total Stock"}:</strong> <span class="total-stock-value">0</span>
                        </div>
                    </div>
                </div>
            </div>
        `}addVariantBox(){this.variantIndex++;const e=this.generateVariantBox(this.variantIndex);$("#variants-container").append(e),$("#variants-empty-state").hide();const a=$(`.variant-box[data-variant-index="${this.variantIndex}"]`),t=a.find(".variant-key-select");return t.length>0&&typeof $.fn.select2<"u"&&(t.select2({theme:"bootstrap-5",width:"100%",dropdownParent:a,placeholder:this.config.selectVariantKey||"Select Variant Key"}),console.log(`✅ Select2 initialized for variant key selector ${this.variantIndex}`)),this.loadVariantKeys(this.variantIndex),console.log(`✅ Added variant box ${this.variantIndex}`),this.variantIndex}loadVariantKeys(e){const t=$(`.variant-box[data-variant-index="${e}"]`).find(".variant-key-select");console.log(`🔄 Loading variant keys for variant ${e}...`);const i=this.config.variantKeys||[];if(i.length===0){console.warn("⚠️ No variant keys found in config"),t.html(`<option value="">${this.config.noVariantKeys||"No variant keys available"}</option>`);return}t.empty(),t.append(`<option value="">${this.config.selectVariantKey||"Select Variant Key"}</option>`),i.forEach(s=>{t.append(`<option value="${s.id}">${s.name}</option>`)}),console.log(`✅ Loaded ${i.length} variant keys`)}async loadVariantTree(e,a,t){const i=e.find(".variant-tree-container"),s=e.find(".nested-variant-levels");console.log(`🌲 Loading variant tree for key ${t}...`);try{const o=await $.ajax({url:`/api/variant-configurations/key/${t}/tree`,method:"GET",dataType:"json"});console.log("🌲 Variant tree loaded:",o),s.empty(),this.buildVariantLevel(s,a,o,0),i.show()}catch(o){console.error("❌ Error loading variant tree:",o),s.html(`<div class="alert alert-danger">${this.config.errorLoadingTree||"Error loading variant tree"}</div>`)}}buildVariantLevel(e,a,t,i){const s=`
            <div class="variant-level mb-3" data-level="${i}">
                <label class="form-label">
                    ${t.name||`Level ${i+1}`}
                    <span class="text-danger">*</span>
                </label>
                <select class="form-control form-select ih-medium ip-gray radius-xs b-light px-15 variant-level-select"
                        data-level="${i}"
                        data-key-id="${t.id}"
                        required>
                    <option value="">${this.config.selectPlaceholder||"Select"} ${t.name}</option>
                    ${(t.children||[]).map(n=>{const l=n.children&&n.children.length>0,r=l?"🌳 ":"";return`<option value="${n.id}" data-has-children="${l?"true":"false"}">${r}${n.name}</option>`}).join("")}
                </select>
            </div>
        `;e.append(s);const o=e.find(`.variant-level-select[data-level="${i}"]`);o.length>0&&typeof $.fn.select2<"u"&&(o.select2({theme:"bootstrap-5",width:"100%",dropdownParent:e.closest(".variant-box"),placeholder:`${this.config.selectPlaceholder||"Select"} ${t.name}`,templateResult:function(n){return n.id?$(n.element).data("has-children")==="true"?$('<span><i class="uil uil-folder-open text-warning me-1"></i>'+n.text.replace("🌳 ","")+"</span>"):$('<span><i class="uil uil-file-alt text-muted me-1"></i>'+n.text+"</span>"):n.text},templateSelection:function(n){return n.id?$(n.element).data("has-children")==="true"?$('<span><i class="uil uil-folder-open text-warning me-1"></i>'+n.text.replace("🌳 ","")+"</span>"):$("<span>"+n.text+"</span>"):n.text}}),console.log(`✅ Select2 initialized for variant level ${i}`))}async handleVariantLevelChange(e,a,t,i){const s=e.find(".nested-variant-levels"),o=e.find(`.variant-level-select[data-level="${i}"] option[value="${t}"]`),n=o.data("has-children")==="true"||o.data("has-children")===!0;if(console.log(`🔄 Level ${i} selected, value: ${t}`),console.log("📊 hasChildren data attribute:",o.data("has-children")),console.log("📊 hasChildren evaluated:",n),e.find(".variant-level[data-level]").each(function(){parseInt($(this).data("level"))>i&&$(this).remove()}),e.find(".variant-product-details").hide(),e.find(".variant-selection-info").hide(),e.find(".final-variant-id").val(t),n){console.log(`🌲 Fetching children for variant ${t}...`);try{const l=await $.ajax({url:`/api/variant-configurations/${t}`,method:"GET",dataType:"json"});console.log("🌲 Next level API response:",l),console.log("🌲 Children count:",l.children?l.children.length:0),l.children&&l.children.length>0?(console.log(`✅ Building next level (${i+1}) with ${l.children.length} options`),this.buildVariantLevel(s,a,l,i+1)):(console.log("⚠️ No children found in response - showing stock section"),this.showVariantStockSection(e,a))}catch(l){console.error("❌ Error loading next level:",l),console.error("❌ Error details:",l.responseText)}}else console.log("📄 Leaf node detected - showing stock section"),this.showVariantStockSection(e,a)}showVariantStockSection(e,a){console.log(`✅ Showing product details and stock section for variant ${a}`),e.find(".variant-selection-info").hide(),e.find(".variant-product-details").show(),console.log(`✅ Product details and stock section shown for variant ${a}`)}generateVariantBox(e){return`
            <div class="variant-box card mb-3" data-variant-index="${e}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0 text-primary variant-title">
                                <i class="uil uil-cube"></i>
                                ${this.config.variantNumber||"Variant"} ${e}
                            </h6>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                            <i class="uil uil-trash-alt m-0"></i> ${this.config.remove||"Remove"}
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">${this.config.selectVariantKey||"Select Variant Key"} <span class="text-danger">*</span></label>
                            <select name="variants[${e}][key_id]" class="form-control form-select ih-medium ip-gray radius-xs b-light px-15 variant-key-select" required>
                                <option value="">${this.config.loadingVariantKeys||"Loading variant keys..."}</option>
                            </select>
                            <small class="text-muted">${this.config.selectVariantKeyHelper||"Choose a variant key to configure this variant"}</small>
                        </div>
                    </div>

                    <div class="variant-tree-container" style="display: none;">
                        <div class="nested-variant-levels">
                            <!-- Dynamic variant levels will be added here -->
                        </div>

                        <!-- Hidden input to store the final selected variant ID -->
                        <input type="hidden" name="variants[${e}][value_id]" class="final-variant-id">

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
                        ${this.generateProductDetailsBox("variant",e)}
                        ${this.generateStockManagementBox("variant",e)}
                    </div>
                </div>
            </div>
        `}removeVariantBox(e){const a=e.data("variant-index");console.log(`🗑️ Removing variant box ${a}`),e.remove(),$(".variant-box").length===0&&$("#variants-empty-state").show()}toggleDiscountFields(e,a){const t=e.closest(".card").find(a);e.is(":checked")?t.show():(t.hide(),t.find("input").val(""))}addStockRow(e,a=!1,t=null){const i=this.config.regions||[],s=this.generateStockRowHtml(i,a,t);e.find(".stock-rows-container").append(s),e.find(".stock-empty-state, .variant-stock-empty-state").hide(),e.find(".stock-table-container").show(),e.find(".total-stock-display").show();const n=e.find(".stock-rows-container").children().last().find("select.select2");n.length>0&&typeof $.fn.select2<"u"?(n.select2({theme:"bootstrap-5",width:"100%",dropdownParent:e.find(".stock-table-container"),placeholder:this.config.selectPlaceholder||"Select Region"}),console.log("✅ Select2 initialized for stock region selector")):console.warn("⚠️ Select2 not available or element not found"),this.updateTotalStock(e)}generateStockRowHtml(e,a,t){const i=a?`variants[${t}]`:"",s=a?"variant-stock-row":"stock-row",o=a?`data-variant-index="${t}"`:"";return`
            <tr class="${s}" ${o}>
                <td class="align-middle">
                    <select name="${i}${a?"[stocks]":"stocks"}[][region_id]" class="form-control form-select ih-medium ip-gray radius-xs b-light px-15 select2" required>
                        <option value="">${this.config.selectPlaceholder||"Select Region"}</option>
                        ${e.map(n=>`<option value="${n.id}">${n.name}</option>`).join("")}
                    </select>
                </td>
                <td class="align-middle">
                    <input type="number" name="${i}${a?"[stocks]":"stocks"}[][quantity]" class="form-control ih-medium ip-gray radius-xs b-light px-15 stock-quantity" min="0" placeholder="${this.config.stockQuantity||"Stock Quantity"}" required>
                </td>
                <td class="text-center align-middle">
                    <div class='actions'>
                        <button type="button" class="btn btn-danger btn-sm remove-stock-row" title="${this.config.remove||"Remove"}">
                            <i class="uil uil-trash-alt m-0"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `}updateTotalStock(e){let a=0;e.find(".stock-quantity").each(function(){const t=parseInt($(this).val())||0;a+=t}),e.find(".total-stock-value").text(a)}}window.ProductFormVariants=c;
