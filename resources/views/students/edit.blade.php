@extends('layouts.app')

@section('title', 'Edit Student')
@section('page-title', 'Edit Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Edit Student</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-user-edit me-2"></i>Edit Student</strong>
                </div>
                <div class="card-body">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading student data...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h4 class="text-danger">Failed to Load Student Data</h4>
                        <p class="text-muted" id="errorText"></p>
                        <a href="{{ route('students.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Students
                        </a>
                    </div>

                    <!-- Student Edit Form -->
                    <form id="studentEditForm" style="display: none;">
                        <!-- Image Upload Section -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <strong>Student Image</strong>
                                    </div>
                                    <div class="card-body">
                                        <!-- Image Preview -->
                                        <div class="text-center mb-3">
                                            <img id="studentImagePreview" class="img-thumbnail rounded-circle"
                                                style="width: 200px; height: 200px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='http://127.0.0.1:8000/uploads/logo/logo.png'">
                                            <div id="imagePlaceholder" class="text-muted p-4 border rounded"
                                                style="display: none;">
                                                <i class="fas fa-user fa-3x mb-3"></i>
                                                <p class="mb-0">Student image will appear here</p>
                                            </div>
                                        </div>

                                        <!-- Image Upload Tabs -->
                                        <ul class="nav nav-tabs" id="imageUploadTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="camera-tab" data-bs-toggle="tab"
                                                    data-bs-target="#camera" type="button" role="tab">
                                                    <i class="fas fa-camera me-1"></i>Camera
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="upload-tab" data-bs-toggle="tab"
                                                    data-bs-target="#upload" type="button" role="tab">
                                                    <i class="fas fa-upload me-1"></i>Upload
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="quick-image-tab" data-bs-toggle="tab"
                                                    data-bs-target="#quick-image" type="button" role="tab">
                                                    <i class="fas fa-bolt me-1"></i>Quick Image
                                                </button>
                                            </li>
                                        </ul>

                                        <!-- Tab Content -->
                                        <div class="tab-content p-3 border border-top-0" id="imageUploadTabsContent">
                                            <!-- Camera Tab -->
                                            <div class="tab-pane fade show active" id="camera" role="tabpanel">
                                                <div id="cameraWrapper" style="display: none">
                                                    <video id="cameraView" width="100%" autoplay muted
                                                        class="rounded border" style="max-height: 200px;"></video>
                                                    <div class="d-flex gap-2 mt-2">
                                                        <button class="btn btn-success flex-fill" type="button"
                                                            id="captureBtn">
                                                            <i class="fas fa-camera me-2"></i>Capture
                                                        </button>
                                                        <button class="btn btn-secondary" type="button" id="closeCameraBtn">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <button class="btn btn-outline-primary w-100" type="button"
                                                    id="openCameraBtn">
                                                    <i class="fas fa-camera me-2"></i>Enable Camera
                                                </button>
                                                <p id="cameraError" class="text-danger mt-2 small" style="display: none">
                                                </p>
                                            </div>

                                            <!-- File Upload Tab -->
                                            <div class="tab-pane fade" id="upload" role="tabpanel">
                                                <div class="file-upload-area border rounded p-3 text-center bg-light">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                                    <p class="text-muted mb-2">Click to browse or drag & drop</p>
                                                    <input type="file" id="fileInput" accept="image/*" class="d-none">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="document.getElementById('fileInput').click()">
                                                        <i class="fas fa-folder-open me-2"></i>Browse Files
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Quick Image Tab -->
                                            <div class="tab-pane fade" id="quick-image" role="tabpanel">
                                                <div class="mb-3">
                                                    <label class="form-label">Search Quick Image by Custom ID</label>
                                                    <div class="input-group">
                                                        <input type="text" id="quickImageSearch" class="form-control"
                                                            placeholder="Enter custom ID...">
                                                        <button class="btn btn-outline-primary" type="button"
                                                            id="searchQuickImage">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="quickImageResults" class="mt-3">
                                                    <!-- Quick images will be displayed here -->
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Selected Image Info -->
                                        <div id="selectedImageInfo" class="mt-3 p-2 bg-light rounded" style="display: none">
                                            <small class="text-muted" id="imageSource"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Details Form -->
                            <div class="col-md-8">
                                <div class="row">
                                    <!-- Student ID Display (Read-only) -->
                                    <div class="col-12 mb-4">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <strong class="text-muted">Student ID:</strong>
                                                        <h5 class="text-primary mb-0" id="displayCustomId">Loading...</h5>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <small class="text-muted">This ID cannot be changed</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="col-12 card-header bg-primary text-white">
                                        <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="fname" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="lname" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                        <input type="text" name="mobile" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">WhatsApp Mobile</label>
                                        <input type="text" name="whatsapp_mobile" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIC</label>
                                        <input type="text" name="nic" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Birthday <span class="text-danger">*</span></label>
                                        <input type="date" name="bday" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" class="form-select" required>
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Address Information -->
                                    <div class="col-12 mt-4 card-header bg-success text-white">
                                        <h5 class="border-bottom pb-2 mb-3">Address Information</h5>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                        <input type="text" name="address1" class="form-control" required>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Address Line 2</label>
                                        <input type="text" name="address2" class="form-control">
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Address Line 3</label>
                                        <input type="text" name="address3" class="form-control">
                                    </div>

                                    <!-- Guardian Information -->
                                    <div class="col-12 mt-4 card-header bg-info text-white">
                                        <h5 class="border-bottom pb-2 mb-3">Guardian Information</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="guardian_fname" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian Last Name</label>
                                        <input type="text" name="guardian_lname" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian Mobile <span class="text-danger">*</span></label>
                                        <input type="text" name="guardian_mobile" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guardian NIC</label>
                                        <input type="text" name="guardian_nic" class="form-control">
                                    </div>

                                    <!-- Academic Information -->
                                    <div class="col-12 mt-4 card-header bg-warning text-dark">
                                        <h5 class="border-bottom pb-2 mb-3">Academic Information</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Grade <span class="text-danger">*</span></label>
                                        <select name="grade_id" class="form-select" required>
                                            <option value="">Select Grade</option>
                                            <!-- Grades will be populated via JavaScript -->
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">School</label>
                                        <input type="text" name="student_school" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Admission</label>
                                        <select name="admission" class="form-select">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Free Card</label>
                                        <select name="is_freecard" class="form-select">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 mt-4">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('students.index') }}"
                                                class="btn btn-secondary btn-lg flex-fill">
                                                <i class="fas fa-arrow-left me-2"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-success btn-lg flex-fill" id="submitBtn">
                                                <i class="fas fa-save me-2"></i>Update Student
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .file-upload-area {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px dashed #dee2e6;
        }

        .file-upload-area:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .quick-image-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .quick-image-item:hover {
            border-color: #0d6efd;
            transform: scale(1.02);
        }

        .quick-image-item.selected {
            border-color: #198754;
            background-color: #f8fff9;
        }

        .nav-tabs .nav-link {
            font-size: 0.85rem;
        }

        .tab-content {
            min-height: 200px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let studentImageUrl = null;
        let cameraStream = null;
        let selectedQuickImageId = null;
        let currentCustomId = '';

        // ================= INITIALIZATION =================
        document.addEventListener('DOMContentLoaded', function () {
            // Get custom_id from URL: http://127.0.0.1:8000/students/SA10095/edit
            const pathSegments = window.location.pathname.split('/');
            currentCustomId = pathSegments[pathSegments.length - 2]; // Get SA10095 from URL

            console.log('Loading student with custom_id:', currentCustomId);
            loadStudentData(currentCustomId);
            loadGrades();
            initializeEventListeners();
        });

        // ================= LOAD STUDENT DATA =================
        async function loadStudentData(customId) {
            try {
                showLoadingState();

                console.log('🔍 Fetching student data for custom_id:', customId);

                const response = await fetch(`/api/students/search/${customId}`);
                console.log('📡 API Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('📦 Full API Response:', result);

                if (result.status === 'success' && result.data) {
                    console.log('✅ Student data received:', result.data);
                    populateForm(result.data);
                    showContentState();
                    showAlert('Student data loaded successfully', 'success');
                } else {
                    throw new Error(result.message || 'Student not found');
                }
            } catch (error) {
                console.error('❌ Error loading student data:', error);
                showErrorState('Failed to load student data: ' + error.message);
            }
        }


        // ================= POPULATE FORM =================
        function populateForm(student) {
            try {
                console.log('🎯 Populating form with student data:', student);

                if (!student) {
                    console.error('❌ Student data is null or undefined');
                    return;
                }

                // Display Student ID (read-only)
                const displayCustomId = document.getElementById('displayCustomId');
                if (displayCustomId) {
                    displayCustomId.textContent = student.custom_id || 'N/A';
                    console.log('📝 Student ID:', student.custom_id);
                }

                // Set image
                if (student.img_url) {
                    studentImageUrl = student.img_url;
                    const studentPhoto = document.getElementById('studentImagePreview');
                    if (studentPhoto) {
                        studentPhoto.src = student.img_url;
                        studentPhoto.style.display = 'block';
                        document.getElementById('imagePlaceholder').style.display = 'none';
                        console.log('🖼️ Image URL set:', student.img_url);
                    }
                } else {
                    console.log('❌ No image URL found');
                }

                // Personal Information - Text inputs
                const fields = [
                    { name: 'fname', value: student.fname, label: 'First Name' },
                    { name: 'lname', value: student.lname, label: 'Last Name' },
                    { name: 'mobile', value: student.mobile, label: 'Mobile' },
                    { name: 'whatsapp_mobile', value: student.whatsapp_mobile, label: 'WhatsApp' },
                    { name: 'email', value: student.email, label: 'Email' },
                    { name: 'nic', value: student.nic, label: 'NIC' },
                    { name: 'address1', value: student.address1, label: 'Address1' },
                    { name: 'address2', value: student.address2, label: 'Address2' },
                    { name: 'address3', value: student.address3, label: 'Address3' },
                    { name: 'guardian_fname', value: student.guardian_fname, label: 'Guardian First Name' },
                    { name: 'guardian_lname', value: student.guardian_lname, label: 'Guardian Last Name' },
                    { name: 'guardian_mobile', value: student.guardian_mobile, label: 'Guardian Mobile' },
                    { name: 'guardian_nic', value: student.guardian_nic, label: 'Guardian NIC' },
                    { name: 'student_school', value: student.student_school, label: 'School' }
                ];

                // Populate text inputs
                fields.forEach(field => {
                    const input = document.querySelector(`input[name="${field.name}"]`);
                    if (input) {
                        input.value = field.value || '';
                        console.log(`✅ ${field.label}:`, field.value, '-> Input value:', input.value);
                    } else {
                        console.error(`❌ Input field not found: ${field.name}`);
                    }
                });

                // Fix birthday field - Convert DD/MM/YYYY to YYYY-MM-DD
                const bdayInput = document.querySelector('input[name="bday"]');
                if (bdayInput) {
                    if (student.bday) {
                        let formattedDate = '';

                        // Check for YYYY-MM-DD format (2025-11-01)
                        if (student.bday.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            formattedDate = student.bday;
                            console.log('🎂 Birthday (YYYY-MM-DD format):', student.bday, '-> Using as is:', formattedDate);
                        }
                        // Check for DD/MM/YYYY format (08/12/2025)
                        else if (student.bday.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                            const [day, month, year] = student.bday.split('/');
                            formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                            console.log('🎂 Birthday (DD/MM/YYYY format):', student.bday, '-> Converted to:', formattedDate);
                        }
                        // Try to parse any other date format
                        else {
                            const dateObj = new Date(student.bday);
                            if (!isNaN(dateObj.getTime())) {
                                formattedDate = dateObj.toISOString().split('T')[0];
                                console.log('🎂 Birthday (other format):', student.bday, '-> Converted to:', formattedDate);
                            } else {
                                console.log('❌ Invalid birthday format:', student.bday);
                                formattedDate = '';
                            }
                        }

                        bdayInput.value = formattedDate;
                    } else {
                        console.log('❌ No birthday data');
                        bdayInput.value = '';
                    }
                }

                // Fix gender field - Convert "Male" to "male"
                const genderSelect = document.querySelector('select[name="gender"]');
                if (genderSelect) {
                    // Convert to lowercase to match option values
                    const genderValue = student.gender ? student.gender.toLowerCase() : '';
                    genderSelect.value = genderValue;
                    console.log('⚧ Gender:', student.gender, '-> Converted to:', genderValue, '-> Selected value:', genderSelect.value);
                }

                // Fix Grade field - Wait for grades to load then set value
                const gradeSelect = document.querySelector('select[name="grade_id"]');
                if (gradeSelect) {
                    // Check if grades are loaded, if not wait a bit
                    if (gradeSelect.options.length > 1) {
                        gradeSelect.value = student.grade_id || '';
                        console.log('📚 Grade ID:', student.grade_id, '-> Selected value:', gradeSelect.value);
                    } else {
                        console.log('⏳ Grades not loaded yet, waiting...');
                        // Wait for grades to load then set value
                        setTimeout(() => {
                            gradeSelect.value = student.grade_id || '';
                            console.log('📚 Grade ID (delayed):', student.grade_id, '-> Selected value:', gradeSelect.value);
                        }, 500);
                    }
                }

                const admissionSelect = document.querySelector('select[name="admission"]');
                if (admissionSelect) {
                    admissionSelect.value = student.admission ? '1' : '0';
                    console.log('🎫 Admission:', student.admission, '-> Selected value:', admissionSelect.value);
                }

                const freecardSelect = document.querySelector('select[name="is_freecard"]');
                if (freecardSelect) {
                    freecardSelect.value = student.is_freecard ? '1' : '0';
                    console.log('🎫 Free Card:', student.is_freecard, '-> Selected value:', freecardSelect.value);
                }

                console.log('✅ Form population completed successfully');

            } catch (error) {
                console.error('❌ Error in populateForm:', error);
                console.error('Error details:', error.message, error.stack);
            }
        }

        // ================= LOAD GRADES =================
        async function loadGrades() {
            try {
                const response = await fetch('/api/grades/dropdown');
                if (!response.ok) throw new Error('Failed to fetch grades');

                const res = await response.json();
                const data = res.data || res;

                const gradeSelect = document.querySelector('select[name="grade_id"]');
                let gradesHtml = '<option value="">Select Grade</option>';

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(g => {
                        gradesHtml += `<option value="${g.id}">Grade ${g.grade_name}</option>`;
                    });
                }
                gradeSelect.innerHTML = gradesHtml;
            } catch (e) {
                console.error('Error loading grades:', e);
            }
        }

        // ================= EVENT LISTENERS =================
        function initializeEventListeners() {
            // Camera functionality
            document.getElementById('openCameraBtn').addEventListener('click', openCamera);
            document.getElementById('closeCameraBtn').addEventListener('click', closeCamera);
            document.getElementById('captureBtn').addEventListener('click', captureImage);

            // File upload
            document.getElementById('fileInput').addEventListener('change', handleFileUpload);

            // Quick image search
            document.getElementById('searchQuickImage').addEventListener('click', searchQuickImages);

            // Form submission
            document.getElementById('studentEditForm').addEventListener('submit', handleFormSubmit);
        }

        // ================= CAMERA FUNCTIONS =================
        async function openCamera() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: 'environment'
                    }
                });

                const cameraView = document.getElementById('cameraView');
                cameraView.srcObject = cameraStream;

                document.getElementById('cameraWrapper').style.display = 'block';
                document.getElementById('openCameraBtn').style.display = 'none';
                document.getElementById('cameraError').style.display = 'none';

            } catch (e) {
                const cameraError = document.getElementById('cameraError');
                cameraError.innerText = 'Camera access denied or not available. Please check permissions.';
                cameraError.style.display = 'block';
                console.error('Camera error:', e);
            }
        }

        function closeCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            document.getElementById('cameraWrapper').style.display = 'none';
            document.getElementById('openCameraBtn').style.display = 'block';
        }

        function captureImage() {
            const video = document.getElementById('cameraView');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob(blob => {
                const file = new File([blob], "student_capture.jpg", { type: "image/jpeg" });
                uploadImage(file, 'camera');
                closeCamera();
            }, "image/jpeg", 0.8);
        }

        // ================= FILE UPLOAD =================
        function handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    showAlert('Please select a valid image file', 'danger');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('Image size should be less than 5MB', 'danger');
                    return;
                }
                uploadImage(file, 'file');
            }
        }

        // ================= QUICK IMAGE FUNCTIONS =================
        async function searchQuickImages() {
            const searchTerm = document.getElementById('quickImageSearch').value.trim();
            if (!searchTerm) {
                showAlert('Please enter a custom ID to search', 'warning');
                return;
            }

            try {
                const response = await fetch('/api/quick-photos/active');
                if (!response.ok) throw new Error('Failed to fetch quick images');

                const res = await response.json();
                const quickImages = res.data || res;

                const filteredImages = quickImages.filter(img =>
                    img.custom_id && img.custom_id.toLowerCase().includes(searchTerm.toLowerCase())
                );

                displayQuickImages(filteredImages);
            } catch (e) {
                console.error('Error searching quick images:', e);
                showAlert('Failed to search quick images', 'danger');
            }
        }

        function selectQuickImage(id, imageUrl, customId) {
            // Remove previous selection
            document.querySelectorAll('.quick-image-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Add selection to clicked item
            event.currentTarget.classList.add('selected');

            // Set student image
            studentImageUrl = imageUrl;
            selectedQuickImageId = id;

            // Update preview
            updateImagePreview(imageUrl, `Quick Image: ${customId}`);

            showAlert(`Quick image "${customId}" selected`, 'success');
        }

        // ================= IMAGE UPLOAD =================
        async function uploadImage(file, source) {
            try {
                showAlert('Uploading image...', 'info');

                const fd = new FormData();
                fd.append('image', file);

                const res = await fetch('/api/image-upload/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: fd
                });

                const data = await res.json();

                if (data.status === 'success') {
                    studentImageUrl = "{{ url('/uploads/images') }}/" + data.image_url;
                    updateImagePreview(studentImageUrl, `Uploaded via ${source}`);
                    showAlert('Image uploaded successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            } catch (e) {
                console.error('Upload error:', e);
                showAlert('Failed to upload image: ' + e.message, 'danger');
            }
        }


        function updateImagePreview(imageUrl, source) {
            const preview = document.getElementById('studentImagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const imageInfo = document.getElementById('selectedImageInfo');

            preview.src = imageUrl;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            imageInfo.style.display = 'block';
            document.getElementById('imageSource').textContent = source;
        }

        // ================= FORM SUBMISSION =================
        async function handleFormSubmit(e) {
            e.preventDefault();

            if (!studentImageUrl) {
                showAlert('Please upload a student image', 'warning');
                return;
            }

            const formData = new FormData(e.target);
            const studentData = {
                img_url: studentImageUrl,
                is_active: true
            };

            // Convert FormData to object with proper type conversion
            for (let [key, value] of formData.entries()) {
                if (value) {
                    // Convert string '0'/'1' to boolean for specific fields
                    if (['admission', 'is_freecard'].includes(key)) {
                        studentData[key] = value === '1';
                    } else {
                        studentData[key] = value;
                    }
                }
            }

            try {
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

                console.log('Updating student data:', studentData);

                // Update student using PUT request to correct API
                const studentResponse = await fetch(`/api/students/${currentCustomId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(studentData)
                });

                console.log('Response status:', studentResponse.status);

                const studentResult = await studentResponse.json();
                console.log('Response data:', studentResult);

                if (studentResult.status === 'success') {
                    // If quick image was used, deactivate it
                    if (selectedQuickImageId) {
                        await deactivateQuickImage(selectedQuickImageId);
                    }

                    showAlert('Student updated successfully!', 'success');

                    // Redirect back to students list after short delay
                    setTimeout(() => {
                        window.location.href = "{{ route('students.index') }}";
                    }, 1500);

                } else if (studentResult.status === 'error' && studentResult.errors) {
                    // Handle validation errors
                    const errorMessages = Object.values(studentResult.errors).flat().join(', ');
                    throw new Error('Validation failed: ' + errorMessages);
                } else {
                    throw new Error(studentResult.message || 'Update failed');
                }

            } catch (e) {
                console.error('Update error:', e);
                console.error('Full error details:', {
                    message: e.message,
                    studentData: studentData
                });
                showAlert('Failed to update student: ' + e.message, 'danger');
            } finally {
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Update Student';
            }
        }

        // ================= DEACTIVATE QUICK IMAGE =================
        async function deactivateQuickImage(quickImageId) {
            try {
                const response = await fetch(`/api/quick-photos/${quickImageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    console.warn('Failed to deactivate quick image, but student was updated');
                }
            } catch (e) {
                console.error('Error deactivating quick image:', e);
            }
        }

        // ================= UTILITY FUNCTIONS =================
        function showLoadingState() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('studentEditForm').style.display = 'none';
        }

        function showContentState() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('studentEditForm').style.display = 'block';
        }

        function showErrorState(message) {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentEditForm').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'block';
            document.getElementById('errorText').textContent = message;
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                                    <strong>${type === 'success' ? 'Success!' : type === 'warning' ? 'Warning!' : 'Error!'}</strong> ${message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                `;

            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function displayQuickImages(images) {
            const resultsContainer = document.getElementById('quickImageResults');

            if (images.length === 0) {
                resultsContainer.innerHTML = '<p class="text-muted text-center">No quick images found</p>';
                return;
            }

            resultsContainer.innerHTML = images.map(img => {
                const imageUrl = img.quick_img.startsWith('http') ?
                    img.quick_img :
                    "{{ url('/uploads/images') }}/" + img.quick_img;

                return `
                                        <div class="quick-image-item card mb-2 p-2" onclick="selectQuickImage(${img.id}, '${imageUrl}', '${img.custom_id || 'No ID'}')">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-3">
                                                    <img src="${imageUrl}" class="img-fluid rounded" style="height: 60px; object-fit: cover;">
                                                </div>
                                                <div class="col-9">
                                                    <small class="fw-bold">ID: ${img.custom_id || 'No ID'}</small><br>
                                                    <small class="text-muted">Grade: ${img.grade?.grade_name || 'N/A'}</small>
                                                </div>
                                            </div>
                                        </div>
                                    `;
            }).join('');
        }
    </script>
@endpush