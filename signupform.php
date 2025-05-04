<?php
$host = "localhost";
$username = "root";
$password = "@MentalH2024";
$databasename = "mentalhealthapp";

// Create connection
$conn = new mysqli($host, $username, $password, $databasename);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$alertMessage = "";
$alertClass = "alert-danger";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = ucwords(strtolower(trim($_POST["name"])));
    $email = strtolower(trim($_POST["email"]));
    $contact = $_POST["contact"];
    $username = strtolower(trim($_POST["username"])); 
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm-password"];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA
    $recaptchaSecret = '6LdOTIEqAAAAAFVumLsh73cqPDHmfBbMgPRa3Irx'; // Replace with your secret key
    $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($recaptchaVerifyUrl.'?secret='.$recaptchaSecret.'&response='.$recaptchaResponse);
    $responseKeys = json_decode($response, true);

    if(intval($responseKeys["success"]) !== 1) {
        $alertMessage = "Please verify that you are not a robot.";
    } else {
        // Password match check
        if ($password !== $confirmPassword) {
            $alertMessage = "Your passwords do not match. Please try again.";
        } else {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT * FROM userloginreg WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $alertMessage = "The username or email already exists. Please choose a different one.";
            }  else {
                // Hash the password before storing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user into database
                $stmt = $conn->prepare("INSERT INTO userloginreg (name, email, contact, username, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $contact, $username, $hashedPassword); // $hashedPassword

                if ($stmt->execute()) {
                    $alertMessage = "Registration successful! Redirecting to login page...";
                    $alertClass = "alert-success";
                    echo "<script>
                            setTimeout(function() {
                                window.location.href = 'loginform.php';
                            }, 3000);
                          </script>";
                } else {
                    $alertMessage = "An error occurred. Please try again later.";
                }
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href ="phone-number-validation-master\build\css\demo.css" rel="stylesheet">
    <link href ="phone-number-validation-master\build\css\intlTelInput.css" rel="stylesheet">

    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 130vh;
            background: linear-gradient(135deg, #A8B5E0, #B6DA9F, #ffc87a);
        }
        .signup-container {
            background-color: #e7e7e7;
            width: 400px;
            border-radius: 70px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        .signup-container h1 {
            color: black;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"] {
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

        input[type="checkbox"] {
            width: 16px; /* Set a smaller width */
            height: 16px; /* Set a smaller height */
            cursor: pointer; /* Optional: Changes the cursor to a pointer */
            vertical-align: middle; /* Ensures it aligns nicely with text */
            margin-right: 5px; /* Add some spacing between checkbox and text */
        }
        .terms {
            font-size: 12px;
            margin-top: 15px;
            text-align: center; /* Centers text inside the container */
            display: inline-block; /* Ensures proper block alignment if needed */
    
        }

        .alert {
            color: white;
            padding: 20px;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            display: none;
            transition: opacity 0.5s ease;
        }

        .alert-danger {
            color: white;
            background-color: #f44336;
            padding: 20px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 16px;
        }

        .alert-success {
            background-color: #4CAF50;
        }

        .strength-bar {
            height: 5px;
            width: 100%;
            background-color: #ddd;
            margin-top: 5px;
        }

        .strength-bar div {
            height: 100%;
            width: 0;
            background-color: red;
        }

        .password-requirements {
            font-size: 12px;
            margin-top: 5px;
            text-align: left;
        }

        .requirement {
            color: red;
            font-size: 12px;
        }

        .requirement.satisfied {
            color: green;
        }

        #strength-label {
            font-size: 10px;
            margin-top: 5px;
        }

        .phone-container {
           
            align-items: center; /* Ensures vertical alignment */
            gap: 10px; /* Adds space between the dropdown and text field */
        }

        #contact {
            flex: 1; /* Ensures the text field takes up available space */
            min-width: 290px; /* Prevents the text field from shrinking too much */
        }
    </style>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script type="text/javascript">
        window.onload = function() {
            var alertMessage = "<?php echo $alertMessage; ?>";
            var alertClass = "<?php echo $alertClass; ?>";

            if (alertMessage !== "") {
                var alertBox = document.getElementById("alert-message");
                alertBox.innerHTML = alertMessage;
                alertBox.className = "alert " + alertClass;
                alertBox.style.display = "block";

                setTimeout(function() {
                    alertBox.style.display = "none";
                }, 10000);
            }


            var emailField = document.getElementById("email");
            emailField.addEventListener("input", validateEmail);

            // Call the validateName function as the user types in the name field
            var nameField = document.getElementById("name");
            nameField.addEventListener("input", validateName);

            var contactField = document.getElementById("contact");
            contactField.addEventListener("input", validateContact); // Add contact validation



        }

        function updateStrengthBar(password) {
            var strengthBar = document.getElementById('strength-bar');
            var strength = 0;
            var strengthLabel = document.getElementById('strength-label');

            var requirements = document.querySelectorAll('.requirement');

            // Reset requirement colors
            requirements.forEach(function(requirement) {
                requirement.classList.remove('satisfied');
            });

            // Check password strength
            if (password.length >= 8) {
                strength += 20;
                document.getElementById('length').classList.add('satisfied');
            }
            if (/[A-Z]/.test(password)) {
                strength += 20;
                document.getElementById('uppercase').classList.add('satisfied');
            }
            if (/[a-z]/.test(password)) {
                strength += 20;
                document.getElementById('lowercase').classList.add('satisfied');
            }
            if (/[0-9]/.test(password)) {
                strength += 20;
                document.getElementById('number').classList.add('satisfied');
            }
            if (/[^A-Za-z0-9]/.test(password)) {
                strength += 20;
                document.getElementById('special').classList.add('satisfied');
            }

            // Set strength bar width and color
            strengthBar.style.width = strength + '%';

            if (strength <= 20) {
                strengthBar.style.backgroundColor = 'red';
                strengthLabel.textContent = "Weak";
            } else if (strength <= 60) {
                strengthBar.style.backgroundColor = 'yellow';
                strengthLabel.textContent = "Moderate";
            } else if (strength <= 80) {
                strengthBar.style.backgroundColor = 'orange';
                strengthLabel.textContent = "Strong";
            } else {
                strengthBar.style.backgroundColor = 'green';
                strengthLabel.textContent = "Very Strong";
            }
        }

       



        // Real-time email validation function
        
        function validateEmail() {
            var emailField = document.getElementById("email");
            var email = emailField.value;
            var alertBox = document.getElementById("email-error");
            
            // Regular expression for validating email format with only one dot after the domain name
            var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            // Check if email contains @ and no numbers after it
            if (email === "") {
                alertBox.style.display = "none";  // Hide error if empty
            } else if (!regex.test(email) || /\d/.test(email.split('@')[1]) || (email.split('@')[1].match(/\./g) || []).length > 1) {
                alertBox.style.display = "block";  // Show error if invalid
                alertBox.innerHTML = "Invalid email format. Ensure it contains an '@', no numbers after it, and only one dot after the domain.";
            } else {
                alertBox.style.display = "none";  // Hide error if valid
            }
        }



        function validateContact() {
    var contactField = document.getElementById("contact");
    var contact = contactField.value;
    var alertBox = document.getElementById("contact-error");
    
    // Check if it starts with '+' and contains only numbers
    var regex = /^[+]?[0-9]*$/;
    
    // Check length of contact number
    var isValidLength = contact.length >= 8 && contact.length <= 15;
    
    if (contact === "") {
        alertBox.style.display = "none";  // Hide error if empty
    } else if (!regex.test(contact) || !isValidLength || !iti.isValidNumber()) {
        alertBox.style.display = "block";  // Show error if invalid
        alertBox.innerHTML = "Please enter a valid phone number (only numbers and + are allowed).";
    } else {
        alertBox.style.display = "none";  // Hide error if valid
    }
}


       
        

        // Validate name to ensure it only contains letters and spaces
        function validateName() {
            var nameField = document.getElementById("name");
            var name = nameField.value;
            var alertBox = document.getElementById("name-error");
            
            // Regular expression for only letters and spaces
            var regex = /^[A-Za-z\s]+$/;

            if (name === "") {
                alertBox.style.display = "none";  // Hide error if empty
            } else if (!regex.test(name)) {
                alertBox.style.display = "block";  // Show error if invalid
                alertBox.innerHTML = "Name must only contain letters and spaces.";
            } else {
                alertBox.style.display = "none";  // Hide error if valid
            }
        }





        // Validate the form on submission

        function validateForm() {
        var password = document.forms["signupForm"]["password"].value;
        var confirmPassword = document.forms["signupForm"]["confirm-password"].value;
        var email = document.forms["signupForm"]["email"].value;
        var name = document.forms["signupForm"]["name"].value;
        var terms = document.forms["signupForm"]["terms"].checked;

        // Re-validate name on form submission
        if (!validateNameOnSubmit(name)) {
            alert("Please enter a valid name (only letters and spaces).");
            return false;
        }

        // Re-validate email on form submission
        if (!validateEmailFormat(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        if (password.length < 8) {
            alert("Password must be at least 8 characters.");
            return false;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }

        if (!terms) {
            alert("You must accept the terms and conditions.");
            return false;
        }

        // Check if phone number is valid
        if (!iti.isValidNumber()) {
            alert("Please enter a valid phone number.");
            return false; // Prevent form submission
        }

        return true;
    }

        // Separate name validation function for form submission
        function validateNameOnSubmit(name) {
            var regex = /^[A-Za-z\s]+$/;
            return regex.test(name);
        }

        // Separate email validation function for form submission
        function validateEmailFormat(email) {
            var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return regex.test(email) && !/\d/.test(email.split('@')[1]) && (email.split('@')[1].match(/\./g) || []).length === 1;
        }


        


    </script>
</head>
<body>
    <div class="alert" id="alert-message"></div>

    <div class="signup-container">
        <h1>Sign Up</h1>
        <form action="" method="POST" name="signupForm" onsubmit="return validateForm();">

            <input type="text" id="name" name="name" placeholder="Enter your Full Name" required>
            <div id="name-error" class="alert alert-danger" style="display:none;"></div>

            <input type="email" id="email" name="email" placeholder="Enter your Email" required>
            <div id="email-error" class="alert alert-danger" style="display:none;"></div>


            <div class="phone-container">
                <input type="tel" id="contact" name="contact" placeholder="Enter your Contact Number" required>
                <div id="contact-error" class="alert alert-danger" style="display:none;"></div>
            </div>

            


            <input type="text" id="username" name="username" placeholder="Enter your Username" required>
            <input type="password" id="password" name="password" placeholder="Enter your Password" required oninput="updateStrengthBar(this.value)">

            <div class="strength-bar">
                <div id="strength-bar"></div>
            </div>
            <div id="strength-label">Weak</div>

            <div class="password-requirements">
                <div id="length" class="requirement">At least 8 characters</div>
                <div id="uppercase" class="requirement">At least one uppercase letter</div>
                <div id="lowercase" class="requirement">At least one lowercase letter</div>
                <div id="number" class="requirement">At least one number</div>
                <div id="special" class="requirement">At least one special character</div>
            </div>

            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your Password" required>
            <center><div class="g-recaptcha" data-sitekey="6LdOTIEqAAAAANrpiv9_zptOasGD_gL0bPuBItRq"></div></center>
            <br>
            <input type="checkbox" id="terms" name="terms" required> I accept the <a href="Terms%20and%20Conditions.pdf" target="_blank">terms and conditions</a>
            <br>
            <input type="submit" value="Sign Up">
            <p class="terms">Already have an account? <a href="loginform.php">Log in here!</a></p>
        </form>

        
        <script src="phone-number-validation-master\build\js\intlTelInput.js"></script>

        <script>

           // Initialize intl-tel-input
            var input = document.querySelector("#contact");
            var iti = window.intlTelInput(input, {
                initialCountry: "us", // Default country
                utilsScript: "phone-number-validation-master/build/js/utils.js", // Path to utils.js
            });


            // Add the selected country code to the input field
            input.addEventListener("countrychange", function () {
                var countryCode = iti.getSelectedCountryData().dialCode;

                // Add the country code if not already added
                if (!input.value.startsWith(`+${countryCode}`)) {
                    input.value = `+${countryCode}`;
                }

                // Place the cursor at the end
                input.setSelectionRange(input.value.length, input.value.length);
            });

            // Block entering more characters once the length limit is reached
            input.addEventListener("input", function () {
                var countryCode = iti.getSelectedCountryData().dialCode;
                var maxLength = iti.getSelectedCountryData().maxLength; // Maximum length for the current country
                var fullLength = maxLength + countryCode.length; // Total max length including the country code

                // Check if the input length has exceeded the allowed length (including country code)
                if (input.value.length > fullLength) {
                    input.value = input.value.substring(0, fullLength); // Trim excess characters
                }

                // Prevent '0' from being entered directly after the country code
                if (input.value.startsWith(`+${countryCode}0`)) {
                    input.value = input.value.replace(`0`, ''); // Remove the 0 after the country code
                }
            });

        </script>

    </div>
</body>
</html>
