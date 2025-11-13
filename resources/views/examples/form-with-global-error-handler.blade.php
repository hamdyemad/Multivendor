{{-- Example: How to use the Global Form Error Handler --}}

@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Example Form with Global Error Handler</h4>
                </div>
                <div class="card-body">
                    {{-- Method 1: Using data attributes (Auto-initialization) --}}
                    <form id="exampleForm" 
                          method="POST" 
                          action="/example/store" 
                          data-auto-error-handler
                          data-loading-text="Creating example..."
                          data-redirect-url="/examples">
                        @csrf
                        
                        {{-- Using the form-field component --}}
                        <x-form-field 
                            name="title" 
                            label="Title" 
                            :required="true" 
                            placeholder="Enter title" 
                        />
                        
                        <x-form-field 
                            name="description" 
                            label="Description" 
                            type="textarea" 
                            rows="4" 
                            placeholder="Enter description" 
                        />
                        
                        <x-form-field 
                            name="category_id" 
                            label="Category" 
                            type="select" 
                            :required="true"
                            :options="[
                                '' => 'Select Category',
                                '1' => 'Category 1',
                                '2' => 'Category 2'
                            ]" 
                        />
                        
                        {{-- Or manually add error containers --}}
                        <div class="form-group">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email">
                            <div class="error-message text-danger" id="error-email" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="uil uil-check"></i> Create Example
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include the global form error handler --}}
<x-form-error-handler />

{{-- Method 2: Manual initialization (if you need custom options) --}}
@push('scripts')
<script>
$(document).ready(function() {
    // Manual initialization with custom options
    window.customFormHandler = new FormErrorHandler({
        formSelector: '#exampleForm',
        loadingText: 'Processing your request...',
        successText: 'Example created successfully!',
        redirectUrl: '/examples',
        redirectDelay: 2000,
        showProgressBar: true,
        showErrorAlert: true,
        showFieldErrors: true,
        scrollOffset: 50
    });
});
</script>
@endpush
@endsection
