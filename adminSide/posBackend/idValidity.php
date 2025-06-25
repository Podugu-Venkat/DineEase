<?php
session_start(); // Ensure session is started
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Staff Member Reservation Validity</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Check Staff Member Reservation Validity</h2>
        <form action="" method="post">
            <div class="form-group">
                <?php
                    $currentStaffId = $_SESSION['logged_account_id'] ?? "Please Login"; 
                ?>
                <label for="staffId">Staff ID:</label>
                <input type="text" id="staffId" name="staffId" class="form-control" 
                       value="<?= $currentStaffId ?>" readonly required>
            </div>
            <div class="form-group">
                <label for="memberId">Member ID:</label>
                <input type="text" id="memberId" name="memberId" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="reservationId">Reservation ID:</label>
                <input type="text" id="reservationId" name="reservationId" class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-dark">Check Validity</button>
                <a class="btn btn-danger" href="javascript:window.history.back();">Cancel</a>
                <a class="btn btn-link" href="posTable.php">Tables Page</a>
            </div>
        </form>
    </div>

<div class="container mt-3">
    <?php
    // Include your database connection configuration
    require_once('../config.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $staffId = $_POST['staffId'];
        $memberId = $_POST['memberId'];
        $reservationId = $_POST['reservationId'];
        $bill_id = $_GET['bill_id'];

        // Check if the staff ID exists in the database
        $query = "SELECT * FROM Staffs WHERE staff_id = '$staffId'";
        $result = mysqli_query($link, $query);
        if (!$result) {
             echo "Error: " . mysqli_error($link);
        } else {
             $staffExists = mysqli_num_rows($result) > 0;

             // Validate member ID with MySQL
             $query = "SELECT * FROM Memberships WHERE member_id = '$memberId'";
             $result = mysqli_query($link, $query);
             if (!$result) {
                 echo "Error: " . mysqli_error($link);
             } else {
                 $memberExists = mysqli_num_rows($result) > 0;
             }

             // Validate reservation ID with MySQL
             $query = "SELECT * FROM Reservations WHERE reservation_id = '$reservationId'";
             $result = mysqli_query($link, $query);
             if (!$result) {
                 echo "Error: " . mysqli_error($link);
             } else {
                 $reservationExists = mysqli_num_rows($result) > 0;
             }

             if ($staffExists && $memberExists && $reservationExists) {
                 echo '<div class="alert alert-success" role="alert">';
                 echo "Staff, member, and reservation are valid.";
                 echo '</div>';
                 echo '<div class="mt-3">';
                 echo '<a href="posCashPayment.php?bill_id=' . $bill_id . '&staff_id=' . $staffId . '&member_id=' . $memberId . '&reservation_id=' . $reservationId . '" class="btn btn-success">Cash</a>';
                 echo '<a href="posCardPayment.php?bill_id=' . $bill_id . '&staff_id=' . $staffId . '&member_id=' . $memberId . '&reservation_id=' . $reservationId . '" class="btn btn-primary ml-2">Credit Card</a>';
                 echo '<a href="posQrPayment.php?bill_id=' . $bill_id . '&staff_id=' . $staffId . '&member_id=' . $memberId . '&reservation_id=' . $reservationId . '" class="btn btn-info ml-2">QR Code</a>';
                 echo '</div>';
             } else {
                 echo "Invalid staff, member, or reservation.";
             }
        }
    }
    ?>
</div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
