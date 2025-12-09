<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ID Card</title>

<style>
    body {
        font-family: 'Times New Roman', serif;
        background: #eee;
        padding: 20px;
    }

    /* Standard ID Card Size (CR80 - 1011px × 637px at 300DPI) */
    .id-card {
        width: 1011px;
        height: 637px;
        background: url('http://127.0.0.1:8000/uploads/id/idcard_bg.png');
        background-size: cover;
        background-position: center;
        border-radius: 12px;
        display: flex;
        padding: 25px 35px;
        justify-content: space-between;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    /* Left Section */
    .left-section {
        width: 60%;
    }

    .profile-box {
        width: 200px;
        height: 240px;
        border: 1px solid #ccc;
        border-radius: 5px;
        overflow: hidden;
        background: #fff;
    }

    .profile-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .info {
        margin-top: 25px;
    }

    .student-id {
        font-size: 40px;
        margin: 0;
        font-weight: bold;
    }

    .student-name {
        font-size: 30px;
        margin: 10px 0;
        font-weight: normal;
    }

    .address {
        font-size: 22px;
        margin-top: 5px;
    }

    /* Right Section */
    .right-section {
        width: 30%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .qr-img {
        width: 200px;
        height: 200px;
        background: #fff;
        padding: 10px;
        border-radius: 8px;
    }

    .logo {
        width: 140px;
        margin-top: auto;
    }
</style>

</head>
<body>

<div class="id-card">

    <div class="left-section">
        <div class="profile-box">
            <img src="https://admin.succesedu.com/uploads/1739686810.jpg" alt="profile" class="profile-img">
        </div>

        <div class="info">
            <h2 class="student-id">SA09123</h2>
            <h3 class="student-name">H.M Chathurvi Punnada Herath</h3>
            <p class="address">No 212/B, Padavi Parakramapura</p>
        </div>
    </div>

    <div class="right-section">
        <!-- Auto QR Code for SA09123 -->
        <img 
            src="https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=SA09123" 
            alt="QR Code" 
            class="qr-img"
        >

        <img src="uploads/logo/logo.png" alt="Logo" class="logo">
    </div>

</div>

</body>
</html>
