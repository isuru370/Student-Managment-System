@extends('layouts.app')

@section('title', 'Manage Class Rooms')
@section('page-title', 'Class Rooms Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Class Rooms</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Total Classes</h4>
                                    <h2 class="text-white" id="totalClasses">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Active Classes</h4>
                                    <h2 class="text-white" id="activeClasses">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-info bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Ongoing Classes</h4>
                                    <h2 class="text-white" id="ongoingClasses">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-play-circle fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Inactive Classes</h4>
                                    <h2 class="text-white" id="inactiveClasses">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-pause-circle fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card custom-card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Class Rooms Management</h5>
                            <p class="text-muted mb-0">Manage all class rooms and their information</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" onclick="loadClassRooms()" title="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <a href="{{ route('class_rooms.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add New Class Room
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body position-relative">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading class rooms...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger d-none" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="errorText"></span>
                    </div>

                    <!-- Action Bar -->
                    <div class="d-none" id="actionBar">
                        <!-- First Row: Info and Export Controls -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted" id="classRoomCount">Showing 0 class rooms</span>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm" onclick="exportTo('csv')">
                                    <i class="fas fa-file-csv me-1"></i>CSV
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="exportTo('excel')">
                                    <i class="fas fa-file-excel me-1"></i>Excel
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i>Print
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="exportTo('pdf')">
                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                </button>
                            </div>
                        </div>

                        <!-- Second Row: Filters and Search -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <!-- View Toggle -->
                                <div class="btn-group btn-group-sm me-2">
                                    <button type="button" class="btn btn-outline-secondary active" id="tableViewBtn"
                                        title="Table View">
                                        <i class="fas fa-table"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="cardViewBtn"
                                        title="Card View">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                </div>

                                <!-- Status Filter -->
                                <div class="btn-group btn-group-sm me-2">
                                    <button type="button" class="btn btn-outline-secondary active" id="filterAll"
                                        data-status="">All</button>
                                    <button type="button" class="btn btn-outline-success" id="filterActive"
                                        data-status="active">Active</button>
                                    <button type="button" class="btn btn-outline-secondary" id="filterInactive"
                                        data-status="inactive">Inactive</button>
                                </div>

                                <!-- Ongoing Filter -->
                                <div class="btn-group btn-group-sm me-2">
                                    <button type="button" class="btn btn-outline-secondary active" id="filterOngoingAll"
                                        data-ongoing="">All</button>
                                    <button type="button" class="btn btn-outline-info" id="filterOngoing"
                                        data-ongoing="ongoing">Ongoing</button>
                                    <button type="button" class="btn btn-outline-secondary" id="filterNotOngoing"
                                        data-ongoing="not_ongoing">Not Ongoing</button>
                                </div>

                                <!-- Grade Filter -->
                                <div class="d-flex align-items-center me-2">
                                    <label for="gradeFilter" class="form-label text-muted mb-0 me-2">Grade:</label>
                                    <select class="form-select form-select-sm" id="gradeFilter" style="width: 120px;">
                                        <option value="">All Grades</option>
                                    </select>
                                </div>

                                <!-- Teacher Filter -->
                                <div class="d-flex align-items-center me-2">
                                    <label for="teacherFilter" class="form-label text-muted mb-0 me-2">Teacher:</label>
                                    <select class="form-select form-select-sm" id="teacherFilter" style="width: 140px;">
                                        <option value="">All Teachers</option>
                                    </select>
                                </div>

                                <!-- Subject Filter -->
                                <div class="d-flex align-items-center me-2">
                                    <label for="subjectFilter" class="form-label text-muted mb-0 me-2">Subject:</label>
                                    <select class="form-select form-select-sm" id="subjectFilter" style="width: 140px;">
                                        <option value="">All Subjects</option>
                                    </select>
                                </div>

                                <!-- Rows Per Page -->
                                <div class="d-flex align-items-center me-2">
                                    <label for="rowsPerPage" class="form-label text-muted mb-0 me-2">Show:</label>
                                    <select class="form-select form-select-sm" id="rowsPerPage" style="width: 80px;">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Search Box -->
                            <div class="input-group input-group-sm" style="width: 280px;">
                                <span class="input-group-text bg-transparent">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search class rooms..." id="searchInput"
                                    autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" title="Clear">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Class Rooms Cards View -->
                    <div class="row d-none" id="classRoomsCardsContainer">
                        <!-- Class room cards will be loaded here via JavaScript -->
                    </div>

                    <!-- Class Rooms Table -->
                    <div class="table-responsive" id="classRoomsTableContainer">
                        <table class="table table-hover" id="classRoomsTable">
                            <thead class="table-primary">
                                <tr>
                                    <th width="60" class="text-center">#</th>
                                    <th>Class Name</th>
                                    <th>Teacher</th>
                                    <th>Subject</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                    <th width="180" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="classRoomsTableBody">
                                <!-- Class rooms will be loaded here via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-3 d-none" id="paginationSection">
                        <div class="col-md-6">
                            <div class="text-muted" id="paginationInfo">
                                Showing <span id="startRecord">0</span> to <span id="endRecord">0</span> of <span
                                    id="totalRecords">0</span> entries
                            </div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Class rooms pagination">
                                <ul class="pagination justify-content-end mb-0" id="paginationLinks">
                                </ul>
                            </nav>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5 d-none">
                        <div class="empty-state-icon">
                            <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-4"></i>
                        </div>
                        <h4 class="text-muted">No Class Rooms Found</h4>
                        <p class="text-muted mb-4">There are no class rooms in the database yet.</p>
                        <a href="{{ route('class_rooms.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Add First Class Room
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activate Class Room Modal -->
    <div class="modal fade" id="activateClassRoomModal" tabindex="-1" aria-labelledby="activateClassRoomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>Activate Class Room
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to activate this class room?</p>
                    <div class="class-room-info bg-light p-3 rounded">
                        <strong id="activateClassName"></strong><br>
                        <small class="text-muted" id="activateClassTeacher"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="confirmActivateBtn">
                        <i class="fas fa-check-circle me-2"></i>Activate Class
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivate Class Room Modal -->
    <div class="modal fade" id="deactivateClassRoomModal" tabindex="-1" aria-labelledby="deactivateClassRoomModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="deactivateClassRoomModalLabel">
                        <i class="fas fa-pause-circle me-2"></i>Deactivate Class Room
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate this class room?</p>
                    <div class="class-room-info bg-light p-3 rounded">
                        <strong id="deactivateClassName"></strong><br>
                        <small class="text-muted" id="deactivateClassTeacher"></small>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This class room will be marked as inactive.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmDeactivateBtn">
                        <i class="fas fa-pause-circle me-2"></i>Deactivate Class
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Ongoing Class Modal -->
    <div class="modal fade" id="startOngoingModal" tabindex="-1" aria-labelledby="startOngoingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-play-circle me-2"></i>Start Class Session
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to start this class session?</p>
                    <div class="class-room-info bg-light p-3 rounded">
                        <strong id="startClassName"></strong><br>
                        <small class="text-muted" id="startClassTeacher"></small>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        This class will be marked as ongoing.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-info" id="confirmStartOngoingBtn">
                        <i class="fas fa-play-circle me-2"></i>Start Session
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stop Ongoing Class Modal -->
    <div class="modal fade" id="stopOngoingModal" tabindex="-1" aria-labelledby="stopOngoingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-stop-circle me-2"></i>Stop Class Session
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to stop this class session?</p>
                    <div class="class-room-info bg-light p-3 rounded">
                        <strong id="stopClassName"></strong><br>
                        <small class="text-muted" id="stopClassTeacher"></small>
                    </div>
                    <div class="alert alert-secondary mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        This class will no longer be marked as ongoing.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-dark" id="confirmStopOngoingBtn">
                        <i class="fas fa-stop-circle me-2"></i>Stop Session
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stat-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .custom-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
            border-color: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .badge.rounded-pill {
            padding: 0.5em 0.8em;
            font-size: 0.75rem;
        }

        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
            border-radius: 10px;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            border-color: #2c3e50;
        }

        .btn-group .btn.active {
            background-color: #2c3e50;
            color: white;
            border-color: #2c3e50;
        }

        .class-room-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .class-room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .class-room-card .card-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 15px;
            border-bottom: none;
        }

        .class-room-card .card-body {
            padding: 20px;
        }

        .class-room-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }

        .class-room-info {
            margin-bottom: 15px;
        }

        .class-room-info i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            color: #6a11cb;
        }

        .status-badges {
            display: flex;
            gap: 5px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 10px 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Global variables for pagination and filtering
        let currentPage = 1;
        let totalPages = 1;
        let rowsPerPage = 10;
        let totalRecords = 0;
        let currentStatusFilter = '';
        let currentOngoingFilter = '';
        let currentGradeFilter = '';
        let currentTeacherFilter = '';
        let currentSubjectFilter = '';
        let currentSearch = '';
        let allClassRooms = [];
        let allGrades = [];
        let allTeachers = [];
        let allSubjects = [];
        let currentView = 'table';

        // Wait for the DOM to be loaded
        document.addEventListener('DOMContentLoaded', function () {
            initializeClassRoomsPage();
        });

        function initializeClassRoomsPage() {
            // Load class rooms and filters on page load
            loadFilters();
            loadClassRooms();

            // Event listeners
            const rowsPerPageEl = document.getElementById('rowsPerPage');
            const gradeFilterEl = document.getElementById('gradeFilter');
            const teacherFilterEl = document.getElementById('teacherFilter');
            const subjectFilterEl = document.getElementById('subjectFilter');
            const searchInputEl = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const filterAllEl = document.getElementById('filterAll');
            const filterActiveEl = document.getElementById('filterActive');
            const filterInactiveEl = document.getElementById('filterInactive');
            const filterOngoingAllEl = document.getElementById('filterOngoingAll');
            const filterOngoingEl = document.getElementById('filterOngoing');
            const filterNotOngoingEl = document.getElementById('filterNotOngoing');
            const tableViewBtn = document.getElementById('tableViewBtn');
            const cardViewBtn = document.getElementById('cardViewBtn');
            const confirmActivateBtn = document.getElementById('confirmActivateBtn');
            const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');
            const confirmStartOngoingBtn = document.getElementById('confirmStartOngoingBtn');
            const confirmStopOngoingBtn = document.getElementById('confirmStopOngoingBtn');

            if (rowsPerPageEl) {
                rowsPerPageEl.addEventListener('change', function () {
                    rowsPerPage = parseInt(this.value);
                    currentPage = 1;
                    renderFilteredClassRooms();
                });
            }

            if (gradeFilterEl) {
                gradeFilterEl.addEventListener('change', function () {
                    currentGradeFilter = this.value;
                    currentPage = 1;
                    renderFilteredClassRooms();
                });
            }

            if (teacherFilterEl) {
                teacherFilterEl.addEventListener('change', function () {
                    currentTeacherFilter = this.value;
                    currentPage = 1;
                    renderFilteredClassRooms();
                });
            }

            if (subjectFilterEl) {
                subjectFilterEl.addEventListener('change', function () {
                    currentSubjectFilter = this.value;
                    currentPage = 1;
                    renderFilteredClassRooms();
                });
            }

            if (searchInputEl) {
                searchInputEl.addEventListener('input', debounce(function (e) {
                    currentSearch = e.target.value.toLowerCase();
                    currentPage = 1;
                    renderFilteredClassRooms();
                }, 300));
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function () {
                    document.getElementById('searchInput').value = '';
                    currentSearch = '';
                    currentPage = 1;
                    renderFilteredClassRooms();
                });
            }

            // Status filter functionality
            if (filterAllEl) {
                filterAllEl.addEventListener('click', function () {
                    setActiveStatusFilter(this, '');
                });
            }

            if (filterActiveEl) {
                filterActiveEl.addEventListener('click', function () {
                    setActiveStatusFilter(this, 'active');
                });
            }

            if (filterInactiveEl) {
                filterInactiveEl.addEventListener('click', function () {
                    setActiveStatusFilter(this, 'inactive');
                });
            }

            // Ongoing filter functionality
            if (filterOngoingAllEl) {
                filterOngoingAllEl.addEventListener('click', function () {
                    setActiveOngoingFilter(this, '');
                });
            }

            if (filterOngoingEl) {
                filterOngoingEl.addEventListener('click', function () {
                    setActiveOngoingFilter(this, 'ongoing');
                });
            }

            if (filterNotOngoingEl) {
                filterNotOngoingEl.addEventListener('click', function () {
                    setActiveOngoingFilter(this, 'not_ongoing');
                });
            }

            // View toggle
            if (tableViewBtn) {
                tableViewBtn.addEventListener('click', function () {
                    setActiveView('table');
                });
            }

            if (cardViewBtn) {
                cardViewBtn.addEventListener('click', function () {
                    setActiveView('card');
                });
            }

            // Modal events
            if (confirmActivateBtn) {
                confirmActivateBtn.addEventListener('click', confirmActivateClassRoom);
            }

            if (confirmDeactivateBtn) {
                confirmDeactivateBtn.addEventListener('click', confirmDeactivateClassRoom);
            }

            if (confirmStartOngoingBtn) {
                confirmStartOngoingBtn.addEventListener('click', confirmStartOngoing);
            }

            if (confirmStopOngoingBtn) {
                confirmStopOngoingBtn.addEventListener('click', confirmStopOngoing);
            }
        }

        // Loading State Functions
        function showLoadingState() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const actionBar = document.getElementById('actionBar');
            const tableContainer = document.getElementById('classRoomsTableContainer');
            const cardsContainer = document.getElementById('classRoomsCardsContainer');
            const paginationSection = document.getElementById('paginationSection');
            const emptyState = document.getElementById('emptyState');
            const errorMessage = document.getElementById('errorMessage');

            if (loadingSpinner) loadingSpinner.classList.remove('d-none');
            if (actionBar) actionBar.classList.add('d-none');
            if (tableContainer) tableContainer.classList.add('d-none');
            if (cardsContainer) cardsContainer.classList.add('d-none');
            if (paginationSection) paginationSection.classList.add('d-none');
            if (emptyState) emptyState.classList.add('d-none');
            if (errorMessage) errorMessage.classList.add('d-none');
        }

        function showContentState() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const actionBar = document.getElementById('actionBar');

            if (loadingSpinner) loadingSpinner.classList.add('d-none');
            if (actionBar) actionBar.classList.remove('d-none');
        }

        function showErrorState(message) {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');

            if (loadingSpinner) loadingSpinner.classList.add('d-none');
            if (errorMessage) errorMessage.classList.remove('d-none');
            if (errorText) errorText.textContent = message;
        }

        // Filter Loading Functions
        function loadFilters() {
            loadGrades();
            loadTeachers();
            loadSubjects();
        }

        function loadGrades() {
            fetch("{{ url('/api/grades/dropdown') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        allGrades = data.data;
                        populateGradeFilter(allGrades);
                    } else {
                        throw new Error(data.message || 'Failed to load grades');
                    }
                })
                .catch(error => {
                    console.error('Error loading grades:', error);
                });
        }

        function loadTeachers() {
            fetch("{{ url('/api/teachers/dropdown') }}")
                .then(response => {
                    if (!response.ok) {
                        console.warn('Teachers API failed, will extract from class rooms data');
                        allTeachers = [];
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.status === 'success') {
                        allTeachers = data.data;
                        populateTeacherFilter(allTeachers);
                    } else {
                        allTeachers = [];
                    }
                })
                .catch(error => {
                    console.error('Error loading teachers:', error);
                    allTeachers = [];
                });
        }

        function loadSubjects() {
            fetch("{{ url('/api/subjects/dropdown') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        allSubjects = data.data;
                        populateSubjectFilter(allSubjects);
                    } else {
                        throw new Error(data.message || 'Failed to load subjects');
                    }
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                });
        }

        // Populate Filter Functions
        function populateGradeFilter(grades) {
            const gradeFilter = document.getElementById('gradeFilter');
            if (!gradeFilter) return;

            while (gradeFilter.options.length > 1) {
                gradeFilter.remove(1);
            }

            grades.forEach(grade => {
                const option = document.createElement('option');
                option.value = grade.id;
                option.textContent = `Grade ${grade.grade_name}`;
                gradeFilter.appendChild(option);
            });
        }

        function populateTeacherFilter(teachers) {
            const teacherFilter = document.getElementById('teacherFilter');
            if (!teacherFilter) return;

            while (teacherFilter.options.length > 1) {
                teacherFilter.remove(1);
            }

            if (teachers.length === 0) return;

            teachers.forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = `${teacher.fname} ${teacher.lname}`;
                teacherFilter.appendChild(option);
            });
        }

        function populateSubjectFilter(subjects) {
            const subjectFilter = document.getElementById('subjectFilter');
            if (!subjectFilter) return;

            while (subjectFilter.options.length > 1) {
                subjectFilter.remove(1);
            }

            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.subject_name;
                subjectFilter.appendChild(option);
            });
        }

        // Extract data from class rooms
        function extractTeachersFromClassRooms(classRooms) {
            const teachersMap = new Map();
            
            classRooms.forEach(classRoom => {
                if (classRoom.teacher && classRoom.teacher.id) {
                    if (!teachersMap.has(classRoom.teacher.id)) {
                        teachersMap.set(classRoom.teacher.id, {
                            id: classRoom.teacher.id,
                            fname: classRoom.teacher.fname,
                            lname: classRoom.teacher.lname
                        });
                    }
                }
            });
            
            return Array.from(teachersMap.values());
        }

        function extractSubjectsFromClassRooms(classRooms) {
            const subjectsMap = new Map();
            
            classRooms.forEach(classRoom => {
                if (classRoom.subject && classRoom.subject.id) {
                    if (!subjectsMap.has(classRoom.subject.id)) {
                        subjectsMap.set(classRoom.subject.id, {
                            id: classRoom.subject.id,
                            subject_name: classRoom.subject.subject_name
                        });
                    }
                }
            });
            
            return Array.from(subjectsMap.values());
        }

        function extractGradesFromClassRooms(classRooms) {
            const gradesMap = new Map();
            
            classRooms.forEach(classRoom => {
                if (classRoom.grade && classRoom.grade.id) {
                    if (!gradesMap.has(classRoom.grade.id)) {
                        gradesMap.set(classRoom.grade.id, {
                            id: classRoom.grade.id,
                            grade_name: classRoom.grade.grade_name
                        });
                    }
                }
            });
            
            return Array.from(gradesMap.values());
        }

        // Filter and View Functions
        function setActiveStatusFilter(button, status) {
            document.querySelectorAll('#actionBar .btn-group:nth-of-type(2) .btn').forEach(btn => {
                btn.classList.remove('active');
            });

            button.classList.add('active');
            currentStatusFilter = status;
            currentPage = 1;
            renderFilteredClassRooms();
        }

        function setActiveOngoingFilter(button, ongoing) {
            document.querySelectorAll('#actionBar .btn-group:nth-of-type(3) .btn').forEach(btn => {
                btn.classList.remove('active');
            });

            button.classList.add('active');
            currentOngoingFilter = ongoing;
            currentPage = 1;
            renderFilteredClassRooms();
        }

        function setActiveView(view) {
            currentView = view;

            const tableViewBtn = document.getElementById('tableViewBtn');
            const cardViewBtn = document.getElementById('cardViewBtn');

            if (tableViewBtn && cardViewBtn) {
                tableViewBtn.classList.toggle('active', view === 'table');
                cardViewBtn.classList.toggle('active', view === 'card');
            }

            const tableContainer = document.getElementById('classRoomsTableContainer');
            const cardsContainer = document.getElementById('classRoomsCardsContainer');

            if (tableContainer) tableContainer.classList.toggle('d-none', view !== 'table');
            if (cardsContainer) cardsContainer.classList.toggle('d-none', view !== 'card');

            renderFilteredClassRooms();
        }

        // Main Data Loading Function
        function loadClassRooms() {
            showLoadingState();
            fetch("{{ url('/api/class-rooms/all') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        allClassRooms = data.data.map(classRoom => ({
                            ...classRoom,
                            is_active: Boolean(classRoom.is_active),
                            is_ongoing: Boolean(classRoom.is_ongoing)
                        }));
                        
                        // If APIs failed, extract from class rooms data
                        if (allTeachers.length === 0) {
                            allTeachers = extractTeachersFromClassRooms(allClassRooms);
                            populateTeacherFilter(allTeachers);
                        }
                        
                        if (allSubjects.length === 0) {
                            allSubjects = extractSubjectsFromClassRooms(allClassRooms);
                            populateSubjectFilter(allSubjects);
                        }
                        
                        if (allGrades.length === 0) {
                            allGrades = extractGradesFromClassRooms(allClassRooms);
                            populateGradeFilter(allGrades);
                        }
                        
                        renderFilteredClassRooms();
                        updateStatistics(allClassRooms);
                        showContentState();
                    } else {
                        throw new Error(data.message || 'Failed to load class rooms');
                    }
                })
                .catch(error => {
                    console.error('Error loading class rooms:', error);
                    showErrorState('Error loading class rooms: ' + error.message);
                });
        }

        // Rendering Functions
        function renderFilteredClassRooms() {
            let filteredClassRooms = allClassRooms;

            // Apply filters
            if (currentStatusFilter === 'active') {
                filteredClassRooms = filteredClassRooms.filter(classRoom => classRoom.is_active);
            } else if (currentStatusFilter === 'inactive') {
                filteredClassRooms = filteredClassRooms.filter(classRoom => !classRoom.is_active);
            }

            if (currentOngoingFilter === 'ongoing') {
                filteredClassRooms = filteredClassRooms.filter(classRoom => classRoom.is_ongoing);
            } else if (currentOngoingFilter === 'not_ongoing') {
                filteredClassRooms = filteredClassRooms.filter(classRoom => !classRoom.is_ongoing);
            }

            if (currentGradeFilter) {
                filteredClassRooms = filteredClassRooms.filter(classRoom =>
                    classRoom.grade_id && classRoom.grade_id.toString() === currentGradeFilter
                );
            }

            if (currentTeacherFilter) {
                filteredClassRooms = filteredClassRooms.filter(classRoom =>
                    classRoom.teacher_id && classRoom.teacher_id.toString() === currentTeacherFilter
                );
            }

            if (currentSubjectFilter) {
                filteredClassRooms = filteredClassRooms.filter(classRoom =>
                    classRoom.subject_id && classRoom.subject_id.toString() === currentSubjectFilter
                );
            }

            if (currentSearch) {
                filteredClassRooms = filteredClassRooms.filter(classRoom =>
                    (classRoom.class_name && classRoom.class_name.toLowerCase().includes(currentSearch)) ||
                    (classRoom.teacher && classRoom.teacher.fname && classRoom.teacher.fname.toLowerCase().includes(currentSearch)) ||
                    (classRoom.teacher && classRoom.teacher.lname && classRoom.teacher.lname.toLowerCase().includes(currentSearch)) ||
                    (classRoom.subject && classRoom.subject.subject_name && classRoom.subject.subject_name.toLowerCase().includes(currentSearch)) ||
                    (classRoom.grade && classRoom.grade.grade_name && classRoom.grade.grade_name.toLowerCase().includes(currentSearch))
                );
            }

            totalRecords = filteredClassRooms.length;
            totalPages = Math.ceil(totalRecords / rowsPerPage);

            if (currentPage > totalPages && totalPages > 0) {
                currentPage = totalPages;
            } else if (currentPage < 1) {
                currentPage = 1;
            }

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedClassRooms = filteredClassRooms.slice(startIndex, endIndex);

            const viewToUse = typeof currentView !== 'undefined' ? currentView : 'table';
            
            if (viewToUse === 'table') {
                renderClassRoomsTable(paginatedClassRooms);
            } else {
                renderClassRoomsCards(paginatedClassRooms);
            }

            updatePagination();
        }

        function renderClassRoomsTable(classRooms) {
            const tbody = document.getElementById('classRoomsTableBody');
            const tableContainer = document.getElementById('classRoomsTableContainer');
            const emptyState = document.getElementById('emptyState');
            const paginationSection = document.getElementById('paginationSection');
            const classRoomCount = document.getElementById('classRoomCount');

            if (!tbody) return;

            tbody.innerHTML = '';

            if (classRooms.length === 0) {
                if (tableContainer) tableContainer.classList.add('d-none');
                if (paginationSection) paginationSection.classList.add('d-none');
                if (emptyState) emptyState.classList.remove('d-none');
                if (classRoomCount) classRoomCount.textContent = 'Showing 0 class rooms';
                return;
            }

            if (tableContainer) tableContainer.classList.remove('d-none');
            if (paginationSection) paginationSection.classList.remove('d-none');
            if (emptyState) emptyState.classList.add('d-none');
            if (classRoomCount) classRoomCount.textContent = `Showing ${classRooms.length} class rooms`;

            classRooms.forEach((classRoom, index) => {
                const startRecord = (currentPage - 1) * rowsPerPage;

                const isActive = classRoom.is_active;
                const isOngoing = classRoom.is_ongoing;

                const statusBadge = isActive ?
                    '<span class="badge bg-success rounded-pill"><i class="fas fa-check-circle me-1"></i>Active</span>' :
                    '<span class="badge bg-secondary rounded-pill"><i class="fas fa-pause-circle me-1"></i>Inactive</span>';

                const ongoingBadge = isOngoing ?
                    '<span class="badge bg-info rounded-pill"><i class="fas fa-play-circle me-1"></i>Ongoing</span>' :
                    '<span class="badge bg-light text-dark border rounded-pill"><i class="fas fa-stop-circle me-1"></i>Not Ongoing</span>';

                // Logic for action buttons based on your rules:
                // - If is_ongoing = 1, then is_active cannot be set to 0
                // - If is_active = 0, then is_ongoing cannot be set to 1
                
                const activateDeactivateButton = isActive ?
                    (isOngoing ?
                        // Ongoing class - cannot deactivate (set is_active to 0)
                        `<button class="btn btn-outline-warning" title="Cannot deactivate ongoing class" disabled>
                            <i class="fas fa-pause-circle"></i>
                        </button>` :
                        // Active but not ongoing - can deactivate
                        `<button class="btn btn-outline-warning" title="Deactivate" 
                                onclick="showDeactivateModal(${classRoom.id}, '${escapeHtml(classRoom.class_name)}', '${escapeHtml(classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher')}')">
                            <i class="fas fa-pause-circle"></i>
                        </button>`
                    ) :
                    // Inactive class - can activate
                    `<button class="btn btn-outline-success" title="Activate" 
                            onclick="showActivateModal(${classRoom.id}, '${escapeHtml(classRoom.class_name)}', '${escapeHtml(classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher')}')">
                        <i class="fas fa-check-circle"></i>
                    </button>`;

                const startStopOngoingButton = isOngoing ?
                    // Ongoing - can stop
                    `<button class="btn btn-outline-dark rounded-end" title="Stop Ongoing" 
                            onclick="showStopOngoingModal(${classRoom.id}, '${escapeHtml(classRoom.class_name)}', '${escapeHtml(classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher')}')">
                        <i class="fas fa-stop-circle"></i>
                    </button>` :
                    // Not ongoing - check if can start
                    (isActive ?
                        // Active class - can start ongoing
                        `<button class="btn btn-outline-info rounded-end" title="Start Ongoing" 
                                onclick="showStartOngoingModal(${classRoom.id}, '${escapeHtml(classRoom.class_name)}', '${escapeHtml(classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher')}')">
                            <i class="fas fa-play-circle"></i>
                        </button>` :
                        // Inactive class - cannot start ongoing
                        `<button class="btn btn-outline-info rounded-end" title="Cannot start ongoing for inactive class" disabled>
                            <i class="fas fa-play-circle"></i>
                        </button>`
                    );

                const row = `
                    <tr class="align-middle">
                        <td class="text-center fw-bold text-muted">${startRecord + index + 1}</td>
                        <td>
                            <h6 class="mb-0 fw-bold">${classRoom.class_name || 'No Name'}</h6>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">${classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher'}</h6>
                                    <small class="text-muted">${classRoom.teacher ? classRoom.teacher.custom_id : ''}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-book me-1 text-primary"></i>
                                ${classRoom.subject ? classRoom.subject.subject_name : 'N/A'}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-gradient">
                                <i class="fas fa-graduation-cap me-1"></i>
                                ${classRoom.grade ? classRoom.grade.grade_name : 'N/A'}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column gap-1 align-items-center">
                                ${statusBadge}
                                ${ongoingBadge}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary rounded-start" title="View" 
                                        onclick="viewClassRoom(${classRoom.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" title="Edit" 
                                        onclick="editClassRoom(${classRoom.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${activateDeactivateButton}
                                ${startStopOngoingButton}
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        function renderClassRoomsCards(classRooms) {
            const cardsContainer = document.getElementById('classRoomsCardsContainer');
            const emptyState = document.getElementById('emptyState');
            const paginationSection = document.getElementById('paginationSection');
            const classRoomCount = document.getElementById('classRoomCount');

            if (!cardsContainer) return;

            cardsContainer.innerHTML = '';

            if (classRooms.length === 0) {
                cardsContainer.classList.add('d-none');
                if (paginationSection) paginationSection.classList.add('d-none');
                if (emptyState) emptyState.classList.remove('d-none');
                if (classRoomCount) classRoomCount.textContent = 'Showing 0 class rooms';
                return;
            }

            cardsContainer.classList.remove('d-none');
            if (paginationSection) paginationSection.classList.remove('d-none');
            if (emptyState) emptyState.classList.add('d-none');
            if (classRoomCount) classRoomCount.textContent = `Showing ${classRooms.length} class rooms`;

            classRooms.forEach((classRoom) => {
                const isActive = classRoom.is_active;
                const isOngoing = classRoom.is_ongoing;

                const statusBadge = isActive ?
                    '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>' :
                    '<span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i>Inactive</span>';

                const ongoingBadge = isOngoing ?
                    '<span class="badge bg-info"><i class="fas fa-play-circle me-1"></i>Ongoing</span>' :
                    '<span class="badge bg-light text-dark border"><i class="fas fa-stop-circle me-1"></i>Not Ongoing</span>';

                // Same logic for card view buttons
                const activateDeactivateButton = isActive ?
                    (isOngoing ?
                        `<button class="btn btn-outline-warning btn-sm" title="Cannot deactivate ongoing class" disabled>
                            <i class="fas fa-pause-circle"></i>
                        </button>` :
                        `<button class="btn btn-outline-warning btn-sm" title="Deactivate" 
                                onclick="showDeactivateModal(${classRoom.id}, '${escapeHtml(classRoom.class_name)}', '${escapeHtml(classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher')}')">
                            <i class="fas fa-pause-circle"></i>
                        </button>`
                    ) :
                    `<button class="btn btn-outline-success btn-sm" title="Activate" 
                            onclick="showActivateModal(${classRoom.id}, '${escapeHtml(classRoom.class_name)}', '${escapeHtml(classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher')}')">
                        <i class="fas fa-check-circle"></i>
                    </button>`;

                const startStopOngoingButton = isOngoing ?
                    `<button class="btn btn-outline-dark btn-sm" title="Stop Ongoing" 
                            onclick="showStopOngoingModal(${classRoom.id}, '${escapeHtml(classRoom.class_name)}', '${escapeHtml(classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher')}')">
                        <i class="fas fa-stop-circle"></i>
                    </button>` :
                    (isActive ?
                        `<button class="btn btn-outline-info btn-sm" title="Start Ongoing" 
                                onclick="showStartOngoingModal(${classRoom.id}, '${escapeHtml(classRoom.class_name)}', '${escapeHtml(classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher')}')">
                            <i class="fas fa-play-circle"></i>
                        </button>` :
                        `<button class="btn btn-outline-info btn-sm" title="Cannot start ongoing for inactive class" disabled>
                            <i class="fas fa-play-circle"></i>
                        </button>`
                    );

                const card = `
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card class-room-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">${classRoom.class_name || 'No Name'}</h6>
                                </div>
                                <div class="status-badges">
                                    ${statusBadge}
                                    ${ongoingBadge}
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <div class="class-room-icon">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                
                                <div class="class-room-info text-start">
                                    <div class="mb-2">
                                        <i class="fas fa-user-tie"></i>
                                        <span><strong>Teacher:</strong> ${classRoom.teacher ? classRoom.teacher.fname + ' ' + classRoom.teacher.lname : 'No Teacher'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-book"></i>
                                        <span><strong>Subject:</strong> ${classRoom.subject ? classRoom.subject.subject_name : 'N/A'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span><strong>Grade:</strong> ${classRoom.grade ? classRoom.grade.grade_name : 'N/A'}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button class="btn btn-outline-primary btn-sm" 
                                            onclick="viewClassRoom(${classRoom.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" 
                                            onclick="editClassRoom(${classRoom.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${activateDeactivateButton}
                                    ${startStopOngoingButton}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                cardsContainer.innerHTML += card;
            });
        }

        // Pagination Functions
        function updatePagination() {
            const startRecord = totalRecords > 0 ? ((currentPage - 1) * rowsPerPage) + 1 : 0;
            const endRecord = Math.min(currentPage * rowsPerPage, totalRecords);

            const startRecordEl = document.getElementById('startRecord');
            const endRecordEl = document.getElementById('endRecord');
            const totalRecordsEl = document.getElementById('totalRecords');

            if (startRecordEl) startRecordEl.textContent = startRecord;
            if (endRecordEl) endRecordEl.textContent = endRecord;
            if (totalRecordsEl) totalRecordsEl.textContent = totalRecords;

            renderPaginationLinks();
        }

        function renderPaginationLinks() {
            const paginationLinks = document.getElementById('paginationLinks');
            if (!paginationLinks) return;

            paginationLinks.innerHTML = '';

            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" aria-label="Previous">
                    <span aria-hidden="true">Previous</span>
                </a>
            `;
            paginationLinks.appendChild(prevLi);

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
                paginationLinks.appendChild(li);
            }

            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" aria-label="Next">
                    <span aria-hidden="true">Next</span>
                </a>
            `;
            paginationLinks.appendChild(nextLi);
        }

        function changePage(page) {
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderFilteredClassRooms();
        }

        // Statistics Function
        function updateStatistics(classRooms) {
            const totalClasses = classRooms.length;
            const activeClasses = classRooms.filter(c => c.is_active).length;
            const ongoingClasses = classRooms.filter(c => c.is_ongoing).length;
            const inactiveClasses = classRooms.filter(c => !c.is_active).length;

            const totalClassesEl = document.getElementById('totalClasses');
            const activeClassesEl = document.getElementById('activeClasses');
            const ongoingClassesEl = document.getElementById('ongoingClasses');
            const inactiveClassesEl = document.getElementById('inactiveClasses');

            if (totalClassesEl) totalClassesEl.textContent = totalClasses;
            if (activeClassesEl) activeClassesEl.textContent = activeClasses;
            if (ongoingClassesEl) ongoingClassesEl.textContent = ongoingClasses;
            if (inactiveClassesEl) inactiveClassesEl.textContent = inactiveClasses;
        }

        // Modal Functions
        function showActivateModal(classRoomId, className, teacherName) {
            const activateClassName = document.getElementById('activateClassName');
            const activateClassTeacher = document.getElementById('activateClassTeacher');
            const confirmActivateBtn = document.getElementById('confirmActivateBtn');

            if (activateClassName) activateClassName.textContent = className;
            if (activateClassTeacher) activateClassTeacher.textContent = `Teacher: ${teacherName}`;

            const modal = new bootstrap.Modal(document.getElementById('activateClassRoomModal'));
            modal.show();

            if (confirmActivateBtn) confirmActivateBtn.setAttribute('data-class-room-id', classRoomId);
        }

        function showDeactivateModal(classRoomId, className, teacherName) {
            const deactivateClassName = document.getElementById('deactivateClassName');
            const deactivateClassTeacher = document.getElementById('deactivateClassTeacher');
            const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');

            if (deactivateClassName) deactivateClassName.textContent = className;
            if (deactivateClassTeacher) deactivateClassTeacher.textContent = `Teacher: ${teacherName}`;

            const modal = new bootstrap.Modal(document.getElementById('deactivateClassRoomModal'));
            modal.show();

            if (confirmDeactivateBtn) confirmDeactivateBtn.setAttribute('data-class-room-id', classRoomId);
        }

        function showStartOngoingModal(classRoomId, className, teacherName) {
            const startClassName = document.getElementById('startClassName');
            const startClassTeacher = document.getElementById('startClassTeacher');
            const confirmStartOngoingBtn = document.getElementById('confirmStartOngoingBtn');

            if (startClassName) startClassName.textContent = className;
            if (startClassTeacher) startClassTeacher.textContent = `Teacher: ${teacherName}`;

            const modal = new bootstrap.Modal(document.getElementById('startOngoingModal'));
            modal.show();

            if (confirmStartOngoingBtn) confirmStartOngoingBtn.setAttribute('data-class-room-id', classRoomId);
        }

        function showStopOngoingModal(classRoomId, className, teacherName) {
            const stopClassName = document.getElementById('stopClassName');
            const stopClassTeacher = document.getElementById('stopClassTeacher');
            const confirmStopOngoingBtn = document.getElementById('confirmStopOngoingBtn');

            if (stopClassName) stopClassName.textContent = className;
            if (stopClassTeacher) stopClassTeacher.textContent = `Teacher: ${teacherName}`;

            const modal = new bootstrap.Modal(document.getElementById('stopOngoingModal'));
            modal.show();

            if (confirmStopOngoingBtn) confirmStopOngoingBtn.setAttribute('data-class-room-id', classRoomId);
        }

        // Confirmation Functions - Updated to match your routes
        function confirmActivateClassRoom() {
            const confirmActivateBtn = document.getElementById('confirmActivateBtn');
            if (!confirmActivateBtn) return;

            const classRoomId = confirmActivateBtn.getAttribute('data-class-room-id');

            fetch(`/api/class-rooms/${classRoomId}/reactivate-active`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('activateClassRoomModal'));
                    if (modal) modal.hide();
                    showAlert('Class room activated successfully!', 'success');
                    loadClassRooms();
                } else {
                    throw new Error(data.message || 'Failed to activate class room');
                }
            })
            .catch(error => {
                console.error('Error activating class room:', error);
                showAlert('Error activating class room: ' + error.message, 'danger');
            });
        }

        function confirmDeactivateClassRoom() {
            const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');
            if (!confirmDeactivateBtn) return;

            const classRoomId = confirmDeactivateBtn.getAttribute('data-class-room-id');

            fetch(`/api/class-rooms/${classRoomId}/deactivate-active`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deactivateClassRoomModal'));
                    if (modal) modal.hide();
                    showAlert('Class room deactivated successfully!', 'success');
                    loadClassRooms();
                } else {
                    throw new Error(data.message || 'Failed to deactivate class room');
                }
            })
            .catch(error => {
                console.error('Error deactivating class room:', error);
                showAlert('Error deactivating class room: ' + error.message, 'danger');
            });
        }

        function confirmStartOngoing() {
            const confirmStartOngoingBtn = document.getElementById('confirmStartOngoingBtn');
            if (!confirmStartOngoingBtn) return;

            const classRoomId = confirmStartOngoingBtn.getAttribute('data-class-room-id');

            fetch(`/api/class-rooms/${classRoomId}/reactivate-ongoing`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('startOngoingModal'));
                    if (modal) modal.hide();
                    showAlert('Class session started successfully!', 'success');
                    loadClassRooms();
                } else {
                    throw new Error(data.message || 'Failed to start class session');
                }
            })
            .catch(error => {
                console.error('Error starting class session:', error);
                showAlert('Error starting class session: ' + error.message, 'danger');
            });
        }

        function confirmStopOngoing() {
            const confirmStopOngoingBtn = document.getElementById('confirmStopOngoingBtn');
            if (!confirmStopOngoingBtn) return;

            const classRoomId = confirmStopOngoingBtn.getAttribute('data-class-room-id');

            fetch(`/api/class-rooms/${classRoomId}/deactivate-ongoing`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('stopOngoingModal'));
                    if (modal) modal.hide();
                    showAlert('Class session stopped successfully!', 'success');
                    loadClassRooms();
                } else {
                    throw new Error(data.message || 'Failed to stop class session');
                }
            })
            .catch(error => {
                console.error('Error stopping class session:', error);
                showAlert('Error stopping class session: ' + error.message, 'danger');
            });
        }

        function viewClassRoom(classRoomId) {
            window.location.href = `/class-rooms/${classRoomId}`;
        }

        function editClassRoom(classRoomId) {
            window.location.href = `/class-rooms/${classRoomId}/edit`;
        }

        // Helper functions
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

        function exportTo(format) {
            showAlert(`Exporting to ${format.toUpperCase()} format...`, 'info');
        }
    </script>
@endpush