<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Success Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .capitalize-text {
            text-transform: capitalize;
        }

        .institution-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .info-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .mobile-stack {
                flex-direction: column;
            }

            .mobile-full {
                width: 100% !important;
            }
        }

        #alertContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Institution Header -->
    <div class="institution-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img src="/uploads/logo/logo.png" alt="Success Academy Logo"
                        class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;"
                        onerror="this.style.display='none'">
                </div>
                <div class="col">
                    <h1 class="mb-1">Success Academy</h1>
                    <p class="mb-0 opacity-75">Student Registration Portal</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <div class="container py-4">
        <!-- Important Information Section -->
        <div class="info-section">
            <div class="row">
                <div class="col-12">
                    <h3><i class="fas fa-info-circle me-2"></i>Important Information Before You Start</h3>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5><i class="fas fa-camera me-2"></i>About Photo Upload</h5>
                            <ul class="small">
                                <li>You can take a photo using your camera or upload from your device</li>
                                <li>Photo should be clear and recent</li>
                                <li>File size should be less than 5MB</li>
                                <li>Supported formats: JPG, PNG, JPEG</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-edit me-2"></i>About Form Filling</h5>
                            <ul class="small">
                                <li>Fields marked with <span class="text-warning">*</span> are mandatory</li>
                                <li>Optional fields can be left empty</li>
                                <li>Text will be automatically capitalized</li>
                                <li>Double-check mobile numbers before submitting</li>
                            </ul>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-shield-alt me-2"></i>Data Privacy</h5>
                            <p class="small mb-0">Your information is secure and will only be used for academic purposes. 
                            We do not share your data with third parties.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h2 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Student Registration</h2>
                    </div>
                    <div class="card-body p-0">
                        <form id="studentRegistrationForm">
                            <div class="row g-0 mobile-stack">
                                <!-- Image Upload Section -->
                                <div class="col-md-4 mobile-full">
                                    <div class="card h-100 border-0">
                                        <div class="card-header bg-secondary text-white">
                                            <strong><i class="fas fa-camera me-2"></i>Student Photo</strong>
                                        </div>
                                        <div class="card-body">
                                            <!-- Image Preview -->
                                            <div class="text-center mb-3">
                                                <img id="studentImagePreview" class="img-thumbnail rounded-circle"
                                                    style="width: 200px; height: 200px; object-fit: cover; display: none;">
                                                <div id="imagePlaceholder" class="text-muted p-4 border rounded">
                                                    <i class="fas fa-user fa-3x mb-3"></i>
                                                    <p class="mb-0">Student photo will appear here</p>
                                                    <small class="text-muted">Click tabs below to add photo</small>
                                                </div>
                                            </div>

                                            <!-- Image Upload Instructions -->
                                            <div class="alert alert-info small">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Photo Guidelines:</strong><br>
                                                • Clear face photo<br>
                                                • Recent picture<br>
                                                • Max 5MB size<br>
                                                • JPG, PNG formats
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
                                            </ul>

                                            <!-- Tab Content -->
                                            <div class="tab-content p-3 border border-top-0"
                                                id="imageUploadTabsContent">
                                                <!-- Camera Tab -->
                                                <div class="tab-pane fade show active" id="camera" role="tabpanel">
                                                    <div id="cameraWrapper" style="display: none">
                                                        <video id="cameraView" width="100%" autoplay muted
                                                            class="rounded border" style="max-height: 200px;"></video>
                                                        <div class="d-flex gap-2 mt-2">
                                                            <button class="btn btn-success flex-fill" type="button"
                                                                id="captureBtn">
                                                                <i class="fas fa-camera me-2"></i>Capture Photo
                                                            </button>
                                                            <button class="btn btn-secondary" type="button"
                                                                id="closeCameraBtn">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-outline-primary w-100" type="button"
                                                        id="openCameraBtn">
                                                        <i class="fas fa-camera me-2"></i>Open Camera
                                                    </button>
                                                    <p id="cameraError" class="text-danger mt-2 small"
                                                        style="display: none"></p>
                                                </div>

                                                <!-- File Upload Tab -->
                                                <div class="tab-pane fade" id="upload" role="tabpanel">
                                                    <div class="file-upload-area border rounded p-3 text-center bg-light"
                                                        onclick="document.getElementById('fileInput').click()">
                                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                                        <p class="text-muted mb-2">Click to browse or drag & drop</p>
                                                        <input type="file" id="fileInput" accept="image/*"
                                                            class="d-none">
                                                        <button class="btn btn-outline-secondary btn-sm" type="button">
                                                            <i class="fas fa-folder-open me-2"></i>Select Photo
                                                        </button>
                                                    </div>
                                                    <div class="mt-2 text-center">
                                                        <small class="text-muted">Max file size: 5MB</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Selected Image Info -->
                                            <div id="selectedImageInfo" class="mt-3 p-2 bg-light rounded"
                                                style="display: none">
                                                <small class="text-muted" id="imageSource"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student Details Form -->
                                <div class="col-md-8 mobile-full">
                                    <div class="p-4">
                                        <!-- Personal Information -->
                                        <div class="row">
                                            <div class="col-12 card-header bg-primary text-white mb-3">
                                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information
                                                </h5>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">First Name</label>
                                                <input type="text" name="fname" class="form-control capitalize-text"
                                                    required onblur="capitalizeInput(this)"
                                                    placeholder="Enter first name">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Last Name</label>
                                                <input type="text" name="lname" class="form-control capitalize-text"
                                                    required onblur="capitalizeInput(this)"
                                                    placeholder="Enter last name">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Mobile Number</label>
                                                <input type="text" name="mobile" class="form-control" required
                                                    pattern="[0-9]{10}" title="Please enter 10 digit mobile number"
                                                    placeholder="07XXXXXXXX">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">WhatsApp Mobile</label>
                                                <input type="text" name="whatsapp_mobile" class="form-control"
                                                    pattern="[0-9]{10}" title="Please enter 10 digit mobile number"
                                                    placeholder="07XXXXXXXX (optional)">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" name="email" class="form-control"
                                                    placeholder="example@gmail.com (optional)">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">NIC Number</label>
                                                <input type="text" name="nic" class="form-control"
                                                    placeholder="NIC number (optional)">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Birthday</label>
                                                <input type="date" name="bday" class="form-control" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Gender</label>
                                                <select name="gender" class="form-select" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>

                                            <!-- Address Information -->
                                            <div class="col-12 card-header bg-success text-white mt-4 mb-3">
                                                <h5 class="mb-0"><i class="fas fa-home me-2"></i>Address Information
                                                </h5>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label required-field">Address Line 1</label>
                                                <input type="text" name="address1" class="form-control capitalize-text"
                                                    required onblur="capitalizeInput(this)"
                                                    placeholder="House number, street name">
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label">Address Line 2</label>
                                                <input type="text" name="address2" class="form-control capitalize-text"
                                                    onblur="capitalizeInput(this)"
                                                    placeholder="Area, village (optional)">
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label">Address Line 3</label>
                                                <input type="text" name="address3" class="form-control capitalize-text"
                                                    onblur="capitalizeInput(this)"
                                                    placeholder="City, district (optional)">
                                            </div>

                                            <!-- Guardian Information -->
                                            <div class="col-12 card-header bg-info text-white mt-4 mb-3">
                                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Guardian Information
                                                </h5>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Guardian First Name</label>
                                                <input type="text" name="guardian_fname"
                                                    class="form-control capitalize-text" required
                                                    onblur="capitalizeInput(this)" placeholder="Guardian's first name">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Guardian Last Name</label>
                                                <input type="text" name="guardian_lname"
                                                    class="form-control capitalize-text" onblur="capitalizeInput(this)"
                                                    placeholder="Guardian's last name (optional)">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Guardian Mobile</label>
                                                <input type="text" name="guardian_mobile" class="form-control" required
                                                    pattern="[0-9]{10}" title="Please enter 10 digit mobile number"
                                                    placeholder="07XXXXXXXX">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Guardian NIC</label>
                                                <input type="text" name="guardian_nic" class="form-control"
                                                    placeholder="Guardian's NIC (optional)">
                                            </div>

                                            <!-- Academic Information -->
                                            <div class="col-12 card-header bg-warning text-dark mt-4 mb-3">
                                                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic
                                                    Information</h5>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Grade</label>
                                                <select name="grade_id" class="form-select" required>
                                                    <option value="">Select Grade</option>
                                                    <!-- Grades will be populated via JavaScript -->
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Current School</label>
                                                <input type="text" name="student_school"
                                                    class="form-control capitalize-text" onblur="capitalizeInput(this)"
                                                    placeholder="Current school name (optional)">
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="col-12 mt-4">
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>Before submitting:</strong> Please verify all information is correct. 
                                                    Once submitted, you will receive a Student ID that should be kept safely.
                                                </div>
                                                <button type="submit" class="btn btn-success btn-lg w-100 py-3"
                                                    id="submitBtn">
                                                    <i class="fas fa-user-plus me-2"></i>Register Student
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
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Registration Successful</h5>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                    <h4 class="text-success mb-3">Student Registered Successfully!</h4>
                    <div class="alert alert-info">
                        <h5>Your Student ID:</h5>
                        <h3 class="text-primary" id="successCustomId"></h3>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important Notice:</strong><br>
                        • Keep this Student ID safe and secure<br>
                        • Do not share it with anyone<br>
                        • You will need this ID for all future academic activities<br>
                        • Write it down or take a screenshot for your records
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let studentImageUrl = null;
        let cameraStream = null;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadGrades();
            initializeEventListeners();
        });

        // Load grades dropdown from API
        async function loadGrades() {
            try {
                const response = await fetch('/api/dropdown');
                
                // Check if response is HTML (error page)
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('API returned non-JSON response');
                }
                
                if (!response.ok) throw new Error('Failed to fetch grades');

                const data = await response.json();
                console.log('Grades API response:', data);

                const gradeSelect = document.querySelector('select[name="grade_id"]');
                gradeSelect.innerHTML = '<option value="">Select Grade</option>';

                // Handle different response formats
                const grades = data.data || data;
                
                if (Array.isArray(grades) && grades.length > 0) {
                    grades.forEach(grade => {
                        gradeSelect.innerHTML += `<option value="${grade.id}">Grade ${grade.grade_name}</option>`;
                    });
                } else {
                    // Fallback to manual grades if API fails
                    createManualGrades();
                }
            } catch (error) {
                console.error('Error loading grades:', error);
                // Fallback to manual grades
                createManualGrades();
                showAlert('Using default grade list', 'info');
            }
        }

        // Create manual grades as fallback
        function createManualGrades() {
            const gradeSelect = document.querySelector('select[name="grade_id"]');
            gradeSelect.innerHTML = '<option value="">Select Grade</option>';
            
            const grades = [
                {id: 1, name: '1'}, {id: 2, name: '2'}, {id: 3, name: '3'}, 
                {id: 4, name: '4'}, {id: 5, name: '5'}, {id: 6, name: '6'},
                {id: 7, name: '7'}, {id: 8, name: '8'}, {id: 9, name: '9'},
                {id: 10, name: '10'}, {id: 11, name: '11'}, {id: 12, name: '12'},
                {id: 13, name: '13'}
            ];
            
            grades.forEach(grade => {
                gradeSelect.innerHTML += `<option value="${grade.id}">Grade ${grade.name}</option>`;
            });
        }

        // Initialize event listeners
        function initializeEventListeners() {
            // Camera functionality
            document.getElementById('openCameraBtn').addEventListener('click', openCamera);
            document.getElementById('closeCameraBtn').addEventListener('click', closeCamera);
            document.getElementById('captureBtn').addEventListener('click', captureImage);

            // File upload
            document.getElementById('fileInput').addEventListener('change', handleFileUpload);

            // Form submission
            document.getElementById('studentRegistrationForm').addEventListener('submit', handleFormSubmit);
        }

        // Camera functions
        async function openCamera() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: 1280, height: 720, facingMode: 'environment' }
                });

                const cameraView = document.getElementById('cameraView');
                cameraView.srcObject = cameraStream;

                document.getElementById('cameraWrapper').style.display = 'block';
                document.getElementById('openCameraBtn').style.display = 'none';
                document.getElementById('cameraError').style.display = 'none';

            } catch (error) {
                document.getElementById('cameraError').innerText = 'Camera access denied or not available. Please check browser permissions.';
                document.getElementById('cameraError').style.display = 'block';
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
                const file = new File([blob], "student_photo.jpg", { type: "image/jpeg" });
                uploadImage(file, 'camera');
                closeCamera();
            }, "image/jpeg", 0.8);
        }

        // File upload handler
        function handleFileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    showAlert('Please select a valid image file (JPG, PNG, JPEG)', 'danger');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('Image size should be less than 5MB', 'danger');
                    return;
                }
                uploadImage(file, 'file');
            }
        }

        // Upload image to server using API
        async function uploadImage(file, source) {
            try {
                showAlert('Uploading photo...', 'info');

                const formData = new FormData();
                formData.append('image', file);

                const response = await fetch('/api/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                // Check if response is successful
                if (!response.ok) {
                    throw new Error(`Upload failed with status: ${response.status}`);
                }

                // Check response type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Image upload API returned non-JSON response');
                }

                const data = await response.json();
                console.log('Upload API response:', data);

                if (data.status === 'success') {
                    // Handle different response formats
                    const imageUrl = data.image_url || data.data?.image_url || data.url;
                    if (imageUrl) {
                        studentImageUrl = imageUrl.startsWith('http') ? imageUrl : "/uploads/images/" + imageUrl;
                        updateImagePreview(studentImageUrl, `Uploaded via ${source}`);
                        showAlert('Photo uploaded successfully!', 'success');
                    } else {
                        throw new Error('No image URL in response');
                    }
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            } catch (error) {
                console.error('Upload error:', error);
                showAlert('Failed to upload photo: ' + error.message, 'danger');
            }
        }

        // Form submission handler - Handle null values properly
        async function handleFormSubmit(e) {
            e.preventDefault();

            if (!studentImageUrl) {
                showAlert('Please upload a student photo before submitting', 'warning');
                return;
            }

            const formData = new FormData(e.target);
            const studentData = {
                img_url: studentImageUrl,
                is_active: 1,
                admission: null,
                is_freecard: null
            };

            // Convert FormData to object - Handle empty values as null
            for (let [key, value] of formData.entries()) {
                if (value && value.trim() !== '') {
                    studentData[key] = value;
                } else {
                    // Send null for empty fields
                    studentData[key] = null;
                }
            }

            try {
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';

                console.log('Submitting student data:', studentData);

                const response = await fetch('/api/public', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(studentData)
                });

                // Check if response is successful
                if (!response.ok) {
                    throw new Error(`Registration failed with status: ${response.status}`);
                }

                // Check response type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Registration API returned non-JSON response');
                }

                const result = await response.json();
                console.log('Registration API response:', result);

                if (result.status === 'success') {
                    const customId = result.data?.custom_id || result.custom_id;
                    if (customId) {
                        showSuccessModal(customId);
                        resetForm();
                    } else {
                        throw new Error('No student ID received');
                    }
                } else if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat().join(', ');
                    throw new Error('Validation failed: ' + errorMessages);
                } else {
                    throw new Error(result.message || 'Registration failed');
                }

            } catch (error) {
                console.error('Registration error:', error);
                showAlert('Failed to register student: ' + error.message, 'danger');
            } finally {
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').innerHTML = '<i class="fas fa-user-plus me-2"></i>Register Student';
            }
        }

        // Utility functions
        function getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!token) {
                console.error('CSRF token not found');
                showAlert('Security token missing. Please refresh the page.', 'danger');
            }
            return token || '';
        }

        function capitalizeInput(input) {
            if (input.value) {
                input.value = input.value.toLowerCase().split(' ').map(word =>
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
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

        function showSuccessModal(customId) {
            document.getElementById('successCustomId').textContent = customId;
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        }

        function resetForm() {
            document.getElementById('studentRegistrationForm').reset();
            studentImageUrl = null;

            const preview = document.getElementById('studentImagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const imageInfo = document.getElementById('selectedImageInfo');

            preview.style.display = 'none';
            placeholder.style.display = 'block';
            imageInfo.style.display = 'none';
        }

        // Fixed showAlert function
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');

            // Remove existing alerts
            alertContainer.innerHTML = '';

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            alertContainer.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode === alertContainer) {
                    alertContainer.removeChild(alertDiv);
                }
            }, 5000);
        }
    </script>
</body>
</html>