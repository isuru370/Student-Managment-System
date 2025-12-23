<?php

namespace App\Services;

use App\Models\ClassRoom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassRoomService
{
    public function fetchAllClassRoom()
    {
        try {
            $classRooms = ClassRoom::with(['teacher', 'subject', 'grade'])->get();

            return response()->json([
                'status' => 'success',
                'data' => $classRooms
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ========================
    // Store new Class Room
    // ========================
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'class_name' => 'required|string|max:255',
                'is_active' => 'required|boolean',
                'is_ongoing' => 'required|boolean',
                'teacher_id' => 'required|exists:teachers,id',
                'subject_id' => 'required|exists:subjects,id',
                'grade_id' => 'required|exists:grades,id',
            ]);

            // Create the grade/class
            $grade = ClassRoom::create($validated);

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

    // ========================
    // Update existing Class Room
    // ========================
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'class_name' => 'required|string|max:255',
                'is_active' => 'nullable|boolean',
                'is_ongoing' => 'nullable|boolean',
                'teacher_id' => 'required|exists:teachers,id',
                'subject_id' => 'required|exists:subjects,id',
                'grade_id' => 'required|exists:grades,id',
            ]);

            $class_room = ClassRoom::findOrFail($id);
            $class_room->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Class room updated successfully',
                'data' => $class_room
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================
    // Optional: Fetch Class Rooms with relationships (API)
    // ========================
    public function fetchClasses()
    {
        $class_rooms = ClassRoom::with([
            'teacher' => function ($query) {
                $query->select('id', 'custom_id', 'fname', 'lname', 'email');
            },
            'subject' => function ($query) {
                $query->select('id', 'subject_name');
            },
            'grade' => function ($query) {
                $query->select('id', 'grade_name');
            },
        ])->get();

        return response()->json([
            'status' => 'success',
            'data' => $class_rooms
        ]);
    }

    public function fetchActiveClasses()
    {
        $class_rooms = ClassRoom::with([
            'teacher' => function ($query) {
                $query->select('id', 'custom_id', 'fname', 'lname', 'email');
            },
            'subject' => function ($query) {
                $query->select('id', 'subject_name');
            },
            'grade' => function ($query) {
                $query->select('id', 'grade_name');
            },
        ])->where('is_active', 1)->get();

        return response()->json([
            'status' => 'success',
            'data' => $class_rooms
        ]);
    }

    public function fetchSingleClasse($id)
    {
        try {
            // Fetch the class with subject and grade relationships
            $class_room = ClassRoom::with([
                'subject:id,subject_name',
                'grade:id,grade_name',
                'teacher:id,custom_id,fname,lname'
            ])->find($id);

            if (!$class_room) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $class_room
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchTeacherClasse($teacher_id)
    {
        try {
            // Fetch classes related to the teacher with subject and grade relationships
            $classes = ClassRoom::with([
                'subject:id,subject_name',
                'grade:id,grade_name',
                'teacher:id,custom_id,fname,lname'
            ])->where('teacher_id', $teacher_id)->get(); // get() instead of find()

            if ($classes->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No classes found for this teacher'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $classes
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch classes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deactivateClassActive($id)
    {
        DB::beginTransaction();
        try {
            $class = ClassRoom::find($id);

            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            $class->update(['is_active' => 0]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Class marked as inactive successfully',
                'data' => [
                    'id' => $class->id,
                    'is_active' => $class->is_active
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deactivateClassOngoing($id)
    {
        DB::beginTransaction();
        try {
            $class = ClassRoom::find($id);

            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            $class->update(['is_ongoing' => 0]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Class marked as not ongoing successfully',
                'data' => [
                    'id' => $class->id,
                    'is_ongoing' => $class->is_ongoing
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivateClassActive($id)
    {
        DB::beginTransaction();
        try {
            $class = ClassRoom::find($id);

            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            $class->update(['is_active' => 1]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Class reactivated successfully',
                'data' => [
                    'id' => $class->id,
                    'is_active' => $class->is_active
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reactivate class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivateClassOngoing($id)
    {
        DB::beginTransaction();
        try {
            $class = ClassRoom::find($id);

            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            $class->update(['is_ongoing' => 1]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Class marked as ongoing successfully',
                'data' => [
                    'id' => $class->id,
                    'is_ongoing' => $class->is_ongoing
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark class as ongoing',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
