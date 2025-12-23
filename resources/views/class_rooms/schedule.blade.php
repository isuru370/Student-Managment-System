@extends('layouts.app')

@section('title', 'Class Schedules')
@section('page-title', 'Class Schedules Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Class Schedules</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="schedulesTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes"
                        type="button" role="tab" aria-controls="classes" aria-selected="true">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Active Classes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories"
                        type="button" role="tab" aria-controls="categories" aria-selected="false">
                        <i class="fas fa-tags me-2"></i>Categories Management
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="schedulesTabsContent">
                <!-- Active Classes Tab -->
                <div class="tab-pane fade show active" id="classes" role="tabpanel" aria-labelledby="classes-tab">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">Active Classes</h5>
                                    <p class="text-muted mb-0">Manage all active class rooms</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary" onclick="loadActiveClasses()" title="Refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Search Bar -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control"
                                            placeholder="Search by grade, subject or teacher..." id="classSearch">
                                    </div>
                                </div>
                            </div>

                            <!-- Active Classes Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th width="60">#</th>
                                            <th>Class Name</th>
                                            <th>Teacher</th>
                                            <th>Subject</th>
                                            <th>Grade</th>
                                            
                                            <th width="100" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="activeClassesTableBody">
                                        <!-- Active classes will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination for Active Classes -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div id="classesPaginationContainer" class="d-none">
                                        <!-- Pagination controls will be loaded here -->
                                    </div>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div id="classesLoading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading active classes...</p>
                            </div>

                            <!-- Empty State -->
                            <div id="classesEmpty" class="text-center py-5 d-none">
                                <div class="empty-state-icon">
                                    <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-4"></i>
                                </div>
                                <h4 class="text-muted">No Active Classes Found</h4>
                                <p class="text-muted mb-4">There are no active classes in the database yet.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Tab -->
                <div class="tab-pane fade" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">Categories Management</h5>
                                    <p class="text-muted mb-0">Manage all categories</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary" onclick="loadCategories()" title="Refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addCategoryModal">
                                        <i class="fas fa-plus me-2"></i>Add Category
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Categories Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th width="60">#</th>
                                            <th>Category Name</th>
                                            <th>Created Date</th>
                                            <th width="120" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="categoriesTableBody">
                                        <!-- Categories will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination for Categories -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div id="categoriesPaginationContainer" class="d-none">
                                        <!-- Pagination controls will be loaded here -->
                                    </div>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div id="categoriesLoading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading categories...</p>
                            </div>

                            <!-- Empty State -->
                            <div id="categoriesEmpty" class="text-center py-5 d-none">
                                <div class="empty-state-icon">
                                    <i class="fas fa-tags fa-4x text-muted mb-4"></i>
                                </div>
                                <h4 class="text-muted">No Categories Found</h4>
                                <p class="text-muted mb-4">There are no categories in the database yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCategoryModalLabel">
                        <i class="fas fa-plus me-2"></i>Add New Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                            <div class="invalid-feedback" id="category_name_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                        <i class="fas fa-save me-2"></i>Save Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editCategoryModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="edit_category_id">
                        <div class="mb-3">
                            <label for="edit_category_name" class="form-label">Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                            <div class="invalid-feedback" id="edit_category_name_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="updateCategoryBtn">
                        <i class="fas fa-save me-2"></i>Update Category
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header.bg-transparent {
            background: transparent !important;
            color: inherit;
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            border: none;
        }

        .table td {
            vertical-align: middle;
            border-color: #f8f9fa;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .empty-state-icon {
            opacity: 0.5;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Tab Styles */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            background: white;
            border-radius: 15px 15px 0 0;
            padding: 0.5rem 0.5rem 0 0.5rem;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px 10px 0 0;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #6c757d;
            background: transparent;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }

        .nav-tabs .nav-link:hover {
            color: #007bff;
            background: rgba(0, 123, 255, 0.1);
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            background: white;
            border-bottom: 3px solid #007bff;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-tabs .nav-link i {
            margin-right: 0.5rem;
        }

        .tab-content {
            background: white;
            border-radius: 0 0 15px 15px;
        }

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
        
        /* Sri Lankan Date Format */
        .sl-date {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Status Badge */
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
        
        .status-ongoing {
            background-color: #cff4fc;
            color: #055160;
            border: 1px solid #b6effb;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let allActiveClasses = [];
        let allCategories = [];

        // Pagination variables
        let classesCurrentPage = 1;
        let categoriesCurrentPage = 1;
        const recordsPerPage = 10;

        document.addEventListener('DOMContentLoaded', function () {
            // Load data on page load
            loadActiveClasses();
            loadCategories();

            // Search functionality
            const classSearch = document.getElementById('classSearch');
            classSearch.addEventListener('input', debounce(function () {
                filterActiveClasses();
            }, 300));

            // Category modal events
            const saveCategoryBtn = document.getElementById('saveCategoryBtn');
            saveCategoryBtn.addEventListener('click', saveCategory);

            const updateCategoryBtn = document.getElementById('updateCategoryBtn');
            updateCategoryBtn.addEventListener('click', updateCategory);

            // Tab change event - reload data when switching tabs
            const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    const target = event.target.getAttribute('data-bs-target');
                    if (target === '#categories') {
                        loadCategories();
                    } else if (target === '#classes') {
                        loadActiveClasses();
                    }
                });
            });
        });

        // ================= SRI LANKAN DATE FORMAT FUNCTIONS =================
        // Format date to Sri Lankan format (DD-MM-YYYY)
        function formatDateToSriLankan(dateString) {
            if (!dateString) return 'N/A';
            
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                
                const day = date.getDate().toString().padStart(2, '0');
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const year = date.getFullYear();
                
                return `${day}-${month}-${year}`;
            } catch (error) {
                console.error('Error formatting date:', error, dateString);
                return dateString;
            }
        }

        // Format time to Sri Lankan 12-hour format
        function formatTimeToSriLankan(dateString) {
            if (!dateString) return '';
            
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
                
                let hours = date.getHours();
                let minutes = date.getMinutes().toString().padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // Convert 0 to 12
                
                return `${hours}:${minutes} ${ampm}`;
            } catch (error) {
                console.error('Error formatting time:', error);
                return '';
            }
        }

        // Format date and time to Sri Lankan format
        function formatDateTimeToSriLankan(dateString) {
            const datePart = formatDateToSriLankan(dateString);
            const timePart = formatTimeToSriLankan(dateString);
            
            return timePart ? `${datePart} ${timePart}` : datePart;
        }

        // ================= ACTIVE CLASSES FUNCTIONS =================
        function loadActiveClasses() {
            showClassesLoading();

            fetch("{{ url('/api/class-rooms/active') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Handle different response formats
                    let classesArray = [];
                    
                    if (data.status === 'success' && data.data) {
                        classesArray = data.data;
                    } else if (Array.isArray(data)) {
                        classesArray = data;
                    } else if (data.classes) {
                        classesArray = data.classes;
                    } else {
                        throw new Error('Invalid response format from active classes API');
                    }

                    // Process the classes data
                    allActiveClasses = processClassData(classesArray);
                    renderActiveClassesTable(allActiveClasses);
                    hideClassesLoading();
                })
                .catch(error => {
                    console.error('Error loading active classes:', error);
                    showAlert('Error loading active classes. Please check console for details.', 'danger');
                    hideClassesLoading();

                    // Show empty state
                    const emptyState = document.getElementById('classesEmpty');
                    if (emptyState) emptyState.classList.remove('d-none');
                });
        }

        // Process class data with proper boolean handling
        function processClassData(classes) {
            return classes.map(classRoom => ({
                ...classRoom,
                // Ensure proper boolean values
                is_active: getBooleanValue(classRoom.is_active),
                is_ongoing: getBooleanValue(classRoom.is_ongoing),
                // Ensure proper date formatting
                created_at: classRoom.created_at || new Date().toISOString(),
                updated_at: classRoom.updated_at || new Date().toISOString(),
                // Ensure teacher object exists
                teacher: classRoom.teacher || { fname: 'N/A', lname: '', custom_id: '' },
                subject: classRoom.subject || { subject_name: 'N/A' },
                grade: classRoom.grade || { grade_name: 'N/A' }
            }));
        }

        // Helper function to convert values to boolean
        function getBooleanValue(value) {
            if (typeof value === 'boolean') return value;
            if (typeof value === 'number') return value === 1;
            if (typeof value === 'string') {
                if (value.toLowerCase() === 'true') return true;
                if (value.toLowerCase() === 'false') return false;
                return value === '1';
            }
            return false;
        }

        function renderActiveClassesTable(classes) {
            const tbody = document.getElementById('activeClassesTableBody');
            const emptyState = document.getElementById('classesEmpty');
            const paginationContainer = document.getElementById('classesPaginationContainer');

            if (!tbody) return;

            tbody.innerHTML = '';

            if (classes.length === 0) {
                emptyState.classList.remove('d-none');
                paginationContainer.classList.add('d-none');
                return;
            }

            emptyState.classList.add('d-none');
            paginationContainer.classList.remove('d-none');

            // Calculate pagination
            const totalPages = Math.ceil(classes.length / recordsPerPage);
            const startIndex = (classesCurrentPage - 1) * recordsPerPage;
            const endIndex = startIndex + recordsPerPage;
            const paginatedClasses = classes.slice(startIndex, endIndex);

            // Render table rows
            paginatedClasses.forEach((classRoom, index) => {
                const actualIndex = startIndex + index;

                // Check if class is ongoing to show Add Student button
                const showAddStudentButton = classRoom.is_ongoing === true;

                // Format dates to Sri Lankan format
                const createdDate = formatDateToSriLankan(classRoom.created_at);

                // Status badges
                const isActiveBadge = classRoom.is_active ? 
                    '<span class="badge status-active status-badge">Active</span>' : 
                    '<span class="badge status-inactive status-badge">Inactive</span>';
                
                const isOngoingBadge = classRoom.is_ongoing ? 
                    '<span class="badge status-ongoing status-badge ms-1">Ongoing</span>' : 
                    '';

                const row = `
                    <tr>
                        <td class="fw-bold text-muted">${actualIndex + 1}</td>
                        <td>
                            <h6 class="mb-0 fw-bold">${classRoom.class_name || 'No Name'}</h6>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">${classRoom.teacher.fname} ${classRoom.teacher.lname}</h6>
                                    <small class="text-muted">${classRoom.teacher.custom_id || ''}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-book me-1 text-primary"></i>
                                ${classRoom.subject.subject_name}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-gradient">
                                <i class="fas fa-graduation-cap me-1"></i>
                                ${classRoom.grade.grade_name}
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="View Class Details" 
                                        onclick="viewClassSchedule(${classRoom.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${showAddStudentButton ? `
                                <button class="btn btn-outline-success" title="Add Student To Class" 
                                        onclick="addStudentToClass(${classRoom.id})">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            // Update pagination controls
            updateClassesPaginationControls(classes.length, totalPages);
        }

        function updateClassesPaginationControls(totalRecords, totalPages) {
            const paginationDiv = document.getElementById('classesPaginationContainer');
            const startRecord = ((classesCurrentPage - 1) * recordsPerPage) + 1;
            const endRecord = Math.min(classesCurrentPage * recordsPerPage, totalRecords);

            paginationDiv.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-2">Show:</span>
                            <select class="form-select form-select-sm" style="width: auto;" onchange="changeClassesRecordsPerPage(this.value)">
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
                                    <li class="page-item ${classesCurrentPage === 1 ? 'disabled' : ''}">
                                        <a class="page-link" href="#" onclick="changeClassesPage(${classesCurrentPage - 1})">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>

                                    ${generateClassesPageNumbers(totalPages)}

                                    <li class="page-item ${classesCurrentPage === totalPages ? 'disabled' : ''}">
                                        <a class="page-link" href="#" onclick="changeClassesPage(${classesCurrentPage + 1})">
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

        function generateClassesPageNumbers(totalPages) {
            let pageNumbers = '';
            const maxPagesToShow = 5;
            let startPage = Math.max(1, classesCurrentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                pageNumbers += `
                    <li class="page-item ${classesCurrentPage === i ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changeClassesPage(${i})">${i}</a>
                    </li>
                `;
            }

            return pageNumbers;
        }

        function changeClassesPage(page) {
            if (page < 1 || page > Math.ceil(allActiveClasses.length / recordsPerPage)) return;
            classesCurrentPage = page;
            renderActiveClassesTable(allActiveClasses);
        }

        function changeClassesRecordsPerPage(newSize) {
            recordsPerPage = parseInt(newSize);
            classesCurrentPage = 1;
            renderActiveClassesTable(allActiveClasses);
        }

        function filterActiveClasses() {
            const searchTerm = document.getElementById('classSearch').value.toLowerCase();
            const filteredClasses = allActiveClasses.filter(classRoom =>
                (classRoom.grade && classRoom.grade.grade_name && classRoom.grade.grade_name.toLowerCase().includes(searchTerm)) ||
                (classRoom.subject && classRoom.subject.subject_name && classRoom.subject.subject_name.toLowerCase().includes(searchTerm)) ||
                (classRoom.teacher && classRoom.teacher.fname && classRoom.teacher.fname.toLowerCase().includes(searchTerm)) ||
                (classRoom.teacher && classRoom.teacher.lname && classRoom.teacher.lname.toLowerCase().includes(searchTerm)) ||
                (classRoom.class_name && classRoom.class_name.toLowerCase().includes(searchTerm))
            );
            classesCurrentPage = 1; // Reset to first page when filtering
            renderActiveClassesTable(filteredClasses);
        }

        function showClassesLoading() {
            document.getElementById('classesLoading').classList.remove('d-none');
            document.getElementById('activeClassesTableBody').closest('.table-responsive').classList.add('d-none');
        }

        function hideClassesLoading() {
            document.getElementById('classesLoading').classList.add('d-none');
            document.getElementById('activeClassesTableBody').closest('.table-responsive').classList.remove('d-none');
        }

        function viewClassSchedule(classId) {
            window.location.href = `/class-rooms/add_class_category/${classId}`;
        }

        function addStudentToClass(classId) {
            // Use the correct route with parameter
            window.location.href = `/students/add_student_to_class/${classId}`;
        }

        // ================= CATEGORIES FUNCTIONS =================
        function loadCategories() {
            showCategoriesLoading();

            fetch("{{ url('/api/categories') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Handle different response formats
                    let categoriesArray = [];
                    
                    if (data.status === 'success' && data.data) {
                        categoriesArray = data.data;
                    } else if (Array.isArray(data)) {
                        categoriesArray = data;
                    } else if (data.categories) {
                        categoriesArray = data.categories;
                    } else {
                        throw new Error('Invalid response format from categories API');
                    }

                    // Process categories with proper date handling
                    allCategories = categoriesArray.map(category => ({
                        ...category,
                        created_at: category.created_at || new Date().toISOString()
                    }));

                    renderCategoriesTable(allCategories);
                    hideCategoriesLoading();
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    showAlert('Error loading categories. Please check console for details.', 'danger');
                    hideCategoriesLoading();

                    // Show empty state
                    const emptyState = document.getElementById('categoriesEmpty');
                    if (emptyState) emptyState.classList.remove('d-none');
                });
        }

        function renderCategoriesTable(categories) {
            const tbody = document.getElementById('categoriesTableBody');
            const emptyState = document.getElementById('categoriesEmpty');
            const paginationContainer = document.getElementById('categoriesPaginationContainer');

            if (!tbody) return;

            tbody.innerHTML = '';

            if (categories.length === 0) {
                emptyState.classList.remove('d-none');
                paginationContainer.classList.add('d-none');
                return;
            }

            emptyState.classList.add('d-none');
            paginationContainer.classList.remove('d-none');

            // Calculate pagination
            const totalPages = Math.ceil(categories.length / recordsPerPage);
            const startIndex = (categoriesCurrentPage - 1) * recordsPerPage;
            const endIndex = startIndex + recordsPerPage;
            const paginatedCategories = categories.slice(startIndex, endIndex);

            // Render table rows with Sri Lankan date format
            paginatedCategories.forEach((category, index) => {
                const actualIndex = startIndex + index;
                const createdDate = formatDateToSriLankan(category.created_at);
                
                const row = `
                    <tr>
                        <td class="fw-bold text-muted">${actualIndex + 1}</td>
                        <td>${category.category_name}</td>
                        <td class="sl-date">${createdDate}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-warning" title="Edit" 
                                        onclick="showEditCategoryModal(${category.id}, '${escapeHtml(category.category_name)}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            // Update pagination controls
            updateCategoriesPaginationControls(categories.length, totalPages);
        }

        function updateCategoriesPaginationControls(totalRecords, totalPages) {
            const paginationDiv = document.getElementById('categoriesPaginationContainer');
            const startRecord = ((categoriesCurrentPage - 1) * recordsPerPage) + 1;
            const endRecord = Math.min(categoriesCurrentPage * recordsPerPage, totalRecords);

            paginationDiv.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-2">Show:</span>
                            <select class="form-select form-select-sm" style="width: auto;" onchange="changeCategoriesRecordsPerPage(this.value)">
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
                                    <li class="page-item ${categoriesCurrentPage === 1 ? 'disabled' : ''}">
                                        <a class="page-link" href="#" onclick="changeCategoriesPage(${categoriesCurrentPage - 1})">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>

                                    ${generateCategoriesPageNumbers(totalPages)}

                                    <li class="page-item ${categoriesCurrentPage === totalPages ? 'disabled' : ''}">
                                        <a class="page-link" href="#" onclick="changeCategoriesPage(${categoriesCurrentPage + 1})">
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

        function generateCategoriesPageNumbers(totalPages) {
            let pageNumbers = '';
            const maxPagesToShow = 5;
            let startPage = Math.max(1, categoriesCurrentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                pageNumbers += `
                    <li class="page-item ${categoriesCurrentPage === i ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changeCategoriesPage(${i})">${i}</a>
                    </li>
                `;
            }

            return pageNumbers;
        }

        function changeCategoriesPage(page) {
            if (page < 1 || page > Math.ceil(allCategories.length / recordsPerPage)) return;
            categoriesCurrentPage = page;
            renderCategoriesTable(allCategories);
        }

        function changeCategoriesRecordsPerPage(newSize) {
            recordsPerPage = parseInt(newSize);
            categoriesCurrentPage = 1;
            renderCategoriesTable(allCategories);
        }

        function showCategoriesLoading() {
            document.getElementById('categoriesLoading').classList.remove('d-none');
            document.getElementById('categoriesTableBody').closest('.table-responsive').classList.add('d-none');
        }

        function hideCategoriesLoading() {
            document.getElementById('categoriesLoading').classList.add('d-none');
            document.getElementById('categoriesTableBody').closest('.table-responsive').classList.remove('d-none');
        }

        // Category Modal Functions
        function showEditCategoryModal(categoryId, categoryName) {
            document.getElementById('edit_category_id').value = categoryId;
            document.getElementById('edit_category_name').value = categoryName;

            const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            modal.show();
        }

        function saveCategory() {
            const categoryName = document.getElementById('category_name').value.trim();
            const saveCategoryBtn = document.getElementById('saveCategoryBtn');
            const originalText = saveCategoryBtn.innerHTML;

            if (!categoryName) {
                showAlert('Please enter category name', 'warning');
                return;
            }

            // Show loading state
            saveCategoryBtn.disabled = true;
            saveCategoryBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

            fetch("{{ url('/api/categories') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ category_name: categoryName })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                        modal.hide();

                        // Clear form
                        document.getElementById('category_name').value = '';

                        // Reload categories table
                        loadCategories();

                        showAlert('Category created successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to create category');
                    }
                })
                .catch(error => {
                    console.error('Error creating category:', error);
                    showAlert('Error creating category: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Restore button state
                    saveCategoryBtn.disabled = false;
                    saveCategoryBtn.innerHTML = originalText;
                });
        }

        function updateCategory() {
            const categoryId = document.getElementById('edit_category_id').value;
            const categoryName = document.getElementById('edit_category_name').value.trim();
            const updateCategoryBtn = document.getElementById('updateCategoryBtn');
            const originalText = updateCategoryBtn.innerHTML;

            if (!categoryName) {
                showAlert('Please enter category name', 'warning');
                return;
            }

            // Show loading state
            updateCategoryBtn.disabled = true;
            updateCategoryBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            fetch(`/api/categories/${categoryId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ category_name: categoryName })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                        modal.hide();

                        // Reload categories table
                        loadCategories();

                        showAlert('Category updated successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to update category');
                    }
                })
                .catch(error => {
                    console.error('Error updating category:', error);
                    showAlert('Error updating category: ' + error.message, 'danger');
                })
                .finally(() => {
                    // Restore button state
                    updateCategoryBtn.disabled = false;
                    updateCategoryBtn.innerHTML = originalText;
                });
        }

        // ================= HELPER FUNCTIONS =================
        function debounce(func, wait) {
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

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const container = document.querySelector('.container') || document.querySelector('.card-body');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        }
    </script>
@endpush