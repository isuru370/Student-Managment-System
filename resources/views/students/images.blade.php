@extends('layouts.app')

@section('title', 'Student Images')
@section('page-title', 'Student Images')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Student Images</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-images me-2"></i>Student Images Management</strong>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Search by name or student ID...">
                                <button class="btn btn-primary" type="button" id="searchBtn">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn">
                                    <i class="fas fa-times me-2"></i>Clear
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                                <i class="fas fa-camera me-2"></i>Update Student Image
                            </button>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading student images...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h4 class="text-danger">Failed to Load Student Images</h4>
                        <p class="text-muted" id="errorText"></p>
                        <button class="btn btn-primary mt-3" onclick="loadStudentImages()">
                            <i class="fas fa-redo me-2"></i>Retry
                        </button>
                    </div>

                    <!-- Student Images Grid -->
                    <div id="studentImagesGrid" class="row g-3" style="display: none;">
                        <!-- Student cards will be populated here -->
                    </div>

                    <!-- No Results Message -->
                    <div id="noResultsMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Students Found</h4>
                        <p class="text-muted">Try adjusting your search criteria</p>
                    </div>

                    <!-- Load More Button -->
                    <div id="loadMoreContainer" class="text-center mt-4" style="display: none;">
                        <button id="loadMoreBtn" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Load More Students
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Image Modal -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-camera me-2"></i>Update Student Image
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadImageForm">
                        <!-- Student Selection with Searchable Dropdown -->
                        <div class="mb-4">
                            <label class="form-label">Select Student <span class="text-danger">*</span></label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle" type="button"
                                    id="studentDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Select a student...
                                </button>
                                <div class="dropdown-menu w-100 p-2" aria-labelledby="studentDropdown">
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm" id="studentSearch"
                                            placeholder="Search students...">
                                    </div>
                                    <div id="studentDropdownOptions" style="max-height: 200px; overflow-y: auto;">
                                        <!-- Student options will be populated here -->
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="selectedStudentId">
                            <input type="hidden" id="selectedStudentName">
                        </div>

                        <!-- Selected Student Info -->
                        <div id="selectedStudentInfo" class="alert alert-info py-2" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong id="selectedStudentText"></strong>
                                </div>
                                <button type="button" class="btn-close" onclick="clearStudentSelection()"></button>
                            </div>
                        </div>

                        <!-- Current Image Preview -->
                        <div class="mb-4" id="currentImageSection" style="display: none;">
                            <label class="form-label">Current Image</label>
                            <div class="text-center">
                                <img id="currentStudentImage" class="img-thumbnail rounded"
                                    style="width: 150px; height: 150px; object-fit: cover;"
                                    onerror="this.src='/uploads/logo/logo.png'">
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <strong>Select New Image Method</strong>
                            </div>
                            <div class="card-body">
                                <!-- New Image Preview -->
                                <div class="text-center mb-3">
                                    <img id="imagePreview" class="img-thumbnail rounded"
                                        style="width: 200px; height: 200px; object-fit: cover; display: none;"
                                        onerror="this.src='/uploads/logo/logo.png'">
                                    <div id="imagePlaceholder" class="text-muted p-4 border rounded">
                                        <i class="fas fa-user fa-3x mb-3"></i>
                                        <p class="mb-0">New image will appear here</p>
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
                                            <i class="fas fa-upload me-1"></i>Browse
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
                                            <video id="cameraView" width="100%" autoplay muted class="rounded border"
                                                style="max-height: 200px;"></video>
                                            <div class="d-flex gap-2 mt-2">
                                                <button class="btn btn-success flex-fill" type="button" id="captureBtn">
                                                    <i class="fas fa-camera me-2"></i>Capture
                                                </button>
                                                <button class="btn btn-secondary" type="button" id="closeCameraBtn">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button class="btn btn-outline-primary w-100" type="button" id="openCameraBtn">
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
                                                <button class="btn btn-outline-primary" type="button" id="searchQuickImage">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="quickImageResults" class="mt-3">
                                            <p class="text-muted text-center">Search for quick images above</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Image Info -->
                                <div id="selectedImageInfo" class="mt-3 p-2 bg-light rounded" style="display: none">
                                    <small class="text-muted" id="imageSource"></small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="uploadImageBtn" disabled>
                        <i class="fas fa-save me-2"></i>Update Image
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Image Modal -->
    <div class="modal fade" id="viewImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewImageTitle">
                        <i class="fas fa-image me-2"></i>Student Image
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <h6>Current Image</h6>
                            <img id="viewImage" src="" class="img-fluid rounded border" style="max-height: 300px;"
                                onerror="this.src='/uploads/logo/logo.png'">
                        </div>
                        <div class="col-md-6">
                            <h6>Student Details</h6>
                            <div class="mb-3">
                                <strong>Student ID:</strong> <span id="viewStudentId" class="badge bg-primary"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Name:</strong> <span id="viewStudentName" class="fw-bold"></span>
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-warning btn-sm w-100"
                                    onclick="openUpdateForCurrentStudent()">
                                    <i class="fas fa-camera me-2"></i>Update This Student's Image
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .student-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            border: 1px solid #e9ecef;
            height: 100%;
        }

        .student-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-color: #0d6efd;
        }

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

        .image-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .student-option {
            cursor: pointer;
            padding: 8px 12px;
            border-bottom: 1px solid #f8f9fa;
        }

        .student-option:hover {
            background-color: #f8f9fa;
        }

        .student-option.selected {
            background-color: #e7f1ff;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let studentImages = [];
        let allStudents = [];
        let currentPage = 1;
        let hasMore = true;
        let currentSearch = '';
        let selectedStudentId = '';
        let selectedImageUrl = '';
        let cameraStream = null;
        let selectedQuickImageId = null;
        let currentViewStudent = null;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadStudentImages();
            loadAllStudents();
            initializeEventListeners();
        });

        // ================= LOAD STUDENT IMAGES =================
        async function loadStudentImages(page = 1, search = '') {
            try {
                if (page === 1) {
                    showLoadingState();
                    studentImages = [];
                }

                // Try different search parameters based on your API
                let url = `/api/students/active?page=${page}`;
                if (search && search.trim() !== '') {
                    // Try different parameter names that your API might expect
                    url += `&search=${encodeURIComponent(search.trim())}`;
                    // Alternative parameter names:
                    // url += `&q=${encodeURIComponent(search.trim())}`;
                    // url += `&keyword=${encodeURIComponent(search.trim())}`;
                    // url += `&filter=${encodeURIComponent(search.trim())}`;
                }

                console.log('🔍 Fetching URL:', url);

                const response = await fetch(url);
                if (!response.ok) throw new Error('Failed to fetch student images');

                const result = await response.json();
                console.log('📦 Student images response:', result);

                if (result.status === 'success' && result.data) {
                    let students = result.data.data || result.data;

                    // If API doesn't support search, filter client-side
                    if (search && search.trim() !== '') {
                        const searchTerm = search.toLowerCase().trim();
                        students = students.filter(student =>
                            student.fname.toLowerCase().includes(searchTerm) ||
                            student.lname.toLowerCase().includes(searchTerm) ||
                            student.custom_id.toLowerCase().includes(searchTerm)
                        );
                    }

                    if (page === 1) {
                        studentImages = students;
                    } else {
                        studentImages = [...studentImages, ...students];
                    }

                    displayStudentImages();

                    // Check if there are more pages
                    const nextPageUrl = result.data.next_page_url || result.next_page_url;
                    updateLoadMoreButton(nextPageUrl);

                    if (page === 1) {
                        showContentState();
                    }

                    // Show/hide no results message
                    const noResults = document.getElementById('noResultsMessage');
                    if (students.length === 0 && page === 1) {
                        noResults.style.display = 'block';
                        document.getElementById('studentImagesGrid').style.display = 'none';
                    } else {
                        noResults.style.display = 'none';
                    }
                } else {
                    throw new Error(result.message || 'No student data found');
                }
            } catch (error) {
                console.error('❌ Error loading student images:', error);
                if (currentPage === 1) {
                    showErrorState('Failed to load student images: ' + error.message);
                }
            }
        }

        // ================= LOAD ALL STUDENTS FOR DROPDOWN =================
        async function loadAllStudents() {
            try {
                const response = await fetch('/api/students/active?per_page=1000');
                if (!response.ok) throw new Error('Failed to fetch students');

                const result = await response.json();
                if (result.status === 'success' && result.data) {
                    allStudents = result.data.data || result.data;
                    populateStudentDropdown(allStudents);
                }
            } catch (error) {
                console.error('Error loading all students:', error);
            }
        }

        // ================= POPULATE STUDENT DROPDOWN =================
        function populateStudentDropdown(students) {
            const container = document.getElementById('studentDropdownOptions');

            if (students.length === 0) {
                container.innerHTML = '<p class="text-muted text-center p-2">No students found</p>';
                return;
            }

            const html = students.map(student => `
                                        <div class="student-option" onclick="selectStudent('${student.custom_id}', '${student.fname} ${student.lname}', '${student.img_url || ''}')">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>${student.custom_id}</strong> - ${student.fname} ${student.lname}
                                                </div>
                                                <small class="text-muted">${student.img_url ? '📷' : '❌'}</small>
                                            </div>
                                        </div>
                                    `).join('');

            container.innerHTML = html;
        }

        // ================= SELECT STUDENT FROM DROPDOWN =================
        function selectStudent(customId, name, currentImageUrl) {
            selectedStudentId = customId;

            // Update dropdown button text
            document.getElementById('studentDropdown').innerHTML = `${customId} - ${name}`;

            // Update hidden inputs
            document.getElementById('selectedStudentId').value = customId;
            document.getElementById('selectedStudentName').value = name;

            // Show selected student info
            document.getElementById('selectedStudentText').textContent = `${customId} - ${name}`;
            document.getElementById('selectedStudentInfo').style.display = 'block';

            // Show current image
            const currentImageSection = document.getElementById('currentImageSection');
            const currentStudentImage = document.getElementById('currentStudentImage');

            if (currentImageUrl) {
                currentStudentImage.src = currentImageUrl;
                currentImageSection.style.display = 'block';
            } else {
                currentImageSection.style.display = 'none';
            }

            // Close dropdown
            const dropdown = new bootstrap.Dropdown(document.getElementById('studentDropdown'));
            dropdown.hide();

            updateUploadButton();
        }

        // ================= CLEAR STUDENT SELECTION =================
        function clearStudentSelection() {
            selectedStudentId = '';
            document.getElementById('studentDropdown').innerHTML = 'Select a student...';
            document.getElementById('selectedStudentId').value = '';
            document.getElementById('selectedStudentName').value = '';
            document.getElementById('selectedStudentInfo').style.display = 'none';
            document.getElementById('currentImageSection').style.display = 'none';
            updateUploadButton();
        }

        // ================= DISPLAY STUDENT IMAGES =================
        function displayStudentImages() {
            const grid = document.getElementById('studentImagesGrid');

            if (studentImages.length === 0) {
                grid.style.display = 'none';
                return;
            }

            console.log('🎨 Displaying', studentImages.length, 'students');

            const html = studentImages.map(student => {
                const studentData = JSON.stringify(student).replace(/'/g, "&#39;");
                return `
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                            <div class="card student-card h-100" onclick="viewStudentImage('${student.custom_id}', '${student.fname} ${student.lname}', '${student.img_url || '/uploads/logo/logo.png'}', '${studentData}')">
                                <div class="card-body text-center p-3 position-relative">
                                    <div class="image-badge">
                                        <span class="badge ${student.img_url ? 'bg-success' : 'bg-warning'}">
                                            <i class="fas ${student.img_url ? 'fa-check' : 'fa-times'} me-1"></i>
                                            ${student.img_url ? 'Has Image' : 'No Image'}
                                        </span>
                                    </div>
                                    <img src="${student.img_url || '/uploads/logo/logo.png'}" 
                                         class="rounded-circle mb-3" 
                                         style="width: 120px; height: 120px; object-fit: cover;"
                                         onerror="this.src='/uploads/logo/logo.png'">
                                    <h6 class="card-title mb-1 text-dark">${student.fname} ${student.lname}</h6>
                                    <p class="card-text text-muted small mb-2">${student.custom_id}</p>
                                    <div class="mt-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation(); openUpdateModal('${student.custom_id}', '${student.fname} ${student.lname}', '${student.img_url || ''}')">
                                            <i class="fas fa-camera me-1"></i>Update Image
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
            }).join('');

            grid.innerHTML = html;
            grid.style.display = 'flex';
        }

        // ================= VIEW STUDENT IMAGE =================
        function viewStudentImage(customId, name, imageUrl, studentData) {
            document.getElementById('viewImage').src = imageUrl;
            document.getElementById('viewStudentId').textContent = customId;
            document.getElementById('viewStudentName').textContent = name;
            document.getElementById('viewImageTitle').innerHTML = `<i class="fas fa-image me-2"></i>${name}'s Image`;

            try {
                currentViewStudent = JSON.parse(studentData.replace(/&#39;/g, "'"));
            } catch (e) {
                currentViewStudent = { custom_id: customId, fname: name.split(' ')[0], lname: name.split(' ')[1] || '' };
            }

            const modal = new bootstrap.Modal(document.getElementById('viewImageModal'));
            modal.show();
        }

        // ================= OPEN UPDATE MODAL FOR SPECIFIC STUDENT =================
        function openUpdateModal(customId, name, currentImageUrl) {
            selectStudent(customId, name, currentImageUrl);
            const uploadModal = new bootstrap.Modal(document.getElementById('uploadImageModal'));
            uploadModal.show();
        }

        // ================= OPEN UPDATE FOR CURRENT VIEW STUDENT =================
        function openUpdateForCurrentStudent() {
            if (currentViewStudent) {
                // Close view modal
                bootstrap.Modal.getInstance(document.getElementById('viewImageModal')).hide();

                // Open update modal for this student
                openUpdateModal(
                    currentViewStudent.custom_id,
                    `${currentViewStudent.fname} ${currentViewStudent.lname}`,
                    currentViewStudent.img_url || ''
                );
            }
        }

        // ================= UPDATE STUDENT IMAGE =================
        async function updateStudentImage() {
            if (!selectedStudentId) {
                showAlert('Please select a student first', 'warning');
                return;
            }

            if (!selectedImageUrl) {
                showAlert('Please capture or upload an image first', 'warning');
                return;
            }

            try {
                const uploadBtn = document.getElementById('uploadImageBtn');
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

                const response = await fetch(`/api/students/update_image/${selectedStudentId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        img_url: selectedImageUrl
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {

                    if(selectedQuickImageId){
                        await deactivateQuickImage(selectedQuickImageId);
                    }
                    showAlert('Student image updated successfully!', 'success');

                    // Close modal and refresh data
                    bootstrap.Modal.getInstance(document.getElementById('uploadImageModal')).hide();

                    // Refresh the student images grid
                    loadStudentImages();

                    // Reset form
                    resetUploadForm();
                } else {
                    throw new Error(result.message || 'Update failed');
                }
            } catch (error) {
                console.error('Error updating student image:', error);
                showAlert('Failed to update student image: ' + error.message, 'danger');
            } finally {
                const uploadBtn = document.getElementById('uploadImageBtn');
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Image';
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

        // ================= IMAGE UPLOAD FUNCTIONS =================
        function initializeEventListeners() {
            // Camera functionality
            document.getElementById('openCameraBtn').addEventListener('click', openCamera);
            document.getElementById('closeCameraBtn').addEventListener('click', closeCamera);
            document.getElementById('captureBtn').addEventListener('click', captureImage);

            // File upload
            document.getElementById('fileInput').addEventListener('change', handleFileUpload);

            // Quick image search
            document.getElementById('searchQuickImage').addEventListener('click', searchQuickImages);

            // Upload image button
            document.getElementById('uploadImageBtn').addEventListener('click', updateStudentImage);

            // Search functionality
            // Search functionality - FIXED
            document.getElementById('searchBtn').addEventListener('click', performSearch);
            document.getElementById('clearSearchBtn').addEventListener('click', clearSearch);

            document.getElementById('searchInput').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
            document.getElementById('searchInput').addEventListener('input', function (e) {
                // Optional: Add debouncing for real-time search
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    if (this.value.trim().length >= 3 || this.value.trim().length === 0) {
                        performSearch();
                    }
                }, 500);
            });

            // Student dropdown search
            document.getElementById('studentSearch').addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase();
                const filteredStudents = allStudents.filter(student =>
                    student.custom_id.toLowerCase().includes(searchTerm) ||
                    student.fname.toLowerCase().includes(searchTerm) ||
                    student.lname.toLowerCase().includes(searchTerm)
                );
                populateStudentDropdown(filteredStudents);
            });

            // Load more
            document.getElementById('loadMoreBtn').addEventListener('click', function () {
                currentPage++;
                loadStudentImages(currentPage, currentSearch);
            });
        }

        // ================= SEARCH FUNCTIONS =================
        function performSearch() {
            currentSearch = document.getElementById('searchInput').value.trim();
            currentPage = 1;
            console.log('🔍 Performing search:', currentSearch);
            loadStudentImages(1, currentSearch);
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            currentSearch = '';
            currentPage = 1;
            console.log('🧹 Clearing search');
            loadStudentImages(1, '');
        }

        // ================= CAMERA FUNCTIONS =================
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

            } catch (e) {
                document.getElementById('cameraError').innerText = 'Camera access denied or not available.';
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

        function displayQuickImages(images) {
            const resultsContainer = document.getElementById('quickImageResults');

            if (images.length === 0) {
                resultsContainer.innerHTML = '<p class="text-muted text-center">No quick images found</p>';
                return;
            }

            resultsContainer.innerHTML = images.map(img => {
                const imageUrl = img.quick_img.startsWith('http') ?
                    img.quick_img :
                    "{{ url('/uploads/') }}/" + img.quick_img;

                return `
                                            <div class="quick-image-item card mb-2 p-2" onclick="selectQuickImage(${img.id}, '${imageUrl}', '${img.custom_id || 'No ID'}')">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-3">
                                                        <img src="${imageUrl}" class="img-fluid rounded" style="height: 60px; object-fit: cover;"
                                                             onerror="this.src='/uploads/logo/logo.png'">
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

        function selectQuickImage(id, imageUrl, customId) {
            document.querySelectorAll('.quick-image-item').forEach(item => {
                item.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');

            selectedImageUrl = imageUrl;
            selectedQuickImageId = id;
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
                    selectedImageUrl = "{{ url('/uploads/') }}/" + data.image_url;
                    updateImagePreview(selectedImageUrl, `Uploaded via ${source}`);
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
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const imageInfo = document.getElementById('selectedImageInfo');

            preview.src = imageUrl;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            imageInfo.style.display = 'block';
            document.getElementById('imageSource').textContent = source;

            updateUploadButton();
        }

        function updateUploadButton() {
            const uploadBtn = document.getElementById('uploadImageBtn');
            uploadBtn.disabled = !(selectedStudentId && selectedImageUrl);
        }

        function resetUploadForm() {
            selectedStudentId = '';
            selectedImageUrl = '';
            selectedQuickImageId = null;

            clearStudentSelection();
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('imagePlaceholder').style.display = 'block';
            document.getElementById('selectedImageInfo').style.display = 'none';
            document.getElementById('uploadImageBtn').disabled = true;

            if (cameraStream) {
                closeCamera();
            }
        }

        // ================= UTILITY FUNCTIONS =================
        function showLoadingState() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('studentImagesGrid').style.display = 'none';
            document.getElementById('loadMoreContainer').style.display = 'none';
            document.getElementById('noResultsMessage').style.display = 'none';
        }

        function showContentState() {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('studentImagesGrid').style.display = 'flex';
        }

        function showErrorState(message) {
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('studentImagesGrid').style.display = 'none';
            document.getElementById('loadMoreContainer').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'block';
            document.getElementById('errorText').textContent = message;
        }

        function updateLoadMoreButton(hasNextPage) {
            const container = document.getElementById('loadMoreContainer');
            container.style.display = hasNextPage ? 'block' : 'none';
        }

        function showAlert(message, type) {
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => {
                if (!alert.classList.contains('alert-info') || !alert.parentElement.classList.contains('modal-body')) {
                    alert.remove();
                }
            });

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                                        ${message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    `;

            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
@endpush