<?php
session_start();
require_once "../config.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if faculty is logged in
function isFacultyLoggedIn() {
    return isset($_SESSION['faculty_id']) && !empty($_SESSION['faculty_id']);
}

// Function to redirect if not logged in
function requireFacultyLogin() {
    if (!isFacultyLoggedIn()) {
        header("Location: ../index.html");
        exit();
    }
}

// Handle faculty login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = array('success' => false, 'message' => '', 'redirect' => '');
    
    try {
        // Get and sanitize input
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            throw new Exception('Please provide both email and password.');
        }
        
        // Log incoming credentials (remove in production)
        error_log("Faculty login attempt - Email: " . $email);
        
        // Check if user exists and get their data
        $sql = "SELECT id, name, password, department FROM faculty WHERE email = ? AND status = 'active'";
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            throw new Exception("Database error: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Query failed: " . mysqli_error($conn));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) !== 1) {
            throw new Exception('Invalid email address');
        }
        
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            error_log("Password verification failed for faculty: " . $email);
            error_log("Input password: " . $password);
            error_log("Stored hash: " . $user['password']);
            throw new Exception('Invalid password');
        }
        
        // Set session variables
        $_SESSION['faculty_id'] = $user['id'];
        $_SESSION['faculty_name'] = $user['name'];
        $_SESSION['faculty_department'] = $user['department'];
        
        // Update last login time
        $update_sql = "UPDATE faculty SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "i", $user['id']);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }
        
        $response['success'] = true;
        $response['redirect'] = '../dashboard/faculty_Dashboard.html';
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $response['message'] = $e->getMessage();
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.html");
    exit();
}
?>
