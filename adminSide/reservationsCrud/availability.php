<?php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $selectedDate = $_GET["reservation_date"]; // Selected Date
    $head_count = $_GET["head_count"];  // Number of people
    $selectedTime = date("H:i:s", strtotime($_GET["reservation_time"]));

    // Query to get all reservations for the selected date and time
    $reservedQuery = "SELECT table_id FROM reservations WHERE reservation_date = '$selectedDate' AND reservation_time = '$selectedTime'";
    $reservedResult = mysqli_query($link, $reservedQuery);

    // Initialize an array to store reserved table IDs
    $reservedTableIDs = array();

    // Collect reserved table IDs
    if ($reservedResult) {
        while ($row = mysqli_fetch_assoc($reservedResult)) {
            $reservedTableIDs[] = $row["table_id"];
        }
    }

    // If ajax parameter is set, return JSON response
    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
        header('Content-Type: application/json');
        echo json_encode(['reserved' => $reservedTableIDs]);
        exit();
    }

    // Check available tables
    if (!empty($reservedTableIDs)) {
        $reservedTableIDsString = implode(",", $reservedTableIDs);
        $availableTables = "SELECT table_id, capacity FROM restaurant_tables WHERE capacity >= '$head_count' AND table_id NOT IN ($reservedTableIDsString)";
        $availableResult = mysqli_query($link, $availableTables);

        if ($availableResult) {
            while ($row = mysqli_fetch_assoc($availableResult)) {
                echo "Available Table ID: " . $row["table_id"] . "<br>";
                echo "Capacity: " . $row["capacity"] . "<br>";
            }
            // Construct the reservation link with all table IDs
            $reservationLink = "reservePage.php?reservation_date=$selectedDate&head_count=$head_count&reservation_time=$selectedTime&reserved_table_id=$reservedTableIDsString";
            header("Location: $reservationLink");
            exit();
        } else {
            echo "Available tables query failed: " . mysqli_error($link);
        }
    } else {
        $reservationLink = "reservePage.php?reservation_date=$selectedDate&head_count=$head_count&reservation_time=$selectedTime&reserved_table_id=0";
        header("Location: $reservationLink");
    }
}
?>