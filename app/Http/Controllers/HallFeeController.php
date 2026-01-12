<?php

namespace App\Http\Controllers;

use App\Models\HallFee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HallFeeController extends Controller
{
public function index()
{
    // Current month start and end
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();

    // Paginated hall fees (active)
    $hallFees = HallFee::where('status', 1)
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->paginate(10);

    // Total amount for current month
    $totalAmount = HallFee::where('status', 1)
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->sum('amount');

    return view('hall_fees.index', compact('hallFees', 'totalAmount'));
}

    public function destroy($id)
    {
        $hallFee = HallFee::find($id);

        if (!$hallFee) {
            return redirect()->route('hall_fees.index')
                ->with('error', 'Hall fee not found.');
        }

        // Soft delete: set status = 0 and deleted_at
        $hallFee->status = 0;
        $hallFee->save();

        return redirect()->route('hall_fees.index')
            ->with('success', 'Hall fee deleted successfully.');
    }
}
