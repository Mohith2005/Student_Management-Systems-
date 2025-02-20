<?php
require_once 'check_faculty_auth.php';
require_once '../config.php';

// Get faculty's courses
$faculty_id = $_SESSION['faculty_id'];
$courses_query = "SELECT c.* FROM courses c 
                 INNER JOIN faculty_courses fc ON c.id = fc.course_id 
                 WHERE fc.faculty_id = ?";

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $status = $_POST['status'];
    $date = $_POST['date'];

    $sql = "INSERT INTO attendance (student_id, course_id, date, status) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE status = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "iisss", $student_id, $course_id, $date, $status, $status);
        mysqli_stmt_execute($stmt);
    }
    
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <style>
        .attendance-grid {
            display: grid;
            gap: 20px;
            padding: 20px;
        }

        .course-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .student-list {
            margin-top: 20px;
        }

        .student-row {
            display: grid;
            grid-template-columns: 100px 200px 1fr;
            gap: 10px;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .attendance-buttons {
            display: flex;
            gap: 10px;
        }

        .attendance-btn {
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .attendance-btn:hover {
            opacity: 0.8;
        }

        .present {
            background-color: #4CAF50;
            color: white;
        }

        .absent {
            background-color: #f44336;
            color: white;
        }

        .late {
            background-color: #ff9800;
            color: white;
        }

        .active {
            opacity: 1;
        }

        .inactive {
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <?php include 'faculty_header.php'; ?>
    
    <div class="attendance-grid">
        <?php
        if($stmt = mysqli_prepare($conn, $courses_query)) {
            mysqli_stmt_bind_param($stmt, "i", $faculty_id);
            mysqli_stmt_execute($stmt);
            $courses_result = mysqli_stmt_get_result($stmt);

            while($course = mysqli_fetch_assoc($courses_result)) {
                echo "<div class='course-section'>";
                echo "<h2>{$course['course_name']}</h2>";
                
                // Get students enrolled in this course
                $students_query = "SELECT s.id, s.name FROM students s 
                                 INNER JOIN student_courses sc ON s.id = sc.student_id 
                                 WHERE sc.course_id = ?";
                
                if($stmt2 = mysqli_prepare($conn, $students_query)) {
                    mysqli_stmt_bind_param($stmt2, "i", $course['id']);
                    mysqli_stmt_execute($stmt2);
                    $students_result = mysqli_stmt_get_result($stmt2);

                    echo "<div class='student-list'>";
                    while($student = mysqli_fetch_assoc($students_result)) {
                        echo "<div class='student-row' data-student-id='{$student['id']}' data-course-id='{$course['id']}'>";
                        echo "<div>{$student['id']}</div>";
                        echo "<div>{$student['name']}</div>";
                        echo "<div class='attendance-buttons'>";
                        echo "<button class='attendance-btn present' onclick='markAttendance({$student['id']}, {$course['id']}, \"present\")'>Present</button>";
                        echo "<button class='attendance-btn absent' onclick='markAttendance({$student['id']}, {$course['id']}, \"absent\")'>Absent</button>";
                        echo "<button class='attendance-btn late' onclick='markAttendance({$student['id']}, {$course['id']}, \"late\")'>Late</button>";
                        echo "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
                echo "</div>";
            }
        }
        ?>
    </div>

    <script>
        function markAttendance(studentId, courseId, status) {
            const today = new Date().toISOString().split('T')[0];
            const data = new FormData();
            data.append('student_id', studentId);
            data.append('course_id', courseId);
            data.append('status', status);
            data.append('date', today);

            fetch('attendance.php', {
                method: 'POST',
                body: data
            })
            .then(response => {
                if(response.ok) {
                    const studentRow = document.querySelector(`[data-student-id="${studentId}"][data-course-id="${courseId}"]`);
                    const buttons = studentRow.querySelectorAll('.attendance-btn');
                    buttons.forEach(btn => {
                        btn.classList.remove('active');
                        if(btn.textContent.toLowerCase() === status) {
                            btn.classList.add('active');
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
