// Theme Management
function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.body.classList.toggle('dark-theme', savedTheme === 'dark');
    }
}

// Handle theme toggle
function toggleTheme() {
    const isDark = document.body.classList.toggle('dark-theme');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// Form Validation
function validateForm(formId, rules) {
    const form = document.getElementById(formId);
    let isValid = true;
    
    rules.forEach(rule => {
        const input = form.querySelector(`[name="${rule.field}"]`);
        const value = input.value.trim();
        
        if (rule.required && !value) {
            showError(input, `${rule.label} is required`);
            isValid = false;
        } else if (rule.minLength && value.length < rule.minLength) {
            showError(input, `${rule.label} must be at least ${rule.minLength} characters`);
            isValid = false;
        } else if (rule.pattern && !rule.pattern.test(value)) {
            showError(input, `Please enter a valid ${rule.label}`);
            isValid = false;
        } else {
            clearError(input);
        }
    });
    
    return isValid;
}

// Show error message
function showError(input, message) {
    const formGroup = input.closest('.form-group');
    const error = formGroup.querySelector('.error-message') || document.createElement('div');
    error.className = 'error-message text-danger';
    error.textContent = message;
    if (!formGroup.querySelector('.error-message')) {
        formGroup.appendChild(error);
    }
    input.classList.add('is-invalid');
}

// Clear error message
function clearError(input) {
    const formGroup = input.closest('.form-group');
    const error = formGroup.querySelector('.error-message');
    if (error) {
        error.remove();
    }
    input.classList.remove('is-invalid');
}

// Show alert message
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} fade-in`;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Handle AJAX requests
function ajaxRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: method,
            data: data,
            success: resolve,
            error: reject
        });
    });
}

// Format date
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Format time
function formatTime(date) {
    return new Date(date).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    
    // Add form validation listeners
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!form.hasAttribute('data-no-validate')) {
                if (!validateForm(form.id, JSON.parse(form.dataset.rules || '[]'))) {
                    e.preventDefault();
                }
            }
        });
    });
});

// Handle logout
function handleLogout() {
    ajaxRequest('../auth/logout.php', 'POST')
        .then(() => {
            window.location.href = '../index.html';
        })
        .catch(error => {
            console.error('Logout failed:', error);
            showAlert('Logout failed. Please try again.', 'danger');
        });
}
