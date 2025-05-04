<?php 
$conn = new mysqli('localhost', 'root', '', 'mentalhealthapp');

// Check if the reset token is set in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token against the database
    $stmt = $conn->prepare("SELECT * FROM userloginreg WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the token is valid and not expired
    if ($result->num_rows > 0) {
        // Handle the form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get the reCAPTCHA response from the form
            $recaptchaResponse = $_POST['g-recaptcha-response'];

            // Verify reCAPTCHA with Google's API
            $recaptchaSecret = '6LdOTIEqAAAAAFVumLsh73cqPDHmfBbMgPRa3Irx';
            $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
            
            $response = file_get_contents($recaptchaVerifyUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
            $responseKeys = json_decode($response, true);

            // Check if the reCAPTCHA is valid
            if (intval($responseKeys["success"]) !== 1) {
                // reCAPTCHA failed, show error
                $recaptchaError = "Please complete the CAPTCHA verification.";
            } else {
                // reCAPTCHA successful, process password reset
                $new_password = $_POST['password'];

                // Update the password in the database and remove the reset token
                $stmt = $conn->prepare("UPDATE userloginreg SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
                $stmt->bind_param('ss', $new_password, $token);
                $stmt->execute();

                // Check if the password update was successful
                if ($stmt->affected_rows > 0) {
                    // Password reset successful, clear form inputs and redirect to login page with success message
                    header("Location: login.php?message=Your password has been successfully reset.");
                    exit();
                } else {
                    $errorMessage = "There was an issue updating your password. Please try again.";
                }
            }
        }
    } else {
        // If the token is invalid or expired
        $errorMessage = "Invalid or expired token. Please request a new password reset link.";
    }
} else {
    // If the token is not set in the URL
    $errorMessage = "Invalid request. Please provide a valid password reset link.";
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
        /* Style for password strength bar */
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

        /* Style for requirements */
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

        /* Style for error and success messages */
        .message {
            font-size: 14px;
            margin-top: 10px;
            color: #f00;
        }

        .success {
            color: green;
        }

        /* Styling for confirm password input */
        .confirm-password {
            margin-top: 10px;
        }

        /* reCAPTCHA error message styling */
        .recaptcha-error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Reset Your Password</h2>

    <?php 
    // Display error message if token is invalid or expired
    if (isset($errorMessage)) {
        echo '<div class="message">' . $errorMessage . '</div>';
    } else {
        // Only show the form if the token is valid
    ?>

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

        <!-- reCAPTCHA widget -->
        <div class="g-recaptcha" data-sitekey="6LdOTIEqAAAAANrpiv9_zptOasGD_gL0bPuBItRq"></div>

        <?php 
        // Display error message if reCAPTCHA is not checked
        if (isset($recaptchaError)) {
            echo '<div class="recaptcha-error">' . $recaptchaError . '</div>';
        }
        ?>

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

            // Update the strength bar and message
            strengthBar.value = strength;

            if (strength < 40) {
                strengthBar.style.backgroundColor = '#f44336'; // red (Weak)
                strengthMessage.innerHTML = "Weak password. ";
            } else if (strength < 60) {
                strengthBar.style.backgroundColor = '#ff9800'; // orange (Moderate)
                strengthMessage.innerHTML = "Moderate password. ";
            } else if (strength < 80) {
                strengthBar.style.backgroundColor = '#ffeb3b'; // yellow (Strong)
                strengthMessage.innerHTML = "Strong password.";
            } else {
                strengthBar.style.backgroundColor = '#4CAF50'; // green (Very Strong)
                strengthMessage.innerHTML = "Very strong password!";
            }
        }

        // Function to check if the confirm password matches the new password
        function checkPasswordMatch() {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm-password').value;
            var confirmPasswordMessage = document.getElementById('confirm-password-message');
            
            if (password !== confirmPassword) {
                confirmPasswordMessage.innerHTML = "Passwords do not match!";
                confirmPasswordMessage.style.color = 'red';
            } else {
                confirmPasswordMessage.innerHTML = "Passwords match!";
                confirmPasswordMessage.style.color = 'green';
            }
        }
    </script>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
