<?php
session_start();
require_once "../config.php";
require_once "../components/navigation_links.php";

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit;
}

// Get student data
$student_id = $_SESSION['student_id'];

// Get course progress data
$sql = "SELECT 
    c.course_name,
    sc.attendance_percentage,
    sc.grade,
    COUNT(DISTINCT t.id) as total_tests,
    COUNT(DISTINCT CASE WHEN st.status = 'completed' THEN st.test_id END) as completed_tests,
    AVG(CASE WHEN st.status = 'completed' THEN st.marks_obtained END) as avg_test_score,
    COUNT(DISTINCT vl.id) as total_videos,
    AVG(svp.watch_percentage) as avg_video_progress
FROM student_courses sc
JOIN courses c ON sc.course_id = c.id
LEFT JOIN tests t ON t.course_id = c.id
LEFT JOIN student_tests st ON st.test_id = t.id AND st.student_id = ?
LEFT JOIN video_lectures vl ON vl.course_id = c.id
LEFT JOIN student_video_progress svp ON svp.video_id = vl.id AND svp.student_id = ?
WHERE sc.student_id = ?
GROUP BY c.id";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $student_id, $student_id, $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get recent test attempts
$sql = "SELECT 
    t.title as test_name,
    c.course_name,
    st.marks_obtained,
    t.total_marks,
    st.completion_time,
    st.submitted_at
FROM student_tests st
JOIN tests t ON st.test_id = t.id
JOIN courses c ON t.course_id = c.id
WHERE st.student_id = ? AND st.status = 'completed'
ORDER BY st.submitted_at DESC
LIMIT 5";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$recent_tests = mysqli_stmt_get_result($stmt);
$recent_tests = mysqli_fetch_all($recent_tests, MYSQLI_ASSOC);

// Get video progress
$sql = "SELECT 
    vl.title as video_name,
    c.course_name,
    svp.watch_percentage,
    svp.last_watched
FROM student_video_progress svp
JOIN video_lectures vl ON svp.video_id = vl.id
JOIN courses c ON vl.course_id = c.id
WHERE svp.student_id = ?
ORDER BY svp.last_watched DESC
LIMIT 5";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$recent_videos = mysqli_stmt_get_result($stmt);
$recent_videos = mysqli_fetch_all($recent_videos, MYSQLI_ASSOC);

// Convert data to JSON for JavaScript
$analyticsData = json_encode([
    'courses' => $courses,
    'recent_tests' => $recent_tests,
    'recent_videos' => $recent_videos
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php echo getNavigationStyles(); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .analytics-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .analytics-header {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .analytics-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }

        .recent-activity {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .progress-bar {
            height: 8px;
            background-color: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: #4CAF50;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="analytics-container">
        <div class="analytics-header">
            <h1>Your Learning Analytics</h1>
            <p>Track your progress across all courses</p>
        </div>

        <?php echo renderNavigationMenu($student_links, 'analytics.php'); ?>

        <div class="analytics-grid">
            <div class="analytics-card">
                <h2>Course Progress</h2>
                <div class="chart-container">
                    <canvas id="courseProgressChart"></canvas>
                </div>
            </div>

            <div class="analytics-card">
                <h2>Test Performance</h2>
                <div class="chart-container">
                    <canvas id="testPerformanceChart"></canvas>
                </div>
            </div>

            <div class="analytics-card">
                <h2>Video Progress</h2>
                <div class="chart-container">
                    <canvas id="videoProgressChart"></canvas>
                </div>
            </div>
        </div>

        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <div id="recentActivity"></div>
        </div>
    </div>

    <script>
        // Analytics data from PHP
        const analyticsData = <?php echo $analyticsData; ?>;

        // Course Progress Chart
        const courseCtx = document.getElementById('courseProgressChart').getContext('2d');
        new Chart(courseCtx, {
            type: 'bar',
            data: {
                labels: analyticsData.courses.map(course => course.course_name),
                datasets: [{
                    label: 'Attendance %',
                    data: analyticsData.courses.map(course => course.attendance_percentage),
                    backgroundColor: '#4CAF50'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Test Performance Chart
        const testCtx = document.getElementById('testPerformanceChart').getContext('2d');
        new Chart(testCtx, {
            type: 'line',
            data: {
                labels: analyticsData.recent_tests.map(test => test.test_name),
                datasets: [{
                    label: 'Score %',
                    data: analyticsData.recent_tests.map(test => 
                        (test.marks_obtained / test.total_marks) * 100
                    ),
                    borderColor: '#2196F3',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Video Progress Chart
        const videoCtx = document.getElementById('videoProgressChart').getContext('2d');
        new Chart(videoCtx, {
            type: 'doughnut',
            data: {
                labels: analyticsData.recent_videos.map(video => video.video_name),
                datasets: [{
                    data: analyticsData.recent_videos.map(video => video.watch_percentage),
                    backgroundColor: [
                        '#4CAF50',
                        '#2196F3',
                        '#FFC107',
                        '#9C27B0',
                        '#F44336'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Recent Activity
        const activityContainer = document.getElementById('recentActivity');
        const activities = [
            ...analyticsData.recent_tests.map(test => ({
                type: 'test',
                title: test.test_name,
                course: test.course_name,
                date: new Date(test.submitted_at),
                details: `Score: ${test.marks_obtained}/${test.total_marks}`
            })),
            ...analyticsData.recent_videos.map(video => ({
                type: 'video',
                title: video.video_name,
                course: video.course_name,
                date: new Date(video.last_watched),
                details: `Progress: ${video.watch_percentage}%`
            }))
        ].sort((a, b) => b.date - a.date);

        activityContainer.innerHTML = activities.map(activity => `
            <div class="activity-item">
                <strong>${activity.title}</strong> (${activity.course})
                <br>
                ${activity.details}
                <br>
                <small>${activity.date.toLocaleDateString()} ${activity.date.toLocaleTimeString()}</small>
            </div>
        `).join('');
    </script>
</body>
</html>
