<?php
session_start();
require_once '../inc/dashHeader.php';
require_once '../config.php';

// Initialize variables
$item_name = $category = $quantity = $unit = $purchase_price = $last_restock_date = "";
$item_name_err = $quantity_err = $purchase_price_err = "";

// Check if item_id is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $item_id = $_GET['id'];

    // Retrieve item details
    $sql = "SELECT * FROM Inventory WHERE item_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_item_id);
        $param_item_id = $item_id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $item_name = $row['item_name'];
                $category = $row['category'];
                $quantity = $row['quantity'];
                $unit = $row['unit'];
                $purchase_price = $row['purchase_price'];
                $last_restock_date = $row['last_restock_date'];
            } else {
                echo "Item not found.";
                exit();
            }
        } else {
            echo "Error retrieving item details.";
            exit();
        }
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST["item_name"]);
    $category = trim($_POST["category"]);
    $quantity = floatval($_POST["quantity"]);
    $unit = trim($_POST["unit"]);
    $purchase_price = floatval($_POST["purchase_price"]);
    $last_restock_date = $_POST["last_restock_date"];

    $update_sql = "UPDATE Inventory SET item_name=?, category=?, quantity=?, unit=?, purchase_price=?, last_restock_date=? WHERE item_id=?";
    if ($stmt = mysqli_prepare($link, $update_sql)) {
        mysqli_stmt_bind_param($stmt, "ssddssi", $item_name, $category, $quantity, $unit, $purchase_price, $last_restock_date, $item_id);

        if (mysqli_stmt_execute($stmt)) {
            header("location: inventory_panel.php");
            exit();
        } else {
            echo "Error updating item: " . mysqli_error($link);
        }
    }
}
?>

<!-- Add new CSS styling -->
<style>
body {
    background-color: #ffffff;
    color: #000000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    padding: 40px 20px;
}
.container-fluid {
    width: 100%;
    max-width: 600px;
    background-color: #ffffff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border: 1px solid #000000;
}
</style>

<!-- HTML Form -->
<div class="wrapper">
    <div class="container-fluid pt-5">
        <div class="row">
            <div class="card p-4 shadow">
                <h2 class="mb-3 text-center">Update Inventory Item</h2>
                <p class="text-center">Please edit and submit to update the record.</p>

                <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="item_name" class="form-control" value="<?php echo $item_name; ?>">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" class="form-control" value="<?php echo $category; ?>">
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="text" name="quantity" class="form-control" value="<?php echo $quantity; ?>">
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" name="unit" class="form-control" value="<?php echo $unit; ?>">
                    </div>
                    <div class="form-group">
                        <label>Purchase Price</label>
                        <input type="text" name="purchase_price" class="form-control" value="<?php echo $purchase_price; ?>">
                    </div>
                    <div class="form-group">
                        <label>Last Restock Date</label>
                        <input type="date" name="last_restock_date" class="form-control" value="<?php echo $last_restock_date; ?>">
                    </div>
                    <div class="form-group text-center">
                        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="inventory_panel.php" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../inc/dashFooter.php'; ?>
