<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');

// Get POST data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Validate input
if (empty($username) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please provide both username and password'
    ]);
    exit;
}

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT id, name, email, password, course FROM students WHERE email = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            
            if ($password === $row['password']) {  // Direct password comparison
                // Password is correct, create session
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                $_SESSION['student_email'] = $row['email'];
                $_SESSION['student_course'] = $row['course'];
                $_SESSION['user_type'] = 'student';
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'course' => $row['course'],
                    'user_type' => 'student'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid password'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Student not found'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error'
        ]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error'
    ]);
}

mysqli_close($conn);
?>