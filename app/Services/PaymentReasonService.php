<?php

namespace App\Services;

use App\Models\PaymentReason;
use Illuminate\Http\Request;
use Exception;

class PaymentReasonService
{
    public function fetchAllPaymentReason()
    {
        try {
            $result = PaymentReason::where('reason_code', '!=', 'salary')->get();

            return response()->json([
                'status' => "success",
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            // 🔍 Validate reason_code should not duplicate
            $request->validate([
                'reason_code' => 'required|unique:payment_reason,reason_code',
                'reason'      => 'required'
            ]);

            // ✔ Save data
            $data = PaymentReason::create([
                'reason_code' => $request->reason_code,
                'reason'      => $request->reason,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $paymentReason = PaymentReason::findOrFail($id);

            $paymentReason->update([
                'reason_code' => $request->reason_code,
                'reason'      => $request->reason,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $paymentReason
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropdownPaymentReason()
    {
        try {
            $payment_reasons = PaymentReason::select('id', 'reason_code', 'reason')
                ->where('reason_code', '!=', 'salary') // skip salary payments
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $payment_reasons
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch payment reason for dropdown',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $paymentReason = PaymentReason::findOrFail($id);

            $paymentReason->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment reason deleted successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
