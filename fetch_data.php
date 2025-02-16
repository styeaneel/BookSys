<?php
session_start();
include 'connection.php'; // Ensure you include your DB connection

// Query database for real-time stats
$sql = "SELECT 
        (SELECT COUNT(*) FROM books) AS totalBooks, 
        (SELECT COUNT(*) FROM users) AS totalUsers,
        (SELECT COUNT(*) FROM borrowed_books WHERE status='borrowed') AS borrowedBooks,
        (SELECT COUNT(*) FROM borrowed_books WHERE due_date < CURDATE() AND status='borrowed') AS overdueBooks";

$result = $conn->query($sql);
$data = $result->fetch_assoc();

// Return data as JSON
echo json_encode($data);
?>
