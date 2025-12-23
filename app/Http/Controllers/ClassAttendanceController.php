<?php

namespace App\Http\Controllers;


use App\Services\ClassAttendanceService;
use Illuminate\Http\Request;

class ClassAttendanceController extends Controller
{

    protected $classAttendanceService;

    public  function __construct(ClassAttendanceService $classAttendanceService)
    {
        $this->classAttendanceService = $classAttendanceService;
    }

    public  function fetchClassesByDate(Request $request)
    {
        return $this->classAttendanceService->fetchClassesByDate($request);
    }
    public  function fetchByClassCategoryHasStudentClasses($classCategoryHasStudentClassId)
    {
        return $this->classAttendanceService->fetchByClassCategoryHasStudentClasses($classCategoryHasStudentClassId);
    }
    public  function fetchClassAttendanceByStudent(Request $request)
    {
        return $this->classAttendanceService->fetchClassAttendanceByStudent($request);
    }

    public  function testConnection()
    {
        return $this->classAttendanceService->testConnection();
    }

    public  function update(Request $request, $id)
    {
        return $this->classAttendanceService->update($request, $id);
    }

    public  function storeBulk(Request $request)
    {
        return $this->classAttendanceService->storeBulk($request);
    }

    public  function bulkDelete(Request $request)
    {
        return $this->classAttendanceService->bulkDelete($request);
    }

    public  function store(Request $request)
    {
        return $this->classAttendanceService->store($request);
    }

    // Controller එකේ  
    public function indexPage($id)
    {
        return view('class-attendance.index', compact('id'));
    }
    public function createPage($id)
    {
        return view('class-attendance.create', compact('id'));
    }


    public function editPage()
    {
        return view('class-attendance.edit');
    }
}
