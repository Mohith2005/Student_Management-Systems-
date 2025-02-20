// login.js
class Login {
    constructor() {
      this.toggleButtons = document.querySelectorAll('.toggle-btn');
      this.currentUserType = 'faculty'; // default selection
      this.usernameInput = document.querySelector('input[type="text"]');
      this.passwordInput = document.querySelector('input[type="password"]');
      this.showPasswordIcon = document.querySelector('.show-password');
      this.loginButton = document.querySelector('.login-btn');
  
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
          // Remove active class from all toggle buttons
          this.toggleButtons.forEach((b) => b.classList.remove('active'));
          // Add active class to the clicked button
          e.target.classList.add('active');
          // Update current user type and placeholder text
          this.currentUserType = e.target.textContent.trim().toLowerCase();
          this.usernameInput.placeholder = (this.currentUserType === 'faculty' ? 'Faculty' : 'Student') + ' ID or Username';
        });
      });
    }
  
    addPasswordToggleListener() {
      // Toggle password visibility on eye icon click
      this.showPasswordIcon.addEventListener('click', () => {
        if (this.passwordInput.type === 'password') {
          this.passwordInput.type = 'text';
          this.showPasswordIcon.textContent = '🙈'; // Icon for hidden password
        } else {
          this.passwordInput.type = 'password';
          this.showPasswordIcon.textContent = '👁'; // Icon for visible password
        }
      });
    }
  
    addLoginListener() {
      this.loginButton.addEventListener('click', (e) => {
        e.preventDefault();
        this.handleLogin();
      });
    }
  
    handleLogin() {
      const username = this.usernameInput.value.trim();
      const password = this.passwordInput.value.trim();
    
      fetch('/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          username: username,
          password: password,
          user_type: this.currentUserType
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          alert(Welcome, ${data.name}!);
          // Redirect based on user type
          if (data.user_type === 'faculty') {
            window.location.href = '/faculty_dashboard';
          } else {
            window.location.href = '/student_dashboard';
          }
        } else {
          alert(data.message);
        }
      })
      .catch(error => console.error('Error:', error));
    }
    
    
  }
  const mockUsers = {
    faculty: [
        { username: 'sarah', password: 'fac123', name: 'Dr. Sarah Johnson' }
    ],
    student: [
        { username: 'john', password: 'stu123', name: 'John Doe' }
    ]
};

  
  // Initialize the login functionality when the DOM is loaded
  document.addEventListener('DOMContentLoaded', () => {
    new Login();
  });