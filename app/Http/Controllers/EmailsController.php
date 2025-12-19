<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReportMail;
use App\Services\TeacherPaymentsService;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class EmailsController extends Controller
{
    protected $teacherPaymentsService;

    public function __construct(TeacherPaymentsService $teacherPaymentsService)
    {
        $this->teacherPaymentsService = $teacherPaymentsService;
    }

    public function sendPaymentReport($teacherId, $yearMonth = null)
    {
        try {
            // 1. Set default month
            $yearMonth = $yearMonth ?? date('Y M');

            // 2. Get payment data
            $paymentData = $this->teacherPaymentsService->studentPaymentMonth($teacherId, $yearMonth);

            if (!$paymentData['success']) {
                return response()->json([
                    'error' => 'Failed to fetch payment data',
                    'message' => $paymentData['message']
                ], 400);
            }

            // 3. Prepare data for PDF view - FIXED
            $pdfViewData = [
                'paymentData' => [
                    'data' => $paymentData['data'] ?? [], // Use the Blade-compatible 'data' array
                    'teacher_email' => $paymentData['teacher_email'] ?? 'N/A',
                    'teacher_name' => $paymentData['teacher_name'] ?? 'Unknown'
                ],
                'month' => $paymentData['payment_for_format'] ?? $yearMonth,
                'teacherName' => $paymentData['teacher_name'] ?? 'Unknown Teacher',
                'teacherId' => $teacherId,
                'teacherPercentage' => $paymentData['teacher_percentage'] ?? 0,
                'totalClassAmount' => $paymentData['total_class_amount'] ?? 0,
                'totalTeacherAmount' => $paymentData['total_teacher_amount'] ?? 0
            ];

            // 4. Generate PDF
            $pdf = Pdf::loadView('emails.payment_report', $pdfViewData);
            $pdf->setPaper('A4', 'portrait');

            // 5. Create file name
            $fileName = 'payment-report-' . $teacherId . '-' .
                str_replace(' ', '-', strtolower($yearMonth)) . '.pdf';

            // 6. Send email
            $teacherEmail = $paymentData['teacher_email'] ?? 'hitrasitha@gmail.com'; //

            // Create email with proper data
            Mail::to($teacherEmail)
                ->send(new PaymentReportMail(
                    $paymentData,          // Full payment data
                    $pdf->output(),        // PDF content
                    $fileName,             // File name
                    $teacherId,            // Teacher ID
                    $paymentData['payment_for_format'] ?? $yearMonth // Month for display
                ));

            return response()->json([
                'success' => true,
                'message' => 'Payment report PDF sent to teacher successfully',
                'teacher_email' => $teacherEmail,
                'file_name' => $fileName,
                'month' => $paymentData['payment_for_format'] ?? $yearMonth,
                'total_amount' => $paymentData['total_class_amount'],
                'teacher_amount' => $paymentData['total_teacher_amount'],
                'total_classes' => $paymentData['total_classes'] ?? 0,
                'total_students' => $paymentData['total_students'] ?? 0,
                'total_paid_students' => $paymentData['total_paid_students'] ?? 0,
                'total_unpaid_students' => $paymentData['total_unpaid_students'] ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'System error: ' . $e->getMessage()
            ], 500);
        }
    }
}
