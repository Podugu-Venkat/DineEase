<?php
session_start(); // Ensure session is started
require_once '../inc/dashHeader.php';
require_once '../config.php';

// Fetch inventory items from the database
$query = "SELECT * FROM Inventory ORDER BY item_name";
$result = mysqli_query($link, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
body {
    background-color: #ffffff;
    color: #000000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.wrapper {
    width: 100%;
    padding: 40px 20px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

.container-fluid {
    width: 100%;
    max-width: 1200px;
    background-color: #ffffff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #000000;
}

h2 {
    color: #000000;
    margin-bottom: 20px;
}

.btn-outline-dark {
    border: 1px solid #000000;
    color: #000000;
    transition: all 0.3s ease-in-out;
}

.btn-outline-dark:hover {
    background-color: #000000;
    color: #ffffff;
}

.table {
    margin-top: 20px;
    background-color: #ffffff;
    color: #000000;
    border: 1px solid #000000;
    border-radius: 8px;
    overflow: hidden;
}

.table th {
    background-color: #f2f2f2;
    color: #000000;
    text-align: center;
    border-bottom: 1px solid #000000;
}

.table td {
    text-align: center;
    vertical-align: middle;
    color: #000000;
    border-top: 1px solid #000000;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: #ffffff;
}

.table tr {
    color: #000000;
}

.fa-pencil,
.fa-trash {
    margin: 0 10px;
    font-size: 18px;
    transition: color 0.2s;
    color: #000000;
}

.fa-pencil:hover {
    color: #555555;
}

.fa-trash:hover {
    color: #cc0000;
}

.alert-danger {
    background-color: #ffdddd;
    color: #a94442;
    border: 1px solid #a94442;
    padding: 15px;
    border-radius: 6px;
}

    </style>
</head>
<body>

<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Inventory Management</h2>
                    <a href="create_inventory_item.php" class="btn btn-outline-dark"><i class="fa fa-plus"></i> Add Item</a>
                </div>

                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    echo '<table class="table table-bordered table-striped">';
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Item ID</th>";
                    echo "<th>Item Name</th>";
                    echo "<th>Category</th>";
                    echo "<th>Quantity</th>";
                    echo "<th>Unit</th>";
                    echo "<th>Purchase Price</th>";
                    echo "<th>Last Restock Date</th>";
                    echo "<th>Actions</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['item_id'] . "</td>";
                        echo "<td>" . $row['item_name'] . "</td>";
                        echo "<td>" . $row['category'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['unit'] . "</td>";
                        echo "<td>" . $row['purchase_price'] . "</td>";
                        echo "<td>" . $row['last_restock_date'] . "</td>";
                        echo "<td>";
                        echo '<a href="update_inventory_item.php?id=' . $row['item_id'] . '" title="Update Record" data-toggle="tooltip"><span class="fa fa-pencil text-black"></span></a>';
                        echo '<a href="delete_inventory_item.php?id=' . $row['item_id'] . '" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash text-black"></span></a>';
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                    mysqli_free_result($result);
                } else {
                    echo '<div class="alert alert-danger"><em>No inventory items were found.</em></div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include '../inc/dashFooter.php'; ?>
</body>
</html>