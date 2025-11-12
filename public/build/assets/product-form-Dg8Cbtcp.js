let m=1;const y=5;let v={},d=0,u=null;document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll(".wizard-step-content").forEach(function(n,o){o===0?n.classList.add("active"):n.classList.remove("active")})});jQuery(document).ready(function(t){console.log("✅ Product form jQuery ready");function n(){console.log("🔧 Attaching event handlers to Select2 dropdowns...");const e=t("#department_id");console.log("📍 Department element found:",e.length>0),console.log("📍 Department has Select2:",e.hasClass("select2-hidden-accessible")),console.log("📍 Department value:",e.val()),t("#department_id").off("change.productForm select2:select.productForm"),t(document).off("change.productForm","#department_id").on("change.productForm","#department_id",function(i){console.log("🎯 Department event triggered:",i.type);const a=t(this).val();console.log("🔄 Department changed:",a);const s=t("#category_id"),c=t("#sub_category_id");if(s.empty().append('<option value="">Loading categories...</option>').prop("disabled",!0).trigger("change"),c.empty().append('<option value="">Select Sub Category</option>').val("").trigger("change"),a){const l=`${window.productFormConfig.categoriesRoute}?department_id=${a}&select2=1`;console.log("🌐 Fetching categories from:",l),fetch(l,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest",lang:document.documentElement.lang||"en"}}).then(r=>{if(console.log("📥 Categories response status:",r.status),!r.ok)throw new Error(`HTTP error! status: ${r.status}`);return r.json()}).then(r=>{console.log("✅ Categories API response:",r),s.empty().append('<option value="">Select Category</option>').prop("disabled",!1),r.status&&r.data&&r.data.length>0?(r.data.forEach(p=>{s.append(`<option value="${p.id}">${p.name}</option>`)}),console.log(`✅ Loaded ${r.data.length} categories`)):(console.log("⚠️ No categories found for department:",a),s.append('<option value="">No categories available</option>')),s.trigger("change")}).catch(r=>{console.error("❌ Error loading categories:",r),s.empty().append('<option value="">Error loading categories</option>').prop("disabled",!1).trigger("change")})}else s.empty().append('<option value="">Select Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Department handler attached"),t("#category_id").off("change.productForm select2:select.productForm"),t(document).off("change.productForm","#category_id").on("change.productForm","#category_id",function(i){console.log("🎯 Category event triggered:",i.type);const a=t(this).val();console.log("🔄 Category changed:",a);const s=t("#sub_category_id");if(s.empty().append('<option value="">Loading subcategories...</option>').prop("disabled",!0).trigger("change"),a){const c=`${window.productFormConfig.subCategoriesRoute}?category_id=${a}`;console.log("🌐 Fetching subcategories from:",c),fetch(c,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"}}).then(l=>{if(console.log("📥 SubCategories response status:",l.status),!l.ok)throw new Error(`HTTP error! status: ${l.status}`);return l.json()}).then(l=>{console.log("✅ SubCategories API response:",l),s.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1),l.status&&l.data&&l.data.length>0?(l.data.forEach(r=>{s.append(`<option value="${r.id}">${r.name}</option>`)}),console.log(`✅ Loaded ${l.data.length} subcategories`)):(console.log("⚠️ No subcategories found for category:",a),s.append('<option value="">No subcategories available</option>')),s.trigger("change")}).catch(l=>{console.error("❌ Error loading subcategories:",l),s.empty().append('<option value="">Error loading subcategories</option>').prop("disabled",!1).trigger("change")})}else s.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Category handler attached"),console.log("✅ All handlers ready!")}function o(){const e=t("#department_id");e.length&&e.hasClass("select2-hidden-accessible")?n():setTimeout(o,100)}setTimeout(o,200),f(m),t("#nextBtn").on("click",function(){console.log("📍 Next button clicked. Current step:",m),g(),m++,m>y&&(m=y),f(m),m===5&&h()}),t("#prevBtn").on("click",function(){m--,m<1&&(m=1),f(m)}),t(".wizard-step-nav").on("click",function(){console.log("🖱️ Wizard step clicked!");const e=parseInt(t(this).data("step"));console.log("Clicked step:",e),g(),m=e,f(m),m===5&&h()}),t(document).on("click",".edit-step",function(){const e=parseInt(t(this).data("step"));g(),m=e,f(m),t("html, body").animate({scrollTop:t(".card").offset().top-100},300)}),t("#productForm").on("submit",L),t("#configuration_type").on("change",function(){const e=t(this).val();e==="simple"?(t("#simple-product-section").show(),t("#variants-section").hide()):e==="variants"?(t("#simple-product-section").hide(),t("#variants-section").show()):(t("#simple-product-section").hide(),t("#variants-section").hide())}),t("#has_discount").on("change",function(){t(this).is(":checked")?t("#discount-fields").slideDown():(t("#discount-fields").slideUp(),t("#price_before_discount").val(""),t("#offer_end_date").val(""))}),t("#add-stock-row").on("click",function(){V()}),t(document).on("click",".remove-stock-row",function(){t(this).closest("tr").remove(),b(),C(),x()}),t(document).on("input",".stock-quantity",function(){b()}),t("#add-variant-btn").on("click",function(){P()}),t(document).on("click",".remove-variant-btn",function(){t(this).closest(".variant-box").remove(),F(),B()}),t(document).on("change",".variant-key-select",function(){const e=t(this).closest(".variant-box"),i=t(this).val();i?(e.find(".variant-tree-container").show(),N(e,i)):(e.find(".variant-tree-container").hide(),e.find(".final-variant-id").val(""))}),t(document).on("change",".variant-level-select",function(){const e=t(this).closest(".variant-box"),i=parseInt(t(this).data("level")),a=t(this).val(),s=t(this).find("option:selected").data("has-children");e.find(".final-variant-id").val(""),a?j(e,i,a,s):(e.find(".nested-variant-levels").find("[data-level]").each(function(){parseInt(t(this).data("level"))>i&&t(this).remove()}),S(e))}),t(document).on("click",".add-stock-row-variant",function(){const e=t(this).data("variant-index");G(e)}),t(document).on("click",".remove-variant-stock-row",function(){const e=t(this).closest("tr"),i=t(this).data("variant-index"),a=t(`.variant-stock-rows[data-variant-index="${i}"]`);e.remove(),a.find("tr").each(function(s){t(this).find("td:first").text(s+1)}),a.find("tr").length===0&&(t(`.variant-stock-table-container[data-variant-index="${i}"]`).hide(),t(`.variant-stock-empty-state[data-variant-index="${i}"]`).show()),k(i)}),t(document).on("input",".variant-stock-quantity",function(){const i=t(this).closest("tr").data("variant-index");k(i)}),T(),K(),console.log("✅ Product form navigation initialized")});function T(){console.log("🌍 Loading regions data..."),$.ajax({url:"/api/regions?select2=1",method:"GET",dataType:"json",success:function(t){t.results&&t.results.length>0?(u=t.results,console.log(`✅ Cached ${u.length} regions`)):t.data&&t.data.items&&t.data.items.length>0?(u=t.data.items.map(n=>({id:n.id,text:n.name})),console.log(`✅ Cached ${u.length} regions (alternative format)`)):(console.log("⚠️ No regions from API, using fallback"),u=[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"},{id:6,text:"Beheira"},{id:7,text:"Fayoum"},{id:8,text:"Gharbia"},{id:9,text:"Ismailia"},{id:10,text:"Menofia"}])},error:function(t,n,o){console.log("❌ API error, using fallback regions"),u=[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"},{id:6,text:"Beheira"},{id:7,text:"Fayoum"},{id:8,text:"Gharbia"},{id:9,text:"Ismailia"},{id:10,text:"Menofia"}]}})}function f(t){$(".wizard-step-content").each(function(){$(this).removeClass("active").css("display","none")});const n=$(`.wizard-step-content[data-step="${t}"]`);if(n.length&&n.addClass("active").css("display","block"),Object.keys(v).length>0&&t!==5)for(let o in v){const e=D(o),i=n.find(`[name="${e}"], [name="${e}[]"], [name="${o}"], [name="${o}[]"]`).first();if(i.length){i.addClass("is-invalid"),i.closest(".form-group").find(".error-message").remove();const a=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${v[o][0]}</div>`;if(i.hasClass("select2")||i.data("select2")){const s=i.next(".select2-container");s.length?s.after(a):i.after(a)}else i.after(a)}}$(".wizard-step-nav").removeClass("current"),$(`.wizard-step-nav[data-step="${t}"]`).addClass("current"),$(".wizard-step-nav").each(function(){parseInt($(this).data("step"))<t?$(this).addClass("completed"):$(this).removeClass("completed")}),t===5&&typeof h=="function"&&h(),t===1?$("#prevBtn").hide():$("#prevBtn").show(),t===y?($("#nextBtn").hide(),$("#submitBtn").show()):($("#nextBtn").show(),$("#submitBtn").hide()),$("html, body").animate({scrollTop:$(".card-body").offset().top-100},300)}function g(){$(".error-message").remove(),$(".is-invalid").removeClass("is-invalid"),$("#review-validation-errors").hide(),$("#review-errors-list").html(""),v={}}function D(t){const n=t.split(".");if(n.length===1)return t;let o=n[0];for(let e=1;e<n.length;e++)o+=`[${n[e]}]`;return o}function A(t){v=t;let n='<ul class="mb-0">';for(let o in t){const e=t[o];e.forEach(s=>{n+=`<li class="mb-2">${s}</li>`});const i=D(o),a=$(`[name="${i}"], [name="${i}[]"], [name="${o}"], [name="${o}[]"]`).first();if(a.length){a.addClass("is-invalid");const s=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${e[0]}</div>`;if(a.closest(".form-group").find(".error-message").remove(),a.hasClass("select2")||a.data("select2")){const c=a.next(".select2-container");c.length?c.after(s):a.after(s)}else a.after(s)}}n+="</ul>",$("#review-errors-list").html(n),$("#review-validation-errors").show()}function h(){const t=window.productFormConfig;if(!t)return;t.languages.forEach(o=>{$(`.review-title-${o.code}`).text($(`input[name="translations[${o.id}][title]"]`).val()||"-")}),$(".review-sku").text($("#sku").val()||"-"),$(".review-brand").text($("#brand_id option:selected").text()||"-");const n=$("#price").val();$(".review-price").text(n?"$"+n:"-"),$(".review-stock").text($("#stock_quantity").val()||"-")}function L(t){t.preventDefault();const n=window.productFormConfig;if(!n)return;g(),typeof LoadingOverlay<"u"&&(LoadingOverlay.show(),LoadingOverlay.progressSequence([30,60,90]));const o=new FormData(this),e=$(this).attr("action");$.ajax({url:e,method:"POST",data:o,processData:!1,contentType:!1,success:function(i){typeof LoadingOverlay<"u"&&LoadingOverlay.animateProgressBar(100),i.success&&(typeof LoadingOverlay<"u"&&LoadingOverlay.showSuccess(i.message||"Product created successfully!","Redirecting..."),setTimeout(function(){window.location.href=n.indexRoute||"/admin/products"},1500))},error:function(i){if(typeof LoadingOverlay<"u"&&LoadingOverlay.hide(),i.status===422){const a=i.responseJSON.errors;A(a),m=5,f(5),setTimeout(function(){const s=$("#review-validation-errors");s.is(":visible")&&$("html, body").animate({scrollTop:s.offset().top-100},300)},100)}else alert("An error occurred. Please try again.")}})}function V(){u&&u.length>0?R(u):(console.log("⏳ Regions not loaded yet, waiting..."),setTimeout(function(){u&&u.length>0?R(u):(console.log("⚠️ Using fallback regions"),q())},500))}function q(){const t=[{id:1,name:"Cairo"},{id:2,name:"Alexandria"},{id:3,name:"Giza"},{id:4,name:"Dakahlia"},{id:5,name:"Red Sea"},{id:6,name:"Beheira"},{id:7,name:"Fayoum"},{id:8,name:"Gharbia"},{id:9,name:"Ismailia"},{id:10,name:"Menofia"}],n=$(".stock-row").length;let o='<option value="">Select Region</option>';t.forEach(a=>{o+=`<option value="${a.id}">${a.name}</option>`});const i=`
        <tr class="stock-row">
            <td>${n+1}</td>
            <td>
                <select name="stocks[${n}][region_id]" class="form-control select2-stock" required>
                    ${o}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${n}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;$("#stock-rows").append(i),C(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),b(),x()}function R(t){const n=$(".stock-row").length;let o='<option value="">Select Region</option>';t.forEach(a=>{const s=a.text||a.name;o+=`<option value="${a.id}">${s}</option>`});const i=`
        <tr class="stock-row">
            <td>${n+1}</td>
            <td>
                <select name="stocks[${n}][region_id]" class="form-control select2-stock" required>
                    ${o}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${n}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;$("#stock-rows").append(i),C(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),b(),x()}function x(){$(".stock-row").each(function(t){$(this).find("td:first").text(t+1)})}function C(){$(".stock-row").length>0?($("#stock-table-container").show(),$("#stock-empty-state").hide()):($("#stock-table-container").hide(),$("#stock-empty-state").show())}function b(){let t=0;$(".stock-quantity").each(function(){const n=parseInt($(this).val())||0;t+=n}),$("#total-stock").text(t)}function P(){d++;const t=`
        <div class="variant-box card mb-3" data-variant-index="${d}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-primary variant-title">
                            <i class="uil uil-cube"></i>
                            Variant ${d}
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
                        <select name="variants[${d}][key_id]" class="form-control variant-key-select" required>
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
                    <input type="hidden" name="variants[${d}][value_id]" class="final-variant-id">

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
                                        <input type="text" name="variants[${d}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Price <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[${d}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">Enable Discount Offer</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input variant-discount-toggle" type="checkbox" role="switch" name="variants[${d}][has_discount]" value="1">
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Fields (shown when discount is checked) -->
                                <div class="variant-discount-fields" style="display: none;" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Price Before Discount</label>
                                                <input type="number" name="variants[${d}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Offer End Date</label>
                                                <input type="date" name="variants[${d}][offer_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
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
                                <button type="button" class="btn btn-primary btn-sm add-stock-row-variant" data-variant-index="${d}">
                                    <i class="uil uil-plus"></i> Add New Region
                                </button>
                            </h5>

                            <!-- Empty state message -->
                            <div class="variant-stock-empty-state text-center py-4" data-variant-index="${d}">
                                <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mb-0">No regions added yet. Click "Add New Region" to start.</p>
                            </div>

                            <!-- Stock table (hidden initially) -->
                            <div class="variant-stock-table-container" data-variant-index="${d}" style="display: none;">
                                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-bordered table-hover variant-stock-table" data-variant-index="${d}" style="width:100%">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th><span class="userDatatable-title">#</span></th>
                                                    <th><span class="userDatatable-title">Region</span></th>
                                                    <th><span class="userDatatable-title">Stock Quantity</span></th>
                                                    <th><span class="userDatatable-title">Actions</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="variant-stock-rows" data-variant-index="${d}">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="text-center fw-bold">Total Stock:</td>
                                                    <td class="fw-bold text-primary">
                                                        <span class="variant-total-stock" data-variant-index="${d}">0</span>
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
    `;$("#variants-container").append(t),F(),z(d),M(d)}function z(t){const o=$(`.variant-box[data-variant-index="${t}"]`).find(".variant-key-select");$.ajax({url:"/admin/api/variant-keys",method:"GET",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(e){if(e.success&&e.data){let i='<option value="">Select Variant Key</option>';e.data.forEach(a=>{i+=`<option value="${a.id}">${a.name}</option>`}),o.html(i)}else o.html('<option value="">Error loading keys</option>')},error:function(){o.html('<option value="">Error loading keys</option>')}})}function N(t,n){const o=t.find(".nested-variant-levels"),e=t.find(".variant-selection-info");if(!n){o.empty(),e.hide();return}o.empty(),e.show().find(".selection-text").text("Loading variants..."),_(t,n,null,0)}function _(t,n,o,e){const i=t.find(".nested-variant-levels");$.ajax({url:"/admin/api/variants-by-key",method:"GET",data:{key_id:n,parent_id:o||"root"},headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(a){if(a.success&&a.data&&a.data.length>0){const s=e===0?"Root Variants":`Level ${e+1}`,c=`
                    <div class="variant-level mb-3" data-level="${e}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">${s} <span class="text-danger">*</span></label>
                                <select class="form-control variant-level-select" data-level="${e}">
                                    <option value="">Select ${s}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;i.append(c);const l=i.find(`[data-level="${e}"]`).find(".variant-level-select");let r='<option value="">Select variant</option>';a.data.forEach(p=>{const I=p.has_children?" 🌳":"";r+=`<option value="${p.id}" data-has-children="${p.has_children}">${p.name}${I}</option>`}),l.html(r),l.select2({theme:"bootstrap-5",width:"100%"}),a.data.length===1&&!a.data[0].has_children&&l.val(a.data[0].id).trigger("change"),S(t)}else e===0&&t.find(".variant-selection-info").show().find(".selection-text").text("No variants available for this key")},error:function(){console.error("Error loading variant level",e)}})}function j(t,n,o,e){const i=t.find(".nested-variant-levels"),a=t.find(".variant-key-select").val();i.find("[data-level]").each(function(){parseInt($(this).data("level"))>n&&$(this).remove()}),o&&e?_(t,a,o,n+1):o&&O(t,o),S(t)}function O(t,n){t.find(".final-variant-id").val(n),H(t)}function S(t){const n=t.find(".variant-selection-info"),o=n.find(".selection-text"),e=t.find(".final-variant-id").val(),i=t.find(".variant-product-details"),a=t.find(".variant-details-path"),s=t.find(".variant-path-text");if(e){const c=[];if(t.find(".variant-level-select").each(function(){const l=$(this).find("option:selected");l.val()&&c.push(l.text().replace(" 🌳",""))}),c.length>0){const l=c.join(" - ");o.html(`<strong>Selected:</strong> ${c.join(" → ")}`),n.removeClass("alert-info").addClass("alert-success").show(),s.text(l),a.show()}}else o.text("Please select a variant"),n.removeClass("alert-success").addClass("alert-info").show(),a.hide(),i.hide()}function M(t){$(`.variant-box[data-variant-index="${t}"]`).find(".variant-key-select").select2({theme:"bootstrap-5",width:"100%"})}function H(t,n){t.find(".variant-product-details").show();const e=t.find(".variant-discount-toggle"),i=t.find(".variant-discount-fields");e.on("change",function(){$(this).is(":checked")?i.show():(i.hide(),i.find("input").val(""))})}function G(t){u&&u.length>0?w(t,u):(console.log("⏳ Regions not loaded yet for variant, waiting..."),setTimeout(function(){u&&u.length>0?w(t,u):(console.log("⚠️ Using fallback regions for variant"),U(t))},500))}function w(t,n){const o=$(`.variant-stock-rows[data-variant-index="${t}"]`),e=o.find("tr").length;let i='<option value="">Select Region</option>';n.forEach(function(c){i+=`<option value="${c.id}">${c.text}</option>`});const a=`
        <tr class="variant-stock-row" data-variant-index="${t}" data-row-index="${e}">
            <td class="text-center">${e+1}</td>
            <td>
                <select name="variants[${t}][stock][${e}][region_id]" class="form-control region-select" required>
                    ${i}
                </select>
            </td>
            <td>
                <input type="number" name="variants[${t}][stock][${e}][quantity]"
                       class="form-control variant-stock-quantity" min="0" value="0" required>
            </td>
            <td class="actions">
                <button type="button" class="btn btn-sm btn-danger remove-variant-stock-row m-0" data-variant-index="${t}">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;o.append(a),o.find("tr").last().find(".region-select").select2({theme:"bootstrap-5",width:"100%"}),$(`.variant-stock-table-container[data-variant-index="${t}"]`).show(),$(`.variant-stock-empty-state[data-variant-index="${t}"]`).hide(),k(t)}function U(t){w(t,[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"}])}function k(t){const n=$(`.variant-stock-rows[data-variant-index="${t}"]`),o=$(`.variant-total-stock[data-variant-index="${t}"]`);let e=0;n.find(".variant-stock-quantity").each(function(){const i=parseInt($(this).val())||0;e+=i}),o.text(e)}function F(){$(".variant-box").length>0?($("#variants-empty-state").hide(),$("#variants-container").show()):($("#variants-empty-state").show(),$("#variants-container").hide())}function B(){$(".variant-box").each(function(t){const n=t+1;$(this).find("h6").html(`<i class="uil uil-cube"></i> Variant ${n}`)})}function K(){console.log("🖼️ Initializing image uploads..."),W(),X()}function W(){const t=$(".upload-area"),n=$("#main_image"),o=$(".upload-content"),e=$(".image-preview");t.on("click",function(i){$(i.target).hasClass("remove-image")||n.click()}),t.on("dragover",function(i){i.preventDefault(),$(this).addClass("drag-over")}),t.on("dragleave",function(i){i.preventDefault(),$(this).removeClass("drag-over")}),t.on("drop",function(i){i.preventDefault(),$(this).removeClass("drag-over");const a=i.originalEvent.dataTransfer.files;a.length>0&&E(a[0])}),n.on("change",function(){const i=this.files[0];i&&E(i)}),$(document).on("click",".remove-image",function(){n.val(""),o.show(),e.hide()})}function E(t){if(!t.type.match("image.*")){alert("Please select an image file.");return}if(t.size>5*1024*1024){alert("File size must be less than 5MB.");return}const n=new FileReader;n.onload=function(o){$(".upload-content").hide(),$(".image-preview img").attr("src",o.target.result),$(".image-preview").show()},n.readAsDataURL(t)}function X(){let t=0;$("#add-image-btn").on("click",function(){n()}),$(document).on("click",".remove-additional-image",function(){$(this).closest(".additional-image-item").remove(),e(),i()});function n(){t++;const a=`
            <div class="col-md-4 mb-3 additional-image-item" data-index="${t}">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Image ${t}</small>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-additional-image">
                                <i class="uil uil-trash-alt m-0"></i>
                            </button>
                        </div>
                        <div class="additional-upload-area" style="border: 2px dashed #ddd; border-radius: 6px; padding: 20px; text-align: center; background: #f8f9fa; cursor: pointer;">
                            <input type="file" name="additional_images[]" class="additional-image-input" accept="image/*" style="display: none;">
                            <div class="additional-upload-content">
                                <i class="uil uil-cloud-upload" style="font-size: 24px; color: #6c757d; margin-bottom: 8px;"></i>
                                <p class="mb-0 small">Click to upload</p>
                            </div>
                            <div class="additional-image-preview" style="display: none;">
                                <img src="" alt="Preview" style="max-width: 100%; max-height: 120px; border-radius: 4px;">
                                <button type="button" class="btn btn-sm btn-danger mt-1 remove-additional-preview">
                                    <i class="uil uil-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;$("#additional-images-container").append(a),e()}$(document).on("click",".additional-upload-area",function(a){$(a.target).hasClass("remove-additional-preview")||$(this).find(".additional-image-input").click()}),$(document).on("change",".additional-image-input",function(){const a=this.files[0],s=$(this).closest(".additional-upload-area");a&&o(a,s)}),$(document).on("click",".remove-additional-preview",function(){const a=$(this).closest(".additional-upload-area");a.find(".additional-image-input").val(""),a.find(".additional-upload-content").show(),a.find(".additional-image-preview").hide()});function o(a,s){if(!a.type.match("image.*")){alert("Please select an image file.");return}if(a.size>5*1024*1024){alert("File size must be less than 5MB.");return}const c=new FileReader;c.onload=function(l){s.find(".additional-upload-content").hide(),s.find(".additional-image-preview img").attr("src",l.target.result),s.find(".additional-image-preview").show()},c.readAsDataURL(a)}function e(){$(".additional-image-item").length>0?($("#images-empty-state").hide(),$("#additional-images-container").show()):($("#images-empty-state").show(),$("#additional-images-container").hide())}function i(){$(".additional-image-item").each(function(a){$(this).find("small").text(`Image ${a+1}`)})}}
