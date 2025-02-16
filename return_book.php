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

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle book return
$return_message = "";
if (isset($_POST['return_book'])) {
    $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);

    // Check if the book is borrowed and not yet returned
    $query = "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND book_id = '$book_id' AND return_date IS NULL";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Update return date
        $update_query = "UPDATE borrowed_books SET return_date = NOW() WHERE user_id = '$user_id' AND book_id = '$book_id'";
        $update_status_query = "UPDATE books SET status = 'Available' WHERE id = '$book_id'";

        if (mysqli_query($conn, $update_query) && mysqli_query($conn, $update_status_query)) {
            $return_message = "<p style='color: green;'>✅ Book return recorded successfully!</p>";
        } else {
            $return_message = "<p style='color: red;'>❌ Error updating return status.</p>";
        }
    } else {
        $return_message = "<p style='color: red;'>⚠️ No matching borrowed book found or already returned.</p>";
    }
}

// Fetch borrowed books
$query = "SELECT borrowed_books.book_id, books.title, borrowed_books.borrow_date, borrowed_books.due_date 
          FROM borrowed_books 
          JOIN books ON borrowed_books.book_id = books.id 
          WHERE borrowed_books.user_id = '$user_id' AND borrowed_books.return_date IS NULL";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return a Book</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F9F9F9;
            margin: 0;
            padding: 0;
        }

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

        .sidebar a {
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

        .sidebar a:hover {
            background-color: #482366;
            padding-left: 20px;
        }

        .content {
            margin-left: 280px;
            padding: 40px;
            margin-top: 80px;
        }

        .table-container {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 700px;
            margin: auto;
            text-align: center;
        }

        h2 {
            color: #5A2D82;
        }

        /* Select dropdown */
        select {
            width: 90%;
            padding: 12px;
            border: 2px solid #5A2D82;
            border-radius: 6px;
            font-size: 16px;
            margin-bottom: 15px;
            background-color: white;
        }

        select:focus {
            outline: none;
            border-color: #482366;
        }

        .return-btn {
            background-color: #5A2D82;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        .return-btn:hover {
            background-color: #482366;
            transform: translateY(-2px);
        }

        .success-message {
            color: green;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .error-message {
            color: red;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

    
        .footer {
            background-color: #5A2D82;
            color: white;
            padding: 20px 0;
            margin-top: 50px;
            text-align: center;
            font-size: 16px;
        }

    </style>
</head>
<body>
    <div class="header">
        <h1>BookSys Library</h1>
        <div class="nav">
            <a href="main_menu.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

   <!-- Sidebar -->
   <div class="sidebar">
        <a href="borrow_book.php">📚 Search Books</a>
        <a href="loan_history.php">📜 Loan History</a>
        <a href="return_book.php">🔄 Return a Book</a>
        <a href="browse_categories.php">📂 Browse Categories</a>
        <a href="popular_books.php">🔥 Popular Books</a>
        <a href="profile_settings.php">⚙️ Profile Settings</a>
    </div>

    <div class="content">
        <div class="table-container">
            <h2>Return a Book</h2>
            <?php echo $return_message; ?>
            <form method="post">
                <label for="book_id"><strong>Select a Book:</strong></label>
                <select name="book_id" required>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <option value="<?php echo $row['book_id']; ?>">
                            <?php echo htmlspecialchars($row['title']) . " (Due: " . date("d M Y", strtotime($row['due_date'])) . ")"; ?>
                        </option>
                    <?php } ?>
                </select>
                <br>
                <button type="submit" name="return_book" class="return-btn">Return Book</button>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>© <?php echo date("Y"); ?> BookSys. All rights reserved.</p>
    </div>
</body>
</html>
