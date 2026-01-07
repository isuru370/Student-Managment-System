@extends('layouts.app')

@section('title', 'Teacher Ledger')
@section('page-title', 'Teacher Ledger Summary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Teacher Ledger Summary</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Month Selector Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1 text-primary">
                                <i class="fas fa-book me-2"></i>Monthly Ledger Summary
                            </h3>
                            <p class="text-muted mb-0">Generate detailed ledger summary for any month</p>
                        </div>
                        <div class="col-md-4">
                            <form id="ledgerForm" method="GET" action="{{ route('teacher_ledger_summary.index') }}">
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </span>
                                    <input type="month" 
                                           class="form-control border-start-0 ps-0" 
                                           id="monthSelect" 
                                           name="month" 
                                           value="{{ request('month') ?? date('Y-m') }}"
                                           required
                                           style="border-color: #dee2e6;">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-chart-line me-2"></i>Generate
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($data) && $data['status'] === 'success')
        @php $ledgerData = $data['data']; @endphp
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Opening Balance
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    Rs. {{ number_format($ledgerData['opening_balance'], 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                    Total Receipts
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    Rs. {{ number_format($ledgerData['summary']['total_receipts'], 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-download fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Total Payments
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    Rs. {{ number_format($ledgerData['summary']['total_payments'], 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-upload fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                    Closing Balance
                                </div>
                                <div class="h5 mb-0 fw-bold text-gray-800">
                                    Rs. {{ $ledgerData['summary']['closing_balance'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-balance-scale fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h4 class="text-white mb-1">
                                    <i class="far fa-calendar-alt me-2"></i>{{ $ledgerData['period']['month'] }}
                                </h4>
                                <p class="text-white-50 mb-0 text-dark">
                                    Period: {{ $ledgerData['period']['start_date'] }} to {{ $ledgerData['period']['end_date'] }}
                                </p>
                            </div>
                            <div class="col-lg-4 text-lg-end">
                                <span class="badge bg-light text-primary px-3 py-2 fs-6">
                                    <i class="fas fa-info-circle me-2"></i>Monthly Ledger
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Table Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2 text-primary"></i>Ledger Details
                            </h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="printLedger()">
                                    <i class="fas fa-print me-1"></i>Print
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel me-1"></i>Export
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="collapse" 
                                        data-bs-target="#filterSection">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filter Section -->
                    <div class="collapse" id="filterSection">
                        <div class="card-body border-bottom bg-light">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Transaction Type</label>
                                    <select class="form-select form-select-sm" id="filterType">
                                        <option value="all">All Transactions</option>
                                        <option value="receipt">Receipts Only</option>
                                        <option value="payment">Payments Only</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Date Range</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           placeholder="Search by description..." id="searchDescription">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Quick Actions</label>
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                                            <i class="fas fa-redo me-1"></i>Clear Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="ledgerTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 15%">
                                            <i class="far fa-calendar me-1"></i>Date
                                        </th>
                                        <th style="width: 45%">
                                            <i class="far fa-file-alt me-1"></i>Description
                                        </th>
                                        <th class="text-end" style="width: 10%">
                                            <i class="fas fa-download me-1 text-success"></i>Receipt
                                        </th>
                                        <th class="text-end" style="width: 10%">
                                            <i class="fas fa-upload me-1 text-danger"></i>Payment
                                        </th>
                                        <th class="text-end pe-4" style="width: 15%">
                                            <i class="fas fa-balance-scale me-1 text-primary"></i>Balance
                                        </th>
                                        <th style="width: 5%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Opening Balance Row -->
                                    <tr class="table-info">
                                        <td class="ps-4 fw-bold">
                                            {{ date('d M Y', strtotime($ledgerData['period']['start_date'])) }}
                                        </td>
                                        <td class="fw-bold">
                                            <i class="fas fa-door-open me-2"></i>Opening Balance
                                        </td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end pe-4 fw-bold">
                                            Rs. {{ number_format($ledgerData['opening_balance'], 2) }}
                                        </td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    
                                    <!-- Ledger Entries -->
                                    @forelse($ledgerData['ledger'] as $entry)
                                        <tr class="ledger-entry">
                                            <td class="ps-4">
                                                <span class="badge bg-light text-dark">
                                                    {{ $entry['date'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs">
                                                            <div class="avatar-title bg-soft-primary rounded-circle fs-16">
                                                                <i class="fas fa-receipt"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-0 fs-14">{{ $entry['description'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                @if($entry['receipt'])
                                                    <span class="badge bg-success bg-opacity-10 text-success fs-12">
                                                        <i class="fas fa-plus-circle me-1"></i>
                                                        Rs. {{ $entry['receipt'] }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($entry['payment'])
                                                    <span class="badge bg-danger bg-opacity-10 text-danger fs-12">
                                                        <i class="fas fa-minus-circle me-1"></i>
                                                        Rs. {{ $entry['payment'] }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4 fw-medium">
                                                Rs. {{ $entry['balance'] }}
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-soft-primary"
                                                        data-bs-toggle="tooltip" title="View Details"
                                                        onclick="viewEntry('{{ $entry['date'] }}', '{{ $entry['description'] }}')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No ledger entries found</h5>
                                                    <p class="text-muted mb-0">No transactions recorded for this period</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    
                                    <!-- Closing Summary Row -->
                                    <tr class="table-warning">
                                        <td colspan="2" class="ps-4 fw-bold">
                                            <i class="fas fa-chart-bar me-2"></i>Period Summary
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            Rs. {{ number_format($ledgerData['summary']['total_receipts'], 2) }}
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            Rs. {{ number_format($ledgerData['summary']['total_payments'], 2) }}
                                        </td>
                                        <td class="text-end pe-4 fw-bold">
                                            Rs. {{ $ledgerData['summary']['closing_balance'] }}
                                        </td>
                                        <td class="text-center">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="text-muted small">
                                    <i class="fas fa-clock me-1"></i>
                                    Generated on: {{ date('Y-m-d H:i:s') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="window.print()">
                                            <i class="fas fa-print me-1"></i>Print Report
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                onclick="downloadPDF()">
                                            <i class="fas fa-file-pdf me-1"></i>Save as PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary and Chart Section -->
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2 text-primary"></i>Financial Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="ps-0">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title bg-soft-primary rounded-circle">
                                                            <i class="fas fa-wallet text-primary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0 fs-14">Opening Balance</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0 fw-bold fs-5">
                                            Rs. {{ number_format($ledgerData['opening_balance'], 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title bg-soft-success rounded-circle">
                                                            <i class="fas fa-download text-success"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0 fs-14">Total Receipts</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0 fw-bold fs-5 text-success">
                                            + Rs. {{ number_format($ledgerData['summary']['total_receipts'], 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title bg-soft-danger rounded-circle">
                                                            <i class="fas fa-upload text-danger"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0 fs-14">Total Payments</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0 fw-bold fs-5 text-danger">
                                            - Rs. {{ number_format($ledgerData['summary']['total_payments'], 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 border-top">
                                            <div class="d-flex align-items-center pt-3">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title bg-soft-info rounded-circle">
                                                            <i class="fas fa-exchange-alt text-info"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0 fs-14">Net Change</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0 border-top pt-3">
                                            @php
                                                $netChange = $ledgerData['summary']['net_change'];
                                                $textClass = $netChange >= 0 ? 'text-success' : 'text-danger';
                                            @endphp
                                            <span class="fw-bold fs-5 {{ $textClass }}">
                                                {{ $netChange >= 0 ? '+' : '' }}Rs. {{ number_format($netChange, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="ps-0">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title bg-soft-warning rounded-circle">
                                                            <i class="fas fa-balance-scale text-warning"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="mb-0">Closing Balance</h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0">
                                            <h4 class="mb-0 text-warning">
                                                Rs. {{ $ledgerData['summary']['closing_balance'] }}
                                            </h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>Visual Analytics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 bg-gradient-success bg-opacity-10">
                                    <div class="card-body text-center py-4">
                                        <h3 class="text-success mb-2">
                                            <i class="fas fa-download"></i>
                                        </h3>
                                        <h4 class="text-success mb-1">
                                            Rs. {{ number_format($ledgerData['summary']['total_receipts'], 2) }}
                                        </h4>
                                        <p class="text-muted mb-0">Total Receipts</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-gradient-danger bg-opacity-10">
                                    <div class="card-body text-center py-4">
                                        <h3 class="text-danger mb-2">
                                            <i class="fas fa-upload"></i>
                                        </h3>
                                        <h4 class="text-danger mb-1">
                                            Rs. {{ number_format($ledgerData['summary']['total_payments'], 2) }}
                                        </h4>
                                        <p class="text-muted mb-0">Total Payments</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="chart-container">
                            <canvas id="ledgerChart" height="250"></canvas>
                        </div>
                        
                        <div class="mt-4">
                            <div class="progress" style="height: 10px;">
                                @php
                                    $total = $ledgerData['summary']['total_receipts'] + $ledgerData['summary']['total_payments'];
                                    $receiptPercent = $total > 0 ? ($ledgerData['summary']['total_receipts'] / $total * 100) : 0;
                                    $paymentPercent = $total > 0 ? ($ledgerData['summary']['total_payments'] / $total * 100) : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $receiptPercent }}%" 
                                     aria-valuenow="{{ $receiptPercent }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ round($receiptPercent, 1) }}%
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ $paymentPercent }}%" 
                                     aria-valuenow="{{ $paymentPercent }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ round($paymentPercent, 1) }}%
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <small class="text-success">
                                        <i class="fas fa-circle me-1"></i>Receipts: {{ round($receiptPercent, 1) }}%
                                    </small>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-danger">
                                        <i class="fas fa-circle me-1"></i>Payments: {{ round($paymentPercent, 1) }}%
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2 text-primary"></i>Monthly Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h2 class="text-primary mb-1">{{ count($ledgerData['ledger']) }}</h2>
                                    <p class="text-muted mb-0">Total Transactions</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h2 class="text-success mb-1">
                                        {{ $ledgerData['ledger']->where('receipt', '!=', '')->count() }}
                                    </h2>
                                    <p class="text-muted mb-0">Receipt Entries</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h2 class="text-danger mb-1">
                                        {{ $ledgerData['ledger']->where('payment', '!=', '')->count() }}
                                    </h2>
                                    <p class="text-muted mb-0">Payment Entries</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <h2 class="text-warning mb-1">
                                        {{ $ledgerData['summary']['net_change'] >= 0 ? 'Profit' : 'Loss' }}
                                    </h2>
                                    <p class="text-muted mb-0">Financial Status</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($data) && $data['status'] === 'error')
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="alert-heading mb-1">Error!</h5>
                            <p class="mb-0">{{ $data['message'] }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Auto submit form on month change
            document.getElementById('monthSelect').addEventListener('change', function() {
                if(this.value) {
                    document.getElementById('ledgerForm').submit();
                }
            });
            
            // Filter functionality
            document.getElementById('filterType').addEventListener('change', filterTable);
            document.getElementById('searchDescription').addEventListener('keyup', filterTable);
            
            @if(isset($data) && $data['status'] === 'success')
                initializeChart();
            @endif
        });
        
        function filterTable() {
            const typeFilter = document.getElementById('filterType').value;
            const searchFilter = document.getElementById('searchDescription').value.toLowerCase();
            const rows = document.querySelectorAll('.ledger-entry');
            
            rows.forEach(row => {
                const receipt = row.querySelector('.text-success') !== null;
                const payment = row.querySelector('.text-danger') !== null;
                const description = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                let showRow = true;
                
                // Apply type filter
                if (typeFilter === 'receipt' && !receipt) showRow = false;
                if (typeFilter === 'payment' && !payment) showRow = false;
                
                // Apply search filter
                if (searchFilter && !description.includes(searchFilter)) showRow = false;
                
                row.style.display = showRow ? '' : 'none';
            });
        }
        
        function clearFilters() {
            document.getElementById('filterType').value = 'all';
            document.getElementById('searchDescription').value = '';
            filterTable();
        }
        
        function initializeChart() {
            const ctx = document.getElementById('ledgerChart').getContext('2d');
            
            // Create gradient for chart
            const gradientReceipt = ctx.createLinearGradient(0, 0, 0, 400);
            gradientReceipt.addColorStop(0, 'rgba(40, 167, 69, 0.8)');
            gradientReceipt.addColorStop(1, 'rgba(40, 167, 69, 0.1)');
            
            const gradientPayment = ctx.createLinearGradient(0, 0, 0, 400);
            gradientPayment.addColorStop(0, 'rgba(220, 53, 69, 0.8)');
            gradientPayment.addColorStop(1, 'rgba(220, 53, 69, 0.1)');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Receipts', 'Payments'],
                    datasets: [{
                        label: 'Amount (Rs.)',
                        data: [
                            {{ $ledgerData['summary']['total_receipts'] ?? 0 }},
                            {{ $ledgerData['summary']['total_payments'] ?? 0 }}
                        ],
                        backgroundColor: [
                            gradientReceipt,
                            gradientPayment
                        ],
                        borderColor: [
                            'rgb(40, 167, 69)',
                            'rgb(220, 53, 69)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 14
                            },
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return `Amount: Rs. ${context.raw.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rs. ' + value.toLocaleString('en-US', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    });
                                },
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function printLedger() {
            const originalContents = document.body.innerHTML;
            const printContents = document.getElementById('ledgerTable').outerHTML;
            
            document.body.innerHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Teacher Ledger - {{ $ledgerData["period"]["month"] ?? "" }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                        th { background-color: #f8f9fa; font-weight: bold; }
                        .text-end { text-align: right; }
                        .text-center { text-align: center; }
                        .text-success { color: #28a745; }
                        .text-danger { color: #dc3545; }
                        .table-info { background-color: #d1ecf1; }
                        .table-warning { background-color: #fff3cd; }
                        h2 { color: #2c3e50; margin-bottom: 10px; }
                        .print-header { 
                            border-bottom: 2px solid #3498db; 
                            padding-bottom: 10px; 
                            margin-bottom: 20px;
                        }
                        @media print {
                            @page { size: landscape; margin: 0.5cm; }
                            body { padding: 10px; }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h2>Teacher Ledger Summary - {{ $ledgerData["period"]["month"] ?? "" }}</h2>
                        <p><strong>Period:</strong> {{ $ledgerData["period"]["start_date"] ?? "" }} to {{ $ledgerData["period"]["end_date"] ?? "" }}</p>
                        <p><strong>Generated:</strong> {{ date("Y-m-d H:i:s") }}</p>
                    </div>
                    ${printContents}
                </body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
        
        function exportToExcel() {
            const table = document.getElementById('ledgerTable');
            const html = table.outerHTML;
            const url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            const link = document.createElement('a');
            link.download = 'teacher-ledger-{{ $ledgerData["period"]["month"] ?? "report" }}.xls';
            link.href = url;
            link.click();
        }
        
        function downloadPDF() {
            // For PDF generation, you might want to use a library like jsPDF
            // or implement a server-side solution
            alert('PDF export would be implemented with jsPDF or server-side solution');
        }
        
        function viewEntry(date, description) {
            const modal = new bootstrap.Modal(document.getElementById('entryModal'));
            document.getElementById('modalTitle').textContent = `Entry Details - ${date}`;
            document.getElementById('modalBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Date</label>
                            <p class="mb-0 fw-bold">${date}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Type</label>
                            <p class="mb-0">
                                <span class="badge bg-info">Transaction</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">Description</label>
                    <p class="mb-0">${description}</p>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Detailed transaction information would appear here
                </div>
            `;
            modal.show();
        }
    </script>
@endsection

@section('styles')
    <style>
        .card {
            border-radius: 12px;
            transition: transform 0.2s ease-in-out;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }
        
        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }
        
        .border-left-danger {
            border-left: 4px solid #e74a3b !important;
        }
        
        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }
        
        .avatar-xs {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bg-soft-primary {
            background-color: rgba(78, 115, 223, 0.1) !important;
        }
        
        .bg-soft-success {
            background-color: rgba(28, 200, 138, 0.1) !important;
        }
        
        .bg-soft-danger {
            background-color: rgba(231, 74, 59, 0.1) !important;
        }
        
        .bg-soft-warning {
            background-color: rgba(246, 194, 62, 0.1) !important;
        }
        
        .bg-soft-info {
            background-color: rgba(54, 185, 204, 0.1) !important;
        }
        
        .btn-soft-primary {
            background-color: rgba(78, 115, 223, 0.1);
            border-color: rgba(78, 115, 223, 0.2);
            color: #4e73df;
        }
        
        .btn-soft-primary:hover {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(45deg, #4e73df, #224abe);
        }
        
        .bg-gradient-success {
            background: linear-gradient(45deg, #1cc88a, #13855c);
        }
        
        .bg-gradient-danger {
            background: linear-gradient(45deg, #e74a3b, #be2617);
        }
        
        .progress {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-bar {
            border-radius: 10px;
        }
        
        .input-group-text {
            border-radius: 8px 0 0 8px !important;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .badge {
            padding: 0.5em 1em;
            border-radius: 20px;
        }
        
        .fs-12 {
            font-size: 12px !important;
        }
        
        .fs-14 {
            font-size: 14px !important;
        }
        
        .fs-16 {
            font-size: 16px !important;
        }
    </style>
@endsection