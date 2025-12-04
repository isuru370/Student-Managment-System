@extends('layouts.app')

@section('title', 'Student Payments')
@section('page-title', 'Student Payments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Student Payments</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2" style="background: linear-gradient(45deg, #4e73df, #2e59d9);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Total Students</div>
                                <div class="h5 mb-0 font-weight-bold text-white" id="totalStudents">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2" style="background: linear-gradient(45deg, #1cc88a, #17a673);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Total Payments</div>
                                <div class="h5 mb-0 font-weight-bold text-white" id="totalPayments">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2" style="background: linear-gradient(45deg, #36b9cc, #2c9faf);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Total Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-white" id="totalAmount">Rs. 0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rupee-sign fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2" style="background: linear-gradient(45deg, #f6c23e, #f4b619);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                    Classes</div>
                                <div class="h5 mb-0 font-weight-bold text-white" id="totalClasses">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chalkboard-teacher fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filter Payments
                </h6>
                <div class="mt-2 mt-md-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="resetDate">
                        <i class="fas fa-redo me-1"></i>Reset
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="payment_date" class="form-label fw-bold">Select Date</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" 
                                   class="form-control" 
                                   id="payment_date" 
                                   name="payment_date"
                                   value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" id="loadPayments">
                            <i class="fas fa-search me-1"></i> View
                        </button>
                    </div>
                    {{-- <div class="col-md-2">
                        <button type="button" class="btn btn-success w-100" id="exportBtn">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                    </div> --}}
                    <div class="col-md-5">
                        <div class="alert alert-info mb-0 p-2">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Showing payments for: <span id="currentDateLabel" class="fw-bold">{{ date('F d, Y') }}</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>Payment Details
                </h6>
                <span class="badge bg-primary" id="summaryBadge">
                    <i class="fas fa-chart-bar me-1"></i>
                    <span id="tableSummary">No data</span>
                </span>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive" id="paymentsTableContainer">
                    <!-- Table will be loaded here by JavaScript -->
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar fa-2x mb-3 opacity-25"></i>
                        <h6 class="text-secondary">Select a date to view payments</h6>
                        <p class="text-muted small">Click "View Payments" to load payment details for the selected date</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            border-radius: 0.35rem;
            border: none;
        }
        
        .card-header {
            border-radius: 0.35rem 0.35rem 0 0 !important;
            padding: 0.75rem 1.25rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        /* Compact Table Styling */
        .payment-cell {
            min-width: 140px;
            vertical-align: top;
            padding: 4px 6px !important;
        }

        .payment-item {
            background-color: #f8f9fa;
            border-radius: 3px;
            padding: 5px;
            border-left: 2px solid #4e73df;
            font-size: 0.75rem;
            margin-bottom: 3px;
        }

        .payment-item:hover {
            background-color: #e9ecef;
        }

        .table {
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #4e73df;
            color: white;
            border-color: #4e73df;
            position: sticky;
            top: 0;
            z-index: 10;
            padding: 6px 8px !important;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .table-bordered {
            border-color: #e3e6f0;
        }

        .table-bordered th,
        .table-bordered td {
            border-color: #e3e6f0;
            padding: 6px 8px !important;
        }

        .class-header {
            background-color: #f8f9fc;
            font-weight: 600;
            position: sticky;
            left: 0;
            z-index: 5;
            padding: 6px 8px !important;
            font-size: 0.75rem;
        }

        .student-header {
            background-color: #e3f2fd;
            min-width: 130px;
            font-size: 0.75rem;
        }

        .student-header div {
            font-size: 0.75rem;
            line-height: 1.2;
        }

        .student-header small {
            font-size: 0.7rem;
            line-height: 1.1;
        }

        .total-cell {
            background-color: #1cc88a !important;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .grand-total {
            background-color: #4e73df !important;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .amount-badge {
            background-color: #4e73df;
            color: white;
            padding: 1px 5px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        /* Reduced icon sizes */
        .fa-xs {
            font-size: 0.7rem;
        }
        
        .fa-sm {
            font-size: 0.8rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .payment-cell {
                min-width: 120px;
            }
            
            .table {
                font-size: 0.7rem;
            }
            
            .student-header {
                min-width: 110px;
            }
            
            .payment-item {
                padding: 3px;
                font-size: 0.7rem;
            }
        }
        
        /* Table container with reduced height */
        #paymentsTableContainer .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        
        /* Form and button adjustments */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .form-control-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
        }
        
        /* Alert text size */
        .alert {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
    </style>
@endpush

@push('scripts')
    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get DOM elements
            const paymentDateInput = document.getElementById('payment_date');
            const loadPaymentsBtn = document.getElementById('loadPayments');
            const resetDateBtn = document.getElementById('resetDate');
            const paymentsTableContainer = document.getElementById('paymentsTableContainer');
            const currentDateLabel = document.getElementById('currentDateLabel');
            const tableSummary = document.getElementById('tableSummary');
            
            // Stats elements
            const totalStudentsEl = document.getElementById('totalStudents');
            const totalPaymentsEl = document.getElementById('totalPayments');
            const totalAmountEl = document.getElementById('totalAmount');
            const totalClassesEl = document.getElementById('totalClasses');
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Format date functions
            function formatDate(date) {
                const d = new Date(date);
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
            
            function formatDateDisplay(date) {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric',
                    year: 'numeric' 
                });
            }
            
            // Update date label
            function updateDateLabel(date) {
                currentDateLabel.textContent = formatDateDisplay(date);
            }
            
            // Update stats cards
            function updateStats(data) {
                if (data.status === 'success') {
                    const payments = data.data || [];
                    const uniqueStudents = new Set();
                    const uniqueClasses = new Set();
                    let totalAmount = 0;
                    
                    payments.forEach(payment => {
                        uniqueStudents.add(payment.student.custom_id);
                        if (payment.student_class) {
                            uniqueClasses.add(payment.student_class.class_name);
                        }
                        totalAmount += parseFloat(payment.amount) || 0;
                    });
                    
                    totalStudentsEl.textContent = uniqueStudents.size;
                    totalPaymentsEl.textContent = payments.length;
                    // REMOVED .toLocaleString() to avoid comma formatting
                    totalAmountEl.textContent = `Rs. ${totalAmount}`;
                    totalClassesEl.textContent = uniqueClasses.size;
                    
                    // Update table summary
                    tableSummary.textContent = `${payments.length} payments • ${uniqueStudents.size} students`;
                } else {
                    // Reset stats if no data
                    totalStudentsEl.textContent = '0';
                    totalPaymentsEl.textContent = '0';
                    totalAmountEl.textContent = 'Rs. 0';
                    totalClassesEl.textContent = '0';
                    tableSummary.textContent = 'No data';
                }
            }
            
            // Load payments for selected date
            function loadPayments(date) {
                // Show loading indicator
                paymentsTableContainer.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-primary small">Loading payment details...</p>
                    </div>
                `;
                
                // Make API request using fetch
                fetch(`/api/payments/by-date/${date}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    updateStats(data);
                    if (data.status === 'success' && data.data.length > 0) {
                        renderTable(data.data);
                    } else {
                        paymentsTableContainer.innerHTML = `
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-receipt fa-2x mb-2 opacity-25"></i>
                                <h6 class="text-secondary mb-1">No payments found</h6>
                                <p class="text-muted small">No payments were recorded for this date</p>
                            </div>
                        `;
                        tableSummary.textContent = 'No data available';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    paymentsTableContainer.innerHTML = `
                        <div class="alert alert-danger text-center py-2">
                            <i class="fas fa-exclamation-triangle me-2 fa-sm"></i>
                            <span class="small">Error loading data. Please try again.</span>
                        </div>
                    `;
                    tableSummary.textContent = 'Error loading data';
                    // Reset stats on error
                    totalStudentsEl.textContent = '0';
                    totalPaymentsEl.textContent = '0';
                    totalAmountEl.textContent = 'Rs. 0';
                    totalClassesEl.textContent = '0';
                });
            }
            
            // Render the table with data
            function renderTable(data) {
                // Group data by class and student
                const groupedData = {};
                const students = new Map();
                const classes = new Map();
                
                // Process the data
                data.forEach(payment => {
                    const className = payment.student_class?.class_name || 'Unassigned';
                    const studentId = payment.student?.custom_id || 'Unknown';
                    const studentName = payment.student ? 
                        `${payment.student.first_name} ` : 
                        'Unknown Student';
                    
                    // Add to students map
                    if (!students.has(studentId)) {
                        students.set(studentId, {
                            id: studentId,
                            name: studentName,
                            total: 0
                        });
                    }
                    
                    // Add to classes map
                    if (!classes.has(className)) {
                        classes.set(className, {
                            name: className,
                            total: 0
                        });
                    }
                    
                    // Group data by class and student
                    if (!groupedData[className]) {
                        groupedData[className] = {};
                    }
                    
                    if (!groupedData[className][studentId]) {
                        groupedData[className][studentId] = [];
                    }
                    
                    groupedData[className][studentId].push({
                        amount: parseFloat(payment.amount) || 0,
                        payment_for: payment.payment_for || 'N/A',
                        id: payment.id
                    });
                    
                    // Calculate totals
                    const amount = parseFloat(payment.amount) || 0;
                    students.get(studentId).total += amount;
                    classes.get(className).total += amount;
                });
                
                // Create table HTML
                let html = `
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-sm" id="paymentsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="class-header" style="min-width: 150px; position: sticky; left: 0; z-index: 20;">
                                        <i class="fas fa-chalkboard me-1 fa-xs"></i>Class / Student
                                    </th>`;
                
                // Add student headers
                students.forEach(student => {
                    html += `<th class="text-center student-header" style="min-width: 130px;">
                                <div class="fw-bold">${student.id}</div>
                                <small class="text-muted d-block">${student.name}</small>
                            </th>`;
                });
                
                html += `<th class="text-center total-cell" style="min-width: 100px; position: sticky; right: 0;">
                            <i class="fas fa-calculator me-1 fa-xs"></i>Total
                        </th>
                        </tr>
                    </thead>
                    <tbody>`;
                
                // Add rows for each class
                classes.forEach((classInfo, className) => {
                    html += `<tr>
                                <td class="class-header fw-bold" style="position: sticky; left: 0; background-color: #f8f9fc;">
                                    <i class="fas fa-graduation-cap me-1 fa-xs"></i>${className}
                                </td>`;
                    
                    let classRowTotal = 0;
                    students.forEach((student, studentId) => {
                        const payments = groupedData[className] ? (groupedData[className][studentId] || []) : [];
                        html += `<td class="payment-cell" style="background-color: white;">`;
                        
                        if (payments.length > 0) {
                            let studentClassTotal = 0;
                            payments.forEach(payment => {
                                html += `
                                    <div class="payment-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-info" style="font-size: 0.65rem; padding: 0.15em 0.4em;">${payment.payment_for}</span>
                                            <!-- REMOVED .toLocaleString() to avoid comma formatting -->
                                            <span class="fw-bold amount-badge">Rs.${payment.amount}</span>
                                        </div>
                                    </div>`;
                                studentClassTotal += payment.amount;
                            });
                            // Add student's total for this class
                            html += `<div class="mt-1 pt-1 border-top text-end">
                                        <small class="text-primary fw-bold">
                                            <i class="fas fa-coins me-1 fa-xs"></i>
                                            <!-- REMOVED .toLocaleString() to avoid comma formatting -->
                                            Rs.${studentClassTotal}
                                        </small>
                                    </div>`;
                            
                            classRowTotal += studentClassTotal;
                        } else {
                            html += `<span class="text-muted small">-</span>`;
                        }
                        
                        html += `</td>`;
                    });
                    
                    // Class total column
                    html += `<td class="total-cell text-center fw-bold" style="position: sticky; right: 0; z-index: 5;">
                                <i class="fas fa-money-bill-wave me-1 fa-xs"></i>
                                <!-- REMOVED .toLocaleString() to avoid comma formatting -->
                                Rs.${classInfo.total}
                            </td>`;
                    
                    html += `</tr>`;
                });
                
                // Add totals row
                html += `<tr class="table-primary">
                            <td class="fw-bold grand-total" style="position: sticky; left: 0;">
                                <i class="fas fa-chart-line me-2 fa-xs"></i>Student Total
                            </td>`;
                
                let grandTotal = 0;
                students.forEach(student => {
                    html += `<td class="text-center fw-bold grand-total">
                                <i class="fas fa-user-check me-1 fa-xs"></i>
                                <!-- REMOVED .toLocaleString() to avoid comma formatting -->
                                Rs.${student.total}
                            </td>`;
                    grandTotal += student.total;
                });
                
                // Grand total column
                html += `<td class="text-center fw-bold grand-total" style="position: sticky; right: 0;">
                            <i class="fas fa-trophy me-1 fa-xs"></i>
                            <!-- REMOVED .toLocaleString() to avoid comma formatting -->
                            Rs.${grandTotal}
                        </td>`;
                
                html += `</tr>
                        </tbody>
                    </table>
                </div>`;
                
                paymentsTableContainer.innerHTML = html;
            }
            
            // Event Listeners
            loadPaymentsBtn.addEventListener('click', function() {
                const selectedDate = paymentDateInput.value;
                if (selectedDate) {
                    updateDateLabel(selectedDate);
                    loadPayments(selectedDate);
                } else {
                    alert('Please select a date');
                }
            });
            
            resetDateBtn.addEventListener('click', function() {
                const today = new Date();
                const formattedDate = formatDate(today);
                paymentDateInput.value = formattedDate;
                updateDateLabel(today);
                loadPayments(formattedDate);
            });
            
            // Date input change
            paymentDateInput.addEventListener('change', function() {
                if (this.value) {
                    updateDateLabel(this.value);
                }
            });
            
            // Keyboard support
            paymentDateInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    loadPaymentsBtn.click();
                }
            });
            
            // Auto-load for current date on page load
            // Set current date and trigger load
            const today = new Date();
            const formattedDate = formatDate(today);
            paymentDateInput.value = formattedDate;
            updateDateLabel(today);
            
            // Small delay to ensure DOM is fully loaded
            setTimeout(() => {
                loadPayments(formattedDate);
            }, 100);
        });
    </script>
@endpush