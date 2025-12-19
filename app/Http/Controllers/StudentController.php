<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{

    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function fetchStudents()
    {
        return $this->studentService->fetchStudents();
    }
    public function fetchActiveStudents()
    {
        return $this->studentService->fetchActiveStudents();
    }
    public function fetchNotPaidAdmissionStudent()
    {
        return $this->studentService->fetchNotPaidAdmissionStudent();
    }
    public function fetchStudent($id)
    {
        return $this->studentService->fetchStudent($id);
    }

    public function filterByCreatedDate(Request $request)
    {
        return $this->studentService->filterByCreatedDate($request);
    }

    public function fetchStudentCustomId($customId)
    {
        return $this->studentService->fetchStudentCustomId($customId);
    }
    public function updateStudentImage(Request $request, $custom_id)
    {
        return $this->studentService->updateStudentImage($request, $custom_id);
    }
    public function analytics($student_id)
    {
        return $this->studentService->studentAnalytics($student_id);
    }

    public function destroy($id)
    {
        return $this->studentService->destroy($id);
    }

    public function reactivate($id)
    {
        return $this->studentService->reactivate($id);
    }

    public function update(Request $request, $id)
    {
        return $this->studentService->update($request, $id);
    }

    public function store(Request $request)
    {
        return $this->studentService->store($request);
    }

    public function generateCustomIdAPI(Request $request)
    {
        return $this->studentService->generateCustomIdAPI($request);
    }


    public function publicStudentRegister(Request $request)
    {
        return $this->studentService->publicStudentRegister($request);
    }

    // web page route
    public function index()
    {
        $students = Student::all(); // Fixed variable name
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }


    public function editPage($custom_id)
    {
        return view('students.edit', compact('custom_id'));
    }

    public function show($custom_id)
    {
        return view('students.show', compact('custom_id'));
    }

    public function studentImages()
    {
        return view('students.student_images');
    }

    public function allImages()
    {
        return view('students.images');
    }



    public function addStudentToClass($class_id)
    {
        return view('students.add_student_to_class', compact('class_id'));
    }

    public function addStudentToSingleClass($student_id)
    {
        return view('students.add_student_to_single_class', compact('student_id'));
    }

    public function studentAnalytic($student_id)
    {
        return view('students.student_analytic', compact('student_id'));
    }
}
