<?php

session_start();

// Check if the session contains the 'username' or use cookies if session doesn't exist
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use session if available
} elseif (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
} else {
    $username = "Guest"; // Fallback for anonymous access
}
?>


<!-- loadingpage.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Background styling */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f4f1de;
            font-family: Arial, sans-serif;
            overflow: hidden;
            color: #555;
        }

        /* Pastel circles in the background */
        .background-circles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
            opacity: 0.7;
        }

        /* Different pastel colors for circles */
        .circle:nth-child(1) { background: #ffadad; width: 180px; height: 180px; top: 20%; left: 15%; }
        .circle:nth-child(2) { background: #ffd6a5; width: 120px; height: 120px; top: 65%; left: 75%; }
        .circle:nth-child(3) { background: #caffbf; width: 250px; height: 250px; top: 50%; left: 40%; }
        .circle:nth-child(4) { background: #9bf6ff; width: 100px; height: 100px; top: 75%; left: 25%; }
        .circle:nth-child(5) { background: #bdb2ff; width: 200px; height: 200px; top: 35%; left: 65%; }

        /* Animation for floating circles */
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            50% {
                transform: translateY(-30px) translateX(-30px);
            }
        }

        /* Loading bar container */
        .loading-container {
            text-align: center;
            max-width: 600px;
            width: 80%;
        }

        /* Progress bar styling */
        .progress-bar {
            width: 100%;
            height: 25px;
            background-color: rgba(255, 255, 255, 0.4);
            border-radius: 15px;
            overflow: hidden;
            margin-top: 30px;
        }

        /* Animated loading bar */
        .progress {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, #ffadad, #ffd6a5);
            border-radius: 15px;
            transition: width 1.5s;
        }

        /* Text styling */
        .percentage {
            font-size: 20px;
            margin-top: 15px;
        }

        .loading-text {
            font-size: 24px;
            font-weight: bold;
            color: #555;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Floating circles background -->
    <div class="background-circles">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <!-- Loading bar container -->
    <div class="loading-container">
        <div class="loading-text">Logging in, please wait...</div>
        <div class="progress-bar">
            <div class="progress" id="progress-bar"></div>
        </div>
        <div class="percentage" id="percentage">0%</div>
    </div>

    <script>
        let percentage = 0;
        const progressBar = document.getElementById('progress-bar');
        const percentageText = document.getElementById('percentage');

        function updateProgress() {
            percentage += 20; // Increase by 20% each time
            progressBar.style.width = percentage + '%';
            percentageText.textContent = percentage + '%';

            // Check if loading is complete
            if (percentage >= 100) {
                // Redirect to index.php after reaching 100%
                window.location.href = 'index.php';
            } else {
                // Continue updating every 1.5 seconds for a slower effect
                setTimeout(updateProgress, 1500);
            }
        }

        // Start updating progress
        updateProgress();
    </script>
</body>
</html>
