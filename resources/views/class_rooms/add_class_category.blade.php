@extends('layouts.app')

@section('title', 'Manage Class Categories & Fees')
@section('page-title', 'Manage Categories & Fees')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('class_rooms.index') }}">Class Rooms</a></li>
    <li class="breadcrumb-item active">Manage Categories & Fees</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags me-2"></i>Manage Categories & Fees for Class ID: {{ $id }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Class Information will be loaded via API -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="class-info p-3 bg-light rounded">
                                <h6 class="fw-bold">Class Details:</h6>
                                <div id="classDetails">
                                    <div class="text-center py-2">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Loading class information...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('class_rooms.schedule') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Classes
                            </a>
                        </div>
                    </div>

                    <!-- Add Category & Fees Form -->
                    <div class="card mb-4">
                        <div class="card-header bg-transparent">
                            <h6 class="card-title mb-0">Add Category & Set Fee</h6>
                        </div>
                        <div class="card-body">
                            <form id="addCategoryFeeForm">
                                @csrf
                                <input type="hidden" name="student_classes_id" value="{{ $id }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="class_category_id" class="form-label">Category <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="class_category_id" name="class_category_id"
                                                required>
                                                <option value="">Select Category</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="fees" class="form-label">Fee Amount <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" class="form-control" id="fees" name="fees" min="0"
                                                    step="0.01" placeholder="0.00" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100" id="addCategoryFeeBtn">
                                                <i class="fas fa-plus me-2"></i>Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Assigned Categories Table -->
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">Assigned Categories & Fees</h6>
                                <button class="btn btn-outline-primary btn-sm" onclick="loadAssignedCategories()"
                                    title="Refresh">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th width="60">#</th>
                                            <th>Category Name</th>
                                            <th class="text-end">Fee Amount</th>
                                            <th width="100" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="assignedCategoriesTableBody">
                                        <!-- Assigned categories will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Loading State -->
                            <div id="assignedCategoriesLoading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading assigned categories...</p>
                            </div>

                            <!-- Empty State -->
                            <div id="assignedCategoriesEmpty" class="text-center py-5 d-none">
                                <div class="empty-state-icon">
                                    <i class="fas fa-tags fa-4x text-muted mb-4"></i>
                                </div>
                                <h4 class="text-muted">No Categories Assigned</h4>
                                <p class="text-muted mb-4">No categories have been assigned to this class yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Fee Modal -->
    <div class="modal fade" id="editCategoryFeeModal" tabindex="-1" aria-labelledby="editCategoryFeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editCategoryFeeModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Category Fee
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryFeeForm">
                        <input type="hidden" id="edit_category_class_id">
                        <input type="hidden" id="edit_category_id">

                        <!-- Category Name Display -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <div class="form-control bg-light" id="edit_category_name_display" style="border: none;">
                                <!-- Category name will be displayed here -->
                            </div>
                        </div>

                        <!-- Current Fee Display -->
                        <div class="mb-3">
                            <label class="form-label">Current Fee</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rs.</span>
                                <input type="text" class="form-control bg-light" id="edit_current_fee" readonly
                                    style="border: none;">
                            </div>
                        </div>

                        <!-- New Fee Input -->
                        <div class="mb-3">
                            <label for="edit_new_fee" class="form-label">New Fee Amount <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" id="edit_new_fee" name="fees" min="0" step="0.01"
                                    placeholder="0.00" required>
                            </div>
                            <div class="form-text">Enter the new fee amount for this category</div>
                            <div class="invalid-feedback" id="edit_new_fee_error"></div>
                        </div>

                        <!-- Fee Change Summary -->
                        <div class="alert alert-info d-none" id="feeChangeSummary">
                            <div class="d-flex justify-content-between">
                                <span>Old Fee:</span>
                                <span id="oldFeeAmount" class="fw-bold"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>New Fee:</span>
                                <span id="newFeeAmount" class="fw-bold"></span>
                            </div>
                            <div class="d-flex justify-content-between border-top mt-2 pt-2">
                                <span>Difference:</span>
                                <span id="feeDifference" class="fw-bold"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="updateCategoryFeeBtn">
                        <i class="fas fa-save me-2"></i>Update Fee
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

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .empty-state-icon {
            opacity: 0.5;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const classId = {{ $id }};

        document.addEventListener('DOMContentLoaded', function () {
            // Load data on page load
            loadClassDetails();
            loadCategoriesDropdown();
            loadAssignedCategories();

            // Form submission
            const addCategoryFeeForm = document.getElementById('addCategoryFeeForm');
            const addCategoryFeeBtn = document.getElementById('addCategoryFeeBtn');

            addCategoryFeeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                addCategoryFee();
            });

            // Update Category Fee Button
            const updateCategoryFeeBtn = document.getElementById('updateCategoryFeeBtn');
            if (updateCategoryFeeBtn) {
                updateCategoryFeeBtn.addEventListener('click', updateCategoryFee);
            }

            // Reset form when modal is hidden
            const editCategoryFeeModal = document.getElementById('editCategoryFeeModal');
            if (editCategoryFeeModal) {
                editCategoryFeeModal.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('editCategoryFeeForm').reset();
                    document.getElementById('feeChangeSummary').classList.add('d-none');
                });
            }

            // Real-time fee calculation for edit modal
            const editNewFeeInput = document.getElementById('edit_new_fee');
            if (editNewFeeInput) {
                editNewFeeInput.addEventListener('input', updateFeeChangeSummary);
            }
        });

        // Load Class Details from your working API
        function loadClassDetails() {
            fetch(`/api/class-has-category-classes/class-category-class/${classId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const classDetailsDiv = document.getElementById('classDetails');

                    // Handle your API response format
                    let responseData = data.data || data;

                    if (responseData && responseData.length > 0 && responseData[0].student_class) {
                        const classData = responseData[0].student_class;

                        classDetailsDiv.innerHTML = `
                                                                <p class="mb-1"><strong>Class Name:</strong> ${classData.class_name || 'N/A'}</p>
                                                                <p class="mb-1"><strong>Teacher Name:</strong> ${classData.teacher ? classData.teacher.fname + ' ' + classData.teacher.lname : 'N/A'}</p>
                                                                <p class="mb-1"><strong>Subject:</strong> ${classData.subject ? classData.subject.subject_name : 'N/A'}</p>
                                                                <p class="mb-1"><strong>Grade:</strong> ${classData.grade ? classData.grade.grade_name : 'N/A'}</p>
                                                                <p class="mb-1"><strong>Status:</strong> 
                                                                    <span class="badge ${classData.is_active ? 'bg-success' : 'bg-secondary'}">
                                                                        ${classData.is_active ? 'Active' : 'Inactive'}
                                                                    </span>
                                                                    ${classData.is_ongoing ?
                                '<span class="badge bg-info ms-1">Ongoing</span>' :
                                '<span class="badge bg-light text-dark border ms-1">Not Ongoing</span>'
                            }
                                                                </p>
                                                                <p class="mb-0"><strong>Class ID:</strong> ${classId}</p>
                                                            `;

                        // Update page title with class name
                        document.querySelector('.card-title').innerHTML =
                            `<i class="fas fa-tags me-2"></i>Manage Categories & Fees for: ${classData.class_name}`;

                    } else {
                        // If no class data found in response, show basic info
                        classDetailsDiv.innerHTML = `
                                                                <p class="mb-0"><strong>Class ID:</strong> ${classId}</p>
                                                                <p class="mb-0 text-muted">No detailed class information available</p>
                                                            `;

                        document.querySelector('.card-title').innerHTML =
                            `<i class="fas fa-tags me-2"></i>Manage Categories & Fees for Class ID: ${classId}`;
                    }
                })
                .catch(error => {
                    console.error('Error loading class details:', error);
                    document.getElementById('classDetails').innerHTML = `
                                                            <p class="mb-0"><strong>Class ID:</strong> ${classId}</p>
                                                            <p class="mb-0 text-muted">Class details loading failed</p>
                                                        `;

                    document.querySelector('.card-title').innerHTML =
                        `<i class="fas fa-tags me-2"></i>Manage Categories & Fees for Class ID: ${classId}`;
                });
        }

        // Load Categories Dropdown
        function loadCategoriesDropdown() {
            fetch(`/api/categories/dropdown`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const categorySelect = document.getElementById('class_category_id');

                    // Handle your API response format
                    let categoriesData = [];
                    if (data.status === 'success' && data.data) {
                        categoriesData = data.data;
                    } else if (Array.isArray(data)) {
                        categoriesData = data;
                    } else if (data.categories) {
                        categoriesData = data.categories;
                    }

                    // Clear existing options except the first one
                    while (categorySelect.options.length > 1) {
                        categorySelect.remove(1);
                    }

                    categoriesData.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.category_name || category.name;
                        categorySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading categories dropdown:', error);
                    showAlert('Error loading categories. Please check console for details.', 'danger');
                });
        }

        // Add Category with Fee
        // Add Category with Fee (Enhanced version)
        async function addCategoryFee() {
            const addCategoryFeeBtn = document.getElementById('addCategoryFeeBtn');
            const originalText = addCategoryFeeBtn.innerHTML;

            const formData = new FormData(document.getElementById('addCategoryFeeForm'));
            const data = {
                student_classes_id: formData.get('student_classes_id'),
                class_category_id: formData.get('class_category_id'),
                fees: parseFloat(formData.get('fees'))
            };

            // Validation
            if (!data.class_category_id) {
                showAlert('Please select a category', 'warning');
                return;
            }

            if (!data.fees || data.fees <= 0) {
                showAlert('Please enter a valid fee amount', 'warning');
                return;
            }

            try {
                // Show loading state
                addCategoryFeeBtn.disabled = true;
                addCategoryFeeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';

                // Step 1: Add category fee
                const addFeeResponse = await fetch(`/api/class-has-category-classes`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!addFeeResponse.ok) {
                    const errorData = await addFeeResponse.json();
                    throw new Error(errorData.message || 'Failed to add category and fee');
                }

                const addFeeResult = await addFeeResponse.json();

                if (addFeeResult.status === 'success') {
                    // Clear form
                    document.getElementById('addCategoryFeeForm').reset();
                    document.getElementById('class_category_id').value = '';

                    // Reload assigned categories
                    loadAssignedCategories();

                    showAlert('Category and fee added successfully!', 'success');

                    // Step 2: Reactivate ongoing class
                    addCategoryFeeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Activating Class...';

                    const reactivateResult = await reactivateOngoingClass();

                    if (reactivateResult) {
                        showAlert('Category fee added and class activated successfully!', 'success');
                    } else {
                        showAlert('Category fee added successfully, but class activation failed.', 'warning');
                    }
                } else {
                    throw new Error(addFeeResult.message || 'Failed to add category and fee');
                }
            } catch (error) {
                console.error('Error adding category and fee:', error);

                if (error.errors) {
                    const errorMessage = Object.values(error.errors).flat().join(', ');
                    showAlert('Error: ' + errorMessage, 'danger');
                } else {
                    showAlert('Error adding category and fee: ' + error.message, 'danger');
                }
            } finally {
                // Restore button state
                addCategoryFeeBtn.disabled = false;
                addCategoryFeeBtn.innerHTML = originalText;
            }
        }

        // Reactivate Ongoing Class (Enhanced version)
        async function reactivateOngoingClass() {
            try {
                const response = await fetch(`/api/class-rooms/${classId}/reactivate-ongoing`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.warn('Failed to reactivate class:', errorData);
                    return null;
                }

                const data = await response.json();

                if (data && data.status === 'success') {
                    console.log('Class reactivated successfully:', data);
                    // Reload class details to reflect the ongoing status change
                    loadClassDetails();
                    return data;
                } else {
                    console.warn('Class reactivation response indicates failure:', data);
                    return null;
                }
            } catch (error) {
                console.error('Error reactivating class:', error);
                return null;
            }
        }

        // Load Assigned Categories
        function loadAssignedCategories() {
            showAssignedCategoriesLoading();

            fetch(`/api/class-has-category-classes/class-category-class/${classId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Handle your API response format
                    let assignedCategories = [];
                    if (data.status === 'success' && data.data) {
                        assignedCategories = data.data;
                    } else if (Array.isArray(data)) {
                        assignedCategories = data;
                    } else if (data.categories) {
                        assignedCategories = data.categories;
                    }

                    renderAssignedCategoriesTable(assignedCategories);
                    hideAssignedCategoriesLoading();
                })
                .catch(error => {
                    console.error('Error loading assigned categories:', error);
                    showAlert('Error loading assigned categories. Please check console for details.', 'danger');
                    hideAssignedCategoriesLoading();

                    // Show empty state
                    const emptyState = document.getElementById('assignedCategoriesEmpty');
                    if (emptyState) emptyState.classList.remove('d-none');
                });
        }

        function renderAssignedCategoriesTable(categories) {
            const tbody = document.getElementById('assignedCategoriesTableBody');
            const emptyState = document.getElementById('assignedCategoriesEmpty');

            if (!tbody) return;

            tbody.innerHTML = '';

            if (categories.length === 0) {
                emptyState.classList.remove('d-none');
                return;
            }

            emptyState.classList.add('d-none');

            categories.forEach((category, index) => {
                const categoryName = category.class_category ? category.class_category.category_name :
                    category.category_name || category.name || 'N/A';

                // Check if category name contains "+" mark
                const hasPlusMark = categoryName.includes('+');

                const row = `
                    <tr>
                        <td class="fw-bold text-muted">${index + 1}</td>
                        <td>${categoryName}</td>
                        <td class="text-end">
                            <span class="badge bg-success fs-6">
                                Rs. ${parseFloat(category.fees || category.fee || 0).toFixed(2)}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <!-- Edit Button - Always visible -->
                                <button class="btn btn-outline-warning" title="Edit Fee" 
                                        onclick="editCategoryFee(
                                            ${category.id}, // Class Category Has Student Class Id ID - class_category_has_student_class table ID
                                            ${category.class_category_id}, // category_id - class_categories table ID  
                                            ${parseFloat(category.fees || 0)}, 
                                            '${escapeHtml(categoryName)}'
                                        )">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- View Button - Hide for categories with + mark -->
                                ${!hasPlusMark ? `
                                <button class="btn btn-outline-primary" title="View Category Details" 
                                        onclick="viewCategoryDetails(
                                           ${category.id},//Class Category Has Student Class Id ID
                                            ${category.class_category_id}, // category_id - class_categories table ID
                                            '${escapeHtml(categoryName)}'
                                        )">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ` : ''}

                                <!-- Schedule Button - Hide for categories with + mark -->
                                ${!hasPlusMark ? `
                                <button class="btn btn-outline-info" title="Manage Schedule" 
                                        onclick="manageCategorySchedule(
                                            ${category.id}, // Class Category Has Student Class Id ID - class_category_has_student_class table ID
                                            ${category.class_category_id}, // category_id - class_categories table ID
                                            '${escapeHtml(categoryName)}'
                                        )">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        // Edit category fee - relationship එක update කිරීම
        function editCategoryFee(classCategoryHasStudentClassId, categoryId, currentFee, categoryName) {
            console.log('Class Category Has Student Class  ID (relationship):', classCategoryHasStudentClassId);
            console.log('Category ID (category):', categoryId);

            // Relationship ID එක use කරන්නේ update/delete operations සඳහා
            document.getElementById('edit_category_class_id').value = classCategoryHasStudentClassId;
            document.getElementById('edit_category_id').value = categoryId;
            document.getElementById('edit_current_fee').value = currentFee.toFixed(2);
            document.getElementById('edit_new_fee').value = currentFee.toFixed(2);
            document.getElementById('edit_category_name_display').textContent = categoryName;

            const modal = new bootstrap.Modal(document.getElementById('editCategoryFeeModal'));
            modal.show();
        }

        // View category details - category එක view කිරීම
        function viewCategoryDetails(classCategoryHasStudentClassId, categoryId, categoryName) {
            console.log('Managing schedule - Class Category Has Student Class Id ID:', classCategoryHasStudentClassId);
            console.log('Viewing category - Category ID:', categoryId);
            // Category ID එක use කරන්නේ category details බලාගැනීම සඳහා
            //showAlert(`Viewing details for category: ${categoryName} (Category ID: ${categoryId})`, 'info');
            window.location.href = `/class-attendances/${classCategoryHasStudentClassId}`;
        }

        // Manage category schedule - relationship එකට schedule add කිරීම  
        function manageCategorySchedule(classCategoryHasStudentClassId, categoryId, categoryName) {
            // console.log('Managing schedule - Association ID:', associationId);
            // console.log('Category ID:', categoryId);
            // // Association ID එක use කරන්නේ මේ specific relationship එකට schedule set කිරීම සඳහා
            // showAlert(`Managing schedule for: ${categoryName} (Association ID: ${associationId})`, 'info');
            window.location.href = `/class-attendances/create/${classCategoryHasStudentClassId}`;
        }

        // Update Fee Change Summary
        function updateFeeChangeSummary() {
            const currentFee = parseFloat(document.getElementById('edit_current_fee').value);
            const newFeeInput = document.getElementById('edit_new_fee');
            const newFee = parseFloat(newFeeInput.value) || 0;

            const feeChangeSummary = document.getElementById('feeChangeSummary');
            const oldFeeAmount = document.getElementById('oldFeeAmount');
            const newFeeAmount = document.getElementById('newFeeAmount');
            const feeDifference = document.getElementById('feeDifference');

            if (newFee !== currentFee) {
                const difference = newFee - currentFee;
                const differenceText = difference > 0 ?
                    `+Rs. ${Math.abs(difference).toFixed(2)}` :
                    `-Rs. ${Math.abs(difference).toFixed(2)}`;

                const differenceClass = difference > 0 ? 'text-success' : 'text-danger';

                oldFeeAmount.textContent = `Rs. ${currentFee.toFixed(2)}`;
                newFeeAmount.textContent = `Rs. ${newFee.toFixed(2)}`;
                feeDifference.innerHTML = `<span class="${differenceClass}">${differenceText}</span>`;

                feeChangeSummary.classList.remove('d-none');
            } else {
                feeChangeSummary.classList.add('d-none');
            }
        }

        // Update Category Fee
        function updateCategoryFee() {
            const updateBtn = document.getElementById('updateCategoryFeeBtn');
            const originalText = updateBtn.innerHTML;

            const categoryClassId = document.getElementById('edit_category_class_id').value;
            const categoryId = document.getElementById('edit_category_id').value;
            const newFee = parseFloat(document.getElementById('edit_new_fee').value);
            const currentFee = parseFloat(document.getElementById('edit_current_fee').value);

            // Validation
            if (!newFee || newFee <= 0) {
                document.getElementById('edit_new_fee').classList.add('is-invalid');
                document.getElementById('edit_new_fee_error').textContent = 'Please enter a valid fee amount';
                document.getElementById('edit_new_fee_error').style.display = 'block';
                return;
            }

            if (newFee === currentFee) {
                showAlert('Fee amount is the same as current fee. No changes made.', 'info');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryFeeModal'));
                modal.hide();
                return;
            }

            // Show loading state
            updateBtn.disabled = true;
            updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';

            fetch(`/api/class-has-category-classes/${categoryClassId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fees: newFee,
                    class_category_id: categoryId,
                    student_classes_id: classId
                })
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryFeeModal'));
                        modal.hide();

                        // Reload assigned categories
                        loadAssignedCategories();

                        showAlert(`Category fee updated successfully from Rs. ${currentFee.toFixed(2)} to Rs. ${newFee.toFixed(2)}!`, 'success');
                    } else {
                        throw new Error(data.message || 'Failed to update category fee');
                    }
                })
                .catch(error => {
                    console.error('Error updating category fee:', error);

                    if (error.errors) {
                        const errorMessage = Object.values(error.errors).flat().join(', ');
                        showAlert('Error: ' + errorMessage, 'danger');
                    } else {
                        showAlert('Error updating category fee: ' + error.message, 'danger');
                    }
                })
                .finally(() => {
                    // Restore button state
                    updateBtn.disabled = false;
                    updateBtn.innerHTML = originalText;
                });
        }

        // Remove Category Function
        function removeCategory(categoryClassId) {
            if (!confirm('Are you sure you want to remove this category from the class?')) {
                return;
            }

            fetch(`/api/class-has-category-classes/${categoryClassId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Reload assigned categories
                        loadAssignedCategories();
                        showAlert('Category removed successfully!', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to remove category');
                    }
                })
                .catch(error => {
                    console.error('Error removing category:', error);
                    showAlert('Error removing category: ' + error.message, 'danger');
                });
        }

        function showAssignedCategoriesLoading() {
            document.getElementById('assignedCategoriesLoading').classList.remove('d-none');
            document.getElementById('assignedCategoriesTableBody').closest('.table-responsive').classList.add('d-none');
        }

        function hideAssignedCategoriesLoading() {
            document.getElementById('assignedCategoriesLoading').classList.add('d-none');
            document.getElementById('assignedCategoriesTableBody').closest('.table-responsive').classList.remove('d-none');
        }

        // Helper Functions
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