<?php
session_start();
require_once "../config.php";
require_once "../components/navigation_links.php";

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit;
}

$student_id = $_SESSION['student_id'];

// Get video lectures for student's courses
$sql = "SELECT 
    vl.*,
    c.course_name,
    COALESCE(svp.watch_percentage, 0) as watch_percentage,
    svp.last_watched
FROM video_lectures vl
JOIN courses c ON vl.course_id = c.id
JOIN student_courses sc ON sc.course_id = c.id
LEFT JOIN student_video_progress svp ON svp.video_id = vl.id AND svp.student_id = ?
WHERE sc.student_id = ?
ORDER BY c.course_name, vl.title";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $student_id, $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$videos = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Group videos by course
$courseVideos = [];
foreach ($videos as $video) {
    if (!isset($courseVideos[$video['course_name']])) {
        $courseVideos[$video['course_name']] = [];
    }
    $courseVideos[$video['course_name']][] = $video;
}

// Convert to JSON for JavaScript
$videosData = json_encode($courseVideos);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Lectures</title>
    <?php echo getNavigationStyles(); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .videos-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .videos-header {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .course-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .video-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }

        .video-thumbnail {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            background-color: #000;
        }

        .video-play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background-color: rgba(255,255,255,0.8);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .video-info {
            padding: 15px;
        }

        .video-title {
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .video-meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .progress-bar {
            height: 4px;
            background-color: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: #4CAF50;
            transition: width 0.3s ease;
        }

        .course-title {
            margin: 0 0 15px 0;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .video-player-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            z-index: 1000;
        }

        .video-player-content {
            position: relative;
            width: 80%;
            max-width: 1000px;
            margin: 40px auto;
        }

        .video-player-close {
            position: absolute;
            right: -30px;
            top: -30px;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .video-frame {
            width: 100%;
            aspect-ratio: 16/9;
            border: none;
        }
    </style>
</head>
<body>
    <div class="videos-container">
        <div class="videos-header">
            <h1>Video Lectures</h1>
            <p>Watch course lectures and track your progress</p>
        </div>

        <?php echo renderNavigationMenu($student_links, 'video_lectures.php'); ?>

        <div id="courseVideosContainer">
            <!-- Course videos will be populated by JavaScript -->
        </div>
    </div>

    <!-- Video Player Modal -->
    <div id="videoPlayerModal" class="video-player-modal">
        <div class="video-player-content">
            <span class="video-player-close" onclick="closeVideoPlayer()">&times;</span>
            <iframe id="videoFrame" class="video-frame" allowfullscreen></iframe>
        </div>
    </div>

    <script>
        const videosData = <?php echo $videosData; ?>;

        function renderVideos() {
            const container = document.getElementById('courseVideosContainer');
            container.innerHTML = Object.entries(videosData).map(([courseName, videos]) => `
                <div class="course-section">
                    <h2 class="course-title">${courseName}</h2>
                    <div class="videos-grid">
                        ${videos.map(video => renderVideoCard(video)).join('')}
                    </div>
                </div>
            `).join('');
        }

        function renderVideoCard(video) {
            return `
                <div class="video-card">
                    <div class="video-thumbnail" onclick="playVideo('${video.video_url}')">
                        <div class="video-play-button">▶</div>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title">${video.title}</h3>
                        <div class="video-meta">
                            Duration: ${video.duration_minutes} minutes<br>
                            ${video.last_watched ? `Last watched: ${formatDate(video.last_watched)}` : ''}
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" style="width: ${video.watch_percentage}%"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        function playVideo(videoUrl) {
            const modal = document.getElementById('videoPlayerModal');
            const videoFrame = document.getElementById('videoFrame');
            videoFrame.src = videoUrl;
            modal.style.display = 'block';

            // Update progress (you would implement this)
            updateVideoProgress(videoUrl);
        }

        function closeVideoPlayer() {
            const modal = document.getElementById('videoPlayerModal');
            const videoFrame = document.getElementById('videoFrame');
            videoFrame.src = '';
            modal.style.display = 'none';
        }

        function updateVideoProgress(videoUrl) {
            // Implement progress tracking
            // This would typically involve making an AJAX call to your server
            console.log('Updating progress for video:', videoUrl);
        }

        // Initialize the page
        renderVideos();

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('videoPlayerModal');
            if (event.target == modal) {
                closeVideoPlayer();
            }
        }
    </script>
</body>
</html>
