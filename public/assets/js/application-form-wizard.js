// Modern Multi-Step Form Wizard with Validation
let currentStep = 1;
const totalSteps = 4;

// Validation rules
const validators = {
    company_name: (value) => !value ? 'Please select a company' : '',
    first_name: (value) => {
        if (!value.trim()) return 'First name is required';
        if (value.trim().length < 2) return 'First name must be at least 2 characters';
        if (!/^[a-zA-Z\s\-]+$/.test(value)) return 'First name can only contain letters';
        return '';
    },
    last_name: (value) => {
        if (!value.trim()) return 'Last name is required';
        if (value.trim().length < 2) return 'Last name must be at least 2 characters';
        if (!/^[a-zA-Z\s\-]+$/.test(value)) return 'Last name can only contain letters';
        return '';
    },
    birthdate: (value) => {
        if (!value) return 'Birthdate is required';
        const selectedDate = new Date(value);
        const today = new Date();
        let age = today.getFullYear() - selectedDate.getFullYear();
        const monthDiff = today.getMonth() - selectedDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < selectedDate.getDate())) {
            age--;
        }
        if (age < 18) return 'Applicant must be at least 18 years old';
        return '';
    },
    email_address: (value) => {
        if (!value.trim()) return 'Email address is required';
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Please enter a valid email';
        return '';
    },
    phone_number: (value) => {
        if (!value.trim()) return 'Phone number is required';
        const digits = value.replace(/\D/g, '');
        if (digits.length !== 10) return 'Phone number must be 10 digits';
        if (!digits.startsWith('9')) return 'Phone number must start with 9';
        return '';
    },
    viber_number: (value) => {
        if (!value.trim()) return 'Viber number is required';
        const digits = value.replace(/\D/g, '');
        if (digits.length !== 10) return 'Viber number must be 10 digits';
        if (!digits.startsWith('9')) return 'Viber number must start with 9';
        return '';
    },
    street_address: (value) => !value.trim() ? 'Street address is required' : '',
    barangay: (value) => !value.trim() ? 'Barangay is required' : '',
    municipality: (value) => !value.trim() ? 'Municipality/City is required' : '',
    province: (value) => !value.trim() ? 'Province is required' : '',
    bpo_experience: (value) => !value.trim() ? 'BPO experience is required' : '',
    educational_attainment: (value) => !value.trim() ? 'Educational attainment is required' : '',
    recruiter_email: (value) => {
        if (!value.trim()) return 'Recruiter email is required';
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Please enter a valid email';
        return '';
    },
    resume: (fileInput) => {
        const file = fileInput.files[0];
        if (file) {
            if (file.type !== 'application/pdf') return 'Only PDF files are allowed';
            if (file.size > 5 * 1024 * 1024) return 'File size must not exceed 5MB';
        }
        return '';
    }
};

// Fields for each step
const stepFields = {
    1: ['company_name', 'first_name', 'last_name', 'birthdate'],
    2: ['email_address', 'phone_number', 'viber_number'],
    3: ['street_address', 'barangay', 'municipality', 'province'],
    4: ['bpo_experience', 'educational_attainment', 'recruiter_email']
};

// Show error message
function showError(fieldId, message) {
    const errorEl = document.getElementById(fieldId + '_error');
    const inputEl = document.getElementById(fieldId);
    
    if (errorEl && message) {
        errorEl.textContent = message;
        errorEl.style.display = 'block';
    }
    if (inputEl) {
        inputEl.classList.add('error');
        const wrapper = inputEl.closest('.phone-input-wrapper');
        if (wrapper) wrapper.classList.add('error');
    }
}

// Clear error message
function clearError(fieldId) {
    const errorEl = document.getElementById(fieldId + '_error');
    const inputEl = document.getElementById(fieldId);
    
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.style.display = 'none';
    }
    if (inputEl) {
        inputEl.classList.remove('error');
        const wrapper = inputEl.closest('.phone-input-wrapper');
        if (wrapper) wrapper.classList.remove('error');
    }
}

// Validate single field
function validateField(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return true;

    const value = field.type === 'file' ? field : field.value;
    const validator = validators[fieldId];
    
    if (validator) {
        const error = validator(value);
        if (error) {
            showError(fieldId, error);
            return false;
        } else {
            clearError(fieldId);
            return true;
        }
    }
    return true;
}

// Validate current step
function validateCurrentStep() {
    const fields = stepFields[currentStep];
    let isValid = true;
    
    fields.forEach(fieldId => {
        if (!validateField(fieldId)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// Show specific step
function showStep(step) {
    document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.step').forEach(el => el.classList.remove('active', 'completed'));
    
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
    document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
    
    for (let i = 1; i < step; i++) {
        document.querySelector(`.step[data-step="${i}"]`).classList.add('completed');
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Next step
function nextStep() {
    if (!validateCurrentStep()) {
        const firstError = document.querySelector('.error-message[style*="display: block"]');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }
    
    if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
        saveFormData();
    }
}

// Previous step
function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

// Phone number formatting
function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 0 && value[0] !== '9') value = '9' + value;
    if (value.length > 10) value = value.substring(0, 10);
    
    if (value.length > 6) {
        value = value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6);
    } else if (value.length > 3) {
        value = value.substring(0, 3) + ' ' + value.substring(3);
    }
    
    input.value = value;
}

// File upload handling
function handleFileSelect(input) {
    const file = input.files[0];
    const uploadArea = document.getElementById('fileUploadArea');
    const preview = document.getElementById('filePreview');
    
    if (file) {
        validateField('resume');
        
        if (file.type === 'application/pdf' && file.size <= 5 * 1024 * 1024) {
            preview.querySelector('.file-name').textContent = file.name;
            preview.querySelector('.file-size').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            preview.style.display = 'flex';
            uploadArea.querySelector('.file-upload-content').style.display = 'none';
        }
    }
}

function removeFile() {
    const input = document.getElementById('resume');
    const uploadArea = document.getElementById('fileUploadArea');
    const preview = document.getElementById('filePreview');
    
    input.value = '';
    preview.style.display = 'none';
    uploadArea.querySelector('.file-upload-content').style.display = 'flex';
    clearError('resume');
}

// Auto-save form data
function saveFormData() {
    const formData = {};
    document.querySelectorAll('#applicationForm input:not([type="file"]), #applicationForm select').forEach(input => {
        if (input.id) formData[input.id] = input.value;
    });
    localStorage.setItem('applicationFormData', JSON.stringify(formData));
}

function loadFormData() {
    const saved = localStorage.getItem('applicationFormData');
    if (saved) {
        const data = JSON.parse(saved);
        Object.keys(data).forEach(id => {
            const input = document.getElementById(id);
            if (input && data[id]) input.value = data[id];
        });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Load saved data
    loadFormData();
    
    // Set max birthdate (18 years ago)
    const birthdateInput = document.getElementById('birthdate');
    const today = new Date();
    const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    birthdateInput.setAttribute('max', maxDate.toISOString().split('T')[0]);
    
    // Phone number formatting
    ['phone_number', 'viber_number'].forEach(id => {
        const input = document.getElementById(id);
        input.addEventListener('input', () => formatPhoneNumber(input));
        input.addEventListener('blur', () => validateField(id));
        input.addEventListener('focus', () => clearError(id));
    });
    
    // Add validation listeners
    Object.keys(validators).forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && field.type !== 'file') {
            field.addEventListener('blur', () => validateField(fieldId));
            field.addEventListener('focus', () => clearError(fieldId));
            field.addEventListener('input', saveFormData);
        }
    });
    
    // Form submission
    document.getElementById('applicationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all steps
        let isValid = true;
        for (let step = 1; step <= totalSteps; step++) {
            stepFields[step].forEach(fieldId => {
                if (!validateField(fieldId)) isValid = false;
            });
        }
        
        if (!isValid) {
            // Go to first step with errors
            for (let step = 1; step <= totalSteps; step++) {
                const hasError = stepFields[step].some(fieldId => {
                    const errorEl = document.getElementById(fieldId + '_error');
                    return errorEl && errorEl.style.display === 'block';
                });
                if (hasError) {
                    currentStep = step;
                    showStep(step);
                    break;
                }
            }
            return;
        }
        
        this.submit();
    });
    
    // Drag and drop
    const uploadArea = document.getElementById('fileUploadArea');
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    });
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => uploadArea.classList.add('drag-over'));
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('drag-over'));
    });
    
    uploadArea.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('resume').files = files;
            handleFileSelect(document.getElementById('resume'));
        }
    });
});
