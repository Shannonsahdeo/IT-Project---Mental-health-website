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
    <title>Your Journal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General styles */
        html, body {
            height: 100%; 
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background: url('background2.jpg') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        .welcome {
            text-align: center;
            padding: 50px 20px;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 80%;
            margin-top: 50px;
        }

        h1 {
            color: #a7c7e7;
            font-size: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        p {
            font-size: 20px;
            margin: 20px 0;
            color: #f4e1d2;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }

        .button {
            background-color: #a7c7e7;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            text-decoration: none;
            border-radius: 8px;
            transition: transform 0.2s ease, background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .button:hover {
            background-color: #85aad6;
            transform: translateY(-4px);
        }
		
        /* Menu styles */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar {
            padding: 10px;
        }


        .content {
            transition: margin-left 0.3s ease;
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
    font-size: 1.2rem;
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
    <header>
        <nav class="navbar">
            <button class="toggle-btn">☰</button>
            <div class="side-menu">
              <center>  <h2>Journal</h2></center>
                
                <ul class="vertical-menu">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../userprofile.php">Profile</a></li>
                <li><a href="../breathe.php">Zen Zone</a></li>
                <li><a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a></li>
             
                <li><a href="../subscriptions/doctor.php">Donations</a></li>
                <li><a href="../reflection/weeklyreflectionform.php">Weekly Reflection</a></li>
                <li><a href="../contacts/contacts_index.php">Emergency Contact</a></li>

                <br><br><br>

                <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
                </ul>

            </div>
        </nav>
    </header>

    <div class="content">
        <center>
            <div class="welcome">
                <h1>Welcome to Your Journal</h1>
                <p>Track your thoughts, emotions, and experiences over time. Journaling helps you reflect and grow. Take a moment to explore.</p>
            </div>
        </center>

        <div class="buttons">
            
            <a href="write_entry.php" class="button">Write a New Entry</a>
            <a href="read_entry.php" class="button">Read Previous Entries</a>
            <a href="custom.php" class="button">Customise your Journal</a>
        </div>
    </div>

    <script>
       // Get the button and the side menu
    const menuToggle = document.querySelector('.toggle-btn');
    const sideMenu = document.querySelector('.side-menu');

    // Toggle the side menu display when the button is clicked
    menuToggle.onclick = function() {
        sideMenu.classList.toggle('active'); // Add or remove the 'active' class to slide in/out
    };

    // Optional: Close the menu if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.toggle-btn') && !event.target.closest('.side-menu')) {
            sideMenu.classList.remove('active');
        }
    };

    function confirmLogout() {
    const confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {
        // Redirect to the log-out page or perform your logout logic here
        window.location.href = "../loginform.php"; // Change this to your logout URL
    }
}
    </script>
</body>
</html>
