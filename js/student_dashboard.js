// student_dashboard.js
class StudentDashboard {
    constructor() {
        this.validateSession();
        this.initializeDashboard();
        this.setupEventListeners();
    }

    validateSession() {
        const currentUser = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
        if (!currentUser.name || currentUser.type !== 'student') {
            window.location.href = 'login.html';
            return;
        }

        // Update welcome message
        const welcomeMessage = document.querySelector('.dashboard-header h1');
        if (welcomeMessage) {
            welcomeMessage.textContent = `Welcome back, ${currentUser.name}`;
        }
    }

    initializeDashboard() {
        this.updateNotificationBadges();
        this.loadCourseProgress();
    }

    updateNotificationBadges() {
        // Update notification counts
        const noticeCards = document.querySelectorAll('.card');
        noticeCards.forEach(card => {
            const notices = card.querySelectorAll('.notice').length;
            const badge = card.querySelector('.badge');
            if (badge) {
                badge.textContent = `${notices} New`;
            }
        });
    }

    loadCourseProgress() {
        // Update progress bars for each course
        document.querySelectorAll('.course-card').forEach(card => {
            const progressText = card.querySelector('.progress-info');
            const progressBar = card.querySelector('.progress-bar');
            
            if (progressText && progressBar) {
                const progressMatch = progressText.textContent.match(/(\d+)%/);
                if (progressMatch) {
                    const progressValue = progressMatch[1];
                    progressBar.style.width = `${progressValue}%`;
                }
            }
        });
    }

    setupEventListeners() {
        // Add event listeners for notice actions
        document.querySelectorAll('.notice-actions').forEach(actionBar => {
            actionBar.addEventListener('click', (e) => {
                if (e.target.textContent.includes('Comment')) {
                    this.handleComment(e);
                } else if (e.target.textContent.includes('Register')) {
                    this.handleRegistration(e);
                } else if (e.target.textContent.includes('View Schedule')) {
                    this.handleViewSchedule(e);
                }
            });
        });

        // Add event listeners for course cards
        document.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('click', (e) => this.handleCourseClick(e));
        });

        // Add logout functionality
        const logoutButton = document.querySelector('.logout-button');
        if (logoutButton) {
            logoutButton.addEventListener('click', () => this.handleLogout());
        }
    }

    handleComment(event) {
        const notice = event.target.closest('.notice');
        const commentSection = notice.querySelector('.comment-section');
        if (commentSection) {
            // Toggle comment section visibility
            commentSection.style.display = 
                commentSection.style.display === 'none' ? 'block' : 'none';
        }
    }

    handleRegistration(event) {
        const notice = event.target.closest('.notice');
        const title = notice.querySelector('.notice-title').textContent;
        alert(`Registration process started for: ${title}`);
        // Implement actual registration logic
    }

    handleViewSchedule(event) {
        // Implement schedule viewing functionality
        alert('Loading examination schedule...');
    }

    handleCourseClick(event) {
        const courseCard = event.target.closest('.course-card');
        const courseTitle = courseCard.querySelector('.course-info h3').textContent;
        // Implement course detail view
        alert(`Opening course details for: ${courseTitle}`);
    }

    handleLogout() {
        sessionStorage.clear();
        window.location.href = 'login.html';
    }
}

// Initialize student dashboard
document.addEventListener('DOMContentLoaded', () => {
    new StudentDashboard();
});