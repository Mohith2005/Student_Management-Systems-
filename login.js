class Login {
    constructor() {
        this.toggleButtons = document.querySelectorAll('.toggle-btn');
        this.currentUserType = 'faculty'; // default selection
        this.usernameInput = document.querySelector('input[name="username"]');
        this.passwordInput = document.querySelector('input[name="password"]');
        this.showPasswordIcon = document.querySelector('.show-password');
        this.loginButton = document.querySelector('.login-btn');
        this.loginForm = document.querySelector('#loginForm');

        this.initialize();
    }

    initialize() {
        this.addToggleListeners();
        this.addPasswordToggleListener();
        this.addLoginListener();
    }

    addToggleListeners() {
        this.toggleButtons.forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                // Remove active class from all toggle buttons
                this.toggleButtons.forEach((b) => b.classList.remove('active'));
                // Add active class to the clicked button
                e.target.classList.add('active');
                // Update current user type and placeholder text
                this.currentUserType = e.target.textContent.trim().toLowerCase();
                this.usernameInput.placeholder = `${this.currentUserType === 'faculty' ? 'Faculty' : 'Student'} ID or Username`;
            });
        });
    }

    addPasswordToggleListener() {
        this.showPasswordIcon.addEventListener('click', () => {
            const type = this.passwordInput.type === 'password' ? 'text' : 'password';
            this.passwordInput.type = type;
            this.showPasswordIcon.textContent = type === 'password' ? '👁️' : '🙈';
        });
    }

    addLoginListener() {
        this.loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleLogin();
        });
    }

    validateInputs() {
        const username = this.usernameInput.value.trim();
        const password = this.passwordInput.value.trim();
        
        if (!username || !password) {
            alert('Please enter both username and password');
            return false;
        }
        return true;
    }

    handleLogin() {
        if (!this.validateInputs()) return;

        const username = this.usernameInput.value.trim();
        const password = this.passwordInput.value.trim();
        
        // Show loading state
        const originalButtonText = this.loginButton.textContent;
        this.loginButton.textContent = 'Logging in...';
        this.loginButton.disabled = true;

        // Determine the correct auth endpoint based on user type
        const authEndpoint = this.currentUserType === 'faculty' 
            ? 'auth/faculty_auth.php' 
            : 'auth/student_auth.php';

        fetch(authEndpoint, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Show success message
                alert(`Welcome, ${data.name}!`);
                
                // Redirect based on user type
                const dashboardPath = this.currentUserType === 'faculty'
                    ? 'dashboard/faculty_dashboard.php'
                    : 'dashboard/student_dashboard.php';
                    
                window.location.href = dashboardPath;
            } else {
                throw new Error(data.message || 'Login failed. Please check your credentials.');
            }
        })
        .catch(error => {
            alert(error.message || 'An error occurred during login. Please try again.');
        })
        .finally(() => {
            // Reset button state
            this.loginButton.textContent = originalButtonText;
            this.loginButton.disabled = false;
        });
    }
}

// Initialize the login functionality when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new Login();
});
