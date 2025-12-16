@extends('layouts.app')

@section('title', 'Ledger Summary')
@section('page-title', 'Ledger Summary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('institute_payment.index') }}">Institute Income</a></li>
    <li class="breadcrumb-item active">Ledger</li>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Control Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-book me-2"></i>Ledger Summary
                        </h4>
                        <div class="d-flex align-items-center">
                            <!-- Month Selector -->
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </span>
                                <input type="month" 
                                       id="monthSelector" 
                                       class="form-control border-start-0"
                                       value="{{ date('Y-m') }}"
                                       max="{{ date('Y-m') }}">
                                <button class="btn btn-light border" onclick="loadCurrentMonth()" title="Current Month">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            
                            <!-- Actions -->
                            <div class="btn-group ms-3">
                                <button class="btn btn-light btn-sm" onclick="printLedger()" title="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-light btn-sm" onclick="exportToExcel()" title="Export to Excel">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                                <button class="btn btn-light btn-sm" onclick="refreshData()" title="Refresh">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Summary Cards -->
                <div class="card-body bg-light">
                    <div class="row" id="summaryCards">
                        <!-- Opening Balance -->
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-start-4 border-start-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-muted mb-2">Opening Balance</h6>
                                            <h3 class="mb-0 text-primary" id="openingBalance">0.00</h3>
                                            <small class="text-muted" id="openingDate"></small>
                                        </div>
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-wallet text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Receipts -->
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-start-4 border-start-success h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-muted mb-2">Total Receipts</h6>
                                            <h3 class="mb-0 text-success" id="totalReceipts">0.00</h3>
                                            <small class="text-muted" id="receiptCount">0 transactions</small>
                                        </div>
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-hand-holding-usd text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Payments -->
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-start-4 border-start-danger h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-muted mb-2">Total Payments</h6>
                                            <h3 class="mb-0 text-danger" id="totalPayments">0.00</h3>
                                            <small class="text-muted" id="paymentCount">0 transactions</small>
                                        </div>
                                        <div class="icon-circle bg-danger">
                                            <i class="fas fa-money-check-alt text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Closing Balance -->
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-start-4 border-start-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="text-muted mb-2">Closing Balance</h6>
                                            <h3 class="mb-0 text-warning" id="closingBalance">0.00</h3>
                                            <small class="text-muted" id="closingDate"></small>
                                        </div>
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-balance-scale text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Period Info -->
                    <div class="alert alert-info mb-0 mt-3 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-calendar me-2"></i>
                                <span id="periodInfo">Month: {{ date('F Y') }}</span>
                            </div>
                            <div class="text-end">
                                <small id="transactionCount">Loading transactions...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ledger Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 ledger-table" id="ledgerTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="12%" class="text-center">Date</th>
                                    <th width="38%">Description</th>
                                    <th width="15%" class="text-end">Receipts</th>
                                    <th width="15%" class="text-end">Payments</th>
                                    <th width="20%" class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody id="ledgerBody">
                                <!-- Loading placeholder -->
                                <tr id="loadingRow">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Loading ledger data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="reportInfo">Report generated: --</span>
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-sm btn-outline-primary" onclick="scrollToTop()">
                                <i class="fas fa-arrow-up me-1"></i>Back to Top
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card.border-start-4 {
        border-left-width: 4px !important;
    }
    
    .ledger-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-bottom: 2px solid #dee2e6;
    }
    
    .ledger-table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    /* Color coding for transaction types */
    .income-row {
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    .payment-row {
        background-color: rgba(220, 53, 69, 0.05);
    }
    
    .opening-balance-row {
        background-color: rgba(0, 123, 255, 0.05);
        font-weight: 600;
    }
    
    .closing-balance-row {
        background-color: rgba(255, 193, 7, 0.05);
        font-weight: 600;
    }
    
    .total-row {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: 2px solid #dee2e6;
    }
    
    /* Print styles */
    @media print {
        .card-header, .card-footer, .btn-group, .input-group, .alert {
            display: none !important;
        }
        
        .ledger-table {
            font-size: 12px;
        }
        
        .card {
            border: none !important;
        }
        
        .table-bordered {
            border: 1px solid #000 !important;
        }
    }
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load current month data on page load
    const currentMonth = document.getElementById('monthSelector').value;
    loadLedgerData(currentMonth);
    
    // Add change event listener to month selector
    document.getElementById('monthSelector').addEventListener('change', function() {
        loadLedgerData(this.value);
    });
});

function loadLedgerData(yearMonth) {
    // Show loading state
    document.getElementById('ledgerBody').innerHTML = `
        <tr id="loadingRow">
            <td colspan="5" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading ledger data for ${yearMonth}...</p>
            </td>
        </tr>
    `;
    
    // Reset summary cards
    resetSummaryCards();
    
    // API endpoint
    const apiUrl = `/api/ledger/monthly/${yearMonth}`;
    
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                updateUIWithData(data.data);
            } else {
                showError('Failed to load ledger data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to load ledger data. Please try again.');
        });
}

function loadCurrentMonth() {
    const currentMonth = '{{ date("Y-m") }}';
    document.getElementById('monthSelector').value = currentMonth;
    loadLedgerData(currentMonth);
}

function refreshData() {
    const currentMonth = document.getElementById('monthSelector').value;
    loadLedgerData(currentMonth);
}

function updateUIWithData(ledgerData) {
    // Update period info
    document.getElementById('periodInfo').textContent = 
        `Month: ${ledgerData.period.month} (${ledgerData.period.start_date} to ${ledgerData.period.end_date})`;
    
    // Update summary cards
    document.getElementById('openingBalance').textContent = 
        formatCurrency(ledgerData.opening_balance);
    document.getElementById('openingDate').textContent = 
        `As of ${formatDate(ledgerData.period.start_date)}`;
    
    document.getElementById('totalReceipts').textContent = 
        formatCurrency(ledgerData.summary.total_receipts);
    
    document.getElementById('totalPayments').textContent = 
        formatCurrency(ledgerData.summary.total_payments);
    
    document.getElementById('closingBalance').textContent = 
        ledgerData.summary.closing_balance;
    document.getElementById('closingDate').textContent = 
        `As of ${formatDate(ledgerData.period.end_date)}`;
    
    // Update transaction counts
    const receiptCount = ledgerData.ledger.filter(item => item.receipt).length;
    const paymentCount = ledgerData.ledger.filter(item => item.payment).length;
    document.getElementById('receiptCount').textContent = `${receiptCount} transactions`;
    document.getElementById('paymentCount').textContent = `${paymentCount} transactions`;
    document.getElementById('transactionCount').textContent = 
        `${ledgerData.ledger.length} transactions`;
    
    // Update report info
    document.getElementById('reportInfo').textContent = 
        `Report generated: ${new Date().toLocaleString()}`;
    
    // Build ledger table
    const ledgerBody = document.getElementById('ledgerBody');
    let tableHTML = '';
    
    // Add opening balance row
    tableHTML += `
        <tr class="opening-balance-row">
            <td class="text-center">${formatDate(ledgerData.period.start_date)}</td>
            <td><strong>Balance brought forward</strong></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"><strong>${formatCurrency(ledgerData.opening_balance)}</strong></td>
        </tr>
    `;
    
    // Group transactions by date
    const groupedByDate = {};
    ledgerData.ledger.forEach(transaction => {
        if (!groupedByDate[transaction.date]) {
            groupedByDate[transaction.date] = [];
        }
        groupedByDate[transaction.date].push(transaction);
    });
    
    // Add transaction rows
    Object.keys(groupedByDate).forEach(date => {
        const dayTransactions = groupedByDate[date];
        dayTransactions.forEach((transaction, index) => {
            const rowClass = transaction.receipt ? 'income-row' : 'payment-row';
            const isFirstOfDay = index === 0;
            
            tableHTML += `
                <tr class="${rowClass}">
                    ${isFirstOfDay ? `<td class="text-center" rowspan="${dayTransactions.length}">${transaction.date}</td>` : ''}
                    <td>${transaction.description}</td>
                    <td class="text-end">${transaction.receipt || ''}</td>
                    <td class="text-end">${transaction.payment || ''}</td>
                    <td class="text-end"><strong>${transaction.balance}</strong></td>
                </tr>
            `;
        });
    });
    
    // Add totals row
    tableHTML += `
        <tr class="total-row">
            <td colspan="2" class="text-end"><strong>Totals:</strong></td>
            <td class="text-end"><strong>${formatCurrency(ledgerData.summary.total_receipts)}</strong></td>
            <td class="text-end"><strong>${formatCurrency(ledgerData.summary.total_payments)}</strong></td>
            <td class="text-end"></td>
        </tr>
    `;
    
    // Add closing balance row
    tableHTML += `
        <tr class="closing-balance-row">
            <td class="text-center">${formatDate(ledgerData.period.end_date)}</td>
            <td><strong>Closing Balance</strong></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"><strong>${ledgerData.summary.closing_balance}</strong></td>
        </tr>
    `;
    
    ledgerBody.innerHTML = tableHTML;
}

function resetSummaryCards() {
    document.getElementById('openingBalance').textContent = '0.00';
    document.getElementById('totalReceipts').textContent = '0.00';
    document.getElementById('totalPayments').textContent = '0.00';
    document.getElementById('closingBalance').textContent = '0.00';
    
    document.getElementById('openingDate').textContent = '';
    document.getElementById('receiptCount').textContent = '0 transactions';
    document.getElementById('paymentCount').textContent = '0 transactions';
    document.getElementById('closingDate').textContent = '';
    document.getElementById('transactionCount').textContent = 'Loading...';
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

function showError(message) {
    document.getElementById('ledgerBody').innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-5 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h5>Error Loading Data</h5>
                <p>${message}</p>
                <button class="btn btn-primary btn-sm mt-2" onclick="refreshData()">
                    <i class="fas fa-redo me-1"></i>Try Again
                </button>
            </td>
        </tr>
    `;
}

function printLedger() {
    window.print();
}

function exportToExcel() {
    // Simple CSV export implementation
    const table = document.getElementById('ledgerTable');
    let csv = [];
    
    // Get headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csv.push(headers.join(','));
    
    // Get data rows
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => {
            row.push(`"${td.textContent.trim().replace(/"/g, '""')}"`);
        });
        csv.push(row.join(','));
    });
    
    // Create download link
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    const month = document.getElementById('monthSelector').value;
    link.setAttribute('href', url);
    link.setAttribute('download', `ledger_${month}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endsection