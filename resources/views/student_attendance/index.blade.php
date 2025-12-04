@extends('layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Success/Error Messages -->
                <div id="attendanceMessages"></div>

                <!-- QR Scanner and Student ID Input Section -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-qrcode me-2"></i>Student Identification
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="qr-scanner-container text-center p-3 border rounded">
                                    <i class="fas fa-camera fa-3x text-muted mb-2"></i>
                                    <h6 class="mb-1">QR Code Scanner</h6>
                                    <p class="text-muted mb-2 small">Scan student QR code to auto-fill information</p>
                                    <button class="btn btn-outline-primary btn-sm" id="startScanner">
                                        <i class="fas fa-camera me-1"></i>Start QR Scanner
                                    </button>
                                    <div id="qr-reader" class="mt-2" style="display: none;"></div>
                                    <div id="qr-error" class="mt-1 text-danger small" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="manual-input-container">
                                    <h6 class="mb-3">Or Enter Student ID Manually</h6>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" id="studentCustomId"
                                            placeholder="Enter Student Custom ID (e.g., SA25087)" autocomplete="off">
                                        <button class="btn btn-primary btn-sm" type="button" id="searchStudent">
                                            <i class="fas fa-search me-1"></i>Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Information Display -->
                <div class="card mb-4 student-info-card" id="studentInfoCard" style="display: none;">
                    <div class="card-header bg-success text-white py-2">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-user-graduate me-2"></i>Student Information
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2" id="studentDetails">
                            <!-- Student details will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Available Classes Section -->
                <div class="card mb-4" id="ongoingClassesCard" style="display: none;">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Available Classes for Attendance
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3" id="ongoingClassesList">
                            <!-- Available classes will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- No Classes Message -->
                <div class="card mb-4" id="noClassesCard" style="display: none;">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                        <h6 class="text-muted mb-2">No Classes Available for Attendance</h6>
                        <p class="text-muted small mb-1">This student doesn't have any classes within the attendance window.
                        </p>
                        <p class="text-muted small">Attendance window: 1 hour before class start → class end time</p>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div class="text-center mt-4" id="loadingSpinner" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 small">Loading student information...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Confirmation Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white py-2">
                    <h6 class="modal-title mb-0" id="attendanceModalLabel">
                        <i class="fas fa-user-check me-2"></i>Confirm Attendance
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-check fa-2x text-warning mb-2"></i>
                        <h6 class="mb-1">Mark Attendance?</h6>
                        <p class="text-muted small mb-0">Are you sure you want to mark attendance for this class?</p>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body p-2">
                            <h6 class="card-title small mb-2">Class Details:</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Student</small>
                                    <p class="mb-0 small" id="modalStudentName">-</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Class</small>
                                    <p class="mb-0 small" id="modalClassName">-</p>
                                </div>
                            </div>
                            <div class="row g-2 mt-1">
                                <div class="col-6">
                                    <small class="text-muted d-block">Time</small>
                                    <p class="mb-0 small" id="modalClassTime">-</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Date</small>
                                    <p class="mb-0 small" id="modalClassDate">-</p>
                                </div>
                            </div>
                            <div class="row g-2 mt-1">
                                <div class="col-12">
                                    <small class="text-muted d-block">Status</small>
                                    <p class="mb-0 small">
                                        <span class="badge badge-sm" id="modalStudentStatusBadge">-</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Notification Info -->
                    <div class="alert alert-info alert-sm py-2 mb-2" id="smsNotificationInfo" style="display: none;">
                        <i class="fas fa-sms me-1"></i>
                        <span class="small" id="smsStatusText">SMS notification will be sent to guardian</span>
                    </div>

                    <form id="attendanceForm">
                        @csrf
                        <input type="hidden" id="attendance_student_id" name="student_id">
                        <input type="hidden" id="attendance_student_class_id" name="student_student_student_class_id">
                        <input type="hidden" id="attendance_class_id" name="class_attendance_id">
                        <input type="hidden" id="attendance_guardian_mobile" name="guardian_mobile">
                        <input type="hidden" id="attendance_student_name" name="student_name">
                        <input type="hidden" id="attendance_class_name" name="class_name">
                        <input type="hidden" id="attendance_class_time" name="class_time">
                    </form>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="confirmAttendanceBtn">
                        <i class="fas fa-check me-1"></i>Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .qr-scanner-container {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .student-info-card {
            border-radius: 8px;
        }

        .class-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            transition: all 0.2s ease;
            height: 100%;
        }

        .class-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-color: #adb5bd;
        }

        .ongoing-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 10px;
            padding: 2px 6px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        .attendance-btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            width: 100%;
        }

        .attendance-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .attendance-btn:hover:not(:disabled) {
            transform: translateY(-1px);
        }

        .time-badge {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: white;
            font-size: 10px;
            padding: 4px 8px;
        }

        .date-badge {
            background: linear-gradient(135deg, #6f42c1 0%, #5a2d9c 100%);
            color: white;
            font-size: 10px;
            padding: 4px 8px;
        }

        .status-badge-active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
        }

        .status-badge-inactive {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
        }

        .badge-sm {
            font-size: 10px;
            padding: 2px 6px;
        }

        .alert-sm {
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 4px;
        }

        .attendance-time-window {
            background: #e8f4fd;
            border-left: 3px solid #2196f3;
            padding: 6px 10px;
            border-radius: 4px;
            margin: 8px 0;
            font-size: 11px;
        }

        .attendance-disabled-message {
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            padding: 6px 10px;
            border-radius: 4px;
            margin: 8px 0;
            font-size: 11px;
        }

        .icon-wrapper {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
        }

        .detail-item {
            border-bottom: 1px solid #f8f9fa;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        #qr-reader {
            width: 100%;
            max-width: 250px;
            margin: 0 auto;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        class AttendanceSystem {
            constructor() {
                this.studentId = null;
                this.currentStudentData = null;
                this.qrScanner = null;
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                this.smsEnabled = false;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.checkSMSSettings();
            }

            setupEventListeners() {
                // QR Scanner
                const startScannerBtn = document.getElementById('startScanner');
                if (startScannerBtn) {
                    startScannerBtn.addEventListener('click', () => this.toggleQRScanner());
                }

                // Manual Search
                const searchStudentBtn = document.getElementById('searchStudent');
                if (searchStudentBtn) {
                    searchStudentBtn.addEventListener('click', () => this.searchStudent());
                }

                const studentCustomIdInput = document.getElementById('studentCustomId');
                if (studentCustomIdInput) {
                    studentCustomIdInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') this.searchStudent();
                    });
                }

                // Attendance Confirmation
                const confirmAttendanceBtn = document.getElementById('confirmAttendanceBtn');
                if (confirmAttendanceBtn) {
                    confirmAttendanceBtn.addEventListener('click', () => this.markAttendance());
                }
            }

            checkSMSSettings() {
                try {
                    const savedSettings = localStorage.getItem('sms_settings');
                    if (savedSettings) {
                        const settings = JSON.parse(savedSettings);
                        this.smsEnabled = settings.sms_enabled === true;
                        console.log('SMS Enabled:', this.smsEnabled);
                    }
                } catch (error) {
                    console.error('Error accessing SMS settings:', error);
                    this.smsEnabled = false;
                }
            }

            async toggleQRScanner() {
                const scannerBtn = document.getElementById('startScanner');
                const qrReader = document.getElementById('qr-reader');
                const qrError = document.getElementById('qr-error');

                if (!scannerBtn || !qrReader) return;

                if (this.qrScanner) {
                    this.stopQRScanner();
                    scannerBtn.innerHTML = '<i class="fas fa-camera me-1"></i>Start QR Scanner';
                    return;
                }

                try {
                    scannerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading Scanner...';

                    if (typeof Html5QrcodeScanner === 'undefined') {
                        throw new Error('QR Scanner library not available');
                    }

                    qrReader.style.display = 'block';
                    if (qrError) qrError.style.display = 'none';
                    scannerBtn.innerHTML = '<i class="fas fa-stop me-1"></i>Stop Scanner';

                    this.qrScanner = new Html5QrcodeScanner("qr-reader", {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        rememberLastUsedCamera: true,
                        showTorchButtonIfSupported: true
                    });

                    this.qrScanner.render(
                        (decodedText) => this.onQRCodeScanned(decodedText),
                        (error) => console.log('QR Scanner error:', error)
                    );

                } catch (error) {
                    console.error('QR Scanner initialization error:', error);
                    if (qrError) {
                        qrError.textContent = 'Unable to start QR scanner. Please refresh and try again.';
                        qrError.style.display = 'block';
                    }
                    if (scannerBtn) {
                        scannerBtn.innerHTML = '<i class="fas fa-camera me-1"></i>Start QR Scanner';
                    }
                    this.qrScanner = null;
                }
            }

            stopQRScanner() {
                if (this.qrScanner) {
                    this.qrScanner.clear().then(() => {
                        const qrReader = document.getElementById('qr-reader');
                        if (qrReader) qrReader.style.display = 'none';
                        this.qrScanner = null;
                    }).catch(console.error);
                }
            }

            onQRCodeScanned(decodedText) {
                const customId = this.extractCustomIdFromQR(decodedText);
                if (customId) {
                    const studentCustomIdInput = document.getElementById('studentCustomId');
                    if (studentCustomIdInput) {
                        studentCustomIdInput.value = customId;
                    }
                    this.searchStudent(customId);
                    this.stopQRScanner();
                }
            }

            extractCustomIdFromQR(qrText) {
                const match = qrText.match(/SA\d+/);
                return match ? match[0] : qrText;
            }

            searchStudent(customId = null) {
                try {
                    const searchId = customId || document.getElementById('studentCustomId')?.value.trim();

                    if (!searchId) {
                        this.showAlert('Please enter a student custom ID', 'warning');
                        return;
                    }

                    this.showLoading(true);
                    this.hideStudentInfo();

                    fetch(`/api/attendances/read-attendance?custom_id=${encodeURIComponent(searchId)}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Student not found or API error');
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                this.studentId = data.student_id;
                                this.currentStudentData = data;
                                this.displayStudentInfo(data);
                                this.displayOngoingClasses(data.data);
                                this.showAlert('Student information loaded successfully', 'success');
                            } else {
                                throw new Error(data.message || 'No data found');
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            this.showAlert('Error: ' + error.message, 'danger');
                        })
                        .finally(() => {
                            this.showLoading(false);
                        });
                } catch (error) {
                    console.error('Error in searchStudent:', error);
                    this.showAlert('Error: ' + error.message, 'danger');
                    this.showLoading(false);
                }
            }

            displayStudentInfo(data) {
                const student = data.data[0]?.student || null;
                if (!student) return;

                const studentDetails = document.getElementById('studentDetails');
                if (!studentDetails) return;

                const guardianMobile = student.guardian_mobile || 'Not available';

                studentDetails.innerHTML = `
                            <div class="col-md-2 text-center">
                                <img src="/uploads/logo/logo.png" alt="Student Photo" 
                                     class="img-thumbnail rounded-circle avatar-sm"
                                     onerror="this.src='/uploads/logo/logo.png'">
                            </div>
                            <div class="col-md-5">
                                <h6 class="mb-1 fw-bold">${student.first_name} ${student.last_name}</h6>
                                <p class="mb-1 small"><strong>Student ID:</strong> ${student.custom_id}</p>
                                <p class="mb-0 small"><strong>Guardian Mobile:</strong> ${guardianMobile}</p>
                            </div>
                            <div class="col-md-5">
                                <p class="mb-1 small"><strong>Status:</strong> 
                                    <span class="badge bg-success">Active</span>
                                </p>
                                <p class="mb-0 small"><strong>Available Classes:</strong> 
                                    <span class="badge bg-info">${data.data.length}</span>
                                </p>
                            </div>
                        `;

                const studentInfoCard = document.getElementById('studentInfoCard');
                if (studentInfoCard) studentInfoCard.style.display = 'block';
            }

            displayOngoingClasses(classesData) {
                const classesList = document.getElementById('ongoingClassesList');
                const ongoingClassesCard = document.getElementById('ongoingClassesCard');
                const noClassesCard = document.getElementById('noClassesCard');

                if (!classesList || !ongoingClassesCard || !noClassesCard) return;

                if (!classesData || classesData.length === 0) {
                    noClassesCard.style.display = 'block';
                    ongoingClassesCard.style.display = 'none';
                    return;
                }

                let html = '';

                classesData.forEach((classData) => {
                    const ongoingClass = classData.ongoing_class;
                    const studentStatus = classData.studentStudentStudentClass.student_class_status;

                    if (!ongoingClass) return;

                    const canMarkAttendance = studentStatus === 1;
                    const currentTime = ongoingClass.current_time || new Date().toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });

                    const startTime = ongoingClass.start_time;
                    const endTime = ongoingClass.end_time;

                    // Calculate 1 hour before
                    const startTimeHour = parseInt(startTime.split(':')[0]);
                    const isPM = startTime.includes('PM');
                    let oneHourBeforeHour = startTimeHour - 1;
                    if (oneHourBeforeHour <= 0) {
                        oneHourBeforeHour = 12;
                    }
                    const oneHourBeforeTime = `${oneHourBeforeHour}:${startTime.split(':')[1].split(' ')[0]} ${isPM ? 'PM' : 'AM'}`;

                    html += `
                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                                    <div class="class-card card h-100">
                                        <div class="card-header bg-info text-white py-2 position-relative">
                                            ${ongoingClass.is_ongoing == 1 ?
                            `<span class="ongoing-badge badge bg-danger">
                                                    <i class="fas fa-circle fa-xs me-1"></i>LIVE
                                                </span>` : ''
                        }
                                            <h6 class="card-title mb-0 fw-bold">${classData.category_name}</h6>
                                            <small class="opacity-75">${classData.student_class_name}</small>
                                        </div>

                                        <div class="card-body p-2">
                                            <div class="attendance-time-window">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted">Attendance Window</small>
                                                    <small class="text-muted">Now: ${currentTime}</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-center">
                                                        <div class="fw-bold small">${oneHourBeforeTime}</div>
                                                        <small class="text-muted">Start</small>
                                                    </div>
                                                    <i class="fas fa-arrow-right text-muted"></i>
                                                    <div class="text-center">
                                                        <div class="fw-bold small">${endTime}</div>
                                                        <small class="text-muted">End</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-2">
                                                <div class="col-6">
                                                    <div class="time-badge badge text-center">
                                                        <i class="fas fa-clock me-1"></i>
                                                        ${startTime}
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="date-badge badge text-center">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        ${ongoingClass.date}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="class-info mb-2">
                                                <div class="detail-item d-flex align-items-center">
                                                    <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle me-2">
                                                        <i class="fas fa-door-open text-info fa-xs"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block">Hall</small>
                                                        <span class="fw-semibold small">${ongoingClass.class_hall_name || 'Hall #' + ongoingClass.class_hall_id}</span>
                                                    </div>
                                                </div>

                                                <div class="detail-item d-flex align-items-center">
                                                    <div class="icon-wrapper ${canMarkAttendance ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10'} rounded-circle me-2">
                                                        <i class="fas ${canMarkAttendance ? 'fa-user-check text-success' : 'fa-user-times text-danger'} fa-xs"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block">Status</small>
                                                        <span class="fw-semibold small ${canMarkAttendance ? 'text-success' : 'text-danger'}">
                                                            ${canMarkAttendance ? 'Active' : 'Inactive'}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-2">
                                                <button class="btn ${canMarkAttendance ? 'btn-success' : 'btn-secondary'} attendance-btn btn-sm" 
                                                        data-student-id="${classData.student.id}"
                                                        data-student-class-id="${classData.studentStudentStudentClass.student_student_student_class_id}"
                                                        data-ongoing-class-id="${ongoingClass.id}"
                                                        data-student-name="${classData.student.first_name} ${classData.student.last_name}"
                                                        data-class-name="${classData.student_class_name}"
                                                        data-class-time="${ongoingClass.start_time} - ${ongoingClass.end_time}"
                                                        data-class-date="${ongoingClass.date}"
                                                        data-student-status="${studentStatus}"
                                                        data-guardian-mobile="${classData.student.guardian_mobile || ''}"
                                                        ${!canMarkAttendance ? 'disabled' : ''}>
                                                    <i class="fas ${canMarkAttendance ? 'fa-user-check' : 'fa-ban'} me-1"></i>
                                                    ${canMarkAttendance ? 'Mark Attendance' : 'Disabled'}
                                                </button>

                                                ${!canMarkAttendance ?
                            `<div class="attendance-disabled-message mt-1">
                                                        <i class="fas fa-exclamation-triangle text-warning fa-xs me-1"></i>
                                                        <small>Student enrollment is inactive</small>
                                                    </div>` : ''
                        }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                });

                classesList.innerHTML = html;
                ongoingClassesCard.style.display = 'block';
                noClassesCard.style.display = 'none';

                this.setupAttendanceButtonListeners();
            }

            setupAttendanceButtonListeners() {
                document.querySelectorAll('.attendance-btn:not(:disabled)').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const studentId = e.currentTarget.getAttribute('data-student-id');
                        const studentClassId = e.currentTarget.getAttribute('data-student-class-id');
                        const ongoingClassId = e.currentTarget.getAttribute('data-ongoing-class-id');
                        const studentName = e.currentTarget.getAttribute('data-student-name');
                        const className = e.currentTarget.getAttribute('data-class-name');
                        const classTime = e.currentTarget.getAttribute('data-class-time');
                        const classDate = e.currentTarget.getAttribute('data-class-date');
                        const studentStatus = e.currentTarget.getAttribute('data-student-status');
                        const guardianMobile = e.currentTarget.getAttribute('data-guardian-mobile');

                        this.openAttendanceModal(
                            studentId,
                            studentClassId,
                            ongoingClassId,
                            studentName,
                            className,
                            classTime,
                            classDate,
                            studentStatus,
                            guardianMobile
                        );
                    });
                });
            }

            openAttendanceModal(studentId, studentClassId, ongoingClassId, studentName, className, classTime, classDate, studentStatus, guardianMobile) {
                // Set modal values
                const attendanceStudentId = document.getElementById('attendance_student_id');
                const attendanceStudentClassId = document.getElementById('attendance_student_class_id');
                const attendanceClassId = document.getElementById('attendance_class_id');
                const attendanceGuardianMobile = document.getElementById('attendance_guardian_mobile');
                const attendanceStudentName = document.getElementById('attendance_student_name');
                const attendanceClassName = document.getElementById('attendance_class_name');
                const attendanceClassTime = document.getElementById('attendance_class_time');

                if (attendanceStudentId) attendanceStudentId.value = studentId;
                if (attendanceStudentClassId) attendanceStudentClassId.value = studentClassId;
                if (attendanceClassId) attendanceClassId.value = ongoingClassId;
                if (attendanceGuardianMobile) attendanceGuardianMobile.value = guardianMobile;
                if (attendanceStudentName) attendanceStudentName.value = studentName;
                if (attendanceClassName) attendanceClassName.value = className;
                if (attendanceClassTime) attendanceClassTime.value = classTime;

                // Set display information
                const modalStudentName = document.getElementById('modalStudentName');
                const modalClassName = document.getElementById('modalClassName');
                const modalClassTime = document.getElementById('modalClassTime');
                const modalClassDate = document.getElementById('modalClassDate');
                const statusBadge = document.getElementById('modalStudentStatusBadge');

                if (modalStudentName) modalStudentName.textContent = studentName;
                if (modalClassName) modalClassName.textContent = className;
                if (modalClassTime) modalClassTime.textContent = classTime;
                if (modalClassDate) modalClassDate.textContent = classDate;

                if (statusBadge) {
                    if (studentStatus == 1) {
                        statusBadge.className = 'badge badge-sm status-badge-active';
                        statusBadge.textContent = 'Active';
                    } else {
                        statusBadge.className = 'badge badge-sm status-badge-inactive';
                        statusBadge.textContent = 'Inactive';
                    }
                }

                // Show/hide SMS notification info
                const smsInfo = document.getElementById('smsNotificationInfo');
                const smsStatusText = document.getElementById('smsStatusText');

                if (smsInfo && smsStatusText) {
                    if (this.smsEnabled && guardianMobile && guardianMobile !== 'Not available') {
                        smsInfo.style.display = 'block';
                        smsInfo.className = 'alert alert-info alert-sm py-2 mb-2';
                        smsStatusText.textContent = `SMS will be sent to ${guardianMobile}`;
                    } else if (this.smsEnabled) {
                        smsInfo.style.display = 'block';
                        smsInfo.className = 'alert alert-warning alert-sm py-2 mb-2';
                        smsStatusText.textContent = 'SMS enabled but no guardian mobile';
                    } else {
                        smsInfo.style.display = 'none';
                    }
                }

                // Show modal
                const modalElement = document.getElementById('attendanceModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            }

            async markAttendance() {
                const btn = document.getElementById('confirmAttendanceBtn');
                const modalElement = document.getElementById('attendanceModal');
                const modal = bootstrap.Modal.getInstance(modalElement);

                if (!btn) return;

                // Disable button and show loading
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';

                try {
                    // IMPORTANT: Change field names to match backend expectations
                    const formData = {
                        student_id: document.getElementById('attendance_student_id')?.value,
                        student_student_student_classes: document.getElementById('attendance_student_class_id')?.value,  // Changed to "classes"
                        status: document.getElementById('attendance_class_id')?.value,  // Changed to "status"
                        _token: this.csrfToken
                    };

                    console.log('Sending attendance data (UPDATED):', formData); // Debug log

                    const response = await fetch('/api/attendances', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();
                    console.log('Response:', data); // Debug log

                    if (!response.ok) {
                        throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    }

                    if (data.status === 'success' || data.status === 'duplicate') {
                        if (data.status === 'success') {
                            this.showAlert('✅ Attendance marked successfully!', 'success');

                            // Send SMS if enabled
                            if (this.smsEnabled) {
                                const guardianMobile = document.getElementById('attendance_guardian_mobile')?.value;
                                const studentName = document.getElementById('attendance_student_name')?.value;
                                const className = document.getElementById('attendance_class_name')?.value;
                                const classTime = document.getElementById('attendance_class_time')?.value;

                                if (guardianMobile && guardianMobile !== 'Not available' && guardianMobile.length >= 10) {
                                    await this.sendSMSNotification(guardianMobile, studentName, className, classTime);
                                }
                            }

                            // Close modal
                            if (modal) {
                                modal.hide();
                            }

                            // Refresh student data
                            setTimeout(() => {
                                const customId = document.getElementById('studentCustomId')?.value;
                                if (customId) {
                                    this.searchStudent(customId);
                                }
                            }, 1500);
                        } else {
                            this.showAlert('⚠️ ' + data.message, 'warning');
                            if (modal) {
                                modal.hide();
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Attendance marking failed.');
                    }

                } catch (error) {
                    console.error('Attendance error:', error);

                    if (error.message.includes('duplicate') || error.message.includes('Duplicate')) {
                        this.showAlert('⚠️ Attendance already marked for this class!', 'warning');
                    } else if (error.message.includes('validation') || error.message.includes('Validation')) {
                        this.showAlert('❌ Validation error. Please check all fields.', 'danger');
                    } else if (error.message.includes('Something went wrong')) {
                        this.showAlert('❌ Server error. Please try again.', 'danger');
                    } else {
                        this.showAlert('❌ Error: ' + error.message, 'danger');
                    }
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }

            async sendSMSNotification(mobile, studentName, className, classTime) {
                try {
                    const message = `Dear Parent, ${studentName} has attended ${className} class at ${classTime}. Thank you.`;

                    const smsData = {
                        mobile: mobile,
                        message: message
                    };

                    const response = await fetch('/api/send-sms', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(smsData)
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        this.showAlert('📱 SMS sent to guardian', 'info');
                    }
                } catch (error) {
                    console.error('SMS sending error:', error);
                }
            }

            showLoading(show) {
                const loadingSpinner = document.getElementById('loadingSpinner');
                if (loadingSpinner) {
                    loadingSpinner.style.display = show ? 'block' : 'none';
                }
            }

            hideStudentInfo() {
                const studentInfoCard = document.getElementById('studentInfoCard');
                const ongoingClassesCard = document.getElementById('ongoingClassesCard');
                const noClassesCard = document.getElementById('noClassesCard');

                if (studentInfoCard) studentInfoCard.style.display = 'none';
                if (ongoingClassesCard) ongoingClassesCard.style.display = 'none';
                if (noClassesCard) noClassesCard.style.display = 'none';
            }

            showAlert(message, type) {
                let alertContainer = document.getElementById('attendanceMessages');
                if (!alertContainer) {
                    alertContainer = document.createElement('div');
                    alertContainer.id = 'attendanceMessages';
                    const container = document.querySelector('.container-fluid');
                    if (container) {
                        container.prepend(alertContainer);
                    }
                }

                const alertId = 'alert-' + Date.now();
                const alertDiv = document.createElement('div');
                alertDiv.id = alertId;
                alertDiv.className = `alert alert-${type} alert-dismissible fade show py-2`;
                alertDiv.innerHTML = `
                            <span class="small">${message}</span>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                        `;

                alertContainer.innerHTML = '';
                alertContainer.appendChild(alertDiv);

                setTimeout(() => {
                    const alert = document.getElementById(alertId);
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }
        }

        // Initialize the attendance system when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            window.attendanceSystem = new AttendanceSystem();
        });
    </script>
@endpush