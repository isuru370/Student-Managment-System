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
                <div class="card">
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
                                        Enable or disable automatic SMS notifications to parents/guardians when payments are successfully processed.
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
                                            When enabled, parents will receive an SMS confirmation after each successful payment.
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

                            <!-- SMS Message Template -->
                            <div class="row mb-4" id="smsTemplateSection" style="display: none;">
                                <div class="col-12">
                                    <h6 class="mb-3">SMS Message Template</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Message Preview:</label>
                                                <div class="border rounded p-3 bg-white">
                                                    <p class="mb-2" id="smsPreview">
                                                        Dear Parent/Guardian, payment of LKR: 5,000.00 has been made for Student Name at Success Edu.
                                                        - Grade 10 Mathematics
                                                        - 2024-01-15
                                                        Thank you for choosing us.
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-mobile-alt me-1"></i>
                                                        This message will be sent to the guardian's mobile number.
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="defaultAmount" class="form-label">Default Amount (LKR)</label>
                                                    <input type="number" class="form-control" id="defaultAmount" 
                                                           step="0.01" min="0" placeholder="Enter default amount">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="instituteName" class="form-label">Institute Name</label>
                                                    <input type="text" class="form-control" id="instituteName" 
                                                           placeholder="Enter institute name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Test SMS Section -->
                            <div class="row mb-4" id="testSmsSection" style="display: none;">
                                <div class="col-12">
                                    <h6 class="mb-3">Test SMS</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <label for="testPhoneNumber" class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control" id="testPhoneNumber" 
                                                           placeholder="Enter phone number (e.g., 94761234567)"
                                                           value="94761234567">
                                                    <small class="text-muted">
                                                        Enter the phone number to send test SMS (must start with 94)
                                                    </small>
                                                </div>
                                                <div class="col-md-4 d-flex align-items-end">
                                                    <button type="button" class="btn btn-outline-primary w-100" id="testSMSBtn">
                                                        <i class="fas fa-paper-plane me-2"></i>Send Test SMS
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- API Response Display -->
                                            <div id="apiResponseSection" style="display: none;">
                                                <div class="border rounded p-3 bg-white">
                                                    <h6 class="mb-2">SMS API Response:</h6>
                                                    <div class="response-content">
                                                        <pre id="apiResponse" class="mb-0 small"></pre>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            This shows the actual response from the SMS API
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" id="saveSettingsBtn">
                                        <i class="fas fa-save me-2"></i>Save Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>       
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Global SMS settings object
    const SMS_SETTINGS_KEY = 'sms_settings';

    // Fix for third-party script error
    window.addEventListener('error', function(e) {
        if (e.message.includes('giveFreely.tsx') && e.message.includes('payload')) {
            console.warn('Third-party script error caught and handled:', e.message);
            e.preventDefault();
            return true;
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const smsToggle = document.getElementById('smsToggle');
        const smsTemplateSection = document.getElementById('smsTemplateSection');
        const testSmsSection = document.getElementById('testSmsSection');
        const smsStatusText = document.getElementById('smsStatusText');
        const smsStatusBox = document.getElementById('smsStatusBox');
        const smsPreview = document.getElementById('smsPreview');
        const saveSettingsBtn = document.getElementById('saveSettingsBtn');
        const testSMSBtn = document.getElementById('testSMSBtn');
        const defaultAmount = document.getElementById('defaultAmount');
        const instituteName = document.getElementById('instituteName');
        const testPhoneNumber = document.getElementById('testPhoneNumber');
        const apiResponseSection = document.getElementById('apiResponseSection');
        const apiResponse = document.getElementById('apiResponse');

        // Load settings from local storage
        loadSmsSettings();

        // Toggle SMS template section
        smsToggle.addEventListener('change', function() {
            updateUI();
        });

        // Update SMS preview when inputs change
        defaultAmount.addEventListener('input', updateSMSPreview);
        instituteName.addEventListener('input', updateSMSPreview);

        // Format phone number to 94 format
        testPhoneNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            
            // Convert to 94 format
            if (value.startsWith('0')) {
                value = '94' + value.substring(1);
            } else if (value.startsWith('+94')) {
                value = '94' + value.substring(3);
            } else if (value.startsWith('94')) {
                // Already in correct format
            } else if (value.length > 0) {
                value = '94' + value;
            }
            
            // Limit to 11 digits (94 + 9 digits)
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            e.target.value = value;
        });

        function updateSMSPreview() {
            const amount = defaultAmount.value ? parseFloat(defaultAmount.value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00';
            
            const institute = instituteName.value || 'Success Edu';
            
            const preview = `Dear Parent/Guardian, payment of LKR: ${amount} has been made for Student Name at ${institute}.\n- Grade 10 Mathematics\n- ${new Date().toISOString().split('T')[0]}\nThank you for choosing us.`;
            
            smsPreview.textContent = preview;
        }

        function updateUI() {
            if (smsToggle.checked) {
                smsTemplateSection.style.display = 'block';
                testSmsSection.style.display = 'block';
                smsStatusText.textContent = 'Enabled';
                smsStatusBox.className = 'border rounded p-3 bg-success text-white';
            } else {
                smsTemplateSection.style.display = 'none';
                testSmsSection.style.display = 'none';
                smsStatusText.textContent = 'Disabled';
                smsStatusBox.className = 'border rounded p-3 bg-light';
            }
            updateSMSPreview();
        }

        function loadSmsSettings() {
            const savedSettings = localStorage.getItem(SMS_SETTINGS_KEY);
            
            if (savedSettings) {
                const settings = JSON.parse(savedSettings);
                smsToggle.checked = settings.sms_enabled || false;
                defaultAmount.value = settings.default_amount || '';
                instituteName.value = settings.institute_name || 'Success Edu';
            } else {
                // Default settings
                smsToggle.checked = false;
                defaultAmount.value = '5000.00';
                instituteName.value = 'Success Edu';
            }
            
            updateUI();
        }

        function saveSmsSettings() {
            const settings = {
                sms_enabled: smsToggle.checked,
                default_amount: defaultAmount.value,
                institute_name: instituteName.value,
                last_updated: new Date().toISOString()
            };
            
            localStorage.setItem(SMS_SETTINGS_KEY, JSON.stringify(settings));
            return settings;
        }

        // Save settings to local storage
        saveSettingsBtn.addEventListener('click', function() {
            try {
                const settings = saveSmsSettings();
                
                // Show success message with save confirmation
                showAlert('✅ SMS settings saved successfully! Settings will be preserved until you change them again.', 'success');
                
                console.log('SMS Settings Saved:', settings);
            } catch (error) {
                console.error('Error saving settings:', error);
                showAlert('❌ Failed to save settings: ' + error.message, 'danger');
            }
        });

        // Test SMS
        testSMSBtn.addEventListener('click', function() {
            if (!smsToggle.checked) {
                showAlert('Please enable SMS notifications first', 'warning');
                return;
            }

            if (!testPhoneNumber.value) {
                showAlert('Please enter a phone number', 'warning');
                return;
            }

            // Validate phone number format (must start with 94 and have 11 digits)
            const phoneRegex = /^94[0-9]{9}$/;
            if (!phoneRegex.test(testPhoneNumber.value)) {
                showAlert('Please enter a valid phone number in 94 format (e.g., 94761234567)', 'warning');
                return;
            }

            const message = smsPreview.textContent;
            const testData = {
                mobile: testPhoneNumber.value,
                message: message
            };

            // Hide previous response
            apiResponseSection.style.display = 'none';
            
            // Disable button and show loading
            const originalText = testSMSBtn.innerHTML;
            testSMSBtn.disabled = true;
            testSMSBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

            fetch('/api/send-sms', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(testData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Show API response
                apiResponseSection.style.display = 'block';
                apiResponse.textContent = JSON.stringify(data, null, 2);
                
                if (data.status === 'success') {
                    apiResponse.className = 'mb-0 small text-success';
                    showAlert('✅ Test SMS sent successfully to ' + testPhoneNumber.value + '!', 'success');
                } else {
                    apiResponse.className = 'mb-0 small text-danger';
                    showAlert('❌ SMS sending failed: ' + (data.message || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error sending test SMS:', error);
                
                // Show API response with error
                apiResponseSection.style.display = 'block';
                const errorResponse = {
                    status: 'error',
                    message: 'Network or API error',
                    error: error.message,
                    timestamp: new Date().toISOString()
                };
                apiResponse.textContent = JSON.stringify(errorResponse, null, 2);
                apiResponse.className = 'mb-0 small text-danger';
                
                showAlert('❌ Failed to send test SMS: ' + error.message, 'danger');
            })
            .finally(() => {
                testSMSBtn.disabled = false;
                testSMSBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Test SMS';
            });
        });

        // Global function to check SMS status from anywhere
        window.getSmsStatus = function() {
            try {
                const savedSettings = localStorage.getItem(SMS_SETTINGS_KEY);
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

        // Global function to get SMS template
        window.getSmsTemplate = function(studentName, className, amount, paymentDate) {
            try {
                const savedSettings = localStorage.getItem(SMS_SETTINGS_KEY);
                const institute = savedSettings ? JSON.parse(savedSettings).institute_name : 'Success Edu';
                
                const formattedAmount = parseFloat(amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                return `Dear Parent/Guardian, payment of LKR: ${formattedAmount} has been made for ${studentName} at ${institute}.\n- ${className}\n- ${paymentDate}\nThank you for choosing us.`;
            } catch (error) {
                console.error('Error generating SMS template:', error);
                return `Dear Parent/Guardian, payment of LKR: ${amount} has been made for ${studentName}.\n- ${className}\n- ${paymentDate}\nThank you for choosing us.`;
            }
        };

        // Global function to format phone number to 94 format
        window.formatPhoneTo94 = function(phoneNumber) {
            if (!phoneNumber) return '';
            
            let cleaned = phoneNumber.toString().replace(/\D/g, '');
            
            if (cleaned.startsWith('0')) {
                return '94' + cleaned.substring(1);
            } else if (cleaned.startsWith('+94')) {
                return '94' + cleaned.substring(3);
            } else if (cleaned.startsWith('94')) {
                return cleaned;
            } else {
                return '94' + cleaned;
            }
        };

        // Initialize
        updateSMSPreview();
    });

    function showAlert(message, type) {
        try {
            // Remove existing alerts
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                if (alert.parentNode) {
                    alert.remove();
                }
            });

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Safe DOM insertion - always append to card body
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.appendChild(alertDiv);
            } else {
                // Fallback to body
                document.body.appendChild(alertDiv);
            }
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
            
        } catch (error) {
            console.error('Error showing alert:', error);
            // Ultimate fallback
            alert(message);
        }
    }

    // Handle uncaught errors
    window.addEventListener('unhandledrejection', function(event) {
        if (event.reason && event.reason.message && event.reason.message.includes('payload')) {
            console.warn('Suppressed third-party promise rejection:', event.reason.message);
            event.preventDefault();
        }
    });

    // Example of how to use from other parts of your application:
    /*
    // Check if SMS is enabled
    if (window.getSmsStatus()) {
        console.log('SMS notifications are enabled');
        
        // Format phone number
        const formattedPhone = window.formatPhoneTo94('0768971213');
        console.log('Formatted phone:', formattedPhone); // 94768971213
        
        // Get SMS template
        const message = window.getSmsTemplate('John Doe', 'Grade 10 Mathematics', '5000.00', '2024-01-15');
        console.log('SMS Message:', message);
    }
    */
</script>

<style>
    .card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .btn-outline-primary {
        border-radius: 8px;
        padding: 10px 15px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .border.rounded {
        border-radius: 10px !important;
    }

    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }

    .form-switch .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }

    #smsPreview {
        white-space: pre-line;
        line-height: 1.5;
        font-family: system-ui, -apple-system, sans-serif;
    }

    #apiResponse {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        max-height: 200px;
        overflow-y: auto;
        font-family: 'Courier New', monospace;
        font-size: 12px;
    }

    .text-success {
        color: #198754 !important;
        font-weight: 600;
    }

    .text-danger {
        color: #dc3545 !important;
        font-weight: 600;
    }

    .alert {
        border-radius: 8px;
    }
</style>
@endpush