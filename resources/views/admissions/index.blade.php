@extends('layouts.app')

@section('title', 'Admissions')
@section('page-title', 'Admissions Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Admissions</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Admissions List</h6>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAdmissionModal">
                <i class="fas fa-plus me-1"></i> Add New Admission
            </button>
        </div>
        <div class="card-body">
            <!-- Loading State -->
            <div id="loading-spinner" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading admissions...</p>
            </div>

            <!-- Empty State -->
            <div id="no-admissions-message" class="text-center py-4 d-none">
                <div class="empty-state">
                    <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Admissions Found</h4>
                    <p class="text-muted">Get started by adding your first admission record.</p>
                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addAdmissionModal">
                        <i class="fas fa-plus me-1"></i> Add Admission
                    </button>
                </div>
            </div>

            <!-- Admissions Table -->
            <div id="admissions-table" class="d-none">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="admissionsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="admissions-table-body">
                            <!-- Admissions data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Admission Modal -->
    <div class="modal fade" id="addAdmissionModal" tabindex="-1" aria-labelledby="addAdmissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdmissionModalLabel">Add New Admission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addAdmissionForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Admission Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0"
                                required>
                            <div class="invalid-feedback" id="amount-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="save-admission-btn">
                            <i class="fas fa-save me-1"></i> Save Admission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Admission Modal -->
    <div class="modal fade" id="editAdmissionModal" tabindex="-1" aria-labelledby="editAdmissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAdmissionModalLabel">Edit Admission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAdmissionForm">
                    <input type="hidden" id="edit_admission_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Admission Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                            <div class="invalid-feedback" id="edit-name-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" min="0"
                                required>
                            <div class="invalid-feedback" id="edit-amount-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="update-admission-btn">
                            <i class="fas fa-sync me-1"></i> Update Admission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
        }

        .action-buttons .btn {
            margin: 0 2px;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .empty-state {
            padding: 3rem 1rem;
        }

        .empty-state i {
            opacity: 0.5;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadAdmissions();

            // Add Admission Form Submission
            document.getElementById('addAdmissionForm').addEventListener('submit', function (e) {
                e.preventDefault();
                saveAdmission();
            });

            // Edit Admission Form Submission
            document.getElementById('editAdmissionForm').addEventListener('submit', function (e) {
                e.preventDefault();
                updateAdmission();
            });

            // Reset form when modal is closed
            document.getElementById('addAdmissionModal').addEventListener('hidden.bs.modal', function () {
                resetAddForm();
            });
        });

        function loadAdmissions() {
            const loadingSpinner = document.getElementById('loading-spinner');
            const noAdmissionsMessage = document.getElementById('no-admissions-message');
            const admissionsTable = document.getElementById('admissions-table');
            const admissionsTableBody = document.getElementById('admissions-table-body');

            // Show loading
            loadingSpinner.classList.remove('d-none');
            noAdmissionsMessage.classList.add('d-none');
            admissionsTable.classList.add('d-none');
            admissionsTableBody.innerHTML = '';

            fetch('/api/admissions')
                .then(async response => {
                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`HTTP ${response.status}: ${errorText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    loadingSpinner.classList.add('d-none');

                    if (!data.status || !data.data || data.data.length === 0) {
                        noAdmissionsMessage.classList.remove('d-none');
                        return;
                    }

                    renderAdmissionsTable(data.data);
                    admissionsTable.classList.remove('d-none');
                })
                .catch(error => {
                    console.error('Error loading admissions:', error);
                    loadingSpinner.classList.add('d-none');

                    if (error.message.includes('HTTP 404')) {
                        showError('API endpoint not found. Please check if the route is defined.');
                    } else if (error.message.includes('HTTP 500')) {
                        showError('Server error. Please check your server logs.');
                    } else if (error.message.includes('HTTP 401') || error.message.includes('HTTP 403')) {
                        showError('Authentication required. Please log in.');
                    } else {
                        showError('Failed to load admissions: ' + error.message);
                    }
                });
        }

        function renderAdmissionsTable(admissions) {
            const admissionsTableBody = document.getElementById('admissions-table-body');

            admissions.forEach(admission => {
                const row = document.createElement('tr');
                row.innerHTML = `
                                    <td>${admission.id}</td>
                                    <td>${admission.name}</td>
                                    <td>${formatAmount(admission.amount)}</td>
                                    <td>${formatDate(admission.created_at)}</td>
                                    <td>${formatDate(admission.updated_at)}</td>
                                    <td>
                                        <div class="action-buttons d-flex justify-content-center">
                                            <button class="btn btn-warning btn-sm" onclick="editAdmission(${admission.id})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                `;
                admissionsTableBody.appendChild(row);
            });
        }

        function saveAdmission() {
            const form = document.getElementById('addAdmissionForm');
            const formData = new FormData(form);
            const saveButton = document.getElementById('save-admission-btn');
            const originalButtonText = saveButton.innerHTML;

            // Reset validation
            resetValidation('addAdmissionForm');

            // Show loading state
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

            fetch('/api/admissions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    amount: formData.get('amount')
                })
            })
                .then(async response => {
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error(`Server returned HTML instead of JSON. Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status) {
                        // Success
                        showSuccess(data.message || 'Admission created successfully!');
                        document.getElementById('addAdmissionModal').querySelector('.btn-close').click();
                        loadAdmissions(); // Refresh the table
                    } else {
                        // Handle validation errors
                        if (data.message && data.message.includes('already exists')) {
                            showValidationError('name', data.message);
                        } else {
                            showError(data.message || 'Failed to create admission');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error saving admission:', error);
                    if (error.message.includes('HTML instead of JSON')) {
                        showError('Server error: Please check if the API endpoint is correct and running.');
                    } else {
                        showError('Failed to create admission. Please try again.');
                    }
                })
                .finally(() => {
                    // Restore button state
                    saveButton.disabled = false;
                    saveButton.innerHTML = originalButtonText;
                });
        }
        function editAdmission(id) {
            fetch(`/api/admissions/${id}`)
                .then(async response => {
                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`HTTP ${response.status}: ${errorText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status) {
                        const admission = data.data;
                        document.getElementById('edit_admission_id').value = admission.id;
                        document.getElementById('edit_name').value = admission.name;
                        document.getElementById('edit_amount').value = admission.amount;

                        // Reset validation
                        resetValidation('editAdmissionForm');

                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('editAdmissionModal'));
                        modal.show();
                    } else {
                        showError('Failed to load admission data');
                    }
                })
                .catch(error => {
                    console.error('Error loading admission:', error);
                    if (error.message.includes('HTTP 404')) {
                        showError('Admission not found or API endpoint unavailable.');
                    } else {
                        showError('Failed to load admission data: ' + error.message);
                    }
                });
        }

        function updateAdmission() {
            const form = document.getElementById('editAdmissionForm');
            const formData = new FormData(form);
            const admissionId = formData.get('id');
            const updateButton = document.getElementById('update-admission-btn');
            const originalButtonText = updateButton.innerHTML;

            // Reset validation
            resetValidation('editAdmissionForm');

            // Show loading state
            updateButton.disabled = true;
            updateButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';

            fetch(`/api/admissions/${admissionId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    amount: formData.get('amount')
                })
            })
                .then(async response => {
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error(`Server returned HTML instead of JSON. Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status) {
                        // Success
                        showSuccess(data.message || 'Admission updated successfully!');
                        document.getElementById('editAdmissionModal').querySelector('.btn-close').click();
                        loadAdmissions(); // Refresh the table
                    } else {
                        // Handle validation errors
                        if (data.message && data.message.includes('already exists')) {
                            showValidationError('edit_name', data.message);
                        } else {
                            showError(data.message || 'Failed to update admission');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating admission:', error);
                    if (error.message.includes('HTML instead of JSON')) {
                        showError('Server error: Please check if the API endpoint is correct and running.');
                    } else {
                        showError('Failed to update admission. Please try again.');
                    }
                })
                .finally(() => {
                    // Restore button state
                    updateButton.disabled = false;
                    updateButton.innerHTML = originalButtonText;
                });
        }

        // Utility Functions
        function formatAmount(amount) {
            return 'Rs. ' + parseFloat(amount).toFixed(2);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function resetAddForm() {
            document.getElementById('addAdmissionForm').reset();
            resetValidation('addAdmissionForm');
        }

        function resetValidation(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('.is-invalid');
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
        }

        function showValidationError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '-error');
            field.classList.add('is-invalid');
            errorElement.textContent = message;
        }

        function showSuccess(message) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert.position-fixed');
            existingAlerts.forEach(alert => alert.remove());

            // Create success alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">Success!</h6>
                                        <p class="mb-0 small">${message}</p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;

            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) alertDiv.remove();
            }, 5000);
        }

        function showError(message) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert.position-fixed');
            existingAlerts.forEach(alert => alert.remove());

            // Create error alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x me-3 text-danger"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">Error!</h6>
                                        <p class="mb-0 small">${message}</p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;

            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) alertDiv.remove();
            }, 5000);
        }
    </script>
@endpush