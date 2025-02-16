<?php
session_start(); // Ensure session is started

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Restrict access: Only students can access
if ($_SESSION['user_type'] !== 'student') {
    header("Location: main_menu.php"); // Redirect staff to staff menu
    exit();
}

// Store user name for display
$user_name = $_SESSION['user_name'] ?? "Guest"; 
?>

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student: BookSys Library</title>

    <!-- FontAwesome for Icons -->
    <script src="https://kit.fontawesome.com/460448da1a.js" crossorigin="anonymous"></script>

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

        /* Right Sidebar */
        .right-sidebar {
            width: 300px;
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(135deg, #F4F4F4, #E1D9F1);
            padding-top: 55px;
            padding-left: 20px;
            padding-right: 20px;
            font-size: 16px;
            box-shadow: -3px 0px 10px rgba(0, 0, 0, 0.15);
            border-left: 2px solid #D3B3F5;
            color: #5A2D82;
            transition: all 0.3s ease;
        }

        /* Profile Section */
        .profile-container {
            width: 100%;
            max-width: 420px;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            text-align: center;
            transition: 0.3s ease-in-out;
        }

        .profile-container:hover {
            transform: translateY(-5px);
            box-shadow: 0px 15px 25px rgba(0, 0, 0, 0.2);
        }

        .profile-header {
            background: linear-gradient(135deg, #6A0DAD, #B066CC, #E0A3FF);
            padding: 25px;
            color: white;
            font-size: 22px;
            font-weight: bold;
            border-bottom-left-radius: 50% 20px;
            border-bottom-right-radius: 50% 20px;
        }

        .profile-info {
            padding: 25px;
            font-size: 16px;
            line-height: 1.7;
        }

         /* Footer */
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
        <h1> BookSys Library</h1>
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
 <!-- Right Sidebar -->
 <div class="right-sidebar">
        <div class="widget">
            <h3>📢 Announcements</h3>
        </div>
        <div class="widget">
            <h3>📅 Library Hours</h3>
            <p>Mon - Fri: 9 AM - 7 PM</p>
            <p>Sat - Sun: Closed</p>
        </div>
    </div>
    <!-- Content -->
    <div class="content">
        <div class="profile-container">
            <div class="profile-header">
                <h2>Hello, <?php echo $user_name; ?> 👋</h2>
                <p>Your personalized library dashboard</p>
            </div>
            <div class="profile-info">
            
                <p><strong>Role:</strong> Student</p>
                <p><strong>Last Login:</strong> 2025-02-10 14:23</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
     <div class="footer">
        <div class="footer-container">
            <p>© <?php echo date("Y"); ?> BookSys. All rights reserved.</p>
            <div class="footer-links">
                <a href="privacy_policy.php">Privacy Policy</a>
                <a href="terms_of_service.php">Terms of Service</a>
                <a href="contact_us.php">Contact Us</a>
            </div>
        </div>
    </div>
</body>
</html>