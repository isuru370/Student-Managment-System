@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Yearly Income Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-chart-line me-2 text-white"></i>Yearly Income Overview - {{ date('Y') }}
                    </h6>
                    <div class="d-flex align-items-center">
                        <div class="input-group input-group-sm me-2" style="width: 120px;">
                            <input type="text" class="form-control form-control-sm" id="yearSelector" 
                                   value="{{ date('Y') }}" placeholder="Year">
                            <button class="btn btn-primary btn-sm" type="button" id="loadYearlyChartBtn">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <button class="btn btn-sm btn-outline-white" id="downloadChartBtn">
                            <i class="fas fa-download me-1"></i>Download
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                        <canvas id="yearlyIncomeChart"></canvas>
                        <div class="chart-overlay text-center d-none" id="chartLoading"
                             style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mb-0 mt-2">Loading chart...</p>
                        </div>
                        <div class="chart-overlay text-center d-none" id="chartNoData"
                             style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No data available for chart</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card bg-light border-left-primary shadow-sm">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-money-bill-wave text-primary fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <small class="text-muted d-block">Yearly Gross Income</small>
                                            <div class="h5 mb-0 fw-bold text-primary" id="yearlyGrossIncome">Rs 0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-left-success shadow-sm">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-chart-bar text-success fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <small class="text-muted d-block">Highest Month</small>
                                            <div class="h5 mb-0 fw-bold text-success" id="highestMonth">--</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-left-info shadow-sm">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-trend-up text-info fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <small class="text-muted d-block">Monthly Average</small>
                                            <div class="h5 mb-0 fw-bold text-info" id="monthlyAverage">Rs 0</div>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Teachers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalActiveTeachers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ongoing Classes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOnGoinClasses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Today's Classes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-classes-count">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-white">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-6 mb-3">
                            <button class="btn btn-primary btn-block btn-action"
                                onclick="window.location.href='{{ route('students.create') }}'">
                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                Add Student
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3">
                            <button class="btn btn-success btn-block btn-action"
                                onclick="window.location.href='{{ route('teachers.create') }}'">
                                <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i><br>
                                Add Teacher
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3">
                            <button class="btn btn-info btn-block btn-action"
                                onclick="window.location.href='{{ route('class_rooms.create') }}'">
                                <i class="fas fa-book fa-2x mb-2"></i><br>
                                Create Class
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3">
                            <button class="btn btn-warning btn-block btn-action"
                                onclick="window.location.href='{{ route('student_attendance.index') }}'">
                                <i class="fas fa-clipboard-check fa-2x mb-2"></i><br>
                                Attendance
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3">
                            <button class="btn btn-danger btn-block btn-action"
                                onclick="window.location.href='{{ route('student-payment.create') }}'">
                                <i class="fas fa-credit-card fa-2x mb-2"></i><br>
                                Payments
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3">
                            <button class="btn btn-secondary btn-block btn-action"
                                onclick="window.location.href='{{ route('reports.index') }}'">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                                View Reports
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Report Style Timetable -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-white">
                <span id="timetable-title">Today's Class Schedule</span>
                <small class="text-muted d-block" id="selected-date-display"></small>
            </h6>
            <div class="btn-group">
                <div class="input-group me-2" style="width: 200px;">
                    <input type="date" class="form-control form-control-sm" id="date-filter"
                        value="{{ now()->format('Y-m-d') }}">
                    <button class="btn btn-primary btn-sm" type="button" onclick="loadTimetable()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <button class="btn btn-sm btn-success" onclick="loadTimetable()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-sm btn-info text-white" onclick="exportToExcel()">
                    <i class="fas fa-file-excel me-1"></i>Export
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Loading State -->
            <div id="loading-spinner" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading classes...</p>
            </div>

            <!-- Empty State -->
            <div id="no-classes-message" class="text-center py-4 d-none">
                <div class="empty-state">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted" id="empty-state-title">No classes scheduled</h4>
                    <p class="text-muted" id="empty-state-message">No classes found for the selected date</p>
                </div>
            </div>

            <!-- Timetable -->
            <div id="timetable-container" class="d-none">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped timetable-table" id="timetable">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2" class="text-center align-middle">Time</th>
                                <th id="subjects-header" colspan="0" class="text-center">Class Subjects</th>
                            </tr>
                            <tr id="subjects-row">
                                <!-- Subjects will be dynamically added here -->
                            </tr>
                        </thead>
                        <tbody id="timetable-body">
                            <!-- Timetable data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid !important;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .border-left-primary {
            border-left-color: #4e73df !important;
        }

        .border-left-success {
            border-left-color: #1cc88a !important;
        }

        .border-left-info {
            border-left-color: #36b9cc !important;
        }

        .border-left-warning {
            border-left-color: #f6c23e !important;
        }

        .btn-action {
            padding: 1.5rem 0.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            color: white;
            font-weight: 500;
            width: 100%;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        /* Chart styles */
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

        /* Timetable Styles */
        .timetable-table {
            font-size: 0.85rem;
            background: white;
        }

        .timetable-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            border: 1px solid #dee2e6;
            padding: 0.75rem;
            text-align: center;
            vertical-align: middle;
        }

        .timetable-table td {
            padding: 0.6rem;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            transition: all 0.2s ease;
        }

        .timetable-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .timetable-table tbody tr:hover td {
            background-color: #f8f9fa;
        }

        .time-slot {
            font-weight: 600;
            background-color: #f8f9fa !important;
            color: #2e59d9;
        }

        .class-cell {
            text-align: center;
            cursor: pointer;
            position: relative;
            min-height: 60px;
            vertical-align: middle;
        }

        .class-cell:hover {
            background-color: #e3f2fd !important;
        }

        .class-info {
            padding: 4px;
            border-radius: 4px;
            margin: 2px 0;
        }

        .class-scheduled {
            background-color: #e3f2fd;
            border-left: 3px solid #2196f3;
            color: #1976d2;
        }

        .class-ongoing {
            background-color: #e8f5e8;
            border-left: 3px solid #4caf50;
            color: #2e7d32;
        }

        .class-cancelled {
            background-color: #ffebee;
            border-left: 3px solid #f44336;
            color: #c62828;
            text-decoration: line-through;
        }

        .class-completed {
            background-color: #f3e5f5;
            border-left: 3px solid #9c27b0;
            color: #7b1fa2;
        }

        .teacher-name {
            font-size: 0.75rem;
            color: #666;
            font-style: italic;
        }

        .class-time {
            font-size: 0.7rem;
            color: #888;
            margin-top: 2px;
        }

        .empty-cell {
            background-color: #f8f9fa;
            color: #999;
            font-style: italic;
        }

        /* Status badges */
        .status-badge {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-scheduled {
            background-color: #bbdefb;
            color: #1976d2;
        }

        .status-ongoing {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .status-cancelled {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .status-completed {
            background-color: #e1bee7;
            color: #7b1fa2;
        }

        /* Empty State */
        .empty-state {
            padding: 3rem 1rem;
        }

        .empty-state i {
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .timetable-table {
                font-size: 0.75rem;
            }

            .timetable-table th,
            .timetable-table td {
                padding: 0.4rem;
            }

            .class-cell {
                min-height: 50px;
            }
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let yearlyChart = null;
        let currentYear = new Date().getFullYear();

        document.addEventListener('DOMContentLoaded', function() {
            loadYearlyIncomeChart(currentYear);
            loadTimetable();

            // Add enter key support for date filter
            document.getElementById('date-filter').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    loadTimetable();
                }
            });

            // Yearly chart event listeners
            document.getElementById('loadYearlyChartBtn').addEventListener('click', function() {
                const year = document.getElementById('yearSelector').value;
                if (year && year >= 2000 && year <= 2100) {
                    currentYear = parseInt(year);
                    loadYearlyIncomeChart(currentYear);
                } else {
                    alert('Please enter a valid year (2000-2100)');
                }
            });

            document.getElementById('yearSelector').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const year = this.value;
                    if (year && year >= 2000 && year <= 2100) {
                        currentYear = parseInt(year);
                        loadYearlyIncomeChart(currentYear);
                    } else {
                        alert('Please enter a valid year (2000-2100)');
                    }
                }
            });

            document.getElementById('downloadChartBtn').addEventListener('click', function() {
                if (yearlyChart) {
                    const link = document.createElement('a');
                    link.download = `yearly-income-chart-${currentYear}.png`;
                    link.href = yearlyChart.toBase64Image();
                    link.click();
                } else {
                    showDummyMessage('No chart available to download');
                }
            });
        });

        function loadYearlyIncomeChart(year) {
            const chartLoading = document.getElementById('chartLoading');
            const chartNoData = document.getElementById('chartNoData');
            
            chartLoading.classList.remove('d-none');
            chartNoData.classList.add('d-none');

            fetch(`/api/institute-payments/yearly-income-chart/${year}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    chartLoading.classList.add('d-none');
                    
                    if (data.status === 'success' && data.chart_data && data.chart_data.gross_incomes) {
                        updateYearlyIncomeChart(data);
                    } else {
                        showNoDataChart();
                    }
                })
                .catch(error => {
                    console.error('Error loading yearly income chart:', error);
                    chartLoading.classList.add('d-none');
                    showNoDataChart();
                });
        }

        function updateYearlyIncomeChart(data) {
            const chartData = data.chart_data;
            const summary = data.summary || {};
            
            // Update summary cards
            document.getElementById('yearlyGrossIncome').textContent = 'Rs ' + formatNumber(summary.yearly_gross_income || 0);
            
            // Calculate highest month
            const grossIncomes = chartData.gross_incomes || [];
            const maxIncome = Math.max(...grossIncomes);
            const maxMonthIndex = grossIncomes.indexOf(maxIncome);
            const monthLabels = chartData.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const highestMonth = maxMonthIndex >= 0 ? monthLabels[maxMonthIndex] : '--';
            document.getElementById('highestMonth').textContent = highestMonth;
            
            // Calculate monthly average
            const monthlyAverage = grossIncomes.length > 0 ? 
                grossIncomes.reduce((a, b) => a + b, 0) / grossIncomes.length : 0;
            document.getElementById('monthlyAverage').textContent = 'Rs ' + formatNumber(monthlyAverage);
            
            // Destroy existing chart if it exists
            if (yearlyChart) {
                yearlyChart.destroy();
            }
            
            // Create new chart
            const ctx = document.getElementById('yearlyIncomeChart').getContext('2d');
            
            // Prepare chart data
            const chartLabels = monthLabels.slice(0, grossIncomes.length);
            const chartIncomes = grossIncomes;
            
            yearlyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Monthly Gross Income',
                        data: chartIncomes,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        pointBackgroundColor: '#4e73df',
                        pointBorderColor: '#4e73df',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#4e73df',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 12
                                },
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'Rs ' + formatNumber(context.raw);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return 'Rs ' + formatNumber(value);
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    hover: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });
        }

        function showNoDataChart() {
            const chartNoData = document.getElementById('chartNoData');
            chartNoData.classList.remove('d-none');
            
            // Destroy existing chart if it exists
            if (yearlyChart) {
                yearlyChart.destroy();
                yearlyChart = null;
            }
            
            // Reset summary cards
            document.getElementById('yearlyGrossIncome').textContent = 'Rs 0';
            document.getElementById('highestMonth').textContent = '--';
            document.getElementById('monthlyAverage').textContent = 'Rs 0';
        }

        function formatNumber(num) {
            const number = Number(num) || 0;
            return number.toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Updated timetable function without ongoing count update
        function loadTimetable() {
            // Safely get elements with null checks
            const getElement = (id) => {
                const element = document.getElementById(id);
                if (!element) {
                    console.error(`Element with id '${id}' not found`);
                    return null;
                }
                return element;
            };
            
            const loadingSpinner = getElement('loading-spinner');
            const noClassesMessage = getElement('no-classes-message');
            const timetableContainer = getElement('timetable-container');
            const timetableBody = getElement('timetable-body');
            const subjectsRow = getElement('subjects-row');
            const subjectsHeader = getElement('subjects-header');
            const totalCountElement = getElement('total-classes-count'); // Only keep total count
            const dateFilter = getElement('date-filter');
            const timetableTitle = getElement('timetable-title');
            const selectedDateDisplay = getElement('selected-date-display');
            const emptyStateTitle = getElement('empty-state-title');
            const emptyStateMessage = getElement('empty-state-message');

            // Check if essential elements exist
            if (!dateFilter || !totalCountElement) {
                console.error('Essential timetable elements not found');
                return;
            }

            const selectedDate = dateFilter.value;

            // Update display
            const displayDate = new Date(selectedDate).toLocaleDateString('si-LK', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            if (selectedDateDisplay) selectedDateDisplay.textContent = displayDate;

            // Show loading
            if (loadingSpinner) loadingSpinner.classList.remove('d-none');
            if (noClassesMessage) noClassesMessage.classList.add('d-none');
            if (timetableContainer) timetableContainer.classList.add('d-none');
            if (timetableBody) timetableBody.innerHTML = '';
            if (subjectsRow) subjectsRow.innerHTML = '';

            // Build API URL with date parameter
            const apiUrl = `/api/class-attendances/by-date?date=${selectedDate}`;

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (loadingSpinner) loadingSpinner.classList.add('d-none');

                    if (!data.status || !data.data || data.data.length === 0) {
                        if (noClassesMessage) noClassesMessage.classList.remove('d-none');
                        if (emptyStateTitle) emptyStateTitle.textContent = 'No classes scheduled';
                        if (emptyStateMessage) emptyStateMessage.textContent = `No classes found for ${displayDate}`;
                        if (totalCountElement) totalCountElement.textContent = '0';
                        return;
                    }

                    // Update counts - only total count
                    const classes = data.data;
                    if (totalCountElement) totalCountElement.textContent = classes.length;

                    // Update title based on date
                    if (timetableTitle) {
                        const today = new Date().toDateString();
                        const selected = new Date(selectedDate).toDateString();
                        if (today === selected) {
                            timetableTitle.textContent = "Today's Class Schedule";
                        } else {
                            timetableTitle.textContent = "Class Schedule";
                        }
                    }

                    // Render timetable
                    renderTimetable(classes);
                    if (timetableContainer) timetableContainer.classList.remove('d-none');
                })
                .catch(error => {
                    console.error('Error loading timetable:', error);
                    if (loadingSpinner) loadingSpinner.classList.add('d-none');
                    showError('Failed to load timetable. Please try again later.');
                });
        }

        function renderTimetable(classes) {
            const timetableBody = document.getElementById('timetable-body');
            const subjectsRow = document.getElementById('subjects-row');
            const subjectsHeader = document.getElementById('subjects-header');

            // Extract unique subjects from API data
            const subjects = [...new Set(classes
                .map(classItem => classItem.class_details?.subject_name)
                .filter(subject => subject && subject.trim() !== '')
            )];

            // Update subjects header
            subjectsHeader.colSpan = subjects.length;

            // Create subject headers
            subjects.forEach(subject => {
                const subjectHeader = document.createElement('th');
                subjectHeader.className = 'text-center';
                subjectHeader.textContent = subject;
                subjectsRow.appendChild(subjectHeader);
            });

            // Define time slots based on actual class times
            const timeSlots = extractTimeSlots(classes);

            // Create timetable rows
            timeSlots.forEach(timeSlot => {
                const row = document.createElement('tr');

                // Time slot cell
                const timeCell = document.createElement('td');
                timeCell.className = 'time-slot';
                timeCell.textContent = timeSlot;
                row.appendChild(timeCell);

                // Subject cells
                subjects.forEach(subject => {
                    const subjectCell = document.createElement('td');
                    subjectCell.className = 'class-cell';

                    // Find classes for this time slot and subject
                    const matchingClasses = classes.filter(classItem => {
                        const classTime = `${classItem.start_time} - ${classItem.end_time}`;
                        const classSubject = classItem.class_details?.subject_name || '';
                        return classTime === timeSlot && classSubject === subject;
                    });

                    if (matchingClasses.length > 0) {
                        matchingClasses.forEach(classItem => {
                            const classInfo = document.createElement('div');
                            classInfo.className = `class-info ${getStatusClass(classItem)}`;

                            classInfo.innerHTML = `
                                        <div class="fw-bold">${classItem.class_details?.grade_name || 'Class'}</div>
                                        <div class="teacher-name">${classItem.class_details?.teacher_name || 'Teacher'}</div>
                                        <div class="class-time">Hall: ${classItem.class_hall || 'N/A'}</div>
                                        <span class="status-badge ${getStatusBadgeClass(classItem)}">${getStatusText(classItem)}</span>
                                    `;

                            classInfo.onclick = () => showClassDetails(classItem);
                            subjectCell.appendChild(classInfo);
                        });
                    } else {
                        subjectCell.innerHTML = '<div class="empty-cell">-</div>';
                    }

                    row.appendChild(subjectCell);
                });

                timetableBody.appendChild(row);
            });
        }

        function extractTimeSlots(classes) {
            // Extract unique time slots from classes
            const timeSlots = [...new Set(classes.map(classItem =>
                `${classItem.start_time} - ${classItem.end_time}`
            ))];

            // Sort time slots chronologically
            return timeSlots.sort((a, b) => {
                const timeA = convertToMinutes(a.split(' - ')[0]);
                const timeB = convertToMinutes(b.split(' - ')[0]);
                return timeA - timeB;
            });
        }

        function convertToMinutes(timeStr) {
            // Convert time string to minutes for sorting
            const [time, modifier] = timeStr.split(' ');
            let [hours, minutes] = time.split(':').map(Number);

            if (modifier === 'PM' && hours !== 12) hours += 12;
            if (modifier === 'AM' && hours === 12) hours = 0;

            return hours * 60 + minutes;
        }

        function getStatusClass(classItem) {
            if (classItem.is_ongoing === 0) return 'class-cancelled';
            if (classItem.status === 0) return 'class-scheduled';
            if (classItem.status === 1) return 'class-ongoing';
            return 'class-ongoing'; // default for other status values
        }

        function getStatusBadgeClass(classItem) {
            if (classItem.is_ongoing === 0) return 'status-cancelled';
            if (classItem.status === 0) return 'status-scheduled';
            if (classItem.status === 1) return 'status-ongoing';
            return 'status-ongoing'; // default for other status values
        }

        function getStatusText(classItem) {
            if (classItem.is_ongoing === 0) return 'Cancelled';
            if (classItem.status === 0) return 'Scheduled';
            if (classItem.status === 1) return 'Ongoing';
            return 'Ongoing'; // default for other status values
        }

        function showClassDetails(classItem) {
            const modalContent = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Class:</strong> ${classItem.class_details?.class_name || 'N/A'}</p>
                                <p><strong>Subject:</strong> ${classItem.class_details?.subject_name || 'N/A'}</p>
                                <p><strong>Grade:</strong> ${classItem.class_details?.grade_name || 'N/A'}</p>
                                <p><strong>Category:</strong> ${classItem.class_details?.category_name || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Teacher:</strong> ${classItem.class_details?.teacher_name || 'N/A'}</p>
                                <p><strong>Time:</strong> ${classItem.start_time} - ${classItem.end_time}</p>
                                <p><strong>Hall:</strong> ${classItem.class_hall || 'N/A'}</p>
                                <p><strong>Date:</strong> ${classItem.date}</p>
                                <p><strong>Status:</strong> ${getStatusText(classItem)}</p>
                            </div>
                        </div>
                    `;

            showModal('Class Details', modalContent);
        }

        function showModal(title, content) {
            // Remove existing modal if any
            const existingModal = document.getElementById('class-details-modal');
            if (existingModal) {
                existingModal.remove();
            }

            const modalDiv = document.createElement('div');
            modalDiv.className = 'modal fade';
            modalDiv.id = 'class-details-modal';
            modalDiv.tabIndex = -1;
            modalDiv.innerHTML = `
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">${title}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ${content}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    `;

            document.body.appendChild(modalDiv);

            const modal = new bootstrap.Modal(modalDiv);
            modal.show();
        }

        function exportToExcel() {
            const dateFilter = document.getElementById('date-filter');
            const selectedDate = dateFilter.value;
            showDummyMessage(`Exporting classes for ${selectedDate} to Excel...`);
        }

        function showError(message) {
            const timetableBody = document.getElementById('timetable-body');
            const subjectsCount = document.getElementById('subjects-row').children.length;
            timetableBody.innerHTML = `
                        <tr>
                            <td colspan="${subjectsCount + 1}">
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <div>${message}</div>
                                </div>
                            </td>
                        </tr>
                    `;
            document.getElementById('timetable-container').classList.remove('d-none');
        }

        function showDummyMessage(message) {
            // Simple alert for dummy messages
            alert(message);
        }
    </script>
@endpush