@extends('layouts.app')

@section('title', 'Institute Income')
@section('page-title', 'Institute Income')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Institute Income</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid">
        <div class="card">
            <!-- Header -->
            <div class="card-header bg-primary text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-university me-1"></i>Institute Income -
                        {{ date('F Y', strtotime($yearMonth ?? 'now')) }}
                    </h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-light px-2" id="prevMonth" title="Previous Month">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-light px-2" id="currentMonth" title="Current Month">
                            <i class="fas fa-calendar-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-light px-2" id="nextMonth" title="Next Month">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-3">
                <!-- Month Selector -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light py-1 px-2">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </span>
                            <input type="month" class="form-control form-control-sm py-1" id="monthSelector"
                                value="{{ date('Y-m') }}">
                            <button class="btn btn-primary btn-sm py-1 px-3" type="button" id="loadDataBtn">
                                <i class="fas fa-sync me-1"></i>Load
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-success px-3" id="viewExtraIncomeBtn">
                                <i class="fas fa-money-bill-wave me-1"></i>Extra
                            </button>
                            <button type="button" class="btn btn-danger px-3" id="viewExpensesBtn">
                                <i class="fas fa-receipt me-1"></i>Expenses
                            </button>
                        </div>
                    </div>
                </div>


                <!-- Summary Cards - Updated to match Quick Stats style -->
                <div class="row mb-4 g-3" id="summaryCards">
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <small class="text-muted ms-2">Loading summary...</small>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row mb-4 g-2">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-pie me-1"></i>Income & Expense Distribution
                                </h6>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="resetChartZoom">
                                        <i class="fas fa-search-minus"></i> Reset Zoom
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="downloadChartBtn">
                                        <i class="fas fa-download"></i> Download
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-3" style="height: 300px; position: relative;">
                                <canvas id="incomeDistributionChart" style="width: 100%; height: 100%;"></canvas>
                                <div class="chart-overlay text-center d-none" id="chartNoData"
                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <i class="fas fa-chart-pie fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No data available for chart</p>
                                </div>
                                <div class="chart-overlay text-center d-none" id="chartLoading"
                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mb-0 mt-2">Loading chart...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4 g-3">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-1"></i>Quick Stats
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3" id="quickStats">
                                    <div class="col-xl-3 col-md-6">
                                        <div class="stat-card bg-primary bg-opacity-10 p-3 rounded border">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="icon-wrapper bg-primary bg-opacity-25 p-3 rounded">
                                                        <i class="fas fa-percentage text-primary fa-lg"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <small class="text-muted d-block">Avg. Commission</small>
                                                    <div class="h5 mb-0 fw-bold text-primary" id="avgCommission">--%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <div class="stat-card bg-success bg-opacity-10 p-3 rounded border">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="icon-wrapper bg-success bg-opacity-25 p-3 rounded">
                                                        <i class="fas fa-user-tie text-success fa-lg"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <small class="text-muted d-block">Active Teachers</small>
                                                    <div class="h5 mb-0 fw-bold text-success" id="activeTeachers">--</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <div class="stat-card bg-info bg-opacity-10 p-3 rounded border">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="icon-wrapper bg-info bg-opacity-25 p-3 rounded">
                                                        <i class="fas fa-chalkboard text-info fa-lg"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <small class="text-muted d-block">Total Classes</small>
                                                    <div class="h5 mb-0 fw-bold text-info" id="totalClasses">--</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6">
                                        <div class="stat-card bg-warning bg-opacity-10 p-3 rounded border">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="icon-wrapper bg-warning bg-opacity-25 p-3 rounded">
                                                        <i class="fas fa-money-bill-wave text-warning fa-lg"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <small class="text-muted d-block">Avg. Net Earning/Teacher</small>
                                                    <div class="h5 mb-0 fw-bold text-warning" id="avgNetEarningTeacher">Rs
                                                        --
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                            <small class="text-muted">
                                <i class="fas fa-chart-bar me-1"></i>Reports
                            </small>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary btn-sm px-3" id="exportPdfBtn">
                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm px-3" id="exportExcelBtn">
                                    <i class="fas fa-file-excel me-1"></i>Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher Table - Updated with new columns -->
                <div class="card border">
                    <div class="card-header bg-light py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-users me-1"></i>Teacher Income
                            </h6>
                            <!-- Pagination Selector -->
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-2">Show:</small>
                                <select class="form-select form-select-sm w-auto" id="paginationSelect">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" id="dataTableContainer">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="3%" class="py-2 px-3">#</th>
                                        <th width="20%" class="py-2 px-3">Teacher</th>
                                        <th width="8%" class="py-2 px-3">%</th>
                                        <th width="12%" class="py-2 px-3 text-end">Payments</th>
                                        <th width="12%" class="py-2 px-3 text-end">Teacher Salary</th>
                                        <th width="12%" class="py-2 px-3 text-end">Advance</th>
                                        <th width="12%" class="py-2 px-3 text-end">Net Salary</th>
                                        <th width="9%" class="py-2 px-3 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="teacherIncomeBody">
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <small class="text-muted ms-2">Loading data...</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer py-2" id="paginationContainer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted" id="paginationInfo">
                                    Showing 0 to 0 of 0 entries
                                </small>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm mb-0" id="paginationLinks">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <div id="noDataMessage" class="text-center d-none p-4">
                            <div class="alert alert-info py-3 mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                <small>No data found for selected month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="classDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title mb-0">Class Details</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3" id="classDetailsContent"></div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="extraIncomeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title mb-0">
                        <i class="fas fa-money-bill-wave me-1"></i>Extra Income
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-info py-2 mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Extra income management will be implemented here.</small>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="expensesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title mb-0">
                        <i class="fas fa-receipt me-1"></i>Expenses
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-info py-2 mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Expenses management will be implemented here.</small>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* Summary Cards Styling - Matching Quick Stats */
        .stat-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--card-color, #4e73df) 0%, var(--card-color-light, #86a4ff) 100%);
        }

        /* Card Color Variants */
        .stat-card.teacher-payments {
            --card-color: #4e73df;
            --card-color-light: #86a4ff;
        }

        .stat-card.teacher-earnings {
            --card-color: #1cc88a;
            --card-color-light: #4ce3aa;
        }

        .stat-card.teacher-advances {
            --card-color: #6f42c1;
            --card-color-light: #9d6ffc;
        }

        .stat-card.teacher-salaries {
            --card-color: #fd7e14;
            --card-color-light: #ffa94d;
        }

        .stat-card.teacher-net-earnings {
            --card-color: #20c997;
            --card-color-light: #5dfcc9;
        }

        .stat-card.institute-income {
            --card-color: #f6c23e;
            --card-color-light: #ffd96a;
        }

        .stat-card.total-with-extra {
            --card-color: #e74a3b;
            --card-color-light: #ff7b6b;
        }

        .stat-card.institute-expenses {
            --card-color: #dc3545;
            --card-color-light: #ff6b7a;
        }

        .stat-card.net-income {
            --card-color: #20c997;
            --card-color-light: #5dfcc9;
        }

        .stat-card.institute-total {
            --card-color: #36b9cc;
            --card-color-light: #6cdef1;
        }

        .stat-card.extra-income {
            --card-color: #858796;
            --card-color-light: #b0b2c3;
        }

        .stat-card.admission-payments {
            --card-color: #17a2b8;
            --card-color-light: #4fd1e5;
        }
         .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .chart-overlay {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Pagination */
        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Teacher Net Earning Colors */
        .net-earning-positive {
            color: #198754 !important;
            font-weight: 600;
        }
        
        .net-earning-zero {
            color: #6c757d !important;
            font-weight: 500;
        }
        
        .net-earning-negative {
            color: #dc3545 !important;
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        /* Icon Styling */
        .icon-wrapper {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .stat-card:hover .icon-wrapper {
            transform: scale(1.05);
        }

        /* Text Styling */
        .stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1.2;
            margin: 0;
        }

        .stat-value .currency {
            font-size: 1rem;
            font-weight: 500;
            color: #718096;
            margin-right: 0.25rem;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .chart-overlay {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Pagination */
        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        /* Teacher Net Earning Colors */
        .net-earning-positive {
            color: #198754 !important;
            font-weight: 600;
        }

        .net-earning-zero {
            color: #6c757d !important;
            font-weight: 500;
        }

        .net-earning-negative {
            color: #dc3545 !important;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .icon-wrapper {
                width: 50px;
                height: 50px;
            }

            .stat-value {
                font-size: 1.25rem;
            }

            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .icon-wrapper {
                width: 45px;
                height: 45px;
            }

            .stat-value {
                font-size: 1.1rem;
            }

            .stat-label {
                font-size: 0.7rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        $(document).ready(function () {
            let currentMonth = $('#monthSelector').val();
            let chartInstance = null;
            let isLoading = false;
            let currentData = null;
            let currentPage = 1;
            let itemsPerPage = parseInt($('#paginationSelect').val());

            // Initialize
            loadInstituteData(currentMonth);

            // Event Listeners
            $('#monthSelector').change(function () {
                currentMonth = $(this).val();
                currentPage = 1;
                loadInstituteData(currentMonth);
            });

            $('#loadDataBtn').click(function () {
                currentMonth = $('#monthSelector').val();
                currentPage = 1;
                loadInstituteData(currentMonth);
            });

            $('#prevMonth').click(function () {
                navigateMonth(-1);
            });

            $('#nextMonth').click(function () {
                navigateMonth(1);
            });

            $('#currentMonth').click(function () {
                currentMonth = '{{ date("Y-m") }}';
                $('#monthSelector').val(currentMonth);
                currentPage = 1;
                loadInstituteData(currentMonth);
            });

            // Pagination
            $('#paginationSelect').change(function () {
                itemsPerPage = parseInt($(this).val());
                currentPage = 1;
                if (currentData) {
                    displayTeacherTable(currentData);
                }
            });

            // Download Chart
            $('#downloadChartBtn').click(function () {
                if (chartInstance) {
                    const link = document.createElement('a');
                    link.download = `income-distribution-${currentMonth}.png`;
                    link.href = chartInstance.toBase64Image();
                    link.click();
                }
            });

            $('#resetChartZoom').click(function () {
                if (chartInstance) {
                    chartInstance.resetZoom();
                    $(this).prop('disabled', true);
                }
            });

            // For navigating in the same window (keeps back button history)
            $('#viewExtraIncomeBtn').click(function () {
                window.location.assign('/institute-payment/extra');
            });

            $('#viewExpensesBtn').click(function () {
                window.location.assign(`/institute-payment/expenses`);
            });

            // Export Buttons (Placeholder functionality)
            $('#exportPdfBtn').click(function () {
                showNotification('PDF export feature will be implemented soon', 'info');
            });

            $('#exportExcelBtn').click(function () {
                showNotification('Excel export feature will be implemented soon', 'info');
            });

            // Functions
            function navigateMonth(direction) {
                let date = new Date(currentMonth + '-01');
                date.setMonth(date.getMonth() + direction);
                currentMonth = date.toISOString().slice(0, 7);
                $('#monthSelector').val(currentMonth);
                currentPage = 1;
                loadInstituteData(currentMonth);
            }

            function loadInstituteData(yearMonth) {
                if (isLoading) return;

                isLoading = true;
                showLoadingState();

                $.ajax({
                    url: '/api/institute-payments/monthly-income/' + yearMonth,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            currentData = response;
                            displaySummaryCards(response);
                            displayTeacherTable(response);
                            updateCharts(response);
                            updateQuickStats(response);
                            hideNoDataMessage();
                        } else {
                            showNoDataMessage();
                            clearCharts();
                        }
                    },
                    error: function (xhr) {
                        console.error('Error loading data:', xhr);
                        showNoDataMessage();
                        clearCharts();
                        showNotification('Error loading data', 'error');
                    },
                    complete: function () {
                        isLoading = false;
                    }
                });
            }

            function showLoadingState() {
                $('#summaryCards').html(`
                                <div class="col-12 text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <small class="text-muted ms-2">Loading summary...</small>
                                </div>
                            `);

                $('#teacherIncomeBody').html(`
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <small class="text-muted ms-2">Loading data...</small>
                                    </td>
                                </tr>
                            `);

                $('#chartLoading').removeClass('d-none');
                $('#chartNoData').addClass('d-none');
            }

            function displaySummaryCards(data) {
                const summary = data.summary || data;

                // Calculate net income (total income including extra minus expenses)
                const totalIncomeWithExtra = parseFloat(summary.total_institute_income_including_extra || 0);
                const expenses = parseFloat(summary.total_institute_expenese || 0);
                const netIncome = totalIncomeWithExtra - expenses;

                // Data for summary cards
                const summaryCardsData = [
                    {
                        label: 'Extra Income',
                        value: summary.extra_income_for_month || 0,
                        icon: 'plus-circle',
                        color: 'secondary',
                        cardClass: 'extra-income',
                        iconClass: 'text-secondary'
                    },
                    {
                        label: 'Total Payments',
                        value: summary.total_teacher_payments || 0,
                        icon: 'money-bill-wave',
                        color: 'primary',
                        cardClass: 'teacher-payments',
                        iconClass: 'text-primary'
                    },
                    {
                        label: 'Teachers Salary',
                        value: summary.total_teacher_earnings || 0,
                        icon: 'user-tie',
                        color: 'success',
                        cardClass: 'teacher-earnings',
                        iconClass: 'text-success'
                    },
                    {
                        label: 'Teacher Advances',
                        value: summary.total_teacher_advances || 0,
                        icon: 'hand-holding-usd',
                        color: 'purple',
                        cardClass: 'teacher-advances',
                        iconClass: 'text-purple'
                    },
                    {
                        label: 'Teacher Net Salary',
                        value: summary.total_teacher_net_earnings || 0,
                        icon: 'calculator',
                        color: 'teal',
                        cardClass: 'teacher-net-earnings',
                        iconClass: 'text-teal'
                    },
                    {
                        label: 'Institute Income',
                        value: summary.total_institute_from_classes || 0,
                        icon: 'school',
                        color: 'warning',
                        cardClass: 'institute-income',
                        iconClass: 'text-warning'
                    },
                    {
                        label: 'Institute Expenses',
                        value: summary.total_institute_expenese || 0,
                        icon: 'receipt',
                        color: 'danger',
                        cardClass: 'institute-expenses',
                        iconClass: 'text-danger'
                    },
                    {
                        label: 'Admission Payments',
                        value: summary.admission_payments || 0,
                        icon: 'user-graduate',
                        color: 'cyan',
                        cardClass: 'admission-payments',
                        iconClass: 'text-cyan'
                    },
                    {
                        label: 'Institute Net Income',
                        value: summary.institute_net_income || 0,
                        icon: 'calculator',
                        color: 'teal',
                        cardClass: 'net-income',
                        iconClass: 'text-teal'
                    }
                ];

                // Create cards HTML - 4 cards per row
                let cardsHTML = '';
                let cardCount = 0;

                summaryCardsData.forEach((card, index) => {
                    if (index % 4 === 0) {
                        // Start new row
                        if (index > 0) {
                            cardsHTML += '</div>';
                        }
                        cardsHTML += '<div class="row mb-2 g-2">';
                    }

                    cardsHTML += `
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6">
                    <div class="stat-card ${card.cardClass} p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="icon-wrapper bg-${card.color} bg-opacity-25 p-2 rounded">
                                    <i class="fas fa-${card.icon} ${card.iconClass}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="stat-label text-truncate" title="${card.label}">${card.label}</div>
                                <div class="stat-value">
                                    <span class="currency">Rs</span>${formatNumber(card.value)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                    cardCount++;

                    // If it's the last card, close the row
                    if (index === summaryCardsData.length - 1) {
                        // Fill remaining slots in the row with empty cards for alignment
                        const remainingSlots = 4 - (cardCount % 4);
                        if (remainingSlots < 4 && remainingSlots > 0) {
                            for (let i = 0; i < remainingSlots; i++) {
                                cardsHTML += `<div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6"></div>`;
                            }
                        }
                        cardsHTML += '</div>';
                    }
                });

                $('#summaryCards').html(cardsHTML);

                // Add custom CSS for colors if not already added
                if (!$('#custom-colors-style').length) {
                    $('head').append(`
                <style id="custom-colors-style">
                    .bg-purple { background-color: #6f42c1 !important; }
                    .text-purple { color: #6f42c1 !important; }
                    .bg-purple.bg-opacity-25 { background-color: rgba(111, 66, 193, 0.25) !important; }

                    .bg-orange { background-color: #fd7e14 !important; }
                    .text-orange { color: #fd7e14 !important; }
                    .bg-orange.bg-opacity-25 { background-color: rgba(253, 126, 20, 0.25) !important; }

                    .bg-teal { background-color: #20c997 !important; }
                    .text-teal { color: #20c997 !important; }
                    .bg-teal.bg-opacity-25 { background-color: rgba(32, 201, 151, 0.25) !important; }

                    .bg-cyan { background-color: #17a2b8 !important; }
                    .text-cyan { color: #17a2b8 !important; }
                    .bg-cyan.bg-opacity-25 { background-color: rgba(23, 162, 184, 0.25) !important; }

                    /* Card hover effects */
                    .stat-card {
                        transition: all 0.3s ease;
                        border: 1px solid rgba(0, 0, 0, 0.05);
                        height: 100%;
                        background: white;
                        border-radius: 10px;
                        overflow: hidden;
                        position: relative;
                        min-height: 110px;
                    }

                    .stat-card:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                    }

                    .stat-card::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        height: 4px;
                        background: linear-gradient(90deg, var(--card-color, #4e73df) 0%, var(--card-color-light, #86a4ff) 100%);
                    }

                    /* Card Color Variants */
                    .stat-card.teacher-payments {
                        --card-color: #4e73df;
                        --card-color-light: #86a4ff;
                    }

                    .stat-card.teacher-earnings {
                        --card-color: #1cc88a;
                        --card-color-light: #4ce3aa;
                    }

                    .stat-card.teacher-advances {
                        --card-color: #6f42c1;
                        --card-color-light: #9d6ffc;
                    }

                    .stat-card.teacher-salaries {
                        --card-color: #fd7e14;
                        --card-color-light: #ffa94d;
                    }

                    .stat-card.teacher-net-earnings {
                        --card-color: #20c997;
                        --card-color-light: #5dfcc9;
                    }

                    .stat-card.institute-income {
                        --card-color: #f6c23e;
                        --card-color-light: #ffd96a;
                    }

                    .stat-card.total-with-extra {
                        --card-color: #e74a3b;
                        --card-color-light: #ff7b6b;
                    }

                    .stat-card.institute-expenses {
                        --card-color: #dc3545;
                        --card-color-light: #ff6b7a;
                    }

                    .stat-card.net-income {
                        --card-color: #20c997;
                        --card-color-light: #5dfcc9;
                    }

                    .stat-card.institute-total {
                        --card-color: #36b9cc;
                        --card-color-light: #6cdef1;
                    }

                    .stat-card.extra-income {
                        --card-color: #858796;
                        --card-color-light: #b0b2c3;
                    }

                    .stat-card.admission-payments {
                        --card-color: #17a2b8;
                        --card-color-light: #4fd1e5;
                    }

                    /* Icon Styling */
                    .icon-wrapper {
                        width: 60px;
                        height: 60px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 12px;
                        transition: all 0.3s ease;
                    }

                    .stat-card:hover .icon-wrapper {
                        transform: scale(1.05);
                    }

                    /* Text Styling */
                    .stat-label {
                        font-size: 0.8rem;
                        font-weight: 600;
                        color: #6c757d;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        margin-bottom: 0.25rem;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }

                    .stat-value {
                        font-size: 1.5rem;
                        font-weight: 700;
                        color: #2d3748;
                        line-height: 1.2;
                        margin: 0;
                    }

                    .stat-value .currency {
                        font-size: 1rem;
                        font-weight: 500;
                        color: #718096;
                        margin-right: 0.25rem;
                    }

                    /* Responsive */
                    @media (max-width: 768px) {
                        .icon-wrapper {
                            width: 50px;
                            height: 50px;
                        }

                        .stat-value {
                            font-size: 1.25rem;
                        }

                        .stat-label {
                            font-size: 0.7rem;
                        }
                    }

                    @media (max-width: 576px) {
                        .icon-wrapper {
                            width: 45px;
                            height: 45px;
                        }

                        .stat-value {
                            font-size: 1.1rem;
                        }

                        .stat-label {
                            font-size: 0.65rem;
                        }

                        .stat-card {
                            min-height: 100px;
                        }
                    }
                </style>
            `);
                }
            }

            function displayTeacherTable(data) {
                if (!data.data || data.data.length === 0) {
                    showNoDataMessage();
                    setupPagination(0, 0);
                    return;
                }

                const allTeachers = data.data;
                const totalItems = allTeachers.length;
                const totalPages = Math.ceil(totalItems / itemsPerPage);

                if (currentPage > totalPages) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;

                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
                const paginatedTeachers = allTeachers.slice(startIndex, endIndex);

                let html = '';
                paginatedTeachers.forEach((teacher, index) => {
                    const globalIndex = startIndex + index + 1;
                    const netEarning = parseFloat(teacher.teacher_net_earning || 0);
                    const netEarningClass = netEarning > 0 ? 'net-earning-positive' :
                        netEarning < 0 ? 'net-earning-negative' : 'net-earning-zero';

                    html += `
                                <tr>
                                    <td class="px-3">${globalIndex}</td>
                                    <td class="px-3">
                                        <div class="fw-semibold">${teacher.teacher_name}</div>
                                        <small class="text-muted">ID: ${teacher.teacher_id}</small>
                                    </td>
                                    <td class="px-3">
                                        <span class="badge bg-info">${teacher.percentage}%</span>
                                    </td>
                                    <td class="text-end px-3">${formatCurrency(teacher.total_payments_this_month)}</td>
                                    <td class="text-end text-success px-3">${formatCurrency(teacher.teacher_total_earning)}</td>
                                    <td class="text-end text-purple px-3">${formatCurrency(teacher.teacher_advance)}</td>
                                    <td class="text-end text-orange px-3">${formatCurrency(teacher.teacher_salary)}</td>
                                    <td class="px-3 text-center">
                                        <button class="btn btn-outline-primary btn-sm view-classes" 
                                                data-teacher="${teacher.teacher_name}"
                                                data-classes='${JSON.stringify(teacher.class_wise_totals)}'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                });

                $('#teacherIncomeBody').html(html);
                $('#dataTableContainer').removeClass('d-none');
                setupPagination(totalItems, totalPages);

                $('.view-classes').off('click').on('click', function () {
                    const teacherName = $(this).data('teacher');
                    const classes = $(this).data('classes');
                    showClassDetails(teacherName, classes);
                });
            }

            function setupPagination(totalItems, totalPages) {
                const startIndex = (currentPage - 1) * itemsPerPage + 1;
                const endIndex = Math.min(currentPage * itemsPerPage, totalItems);

                $('#paginationInfo').text(`Showing ${startIndex} to ${endIndex} of ${totalItems} entries`);

                let paginationHtml = '';

                if (currentPage > 1) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a></li>`;
                } else {
                    paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Previous</a></li>`;
                }

                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                if (startPage > 1) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                    if (startPage > 2) paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
                }

                for (let i = startPage; i <= endPage; i++) {
                    if (i === currentPage) {
                        paginationHtml += `<li class="page-item active"><a class="page-link" href="#">${i}</a></li>`;
                    } else {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                    }
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
                }

                if (currentPage < totalPages) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}">Next</a></li>`;
                } else {
                    paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>`;
                }

                $('#paginationLinks').html(paginationHtml);

                $('.page-link[data-page]').click(function (e) {
                    e.preventDefault();
                    const page = parseInt($(this).data('page'));
                    if (page !== currentPage) {
                        currentPage = page;
                        displayTeacherTable(currentData);
                        $('html, body').animate({
                            scrollTop: $("#dataTableContainer").offset().top - 100
                        }, 300);
                    }
                });
            }

            function updateCharts(data) {
                if (typeof Chart === 'undefined') return;

                if (chartInstance) chartInstance.destroy();

                $('#chartLoading').addClass('d-none');

                const summary = data.summary || data;

                // Include all components in the chart
                const chartData = {
                    teacherEarnings: summary.total_teacher_earnings || 0,
                    teacherAdvances: summary.total_teacher_advances || 0,
                    teacherSalaries: summary.total_teacher_salaries || 0,
                    instituteIncome: summary.total_institute_from_classes || 0,
                    extraIncome: summary.extra_income_for_month || 0,
                    expenses: summary.total_institute_expenese || 0
                };

                const total = chartData.teacherEarnings + chartData.teacherAdvances +
                    chartData.teacherSalaries + chartData.instituteIncome +
                    chartData.extraIncome + chartData.expenses;

                if (total === 0) {
                    $('#chartNoData').removeClass('d-none');
                    $('#resetChartZoom').prop('disabled', true);
                    return;
                }

                $('#chartNoData').addClass('d-none');

                const ctx = document.getElementById('incomeDistributionChart').getContext('2d');
                chartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Teacher Salary', 'Teacher Advances',
                            'Institute Income', 'Extra Income', 'Expenses'],
                        datasets: [{
                            data: [
                                chartData.teacherEarnings,
                                chartData.teacherAdvances,
                                chartData.instituteIncome,
                                chartData.extraIncome,
                                chartData.expenses
                            ],
                            backgroundColor: [
                                'rgba(40, 167, 69, 0.8)',    // Green for teacher earnings
                                'rgba(111, 66, 193, 0.8)',   // Purple for teacher advances
                                'rgba(255, 193, 7, 0.8)',    // Yellow for institute income
                                'rgba(23, 162, 184, 0.8)',   // Teal for extra income
                                'rgba(220, 53, 69, 0.8)'     // Red for expenses
                            ],
                            borderColor: [
                                'rgba(40, 167, 69, 1)',
                                'rgba(111, 66, 193, 1)',
                                'rgba(255, 193, 7, 1)',
                                'rgba(23, 162, 184, 1)',
                                'rgba(220, 53, 69, 1)'
                            ],
                            borderWidth: 1,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 12,
                                    font: { size: 11 },
                                    padding: 15
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: Rs ${formatNumber(value)} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });

                $('#resetChartZoom').prop('disabled', true);
            }

            function updateQuickStats(data) {
                const summary = data.summary || data;
                const teachers = data.data || [];

                let totalCommission = 0;
                let activeTeachers = 0;
                let totalClasses = 0;
                let totalNetEarning = 0;

                teachers.forEach(teacher => {
                    if (teacher.total_payments_this_month > 0) {
                        totalCommission += parseFloat(teacher.percentage);
                        activeTeachers++;
                        totalNetEarning += parseFloat(teacher.teacher_net_earning || 0);
                    }
                    if (teacher.class_wise_totals) {
                        // Count only classes that have actual data (not null class_id)
                        const validClasses = teacher.class_wise_totals.filter(cls => cls.class_id !== null);
                        totalClasses += validClasses.length;
                    }
                });

                const avgCommission = activeTeachers > 0 ? (totalCommission / activeTeachers).toFixed(1) : '0.0';
                const avgNetEarningTeacher = activeTeachers > 0 ?
                    Math.round(totalNetEarning / activeTeachers) : 0;

                $('#avgCommission').text(avgCommission + '%');
                $('#activeTeachers').text(activeTeachers);
                $('#totalClasses').text(totalClasses);
                $('#avgNetEarningTeacher').text('Rs ' + formatNumber(avgNetEarningTeacher));
            }

            function clearCharts() {
                if (chartInstance) {
                    chartInstance.destroy();
                    chartInstance = null;
                }
                $('#chartNoData').removeClass('d-none');
                $('#chartLoading').addClass('d-none');
                $('#resetChartZoom').prop('disabled', true);

                $('#quickStats div.h5').each(function () {
                    if ($(this).attr('id') === 'avgCommission') {
                        $(this).text('--%');
                    } else if ($(this).attr('id') === 'avgNetEarningTeacher') {
                        $(this).text('Rs --');
                    } else {
                        $(this).text('--');
                    }
                });
            }

            function showNoDataMessage() {
                $('#noDataMessage').removeClass('d-none');
                $('#dataTableContainer').addClass('d-none');
                $('#paginationContainer').addClass('d-none');
                $('#summaryCards').html(`
                            <div class="col-12">
                                <div class="alert alert-warning py-3">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <small>No data available for selected month</small>
                                </div>
                            </div>
                        `);
            }

            function hideNoDataMessage() {
                $('#noDataMessage').addClass('d-none');
                $('#dataTableContainer').removeClass('d-none');
                $('#paginationContainer').removeClass('d-none');
            }

            function showClassDetails(teacherName, classes) {
                let html = `<h6 class="mb-3">${teacherName}</h6>`;

                if (!classes || classes.length === 0 || classes[0].class_id === null) {
                    html += `<div class="alert alert-warning py-2"><small>No class data</small></div>`;
                } else {
                    html += `
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th><small>Class</small></th>
                                                <th class="text-end"><small>Amount</small></th>
                                                <th class="text-end"><small>Teacher</small></th>
                                                <th class="text-end"><small>Institute</small></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                    let totalAmount = 0, totalTeacher = 0, totalInstitute = 0;

                    classes.forEach(cls => {
                        if (cls.class_id !== null) { // Only show valid classes
                            totalAmount += parseFloat(cls.total_amount || 0);
                            totalTeacher += parseFloat(cls.teacher_earning || 0);
                            totalInstitute += parseFloat(cls.institute_income || 0);

                            html += `
                                        <tr>
                                            <td><small>${cls.class_name || 'N/A'}</small></td>
                                            <td class="text-end"><small>${formatCurrency(cls.total_amount)}</small></td>
                                            <td class="text-end text-success"><small>${formatCurrency(cls.teacher_earning)}</small></td>
                                            <td class="text-end text-primary"><small>${formatCurrency(cls.institute_income)}</small></td>
                                        </tr>
                                    `;
                        }
                    });

                    html += `
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th><small>Total</small></th>
                                                <th class="text-end"><small>${formatCurrency(totalAmount)}</small></th>
                                                <th class="text-end"><small>${formatCurrency(totalTeacher)}</small></th>
                                                <th class="text-end"><small>${formatCurrency(totalInstitute)}</small></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            `;
                }

                $('#classDetailsContent').html(html);
                $('#classDetailsModal').modal('show');
            }

            function formatCurrency(value) {
                const num = Number(value) || 0;
                return 'Rs ' + num.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            function formatNumber(num) {
                const number = Number(num) || 0;
                return number.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            function showNotification(message, type = 'info') {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                } else {
                    const alertClass = type === 'error' ? 'danger' : type;
                    const icon = type === 'success' ? 'check-circle' :
                        type === 'error' ? 'exclamation-triangle' : 'info-circle';

                    const notification = $(`
                                <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
                                     style="top: 20px; right: 20px; z-index: 9999; max-width: 300px;">
                                    <i class="fas fa-${icon} me-2"></i>
                                    <small>${message}</small>
                                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                </div>
                            `);

                    $('body').append(notification);
                    setTimeout(() => {
                        notification.alert('close');
                    }, 3000);
                }
            }
        });
    </script>
@endpush