<?php
require_once 'check_faculty_auth.php';
require_once '../config.php';

$faculty_id = $_SESSION['faculty_id'];

// Handle course modifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $course_id = $_POST['course_id'];
    
    switch($action) {
        case 'add_student':
            $student_id = $_POST['student_id'];
            $sql = "INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)";
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ii", $student_id, $course_id);
                mysqli_stmt_execute($stmt);
                echo json_encode(['success' => true]);
            }
            break;
            
        case 'remove_student':
            $student_id = $_POST['student_id'];
            $sql = "DELETE FROM student_courses WHERE student_id = ? AND course_id = ?";
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ii", $student_id, $course_id);
                mysqli_stmt_execute($stmt);
                echo json_encode(['success' => true]);
            }
            break;
            
        case 'update_course':
            $course_name = $_POST['course_name'];
            $description = $_POST['description'];
            $sql = "UPDATE courses SET course_name = ?, description = ? WHERE id = ?";
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssi", $course_name, $description, $course_id);
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
    <title>Course Management</title>
    <style>
        .course-management {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .course-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .edit-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .student-list {
            display: grid;
            gap: 10px;
        }

        .student-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .remove-student {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }

        .add-student-form {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .add-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            margin: 50px auto;
        }
    </style>
</head>
<body>
    <?php include 'faculty_header.php'; ?>
    
    <div class="course-management">
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
                echo "<div class='course-card'>";
                echo "<div class='course-header'>";
                echo "<h2>{$course['course_name']}</h2>";
                echo "<button class='edit-btn' onclick='editCourse({$course['id']})'>Edit Course</button>";
                echo "</div>";
                
                // Get enrolled students
                $students_query = "SELECT s.* FROM students s 
                                 INNER JOIN student_courses sc ON s.id = sc.student_id 
                                 WHERE sc.course_id = ?";
                
                if($stmt2 = mysqli_prepare($conn, $students_query)) {
                    mysqli_stmt_bind_param($stmt2, "i", $course['id']);
                    mysqli_stmt_execute($stmt2);
                    $students_result = mysqli_stmt_get_result($stmt2);

                    echo "<div class='student-list'>";
                    while($student = mysqli_fetch_assoc($students_result)) {
                        echo "<div class='student-item'>";
                        echo "<span>{$student['name']} (ID: {$student['id']})</span>";
                        echo "<button class='remove-student' onclick='removeStudent({$student['id']}, {$course['id']})'>Remove</button>";
                        echo "</div>";
                    }
                    echo "</div>";

                    echo "<div class='add-student-form'>";
                    echo "<h3>Add Student</h3>";
                    echo "<div class='form-group'>";
                    echo "<label>Student ID:</label>";
                    echo "<input type='number' id='new-student-id-{$course['id']}'>";
                    echo "</div>";
                    echo "<button class='add-btn' onclick='addStudent({$course['id']})'>Add Student</button>";
                    echo "</div>";
                }
                echo "</div>";
            }
        }
        ?>
    </div>

    <!-- Edit Course Modal -->
    <div id="editCourseModal" class="modal">
        <div class="modal-content">
            <h2>Edit Course</h2>
            <div class="form-group">
                <label>Course Name:</label>
                <input type="text" id="edit-course-name">
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea id="edit-course-description"></textarea>
            </div>
            <button onclick="saveCourseChanges()">Save Changes</button>
            <button onclick="closeModal()">Cancel</button>
        </div>
    </div>

    <script>
        let currentCourseId = null;

        function addStudent(courseId) {
            const studentId = document.getElementById(`new-student-id-${courseId}`).value;
            
            fetch('faculty_course_management.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_student&course_id=${courseId}&student_id=${studentId}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }

        function removeStudent(studentId, courseId) {
            if(confirm('Are you sure you want to remove this student?')) {
                fetch('faculty_course_management.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove_student&course_id=${courseId}&student_id=${studentId}`
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    }
                });
            }
        }

        function editCourse(courseId) {
            currentCourseId = courseId;
            document.getElementById('editCourseModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editCourseModal').style.display = 'none';
        }

        function saveCourseChanges() {
            const courseName = document.getElementById('edit-course-name').value;
            const description = document.getElementById('edit-course-description').value;
            
            fetch('faculty_course_management.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_course&course_id=${currentCourseId}&course_name=${encodeURIComponent(courseName)}&description=${encodeURIComponent(description)}`
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
