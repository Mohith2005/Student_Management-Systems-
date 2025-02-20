<?php
require_once "../includes/session.php";
require_once "../config.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if student is logged in
if (!isset($_SESSION['student_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    error_log("Student not logged in. Session data: " . print_r($_SESSION, true));
    header("Location: ../index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Dashboard - Redesigned</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: #f4f7fe;
        }
        
        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #4776E6 0%, #8E54E9 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <?php include 'student_header.php'; ?>
    <div class="dashboard">
        <div class="dashboard-header">
            <div>
                <h1>Student Dashboard</h1>
                <p style="opacity: 0.9; margin-top: 5px;"><?php echo htmlspecialchars($_SESSION['student_name']); ?></p>
            </div>
        </div>
        
        <div class="content">
            <!-- Your dashboard content here -->
        </div>
    </div>
</body>
</html>
