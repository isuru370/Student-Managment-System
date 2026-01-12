<?php

namespace App\Http\Controllers;

use App\Models\WelfareExpense;
use App\Services\WelfareExpenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class WelfareExpenseController extends Controller
{
    protected $welfareExpenseService;

    public function __construct(WelfareExpenseService $welfareExpenseService)
    {
        $this->welfareExpenseService = $welfareExpenseService;
    }

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $expenses = $this->welfareExpenseService->fetchAllWelfare();
        return view('welfare_expenses.index', compact('expenses'));
    }

    public function create()
    {
        $remaining = $this->welfareExpenseService->RemainingBalance();
        return view('welfare_expenses.create', compact('remaining'));
    }

    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'expense_for'    => 'required|string|max:255',
            'expense_type'   => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|string|max:100',
            'description'    => 'nullable|string',
            'remarks'        => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create new welfare expense
        $welfareExpense = new WelfareExpense();
        $welfareExpense->receipt_no = $this->welfareExpenseService->generateExpenseReceiptNo();
        $welfareExpense->expense_for = $request->input('expense_for');
        $welfareExpense->expense_type = $request->input('expense_type');
        $welfareExpense->amount = $request->input('amount');
        $welfareExpense->expense_date = $request->input('expense_date');
        $welfareExpense->payment_method = $request->input('payment_method');
        $welfareExpense->recorded_by = Auth::id();
        $welfareExpense->description = $request->input('description');
        $welfareExpense->remarks = $request->input('remarks');
        $welfareExpense->status = 1; // Approved/Paid

        $welfareExpense->save();

        return redirect()->route('welfare_expenses.index')
            ->with('success', 'Welfare expense recorded successfully.');
    }

    public function show($id)
    {
        $expense = $this->welfareExpenseService->getExpenseById($id);
        return view('welfare_expenses.show', compact('expense'));
    }

    public function destroy($id)
    {
        $deleted = $this->welfareExpenseService->destroy($id);

        if ($deleted) {
            return redirect()->route('welfare_expenses.index')
                ->with('success', 'Welfare expense cancelled successfully.');
        } else {
            return redirect()->route('welfare_expenses.index')
                ->with('error', 'Welfare expense not found.');
        }
    }
}
