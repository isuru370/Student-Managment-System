<?php

namespace App\Services;

use App\Models\Exam;
use Exception;

class ExamService
{
    public function fetchExam($class_id)
    {
        try {
            if (!$class_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Exam not found'
                ], 404);
            }
            $result = Exam::with(['student_classes_id'])
                ->where('student_classes_id', $class_id)
                ->get();
            return response()->json([
                'status' => 'success',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch Exam',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
