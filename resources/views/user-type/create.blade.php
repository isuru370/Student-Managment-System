@extends('layouts.app')

@section('title', 'Add User Type')
@section('page-title', 'Add User Type')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('user-types.index') }}">User Types</a></li>
    <li class="breadcrumb-item active">Add New Type</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Add New User Type</h5>
                        <p class="text-muted mb-0">Create a new user type for system users</p>
                    </div>
                    <a href="{{ route('user-types.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Types
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Creating user type...</p>
                </div>

                <!-- Success Message -->
                <div id="successMessage" class="alert alert-success d-none" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="successText"></span>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="alert alert-danger d-none" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="errorText"></span>
                </div>

                <!-- User Type Creation Form -->
                <form id="createTypeForm" class="needs-validation" novalidate>
                    @csrf
                    
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Type Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="type" name="type" 
                                               placeholder="Enter user type name (e.g., Administrator, Manager, Teacher)" 
                                               required maxlength="255">
                                        <div class="invalid-feedback">Please provide a user type name.</div>
                                        <div class="form-text">This will be used to categorize system users.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('user-types.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Create Type
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createTypeForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    // Bootstrap validation
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        submitForm();
    });

    function submitForm() {
        showLoading();
        hideMessages();

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch('/api/user-types', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            const responseData = await response.json();
            
            if (!response.ok) {
                throw new Error(responseData.message || 'Failed to create user type');
            }
            
            return responseData;
        })
        .then(data => {
            showSuccess('User type created successfully! Redirecting...');
            form.reset();
            form.classList.remove('was-validated');
            
            // Redirect to types list after 2 seconds
            setTimeout(() => {
                window.location.href = '{{ route("user-types.index") }}';
            }, 2000);
        })
        .catch(error => {
            console.error('Error creating user type:', error);
            showError(error.message || 'Failed to create user type. Please try again.');
        })
        .finally(() => {
            hideLoading();
        });
    }

    function showLoading() {
        loadingSpinner.classList.remove('d-none');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
    }

    function hideLoading() {
        loadingSpinner.classList.add('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Create Type';
    }

    function showSuccess(message) {
        successMessage.querySelector('#successText').textContent = message;
        successMessage.classList.remove('d-none');
        errorMessage.classList.add('d-none');
    }

    function showError(message) {
        errorMessage.querySelector('#errorText').textContent = message;
        errorMessage.classList.remove('d-none');
        successMessage.classList.add('d-none');
    }

    function hideMessages() {
        successMessage.classList.add('d-none');
        errorMessage.classList.add('d-none');
    }
});
</script>
@endpush