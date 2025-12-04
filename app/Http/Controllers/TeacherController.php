<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Services\TeacherService;
use Illuminate\Http\Request;


class TeacherController extends Controller
{

    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }
    public function getDropdownTeachers()
    {
        return $this->teacherService->getDropdownTeachers();
    }

    public function fetchTeachers()
    {
        return $this->teacherService->fetchTeachers();
    }

    public function fetchActiveTeachers()
    {
        return $this->teacherService->fetchActiveTeachers();
    }

    public function fetchTeacher($id)
    {
        return $this->teacherService->fetchTeacher($id);
    }
    public function destroy($id)
    {
        return $this->teacherService->destroy($id);
    }

    public function reactivate($id)
    {
        return $this->teacherService->reactivate($id);
    }
    public function checkEmailUnique(Request $request)
    {
        return $this->teacherService->checkEmailUnique($request);
    }

    public function checkNicUnique(Request $request)
    {
        return $this->teacherService->checkNicUnique($request);
    }
    public function update(Request $request, $id)
    {
        return $this->teacherService->update($request, $id);
    }

    public function store(Request $request)
    {
        return $this->teacherService->store($request);
    }







    public function create()
    {
        return view('teachers.create');
    }

    public function index()
    {
        $teachers = Teacher::all();
        return view('teachers.index', compact('teachers'));
    }

    public function editPage($id)
    {
        return view('teachers.edit', compact('id'));
    }

    public function show($id)
    {
        return view('teachers.show', compact('id'));
    }

    public function classes($id)
    {
        return view('teachers.classes', compact('id'));
    }
    public function viewStudents($id)
    {
        return view('teachers.view_student', compact('id'));
    }
}
