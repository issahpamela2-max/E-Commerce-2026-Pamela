document.addEventListener('DOMContentLoaded', function () {

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^\+?[0-9]{9,14}$/;
    const passRegex  = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

    function showError(id, message) {
        document.getElementById(id).textContent = message;
    }

    function clearErrors() {
        const spans = document.querySelectorAll('.field-error');
        for (let i = 0; i < spans.length; i++) {
            spans[i].textContent = '';
        }
    }

    // ---- Registration form ----
    const form = document.getElementById('register-form');

    if (form) {
        form.addEventListener('submit', function (e) {
            clearErrors();
            let valid = true;

            const name    = document.getElementById('name').value.trim();
            const email   = document.getElementById('email').value.trim();
            const pass    = document.getElementById('pass').value;
            const country = document.getElementById('country').value;
            const city    = document.getElementById('city').value.trim();
            const contact = document.getElementById('contact').value.trim().replace(/[\s\-]/g, '');

            if (name.length < 2) {
                showError('name-error', 'Please enter your full name.');
                valid = false;
            }

            if (!emailRegex.test(email)) {
                showError('email-error', 'Please enter a valid email address.');
                valid = false;
            } else if (email.length > 50) {
                showError('email-error', 'Email must be 50 characters or fewer.');
                valid = false;
            }

            if (!passRegex.test(pass)) {
                showError('pass-error', 'Min 8 characters, with uppercase, lowercase, a number and a special character.');
                valid = false;
            }

            if (country === '') {
                showError('country-error', 'Please select a country.');
                valid = false;
            }

            if (city.length < 2) {
                showError('city-error', 'Please enter your city.');
                valid = false;
            }

            if (!phoneRegex.test(contact)) {
                showError('contact-error', 'Enter a valid phone number, 9 to 14 digits, optionally starting with +.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // ---- Login form ----
    const loginForm = document.getElementById('login-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            clearErrors();
            let ok = true;

            const email = document.getElementById('login-email').value.trim();
            const pass  = document.getElementById('login-pass').value;

            if (!emailRegex.test(email)) {
                showError('login-email-error', 'Please enter a valid email address.');
                ok = false;
            }

            if (pass === '') {
                showError('login-pass-error', 'Please enter your password.');
                ok = false;
            }

            if (!ok) {
                e.preventDefault();
            }
        });
    }
});