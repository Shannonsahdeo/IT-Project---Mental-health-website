<?php
// payment-notify.php

// Database connection
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = "@MentalH2024"; // Your database password
$dbname = "mentalhealthapp"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the payment data from PayFast
$paymentData = $_POST;

// Validate the payment notification with PayFast
if ($paymentData['payment_status'] === 'COMPLETE') {
    // Insert subscription into the database
    $userId = $paymentData['user_id']; // Get user ID from payment data
    $amount = $paymentData['amount'];

    $stmt = $conn->prepare("INSERT INTO Subscriptions (user_id, amount, start_date, next_payment_date, status) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 'active')");
    $stmt->bind_param("id", $userId, $amount);
    
    if ($stmt->execute()) {
        echo "Subscription added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>