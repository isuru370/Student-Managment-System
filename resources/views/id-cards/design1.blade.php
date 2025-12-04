<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card</title>
    <style>
        .id-card {
            width: 85.6mm;
            height: 53.98mm;
            background: white;
            border: 2px solid #1a237e;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            font-family: Arial, sans-serif;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .background-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('http://127.0.0.1:8000/uploads/id/idcard_bg.png');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            height: 100%;
            padding: 8px;
            display: flex;
            flex-direction: column;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #1a237e;
        }

        .school-name {
            margin: 0;
            color: #1a237e;
            font-size: 16px;
            font-weight: bold;
        }

        .card-title {
            margin: 0;
            color: #1a237e;
            font-size: 10px;
            font-weight: bold;
        }

        .main-content {
            display: flex;
            flex: 1;
            gap: 8px;
        }

        .left-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .photo-container {
            width: 70px;
            height: 80px;
            border: 1px solid #1a237e;
            border-radius: 4px;
            overflow: hidden;
            background: white;
        }

        .student-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-id-box {
            width: 100%;
            text-align: center;
            padding: 3px;
            background: #1a237e;
            color: white;
            font-size: 11px;
            font-weight: bold;
            border-radius: 3px;
        }

        .right-section {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .student-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .detail-row {
            margin-bottom: 4px;
            display: flex;
        }

        .label {
            color: #1a237e;
            font-size: 9px;
            font-weight: bold;
            min-width: 50px;
        }

        .value {
            font-size: 9px;
            color: #333;
            flex: 1;
        }

        .student-name {
            font-size: 11px;
            font-weight: bold;
            color: #1a237e;
        }

        .address {
            font-size: 8px;
            color: #333;
            line-height: 1.2;
        }

        .footer {
            border-top: 1px solid #1a237e;
            padding-top: 3px;
            margin-top: 3px;
        }

        .date-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .date-label {
            color: #1a237e;
            font-size: 7px;
            font-weight: bold;
        }

        .date-value {
            font-size: 7px;
            color: #333;
        }

        .bottom-border {
            height: 6px;
            background: linear-gradient(90deg, #1a237e, #283593, #1a237e);
            margin-top: 5px;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <!-- Background Pattern -->
        <div class="background-pattern"></div>
        
        <!-- Main Content -->
        <div class="content">

            <!-- Main Content Area -->
            <div class="main-content">
                <!-- Left Side - Photo & ID -->
                <div class="left-section">
                    <div class="photo-container">
                        <img src="http://127.0.0.1:8000/uploads/logo/logo.png" 
                             alt="Student Photo" 
                             class="student-photo"
                             onerror="this.src='http://127.0.0.1:8000/uploads/logo/logo.png'">
                    </div>
                    
                    <div class="student-id-box">
                        SA01001
                    </div>
                </div>

                <!-- Right Side - Details -->
                <div class="right-section">
                    <!-- Student Information -->
                    <div class="student-details">
                        <div class="detail-row">
                            <span class="label">NAME:</span>
                            <span class="value student-name">W. Sitheksha Uththarangi</span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="label">ADDRESS:</span>
                            <span class="value address">No 87, Thonigala, Padavi Parakramapura</span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="label">GRADE:</span>
                            <span class="value">Grade 10</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Border -->
            <div class="bottom-border"></div>
        </div>
    </div>
</body>
</html>