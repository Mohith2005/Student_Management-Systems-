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

// Get all tests for faculty's courses
$sql = "SELECT 
    t.*,
    c.course_name,
    COUNT(DISTINCT st.student_id) as attempts,
    AVG(CASE WHEN st.status = 'completed' THEN st.marks_obtained END) as avg_score
FROM tests t
JOIN courses c ON t.course_id = c.id
JOIN faculty_courses fc ON fc.course_id = c.id
LEFT JOIN student_tests st ON st.test_id = t.id
WHERE fc.faculty_id = ?
GROUP BY t.id
ORDER BY t.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$tests = mysqli_stmt_get_result($stmt);
$tests = mysqli_fetch_all($tests, MYSQLI_ASSOC);

// Get faculty's courses for the new test form
$sql = "SELECT c.* FROM courses c
JOIN faculty_courses fc ON fc.course_id = c.id
WHERE fc.faculty_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$courses = mysqli_stmt_get_result($stmt);
$courses = mysqli_fetch_all($courses, MYSQLI_ASSOC);

// Convert to JSON for JavaScript
$pageData = json_encode([
    'tests' => $tests,
    'courses' => $courses
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tests</title>
    <?php echo getNavigationStyles(); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-new-test {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .tests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .test-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .test-header {
            margin-bottom: 15px;
        }

        .test-title {
            margin: 0;
            color: #333;
            font-size: 18px;
        }

        .test-course {
            color: #666;
            margin: 5px 0;
        }

        .test-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }

        .stat-item {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2196F3;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
        }

        .test-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            flex: 1;
        }

        .btn-edit {
            background-color: #2196F3;
            color: white;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 50px auto;
            padding: 20px;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close {
            font-size: 24px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .btn-submit {
            background-color: #4CAF50;
            color: white;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Tests</h1>
            <button class="btn-new-test" onclick="openNewTestModal()">
                <i class="fas fa-plus"></i> New Test
            </button>
        </div>

        <?php echo renderNavigationMenu($faculty_links, 'manage_tests.php'); ?>

        <div class="tests-grid" id="testsGrid">
            <!-- Tests will be populated by JavaScript -->
        </div>
    </div>

    <!-- New/Edit Test Modal -->
    <div id="testModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">New Test</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="testForm" onsubmit="handleTestSubmit(event)">
                <input type="hidden" id="testId" name="testId">
                <div class="form-group">
                    <label for="courseId">Course</label>
                    <select id="courseId" name="courseId" required>
                        <!-- Courses will be populated by JavaScript -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">Test Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="duration">Duration (minutes)</label>
                    <input type="number" id="duration" name="duration" min="1" required>
                </div>
                <div class="form-group">
                    <label for="totalMarks">Total Marks</label>
                    <input type="number" id="totalMarks" name="totalMarks" min="1" required>
                </div>
                <button type="submit" class="btn-submit">Save Test</button>
            </form>
        </div>
    </div>

    <script>
        const pageData = <?php echo $pageData; ?>;

        function renderTests() {
            const testsGrid = document.getElementById('testsGrid');
            testsGrid.innerHTML = pageData.tests.map(test => `
                <div class="test-card">
                    <div class="test-header">
                        <h3 class="test-title">${test.title}</h3>
                        <div class="test-course">${test.course_name}</div>
                    </div>
                    <div class="test-stats">
                        <div class="stat-item">
                            <div class="stat-value">${test.attempts || 0}</div>
                            <div class="stat-label">Attempts</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${test.avg_score ? Math.round(test.avg_score) : 0}%</div>
                            <div class="stat-label">Avg Score</div>
                        </div>
                    </div>
                    <div class="test-actions">
                        <button class="btn btn-edit" onclick="editTest(${test.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-delete" onclick="deleteTest(${test.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function populateCourseSelect() {
            const courseSelect = document.getElementById('courseId');
            courseSelect.innerHTML = pageData.courses.map(course => `
                <option value="${course.id}">${course.course_name}</option>
            `).join('');
        }

        function openNewTestModal() {
            const modal = document.getElementById('testModal');
            const form = document.getElementById('testForm');
            const modalTitle = document.getElementById('modalTitle');
            
            modalTitle.textContent = 'New Test';
            form.reset();
            document.getElementById('testId').value = '';
            
            modal.style.display = 'block';
        }

        function closeModal() {
            const modal = document.getElementById('testModal');
            modal.style.display = 'none';
        }

        function editTest(testId) {
            const test = pageData.tests.find(t => t.id === testId);
            if (!test) return;

            const modal = document.getElementById('testModal');
            const form = document.getElementById('testForm');
            const modalTitle = document.getElementById('modalTitle');

            modalTitle.textContent = 'Edit Test';
            document.getElementById('testId').value = test.id;
            document.getElementById('courseId').value = test.course_id;
            document.getElementById('title').value = test.title;
            document.getElementById('description').value = test.description;
            document.getElementById('duration').value = test.duration_minutes;
            document.getElementById('totalMarks').value = test.total_marks;

            modal.style.display = 'block';
        }

        function deleteTest(testId) {
            if (confirm('Are you sure you want to delete this test?')) {
                // Implement delete functionality
                console.log('Deleting test:', testId);
            }
        }

        function handleTestSubmit(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const testData = Object.fromEntries(formData.entries());
            
            // Implement save functionality
            console.log('Saving test:', testData);
            
            closeModal();
        }

        // Initialize the page
        renderTests();
        populateCourseSelect();

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('testModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
