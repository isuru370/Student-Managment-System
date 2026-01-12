<?php

namespace App\Services;

use App\Models\WelfareExpense;
use App\Models\WelfarePayment;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;

class WelfareExpenseService
{
    /**
     * Generate a unique receipt number for expenses
     */
    public function generateExpenseReceiptNo(): string
    {
        $date = now()->format('Ymd');
        $random = Str::upper(Str::random(6));
        return "SA-{$date}-{$random}";
    }


    public function fetchAllWelfare()
    {
        // 1. Total welfare received (all time)
        $totalWelfare = WelfarePayment::where('status', 1)
            ->sum('amount');

        // 2. Total welfare spent (all time)
        $totalSpent = WelfareExpense::where('status', 1)
            ->sum('amount');

        // 3. Remaining balance
        $remaining = $totalWelfare - $totalSpent;

        // 4. All approved expenses (latest first)
        $expenses = WelfareExpense::where('status', 1)
            ->orderBy('expense_date', 'desc')
            ->get();

        return [
            'total_welfare'     => $totalWelfare,
            'total_spent'       => $totalSpent,
            'remaining_balance' => $remaining,
            'expenses'          => $expenses,
        ];
    }

    public function fetchExpenses(string $yearMonth): Collection
    {
        $date = Carbon::parse($yearMonth);

        // 1. Total welfare received (payments)
        $totalWelfare = WelfarePayment::where('status', 1)
            ->whereYear('payment_date', $date->year)
            ->whereMonth('payment_date', $date->month)
            ->sum('amount');

        // 2. Total welfare spent (expenses)
        $totalSpent = WelfareExpense::where('status', 1)
            ->whereYear('expense_date', $date->year)
            ->whereMonth('expense_date', $date->month)
            ->sum('amount');

        // 3. Remaining balance
        $remaining = $totalWelfare - $totalSpent;

        // 4. Expense list (optional but useful)
        $expenses = WelfareExpense::where('status', 1)
            ->whereYear('expense_date', $date->year)
            ->whereMonth('expense_date', $date->month)
            ->orderBy('expense_date', 'desc')
            ->get();

        return collect([
            'year_month'        => $date->format('Y-m'),
            'total_welfare'     => $totalWelfare,
            'total_spent'       => $totalSpent,
            'remaining_balance' => $remaining,
            'expenses'          => $expenses,
        ]);
    }

    public function RemainingBalance(): float
    {
        $totalWelfare = WelfarePayment::where('status', 1)
            ->sum('amount');

        $totalSpent = WelfareExpense::where('status', 1)
            ->sum('amount');

        return $totalWelfare - $totalSpent;
    }

    public function getExpenseById(int $id): ?WelfareExpense
    {
        return WelfareExpense::find($id);
    }

    public function destroy($id): bool
    {
        $expense = WelfareExpense::find($id);

        if ($expense) {
            // Mark as cancelled
            $expense->status = 0;

            // Soft delete (sets deleted_at automatically)
            $expense->delete();

            return true;
        }

        return false;
    }
}
