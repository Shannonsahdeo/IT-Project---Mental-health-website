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

$profile_pic = 'default_profile.png'; // Default profile picture

if ($username !== "Guest") {
    // Fetch profile picture for logged-in user
    $stmt = $conn->prepare("SELECT profile_pic FROM userloginreg WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $profile_pic = $row['profile_pic'] ?: $profile_pic; // Use default if no profile picture
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Welcome to Moodify</title>

<link rel="stylesheet" href="styles.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>



.logout-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 20px;
    border-radius: 10px;
    display: none;  /* Hidden by default */
    text-align: center;
    font-size: 18px;
}


.task-btn {
    padding: 14px 50px;
    background-color: #00796b; /* Matches task item background */
    color: white; /* Task item text color */
    border: none;
    border-radius: 10px;
    cursor: pointer;
    margin: 5px;
    font-family: 'Poppins', Arial, sans-serif; /* Clean font style */
    font-size: 16px;
    font-weight: bold;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.task-btn:hover {
    background-color: #b2ebf2; /* Slightly darker for hover */
    transform: scale(1.05);
}

.task-btn.active {
    background-color: #b2ebf2; /* Same as hover to indicate active state */
    color: #004d40; /* Slightly darker text color for active button */
}
/* Task item styling */
.task-item {
    background: #e0f7fa;
    padding: 16px;
    margin: 10px 0;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.task-item:hover {
    background: #b2ebf2;
    transform: translateY(-5px);
}

.task-header {
    font-size: 18px;
    font-weight: bold;
    color: #00796b;
    display: flex;
    justify-content: space-between;
}

.task-description {
    font-size: 16px;
    color: #004d40;
    padding-top: 10px;
    display: none;
    overflow: hidden;
    transition: max-height 0.5s ease-out, opacity 0.5s ease;
    max-height: 0;
    opacity: 0;
}

/* Toggle visibility smoothly */
.task-item.active .task-description {
    display: block;
    max-height: 500px;
    opacity: 1;
}

.task-container {
    background-image: url('images/indexCon.jpg'); /* Replace with your image path */
    background-size: cover;  /* Ensures the image covers the entire container */
    background-position: center; /* Centers the image */
    background-repeat: no-repeat; /* Prevents the image from repeating */
    padding: 20px; /* Adds padding for internal spacing */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional: Adds a soft shadow */
}

.div-container {
  display: flex;
  position: relative;
  width: 100px; 
  height: 20px; /* Set height to a fixed value, or you can also use a percentage */
  margin: 0 auto; /* Center the container horizontally */
  background-color: #f0f0f0;/
  
}
</style>

</head>

<body class="index-page">

<!-- Top Dropdown Menu -->

<header id="header">



<nav class="navbar">

<button class="menu-toggle">☰</button>

<a href="userprofile.php" class="logged-in-user">
    <div class="user-info">
        <p><?php echo htmlspecialchars($username); ?></p> <!-- Display the username -->
    </div>
    &nbsp;
    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Icon" class="profile-icon"> <!-- Display the profile icon -->
</a>

<nav class="fancy-menu">

<ul>

<li><a href="userprofile.php">Profile</a></li>
<li><a href="journal_final/journal.php">Journal</a></li>
<li><a href="breathe.php">Zen Zone</a></li>
<li><a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a></li>
<li><a href="subscriptions/doctor.php">Donations</a></li>
<li><a href="reflection/weeklyreflectionform.php">Weekly Reflection</a></li>
<li><a href="contacts/contacts_index.php">Emergency Contact</a></li>


<li><a href="javascript:void(0);" onclick="confirmLogout()">Log Out</a></li> <!-- Log Out link -->

</ul>

</nav>

</nav>

</header>

<div class="welcome-section">

<h1>Welcome to Moodify, <?php echo htmlspecialchars($username); ?>!</h1>

<div id="quote" class="quote"></div>

</div>

<script>


// Function to handle logout confirmation and redirection
function confirmLogout() {
    // Add a delay to show the "logging out..." message before redirecting
    setTimeout(function() {
        window.location.href = window.location.href.split('?')[0] + '?logout=true';  // Append '?logout=true' to the URL
    }, 500);  // Small delay to allow the "logging out..." message to be seen
}




// JavaScript to toggle dropdown menu visibility when the button is clicked
document.querySelector('.menu-toggle').addEventListener('click', function(event) {
    const menu = document.querySelector('.fancy-menu');
    
    // Toggle the 'show' class to display or hide the menu
    menu.classList.toggle('show');
    
    // Prevent click propagation to avoid triggering the document click listener
    event.stopPropagation();
});

// Close the menu if the user clicks outside of it
document.addEventListener('click', function(event) {
    const menu = document.querySelector('.index-page .fancy-menu');
    const menuButton = document.querySelector('.index-page .menu-toggle');
    
    // Check if the click is outside the menu and the menu button
    if (!menu.contains(event.target) && event.target !== menuButton) {
        menu.classList.remove('show'); // Hide the menu
    }
});

document.addEventListener("DOMContentLoaded", function() {

const quotes = [

"The only way to do great work is to love what you do. – Steve Jobs",

"Believe you can and you're halfway there. – Theodore Roosevelt",

"Your limitation—it's only your imagination.",

"Push yourself, because no one else is going to do it for you.",

"Great things never come from comfort zones.",

"Dream it. Wish it. Do it.",

"Success doesn’t just find you. You have to go out and get it.",

"The harder you work for something, the greater you’ll feel when you achieve it."

];

const quoteElement = document.getElementById("quote");

const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];


quoteElement.textContent = randomQuote; // Display the random quote

});
// tasks 
document.addEventListener("DOMContentLoaded", function () {
    // Fetch tasks from the backend after the user logs in
    fetch('indexFetch_tasks.php')  // Endpoint to fetch tasks from the server
        .then(response => response.json())
        .then(data => {
            // Store the tasks globally or in session for easy access
            window.taskData = {
                daily: data.daily,
                weekly: data.weekly,
                monthly: data.monthly
            };

            // Show daily tasks by default (or another type based on logic)
            showTasks('daily');
        })
        .catch(error => console.error('Error fetching tasks:', error));
});

// Function to show tasks (daily, weekly, monthly)
function showTasks(type) {
    // Update button styles
    document.querySelectorAll('.task-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(`${type}-btn`).classList.add('active');

    // Clear and display tasks
    const container = document.getElementById('task-list');
    container.innerHTML = '';  // Clear existing tasks

    if (window.taskData[type].length === 0) {
        container.innerHTML = `<div class="task-item">No tasks available for ${type}.</div>`;
        return;
    }

    window.taskData[type].forEach(task => {
        const taskItem = document.createElement('div');
        taskItem.className = 'task-item';
        taskItem.innerHTML = `
            <div class="task-header">${task.task_name}</div>
            <div class="task-description">${task.task_description}</div>
        `;

        // Toggle description display with smooth animation
        taskItem.querySelector('.task-header').addEventListener('click', function () {
            taskItem.classList.toggle('active');
        });

        container.appendChild(taskItem);
    });
}

function confirmLogout() {
    const confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {
        // Redirect to the log-out page or perform your logout logic here
        window.location.href = "loginform.php"; // Change this to your logout URL
    }
}

</script>


<!-- div sepearting images  -->
<div class="div-container">
  
</div>

<!--tasks-->
<div class="task-container">
    <h1 class="task-title"><center>Small Steps, Big Change: Try these tasks to boost your mental and physical wellbeing!</center></h1>
    <h3><center>Click task to see description</center></h3>
    
    <div class="task-buttons">
        <center>
        <button id="daily-btn" class="task-btn active" onclick="showTasks('daily')">Daily</button>
        <button id="weekly-btn" class="task-btn" onclick="showTasks('weekly')">Weekly</button>
        <button id="monthly-btn" class="task-btn" onclick="showTasks('monthly')">Monthly</button>
        </center>
    </div>

    <div id="task-list" class="task-list">
        <!-- Tasks will be dynamically added here -->
    </div>
</div>

<footer class="footer">
  <div class="footer-content">
    <div class="social-media">
	<p>Follow us on:</p>
     
    <a href="https://www.instagram.com/moodifysa/profilecard/?igsh=aXp6ejFjcHF2Z2E3" class="social-link">Instagram</a>
        
    </div>
    <div class="contact-info">
	  <p>Email us at: <a href="mailto:moodifysa@gmail.com" style="color: white">moodifysa@gmail.com</a></p>
      <p>© 2024 Moodify. All rights reserved.</p>
    </div>
  </div>
</footer>

<div id="logoutPopup" class="logout-popup" style="display:none;">
    <p>Logging out...</p>
</div>

</body>

</html>