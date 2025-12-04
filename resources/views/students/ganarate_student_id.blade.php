@extends('layouts.app')

@section('title', 'Generate Student ID')
@section('page-title', 'Generate Student ID')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Generate Student ID</li>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-id-card me-2"></i>Student ID Card Generator</strong>
                </div>
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Date</label>
                            <input type="date" id="filterDate" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search Student</label>
                            <input type="text" id="searchStudent" class="form-control"
                                placeholder="Search by name or ID...">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-success me-2" id="bulkDownloadBtn">
                                <i class="fas fa-download me-2"></i>Bulk Download All
                            </button>
                            <button class="btn btn-outline-secondary" id="clearFiltersBtn">
                                <i class="fas fa-times me-2"></i>Clear
                            </button>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading students...</p>
                    </div>

                    <!-- Students List -->
                    <div id="studentsList" class="row" style="display: none;">
                        <!-- Students will be populated here -->
                    </div>

                    <!-- No Students Message -->
                    <div id="noStudentsMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Students Found</h4>
                        <p class="text-muted">Try adjusting your search criteria</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .student-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .student-item:hover {
            border-color: #0d6efd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let allStudents = [];
        let filteredStudents = [];

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadStudents();
            initializeEventListeners();
        });

        // Load students from API
        async function loadStudents() {
            try {
                showLoadingState();

                const response = await fetch('http://127.0.0.1:8000/api/students/active');
                if (!response.ok) throw new Error('Failed to fetch students');

                const result = await response.json();

                if (result.status === 'success' && result.data) {
                    allStudents = result.data.data || result.data;
                    filteredStudents = [...allStudents];
                    displayStudents();
                    showContentState();
                } else {
                    throw new Error(result.message || 'No student data found');
                }
            } catch (error) {
                console.error('Error loading students:', error);
                showErrorState('Failed to load students: ' + error.message);
            }
        }

        // Display students list
        function displayStudents() {
            const container = document.getElementById('studentsList');

            if (filteredStudents.length === 0) {
                container.style.display = 'none';
                document.getElementById('noStudentsMessage').style.display = 'block';
                return;
            }

            const html = filteredStudents.map(student => {
                return `
                            <div class="col-md-6 col-lg-4">
                                <div class="student-item">
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h6 class="mb-1">${student.fname} ${student.lname}</h6>
                                            <p class="text-muted small mb-1">ID: ${student.custom_id}</p>
                                            <p class="text-muted small mb-2">Grade: ${student.grade?.grade_name || 'N/A'}</p>
                                        </div>
                                        <div class="col-4 text-end">
                                            <button class="btn btn-primary btn-sm" onclick="generateSingleID('${student.custom_id}')">
                                                <i class="fas fa-download me-1"></i>Download ID
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
            }).join('');

            container.innerHTML = html;
            container.style.display = 'flex';
            document.getElementById('noStudentsMessage').style.display = 'none';
        }

        // Create ID card HTML based on your design
        function createIDCardHTML(student) {
            const imageUrl = student.img_url ? student.img_url : 'http://127.0.0.1:8000/uploads/logo/logo.png';
            const bgImageUrl = 'http://127.0.0.1:8000/uploads/id/idcard_bg.png';

            const validDate = new Date();
            validDate.setFullYear(validDate.getFullYear() + 1);
            const formattedDate = validDate.toISOString().split('T')[0];

            // Create the main container div
            const container = document.createElement('div');
            container.className = 'id-card';
            container.style.width = '85.6mm';
            container.style.height = '53.98mm';
            container.style.position = 'relative';
            container.style.overflow = 'hidden';
            container.style.background = 'white';
            container.style.borderRadius = '8px';
            container.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';

            // Set the inner HTML
            container.innerHTML = `
                <!-- Background Image -->
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('${bgImageUrl}'); background-size: cover; background-position: center; opacity: 0.1; z-index: 1;"></div>

                <!-- Content -->
                <div style="position: relative; z-index: 2; height: 100%; padding: 8px; display: flex; flex-direction: column;">

                    <!-- Header Section -->
                    <div style="text-align: center; margin-bottom: 10px; border-bottom: 2px solid #1a237e; padding-bottom: 5px;">
                        <h3 style="margin: 0; color: #1a237e; font-size: 16px; font-weight: bold;">SCHOOL NAME</h3>
                        <p style="margin: 0; color: #1a237e; font-size: 10px; font-weight: bold;">STUDENT IDENTITY CARD</p>
                    </div>

                    <!-- Main Content -->
                    <div style="display: flex; flex: 1; gap: 10px;">
                        <!-- Left Side - Photo and QR -->
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                            <!-- Student Photo -->
                            <div style="width: 70px; height: 80px; border: 2px solid #1a237e; border-radius: 4px; overflow: hidden; margin-bottom: 8px; background: white;">
                                <img src="${imageUrl}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='http://127.0.0.1:8000/uploads/logo/logo.png'">
                            </div>

                            <!-- QR Code -->
                            <div class="qr-code-container" style="width: 60px; height: 60px; background: white; padding: 3px; border: 1px solid #ddd; border-radius: 4px;">
                                <div class="qr-code" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></div>
                            </div>
                        </div>

                        <!-- Right Side - Details -->
                        <div style="flex: 2; display: flex; flex-direction: column; justify-content: space-between;">
                            <!-- Student Details -->
                            <div>
                                <!-- Student ID -->
                                <div style="margin-bottom: 4px;">
                                    <strong style="color: #1a237e; font-size: 10px; display: block;">STUDENT ID:</strong>
                                    <span style="font-size: 12px; font-weight: bold; color: #333;">${student.custom_id}</span>
                                </div>

                                <!-- Student Name -->
                                <div style="margin-bottom: 4px;">
                                    <strong style="color: #1a237e; font-size: 10px; display: block;">NAME:</strong>
                                    <span style="font-size: 11px; color: #333; font-weight: 500;">${student.fname} ${student.lname}</span>
                                </div>

                                <!-- Address -->
                                <div style="margin-bottom: 4px;">
                                    <strong style="color: #1a237e; font-size: 9px; display: block;">ADDRESS:</strong>
                                    <span style="font-size: 8px; color: #333; line-height: 1.2;">${student.address || 'No 87, Thonigala, Padavi Parakramapura'}</span>
                                </div>

                                <!-- Grade/Class -->
                                <div style="margin-bottom: 4px;">
                                    <strong style="color: #1a237e; font-size: 9px; display: block;">GRADE:</strong>
                                    <span style="font-size: 9px; color: #333;">${student.grade?.grade_name || 'N/A'}</span>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div style="border-top: 1px solid #1a237e; padding-top: 4px; margin-top: 5px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong style="color: #1a237e; font-size: 7px;">ISSUED DATE:</strong>
                                        <span style="font-size: 7px; color: #333;">${new Date().toISOString().split('T')[0]}</span>
                                    </div>
                                    <div>
                                        <strong style="color: #1a237e; font-size: 7px;">VALID UNTIL:</strong>
                                        <span style="font-size: 7px; color: #333;">${formattedDate}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Border -->
                    <div style="height: 6px; background: linear-gradient(90deg, #1a237e, #283593, #1a237e); margin-top: 5px; border-radius: 0 0 8px 8px;"></div>
                </div>
            `;

            return container;
        }

        // Generate single ID card
        async function generateSingleID(customId) {
            try {
                const student = allStudents.find(s => s.custom_id === customId);
                if (!student) {
                    alert('Student not found!');
                    return;
                }

                // Create temporary container
                const tempContainer = document.createElement('div');
                tempContainer.style.position = 'absolute';
                tempContainer.style.left = '-1000px';
                tempContainer.style.top = '0';
                document.body.appendChild(tempContainer);

                // Create ID card and append it properly
                const idCardElement = createIDCardHTML(student);
                tempContainer.appendChild(idCardElement);

                // Generate QR code
                const qrContainer = tempContainer.querySelector('.qr-code');
                if (qrContainer && typeof QRCode !== 'undefined') {
                    qrContainer.innerHTML = '';
                    try {
                        new QRCode(qrContainer, {
                            text: student.custom_id,
                            width: 54,
                            height: 54,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } catch (error) {
                        console.error('QR Code generation error:', error);
                        qrContainer.innerHTML = `<div style="text-align:center;font-size:6px;color:#000;width:100%;height:100%;display:flex;align-items:center;justify-content:center;">${student.custom_id}</div>`;
                    }
                }

                // Wait for images to load
                await new Promise(resolve => setTimeout(resolve, 800));

                const canvas = await html2canvas(tempContainer.firstChild, { // Use firstChild instead of tempContainer
                    scale: 3,
                    useCORS: true,
                    allowTaint: false,
                    backgroundColor: '#ffffff',
                    logging: false
                });

                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [85.6, 53.98]
                });

                pdf.addImage(imgData, 'PNG', 0, 0, 85.6, 53.98);
                pdf.save(`ID_${student.custom_id}_${student.fname}_${student.lname}.pdf`);

                // Clean up
                document.body.removeChild(tempContainer);

            } catch (error) {
                console.error('Error generating ID card:', error);
                alert('Failed to generate ID card: ' + error.message);
            }
        }


        // Bulk download all ID cards
        async function bulkDownloadAll() {
            try {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [85.6, 53.98]
                });

                // Show loading
                const downloadBtn = document.getElementById('bulkDownloadBtn');
                downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
                downloadBtn.disabled = true;

                let successCount = 0;

                for (let i = 0; i < filteredStudents.length; i++) {
                    const student = filteredStudents[i];

                    try {
                        // Create temporary container for ID card
                        const tempContainer = document.createElement('div');
                        tempContainer.style.position = 'absolute';
                        tempContainer.style.left = '-1000px';
                        tempContainer.style.top = '0';
                        document.body.appendChild(tempContainer);

                        // Create ID card
                        const idCardElement = createIDCardHTML(student);
                        tempContainer.appendChild(idCardElement);

                        // Generate QR code
                        const qrContainer = tempContainer.querySelector('.qr-code');
                        if (qrContainer && typeof QRCode !== 'undefined') {
                            qrContainer.innerHTML = '';
                            try {
                                new QRCode(qrContainer, {
                                    text: student.custom_id,
                                    width: 54,
                                    height: 54,
                                    colorDark: "#000000",
                                    colorLight: "#ffffff",
                                    correctLevel: QRCode.CorrectLevel.H
                                });
                            } catch (error) {
                                qrContainer.innerHTML = `<div style="text-align:center;font-size:6px;color:#000;width:100%;height:100%;display:flex;align-items:center;justify-content:center;">${student.custom_id}</div>`;
                            }
                        }

                        // Wait for images to load
                        await new Promise(resolve => setTimeout(resolve, 500));

                        const canvas = await html2canvas(tempContainer.firstChild, {
                            scale: 2,
                            useCORS: true,
                            allowTaint: false,
                            backgroundColor: '#ffffff',
                            logging: false
                        });

                        const imgData = canvas.toDataURL('image/png', 0.8);

                        if (successCount > 0) {
                            pdf.addPage();
                        }

                        pdf.addImage(imgData, 'PNG', 0, 0, 85.6, 53.98);
                        successCount++;

                    } catch (error) {
                        console.error(`Error generating card for ${student.custom_id}:`, error);
                    } finally {
                        // Clean up - remove tempContainer if it exists
                        if (tempContainer && tempContainer.parentNode) {
                            document.body.removeChild(tempContainer);
                        }
                    }

                    // Add small delay to prevent browser freezing
                    if (i % 2 === 0) {
                        await new Promise(resolve => setTimeout(resolve, 300));
                    }
                }

                if (successCount > 0) {
                    pdf.save(`Student_ID_Cards_Bulk_${new Date().toISOString().split('T')[0]}.pdf`);
                    alert(`Successfully generated ${successCount} ID cards!`);
                } else {
                    alert('No ID cards were generated. Please check the console for errors.');
                }

            } catch (error) {
                console.error('Error in bulk download:', error);
                alert('Failed to generate bulk download: ' + error.message);
            } finally {
                // Restore button state
                const downloadBtn = document.getElementById('bulkDownloadBtn');
                downloadBtn.innerHTML = '<i class="fas fa-download me-2"></i>Bulk Download All';
                downloadBtn.disabled = false;
            }
        }

        // Filter students
        function filterStudents() {
            const dateFilter = document.getElementById('filterDate').value;
            const searchFilter = document.getElementById('searchStudent').value.toLowerCase();

            filteredStudents = allStudents.filter(student => {
                const matchesDate = !dateFilter || true;
                const matchesSearch = !searchFilter ||
                    student.fname.toLowerCase().includes(searchFilter) ||
                    student.lname.toLowerCase().includes(searchFilter) ||
                    student.custom_id.toLowerCase().includes(searchFilter);

                return matchesDate && matchesSearch;
            });

            displayStudents();
        }

        // Initialize event listeners
        function initializeEventListeners() {
            document.getElementById('filterDate').addEventListener('change', filterStudents);
            document.getElementById('searchStudent').addEventListener('input', filterStudents);
            document.getElementById('bulkDownloadBtn').addEventListener('click', bulkDownloadAll);
            document.getElementById('clearFiltersBtn').addEventListener('click', clearFilters);
        }

        // Clear filters
        function clearFilters() {
            document.getElementById('filterDate').value = '';
            document.getElementById('searchStudent').value = '';
            filteredStudents = [...allStudents];
            displayStudents();
        }

        // UI State Management
        function showLoadingState() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('studentsList').style.display = 'none';
            document.getElementById('noStudentsMessage').style.display = 'none';
        }

        function showContentState() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }

        function showErrorState(message) {
            document.getElementById('loadingSpinner').style.display = 'none';
            alert('Error: ' + message);
        }
    </script>
@endpush