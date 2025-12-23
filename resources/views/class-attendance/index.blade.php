@extends('layouts.app')

@section('title', 'Class Attendance')
@section('page-title', 'Class Attendance Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('class_rooms.index') }}">Class Rooms</a></li>
    <li class="breadcrumb-item active">Class Attendance</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Class Attendance
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Class Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="class-info p-3 bg-light rounded">
                                <h6 class="fw-bold">Class & Hall Details:</h6>
                                <div id="classDetails">
                                    <div class="text-center py-2">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Loading class information...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Date Search Filter -->
                            <div class="filters-section">
                                <div class="card">
                                    <div class="card-header bg-transparent">
                                        <h6 class="card-title mb-0">Search Attendance</h6>
                                    </div>
                                    <div class="card-body">
                                        <form id="dateSearchForm">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label for="searchDate" class="form-label">Search by Date</label>
                                                        <input type="date" class="form-control" id="searchDate"
                                                            name="searchDate">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">&nbsp;</label>
                                                        <button type="submit" class="btn btn-primary w-100">
                                                            <i class="fas fa-search me-2"></i>Search
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <button type="button" class="btn btn-outline-secondary w-100"
                                            onclick="clearSearch()">
                                            <i class="fas fa-sync-alt me-2"></i>Show All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-tasks me-2"></i>Bulk Actions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted" id="pendingCount">Pending records: 0</span>
                                            <span class="text-muted ms-3" id="selectedCount">Selected: 0</span>
                                        </div>
                                        <div>
                                            <button class="btn btn-danger" onclick="deleteSelectedAttendance()"
                                                id="bulkDeleteBtn" disabled>
                                                <i class="fas fa-trash me-2"></i>Delete Selected Pending Attendance
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Summary -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Attendance Summary</h6>
                                <div>
                                    <button class="btn btn-success btn-sm me-2" onclick="generatePDF('all')">
                                        <i class="fas fa-file-pdf me-2"></i>Export All PDF
                                    </button>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-success"
                                            onclick="generatePDF('marked')">Marked</button>
                                        <button class="btn btn-outline-danger" onclick="generatePDF('not_marked')">Not
                                            Marked</button>
                                        <button class="btn btn-outline-warning"
                                            onclick="generatePDF('pending')">Pending</button>
                                        <button class="btn btn-outline-secondary"
                                            onclick="generatePDF('deleted')">Deleted</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3" id="attendanceSummary">
                                <!-- Summary cards will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">Attendance Records</h6>
                                        <button class="btn btn-outline-primary btn-sm" onclick="loadAttendanceData()"
                                            title="Refresh">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="attendanceTable">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th width="50">
                                                        <input type="checkbox" id="selectAllPending"
                                                            onchange="toggleSelectAll(this)">
                                                    </th>
                                                    <th width="50">#</th>
                                                    <th>Date</th>
                                                    <th>Day</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Hall</th>
                                                    <th>Status</th>
                                                    <th width="120" class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="attendanceTableBody">
                                                <!-- Attendance data will be loaded here -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination Section -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div id="paginationContainer" class="d-none">
                                                <!-- Pagination controls will be loaded here -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Loading State -->
                                    <div id="attendanceLoading" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Loading attendance records...</p>
                                    </div>

                                    <!-- Empty State -->
                                    <div id="attendanceEmpty" class="text-center py-5 d-none">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                                        </div>
                                        <h4 class="text-muted">No Attendance Records</h4>
                                        <p class="text-muted mb-4">No attendance records found for this class.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editAttendanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editAttendanceModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Attendance
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAttendanceForm">
                        <input type="hidden" id="edit_attendance_id">
                        <input type="hidden" id="edit_class_category_has_student_class_id">

                        <!-- Date Selection -->
                        <div class="mb-3">
                            <label for="edit_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                        </div>

                        <!-- Day of Week Selection -->
                        <div class="mb-3">
                            <label for="edit_day_of_week" class="form-label">Day of Week <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="edit_day_of_week" name="day_of_week" required>
                                <option value="">Select Day</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>

                        <!-- Start Time -->
                        <div class="mb-3">
                            <label for="edit_start_time" class="form-label">Start Time <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_start_time" name="start_time" required>
                        </div>

                        <!-- End Time -->
                        <div class="mb-3">
                            <label for="edit_end_time" class="form-label">End Time <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_end_time" name="end_time" required>
                        </div>

                        <!-- Hall Selection -->
                        <div class="mb-3">
                            <label for="edit_class_hall_id" class="form-label">Hall <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="edit_class_hall_id" name="class_hall_id" required>
                                <option value="">Select Hall</option>
                                <!-- Hall options will be loaded dynamically -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="updateAttendanceBtn">
                        <i class="fas fa-save me-2"></i>Update Attendance
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Table Row Styles */
        .attendance-marked {
            background-color: #d4edda !important;
        }

        .attendance-not-marked {
            background-color: #f8d7da !important;
        }

        .attendance-pending {
            background-color: #fff3cd !important;
        }

        .attendance-deleted {
            background-color: #f8f9fa !important;
            text-decoration: line-through;
            color: #6c757d !important;
        }

        .attendance-deleted td {
            opacity: 0.6;
        }

        /* Summary Card Styles */
        .summary-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        }

        .summary-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .summary-card .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .summary-card h2 {
            font-size: 2.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        /* Card Color Themes */
        .total-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .marked-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .not-marked-card {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .pending-card {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: #2c3e50;
        }

        .deleted-card {
            background: linear-gradient(135deg, #868f96 0%, #596164 100%);
            color: white;
        }

        /* Summary Icon Styles */
        .summary-icon {
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .summary-card:hover .summary-icon {
            transform: scale(1.1);
            opacity: 1;
        }

        /* Progress Bar Styles */
        .summary-card .progress {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            height: 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .pending-card .progress {
            background: rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* Text Styles */
        .summary-card .text-muted {
            opacity: 0.9;
        }

        .total-card .text-muted,
        .marked-card .text-muted,
        .not-marked-card .text-muted,
        .deleted-card .text-muted {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Pagination Styles */
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        .pagination .page-link {
            color: #007bff;
            border-radius: 5px;
            margin: 0 2px;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        /* Checkbox Styles */
        .select-checkbox {
            transform: scale(1.2);
        }

        /* Main Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Additional Utility Styles */
        .opacity-75 {
            opacity: 0.75 !important;
        }

        .border-opacity-25 {
            border-opacity: 0.25 !important;
        }

        .bg-opacity-25 {
            background-color: rgba(var(--bs-white-rgb), 0.25) !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        const classCategoryHasStudentClassId = {{ $id }};
        let classattendanceId, hallId;
        let allAttendanceData = [];
        let selectedAttendanceIds = [];
        let pendingAttendanceIds = [];

        // Global pagination variables
        let currentPage = 1;
        let recordsPerPage = 10;
        let totalPages = 1;

        document.addEventListener('DOMContentLoaded', function () {
            loadClassDetails();
            loadAttendanceData();
            // Don't pre-load halls here, will load when modal opens

            // Date search form
            document.getElementById('dateSearchForm').addEventListener('submit', function (e) {
                e.preventDefault();
                filterByDate();
            });

            // Update attendance button
            document.getElementById('updateAttendanceBtn').addEventListener('click', updateAttendance);
        });

        // Load Class Details
        async function loadClassDetails() {
            try {
                const response = await fetch(`/api/class-attendances/${classCategoryHasStudentClassId}`);
                const data = await response.json();
                const classDetailsDiv = document.getElementById('classDetails');

                if (data.data && data.data.length > 0) {
                    const firstRecord = data.data[0];
                    const classData = firstRecord.class_category_student_class;
                    const hallData = firstRecord.hall;

                    // Load class room details
                    let classRoomDetails = 'N/A';
                    if (classData.student_classes_id) {
                        try {
                            const classResponse = await fetch(`/api/class-rooms/${classData.student_classes_id}`);
                            const classResult = await classResponse.json();
                            if (classResult.status === 'success' && classResult.data) {
                                const classInfo = classResult.data;
                                classRoomDetails = `${classInfo.class_name} - Grade ${classInfo.grade.grade_name} - ${classInfo.subject.subject_name}`;
                            }
                        } catch (error) {
                            console.error('Error loading class room details:', error);
                        }
                    }

                    // Load category details
                    let categoryDetails = 'N/A';
                    if (classData.class_category_id) {
                        try {
                            const categoryResponse = await fetch(`/api/categories/${classData.class_category_id}`);
                            const categoryResult = await categoryResponse.json();
                            if (categoryResult.status === 'success' && categoryResult.data) {
                                categoryDetails = categoryResult.data.category_name;
                            }
                        } catch (error) {
                            console.error('Error loading category details:', error);
                        }
                    }

                    classDetailsDiv.innerHTML = `
                                                                    <p class="mb-1"><strong>Class Category Student Class ID:</strong> ${classCategoryHasStudentClassId}</p>
                                                                    <p class="mb-1"><strong>Fees:</strong> Rs. ${classData.fees || '0'}</p>
                                                                    <p class="mb-1"><strong>Student Class:</strong> ${classRoomDetails}</p>
                                                                    <p class="mb-1"><strong>Class Category:</strong> ${categoryDetails}</p>
                                                                    ${hallData ? `<p class="mb-0"><strong>Default Hall:</strong> ${hallData.hall_name} (${hallData.hall_id})</p>` : ''}
                                                                `;
                } else {
                    classDetailsDiv.innerHTML = `
                                                                    <p class="mb-0"><strong>Class Category Student Class ID:</strong> ${classCategoryHasStudentClassId}</p>
                                                                    <p class="mb-0 text-muted">No detailed information available</p>
                                                                `;
                }
            } catch (error) {
                console.error('Error loading class details:', error);
                document.getElementById('classDetails').innerHTML = `
                                                                <p class="mb-0"><strong>Class Category Student Class ID:</strong> ${classCategoryHasStudentClassId}</p>
                                                                <p class="mb-0 text-muted">Failed to load class details</p>
                                                            `;
            }
        }

        // Load Attendance Data
        function loadAttendanceData() {
            showAttendanceLoading();
            selectedAttendanceIds = []; // Clear selected items
            updateSelectedCount();

            fetch(`/api/class-attendances/${classCategoryHasStudentClassId}`)
                .then(response => response.json())
                .then(data => {
                    allAttendanceData = data.data || [];
                    renderAttendanceTable(allAttendanceData);
                    updateAttendanceSummary(allAttendanceData);
                    updatePendingCount();
                    hideAttendanceLoading();
                })
                .catch(error => {
                    console.error('Error loading attendance data:', error);
                    showAlert('Error loading attendance records', 'danger');
                    hideAttendanceLoading();
                    document.getElementById('attendanceEmpty').classList.remove('d-none');
                });
        }

        // Update Pending Count
        function updatePendingCount() {
            const today = new Date();
            const pendingRecords = allAttendanceData.filter(record =>
                record.is_ongoing === 1 && // Only include active records
                record.status == 0 &&
                new Date(record.date) > today
            );

            pendingAttendanceIds = pendingRecords.map(record => record.id);
            document.getElementById('pendingCount').textContent = `Pending records: ${pendingRecords.length}`;

            // Enable/disable bulk delete button based on selection
            updateBulkDeleteButton();
        }

        // Update Selected Count
        function updateSelectedCount() {
            document.getElementById('selectedCount').textContent = `Selected: ${selectedAttendanceIds.length}`;
            updateBulkDeleteButton();
        }

        // Update Bulk Delete Button State
        function updateBulkDeleteButton() {
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            bulkDeleteBtn.disabled = selectedAttendanceIds.length === 0;
        }

        // Toggle Select All
        function toggleSelectAll(checkbox) {
            const today = new Date();
            const pendingRecords = allAttendanceData.filter(record =>
                record.is_ongoing == 1 && // Only active records
                record.status == 0 &&
                new Date(record.date) > today
            );

            if (checkbox.checked) {
                selectedAttendanceIds = [...pendingRecords.map(record => record.id)];
            } else {
                selectedAttendanceIds = [];
            }

            // Update all checkboxes in the table
            document.querySelectorAll('.attendance-checkbox').forEach(cb => {
                cb.checked = checkbox.checked;
            });

            updateSelectedCount();
        }

        // Toggle Single Selection
        function toggleSelection(attendanceId, checkbox) {
            if (checkbox.checked) {
                selectedAttendanceIds.push(attendanceId);
            } else {
                selectedAttendanceIds = selectedAttendanceIds.filter(id => id !== attendanceId);
            }

            // Update "Select All" checkbox state
            updateSelectAllCheckbox();
            updateSelectedCount();
        }

        // Update Select All Checkbox
        function updateSelectAllCheckbox() {
            const today = new Date();
            const pendingRecords = allAttendanceData.filter(record =>
                record.is_ongoing == 1 && // Only active records
                record.status == 0 &&
                new Date(record.date) > today
            );

            const selectAllCheckbox = document.getElementById('selectAllPending');
            if (pendingRecords.length > 0 && selectedAttendanceIds.length === pendingRecords.length) {
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.checked = false;
            }
        }

        // Delete Selected Attendance
        function deleteSelectedAttendance() {
            if (selectedAttendanceIds.length === 0) {
                showAlert('Please select pending attendance records to delete', 'warning');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${selectedAttendanceIds.length} selected pending attendance records?`)) {
                return;
            }

            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const originalText = bulkDeleteBtn.innerHTML;

            bulkDeleteBtn.disabled = true;
            bulkDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';

            fetch('/api/class-attendances/bulk-delete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ids: selectedAttendanceIds
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Selected pending attendance records deleted successfully!', 'success');
                        loadAttendanceData(); // Reload data
                    } else {
                        throw new Error(data.message || 'Failed to delete selected attendance');
                    }
                })
                .catch(error => {
                    console.error('Error deleting selected attendance:', error);
                    showAlert('Error deleting selected attendance: ' + error.message, 'danger');
                })
                .finally(() => {
                    bulkDeleteBtn.disabled = false;
                    bulkDeleteBtn.innerHTML = originalText;
                });
        }

        // Render Attendance Table - UPDATED with date formatting
        function renderAttendanceTable(attendanceData) {
            const tbody = document.getElementById('attendanceTableBody');
            const emptyState = document.getElementById('attendanceEmpty');
            const selectAllCheckbox = document.getElementById('selectAllPending');

            if (!tbody) return;

            tbody.innerHTML = '';
            selectAllCheckbox.checked = false;

            if (attendanceData.length === 0) {
                emptyState.classList.remove('d-none');
                document.getElementById('paginationContainer').classList.add('d-none');
                return;
            }

            emptyState.classList.add('d-none');
            document.getElementById('paginationContainer').classList.remove('d-none');

            // Calculate pagination
            totalPages = Math.ceil(attendanceData.length / recordsPerPage);
            const startIndex = (currentPage - 1) * recordsPerPage;
            const endIndex = startIndex + recordsPerPage;
            const paginatedData = attendanceData.slice(startIndex, endIndex);

            // Current date for comparison
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Render table rows
            paginatedData.forEach((record, index) => {
                const actualIndex = startIndex + index;
                const classattendanceId = record.id;
                const classCategoryHasStudentClassId = record.class_category_has_student_class_id;

                // Format the date from ISO to YYYY-MM-DD
                const formattedDate = formatDate(record.date);

                // Date for comparison (remove time part)
                const recordDate = new Date(record.date);
                recordDate.setHours(0, 0, 0, 0);

                const isPastDate = recordDate < today;
                const isFutureDate = recordDate > today;
                const isToday = recordDate.getTime() === today.getTime();
                const isDeleted = record.is_ongoing == 0;

                // Check if marked based on your API
                const isMarked = record.status == 1;

                let statusText, statusClass, canEdit, showCheckbox;

                // FIRST: Check if record is deleted
                if (isDeleted) {
                    statusText = "Deleted";
                    statusClass = "attendance-deleted";
                    canEdit = false;
                    showCheckbox = false;
                }
                // SECOND: Check if record is marked
                else if (isMarked) {
                    statusText = "Marked";
                    statusClass = "attendance-marked";
                    canEdit = false;
                    showCheckbox = false;
                }
                // THIRD: Check if record is not marked
                else {
                    if (isPastDate) {
                        statusText = "Not Marked";
                        statusClass = "attendance-not-marked";
                        canEdit = false;
                        showCheckbox = false;
                    }
                    else if (isFutureDate) {
                        statusText = "Pending";
                        statusClass = "attendance-pending";
                        canEdit = true;
                        showCheckbox = true;
                    }
                    else if (isToday) {
                        // Today's records
                        const currentTime = new Date();
                        const recordEndTime = new Date(record.date);

                        if (record.end_time) {
                            // Combine date with end_time
                            const [hours, minutes] = record.end_time.split(':');
                            recordEndTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                        }

                        if (currentTime > recordEndTime) {
                            // Class time has passed today
                            statusText = "Not Marked";
                            statusClass = "attendance-not-marked";
                            canEdit = false;
                            showCheckbox = false;
                        } else {
                            // Class is still pending for today
                            statusText = "Pending";
                            statusClass = "attendance-pending";
                            canEdit = true;
                            showCheckbox = true;
                        }
                    }
                }

                const isSelected = selectedAttendanceIds.includes(classattendanceId);
                const checkbox = showCheckbox ?
                    `<input type="checkbox" class="attendance-checkbox select-checkbox" 
                          onchange="toggleSelection(${classattendanceId}, this)" 
                          ${isSelected ? 'checked' : ''}>` :
                    '';

                const row = `
                    <tr class="${statusClass}">
                        <td>${checkbox}</td>
                        <td class="fw-bold text-muted">${actualIndex + 1}</td>
                        <td>${formattedDate}</td>
                        <td>${record.day_of_week || 'N/A'}</td>
                        <td>${record.start_time || 'N/A'}</td>
                        <td>${record.end_time || 'N/A'}</td>
                        <td>${record.hall ? record.hall.hall_name : 'N/A'}</td>
                        <td>
                            <span class="badge ${getStatusBadgeClass(statusClass)}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="text-center">
                            ${canEdit ?
                        `<button class="btn btn-outline-warning btn-sm" title="Edit Attendance" 
                                    onclick="editAttendance(
                                        ${classattendanceId},
                                        ${classCategoryHasStudentClassId},
                                        '${formattedDate}',
                                        '${record.day_of_week || ''}',
                                        '${record.start_time || ''}',
                                        '${record.end_time || ''}',
                                        '${record.status}',
                                        ${record.class_hall_id}
                                    )">
                                    <i class="fas fa-edit"></i>
                                </button>`
                        :
                        '<button class="btn btn-outline-secondary btn-sm" disabled title="Cannot Edit"><i class="fas fa-edit"></i></button>'
                    }
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            // Update pagination controls
            updatePaginationControls(attendanceData.length);
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';

            try {
                // ISO 8601 format දිනය ගන්න (2025-08-10T18:30:00.000000Z)
                const date = new Date(dateString);

                // YYYY-MM-DD ආකෘතියට හරවන්න
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0'); // මාස 0-11 නිසා +1 කරන්න
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            } catch (error) {
                console.error('Error formatting date:', dateString, error);
                return 'Invalid Date';
            }
        }

        // Helper function to get badge class based on status
        function getStatusBadgeClass(statusClass) {
            if (statusClass.includes('marked')) return 'bg-success';
            if (statusClass.includes('pending')) return 'bg-warning';
            if (statusClass.includes('deleted')) return 'bg-secondary';
            return 'bg-danger';
        }

        // Edit Attendance
        async function editAttendance(classattendanceId, classCategoryHasStudentClassId, date, dayOfWeek, startTime, endTime, status, classHallId) {
            document.getElementById('edit_attendance_id').value = classattendanceId;
            document.getElementById('edit_class_category_has_student_class_id').value = classCategoryHasStudentClassId;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_day_of_week').value = dayOfWeek;
            document.getElementById('edit_start_time').value = startTime;
            document.getElementById('edit_end_time').value = endTime;

            const modal = new bootstrap.Modal(document.getElementById('editAttendanceModal'));
            modal.show();

            try {
                // Load halls and wait for it to complete
                await loadHallsDropdown();

                // Set the hall value after dropdown is fully loaded
                if (classHallId) {
                    document.getElementById('edit_class_hall_id').value = classHallId;
                }
            } catch (error) {
                console.error('Error setting hall value:', error);
            }
        }

        // Update Attendance
        function updateAttendance() {
            const updateBtn = document.getElementById('updateAttendanceBtn');
            const originalText = updateBtn.innerHTML;

            const attendanceId = document.getElementById('edit_attendance_id').value;
            const data = {
                status: "0", // Always send 0 as per requirement
                date: document.getElementById('edit_date').value,
                day_of_week: document.getElementById('edit_day_of_week').value,
                start_time: document.getElementById('edit_start_time').value,
                end_time: document.getElementById('edit_end_time').value,
                class_hall_id: document.getElementById('edit_class_hall_id').value,
                class_category_has_student_class_id: document.getElementById('edit_class_category_has_student_class_id').value
            };

            // Validation
            if (!data.date || !data.day_of_week || !data.start_time || !data.end_time || !data.class_hall_id) {
                showAlert('Please fill all required fields', 'warning');
                return;
            }

            updateBtn.disabled = true;
            updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            fetch(`/api/class-attendances/${attendanceId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editAttendanceModal'));
                        modal.hide();
                        loadAttendanceData();
                        showAlert('Attendance updated successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to update attendance');
                    }
                })
                .catch(error => {
                    console.error('Error updating attendance:', error);
                    showAlert('Error updating attendance: ' + error.message, 'danger');
                })
                .finally(() => {
                    updateBtn.disabled = false;
                    updateBtn.innerHTML = originalText;
                });
        }

        function updateAttendanceSummary(attendanceData) {
            const summaryDiv = document.getElementById('attendanceSummary');
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const totalRecords = attendanceData.length;

            // 1. මකා දැමූ පැමිණීම් (Deleted)
            const deletedRecords = attendanceData.filter(record => record.is_ongoing == 0).length;

            // 2. ක්‍රියාකාරී පැමිණීම් (Active)
            const activeRecords = attendanceData.filter(record => record.is_ongoing == 1);

            // 3. විවිධ කාණ්ඩ ගණන් කරන්න
            let markedRecords = 0;
            let notMarkedRecords = 0;
            let pendingRecords = 0;
            let todayPendingRecords = 0;

            activeRecords.forEach(record => {
                const recordDate = new Date(record.date);
                recordDate.setHours(0, 0, 0, 0);

                // IMPORTANT: Check if status is marked (based on your API)
                // If status is "1" or 1, it's marked
                const isMarked = record.status == 1;

                if (isMarked) {
                    markedRecords++;
                } else {
                    // Not marked records
                    if (recordDate < today) {
                        // Past dates - Not Marked
                        notMarkedRecords++;
                    } else if (recordDate > today) {
                        // Future dates - Pending
                        pendingRecords++;
                    } else {
                        // Today's date
                        const currentTime = new Date();

                        // Check if class time has ended
                        if (record.end_time) {
                            const [hours, minutes] = record.end_time.split(':');
                            const endTime = new Date();
                            endTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);

                            if (currentTime > endTime) {
                                // Class time has passed
                                notMarkedRecords++;
                            } else {
                                // Class time hasn't passed yet
                                pendingRecords++;
                                todayPendingRecords++;
                            }
                        } else {
                            // No end time specified
                            pendingRecords++;
                            todayPendingRecords++;
                        }
                    }
                }
            });

            // Debug logging
            console.log('=== ATTENDANCE SUMMARY DEBUG ===');
            console.log('Total Records:', totalRecords);
            console.log('Active Records:', activeRecords.length);
            console.log('Deleted Records:', deletedRecords);
            console.log('Marked Records:', markedRecords);
            console.log('Not Marked Records:', notMarkedRecords);
            console.log('Pending Records:', pendingRecords);
            console.log('Today Pending:', todayPendingRecords);
            console.log('===============================');

            // Calculate percentages
            const activeTotal = activeRecords.length;
            const markedPercentage = activeTotal > 0 ? ((markedRecords / activeTotal) * 100).toFixed(1) : 0;
            const notMarkedPercentage = activeTotal > 0 ? ((notMarkedRecords / activeTotal) * 100).toFixed(1) : 0;
            const pendingPercentage = activeTotal > 0 ? ((pendingRecords / activeTotal) * 100).toFixed(1) : 0;
            const deletedPercentage = totalRecords > 0 ? ((deletedRecords / totalRecords) * 100).toFixed(1) : 0;

            // Update the HTML (same as before)
            summaryDiv.innerHTML = `
                            <div class="col-xl-2 col-md-4 mb-4">
                                <div class="summary-card total-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <div class="card-body position-relative">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mb-1 fw-bold">${totalRecords}</h2>
                                                <p class="mb-0 opacity-75">Total Records</p>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top border-white border-opacity-25">
                                            <small class="opacity-75">All attendance records</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 mb-4">
                                <div class="summary-card marked-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <div class="card-body position-relative">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mb-1 fw-bold">${markedRecords}</h2>
                                                <p class="mb-0 opacity-75">Marked</p>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top border-white border-opacity-25">
                                            <div class="progress bg-white bg-opacity-25" style="height: 8px; border-radius: 10px;">
                                                <div class="progress-bar bg-white" style="width: ${markedPercentage}%"></div>
                                            </div>
                                            <small class="opacity-75 d-block mt-1">${markedPercentage}% of active</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 mb-4">
                                <div class="summary-card not-marked-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                    <div class="card-body position-relative">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mb-1 fw-bold">${notMarkedRecords}</h2>
                                                <p class="mb-0 opacity-75">Not Marked</p>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-times-circle fa-2x opacity-75"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top border-white border-opacity-25">
                                            <div class="progress bg-white bg-opacity-25" style="height: 8px; border-radius: 10px;">
                                                <div class="progress-bar bg-white" style="width: ${notMarkedPercentage}%"></div>
                                            </div>
                                            <small class="opacity-75 d-block mt-1">${notMarkedPercentage}% of active</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 mb-4">
                                <div class="summary-card pending-card" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #2c3e50;">
                                    <div class="card-body position-relative">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mb-1 fw-bold">${pendingRecords}</h2>
                                                <p class="mb-0 opacity-75">Pending</p>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-clock fa-2x opacity-75"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top border-dark border-opacity-25">
                                            <div class="progress bg-dark bg-opacity-10" style="height: 8px; border-radius: 10px;">
                                                <div class="progress-bar bg-warning" style="width: ${pendingPercentage}%"></div>
                                            </div>
                                            <small class="opacity-75 d-block mt-1">${pendingPercentage}% of active</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 mb-4">
                                <div class="summary-card deleted-card" style="background: linear-gradient(135deg, #868f96 0%, #596164 100%);">
                                    <div class="card-body position-relative">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mb-1 fw-bold">${deletedRecords}</h2>
                                                <p class="mb-0 opacity-75">Deleted</p>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-trash fa-2x opacity-75"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top border-white border-opacity-25">
                                            <div class="progress bg-white bg-opacity-25" style="height: 8px; border-radius: 10px;">
                                                <div class="progress-bar bg-white" style="width: ${deletedPercentage}%"></div>
                                            </div>
                                            <small class="opacity-75 d-block mt-1">${deletedPercentage}% of total</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 mb-4">
                                <div class="summary-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #2c3e50;">
                                    <div class="card-body position-relative">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h2 class="mb-1 fw-bold">${activeRecords.length}</h2>
                                                <p class="mb-0 opacity-75">Active</p>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-play-circle fa-2x opacity-75"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top border-dark border-opacity-25">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="opacity-75">Active records</small>
                                                <span class="badge bg-success bg-opacity-25 text-success">Live</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
        }

        // Filter by Date
        function filterByDate() {
            const searchDate = document.getElementById('searchDate').value;

            if (!searchDate) {
                showAlert('Please select a date to search', 'warning');
                return;
            }

            const filteredData = allAttendanceData.filter(record => record.date === searchDate);
            renderAttendanceTable(filteredData);

            if (filteredData.length === 0) {
                showAlert('No attendance records found for the selected date', 'info');
            }
        }

        // Clear Search
        function clearSearch() {
            document.getElementById('searchDate').value = '';
            document.getElementById('dateSearchForm').reset();
            renderAttendanceTable(allAttendanceData);
        }

        // Load Halls Dropdown
        function loadHallsDropdown() {
            return new Promise((resolve, reject) => {
                fetch(`/api/halls/dropdown`)
                    .then(response => response.json())
                    .then(data => {
                        const hallSelect = document.getElementById('edit_class_hall_id');

                        if (data.status === 'success') {
                            hallSelect.innerHTML = '<option value="">Select Hall</option>';

                            data.data.forEach(hall => {
                                const option = document.createElement('option');
                                option.value = hall.id;
                                option.textContent = `${hall.hall_name} (${hall.hall_id}) - ${hall.hall_type || 'No Type'} - Rs.${hall.hall_price || '0'}`;
                                hallSelect.appendChild(option);
                            });
                            resolve(data.data); // Resolve with halls data
                        } else {
                            hallSelect.innerHTML = '<option value="">Error loading halls</option>';
                            reject(new Error('API returned error status'));
                        }
                    })
                    .catch(error => {
                        console.error('Error loading halls:', error);
                        document.getElementById('edit_class_hall_id').innerHTML = '<option value="">Error loading halls</option>';
                        reject(error);
                    });
            });
        }

        // Helper Functions
        function showAttendanceLoading() {
            document.getElementById('attendanceLoading').classList.remove('d-none');
            document.getElementById('attendanceTable').classList.add('d-none');
        }

        function hideAttendanceLoading() {
            document.getElementById('attendanceLoading').classList.add('d-none');
            document.getElementById('attendanceTable').classList.remove('d-none');
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                                                            ${message}
                                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                        `;

            const container = document.querySelector('.card-body');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
                setTimeout(() => alertDiv.remove(), 5000);
            }
        }

        function updatePaginationControls(totalRecords) {
            const paginationDiv = document.getElementById('paginationContainer');
            const startRecord = ((currentPage - 1) * recordsPerPage) + 1;
            const endRecord = Math.min(currentPage * recordsPerPage, totalRecords);

            paginationDiv.innerHTML = `
                                                            <div class="row align-items-center">
                                                                <div class="col-md-6">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="text-muted me-2">Show:</span>
                                                                        <select class="form-select form-select-sm" style="width: auto;" onchange="changeRecordsPerPage(this.value)">
                                                                            <option value="10" ${recordsPerPage === 10 ? 'selected' : ''}>10</option>
                                                                            <option value="25" ${recordsPerPage === 25 ? 'selected' : ''}>25</option>
                                                                            <option value="50" ${recordsPerPage === 50 ? 'selected' : ''}>50</option>
                                                                            <option value="100" ${recordsPerPage === 100 ? 'selected' : ''}>100</option>
                                                                        </select>
                                                                        <span class="text-muted ms-2">records per page</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 text-end">
                                                                    <div class="d-flex align-items-center justify-content-end">
                                                                        <span class="text-muted me-3">
                                                                            Showing ${startRecord} to ${endRecord} of ${totalRecords} records
                                                                        </span>
                                                                        <nav>
                                                                            <ul class="pagination pagination-sm mb-0">
                                                                                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                                                                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">
                                                                                        <i class="fas fa-chevron-left"></i>
                                                                                    </a>
                                                                                </li>

                                                                                ${generatePageNumbers()}

                                                                                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                                                                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">
                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                        </nav>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        `;
        }

        function generatePageNumbers() {
            let pageNumbers = '';
            const maxPagesToShow = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                pageNumbers += `
                                                                <li class="page-item ${currentPage === i ? 'active' : ''}">
                                                                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                                                                </li>
                                                            `;
            }

            return pageNumbers;
        }

        function changePage(page) {
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderAttendanceTable(allAttendanceData);
        }

        function changeRecordsPerPage(newSize) {
            recordsPerPage = parseInt(newSize);
            currentPage = 1;
            renderAttendanceTable(allAttendanceData);
        }

        // PDF Generation Functions - DESIGN ONLY
        function generatePDF(filterType = 'all') {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Filter data based on type
            const filteredData = filterAttendanceData(filterType);
            const filterLabel = getFilterLabel(filterType);

            // PDF Header
            doc.setFontSize(20);
            doc.setTextColor(0, 0, 128);
            doc.text('ATTENDANCE DETAILED REPORT', 105, 20, { align: 'center' });

            doc.setFontSize(12);
            doc.setTextColor(100);
            doc.text(`Filter: ${filterLabel} | Generated on: ${new Date().toLocaleDateString()}`, 105, 28, { align: 'center' });

            // Class Information
            doc.setFontSize(14);
            doc.setTextColor(0, 0, 0);
            doc.text('Class Information:', 20, 45);

            doc.setFontSize(10);
            doc.text(`Class Category Student Class ID: ${classCategoryHasStudentClassId}`, 20, 55);
            doc.text(`Total Records: ${filteredData.length}`, 20, 62);
            doc.text(`Filter Applied: ${filterLabel}`, 20, 69);
            doc.text(`Report Date: ${new Date().toLocaleDateString()}`, 20, 76);

            // Save PDF
            const fileName = `attendance-${filterType}-${classCategoryHasStudentClassId}-${new Date().toISOString().split('T')[0]}.pdf`;
            doc.save(fileName);
        }

        function filterAttendanceData(filterType) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            switch (filterType) {
                case 'marked':
                    return state.allAttendanceData.filter(record =>
                        record.is_ongoing == 1 && record.status == 1
                    );
                case 'not_marked':
                    return state.allAttendanceData.filter(record =>
                        record.is_ongoing == 1 &&
                        record.status == 0 &&
                        new Date(record.date) < today
                    );
                case 'pending':
                    return state.allAttendanceData.filter(record =>
                        record.is_ongoing == 1 &&
                        record.status == 0 &&
                        new Date(record.date) > today
                    );
                case 'deleted':
                    return state.allAttendanceData.filter(record => record.is_ongoing == 0);
                default:
                    return state.allAttendanceData;
            }
        }

        // Get filter label
        function getFilterLabel(filterType) {
            switch (filterType) {
                case 'marked': return 'Marked Only';
                case 'not_marked': return 'Not Marked Only';
                case 'pending': return 'Pending Only';
                case 'deleted': return 'Deleted Only';
                default: return 'All Records';
            }
        }
    </script>
@endpush