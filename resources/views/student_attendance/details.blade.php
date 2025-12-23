@extends('layouts.app')

@section('title', 'Attendance Details')
@section('page-title', 'Attendance Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student_attendance.index') }}">Mark Attendance</a></li>
    <li class="breadcrumb-item active">Attendance Details</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Toast Notification Container -->
        <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;"></div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            Attendance Details
                        </h5>
                        <button id="sendSmsBtn" class="btn btn-light btn-sm">
                            <i class="fas fa-sms me-1"></i> Send SMS
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading attendance details...</p>
                        </div>

                        <!-- Error State -->
                        <div id="errorCard" class="alert alert-danger" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Error Loading Attendance</h6>
                                    <p id="errorMessage" class="mb-0 small">Failed to load attendance details.</p>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button id="retryBtn" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-redo me-1"></i> Retry
                                </button>
                            </div>
                        </div>

                        <!-- Attendance Summary - Compact Version -->
                        <div id="attendanceSummary" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-3 col-6">
                                    <div class="card border-0 shadow-sm mb-2">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-shape bg-primary text-white rounded-circle p-2 me-2">
                                                    <i class="fas fa-calendar-day"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Date</small>
                                                    <h6 id="attendanceDate" class="fw-bold mb-0">-</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="card border-0 shadow-sm mb-2">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-shape bg-info text-white rounded-circle p-2 me-2">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Total</small>
                                                    <h6 id="totalStudents" class="fw-bold mb-0">-</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="card border-0 shadow-sm mb-2">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-shape bg-success text-white rounded-circle p-2 me-2">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Present</small>
                                                    <h6 id="presentCount" class="fw-bold mb-0 text-success">-</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="card border-0 shadow-sm mb-2">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-shape bg-danger text-white rounded-circle p-2 me-2">
                                                    <i class="fas fa-times-circle"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Absent</small>
                                                    <h6 id="absentCount" class="fw-bold mb-0 text-danger">-</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm mb-2">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-shape bg-warning text-white rounded-circle p-2 me-2">
                                                    <i class="fas fa-chart-line"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Attendance %</small>
                                                    <h6 id="attendancePercentage" class="fw-bold mb-0 text-primary">-</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card border-0 shadow-sm mb-2">
                                        <div class="card-body py-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Category</small>
                                                    <span id="categoryName" class="badge bg-primary">-</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Attendance ID</small>
                                                    <span id="attendanceId" class="text-muted">-</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Class ID</small>
                                                    <span id="classId" class="text-muted">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Students List with Pagination -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user-graduate me-2"></i>
                                        Student Attendance List
                                    </h6>
                                    <span class="badge bg-primary" id="studentCountBadge">0 Students</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="15%">Student ID</th>
                                                    <th width="30%">Student Name</th>
                                                    <th width="20%">Guardian Mobile</th>
                                                    <th width="15%">Status</th>
                                                    <th width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="studentsList">
                                                <!-- Student rows will be dynamically inserted here -->
                                                <tr id="noStudentsRow">
                                                    <td colspan="6" class="text-center py-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-user-slash fa-lg mb-2"></i>
                                                            <p class="mb-0 small">No students found</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <div id="paginationContainer" class="d-none">
                                        <nav aria-label="Student pagination" class="px-3 py-2 border-top">
                                            <ul class="pagination pagination-sm justify-content-center mb-0">
                                                <li class="page-item disabled" id="prevPage">
                                                    <a class="page-link" href="#" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                                <li class="page-item active"><a class="page-link" href="#"
                                                        data-page="1">1</a></li>
                                                <li class="page-item" id="nextPage">
                                                    <a class="page-link" href="#" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="text-center mt-1">
                                                <small class="text-muted">
                                                    Showing <span id="currentRange">0-0</span> of <span
                                                        id="totalStudentsCount">0</span> students
                                                </small>
                                            </div>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- Back Button -->
                            <div class="mt-3 text-end">
                                <a href="{{ route('student_attendance.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <div class="icon-shape bg-danger text-white rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <h4 class="modal-title fw-bold text-danger" id="deleteModalLabel">Confirm Deletion</h4>
                        <p class="text-muted mb-0" id="deleteModalText">
                            Are you sure you want to delete this attendance record? This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-top-0 justify-content-center pt-0 pb-4">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-lg px-4" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-2"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Confirmation Modal -->
    <div class="modal fade" id="confirmSmsModal" tabindex="-1" aria-labelledby="confirmSmsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmSmsModalLabel">
                        <i class="fas fa-sms me-2"></i>Confirm SMS Sending
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div id="confirmSmsText"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmSmsSendBtn">
                        <i class="fas fa-paper-plane me-1"></i>Send SMS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Result Modal -->
    <div class="modal fade" id="smsResultModal" tabindex="-1" aria-labelledby="smsResultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="smsResultModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div id="smsResultBody"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="fas fa-check me-1"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom Toast Styles */
        .custom-toast {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
            overflow: hidden;
            margin-bottom: 15px;
            animation: slideInRight 0.3s ease-out;
        }

        .toast-success {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        }

        .toast-error {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .toast-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }

        .toast-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }

        .toast-body {
            color: white;
            padding: 15px 20px;
            font-weight: 500;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        /* Modal Animation */
        .modal.fade .modal-dialog {
            transform: scale(0.9);
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* Delete button animation */
        .delete-attendance-btn {
            transition: all 0.2s ease;
        }

        .delete-attendance-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Extract parameters from URL
            const pathParts = window.location.pathname.split('/');
            const classId = pathParts[2];
            const attendanceId = pathParts[3];
            const classCategoryId = pathParts[4];

            // Modal instances
            let deleteModal = null;
            let smsResultModal = null;
            let confirmSmsModal = null;

            // Variables for delete operation
            let currentDeleteAttendanceId = null;
            let currentDeleteStudentName = null;

            // Pagination variables
            let currentPage = 1;
            const studentsPerPage = 10;
            let allStudents = [];
            let totalPages = 1;

            // SMS sending variables
            let isSendingSMS = false;

            // Initialize Bootstrap Modals
            const modalElement = document.getElementById('deleteModal');
            if (modalElement) {
                deleteModal = new bootstrap.Modal(modalElement);
            }

            const smsResultModalElement = document.getElementById('smsResultModal');
            if (smsResultModalElement) {
                smsResultModal = new bootstrap.Modal(smsResultModalElement);
            }

            const confirmSmsModalElement = document.getElementById('confirmSmsModal');
            if (confirmSmsModalElement) {
                confirmSmsModal = new bootstrap.Modal(confirmSmsModalElement);
            }

            // UI Functions
            const showLoading = (show) => {
                document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
            };

            const showError = (message) => {
                const errorCard = document.getElementById('errorCard');
                const errorMessage = document.getElementById('errorMessage');
                errorMessage.textContent = message;
                errorCard.style.display = 'block';
            };

            const hideError = () => {
                document.getElementById('errorCard').style.display = 'none';
            };

            const showAttendanceData = () => {
                document.getElementById('attendanceSummary').style.display = 'block';
            };

            const hideAttendanceData = () => {
                document.getElementById('attendanceSummary').style.display = 'none';
            };

            // Format date
            const formatDate = (dateString) => {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            };

            // Calculate pagination
            const getPaginatedStudents = (students, page, perPage) => {
                const startIndex = (page - 1) * perPage;
                const endIndex = startIndex + perPage;
                return students.slice(startIndex, endIndex);
            };

            // Update pagination UI
            const updatePagination = (totalItems, currentPage, perPage) => {
                const paginationContainer = document.getElementById('paginationContainer');
                const totalPages = Math.ceil(totalItems / perPage);

                if (totalPages <= 1) {
                    paginationContainer.classList.add('d-none');
                    return;
                }

                paginationContainer.classList.remove('d-none');

                // Update page numbers
                const pageNumbersContainer = paginationContainer.querySelector('.pagination');
                let pageNumbersHtml = '';

                // Previous button
                const prevBtn = document.getElementById('prevPage');
                if (currentPage === 1) {
                    prevBtn.classList.add('disabled');
                } else {
                    prevBtn.classList.remove('disabled');
                }

                // Page numbers
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                for (let i = startPage; i <= endPage; i++) {
                    pageNumbersHtml += `
                                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                                        </li>
                                    `;
                }

                // Replace only the page number items (keep prev/next buttons)
                const pageItems = pageNumbersContainer.querySelectorAll('.page-item:not(#prevPage):not(#nextPage)');
                pageItems.forEach(item => item.remove());

                const nextBtn = document.getElementById('nextPage');
                prevBtn.insertAdjacentHTML('afterend', pageNumbersHtml);

                // Next button
                if (currentPage === totalPages) {
                    nextBtn.classList.add('disabled');
                } else {
                    nextBtn.classList.remove('disabled');
                }

                // Update range text
                const startRange = ((currentPage - 1) * perPage) + 1;
                const endRange = Math.min(currentPage * perPage, totalItems);
                document.getElementById('currentRange').textContent = `${startRange}-${endRange}`;
                document.getElementById('totalStudentsCount').textContent = totalItems;
            };

            // Display student list with pagination
            const displayStudentList = (students) => {
                allStudents = students;
                totalPages = Math.ceil(students.length / studentsPerPage);

                const paginatedStudents = getPaginatedStudents(students, currentPage, studentsPerPage);
                const studentsList = document.getElementById('studentsList');
                const noStudentsRow = document.getElementById('noStudentsRow');

                // Always hide the noStudentsRow initially
                if (noStudentsRow) {
                    noStudentsRow.style.display = 'none';
                }

                if (paginatedStudents.length > 0) {
                    let html = '';
                    const startIndex = (currentPage - 1) * studentsPerPage;

                    paginatedStudents.forEach((student, index) => {
                        const globalIndex = startIndex + index;
                        const statusClass = student.attendance_status === 'present' ? 'success' : 'danger';
                        const statusText = student.attendance_status === 'present' ? 'Present' : 'Absent';
                        const statusIcon = student.attendance_status === 'present' ? 'fa-check-circle' : 'fa-times-circle';

                        // Check if student has attendance_id (present students have it)
                        const hasAttendanceId = student.attendance_id !== null;

                        // Show delete button only for Present students with attendance_id
                        const actionButton = student.attendance_status === 'present' && hasAttendanceId
                            ? `<button class="btn btn-sm btn-outline-danger delete-attendance-btn" 
                                                   data-attendance-id="${student.attendance_id}"
                                                   data-student-name="${student.fname} ${student.lname}">
                                                <i class="fas fa-trash me-1"></i> Delete
                                              </button>`
                            : '<span class="text-muted small">No action</span>';

                        html += `
                                        <tr>
                                            <td class="align-middle">${globalIndex + 1}</td>
                                            <td class="align-middle">
                                                <span class="badge bg-secondary">${student.custom_id}</span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="fw-semibold">${student.fname} ${student.lname}</div>
                                            </td>
                                            <td class="align-middle">
                                                <i class="fas fa-phone me-1 text-muted"></i>
                                                ${student.guardian_mobile || 'N/A'}
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-${statusClass}">
                                                    <i class="fas ${statusIcon} me-1"></i>
                                                    ${statusText}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                ${actionButton}
                                            </td>
                                        </tr>
                                    `;
                    });

                    studentsList.innerHTML = html;

                    // Add event listeners to delete buttons
                    document.querySelectorAll('.delete-attendance-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            currentDeleteAttendanceId = this.getAttribute('data-attendance-id');
                            currentDeleteStudentName = this.getAttribute('data-student-name');
                            showDeleteConfirmation(currentDeleteStudentName);
                        });
                    });

                } else {
                    // Clear the list and show no students message
                    studentsList.innerHTML = '';

                    // Create and show no students row
                    const noStudentsHtml = `
                                    <tr id="noStudentsRow">
                                        <td colspan="6" class="text-center py-3">
                                            <div class="text-muted">
                                                <i class="fas fa-user-slash fa-lg mb-2"></i>
                                                <p class="mb-0 small">No students found</p>
                                            </div>
                                        </td>
                                    </tr>
                                `;

                    studentsList.innerHTML = noStudentsHtml;
                }

                // Update pagination
                updatePagination(students.length, currentPage, studentsPerPage);
            };

            // Show delete confirmation modal
            const showDeleteConfirmation = (studentName) => {
                if (!deleteModal) return;

                // Update modal text
                const modalText = document.getElementById('deleteModalText');
                modalText.innerHTML = `
                                    Are you sure you want to delete the attendance record for 
                                    <strong class="text-danger">${studentName}</strong>?<br><br>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        This action will mark the student as absent and cannot be undone.
                                    </small>
                                `;

                deleteModal.show();
            };

            // Delete attendance record
            const deleteAttendanceRecord = async (attendanceRecordId, studentName) => {
                try {
                    showLoading(true);

                    const apiUrl = `/api/attendances/delete/${attendanceRecordId}`;
                    const response = await fetch(apiUrl, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.status === true || data.status === 'success') {
                        // Show success message
                        showSmsResultModal(
                            'Success!',
                            `Attendance record for <strong>${studentName}</strong> has been deleted successfully!`,
                            'success'
                        );
                        loadAttendanceDetails(); // Reload the data
                    } else {
                        throw new Error(data.message || 'Failed to delete attendance record');
                    }

                } catch (error) {
                    console.error('Error deleting attendance:', error);
                    showSmsResultModal(
                        'Error!',
                        `Failed to delete attendance record: ${error.message}`,
                        'error'
                    );
                } finally {
                    showLoading(false);
                }
            };

            // Change page
            const changePage = (page) => {
                if (page < 1 || page > totalPages) return;

                currentPage = page;
                displayStudentList(allStudents);

                // Scroll to top of table
                const tableResponsive = document.querySelector('.table-responsive');
                if (tableResponsive) {
                    tableResponsive.scrollTop = 0;
                }
            };

            // Load attendance details from API
            const loadAttendanceDetails = async () => {
                showLoading(true);
                hideError();
                hideAttendanceData();

                try {
                    const apiUrl = `/api/attendances/daily/${classId}/${attendanceId}/${classCategoryId}/details`;

                    const response = await fetch(apiUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.status === true) {
                        displayAttendanceDetails(data.data);
                        showAttendanceData();
                    } else {
                        throw new Error(data.message || 'Failed to load attendance details');
                    }

                } catch (error) {
                    console.error('Error:', error);
                    showError(error.message || 'Failed to load attendance details. Please try again.');
                } finally {
                    showLoading(false);
                }
            };

            // Display attendance details
            const displayAttendanceDetails = (data) => {
                // Update summary information
                if (data.summary) {
                    document.getElementById('attendanceDate').textContent = formatDate(data.summary.date);
                    document.getElementById('totalStudents').textContent = data.summary.total_students;
                    document.getElementById('presentCount').textContent = data.summary.present;
                    document.getElementById('absentCount').textContent = data.summary.absent;
                    document.getElementById('attendancePercentage').textContent = `${data.summary.attendance_percentage}%`;
                }

                // Update class information
                if (data.matched_group) {
                    document.getElementById('categoryName').textContent = data.matched_group.category_name;
                }

                document.getElementById('attendanceId').textContent = attendanceId;
                document.getElementById('classId').textContent = classId;

                // Update student count badge
                const studentCount = data.attendance_list?.length || 0;
                document.getElementById('studentCountBadge').textContent = `${studentCount} Student${studentCount !== 1 ? 's' : ''}`;

                // Display student list with pagination
                if (data.attendance_list && data.attendance_list.length > 0) {
                    displayStudentList(data.attendance_list);
                } else {
                    displayStudentList([]);
                }
            };

            // Format phone number to 94 format
            const formatPhoneTo94 = (phoneNumber) => {
                if (!phoneNumber) return '';

                // Remove all non-digit characters
                let cleaned = phoneNumber.replace(/\D/g, '');

                // If it starts with 0, replace with 94
                if (cleaned.startsWith('0')) {
                    cleaned = '94' + cleaned.substring(1);
                }

                // If it starts with +94, remove the +
                if (cleaned.startsWith('+94')) {
                    cleaned = cleaned.substring(1);
                }

                // Ensure it starts with 94
                if (!cleaned.startsWith('94')) {
                    cleaned = '94' + cleaned;
                }

                return cleaned;
            };

            // Validate phone number
            const isValidPhoneNumber = (phoneNumber) => {
                const formatted = formatPhoneTo94(phoneNumber);
                const phoneRegex = /^94[1-9][0-9]{8}$/;
                return phoneRegex.test(formatted);
            };

            // Show SMS confirmation modal
            const showSmsConfirmation = (absentCount) => {
                if (!confirmSmsModal) return;

                // Update modal text
                const confirmText = document.getElementById('confirmSmsText');
                confirmText.innerHTML = `
                                    <div class="text-center">
                                        <i class="fas fa-sms fa-3x text-primary mb-3"></i>
                                        <h5 class="fw-bold">Send SMS to Absent Students</h5>
                                        <p class="mb-0">
                                            Are you sure you want to send SMS notifications to 
                                            <span class="fw-bold text-primary">${absentCount} absent student${absentCount !== 1 ? 's' : ''}</span>?
                                        </p>
                                        <div class="alert alert-info mt-3 small">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Each guardian will receive a notification about their child's absence from <strong>Success Academy</strong>.
                                        </div>
                                    </div>
                                `;

                confirmSmsModal.show();
            };

            // Show SMS result modal
            const showSmsResultModal = (title, message, type = 'info') => {
                if (!smsResultModal) return;

                // Set modal icon and color based on type
                let icon = 'fa-info-circle';
                let iconColor = 'text-primary';

                switch (type) {
                    case 'success':
                        icon = 'fa-check-circle';
                        iconColor = 'text-success';
                        break;
                    case 'error':
                        icon = 'fa-exclamation-circle';
                        iconColor = 'text-danger';
                        break;
                    case 'warning':
                        icon = 'fa-exclamation-triangle';
                        iconColor = 'text-warning';
                        break;
                }

                // Update modal content
                const modalTitle = document.getElementById('smsResultModalLabel');
                const modalBody = document.getElementById('smsResultBody');

                modalTitle.textContent = title;
                modalBody.innerHTML = `
                                    <div class="text-center py-3">
                                        <i class="fas ${icon} fa-3x ${iconColor} mb-3"></i>
                                        <div class="fs-5">${message}</div>
                                        <div class="mt-4 pt-3 border-top text-muted small">
                                            <i class="fas fa-building me-1"></i>
                                            Success Academy - Student Management System
                                        </div>
                                    </div>
                                `;

                smsResultModal.show();
            };

            // Update SMS progress
            const updateSmsProgress = (current, total, message = '') => {
                const progressElement = document.getElementById('smsProgressText');
                if (progressElement) {
                    const percentage = Math.round((current / total) * 100);
                    progressElement.innerHTML = `
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Sending SMS ${current} of ${total}</span>
                                            <span>${percentage}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" 
                                                 style="width: ${percentage}%">
                                            </div>
                                        </div>
                                        ${message ? `<div class="mt-2 small text-muted">${message}</div>` : ''}
                                    `;
                }
            };

            // Create SMS message for absent student
            const createAbsentSmsMessage = (student, attendanceDate, className) => {
                const studentName = student.fname;

                return `Dear Parent/Guardian,\n\n${studentName} was marked absent from ${className} class at Success Academy on ${attendanceDate}. Please contact the administration if you have any concerns.\n\nThank you,\nSuccess Academy`;
            };

            // Send SMS to absent students
            const sendSmsToAbsent = async () => {
                if (isSendingSMS) return;

                // SMS settings check function
                const checkSmsEnabled = () => {
                    try {
                        const savedSettings = localStorage.getItem('sms_settings');
                        console.log('Retrieved SMS settings from localStorage:', savedSettings);

                        if (savedSettings) {
                            const settings = JSON.parse(savedSettings);
                            console.log('Parsed SMS settings:', settings);
                            // Check both sms_enabled and enabled properties
                            return settings.sms_enabled === true || settings.enabled === true;
                        }
                        console.log('No SMS settings found in localStorage');
                        return false; // Default to disabled if no settings
                    } catch (error) {
                        console.error('Error accessing SMS settings:', error);
                        return false; // Default to disabled on error
                    }
                };

                // Check if SMS is enabled
                const isSmsEnabled = checkSmsEnabled();
                console.log('Is SMS enabled?', isSmsEnabled);

                if (!isSmsEnabled) {
                    showSmsResultModal(
                        'SMS Not Enabled',
                        '<div class="text-center">' +
                        '<i class="fas fa-bell-slash fa-3x text-warning mb-3"></i>' +
                        '<h5 class="fw-bold">SMS Sending is Disabled</h5>' +
                        '<p class="mb-2">SMS notifications are currently turned off in your settings.</p>' +
                        '<div class="alert alert-info mt-3 small">' +
                        '<i class="fas fa-info-circle me-2"></i>' +
                        'To enable SMS sending, go to Settings and turn on SMS notifications.' +
                        '</div>' +
                        '</div>',
                        'warning'
                    );
                    return;
                }

                const absentStudents = allStudents.filter(student =>
                    student.attendance_status === 'absent'
                );

                if (absentStudents.length === 0) {
                    showSmsResultModal(
                        'No Absent Students',
                        'There are no absent students to send SMS notifications to.',
                        'info'
                    );
                    return;
                }

                // Filter students with valid mobile numbers
                const validStudents = absentStudents.filter(student =>
                    student.guardian_mobile &&
                    student.guardian_mobile.trim() !== '' &&
                    isValidPhoneNumber(student.guardian_mobile)
                );

                const invalidStudents = absentStudents.filter(student =>
                    !student.guardian_mobile ||
                    student.guardian_mobile.trim() === '' ||
                    !isValidPhoneNumber(student.guardian_mobile)
                );

                // Show confirmation modal
                showSmsConfirmation(validStudents.length);

                // Handle confirmation
                document.getElementById('confirmSmsSendBtn').onclick = async () => {
                    // Double-check SMS is still enabled
                    if (!checkSmsEnabled()) {
                        showSmsResultModal(
                            'SMS Disabled',
                            'SMS sending was disabled during the process. Please check your settings.',
                            'warning'
                        );
                        confirmSmsModal.hide();
                        return;
                    }

                    confirmSmsModal.hide();
                    isSendingSMS = true;
                    showLoading(true);

                    // Show progress modal
                    const progressBody = document.getElementById('smsResultBody');
                    progressBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mb-2">Sending SMS Notifications</h5>
                    <p class="text-muted mb-3">Please wait while we send SMS to absent students...</p>
                    <div id="smsProgressText" class="mt-3"></div>
                </div>
            `;

                    const modalTitle = document.getElementById('smsResultModalLabel');
                    modalTitle.textContent = 'Sending SMS...';
                    smsResultModal.show();

                    try {
                        // Get attendance data for SMS content
                        const attendanceDate = document.getElementById('attendanceDate').textContent;
                        const className = document.getElementById('categoryName').textContent;

                        const results = {
                            success: 0,
                            failed: 0,
                            total: validStudents.length
                        };

                        // Send SMS to each valid student
                        for (let i = 0; i < validStudents.length; i++) {
                            const student = validStudents[i];

                            // Update progress
                            updateSmsProgress(i + 1, validStudents.length, `Sending to ${student.fname} ${student.lname}`);

                            const formattedMobile = formatPhoneTo94(student.guardian_mobile);
                            const message = createAbsentSmsMessage(student, attendanceDate, className);

                            const smsData = {
                                mobile: formattedMobile,
                                message: message,
                                student_id: student.student_id || student.custom_id,
                                student_name: `${student.fname} ${student.lname}`
                            };

                            try {
                                const response = await fetch('/api/send-sms', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify(smsData)
                                });

                                const data = await response.json();
                                console.log('SMS API Response:', data);

                                if (response.ok && (data.status === 'success' || data.success === true)) {
                                    results.success++;
                                } else {
                                    results.failed++;
                                    console.warn(`SMS failed for ${student.fname}:`, data.message);
                                }

                            } catch (error) {
                                results.failed++;
                                console.error(`Error sending SMS to ${student.fname}:`, error);
                            }

                            // Small delay between requests to avoid rate limiting
                            await new Promise(resolve => setTimeout(resolve, 100));
                        }

                        // Prepare result message
                        let resultMessage = '';
                        let resultType = 'success';

                        if (results.success === results.total && results.total > 0) {
                            resultMessage = `
                        <div class="text-center">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="fw-bold">SMS Sent Successfully!</h5>
                            <p class="mb-2">
                                Successfully sent SMS notifications to 
                                <span class="fw-bold text-success">${results.success}</span> 
                                absent student${results.success !== 1 ? 's' : ''}.
                            </p>
                            <p class="small text-muted">
                                All guardians have been notified about their child's absence from Success Academy.
                            </p>
                        </div>
                    `;
                        } else if (results.success > 0) {
                            resultMessage = `
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5 class="fw-bold">Partially Completed</h5>
                            <p class="mb-2">
                                <span class="fw-bold text-success">${results.success}</span> SMS sent successfully,
                                <span class="fw-bold text-danger">${results.failed}</span> failed to send.
                            </p>
                            <div class="alert alert-warning mt-3 small">
                                <i class="fas fa-info-circle me-2"></i>
                                Some SMS messages may not have been delivered. Please check the logs for details.
                            </div>
                        </div>
                    `;
                            resultType = 'warning';
                        } else {
                            resultMessage = `
                        <div class="text-center">
                            <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                            <h5 class="fw-bold">Failed to Send SMS</h5>
                            <p class="mb-2">
                                Unable to send SMS notifications to absent students.
                            </p>
                            <div class="alert alert-danger mt-3 small">
                                <i class="fas fa-info-circle me-2"></i>
                                Please check your SMS API configuration and try again.
                            </div>
                        </div>
                    `;
                            resultType = 'error';
                        }

                        // Add invalid numbers info if any
                        if (invalidStudents.length > 0) {
                            resultMessage += `
                        <div class="mt-4 pt-3 border-top">
                            <p class="small text-muted mb-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>${invalidStudents.length} student${invalidStudents.length !== 1 ? 's' : ''}</strong> 
                                skipped due to invalid or missing mobile numbers.
                            </p>
                        </div>
                    `;
                        }

                        // Add institution footer
                        resultMessage += `
                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-building me-1"></i>
                            <strong>Success Academy</strong> - Excellence in Education
                        </p>
                        <p class="mb-0 small text-muted">
                            Student Management System
                        </p>
                    </div>
                `;

                        // Show final result
                        modalTitle.textContent = resultType === 'success' ? 'Success!' :
                            resultType === 'warning' ? 'Warning!' : 'Error!';
                        progressBody.innerHTML = resultMessage;

                    } catch (error) {
                        console.error('Error in sendSmsToAbsent:', error);

                        modalTitle.textContent = 'Error!';
                        progressBody.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                        <h5 class="fw-bold">System Error</h5>
                        <p class="mb-3">An unexpected error occurred while sending SMS:</p>
                        <div class="alert alert-danger small">
                            ${error.message}
                        </div>
                        <div class="mt-4 pt-3 border-top text-muted small">
                            <i class="fas fa-building me-1"></i>
                            Success Academy - Please contact system administrator
                        </div>
                    </div>
                `;
                    } finally {
                        isSendingSMS = false;
                        showLoading(false);
                    }
                };
            };

            // Event Listeners
            const setupEventListeners = () => {
                // Retry button
                document.getElementById('retryBtn').addEventListener('click', loadAttendanceDetails);

                // Send SMS button
                document.getElementById('sendSmsBtn').addEventListener('click', sendSmsToAbsent);

                // Confirm delete button
                document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                    if (currentDeleteAttendanceId && currentDeleteStudentName) {
                        deleteModal.hide();
                        deleteAttendanceRecord(currentDeleteAttendanceId, currentDeleteStudentName);
                    }
                });

                // Modal close events
                modalElement.addEventListener('hidden.bs.modal', () => {
                    currentDeleteAttendanceId = null;
                    currentDeleteStudentName = null;
                });

                smsResultModalElement.addEventListener('hidden.bs.modal', () => {
                    // Reset modal for next use
                    const progressBody = document.getElementById('smsResultBody');
                    if (progressBody) {
                        progressBody.innerHTML = '';
                    }
                });

                // Pagination event delegation
                document.addEventListener('click', function (e) {
                    // Page number click
                    if (e.target.classList.contains('page-link') && e.target.dataset.page) {
                        e.preventDefault();
                        changePage(parseInt(e.target.dataset.page));
                    }

                    // Previous button click
                    if (e.target.closest('#prevPage') && !e.target.closest('#prevPage').classList.contains('disabled')) {
                        e.preventDefault();
                        changePage(currentPage - 1);
                    }

                    // Next button click
                    if (e.target.closest('#nextPage') && !e.target.closest('#nextPage').classList.contains('disabled')) {
                        e.preventDefault();
                        changePage(currentPage + 1);
                    }
                });
            };

            // Initialize
            const initialize = () => {
                setupEventListeners();
                loadAttendanceDetails();
            };

            // Start the application
            initialize();
        });
    </script>
@endpush