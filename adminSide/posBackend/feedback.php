<?php
session_start();
require_once '../posBackend/checkIfLoggedIn.php';
require_once '../config.php';
include '../inc/dashHeader.php';

// Query to get feedback data
$query = "SELECT name, email, message FROM feedback ORDER BY submitted_at";
$result = mysqli_query($link, $query);
?>
<html>
<head>
    <style>
        .wrapper { width: 50%; padding-left: 200px; padding-top: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Feedback</h2>
    <?php
    if($result && mysqli_num_rows($result) > 0){
        echo '<table class="table table-bordered table-striped">';
        echo "<thead>";
        echo "<tr>";
        echo "<th>Name</th>";
        echo "<th>Gmail</th>";
        echo "<th>Feedback</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
         while($row = mysqli_fetch_assoc($result)){
             echo "<tr>";
             echo "<td>" . htmlspecialchars($row['name']) . "</td>";
             echo "<td>" . htmlspecialchars($row['email']) . "</td>";
             echo "<td>" . htmlspecialchars($row['message']) . "</td>";
             echo "</tr>";
         }
         echo "</tbody>";
         echo "</table>";
         mysqli_free_result($result);
    } else {
         echo '<div class="alert alert-danger"><em>No feedback was found.</em></div>';
    }
    
    // New code to display reviews table below feedback
    // Changed query: Join with menu table to display item_name instead of item_id
    $query2 = "SELECT r.review_id, m.item_name, r.review_text FROM reviews r JOIN menu m ON r.item_id = m.item_id WHERE r.review_text IS NOT NULL AND TRIM(r.review_text) <> '' ORDER BY r.created_at";
    $result2 = mysqli_query($link, $query2);
    echo "<h2>Reviews</h2>";
    if($result2 && mysqli_num_rows($result2) > 0){
         echo '<table class="table table-bordered table-striped">';
         echo "<thead><tr><th>Review ID</th><th>Item Name</th><th>Review</th></tr></thead>";
         echo "<tbody>";
         while($row = mysqli_fetch_assoc($result2)){
             echo "<tr>";
             echo "<td>" . htmlspecialchars($row['review_id']) . "</td>";
             echo "<td>" . htmlspecialchars($row['item_name']) . "</td>"; // Changed line: display item_name
             echo "<td>" . htmlspecialchars($row['review_text']) . "</td>";
             echo "</tr>";
         }
         echo "</tbody>";
         echo "</table>";
         mysqli_free_result($result2);
    } else {
         echo '<div class="alert alert-danger"><em>No reviews were found.</em></div>';
    }
    mysqli_close($link);
    ?>
</div>
<?php include '../inc/dashFooter.php'; ?>
</body>
</html>
