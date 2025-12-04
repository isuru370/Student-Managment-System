@extends('layouts.app')

@section('title', 'Add Student to Class')
@section('page-title', 'Add Student to Class')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Add Student</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Student Information Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary bg-gradient text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Student Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="studentInfo">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0">Loading student information...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Search Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-info bg-gradient text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-search me-2"></i>Search and Select Class
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Search Input -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Search Classes</label>
                                <div class="input-group">
                                    <input type="text" id="classSearch" class="form-control"
                                        placeholder="Search by class name, teacher name, teacher ID, or category...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Search by class name, teacher name, teacher custom ID, or category
                                    name</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">&nbsp;</label>
                                <div>
                                    <button class="btn btn-success w-100" onclick="searchClasses()">
                                        <i class="fas fa-search me-2"></i>Search Classes
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Search Results -->
                        <div id="searchResults" class="d-none">
                            <h6 class="mb-3 fw-semibold">Search Results:</h6>
                            <div class="row flex-nowrap overflow-auto pb-3" id="classesList" style="scrollbar-width: thin;">
                                <!-- Cards will appear here -->
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="searchLoading" class="text-center py-4 d-none">
                            <div class="spinner-border text-info" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Searching classes...</p>
                        </div>

                        <!-- Empty State -->
                        <div id="searchEmpty" class="text-center py-5 d-none">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-chalkboard fa-3x text-muted"></i>
                            </div>
                            <h4 class="text-muted">No Classes Found</h4>
                            <p class="text-muted">No classes match your search criteria.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selected Class Confirmation -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success bg-gradient text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-check-circle me-2"></i>Confirm Selection
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="confirmationSection" class="d-none">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">Please Confirm Student Enrollment</h6>
                                <p class="mb-0">Review the details below before adding the student to the class.</p>
                            </div>

                            <div class="row">
                                <!-- Student Details -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0 fw-semibold">Student Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="studentDetails">
                                                <!-- Student details will be populated here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Class Details -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0 fw-semibold">Class Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="classDetails">
                                                <!-- Class details will be populated here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Free Card Option -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0 fw-semibold">Enrollment Options</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="freeCardCheckbox"
                                                    style="transform: scale(1.2);">
                                                <label class="form-check-label fw-semibold" for="freeCardCheckbox">
                                                    <i class="fas fa-id-card me-2 text-warning"></i>Mark as Free Card
                                                </label>
                                                <small class="form-text text-muted d-block mt-1">
                                                    Enable this if the student should have free access to this class without
                                                    payment.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button class="btn btn-outline-secondary" onclick="clearSelection()">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </button>
                                        <button class="btn btn-success" id="addToClassBtn" onclick="addStudentToClass()">
                                            <i class="fas fa-user-plus me-2"></i>Add Student to Class
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Initial State -->
                        <div id="initialState" class="text-center py-5">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-mouse-pointer fa-3x text-muted"></i>
                            </div>
                            <h4 class="text-muted">Select a Class</h4>
                            <p class="text-muted">Search and select a class to add the student.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Enrollments Section - MOVED TO BOTTOM -->
        <div class="row mb-4" id="enrollmentsSection" style="display: none;">
            <div class="col-12">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-warning bg-gradient text-dark py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-list fa-lg me-3"></i>
                            <h5 class="card-title mb-0 fw-bold">Current Class Enrollments</h5>
                            <span class="badge bg-dark bg-opacity-25 ms-2 fs-6" id="enrollmentCount">0</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row flex-nowrap overflow-auto pb-3 g-3" id="enrollmentsList">
                            <!-- Enrollments will be loaded here -->
                        </div>
                        <div id="noEnrollments" class="text-center py-5 d-none">
                            <div class="mb-4">
                                <i class="fas fa-chalkboard-teacher fa-4x text-muted opacity-50"></i>
                            </div>
                            <h5 class="text-muted fw-semibold mb-2">No Current Enrollments</h5>
                            <p class="text-muted mb-0">This student is not enrolled in any classes yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .search-highlight {
            background-color: yellow;
            font-weight: bold;
            padding: 0 2px;
        }

        .class-card {
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 280px;
        }

        .class-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .class-card.selected {
            border: 2px solid #0d6efd;
            background-color: #f0f8ff;
        }

        .class-name-highlight {
            font-size: 1.1rem;
        }

        .enrollment-card {
            transition: all 0.3s ease;
            min-width: 320px;
        }

        .enrollment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .row.flex-nowrap {
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        .row.flex-nowrap::-webkit-scrollbar {
            height: 8px;
        }

        .row.flex-nowrap::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .row.flex-nowrap::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .row.flex-nowrap::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .fs-7 {
            font-size: 0.875rem !important;
        }

        .action-buttons .btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        .free-card-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 193, 7, 0.9);
            color: #212529;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 10;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Helper functions
        const api = function (endpoint) {
            return `/api/${endpoint}`;
        };

        const getCsrfToken = function () {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        };

        // Get student ID from URL
        const pathArray = window.location.pathname.split('/');
        const studentId = pathArray[pathArray.length - 1];

        let selectedClass = null;
        let currentStudent = null;

        document.addEventListener('DOMContentLoaded', function () {
            loadStudentInfo();
            loadStudentEnrollments();

            // Event listeners
            document.getElementById('classSearch').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    searchClasses();
                }
            });

            document.getElementById('clearSearch').addEventListener('click', function () {
                document.getElementById('classSearch').value = '';
            });
        });

        // Load Student Information
        async function loadStudentInfo() {
            try {
                const studentResponse = await fetch(api(`students/${studentId}`));
                if (studentResponse.ok) {
                    const studentData = await studentResponse.json();
                    currentStudent = studentData.data || studentData;
                    displayStudentInfo(currentStudent);
                } else {
                    await loadStudentInfoFromEnrollment();
                }
            } catch (error) {
                console.error('Error loading student info:', error);
                await loadStudentInfoFromEnrollment();
            }
        }

        // Fallback function to get student info from enrollment API
        async function loadStudentInfoFromEnrollment() {
            try {
                const response = await fetch(api(`student-classes/student/${studentId}`));
                if (response.ok) {
                    const data = await response.json();
                    if (Array.isArray(data) && data.length > 0 && data[0].student) {
                        currentStudent = data[0].student;
                        displayStudentInfo(currentStudent);
                    } else if (data.student) {
                        currentStudent = data.student;
                        displayStudentInfo(currentStudent);
                    } else {
                        displayStudentInfo(null);
                    }
                } else {
                    displayStudentInfo(null);
                }
            } catch (error) {
                console.error('Error loading student info from enrollment:', error);
                displayStudentInfo(null);
            }
        }

        // Load Student Enrollments
        // Load Student Enrollments - FIXED VERSION
        async function loadStudentEnrollments() {
            try {
                const response = await fetch(api(`student-classes/student/${studentId}`));

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Enrollments API response:', data); // Debug log

                let enrollments = [];

                // Handle different response structures
                if (Array.isArray(data)) {
                    enrollments = data;
                } else if (data.data && Array.isArray(data.data)) {
                    enrollments = data.data;
                } else if (data.data && !Array.isArray(data.data) && data.data.id) {
                    // Single enrollment object
                    enrollments = [data.data];
                } else if (data.id) {
                    // Direct enrollment object
                    enrollments = [data];
                } else if (data.student) {
                    // Response with student property
                    enrollments = [data];
                } else if (data.enrollments && Array.isArray(data.enrollments)) {
                    enrollments = data.enrollments;
                }

                console.log('Processed enrollments:', enrollments); // Debug log

                displayStudentEnrollments(enrollments);

            } catch (error) {
                console.error('Error loading enrollments:', error);
                showNoEnrollments();
                showAlert('Failed to load current enrollments', 'warning');
            }
        }

        // Display Student Enrollments - UPDATED with better error handling
        function displayStudentEnrollments(enrollments) {
            const enrollmentsSection = document.getElementById('enrollmentsSection');
            const enrollmentsList = document.getElementById('enrollmentsList');
            const noEnrollmentsDiv = document.getElementById('noEnrollments');
            const enrollmentCount = document.getElementById('enrollmentCount');

            // Clear existing content
            enrollmentsList.innerHTML = '';

            // Show section immediately
            enrollmentsSection.style.display = 'block';

            // Validate and filter enrollments
            const validEnrollments = [];

            enrollments.forEach(enrollment => {
                try {
                    // Check if this is a valid enrollment object
                    if (!enrollment || typeof enrollment !== 'object') {
                        return;
                    }

                    // Check for nested structure
                    let studentClass = enrollment.student_classes || enrollment.student_class;
                    let category = enrollment.class_category_has_student_class || enrollment.category;

                    // If we have a student_class_id but no student_class object, we can't display it properly
                    if (!studentClass && enrollment.student_class_id) {
                        console.warn('Enrollment has student_class_id but no student_class object:', enrollment);
                        return;
                    }

                    // Check if we have at least some data to display
                    if (studentClass || enrollment.id) {
                        validEnrollments.push({
                            id: enrollment.id || enrollment.enrollment_id,
                            status: enrollment.status,
                            is_free_card: enrollment.is_free_card,
                            created_at: enrollment.created_at || enrollment.enrollment_date,
                            student_classes: studentClass,
                            class_category_has_student_class: category
                        });
                    }
                } catch (e) {
                    console.error('Error processing enrollment:', enrollment, e);
                }
            });

            enrollmentCount.textContent = validEnrollments.length;

            if (validEnrollments.length === 0) {
                showNoEnrollments();
                return;
            }

            let enrollmentsHTML = '';

            validEnrollments.forEach(enrollment => {
                const studentClass = enrollment.student_classes;
                const category = enrollment.class_category_has_student_class;

                // Class information
                const className = studentClass ?
                    (studentClass.class_name || `Class ${studentClass.id || ''}`) :
                    'Unnamed Class';

                const gradeName = studentClass && studentClass.grade ?
                    `G${studentClass.grade.grade_name}` : 'N/A';

                // Teacher information
                let teacherName = 'N/A';
                if (studentClass && studentClass.teacher) {
                    teacherName = `${studentClass.teacher.fname || ''} ${studentClass.teacher.lname || ''}`.trim();
                } else if (studentClass && studentClass.teacher_name) {
                    teacherName = studentClass.teacher_name;
                }

                // Subject information
                const subjectName = studentClass && studentClass.subject ?
                    studentClass.subject.subject_name : 'N/A';

                // Category information
                const categoryName = category && category.class_category ?
                    category.class_category.category_name :
                    (category ? category.category_name : 'General');

                // Fees
                const fees = category ? (category.fees || 0) : 0;

                // Status and badges
                const isActive = enrollment.status === 1 || enrollment.status === 'active';
                const statusBadge = isActive ?
                    '<span class="badge bg-success bg-gradient rounded-pill px-3 py-2">Active</span>' :
                    '<span class="badge bg-secondary bg-gradient rounded-pill px-3 py-2">Inactive</span>';

                // Free card status
                const freeCardStatus = enrollment.is_free_card === 1 || enrollment.is_free_card === true;
                const freeCardBadge = freeCardStatus ?
                    '<span class="badge bg-warning bg-gradient text-dark rounded-pill px-2 py-1"><i class="fas fa-id-card me-1"></i>Free Card</span>' :
                    '<span class="badge bg-light bg-gradient text-dark border rounded-pill px-2 py-1"><i class="fas fa-money-bill me-1"></i>Paid</span>';

                // Format enrollment date
                const enrollmentDate = enrollment.created_at ?
                    formatDate(enrollment.created_at) : 'N/A';

                enrollmentsHTML += `
                    <div class="col-auto">
                        <div class="card border-0 shadow-sm h-100 enrollment-card position-relative">
                            ${freeCardStatus ? '<div class="free-card-indicator"><i class="fas fa-crown me-1"></i>FREE</div>' : ''}
                            <div class="card-header bg-primary bg-gradient text-white py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="card-title mb-0 fw-bold text-truncate" style="max-width: 200px;">${className}</h6>
                                    <span class="badge bg-light text-dark fs-7">${gradeName}</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <!-- Status and Fee Row -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        ${statusBadge}
                                        ${freeCardBadge}
                                    </div>
                                    <span class="badge bg-info bg-gradient fs-6">Rs. ${fees}</span>
                                </div>

                                <!-- Info Items -->
                                <div class="mb-2">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-tag text-muted me-2 fs-7"></i>
                                        <small class="text-muted"><strong>Category:</strong> ${categoryName}</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-graduate text-muted me-2 fs-7"></i>
                                        <small class="text-muted"><strong>Teacher:</strong> ${teacherName}</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-book text-muted me-2 fs-7"></i>
                                        <small class="text-muted"><strong>Subject:</strong> ${subjectName}</small>
                                    </div>
                                </div>

                                <!-- Enrollment Details -->
                                <div class="border-top pt-2 mt-2">
                                    <div class="row g-1 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar me-1"></i>
                                                <strong>Enrolled:</strong>
                                            </small>
                                            <small class="text-dark fw-semibold">${enrollmentDate}</small>
                                        </div>
                                        <div class="col-6 text-end">
                                            <small class="text-muted d-block">
                                                <strong>Enrollment ID:</strong>
                                            </small>
                                            <small class="text-dark fw-semibold">#${enrollment.id || 'N/A'}</small>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="action-buttons">
                                        ${isActive ?
                        `<button class="btn btn-outline-danger btn-sm w-100" onclick="deactivateEnrollment(${enrollment.id})">
                                                <i class="fas fa-pause me-1"></i>Deactivate
                                            </button>` :
                        `<button class="btn btn-outline-success btn-sm w-100" onclick="activateEnrollment(${enrollment.id})">
                                                <i class="fas fa-play me-1"></i>Activate
                                            </button>`
                    }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            enrollmentsList.innerHTML = enrollmentsHTML;
            noEnrollmentsDiv.classList.add('d-none');
        }

        // Deactivate Enrollment - UPDATED to single endpoint
        async function deactivateEnrollment(enrollmentId) {
            if (!confirm('Are you sure you want to deactivate this enrollment?')) {
                return;
            }

            try {
                const response = await fetch(api(`student-classes/${enrollmentId}/deactivate`), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    showAlert('Enrollment deactivated successfully!', 'success');
                    loadStudentEnrollments(); // Reload to show updated status
                } else {
                    throw new Error(result.message || 'Failed to deactivate enrollment');
                }
            } catch (error) {
                console.error('Error deactivating enrollment:', error);
                showAlert('Failed to deactivate enrollment: ' + error.message, 'danger');
            }
        }

        // Activate Enrollment - UPDATED to single endpoint
        async function activateEnrollment(enrollmentId) {
            if (!confirm('Are you sure you want to activate this enrollment?')) {
                return;
            }

            try {
                const response = await fetch(api(`student-classes/${enrollmentId}/activate`), {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    showAlert('Enrollment activated successfully!', 'success');
                    loadStudentEnrollments(); // Reload to show updated status
                } else {
                    throw new Error(result.message || 'Failed to activate enrollment');
                }
            } catch (error) {
                console.error('Error activating enrollment:', error);
                showAlert('Failed to activate enrollment: ' + error.message, 'danger');
            }
        }

        // Show no enrollments state
        function showNoEnrollments() {
            const enrollmentsSection = document.getElementById('enrollmentsSection');
            const enrollmentsList = document.getElementById('enrollmentsList');
            const noEnrollmentsDiv = document.getElementById('noEnrollments');

            enrollmentsList.innerHTML = '';
            enrollmentsSection.style.display = 'block';
            noEnrollmentsDiv.classList.remove('d-none');
        }

        // Format date for display
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Display Student Information
        function displayStudentInfo(student) {
            const studentInfoDiv = document.getElementById('studentInfo');

            if (student) {
                studentInfoDiv.innerHTML = `
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Student ID:</strong><br>
                                    <span class="badge bg-secondary fs-6">${student.custom_id}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Name:</strong><br>
                                    <span class="fs-6 fw-bold">${student.fname} ${student.lname}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Grade:</strong><br>
                                    <span class="badge bg-info">${student.grade ? 'Grade ' + student.grade.grade_name : 'N/A'}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Mobile:</strong><br>
                                    <span>${student.mobile || 'N/A'}</span>
                                </div>
                            </div>
                        `;
            } else {
                studentInfoDiv.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Student information not available. Student ID: ${studentId}
                            </div>
                        `;
            }
        }

        // Search Classes
        async function searchClasses() {
            const searchTerm = document.getElementById('classSearch').value.trim();

            if (!searchTerm) {
                showAlert('Please enter a search term', 'warning');
                return;
            }

            showSearchLoading();

            try {
                const response = await fetch(api(`class-has-category-classes/classes/search?q=${encodeURIComponent(searchTerm)}`));

                if (!response.ok) {
                    throw new Error('Failed to search classes');
                }

                const data = await response.json();
                displaySearchResults(data.data || [], searchTerm);
                hideSearchLoading();

            } catch (error) {
                console.error('Error searching classes:', error);
                hideSearchLoading();
                showSearchEmptyState();
                showAlert('Failed to search classes: ' + error.message, 'danger');
            }
        }

        // Display Search Results
        function displaySearchResults(classes, searchTerm) {
            const resultsDiv = document.getElementById('searchResults');
            const classesListDiv = document.getElementById('classesList');
            const emptyState = document.getElementById('searchEmpty');

            classesListDiv.innerHTML = '';

            if (classes.length === 0) {
                resultsDiv.classList.add('d-none');
                emptyState.classList.remove('d-none');
                return;
            }

            emptyState.classList.add('d-none');
            resultsDiv.classList.remove('d-none');

            classes.forEach(classItem => {
                const studentClass = classItem.student_class;
                const classCategory = classItem.class_category;

                if (!studentClass) return;

                const className = studentClass.class_name;
                const gradeName = studentClass.grade ? `G${studentClass.grade.grade_name}` : 'N/A';
                const fullClassName = `${className} ${gradeName}`;

                const classCard = `
                            <div class="col-auto">
                                <div class="card class-card h-100" onclick="selectClass(${JSON.stringify(classItem).replace(/"/g, '&quot;')})">
                                    <div class="card-body">
                                        <h6 class="card-title">${highlightSearchTerm(fullClassName, searchTerm)}</h6>
                                        <div class="mb-2">
                                            <small class="text-muted">Teacher:</small><br>
                                            <strong>${studentClass.teacher ? highlightSearchTerm(studentClass.teacher.fname + ' ' + studentClass.teacher.lname, searchTerm) : 'N/A'}</strong>
                                            <br>
                                            <small class="text-muted">${studentClass.teacher ? studentClass.teacher.custom_id : ''}</small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Subject:</small><br>
                                            <span class="badge bg-light text-dark">${studentClass.subject ? studentClass.subject.subject_name : 'N/A'}</span>
                                            <span class="badge bg-info">Grade ${studentClass.grade ? studentClass.grade.grade_name : 'N/A'}</span>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Category & Fee:</small><br>
                                            <span class="badge bg-success">${classCategory ? highlightSearchTerm(classCategory.category_name, searchTerm) : 'N/A'}</span>
                                            <span class="badge bg-primary ms-1">Rs. ${classItem.fees || 0}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                classesListDiv.innerHTML += classCard;
            });
        }

        // Highlight search term in text
        function highlightSearchTerm(text, searchTerm) {
            if (!text || !searchTerm) return text;
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            return text.replace(regex, '<span class="search-highlight">$1</span>');
        }

        // Select Class
        function selectClass(classData) {
            selectedClass = classData;

            // Update UI
            document.querySelectorAll('.class-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');

            // Reset free card checkbox
            document.getElementById('freeCardCheckbox').checked = false;

            // Show confirmation section
            showConfirmationSection(classData);
        }

        // Show Confirmation Section
        function showConfirmationSection(classData) {
            const studentClass = classData.student_class;
            const classCategory = classData.class_category;

            const className = studentClass.class_name;
            const gradeName = studentClass.grade ? `G${studentClass.grade.grade_name}` : 'N/A';
            const fullClassName = `${className} ${gradeName}`;

            // Populate student details
            document.getElementById('studentDetails').innerHTML = `
                        <div class="mb-2">
                            <strong>Student ID:</strong> ${currentStudent ? currentStudent.custom_id : studentId}
                        </div>
                        <div class="mb-2">
                            <strong>Name:</strong> ${currentStudent ? currentStudent.fname + ' ' + currentStudent.lname : 'N/A'}
                        </div>
                        <div class="mb-2">
                            <strong>Grade:</strong> <span class="badge bg-info">${currentStudent && currentStudent.grade ? 'Grade ' + currentStudent.grade.grade_name : 'N/A'}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Mobile:</strong> ${currentStudent ? currentStudent.mobile : 'N/A'}
                        </div>
                    `;

            // Populate class details
            document.getElementById('classDetails').innerHTML = `
                        <div class="mb-3">
                            <strong>Class Name:</strong> 
                            <div class="class-name-highlight mt-1 p-2 bg-primary text-white rounded text-center fw-bold">
                                ${fullClassName}
                            </div>
                        </div>
                        <div class="mb-2">
                            <strong>Teacher:</strong> ${studentClass.teacher ? studentClass.teacher.fname + ' ' + studentClass.teacher.lname : 'N/A'}
                            <br><small class="text-muted">${studentClass.teacher ? studentClass.teacher.custom_id : ''}</small>
                        </div>
                        <div class="mb-2">
                            <strong>Subject:</strong> <span class="badge bg-light text-dark">${studentClass.subject ? studentClass.subject.subject_name : 'N/A'}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Grade:</strong> <span class="badge bg-info">${studentClass.grade ? 'Grade ' + studentClass.grade.grade_name : 'N/A'}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Category:</strong> <span class="badge bg-success">${classCategory ? classCategory.category_name : 'N/A'}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Fee:</strong> <span class="badge bg-primary">Rs. ${classData.fees || 0}</span>
                        </div>
                    `;

            // Show confirmation section
            document.getElementById('confirmationSection').classList.remove('d-none');
            document.getElementById('initialState').classList.add('d-none');
        }

        // Add Student to Class - UPDATED with free card option
        async function addStudentToClass() {
            if (!selectedClass) {
                showAlert('Please select a class first', 'warning');
                return;
            }

            const addBtn = document.getElementById('addToClassBtn');
            const originalText = addBtn.innerHTML;

            try {
                addBtn.disabled = true;
                addBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding Student...';

                const isFreeCard = document.getElementById('freeCardCheckbox').checked;

                const requestData = {
                    student_id: parseInt(studentId),
                    student_classes_id: selectedClass.student_classes_id,
                    class_category_has_student_class_id: selectedClass.id,
                    is_free_card: isFreeCard ? 1 : 0
                };

                console.log('Adding student to class:', requestData);

                const response = await fetch(api('student-classes/single'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert('Student added to class successfully!', 'success');

                    // Reload enrollments to show the new one
                    await loadStudentEnrollments();

                    // Clear selection
                    clearSelection();

                } else {
                    throw new Error(result.message || 'Failed to add student to class');
                }

            } catch (error) {
                console.error('Error adding student to class:', error);
                showAlert('Failed to add student: ' + error.message, 'danger');
            } finally {
                addBtn.disabled = false;
                addBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Add Student to Class';
            }
        }

        // Utility Functions
        function clearSelection() {
            selectedClass = null;
            document.querySelectorAll('.class-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.getElementById('confirmationSection').classList.add('d-none');
            document.getElementById('initialState').classList.remove('d-none');
            document.getElementById('freeCardCheckbox').checked = false;
        }

        function showSearchLoading() {
            document.getElementById('searchLoading').classList.remove('d-none');
            document.getElementById('searchResults').classList.add('d-none');
            document.getElementById('searchEmpty').classList.add('d-none');
        }

        function hideSearchLoading() {
            document.getElementById('searchLoading').classList.add('d-none');
        }

        function showSearchEmptyState() {
            document.getElementById('searchEmpty').classList.remove('d-none');
            document.getElementById('searchResults').classList.add('d-none');
        }

        function showAlert(message, type) {
            // Remove any existing alerts
            const existingAlerts = document.querySelectorAll('.alert.position-fixed');
            existingAlerts.forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                        <strong>${type === 'success' ? 'Success!' : type === 'warning' ? 'Warning!' : 'Error!'}</strong> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
@endpush