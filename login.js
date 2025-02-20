// login.js
class Login {
    constructor() {
        this.userTypeToggle = document.querySelector('.toggle-container');
        this.currentUserType = 'faculty'; // default selection
        this.loginButton = document.querySelector('.button-container button');
        this.usernameInput = document.querySelector('input[type="text"]');
        this.passwordInput = document.querySelector('input[type="password"]');
        
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Toggle between faculty and student
        this.userTypeToggle.addEventListener('click', (e) => {
            if (e.target.textContent === 'Faculty') {
                this.currentUserType = 'faculty';
                e.target.classList.add('active');
                e.target.nextElementSibling?.classList.remove('active');
            } else if (e.target.textContent === 'Student') {
                this.currentUserType = 'student';
                e.target.classList.add('active');
                e.target.previousElementSibling?.classList.remove('active');
            }
        });

        // Handle login form submission
        this.loginButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.handleLogin();
        });
    }

    handleLogin() {
        const username = this.usernameInput.value;
        const password = this.passwordInput.value;

        // Mock user database - in real application, this would be server-side
        const mockUsers = {
            faculty: [
                { username: 'sarah.johnson', password: 'faculty123', name: 'Dr. Sarah Johnson' }
            ],
            student: [
                { username: 'john.doe', password: 'student123', name: 'John Doe' }
            ]
        };

        // Validate credentials
        const users = mockUsers[this.currentUserType];
        const user = users.find(u => u.username === username && u.password === password);

        if (user) {
            // Store user info in sessionStorage
            sessionStorage.setItem('currentUser', JSON.stringify({
                name: user.name,
                type: this.currentUserType
            }));

            // Redirect to appropriate dashboard
            window.location.href = this.currentUserType === 'faculty' 
                ? 'faculty_Dashboard.html' 
                : 'student_Dashboard.html';
        } else {
            this.showError('Invalid username or password');
        }
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        this.loginButton.parentElement.insertBefore(errorDiv, this.loginButton);
        
        // Remove error message after 3 seconds
        setTimeout(() => errorDiv.remove(), 3000);
    }
}

// Initialize login functionality
document.addEventListener('DOMContentLoaded', () => {
    new Login();
});