@extends('layouts.app')

@section('title', 'Welfare Amount Settings')
@section('page-title', 'Welfare Amount Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Welfare Amount</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Welfare Amount</h3>
                </div>
                
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <!-- Current Welfare Setting Display -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="info-box">
                                <h5>Current Welfare Setting</h5>
                                @if($welfareSetting)
                                    <div class="alert alert-success">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-cog me-2"></i>
                                                <strong>Active Welfare Amount:</strong> 
                                                <span class="ms-2">LKR {{ number_format($welfareSetting->amount, 2) }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    Set on: {{ $welfareSetting->created_at->format('M d, Y h:i A') }}
                                                </small>
                                            </div>
                                            <div>
                                                <form action="{{ route('welfare_settings.destroy', $welfareSetting->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to deactivate this welfare setting?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash-alt me-1"></i> Deactivate
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No active welfare setting found. Please create one below.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Create/Update Form -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        {{ $welfareSetting ? 'Update Welfare Amount' : 'Create New Welfare Amount' }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('welfare_settings.store') }}" method="POST" id="welfareForm">
                                        @csrf
                                        
                                        @if($welfareSetting)
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Creating a new welfare amount will automatically deactivate the current one.
                                            </div>
                                        @endif
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="amount" class="form-label">
                                                        <i class="fas fa-money-bill-wave me-1"></i>
                                                        Welfare Amount (LKR)
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">LKR</span>
                                                        <input type="number" 
                                                               step="1" 
                                                               min="1" 
                                                               max="1000000"
                                                               class="form-control @error('amount') is-invalid @enderror" 
                                                               id="amount" 
                                                               name="amount" 
                                                               value="{{ old('amount') }}" 
                                                               placeholder="Enter welfare amount in LKR"
                                                               required>
                                                    </div>
                                                    @error('amount')
                                                        <div class="invalid-feedback d-block">
                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <small class="form-text text-muted">
                                                        Enter the welfare contribution amount in Sri Lankan Rupees.
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Quick Amounts (LKR)</label>
                                                    <div class="btn-group d-flex flex-wrap" role="group">
                                                        <button type="button" class="btn btn-outline-secondary quick-amount m-1" data-amount="1000">LKR 1,000</button>
                                                        <button type="button" class="btn btn-outline-secondary quick-amount m-1" data-amount="2000">LKR 2,000</button>
                                                        <button type="button" class="btn btn-outline-secondary quick-amount m-1" data-amount="5000">LKR 5,000</button>
                                                        <button type="button" class="btn btn-outline-secondary quick-amount m-1" data-amount="10000">LKR 10,000</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i>
                                                    {{ $welfareSetting ? 'Update Welfare Amount' : 'Create Welfare Amount' }}
                                                </button>
                                                <button type="reset" class="btn btn-outline-secondary">
                                                    <i class="fas fa-redo me-1"></i>
                                                    Reset
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Information Panel -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-lightbulb me-2"></i>How it works:</h6>
                                        <ul class="mb-0">
                                            <li>Only one welfare amount can be active at a time</li>
                                            <li>Creating a new amount deactivates the previous one</li>
                                            <li>Deactivated amounts can be viewed in history</li>
                                            <li>Amounts are used for welfare calculations</li>
                                            <li>All amounts are in Sri Lankan Rupees (LKR)</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6><i class="fas fa-history me-2"></i>Recent Activity</h6>
                                        @php
                                            $recentSettings = App\Models\WelfareSetting::where('user_id', auth()->id())
                                                ->orderBy('created_at', 'desc')
                                                ->limit(5)
                                                ->get();
                                        @endphp
                                        
                                        @if($recentSettings->count() > 0)
                                            <div class="list-group list-group-flush">
                                                @foreach($recentSettings as $setting)
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="badge bg-{{ $setting->status ? 'success' : 'secondary' }} me-2">
                                                                {{ $setting->status ? 'Active' : 'Inactive' }}
                                                            </span>
                                                            LKR {{ number_format($setting->amount, 2) }}
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $setting->created_at->format('M d') }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No previous settings found.</p>
                                        @endif
                                    </div>
                                    
                                    <!-- Currency Information -->
                                    <div class="mt-3">
                                        <h6><i class="fas fa-coins me-2"></i>Currency</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-flag text-success"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted">
                                                    All amounts are in Sri Lankan Rupees (LKR)<br>
                                                    Symbol: රු | Code: LKR
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer with Stats -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                User: {{ auth()->user()->name }}
                            </small>
                        </div>
                        <div class="col-md-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Last Updated: {{ $welfareSetting ? $welfareSetting->updated_at->diffForHumans() : 'Never' }}
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <small class="text-muted">
                                <i class="fas fa-database me-1"></i>
                                Status: {{ $welfareSetting ? 'Active' : 'Inactive' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quick amount buttons
        document.querySelectorAll('.quick-amount').forEach(button => {
            button.addEventListener('click', function() {
                const amount = this.getAttribute('data-amount');
                document.getElementById('amount').value = amount;
                
                // Highlight the selected button
                document.querySelectorAll('.quick-amount').forEach(btn => {
                    btn.classList.remove('active', 'btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary', 'active');
            });
        });
        
        // Form validation
        document.getElementById('welfareForm').addEventListener('submit', function(e) {
            const amountInput = document.getElementById('amount');
            const amount = parseFloat(amountInput.value);
            
            if (amount < 1 || amount > 1000000) {
                e.preventDefault();
                alert('Please enter an amount between LKR 1 and LKR 1,000,000');
                amountInput.focus();
                return false;
            }
            
            @if($welfareSetting)
                if (confirm('This will deactivate your current welfare setting. Are you sure?')) {
                    return true;
                } else {
                    e.preventDefault();
                    return false;
                }
            @endif
        });
        
        // Reset form button
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            document.querySelectorAll('.quick-amount').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-secondary');
            });
        });
        
        // Format input with commas on blur
        document.getElementById('amount').addEventListener('blur', function() {
            const value = this.value.replace(/,/g, '');
            if (value) {
                const formatted = new Intl.NumberFormat('en-LK', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);
                this.value = value; // Keep as number for form submission
            }
        });
        
        // Remove commas on focus
        document.getElementById('amount').addEventListener('focus', function() {
            this.value = this.value.replace(/,/g, '');
        });
    });
</script>

<style>
.info-box {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.info-box h5 {
    color: #495057;
    margin-bottom: 10px;
}

.quick-amount.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.quick-amount:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.list-group-item {
    border: none;
    padding: 0.5rem 0;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.input-group-text {
    background-color: #28a745;
    color: white;
    border-color: #28a745;
    font-weight: bold;
}

.bg-success {
    background-color: #28a745 !important;
}
</style>
@endpush