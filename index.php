<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['faculty_id'])) {
    header("Location: dashboard/faculty_dashboard.php");
    exit;
} elseif (isset($_SESSION['student_id'])) {
    header("Location: dashboard/student_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduConnect - Welcome</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 20px;
    }
    .container {
      width: 100%;
      max-width: 450px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(10px);
    }
    .header {
      text-align: center;
      margin-bottom: 40px;
    }
    .logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin: 0 10px 20px;
      padding: 10px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .logo-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 20px;
    }
    h1 {
      color: #2d3748;
      font-size: 32px;
      margin-bottom: 10px;
      font-weight: 700;
    }
    .subtitle {
      color: #718096;
      font-size: 16px;
      margin-bottom: 30px;
    }
    .login-options {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-top: 20px;
    }
    .login-option {
      text-decoration: none;
      padding: 20px;
      border-radius: 15px;
      text-align: center;
      transition: all 0.3s ease;
      background: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }
    .login-option i {
      font-size: 2rem;
      color: #4F46E5;
    }
    .login-option span {
      color: #2d3748;
      font-weight: 600;
    }
    .login-option:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    .login-option.faculty {
      border: 2px solid #4F46E5;
    }
    .login-option.student {
      border: 2px solid #10B981;
    }
    .login-option.student i {
      color: #10B981;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="logo-container">
        <img src="./logo/education.jpg" alt="Department Logo" class="logo">
        <img src="./logo/student.jpeg" alt="Student Logo" class="logo">
      </div>
      <h1>EduConnect</h1>
      <p class="subtitle">Choose your login type to continue</p>
    </div>

    <div class="login-options">
      <a href="login/faculty_login.php" class="login-option faculty">
        <i class="fas fa-chalkboard-teacher"></i>
        <span>Faculty</span>
      </a>
      <a href="login/student_login.php" class="login-option student">
        <i class="fas fa-user-graduate"></i>
        <span>Student</span>
      </a>
    </div>
  </div>
</body>
</html>
