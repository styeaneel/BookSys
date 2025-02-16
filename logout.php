<?php
session_start();

if (isset($_POST['confirm_logout'])) {
    // Destroy the session and redirect to login.php with a success message
    session_destroy();
    header("Location: login.php?message=Logout successful");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        h2 {
            color: #5A2D82;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .btn-yes {
            background-color: #28a745;
            color: white;
        }
        .btn-no {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Are you sure you want to log out?</h2>
    <form method="POST">
        <button type="submit" name="confirm_logout" class="btn btn-yes">Yes</button>
        <a href="javascript:history.back()" class="btn btn-no">No</a>
    </form>
</div>

</body>
</html>

