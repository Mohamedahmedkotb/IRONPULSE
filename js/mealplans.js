document.addEventListener('DOMContentLoaded', () => {
    // 1. Goal Toggle Buttons
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all
            toggleBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked
            btn.classList.add('active');
            
            // In a real app, this would trigger a data reload
            // For demo, let's just re-trigger animations
            animateProgressBars();
        });
    });

    // 2. Animate Counter for Daily Target
    const targetCounter = document.getElementById('target-counter');
    if (targetCounter) {
        animateCounter(targetCounter, 0, 3250, 1500, (val) => {
            return val.toLocaleString(); // Add commas
        });
    }

    // 3. Animate Hydration Counter
    const hydrationCounter = document.getElementById('hydration-counter');
    if (hydrationCounter) {
        animateCounter(hydrationCounter, 0, 2.4, 1500, (val) => {
            return val.toFixed(1); // 1 decimal place
        });
    }

    // 4. Animate Progress Bars
    function animateProgressBars() {
        const progressFills = document.querySelectorAll('.progress-fill');
        progressFills.forEach(fill => {
            // Reset width to 0
            fill.style.width = '0%';
            
            // Read target width
            const targetWidth = fill.getAttribute('data-target');
            
            // Delay to allow reflow and create a staggered effect
            setTimeout(() => {
                fill.style.width = targetWidth + '%';
            }, 300);
        });
    }

    // Initial trigger
    animateProgressBars();

    // Utility function to animate numbers
    function animateCounter(element, start, end, duration, formatFn = val => val) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            
            // easeOutExpo easing function
            const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            const currentVal = start + easeProgress * (end - start);
            
            element.textContent = formatFn(currentVal);
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                element.textContent = formatFn(end); // Ensure exact final value
            }
        };
        window.requestAnimationFrame(step);
    }
});
