<?php

namespace App\Http\Controllers;

use App\Services\InstitutePaymentService;
use Illuminate\Http\Request;

class InstitutePaymentController extends Controller
{
    protected $institutePaymentService;

    public function __construct(InstitutePaymentService $institutePaymentService)
    {
        $this->institutePaymentService = $institutePaymentService;
    }

    public function fetchExtraIncome($yearMonth)
    {
        return $this->institutePaymentService->fetchExtraIncome($yearMonth);
    }
    public function fetchInstituteExpenses($yearMonth)
    {
        return $this->institutePaymentService->fetchInstituteExpenses($yearMonth);
    }

    public function fetchYearlyIncomeChart($year)
    {
        return $this->institutePaymentService->fetchYearlyIncomeChart($year);
    }

    public function fetchInstitutePaymentByMonth($yearMonth)
    {
        return $this->institutePaymentService->fetchInstitutePaymentByMonth($yearMonth);
    }
    public function institutePaymentStore(Request $request)
    {
        return $this->institutePaymentService->institutePaymentStore($request);
    }

    public function institutePaymentDestroy($id)
    {
        return $this->institutePaymentService->institutePaymentDestroy($id);
    }

    public function extraIncomeStore(Request $request)
    {
        return $this->institutePaymentService->extraIncomeStore($request);
    }

    public function extraIncomeDelete($id)
    {
        return $this->institutePaymentService->extraIncomeDelete($id);
    }


    // web page route

    public function indexPage()
    {
        return view('institute_payment.index');
    }

    public function extraIncomePage()
    {
        return view('institute_payment.extra');
    }
    public function expensesPage()
    {
        return view('institute_payment.expenses');
    }

    public function ledgerPage()
    {
        return view('institute_payment.ledger');
    }
}
