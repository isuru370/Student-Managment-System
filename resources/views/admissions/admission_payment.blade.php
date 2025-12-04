@extends('layouts.app')

@section('title', 'Admission Payments')
@section('page-title', 'Admission Payments Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Admission Payments</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Admission Payments Management
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Filter by Date</label>
                            <input type="date" class="form-control" id="filterDate">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Admission Status</label>
                            <select class="form-select" id="admissionStatusFilter">
                                <option value="all">All Students</option>
                                <option value="paid">Paid Only</option>
                                <option value="not_paid">Not Paid Only</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-times me-2"></i>Clear
                            </button>
                        </div>
                        <div class="col-md-3 text-end">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-success" onclick="loadAllStudents()">
                                    <i class="fas fa-sync me-2"></i>Refresh All
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions Section -->
                    <div class="row mb-4" id="bulkActionsSection" style="display: none;">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-bulk me-2"></i>
                                        Bulk Admission Payments
                                        <span class="badge bg-dark ms-2" id="selectedCount">0 students selected</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Admission Type</label>
                                            <select class="form-select" id="admissionType">
                                                <option value="">Select Admission Type</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Amount</label>
                                            <input type="text" class="form-control" id="paymentAmount" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">&nbsp;</label>
                                            <button class="btn btn-success w-100" id="processBulkPaymentsBtn" onclick="processBulkPayments()" disabled>
                                                <i class="fas fa-money-bill me-2"></i>
                                                Process Payments
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-primary btn-sm" onclick="selectAllOnCurrentPage()">
                                                    <i class="fas fa-check-square me-1"></i>Select Page
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm" onclick="selectAllStudents()">
                                                    <i class="fas fa-check-double me-1"></i>Select All
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm" onclick="deselectAllStudents()">
                                                    <i class="fas fa-times-circle me-1"></i>Deselect All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading students...</p>
                    </div>

                    <!-- Students Table -->
                    <div id="studentsTableSection" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="text-muted" id="tableInfo">Showing 0 students</span>
                                <span class="badge bg-info ms-2" id="admissionStats"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="me-2 text-muted">Show:</span>
                                <select class="form-select form-select-sm" id="pageSize" style="width: auto;" onchange="changePageSize()">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                                        </th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Grade</th>
                                        <th>Mobile</th>
                                        <th>Created Date</th>
                                        <th>Admission Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <!-- Students will be populated here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Student pagination" id="paginationSection">
                            <ul class="pagination justify-content-center" id="paginationContainer">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>

                    <!-- No Students Message -->
                    <div id="noStudentsMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Students Found</h4>
                        <p class="text-muted">No students match your filter criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Admissions Modal -->
    <div class="modal fade" id="viewAdmissionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt me-2"></i>
                        Student Admission Payments
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Student ID:</strong> <span id="modalStudentId" class="badge bg-primary"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Name:</strong> <span id="modalStudentName" class="fw-bold"></span>
                        </div>
                    </div>
                    
                    <div id="admissionsLoading" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading admission payments...</p>
                    </div>
                    
                    <div id="admissionsContent" style="display: none;">
                        <h6>Admission Payment History</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Admission Type</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody id="admissionsTableBody">
                                    <!-- Admissions will be populated here -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noAdmissionsMessage" class="text-center py-3" style="display: none;">
                            <p class="text-muted">No admission payments found for this student.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .student-row {
            cursor: pointer;
        }
        .student-row:hover {
            background-color: #f8f9fa;
        }
        .payment-badge {
            font-size: 0.8rem;
        }
        .pagination .page-link {
            color: #0d6efd;
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .admission-paid {
            background-color: #d1edff;
        }
        .admission-not-paid {
            background-color: #fff3cd;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let allStudents = [];
        let filteredStudents = [];
        let selectedStudents = [];
        let admissionTypes = [];
        let currentModalStudentId = null;
        
        // Pagination variables
        let currentPage = 1;
        let pageSize = 25;
        let totalPages = 1;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadAllStudents();
            loadAdmissionTypes();
        });

        // ================= LOAD ALL STUDENTS =================
        async function loadAllStudents() {
            try {
                showLoadingState();
                
                const response = await fetch('/api/students/active');
                if (!response.ok) throw new Error('Failed to fetch students');

                const result = await response.json();
                
                if (result.status === 'success' && result.data) {
                    allStudents = result.data.data || result.data;
                    applyFilters(); // Apply any existing filters
                } else {
                    throw new Error(result.message || 'No students found');
                }
            } catch (error) {
                console.error('Error loading students:', error);
                showErrorState('Failed to load students: ' + error.message);
            }
        }

        // ================= APPLY FILTERS =================
        function applyFilters() {
            const filterDate = document.getElementById('filterDate').value;
            const admissionStatus = document.getElementById('admissionStatusFilter').value;
            
            let filtered = [...allStudents];
            
            // Apply date filter
            if (filterDate) {
                filtered = filtered.filter(student => {
                    const studentDate = new Date(student.created_at).toISOString().split('T')[0];
                    return studentDate === filterDate;
                });
            }
            
            // Apply admission status filter
            if (admissionStatus === 'paid') {
                filtered = filtered.filter(student => student.admission === 1);
            } else if (admissionStatus === 'not_paid') {
                filtered = filtered.filter(student => student.admission === 0);
            }
            
            filteredStudents = filtered;
            currentPage = 1;
            displayStudents();
            showContentState();
            
            if (filteredStudents.length === 0) {
                showNoStudentsMessage();
            }
        }

        // ================= CLEAR FILTERS =================
        function clearFilters() {
            document.getElementById('filterDate').value = '';
            document.getElementById('admissionStatusFilter').value = 'all';
            filteredStudents = [...allStudents];
            currentPage = 1;
            displayStudents();
            showContentState();
        }

        // ================= LOAD ADMISSION TYPES =================
        async function loadAdmissionTypes() {
            try {
                const response = await fetch('/api/admissions/dropdown');
                if (!response.ok) throw new Error('Failed to fetch admission types');

                const result = await response.json();
                
                if (Array.isArray(result)) {
                    admissionTypes = result;
                    populateAdmissionTypesDropdown();
                } else if (result.data && Array.isArray(result.data)) {
                    admissionTypes = result.data;
                    populateAdmissionTypesDropdown();
                }
            } catch (error) {
                console.error('Error loading admission types:', error);
                showAlert('Failed to load admission types', 'danger');
            }
        }

        // ================= POPULATE ADMISSION TYPES DROPDOWN =================
        function populateAdmissionTypesDropdown() {
            const dropdown = document.getElementById('admissionType');
            let options = '<option value="">Select Admission Type</option>';
            
            admissionTypes.forEach(type => {
                options += `<option value="${type.id}" data-amount="${type.amount}">${type.name} - Rs. ${type.amount}</option>`;
            });
            
            dropdown.innerHTML = options;
            
            // Add event listener for amount update
            dropdown.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const amount = selectedOption.getAttribute('data-amount');
                document.getElementById('paymentAmount').value = amount ? 'Rs. ' + amount : '';
                updateProcessButton();
            });
        }

        // ================= DISPLAY STUDENTS =================
        function displayStudents() {
            const tableBody = document.getElementById('studentsTableBody');
            const tableInfo = document.getElementById('tableInfo');
            const admissionStats = document.getElementById('admissionStats');
            
            if (filteredStudents.length === 0) {
                showNoStudentsMessage();
                return;
            }

            // Calculate admission statistics
            const paidCount = filteredStudents.filter(student => student.admission === 1).length;
            const notPaidCount = filteredStudents.filter(student => student.admission === 0).length;
            
            // Update table info and stats
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, filteredStudents.length);
            tableInfo.textContent = `Showing ${startIndex + 1}-${endIndex} of ${filteredStudents.length} students`;
            admissionStats.textContent = `Paid: ${paidCount} | Not Paid: ${notPaidCount}`;

            // Calculate pagination
            totalPages = Math.ceil(filteredStudents.length / pageSize);
            const paginatedStudents = filteredStudents.slice(startIndex, endIndex);

            let html = '';
            
            paginatedStudents.forEach(student => {
                const isSelected = selectedStudents.includes(student.id);
                const createdDate = new Date(student.created_at).toLocaleDateString();
                const admissionStatus = student.admission === 1 ? 'Paid' : 'Not Paid';
                const statusBadge = student.admission === 1 ? 
                    '<span class="badge bg-success payment-badge"><i class="fas fa-check me-1"></i>Paid</span>' :
                    '<span class="badge bg-danger payment-badge"><i class="fas fa-times me-1"></i>Not Paid</span>';
                const rowClass = student.admission === 1 ? 'admission-paid' : 'admission-not-paid';
                
                html += `
                    <tr class="student-row ${rowClass}">
                        <td>
                            <input type="checkbox" ${isSelected ? 'checked' : ''} 
                                   onchange="toggleStudentSelection(${student.id}, this)">
                        </td>
                        <td>${student.custom_id}</td>
                        <td>${student.fname} ${student.lname}</td>
                        <td>${student.grade ? student.grade.grade_name : 'N/A'}</td>
                        <td>${student.mobile || 'N/A'}</td>
                        <td>${createdDate}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info" onclick="viewStudentAdmissions(${student.id}, '${student.custom_id}', '${student.fname} ${student.lname}')">
                                <i class="fas fa-eye me-1"></i>View Admissions
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tableBody.innerHTML = html;
            generatePagination();
            updateBulkActions();
        }

        // ================= PAGINATION FUNCTIONS =================
        function generatePagination() {
            const paginationContainer = document.getElementById('paginationContainer');
            
            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let paginationHtml = '';
            
            // Previous button
            paginationHtml += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            `;
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    paginationHtml += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                        </li>
                    `;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }
            
            // Next button
            paginationHtml += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            `;
            
            paginationContainer.innerHTML = paginationHtml;
        }

        function changePage(page) {
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            displayStudents();
        }

        function changePageSize() {
            pageSize = parseInt(document.getElementById('pageSize').value);
            currentPage = 1;
            displayStudents();
        }

        // ================= VIEW STUDENT ADMISSIONS =================
        async function viewStudentAdmissions(studentId, customId, studentName) {
            currentModalStudentId = studentId;
            
            // Update modal header
            document.getElementById('modalStudentId').textContent = customId;
            document.getElementById('modalStudentName').textContent = studentName;
            
            // Show loading state
            document.getElementById('admissionsLoading').style.display = 'block';
            document.getElementById('admissionsContent').style.display = 'none';
            document.getElementById('noAdmissionsMessage').style.display = 'none';
            
            try {
                const response = await fetch(`/api/payment-admissions/student?student_id=${studentId}`);
                if (!response.ok) throw new Error('Failed to fetch admissions');
                
                const result = await response.json();
                
                // Hide loading
                document.getElementById('admissionsLoading').style.display = 'none';
                document.getElementById('admissionsContent').style.display = 'block';
                
                if (result.status && result.data && result.data.length > 0) {
                    const admissionsBody = document.getElementById('admissionsTableBody');
                    let admissionsHtml = '';
                    
                    result.data.forEach(payment => {
                        const paymentDate = new Date(payment.created_at).toLocaleDateString();
                        admissionsHtml += `
                            <tr>
                                <td>${payment.admission_name}</td>
                                <td>Rs. ${payment.amount}</td>
                                <td>${paymentDate}</td>
                            </tr>
                        `;
                    });
                    
                    admissionsBody.innerHTML = admissionsHtml;
                    document.getElementById('noAdmissionsMessage').style.display = 'none';
                } else {
                    document.getElementById('noAdmissionsMessage').style.display = 'block';
                    document.getElementById('admissionsTableBody').innerHTML = '';
                }
            } catch (error) {
                console.error('Error loading admissions:', error);
                document.getElementById('admissionsLoading').style.display = 'none';
                document.getElementById('admissionsContent').style.display = 'block';
                document.getElementById('noAdmissionsMessage').style.display = 'block';
                document.getElementById('noAdmissionsMessage').innerHTML = '<p class="text-danger">Failed to load admission payments</p>';
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewAdmissionsModal'));
            modal.show();
        }

        // ================= SELECTION FUNCTIONS =================
        function toggleSelectAll(checkbox) {
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, filteredStudents.length);
            const currentPageStudents = filteredStudents.slice(startIndex, endIndex);
            
            if (checkbox.checked) {
                currentPageStudents.forEach(student => {
                    if (!selectedStudents.includes(student.id)) {
                        selectedStudents.push(student.id);
                    }
                });
            } else {
                currentPageStudents.forEach(student => {
                    selectedStudents = selectedStudents.filter(id => id !== student.id);
                });
            }
            displayStudents();
        }

        function toggleStudentSelection(studentId, checkbox) {
            if (checkbox.checked) {
                if (!selectedStudents.includes(studentId)) {
                    selectedStudents.push(studentId);
                }
            } else {
                selectedStudents = selectedStudents.filter(id => id !== studentId);
            }
            updateBulkActions();
        }

        function selectAllOnCurrentPage() {
            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, filteredStudents.length);
            const currentPageStudents = filteredStudents.slice(startIndex, endIndex);
            
            currentPageStudents.forEach(student => {
                if (!selectedStudents.includes(student.id)) {
                    selectedStudents.push(student.id);
                }
            });
            displayStudents();
        }

        function selectAllStudents() {
            selectedStudents = filteredStudents.map(student => student.id);
            displayStudents();
        }

        function deselectAllStudents() {
            selectedStudents = [];
            displayStudents();
        }

        function updateBulkActions() {
            const bulkSection = document.getElementById('bulkActionsSection');
            const selectedCount = document.getElementById('selectedCount');
            
            if (selectedStudents.length > 0) {
                bulkSection.style.display = 'block';
                selectedCount.textContent = `${selectedStudents.length} students selected`;
            } else {
                bulkSection.style.display = 'none';
            }
            
            updateProcessButton();
        }

        function updateProcessButton() {
            const button = document.getElementById('processBulkPaymentsBtn');
            const admissionType = document.getElementById('admissionType').value;
            
            button.disabled = !(selectedStudents.length > 0 && admissionType);
        }

        // ================= PROCESS BULK PAYMENTS =================
        async function processBulkPayments() {
            const admissionTypeId = document.getElementById('admissionType').value;
            const admissionType = admissionTypes.find(type => type.id == admissionTypeId);
            
            if (!admissionType) {
                showAlert('Please select a valid admission type', 'warning');
                return;
            }

            if (selectedStudents.length === 0) {
                showAlert('Please select at least one student', 'warning');
                return;
            }

            // Prepare payment data
            const payments = selectedStudents.map(studentId => ({
                student_id: studentId,
                admission_id: parseInt(admissionTypeId),
                amount: admissionType.amount
            }));

            const paymentData = {
                payments: payments
            };

            try {
                // Disable button and show loading
                const button = document.getElementById('processBulkPaymentsBtn');
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                const response = await fetch('/api/payment-admissions/store-pay-admission/bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(paymentData)
                });

                const result = await response.json();

                if (response.ok) {
                    showAlert(`Successfully processed ${selectedStudents.length} admission payments!`, 'success');
                    
                    // Reset selections and reload data
                    selectedStudents = [];
                    document.getElementById('admissionType').value = '';
                    document.getElementById('paymentAmount').value = '';
                    await loadAllStudents(); // Reload to get updated admission status
                } else {
                    throw new Error(result.message || 'Payment processing failed');
                }
            } catch (error) {
                console.error('Error processing bulk payments:', error);
                showAlert('Failed to process payments: ' + error.message, 'danger');
            } finally {
                // Reset button
                const button = document.getElementById('processBulkPaymentsBtn');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-money-bill me-2"></i>Process Payments';
            }
        }

        // ================= UTILITY FUNCTIONS =================
        function showLoadingState() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('studentsTableSection').style.display = 'none';
            document.getElementById('noStudentsMessage').style.display = 'none';
            document.getElementById('bulkActionsSection').style.display = 'none';
        }

        function showContentState() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentsTableSection').style.display = 'block';
            document.getElementById('noStudentsMessage').style.display = 'none';
        }

        function showNoStudentsMessage() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentsTableSection').style.display = 'none';
            document.getElementById('noStudentsMessage').style.display = 'block';
            document.getElementById('bulkActionsSection').style.display = 'none';
        }

        function showErrorState(message) {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentsTableSection').style.display = 'none';
            document.getElementById('noStudentsMessage').style.display = 'block';
            document.getElementById('noStudentsMessage').innerHTML = `
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h4 class="text-danger">Error Loading Students</h4>
                <p class="text-muted">${message}</p>
                <button class="btn btn-primary mt-3" onclick="loadAllStudents()">
                    <i class="fas fa-redo me-2"></i>Try Again
                </button>
            `;
        }

        function showAlert(message, type) {
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
@endpush