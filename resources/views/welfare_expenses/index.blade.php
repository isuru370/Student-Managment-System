@extends('layouts.app')

@section('title', 'Welfare Expenses')
@section('page-title', 'Welfare Expenses Management')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Welfare Expenses</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">

        {{-- ================= SUMMARY CARDS ================= --}}
        <div class="row mb-4">

            {{-- Total Welfare --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-left-primary">
                    <div class="card-body">
                        <h6 class="text-muted">Total Welfare</h6>
                        <h3 class="text-primary">
                            Rs. {{ number_format($expenses['total_welfare'], 2) }}
                        </h3>
                    </div>
                </div>
            </div>

            {{-- Total Spent --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-left-danger">
                    <div class="card-body">
                        <h6 class="text-muted">Total Spent</h6>
                        <h3 class="text-danger">
                            Rs. {{ number_format($expenses['total_spent'], 2) }}
                        </h3>
                    </div>
                </div>
            </div>

            {{-- Remaining Balance --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-left-success">
                    <div class="card-body">
                        <h6 class="text-muted">Remaining Balance</h6>
                        <h3 class="text-success">
                            Rs. {{ number_format($expenses['remaining_balance'], 2) }}
                        </h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= EXPENSES TABLE ================= --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Welfare Expenses</h5>
                <a href="{{ route('welfare_expenses.create') }}" class="btn btn-primary btn-sm">
                    + Add Expense
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Receipt No</th>
                                <th>Expense For</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th class="text-right">Amount (Rs.)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expenses['expenses'] as $index => $expense)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $expense->receipt_no }}</td>
                                    <td>{{ $expense->expense_for }}</td>
                                    <td>{{ ucfirst($expense->expense_type) }}</td>
                                    <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                    <td class="text-right">
                                        {{ number_format($expense->amount, 2) }}
                                    </td>
                                    <td>
                                        @if ($expense->status == 1)
                                            <span class="badge badge-success text-primary">Approved</span>
                                        @else
                                            <span class="badge badge-danger text-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- View Button --}}
                                        <a href="{{ route('welfare_expenses.show', $expense->id) }}" class="btn btn-sm btn-info">View</a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('welfare_expenses.destroy', $expense->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to cancel this expense?');">
                                            Delete
                                        </button>
                                    </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No welfare expenses found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
@endsection