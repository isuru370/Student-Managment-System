@extends('layouts.app')

@section('title', 'Welfare Expenses Statistics')
@section('page-title', 'Welfare Expenses Statistics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('welfare-expenses.index') }}">Welfare Expenses</a></li>
    <li class="breadcrumb-item active">Statistics</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Overall Statistics -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Overall Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Expenses</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Rs. {{ number_format($statistics['overall']->grand_total ?? 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Average Expense</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Rs. {{ number_format($statistics['overall']->average_amount ?? 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Records</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $statistics['overall']->total_count ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-database fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pending Approvals</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $statistics['by_status'][\App\Models\WelfareExpense::STATUS_PENDING]->count ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Status Breakdown -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_status'] as $status => $data)
                                <tr>
                                    <td>
                                        @if($status == \App\Models\WelfareExpense::STATUS_APPROVED_PAID)
                                            <span class="badge badge-success">Approved / Paid</span>
                                        @elseif($status == \App\Models\WelfareExpense::STATUS_PENDING)
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($status == \App\Models\WelfareExpense::STATUS_REJECTED)
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($status == \App\Models\WelfareExpense::STATUS_CANCELLED)
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>{{ $data->count ?? 0 }}</td>
                                    <td>Rs. {{ number_format($data->total_amount ?? 0, 2) }}</td>
                                    <td>
                                        @if($statistics['overall']->total_count > 0)
                                            {{ number_format(($data->count / $statistics['overall']->total_count) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Expense Type Breakdown -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Expense Type Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Expense Type</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Average</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_type'] as $type => $data)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                                    <td>{{ $data->count ?? 0 }}</td>
                                    <td>Rs. {{ number_format($data->total_amount ?? 0, 2) }}</td>
                                    <td>Rs. {{ number_format(($data->total_amount / max($data->count, 1)), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Monthly Trend -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Trend (Last 12 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Month</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Average per Expense</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['monthly_trend'] as $trend)
                                <tr>
                                    <td>{{ \Carbon\Carbon::create($trend->year, $trend->month)->format('M Y') }}</td>
                                    <td>{{ $trend->count }}</td>
                                    <td>Rs. {{ number_format($trend->total_amount, 2) }}</td>
                                    <td>Rs. {{ number_format($trend->total_amount / max($trend->count, 1), 2) }}</td>
                                    <td>
                                        @if($loop->index > 0)
                                            @php
                                                $prev = $statistics['monthly_trend'][$loop->index - 1];
                                                $change = $prev->total_amount > 0 
                                                    ? (($trend->total_amount - $prev->total_amount) / $prev->total_amount) * 100 
                                                    : 0;
                                            @endphp
                                            @if($change > 0)
                                                <span class="text-success">↑ {{ number_format($change, 1) }}%</span>
                                            @elseif($change < 0)
                                                <span class="text-danger">↓ {{ number_format(abs($change), 1) }}%</span>
                                            @else
                                                <span class="text-muted">→ 0%</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <a href="{{ route('welfare-expenses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Expenses
                    </a>
                    <a href="{{ route('welfare-expenses.export', array_merge(request()->all(), ['report' => 'statistics'])) }}" 
                       class="btn btn-success">
                        <i class="fas fa-file-export"></i> Export Statistics
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .card {
            border: 1px solid #000 !important;
        }
        .btn {
            display: none !important;
        }
    }
</style>
@endpush