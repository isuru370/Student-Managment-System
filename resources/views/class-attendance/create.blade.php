@extends('layouts.app')

@section('title', 'Attendance Manager')
@section('page-title', 'Attendance Manager')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"> <a href="{{ route('class-attendance.index', $id) }}">Class Attendance</a></li>
    <li class="breadcrumb-item active">Attendance Manager</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Attendance Manager
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Class Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="class-info p-3 bg-light rounded">
                                <h6 class="fw-bold">Class Details:</h6>
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
                        <div class="col-md-6 text-end">
                            <a href="{{ route('class_rooms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Classes
                            </a>
                        </div>
                    </div>

                    <!-- Bulk Attendance Form -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-calendar-plus me-2"></i>Create Bulk Attendance
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="bulkAttendanceForm">
                                @csrf
                                <input type="hidden" name="class_category_has_student_class_id"
                                    id="class_category_has_student_class_id" value="{{ $id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_month" class="form-label">Start Month <span
                                                    class="text-danger">*</span></label>
                                            <input type="month" class="form-control" id="start_month" name="start_month"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_month" class="form-label">End Month <span
                                                    class="text-danger">*</span></label>
                                            <input type="month" class="form-control" id="end_month" name="end_month"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_time" class="form-label">Start Time <span
                                                    class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="start_time" name="start_time"
                                                required>
                                            <small class="text-muted">Sri Lankan Time (12-hour: 9:00 AM, 2:30 PM)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_time" class="form-label">End Time <span
                                                    class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                                            <small class="text-muted">Sri Lankan Time (12-hour: 10:00 AM, 3:30 PM)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="day_of_week" class="form-label">Day of Week <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="day_of_week" name="day_of_week" required>
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
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="class_hall_id" class="form-label">Hall <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="class_hall_id" name="class_hall_id" required>
                                                <option value="">Loading halls...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success" id="submitBulkBtn">
                                        <i class="fas fa-save me-2"></i>Create Bulk Attendance
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Attendance Records Table -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">Attendance Records</h6>
                                <button class="btn btn-outline-light btn-sm" onclick="loadAttendanceData()" title="Refresh">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="attendanceTable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th width="80">#</th>
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

                            <!-- Pagination -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div id="paginationContainer" class="d-none">
                                        <!-- Pagination controls will be loaded here -->
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

                        <!-- Date Display -->
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <div class="form-control bg-light" id="edit_date_display" style="border: none;"></div>
                        </div>

                        <!-- Day Display -->
                        <div class="mb-3">
                            <label class="form-label">Day of Week</label>
                            <div class="form-control bg-light" id="edit_day_display" style="border: none;"></div>
                        </div>

                        <!-- Start Time -->
                        <div class="mb-3">
                            <label for="edit_start_time" class="form-label">Start Time <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_start_time" name="start_time" 
                                placeholder="e.g., 9:00 AM, 2:30 PM" required>
                            <small class="text-muted">Sri Lankan Time Format (12-hour with AM/PM)</small>
                        </div>

                        <!-- End Time -->
                        <div class="mb-3">
                            <label for="edit_end_time" class="form-label">End Time <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_end_time" name="end_time" 
                                placeholder="e.g., 10:00 AM, 3:30 PM" required>
                            <small class="text-muted">Sri Lankan Time Format (12-hour with AM/PM)</small>
                        </div>

                        <!-- Hall Selection -->
                        <div class="mb-3">
                            <label for="edit_class_hall_id" class="form-label">Hall <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="edit_class_hall_id" name="class_hall_id" required>
                                <option value="">Select Hall</option>
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
        .attendance-marked {
            background-color: #d4edda !important;
        }

        .attendance-not-marked {
            background-color: #f8d7da !important;
        }

        .attendance-pending {
            background-color: #fff3cd !important;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            border: none;
        }
        
        /* Sri Lankan Time Format Styling */
        .sl-time {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const classCategoryHasStudentClassId = {{ $id }};

        // Global variables for pagination
        let currentPage = 1;
        let recordsPerPage = 10;
        let totalPages = 1;
        let allAttendanceData = [];

        document.addEventListener('DOMContentLoaded', function () {
            loadClassDetails();
            loadHallsDropdown();
            loadEditHallsDropdown();
            loadAttendanceData();

            // Bulk form submission
            document.getElementById('bulkAttendanceForm').addEventListener('submit', function (e) {
                e.preventDefault();
                submitBulkAttendance();
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


        // Load Halls Dropdown
        function loadHallsDropdown() {
            fetch(`/api/halls/dropdown`)
                .then(response => response.json())
                .then(data => {
                    const hallSelect = document.getElementById('class_hall_id');

                    if (data.status === 'success') {
                        hallSelect.innerHTML = '<option value="">Select Hall</option>';

                        data.data.forEach(hall => {
                            const option = document.createElement('option');
                            option.value = hall.id;
                            option.textContent = `${hall.hall_name} (${hall.hall_id}) - ${hall.hall_type || 'No Type'} - Rs.${hall.hall_price || '0'}`;
                            hallSelect.appendChild(option);
                        });
                    } else {
                        hallSelect.innerHTML = '<option value="">Error loading halls</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading halls:', error);
                    document.getElementById('class_hall_id').innerHTML = '<option value="">Error loading halls</option>';
                });
        }

        // Load Halls for Edit Modal
        function loadEditHallsDropdown() {
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
                    }
                })
                .catch(error => {
                    console.error('Error loading edit halls:', error);
                });
        }

        // Submit Bulk Attendance
        function submitBulkAttendance() {
            const submitBtn = document.getElementById('submitBulkBtn');
            const originalText = submitBtn.innerHTML;

            const formData = new FormData(document.getElementById('bulkAttendanceForm'));
            
            // Convert time inputs to Sri Lankan format
            const startTimeSL = formatToSriLankanTime(formData.get('start_time'));
            const endTimeSL = formatToSriLankanTime(formData.get('end_time'));
            
            const data = {
                class_category_has_student_class_id: classCategoryHasStudentClassId,
                start_month: formData.get('start_month'),
                end_month: formData.get('end_month'),
                start_time: startTimeSL,
                end_time: endTimeSL,
                day_of_week: formData.get('day_of_week'),
                class_hall_id: formData.get('class_hall_id'),
                status: "0", // Always set to 0 (Not Marked)
                is_ongoing: true // Always set to true
            };

            // Validation
            if (!data.start_month || !data.end_month) {
                showAlert('Please select both start and end months', 'warning');
                return;
            }

            if (new Date(data.start_month) > new Date(data.end_month)) {
                showAlert('End month must be after start month', 'warning');
                return;
            }

            // Time validation
            if (!isValidSriLankanTime(startTimeSL)) {
                showAlert('Please enter start time in Sri Lankan format (e.g., 9:00 AM, 2:30 PM)', 'warning');
                return;
            }

            if (!isValidSriLankanTime(endTimeSL)) {
                showAlert('Please enter end time in Sri Lankan format (e.g., 10:00 AM, 3:30 PM)', 'warning');
                return;
            }

            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';

            fetch(`/api/class-attendances/bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        showAlert('Bulk attendance records created successfully! ' + result.data.created_count + ' records added.', 'success');
                        document.getElementById('bulkAttendanceForm').reset();
                        loadAttendanceData(); // Reload the attendance table
                    } else {
                        showAlert(result.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to create bulk attendance records', 'danger');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        // Load Attendance Data
        function loadAttendanceData() {
            showAttendanceLoading();

            fetch(`/api/class-attendances/${classCategoryHasStudentClassId}`)
                .then(response => response.json())
                .then(data => {
                    allAttendanceData = data.data || [];
                    renderAttendanceTable(allAttendanceData);
                    updateAttendanceSummary(allAttendanceData);
                    hideAttendanceLoading();

                    // Update total records count
                    const totalRecordsElement = document.getElementById('totalRecords');
                    if (totalRecordsElement) {
                        totalRecordsElement.textContent = allAttendanceData.length;
                    }
                })
                .catch(error => {
                    console.error('Error loading attendance data:', error);
                    showAlert('Error loading attendance records', 'danger');
                    hideAttendanceLoading();
                    document.getElementById('attendanceEmpty').classList.remove('d-none');
                });
        }

        // Render Attendance Table with Pagination
        function renderAttendanceTable(attendanceData) {
            const tbody = document.getElementById('attendanceTableBody');
            const emptyState = document.getElementById('attendanceEmpty');

            if (!tbody) return;

            tbody.innerHTML = '';

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

            // Render table rows
            paginatedData.forEach((record, index) => {
                const formattedDate = formatDateToSriLankan(record.date);
                const actualIndex = startIndex + index;
                const classattendanceId = record.id;
                const hallId = record.class_hall_id;

                const recordDate = new Date(record.date);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                const isPastDate = recordDate < today;
                const isFutureDate = recordDate > today;

                let statusText, statusClass, canEdit;

                if (record.status == 1) {
                    statusText = "Marked";
                    statusClass = "attendance-marked";
                    canEdit = false;
                } else if (record.status == 0 && isPastDate) {
                    statusText = "Not Marked";
                    statusClass = "attendance-not-marked";
                    canEdit = false;
                } else if (record.status == 0 && isFutureDate) {
                    statusText = "Pending";
                    statusClass = "attendance-pending";
                    canEdit = true;
                } else {
                    statusText = "Not Marked";
                    statusClass = "attendance-not-marked";
                    canEdit = false;
                }

                // Format times for display (Sri Lankan format)
                const startTimeDisplay = formatToSriLankanDisplay(record.start_time);
                const endTimeDisplay = formatToSriLankanDisplay(record.end_time);

                // Check if is_ongoing is 0 and show delete label
                const deleteLabel = record.is_ongoing == 0 ?
                    '<span class="badge bg-danger ms-1" title="Marked for deletion">Delete</span>' :
                    '';

                const row = `
                    <tr class="${statusClass}">
                        <td class="fw-bold text-muted">${actualIndex + 1}</td>
                        <td>${formattedDate}</td>
                        <td>${record.day_of_week || 'N/A'}</td>
                        <td class="sl-time">${startTimeDisplay}</td>
                        <td class="sl-time">${endTimeDisplay}</td>
                        <td>${record.hall ? record.hall.hall_name : 'N/A'}</td>
                        <td>
                            <span class="badge ${statusClass.includes('marked') ? 'bg-success' : statusClass.includes('pending') ? 'bg-warning' : 'bg-danger'}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="text-center">
                            ${canEdit ?
                        `<button class="btn btn-outline-warning btn-sm" title="Edit Attendance" 
                                    onclick="editAttendance(
                                        ${classattendanceId},
                                        ${classCategoryHasStudentClassId},
                                        '${record.date}',
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
                            ${deleteLabel}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            // Update pagination controls
            updatePaginationControls(attendanceData.length);
        }

        // Update Pagination Controls
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
                                                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>

                                                ${generatePageNumbers()}

                                                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">
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

        // Generate Page Numbers
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
                                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                                </li>
                            `;
            }

            return pageNumbers;
        }

        // Change Page
        function changePage(page) {
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderAttendanceTable(allAttendanceData);
        }

        // Change Records Per Page
        function changeRecordsPerPage(newSize) {
            recordsPerPage = parseInt(newSize);
            currentPage = 1;
            renderAttendanceTable(allAttendanceData);
        }

        // Edit Attendance
        function editAttendance(classattendanceId, classCategoryHasStudentClassId, date, dayOfWeek, startTime, endTime, status, classHallId) {
            document.getElementById('edit_attendance_id').value = classattendanceId;
            document.getElementById('edit_class_category_has_student_class_id').value = classCategoryHasStudentClassId;
            
            // Format date for display
            const formattedDate = formatDateToSriLankan(date);
            document.getElementById('edit_date_display').textContent = formattedDate;
            document.getElementById('edit_day_display').textContent = dayOfWeek;

            // Format times for Sri Lankan display
            document.getElementById('edit_start_time').value = formatToSriLankanDisplay(startTime);
            document.getElementById('edit_end_time').value = formatToSriLankanDisplay(endTime);
            document.getElementById('edit_class_hall_id').value = classHallId;

            const modal = new bootstrap.Modal(document.getElementById('editAttendanceModal'));
            modal.show();
        }

        // Update Attendance
        function updateAttendance() {
            const updateBtn = document.getElementById('updateAttendanceBtn');
            const originalText = updateBtn.innerHTML;

            const attendanceId = document.getElementById('edit_attendance_id').value;
            const classCategoryHasStudentClassId = document.getElementById('edit_class_category_has_student_class_id').value;
            const date = document.getElementById('edit_date_display').textContent;

            // Get Sri Lankan formatted times
            const startTimeInput = document.getElementById('edit_start_time').value.trim();
            const endTimeInput = document.getElementById('edit_end_time').value.trim();
            const dayOfWeek = document.getElementById('edit_day_display').textContent;

            // Convert to proper Sri Lankan time format
            const startTimeSL = formatToSriLankanTimeFromInput(startTimeInput);
            const endTimeSL = formatToSriLankanTimeFromInput(endTimeInput);

            // Validate Sri Lankan time format
            if (!isValidSriLankanTime(startTimeSL)) {
                showAlert('Please enter start time in Sri Lankan format (e.g., 9:00 AM, 2:30 PM)', 'warning');
                return;
            }

            if (!isValidSriLankanTime(endTimeSL)) {
                showAlert('Please enter end time in Sri Lankan format (e.g., 10:00 AM, 3:30 PM)', 'warning');
                return;
            }

            // Parse the Sri Lankan date back to YYYY-MM-DD format
            const originalDate = document.getElementById('edit_date_display').textContent;
            const dateParts = originalDate.split('-');
            const formattedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;

            const data = {
                class_category_has_student_class_id: classCategoryHasStudentClassId,
                date: formattedDate,
                start_time: startTimeSL,
                end_time: endTimeSL,
                day_of_week: dayOfWeek,
                class_hall_id: document.getElementById('edit_class_hall_id').value,
                status: "0", // Always set to 0 when updating
                is_ongoing: true, // Always set to true
                start: getYearMonthFromDate(formattedDate), // Auto-generate from date
                end: getYearMonthFromDate(formattedDate)    // Auto-generate from date
            };

            // Additional validation
            if (!data.start_time || !data.end_time || !data.class_hall_id) {
                showAlert('Please fill all required fields', 'warning');
                return;
            }

            // Time comparison validation
            const startTime24 = convertTo24Hour(startTimeSL);
            const endTime24 = convertTo24Hour(endTimeSL);
            
            if (startTime24 >= endTime24) {
                showAlert('End time must be after start time', 'warning');
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

        // SRI LANKAN TIME HELPER FUNCTIONS

        // Format time to Sri Lankan format (12-hour with AM/PM)
        function formatToSriLankanTime(timeString) {
            if (!timeString) return '';
            
            // If already in 12-hour format with AM/PM, return as is
            if (timeString.match(/\d{1,2}:\d{2}\s*(AM|PM)/i)) {
                return timeString.toUpperCase();
            }
            
            // Convert 24-hour format to 12-hour Sri Lankan format
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const minute = minutes || '00';
            
            if (hour >= 12) {
                const displayHour = hour > 12 ? hour - 12 : hour;
                return `${displayHour}:${minute} PM`;
            } else {
                const displayHour = hour === 0 ? 12 : hour;
                return `${displayHour}:${minute} AM`;
            }
        }

        // Format time input to Sri Lankan display format
        function formatToSriLankanDisplay(timeString) {
            if (!timeString) return 'N/A';
            
            // Convert any format to proper Sri Lankan format
            const formattedTime = formatToSriLankanTime(timeString);
            return formattedTime;
        }

        // Format time from user input to Sri Lankan time
        function formatToSriLankanTimeFromInput(input) {
            if (!input) return '';
            
            // Remove extra spaces and convert to uppercase
            input = input.trim().toUpperCase();
            
            // If already in correct format, return as is
            if (input.match(/^\d{1,2}:\d{2}\s*(AM|PM)$/)) {
                return input;
            }
            
            // Try to parse various formats
            const timeMatch = input.match(/(\d{1,2}):?(\d{2})?\s*(AM|PM|am|pm)?/i);
            if (timeMatch) {
                let hours = parseInt(timeMatch[1]);
                let minutes = timeMatch[2] || '00';
                let period = timeMatch[3] || '';
                
                // Handle 24-hour format
                if (!period) {
                    return formatToSriLankanTime(`${hours}:${minutes}`);
                }
                
                // Ensure proper formatting
                period = period.toUpperCase();
                if (hours > 12) {
                    hours = hours - 12;
                    period = 'PM';
                }
                
                return `${hours}:${minutes} ${period}`;
            }
            
            return input;
        }

        // Validate Sri Lankan time format
        function isValidSriLankanTime(timeString) {
            if (!timeString) return false;
            
            const timeRegex = /^(0?[1-9]|1[0-2]):[0-5][0-9]\s*(AM|PM)$/i;
            return timeRegex.test(timeString.trim());
        }

        // Format date to Sri Lankan format (DD-MM-YYYY)
        function formatDateToSriLankan(dateString) {
            if (!dateString) return 'N/A';
            
            const date = new Date(dateString);
            if (isNaN(date)) return dateString;
            
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            
            return `${day}-${month}-${year}`;
        }

        // Convert 12-hour Sri Lankan time to 24-hour format for comparison
        function convertTo24Hour(timeString) {
            if (!timeString) return '';
            
            const time = timeString.match(/(\d+):(\d+)\s*(AM|PM)/i);
            if (time) {
                let hours = parseInt(time[1]);
                const minutes = time[2];
                const period = time[3].toUpperCase();

                if (period === 'PM' && hours < 12) hours += 12;
                if (period === 'AM' && hours === 12) hours = 0;

                return `${hours.toString().padStart(2, '0')}:${minutes}`;
            }
            
            return timeString;
        }

        function getYearMonthFromDate(dateString) {
            const date = new Date(dateString);
            return date.toISOString().slice(0, 7); // Returns "2025-02"
        }

        function updateAttendanceSummary(attendanceData) {
            // You can add summary cards here if needed
        }

        function showAttendanceLoading() {
            document.getElementById('attendanceLoading').classList.remove('d-none');
            document.getElementById('attendanceTable').classList.add('d-none');
        }

        function hideAttendanceLoading() {
            document.getElementById('attendanceLoading').classList.add('d-none');
            document.getElementById('attendanceTable').classList.remove('d-none');
        }

        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
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
    </script>
@endpush