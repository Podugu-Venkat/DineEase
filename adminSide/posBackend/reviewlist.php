<?php
session_start();
require_once '../posBackend/checkIfLoggedIn.php';
require_once '../config.php';
include '../inc/dashHeader.php';

// Updated query to get item name and avg rating from menu_review
$query = "SELECT m.item_name, mr.avg_rating 
          FROM menu_review mr 
          JOIN menu m ON mr.item_id = m.item_id 
          ORDER BY m.item_name";
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
    <h2>Menu Reviews</h2>
    <?php
    if($result && mysqli_num_rows($result) > 0){
        echo '<table class="table table-bordered table-striped">';
        echo "<thead>";
        echo "<tr>";
        echo "<th>Item Name</th>";
        echo "<th>Rating</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
         while($row = mysqli_fetch_assoc($result)){
             echo "<tr>";
             echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
             echo "<td>" . htmlspecialchars($row['avg_rating']) . "</td>";
             echo "</tr>";
         }
         echo "</tbody>";
         echo "</table>";
         mysqli_free_result($result);
    } else {
         echo '<div class="alert alert-danger"><em>No reviews were found.</em></div>';
    }
    mysqli_close($link);
    ?>
</div>
<?php include '../inc/dashFooter.php'; ?>
</body>
</html>
