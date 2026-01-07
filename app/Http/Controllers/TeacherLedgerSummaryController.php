<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TeacherLedgerSummaryService;
use Carbon\Carbon;

class TeacherLedgerSummaryController extends Controller
{
    protected $ledgerService;

    public function __construct(TeacherLedgerSummaryService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function index(Request $request)
    {
        // Default month - මෙම මාසය
        $defaultMonth = Carbon::now()->format('Y-m');
        
        // Request එකෙන් month ලැබුණේ නැත්නම් default month එක
        $yearMonth = $request->input('month', $defaultMonth);
        
        // Validate only if month is provided
        if ($request->filled('month')) {
            $request->validate([
                'month' => 'date_format:Y-m'
            ]);
        }
        
        $data = $this->ledgerService->monthlyLedgerSummary($yearMonth);

        return view('teacher_ledger_summary.index', compact('data', 'yearMonth'));
    }
}