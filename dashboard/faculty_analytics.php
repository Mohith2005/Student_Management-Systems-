<?php
session_start();
require_once "../config.php";
require_once "../components/navigation_links.php";

// Check if faculty is logged in
if (!isset($_SESSION['faculty_id'])) {
    header("Location: ../index.html");
    exit;
}

$faculty_id = $_SESSION['faculty_id'];

// Get course statistics
$sql = "SELECT 
    c.id as course_id,
    c.course_name,
    COUNT(DISTINCT sc.student_id) as total_students,
    AVG(sc.attendance_percentage) as avg_attendance,
    COUNT(DISTINCT t.id) as total_tests,
    AVG(CASE WHEN st.status = 'completed' THEN st.marks_obtained END) as avg_test_score,
    COUNT(DISTINCT vl.id) as total_videos,
    AVG(svp.watch_percentage) as avg_video_progress
FROM courses c
JOIN faculty_courses fc ON fc.course_id = c.id
LEFT JOIN student_courses sc ON sc.course_id = c.id
LEFT JOIN tests t ON t.course_id = c.id
LEFT JOIN student_tests st ON st.test_id = t.id
LEFT JOIN video_lectures vl ON vl.course_id = c.id
LEFT JOIN student_video_progress svp ON svp.video_id = vl.id
WHERE fc.faculty_id = ?
GROUP BY c.id";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$courses = mysqli_stmt_get_result($stmt);
$courses = mysqli_fetch_all($courses, MYSQLI_ASSOC);

// Get recent test submissions
$sql = "SELECT 
    s.name as student_name,
    c.course_name,
    t.title as test_title,
    st.marks_obtained,
    t.total_marks,
    st.submitted_at
FROM student_tests st
JOIN tests t ON st.test_id = t.id
JOIN courses c ON t.course_id = c.id
JOIN faculty_courses fc ON fc.course_id = c.id
JOIN students s ON st.student_id = s.id
WHERE fc.faculty_id = ? AND st.status = 'completed'
ORDER BY st.submitted_at DESC
LIMIT 10";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$recent_submissions = mysqli_stmt_get_result($stmt);
$recent_submissions = mysqli_fetch_all($recent_submissions, MYSQLI_ASSOC);

// Get video engagement data
$sql = "SELECT 
    c.course_name,
    vl.title,
    COUNT(DISTINCT svp.student_id) as total_views,
    AVG(svp.watch_percentage) as avg_completion
FROM video_lectures vl
JOIN courses c ON vl.course_id = c.id
JOIN faculty_courses fc ON fc.course_id = c.id
LEFT JOIN student_video_progress svp ON svp.video_id = vl.id
WHERE fc.faculty_id = ?
GROUP BY vl.id
ORDER BY total_views DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$video_stats = mysqli_stmt_get_result($stmt);
$video_stats = mysqli_fetch_all($video_stats, MYSQLI_ASSOC);

// Convert data to JSON for JavaScript
$analyticsData = json_encode([
    'courses' => $courses,
    'recent_submissions' => $recent_submissions,
    'video_stats' => $video_stats
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Analytics</title>
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

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .stat-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2196F3;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .recent-submissions {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .submission-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .submission-item:last-child {
            border-bottom: none;
        }

        h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .video-stats {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .video-stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .video-stat-item:last-child {
            border-bottom: none;
        }

        .progress-bar {
            height: 8px;
            background-color: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            flex: 1;
            margin: 0 15px;
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
            <h1>Course Analytics</h1>
            <p>Track student performance and engagement across your courses</p>
        </div>

        <?php echo renderNavigationMenu($faculty_links, 'faculty_analytics.php'); ?>

        <div class="analytics-grid">
            <div class="analytics-card">
                <h2>Course Overview</h2>
                <div class="chart-container">
                    <canvas id="courseOverviewChart"></canvas>
                </div>
            </div>

            <div class="analytics-card">
                <h2>Test Performance</h2>
                <div class="chart-container">
                    <canvas id="testPerformanceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="recent-submissions">
            <h2>Recent Test Submissions</h2>
            <div id="recentSubmissions"></div>
        </div>

        <div class="video-stats">
            <h2>Video Engagement</h2>
            <div id="videoStats"></div>
        </div>
    </div>

    <script>
        const analyticsData = <?php echo $analyticsData; ?>;

        // Course Overview Chart
        const courseCtx = document.getElementById('courseOverviewChart').getContext('2d');
        new Chart(courseCtx, {
            type: 'bar',
            data: {
                labels: analyticsData.courses.map(course => course.course_name),
                datasets: [{
                    label: 'Average Attendance %',
                    data: analyticsData.courses.map(course => course.avg_attendance),
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
                labels: analyticsData.courses.map(course => course.course_name),
                datasets: [{
                    label: 'Average Test Score %',
                    data: analyticsData.courses.map(course => 
                        course.avg_test_score ? (course.avg_test_score) : 0
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

        // Render Recent Submissions
        const submissionsContainer = document.getElementById('recentSubmissions');
        submissionsContainer.innerHTML = analyticsData.recent_submissions.map(submission => `
            <div class="submission-item">
                <strong>${submission.student_name}</strong> - ${submission.course_name}
                <br>
                ${submission.test_title}: ${submission.marks_obtained}/${submission.total_marks}
                <br>
                <small>Submitted: ${new Date(submission.submitted_at).toLocaleString()}</small>
            </div>
        `).join('');

        // Render Video Stats
        const videoStatsContainer = document.getElementById('videoStats');
        videoStatsContainer.innerHTML = analyticsData.video_stats.map(video => `
            <div class="video-stat-item">
                <div>
                    <strong>${video.title}</strong>
                    <br>
                    <small>${video.course_name}</small>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width: ${video.avg_completion}%"></div>
                </div>
                <div>
                    <div class="stat-value">${video.total_views}</div>
                    <div class="stat-label">Views</div>
                </div>
            </div>
        `).join('');
    </script>
</body>
</html>
