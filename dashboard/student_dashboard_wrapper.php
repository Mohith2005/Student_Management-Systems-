<?php
require_once "../auth/student_auth.php";
requireStudentLogin();

// Get the requested page
$page = isset($_GET['page']) ? $_GET['page'] : 'student_Dashboard.html';

// List of allowed pages for students
$allowed_pages = [
    'student_Dashboard.html',
    'Student_course.html',
    'Test.html',
    'Video_lecture.html'
];

// Check if the requested page is allowed
if (!in_array($page, $allowed_pages)) {
    header("Location: student_Dashboard.html");
    exit();
}

// Include the requested page
include($page);
?>
