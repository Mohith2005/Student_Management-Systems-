<?php
session_start();
require_once "../config.php";

// Function to check if student is logged in
function isStudentLoggedIn() {
    return isset($_SESSION['student_id']) && !empty($_SESSION['student_id']);
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
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT id, name, password FROM students WHERE email = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                if(password_verify($password, $row['password'])) {
                    // Login successful
                    $_SESSION['student_id'] = $row['id'];
                    $_SESSION['student_name'] = $row['name'];
                    
                    // Return success response
                    echo json_encode(['success' => true, 'redirect' => 'dashboard/student_Dashboard.html']);
                    exit();
                }
            }
        }
        mysqli_stmt_close($stmt);
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
