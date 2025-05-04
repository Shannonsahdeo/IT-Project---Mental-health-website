<?php 
session_start();

// Determine username from session or cookie
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use session if available
} elseif (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
} else {
    $username = null; // No user logged in
}

// Check if the user is logged out, then destroy session and redirect
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_unset();
    session_destroy();
    setcookie("username", "", time() - 3600, "/"); // Optional: Delete the cookie
    header("Location: loginform.php"); // Redirect to login page
    exit();
}

// Database connection
$host = "localhost";
$db_username = "root";
$password = "@MentalH2024";
$databasename = "mentalhealthapp";

// Create connection
$conn = new mysqli($host, $db_username, $password, $databasename);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user ID from session (assuming ID is saved in session)
$user_id = $_SESSION['ID'] ?? 0;  // Ensure user_id is fetched from session

// Initialize default profile picture
$default_pic = 'uploads/default-profile.png';

// Retrieve user profile picture, username, and email from the database
$sql = "SELECT profile_pic, username, email FROM userloginreg WHERE ID = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $profile_pic = $user && $user['profile_pic'] ? $user['profile_pic'] : $default_pic;
    $current_username = $user['username'] ?? '';
    $user_email = $user['email'] ?? '';
} else {
    $profile_pic = $default_pic; // Fallback to default
    $current_username = '';
    $user_email = '';
}

// Initialize message variables
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_username'])) {
        // Update username
        $new_username = $conn->real_escape_string($_POST['username']);
        // Update the username in the database
        $update_query = "UPDATE userloginreg SET username = '$new_username' WHERE ID = $user_id";
        if ($conn->query($update_query)) {
            $success_message = "Username updated successfully.";
            $_SESSION['username'] = $new_username;
            $current_username = $new_username;  // Update current username in the session
        } else {
            $error_message = "Error updating username: " . $conn->error;
        }
    } elseif (isset($_POST['update_email'])) {
        // Update email
        $new_email = $conn->real_escape_string($_POST['email']);
        // Update the email in the database
        $update_query = "UPDATE userloginreg SET email = '$new_email' WHERE ID = $user_id";
        if ($conn->query($update_query)) {
            $success_message = "Email updated successfully.";
            $user_email = $new_email;  // Update email in session
        } else {
            $error_message = "Error updating email: " . $conn->error;
        }
    } elseif (isset($_POST['update_photo']) && isset($_FILES['profile_pic'])) {
        // Handle profile picture upload using the reference code logic
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $fileName = $_FILES['profile_pic']['name'];
            $fileSize = $_FILES['profile_pic']['size'];
            $fileType = $_FILES['profile_pic']['type'];
            $fileTmpName = $_FILES['profile_pic']['tmp_name'];
            
            $uploadDir = "uploads/"; // Upload directory
            
            // Ensure the upload directory exists, create if not
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Creates the directory with write permissions
            }

            // Supported image formats
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            // Extract file extension from mime type
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Check if the file type is allowed
            if (in_array($fileExt, $allowedTypes)) {
                // Create a new unique file name
                $newFileName = md5(time() . $fileName) . '.' . $fileExt;

                // Move the file to the upload directory
                if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
                    // Prepare the update query using direct variable substitution
                    $newProfilePicPath = $uploadDir . $newFileName;
                    $newProfilePicPath = $conn->real_escape_string($newProfilePicPath); // Escape path for SQL query

                    $update_query = "UPDATE userloginreg SET profile_pic = '$newProfilePicPath' WHERE ID = $user_id";

                    if ($conn->query($update_query)) {
                        $success_message = "Profile picture updated successfully.";
                        $profile_pic = $newProfilePicPath; // Update current session with the new image
                    } else {
                        $error_message = "Error updating profile picture: " . $conn->error;
                    }
                } else {
                    $error_message = "Error uploading file.";
                }
            } else {
                $error_message = "Invalid file format. Allowed formats are: jpg, jpeg, png, gif.";
            }
        } else {
            $error_message = "No file uploaded or an error occurred during file upload.";
        }
    }
}

$conn->close();
?>

<?php if ($success_message || $error_message): ?>
    <div id="messageBox" class="<?php echo $success_message ? 'success' : 'error'; ?>">
        <?php echo $success_message ?: $error_message; ?>
    </div>
<?php endif; ?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>


 /* Apply the background and font to the body */
 body { 
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            padding: 0;
            margin: 0;
            height: 100vh; /* Full viewport height */
            background: url('beach3.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }

        /* Style for the message box */
        #messageBox {
            display: block;
            padding: 15px;
            font-size: 20px;
            text-align: center;
            border-radius: 5px;
            background-color: #28a745; /* Dark translucent background */
            color: white;
            opacity: 1;
            transition: opacity 1s ease;
            position: absolute; /* Position it inside the container */
            top: 50%; /* Center it vertically */
            left: 50%; /* Center it horizontally */
            transform: translate(-50%, -50%); /* Adjust for exact center */
            z-index: 10; /* Ensure it's on top */
        }


        .container {
            max-width: 500px;
            width: 100%;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative; /* Position it relative to the page */
        }


        /* Profile photo styles and other existing styles */
        .profile-photo-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #28a745;
            position: absolute;
            top: -50px;
            left: calc(50% - 50px);
            background-color: #fff;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 60px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="file"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        button {
            padding: 10px 15px;
            background-color: #00aaff;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0088cc;
        }

        .message {
            text-align: center;
            margin: 10px 0;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        

        .success {
            background-color: #4CAF50;
            color: white;
        }

        .error {
            background-color: #f44336;
            color: white;
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

<!-- Menu Toggle Button -->
<button class="toggle-btn">☰</button>

<!-- Side Menu -->
<div class="side-menu">
    <center><h1>Profile</h1></center>
    <ul class="vertical-menu">
        
    
<li><a href="index.php">Home</a></li>
<li><a href="journal_final/journal.php">Journal</a></li>
<li><a href="breathe.php">Zen Zone</a></li>
<li><a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a></li>

<li><a href="subscriptions/doctor.php">Donations</a></li>
<li><a href="reflection/weeklyreflectionform.php">Weekly Reflection</a></li>
<li><a href="contacts/contacts_index.php">Emergency Contact</a></li>


<button class="logout-btn" onclick="confirmLogout()">Log Out</button>

       
    </ul>
    
</div>

<!-- Main Content Container -->
<div class="container">
    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-photo-circle">
    <h2>Profile</h2>

     <!-- Update Details Form with Individual Button -->
     <form method="POST" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter new username" required value="<?php echo htmlspecialchars($current_username); ?>">
        <button type="submit" name="update_username">Update Details</button>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter new email" value="<?php echo htmlspecialchars($user_email); ?>">

        <button type="submit" name="update_email">Update Details</button>
    </form>

    <!-- Update Profile Picture Form -->
    <form method="POST" enctype="multipart/form-data" action="">
        <label for="profile_pic">Profile Picture</label>
        <input type="file" id="profile_pic" name="profile_pic" accept="image/*" required>

        <button type="submit" name="update_photo">Update Profile Picture</button>
    </form>
</div>

<script>

// Wait for the page to load
window.onload = function() {
  const messageBox = document.getElementById('messageBox');

  // After 4 seconds, fade the message out
  setTimeout(function() {
    messageBox.style.opacity = 0; // Fade the message out
  }, 2000); // 4 seconds
};



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
        window.location.href = "loginform.php"; // Change this to your logout URL
    }
}

</script>

</body>
</html>
