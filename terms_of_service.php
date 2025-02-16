<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terms of Service | BookSys</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        h2 { color: #5A2D82; }
        h3 { color: #333; }
        p { line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        .footer { margin-top: 20px; text-align: center; font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Terms of Service</h2>
        <p>Welcome to BookSys. By using our library system, you agree to the following terms and conditions.</p>

        <h3>1. User Responsibilities</h3>
        <p>All users must:</p>
        <ul>
            <li>Provide accurate personal information</li>
            <li>Use the system only for library-related activities</li>
            <li>Return borrowed books on time</li>
            <li>Respect other users and library staff</li>
        </ul>

        <h3>2. Borrowing and Returning Books</h3>
        <p>Users must adhere to the borrowing policies:</p>
        <ul>
            <li>Books must be returned by the due date</li>
            <li>Late returns may result in penalties</li>
            <li>Lost or damaged books must be reported immediately</li>
        </ul>

        <h3>3. Account Security</h3>
        <p>Users are responsible for maintaining the security of their accounts. Do not share your login credentials with others.</p>

        <h3>4. System Misuse</h3>
        <p>Any attempt to manipulate or exploit the system may result in suspension or termination of access.</p>

        <h3>5. Changes to Terms</h3>
        <p>We may update these terms from time to time. Continued use of the system indicates acceptance of the latest terms.</p>

        <div class="footer">
            <p>&copy; <?php echo date("Y"); ?> BookSys. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
