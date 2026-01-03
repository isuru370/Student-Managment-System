@extends('layouts.app')

@section('title', 'Teacher Expenses Summary')
@section('page-title', 'Teacher Expenses Summary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher_payment.index') }}">Teacher Payments</a></li>
    <li class="breadcrumb-item active">Teacher Expenses</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="container-fluid">
        <!-- Month/Year Selector -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body py-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label mb-0 small">Month</label>
                                <select class="form-select form-select-sm" id="monthSelect" name="month">
                                    @php
                                        $currentMonth = date('m'); // Current month
                                        $defaultMonth = $currentMonth; // Default to current month
                                    @endphp
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                                {{ $i == $defaultMonth ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-0 small">Year</label>
                                <select class="form-select form-select-sm" id="yearSelect" name="year">
                                    @php
                                        $currentYear = date('Y'); // Current year
                                        $defaultYear = $currentYear; // Default to current year
                                    @endphp
                                    @for($year = date('Y') + 1; $year >= 2020; $year--)
                                        <option value="{{ $year }}" {{ $year == $defaultYear ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-0 small">Search Teacher</label>
                                <input type="text" class="form-control form-control-sm" id="teacherSearch" 
                                       placeholder="Search by teacher name or ID...">
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="badge bg-info text-dark fs-6 px-3 py-2" id="selectedMonthYear">
                                    {{ date('F Y') }} <!-- Current month and year -->
                                </div>
                                <small class="text-muted d-block mt-1">Current month expenses</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                        <h6 class="card-title small mb-1">Total Active Expenses</h6>
                        <h3 class="fw-bold mb-0" id="totalActiveAmount">LKR 0.00</h3>
                        <small id="teacherCount">0 teachers</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body py-3">
                        <h6 class="card-title small mb-1">Active Teachers</h6>
                        <h3 class="fw-bold mb-0" id="activeTeacherCount">0</h3>
                        <small>With active expenses</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body py-3">
                        <h6 class="card-title small mb-1">Average Per Teacher</h6>
                        <h3 class="fw-bold mb-0" id="averagePerTeacher">LKR 0.00</h3>
                        <small>Active expenses only</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Summary Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-users me-1"></i> Teacher Expenses Summary - <span id="currentMonthYearDisplay">{{ date('F Y') }}</span>
                            </h6>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-light px-2 py-1" id="exportExcelBtn">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                                <button class="btn btn-sm btn-light px-2 py-1" id="exportPdfBtn">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </button>
                                <button class="btn btn-sm btn-light px-2 py-1" id="refreshBtn">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 small">Loading teacher expenses summary...</p>
                        </div>

                        <!-- Empty State -->
                        <div id="emptyState" class="text-center d-none">
                            <div class="alert alert-info py-2">
                                <h6 class="mb-1"><i class="fas fa-info-circle"></i> No Active Expenses</h6>
                                <p class="mb-0 small">No active expense records found for {{ date('F Y') }}.</p>
                            </div>
                        </div>

                        <!-- Table Container -->
                        <div class="table-responsive d-none" id="tableContainer">
                            <table class="table table-bordered table-hover table-sm" id="summaryTable">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="small">#</th>
                                        <th class="small">Teacher ID</th>
                                        <th class="small">Teacher Name</th>
                                        <th class="small">Total Active Amount</th>
                                        <th class="small">Active Expenses</th>
                                        <th class="small text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="summaryTableBody">
                                    <!-- Dynamic data -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Details Modal -->
    <div class="modal fade" id="teacherDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-graduate me-2"></i>
                        <span id="modalTeacherName">Teacher Details</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Teacher Info -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <label class="form-label text-muted small mb-1">Teacher ID</label>
                                                <p class="fw-bold mb-0" id="modalTeacherId">-</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <label class="form-label text-muted small mb-1">Email</label>
                                                <p class="fw-bold mb-0" id="modalTeacherEmail">-</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <label class="form-label text-muted small mb-1">Total Active</label>
                                                <p class="fw-bold text-primary mb-0" id="modalTotalActive">LKR 0.00</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <label class="form-label text-muted small mb-1">Total Inactive</label>
                                                <p class="fw-bold text-warning mb-0" id="modalTotalInactive">LKR 0.00</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- All Expenses Table -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white py-2">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-file-invoice-dollar me-1"></i> All Expenses - <span id="modalMonthYear">{{ date('F Y') }}</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="teacherExpensesTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="small">Date</th>
                                                    <th class="small">Amount</th>
                                                    <th class="small">Reason</th>
                                                    <th class="small">Reason Code</th>
                                                    <th class="small">Status</th>
                                                    <th class="small">Entered By</th>
                                                    <th class="small text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="teacherExpensesTableBody">
                                                <!-- Dynamic data -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center d-none" id="noExpensesMessage">
                                        <div class="alert alert-light mt-3">
                                            <i class="fas fa-info-circle me-1"></i> No expenses found for this teacher
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal fade" id="statusChangeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="statusChangeReason" class="form-label">Reason *</label>
                        <textarea class="form-control" id="statusChangeReason" rows="3" 
                                  placeholder="Enter reason for status change..." required></textarea>
                        <div class="form-text">Minimum 3 characters required</div>
                    </div>
                    <input type="hidden" id="statusChangePaymentId">
                    <input type="hidden" id="statusChangeCurrentStatus">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmStatusChange()">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            box-shadow: 0 0.1rem 0.2rem rgba(0, 0, 0, 0.05);
        }
        
        .table th, .table td {
            padding: 0.3rem 0.5rem;
            vertical-align: middle;
            font-size: 0.85rem;
        }
        
        .badge {
            font-size: 0.75em;
            padding: 0.25em 0.5em;
        }
        
        .card-header {
            padding: 0.5rem 0.75rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .view-btn {
            min-width: 70px;
        }
        
        .modal-xl {
            max-width: 1200px;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.75rem;
            }
            
            .view-btn {
                min-width: 60px;
                font-size: 0.75rem;
            }
            
            .modal-xl {
                max-width: 95%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
    (function () {
        'use strict';

        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // DOM Elements
        const monthSelect = document.getElementById('monthSelect');
        const yearSelect = document.getElementById('yearSelect');
        const teacherSearch = document.getElementById('teacherSearch');
        const selectedMonthYear = document.getElementById('selectedMonthYear');
        const currentMonthYearDisplay = document.getElementById('currentMonthYearDisplay');
        const totalActiveAmount = document.getElementById('totalActiveAmount');
        const teacherCount = document.getElementById('teacherCount');
        const activeTeacherCount = document.getElementById('activeTeacherCount');
        const averagePerTeacher = document.getElementById('averagePerTeacher');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const emptyState = document.getElementById('emptyState');
        const tableContainer = document.getElementById('tableContainer');
        const summaryTableBody = document.getElementById('summaryTableBody');
        const exportExcelBtn = document.getElementById('exportExcelBtn');
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        const refreshBtn = document.getElementById('refreshBtn');

        // Modal elements
        const modalTeacherName = document.getElementById('modalTeacherName');
        const modalTeacherId = document.getElementById('modalTeacherId');
        const modalTeacherEmail = document.getElementById('modalTeacherEmail');
        const modalTotalActive = document.getElementById('modalTotalActive');
        const modalTotalInactive = document.getElementById('modalTotalInactive');
        const modalMonthYear = document.getElementById('modalMonthYear');
        const teacherExpensesTableBody = document.getElementById('teacherExpensesTableBody');
        const noExpensesMessage = document.getElementById('noExpensesMessage');

        // Global variables
        let currentMonth = '';
        let currentYear = '';
        let teacherSummaryData = [];
        let filteredSummaryData = [];
        let currentTeacherDetails = null;

        // Helper functions
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-LK', {
                style: 'currency',
                currency: 'LKR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount || 0);
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            } catch (e) {
                return dateString;
            }
        }

        function formatDateTime(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                return dateString;
            }
        }

        function getMonthName(monthNumber) {
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            const monthIndex = parseInt(monthNumber) - 1;
            return months[monthIndex] || 'Unknown';
        }

        function updateSelectedMonthYear() {
            const month = monthSelect.value;
            const year = yearSelect.value;
            const monthYear = `${getMonthName(month)} ${year}`;
            
            if (selectedMonthYear) {
                selectedMonthYear.textContent = monthYear;
            }
            
            if (currentMonthYearDisplay) {
                currentMonthYearDisplay.textContent = monthYear;
            }
            
            if (modalMonthYear) {
                modalMonthYear.textContent = monthYear;
            }
            
            currentMonth = month;
            currentYear = year;
        }

        // UI Functions
        function showLoading(show) {
            if (loadingSpinner) {
                if (show) {
                    loadingSpinner.classList.remove('d-none');
                } else {
                    loadingSpinner.classList.add('d-none');
                }
            }
        }

        function showTable(show) {
            if (tableContainer) {
                if (show) {
                    tableContainer.classList.remove('d-none');
                } else {
                    tableContainer.classList.add('d-none');
                }
            }
        }

        function showEmptyState(show) {
            if (emptyState) {
                if (show) {
                    emptyState.classList.remove('d-none');
                } else {
                    emptyState.classList.add('d-none');
                }
            }
        }

        // Process API data to get teacher summaries
        function processTeacherSummary(expensesData) {
            const teacherMap = new Map();
            
            // Group expenses by teacher and calculate active amounts only
            expensesData.forEach(expense => {
                const teacherId = expense.teacher_id;
                const teacher = expense.teacher || {};
                
                if (!teacherMap.has(teacherId)) {
                    teacherMap.set(teacherId, {
                        teacher_id: teacherId,
                        teacher: teacher,
                        active_amount: 0,
                        active_count: 0,
                        all_expenses: [],
                        inactive_amount: 0,
                        inactive_count: 0
                    });
                }
                
                const teacherData = teacherMap.get(teacherId);
                teacherData.all_expenses.push(expense);
                
                if (expense.status === true) {
                    teacherData.active_amount += parseFloat(expense.payment) || 0;
                    teacherData.active_count += 1;
                } else {
                    teacherData.inactive_amount += parseFloat(expense.payment) || 0;
                    teacherData.inactive_count += 1;
                }
            });
            
            // Convert map to array and filter out teachers with no active expenses
            return Array.from(teacherMap.values())
                .filter(teacher => teacher.active_amount > 0)
                .sort((a, b) => b.active_amount - a.active_amount);
        }

        // Fetch expenses data and process
        async function fetchExpenses(month, year) {
            showLoading(true);
            showTable(false);
            showEmptyState(false);

            try {
                const url = `/api/teacher-payments/expenses/${year}-${month}`;
                console.log('Fetching expenses from:', url);
                
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Process data to get teacher summaries (active amounts only)
                    teacherSummaryData = processTeacherSummary(data.expenses || []);
                    filteredSummaryData = [...teacherSummaryData];
                    
                    renderTeacherSummary();
                    updateSummaryStats();
                } else {
                    throw new Error(data.message || 'Failed to load data');
                }
            } catch (error) {
                console.error('Error fetching expenses:', error);
                showEmptyState(true);
                teacherSummaryData = [];
                filteredSummaryData = [];
                renderTeacherSummary();
                updateSummaryStats();
            } finally {
                showLoading(false);
            }
        }

        // Update summary statistics
        function updateSummaryStats() {
            const totalActive = filteredSummaryData.reduce((sum, teacher) => sum + teacher.active_amount, 0);
            const teacherCountValue = filteredSummaryData.length;
            const average = teacherCountValue > 0 ? totalActive / teacherCountValue : 0;
            
            if (totalActiveAmount) {
                totalActiveAmount.textContent = formatCurrency(totalActive);
            }
            
            if (teacherCount) {
                teacherCount.textContent = `${teacherCountValue} teacher${teacherCountValue !== 1 ? 's' : ''}`;
            }
            
            if (activeTeacherCount) {
                activeTeacherCount.textContent = teacherCountValue;
            }
            
            if (averagePerTeacher) {
                averagePerTeacher.textContent = formatCurrency(average);
            }
        }

        // Render teacher summary table
        function renderTeacherSummary() {
            if (!summaryTableBody) return;

            summaryTableBody.innerHTML = '';

            if (filteredSummaryData.length === 0) {
                showEmptyState(true);
                showTable(false);
                return;
            }

            showEmptyState(false);
            showTable(true);

            filteredSummaryData.forEach((teacher, index) => {
                const row = document.createElement('tr');
                const teacherInfo = teacher.teacher || {};
                
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        <span class="badge bg-secondary">${teacherInfo.custom_id || 'N/A'}</span>
                    </td>
                    <td>
                        <div class="fw-bold small">${teacherInfo.fname || ''} ${teacherInfo.lname || ''}</div>
                        <small class="text-muted">ID: ${teacher.teacher_id}</small>
                    </td>
                    <td class="fw-bold text-primary">${formatCurrency(teacher.active_amount)}</td>
                    <td>
                        <span class="badge bg-success">${teacher.active_count} active</span>
                        ${teacher.inactive_count > 0 ? 
                            `<span class="badge bg-warning ms-1">${teacher.inactive_count} inactive</span>` : ''}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary view-btn"
                                onclick="showTeacherDetails(${teacher.teacher_id})"
                                title="View all expenses">
                            <i class="fas fa-eye me-1"></i> View
                        </button>
                    </td>
                `;
                
                summaryTableBody.appendChild(row);
            });
        }

        // Search functionality
        function filterTeacherSummary(searchTerm) {
            if (!searchTerm) {
                filteredSummaryData = [...teacherSummaryData];
            } else {
                const term = searchTerm.toLowerCase();
                filteredSummaryData = teacherSummaryData.filter(teacher => {
                    const teacherInfo = teacher.teacher || {};
                    const teacherName = `${teacherInfo.fname || ''} ${teacherInfo.lname || ''}`.toLowerCase();
                    const teacherId = (teacherInfo.custom_id || '').toLowerCase();
                    const teacherEmail = (teacherInfo.email || '').toLowerCase();
                    
                    return teacherName.includes(term) || 
                           teacherId.includes(term) || 
                           teacherEmail.includes(term);
                });
            }
            
            renderTeacherSummary();
            updateSummaryStats();
        }

        // Show teacher details modal
        window.showTeacherDetails = function(teacherId) {
            // Find the teacher data
            const teacherData = teacherSummaryData.find(t => t.teacher_id === teacherId);
            if (!teacherData) return;
            
            currentTeacherDetails = teacherData;
            const teacherInfo = teacherData.teacher || {};
            
            // Update modal header
            modalTeacherName.textContent = `${teacherInfo.fname || ''} ${teacherInfo.lname || ''}`;
            modalTeacherId.textContent = teacherInfo.custom_id || 'N/A';
            modalTeacherEmail.textContent = teacherInfo.email || 'N/A';
            modalTotalActive.textContent = formatCurrency(teacherData.active_amount);
            modalTotalInactive.textContent = formatCurrency(teacherData.inactive_amount);
            
            // Render teacher expenses table
            renderTeacherExpensesTable(teacherData.all_expenses);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('teacherDetailsModal'));
            modal.show();
        };

        // Render teacher expenses table in modal
        function renderTeacherExpensesTable(expenses) {
            if (!teacherExpensesTableBody) return;

            teacherExpensesTableBody.innerHTML = '';

            if (!expenses || expenses.length === 0) {
                noExpensesMessage.classList.remove('d-none');
                return;
            }

            noExpensesMessage.classList.add('d-none');

            expenses.forEach((expense, index) => {
                const row = document.createElement('tr');
                const user = expense.user || {};
                
                row.innerHTML = `
                    <td>${formatDateTime(expense.date)}</td>
                    <td class="${expense.status === true ? 'fw-bold text-primary' : 'text-muted'}">
                        ${formatCurrency(expense.payment)}
                    </td>
                    <td>
                        <div class="small">${expense.reason || 'No reason provided'}</div>
                    </td>
                    <td>
                        <span class="badge bg-info">${expense.reason_code || 'N/A'}</span>
                    </td>
                    <td>
                        ${expense.status === true 
                            ? '<span class="badge bg-success">Active</span>' 
                            : '<span class="badge bg-danger">Inactive</span>'}
                    </td>
                    <td>
                        <div class="small">${user.name || 'Unknown'}</div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm ${expense.status === true ? 'btn-warning' : 'btn-success'} action-btn"
                                onclick="showStatusChangeModal(${expense.id}, ${expense.status})"
                                title="${expense.status === true ? 'Deactivate' : 'Activate'} this payment">
                            <i class="fas ${expense.status === true ? 'fa-times' : 'fa-check'}"></i>
                        </button>
                    </td>
                `;
                
                teacherExpensesTableBody.appendChild(row);
            });
        }

        // Status change functions
        window.showStatusChangeModal = function(paymentId, currentStatus) {
            document.getElementById('statusChangePaymentId').value = paymentId;
            document.getElementById('statusChangeCurrentStatus').value = currentStatus;
            document.getElementById('statusChangeReason').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('statusChangeModal'));
            modal.show();
        };

        window.confirmStatusChange = async function() {
            const paymentId = document.getElementById('statusChangePaymentId').value;
            const currentStatus = document.getElementById('statusChangeCurrentStatus').value;
            const reason = document.getElementById('statusChangeReason').value.trim();
            
            if (!reason || reason.length < 3) {
                alert('Please enter a valid reason (minimum 3 characters)');
                return;
            }
            
            try {
                const response = await fetch(`/api/teacher-payments/${paymentId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reason })
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Show success message
                    alert(data.message);
                    
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('statusChangeModal')).hide();
                    
                    // Refresh data
                    fetchExpenses(currentMonth, currentYear);
                    
                    // If teacher details modal is open, refresh it too
                    if (currentTeacherDetails) {
                        const teacherModal = bootstrap.Modal.getInstance(document.getElementById('teacherDetailsModal'));
                        if (teacherModal) {
                            // Re-fetch expenses to get updated data
                            fetchExpenses(currentMonth, currentYear).then(() => {
                                // Find updated teacher data
                                const updatedTeacherData = teacherSummaryData.find(
                                    t => t.teacher_id === currentTeacherDetails.teacher_id
                                );
                                if (updatedTeacherData) {
                                    renderTeacherExpensesTable(updatedTeacherData.all_expenses);
                                }
                            });
                        }
                    }
                } else {
                    if (data.errors && data.errors.reason) {
                        alert(data.errors.reason[0]);
                    } else {
                        alert(data.message || 'Failed to change status');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to change payment status');
            }
        };

        // Export functions
        function setupExportButtons() {
            // Excel Export
            if (exportExcelBtn) {
                exportExcelBtn.addEventListener('click', function() {
                    if (filteredSummaryData.length === 0) {
                        alert('No data to export');
                        return;
                    }

                    try {
                        const exportData = filteredSummaryData.map((teacher, index) => {
                            const teacherInfo = teacher.teacher || {};
                            return {
                                'No': index + 1,
                                'Teacher ID': teacherInfo.custom_id || 'N/A',
                                'Teacher Name': `${teacherInfo.fname || ''} ${teacherInfo.lname || ''}`,
                                'Teacher Email': teacherInfo.email || '',
                                'Active Amount': teacher.active_amount,
                                'Active Expenses Count': teacher.active_count,
                                'Inactive Amount': teacher.inactive_amount,
                                'Inactive Expenses Count': teacher.inactive_count
                            };
                        });

                        const ws = XLSX.utils.json_to_sheet(exportData);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Teacher Expenses Summary');

                        const filename = `Teacher_Expenses_Summary_${getMonthName(currentMonth)}_${currentYear}.xlsx`;
                        XLSX.writeFile(wb, filename);
                    } catch (error) {
                        console.error('Error exporting to Excel:', error);
                        alert('Failed to export Excel file');
                    }
                });
            }

            // PDF Export
            if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', function() {
                    if (filteredSummaryData.length === 0) {
                        alert('No data to export');
                        return;
                    }

                    try {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('landscape');

                        // Title
                        doc.setFontSize(14);
                        doc.text(`Teacher Expenses Summary - ${getMonthName(currentMonth)} ${currentYear}`, 14, 10);
                        doc.setFontSize(10);
                        doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 16);

                        // Table data
                        const headers = [
                            'No', 'Teacher ID', 'Teacher Name', 'Active Amount', 'Active Count', 'Inactive Amount', 'Inactive Count'
                        ];

                        const tableData = filteredSummaryData.map((teacher, index) => {
                            const teacherInfo = teacher.teacher || {};
                            return [
                                index + 1,
                                teacherInfo.custom_id || 'N/A',
                                `${teacherInfo.fname || ''} ${teacherInfo.lname || ''}`,
                                formatCurrency(teacher.active_amount),
                                teacher.active_count,
                                formatCurrency(teacher.inactive_amount),
                                teacher.inactive_count
                            ];
                        });

                        // Add table
                        doc.autoTable({
                            head: [headers],
                            body: tableData,
                            startY: 20,
                            styles: { fontSize: 8 },
                            headStyles: { fillColor: [41, 128, 185] }
                        });

                        const filename = `Teacher_Expenses_Summary_${getMonthName(currentMonth)}_${currentYear}.pdf`;
                        doc.save(filename);
                    } catch (error) {
                        console.error('Error exporting to PDF:', error);
                        alert('Failed to export PDF file');
                    }
                });
            }
        }

        // Event Listeners
        function setupEventListeners() {
            // Month/Year change
            if (monthSelect) {
                monthSelect.addEventListener('change', function() {
                    updateSelectedMonthYear();
                    fetchExpenses(currentMonth, currentYear);
                });
            }

            if (yearSelect) {
                yearSelect.addEventListener('change', function() {
                    updateSelectedMonthYear();
                    fetchExpenses(currentMonth, currentYear);
                });
            }

            // Search
            if (teacherSearch) {
                let searchTimeout;
                teacherSearch.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        filterTeacherSummary(this.value.trim());
                    }, 300);
                });
            }

            // Refresh button
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    fetchExpenses(currentMonth, currentYear);
                });
            }

            // Export buttons
            setupExportButtons();
        }

        // Initialize
        function init() {
            // Set default to CURRENT month and year
            const now = new Date();
            currentMonth = (now.getMonth() + 1).toString().padStart(2, '0'); // Current month
            currentYear = now.getFullYear().toString(); // Current year
            
            if (monthSelect) monthSelect.value = currentMonth;
            if (yearSelect) yearSelect.value = currentYear;
            
            updateSelectedMonthYear();
            setupEventListeners();
            
            // Load initial data for current month
            fetchExpenses(currentMonth, currentYear);
        }

        // Start when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }

    })();
    </script>
@endpush