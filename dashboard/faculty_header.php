<?php
require_once '../auth/faculty_auth.php';
$faculty_name = isset($_SESSION['faculty_name']) ? $_SESSION['faculty_name'] : '';
$faculty_department = isset($_SESSION['faculty_department']) ? $_SESSION['faculty_department'] : '';
?>
<style>
.header-nav {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px 30px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-name {
    font-weight: 500;
}

.department {
    font-size: 0.9em;
    opacity: 0.9;
}

.nav-links {
    display: flex;
    gap: 20px;
    align-items: center;
}

.nav-link {
    color: white;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.logout-btn {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid white;
    padding: 8px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    font-weight: 500;
}

.logout-btn:hover {
    background-color: white;
    color: #764ba2;
}
</style>

<div class="header-nav">
    <div class="user-info">
        <div>
            <span class="user-name">Welcome, <?php echo htmlspecialchars($faculty_name); ?></span>
            <span class="department"><?php echo htmlspecialchars($faculty_department); ?></span>
        </div>
    </div>
    <div class="nav-links">
        <a href="faculty_Dashboard.html" class="nav-link">Dashboard</a>
        <a href="faculty_course.html" class="nav-link">Manage Courses</a>
        <a href="attendance.php" class="nav-link">Attendance</a>
        <a href="Test.html" class="nav-link">Tests</a>
        <a href="Video_lecture.html" class="nav-link">Video Lectures</a>
        <a href="../auth/faculty_auth.php?logout=true" class="logout-btn">Logout</a>
    </div>
</div>
