<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Payments;
use App\Models\StudentStudentStudentClass;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class TeacherPaymentsService
{
    public function fetchTeacherPaymentsByMonth($yearMonth)
    {
        try {

            // Convert year-month format (YYYY-MM) into start & end of month
            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            // Load active teachers
            $teachers = Teacher::select('id', 'fname', 'lname', 'precentage')
                ->where('is_active', 1)
                ->get();

            $result = [];

            foreach ($teachers as $teacher) {

                // Payments for this teacher within selected month
                $payments = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->with('studentStudentClass.studentClass')
                    ->get();

                $totalForMonth = $payments->sum('amount');
                $teacherEarning = ($totalForMonth * $teacher->precentage) / 100;
                $institutionIncome = $totalForMonth - $teacherEarning;

                // CLASS-WISE TOTALS (Not empty)
                $validPayments = $payments->filter(function ($payment) {
                    return
                        $payment->studentStudentClass &&
                        $payment->studentStudentClass->studentClass;
                });

                if ($validPayments->isEmpty()) {
                    $classWiseTotals = [[
                        'class_id' => null,
                        'class_name' => null,
                        'total_amount' => 0,
                    ]];
                } else {
                    $classWiseTotals = $validPayments
                        ->groupBy(function ($payment) {
                            return $payment->studentStudentClass->studentClass->id;
                        })
                        ->map(function ($group) {
                            $class = $group->first()->studentStudentClass->studentClass;

                            return [
                                'class_id' => $class->id,
                                'class_name' => $class->class_name,
                                'total_amount' => $group->sum('amount'),
                            ];
                        })
                        ->values();
                }

                // Already paid for that month
                $teacherPaidList = TeacherPayment::with('reasonDetail')
                    ->where('teacher_id', $teacher->id)
                    ->where('status', 1)
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->get();

                $alreadyPaid = $teacherPaidList->sum('payment');

                // Payment details with reason + reason code
                $paidDetails = $teacherPaidList->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'payment' => $item->payment,
                        'reason_detail' => [
                            'id' => $item->reasonDetail->id ?? null,
                            'reason_code' => $item->reasonDetail->reason_code ?? null,
                            'reason' => $item->reasonDetail->reason ?? null,
                        ]
                    ];
                });

                $finalPayable = max($teacherEarning - $alreadyPaid, 0);

                // Final response object
                $result[] = [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->fname . " " . $teacher->lname,
                    'percentage' => $teacher->precentage,
                    'total_payments_this_month' => $totalForMonth,
                    'teacher_earning' => $teacherEarning,
                    'institution_income' => $institutionIncome,
                    'already_paid' => $alreadyPaid,
                    'final_payable' => $finalPayable,
                    'class_wise_totals' => $classWiseTotals,
                    'teacher_paid_details' => $paidDetails,
                ];
            }

            return response()->json([
                'status' => 'success',
                'year_month' => $yearMonth,
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchTeacherPaymentsCurrentMonth()
    {
        try {
            $currentYearMonth = Carbon::now()->format('Y-m');
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // Load all active teachers at once
            $teachers = Teacher::select('id', 'fname', 'lname', 'precentage')
                ->where('is_active', 1)
                ->get();

            $teacherIds = $teachers->pluck('id');

            // 1️⃣ Load ALL student payment totals grouped by teacher
            $monthlyPayments = Payments::selectRaw('
                student_classes.teacher_id,
                SUM(payments.amount) AS total_payment
            ')
                ->join('student_student_student_classes', 'payments.student_student_student_classes_id', '=', 'student_student_student_classes.id')
                ->join('student_classes', 'student_student_student_classes.student_classes_id', '=', 'student_classes.id')
                ->whereIn('student_classes.teacher_id', $teacherIds)
                ->where('payments.status', 1)
                ->whereBetween('payments.payment_date', [$startOfMonth, $endOfMonth])
                ->groupBy('student_classes.teacher_id')
                ->get()
                ->keyBy('teacher_id');

            // 2️⃣ Load all advance payments grouped by teacher
            $advancePayments = TeacherPayment::selectRaw('
                teacher_id,
                SUM(payment) AS advance_total
            ')
                ->whereIn('teacher_id', $teacherIds)
                ->where('status', 1)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->groupBy('teacher_id')
                ->get()
                ->keyBy('teacher_id');

            $result = [];

            foreach ($teachers as $teacher) {

                $totalForMonth = $monthlyPayments[$teacher->id]->total_payment ?? 0;
                $grossTeacherEarning = ($totalForMonth * $teacher->precentage) / 100;

                $advanceDeducted = $advancePayments[$teacher->id]->advance_total ?? 0;

                $netPayable = max($grossTeacherEarning - $advanceDeducted, 0);

                $result[] = [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->fname . " " . $teacher->lname,
                    'percentage' => $teacher->precentage,

                    'total_payments_this_month' => number_format($totalForMonth, 2, '.', ''),
                    'gross_earning' => number_format($grossTeacherEarning, 2, '.', ''),
                    'advance_deducted_this_month' => number_format($advanceDeducted, 2, '.', ''),
                    'teacher_earning' => number_format($netPayable, 2, '.', ''),
                ];
            }

            return response()->json([
                'status' => 'success',
                'year_month' => $currentYearMonth,
                'data' => $result
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchTeacherClassPaymentsByMonth($teacherId, $yearMonth)
    {
        try {
            if (!$teacherId) {
                return response()->json([
                    "status" => "error",
                    "message" => "Teacher ID is required"
                ], 400);
            }

            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Year-Month format must be YYYY-MM"
                ], 400);
            }

            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            // Load teacher classes
            $classes = ClassRoom::with('subject', 'teacher', 'grade')
                ->where('teacher_id', $teacherId)
                ->where('is_active',1)
                ->select('id', 'class_name', 'subject_id', 'teacher_id', 'grade_id')
                ->get();

            if ($classes->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'teacher_id' => $teacherId,
                    'percentage' => null,
                    'teacher_name' => null,
                    'grade_name' => null,
                    'subject_name' => null,
                    'is_salary_paid' => false,
                    'salary_payments' => [],
                    'total_payments_this_month' => 0,
                    'advance_payment_this_month' => 0,
                    'net_payable' => 0,
                    'classes' => []
                ]);
            }

            $classIds = $classes->pluck('id');
            $subjectName = $classes->first()->subject->subject_name ?? null;
            $teacherName = $classes->first()->teacher->fname ?? null;

            // Teacher percentage
            $teacherPercentage = $classes->first()->teacher->precentage ?? 0;
            $institutionPercentage = 100 - $teacherPercentage;

            // Get grouped payments
            $payments = Payments::selectRaw("
            student_student_student_classes.student_classes_id AS class_id,
            DATE(payments.payment_date) AS pay_date,
            SUM(payments.amount) AS total_amount
        ")
                ->join('student_student_student_classes', 'payments.student_student_student_classes_id', '=', 'student_student_student_classes.id')
                ->whereIn('student_student_student_classes.student_classes_id', $classIds)
                ->where('payments.status', 1)
                ->whereBetween('payments.payment_date', [$startOfMonth, $endOfMonth])
                ->groupBy('student_student_student_classes.student_classes_id', 'pay_date')
                ->get();

            $result = [];

            foreach ($classes as $cls) {
                $classPayments = $payments->where('class_id', $cls->id);

                $dailyPayments = [];
                foreach ($classPayments->sortBy('pay_date') as $p) {
                    $dailyPayments[$p->pay_date] = $p->total_amount;
                }

                $studentCount = StudentStudentStudentClass::where('student_classes_id', $cls->id)->count();

                $paidStudentCount = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->whereHas('studentStudentClass', function ($q) use ($cls) {
                        $q->where('student_classes_id', $cls->id);
                    })
                    ->distinct('student_student_student_classes_id')
                    ->count('student_student_student_classes_id');

                $result[] = [
                    'class_id' => $cls->id,
                    'class_name' => $cls->class_name,
                    'grade_name' => $cls->grade->grade_name ?? null,
                    'payments' => $dailyPayments,
                    'total_students' => $studentCount,
                    'students_paid' => $paidStudentCount
                ];
            }

            $totalPayments = Payments::whereHas('studentStudentClass', function ($q) use ($classIds) {
                $q->whereIn('student_classes_id', $classIds);
            })
                ->where('status', 1)
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            // Teacher salary for the month
            $salaryPayment = TeacherPayment::where('teacher_id', $teacherId)
                ->where('reason_code', 'salary')
                ->where('status', 1)
                ->whereBetween('payment_for', [
                    $startOfMonth->format('m Y'),
                    $endOfMonth->format('m Y')
                ])
                ->first();

            $salaryAmount = $salaryPayment ? $salaryPayment->payment : 0;



            // All teacher payments this month
            $salaryPayments = TeacherPayment::where('teacher_id', $teacherId)
                ->whereBetween('payment_for', [$startOfMonth->format('m Y'), $endOfMonth->format('m Y')])
                ->get();

            // Check if salary is paid for the month
            $isSalaryPaid = TeacherPayment::where('teacher_id', $teacherId)
                ->where('reason_code', 'salary')
                ->where('status', 1)
                ->whereBetween('payment_for', [$startOfMonth->format('m Y'), $endOfMonth->format('m Y')])
                ->exists();

            // Calculate advance including salary
            $advancePayment = TeacherPayment::where('teacher_id', $teacherId)
                ->where('status', 1)
                ->where('reason_code', '!=', 'salary') // skip salary payments
                ->whereBetween('payment_for', [$startOfMonth->format('m Y'), $endOfMonth->format('m Y')])
                ->sum('payment');


            // Percentage-based shares (CORRECT)
            $teacherShare = round($totalPayments * ($teacherPercentage / 100), 2);
            $institutionShare = round($totalPayments * ($institutionPercentage / 100), 2);

            // Correct Net Payable
            $salaryAdvance = $advancePayment +  $salaryAmount;
            $netPayable = $teacherShare - $salaryAdvance;

            return response()->json([
                'status' => 'success',
                'teacher_id' => $teacherId,
                'teacher_name' => $teacherName,
                'subject_name' => $subjectName,
                'is_salary_paid' => $isSalaryPaid,
                'salary_payments' => $salaryPayments,
                'total_payments_this_month' => $totalPayments,
                'advance_payment_this_month' => $advancePayment,
                'net_payable' => $netPayable,
                'teacher_percentage' => $teacherPercentage,
                'institution_percentage' => $institutionPercentage,
                'teacher_share' => $teacherShare,
                'institution_share' => $institutionShare,
                'classes' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getTeacherClassWiseStudentPaymentStatus($teacherId, $yearMonth)
    {
        try {
            if (!$teacherId) {
                return response()->json([
                    "status" => "error",
                    "message" => "Teacher ID is required"
                ], 400);
            }

            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Year-Month format must be YYYY-MM"
                ], 400);
            }

            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            // Load teacher classes with relations
            $classes = ClassRoom::with(['subject', 'teacher', 'grade'])
                ->where('teacher_id', $teacherId)
                ->select('id', 'class_name', 'subject_id', 'teacher_id', 'grade_id')
                ->get();

            if ($classes->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'teacher_id' => $teacherId,
                    'year_month' => $yearMonth,
                    'total_classes' => 0,
                    'classes' => []
                ]);
            }

            $teacherName = $classes->first()->teacher->fname ?? 'Unknown Teacher';
            $subjectName = $classes->first()->subject->subject_name ?? 'Unknown Subject';

            $classIds = $classes->pluck('id');
            $result = [];

            foreach ($classes as $cls) {
                // Get all students in this class
                $classStudents = StudentStudentStudentClass::with(['student' => function ($q) {
                    $q->select('id', 'custom_id', 'fname', 'lname', 'img_url', 'whatsapp_mobile', 'guardian_mobile');
                }])
                    ->where('student_classes_id', $cls->id)
                    ->get();

                $paidStudents = [];
                $unpaidStudents = [];

                foreach ($classStudents as $studentClass) {
                    $student = $studentClass->student;
                    $studentId = $student->id ?? null;
                    $studentName = ($student->fname ?? '') . ' ' . ($student->lname ?? '');
                    $customId = $student->custom_id;
                    $imgUrl = $student->img_url;
                    $whatsappMobile = $student->whatsapp_mobile;
                    $guardianMobile = $student->guardian_mobile;

                    // Check if student has paid for this month
                    $payment = Payments::where('student_student_student_classes_id', $studentClass->id)
                        ->where('status', 1)
                        ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                        ->select('amount', 'payment_date')
                        ->first();

                    $studentData = [
                        'id' => $studentId,
                        'custom_id' => $customId,
                        'name' => $studentName,
                        'img_url' => $imgUrl,
                        'whatsapp_mobile' => $whatsappMobile,
                        'guardian_mobile' => $guardianMobile,
                    ];

                    if ($payment && $payment->amount > 0) {
                        // Student has paid (amount > 0)
                        $studentData['amount_paid'] = $payment->amount;
                        $studentData['payment_date'] = $payment->payment_date;
                        $studentData['paid_status'] = 'Paid';
                        $paidStudents[] = $studentData;
                    } else {
                        // Student has not paid or amount is 0 or negative
                        $studentData['amount_paid'] = 0;
                        $studentData['payment_date'] = null;
                        $studentData['paid_status'] = 'Un Paid';
                        $unpaidStudents[] = $studentData;
                    }
                }

                $totalClassStudents = count($classStudents);
                $paidCount = count($paidStudents);
                $unpaidCount = count($unpaidStudents);

                // Get total collection for this class (only amount > 0)
                $totalCollection = Payments::whereHas('studentStudentClass', function ($q) use ($cls) {
                    $q->where('student_classes_id', $cls->id);
                })
                    ->where('status', 1)
                    ->where('amount', '>', 0) // Only positive amounts
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                // Get payment summary by date (only amount > 0)
                $paymentsSummary = Payments::whereHas('studentStudentClass', function ($q) use ($cls) {
                    $q->where('student_classes_id', $cls->id);
                })
                    ->where('status', 1)
                    ->where('amount', '>', 0) // Only positive amounts
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->selectRaw("DATE(payment_date) as pay_date, SUM(amount) as total_amount")
                    ->groupBy('pay_date')
                    ->get()
                    ->pluck('total_amount', 'pay_date');

                $result[] = [
                    'class_id' => $cls->id,
                    'class_name' => $cls->class_name,
                    'grade_name' => $cls->grade->grade_name ?? 'N/A',
                    'total_students' => $totalClassStudents,
                    'paid_students_count' => $paidCount,
                    'unpaid_students_count' => $unpaidCount,
                    'total_collection' => $totalCollection,
                    'paid_students' => $paidStudents,
                    'unpaid_students' => $unpaidStudents,
                    'payments_summary' => $paymentsSummary
                ];
            }

            // Calculate totals
            $totalStudents = array_sum(array_column($result, 'total_students'));
            $totalPaidStudents = array_sum(array_column($result, 'paid_students_count'));
            $totalUnpaidStudents = array_sum(array_column($result, 'unpaid_students_count'));
            $totalCollection = array_sum(array_column($result, 'total_collection'));

            // Calculate payment rate
            $paymentRate = $totalStudents > 0 ? round(($totalPaidStudents / $totalStudents) * 100, 2) : 0;

            return response()->json([
                'status' => 'success',
                'teacher_id' => $teacherId,
                'teacher_name' => $teacherName,
                'subject_name' => $subjectName,
                'year_month' => $yearMonth,
                'total_classes' => $classes->count(),
                'total_students' => $totalStudents,
                'total_paid_students' => $totalPaidStudents,
                'total_unpaid_students' => $totalUnpaidStudents,
                'payment_rate' => $paymentRate,
                'total_collection' => $totalCollection,
                'classes' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchSalarySlipData($teacherId, $yearMonth)
    {
        try {
            // Input validation
            if (!$teacherId) {
                return [
                    "status" => "error",
                    "message" => "Teacher ID is required"
                ];
            }

            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return [
                    "status" => "error",
                    "message" => "Year-Month format must be YYYY-MM"
                ];
            }

            // Month start and end
            $start = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();
            [$year, $month] = explode('-', $yearMonth);

            $teacher = Teacher::find($teacherId);
            if (!$teacher) {
                return [
                    "status" => "error",
                    "message" => "Teacher not found"
                ];
            }

            // Teacher's classes
            $classes = ClassRoom::with(['subject', 'grade'])
                ->where('teacher_id', $teacherId)
                ->get();

            if ($classes->isEmpty()) {
                return [
                    'status' => 'success',
                    'teacher_id' => $teacherId,
                    'teacher_name' => $teacher->fname ?? 'N/A',
                    'month_year' => "$month $year",
                    'month_year_display' => date('F', mktime(0, 0, 0, $month, 1)) . " $year",
                    'date_generated' => now()->format('Y-m-d H:i:s'),
                    'earnings' => [],
                    'total_addition' => 0,
                    'deductions' => [],
                    'total_deductions' => 0,
                    'net_salary' => 0,
                    'payment_method' => 'Cash / Bank Deposit'
                ];
            }

            $classIds = $classes->pluck('id');

            // Total earnings for all classes
            $totalEarnings = Payments::whereHas('studentStudentClass', function ($q) use ($classIds) {
                $q->whereIn('student_classes_id', $classIds);
            })
                ->where('status', 1)
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount');

            // Class-wise earnings (for display)
            $earnings = [];
            foreach ($classes as $class) {
                $classTotal = Payments::whereHas('studentStudentClass', function ($q) use ($class) {
                    $q->where('student_classes_id', $class->id);
                })
                    ->where('status', 1)
                    ->whereBetween('payment_date', [$start, $end])
                    ->sum('amount');

                if ($classTotal > 0) {
                    $earnings[] = [
                        "description" => $class->grade->grade_name . " - " . ($class->subject->subject_name ?? ''),
                        "amount" => $classTotal
                    ];
                }
            }

            // Total addition = total earnings
            $total_addition = $totalEarnings;

            // Deductions
            $deductions = [];
            $totalDeductions = 0;

            // Advance payments
            $advanceTotal = TeacherPayment::where('teacher_id', $teacherId)
                ->where('status', 1)
                ->where(function ($query) use ($start, $end) {
                    $query->where('reason_code', '!=', 'salary')
                        ->orWhere(function ($q) use ($start, $end) {
                            $q->where('reason_code', 'salary')
                                ->whereNotBetween('date', [$start, $end]);
                        });
                })
                ->whereBetween('date', [$start, $end])
                ->sum('payment');

            if ($advanceTotal > 0) {
                $deductions[] = [
                    "description" => "Teacher Advance Payment",
                    "amount" => $advanceTotal
                ];
                $totalDeductions += $advanceTotal;
            }

            // Institution fees
            $institutionShare = round($totalEarnings * ((100 - ($teacher->precentage ?? 0)) / 100), 2);
            if ($institutionShare > 0) {
                $deductions[] = [
                    "description" => "Institution Fees",
                    "amount" => $institutionShare
                ];
                $totalDeductions += $institutionShare;
            }

            // Net salary
            $netSalary = max(0, $total_addition - $totalDeductions);

            return [
                "status" => "success",
                "teacher_id" => $teacherId,
                "teacher_name" => $teacher->fname ?? 'N/A',
                "month_year" => "$month $year",
                "month_year_display" => date('F', mktime(0, 0, 0, $month, 1)) . " $year",
                "date_generated" => now()->format('Y-m-d H:i:s'),
                "earnings" => $earnings,
                "total_addition" => $total_addition,
                "deductions" => $deductions,
                "total_deductions" => $totalDeductions,
                "net_salary" => $netSalary,
                "payment_method" => "Cash / Bank Deposit"
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }


    public function fetchSalarySlipDataTest($teacherId, $yearMonth)
    {
        try {
            // Input validation
            if (!$teacherId) {
                return response()->json([
                    "status" => "error",
                    "message" => "Teacher ID is required"
                ], 400);
            }

            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Year-Month format must be YYYY-MM"
                ], 400);
            }

            $start = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $end   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();
            [$year, $month] = explode('-', $yearMonth);

            $teacher = Teacher::find($teacherId);
            if (!$teacher) {
                return response()->json([
                    "status" => "error",
                    "message" => "Teacher not found"
                ], 404);
            }

            // Teacher's classes
            $classes = ClassRoom::with(['subject', 'grade'])
                ->where('teacher_id', $teacherId)
                ->where('is_active',1)
                ->get();

            if ($classes->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'teacher_id' => $teacherId,
                    'teacher_name' => $teacher->fname ?? 'N/A',
                    'month_year' => "$month $year",
                    'month_year_display' => date('F', mktime(0, 0, 0, $month, 1)) . " $year",
                    'date_generated' => now()->format('Y-m-d H:i:s'),
                    'earnings' => [],
                    'total_addition' => 0,
                    'deductions' => [],
                    'total_deductions' => 0,
                    'net_salary' => 0,
                    'payment_method' => 'Cash / Bank Deposit'
                ], 200);
            }

            // Calculate total earnings for all classes
            $classIds = $classes->pluck('id');

            $totalEarnings = Payments::whereHas('studentStudentClass', function ($q) use ($classIds) {
                $q->whereIn('student_classes_id', $classIds);
            })
                ->where('status', 1)
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount');

            // Class-wise earnings for presentation
            $earnings = [];
            foreach ($classes as $class) {
                $classTotal = Payments::whereHas('studentStudentClass', function ($q) use ($class) {
                    $q->where('student_classes_id', $class->id);
                })
                    ->where('status', 1)
                    ->whereBetween('payment_date', [$start, $end])
                    ->sum('amount');

                if ($classTotal > 0) {
                    $earnings[] = [
                        "description" => $class->grade->grade_name . " - " . ($class->subject->subject_name ?? ''),
                        "amount" => $classTotal
                    ];
                }
            }

            // Total addition
            $total_addition = $totalEarnings;

            // Deductions
            $deductions = [];
            $totalDeductions = 0;

            // Advance payments
            $advanceTotal = TeacherPayment::where('teacher_id', $teacherId)
                ->where('status', 1)
                ->where(function ($query) use ($start, $end) {
                    $query->where('reason_code', '!=', 'salary')
                        ->orWhere(function ($q) use ($start, $end) {
                            $q->where('reason_code', 'salary')
                                ->whereNotBetween('date', [$start, $end]);
                        });
                })
                ->whereBetween('date', [$start, $end])
                ->sum('payment');

            if ($advanceTotal > 0) {
                $deductions[] = [
                    "description" => "Teacher Advance Payment",
                    "amount" => $advanceTotal
                ];
                $totalDeductions += $advanceTotal;
            }

            // Institution fees (optional, based on your previous logic)
            $institutionShare = round($totalEarnings * ((100 - ($teacher->precentage ?? 0)) / 100), 2);
            if ($institutionShare > 0) {
                $deductions[] = [
                    "description" => "Institution Fees",
                    "amount" => $institutionShare
                ];
                $totalDeductions += $institutionShare;
            }

            // Net salary
            $netSalary = max(0, $total_addition - $totalDeductions);

            return response()->json([
                "status" => "success",
                "teacher_id" => $teacherId,
                "teacher_name" => $teacher->fname ?? 'N/A',
                "month_year" => "$month $year",
                "month_year_display" => date('F', mktime(0, 0, 0, $month, 1)) . " $year",
                "date_generated" => now()->format('Y-m-d H:i:s'),
                "earnings" => $earnings,
                "total_addition" => $total_addition,
                "deductions" => $deductions,
                "total_deductions" => $totalDeductions,
                "net_salary" => $netSalary,
                "payment_method" => "Cash / Bank Deposit"
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500);
        }
    }












    public function storeTeacherPayments(Request $request)
    {
        try {
            // Validate required fields
            $request->validate([
                'teacher_id' => 'required|exists:teachers,id',
                'payment' => 'required|numeric|min:0',
                'reason_code' => 'required|exists:payment_reason,reason_code',
            ]);

            $paymentDate = now(); // Current date/time

            // Get paymentFor from request or default to current month
            $paymentFor = $request->input('paymentFor', $paymentDate->format('M Y')); // e.g., "Dec 2025"

            // Convert month name to numeric month using Carbon
            if (preg_match('/^[A-Za-z]{3,9} \d{4}$/', $paymentFor)) {
                // Try parsing with short month first
                $carbonDate = Carbon::createFromFormat('M Y', $paymentFor);
                if (!$carbonDate) {
                    // Fallback for full month name, e.g., "June 2025"
                    $carbonDate = Carbon::createFromFormat('F Y', $paymentFor);
                }
                $paymentFor = $carbonDate->format('m Y'); // "06 2025"
            }

            $teacherPayment = TeacherPayment::create([
                'teacher_id' => $request->teacher_id,
                'payment' => $request->payment,
                'reason_code' => $request->reason_code,
                'reason' => '', // leave empty
                'payment_for' => $paymentFor,
                'date' => $paymentDate,
                'status' => 1, // active
                'user_id' => auth()->id() ?? null, // current logged in user
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $teacherPayment,
                'message' => 'Teacher payment stored successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
