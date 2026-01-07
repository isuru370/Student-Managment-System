<!DOCTYPE html>
<html>
<head>
    <title>Student Payment Report - {{ $month }}</title>
    <style>
        /* Keep your existing styles */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .teacher-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }

        .summary-box {
            margin: 15px 0;
            padding: 12px;
            background-color: #e8f4fd;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        .amount {
            text-align: right;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
        }
        
        .free-card {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">STUDENT PAYMENT REPORT</h2>
        <h3 style="margin: 5px 0;">Month: {{ $month }}</h3>
    </div>

    <div class="teacher-info">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 3px;">
                    <strong>Teacher:</strong> {{ $teacher['name'] ?? 'Unknown' }}
                </td>
                <td style="border: none; padding: 3px;">
                    <strong>Teacher ID:</strong> {{ $teacher['id'] ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px;">
                    <strong>Email:</strong> {{ $teacher['email'] ?? 'N/A' }}
                </td>
                <td style="border: none; padding: 3px;">
                    <strong>Percentage:</strong> {{ $teacher['percentage'] ?? $teacher['precentage'] ?? 0 }}%
                </td>
            </tr>
        </table>
    </div>

    <!-- SUMMARY SECTION -->
    <div class="summary-box">
        <h4 style="margin: 0 0 10px 0;">FINANCIAL SUMMARY</h4>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 4px;">
                    <strong>Total Students:</strong> {{ $totalStudents }}
                </td>
                <td style="border: none; padding: 4px;">
                    <strong>Total Payments:</strong> {{ count($students) }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 4px;">
                    <strong>Total Collected:</strong>
                </td>
                <td style="border: none; padding: 4px; text-align: right;">
                    <strong>Rs. {{ number_format($totalAmount, 2) }}</strong>
                </td>
            </tr>
            @php
                $teacherPercentage = $teacher['percentage'] ?? $teacher['precentage'] ?? 0;
                $teacherAmount = round($totalAmount * ($teacherPercentage / 100), 2);
            @endphp
            <tr>
                <td style="border: none; padding: 4px;">
                    <strong>Teacher's Percentage:</strong> {{ $teacherPercentage }}%
                </td>
                <td style="border: none; padding: 4px; text-align: right;">
                    <strong>Rs. {{ number_format($teacherAmount, 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- STUDENT PAYMENTS DETAILS -->
    @if(count($students) > 0)
        <h4 style="margin: 20px 0 10px 0;">STUDENT PAYMENT DETAILS</h4>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Payment Date</th>
                    <th>Payment For</th>
                    <th class="amount">Amount (Rs.)</th>
                    <th>Free Card</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $student['custom_id'] ?? 'N/A' }}</td>
                        <td>{{ $student['student_name'] ?? 'Unknown' }}</td>
                        <td>
                            @if(!empty($student['date']))
                                {{ date('Y-m-d', strtotime($student['date'])) }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $student['payment_for'] ?? 'N/A' }}</td>
                        <td class="amount">{{ number_format($student['amount'], 2) }}</td>
                        <td>
                            @if($student['is_free_card'] == 1)
                                <span class="free-card">Yes</span>
                            @else
                                No
                            @endif
                        </td>
                        <td>
                            @if($student['is_free_card'] == 1)
                                <span class="free-card">Free Card</span>
                            @elseif($student['amount'] > 0)
                                <span style="color: green; font-weight: bold;">Paid</span>
                            @else
                                <span style="color: red; font-weight: bold;">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                @endforeach

                <!-- TOTALS ROW -->
                <tr style="font-weight: bold; background-color: #f0f0f0;">
                    <td colspan="5" style="text-align: right;">Total Collected:</td>
                    <td class="amount">{{ number_format($totalAmount, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 20px; color: #666;">
            No payment data available for this month.
        </p>
    @endif

    <div class="footer">
        <p><strong>Generated on:</strong> {{ date('Y-m-d H:i:s') }}</p>
        <p><strong>Report ID:</strong> PAY-{{ date('Ymd') }}-{{ $teacher['id'] ?? 'N/A' }}</p>
        <p><strong>Report Period:</strong> {{ $month }}</p>
    </div>
</body>
</html>