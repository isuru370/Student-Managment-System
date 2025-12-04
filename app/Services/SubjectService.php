<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Http\Request;
use Exception;


class SubjectService
{
    // Get all subjects
    public function fetchAllSubject()
    {
        return response()->json(Subject::all());
    }

    // STORE: Create new subject
    public function store(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'subject_name' => 'required|string|max:255|unique:subjects,subject_name'
            ]);

            // Create subject
            $subject = Subject::create([
                'subject_name' => $request->subject_name
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subject created successfully',
                'data' => $subject
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // UPDATE: Update subject
    public function update(Request $request, $id)
    {
        try {
            $subject = Subject::find($id);

            if (!$subject) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Subject not found'
                ], 404);
            }

            // Validate
            $request->validate([
                'subject_name' => 'required|string|max:255|unique:subjects,subject_name,' . $id
            ]);

            // Update
            $subject->update([
                'subject_name' => $request->subject_name
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subject updated successfully',
                'data' => $subject
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update subject',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get subjects for dropdown
    public function getDropdownSubject()
    {
        try {
            $subjects = Subject::select('id', 'subject_name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $subjects
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch subjects for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}