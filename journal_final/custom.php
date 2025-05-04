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

// Get user ID from the database using the username
$userId = null;
if ($username != "Guest") {
    $stmt = $conn->prepare("SELECT ID FROM userloginreg WHERE username = ?");
    $stmt->bind_param("s", $username); // Bind the username parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user) {
        $userId = $user['ID'];
    }
}

// Fetch journal entries for the user
$journalEntries = [];
$journalTitle = '';
$journalQuote = '';
if ($userId) {
    $stmt = $conn->prepare("SELECT * FROM journal WHERE ID = ? ORDER BY created_at DESC LIMIT 1"); // Get the most recent journal entry
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $entry = $result->fetch_assoc();

    if ($entry) {
        $journalEntries[] = $entry; // Store journal entry
        $journalTitle = $entry['title']; // Set the journal title
        $journalQuote = $entry['quote']; // Set the journal quote
        $fontType = $entry['font_type']; // Set font type
        $fontSize = $entry['font_size']; // Set font size
        $fontColor = $entry['font_color']; // Set font color
    }
}

// Predefined image paths
$imagePaths = [
    '../journal_final/beach.jpg',
    '../journal_final/blackandgrey.jpg',
    '../journal_final/brown.jpg',
    '../journal_final/butterfly.jpg',
    '../journal_final/groovy.jpg',
    '../journal_final/plants.jpg',
    '../journal_final/purpleabstract.jpg',
    '../journal_final/strawberry.jpg',
    '../journal_final/space.jpg',
    '../journal_final/shells.jpg'
];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_url'])) {
    $image_url = $_POST['image_url']; // Image selected by the user
    $title = $_POST['title']; // Title provided by the user
    $quote = $_POST['quote']; // Quote provided by the user

    // Check if font options are set, and apply defaults if not
    $fontType = $_POST['font_type'] ?? 'Arial';   // Default to 'Arial' if not set
    $fontSize = $_POST['font_size'] ?? 16;        // Default to 16 if not set
    $fontColor = $_POST['font_color'] ?? '#333333'; // Default to '#333333' (dark gray) if not set

    // Check if user is logged in
    if ($userId) {
        // Prepare the SQL query to update the journal
        $stmt = $conn->prepare("UPDATE journal SET image_url = ?, title = ?, quote = ?, font_type = ?, font_size = ?, font_color = ? WHERE ID = ?");
        $stmt->bind_param("ssssiis", $image_url, $title, $quote, $fontType, $fontSize, $fontColor, $userId);

        if ($stmt->execute()) {
            // Send the updated data as a JSON response
            json_encode([ 
                'status' => 'success',
    'image_url' => $image_url,
    'title' => $title,
    'quote' => $quote,
    'font_type' => $fontType,
    'font_size' => $fontSize,
    'font_color' => $fontColor
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not update journal']);
        }

        // Close the statement after execution
        $stmt->close();
    } else {
        // Error if user is not logged in
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customization & Journal</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Bokor&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Sour+Gummy&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Sevillana&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
 body { 

   font-family: Poppins;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
            overflow-x: hidden;
            
        }

        .success-message {
    display: block;
    padding: 15px;
    font-size: 20px;
    text-align: center;
    border-radius: 5px;
    background-color: #28a745; /* Dark translucent background */
    color: white;
    opacity: 1;
    transition: opacity 1s ease;
    position: fixed; /* Position it relative to the viewport */
    top: 50%; /* Center it vertically */
    left: 50%; /* Center it horizontally */
    transform: translate(-50%, -50%); /* Adjust for exact center */
    z-index: 9999; /* Ensure it's on top of all other content */
}


       .success-overlay {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 300px;
    padding: 20px;
    background-color: rgba(0, 128, 0, 0.9); /* Green background with opacity */
    color: white;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Subtle shadow */
    z-index: 9999;
    opacity: 0; /* Initially hidden */
    visibility: hidden;
    transition: opacity 2s ease-out, visibility 3s ease-out;
}

.success-overlay.show {
    opacity: 1; /* Show overlay */
    visibility: visible; /* Make it visible */
}




table {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border-spacing: 20px;
        }

        td {
            vertical-align: top;
            padding: 20px;
        }

        .title, .quote {
            position: absolute;
            z-index: 1;
            left: 20px;
            right: 20px;
            text-align: center;
            color: white;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
        }

        .title {
            top: 20px;
           
            font-weight: bold;
        }

        .quote {
            bottom: 20px;
           
            font-style: italic;
        }


        .image-option {
    display: inline-block;
    margin: 10px;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.image-option img {
    width: 70px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
    margin-bottom: 5px;
}

.image-option:hover {
    transform: scale(1.1); /* Slight zoom effect on hover */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Add a shadow for depth */
}



input[type="radio"] {
    display: block;
    margin: 0 auto;
}

.journal-container {
            position: relative;
            width: 500px;
            height: 700px;
            background-size: cover;
            background-position: center;
            border: 2px solid #ddd;
            border-radius: 0 25px 25px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

.customization-container{
    background: white;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 5px;
    
}



/* Label Styling */
label {
    font-weight: bold;  /* Make the text bold */
    font-size: 1.1em;    /* Slightly increase font size */
    margin-bottom: 8px;  /* Space below the label */
    display: block;      /* Make the label block level so it takes up full width */
    color: #333;         /* Dark gray color for better readability */
}

/* Input Fields Styling */
input[type="text"],
input[type="file"],
input[type="email"],
input[type="password"] {
    width: 100%;            /* Make the input stretch across the container */
    padding: 12px;          /* Add padding inside the input */
    font-size: 1em;         /* Set font size */
    border: 1px solid #ccc; /* Light border */
    border-radius: 4px;     /* Slightly rounded corners */
    margin-bottom: 12px;    /* Space between inputs */
    box-sizing: border-box; /* Include padding and border in width calculation */
}

/* Button Styling */
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

/* Add some space around the form container */
.customization-container {
    padding: 20px;
    max-width: 500px; /* Limit the width of the form */
    margin: 0 auto;  /* Center the form horizontally */
}



.journal-entry {
    position: relative; /* Make the entry container position relative */
    width: 504px;        /* Ensure it takes up the full width of its parent */
    height: 704px;      /* Define a fixed height for the container */
}

.journal-image {
    position: absolute; /* Position the image absolutely within the container */
    top: 0;             /* Align it to the top */
    left: 0;            /* Align it to the left */
    width: 100%;        /* Make the image stretch across the full width */
    height: 100%;       /* Make the image stretch to fill the container's height */
    object-fit: cover;  /* Ensure the image covers the entire container without distortion */
}




  /* Side Menu Styles */
             /* Side Menu Styles */
.side-menu {
    position: fixed;
    top: 0;
    left: -300px; /* Start off-screen */
    width: 250px;
    height: 100%;
    background-color: rgba(255, 170, 120, 0.9);  /* Peach color with transparency */
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
    font-size: 1.5rem;
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

<div class="success-overlay" id="success-overlay">
    Please refresh the page to apply your changes.
</div>


<!-- Menu Toggle Button -->
<button class="toggle-btn">☰</button>

<!-- Side Menu -->
<div class="side-menu">
    <center><h2>Customize Your Journal</h2></center>
    <ul class="vertical-menu">
        
    
<li><a href="../index.php">Home</a></li>
<li><a href="../journal_final/journal.php">Journal</a></li>
<li><a href="../breathe.php">Zen Zone</a></li>
<li><a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a></li>

<li><a href="../subscriptions/doctor.php">Donations</a></li>
<li><a href="../reflection/weeklyreflectionform.php">Weekly Reflection</a></li>
<li><a href="../contacts/contacts_index.php">Emergency Contact</a></li>
<br><br><br>

<button class="logout-btn" onclick="confirmLogout()">Log Out</button>

       
    </ul>
    
</div>

<center><h1> Your Journal</h1></center>


    <!-- Table Layout -->
    <table style="width: 100%; border-spacing: 20px;">
        <tr>
           
            
            <!-- Customization Container Column -->
            <td style="width: 55%; vertical-align: top;">
                <div class="customization-container">
                   
                    <form action="" method="POST">

                    <center><h1>Customize Your Journal</h1></center>

                    <label for="title-text">Title:</label>
                    <input type="text" id="title-text" placeholder="Enter journal title" name="title" required>

                    <br><br>

                    <label for="quote-text">Quote:</label>
                    <input type="text" id="quote-text" placeholder="Enter your quote" name="quote">

                    <br><br>

                    <div>
                    <label for="font-type">Font Type:</label>
<select id="font-type" onchange="changeFont(this)">
    <option value="Arial">Arial</option>
    <option value="Courier New">Courier New</option>
    <option value="Verdana">Verdana</option>
    <option value="Dancing Script" style="font-family: 'Dancing Script', cursive;">Dancing Script</option>
    <option value="Great Vibes" style="font-family: 'Great Vibes', cursive;">Great Vibes</option>
    <option value="Satisfy" style="font-family: 'Satisfy', cursive;">Satisfy</option>
    <option value="Montserrat" style="font-family: 'Montserrat', cursive;">Montserrat</option>
    <option value="Bokor" style="font-family: 'Bokor', cursive;">Bokor</option>
    <option value="Sour Gummy" style="font-family: 'Sour Gummy', cursive;">Sour Gummy</option>
    <option value="Sevillana" style="font-family: 'Sevillana', cursive;">Sevillana</option>
</select>

<br><br>

<label for="font-size">Font Size:</label>
<input type="number" id="font-size" name="font_size" value="16" min="8" max="48" step="2">

<br><br>

<label for="font-color">Font Color:</label>
<input type="color" id="font-color" name="font_color" value="#333333">
                    

                    <br><br>

                        <label for="image_url">Select a Journal Cover:</label><br>
                        
                        <!-- Display predefined images -->
                        <?php foreach ($imagePaths as $image): ?>
                            <div class="image-option">
                                <input type="radio" name="image_url" value="<?php echo $image; ?>" id="image_<?php echo basename($image, '.jpg'); ?>" required>
                                <label for="image_<?php echo basename($image, '.jpg'); ?>">
                                    <img src="<?php echo $image; ?>" alt="Image" class="image-thumbnail">
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <br>
                        <button type="submit">Customize</button>
                    </form>
                </div>
            </td>


             <!-- Journal Container Column -->
             <td style="width: 50%; vertical-align: top;">
             <div class="journal-container" id="journal-container" style="background-image: url('<?php echo htmlspecialchars($journalEntries[0]['image_url'] ?? 'https://via.placeholder.com/300x400.png?text=Default+Cover'); ?>');">
    <div class="title" id="title"><?php echo htmlspecialchars($journalTitle ?? 'Your Journal Title'); ?></div>
    <div class="quote" id="quote"><?php echo htmlspecialchars($journalQuote ?? 'Your Quote'); ?></div>
    
    <?php if (count($journalEntries) > 0): ?>
        <?php foreach ($journalEntries as $entry): ?>
            <div class="journal-entry">
                <?php if (!empty($entry['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($entry['image_url']); ?>" alt="Journal Image" class="journal-image">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No journal entries found.</p>
    <?php endif; ?>
</div>

</td>


        </tr>
    </table>

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
		
		</script>


    <script>
 // Define the function to handle font change
 function changeFont(selectElement) {
        const selectedFont = selectElement.value;
        // Display the alert with the selected font type
        alert("You selected: " + selectedFont);
    }



   document.getElementById('customization-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form from submitting normally

    // Get the selected image URL
    const selectedImage = document.querySelector('input[name="image_url"]:checked');
    const title = document.getElementById('title-text').value; // Get the title from the input field
    const quote = document.getElementById('quote-text').value; // Get the quote from the input field
    const fontType = document.getElementById('font-type').value;
    const fontSize = document.getElementById('font-size').value;
    const fontColor = document.getElementById('font-color').value;

    if (selectedImage) {
        const imageUrl = selectedImage.value;

        // Make AJAX request to update the image, title, quote, and font settings in the database
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText); // Parse the JSON response
                if (response.status === 'success') {
                    // Update journal display immediately with the new data
                    document.getElementById('journal-container').style.backgroundImage = `url(${response.image_url})`;
                    document.getElementById('title').innerText = response.title;  // Update the title text
                    document.getElementById('quote').innerText = response.quote;  // Update the quote text

                    // Apply font settings
                    document.getElementById('title').style.fontFamily = response.font_type;
                    document.getElementById('quote').style.fontFamily = response.font_type;
                    document.getElementById('title').style.fontSize = response.font_size + 'px';
                    document.getElementById('quote').style.fontSize = response.font_size + 'px';
                    document.getElementById('title').style.color = response.font_color;
                    document.getElementById('quote').style.color = response.font_color;

                    // Store the settings in localStorage to persist them for future visits
                    const settings = {
                        title: response.title,
                        quote: response.quote,
                        fontType: response.font_type,
                        fontSize: response.font_size,
                        fontColor: response.font_color
                    };
                    localStorage.setItem('journalSettings', JSON.stringify(settings));
                    localStorage.setItem('journalImage', response.image_url);

                    // Show success alert
                    alert('Your journal has been successfully updated!');

                    // Show the success overlay after customization
                    const overlay = document.getElementById('success-overlay');
                    overlay.classList.add('show');
                    setTimeout(function() {
                        overlay.classList.remove('show');
                    }, 3000); // Hide after 3 seconds
                } else {
                    alert('Error: ' + response.message); // Display error message if status is not success
                }
            }
        };

        // Send the data to the server
        xhr.send(`image_url=${encodeURIComponent(imageUrl)}&title=${encodeURIComponent(title)}&quote=${encodeURIComponent(quote)}&font_type=${encodeURIComponent(fontType)}&font_size=${encodeURIComponent(fontSize)}&font_color=${encodeURIComponent(fontColor)}`);
    }
});



// Function to apply saved settings from localStorage
function applySettings() {
    const settings = JSON.parse(localStorage.getItem('journalSettings'));
    const journalTitle = document.getElementById('title');
    const journalQuote = document.getElementById('quote');
    const journalContainer = document.getElementById('journal-container');

    // Apply saved settings
    if (settings) {
        if (settings.title) journalTitle.innerText = settings.title;
        if (settings.quote) journalQuote.innerText = settings.quote;
        if (settings.fontType) {
            journalTitle.style.fontFamily = settings.fontType;
            journalQuote.style.fontFamily = settings.fontType;
        }
        if (settings.fontSize) {
            journalTitle.style.fontSize = settings.fontSize + 'px';
            journalQuote.style.fontSize = settings.fontSize + 'px';
        }
        if (settings.fontColor) {
            journalTitle.style.color = settings.fontColor;
            journalQuote.style.color = settings.fontColor;
        }
        if (localStorage.getItem('journalImage')) {
            journalContainer.style.backgroundImage = `url(${localStorage.getItem('journalImage')})`;
        }
    }
}

alert('This is a test alert!');

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
