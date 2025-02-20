<?php
$host = 'localhost';
$dbname = 'student_management';
$username = 'root';
$password = '';

// Create connection with charset and collation settings
$conn = mysqli_init();
if (!$conn) {
    die("mysqli_init failed");
}

if (!mysqli_real_connect($conn, $host, $username, $password, $dbname)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set proper character encoding
if (!mysqli_set_charset($conn, 'utf8mb4')) {
    die("Error setting charset: " . mysqli_error($conn));
}

// Set SQL mode to handle strict requirements
mysqli_query($conn, "SET sql_mode = 'STRICT_ALL_TABLES'");
?>
