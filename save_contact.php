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




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contactName = htmlspecialchars($_POST['contactName']);
    $contactNumber = htmlspecialchars($_POST['contactNumber']);

    // Store the data in a simple file or database. For this example, we'll store it in a text file.
    $file = fopen("emergency_contacts.txt", "a");
    if ($file) {
        $date = date('Y-m-d H:i:s');
        fwrite($file, "[$date] Name: $contactName, Number: $contactNumber\n");
        fclose($file);
        echo "Your contact has been saved successfully!";
    } else {
        echo "There was an error saving your contact. Please try again.";
    }
}
?>
