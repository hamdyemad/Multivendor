// Import jQuery (required by DataTables)
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import JSZip (required for Excel export)
import JSZip from 'jszip';
window.JSZip = JSZip;

// Import Select2
import 'select2';
import 'select2/dist/css/select2.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.css';

// Import DataTables
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';

// Import DataTables Buttons
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons-bs5/css/buttons.bootstrap5.css';
import 'datatables.net-buttons/js/buttons.html5.js';

// Global Password Visibility Toggle
document.addEventListener('DOMContentLoaded', function() {
    initPasswordToggle();

    // Observe body for dynamically added password inputs
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                initPasswordToggle();
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});

function initPasswordToggle() {
    const passwordInputs = document.querySelectorAll('input[type="password"]:not(.pw-toggle-init)');

    passwordInputs.forEach(input => {
        // Skip hidden inputs or inputs already initialized
        if (input.type === 'hidden' || input.classList.contains('pw-toggle-init')) return;
        
        input.classList.add('pw-toggle-init');

        // Check if already has a toggle icon in its vicinity
        const parent = input.parentElement;
        if (parent.querySelector('.toggle-password') || parent.querySelector('.password-toggle-icon')) {
            return;
        }

        // Create a wrapper to ensure the icon is centered RELATIVE TO THE INPUT ONLY
        const wrapper = document.createElement('div');
        wrapper.className = 'password-toggle-container position-relative w-100';
        
        // Transfer input's margin-bottom to wrapper to maintain layout spacing
        const inputStyle = window.getComputedStyle(input);
        if (inputStyle.marginBottom !== '0px') {
            wrapper.style.marginBottom = inputStyle.marginBottom;
            input.style.marginBottom = '0';
        }

        // Insert wrapper before input, then move input inside it
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        // Create eye icon
        const icon = document.createElement('i');
        icon.className = 'uil uil-eye-slash password-toggle-icon';

        // Add click listener
        icon.addEventListener('click', function(e) {
            e.preventDefault();
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');

            this.classList.toggle('uil-eye');
            this.classList.toggle('uil-eye-slash');
        });

        // Add icon to wrapper
        wrapper.appendChild(icon);
    });
}