<?php

session_start();

if (!isset($_SESSION['username'])) {
    if (isset($_COOKIE['username'])) {
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['ID'] = $_COOKIE['ID']; // Assuming ID is stored in the cookie
    } else {
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
}




// Check if the user is logged out, then destroy session and redirect
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_unset();
  session_destroy();
  setcookie("username", "", time() - 3600, "/"); // Optional: Delete the cookie
  header("Location: loginform.php"); // Redirect to login page
  exit();
}



$user_id = $_SESSION['ID'];

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mentalhealthapp";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Check if task_id is provided via POST
if (isset($_POST['task_id'])) {
    $task_id = (int) $_POST['task_id'];
    $user_id = $_SESSION['ID']; // Get logged-in user ID
    $is_complete = 1; // Mark as completed
    $completion_date = date('Y-m-d H:i:s'); // Current timestamp

    // First, check if the task exists in user_tasks
    $check_stmt = $conn->prepare("SELECT * FROM user_tasks WHERE task_id = ? AND ID = ?");
    $check_stmt->bind_param("ii", $task_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows == 0) {
        // If no record exists, insert a new record
        $insert_stmt = $conn->prepare("INSERT INTO user_tasks (ID, task_id, is_complete, completion_date) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("iiss", $user_id, $task_id, $is_complete, $completion_date);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Task added and marked as completed']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error inserting task: ' . $insert_stmt->error]);
        }
        $insert_stmt->close();
    } else {
        // If record exists, update the existing record
        $update_stmt = $conn->prepare("UPDATE user_tasks SET is_complete = ?, completion_date = ? WHERE task_id = ? AND ID = ?");
        $update_stmt->bind_param("isii", $is_complete, $completion_date, $task_id, $user_id);
        
        if ($update_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Task updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating task: ' . $update_stmt->error]);
        }
        $update_stmt->close();
    }

    $check_stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Task ID not provided.']);
}

// Close the database connection
$conn->close();
?>