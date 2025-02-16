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

$query = "SELECT 
            books.title, 
            borrowed_books.borrow_date, 
            borrowed_books.due_date, 
            borrowed_books.return_date,
            CASE 
                WHEN borrowed_books.return_date IS NOT NULL THEN 'Returned'
                WHEN NOW() > borrowed_books.due_date THEN 'Overdue'
                ELSE 'Borrowed'
            END AS status
          FROM borrowed_books 
          JOIN books ON borrowed_books.book_id = books.id 
          WHERE borrowed_books.user_id = '$user_id'";


$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan History</title>
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
            margin-right: 320px; /* Leave space for the right sidebar */
            padding: 30px;
            font-size: 18px;
        }
        .content {
            margin-left: 270px;
            padding: 40px;
            margin-top: 80px;
        }

        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            margin: auto;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #5A2D82;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
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

    !-- Sidebar -->
   <div class="sidebar">
        <a href="borrow_book.php">📚 Search Books</a>
        <a href="loan_history.php">📜 Loan History</a>
        <a href="citation_generator.php">📂 Citation Generator</a>
        <a href="check_and_pay_fines.php"> Overdue Books & Fines</a>
        <a href="profile_settings.php">⚙️ Profile Settings</a>
    </div>


    <div class="content">
        <div class="table-container">
            <h2 style="text-align: center; color: #5A2D82;">Your Loan History</h2>
            <table>
                <tr>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo date("d M Y", strtotime($row['borrow_date'])); ?></td>
                    <td><?php echo date("d M Y", strtotime($row['due_date'])); ?></td>
                    <td><?php echo $row['status']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>© <?php echo date("Y"); ?> BookSys. All rights reserved.</p>
    </div>
</body>
</html>
