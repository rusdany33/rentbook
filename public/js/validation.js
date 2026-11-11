// Form Validation Module
const Validation = {
    // Email validation
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // Password validation (min 6 characters)
    validatePassword(password) {
        return password.length >= 6;
    },
    
    // Name validation (min 3 characters)
    validateName(name) {
        return name.length >= 3;
    },
    
    // Show error message
    showError(fieldId, message) {
        const errorElement = document.getElementById(`${fieldId}-error`);
        const inputElement = document.getElementById(fieldId);
        
        if (errorElement) {
            errorElement.textContent = message;
        }
        
        if (inputElement) {
            inputElement.classList.add('error');
        }
    },
    
    // Clear error message
    clearError(fieldId) {
        const errorElement = document.getElementById(`${fieldId}-error`);
        const inputElement = document.getElementById(fieldId);
        
        if (errorElement) {
            errorElement.textContent = '';
        }
        
        if (inputElement) {
            inputElement.classList.remove('error');
        }
    },
    
    // Clear all errors
    clearAllErrors() {
        document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
        document.querySelectorAll('input.error').forEach(el => el.classList.remove('error'));
    }
};

// Show alert message
function showAlert(elementId, message, type = 'success') {
    const alertElement = document.getElementById(elementId);
    if (!alertElement) return;
    
    alertElement.textContent = message;
    alertElement.className = `alert alert-${type}`;
    alertElement.style.display = 'block';
    
    // Hide after 5 seconds
    setTimeout(() => {
        alertElement.style.display = 'none';
    }, 5000);
}

// Hide alert
function hideAlert(elementId) {
    const alertElement = document.getElementById(elementId);
    if (alertElement) {
        alertElement.style.display = 'none';
    }
}

// Format currency (Rupiah)
function formatCurrency(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Format short date
function formatShortDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
}
