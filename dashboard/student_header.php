<?php
require_once '../includes/session.php';
$student_name = isset($_SESSION['student_name']) ? $_SESSION['student_name'] : '';
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
        <span class="user-name">Welcome, <?php echo htmlspecialchars($student_name); ?></span>
    </div>
    <a href="../auth/student_auth.php?logout=true" class="logout-btn">Logout</a>
</div>
