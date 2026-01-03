@extends('layouts.app')

@section('title', 'Teacher Payment Matrix')
@section('page-title', 'Teacher Payment Matrix')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher_payment.index') }}">Teacher Payments</a></li>
    <li class="breadcrumb-item active">Payment Matrix</li>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<input type="hidden" id="teacherId" value="{{ $teacherId }}">

<!-- Teacher Summary -->
<div class="row mb-4" id="teacherSummary" style="display: none;">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Teacher
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800" id="teacherNameDisplay">-</div>
                        <div class="text-muted small mt-1" id="subjectNameDisplay">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-start border-info border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            Period
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800" id="periodDisplay">-</div>
                        <div class="text-muted small mt-1" id="classesDisplay">0 classes</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Students
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 me-3 fw-bold text-gray-800" id="totalStudentsDisplay">0</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm me-2">
                                    <div id="paymentProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted small mt-1">
                            <span id="paidStudentsDisplay" class="text-success">0 paid</span> • 
                            <span id="unpaidStudentsDisplay" class="text-danger">0 unpaid</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Collection
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800" id="totalCollectionDisplay">Rs 0</div>
                        <div class="text-muted small mt-1">
                            <i class="fas fa-percentage"></i> 
                            Payment Rate: <span id="paymentRateDisplay">0%</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Month Filter -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-primary text-white py-3">
        <h6 class="mb-0 fw-bold">
            <i class="fas fa-filter me-2"></i>Select Month
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">Year</label>
                    <select class="form-select" id="yearFilter">
                        @php
                            $currentYear = date('Y');
                            $years = range($currentYear, $currentYear - 5);
                        @endphp
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">Month</label>
                    <select class="form-select" id="monthFilter">
                        @php
                            $months = [
                                '01' => 'January', '02' => 'February', '03' => 'March',
                                '04' => 'April', '05' => 'May', '06' => 'June',
                                '07' => 'July', '08' => 'August', '09' => 'September',
                                '10' => 'October', '11' => 'November', '12' => 'December'
                            ];
                        @endphp
                        @foreach($months as $key => $month)
                            <option value="{{ $key }}" {{ $key == date('m') ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">Items per page</label>
                    <select class="form-select" id="perPageFilter">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">Action</label>
                    <button class="btn btn-primary w-100" onclick="loadData()" id="loadBtn">
                        <i class="fas fa-sync-alt me-2"></i>Load Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading State -->
<div class="text-center py-5" id="loadingState" style="display: none;">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h4 class="mt-3 text-primary">Loading Payment Data...</h4>
    <p class="text-muted">Please wait while we fetch the information</p>
</div>

<!-- Error State -->
<div class="text-center py-5" id="errorState" style="display: none;">
    <div class="text-danger mb-3">
        <i class="fas fa-exclamation-triangle fa-3x"></i>
    </div>
    <h4 class="mb-3" id="errorMessage">Error loading data</h4>
    <button class="btn btn-primary" onclick="loadData()">
        <i class="fas fa-redo me-2"></i>Try Again
    </button>
</div>

<!-- No Data Message -->
<div class="text-center py-5" id="noDataMessage">
    <div class="mb-4">
        <i class="fas fa-database fa-4x text-muted"></i>
    </div>
    <h4 class="text-muted mb-3">Payment Matrix</h4>
    <p class="text-muted mb-4">Select a month and click "Load Data" to view payment information</p>
</div>

<!-- Step 1: Class Cards Grid -->
<div id="classCardsContainer" class="row mb-4" style="display: none;">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-chalkboard me-2"></i>Select a Class
                    <small class="text-muted ms-2" id="selectedMonthInfo"></small>
                </h6>
            </div>
            <div class="card-body">
                <div class="row" id="classCardsGrid">
                    <!-- Class cards will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Step 2: Students Table with Pagination -->
<div id="studentsContainer" class="row mb-4" style="display: none;">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-users me-2"></i>Students
                            <small class="text-muted ms-2" id="selectedClassInfo"></small>
                        </h6>
                        <small class="text-muted" id="paginationInfo">Page 1 of 1</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="goBackToClasses()">
                            <i class="fas fa-arrow-left me-1"></i>Back to Classes
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Students Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th width="120">Student ID</th>
                                <th>Student Name</th>
                                <th width="120">Mobile</th>
                                <th width="100">Status</th>
                                <th width="120">Amount Paid</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <!-- Students will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls -->
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <div class="text-muted small">
                        Showing <span id="fromItem">0</span> to <span id="toItem">0</span> of 
                        <span id="totalItems">0</span> students
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0" id="paginationControls">
                            <!-- Pagination buttons will be generated here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Payment Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Payment details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .class-card {
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
        border-left: 4px solid #4e73df;
        cursor: pointer;
    }
    
    .class-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #4e73df;
    }
    
    .class-card.selected {
        background-color: #f8f9fa;
        border-color: #28a745;
        border-left-color: #28a745;
    }
    
    .stats-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .student-row.paid {
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    .student-row.unpaid {
        background-color: rgba(220, 53, 69, 0.05);
    }
    
    .payment-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }
    
    .progress-thin {
        height: 5px;
        border-radius: 2px;
    }
    
    .pagination-sm .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
// Global variables
let matrixData = null;
let selectedClassId = null;
let selectedClassName = '';
let currentPage = 1;
let perPage = 10;
let classPaginationData = {}; // Store pagination data for each class

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial per page value
    perPage = parseInt(document.getElementById('perPageFilter').value);
    
    // Auto-load current month data
    setTimeout(loadData, 500);
});

// Load data with pagination
async function loadData(page = 1) {
    currentPage = page;
    
    const teacherId = document.getElementById('teacherId').value;
    const year = document.getElementById('yearFilter').value;
    const month = document.getElementById('monthFilter').value;
    const yearMonth = `${year}-${month}`;
    perPage = parseInt(document.getElementById('perPageFilter').value);
    
    console.log('Loading data for:', { teacherId, year, month, yearMonth, page, perPage });
    
    // Show loading state
    showLoading();
    
    try {
        // Build API URL with pagination parameters
        const apiUrl = `/api/teacher-payments/class-wise/${teacherId}/${yearMonth}?page=${page}&per_page=${perPage}`;
        console.log('API URL:', apiUrl);
        
        // Make API call
        const response = await fetch(apiUrl, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Get response text
        const responseText = await response.text();
        console.log('Response received');
        
        // Parse JSON response
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            throw new Error('Invalid JSON response from server');
        }
        
        console.log('Parsed data:', data);
        
        if (!data) {
            throw new Error('No data received from server');
        }
        
        if (data.status === 'success') {
            matrixData = data;
            renderTeacherSummary(data);
            renderClassCards(data.classes);
            showClassCards();
            hideErrorState();
        } else {
            throw new Error(data.message || 'Failed to load data');
        }
        
    } catch (error) {
        console.error('Error in loadData:', error);
        showErrorState('Failed to load payment data: ' + error.message);
        hideAllContainers();
    } finally {
        hideLoading();
    }
}

// Render teacher summary
function renderTeacherSummary(data) {
    const teacherSummary = document.getElementById('teacherSummary');
    if (!teacherSummary) return;
    
    teacherSummary.style.display = 'flex';
    
    // Update all summary fields with null checks
    document.getElementById('teacherNameDisplay').textContent = data.teacher_name || 'Unknown Teacher';
    document.getElementById('subjectNameDisplay').textContent = data.subject_name || 'N/A';
    document.getElementById('periodDisplay').textContent = data.year_month || '-';
    document.getElementById('classesDisplay').textContent = `${data.total_classes || 0} classes`;
    document.getElementById('totalStudentsDisplay').textContent = data.total_students || 0;
    document.getElementById('paidStudentsDisplay').textContent = `${data.total_paid_students || 0} paid`;
    document.getElementById('unpaidStudentsDisplay').textContent = `${data.total_unpaid_students || 0} unpaid`;
    document.getElementById('totalCollectionDisplay').textContent = `Rs ${formatNumber(data.total_collection || 0)}`;
    document.getElementById('paymentRateDisplay').textContent = `${data.payment_rate || 0}%`;
    
    // Update progress bar
    const progressBar = document.getElementById('paymentProgress');
    if (progressBar) {
        const paymentRate = data.payment_rate || 0;
        progressBar.style.width = `${paymentRate}%`;
    }
    
    // Update selected month info
    const selectedMonthInfo = document.getElementById('selectedMonthInfo');
    if (selectedMonthInfo) {
        selectedMonthInfo.textContent = `for ${data.year_month || 'selected month'}`;
    }
}

// Render class cards
function renderClassCards(classes) {
    const container = document.getElementById('classCardsGrid');
    if (!container) return;
    
    // Check if classes exist and is array
    if (!classes || !Array.isArray(classes) || classes.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No classes found for this teacher in the selected month.
                </div>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    classes.forEach(cls => {
        // Ensure all required properties exist
        const className = cls.class_name || 'Unnamed Class';
        const gradeName = cls.grade_name || 'N/A';
        const totalStudents = cls.total_students || 0;
        const paidCount = cls.paid_students_count || 0;
        const unpaidCount = cls.unpaid_students_count || 0;
        const totalCollection = cls.total_collection || 0;
        
        // Calculate payment rate
        const paidPercentage = totalStudents > 0 
            ? Math.round((paidCount / totalStudents) * 100) 
            : 0;
        
        // Determine progress bar color
        let progressColor = 'bg-danger';
        if (paidPercentage >= 70) progressColor = 'bg-success';
        else if (paidPercentage >= 40) progressColor = 'bg-warning';
        
        // Store pagination data for this class
        if (cls.students_pagination) {
            classPaginationData[cls.class_id] = cls.students_pagination;
        }
        
        html += `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card class-card h-100" onclick="selectClass(${cls.class_id}, '${escapeString(className)}')">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title fw-bold text-primary mb-1">${className}</h6>
                            <p class="card-text text-muted mb-0">
                                <i class="fas fa-graduation-cap me-1"></i>${gradeName}
                            </p>
                        </div>
                        <span class="badge bg-info">${totalStudents} students</span>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Payment Rate</span>
                            <span class="fw-bold ${paidPercentage >= 70 ? 'text-success' : paidPercentage >= 40 ? 'text-warning' : 'text-danger'}">
                                ${paidPercentage}%
                            </span>
                        </div>
                        <div class="progress progress-thin">
                            <div class="progress-bar ${progressColor}" 
                                 style="width: ${paidPercentage}%"></div>
                        </div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                                <div class="fw-bold text-success">${paidCount}</div>
                                <small class="text-muted">Paid</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-danger bg-opacity-10 rounded">
                                <div class="fw-bold text-danger">${unpaidCount}</div>
                                <small class="text-muted">Unpaid</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top text-center">
                        <small class="text-muted">
                            <i class="fas fa-money-bill-wave me-1"></i>
                            Collection: <span class="fw-bold">Rs ${formatNumber(totalCollection)}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        `;
    });
    
    container.innerHTML = html;
}

// Select a class
function selectClass(classId, className) {
    selectedClassId = classId;
    selectedClassName = unescapeString(className);
    
    // Remove selected class from all cards
    document.querySelectorAll('.class-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    event.currentTarget.classList.add('selected');
    
    // Show students for selected class with pagination
    showStudentsForClass(classId, selectedClassName, currentPage);
}

// Show students for selected class with pagination
function showStudentsForClass(classId, className, page = 1) {
    if (!matrixData || !matrixData.classes) {
        console.error('No matrix data available');
        return;
    }
    
    // Find the selected class
    const selectedClass = matrixData.classes.find(cls => cls.class_id == classId);
    if (!selectedClass) {
        console.error('Class not found:', classId);
        return;
    }
    
    // Update selected class info
    const selectedClassInfo = document.getElementById('selectedClassInfo');
    if (selectedClassInfo) {
        selectedClassInfo.textContent = `- ${className}`;
    }
    
    // Combine paid and unpaid students
    const paidStudents = selectedClass.paid_students || [];
    const unpaidStudents = selectedClass.unpaid_students || [];
    const allStudents = [
        ...paidStudents.map(s => ({ ...s, paid: true })),
        ...unpaidStudents.map(s => ({ ...s, paid: false }))
    ];
    
    // Get pagination data for this class
    const paginationData = selectedClass.students_pagination || classPaginationData[classId] || {};
    const totalStudents = paginationData.total || allStudents.length;
    const perPage = paginationData.per_page || 10;
    const currentPage = paginationData.current_page || 1;
    const lastPage = paginationData.last_page || 1;
    const fromItem = paginationData.from || 1;
    const toItem = paginationData.to || Math.min(perPage, totalStudents);
    
    // Update pagination info
    updatePaginationInfo(totalStudents, currentPage, perPage, fromItem, toItem, lastPage);
    
    // Render students table
    renderStudentsTable(allStudents, fromItem - 1);
    
    // Show students container and hide class cards
    document.getElementById('classCardsContainer').style.display = 'none';
    document.getElementById('studentsContainer').style.display = 'block';
}

// Render students table
function renderStudentsTable(students, startIndex = 0) {
    const tableBody = document.getElementById('studentsTableBody');
    if (!tableBody) return;
    
    let html = '';
    
    if (students.length === 0) {
        html = `
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="fas fa-users fa-lg mb-2"></i>
                    <p class="mb-0">No students found in this class</p>
                </td>
            </tr>
        `;
    } else {
        students.forEach((student, index) => {
            const studentName = student.name || 'Unknown';
            const studentId = student.custom_id || student.id || 'N/A';
            const mobile = student.whatsapp_mobile || 'N/A';
            const guardianMobile = student.guardian_mobile || '';
            const isPaid = student.paid || false;
            const amount = student.total_amount_paid || 0;
            const paymentCount = student.payment_count || 0;
            
            const statusClass = isPaid ? 'success' : 'danger';
            const statusText = isPaid ? 'Paid' : 'Unpaid';
            
            // Get initials for avatar
            const initials = getInitials(studentName);
            
            html += `
            <tr class="student-row ${isPaid ? 'paid' : 'unpaid'}">
                <td>${startIndex + index + 1}</td>
                <td>
                    <span class="fw-bold">${studentId}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle bg-primary text-white me-2">
                            ${initials}
                        </div>
                        <div>
                            <div class="fw-medium">${studentName}</div>
                            ${guardianMobile ? `
                                <small class="text-muted">
                                    <i class="fas fa-user-friends"></i> ${guardianMobile}
                                </small>
                            ` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    ${mobile !== 'N/A' ? `
                        <span class="text-muted">
                            <i class="fas fa-phone"></i> ${mobile}
                        </span>
                    ` : 'N/A'}
                </td>
                <td>
                    <span class="badge bg-${statusClass} payment-badge">${statusText}</span>
                </td>
                <td>
                    <span class="fw-bold ${amount > 0 ? 'text-success' : 'text-muted'}">
                        Rs ${formatNumber(amount)}
                    </span>
                    ${paymentCount > 0 ? `
                        <div class="text-muted small">
                            ${paymentCount} payment(s)
                        </div>
                    ` : ''}
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" 
                            onclick="showPaymentDetails(${JSON.stringify(student).replace(/"/g, '&quot;')})"
                            ${paymentCount === 0 ? 'disabled' : ''}>
                        <i class="fas fa-eye me-1"></i>View
                    </button>
                </td>
            </tr>
            `;
        });
    }
    
    tableBody.innerHTML = html;
}

// Update pagination information and controls
function updatePaginationInfo(totalItems, currentPage, perPage, fromItem, toItem, lastPage) {
    // Update text info
    document.getElementById('fromItem').textContent = fromItem;
    document.getElementById('toItem').textContent = toItem;
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('paginationInfo').textContent = `Page ${currentPage} of ${lastPage}`;
    
    // Update pagination controls
    const paginationControls = document.getElementById('paginationControls');
    if (!paginationControls) return;
    
    let html = '';
    
    // Previous button
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(lastPage, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    // First page
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }
    
    // Last page
    if (endPage < lastPage) {
        if (endPage < lastPage - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(${lastPage})">${lastPage}</a>
            </li>
        `;
    }
    
    // Next button
    html += `
        <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    paginationControls.innerHTML = html;
}

// Change page for current class
function changePage(page) {
    if (selectedClassId) {
        showStudentsForClass(selectedClassId, selectedClassName, page);
    }
}

// Show payment details modal
function showPaymentDetails(student) {
    if (!student) {
        console.error('No student data provided');
        return;
    }
    
    // Parse student data if it's a string
    if (typeof student === 'string') {
        try {
            student = JSON.parse(student.replace(/&quot;/g, '"'));
        } catch (e) {
            console.error('Error parsing student data:', e);
            alert('Error loading payment details');
            return;
        }
    }
    
    const modalBody = document.querySelector('#paymentDetailsModal .modal-body');
    if (!modalBody) return;
    
    const studentName = student.name || 'Unknown';
    const studentId = student.custom_id || student.id || 'N/A';
    const mobile = student.whatsapp_mobile || 'N/A';
    const guardianMobile = student.guardian_mobile || '';
    const totalAmount = student.total_amount_paid || 0;
    const paymentDetails = student.payment_details || [];
    
    let html = `
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold">${studentName}</h6>
                <p class="mb-1">
                    <i class="fas fa-id-card text-muted me-2"></i>
                    ID: ${studentId}
                </p>
                ${mobile !== 'N/A' ? `
                    <p class="mb-1">
                        <i class="fas fa-phone text-muted me-2"></i>
                        WhatsApp: ${mobile}
                    </p>
                ` : ''}
                ${guardianMobile ? `
                    <p class="mb-0">
                        <i class="fas fa-user-shield text-muted me-2"></i>
                        Guardian: ${guardianMobile}
                    </p>
                ` : ''}
            </div>
            <div class="col-md-6 text-end">
                <div class="alert alert-success">
                    <h3 class="mb-0">Rs ${formatNumber(totalAmount)}</h3>
                    <small>Total Paid Amount</small>
                </div>
            </div>
        </div>
        
        <hr>
        
        <h6 class="fw-bold mb-3">Payment History</h6>
    `;
    
    if (paymentDetails.length > 0) {
        html += `
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment For</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        paymentDetails.forEach(payment => {
            const paymentDate = payment.date || 'N/A';
            const paymentAmount = payment.amount || 0;
            const paymentFor = payment.paymentFor || 'N/A';
            
            html += `
                <tr>
                    <td>${formatDate(paymentDate)}</td>
                    <td class="text-success fw-bold">Rs ${formatNumber(paymentAmount)}</td>
                    <td>${paymentFor}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    } else {
        html += `
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <p class="text-muted">No payment records found</p>
            </div>
        `;
    }
    
    modalBody.innerHTML = html;
    
    // Show modal using Bootstrap 5
    const modalElement = document.getElementById('paymentDetailsModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}

// Go back to class cards
function goBackToClasses() {
    document.getElementById('studentsContainer').style.display = 'none';
    document.getElementById('classCardsContainer').style.display = 'block';
    selectedClassId = null;
    selectedClassName = '';
}

// ============================================
// UI STATE MANAGEMENT FUNCTIONS
// ============================================

function showLoading() {
    const loadingState = document.getElementById('loadingState');
    if (loadingState) loadingState.style.display = 'block';
    
    hideAllContainers();
    
    const loadBtn = document.getElementById('loadBtn');
    if (loadBtn) {
        loadBtn.disabled = true;
        loadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
    }
}

function hideLoading() {
    const loadingState = document.getElementById('loadingState');
    if (loadingState) loadingState.style.display = 'none';
    
    const loadBtn = document.getElementById('loadBtn');
    if (loadBtn) {
        loadBtn.disabled = false;
        loadBtn.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Load Data';
    }
}

function showClassCards() {
    document.getElementById('classCardsContainer').style.display = 'block';
    document.getElementById('studentsContainer').style.display = 'none';
    document.getElementById('noDataMessage').style.display = 'none';
    document.getElementById('errorState').style.display = 'none';
    document.getElementById('teacherSummary').style.display = 'flex';
}

function hideAllContainers() {
    const containers = [
        'classCardsContainer',
        'studentsContainer',
        'noDataMessage',
        'errorState',
        'teacherSummary'
    ];
    
    containers.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.style.display = 'none';
    });
}

function showErrorState(message) {
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');
    
    if (errorState) errorState.style.display = 'block';
    if (errorMessage && message) errorMessage.textContent = message;
    
    hideAllContainers();
}

function hideErrorState() {
    const errorState = document.getElementById('errorState');
    if (errorState) errorState.style.display = 'none';
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function getInitials(name) {
    if (!name || typeof name !== 'string') return '??';
    return name.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
}

function formatNumber(num) {
    const number = parseFloat(num) || 0;
    return number.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatDate(dateString) {
    if (!dateString || dateString === 'N/A') return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

function escapeString(str) {
    if (!str) return '';
    return str.replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

function unescapeString(str) {
    if (!str) return '';
    return str.replace(/\\'/g, "'").replace(/&quot;/g, '"');
}
</script>
@endpush