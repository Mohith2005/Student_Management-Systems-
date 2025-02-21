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

$faculty_id = $_SESSION['faculty_id'];

// Get faculty's courses
$sql = "SELECT c.*, COUNT(sc.student_id) as student_count 
        FROM courses c 
        LEFT JOIN student_courses sc ON c.id = sc.course_id 
        WHERE c.faculty_id = ? 
        GROUP BY c.id";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}
$courses_result = mysqli_stmt_get_result($stmt);
$courses = [];
while ($course = mysqli_fetch_assoc($courses_result)) {
    $courses[] = $course;
}
mysqli_stmt_close($stmt);

// Get recent assignments
$sql = "SELECT a.*, c.course_name, COUNT(sa.student_id) as submissions 
        FROM assignments a 
        JOIN courses c ON a.course_id = c.id 
        LEFT JOIN student_assignments sa ON a.id = sa.assignment_id 
        WHERE c.faculty_id = ? 
        GROUP BY a.id 
        ORDER BY a.due_date DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}
$assignments_result = mysqli_stmt_get_result($stmt);
$assignments = [];
while ($assignment = mysqli_fetch_assoc($assignments_result)) {
    $assignments[] = $assignment;
}
mysqli_stmt_close($stmt);

// Get unread messages count
$sql = "SELECT COUNT(*) as unread_count 
        FROM messages 
        WHERE receiver_id = ? 
        AND receiver_type = 'faculty' 
        AND read_status = 0";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}
$unread_result = mysqli_stmt_get_result($stmt);
$unread_count = mysqli_fetch_assoc($unread_result)['unread_count'];
mysqli_stmt_close($stmt);

// Get recent student activities
$sql = "SELECT s.name as student_name, c.course_name, 
        CASE 
            WHEN sa.id IS NOT NULL THEN 'submitted assignment'
            WHEN sv.id IS NOT NULL THEN 'watched video'
            WHEN st.id IS NOT NULL THEN 'took test'
        END as activity_type,
        COALESCE(sa.submitted_at, sv.watched_at, st.taken_at) as activity_time
        FROM students s
        JOIN student_courses sc ON s.id = sc.student_id
        JOIN courses c ON sc.course_id = c.id
        LEFT JOIN student_assignments sa ON s.id = sa.student_id
        LEFT JOIN student_videos sv ON s.id = sv.student_id
        LEFT JOIN student_tests st ON s.id = st.student_id
        WHERE c.faculty_id = ?
        ORDER BY activity_time DESC LIMIT 10";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}
$activities_result = mysqli_stmt_get_result($stmt);
$activities = [];
while ($activity = mysqli_fetch_assoc($activities_result)) {
    $activities[] = $activity;
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 10px 0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .course-card {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .course-title {
            font-size: 1.25rem;
            color: var(--primary-color);
            margin: 0;
        }

        .student-activity {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .activity-item {
            padding: 10px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .activity-time {
            font-size: 0.8rem;
            color: #666;
        }

        .chart-container {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php echo getNavbar('faculty'); ?>
    
    <div class="dashboard-container">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-book fa-2x"></i>
                <div class="stat-value"><?php echo count($courses); ?></div>
                <div class="stat-label">Active Courses</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users fa-2x"></i>
                <div class="stat-value">
                    <?php 
                    $total_students = 0;
                    foreach ($courses as $course) {
                        $total_students += $course['student_count'];
                    }
                    echo $total_students;
                    ?>
                </div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-tasks fa-2x"></i>
                <div class="stat-value"><?php echo count($assignments); ?></div>
                <div class="stat-label">Active Assignments</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-envelope fa-2x"></i>
                <div class="stat-value"><?php echo $unread_count; ?></div>
                <div class="stat-label">Unread Messages</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="main-content">
                <!-- Course Performance Chart -->
                <div class="chart-container">
                    <h2>Course Performance</h2>
                    <canvas id="coursePerformanceChart"></canvas>
                </div>

                <!-- Courses List -->
                <div class="course-card">
                    <div class="course-header">
                        <h2>My Courses</h2>
                        <a href="manage_courses.php" class="btn">Manage Courses</a>
                    </div>
                    <?php foreach ($courses as $course): ?>
                    <div class="course-item">
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <p><?php echo $course['student_count']; ?> Students Enrolled</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo rand(70, 100); ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Recent Assignments -->
                <div class="course-card">
                    <div class="course-header">
                        <h2>Recent Assignments</h2>
                        <a href="manage_assignments.php" class="btn">View All</a>
                    </div>
                    <?php foreach ($assignments as $assignment): ?>
                    <div class="assignment-item">
                        <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                        <p><?php echo htmlspecialchars($assignment['course_name']); ?></p>
                        <p>Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?></p>
                        <p>Submissions: <?php echo $assignment['submissions']; ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sidebar">
                <!-- Student Activity Feed -->
                <div class="student-activity">
                    <h2>Recent Student Activity</h2>
                    <?php foreach ($activities as $activity): ?>
                    <div class="activity-item">
                        <strong><?php echo htmlspecialchars($activity['student_name']); ?></strong>
                        <?php echo htmlspecialchars($activity['activity_type']); ?> in
                        <?php echo htmlspecialchars($activity['course_name']); ?>
                        <div class="activity-time">
                            <?php echo date('M d, g:i A', strtotime($activity['activity_time'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Course Performance Chart
        const ctx = document.getElementById('coursePerformanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($courses, 'course_name')); ?>,
                datasets: [{
                    label: 'Average Score',
                    data: <?php echo json_encode(array_map(function() { return rand(70, 95); }, $courses)); ?>,
                    backgroundColor: 'rgba(33, 150, 243, 0.5)',
                    borderColor: 'rgba(33, 150, 243, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Handle dark mode
        function updateTheme() {
            const isDarkMode = localStorage.getItem('theme') === 'dark';
            document.body.classList.toggle('dark-theme', isDarkMode);
        }

        // Update theme on load and changes
        document.addEventListener('DOMContentLoaded', updateTheme);
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') updateTheme();
        });
    </script>
</body>
</html>
