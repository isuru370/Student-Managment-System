<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $teacherName ?? 'Teacher' }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 40px;
            font-size: 14px;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .logo-section {
            flex: 0 0 auto;
        }

        .logo {
            width: 90px;
            height: auto;
        }

        .header {
            flex: 1;
            text-align: center;
        }

        .header h2 {
            margin: 5px 0;
            font-weight: 700;
            font-size: 24px;
        }

        .header h3 {
            margin: 3px 0;
            font-weight: 600;
        }

        .date {
            flex: 0 0 auto;
            font-size: 13px;
            text-align: right;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 6px 8px;
            text-align: left;
        }

        th.right-align,
        td.right-align {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .signature-area {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .deduction-bold {
            font-weight: bold;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 100px;
            font-size: 18px;
        }

        .teacher-info-table {
            margin-top: 15px;
        }

        .teacher-info-table th {
            text-align: left;
            background-color: #f2f2f2;
            padding: 8px 10px;
        }

        .teacher-info-table td {
            padding: 8px 10px;
        }
    </style>

</head>

<body>

    @if(isset($error))
        <div class="error-message">
            <h3>Error Loading Salary Slip</h3>
            <p>{{ $error }}</p>
            <p>Teacher ID: {{ $teacherId }}, Month: {{ $yearMonth ?? 'N/A' }}</p>
        </div>
    @elseif(isset($success) && $success)

        <!-- HEADER - Logo on left, title center, date right -->
        <div class="top-section">
            <!-- Logo on left -->
            <div class="logo-section">
                <img src="{{ asset('uploads/logo/logo.png') }}" class="logo" alt="Logo">
            </div>

            <!-- Title in center -->
            <div class="header">
                <h2>Success Academy</h2>
                <h3>Padavi Parakramapura</h3>
                <h3>Salary Slip</h3>
            </div>

            <!-- Date on right -->
            <div class="date">
                <strong>Date:</strong> {{ $dateGenerated ?? now()->format('Y-m-d H:i:s') }}
            </div>
        </div>

        <!-- TEACHER INFO TABLE - LEFT ALIGNED -->
        <table class="teacher-info-table">
            <tr>
                <th>Teacher ID</th>
                <td>{{ $teacherId ?? 'N/A' }}</td>
                <th>Teacher Name</th>
                <td>{{ $teacherName ?? 'N/A' }}</td>
            </tr>

            <!-- Month/Year on right side -->
            <tr>
                <th>Month/Year</th>
                <td colspan="3">{{ $monthYear ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- EARNINGS & DEDUCTIONS TABLE -->
        <table>
            <tr class="bold">
                <th>Earnings</th>
                <th class="right-align">Amount (Rs.)</th>
                <th>Deductions</th>
                <th class="right-align">Amount (Rs.)</th>
            </tr>

            <!-- Earnings Rows -->
            @foreach($earnings as $index => $earning)
                <tr>
                    <!-- Earnings Column -->
                    <td>{{ $earning['description'] }}</td>
                    <td class="right-align">{{ number_format($earning['amount'], 2) }}</td>

                    <!-- Deductions Column -->
                    @if(isset($deductions[$index]))
                        @if($deductions[$index]['description'] == 'Teacher Advance Payment' || $deductions[$index]['description'] == 'Corporated Fees')
                            <td class="deduction-bold">{{ $deductions[$index]['description'] }}</td>
                        @else
                            <td>{{ $deductions[$index]['description'] }}</td>
                        @endif
                        <td class="right-align">{{ number_format($deductions[$index]['amount'], 2) }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach

            <!-- If there are more deductions than earnings -->
            @if(count($deductions) > count($earnings))
                @for($i = count($earnings); $i < count($deductions); $i++)
                    <tr>
                        <td></td>
                        <td></td>
                        @if($deductions[$i]['description'] == 'Teacher Advance Payment' || $deductions[$i]['description'] == 'Corporated Fees')
                            <td class="deduction-bold">{{ $deductions[$i]['description'] }}</td>
                        @else
                            <td>{{ $deductions[$i]['description'] }}</td>
                        @endif
                        <td class="right-align">{{ number_format($deductions[$i]['amount'], 2) }}</td>
                    </tr>
                @endfor
            @endif

            <!-- EMPTY ROW -->
            <tr>
                <td colspan="4" style="height: 20px;"></td>
            </tr>

            <!-- Total rows -->
            <tr class="bold">
                <td>Total Addition</td>
                <td class="right-align">{{ number_format($totalAddition ?? 0, 2) }}</td>
                <td>Total Deductions</td>
                <td class="right-align">{{ number_format($totalDeductions ?? 0, 2) }}</td>
            </tr>

            <!-- EMPTY ROW -->
            <tr>
                <td colspan="4" style="height: 20px;"></td>
            </tr>

            <!-- Net Salary -->
            <tr class="bold">
                <td colspan="3">Net Salary</td>
                <td class="right-align">{{ number_format($netSalary ?? 0, 2) }}</td>
            </tr>
        </table>


        <!-- PAYMENT METHOD -->
        <table class="teacher-info-table" style="margin-top: 20px;">
            <tr>
                <th>Salary Paid By</th>
                <td>{{ $paymentMethod ?? 'Cash / Bank Deposit' }}</td>
            </tr>
        </table>

        <!-- SIGNATURES -->
        <div class="signature-area">
            <div><strong>Teacher's Signature:</strong> _____________</div>
            <div><strong>SA Owner:</strong> _____________</div>
        </div>

    @else
        <div class="error-message">
            <h3>No Data Available</h3>
            <p>Unable to load salary slip data.</p>
        </div>
    @endif

    <script>
        // Auto-print functionality
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const autoPrint = urlParams.get('autoPrint');
            
            if (autoPrint === 'true') {
                setTimeout(function() {
                    window.print();
                }, 1000);
            }
        });
    </script>

</body>

</html>