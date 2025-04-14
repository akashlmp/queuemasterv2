// eye icon password
const passwordInput = document.getElementById('password');
const passwordToggle = document.getElementById('password-toggle');

// Toggle password visibility
passwordToggle.addEventListener('click', () => {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordToggle.classList.remove('bx-hide');
        passwordToggle.classList.add('bx-show');
    } else {
        passwordInput.type = 'password';
        passwordToggle.classList.remove('bx-show');
        passwordToggle.classList.add('bx-hide');
    }
});


function validateForm() {
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value.trim();
    var emailError = document.getElementById("emailError");
    var passwordError = document.getElementById("passwordError");
    var errorMessages = "";

    // Reset error messages
    emailError.innerHTML = "";
    passwordError.innerHTML = "";

    // Perform validation based on Laravel rules
    if (!email) {
        errorMessages += "Email is required.<br>";
    } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        errorMessages += "Please enter a valid email address.<br>";
    }

    if (!password) {
        errorMessages += "Password is required.<br>";
    } else if (
        password.length < 8 ||
        !/[a-zA-Z]/.test(password) ||
        !/\d/.test(password) ||
        !/[!@#$%^&*]/.test(password)
    ) {
        errorMessages += "The password must meet the following criteria:<br>" +
                         "- Be at least 8 characters long.<br>" +
                         "- Contain at least one letter.<br>" +
                         "- Contain at least one number.<br>" +
                         "- Contain at least one special character.<br>";
    }

    // Display specific error messages for email and password
    emailError.innerHTML = errorMessages;
    passwordError.innerHTML = errorMessages;

    // If no validation errors, submit the form
    if (errorMessages === "") {
        document.getElementById("loginForm").submit();
    }

    return false; // Prevent form submission
}
