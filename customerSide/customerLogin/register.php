<?php
// Include your database connection code here (not shown in this example).
require_once '../config.php';
session_start();

// Define variables and initialize them to empty values
$email = $member_name = $password = $phone_number = "";
$email_err = $member_name_err = $password_err = $phone_number_err = "";

// Check if the form was submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email. Ex: johndoe@email.com";
    } else {
        $email = trim($_POST["email"]);
        // Ensure the email is a Gmail address.
        if (strpos($email, '@gmail.com') === false) {
            $email_err = "Please enter a Gmail address.";
        }
    }

    $selectCreatedEmail = "SELECT email from Accounts WHERE email = ?";

    if($stmt = $link->prepare($selectCreatedEmail)){
        $stmt->bind_param("s", $_POST['email']);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Email already exists
            $email_err = "This email is already registered.";
        } else {
            $email = trim($_POST["email"]);
        }
        $stmt->close();
    }

    // Validate member name
    if (empty(trim($_POST["member_name"]))) {
        $member_name_err = "Please enter your member name.";
    } else {
        $member_name = trim($_POST["member_name"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $password_err = "Password must have at least 8 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone_number"]))) {
        $phone_number_err = "Please enter your phone number.";
    } else if (!preg_match('/^\+91\d{10}$/', trim($_POST["phone_number"]))) {
        $phone_number_err = "Phone number must start with +91 followed by 10 digits.";
    } else {
        $phone_number = trim($_POST["phone_number"]);
    }

    // Check input errors before inserting into the database
    if (empty($email_err) && empty($member_name_err) && empty($password_err) && empty($phone_number_err)) {
        // Start a transaction
        mysqli_begin_transaction($link);

        // Prepare an insert statement for Accounts table
      // Prepare an insert statement for Accounts table
$sql_accounts = "INSERT INTO Accounts (email, password, phone_number, register_date) VALUES (?, ?, ?, NOW())";
if ($stmt_accounts = mysqli_prepare($link, $sql_accounts)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt_accounts, "sss", $param_email, $param_password, $param_phone_number);

    // Set parameters
    $param_email = $email;
    // Store the password as plain text (not recommended for production)
    $param_password = $password;
    $param_phone_number = $phone_number;

    // ...
}

            // Attempt to execute the prepared statement for Accounts table
            if (mysqli_stmt_execute($stmt_accounts)) {
                // Get the last inserted account_id
                $last_account_id = mysqli_insert_id($link);

                // Prepare an insert statement for Memberships table
                $sql_memberships = "INSERT INTO Memberships (member_name, points, account_id) VALUES (?, ?, ?)";
                if ($stmt_memberships = mysqli_prepare($link, $sql_memberships)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt_memberships, "sii", $param_member_name, $param_points, $last_account_id);

                    // Set parameters for Memberships table
                    $param_member_name = $member_name;
                    $param_points = 0; // You can set an initial value for points

                    // Attempt to execute the prepared statement for Memberships table
                    if (mysqli_stmt_execute($stmt_memberships)) {
                        // Commit the transaction
                        mysqli_commit($link);

                        // Registration successful, redirect to the login page
                        header("location: register_process.php");
                        exit;
                    } else {
                        // Rollback the transaction if there was an error
                        mysqli_rollback($link);
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close the statement for Memberships table
                    mysqli_stmt_close($stmt_memberships);
                }
            } else {
                // Rollback the transaction if there was an error
                mysqli_rollback($link);
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close the statement for Accounts table
            mysqli_stmt_close($stmt_accounts);
        }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        /* Updated body style */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .register-container {
            text-align: center;
            max-width: 400px;
            width: 100%;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .register_wrapper {
            width: 100%;
        }
        h2, p {
            text-align: center;
            font-family: 'Montserrat', serif;
        }
        .form-group {
            margin-bottom: 15px;
        }
        ::placeholder {
            font-size: 14px;
        }
        .btn-orange {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            transition: background 0.3s;
        }
        .btn-orange:hover {
            background: linear-gradient(to right, #feb47b, #ff7e5f);
        }
        .logo {
            margin-bottom: 20px;
        }
        .form-text {
            font-size: 14px;
            color: #6c757d;
        }
        .requirement-message {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            background: #f1f1f1;
            padding: 0 10px;
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        .requirement-message.show {
            max-height: 50px;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <a href="../home/home.php">
            <img src="../image/logo2.png" alt="Logo" class="logo" style="width:150px; height:auto;">
        </a>
        <div class="register_wrapper">
            <form action="register.php" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Enter Email">
                    <div class="requirement-message">Must be a valid Gmail address.</div>
                    <span class="text-danger"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Member Name</label>
                    <input type="text" name="member_name" class="form-control" placeholder="Enter Member Name">
                    <span class="text-danger"><?php echo $member_name_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password">
                    <div class="requirement-message">Password must have at least 8 characters.</div>
                    <span class="text-danger"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">+91</span>
                        </div>
                        <input type="text" name="phone_number" class="form-control" placeholder="Enter Phone Number (10 digits)">
                    </div>
                    <div class="requirement-message">Enter 10 digits (the +91 is prefilled).</div>
                    <span class="text-danger"><?php echo $phone_number_err; ?></span>
                </div>
                <button class="btn btn-orange" type="submit" name="register" value="Register">Register</button>
            </form>
            <p style="margin-top:1em;">Already have an account? <a href="../customerLogin/login.php" style="color: #ff7e5f;">Proceed to Login</a></p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var inputs = document.querySelectorAll('input[name="email"], input[name="password"], input[name="phone_number"]');
            inputs.forEach(function(input) {
                input.addEventListener('focus', function() {
                    var req = this.parentNode.querySelector('.requirement-message');
                    if (req) req.classList.add('show');
                });
                input.addEventListener('blur', function() {
                    var req = this.parentNode.querySelector('.requirement-message');
                    if (req) req.classList.remove('show');
                });
            });
        });
    </script>
</body>
</html>