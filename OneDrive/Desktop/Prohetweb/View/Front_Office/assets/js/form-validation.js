// Form Validation Utility Functions
const formValidation = {
    // Email validation
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    // Password validation
    validatePassword: function(password) {
        // At least 6 characters, one uppercase, one lowercase, one number
        const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$/;
        return re.test(password);
    },

    // Phone number validation
    validatePhone: function(phone) {
        const re = /^[0-9]{8,15}$/;
        return re.test(phone);
    },

    // Name validation
    validateName: function(name) {
        return name.length >= 2 && /^[a-zA-Z\s]+$/.test(name);
    },

    // Number validation
    validateNumber: function(number, min = 0, max = 999999) {
        const num = parseInt(number);
        return !isNaN(num) && num >= min && num <= max;
    },

    // Date validation
    validateDate: function(date) {
        const selectedDate = new Date(date);
        const today = new Date();
        return selectedDate >= today;
    },

    // Show error message
    showError: function(input, message) {
        const $input = $(input);
        $input.addClass('is-invalid');
        $input.removeClass('shake');
        void $input[0].offsetWidth; // force reflow
        $input.addClass('shake');

        if (!$input.next('.invalid-feedback').length) {
            $input.after(`<div class="invalid-feedback">${message}</div>`);
        } else {
            $input.next('.invalid-feedback').text(message);
        }
    },

    // Clear error message
    clearError: function(input) {
        const $input = $(input);
        $input.removeClass('is-invalid shake');
        $input.next('.invalid-feedback').remove();
    },

    // Initialize form validation
    initForm: function(formId, rules) {
        const form = $(formId);
        
        // Add validation for each field
        Object.keys(rules).forEach(field => {
            const input = form.find(`#${field}`);
            const fieldRules = rules[field];

            input.on('input', function() {
                const value = $(this).val().trim();
                
                if (value === '') {
                    if (fieldRules.required) {
                        formValidation.showError(this, fieldRules.messages.required);
                    } else {
                        formValidation.clearError(this);
                    }
                    return;
                }

                // Validate based on type
                let isValid = true;
                let errorMessage = '';

                if (fieldRules.type === 'email' && !formValidation.validateEmail(value)) {
                    isValid = false;
                    errorMessage = fieldRules.messages.invalid || 'Please enter a valid email address';
                } else if (fieldRules.type === 'password' && !formValidation.validatePassword(value)) {
                    isValid = false;
                    errorMessage = fieldRules.messages.invalid || 'Password must be at least 6 characters with one uppercase, one lowercase, and one number';
                } else if (fieldRules.type === 'phone' && !formValidation.validatePhone(value)) {
                    isValid = false;
                    errorMessage = fieldRules.messages.invalid || 'Please enter a valid phone number';
                } else if (fieldRules.type === 'name' && !formValidation.validateName(value)) {
                    isValid = false;
                    errorMessage = fieldRules.messages.invalid || 'Please enter a valid name';
                } else if (fieldRules.type === 'number') {
                    if (!formValidation.validateNumber(value, fieldRules.min, fieldRules.max)) {
                        isValid = false;
                        errorMessage = fieldRules.messages.invalid || `Please enter a number between ${fieldRules.min} and ${fieldRules.max}`;
                    }
                } else if (fieldRules.type === 'date' && !formValidation.validateDate(value)) {
                    isValid = false;
                    errorMessage = fieldRules.messages.invalid || 'Please select a future date';
                }

                if (isValid) {
                    formValidation.clearError(this);
                } else {
                    formValidation.showError(this, errorMessage);
                }
            });
        });

        // Form submission validation
        form.on('submit', function(e) {
            let isValid = true;
            
            Object.keys(rules).forEach(field => {
                const input = form.find(`#${field}`);
                const value = input.val().trim();
                const fieldRules = rules[field];

                if (fieldRules.required && value === '') {
                    formValidation.showError(input, fieldRules.messages.required);
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
};

// Export for use in other files
window.formValidation = formValidation; 