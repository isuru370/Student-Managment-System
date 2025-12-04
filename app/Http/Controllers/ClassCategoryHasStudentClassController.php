<?php

namespace App\Http\Controllers;

use App\Models\ClassCategoryHasStudentClass;
use App\Services\ClassCategoryHasStudentService;
use Illuminate\Http\Request;

class ClassCategoryHasStudentClassController extends Controller
{

    protected $classCategoryHasStudentService;

    public function __construct(ClassCategoryHasStudentService $classCategoryHasStudentService)
    {
        $this->classCategoryHasStudentService = $classCategoryHasStudentService;
    }

    public function fetchByClassId($classId)
    {
        return $this->classCategoryHasStudentService->fetchByClassId($classId);
    }
    public function searchClasses(Request $request)
    {
        return $this->classCategoryHasStudentService->searchClasses($request);
    }
    public function show($id)
    {
        return $this->classCategoryHasStudentService->show($id);
    }

    public function update(Request $request, $id)
    {
        return $this->classCategoryHasStudentService->update($request, $id);
    }
    public function store(Request $request)
    {
        return $this->classCategoryHasStudentService->store($request);
    }


    public function index()
    {
        return response()->json(ClassCategoryHasStudentClass::all());
    }
}
