@extends('layouts.app')

@section('title', 'View Teacher')
@section('page-title', 'Teacher Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active">View Teacher</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Teacher Details</h5>
                            <p class="text-muted mb-0">View complete teacher information</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Teachers
                            </a>
                            <button onclick="window.location.href='{{ route('teachers.edit', $id) }}'"
                                class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Teacher
                            </button>
                            <button onclick="window.location.href='{{ route('teachers.classes', $id) }}'"
                                class="btn btn-outline-primary">
                                <i class="fas fa-chalkboard me-2"></i>View Classes
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading teacher details...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger d-none" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="errorText"></span>
                    </div>

                    <!-- Teacher Details -->
                    <div id="teacherDetails" class="d-none">
                        <div class="row">
                            <!-- Teacher Profile Header -->
                            <div class="col-12">
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-2 text-center">
                                                <div class="avatar-lg bg-primary bg-gradient rounded-circle text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                                                    style="width: 80px; height: 80px;">
                                                    <span class="fw-bold fs-4" id="teacherInitials">TM</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h3 class="mb-1" id="teacherName">Teacher Name</h3>
                                                <p class="text-muted mb-1" id="teacherId">ID: </p>
                                                <p class="text-muted mb-0" id="teacherEmail">Email: </p>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <span class="badge bg-success fs-6 p-2" id="statusBadge">Active</span>
                                                <div class="mt-2">
                                                    <small class="text-muted" id="lastUpdated">Last updated: </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Full Name:</div>
                                            <div class="col-sm-8" id="fullName">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Email:</div>
                                            <div class="col-sm-8" id="displayEmail">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Mobile:</div>
                                            <div class="col-sm-8" id="displayMobile">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">NIC:</div>
                                            <div class="col-sm-8" id="displayNic">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Birth Date:</div>
                                            <div class="col-sm-8" id="displayBday">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Gender:</div>
                                            <div class="col-sm-8" id="displayGender">-</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4 fw-bold text-muted">Status:</div>
                                            <div class="col-sm-8" id="displayStatus">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-home me-2"></i>Address Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Address Line 1:</div>
                                            <div class="col-sm-8" id="displayAddress1">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Address Line 2:</div>
                                            <div class="col-sm-8" id="displayAddress2">-</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4 fw-bold text-muted">Address Line 3:</div>
                                            <div class="col-sm-8" id="displayAddress3">-</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Information -->
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Professional Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Graduation:</div>
                                            <div class="col-sm-8" id="displayGraduation">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold text-muted">Experience:</div>
                                            <div class="col-sm-8" id="displayExperience">-</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4 fw-bold text-muted">Percentage:</div>
                                            <div class="col-sm-8" id="displayPercentage">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="mb-0"><i class="fas fa-university me-2"></i>Bank Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="row mb-3">
                                                    <div class="col-sm-5 fw-bold text-muted">Bank:</div>
                                                    <div class="col-sm-7" id="displayBank">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row mb-3">
                                                    <div class="col-sm-5 fw-bold text-muted">Branch:</div>
                                                    <div class="col-sm-7" id="displayBranch">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row mb-3">
                                                    <div class="col-sm-5 fw-bold text-muted">Account No:</div>
                                                    <div class="col-sm-7" id="displayAccountNumber">-</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Timeline</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row mb-2">
                                                    <div class="col-sm-5 fw-bold text-muted">Created At:</div>
                                                    <div class="col-sm-7" id="displayCreatedAt">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row mb-2">
                                                    <div class="col-sm-5 fw-bold text-muted">Updated At:</div>
                                                    <div class="col-sm-7" id="displayUpdatedAt">-</div>
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
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .custom-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .avatar-lg {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
            border-radius: 10px;
        }

        .fw-bold.text-muted {
            color: #6c757d !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Global variable to store teacher ID
        let currentTeacherId = {{ $id }};

        // Wait for the DOM to be loaded
        document.addEventListener('DOMContentLoaded', function () {
            loadTeacherDetails();
        });

        function loadTeacherDetails() {
            showLoading();

            fetch(`/api/teachers/${currentTeacherId}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        displayTeacherDetails(data.data);
                    } else {
                        throw new Error(data.message || 'Failed to load teacher details');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showError('Error loading teacher details: ' + error.message);
                });
        }

        function displayTeacherDetails(teacher) {
            // Teacher Profile Header
            document.getElementById('teacherInitials').textContent = getInitials(teacher.fname, teacher.lname);
            document.getElementById('teacherName').textContent = `${teacher.fname} ${teacher.lname}`;
            document.getElementById('teacherId').textContent = `ID: ${teacher.custom_id || 'N/A'}`;
            document.getElementById('teacherEmail').textContent = `Email: ${teacher.email}`;

            // Status badge
            const statusBadge = document.getElementById('statusBadge');
            if (teacher.is_active) {
                statusBadge.className = 'badge bg-success fs-6 p-2';
                statusBadge.textContent = 'Active';
            } else {
                statusBadge.className = 'badge bg-secondary fs-6 p-2';
                statusBadge.textContent = 'Inactive';
            }

            // Last updated
            document.getElementById('lastUpdated').textContent = `Last updated: ${formatDate(teacher.updated_at)}`;

            // Personal Information
            document.getElementById('fullName').textContent = `${teacher.fname} ${teacher.lname}`;
            document.getElementById('displayEmail').textContent = teacher.email || '-';
            document.getElementById('displayMobile').textContent = teacher.mobile || '-';
            document.getElementById('displayNic').textContent = teacher.nic || '-';
            document.getElementById('displayBday').textContent = teacher.bday ? formatDate(teacher.bday) : '-';
            document.getElementById('displayGender').textContent = teacher.gender ? capitalizeFirst(teacher.gender) : '-';
            document.getElementById('displayStatus').textContent = teacher.is_active ? 'Active' : 'Inactive';

            // Address Information
            document.getElementById('displayAddress1').textContent = teacher.address1 || '-';
            document.getElementById('displayAddress2').textContent = teacher.address2 || '-';
            document.getElementById('displayAddress3').textContent = teacher.address3 || '-';

            // Professional Information
            document.getElementById('displayGraduation').textContent = teacher.graduation_details || '-';
            document.getElementById('displayExperience').textContent = teacher.experience ? `${teacher.experience} years` : '-';
            document.getElementById('displayPercentage').textContent = teacher.precentage ? `${teacher.precentage}%` : '-';

            // Bank Information
            document.getElementById('displayBank').textContent = teacher.bank_branch && teacher.bank_branch.bank ?
                `${teacher.bank_branch.bank.bank_name} (${teacher.bank_branch.bank.bank_code})` : '-';
            document.getElementById('displayBranch').textContent = teacher.bank_branch ?
                `${teacher.bank_branch.branch_name} (${teacher.bank_branch.branch_code})` : '-';
            document.getElementById('displayAccountNumber').textContent = teacher.account_number || '-';

            // Timeline
            document.getElementById('displayCreatedAt').textContent = formatDateTime(teacher.created_at);
            document.getElementById('displayUpdatedAt').textContent = formatDateTime(teacher.updated_at);

            // Show the details section
            document.getElementById('teacherDetails').classList.remove('d-none');
        }

        // Helper functions
        function getInitials(firstName, lastName) {
            return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB');
        }

        function formatDateTime(dateTimeString) {
            if (!dateTimeString) return '-';
            const date = new Date(dateTimeString);
            return date.toLocaleString('en-GB');
        }

        function capitalizeFirst(string) {
            if (!string) return '-';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('d-none');
            document.getElementById('teacherDetails').classList.add('d-none');
            document.getElementById('errorMessage').classList.add('d-none');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('d-none');
        }

        function showError(message) {
            document.getElementById('errorText').textContent = message;
            document.getElementById('errorMessage').classList.remove('d-none');
        }
    </script>
@endpush