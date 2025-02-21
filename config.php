<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_management');

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to handle special characters
mysqli_set_charset($conn, "utf8mb4");

// Common functions
function redirect($location) {
    header("Location: $location");
    exit;
}

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    redirect('/index.html');
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Base URL for the project
define('BASE_URL', '/Student_Management-Systems-');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
