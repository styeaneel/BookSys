<?php
session_start();
include 'connection.php'; // Ensure database connection is included

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get user type from session
$user_type = $_SESSION['user_type'] ?? ''; // Default to empty if not set

// Redirect based on user type
if ($user_type === 'staff') {
    header("Location: staff_main_menu.php");
    exit();
} elseif ($user_type === 'student') {
    header("Location: main_menu.php");
    exit();
} else {
    echo "<p style='color: red;'>❌ Invalid user type detected.</p>";
}
?>
