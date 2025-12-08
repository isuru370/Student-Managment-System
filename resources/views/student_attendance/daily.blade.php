@extends('layouts.app')

@section('title', 'Today\'s Classes')
@section('page-title', 'Today\'s Classes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student_attendance.index') }}">Mark Attendance</a></li>
    <li class="breadcrumb-item active">Today's Classes</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Success/Error Messages -->
                <div id="messages"></div>

                <!-- Page Header Card -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-primary bg-gradient text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-calendar-day me-2"></i>Today's Classes
                                </h5>
                                <small class="opacity-75">Classes scheduled for today</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark fs-6 px-3 py-2 me-3" id="currentDate">
                                    <i class="fas fa-calendar me-1"></i>
                                    <span id="dateText"></span>
                                </span>
                                <button class="btn btn-light btn-sm shadow-sm" id="refreshBtn">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div class="text-center py-5" id="loadingSpinner">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading today's classes...</p>
                </div>

                <!-- No Classes State -->
                <div class="card border-0 shadow-sm" id="noClassesCard" style="display: none;">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-calendar-times fa-4x text-muted opacity-50"></i>
                        </div>
                        <h4 class="text-muted mb-3">No Classes Today</h4>
                        <p class="text-muted mb-4">There are no scheduled classes for today.</p>
                        <button class="btn btn-outline-primary" id="retryEmptyBtn">
                            <i class="fas fa-redo me-2"></i>Check Again
                        </button>
                    </div>
                </div>

                <!-- Error State -->
                <div class="card border-danger border-2" id="errorCard" style="display: none;">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle fa-4x text-danger opacity-75"></i>
                        </div>
                        <h4 class="text-danger mb-3">Error Loading Classes</h4>
                        <p class="text-muted mb-4" id="errorMessage"></p>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-primary" id="retryBtn">
                                <i class="fas fa-redo me-2"></i>Try Again
                            </button>
                            <button class="btn btn-outline-secondary" id="reportBtn">
                                <i class="fas fa-bug me-2"></i>Report Issue
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Classes List Container -->
                <div id="classesListContainer" style="display: none;">
                    <div class="row" id="classesList">
                        <!-- Classes will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .class-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
            border: none;
        }

        .class-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .class-header {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        .status-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 0.75rem;
            padding: 4px 10px;
        }

        .class-time {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }

        .detail-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .ongoing-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        .view-button {
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .view-button:hover {
            transform: translateY(-2px);
        }

        .class-status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .status-scheduled {
            background-color: #28a745;
        }

        .status-ongoing {
            background-color: #dc3545;
        }

        .status-completed {
            background-color: #6c757d;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get current local time
            const getCurrentTime = () => {
                return new Date();
            };

            // Parse AM/PM time to 24-hour format
            const parseTimeTo24Hour = (timeStr) => {
                if (!timeStr || typeof timeStr !== 'string') {
                    return null;
                }

                timeStr = timeStr.trim().toUpperCase();

                let period = '';
                let timePart = timeStr;

                if (timeStr.includes(' AM')) {
                    period = 'AM';
                    timePart = timeStr.replace(' AM', '');
                } else if (timeStr.includes(' PM')) {
                    period = 'PM';
                    timePart = timeStr.replace(' PM', '');
                }

                const parts = timePart.split(':');
                if (parts.length < 1) return null;

                let hours = parseInt(parts[0], 10);
                const minutes = parts[1] ? parseInt(parts[1], 10) : 0;

                if (period === 'PM' && hours < 12) {
                    hours += 12;
                } else if (period === 'AM' && hours === 12) {
                    hours = 0;
                }

                return { hours, minutes };
            };

            // Create Date object with specific time
            const createDateTime = (dateStr, hour, minute) => {
                const date = new Date(dateStr);
                date.setHours(hour, minute, 0, 0);
                return date;
            };

            // Determine class status
            const determineClassStatus = (classItem) => {
                // Rule 1: If is_ongoing is 0, skip entirely
                if (classItem.is_ongoing === 0) {
                    return null;
                }

                const now = getCurrentTime();

                // Parse start and end times
                const startTime = parseTimeTo24Hour(classItem.start_time);
                const endTime = parseTimeTo24Hour(classItem.end_time);

                if (!startTime || !endTime) {
                    return 'unknown';
                }

                // Create Date objects
                const startDateTime = createDateTime(classItem.date, startTime.hours, startTime.minutes);
                const endDateTime = createDateTime(classItem.date, endTime.hours, endTime.minutes);

                // Handle cases where end time is earlier than start time (crosses midnight)
                if (endDateTime < startDateTime) {
                    endDateTime.setDate(endDateTime.getDate() + 1);
                }

                // Convert DB status to string for consistent comparison
                const dbStatus = classItem.status.toString();

                if (dbStatus === "0") {
                    if (now < startDateTime) {
                        return 'scheduled';
                    } else if (now >= startDateTime && now <= endDateTime) {
                        return 'live';
                    } else {
                        return 'completed';
                    }
                }

                if (dbStatus === "1") {
                    if (now >= startDateTime && now <= endDateTime) {
                        return 'live';
                    } else if (now > endDateTime) {
                        return 'completed';
                    } else {
                        return 'scheduled';
                    }
                }

                return 'unknown';
            };

            // UI Functions
            const showLoading = (show) => {
                const spinner = document.getElementById('loadingSpinner');
                if (spinner) {
                    spinner.style.display = show ? 'block' : 'none';
                }
            };

            const hideAllStates = () => {
                const elements = ['noClassesCard', 'errorCard', 'classesListContainer'];
                elements.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.style.display = 'none';
                    }
                });
            };

            const showNoClasses = () => {
                const element = document.getElementById('noClassesCard');
                if (element) {
                    element.style.display = 'block';
                }
            };

            const showError = (message) => {
                const errorCard = document.getElementById('errorCard');
                const errorMessage = document.getElementById('errorMessage');
                if (errorCard && errorMessage) {
                    errorMessage.textContent = message;
                    errorCard.style.display = 'block';
                }
            };

            const formatDisplayDate = (dateStr) => {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
            };

            const getStatusConfig = (status) => {
                const configs = {
                    'scheduled': {
                        text: 'Scheduled',
                        badgeClass: 'bg-success',
                        headerClass: 'bg-success',
                        showViewButton: false,
                        isLive: false
                    },
                    'live': {
                        text: 'Live Now',
                        badgeClass: 'bg-danger',
                        headerClass: 'bg-danger',
                        showViewButton: true,
                        isLive: true
                    },
                    'completed': {
                        text: 'Completed',
                        badgeClass: 'bg-secondary',
                        headerClass: 'bg-secondary',
                        showViewButton: true,
                        isLive: false
                    },
                    'unknown': {
                        text: 'Unknown',
                        badgeClass: 'bg-secondary',
                        headerClass: 'bg-secondary',
                        showViewButton: false,
                        isLive: false
                    }
                };

                return configs[status] || configs.unknown;
            };

            // Load classes from API
            const loadClasses = async () => {
                showLoading(true);
                hideAllStates();

                try {
                    const today = getCurrentTime().toLocaleDateString('en-CA');
                    const apiUrl = `/api/class-attendances/by-date?date=${today}`;

                    const response = await fetch(apiUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.status === true || data.status === 'success') {
                        displayClasses(data.data || []);
                    } else {
                        throw new Error(data.message || 'Failed to load classes');
                    }

                } catch (error) {
                    showError(error.message || 'Failed to load classes. Please try again.');
                } finally {
                    showLoading(false);
                }
            };

            // Display classes in the UI
            const displayClasses = (classesData) => {
                const classesList = document.getElementById('classesList');
                const container = document.getElementById('classesListContainer');

                if (!classesList || !container) return;

                // Process each class
                const validClasses = [];

                classesData.forEach((classItem) => {
                    const status = determineClassStatus(classItem);

                    if (status !== null) {
                        const config = getStatusConfig(status);
                        validClasses.push({ classItem, config });
                    }
                });

                if (validClasses.length === 0) {
                    showNoClasses();
                    return;
                }

                // Generate HTML
                let html = '';

                validClasses.forEach(({ classItem, config }) => {
                    // Extract IDs from the class item
                    const attendanceId = classItem.attendance_id;
                    const classCategoryStudentClassId = classItem.classCategoryStudentClass?.class_category_student_class_id;
                    const classId = classItem.class_details?.class_id;
                    const statusText = config.text;

                    html += `
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card class-card border-0 shadow-sm h-100">
                            <div class="class-header ${config.headerClass} text-white p-4 position-relative">
                                ${config.isLive ? `
                                    <span class="status-badge badge bg-white text-dark ongoing-pulse">
                                        <i class="fas fa-circle fa-xs me-1"></i> LIVE NOW
                                    </span>
                                ` : ''}

                                <div class="mb-3">
                                    <h6 class="card-title mb-1 fw-bold">
                                        ${classItem.class_details?.class_name || 'Unnamed Class'}
                                    </h6>
                                    <p class="mb-0 opacity-75">
                                        ${classItem.class_details?.subject_name || ''}
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="class-time">
                                        <i class="fas fa-clock me-2"></i>
                                        ${classItem.start_time} - ${classItem.end_time}
                                    </div>
                                    <span class="badge ${config.badgeClass} px-3 py-2">
                                        ${config.text}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <div class="detail-item">
                                    <div class="detail-icon bg-primary bg-opacity-10">
                                        <i class="fas fa-chalkboard-teacher text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Teacher</small>
                                        <span class="fw-semibold text-truncate-2">
                                            ${classItem.class_details?.teacher_name || 'Not assigned'}
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon bg-info bg-opacity-10">
                                        <i class="fas fa-graduation-cap text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Grade & Category</small>
                                        <span class="fw-semibold">
                                            ${classItem.class_details?.grade_name || ''} 
                                            ${classItem.class_details?.category_name ? `• ${classItem.class_details.category_name}` : ''}
                                        </span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon bg-warning bg-opacity-10">
                                        <i class="fas fa-door-open text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Class Hall</small>
                                        <span class="fw-semibold">${classItem.class_hall || 'Not assigned'}</span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon bg-secondary bg-opacity-10">
                                        <i class="fas fa-calendar-day text-secondary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Day & Date</small>
                                        <span class="fw-semibold">
                                            ${classItem.day_of_week}, ${formatDisplayDate(classItem.date)}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-grid">
                                        ${config.showViewButton ? `
                                            <button class="btn btn-primary view-button" 
                                                data-attendance-id="${attendanceId}"
                                                data-class-category-id="${classCategoryStudentClassId}"
                                                data-class-id="${classId}">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </button>
                                        ` : `
                                            <div class="alert alert-info alert-sm mb-0 text-center py-2">
                                                <i class="fas fa-info-circle me-2"></i>
                                                This class is ${statusText.toLowerCase()}
                                            </div>
                                        `}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                });

                classesList.innerHTML = html;
                container.style.display = 'block';

                // Add click handlers to view buttons
                document.querySelectorAll('.view-button').forEach(button => {
                    button.addEventListener('click', function () {
                        const attendanceId = this.getAttribute('data-attendance-id');
                        const classCategoryId = this.getAttribute('data-class-category-id');
                        const classId = this.getAttribute('data-class-id');

                        // Construct the URL with all three parameters
                        window.location.href = `/student_attendance/${classId}/${attendanceId}/${classCategoryId}/details`;
                    });
                });
            };

            // Set current date in header
            const setCurrentDate = () => {
                const dateElement = document.getElementById('dateText');
                if (dateElement) {
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const formattedDate = getCurrentTime().toLocaleDateString('en-US', options);
                    dateElement.textContent = formattedDate;
                }
            };

            // Setup event listeners
            const setupEventListeners = () => {
                const refreshBtn = document.getElementById('refreshBtn');
                const retryBtn = document.getElementById('retryBtn');
                const retryEmptyBtn = document.getElementById('retryEmptyBtn');
                const reportBtn = document.getElementById('reportBtn');

                if (refreshBtn) {
                    refreshBtn.addEventListener('click', loadClasses);
                }

                if (retryBtn) {
                    retryBtn.addEventListener('click', loadClasses);
                }

                if (retryEmptyBtn) {
                    retryEmptyBtn.addEventListener('click', loadClasses);
                }

                if (reportBtn) {
                    reportBtn.addEventListener('click', () => {
                        alert('Issue reported to administrators');
                    });
                }
            };

            // Initialize application
            const initialize = () => {
                setCurrentDate();
                setupEventListeners();
                loadClasses();
            };

            // Start the application
            initialize();
        });
    </script>
@endpush