<?php

namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class StudentAdmissionPaymentService
{
    public function storeBulkAdmissionPayment(Request $request)
    {
        try {

            // Force JSON response
            $request->headers->set('Accept', 'application/json');

            // Validate
            $validated = $request->validate([
                'payments' => 'required|array|min:1',
                'payments.*.student_id' => 'required|integer|exists:students,id',
                'payments.*.admission_id' => 'required|integer|exists:admissions,id',
                'payments.*.amount' => 'required|numeric|min:0',
            ]);

            // Process inside transaction
            DB::transaction(function () use ($validated) {

                $dataToInsert = [];
                $studentIdsToUpdate = [];

                foreach ($validated['payments'] as $payment) {

                    $dataToInsert[] = [
                        'student_id'   => $payment['student_id'],
                        'admission_id' => $payment['admission_id'],
                        'amount'       => $payment['amount'],
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];

                    $studentIdsToUpdate[] = $payment['student_id'];
                }

                // Bulk insert
                AdmissionPayments::insert($dataToInsert);

                // Update admission status
                Student::whereIn('id', $studentIdsToUpdate)
                    ->update(['admission' => 1]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Admission payments stored successfully',
                'inserted_count' => count($validated['payments']),
            ], 201);
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Bulk admission payment failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchPayAdmissions()
    {
        $result = AdmissionPayments::with(['student', 'admission'])
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'student_name' => $payment->student ? $payment->student->fname . ' ' . $payment->student->lname : 'N/A',
                    'admission_name' => $payment->admission ? $payment->admission->name : 'N/A',
                    'amount' => $payment->amount,
                    'created_at' => $payment->created_at->toDateTimeString(),
                ];
            });
        return response()->json([
            'status' => true,
            'data' => $result
        ], 200);
    }

    public function fetchStudentAdmissions(Request $request)
    {
        // Validate that student_id exists and is an integer
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $studentId = $request->student_id;

        try {
            $result = AdmissionPayments::with(['student', 'admission'])
                ->where('student_id', $studentId)
                ->latest()
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'student_id' => $payment->student_id,
                        'student_name' => $payment->student ? $payment->student->fname . ' ' . $payment->student->lname : 'N/A',
                        'admission_name' => $payment->admission ? $payment->admission->name : 'N/A',
                        'amount' => $payment->amount,
                        'created_at' => $payment->created_at->toDateTimeString(),
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'Student admissions fetched successfully',
                'data' => $result,
                'count' => $result->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
