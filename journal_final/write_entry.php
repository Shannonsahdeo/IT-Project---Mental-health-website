<?php
session_start();

// Database Connection
$host = 'localhost'; 
$dbname = 'mentalhealthapp'; 
$user = 'root'; 
$pass = '@MentalH2024';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if user is logged in
$user_id = $_SESSION['ID'] ?? null; // Get user ID from session, or null if not logged in

if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_id) {
    $file_name = $_POST['file_name'] ?? '';
    $file_content = $_POST['file_content'] ?? '';

    if ($file_name && $file_content) {
        // Simple insert query
        $sql = "INSERT INTO journal (ID, file_name, file_content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$user_id, $file_name, $file_content])) {
            echo "<p class='success'>Entry saved successfully!</p>";
        } else {
            echo "<p class='error'>Error saving entry. Please try again.</p>";
        }
    } else {
        echo "<p class='error'>Both file name and content are required.</p>";
    }
} 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a New Journal Entry</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('background1.jpg'); /* Background image */
            background-size: cover; /* Make the background cover the entire page */
            background-position: center center; /* Center the background */
            background-attachment: fixed; /* Keep the background fixed while scrolling */
            color: #333;
            margin: 0;
            padding: 0;
        }
        .heading {
            text-align: center;
            padding: 30px 20px; /* Reduced padding for smaller header */
            background-color: #0e5066; /* Semi-transparent background for readability */
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 80%;
            margin: 50px auto;
        }
        
		.entry{
            color: rgba(255, 170, 120, 0.9);
            font-size: 40px; /* Smaller header font size */
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
        }
        .form-container {
            width: 80%;
            margin: 0 auto;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8); /* Light background for form area */
            padding: 30px;
            border-radius: 10px;
        }
        label {
            font-size: 18px;
            margin-top: 20px;
            display: block;
        }
		
		
        textarea {
            width: 100%;
            height: 300px; /* Make it taller for a more journal-like experience */
            padding: 15px;
            font-size: 18px;
            border: 2px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box; /* Make sure padding is included in width/height */
            background-color: #f9f9f9; /* Light background color for the textarea */
            color: #333;
			resize: disabled; /* Allows vertical resizing */
    }

    textarea:focus {
        outline: none; /* Removes the default outline */
    }
        
			
		textarea.plain {
			background-image: none; /* No background pattern */
			background-color: white; /* Plain white background */
		}
		textarea.lined {
			background-image: repeating-linear-gradient(to bottom, #ccc 0, #ccc 1px, transparent 1px, transparent 32px);
		}

    
		textarea.dotted {
			background-image: radial-gradient(circle, #ccc 1px, transparent 1px);
			background-size: 22px 22px;
		}

        .save {
            background-color: #00aaff; 
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            text-decoration: none;
            border-radius: 30px;
            margin-top: 20px;
            transition: transform 0.2s ease, background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .save:hover {
            background-color: #0088cc; 
			transform: scale(1.05);
        }
        .back-home-btn {
            background-color: #00aaff; 
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            text-decoration: none;
            border-radius: 30px;
            margin-top: 20px;
            display: inline-block;
            transition: transform 0.2s ease, background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .back-home-btn:hover {
            background-color: #0088cc; /* Slightly darker beige */
            transform: scale(1.05);
        }
		
		
		 /* Side Menu Styles */
.side-menu {
    position: fixed;
    top: 0;
    left: -300px; /* Start off-screen */
    width: 250px;
    height: 100%;
    background-color: rgba(255, 170, 120, 0.9); /* Peach color with transparency */
    color: #fff;
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
<div class="side-menu" id="side-menu">
    <center>
    <h2>Write Journal</h2>
  
<a href="../index.php">Home</a>
<a href="../userprofile.php">Profile</a>

<a href="../breathe.php">Zen Zone</a>
<a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a>

<a href="../subscriptions/doctor.php">Donations</a>
<a href="../reflection/weeklyreflectionform.php">Weekly Reflection</a>
<a href="../contacts/contacts_index.php">Emergency Contact</a>

   

    <!-- Log Out Button -->
    <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
<center>
</div>
	<div class = "content">

    <div class = "heading">
        <h1 class = "entry">Write a New Journal Entry</h1>
    </div>
	
	

    <div class="form-container">
        <form action="write_entry.php" method="POST">
		
			<label for="paper_type">Select Paper Type:</label>
			<select id="paper_type" name="paper_type">
				<option value="plain">Plain</option>
				<option value="lined">Lined</option>
				<option value="dotted">Dotted</option>
			</select>
			
            <label for="file_name">Title of Your Journal Entry:</label>
            <input type="text" id="file_name" name="file_name" placeholder="Enter a title for your journal entry" required style="width: 100%; padding: 15px; font-size: 18px; border-radius: 8px; border: 1px solid #ccc; background-color: #f9f9f9; box-sizing: border-box;">
			
            <label for="file_content">Your Journal Entry:</label>
            <textarea id="file_content" name="file_content" placeholder="Write your journal entry here..." required></textarea>

            <button type="submit" class = "save">Save Entry</button>
        </form>
		
        <a href="journal.php" class="back-home-btn">Back to Home</a>
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
	
    document.getElementById('paper_type').addEventListener('change', function () {
      const textarea = document.getElementById('file_content');
      textarea.className = ''; // Clear existing classes
      textarea.classList.add(this.value); // Add the selected class based on the dropdown value
    });
	
	const paperTypeSelect = document.getElementById('paper_type');

        // Save data to localStorage on change
        paperTypeSelect.addEventListener('change', () => {
            localStorage.setItem('journal_paper_type', paperTypeSelect.value);
        });


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