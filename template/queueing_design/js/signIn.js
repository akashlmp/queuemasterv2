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
