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

        .class-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .class-title {
            background-color: #2c3e50;
            color: white;
            padding: 8px;
            font-weight: bold;
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

        .paid {
            color: green;
            font-weight: bold;
        }

        .unpaid {
            color: red;
            font-weight: bold;
        }

        .free {
            color: orange;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
        }

        .amount {
            text-align: right;
        }
        
        .page-break {
            page-break-before: always;
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
                <td style="border: none; padding: 3px;"><strong>Teacher:</strong> {{ $teacherName }}</td>
                <td style="border: none; padding: 3px;"><strong>Teacher ID:</strong> {{ $teacherId }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px;"><strong>Email:</strong> {{ $paymentData['teacher_email'] ?? 'N/A' }}</td>
                <td style="border: none; padding: 3px;"><strong>Percentage:</strong> {{ $teacherPercentage }}%</td>
            </tr>
        </table>
    </div>

    <!-- SUMMARY SECTION -->
    <div class="summary-box">
        <h4 style="margin: 0 0 10px 0;">FINANCIAL SUMMARY</h4>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 4px;"><strong>Total Classes:</strong> {{ count($paymentData['data'] ?? []) }}</td>
                <td style="border: none; padding: 4px;"><strong>Total Students:</strong> {{ $totalStudents ?? 0 }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 4px;"><strong>Total Collected:</strong></td>
                <td style="border: none; padding: 4px; text-align: right;"><strong>Rs. {{ number_format($totalClassAmount, 2) }}</strong></td>
            </tr>
            <tr>
                <td style="border: none; padding: 4px;"><strong>Teacher's Percentage:</strong> {{ $teacherPercentage }}%</td>
                <td style="border: none; padding: 4px; text-align: right;"><strong>Rs. {{ number_format($totalTeacherAmount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- CLASS-WISE DETAILS -->
    @if(isset($paymentData['data']) && count($paymentData['data']) > 0)
        @foreach($paymentData['data'] as $classIndex => $class)
            @if($classIndex > 0 && $classIndex % 2 == 0)
                <div class="page-break"></div>
            @endif
            
            <div class="class-section">
                <div class="class-title">
                    CLASS {{ $classIndex + 1 }}: {{ $class['class_name'] ?? 'Class ' . ($class['class_id'] ?? 'Unknown') }}
                </div>

                <!-- Class Summary -->
                <div style="margin: 5px 0; padding: 5px; background-color: #f8f9fa;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="border: none;"><strong>Subject:</strong> {{ $class['subject_name'] ?? 'N/A' }}</td>
                            <td style="border: none;"><strong>Grade:</strong> {{ $class['grade_name'] ?? 'N/A' }}</td>
                            <td style="border: none;"><strong>Total Students:</strong> {{ $class['total_students'] ?? 0 }}</td>
                            <td style="border: none; text-align: right;">
                                <strong>Class Total:</strong> Rs. {{ number_format($class['class_total_amount'] ?? 0, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>

                @if(isset($class['students']) && count($class['students']) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th class="amount">Amount (Rs.)</th>
                                <th>Free Card</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class['students'] as $studentIndex => $student)
                                <tr>
                                    <td>{{ $studentIndex + 1 }}</td>
                                    <td>{{ $student['student_custom_id'] ?? 'N/A' }}</td>
                                    <td>{{ $student['student_name'] ?? 'Unknown' }}</td>
                                    <td class="{{ $student['payment_status'] ?? 'unpaid' }}">
                                        @if($student['payment_status'] == 'free')
                                            Free Card
                                        @elseif($student['payment_status'] == 'paid')
                                            Paid
                                        @else
                                            Unpaid
                                        @endif
                                    </td>
                                    <td>
                                        @if($student['payment_date'])
                                            {{ date('Y-m-d', strtotime($student['payment_date'])) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="amount">{{ number_format($student['payment_amount'] ?? 0, 2) }}</td>
                                    <td style="text-align: center;">{{ $student['is_free_card'] ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endforeach

                            <!-- Class Total Row -->
                            <tr style="font-weight: bold; background-color: #f0f0f0;">
                                <td colspan="5" style="text-align: right;">Class {{ $classIndex + 1 }} Total:</td>
                                <td class="amount">{{ number_format($class['class_total_amount'] ?? 0, 2) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; padding: 10px; color: #666; font-style: italic;">
                        No students found in this class.
                    </p>
                @endif
            </div>
        @endforeach

        <!-- GRAND TOTAL -->
        <div style="margin-top: 20px; padding: 10px; background-color: #2c3e50; color: white; border-radius: 5px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; text-align: right; padding: 5px;"><strong>GRAND TOTAL COLLECTED:</strong></td>
                    <td style="border: none; width: 150px; text-align: right; padding: 5px;"><strong>Rs. {{ number_format($totalClassAmount, 2) }}</strong></td>
                </tr>
                <tr>
                    <td style="border: none; text-align: right; padding: 5px;"><strong>TEACHER'S AMOUNT ({{ $teacherPercentage }}%):</strong></td>
                    <td style="border: none; width: 150px; text-align: right; padding: 5px;"><strong>Rs. {{ number_format($totalTeacherAmount, 2) }}</strong></td>
                </tr>
            </table>
        </div>

    @else
        <p style="text-align: center; padding: 20px; color: #666;">
            No payment data available for this month.
        </p>
    @endif

    <div class="footer">
        <p><strong>Generated on:</strong> {{ date('Y-m-d H:i:s') }}</p>
        <p><strong>Report ID:</strong> PAY-{{ date('Ymd') }}-{{ $teacherId }}</p>
        <p><strong>Report Period:</strong> {{ $month }}</p>
    </div>
</body>
</html>