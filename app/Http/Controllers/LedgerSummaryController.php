<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LedgerSummaryService;
use Illuminate\Http\Request;

class LedgerSummaryController extends Controller
{
    protected $ledgerService;

    public function __construct(LedgerSummaryService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Get monthly ledger summary
     */
    public function getMonthlySummary($yearMonth)
    {
        // Validate year-month format
        if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid year-month format. Use YYYY-MM format.'
            ], 400);
        }

        $result = $this->ledgerService->monthlyLedgerSummary($yearMonth);

        if ($result['status'] === 'success') {
            return response()->json($result);
        } else {
            return response()->json($result, 500);
        }
    }


    /**
     * Get current month ledger summary
     */
    public function getCurrentMonthSummary()
    {
        $currentMonth = now()->format('Y-m');
        return $this->getMonthlySummary($currentMonth);
    }

    /**
     * Get ledger summary for previous month
     */
    public function getPreviousMonthSummary()
    {
        $previousMonth = now()->subMonth()->format('Y-m');
        return $this->getMonthlySummary($previousMonth);
    }
}
