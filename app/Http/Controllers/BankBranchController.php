<?php

namespace App\Http\Controllers;

use App\Services\BankBranchService;
use Symfony\Component\HttpFoundation\Request;

class BankBranchController extends Controller
{
    protected $bankBranchService;

    public function __construct(BankBranchService $bankBranchService)
    {
        $this->bankBranchService = $bankBranchService;
    }

    public function fetchDropdownBranches(Request $request)
    {
        return $this->bankBranchService->fetchDropdownBranches($request);
    }

    public function fetchBranches(Request $request)
    {
        return $this->bankBranchService->fetchBranches($request);
    }
}
