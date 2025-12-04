<?php

namespace App\Http\Controllers;


use App\Services\GradeService;
use Illuminate\Http\Request;


class GradeController extends Controller
{

    protected $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function fetchDropdownGrade()
    {
        return $this->gradeService->fetchDropdownGrade();
    }

    public function fetchPublicDropdownGrade()
    {
        return $this->gradeService->fetchPublicDropdownGrade();
    }

    public function fetchAllGrade()
    {
        return $this->gradeService->fetchAllGrade();
    }

    public function update(Request $request, $id)
    {
        return $this->gradeService->update($request, $id);
    }

    public function store(Request $request)
    {
        return $this->gradeService->store($request);
    }
}
