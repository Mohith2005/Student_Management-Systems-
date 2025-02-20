// login.js
class Login {
    constructor() {
        this.userTypeToggle = document.querySelector('.toggle-container');
        this.currentUserType = 'faculty'; // default selection
        this.formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        this.loginForm = document.getElementById('loginForm');
        this.registerForm = document.getElementById('registerForm');
        this.usernameInput = document.querySelector('#loginForm input[type="text"]');
        this.passwordInput = document.querySelector('#loginForm input[type="password"]');
        
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

        // Toggle between login and register forms
        this.formToggleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.formToggleBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                if (btn.dataset.form === 'login') {
                    this.loginForm.classList.add('active');
                    this.registerForm.classList.remove('active');
                } else {
                    this.registerForm.classList.add('active');
                    this.loginForm.classList.remove('active');
                }
            });
        });

        // Handle login form submission
        this.loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleLogin();
        });

        // Handle registration form submission
        this.registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleRegistration();
        });
    }

    handleLogin() {
        const username = this.usernameInput.value;
        const password = this.passwordInput.value;

        // Get users from localStorage
        const users = JSON.parse(localStorage.getItem('users') || '{"faculty":[],"student":[]}');
        const userList = users[this.currentUserType];
        const user = userList.find(u => u.username === username && u.password === password);

        if (user) {
            // Store user info in sessionStorage
            sessionStorage.setItem('currentUser', JSON.stringify({
                name: user.name,
                type: this.currentUserType,
                email: user.email
            }));

            // Redirect to appropriate dashboard
            window.location.href = this.currentUserType === 'faculty' 
                ? 'faculty_Dashboard.html' 
                : 'student_Dashboard.html';
        } else {
            this.showError('Invalid username or password');
        }
    }

    handleRegistration() {
        const formData = new FormData(this.registerForm);
        const userData = {
            name: formData.get('fullname'),
            email: formData.get('email'),
            username: formData.get('userid'),
            password: formData.get('password'),
            confirmPassword: formData.get('confirm_password')
        };

        // Validate passwords match
        if (userData.password !== userData.confirmPassword) {
            this.showError('Passwords do not match');
            return;
        }

        // Get existing users from localStorage
        const users = JSON.parse(localStorage.getItem('users') || '{"faculty":[],"student":[]}');
        
        // Check if user already exists
        const existingUser = users[this.currentUserType].find(
            u => u.username === userData.username || u.email === userData.email
        );

        if (existingUser) {
            this.showError('User ID or email already exists');
            return;
        }

        // Add new user
        users[this.currentUserType].push({
            name: userData.name,
            email: userData.email,
            username: userData.username,
            password: userData.password
        });

        // Save updated users to localStorage
        localStorage.setItem('users', JSON.stringify(users));

        // Show success message and switch to login form
        this.showSuccess('Registration successful! Please login.');
        this.formToggleBtns[0].click(); // Switch to login form
        this.registerForm.reset();
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        
        const activeForm = document.querySelector('.form.active');
        const submitBtn = activeForm.querySelector('button[type="submit"]');
        activeForm.insertBefore(errorDiv, submitBtn.parentElement);
        
        setTimeout(() => errorDiv.remove(), 3000);
    }

    showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.style.color = '#48bb78';
        successDiv.style.textAlign = 'center';
        successDiv.style.marginBottom = '10px';
        successDiv.style.fontSize = '14px';
        successDiv.textContent = message;
        
        const activeForm = document.querySelector('.form.active');
        const submitBtn = activeForm.querySelector('button[type="submit"]');
        activeForm.insertBefore(successDiv, submitBtn.parentElement);
        
        setTimeout(() => successDiv.remove(), 3000);
    }
}

// Initialize login functionality
document.addEventListener('DOMContentLoaded', () => {
    new Login();
});