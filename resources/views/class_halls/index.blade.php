@extends('layouts.app')

@section('title', 'Class Halls')
@section('page-title', 'Class Halls Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('class_rooms.index') }}">Class Room</a></li>
    <li class="breadcrumb-item active">Class Halls</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>Class Halls List
                        </h3>
                    </div>
                    <div class="card-header btn-light text-white">
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addHallModal">
                            <i class="fas fa-plus me-2"></i>Add New Hall
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading halls data...</p>
                        </div>
                        <!-- Halls Table -->
                        <div class="table-responsive" id="hallsTableSection" style="display: none;">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Hall ID</th>
                                        <th>Hall Name</th>
                                        <th>Hall Type</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="hallsTableBody">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <!-- No Data Message -->
                        <div id="noHallsMessage" class="text-center py-5" style="display: none;">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Halls Found</h4>
                            <p class="text-muted">No class halls available. Add your first hall to get started.</p>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addHallModal">
                                <i class="fas fa-plus me-2"></i>Add First Hall
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Hall Modal -->
    <div class="modal fade" id="addHallModal" tabindex="-1" aria-labelledby="addHallModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addHallModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Add New Hall
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="addHallForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="hall_id" class="form-label">Hall ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="hall_id" name="hall_id" required
                                placeholder="Enter unique hall ID (e.g., H01, HALL-001)">
                            <div class="form-text">Unique identifier for the hall</div>
                            <span class="text-danger error-text hall_id_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="hall_name" class="form-label">Hall Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="hall_name" name="hall_name" required
                                placeholder="Enter hall name (e.g., Hall 1, Main Auditorium)">
                            <span class="text-danger error-text hall_name_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="hall_type" class="form-label">Hall Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="hall_type" name="hall_type" required>
                                <option value="">Select Type</option>
                                <option value="AC">AC Hall</option>
                                <option value="Non AC">Non AC Hall</option>
                                <option value="Auditorium">Auditorium</option>
                                <option value="Classroom">Classroom</option>
                                <option value="HALL">Normal Hall</option>
                            </select>
                            <span class="text-danger error-text hall_type_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="hall_price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" id="hall_price" name="hall_price"
                                    required placeholder="0.00" min="0">
                            </div>
                            <div class="form-text">Enter 0 for free halls</div>
                            <span class="text-danger error-text hall_price_error"></span>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="hall_status" name="status" checked>
                            <label class="form-check-label" for="hall_status">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success" id="saveHallBtn">
                            <i class="fas fa-save me-2"></i>Save Hall
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Hall Modal -->
    <div class="modal fade" id="editHallModal" tabindex="-1" aria-labelledby="editHallModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editHallModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Hall
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editHallForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_hall_id" name="id">
                        <div class="mb-3">
                            <label for="edit_hall_id_field" class="form-label">Hall ID <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_hall_id_field" name="hall_id" required>
                            <span class="text-danger error-text edit_hall_id_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="edit_hall_name" class="form-label">Hall Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_hall_name" name="hall_name" required>
                            <span class="text-danger error-text edit_hall_name_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="edit_hall_type" class="form-label">Hall Type <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="edit_hall_type" name="hall_type" required>
                                <option value="">Select Type</option>
                                <option value="AC">AC Hall</option>
                                <option value="Non AC">Non AC Hall</option>
                                <option value="Auditorium">Auditorium</option>
                                <option value="Classroom">Classroom</option>
                                <option value="HALL">Normal Hall</option>
                            </select>
                            <span class="text-danger error-text edit_hall_type_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="edit_hall_price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" class="form-control" id="edit_hall_price" name="hall_price"
                                    required min="0">
                            </div>
                            <span class="text-danger error-text edit_hall_price_error"></span>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_hall_status" name="status">
                            <label class="form-check-label" for="edit_hall_status">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-warning" id="updateHallBtn">
                            <i class="fas fa-sync me-2"></i>Update Hall
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .table th {
            border-top: none;
            font-weight: 600;
        }

        .btn-group .btn {
            margin-right: 0.25rem;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }
        
        .price-cell {
            font-weight: 600;
        }
        
        .price-free {
            color: #28a745;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Get CSRF token
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Show alert function
        function showAlert(message, type = 'success') {
            // Remove existing alerts
            document.querySelectorAll('.alert-dismissible').forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                    <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Loading states
        function showLoadingState() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('hallsTableSection').style.display = 'none';
            document.getElementById('noHallsMessage').style.display = 'none';
        }

        function showContentState() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('hallsTableSection').style.display = 'block';
            document.getElementById('noHallsMessage').style.display = 'none';
        }

        function showNoDataState() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('hallsTableSection').style.display = 'none';
            document.getElementById('noHallsMessage').style.display = 'block';
        }

        // Format price with Rs. symbol
        function formatPrice(price) {
            if (price === null || price === undefined) return 'N/A';
            const numPrice = parseFloat(price);
            if (numPrice === 0) return '<span class="price-cell price-free">Free</span>';
            return `<span class="price-cell">Rs. ${numPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`;
        }

        // Get status text
        function getStatusText(status) {
            if (status === true || status == 1) return 'Active';
            if (status === false || status == 0) return 'Inactive';
            return 'Not Set';
        }

        // Get status badge
        function getStatusBadge(status) {
            if (status === true || status == 1) return '<span class="badge bg-success">Active</span>';
            if (status === false || status == 0) return '<span class="badge bg-danger">Inactive</span>';
            return '<span class="badge bg-secondary">Not Set</span>';
        }

        // Get hall type display text
        function getHallTypeDisplay(type) {
            if (!type) return 'N/A';
            const typeMap = {
                'AC': 'AC Hall',
                'Non AC': 'Non AC Hall',
                'Auditorium': 'Auditorium',
                'Classroom': 'Classroom',
                'HALL': 'Normal Hall'
            };
            return typeMap[type] || type;
        }

        // Load halls data
        async function loadHalls() {
            try {
                showLoadingState();

                const response = await fetch('/api/halls');
                if (!response.ok) throw new Error('Failed to fetch halls');

                const result = await response.json();

                if (result.status && result.data && result.data.length > 0) {
                    const tbody = document.getElementById('hallsTableBody');
                    tbody.innerHTML = '';

                    result.data.forEach((hall, index) => {
                        const statusBadge = getStatusBadge(hall.status);
                        
                        // Determine action buttons based on status
                        let actionButtons = '';
                        if (hall.status === true || hall.status == 1) {
                            actionButtons = `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning edit-hall" data-id="${hall.id}" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger toggle-status" data-id="${hall.id}" data-action="deactivate" data-bs-toggle="tooltip" title="Deactivate">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
                        } else if (hall.status === false || hall.status == 0) {
                            actionButtons = `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning edit-hall" data-id="${hall.id}" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success toggle-status" data-id="${hall.id}" data-action="activate" data-bs-toggle="tooltip" title="Activate">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            `;
                        } else {
                            // For null status
                            actionButtons = `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning edit-hall" data-id="${hall.id}" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success toggle-status" data-id="${hall.id}" data-action="activate" data-bs-toggle="tooltip" title="Set as Active">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            `;
                        }

                        const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td><strong>${hall.hall_id || 'N/A'}</strong></td>
                                <td>${hall.hall_name || 'N/A'}</td>
                                <td>${getHallTypeDisplay(hall.hall_type)}</td>
                                <td>${formatPrice(hall.hall_price)}</td>
                                <td>${statusBadge}</td>
                                <td>${hall.created_at ? new Date(hall.created_at).toLocaleDateString('en-IN') : 'N/A'}</td>
                                <td>${actionButtons}</td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });

                    showContentState();
                } else {
                    showNoDataState();
                }
            } catch (error) {
                console.error('Error loading halls:', error);
                showNoDataState();
                showAlert('Error loading halls data: ' + error.message, 'danger');
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadHalls();
            initializeEventListeners();
        });

        // Initialize event listeners
        function initializeEventListeners() {
            // Add Hall Form Submission
            document.getElementById('addHallForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const btn = document.getElementById('saveHallBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

                try {
                    const formData = new FormData(this);
                    const data = {
                        hall_id: formData.get('hall_id'),
                        hall_name: formData.get('hall_name'),
                        hall_type: formData.get('hall_type'),
                        hall_price: parseFloat(formData.get('hall_price')) || 0,
                        status: formData.get('status') ? 1 : 0
                    };

                    const response = await fetch('/api/halls', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok && result.status) {
                        // Close modal and reset form
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addHallModal'));
                        modal.hide();
                        this.reset();

                        // Clear errors
                        document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

                        // Reload data and show success message
                        loadHalls();
                        showAlert('Hall created successfully!');
                    } else {
                        // Handle validation errors
                        if (response.status === 422 && result.errors) {
                            // Clear previous errors
                            document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

                            // Show new errors
                            Object.keys(result.errors).forEach(key => {
                                const errorElement = document.querySelector(`.${key}_error`);
                                if (errorElement) {
                                    errorElement.textContent = result.errors[key][0];
                                }
                            });
                            showAlert('Please fix the validation errors', 'warning');
                        } else {
                            throw new Error(result.message || 'Failed to create hall');
                        }
                    }
                } catch (error) {
                    console.error('Error creating hall:', error);
                    showAlert('Error creating hall: ' + error.message, 'danger');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-2"></i>Save Hall';
                }
            });

            // Edit Hall - Open Modal
            document.addEventListener('click', function (e) {
                if (e.target.closest('.edit-hall')) {
                    const hallId = e.target.closest('.edit-hall').getAttribute('data-id');
                    openEditModal(hallId);
                }
            });

            // Edit Hall Form Submission
            document.getElementById('editHallForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const btn = document.getElementById('updateHallBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

                try {
                    const hallId = document.getElementById('edit_hall_id').value;
                    const formData = new FormData(this);
                    const data = {
                        hall_id: formData.get('hall_id'),
                        hall_name: formData.get('hall_name'),
                        hall_type: formData.get('hall_type'),
                        hall_price: parseFloat(formData.get('hall_price')) || 0,
                        status: formData.get('status') ? 1 : 0
                    };

                    const response = await fetch(`/api/halls/${hallId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok && result.status) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editHallModal'));
                        modal.hide();

                        // Clear errors
                        document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

                        // Reload data and show success message
                        loadHalls();
                        showAlert('Hall updated successfully!');
                    } else {
                        if (response.status === 422 && result.errors) {
                            document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

                            Object.keys(result.errors).forEach(key => {
                                const errorElement = document.querySelector(`.edit_${key}_error`);
                                if (errorElement) {
                                    errorElement.textContent = result.errors[key][0];
                                }
                            });
                            showAlert('Please fix the validation errors', 'warning');
                        } else {
                            throw new Error(result.message || 'Failed to update hall');
                        }
                    }
                } catch (error) {
                    console.error('Error updating hall:', error);
                    showAlert('Error updating hall: ' + error.message, 'danger');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync me-2"></i>Update Hall';
                }
            });

            // Toggle Status (Activate/Deactivate) - FIXED VERSION
            document.addEventListener('click', async function (e) {
                if (e.target.closest('.toggle-status')) {
                    const button = e.target.closest('.toggle-status');
                    const hallId = button.getAttribute('data-id');
                    const action = button.getAttribute('data-action');
                    
                    const actionText = action === 'activate' ? 'activate' : 'deactivate';
                    const actionPast = action === 'activate' ? 'activated' : 'deactivated';

                    if (!confirm(`Are you sure you want to ${actionText} this hall?`)) {
                        return;
                    }

                    try {
                        // Use the correct endpoint format from your original code
                        const url = `/api/halls/${hallId}/${action}`;
                        const response = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (response.ok && result.status) {
                            loadHalls();
                            showAlert(`Hall ${actionPast} successfully!`);
                        } else {
                            throw new Error(result.message || `Failed to ${actionText} hall`);
                        }
                    } catch (error) {
                        console.error(`Error ${actionText}ing hall:`, error);
                        showAlert(`Error ${actionText}ing hall: ` + error.message, 'danger');
                    }
                }
            });
        }

        // Open Edit Modal
        async function openEditModal(hallId) {
            try {
                const response = await fetch(`/api/halls/${hallId}`);
                const result = await response.json();

                if (result.status) {
                    const hall = result.data;
                    document.getElementById('edit_hall_id').value = hall.id;
                    document.getElementById('edit_hall_id_field').value = hall.hall_id || '';
                    document.getElementById('edit_hall_name').value = hall.hall_name || '';
                    document.getElementById('edit_hall_type').value = hall.hall_type || '';
                    document.getElementById('edit_hall_price').value = hall.hall_price || 0;
                    document.getElementById('edit_hall_status').checked = hall.status == 1;

                    // Clear previous errors
                    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

                    const modal = new bootstrap.Modal(document.getElementById('editHallModal'));
                    modal.show();
                } else {
                    throw new Error(result.message || 'Failed to load hall data');
                }
            } catch (error) {
                console.error('Error loading hall data:', error);
                showAlert('Error loading hall data: ' + error.message, 'danger');
            }
        }
    </script>
@endpush