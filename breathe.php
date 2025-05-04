<?php
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use session if available
} elseif (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
} else {
    $username = "Guest"; // Fallback for anonymous access
}



// Check if the user is logged out, then destroy session and redirect
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_unset();
  session_destroy();
  setcookie("username", "", time() - 3600, "/"); // Optional: Delete the cookie
  header("Location: loginform.php"); // Redirect to login page
  exit();
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Breathing Exercise</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('beachwaves.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Add dimming overlay */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Dimming effect */
            z-index: 1;
            overflow: hidden; /* Ensure particles stay within overlay */
        }

        /* Particle styles */
        .particle {
            position: absolute;
            width: 8px;
            height: 10px;
            background-color: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            opacity: 1;
            animation: particle-animation 5s infinite ease-in-out;
        }

        @keyframes particle-animation {
            0% {
                transform: translate(0, 0) scale(0.5);
                opacity: 0.7;
            }
            50% {
                transform: translate(200px, 200px) scale(1);
                opacity: 0.9;
            }
            100% {
                transform: translate(0, 0) scale(0.5);
                opacity: 0;
            }
        }

        /* General Body Styles */
        h1 {
            font-size: 3rem;
            margin-top: 0px;
            color: #fff;
            text-shadow: 0px 0px 5px rgba(255, 255, 255, 0.7);
            z-index: 2;
            position: relative;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            margin-top: 40px;
        }

        .circle-container {
            width: 290px;
            height: 290px;
            position: relative;
            margin-bottom: 40px;
        }

        .progress-ring {
            position: absolute;
            width: 280px;
            height: 280px;
            border: 10px solid transparent;
            border-top: 10px solid #ffffff;
            border-radius: 50%;
            transform-origin: center;
            transform: rotate(0deg);
            transition: transform 1s linear;
        }

        .circle {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.7);
            animation: pulse 8s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }

        .instructions {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
        }

        .timer {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            font-weight: bold;
            color: #ffffff;
        }

        /* Buttons */
        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        button {
            padding: 10px 20px;
            font-size: 1.2rem;
            background-color: rgba(255, 255, 255, 0.8);
            color: #333;
            border: 2px solid #ffffff;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: rgba(255, 255, 255, 1);
            transform: scale(1.05);
        }

        /* Toggle Menu Button */
        .toggle-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.7);
            color: #333;
            padding: 10px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            z-index: 3;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .toggle-btn:hover {
            background-color: rgba(255, 255, 255, 1);
            transform: scale(1.1);
        }

        /* Side Menu Styles */
             /* Side Menu Styles */
.side-menu {
    position: fixed;
    top: 0;
    left: -300px; /* Start off-screen */
    width: 250px;
    height: 100%;
    background-color: rgba(255, 200, 150, 0.7); /* Peach color with transparency */
    color: #fff;
    padding: 20px;
    transition: left 0.3s ease; /* Smooth transition when opening/closing */
    z-index: 2;
    display: flex;
    flex-direction: column;
    justify-content: flex-start; /* Align items to the top */
}

        /* Menu List Styles */
        .side-menu ul {
            padding: 0;
            margin: 0;
            list-style-type: none; /* Remove bullet points */
            text-align: center; /* Center the list items */
        }

       /* Menu Item Styles */
.side-menu a {
    color: #fff;
    text-decoration: none;
    font-size: 1.3rem;
    display: block;
    margin: -1px 0; /* Reduced margin to bring items closer */
    margin-left: 20px;
    padding: 8px 15px; /* Adjusted padding for a more compact appearance */
    border-radius: 20px;
    transition: all 0.3s ease;
}

       /* Hover Effect for Menu Items */
.side-menu a:hover {
    background-color: white;
    color: rgba(255, 200, 150, 0.7); /* Peach-colored text on hover */
    transform: scale(1.05); /* Make items "pop" on hover */
}

/* Show the side menu when active */
.side-menu.active {
    left: 0; /* Slide in */
}


       
h2 {
            color: white;
        }


         /* Log Out Button Styles */
.logout-btn {
    background-color: white; /* White background for the button */
    color: rgba(255, 150, 100, 0.8); /* Darker peach color for the text */
    font-size: 1.5rem;
    padding: 12px 50px; /* Adjusted padding for better button size */
    border: none;
    border-radius: 20px;
    cursor: pointer;
    margin-top: 20px; /* Space above the Log Out button */
    margin-bottom: 200px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Hover Effect for Log Out Button */
.logout-btn:hover {
    background-color: rgba(255, 200, 150, 0.8); /* Darker peach background on hover */
    transform: scale(1.05); /* Button expands slightly on hover */
}

        /* Toggle Button Styles */
        .toggle-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.7);
            color: #333;
            padding: 10px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            z-index: 3;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .toggle-btn:hover {
            background-color: rgba(255, 255, 255, 1);
            transform: scale(1.1);
        }




    </style>
</head>

<body>
    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggle-btn">☰</button>

    <!-- Side Menu -->
    <div class="side-menu" id="side-menu">
    <h2>Zen Zone</h2>
  
    <a href="index.php">Home</a>
<a href="userprofile.php">Profile</a>
<a href="journal_final/journal.php">Journal</a>
<a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a>

<a href="subscriptions/doctor.php">Donations</a>
<a href="reflection/weeklyreflectionform.php">Weekly Reflection</a>
<a href="contacts/contacts_index.php">Emergency Contact</a>

<br><br><br><br><br><br><br><br>

    <!-- Log Out Button -->
    <button class="logout-btn" onclick="confirmLogout()">Log Out</button>

</div>


    <div class="container">
        <h1>Relax and Breathe</h1>

        <!-- Breathing Circle -->
        <div class="circle-container">
            <div class="circle"></div>
            <div class="progress-ring" id="progress-ring"></div>
            <div class="instructions" id="instructions">Press Start</div>
            <div class="timer" id="timer">4</div>
        </div>

        <!-- Buttons -->
        <div class="buttons">
            <button id="start-button">Start</button>
            <button id="stop-button">Stop</button>
        </div>
    </div>

    <script>
    const instructions = document.getElementById("instructions");
    const timerDisplay = document.getElementById("timer");
    const startButton = document.getElementById("start-button");
    const stopButton = document.getElementById("stop-button");
    const progressRing = document.getElementById("progress-ring");
    const toggleBtn = document.getElementById("toggle-btn");
    const sideMenu = document.getElementById("side-menu");

    let timer = 4;
    let isInhaling = true;
    let isHolding = false;
    let isExhaling = false;
    let interval;
    let ringRotation = 0;

    // Toggle the side menu visibility
    toggleBtn.addEventListener("click", () => {
        sideMenu.classList.toggle("active");
    });

    // Close the menu when clicking anywhere outside of it
    document.addEventListener("click", (event) => {
        // Check if the click was outside the side menu and toggle button
        if (!sideMenu.contains(event.target) && event.target !== toggleBtn) {
            sideMenu.classList.remove("active");
        }
    });

    // Function to create a particle effect
    function createParticle() {
        const particle = document.createElement("div");
        particle.classList.add("particle");
        document.body.appendChild(particle);

        // Randomly position particle and animate it
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100}%`;

        // Remove particle after animation ends
        setTimeout(() => {
            particle.remove();
        }, 5000); // Particle duration matches animation duration
    }

    // Create particles continuously
    setInterval(createParticle, 200); // Adjust the interval to control particle density

    // Function to update the breathing instructions and timer
    function updateBreathing() {
        if (timer === 0) {
            if (isInhaling) {
                isInhaling = false;
                isHolding = true;
                timer = 3; // Set timer for hold breath
                instructions.textContent = "Hold...";
            } else if (isHolding) {
                isHolding = false;
                isExhaling = true;
                instructions.textContent = "Exhale...";
                timer = 4; // Set timer for exhale
            } else if (isExhaling) {
                isExhaling = false;
                setTimeout(() => {
                    isInhaling = true;
                    instructions.textContent = "Inhale...";
                    timer = 4;
                }, 1000);
                return;
            }
        }

        timerDisplay.textContent = timer;
        timer--;

        // Update the ring's rotation
        ringRotation += 360 / (isInhaling ? 4 : isHolding ? 4 : 6);
        progressRing.style.transform = `rotate(${ringRotation}deg)`;
    }

    // Start the breathing exercise
    function startExercise() {
        clearInterval(interval);
        timer = 4;
        isInhaling = true;
        isHolding = false;
        isExhaling = false;
        instructions.textContent = "Inhale...";
        timerDisplay.textContent = timer;
        ringRotation = 0;
        progressRing.style.transform = `rotate(0deg)`;

        interval = setInterval(updateBreathing, 1000);
    }

    // Stop the breathing exercise and refresh the page
    function stopExercise() {
        clearInterval(interval);
        instructions.textContent = "Press Start";
        ringRotation = 0;
        progressRing.style.transform = "rotate(0deg)";
        timerDisplay.textContent = "--";

        window.location.reload();
    }

    startButton.addEventListener("click", startExercise);
    stopButton.addEventListener("click", stopExercise);

    function confirmLogout() {
    const confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {
        // Redirect to the log-out page or perform your logout logic here
        window.location.href = "loginform.php"; // Change this to your logout URL
    }
}

</script>

</body>

</html>
