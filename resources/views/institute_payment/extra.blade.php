@extends('layouts.app')

@section('title', 'Extra Income')
@section('page-title', 'Extra Income')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('institute_payment.index') }}">Institute Income</a></li>
    <li class="breadcrumb-item active">Extra Income</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <div class="card">
            <!-- Header -->
            <div class="card-header bg-success text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Extra Income
                    </h6>
                    <button type="button" class="btn btn-light btn-sm" id="backToInstituteBtn">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </button>
                </div>
            </div>

            <div class="card-body p-3">
                <!-- Month Selector -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light py-1">
                                <i class="fas fa-calendar-alt text-success"></i>
                            </span>
                            <input type="month" class="form-control form-control-sm py-1" id="monthSelector"
                                value="{{ date('Y-m') }}">
                            <button class="btn btn-success btn-sm py-1 px-3" type="button" id="loadDataBtn">
                                <i class="fas fa-sync me-1"></i>Load
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary btn-sm px-3" id="addExtraIncomeBtn">
                            <i class="fas fa-plus me-1"></i>Add
                        </button>
                    </div>
                </div>

                <!-- Summary Cards with White Text -->
                <div class="row mb-3 g-2">
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-success text-white">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="icon-wrapper bg-white bg-opacity-25 p-2 rounded-circle">
                                            <i class="fas fa-money-bill-wave text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-white-50 d-block">Total</small>
                                        <div class="h6 mb-0 fw-bold" id="totalExtraIncome">Rs 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="icon-wrapper bg-white bg-opacity-25 p-2 rounded-circle">
                                            <i class="fas fa-list text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-white-50 d-block">Entries</small>
                                        <div class="h6 mb-0 fw-bold" id="totalEntries">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-info text-white">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="icon-wrapper bg-white bg-opacity-25 p-2 rounded-circle">
                                            <i class="fas fa-calendar text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-white-50 d-block">Month</small>
                                        <div class="h6 mb-0 fw-bold" id="selectedMonthDisplay">{{ date('F Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="icon-wrapper bg-white bg-opacity-25 p-2 rounded-circle">
                                            <i class="fas fa-chart-line text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-white-50 d-block">Avg/Entry</small>
                                        <div class="h6 mb-0 fw-bold" id="avgPerEntry">Rs 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extra Income Table -->
                <div class="card border">
                    <div class="card-header bg-light py-2">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-table me-1"></i>Records
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="py-2 px-3">#</th>
                                        <th width="55%" class="py-2 px-3">Reason</th>
                                        <th width="20%" class="py-2 px-3">Amount</th>
                                        <th width="15%" class="py-2 px-3">Date</th>
                                        <th width="5%" class="py-2 px-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="extraIncomeBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="spinner-border spinner-border-sm text-success" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <small class="text-muted ms-2">Loading...</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="noDataMessage" class="text-center d-none p-4">
                            <div class="alert alert-info py-2">
                                <i class="fas fa-info-circle me-1"></i>
                                <small>No records found</small>
                            </div>
                            <button type="button" class="btn btn-success btn-sm mt-1" id="addFirstRecordBtn">
                                <i class="fas fa-plus me-1"></i>Add First
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="extraIncomeModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title mb-0" id="modalTitle">Add Extra Income</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="extraIncomeForm">
                    <div class="modal-body p-3">
                        <input type="hidden" id="recordId" name="id">
                        <input type="hidden" id="currentMonth" name="current_month">

                        <div class="mb-2">
                            <label for="reason" class="form-label small">Reason <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="reason" name="reason"
                                placeholder="Enter reason" required>
                            <div class="invalid-feedback small">Required</div>
                        </div>

                        <div class="mb-2">
                            <label for="amount" class="form-label small">Amount <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rs</span>
                                <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01"
                                    placeholder="0.00" required>
                                <div class="invalid-feedback small">Required</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="recordDate" class="form-label small">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="recordDate" name="record_date"
                                value="{{ date('Y-m-d') }}" required>
                            <div class="invalid-feedback small">Required</div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title mb-0">Confirm Delete</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                        <p class="mb-1 small">Delete this record?</p>
                        <p class="text-muted small">Cannot be undone</p>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .icon-wrapper {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-body {
            padding: 0.5rem !important;
        }

        .table th,
        .table td {
            padding: 0.5rem !important;
        }

        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }

        .btn-outline-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function () {
            let currentMonth = $('#monthSelector').val();
            let deleteRecordId = null;
            let isLoading = false;

            function formatMonthDisplay(month) {
                const date = new Date(month + '-01');
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }

            $('#selectedMonthDisplay').text(formatMonthDisplay(currentMonth));
            loadExtraIncome(currentMonth);

            // Event Listeners
            $('#monthSelector').change(function () {
                currentMonth = $(this).val();
                $('#selectedMonthDisplay').text(formatMonthDisplay(currentMonth));
                loadExtraIncome(currentMonth);
            });

            $('#loadDataBtn').click(function () {
                currentMonth = $('#monthSelector').val();
                $('#selectedMonthDisplay').text(formatMonthDisplay(currentMonth));
                loadExtraIncome(currentMonth);
            });

            $('#backToInstituteBtn').click(function () {
                window.location.href = "{{ route('institute_payment.index') }}";
            });

            $('#addExtraIncomeBtn, #addFirstRecordBtn').click(function () {
                openAddModal();
            });

            $('#extraIncomeForm').submit(function (e) {
                e.preventDefault();
                saveExtraIncome();
            });

            $('#confirmDeleteBtn').click(function () {
                if (deleteRecordId) deleteExtraIncome(deleteRecordId);
            });

            // Modal Functions
            function openAddModal(record = null) {
                $('#modalTitle').text(record ? 'Edit Extra Income' : 'Add Extra Income');
                $('#recordId').val(record ? record.id : '');
                $('#reason').val(record ? record.reason : '');
                $('#amount').val(record ? record.amount : '');
                $('#recordDate').val(record ? record.record_date || record.created_at.split('T')[0] : '{{ date("Y-m-d") }}');
                $('#currentMonth').val(currentMonth);
                $('#extraIncomeForm').find('.is-invalid').removeClass('is-invalid');
                $('#extraIncomeModal').modal('show');
            }

            function openDeleteModal(id) {
                deleteRecordId = id;
                $('#deleteModal').modal('show');
            }

            // API Functions
            function loadExtraIncome(month) {
                if (isLoading) return;
                isLoading = true;
                showLoadingState();

                $.ajax({
                    url: `/api/institute-payments/extra-income/${month}`,
                    method: 'GET',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        if (response.status === 'success') {
                            displayExtraIncome(response.data, response.total, month);
                            updateSummaryCards(response.data, response.total);
                            hideNoDataMessage();
                        } else {
                            showNoDataMessage();
                        }
                    },
                    error: function (xhr) {
                        console.error('Error loading extra income:', xhr);
                        showNoDataMessage();
                        showNotification('Error loading data', 'error');
                    },
                    complete: function () { isLoading = false; }
                });
            }

            function saveExtraIncome() {
                if (isLoading) return;

                const formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    reason: $('#reason').val(),
                    amount: $('#amount').val(),
                    record_date: $('#recordDate').val()
                };

                // Validation
                if (!formData.reason.trim()) {
                    $('#reason').addClass('is-invalid');
                    return;
                }
                if (!formData.amount || formData.amount <= 0) {
                    $('#amount').addClass('is-invalid');
                    return;
                }
                if (!formData.record_date) {
                    $('#recordDate').addClass('is-invalid');
                    return;
                }

                isLoading = true;
                $('#saveBtn').prop('disabled', true);
                $('#saveBtn .spinner-border').removeClass('d-none');

                $.ajax({
                    url: '/api/institute-payments/extra-income/store',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#extraIncomeModal').modal('hide');
                            showNotification('Saved successfully', 'success');
                            loadExtraIncome(currentMonth);
                        } else {
                            showNotification(response.message || 'Error saving', 'error');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error saving extra income:', xhr);
                        showNotification('Error saving record', 'error');
                    },
                    complete: function () {
                        isLoading = false;
                        $('#saveBtn').prop('disabled', false);
                        $('#saveBtn .spinner-border').addClass('d-none');
                    }
                });
            }

            function deleteExtraIncome(id) {
                if (isLoading) return;

                isLoading = true;
                $('#confirmDeleteBtn').prop('disabled', true);
                $('#confirmDeleteBtn .spinner-border').removeClass('d-none');

                // Use the correct delete URL
                $.ajax({
                    url: `/api/institute-payments/extra-income/delete/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#deleteModal').modal('hide');
                            showNotification('Record deleted successfully', 'success');
                            loadExtraIncome(currentMonth);
                        } else {
                            showNotification(response.message || 'Error deleting record', 'error');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error deleting extra income:', xhr);
                        let errorMessage = 'Error deleting record';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showNotification(errorMessage, 'error');
                    },
                    complete: function () {
                        isLoading = false;
                        deleteRecordId = null;
                        $('#confirmDeleteBtn').prop('disabled', false);
                        $('#confirmDeleteBtn .spinner-border').addClass('d-none');
                    }
                });
            }

            // Display Functions
            function displayExtraIncome(data, total, selectedMonth) {
                if (!data || data.length === 0) {
                    showNoDataMessage();
                    return;
                }

                let html = '';
                const currentDate = new Date();
                const currentYearMonth = currentDate.getFullYear() + '-' +
                    String(currentDate.getMonth() + 1).padStart(2, '0');

                data.forEach((item, index) => {
                    const recordDate = item.record_date || item.created_at;
                    const date = new Date(recordDate);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });

                    // Get record month (YYYY-MM format)
                    const recordYear = date.getFullYear();
                    const recordMonth = String(date.getMonth() + 1).padStart(2, '0');
                    const recordYearMonth = `${recordYear}-${recordMonth}`;

                    // Check if record is from the CURRENT actual month (not selected month)
                    const canDelete = recordYearMonth === currentYearMonth;

                    html += `
                            <tr>
                                <td class="px-3">${index + 1}</td>
                                <td class="px-3"><small>${item.reason}</small></td>
                                <td class="px-3">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                        Rs ${formatNumber(item.amount)}
                                    </span>
                                </td>
                                <td class="px-3"><small>${formattedDate}</small></td>
                                <td class="px-3 text-center">
                                    ${canDelete ? `
                                    <button class="btn btn-outline-danger btn-sm delete-btn" 
                                            data-id="${item.id}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    ` : `
                                    <span class="text-muted" title="Cannot delete - Record is not from current month">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    `}
                                </td>
                            </tr>
                        `;
                });

                $('#extraIncomeBody').html(html);

                // Reattach event listeners for delete buttons
                $('.delete-btn').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = $(this).data('id');
                    openDeleteModal(id);
                });
            }

            function updateSummaryCards(data, total) {
                const totalAmount = total || 0;
                const entryCount = data ? data.length : 0;
                const avgPerEntry = entryCount > 0 ? (totalAmount / entryCount).toFixed(2) : 0;

                $('#totalExtraIncome').text('Rs ' + formatNumber(totalAmount));
                $('#totalEntries').text(entryCount);
                $('#avgPerEntry').text('Rs ' + formatNumber(avgPerEntry));
            }

            function showLoadingState() {
                $('#extraIncomeBody').html(`
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-success"></div>
                                <small class="text-muted ms-2">Loading...</small>
                            </td>
                        </tr>
                    `);
            }

            function showNoDataMessage() {
                $('#extraIncomeBody').html('');
                $('#noDataMessage').removeClass('d-none');
                updateSummaryCards([], 0);
            }

            function hideNoDataMessage() {
                $('#noDataMessage').addClass('d-none');
            }

            // Utility Functions
            function formatNumber(num) {
                const number = Number(num) || 0;
                return number.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            function showNotification(message, type = 'info') {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 3000
                    });
                }
            }

            // Input validation
            $('#reason, #amount, #recordDate').on('input change', function () {
                $(this).removeClass('is-invalid');
            });
        });
    </script>
@endpush