// faculty_dashboard.js
class FacultyDashboard {
    constructor() {
        // facultyData is now passed from PHP
        this.facultyData = facultyData; // Assuming facultyData is passed from PHP
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
        const facultyName = document.querySelector('#facultyName');
        if (facultyName) {
            facultyName.textContent = this.facultyData.name;
        }
    }

    updateStatistics() {
        const stats = {
            'Active Courses': this.facultyData.courses.length,
            'Total Students': 0, // Will be calculated from database
            'Upcoming Classes': 0,
            'Pending Assignments': 0
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
        courseGrid.innerHTML = this.facultyData.courses
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
            const initials = this.facultyData.name
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
        document.getElementById('modalName').textContent = this.facultyData.name;
        document.getElementById('modalEmail').textContent = this.facultyData.email;
        document.getElementById('modalDepartment').textContent = this.facultyData.department;

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
        // Notice actions
        document.querySelectorAll('.notice-actions').forEach(actionBar => {
            actionBar.addEventListener('click', (e) => {
                const text = e.target.textContent.trim();
                switch(text) {
                    case 'Comment':
                        this.handleComment(e);
                        break;
                    case 'View Details':
                        this.handleViewDetails(e);
                        break;
                    case 'Submit Grades':
                        this.handleGradeSubmission(e);
                        break;
                    case 'Register':
                        this.handleRegistration(e);
                        break;
                }
            });
        });
    }

    handleComment(event) {
        const notice = event.target.closest('.notice');
        const title = notice.querySelector('.notice-title').textContent;
        alert(`Adding comment to: ${title}`);
    }

    handleViewDetails(event) {
        const notice = event.target.closest('.notice');
        const title = notice.querySelector('.notice-title').textContent;
        alert(`Viewing details for: ${title}`);
    }

    handleGradeSubmission(event) {
        alert('Opening grade submission form');
    }

    handleRegistration(event) {
        const notice = event.target.closest('.notice');
        const title = notice.querySelector('.notice-title').textContent;
        alert(`Registering for: ${title}`);
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new FacultyDashboard();
});