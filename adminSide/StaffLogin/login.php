<?php 
session_start();
if(isset($_SESSION['logged_account_id'])) {
    header("Location: ../panel/pos-panel.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            color: black;
            background-color: white; /* Changed to white */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .btn-light {
            background-color: orange; /* Changed to orange */
            color: white;
            border: none;
            font-size: 18px; /* Increased font size */
            padding: 10px 20px; /* Increased padding */
            width: 100%; /* Full width button */
        }

        .btn-light:hover {
            background-color: darkorange; /* Slightly darker shade for hover */
        }

        .brand img {
            display: block;
            margin: 0 auto 20px; /* Center the logo and add spacing */
        }

        .form-control {
            border: 1px solid #ccc; /* Added outline */
            box-shadow: none; /* Removed default shadow */
        }

        .form-control:focus {
            border-color: orange; /* Highlight outline on focus */
            box-shadow: 0 0 5px orange; /* Add subtle glow */
        }

        table {
            border: 1px solid grey; /* Added grey outline to table */
        }
    </style>
</head>
<body>
    <p>&nbsp;&nbsp;&nbsp;</p> 
    <section id="signup">
    <div class="container my-6 ">
        <div class="brand">
            <a class="nav-link" href="http://localhost/restaurant-management/customerSide/home/home.php#hero">
                <img src="../image/logo2.png" alt="Logo" style="width:150px; height:auto;">
            </a>
        </div>
    
    <div class="wrapper">
        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

    <form action="login_process.php" method="post" >
        <div class="form-group">
            <label for="account_id">Staff Account ID</label>
            <input type="number" id="account_id" name="account_id" placeholder="Enter Account ID" required class="form-control <?php echo (!empty($account_id)) ? 'is-invalid' : ''; ?>" value="<?php echo $account_id; ?>">
            <span class="invalid-feedback"><?php echo $account_id; ?></span>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
        </div>
            
            <div class="form-group">
                <button class="btn btn-light" type="submit" name="submit" value="Login">Login</button>
            </div>
    </form>
    </div>
</body>
</html>
