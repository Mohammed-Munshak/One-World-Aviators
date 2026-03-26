document.addEventListener('DOMContentLoaded', () => {
    
    // --- Mobile Menu Toggle ---
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            
            // Toggle icon between bars and close (X)
            const icon = menuToggle.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });
    }

    // --- Simple Form Validation for Passwords ---
    const signupForm = document.querySelector('form[action="auth_signup.php"]');
    if (signupForm) {
        signupForm.addEventListener('submit', (e) => {
            const password = signupForm.querySelector('input[name="password"]').value;
            const confirm = signupForm.querySelector('input[name="confirm_password"]').value;

            if (password !== confirm) {
                e.preventDefault(); // Stop form submission
                alert("Passwords do not match!");
            }
        });
    }
});