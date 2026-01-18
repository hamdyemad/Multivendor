@props([
    'formId' => 'ajaxForm',
    'successMessage' => null,
    'redirectUrl' => null,
    'showSuccessAlert' => true,
    'reloadOnSuccess' => false,
])

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('{{ $formId }}');
    if (!form) {
        console.error('Form with ID "{{ $formId }}" not found');
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const alertContainer = document.getElementById('alertContainer');
    let originalBtnHtml = '';

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable submit button and show loading
        submitBtn.disabled = true;
        originalBtnHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("common.processing") ?? "Processing..." }}';

        // Show loading overlay
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.show();
        }

        // Clear previous alerts
        if (alertContainer) {
            alertContainer.innerHTML = '';
        }

        // Remove previous validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        // Start progress bar animation
        const progressPromise = typeof LoadingOverlay !== 'undefined' 
            ? LoadingOverlay.animateProgressBar(30, 300)
            : Promise.resolve();

        progressPromise.then(() => {
            // Prepare form data
            const formData = new FormData(form);

            // Send AJAX request
            return fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
        })
        .then(response => {
            // Progress to 60%
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.animateProgressBar(60, 200);
            }

            if (!response.ok) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            // Progress to 90%
            const progress = typeof LoadingOverlay !== 'undefined'
                ? LoadingOverlay.animateProgressBar(90, 200)
                : Promise.resolve();
            
            return progress.then(() => data);
        })
        .then(data => {
            // Complete progress bar
            const complete = typeof LoadingOverlay !== 'undefined'
                ? LoadingOverlay.animateProgressBar(100, 200)
                : Promise.resolve();

            return complete.then(() => {
                // Show success animation
                if (typeof LoadingOverlay !== 'undefined') {
                    const successMsg = '{{ $successMessage }}' || data.message || '{{ __("common.success") }}';
                    LoadingOverlay.showSuccess(successMsg, '{{ __("common.please_wait") }}');
                }

                // Show success alert
                @if($showSuccessAlert)
                if (alertContainer) {
                    showAlert('success', data.message || '{{ $successMessage }}' || '{{ __("common.success") }}');
                }
                @endif

                // Handle redirect or reload
                @if($redirectUrl)
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ $redirectUrl }}';
                    }, 1500);
                @elseif($reloadOnSuccess)
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                @else
                    setTimeout(() => {
                        if (typeof LoadingOverlay !== 'undefined') {
                            LoadingOverlay.hide();
                        }
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }, 1500);
                @endif
            });
        })
        .catch(error => {
            // Hide loading overlay
            if (typeof LoadingOverlay !== 'undefined') {
                LoadingOverlay.hide();
            }

            // Handle validation errors
            if (error.errors) {
                Object.keys(error.errors).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.classList.add('is-invalid');

                        // Remove any existing feedback
                        const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                        if (existingFeedback) {
                            existingFeedback.remove();
                        }

                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback d-block';
                        feedback.textContent = error.errors[key][0];
                        input.parentNode.appendChild(feedback);
                    }
                });
                
                if (alertContainer) {
                    showAlert('danger', error.message || '{{ __("common.please_check_form") }}');
                }
            } else {
                if (alertContainer) {
                    showAlert('danger', error.message || '{{ __("common.error_occurred") }}');
                }
            }

            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        });
    });

    // Show alert function
    function showAlert(type, message) {
        if (!alertContainer) return;
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show mb-20`;
        alert.innerHTML = `
            <i class="uil uil-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.appendChild(alert);

        // Scroll to top to show alert
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
