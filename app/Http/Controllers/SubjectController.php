<?php

namespace App\Http\Controllers;


use App\Services\SubjectService;
use Illuminate\Http\Request;


class SubjectController extends Controller
{

    protected $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    public function getDropdownSubject()
    {
        return $this->subjectService->getDropdownSubject();
    }
    public function fetchAllSubject()
    {
        return $this->subjectService->fetchAllSubject();
    }

    public function update(Request $request, $id)
    {
        return $this->subjectService->update($request, $id);
    }
    public function store(Request $request)
    {
        return $this->subjectService->store($request);
    }
}
