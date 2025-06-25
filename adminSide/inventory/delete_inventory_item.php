<?php
require_once "../config.php";

// Check if the item_id parameter is set in the URL
if (isset($_GET['id'])) {
    // Get the item_id from the URL and sanitize it
    $item_id = intval($_GET['id']);

    // Prepare a delete statement
    $sql = "DELETE FROM Inventory WHERE item_id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_item_id);

        // Set parameters
        $param_item_id = $item_id;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Records deleted successfully. Redirect to landing page
            header("location: inventory_panel.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
} else {
    // Check existence of id parameter
    echo "Error: ID parameter not found.";
}
?>