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


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe to Doctor Access</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
	<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Poppins, sans-serif;
            background: linear-gradient(to bottom, #43a3ec, #60dbe4);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            border-radius: 38px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            padding: 20px;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            color: #0e5066; 
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #666;
        }
        input[type="hidden"] {
            display: none; /* Hide hidden fields */
        }
        input[type="submit"] {
            background-color: #00aaff; 
            color: white;
            border: none;
            border-radius: 30px;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #0088cc; 
			transform: scale(1.05);
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #999;
        }
		
		/* Side Menu Styles */
        .side-menu {
            position: fixed;
            top: 0;
            left: -300px; /* Start off-screen */
            width: 250px;
            height: 100%;
            background-color: rgba(255, 200, 150, 0.7); /* Peach color with transparency */
            color: #fff;
            padding: 10px;
            transition: left 0.3s ease; /* Smooth transition when opening/closing */
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center the items vertically */
            align-items: center; /* Center the items horizontally */
        }

        /* Menu List Styles */
        .side-menu ul {
            padding: 0;
            margin: 0;
            list-style-type: none; /* Remove bullet points */
            text-align: center; /* Center the list items */
        }

        /* Menu Item Styles */
        .side-menu a {
            color: #fff;
            text-decoration: none;
            font-size: 1.3rem;
            display: block;
            margin: 5px 0; /* Reduced margin to bring items closer */
            padding: 5px 15px; /* Adjusted padding for a more compact appearance */
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        /* Hover Effect for Menu Items */
        .side-menu a:hover {
            background-color: white;
            color: rgba(240, 180, 130, 0.7); /* Slightly darker peach color */


            transform: scale(1.05); /* Make items "pop" on hover */
        }

        /* Show the side menu when active */
        .side-menu.active {
            left: 0; /* Slide in */
        }

        /* Log Out Button Styles */
        .logout-btn {
            background-color: white; /* White background for the button */
            color: rgba(255, 150, 100, 0.8); /* Darker peach color for the text */
            font-size: 1.5rem;
            padding: 12px 20px; /* Adjusted padding for better button size */
            border: none;
            border-radius: 20px;
            cursor: pointer;
            margin-top: 20px; /* Space above the Log Out button */
            margin-bottom: 20px; 
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        /* Hover Effect for Log Out Button */
        .logout-btn:hover {
            background-color: rgba(255, 200, 150, 0.8); /* Darker peach background on hover */
            transform: scale(1.05); /* Button expands slightly on hover */
        }

        /* Toggle Button Styles */
        .toggle-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.7);
            color: #333;
            padding: 10px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            z-index: 3;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .toggle-btn:hover {
            background-color: rgba(255, 255, 255, 1);
            transform: scale(1.1);
        }
    </style>
</head>
<body>
<!-- Menu Toggle Button -->
<button class="toggle-btn">☰</button>

<!-- Side Menu -->
<div class="side-menu">
    <h2 style = "color: white;">Subscriptions</h2>
    <ul class="vertical-menu">
        <a href="../index.php">Home</a>
<a href="../userprofile.php">Profile</a>
<a href="../journal_final/journal.php">Journal</a>
<a href="../breathe.php">Zen Zone</a>
<a href="http://localhost:5000/music-recommendation">Tunes for Your Mood</a>


<a href="../reflection/weeklyreflectionform.php">Weekly Reflection</a>
<a href="../contacts/contacts_index.php">Emergency Contact</a>
        
    </ul>
    <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
</div>

    <div class="container">
        <h1>Support the platform?</h1>
        <p>Enjoying our website?If you are finding our website helpful please consider to leave a small donation to help us facilitate further development and improve our site. </p>
        <form id="subscriptionForm" action="https://www.payfast.co.za/eng/process" method="POST">
            <input type="hidden" name="merchant_id" value="25979149"> <!-- Your Merchant ID -->
            <input type="hidden" name="merchant_key" value="lnfvdj2rwg575"> <!-- Your Merchant Key -->
            <input type="hidden" name="return_url" value="http://localhost:3000/payment-success"> <!-- Replace with your return URL -->
            <input type="hidden" name="cancel_url" value="http://localhost:3000/payment-cancel"> <!-- Replace with your cancel URL -->
            <input type="hidden" name="notify_url" value="http://localhost:3000/payment-notify"> <!-- Replace with your notify URL -->
            <input type="hidden" name="amount" value="10.00">
            <input type="hidden" name="item_name" value="Donation">
            <input type="hidden" name="email" value="USER_EMAIL"> <!-- Replace with the user's email -->
            <input type="submit" value="Subscribe Now">
        </form>
        <div id="message" style="margin-top: 20px; color: #4A90E2;"></div>
        <div class="footer">
            <p>Thank you</p>
        </div>
    </div>
	<script>
	
	// Get the button and the side menu
    const menuToggle = document.querySelector('.toggle-btn');
    const sideMenu = document.querySelector('.side-menu');

    // Toggle the side menu display when the button is clicked
    menuToggle.onclick = function() {
        sideMenu.classList.toggle('active'); // Add or remove the 'active' class to slide in/out
    };

    // Optional: Close the menu if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.toggle-btn') && !event.target.closest('.side-menu')) {
            sideMenu.classList.remove('active');
        }
    };
		</script>

    <script>
        // Handle return and cancel actions
        function handleReturn() {
            const message = "Thank you for your payment! Your subscription is now active.";
            document.getElementById('message').innerText = message;
            generatePDF(); // Generate PDF proof of payment
        }

        function handleCancel() {
            document.getElementById('message').innerText = "Your payment was canceled. Please try again.";
        }

        // Simulate return and cancel actions for demonstration purposes
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('status')) {
            const status = urlParams.get('status');
            if (status === 'success') {
                handleReturn();
            } else if (status === 'cancel') {
                handleCancel();
            }
        }

        // Function to generate PDF proof of payment
        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Add content to the PDF
            doc.setFontSize(20);
            doc.text("Proof of Payment", 20, 20);
            doc.setFontSize(12);
            doc.text("Thank you for your payment!", 20, 40);
            doc.text("Subscription: Doctor Access", 20, 50);
            doc.text("Amount: R10.00", 20, 60);
            doc.text("Date: " + new Date().toLocaleDateString(), 20, 70);
            doc.text("Status: Successful", 20, 80);

            // Save the PDF
            doc.save("proof_of_payment.pdf");
        }


        function confirmLogout() {
    const confirmation = confirm("Are you sure you want to log out?");
    if (confirmation) {
        // Redirect to the log-out page or perform your logout logic here
        window.location.href = "../loginform.php"; // Change this to your logout URL
    }
}

    </script>
</body>
</html>