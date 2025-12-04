<?php

namespace App\Services;

use App\Models\Bank;
use Exception;

class BankService
{
    public function fetchBanks()
    {
        return response()->json(Bank::all());
    }

    public function fetchDropdownBanks()
    {
        try {
            $banks = Bank::select('id', 'bank_name', 'bank_code')->get();

            return response()->json([
                'status' => 'success',
                'data' => $banks
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch banks for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
