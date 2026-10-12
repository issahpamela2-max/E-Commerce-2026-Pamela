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

    // ---- Brand form ----
    const brandForm = document.getElementById('brand-form');

    if (brandForm) {
        brandForm.addEventListener('submit', function (e) {
            clearErrors();

            const name = document.getElementById('brand_name').value.trim();

            if (name.length < 1 || name.length > 100) {
                showError('brand_name-error', 'Brand name is required, max 100 characters.');
                e.preventDefault();
            }
        });
    }

    // ---- Category form ----
    const categoryForm = document.getElementById('category-form');

    if (categoryForm) {
        categoryForm.addEventListener('submit', function (e) {
            clearErrors();

            const name = document.getElementById('cat_name').value.trim();

            if (name.length < 1 || name.length > 100) {
                showError('cat_name-error', 'Category name is required, max 100 characters.');
                e.preventDefault();
            }
        });
    }

    // ---- Profile form (My Account) ----
    const profileForm = document.getElementById('profile-form');

    if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
            clearErrors();
            let valid = true;

            const name    = document.getElementById('profile-name').value.trim();
            const country = document.getElementById('profile-country').value;
            const city    = document.getElementById('profile-city').value.trim();
            const contact = document.getElementById('profile-contact').value.trim().replace(/[\s\-]/g, '');

            if (name.length < 2 || name.length > 100) {
                showError('profile-name-error', 'Name must be between 2 and 100 characters.');
                valid = false;
            }

            if (country === '') {
                showError('profile-country-error', 'Please select a country.');
                valid = false;
            }

            if (city.length < 2) {
                showError('profile-city-error', 'Please enter your city.');
                valid = false;
            }

            if (!phoneRegex.test(contact)) {
                showError('profile-contact-error', 'Enter a valid phone number, 9 to 14 digits, optionally starting with +.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // ---- Change password form (My Account) ----
    const passwordForm = document.getElementById('password-form');

    if (passwordForm) {
        passwordForm.addEventListener('submit', function (e) {
            clearErrors();
            let valid = true;

            const current = document.getElementById('current_pass').value;
            const newPass = document.getElementById('new_pass').value;
            const confirm = document.getElementById('confirm_pass').value;

            if (current === '') {
                showError('current_pass-error', 'Please enter your current password.');
                valid = false;
            }

            if (!passRegex.test(newPass)) {
                showError('new_pass-error', 'Min 8 characters, with uppercase, lowercase, a number and a special character.');
                valid = false;
            }

            if (newPass !== confirm) {
                showError('confirm_pass-error', 'Passwords do not match.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }
        // ---- Product form ----
    const productForm = document.getElementById('product-form');

    if (productForm) {
        productForm.addEventListener('submit', function (e) {
            clearErrors();
            let valid = true;

            const title    = document.getElementById('product_title').value.trim();
            const cat      = document.getElementById('product_cat').value;
            const brand    = document.getElementById('product_brand').value;
            const price    = parseFloat(document.getElementById('product_price').value);
            const desc     = document.getElementById('product_desc').value.trim();
            const keywords = document.getElementById('product_keywords').value.trim();
            const fileIn   = document.getElementById('product_image');
            const isEdit   = document.querySelector('input[name="product_id"]') !== null;

            if (title.length < 1 || title.length > 200) {
                showError('product_title-error', 'Title is required, max 200 characters.');
                valid = false;
            }

            if (cat === '') {
                showError('product_cat-error', 'Please select a category.');
                valid = false;
            }

            if (brand === '') {
                showError('product_brand-error', 'Please select a brand.');
                valid = false;
            }

            if (isNaN(price) || price <= 0) {
                showError('product_price-error', 'Enter a price greater than zero.');
                valid = false;
            }

            if (desc.length > 500) {
                showError('product_desc-error', 'Description must be 500 characters or fewer.');
                valid = false;
            }

            if (keywords.length > 100) {
                showError('product_keywords-error', 'Keywords must be 100 characters or fewer.');
                valid = false;
            }

            // Image: required when adding, optional when editing
            if (fileIn.files.length === 0) {
                if (!isEdit) {
                    showError('product_image-error', 'Please choose a product image.');
                    valid = false;
                }
            } else {
                const file    = fileIn.files[0];
                const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

                if (allowed.indexOf(file.type) === -1) {
                    showError('product_image-error', 'Image must be JPG, PNG, GIF or WEBP.');
                    valid = false;
                } else if (file.size > 2 * 1024 * 1024) {
                    showError('product_image-error', 'Image must be 2MB or smaller.');
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }
});