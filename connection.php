<?php
// Database configuration
$servername = "localhost";  // Database server (use 'localhost' or the IP address of your DB server)
$username = "root";         // Database username (default is usually 'root' on local setups)
$password = "";             // Database password (empty by default for local setups)
$dbname = "booksys";        // Name of your database

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
