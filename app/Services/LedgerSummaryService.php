<?php

namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\ExtraIncomes;
use App\Models\InstitutePayment;
use App\Models\Payments;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use App\Models\WelfarePayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Collection;

class LedgerSummaryService
{
    /**
     * Monthly Ledger Summary (Cash Based)
     */
    public function monthlyLedgerSummary(string $yearMonth): array
    {
        try {
            $date  = Carbon::createFromFormat('Y-m', $yearMonth);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();

            $openingBalance = $this->getOpeningBalance($yearMonth);

            $entries = collect()
                ->merge($this->classIncomeEntries($start, $end))
                ->merge($this->admissionEntries($start, $end))
                ->merge($this->extraIncomeEntries($start, $end))
                ->merge($this->teacherPaymentEntries($start, $end))
                ->merge($this->welfareEntries($start, $end))
                ->merge($this->instituteExpenseEntries($start, $end))
                ->sortBy('date')
                ->values();

            $ledger = $this->applyRunningBalance($entries, $openingBalance);

            $summary = $this->calculateSummary($ledger);

            return [
                'status' => 'success',
                'data' => [
                    'period' => [
                        'month' => $date->format('F Y'),
                        'start_date' => $start->format('Y-m-d'),
                        'end_date' => $end->format('Y-m-d'),
                    ],
                    'opening_balance' => round($openingBalance, 2),
                    'ledger' => $ledger,
                    'summary' => $summary,
                ]
            ];
        } catch (Exception $e) {
            Log::error('Ledger Summary Error', ['error' => $e->getMessage(), 'month' => $yearMonth]);
            return [
                'status' => 'error',
                'message' => 'Ledger calculation failed'
            ];
        }
    }

    /**
     * Get Opening Balance (Previous Month Closing Balance)
     */
    /**
     * Get Opening Balance (Previous Month Closing Balance)
     * SIMPLE NON-RECURSIVE VERSION
     */
    private function getOpeningBalance(string $yearMonth): float
    {
        try {
            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) return 0.0;

            // If it's the first month (2024-01), return 0
            if ($yearMonth <= '2024-01') {
                return 0.0;
            }

            // Get previous month
            $prevMonth = Carbon::createFromFormat('Y-m', $yearMonth)->subMonth();
            $prevYearMonth = $prevMonth->format('Y-m');

            // Calculate ALL months from start to previous month
            $startDate = Carbon::createFromFormat('Y-m', '2024-01');
            $runningBalance = 0.0;

            // Loop through each month from start to previous month
            $currentDate = $startDate->copy();
            while ($currentDate->format('Y-m') < $yearMonth) {
                $monthStart = $currentDate->copy()->startOfMonth();
                $monthEnd = $currentDate->copy()->endOfMonth();

                // Calculate this month's net change
                $monthNetChange = $this->calculateMonthNetChange($monthStart, $monthEnd);
                $runningBalance += $monthNetChange;

                // Move to next month
                $currentDate->addMonth();
            }

            return round($runningBalance, 2);
        } catch (Throwable $e) {
            Log::error('Opening balance error', ['month' => $yearMonth, 'error' => $e->getMessage()]);
            return 0.0;
        }
    }

    /**
     * Calculate net change for a specific month
     */
    private function calculateMonthNetChange(Carbon $start, Carbon $end): float
    {
        // 1. TOTAL RECEIPTS (FULL amounts, no deductions)
        $classIncome = (float) Payments::where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->sum('amount');

        $admission = (float) AdmissionPayments::whereBetween('created_at', [$start, $end])->sum('amount');
        $extraIncome = (float) ExtraIncomes::whereBetween('created_at', [$start, $end])->sum('amount');
        $welfareIncome = (float) WelfarePayment::where('status', 1)
            ->whereBetween('payment_date', [$start, $end])->sum('amount');

        $totalReceipts = $classIncome + $admission + $extraIncome + $welfareIncome;

        // 2. TOTAL PAYMENTS
        $teacherPayment = (float) TeacherPayment::where('status', 1)
            ->whereBetween('date', [$start, $end])->sum('payment');

        $instituteExpenses = (float) InstitutePayment::where('status', 1)
            ->whereBetween('date', [$start, $end])->sum('payment');

        $totalPayments = $teacherPayment + $instituteExpenses;

        // 3. NET CHANGE
        return $totalReceipts - $totalPayments;
    }

    /**
     * Class income ledger entries (FULL AMOUNT - No percentage deduction)
     */
    private function classIncomeEntries(Carbon $start, Carbon $end): Collection
    {
        return Payments::query()
            ->where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->selectRaw('DATE(payment_date) as day, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn($p) => [
                'date' => Carbon::parse($p->day)->startOfDay(),
                'description' => "Class Fee ({$p->count})",
                'receipt' => (float)$p->total,  // FULL AMOUNT - no deduction
                'payment' => 0.0
            ]);
    }

    /**
     * Admission ledger entries
     */
    private function admissionEntries(Carbon $start, Carbon $end): Collection
    {
        return AdmissionPayments::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn($p) => [
                'date' => Carbon::parse($p->day)->startOfDay(),
                'description' => 'Admission Fee',
                'receipt' => (float)$p->total,
                'payment' => 0.0
            ]);
    }

    /**
     * Extra income ledger entries
     */
    private function extraIncomeEntries(Carbon $start, Carbon $end): Collection
    {
        return ExtraIncomes::whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get()
            ->map(fn($e) => [
                'date' => Carbon::parse($e->created_at)->startOfDay(),
                'description' => $e->reason ?: 'Extra Income',
                'receipt' => (float)$e->amount,
                'payment' => 0.0
            ]);
    }

    /**
     * Teacher payment ledger entries
     */
    private function teacherPaymentEntries(Carbon $start, Carbon $end): Collection
    {
        return TeacherPayment::with('teacher:id,fname,lname')
            ->where('status', 1)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get()
            ->map(fn($t) => [
                'date' => Carbon::parse($t->date)->startOfDay(),
                'description' => $t->reason_code ? $t->reason_code . ' - ' . trim($t->teacher->fname . ' ' . $t->teacher->lname) : trim($t->teacher->fname . ' ' . $t->teacher->lname),
                'receipt' => 0.0,
                'payment' => (float)$t->payment
            ]);
    }

    /**
     * Welfare ledger entries
     */
    /**
     * Welfare ledger entries
     */
    /**
     * Welfare ledger entries - Teacher pays to Institute (INCOME for institute)
     */
    private function welfareEntries(Carbon $start, Carbon $end): Collection
    {
        return WelfarePayment::with('teacher')
            ->where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get()
            ->map(fn($w) => [
                'date' => Carbon::parse($w->payment_date)->startOfDay(),
                'description' => ($w->reason ?: 'Welfare Contribution') . ' - ' . ($w->teacher ? trim($w->teacher->fname . ' ' . $w->teacher->lname) : ''),
                'receipt' => (float)$w->amount,  // CORRECTED: This is INCOME for institute
                'payment' => 0.0  // Not an expense for institute
            ]);
    }

    /**
     * Institute expense ledger entries
     */
    private function instituteExpenseEntries(Carbon $start, Carbon $end): Collection
    {
        return InstitutePayment::where('status', 1)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get()
            ->map(fn($e) => [
                'date' => Carbon::parse($e->date)->startOfDay(),
                'description' => $e->reason ?: 'Institute Expense',
                'receipt' => 0.0,
                'payment' => (float)$e->payment
            ]);
    }

    /**
     * Apply running balance
     */
    private function applyRunningBalance($entries, float $openingBalance)
    {
        $balance = $openingBalance;

        return $entries->map(function ($e) use (&$balance) {
            $balance += $e['receipt'] - $e['payment'];
            return [
                'date' => Carbon::parse($e['date'])->format('d M Y'),
                'description' => $e['description'],
                'receipt' => $e['receipt'] > 0 ? number_format($e['receipt'], 2) : '',
                'payment' => $e['payment'] > 0 ? number_format($e['payment'], 2) : '',
                'balance' => number_format($balance, 2)
            ];
        });
    }

    /**
     * Calculate summary
     */
    private function calculateSummary($ledger)
    {
        $receipts = $ledger->sum(fn($l) => (float) str_replace(',', '', $l['receipt'] ?: 0));
        $payments = $ledger->sum(fn($l) => (float) str_replace(',', '', $l['payment'] ?: 0));

        return [
            'total_receipts' => round($receipts, 2),
            'total_payments' => round($payments, 2),
            'net_change' => round($receipts - $payments, 2),
            'closing_balance' => $ledger->last()['balance'] ?? '0.00'
        ];
    }
}
