document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Target Days Toggle
    const dayPills = document.querySelectorAll('.day-pill');
    dayPills.forEach(pill => {
        pill.addEventListener('click', () => {
            pill.classList.toggle('active');
        });
    });

    // 2. Header Tabs Toggle
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
        });
    });

    // 3. Close Recommendation Card
    const closeBtn = document.querySelector('.close-btn');
    const recommendationCard = document.querySelector('.recommendation-card');
    
    if (closeBtn && recommendationCard) {
        closeBtn.addEventListener('click', () => {
            // Add a fade out animation
            recommendationCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease, margin 0.3s ease, padding 0.3s ease, height 0.3s ease';
            recommendationCard.style.opacity = '0';
            recommendationCard.style.transform = 'scale(0.95)';
            recommendationCard.style.height = recommendationCard.offsetHeight + 'px'; // Fix height for smooth collapse
            
            // Force reflow
            recommendationCard.offsetHeight;
            
            recommendationCard.style.height = '0';
            recommendationCard.style.padding = '0';
            recommendationCard.style.margin = '0';
            recommendationCard.style.overflow = 'hidden';
            recommendationCard.style.border = 'none';
            
            setTimeout(() => {
                recommendationCard.style.display = 'none';
            }, 300);
        });
    }

    // 4. Add Exercise Button Functionality (Clone template)
    const addExerciseBtn = document.querySelector('.add-exercise-btn');
    const exerciseList = document.querySelector('.exercise-list');

    if (addExerciseBtn && exerciseList) {
        addExerciseBtn.addEventListener('click', () => {
            const exerciseItems = document.querySelectorAll('.exercise-item');
            if (exerciseItems.length > 0) {
                // Clone the first item
                const newExercise = exerciseItems[0].cloneNode(true);
                
                // Clear the input values
                const inputs = newExercise.querySelectorAll('input');
                inputs.forEach(input => input.value = '');
                
                // Reset placeholder text
                const title = newExercise.querySelector('h3');
                if (title) title.textContent = 'New Exercise';
                
                const subtitle = newExercise.querySelector('p');
                if (subtitle) subtitle.textContent = 'Select muscle group';
                
                // Add a small entrance animation to the new item
                newExercise.style.opacity = '0';
                newExercise.style.transform = 'translateY(10px)';
                newExercise.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                
                // Append to list
                exerciseList.appendChild(newExercise);
                
                // Trigger animation
                setTimeout(() => {
                    newExercise.style.opacity = '1';
                    newExercise.style.transform = 'translateY(0)';
                }, 10);
                
                // Update movement counter badge
                updateMovementBadge();
            }
        });
    }

    // Helper to update the badge count
    function updateMovementBadge() {
        const badge = document.querySelector('.exercise-header .badge');
        if (badge) {
            const currentCount = document.querySelectorAll('.exercise-item').length;
            badge.textContent = `${currentCount} Movements`;
        }
    }
});
