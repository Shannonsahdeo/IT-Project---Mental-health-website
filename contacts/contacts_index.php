<?php
session_start();

// Check session or cookie for user
if (isset($_SESSION['username']) && isset($_SESSION['ID'])) {
    $username = $_SESSION['username']; // Use session if available
    $user_id = $_SESSION['ID']; // Assuming you store the user ID in the session as well
} elseif (isset($_COOKIE['username']) && isset($_COOKIE['ID'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
    $user_id = $_COOKIE['ID']; // Assuming you store the user ID in the cookie as well
} else {
    $username = "Guest"; // Fallback for anonymous access
    $user_id = null; // No user ID for guest
}

// Check if the user is logged out, then destroy session and redirect
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    setcookie("username", "", time() - 3600, "/"); // Optional: Delete the cookie
    header("Location: loginform.php"); // Redirect to login page
    exit();
  }
  

$servername = "localhost"; // Your database server
$usernameDB = "root"; // Your database username
$passwordDB = "@MentalH2024"; // Your database password
$dbname = "mentalhealthapp"; // Your database name

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contactName']) && isset($_POST['contactNumber'])) {
    $contactName = $conn->real_escape_string($_POST['contactName']);
    $contactNumber = $conn->real_escape_string($_POST['contactNumber']);
    
    // Insert new contact into database
    $sql = "INSERT INTO emergency_contacts (ID, contact_name, contact_number) VALUES ('$user_id', '$contactName', '$contactNumber')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New contact saved successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch saved contacts
$sql = "SELECT contact_name, contact_number FROM emergency_contacts WHERE ID = '$user_id'";
$result = $conn->query($sql);

// Check if query was successful
if ($result === FALSE) {
    echo "Error in query: " . $conn->error; // Print the error message if query fails
    exit();
}

$savedContacts = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $savedContacts[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Emergency Contacts - Mental Health Awareness</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
		/* Basic Styles */
body {
    font-family: Poppins, sans-serif;
    margin: 0;
    padding: 0;
    background-color:rgba(255,200,150,0.8) ;
    background-size: 100% 200%;
    color: #333;
}


h1 {
    font-size: 2rem;
}

.contact-card {
    background-color: white;
    color: #0e5066;
    padding: 15px;
    width:50%;
    margin: 10px 0;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.contact-card h3 {
    color: #333;
}

.contact-card p {
    font-size: 1.2rem;
    margin: 5px 0;
}

.custom-contact {
    background-color: white;
    color: #0e5066;
    padding: 20px;
    margin: 30px 0;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

form label {
    display: block;
    margin: 10px 0 5px;
}

form input {
    width: 25%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid grey;
    border-radius: 30px;
}

form button {
    background-color: #00aaff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
}

form button:hover {
    background-color: #0088cc;
	transform: scale(1.05);
}

footer {
    background-color: #0e5066;
    color: white;
    text-align: center;
    padding: 20px;
    width: 100%;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
}

.social-media {
    margin-bottom: 5px;
}

.social-link {
    margin: 0 8px;
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.social-link:hover {
    color: grey;
}

.contact-info p {
    margin: 3px 0;
}

.contact-info a {
    color: #333;
    text-decoration: none;
}

.contact-info a:hover {
    text-decoration: underline;
}

/* Side Menu Styles */
        .side-menu {
            position: fixed;
            top: 0;
            left: -300px; /* Start off-screen */
            width: 250px;
            height: 100%;
            background-color: rgba(255, 170, 120, 0.9); /* Slightly darker peach */
            color: #fff;
            padding: 10px;
            transition: left 0.3s ease; /* Smooth transition when opening/closing */
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center the items vertically */
            align-items: center; /* Center the items horizontally */
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
            margin: 5px 0; /* Reduced margin to bring items closer */
            padding: 5px 15px; /* Adjusted padding for a more compact appearance */
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        /* Hover Effect for Menu Items */
        .side-menu a:hover {
            background-color: white;
            color: rgba(240, 180, 130, 0.7); /* Slightly darker peach color */


            transform: scale(1.05); /* Make items "pop" on hover */
        }

        /* Show the side menu when active */
        .side-menu.active {
            left: 0; /* Slide in */
        }

        /* Log Out Button Styles */
        .logout-btn {
            background-color: white; /* White background for the button */
            color: rgba(255, 150, 100, 0.8); /* Darker peach color for the text */
            font-size: 1.5rem;
            padding: 12px 20px; /* Adjusted padding for better button size */
            border: none;
            border-radius: 20px;
            cursor: pointer;
            margin-top: 20px; /* Space above the Log Out button */
            margin-bottom: 20px; 
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

<!-- Menu Toggle Button -->
<button class="toggle-btn">☰</button>

<!-- Side Menu -->
<div class="side-menu">
    <center><h2>Emergency Contacts</h2></center>
    <ul class="vertical-menu">
         <a href="../index.php">Home</a>
<a href="../userprofile.php">Profile</a>
<a href="../journal_final/journal.php">Journal</a>
<a href="../breathe.php">Zen Zone</a>
<a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a>

<a href="../subscriptions/doctor.php">Donations</a>
<a href="../reflection/weeklyreflectionform.php">Weekly Reflection</a>

        
    </ul>
    <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
    
</div>

<div class="content">
    <center>
    <section class="contacts">
        <h1 style="text-align: center; color: #0e5066;">Emergency Contact Numbers</h1>
        <div class="contact-card">
            <h3>Police</h3>
            <p><a href="tel:10111">10111</a></p>
        </div>
        <div class="contact-card">
            <h3>Fire Department</h3>
            <p><a href="tel:10177">10177</a></p>
        </div>
        <div class="contact-card">
            <h3>Emergency (Non Crime Related)</h3>
            <p><a href="tel:112">112</a></p>
        </div>
        <div class="contact-card">
            <h3>Mountain Rescue</h3>
            <p>KZN: <a href="tel:0313077744">031 307 7744</a></p>
            <p>Western Cape: <a href="tel:0219489900">021 948 9900</a></p>
            <p>Gauteng: <a href="tel:0741251385">074 125 1385</a> / <a href="tel:0741633952">074 163 3952</a></p>
        </div>
        <div class="contact-card">
            <h3>Poison Emergency Numbers</h3>
            <p>Tygerberg: <a href="tel:0219316129">021 931 6129</a></p>
            <p>Red Cross: <a href="tel:0216895227">021 689 5227</a></p>
            <p>KZN: <a href="tel:0800333444">080 033 3444</a></p>
            <p>Bloemfontein: <a href="tel:0824910160">082 491 0160</a></p>
        </div>
        <div class="contact-card">
            <h3>Mental Health & Child Welfare</h3>
            <p>Lifeline: <a href="tel:0861322322">0861 322 322</a></p>
            <p>Suicide Crisis Line: <a href="tel:0800567567">0800 567 567</a></p>
            <p>SADAG Mental Health Line: <a href="tel:0112344837">011 234 4837</a></p>
        </div>
        <div class="contact-card">
            <h3>Child Abuse</h3>
            <p>Childline: <a href="tel:0800055555">0800 05 55 55</a></p>
            
        </div>
        </center>
    </section>

    <section class="custom-contact">
        <h2>Your Own Emergency Contacts</h2>
        <p>If you have personal contacts or a therapist you would like to store for emergencies, you can add them here.</p>
        <form method="POST" id="contactForm">
            <label for="contactName">Contact Name:</label>
            <input type="text" id="contactName" name="contactName" placeholder="Enter your Full Name" required>

            <label for="contactNumber">Contact Number:</label>
            <input type="tel" id="contactNumber" name="contactNumber" required pattern="[0-9]{3}[0-9]{3}[0-9]{4}" placeholder="Enter a valid phone number">
            <br><br>
            <button type="submit">Save Contact</button>
        </form>

        <h3>Your Saved Contacts:</h3>
        <div id="savedContactsList">
            <?php
            if (count($savedContacts) > 0) {
                foreach ($savedContacts as $contact) {
                    echo "<div class='contact-card'>
                            <h3>" . htmlspecialchars($contact['contact_name']) . "</h3>
                            <p><a href='tel:" . htmlspecialchars($contact['contact_number']) . "'>" . htmlspecialchars($contact['contact_number']) . "</a></p>
                          </div>";
                }
            } else {
                echo "<p>No custom contacts saved yet.</p>";
            }
            ?>
        </div>
    </section>
</div>

<footer>
    <div class="footer-content">
        <div class="social-media">
            <p>Follow us on:</p>
            <a href="#" class="social-link">Instagram</a>
        </div>
        <div class="contact-info">
            <p>Email us at: <a href="mailto:moodifysa@gmail.com" style="color: white">moodifysa@gmail.com</a></p>
        </div>
    </footer>

    <script>
        // Get the button and the side menu
        const menuToggle = document.querySelector('.toggle-btn');
        const sideMenu = document.querySelector('.side-menu');

        // Toggle the side menu display when the button is clicked
        menuToggle.onclick = function() {
            sideMenu.classList.toggle('active'); // Add or remove the 'active' class to slide in/out
        };

        // Optional: Close the menu if the user clicks anywhere outside
        window.onclick = function(event) {
            if (!sideMenu.contains(event.target) && !menuToggle.contains(event.target)) {
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
