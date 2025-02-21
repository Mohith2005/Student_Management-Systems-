// student_dashboard.js
class StudentDashboard {
    constructor() {
        // studentData is passed from PHP
        this.initializeDashboard();
        this.setupEventListeners();
    }

    initializeDashboard() {
        this.updateWelcomeMessage();
        this.updateStatistics();
        this.updateCourseGrid();
        this.createUserInfoPopup();
        this.updateUserAvatar();
    }

    updateWelcomeMessage() {
        const studentName = document.querySelector('#studentName');
        if (studentName) {
            studentName.textContent = studentData.name;
        }
    }

    updateStatistics() {
        const stats = {
            'Enrolled Courses': studentData.courses.length,
            'Completed Courses': 0, // Will be calculated from database
            'Current GPA': '0.00',
            'Attendance': '0%'
        };

        const statsGrid = document.getElementById('statsGrid');
        statsGrid.innerHTML = Object.entries(stats)
            .map(([label, value]) => `
                <div class="stat-card">
                    <h3>${value}</h3>
                    <p>${label}</p>
                </div>
            `).join('');
    }

    updateCourseGrid() {
        const courseGrid = document.getElementById('courseGrid');
        courseGrid.innerHTML = studentData.courses
            .map(course => `
                <div class="course-card">
                    <div class="course-header">
                        <div class="course-icon">
                            ${course.course_code.substring(0, 2)}
                        </div>
                        <div>
                            <h3>${course.course_code} - ${course.course_name}</h3>
                            <p>${course.department}</p>
                        </div>
                    </div>
                    <div class="course-stats">
                        <div class="stat">
                            <h4>Credits</h4>
                            <p>${course.credits}</p>
                        </div>
                    </div>
                </div>
            `).join('');
    }

    updateUserAvatar() {
        const avatar = document.querySelector('#userAvatar');
        if (avatar) {
            const initials = studentData.name
                .split(' ')
                .map(n => n[0])
                .join('')
                .toUpperCase();
            avatar.textContent = initials;
        }
    }

    createUserInfoPopup() {
        const modal = document.getElementById('profileModal');
        const closeBtn = modal.querySelector('.close');
        const avatar = document.getElementById('userAvatar');

        // Update modal content
        document.getElementById('modalName').textContent = studentData.name;
        document.getElementById('modalEmail').textContent = studentData.email;
        document.getElementById('modalCourse').textContent = studentData.course;
        document.getElementById('modalId').textContent = studentData.id;

        // Show modal on avatar click
        avatar.onclick = () => modal.style.display = "block";
        
        // Close modal on X click
        closeBtn.onclick = () => modal.style.display = "none";
        
        // Close modal on outside click
        window.onclick = (event) => {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
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

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new StudentDashboard();
});