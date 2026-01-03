<!DOCTYPE html>
<html>

<head>
    <title>Receipt #{{ $data['id'] }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #10b981;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --border-color: #e5e7eb;
            --success-color: #059669;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .receipt-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .receipt-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), 0 1px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
            transition: transform 0.3s ease;
        }

        .receipt-card:hover {
            transform: translateY(-5px);
        }

        .receipt-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .receipt-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.2;
        }

        .academy-logo {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .academy-logo i {
            font-size: 32px;
            color: #fbbf24;
        }

        .receipt-title {
            font-size: 18px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .receipt-subtitle {
            font-size: 13px;
            opacity: 0.8;
            font-weight: 400;
        }

        .receipt-body {
            padding: 25px;
        }

        .receipt-id {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--light-color);
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }

        .receipt-id-label {
            font-weight: 600;
            color: var(--dark-color);
        }

        .receipt-id-value {
            font-weight: 700;
            font-size: 16px;
            color: var(--primary-color);
            background: white;
            padding: 4px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .info-section {
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title i {
            color: var(--primary-color);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 6px 0;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            flex: 1;
        }

        .info-value {
            font-weight: 500;
            color: var(--dark-color);
            flex: 1;
            text-align: right;
        }

        .highlight {
            background: linear-gradient(to right, rgba(37, 99, 235, 0.05), transparent);
            border-radius: 6px;
            padding: 8px 12px;
        }

        .payment-details {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border: 1px solid var(--border-color);
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }

        .payment-row:last-child {
            border-bottom: none;
        }

        .payment-row.total {
            border-top: 2px solid var(--primary-color);
            border-bottom: none;
            padding-top: 15px;
            margin-top: 10px;
            font-weight: 700;
            font-size: 16px;
            color: var(--dark-color);
        }

        .payment-label {
            font-weight: 600;
        }

        .payment-amount {
            font-weight: 600;
            color: var(--success-color);
        }

        .total-amount {
            color: var(--secondary-color);
            font-size: 18px;
        }

        .receipt-footer {
            text-align: center;
            padding: 20px;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
            border-radius: 0 0 16px 16px;
        }

        .footer-text {
            color: #666;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .watermark {
            position: absolute;
            bottom: 20px;
            right: 20px;
            opacity: 0.05;
            font-size: 80px;
            font-weight: 900;
            color: var(--primary-color);
            transform: rotate(-15deg);
            pointer-events: none;
            user-select: none;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            min-width: 140px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-color), #0da271);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-3px);
        }

        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--success-color);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(5, 150, 105, 0.3);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-container {
                max-width: 100%;
            }

            .receipt-card {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .watermark {
                opacity: 0.1;
            }

            @page {
                size: auto;
                margin: 0;
            }
        }

        @media (max-width: 480px) {
            .receipt-body {
                padding: 20px 15px;
            }

            .btn {
                min-width: 100%;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
    @if(request()->has('print'))
        <script>
            window.onload = function () {
                window.print();
                setTimeout(function () {
                    window.close();
                }, 500);
            }
        </script>
    @endif
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-card">
            <!-- Status Badge -->
            <div class="status-badge no-print">
                <i class="fas fa-check-circle"></i> PAID
            </div>

            <!-- Watermark -->
            <div class="watermark">PAID</div>

            <!-- Header -->
            <div class="receipt-header">
                <div class="academy-logo">
                    <i class="fas fa-graduation-cap"></i>
                    SAVIDYA EDUCATION
                </div>
                <div class="receipt-title">OFFICIAL PAYMENT RECEIPT</div>
                <div class="receipt-subtitle">Education Excellence Since 2010</div>
            </div>

            <!-- Body -->
            <div class="receipt-body">
                <!-- Receipt ID -->
                <div class="receipt-id">
                    <div class="receipt-id-label">RECEIPT NUMBER</div>
                    <div class="receipt-id-value">#{{ str_pad($data['id'], 6, '0', STR_PAD_LEFT) }}</div>
                </div>

                <!-- Receipt Info -->
                <div class="info-section">
                    <div class="section-title">
                        <i class="fas fa-receipt"></i>
                        RECEIPT INFORMATION
                    </div>
                    <div class="info-row highlight">
                        <div class="info-label">Issued Date</div>
                        <div class="info-value">{{ date('d/m/Y', strtotime($data['payment_date'])) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Transaction Time</div>
                        <div class="info-value">{{ date('h:i A', strtotime($data['payment_date'])) }}</div>
                    </div>
                </div>

                <!-- Student Info -->
                <div class="info-section">
                    <div class="section-title">
                        <i class="fas fa-user-graduate"></i>
                        STUDENT INFORMATION
                    </div>
                    <div class="info-row highlight">
                        <div class="info-label">Student Name</div>
                        <div class="info-value">{{ $data['student']['fname'] }} {{ $data['student']['lname'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Student ID</div>
                        <div class="info-value">{{ $data['student']['custom_id'] }}</div>
                    </div>
                </div>

                <!-- Class Info -->
                <div class="info-section">
                    <div class="section-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        CLASS INFORMATION
                    </div>
                    <div class="info-row highlight">
                        <div class="info-label">Class Name</div>
                        <div class="info-value">{{ $data['student_class']['class_name'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Grade & Subject</div>
                        <div class="info-value">Grade {{ $data['student_class']['grade'] }} -
                            {{ $data['student_class']['subject'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Program Category</div>
                        <div class="info-value">
                            {{ $data['class_category_has_student_class']['class_category']['category_name'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Payment Period</div>
                        <div class="info-value">{{ $data['payment_for'] }}</div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="payment-details">
                    <div class="section-title">
                        <i class="fas fa-money-bill-wave"></i>
                        PAYMENT DETAILS
                    </div>

                    <div class="payment-row">
                        <div class="payment-label">Class Tuition Fee</div>
                        <div class="payment-amount">Rs.
                            {{ number_format($data['class_category_has_student_class']['fees'], 2) }}</div>
                    </div>

                    <div class="payment-row">
                        <div class="payment-label">Hall Facility Charges</div>
                        <div class="payment-amount">Rs. {{ number_format($data['hall_price'], 2) }}</div>
                    </div>

                    <div class="payment-row total">
                        <div class="payment-label">TOTAL AMOUNT PAID</div>
                        <div class="payment-amount total-amount">Rs. {{ number_format($data['total'], 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="receipt-footer">
                <div class="footer-text">
                    <i class="fas fa-shield-alt"></i> This is an official computer-generated receipt
                </div>
                <div class="footer-text">
                    Generated on {{ date('d/m/Y h:i A') }}
                </div>
                <div class="footer-text" style="margin-top: 10px;">
                    <strong>Savidya Education</strong> | Mirigama, Sri Lanka<br>
                    Tel: +94 11 234 5678 | Email: account@successacademy.lk
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="actions no-print">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Receipt
            </button>

            <a href="{{ route('receipt.download', $data['id']) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Download PDF
            </a>

            <button class="btn btn-secondary" onclick="window.close()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>

        <!-- Additional Info -->
        <div class="no-print" style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">
            <p><i class="fas fa-info-circle"></i> Keep this receipt for your records. Valid for tax purposes.</p>
        </div>
    </div>

    <script>
        // Add thermal print functionality
        function thermalPrint() {
            if (confirm('Print to thermal printer?')) {
                fetch("{{ route('receipt.thermal-print', $data['id']) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showNotification('Receipt sent to thermal printer!', 'success');
                        } else {
                            showNotification('Printer error. Please try downloading.', 'error');
                            if (data.download_url) {
                                setTimeout(() => {
                                    window.open(data.download_url, '_blank');
                                }, 1500);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Failed to connect to printer.', 'error');
                    });
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
                animation: slideIn 0.3s ease;
            `;

            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);

            // Add keyframe animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }

        // Add thermal print button to actions
        document.addEventListener('DOMContentLoaded', function () {
            const actions = document.querySelector('.actions');
            const thermalBtn = document.createElement('button');
            thermalBtn.className = 'btn btn-success';
            thermalBtn.innerHTML = '<i class="fas fa-receipt"></i> Thermal Print';
            thermalBtn.onclick = thermalPrint;
            actions.insertBefore(thermalBtn, actions.children[1]);
        });
    </script>
</body>

</html>