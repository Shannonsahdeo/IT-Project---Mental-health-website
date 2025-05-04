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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatroom - Mental Health Support</title>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        #chatContainer {
            width: 100%;
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        #messages {
            height: 400px;
            overflow-y: scroll;
            padding: 10px;
            border-bottom: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            gap: 10px; /* Space between messages */
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            background-color: #e0f7fa; /* Light blue background */
            position: relative;
            max-width: 80%; /* Limit message width */
        }
        .timestamp {
            font-size: 0.8em;
            color: #666;
            position: absolute;
            bottom: 5px;
            right: 10px;
        }
        #messageInput {
            display: flex;
            padding: 10px;
        }
        #messageInput input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #messageInput button {
            padding: 10px;
            border: none;
            background-color: #00aaff;
            color: white;
            border-radius: 10px;
            margin-left: 6px;
            cursor: pointer;
        }
        #messageInput button:hover {
            background-color: #0088cc; 
        }
		
		/* Side Menu Styles */
        .side-menu {
            position: fixed;
            top: 0;
            left: -300px; /* Start off-screen */
            width: 250px;
            height: 100%;
            background-color: rgba(255, 200, 150, 0.7); /* Peach color with transparency */
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
            font-size: 1.5rem;
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
    <h1>Chatroom</h1>
    <ul class="vertical-menu">

 <a href="../index.php">Home</a>
<a href="../userprofile.php">Profile</a>
<a href="../journal_final/journal.php">Journal</a>
<a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a>
<a href="../breathe.php">Zen Zone</a>
<a href="../subscriptions/doctor.php">Subscriptions</a>
<a href="../reflection/weeklyreflectionform.php">Weekly Reflection</a>
<a href="../contacts/contacts_index.php">Emergency Contact</a>
    </ul>
    <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
</div>

	<h2 style = "color: #0e5066; text-align: center;">Chat with a Therapist</h2>
<div class = "content">

    <div id="chatContainer">
        <div id="messages"></div>
        <div id="messageInput">
            <input type="text" id="message" placeholder="Type your message here..." />
            <button onclick="sendMessage()">Send</button>
        </div>
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
		
		</script>

    <!-- Inline JavaScript -->
    <script>
        const therapistId = 1; // Replace with the actual therapist ID

        // Function to fetch messages
        function fetchMessages() {
            fetch('message-handler.php')
                .then(response => response.json())
                .then(data => {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = ''; // Clear existing messages
                    data.forEach(message => {
                        const messageElement = document.createElement('div');
                        messageElement.className = 'message';
                        messageElement.innerHTML = `
                            ${message.message}
                            <span class="timestamp">${new Date(message.timestamp).toLocaleTimeString()}</span>
                        `;
                        messagesDiv.appendChild(messageElement);
                    });
                    messagesDiv.scrollTop = messagesDiv.scrollHeight; // Auto-scroll to the bottom
                });
        }

        // Function to send a message
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const messageText = messageInput.value;

            if (messageText.trim() === '') return; // Prevent sending empty messages

            const formData = new FormData();
            formData.append('therapist_id', therapistId);
            formData.append('message', messageText);

            fetch('message-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageInput.value = ''; // Clear input field
                    fetchMessages(); // Refresh messages
                } else {
                    alert(data.message); // Show error message
                }
            });
        }

        // Fetch messages every 5 seconds
        setInterval(fetchMessages, 5000);
        fetchMessages(); // Initial fetch


        
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