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

$sql = "SELECT * FROM journal ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$entries = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previous Entries</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            background-image: url('background.jpg'); /* Background image */
            background-size: cover; /* Make the background cover the entire page */
            background-position: center center; /* Center the background */
            background-attachment: fixed; /* Keep the background fixed while scrolling */
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: linear-gradient(135deg, #f4e1d2, #a7c7e7); /* Beige to pastel blue gradient */
        }
        h1 {
            color: #4e7ea1; /* Dark pastel blue */
            text-align: center;
            font-size: 36px;
        }
        .entry {
            background-color: #f3f4f6;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .entry h2 {
            color: #4e7ea1;
            font-size: 28px;
        }
        .entry p {
            font-size: 18px;
            color: #333;
            line-height: 1.5;
        }
        .entry small {
            color: #777;
            font-size: 14px;
        }
        .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            background-color: #f4e1d2; /* Beige */
            color: white;
            padding: 12px 20px;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #d3b59c; /* Slightly darker beige */
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Your Journal Entries</h1>
        <?php foreach ($entries as $entry) { ?>
            <div class="entry">
                <h2><?php echo htmlspecialchars($entry['file_name']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($entry['file_content'])); ?></p>
                <p><small>Created on: <?php echo $entry['created_at']; ?></small></p>
            </div>
        <?php } ?>
        <a href="index.php" class="back-button">Back to Home</a>
    </div>

</body>
</html>
