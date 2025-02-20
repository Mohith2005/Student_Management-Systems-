<?php
session_start();
require_once "../config.php";

// Check if faculty is logged in
if (!isset($_SESSION['faculty_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty') {
    header("Location: ../index.html");
    exit();
}

// Include the HTML dashboard
include 'Faculty_Dashboard.html';
?>
