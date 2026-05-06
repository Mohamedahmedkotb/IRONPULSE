document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const passwordInput = document.getElementById('pass');
    const confirmPasswordInput = document.getElementById('confirm');

    form.addEventListener('submit', (e) => {
        // Clear previous custom validity
        confirmPasswordInput.setCustomValidity('');

        if (passwordInput.value !== confirmPasswordInput.value) {
            e.preventDefault(); // Prevent form submission
            // Using setCustomValidity for better HTML5 validation integration
            confirmPasswordInput.setCustomValidity('Passwords do not match.');
            confirmPasswordInput.reportValidity();
        } else if (passwordInput.value.length < 8) {
            e.preventDefault();
            passwordInput.setCustomValidity('Password must be at least 8 characters long.');
            passwordInput.reportValidity();
        } else {
            passwordInput.setCustomValidity('');
            confirmPasswordInput.setCustomValidity('');
        }
    });

    // Clear custom validity on input change to allow the user to submit again without getting stuck
    passwordInput.addEventListener('input', () => {
        passwordInput.setCustomValidity('');
    });

    confirmPasswordInput.addEventListener('input', () => {
        confirmPasswordInput.setCustomValidity('');
    });
});
