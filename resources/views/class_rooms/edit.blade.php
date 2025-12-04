@extends('layouts.app')

@section('title', 'Edit Class Room')
@section('page-title', 'Edit Class Room')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('class_rooms.index') }}">Class Rooms</a></li>
    <li class="breadcrumb-item"><a href="{{ route('class_rooms.show', $id) }}">Class Details</a></li>
    <li class="breadcrumb-item active">Edit Class Room</li>
@endsection

@section('content')
    <div class="container-fluid"> {{-- Changed from row to container-fluid --}}
        <div class="row justify-content-center">
            <div class="col-12"> {{-- Changed to col-12 for full width --}}
                <!-- Edit Class Room Card -->
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-edit me-2"></i>Edit Class Room
                            </h5>
                            <button class="btn btn-light btn-sm" onclick="loadAllData()" title="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="editClassRoomForm">
                            @csrf
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <!-- Class Name -->
                                    <div class="mb-3">
                                        <label for="class_name" class="form-label">Class Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="class_name" name="class_name" required>
                                        <div class="invalid-feedback" id="class_name_error"></div>
                                    </div>

                                    <!-- Teacher Dropdown -->
                                    <div class="mb-3">
                                        <label for="teacher_id" class="form-label">Teacher <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="teacher_id" name="teacher_id" required>
                                            <option value="">Select Teacher</option>
                                        </select>
                                        <div class="invalid-feedback" id="teacher_id_error"></div>
                                    </div>

                                    <!-- Status Toggle -->
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                                value="1">
                                            <label class="form-check-label" for="is_active">Active Class</label>
                                        </div>
                                        <small class="text-muted">Toggle to activate or deactivate this class</small>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <!-- Grade Dropdown -->
                                    <div class="mb-3">
                                        <label for="grade_id" class="form-label">Grade <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="grade_id" name="grade_id" required>
                                            <option value="">Select Grade</option>
                                        </select>
                                        <div class="invalid-feedback" id="grade_id_error"></div>
                                    </div>

                                    <!-- Subject Dropdown -->
                                    <div class="mb-3">
                                        <label for="subject_id" class="form-label">Subject <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="subject_id" name="subject_id" required>
                                            <option value="">Select Subject</option>
                                        </select>
                                        <div class="invalid-feedback" id="subject_id_error"></div>
                                    </div>

                                    <!-- Ongoing Status Toggle -->
                                    <div class="mb-3">
                                        <label class="form-label">Progress Status</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_ongoing"
                                                name="is_ongoing" value="1">
                                            <label class="form-check-label" for="is_ongoing">Class is Ongoing</label>
                                        </div>
                                        <small class="text-muted">Toggle if this class is currently in progress</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('class_rooms.show', $id) }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-warning" id="updateBtn">
                                            <i class="fas fa-save me-2"></i>Update Class Room
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Remove all the width styles and let Bootstrap handle the layout */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e9ecef;
            height: 100%;
        }

        .card-header {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
            transform: translateY(-2px);
        }

        .btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .form-check-input:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .text-muted {
            font-size: 0.875rem;
        }

        /* Ensure the container takes full width */
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }

        /* Make the row full width */
        .row.justify-content-center {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const classId = {{ $id }};
        let classData = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Load all data in sequence
            loadAllData();

            // Form submission
            const editClassRoomForm = document.getElementById('editClassRoomForm');
            editClassRoomForm.addEventListener('submit', function (e) {
                e.preventDefault();
                updateClassRoom();
            });
        });

        // Load all data in correct sequence
        function loadAllData() {
            // First load class data, then load dropdowns
            loadClassData()
                .then(() => {
                    return loadTeachers();
                })
                .then(() => {
                    return loadGrades();
                })
                .then(() => {
                    return loadSubjects();
                })
                .then(() => {
                    // After all dropdowns are loaded, populate the form
                    if (classData) {
                        populateForm(classData);
                    }
                })
                .catch(error => {
                    console.error('Error loading data:', error);
                    showAlert('Error loading data: ' + error.message, 'danger');
                });
        }

        // Load Class Data
        function loadClassData() {
            return fetch(`/api/class-rooms/${classId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success' && data.data) {
                        classData = data.data;
                        console.log('Class data loaded:', classData);
                    } else {
                        throw new Error('Invalid response format');
                    }
                });
        }

        // Populate Form with Class Data
        function populateForm(classData) {
            console.log('Populating form with data:', classData);

            // Set basic fields
            document.getElementById('class_name').value = classData.class_name || '';

            // Set dropdown values - wait a bit to ensure dropdowns are populated
            setTimeout(() => {
                if (classData.teacher_id) {
                    const teacherSelect = document.getElementById('teacher_id');
                    teacherSelect.value = classData.teacher_id;
                    console.log('Setting teacher_id:', classData.teacher_id, 'Current value:', teacherSelect.value);
                }

                if (classData.grade_id) {
                    const gradeSelect = document.getElementById('grade_id');
                    gradeSelect.value = classData.grade_id;
                    console.log('Setting grade_id:', classData.grade_id, 'Current value:', gradeSelect.value);
                }

                if (classData.subject_id) {
                    const subjectSelect = document.getElementById('subject_id');
                    subjectSelect.value = classData.subject_id;
                    console.log('Setting subject_id:', classData.subject_id, 'Current value:', subjectSelect.value);
                }

                // Set toggle switches
                document.getElementById('is_active').checked = classData.is_active == 1;
                document.getElementById('is_ongoing').checked = classData.is_ongoing == 1;

                // Show success message
                showAlert('Form data loaded successfully!', 'success');
            }, 500);
        }

        // Dropdown Loading Functions
        function loadTeachers() {
            return fetch(`/api/teachers/dropdown`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const teacherSelect = document.getElementById('teacher_id');
                        // Clear existing options except the first one
                        while (teacherSelect.options.length > 1) {
                            teacherSelect.remove(1);
                        }
                        data.data.forEach(teacher => {
                            const option = document.createElement('option');
                            option.value = teacher.id;
                            option.textContent = `${teacher.fname} ${teacher.lname} (${teacher.custom_id || 'No ID'})`;
                            teacherSelect.appendChild(option);
                        });
                        console.log('Teachers loaded:', data.data.length);
                    } else {
                        throw new Error(data.message || 'Failed to load teachers');
                    }
                });
        }

        function loadGrades() {
            return fetch(`/api/grades/dropdown`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const gradeSelect = document.getElementById('grade_id');
                        // Clear existing options except the first one
                        while (gradeSelect.options.length > 1) {
                            gradeSelect.remove(1);
                        }
                        data.data.forEach(grade => {
                            const option = document.createElement('option');
                            option.value = grade.id;
                            option.textContent = `Grade ${grade.grade_name}`;
                            gradeSelect.appendChild(option);
                        });
                        console.log('Grades loaded:', data.data.length);
                    } else {
                        throw new Error(data.message || 'Failed to load grades');
                    }
                });
        }

        function loadSubjects() {
            return fetch(`/api/subjects/dropdown`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        const subjectSelect = document.getElementById('subject_id');
                        // Clear existing options except the first one
                        while (subjectSelect.options.length > 1) {
                            subjectSelect.remove(1);
                        }
                        data.data.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.subject_name;
                            subjectSelect.appendChild(option);
                        });
                        console.log('Subjects loaded:', data.data.length);
                    } else {
                        throw new Error(data.message || 'Failed to load subjects');
                    }
                });
        }

        // Update Class Room
        function updateClassRoom() {
            const updateBtn = document.getElementById('updateBtn');
            const originalText = updateBtn.innerHTML;

            updateBtn.disabled = true;
            updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            const formData = {
                class_name: document.getElementById('class_name').value,
                teacher_id: document.getElementById('teacher_id').value,
                subject_id: document.getElementById('subject_id').value,
                grade_id: document.getElementById('grade_id').value,
                is_active: document.getElementById('is_active').checked ? 1 : 0,
                is_ongoing: document.getElementById('is_ongoing').checked ? 1 : 0
            };

            // Validation
            if (!formData.class_name || !formData.teacher_id || !formData.subject_id || !formData.grade_id) {
                showAlert('Please fill all required fields', 'warning');
                updateBtn.disabled = false;
                updateBtn.innerHTML = originalText;
                return;
            }

            // Use PUT method directly
            fetch(`/api/class-rooms/${classId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        showAlert('Class room updated successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = `/class-rooms/${classId}`;
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Failed to update class room');
                    }
                })
                .catch(error => {
                    console.error('Error updating class room:', error);
                    if (error.errors) {
                        displayValidationErrors(error.errors);
                    } else {
                        showAlert('Error updating class room: ' + error.message, 'danger');
                    }
                    updateBtn.disabled = false;
                    updateBtn.innerHTML = originalText;
                });
        }

        // Display validation errors
        function displayValidationErrors(errors) {
            // Clear previous errors
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.style.display = 'none';
            });
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });

            // Display new errors
            for (const field in errors) {
                const errorElement = document.getElementById(field + '_error');
                const inputElement = document.getElementById(field);

                if (errorElement && inputElement) {
                    errorElement.textContent = errors[field][0];
                    errorElement.style.display = 'block';
                    inputElement.classList.add('is-invalid');
                }
            }
        }

        // Helper Functions
        function showAlert(message, type) {
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());

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

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        }
    </script>
@endpush