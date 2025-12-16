@extends('layouts.app')

@section('title', 'Institute Expenses')
@section('page-title', 'Institute Expenses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('institute_payment.index') }}">Institute Income</a></li>
    <li class="breadcrumb-item active">Institute Expenses</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <div class="card">
            <!-- Header -->
            <div class="card-header bg-danger text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>Institute Expenses
                    </h6>
                    <button type="button" class="btn btn-light btn-sm" id="backToInstituteBtn">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </button>
                </div>
            </div>

            <div class="card-body p-3">
                <!-- Month Selector -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light py-1">
                                <i class="fas fa-calendar-alt text-danger"></i>
                            </span>
                            <input type="month" class="form-control form-control-sm py-1" id="monthSelector"
                                value="{{ date('Y-m') }}">
                            <button class="btn btn-danger btn-sm py-1 px-3" type="button" id="loadDataBtn">
                                <i class="fas fa-sync me-1"></i>Load
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary btn-sm px-3 d-none" id="addExpenseBtn">
                            <i class="fas fa-plus me-1"></i>Add Expense
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-3 g-2">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="icon-wrapper bg-white bg-opacity-25 p-2 rounded-circle">
                                            <i class="fas fa-money-bill-wave text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-white-50 d-block">Gross Income</small>
                                        <div class="h6 mb-0 fw-bold" id="grossIncome">Rs 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="icon-wrapper bg-white bg-opacity-25 p-2 rounded-circle">
                                            <i class="fas fa-calendar text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-white-50 d-block">Selected Month</small>
                                        <div class="h6 mb-0 fw-bold" id="selectedMonthDisplay">{{ date('F Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="icon-wrapper bg-white bg-opacity-25 p-2 rounded-circle">
                                            <i class="fas fa-calculator text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-white-50 d-block">Total Expenses</small>
                                        <div class="h6 mb-0 fw-bold" id="totalExpenses">Rs 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="icon-wrapper bg-white bg-opacity-25 p-2 rounded-circle">
                                            <i class="fas fa-balance-scale text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-white-50 d-block">Net Total</small>
                                        <div class="h6 mb-0 fw-bold" id="netTotal">Rs 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expenses Table -->
                <div class="card border">
                    <div class="card-header bg-light py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-table me-1"></i>Expense Records
                            </h6>
                            <small class="text-muted" id="totalEntries">0 entries</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="py-2 px-3">#</th>
                                        <th width="15%" class="py-2 px-3">Code</th>
                                        <th width="15%" class="py-2 px-3">Date</th>
                                        <th width="35%" class="py-2 px-3">Reason</th>
                                        <th width="20%" class="py-2 px-3">Amount</th>
                                        <th width="10%" class="py-2 px-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="expensesBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="spinner-border spinner-border-sm text-danger" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <small class="text-muted ms-2">Loading...</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="noDataMessage" class="text-center d-none p-4">
                            <div class="alert alert-info py-2">
                                <i class="fas fa-info-circle me-1"></i>
                                <small>No expense records found for this month</small>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm mt-1 d-none" id="addFirstRecordBtn">
                                <i class="fas fa-plus me-1"></i>Add First Expense
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <!-- Add Expense Modal -->
    <div class="modal fade" id="expenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title mb-0">Add Expense</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="expenseForm">
                    <div class="modal-body p-3">
                        <!-- Net Total Warning -->
                        <div class="alert alert-info alert-sm py-1 mb-2 d-none" id="netTotalWarning">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Available Net Total: <strong id="availableNetTotal">Rs 0</strong></small>
                        </div>

                        <div class="mb-2">
                            <label for="reason_code" class="form-label small">Reason Code <span
                                    class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="reason_code" name="reason_code" required>
                                <option value="">Select reason code</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                            <div class="invalid-feedback small">Required</div>
                        </div>

                        <div class="mb-2">
                            <label for="reason" class="form-label small">Reason <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="reason" name="reason"
                                placeholder="Reason will auto-fill from code" readonly>
                            <div class="invalid-feedback small">Required</div>
                        </div>

                        <div class="mb-2">
                            <label for="payment" class="form-label small">Amount <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rs</span>
                                <input type="number" class="form-control" id="payment" name="payment" min="1" step="0.01"
                                    placeholder="0.00" required>
                                <div class="invalid-feedback small" id="paymentValidation">
                                    Required
                                </div>
                            </div>
                            <small class="text-muted" id="remainingBalanceText"></small>
                        </div>

                        <div class="mb-2">
                            <label for="date" class="form-label small">Date <span class="text-danger">*</span></label>
                            <input type="date" disabled class="form-control form-control-sm" id="date" name="date"
                                value="{{ date('Y-m-d') }}" required>
                            <div class="invalid-feedback small">Required</div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveBtn" disabled>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title mb-0">Confirm Delete</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                        <p class="mb-1 small">Delete this expense record?</p>
                        <p class="text-muted small">Cannot be undone</p>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .icon-wrapper {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-body {
            padding: 0.5rem !important;
        }

        .table th,
        .table td {
            padding: 0.5rem !important;
        }

        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }

        .deleted-record {
            text-decoration: line-through;
            opacity: 0.6;
        }

        .alert-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .alert-sm i {
            font-size: 0.8rem;
        }

        #remainingBalanceText {
            display: block;
            margin-top: 2px;
            font-size: 0.8rem;
        }

        .text-success {
            color: #198754 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentMonth = document.getElementById('monthSelector').value;
            let deleteRecordId = null;
            let isLoading = false;
            let currentSummary = null;
            let reasonCodes = [];
            let currentNetTotal = 0;

            // Initialize
            formatMonthDisplay(currentMonth);
            loadExpenses(currentMonth);
            loadReasonCodes();
            checkAddButtonVisibility(currentMonth);
            updateNetTotalWarning();

            // Event Listeners
            document.getElementById('monthSelector').addEventListener('change', function () {
                currentMonth = this.value;
                formatMonthDisplay(currentMonth);
                checkAddButtonVisibility(currentMonth);
                reloadPageData();
            });

            document.getElementById('loadDataBtn').addEventListener('click', function () {
                currentMonth = document.getElementById('monthSelector').value;
                formatMonthDisplay(currentMonth);
                checkAddButtonVisibility(currentMonth);
                reloadPageData();
            });

            document.getElementById('backToInstituteBtn').addEventListener('click', function () {
                window.location.href = "{{ route('institute_payment.index') }}";
            });

            document.getElementById('addExpenseBtn').addEventListener('click', openAddModal);
            document.getElementById('addFirstRecordBtn').addEventListener('click', openAddModal);

            document.getElementById('expenseForm').addEventListener('submit', function (e) {
                e.preventDefault();
                saveExpense();
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (deleteRecordId) deleteExpense(deleteRecordId);
            });

            document.getElementById('reason_code').addEventListener('change', function () {
                const selectedCode = this.value;
                const selectedReason = reasonCodes.find(rc => rc.reason_code === selectedCode);
                if (selectedReason) {
                    document.getElementById('reason').value = selectedReason.reason;
                } else {
                    document.getElementById('reason').value = '';
                }
            });

            // Payment amount validation against Net Total
            document.getElementById('payment').addEventListener('input', validatePaymentAmount);
            document.getElementById('payment').addEventListener('change', validatePaymentAmount);

            // Modal Functions
            function openAddModal() {
                document.getElementById('expenseForm').reset();
                document.getElementById('reason_code').value = '';
                document.getElementById('reason').value = '';
                document.getElementById('payment').value = '';
                document.getElementById('date').value = currentMonth + '-01';
                document.getElementById('saveBtn').disabled = true;

                // Show/hide net total warning
                updateNetTotalWarning();

                // Clear validation
                const invalidElements = document.querySelectorAll('.is-invalid');
                invalidElements.forEach(el => el.classList.remove('is-invalid'));

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
                modal.show();

                // Focus on first input
                setTimeout(() => {
                    document.getElementById('reason_code').focus();
                }, 300);
            }

            function validatePaymentAmount() {
                const paymentInput = document.getElementById('payment');
                const paymentValue = parseFloat(paymentInput.value) || 0;
                const saveBtn = document.getElementById('saveBtn');
                const validationDiv = document.getElementById('paymentValidation');
                const remainingBalanceText = document.getElementById('remainingBalanceText');

                // Hide previous validation
                paymentInput.classList.remove('is-invalid');
                validationDiv.textContent = 'Required';

                // Get current net total
                const currentNetTotalValue = parseFloat(currentNetTotal) || 0;

                if (paymentValue <= 0) {
                    paymentInput.classList.add('is-invalid');
                    validationDiv.textContent = 'Amount must be greater than 0';
                    saveBtn.disabled = true;
                    remainingBalanceText.textContent = '';
                    return false;
                }

                // Check if payment exceeds net total
                if (paymentValue > currentNetTotalValue) {
                    paymentInput.classList.add('is-invalid');
                    validationDiv.textContent = `Amount (Rs ${formatNumber(paymentValue)}) exceeds available Net Total (Rs ${formatNumber(currentNetTotalValue)})`;
                    saveBtn.disabled = true;

                    const difference = paymentValue - currentNetTotalValue;
                    remainingBalanceText.innerHTML = `<span class="text-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Exceeds by Rs ${formatNumber(difference)}
                    </span>`;
                    return false;
                } else {
                    const remaining = currentNetTotalValue - paymentValue;
                    remainingBalanceText.innerHTML = `<span class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        Remaining balance: Rs ${formatNumber(remaining)}
                    </span>`;

                    // Enable save button if other required fields are filled
                    const reasonCode = document.getElementById('reason_code').value;
                    const date = document.getElementById('date').value;
                    saveBtn.disabled = !(reasonCode && date && paymentValue > 0);
                    return true;
                }
            }

            function updateNetTotalWarning() {
                const netTotalWarning = document.getElementById('netTotalWarning');
                const availableNetTotal = document.getElementById('availableNetTotal');

                if (currentNetTotal > 0) {
                    netTotalWarning.classList.remove('d-none');
                    availableNetTotal.textContent = 'Rs ' + formatNumber(currentNetTotal);
                } else {
                    netTotalWarning.classList.add('d-none');
                }
            }

            function openDeleteModal(id) {
                deleteRecordId = id;
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            }

            // Check if add button should be visible
            function checkAddButtonVisibility(month) {
                const currentDate = new Date();
                const currentYearMonth = currentDate.getFullYear() + '-' +
                    String(currentDate.getMonth() + 1).padStart(2, '0');

                const addBtn = document.getElementById('addExpenseBtn');
                const addFirstBtn = document.getElementById('addFirstRecordBtn');

                if (month === currentYearMonth) {
                    addBtn.classList.remove('d-none');
                    addFirstBtn.classList.remove('d-none');
                } else {
                    addBtn.classList.add('d-none');
                    addFirstBtn.classList.add('d-none');
                }
            }

            // API Functions
            function loadExpenses(month) {
                if (isLoading) return;
                isLoading = true;
                showLoadingState();

                fetch(`/api/institute-payments/institute-expenses/${month}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success') {
                            displayExpenses(response.expense_details);
                            updateSummaryCards(response.summary);
                            hideNoDataMessage();

                            // Store current net total for validation
                            currentNetTotal = response.summary.net_total || 0;
                            updateNetTotalWarning();
                        } else {
                            showNoDataMessage();
                            currentNetTotal = 0;
                            updateNetTotalWarning();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading expenses:', error);
                        showNoDataMessage();
                        showNotification('Error loading expenses', 'error');
                        currentNetTotal = 0;
                        updateNetTotalWarning();
                    })
                    .finally(() => {
                        isLoading = false;
                    });
            }

            function loadReasonCodes() {
                fetch('/api/payment-reason/dropdown', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                    .then(response => response.json())
                    .then(response => {
                        if (Array.isArray(response)) {
                            reasonCodes = response;
                            populateReasonCodes(response);
                        } else if (response.data && Array.isArray(response.data)) {
                            reasonCodes = response.data;
                            populateReasonCodes(response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading reason codes:', error);
                    });
            }

            function populateReasonCodes(data) {
                const select = document.getElementById('reason_code');
                select.innerHTML = '<option value="">Select reason code</option>';

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.reason_code;
                    option.textContent = `${item.reason_code} - ${item.reason}`;
                    option.dataset.reason = item.reason;
                    select.appendChild(option);
                });
            }

            function saveExpense() {
                if (isLoading) return;

                // Validate payment amount first
                if (!validatePaymentAmount()) {
                    showNotification('Please check the payment amount', 'error');
                    return;
                }

                const formData = {
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                    reason_code: document.getElementById('reason_code').value,
                    reason: document.getElementById('reason').value,
                    payment: document.getElementById('payment').value,
                    date: document.getElementById('date').value,
                    status: 1
                };

                // Validation
                let isValid = true;
                if (!formData.reason_code) {
                    document.getElementById('reason_code').classList.add('is-invalid');
                    isValid = false;
                }
                if (!formData.payment || formData.payment <= 0) {
                    document.getElementById('payment').classList.add('is-invalid');
                    isValid = false;
                }
                if (!formData.date) {
                    document.getElementById('date').classList.add('is-invalid');
                    isValid = false;
                }
                if (!isValid) {
                    showNotification('Please fill all required fields correctly', 'error');
                    return;
                }

                isLoading = true;
                const saveBtn = document.getElementById('saveBtn');
                saveBtn.disabled = true;
                saveBtn.querySelector('.spinner-border').classList.remove('d-none');

                fetch('/api/institute-payments/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData._token
                    },
                    body: JSON.stringify(formData)
                })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success') {
                            bootstrap.Modal.getInstance(document.getElementById('expenseModal')).hide();
                            showNotification('Expense added successfully', 'success');

                            // Auto-reload after successful save
                            setTimeout(() => {
                                reloadPageData();
                            }, 500);
                        } else {
                            showNotification(response.message || 'Error saving expense', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error saving expense:', error);
                        showNotification('Error saving expense', 'error');
                    })
                    .finally(() => {
                        isLoading = false;
                        saveBtn.disabled = false;
                        saveBtn.querySelector('.spinner-border').classList.add('d-none');
                    });
            }

            function deleteExpense(id) {
                if (isLoading) return;

                isLoading = true;
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                confirmBtn.disabled = true;
                confirmBtn.querySelector('.spinner-border').classList.remove('d-none');

                fetch(`/api/institute-payments/destroy/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'success') {
                            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                            showNotification('Expense deleted successfully', 'success');

                            // Auto-reload after successful delete
                            setTimeout(() => {
                                reloadPageData();
                            }, 500);
                        } else {
                            showNotification(response.message || 'Error deleting expense', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting expense:', error);
                        showNotification('Error deleting expense', 'error');
                    })
                    .finally(() => {
                        isLoading = false;
                        deleteRecordId = null;
                        confirmBtn.disabled = false;
                        confirmBtn.querySelector('.spinner-border').classList.add('d-none');
                    });
            }

            // Auto-reload function
            function reloadPageData() {
                loadExpenses(currentMonth);
                showNotification('Refreshing data...', 'info');
            }

            // Display Functions
            function displayExpenses(expenseDetails) {
                if (!expenseDetails || expenseDetails.length === 0) {
                    showNoDataMessage();
                    return;
                }

                let html = '';
                const currentDate = new Date();
                const currentYearMonth = currentDate.getFullYear() + '-' +
                    String(currentDate.getMonth() + 1).padStart(2, '0');

                expenseDetails.forEach((item, index) => {
                    const date = new Date(item.date);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });

                    // Get record month (YYYY-MM format)
                    const recordYear = date.getFullYear();
                    const recordMonth = String(date.getMonth() + 1).padStart(2, '0');
                    const recordYearMonth = `${recordYear}-${recordMonth}`;

                    // Check if record is from the CURRENT actual month (not selected month) and status is active
                    const canDelete = (recordYearMonth === currentYearMonth) && (item.status === 1);
                    const isDeleted = item.status === 0;

                    html += `
                        <tr class="${isDeleted ? 'deleted-record' : ''}">
                            <td class="px-3">${index + 1}</td>
                            <td class="px-3"><small><strong>${item.reason_code || 'N/A'}</strong></small></td>
                            <td class="px-3"><small>${formattedDate}</small></td>
                            <td class="px-3"><small>${item.reason || 'No reason'}</small></td>
                            <td class="px-3">
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                    Rs ${formatNumber(item.amount)}
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                ${canDelete ? `
                                <button class="btn btn-outline-danger btn-sm delete-btn" 
                                        data-id="${item.id}"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                ` : `
                                <span class="text-muted" title="${isDeleted ? 'Already deleted' : 'Cannot delete - Record is not from current month'}">
                                    <i class="fas ${isDeleted ? 'fa-ban' : 'fa-lock'}"></i>
                                </span>
                                `}
                            </td>
                        </tr>
                    `;
                });

                document.getElementById('expensesBody').innerHTML = html;
                document.getElementById('totalEntries').textContent = `${expenseDetails.length} entries`;

                // Reattach event listeners for delete buttons
                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const id = this.getAttribute('data-id');
                        openDeleteModal(id);
                    });
                });
            }

            function updateSummaryCards(summary) {
                currentSummary = summary;

                document.getElementById('grossIncome').textContent = 'Rs ' + formatNumber(summary.gross_income || 0);
                document.getElementById('totalExpenses').textContent = 'Rs ' + formatNumber(summary.total_expenses || 0);
                document.getElementById('netTotal').textContent = 'Rs ' + formatNumber(summary.net_total || 0);
            }

            function formatMonthDisplay(month) {
                const date = new Date(month + '-01');
                const formatted = date.toLocaleDateString('en-US', {
                    month: 'short',
                    year: 'numeric'
                });
                document.getElementById('selectedMonthDisplay').textContent = formatted;
            }

            function showLoadingState() {
                document.getElementById('expensesBody').innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-danger"></div>
                            <small class="text-muted ms-2">Loading...</small>
                        </td>
                    </tr>
                `;
                document.getElementById('totalEntries').textContent = 'Loading...';
            }

            function showNoDataMessage() {
                document.getElementById('expensesBody').innerHTML = '';
                document.getElementById('noDataMessage').classList.remove('d-none');
                document.getElementById('totalEntries').textContent = '0 entries';
                updateSummaryCards({
                    gross_income: 0,
                    total_expenses: 0,
                    net_total: 0
                });
            }

            function hideNoDataMessage() {
                document.getElementById('noDataMessage').classList.add('d-none');
            }

            // Utility Functions
            function formatNumber(num) {
                const number = Number(num) || 0;
                return number.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            function showNotification(message, type = 'info') {
                // Remove existing notifications
                const existingAlerts = document.querySelectorAll('.alert.position-fixed');
                existingAlerts.forEach(alert => alert.remove());

                const alertClass = type === 'success' ? 'alert-success' :
                    type === 'error' ? 'alert-danger' :
                        type === 'warning' ? 'alert-warning' : 'alert-info';

                const notification = document.createElement('div');
                notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
                notification.innerHTML = `
                    <i class="fas ${type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-triangle' :
                            'fa-info-circle'} me-2"></i>
                    <small>${message}</small>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(notification);

                // Auto remove after 3 seconds
                setTimeout(() => {
                    if (notification.parentNode) {
                        const bsAlert = new bootstrap.Alert(notification);
                        bsAlert.close();
                    }
                }, 3000);
            }

            // Input validation for other fields
            document.getElementById('reason_code').addEventListener('change', function () {
                this.classList.remove('is-invalid');
                validatePaymentAmount(); // Re-check to enable/disable save button
            });

            document.getElementById('date').addEventListener('change', function () {
                this.classList.remove('is-invalid');
                validatePaymentAmount(); // Re-check to enable/disable save button
            });
        });
    </script>
@endpush