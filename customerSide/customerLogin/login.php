<?php
// Include your database connection code here
require_once '../config.php';
session_start();

// Define variables for email and password
$email = $password = "";
$email_err = $password_err = "";

// Check if the form was submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before checking authentication
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT * FROM Accounts WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Get the result
                $result = mysqli_stmt_get_result($stmt);

                // Check if a matching record was found.
                if (mysqli_num_rows($result) == 1) {
                    // Fetch the result row
                    $row = mysqli_fetch_assoc($result);

                    
                   // Verify the password
                    if ($password === $row["password"]) {
                        // Password is correct, start a new session and redirect the user to a dashboard or home page.
                        $_SESSION["loggedin"] = true;
                        $_SESSION["email"] = $email;

                        // Query to get membership details
                        $sql_member = "SELECT * FROM Memberships WHERE account_id = " . $row['account_id'];
                        $result_member = mysqli_query($link, $sql_member);

                        if ($result_member) {
                            $membership_row = mysqli_fetch_assoc($result_member);

                            if ($membership_row) {
                                $_SESSION["account_id"] = $membership_row["account_id"];
                                header("location: ../home/home.php"); // Redirect to the home page
                                exit;
                            } else {
                                // No membership details found
                                $password_err = "No membership details found for this account.";
                            }
                        } else {
                            // Error in membership query
                            $password_err = "Error fetching membership details: " . mysqli_error($link);
                        }
                    } else {
                        // Password is incorrect
                        $password_err = "Invalid password. Please try again.";
                    }


                } else {
                    // No matching records found
                    $email_err = "No account found with this email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }

        .login-container {
            text-align: center;
            max-width: 400px;
            width: 100%;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-container img {
            width: 100px; 
            padding: 10px/* Increased logo size */
        }

        .login-container h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: #555;
        }

        .form-control {
            font-size: 14px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .btn {
            background: linear-gradient(90deg, #ff416c, #ff4b2b);
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            width: 100%;
            margin-top: 10px;
        }

        .btn:hover {
            background: linear-gradient(90deg, #ff4b2b, #ff416c);
        }

        .text-danger {
            font-size: 12px;
            margin-top: 5px;
        }

        .login-container p {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }

        .login-container p a {
            color: #ff416c;
            text-decoration: none;
        }

        .login-container p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="http://localhost/restaurant-management/customerSide/home/home.php">
            <img src="../image/logo2.png" alt="Logo" style="width:250px; height:auto;">
        </a>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="sample@name.com" required>
                <span class="text-danger"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="********" required>
                <span class="text-danger"><?php echo $password_err; ?></span>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
</body>
</html>

