@extends('layouts.app')

@section('title', 'Generate Student ID')
@section('page-title', 'Generate Student ID')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Generate Student ID</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ID Card Styles */
        .student-id-card {
            width: 86mm;
            height: 54mm;
            background: url('{{ asset('uploads/id/idcard_bg.png') }}') no-repeat center;
            background-size: cover;
            border-radius: 3mm;
            padding: 3mm;
            box-shadow: 0 2mm 5mm rgba(0, 0, 0, .25);
            margin: 0 auto;
            position: relative;
        }

        .id-card-profile-box {
            width: 18mm;
            height: 22mm;
            border: 0.3mm solid #ccc;
            border-radius: 1mm;
            overflow: hidden;
            background: #fff;
        }

        .id-card-profile-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .id-card-student-id {
            font-family: 'Monbaiti', serif;
            font-size: 4.5mm;
            font-weight: bold;
            line-height: 1.1;
        }

        .id-card-student-name {
            font-family: 'Monbaiti', serif;
            font-size: 4.3mm;
            line-height: 1.2;
        }

        .id-card-address {
            font-family: 'Monbaiti', serif;
            font-size: 3mm;
            line-height: 1.2;
        }

        .id-card-qr-img {
            width: 18mm;
            height: 18mm;
            background: #fff;
            padding: 1mm;
            border-radius: 1mm;
        }

        .id-card-logo {
            width: 4mm;
        }

        /* Modal ID Card Styles */
        .modal-id-card {
            width: 100%;
            max-width: 86mm;
            height: 54mm;
            background: url('{{ asset('uploads/id/idcard_bg.png') }}') no-repeat center;
            background-size: cover;
            border-radius: 3mm;
            padding: 3mm;
            box-shadow: 0 2mm 5mm rgba(0, 0, 0, .25);
            margin: 0 auto;
        }

        /* Bulk Preview Grid */
        .bulk-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90mm, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .bulk-preview-item {
            page-break-inside: avoid;
        }

        @media print {
            .bulk-preview-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <!-- Advanced Search and Sort Form -->
    <form method="GET" action="{{ route('student-id-card.ganarateStudentId') }}" class="mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Advanced Search & Sort</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search by ID, name, or address"
                            value="{{ $searchTerm ?? '' }}">
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}">
                    </div>

                    <!-- Sort By -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Sort By</label>
                        <select name="sort_by" class="form-select">
                            <option value="created_at" {{ ($sortBy ?? 'created_at') == 'created_at' ? 'selected' : '' }}>
                                Registration Date</option>
                            <option value="custom_id" {{ ($sortBy ?? '') == 'custom_id' ? 'selected' : '' }}>Student ID
                            </option>
                            <option value="lname" {{ ($sortBy ?? '') == 'lname' ? 'selected' : '' }}>Last Name</option>
                            <option value="fname" {{ ($sortBy ?? '') == 'fname' ? 'selected' : '' }}>First Name</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Sort Order</label>
                        <select name="sort_order" class="form-select">
                            <option value="desc" {{ ($sortOrder ?? 'desc') == 'desc' ? 'selected' : '' }}>Newest First
                            </option>
                            <option value="asc" {{ ($sortOrder ?? '') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('student-id-card.clear-filters') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Clear Filters
                        </a>
                        <span class="ms-3 text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Showing {{ $students ? $students->count() : 0 }} students
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title mb-0">Generate Student ID Cards</h4>
                    <p class="text-muted mb-0">Select students to generate ID cards in bulk</p>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-primary" onclick="generateSelectedCards()">
                        <i class="fas fa-id-card"></i> Generate Selected
                    </button>
                    <button type="button" class="btn btn-success" onclick="generateAllCards()">
                        <i class="fas fa-id-card-alt"></i> Generate All
                    </button>
                    <button type="button" class="btn btn-warning text-white" onclick="previewSelectedCards()">
                        <i class="fas fa-eye"></i> Preview Selected
                    </button>
                    <button type="button" class="btn btn-info" onclick="downloadSelectedCards()">
                        <i class="fas fa-download"></i> Download Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Client-side Search and Filter Section (for UI filtering within current results) -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Filter within results by ID, Name, or Address...">
                        <button class="btn btn-outline-secondary" type="button" onclick="searchStudents()">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="date" id="dateFilter" class="form-control" placeholder="Filter by Created Date">
                        <button class="btn btn-outline-secondary" type="button" onclick="filterByDate()">
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-info py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="selectedCount">0</span> students selected out of
                                <span id="totalCount">{{ $students ? $students->count() : 0 }}</span> total
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                    <i class="fas fa-square"></i> Deselect All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Grid -->
            <div class="row" id="studentsGrid">
                @if($students && $students->count() > 0)
                    @foreach($students as $student)
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4 student-card" data-id="{{ $student['custom_id'] }}"
                            data-name="{{ strtolower($student['fname'] . ' ' . $student['lname']) }}"
                            data-address="{{ strtolower($student['address']) }}" data-created="{{ $student['created_at'] ?? '' }}"
                            data-student='@json($student)'>
                            <div class="card h-100">
                                <div class="card-body p-3">
                                    <!-- Checkbox -->
                                    <div class="form-check mb-3">
                                        <input class="form-check-input student-checkbox" type="checkbox"
                                            value="{{ $student['custom_id'] }}" id="student_{{ $student['custom_id'] }}">
                                        <label class="form-check-label fw-bold" for="student_{{ $student['custom_id'] }}">
                                            {{ $student['custom_id'] }}
                                        </label>
                                    </div>

                                    <!-- Student ID Card Embedded -->
                                    <div class="mb-3">
                                        <div class="student-id-card">
                                            <div class="row h-100">
                                                <!-- LEFT -->
                                                <div class="col-8 d-flex flex-column">
                                                    <div class="id-card-profile-box mt-1 ms-1">
                                                        @php
                                                            // Fix image path issue
                                                            $defaultImage = asset('uploads/logo/logo.png');
                                                            $studentImage = null;

                                                            if (isset($student['img_url']) && !empty($student['img_url'])) {
                                                                // Check if it's already a full URL
                                                                if (filter_var($student['img_url'], FILTER_VALIDATE_URL)) {
                                                                    $studentImage = $student['img_url'];
                                                                } else {
                                                                    // Remove leading slash if present
                                                                    $imagePath = ltrim($student['img_url'], '/');
                                                                    $studentImage = asset($imagePath);
                                                                }
                                                            } else {
                                                                $studentImage = $defaultImage;
                                                            }
                                                        @endphp

                                                        <img src="{{ $studentImage }}" alt="Student Photo"
                                                            onerror="this.onerror=null;this.src='{{ $defaultImage }}'">
                                                    </div>

                                                    <div class="ms-1 mt-3">
                                                        <div class="id-card-student-id">{{ $student['custom_id'] ?? 'N/A' }}</div>
                                                        <div class="id-card-student-name mt-1">
                                                            {{ $student['lname'] }}
                                                        </div>
                                                        <div class="id-card-address mt-1">
                                                            {{ Str::limit($student['address'] ?? 'Address not available', 40) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- RIGHT -->
                                                <div class="col-4 d-flex flex-column align-items-center">
                                                    @php
                                                        $qrData = $student['custom_id'] ?? 'N/A';
                                                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=' . urlencode($qrData);
                                                    @endphp

                                                    <img src="{{ $qrUrl }}" class="id-card-qr-img mt-1" alt="QR Code"
                                                        crossorigin="anonymous">

                                                    <img src="{{ asset('uploads/logo/logo.png') }}"
                                                        class="id-card-logo mt-auto mb-1" alt="Logo">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Student Details -->
                                    <div class="student-details mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $student['fname'] }} {{ $student['lname'] }}
                                            </small>
                                            @if(isset($student['created_at']))
                                                <small class="text-muted">
                                                    <i class="far fa-calendar me-1"></i>
                                                    {{ \Carbon\Carbon::parse($student['created_at'])->format('Y-m-d') }}
                                                </small>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ Str::limit($student['address'], 50) }}
                                        </small>
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-flex justify-content-between mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary preview-single-card"
                                            data-student-id="{{ $student['custom_id'] }}">
                                            <i class="fas fa-expand"></i> Preview
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info toggle-select"
                                            data-id="{{ $student['custom_id'] }}">
                                            <i class="fas fa-check"></i> Select
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No students found. Please add students first.
                        </div>
                    </div>
                @endif
            </div>

            <!-- No Results Message (Hidden by default) -->
            <div id="noResults" class="d-none">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-search me-2"></i>
                        No students found matching your search criteria.
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Footer -->
        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                        <label class="form-check-label" for="selectAllCheckbox">
                            Select/Deselect All
                        </label>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-warning text-white" onclick="previewSelectedCards()">
                        <i class="fas fa-eye"></i> Preview Selected ( <span id="selectedBadge"
                            class="badge bg-white text-warning">0</span> )
                    </button>
                    <button type="button" class="btn btn-info" onclick="downloadSelectedCards()">
                        <i class="fas fa-download"></i> Download Selected
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Card Preview Modal -->
    <div class="modal fade" id="singleCardModal" tabindex="-1" aria-labelledby="singleCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="singleCardModalLabel">Student ID Card Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="modalCardContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadSingleCard()">
                        <i class="fas fa-download"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Preview Modal -->
    <div class="modal fade" id="bulkPreviewModal" tabindex="-1" aria-labelledby="bulkPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkPreviewModalLabel">
                        <i class="fas fa-id-card-alt me-2"></i> Bulk ID Cards Preview
                        (<span id="bulkPreviewCount">0</span> cards)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="downloadAsPNG()">
                                <i class="fas fa-file-image"></i> Download as PNG
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="downloadAsJPG()">
                                <i class="fas fa-file-image"></i> Download as JPG
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="generatePDF()">
                                <i class="fas fa-file-pdf"></i> Download as PDF
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="printBulkCards()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                        <div class="form-check form-check-inline ms-3">
                            <input class="form-check-input" type="checkbox" id="includeGridLines" checked>
                            <label class="form-check-label" for="includeGridLines">Show grid lines</label>
                        </div>
                    </div>

                    <div id="bulkPreviewContainer" class="bulk-preview-grid">
                        <!-- Cards will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mb-3">Generating ID Cards...</h5>
                    <p class="text-muted" id="loadingMessage">Please wait while we prepare your ID cards for download.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Responsive ID Card */
        @media (max-width: 1200px) {
            .student-id-card {
                transform: scale(0.9);
                transform-origin: center;
            }
        }

        @media (max-width: 768px) {
            .student-id-card {
                transform: scale(0.8);
            }
        }

        /* Card hover effect */
        .student-card .card:hover {
            border-color: #3498db;
        }

        .student-card .card {
            transition: all 0.3s ease;
        }

        /* Modal styles */
        .modal-id-card {
            transform: scale(1.2);
            transform-origin: center;
            margin: 20px auto;
        }

        @media (max-width: 768px) {
            .modal-id-card {
                transform: scale(1);
            }
        }

        /* Grid lines for bulk preview */
        .grid-line {
            border: 1px dashed #ddd;
            padding: 5px;
        }
    </style>
@endpush

@push('scripts')
    <!-- Load html2canvas FIRST -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- Load jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Load SweetAlert2 LAST -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global variables
        let selectedStudentForSinglePreview = null;
        let selectedStudentsForBulk = [];

        // Debug: Check if html2canvas is loaded
        console.log('html2canvas loaded:', typeof html2canvas !== 'undefined');
        console.log('SweetAlert2 loaded:', typeof Swal !== 'undefined');

        document.addEventListener('DOMContentLoaded', function () {
            updateSelectedCount();

            // Select All Checkbox
            document.getElementById('selectAllCheckbox').addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.student-checkbox:not(:disabled)');
                const isChecked = this.checked;

                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                    updateCardSelection(checkbox.value, isChecked);
                });

                updateSelectedCount();
            });

            // Individual checkbox change
            document.querySelectorAll('.student-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    updateCardSelection(this.value, this.checked);
                    updateSelectedCount();
                    updateSelectAllCheckbox();
                });
            });

            // Toggle select buttons
            document.querySelectorAll('.toggle-select').forEach(button => {
                button.addEventListener('click', function () {
                    const studentId = this.getAttribute('data-id');
                    const checkbox = document.querySelector(`#student_${studentId}`);
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                });
            });

            // Single card preview buttons
            document.querySelectorAll('.preview-single-card').forEach(button => {
                button.addEventListener('click', function () {
                    const studentId = this.getAttribute('data-student-id');
                    previewSingleCard(studentId);
                });
            });

            // Search input enter key
            document.getElementById('searchInput').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    searchStudents();
                }
            });

            // Date filter enter key
            document.getElementById('dateFilter').addEventListener('change', function () {
                if (this.value) {
                    filterByDate();
                }
            });
        });

        // Preview single card
        function previewSingleCard(studentId) {
            const cardElement = document.querySelector(`.student-card[data-id="${studentId}"]`);
            if (!cardElement) return;

            try {
                const studentData = JSON.parse(cardElement.getAttribute('data-student'));
                selectedStudentForSinglePreview = studentData;

                const modalContainer = document.getElementById('modalCardContainer');
                modalContainer.innerHTML = generateCardHTML(studentData);

                const modal = new bootstrap.Modal(document.getElementById('singleCardModal'));
                modal.show();
            } catch (error) {
                console.error('Error previewing single card:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to preview ID card. Please try again.',
                    confirmButtonColor: '#d33',
                });
            }
        }

        // Preview selected cards
        function previewSelectedCards() {
            const selectedIds = getSelectedStudentIds();

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one student to preview.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            selectedStudentsForBulk = [];
            const bulkContainer = document.getElementById('bulkPreviewContainer');
            bulkContainer.innerHTML = '';

            // Get student data for selected IDs
            selectedIds.forEach(studentId => {
                const cardElement = document.querySelector(`.student-card[data-id="${studentId}"]`);
                if (cardElement) {
                    try {
                        const studentData = JSON.parse(cardElement.getAttribute('data-student'));
                        selectedStudentsForBulk.push(studentData);

                        // Add card to bulk preview
                        const cardDiv = document.createElement('div');
                        cardDiv.className = 'bulk-preview-item';
                        cardDiv.innerHTML = generateCardHTML(studentData);
                        bulkContainer.appendChild(cardDiv);
                    } catch (error) {
                        console.error('Error parsing student data:', error);
                    }
                }
            });

            document.getElementById('bulkPreviewCount').textContent = selectedStudentsForBulk.length;

            const modal = new bootstrap.Modal(document.getElementById('bulkPreviewModal'));
            modal.show();
        }

        // Generate card HTML with fixed image paths
        function generateCardHTML(student) {
            const defaultImage = '{{ asset('uploads/logo/logo.png') }}';
            let studentImage = defaultImage;

            // Fix image path
            if (student.img_url && student.img_url.trim() !== '') {
                if (student.img_url.startsWith('http') || student.img_url.startsWith('//')) {
                    studentImage = student.img_url;
                } else {
                    // Remove leading slash if present
                    const cleanPath = student.img_url.startsWith('/') ? student.img_url.substring(1) : student.img_url;
                    studentImage = '{{ asset('') }}' + cleanPath;
                }
            }

            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=${encodeURIComponent(student.custom_id || 'N/A')}`;

            return `
                    <div class="student-id-card">
                        <div class="row h-100">
                            <div class="col-8 d-flex flex-column">
                                <div class="id-card-profile-box mt-1 ms-1">
                                    <img src="${studentImage}" 
                                         alt="Student Photo" 
                                         crossorigin="anonymous"
                                         onerror="this.onerror=null;this.src='${defaultImage}'">
                                </div>
                                <div class="ms-1 mt-3">
                                    <div class="id-card-student-id">${student.custom_id || 'N/A'}</div>
                                    <div class="id-card-student-name mt-1">${student.lname || ''}</div>
                                    <div class="id-card-address mt-1">${student.address ? student.address.substring(0, 40) : 'Address not available'}</div>
                                </div>
                            </div>
                            <div class="col-4 d-flex flex-column align-items-center">
                                <img src="${qrUrl}" class="id-card-qr-img mt-1" alt="QR Code" crossorigin="anonymous">
                                <img src="{{ asset('uploads/logo/logo.png') }}" class="id-card-logo mt-auto mb-1" alt="Logo">
                            </div>
                        </div>
                    </div>
                `;
        }

        // Download single card
        function downloadSingleCard() {
            if (!selectedStudentForSinglePreview) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Card Selected',
                    text: 'Please select a card to download.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            showLoading('Downloading ID card...');

            const cardContainer = document.getElementById('modalCardContainer');
            const cardElement = cardContainer.querySelector('.student-id-card');

            if (!cardElement) {
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Card element not found.',
                    confirmButtonColor: '#d33',
                });
                return;
            }

            // Check if html2canvas is available
            if (typeof html2canvas === 'undefined') {
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Library Error',
                    text: 'html2canvas library is not loaded. Please refresh the page.',
                    confirmButtonColor: '#d33',
                });
                return;
            }

            html2canvas(cardElement, {
                scale: 4,
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                logging: true,
                onclone: function (clonedDoc) {
                    // Fix images in cloned document
                    const images = clonedDoc.querySelectorAll('img');
                    images.forEach(img => {
                        img.crossOrigin = 'anonymous';
                    });
                }
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = `ID_${selectedStudentForSinglePreview.custom_id || 'student'}_${Date.now()}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                hideLoading();

                Swal.fire({
                    icon: 'success',
                    title: 'Download Complete',
                    text: 'ID card downloaded successfully!',
                    confirmButtonColor: '#3085d6',
                    timer: 2000
                });
            }).catch(error => {
                console.error('Download failed:', error);
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Download Failed',
                    text: 'Failed to download ID card. Please try again.',
                    confirmButtonColor: '#d33',
                });
            });
        }

        // Download selected cards
        function downloadSelectedCards() {
            const selectedIds = getSelectedStudentIds();

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one student to download.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            // Show download options
            Swal.fire({
                title: 'Download Options',
                text: `You have selected ${selectedIds.length} ID card(s).`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Download All as PDF',
                cancelButtonText: 'Download Individual PNGs',
                showDenyButton: true,
                denyButtonText: 'Download as Single Image',
            }).then((result) => {
                if (result.isConfirmed) {
                    generatePDF(selectedIds);
                } else if (result.isDenied) {
                    downloadAsSingleImage(selectedIds);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    downloadIndividualCards(selectedIds);
                }
            });
        }

        // Download as single image
        function downloadAsSingleImage(selectedIds) {
            showLoading('Creating single image file...');

            // Create a container for all cards
            const tempContainer = document.createElement('div');
            tempContainer.style.cssText = `
                    position: absolute;
                    left: -9999px;
                    top: -9999px;
                    width: 100%;
                    padding: 20px;
                    background: white;
                `;

            selectedIds.forEach(studentId => {
                const cardElement = document.querySelector(`.student-card[data-id="${studentId}"] .student-id-card`);
                if (cardElement) {
                    const clone = cardElement.cloneNode(true);
                    clone.style.marginBottom = '20px';
                    tempContainer.appendChild(clone);
                }
            });

            document.body.appendChild(tempContainer);

            html2canvas(tempContainer, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = `ID_Cards_Bulk_${Date.now()}.png`;
                link.click();

                // Clean up
                document.body.removeChild(tempContainer);
                hideLoading();

                Swal.fire({
                    icon: 'success',
                    title: 'Download Complete',
                    text: 'All ID cards downloaded as single image!',
                    confirmButtonColor: '#3085d6',
                    timer: 2000
                });
            }).catch(error => {
                console.error('Download failed:', error);
                document.body.removeChild(tempContainer);
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Download Failed',
                    text: 'Failed to generate image. Please try again.',
                    confirmButtonColor: '#d33',
                });
            });
        }

        // Download as PNG from bulk preview
        function downloadAsPNG() {
            const container = document.getElementById('bulkPreviewContainer');
            if (!container.children.length) return;

            showLoading('Generating PNG file...');

            html2canvas(container, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = `ID_Cards_Bulk_${Date.now()}.png`;
                link.click();
                hideLoading();

                Swal.fire({
                    icon: 'success',
                    title: 'Download Complete',
                    text: 'PNG file downloaded successfully!',
                    confirmButtonColor: '#3085d6',
                    timer: 2000
                });
            }).catch(error => {
                console.error('Download failed:', error);
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Download Failed',
                    text: 'Failed to generate PNG file. Please try again.',
                    confirmButtonColor: '#d33',
                });
            });
        }

        // Download as JPG from bulk preview
        function downloadAsJPG() {
            const container = document.getElementById('bulkPreviewContainer');
            if (!container.children.length) return;

            showLoading('Generating JPG file...');

            html2canvas(container, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/jpeg', 0.9);
                link.download = `ID_Cards_Bulk_${Date.now()}.jpg`;
                link.click();
                hideLoading();

                Swal.fire({
                    icon: 'success',
                    title: 'Download Complete',
                    text: 'JPG file downloaded successfully!',
                    confirmButtonColor: '#3085d6',
                    timer: 2000
                });
            }).catch(error => {
                console.error('Download failed:', error);
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Download Failed',
                    text: 'Failed to generate JPG file. Please try again.',
                    confirmButtonColor: '#d33',
                });
            });
        }

        // Generate PDF
        async function generatePDF(selectedIds = null) {
            const ids = selectedIds || getSelectedStudentIds();
            if (ids.length === 0) return;

            showLoading(`Generating PDF with ${ids.length} ID cards...`);

            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');

                let yPos = 10;
                let xPos = 10;
                const cardWidth = 86;
                const cardHeight = 54;
                const margin = 10;

                for (let i = 0; i < ids.length; i++) {
                    const studentId = ids[i];
                    const cardElement = document.querySelector(`.student-card[data-id="${studentId}"] .student-id-card`);

                    if (cardElement) {
                        const canvas = await html2canvas(cardElement, {
                            scale: 2,
                            useCORS: true,
                            allowTaint: true,
                            backgroundColor: null,
                            logging: false
                        });

                        const imgData = canvas.toDataURL('image/jpeg', 0.9);

                        // Check if we need a new page
                        if (i % 2 === 0 && i !== 0) {
                            doc.addPage();
                            yPos = 10;
                            xPos = 10;
                        }

                        // Calculate position
                        const currentX = (i % 2 === 0) ? xPos : xPos + cardWidth + margin;

                        // Add image to PDF
                        doc.addImage(imgData, 'JPEG', currentX, yPos, cardWidth, cardHeight);

                        // If we placed 2 cards, move to next row
                        if (i % 2 === 1) {
                            yPos += cardHeight + margin;
                        }
                    }

                    // Show progress
                    updateLoadingMessage(`Processing card ${i + 1} of ${ids.length}...`);
                }

                // Save PDF
                doc.save(`ID_Cards_${Date.now()}.pdf`);
                hideLoading();

                Swal.fire({
                    icon: 'success',
                    title: 'PDF Generated',
                    text: `PDF with ${ids.length} ID cards downloaded successfully!`,
                    confirmButtonColor: '#3085d6',
                    timer: 3000
                });

            } catch (error) {
                console.error('PDF generation failed:', error);
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'PDF Generation Failed',
                    text: 'Failed to generate PDF. Please try again.',
                    confirmButtonColor: '#d33',
                });
            }
        }

        // Download individual cards
        function downloadIndividualCards(selectedIds) {
            showLoading(`Downloading ${selectedIds.length} individual cards...`);

            let downloaded = 0;
            const total = selectedIds.length;

            selectedIds.forEach((studentId, index) => {
                setTimeout(() => {
                    const cardElement = document.querySelector(`.student-card[data-id="${studentId}"] .student-id-card`);
                    if (cardElement) {
                        html2canvas(cardElement, {
                            scale: 4,
                            useCORS: true,
                            allowTaint: true,
                            backgroundColor: null,
                            logging: false
                        }).then(canvas => {
                            const link = document.createElement('a');
                            link.href = canvas.toDataURL('image/png');
                            link.download = `ID_${studentId}_${Date.now()}.png`;
                            link.style.display = 'none';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            downloaded++;
                            updateLoadingMessage(`Downloaded ${downloaded} of ${total} cards...`);

                            if (downloaded === total) {
                                hideLoading();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Download Complete',
                                    text: `Successfully downloaded ${total} ID cards.`,
                                    confirmButtonColor: '#3085d6',
                                    timer: 3000
                                });
                            }
                        }).catch(error => {
                            console.error(`Failed to download card ${studentId}:`, error);
                            downloaded++;
                            if (downloaded === total) {
                                hideLoading();
                            }
                        });
                    } else {
                        downloaded++;
                        if (downloaded === total) {
                            hideLoading();
                        }
                    }
                }, index * 500); // Stagger downloads
            });
        }

        // Print bulk cards
        function printBulkCards() {
            const container = document.getElementById('bulkPreviewContainer');
            const printContent = container.innerHTML;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Print ID Cards</title>
                        <style>
                            body { 
                                margin: 0; 
                                padding: 20px; 
                                background: white;
                            }
                            .bulk-preview-grid {
                                display: grid;
                                grid-template-columns: repeat(2, 1fr);
                                gap: 10px;
                            }
                            .student-id-card {
                                width: 86mm !important;
                                height: 54mm !important;
                                page-break-inside: avoid;
                                break-inside: avoid;
                            }
                            @media print {
                                @page { 
                                    margin: 10mm;
                                    size: A4;
                                }
                                body { margin: 0; }
                            }
                        </style>
                    </head>
                    <body>
                        ${printContent}
                        <script>
                            window.onload = function() {
                                window.print();
                                setTimeout(function() {
                                    window.close();
                                }, 1000);
                            };
                        <\/script>
                    </body>
                    </html>
                `);
            printWindow.document.close();
        }

        // Helper functions
        function getSelectedStudentIds() {
            return Array.from(document.querySelectorAll('.student-checkbox:checked'))
                .map(checkbox => checkbox.value);
        }

        function showLoading(message = 'Please wait...') {
            document.getElementById('loadingMessage').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
            modal.show();
        }

        function updateLoadingMessage(message) {
            document.getElementById('loadingMessage').textContent = message;
        }

        function hideLoading() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
            if (modal) {
                modal.hide();
            }
        }

        function updateCardSelection(studentId, isSelected) {
            const card = document.querySelector(`.student-card[data-id="${studentId}"]`);
            if (card) {
                if (isSelected) {
                    card.querySelector('.card').classList.add('border-primary', 'shadow-sm');
                    card.querySelector('.toggle-select').classList.replace('btn-outline-info', 'btn-info');
                    card.querySelector('.toggle-select').innerHTML = '<i class="fas fa-check"></i> Selected';
                } else {
                    card.querySelector('.card').classList.remove('border-primary', 'shadow-sm');
                    card.querySelector('.toggle-select').classList.replace('btn-info', 'btn-outline-info');
                    card.querySelector('.toggle-select').innerHTML = '<i class="fas fa-check"></i> Select';
                }
            }
        }

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.student-checkbox:checked').length;
            const total = document.querySelectorAll('.student-checkbox').length;

            document.getElementById('selectedCount').textContent = selected;
            document.getElementById('selectedBadge').textContent = selected;
            document.getElementById('totalCount').textContent = total;
        }

        function updateSelectAllCheckbox() {
            const checkboxes = document.querySelectorAll('.student-checkbox:not(:disabled)');
            const checked = document.querySelectorAll('.student-checkbox:checked:not(:disabled)');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');

            if (checkboxes.length === checked.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checked.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        function selectAll() {
            document.querySelectorAll('.student-checkbox:not(:disabled)').forEach(checkbox => {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });
            document.getElementById('selectAllCheckbox').checked = true;
            document.getElementById('selectAllCheckbox').indeterminate = false;
        }

        function deselectAll() {
            document.querySelectorAll('.student-checkbox:not(:disabled)').forEach(checkbox => {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            });
            document.getElementById('selectAllCheckbox').checked = false;
            document.getElementById('selectAllCheckbox').indeterminate = false;
        }

        // Client-side filtering functions (within current results)
        function searchStudents() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.student-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                const address = card.getAttribute('data-address');
                const id = card.getAttribute('data-id');

                if (searchTerm === '' ||
                    name.includes(searchTerm) ||
                    address.includes(searchTerm) ||
                    id.includes(searchTerm)) {
                    card.classList.remove('d-none');
                    visibleCount++;
                } else {
                    card.classList.add('d-none');
                }
            });

            const noResults = document.getElementById('noResults');
            if (visibleCount === 0 && searchTerm !== '') {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }

            updateVisibleStats();
        }

        function filterByDate() {
            const dateFilter = document.getElementById('dateFilter').value;
            const cards = document.querySelectorAll('.student-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const createdDate = card.getAttribute('data-created');

                if (!dateFilter || !createdDate) {
                    card.classList.remove('d-none');
                    visibleCount++;
                } else if (createdDate.startsWith(dateFilter)) {
                    card.classList.remove('d-none');
                    visibleCount++;
                } else {
                    card.classList.add('d-none');
                }
            });

            const noResults = document.getElementById('noResults');
            if (visibleCount === 0 && dateFilter !== '') {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }

            updateVisibleStats();
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('dateFilter').value = '';

            document.querySelectorAll('.student-card').forEach(card => {
                card.classList.remove('d-none');
            });

            document.getElementById('noResults').classList.add('d-none');
            updateVisibleStats();
        }

        function updateVisibleStats() {
            const visibleCards = document.querySelectorAll('.student-card:not(.d-none)');
            const visibleCount = visibleCards.length;
            const totalCount = document.querySelectorAll('.student-card').length;

            document.getElementById('totalCount').textContent = visibleCount;

            const visibleSelected = Array.from(visibleCards).filter(card => {
                const checkbox = card.querySelector('.student-checkbox');
                return checkbox && checkbox.checked;
            }).length;

            document.getElementById('selectedCount').textContent = visibleSelected;
        }

        // Server-side generation functions
        function generateSelectedCards() {
            const selectedIds = getSelectedStudentIds();

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one student to generate ID cards.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            showLoading();

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("student-id-card.generate.bulk") }}';
            form.style.display = 'none';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Pass sort parameters
            const sortByInput = document.createElement('input');
            sortByInput.type = 'hidden';
            sortByInput.name = 'sort_by';
            sortByInput.value = '{{ $sortBy ?? "created_at" }}';
            form.appendChild(sortByInput);

            const sortOrderInput = document.createElement('input');
            sortOrderInput.type = 'hidden';
            sortOrderInput.name = 'sort_order';
            sortOrderInput.value = '{{ $sortOrder ?? "desc" }}';
            form.appendChild(sortOrderInput);

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        function generateAllCards() {
            showLoading();
            // Pass sort parameters to server
            const url = new URL('{{ route("student-id-card.generate.all") }}');
            url.searchParams.append('sort_by', '{{ $sortBy ?? "created_at" }}');
            url.searchParams.append('sort_order', '{{ $sortOrder ?? "desc" }}');
            window.location.href = url.toString();
        }
    </script>

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
            });
        </script>
    @endif

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
            });
        </script>
    @endif
@endpush