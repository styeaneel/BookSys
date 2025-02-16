<?php
// Start the session to store user data
session_start();

// Include database connection
include('connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize POST data
    $user_type = trim($_POST['user_type']);
    $id = trim($_POST['id']);
    $password = trim($_POST['password']);

    // Validate required fields
    if (empty($user_type) || empty($id) || empty($password)) {
        $login_error = "All fields are required!";
    } else {
        // Check if the user_type is valid
        if (!in_array($user_type, ['staff', 'student'])) {
            $login_error = "Invalid user type selected!";
        } else {
            // Prepare and execute the query to find the user
            $sql = "SELECT * FROM users WHERE id = ? AND user_type = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $id, $user_type);
            $stmt->execute();
            $result = $stmt->get_result();

            // If a matching user is found
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                // Verify the password using password_verify
                if (password_verify($password, $row['password'])) {
                    // Store user data in the session
                    $_SESSION['user_name'] = $row['first_name'] . ' ' . $row['last_name'];
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_type'] = $row['user_type'];

                    // Redirect the user based on their role
                    if ($user_type === 'staff') {
                        header('Location: staff_main_menu.php');
                    } elseif ($user_type === 'student') {
                        header('Location: main_menu.php');
                    }
                    exit();
                } else {
                    $login_error = "Invalid login credentials!";
                }
            } else {
                $login_error = "Invalid login credentials!";
            }
        }
    }
}
?>

<?php include('header.php'); ?>

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
            text-decoration: underline;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
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
        <img src="images/logo.jpg" alt="BookSys Logo" class="logo">
        <h2>Welcome to BookSys</h2>
        <h3>Log In</h3>
        
        <?php if (isset($login_error)) { echo "<p class='error'>$login_error</p>"; } ?>
        
        <form action="login.php" method="POST">
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

<?php include('footer.php'); ?>

