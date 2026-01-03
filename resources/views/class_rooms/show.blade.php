@extends('layouts.app')

@section('title', 'Class Details')
@section('page-title', 'Class Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('class_rooms.index') }}">Class Rooms</a></li>
    <li class="breadcrumb-item active">Class Details</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Class Details
                        </h5>
                        <button class="btn btn-light btn-sm" onclick="loadClassDetails()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Class Information -->
                    <div class="row">
                        <div class="col-md-8">
                            <div id="classDetails">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Loading class information...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="mb-4">
                                        <div class="icon-container bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block">
                                            <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                                        </div>
                                        <h5 class="mt-3 mb-3">Quick Actions</h5>
                                        <p class="text-muted small">Manage your class efficiently</p>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-hover" onclick="loadClassDetails()">
                                            <i class="fas fa-sync-alt me-2"></i>Refresh Data
                                        </button>
                                        <a href="{{ route('class_rooms.index') }}" class="btn btn-outline-secondary btn-hover">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Classes
                                        </a>
                                        
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics Card -->
                            <div class="card border-0 shadow-sm mt-4">
                                <div class="card-body p-4">
                                    <h6 class="card-title mb-3 text-center">
                                        <i class="fas fa-chart-bar me-2 text-info"></i>Class Stats
                                    </h6>
                                    <div class="stats-container">
                                        <div class="stat-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <span class="text-muted">Status</span>
                                            <span id="statusBadge" class="badge bg-success">Active</span>
                                        </div>
                                        <div class="stat-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <span class="text-muted">Progress</span>
                                            <span id="progressBadge" class="badge bg-info">Ongoing</span>
                                        </div>
                                        <div class="stat-item d-flex justify-content-between align-items-center py-2">
                                            <span class="text-muted">Last Updated</span>
                                            <small id="lastUpdated" class="text-muted">Just now</small>
                                        </div>
                                    </div>
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
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
        }

        .info-card .card-body {
            position: relative;
            z-index: 1;
            padding: 2rem;
        }

        .detail-item {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }

        .detail-item:hover {
            background: rgba(255, 255, 255, 0.05);
            margin: 0 -1rem;
            padding: 1rem;
            border-radius: 8px;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .badge-lg {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .icon-container {
            transition: all 0.3s ease;
        }

        .icon-container:hover {
            transform: scale(1.1);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .icon-container:hover i {
            color: white !important;
        }

        .btn-hover {
            transition: all 0.3s ease;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stats-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1rem;
        }

        .stat-item {
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .teacher-card, .subject-card, .grade-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .teacher-card {
            border-left-color: #28a745;
        }

        .subject-card {
            border-left-color: #17a2b8;
        }

        .grade-card {
            border-left-color: #ffc107;
        }

        .teacher-card:hover, .subject-card:hover, .grade-card:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
        }

        .floating-icon {
            position: absolute;
            top: -20px;
            right: 20px;
            background: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script>
        const classId = {{ $id }};
        const baseUrl = '{{ config('app.url') }}';

        document.addEventListener('DOMContentLoaded', function() {
            loadClassDetails();
        });

        // Load Class Details
        function loadClassDetails() {
            const classDetailsDiv = document.getElementById('classDetails');
            
            // Show loading state
            classDetailsDiv.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading class information...</p>
                </div>
            `;

            fetch(`/api/class-rooms/${classId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success' && data.data) {
                        renderClassDetails(data.data);
                        updateStats(data.data);
                    } else {
                        throw new Error('Invalid response format');
                    }
                })
                .catch(error => {
                    console.error('Error loading class details:', error);
                    classDetailsDiv.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error!</strong> Unable to load class details: ${error.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                });
        }

        // Render Class Details
        function renderClassDetails(classData) {
            const classDetailsDiv = document.getElementById('classDetails');
            
            const statusBadge = classData.is_active === 1 ? 
                '<span class="badge bg-success badge-lg"><i class="fas fa-check-circle me-1"></i>Active</span>' :
                '<span class="badge bg-danger badge-lg"><i class="fas fa-times-circle me-1"></i>Inactive</span>';

            const ongoingBadge = classData.is_ongoing === 1 ?
                '<span class="badge bg-info badge-lg"><i class="fas fa-play-circle me-1"></i>Ongoing</span>' :
                '<span class="badge bg-warning badge-lg"><i class="fas fa-pause-circle me-1"></i>Not Ongoing</span>';

            classDetailsDiv.innerHTML = `
                <div class="row">
                    <!-- Main Class Info -->
                    <div class="col-12 mb-4">
                        <div class="card info-card">
                            <div class="card-body">
                                <div class="floating-icon">
                                    <i class="fas fa-chalkboard text-primary fa-2x"></i>
                                </div>
                                <h2 class="card-title mb-3">
                                    ${classData.class_name || 'Unnamed Class'}
                                </h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <strong><i class="fas fa-fingerprint me-2"></i>Class ID:</strong>
                                            <span class="float-end fw-bold">#${classData.id}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong><i class="fas fa-toggle-on me-2"></i>Status:</strong>
                                            <span class="float-end">${statusBadge}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <strong><i class="fas fa-running me-2"></i>Progress:</strong>
                                            <span class="float-end">${ongoingBadge}</span>
                                        </div>
                                        <div class="detail-item">
                                            <strong><i class="fas fa-calendar-plus me-2"></i>Created:</strong>
                                            <span class="float-end">${new Date(classData.created_at).toLocaleDateString()}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Details Cards -->
                    <div class="col-md-4 mb-3">
                        <div class="card teacher-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user-tie fa-2x text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="card-title mb-1 text-muted">Teacher</h6>
                                        <h5 class="mb-0 text-success">${classData.teacher ? classData.teacher.fname + ' ' + classData.teacher.lname : 'No Teacher'}</h5>
                                        <small class="text-muted">${classData.teacher ? 'ID: ' + classData.teacher.custom_id : ''}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card subject-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-book fa-2x text-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="card-title mb-1 text-muted">Subject</h6>
                                        <h5 class="mb-0 text-info">${classData.subject ? classData.subject.subject_name : 'No Subject'}</h5>
                                        <small class="text-muted">${classData.subject ? 'Subject Details' : ''}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card grade-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-graduation-cap fa-2x text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="card-title mb-1 text-muted">Grade</h6>
                                        <h5 class="mb-0 text-warning">${classData.grade ? 'Grade ' + classData.grade.grade_name : 'No Grade'}</h5>
                                        <small class="text-muted">${classData.grade ? 'Grade Level' : ''}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Update Statistics
        function updateStats(classData) {
            const statusBadge = document.getElementById('statusBadge');
            const progressBadge = document.getElementById('progressBadge');
            const lastUpdated = document.getElementById('lastUpdated');

            if (statusBadge) {
                statusBadge.className = classData.is_active === 1 ? 
                    'badge bg-success' : 'badge bg-danger';
                statusBadge.textContent = classData.is_active === 1 ? 'Active' : 'Inactive';
            }

            if (progressBadge) {
                progressBadge.className = classData.is_ongoing === 1 ?
                    'badge bg-info' : 'badge bg-warning';
                progressBadge.textContent = classData.is_ongoing === 1 ? 'Ongoing' : 'Not Ongoing';
            }

            if (lastUpdated) {
                lastUpdated.textContent = 'Just now';
            }
        }

        // View Class Attendance
        function viewClassAttendance() {
            showAlert('Attendance feature will be implemented soon!', 'info');
        }

        // Show alert function
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const container = document.querySelector('.card-body');
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