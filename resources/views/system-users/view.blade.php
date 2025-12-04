@extends('layouts.app')

@section('title', 'System Users')
@section('page-title', 'System Users Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">System Users</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-primary bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Total Users</h4>
                                    <h2 class="text-white" id="totalUsers">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-success bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Active Users</h4>
                                    <h2 class="text-white" id="activeUsers">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-check fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-warning bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Inactive Users</h4>
                                    <h2 class="text-white" id="inactiveUsers">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-slash fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card bg-info bg-gradient">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="card-title text-white">Admins</h4>
                                    <h2 class="text-white" id="adminUsers">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-shield fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card custom-card">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">System Users</h5>
                            <p class="text-muted mb-0">Manage all system users and their permissions</p>
                        </div>
                        <div class="d-flex gap-2">
                            <!-- Filter Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="all">All Users</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="active">Active Only</a>
                                    </li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="inactive">Inactive
                                            Only</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="admin">Admins</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="user">Regular Users</a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Refresh Button -->
                            <button class="btn btn-outline-primary" id="refreshBtn" title="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>

                            <!-- Add New User Button -->
                            <a href="{{ route('system-users.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Add New User
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body position-relative">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-5">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading system users...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Failed to Load Users</h5>
                                <span id="errorText" class="mb-0"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-3 d-none" id="actionBar">
                        <div class="d-flex align-items-center gap-2">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm" onclick="exportTo('csv')">
                                    <i class="fas fa-file-csv me-1"></i>CSV
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="exportTo('excel')">
                                    <i class="fas fa-file-excel me-1"></i>Excel
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i>Print
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="exportTo('pdf')">
                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                </button>
                            </div>
                            <span class="text-muted ms-2" id="userCount">Showing 0 users</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <!-- Bulk Actions -->
                            <select class="form-select form-select-sm" style="width: 140px;" id="bulkActions">
                                <option value="">Bulk Actions</option>
                                <option value="activate">Activate Selected</option>
                                <option value="deactivate">Deactivate Selected</option>
                            </select>

                            <!-- Search Box -->
                            <div class="input-group input-group-sm" style="width: 280px;">
                                <span class="input-group-text bg-transparent">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search users..." id="searchInput"
                                    autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" title="Clear">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive d-none" id="usersTableContainer">
                        <table class="table table-hover table-striped" id="usersTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>User</th>
                                    <th>Contact</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <th>Last Active</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <!-- Users will be loaded here via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5 d-none">
                        <div class="empty-state-icon">
                            <i class="fas fa-users fa-4x text-muted mb-4"></i>
                        </div>
                        <h4 class="text-muted">No System Users Found</h4>
                        <p class="text-muted mb-4">There are no system users in the database yet.</p>
                        <a href="{{ route('system-users.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Add First User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Deactivation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate this user? The user will not be able to access the system.</p>
                    <p class="text-muted small">This action can be reversed by reactivating the user later.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Deactivate User</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Reactivate Confirmation Modal -->
    <div class="modal fade" id="reactivateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-user-check me-2"></i>Confirm Reactivation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reactivate this user? The user will be able to access the system again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmReactivateBtn">Reactivate User</button>
                </div>
            </div>
        </div>
    </div>



    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user me-2"></i>User Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="userDetailsContent">
                        <!-- User details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .stat-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .custom-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .custom-card .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .table th {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem 0.75rem;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
            border-color: #f1f3f4;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .avatar-lg {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
            border-radius: 10px;
        }

        .user-status-active {
            border-left: 4px solid #28a745;
        }

        .user-status-inactive {
            border-left: 4px solid #dc3545;
        }

        .empty-state-icon {
            opacity: 0.5;
        }

        .action-buttons .btn {
            border-radius: 8px;
            padding: 0.375rem 0.75rem;
        }

        /* Custom scrollbar for table */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/system_user/view.js') }}"></script>
@endpush