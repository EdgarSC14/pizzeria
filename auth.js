document.addEventListener('DOMContentLoaded', function() {
    // Add floating label effect
    const inputs = document.querySelectorAll('.form-group input, .form-group textarea');
    inputs.forEach(input => {
        // Add floating class if input has value
        if (input.value) {
            input.parentElement.classList.add('floating');
        }

        // Add floating class on focus
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('floating');
        });

        // Remove floating class on blur if empty
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('floating');
            }
        });
    });

    // Form submission animation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.classList.add('loading');
                submitButton.disabled = true;
            }
        });
    });

    // Password strength indicator
    const passwordInput = document.querySelector('input[type="password"]');
    if (passwordInput) {
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength';
        passwordInput.parentElement.appendChild(strengthIndicator);

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            const strengthClasses = ['weak', 'medium', 'good', 'strong', 'very-strong'];
            strengthIndicator.className = 'password-strength ' + strengthClasses[strength - 1];
            
            const strengthTexts = ['Débil', 'Media', 'Buena', 'Fuerte', 'Muy Fuerte'];
            strengthIndicator.textContent = strengthTexts[strength - 1];
        });
    }

    // Smooth scroll to error messages
    const errorMessage = document.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}); 