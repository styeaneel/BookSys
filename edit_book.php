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

// Handle the search query
$search_query = "";
if (isset($_POST['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_POST['search_term']);
    $search_query = " WHERE title LIKE '%$search_term%' OR author LIKE '%$search_term%'";
}

// If the user is editing a book, fetch the book data
if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Get the book details from the database
    $query = "SELECT * FROM books WHERE id = '$book_id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $book = mysqli_fetch_assoc($result);
    } else {
        $message = "<p style='color: red;'>Book not found.</p>";
    }
}

// Handle the form submission to update the book
if (isset($_POST['submit'])) {
    // Collect and sanitize form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
    $published_year = mysqli_real_escape_string($conn, $_POST['published_year']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

    // Validate if all required fields are filled
    if (empty($title) || empty($author) || empty($isbn) || empty($published_year) || empty($quantity)) {
        $message = "<p style='color: red;'>Please fill in all required fields.</p>";
    } else {
        // Update the book in the database
        $update_query = "UPDATE books 
                         SET title = '$title', author = '$author', isbn = '$isbn', 
                             published_year = '$published_year', genre = '$genre', quantity = '$quantity' 
                         WHERE id = '$book_id'";

        if (mysqli_query($conn, $update_query)) {
            $message = "<p style='color: green;'>Book updated successfully!</p>";
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
    <title>Edit Book</title>
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


        /* Content */
        .content {
            margin-left: 280px;
            padding: 30px;
            font-size: 18px;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            margin-top: 100px;
            box-sizing: border-box;
        }

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

        /* Table Styling */
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            text-align: left;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #5A2D82;
            color: white;
        }

        table td a {
            color: #5A2D82;
            text-decoration: none;
            font-weight: bold;
        }

        table td a:hover {
            text-decoration: underline;
        }

        .footer {
            background-color: #5A2D82;
            color: white;
            padding: 20px 0;
            margin-top: 500px;
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
        
        <button class="collapsible" onclick="toggleMenu('booksMenu')">📚 Manage Books ▼</button>
        <div id="booksMenu" style="display: none; padding-left: 12px;">
            <a href="search_book.php">🔍 Search Books</a>
            <a href="add_book.php">➕ Add Book</a>
            <a href="edit_book.php">✏️ Edit Book</a>
            <a href="delete_book.php">🗑️ Delete Book</a>

        </div>

        <button class="collapsible" onclick="toggleMenu('usersMenu')">👥 Manage Users ▼</button>
        <div id="usersMenu" style="display: none; padding-left: 12px;">
            <a href="add_user.php">➕ Add User</a>
            <a href="edit_user.php">✏️ Edit User</a>
            <a href="delete_user.php">🗑️ Delete User</a>
            <a href="borrowed_books.php">📖 Borrowed Books</a>
        </div>
        <a href="fines_management.php"> 💰 Check Overdue Books & Fines</a>
        <a href="reports.php">📑 Reports</a>
        <a href="settings.php">⚙️ Settings</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="form-container">
            <h2 style="text-align: center; color: #5A2D82;">Edit Book</h2>

            <!-- Search Bar -->
            <form method="POST" style="margin-bottom: 20px;">
                <input type="text" name="search_term" placeholder="Search by title or author" value="<?= isset($_POST['search_term']) ? $_POST['search_term'] : '' ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <button type="submit" name="search" style="background-color: #5A2D82; color: white; padding: 12px; border-radius: 5px; border: none; width: 100%;">Search</button>
            </form>

            <!-- Display Book List -->
            <h3>Books in the system:</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch books based on search query
                    $books_query = "SELECT * FROM books" . $search_query;
                    $books_result = mysqli_query($conn, $books_query);

                    if (mysqli_num_rows($books_result) > 0) {
                        while ($book_row = mysqli_fetch_assoc($books_result)) {
                            echo "<tr>
                                    <td>{$book_row['id']}</td>
                                    <td>{$book_row['title']}</td>
                                    <td>{$book_row['author']}</td>
                                    <td><a href='edit_book.php?id={$book_row['id']}'>Edit</a></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No books found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Book Edit Form -->
            <?php if (isset($book)): ?>
                <form method="POST">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" value="<?= $book['title'] ?>" required>

                    <label for="author">Author:</label>
                    <input type="text" name="author" id="author" value="<?= $book['author'] ?>" required>

                    <label for="isbn">ISBN:</label>
                    <input type="text" name="isbn" id="isbn" value="<?= $book['isbn'] ?>" required>

                    <label for="published_year">Published Year:</label>
                    <input type="number" name="published_year" id="published_year" value="<?= $book['published_year'] ?>" required>

                    <label for="genre">Genre:</label>
                    <input type="text" name="genre" id="genre" value="<?= $book['genre'] ?>">

                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="<?= $book['quantity'] ?>" required>

                    <button type="submit" name="submit">Update Book</button>
                </form>
            <?php endif; ?>

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
            </div>
        </div>
    </div>

    <script>
        // Toggle Sidebar menu
        document.querySelectorAll('.collapsible').forEach(button => {
            button.addEventListener('click', () => {
                const menu = button.nextElementSibling;
                menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
