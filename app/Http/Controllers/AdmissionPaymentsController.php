<?php

namespace App\Http\Controllers;

use App\Services\StudentAdmissionPaymentService;
use Illuminate\Http\Request;


class AdmissionPaymentsController extends Controller
{

    protected $admissionPyamentsService;

    public function __construct(StudentAdmissionPaymentService $admissionPaymentsService)
    {
        $this->admissionPyamentsService = $admissionPaymentsService;
    }

    public function fetchPayAdmissions()
    {
        return $this->admissionPyamentsService->fetchPayAdmissions();
    }

    public function fetchStudentAdmissions(Request $request)
    {
        return $this->admissionPyamentsService->fetchStudentAdmissions($request);
    }

    public function storeBulkAdmissionPayment(Request $request)
    {
        return $this->admissionPyamentsService->storeBulkAdmissionPayment($request);
    }

     public function fetchPayAdmissionsStaticCart($yser, $month)
    {
        return $this->admissionPyamentsService->fetchPayAdmissionsStaticCart($yser, $month);
    }


    /*
     * web page route
    */

    public function payAdmissionPage()
    {
        return view('admissions.admission_payment');
    }
}
