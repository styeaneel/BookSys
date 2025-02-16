<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure staff is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
    header("Location: staff_main_menu.php");
    exit();
}

include('connection.php');

// Handle fine waiving
if (isset($_POST['waive_fine'])) {
    $fine_id = $_POST['fine_id'];
    $staff_id = $_SESSION['user_id']; // Logged-in staff

    $update_query = "UPDATE fines SET fine_status = 'Waived', waived_by = '$staff_id' WHERE id = '$fine_id'";
    if (mysqli_query($conn, $update_query)) {
        $message = "<p style='color: green;'>✅ Fine waived successfully!</p>";
    } else {
        $message = "<p style='color: red;'>❌ Error waiving fine.</p>";
    }
}

// Fetch fines with filtering
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';

$query = "SELECT fines.id, users.first_name, users.last_name, books.title AS book_title, 
                 fines.fine_amount, fines.fine_status, fines.fine_payment_date
          FROM fines
          JOIN users ON fines.user_id = users.id
          JOIN books ON fines.book_id = books.id
          WHERE 1=1"; // Ensures WHERE clause is valid

if (!empty($search)) {
    $query .= " AND (users.first_name LIKE '%$search%' OR users.last_name LIKE '%$search%' OR books.title LIKE '%$search%')";
}

if (!empty($filter_status)) {
    $query .= " AND fines.fine_status = '$filter_status'";
}

$query .= " ORDER BY fines.fine_status ASC, fines.fine_amount DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fines Management - BookSys</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 900px; margin: auto; }
        .message { font-size: 16px; margin-bottom: 15px; }
        .filter-container { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #5A2D82; color: white; }
        .btn { padding: 5px 10px; border: none; cursor: pointer; color: white; border-radius: 5px; }
        .btn-waive { background-color: #D32F2F; } /* Red button for waiving fines */
        .btn-disabled { background-color: grey; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📑 Fines Management</h2>
        <?= isset($message) ? "<div class='message'>$message</div>" : ""; ?>

        <!-- Filters -->
        <form method="GET" class="filter-container">
            <input type="text" name="search" placeholder="Search by student or book title" value="<?= htmlspecialchars($search) ?>">
            <select name="filter_status">
                <option value="">All Status</option>
                <option value="Unpaid" <?= $filter_status == 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                <option value="Paid" <?= $filter_status == 'Paid' ? 'selected' : '' ?>>Paid</option>
                <option value="Waived" <?= $filter_status == 'Waived' ? 'selected' : '' ?>>Waived</option>
            </select>
            <button type="submit">Apply Filters</button>
        </form>

        <!-- Fine Table -->
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Book Title</th>
                    <th>Fine Amount</th>
                    <th>Status</th>
                    <th>Paid Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['first_name'] . " " . $row['last_name'] ?></td>
                            <td><?= $row['book_title'] ?></td>
                            <td>RM<?= number_format($row['fine_amount'], 2) ?></td>
                            <td><?= $row['fine_status'] ?></td>
                            <td><?= $row['fine_payment_date'] ? $row['fine_payment_date'] : "N/A" ?></td>
                            <td>
                                <?php if ($row['fine_status'] == 'Unpaid'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="fine_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="waive_fine" class="btn btn-waive" onclick="return confirm('Are you sure you want to waive this fine?');">Waive</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-disabled" disabled>Waived/Paid</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No fines found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
