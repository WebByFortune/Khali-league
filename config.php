

<?php
// config.php - MySQLi Database Connection

$dbHost = 'localhost'; // Usually 'localhost' for local development
$dbUser = 'root';      // Your database username (e.g., 'root' for XAMPP/WAMP)
$dbPass = '';          // Your database password (e.g., empty for XAMPP/WAMP root)
$dbName = 'userdata_db'; // <--- ***CRITICAL CHANGE HERE***

// Attempt to connect to MySQL database
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set character set (recommended)
mysqli_set_charset($conn, "utf8mb4");

// For debugging:
// echo "Database connected successfully!";
?>