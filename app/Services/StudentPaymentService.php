<?php

namespace App\Services;

use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPaymentService
{
    public function fetchStudentPayments($student_id, $student_class_id)
    {
        try {

            // Validate inputs
            if (empty($student_id) || empty($student_class_id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student ID and Class ID are required',
                    'data' => []
                ], 400);
            }

            // Fetch payments
            $payments = Payments::where('student_id', $student_id)
                ->where('student_student_student_classes_id', $student_class_id)
                ->orderBy('payment_date', 'desc')
                ->get();

            $formattedPayments = $this->formatPaymentsForMonthlyView($payments);

            return response()->json([
                'status' => 'success',
                'message' => 'Payments fetched successfully',
                'data' => [
                    'monthly_view' => $formattedPayments,
                    'summary' => $this->calculatePaymentSummary($payments)
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch payments data',
                'data' => [],
                // 'error' => $e->getMessage()  // (optional for debugging)
            ], 500);
        }
    }

    private function formatPaymentsForMonthlyView($payments)
    {
        $monthlyData = [];

        foreach ($payments as $payment) {
            $paymentDate = Carbon::parse($payment->payment_date);
            $yearMonth = $paymentDate->format('Y-m');
            $monthName = $paymentDate->format('F Y');

            if (!isset($monthlyData[$yearMonth])) {
                $monthlyData[$yearMonth] = [
                    'month' => $monthName,
                    'year_month' => $yearMonth,
                    'total_amount' => 0,
                    'payment_count' => 0,
                    'payments' => []
                ];
            }

            $monthlyData[$yearMonth]['total_amount'] += floatval($payment->amount);
            $monthlyData[$yearMonth]['payment_count']++;
            $monthlyData[$yearMonth]['payments'][] = [
                'id' => $payment->id,
                'payment_date' => $paymentDate->format('Y-m-d'),
                'display_date' => $paymentDate->format('M d, Y'),
                'amount' => floatval($payment->amount),
                'payment_for' => $payment->payment_for,
                'status' => $payment->status,
                'status_text' => $this->getPaymentStatusText($payment->status),
                'created_at' => $payment->created_at ? $payment->created_at->format('Y-m-d H:i:s') : $paymentDate->format('Y-m-d H:i:s'), // Include created_at
                'can_edit_delete' => $this->canEditDelete($payment) // Add edit/delete permission flag
            ];
        }

        krsort($monthlyData);

        return array_values($monthlyData);
    }

    private function calculatePaymentSummary($payments)
    {
        $totalPaid = 0;
        $activePayments = 0;

        foreach ($payments as $payment) {
            if ($payment->status == 1) {
                $totalPaid += floatval($payment->amount);
                $activePayments++;
            }
        }

        return [
            'total_paid' => $totalPaid,
            'total_payments' => count($payments),
            'active_payments' => $activePayments
        ];
    }

    private function getPaymentStatusText($status)
    {
        $statusMap = [
            0 => 'Deleted', // Changed from 'Pending' to 'Deleted'
            1 => 'Active',  // Changed from 'Completed' to 'Active'
            2 => 'Failed',
            3 => 'Cancelled'
        ];

        return $statusMap[$status] ?? 'Unknown';
    }

    /**
     * Check if payment can be edited or deleted (within 7 days)
     */
    private function canEditDelete($payment)
    {
        // If payment is not active, cannot edit/delete
        if ($payment->status != 1) {
            return false;
        }

        // Use created_at if available, otherwise use payment_date
        $paymentCreatedDate = $payment->created_at ? Carbon::parse($payment->created_at) : Carbon::parse($payment->payment_date);
        $currentDate = Carbon::now();

        // Check if payment is within 7 days
        return $paymentCreatedDate->diffInDays($currentDate) <= 7;
    }

    /**
     * Get days remaining for edit/delete
     */
    private function getDaysRemaining($payment)
    {
        if ($payment->status != 1) {
            return 0;
        }

        $paymentCreatedDate = $payment->created_at ? Carbon::parse($payment->created_at) : Carbon::parse($payment->payment_date);
        $currentDate = Carbon::now();
        $daysPassed = $paymentCreatedDate->diffInDays($currentDate);
        $daysRemaining = 7 - $daysPassed;

        return max(0, $daysRemaining);
    }


    public function storePayment(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'status' => 'required|integer|in:0,1', // 0=deleted, 1=active
            'amount' => 'required|numeric',
            'student_id' => 'required|integer',
            'student_student_student_classes_id' => 'required|integer',
            'payment_for' => 'required|string|max:20', // e.g., "2025 Feb"
        ]);

        try {
            DB::beginTransaction();

            // For ACTIVE payments (status = 1), check for duplicates
            if ($validated['status'] == 1) {
                $existingPayment = Payments::where('payment_for', $validated['payment_for'])
                    ->where('student_id', $validated['student_id'])
                    ->where('student_student_student_classes_id', $validated['student_student_student_classes_id'])
                    ->where('status', 1) // Check only ACTIVE payments
                    ->first();

                if ($existingPayment) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Duplicate payment found',
                        'details' => 'An active payment already exists for this student and class for ' . $validated['payment_for']
                    ], 422);
                }
            }

            // For DELETED payments (status = 0), allow duplicates
            // Or if no duplicate found for active payment, create new record

            // Save record
            $payment = Payments::create($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment stored successfully',
                'data' => $payment
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Edit payment amount
    public function updatePayment(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $payment = Payments::findOrFail($id);
            $payment->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete payment (set status to 0)
    public function deletePayment($id)
    {
        try {
            $payment = Payments::findOrFail($id);
            $payment->update(['status' => 0]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment deleted successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPaymentsByDate($date)
    {
        try {
            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid date format. Please use YYYY-MM-DD.'
                ], 400);
            }

            if (!strtotime($date)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid date.'
                ], 400);
            }

            // Load payments with student and student class data
            $payments = Payments::with([
                'student',
                'studentStudentClass.studentClass',
                'studentStudentClass.studentClass.teacher'

            ])
                ->whereDate('payment_date', $date)
                ->where('status', 1)
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'payment_date' => $payment->payment_date,
                        'amount' => $payment->amount,
                        'payment_for' => $payment->payment_for,
                        'created_at' => $payment->created_at,
                        'student' =>  [
                            'id' => $payment->student->id,
                            'custom_id' => $payment->student->custom_id,
                            'first_name' => $payment->student->fname,
                            'last_name' => $payment->student->lname,
                        ],
                        'student_class' => [
                            'id' => $payment->studentStudentClass->studentClass->id,
                            'class_name' => $payment->studentStudentClass->studentClass->class_name,
                        ],
                        'teacher' => [
                            'id' => $payment->studentStudentClass->studentClass->teacher->id,
                            'first_name' => $payment->studentStudentClass->studentClass->teacher->fname,
                            'last_name' => $payment->studentStudentClass->studentClass->teacher->lname,
                        ],
                    ];
                });

            if ($payments->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No payments found for this date.',
                    'data' => []
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payments retrieved successfully.',
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve payments.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTeacherPayments(Request $request)
    {
        try {
            // Get all payments with student, class, and teacher relationships
            $payments = Payments::with([
                'student',
                'studentStudentClass.studentClass',
                'studentStudentClass.studentClass.teacher'
            ])
                ->where('status', 1)
                ->get();

            if ($payments->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No payments found.',
                    'data' => []
                ]);
            }

            // Group payments by teacher
            $groupedByTeacher = [];

            foreach ($payments as $payment) {
                // Check if all relationships exist
                if (
                    !$payment->student ||
                    !$payment->studentStudentClass ||
                    !$payment->studentStudentClass->studentClass ||
                    !$payment->studentStudentClass->studentClass->teacher
                ) {
                    continue; // Skip if any relationship is missing
                }

                $teacher = $payment->studentStudentClass->studentClass->teacher;
                $studentClass = $payment->studentStudentClass->studentClass;
                $student = $payment->student;

                $teacherId = $teacher->id;
                $classId = $studentClass->id;

                // Initialize teacher if not exists
                if (!isset($groupedByTeacher[$teacherId])) {
                    $groupedByTeacher[$teacherId] = [
                        'teacher_id' => $teacher->id,
                        'teacher_name' => $teacher->fname . ' ' . $teacher->lname,
                        'total_amount' => 0,
                        'total_payments' => 0,
                        'classes' => []
                    ];
                }

                // Initialize class if not exists
                if (!isset($groupedByTeacher[$teacherId]['classes'][$classId])) {
                    $groupedByTeacher[$teacherId]['classes'][$classId] = [
                        'class_id' => $studentClass->id,
                        'class_name' => $studentClass->class_name,
                        'total_amount' => 0,
                        'total_payments' => 0,
                        'students' => []
                    ];
                }

                $studentId = $student->id;

                // Initialize student if not exists
                if (!isset($groupedByTeacher[$teacherId]['classes'][$classId]['students'][$studentId])) {
                    $groupedByTeacher[$teacherId]['classes'][$classId]['students'][$studentId] = [
                        'student_id' => $student->id,
                        'student_custom_id' => $student->custom_id,
                        'student_name' => $student->fname . ' ' . $student->lname,
                        'total_amount' => 0,
                        'payments' => []
                    ];
                }

                // Add payment to student
                $paymentData = [
                    'payment_id' => $payment->id,
                    'amount' => (float) $payment->amount,
                    'payment_for' => $payment->payment_for,
                    'payment_date' => $payment->payment_date,
                    'created_at' => $payment->created_at
                ];

                $groupedByTeacher[$teacherId]['classes'][$classId]['students'][$studentId]['payments'][] = $paymentData;
                $groupedByTeacher[$teacherId]['classes'][$classId]['students'][$studentId]['total_amount'] += (float) $payment->amount;

                // Update class totals
                $groupedByTeacher[$teacherId]['classes'][$classId]['total_amount'] += (float) $payment->amount;
                $groupedByTeacher[$teacherId]['classes'][$classId]['total_payments']++;

                // Update teacher totals
                $groupedByTeacher[$teacherId]['total_amount'] += (float) $payment->amount;
                $groupedByTeacher[$teacherId]['total_payments']++;
            }

            // Convert associative arrays to indexed arrays for JSON
            $formattedData = [];
            foreach ($groupedByTeacher as $teacherId => $teacherData) {
                $teacherClasses = [];
                foreach ($teacherData['classes'] as $classId => $classData) {
                    $classStudents = [];
                    foreach ($classData['students'] as $studentId => $studentData) {
                        $classStudents[] = $studentData;
                    }

                    $classData['students'] = $classStudents;
                    $teacherClasses[] = $classData;
                }

                $teacherData['classes'] = $teacherClasses;
                $formattedData[] = $teacherData;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payments retrieved successfully.',
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve payments.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
