<?php
session_start();
require_once '../config.php';
include '../inc/dashHeader.php';

$bill_id = $_GET['bill_id'];
$staff_id = $_GET['staff_id'];
$member_id = intval($_GET['member_id']);
$reservation_id = $_GET['reservation_id'];
$paid = false;

// Calculate total
$cart_query = "SELECT bi.*, m.item_price FROM bill_items bi
               JOIN Menu m ON bi.item_id = m.item_id
               WHERE bi.bill_id = '$bill_id'";
$cart_result = mysqli_query($link, $cart_query);
$cart_total = 0;
$tax = 0.1;

if ($cart_result && mysqli_num_rows($cart_result) > 0) {
    while ($cart_row = mysqli_fetch_assoc($cart_result)) {
        $cart_total += $cart_row['item_price'] * $cart_row['quantity'];
    }
}
$GRANDTOTAL = $cart_total + ($cart_total * $tax);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    // Check if already paid
    $check = mysqli_query($link, "SELECT payment_time FROM Bills WHERE bill_id = $bill_id");
    $row = mysqli_fetch_assoc($check);
    if ($row && $row['payment_time']) {
        $message = "Bill already paid.";
    } else {
        $currentTime = date('Y-m-d H:i:s');
        $updateQuery = "UPDATE Bills SET payment_method = 'qr', payment_time = '$currentTime',
                        staff_id = $staff_id, member_id = $member_id, reservation_id = $reservation_id
                        WHERE bill_id = $bill_id";
        if (mysqli_query($link, $updateQuery)) {
            $points = intval($GRANDTOTAL);
            mysqli_query($link, "UPDATE Memberships SET points = points + $points WHERE member_id = $member_id");
            $paid = true;
        } else {
            $message = "Error: " . mysqli_error($link);
        }
    }
}
?>

<div class="container mt-5">
    <h3 class="text-center">QR Code Payment</h3>

    <?php if ($paid): ?>
        <div class="alert alert-success">QR Payment successful.</div>
        <a href="posTable.php" class="btn btn-dark">Back to Tables</a>
        <a href="review.php?bill_id=<?= $bill_id ?>" class="btn btn-info ml-2">Review Order</a>
        <a href="receipt.php?bill_id=<?= $bill_id ?>" class="btn btn-light">Print Receipt</a>
    <?php elseif (!empty($message)): ?>
        <div class="alert alert-warning"><?= $message ?></div>
        <a href="posTable.php" class="btn btn-dark">Back to Tables</a>
        <a href="review.php?bill_id=<?= $bill_id ?>" class="btn btn-info ml-2">Review Order</a>
    <?php else: ?>
        <div class="text-center mb-4">
            <p>Please scan the QR code to complete payment of <strong>Rs. <?= number_format($GRANDTOTAL, 2) ?></strong></p>
            <img src="payment_qr.png" alt="Scan to Pay" class="img-fluid" style="max-height: 300px;">
        </div>
        <form method="post" class="text-center">
            <input type="hidden" name="confirm_payment" value="1">
            <button type="submit" class="btn btn-success">Confirm Payment Received</button>
            <a href="posTable.php" class="btn btn-secondary ml-2">Cancel</a>
        </form>
    <?php endif; ?>
</div>

<?php include '../inc/dashFooter.php'; ?>
