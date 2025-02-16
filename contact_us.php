<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
        // Simulate email sending (Replace with actual email functionality if needed)
        $success_message = "Thank you, $name! Your message has been sent.";
    } else {
        $error_message = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | BookSys</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; }
        h2 { color: #5A2D82; }
        p { line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        form { display: flex; flex-direction: column; }
        input, textarea { margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 100%; }
        button { background: #5A2D82; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #482366; }
        .message { margin-top: 10px; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Contact Us</h2>
        <p>Have a question or need support? Fill out the form below and we'll get back to you as soon as possible.</p>

        <?php if (isset($success_message)) echo "<p class='message' style='color: green;'>$success_message</p>"; ?>
        <?php if (isset($error_message)) echo "<p class='message' style='color: red;'>$error_message</p>"; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
            <button type="submit">Send Message</button>
        </form>

        <div class="footer">
            <p>&copy; <?php echo date("Y"); ?> BookSys. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
