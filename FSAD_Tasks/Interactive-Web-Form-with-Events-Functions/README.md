# 📝 Interactive Feedback Form with Events & Functions

A complete, production-ready customer feedback/survey system built with **HTML, CSS, and vanilla JavaScript** — no frameworks required. Features real-time validation, glassmorphism UI, dark mode, auto-save, and comprehensive event handling.

---

## 📁 Project Structure

```
Interactive Web Form with Events & Functions/
├── index.html          # Main feedback form
├── thankyou.html       # Success page after submission
├── css/
│   └── style.css       # Modern glassmorphism styles + dark mode
├── js/
│   └── script.js       # All interactive logic & validation
├── assets/
│   └── images/         # Image assets directory
└── README.md           # This documentation
```

---

## 🎯 Features

### Core Features
- **Full Name** — letter-only validation with `onkeypress`
- **Email Address** — regex format validation
- **Phone Number** — exactly 10 digits with `maxlength`
- **Country** — dropdown selection
- **Gender** — radio button group
- **Rating** — interactive 5-star rating system
- **Feedback Message** — minimum 20 characters with live counter
- **Terms & Conditions** — required checkbox
- **Submit Button** — double-click confirmation (`ondblclick`)

### Interactive Events
| Event | Element | Purpose |
|-------|---------|---------|
| `onkeypress` | Full Name | Allow letters only |
| `onkeypress` | Phone | Allow digits only |
| `onkeyup` | Feedback | Live character counter |
| `onmouseover` | All inputs | Highlight field |
| `onmouseout` | All inputs | Remove highlight |
| `ondblclick` | Submit button | Confirm & submit form |
| `onchange` | Country/Gender/Rating/Terms | Update validation state |
| `onfocus` | All inputs | Apply glow effect |
| `onblur` | All inputs | Validate & remove glow |

### Validation Rules
- ✅ Name: letters only, minimum 2 characters
- ✅ Email: valid format (`name@domain.com`)
- ✅ Phone: exactly 10 digits
- ✅ Country: must select an option
- ✅ Gender: must select an option
- ✅ Rating: at least 1 star
- ✅ Feedback: minimum 20 characters
- ✅ Terms: must be checked

### Extra Features
- 🌙 **Dark Mode Toggle** — persistent via localStorage
- 🔢 **Character Counter** — live count with color states
- 📊 **Progress Bar** — real-time completion percentage
- 💾 **Auto-Save** — form data saved to localStorage
- 🔔 **Toast Notifications** — success/error messages
- ⭐ **Star Rating Animation** — hover & click effects
- 📱 **Fully Responsive** — mobile-first design
- 🎨 **Glassmorphism Design** — modern frosted-glass UI

---

## 🚀 How to Run

1. Extract the ZIP file to any folder
2. Open `index.html` in any modern browser
3. No server or build tools required!

Or serve locally:
```bash
# Using Python 3
python -m http.server 8000

# Using Node.js
npx serve .

# Using PHP
php -S localhost:8000
```

---

## 🧪 Testing the Features

### Test Valid Input
1. Enter name: `John Doe`
2. Enter email: `john@example.com`
3. Enter phone: `1234567890`
4. Select country: `United States`
5. Select gender: `Male`
6. Click 4 stars for rating
7. Enter feedback: `This is a great service, very helpful!` (22 chars)
8. Check Terms & Conditions
9. **Double-click** the Submit button
10. You should be redirected to the Thank You page

### Test Validation Errors
- Try entering numbers in the Name field → blocked by `onkeypress`
- Enter invalid email → error shown on blur
- Enter 9 digits in phone → error message appears
- Submit without filling fields → shake animation + toast

### Test Auto-Save
- Fill in some fields
- Refresh the page
- Your data is restored from localStorage!

### Test Dark Mode
- Click the 🌙 icon in the top-right corner
- Refresh the page → theme persists!

---

## 📚 JavaScript Events Explained

### 1. `onkeypress` — Key Press Event
```javascript
function validateNameKeyPress(event) {
    // Only allows letters and spaces
    const regex = /^[a-zA-Z\s]$/;
    if (!regex.test(char)) {
        event.preventDefault(); // Block invalid input
        return false;
    }
}
```
**Use case:** Prevents invalid characters from being entered, providing immediate feedback.

### 2. `onkeyup` — Key Release Event
```javascript
// In HTML: onkeyup="validateFeedback(); updateProgress()"
```
**Use case:** Updates the character counter in real-time as the user types.

### 3. `onmouseover` — Mouse Enter Event
```javascript
function highlightField(element) {
    element.classList.add('highlighted');
}
```
**Use case:** Visually highlights form fields when the user hovers over them.

### 4. `onmouseout` — Mouse Leave Event
```javascript
function removeHighlight(element) {
    element.classList.remove('highlighted');
}
```
**Use case:** Removes the highlight effect when mouse leaves the field.

### 5. `ondblclick` — Double Click Event
```javascript
function submitForm() {
    // Validates all fields
    // Shows confirmation
    // Redirects on success
}
```
**Use case:** Prevents accidental form submission by requiring deliberate double-click.

### 6. `onchange` — Value Change Event
```javascript
// Used on: select, radio, checkbox
// Triggers: validateCountry(), validateGender(), validateTerms(), updateProgress()
```
**Use case:** Validates and updates progress when selection changes.

### 7. `onfocus` — Focus Event
```javascript
// In HTML: onfocus="highlightField(this)"
```
**Use case:** Applies glow effect when user clicks into a field.

### 8. `onblur` — Blur/Focus Leave Event
```javascript
// In HTML: onblur="validateName(); removeHighlight(this)"
```
**Use case:** Validates input and removes styling when user leaves the field.

---

## 🔧 Reusable Functions

| Function | Purpose | Returns |
|----------|---------|---------|
| `validateName()` | Validates full name | `boolean` |
| `validateEmail()` | Validates email format | `boolean` |
| `validatePhone()` | Validates 10-digit phone | `boolean` |
| `validateCountry()` | Validates country selection | `boolean` |
| `validateGender()` | Validates gender selection | `boolean` |
| `validateRating()` | Validates star rating | `boolean` |
| `validateFeedback()` | Validates message length | `boolean` |
| `validateTerms()` | Validates checkbox | `boolean` |
| `highlightField()` | Adds hover highlight | `void` |
| `removeHighlight()` | Removes hover highlight | `void` |
| `submitForm()` | Main submission handler | `void` |
| `resetForm()` | Clears all fields | `void` |
| `showToast()` | Shows notification | `void` |
| `updateProgress()` | Updates progress bar | `void` |
| `toggleTheme()` | Switches dark/light mode | `void` |
| `saveFormData()` | Saves to localStorage | `void` |
| `loadFormData()` | Restores from localStorage | `void` |

---

## 🎨 CSS Features

- **Glassmorphism Card** — `backdrop-filter: blur(20px)`
- **CSS Variables** — for easy theme switching
- **Gradient Backgrounds** — smooth purple gradient
- **Smooth Transitions** — all interactive elements animate
- **Responsive Grid** — works on mobile, tablet, desktop
- **Dark Mode** — complete theme with CSS variables

---

## 📱 Responsive Breakpoints

| Breakpoint | Adjustments |
|------------|-------------|
| > 640px | Full desktop layout |
| ≤ 640px | Stacked radio buttons, vertical buttons |
| ≤ 380px | Reduced padding, smaller fonts |

---

## 💡 Browser Compatibility

- ✅ Chrome / Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest) — with `-webkit-backdrop-filter` fallbacks
- ✅ Mobile browsers (iOS Safari, Chrome Android)

---

## 🛠️ Development Notes

### No Build Process Required
This project uses vanilla HTML, CSS, and JavaScript with no dependencies. Simply open the files in a browser.

### Code Quality
- Clean, organized functions
- Comprehensive comments
- Consistent naming conventions
- Proper indentation (2 spaces)
- Semantic HTML structure

### Security Considerations
- Client-side validation is for UX only
- In production, always validate on server-side
- Sanitize all user inputs before storage

---

## 📄 License

This project is open-source and free to use for educational purposes.

---

## 👨‍💻 Author

Built as a comprehensive demo of JavaScript Events & Functions for frontend web development.

Happy Coding! 🚀

