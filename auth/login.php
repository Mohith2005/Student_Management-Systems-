<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');

try {
    // Get and validate input
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $userType = $_POST['userType'] ?? '';

    if (!$email || !$password || !$userType) {
        throw new Exception('Please provide all required fields');
    }

    // Sanitize inputs
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);
    
    // Select appropriate table based on user type
    $table = ($userType === 'faculty') ? 'faculty' : 'students';
    
    // Prepare SQL statement
    $sql = "SELECT * FROM $table WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement');
    }
    
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Verify password (in production, use password_verify with hashed passwords)
        if ($password === $row['password']) {
            // Set session variables
            $_SESSION['user_type'] = $userType;
            
            if ($userType === 'faculty') {
                $_SESSION['faculty_id'] = $row['id'];
                $_SESSION['faculty_name'] = $row['name'];
                $_SESSION['faculty_email'] = $row['email'];
                $_SESSION['faculty_dept'] = $row['department'];
                $redirect = '../dashboard/faculty_dashboard.php';
            } else {
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                $_SESSION['student_email'] = $row['email'];
                $_SESSION['enrollment_number'] = $row['enrollment_number'];
                $redirect = '../dashboard/student_dashboard.php';
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $redirect
            ]);
        } else {
            throw new Exception('Invalid password');
        }
    } else {
        throw new Exception('User not found');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>
