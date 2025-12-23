<?php

namespace App\Services;

use App\Models\ClassCategoryHasStudentClass;
use App\Models\StudentStudentStudentClass;

class StudentClassSeparateService
{
    public function getStudentSeparateCategories($student_id)
    {
        if (!$student_id) {
            return collect(); // return empty collection if no student_id
        }

        // 1) Get all student enrollments with studentClass
        $enrollments = StudentStudentStudentClass::with('studentClass')
            ->where('student_id', $student_id)
            ->get();

        // 2) Get unique studentClass IDs
        $studentClassIds = $enrollments->pluck('studentClass.id')->filter()->unique();

        if ($studentClassIds->isEmpty()) {
            return collect();
        }

        // 3) Fetch all categories for these classes at once
        $allCategories = ClassCategoryHasStudentClass::with(['classCategory', 'studentClass'])
            ->select([
                'id',
                'fees',
                'student_classes_id',
                'class_category_id'
            ])
            ->whereIn('student_classes_id', $studentClassIds)
            ->get()
            ->map(function ($item) {
                return [
                    'class_category_has_student_class_id' => $item->id,
                    'fees' => $item->fees,
                    'student_classes_id' => $item->student_classes_id,
                    'category_id' => $item->class_category_id,
                    'category_name' => $item->classCategory->category_name ?? null
                ];
            })
            ->groupBy('student_classes_id');

        $result = collect();

        // 4) Loop through enrollments and separate combined classes
        foreach ($enrollments as $enrollment) {
            $studentClass = $enrollment->studentClass;
            if (!$studentClass) continue;

            $categories = $allCategories->get($studentClass->id, collect());

            // Find combined record (Theory+Revision etc.)
            $combinedRecord = $categories->first(fn($c) => strpos($c->category_name, '+') !== false);

            $actualFee = $combinedRecord ? $combinedRecord->fees : null;
            $actualId = $combinedRecord ? $combinedRecord->class_category_has_student_class_id : null;

            // Push individual categories
            $categories->each(function ($cat) use ($studentClass, $combinedRecord, $actualFee, $actualId, $result) {
                // Skip the combined record itself
                if ($combinedRecord && $cat->class_category_has_student_class_id === $combinedRecord->class_category_has_student_class_id) {
                    return;
                }

                $result->push([
                    'classCategoryHasStudentClass_id' => $cat->class_category_has_student_class_id,
                    'actual_classCategoryHasStudentClass_id' => $actualId ?? $cat->class_category_has_student_class_id,
                    'fees' => $cat->fees,
                    'actual_fee' => $actualFee ?? $cat->fees,
                    'category_name' => $cat->category_name,
                    'class_name' => $studentClass->class_name
                ]);
            });
        }

        return $result;
    }
}
