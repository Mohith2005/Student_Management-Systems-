// faculty_dashboard.js
class FacultyDashboard {
    constructor() {
        this.facultyData = {
            name: "Mrs A.ANANYA",
            designation: "AP/AI",
            department: "Artificial Intelligence",
            email: "ananya.ai@mkce.ac.in",
            facultyId: "MKCE/AI/2024",
            courses: [
                {
                    code: "18AIC301",
                    name: "MACHINE LEARNING WITH AI SERVICES",
                    students: 16,
                    classesLeft: 8
                },
                {
                    code: "18AIC302",
                    name: "DATA ANALYSIS AND BUSINESS INTELLIGENCE",
                    students: 15,
                    classesLeft: 10
                },
                {
                    code: "18AIC303",
                    name: "COMPUTER NETWORKS",
                    students: 16,
                    classesLeft: 12
                },
                {
                    code: "18AIC304",
                    name: "EMBEDDED SYSTEMS WITH AI",
                    students: 15,
                    classesLeft: 9
                }
            ]
        };
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
        const welcomeMessage = document.querySelector('.profile-info h1');
        if (welcomeMessage) {
            welcomeMessage.textContent = `Welcome back, ${this.facultyData.name}`;
        }
    }

    updateStatistics() {
        const stats = {
            'Active Courses': this.facultyData.courses.length,
            'Total Students': 62,
            'Upcoming Classes': this.facultyData.courses.reduce((sum, course) => sum + course.classesLeft, 0),
            'Pending Assignments': 8
        };

        document.querySelectorAll('.stat-card').forEach(card => {
            const label = card.querySelector('.stat-label').textContent.trim();
            if (stats[label] !== undefined) {
                card.querySelector('.stat-number').textContent = stats[label];
            }
        });
    }

    updateCourseGrid() {
        const courseGrid = document.querySelector('.course-grid');
        if (!courseGrid) return;

        courseGrid.innerHTML = this.facultyData.courses.map(course => `
            <div class="course-card">
                <div class="course-header">
                    <div class="course-icon">
                        ${course.code.substring(5, 7)}
                    </div>
                    <div>
                        <h3>${course.code} - ${course.name}</h3>
                    </div>
                </div>
                <div class="course-stats">
                    <div class="course-stat">
                        <div class="course-stat-number">${course.students}</div>
                        <div class="course-stat-label">Students</div>
                    </div>
                    <div class="course-stat">
                        <div class="course-stat-number">${course.classesLeft}</div>
                        <div class="course-stat-label">Classes Left</div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    updateUserAvatar() {
        const avatar = document.querySelector('.user-avatar');
        if (avatar) {
            avatar.textContent = 'AA';  // Initial of Mrs A.ANANYA
        }
    }

    createUserInfoPopup() {
        const popupHTML = `
            <div id="userInfoPopup" class="user-info-popup" style="display: none;">
                <div class="popup-content">
                    <h3>${this.facultyData.name}</h3>
                    <p><strong>Designation:</strong> ${this.facultyData.designation}</p>
                    <p><strong>Department:</strong> ${this.facultyData.department}</p>
                    <p><strong>Email:</strong> ${this.facultyData.email}</p>
                    <p><strong>Faculty ID:</strong> ${this.facultyData.facultyId}</p>
                    <div class="popup-footer">
                        <button class="edit-profile-btn">Edit Profile</button>
                        <button class="logout-btn">Logout</button>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', popupHTML);
        this.addPopupStyles();
    }

    addPopupStyles() {
        const styles = `
            .user-info-popup {
                position: fixed;
                top: 60px;
                right: 20px;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                z-index: 1000;
            }
            .popup-content {
                min-width: 300px;
            }
            .popup-content h3 {
                margin-top: 0;
                color: #333;
            }
            .popup-content p {
                margin: 10px 0;
                color: #666;
            }
            .popup-footer {
                margin-top: 15px;
                display: flex;
                justify-content: space-between;
            }
            .popup-footer button {
                padding: 8px 15px;
                border-radius: 4px;
                border: none;
                cursor: pointer;
            }
            .edit-profile-btn {
                background: #007bff;
                color: white;
            }
            .logout-btn {
                background: #dc3545;
                color: white;
            }
            .user-avatar {
                cursor: pointer;
                width: 40px;
                height: 40px;
                background: #007bff;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }
            .course-card {
                background: white;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
        `;

        const styleSheet = document.createElement("style");
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);
    }

    setupEventListeners() {
        // Profile popup toggle
        const avatar = document.querySelector('.user-avatar');
        const popup = document.getElementById('userInfoPopup');

        if (avatar && popup) {
            avatar.addEventListener('click', (e) => {
                e.stopPropagation();
                popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
            });

            // Close popup when clicking outside
            document.addEventListener('click', (e) => {
                if (!popup.contains(e.target) && e.target !== avatar) {
                    popup.style.display = 'none';
                }
            });

            // Popup buttons
            popup.querySelector('.edit-profile-btn')?.addEventListener('click', () => {
                alert('Edit profile functionality will be implemented');
            });

            popup.querySelector('.logout-btn')?.addEventListener('click', () => {
                window.location.href = 'login.html';
            });
        }

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