<?php

namespace App\Http\Controllers;


use App\Services\BankService;

class BankController extends Controller
{
    protected $bankService;

    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
    }

    public function fetchDropdownBanks()
    {
        return $this->bankService->fetchDropdownBanks();
    }

    public function fetchBanks()
    {
        return $this->bankService->fetchBanks();
    }
}
