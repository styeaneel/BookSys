<?php
session_start();

// Enable error reporting for debugging
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
    $search_query = " AND (CONCAT(users.first_name, ' ', users.last_name) LIKE '%$search_term%' OR books.title LIKE '%$search_term%')";
}

// Fetch overdue books
$query = "SELECT borrowed_books.id, CONCAT(users.first_name, ' ', users.last_name) AS user_name, books.title AS book_title, 
                 borrowed_books.borrow_date, borrowed_books.due_date
          FROM borrowed_books
          JOIN users ON borrowed_books.user_id = users.id
          JOIN books ON borrowed_books.book_id = books.id
          WHERE borrowed_books.due_date < CURDATE()" . $search_query;

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Books</title>
    <style>
        /* Same styles as reference page */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F9F9F9;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #5A2D82;
            color: white;
            padding: 13px;
            text-align: center;
            font-size: 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background: linear-gradient(135deg, #D3B3F5, #5A2D82);
            padding-top: 55px;
            padding-left: 20px;
            font-size: 16px;
            color: white;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
            border-radius: 8px;
        }

        .content {
            margin-left: 280px;
            padding: 30px;
        }

        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 80px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #5A2D82;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">Overdue Books</div>
    <div class="sidebar">
        <a href="staff_main_menu.php">Dashboard</a>
        <a href="borrowed_books.php">📖 Borrowed Books</a>
        <a href="overdue_books.php">⏳ Overdue Books</a>
    </div>
    <div class="content">
        <div class="table-container">
            <h2 style="text-align: center; color: #5A2D82;">Overdue Books List</h2>
            <form method="POST">
                <input type="text" name="search_term" placeholder="Search by user or book title" value="<?= isset($_POST['search_term']) ? $_POST['search_term'] : '' ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <button type="submit" name="search" style="background-color: #5A2D82; color: white; padding: 12px; border-radius: 5px; border: none; width: 100%;">Search</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Book Title</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>{$row['user_name']}</td>
                                    <td>{$row['book_title']}</td>
                                    <td>{$row['borrow_date']}</td>
                                    <td style='color: red; font-weight: bold;'>{$row['due_date']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No overdue books found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>