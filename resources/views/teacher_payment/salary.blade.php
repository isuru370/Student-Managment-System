@extends('layouts.app')

@section('title', 'Teacher Income Details')
@section('page-title', 'Teacher Income Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher_payment.index') }}">Teacher Payments</a></li>
    <li class="breadcrumb-item active">Teacher Income Details</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <!-- Current Month Display -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body py-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-12 text-center">
                                <div class="badge bg-info text-dark fs-6 px-3 py-2" id="selectedMonthYear">
                                    {{ date('F Y') }}
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> Showing data for current month only
                                </small>
                            </div>
                        </div>
                    </div>
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
                                {{ date('F Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Teacher Information - Compact -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-header bg-primary text-white py-2">
                                        <h6 class="mb-0 small"><i class="fas fa-info-circle me-1"></i> Teacher Information
                                        </h6>
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
                                            <label class="form-label text-muted small mb-1">Status</label>
                                            <br>
                                            <span class="badge bg-warning" id="salaryStatus">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Summary - Compact -->
                            <div class="col-md-8 mb-3">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white py-2">
                                        <h6 class="mb-0 small"><i class="fas fa-chart-bar me-1"></i> Financial Summary</h6>
                                    </div>
                                    <div class="card-body py-2">
                                        <!-- Quick Stats Row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="card bg-light h-100">
                                                    <div class="card-body py-2">
                                                        <h6 class="card-title text-muted small mb-1">Total Collections</h6>
                                                        <h4 class="fw-bold text-primary mb-0" id="totalCollections">LKR 0.00
                                                        </h4>
                                                        <small class="text-muted">From students</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-light h-100">
                                                    <div class="card-body py-2">
                                                        <h6 class="card-title text-muted small mb-1">Advance Payments</h6>
                                                        <h4 class="fw-bold text-warning mb-0" id="advancePayments">LKR 0.00
                                                        </h4>
                                                        <small class="text-muted">Paid in advance</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Percentage Split -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-primary" id="teacherPercentageBar"
                                                        style="width: 0%">
                                                        <span class="small fw-bold" id="teacherPercentageTextBar">Teacher:
                                                            0%</span>
                                                    </div>
                                                    <div class="progress-bar bg-secondary" id="institutionPercentageBar"
                                                        style="width: 0%">
                                                        <span class="small fw-bold"
                                                            id="institutionPercentageTextBar">Institution: 0%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Shares Row -->
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

                                        <!-- Net Payable with Pay Button -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card bg-warning">
                                                    <div class="card-body py-2 text-center">
                                                        <h6 class="card-title mb-1">
                                                            <i class="fas fa-money-bill-wave me-1"></i> Net Payable to
                                                            Teacher
                                                        </h6>
                                                        <h3 class="fw-bold my-1" id="netPayable">LKR 0.00</h3>
                                                        <small class="text-muted">(Teacher's Share - Advance
                                                            Payments)</small>
                                                        <div class="mt-2">
                                                            <button class="btn btn-sm btn-success px-3" id="payTeacherBtn"
                                                                disabled>
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

        <!-- Classes Breakdown - Compact -->
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
                            <!-- Classes will be populated here -->
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
                        <div id="tableLoadingSpinner" class="text-center d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 small">Loading payment data...</p>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" id="paymentTable">
                                <thead class="table-primary" id="paymentTableHeader">
                                    <!-- Dynamic header will be populated here -->
                                </thead>
                                <tbody id="paymentTableBody">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                                <tfoot class="table-secondary" id="paymentTableFooter">
                                    <!-- Dynamic footer will be populated here -->
                                </tfoot>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div id="tableEmptyState" class="text-center d-none">
                            <div class="alert alert-info py-2">
                                <h6 class="mb-1"><i class="fas fa-info-circle"></i> No Payment Data</h6>
                                <p class="mb-0 small">No payment records found for the current month.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary Payments History - Compact -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-danger text-white py-2">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-history me-1"></i> Advance Payment History
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="table-responsive">
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
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div id="salaryEmptyState" class="text-center d-none">
                            <div class="alert alert-warning py-2">
                                <h6 class="mb-1"><i class="fas fa-info-circle"></i> No Advance Payments</h6>
                                <p class="mb-0 small">No salary Advance found for this teacher.</p>
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

        .table th,
        .table td {
            padding: 0.3rem 0.5rem;
            vertical-align: middle;
            font-size: 0.85rem;
        }

        .table-primary th {
            background-color: #e3f2fd;
        }

        .bg-success.text-white th {
            background-color: #28a745 !important;
        }

        .card-header {
            padding: 0.5rem 0.75rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .form-select,
        .form-control {
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.75rem;
            }

            .card-body .row>div {
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

        .text-primary {
            color: #0d6efd !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        h4,
        h5,
        h6 {
            margin-bottom: 0.5rem;
        }

        .small {
            font-size: 0.85rem;
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

            // Always use current month and year
            const now = new Date();
            let currentMonth = (now.getMonth() + 1).toString().padStart(2, '0');
            let currentYear = now.getFullYear().toString();

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
            const salaryPaymentsTableBody = document.getElementById('salaryPaymentsTableBody');
            const salaryEmptyState = document.getElementById('salaryEmptyState');
            const exportTableExcelBtn = document.getElementById('exportTableExcelBtn');
            const exportTablePdfBtn = document.getElementById('exportTablePdfBtn');
            const payTeacherBtn = document.getElementById('payTeacherBtn');

            // Format currency to LKR
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

            // Format date
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            // Format date for table (DD/MM/YY)
            function formatDateTable(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: '2-digit'
                }).replace(/\//g, '/');
            }

            // Get month name from number
            function getMonthName(monthNumber) {
                const months = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                return months[parseInt(monthNumber) - 1] || 'Unknown';
            }

            // Show loading spinner for table
            function showTableLoading(show) {
                if (tableLoadingSpinner) {
                    if (show) {
                        tableLoadingSpinner.classList.remove('d-none');
                        if (paymentTableBody) {
                            paymentTableBody.innerHTML = '';
                        }
                    } else {
                        tableLoadingSpinner.classList.add('d-none');
                    }
                }
            }

            // Show empty state for table
            function showTableEmptyState(show) {
                if (tableEmptyState) {
                    if (show) {
                        tableEmptyState.classList.remove('d-none');
                    } else {
                        tableEmptyState.classList.add('d-none');
                    }
                }
            }

            // Show empty state for salary payments
            function showSalaryEmptyState(show) {
                if (salaryEmptyState) {
                    if (show) {
                        salaryEmptyState.classList.remove('d-none');
                    } else {
                        salaryEmptyState.classList.add('d-none');
                    }
                }
            }

            // Fetch teacher data - ALWAYS for current month/year
            async function fetchTeacherData() {
                showTableLoading(true);

                try {
                    const url = `/api/teacher-payments/monthly-income/${teacherId}/${currentYear}-${currentMonth}`;
                    console.log('Fetching data from:', url);

                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('API Response:', data);

                    if (data.status === 'success') {
                        teacherData = data;
                        renderTeacherData();
                        renderClassesCards();
                        renderPaymentTable();
                        renderSalaryPayments();
                        updateSelectedMonthYear();
                    } else {
                        throw new Error(data.message || 'Failed to load teacher data');
                    }
                } catch (error) {
                    console.error('Error fetching teacher data:', error);
                    showTableEmptyState(true);
                } finally {
                    showTableLoading(false);
                }
            }

            // Update selected month year display - ALWAYS current month/year
            function updateSelectedMonthYear() {
                if (selectedMonthYear) {
                    selectedMonthYear.textContent = `${getMonthName(currentMonth)} ${currentYear}`;
                }
                if (summaryMonthYear) {
                    summaryMonthYear.textContent = `${getMonthName(currentMonth)} ${currentYear}`;
                }
            }

            // Render teacher data
            function renderTeacherData() {
                if (!teacherData) return;

                // Update teacher information
                if (teacherNameTitle) {
                    teacherNameTitle.innerHTML = `<i class="fas fa-user-graduate me-1"></i> ${teacherData.teacher_name}'s Income`;
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

                // Update financial summary
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

                // Get percentages from API response
                const teacherPercentage = teacherData.teacher_percentage || 0;
                const institutionPercentage = teacherData.institution_percentage || 0;

                // Update percentage texts
                if (teacherPercentageText) {
                    teacherPercentageText.textContent = `${teacherPercentage}% of total collections`;
                }

                if (institutionPercentageText) {
                    institutionPercentageText.textContent = `${institutionPercentage}% of total collections`;
                }

                // Update progress bars with actual percentages
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

                // Update Pay Teacher button
                if (payTeacherBtn) {
                    const netPayable = teacherData.net_payable || 0;
                    if (netPayable > 0 && !teacherData.is_salary_paid) {
                        payTeacherBtn.disabled = false;
                        payTeacherBtn.title = `Pay ${formatCurrency(netPayable)}`;
                    } else {
                        payTeacherBtn.disabled = true;
                        payTeacherBtn.title = teacherData.is_salary_paid ? 'Salary already paid' : 'No amount payable';
                    }
                }
            }

            // Render classes cards
            function renderClassesCards() {
                if (!teacherData || !classesCards || !teacherData.classes) return;

                classesCards.innerHTML = '';

                // Get percentages from API
                const teacherPercentage = teacherData.teacher_percentage || 0;

                teacherData.classes.forEach(cls => {
                    const totalPaid = Object.values(cls.payments || {}).reduce((sum, val) => sum + val, 0);
                    const percentagePaid = cls.total_students > 0 ? Math.round((cls.students_paid / cls.total_students) * 100) : 0;
                    const teacherShare = totalPaid * teacherPercentage / 100;

                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-3 mb-2';
                    card.innerHTML = `
                                        <div class="card class-card h-100">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0 small">${cls.class_name || 'Unnamed Class'}</h6>
                                                <small class="text-muted">Grade: ${cls.grade_name || 'N/A'}</small>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted small">Paid</span>
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
                                                        <span class="text-muted small">Teacher's:</span>
                                                        <span class="fw-bold text-success small">${formatCurrency(teacherShare)}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;

                    classesCards.appendChild(card);
                });
            }

            // Render payment table with dynamic columns
            function renderPaymentTable() {
                if (!teacherData || !paymentTableBody || !teacherData.classes) {
                    showTableEmptyState(true);
                    return;
                }

                // Clear previous data
                paymentTableBody.innerHTML = '';
                allPayments = [];
                allGrades = [];

                // 1. Collect all unique dates and grades from API response
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

                // Sort dates and grades
                const sortedDates = Array.from(allDates).sort((a, b) => new Date(a) - new Date(b));
                allGrades = Array.from(gradeSet).sort();

                if (sortedDates.length === 0) {
                    showTableEmptyState(true);
                    return;
                }

                showTableEmptyState(false);

                // 2. Create dynamic table header
                renderTableHeader();

                // 3. Calculate totals
                const totals = {
                    gradeTotals: {},
                    totalCollection: 0,
                    institutionShare: 0,
                    teacherShare: 0
                };

                // Initialize grade totals
                allGrades.forEach(grade => {
                    totals.gradeTotals[grade] = 0;
                });

                // Get percentages from API
                const teacherPercentage = teacherData.teacher_percentage || 0;
                const institutionPercentage = teacherData.institution_percentage || 0;

                // 4. Create table rows for each date
                sortedDates.forEach(date => {
                    const rowData = {
                        date: date,
                        gradePayments: {},
                        totalCollection: 0,
                        institutionShare: 0,
                        teacherShare: 0
                    };

                    // Initialize grade payments for this date
                    allGrades.forEach(grade => {
                        rowData.gradePayments[grade] = 0;
                    });

                    // Collect payments for this date from all classes
                    teacherData.classes.forEach(cls => {
                        const grade = cls.grade_name;
                        const payment = cls.payments[date] || 0;

                        if (payment > 0 && grade && rowData.gradePayments[grade] !== undefined) {
                            rowData.gradePayments[grade] += payment;
                            totals.gradeTotals[grade] += payment;
                        }
                    });

                    // Calculate row totals with ACTUAL percentages from API
                    rowData.totalCollection = Object.values(rowData.gradePayments).reduce((sum, val) => sum + val, 0);
                    rowData.institutionShare = rowData.totalCollection * institutionPercentage / 100;
                    rowData.teacherShare = rowData.totalCollection * teacherPercentage / 100;

                    // Update overall totals
                    totals.totalCollection += rowData.totalCollection;
                    totals.institutionShare += rowData.institutionShare;
                    totals.teacherShare += rowData.teacherShare;

                    allPayments.push(rowData);

                    // Create table row
                    const row = document.createElement('tr');

                    // Date cell
                    let rowHTML = `<td class="fw-bold">${formatDateTable(date)}</td>`;

                    // Grade payment cells
                    allGrades.forEach(grade => {
                        const amount = rowData.gradePayments[grade];
                        rowHTML += `<td>${amount > 0 ? formatCurrency(amount) : '-'}</td>`;
                    });

                    // Total and percentage cells
                    rowHTML += `
                                        <td class="fw-bold bg-light text-primary">${formatCurrency(rowData.totalCollection)}</td>
                                        <td class="bg-light text-secondary">${formatCurrency(rowData.institutionShare)}</td>
                                        <td class="fw-bold bg-success text-white">${formatCurrency(rowData.teacherShare)}</td>
                                    `;

                    row.innerHTML = rowHTML;
                    paymentTableBody.appendChild(row);
                });

                // 5. Create footer with totals
                renderTableFooter(totals);
            }

            // Render table header based on grades from API
            function renderTableHeader() {
                if (!paymentTableHeader) return;

                paymentTableHeader.innerHTML = '';

                // Get percentages from API response
                const teacherPercentage = teacherData.teacher_percentage || 0;
                const institutionPercentage = teacherData.institution_percentage || 0;

                // First row: Date column + Grade columns + Total + Percentage Split
                const firstRow = document.createElement('tr');

                // Date column
                firstRow.innerHTML = `<th rowspan="2">Date (DD/MM/YY)</th>`;

                // Grade columns - dynamic based on API response
                allGrades.forEach(grade => {
                    firstRow.innerHTML += `<th>Grade ${grade}</th>`;
                });

                // Total Collection column
                firstRow.innerHTML += `<th rowspan="2" class="bg-light text-primary">Total Collection</th>`;

                // Percentage Split columns with dynamic percentages
                firstRow.innerHTML += `<th colspan="2" class="text-center" rowspan="2">Percentage Split</th>`;

                paymentTableHeader.appendChild(firstRow);

                // Second row for percentage labels
                const secondRow = document.createElement('tr');
                // No additional cells needed for grades
                paymentTableHeader.appendChild(secondRow);

                // Update the second row with percentage labels in the correct columns
                setTimeout(() => {
                    const tableHeaders = document.querySelectorAll('#paymentTable thead tr:last-child th');
                    if (tableHeaders.length > allGrades.length + 2) {
                        // Set institution percentage
                        tableHeaders[allGrades.length + 1].textContent = `${institutionPercentage}%`;
                        tableHeaders[allGrades.length + 1].className = 'bg-light text-secondary';

                        // Set teacher percentage
                        tableHeaders[allGrades.length + 2].textContent = `${teacherPercentage}%`;
                        tableHeaders[allGrades.length + 2].className = 'bg-success text-white';
                    }
                }, 10);
            }

            // Render table footer with totals
            function renderTableFooter(totals) {
                if (!paymentTableFooter) return;

                paymentTableFooter.innerHTML = '';

                const footerRow = document.createElement('tr');

                // Totals label
                footerRow.innerHTML = `<td class="fw-bold">Totals</td>`;

                // Grade totals
                allGrades.forEach(grade => {
                    footerRow.innerHTML += `<td class="fw-bold">${formatCurrency(totals.gradeTotals[grade] || 0)}</td>`;
                });

                // Overall totals
                footerRow.innerHTML += `
                                    <td class="fw-bold bg-light text-primary">${formatCurrency(totals.totalCollection)}</td>
                                    <td class="fw-bold bg-light text-secondary">${formatCurrency(totals.institutionShare)}</td>
                                    <td class="fw-bold bg-success text-white">${formatCurrency(totals.teacherShare)}</td>
                                `;

                paymentTableFooter.appendChild(footerRow);
            }

            // Render salary payments
            function renderSalaryPayments() {
                if (!teacherData || !salaryPaymentsTableBody) return;

                salaryPaymentsTableBody.innerHTML = '';

                if (!teacherData.salary_payments || teacherData.salary_payments.length === 0) {
                    showSalaryEmptyState(true);
                    return;
                }

                showSalaryEmptyState(false);

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

            // Export table to Excel
            function setupExportTableExcel() {
                if (!exportTableExcelBtn) return;

                exportTableExcelBtn.addEventListener('click', function () {
                    if (!teacherData || allPayments.length === 0) {
                        alert('No data to export');
                        return;
                    }

                    try {
                        // Get percentages from API
                        const teacherPercentage = teacherData.teacher_percentage || 0;
                        const institutionPercentage = teacherData.institution_percentage || 0;

                        // Prepare data for export with dynamic columns
                        const exportData = allPayments.map(payment => {
                            const rowData = {
                                'Date': formatDateTable(payment.date)
                            };

                            // Add dynamic grade columns
                            allGrades.forEach(grade => {
                                rowData[`Grade ${grade}`] = payment.gradePayments[grade] || 0;
                            });

                            // Add calculated columns with ACTUAL percentages from API
                            rowData['Total Collection'] = payment.totalCollection || 0;
                            rowData[`Institution Share (${institutionPercentage}%)`] = payment.institutionShare || 0;
                            rowData[`Teacher Share (${teacherPercentage}%)`] = payment.teacherShare || 0;

                            return rowData;
                        });

                        // Add totals row
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

                        // Create worksheet
                        const ws = XLSX.utils.json_to_sheet(exportData);

                        // Create workbook
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Teacher Payments');

                        // Generate filename
                        const filename = `${teacherData.teacher_name}_${getMonthName(currentMonth)}_${currentYear}_Payments.xlsx`;

                        // Generate Excel file
                        XLSX.writeFile(wb, filename);
                    } catch (error) {
                        console.error('Error exporting to Excel:', error);
                        alert('Failed to export Excel file. Please try again.');
                    }
                });
            }

            // Export table to PDF
            function setupExportTablePdf() {
                if (!exportTablePdfBtn) return;

                exportTablePdfBtn.addEventListener('click', function () {
                    if (!teacherData || allPayments.length === 0) {
                        alert('No data to export');
                        return;
                    }

                    try {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('landscape');

                        // Get percentages from API
                        const teacherPercentage = teacherData.teacher_percentage || 0;
                        const institutionPercentage = teacherData.institution_percentage || 0;

                        // Title
                        doc.setFontSize(14);
                        doc.text(`${teacherData.teacher_name} - Payment Report`, 14, 10);
                        doc.setFontSize(10);
                        doc.text(`Period: ${getMonthName(currentMonth)} ${currentYear}`, 14, 16);
                        doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 22);

                        // Summary section
                        doc.setFontSize(11);
                        doc.text('Summary', 14, 30);
                        doc.setFontSize(9);
                        doc.text(`Total Collections: ${formatCurrency(teacherData.total_payments_this_month || 0)}`, 14, 36);
                        doc.text(`Teacher's Share (${teacherPercentage}%): ${formatCurrency(teacherData.teacher_share || 0)}`, 14, 41);
                        doc.text(`Advance Payments: ${formatCurrency(teacherData.advance_payment_this_month || 0)}`, 14, 46);
                        doc.text(`Net Payable: ${formatCurrency(teacherData.net_payable || 0)}`, 14, 51);

                        // Prepare table headers
                        const headers = ['Date'];
                        allGrades.forEach(grade => {
                            headers.push(`Grade ${grade}`);
                        });
                        headers.push('Total', `Inst (${institutionPercentage}%)`, `Teach (${teacherPercentage}%)`);

                        // Prepare table data
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

                        // Add totals row
                        const totalsRow = ['TOTALS'];
                        allGrades.forEach(grade => {
                            const total = allPayments.reduce((sum, p) => sum + (p.gradePayments[grade] || 0), 0);
                            totalsRow.push(formatCurrency(total));
                        });

                        const totalCollection = allPayments.reduce((sum, p) => sum + p.totalCollection, 0);
                        const totalInstitutionShare = allPayments.reduce((sum, p) => sum + p.institutionShare, 0);
                        const totalTeacherShare = allPayments.reduce((sum, p) => sum + p.teacherShare, 0);

                        totalsRow.push(
                            formatCurrency(totalCollection),
                            formatCurrency(totalInstitutionShare),
                            formatCurrency(totalTeacherShare)
                        );

                        tableData.push(totalsRow);

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

                        // Generate filename
                        const filename = `${teacherData.teacher_name}_${getMonthName(currentMonth)}_${currentYear}_Payments.pdf`;

                        // Save PDF
                        doc.save(filename);
                    } catch (error) {
                        console.error('Error exporting to PDF:', error);
                        alert('Failed to export PDF file. Please try again.');
                    }
                });
            }

            // Initialize everything
            function init() {
                console.log('Initializing Teacher Income Details...');

                // Always use current month/year - no filters needed
                updateSelectedMonthYear();

                // Setup event listeners
                setupPayTeacherButton();
                setupExportTableExcel();
                setupExportTablePdf();

                // Load initial data for current month
                fetchTeacherData();

                console.log('Teacher Income Details initialized successfully');
            }

            // Start when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            // Error handling
            window.addEventListener('error', function (event) {
                console.error('Global error:', event.error);
            });

            window.addEventListener('unhandledrejection', function (event) {
                console.error('Unhandled promise rejection:', event.reason);
            });

        })();
    </script>
@endpush