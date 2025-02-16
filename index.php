<?PHP include('header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSys - Login</title>
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
        table {
            margin: 0 auto;
        }
        td {
            padding: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #7851A9;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #7851A9;
        }
        .footer-link {
            margin-top: 15px;
            font-size: 14px;
        }
        .footer-link a {
            text-decoration: none;
            color: #7851A9;
        }
        .footer-link a:hover {
            text-decoration: thickness;
        }
        .logo {
            width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="images/uitm.png" alt="BookSys Logo" class="logo">
        <h2>Welcome to BookSys</h2>
        <h3>Log In</h3>
        <form action="" method="POST">
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
                    <td align="right">ID:</td>
                    <td><input type="text" name="id" required></td>
                </tr>
                <tr>
                    <td align="right">Password:</td>
                    <td><input type="password" name="password" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Login"></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="footer-link">| <a href="register.php">Register as a New User</a> |</td>

                </tr>
            </table>
        </form>
    </div>
</body>
</html>

<?PHP
// L1: Check if POST data exists
if (!empty($_POST)) {
    // L2: Retrieve POST data
    $id = $_POST['id'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    // L3: Include the connection file
    include('connection.php');

    // Encrypt password (optional)
    $password = base64_encode($password);

    // L4: Query to find matching user
    $sql = mysqli_query($condb, "SELECT * FROM users WHERE id='$id' AND password='$password' AND user_type='$user_type' AND status != 'inactive' LIMIT 1");

    // L5: Check the number of rows
    if (mysqli_num_rows($sql) == 1) {
        $row = mysqli_fetch_array($sql);

        // Set session variables
        session_start();
        $_SESSION["user_name"] = $row['name'];
        $_SESSION["user_id"] = $row['id'];
        $_SESSION["user_type"] = $row['user_type'];

        // Redirect to main menu
        echo "<script>window.location.href='main_menu.php';</script>";
    } else {
        // Login failed
        echo "<script>alert('Login Failed'); window.history.back();</script>";
    }

    // Close the connection
    mysqli_close($condb);
}
?>

<?php include('footer.php'); ?>
