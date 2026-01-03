@extends('layouts.app')

@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Show Student</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>Student Details
                    </h5>
                    <div class="no-print">
                        <button class="btn btn-light btn-sm me-2" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                        <button class="btn btn-warning btn-sm me-2" id="editStudentBtn">
                            <i class="fas fa-edit me-1"></i>Edit Student
                        </button>
                        <button class="btn btn-info btn-sm me-2" id="addClassBtn">
                            <i class="fas fa-plus-circle me-1"></i>Add Class
                        </button>
                        <button class="btn btn-success btn-sm" id="viewAnalyticBtn">
                            <i class="fa fa-line-chart me-1" aria-hidden="true"></i>View Analytic
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading student details...</p>
                    </div>

                    <!-- Student Details -->
                    <div id="studentDetails" style="display: none;">
                        <div class="row">
                            <!-- Student Photo & Basic Info -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-secondary text-white">
                                        <strong>Student Photo</strong>
                                    </div>
                                    <div class="card-body text-center">
                                        <img id="studentPhoto" class="img-thumbnail rounded-circle mb-3" 
                                             style="width: 200px; height: 200px; object-fit: cover;"
                                             onerror="this.onerror=null; this.src='/uploads/logo/logo.png'">
                                        <h4 id="studentName" class="mb-1"></h4>
                                        <h5 class="text-primary mb-2" id="studentId"></h5>
                                        <p class="text-muted mb-1" id="studentGrade"></p>
                                        <p class="text-muted mb-0" id="studentStatus"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <strong><i class="fas fa-user me-2"></i>Personal Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>First Name:</strong> <span id="fname"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Last Name:</strong> <span id="lname"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Mobile:</strong> <span id="mobile"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>WhatsApp:</strong> <span id="whatsapp_mobile"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Email:</strong> <span id="email"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>NIC:</strong> <span id="nic"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Birthday:</strong> <span id="bday"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Gender:</strong> <span id="gender"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <strong><i class="fas fa-home me-2"></i>Address Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <strong>Address Line 1:</strong> <span id="address1"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <strong>Address Line 2:</strong> <span id="address2"></span>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <strong>Address Line 3:</strong> <span id="address3"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <strong><i class="fas fa-users me-2"></i>Guardian Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>Guardian Name:</strong> <span id="guardianName"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Guardian Mobile:</strong> <span id="guardian_mobile"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Guardian NIC:</strong> <span id="guardian_nic"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <strong><i class="fas fa-graduation-cap me-2"></i>Academic Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>Grade:</strong> <span id="gradeName"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>School:</strong> <span id="student_school"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Admission Status:</strong> <span id="admissionStatus"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Free Card:</strong> <span id="freeCardStatus"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Registration Date:</strong> <span id="createdAt"></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Status:</strong> <span id="activeStatus"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h4 class="text-danger">Failed to Load Student Details</h4>
                        <p class="text-muted" id="errorText"></p>
                        <a href="{{ route('students.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingSpinner = document.getElementById('loadingSpinner');
        const studentDetails = document.getElementById('studentDetails');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const editStudentBtn = document.getElementById('editStudentBtn');
        const addClassBtn = document.getElementById('addClassBtn');
        const viewAnalyticBtn = document.getElementById('viewAnalyticBtn');

        let currentStudentId = '';
        let studentId = '';

        // Get custom_id from URL
        const pathSegments = window.location.pathname.split('/');
        const customId = pathSegments[pathSegments.length - 1];
        currentStudentId = customId;

        // Load student details automatically
        loadStudentDetails(customId);

        // Edit Student button click event
        editStudentBtn.addEventListener('click', function() {
            if (currentStudentId) {
                window.location.href = `/students/${currentStudentId}/edit`;
            } else {
                showAlert('Student ID not found', 'error');
            }
        });

        // Add Class button click event
        addClassBtn.addEventListener('click', function() {
            // TODO: Implement add class functionality
            //showAlert('Add Class functionality to be implemented', 'info');
            window.location.href = `/students/add_student_to_single_class/${studentId}`;
        });

        // View Class button click event
        viewAnalyticBtn.addEventListener('click', function() {
            // TODO: Implement class view navigation
            //showAlert('Class view functionality to be implemented', 'info');
            window.location.href = `/students/student_analytic/${studentId}`;
        });

        // Load student details function
        async function loadStudentDetails(customId) {
            try {
                loadingSpinner.style.display = 'block';
                studentDetails.style.display = 'none';
                errorMessage.style.display = 'none';

                const response = await fetch(`/api/students/search/${customId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.status === 'success' && result.data) {
                    displayStudentDetails(result.data);
                    studentId = result.data.id;
                    showAlert('Student details loaded successfully', 'success');
                } else {
                    throw new Error(result.message || 'Student not found');
                }
            } catch (error) {
                console.error('Error loading student details:', error);
                showError('Failed to load student details: ' + error.message);
            } finally {
                loadingSpinner.style.display = 'none';
            }
        }

        // Display student details
        function displayStudentDetails(student) {
            // Set image with fallback to logo
            const studentPhoto = document.getElementById('studentPhoto');
            if (student.img_url && isValidImageUrl(student.img_url)) {
                studentPhoto.src = student.img_url;
            } else {
                studentPhoto.src = '/uploads/logo/logo.png';
            }

            // Personal Information
            document.getElementById('studentName').textContent = `${student.fname} ${student.lname}`;
            document.getElementById('studentId').textContent = student.custom_id;
            document.getElementById('studentGrade').textContent = `Grade ${student.grade?.grade_name || 'N/A'}`;
            document.getElementById('studentStatus').textContent = student.is_active == 1 ? 'Active' : 'Inactive';
            
            document.getElementById('fname').textContent = student.fname || 'N/A';
            document.getElementById('lname').textContent = student.lname || 'N/A';
            document.getElementById('mobile').textContent = student.mobile || 'N/A';
            document.getElementById('whatsapp_mobile').textContent = student.whatsapp_mobile || 'N/A';
            document.getElementById('email').textContent = student.email || 'N/A';
            document.getElementById('nic').textContent = student.nic || 'N/A';
            document.getElementById('bday').textContent = student.bday || 'N/A';
            document.getElementById('gender').textContent = student.gender || 'N/A';

            // Address Information
            document.getElementById('address1').textContent = student.address1 || 'N/A';
            document.getElementById('address2').textContent = student.address2 || 'N/A';
            document.getElementById('address3').textContent = student.address3 || 'N/A';

            // Guardian Information
            document.getElementById('guardianName').textContent = `${student.guardian_fname || ''} ${student.guardian_lname || ''}`.trim() || 'N/A';
            document.getElementById('guardian_mobile').textContent = student.guardian_mobile || 'N/A';
            document.getElementById('guardian_nic').textContent = student.guardian_nic || 'N/A';

            // Academic Information
            document.getElementById('gradeName').textContent = `Grade ${student.grade?.grade_name || 'N/A'}`;
            document.getElementById('student_school').textContent = student.student_school || 'N/A';
            document.getElementById('admissionStatus').textContent = student.admission == 1 ? 'Yes' : 'No';
            document.getElementById('freeCardStatus').textContent = student.is_freecard == 1 ? 'Yes' : 'No';
            document.getElementById('createdAt').textContent = new Date(student.created_at).toLocaleDateString();
            document.getElementById('activeStatus').textContent = student.is_active == 1 ? 'Active' : 'Inactive';

            studentDetails.style.display = 'block';
        }

        // Check if image URL is valid
        function isValidImageUrl(url) {
            return url && 
                   typeof url === 'string' && 
                   url.length > 0 && 
                   !url.includes('undefined') && 
                   !url.includes('null') &&
                   (url.startsWith('http') || url.startsWith('/'));
        }

        // Show error message
        function showError(message) {
            errorText.textContent = message;
            errorMessage.style.display = 'block';
        }

        // Show alert message
        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show mt-3`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    });
</script>
@endpush