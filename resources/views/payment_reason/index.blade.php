@extends('layouts.app')

@section('title', 'Payment Reason Management')
@section('page-title', 'Payment Reason Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Payment Reason</li>
@endsection

@section('styles')
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1) !important;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
        }
        
        .action-buttons .btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        
        .reason-code {
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 2.5rem;
            border-radius: 20px;
        }
        
        .search-box .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .floating-add-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            z-index: 1000;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Toast Notification Container -->
    <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;"></div>
    
    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary h-100 py-2 card-hover">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Payment Reasons</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCount">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success h-100 py-2 card-hover">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Last Added</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800" id="lastAdded">-</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-plus-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info h-100 py-2 card-hover">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Today's Activity</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayActivity">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-day fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning h-100 py-2 card-hover">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Unique Codes</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="uniqueCodes">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-key fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="mb-2 mb-md-0">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-money-check-alt me-2"></i>Payment Reasons
                        </h5>
                        <p class="text-muted mb-0 small">Manage payment reasons and their codes</p>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <!-- Search Box -->
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search reasons..." style="width: 250px;">
                        </div>
                        
                        <!-- Add New Button -->
                        <button class="btn btn-primary" id="addNewBtn">
                            <i class="fas fa-plus me-1"></i> Add New
                        </button>
                        
                        <!-- Refresh Button -->
                        <button class="btn btn-outline-secondary" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading payment reasons...</p>
                    </div>
                    
                    <!-- Error State -->
                    <div id="errorState" class="text-center py-5" style="display: none;">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5 class="text-danger">Failed to load payment reasons</h5>
                        <p id="errorMessage" class="text-muted"></p>
                        <button class="btn btn-primary mt-3" id="retryBtn">
                            <i class="fas fa-redo me-1"></i> Try Again
                        </button>
                    </div>
                    
                    <!-- Data Table -->
                    <div class="table-responsive" id="dataTable" style="display: none;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Reason Code</th>
                                    <th width="40%">Reason</th>
                                    <th width="15%">Created</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentReasonsTable">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                        
                        <!-- Empty State -->
                        <div id="emptyState" class="empty-state" style="display: none;">
                            <i class="fas fa-inbox"></i>
                            <h4 class="mt-3">No Payment Reasons Found</h4>
                            <p class="text-muted">Get started by creating your first payment reason.</p>
                            <button class="btn btn-primary mt-2" id="addFirstBtn">
                                <i class="fas fa-plus me-1"></i> Add Payment Reason
                            </button>
                        </div>
                        
                        <!-- Pagination -->
                        <nav id="paginationContainer" class="d-none">
                            <ul class="pagination justify-content-center mt-4">
                                <li class="page-item disabled" id="prevPage">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item active"><span class="page-link">1</span></li>
                                <li class="page-item" id="nextPage">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="paymentReasonModal" tabindex="-1" aria-labelledby="paymentReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-plus me-2"></i>Add New Payment Reason
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentReasonForm">
                    <input type="hidden" id="editId">
                    
                    <div class="mb-3">
                        <label for="reason_code" class="form-label">
                            <i class="fas fa-key me-1 text-primary"></i>Reason Code
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="reason_code" 
                               placeholder="Enter unique code (e.g., TUITION_FEE)" required>
                        <div class="form-text">
                            <small>This code must be unique and will be used in payment systems.</small>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="reason" class="form-label">
                            <i class="fas fa-sticky-note me-1 text-primary"></i>Reason Description
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="reason" rows="3" 
                                  placeholder="Enter payment reason description..." required></textarea>
                    </div>
                    
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Make sure the reason code is unique and descriptive enough for easy identification.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveBtn">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger"></i>
                </div>
                <h5>Are you sure?</h5>
                <p class="text-muted" id="deleteMessage">
                    You are about to delete a payment reason. This action cannot be undone.
                </p>
                <div class="alert alert-warning mt-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <small>Make sure this reason is not being used in any active payments.</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<button class="btn btn-primary btn-lg floating-add-btn" id="floatingAddBtn">
    <i class="fas fa-plus"></i>
</button>
@endsection

@push('styles')
<style>
    /* Toast Notifications */
    .custom-toast {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
        margin-bottom: 15px;
        animation: slideInRight 0.3s ease-out;
        backdrop-filter: blur(10px);
    }

    .toast-success {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.95), rgba(25, 135, 84, 0.95));
    }

    .toast-error {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.95), rgba(200, 35, 51, 0.95));
    }

    .toast-info {
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.95), rgba(13, 110, 253, 0.95));
    }

    .toast-warning {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.95), rgba(255, 152, 0, 0.95));
    }

    .toast-body {
        color: white;
        padding: 15px 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    /* Modal Animations */
    .modal.fade .modal-dialog {
        transform: scale(0.9);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }

    /* Button Animations */
    .floating-add-btn {
        transition: all 0.3s ease;
    }

    .floating-add-btn:hover {
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4) !important;
    }

    /* Table Row Animation */
    .table tbody tr {
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Loading Spinner Animation */
    .spinner-border {
        animation: spinner-border 0.75s linear infinite;
    }

    /* Card Hover Effect */
    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // API Base URL
    const API_BASE_URL = '/api/payment-reason';
    
    // Modal instances
    let paymentReasonModal = null;
    let deleteModal = null;
    
    // Variables
    let currentDeleteId = null;
    let allPaymentReasons = [];
    let filteredPaymentReasons = [];
    
    // Initialize modals
    const initModals = () => {
        const paymentReasonModalEl = document.getElementById('paymentReasonModal');
        const deleteModalEl = document.getElementById('deleteModal');
        
        if (paymentReasonModalEl) {
            paymentReasonModal = new bootstrap.Modal(paymentReasonModalEl);
        }
        
        if (deleteModalEl) {
            deleteModal = new bootstrap.Modal(deleteModalEl);
        }
    };
    
    // Toast Notification System
    const showToast = (message, type = 'info', duration = 5000) => {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;
        
        const toast = document.createElement('div');
        toast.className = `custom-toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-body">
                <i class="fas ${getToastIcon(type)} me-3 fa-lg"></i>
                <div class="flex-grow-1">
                    <div>${message}</div>
                    <small class="opacity-75">${new Date().toLocaleTimeString()}</small>
                </div>
                <button type="button" class="btn-close btn-close-white ms-3" 
                        onclick="this.closest('.custom-toast').remove()"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after duration
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'fadeOut 0.5s ease-out';
                setTimeout(() => toast.remove(), 500);
            }
        }, duration);
    };
    
    const getToastIcon = (type) => {
        switch(type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            case 'info': return 'fa-info-circle';
            default: return 'fa-info-circle';
        }
    };
    
    // UI State Management
    const showLoading = (show) => {
        const loadingEl = document.getElementById('loadingSpinner');
        const dataTableEl = document.getElementById('dataTable');
        const errorStateEl = document.getElementById('errorState');
        
        if (loadingEl) loadingEl.style.display = show ? 'block' : 'none';
        if (dataTableEl) dataTableEl.style.display = show ? 'none' : 'block';
        if (errorStateEl) errorStateEl.style.display = 'none';
    };
    
    const showError = (message) => {
        const errorStateEl = document.getElementById('errorState');
        const errorMessageEl = document.getElementById('errorMessage');
        const dataTableEl = document.getElementById('dataTable');
        const loadingEl = document.getElementById('loadingSpinner');
        
        if (errorStateEl && errorMessageEl) {
            errorMessageEl.textContent = message;
            errorStateEl.style.display = 'block';
        }
        
        if (dataTableEl) dataTableEl.style.display = 'none';
        if (loadingEl) loadingEl.style.display = 'none';
    };
    
    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    };
    
    // Fetch all payment reasons
    const fetchPaymentReasons = async () => {
        try {
            showLoading(true);
            
            const response = await fetch(API_BASE_URL, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.status === 'success') {
                allPaymentReasons = data.data || [];
                filteredPaymentReasons = [...allPaymentReasons];
                renderPaymentReasons();
                updateStatistics();
            } else {
                throw new Error(data.message || 'Failed to load payment reasons');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showError(error.message || 'Failed to load payment reasons. Please try again.');
            showToast('Failed to load payment reasons', 'error');
        } finally {
            showLoading(false);
        }
    };
    
    // Render payment reasons table
    const renderPaymentReasons = () => {
        const tableBody = document.getElementById('paymentReasonsTable');
        const emptyState = document.getElementById('emptyState');
        
        if (!tableBody || !emptyState) return;
        
        if (filteredPaymentReasons.length === 0) {
            tableBody.innerHTML = '';
            emptyState.style.display = 'block';
            return;
        }
        
        emptyState.style.display = 'none';
        
        let html = '';
        filteredPaymentReasons.forEach((reason, index) => {
            html += `
                <tr>
                    <td class="align-middle">${index + 1}</td>
                    <td class="align-middle">
                        <span class="reason-code">${reason.reason_code}</span>
                    </td>
                    <td class="align-middle">
                        <div class="fw-semibold">${reason.reason}</div>
                    </td>
                    <td class="align-middle">
                        <span class="text-muted small">${formatDate(reason.created_at)}</span>
                    </td>
                    <td class="align-middle">
                        <div class="action-buttons d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary edit-btn" 
                                    data-id="${reason.id}"
                                    data-code="${reason.reason_code}"
                                    data-reason="${reason.reason}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" 
                                    data-id="${reason.id}"
                                    data-reason="${reason.reason}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = html;
        
        // Attach event listeners to buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.getAttribute('data-id');
                const code = e.currentTarget.getAttribute('data-code');
                const reason = e.currentTarget.getAttribute('data-reason');
                openEditModal(id, code, reason);
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.getAttribute('data-id');
                const reason = e.currentTarget.getAttribute('data-reason');
                openDeleteModal(id, reason);
            });
        });
    };
    
    // Update statistics
    const updateStatistics = () => {
        const totalCount = document.getElementById('totalCount');
        const lastAdded = document.getElementById('lastAdded');
        const uniqueCodes = document.getElementById('uniqueCodes');
        
        if (totalCount) {
            totalCount.textContent = allPaymentReasons.length;
        }
        
        if (lastAdded && allPaymentReasons.length > 0) {
            const lastReason = allPaymentReasons[allPaymentReasons.length - 1];
            lastAdded.textContent = lastReason.reason_code;
        }
        
        if (uniqueCodes) {
            const codes = new Set(allPaymentReasons.map(r => r.reason_code));
            uniqueCodes.textContent = codes.size;
        }
    };
    
    // Open modal for adding new
    const openAddModal = () => {
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('paymentReasonForm');
        
        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-plus me-2"></i>Add New Payment Reason';
        }
        
        if (form) {
            form.reset();
            document.getElementById('editId').value = '';
        }
        
        if (paymentReasonModal) {
            paymentReasonModal.show();
        }
    };
    
    // Open modal for editing
    const openEditModal = (id, code, reason) => {
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('paymentReasonForm');
        
        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-edit me-2"></i>Edit Payment Reason';
        }
        
        if (form) {
            form.reset();
            document.getElementById('editId').value = id;
            document.getElementById('reason_code').value = code;
            document.getElementById('reason').value = reason;
        }
        
        if (paymentReasonModal) {
            paymentReasonModal.show();
        }
    };
    
    // Open delete confirmation modal
    const openDeleteModal = (id, reason) => {
        currentDeleteId = id;
        const deleteMessage = document.getElementById('deleteMessage');
        
        if (deleteMessage) {
            deleteMessage.innerHTML = `
                You are about to delete the payment reason:<br>
                <strong class="text-danger">"${reason}"</strong><br><br>
                This action cannot be undone.
            `;
        }
        
        if (deleteModal) {
            deleteModal.show();
        }
    };
    
    // Save payment reason (create or update)
    const savePaymentReason = async () => {
        const id = document.getElementById('editId').value;
        const reasonCode = document.getElementById('reason_code').value.trim();
        const reason = document.getElementById('reason').value.trim();
        
        // Validate
        if (!reasonCode || !reason) {
            showToast('Please fill in all required fields', 'warning');
            return;
        }
        
        try {
            showLoading(true);
            
            const url = id ? `${API_BASE_URL}/${id}` : API_BASE_URL;
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    reason_code: reasonCode,
                    reason: reason
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                showToast(
                    id ? 'Payment reason updated successfully!' : 'Payment reason created successfully!',
                    'success'
                );
                
                if (paymentReasonModal) {
                    paymentReasonModal.hide();
                }
                
                // Refresh the list
                await fetchPaymentReasons();
                
            } else {
                throw new Error(data.message || 'Failed to save payment reason');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to save payment reason', 'error');
        } finally {
            showLoading(false);
        }
    };
    
    // Delete payment reason
    const deletePaymentReason = async () => {
        if (!currentDeleteId) return;
        
        try {
            showLoading(true);
            
            const response = await fetch(`${API_BASE_URL}/${currentDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                showToast('Payment reason deleted successfully!', 'success');
                
                if (deleteModal) {
                    deleteModal.hide();
                }
                
                currentDeleteId = null;
                
                // Refresh the list
                await fetchPaymentReasons();
                
            } else {
                throw new Error(data.message || 'Failed to delete payment reason');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to delete payment reason', 'error');
        } finally {
            showLoading(false);
        }
    };
    
    // Search functionality
    const setupSearch = () => {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;
        
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                if (searchTerm === '') {
                    filteredPaymentReasons = [...allPaymentReasons];
                } else {
                    filteredPaymentReasons = allPaymentReasons.filter(reason => 
                        reason.reason_code.toLowerCase().includes(searchTerm) ||
                        reason.reason.toLowerCase().includes(searchTerm)
                    );
                }
                
                renderPaymentReasons();
            }, 300);
        });
    };
    
    // Event Listeners Setup
    const setupEventListeners = () => {
        // Add new button
        const addNewBtn = document.getElementById('addNewBtn');
        const addFirstBtn = document.getElementById('addFirstBtn');
        const floatingAddBtn = document.getElementById('floatingAddBtn');
        
        if (addNewBtn) addNewBtn.addEventListener('click', openAddModal);
        if (addFirstBtn) addFirstBtn.addEventListener('click', openAddModal);
        if (floatingAddBtn) floatingAddBtn.addEventListener('click', openAddModal);
        
        // Save button
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) saveBtn.addEventListener('click', savePaymentReason);
        
        // Delete confirmation button
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) confirmDeleteBtn.addEventListener('click', deletePaymentReason);
        
        // Refresh button
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) refreshBtn.addEventListener('click', fetchPaymentReasons);
        
        // Retry button
        const retryBtn = document.getElementById('retryBtn');
        if (retryBtn) retryBtn.addEventListener('click', fetchPaymentReasons);
        
        // Search setup
        setupSearch();
        
        // Allow Enter key to save in modal
        const modalForm = document.getElementById('paymentReasonForm');
        if (modalForm) {
            modalForm.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    savePaymentReason();
                }
            });
        }
    };
    
    // Initialize application
    const initialize = () => {
        initModals();
        setupEventListeners();
        fetchPaymentReasons();
    };
    
    // Start the application
    initialize();
});
</script>
@endpush