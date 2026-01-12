@extends('layouts.app')

@section('title', 'Welfare Expenses Dashboard')
@section('page-title', 'Welfare Expenses Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Welfare Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Today's Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($summary['today']['amount'] ?? 0, 2) }}
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-nowrap">{{ $summary['today']['count'] ?? 0 }} records</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($summary['this_month']['amount'] ?? 0, 2) }}
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-nowrap">{{ $summary['this_month']['count'] ?? 0 }} records</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approval</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['pending_count'] ?? 0 }}
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-nowrap">Requires attention</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Approved This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['approved_count'] ?? 0 }}
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-nowrap">Completed expenses</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Expenses & Quick Actions -->
    <div class="row">
        <!-- Recent Expenses -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Expenses</h6>
                    <a href="{{ route('welfare-expenses.index') }}" class="btn btn-sm btn-primary">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary['recent_expenses'] ?? [] as $expense)
                                <tr>
                                    <td>
                                        <strong>{{ $expense->receipt_no }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $expense->expense_for }}</small>
                                    </td>
                                    <td class="font-weight-bold text-primary">
                                        {{ $expense->formatted_amount }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $expense->expense_type_text }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($expense->isApproved())
                                            <span class="badge badge-success">{{ $expense->status_text }}</span>
                                        @elseif($expense->isPending())
                                            <span class="badge badge-warning">{{ $expense->status_text }}</span>
                                        @elseif($expense->isRejected())
                                            <span class="badge badge-danger">{{ $expense->status_text }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $expense->status_text }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $expense->getFormattedExpenseDate('Y-m-d') }}
                                        <br>
                                        <small class="text-muted">{{ $expense->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('welfare-expenses.show', $expense->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($expense->isPending() && auth()->user()->can('approve-welfare-expense', $expense))
                                        <form action="{{ route('welfare-expenses.approve', $expense->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Approve this expense?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        No recent expenses found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    @can('create-welfare-expense')
                    <a href="{{ route('welfare-expenses.create') }}" class="btn btn-primary btn-block mb-3">
                        <i class="fas fa-plus mr-2"></i> Add New Expense
                    </a>
                    @endcan
                    
                    @can('view-welfare-expenses')
                    <a href="{{ route('welfare-expenses.pending') }}" class="btn btn-warning btn-block mb-3">
                        <i class="fas fa-clock mr-2"></i> View Pending Expenses
                    </a>
                    
                    <a href="{{ route('welfare-expenses.statistics') }}" class="btn btn-info btn-block mb-3">
                        <i class="fas fa-chart-bar mr-2"></i> View Statistics
                    </a>
                    
                    <a href="{{ route('welfare-expenses.export') }}" class="btn btn-success btn-block mb-3">
                        <i class="fas fa-file-export mr-2"></i> Export Data
                    </a>
                    @endcan
                    
                    <hr>
                    
                    <h6 class="font-weight-bold mb-3">Quick Filters</h6>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="{{ route('welfare-expenses.index', ['status' => \App\Models\WelfareExpense::STATUS_APPROVED_PAID]) }}" 
                               class="btn btn-outline-success btn-sm btn-block">
                                <i class="fas fa-check"></i> Approved
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('welfare-expenses.index', ['status' => \App\Models\WelfareExpense::STATUS_REJECTED]) }}" 
                               class="btn btn-outline-danger btn-sm btn-block">
                                <i class="fas fa-times"></i> Rejected
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('welfare-expenses.index', ['expense_type' => 'salary']) }}" 
                               class="btn btn-outline-info btn-sm btn-block">
                                Salary
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('welfare-expenses.index', ['expense_type' => 'medical']) }}" 
                               class="btn btn-outline-info btn-sm btn-block">
                                Medical
                            </a>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold mb-3">Monthly Reports</h6>
                    <div class="row">
                        @foreach(range(1, 3) as $month)
                        <div class="col-12 mb-2">
                            <a href="{{ route('welfare-expenses.index', ['month' => now()->subMonths($month)->format('Y-m')]) }}" 
                               class="btn btn-outline-secondary btn-sm btn-block text-left">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                {{ now()->subMonths($month)->format('F Y') }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Status Summary -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Approved</h6>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $summary['approved_count'] / max($summary['approved_count'] + $summary['pending_count'], 1) * 100 }}%">
                            </div>
                        </div>
                        <small class="text-muted">{{ $summary['approved_count'] }} expenses</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Pending</h6>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ $summary['pending_count'] / max($summary['approved_count'] + $summary['pending_count'], 1) * 100 }}%">
                            </div>
                        </div>
                        <small class="text-muted">{{ $summary['pending_count'] }} expenses</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection