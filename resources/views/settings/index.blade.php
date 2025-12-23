@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">System Settings</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- SMS Settings Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sms me-2"></i>SMS Notification Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="smsSettingsForm">
                            <!-- SMS Toggle Switch -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h6 class="mb-2">Payment Success SMS Notifications</h6>
                                    <p class="text-muted mb-3">
                                        Enable or disable automatic SMS notifications to parents/guardians when payments are
                                        successfully processed.
                                    </p>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="smsToggle" name="sms_enabled">
                                        <label class="form-check-label fw-semibold" for="smsToggle">
                                            Send SMS after successful payment
                                        </label>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            When enabled, parents will receive an SMS confirmation after each successful
                                            payment.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="border rounded p-3 bg-light" id="smsStatusBox">
                                        <i class="fas fa-sms fa-2x mb-2"></i>
                                        <h5 class="mb-1" id="smsStatusText">Disabled</h5>
                                        <small>SMS Notifications</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Save SMS Settings Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" id="saveSmsSettingsBtn">
                                        <i class="fas fa-save me-2"></i>Save SMS Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Email Notification Settings Card -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-envelope me-2"></i>Email Notification Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="emailSettingsForm">
                            <!-- Email Toggle Switch -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h6 class="mb-2">Payment Success Email Notifications</h6>
                                    <p class="text-muted mb-3">
                                        Enable or disable automatic email notifications to parents/guardians when payments
                                        are successfully processed.
                                    </p>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emailToggle"
                                            name="email_enabled">
                                        <label class="form-check-label fw-semibold" for="emailToggle">
                                            Send Email after successful payment
                                        </label>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            When enabled, parents will receive an email confirmation after each successful
                                            payment.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="border rounded p-3 bg-light" id="emailStatusBox">
                                        <i class="fas fa-envelope fa-2x mb-2"></i>
                                        <h5 class="mb-1" id="emailStatusText">Disabled</h5>
                                        <small>Email Notifications</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Save Email Settings Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success" id="saveEmailSettingsBtn">
                                        <i class="fas fa-save me-2"></i>Save Email Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Teacher Receipt Printing Settings Card -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>Teacher Receipt Printing
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="teacherReceiptSettingsForm">
                            <!-- Teacher Receipt Toggle Switch -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h6 class="mb-2">Auto Print Teacher Salary Receipts</h6>
                                    <p class="text-muted mb-3">
                                        Enable or disable automatic receipt printing after successful teacher salary
                                        payments.
                                    </p>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="teacherReceiptToggle"
                                            name="teacher_receipt_enabled">
                                        <label class="form-check-label fw-semibold" for="teacherReceiptToggle">
                                            Auto-print teacher salary receipt
                                        </label>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            When enabled, teacher salary receipts will automatically print after successful
                                            payments.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="border rounded p-3 bg-light" id="teacherReceiptStatusBox">
                                        <i class="fas fa-print fa-2x mb-2"></i>
                                        <h5 class="mb-1" id="teacherReceiptStatusText">Disabled</h5>
                                        <small>Teacher Receipts</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Save Teacher Receipt Settings Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-info text-white"
                                        id="saveTeacherReceiptSettingsBtn">
                                        <i class="fas fa-save me-2"></i>Save Teacher Receipt Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Student Fee Receipt Printing Settings Card -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-graduate me-2"></i>Student Fee Receipt Printing
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="studentFeeReceiptSettingsForm">
                            <!-- Student Fee Receipt Toggle Switch -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h6 class="mb-2">Auto Print Student Fee Receipts</h6>
                                    <p class="text-muted mb-3">
                                        Enable or disable automatic receipt printing after successful student fee payments.
                                    </p>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="studentFeeReceiptToggle"
                                            name="student_fee_receipt_enabled">
                                        <label class="form-check-label fw-semibold" for="studentFeeReceiptToggle">
                                            Auto-print student fee receipt
                                        </label>
                                    </div>

                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            When enabled, student fee receipts will automatically print after successful
                                            payments.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="border rounded p-3 bg-light" id="studentFeeReceiptStatusBox">
                                        <i class="fas fa-receipt fa-2x mb-2"></i>
                                        <h5 class="mb-1" id="studentFeeReceiptStatusText">Disabled</h5>
                                        <small>Student Receipts</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Save Student Fee Receipt Settings Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-warning text-white"
                                        id="saveStudentFeeReceiptSettingsBtn">
                                        <i class="fas fa-save me-2"></i>Save Student Fee Receipt Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Clear All Settings Button -->
                <div class="card">
                    <div class="card-header bg-danger">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trash-alt me-2"></i>Reset Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This action will clear all your saved settings and restore defaults.
                        </div>
                        <button type="button" class="btn btn-danger" id="clearAllSettingsBtn">
                            <i class="fas fa-trash me-2"></i>Clear All Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Global settings keys
        const SETTINGS_KEYS = {
            SMS: 'sms_settings',
            EMAIL: 'email_settings',
            TEACHER_RECEIPT: 'teacher_receipt_settings',
            STUDENT_FEE_RECEIPT: 'student_fee_receipt_settings'
        };

        document.addEventListener('DOMContentLoaded', function () {
            // SMS Elements
            const smsToggle = document.getElementById('smsToggle');
            const smsStatusText = document.getElementById('smsStatusText');
            const smsStatusBox = document.getElementById('smsStatusBox');
            const saveSmsSettingsBtn = document.getElementById('saveSmsSettingsBtn');

            // Email Elements
            const emailToggle = document.getElementById('emailToggle');
            const emailStatusText = document.getElementById('emailStatusText');
            const emailStatusBox = document.getElementById('emailStatusBox');
            const saveEmailSettingsBtn = document.getElementById('saveEmailSettingsBtn');

            // Teacher Receipt Elements
            const teacherReceiptToggle = document.getElementById('teacherReceiptToggle');
            const teacherReceiptStatusText = document.getElementById('teacherReceiptStatusText');
            const teacherReceiptStatusBox = document.getElementById('teacherReceiptStatusBox');
            const saveTeacherReceiptSettingsBtn = document.getElementById('saveTeacherReceiptSettingsBtn');

            // Student Fee Receipt Elements
            const studentFeeReceiptToggle = document.getElementById('studentFeeReceiptToggle');
            const studentFeeReceiptStatusText = document.getElementById('studentFeeReceiptStatusText');
            const studentFeeReceiptStatusBox = document.getElementById('studentFeeReceiptStatusBox');
            const saveStudentFeeReceiptSettingsBtn = document.getElementById('saveStudentFeeReceiptSettingsBtn');

            // Clear button
            const clearAllSettingsBtn = document.getElementById('clearAllSettingsBtn');

            // Load all settings
            loadAllSettings();

            // SMS Event Listeners
            smsToggle.addEventListener('change', updateSmsUI);
            saveSmsSettingsBtn.addEventListener('click', saveSmsSettings);

            // Email Event Listeners
            emailToggle.addEventListener('change', updateEmailUI);
            saveEmailSettingsBtn.addEventListener('click', saveEmailSettings);

            // Teacher Receipt Event Listeners
            teacherReceiptToggle.addEventListener('change', updateTeacherReceiptUI);
            saveTeacherReceiptSettingsBtn.addEventListener('click', saveTeacherReceiptSettings);

            // Student Fee Receipt Event Listeners
            studentFeeReceiptToggle.addEventListener('change', updateStudentFeeReceiptUI);
            saveStudentFeeReceiptSettingsBtn.addEventListener('click', saveStudentFeeReceiptSettings);

            // Clear settings listener
            clearAllSettingsBtn.addEventListener('click', clearAllSettings);

            // Helper function to show alerts
            function showAlert(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
                alertDiv.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;

                const cardBody = document.querySelector('.card-body');
                if (cardBody) {
                    cardBody.appendChild(alertDiv);
                } else {
                    document.body.appendChild(alertDiv);
                }

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            // Load all settings from localStorage
            function loadAllSettings() {
                loadSmsSettings();
                loadEmailSettings();
                loadTeacherReceiptSettings();
                loadStudentFeeReceiptSettings();
            }

            // SMS Functions
            function loadSmsSettings() {
                const savedSettings = localStorage.getItem(SETTINGS_KEYS.SMS);

                if (savedSettings) {
                    const settings = JSON.parse(savedSettings);
                    smsToggle.checked = settings.sms_enabled || false;
                } else {
                    smsToggle.checked = false;
                }

                updateSmsUI();
            }

            function updateSmsUI() {
                if (smsToggle.checked) {
                    smsStatusText.textContent = 'Enabled';
                    smsStatusBox.className = 'border rounded p-3 bg-success text-white';
                } else {
                    smsStatusText.textContent = 'Disabled';
                    smsStatusBox.className = 'border rounded p-3 bg-light';
                }
            }

            function saveSmsSettings() {
                const settings = {
                    sms_enabled: smsToggle.checked,
                    last_updated: new Date().toISOString()
                };

                localStorage.setItem(SETTINGS_KEYS.SMS, JSON.stringify(settings));
                showAlert('✅ SMS settings saved successfully!', 'success');
                console.log('SMS Settings Saved:', settings);
            }

            // Email Functions
            function loadEmailSettings() {
                const savedSettings = localStorage.getItem(SETTINGS_KEYS.EMAIL);

                if (savedSettings) {
                    const settings = JSON.parse(savedSettings);
                    emailToggle.checked = settings.email_enabled || false;
                } else {
                    emailToggle.checked = false;
                }

                updateEmailUI();
            }

            function updateEmailUI() {
                if (emailToggle.checked) {
                    emailStatusText.textContent = 'Enabled';
                    emailStatusBox.className = 'border rounded p-3 bg-success text-white';
                } else {
                    emailStatusText.textContent = 'Disabled';
                    emailStatusBox.className = 'border rounded p-3 bg-light';
                }
            }

            function saveEmailSettings() {
                const settings = {
                    email_enabled: emailToggle.checked,
                    last_updated: new Date().toISOString()
                };

                localStorage.setItem(SETTINGS_KEYS.EMAIL, JSON.stringify(settings));
                showAlert('✅ Email settings saved successfully!', 'success');
                console.log('Email Settings Saved:', settings);
            }

            // Teacher Receipt Functions
            function loadTeacherReceiptSettings() {
                const savedSettings = localStorage.getItem(SETTINGS_KEYS.TEACHER_RECEIPT);

                if (savedSettings) {
                    const settings = JSON.parse(savedSettings);
                    teacherReceiptToggle.checked = settings.teacher_receipt_enabled || false;
                } else {
                    teacherReceiptToggle.checked = false;
                }

                updateTeacherReceiptUI();
            }

            function updateTeacherReceiptUI() {
                if (teacherReceiptToggle.checked) {
                    teacherReceiptStatusText.textContent = 'Enabled';
                    teacherReceiptStatusBox.className = 'border rounded p-3 bg-success text-white';
                } else {
                    teacherReceiptStatusText.textContent = 'Disabled';
                    teacherReceiptStatusBox.className = 'border rounded p-3 bg-light';
                }
            }

            function saveTeacherReceiptSettings() {
                const settings = {
                    teacher_receipt_enabled: teacherReceiptToggle.checked,
                    last_updated: new Date().toISOString()
                };

                localStorage.setItem(SETTINGS_KEYS.TEACHER_RECEIPT, JSON.stringify(settings));
                showAlert('✅ Teacher receipt settings saved successfully!', 'success');
                console.log('Teacher Receipt Settings Saved:', settings);
            }

            // Student Fee Receipt Functions
            function loadStudentFeeReceiptSettings() {
                const savedSettings = localStorage.getItem(SETTINGS_KEYS.STUDENT_FEE_RECEIPT);

                if (savedSettings) {
                    const settings = JSON.parse(savedSettings);
                    studentFeeReceiptToggle.checked = settings.student_fee_receipt_enabled || false;
                } else {
                    studentFeeReceiptToggle.checked = false;
                }

                updateStudentFeeReceiptUI();
            }

            function updateStudentFeeReceiptUI() {
                if (studentFeeReceiptToggle.checked) {
                    studentFeeReceiptStatusText.textContent = 'Enabled';
                    studentFeeReceiptStatusBox.className = 'border rounded p-3 bg-success text-white';
                } else {
                    studentFeeReceiptStatusText.textContent = 'Disabled';
                    studentFeeReceiptStatusBox.className = 'border rounded p-3 bg-light';
                }
            }

            function saveStudentFeeReceiptSettings() {
                const settings = {
                    student_fee_receipt_enabled: studentFeeReceiptToggle.checked,
                    last_updated: new Date().toISOString()
                };

                localStorage.setItem(SETTINGS_KEYS.STUDENT_FEE_RECEIPT, JSON.stringify(settings));
                showAlert('✅ Student fee receipt settings saved successfully!', 'success');
                console.log('Student Fee Receipt Settings Saved:', settings);
            }

            // Clear all settings
            function clearAllSettings() {
                if (confirm('Are you sure you want to clear ALL settings? This action cannot be undone.')) {
                    localStorage.removeItem(SETTINGS_KEYS.SMS);
                    localStorage.removeItem(SETTINGS_KEYS.EMAIL);
                    localStorage.removeItem(SETTINGS_KEYS.TEACHER_RECEIPT);
                    localStorage.removeItem(SETTINGS_KEYS.STUDENT_FEE_RECEIPT);
                    loadAllSettings();
                    showAlert('✅ All settings have been cleared!', 'success');
                }
            }

            // Global utility functions for other pages to use
            window.getSmsStatus = function () {
                try {
                    const savedSettings = localStorage.getItem(SETTINGS_KEYS.SMS);
                    if (savedSettings) {
                        const settings = JSON.parse(savedSettings);
                        return settings.sms_enabled || false;
                    }
                    return false;
                } catch (error) {
                    console.error('Error getting SMS status:', error);
                    return false;
                }
            };

            window.getEmailStatus = function () {
                try {
                    const savedSettings = localStorage.getItem(SETTINGS_KEYS.EMAIL);
                    if (savedSettings) {
                        const settings = JSON.parse(savedSettings);
                        return settings.email_enabled || false;
                    }
                    return false;
                } catch (error) {
                    console.error('Error getting Email status:', error);
                    return false;
                }
            };

            window.getTeacherReceiptStatus = function () {
                try {
                    const savedSettings = localStorage.getItem(SETTINGS_KEYS.TEACHER_RECEIPT);
                    if (savedSettings) {
                        const settings = JSON.parse(savedSettings);
                        return settings.teacher_receipt_enabled || false;
                    }
                    return false;
                } catch (error) {
                    console.error('Error getting Teacher Receipt status:', error);
                    return false;
                }
            };

            window.getStudentFeeReceiptStatus = function () {
                try {
                    const savedSettings = localStorage.getItem(SETTINGS_KEYS.STUDENT_FEE_RECEIPT);
                    if (savedSettings) {
                        const settings = JSON.parse(savedSettings);
                        return settings.student_fee_receipt_enabled || false;
                    }
                    return false;
                } catch (error) {
                    console.error('Error getting Student Fee Receipt status:', error);
                    return false;
                }
            };
        });
    </script>

    <style>
        .card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
        }

        .form-switch .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }

        .alert {
            border-radius: 8px;
            z-index: 1000;
        }
    </style>
@endpush