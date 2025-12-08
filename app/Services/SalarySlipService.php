<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\Payments;
use App\Models\ClassRoom;
use App\Models\TeacherPayment;
use Carbon\Carbon;

class SalarySlipService
{
    public function generate($teacherId, $yearMonth)
    {
        if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            return [
                "status" => "error",
                "message" => "Invalid year-month format. Use YYYY-MM."
            ];
        }

        // Month range
        $start = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();
        [$year, $month] = explode('-', $yearMonth);

        // Teacher model
        $teacher = Teacher::find($teacherId);

        if (!$teacher) {
            return [
                "status" => "error",
                "message" => "Teacher not found"
            ];
        }

        // Get classes
        $classes = ClassRoom::with(['subject', 'grade'])
            ->where('teacher_id', $teacherId)
            ->get();

        if ($classes->isEmpty()) {
            return [
                "status" => "success",
                "teacher_id" => $teacherId,
                "teacher_name" => $teacher->fname ?? 'N/A',
                "month_year" => "$month $year",
                "month_year_display" => date('F', mktime(0,0,0,$month,1)) . " $year",
                "date_generated" => now()->format('Y-m-d H:i:s'),
                "earnings" => [],
                "total_addition" => 0,
                "deductions" => [],
                "total_deductions" => 0,
                "net_salary" => 0,
                "payment_method" => "Cash / Bank Deposit"
            ];
        }

        $classIds = $classes->pluck('id');

        // Total student payments for month
        $totalPayments = Payments::whereHas('studentStudentClass', function ($q) use ($classIds) {
                $q->whereIn('student_classes_id', $classIds);
            })
            ->where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->sum('amount');

        // Percentages
        $teacherPercent = $teacher->precentage ?? 0;
        $institutionPercent = 100 - $teacherPercent;

        $teacherShare = round($totalPayments * ($teacherPercent / 100), 2);
        $institutionShare = round($totalPayments * ($institutionPercent / 100), 2);

        // Earnings (class-wise)
        $earnings = [];

        foreach ($classes as $class) {
            $classTotal = Payments::whereHas('studentStudentClass', function ($q) use ($class) {
                    $q->where('student_classes_id', $class->id);
                })
                ->where('status', 1)
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount');

            $classShare = round($classTotal * ($teacherPercent / 100), 2);

            if ($classShare > 0) {
                $earnings[] = [
                    "description" => $class->grade->grade_name . " - " . $class->subject->subject_name,
                    "amount" => $classShare
                ];
            }
        }

        // Deductions
        $deductions = [];
        $totalDeductions = 0;

        // Teacher advances
        $advanceTotal = TeacherPayment::where('teacher_id', $teacherId)
            ->where('status', 1)
            ->whereBetween('date', [$start, $end])
            ->sum('payment');

        if ($advanceTotal > 0) {
            $deductAdvance = min($advanceTotal, $teacherShare);
            $deductions[] = [
                "description" => "Teacher Advance Payment",
                "amount" => $deductAdvance
            ];
            $totalDeductions += $deductAdvance;
        }

        // Institution fee
        if ($institutionShare > 0) {
            $deductions[] = [
                "description" => "Corporated Fees",
                "amount" => $institutionShare
            ];
            $totalDeductions += $institutionShare;
        }

        $netSalary = max(0, $teacherShare - $totalDeductions);

        return [
            "status" => "success",
            "teacher_id" => $teacherId,
            "teacher_name" => $teacher->fname ?? 'N/A',
            "month_year" => "$month $year",
            "month_year_display" => date('F', mktime(0,0,0,$month,1)) . " $year",
            "date_generated" => now()->format('Y-m-d H:i:s'),
            "earnings" => $earnings,
            "total_addition" => $teacherShare,
            "deductions" => $deductions,
            "total_deductions" => $totalDeductions,
            "net_salary" => $netSalary,
            "payment_method" => "Cash / Bank Deposit"
        ];
    }
}
