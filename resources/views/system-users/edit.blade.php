@extends('layouts.app')

@section('title', 'Edit System User')
@section('page-title', 'Edit System User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('system-users.index') }}">System Users</a></li>
    <li class="breadcrumb-item active">Edit User</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Edit System User</h5>
                            <p class="text-muted mb-0">Update user account information</p>
                        </div>
                        <a href="{{ route('system-users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading user data...</p>
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

                    <!-- User Edit Form -->
                    <form id="editUserForm" class="needs-validation d-none" novalidate>
                        @csrf
                        @method('PUT')

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
                                                <label for="password" class="form-label">New Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" minlength="6">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        id="togglePassword">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Leave blank to keep current password</div>
                                                <div class="invalid-feedback">Password must be at least 6 characters.</div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password">
                                            <div class="invalid-feedback">Passwords do not match.</div>
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
                                        <button type="button" class="btn btn-outline-warning me-2" id="resetBtn">
                                            <i class="fas fa-redo me-2"></i>Reset Changes
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>Update User
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
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('editUserForm');
            const userTypeSelect = document.getElementById('user_type');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const resetBtn = document.getElementById('resetBtn');
            let originalData = {};

            const userId = {{ $id ?? 'null' }};

            if (!userId) {
                alert('User ID not found');
                window.location.href = '{{ route("system-users.index") }}';
                return;
            }

            // Hide alerts initially
            document.getElementById('successMessage').classList.add('d-none');
            document.getElementById('errorMessage').classList.add('d-none');

            // Load user data and user types
            Promise.all([
                fetch(`/api/system-users/${userId}`).then(r => r.json()),
                fetch('/api/user-types/dropdown').then(r => r.json())
            ]).then(([userData, typesData]) => {
                document.getElementById('loadingSpinner').classList.add('d-none');
                form.classList.remove('d-none');

                if (userData.status === 'success') {
                    const user = userData.data;
                    originalData = {
                        fname: user.fname || '',
                        lname: user.lname || '',
                        nic: user.nic || '',
                        bday: user.bday || '',
                        gender: user.gender || '',
                        email: user.email || '',
                        mobile: user.mobile || '',
                        address1: user.address1 || '',
                        address2: user.address2 || '',
                        address3: user.address3 || '',
                        user_type: user.user?.user_type || ''
                    };

                    // Populate form with user data
                    Object.keys(originalData).forEach(key => {
                        const element = document.getElementById(key);
                        if (element) {
                            element.value = originalData[key];
                        }
                    });
                } else {
                    throw new Error(userData.message || 'Failed to load user data');
                }

                if (typesData.status === 'success' && typesData.data) {
                    let options = '<option value="">Select User Type</option>';
                    typesData.data.forEach(type => {
                        const selected = type.id == originalData.user_type ? 'selected' : '';
                        options += `<option value="${type.id}" ${selected}>${type.type}</option>`;
                    });
                    userTypeSelect.innerHTML = options;
                }
            }).catch(error => {
                document.getElementById('loadingSpinner').classList.add('d-none');
                document.getElementById('errorMessage').classList.remove('d-none');
                document.getElementById('errorText').textContent = error.message;
                console.error('Error:', error);
            });

            // Toggle password visibility
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });

            // Reset form to original data
            resetBtn.addEventListener('click', function () {
                Object.keys(originalData).forEach(key => {
                    const element = document.getElementById(key);
                    if (element) {
                        element.value = originalData[key];
                    }
                });
                document.getElementById('password').value = '';
                document.getElementById('confirm_password').value = '';
                form.classList.remove('was-validated');
                document.getElementById('successMessage').classList.add('d-none');
                document.getElementById('errorMessage').classList.add('d-none');
            });

            // Form validation and submission
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                // Check password match if password is provided
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (password && password !== confirmPassword) {
                    document.getElementById('confirm_password').setCustomValidity("Passwords don't match");
                    form.classList.add('was-validated');
                    return;
                } else {
                    document.getElementById('confirm_password').setCustomValidity('');
                }

                // Show loading spinner
                document.getElementById('loadingSpinner').classList.remove('d-none');
                document.getElementById('submitBtn').disabled = true;

                // Prepare form data
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);

                // Remove empty password fields if not changing password
                if (!data.password) {
                    delete data.password;
                    delete data.confirm_password;
                }

                // Send AJAX request to update user
                fetch(`/api/system-users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loadingSpinner').classList.add('d-none');
                        document.getElementById('submitBtn').disabled = false;

                        if (data.status === 'success') {
                            document.getElementById('successMessage').classList.remove('d-none');
                            document.getElementById('successText').textContent = data.message;

                            // Update original data
                            if (data.data) {
                                Object.keys(data.data).forEach(key => {
                                    if (originalData.hasOwnProperty(key)) {
                                        originalData[key] = data.data[key];
                                    }
                                });
                            }

                            // Clear password fields
                            document.getElementById('password').value = '';
                            document.getElementById('confirm_password').value = '';

                            // Navigate back to system users view after 2 seconds
                            setTimeout(() => {
                                window.location.href = '{{ route("system-users.index") }}';
                            }, 2000);

                        } else {
                            document.getElementById('errorMessage').classList.remove('d-none');
                            document.getElementById('errorText').textContent = data.message || 'An error occurred';
                        }
                    })
                    .catch(error => {
                        document.getElementById('loadingSpinner').classList.add('d-none');
                        document.getElementById('submitBtn').disabled = false;
                        document.getElementById('errorMessage').classList.remove('d-none');
                        document.getElementById('errorText').textContent = 'Network error occurred. Please try again.';
                        console.error('Error:', error);
                    });
            });

            // Real-time password confirmation validation
            document.getElementById('confirm_password').addEventListener('input', function () {
                const password = document.getElementById('password').value;
                const confirmPassword = this.value;

                if (password && confirmPassword && password !== confirmPassword) {
                    this.setCustomValidity("Passwords don't match");
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
@endpush