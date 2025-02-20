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
$sql = "SELECT id, name, password FROM students WHERE email = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $row['password'])) {
                // Password is correct, create session
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                $_SESSION['user_type'] = 'student';
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'name' => $row['name'],
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
                'message' => 'Student account not found'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error occurred'
        ]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database preparation failed'
    ]);
}

mysqli_close($conn);
?>