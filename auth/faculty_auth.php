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
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Log incoming credentials (remove in production)
    error_log("Faculty login attempt - Email: " . $email);
    
    $sql = "SELECT id, name, password, department FROM faculty WHERE email = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                
                // Log hashed passwords for comparison (remove in production)
                error_log("Stored hash: " . $row['password']);
                error_log("Verifying password: " . $password);
                
                if(password_verify($password, $row['password'])) {
                    // Login successful
                    $_SESSION['faculty_id'] = $row['id'];
                    $_SESSION['faculty_name'] = $row['name'];
                    $_SESSION['faculty_department'] = $row['department'];
                    
                    // Return success response
                    echo json_encode(['success' => true, 'redirect' => 'dashboard/faculty_Dashboard.html']);
                    exit();
                } else {
                    error_log("Password verification failed");
                }
            } else {
                error_log("No faculty found with email: " . $email);
            }
        } else {
            error_log("Query execution failed: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Statement preparation failed: " . mysqli_error($conn));
    }
    
    // Login failed
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.html");
    exit();
}
?>
