<?php
// Start the session
session_start();

// Include database connection
include('connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

    // Encrypt password (use password_hash() in production)
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the database
    $sql = "INSERT INTO users (user_type, first_name, last_name, email, password, address, phone_number) 
            VALUES ('$user_type', '$first_name', '$last_name', '$email', '$password', '$address', '$phone_number')";

    if (mysqli_query($conn, $sql)) {
        // Registration successful
        $_SESSION['success_message'] = "Registration successful! Please log in.";
        header('Location: login.php');
        exit();
    } else {
        // Error in registration
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>

<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSys - Sign Up</title>
    <style>
        body {
            font-family: Georgia, Serif;
            background-color: #FCFCFC;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            padding: 20px;
            background-color: #E5CDFB;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        h2 {
            color: #000000;
        }
        .logo {
            width: 200px;
            margin-bottom: 20px;
        }
        .footer-link a {
            text-decoration: none;
            color: #7851A9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="images/uitm.png" alt="BookSys Logo" class="logo">
        <h2>Welcome to BookSys</h2>
        <h3>Sign Up as a New User</h3>

        <?php
        // Display error message if there is one
        if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }

        // Display success message if user is successfully registered
        if (isset($_SESSION['success_message'])) {
            echo "<p class='success'>" . $_SESSION['success_message'] . "</p>";
            unset($_SESSION['success_message']);
        }
        ?>

        <form action="sign_up.php" method="POST">
            <table>
                <tr>
                    <td align="right">User Type:</td>
                    <td>
                        <select name="user_type" required>
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="right">First Name:</td>
                    <td><input type="text" name="first_name" required></td>
                </tr>
                <tr>
                    <td align="right">Last Name:</td>
                    <td><input type="text" name="last_name" required></td>
                </tr>
                <tr>
                    <td align="right">Email:</td>
                    <td><input type="email" name="email" required></td>
                </tr>
                <tr>
                    <td align="right">Password:</td>
                    <td><input type="password" name="password" required></td>
                </tr>
                <tr>
                    <td align="right">Address:</td>
                    <td><textarea name="address" required></textarea></td>
                </tr>
                <tr>
                    <td align="right">Phone Number:</td>
                    <td><input type="text" name="phone_number" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Sign Up"></td>
                </tr>
            </table>
        </form>
        <p class="footer-link">Already have an account? <a href="login.php">Log in here</a>.</p>
    </div>

    <footer>
        <p>&copy; 2025 BookSys. All Rights Reserved.</p>
    </footer>
</body>
</html>

<?php include('footer.php'); ?>
