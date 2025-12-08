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
    
    <!-- Static Summary Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Matrix View Instructions</h3>
                </div>
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-1">
                                <small>
                                    <strong>Matrix View:</strong> Shows all students and their payments across different classes in a single table.
                                    Each column represents a class, and each cell shows the payment amount for that student in that class.
                                </small>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <small class="text-muted">
                                <i class="fas fa-check-circle text-success"></i> Green = Paid &nbsp;
                                <i class="fas fa-minus-circle text-secondary"></i> Gray = Not Paid
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Filter Section -->
    <div class="card card-primary card-outline">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0" style="font-size: 1rem;">
                        <i class="fas fa-filter mr-1"></i> Filters
                        @if(isset($teacher_name))
                            <span class="badge badge-info ml-2">{{ $teacher_name }}</span>
                        @endif
                    </h3>
                </div>
                <div>
                    <button type="button" class="btn btn-tool py-0" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body py-2">
            <form id="filterForm" class="row g-2">
                <div class="col-md-3 col-sm-6">
                    <div class="form-group mb-1">
                        <label class="mb-0" style="font-size: 0.85rem;">Year</label>
                        <select name="year" id="yearFilter" class="form-control form-control-sm" style="font-size: 0.85rem;">
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="form-group mb-1">
                        <label class="mb-0" style="font-size: 0.85rem;">Month</label>
                        <select name="month" id="monthFilter" class="form-control form-control-sm" style="font-size: 0.85rem;">
                            @foreach(['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', 
                                    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
                                    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'] as $key => $month)
                                <option value="{{ $key }}" {{ date('m') == $key ? 'selected' : '' }}>
                                    {{ $month }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4 col-sm-8">
                    <div class="form-group mb-1">
                        <label class="mb-0" style="font-size: 0.85rem;">Quick Actions</label>
                        <div class="btn-group btn-group-sm d-flex" role="group">
                            <button type="button" class="btn btn-primary flex-fill" onclick="loadMatrixData()" id="loadBtn">
                                <i class="fas fa-sync-alt"></i> Load
                            </button>
                            <button type="button" class="btn btn-success flex-fill" onclick="exportExcel()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button type="button" class="btn btn-danger flex-fill" onclick="exportPDF()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="button" class="btn btn-info flex-fill" onclick="printMatrix()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-4">
                    <div class="form-group mb-1">
                        <label class="mb-0" style="font-size: 0.85rem;">View</label>
                        <a href="{{ route('teacher_payment.history', $teacherId) }}" class="btn btn-warning btn-sm btn-block">
                            <i class="fas fa-list"></i> Detailed
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div id="statsRow" class="row mb-3" style="display: none;">
        <div class="col-12">
            <div class="card card-success">
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col">
                            <small class="text-muted">Classes</small>
                            <h5 class="mb-0" id="statClasses">0</h5>
                        </div>
                        <div class="col">
                            <small class="text-muted">Students</small>
                            <h5 class="mb-0" id="statStudents">0</h5>
                        </div>
                        <div class="col">
                            <small class="text-muted">Collection</small>
                            <h5 class="mb-0" id="statCollection">Rs. 0</h5>
                        </div>
                        <div class="col">
                            <small class="text-muted">Paid</small>
                            <h5 class="mb-0" id="statPaid">0</h5>
                        </div>
                        <div class="col">
                            <small class="text-muted">Rate</small>
                            <h5 class="mb-0" id="statRate">0%</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0" style="font-size: 1rem;">
                    <i class="fas fa-table mr-1"></i> Payment Matrix
                    <small class="text-muted ml-1" id="matrixSubtitle"></small>
                </h3>
                <div class="input-group input-group-sm" style="width: 300px;">
                    <input type="text" class="form-control form-control-sm" placeholder="Search students..." id="searchInput">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="searchTable()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <!-- Loading State -->
            <div id="loadingState" class="text-center py-4" style="display: none;">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 mb-0" style="font-size: 0.85rem;">Loading payment data...</p>
            </div>
            
            <!-- Error State -->
            <div id="errorState" class="text-center py-4" style="display: none;">
                <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                <p class="mb-1" style="font-size: 0.85rem;" id="errorMessage"></p>
                <button class="btn btn-sm btn-primary" onclick="loadMatrixData()">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
            
            <!-- Empty State -->
            <div id="emptyState" class="text-center py-4">
                <i class="fas fa-table text-muted fa-2x mb-2"></i>
                <p class="mb-1" style="font-size: 0.85rem;">No data loaded</p>
                <p class="text-muted mb-2" style="font-size: 0.8rem;">Select year/month and click Load to view payment matrix</p>
            </div>
            
            <!-- Matrix Wrapper with Pagination -->
            <div id="matrixWrapper" style="display: none;">
                <!-- Pagination Info -->
                <div class="px-3 pt-2" style="font-size: 0.8rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing <span id="currentPage">1</span> of <span id="totalPages">1</span> pages
                            (<span id="visibleRows">0</span> of <span id="totalRows">0</span> students)
                        </div>
                        <div>
                            <select class="form-control form-control-sm d-inline-block" id="rowsPerPage" style="width: auto; font-size: 0.8rem;">
                                <option value="10">10 rows</option>
                                <option value="25" selected>25 rows</option>
                                <option value="50">50 rows</option>
                                <option value="100">100 rows</option>
                                <option value="0">All rows</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Matrix Container -->
                <div id="matrixContainer" style="overflow-x: auto; max-height: 600px;">
                    <!-- Matrix will be rendered here -->
                </div>
                
                <!-- Pagination Controls -->
                <div class="card-footer py-2" id="paginationControls" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted" style="font-size: 0.8rem;">
                            Showing <span id="startRow">0</span> to <span id="endRow">0</span> of 
                            <span id="totalRowsDisplay">0</span> students
                        </div>
                        <div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0" id="paginationList">
                                    <!-- Pagination buttons will be generated here -->
                                </ul>
                            </nav>
                        </div>
                        <div class="text-muted" style="font-size: 0.8rem;">
                            <span id="pageInfo">Page 1 of 1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden teacher ID -->
    <input type="hidden" id="teacherId" value="{{ $teacherId }}">
@endsection

@push('styles')
<style>
    /* Matrix Table Styling */
    .matrix-table {
        font-size: 11px !important;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0 !important;
    }
    
    .matrix-table th {
        font-size: 10px;
        padding: 4px 6px !important;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .matrix-table td {
        padding: 4px 6px !important;
        border: 1px solid #dee2e6;
        vertical-align: middle;
        text-align: center;
    }
    
    .student-cell {
        background-color: white;
        position: sticky;
        left: 0;
        z-index: 5;
        min-width: 120px;
        text-align: left;
        border-right: 2px solid #007bff;
    }
    
    .class-header {
        background-color: #e3f2fd;
        border-left: 2px solid #2196f3 !important;
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        text-align: center;
        min-width: 40px;
        max-width: 40px;
        height: 100px;
    }
    
    .amount-cell {
        min-width: 70px;
        max-width: 70px;
    }
    
    .paid-cell {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .unpaid-cell {
        background-color: rgba(108, 117, 125, 0.05);
    }
    
    .total-column {
        background-color: #f8f9fa;
        font-weight: bold;
        border-left: 2px solid #6c757d !important;
    }
    
    .summary-row {
        background-color: #e9ecef;
        font-weight: bold;
    }
    
    /* Pagination Styling */
    .pagination-sm .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Compact badges */
    .badge-sm {
        font-size: 9px;
        padding: 2px 4px;
    }
    
    /* Scrollbar styling */
    #matrixContainer::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }
    
    #matrixContainer::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    #matrixContainer::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    
    #matrixContainer::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .matrix-table {
            font-size: 10px !important;
        }
        
        .matrix-table th,
        .matrix-table td {
            padding: 3px 4px !important;
        }
        
        .amount-cell {
            min-width: 60px;
            max-width: 60px;
        }
        
        .student-cell {
            min-width: 100px;
        }
        
        .pagination {
            flex-wrap: wrap;
        }
    }
    
    /* Print styles */
    @media print {
        .card-header, .card-footer, .btn, .input-group, #filterForm, #paginationControls, #rowsPerPage {
            display: none !important;
        }
        
        .matrix-table {
            font-size: 8px !important;
        }
        
        .matrix-table th,
        .matrix-table td {
            padding: 2px 3px !important;
        }
        
        #matrixContainer {
            max-height: none !important;
            overflow: visible !important;
        }
    }
</style>
@endpush

@push('scripts')
<!-- Load jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
// Check if jQuery is loaded
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded. Please include jQuery in your layout.');
    
    // Fallback: Load jQuery dynamically
    const script = document.createElement('script');
    script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
    script.onload = function() {
        console.log('jQuery loaded dynamically');
        initializeApp();
    };
    document.head.appendChild(script);
} else {
    // jQuery is already loaded
    $(document).ready(initializeApp);
}

// Global variables
let matrixData = null;
let studentsMap = new Map();
let classesList = [];
let paginatedStudents = [];
let currentPage = 1;
let rowsPerPage = 25;
let totalPages = 1;
let filteredStudents = [];
let currentSearchTerm = '';

// Main initialization function
function initializeApp() {
    console.log('Initializing Matrix App with jQuery version:', $.fn.jquery);
    
    // Auto-load current month data
    setTimeout(() => {
        loadMatrixData();
    }, 500);
    
    // Enter key for search
    $('#searchInput').on('keyup', function(e) {
        if (e.key === 'Enter') {
            searchTable();
        }
    });
    
    // Rows per page change
    $('#rowsPerPage').on('change', function() {
        rowsPerPage = parseInt($(this).val());
        currentPage = 1;
        updatePagination();
        renderMatrix();
    });
    
    // Check if teacherId exists
    const teacherId = document.getElementById('teacherId').value;
    if (!teacherId) {
        showError('Teacher ID is missing. Please go back and select a teacher.');
        return;
    }
    
    console.log('Teacher ID:', teacherId);
}

// Main function to load matrix data
async function loadMatrixData() {
    try {
        const teacherId = document.getElementById('teacherId').value;
        const year = document.getElementById('yearFilter').value;
        const month = document.getElementById('monthFilter').value;
        const yearMonth = `${year}-${month}`;
        
        console.log('Loading data for:', { teacherId, year, month, yearMonth });
        
        if (!teacherId || !year || !month) {
            throw new Error('Missing required parameters');
        }
        
        // Update UI states
        const loadBtn = document.getElementById('loadBtn');
        if (loadBtn) {
            loadBtn.disabled = true;
            loadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading';
        }
        
        showLoading();
        
        // Reset pagination
        currentPage = 1;
        currentSearchTerm = '';
        $('#searchInput').val('');
        
        // Call API
        const apiUrl = `http://127.0.0.1:8000/api/teacher-payments/class-wise/${teacherId}/${yearMonth}`;
        console.log('API URL:', apiUrl);
        
        const response = await fetch(apiUrl, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('API Response:', data);
        
        if (data.status === 'success') {
            matrixData = data;
            processMatrixData();
            filterStudents();
            updatePagination();
            renderMatrix();
            updateStats();
            showSuccess();
        } else {
            throw new Error(data.message || 'API returned unsuccessful status');
        }
    } catch (error) {
        console.error('Error loading matrix data:', error);
        showError('Failed to load data: ' + error.message);
    } finally {
        const loadBtn = document.getElementById('loadBtn');
        if (loadBtn) {
            loadBtn.disabled = false;
            loadBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Load';
        }
    }
}

// Process API data
function processMatrixData() {
    studentsMap.clear();
    classesList = [];
    
    // Process classes
    matrixData.classes.forEach(cls => {
        const classInfo = {
            id: cls.class_id,
            name: cls.class_name,
            grade: cls.grade_name,
            total: cls.total_collection || 0
        };
        classesList.push(classInfo);
        
        // Process paid students
        if (cls.paid_students && Array.isArray(cls.paid_students)) {
            cls.paid_students.forEach(student => {
                addStudentPayment(student, cls.class_id, true);
            });
        }
        
        // Process unpaid students
        if (cls.unpaid_students && Array.isArray(cls.unpaid_students)) {
            cls.unpaid_students.forEach(student => {
                addStudentPayment(student, cls.class_id, false);
            });
        }
    });
}

// Add student payment to map
function addStudentPayment(student, classId, isPaid) {
    const studentId = student.custom_id;
    
    if (!studentsMap.has(studentId)) {
        studentsMap.set(studentId, {
            id: student.id,
            custom_id: student.custom_id,
            name: student.name,
            img_url: student.img_url,
            payments: {}
        });
    }
    
    const studentData = studentsMap.get(studentId);
    studentData.payments[classId] = {
        amount: isPaid ? parseFloat(student.amount_paid) : 0,
        paid: isPaid,
        date: student.payment_date
    };
}

// Filter students based on search term
function filterStudents() {
    const searchTerm = currentSearchTerm.toLowerCase().trim();
    
    // Convert Map to sorted array
    let studentsArray = Array.from(studentsMap.values()).sort((a, b) => 
        a.custom_id.localeCompare(b.custom_id)
    );
    
    if (searchTerm) {
        filteredStudents = studentsArray.filter(student => 
            student.custom_id.toLowerCase().includes(searchTerm) ||
            student.name.toLowerCase().includes(searchTerm)
        );
    } else {
        filteredStudents = studentsArray;
    }
    
    return filteredStudents;
}

// Update pagination
function updatePagination() {
    const totalStudents = filteredStudents.length;
    const rowsPerPageValue = rowsPerPage === 0 ? totalStudents : rowsPerPage;
    totalPages = rowsPerPage === 0 ? 1 : Math.ceil(totalStudents / rowsPerPageValue);
    
    if (currentPage > totalPages) {
        currentPage = totalPages || 1;
    }
    
    // Update paginated students
    if (rowsPerPage === 0) {
        paginatedStudents = filteredStudents;
    } else {
        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, totalStudents);
        paginatedStudents = filteredStudents.slice(startIndex, endIndex);
    }
    
    updatePaginationControls();
    updatePaginationInfo();
}

// Update pagination controls
function updatePaginationControls() {
    const paginationList = document.getElementById('paginationList');
    const paginationControls = document.getElementById('paginationControls');
    
    if (!paginationList || !paginationControls) return;
    
    if (filteredStudents.length === 0 || rowsPerPage === 0) {
        paginationControls.style.display = 'none';
        return;
    }
    
    paginationControls.style.display = 'flex';
    
    let html = '';
    const totalPagesValue = totalPages;
    
    // Previous button
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPagesValue, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePage(1)">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }
    
    if (endPage < totalPagesValue) {
        if (endPage < totalPagesValue - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="changePage(${totalPagesValue})">${totalPagesValue}</a>
            </li>
        `;
    }
    
    // Next button
    html += `
        <li class="page-item ${currentPage === totalPagesValue ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    paginationList.innerHTML = html;
}

// Update pagination info
function updatePaginationInfo() {
    const totalStudents = filteredStudents.length;
    const rowsPerPageValue = rowsPerPage === 0 ? totalStudents : rowsPerPage;
    
    const startRow = rowsPerPage === 0 ? 1 : (currentPage - 1) * rowsPerPage + 1;
    const endRow = rowsPerPage === 0 ? totalStudents : Math.min(currentPage * rowsPerPage, totalStudents);
    
    // Update display elements
    document.getElementById('startRow').textContent = startRow;
    document.getElementById('endRow').textContent = endRow;
    document.getElementById('totalRowsDisplay').textContent = totalStudents;
    document.getElementById('totalRows').textContent = totalStudents;
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('totalPages').textContent = totalPages;
    document.getElementById('visibleRows').textContent = paginatedStudents.length;
    document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
}

// Change page
function changePage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    
    currentPage = page;
    updatePagination();
    renderMatrix();
    
    // Scroll to top of table
    const matrixContainer = document.getElementById('matrixContainer');
    if (matrixContainer) {
        matrixContainer.scrollTop = 0;
        matrixContainer.scrollLeft = 0;
    }
}

// Render the matrix table
function renderMatrix() {
    if (!matrixData || paginatedStudents.length === 0) {
        console.log('No data to render');
        return;
    }
    
    let html = `
        <table class="table table-bordered matrix-table">
            <thead>
                <tr>
                    <th class="student-cell bg-light">Student</th>
    `;
    
    // Add class headers
    classesList.forEach(cls => {
        html += `
            <th class="class-header" title="${cls.name} - Grade ${cls.grade}">
                <div class="d-flex flex-column align-items-center">
                    <small><strong>${cls.name}</strong></small>
                    <small class="text-muted">G${cls.grade}</small>
                </div>
            </th>
        `;
    });
    
    // Add total column
    html += `
                    <th class="total-column bg-light">Total</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    // Add student rows
    paginatedStudents.forEach(student => {
        let studentTotal = 0;
        
        html += `
            <tr>
                <td class="student-cell">
                    <div class="d-flex align-items-center">
                        <div class="mr-1">
        `;
        
        if (student.img_url) {
            html += `<img src="${student.img_url}" class="rounded-circle" 
                          style="width: 20px; height: 20px; object-fit: cover;"
                          onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Ccircle%20cx%3D%2210%22%20cy%3D%2210%22%20r%3D%229%22%20fill%3D%22%23dee2e6%22%2F%3E%3C%2Fsvg%3E'">`;
        } else {
            html += `<div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
                          style="width: 20px; height: 20px;">
                        <i class="fas fa-user text-muted" style="font-size: 10px;"></i>
                     </div>`;
        }
        
        html += `
                        </div>
                        <div>
                            <div class="font-weight-bold" style="font-size: 10px;">${student.custom_id}</div>
                            <div class="text-muted" style="font-size: 9px; line-height: 1;">${truncateText(student.name, 15)}</div>
                        </div>
                    </div>
                </td>
        `;
        
        // Add payment cells for each class
        classesList.forEach(cls => {
            const payment = student.payments[cls.id];
            const amount = payment ? payment.amount : 0;
            const isPaid = payment ? payment.paid : false;
            
            studentTotal += amount;
            
            html += `<td class="amount-cell ${isPaid ? 'paid-cell' : 'unpaid-cell'}">`;
            
            if (amount > 0) {
                html += `
                    <div class="text-success font-weight-bold" style="font-size: 10px;">
                        Rs. ${formatNumber(amount)}
                    </div>
                `;
                
                if (payment && payment.date) {
                    html += `<small class="text-muted" style="font-size: 8px;">${formatDateShort(payment.date)}</small>`;
                }
            } else {
                html += `<span class="text-muted" style="font-size: 9px;">-</span>`;
            }
            
            html += `</td>`;
        });
        
        // Add student total
        html += `
                <td class="total-column">
                    <span class="font-weight-bold ${studentTotal > 0 ? 'text-primary' : 'text-muted'}" style="font-size: 10px;">
                        Rs. ${formatNumber(studentTotal)}
                    </span>
                </td>
            </tr>
        `;
    });
    
    // Add summary row (only if showing all data or last page)
    if (rowsPerPage === 0 || currentPage === totalPages) {
        html += `
            <tr class="summary-row">
                <td class="student-cell">
                    <strong>Class Totals</strong>
                </td>
        `;
        
        let grandTotal = 0;
        
        // Calculate class totals from ALL students
        classesList.forEach(cls => {
            let classTotal = 0;
            
            filteredStudents.forEach(student => {
                const payment = student.payments[cls.id];
                if (payment) {
                    classTotal += payment.amount;
                }
            });
            
            grandTotal += classTotal;
            
            html += `
                <td class="amount-cell">
                    <strong class="${classTotal > 0 ? 'text-success' : 'text-muted'}" style="font-size: 10px;">
                        Rs. ${formatNumber(classTotal)}
                    </strong>
                </td>
            `;
        });
        
        // Add grand total
        html += `
                <td class="total-column">
                    <strong class="text-success" style="font-size: 11px;">
                        Rs. ${formatNumber(grandTotal)}
                    </strong>
                </td>
            </tr>
        `;
    }
    
    html += `</tbody></table>`;
    
    // Update UI
    const matrixContainer = document.getElementById('matrixContainer');
    const matrixSubtitle = document.getElementById('matrixSubtitle');
    
    if (matrixContainer) {
        matrixContainer.innerHTML = html;
    }
    
    if (matrixSubtitle && matrixData) {
        matrixSubtitle.textContent = `${matrixData.teacher_name} • ${matrixData.year_month}`;
    }
}

// Search functionality
function searchTable() {
    currentSearchTerm = document.getElementById('searchInput').value;
    currentPage = 1;
    filterStudents();
    updatePagination();
    renderMatrix();
}

// Clear search
function clearSearch() {
    document.getElementById('searchInput').value = '';
    currentSearchTerm = '';
    currentPage = 1;
    filterStudents();
    updatePagination();
    renderMatrix();
}

// Update statistics
function updateStats() {
    if (!matrixData) return;
    
    const statClasses = document.getElementById('statClasses');
    const statStudents = document.getElementById('statStudents');
    const statCollection = document.getElementById('statCollection');
    const statPaid = document.getElementById('statPaid');
    const statRate = document.getElementById('statRate');
    const statsRow = document.getElementById('statsRow');
    
    if (statClasses) statClasses.textContent = matrixData.total_classes || 0;
    if (statStudents) statStudents.textContent = matrixData.total_students || 0;
    if (statCollection) statCollection.textContent = `Rs. ${formatNumber(matrixData.total_collection || 0)}`;
    if (statPaid) statPaid.textContent = matrixData.total_paid_students || 0;
    if (statRate) statRate.textContent = `${matrixData.payment_rate || 0}%`;
    if (statsRow) statsRow.style.display = 'block';
}

// Export functions
function exportExcel() {
    if (!matrixData || filteredStudents.length === 0) {
        alert('Please load data first');
        return;
    }
    
    const year = document.getElementById('yearFilter').value;
    const month = document.getElementById('monthFilter').value;
    const teacherName = matrixData.teacher_name.replace(/[^a-z0-9]/gi, '_');
    
    // Create CSV content
    let csv = 'Student ID,Student Name';
    
    // Add class headers
    classesList.forEach(cls => {
        csv += `,"${cls.name} (G${cls.grade})"`;
    });
    
    csv += ',Total\n';
    
    // Add student data
    filteredStudents.forEach(student => {
        csv += `"${student.custom_id}","${student.name}"`;
        
        let studentTotal = 0;
        
        classesList.forEach(cls => {
            const payment = student.payments[cls.id];
            const amount = payment ? payment.amount : 0;
            csv += `,"${amount}"`;
            studentTotal += amount;
        });
        
        csv += `,"${studentTotal}"\n`;
    });
    
    // Add totals row
    csv += '"TOTALS",""';
    
    let grandTotal = 0;
    classesList.forEach(cls => {
        let classTotal = 0;
        filteredStudents.forEach(student => {
            const payment = student.payments[cls.id];
            if (payment) {
                classTotal += payment.amount;
            }
        });
        csv += `,"${classTotal}"`;
        grandTotal += classTotal;
    });
    
    csv += `,"${grandTotal}"\n`;
    
    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Payment_Matrix_${teacherName}_${year}_${month}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function exportPDF() {
    if (!matrixData) {
        alert('Please load data first');
        return;
    }
    
    printMatrix();
}

function printMatrix() {
    if (!matrixData) {
        alert('Please load data first');
        return;
    }
    
    // Temporarily show all rows for printing
    const originalRowsPerPage = rowsPerPage;
    const originalCurrentPage = currentPage;
    
    rowsPerPage = 0;
    currentPage = 1;
    updatePagination();
    renderMatrix();
    
    const printContent = document.getElementById('matrixContainer').innerHTML;
    const title = `Payment Matrix - ${matrixData.teacher_name} (${matrixData.year_month})`;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>${title}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { border-collapse: collapse; width: 100%; font-size: 9px; }
                th, td { border: 1px solid #ddd; padding: 4px; text-align: center; }
                th { background-color: #f5f5f5; }
                .student-col { text-align: left; background-color: #f8f9fa; }
                .total-col { font-weight: bold; background-color: #e9ecef; }
                @media print {
                    @page { margin: 0.5cm; }
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            <h3>${title}</h3>
            <p>Generated: ${new Date().toLocaleDateString()}</p>
            ${printContent}
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 500);
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
    
    // Restore original pagination
    setTimeout(() => {
        rowsPerPage = originalRowsPerPage;
        currentPage = originalCurrentPage;
        updatePagination();
        renderMatrix();
    }, 1000);
}

// UI State Management
function showLoading() {
    const loadingState = document.getElementById('loadingState');
    const errorState = document.getElementById('errorState');
    const emptyState = document.getElementById('emptyState');
    const matrixWrapper = document.getElementById('matrixWrapper');
    
    if (loadingState) loadingState.style.display = 'block';
    if (errorState) errorState.style.display = 'none';
    if (emptyState) emptyState.style.display = 'none';
    if (matrixWrapper) matrixWrapper.style.display = 'none';
}

function showSuccess() {
    const loadingState = document.getElementById('loadingState');
    const errorState = document.getElementById('errorState');
    const emptyState = document.getElementById('emptyState');
    const matrixWrapper = document.getElementById('matrixWrapper');
    
    if (loadingState) loadingState.style.display = 'none';
    if (errorState) errorState.style.display = 'none';
    if (emptyState) emptyState.style.display = 'none';
    if (matrixWrapper) matrixWrapper.style.display = 'block';
}

function showError(message) {
    const loadingState = document.getElementById('loadingState');
    const errorState = document.getElementById('errorState');
    const emptyState = document.getElementById('emptyState');
    const matrixWrapper = document.getElementById('matrixWrapper');
    const errorMessage = document.getElementById('errorMessage');
    const statsRow = document.getElementById('statsRow');
    
    if (loadingState) loadingState.style.display = 'none';
    if (errorMessage) errorMessage.textContent = message || 'An error occurred';
    if (errorState) errorState.style.display = 'block';
    if (emptyState) emptyState.style.display = 'none';
    if (matrixWrapper) matrixWrapper.style.display = 'none';
    if (statsRow) statsRow.style.display = 'none';
}

// Helper functions
function formatNumber(num) {
    return num.toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

function formatDateShort(dateString) {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        return date.getDate() + '/' + (date.getMonth() + 1);
    } catch (e) {
        return '';
    }
}

function truncateText(text, maxLength) {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}
</script>
@endpush