<?php

namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\ExtraIncomes;
use App\Models\InstitutePayment;
use App\Models\Payments;
use App\Models\Teacher;
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
    private function getOpeningBalance(string $yearMonth): float
    {
        try {
            // YYYY-MM format validate කරනවා
            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
                return 0.0;
            }

            // 2024-01 නම් opening balance 0
            if ($yearMonth <= '2024-01') {
                return 0.0;
            }

            // Previous month dates
            $prevMonth = Carbon::createFromFormat('Y-m', $yearMonth)->subMonth();
            $prevMonthStart = $prevMonth->copy()->startOfMonth();
            $prevMonthEnd   = $prevMonth->copy()->endOfMonth();

            // 1. ආදායම් (Institute Share පමණක්)
            $totalInstituteReceipts = 0;

            // Class payments (teacher කොටස අඩු කරලා)
            $classPayments = Payments::with(['studentStudentClass.studentClass.teacher'])
                ->where('status', 1)
                ->whereBetween('payment_date', [$prevMonthStart, $prevMonthEnd])
                ->get();

            foreach ($classPayments as $payment) {
                $teacher = $payment->studentStudentClass->studentClass->teacher ?? null;

                if ($teacher && $teacher->is_active) {
                    // Database එකේ precentage column එක භාවිතා කරන්න
                    $teacherShare = ($payment->amount * $teacher->precentage) / 100;
                    $instituteShare = $payment->amount - $teacherShare;
                } else {
                    $instituteShare = $payment->amount;
                }

                $totalInstituteReceipts += $instituteShare;
            }

            // Admission payments (සම්පූර්ණයි)
            $totalInstituteReceipts += (float) AdmissionPayments::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
                ->sum('amount');

            // Extra incomes (සම්පූර්ණයි)
            $totalInstituteReceipts += (float) ExtraIncomes::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
                ->sum('amount');

            // 2. වියදම් (Institute expenses පමණක්)
            $totalExpenses = (float) InstitutePayment::where('status', 1)
                ->whereBetween('date', [$prevMonthStart, $prevMonthEnd])
                ->sum('payment');

            // Opening balance = Institute receipts - Institute expenses
            $openingBalance = $totalInstituteReceipts - $totalExpenses;

            return round($openingBalance, 2);
        } catch (Throwable $e) {
            Log::error('Opening balance error', [
                'month' => $yearMonth,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0.0;
        }
    }

    /**
     * Class income ledger entries (INSTITUTE SHARE ONLY - After teacher percentage deduction)
     */
    private function classIncomeEntries(Carbon $start, Carbon $end): Collection
    {
        $entries = collect();

        // Get payments grouped by date
        $paymentsByDate = Payments::with(['studentStudentClass.studentClass.teacher'])
            ->where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get()
            ->groupBy(function ($payment) {
                return Carbon::parse($payment->payment_date)->format('Y-m-d');
            });

        foreach ($paymentsByDate as $date => $payments) {
            $totalForDay = 0;
            $paymentCount = $payments->count();

            foreach ($payments as $payment) {
                $teacher = $payment->studentStudentClass->studentClass->teacher ?? null;

                if ($teacher && $teacher->is_active) {
                    // Teacher's share
                    $teacherShare = ($payment->amount * $teacher->precentage) / 100;
                    // Institute's share (full amount minus teacher's share)
                    $instituteShare = $payment->amount - $teacherShare;
                } else {
                    // No teacher assigned or teacher inactive - institute gets full amount
                    $instituteShare = $payment->amount;
                }

                $totalForDay += $instituteShare;
            }

            if ($totalForDay > 0) {
                $entries->push([
                    'date' => Carbon::parse($date)->startOfDay(),
                    'description' => "Class Fee ({$paymentCount})",
                    'receipt' => (float) round($totalForDay, 2),
                    'payment' => 0.0
                ]);
            }
        }

        return $entries;
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
