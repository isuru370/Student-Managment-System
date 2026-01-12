@extends('layouts.app')

@section('title', 'Welfare Expense Details')
@section('page-title', 'Welfare Expense Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('welfare_expenses.index') }}">Welfare Expenses</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Card with expense details --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Expense Details - {{ $expense->receipt_no }}</h5>
            <a href="{{ route('welfare_expenses.index') }}" class="btn btn-secondary btn-sm">
                Back to List
            </a>
        </div>

        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Expense For:</strong>
                    <p>{{ $expense->expense_for }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Expense Type:</strong>
                    <p>{{ ucfirst($expense->expense_type) }}</p>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Expense Date:</strong>
                    <p>{{ $expense->expense_date->format('Y-m-d') }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Amount (Rs.):</strong>
                    <p>{{ number_format($expense->amount, 2) }}</p>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Payment Method:</strong>
                    <p>{{ ucfirst($expense->payment_method) }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong>
                    @if($expense->status == 1)
                        <span class="badge badge-success text-primary">Approved</span>
                    @else
                        <span class="badge badge-danger text-danger">Cancelled</span>
                    @endif
                </div>
            </div>

            @if($expense->description)
                <div class="row mb-2">
                    <div class="col-md-12">
                        <strong>Description:</strong>
                        <p>{{ $expense->description }}</p>
                    </div>
                </div>
            @endif

            @if($expense->remarks)
                <div class="row mb-2">
                    <div class="col-md-12">
                        <strong>Remarks:</strong>
                        <p>{{ $expense->remarks }}</p>
                    </div>
                </div>
            @endif

            <div class="text-end mt-3">
                <a href="{{ route('welfare_expenses.index') }}" class="btn btn-secondary">
                    Back
                </a>
                @if($expense->status == 1)
                    <form action="{{ route('welfare_expenses.destroy', $expense->id) }}" method="POST" style="display:inline-block;" 
                          onsubmit="return confirm('Are you sure you want to cancel this expense?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            Cancel Expense
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
