/**
 * ============================================
 * INTERACTIVE FEEDBACK FORM - MAIN JAVASCRIPT
 * Events & Functions Demo
 * ============================================
 */

// ============================================
// GLOBAL VARIABLES
// ============================================
const STORAGE_KEY = 'feedbackFormData';
let currentRating = 0;

// ============================================
// DOM ELEMENT REFERENCES
// ============================================
const form = document.getElementById('feedbackForm');
const themeToggle = document.getElementById('themeToggle');
const body = document.body;
const toast = document.getElementById('toast');
const toastMessage = document.getElementById('toastMessage');
const progressBar = document.getElementById('progressBar');
const progressText = document.getElementById('progressText');
const submitBtn = document.getElementById('submitBtn');

// Field references
const fullName = document.getElementById('fullName');
const email = document.getElementById('email');
const phone = document.getElementById('phone');
const country = document.getElementById('country');
const feedback = document.getElementById('feedback');
const terms = document.getElementById('terms');
const ratingInput = document.getElementById('rating');
const charCount = document.getElementById('charCount');
const ratingText = document.getElementById('ratingText');

// Error message elements
const nameError = document.getElementById('nameError');
const emailError = document.getElementById('emailError');
const phoneError = document.getElementById('phoneError');
const countryError = document.getElementById('countryError');
const genderError = document.getElementById('genderError');
const ratingError = document.getElementById('ratingError');
const feedbackError = document.getElementById('feedbackError');
const termsError = document.getElementById('termsError');

// ============================================
// EVENT HANDLERS SETUP (Programmatic)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Load saved theme
    loadTheme();
    
    // Load auto-saved form data
    loadFormData();
    
    // Initialize progress bar
    updateProgress();
    
    // Setup auto-save on input changes
    setupAutoSave();
    
    // Setup theme toggle
    themeToggle.addEventListener('click', toggleTheme);
});

// Auto-save setup function
function setupAutoSave() {
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('change', saveFormData);
        input.addEventListener('keyup', saveFormData);
    });
}

// ============================================
// 1. onkeypress EVENT - Allow only letters in name
// ============================================
/**
 * validateNameKeyPress(event)
 * Intercepts keypress events on the name field.
 * Only allows alphabetic characters (A-Z, a-z) and spaces.
 * Prevents numbers and special characters from being entered.
 */
function validateNameKeyPress(event) {
    const charCode = event.which || event.keyCode;
    const char = String.fromCharCode(charCode);
    
    // Allow: backspace (8), delete (46), tab (9), escape (27), enter (13)
    if ([8, 46, 9, 27, 13].indexOf(charCode) !== -1) {
        return true;
    }
    
    // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
    if ((event.ctrlKey || event.metaKey) && [65, 67, 86, 88].indexOf(charCode) !== -1) {
        return true;
    }
    
    // Allow only letters and spaces
    const regex = /^[a-zA-Z\s]$/;
    if (!regex.test(char)) {
        event.preventDefault();
        showToast('Only letters and spaces are allowed!', 'error');
        return false;
    }
    return true;
}

// ============================================
// 2. onkeyup EVENT - Live character counter
// ============================================
/**
 * The feedback textarea uses onkeyup in HTML to trigger both:
 * - validateFeedback() for validation
 * - updateProgress() for progress bar
 * 
 * This provides real-time feedback to users as they type.
 */

// ============================================
// 3. onmouseover / 4. onmouseout EVENTS
// ============================================
/**
 * highlightField(element)
 * Triggered when mouse enters an input field.
 * Adds 'highlighted' class for visual emphasis.
 */
function highlightField(element) {
    element.classList.add('highlighted');
}

/**
 * removeHighlight(element)
 * Triggered when mouse leaves an input field.
 * Removes 'highlighted' class.
 */
function removeHighlight(element) {
    element.classList.remove('highlighted');
}

// ============================================
// 5. ondblclick EVENT - Submit form confirmation
// ============================================
/**
 * submitForm()
 * Triggered on double-click of the submit button.
 * Validates all fields before submission.
 * Shows confirmation and redirects on success.
 */
function submitForm() {
    // Run all validations
    const isNameValid = validateName();
    const isEmailValid = validateEmail();
    const isPhoneValid = validatePhone();
    const isCountryValid = validateCountry();
    const isGenderValid = validateGender();
    const isRatingValid = validateRating();
    const isFeedbackValid = validateFeedback();
    const isTermsValid = validateTerms();
    
    // Check if all validations pass
    const allValid = isNameValid && isEmailValid && isPhoneValid && 
                     isCountryValid && isGenderValid && isRatingValid && 
                     isFeedbackValid && isTermsValid;
    
    if (!allValid) {
        // Shake the form to indicate error
        const card = document.querySelector('.glass-card');
        card.classList.add('shake');
        setTimeout(() => card.classList.remove('shake'), 500);
        
        showToast('Please fix all errors before submitting!', 'error');
        return;
    }
    
    // Show loading state
    submitBtn.classList.add('loading');
    submitBtn.textContent = 'Submitting...';
    
    // Gather form data
    const formData = {
        fullName: fullName.value.trim(),
        email: email.value.trim(),
        phone: phone.value.trim(),
        country: country.options[country.selectedIndex].text,
        gender: document.querySelector('input[name="gender"]:checked')?.value || '',
        rating: currentRating,
        feedback: feedback.value.trim(),
        timestamp: new Date().toISOString()
    };
    
    // Save to localStorage for thank you page
    localStorage.setItem('lastSubmittedFeedback', JSON.stringify(formData));
    
    // Simulate network delay for realism
    setTimeout(() => {
        showToast('Form submitted successfully!', 'success');
        
        // Clear saved form data
        localStorage.removeItem(STORAGE_KEY);
        
        // Redirect to thank you page
        setTimeout(() => {
            window.location.href = 'thankyou.html';
        }, 1500);
    }, 1000);
}

// ============================================
// 6. onchange EVENT - Update selected rating text
// ============================================
// Also used for: country dropdown, gender radio buttons, terms checkbox

// ============================================
// 7. onfocus EVENT - Input glow effect
// ============================================
// Handled via CSS :focus pseudo-class and 'glow' class
// Additional glow is applied via highlightField on focus

// ============================================
// 8. onblur EVENT - Validate empty fields
// ============================================
// Each input has onblur handler calling respective validation function

// ============================================
// VALIDATION FUNCTIONS
// ============================================

/**
 * validateName()
 * Validates the full name field.
 * Rules: Required, minimum 2 characters, letters only.
 */
function validateName() {
    const value = fullName.value.trim();
    
    if (value === '') {
        showError(fullName, nameError, 'Name is required');
        return false;
    }
    
    if (value.length < 2) {
        showError(fullName, nameError, 'Name must be at least 2 characters');
        return false;
    }
    
    const nameRegex = /^[a-zA-Z\s]+$/;
    if (!nameRegex.test(value)) {
        showError(fullName, nameError, 'Name can only contain letters');
        return false;
    }
    
    clearError(fullName, nameError);
    return true;
}

/**
 * validateEmail()
 * Validates the email address field.
 * Rules: Required, must match standard email format.
 */
function validateEmail() {
    const value = email.value.trim();
    
    if (value === '') {
        showError(email, emailError, 'Email is required');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
        showError(email, emailError, 'Please enter a valid email address');
        return false;
    }
    
    clearError(email, emailError);
    return true;
}

/**
 * validatePhone()
 * Validates the phone number field.
 * Rules: Required, exactly 10 digits.
 */
function validatePhone() {
    const value = phone.value.trim();
    
    if (value === '') {
        showError(phone, phoneError, 'Phone number is required');
        return false;
    }
    
    const phoneRegex = /^\d{10}$/;
    if (!phoneRegex.test(value)) {
        showError(phone, phoneError, 'Phone must be exactly 10 digits');
        return false;
    }
    
    clearError(phone, phoneError);
    return true;
}

/**
 * validatePhoneKeyPress(event)
 * Only allows digit input for phone field.
 */
function validatePhoneKeyPress(event) {
    const charCode = event.which || event.keyCode;
    const char = String.fromCharCode(charCode);
    
    // Allow control keys
    if ([8, 46, 9, 27, 13].indexOf(charCode) !== -1) {
        return true;
    }
    
    if ((event.ctrlKey || event.metaKey) && [65, 67, 86, 88].indexOf(charCode) !== -1) {
        return true;
    }
    
    // Allow only digits
    if (!/^\d$/.test(char)) {
        event.preventDefault();
        return false;
    }
    return true;
}

/**
 * validateCountry()
 * Validates the country selection.
 * Rules: Required, must select a value.
 */
function validateCountry() {
    const value = country.value;
    
    if (value === '') {
        showError(country, countryError, 'Please select a country');
        return false;
    }
    
    clearError(country, countryError);
    return true;
}

/**
 * validateGender()
 * Validates the gender selection.
 * Rules: Required, must select an option.
 */
function validateGender() {
    const selected = document.querySelector('input[name="gender"]:checked');
    
    if (!selected) {
        genderError.textContent = 'Please select a gender';
        return false;
    }
    
    genderError.textContent = '';
    return true;
}

/**
 * validateRating()
 * Validates the star rating selection.
 * Rules: Required, must select at least 1 star.
 */
function validateRating() {
    if (currentRating === 0) {
        ratingError.textContent = 'Please provide a rating';
        return false;
    }
    
    ratingError.textContent = '';
    return true;
}

/**
 * validateFeedback()
 * Validates the feedback message.
 * Rules: Required, minimum 20 characters.
 * Also updates character counter.
 */
function validateFeedback() {
    const value = feedback.value;
    const length = value.length;
    
    // Update character counter
    charCount.textContent = length;
    const counter = document.querySelector('.char-counter');
    
    if (length < 20) {
        counter.classList.remove('success');
        counter.classList.add('warning');
    } else {
        counter.classList.remove('warning');
        counter.classList.add('success');
    }
    
    // Validation
    if (value.trim() === '') {
        showError(feedback, feedbackError, 'Feedback is required');
        return false;
    }
    
    if (length < 20) {
        showError(feedback, feedbackError, `Feedback must be at least 20 characters (${length}/20)`);
        return false;
    }
    
    clearError(feedback, feedbackError);
    return true;
}

/**
 * validateTerms()
 * Validates the terms checkbox.
 * Rules: Required, must be checked.
 */
function validateTerms() {
    if (!terms.checked) {
        showError(terms, termsError, 'You must agree to the terms');
        return false;
    }
    
    clearError(terms, termsError);
    return true;
}

// ============================================
// UI HELPER FUNCTIONS
// ============================================

/**
 * showError(input, errorElement, message)
 * Displays an error message and styles the input.
 */
function showError(input, errorElement, message) {
    input.classList.add('error');
    errorElement.textContent = message;
}

/**
 * clearError(input, errorElement)
 * Removes error styling and message.
 */
function clearError(input, errorElement) {
    input.classList.remove('error');
    errorElement.textContent = '';
}

/**
 * showToast(message, type)
 * Displays a toast notification.
 * type: 'success' | 'error'
 */
function showToast(message, type = 'success') {
    toastMessage.textContent = message;
    
    if (type === 'error') {
        toast.style.background = '#e53e3e';
        toast.querySelector('.toast-icon').textContent = '✕';
    } else {
        toast.style.background = 'var(--toast-bg)';
        toast.querySelector('.toast-icon').textContent = '✓';
    }
    
    toast.classList.add('show');
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// ============================================
// STAR RATING FUNCTIONS
// ============================================

/**
 * setRating(value)
 * Sets the selected rating and updates UI.
 */
function setRating(value) {
    currentRating = value;
    ratingInput.value = value;
    updateStarDisplay(value);
    validateRating();
    updateProgress();
    
    const ratingLabels = {
        1: 'Poor - Needs Improvement',
        2: 'Fair - Below Average',
        3: 'Good - Average',
        4: 'Very Good - Above Average',
        5: 'Excellent - Outstanding!'
    };
    
    ratingText.textContent = ratingLabels[value];
    ratingText.style.color = 'var(--star-filled)';
}

/**
 * hoverStar(value)
 * Temporarily highlights stars on hover.
 */
function hoverStar(value) {
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < value) {
            star.style.color = 'var(--star-filled)';
            star.style.transform = 'scale(1.3)';
        } else {
            star.style.color = 'var(--star-empty)';
            star.style.transform = 'scale(1)';
        }
    });
}

/**
 * resetStar()
 * Resets star display to the selected rating after hover.
 */
function resetStar() {
    updateStarDisplay(currentRating);
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => {
        star.style.transform = '';
    });
}

/**
 * updateStarDisplay(value)
 * Updates star colors based on current selection.
 */
function updateStarDisplay(value) {
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < value) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

// ============================================
// PROGRESS BAR FUNCTION
// ============================================

/**
 * updateProgress()
 * Calculates and updates the form completion progress bar.
 * Each required field contributes equally to 100%.
 */
function updateProgress() {
    const totalFields = 8;
    let completedFields = 0;
    
    // Check each field
    if (fullName.value.trim().length >= 2) completedFields++;
    if (email.value.trim() !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) completedFields++;
    if (phone.value.trim().length === 10) completedFields++;
    if (country.value !== '') completedFields++;
    if (document.querySelector('input[name="gender"]:checked')) completedFields++;
    if (currentRating > 0) completedFields++;
    if (feedback.value.trim().length >= 20) completedFields++;
    if (terms.checked) completedFields++;
    
    const percentage = Math.round((completedFields / totalFields) * 100);
    
    progressBar.style.width = percentage + '%';
    progressText.textContent = percentage + '% Complete';
    
    // Change color based on progress
    if (percentage < 30) {
        progressBar.style.background = 'linear-gradient(90deg, #fc8181, #f6ad55)';
    } else if (percentage < 70) {
        progressBar.style.background = 'linear-gradient(90deg, #f6ad55, #f6e05e)';
    } else {
        progressBar.style.background = 'linear-gradient(90deg, #48bb78, #38b2ac)';
    }
}

// ============================================
// FORM RESET FUNCTION
// ============================================

/**
 * resetForm()
 * Clears all form fields, errors, and localStorage.
 */
function resetForm() {
    // Confirm reset
    if (!confirm('Are you sure you want to clear all fields?')) {
        return;
    }
    
    // Reset form
    form.reset();
    
    // Reset variables
    currentRating = 0;
    ratingInput.value = 0;
    
    // Reset UI
    updateStarDisplay(0);
    ratingText.textContent = 'Click a star to rate';
    ratingText.style.color = '';
    charCount.textContent = '0';
    document.querySelector('.char-counter').classList.remove('warning', 'success');
    
    // Clear all errors
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(el => el.textContent = '');
    
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => input.classList.remove('error'));
    
    // Reset progress
    updateProgress();
    
    // Clear localStorage
    localStorage.removeItem(STORAGE_KEY);
    
    showToast('Form has been reset', 'success');
}

// ============================================
// DARK MODE FUNCTIONS
// ============================================

/**
 * toggleTheme()
 * Toggles between light and dark mode.
 */
function toggleTheme() {
    body.classList.toggle('dark-mode');
    const isDark = body.classList.contains('dark-mode');
    themeToggle.querySelector('.theme-icon').textContent = isDark ? '☀️' : '🌙';
    localStorage.setItem('feedbackFormTheme', isDark ? 'dark' : 'light');
}

/**
 * loadTheme()
 * Loads saved theme preference from localStorage.
 */
function loadTheme() {
    const savedTheme = localStorage.getItem('feedbackFormTheme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        themeToggle.querySelector('.theme-icon').textContent = '☀️';
    }
}

// ============================================
// LOCALSTORAGE AUTO-SAVE FUNCTIONS
// ============================================

/**
 * saveFormData()
 * Saves current form values to localStorage.
 */
function saveFormData() {
    const formData = {
        fullName: fullName.value,
        email: email.value,
        phone: phone.value,
        country: country.value,
        gender: document.querySelector('input[name="gender"]:checked')?.value || '',
        rating: currentRating,
        feedback: feedback.value,
        terms: terms.checked,
        timestamp: new Date().toISOString()
    };
    
    localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
}

/**
 * loadFormData()
 * Restores form values from localStorage.
 */
function loadFormData() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (!saved) return;
    
    try {
        const data = JSON.parse(saved);
        
        if (data.fullName) fullName.value = data.fullName;
        if (data.email) email.value = data.email;
        if (data.phone) phone.value = data.phone;
        if (data.country) country.value = data.country;
        if (data.feedback) {
            feedback.value = data.feedback;
            charCount.textContent = data.feedback.length;
        }
        if (data.terms) terms.checked = data.terms;
        if (data.rating) {
            currentRating = parseInt(data.rating);
            setRating(currentRating);
        }
        if (data.gender) {
            const genderRadio = document.querySelector(`input[name="gender"][value="${data.gender}"]`);
            if (genderRadio) genderRadio.checked = true;
        }
        
        // Update progress after loading
        updateProgress();
        
        // Show restore notification
        if (data.timestamp) {
            const savedTime = new Date(data.timestamp);
            const now = new Date();
            const diffMinutes = Math.floor((now - savedTime) / 60000);
            
            if (diffMinutes < 60) {
                showToast(`Restored form data from ${diffMinutes} min ago`, 'success');
            }
        }
    } catch (e) {
        console.error('Error loading saved form data:', e);
    }
}

// ============================================
// ADDITIONAL EVENT LISTENERS
// ============================================

// Prevent form submission on Enter key
form.addEventListener('keydown', function(event) {
    if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
        event.preventDefault();
    }
});

// Real-time phone formatting (optional enhancement)
phone.addEventListener('input', function() {
    // Remove non-digits
    this.value = this.value.replace(/\D/g, '');
    
    // Limit to 10 digits
    if (this.value.length > 10) {
        this.value = this.value.slice(0, 10);
    }
    
    updateProgress();
    saveFormData();
});

// Real-time email validation on input
email.addEventListener('input', function() {
    updateProgress();
    saveFormData();
});

// Real-time name validation on input
fullName.addEventListener('input', function() {
    updateProgress();
    saveFormData();
});

// ============================================
// CONSOLE WELCOME MESSAGE
// ============================================
console.log('%c📝 Interactive Feedback Form', 'font-size: 20px; font-weight: bold; color: #667eea;');
console.log('%cEvents & Functions Demo', 'font-size: 14px; color: #764ba2;');
console.log('%cTry these commands:', 'font-size: 12px; color: #4a5568;');
console.log('  - validateName()');
console.log('  - validateEmail()');
console.log('  - validatePhone()');
console.log('  - updateProgress()');
console.log('  - resetForm()');
console.log('  - toggleTheme()');

