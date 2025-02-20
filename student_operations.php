<?php
session_start();
require_once "config.php";

// Function to add new student
function addStudent($name, $email, $password, $course) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO students (name, email, password, course) VALUES (?, ?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $course);
        
        if(mysqli_stmt_execute($stmt)) {
            return true;
        } else {
            return false;
        }
        mysqli_stmt_close($stmt);
    }
}

// Function to get student details
function getStudentDetails($student_id) {
    global $conn;
    $sql = "SELECT * FROM students WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to update student details
function updateStudent($student_id, $name, $email, $course) {
    global $conn;
    $sql = "UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $course, $student_id);
        
        if(mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to delete student
function deleteStudent($student_id) {
    global $conn;
    $sql = "DELETE FROM students WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        
        if(mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to get all courses
function getAllCourses() {
    global $conn;
    $sql = "SELECT * FROM courses";
    $result = mysqli_query($conn, $sql);
    $courses = array();
    
    while($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    return $courses;
}

// Student login function
function studentLogin($email, $password) {
    global $conn;
    $sql = "SELECT id, name, password FROM students WHERE email = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                if(password_verify($password, $row['password'])) {
                    $_SESSION['student_id'] = $row['id'];
                    $_SESSION['student_name'] = $row['name'];
                    return true;
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}
?>
