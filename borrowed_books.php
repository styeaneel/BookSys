<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('connection.php');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- Search & Filter for Borrowed Books ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_overdue = isset($_GET['filter_overdue']) ? " AND borrowed_books.due_date < NOW() AND borrowed_books.return_date IS NULL" : "";
$filter_pickup = isset($_GET['filter_pickup']) ? " AND borrowed_books.picked_up = 'NO'" : '';

$query = "SELECT borrowed_books.id, borrowed_books.picked_up, 
                 CONCAT_WS(' ', users.first_name, users.last_name) AS user_name, 
                 books.title AS book_title, 
                 borrowed_books.borrow_date, borrowed_books.due_date, borrowed_books.return_date
          FROM borrowed_books
          JOIN users ON borrowed_books.user_id = users.id
          JOIN books ON borrowed_books.book_id = books.id
          WHERE 1=1";
if (!empty($search)) {
    $query .= " AND (users.first_name LIKE '%$search%' OR users.last_name LIKE '%$search%' OR books.title LIKE '%$search%')";
}
$query .= $filter_overdue . $filter_pickup;
$result = mysqli_query($conn, $query);

// --- Handle Book Return Confirmation ---
$return_message = "";
if (isset($_POST['confirm_return'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);

    // Check if the book is borrowed and not yet returned
    $query_return = "SELECT * FROM borrowed_books WHERE user_id = '$student_id' AND book_id = '$book_id' AND return_date IS NULL";
    $result_return = mysqli_query($conn, $query_return);
    if (mysqli_num_rows($result_return) > 0) {
        $update_query = "UPDATE borrowed_books SET return_date = NOW() WHERE user_id = '$student_id' AND book_id = '$book_id'";
        $update_status_query = "UPDATE books SET status = 'Available' WHERE id = '$book_id'";
        if (mysqli_query($conn, $update_query) && mysqli_query($conn, $update_status_query)) {
            $return_message = "<p style='color: green;'>✅ Book return confirmed successfully!</p>";
        } else {
            $return_message = "<p style='color: red;'>❌ Error updating return status.</p>";
        }
    } else {
        $return_message = "<p style='color: red;'>⚠️ No matching borrowed book found or already returned.</p>";
    }
}

// --- Handle Book Pickup Confirmation ---
if (isset($_POST['confirm_pickup'])) {
    $borrow_id = mysqli_real_escape_string($conn, $_POST['borrow_id']);
    $pickup_query = "UPDATE borrowed_books SET picked_up = 'YES' WHERE id = '$borrow_id'";

    if (mysqli_query($conn, $pickup_query)) {
        $_SESSION['return_message'] = "<p style='color: green;'>✅ Book marked as picked up!</p>";
    } else {
        $_SESSION['return_message'] = "<p style='color: red;'>❌ Error updating pickup status.</p>";
    }

    // Redirect to prevent form resubmission (Stops duplicate messages)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Display the message once and clear it
if (isset($_SESSION['return_message'])) {
    echo $_SESSION['return_message'];
    unset($_SESSION['return_message']); // Clears the message after display
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Books - Admin BookSys Library</title>
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
            padding: 13px 0;
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
            box-shadow: 3px 0 6px rgba(0,0,0,0.1);
            color: white;
            transition: all 0.3s ease;
        }
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
        .sidebar a:hover, .sidebar button:hover {
            background-color: #482366;
            padding-left: 20px;
        }
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
        .collapsible:hover {
            background-color: #482366;
        }
        .sidebar .collapsible + div {
            padding-left: 20px;
            display: none;
        }
        .sidebar .collapsible + div a {
            margin-bottom: 10px;
            padding-left: 25px;
            font-size: 15px;
            background-color: transparent;
            transition: all 0.3s ease;
        }
        .sidebar .collapsible + div a:hover {
            background-color: #4c1f5a;
            padding-left: 30px;
        }
        .sidebar .collapsible.active + div {
            display: block;
        }
        /* Content */
        .content {
            margin-left: 280px;
            padding: 30px;
            font-size: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 80px;
        }
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }
        .search-container input, .search-container select, .search-container button {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-container button {
            background-color: #5A2D82;
            color: white;
            cursor: pointer;
        }
        .table-container {
            width: 80%;
            margin: 20px auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
            text-align: center;
        }
        th, td {
            padding: 10px;
        }
        th {
            background-color: #5A2D82;
            color: white;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .btn-confirm {
            background-color: #5A2D82;
            color: white;
        }
        .btn-disabled {
            background-color: grey;
            color: white;
            cursor: not-allowed;
        }
        /* Return Form */
        .return-form {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .return-form label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #5A2D82;
        }
        .return-form input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }
        .return-form button {
            background-color: #5A2D82;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .return-form button:hover {
            background-color: #482366;
        }
        /* Receipt Card Styling */
        .receipt-card {
            width: 400px;
            background: #fff;
            border: 3px solid #5A2D82;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.2);
            margin: 20px auto;
            text-align: center;
        }
        .receipt-title {
            color: #5A2D82;
            font-size: 18px;
            font-weight: bold;
        }
        .receipt-details {
            text-align: left;
            font-size: 14px;
            margin: 10px 0;
        }
        .receipt-row {
            margin: 5px 0;
        }
        .receipt-footer {
            font-size: 12px;
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
        .receipt-actions {
            margin-top: 15px;
        }
        /* Footer */
        .footer {
            background-color: #5A2D82;
            color: white;
            padding: 20px 0;
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
        <div id="booksMenu" style="display: none;">
            <a href="search_books.php">🔍 Search Books</a>
            <a href="add_book.php">➕ Add Book</a>
            <a href="edit_book.php">✏️ Edit Book</a>
            <a href="delete_book.php">🗑️ Delete Book</a>
        </div>
        <button class="collapsible">👥 Manage Users ▼</button>
        <div id="usersMenu" style="display: none;">
            <a href="add_user.php">➕ Add User</a>
            <a href="edit_user.php">✏️ Edit User</a>
            <a href="delete_user.php">🗑️ Delete User</a>
            <a href="borrowed_books.php">📖 Borrowed Books</a>
            <a href="overdue_books.php">⏳ Overdue Books</a>
        </div>
        <a href="reports.php">📑 Reports</a>
        <a href="settings.php">⚙️ Settings</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="search-container">
            <form method="GET">
                <input type="text" name="search" placeholder="Search by User or Book Title" value="<?= htmlspecialchars($search) ?>">
                <label>
                    <input type="checkbox" name="filter_overdue" <?= isset($_GET['filter_overdue']) ? 'checked' : '' ?>> Overdue Books
                </label>
                <label>
                    <input type="checkbox" name="filter_pickup" <?= isset($_GET['filter_pickup']) ? 'checked' : '' ?>> Not Picked Up
                </label>
                <button type="submit">Apply Filters</button>
            </form>
        </div>
        <h2>Borrowed Books List</h2>
        <?= $return_message; ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Book Title</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Pickup Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : 
                            $return_status = !empty($row['return_date']) 
                                ? ($row['return_date'] > $row['due_date'] 
                                    ? '<span style="color: orange;">Returned Late</span>' 
                                    : $row['return_date']) 
                                : '<span style="color: red; font-weight: bold;">Not Returned</span>';
                            $pickup_status = $row['picked_up'] === 'YES' 
                                ? "<span style='color: green;'>✅ Picked Up</span>" 
                                : "<span style='color: red;'>❌ Not Picked Up</span>";
                        ?>
                            <tr>
                                <td><?= $row['user_name']; ?></td>
                                <td><?= $row['book_title']; ?></td>
                                <td><?= $row['borrow_date']; ?></td>
                                <td><?= $row['due_date']; ?></td>
                                <td><?= $return_status; ?></td>
                                <td><?= $pickup_status; ?></td>
                                <td>
                                    <?php if ($row['picked_up'] === 'NO') : ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="borrow_id" value="<?= $row['id']; ?>">
                                            <button type="submit" name="confirm_pickup" class="btn btn-confirm">Confirm Pickup</button>
                                        </form>
                                    <?php else : ?>
                                        <button class="btn btn-disabled" disabled>Pickup Confirmed</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan="7">No borrowed books found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="return-form">
            <h3>Return a Book</h3>
            <?php if (!empty($return_message)) : ?>
                <div class="return-message">
                    <?= $return_message; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label>Student ID:</label>
                <input type="text" name="student_id" required>
                <label>Book ID:</label>
                <input type="text" name="book_id" required>
                <button type="submit" name="confirm_return" class="btn btn-confirm">Confirm Return</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-container">
            <p>© <?= date("Y"); ?> BookSys. All rights reserved.</p>
            <div class="footer-links">
                <a href="privacy_policy.php">Privacy Policy</a>
                <a href="terms_of_service.php">Terms of Service</a>
                <a href="contact_us.php">Contact Us</a>
            </div>
        </div>
    </div>
    <script>
    setTimeout(() => {
        document.getElementById('messageBox')?.remove();
    }, 3000); // Removes message after 3 seconds
</script>
    <script>
        // Toggle visibility of menus
        function toggleMenu(id) {
            var menu = document.getElementById(id);
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }
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
<?php $conn->close(); ?>
