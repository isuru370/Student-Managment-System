<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Http\Request;

class ReadQRCodeService
{
    public function readQRCode(Request $request)
    {
        // Validate request
        $request->validate([
            'custom_id' => 'required|string'
        ]);

        try {

            // Find student by custom_id
            $student = Student::where('custom_id', $request->custom_id)->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student not found'
                ], 404);
            }

            if ($student->is_active == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Student is inactive'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'QR Code scanned successfully',
                'student_id' => $student->id,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
