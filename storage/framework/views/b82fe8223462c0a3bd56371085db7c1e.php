<!-- WhatsApp Button -->
<span class="whats_app_modern_design">
    <a href="https://wa.me/+201011559674" target="_blank" class="whatsapp-float" title="الدردشة على واتساب">
        <i class="uil uil-whatsapp"></i>
        <span class="whatsapp-text">Whatsapp</span>
    </a>
</span>

<!-- Scroll to Top Button -->
<button id="scrollTopBtn" class="scroll-top-float" title="الرجوع للأعلى">
    <i class="uil uil-arrow-up"></i>
</button>

<style>
    /* WhatsApp Floating Button */
    .whatsapp-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 10px;
        right: 100px;
        background-color: #25d366;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        text-decoration: none;
        overflow: hidden;
        padding: 0 10px;
        /* مساحة داخلية صغيرة للنص */
        white-space: nowrap;
    }

    .whatsapp-float i {
        font-size: 30px;
        transition: margin 0.3s ease;
    }

    .whatsapp-float .whatsapp-text {
        opacity: 0;
        font-size: 16px;
        font-weight: 500;
        margin-left: -70px;
        transition: opacity 0.3s ease, margin-left 0.3s ease, margin-right 0.3s ease;
        transform: translateX(-10px);
    }

    /* حالة الـ Hover */
    .whatsapp-float:hover {
        width: 150px;
        background-color: #128c7e;
        justify-content: flex-start;
        padding-left: 18px;
        padding-right: 18px;
        transform: scale(1.05);
    }

    .whatsapp-float:hover i {
        margin-right: 8px;
    }

    .whatsapp-float:hover .whatsapp-text {
        opacity: 1;
        margin-left: 8px;
        transform: translateX(0);
    }

    /* RTL Support */
    [dir="rtl"] .whatsapp-float {
        right: auto;
        left: 100px;
        flex-direction: row-reverse;
    }

    [dir="rtl"] .whatsapp-float:hover {
        justify-content: flex-start;
        padding-left: 18px;
        padding-right: 18px;
    }

    [dir="rtl"] .whatsapp-float i {
        margin-left: 0;
        margin-right: 0;
    }

    [dir="rtl"] .whatsapp-float:hover i {
        margin-left: 8px;
        margin-right: 0;
    }

    [dir="rtl"] .whatsapp-float .whatsapp-text {
        margin-left: 0;
        margin-right: -70px;
        transform: translateX(10px);
    }

    [dir="rtl"] .whatsapp-float:hover .whatsapp-text {
        margin-left: 0;
        margin-right: 8px;
        transform: translateX(0);
    }

    /* Responsive */
    @media screen and (max-width: 768px) {
        .whatsapp-float {
            width: 50px;
            height: 50px;
            bottom: 10px;
            right: 85px;
            font-size: 25px;
            padding: 0 8px;
        }

        .whatsapp-float i {
            font-size: 25px;
        }

        .whatsapp-float .whatsapp-text {
            font-size: 14px;
        }

        .whatsapp-float:hover {
            width: 130px;
            padding-left: 15px;
            padding-right: 15px;
        }

        [dir="rtl"] .whatsapp-float {
            right: auto;
            left: 85px;
        }
    }

    /* Scroll to Top Button */
    .scroll-top-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 10px;
        right: 30px;
        background-color: #007bff;
        color: #FFF;
        border: none;
        border-radius: 50px;
        font-size: 28px;
        cursor: pointer;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .scroll-top-float:hover {
        background-color: #0056b3;
        transform: scale(1.1);
        box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.4);
    }

    [dir="rtl"] .scroll-top-float {
        right: auto;
        left: 30px;
    }

    @media screen and (max-width: 768px) {
        .scroll-top-float {
            width: 50px;
            height: 50px;
            bottom: 10px;
            right: 20px;
            font-size: 24px;
        }

        [dir="rtl"] .scroll-top-float {
            right: auto;
            left: 20px;
        }
    }

    .whats_app_modern_design > a:hover {
        color: #fff !important;
    }
</style>

<script>
    // Scroll to top functionality
    const scrollTopBtn = document.getElementById("scrollTopBtn");
    scrollTopBtn.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
</script>
<?php /**PATH C:\laragon\www\eramo-multi-vendor\resources\views/partials/whatsapp-button.blade.php ENDPATH**/ ?>