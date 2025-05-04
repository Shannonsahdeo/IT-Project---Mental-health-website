<?php 
$conn = new mysqli('localhost', 'root', '@MentalH2024', 'mentalhealthapp');

// Check if the reset token is set in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token against the database
    $stmt = $conn->prepare("SELECT * FROM userloginreg WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the token is invalid or expired, show error message and redirect to login
    if ($result->num_rows === 0) {
        $errorMessage = "Invalid or expired token. Please request a new password reset link.";
        echo "<div style='
            font-size: 24px; 
            color: #f00; 
            text-align: center; 
            border: 3px solid #f00; 
            padding: 20px; 
            background-color: #fff5f5; 
            margin: 20% auto; 
            width: 50%; 
            border-radius: 8px;'>
            $errorMessage
        </div>";
        echo "<script>setTimeout(function() { window.location.href = 'loginform.php'; }, 5000);</script>";
        exit; // Stop further script execution
    } else {
        // Token is valid, handle form submission for password reset
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['password'];
            $confirm_password = $_POST['confirm-password'];
            $recaptcha_response = $_POST['g-recaptcha-response'];

            // Verify reCAPTCHA
            $recaptchaSecret = '6LdOTIEqAAAAAFVumLsh73cqPDHmfBbMgPRa3Irx';
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_data = [
                'secret' => $recaptchaSecret,
                'response' => $recaptcha_response
            ];

            $recaptcha_verify = file_get_contents($recaptcha_url . '?' . http_build_query($recaptcha_data));
            $recaptcha_result = json_decode($recaptcha_verify);

            if (!$recaptcha_result->success) {
                $errorMessage = "Please verify that you are not a robot.";
            } elseif ($new_password !== $confirm_password) {
                $errorMessage = "Passwords do not match!";
            } else {
                // Update the password in the database and remove the reset token
                $stmt = $conn->prepare("UPDATE userloginreg SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
                $stmt->bind_param('ss', $new_password, $token);
                $stmt->execute();

                // Check if the password update was successful
                if ($stmt->affected_rows > 0) {
                    echo "<script>alert('Your password has been successfully reset!'); window.location.href = 'loginform.php';</script>";
                    exit;
                } else {
                    $errorMessage = "There was an issue updating your password. Please try again.";
                }
            }
        }
    }
} else {
    // If no token is provided in the URL, redirect to login
    header("Location: loginform.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Reset</title>
    <style>
        /* Basic styling for the form */
        form {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Style for error and success messages */
        .message {
            font-size: 20px; /* Make the font size bigger */
            text-align: center; /* Center the message */
            margin-top: 20px; /* Add some space above the message */
            color: #f00; /* Red color for error */
            padding: 10px; /* Add some padding for better readability */
            border: 2px solid #f00; /* Border around the message */
            background-color: #fff3f3; /* Light red background */
            border-radius: 5px; /* Rounded corners */
        }


        .error-message {
        font-size: 24px; /* Larger font size */
        color: #f00; /* Red text color */
        text-align: center; /* Center align the text */
        border: 3px solid #f00; /* Red border */
        padding: 20px; /* Add padding */
        background-color: #fff5f5; /* Light red background */
        margin: 20% auto; /* Center vertically and horizontally */
        width: 50%; /* Set the width of the error box */
        border-radius: 8px; /* Add rounded corners */
    }

        .success {
            color: green;
        }

        .requirements {
            font-size: 14px;
            margin-top: 10px;
        }

        .requirement {
            color: red;
            text-decoration: line-through;
        }

        .requirement.satisfied {
            color: green;
            text-decoration: none;
        }

        /* reCAPTCHA error message styling */
        .recaptcha-error {
            color: red;
            margin-top: 10px;
        }

        #password-strength-bar {
            width: 100%;
            height: 10px;
            margin-top: 10px;
            transition: background-color 0.5s ease;
        }

        #password-strength-message {
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Reset Your Password</h2>

    <!-- The form will only display if the token is valid and not expired -->
    <?php if (isset($result) && $result->num_rows > 0) { ?>
        <form method="POST" action="">
            <label for="password">Enter new password:</label>
            <input type="password" name="password" id="password" required onkeyup="checkPasswordStrength()">
            
            <!-- Password strength bar -->
            <div id="password-strength-message"></div>
            <progress id="password-strength-bar" value="0" max="100"></progress>
            
            <!-- Password requirements -->
            <div class="requirements">
                <p id="length" class="requirement">Minimum 8 characters</p>
                <p id="number" class="requirement">At least one number</p>
                <p id="uppercase" class="requirement">At least one uppercase letter</p>
                <p id="lowercase" class="requirement">At least one lowercase letter</p>
                <p id="special" class="requirement">At least one special character</p>
            </div>

            <!-- Reconfirm password field -->
            <div class="confirm-password">
                <label for="confirm-password">Confirm new password:</label>
                <input type="password" name="confirm-password" id="confirm-password" required onkeyup="checkPasswordMatch()">
                <div id="confirm-password-message"></div>
            </div>

            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LdOTIEqAAAAANrpiv9_zptOasGD_gL0bPuBItRq"></div>
            
            <button type="submit">Reset Password</button>
        </form>
    <?php } ?>

    <script>
        // Function to check password strength
        function checkPasswordStrength() {
            var password = document.getElementById('password').value;
            var strengthBar = document.getElementById('password-strength-bar');
            var strengthMessage = document.getElementById('password-strength-message');
            var strength = 0;

            // Check password length (at least 8 characters)
            if (password.length >= 8) {
                strength += 20;
                document.getElementById('length').classList.add('satisfied');
            } else {
                document.getElementById('length').classList.remove('satisfied');
            }

            // Check if the password contains numbers
            if (/[0-9]/.test(password)) {
                strength += 20;
                document.getElementById('number').classList.add('satisfied');
            } else {
                document.getElementById('number').classList.remove('satisfied');
            }

            // Check if the password contains uppercase letters
            if (/[A-Z]/.test(password)) {
                strength += 20;
                document.getElementById('uppercase').classList.add('satisfied');
            } else {
                document.getElementById('uppercase').classList.remove('satisfied');
            }

            // Check if the password contains lowercase letters
            if (/[a-z]/.test(password)) {
                strength += 20;
                document.getElementById('lowercase').classList.add('satisfied');
            } else {
                document.getElementById('lowercase').classList.remove('satisfied');
            }

            // Check if the password contains special characters
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                strength += 20;
                document.getElementById('special').classList.add('satisfied');
            } else {
                document.getElementById('special').classList.remove('satisfied');
            }

            // Update the strength bar and message based on password strength
            strengthBar.value = strength;
            if (strength < 40) {
                strengthBar.style.backgroundColor = '#ff0000'; // red (Weak)
                strengthMessage.innerHTML = "Weak password.";
            } else if (strength < 60) {
                strengthBar.style.backgroundColor = '#ffc107'; // yellow (Good)
                strengthMessage.innerHTML = "Good password.";
            } else {
                strengthBar.style.backgroundColor = '#4caf50'; // green (Strong)
                strengthMessage.innerHTML = "Strong password!";
            }
        }

        // Function to check if passwords match
        function checkPasswordMatch() {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm-password').value;
            var confirmPasswordMessage = document.getElementById('confirm-password-message');

            if (password === confirmPassword) {
                confirmPasswordMessage.innerHTML = "Passwords match!";
                confirmPasswordMessage.style.color = "green";
            } else {
                confirmPasswordMessage.innerHTML = "Passwords do not match!";
                confirmPasswordMessage.style.color = "red";
            }
        }
    </script>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
