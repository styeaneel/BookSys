<?php
session_start();
include('connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$totalBooksQuery = "SELECT COUNT(*) AS total_books FROM books";
$totalBooksResult = mysqli_query($conn, $totalBooksQuery);
$totalBooks = mysqli_fetch_assoc($totalBooksResult)['total_books'];

$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total_users'];

$borrowedBooksQuery = "SELECT COUNT(*) AS borrowed_books FROM borrowed_books WHERE return_date IS NULL";
$borrowedBooksResult = mysqli_query($conn, $borrowedBooksQuery);
$borrowedBooks = mysqli_fetch_assoc($borrowedBooksResult)['borrowed_books'];

$overdueBooksQuery = "SELECT COUNT(*) AS overdue_books FROM borrowed_books WHERE due_date < CURDATE() AND return_date IS NULL";
$overdueBooksResult = mysqli_query($conn, $overdueBooksQuery);
$overdueBooks = mysqli_fetch_assoc($overdueBooksResult)['overdue_books'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Reports - BookSys</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F4F4F9;
            margin: 0;
            padding: 0;
        }
        .content {
            margin-left: 280px;
            padding: 30px;
            font-size: 18px;
        }
        h2 {
            color: #5A2D82;
        }
        .report-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .report-box {
            background: linear-gradient(135deg, #8360c3, #2ebf91);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 230px;
            text-align: center;
            color: white;
            transition: transform 0.2s;
        }
        .report-box:hover {
            transform: scale(1.05);
        }
        .report-box h3 {
            margin: 0;
            font-size: 22px;
        }
        .report-box p {
            font-size: 26px;
            font-weight: bold;
        }
        .export-buttons {
            margin-top: 20px;
        }
        .export-buttons button {
            padding: 12px;
            margin-right: 10px;
            background: #5A2D82;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .export-buttons button:hover {
            background: #7a3db8;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>📑 Library Reports</h2>
        <p>View key statistics about the library system.</p>

        <div class="report-container" id="reportTable">
            <div class="report-box"><h3>Total Books</h3><p><?php echo $totalBooks; ?></p></div>
            <div class="report-box"><h3>Total Users</h3><p><?php echo $totalUsers; ?></p></div>
            <div class="report-box"><h3>Borrowed Books</h3><p><?php echo $borrowedBooks; ?></p></div>
            <div class="report-box"><h3>Overdue Books</h3><p><?php echo $overdueBooks; ?></p></div>
        </div>
        
        <div class="export-buttons">
            <button onclick="exportToPDF()">Export to PDF</button>
            <button onclick="exportToExcel()">Export to Excel</button>
        </div>
    </div>

    <script>
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            let doc = new jsPDF();
            doc.text("Library Reports", 10, 10);
            let elements = document.querySelectorAll(".report-box");
            let y = 20;
            elements.forEach(el => {
                let title = el.querySelector("h3").textContent;
                let value = el.querySelector("p").textContent;
                doc.text(`${title}: ${value}`, 10, y);
                y += 10;
            });
            doc.save("Library_Reports.pdf");
        }

        function exportToExcel() {
            let wb = XLSX.utils.book_new();
            let data = [["Metric", "Value"],
                ["Total Books", "<?php echo $totalBooks; ?>"],
                ["Total Users", "<?php echo $totalUsers; ?>"],
                ["Borrowed Books", "<?php echo $borrowedBooks; ?>"],
                ["Overdue Books", "<?php echo $overdueBooks; ?>"]];
            let ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, "Library Reports");
            XLSX.writeFile(wb, "Library_Reports.xlsx");
        }
    </script>
</body>
</html>
