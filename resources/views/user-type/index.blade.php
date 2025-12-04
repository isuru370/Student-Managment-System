@extends('layouts.app')

@section('title', 'User Types')
@section('page-title', 'User Types Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">User Types</li>
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
                                    <h4 class="card-title text-white">Total Types</h4>
                                    <h2 class="text-white" id="totalTypes">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-layer-group fa-2x text-white-50"></i>
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
                                    <h4 class="card-title text-white">Active Types</h4>
                                    <h2 class="text-white" id="activeTypes">0</h2>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x text-white-50"></i>
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
                            <h5 class="card-title mb-1">User Types</h5>
                            <p class="text-muted mb-0">Manage all user types and their permissions</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" onclick="loadUserTypes()" title="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <a href="{{ route('user-types.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add New Type
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body position-relative">
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading user types...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger d-none" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="errorText"></span>
                    </div>

                    <!-- Action Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-3 d-none" id="actionBar">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted" id="typeCount">Showing 0 types</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <!-- Search Box -->
                            <div class="input-group input-group-sm" style="width: 280px;">
                                <span class="input-group-text bg-transparent">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Search user types..." id="searchInput"
                                    autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" title="Clear">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- User Types Table -->
                    <div class="table-responsive d-none" id="typesTableContainer">
                        <table class="table table-hover table-striped" id="typesTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Type Name</th>
                                    <th>Created Date</th>
                                    <th>Updated Date</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="typesTableBody">
                                <!-- User types will be loaded here via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5 d-none">
                        <div class="empty-state-icon">
                            <i class="fas fa-layer-group fa-4x text-muted mb-4"></i>
                        </div>
                        <h4 class="text-muted">No User Types Found</h4>
                        <p class="text-muted mb-4">There are no user types in the database yet.</p>
                        <a href="{{ route('user-types.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Add First Type
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Type Modal - FIXED VERSION -->
    <div class="modal fade" id="editTypeModal" tabindex="-1" aria-labelledby="editTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editTypeModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit User Type
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTypeForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editTypeId" name="id">
                        <div class="mb-3">
                            <label for="editTypeName" class="form-label">Type Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editTypeName" name="type_name" required
                                placeholder="Enter user type name">
                            <div class="invalid-feedback" id="typeNameError">Please provide a valid type name.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="updateTypeBtn">
                        <i class="fas fa-save me-2"></i>Update Type
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Modal Close Button -->
    <div class="position-fixed bottom-0 end-0 m-3" style="z-index: 9999;">
        <button type="button" class="btn btn-danger btn-sm shadow" id="emergencyModalClose" onclick="emergencyModalClose()">
            <i class="fas fa-times-circle me-1"></i>Close Modal
        </button>
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

        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
            border-radius: 10px;
        }

        /* Modal Fix Styles */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .modal {
            z-index: 1060 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }
    </style>
@endpush

@push('styles')
/* Custom modal backdrop */
.modal-backdrop-custom {
animation: fadeIn 0.15s linear;
}

@keyframes fadeIn {
from { opacity: 0; }
to { opacity: 0.5; }
}

/* Ensure modal is properly positioned */
.modal {
background: transparent !important;
}

.modal-content {
box-shadow: 0 10px 30px rgba(0,0,0,0.3);
border: none;
border-radius: 12px;
}
@push('scripts')
    <!-- Load emergency fix first -->
    <script src="{{ asset('js/user_types/index.js') }}"></script>
@endpush