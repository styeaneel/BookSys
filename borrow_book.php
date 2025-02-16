<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Only allow students to access this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: main_menu.php");
    exit();
}

$user_id = $_SESSION['user_id'];

include('connection.php');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Pagination Setup ---
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$offset = ($page - 1) * $items_per_page;

// --- Filters ---
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$genre_filter = isset($_GET['genre']) ? mysqli_real_escape_string($conn, $_GET['genre']) : '';

// --- Build Query for Books List (showing all books, both available and unavailable) ---
$query = "SELECT * FROM books WHERE 1=1";
if (!empty($genre_filter)) {
    $query .= " AND genre = '$genre_filter'";
}
if (!empty($search_query)) {
    $query .= " AND (title LIKE '%$search_query%' OR author LIKE '%$search_query%')";
}
$query .= " ORDER BY title ASC LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($query);

// --- Count Total Books for Pagination ---
$count_query = "SELECT COUNT(*) AS total FROM books WHERE 1=1";
if (!empty($genre_filter)) {
    $count_query .= " AND genre = '$genre_filter'";
}
if (!empty($search_query)) {
    $count_query .= " AND (title LIKE '%$search_query%' OR author LIKE '%$search_query%')";
}
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$total_items = $count_row['total'];
$total_pages = ceil($total_items / $items_per_page);

// --- Handle Book Borrowing ---
$receipt = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    // Check if book is available
    $book_status_query = "SELECT status FROM books WHERE id = '$book_id'";
    $status_result = $conn->query($book_status_query);
    $status_row = $status_result->fetch_assoc();
    if ($status_row['status'] !== 'Available') {
        echo "<script>alert('Book is not available for borrowing.'); window.location.href='borrow_book.php';</script>";
        exit();
    }
    $borrow_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days'));
    $receipt_number = strtoupper(uniqid('REC-'));

    // Get student info
    $student_query = "SELECT first_name, last_name FROM users WHERE id = '$user_id'";
    $student_result = $conn->query($student_query);
    $student_row = $student_result->fetch_assoc();
    $student_name = $student_row['first_name'] . " " . $student_row['last_name'];

    // Get book info
    $book_query = "SELECT title, author FROM books WHERE id = '$book_id'";
    $book_result = $conn->query($book_query);
    $book_row = $book_result->fetch_assoc();
    $book_title = $book_row['title'];
    $book_author = $book_row['author'];

    $borrow_query = "INSERT INTO borrowed_books (user_id, book_id, book_title, borrow_date, due_date, receipt_number) 
                     VALUES ('$user_id', '$book_id', '$book_title', '$borrow_date', '$due_date', '$receipt_number')";
    $update_book_status = "UPDATE books SET status = 'Borrowed' WHERE id = '$book_id'";

    if ($conn->query($borrow_query) && $conn->query($update_book_status)) {
        $receipt = "
            <div id='receipt' class='receipt-card'>
                <h2 class='receipt-title'>📌 Borrow Receipt</h2>
                <div class='receipt-details'>
                    <div class='receipt-row'><strong>Student Name:</strong> $student_name</div>
                    <div class='receipt-row'><strong>Student ID:</strong> $user_id</div>
                    <div class='receipt-row'><strong>Book Title:</strong> $book_title</div>
                    <div class='receipt-row'><strong>Author:</strong> $book_author</div>
                    <div class='receipt-row'><strong>Borrow Date:</strong> $borrow_date</div>
                    <div class='receipt-row'><strong>Due Date:</strong> $due_date</div>
                    <div class='receipt-row'><strong>Receipt Number:</strong> $receipt_number</div>
                </div>
                <div class='receipt-footer'>
                    <p class='receipt-instructions'>⚠ Present this receipt at the counter to receive your book and obtain a shelf number card. Return the card to confirm book collection.</p>
                </div>
                <div class='receipt-actions'>
                    <button onclick='printReceipt()' class='btn print-btn'>🖨 Print</button>
                    <button onclick='downloadPDF()' class='btn pdf-btn'>📥 Download PDF</button>
                </div>
            </div>
        ";
    } else {
        echo "<script>alert('Error borrowing book. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrow a Book - BookSys Library</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* BookSys Base Styling */
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
            margin-top: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }
        .filter-container input, .filter-container select, .filter-container button {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .filter-container button {
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
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-size: 14px;
        }
        .borrow-btn { background-color: #28A745; }
       
        

    /* Receipt Container */
    .receipt-card {
        width: 420px;
        background: #fff;
        border: 2px dashed #5A2D82;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.2);
        margin: 30px auto;
        text-align: center;
        font-family: 'Arial', sans-serif;
    }

    /* Receipt Header */
    .receipt-header {
        text-align: center;
        border-bottom: 2px solid #5A2D82;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .receipt-header h2 {
        color: #5A2D82;
        font-size: 18px;
        margin: 0;
        font-weight: bold;
    }

    /* Receipt Details */
    .receipt-details {
        text-align: left;
        font-size: 14px;
        line-height: 1.6;
    }

    .receipt-row {
        display: flex;
        justify-content: space-between;
        margin: 4px 0;
        padding: 3px 0;
        border-bottom: 1px dashed #ccc;
    }

    .receipt-footer {
        text-align: center;
        font-size: 12px;
        color: #5A2D82;
        font-weight: bold;
        padding-top: 10px;
        border-top: 2px solid #5A2D82;
    }

    /* Receipt Actions */
    .receipt-actions {
        margin-top: 15px;
    }

    .btn {
        padding: 8px 12px;
        border: none;
        cursor: pointer;
        color: white;
        border-radius: 5px;
        font-size: 14px;
        margin: 5px;
    }

    .print-btn { background-color: #5A2D82; }
    .pdf-btn { background-color: #007BFF; }

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
        .return-form button:hover { background-color: #482366; }

      

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .pagination a {
        margin: 0 5px;
        padding: 8px 12px;
        background-color: #5A2D82;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .pagination a:hover {
        background-color: #482366;
        transform: scale(1.05);
    }
    .pagination a.active {
        background-color: #482366;
        cursor: default;
    }

        /* Footer */
        .footer {
            background-color: #5A2D82;
            color: white;
            padding: 20px 0;
            text-align: center;
            font-size: 16px;
            margin-top: 500px;
        }
        .footer-container { max-width: 1200px; margin: 0 auto; }
        .footer p { font-size: 18px; margin: 0; padding-bottom: 10px; }
        .footer-links { margin-top: 10px; }
        .footer-links a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .footer-links a:hover { color: #D3B3F5; }
        @media (max-width: 768px) {
            .footer-links a { margin: 5px; }
        }
    </style>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function printReceipt() {
            let receipt = document.getElementById("receipt").outerHTML;
            let printWindow = window.open("", "_blank");
            printWindow.document.write("<html><head><title>Print Receipt</title></head><body>" + receipt + "</body></html>");
            printWindow.document.close();
            printWindow.print();
        }
        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            let doc = new jsPDF();
            let receiptText = document.getElementById("receipt").innerText.trim();
            doc.setFont("helvetica", "bold");
            doc.setFontSize(18);
            doc.text("📌 Borrow Receipt", 20, 20);
            doc.setFont("helvetica", "normal");
            doc.setFontSize(14);
            let margin = 30;
            let lineSpacing = 10;
            receiptText.split("\n").forEach(line => {
                doc.text(line, 20, margin);
                margin += lineSpacing;
            });
            doc.setFont("helvetica", "bold");
            doc.setTextColor(255, 0, 0);
            doc.text("Instructions: Present this receipt at the counter to receive your book.", 20, margin + 10, { maxWidth: 160 });
            doc.save("Borrow_Receipt.pdf");
        }
    </script>
</head>
<body>


    <!-- Header -->
    <div class="header">
        <h1>BookSys Library</h1>
        <div class="nav">
            <a href="main_menu.php">Home</a>
            <a href="about.php">About</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
   <!-- Sidebar -->
   <div class="sidebar">
        <a href="borrow_book.php">📚 Search Books</a>
        <a href="loan_history.php">📜 Loan History</a>
        <a href="citation_generator.php">📂 Citation Generator</a>
        <a href="check_and_pay_fines.php">🔥 Overdue Books & Fines</a>
        <a href="profile_settings.php">⚙️ Profile Settings</a>
    </div>
    <!-- Content -->
    <div class="content">
        <h2>Borrow a Book</h2>
        <?php if (!empty($receipt)) { ?>
            <?= $receipt ?>
        <?php } else { ?>
            <form method="GET" class="filter-container">
                <input type="text" name="search" placeholder="Search by title or author" value="<?= htmlspecialchars($search_query) ?>">
                <select name="genre">
                    <option value="">All Genres</option>
                    <option value="Fiction" <?= $genre_filter == "Fiction" ? "selected" : "" ?>>Fiction</option>
                    <option value="Non-Fiction" <?= $genre_filter == "Non-Fiction" ? "selected" : "" ?>>Non-Fiction</option>
                    <option value="Fantasy" <?= $genre_filter == "Fantasy" ? "selected" : "" ?>>Fantasy</option>
                </select>
                <button type="submit">Search</button>
            </form>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><?= htmlspecialchars($row['genre']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <?php if ($row['status'] == 'Available') { ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="book_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn borrow-btn">Borrow</button>
                                    </form>
                                <?php } else { ?>
                                    <button class="btn btn-disabled" disabled>Unavailable</button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
               <!-- Pagination -->
<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search_query) ?>&genre=<?= urlencode($genre_filter) ?>"
           class="<?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php } ?>
</div>
            </div>
        <?php } ?>
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
