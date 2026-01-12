@extends('layouts.app')

@section('title', 'Create Welfare Expense')
@section('page-title', 'Create New Welfare Expense')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('welfare_expenses.index') }}">Welfare Expenses</a>
    </li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Remaining Balance Alert --}}
    <div class="alert alert-info">
        <strong>Remaining Welfare Balance:</strong>
        Rs. {{ number_format($remaining, 2) }}
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Add Welfare Expense</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('welfare_expenses.store') }}" method="POST" id="expenseForm">
                @csrf

                <input type="hidden" id="remainingBalance" value="{{ $remaining }}">

                <div class="row">

                    {{-- Expense For --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expense For <span class="text-danger">*</span></label>
                        <input type="text" name="expense_for" class="form-control" required>
                    </div>

                    {{-- Expense Type --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expense Type <span class="text-danger">*</span></label>
                        <select name="expense_type" class="form-control" required>
                            <option value="">-- Select Type --</option>
                            <option value="medical">Medical</option>
                            <option value="education">Education</option>
                            <option value="emergency">Emergency</option>
                            <option value="general">General</option>
                        </select>
                    </div>

                    {{-- Expense Date --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" required>
                    </div>

                    {{-- Amount --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Amount (Rs.) <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="amount"
                               id="amount"
                               class="form-control"
                               step="0.01"
                               min="0"
                               required>
                        <small class="text-danger d-none" id="amountError">
                            Amount cannot exceed remaining balance.
                        </small>
                    </div>

                    {{-- Payment Method --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    {{-- Remarks --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>

                </div>

                <div class="text-end">
                    <a href="{{ route('welfare_expenses.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        Save Expense
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const amountInput = document.getElementById('amount');
    const remaining = parseFloat(document.getElementById('remainingBalance').value);
    const errorText = document.getElementById('amountError');
    const submitBtn = document.getElementById('submitBtn');

    amountInput.addEventListener('input', function () {
        const amount = parseFloat(this.value);

        if (amount > remaining) {
            errorText.classList.remove('d-none');
            submitBtn.disabled = true;
        } else {
            errorText.classList.add('d-none');
            submitBtn.disabled = false;
        }
    });
</script>
@endpush
