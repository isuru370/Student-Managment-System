<?php

namespace App\Services;

use App\Models\Grade;
use Exception;
use Illuminate\Http\Request;

class GradeService
{
    public function fetchAllGrade()
    {
        return response()->json(Grade::all());
    }

    //  STORE: Create new grade
    public function store(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'grade_name' => 'required|string|max:255|unique:grades,grade_name'
            ]);

            // Create grade
            $grade = Grade::create([
                'grade_name' => $request->grade_name
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Grade created successfully',
                'data' => $grade
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create grade',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //  UPDATE: Update grade
    public function update(Request $request, $id)
    {
        try {
            $grade = Grade::find($id);

            if (!$grade) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Grade not found'
                ], 404);
            }

            // Validate
            $request->validate([
                'grade_name' => 'required|string|max:255|unique:grades,grade_name,' . $id
            ]);

            // Update
            $grade->update([
                'grade_name' => $request->grade_name
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Grade updated successfully',
                'data' => $grade
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update grade',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get grades for dropdown
    public function fetchDropdownGrade()
    {
        try {
            $banks = Grade::select('id', 'grade_name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $banks
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch grades for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchPublicDropdownGrade()
    {
        try {
            $grade = Grade::select('id', 'grade_name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $grade
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch grades for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}