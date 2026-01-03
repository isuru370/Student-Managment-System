<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            width: 80mm;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .academy-name {
            font-size: 14px;
            margin: 2px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .total {
            font-weight: bold;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="academy-name">SAVIDYA EDUCATION</div>
        <div>PAYMENT RECEIPT</div>
    </div>
    
    <div class="divider"></div>
    
    <div class="row">
        <span><strong>Receipt No:</strong></span>
        <span>#{{ $data['id'] }}</span>
    </div>
    
    <div class="row">
        <span><strong>Date:</strong></span>
        <span>{{ date('d/m/Y', strtotime($data['payment_date'])) }}</span>
    </div>
    
    <div class="divider"></div>
    
    <div class="row">
        <span><strong>Student:</strong></span>
        <span>{{ $data['student']['fname'] }} {{ $data['student']['lname'] }}</span>
    </div>
    
    <div class="row">
        <span><strong>Student ID:</strong></span>
        <span>{{ $data['student']['custom_id'] }}</span>
    </div>
    
    <div class="divider"></div>
    
    <div class="row">
        <span><strong>Class:</strong></span>
        <span>{{ $data['student_class']['class_name'] }}</span>
    </div>
    
    <div class="row">
        <span><strong>Grade:</strong></span>
        <span>{{ $data['student_class']['grade'] }}</span>
    </div>
    
    <div class="row">
        <span><strong>Subject:</strong></span>
        <span>{{ $data['student_class']['subject'] }}</span>
    </div>
    
    <div class="row">
        <span><strong>Category:</strong></span>
        <span>{{ $data['class_category_has_student_class']['class_category']['category_name'] }}</span>
    </div>
    
    <div class="row">
        <span><strong>Payment For:</strong></span>
        <span>{{ $data['payment_for'] }}</span>
    </div>
    
    <div class="divider"></div>
    
    <div class="row">
        <span>Class Fees:</span>
        <span>Rs. {{ number_format($data['class_category_has_student_class']['fees'], 2) }}</span>
    </div>
    
    <div class="row">
        <span>Hall Charges:</span>
        <span>Rs. {{ number_format($data['hall_price'], 2) }}</span>
    </div>
    
    <div class="divider"></div>
    
    <div class="row total">
        <span>TOTAL:</span>
        <span>Rs. {{ number_format($data['total'], 2) }}</span>
    </div>
    
    <div class="divider"></div>
    
    <div class="footer">
        <p>Generated: {{ $date }}</p>
        <p>** Computer Generated **</p>
        <p>Thank you for your payment!</p>
    </div>
</body>
</html>