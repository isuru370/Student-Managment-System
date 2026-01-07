<?php

namespace App\Http\Controllers;

use App\Models\WelfareExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WelfareExpenseController extends Controller
{
    public function index(Request $request)
    {

        return view('welfare_expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('welfare_expenses.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('welfare_expenses.index')
            ->with('success', 'Welfare expense recorded successfully. Receipt No: ');
    }

    public function show($id)
    {
        return view('welfare_expenses.show', compact('welfareExpense'));
    }

    public function edit(Request $request, $id)
    {
        return view('welfare_expenses.edit', compact('welfareExpense'));
    }

}