<?php
/*
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use session if available
} elseif (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
} else {
    $username = "Guest"; // Fallback for anonymous access
}
*/

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['ID'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mentalhealthapp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['taskType'])) {
    $taskType = $_GET['taskType'];

    // Prepare statement to prevent SQL injection
    //$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_type = ? ORDER BY id DESC LIMIT 4");
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE task_type = ? ORDER BY task_id DESC LIMIT 4");
    $stmt->bind_param("s", $taskType);
    $stmt->execute();
    $result = $stmt->get_result();

    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = [
            //'id' => $row['ID'],
            'name' => $row['task_name'],
            'description' => $row['task_description'],
            'xp' => $row['xp_points']
        ];
    }

    echo json_encode($tasks);
    $stmt->close();
}

