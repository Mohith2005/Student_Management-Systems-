<?php
require_once 'check_faculty_auth.php';
require_once '../config.php';

$faculty_id = $_SESSION['faculty_id'];

// Handle video lecture operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    switch($action) {
        case 'add_video':
            $course_id = $_POST['course_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $video_url = $_POST['video_url'];
            
            $sql = "INSERT INTO video_lectures (course_id, title, description, video_url) 
                    VALUES (?, ?, ?, ?)";
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "isss", $course_id, $title, $description, $video_url);
                mysqli_stmt_execute($stmt);
                echo json_encode(['success' => true]);
            }
            break;
            
        case 'reply_feedback':
            $feedback_id = $_POST['feedback_id'];
            $reply = $_POST['reply'];
            
            $sql = "UPDATE video_feedback SET faculty_reply = ?, reply_date = NOW() 
                    WHERE id = ?";
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $reply, $feedback_id);
                mysqli_stmt_execute($stmt);
                echo json_encode(['success' => true]);
            }
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Lecture Management</title>
    <style>
        .video-management {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .course-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .video-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .video-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .feedback-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .feedback-item {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9em;
            color: #666;
        }

        .feedback-content {
            margin-bottom: 10px;
        }

        .feedback-reply {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .reply-form {
            margin-top: 10px;
        }

        .reply-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .add-video-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }

        .btn-primary {
            background: #007bff;
        }

        .btn-success {
            background: #28a745;
        }

        .progress-bar {
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress {
            height: 100%;
            background: #28a745;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <?php include 'faculty_header.php'; ?>
    
    <div class="video-management">
        <?php
        // Get faculty's courses
        $courses_query = "SELECT c.* FROM courses c 
                         INNER JOIN faculty_courses fc ON c.id = fc.course_id 
                         WHERE fc.faculty_id = ?";
        
        if($stmt = mysqli_prepare($conn, $courses_query)) {
            mysqli_stmt_bind_param($stmt, "i", $faculty_id);
            mysqli_stmt_execute($stmt);
            $courses_result = mysqli_stmt_get_result($stmt);

            while($course = mysqli_fetch_assoc($courses_result)) {
                echo "<div class='course-section'>";
                echo "<h2>{$course['course_name']}</h2>";
                
                // Add new video form
                echo "<div class='add-video-form'>";
                echo "<h3>Add New Video Lecture</h3>";
                echo "<div class='form-group'>";
                echo "<label>Title:</label>";
                echo "<input type='text' id='title-{$course['id']}'>";
                echo "</div>";
                echo "<div class='form-group'>";
                echo "<label>Description:</label>";
                echo "<textarea id='description-{$course['id']}'></textarea>";
                echo "</div>";
                echo "<div class='form-group'>";
                echo "<label>Video URL:</label>";
                echo "<input type='url' id='video-url-{$course['id']}'>";
                echo "</div>";
                echo "<button class='btn btn-primary' onclick='addVideo({$course['id']})'>Add Video</button>";
                echo "</div>";
                
                // List existing videos
                $videos_query = "SELECT v.*, 
                               (SELECT COUNT(*) FROM video_progress vp WHERE vp.video_id = v.id) as total_views,
                               (SELECT COUNT(*) FROM video_progress vp WHERE vp.video_id = v.id AND vp.completion_percentage = 100) as completed_views
                               FROM video_lectures v 
                               WHERE v.course_id = ?";
                
                if($stmt2 = mysqli_prepare($conn, $videos_query)) {
                    mysqli_stmt_bind_param($stmt2, "i", $course['id']);
                    mysqli_stmt_execute($stmt2);
                    $videos_result = mysqli_stmt_get_result($stmt2);

                    while($video = mysqli_fetch_assoc($videos_result)) {
                        echo "<div class='video-card'>";
                        echo "<div class='video-header'>";
                        echo "<h3>{$video['title']}</h3>";
                        echo "<div>Views: {$video['total_views']} | Completed: {$video['completed_views']}</div>";
                        echo "</div>";
                        echo "<p>{$video['description']}</p>";
                        
                        // Progress statistics
                        echo "<div class='progress-bar'>";
                        $completion_rate = $video['total_views'] > 0 ? ($video['completed_views'] / $video['total_views']) * 100 : 0;
                        echo "<div class='progress' style='width: {$completion_rate}%'></div>";
                        echo "</div>";
                        
                        // Feedback section
                        echo "<div class='feedback-section'>";
                        echo "<h4>Student Feedback</h4>";
                        
                        $feedback_query = "SELECT f.*, s.name as student_name 
                                         FROM video_feedback f
                                         INNER JOIN students s ON f.student_id = s.id
                                         WHERE f.video_id = ?";
                        
                        if($stmt3 = mysqli_prepare($conn, $feedback_query)) {
                            mysqli_stmt_bind_param($stmt3, "i", $video['id']);
                            mysqli_stmt_execute($stmt3);
                            $feedback_result = mysqli_stmt_get_result($stmt3);

                            while($feedback = mysqli_fetch_assoc($feedback_result)) {
                                echo "<div class='feedback-item'>";
                                echo "<div class='feedback-header'>";
                                echo "<span>{$feedback['student_name']}</span>";
                                echo "<span>" . date('M d, Y', strtotime($feedback['feedback_date'])) . "</span>";
                                echo "</div>";
                                echo "<div class='feedback-content'>{$feedback['feedback_text']}</div>";
                                
                                if($feedback['faculty_reply']) {
                                    echo "<div class='feedback-reply'>";
                                    echo "<strong>Your Reply:</strong> {$feedback['faculty_reply']}";
                                    echo "</div>";
                                } else {
                                    echo "<div class='reply-form'>";
                                    echo "<textarea id='reply-{$feedback['id']}' placeholder='Write your reply...'></textarea>";
                                    echo "<button class='btn btn-success' onclick='replyToFeedback({$feedback['id']})'>Reply</button>";
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                        }
                        echo "</div>"; // End feedback section
                        echo "</div>"; // End video card
                    }
                }
                echo "</div>"; // End course section
            }
        }
        ?>
    </div>

    <script>
        function addVideo(courseId) {
            const title = document.getElementById(`title-${courseId}`).value;
            const description = document.getElementById(`description-${courseId}`).value;
            const videoUrl = document.getElementById(`video-url-${courseId}`).value;
            
            if(!title || !videoUrl) {
                alert('Please fill in all required fields');
                return;
            }
            
            fetch('faculty_video_management.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_video&course_id=${courseId}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&video_url=${encodeURIComponent(videoUrl)}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }

        function replyToFeedback(feedbackId) {
            const reply = document.getElementById(`reply-${feedbackId}`).value;
            
            if(!reply) {
                alert('Please write a reply');
                return;
            }
            
            fetch('faculty_video_management.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=reply_feedback&feedback_id=${feedbackId}&reply=${encodeURIComponent(reply)}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
