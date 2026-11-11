// Authentication handling
document.addEventListener('DOMContentLoaded', function() {
    // Login form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Register form
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
        
        // Real-time validation
        document.getElementById('email').addEventListener('blur', validateEmailField);
        document.getElementById('password').addEventListener('blur', validatePasswordField);
        document.getElementById('name')?.addEventListener('blur', validateNameField);
        document.getElementById('confirm_password')?.addEventListener('blur', validateConfirmPassword);
    }
});

// Handle login
async function handleLogin(e) {
    e.preventDefault();
    
    Validation.clearAllErrors();
    hideAlert('error-message');
    
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    // Validation
    let hasError = false;
    
    if (!Validation.validateEmail(email)) {
        Validation.showError('email', 'Format email tidak valid');
        hasError = true;
    }
    
    if (password.length === 0) {
        Validation.showError('password', 'Password harus diisi');
        hasError = true;
    }
    
    if (hasError) return;
    
    // Submit
    const formData = new FormData();
    formData.append('action', 'login');
    formData.append('email', email);
    formData.append('password', password);
    
    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.href = 'dashboard.html';
        } else {
            showAlert('error-message', result.message, 'error');
        }
    } catch (error) {
        showAlert('error-message', 'Terjadi kesalahan koneksi', 'error');
    }
}

// Handle register
async function handleRegister(e) {
    e.preventDefault();
    
    Validation.clearAllErrors();
    hideAlert('error-message');
    hideAlert('success-message');
    
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Validation
    let hasError = false;
    
    if (!Validation.validateName(name)) {
        Validation.showError('name', 'Nama minimal 3 karakter');
        hasError = true;
    }
    
    if (!Validation.validateEmail(email)) {
        Validation.showError('email', 'Format email tidak valid');
        hasError = true;
    }
    
    if (!Validation.validatePassword(password)) {
        Validation.showError('password', 'Password minimal 6 karakter');
        hasError = true;
    }
    
    if (password !== confirmPassword) {
        Validation.showError('confirm_password', 'Password tidak cocok');
        hasError = true;
    }
    
    if (hasError) return;
    
    // Submit
    const formData = new FormData();
    formData.append('action', 'register');
    formData.append('name', name);
    formData.append('email', email);
    formData.append('password', password);
    
    try {
        const response = await fetch('index.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success-message', result.message + ' Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        } else {
            showAlert('error-message', result.message, 'error');
        }
    } catch (error) {
        showAlert('error-message', 'Terjadi kesalahan koneksi', 'error');
    }
}

// Real-time validation functions
function validateEmailField() {
    const email = document.getElementById('email').value.trim();
    Validation.clearError('email');
    
    if (email && !Validation.validateEmail(email)) {
        Validation.showError('email', 'Format email tidak valid');
    }
}

function validatePasswordField() {
    const password = document.getElementById('password').value;
    Validation.clearError('password');
    
    if (password && !Validation.validatePassword(password)) {
        Validation.showError('password', 'Password minimal 6 karakter');
    }
}

function validateNameField() {
    const name = document.getElementById('name').value.trim();
    Validation.clearError('name');
    
    if (name && !Validation.validateName(name)) {
        Validation.showError('name', 'Nama minimal 3 karakter');
    }
}

function validateConfirmPassword() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    Validation.clearError('confirm_password');
    
    if (confirmPassword && password !== confirmPassword) {
        Validation.showError('confirm_password', 'Password tidak cocok');
    }
}

// Logout handler
function handleLogout() {
    if (confirm('Yakin ingin logout?')) {
        fetch('index.php?action=logout')
            .then(() => {
                window.location.href = 'login.html';
            });
    }
}
