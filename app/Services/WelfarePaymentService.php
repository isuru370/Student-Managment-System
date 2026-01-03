<?php

namespace App\Services;

use App\Models\Payments;
use App\Models\Teacher;
use App\Models\TeacherPayment;
use App\Models\WelfarePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WelfarePaymentService
{
    /**
     * Store a new welfare payment
     */
    public function store(Request $request): WelfarePayment
    {
        $validated = $request->validate([
            'teacher_id'     => 'required|exists:teachers,id',
            'amount'         => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|in:salary_deduction,cash,bank_transfer',
            'description'    => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {

            $paymentDate = Carbon::parse($validated['payment_date']);

            $exists = WelfarePayment::where('teacher_id', $validated['teacher_id'])
                ->whereBetween('payment_date', [
                    $paymentDate->copy()->startOfMonth(),
                    $paymentDate->copy()->endOfMonth(),
                ])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'teacher_id' =>
                    'This teacher already has a welfare payment for ' .
                        $paymentDate->format('F Y'),
                ]);
            }

            $validated['user_id'] = Auth::id();
            $validated['payment_method'] = $validated['payment_method'] ?? 'salary_deduction';
            $validated['status'] = true;

            $payment = WelfarePayment::create($validated);

            return $payment->load(['teacher', 'user']);
        });
    }

    /**
     * Fetch welfare payments for a specific month (YYYY-MM)
     */
    public function fetch()
    {
        try {
            $currentYearMonth = Carbon::now()->format('Y-m');
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // 1️⃣ Load all active teachers
            $teachers = Teacher::select('id', 'fname', 'lname', 'precentage')
                ->where('is_active', 1)
                ->get();

            $teacherIds = $teachers->pluck('id');

            // 2️⃣ Load monthly student payments grouped by teacher
            $monthlyPayments = Payments::selectRaw('
                student_classes.teacher_id,
                SUM(payments.amount) AS total_payment
            ')
                ->join(
                    'student_student_student_classes',
                    'payments.student_student_student_classes_id',
                    '=',
                    'student_student_student_classes.id'
                )
                ->join(
                    'student_classes',
                    'student_student_student_classes.student_classes_id',
                    '=',
                    'student_classes.id'
                )
                ->whereIn('student_classes.teacher_id', $teacherIds)
                ->where('payments.status', 1)
                ->whereBetween('payments.payment_date', [$startOfMonth, $endOfMonth])
                ->groupBy('student_classes.teacher_id')
                ->get()
                ->keyBy('teacher_id');

            // 3️⃣ Load advance payments (current month)
            $currentMonthYear = Carbon::now()->format('m Y');

            $advancePayments = TeacherPayment::selectRaw('
                teacher_id,
                SUM(payment) AS advance_total
            ')
                ->whereIn('teacher_id', $teacherIds)
                ->where('status', 1)
                ->where('payment_for', $currentMonthYear)
                ->groupBy('teacher_id')
                ->get()
                ->keyBy('teacher_id');

            // 4️⃣ Load welfare payments for current month
            $welfarePayments = WelfarePayment::selectRaw('
                teacher_id,
                SUM(amount) AS welfare_total
            ')
                ->whereIn('teacher_id', $teacherIds)
                ->where('status', 1)
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->groupBy('teacher_id')
                ->get()
                ->keyBy('teacher_id');

            // 5️⃣ Build result
            $result = [];

            foreach ($teachers as $teacher) {

                $totalForMonth = $monthlyPayments[$teacher->id]->total_payment ?? 0;
                $grossTeacherEarning = ($totalForMonth * $teacher->precentage) / 100;

                $advanceDeducted = $advancePayments[$teacher->id]->advance_total ?? 0;
                $netPayable = max($grossTeacherEarning - $advanceDeducted, 0);

                $welfareAmount = $welfarePayments[$teacher->id]->welfare_total ?? 0;
                $welfarePaid = $welfareAmount > 0;

                $result[] = [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->fname . ' ' . $teacher->lname,
                    'percentage' => $teacher->precentage,

                    'total_payments_this_month' => number_format($totalForMonth, 2, '.', ''),
                    'gross_earning' => number_format($grossTeacherEarning, 2, '.', ''),
                    'advance_deducted_this_month' => number_format($advanceDeducted, 2, '.', ''),
                    'teacher_earning' => number_format($netPayable, 2, '.', ''),

                    // 🆕 Welfare details
                    'welfare_amount' => number_format($welfareAmount, 2, '.', ''),
                    'welfare_paid' => $welfarePaid,
                    'remaining_balance' => number_format(
                        max($netPayable - $welfareAmount, 0),
                        2,
                        '.',
                        ''
                    ),
                ];
            }

            return [
                'status' => 'success',
                'year_month' => $currentYearMonth,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update welfare payment
     */
    public function update(Request $request, int $id): WelfarePayment
    {
        $validated = $request->validate([
            'teacher_id'     => 'sometimes|exists:teachers,id',
            'amount'         => 'sometimes|numeric|min:0.01',
            'payment_date'   => 'sometimes|date',
            'payment_method' => 'nullable|in:salary_deduction,cash,bank_transfer',
            'status'         => 'sometimes|boolean',
            'description'    => 'nullable|string',
        ]);

        return DB::transaction(function () use ($id, $validated) {

            $payment = WelfarePayment::findOrFail($id);

            if (
                isset($validated['teacher_id']) ||
                isset($validated['payment_date'])
            ) {
                $teacherId = $validated['teacher_id'] ?? $payment->teacher_id;
                $paymentDate = Carbon::parse(
                    $validated['payment_date'] ?? $payment->payment_date
                );

                $exists = WelfarePayment::where('teacher_id', $teacherId)
                    ->whereBetween('payment_date', [
                        $paymentDate->copy()->startOfMonth(),
                        $paymentDate->copy()->endOfMonth(),
                    ])
                    ->where('id', '!=', $payment->id)
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'teacher_id' =>
                        'This teacher already has a welfare payment for ' .
                            $paymentDate->format('F Y'),
                    ]);
                }
            }

            $payment->update($validated);

            return $payment->fresh(['teacher', 'user']);
        });
    }

    /**
     * Soft delete welfare payment
     */
    public function delete(int $id): bool
    {
        return WelfarePayment::findOrFail($id)->delete();
    }

    /**
     * Restore deleted welfare payment
     */
    public function restore(int $id): bool
    {
        return WelfarePayment::withTrashed()
            ->findOrFail($id)
            ->restore();
    }
}
