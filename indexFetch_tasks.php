<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '@MentalH2024';
$dbname = 'mentalhealthapp';

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch 5 random daily tasks
$dailyTasksQuery = "SELECT * FROM tasks WHERE task_type='daily' ORDER BY RAND() LIMIT 5";
$dailyTasks = $conn->query($dailyTasksQuery)->fetch_all(MYSQLI_ASSOC);

// Fetch 5 random weekly tasks
$weeklyTasksQuery = "SELECT * FROM tasks WHERE task_type='weekly' ORDER BY RAND() LIMIT 5";
$weeklyTasks = $conn->query($weeklyTasksQuery)->fetch_all(MYSQLI_ASSOC);

// Fetch 5 random monthly tasks
$monthlyTasksQuery = "SELECT * FROM tasks WHERE task_type='monthly' ORDER BY RAND() LIMIT 5";
$monthlyTasks = $conn->query($monthlyTasksQuery)->fetch_all(MYSQLI_ASSOC);

// Combine tasks into a single array and return as JSON
$tasks = [
    'daily' => $dailyTasks,
    'weekly' => $weeklyTasks,
    'monthly' => $monthlyTasks
];

header('Content-Type: application/json');
echo json_encode($tasks);

$conn->close();
?>
