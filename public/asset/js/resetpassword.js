const passwordInputNew = document.getElementById('Newpassword');
const passwordToggleNew = document.getElementById('password-toggle-new');

passwordToggleNew.addEventListener('click', () => {
    if (passwordInputNew.type === 'password') {
        passwordInputNew.type = 'text';
        passwordToggleNew.classList.remove('bx-hide');
        passwordToggleNew.classList.add('bx-show');
    } else {
        passwordInputNew.type = 'password';
        passwordToggleNew.classList.remove('bx-show');
        passwordToggleNew.classList.add('bx-hide');
    }
});

const passwordInputConfirm = document.getElementById('Confirmpassword');
const passwordToggleConfirm = document.getElementById('password-toggle-confirm');

passwordToggleConfirm.addEventListener('click', () => {
    if (passwordInputConfirm.type === 'password') {
        passwordInputConfirm.type = 'text';
        passwordToggleConfirm.classList.remove('bx-hide');
        passwordToggleConfirm.classList.add('bx-show');
    } else {
        passwordInputConfirm.type = 'password';
        passwordToggleConfirm.classList.remove('bx-show');
        passwordToggleConfirm.classList.add('bx-hide');
    }
});
