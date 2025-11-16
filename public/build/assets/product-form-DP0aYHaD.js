let g=1;const b=4;let m={},p=0,f=null;document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll(".wizard-step-content").forEach(function(l,i){i===0?l.classList.add("active"):l.classList.remove("active")})});jQuery(document).ready(function(t){console.log("✅ Product form jQuery ready"),setTimeout(function(){t("#brand_id, #vendor_id, #department_id, #category_id, #sub_category_id, #tax_id, #configuration_type").each(function(){var e=t(this).find('option[value=""]').text().trim();console.log("📋 Select2 Init - ID:",t(this).attr("id"),"Placeholder:",e),t(this).select2({theme:"bootstrap-5",width:"100%",allowClear:!1,placeholder:e||"Select An Option"})})},100),t("#vendor_id").on("change",function(){var n;const e=t(this).val(),o=((n=window.productFormConfig)==null?void 0:n.vendorActivitiesMap)||{},s=o[e]||[];console.log("🔄 Vendor changed to:",e),console.log("📊 Vendor Activities Map:",o),console.log("📋 Vendor Activities:",s),t("#department_id option").each(function(){const a=t(this);if(a.val()===""){a.show();return}try{let d=a.attr("data-activities"),c=[];if(d){const h=document.createElement("textarea");h.innerHTML=d;const q=h.value;c=JSON.parse(q)}c.some(h=>s.includes(h))||s.length===0?a.show():(a.hide(),a.is(":selected")&&t("#department_id").val("").trigger("change"))}catch(d){console.error("Error parsing department activities:",d),a.show()}}),t("#department_id").select2("destroy").select2({theme:"bootstrap-5",width:"100%",allowClear:!1})}),setTimeout(function(){T()},500),window.testErrorContainers=function(){console.log("🧪 Testing error containers..."),[1,2].forEach(o=>{const s=`error-translations-${o}-title`,n=t(`#${s}`);console.log(`📝 Container #${s}: exists=${n.length>0}`),n.length>0&&(n.html('<i class="uil uil-exclamation-triangle"></i> Test error message').show(),console.log(`✅ Test error displayed in ${s}`),setTimeout(()=>{n.hide().empty(),console.log(`🧹 Cleared test error from ${s}`)},3e3))}),console.log("💡 Check the form to see test error messages appear and disappear")},window.testTitleError=function(){console.log("🧪 Testing Title (English) error specifically...");const e=t('input[name="translations[1][title]"]'),o=t("#error-translations-1-title");console.log("📝 Title input found:",e.length>0),console.log("📝 Title input element:",e[0]),console.log("📝 Error container found:",o.length>0),console.log("📝 Error container element:",o[0]),o.length>0?(console.log("📝 Container current display:",o.css("display")),console.log("📝 Container current visibility:",o.is(":visible")),console.log("📝 Container classes:",o.attr("class")),console.log("📝 Container style attribute:",o.attr("style")),o.html('<i class="uil uil-exclamation-triangle"></i> TEST: Title is required for English'),o.show(),o.css("display","block"),o.css("visibility","visible"),o.removeClass("d-none").addClass("d-block"),o.attr("style","display: block !important;"),console.log("📝 After force show - display:",o.css("display")),console.log("📝 After force show - visible:",o.is(":visible")),console.log("📝 After force show - style:",o.attr("style")),e.addClass("is-invalid"),console.log("✅ Test error should now be visible under Title (English) field")):console.log("❌ Error container not found for Title (English)")},window.testErrorContainer=function(){console.log("🧪 Direct test of error-translations-1-title container...");const e=t("#error-translations-1-title");console.log("📝 Container found:",e.length>0),console.log("📝 Container element:",e[0]),e.length>0?(console.log("📝 Current display:",e.css("display")),console.log("📝 Current visibility:",e.css("visibility")),console.log("📝 Is visible:",e.is(":visible")),console.log("📝 Current content:",e.html()),console.log("📝 Current classes:",e.attr("class")),console.log("📝 Current style:",e.attr("style")),e.html('<i class="uil uil-exclamation-triangle"></i> DIRECT TEST MESSAGE'),e.show(),e.css("display","block"),e.css("visibility","visible"),e.removeClass("d-none").addClass("d-block"),e.attr("style","display: block !important; visibility: visible !important;"),console.log("📝 After force show:"),console.log("📝 Display:",e.css("display")),console.log("📝 Visibility:",e.css("visibility")),console.log("📝 Is visible:",e.is(":visible")),console.log("📝 Style attr:",e.attr("style")),console.log("✅ Direct test completed - check if message appears")):console.log("❌ Container #error-translations-1-title not found")};function l(){console.log("🔧 Attaching event handlers to Select2 dropdowns..."),t("#vendor_id");const e=t("#department_id"),o=window.productFormConfig.vendorActivitiesMap||{};Object.keys(o).length>0&&(console.log("👤 Admin/Super Admin user detected - hiding departments until vendor selected"),e.find("option[value!=''][data-activities]").hide()),t(document).off("change.productForm","#vendor_id").on("change.productForm","#vendor_id",function(n){console.log("🎯 Vendor changed");const a=t(this).val(),r=t("#department_id");r.val();const d=`${window.productFormConfig.departmentsRoute}?vendor_id=${a}&select2=1`;console.log("🔍 Vendor ID:",a),fetch(d,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest",lang:document.documentElement.lang||"en"}}).then(c=>{if(console.log("📥 Departments response status:",c.status),!c.ok)throw new Error(`HTTP error! status: ${c.status}`);return c.json()}).then(c=>{console.log("✅ Departments API response:",c),r.empty().append('<option value="">Select Department</option>').prop("disabled",!1),c.status&&c.data&&c.data.length>0?(c.data.forEach(u=>{r.append(`<option value="${u.id}">${u.name}</option>`)}),console.log(`✅ Loaded ${c.data.length} departments`)):(console.log("⚠️ No departments found for vendor:",a),r.append('<option value="">No departments available</option>')),r.trigger("change")}).catch(c=>{console.error("❌ Error loading departments:",c),r.empty().append('<option value="">Error loading departments</option>').prop("disabled",!1).trigger("change")})}),t(document).off("change.productForm","#department_id").on("change.productForm","#department_id",function(n){console.log("🎯 Department event triggered:",n.type);const a=t(this).val();console.log("🔄 Department changed:",a);const r=t("#category_id"),d=t("#sub_category_id");if(r.empty().append('<option value="">Loading categories...</option>').prop("disabled",!0).trigger("change"),d.empty().append('<option value="">Select Sub Category</option>').val("").trigger("change"),a){const c=`${window.productFormConfig.categoriesRoute}?department_id=${a}&select2=1`;console.log("🌐 Fetching categories from:",c),fetch(c,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest",lang:document.documentElement.lang||"en"}}).then(u=>{if(console.log("📥 Categories response status:",u.status),!u.ok)throw new Error(`HTTP error! status: ${u.status}`);return u.json()}).then(u=>{console.log("✅ Categories API response:",u),r.empty().append('<option value="">Select Category</option>').prop("disabled",!1),u.status&&u.data&&u.data.length>0?(u.data.forEach(h=>{r.append(`<option value="${h.id}">${h.name}</option>`)}),console.log(`✅ Loaded ${u.data.length} categories`)):(console.log("⚠️ No categories found for department:",a),r.append('<option value="">No categories available</option>')),r.trigger("change")}).catch(u=>{console.error("❌ Error loading categories:",u),r.empty().append('<option value="">Error loading categories</option>').prop("disabled",!1).trigger("change")})}else r.empty().append('<option value="">Select Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Department handler attached"),t("#category_id").off("change.productForm select2:select.productForm"),t(document).off("change.productForm","#category_id").on("change.productForm","#category_id",function(n){console.log("🎯 Category event triggered:",n.type);const a=t(this).val();console.log("🔄 Category changed:",a);const r=t("#sub_category_id");if(r.empty().append('<option value="">Loading subcategories...</option>').prop("disabled",!0).trigger("change"),a){const d=`${window.productFormConfig.subCategoriesRoute}?category_id=${a}`;console.log("🌐 Fetching subcategories from:",d),fetch(d,{method:"GET",headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"}}).then(c=>{if(console.log("📥 SubCategories response status:",c.status),!c.ok)throw new Error(`HTTP error! status: ${c.status}`);return c.json()}).then(c=>{console.log("✅ SubCategories API response:",c),r.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1),c.status&&c.data&&c.data.length>0?(c.data.forEach(u=>{r.append(`<option value="${u.id}">${u.name}</option>`)}),console.log(`✅ Loaded ${c.data.length} subcategories`)):(console.log("⚠️ No subcategories found for category:",a),r.append('<option value="">No subcategories available</option>')),r.trigger("change")}).catch(c=>{console.error("❌ Error loading subcategories:",c),r.empty().append('<option value="">Error loading subcategories</option>').prop("disabled",!1).trigger("change")})}else r.empty().append('<option value="">Select Sub Category</option>').prop("disabled",!1).trigger("change")}),console.log("✅ Category handler attached"),console.log("✅ All handlers ready!")}function i(){const e=t("#department_id");e.length&&e.hasClass("select2-hidden-accessible")?l():setTimeout(i,100)}setTimeout(i,200),t("#productForm").on("submit",S),v(g),t("#nextBtn").on("click",function(){console.log("📍 Next button clicked. Current step:",g),g++,g>b&&(g=b),v(g)}),t("#prevBtn").on("click",function(){g--,g<1&&(g=1),v(g)}),t(".wizard-step-nav").on("click",function(){console.log("🖱️ Wizard step clicked!");const e=parseInt(t(this).data("step"));console.log("Clicked step:",e),t("#validation-alerts-container").hide().empty(),g=e,v(g)}),t(document).on("click",".edit-step",function(){g=parseInt(t(this).data("step")),v(g),t("html, body").animate({scrollTop:t(".card").offset().top-100},300)}),t("#productForm").on("submit",S),t("#productForm").on("input keyup change","input, textarea, select",function(){const e=t(this),o=e.attr("name");if(o){e.removeClass("is-invalid");let s=null;if(o.includes("translations[")){const n=o.match(/translations\[(\d+)\]\[([^\]]+)\]/);if(n){const a=n[1],r=n[2];s=t(`#error-translations-${a}-${r}`)}}if(!s||!s.length){const n=[`#error-${o}`,`#error-${o.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`,`[data-error-for="${o}"]`];for(const a of n)if(s=t(a),s.length>0)break}s&&s.length&&s.hide().empty(),e.closest(".form-group").find(".error-message:not([id])").remove(),e.siblings(".error-message:not([id])").remove(),(e.hasClass("select2")||e.data("select2"))&&e.next(".select2-container").siblings(".error-message:not([id])").remove(),console.log(`🧹 Cleared error for field: ${o}`)}}),t("#configuration_type").on("change",function(){const e=t(this).val();e==="simple"?(t("#simple-product-section").show(),t("#variants-section").hide()):e==="variants"?(t("#simple-product-section").hide(),t("#variants-section").show()):(t("#simple-product-section").hide(),t("#variants-section").hide())}),t("#has_discount").on("change",function(){t(this).is(":checked")?t("#discount-fields").slideDown():(t("#discount-fields").slideUp(),t("#price_before_discount").val(""),t("#offer_end_date").val(""))}),t("#add-stock-row").on("click",function(){V()}),t(document).on("click",".remove-stock-row",function(){t(this).closest("tr").remove(),y(),A(),_()}),t(document).on("input",".stock-quantity",function(){y()}),t("#add-variant-btn").on("click",function(){P()}),t(document).on("click",".remove-variant-btn",function(){t(this).closest(".variant-box").remove(),R(),W()}),t(document).on("change",".variant-key-select",function(){const e=t(this).closest(".variant-box"),o=t(this).val();o?(e.find(".variant-tree-container").show(),N(e,o)):(e.find(".variant-tree-container").hide(),e.find(".final-variant-id").val(""))}),t(document).on("change",".variant-level-select",function(){const e=t(this).closest(".variant-box"),o=parseInt(t(this).data("level")),s=t(this).val(),n=t(this).find("option:selected").data("has-children");e.find(".final-variant-id").val(""),s?M(e,o,s,n):(e.find(".nested-variant-levels").find("[data-level]").each(function(){parseInt(t(this).data("level"))>o&&t(this).remove()}),x(e))}),t(document).on("click",".add-stock-row-variant",function(){const e=t(this).data("variant-index");X(e)}),t(document).on("click",".remove-variant-stock-row",function(){const e=t(this).closest("tr"),o=t(this).data("variant-index"),s=t(`.variant-stock-rows[data-variant-index="${o}"]`);e.remove(),s.find("tr").each(function(n){t(this).find("td:first").text(n+1)}),s.find("tr").length===0&&(t(`.variant-stock-table-container[data-variant-index="${o}"]`).hide(),t(`.variant-stock-empty-state[data-variant-index="${o}"]`).show()),k(o)}),t(document).on("input",".variant-stock-quantity",function(){const o=t(this).closest("tr").data("variant-index");k(o)}),D(),typeof LoadingOverlay<"u"&&LoadingOverlay.init?(console.log("🔄 Initializing LoadingOverlay..."),LoadingOverlay.init(),console.log("✅ LoadingOverlay initialized")):console.warn("⚠️ LoadingOverlay not available"),console.log("✅ Product form navigation initialized")});function D(){console.log("🌍 Loading regions data..."),$.ajax({url:"/api/area/regions?select2=1",method:"GET",dataType:"json",success:function(l){f=l.data,console.log("✅ Regions loaded successfully:",f)},error:function(l,i,e){console.log("❌ API error, using fallback regions"),f=[{id:1,text:"Cairo",name:"Cairo"},{id:2,text:"Alexandria",name:"Alexandria"},{id:3,text:"Giza",name:"Giza"},{id:4,text:"Luxor",name:"Luxor"},{id:5,text:"Aswan",name:"Aswan"}],console.log("✅ Fallback regions set:",f)}})}function v(t){$(".wizard-step-content").each(function(){$(this).removeClass("active").css("display","none")});const l=$(`.wizard-step-content[data-step="${t}"]`);if(l.length&&l.addClass("active").css("display","block"),Object.keys(m).length>0&&t!==4)for(let i in m){const e=E(i),o=l.find(`[name="${e}"], [name="${e}[]"], [name="${i}"], [name="${i}[]"]`).first();if(o.length){o.addClass("is-invalid");let s=null;if(i.includes("translations.")){const n=i.split(".");if(n.length===3&&n[0]==="translations"){const a=n[1],r=n[2],d=`error-translations-${a}-${r}`;s=$(`#${d}`)}}if(!s||!s.length){const n=o.attr("name"),a=[`#error-${i}`,`#error-${n}`,`#error-${i.replace(/\./g,"-")}`,`#error-${n.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`];for(const r of a)if(s=$(r),s.length>0)break}if(s&&s.length){const n=`<i class="uil uil-exclamation-triangle"></i> ${m[i][0]}`;s.html(n).show().css("display","block").removeClass("d-none").addClass("d-block")}else{o.closest(".form-group").find(".error-message:not([id])").remove();const n=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${m[i][0]}</div>`;if(o.hasClass("select2")||o.data("select2")){const a=o.next(".select2-container");a.length?a.after(n):o.after(n)}else o.after(n)}}}$(".wizard-step-nav").removeClass("current"),$(`.wizard-step-nav[data-step="${t}"]`).addClass("current"),$(".wizard-step-nav").each(function(){parseInt($(this).data("step"))<t?$(this).addClass("completed"):$(this).removeClass("completed")}),t===1?$("#prevBtn").hide():$("#prevBtn").show(),t===b?($("#nextBtn").hide(),$("#submitBtn").show()):($("#nextBtn").show(),$("#submitBtn").hide()),$("html, body").animate({scrollTop:$(".card-body").offset().top-100},300)}function E(t){const l=t.split(".");if(l.length===1)return t;let i=l[0];for(let e=1;e<l.length;e++)i+=`[${l[e]}]`;return i}function T(){console.log("🔧 Ensuring all form fields have error containers..."),$("#productForm").find("input, select, textarea").each(function(){const t=$(this),l=t.attr("name");if(!l)return;const i=`error-${l.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`;if($(`#${i}`).length===0){const e=`<div class="error-message text-danger" id="${i}" style="display: none;"></div>`;if(t.hasClass("select2")||t.data("select2")){const o=t.next(".select2-container");o.length?o.after(e):t.after(e)}else t.after(e);console.log(`✅ Created error container for: ${l}`)}})}function F(t){m=t,T();let l='<ul class="mb-0">';for(let i in t){const e=t[i];e.forEach(n=>{l+=`<li class="mb-2">${n}</li>`});const o=E(i),s=$(`[name="${o}"], [name="${o}[]"], [name="${i}"], [name="${i}[]"]`).first();if(console.log(`🔍 Looking for field: ${i} -> ${o}, found: ${s.length>0}`),i.includes("translations.")&&i.includes(".title")){console.log(`📝 Translation title field detected: ${i}`);const n=`error-translations-${i.split(".")[1]}-title`;console.log(`📝 Expected error container ID: ${n}`),console.log("📝 Container exists:",$(`#${n}`).length>0),console.log("📝 Container element:",$(`#${n}`)[0])}if(s.length){s.addClass("is-invalid");let n=null;if(i.includes("translations.")){const a=i.split(".");if(a.length===3&&a[0]==="translations"){const r=a[1],d=a[2],c=`error-translations-${r}-${d}`;n=$(`#${c}`),console.log(`🔍 Looking for translation container: #${c}, found: ${n.length>0}`),n.length>0&&console.log("📝 Container element:",n[0])}}if(!n||!n.length){const a=s.attr("name"),r=[`#error-${i}`,`#error-${a}`,`#error-${i.replace(/\./g,"-")}`,`#error-${a.replace(/\[|\]/g,"-").replace(/--/g,"-").replace(/-$/,"")}`];console.log("🔍 Trying fallback selectors:",r);for(const d of r)if(n=$(d),n.length>0){console.log(`✅ Found with selector: ${d}`);break}}if(n&&n.length){const a=`<i class="uil uil-exclamation-triangle"></i> ${e[0]}`;console.log(`✅ Using existing container for ${i}, setting content: ${a}`),n.html(a),n.show(),n.css("display","block"),n.css("visibility","visible"),n.removeClass("d-none").addClass("d-block"),n.attr("style","display: block !important;"),console.log(`✅ Container after update - visible: ${n.is(":visible")}, display: ${n.css("display")}`)}else{const a=`<div class="error-message text-danger small mt-1"><i class="uil uil-exclamation-triangle"></i> ${e[0]}</div>`;if(s.closest(".form-group").find(".error-message:not([id])").remove(),s.hasClass("select2")||s.data("select2")){const r=s.next(".select2-container");r.length?r.after(a):s.after(a)}else s.after(a);console.log(`✅ Created new error message for field: ${i}`)}}else console.log(`❌ Field element not found for: ${i} (${o})`)}if(l+="</ul>",Object.keys(t).length>0){const e=`
            <div class="alert alert-danger alert-dismissible fade show validation-errors-alert" role="alert">
                <div class="d-flex align-items-start">
                    <i class="uil uil-exclamation-triangle me-2" style="font-size: 18px; margin-top: 2px;"></i>
                    <div class="flex-grow-1">
                        <h6 class="mb-2">${document.documentElement.dir==="rtl"||document.documentElement.lang==="ar"||$("html").attr("lang")==="ar"?"يرجى تصحيح الأخطاء التالية:":"Please correct the following errors:"}</h6>
                        ${l}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;$(".validation-errors-alert").remove();const o=$("#validation-alerts-container");o.length?(console.log("✅ Adding alert to validation-alerts-container"),o.html(e).show()):(console.log("⚠️ validation-alerts-container not found, using fallback"),$(".card-body").prepend(e)),setTimeout(()=>{const s=$(".validation-errors-alert");s.length?(s.show(),console.log("✅ Alert should now be visible"),$("html, body").animate({scrollTop:s.offset().top-100},300)):console.log("❌ Alert element not found after creation")},100)}}function I(){$(".wizard-step-content:not(.active)").each(function(){$(this).find("[required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}),$("#simple-product-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")}),$("#variants-section:hidden [required]").each(function(){$(this).attr("data-was-required","true").removeAttr("required")})}function C(){$('[data-was-required="true"]').each(function(){$(this).attr("required","required").removeAttr("data-was-required")})}function S(t){console.log("Form submission started"),t.preventDefault();const l=window.productFormConfig;if(!l){console.error("productFormConfig is not defined");return}if(I(),typeof LoadingOverlay<"u")LoadingOverlay.overlay||(console.log("Initializing LoadingOverlay..."),LoadingOverlay.init());else{console.error("LoadingOverlay is not defined");return}const i=new FormData(this),e=$(this).attr("action"),s=$('input[name="_method"][value="PUT"]').length>0?l.updatingProduct||"Updating product...":l.creatingProduct||"Creating product...",n=document.getElementById("loadingOverlay");n&&(n.querySelector(".loading-text").textContent=s,n.querySelector(".loading-subtext").textContent=l.pleaseWait||"Please wait..."),LoadingOverlay.show(),LoadingOverlay.animateProgressBar(30,300).then(()=>fetch(e,{method:"POST",body:i,headers:{"X-Requested-With":"XMLHttpRequest",Accept:"application/json"}})).then(a=>(LoadingOverlay.animateProgressBar(60,200),a.ok?a.json():a.json().then(r=>{throw r}))).then(a=>LoadingOverlay.animateProgressBar(90,200).then(()=>a)).then(a=>LoadingOverlay.animateProgressBar(100,200).then(()=>{C();const r=$('input[name="_method"][value="PUT"]').length>0,d=a.message||(r?l.productUpdated:l.productCreated)||"Product saved successfully!";LoadingOverlay.showSuccess(d,l.redirecting||"Redirecting..."),setTimeout(()=>{window.location.href=a.redirect||l.indexRoute||"/admin/products"},1500)})).catch(a=>{if(LoadingOverlay.hide(),C(),console.log("Error:",a),a.errors)console.log("Validation errors:",a.errors),F(a.errors);else{const r=a.message||"An error occurred. Please try again.";console.error("Error message:",r),alert(r)}})}function V(){O(f)}function O(t){const l=$(".stock-row").length;let i='<option value="">Select Region</option>';t.forEach(s=>{const n=s.text||s.name;i+=`<option value="${s.id}">${n}</option>`});const o=`
        <tr class="stock-row">
            <td>${l+1}</td>
            <td>
                <select name="stocks[${l}][region_id]" class="form-control select2-stock" required>
                    ${i}
                </select>
            </td>
            <td>
                <input type="number" name="stocks[${l}][quantity]" class="form-control stock-quantity" min="0" value="0" required>
            </td>
            <td class="text-center actions">
                <button type="button" class="btn btn-sm btn-danger remove-stock-row">
                    <i class="uil uil-trash-alt m-0"></i>
                </button>
            </td>
        </tr>
    `;$("#stock-rows").append(o),A(),$(".select2-stock").select2({theme:"bootstrap-5",width:"100%"}),y(),_()}function _(){$(".stock-row").each(function(t){$(this).find("td:first").text(t+1)})}function A(){$(".stock-row").length>0?($("#stock-table-container").show(),$("#stock-empty-state").hide()):($("#stock-table-container").hide(),$("#stock-empty-state").show())}function y(){let t=0;$(".stock-quantity").each(function(){const l=parseInt($(this).val())||0;t+=l}),$("#total-stock").text(t)}function P(){p++;const t=window.productFormConfig,l=`
        <div class="variant-box card mb-3" data-variant-index="${p}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-primary variant-title">
                            <i class="uil uil-cube"></i>
                            ${t.variantNumber} ${p}
                        </h6>
                        <small class="text-muted variant-details-path" style="display: none;">
                            <strong>${t.variantDetails}:</strong> <span class="variant-path-text"></span>
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                        <i class="uil uil-trash-alt m-0"></i> ${t.remove}
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">${t.selectVariantKey} <span class="text-danger">*</span></label>
                        <select name="variants[${p}][key_id]" class="form-control variant-key-select" required>
                            <option value="">${t.loadingVariantKeys}</option>
                        </select>
                        <small class="text-muted">${t.selectVariantKeyHelper}</small>
                    </div>
                </div>

                <div class="variant-tree-container" style="display: none;">
                    <div class="nested-variant-levels">
                        <!-- Dynamic variant levels will be added here -->
                    </div>

                    <!-- Hidden input to store the final selected variant ID -->
                    <input type="hidden" name="variants[${p}][value_id]" class="final-variant-id">

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
                                ${t.productDetails}
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${t.variantSku} <span class="text-danger">*</span></label>
                                        <input type="text" name="variants[${p}][sku]" class="form-control ih-medium ip-gray radius-xs b-light px-15" placeholder="PRD-12345">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">${t.price} <span class="text-danger">*</span></label>
                                        <input type="number" name="variants[${p}][price]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter price" required>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label d-block">${t.enableDiscountOffer}</label>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input variant-discount-toggle" type="checkbox" role="switch" name="variants[${p}][has_discount]" value="1">
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Fields (shown when discount is checked) -->
                                <div class="variant-discount-fields" style="display: none;" class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">${t.priceBeforeDiscount}</label>
                                                <input type="number" name="variants[${p}][price_before_discount]" class="form-control ih-medium ip-gray radius-xs b-light px-15" min="0" step="0.01" placeholder="Enter original price">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">${t.offerEndDate}</label>
                                                <input type="date" name="variants[${p}][offer_end_date]" class="form-control ih-medium ip-gray radius-xs b-light px-15">
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
                                    ${t.stockPerRegion}
                                </div>
                                <button type="button" class="btn btn-primary btn-sm add-stock-row-variant" data-variant-index="${p}">
                                    <i class="uil uil-plus"></i> ${t.addNewRegion}
                                </button>
                            </h5>

                            <!-- Empty state message -->
                            <div class="variant-stock-empty-state text-center py-4" data-variant-index="${p}">
                                <i class="uil uil-package text-muted" style="font-size: 48px;"></i>
                                <p class="text-muted mb-0">${t.noRegionsAddedYet}</p>
                            </div>

                            <!-- Stock table (hidden initially) -->
                            <div class="variant-stock-table-container" data-variant-index="${p}" style="display: none;">
                                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-bordered table-hover variant-stock-table" data-variant-index="${p}" style="width:100%">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th><span class="userDatatable-title">#</span></th>
                                                    <th><span class="userDatatable-title">${t.region}</span></th>
                                                    <th><span class="userDatatable-title">${t.stockQuantity}</span></th>
                                                    <th><span class="userDatatable-title">${t.actionsLabel}</span></th>
                                                </tr>
                                            </thead>
                                            <tbody class="variant-stock-rows" data-variant-index="${p}">
                                                <!-- Stock rows will be added here -->
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="2" class="text-center fw-bold">${t.totalStock}:</td>
                                                    <td class="fw-bold text-primary">
                                                        <span class="variant-total-stock" data-variant-index="${p}">0</span>
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
    `;$("#variants-container").append(l),R(),j(p),H(p)}function j(t){const i=$(`.variant-box[data-variant-index="${t}"]`).find(".variant-key-select");$.ajax({url:"/admin/api/variant-keys",method:"GET",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(e){if(e.success&&e.data){let o='<option value="">Select Variant Key</option>';e.data.forEach(s=>{o+=`<option value="${s.id}">${s.name}</option>`}),i.html(o)}else i.html('<option value="">Error loading keys</option>')},error:function(){i.html('<option value="">Error loading keys</option>')}})}function N(t,l){const i=t.find(".nested-variant-levels"),e=t.find(".variant-selection-info");if(!l){i.empty(),e.hide();return}i.empty(),e.show().find(".selection-text").text("Loading variants..."),L(t,l,null,0)}function L(t,l,i,e){const o=t.find(".nested-variant-levels"),s=window.productFormConfig;$.ajax({url:"/admin/api/variants-by-key",method:"GET",data:{key_id:l,parent_id:i||"root"},headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content"),Accept:"application/json"},success:function(n){if(n.success&&n.data&&n.data.length>0){const a=e===0?s.rootVariantsLabel:`${s.selectLevel} ${e+1}`,r=`
                    <div class="variant-level mb-3" data-level="${e}">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">${a} <span class="text-danger">*</span></label>
                                <select class="form-control variant-level-select" data-level="${e}">
                                    <option value="">Select ${a}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;o.append(r);const d=o.find(`[data-level="${e}"]`).find(".variant-level-select");let c='<option value="">Select variant</option>';n.data.forEach(u=>{const h=u.has_children?" 🌳":"";c+=`<option value="${u.id}" data-has-children="${u.has_children}">${u.name}${h}</option>`}),d.html(c),d.select2({theme:"bootstrap-5",width:"100%"}),n.data.length===1&&!n.data[0].has_children&&d.val(n.data[0].id).trigger("change"),x(t)}else e===0&&t.find(".variant-selection-info").show().find(".selection-text").text("No variants available for this key")},error:function(){console.error("Error loading variant level",e)}})}function M(t,l,i,e){const o=t.find(".nested-variant-levels"),s=t.find(".variant-key-select").val();o.find("[data-level]").each(function(){parseInt($(this).data("level"))>l&&$(this).remove()}),i&&e?L(t,s,i,l+1):i&&z(t,i),x(t)}function z(t,l){t.find(".final-variant-id").val(l),G(t)}function x(t){const l=window.productFormConfig,i=t.find(".variant-selection-info"),e=i.find(".selection-text"),o=t.find(".final-variant-id").val(),s=t.find(".variant-product-details"),n=t.find(".variant-details-path"),a=t.find(".variant-path-text");if(o){const r=[];if(t.find(".variant-level-select").each(function(){const d=$(this).find("option:selected");d.val()&&r.push(d.text().replace(" 🌳",""))}),r.length>0){const d=r.join(" - ");e.html(`<strong>${l.selectedColon}</strong> ${r.join(" → ")}`),i.removeClass("alert-info").addClass("alert-success").show(),a.text(d),n.show()}}else e.text(l.pleaseSelectVariant),i.removeClass("alert-success").addClass("alert-info").show(),n.hide(),s.hide()}function H(t){$(`.variant-box[data-variant-index="${t}"]`).find(".variant-key-select").select2({theme:"bootstrap-5",width:"100%"})}function G(t,l){t.find(".variant-product-details").show();const e=t.find(".variant-discount-toggle"),o=t.find(".variant-discount-fields");e.on("change",function(){$(this).is(":checked")?o.show():(o.hide(),o.find("input").val(""))})}function X(t){f&&f.length>0?w(t,f):(console.log("⏳ Regions not loaded yet for variant, waiting..."),setTimeout(function(){f&&f.length>0?w(t,f):(console.log("⚠️ Using fallback regions for variant"),U(t))},500))}function w(t,l){const i=$(`.variant-stock-rows[data-variant-index="${t}"]`),e=i.find("tr").length;let o='<option value="">Select Region</option>';l.forEach(function(a){o+=`<option value="${a.id}">${a.text}</option>`});const s=`
        <tr class="variant-stock-row" data-variant-index="${t}" data-row-index="${e}">
            <td class="text-center">${e+1}</td>
            <td>
                <select name="variants[${t}][stock][${e}][region_id]" class="form-control region-select" required>
                    ${o}
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
    `;i.append(s),i.find("tr").last().find(".region-select").select2({theme:"bootstrap-5",width:"100%"}),$(`.variant-stock-table-container[data-variant-index="${t}"]`).show(),$(`.variant-stock-empty-state[data-variant-index="${t}"]`).hide(),k(t)}function U(t){w(t,[{id:1,text:"Cairo"},{id:2,text:"Alexandria"},{id:3,text:"Giza"},{id:4,text:"Luxor"},{id:5,text:"Aswan"}])}function k(t){const l=$(`.variant-stock-rows[data-variant-index="${t}"]`),i=$(`.variant-total-stock[data-variant-index="${t}"]`);let e=0;l.find(".variant-stock-quantity").each(function(){const o=parseInt($(this).val())||0;e+=o}),i.text(e)}function R(){$(".variant-box").length>0?($("#variants-empty-state").hide(),$("#variants-container").show()):($("#variants-empty-state").show(),$("#variants-container").hide())}function W(){$(".variant-box").each(function(t){const l=t+1;$(this).find("h6").html(`<i class="uil uil-cube"></i> Variant ${l}`)})}
