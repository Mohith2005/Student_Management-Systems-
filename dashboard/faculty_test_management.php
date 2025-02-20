<?php
require_once 'check_faculty_auth.php';
require_once '../config.php';

$faculty_id = $_SESSION['faculty_id'];

// Handle test score submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $test_id = $_POST['test_id'];
    $score = $_POST['score'];
    $feedback = $_POST['feedback'];
    
    $sql = "UPDATE student_tests SET score = ?, feedback = ?, graded_date = NOW() 
            WHERE student_id = ? AND test_id = ?";
            
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "dsii", $score, $feedback, $student_id, $test_id);
        mysqli_stmt_execute($stmt);
        echo json_encode(['success' => true]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Management</title>
    <style>
        .test-management {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .test-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .test-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .student-submission {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .submission-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .grading-form {
            margin-top: 10px;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-grade {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .status-pending {
            background: #ffc107;
            color: #000;
        }

        .status-graded {
            background: #28a745;
            color: white;
        }

        .score {
            font-weight: bold;
            color: #28a745;
        }

        .feedback {
            margin-top: 10px;
            padding: 10px;
            background: #e9ecef;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'faculty_header.php'; ?>
    
    <div class="test-management">
        <?php
        // Get faculty's courses and their tests
        $tests_query = "SELECT t.*, c.course_name 
                       FROM tests t
                       INNER JOIN courses c ON t.course_id = c.id
                       INNER JOIN faculty_courses fc ON c.id = fc.course_id
                       WHERE fc.faculty_id = ?
                       ORDER BY t.test_date DESC";
        
        if($stmt = mysqli_prepare($conn, $tests_query)) {
            mysqli_stmt_bind_param($stmt, "i", $faculty_id);
            mysqli_stmt_execute($stmt);
            $tests_result = mysqli_stmt_get_result($stmt);

            while($test = mysqli_fetch_assoc($tests_result)) {
                echo "<div class='test-card'>";
                echo "<div class='test-header'>";
                echo "<h2>{$test['course_name']} - {$test['test_name']}</h2>";
                echo "<span>Date: " . date('M d, Y', strtotime($test['test_date'])) . "</span>";
                echo "</div>";
                
                // Get student submissions
                $submissions_query = "SELECT st.*, s.name as student_name 
                                    FROM student_tests st
                                    INNER JOIN students s ON st.student_id = s.id
                                    WHERE st.test_id = ?";
                
                if($stmt2 = mysqli_prepare($conn, $submissions_query)) {
                    mysqli_stmt_bind_param($stmt2, "i", $test['id']);
                    mysqli_stmt_execute($stmt2);
                    $submissions_result = mysqli_stmt_get_result($stmt2);

                    while($submission = mysqli_fetch_assoc($submissions_result)) {
                        echo "<div class='student-submission'>";
                        echo "<div class='submission-header'>";
                        echo "<h3>{$submission['student_name']} (ID: {$submission['student_id']})</h3>";
                        
                        if($submission['score'] !== null) {
                            echo "<span class='status-badge status-graded'>Graded</span>";
                        } else {
                            echo "<span class='status-badge status-pending'>Pending</span>";
                        }
                        echo "</div>";

                        if($submission['score'] !== null) {
                            echo "<div class='score'>Score: {$submission['score']}%</div>";
                            if($submission['feedback']) {
                                echo "<div class='feedback'>Feedback: {$submission['feedback']}</div>";
                            }
                        } else {
                            echo "<div class='grading-form'>";
                            echo "<div class='form-group'>";
                            echo "<label>Score (%):</label>";
                            echo "<input type='number' min='0' max='100' id='score-{$submission['student_id']}-{$test['id']}'>";
                            echo "</div>";
                            echo "<div class='form-group'>";
                            echo "<label>Feedback:</label>";
                            echo "<textarea id='feedback-{$submission['student_id']}-{$test['id']}'></textarea>";
                            echo "</div>";
                            echo "<button class='submit-grade' onclick='submitGrade({$submission['student_id']}, {$test['id']})'>Submit Grade</button>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                }
                echo "</div>";
            }
        }
        ?>
    </div>

    <script>
        function submitGrade(studentId, testId) {
            const score = document.getElementById(`score-${studentId}-${testId}`).value;
            const feedback = document.getElementById(`feedback-${studentId}-${testId}`).value;
            
            if(!score || score < 0 || score > 100) {
                alert('Please enter a valid score between 0 and 100');
                return;
            }
            
            fetch('faculty_test_management.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `student_id=${studentId}&test_id=${testId}&score=${score}&feedback=${encodeURIComponent(feedback)}`
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
