@extends('layouts.app')

@section('title', 'Teacher Income History')
@section('page-title', 'Teacher Income History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher_payment.index') }}">Teacher Payments</a></li>
    <li class="breadcrumb-item active">Teacher Income History</li>
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
                                        $currentMonth = date('m');
                                        // Default: previous month
                                        $defaultMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
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
                                        $currentYear = date('Y');
                                        $currentMonth = date('m');
                                        // If current month is January, default to previous year
                                        $defaultYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
                                    @endphp
                                    @for($year = date('Y'); $year >= 2020; $year--)
                                        <option value="{{ $year }}" {{ $year == $defaultYear ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="badge bg-info text-dark fs-6 px-3 py-2" id="selectedMonthYear">
                                    {{ date('F Y', strtotime('-1 month')) }}
                                </div>
                                <small class="text-muted d-block mt-1">Only previous months available</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Month Warning -->
        <div class="row mb-3 d-none" id="currentMonthWarning">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show py-2" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Current month data is not available</strong>
                            <p class="mb-0 small">Only previous months' payment history can be viewed. The system will automatically show the previous month's data.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Teacher Summary Section -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0" id="teacherNameTitle">
                                <i class="fas fa-user-graduate me-1"></i> Teacher's Income Summary
                            </h6>
                            <div class="badge bg-light text-dark" id="summaryMonthYear">
                                {{ date('F Y', strtotime('-1 month')) }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Teacher Information -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-header bg-primary text-white py-2">
                                        <h6 class="mb-0 small"><i class="fas fa-info-circle me-1"></i> Teacher Information</h6>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="mb-2">
                                            <label class="form-label text-muted small mb-1">Teacher ID</label>
                                            <p class="fw-bold mb-0" id="teacherId">-</p>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label text-muted small mb-1">Name</label>
                                            <p class="fw-bold mb-0" id="teacherName">-</p>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label text-muted small mb-1">Subject</label>
                                            <p class="fw-bold mb-0" id="subjectName">-</p>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label text-muted small mb-1">Payment Status</label>
                                            <br>
                                            <span class="badge bg-warning" id="salaryStatus">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Summary -->
                            <div class="col-md-8 mb-3">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white py-2">
                                        <h6 class="mb-0 small"><i class="fas fa-chart-bar me-1"></i> Financial Summary</h6>
                                    </div>
                                    <div class="card-body py-2">
                                        <!-- Quick Stats -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="card bg-light h-100">
                                                    <div class="card-body py-2">
                                                        <h6 class="card-title text-muted small mb-1">Total Collections</h6>
                                                        <h4 class="fw-bold text-primary mb-0" id="totalCollections">LKR 0.00</h4>
                                                        <small class="text-muted">From students</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-light h-100">
                                                    <div class="card-body py-2">
                                                        <h6 class="card-title text-muted small mb-1">Advance Payments</h6>
                                                        <h4 class="fw-bold text-warning mb-0" id="advancePayments">LKR 0.00</h4>
                                                        <small class="text-muted">Paid in advance</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Percentage Split -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-primary" id="teacherPercentageBar" style="width: 0%">
                                                        <span class="small fw-bold" id="teacherPercentageTextBar">Teacher: 0%</span>
                                                    </div>
                                                    <div class="progress-bar bg-secondary" id="institutionPercentageBar" style="width: 0%">
                                                        <span class="small fw-bold" id="institutionPercentageTextBar">Institution: 0%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Shares -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="card bg-primary text-white h-100">
                                                    <div class="card-body py-2">
                                                        <h6 class="card-title small mb-1">Teacher's Share</h6>
                                                        <h4 class="fw-bold mb-0" id="teacherShare">LKR 0.00</h4>
                                                        <small id="teacherPercentageText">0% of total</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-secondary text-white h-100">
                                                    <div class="card-body py-2">
                                                        <h6 class="card-title small mb-1">Institution's Share</h6>
                                                        <h4 class="fw-bold mb-0" id="institutionShare">LKR 0.00</h4>
                                                        <small id="institutionPercentageText">0% of total</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Net Payable -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card bg-warning">
                                                    <div class="card-body py-2 text-center">
                                                        <h6 class="card-title mb-1">
                                                            <i class="fas fa-money-bill-wave me-1"></i> Net Payable to Teacher
                                                        </h6>
                                                        <h3 class="fw-bold my-1" id="netPayable">LKR 0.00</h3>
                                                        <small class="text-muted">(Teacher's Share - Advance Payments)</small>
                                                        <div class="mt-2">
                                                            <button class="btn btn-sm btn-success px-3" id="payTeacherBtn" disabled>
                                                                <i class="fas fa-money-check-alt me-1"></i> Pay Teacher
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Breakdown -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark py-2">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chalkboard-teacher me-1"></i> Classes Breakdown
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row" id="classesCards">
                            <div class="col-12 text-center">
                                <div class="alert alert-light mb-0">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Loading class data...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Payment Table -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark text-white py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-table me-1"></i> Detailed Payment Records
                            </h6>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-light px-2 py-1" id="exportTableExcelBtn">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                                <button class="btn btn-sm btn-light px-2 py-1" id="exportTablePdfBtn">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <!-- Loading Spinner -->
                        <div id="tableLoadingSpinner" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 small">Loading payment data...</p>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive d-none" id="tableContainer">
                            <table class="table table-bordered table-hover table-sm" id="paymentTable">
                                <thead class="table-primary" id="paymentTableHeader">
                                    <!-- Dynamic header -->
                                </thead>
                                <tbody id="paymentTableBody">
                                    <!-- Dynamic data -->
                                </tbody>
                                <tfoot class="table-secondary" id="paymentTableFooter">
                                    <!-- Dynamic footer -->
                                </tfoot>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div id="tableEmptyState" class="text-center d-none">
                            <div class="alert alert-info py-2">
                                <h6 class="mb-1"><i class="fas fa-info-circle"></i> No Payment Data</h6>
                                <p class="mb-0 small">No payment records found for the selected month.</p>
                            </div>
                        </div>

                        <!-- Current Month Block -->
                        <div id="currentMonthBlock" class="text-center d-none">
                            <div class="alert alert-warning py-2">
                                <h6 class="mb-1"><i class="fas fa-calendar-times"></i> Current Month Not Available</h6>
                                <p class="mb-0 small">Current month's data cannot be viewed. Please select a previous month.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary Payments History -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-danger text-white py-2">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-history me-1"></i> Advance Payment History
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="table-responsive d-none" id="salaryTableContainer">
                            <table class="table table-hover table-sm" id="salaryPaymentsTable">
                                <thead class="table-danger">
                                    <tr>
                                        <th class="small">Date</th>
                                        <th class="small">Amount</th>
                                        <th class="small">Reason Code</th>
                                        <th class="small">Payment For</th>
                                        <th class="small">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="salaryPaymentsTableBody">
                                    <!-- Dynamic data -->
                                </tbody>
                            </table>
                        </div>
                        <div id="salaryEmptyState" class="text-center d-none">
                            <div class="alert alert-warning py-2">
                                <h6 class="mb-1"><i class="fas fa-info-circle"></i> No Advance Payments</h6>
                                <p class="mb-0 small">No Advance payments found for this teacher.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            box-shadow: 0 0.1rem 0.2rem rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.3rem 0.6rem rgba(0, 0, 0, 0.1);
        }
        
        .progress {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .progress-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }
        
        .badge {
            font-size: 0.75em;
            padding: 0.25em 0.5em;
        }
        
        .table th, .table td {
            padding: 0.3rem 0.5rem;
            vertical-align: middle;
            font-size: 0.85rem;
        }
        
        .table-primary th {
            background-color: #e3f2fd;
        }
        
        .card-header {
            padding: 0.5rem 0.75rem;
        }
        
        .card-body {
            padding: 0.75rem;
        }
        
        .form-select, .form-control {
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.75rem;
            }
            
            .card-body .row > div {
                margin-bottom: 0.5rem;
            }
            
            .progress-bar span {
                font-size: 0.7rem;
            }
        }
        
        .class-card {
            border-left: 3px solid #0d6efd;
            transition: transform 0.2s;
        }
        
        .class-card:hover {
            transform: translateY(-3px);
        }
        
        .student-progress .progress {
            height: 6px;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .small {
            font-size: 0.85rem;
        }
        
        .disabled-month {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .current-month-warning {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>
@endpush

@push('scripts')
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <!-- SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        (function () {
            'use strict';

            // CSRF Token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Global variables
            let teacherData = null;
            let allPayments = [];
            let allGrades = [];
            const teacherId = window.location.pathname.split('/').pop() || '{{ $teacher_id ?? 18 }}';
            let currentMonth = '';
            let currentYear = '';

            // DOM Elements
            const teacherNameTitle = document.getElementById('teacherNameTitle');
            const selectedMonthYear = document.getElementById('selectedMonthYear');
            const summaryMonthYear = document.getElementById('summaryMonthYear');
            const teacherIdElement = document.getElementById('teacherId');
            const teacherNameElement = document.getElementById('teacherName');
            const subjectNameElement = document.getElementById('subjectName');
            const salaryStatusElement = document.getElementById('salaryStatus');
            const totalCollectionsElement = document.getElementById('totalCollections');
            const advancePaymentsElement = document.getElementById('advancePayments');
            const teacherShareElement = document.getElementById('teacherShare');
            const institutionShareElement = document.getElementById('institutionShare');
            const netPayableElement = document.getElementById('netPayable');
            const teacherPercentageBar = document.getElementById('teacherPercentageBar');
            const institutionPercentageBar = document.getElementById('institutionPercentageBar');
            const teacherPercentageText = document.getElementById('teacherPercentageText');
            const institutionPercentageText = document.getElementById('institutionPercentageText');
            const classesCards = document.getElementById('classesCards');
            const paymentTableBody = document.getElementById('paymentTableBody');
            const paymentTableHeader = document.getElementById('paymentTableHeader');
            const paymentTableFooter = document.getElementById('paymentTableFooter');
            const tableLoadingSpinner = document.getElementById('tableLoadingSpinner');
            const tableEmptyState = document.getElementById('tableEmptyState');
            const currentMonthBlock = document.getElementById('currentMonthBlock');
            const tableContainer = document.getElementById('tableContainer');
            const salaryPaymentsTableBody = document.getElementById('salaryPaymentsTableBody');
            const salaryEmptyState = document.getElementById('salaryEmptyState');
            const salaryTableContainer = document.getElementById('salaryTableContainer');
            const monthSelect = document.getElementById('monthSelect');
            const yearSelect = document.getElementById('yearSelect');
            const exportTableExcelBtn = document.getElementById('exportTableExcelBtn');
            const exportTablePdfBtn = document.getElementById('exportTablePdfBtn');
            const payTeacherBtn = document.getElementById('payTeacherBtn');
            const currentMonthWarning = document.getElementById('currentMonthWarning');

            // Helper Functions
            function formatCurrency(amount) {
                if (isNaN(amount) || amount === null || amount === undefined) {
                    amount = 0;
                }
                return new Intl.NumberFormat('en-LK', {
                    style: 'currency',
                    currency: 'LKR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            }

            function formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function formatDateTable(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: '2-digit'
                });
            }

            function getMonthName(monthNumber) {
                const months = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                return months[parseInt(monthNumber) - 1] || 'Unknown';
            }

            function isCurrentMonth(month, year) {
                const now = new Date();
                const currentMonth = (now.getMonth() + 1).toString().padStart(2, '0');
                const currentYear = now.getFullYear().toString();
                return month === currentMonth && year === currentYear;
            }

            function getPreviousMonthYear() {
                const now = new Date();
                let month, year;
                
                if (now.getMonth() === 0) {
                    month = '12';
                    year = (now.getFullYear() - 1).toString();
                } else {
                    month = now.getMonth().toString().padStart(2, '0');
                    year = now.getFullYear().toString();
                }
                
                return { month, year };
            }

            // UI Functions
            function showLoading(show) {
                if (tableLoadingSpinner) {
                    if (show) {
                        tableLoadingSpinner.classList.remove('d-none');
                    } else {
                        tableLoadingSpinner.classList.add('d-none');
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

            function showCurrentMonthBlock(show) {
                if (currentMonthBlock) {
                    if (show) {
                        currentMonthBlock.classList.remove('d-none');
                        currentMonthWarning.classList.remove('d-none');
                    } else {
                        currentMonthBlock.classList.add('d-none');
                        currentMonthWarning.classList.add('d-none');
                    }
                }
            }

            function showEmptyState(show) {
                if (tableEmptyState) {
                    if (show) {
                        tableEmptyState.classList.remove('d-none');
                    } else {
                        tableEmptyState.classList.add('d-none');
                    }
                }
            }

            function showSalaryTable(show) {
                if (salaryTableContainer) {
                    if (show) {
                        salaryTableContainer.classList.remove('d-none');
                    } else {
                        salaryTableContainer.classList.add('d-none');
                    }
                }
            }

            function showSalaryEmptyState(show) {
                if (salaryEmptyState) {
                    if (show) {
                        salaryEmptyState.classList.remove('d-none');
                    } else {
                        salaryEmptyState.classList.add('d-none');
                    }
                }
            }

            function updateSelectedMonthYear(month, year) {
                if (selectedMonthYear) {
                    selectedMonthYear.textContent = `${getMonthName(month)} ${year}`;
                }
                if (summaryMonthYear) {
                    summaryMonthYear.textContent = `${getMonthName(month)} ${year}`;
                }
                currentMonth = month;
                currentYear = year;
            }

            // Data Fetching
            async function fetchTeacherData(month, year) {
                showLoading(true);
                showTable(false);
                showCurrentMonthBlock(false);
                showEmptyState(false);
                showSalaryTable(false);
                showSalaryEmptyState(false);

                // Check if trying to view current month
                if (isCurrentMonth(month, year)) {
                    showLoading(false);
                    showCurrentMonthBlock(true);
                    
                    // Auto-select previous month
                    const prev = getPreviousMonthYear();
                    if (monthSelect) monthSelect.value = prev.month;
                    if (yearSelect) yearSelect.value = prev.year;
                    
                    // Update display
                    updateSelectedMonthYear(prev.month, prev.year);
                    
                    // Fetch previous month data
                    await fetchTeacherData(prev.month, prev.year);
                    return;
                }

                try {
                    const url = `/api/teacher-payments/monthly-income/${teacherId}/${year}-${month}`;
                    const response = await fetch(url);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        teacherData = data;
                        renderAllSections();
                    } else {
                        throw new Error(data.message || 'Failed to load data');
                    }
                } catch (error) {
                    console.error('Error fetching teacher data:', error);
                    showEmptyState(true);
                } finally {
                    showLoading(false);
                }
            }

            function renderAllSections() {
                if (!teacherData) return;
                
                renderTeacherData();
                renderClassesCards();
                renderPaymentTable();
                renderSalaryPayments();
                showTable(true);
            }

            function renderTeacherData() {
                if (!teacherData) return;

                // Teacher Information
                if (teacherNameTitle) {
                    teacherNameTitle.innerHTML = `<i class="fas fa-user-graduate me-1"></i> ${teacherData.teacher_name || 'Teacher'}'s Income`;
                }
                
                if (teacherIdElement) {
                    teacherIdElement.textContent = teacherData.teacher_id || '-';
                }
                
                if (teacherNameElement) {
                    teacherNameElement.textContent = teacherData.teacher_name || '-';
                }
                
                if (subjectNameElement) {
                    subjectNameElement.textContent = teacherData.subject_name || '-';
                }
                
                if (salaryStatusElement) {
                    const isPaid = teacherData.is_salary_paid || false;
                    salaryStatusElement.textContent = isPaid ? 'Salary Paid' : 'Salary Not Paid';
                    salaryStatusElement.className = `badge bg-${isPaid ? 'success' : 'warning'}`;
                }

                // Financial Summary
                if (totalCollectionsElement) {
                    totalCollectionsElement.textContent = formatCurrency(teacherData.total_payments_this_month || 0);
                }
                
                if (advancePaymentsElement) {
                    advancePaymentsElement.textContent = formatCurrency(teacherData.advance_payment_this_month || 0);
                }
                
                if (teacherShareElement) {
                    teacherShareElement.textContent = formatCurrency(teacherData.teacher_share || 0);
                }
                
                if (institutionShareElement) {
                    institutionShareElement.textContent = formatCurrency(teacherData.institution_share || 0);
                }
                
                if (netPayableElement) {
                    netPayableElement.textContent = formatCurrency(teacherData.net_payable || 0);
                }

                // Percentages
                const teacherPercentage = teacherData.teacher_percentage || 0;
                const institutionPercentage = teacherData.institution_percentage || 0;

                if (teacherPercentageText) {
                    teacherPercentageText.textContent = `${teacherPercentage}% of total`;
                }
                
                if (institutionPercentageText) {
                    institutionPercentageText.textContent = `${institutionPercentage}% of total`;
                }

                if (teacherPercentageBar) {
                    teacherPercentageBar.style.width = `${teacherPercentage}%`;
                    const teacherPercentageTextBar = document.getElementById('teacherPercentageTextBar');
                    if (teacherPercentageTextBar) {
                        teacherPercentageTextBar.textContent = `Teacher: ${teacherPercentage}%`;
                    }
                }
                
                if (institutionPercentageBar) {
                    institutionPercentageBar.style.width = `${institutionPercentage}%`;
                    const institutionPercentageTextBar = document.getElementById('institutionPercentageTextBar');
                    if (institutionPercentageTextBar) {
                        institutionPercentageTextBar.textContent = `Institution: ${institutionPercentage}%`;
                    }
                }

                // Pay Button
                if (payTeacherBtn) {
                    const netPayable = teacherData.net_payable || 0;
                    const isPaid = teacherData.is_salary_paid || false;
                    
                    if (netPayable > 0 && !isPaid) {
                        payTeacherBtn.disabled = false;
                        payTeacherBtn.title = `Pay ${formatCurrency(netPayable)}`;
                    } else {
                        payTeacherBtn.disabled = true;
                        payTeacherBtn.title = isPaid ? 'Salary already paid' : 'No amount payable';
                    }
                }
            }

            function renderClassesCards() {
                if (!teacherData || !classesCards || !teacherData.classes) return;

                classesCards.innerHTML = '';

                if (!teacherData.classes.length) {
                    classesCards.innerHTML = `
                        <div class="col-12 text-center">
                            <div class="alert alert-light mb-0">
                                <i class="fas fa-info-circle me-1"></i> No class data available
                            </div>
                        </div>
                    `;
                    return;
                }

                const teacherPercentage = teacherData.teacher_percentage || 0;

                teacherData.classes.forEach(cls => {
                    const totalPaid = Object.values(cls.payments || {}).reduce((sum, val) => sum + val, 0);
                    const percentagePaid = cls.total_students > 0 ? Math.round((cls.students_paid / cls.total_students) * 100) : 0;
                    const teacherShare = totalPaid * teacherPercentage / 100;
                    
                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-4 mb-3';
                    card.innerHTML = `
                        <div class="card class-card h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 small">${cls.class_name || 'Unnamed Class'}</h6>
                                <small class="text-muted">Grade: ${cls.grade_name || 'N/A'}</small>
                            </div>
                            <div class="card-body py-2">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Paid Students</span>
                                        <span class="fw-bold small">${cls.students_paid || 0}/${cls.total_students || 0}</span>
                                    </div>
                                    <div class="student-progress">
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: ${percentagePaid}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Collection:</span>
                                        <span class="fw-bold small">${formatCurrency(totalPaid)}</span>
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Teacher's Share:</span>
                                        <span class="fw-bold text-success small">${formatCurrency(teacherShare)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    classesCards.appendChild(card);
                });
            }

            function renderPaymentTable() {
                if (!teacherData || !paymentTableBody || !teacherData.classes) {
                    showEmptyState(true);
                    return;
                }

                paymentTableBody.innerHTML = '';
                allPayments = [];
                allGrades = [];

                // Collect unique dates and grades
                const allDates = new Set();
                const gradeSet = new Set();
                
                teacherData.classes.forEach(cls => {
                    const grade = cls.grade_name;
                    if (grade) {
                        gradeSet.add(grade);
                    }
                    
                    Object.keys(cls.payments || {}).forEach(date => {
                        allDates.add(date);
                    });
                });

                const sortedDates = Array.from(allDates).sort((a, b) => new Date(a) - new Date(b));
                allGrades = Array.from(gradeSet).sort();

                if (sortedDates.length === 0) {
                    showEmptyState(true);
                    showTable(false);
                    return;
                }

                showEmptyState(false);

                // Render header
                renderTableHeader();

                // Calculate totals
                const totals = {
                    gradeTotals: {},
                    totalCollection: 0,
                    institutionShare: 0,
                    teacherShare: 0
                };

                allGrades.forEach(grade => {
                    totals.gradeTotals[grade] = 0;
                });

                const teacherPercentage = teacherData.teacher_percentage || 0;
                const institutionPercentage = teacherData.institution_percentage || 0;

                // Create rows
                sortedDates.forEach(date => {
                    const rowData = {
                        date: date,
                        gradePayments: {},
                        totalCollection: 0,
                        institutionShare: 0,
                        teacherShare: 0
                    };

                    allGrades.forEach(grade => {
                        rowData.gradePayments[grade] = 0;
                    });

                    teacherData.classes.forEach(cls => {
                        const grade = cls.grade_name;
                        const payment = cls.payments[date] || 0;
                        
                        if (payment > 0 && grade && rowData.gradePayments[grade] !== undefined) {
                            rowData.gradePayments[grade] += payment;
                            totals.gradeTotals[grade] += payment;
                        }
                    });

                    rowData.totalCollection = Object.values(rowData.gradePayments).reduce((sum, val) => sum + val, 0);
                    rowData.institutionShare = rowData.totalCollection * institutionPercentage / 100;
                    rowData.teacherShare = rowData.totalCollection * teacherPercentage / 100;

                    totals.totalCollection += rowData.totalCollection;
                    totals.institutionShare += rowData.institutionShare;
                    totals.teacherShare += rowData.teacherShare;

                    allPayments.push(rowData);

                    const row = document.createElement('tr');
                    
                    let rowHTML = `<td class="fw-bold">${formatDateTable(date)}</td>`;
                    
                    allGrades.forEach(grade => {
                        const amount = rowData.gradePayments[grade];
                        rowHTML += `<td>${amount > 0 ? formatCurrency(amount) : '-'}</td>`;
                    });
                    
                    rowHTML += `
                        <td class="fw-bold bg-light text-primary">${formatCurrency(rowData.totalCollection)}</td>
                        <td class="bg-light text-secondary">${formatCurrency(rowData.institutionShare)}</td>
                        <td class="fw-bold bg-success text-white">${formatCurrency(rowData.teacherShare)}</td>
                    `;
                    
                    row.innerHTML = rowHTML;
                    paymentTableBody.appendChild(row);
                });

                renderTableFooter(totals);
            }

            function renderTableHeader() {
                if (!paymentTableHeader) return;

                paymentTableHeader.innerHTML = '';

                const teacherPercentage = teacherData.teacher_percentage || 0;
                const institutionPercentage = teacherData.institution_percentage || 0;

                const firstRow = document.createElement('tr');
                firstRow.innerHTML = `<th rowspan="2">Date (DD/MM/YY)</th>`;
                
                allGrades.forEach(grade => {
                    firstRow.innerHTML += `<th>Grade ${grade}</th>`;
                });
                
                firstRow.innerHTML += `<th rowspan="2" class="bg-light text-primary">Total Collection</th>`;
                firstRow.innerHTML += `<th colspan="2" class="text-center" rowspan="2">Percentage Split</th>`;
                
                paymentTableHeader.appendChild(firstRow);

                const secondRow = document.createElement('tr');
                paymentTableHeader.appendChild(secondRow);

                setTimeout(() => {
                    const tableHeaders = document.querySelectorAll('#paymentTable thead tr:last-child th');
                    if (tableHeaders.length > allGrades.length + 2) {
                        tableHeaders[allGrades.length + 1].textContent = `${institutionPercentage}%`;
                        tableHeaders[allGrades.length + 1].className = 'bg-light text-secondary';
                        tableHeaders[allGrades.length + 2].textContent = `${teacherPercentage}%`;
                        tableHeaders[allGrades.length + 2].className = 'bg-success text-white';
                    }
                }, 10);
            }

            function renderTableFooter(totals) {
                if (!paymentTableFooter) return;

                paymentTableFooter.innerHTML = '';

                const footerRow = document.createElement('tr');
                footerRow.innerHTML = `<td class="fw-bold">Totals</td>`;
                
                allGrades.forEach(grade => {
                    footerRow.innerHTML += `<td class="fw-bold">${formatCurrency(totals.gradeTotals[grade] || 0)}</td>`;
                });
                
                footerRow.innerHTML += `
                    <td class="fw-bold bg-light text-primary">${formatCurrency(totals.totalCollection)}</td>
                    <td class="fw-bold bg-light text-secondary">${formatCurrency(totals.institutionShare)}</td>
                    <td class="fw-bold bg-success text-white">${formatCurrency(totals.teacherShare)}</td>
                `;
                
                paymentTableFooter.appendChild(footerRow);
            }

            function renderSalaryPayments() {
                if (!teacherData || !salaryPaymentsTableBody) return;

                salaryPaymentsTableBody.innerHTML = '';

                if (!teacherData.salary_payments || teacherData.salary_payments.length === 0) {
                    showSalaryEmptyState(true);
                    showSalaryTable(false);
                    return;
                }

                showSalaryEmptyState(false);
                showSalaryTable(true);

                teacherData.salary_payments.forEach(payment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${formatDate(payment.date)}</td>
                        <td class="fw-bold">${formatCurrency(payment.payment)}</td>
                        <td><span class="badge bg-info">${payment.reason_code || 'N/A'}</span></td>
                        <td>${payment.payment_for || 'N/A'}</td>
                        <td>
                            <span class="badge ${payment.status === 1 ? 'bg-success' : 'bg-warning'}">
                                ${payment.status === 1 ? 'Paid' : 'Pending'}
                            </span>
                        </td>
                    `;
                    salaryPaymentsTableBody.appendChild(row);
                });
            }

            // Event Handlers
            function setupMonthYearSelectors() {
                if (!monthSelect || !yearSelect) return;

                monthSelect.addEventListener('change', function() {
                    handleMonthYearChange();
                });

                yearSelect.addEventListener('change', function() {
                    handleMonthYearChange();
                });
            }

            function handleMonthYearChange() {
                const month = monthSelect.value;
                const year = yearSelect.value;
                
                if (isCurrentMonth(month, year)) {
                    alert('Current month data is not available. Showing previous month instead.');
                    
                    const prev = getPreviousMonthYear();
                    monthSelect.value = prev.month;
                    yearSelect.value = prev.year;
                    
                    updateSelectedMonthYear(prev.month, prev.year);
                    fetchTeacherData(prev.month, prev.year);
                } else {
                    updateSelectedMonthYear(month, year);
                    fetchTeacherData(month, year);
                }
            }

            // Setup Pay Teacher button with auto print after successful payment
            function setupPayTeacherButton() {
                if (!payTeacherBtn) return;

                payTeacherBtn.addEventListener('click', function () {
                    if (!teacherData || teacherData.net_payable <= 0 || teacherData.is_salary_paid) {
                        return;
                    }

                    const amount = teacherData.net_payable;
                    const teacherName = teacherData.teacher_name;
                    const teacherId = teacherData.teacher_id;
                    const monthYear = `${getMonthName(currentMonth)} ${currentYear}`;
                    const formattedAmount = formatCurrency(amount);

                    // Show custom confirmation
                    showPaymentConfirmation(teacherName, formattedAmount, monthYear, function (confirmed) {
                        if (confirmed) {
                            processPayment(teacherId, teacherName, amount, monthYear);
                        }
                    });
                });
            }

            // Custom confirmation
            function showPaymentConfirmation(teacherName, amount, monthYear, callback) {
                const modal = document.createElement('div');
                modal.id = 'paymentConfirmation';
                modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9998;
        `;

                modal.innerHTML = `
            <div style="
                background: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 350px;
                width: 90%;
                box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            ">
                <div style="text-align: center; margin-bottom: 15px;">
                    <h5 style="margin: 0 0 15px 0; color: #333;">Confirm Payment</h5>
                </div>

                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #666;">Teacher:</span>
                        <strong>${teacherName}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #666;">Amount:</span>
                        <strong style="color: #28a745;">${amount}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Period:</span>
                        <strong>${monthYear}</strong>
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button id="confirmBtn" style="
                        background: #28a745;
                        color: white;
                        border: none;
                        padding: 8px 20px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        flex: 1;
                    ">
                        Confirm
                    </button>

                    <button id="cancelBtn" style="
                        background: #dc3545;
                        color: white;
                        border: none;
                        padding: 8px 20px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        flex: 1;
                    ">
                        Cancel
                    </button>
                </div>
            </div>
        `;

                document.body.appendChild(modal);

                document.getElementById('confirmBtn').addEventListener('click', function () {
                    modal.remove();
                    callback(true);
                });

                document.getElementById('cancelBtn').addEventListener('click', function () {
                    modal.remove();
                    callback(false);
                });

                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        modal.remove();
                        callback(false);
                    }
                });
            }

            // Process payment
            function processPayment(teacherId, teacherName, amount, monthYear) {
                payTeacherBtn.disabled = true;
                payTeacherBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

                showPaymentProcessing(teacherName, amount, monthYear);

                fetch('/api/teacher-payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        teacher_id: teacherId,
                        payment: amount,
                        paymentFor: monthYear,
                        reason_code: 'salary',
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            hidePaymentProcessing();
                            showPaymentSuccess(data, teacherId, teacherName, amount, monthYear);
                            setTimeout(() => {
                                fetchTeacherData();
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Payment failed');
                        }
                    })
                    .catch(error => {
                        hidePaymentProcessing();
                        showPaymentError(error.message, teacherName, amount);
                        payTeacherBtn.disabled = false;
                        payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-1"></i> Pay Teacher';
                    });
            }

            // Show payment processing
            function showPaymentProcessing(teacherName, amount, monthYear) {
                const overlay = document.createElement('div');
                overlay.id = 'paymentProcessing';
                overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;

                overlay.innerHTML = `
            <div style="
                background: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 300px;
                width: 90%;
                text-align: center;
            ">
                <div style="font-size: 30px; color: #007bff; margin-bottom: 10px;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>

                <h5 style="margin-bottom: 15px; color: #333;">Processing Payment</h5>

                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="color: #666;">Teacher:</span>
                        <strong>${teacherName}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Amount:</span>
                        <strong>${formatCurrency(amount)}</strong>
                    </div>
                </div>

                <p style="color: #666; font-size: 13px; margin: 0;">
                    Please wait...
                </p>
            </div>
        `;

                document.body.appendChild(overlay);
            }

            // Hide payment processing
            function hidePaymentProcessing() {
                const overlay = document.getElementById('paymentProcessing');
                if (overlay) {
                    overlay.remove();
                }
            }

            // Show payment success
            function showPaymentSuccess(data, teacherId, teacherName, amount, monthYear) {
                const modal = document.createElement('div');
                modal.id = 'paymentSuccess';
                modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
        `;

                const formattedAmount = formatCurrency(amount);
                const paymentDate = new Date().toLocaleTimeString('en-LK', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                modal.innerHTML = `
            <div style="
                background: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 350px;
                width: 90%;
            ">
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="
                        width: 50px;
                        height: 50px;
                        background: #28a745;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 10px;
                    ">
                        <i class="fas fa-check" style="font-size: 20px; color: white;"></i>
                    </div>
                    <h5 style="margin: 0 0 5px 0; color: #28a745;">Payment Successful</h5>
                    <p style="color: #666; font-size: 13px; margin: 0;">${teacherName}</p>
                </div>

                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #666;">Amount:</span>
                        <strong style="color: #28a745;">${formattedAmount}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #666;">Period:</span>
                        <strong>${monthYear}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Time:</span>
                        <strong>${paymentDate}</strong>
                    </div>
                </div>

                <div style="
                    background: #d4edda;
                    padding: 8px;
                    border-radius: 4px;
                    margin-bottom: 15px;
                    border-left: 3px solid #28a745;
                ">
                    <p style="margin: 0; color: #155724; font-size: 12px;">
                        Printing in <span id="countdown" style="font-weight: bold;">5</span> seconds...
                    </p>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button id="printBtn" style="
                        background: #007bff;
                        color: white;
                        border: none;
                        padding: 8px 15px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        flex: 1;
                    ">
                        Print Now
                    </button>

                    <button id="closeBtn" style="
                        background: #6c757d;
                        color: white;
                        border: none;
                        padding: 8px 15px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        flex: 1;
                    ">
                        Close
                    </button>
                </div>
            </div>
        `;

                document.body.appendChild(modal);

                let countdown = 5;
                const countdownElement = document.getElementById('countdown');
                const countdownInterval = setInterval(() => {
                    countdown--;
                    countdownElement.textContent = countdown;

                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        openSalarySlip(teacherId, currentYear, currentMonth);
                    }
                }, 1000);

                document.getElementById('printBtn').addEventListener('click', function () {
                    clearInterval(countdownInterval);
                    openSalarySlip(teacherId, currentYear, currentMonth);
                });

                document.getElementById('closeBtn').addEventListener('click', function () {
                    clearInterval(countdownInterval);
                    modal.remove();
                    payTeacherBtn.disabled = false;
                    payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-1"></i> Pay Teacher';
                });

                setTimeout(() => {
                    if (document.getElementById('paymentSuccess')) {
                        modal.remove();
                        payTeacherBtn.disabled = false;
                        payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-1"></i> Pay Teacher';
                    }
                }, 15000);
            }

            // Open salary slip
            function openSalarySlip(teacherId, year, month) {
                const modal = document.getElementById('paymentSuccess');
                if (modal) {
                    modal.remove();
                }

                const formattedMonth = month.toString().padStart(2, '0');
                const yearMonth = `${year}-${formattedMonth}`;
                const salarySlipUrl = `/teacher-payment/salary-slip/${teacherId}/${yearMonth}?autoPrint=true&ref=${Date.now()}`;

                const printWindow = window.open(salarySlipUrl, '_blank', 'width=900,height=700,scrollbars=yes');

                if (printWindow) {
                    printWindow.focus();
                }

                payTeacherBtn.disabled = false;
                payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-1"></i> Pay Teacher';
            }

            // Show payment error
            function showPaymentError(errorMessage, teacherName, amount) {
                const modal = document.createElement('div');
                modal.id = 'paymentError';
                modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
        `;

                modal.innerHTML = `
            <div style="
                background: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 350px;
                width: 90%;
            ">
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="font-size: 30px; color: #dc3545; margin-bottom: 10px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 style="margin: 0; color: #dc3545;">Payment Failed</h5>
                </div>

                <div style="margin-bottom: 15px;">
                    <p style="color: #721c24; font-size: 14px; margin: 0 0 10px 0;">
                        ${errorMessage}
                    </p>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Teacher:</span>
                        <strong>${teacherName}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                        <span style="color: #666;">Amount:</span>
                        <strong>${formatCurrency(amount)}</strong>
                    </div>
                </div>

                <button id="errorCloseBtn" style="
                    background: #dc3545;
                    color: white;
                    border: none;
                    padding: 8px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    width: 100%;
                ">
                    Try Again
                </button>
            </div>
        `;

                document.body.appendChild(modal);

                document.getElementById('errorCloseBtn').addEventListener('click', function () {
                    modal.remove();
                    payTeacherBtn.disabled = false;
                    payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-1"></i> Pay Teacher';
                });

                setTimeout(() => {
                    if (document.getElementById('paymentError')) {
                        modal.remove();
                        payTeacherBtn.disabled = false;
                        payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-1"></i> Pay Teacher';
                    }
                }, 10000);
            }

            function setupExportButtons() {
                // Excel Export
                if (exportTableExcelBtn) {
                    exportTableExcelBtn.addEventListener('click', function() {
                        if (!teacherData || allPayments.length === 0) {
                            alert('No data to export');
                            return;
                        }

                        try {
                            const teacherPercentage = teacherData.teacher_percentage || 0;
                            const institutionPercentage = teacherData.institution_percentage || 0;

                            const exportData = allPayments.map(payment => {
                                const rowData = {
                                    'Date': formatDateTable(payment.date)
                                };
                                
                                allGrades.forEach(grade => {
                                    rowData[`Grade ${grade}`] = payment.gradePayments[grade] || 0;
                                });
                                
                                rowData['Total Collection'] = payment.totalCollection || 0;
                                rowData[`Institution Share (${institutionPercentage}%)`] = payment.institutionShare || 0;
                                rowData[`Teacher Share (${teacherPercentage}%)`] = payment.teacherShare || 0;
                                
                                return rowData;
                            });

                            const totalsRow = {
                                'Date': 'TOTALS'
                            };
                            
                            allGrades.forEach(grade => {
                                totalsRow[`Grade ${grade}`] = allPayments.reduce((sum, p) => sum + (p.gradePayments[grade] || 0), 0);
                            });
                            
                            totalsRow['Total Collection'] = allPayments.reduce((sum, p) => sum + p.totalCollection, 0);
                            totalsRow[`Institution Share (${institutionPercentage}%)`] = allPayments.reduce((sum, p) => sum + p.institutionShare, 0);
                            totalsRow[`Teacher Share (${teacherPercentage}%)`] = allPayments.reduce((sum, p) => sum + p.teacherShare, 0);
                            
                            exportData.push(totalsRow);

                            const ws = XLSX.utils.json_to_sheet(exportData);
                            const wb = XLSX.utils.book_new();
                            XLSX.utils.book_append_sheet(wb, ws, 'Teacher Payments');

                            const filename = `${teacherData.teacher_name}_${getMonthName(currentMonth)}_${currentYear}_Payments.xlsx`;
                            XLSX.writeFile(wb, filename);
                        } catch (error) {
                            console.error('Error exporting to Excel:', error);
                            alert('Failed to export Excel file. Please try again.');
                        }
                    });
                }

                // PDF Export
                if (exportTablePdfBtn) {
                    exportTablePdfBtn.addEventListener('click', function() {
                        if (!teacherData || allPayments.length === 0) {
                            alert('No data to export');
                            return;
                        }

                        try {
                            const { jsPDF } = window.jspdf;
                            const doc = new jsPDF('landscape');

                            const teacherPercentage = teacherData.teacher_percentage || 0;
                            const institutionPercentage = teacherData.institution_percentage || 0;

                            // Title
                            doc.setFontSize(14);
                            doc.text(`${teacherData.teacher_name} - Payment Report`, 14, 10);
                            doc.setFontSize(10);
                            doc.text(`Period: ${getMonthName(currentMonth)} ${currentYear}`, 14, 16);
                            doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 22);

                            // Summary
                            doc.setFontSize(11);
                            doc.text('Summary', 14, 30);
                            doc.setFontSize(9);
                            doc.text(`Total Collections: ${formatCurrency(teacherData.total_payments_this_month || 0)}`, 14, 36);
                            doc.text(`Teacher's Share (${teacherPercentage}%): ${formatCurrency(teacherData.teacher_share || 0)}`, 14, 41);
                            doc.text(`Advance Payments: ${formatCurrency(teacherData.advance_payment_this_month || 0)}`, 14, 46);
                            doc.text(`Net Payable: ${formatCurrency(teacherData.net_payable || 0)}`, 14, 51);

                            // Table headers
                            const headers = ['Date'];
                            allGrades.forEach(grade => {
                                headers.push(`Grade ${grade}`);
                            });
                            headers.push('Total', `Inst (${institutionPercentage}%)`, `Teach (${teacherPercentage}%)`);

                            // Table data
                            const tableData = allPayments.map(payment => {
                                const row = [formatDateTable(payment.date)];
                                
                                allGrades.forEach(grade => {
                                    row.push(formatCurrency(payment.gradePayments[grade] || 0));
                                });
                                
                                row.push(
                                    formatCurrency(payment.totalCollection),
                                    formatCurrency(payment.institutionShare),
                                    formatCurrency(payment.teacherShare)
                                );
                                
                                return row;
                            });

                            // Add table
                            doc.autoTable({
                                head: [headers],
                                body: tableData,
                                startY: 55,
                                styles: { fontSize: 7 },
                                headStyles: { fillColor: [41, 128, 185] },
                                columnStyles: {
                                    0: { fontStyle: 'bold', cellWidth: 25 },
                                    [allGrades.length + 1]: { fontStyle: 'bold', textColor: [13, 110, 253] },
                                    [allGrades.length + 2]: { textColor: [108, 117, 125] },
                                    [allGrades.length + 3]: { fontStyle: 'bold', fillColor: [39, 174, 96], textColor: [255, 255, 255] }
                                }
                            });

                            const filename = `${teacherData.teacher_name}_${getMonthName(currentMonth)}_${currentYear}_Payments.pdf`;
                            doc.save(filename);
                        } catch (error) {
                            console.error('Error exporting to PDF:', error);
                            alert('Failed to export PDF file. Please try again.');
                        }
                    });
                }
            }

            // Initialize
            function init() {
                console.log('Initializing Teacher Income History...');

                // Set initial month/year to previous month
                const prev = getPreviousMonthYear();
                currentMonth = prev.month;
                currentYear = prev.year;
                
                if (monthSelect && monthSelect.value !== prev.month) {
                    monthSelect.value = prev.month;
                }
                if (yearSelect && yearSelect.value !== prev.year) {
                    yearSelect.value = prev.year;
                }
                
                updateSelectedMonthYear(prev.month, prev.year);

                // Setup event listeners
                setupMonthYearSelectors();
                setupPayTeacherButton();
                setupExportButtons();

                // Load initial data
                fetchTeacherData(prev.month, prev.year);

                console.log('Teacher Income History initialized successfully');
            }

            // Start when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            // Error handling
            window.addEventListener('error', function(event) {
                console.error('Global error:', event.error);
            });

            window.addEventListener('unhandledrejection', function(event) {
                console.error('Unhandled promise rejection:', event.reason);
                alert('An unexpected error occurred. Please refresh the page.');
            });

        })();
    </script>
@endpush