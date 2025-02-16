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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About BookSys</title>
    <style>
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

        /* Content */
        .content {
            margin-left: 280px;
            padding: 30px;
            font-size: 18px;
        }

        /* Footer */
        .footer {
            background-color: #5A2D82;
            color: white;
            padding: 20px 0;
            margin-top: 550px;
            text-align: center;
            font-size: 16px;
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
    <h2 style="color: #5A2D82;">About BookSys</h2>
    
    <p><strong>BookSys</strong> is an advanced <em>library management system</em> designed to simplify the process of cataloging, searching, and managing books efficiently. It empowers librarians and staff with a streamlined workflow, ensuring smooth library operations.</p>

    <h3 style="color: #5A2D82;">Key Features:</h3>
    <ul style="line-height: 1.8; font-size: 17px;">
        <li>📚 <strong>Efficient Book Management:</strong> Easily add, edit, and remove books from the catalog.</li>
        <li>🔍 <strong>Advanced Search:</strong> Quickly locate books by title, author, or category.</li>
        <li>👥 <strong>User Management:</strong> Manage library users, including students and staff.</li>
        <li>📖 <strong>Borrowing & Returns:</strong> Keep track of borrowed books and due dates with automated reminders.</li>
        <li>📊 <strong>Reports & Insights:</strong> Generate reports on book circulation, overdue items, and user activity.</li>
    </ul>

    <p>Designed with an <strong>intuitive interface</strong>, BookSys ensures ease of use for both administrators and library patrons. Whether you're managing a small school library or a large institutional repository, BookSys enhances efficiency and organization.</p>

    <p style="font-style: italic;">Experience a smarter way to manage your library with BookSys!</p>
</div>

    <!-- Footer -->
    <div class="footer">
        <p>© <?php echo date("Y"); ?> BookSys. All rights reserved.</p>
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

