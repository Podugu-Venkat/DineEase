<?php
require_once '../../config.php';

if (isset($_GET['action']) && $_GET['action'] == 'set_time_ended' && isset($_GET['kitchen_id'])) {
    $kitchen_id = $_GET['kitchen_id'];

    // 1. Get item_id and quantity from Kitchen
    $orderQuery = "SELECT item_id, quantity FROM Kitchen WHERE kitchen_id = '$kitchen_id'";
    $orderResult = mysqli_query($link, $orderQuery);

    if ($orderResult && mysqli_num_rows($orderResult) > 0) {
        $orderRow = mysqli_fetch_assoc($orderResult);
        $item_id = $orderRow['item_id'];
        $order_quantity = $orderRow['quantity'];

        // 2. Get all ingredients from Recipe
        $recipeQuery = "SELECT inventory_item_id, quantity_required FROM Recipe WHERE menu_item_id = '$item_id'";
        $recipeResult = mysqli_query($link, $recipeQuery);

        while ($recipeRow = mysqli_fetch_assoc($recipeResult)) {
            $inventory_item_id = $recipeRow['inventory_item_id'];
            $quantity_required = $recipeRow['quantity_required'];

            $total_to_deduct = $quantity_required * $order_quantity;

            // 3. Update Inventory quantity
            $updateInventory = "UPDATE Inventory SET quantity = quantity - $total_to_deduct WHERE item_id = '$inventory_item_id'";
            mysqli_query($link, $updateInventory);
        }

        // 4. Set time_ended
        $timeNow = date('Y-m-d H:i:s');
        $updateQuery = "UPDATE Kitchen SET time_ended = '$timeNow' WHERE kitchen_id = '$kitchen_id'";
        mysqli_query($link, $updateQuery);
    }
}

header('Location: ../../panel/kitchen-panel.php');
exit();