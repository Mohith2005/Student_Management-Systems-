<?php
// Common navigation links for both student and faculty

// Student Navigation Links
$student_links = [
    'Home' => [
        'url' => 'student_dashboard.php',
        'icon' => 'fa-home',
        'description' => 'View your dashboard with course overview and recent activities'
    ],
    'Analytics' => [
        'url' => 'analytics.php',
        'icon' => 'fa-chart-line',
        'description' => 'Track your academic progress and performance metrics'
    ],
    'Tests' => [
        'url' => 'test.php',
        'icon' => 'fa-tasks',
        'description' => 'Access and take your course tests'
    ],
    'Video Lectures' => [
        'url' => 'video_lectures.php',
        'icon' => 'fa-play-circle',
        'description' => 'Watch course video lectures and track your progress'
    ],
    'Assignments' => [
        'url' => 'assignments.php',
        'icon' => 'fa-book',
        'description' => 'View and submit your course assignments'
    ],
    'Messages' => [
        'url' => 'messages.php',
        'icon' => 'fa-envelope',
        'description' => 'Communicate with faculty and classmates'
    ],
    'Profile' => [
        'url' => 'profile.php',
        'icon' => 'fa-user',
        'description' => 'View and update your profile information'
    ]
];

// Faculty Navigation Links
$faculty_links = [
    'Home' => [
        'url' => 'faculty_dashboard.php',
        'icon' => 'fa-home',
        'description' => 'View your dashboard with course overview and recent activities'
    ],
    'Analytics' => [
        'url' => 'faculty_analytics.php',
        'icon' => 'fa-chart-line',
        'description' => 'Track student performance and course metrics'
    ],
    'Manage Tests' => [
        'url' => 'manage_tests.php',
        'icon' => 'fa-tasks',
        'description' => 'Create and manage course tests'
    ],
    'Manage Videos' => [
        'url' => 'manage_videos.php',
        'icon' => 'fa-video',
        'description' => 'Upload and manage video lectures'
    ],
    'Assignments' => [
        'url' => 'manage_assignments.php',
        'icon' => 'fa-book',
        'description' => 'Create and grade student assignments'
    ],
    'Messages' => [
        'url' => 'faculty_messages.php',
        'icon' => 'fa-envelope',
        'description' => 'Communicate with students and other faculty'
    ],
    'Students' => [
        'url' => 'manage_students.php',
        'icon' => 'fa-users',
        'description' => 'View and manage student information'
    ],
    'Profile' => [
        'url' => 'faculty_profile.php',
        'icon' => 'fa-user',
        'description' => 'View and update your profile information'
    ]
];

function renderNavigationMenu($links, $currentPage = '') {
    $html = '<div class="navigation-wrapper">';
    
    // Main navigation
    $html .= '<div class="navigation-menu">';
    foreach ($links as $name => $link) {
        $isActive = basename($_SERVER['PHP_SELF']) === $link['url'] ? 'active' : '';
        $html .= sprintf(
            '<a href="%s" class="nav-item %s" title="%s">
                <i class="fas %s"></i>
                <span class="nav-text">%s</span>
            </a>',
            $link['url'],
            $isActive,
            htmlspecialchars($link['description']),
            $link['icon'],
            $name
        );
    }
    $html .= '</div>';

    // Quick links
    $html .= '<div class="quick-links">';
    $html .= '<a href="../auth/logout.php" class="nav-item logout" title="Sign out">
        <i class="fas fa-sign-out-alt"></i>
        <span class="nav-text">Logout</span>
    </a>';
    $html .= '</div>';

    $html .= '</div>';

    return $html;
}

// Add these styles to the page
function getNavigationStyles() {
    return '
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navigation-wrapper {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 15px;
        }

        .navigation-menu {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .nav-item {
            text-decoration: none;
            color: #666;
            padding: 10px 20px;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 120px;
            justify-content: center;
        }

        .nav-item:hover {
            background-color: #f5f5f5;
            color: #2196F3;
            transform: translateY(-2px);
        }

        .nav-item.active {
            background-color: #2196F3;
            color: white;
        }

        .nav-item i {
            font-size: 1.2em;
        }

        .nav-text {
            font-size: 0.9em;
            font-weight: 500;
        }

        .quick-links {
            border-top: 1px solid #eee;
            padding-top: 15px;
            display: flex;
            justify-content: center;
        }

        .logout {
            color: #dc3545;
        }

        .logout:hover {
            background-color: #dc3545;
            color: white;
        }

        @media (max-width: 768px) {
            .navigation-menu {
                flex-direction: column;
                gap: 10px;
            }

            .nav-item {
                width: 100%;
            }
        }
    </style>';
}
?>
