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

        /* Modal animations */
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Print window styles */
        @media print {
            .no-print {
                display: none !important;
            }
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
        /**
         * Teacher Income Details - Production Ready Version
         * Maintains all original functionality with improvements
         */
        (function () {
            'use strict';

            // ==================== CONFIGURATION ====================
            const CONFIG = {
                API_TIMEOUT: 30000,
                MAX_RETRIES: 2,
                RETRY_DELAY: 1000,
                AUTO_CLOSE_TIMEOUT: 15000,
                TOAST_DURATION: 3000,
                PRINT_WINDOW_DELAY: 1000,
                REFRESH_DELAY: 2000
            };

            // ==================== STATE MANAGEMENT ====================
            const state = {
                teacherData: null,
                allPayments: [],
                allGrades: [],
                currentFetchId: 0,
                abortController: null,
                isProcessingPayment: false
            };

            // ==================== DOM ELEMENT CACHE ====================
            const elements = {
                teacherNameTitle: document.getElementById('teacherNameTitle'),
                selectedMonthYear: document.getElementById('selectedMonthYear'),
                summaryMonthYear: document.getElementById('summaryMonthYear'),
                teacherId: document.getElementById('teacherId'),
                teacherName: document.getElementById('teacherName'),
                subjectName: document.getElementById('subjectName'),
                salaryStatus: document.getElementById('salaryStatus'),
                totalCollections: document.getElementById('totalCollections'),
                advancePayments: document.getElementById('advancePayments'),
                teacherShare: document.getElementById('teacherShare'),
                institutionShare: document.getElementById('institutionShare'),
                netPayable: document.getElementById('netPayable'),
                teacherPercentageBar: document.getElementById('teacherPercentageBar'),
                institutionPercentageBar: document.getElementById('institutionPercentageBar'),
                teacherPercentageText: document.getElementById('teacherPercentageText'),
                institutionPercentageText: document.getElementById('institutionPercentageText'),
                classesCards: document.getElementById('classesCards'),
                paymentTableBody: document.getElementById('paymentTableBody'),
                paymentTableHeader: document.getElementById('paymentTableHeader'),
                paymentTableFooter: document.getElementById('paymentTableFooter'),
                tableLoadingSpinner: document.getElementById('tableLoadingSpinner'),
                tableEmptyState: document.getElementById('tableEmptyState'),
                salaryPaymentsTableBody: document.getElementById('salaryPaymentsTableBody'),
                salaryEmptyState: document.getElementById('salaryEmptyState'),
                exportTableExcelBtn: document.getElementById('exportTableExcelBtn'),
                exportTablePdfBtn: document.getElementById('exportTablePdfBtn'),
                payTeacherBtn: document.getElementById('payTeacherBtn')
            };

            // ==================== UTILITY FUNCTIONS ====================
            const utils = {
                // CSRF Token
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',

                // Teacher ID extraction
                teacherId: (() => {
                    const pathParts = window.location.pathname.split('/').filter(part => part);
                    return pathParts[pathParts.length - 1] || '{{ $teacher_id ?? 18 }}';
                })(),

                // Current date
                now: new Date(),
                currentMonth: (new Date().getMonth() + 1).toString().padStart(2, '0'),
                currentYear: new Date().getFullYear().toString(),

                /**
                 * Format currency to LKR
                 * Handles both string and number inputs
                 */
                formatCurrency(amount) {
                    let numericAmount = amount;

                    if (typeof amount === 'string') {
                        numericAmount = amount.toString()
                            .replace(/[^\d.-]/g, '')
                            .replace(/,/g, '');
                    }

                    numericAmount = parseFloat(numericAmount);

                    if (isNaN(numericAmount) || numericAmount === null || numericAmount === undefined) {
                        numericAmount = 0;
                    }

                    return new Intl.NumberFormat('en-LK', {
                        style: 'currency',
                        currency: 'LKR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(numericAmount);
                },

                /**
                 * Format date for display
                 */
                formatDate(dateString) {
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    } catch (error) {
                        console.warn('Invalid date format:', dateString);
                        return dateString;
                    }
                },

                /**
                 * Format date for table (DD/MM/YY)
                 */
                formatDateTable(dateString) {
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        }).replace(/\//g, '/');
                    } catch (error) {
                        console.warn('Invalid date format for table:', dateString);
                        return dateString;
                    }
                },

                /**
                 * Get month name from number
                 */
                getMonthName(monthNumber) {
                    const months = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    const monthIndex = parseInt(monthNumber) - 1;
                    return months[monthIndex] || 'Unknown';
                },

                /**
                 * Safe number conversion
                 */
                toNumber(value) {
                    if (value == null || value === '' || value === undefined) return 0;
                    if (typeof value === 'number') return value;

                    const cleaned = String(value)
                        .replace(/[^\d.-]/g, '')
                        .replace(/,/g, '');

                    const num = parseFloat(cleaned);
                    return isNaN(num) ? 0 : num;
                },

                /**
                 * Safe integer conversion
                 */
                toInt(value) {
                    return Math.floor(this.toNumber(value));
                },

                /**
                 * Create a toast notification
                 */
                showToast(message, type = 'info') {
                    const toast = document.createElement('div');
                    const bgColor = {
                        success: '#28a745',
                        error: '#dc3545',
                        warning: '#ffc107',
                        info: '#17a2b8'
                    }[type] || '#17a2b8';

                    const icon = {
                        success: 'fa-check-circle',
                        error: 'fa-exclamation-circle',
                        warning: 'fa-exclamation-triangle',
                        info: 'fa-info-circle'
                    }[type] || 'fa-info-circle';

                    toast.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            background: ${bgColor};
                            color: white;
                            padding: 12px 20px;
                            border-radius: 4px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            z-index: 999999;
                            animation: slideIn 0.3s ease-out;
                        `;

                    toast.innerHTML = `
                            <div style="display: flex; align-items: center;">
                                <i class="fas ${icon} me-2"></i>
                                <span>${message}</span>
                            </div>
                        `;

                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => toast.remove(), 300);
                    }, CONFIG.TOAST_DURATION);

                    return toast;
                },

                /**
                 * Check if printing is enabled
                 */
                checkPrintingEnabled() {
                    try {
                        const teacherReceiptSettings = localStorage.getItem('teacher_receipt_settings');
                        if (teacherReceiptSettings) {
                            const settings = JSON.parse(teacherReceiptSettings);
                            return settings.teacher_receipt_enabled === true;
                        }

                        if (typeof window.getTeacherReceiptStatus === 'function') {
                            return window.getTeacherReceiptStatus();
                        }

                        return false;
                    } catch (error) {
                        console.error('Error checking printing status:', error);
                        return false;
                    }
                },

                /**
                 * Check if email is enabled
                 */
                checkEmailEnabled() {
                    try {
                        const emailSettings = localStorage.getItem('email_settings');
                        if (emailSettings) {
                            const settings = JSON.parse(emailSettings);
                            return settings.email_enabled === true;
                        }

                        if (typeof window.getEmailStatus === 'function') {
                            return window.getEmailStatus();
                        }

                        return false;
                    } catch (error) {
                        console.error('Error checking email status:', error);
                        return false;
                    }
                },

                /**
                 * Format month year for URL
                 */
                formatMonthYearForURL(monthYear) {
                    const parts = monthYear.split(' ');
                    if (parts.length === 2) {
                        const year = parts[0];
                        const monthName = parts[1];
                        const monthMap = {
                            'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                            'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                            'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
                        };
                        const monthNumber = monthMap[monthName] || '01';
                        return `${year}-${monthNumber}`;
                    }
                    return monthYear;
                },

                /**
                 * Debounce function for performance
                 */
                debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }
            };

            // ==================== UI STATE MANAGEMENT ====================
            const ui = {
                showTableLoading(show) {
                    if (elements.tableLoadingSpinner) {
                        elements.tableLoadingSpinner.classList.toggle('d-none', !show);
                    }
                },

                showTableEmptyState(show) {
                    if (elements.tableEmptyState) {
                        elements.tableEmptyState.classList.toggle('d-none', !show);
                    }
                },

                showSalaryEmptyState(show) {
                    if (elements.salaryEmptyState) {
                        elements.salaryEmptyState.classList.toggle('d-none', !show);
                    }
                },

                updateSelectedMonthYear() {
                    if (elements.selectedMonthYear) {
                        elements.selectedMonthYear.textContent =
                            `${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`;
                    }
                    if (elements.summaryMonthYear) {
                        elements.summaryMonthYear.textContent =
                            `${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`;
                    }
                },

                enablePayButton(enable) {
                    if (elements.payTeacherBtn) {
                        elements.payTeacherBtn.disabled = !enable;
                    }
                },

                setPayButtonLoading(loading) {
                    if (elements.payTeacherBtn) {
                        if (loading) {
                            elements.payTeacherBtn.disabled = true;
                            elements.payTeacherBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
                        } else {
                            elements.payTeacherBtn.innerHTML = '<i class="fas fa-money-check-alt me-1"></i> Pay Teacher';
                        }
                    }
                }
            };

            // ==================== DATA FETCHING ====================
            /**
             * Fetch teacher data with retry logic
             */
            async function fetchTeacherData(retryCount = 0) {
                const fetchId = ++state.currentFetchId;

                // Cancel previous request if exists
                if (state.abortController) {
                    state.abortController.abort();
                }

                state.abortController = new AbortController();
                const timeoutId = setTimeout(() => {
                    state.abortController.abort();
                }, CONFIG.API_TIMEOUT);

                try {
                    ui.showTableLoading(true);

                    const url = `/api/teacher-payments/monthly-income/${utils.teacherId}/${utils.currentYear}-${utils.currentMonth}`;

                    const response = await fetch(url, {
                        signal: state.abortController.signal,
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache'
                        }
                    });

                    clearTimeout(timeoutId);

                    if (fetchId !== state.currentFetchId) {
                        return; // Newer fetch in progress
                    }

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.status === 'success') {
                        state.teacherData = convertStringValuesToNumeric(data);
                        renderAllData();
                    } else {
                        throw new Error(data.message || 'Failed to load teacher data');
                    }
                } catch (error) {
                    if (error.name === 'AbortError') {
                        console.log('Fetch aborted');
                        return;
                    }

                    console.error('Error fetching teacher data:', error);

                    if (retryCount < CONFIG.MAX_RETRIES) {
                        console.log(`Retrying... (${retryCount + 1}/${CONFIG.MAX_RETRIES})`);
                        await new Promise(resolve => setTimeout(resolve, CONFIG.RETRY_DELAY * (retryCount + 1)));
                        return fetchTeacherData(retryCount + 1);
                    }

                    ui.showTableEmptyState(true);
                    utils.showToast('Failed to load teacher data. Please try again.', 'error');
                } finally {
                    clearTimeout(timeoutId);
                    ui.showTableLoading(false);
                }
            }

            /**
             * Convert string values to numeric for consistent processing
             */
            function convertStringValuesToNumeric(data) {
                const converted = JSON.parse(JSON.stringify(data));

                // Convert top-level numeric fields
                const numericFields = [
                    'total_payments_this_month',
                    'advance_payment_this_month',
                    'net_payable',
                    'teacher_percentage',
                    'institution_percentage',
                    'teacher_share',
                    'institution_share'
                ];

                numericFields.forEach(field => {
                    if (converted[field] !== undefined && converted[field] !== null) {
                        converted[field] = utils.toNumber(converted[field]);
                    }
                });

                // Convert salary payments
                if (converted.salary_payments && Array.isArray(converted.salary_payments)) {
                    converted.salary_payments = converted.salary_payments.map(payment => ({
                        ...payment,
                        payment: utils.toNumber(payment.payment),
                        status: payment.status ? 1 : 0
                    }));
                }

                // Convert classes data
                if (converted.classes && Array.isArray(converted.classes)) {
                    converted.classes = converted.classes.map(cls => {
                        const convertedClass = {
                            ...cls,
                            total_students: utils.toInt(cls.total_students),
                            students_paid: utils.toInt(cls.students_paid)
                        };

                        if (convertedClass.payments && typeof convertedClass.payments === 'object') {
                            const convertedPayments = {};
                            Object.entries(convertedClass.payments).forEach(([date, amount]) => {
                                convertedPayments[date] = utils.toNumber(amount);
                            });
                            convertedClass.payments = convertedPayments;
                        }

                        return convertedClass;
                    });
                }

                return converted;
            }

            // ==================== RENDER FUNCTIONS ====================
            /**
             * Render all data after fetch
             */
            function renderAllData() {
                if (!state.teacherData) return;

                renderTeacherData();
                renderClassesCards();
                renderPaymentTable();
                renderSalaryPayments();
                ui.updateSelectedMonthYear();
            }

            /**
             * Render teacher data in summary section
             */
            function renderTeacherData() {
                if (!state.teacherData) return;

                const safeData = {
                    teacher_id: utils.toInt(state.teacherData.teacher_id),
                    teacher_name: state.teacherData.teacher_name || '-',
                    subject_name: state.teacherData.subject_name || '-',
                    is_salary_paid: Boolean(state.teacherData.is_salary_paid),
                    total_payments_this_month: utils.toNumber(state.teacherData.total_payments_this_month),
                    advance_payment_this_month: utils.toNumber(state.teacherData.advance_payment_this_month),
                    net_payable: utils.toNumber(state.teacherData.net_payable),
                    teacher_percentage: utils.toInt(state.teacherData.teacher_percentage),
                    institution_percentage: utils.toInt(state.teacherData.institution_percentage),
                    teacher_share: utils.toNumber(state.teacherData.teacher_share),
                    institution_share: utils.toNumber(state.teacherData.institution_share)
                };

                // Update teacher information
                if (elements.teacherNameTitle) {
                    elements.teacherNameTitle.innerHTML =
                        `<i class="fas fa-user-graduate me-1"></i> ${safeData.teacher_name}'s Income`;
                }

                if (elements.teacherId) elements.teacherId.textContent = safeData.teacher_id || '-';
                if (elements.teacherName) elements.teacherName.textContent = safeData.teacher_name || '-';
                if (elements.subjectName) elements.subjectName.textContent = safeData.subject_name || '-';

                if (elements.salaryStatus) {
                    elements.salaryStatus.textContent = safeData.is_salary_paid ? 'Salary Paid' : 'Salary Not Paid';
                    elements.salaryStatus.className = `badge bg-${safeData.is_salary_paid ? 'success' : 'warning'}`;
                }

                // Update financial summary
                if (elements.totalCollections) {
                    elements.totalCollections.textContent = utils.formatCurrency(safeData.total_payments_this_month);
                }

                if (elements.advancePayments) {
                    elements.advancePayments.textContent = utils.formatCurrency(safeData.advance_payment_this_month);
                }

                if (elements.teacherShare) {
                    elements.teacherShare.textContent = utils.formatCurrency(safeData.teacher_share);
                }

                if (elements.institutionShare) {
                    elements.institutionShare.textContent = utils.formatCurrency(safeData.institution_share);
                }

                if (elements.netPayable) {
                    elements.netPayable.textContent = utils.formatCurrency(safeData.net_payable);
                }

                // Update percentage texts
                const teacherPercentage = safeData.teacher_percentage;
                const institutionPercentage = safeData.institution_percentage;

                if (elements.teacherPercentageText) {
                    elements.teacherPercentageText.textContent = `${teacherPercentage}% of total collections`;
                }

                if (elements.institutionPercentageText) {
                    elements.institutionPercentageText.textContent = `${institutionPercentage}% of total collections`;
                }

                // Update progress bars
                if (elements.teacherPercentageBar) {
                    elements.teacherPercentageBar.style.width = `${teacherPercentage}%`;
                    const textElement = document.getElementById('teacherPercentageTextBar');
                    if (textElement) {
                        textElement.textContent = `Teacher: ${teacherPercentage}%`;
                    }
                }

                if (elements.institutionPercentageBar) {
                    elements.institutionPercentageBar.style.width = `${institutionPercentage}%`;
                    const textElement = document.getElementById('institutionPercentageTextBar');
                    if (textElement) {
                        textElement.textContent = `Institution: ${institutionPercentage}%`;
                    }
                }

                // Update Pay Teacher button
                if (elements.payTeacherBtn) {
                    if (safeData.net_payable > 0 && !safeData.is_salary_paid) {
                        elements.payTeacherBtn.disabled = false;
                        elements.payTeacherBtn.title = `Pay ${utils.formatCurrency(safeData.net_payable)}`;
                    } else {
                        elements.payTeacherBtn.disabled = true;
                        elements.payTeacherBtn.title = safeData.is_salary_paid ? 'Salary already paid' : 'No amount payable';
                    }
                }
            }

            /**
             * Render classes cards
             */
            function renderClassesCards() {
                if (!state.teacherData || !elements.classesCards) return;

                elements.classesCards.innerHTML = '';
                const teacherPercentage = utils.toInt(state.teacherData.teacher_percentage);

                state.teacherData.classes.forEach(cls => {
                    const safeClass = {
                        class_name: cls.class_name || 'Unnamed Class',
                        grade_name: cls.grade_name || 'N/A',
                        total_students: utils.toInt(cls.total_students),
                        students_paid: utils.toInt(cls.students_paid),
                        payments: cls.payments || {}
                    };

                    let totalPaid = 0;
                    Object.values(safeClass.payments).forEach(val => {
                        totalPaid += utils.toNumber(val);
                    });

                    const percentagePaid = safeClass.total_students > 0 ?
                        Math.round((safeClass.students_paid / safeClass.total_students) * 100) : 0;

                    const teacherShare = totalPaid * teacherPercentage / 100;

                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-3 mb-2';
                    card.innerHTML = `
                            <div class="card class-card h-100">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 small">${safeClass.class_name}</h6>
                                    <small class="text-muted">Grade: ${safeClass.grade_name}</small>
                                </div>
                                <div class="card-body py-2">
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">Paid</span>
                                            <span class="fw-bold small">${safeClass.students_paid}/${safeClass.total_students}</span>
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
                                            <span class="fw-bold small">${utils.formatCurrency(totalPaid)}</span>
                                        </div>
                                    </div>
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">Teacher's:</span>
                                            <span class="fw-bold text-success small">${utils.formatCurrency(teacherShare)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                    elements.classesCards.appendChild(card);
                });
            }

            /**
             * Render payment table with dynamic columns
             */
            function renderPaymentTable() {
                if (!state.teacherData || !elements.paymentTableBody) {
                    ui.showTableEmptyState(true);
                    return;
                }

                elements.paymentTableBody.innerHTML = '';
                state.allPayments = [];
                state.allGrades = [];

                const allDates = new Set();
                const gradeSet = new Set();

                state.teacherData.classes.forEach(cls => {
                    const grade = cls.grade_name;
                    if (grade) gradeSet.add(grade);

                    Object.keys(cls.payments || {}).forEach(date => {
                        allDates.add(date);
                    });
                });

                const sortedDates = Array.from(allDates).sort((a, b) => new Date(a) - new Date(b));
                state.allGrades = Array.from(gradeSet).sort();

                if (sortedDates.length === 0) {
                    ui.showTableEmptyState(true);
                    return;
                }

                ui.showTableEmptyState(false);
                renderTableHeader();

                const totals = {
                    gradeTotals: {},
                    totalCollection: 0,
                    institutionShare: 0,
                    teacherShare: 0
                };

                state.allGrades.forEach(grade => {
                    totals.gradeTotals[grade] = 0;
                });

                const teacherPercentage = utils.toInt(state.teacherData.teacher_percentage);
                const institutionPercentage = utils.toInt(state.teacherData.institution_percentage);

                sortedDates.forEach(date => {
                    const rowData = {
                        date: date,
                        gradePayments: {},
                        totalCollection: 0,
                        institutionShare: 0,
                        teacherShare: 0
                    };

                    state.allGrades.forEach(grade => {
                        rowData.gradePayments[grade] = 0;
                    });

                    state.teacherData.classes.forEach(cls => {
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

                    state.allPayments.push(rowData);

                    const row = document.createElement('tr');
                    let rowHTML = `<td class="fw-bold">${utils.formatDateTable(date)}</td>`;

                    state.allGrades.forEach(grade => {
                        const amount = rowData.gradePayments[grade];
                        rowHTML += `<td>${amount > 0 ? utils.formatCurrency(amount) : '-'}</td>`;
                    });

                    rowHTML += `
                            <td class="fw-bold bg-light text-primary">${utils.formatCurrency(rowData.totalCollection)}</td>
                            <td class="bg-light text-secondary">${utils.formatCurrency(rowData.institutionShare)}</td>
                            <td class="fw-bold bg-success text-white">${utils.formatCurrency(rowData.teacherShare)}</td>
                        `;

                    row.innerHTML = rowHTML;
                    elements.paymentTableBody.appendChild(row);
                });

                renderTableFooter(totals);
            }

            /**
             * Render table header based on grades
             */
            function renderTableHeader() {
                if (!elements.paymentTableHeader) return;

                elements.paymentTableHeader.innerHTML = '';

                const teacherPercentage = utils.toInt(state.teacherData.teacher_percentage);
                const institutionPercentage = utils.toInt(state.teacherData.institution_percentage);

                const firstRow = document.createElement('tr');
                firstRow.innerHTML = `<th rowspan="2">Date (DD/MM/YY)</th>`;

                state.allGrades.forEach(grade => {
                    firstRow.innerHTML += `<th>Grade ${grade}</th>`;
                });

                firstRow.innerHTML += `
                        <th rowspan="2" class="bg-light text-primary">Total Collection</th>
                        <th colspan="2" class="text-center" rowspan="2">Percentage Split</th>
                    `;

                elements.paymentTableHeader.appendChild(firstRow);

                const secondRow = document.createElement('tr');
                elements.paymentTableHeader.appendChild(secondRow);

                // Update percentage labels
                setTimeout(() => {
                    const tableHeaders = document.querySelectorAll('#paymentTable thead tr:last-child th');
                    if (tableHeaders.length > state.allGrades.length + 2) {
                        tableHeaders[state.allGrades.length + 1].textContent = `${institutionPercentage}%`;
                        tableHeaders[state.allGrades.length + 1].className = 'bg-light text-secondary';

                        tableHeaders[state.allGrades.length + 2].textContent = `${teacherPercentage}%`;
                        tableHeaders[state.allGrades.length + 2].className = 'bg-success text-white';
                    }
                }, 10);
            }

            /**
             * Render table footer with totals
             */
            function renderTableFooter(totals) {
                if (!elements.paymentTableFooter) return;

                elements.paymentTableFooter.innerHTML = '';

                const footerRow = document.createElement('tr');
                footerRow.innerHTML = `<td class="fw-bold">Totals</td>`;

                state.allGrades.forEach(grade => {
                    footerRow.innerHTML += `<td class="fw-bold">${utils.formatCurrency(totals.gradeTotals[grade] || 0)}</td>`;
                });

                footerRow.innerHTML += `
                        <td class="fw-bold bg-light text-primary">${utils.formatCurrency(totals.totalCollection)}</td>
                        <td class="fw-bold bg-light text-secondary">${utils.formatCurrency(totals.institutionShare)}</td>
                        <td class="fw-bold bg-success text-white">${utils.formatCurrency(totals.teacherShare)}</td>
                    `;

                elements.paymentTableFooter.appendChild(footerRow);
            }

            /**
             * Render salary payments history
             */
            function renderSalaryPayments() {
                if (!state.teacherData || !elements.salaryPaymentsTableBody) return;

                elements.salaryPaymentsTableBody.innerHTML = '';

                if (!state.teacherData.salary_payments || state.teacherData.salary_payments.length === 0) {
                    ui.showSalaryEmptyState(true);
                    return;
                }

                ui.showSalaryEmptyState(false);

                state.teacherData.salary_payments.forEach(payment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>${utils.formatDate(payment.date)}</td>
                            <td class="fw-bold">${utils.formatCurrency(payment.payment)}</td>
                            <td><span class="badge bg-info">${payment.reason_code || 'N/A'}</span></td>
                            <td>${payment.payment_for || 'N/A'}</td>
                            <td>
                                <span class="badge ${payment.status == 1 ? 'bg-success' : 'bg-warning'}">
                                    ${payment.status == 1 ? 'Paid' : 'Deleted'}
                                </span>
                            </td>
                        `;

                    elements.salaryPaymentsTableBody.appendChild(row);
                });
            }

            // ==================== PAYMENT PROCESSING ====================
            /**
             * Setup pay teacher button with event listener
             */
            function setupPayTeacherButton() {
                if (!elements.payTeacherBtn) return;

                elements.payTeacherBtn.addEventListener('click', function () {
                    if (!state.teacherData || state.teacherData.net_payable <= 0 || state.teacherData.is_salary_paid) {
                        return;
                    }

                    const amount = state.teacherData.net_payable;
                    const teacherName = state.teacherData.teacher_name;
                    const teacherId = state.teacherData.teacher_id;
                    const monthYear = `${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`;
                    const formattedAmount = utils.formatCurrency(amount);

                    showPaymentConfirmation(teacherName, formattedAmount, monthYear, function (confirmed) {
                        if (confirmed) {
                            processPayment(teacherId, teacherName, amount, monthYear);
                        }
                    });
                });
            }

            /**
             * Show payment confirmation modal
             */
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

            /**
             * Process payment API call
             */
            function processPayment(teacherId, teacherName, amount, monthYear) {
                if (state.isProcessingPayment) return;

                state.isProcessingPayment = true;
                ui.setPayButtonLoading(true);

                showPaymentProcessing(teacherName, amount, monthYear);

                const paymentData = {
                    teacher_id: teacherId,
                    payment: amount,
                    reason_code: 'salary',
                    month_year: monthYear
                };

                fetch('/api/teacher-payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': utils.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(paymentData)
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

                            const isPrintingEnabled = utils.checkPrintingEnabled();
                            const isEmailEnabled = utils.checkEmailEnabled();

                            // Print salary slip if enabled
                            if (isPrintingEnabled) {
                                setTimeout(() => {
                                    openSalarySlip(teacherId, utils.currentYear, utils.currentMonth);
                                    console.log('✅ Salary slip printing initiated');
                                }, 500);
                            }

                            // Send email if enabled
                            if (isEmailEnabled) {
                                sendPaymentReportToTeacher(teacherId, monthYear);
                                console.log('✅ Email notification initiated');
                            }

                            showPaymentSuccess(data, teacherId, teacherName, amount, monthYear, isPrintingEnabled, isEmailEnabled);

                            // Refresh data
                            setTimeout(() => {
                                fetchTeacherData();
                            }, CONFIG.REFRESH_DELAY);

                        } else {
                            throw new Error(data.message || 'Payment failed');
                        }
                    })
                    .catch(error => {
                        hidePaymentProcessing();
                        showPaymentError(error.message, teacherName, amount);
                        state.isProcessingPayment = false;
                        ui.setPayButtonLoading(false);
                    });
            }

            /**
             * Send payment report to teacher via email
             */
            function sendPaymentReportToTeacher(teacherId, monthYear) {
                const formattedMonthYear = utils.formatMonthYearForURL(monthYear);

                fetch(`/send-mail/${teacherId}/${formattedMonthYear}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': utils.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Report email sent successfully:', data);
                        } else {
                            console.warn('Report email may not have been sent:', data);
                        }
                    })
                    .catch(error => {
                        console.error('Error sending report email:', error);
                    });
            }

            /**
             * Show payment processing overlay
             */
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

                const isPrintingEnabled = utils.checkPrintingEnabled();
                const isEmailEnabled = utils.checkEmailEnabled();

                let featuresMessage = '';
                if (isPrintingEnabled && isEmailEnabled) {
                    featuresMessage = 'Payment slip will auto-print and report will be emailed';
                } else if (isPrintingEnabled) {
                    featuresMessage = 'Payment slip will auto-print';
                } else if (isEmailEnabled) {
                    featuresMessage = 'Report will be emailed';
                } else {
                    featuresMessage = 'Payment will be processed (no additional actions)';
                }

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
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span style="color: #666;">Amount:</span>
                                    <strong>${utils.formatCurrency(amount)}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #666;">Period:</span>
                                    <strong>${monthYear}</strong>
                                </div>
                            </div>

                            <div style="
                                background: #e8f4fd;
                                padding: 8px;
                                border-radius: 4px;
                                margin-top: 15px;
                                border-left: 3px solid #007bff;
                            ">
                                <p style="margin: 0; color: #0056b3; font-size: 12px;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ${featuresMessage}
                                </p>
                            </div>

                            <p style="color: #666; font-size: 13px; margin: 10px 0 0 0;">
                                Please wait...
                            </p>
                        </div>
                    `;

                document.body.appendChild(overlay);
            }

            /**
             * Show payment success modal
             */
            function showPaymentSuccess(data, teacherId, teacherName, amount, monthYear, isPrintingEnabled, isEmailEnabled) {
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

                const formattedAmount = utils.formatCurrency(amount);
                const paymentDate = new Date().toLocaleTimeString('en-LK', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                let printStatusMessage = '';
                let emailStatusMessage = '';
                let actionsHTML = '';

                if (isPrintingEnabled) {
                    printStatusMessage = `
                            <div style="
                                background: #d4edda;
                                padding: 8px;
                                border-radius: 4px;
                                margin-bottom: 8px;
                                border-left: 3px solid #28a745;
                            ">
                                <p style="margin: 0; color: #155724; font-size: 12px;">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Payment slip has been printed
                                </p>
                            </div>
                        `;
                    actionsHTML += `
                            <button id="printAgainBtn" style="
                                background: #007bff;
                                color: white;
                                border: none;
                                padding: 8px 15px;
                                border-radius: 4px;
                                cursor: pointer;
                                font-size: 14px;
                                flex: 1;
                            ">
                                <i class="fas fa-print me-1"></i> Print Again
                            </button>
                        `;
                } else {
                    printStatusMessage = `
                            <div style="
                                background: #f8f9fa;
                                padding: 8px;
                                border-radius: 4px;
                                margin-bottom: 8px;
                                border-left: 3px solid #6c757d;
                            ">
                                <p style="margin: 0; color: #6c757d; font-size: 12px;">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Printing disabled in settings
                                </p>
                            </div>
                        `;
                }

                if (isEmailEnabled) {
                    emailStatusMessage = `
                            <div style="
                                background: #e8f4fd;
                                padding: 8px;
                                border-radius: 4px;
                                margin-bottom: 15px;
                                border-left: 3px solid #007bff;
                            ">
                                <p style="margin: 0; color: #0056b3; font-size: 12px;">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Report has been emailed to teacher
                                </p>
                            </div>
                        `;
                    actionsHTML += `
                            <button id="emailAgainBtn" style="
                                background: #17a2b8;
                                color: white;
                                border: none;
                                padding: 8px 15px;
                                border-radius: 4px;
                                cursor: pointer;
                                font-size: 14px;
                                flex: 1;
                            ">
                                <i class="fas fa-envelope me-1"></i> Resend Email
                            </button>
                        `;
                } else {
                    emailStatusMessage = `
                            <div style="
                                background: #f8f9fa;
                                padding: 8px;
                                border-radius: 4px;
                                margin-bottom: 15px;
                                border-left: 3px solid #6c757d;
                            ">
                                <p style="margin: 0; color: #6c757d; font-size: 12px;">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Email disabled in settings
                                </p>
                            </div>
                        `;
                }

                actionsHTML += `
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
                    `;

                modal.innerHTML = `
                        <div style="
                            background: white;
                            padding: 20px;
                            border-radius: 8px;
                            max-width: 400px;
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

                            <!-- Status messages -->
                            ${printStatusMessage}
                            ${emailStatusMessage}

                            <div style="display: flex; gap: 10px; margin-top: 20px;">
                                ${actionsHTML}
                            </div>
                        </div>
                    `;

                document.body.appendChild(modal);

                // Event listeners
                if (isPrintingEnabled) {
                    document.getElementById('printAgainBtn').addEventListener('click', function () {
                        openSalarySlip(teacherId, utils.currentYear, utils.currentMonth);
                        utils.showToast('Printing salary slip again...', 'info');
                    });
                }

                if (isEmailEnabled) {
                    document.getElementById('emailAgainBtn').addEventListener('click', function () {
                        sendPaymentReportToTeacher(teacherId, monthYear);
                        utils.showToast('Report email sent again', 'success');
                    });
                }

                document.getElementById('closeBtn').addEventListener('click', function () {
                    modal.remove();
                    state.isProcessingPayment = false;
                    ui.setPayButtonLoading(false);
                });

                // Auto close after timeout
                setTimeout(() => {
                    if (document.getElementById('paymentSuccess')) {
                        modal.remove();
                        state.isProcessingPayment = false;
                        ui.setPayButtonLoading(false);
                    }
                }, CONFIG.AUTO_CLOSE_TIMEOUT);
            }

            /**
             * Open salary slip for printing
             */
            function openSalarySlip(teacherId, year, month) {
                const formattedMonth = month.toString().padStart(2, '0');
                const yearMonth = `${year}-${formattedMonth}`;
                const salarySlipUrl = `/teacher-payment/salary-slip/${teacherId}/${yearMonth}?autoPrint=true&timestamp=${Date.now()}`;

                const printWindow = window.open(salarySlipUrl, '_blank', 'width=900,height=700,scrollbars=yes');

                if (printWindow) {
                    printWindow.focus();

                    printWindow.onload = function () {
                        setTimeout(() => {
                            printWindow.print();
                        }, CONFIG.PRINT_WINDOW_DELAY);
                    };
                }
            }

            /**
             * Hide payment processing overlay
             */
            function hidePaymentProcessing() {
                const overlay = document.getElementById('paymentProcessing');
                if (overlay) {
                    overlay.remove();
                }
            }

            /**
             * Show payment error modal
             */
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
                                    <strong>${utils.formatCurrency(amount)}</strong>
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
                    state.isProcessingPayment = false;
                    ui.setPayButtonLoading(false);
                });

                setTimeout(() => {
                    if (document.getElementById('paymentError')) {
                        modal.remove();
                        state.isProcessingPayment = false;
                        ui.setPayButtonLoading(false);
                    }
                }, 10000);
            }

            // ==================== EXPORT FUNCTIONS ====================
            /**
             * Setup Excel export button
             */
            function setupExportTableExcel() {
                if (!elements.exportTableExcelBtn) return;

                elements.exportTableExcelBtn.addEventListener('click', utils.debounce(function () {
                    if (!state.teacherData || state.allPayments.length === 0) {
                        utils.showToast('No data to export', 'warning');
                        return;
                    }

                    try {
                        const teacherPercentage = utils.toInt(state.teacherData.teacher_percentage);
                        const institutionPercentage = utils.toInt(state.teacherData.institution_percentage);

                        const exportData = state.allPayments.map(payment => {
                            const rowData = {
                                'Date': utils.formatDateTable(payment.date)
                            };

                            state.allGrades.forEach(grade => {
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

                        state.allGrades.forEach(grade => {
                            totalsRow[`Grade ${grade}`] = state.allPayments.reduce((sum, p) => sum + (p.gradePayments[grade] || 0), 0);
                        });

                        totalsRow['Total Collection'] = state.allPayments.reduce((sum, p) => sum + p.totalCollection, 0);
                        totalsRow[`Institution Share (${institutionPercentage}%)`] = state.allPayments.reduce((sum, p) => sum + p.institutionShare, 0);
                        totalsRow[`Teacher Share (${teacherPercentage}%)`] = state.allPayments.reduce((sum, p) => sum + p.teacherShare, 0);

                        exportData.push(totalsRow);

                        const ws = XLSX.utils.json_to_sheet(exportData);
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, 'Teacher Payments');

                        const filename = `${state.teacherData.teacher_name}_${utils.getMonthName(utils.currentMonth)}_${utils.currentYear}_Payments.xlsx`;
                        XLSX.writeFile(wb, filename);

                        utils.showToast('Excel file exported successfully', 'success');
                    } catch (error) {
                        console.error('Error exporting to Excel:', error);
                        utils.showToast('Failed to export Excel file', 'error');
                    }
                }, 300));
            }

            /**
             * Setup PDF export button
             */
            function setupExportTablePdf() {
                if (!elements.exportTablePdfBtn) return;

                elements.exportTablePdfBtn.addEventListener('click', utils.debounce(function () {
                    if (!state.teacherData || state.allPayments.length === 0) {
                        utils.showToast('No data to export', 'warning');
                        return;
                    }

                    try {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('landscape');

                        const teacherPercentage = utils.toInt(state.teacherData.teacher_percentage);
                        const institutionPercentage = utils.toInt(state.teacherData.institution_percentage);

                        doc.setFontSize(14);
                        doc.text(`${state.teacherData.teacher_name} - Payment Report`, 14, 10);
                        doc.setFontSize(10);
                        doc.text(`Period: ${utils.getMonthName(utils.currentMonth)} ${utils.currentYear}`, 14, 16);
                        doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 22);

                        doc.setFontSize(11);
                        doc.text('Summary', 14, 30);
                        doc.setFontSize(9);
                        doc.text(`Total Collections: ${utils.formatCurrency(state.teacherData.total_payments_this_month || 0)}`, 14, 36);
                        doc.text(`Teacher's Share (${teacherPercentage}%): ${utils.formatCurrency(state.teacherData.teacher_share || 0)}`, 14, 41);
                        doc.text(`Advance Payments: ${utils.formatCurrency(state.teacherData.advance_payment_this_month || 0)}`, 14, 46);
                        doc.text(`Net Payable: ${utils.formatCurrency(state.teacherData.net_payable || 0)}`, 14, 51);

                        const headers = ['Date'];
                        state.allGrades.forEach(grade => {
                            headers.push(`Grade ${grade}`);
                        });
                        headers.push('Total', `Inst (${institutionPercentage}%)`, `Teach (${teacherPercentage}%)`);

                        const tableData = state.allPayments.map(payment => {
                            const row = [utils.formatDateTable(payment.date)];

                            state.allGrades.forEach(grade => {
                                row.push(utils.formatCurrency(payment.gradePayments[grade] || 0));
                            });

                            row.push(
                                utils.formatCurrency(payment.totalCollection),
                                utils.formatCurrency(payment.institutionShare),
                                utils.formatCurrency(payment.teacherShare)
                            );

                            return row;
                        });

                        const totalsRow = ['TOTALS'];
                        state.allGrades.forEach(grade => {
                            const total = state.allPayments.reduce((sum, p) => sum + (p.gradePayments[grade] || 0), 0);
                            totalsRow.push(utils.formatCurrency(total));
                        });

                        const totalCollection = state.allPayments.reduce((sum, p) => sum + p.totalCollection, 0);
                        const totalInstitutionShare = state.allPayments.reduce((sum, p) => sum + p.institutionShare, 0);
                        const totalTeacherShare = state.allPayments.reduce((sum, p) => sum + p.teacherShare, 0);

                        totalsRow.push(
                            utils.formatCurrency(totalCollection),
                            utils.formatCurrency(totalInstitutionShare),
                            utils.formatCurrency(totalTeacherShare)
                        );

                        tableData.push(totalsRow);

                        doc.autoTable({
                            head: [headers],
                            body: tableData,
                            startY: 55,
                            styles: { fontSize: 7 },
                            headStyles: { fillColor: [41, 128, 185] },
                            columnStyles: {
                                0: { fontStyle: 'bold', cellWidth: 25 },
                                [state.allGrades.length + 1]: { fontStyle: 'bold', textColor: [13, 110, 253] },
                                [state.allGrades.length + 2]: { textColor: [108, 117, 125] },
                                [state.allGrades.length + 3]: { fontStyle: 'bold', fillColor: [39, 174, 96], textColor: [255, 255, 255] }
                            }
                        });

                        const filename = `${state.teacherData.teacher_name}_${utils.getMonthName(utils.currentMonth)}_${utils.currentYear}_Payments.pdf`;
                        doc.save(filename);

                        utils.showToast('PDF file exported successfully', 'success');
                    } catch (error) {
                        console.error('Error exporting to PDF:', error);
                        utils.showToast('Failed to export PDF file', 'error');
                    }
                }, 300));
            }

            // ==================== INITIALIZATION ====================
            /**
             * Initialize the application
             */
            function init() {
                console.log('Initializing Teacher Income Details...');

                // Setup event listeners
                setupPayTeacherButton();
                setupExportTableExcel();
                setupExportTablePdf();

                // Load initial data
                fetchTeacherData();

                // Update month year display
                ui.updateSelectedMonthYear();

                console.log('Teacher Income Details initialized successfully');
            }

            // Start application when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            // Global error handling
            window.addEventListener('error', function (event) {
                console.error('Global error:', event.error);
                utils.showToast('An unexpected error occurred', 'error');
            });

            window.addEventListener('unhandledrejection', function (event) {
                console.error('Unhandled promise rejection:', event.reason);
                utils.showToast('A network error occurred', 'error');
            });

        })();
    </script>
@endpush