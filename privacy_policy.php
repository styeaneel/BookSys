<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Privacy Policy | BookSys</title>
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
        <h2>Privacy Policy</h2>
        <p>Welcome to BookSys. Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal data.</p>

        <h3>1. Information We Collect</h3>
        <p>When you use our library system, we may collect the following information:</p>
        <ul>
            <li>Personal details (name, email, role)</li>
            <li>Login credentials</li>
            <li>Borrowing history</li>
            <li>System usage logs</li>
        </ul>

        <h3>2. How We Use Your Information</h3>
        <p>We use your data to:</p>
        <ul>
            <li>Manage book loans and returns</li>
            <li>Send notifications regarding due dates</li>
            <li>Improve library services</li>
            <li>Ensure account security</li>
        </ul>

        <h3>3. Data Protection</h3>
        <p>We take appropriate measures to protect your data. Only authorized staff members can access user records.</p>

        <h3>4. Third-Party Sharing</h3>
        <p>We do not share your personal data with third parties, except when required by law.</p>

        <h3>5. Your Rights</h3>
        <p>You have the right to access, update, or request deletion of your data. Contact the library admin for assistance.</p>

        <div class="footer">
            <p>&copy; <?php echo date("Y"); ?> BookSys. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
