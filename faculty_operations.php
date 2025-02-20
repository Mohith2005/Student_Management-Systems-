<?php
session_start();
require_once "config.php";

// Function to add new faculty
function addFaculty($name, $email, $password, $department) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO faculty (name, email, password, department) VALUES (?, ?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $department);
        
        if(mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to get faculty details
function getFacultyDetails($faculty_id) {
    global $conn;
    $sql = "SELECT * FROM faculty WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $faculty_id);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to update faculty details
function updateFaculty($faculty_id, $name, $email, $department) {
    global $conn;
    $sql = "UPDATE faculty SET name = ?, email = ?, department = ? WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $department, $faculty_id);
        
        if(mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to assign course to faculty
function assignCourse($faculty_id, $course_id) {
    global $conn;
    $sql = "INSERT INTO faculty_courses (faculty_id, course_id) VALUES (?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $faculty_id, $course_id);
        
        if(mysqli_stmt_execute($stmt)) {
            return true;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Faculty login function
function facultyLogin($email, $password) {
    global $conn;
    $sql = "SELECT id, name, password FROM faculty WHERE email = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                if(password_verify($password, $row['password'])) {
                    $_SESSION['faculty_id'] = $row['id'];
                    $_SESSION['faculty_name'] = $row['name'];
                    return true;
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to get faculty's assigned courses
function getFacultyCourses($faculty_id) {
    global $conn;
    $sql = "SELECT c.* FROM courses c 
            INNER JOIN faculty_courses fc ON c.id = fc.course_id 
            WHERE fc.faculty_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $faculty_id);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $courses = array();
            while($row = mysqli_fetch_assoc($result)) {
                $courses[] = $row;
            }
            return $courses;
        }
        mysqli_stmt_close($stmt);
    }
    return array();
}
?>
