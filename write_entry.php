<?php


session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use session if available
} elseif (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
} else {
    $username = "Guest"; // Fallback for anonymous access
}



include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = 1;  // Set this dynamically for logged-in users.
    $file_name = $_POST['file_name'];
    $file_content = $_POST['file_content'];

    $sql = "INSERT INTO journal (userId, file_name, file_content) VALUES (:userId, :file_name, :file_content)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':file_name', $file_name);
    $stmt->bindParam(':file_content', $file_content);
    
    if ($stmt->execute()) {
        echo "<p class='success'>Entry saved successfully!</p>";
    } else {
        echo "<p class='error'>Error saving entry. Please try again.</p>";
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
        header {
            text-align: center;
            padding: 30px 20px; /* Reduced padding for smaller header */
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background for readability */
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 80%;
            margin: 50px auto;
        }
        h1 {
            color: #a7c7e7; /* Pastel blue */
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
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: none; /* Disable resizing */
            box-sizing: border-box; /* Make sure padding is included in width/height */
            background-color: #f9f9f9; /* Light background color for the textarea */
            color: #333;
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

        button {
            background-color: #a7c7e7; /* Pastel blue */
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            transition: transform 0.2s ease, background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        button:hover {
            background-color: #85aad6; /* Slightly darker pastel blue */
            transform: translateY(-4px);
        }
        .back-home-btn {
            background-color: #f4e1d2; /* Beige */
            color: #333;
            padding: 15px 30px;
            font-size: 18px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            display: inline-block;
            transition: transform 0.2s ease, background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .back-home-btn:hover {
            background-color: #e1c1a0; /* Slightly darker beige */
            transform: translateY(-4px);
        }
		
		.back{
			text-decoration: none;
			display: inline-block;
			padding: 8px 16px;
		}

		.back:hover {
			background-color: #ddd;
			color: black;
		}

		.previous {
			background-color: grey;
			color: black;
		}

		.round {
		border-radius: 50%;
		}
    </style>
</head>
<body>

    <header>
        <h1>Write a New Journal Entry</h1>
    </header>
	
	<br>
	<a href="journal.php" class="previous round" id = "back">&#8249;</a>

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

            <button type="submit">Save Entry</button>
        </form>
		
        <a href="journal.php" class="back-home-btn">Back to Home</a>
    </div>
	
	<script>
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

        // Load data from localStorage on page load
        window.addEventListener('load', () => {
            const savedPaperType = localStorage.getItem('journal_paper_type');

            
            if (savedPaperType) {
                paperTypeSelect.value = savedPaperType;
                textarea.className = ''; // Clear existing classes
                textarea.classList.add(savedPaperType); // Apply the saved paper type
            }
        });
  </script>

</body>
</html>