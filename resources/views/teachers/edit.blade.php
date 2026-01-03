@extends('layouts.app')

@section('title', 'Edit Teacher')
@section('page-title', 'Edit Teacher')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Edit Teacher</h5>
                            <p class="text-muted mb-0">Update teacher information</p>
                        </div>
                        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Teachers
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading teacher data...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger d-none" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="errorText"></span>
                    </div>

                    <!-- Success Message -->
                    <div id="successMessage" class="alert alert-success d-none" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="successText"></span>
                    </div>

                    <form id="editTeacherForm" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="fname" class="form-label">First Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="fname" name="fname"
                                                        required>
                                                    <div class="invalid-feedback">Please provide first name.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="lname" class="form-label">Last Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="lname" name="lname"
                                                        required>
                                                    <div class="invalid-feedback">Please provide last name.</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                            <div class="invalid-feedback">Please provide a valid email.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="mobile" class="form-label">Mobile Number <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="mobile" name="mobile" required>
                                            <div class="invalid-feedback">Please provide mobile number.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="nic" class="form-label">NIC Number</label>
                                            <input type="text" class="form-control" id="nic" name="nic">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="bday" class="form-label">Birth Date</label>
                                                    <input type="date" class="form-control" id="bday" name="bday">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="gender" class="form-label">Gender</label>
                                                    <select class="form-control" id="gender" name="gender">
                                                        <option value="">Select Gender</option>
                                                        <option value="male">Male</option>
                                                        <option value="female">Female</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_active"
                                                    name="is_active" value="1">
                                                <label class="form-check-label" for="is_active">Active Teacher</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address & Professional Information -->
                            <div class="col-md-6">
                                <!-- Address Information -->
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-home me-2"></i>Address Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="address1" class="form-label">Address Line 1</label>
                                            <input type="text" class="form-control" id="address1" name="address1"
                                                placeholder="Street address, P.O. Box">
                                        </div>
                                        <div class="mb-3">
                                            <label for="address2" class="form-label">Address Line 2</label>
                                            <input type="text" class="form-control" id="address2" name="address2"
                                                placeholder="Apartment, suite, unit, building, floor">
                                        </div>
                                        <div class="mb-3">
                                            <label for="address3" class="form-label">Address Line 3</label>
                                            <input type="text" class="form-control" id="address3" name="address3"
                                                placeholder="City, state, ZIP code">
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Information -->
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Professional Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="graduation_details" class="form-label">Graduation Details</label>
                                            <textarea class="form-control" id="graduation_details" name="graduation_details"
                                                rows="3" placeholder="Degree, university, year..."></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                              <label for="experience" class="form-label">Experience</label>
                                                <select class="form-select" id="experience" name="experience">
                                                    <option value="">Select Experience</option>
                                                    <option value="Less than a year">Less than a year</option>
                                                    <option value="One year">One year</option>
                                                    <option value="Two years" >Two years</option>
                                                    <option value="Less than two years">Less than two years</option>
                                                    <option value="Three years">Three years</option>
                                                    <option value="Less than three years">Less than three years</option>
                                                    <option value="Four years">Four years</option>
                                                    <option value="Less than four years">Less than four years</option>
                                                    <option value="Five years">Five years</option>
                                                    <option value="More than five years">More than five years</option>
                                                </select>
                                            </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="precentage" class="form-label">Percentage (%)</label>
                                                    <input type="number" class="form-control" id="precentage"
                                                        name="precentage" min="0" max="100" step="0.1" placeholder="0.0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="mb-0"><i class="fas fa-university me-2"></i>Bank Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="bank_id" class="form-label">Bank</label>
                                                    <select class="form-control" id="bank_id" name="bank_id">
                                                        <option value="">Select Bank</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="bank_branch_id" class="form-label">Bank Branch</label>
                                                    <select class="form-control" id="bank_branch_id" name="bank_branch_id">
                                                        <option value="">Select Bank First</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="account_number" class="form-label">Account Number</label>
                                                    <input type="text" class="form-control" id="account_number"
                                                        name="account_number" placeholder="Account number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Update Teacher
                                    </button>
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

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .invalid-feedback {
            display: none;
            font-size: 0.875em;
        }

        .was-validated .form-control:invalid,
        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4.4-.4'/%3e%3cpath d='M6 7v2'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .was-validated .form-control:valid,
        .form-control.is-valid {
            border-color: #198754;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Global variable to store teacher data
        let currentTeacherId = {{ $id }};

        // Wait for the DOM to be loaded
        document.addEventListener('DOMContentLoaded', function () {
            initializeEditTeacherPage();
        });

        function initializeEditTeacherPage() {
            // Load teacher data and banks
            loadTeacherData();
            loadBanks();

            // Bank change event
            document.getElementById('bank_id').addEventListener('change', function () {
                const bankId = this.value;
                if (bankId) {
                    loadBankBranches(bankId);
                } else {
                    document.getElementById('bank_branch_id').innerHTML = '<option value="">Select Bank First</option>';
                }
            });

            // Form submission
            document.getElementById('editTeacherForm').addEventListener('submit', function (e) {
                e.preventDefault();
                updateTeacher();
            });

            // Real-time validation
            setupRealTimeValidation();
        }

        function loadTeacherData() {
            showLoading();

            fetch(`/api/teachers/${currentTeacherId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        populateForm(data.data);
                    } else {
                        throw new Error(data.message || 'Failed to load teacher data');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showError('Error loading teacher data: ' + error.message);
                });
        }

        function populateForm(teacher) {
            console.log('Teacher data:', teacher); // Debug log

            // Personal Information
            document.getElementById('fname').value = teacher.fname || '';
            document.getElementById('lname').value = teacher.lname || '';
            document.getElementById('email').value = teacher.email || '';
            document.getElementById('mobile').value = teacher.mobile || '';
            document.getElementById('nic').value = teacher.nic || '';

            // Format date from MM/DD/YYYY to YYYY-MM-DD
            if (teacher.bday) {
                const dateParts = teacher.bday.split('/');
                if (dateParts.length === 3) {
                    // Assuming format is MM/DD/YYYY
                    const formattedDate = `${dateParts[2]}-${dateParts[0].padStart(2, '0')}-${dateParts[1].padStart(2, '0')}`;
                    document.getElementById('bday').value = formattedDate;
                } else {
                    document.getElementById('bday').value = teacher.bday;
                }
            } else {
                document.getElementById('bday').value = '';
            }

            // Gender - make sure it's lowercase to match select options
            document.getElementById('gender').value = teacher.gender ? teacher.gender.toLowerCase() : '';

            // Active status - fix for boolean value
            document.getElementById('is_active').checked = teacher.is_active === true || teacher.is_active === '1' || teacher.is_active === 1;

            // Address Information
            document.getElementById('address1').value = teacher.address1 || '';
            document.getElementById('address2').value = teacher.address2 || '';
            document.getElementById('address3').value = teacher.address3 || '';

            // Professional Information
            document.getElementById('graduation_details').value = teacher.graduation_details || '';

            // Experience - handle different data types
            setExperienceValue(teacher.experience);

            // Percentage
            document.getElementById('precentage').value = teacher.precentage || '';

            // Bank Information
            document.getElementById('account_number').value = teacher.account_number || '';

            // If teacher has bank branch, load the corresponding bank and branches
            if (teacher.bank_branch_id && teacher.bank_branch) {
                console.log('Bank branch found:', teacher.bank_branch); // Debug log
                // Load banks first, then set bank and branch
                loadBanks().then(() => {
                    setTimeout(() => {
                        setBankAndBranch(teacher.bank_branch.bank_id, teacher.bank_branch_id);
                    }, 500);
                });
            }
        }

        // ================= EXPERIENCE VALUE SETTER =================
        function setExperienceValue(experienceValue) {
            const experienceSelect = document.getElementById('experience');
            
            if (!experienceValue && experienceValue !== 0) {
                experienceSelect.value = '';
                return;
            }
            
            const strValue = String(experienceValue).trim().toLowerCase();
            console.log('Setting experience value:', strValue);
            
            // Direct mapping for common cases
            const mapping = {
                // Text values from database
                'less than a year': 'Less than a year',
                'less than 1 year': 'Less than a year',
                '<1 year': 'Less than a year',
                '1 year': 'One year',
                'one year': 'One year',
                '2 years': 'Two years',
                'two years': 'Two years',
                'less than 2 years': 'Less than two years',
                '<2 years': 'Less than two years',
                '3 years': 'Three years',
                'three years': 'Three years',
                'less than 3 years': 'Less than three years',
                '<3 years': 'Less than three years',
                '4 years': 'Four years',
                'four years': 'Four years',
                'less than 4 years': 'Less than four years',
                '<4 years': 'Less than four years',
                '5 years': 'Five years',
                'five years': 'Five years',
                'more than 5 years': 'More than five years',
                '5+': 'More than five years',
                '>5': 'More than five years',
                
                // Numeric values
                '0': 'Less than a year',
                '1': 'One year',
                '2': 'Two years',
                '3': 'Three years',
                '4': 'Four years',
                '5': 'Five years'
            };
            
            // Try lowercase match first
            if (mapping[strValue]) {
                experienceSelect.value = mapping[strValue];
                console.log('Direct mapping:', strValue, '->', mapping[strValue]);
                return;
            }
            
            // If value is a number > 5
            const num = parseInt(strValue);
            if (!isNaN(num) && num > 5) {
                experienceSelect.value = 'More than five years';
                console.log('Number >5 mapped:', num, '-> More than five years');
                return;
            }
            
            // Check dropdown options directly (case insensitive)
            for (let option of experienceSelect.options) {
                if (option.value.toLowerCase() === strValue) {
                    option.selected = true;
                    console.log('Exact dropdown match:', strValue);
                    return;
                }
            }
            
            // Try partial match
            for (let option of experienceSelect.options) {
                if (strValue.includes(option.value.toLowerCase()) || 
                    option.value.toLowerCase().includes(strValue)) {
                    option.selected = true;
                    console.log('Partial match:', strValue, '->', option.value);
                    return;
                }
            }
            
            console.log('Experience not matched:', experienceValue);
            experienceSelect.value = ''; // Default to empty
        }

        function setBankAndBranch(bankId, branchId) {
            const bankSelect = document.getElementById('bank_id');
            const branchSelect = document.getElementById('bank_branch_id');

            console.log('Setting bank:', bankId, 'branch:', branchId); // Debug log

            // Set bank if it exists in the select
            if (bankId && Array.from(bankSelect.options).some(option => option.value == bankId)) {
                bankSelect.value = bankId;

                // Load branches for this bank and then set the branch
                if (bankId) {
                    loadBankBranches(bankId).then(() => {
                        setTimeout(() => {
                            // Set branch after branches are loaded
                            if (branchId && Array.from(branchSelect.options).some(option => option.value == branchId)) {
                                branchSelect.value = branchId;
                                console.log('Branch set to:', branchId); // Debug log
                            } else {
                                console.log('Branch ID not found in options:', branchId); // Debug log
                            }
                        }, 300);
                    }).catch(error => {
                        console.error('Error loading branches:', error);
                    });
                }
            } else {
                console.log('Bank ID not found in options:', bankId); // Debug log
            }
        }

        function setupRealTimeValidation() {
            const inputs = document.querySelectorAll('#editTeacherForm input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function () {
                    validateField(this);
                });
            });
        }

        function validateField(field) {
            if (field.value.trim() === '') {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        }

        function loadBanks() {
            return new Promise((resolve, reject) => {
                fetch('/api/banks/dropdown')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const bankSelect = document.getElementById('bank_id');
                            bankSelect.innerHTML = '<option value="">Select Bank</option>';

                            data.data.forEach(bank => {
                                const option = document.createElement('option');
                                option.value = bank.id;
                                option.textContent = `${bank.bank_name} (${bank.bank_code})`;
                                bankSelect.appendChild(option);
                            });
                            resolve();
                        } else {
                            console.error('Failed to load banks:', data.message);
                            reject(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading banks:', error);
                        reject(error);
                    });
            });
        }

        function loadBankBranches(bankId) {
            return new Promise((resolve, reject) => {
                const branchSelect = document.getElementById('bank_branch_id');
                branchSelect.innerHTML = '<option value="">Loading branches...</option>';
                branchSelect.disabled = true;

                fetch(`/api/bank-branches/${bankId}/dropdown`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            branchSelect.innerHTML = '<option value="">Select Bank Branch</option>';

                            data.data.forEach(branch => {
                                const option = document.createElement('option');
                                option.value = branch.id;
                                option.textContent = `${branch.branch_name} (${branch.branch_code})`;
                                branchSelect.appendChild(option);
                            });

                            branchSelect.disabled = false;
                            resolve();
                        } else {
                            branchSelect.innerHTML = '<option value="">Failed to load branches</option>';
                            reject(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading bank branches:', error);
                        branchSelect.innerHTML = '<option value="">Error loading branches</option>';
                        reject(error);
                    });
            });
        }

        // ================= UPDATED UPDATE TEACHER FUNCTION =================
        function updateTeacher() {
            const form = document.getElementById('editTeacherForm');
            const submitBtn = document.getElementById('submitBtn');

            // Validate form
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                showError('Please fill in all required fields correctly.');
                return;
            }

            // Validate percentage
            const percentage = document.getElementById('precentage').value;
            if (percentage < 0 || percentage > 100) {
                showError('Percentage must be between 0 and 100.');
                document.getElementById('precentage').focus();
                return;
            }

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            // Create form data object
            const formData = {};
            
            // Add all form fields
            const formElements = form.elements;
            for (let element of formElements) {
                if (element.name && element.type !== 'button' && element.type !== 'submit') {
                    if (element.type === 'checkbox') {
                        formData[element.name] = element.checked ? '1' : '0';
                    } else if (element.type === 'number') {
                        formData[element.name] = element.value || '0';
                    } else if (element.type === 'select-one') {
                        formData[element.name] = element.value || '';
                    } else {
                        formData[element.name] = element.value || '';
                    }
                }
            }

            // Add _method for Laravel to recognize as PUT
            formData['_method'] = 'PUT';

            // Log the data being sent
            console.log('📤 Sending update data:', formData);

            fetch(`/api/teachers/${currentTeacherId}`, {
                method: 'POST', // Laravel needs POST for PUT with _method
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: new URLSearchParams(formData) // Use URLSearchParams instead of FormData
            })
                .then(response => {
                    // Clone response for debugging
                    const responseClone = response.clone();
                    
                    if (!response.ok) {
                        // Try to get detailed error info
                        return response.json().then(errorData => {
                            console.error('❌ Server error response:', errorData);
                            
                            // Handle validation errors (422)
                            if (response.status === 422 && errorData.errors) {
                                displayValidationErrors(errorData.errors);
                                throw new Error('Validation failed');
                            }
                            
                            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                        }).catch(jsonError => {
                            // If can't parse JSON
                            throw new Error(`Server error: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Server response:', data);
                    
                    if (data.status === 'success') {
                        showSuccess('Teacher updated successfully!');

                        // Redirect to teachers list after 2 seconds
                        setTimeout(() => {
                            window.location.href = '/teachers';
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Failed to update teacher');
                    }
                })
                .catch(error => {
                    console.error('❌ Error updating teacher:', error);
                    
                    // Don't show error if it's validation (already handled)
                    if (!error.message.includes('Validation failed')) {
                        showError('Error updating teacher: ' + error.message);
                    }
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Teacher';
                });
        }

        // ================= VALIDATION ERROR DISPLAY =================
        function displayValidationErrors(errors) {
            console.log('Validation errors:', errors);
            
            // Clear previous error highlights
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            // Clear previous error messages
            document.querySelectorAll('.field-error-message').forEach(el => {
                el.remove();
            });
            
            let errorMessages = [];
            
            // Process each field error
            Object.entries(errors).forEach(([field, messages]) => {
                const input = document.querySelector(`[name="${field}"]`);
                const formGroup = input ? input.closest('.mb-3') : null;
                
                if (input && formGroup) {
                    // Add Bootstrap invalid class
                    input.classList.add('is-invalid');
                    
                    // Create error message element
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error-message text-danger mt-1';
                    errorDiv.style.fontSize = '0.875em';
                    errorDiv.innerHTML = messages.join('<br>');
                    
                    // Insert after input
                    formGroup.appendChild(errorDiv);
                    
                    // Add to error messages list
                    errorMessages.push(...messages);
                } else {
                    // Field not found in form, add to general errors
                    errorMessages.push(...messages);
                }
            });
            
            // Show general error message
            if (errorMessages.length > 0) {
                const uniqueMessages = [...new Set(errorMessages)];
                showError('Please fix the following errors:<br>' + uniqueMessages.join('<br>'));
            }
            
            // Scroll to first error
            const firstInvalid = document.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }

        // Helper functions
        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('d-none');
            document.getElementById('editTeacherForm').classList.add('d-none');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('d-none');
            document.getElementById('editTeacherForm').classList.remove('d-none');
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            document.getElementById('errorText').innerHTML = message; // Use innerHTML for <br> tags
            errorDiv.classList.remove('d-none');

            // Scroll to error message
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorDiv.classList.add('d-none');
            }, 5000);
        }

        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            document.getElementById('successText').textContent = message;
            successDiv.classList.remove('d-none');

            // Scroll to success message
            successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Auto-hide after 3 seconds
            setTimeout(() => {
                successDiv.classList.add('d-none');
            }, 3000);
        }
    </script>
@endpush