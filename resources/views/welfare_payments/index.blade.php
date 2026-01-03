@extends('layouts.app')

@section('title', 'Welfare Summary')
@section('page-title', 'Welfare Payment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Welfare Payment</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0" style="font-size: 1.1rem;">
                            <i class="fas fa-hand-holding-usd mr-2"></i>
                            Welfare Payments Summary - 
                            @if(isset($welfarePayments['year_month']))
                                {{ \Carbon\Carbon::parse(str_replace('!', '', $welfarePayments['year_month']))->format('F Y') }}
                            @else
                                {{ now()->format('F Y') }}
                            @endif
                        </h3>
                        
                        <button type="button" class="btn btn-light btn-sm" onclick="location.reload()" 
                                data-toggle="tooltip" title="Refresh Data" style="font-size: 0.85rem;">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                
                @if(isset($welfarePayments['status']) && $welfarePayments['status'] === 'error')
                    <div class="card-body">
                        <div class="alert alert-danger" style="font-size: 0.9rem;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            {{ $welfarePayments['message'] ?? 'Failed to load welfare payments data.' }}
                        </div>
                        <a href="{{ route('welfare_payments.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-redo"></i> Try Again
                        </a>
                    </div>
                @elseif(isset($welfarePayments['data']) && count($welfarePayments['data']) > 0)
                    <!-- Filters Section -->
                    <div class="card-body bg-light py-2">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="monthFilter" class="small font-weight-bold text-muted mb-1" style="font-size: 0.8rem;">Filter Month</label>
                                    <input type="month" 
                                           id="monthFilter" 
                                           class="form-control form-control-sm" 
                                           value="{{ isset($welfarePayments['year_month']) ? str_replace('!', '', $welfarePayments['year_month']) : now()->format('Y-m') }}"
                                           onchange="filterByMonth(this.value)"
                                           style="font-size: 0.85rem;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="teacherFilter" class="small font-weight-bold text-muted mb-1" style="font-size: 0.8rem;">Filter Teacher</label>
                                    <select id="teacherFilter" class="form-control form-control-sm" onchange="filterByTeacher(this.value)" style="font-size: 0.85rem;">
                                        <option value="">All Teachers</option>
                                        @foreach($welfarePayments['data'] as $payment)
                                            <option value="{{ $payment['teacher_id'] }}">
                                                {{ $payment['teacher_name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="statusFilter" class="small font-weight-bold text-muted mb-1" style="font-size: 0.8rem;">Filter Status</label>
                                    <select id="statusFilter" class="form-control form-control-sm" onchange="filterByStatus(this.value)" style="font-size: 0.85rem;">
                                        <option value="">All Status</option>
                                        <option value="paid">Paid Only</option>
                                        <option value="pending">Pending Only</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table Section -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0" id="welfareTable" style="font-size: 0.85rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Teacher</th>
                                        <th width="60">%</th>
                                        <th width="120" class="text-right">Total Payments</th>
                                        <th width="120" class="text-right">Gross Earning</th>
                                        <th width="100" class="text-right">Advance</th>
                                        <th width="120" class="text-right">Net Payable</th>
                                        <th width="120" class="text-right">Welfare</th>
                                        <th width="120" class="text-right">Balance</th>
                                        <th width="100" class="text-center">Status</th>
                                        <th width="100" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grandTotals = [
                                            'total_payments' => 0,
                                            'gross_earning' => 0,
                                            'advance_deducted' => 0,
                                            'teacher_earning' => 0,
                                            'welfare_amount' => 0,
                                            'remaining_balance' => 0,
                                        ];
                                    @endphp
                                    
                                    @foreach($welfarePayments['data'] as $index => $payment)
                                        @php
                                            // Add to grand totals
                                            $grandTotals['total_payments'] += floatval($payment['total_payments_this_month']);
                                            $grandTotals['gross_earning'] += floatval($payment['gross_earning']);
                                            $grandTotals['advance_deducted'] += floatval($payment['advance_deducted_this_month']);
                                            $grandTotals['teacher_earning'] += floatval($payment['teacher_earning']);
                                            $grandTotals['welfare_amount'] += floatval($payment['welfare_amount']);
                                            $grandTotals['remaining_balance'] += floatval($payment['remaining_balance']);
                                            
                                            // Calculate row colors based on status
                                            $rowClass = '';
                                            $statusClass = '';
                                            if ($payment['teacher_earning'] == 0) {
                                                $rowClass = 'table-secondary';
                                                $statusClass = 'text-muted';
                                            } elseif ($payment['remaining_balance'] < 0) {
                                                $rowClass = 'table-warning';
                                            } elseif ($payment['welfare_paid']) {
                                                $statusClass = 'text-success';
                                            } else {
                                                $statusClass = 'text-primary';
                                            }
                                        @endphp
                                        
                                        <tr class="{{ $rowClass }}">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold {{ $statusClass }}" style="font-size: 0.85rem;">{{ $payment['teacher_name'] }}</span>
                                                    <small class="text-muted" style="font-size: 0.75rem;">ID: {{ $payment['teacher_id'] }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-light" style="font-size: 0.75rem;">{{ $payment['percentage'] }}%</span>
                                            </td>
                                            <td class="text-right font-weight-medium">
                                                {{ number_format($payment['total_payments_this_month'], 2) }}
                                            </td>
                                            <td class="text-right">
                                                <span class="font-weight-bold text-dark">
                                                    {{ number_format($payment['gross_earning'], 2) }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                @if($payment['advance_deducted_this_month'] > 0)
                                                    <span class="text-danger">
                                                        <i class="fas fa-minus-circle mr-1"></i>{{ number_format($payment['advance_deducted_this_month'], 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">0.00</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <span class="font-weight-bold {{ $statusClass }}">
                                                    {{ number_format($payment['teacher_earning'], 2) }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                @if($payment['welfare_amount'] > 0)
                                                    <span class="text-success font-weight-bold">
                                                        <i class="fas fa-arrow-up mr-1"></i>{{ number_format($payment['welfare_amount'], 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">0.00</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($payment['remaining_balance'] > 0)
                                                    <span class="text-info font-weight-bold">
                                                        {{ number_format($payment['remaining_balance'], 2) }}
                                                    </span>
                                                @elseif($payment['remaining_balance'] == 0)
                                                    <span class="text-success">
                                                        <i class="fas fa-check-circle mr-1"></i>0.00
                                                    </span>
                                                @else
                                                    <span class="text-danger font-weight-bold">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ number_format($payment['remaining_balance'], 2) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($payment['welfare_paid'])
                                                    <span class="badge badge-success badge-pill py-1 px-2" style="font-size: 0.75rem;">
                                                        <i class="fas fa-check-circle mr-1"></i> Paid
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning badge-pill py-1 px-2" style="font-size: 0.75rem;">
                                                        <i class="fas fa-clock mr-1"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($payment['teacher_earning'] > 0 && !$payment['welfare_paid'])
                                                    <button type="button" 
                                                            class="btn btn-success btn-sm welfare-pay-btn"
                                                            data-teacher-id="{{ $payment['teacher_id'] }}"
                                                            data-teacher-name="{{ $payment['teacher_name'] }}"
                                                            data-available-balance="{{ $payment['teacher_earning'] }}"
                                                            data-toggle="tooltip"
                                                            title="Pay Welfare"
                                                            style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                                                        <i class="fas fa-hand-holding-usd"></i> Pay
                                                    </button>
                                                @elseif($payment['welfare_amount'] > 0)
                                                    <a href="{{ route('welfare_payments.index') }}?teacher_id={{ $payment['teacher_id'] }}" 
                                                       class="btn btn-info btn-sm"
                                                       data-toggle="tooltip"
                                                       title="View Details"
                                                       style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted" style="font-size: 0.75rem;">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Grand Totals Row -->
                                    <tr class="table-active">
                                        <td colspan="3" class="text-right font-weight-bold" style="font-size: 0.85rem;">GRAND TOTALS:</td>
                                        <td class="text-right font-weight-bold">{{ number_format($grandTotals['total_payments'], 2) }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($grandTotals['gross_earning'], 2) }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($grandTotals['advance_deducted'], 2) }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($grandTotals['teacher_earning'], 2) }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($grandTotals['welfare_amount'], 2) }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($grandTotals['remaining_balance'], 2) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Summary Section -->
                    <div class="card-footer bg-white py-2">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary btn-sm" onclick="showWelfareModal()" style="font-size: 0.85rem;">
                                    <i class="fas fa-plus-circle mr-1"></i> Add Welfare Payment
                                </button>
                            </div>
                        </div>
                    </div>
                    
                @else
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted mb-2" style="font-size: 1rem;">No Welfare Data Available</h5>
                            <p class="text-muted mb-3" style="font-size: 0.9rem;">No welfare payment data found for the selected period.</p>
                            <button type="button" class="btn btn-primary btn-sm" onclick="showWelfareModal()" style="font-size: 0.85rem;">
                                <i class="fas fa-plus-circle mr-1"></i> Add Welfare Payment
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Welfare Payment Modal - Production Optimized -->
<div class="modal fade" id="welfarePaymentModal" tabindex="-1" role="dialog" aria-labelledby="welfarePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 8px; border: 1px solid #dee2e6;">
            <div class="modal-header bg-primary text-white border-0 py-2">
                <div class="d-flex align-items-center w-100">
                    <div class="mr-2">
                        <div class="icon-circle bg-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 50%;">
                            <i class="fas fa-hand-holding-usd" style="color: #3498db; font-size: 0.85rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="modal-title mb-0 text-white" id="welfarePaymentModalLabel" style="font-weight: 600; font-size: 0.9rem;">
                            WELFARE PAYMENT
                        </h6>
                        <p class="mb-0 text-white opacity-90" style="font-size: 0.75rem;">
                            Add new payment record
                        </p>
                    </div>
                    <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close" onclick="closeModal()" style="font-size: 1.2rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            
            <form id="welfarePaymentForm" action="{{ route('welfare_payments.store') }}" method="POST">
                @csrf
                
                <div class="modal-body p-0" style="background: #f8f9fa;">
                    <!-- Success/Error Messages -->
                    <div id="formMessages" class="px-3 pt-3"></div>
                    
                    <!-- Teacher Info Card -->
                    <div class="card mx-3 mb-3 border-0 shadow-sm d-none" id="teacherInfo" style="border-radius: 6px; background: #2c3e50;">
                        <div class="card-body p-2 text-white">
                            <div class="row align-items-center">
                                <div class="col-md-6 border-right border-light border-opacity-25 pr-2">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2 bg-white bg-opacity-20 p-1 rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                            <i class="fas fa-chalkboard-teacher" style="font-size: 0.75rem;"></i>
                                        </div>
                                        <div style="min-width: 0;">
                                            <p class="mb-0 opacity-80" style="font-size: 0.7rem; letter-spacing: 0.3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">TEACHER</p>
                                            <p class="mb-0 font-weight-bold" id="teacherNameDisplay" style="font-size: 0.8rem; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Select a teacher</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pl-2">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2 bg-white bg-opacity-20 p-1 rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                            <i class="fas fa-wallet" style="font-size: 0.75rem;"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 opacity-80" style="font-size: 0.7rem; letter-spacing: 0.3px;">BALANCE</p>
                                            <p class="mb-0 font-weight-bold text-success" id="availableBalanceDisplay" style="font-size: 0.9rem;">0.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden Fields -->
                    <input type="hidden" id="modal_teacher_id" name="teacher_id" value="">
                    <input type="hidden" id="modal_available_balance" value="0">
                    
                    <!-- Form Content -->
                    <div class="px-3 pb-3">
                        <!-- Teacher Selection -->
                        <div class="form-group mb-2">
                            <label for="teacher_id" class="form-label mb-1 d-flex align-items-center" style="color: #495057; font-size: 0.8rem; font-weight: 600;">
                                <i class="fas fa-user-graduate mr-1" style="color: #3498db; font-size: 0.8rem;"></i>
                                Select Teacher
                                <span class="text-danger ml-1">*</span>
                            </label>
                            <select class="form-control select2-custom" id="teacher_id" name="teacher_id" required onchange="updateTeacherInfo()" 
                                    style="height: 36px; border-radius: 4px; border: 1px solid #ced4da; padding: 0.25rem 0.5rem; font-size: 0.85rem;">
                                <option value="">Choose a teacher...</option>
                                @if(isset($welfarePayments['data']))
                                    @foreach($welfarePayments['data'] as $payment)
                                        @if(!$payment['welfare_paid'])
                                            <option value="{{ $payment['teacher_id'] }}" 
                                                    data-name="{{ $payment['teacher_name'] }}"
                                                    data-balance="{{ $payment['teacher_earning'] }}">
                                                {{ $payment['teacher_name'] }} 
                                                <span class="text-muted">- ${{ number_format($payment['teacher_earning'], 2) }}</span>
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="row">
                            <!-- Amount Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="amount" class="form-label mb-1 d-flex align-items-center" style="color: #495057; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-money-bill-wave mr-1" style="color: #3498db; font-size: 0.8rem;"></i>
                                        Amount
                                        <span class="text-danger ml-1">*</span>
                                    </label>
                                    <div class="input-group" style="border-radius: 4px; overflow: hidden; border: 1px solid #ced4da;">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-0 bg-light" style="font-size: 0.85rem; font-weight: 600; color: #495057; padding: 0.25rem 0.5rem;">
                                                $
                                            </span>
                                        </div>
                                        <input type="number" 
                                               class="form-control border-0" 
                                               id="amount" 
                                               name="amount" 
                                               step="0.01" 
                                               min="0.01" 
                                               required
                                               onchange="validateAmount()"
                                               placeholder="0.00"
                                               style="height: 36px; font-size: 0.85rem; font-weight: 500; color: #495057; padding: 0.25rem 0.5rem;">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="form-text text-muted" style="font-size: 0.75rem;">
                                            Max: <span id="maxAmount" class="font-weight-bold" style="color: #3498db;">0.00</span>
                                        </small>
                                        <span class="badge px-2 py-1" id="amountPercentage" style="background: #e9ecef; color: #495057; font-size: 0.7rem; font-weight: 600; border-radius: 10px;">0%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Date -->
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="payment_date" class="form-label mb-1 d-flex align-items-center" style="color: #495057; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-calendar-alt mr-1" style="color: #3498db; font-size: 0.8rem;"></i>
                                        Date
                                        <span class="text-danger ml-1">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="payment_date" 
                                           name="payment_date" 
                                           value="{{ date('Y-m-d') }}" 
                                           required
                                           style="height: 36px; border-radius: 4px; border: 1px solid #ced4da; font-size: 0.85rem; color: #495057; padding: 0.25rem 0.5rem;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Payment Method -->
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="payment_method" class="form-label mb-1 d-flex align-items-center" style="color: #495057; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-credit-card mr-1" style="color: #3498db; font-size: 0.8rem;"></i>
                                        Method
                                        <span class="text-danger ml-1">*</span>
                                    </label>
                                    <select class="form-control" id="payment_method" name="payment_method" required 
                                            style="height: 36px; border-radius: 4px; border: 1px solid #ced4da; font-size: 0.85rem; color: #495057; padding: 0.25rem 0.5rem;">
                                        <option value="salary_deduction">Salary Deduction</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="description" class="form-label mb-1 d-flex align-items-center" style="color: #495057; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-file-alt mr-1" style="color: #3498db; font-size: 0.8rem;"></i>
                                        Description
                                    </label>
                                    <textarea class="form-control" 
                                              id="description" 
                                              name="description" 
                                              rows="2" 
                                              placeholder="Optional notes..."
                                              style="border-radius: 4px; border: 1px solid #ced4da; padding: 0.25rem 0.5rem; font-size: 0.85rem; color: #495057; resize: vertical; min-height: 60px;"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Summary -->
                        <div class="card border-0 bg-primary text-white d-none mt-2" id="paymentSummary" style="border-radius: 6px;">
                            <div class="card-body p-2">
                                <h6 class="text-white mb-1 d-flex align-items-center" style="font-size: 0.8rem; font-weight: 600;">
                                    <i class="fas fa-calculator mr-1"></i>
                                    PAYMENT SUMMARY
                                </h6>
                                <div class="row text-center">
                                    <div class="col-md-4 mb-1">
                                        <div class="p-1 rounded" style="background: rgba(255,255,255,0.1);">
                                            <small class="text-white opacity-90 d-block mb-1" style="font-size: 0.7rem;">AVAILABLE</small>
                                            <div class="font-weight-bold" id="summaryBalance" style="font-size: 0.85rem;">$0.00</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <div class="p-1 rounded" style="background: rgba(255,255,255,0.1);">
                                            <small class="text-white opacity-90 d-block mb-1" style="font-size: 0.7rem;">PAYMENT</small>
                                            <div class="font-weight-bold" id="summaryAmount" style="font-size: 0.85rem;">$0.00</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-1 rounded" style="background: rgba(255,255,255,0.1);">
                                            <small class="text-white opacity-90 d-block mb-1" style="font-size: 0.7rem;">REMAINING</small>
                                            <div class="font-weight-bold" id="summaryRemaining" style="font-size: 0.85rem;">$0.00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-top py-2 px-3" style="background: #f8f9fa; border-color: #dee2e6 !important;">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="closeModal()" 
                            style="border-radius: 4px; font-size: 0.8rem; font-weight: 500; padding: 0.25rem 0.75rem;">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="submitBtn"
                            style="border-radius: 4px; font-size: 0.8rem; font-weight: 500; padding: 0.25rem 0.75rem;">
                        <i class="fas fa-check mr-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />

<script>
    // Make functions available globally
    window.showWelfareModal = function(teacherId = null, teacherName = null, availableBalance = null) {
        // Reset form first
        $('#welfarePaymentForm')[0].reset();
        $('#formMessages').empty();
        $('#teacherInfo').addClass('d-none');
        $('#paymentSummary').addClass('d-none');
        
        if (teacherId && teacherName && availableBalance) {
            // Pre-fill for specific teacher from table button
            $('#teacher_id').val(teacherId).trigger('change');
            $('#modal_teacher_id').val(teacherId);
            $('#modal_available_balance').val(availableBalance);
            
            // Update display
            $('#teacherNameDisplay').text(teacherName);
            $('#availableBalanceDisplay').text(parseFloat(availableBalance).toFixed(2));
            $('#teacherInfo').removeClass('d-none');
            
            // Update amount field max
            $('#maxAmount').text(parseFloat(availableBalance).toFixed(2));
            $('#amount').attr('max', availableBalance);
            
            // Update summary
            updatePaymentSummary();
        } else {
            // Reset dropdown to first option
            $('#teacher_id').val(null).trigger('change');
        }
        
        // Show modal
        $('#welfarePaymentModal').modal('show');
    };
    
    window.closeModal = function() {
        $('#welfarePaymentModal').modal('hide');
    };
    
    window.updateTeacherInfo = function() {
        const select = document.getElementById('teacher_id');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const teacherName = selectedOption.getAttribute('data-name');
            const availableBalance = selectedOption.getAttribute('data-balance');
            
            // Update hidden fields
            $('#modal_teacher_id').val(selectedOption.value);
            $('#modal_available_balance').val(availableBalance);
            
            // Update display
            $('#teacherNameDisplay').text(teacherName);
            $('#availableBalanceDisplay').text(parseFloat(availableBalance).toFixed(2));
            $('#teacherInfo').removeClass('d-none');
            
            // Update amount field max
            $('#maxAmount').text(parseFloat(availableBalance).toFixed(2));
            $('#amount').attr('max', availableBalance);
            
            // Update summary
            updatePaymentSummary();
        } else {
            $('#teacherInfo').addClass('d-none');
            $('#paymentSummary').addClass('d-none');
        }
    };
    
    window.validateAmount = function() {
        const amount = parseFloat($('#amount').val()) || 0;
        const maxAmount = parseFloat($('#modal_available_balance').val()) || 0;
        
        if (amount > maxAmount) {
            $('#amount').addClass('is-invalid');
            $('#submitBtn').prop('disabled', true);
            
            // Show error
            $('#formMessages').html(`
                <div class="alert alert-warning alert-dismissible fade show mb-2" role="alert" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> 
                    Amount cannot exceed available balance of ${maxAmount.toFixed(2)}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="font-size: 1rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
        } else {
            $('#amount').removeClass('is-invalid');
            $('#submitBtn').prop('disabled', false);
            $('#formMessages').empty();
            updatePaymentSummary();
        }
    };
    
    window.updatePaymentSummary = function() {
        const availableBalance = parseFloat($('#modal_available_balance').val()) || 0;
        const amount = parseFloat($('#amount').val()) || 0;
        const remaining = availableBalance - amount;
        
        $('#summaryBalance').text('$' + availableBalance.toFixed(2));
        $('#summaryAmount').text('$' + amount.toFixed(2));
        $('#summaryRemaining').text('$' + remaining.toFixed(2));
        
        if (amount > 0) {
            $('#paymentSummary').removeClass('d-none');
        } else {
            $('#paymentSummary').addClass('d-none');
        }
    };
    
    // Filter functions
    function filterByMonth(month) {
        if (month) {
            window.location.href = "{{ route('welfare_payments.index') }}?month=" + month;
        }
    }
    
    function filterByTeacher(teacherId) {
        if (teacherId) {
            window.location.href = "{{ route('welfare_payments.index') }}?teacher_id=" + teacherId;
        } else {
            window.location.href = "{{ route('welfare_payments.index') }}";
        }
    }
    
    function filterByStatus(status) {
        if (status) {
            window.location.href = "{{ route('welfare_payments.index') }}?status=" + status;
        } else {
            window.location.href = "{{ route('welfare_payments.index') }}";
        }
    }

    $(document).ready(function() {
        console.log('Initializing Welfare Payment System...');
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize Select2 for teacher dropdown
        $('#teacher_id').select2({
            theme: 'bootstrap',
            placeholder: 'Choose a teacher...',
            allowClear: true,
            dropdownParent: $('#welfarePaymentModal'),
            width: '100%',
            dropdownCssClass: 'select2-dropdown-sm',
            containerCssClass: 'select2-container-sm'
        });
        
        // Handle table welfare payment button clicks
        $(document).on('click', '.welfare-pay-btn', function() {
            const teacherId = $(this).data('teacher-id');
            const teacherName = $(this).data('teacher-name');
            const availableBalance = $(this).data('available-balance');
            
            console.log('Opening modal for:', teacherName, 'Balance:', availableBalance);
            showWelfareModal(teacherId, teacherName, availableBalance);
        });
        
        // Form submission handler
        $('#welfarePaymentForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = $('#submitBtn');
            const originalText = submitBtn.html();
            
            // Disable submit button and show loading
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Show success message
                    $('#formMessages').html(`
                        <div class="alert alert-success alert-dismissible fade show mb-2" role="alert" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                            <i class="fas fa-check-circle mr-2"></i> 
                            Welfare payment created successfully!
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="font-size: 1rem;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                    
                    // Reload page after 2 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    let errors = '';
                    if (xhr.status === 422) {
                        // Validation errors
                        const responseErrors = xhr.responseJSON.errors;
                        $.each(responseErrors, function(key, value) {
                            errors += `<div class="text-danger" style="font-size: 0.85rem;">${value[0]}</div>`;
                        });
                    } else {
                        errors = '<div class="text-danger" style="font-size: 0.85rem;">Failed to create welfare payment. Please try again.</div>';
                    }
                    
                    $('#formMessages').html(`
                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                            <i class="fas fa-exclamation-triangle mr-2"></i> 
                            ${errors}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="font-size: 1rem;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                    
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
        
        // Reset form when modal is hidden
        $('#welfarePaymentModal').on('hidden.bs.modal', function () {
            $('#welfarePaymentForm')[0].reset();
            $('#formMessages').empty();
            $('#teacherInfo').addClass('d-none');
            $('#paymentSummary').addClass('d-none');
            $('#teacher_id').val(null).trigger('change');
            $('#submitBtn').prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Submit');
        });
        
        // Auto-update summary when amount changes
        $('#amount').on('input', function() {
            updatePaymentSummary();
            validateAmount();
        });
        
        // Close modal when clicking the close button in header
        $('.modal-header .close').on('click', function() {
            closeModal();
        });
        
        // Percentage indicator for amount
        $('#amount').on('input', function() {
            const amount = parseFloat($(this).val()) || 0;
            const maxAmount = parseFloat($('#modal_available_balance').val()) || 0;
            const percentage = maxAmount > 0 ? Math.round((amount / maxAmount) * 100) : 0;
            
            $('#amountPercentage').text(percentage + '%');
            
            // Change badge color based on percentage
            const badge = $('#amountPercentage');
            badge.removeClass('badge-success badge-warning badge-danger');
            
            if (percentage > 100) {
                badge.addClass('badge-danger');
            } else if (percentage > 80) {
                badge.addClass('badge-warning');
            } else if (percentage > 0) {
                badge.addClass('badge-success');
            } else {
                badge.removeClass('badge-success badge-warning badge-danger');
                badge.css({
                    'background': '#e9ecef',
                    'color': '#495057'
                });
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Production Optimized Styles */
    body {
        font-size: 0.9rem;
    }
    
    /* Table Optimizations */
    .table {
        font-size: 0.85rem;
    }
    
    .table td, .table th {
        padding: 0.5rem;
    }
    
    .table-sm td, .table-sm th {
        padding: 0.3rem;
    }
    
    /* Modal Optimizations */
    .modal-md {
        max-width: 450px;
    }
    
    .modal-content {
        font-size: 0.85rem;
    }
    
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    /* Select2 Optimizations */
    .select2-container--bootstrap .select2-selection {
        font-size: 0.85rem !important;
        height: 36px !important;
        padding: 0.25rem 0.5rem !important;
    }
    
    .select2-container--bootstrap .select2-selection__rendered {
        line-height: 1.4 !important;
        font-size: 0.85rem !important;
    }
    
    .select2-container--bootstrap .select2-selection__arrow {
        height: 34px !important;
    }
    
    .select2-dropdown {
        font-size: 0.85rem !important;
    }
    
    /* Form Control Optimizations */
    .form-control, .form-control-sm {
        font-size: 0.85rem !important;
        height: 36px;
    }
    
    textarea.form-control {
        min-height: 60px;
    }
    
    /* Button Optimizations */
    .btn-sm {
        font-size: 0.8rem !important;
        padding: 0.25rem 0.5rem !important;
    }
    
    /* Badge Optimizations */
    .badge {
        font-size: 0.7rem !important;
        padding: 0.2rem 0.4rem !important;
    }
    
    .badge-pill {
        padding: 0.2rem 0.6rem !important;
    }
    
    /* Card Optimizations */
    .card {
        font-size: 0.85rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    /* Alert Optimizations */
    .alert {
        font-size: 0.85rem !important;
        padding: 0.5rem 1rem !important;
        margin-bottom: 0.75rem !important;
    }
    
    /* Tooltip Optimizations */
    .tooltip {
        font-size: 0.8rem;
    }
    
    /* Animation Optimizations */
    .fade {
        transition: opacity 0.15s linear;
    }
    
    /* Performance Optimizations */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Scrollbar Optimizations */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Responsive Optimizations */
    @media (max-width: 576px) {
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .modal-content {
            border-radius: 6px;
        }
        
        .modal-body {
            padding: 0.75rem;
        }
        
        .row > .col-md-6 {
            margin-bottom: 0.75rem;
        }
        
        .btn-sm {
            padding: 0.2rem 0.4rem !important;
            font-size: 0.75rem !important;
        }
    }
    
    /* Focus State Optimizations */
    .form-control:focus, 
    .select2-container--bootstrap .select2-selection:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    
    /* Error State */
    .is-invalid {
        border-color: #e74c3c !important;
    }
    
    .is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25) !important;
    }
</style>
@endpush