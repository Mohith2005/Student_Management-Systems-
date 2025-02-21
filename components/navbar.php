<?php
function getNavbar($userType = 'student') {
    $html = '
    <style>
        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
        }

        .brand-text {
            font-size: 1.25rem;
            font-weight: 600;
            color: #4F46E5;
            display: flex;
            flex-direction: column;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: #6B7280;
            font-weight: normal;
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            color: #4B5563;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            color: #4F46E5;
            background: #F3F4F6;
        }

        .nav-link.active {
            color: #4F46E5;
            background: #EEF2FF;
        }

        .nav-link i {
            font-size: 1.1em;
        }

        .profile-menu {
            position: relative;
        }

        .profile-button {
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #4B5563;
            font-weight: 500;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .profile-button:hover {
            background: #F3F4F6;
        }

        .profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #4F46E5;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .profile-name {
            display: flex;
            flex-direction: column;
        }

        .profile-role {
            font-size: 0.75rem;
            color: #6B7280;
        }

        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            display: none;
            animation: slideDown 0.2s ease-out;
        }

        .profile-dropdown.show {
            display: block;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            color: #4B5563;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: #F3F4F6;
            color: #4F46E5;
        }

        .dropdown-divider {
            height: 1px;
            background: #E5E7EB;
            margin: 0.5rem 0;
        }

        .theme-toggle {
            padding: 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            color: #4B5563;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: #F3F4F6;
            color: #4F46E5;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .brand-text {
                display: none;
            }

            .navbar-menu {
                gap: 0.75rem;
            }

            .profile-name {
                display: none;
            }
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #EF4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <nav class="navbar">
        <a href="' . ($userType == "student" ? "student_dashboard.php" : "faculty_dashboard.php") . '" class="navbar-brand">
            <img src="../assets/logo.png" alt="Logo" class="logo">
            <div class="brand-text">
                Student Management
                <span class="brand-subtitle">Learning Platform</span>
            </div>
        </a>

        <div class="navbar-menu">
            <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                <i class="fas fa-moon"></i>
            </button>

            <div class="profile-menu">
                <button class="profile-button" onclick="toggleProfileMenu()">
                    <div class="profile-avatar">
                        ' . substr($_SESSION[$userType . '_name'] ?? 'U', 0, 1) . '
                    </div>
                    <div class="profile-name">
                        <span>' . ($_SESSION[$userType . '_name'] ?? 'User') . '</span>
                        <span class="profile-role">' . ucfirst($userType) . '</span>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </button>

                <div class="profile-dropdown" id="profileDropdown">
                    <a href="profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="settings.php" class="dropdown-item">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="javascript:void(0)" onclick="handleLogout()" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        function toggleProfileMenu() {
            const dropdown = document.getElementById("profileDropdown");
            dropdown.classList.toggle("show");

            // Close dropdown when clicking outside
            document.addEventListener("click", function(event) {
                if (!event.target.closest(".profile-menu")) {
                    dropdown.classList.remove("show");
                }
            });
        }

        // Handle theme toggle
        function toggleTheme() {
            const isDark = document.body.classList.toggle("dark-theme");
            localStorage.setItem("theme", isDark ? "dark" : "light");
            
            // Update theme toggle icon
            const themeToggle = document.querySelector(".theme-toggle i");
            themeToggle.className = isDark ? "fas fa-sun" : "fas fa-moon";
        }

        // Initialize theme
        document.addEventListener("DOMContentLoaded", () => {
            const isDark = localStorage.getItem("theme") === "dark";
            document.body.classList.toggle("dark-theme", isDark);
            
            const themeToggle = document.querySelector(".theme-toggle i");
            themeToggle.className = isDark ? "fas fa-sun" : "fas fa-moon";
        });
    </script>';

    return $html;
}

function getStudentMenu() {
    return '
        <a href="student_dashboard.php" class="navbar-item">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="analytics.php" class="navbar-item">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a href="test.php" class="navbar-item">
            <i class="fas fa-tasks"></i> Tests
        </a>
        <a href="video_lectures.php" class="navbar-item">
            <i class="fas fa-play-circle"></i> Video Lectures
        </a>
        <a href="assignments.php" class="navbar-item">
            <i class="fas fa-book"></i> Assignments
        </a>
        <a href="messages.php" class="navbar-item">
            <i class="fas fa-envelope"></i> Messages
        </a>';
}

function getFacultyMenu() {
    return '
        <a href="faculty_Dashboard.html" class="navbar-item">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="faculty_analytics.php" class="navbar-item">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a href="manage_tests.php" class="navbar-item">
            <i class="fas fa-tasks"></i> Manage Tests
        </a>
        <a href="manage_videos.php" class="navbar-item">
            <i class="fas fa-video"></i> Manage Videos
        </a>
        <a href="manage_assignments.php" class="navbar-item">
            <i class="fas fa-book"></i> Assignments
        </a>
        <a href="faculty_messages.php" class="navbar-item">
            <i class="fas fa-envelope"></i> Messages
        </a>
        <a href="manage_students.php" class="navbar-item">
            <i class="fas fa-users"></i> Students
        </a>
        <a href="faculty_profile.php" class="navbar-item">
            <i class="fas fa-user"></i> Profile
        </a>';
}
?>
