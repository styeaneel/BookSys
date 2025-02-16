<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSys</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Header Styles */
        .main-header {
    background: linear-gradient(135deg, #5A2D82, #D3B3F5); /* Gradient Background */
    padding: 15px 20px;
    text-align: center;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.logo-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 1400px; /* Adjust width for responsiveness */
    margin: 0 auto;
}

.logo {
    width: 300px; /* Increase this value to make it longer */
    height: 80px; /* Adjust to control height */
    object-fit: contain; /* Keeps the image within set dimensions */
}

.site-title {
    color: white;
    font-size: 32px;
    font-weight: bold;
    text-transform: uppercase;
    flex-grow: 1;
    text-align: center;
}

.auth-buttons {
    text-align: right;
    padding-right: 20px;
}
.auth-buttons a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    padding: 10px 15px;
    background: #FF9800;
    border-radius: 5px;
    transition: background 0.3s;
}
.auth-buttons a:hover {
    background: #E68900;
}


    </style>
</head>
<body>
  <!-- Header Section -->
<header class="main-header">
    <div class="logo-banner">
        <img src="images/uitm.png" alt="UiTM Logo" class="logo">
        <h1 class="site-title">BookSys Library Management</h1>
        <img src="images/logo.jpg" alt="Second Logo" class="logo">
    </div>

    <div class="auth-buttons">
        <a href="register.php">SignUp</a>
    </div>
</header>


</body>
</html>
