<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentAdmissionService;

class AdmissionsController extends Controller
{

    protected $admissionService;

    public function __construct(StudentAdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

    public function getDropdownAdmissions()
    {
        return $this->admissionService->getDropdownAdmissions();
    }

    public function fetchAdmissions()
    {
        return $this->admissionService->fetchAdmissions();
    }

    public function showAdmission($id)
    {
        return $this->admissionService->showAdmission($id);
    }

    public function updateAdmission(Request $request, $id)
    {
        return $this->admissionService->updateAdmission($request, $id);
    }

    public function storeAdmission(Request $request)
    {
        return $this->admissionService->storeAdmission($request);
    }

    /* 
    * Web page Route
    */
    public function indexPage()
    {
        return view('admissions.index');
    }
}
