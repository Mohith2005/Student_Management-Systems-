<?php
session_start();
require_once "../config.php";
require_once "../components/navigation_links.php";
require_once "../components/navbar.php";

// Check if faculty is logged in
if (!isset($_SESSION['faculty_id'])) {
    header("Location: ../index.html");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assignments - Faculty Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 60px;
            background-color: #f4f6f9;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .assignment-list {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .create-assignment {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn {
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <?php echo getNavbar('faculty'); ?>
    
    <div class="container">
        <h1>Manage Assignments</h1>
        
        <div class="create-assignment">
            <h2>Create New Assignment</h2>
            <form id="assignmentForm">
                <div>
                    <label for="title">Assignment Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div>
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div>
                    <label for="dueDate">Due Date:</label>
                    <input type="datetime-local" id="dueDate" name="dueDate" required>
                </div>
                <div>
                    <label for="points">Total Points:</label>
                    <input type="number" id="points" name="points" required>
                </div>
                <button type="submit" class="btn">Create Assignment</button>
            </form>
        </div>

        <div class="assignment-list">
            <h2>Current Assignments</h2>
            <!-- Assignment list will be loaded here dynamically -->
        </div>
    </div>

    <script>
        // Add your JavaScript for handling assignments here
    </script>
</body>
</html>
