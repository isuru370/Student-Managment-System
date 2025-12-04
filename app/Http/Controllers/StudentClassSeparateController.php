<?php

namespace App\Http\Controllers;


use App\Services\StudentClassSeparateService;

class StudentClassSeparateController extends Controller
{
    protected $studentClassSeparateService;

    public function __construct(StudentClassSeparateService $studentClassSeparateService)
    {
        $this->studentClassSeparateService = $studentClassSeparateService; // fixed variable name
    }

    public function showStudentCategories($student_id)
    {
        $categories = $this->studentClassSeparateService->getStudentSeparateCategories($student_id);

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}
