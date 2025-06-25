<?php
// This file handles updating inventory when an order is processed
require_once '../config.php';

// Function to deduct ingredients from inventory based on menu items ordered
function deductIngredientsFromInventory($menuItemId, $quantity, $conn) {
    // Get all ingredients required for this menu item
    $sql = "SELECT ri.ingredient_id, ri.quantity_required * ? AS total_required
            FROM recipe_ingredients ri
            WHERE ri.menu_item_id = ?";
   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $quantity, $menuItemId);
    $stmt->execute();
    $result = $stmt->get_result();
   
    // For each ingredient, deduct the required amount from inventory
    while ($row = $result->fetch_assoc()) {
        $ingredientId = $row['ingredient_id'];
        $requiredAmount = $row['total_required'];
       
        // Update inventory
        $updateSql = "UPDATE inventory
                      SET quantity = quantity - ?,
                          last_updated = NOW()
                      WHERE id = ?";
       
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("di", $requiredAmount, $ingredientId);
        $updateStmt->execute();
       
        // Check if this update caused inventory to go below threshold
        checkLowStockAlert($ingredientId, $conn);
       
        // Log the usage for tracking
        logInventoryUsage($ingredientId, $requiredAmount, $menuItemId, $conn);
    }
}

// Function to check if inventory is below threshold and create alert if needed
function checkLowStockAlert($ingredientId, $conn) {
    $sql = "SELECT i.name, i.quantity, i.threshold_quantity
            FROM inventory i
            WHERE i.id = ? AND i.quantity <= i.threshold_quantity";
   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ingredientId);
    $stmt->execute();
    $result = $stmt->get_result();
   
    if ($row = $result->fetch_assoc()) {
        // Create alert in the alerts table
        $alertSql = "INSERT INTO inventory_alerts (ingredient_id, alert_message, alert_date, is_resolved)
                     VALUES (?, CONCAT('Low stock alert for ', ?, ' - Current quantity: ', ?), NOW(), 0)";
       
        $alertStmt = $conn->prepare($alertSql);
        $message = $row['name'];
        $quantity = $row['quantity'];
        $alertStmt->bind_param("isd", $ingredientId, $message, $quantity);
        $alertStmt->execute();
    }
}

// Function to log inventory usage for reporting
function logInventoryUsage($ingredientId, $quantity, $menuItemId, $conn) {
    $sql = "INSERT INTO inventory_usage_log (ingredient_id, quantity_used, menu_item_id, usage_date)
            VALUES (?, ?, ?, NOW())";
   
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idi", $ingredientId, $quantity, $menuItemId);
    $stmt->execute();
}

// Process the request if it's a POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the order details from the POST data
    $orderId = isset($_POST['order_id']) ? $_POST['order_id'] : null;
   
    if ($orderId) {
        // Get all items in this order
        $sql = "SELECT oi.menu_item_id, oi.quantity
                FROM order_items oi
                WHERE oi.order_id = ?";
       
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
       
        // Process each menu item
        while ($row = $result->fetch_assoc()) {
            deductIngredientsFromInventory($row['menu_item_id'], $row['quantity'], $conn);
        }
       
        echo json_encode(['success' => true, 'message' => 'Inventory updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    }
}
?>

