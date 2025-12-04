@extends('layouts.app')

@section('title', 'Add System User')
@section('page-title', 'Add System User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('system-users.index') }}">System Users</a></li>
    <li class="breadcrumb-item active">Add New User</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Add New System User</h5>
                            <p class="text-muted mb-0">Create a new system user account with appropriate permissions</p>
                        </div>
                        <a href="{{ route('system-users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Creating user account...</p>
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

                    <!-- User Creation Form -->
                    <form id="createUserForm" class="needs-validation" novalidate>
                        @csrf

                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user me-2"></i>Personal Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="fname" class="form-label">First Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="fname" name="fname" required>
                                                <div class="invalid-feedback">Please provide first name.</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="lname" class="form-label">Last Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="lname" name="lname" required>
                                                <div class="invalid-feedback">Please provide last name.</div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="nic" class="form-label">NIC Number</label>
                                            <input type="text" class="form-control" id="nic" name="nic"
                                                placeholder="Enter NIC number">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="bday" class="form-label">Birthday</label>
                                                <input type="date" class="form-control" id="bday" name="bday">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="gender" class="form-label">Gender</label>
                                                <select class="form-select" id="gender" name="gender">
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-address-book me-2"></i>Contact Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                            <div class="invalid-feedback">Please provide a valid email address.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="mobile" class="form-label">Mobile Number <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" id="mobile" name="mobile" required>
                                            <div class="invalid-feedback">Please provide mobile number.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="address1" class="form-label">Address Line 1</label>
                                            <input type="text" class="form-control" id="address1" name="address1"
                                                placeholder="Street address">
                                        </div>

                                        <div class="mb-3">
                                            <label for="address2" class="form-label">Address Line 2</label>
                                            <input type="text" class="form-control" id="address2" name="address2"
                                                placeholder="Apartment, suite, etc.">
                                        </div>

                                        <div class="mb-3">
                                            <label for="address3" class="form-label">City</label>
                                            <input type="text" class="form-control" id="address3" name="address3"
                                                placeholder="City">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-key me-2"></i>Account Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="user_type" class="form-label">User Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="user_type" name="user_type" required>
                                                    <option value="">Loading user types...</option>
                                                </select>
                                                <div class="invalid-feedback">Please select user type.</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="password" class="form-label">Password <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" required minlength="6">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        id="togglePassword">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Password must be at least 6 characters long.</div>
                                                <div class="invalid-feedback">Please provide a password (min 6 characters).
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password" required>
                                            <div class="invalid-feedback">Passwords do not match.</div>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="send_welcome_email"
                                                name="send_welcome_email">
                                            <label class="form-check-label" for="send_welcome_email">
                                                Send welcome email with login credentials
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('system-users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-redo me-2"></i>Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-user-plus me-2"></i>Create User
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

@push('styles')
    <style>
        .custom-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header.bg-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            border-bottom: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .was-validated .form-control:invalid~.invalid-feedback,
        .was-validated .form-select:invalid~.invalid-feedback {
            display: block;
        }
    </style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');
    const userTypeSelect = document.getElementById('user_type');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    // Hide alerts initially
    document.getElementById('successMessage').classList.add('d-none');
    document.getElementById('errorMessage').classList.add('d-none');

    // Load user types with error handling
    function loadUserTypes() {
        fetch('/api/user-types/dropdown')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success' && data.data) {
                    let options = '<option value="">Select User Type</option>';
                    data.data.forEach(type => {
                        options += `<option value="${type.id}">${type.type}</option>`;
                    });
                    userTypeSelect.innerHTML = options;
                } else {
                    userTypeSelect.innerHTML = '<option value="">No user types available</option>';
                }
            })
            .catch(error => {
                console.warn('Failed to load user types:', error);
                userTypeSelect.innerHTML = '<option value="">Error loading user types</option>';
            });
    }

    loadUserTypes();

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Form validation and submission
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        // Check password match
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            document.getElementById('confirm_password').setCustomValidity("Passwords don't match");
            form.classList.add('was-validated');
            return;
        } else {
            document.getElementById('confirm_password').setCustomValidity('');
        }

        // Show loading spinner
        document.getElementById('loadingSpinner').classList.remove('d-none');
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('successMessage').classList.add('d-none');
        document.getElementById('errorMessage').classList.add('d-none');

        try {
            // Prepare form data
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Remove empty fields
            Object.keys(data).forEach(key => {
                if (data[key] === '') {
                    delete data[key];
                }
            });

            console.log('Submitting data:', data); // Debug log

            // Send AJAX request to create user - CORRECTED URL
            const response = await fetch('/api/system-users', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            document.getElementById('loadingSpinner').classList.add('d-none');
            document.getElementById('submitBtn').disabled = false;

            if (result.status === 'success') {
                document.getElementById('successMessage').classList.remove('d-none');
                document.getElementById('successText').textContent = result.message;
                
                // Reset form after success
                setTimeout(() => {
                    form.reset();
                    form.classList.remove('was-validated');
                    document.getElementById('successMessage').classList.add('d-none');
                    
                    // Redirect to users list after 2 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route("system-users.index") }}';
                    }, 2000);
                }, 3000);
            } else {
                document.getElementById('errorMessage').classList.remove('d-none');
                document.getElementById('errorText').textContent = result.message || 'An error occurred while creating user';
                console.error('API Error:', result);
            }
        } catch (error) {
            document.getElementById('loadingSpinner').classList.add('d-none');
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('errorMessage').classList.remove('d-none');
            document.getElementById('errorText').textContent = 'Network error occurred. Please try again.';
            console.error('Network Error:', error);
        }
    });

    // Real-time password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.setCustomValidity("Passwords don't match");
        } else {
            this.setCustomValidity('');
        }
    });

    // Reset form validation on reset
    form.addEventListener('reset', function() {
        form.classList.remove('was-validated');
        document.getElementById('successMessage').classList.add('d-none');
        document.getElementById('errorMessage').classList.add('d-none');
        
        // Clear custom validity
        document.getElementById('confirm_password').setCustomValidity('');
    });
});
</script>
@endpush