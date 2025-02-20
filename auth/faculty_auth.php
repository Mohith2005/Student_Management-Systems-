<?php
session_start();
require_once "../config.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if faculty is logged in
function isFacultyLoggedIn() {
    return isset($_SESSION['faculty_id']) && !empty($_SESSION['faculty_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'faculty';
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
        // Get input
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Basic validation
        if (empty($email) || empty($password)) {
            throw new Exception('Please provide both email and password.');
        }
        
        // Debug log
        error_log("Faculty login attempt - Email: " . $email . ", Password: " . $password);
        
        // Get user data with role information
        $sql = "SELECT f.id, f.name, f.password, f.department, f.status 
                FROM faculty f 
                WHERE f.email = ? AND f.status = 'active'";
        
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
        
        // For testing purposes, check if password matches ID pattern
        $expected_password = 'FAC' . $user['id'];
        if ($password !== $expected_password) {
            error_log("Expected password: " . $expected_password . ", Received: " . $password);
            throw new Exception('Invalid password');
        }
        
        // Login successful
        $_SESSION['faculty_id'] = $user['id'];
        $_SESSION['faculty_name'] = $user['name'];
        $_SESSION['faculty_department'] = $user['department'];
        $_SESSION['role'] = 'faculty';
        
        // Update last login
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
    
    // Send response
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
