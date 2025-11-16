let m=1;const y=4;let v={},u=0,g=null;document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll(".wizard-step-content").forEach(function(s,o){o===0?s.classList.add("active"):s.classList.remove("active")})});jQuery(document).ready(function(t){console.log("✅ Product form jQuery ready"),setTimeout(function(){L()},500),window.testErrorContainers=function(){console.log("🧪 Testing error containers..."),[1,2].forEach(a=>{const n=`error-translations-${a}-title`,i=t(`#${n}`);console.log(`📝 Container #${n}: exists=${i.length>0}`),i.length>0&&(i.html('<i class="uil uil-exclamation-triangle"></i> Test error message').show(),console.log(`✅ Test error displayed in ${n}`),setTimeout(()=>{i.hide().empty(),console.log(`🧹 Cleared test error from ${n}`)},3e3))}),console.log("💡 Check the form to see test error messages appear and disappear")},window.testTitleError=function(){console.log("🧪 Testing Title (English) error specifically...");const e=t('input[name="translations[1][title]"]'),a=t("#error-translations-1-title");console.log("📝 Title input found:",e.length>0),console.log("📝 Title input element:",e[0]),console.log("📝 Error container found:",a.length>0),console.log("📝 Error container element:",a[0]),a.length>0?(console.log("📝 Container current display:",a.css("display")),console.log("📝 Container current visibility:",a.is(":visible")),console.log("📝 Container classes:",a.attr("class")),console.log("📝 Container style attribute:",a.attr("style")),a.html('<i class="uil uil-exclamation-triangle"></i> TEST: Title is required for English'),a.show(),a.css("display","block"),a.css("visibility","visible"),a.removeClass("d-none").addClass("d-block"),a.attr("style","display: block !important;"),console.log("📝 After force show - display:",a.css("display")),console.log("📝 After force show - visible:",a.is(":visible")),console.log("📝 After force show - style:",a.attr("style")),e.addClass("is-invalid"),console.log("✅ Test error should now be visible under Title (English) field")):console.log("❌ Error container not found for Title (English)")},window.testErrorContainer=function(){console.log("🧪 Direct test of error-translations-1-title container...");const e=t("#error-translations-1-title");console.log("📝 Container found:",e.length>0),console.log("📝 Container element:",e[0]),e.length>0?(console.log("📝 Current display:",e.css("display")),console.log("📝 Current visibility:",e.css("visibility")),console.log("📝 Is visible:",e.is(":visible")),console.log("📝 Current content:",e.html()),console.log("📝 Current classes:",e.attr("class")),console.log("📝 Current style:",e.attr("style")),e.html('<i class="uil uil-exclamation-triangle"></i> DIRECT TEST MESSAGE'),e.show(),e.css("display","block"),e.css("visibility","visible"),e.removeClass("d-none").addClass("d-block"),e.attr("style","display: block !important; visibility: visible !important;"),console.log("📝 After force show:"),console.log("📝 Display:",e.css("display")),console.log("📝 Visibility:",e.css("visibility")),console.log("📝 Is visible:",e.is(":visible")),console.log("📝 Style attr:",e.attr("style")),console.log("✅ Direct test completed - check if message appears")):console.log("❌ Container #error-translations-1-title not found")};function s(){console.log("🔧 Attaching event handlers to Select2 dropdowns...");const e=t("#department_id");console.log("📍 Department element found:",e.length>0),console.log("📍 Department has Select2:",e.hasClass("select2-hidden-accessible")),console.log("📍 Department value:",e.val()),t("#department_id").off("change.productForm select2:select.productForm"),t(document).off("change.productForm","#department_id").on("change.productForm","#department_id",function(a){console.log("🎯 Department event triggered:",a.type);const n=t(this).val();console.log("🔄 Department changed:",n);const i=t("#category_id"),l=t("#sub_category_id");if(i.empty().append('<option value="">Loading categories...</option>').prop("disabled",!0).trigger("change"),l.empty().append('<option value="">Select Sub Category</option>').val("").trigger("change"),n){const r=`${window.productFormConfig.categoriesRoute}?department_id=${n}&select2=1`;console.log("🌐 Fetching categories from:",r),fetch(r,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest",lang:document.documentElement.lang||"en"}}).then(c=>{if(console.log("📥 Categories response status:",c.status),!c.ok)throw new Error(`HTTP error! status: ${c.status}`);return c.json()}).then(c=>{console.log("✅ Categories API response:",c),i.empty().append('<option value="">Select Category</option>').prop("disabled",!1),c.status&&c.data&&c.data.length>0?(c.data.forEach(d=>{i.append(`<option value="${d.id}">${d.name}</option>`)}),console.log(`✅ Loaded ${c.data.length} categories`)):(console.log("⚠️ No categories found for department:",n),i.append('<option value="">No categories available</option>')),i.trigger("change")}).catch(c=>{console.error("❌ Error loading categories:",c),i.empty().append('<option value="">Error loading categories</option>').prop("disabled",!1).trigger("change")})}else i.empty().append('<option value="">Select Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Department handler attached"),t("#category_id").off("change.productForm select2:select.productForm"),t(document).off("change.productForm","#category_id").on("change.productForm","#category_id",function(a){console.log("🎯 Category event triggered:",a.type);const n=t(this).val();console.log("🔄 Category changed:",n);const i=t("#sub_category_id");if(i.empty().append('<option value="">Loading subcategories...</option>').prop("disabled",!0).trigger("change"),n){const l=`${window.productFormConfig.subCategoriesRoute}?category_id=${n}`;console.log("🌐 Fetching subcategories from:",l),fetch(l,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"}}).then(r=>{if(console.log("📥 SubCategories response status:",r.status),!r.ok)throw new Error(`HTTP error! status: ${r.status}`);return r.json()}).then(r=>{console.log("✅ SubCategories API response:",r),i.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1),r.status&&r.data&&r.data.length>0?(r.data.forEach(c=>{i.append(`<option value="${c.id}">${c.name}</option>`)}),console.log(`✅ Loaded ${r.data.length} subcategories`)):(console.log("⚠️ No subcategories found for category:",n),i.append('<option value="">No subcategories available</option>')),i.trigger("change")}).catch(r=>{console.error("❌ Error loading subcategories:",r),i.empty().append('<option value="">Error loading subcategories</option>').prop("disabled",!1).trigger("change")})}else i.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Category handler attached"),console.log("✅ All handlers ready!")}function o(){const e=t("#department_id");e.length&&e.hasClass("select2-hidden-accessible")?s():setTimeout(o,100)}setTimeout(o,200),t("#productForm").on("submit",I),p(m),t("#nextBtn").on("click",function(){console.log("📍 Next button clicked. Current step:",m),m++,m>y&&(m=y),p(m)}),t("#prevBtn").on("click",function(){m--,m<1&&(m=1),p(m)}),t(".wizard-step-nav").on("click",function(){console.log("🖱️ Wizard step clicked!");const e=parseInt(t(this).data("step"));console.log("Clicked step:",e),t("#validation-alerts-container").hide().empty(),m=e,p(m)}),t(document).on("click",".edit-step",function(){m=parseInt(t(this).data("step")),p(m),t("html, body").animate({scrollTop:t(".card").offset().top-100},300)}),t("#productForm").on("submit",I),t("#productForm").on("input keyup change","input, textarea, select",function(){const e=t(this),a=e.attr("name");if(a){e.removeClass("is-invalid");let n=null;if(a.includes("translations[")){const i=a.match(/translations\[(\d+)\]\[([^\]]+)\]/);if(i){const l=i[1],r=i[2];n=t(`#error-translations-${l}-${r}`)}}if(!n||!n.length){const i=[`#error-${a}`,`#error-${a.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`,`[data-error-for="${a}"]`];for(const l of i)if(n=t(l),n.length>0)break}n&&n.length&&n.hide().empty(),e.closest(".form-group").find(".error-message:not([id])").remove(),e.siblings(".error-message:not([id])").remove(),(e.hasClass("select2")||e.data("select2"))&&e.next(".select2-container").siblings(".error-message:not([id])").remove(),console.log(`🧹 Cleared error for field: ${a}`)}}),t("#configuration_type").on("change",function(){const e=t(this).val();e==="simple"?(t("#simple-product-section").show(),t("#variants-section").hide()):e==="variants"?(t("#simple-product-section").hide(),t("#variants-section").show()):(t("#simple-product-section").hide(),t("#variants-section").hide())}),t("#has_discount").on("change",function(){t(this).is(":checked")?t("#discount-fields").slideDown():(t("#discount-fields").slideUp(),t("#price_before_discount").val(""),t("#offer_end_date").val(""))}),t("#add-stock-row").on("click",function(){O()}),t(document).on("click",".remove-stock-row",function(){t(this).closest("tr").remove(),b(),C(),x()}),t(document).on("input",".stock-quantity",function(){b()}),t("#add-variant-btn").on("click",function(){z()}),t(document).on("click",".remove-variant-btn",function(){t(this).closest(".variant-box").remove(),q(),Q()}),t(document).on("change",".variant-key-select",function(){const e=t(this).closest(".variant-box"),a=t(this).val();a?(e.find(".variant-tree-container").show(),H(e,a)):(e.find(".variant-tree-container").hide(),e.find(".final-variant-id").val(""))}),t(document).on("change",".variant-level-select",function(){const e=t(this).closest(".variant-box"),a=parseInt(t(this).data("level")),n=t(this).val(),i=t(this).find("option:selected").data("has-children");e.find(".final-variant-id").val(""),n?B(e,a,n,i):(e.find(".nested-variant-levels").find("[data-level]").each(function(){parseInt(t(this).data("level"))>a&&t(this).remove()}),S(e))}),t(document).on("click",".add-stock-row-variant",function(){const e=t(this).data("variant-index");W(e)}),t(document).on("click",".remove-variant-stock-row",function(){const e=t(this).closest("tr"),a=t(this).data("variant-index"),n=t(`.variant-stock-rows[data-variant-index="${a}"]`);e.remove(),n.find("tr").each(function(i){t(this).find("td:first").text(i+1)}),n.find("tr").length===0&&(t(`.variant-stock-table-container[data-variant-index="${a}"]`).hide(),t(`.variant-stock-empty-state[data-variant-index="${a}"]`).show()),k(a)}),t(document).on("input",".variant-stock-quantity",function(){const a=t(this).closest("tr").data("variant-index");k(a)}),F(),J(),console.log("✅ Product form navigation initialized")});function F(){console.log("🌍 Loading regions data..."),$.ajax({url:"/api/regions?select2=1",method:"GET",dataType:"json",success:function(t){t.results&&t.results.length>0?(g=t.results,console.log(`✅ Cached ${g.length} regions`)):t.data&&t.data.items&&t.data.items.length>0?(g=t.data.items.map(s=>({id:s.id,text:s.name})),console.log(`✅ Cached ${g.length} regions (alternative format)`)):(console.log("⚠️ No regions from API, using fallback"),g=[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"},{id:6,text:"Beheira"},{id:7,text:"Fayoum"},{id:8,text:"Gharbia"},{id:9,text:"Ismailia"},{id:10,text:"Menofia"}])},error:function(t,s,o){console.log("❌ API error, using fallback regions"),g=[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"},{id:6,text:"Beheira"},{id:7,text:"Fayoum"},{id:8,text:"Gharbia"},{id:9,text:"Ismailia"},{id:10,text:"Menofia"}]}})}function p(t){$(".wizard-step-content").each(function(){$(this).removeClass("active").css("display","none")});const s=$(`.wizard-step-content[data-step="${t}"]`);if(s.length&&s.addClass("active").css("display","block"),Object.keys(v).length>0&&t!==4)for(let o in v){const e=D(o),a=s.find(`[name="${e}"], [name="${e}[]"], [name="${o}"], [name="${o}[]"]`).first();if(a.length){a.addClass("is-invalid");let n=null;if(o.includes("translations.")){const i=o.split(".");if(i.length===3&&i[0]==="translations"){const l=i[1],r=i[2],c=`error-translations-${l}-${r}`;n=$(`#${c}`)}}if(!n||!n.length){const i=a.attr("name"),l=[`#error-${o}`,`#error-${i}`,`#error-${o.replace(/\./g,"-")}`,`#error-${i.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`];for(const r of l)if(n=$(r),n.length>0)break}if(n&&n.length){const i=`<i class="uil uil-exclamation-triangle"></i> ${v[o][0]}`;n.html(i).show().css("display","block").removeClass("d-none").addClass("d-block")}else{a.closest(".form-group").find(".error-message:not([id])").remove();const i=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${v[o][0]}</div>`;if(a.hasClass("select2")||a.data("select2")){const l=a.next(".select2-container");l.length?l.after(i):a.after(i)}else a.after(i)}}}$(".wizard-step-nav").removeClass("current"),$(`.wizard-step-nav[data-step="${t}"]`).addClass("current"),$(".wizard-step-nav").each(function(){parseInt($(this).data("step"))<t?$(this).addClass("completed"):$(this).removeClass("completed")}),t===1?$("#prevBtn").hide():$("#prevBtn").show(),t===y?($("#nextBtn").hide(),$("#submitBtn").show()):($("#nextBtn").show(),$("#submitBtn").hide()),$("html, body").animate({scrollTop:$(".card-body").offset().top-100},300)}function D(t){const s=t.split(".");if(s.length===1)return t;let o=s[0];for(let e=1;e<s.length;e++)o+=`[${s[e]}]`;return o}function L(){console.log("🔧 Ensuring all form fields have error containers..."),$("#productForm").find("input, select, textarea").each(function(){const t=$(this),s=t.attr("name");if(!s)return;const o=`error-${s.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`;if($(`#${o}`).length===0){const e=`<div class="error-message text-danger" id="${o}" style="display: none;"></div>`;if(t.hasClass("select2")||t.data("select2")){const a=t.next(".select2-container");a.length?a.after(e):t.after(e)}else t.after(e);console.log(`✅ Created error container for: ${s}`)}})}function P(t){v=t,L();let s='<ul class="mb-0">';for(let o in t){const e=t[o];e.forEach(i=>{s+=`<li class="mb-2">${i}</li>`});const a=D(o),n=$(`[name="${a}"], [name="${a}[]"], [name="${o}"], [name="${o}[]"]`).first();if(console.log(`🔍 Looking for field: ${o} -> ${a}, found: ${n.length>0}`),o.includes("translations.")&&o.includes(".title")){console.log(`📝 Translation title field detected: ${o}`);const i=`error-translations-${o.split(".")[1]}-title`;console.log(`📝 Expected error container ID: ${i}`),console.log("📝 Container exists:",$(`#${i}`).length>0),console.log("📝 Container element:",$(`#${i}`)[0])}if(n.length){n.addClass("is-invalid");let i=null;if(o.includes("translations.")){const l=o.split(".");if(l.length===3&&l[0]==="translations"){const r=l[1],c=l[2],d=`error-translations-${r}-${c}`;i=$(`#${d}`),console.log(`🔍 Looking for translation container: #${d}, found: ${i.length>0}`),i.length>0&&console.log("📝 Container element:",i[0])}}if(!i||!i.length){const l=n.attr("name"),r=[`#error-${o}`,`#error-${l}`,`#error-${o.replace(/\./g,"-")}`,`#error-${l.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`];console.log("🔍 Trying fallback selectors:",r);for(const c of r)if(i=$(c),i.length>0){console.log(`✅ Found with selector: ${c}`);break}}if(i&&i.length){const l=`<i class="uil uil-exclamation-triangle"></i> ${e[0]}`;console.log(`✅ Using existing container for ${o}, setting content: ${l}`),i.html(l),i.show(),i.css("display","block"),i.css("visibility","visible"),i.removeClass("d-none").addClass("d-block"),i.attr("style","display: block !important;"),console.log(`✅ Container after update - visible: ${i.is(":visible")}, display: ${i.css("display")}`)}else{const l=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${e[0]}</div>`;if(n.closest(".form-group").find(".error-message:not([id])").remove(),n.hasClass("select2")||n.data("select2")){const r=n.next(".select2-container");r.length?r.after(l):n.after(l)}else n.after(l);console.log(`✅ Created new error message for field: ${o}`)}}else console.log(`❌ Field element not found for: ${o} (${a})`)}if(s+="</ul>",Object.keys(t).length>0){const e=`
            <div class="alert alert-danger alert-dismissible fade show validation-errors-alert" role="alert">
                <div class="d-flex align-items-start">
                    <i class="uil uil-exclamation-triangle me-2" style="font-size: 18px; margin-top: 2px;"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-2">${document.documentElement.dir==="rtl"||document.documentElement.lang==="ar"||$("html").attr("lang")==="ar"?"يرجى تصحيح الأخطاء التالية:":"Please correct the following errors:"}</h6>
                        ${s}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;$(".validation-errors-alert").remove();const a=$("#validation-alerts-container");a.length?(console.log("✅ Adding alert to validation-alerts-container"),a.html(e).show()):(console.log("⚠️ validation-alerts-container not found, using fallback"),$(".card-body").prepend(e)),setTimeout(()=>{const n=$(".validation-errors-alert");n.length?(n.show(),console.log("✅ Alert should now be visible"),$("html, body").animate({scrollTop:n.offset().top-100},300)):console.log("❌ Alert element not found after creation")},100)}}function V(){$(".wizard-step-content:not(.active)").each(function(){$(this).find("[required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}),$("#simple-product-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")}),$("#variants-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}function N(){$('[data-was-required="true"]').each(function(){$(this).attr("required","required").removeAttr("data-was-required")})}function I(t){console.log("Form submission started"),t.preventDefault();const s=window.productFormConfig;if(!s){console.error("productFormConfig is not defined");return}if(console.log("LoadingOverlay available:",typeof LoadingOverlay<"u"),typeof LoadingOverlay>"u"&&console.error("LoadingOverlay is not defined. Make sure the loading-overlay component is included in the layout."),V(),typeof LoadingOverlay<"u"){const n=$('input[name="_method"][value="PUT"]').length>0?s.updatingProduct||"Updating product...":s.creatingProduct||"Creating product...";LoadingOverlay.show({text:n,progress:!0}),LoadingOverlay.progressSequence([30,60,90])}const o=new FormData(this),e=$(this).attr("action");$.ajax({url:e,method:"POST",data:o,processData:!1,contentType:!1,success:function(a){typeof LoadingOverlay<"u"&&LoadingOverlay.animateProgressBar(100),a.success&&(typeof LoadingOverlay<"u"&&LoadingOverlay.showSuccess(a.message||s.productCreated,s.redirecting),setTimeout(function(){window.location.href=s.indexRoute||"/admin/products"},1500))},error:function(a){if(typeof LoadingOverlay<"u"&&LoadingOverlay.hide(),N(),a.status===422){const n=a.responseJSON.errors;P(n)}else alert("An error occurred. Please try again.")}})}function O(){g&&g.length>0?A(g):(console.log("⏳ Regions not loaded yet, waiting..."),setTimeout(function(){g&&g.length>0?A(g):(console.log("⚠️ Using fallback regions"),j())},500))}function j(){const t=[{id:1,name:"Cairo"},{id:2,name:"Alexandria"},{id:3,name:"Giza"},{id:4,name:"Dakahlia"},{id:5,name:"Red Sea"},{id:6,name:"Beheira"},{id:7,name:"Fayoum"},{id:8,name:"Gharbia"},{id:9,name:"Ismailia"},{id:10,name:"Menofia"}],s=$(".stock-row").length;let o='<option value="">Select Region</option>';t.forEach(n=>{o+=`<option value="${n.id}">${n.name}</option>`});const a=`
        <tr class="stock-row">
            <td>${s+1}</td>
            <td>
                <select name="stocks[${s}][region_id]" class="form-control select2-stock" required>
                    ${o}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${s}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;$("#stock-rows").append(a),C(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),b(),x()}function A(t){const s=$(".stock-row").length;let o='<option value="">Select Region</option>';t.forEach(n=>{const i=n.text||n.name;o+=`<option value="${n.id}">${i}</option>`});const a=`
        <tr class="stock-row">
            <td>${s+1}</td>
            <td>
                <select name="stocks[${s}][region_id]" class="form-control select2-stock" required>
                    ${o}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${s}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;$("#stock-rows").append(a),C(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),b(),x()}function x(){$(".stock-row").each(function(t){$(this).find("td:first").text(t+1)})}function C(){$(".stock-row").length>0?($("#stock-table-container").show(),$("#stock-empty-state").hide()):($("#stock-table-container").hide(),$("#stock-empty-state").show())}function b(){let t=0;$(".stock-quantity").each(function(){const s=parseInt($(this).val())||0;t+=s}),$("#total-stock").text(t)}function z(){u++;const t=`
        <div class="variant-box card mb-3" data-variant-index="${u}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-primary variant-title">
                            <i class="uil uil-cube"></i>
                            Variant ${u}
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
                        <select name="variants[${u}][key_id]" class="form-control variant-key-select" required>
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
                    <input type="hidden" name="variants[${u}][value_id]" class="final-variant-id">

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
                                        <input type="text" name="variants[${u}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">Price <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[${u}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">Enable Discount Offer</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input variant-discount-toggle" type="checkbox" role="switch" name="variants[${u}][has_discount]" value="1">
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Fields (shown when discount is checked) -->
                                <div class="variant-discount-fields" style="display: none;" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Price Before Discount</label>
                                                <input type="number" name="variants[${u}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Offer End Date</label>
                                                <input type="date" name="variants[${u}][offer_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
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
                                <button type="button" class="btn btn-primary btn-sm add-stock-row-variant" data-variant-index="${u}">
                                    <i class="uil uil-plus"></i> Add New Region
                                </button>
                            </h5>

                            <!-- Empty state message -->
                            <div class="variant-stock-empty-state text-center py-4" data-variant-index="${u}">
                                <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mb-0">No regions added yet. Click "Add New Region" to start.</p>
                            </div>

                            <!-- Stock table (hidden initially) -->
                            <div class="variant-stock-table-container" data-variant-index="${u}" style="display: none;">
                                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-bordered table-hover variant-stock-table" data-variant-index="${u}" style="width:100%">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th><span class="userDatatable-title">#</span></th>
                                                    <th><span class="userDatatable-title">Region</span></th>
                                                    <th><span class="userDatatable-title">Stock Quantity</span></th>
                                                    <th><span class="userDatatable-title">Actions</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="variant-stock-rows" data-variant-index="${u}">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="text-center fw-bold">Total Stock:</td>
                                                    <td class="fw-bold text-primary">
                                                        <span class="variant-total-stock" data-variant-index="${u}">0</span>
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
    `;$("#variants-container").append(t),q(),M(u),U(u)}function M(t){const o=$(`.variant-box[data-variant-index="${t}"]`).find(".variant-key-select");$.ajax({url:"/admin/api/variant-keys",method:"GET",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(e){if(e.success&&e.data){let a='<option value="">Select Variant Key</option>';e.data.forEach(n=>{a+=`<option value="${n.id}">${n.name}</option>`}),o.html(a)}else o.html('<option value="">Error loading keys</option>')},error:function(){o.html('<option value="">Error loading keys</option>')}})}function H(t,s){const o=t.find(".nested-variant-levels"),e=t.find(".variant-selection-info");if(!s){o.empty(),e.hide();return}o.empty(),e.show().find(".selection-text").text("Loading variants..."),_(t,s,null,0)}function _(t,s,o,e){const a=t.find(".nested-variant-levels");$.ajax({url:"/admin/api/variants-by-key",method:"GET",data:{key_id:s,parent_id:o||"root"},headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(n){if(n.success&&n.data&&n.data.length>0){const i=e===0?"Root Variants":`Level ${e+1}`,l=`
                    <div class="variant-level mb-3" data-level="${e}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">${i} <span class="text-danger">*</span></label>
                                <select class="form-control variant-level-select" data-level="${e}">
                                    <option value="">Select ${i}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;a.append(l);const r=a.find(`[data-level="${e}"]`).find(".variant-level-select");let c='<option value="">Select variant</option>';n.data.forEach(d=>{const f=d.has_children?" 🌳":"";c+=`<option value="${d.id}" data-has-children="${d.has_children}">${d.name}${f}</option>`}),r.html(c),r.select2({theme:"bootstrap-5",width:"100%"}),n.data.length===1&&!n.data[0].has_children&&r.val(n.data[0].id).trigger("change"),S(t)}else e===0&&t.find(".variant-selection-info").show().find(".selection-text").text("No variants available for this key")},error:function(){console.error("Error loading variant level",e)}})}function B(t,s,o,e){const a=t.find(".nested-variant-levels"),n=t.find(".variant-key-select").val();a.find("[data-level]").each(function(){parseInt($(this).data("level"))>s&&$(this).remove()}),o&&e?_(t,n,o,s+1):o&&G(t,o),S(t)}function G(t,s){t.find(".final-variant-id").val(s),K(t)}function S(t){const s=t.find(".variant-selection-info"),o=s.find(".selection-text"),e=t.find(".final-variant-id").val(),a=t.find(".variant-product-details"),n=t.find(".variant-details-path"),i=t.find(".variant-path-text");if(e){const l=[];if(t.find(".variant-level-select").each(function(){const r=$(this).find("option:selected");r.val()&&l.push(r.text().replace(" 🌳",""))}),l.length>0){const r=l.join(" - ");o.html(`<strong>Selected:</strong> ${l.join(" → ")}`),s.removeClass("alert-info").addClass("alert-success").show(),i.text(r),n.show()}}else o.text("Please select a variant"),s.removeClass("alert-success").addClass("alert-info").show(),n.hide(),a.hide()}function U(t){$(`.variant-box[data-variant-index="${t}"]`).find(".variant-key-select").select2({theme:"bootstrap-5",width:"100%"})}function K(t,s){t.find(".variant-product-details").show();const e=t.find(".variant-discount-toggle"),a=t.find(".variant-discount-fields");e.on("change",function(){$(this).is(":checked")?a.show():(a.hide(),a.find("input").val(""))})}function W(t){g&&g.length>0?w(t,g):(console.log("⏳ Regions not loaded yet for variant, waiting..."),setTimeout(function(){g&&g.length>0?w(t,g):(console.log("⚠️ Using fallback regions for variant"),X(t))},500))}function w(t,s){const o=$(`.variant-stock-rows[data-variant-index="${t}"]`),e=o.find("tr").length;let a='<option value="">Select Region</option>';s.forEach(function(l){a+=`<option value="${l.id}">${l.text}</option>`});const n=`
        <tr class="variant-stock-row" data-variant-index="${t}" data-row-index="${e}">
            <td class="text-center">${e+1}</td>
            <td>
                <select name="variants[${t}][stock][${e}][region_id]" class="form-control region-select" required>
                    ${a}
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
    `;o.append(n),o.find("tr").last().find(".region-select").select2({theme:"bootstrap-5",width:"100%"}),$(`.variant-stock-table-container[data-variant-index="${t}"]`).show(),$(`.variant-stock-empty-state[data-variant-index="${t}"]`).hide(),k(t)}function X(t){w(t,[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"}])}function k(t){const s=$(`.variant-stock-rows[data-variant-index="${t}"]`),o=$(`.variant-total-stock[data-variant-index="${t}"]`);let e=0;s.find(".variant-stock-quantity").each(function(){const a=parseInt($(this).val())||0;e+=a}),o.text(e)}function q(){$(".variant-box").length>0?($("#variants-empty-state").hide(),$("#variants-container").show()):($("#variants-empty-state").show(),$("#variants-container").hide())}function Q(){$(".variant-box").each(function(t){const s=t+1;$(this).find("h6").html(`<i class="uil uil-cube"></i> Variant ${s}`)})}function J(){$("#add-additional-image-btn").on("click",function(){t()}),$(document).on("click",".remove-additional-image",function(){$(this).closest(".additional-image-item").remove(),o(),e()});function t(){const n=$(".additional-image-item").length+1,i="additional_image_"+Date.now(),l=`
            <div class="col-md-4 mb-3 additional-image-item" data-index="${n}">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Additional Image ${n}</small>
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
                                <div class="image-preview-container" id="${i}-preview-container" data-target="${i}">
                                    <div class="image-placeholder" id="${i}-placeholder">
                                        <i class="uil uil-image-plus"></i>
                                        <p>Click to upload image</p>
                                        <small>Recommended: 800x800px</small>
                                    </div>
                                    <div class="image-overlay">
                                        <button type="button" class="btn-change-image" data-target="${i}">
                                            <i class="uil uil-camera"></i> Change
                                        </button>
                                        <button type="button" class="btn-remove-image" data-target="${i}" style="display: none;">
                                            <i class="uil uil-trash-alt"></i> Remove
                                        </button>
                                    </div>
                                </div>
                                <input type="file" class="d-none image-file-input" id="${i}" name="additional_images[]" accept="image/jpeg,image/png,image/jpg,image/webp" data-preview="${i}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;$("#additional-images-container").append(l),s(i),o()}function s(a){const n=document.getElementById(a),i=document.getElementById(a+"-preview-container"),l=document.getElementById(a+"-placeholder"),r=i.querySelector(".btn-change-image"),c=i.querySelector(".btn-remove-image");i.addEventListener("click",d=>{!d.target.closest(".btn-change-image")&&!d.target.closest(".btn-remove-image")&&n.click()}),r&&r.addEventListener("click",d=>{d.stopPropagation(),d.preventDefault(),n.click()}),n.addEventListener("change",function(d){const f=d.target.files[0];if(f){const E=new FileReader;E.onload=function(T){let R=document.getElementById(a+"-preview-img");if(R)R.src=T.target.result;else{const h=document.createElement("img");h.id=a+"-preview-img",h.className="preview-image",h.src=T.target.result,i.insertBefore(h,l)}l&&(l.style.display="none"),c&&(c.style.display="inline-flex")},E.readAsDataURL(f)}}),c&&c.addEventListener("click",function(d){d.stopPropagation(),n.value="";const f=document.getElementById(a+"-preview-img");f&&f.remove(),l&&(l.style.display="flex"),c.style.display="none"})}function o(){$(".additional-image-item").length>0?($("#additional-images-empty-state").hide(),$("#additional-images-container").show()):($("#additional-images-empty-state").show(),$("#additional-images-container").hide())}function e(){$(".additional-image-item").each(function(a){const n=a+1;$(this).attr("data-index",n),$(this).find("small").text(`Additional Image ${n}`)})}}
