@props([
    'formSelector' => '#form',
    'loadingText' => 'Processing...',
    'successText' => 'Success!',
    'redirectUrl' => null,
    'redirectDelay' => 1500,
    'showProgressBar' => true,
    'showErrorAlert' => true,
    'showFieldErrors' => true,
    'scrollOffset' => 100,
    'autoInit' => true
])

@once
    @push('styles')
        @vite(['resources/assets/scss/form-error-handler.scss'])
        @vite(['resources/assets/scss/global-loader.scss'])
    @endpush
    
    @push('scripts')
        @vite(['resources/assets/js/form-error-handler.js'])
    @endpush
@endonce

@if($autoInit)
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize form error handler
                window.formErrorHandler = new FormErrorHandler({
                    formSelector: '{{ $formSelector }}',
                    loadingText: '{{ $loadingText }}',
                    successText: '{{ $successText }}',
                    redirectUrl: '{{ $redirectUrl }}',
                    redirectDelay: {{ $redirectDelay }},
                    showProgressBar: {{ $showProgressBar ? 'true' : 'false' }},
                    showErrorAlert: {{ $showErrorAlert ? 'true' : 'false' }},
                    showFieldErrors: {{ $showFieldErrors ? 'true' : 'false' }},
                    scrollOffset: {{ $scrollOffset }}
                });
            });
        </script>
    @endpush
@endif
