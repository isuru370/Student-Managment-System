<?php

namespace App\Services;

use App\Models\ClassCategoryHasStudentClass;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\Rule;

class ClassCategoryHasStudentService
{
    public function fetchByClassId($classId)
    {
        $records = ClassCategoryHasStudentClass::with([
            'studentClass.grade',
            'studentClass.subject',
            'studentClass.teacher',
            'classCategory'
        ])
            ->where('student_classes_id', $classId)
            ->get();

        if ($records->isEmpty()) {
            return response()->json([
                'status' => 'empty',
                'message' => 'No categories assigned to this class'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $records
        ]);
    }
    public function searchClasses(Request $request)
    {
        $searchTerm = $request->get('q');

        $classes = ClassCategoryHasStudentClass::with([
            'studentClass.grade',
            'studentClass.subject',
            'studentClass.teacher',
            'classCategory'
        ])
            // Search in studentClass relationships
            ->whereHas('studentClass', function ($query) use ($searchTerm) {
                $query->where('class_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('teacher', function ($q) use ($searchTerm) {
                        $q->where('fname', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('lname', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('custom_id', 'LIKE', "%{$searchTerm}%");
                    });
            })
            // Search in classCategory
            ->orWhereHas('classCategory', function ($query) use ($searchTerm) {
                $query->where('category_name', 'LIKE', "%{$searchTerm}%");
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $classes
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fees' => 'required|numeric',
            'student_classes_id' => 'required|integer|exists:student_classes,id',
            'class_category_id' => [
                'required',
                'integer',
                'exists:class_categories,id',
                Rule::unique('class_category_has_student_class')
                    ->where('student_classes_id', $request->student_classes_id)
            ],
        ]);

        try {
            $record = ClassCategoryHasStudentClass::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Record created successfully',
                'data' => $record
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        $record = ClassCategoryHasStudentClass::with(['studentClass', 'classCategory'])->find($id);

        if (!$record) {
            return response()->json([
                'status' => 'error',
                'message' => 'Record not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $record
        ]);
    }
    public function update(Request $request, $id)
    {
        $record = ClassCategoryHasStudentClass::find($id);

        if (!$record) {
            return response()->json([
                'status' => 'error',
                'message' => 'Record not found'
            ], 404);
        }

        $request->validate([
            'fees' => 'nullable|numeric',
            'student_classes_id' => 'nullable|integer|exists:student_classes,id',
            'class_category_id' => 'nullable|integer|exists:class_categories,id',
        ]);

        try {
            $record->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Record updated successfully',
                'data' => $record
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update record',
                'error' => $e->getMessage()
            ]);
        }
    }
}
