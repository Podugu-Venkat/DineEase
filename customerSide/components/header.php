<?php
session_start(); // Ensure session is started at the beginning
require_once '../config.php'; // Include database configuration

// Fetch data from the new table
$query = "SELECT * FROM menu_with_reviews WHERE item_category = 'Main Dishes'";
$result = mysqli_query($link, $query);
if (!$result) {
    die("Error fetching main dishes: " . mysqli_error($link));
}
$mainDishes = mysqli_fetch_all($result, MYSQLI_ASSOC);

$query = "SELECT * FROM menu_with_reviews WHERE item_category = 'Side Snacks'";
$result = mysqli_query($link, $query);
if (!$result) {
    die("Error fetching side dishes: " . mysqli_error($link));
}
$sides = mysqli_fetch_all($result, MYSQLI_ASSOC);

$query = "SELECT * FROM menu_with_reviews WHERE item_category = 'Drinks'";
$result = mysqli_query($link, $query);
if (!$result) {
    die("Error fetching drinks: " . mysqli_error($link));
}
$drinks = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Home</title>
</head>

<body>
    <!-- Header -->
    <section id="header">
        <div class="header container">
            <div class="nav-bar">
                <div class="brand">
                    <a class="nav-link" href="../home/home.php#hero">
                        <img src="../image/logo.png" alt="Logo" style="width:150px; height:auto;">
                    </a>
                </div>
                <div class="nav-list">
                    <div class="hamburger">
                        <div class="bar"></div>
                    </div>
                    <div class="navbar-container">
                        <div class="navbar">
                            <ul>
                                <?php
                                $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                ?>
                                <li><a href="<?= strpos($current_url, "../../home/home.php") !== false ? "#hero" : "../home/home.php" ?>" data-after="Home">Home</a></li>
                                <?php if (strpos($current_url, "../../home/home.php") !== false): ?>
                                    <li><a href="#projects" data-after="Projects">Menu</a></li>
                                    <li><a href="#about" data-after="About">About</a></li>
                                    <li><a href="#contact" data-after="Contact">Contact</a></li>
                                <?php else: ?>
                                    <li><a href="../CustomerReservation/reservePage.php" data-after="Service">Reservation</a></li>
                                    <li><a href="../../adminSide/StaffLogin/login.php" data-after="Staff">Staff</a></li>
                                <?php endif; ?>
                                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                                    <li><a href="../CustomerReservation/feedback.php" data-after="Feedback">Feedback</a></li>
                                <?php endif; ?>
                                <div class="dropdown">
                                    <button class="dropbtn">ACCOUNT <i class="fa fa-caret-down" aria-hidden="true"></i></button>
                                    <div class="dropdown-content">
                                        <?php
                                        $account_id = $_SESSION['account_id'] ?? null;
                                        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $account_id != null) {
                                            $query = "SELECT member_name, points FROM memberships WHERE account_id = $account_id";
                                            $result = mysqli_query($link, $query);
                                            if ($result) {
                                                $row = mysqli_fetch_assoc($result);
                                                if ($row) {
                                                    $member_name = $row['member_name'];
                                                    $points = $row['points'];
                                                    $vip_status = ($points >= 1000) ? 'VIP' : 'Regular';
                                                    $vip_tooltip = ($vip_status === 'Regular') ? ($points < 1000 ? (1000 - $points) . ' points to VIP ' : 'You are eligible for VIP') : '';
                                                    echo "<p class='logout-link' style='font-size:1.3em; margin-left:15px; padding:5px; color:white;'>$member_name</p>";
                                                    echo "<p class='logout-link' style='font-size:1.3em; margin-left:15px;padding:5px;color:white;'>$points Points</p>";
                                                    echo "<p class='logout-link' style='font-size:1.3em; margin-left:15px;padding:5px; color:white;'>$vip_status";
                                                    if ($vip_status === 'Regular') {
                                                        echo " <span class='tooltip'>$vip_tooltip</span>";
                                                    }
                                                    echo "</p>";
                                                } else {
                                                    echo "Member not found.";
                                                }
                                            } else {
                                                echo "Error: " . mysqli_error($link);
                                            }
                                            echo '<a class="logout-link" style="color: white; font-size:1.3em;" href="../customerLogin/logout.php">Logout</a>';
                                        } else {
                                            echo '<a class="signin-link" style="color: white; font-size:15px;" href="../customerLogin/register.php">Sign Up </a>';
                                            echo '<a class="login-link" style="color: white; font-size:15px;" href="../customerLogin/login.php">Log In</a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Header -->
