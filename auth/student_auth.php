<?php
require_once "../includes/session.php";
require_once "../config.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if student is logged in
function isStudentLoggedIn() {
    return isset($_SESSION['student_id']) && !empty($_SESSION['student_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

// Function to redirect if not logged in
function requireStudentLogin() {
    if (!isStudentLoggedIn()) {
        header("Location: ../index.html");
        exit();
    }
}

// Handle student login
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
        error_log("Login attempt - Email: " . $email . ", Password: " . $password);
        
        // Get user data with role information
        $sql = "SELECT s.id, s.name, s.password, s.status 
                FROM students s 
                WHERE s.email = ? AND s.status = 'active'";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            error_log("Database error: " . mysqli_error($conn));
            throw new Exception("Database error occurred");
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
        $expected_password = 'STU' . $user['id'];
        if ($password !== $expected_password) {
            error_log("Expected password: " . $expected_password . ", Received: " . $password);
            throw new Exception('Invalid password');
        }
        
        // Login successful
        $_SESSION['student_id'] = $user['id'];
        $_SESSION['student_name'] = $user['name'];
        $_SESSION['role'] = 'student';
        
        // Update last login
        $update_sql = "UPDATE students SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "i", $user['id']);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }
        
        $response['success'] = true;
        $response['redirect'] = '../dashboard/student_Dashboard.php';
        
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
