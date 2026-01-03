<?php

namespace App\Http\Controllers;

use App\Services\TeacherPaymentsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeacherPaymentsController extends Controller
{
    protected $teacherPaymentsService;
    public function __construct(TeacherPaymentsService $teacherPaymentsService)
    {
        $this->teacherPaymentsService = $teacherPaymentsService;
    }

    public function getMonthlyPayments($yearMonth)
    {
        return $this->teacherPaymentsService->fetchTeacherPaymentsByMonth($yearMonth);
    }

    public function fetchTeacherPaymentsCurrentMonth()
    {
        return $this->teacherPaymentsService->fetchTeacherPaymentsCurrentMonth();
    }
    public function fetchTeacherClassPayments($teacherId, $yearMonth)
    {
        return $this->teacherPaymentsService->fetchTeacherClassPaymentsByMonth($teacherId, $yearMonth);
    }
    public function getTeacherClassWiseStudentPaymentStatus($teacherId, $yearMonth, Request $request)
    {
        return $this->teacherPaymentsService->getTeacherClassWiseStudentPaymentStatus($teacherId, $yearMonth, $request);
    }
    public function fetchSalarySlipDataTest($teacherId, $yearMonth)
    {
        return $this->teacherPaymentsService->fetchSalarySlipDataTest($teacherId, $yearMonth);
    }

    public function studentPaymentMonthCheck($teacherId, $yearMonth)
    {
        return $this->teacherPaymentsService->studentPaymentMonthCheck($teacherId, $yearMonth);
    }
    public function showSalarySlip($teacherId, $yearMonth)
    {
        try {
            // Get data FROM SERVICE (now returns ARRAY, not JSON)
            $data = $this->teacherPaymentsService->fetchSalarySlipData($teacherId, $yearMonth);

            // If service returned an error
            if ($data['status'] === 'error') {
                return view('teacher_payment.salary-slip-exact', [
                    'error' => $data['message'],
                    'teacherId' => $teacherId,
                    'yearMonth' => $yearMonth
                ]);
            }

            // Pass clean array data to view
            return view('teacher_payment.salary-slip-exact', [
                'teacherId' => $data['teacher_id'],
                'teacherName' => $data['teacher_name'],
                'monthYear' => $data['month_year'],
                'dateGenerated' => $data['date_generated'],
                'earnings' => $data['earnings'],
                'deductions' => $data['deductions'],
                'totalAddition' => $data['total_addition'],
                'totalDeductions' => $data['total_deductions'],
                'teacherWelfare' => $data['teacher_welfare'] ?? 0,
                'netSalary' => $data['net_salary'],
                'paymentMethod' => $data['payment_method'],
                'success' => true
            ]);
        } catch (\Exception $e) {

            return view('teacher_payment.salary-slip-exact', [
                'error' => "Unexpected error occurred.",
                'teacherId' => $teacherId,
                'yearMonth' => $yearMonth
            ]);
        }
    }



    public function teachersExpenses($yearMonth)
    {
        return $this->teacherPaymentsService->teachersExpenses($yearMonth);
    }

    public function togglePaymentStatus(Request $request, $id)
    {
        return $this->teacherPaymentsService->togglePaymentStatus($request, $id);
    }
    public function storeTeacherPayments(Request $request)
    {
        return $this->teacherPaymentsService->storeTeacherPayments($request);
    }



    // web page routes

    public function indexPage()
    {
        return view('teacher_payment.index');
    }
    public function paymentPage($teacherId)
    {
        return view('teacher_payment.salary', ['teacherId' => $teacherId]);
    }
    public function historyPage($teacherId)
    {
        return view('teacher_payment.history', ['teacherId' => $teacherId]);
    }
    public function viewPage($teacherId)
    {
        return view('teacher_payment.view', ['teacherId' => $teacherId]);
    }
    public function expensesPage()
    {
        return view('teacher_payment.expenses');
    }
}
