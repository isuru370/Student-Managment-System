@extends('layouts.app')

@section('title', 'View Class Students')
@section('page-title', 'View Class Students')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active">View Students</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Class Information Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Class Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="classInfo">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0">Loading class information...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Categories Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tags me-2"></i>Available Categories
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="classCategories">
                            <div class="text-center py-3">
                                <div class="spinner-border text-info" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 mb-0">Loading class categories...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>Enrolled Students
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Selected Category Info -->
                        <div id="selectedCategoryInfo" class="alert alert-warning mb-4" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Selected Category:</strong>
                                    <span id="selectedCategoryName" class="fw-bold"></span> -
                                    Fee: Rs. <span id="selectedCategoryFee" class="fw-bold"></span>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" onclick="clearSelection()">
                                    <i class="fas fa-times me-1"></i>Clear Selection
                                </button>
                            </div>
                        </div>

                        <!-- Bulk Actions Bar -->
                        <div id="bulkActions" class="alert alert-info mb-3" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Bulk Actions:</strong>
                                    <span id="selectedStudentsCount" class="badge bg-primary ms-2">0 students
                                        selected</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-warning" id="bulkDeactivateBtn"
                                        onclick="bulkDeactivateStudents()" disabled>
                                        <i class="fas fa-user-minus me-1"></i>Deactivate Selected
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="clearAllSelections()">
                                        <i class="fas fa-times me-1"></i>Clear All
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Search and Filters -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Search Students</label>
                                <div class="input-group">
                                    <input type="text" id="studentSearch" class="form-control" placeholder="Name or ID...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filter by Grade</label>
                                <select class="form-select" id="gradeFilter">
                                    <option value="">All Grades</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filter by Status</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Records Per Page</label>
                                <select class="form-select" id="recordsPerPage">
                                    <option value="10">10 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>
                        </div>

                        <!-- Students Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-success">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllStudents" class="form-check-input">
                                        </th>
                                        <th width="60">#</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Grade</th>
                                        <th>Guardian Mobile</th>
                                        <th>Enrollment Status</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <!-- Students will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Loading State -->
                        <div id="studentsLoading" class="text-center py-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading students...</p>
                        </div>

                        <!-- Empty State -->
                        <div id="studentsEmpty" class="text-center py-5 d-none">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-users fa-3x text-muted"></i>
                            </div>
                            <h4 class="text-muted">No Students Found</h4>
                            <p class="text-muted">No enrolled students found for this category.</p>
                        </div>

                        <!-- Pagination -->
                        <div id="studentsPagination" class="d-none">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="text-muted" id="paginationInfo">
                                        Showing 0 to 0 of 0 entries
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <nav aria-label="Students pagination">
                                        <ul class="pagination justify-content-end mb-0" id="paginationControls">
                                            <!-- Pagination controls will be inserted here -->
                                        </ul>
                                    </nav>
                                </div>
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
        .category-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .category-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .category-card.selected {
            border-color: #198754;
            background-color: #f8fff9;
        }

        .student-row {
            transition: all 0.2s ease;
        }

        .student-row.selected {
            background-color: #e8f5e8;
        }

        .table th {
            background: linear-gradient(135deg, #198754, #157347);
            color: white;
            font-weight: 600;
        }

        .badge-fee {
            font-size: 0.9rem;
            padding: 0.5rem 0.8rem;
        }

        .page-link {
            color: #198754;
            border-color: #dee2e6;
        }

        .page-item.active .page-link {
            background-color: #198754;
            border-color: #198754;
        }

        .page-link:hover {
            color: #146c43;
            background-color: #e9ecef;
            border-color: #dee2e6;
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

        const classId = {{ $id }};
        let selectedCategoryId = null;
        let selectedCategoryName = '';
        let selectedCategoryFee = 0;
        let selectedStudents = new Set();
        let allEnrolledStudents = [];
        let filteredStudents = [];

        // Pagination variables
        let studentsCurrentPage = 1;
        let studentsRecordsPerPage = 10;

        document.addEventListener('DOMContentLoaded', function () {
            loadClassInfo();
            loadGradesDropdown();

            // Event listeners
            document.getElementById('studentSearch').addEventListener('input', filterStudents);
            document.getElementById('gradeFilter').addEventListener('change', filterStudents);
            document.getElementById('statusFilter').addEventListener('change', filterStudents);
            document.getElementById('selectAllStudents').addEventListener('change', toggleSelectAll);
            document.getElementById('clearSearch').addEventListener('click', clearSearch);
            document.getElementById('recordsPerPage').addEventListener('change', function () {
                studentsRecordsPerPage = parseInt(this.value);
                studentsCurrentPage = 1;
                renderStudentsTable();
            });
        });

        // Load Class Information
        async function loadClassInfo() {
            try {
                const response = await fetch(api(`class-has-category-classes/class-category-class/${classId}`));
                if (!response.ok) throw new Error('Failed to load class information');

                const data = await response.json();
                const classData = data.data && data.data[0] ? data.data[0].student_class : null;

                if (classData) {
                    document.getElementById('classInfo').innerHTML = `
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <strong>Class Name:</strong><br>
                                                                <span class="fs-5 fw-bold text-primary">${classData.class_name}</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <strong>Teacher:</strong><br>
                                                                <span class="fs-6">${classData.teacher ? classData.teacher.fname + ' ' + classData.teacher.lname : 'N/A'}</span>
                                                                <br><small class="text-muted">${classData.teacher ? classData.teacher.custom_id : ''}</small>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <strong>Subject:</strong><br>
                                                                <span class="badge bg-light text-dark border">${classData.subject ? classData.subject.subject_name : 'N/A'}</span>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <strong>Grade:</strong><br>
                                                                <span class="badge bg-primary">${classData.grade ? 'Grade ' + classData.grade.grade_name : 'N/A'}</span>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <strong>Status:</strong><br>
                                                                <span class="badge ${classData.is_active ? 'bg-success' : 'bg-secondary'}">
                                                                    ${classData.is_active ? 'Active' : 'Inactive'}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    `;
                }

                // Load categories
                renderClassCategories(data.data || []);
            } catch (error) {
                console.error('Error loading class info:', error);
                document.getElementById('classInfo').innerHTML = `
                                                    <div class="alert alert-danger">
                                                        Failed to load class information: ${error.message}
                                                    </div>
                                                `;
            }
        }

        // Render Class Categories
        function renderClassCategories(categories) {
            const container = document.getElementById('classCategories');

            if (!categories || categories.length === 0) {
                container.innerHTML = `
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No categories available for this class.
                                    </div>
                                `;
                return;
            }

            container.innerHTML = categories.map((category, index) => {
                const categoryName = category.class_category ? category.class_category.category_name : 'Unknown Category';
                const fee = category.fees || 0;

                return `
                                    <div class="col-md-4 mb-3">
                                        <div class="card category-card" onclick="selectCategory(${category.id}, '${categoryName}', ${fee})">
                                            <div class="card-body">
                                                <h6 class="card-title">${categoryName}</h6>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-success badge-fee">
                                                        <i class="fas fa-rupee-sign me-1"></i>${fee.toFixed(2)}
                                                    </span>
                                                    <small class="text-muted">Click to view students</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
            }).join('');

            // Wrap in row
            container.innerHTML = `<div class="row">${container.innerHTML}</div>`;

            // NEW: Automatically select first category
            if (categories.length > 0) {
                const firstCategory = categories[0];
                const categoryName = firstCategory.class_category ? firstCategory.class_category.category_name : 'Unknown';
                const fee = firstCategory.fees || 0;

                // Auto-select first category with isAutoSelect flag
                setTimeout(() => {
                    selectCategory(firstCategory.id, categoryName, fee, true);
                }, 500);
            }
        }

        // Select Category
        async function selectCategory(categoryId, categoryName, fee, isAutoSelect = false) {
            selectedCategoryId = categoryId;
            selectedCategoryName = categoryName;
            selectedCategoryFee = fee;

            // Update UI - Only if not auto-selecting
            if (!isAutoSelect) {
                document.querySelectorAll('.category-card').forEach(card => {
                    card.classList.remove('selected');
                });
                event.currentTarget.classList.add('selected');
            } else {
                // For auto-select, find and select the first category card
                document.querySelectorAll('.category-card').forEach(card => {
                    card.classList.remove('selected');
                });
                // Select the first category card
                const firstCategoryCard = document.querySelector('.category-card');
                if (firstCategoryCard) {
                    firstCategoryCard.classList.add('selected');
                }
            }

            // Show selected category info
            document.getElementById('selectedCategoryInfo').style.display = 'block';
            document.getElementById('selectedCategoryName').textContent = categoryName;
            document.getElementById('selectedCategoryFee').textContent = fee.toFixed(2);

            // Load enrolled students for this category
            await loadEnrolledStudents(categoryId);
        }

        // Load Enrolled Students
        async function loadEnrolledStudents(categoryId) {
            showStudentsLoading();

            try {
                console.log('Loading students for class:', classId, 'category:', categoryId);

                const response = await fetch(`/api/student-classes/all/${classId}/category/${categoryId}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Raw API Response:', data);

                // FIX: Handle different response structures
                if (data.status === "empty") {
                    allEnrolledStudents = [];
                } else if (Array.isArray(data)) {
                    allEnrolledStudents = data;
                } else if (data.data && Array.isArray(data.data)) {
                    allEnrolledStudents = data.data;
                } else {
                    allEnrolledStudents = [];
                }

                // FIX: Check student data structure
                allEnrolledStudents.forEach((enrollment, index) => {
                    console.log(`Enrollment ${index}:`, enrollment);
                    if (!enrollment.student) {
                        console.warn(`Enrollment ${enrollment.id} has no student data!`);
                    }
                });

                console.log('Processed students count:', allEnrolledStudents.length);

                filteredStudents = [...allEnrolledStudents];
                studentsCurrentPage = 1;

                renderStudentsTable();
                hideStudentsLoading();

                if (allEnrolledStudents.length > 0) {
                    document.getElementById('bulkActions').style.display = 'block';
                }

            } catch (error) {
                console.error('Error loading enrolled students:', error);
                hideStudentsLoading();
                showStudentsEmptyState();
                showAlert('Failed to load students: ' + error.message, 'danger');
            }
        }

        // Load Grades Dropdown
        async function loadGradesDropdown() {
            try {
                const response = await fetch(api('grades/dropdown'));
                if (!response.ok) throw new Error('Failed to load grades');

                const data = await response.json();
                const grades = data.data || data;
                const gradeSelect = document.getElementById('gradeFilter');

                grades.forEach(grade => {
                    const option = document.createElement('option');
                    option.value = grade.id;
                    option.textContent = `Grade ${grade.grade_name}`;
                    gradeSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading grades:', error);
            }
        }

        // Render Students Table with Pagination
        function renderStudentsTable() {
            const tbody = document.getElementById('studentsTableBody');
            const emptyState = document.getElementById('studentsEmpty');
            const paginationContainer = document.getElementById('studentsPagination');

            tbody.innerHTML = '';

            if (filteredStudents.length === 0) {
                emptyState.classList.remove('d-none');
                paginationContainer.classList.add('d-none');
                document.getElementById('bulkActions').style.display = 'none';
                return;
            }

            emptyState.classList.add('d-none');
            paginationContainer.classList.remove('d-none');

            // Calculate pagination
            const totalPages = Math.ceil(filteredStudents.length / studentsRecordsPerPage);
            const startIndex = (studentsCurrentPage - 1) * studentsRecordsPerPage;
            const endIndex = Math.min(startIndex + studentsRecordsPerPage, filteredStudents.length);
            const paginatedStudents = filteredStudents.slice(startIndex, endIndex);

            // Render table rows
            paginatedStudents.forEach((enrollment, index) => {
                const actualIndex = startIndex + index;
                const student = enrollment.student;
                const isSelected = selectedStudents.has(enrollment.id);
                const isActive = enrollment.status === 1;

                // FIX: Get grade name properly
                let gradeDisplay = 'N/A';
                if (student.grade && student.grade.grade_name) {
                    gradeDisplay = `Grade ${student.grade.grade_name}`;
                } else if (student.grade_id) {
                    gradeDisplay = `Grade ID: ${student.grade_id}`;
                }

                console.log(`Student ${student.custom_id}: grade_id=${student.grade_id}, grade_object=`, student.grade);

                const row = `
                <tr class="student-row ${isSelected ? 'selected' : ''}">
                    <td>
                        <input type="checkbox" class="form-check-input student-checkbox" 
                               value="${enrollment.id}" ${isSelected ? 'checked' : ''}
                               onchange="toggleStudentSelection(${enrollment.id}, this.checked)">
                    </td>
                    <td class="fw-bold text-muted">${actualIndex + 1}</td>
                    <td>
                        <span class="badge bg-secondary">${student.custom_id}</span>
                    </td>
                    <td>
                        <strong>${student.fname} ${student.lname}</strong>
                    </td>
                    <td>
                        <span class="badge bg-info">${gradeDisplay}</span>
                    </td>
                    <td>${student.guardian_mobile || student.mobile || 'N/A'}</td>
                    <td>
                        <span class="badge ${isActive ? 'bg-success' : 'bg-warning text-dark'}">
                            ${isActive ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            ${isActive ?
                        `<button class="btn btn-warning" onclick="deactivateStudent(${enrollment.id})" title="Deactivate">
                                    <i class="fas fa-user-minus"></i>
                                </button>` :
                        `<button class="btn btn-success" onclick="activateStudent(${enrollment.id})" title="Activate">
                                    <i class="fas fa-user-check"></i>
                                </button>`
                    }
                        </div>
                    </td>
                </tr>
            `;
                tbody.innerHTML += row;
            });

            // Update pagination info
            updatePaginationInfo(startIndex, endIndex, filteredStudents.length);

            // Update pagination controls
            updatePaginationControls(totalPages);

            updateSelectedCount();
            updateSelectAllCheckbox();
            updateBulkActionButtons();
        }

        // Update Select All Checkbox State
        function updateSelectAllCheckbox() {
            const selectAllCheckbox = document.getElementById('selectAllStudents');
            const allCheckboxes = document.querySelectorAll('.student-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.student-checkbox:checked');

            selectAllCheckbox.checked = checkedCheckboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
            selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        }

        // Update Bulk Action Buttons
        function updateBulkActionButtons() {
            const bulkDeactivateBtn = document.getElementById('bulkDeactivateBtn');
            bulkDeactivateBtn.disabled = selectedStudents.size === 0;
        }

        // Update Pagination Information
        function updatePaginationInfo(startIndex, endIndex, total) {
            const infoElement = document.getElementById('paginationInfo');
            if (total === 0) {
                infoElement.textContent = 'Showing 0 to 0 of 0 entries';
            } else {
                infoElement.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${total} entries`;
            }
        }

        // Update Pagination Controls
        function updatePaginationControls(totalPages) {
            const paginationContainer = document.getElementById('paginationControls');
            paginationContainer.innerHTML = '';

            // Previous button
            const prevButton = `
                                                <li class="page-item ${studentsCurrentPage === 1 ? 'disabled' : ''}">
                                                    <a class="page-link" href="#" onclick="changePage(${studentsCurrentPage - 1})" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                            `;
            paginationContainer.innerHTML += prevButton;

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, studentsCurrentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageItem = `
                                                    <li class="page-item ${i === studentsCurrentPage ? 'active' : ''}">
                                                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                                                    </li>
                                                `;
                paginationContainer.innerHTML += pageItem;
            }

            // Next button
            const nextButton = `
                                                <li class="page-item ${studentsCurrentPage === totalPages ? 'disabled' : ''}">
                                                    <a class="page-link" href="#" onclick="changePage(${studentsCurrentPage + 1})" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            `;
            paginationContainer.innerHTML += nextButton;
        }

        // Change Page
        function changePage(page) {
            if (page < 1 || page > Math.ceil(filteredStudents.length / studentsRecordsPerPage)) {
                return;
            }
            studentsCurrentPage = page;
            renderStudentsTable();
        }

        // Student Selection Functions
        function toggleStudentSelection(enrollmentId, isSelected) {
            if (isSelected) {
                selectedStudents.add(enrollmentId);
            } else {
                selectedStudents.delete(enrollmentId);
            }

            // Update row appearance
            const row = event.target.closest('tr');
            if (row) {
                row.classList.toggle('selected', isSelected);
            }

            updateSelectedCount();
            updateSelectAllCheckbox();
            updateBulkActionButtons();
        }

        function toggleSelectAll() {
            const isChecked = event.target.checked;
            const checkboxes = document.querySelectorAll('.student-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
                const enrollmentId = parseInt(checkbox.value);

                if (isChecked) {
                    selectedStudents.add(enrollmentId);
                } else {
                    selectedStudents.delete(enrollmentId);
                }
            });

            // Update all rows
            document.querySelectorAll('.student-row').forEach(row => {
                row.classList.toggle('selected', isChecked);
            });

            updateSelectedCount();
            updateBulkActionButtons();
        }

        function clearAllSelections() {
            selectedStudents.clear();
            document.querySelectorAll('.student-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.querySelectorAll('.student-row').forEach(row => {
                row.classList.remove('selected');
            });
            document.getElementById('selectAllStudents').checked = false;

            updateSelectedCount();
            updateBulkActionButtons();
        }

        // Filter Students
        function filterStudents() {
            const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
            const gradeFilter = document.getElementById('gradeFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;

            console.log('Filtering - Search:', searchTerm, 'Grade:', gradeFilter, 'Status:', statusFilter);

            filteredStudents = allEnrolledStudents.filter(enrollment => {
                const student = enrollment.student;

                // Check if student object exists
                if (!student) {
                    return false;
                }

                // Search filter
                const matchesSearch = !searchTerm ||
                    (student.fname && student.fname.toLowerCase().includes(searchTerm)) ||
                    (student.lname && student.lname.toLowerCase().includes(searchTerm)) ||
                    (student.custom_id && student.custom_id.toLowerCase().includes(searchTerm));

                // Grade filter - FIXED: Check both grade_id and nested grade object
                let matchesGrade = true;
                if (gradeFilter) {
                    // First check student.grade_id
                    if (student.grade_id && student.grade_id.toString() === gradeFilter.toString()) {
                        matchesGrade = true;
                    }
                    // Then check nested grade object
                    else if (student.grade && student.grade.id && student.grade.id.toString() === gradeFilter.toString()) {
                        matchesGrade = true;
                    } else {
                        matchesGrade = false;
                    }
                }

                // Status filter
                const matchesStatus = !statusFilter ||
                    enrollment.status.toString() === statusFilter;

                console.log(`Student ${student.custom_id}: search=${matchesSearch}, grade=${matchesGrade}, status=${matchesStatus}, grade_id=${student.grade_id}, grade_object=`, student.grade);

                return matchesSearch && matchesGrade && matchesStatus;
            });

            console.log('Filtered students count:', filteredStudents.length);

            studentsCurrentPage = 1;
            renderStudentsTable();
        }

        // Bulk Deactivate Students
        async function bulkDeactivateStudents() {
            if (selectedStudents.size === 0) {
                showAlert('Please select at least one student to deactivate', 'warning');
                return;
            }

            if (!confirm(`Are you sure you want to deactivate ${selectedStudents.size} students?`)) {
                return;
            }

            const bulkDeactivateBtn = document.getElementById('bulkDeactivateBtn');
            const originalText = bulkDeactivateBtn.innerHTML;

            try {
                bulkDeactivateBtn.disabled = true;
                bulkDeactivateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deactivating...';

                const enrollmentIds = Array.from(selectedStudents);
                const requestData = {
                    student_class_ids: enrollmentIds
                };

                const response = await fetch(api('student-classes/bulk-deactivate'), {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert(`Successfully deactivated ${result.deactivated_count} students`, 'success');

                    // Reload enrolled students to update the table
                    if (selectedCategoryId) {
                        await loadEnrolledStudents(selectedCategoryId);
                    }

                    // Clear selections
                    clearAllSelections();

                } else {
                    throw new Error(result.message || 'Failed to deactivate students');
                }

            } catch (error) {
                console.error('Error bulk deactivating students:', error);
                showAlert('Failed to deactivate students: ' + error.message, 'danger');
            } finally {
                bulkDeactivateBtn.disabled = false;
                bulkDeactivateBtn.innerHTML = '<i class="fas fa-user-minus me-1"></i>Deactivate Selected';
            }
        }

        // Activate Single Student
        async function activateStudent(enrollmentId) {
            if (!confirm('Are you sure you want to activate this student?')) {
                return;
            }

            try {
                const response = await fetch(api(`student-classes/${enrollmentId}/activate`), {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert('Student activated successfully', 'success');

                    // Reload enrolled students to update the table
                    if (selectedCategoryId) {
                        await loadEnrolledStudents(selectedCategoryId);
                    }

                } else {
                    throw new Error(result.message || 'Failed to activate student');
                }

            } catch (error) {
                console.error('Error activating student:', error);
                showAlert('Failed to activate student: ' + error.message, 'danger');
            }
        }

        // Deactivate Single Student
        async function deactivateStudent(enrollmentId) {
            if (!confirm('Are you sure you want to deactivate this student?')) {
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

                if (result.status === 'success') {
                    showAlert('Student deactivated successfully', 'success');

                    // Reload enrolled students to update the table
                    if (selectedCategoryId) {
                        await loadEnrolledStudents(selectedCategoryId);
                    }

                } else {
                    throw new Error(result.message || 'Failed to deactivate student');
                }

            } catch (error) {
                console.error('Error deactivating student:', error);
                showAlert('Failed to deactivate student: ' + error.message, 'danger');
            }
        }

        // Utility Functions
        function clearSelection() {
            selectedCategoryId = null;
            selectedCategoryName = '';
            selectedCategoryFee = 0;
            selectedStudents.clear();
            allEnrolledStudents = [];
            filteredStudents = [];

            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('selected');
            });

            document.getElementById('selectedCategoryInfo').style.display = 'none';
            document.getElementById('bulkActions').style.display = 'none';

            // Clear table
            const tbody = document.getElementById('studentsTableBody');
            tbody.innerHTML = '';
            document.getElementById('studentsEmpty').classList.remove('d-none');
            document.getElementById('studentsPagination').classList.add('d-none');

            updateSelectedCount();
        }

        function clearSearch() {
            document.getElementById('studentSearch').value = '';
            document.getElementById('gradeFilter').value = '';
            document.getElementById('statusFilter').value = '';
            filterStudents();
        }

        function updateSelectedCount() {
            const count = selectedStudents.size;
            document.getElementById('selectedStudentsCount').textContent = `${count} student${count !== 1 ? 's' : ''} selected`;
        }

        function showStudentsLoading() {
            document.getElementById('studentsLoading').classList.remove('d-none');
            document.getElementById('studentsTableBody').closest('.table-responsive').classList.add('d-none');
            document.getElementById('studentsPagination').classList.add('d-none');
        }

        function hideStudentsLoading() {
            document.getElementById('studentsLoading').classList.add('d-none');
            document.getElementById('studentsTableBody').closest('.table-responsive').classList.remove('d-none');
        }

        function showStudentsEmptyState() {
            document.getElementById('studentsEmpty').classList.remove('d-none');
            document.getElementById('studentsTableBody').closest('.table-responsive').classList.add('d-none');
            document.getElementById('studentsPagination').classList.add('d-none');
        }

        function showAlert(message, type) {
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