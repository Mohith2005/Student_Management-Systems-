<?php
session_start();
require_once "../config.php";
require_once "../components/navigation_links.php";
require_once "../components/navbar.php";

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit;
}

// Get student data from session
$student_id = $_SESSION['student_id'];

// Get student's courses
$sql = "SELECT c.* FROM courses c 
        JOIN student_courses sc ON c.id = sc.course_id 
        WHERE sc.student_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$courses_result = mysqli_stmt_get_result($stmt);
$courses = [];
while ($course = mysqli_fetch_assoc($courses_result)) {
    $courses[] = $course;
}

// Get upcoming assignments
$sql = "SELECT a.*, c.course_name FROM assignments a 
        JOIN courses c ON a.course_id = c.id 
        JOIN student_courses sc ON c.id = sc.course_id 
        WHERE sc.student_id = ? AND a.due_date >= CURDATE() 
        ORDER BY a.due_date ASC LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$assignments_result = mysqli_stmt_get_result($stmt);
$assignments = [];
while ($assignment = mysqli_fetch_assoc($assignments_result)) {
    $assignments[] = $assignment;
}

// Get unread messages
$sql = "SELECT m.*, f.name as sender_name FROM messages m 
        JOIN faculty f ON m.sender_id = f.id 
        WHERE m.receiver_id = ? AND m.receiver_type = 'student' 
        AND m.read_status = 0 
        ORDER BY m.sent_time DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$messages_result = mysqli_stmt_get_result($stmt);
$messages = [];
while ($message = mysqli_fetch_assoc($messages_result)) {
    $messages[] = $message;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        :root {
            --primary-color: #2196F3;
            --secondary-color: #1976D2;
            --success-color: #4CAF50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --text-color: #333;
            --bg-color: #f4f6f9;
            --card-bg: #ffffff;
        }

        body.dark-theme {
            --text-color: #ffffff;
            --bg-color: #1a1a1a;
            --card-bg: #2d2d2d;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding-top: 60px;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        .card-content {
            margin-bottom: 15px;
        }

        .assignment-item, .message-item, .course-item {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            background: rgba(0,0,0,0.05);
            transition: background-color 0.3s ease;
        }

        .assignment-item:hover, .message-item:hover, .course-item:hover {
            background: rgba(0,0,0,0.1);
        }

        .due-date {
            color: var(--warning-color);
            font-size: 0.9rem;
        }

        .message-sender {
            font-weight: bold;
            color: var(--primary-color);
        }

        .message-time {
            font-size: 0.8rem;
            color: #666;
        }

        .progress-bar {
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: var(--secondary-color);
        }

        .notification-badge {
            background: var(--danger-color);
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php echo getNavbar('student'); ?>
    
    <div class="dashboard-container">
        <!-- Courses Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-book"></i> My Courses</h2>
                <a href="courses.php" class="btn">View All</a>
            </div>
            <div class="card-content">
                <?php foreach ($courses as $course): ?>
                <div class="course-item">
                    <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $course['progress'] ?? 0; ?>%"></div>
                    </div>
                    <small><?php echo $course['progress'] ?? 0; ?>% Complete</small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Assignments Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-tasks"></i> Upcoming Assignments</h2>
                <a href="assignments.php" class="btn">View All</a>
            </div>
            <div class="card-content">
                <?php if (empty($assignments)): ?>
                    <p>No upcoming assignments!</p>
                <?php else: ?>
                    <?php foreach ($assignments as $assignment): ?>
                    <div class="assignment-item">
                        <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                        <p><?php echo htmlspecialchars($assignment['course_name']); ?></p>
                        <p class="due-date">Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Messages Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>
                    <i class="fas fa-envelope"></i> Messages
                    <?php if (count($messages) > 0): ?>
                    <span class="notification-badge"><?php echo count($messages); ?></span>
                    <?php endif; ?>
                </h2>
                <a href="messages.php" class="btn">View All</a>
            </div>
            <div class="card-content">
                <?php if (empty($messages)): ?>
                    <p>No new messages!</p>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                    <div class="message-item">
                        <span class="message-sender"><?php echo htmlspecialchars($message['sender_name']); ?></span>
                        <p><?php echo htmlspecialchars(substr($message['content'], 0, 100)) . '...'; ?></p>
                        <span class="message-time"><?php echo date('M d, g:i A', strtotime($message['sent_time'])); ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Handle dark mode
        function updateTheme() {
            const isDarkMode = localStorage.getItem('theme') === 'dark';
            document.body.classList.toggle('dark-theme', isDarkMode);
        }

        // Update theme on load
        document.addEventListener('DOMContentLoaded', updateTheme);

        // Listen for theme changes
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') updateTheme();
        });
    </script>
</body>
</html>
