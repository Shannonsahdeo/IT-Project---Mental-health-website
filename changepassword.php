<?php

session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use session if available
} elseif (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
} else {
    $username = "Guest"; // Fallback for anonymous access
}



// Check if the user is logged out, then destroy session and redirect
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_unset();
  session_destroy();
  setcookie("username", "", time() - 3600, "/"); // Optional: Delete the cookie
  header("Location: loginform.php"); // Redirect to login page
  exit();
}






$conn = new mysqli('localhost', 'root', '@MentalH2024', 'mentalhealthapp');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT username, email, password FROM userloginreg WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

function validatePassword($password) {
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must include at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must include at least one lowercase letter.";
    }
    if (!preg_match('/\d/', $password)) {
        return "Password must include at least one number.";
    }
    if (!preg_match('/[^\w]/', $password)) {
        return "Password must include at least one special character.";
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $current_password = $_POST['password'];
    $new_password = !empty($_POST['new_password']) ? $_POST['new_password'] : null;
    $confirm_password = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

    if (password_verify($current_password, $user['password'])) {
        if ($new_password && $new_password === $confirm_password) {
            $password_validation_result = validatePassword($new_password);
            if ($password_validation_result === true) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update_query = "UPDATE userloginreg SET password='$hashed_password' WHERE id='$user_id'";

                if ($conn->query($update_query) === TRUE) {
                    $success_message = "Password Updated";
                } else {
                    $error_message = "Error updating profile: " . $conn->error;
                }
            } else {
                $error_message = $password_validation_result;
            }
        } else {
            $error_message = "Passwords do not match!";
        }
    } else {
        $error_message = "Current password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }

        .message {
            text-align: center;
            margin: 10px 0;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($success_message)) echo "<div class='message success'>$success_message</div>"; ?>
        <?php if (isset($error_message)) echo "<div class='message error'>$error_message</div>"; ?>

        <form method="POST" action="">
            <label for="password">Current Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Current password" required>

            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter New password" required>

            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New password" required>

			<br>
			
            <button type="submit" name="update">Change</button>
        </form>
    </div>
</body>
</html>
