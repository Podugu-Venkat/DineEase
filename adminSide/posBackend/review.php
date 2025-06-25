<?php
session_start(); // Ensure session is started
require_once '../config.php';
include '../inc/dashHeader.php';
?>
<!-- Modified CSS for professional look -->
<style>
body {
  background: #f7f9fc;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}
.container {
  max-width: 900px;
  margin: auto;
}
h2 {
  color: #333;
  margin-bottom: 1.5rem;
}
.card {
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  background-color: #fff;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.card-body {
  padding: 1.5rem;
}
.star-rating {
  direction: rtl;
  display: inline-block;
  font-size: 1.8rem;
}
.star-rating input {
  display: none;
}
.star-rating label {
  color: #ccc;
  cursor: pointer;
  transition: color 0.2s ease;
  margin: 0 2px;
}
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
  color: #f2b600;
}
.btn-primary {
  background-color: #007bff;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 5px;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}
.btn-primary:hover {
  background-color: #0056b3;
}
</style>

<?php
$bill_id = $_GET['bill_id'] ?? '';
if (!$bill_id) {
    echo "No bill specified.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process reviews submission
    foreach ($_POST['rating'] as $item_id => $rating) {
        $rating = intval($rating);
        // Validate rating between 0 and 5
        if ($rating < 0 || $rating > 5) continue;
        $review_text = mysqli_real_escape_string($link, $_POST['review_text'][$item_id] ?? '');
        $item_id   = mysqli_real_escape_string($link, $item_id);
        $bill_id_esc = mysqli_real_escape_string($link, $bill_id);
        $insert_query = "INSERT INTO reviews (bill_id, item_id, rating, review_text) VALUES ('$bill_id_esc', '$item_id', $rating, '$review_text')";
        mysqli_query($link, $insert_query);

        // ----- Update menu_review table with new rating -------
        $select_query = "SELECT avg_rating, total_reviews FROM menu_review WHERE item_id = '$item_id'";
        $result_review = mysqli_query($link, $select_query);
        if ($result_review && mysqli_num_rows($result_review) > 0) {
            $row = mysqli_fetch_assoc($result_review);
            $current_avg = floatval($row['avg_rating']);
            $current_total = intval($row['total_reviews']);
            $new_total = $current_total + 1;
            $new_avg = round((($current_avg * $current_total) + $rating) / $new_total, 2);
            $update_query = "UPDATE menu_review SET avg_rating = $new_avg, total_reviews = $new_total WHERE item_id = '$item_id'";
            mysqli_query($link, $update_query);
        } else {
            // Insert default values then update with new review
            $default_total = 6;
            $default_avg = 4.25;
            $insert_default = "INSERT INTO menu_review (item_id, avg_rating, total_reviews) VALUES ('$item_id', $default_avg, $default_total)";
            mysqli_query($link, $insert_default);
            $new_total = $default_total + 1;
            $new_avg = round((($default_avg * $default_total) + $rating) / $new_total, 2);
            $update_query = "UPDATE menu_review SET avg_rating = $new_avg, total_reviews = $new_total WHERE item_id = '$item_id'";
            mysqli_query($link, $update_query);
        }
        // ----- End update ---------------------------------------
    }
    // Display thank-you overlay and redirect after 3 seconds
    echo "
    <div id='thankYouOverlay' style='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);display:flex;justify-content:center;align-items:center;z-index:1000;'>
        <div style='background:#fff;padding:2rem;border-radius:8px;text-align:center;'>
            <h2>Thank you for your feedback!</h2>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'http://localhost/restaurant-management/adminSide/posBackend/posTable.php';
        }, 3000);
    </script>";
    exit();
}

$query = "SELECT bi.*, m.item_name FROM bill_items bi JOIN Menu m ON bi.item_id = m.item_id WHERE bi.bill_id = '$bill_id'";
$result = mysqli_query($link, $query);
?>
<div class="container" style="margin-top: 5rem;">
    <h2>Review Your Order</h2>
    <form method="post" action="review.php?bill_id=<?php echo $bill_id; ?>">
        <?php while ($row = mysqli_fetch_assoc($result)) { 
              $item_id = $row['item_id'];
              $item_name = $row['item_name'];
        ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5><?php echo $item_name; ?></h5>
                <p>Rate this item:</p>
                <!-- Modified star rating interface -->
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 0; $i--) { ?>
                        <input type="radio" name="rating[<?php echo $item_id; ?>]" id="rating_<?php echo $item_id . '_' . $i; ?>" value="<?php echo $i; ?>" required>
                        <label for="rating_<?php echo $item_id . '_' . $i; ?>">&#9733;</label>
                    <?php } ?>
                </div>
                <div class="form-group mt-2">
                    <label for="review_<?php echo $item_id; ?>">Review (optional):</label>
                    <textarea class="form-control" name="review_text[<?php echo $item_id; ?>]" id="review_<?php echo $item_id; ?>" rows="2"></textarea>
                </div>
            </div>
        </div>
        <?php } ?>
        <button type="submit" class="btn btn-primary">Submit Reviews</button>
    </form>
</div>
<?php include '../inc/dashFooter.php'; ?>