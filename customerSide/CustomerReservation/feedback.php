<?php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form inputs
    $name = htmlspecialchars(strip_tags(trim($_POST["name"])));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(strip_tags(trim($_POST["message"])));

    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        $response = "All fields are required.";
    } else {
        $stmt = $link->prepare("INSERT INTO Feedback (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $stmt->close();
            $link->close();
            // Redirect to home page (change 'index.php' to your actual home page if needed)
            header("Location: ../home/home.php");
            exit();
        } else {
            $response = "Error submitting feedback: " . $stmt->error;
        }
        $stmt->close();
    }
    $link->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Feedback Form</title>
    <style>
        /* Add this new style for response messages */
        body {
  background-image: url('../image/loginBackground.jpg');
  background-size: cover;           /* Ensures the image covers the entire area without distortion */
  background-position: center;      /* Centers the image both vertically and horizontally */
  background-repeat: no-repeat;     /* Prevents the image from repeating */
  background-attachment: fixed;     /* (Optional) Keeps the image fixed during scroll for a parallax effect */
}

  .contact-container{
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: space-evenly;
 }
 .contact-left{
    display: flex;
    flex-direction: column;
    align-items: start;
    gap: 20px;
}
.contact-left-title h2{
    font-weight: 600;
    color:#fff;
    font-size: 40px;
}


.contact-inputs{
    width: 400px;
    height: 50px;
    border: none;
    outline: none;
    padding-left: 25px;
    font-weight: 500;
    color: #666;
    border-radius: 50px;
}
.contact-left textarea{
    height: 140px;
    padding-top: 15px;
    border-radius: 20px;
}
.contact-inputs:focus{
    border: 2px solid #3969ec;
}
.contact-inputs::placeholder{
    color:#a9a9a9;
}
.contact-left button{
    display: flex;
    align-items: center;
    padding: 15px 30px;
    font-size: 16px;
    color: #fff;
    gap: 10px;
    border: none;
    border-radius: 50px;
    background: linear-gradient(270deg,#6e02b7,#140f79);
    cursor:pointer;
}
@media (max-width:800px){
    .contact-inputs{
        width: 80vw;
    }
}
        /* ... (keep rest of your existing styles) ... */
    </style>
</head>
<body>
    <div class="contact-container">
        <?php if (!empty($response)): ?>
            <div class="response"><?php echo $response; ?></div>
        <?php endif; ?>
        
        <form action="feedback.php" method="POST" class="contact-left">
            <div class="contact-left-title">
                <h2>Feedback</h2>
            </div>
            <input type="text" name="name" placeholder="Your Name" class="contact-inputs" 
                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
            <input type="email" name="email" placeholder="Your Email" class="contact-inputs"
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
            <textarea name="message" placeholder="Your Feedback" class="contact-inputs" required><?php 
                echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
