<?php

namespace App\Services;

use App\Models\Payments;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use App\Models\WelfarePayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Collection;

class TeacherLedgerSummaryService
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

            // පෙර මාසයේ closing balance
            $openingBalance = $this->getOpeningBalance($yearMonth);

            // Ledger entries එකතු කරන්න
            $entries = collect()
                ->merge($this->teacherIncomeEntries($start, $end))
                ->merge($this->teacherPaymentEntries($start, $end))
                ->merge($this->welfareEntries($start, $end))
                ->sortBy('date')
                ->values();

            // Running balance apply කරන්න
            $ledger = $this->applyRunningBalance($entries, $openingBalance);

            // Summary calculate කරන්න
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
     * Get Opening Balance (Previous Month Net Balance)
     */
    private function getOpeningBalance(string $yearMonth): float
    {
        try {
            if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth) || $yearMonth <= '2024-01') {
                return 0.0;
            }

            // Previous month start & end
            $startDate = Carbon::createFromFormat('Y-m', $yearMonth)
                ->subMonth()
                ->startOfMonth();

            $endDate = Carbon::createFromFormat('Y-m', $yearMonth)
                ->subMonth()
                ->endOfMonth();

            $totalBalance = 0;

            $teachers = Teacher::where('is_active', 1)->get();

            foreach ($teachers as $teacher) {
                // Database column name අනුව percentage/precentage
                $percentage = $teacher->percentage ?? $teacher->precentage ?? 0;

                // Teacher earnings (previous month only)
                $totalEarnings = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id)
                            ->where('is_active', 1);
                    })
                    ->sum('amount');

                // Payments already made to teacher - column name 'payment' විය හැකිය
                $totalPaid = TeacherPayment::where('status', 1)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('teacher_id', $teacher->id)
                    ->sum('payment'); // amount → payment වෙනස් කරන්න

                // Welfare payments from teacher
                $totalWelfare = WelfarePayment::where('status', 1)
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->where('teacher_id', $teacher->id)
                    ->sum('amount');

                // Teacher net balance
                $teacherNet = (($totalEarnings * $percentage) / 100) - $totalPaid - $totalWelfare;

                $totalBalance += $teacherNet;
            }

            return round($totalBalance, 2);
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
     * Teacher payment ledger entries
     */
    private function teacherIncomeEntries(Carbon $start, Carbon $end): Collection
    {
        $entries = collect();

        // Group payments by teacher and date
        $payments = Payments::with(['studentStudentClass.studentClass.teacher'])
            ->where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->whereHas('studentStudentClass.studentClass', function ($q) {
                $q->where('is_active', 1)
                    ->whereHas('teacher', function ($q2) {
                        $q2->where('is_active', 1);
                    });
            })
            ->orderBy('payment_date')
            ->get();

        // Group by teacher_id and date
        $groupedPayments = [];

        foreach ($payments as $p) {
            $teacher = $p->studentStudentClass->studentClass->teacher;
            if (!$teacher) {
                continue;
            }

            $date = Carbon::parse($p->payment_date)->format('Y-m-d');
            $key = $teacher->id . '|' . $date;

            if (!isset($groupedPayments[$key])) {
                $groupedPayments[$key] = [
                    'date' => Carbon::parse($p->payment_date)->startOfDay(),
                    'teacher' => $teacher,
                    'total_amount' => 0
                ];
            }

            // Calculate teacher's share for this payment
            $teacherShare = ($p->amount * $teacher->precentage) / 100;
            $groupedPayments[$key]['total_amount'] += $teacherShare;
        }

        // Create entries
        foreach ($groupedPayments as $group) {
            if ($group['total_amount'] > 0) {
                $entries->push([
                    'date' => $group['date'],
                    'description' => 'Income - ' . trim(
                        $group['teacher']->fname . ' ' . $group['teacher']->lname
                    ),
                    'receipt' => (float) round($group['total_amount'], 2),
                    'payment' => 0.0
                ]);
            }
        }

        return $entries;
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
                'description' => $t->reason_code
                    ? $t->reason_code . ' - ' . trim($t->teacher->fname . ' ' . $t->teacher->lname)
                    : trim($t->teacher->fname . ' ' . $t->teacher->lname),
                'receipt' => 0.0,
                'payment' => (float)$t->payment
            ]);
    }

    /**
     * Welfare ledger entries
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
                'receipt' => 0.0,  // ආදායමක් නොවේ
                'payment' => (float)$w->amount  // ගුරුවරයාගෙන් අඩු කරන මුදලක් (deduction)
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
