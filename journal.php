<?php

session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use session if available
} elseif (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
} else {
    $username = "Guest"; // Fallback for anonymous access
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
        /* Ensure the html and body elements take the full height of the page */
        html, body {
            height: 100%; /* Set height to 100% for both html and body */
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background: url('background2.jpg') no-repeat center center/cover; /* Background image that covers entire page */
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh; /* Make sure the body height covers the entire page */
            padding: 20px;
        }

        header {
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
            color: #a7c7e7; /* Pastel blue */
            font-size: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        p {
            font-size: 20px;
            margin: 20px 0;
            color: #f4e1d2; /* Beige color */
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }

        .button {
            background-color: #a7c7e7; /* Pastel blue */
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            text-decoration: none;
            border-radius: 8px;
            transition: transform 0.2s ease, background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .button:hover {
            background-color: #85aad6; /* Slightly darker pastel blue */
            transform: translateY(-4px);
        }

    </style>
</head>
<body>
    

    <header >
        <h1>Welcome to Your Journal</h1>
        
        <p>Track your thoughts, emotions, and experiences over time. Journaling helps you reflect and grow. Take a moment to explore.</p>
    </header>

    

    <div class="buttons">
        <a href="journal_final/customization.php" class="button">Customize your Journal</a>
        <a href="write_entry.php" class="button">Write a New Entry</a>
        <a href="read_entry.php" class="button">Read Previous Entries</a>
    </div>

</body>
</html>
