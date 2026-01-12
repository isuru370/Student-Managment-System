<?php


namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\ExtraIncomes;
use App\Models\HallFee;
use App\Models\InstitutePayment;
use App\Models\Payments;
use App\Models\TeacherPayment;
use App\Models\WelfareExpense;
use App\Models\WelfarePayment;

class ReportService
{
    public function generateUsersReport()
    {
        // Logic to generate user report
        return "Users report generated.";
    }

    public function generateStudentsReport()
    {
        // Logic to generate payment report
        return "Students report generated.";
    }

    public function generateTeachersReport()
    {
        // Logic to generate attendance report
        return "Teachers report generated.";
    }

    public function generateClassesReport()
    {
        // Logic to generate exam report
        return "Classes report generated.";
    }

    /*
    // Add payment report generation methods as needed
    */

    public function generateYearlyPaymentsReport($year)
    {
        // Logic to generate exam report
        return "Exams report generated.";
    }

    public function generateMonthlyPaymentsReport($month)
    {
        // Logic to generate exam report
        return "Exams report generated.";
    }

    public function generateDayllyPaymentsReport($day)
    {
        // Logic to generate exam report
        return "Exams report generated.";
    }



    public function generateAllDailyPaymentsReport($day)
    {
        // ------------------ RECEIPTS (RECORDS) ------------------
        $studentPaymentRecords = Payments::where('status', 1)
            ->whereDate('created_at', $day)
            ->get();

        $admissionPaymentRecords = AdmissionPayments::whereDate('created_at', $day)
            ->get();

        $extraIncomeRecords = ExtraIncomes::whereDate('created_at', $day)
            ->get();

        $classHallFeesRecords = HallFee::where('status', 1)
            ->get(); // If hall fees are daily, you may want to filter by date

        $welfarePaymentRecords = WelfarePayment::where('status', 1)
            ->whereDate('created_at', $day)
            ->get();

        // ------------------ RECEIPTS (TOTALS) ------------------
        $studentPayments = $studentPaymentRecords->sum('amount');
        $admissionPayments = $admissionPaymentRecords->sum('amount');
        $extraIncomes = $extraIncomeRecords->sum('amount');
        $welfarePayments = $welfarePaymentRecords->sum('amount');
        $classHallFees = $classHallFeesRecords->sum('amount');

        $totalReceipts = $studentPayments
            + $admissionPayments
            + $extraIncomes
            + $welfarePayments
            + $classHallFees;

        // ------------------ PAYMENTS (RECORDS) ------------------
        $teacherPaymentRecords = TeacherPayment::where('status', 1)
            ->whereDate('created_at', $day)
            ->get();

        $institutePaymentRecords = InstitutePayment::where('status', 1)
            ->whereDate('created_at', $day)
            ->get();

        $welfareExpenseRecords = WelfareExpense::where('status', 1)
            ->whereDate('created_at', $day) // Use expense_date instead of created_at
            ->get();

        // ------------------ PAYMENTS (TOTALS) ------------------
        $teacherPayments = $teacherPaymentRecords->sum('payment');
        $institutePayments = $institutePaymentRecords->sum('payment');
        $welfareExpenses = $welfareExpenseRecords->sum('amount');

        $totalPayments = $teacherPayments
            + $institutePayments
            + $welfareExpenses;

        // ------------------ BALANCE ------------------
        $balance = $totalReceipts - $totalPayments;

        // ------------------ RETURN FULL REPORT ------------------
        return [
            'date' => $day,

            // Receipts records
            'student_payment_records' => $studentPaymentRecords,
            'admission_payment_records' => $admissionPaymentRecords,
            'extra_income_records' => $extraIncomeRecords,
            'welfare_payment_records' => $welfarePaymentRecords,
            'class_hall_fee_records' => $classHallFeesRecords, // fixed comma

            // Receipts totals
            'student_payments_total' => $studentPayments,
            'admission_payments_total' => $admissionPayments,
            'extra_incomes_total' => $extraIncomes,
            'welfare_payments_total' => $welfarePayments,
            'class_hall_fees_total' => $classHallFees,

            'total_receipts' => $totalReceipts,

            // Payments records
            'teacher_payment_records' => $teacherPaymentRecords,
            'institute_payment_records' => $institutePaymentRecords,
            'welfare_expense_records' => $welfareExpenseRecords,

            // Payments totals
            'teacher_payments_total' => $teacherPayments,
            'institute_payments_total' => $institutePayments,
            'welfare_expenses_total' => $welfareExpenses,

            'total_payments' => $totalPayments,

            // Balance
            'balance' => $balance,
        ];
    }
}
