@extends('layouts.app')

@section('title', 'Hall Fees')
@section('page-title', 'Hall Fees Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Hall Fee Manage</li>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid">

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Hall Fees</h5>
            {{-- Optional: Add Hall Fee Button --}}
            {{-- <a href="{{ route('hall_fees.create') }}" class="btn btn-primary btn-sm">+ Add Fee</a> --}}
        </div>

        <div class="card-body">

            {{-- Total Amount for Current Month --}}
            @isset($totalAmount)
            <div class="mb-3">
                <h6>Total Hall Fees This Month: Rs. {{ number_format($totalAmount, 2) }}</h6>
            </div>
            @endisset

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Hall Name</th>
                            <th>Amount (Rs.)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hallFees as $index => $fee)
                            <tr>
                                <td>{{ $hallFees->firstItem() + $index }}</td>
                                <td>{{ $fee->hall->hall_name ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($fee->amount, 2) }}</td>
                                <td>
                                    @if ($fee->status == 1)
                                        <span class="badge text-white" style="background-color: #007bff;">Active</span>
                                    @else
                                        <span class="badge text-white" style="background-color: #dc3545;">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('hall_fees.destroy', $fee->id) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Are you sure you want to delete this hall fee?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hall fees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-3 d-flex justify-content-end">
                {{ $hallFees->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
