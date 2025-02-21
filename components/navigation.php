<?php
function getStudentNavigation($currentPage = '') {
    $nav_items = [
        'student_dashboard.php' => ['icon' => 'fa-home', 'text' => 'Dashboard'],
        'analytics.php' => ['icon' => 'fa-chart-line', 'text' => 'Analytics'],
        'test.php' => ['icon' => 'fa-tasks', 'text' => 'Tests'],
        'video_lectures.php' => ['icon' => 'fa-play-circle', 'text' => 'Video Lectures']
    ];

    return renderNavigation($nav_items, $currentPage);
}

function getFacultyNavigation($currentPage = '') {
    $nav_items = [
        'faculty_dashboard.php' => ['icon' => 'fa-home', 'text' => 'Dashboard'],
        'faculty_analytics.php' => ['icon' => 'fa-chart-line', 'text' => 'Analytics'],
        'manage_tests.php' => ['icon' => 'fa-tasks', 'text' => 'Manage Tests'],
        'manage_videos.php' => ['icon' => 'fa-video', 'text' => 'Manage Videos']
    ];

    return renderNavigation($nav_items, $currentPage);
}

function renderNavigation($items, $currentPage) {
    $html = '<div class="navigation-menu">';
    foreach ($items as $page => $item) {
        $isActive = basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
        $html .= sprintf(
            '<a href="%s" class="nav-item %s">
                <i class="fas %s"></i> %s
            </a>',
            $page,
            $isActive,
            $item['icon'],
            $item['text']
        );
    }
    $html .= '</div>';
    return $html;
}

function getNavigationStyles() {
    return '
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navigation-menu {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
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
            white-space: nowrap;
        }

        .nav-item:hover {
            background-color: #f5f5f5;
            color: #2196F3;
        }

        .nav-item.active {
            background-color: #2196F3;
            color: white;
        }

        .nav-item i {
            font-size: 1.2em;
        }

        @media (max-width: 768px) {
            .navigation-menu {
                flex-direction: column;
                gap: 10px;
            }

            .nav-item {
                width: 100%;
                justify-content: center;
            }
        }
    </style>';
}
?>
