/**
 * CKEditor Initialization Module
 * This module handles CKEditor initialization for all textareas
 */

// Import CKEditor from the global scope (loaded via script tag)
window.initializeCKEditor = function() {
    // Wait for CKEditor to be available
    if (typeof CKEDITOR === 'undefined') {
        console.error('CKEditor is not loaded. Please check the script path.');
        return;
    }

    // Configure CKEditor globally
    CKEDITOR.config.height = 250;
    CKEDITOR.config.width = 'auto';
    CKEDITOR.config.removePlugins = 'elementspath';
    CKEDITOR.config.resize_enabled = false;

    // Toolbar configuration
    CKEDITOR.config.toolbar = [
        { name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },
        { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'SpellChecker', 'Scayt' ] },
        '/',
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
        '/',
        { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
        { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
        { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
    ];

    // Initialize CKEditor for ALL textareas in the form
    const textareas = document.querySelectorAll('textarea');

    textareas.forEach(function(textarea) {
        // Skip if already initialized
        if (CKEDITOR.instances[textarea.id]) {
            return;
        }

        const isRTL = textarea.getAttribute('dir') === 'rtl';

        CKEDITOR.replace(textarea.id, {
            language: isRTL ? 'ar' : 'en',
            contentsLangDirection: isRTL ? 'rtl' : 'ltr',
            height: 200,
            toolbar: [
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'links', items: [ 'Link', 'Unlink' ] },
                { name: 'styles', items: [ 'Format', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] }
            ],
            removePlugins: 'elementspath',
            resize_enabled: false,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_P
        });
    });

    console.log('CKEditor initialized for', textareas.length, 'textareas');
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for CKEditor to load
    setTimeout(function() {
        window.initializeCKEditor();
    }, 100);
});

// Export for module use
export { initializeCKEditor };
