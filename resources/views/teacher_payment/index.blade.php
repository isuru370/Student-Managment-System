@extends('layouts.app')

@section('title', 'Teacher Income')
@section('page-title', 'Teacher Income')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Teacher Income</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <!-- Static Section at the Top -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Summary for {{ date('F Y') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Payments This Month</h6>
                                        <h3 class="mb-0" id="summaryTotalPayments">LKR 0.00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Teacher Earnings</h6>
                                        <h3 class="mb-0" id="summaryTotalEarnings">LKR 0.00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Teachers</h6>
                                        <h3 class="mb-0" id="summaryTotalTeachers">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Static Section -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Teacher Monthly Income - {{ date('F Y') }}</h5>
                        <div class="card-tools">
                            <button id="refreshBtn" class="btn btn-light btn-sm">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" id="searchInput" class="form-control"
                                        placeholder="Search by teacher name or ID...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        Clear
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group">
                                    <button class="btn btn-success" id="exportExcelBtn">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                    <button class="btn btn-danger" id="exportPdfBtn">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="text-center d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading teacher data...</p>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="teacherTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th data-sort="teacher_id">
                                            ID <i class="fas fa-sort"></i>
                                        </th>
                                        <th data-sort="teacher_name">
                                            Teacher Name <i class="fas fa-sort"></i>
                                        </th>
                                        <th data-sort="percentage">
                                            Percentage <i class="fas fa-sort"></i>
                                        </th>
                                        <th data-sort="total_payments_this_month">
                                            Total Payments <i class="fas fa-sort"></i>
                                        </th>
                                        <th data-sort="teacher_earning">
                                            Payment Due <i class="fas fa-sort"></i>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="teacherTableBody">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div id="emptyState" class="text-center d-none">
                            <div class="alert alert-info">
                                <h4><i class="fas fa-info-circle"></i> No Data Available</h4>
                                <p>No teacher payment data found for the current month.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advance Payment Modal -->
    <div class="modal fade" id="advanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Make Advance Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="advancePaymentForm">
                    <div class="modal-body">
                        <input type="hidden" id="advanceTeacherId" name="teacher_id">

                        <div class="mb-3">
                            <label for="teacherName" class="form-label">Teacher</label>
                            <input type="text" class="form-control" id="teacherName" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="availableEarning" class="form-label">Available Earning</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input type="text" class="form-control" id="availableEarning" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Advance Amount *</label>
                            <div class="input-group">
                                <span class="input-group-text">LKR</span>
                                <input type="number" class="form-control" id="amount" name="payment" min="1" step="0.01"
                                    required>
                            </div>
                            <div class="form-text">Enter amount up to available earning</div>
                            <div class="invalid-feedback" id="amountError"></div>
                        </div>

                        <div class="mb-3">
                            <label for="reasonCode" class="form-label">Reason Code *</label>
                            <select class="form-control" id="reasonCode" name="reason_code" required>
                                <option value="">Select a reason...</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                            <div class="invalid-feedback" id="reasonCodeError"></div>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                This advance payment will be recorded against the teacher's earnings.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="submitAdvanceBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Submit Advance Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }

        .table-success:hover {
            background-color: rgba(40, 167, 69, 0.2) !important;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
        }

        .card-header {
            padding: 0.75rem 1.25rem;
        }

        .modal-header {
            padding: 1rem 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
        }

        .invalid-feedback {
            display: block;
        }

        .spinner-border {
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .btn-group>.btn {
                border-radius: 0.25rem !important;
                margin-right: 0 !important;
            }

            .card-header .card-tools {
                margin-top: 0.5rem;
                width: 100%;
                text-align: right;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <!-- SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        (function () {
            'use strict';

            // CSRF Token setup for Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Global variables
            let teachersData = [];
            let paymentReasons = [];
            let currentSort = { column: null, direction: 'asc' };
            let advanceModalInstance = null;

            // DOM Elements
            const teacherTableBody = document.getElementById('teacherTableBody');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const emptyState = document.getElementById('emptyState');
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');
            const refreshBtn = document.getElementById('refreshBtn');
            const exportExcelBtn = document.getElementById('exportExcelBtn');
            const exportPdfBtn = document.getElementById('exportPdfBtn');
            const summaryTotalPayments = document.getElementById('summaryTotalPayments');
            const summaryTotalEarnings = document.getElementById('summaryTotalEarnings');
            const summaryTotalTeachers = document.getElementById('summaryTotalTeachers');

            // API Endpoints
            const API_ENDPOINTS = {
                teacherPayments: '/api/teacher-payments/monthly-income',
                advancePayment: '/api/teacher-payments',
                paymentReasons: '/api/payment-reason/dropdown',
                viewTeacher: (id) => `/teacher-payment/view/${id}`,
                payTeacher: (id) => `/teacher-payment/pay/${id}`,
                payhistory: (id) => `/teacher-payment/history/${id}`

            };

            // Configuration
            const CONFIG = {
                debounceDelay: 300,
                alertTimeout: {
                    success: 5000,
                    error: 10000,
                    warning: 8000,
                    info: 5000
                },
                currency: {
                    locale: 'en-LK',
                    currency: 'LKR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            };

            // Format currency to LKR
            function formatCurrency(amount) {
                if (isNaN(amount) || amount === null || amount === undefined) {
                    amount = 0;
                }
                return new Intl.NumberFormat(CONFIG.currency.locale, {
                    style: 'currency',
                    currency: CONFIG.currency.currency,
                    minimumFractionDigits: CONFIG.currency.minimumFractionDigits,
                    maximumFractionDigits: CONFIG.currency.maximumFractionDigits
                }).format(amount);
            }

            // Format number with commas
            function formatNumber(number) {
                if (isNaN(number) || number === null || number === undefined) {
                    number = 0;
                }
                return new Intl.NumberFormat(CONFIG.currency.locale).format(number);
            }

            // Format percentage
            function formatPercentage(percentage) {
                if (isNaN(percentage) || percentage === null || percentage === undefined) {
                    return '0%';
                }
                return `${percentage}%`;
            }

            // Parse currency string to number
            function parseCurrency(currencyString) {
                if (!currencyString) return 0;
                const cleaned = currencyString.replace(/[^\d.-]/g, '');
                return parseFloat(cleaned) || 0;
            }

            // Load payment reasons dropdown
            async function loadPaymentReasons() {
                try {
                    const response = await fetch(API_ENDPOINTS.paymentReasons);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (data.status === 'success') {
                        paymentReasons = data.data || [];
                        populateReasonCodes();
                    } else {
                        throw new Error(data.message || 'Failed to load payment reasons');
                    }
                } catch (error) {
                    console.error('Error loading payment reasons:', error);
                    populateReasonCodes();
                    showAlert('Using default payment reasons. Some features may be limited.', 'warning');
                }
            }

            // Populate reason codes in select dropdown
            function populateReasonCodes() {
                const select = document.getElementById('reasonCode');
                if (!select) return;

                select.innerHTML = '<option value="">Select a reason...</option>';

                paymentReasons.forEach(reason => {
                    const option = document.createElement('option');
                    option.value = reason.reason_code || reason.code || reason;
                    option.textContent = reason.reason_code || reason.code || reason;
                    select.appendChild(option);
                });
            }

            // Fetch teacher payments data
            async function fetchTeacherPayments() {
                showLoading(true);

                try {
                    const response = await fetch(API_ENDPOINTS.teacherPayments);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (data.status === 'success') {
                        teachersData = data.data || [];
                        renderTable(teachersData);
                        updateSummary(teachersData);
                    } else {
                        throw new Error(data.message || 'Failed to load teacher payments');
                    }
                } catch (error) {
                    console.error('Error fetching teacher payments:', error);
                    showAlert('Failed to load teacher payments. Please try again.', 'danger');
                    teacherTableBody.innerHTML = '';
                    showEmptyState(true);
                } finally {
                    showLoading(false);
                }
            }

            // Render table with data
            function renderTable(data) {
                if (!teacherTableBody) return;

                if (!data || data.length === 0) {
                    showEmptyState(true);
                    return;
                }

                showEmptyState(false);
                teacherTableBody.innerHTML = '';

                // Check if we're in the last 5 days of the month
                const showPayButton = isLastFiveDaysOfMonth();

                data.forEach(teacher => {
                    const row = document.createElement('tr');

                    // Highlight rows with earnings > 0
                    const teacherEarning = parseFloat(teacher.teacher_earning) || 0;
                    if (teacherEarning > 0) {
                        row.classList.add('table-success');
                    }

                    row.innerHTML = `
                <td>${teacher.teacher_id || ''}</td>
                <td>${teacher.teacher_name || ''}</td>
                <td>${formatPercentage(teacher.percentage)}</td>
                <td>${formatCurrency(teacher.total_payments_this_month)}</td>
                <td>${formatCurrency(teacher.teacher_earning)}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="${API_ENDPOINTS.viewTeacher(teacher.teacher_id)}" 
                           class="btn btn-info" target="_blank">
                            <i class="fas fa-eye"></i> View
                        </a>
                        ${showPayButton ? `
                        <a href="${API_ENDPOINTS.payTeacher(teacher.teacher_id)}" 
                           class="btn btn-success ${teacherEarning === 0 ? 'disabled' : ''}"
                           ${teacherEarning === 0 ? 'aria-disabled="true"' : ''}>
                            <i class="fas fa-money-bill-wave"></i> Pay
                        </a>
                        ` : ''}
                        <button type="button" class="btn btn-warning advance-btn" 
                                data-teacher-id="${teacher.teacher_id || ''}"
                                data-teacher-name="${teacher.teacher_name || ''}"
                                data-teacher-earning="${teacherEarning}"
                                ${teacherEarning === 0 ? 'disabled' : ''}>
                            <i class="fas fa-hand-holding-usd"></i> Advance
                        </button>
                        <a href="${API_ENDPOINTS.payhistory(teacher.teacher_id)}" 
                           class="btn btn-primary"
                           ${teacherEarning === 0 ? 'aria-disabled="true"' : ''}>
                            <i class="fas fa-history"></i> History
                        </a>
                    </div>
                </td>
            `;

                    teacherTableBody.appendChild(row);
                });

                // Attach event listeners to advance buttons
                attachAdvanceButtonListeners();
            }

            // Update summary cards
            function updateSummary(data) {
                if (!summaryTotalPayments || !summaryTotalEarnings || !summaryTotalTeachers) return;

                const totalPaymentsSum = data.reduce((sum, teacher) => sum + (parseFloat(teacher.total_payments_this_month) || 0), 0);
                const totalEarningsSum = data.reduce((sum, teacher) => sum + (parseFloat(teacher.teacher_earning) || 0), 0);

                summaryTotalPayments.textContent = formatCurrency(totalPaymentsSum);
                summaryTotalEarnings.textContent = formatCurrency(totalEarningsSum);
                summaryTotalTeachers.textContent = formatNumber(data.length);
            }

            // Show/hide loading spinner
            function showLoading(show) {
                if (!loadingSpinner) return;

                if (show) {
                    loadingSpinner.classList.remove('d-none');
                    if (teacherTableBody) {
                        teacherTableBody.innerHTML = '';
                    }
                } else {
                    loadingSpinner.classList.add('d-none');
                }
            }

            // Show/hide empty state
            function showEmptyState(show) {
                if (!emptyState) return;

                if (show) {
                    emptyState.classList.remove('d-none');
                } else {
                    emptyState.classList.add('d-none');
                }
            }

            // Show alert message
            function showAlert(message, type = 'info') {
                const alertTypes = {
                    'success': { class: 'alert-success', icon: 'fa-check-circle' },
                    'danger': { class: 'alert-danger', icon: 'fa-exclamation-circle' },
                    'warning': { class: 'alert-warning', icon: 'fa-exclamation-triangle' },
                    'info': { class: 'alert-info', icon: 'fa-info-circle' }
                };

                const alertConfig = alertTypes[type] || alertTypes.info;

                const alertDiv = document.createElement('div');
                alertDiv.className = `alert ${alertConfig.class} alert-dismissible fade show`;
                alertDiv.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <i class="fas ${alertConfig.icon} me-2"></i>
                                        <div>${message}</div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                `;

                const cardBody = document.querySelector('.card-body');
                if (cardBody) {
                    cardBody.insertBefore(alertDiv, cardBody.firstChild);

                    // Auto remove after timeout
                    const timeout = CONFIG.alertTimeout[type] || CONFIG.alertTimeout.info;
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            try {
                                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
                                bsAlert.close();
                            } catch (e) {
                                alertDiv.remove();
                            }
                        }
                    }, timeout);
                }
            }

            // Sort table data
            function sortTable(column, data) {
                if (!column || !data) return data;

                return [...data].sort((a, b) => {
                    let aValue = a[column];
                    let bValue = b[column];

                    // Handle different data types
                    if (column === 'teacher_name') {
                        aValue = (aValue || '').toLowerCase();
                        bValue = (bValue || '').toLowerCase();
                    } else if (column === 'teacher_id' || column === 'percentage' ||
                        column === 'total_payments_this_month' || column === 'teacher_earning') {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                    }

                    if (aValue < bValue) return currentSort.direction === 'asc' ? -1 : 1;
                    if (aValue > bValue) return currentSort.direction === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            // Filter table data
            function filterTable(searchTerm, data) {
                if (!searchTerm || !data) return data;

                const term = searchTerm.toLowerCase();
                return data.filter(teacher => {
                    const teacherName = (teacher.teacher_name || '').toLowerCase();
                    const teacherId = (teacher.teacher_id || '').toString().toLowerCase();
                    return teacherName.includes(term) || teacherId.includes(term);
                });
            }

            // Attach event listeners to advance buttons
            function attachAdvanceButtonListeners() {
                // Use event delegation on the table body
                if (teacherTableBody) {
                    teacherTableBody.addEventListener('click', function (event) {
                        const advanceBtn = event.target.closest('.advance-btn');
                        if (advanceBtn && !advanceBtn.disabled) {
                            event.preventDefault();
                            event.stopPropagation();

                            const teacherId = advanceBtn.getAttribute('data-teacher-id');
                            const teacherName = advanceBtn.getAttribute('data-teacher-name');
                            const teacherEarning = parseFloat(advanceBtn.getAttribute('data-teacher-earning')) || 0;

                            showAdvanceModal(teacherId, teacherName, teacherEarning);
                        }
                    });
                }
            }

            // Show Advance Modal
            function showAdvanceModal(teacherId, teacherName, teacherEarning) {
                // Get modal element
                const advanceModalElement = document.getElementById('advanceModal');
                if (!advanceModalElement) return;

                // Set values in the modal inputs
                const teacherNameInput = document.getElementById('teacherName');
                const availableEarningInput = document.getElementById('availableEarning');
                const advanceTeacherIdInput = document.getElementById('advanceTeacherId');
                const amountInput = document.getElementById('amount');
                const reasonCodeSelect = document.getElementById('reasonCode');

                // Set values
                if (teacherNameInput) teacherNameInput.value = teacherName || '';
                if (availableEarningInput) availableEarningInput.value = formatCurrency(teacherEarning);
                if (advanceTeacherIdInput) advanceTeacherIdInput.value = teacherId || '';

                // Configure amount input
                if (amountInput) {
                    amountInput.value = '';
                    amountInput.max = teacherEarning;
                    amountInput.placeholder = `Max: ${formatCurrency(teacherEarning)}`;
                    amountInput.classList.remove('is-invalid');
                }

                // Reset reason code
                if (reasonCodeSelect) {
                    reasonCodeSelect.value = '';
                    reasonCodeSelect.classList.remove('is-invalid');
                }

                // Clear validation messages
                const amountError = document.getElementById('amountError');
                const reasonCodeError = document.getElementById('reasonCodeError');
                if (amountError) amountError.textContent = '';
                if (reasonCodeError) reasonCodeError.textContent = '';

                // Show modal
                try {
                    if (advanceModalInstance) {
                        advanceModalInstance.dispose();
                    }

                    advanceModalInstance = new bootstrap.Modal(advanceModalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });

                    advanceModalInstance.show();

                    // Focus on amount input
                    advanceModalElement.addEventListener('shown.bs.modal', function () {
                        if (amountInput) {
                            setTimeout(() => amountInput.focus(), 100);
                        }
                    });
                } catch (error) {
                    console.error('Error showing modal:', error);
                    // Fallback for Bootstrap issues
                    advanceModalElement.classList.add('show');
                    advanceModalElement.style.display = 'block';
                    advanceModalElement.setAttribute('aria-modal', 'true');
                    advanceModalElement.setAttribute('role', 'dialog');

                    // Add backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                    document.body.classList.add('modal-open');
                }
            }

            // Submit Advance Payment
            function setupAdvancePaymentForm() {
                const advancePaymentForm = document.getElementById('advancePaymentForm');
                if (!advancePaymentForm) return;

                advancePaymentForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const submitBtn = document.getElementById('submitAdvanceBtn');
                    const teacherId = document.getElementById('advanceTeacherId')?.value;
                    const amountInput = document.getElementById('amount');
                    const amount = parseFloat(amountInput?.value) || 0;
                    const maxAmount = parseFloat(amountInput?.max) || 0;
                    const reasonCodeSelect = document.getElementById('reasonCode');
                    const reasonCode = reasonCodeSelect?.value || '';

                    // Reset validation
                    if (amountInput) amountInput.classList.remove('is-invalid');
                    if (reasonCodeSelect) reasonCodeSelect.classList.remove('is-invalid');

                    // Validation
                    let isValid = true;

                    // Amount validation
                    if (!amount || amount <= 0) {
                        if (amountInput) {
                            amountInput.classList.add('is-invalid');
                            document.getElementById('amountError').textContent = 'Please enter a valid amount';
                        }
                        isValid = false;
                    } else if (amount > maxAmount) {
                        if (amountInput) {
                            amountInput.classList.add('is-invalid');
                            document.getElementById('amountError').textContent = `Amount cannot exceed ${formatCurrency(maxAmount)}`;
                        }
                        isValid = false;
                    }

                    // Reason code validation
                    if (!reasonCode) {
                        if (reasonCodeSelect) {
                            reasonCodeSelect.classList.add('is-invalid');
                            document.getElementById('reasonCodeError').textContent = 'Please select a reason code';
                        }
                        isValid = false;
                    }

                    if (!isValid) {
                        return;
                    }

                    // Prepare data
                    const formData = {
                        teacher_id: teacherId,
                        payment: amount,
                        reason_code: reasonCode
                    };

                    try {
                        // Show loading
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            const spinner = submitBtn.querySelector('.spinner-border');
                            if (spinner) spinner.classList.remove('d-none');
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                        }

                        // Make API request
                        const response = await fetch(API_ENDPOINTS.advancePayment, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(formData)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || data.error || `HTTP error! status: ${response.status}`);
                        }

                        if (data.status === 'success') {
                            // Close modal
                            if (advanceModalInstance) {
                                advanceModalInstance.hide();
                            }

                            // Show success message
                            showAlert(data.message || 'Advance payment submitted successfully!', 'success');

                            // Refresh data after a delay
                            setTimeout(() => {
                                fetchTeacherPayments();
                                loadPaymentReasons();
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'Failed to submit advance payment');
                        }
                    } catch (error) {
                        console.error('Error submitting advance payment:', error);
                        showAlert(error.message || 'Failed to submit advance payment. Please try again.', 'danger');
                    } finally {
                        // Reset button
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> Submit Advance Payment';
                        }
                    }
                });
            }

            // Export to Excel
            function setupExportExcel() {
                if (!exportExcelBtn) return;

                exportExcelBtn.addEventListener('click', function () {
                    if (!teachersData || teachersData.length === 0) {
                        showAlert('No data to export', 'warning');
                        return;
                    }

                    try {
                        // Prepare data for export
                        const exportData = teachersData.map(teacher => ({
                            'Teacher ID': teacher.teacher_id || '',
                            'Teacher Name': teacher.teacher_name || '',
                            'Percentage': formatPercentage(teacher.percentage),
                            'Total Payments': parseFloat(teacher.total_payments_this_month) || 0,
                            'Teacher Earning': parseFloat(teacher.teacher_earning) || 0
                        }));

                        // Create worksheet
                        const ws = XLSX.utils.json_to_sheet(exportData);

                        // Create workbook
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Teacher Payments');

                        // Generate filename with timestamp
                        const timestamp = new Date().toISOString().split('T')[0];
                        const filename = `teacher_payments_${timestamp}.xlsx`;

                        // Generate Excel file
                        XLSX.writeFile(wb, filename);

                        showAlert('Excel file exported successfully!', 'success');
                    } catch (error) {
                        console.error('Error exporting to Excel:', error);
                        showAlert('Failed to export Excel file. Please try again.', 'danger');
                    }
                });
            }

            // Export to PDF
            function setupExportPdf() {
                if (!exportPdfBtn) return;

                exportPdfBtn.addEventListener('click', function () {
                    if (!teachersData || teachersData.length === 0) {
                        showAlert('No data to export', 'warning');
                        return;
                    }

                    try {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF();

                        // Title
                        doc.setFontSize(16);
                        doc.text('Teacher Payments Report', 14, 15);
                        doc.setFontSize(10);
                        doc.text(`Date: ${new Date().toLocaleDateString()}`, 14, 22);

                        // Summary section
                        const totalPaymentsSum = teachersData.reduce((sum, teacher) => sum + (parseFloat(teacher.total_payments_this_month) || 0), 0);
                        const totalEarningsSum = teachersData.reduce((sum, teacher) => sum + (parseFloat(teacher.teacher_earning) || 0), 0);

                        doc.text(`Total Teachers: ${teachersData.length}`, 14, 30);
                        doc.text(`Total Payments: ${formatCurrency(totalPaymentsSum)}`, 14, 36);
                        doc.text(`Total Earnings: ${formatCurrency(totalEarningsSum)}`, 14, 42);

                        // Prepare data for table
                        const tableData = teachersData.map(teacher => [
                            teacher.teacher_id || '',
                            teacher.teacher_name || '',
                            formatPercentage(teacher.percentage),
                            formatCurrency(teacher.total_payments_this_month),
                            formatCurrency(teacher.teacher_earning)
                        ]);

                        // Add table
                        doc.autoTable({
                            head: [['ID', 'Name', 'Percentage', 'Total Payments', 'Payment Due']],
                            body: tableData,
                            startY: 50,
                            styles: { fontSize: 8 },
                            headStyles: { fillColor: [0, 123, 255] },
                            margin: { top: 30 }
                        });

                        // Generate filename with timestamp
                        const timestamp = new Date().toISOString().split('T')[0];
                        const filename = `teacher_payments_${timestamp}.pdf`;

                        // Save PDF
                        doc.save(filename);

                        showAlert('PDF file exported successfully!', 'success');
                    } catch (error) {
                        console.error('Error exporting to PDF:', error);
                        showAlert('Failed to export PDF file. Please try again.', 'danger');
                    }
                });
            }

            // Setup search functionality
            function setupSearch() {
                if (!searchInput) return;

                let searchTimeout;
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.trim();
                        let filteredData = teachersData;

                        if (searchTerm) {
                            filteredData = filterTable(searchTerm, teachersData);
                        }

                        renderTable(filteredData);
                        updateSummary(filteredData);
                    }, CONFIG.debounceDelay);
                });
            }

            // Setup clear search
            function setupClearSearch() {
                if (!clearSearch) return;

                clearSearch.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        renderTable(teachersData);
                        updateSummary(teachersData);
                    }
                });
            }

            // Setup refresh button
            function setupRefreshButton() {
                if (!refreshBtn) return;

                refreshBtn.addEventListener('click', function () {
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Refreshing...';

                    Promise.all([
                        fetchTeacherPayments(),
                        loadPaymentReasons()
                    ]).finally(() => {
                        setTimeout(() => {
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                        }, 1000);
                    });
                });
            }

            // Setup table sorting
            function setupTableSorting() {
                const sortableHeaders = document.querySelectorAll('th[data-sort]');
                if (!sortableHeaders.length) return;

                sortableHeaders.forEach(th => {
                    th.addEventListener('click', function () {
                        const column = this.getAttribute('data-sort');
                        if (!column) return;

                        // Update sort direction
                        if (currentSort.column === column) {
                            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                        } else {
                            currentSort.column = column;
                            currentSort.direction = 'asc';
                        }

                        // Update sort icons
                        document.querySelectorAll('th[data-sort] i').forEach(icon => {
                            icon.className = 'fas fa-sort';
                        });

                        const sortIcon = this.querySelector('i');
                        if (sortIcon) {
                            sortIcon.className = currentSort.direction === 'asc'
                                ? 'fas fa-sort-up'
                                : 'fas fa-sort-down';
                        }

                        // Sort and render data
                        const sortedData = sortTable(column, teachersData);
                        renderTable(sortedData);
                    });
                });
            }

            // Add this function in your script section
            function isLastFiveDaysOfMonth() {
                const today = new Date();
                const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
                const currentDay = today.getDate();

                return currentDay > (lastDayOfMonth - 25);
            }

            // Initialize everything
            function init() {
                console.log('Initializing Teacher Payments module...');

                // Check if Bootstrap is loaded
                if (typeof bootstrap === 'undefined') {
                    console.error('Bootstrap is not loaded. Please check the Bootstrap CDN.');
                    showAlert('Required components failed to load. Please refresh the page.', 'danger');
                    return;
                }

                // Check for required elements
                const requiredElements = [
                    'teacherTableBody', 'loadingSpinner', 'emptyState',
                    'summaryTotalPayments', 'summaryTotalEarnings', 'summaryTotalTeachers'
                ];

                const missingElements = requiredElements.filter(id => !document.getElementById(id));
                if (missingElements.length > 0) {
                    console.error('Missing required elements:', missingElements);
                    showAlert('Page failed to load properly. Please refresh.', 'danger');
                    return;
                }

                // Setup all event listeners
                try {
                    setupAdvancePaymentForm();
                    setupExportExcel();
                    setupExportPdf();
                    setupSearch();
                    setupClearSearch();
                    setupRefreshButton();
                    setupTableSorting();

                    // Load initial data
                    Promise.all([
                        fetchTeacherPayments(),
                        loadPaymentReasons()
                    ]).catch(error => {
                        console.error('Error during initialization:', error);
                        showAlert('Failed to initialize page. Please refresh.', 'danger');
                    });

                    console.log('Teacher Payments module initialized successfully');
                } catch (error) {
                    console.error('Error during initialization:', error);
                    showAlert('Failed to initialize page. Please refresh.', 'danger');
                }
            }

            // Start when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            // Global error handling
            window.addEventListener('error', function (event) {
                console.error('Global error:', event.error);
            });

            window.addEventListener('unhandledrejection', function (event) {
                console.error('Unhandled promise rejection:', event.reason);
                showAlert('An unexpected error occurred. Please try again.', 'danger');
            });

        })();
    </script>
@endpush