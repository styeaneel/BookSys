
<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include('connection.php');

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form was submitted
if (isset($_POST['delete'])) {
    // Collect ISBN or Book ID to delete
    $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);

    // Check if the ISBN is provided
    if (empty($isbn)) {
        $message = "<p style='color: red;'>Please enter a valid ISBN to delete the book.</p>";
    } else {
        // Delete the book from the database based on ISBN
        $query = "DELETE FROM books WHERE isbn = '$isbn'";

        if (mysqli_query($conn, $query)) {
            $message = "<p style='color: green;'>Book deleted successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Book</title>
    <style>

        /* Sidebar */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background: linear-gradient(135deg, #D3B3F5, #5A2D82);
            padding-top: 55px;
            padding-left: 20px;
            padding-right: 20px;
            font-size: 16px;
            box-shadow: 3px 0px 6px rgba(0, 0, 0, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        /* Sidebar links and button styles */
        .sidebar a, .sidebar button {
            display: block;
            color: white;
            background-color: #5A2D82;
            margin-bottom: 12px;
            padding: 15px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
        }

        /* Hover effect for links and buttons */
        .sidebar a:hover, .sidebar button:hover {
            background-color: #482366;
            padding-left: 20px;
        }

        /* Collapsible button styling */
        .collapsible {
            background-color: #5A2D82;
            color: white;
            cursor: pointer;
            padding: 15px;
            border: none;
            width: 100%;
            text-align: left;
            outline: none;
            font-size: 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        /* Hover effect for collapsible buttons */
        .collapsible:hover {
            background-color: #482366;
        }

        /* Submenu for collapsible buttons */
        .sidebar .collapsible + div {
            padding-left: 20px;
            display: none;
        }

        /* Submenu items */
        .sidebar .collapsible + div a {
            margin-bottom: 10px;
            padding-left: 25px;
            font-size: 15px;
            background-color: transparent;
            transition: all 0.3s ease;
        }

        /* Hover effect for submenu links */
        .sidebar .collapsible + div a:hover {
            background-color: #4c1f5a;
            padding-left: 30px;
        }

        /* Active State for Collapsible Menu */
        .sidebar .collapsible.active + div {
            display: block;
        }


        /* Base Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F9F9F9;
            margin: 0;
            padding: 0;
        }

        /* Header */
        .header {
            background-color: #5A2D82;
            color: white;
            padding: 13px 0px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            flex-grow: 1;
            text-align: center;
        }

        .nav {
            display: flex;
            gap: 15px;
        }

        .nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        .nav a:hover {
            text-decoration: underline;
        }


        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #5A2D82;
        }

        input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: #5A2D82;
            outline: none;
        }

        button {
            background-color: #5A2D82;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #482366;
        }

        /* Success/Error Messages */
        .success-message, .error-message {
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        /* Footer */
        .footer {
            background-color: #5A2D82;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
            text-align: center;
            font-size: 16px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer p {
            font-size: 18px;
            margin: 0;
            padding-bottom: 10px;
        }

        .footer-links {
            margin-top: 10px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #D3B3F5;
        }

        @media (max-width: 768px) {
            .footer-links a {
                margin: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Admin BookSys Library</h1>
        <div class="nav">
            <a href="staff_main_menu.php">Home</a>
            <a href="about.php">About</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="staff_main_menu.php">Dashboard</a>
        <button class="collapsible">📚 Manage Books ▼</button>
        <div id="booksMenu" style="display: none; padding-left: 12px;">
            <a href="search_books.php">🔍 Search Books</a>
            <a href="add_book.php">➕ Add Book</a>
            <a href="edit_book.php">✏️ Edit Book</a>
            <a href="delete_book.php">🗑️ Delete Book</a>
        </div>
        <button class="collapsible">👥 Manage Users ▼</button>
        <div id="usersMenu" style="display: none; padding-left: 12px;">
            <a href="add_user.php">➕ Add User</a>
            <a href="edit_user.php">✏️ Edit User</a>
            <a href="delete_user.php">🗑️ Delete User</a>
            <a href="borrowed_books.php">📖 Borrowed Books</a>
           
        </div>
        <a href="reports.php">📑 Reports</a>
        <a href="settings.php">⚙️ Settings</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="form-container">
            <h2 style="text-align: center; color: #5A2D82;">Delete Book</h2>
            <form method="POST">
                <label for="isbn">Enter ISBN to delete:</label>
                <input type="text" name="isbn" id="isbn" required>

                <button type="submit" name="delete">Delete Book</button>
            </form>

            <!-- Display any success or error messages -->
            <?php
            if (isset($message)) {
                echo $message;
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-container">
            <p>© <?php echo date("Y"); ?> BookSys. All rights reserved.</p>
            <div class="footer-links">
                <a href="privacy_policy.php">Privacy Policy</a>
                <a href="terms_of_service.php">Terms of Service</a>
                <a href="contact_us.php">Contact Us</a>
            </div>
        </div>
    </div>

    <script>
        // Toggle visibility of menus
        function toggleMenu(id) {
            var menu = document.getElementById(id);
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        // Make collapsible buttons work
        var coll = document.getElementsByClassName("collapsible");
        for (var i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                toggleMenu(this.nextElementSibling.id);
            });
        }
    </script>
</body>
</html>
