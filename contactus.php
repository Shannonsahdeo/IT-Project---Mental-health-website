<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load PHPMailer
require 'C:\xampp\htdocs\IT_Project-2024\emailreset\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\IT_Project-2024\emailreset\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\IT_Project-2024\emailreset\PHPMailer-master\src\Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Invalid email address. Please enter a valid email.');
                window.history.back();
              </script>";
        exit();
    }

    // Validate other fields
    if (empty($name) || empty($message)) {
        echo "<script>
                alert('Please fill in all required fields.');
                window.history.back();
              </script>";
        exit();
    }

    // PHPMailer setup
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'moodifysa@gmail.com'; // Your email
        $mail->Password = 'ffvl fgwa phqi qekp'; // Use app password for Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Send the user's message to your team
        $mail->setFrom('moodifysa@gmail.com', 'Moodify Team'); // Your app's email
        $mail->addAddress('moodifysa@gmail.com', 'Moodify Support'); // Recipient email
        $mail->addReplyTo($email, $name); // User's email for replies

        // Email content to your team
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Message from " . $name;
        $mail->Body = "<h3>New Contact Form Message</h3>
                       <p><b>Name:</b> $name</p>
                       <p><b>Email:</b> $email</p>
                       <p><b>Message:</b><br>$message</p>";
        $mail->AltBody = "Name: $name\nEmail: $email\n\nMessage:\n$message";

        $mail->send();

        // Send a confirmation email to the user
        $mail->clearAddresses(); // Clear previous recipients
        $mail->addAddress($email, $name); // Send to the user's email

        $mail->Subject = "Thank you for contacting Moodify!";
        $mail->Body = "<h3>Hi $name,</h3>
                       <p>Thank you for reaching out to us. We have received your message and will get back to you as soon as possible.</p>
                       <p><b>Your message:</b><br>$message</p>
                       <p>Best regards,<br>The Moodify Team</p>";
        $mail->AltBody = "Hi $name,\n\nThank you for reaching out to us. We have received your message and will get back to you as soon as possible.\n\nYour message:\n$message\n\nBest regards,\nThe Moodify Team";

        $mail->send();

        // Success message and redirection
        echo "<script>
                alert('Thank you for your message! A confirmation email has been sent to you.');
                window.location.href = 'thankyou.html'; // Redirect to a thank-you page
              </script>";
    } catch (Exception $e) {
        // Error handling
        echo "<script>
                alert('There was an issue sending your message. Please try again later.');
              </script>";
        echo "Mailer Error: " . $mail->ErrorInfo; // Debugging output
    }
} else {
    header("Location: contactus.html");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Poppins, sans-serif; 
			background: linear-gradient(to bottom, #2bc0e4, #74ebd5, #ffffff, #FA709A);
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 180vh;
            margin: 0;
        }

        
        .contact-image {
            width: 200px;
            height: 200px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            object-fit: cover;
        }

        .contact-container {
            background-color: #e7e7e7;
            padding: 20px 30px;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .contact-container h1 {
            color: black;
            margin-bottom: 10px;
        }

        .contact-container p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
        }

        .contact-form input, .contact-form textarea {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 15px;
            font-size: 16px;
            width: 100%;
        }

        .contact-form button {
            width: 35%;
            margin: 20px auto 0;
            padding: 10px;
            background-color: #00aaff;
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .contact-form button:hover {
            background-color: #0088cc;
        }

        footer {
            background-color: #0e5066;
            color: white;
            text-align: center;
            padding: 20px;
            width: 100%;
			margin-top: 100px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .social-media {
            margin-bottom: 10px;
        }

        .social-link {
            margin: 0 8px;
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .social-link:hover {
            color: grey;
        }

        .contact-info p {
            margin: 5px 0;
        }

        .contact-info a {
            color: white;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }
		
		.home{
            position:absolute;
            top: 50%;
            left:50%;
            transform: translate(-50%, -50%);
            z-index:10;
            padding:10px 20px;
			margin-right: 1400px;
			text-decoration: none;
			color: black;
		}
		.home:hover{
			color: grey;
		}
    </style>
</head>
<body>
<a href = "homeexample.html" class = "home">&larr; Home</a>
<header>
    <img src="images\moodifylogo.jpg" alt="Moodify Logo" class="contact-image">
</header><br><br>

<div class="contact-container">
    <h1>Contact Us</h1>
    <p>If you have any questions, suggestions, or just want to say hello, feel free to reach out to us!</p>
    <form class="contact-form" action="" method="POST">
        <input type="text" id="name" name="name" placeholder="Name" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <textarea id="message" name="message" rows="5" placeholder="Please enter your message" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</div><br><br><br>

<footer>
    <div class="footer-content">
        <div class="social-media">
            <p>Follow us on:</p>
           
            <a href="https://www.instagram.com/moodifysa/profilecard/?igsh=aXp6ejFjcHF2Z2E3" class="social-link">Instagram</a>
     
        </div>
        <div class="contact-info">
            <p>Email us at: <a href="mailto:moodifysa@gmail.com">moodifysa@gmail.com</a></p>
            <p>Phone: +1-234-567-890</p>
        </div>
        <div class="social-media">
            <a href="homeexample.html" class="social-link">Home</a>
            <a href="aboutus.html" class="social-link">About Us</a>
            <a href="resources.html" class="social-link">Resources</a>
            <a href="FAQ.html" class="social-link">FAQs</a>
        </div>
    </div>
</footer>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $to = "moodifysa@gmail.com"; 

    $subject = "New Contact Form Message from " . $name;

    $email_content = "Name: " . $name . "\n";
    $email_content .= "Email: " . $email . "\n\n";
    $email_content .= "Message:\n" . $message . "\n";

    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $email_content, $headers)) {
        echo "<script>alert('Thank you for your message! We will get back to you soon.' 'color: green'); window.location.href='contactus.html';</script>";
    } else {
        echo "<script>alert('There was an issue sending your message. Please try again later.' 'color: red'); window.location.href='contactus.html';</script>";
    }
} else {
    header("Location: contactus.php");
    exit();
}
?>

