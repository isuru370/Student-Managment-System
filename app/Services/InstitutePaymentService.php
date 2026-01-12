<?php

namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\ExtraIncomes;
use App\Models\HallFee;
use App\Models\InstitutePayment;
use App\Models\Payments;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use App\Models\WelfarePayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class InstitutePaymentService
{

    public function fetchInstitutePaymentByMonth($yearMonth)
    {
        try {
            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();
            $monthYear    = now()->format('m Y');

            // --- ආදායම් ---
            $admissionPayment = AdmissionPayments::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $teacherAdvance = TeacherPayment::where('status', 1)
                ->where('reason_code', '!=', 'salary')
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('payment');

            $teacherSalary = TeacherPayment::where('status', 1)
                ->where('reason_code', 'salary')
                ->where('payment_for', $monthYear)
                ->sum('payment');

            $extraIncome = ExtraIncomes::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $hallFees = HallFee::where('status', 1)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $totalExpenese = InstitutePayment::where('status', 1)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('payment');

            // --- ගුරුවරුන් ---
            $teachers = Teacher::select('id', 'fname', 'lname', 'precentage')
                ->where('is_active', 1)
                ->get();

            $result = [];
            $totalInstituteIncome = 0;
            $totalTeacherPayments = 0;
            $totalTeacherEarnings = 0;
            $totalTeacherNetEarnings = 0;
            $totalTeacherWelfare = 0;

            foreach ($teachers as $teacher) {

                $payments = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                        $q->where('is_active', 1)
                            ->where('teacher_id', $teacher->id);
                    })
                    ->with('studentStudentClass.studentClass')
                    ->get();

                $classWiseTotals = $payments
                    ->groupBy(fn($p) => optional($p->studentStudentClass->studentClass)->id)
                    ->map(function ($group) use ($teacher) {
                        $class = $group->first()->studentStudentClass->studentClass;
                        $classTotal = $group->sum('amount');

                        $teacherPremium = round(($classTotal * $teacher->precentage) / 100, 2);
                        $instituteIncome = round($classTotal - $teacherPremium, 2);

                        return [
                            'class_id' => $class->id,
                            'class_name' => $class->class_name,
                            'total_amount' => round($classTotal, 2),
                            'teacher_earning' => $teacherPremium,
                            'institute_income' => $instituteIncome
                        ];
                    })
                    ->values();

                if ($classWiseTotals->isEmpty()) {
                    $classWiseTotals = [[
                        'class_id' => null,
                        'class_name' => null,
                        'total_amount' => 0,
                        'teacher_earning' => 0,
                        'institute_income' => 0,
                    ]];
                }

                $totalForMonth = $payments->sum('amount');
                $teacherTotalEarning = round(($totalForMonth * $teacher->precentage) / 100, 2);
                $institutionTotalIncome = round($totalForMonth - $teacherTotalEarning, 2);

                $totalInstituteIncome += $institutionTotalIncome;
                $totalTeacherPayments += $totalForMonth;
                $totalTeacherEarnings += $teacherTotalEarning;

                $teacherMonthlyAdvance = TeacherPayment::where('teacher_id', $teacher->id)
                    ->where('reason_code', '!=', 'salary')
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->sum('payment');

                $teacherMonthlySalary = TeacherPayment::where('teacher_id', $teacher->id)
                    ->where('reason_code', 'salary')
                    ->where('payment_for', $monthYear)
                    ->sum('payment');

                $teacherWelfare = WelfarePayment::where('status', 1)
                    ->where('teacher_id', $teacher->id)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                // Add to total
                $totalTeacherWelfare += $teacherWelfare;

                // ✅ Correct net earning (advance + salary + welfare deducted)
                $teacherNetEarning = round(
                    $teacherTotalEarning
                        - ($teacherMonthlyAdvance + $teacherMonthlySalary + $teacherWelfare),
                    2
                );


                $totalTeacherNetEarnings += $teacherNetEarning;

                $result[] = [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->fname . ' ' . $teacher->lname,
                    'percentage' => $teacher->precentage,
                    'total_payments_this_month' => round($totalForMonth, 2),
                    'teacher_total_earning' => $teacherTotalEarning,
                    'teacher_advance' => round($teacherMonthlyAdvance, 2),
                    'teacher_welfare' => round($teacherWelfare, 2),
                    'teacher_salary' => round($teacherMonthlySalary, 2),
                    'teacher_net_earning' => $teacherNetEarning,
                    'institution_total_income' => $institutionTotalIncome,
                    'class_wise_totals' => $classWiseTotals
                ];
            }

            $calculatedInstituteIncome = round($totalTeacherPayments - $totalTeacherEarnings, 2);
            $instituteIncomeWithAdmission = round($totalInstituteIncome + $admissionPayment + $hallFees, 2);
            $totalWithExtraIncome = round($instituteIncomeWithAdmission + $extraIncome, 2);
            $netIncome = round($totalWithExtraIncome - $totalExpenese, 2);

            return response()->json([
                'status' => 'success',
                'year_month' => $yearMonth,
                'summary' => [
                    'total_teacher_payments' => round($totalTeacherPayments, 2),
                    'total_teacher_earnings' => round($totalTeacherEarnings, 2),
                    'total_teacher_advances' => round($teacherAdvance, 2),
                    'total_teacher_welfare' => round($totalTeacherWelfare, 2),
                    'total_teacher_salaries' => round($teacherSalary, 2),
                    'total_teacher_net_earnings' => round($totalTeacherNetEarnings, 2),
                    'total_institute_from_classes' => round($totalInstituteIncome, 2),
                    'admission_payments' => round($admissionPayment, 2),
                    'hall_fees_payments' => round($hallFees, 2),
                    'extra_income_for_month' => round($extraIncome, 2),
                    'total_institute_expenese' => round($totalExpenese, 2),
                    'institute_gross_income' => $totalWithExtraIncome,
                    'institute_net_income' => $netIncome,
                    'verification' => [
                        'calculated_institute_income' => $calculatedInstituteIncome,
                    ]
                ],
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function fetchExtraIncome($date)
    {
        try {
            // Parse the date to get year and month
            $yearMonth = Carbon::parse($date)->format('Y-m');

            // Get all extra incomes for the specific month
            $extraIncomes = ExtraIncomes::whereYear('created_at', Carbon::parse($date)->year)
                ->whereMonth('created_at', Carbon::parse($date)->month)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $extraIncomes,
                'total' => $extraIncomes->sum('amount')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchInstituteExpenses($yearMonth)
    {
        try {
            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

            $admissionPayment = AdmissionPayments::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');
            // Get total income from classes (without class details)
            $teachers = Teacher::select('id', 'fname', 'lname', 'precentage')
                ->where('is_active', 1)
                ->get();

            $totalIncomeFromClasses = 0;
            foreach ($teachers as $teacher) {
                $payments = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->sum('amount');

                $totalForMonth = $payments;
                $teacherTotalEarning = round(($totalForMonth * $teacher->precentage) / 100, 2);
                $institutionTotalIncome = round($totalForMonth - $teacherTotalEarning, 2);
                $totalIncomeFromClasses += $institutionTotalIncome;
            }

            // Get extra income
            $extraIncome = (float) ExtraIncomes::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            // Get expenses with details
            $expenses = InstitutePayment::where('status', 1)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->orderBy('date', 'desc')
                ->get();

            // Calculate total expenses
            $totalExpenses = $expenses->sum('payment');

            // Calculate net total
            $grossIncome = round($totalIncomeFromClasses + $extraIncome + $admissionPayment, 2);
            $netTotal = round($grossIncome - $totalExpenses, 2);

            // Format expense details - FIXED: Use Carbon::parse() to convert string to Carbon
            $expenseDetails = $expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'date' => $expense->date ? Carbon::parse($expense->date)->format('Y-m-d') : null,
                    'reason' => $expense->reason,
                    'reason_code' => $expense->reason_code,
                    'amount' => round($expense->payment, 2),
                    'status' => $expense->status,
                    'created_at' => $expense->created_at ? Carbon::parse($expense->created_at)->format('Y-m-d H:i:s') : null,
                    'updated_at' => $expense->updated_at ? Carbon::parse($expense->updated_at)->format('Y-m-d H:i:s') : null
                ];
            });

            return response()->json([
                'status' => 'success',
                'year_month' => $yearMonth,
                'summary' => [
                    'income_from_classes' => round($totalIncomeFromClasses, 2),
                    'extra_income' => round($extraIncome, 2),
                    'gross_income' => $grossIncome,
                    'total_expenses' => round($totalExpenses, 2),
                    'net_total' => $netTotal
                ],
                'expense_details' => $expenseDetails
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function fetchYearlyIncomeChart($year)
    {
        try {
            $monthlyData = [];
            $monthLabels = [];
            $monthlyGrossIncomes = [];

            $admission = AdmissionPayments::whereYear('created_at', $year)
                ->sum('amount');

            // Get all active teachers with their percentages
            $teachers = Teacher::where('is_active', 1)
                ->select('id', 'precentage')
                ->get();

            // Loop through each month of the year
            for ($month = 1; $month <= 12; $month++) {
                $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
                $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

                $monthLabel = Carbon::create($year, $month, 1)->format('M');
                $monthLabels[] = $monthLabel;

                // Initialize total income from classes for this month
                $totalIncomeFromClasses = 0;

                if ($teachers->isNotEmpty()) {
                    // Calculate income for each teacher individually
                    foreach ($teachers as $teacher) {
                        $payments = Payments::where('status', 1)
                            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                            ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                                $q->where('teacher_id', $teacher->id);
                            })
                            ->sum('amount');

                        if ($payments > 0) {
                            // Calculate teacher's earnings using their actual percentage
                            $teacherEarnings = round(($payments * $teacher->precentage) / 100, 2);
                            // Institute's income from this teacher's classes
                            $institutionIncome = round($payments - $teacherEarnings, 2);
                            $totalIncomeFromClasses += $institutionIncome;
                        }
                    }
                }

                // Get extra income for this month
                $extraIncome = (float) ExtraIncomes::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->sum('amount');

                // Calculate gross income for this month (income from classes + extra income)
                $grossIncome = round($totalIncomeFromClasses + $extraIncome, 2);

                $monthlyGrossIncomes[] = $grossIncome;

                // Store detailed data if needed
                $monthlyData[] = [
                    'month' => $monthLabel,
                    'month_number' => $month,
                    'income_from_classes' => round($totalIncomeFromClasses, 2),
                    'extra_income' => round($extraIncome, 2),
                    'gross_income' => $grossIncome
                ];
            }

            // Calculate yearly totals
            $yearlyIncomeFromClasses = array_sum(array_column($monthlyData, 'income_from_classes'));
            $yearlyExtraIncome = array_sum(array_column($monthlyData, 'extra_income'));
            $yearlyGrossIncome = array_sum($monthlyGrossIncomes);

            return response()->json([
                'status' => 'success',
                'year' => $year,
                'summary' => [
                    'yearly_income_from_classes' => round($yearlyIncomeFromClasses, 2),
                    'yearly_extra_income' => round($yearlyExtraIncome, 2),
                    'yearly_gross_income' => round($yearlyGrossIncome, 2)
                ],
                'chart_data' => [
                    'labels' => $monthLabels,
                    'gross_incomes' => $monthlyGrossIncomes,
                    'detailed_data' => $monthlyData
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function institutePaymentStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'reason_code' => 'required|string|max:50',
                'payment' => 'required|numeric|min:0',
                'reason' => 'nullable|string|max:255', // nullable කරන්න
            ]);

            $income = InstitutePayment::create([
                'payment' => $validated['payment'],
                'date' => now(),
                'reason' => $validated['reason'] ?? '', // empty string ලෙස හෝ
                'reason_code' => $validated['reason_code'],
                'status' => 1,
                'user_id' => auth()->id() ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Institute payment saved successfully',
                'data' => $income,
                'current_server_time' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'current_server_time' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }

    public function institutePaymentDestroy($id)
    {
        try {
            // Find the payment record
            $payment = InstitutePayment::find($id);

            // Check if payment exists
            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment record not found'
                ], 404);
            }

            // Update status to 0 (soft delete or deactivate)
            $payment->update([
                'status' => 0,
                'user_id' => auth()->id() ?? null,
                'updated_at' => now() // Optional: update timestamp
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'Payment record deactivated successfully',
                'data' => $payment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function extraIncomeStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
            ]);

            $income = ExtraIncomes::create([
                'reason' => $validated['reason'],
                'amount' => $validated['amount'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Extra income saved successfully',
                'data' => $income
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function extraIncomeDelete($id)
    {
        try {
            // Find the income record by ID
            $income = ExtraIncomes::find($id);

            if (!$income) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Income record not found'
                ], 404);
            }

            // Check if the record is from the current month
            $incomeMonth = Carbon::parse($income->created_at)->format('Y-m');
            $currentMonth = Carbon::now()->format('Y-m');

            if ($incomeMonth !== $currentMonth) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You can only delete extra incomes from the current month'
                ], 403);
            }

            // Now safe to delete
            $income->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Extra income deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
