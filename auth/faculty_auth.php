<?php
session_start();
require_once "../config.php";

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
    
    $sql = "SELECT id, name, password, department FROM faculty WHERE email = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                if(password_verify($password, $row['password'])) {
                    // Login successful
                    $_SESSION['faculty_id'] = $row['id'];
                    $_SESSION['faculty_name'] = $row['name'];
                    $_SESSION['faculty_department'] = $row['department'];
                    
                    // Return success response
                    echo json_encode(['success' => true, 'redirect' => 'dashboard/faculty_Dashboard.html']);
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
