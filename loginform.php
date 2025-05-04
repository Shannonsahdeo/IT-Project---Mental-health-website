<?php  
session_start();

// Include PHPMailer
require 'C:\xampp\htdocs\IT_Project-2024\emailreset\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\IT_Project-2024\emailreset\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\IT_Project-2024\emailreset\PHPMailer-master\src\Exception.php';


/*Haley's paths for testing
require 'C:\xampp\htdocs\IT_Project-2024-main\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\IT_Project-2024-main\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\IT_Project-2024-main\PHPMailer-master\src\Exception.php';
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli('localhost', 'root', '@MentalH2024', 'mentalhealthapp');
date_default_timezone_set('Africa/Johannesburg');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = ""; // To store error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required POST data exists
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Sanitize inputs to prevent SQL injection
        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);

         // Convert the entered username to lowercase
         $username = strtolower($username);
///////////////////
        // Check credentials in the database
        $stmt = $conn->prepare("SELECT * FROM userloginreg WHERE BINARY username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // Fetch the user's details
            // Use password_verify to check if the entered password matches the hashed password
            if (password_verify($password, $user['password'])) {
                // Login successful
                // Set session variables
                $_SESSION['username'] = $username;
                $_SESSION['ID'] = $user['ID'];  // Assuming 'ID' is the primary key in your database
        
                // If "Remember me" is checked, create a cookie
                if (isset($_POST['remember'])) {
                    // Set cookies for 1 week
                    setcookie('username', $username, time() + (86400 * 7), "/"); // 86400 = 1 day
                    setcookie('password', $password, time() + (86400 * 7), "/");
                }
        
                // Redirect to loading page
                header("Location: loadingpage.php");
                exit; // Ensure script ends here
            } else {
                // Incorrect credentials
                $error = "Invalid username or password!";
            }
        }
    }
}

// If cookies exist, use them to automatically log in
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    $username = $_COOKIE['username'];
    $password = $_COOKIE['password'];

    // Validate cookies against the database
    $stmt = $conn->prepare("SELECT * FROM userloginreg WHERE username = ? AND password = ?");
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

   /* if ($result->num_rows > 0) {
        // Cookie credentials are valid, start session and redirect
        $_SESSION['username'] = $username;

        // Debugging: Check if session is set
        // echo "Session set for " . $_SESSION['username'];

        header("Location: loadingpage.php");
        exit; // Ensure script ends here
    }*/
}


// Handle forgot password request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT * FROM userloginreg WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate a new token
            $token = bin2hex(random_bytes(32));

            // Set expiration time 15 minutes from now
            $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Save the token and expiration time in the database
            $stmt = $conn->prepare("UPDATE userloginreg SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmt->bind_param('sss', $token, $expires, $email);
            $stmt->execute();

            // Generate reset link
            $resetLink = "http://localhost/IT_Project-2024/loginresetpassword.php?token=" . $token;

            // Send the email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'moodifysa@gmail.com'; // Your Gmail
                $mail->Password = 'ffvl fgwa phqi qekp';  // App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('moodifysa@hmail.com', 'moodify');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Hi, click <a href='$resetLink'>here</a> to reset your password. This link is valid for 15 minutes.";

                $mail->send();
                $resetMessage = "A reset link has been sent to your email.";
            } catch (Exception $e) {
                $resetMessage = "Email could not be sent. Error: {$mail->ErrorInfo}";
            }
        } else {
            $resetMessage = "No account found with that email.";
        }
    }
}

// Handle password reset via token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token
    $stmt = $conn->prepare("SELECT * FROM userloginreg WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid and not expired
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['password'])) {
                $new_password = $_POST['password'];

                // Update the password and remove the reset token and expiration time
                $stmt = $conn->prepare("UPDATE userloginreg SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
                $stmt->bind_param('ss', $new_password, $token);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $passwordResetMessage = "Your password has been successfully reset!";
                } else {
                    $passwordResetMessage = "There was an issue updating your password. Please try again.";
                }
            }
        }
    } else {
        // Invalid or expired token
        $passwordResetMessage = "Invalid or expired token.";
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(60deg, #f0e4b0 0%, #f7f0d1 100%);
        }

        .login-container {
            background-color: #e7e7e7;
            width: 350px;
            border-radius: 70px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            position: relative;
            z-index: 2; /* Higher z-index to make it on top of the wave background */
        }

        

        .login-container h1 {
            color: black;
        }

        input[type="text"], input[type="password"] {
            width: 80%;
            padding: 12px;
            margin: 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 50px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 85%;
            background-color: #00aaff;
            color: white;
            padding: 12px 20px;
            margin: 15px 0;
            border: none;
            border-radius: 50px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0088cc;
        }

        .alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.5);
            display: none;
        }

        .alert-box {
            background-color: #e7ffe7;
            color: #007700;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-size: 18px;
            text-align: center;
        }

        .alert-box.alert-error {
            background-color: #ffe7e7;
            color: #ff0000;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 3; /* Ensure modal is above the login form */
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 20px;
            cursor: pointer;
        }

         /* Error alert styles */
         .error-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .error-box {
            background-color: #ffcccc;
            color: #cc0000;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

         /* Wave background styles */
         .waves {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40vh;
            z-index: 0; /* Ensures that the waves are behind everything */
        }

        /* Animation for wave movement (updated animation) */
        .parallax > use {
            animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
        }

        .parallax > use:nth-child(1) {
            animation-delay: -2s;
            animation-duration: 7s;
        }

        .parallax > use:nth-child(2) {
            animation-delay: -3s;
            animation-duration: 10s;
        }

        .parallax > use:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 13s;
        }

        .parallax > use:nth-child(4) {
            animation-delay: -5s;
            animation-duration: 20s;
        }

        /* Keyframes for wave animation */
        @keyframes move-forever {
            0% {
                transform: translate3d(-90px, 0, 0);
            }
            100% {
                transform: translate3d(85px, 0, 0);
            }
        }

    </style>
</head>
<body>

<!-- Background Container with Waves -->
<div class="background-container">
    <svg class="waves" xmlns="http://www.w3.org/2000/svg" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
        </defs>
        <g class="parallax">
            <!-- Updated waves with animation -->
            <use xlink:href="#gentle-wave" x="48" y="2" fill="#72efdd" />
            <use xlink:href="#gentle-wave" x="48" y="6" fill="#9ceaef" />
            <use xlink:href="#gentle-wave" x="48" y="5" fill="#68d8d6" />
            <use xlink:href="#gentle-wave" x="48" y="7" fill="#3dccc7" />
            <use xlink:href="#gentle-wave" x="48" y="20" fill="#07beb8" />
        </g>
    </svg>
</div>

<div class="login-container">
    <h1>Log In</h1>
    <form action="loginform.php" method="POST">
        <input type="text" id="username" name="username" placeholder="Username" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <div class="remember-me">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Remember me</label>
        </div>
        <input type="submit" value="Log In">
    </form>
    <p class="forgot-password"><a href="#" id="forgotPasswordLink">Forgot password?</a></p>
    <p class="terms">Don't have an account? <a href="signupform.php">Sign up here!</a></p>
</div>

<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Reset Password</h2>
        <form action="loginform.php" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="reset_password">Send Reset Link</button>
        </form>
        <?php if (isset($resetMessage)) { echo "<p>$resetMessage</p>"; } ?>
    </div>
</div>

<?php if (isset($passwordResetMessage)) { echo "<div class='alert-box'>$passwordResetMessage</div>"; } ?>

<script>
    const modal = document.getElementById('forgotPasswordModal');
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');

    // Open modal when "Forgot password?" is clicked
    forgotPasswordLink.onclick = function() {
        modal.style.display = 'flex';
    }

    // Close modal when "x" is clicked
    function closeModal() {
        modal.style.display = 'none';
    }

    // Close modal if clicked outside of modal content
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<!-- Error Alert Box -->
<?php if (!empty($error)) : ?>
    <div class="error-overlay" id="errorOverlay">
        <div class="error-box">
            <?php echo $error; ?>
        </div>
    </div>
    <script>
        // Remove the error overlay after 4 seconds
        setTimeout(() => {
            document.getElementById('errorOverlay').style.display = 'none';
            document.getElementById('password').value = ''; // Clear password field
        }, 4000);
    </script>
<?php endif; ?>

<!-- Success/Error Message Popup -->
<?php if (isset($resetMessage) || isset($passwordResetMessage)) { ?>
    <div class="modal" id="messageModal">
        <div class="modal-content">
            <span class="close" onclick="closeMessageModal()">&times;</span>
            <div class="alert-box <?php echo isset($passwordResetMessage) ? 'alert-error' : ''; ?>">
                <?php echo isset($passwordResetMessage) ? $passwordResetMessage : $resetMessage; ?>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    // Show the forgot password modal
    document.getElementById('forgotPasswordLink').onclick = function() {
        document.getElementById('forgotPasswordModal').style.display = 'flex';
    }

    // Close the modal
    function closeModal() {
        document.getElementById('forgotPasswordModal').style.display = 'none';
    }

    // Show and close the message popup
    function closeMessageModal() {
        document.getElementById('messageModal').style.display = 'none';
    }

    // Automatically show the message popup if there's a message
    <?php if (isset($resetMessage) || isset($passwordResetMessage)) { ?>
        document.getElementById('messageModal').style.display = 'flex';
    <?php } ?>


   
</script>

</body>
</html>
