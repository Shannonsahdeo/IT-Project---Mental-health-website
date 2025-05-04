<?php
session_start();
include('db.php'); // Include your database connection

// Check if user is logged in
$userId = $_SESSION['email']; // Adjust based on how you store user info

// Check subscription status
$stmt = $conn->prepare("SELECT status FROM Subscriptions WHERE ID = :userId");
$stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
$stmt->execute();
$userSubscription = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userSubscription['status'] !== 'active') {
    header('Location: doctor.html'); // Redirect to subscription form
    exit();
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $therapistId = $_POST['therapist_id']; // Get therapist ID from the form
    $message = $_POST['message']; // Get message from the form

    $stmt = $conn->prepare("INSERT INTO Messages (user_id, therapist_id, message) VALUES (:userId, :therapistId, :message)");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':therapistId', $therapistId, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->errorInfo()[2]]);
    }

    exit();
}

// Retrieve messages
$stmt = $conn->prepare("SELECT * FROM Messages WHERE user_id = :userId OR therapist_id = :therapistId");
$stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
$stmt->bindParam(':therapistId', $therapistId, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return messages as JSON
echo json_encode($messages);
?>
