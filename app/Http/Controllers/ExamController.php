<?php

namespace App\Http\Controllers;

use App\Services\ExamService;

class ExamController extends Controller
{

    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function fetchExam($class_id)
    {
        return $this->examService->fetchExam($class_id);
    }

    // web page route
    public function indexPage()
    {
        view('student_exam.index');
    }
}
