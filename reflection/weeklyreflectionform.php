<?php
session_start();

// Track the user's last login time
if (isset($_SESSION['last_login'])) {
    $last_login = $_SESSION['last_login'];
} else {
    $last_login = "No record"; // Default if no login record exists
}

// Update last login time for the current session
$_SESSION['last_login'] = date('Y-m-d H:i:s');

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

// Database connection
$host = 'localhost'; // Update with your database host
$user = 'root'; // Update with your database username
$password = '@MentalH2024'; // Update with your database password
$dbname = 'mentalhealthapp'; // Update with your database name

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the start of this week (Monday)
    $week_start_date = date('Y-m-d', strtotime('monday this week'));

    // Get reflection input
    $question_1 = $conn->real_escape_string($_POST['question_1']);
    $question_2 = $conn->real_escape_string($_POST['question_2']);
    $question_3 = $conn->real_escape_string($_POST['question_3']);

    // Check if a reflection already exists for this week
    $sql_check = "SELECT * FROM weekly_reflections WHERE username = '$username' AND week_start_date = '$week_start_date'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // Determine next available day for reflection
        $current_day = date('l'); // Get the current day name

        if (in_array($current_day, ["Monday", "Tuesday", "Wednesday", "Thursday"])) {
            $next_available_date = "this Friday";
        } else {
            // If already Friday-Sunday, the next available is next week's Friday
            $next_available_date = "next Friday";
        }

        $message = "You have already submitted a reflection for this week. Please return on <strong>" . $next_available_date . "</strong> to submit your next reflection.";
    } else {
        // Insert the new reflection
        $sql_insert = "INSERT INTO weekly_reflections (username, week_start_date, question_1, question_2, question_3) 
                       VALUES ('$username', '$week_start_date', '$question_1', '$question_2', '$question_3')";

        if ($conn->query($sql_insert) === TRUE) {
            $message = "Your reflection has been saved successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Determine what to display (form or report)
$view = isset($_GET['view']) ? $_GET['view'] : 'form'; // Default to 'form'

// Log session details for debugging or analytics
error_log("User $username accessed the application on " . date('Y-m-d H:i:s'));
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Reflection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            background: linear-gradient(60deg, #f0e4b0 0%, #f7f0d1 100%);
        }
        h2 {
            color: white;
        }
        button {
            background-color: #FF9966; /* Peach color */
            color: white; /* Text color */
            border: none; /* Remove default border */
            border-radius: 25px; /* Round the corners */
            padding: 12px 24px; /* Add some padding for size */
            font-size: 16px; /* Adjust text size */
            cursor: pointer; /* Change cursor to pointer */
            transition: all 0.3s ease; /* Smooth transition for hover effects */
        }
        
        button:hover {
            background-color: #FF9966; /* Darker peach on hover */
            transform: scale(1.05); /* Slightly enlarge the button */
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1); /* Add subtle shadow */
        }


        /* Style for labels */
        label {
            font-size: 1.2em;  /* Makes the font a bit larger */
            font-weight: bold; /* Makes the font bold */
            color: #333;       /* Darker text for readability */
            display: block;    /* Ensures the label is on its own line */
            
        }


        textarea {
            width: 50%;
            padding: 10px;
        }


        @media print {
            button, .no-print {
                display: none !important;
            }
        }

       


        .reportimage {
            width: 200px;
            height: 200px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            object-fit: cover;
        }

        .week-box {
        background-color: #FFDAB9; /* Peach background */
        color: #000; /* Black text */
        border: 1px solid #E59866; /* Slightly darker peach border */
        padding: 10px;
        font-size: 1.2em;
        border-radius: 8px; /* Rounded corners */
        width: fit-content; /* Adjust box size to content */
        margin-bottom: 10px; /* Add spacing below */
    }

    .button-container {
    margin: 20px auto;
}

.reflec{
    color: #4f4d4e;
}

.filter-button {
    background-color: #FFDAB9; /* Peach color */
    color: #2c3e50; /* Dark text color */
    text-decoration: none;
    padding: 12px 20px;
    margin: 5px;
    border: 2px solid #E59866; /* Slightly darker border */
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease; /* Smooth hover effect */
}

.filter-button:hover {
    background-color: #E59866; /* Darker peach */
    color: white; /* Light text color for contrast */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
    transform: scale(1.05); /* Slight zoom effect */
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
.side-menu {
    position: fixed;
    top: 0;
    left: -300px; /* Start off-screen */
    width: 250px;
    height: 100%;
    background-color: rgba(255, 170, 120, 0.9); /* Slightly darker peach */
    color: #ff9966;
    padding: 20px;
    transition: left 0.3s ease; /* Smooth transition when opening/closing */
    z-index: 2;
    display: flex;
    flex-direction: column;
    justify-content: flex-start; /* Align items to the top */
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
    color: rgba(255, 200, 150, 1); /* Peach-colored text on hover */
    transform: scale(1.05); /* Make items "pop" on hover */
}

/* Show the side menu when active */
.side-menu.active {
    left: 0; /* Slide in */
}

footer {
            background-color: #0e5066;
            color: white;
            text-align: center;
            padding: 10px;
            width: 100%;
        }

.footer-content {
  max-width: 1000px;
  margin: 0 auto; /* Center the content */
}

.social-media {
  margin-bottom: 5px; /* Reduced margin */
}

.social-link {
            margin: 0 8px;
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

.social-link:hover {
  color: grey; /* Change color on hover */
}

.contact-info p {
  margin: 3px 0; /* Reduced space between paragraphs */
}

.contact-info a {
  color: #333; /* Link color */
  text-decoration: none; /* Remove underline */
}

.contact-info a:hover {
  text-decoration: underline; /* Underline on hover */
}

.report-only {
  display: none;
}

body.report-page .report-only {
  display: block;  /* Show the content on the report page */
}

/* Style for the submit button */
input[type="submit"] {
    background-color: #FF9966; /* Peach color */
    color: white; /* Text color */
    border: none; /* Remove default border */
    border-radius: 25px; /* Round the corners */
    padding: 12px 24px; /* Add some padding for size */
    font-size: 16px; /* Adjust text size */
    cursor: pointer; /* Change cursor to pointer */
    transition: all 0.3s ease; /* Smooth transition for hover effects */
}

/* Hover effect */
input[type="submit"]:hover {
    background-color: #FF9966; /* Darker peach on hover */
    transform: scale(1.05); /* Slightly enlarge the button */
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1); /* Add subtle shadow */
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

    </style>
</head>
<body class="report-page">

<!-- Toggle Button -->
<button class="toggle-btn" id="toggle-btn">☰</button>

<!-- Side Menu -->
<div class="side-menu" id="side-menu">
    <center>
    <h2>Weekly Reflection</h2>

    <a href="../index.php">Home</a>
    <a href="../userprofile.php">Profile</a>
    <a href="../journal_final/journal.php">Journal</a>
    <a href="../breathe.php">Zen Zone</a>
    <a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a>
   
    <a href="../subscriptions/doctor.php">Donations</a>
    <a href="../contacts/contacts_index.php">Emergency Contact</a>

    <br><br>
    <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
</center>
    
    <hr>
</div>



<?php if ($view === 'form'): ?>
    <header style="text-align: center; margin-bottom: 20px;">
    <img src="../images/moodifylogo.jpg" alt="Moodify Logo" style="width: 100px; height: auto;" class="reportimage">
    <h1>Moodify Weekly Reflection Report</h1>

    <a href="?view=form"><button style="position: absolute; top: 20px; right: 305px;">Fill Reflection</button></a>
    <a href="?view=report"><button style="position: absolute; top: 20px; right: 160px;">View Report</button></a>
    <button style="position: absolute; top: 20px; right: 20px;" onclick="window.print()">Print Report</button>
</header>

<center>
<?php if (isset($message)): ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

</center>

    <hr>
            


            <form action="" method="POST">

            
                <center>
                    <label for="question_1">What went well this week?</label><br>
                    <textarea id="question_1" name="question_1" rows="4" required></textarea><br><br>

                    <label for="question_2">What challenges did you face?</label><br>
                    <textarea id="question_2" name="question_2" rows="4" required></textarea><br><br>

                    <label for="question_3">What’s one thing you want to improve next week?</label><br>
                    <textarea id="question_3" name="question_3" rows="4" required></textarea><br><br>

                    <input type="submit" value="Submit Reflection">
                    <br><br>

                </center>
            </form>

       <?php endif; ?>

    <?php if ($view === 'report'): ?>
        <header style="text-align: center; margin-bottom: 20px;">
            <img src="../images/moodifylogo.jpg" alt="Moodify Logo" style="width: 100px; height: auto;" class="reportimage">
            <h1>Moodify Weekly Reflection Report</h1>

            <a href="?view=form"><button style="position: absolute; top: 20px; right: 305px;">Fill Reflection</button></a>
            <a href="?view=report"><button style="position: absolute; top: 20px; right: 160px;">View Report</button></a>
            <button style="position: absolute; top: 20px; right: 20px;" onclick="window.print()">Print Report</button>
   
    
       
        </header>
        <hr>

       <!-- Filter Section -->
        <div class="button-container no-print" style="text-align: center; margin-bottom: 20px;">
            <a href="?view=report&filter=this_month" class="filter-button">This Month</a>
            <a href="?view=report&filter=last_3_months" class="filter-button">Last 3 Months</a>
            <a href="?view=report&filter=last_6_months" class="filter-button">Last 6 Months</a>
            <a href="?view=report&filter=all" class="filter-button">All Time</a>
        </div>

        <!-- Horizontal Line -->
        <hr class="no-print">

        <h2 class="reflec" >Your Weekly Reflections</h2>
        <hr>

       <!-- Filter Section -->



        <?php

        // Fetch reflections with filtering
$date_condition = ""; // Default: no filtering

if (isset($_GET['filter'])) {
    $current_date = date('Y-m-d');
    switch ($_GET['filter']) {
        case 'this_month':
            $start_date = date('Y-m-01'); // First day of this month
            $end_date = $current_date; // Current date
            $date_condition = "AND week_start_date BETWEEN '$start_date' AND '$end_date'";
            break;

        case 'last_3_months':
            $start_date = date('Y-m-d', strtotime('-3 months'));
            $date_condition = "AND week_start_date >= '$start_date'";
            break;

        case 'last_6_months':
            $start_date = date('Y-m-d', strtotime('-6 months'));
            $date_condition = "AND week_start_date >= '$start_date'";
            break;

        case 'all':
        default:
            $date_condition = ""; // No filtering
    }
}

        // Update the SQL query to include the filter
        $sql = "SELECT week_start_date, question_1, question_2, question_3 
        FROM weekly_reflections 
        WHERE username = '$username' $date_condition
        ORDER BY week_start_date DESC";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='week-box'>Week Starting: " . $row['week_start_date'] . "</div>";
                echo "<p><strong>What went well this week?</strong><br>" . htmlspecialchars($row['question_1']) . "</p>";
                echo "<p><strong>What challenges did you face?</strong><br>" . htmlspecialchars($row['question_2']) . "</p>";
                echo "<p><strong>What’s one thing you want to improve next week?</strong><br>" . htmlspecialchars($row['question_3']) . "</p>";
                echo "<hr>";
            }
        } else {
            echo "<p>No reflections found for the selected date range.</p>";
        }
        ?>

        
    <?php endif; ?>

    <script>

        if (window.location.pathname === '/report.php') {
  document.body.classList.add('report-page');
}


       // JavaScript for toggling the side menu
        document.getElementById("toggle-btn").addEventListener("click", function() {
            document.getElementById("side-menu").classList.toggle("active");
        });

        // Close the menu if clicked outside
        document.addEventListener("click", function(event) {
            var sideMenu = document.getElementById("side-menu");
            var toggleBtn = document.getElementById("toggle-btn");
            
            // If the click was outside the menu and toggle button, close the menu
            if (!sideMenu.contains(event.target) && event.target !== toggleBtn) {
                sideMenu.classList.remove("active");
            }
        });

        
    
        function confirmLogout() {
    const confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {
        // Redirect to the log-out page or perform your logout logic here
        window.location.href = "../loginform.php"; // Change this to your logout URL
    }
}

    </script>

<footer class="footer">
  <div class="footer-content">
    <!-- Social Media Section -->
    <div class="social-media no-print">
      <p>Follow us on:</p>
      <a href="https://www.instagram.com/moodifysa/profilecard/?igsh=aXp6ejFjcHF2Z2E3" class="social-link">Instagram</a>
    </div>

    <!-- Moodify tagline and contact info (for Report page only) -->
    <div class="report-only">
      <center>
        <p><strong>Moodify</strong> - Helping you track your mental health journey.</p>
        <p>Contact us at <a href="mailto:moodifysa@gmail.com">moodifysa@gmail.com</a></p>
      
      <p>© 2024 Moodify. All rights reserved.</p>

      </center>
    </div>

    
  </div>
</footer>


</body>
</html>

<?php
$conn->close();
?>