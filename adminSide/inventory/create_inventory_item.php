<?php
session_start();
require_once '../inc/dashHeader.php';
require_once '../config.php';

$item_name = $category = $quantity = $unit = $purchase_price = $last_restock_date = "";
$item_name_err = $quantity_err = $purchase_price_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["item_name"]))) {
        $item_name_err = "Please enter an item name.";
    } else {
        $item_name = trim($_POST["item_name"]);
    }

    if (empty(trim($_POST["quantity"]))) {
        $quantity_err = "Please enter the quantity.";
    } elseif (!filter_var(trim($_POST["quantity"]), FILTER_VALIDATE_FLOAT)) {
        $quantity_err = "Please enter a valid number.";
    } else {
        $quantity = trim($_POST["quantity"]);
    }

    if (empty(trim($_POST["purchase_price"]))) {
        $purchase_price_err = "Please enter the purchase price.";
    } elseif (!filter_var(trim($_POST["purchase_price"]), FILTER_VALIDATE_FLOAT)) {
        $purchase_price_err = "Please enter a valid number.";
    } else {
        $purchase_price = trim($_POST["purchase_price"]);
    }

    $category = trim($_POST["category"]);
    $unit = trim($_POST["unit"]);
    $last_restock_date = trim($_POST["last_restock_date"]);

    if (empty($item_name_err) && empty($quantity_err) && empty($purchase_price_err)) {
        $sql = "INSERT INTO Inventory (item_name, category, quantity, unit, purchase_price, last_restock_date) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssdsss", $item_name, $category, $quantity, $unit, $purchase_price, $last_restock_date);
            if (mysqli_stmt_execute($stmt)) {
                header("location: inventory_panel.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
?>

<style>
    body {
    background-color: #ffffff;
    color: #000000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.form-wrapper {
    width: 1300px;
    padding-left: 200px;
    padding-top: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.form-container {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
    border: 1px solid #ccc;
}

h2 {
    color: #333;
}

.form-control {
    background-color: #f8f8f8;
    border: 1px solid #ccc;
    color: #333;
}

.form-control:focus {
    border-color: #000;
    box-shadow: 0 0 5px #000;
}

.btn-primary {
    background-color: #000;
    border-color: #000;
    color: #fff;
}

.btn-primary:hover {
    background-color: #333;
}

.btn-secondary {
    background-color: #f1f1f1;
    border-color: #ccc;
    color: #333;
}

.btn-secondary:hover {
    background-color: #e1e1e1;
}

.invalid-feedback {
    color: #ff6b6b;
}

</style>

<div class="form-wrapper">
    <div class="form-container">
        <h2 class="text-center mb-4">Add Inventory Item</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group mb-3">
                <label>Item Name</label>
                <input type="text" name="item_name" class="form-control <?php echo (!empty($item_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($item_name); ?>">
                <span class="invalid-feedback"><?php echo $item_name_err; ?></span>
            </div>
            <div class="form-group mb-3">
                <label>Category</label>
                <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($category); ?>">
            </div>
            <div class="form-group mb-3">
                <label>Quantity</label>
                <input type="text" name="quantity" class="form-control <?php echo (!empty($quantity_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($quantity); ?>">
                <span class="invalid-feedback"><?php echo $quantity_err; ?></span>
            </div>
            <div class="form-group mb-3">
                <label>Unit</label>
                <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($unit); ?>">
            </div>
            <div class="form-group mb-3">
                <label>Purchase Price</label>
                <input type="text" name="purchase_price" class="form-control <?php echo (!empty($purchase_price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($purchase_price); ?>">
                <span class="invalid-feedback"><?php echo $purchase_price_err; ?></span>
            </div>
            <div class="form-group mb-4">
                <label>Last Restock Date</label>
                <input type="date" name="last_restock_date" class="form-control" value="<?php echo htmlspecialchars($last_restock_date); ?>">
            </div>
            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="inventory_panel.php" class="btn btn-secondary ml-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../inc/dashFooter.php'; ?>