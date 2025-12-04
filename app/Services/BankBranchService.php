<?php

namespace App\Services;

use App\Models\BankBranch;
use Exception;

class BankBranchService
{
    public function fetchBranches($bankId)
    {
        return response()->json(
            BankBranch::where('bank_id', $bankId)->get()
        );
    }

    public function fetchDropdownBranches($bankId)
    {
        try {
            $branches = BankBranch::where('bank_id', $bankId)
                ->select('id', 'bank_id', 'branch_name', 'branch_code')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $branches
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch bank branches for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
