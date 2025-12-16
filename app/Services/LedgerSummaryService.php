<?php

namespace App\Services;

use App\Models\AdmissionPayments;
use App\Models\ExtraIncomes;
use App\Models\InstitutePayment;
use App\Models\Payments;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

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
                ->merge($this->instituteExpenseEntries($start, $end))
                ->sortBy('date')
                ->values();

            $ledger = $this->applyRunningBalance($entries, $openingBalance);

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
                    'summary' => $this->calculateSummary($ledger),
                ]
            ];
        } catch (Exception $e) {
            Log::error('Ledger Summary Error', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Ledger calculation failed'
            ];
        }
    }

    /**
     * Get Daily Amounts for a specific month
     */
    public function getDailyAmounts(string $yearMonth): array
    {
        try {
            $date = Carbon::createFromFormat('Y-m', $yearMonth);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            // Get all entries grouped by date
            $entries = collect()
                ->merge($this->classIncomeEntries($start, $end))
                ->merge($this->admissionEntries($start, $end))
                ->merge($this->extraIncomeEntries($start, $end))
                ->merge($this->teacherPaymentEntries($start, $end))
                ->merge($this->instituteExpenseEntries($start, $end));

            // Group by date and calculate daily totals
            $dailyTotals = [];

            foreach ($entries as $entry) {
                $dateKey = Carbon::parse($entry['date'])->format('Y-m-d');

                if (!isset($dailyTotals[$dateKey])) {
                    $dailyTotals[$dateKey] = [
                        'date' => $dateKey,
                        'receipt' => 0,
                        'payment' => 0,
                    ];
                }

                $dailyTotals[$dateKey]['receipt'] += $entry['receipt'];
                $dailyTotals[$dateKey]['payment'] += $entry['payment'];
            }

            // Sort by date
            ksort($dailyTotals);

            // Format the results
            $formattedDailyTotals = array_map(function ($day) {
                return [
                    'date' => Carbon::parse($day['date'])->format('d M Y'),
                    'receipt' => number_format($day['receipt'], 2),
                    'payment' => number_format($day['payment'], 2),
                    'net' => number_format($day['receipt'] - $day['payment'], 2),
                ];
            }, array_values($dailyTotals));

            return [
                'status' => 'success',
                'data' => [
                    'daily_totals' => $formattedDailyTotals,
                    'monthly_summary' => [
                        'total_receipts' => number_format(array_sum(array_column($dailyTotals, 'receipt')), 2),
                        'total_payments' => number_format(array_sum(array_column($dailyTotals, 'payment')), 2),
                        'net_total' => number_format(
                            array_sum(array_column($dailyTotals, 'receipt')) -
                                array_sum(array_column($dailyTotals, 'payment')),
                            2
                        ),
                    ]
                ]
            ];
        } catch (Exception $e) {
            Log::error('Daily Amounts Error', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Daily amounts calculation failed'
            ];
        }
    }

    /**
     * Get Daily Cash Flow (with optional opening balance)
     */
    public function getDailyCashFlow(string $yearMonth, bool $includeOpeningBalance = false): array
    {
        try {
            $date = Carbon::createFromFormat('Y-m', $yearMonth);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $openingBalance = $includeOpeningBalance ? $this->getOpeningBalance($yearMonth) : 0;

            // Get all entries
            $entries = collect()
                ->merge($this->classIncomeEntries($start, $end))
                ->merge($this->admissionEntries($start, $end))
                ->merge($this->extraIncomeEntries($start, $end))
                ->merge($this->teacherPaymentEntries($start, $end))
                ->merge($this->instituteExpenseEntries($start, $end))
                ->sortBy('date');

            // Group by date
            $dailyEntries = [];
            foreach ($entries as $entry) {
                $dateKey = Carbon::parse($entry['date'])->format('Y-m-d');

                if (!isset($dailyEntries[$dateKey])) {
                    $dailyEntries[$dateKey] = [
                        'date' => $dateKey,
                        'receipt' => 0,
                        'payment' => 0,
                        'details' => []
                    ];
                }

                $dailyEntries[$dateKey]['receipt'] += $entry['receipt'];
                $dailyEntries[$dateKey]['payment'] += $entry['payment'];
                $dailyEntries[$dateKey]['details'][] = [
                    'description' => $entry['description'],
                    'receipt' => $entry['receipt'],
                    'payment' => $entry['payment']
                ];
            }

            // Sort by date
            ksort($dailyEntries);

            // Calculate running balance
            $balance = $openingBalance;
            $dailyCashFlow = [];

            foreach ($dailyEntries as $day) {
                $net = $day['receipt'] - $day['payment'];
                $balance += $net;

                $dailyCashFlow[] = [
                    'date' => Carbon::parse($day['date'])->format('d M Y'),
                    'receipt' => number_format($day['receipt'], 2),
                    'payment' => number_format($day['payment'], 2),
                    'net_change' => number_format($net, 2),
                    'balance' => number_format($balance, 2),
                    'details' => $day['details']
                ];
            }

            return [
                'status' => 'success',
                'data' => [
                    'opening_balance' => number_format($openingBalance, 2),
                    'daily_cash_flow' => $dailyCashFlow,
                    'closing_balance' => end($dailyCashFlow)['balance'] ?? number_format($openingBalance, 2)
                ]
            ];
        } catch (Exception $e) {
            Log::error('Daily Cash Flow Error', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Daily cash flow calculation failed'
            ];
        }
    }

    /**
     * Get Date-wise Detailed Report
     */
    public function getDatewiseReport(string $date): array
    {
        try {
            $targetDate = Carbon::parse($date);
            $start = $targetDate->copy()->startOfDay();
            $end = $targetDate->copy()->endOfDay();

            $entries = collect()
                ->merge($this->classIncomeEntries($start, $end))
                ->merge($this->admissionEntries($start, $end))
                ->merge($this->extraIncomeEntries($start, $end))
                ->merge($this->teacherPaymentEntries($start, $end))
                ->merge($this->instituteExpenseEntries($start, $end))
                ->sortBy('date')
                ->values();

            $totalReceipt = $entries->sum('receipt');
            $totalPayment = $entries->sum('payment');
            $netTotal = $totalReceipt - $totalPayment;

            $formattedEntries = $entries->map(function ($e) {
                return [
                    'date' => Carbon::parse($e['date'])->format('h:i A'),
                    'description' => $e['description'],
                    'receipt' => $e['receipt'] > 0 ? number_format($e['receipt'], 2) : '',
                    'payment' => $e['payment'] > 0 ? number_format($e['payment'], 2) : '',
                ];
            });

            return [
                'status' => 'success',
                'data' => [
                    'date' => $targetDate->format('d M Y'),
                    'entries' => $formattedEntries,
                    'totals' => [
                        'total_receipt' => number_format($totalReceipt, 2),
                        'total_payment' => number_format($totalPayment, 2),
                        'net_total' => number_format($netTotal, 2),
                    ]
                ]
            ];
        } catch (Exception $e) {
            Log::error('Datewise Report Error', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Datewise report calculation failed'
            ];
        }
    }

    /**
     * Opening balance from previous month (cash net)
     */
    private function getOpeningBalance(string $yearMonth): float
    {
        try {
            $startOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)
                ->subMonth()
                ->startOfMonth();

            $endOfMonth = Carbon::createFromFormat('Y-m', $yearMonth)
                ->subMonth()
                ->endOfMonth();

            // Admission payments
            $admissionPayment = AdmissionPayments::whereBetween(
                'created_at',
                [$startOfMonth, $endOfMonth]
            )->sum('amount');

            // Institution income from classes (teacher percentage deducted)
            $teachers = Teacher::where('is_active', 1)->get();

            $totalIncomeFromClasses = 0;

            foreach ($teachers as $teacher) {
                $payments = Payments::where('status', 1)
                    ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                    ->whereHas('studentStudentClass.studentClass', function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->sum('amount');

                $teacherShare = round(($payments * $teacher->precentage) / 100, 2);
                $institutionShare = round($payments - $teacherShare, 2);

                $totalIncomeFromClasses += $institutionShare;
            }

            // Extra income
            $extraIncome = ExtraIncomes::whereBetween(
                'created_at',
                [$startOfMonth, $endOfMonth]
            )->sum('amount');

            // Institute expenses ONLY
            $totalExpenses = InstitutePayment::where('status', 1)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('payment');

            // Final hand total
            $grossIncome = $totalIncomeFromClasses + $extraIncome;
            $netTotal = $admissionPayment + ($grossIncome - $totalExpenses);

            return round($netTotal, 2);
        } catch (Exception $e) {
            Log::error('Opening balance error: ' . $e->getMessage());
            return 0;
        }
    }


    /**
     * Ledger entries – class income
     */
    private function classIncomeEntries($start, $end)
    {
        return Payments::where('status', 1)
            ->whereBetween('payment_date', [$start, $end])
            ->selectRaw('DATE(payment_date) as payment_day, SUM(amount) as daily_total, COUNT(*) as fee_count')
            ->groupBy('payment_day')
            ->get()
            ->map(function ($p) {
                $description = 'Class Fee';
                if ($p->fee_count > 0) {
                    $description .= ' (' . $p->fee_count . ($p->fee_count === 1 ? ' fee' : ' fees') . ')';
                }

                return [
                    'date' => Carbon::parse($p->payment_day)->startOfDay(),
                    'description' => $description,
                    'receipt' => (float) $p->daily_total,
                    'payment' => 0
                ];
            });
    }

    /**
     * Ledger entries – admission fees
     */
    private function admissionEntries($start, $end)
    {
        // First, group by date and sum the amounts
        $groupedAdmissions = AdmissionPayments::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as admission_date, SUM(amount) as total_amount')
            ->groupBy('admission_date')
            ->get();

        return $groupedAdmissions->map(fn($group) => [
            'date' => Carbon::parse($group->admission_date)->startOfDay(),
            'description' => 'Admission Fee',
            'receipt' => (float) $group->total_amount,
            'payment' => 0
        ]);
    }

    /**
     * Ledger entries – extra income
     */
    private function extraIncomeEntries($start, $end)
    {
        return ExtraIncomes::whereBetween('created_at', [$start, $end])
            ->get()
            ->map(fn($e) => [
                'date' => $e->created_at,
                'description' => $e->reason ?? 'Extra Income',
                'receipt' => (float) $e->amount,
                'payment' => 0
            ]);
    }

    /**
     * Ledger entries – teacher payments
     * Filtered by payment_for field (e.g., "05 2025")
     */
    private function teacherPaymentEntries($start, $end)
    {
        return TeacherPayment::with('teacher:id,fname,lname')
            ->where('status', 1)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->map(function ($t) {
                $name = trim(($t->teacher->fname ?? '') . ' ' . ($t->teacher->lname ?? ''));

                // Format: "reason_code - teacher_name"
                if ($t->reason_code) {
                    $description = $t->reason_code . ' - ' . $name;
                } else {
                    $description = $name; // Fallback if no reason_code
                }

                return [
                    'date' => $t->date,
                    'description' => $description,
                    'receipt' => 0,
                    'payment' => (float) $t->payment
                ];
            });
    }
    /**
     * Ledger entries – institute expenses
     */
    private function instituteExpenseEntries($start, $end)
    {
        return InstitutePayment::where('status', 1)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->map(fn($e) => [
                'date' => $e->date,
                'description' => $e->reason ?? 'Institute Expense',
                'receipt' => 0,
                'payment' => (float) $e->payment
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
     * Ledger summary
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
