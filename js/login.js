document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    if (form) {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            
            // Clear previous validity messages
            emailInput.setCustomValidity('');
            passwordInput.setCustomValidity('');

            // Basic client-side validation
            if (!emailInput.value.includes('@') || !emailInput.value.includes('.')) {
                e.preventDefault();
                emailInput.setCustomValidity('Please enter a valid email address.');
                emailInput.reportValidity();
                isValid = false;
            } else if (passwordInput.value.length < 1) {
                e.preventDefault();
                passwordInput.setCustomValidity('Password is required.');
                passwordInput.reportValidity();
                isValid = false;
            }

            // Visual feedback on successful validation before submission
            if (isValid) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = 'Authenticating... <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                    
                    // Revert text if navigation is somehow delayed or prevented
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                    }, 3000);
                }
            }
        });

        // Clear validation messages as soon as the user starts typing again
        if (emailInput) {
            emailInput.addEventListener('input', () => {
                emailInput.setCustomValidity('');
            });
        }
        
        if (passwordInput) {
            passwordInput.addEventListener('input', () => {
                passwordInput.setCustomValidity('');
            });
        }
    }

    // Social Login Buttons Functionality
    const socialBtns = document.querySelectorAll('.btn-social');
    socialBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Determine which provider was clicked by reading the text
            const provider = btn.textContent.trim() || 'Social Provider';
            const originalHtml = btn.innerHTML;
            
            // Update button to show loading state
            btn.innerHTML = `Connecting to ${provider}...`;
            btn.style.opacity = '0.7';
            btn.style.cursor = 'wait';
            
            // Simulate an OAuth redirect/network request
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                alert(`Successfully authenticated with ${provider}! Redirecting to your dashboard...`);
                
                // Redirect user to the app
                window.location.href = 'routinebuilder.html';
            }, 1500);
        });
    });
});
