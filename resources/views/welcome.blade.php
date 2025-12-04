<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Success Academy</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        .container {
            text-align: center;
            z-index: 10;
            max-width: 800px;
            padding: 2rem;
        }
        
        .logo-container {
            margin-bottom: 2rem;
            animation: fadeInDown 1s ease-out;
        }
        
        .logo {
            max-width: 200px;
            height: auto;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
        }
        
        h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            animation: fadeIn 1.2s ease-out 0.3s both;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .accent {
            color: #4ade80;
        }
        
        .tagline {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            font-weight: 300;
            animation: fadeIn 1.2s ease-out 0.6s both;
            opacity: 0.9;
        }
        
        .redirect-message {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            animation: fadeIn 1s ease-out 1s both;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .countdown {
            font-weight: bold;
            color: #4ade80;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 100px;
            height: 100px;
            top: 60%;
            left: 80%;
            animation-delay: 1s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(4) {
            width: 120px;
            height: 120px;
            top: 30%;
            left: 70%;
            animation-delay: 3s;
        }
        
        .shape:nth-child(5) {
            width: 50px;
            height: 50px;
            top: 10%;
            left: 50%;
            animation-delay: 4s;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }
            
            .tagline {
                font-size: 1.2rem;
            }
            
            .logo {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="container">
        <div class="logo-container">
            <img src="http://127.0.0.1:8000/uploads/logo/logo.png" alt="Success Academy Logo" class="logo">
        </div>
        
        <h1>Welcome to <span class="accent">Success Academy</span></h1>
        <p class="tagline">Empowering minds, shaping futures through excellence in education</p>
        
        <div class="redirect-message">
            <p>You will be redirected to the login page in <span class="countdown" id="countdown">5</span> seconds</p>
        </div>
    </div>

    <script>
        // Countdown and redirect functionality
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                // Redirect to login page - replace with your actual login route
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    </script>
</body>
</html>