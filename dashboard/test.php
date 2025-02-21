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

// Get available tests
$sql = "SELECT 
    t.*,
    c.course_name,
    st.status,
    st.marks_obtained
FROM tests t
JOIN courses c ON t.course_id = c.id
JOIN student_courses sc ON sc.course_id = c.id
LEFT JOIN student_tests st ON st.test_id = t.id AND st.student_id = ?
WHERE sc.student_id = ?
ORDER BY t.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $student_id, $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tests = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Convert to JSON for JavaScript
$testsData = json_encode($tests);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Tests</title>
    <?php echo getNavigationStyles(); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .tests-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .tests-header {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .tests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .test-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .test-card h3 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .test-info {
            margin-bottom: 15px;
            color: #666;
        }

        .test-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #FFF3E0;
            color: #FF9800;
        }

        .status-completed {
            background-color: #E8F5E9;
            color: #4CAF50;
        }

        .status-missed {
            background-color: #FFEBEE;
            color: #F44336;
        }

        .test-button {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .test-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .score {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="tests-container">
        <div class="tests-header">
            <h1>Your Tests</h1>
            <p>View and take your course tests</p>
        </div>

        <?php echo renderNavigationMenu($student_links, 'test.php'); ?>

        <div class="tests-grid" id="testsGrid">
            <!-- Tests will be populated by JavaScript -->
        </div>
    </div>

    <script>
        const testsData = <?php echo $testsData; ?>;

        function formatDate(dateString) {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        function renderTests() {
            const testsGrid = document.getElementById('testsGrid');
            testsGrid.innerHTML = testsData.map(test => `
                <div class="test-card">
                    <h3>${test.title}</h3>
                    <div class="test-info">
                        <strong>Course:</strong> ${test.course_name}<br>
                        <strong>Duration:</strong> ${test.duration_minutes} minutes<br>
                        <strong>Total Marks:</strong> ${test.total_marks}
                    </div>
                    ${renderTestStatus(test)}
                    ${renderTestAction(test)}
                </div>
            `).join('');
        }

        function renderTestStatus(test) {
            if (!test.status || test.status === 'pending') {
                return `<div class="test-status status-pending">Pending</div>`;
            } else if (test.status === 'completed') {
                return `
                    <div class="test-status status-completed">Completed</div>
                    <div class="score">Score: ${test.marks_obtained}/${test.total_marks}</div>
                `;
            } else {
                return `<div class="test-status status-missed">Missed</div>`;
            }
        }

        function renderTestAction(test) {
            if (!test.status || test.status === 'pending') {
                return `
                    <button class="test-button" onclick="startTest(${test.id})">
                        Start Test
                    </button>
                `;
            } else if (test.status === 'completed') {
                return `
                    <button class="test-button" onclick="viewTestResults(${test.id})">
                        View Results
                    </button>
                `;
            } else {
                return `
                    <button class="test-button" disabled>
                        Test Missed
                    </button>
                `;
            }
        }

        function startTest(testId) {
            // Implement test taking functionality
            alert('Starting test ' + testId);
        }

        function viewTestResults(testId) {
            // Implement test results view
            alert('Viewing results for test ' + testId);
        }

        // Initialize the page
        renderTests();
    </script>
</body>
</html>
